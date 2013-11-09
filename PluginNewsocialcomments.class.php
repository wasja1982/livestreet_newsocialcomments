<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.2
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
	}
}
?>