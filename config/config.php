<?php
/**
 * OpenСomments - плагин для гостевых комментариев
 *
 * Автор:	flexbyte 
 * Профиль:	http://livestreet.ru/profile/flexbyte/
 * Сайт:	http://flexbyte.com
 **/

// Позволяет вкл\выкл добавление анонимных комментариев
// без отключения плагина, т.к. при отключенном плагине 
// вместо имени гостя будет отображаться guest 
$config['enabled']=true;

// Запрашивать e-mail
$config['ask_mail']=true;

// Разрешить авторизацию через Facebook
$config['use_fb_api']=true;
// Идентификатор приложения для Facebook
$config['fb_id']='000000000000000';

// Разрешить авторизацию через Вконтакте
$config['use_vk_api']=true;
// Идентификатор приложения для Вконтакте
$config['vk_id']=0000000;

return $config;
?>