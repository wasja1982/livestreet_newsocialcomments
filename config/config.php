<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.0
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 * Основан на плагинах:
 * 1) "OpenComments" (автор: flexbyte, модификации: iMind) - https://catalog.livestreetcms.com/addon/view/39/, https://github.com/iM1nd/opencomments
 * 2) "Social Comments" (автор: 4ever4you) - https://catalog.livestreetcms.com/addon/view/201/
 *
 **/

// Позволяет отключать добавление гостевых комментариев без деактивации плагина.
// При отключенном плагине вместо имени гостя будет отображаться guest.
$config['enabled']=true;

// Запрашивать e-mail
$config['ask_mail']=true;

// Разрешить авторизацию через Facebook
$config['use_fb_api']=true;
// Идентификатор приложения для Facebook
$config['fb_id']='000000000000000';
// Секретный ключ приложения для Facebook
$config['fb_secret']='00000000000000000000000000000000';

// Разрешить авторизацию через Вконтакте
$config['use_vk_api']=true;
// Идентификатор приложения для Вконтакте
$config['vk_id']=0000000;
// Секретный ключ приложения для Вконтакте
$config['vk_secret']='00000000000000000000';

return $config;
?>