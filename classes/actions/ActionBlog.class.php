<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.5
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
 * Класс обработки URL'ов вида /blog/
 *
 */

class PluginNewsocialcomments_ActionBlog extends PluginNewsocialcomments_Inherit_ActionBlog
{

    /**
     * Обработка добавление комментария к топику
     *
     * @return bool
     */
    protected function SubmitComment() {

        /**
         * Проверяем авторизован ли пользователь
         */
        if ($this->User_IsAuthorization()) {
            parent::SubmitComment();
            return;
        } else {
            $this->oUserCurrent = $this->User_GetUserById(0);
        }
        $social_type = getRequest("social");

        if (!Config::Get('plugin.newsocialcomments.enabled') && !$social_type) {
            $this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
            return;
        }

        /**
         * Проверяем на наличие aceAdminPanel, чтобы проверить IP адрес в бане
         */
        if (class_exists('PluginAceadminpanel')) {
            $plugins = $this->Plugin_GetActivePlugins();
            if (in_array('aceadminpanel', $plugins)) {
                if ($this->PluginAceadminpanel_Admin_IsBanIp(func_getIp())) {
                    $this->Message_AddErrorSingle($this->Lang_Get('adm_banned2_text'), $this->Lang_Get('error'));
                    return;
                }
            }
        }

        if (!func_check(getRequest("guest_name"),"text",2,50)) {
            $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_name'),$this->Lang_Get('error'));
            return;
        }

        if (!$social_type) {
            if (Config::Get('plugin.newsocialcomments.ask_mail')) {
                if (!func_check(getRequest("guest_email"),"mail")) {
                    $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_mail'),$this->Lang_Get('error'));
                    return;
                }
            }

            if (!isset($_SESSION['captcha_keystring']) or $_SESSION['captcha_keystring']!=strtolower(getRequest('captcha'))) {
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_captcha'),$this->Lang_Get('error'));
                return;
            }
        } else {
            if (($social_type == "vk" && !func_check(getRequest("social_avatar"),"text",1,255)) || ($social_type == "mr" && !func_check(getRequest("social_profile"),"text",1,255))) {
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_social'),$this->Lang_Get('error'));
                return;
            }

            $social_session = false;
            switch ($social_type) {
                case "vk":
                    $social_session = $this->checkVkAuth();
                    break;
                case "fb":
                    $social_session = $this->checkFbAuth();
                    break;
                case "mr":
                    $social_session = $this->checkMrAuth();
                    break;
            }
            if (!in_array($social_type, array("vk", "fb", "mr")) || !$social_session) {
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_auth'),$this->Lang_Get('error'));
                return;
            }
        }

        /**
         * Проверяем топик
         */
        if (!($oTopic=$this->Topic_GetTopicById(getRequest('cmt_target_id')))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return;
        }
        /**
         * Возможность постить коммент в топик в черновиках
         */
        if (!$oTopic->getPublish() and $this->oUserCurrent->getId()!=$oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return;
        }

        /**
        * Проверяем запрет на добавления коммента автором топика
        */
        if ($oTopic->getForbidComment()) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_notallow'),$this->Lang_Get('error'));
            return;
        }
        /**
        * Проверяем текст комментария
        */
        if (Config::Get('plugin.newsocialcomments.use_parser')) {
            $sText=$this->Text_Parser(getRequestStr('comment_text'));
        } else {
            $sText=nl2br(strip_tags(getRequestStr('comment_text')));
        }

