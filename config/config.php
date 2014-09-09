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

// Позволяет отключать добавление гостевых комментариев без деактивации плагина.
// При отключенном плагине вместо имени гостя будет отображаться guest.
$config['enabled']=true;

// Запрашивать e-mail для гостевых комментариев
$config['ask_mail']=true;

// Проверять существование сервера почты для e-mail гостя
$config['use_mail_check']=true;

// Дополнительная защита от подмены данных на клиенте - запрашиваются информация о пользователе напрямую в соцсети.
// Замедляет скорость добавления комментариев.
$config['use_server_check']=true;

// Автоматическая проверка авторизации при загрузке страницы
$config['use_auto_login']=true;

// Загружать скрипт, отображающий иконки соцсетей возле имени автора комментария.
// Вместо данного скрипта рекомендуется использовать изменение шаблона.
$config['show_icon']=true;

// Размер иконок - 16px (true) или 24px (false)
$config['use_small_icon']=true;

// Использовать стандартный парсер для обработки комментариев (разрешает вставлять видео и т.п.).
// Потенциально может снизить безопасность сайта. По умолчанию отключено (false).
$config['use_parser']=false;

// Добавлять в форму дополнительное поле для борьбы со спамом
$config['add_field']=true;

// Название дополнительного поля для борьбы со спамом
$config['field_name']="guest_phone";

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

// Разрешить авторизацию через Mail.ru
$config['use_mr_api']=true;
// Идентификатор приложения для Mail.ru
$config['mr_id']=000000;
// Приватный ключ приложения для Mail.ru
$config['mr_private']='00000000000000000000000000000000';
// Секретный ключ приложения для Mail.ru
$config['mr_secret']='00000000000000000000000000000000';

return $config;
?>