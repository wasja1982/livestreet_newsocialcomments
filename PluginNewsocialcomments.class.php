<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.7
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 * Основан на плагинах:
 * 1) "OpenComments" (автор: flexbyte, модификации: iMind) - https://catalog.livestreetcms.com/addon/view/39/, https://github.com/iM1nd/opencomments
 * 2) "Social Comments" (автор: 4ever4you) - https://catalog.livestreetcms.com/addon/view/201/
 *
 **/

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
    die('Hacking attemp!');
}

class PluginNewsocialcomments extends Plugin {

    protected $aInherits = array(
        'action' => array('ActionBlog','ActionAjax'),
        'mapper' => array('ModuleComment_MapperComment'),
        'module' => array('ModuleStream, ModuleComment'),
        'entity' => array('ModuleComment_EntityComment','ModuleUser_EntityUser')
    );

    /**
     * Активация плагина
     */
    public function Activate() {
        if( !$this->User_GetUserById(0) ) {
            $this->ExportSQL(dirname(__FILE__).'/dump.sql');
        }
        return true;
    }

    /**
     * Инициализация плагина
     */
    public function Init() {
        Config::Set('plugin.newsocialcomments.webpath', Plugin::GetTemplateWebPath(__CLASS__));
        Config::Set('plugin.newsocialcomments.is_mobile', (class_exists('MobileDetect') && MobileDetect::IsMobileTemplate()));
    }

    /**
     * Проверка авторизации Вконтакте
     * http://vk.com/dev/openapi_auth
     */
    static function checkVkAuth() {
        $keys = array('expire', 'mid', 'secret', 'sid');
        $vk_id = Config::Get('plugin.newsocialcomments.vk_id');
        $vk_secret = Config::Get('plugin.newsocialcomments.vk_secret');
        $session = array();
        if (isset($_COOKIE['vk_app_' . $vk_id])) {
            $vk_cookie = $_COOKIE['vk_app_' . $vk_id];
            $session_data = explode ('&', $vk_cookie, 10);
            foreach ($session_data as $pair) {
                list($key, $value) = explode('=', $pair, 2);
                if (!empty($key) && !empty($value)) {
                    if ($key === 'sig') $sig = $value;
                    elseif (in_array($key, $keys)) $session[$key] = $value;
                }
            }
            foreach ($keys as $key) {
                if (!isset($session[$key])) return false;
            }
            $sign = PluginNewsocialcomments::getSign($session, $vk_secret);
            if ($sig === $sign && $session['expire'] > time() && isset($session['mid'])) {
                return $session;
            }
        }
        return false;
    }

    /**
     * Проверка авторизации Facebook
     * https://developers.facebook.com/docs/facebook-login/using-login-with-games/
     */
    static function checkFbAuth() {
        $fb_id = Config::Get('plugin.newsocialcomments.fb_id');
        $fb_secret = Config::Get('plugin.newsocialcomments.fb_secret');
        $session = array();
        if (isset($_COOKIE['fbsr_' . $fb_id])) {
            $fb_cookie = $_COOKIE['fbsr_' . $fb_id];
            list($encoded_sig, $session_data) = explode('.', $fb_cookie, 2);
            $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
            $session = json_decode(base64_decode(strtr($session_data, '-_', '+/')), true);
            $sign = hash_hmac('sha256', $session_data, $fb_secret, $raw = true);
            if ($sig === $sign && (!isset($session['expires']) || $session['expires'] > time()) && isset($session['user_id'])) {
                return $session;
            }
        }
        return false;
    }

    /**
     * Проверка авторизации Mail.ru
     * http://api.mail.ru/docs/guides/jsapi/#mailru.session
     */
    static function checkMrAuth() {
        $mr_id = Config::Get('plugin.newsocialcomments.mr_id');
        $session = array();
        if (isset($_COOKIE['mrc'])) {
            $mr_cookie = $_COOKIE['mrc'];
            $session_data = explode('&', urldecode($mr_cookie), 10);
            foreach ($session_data as $pair) {
                list($key, $value) = explode('=', $pair, 2);
                if (!empty($key) && !empty($value)) {
                    $session[$key] = $value;
                }
            }
            if ($session['app_id'] == $mr_id && isset($session['exp']) && $session['exp'] > time() && isset($session['is_app_user']) && $session['is_app_user'] == 1 && isset($session['vid'])) {
                return $session;
            }
        }
        return false;
    }

    /**
     * Получение информации о пользователе Вконтакте
     * http://vk.com/pages.php?o=-1&p=getProfiles
     */
    static function getVkUserInfo($session) {
        if (isset($session['mid'])) {
            $params = array(
                'uids'      => $session['mid'],
                'fields'    => 'photo',
                'v'         => '4.0',
            );
            $user_info = json_decode(@file_get_contents('https://api.vk.com/method/getProfiles' . '?' . urldecode(http_build_query($params))), true);
            return (is_array($user_info) && isset($user_info['response']) && is_array($user_info['response']) && count($user_info['response']) ? $user_info['response'][0] : false);
        }
        return false;
    }

    /**
     * Получение информации о пользователе Facebook
     * http://developers.facebook.com/docs/facebook-login/access-tokens/
     */
    static function getFbUserInfo($session) {
        $fb_id = Config::Get('plugin.newsocialcomments.fb_id');
        $fb_secret = Config::Get('plugin.newsocialcomments.fb_secret');

        if (isset($session['user_id'])) {
            $params = array(
                'fields'        => 'id,name,email',
                'access_token'  => $fb_id . '|' . $fb_secret,
                'format'        => 'json',
            );
            $user_info = json_decode(@file_get_contents("https://graph.facebook.com/{$session['user_id']}" . '?' . urldecode(http_build_query($params))), true);
            return (is_array($user_info) ? $user_info : false);
        }
        return false;
    }

    /**
     * Получение информации о пользователе Mail.ru
     * http://api.mail.ru/docs/reference/rest/users.getInfo/#result
     */
    static function getMrUserInfo($session) {
        $mr_id = Config::Get('plugin.newsocialcomments.mr_id');
        $mr_secret = Config::Get('plugin.newsocialcomments.mr_secret');

        if (isset($session['session_key'])) {
            $params = array(
                'method'       => 'users.getInfo',
                'secure'       => '1',
                'app_id'       => $mr_id,
                'session_key'  => $session['session_key'],
            );
            $params['sig'] = PluginNewsocialcomments::getSign($params, $mr_secret);
            $user_info = json_decode(@file_get_contents('http://www.appsmail.ru/platform/api' . '?' . urldecode(http_build_query($params))), true);
            return (is_array($user_info) && count($user_info) ? $user_info[0] : false);
        }
        return false;
    }

    /**
     * Вычисление хеша
     *
     */
    static function getSign(array $keys, $secret_key) {
        ksort($keys);
        $params = '';
        foreach ($keys as $key => $value) {
            $params .= ($key . '=' . $value);
        }
        return md5($params . $secret_key);
    }
}
?>