        if (!func_check($sText,'text',2,10000)) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'),$this->Lang_Get('error'));
            return;
        }
        /**
        * Проверям на какой коммент отвечаем
        */
        $sParentId=(int)getRequest('reply');
        if (!func_check($sParentId,'id')) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return;
        }
        $oCommentParent=null;
        if ($sParentId!=0) {
            /**
            * Проверяем существует ли комментарий на который отвечаем
            */
            if (!($oCommentParent=$this->Comment_GetCommentById($sParentId))) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
                return;
            }
            /**
            * Проверяем из одного топика ли новый коммент и тот на который отвечаем
            */
            if ($oCommentParent->getTargetId()!=$oTopic->getId()) {
                $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
                return;
            }
        } else {
            /**
            * Корневой комментарий
            */
            $sParentId=null;
        }
        /**
        * Проверка на дублирующий коммент
        */
        if ($this->Comment_GetCommentUnique($oTopic->getId(),'topic',$this->oUserCurrent->getId(),$sParentId,md5($sText))) {
            $this->Message_AddErrorSingle($this->Lang_Get('topic_comment_spam'),$this->Lang_Get('error'));
            return;
        }
        /**
        * Создаём коммент
        */
        $oCommentNew=Engine::GetEntity('Comment');
        $oCommentNew->setTargetId($oTopic->getId());
        $oCommentNew->setTargetType('topic');
        $oCommentNew->setTargetParentId($oTopic->getBlog()->getId());
        $oCommentNew->setUserId($this->oUserCurrent->getId());
        $oCommentNew->setText($sText);
        $oCommentNew->setDate(date("Y-m-d H:i:s"));
        $oCommentNew->setUserIp(func_getIp());
        $oCommentNew->setPid($sParentId);
        $oCommentNew->setTextHash(md5($sText));
        $oCommentNew->setPublish($oTopic->getPublish());

        if (getRequest("guest_name")) $oCommentNew->setGuestName(getRequest("guest_name"));
        if (getRequest("guest_email")) $oCommentNew->setGuestEmail(getRequest("guest_email"));
        if (getRequest("social_avatar")) $oCommentNew->setGuestAvatar(getRequest("social_avatar"));
        if (getRequest("social_profile")) $oCommentNew->setGuestProfile(getRequest("social_profile"));
        $oCommentNew->setGuestType($social_type);
        switch ($social_type) {
            case "vk":
                if (Config::Get('plugin.newsocialcomments.use_server_check')) {
                    $user_info = $this->getVkUserInfo($social_session);
                    if (is_array($user_info) && isset($user_info['uid']) && isset($user_info['first_name']) && isset($user_info['last_name'])) {
                        $oCommentNew->setGuestId($user_info['uid']);
                        $oCommentNew->setGuestName($user_info['first_name'] . ' ' . $user_info['last_name']);
                        if (isset($user_info['photo']))
                        $oCommentNew->setGuestAvatar($user_info['photo']);
                    } else {
                        $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_auth'),$this->Lang_Get('error'));
                        return;
                    }
                } else {
                    $oCommentNew->setGuestId($social_session['mid']);
                }
                break;
            case "fb":
                if (Config::Get('plugin.newsocialcomments.use_server_check')) {
                    $user_info = $this->getFbUserInfo($social_session);
                    if (is_array($user_info) && isset($user_info['id']) && isset($user_info['name'])) {
                        $oCommentNew->setGuestId($user_info['id']);
                        $oCommentNew->setGuestName($user_info['name']);
                        if (isset($user_info['email']))
                        $oCommentNew->setGuestEmail($user_info['email']);
                    } else {
                        $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_auth'),$this->Lang_Get('error'));
                        return;
                    }
                } else {
                    $oCommentNew->setGuestId($social_session['user_id']);
                }
                break;
            case "mr":
                if (Config::Get('plugin.newsocialcomments.use_server_check')) {
                    $user_info = $this->getMrUserInfo($social_session);
                    if (is_array($user_info) && isset($user_info['uid']) && isset($user_info['first_name']) && isset($user_info['last_name'])) {
                        $oCommentNew->setGuestId($user_info['uid']);
                        $oCommentNew->setGuestName($user_info['first_name'] . ' ' . $user_info['last_name']);
                        if (isset($user_info['has_pic']) && $user_info['has_pic'] && isset($user_info['pic']))
                        $oCommentNew->setGuestAvatar($user_info['pic']);
                        if (isset($user_info['link']))
                        $oCommentNew->setGuestProfile($user_info['link']);
                        if (isset($user_info['email']))
                        $oCommentNew->setGuestEmail($user_info['email']);
                    } else {
                        $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_auth'),$this->Lang_Get('error'));
                        return;
                    }
                } else {
                    $oCommentNew->setGuestId($social_session['vid']);
                }
                break;
        }
        unset($_SESSION['captcha_keystring']);

        /**
        * Добавляем коммент
        */
        $this->Hook_Run('comment_add_before', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));
        if ($this->Comment_AddComment($oCommentNew)) {
            $this->Hook_Run('comment_add_after', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));

            $this->Viewer_AssignAjax('sCommentId',$oCommentNew->getId());
            if ($oTopic->getPublish()) {
                /**
                * Добавляем коммент в прямой эфир если топик не в черновиках
                */
                $oCommentOnline=Engine::GetEntity('Comment_CommentOnline');
                $oCommentOnline->setTargetId($oCommentNew->getTargetId());
                $oCommentOnline->setTargetType($oCommentNew->getTargetType());
                $oCommentOnline->setTargetParentId($oCommentNew->getTargetParentId());
                $oCommentOnline->setCommentId($oCommentNew->getId());

                $this->Comment_AddCommentOnline($oCommentOnline);
            }
            /**
            * Сохраняем дату последнего коммента для юзера
            */
            $this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
            $this->User_Update($this->oUserCurrent);

            /**
             * Список емайлов на которые не нужно отправлять уведомление
            */
            $aExcludeMail=array();
            /**
            * Отправляем уведомление тому на чей коммент ответили
            */
            if ($oCommentParent and $oCommentParent->getUserId()!=0 and $oCommentParent->getUserId()!=$oTopic->getUserId() and $oCommentNew->getUserId()!=$oCommentParent->getUserId()) {
                $oUserAuthorComment=$oCommentParent->getUser();
                $aExcludeMail[]=$oUserAuthorComment->getMail();
                $this->Notify_SendCommentReplyToAuthorParentComment($oUserAuthorComment,$oTopic,$oCommentNew,$this->oUserCurrent);
            }
            /**
             * Отправка уведомления автору топика
             */
            $this->Subscribe_Send('topic_new_comment',$oTopic->getId(),'notify.comment_new.tpl',$this->Lang_Get('notify_subject_comment_new'),array(
                'oTopic' => $oTopic,
                'oComment' => $oCommentNew,
                'oUserComment' => $this->oUserCurrent,
                ),$aExcludeMail);
            /**
             * Добавляем событие в ленту
             */
            $this->Stream_write($oCommentNew->getUserId(), 'add_comment', $oCommentNew->getId(), $oTopic->getPublish() && $oTopic->getBlog()->getType()!='close');
        } else {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
        }
    }

    /**
     * Получение новых комментариев
     *
     */
    protected function AjaxResponseComment() {

        $this->Viewer_SetResponseAjax('json');
        /**
         * Пользователь авторизован?
         */
        if (!$this->oUserCurrent) {
            $this->oUserCurrent = $this->User_GetUserById(0);
        }
        /**
         * Топик существует?
         */
        $idTopic=getRequestStr('idTarget',null,'post');
        if (!($oTopic=$this->Topic_GetTopicById($idTopic))) {
            $this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
            return;
        }

        $idCommentLast=getRequestStr('idCommentLast',null,'post');
        $selfIdComment=getRequestStr('selfIdComment',null,'post');
        $aComments=array();
        /**
         * Если используется постраничность, возвращаем только добавленный комментарий
         */
        if (getRequest('bUsePaging',null,'post') and $selfIdComment or $this->oUserCurrent->getUserId()==0) {
            if ($oComment=$this->Comment_GetCommentById($selfIdComment) and $oComment->getTargetId()==$oTopic->getId() and $oComment->getTargetType()=='topic') {
                $oViewerLocal=$this->Viewer_GetLocalViewer();
                $oViewerLocal->Assign('oUserCurrent',$this->oUserCurrent);
                $oViewerLocal->Assign('bOneComment',true);

                $oViewerLocal->Assign('oComment',$oComment);
                $sText=$oViewerLocal->Fetch($this->Comment_GetTemplateCommentByTarget($oTopic->getId(),'topic'));
                $aCmt=array();
                $aCmt[]=array(
                    'html' => $sText,
                    'obj'  => $oComment,
                );
            } else {
                $aCmt=array();
            }
            $aReturn['comments']=$aCmt;
            $aReturn['iMaxIdComment']=$selfIdComment;
        } else {
            $aReturn=$this->Comment_GetCommentsNewByTargetId($oTopic->getId(),'topic',$idCommentLast);
        }
        $iMaxIdComment=$aReturn['iMaxIdComment'];

        $oTopicRead=Engine::GetEntity('Topic_TopicRead');
        $oTopicRead->setTopicId($oTopic->getId());
        $oTopicRead->setUserId($this->oUserCurrent->getId());
        $oTopicRead->setCommentCountLast($oTopic->getCountComment());
        $oTopicRead->setCommentIdLast($iMaxIdComment);
        $oTopicRead->setDateRead(date("Y-m-d H:i:s"));
        $this->Topic_SetTopicRead($oTopicRead);

        $aCmts=$aReturn['comments'];
        if ($aCmts and is_array($aCmts)) {
            foreach ($aCmts as $aCmt) {
                $aComments[]=array(
                    'html' => $aCmt['html'],
                    'idParent' => $aCmt['obj']->getPid(),
                    'id' => $aCmt['obj']->getId(),
                );
            }
        }

        $this->Viewer_AssignAjax('iMaxIdComment',$iMaxIdComment);
        $this->Viewer_AssignAjax('aComments',$aComments);
    }

    /**
     * Проверка авторизации Вконтакте
     * http://vk.com/dev/openapi_auth
     */
    function checkVkAuth() {
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
            $sign = $this->getSign($session, $vk_secret);
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
    function checkFbAuth() {
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
    function checkMrAuth() {
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
    function getVkUserInfo($session) {
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
    function getFbUserInfo($session) {
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
    function getMrUserInfo($session) {
        $mr_id = Config::Get('plugin.newsocialcomments.mr_id');
        $mr_secret = Config::Get('plugin.newsocialcomments.mr_secret');

        if (isset($session['session_key'])) {
            $params = array(
                'method'       => 'users.getInfo',
                'secure'       => '1',
                'app_id'       => $mr_id,
                'session_key'  => $session['session_key'],
            );
            $params['sig'] = $this->getSign($params, $mr_secret);
            $user_info = json_decode(@file_get_contents('http://www.appsmail.ru/platform/api' . '?' . urldecode(http_build_query($params))), true);
            return (is_array($user_info) && count($user_info) ? $user_info[0] : false);
        }
        return false;
    }

    /**
     * Вычисление хеша
     *
     */
    function getSign(array $keys, $secret_key) {
        ksort($keys);
        $params = '';
        foreach ($keys as $key => $value) {
            $params .= ($key . '=' . $value);
        }
        return md5($params . $secret_key);
    }
}
?>