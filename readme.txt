Плагин "New Social Comments" (версия 1.0.0) для LiveStreet 1.0.3

Основан на плагинах:
1) "OpenComments" (автор: flexbyte, модификации: iMind) - https://catalog.livestreetcms.com/addon/view/39/, https://github.com/iM1nd/opencomments
2) "Social Comments" (автор: 4ever4you) - https://catalog.livestreetcms.com/addon/view/201/


ОПИСАНИЕ

Позволяет посетителям оставлять комментарии, используя аккаунты социальных сетей, и гостевые комментарии без регистрации.
Поддерживается авторизация из социальных сетей Вконтакте и Facebook (для корректной работы необходимо получение идентификаторов для сайта).

Настройка плагина осуществляется редактированием файла "/plugins/newsocialcomments/config/config.php".

Поддерживаемые директивы:
1) $config['enabled'] - Позволяет отключать добавление гостевых комментариев без деактивации плагина.
При отключенном плагине вместо имени гостя будет отображаться guest. По умолчанию включено (true).

2) $config['ask_mail'] - Запрашивать e-mail для гостевых комментариев. По умолчанию включено (true).

3) $config['use_fb_api'] - Разрешить авторизацию через Facebook. По умолчанию разрешено (true).

4) $config['fb_id'] - Идентификатор приложения для Facebook. Уникальный идентификатор для своего сайта необходимо получить по ссылке https://developers.facebook.com/apps/?action=create

5) $config['use_vk_api'] - Разрешить авторизацию через Вконтакте. По умолчанию разрешено (true).

6) $config['vk_id'] - Идентификатор приложения для Вконтакте. Уникальный идентификатор для своего сайта необходимо получить по ссылке https://vk.com/editapp?act=create


УСТАНОВКА

1. Скопировать плагин в каталог /plugins/
2. Через панель управления плагинами (/admin/plugins/) запустить его активацию.
3. Активация будет успешной если пользователя с ID = 0 не существует в базе (см. prefix_user).
   В противном случае, надо выполнить вручную след. SQL запрос:
	
ALTER TABLE `prefix_comment`
        ADD  `guest_name` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
        ADD  `guest_email` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
        ADD  `guest_extra` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL


4. ВНИМАНИЕ!
Если у вас стоит другой шаблон, отличный от стандартного необходимо скопировать и изменить след. файлы:
	comment_tree.tpl, comment.tpl, block.stream_comment.tpl, comment_list.tpl
в /plugins/newsocialcomments/templates/skin/<имя_вашего_шаблона>

Изменения, к-е необходимо добавить можно найти с помощью утилиты WinMerge сравнив два файла, например:
файл 1 - /plugins/newsocialcomments/templates/skin/default/comment.tpl  и
файл 2 - /templates/skin/new-jquery/comment.tpl


АВТОР
Александр Вереник

САЙТ 
https://github.com/wasja1982/livestreet_newsocialcomments
