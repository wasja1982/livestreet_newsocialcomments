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

/**
 * Запрещаем напрямую через браузер обращение к этому файлу.
 */
if (!class_exists('Plugin')) {
	die('Hacking attemp!');
}

class PluginNewsocialcomments extends Plugin {

	protected $aDelegates = array(
		'template' => array('comment_tree.tpl' => '_comment_tree.tpl', 'comment.tpl' => '_comment.tpl', 
		                    'blocks/block.stream_comment.tpl' => '_blocks/block.stream_comment.tpl', 'comment_list.tpl' => '_comment_list.tpl'),
	);
	
	protected $aInherits = array(
        'action' => array('ActionBlog','ActionAjax','ActionRss'),
		'mapper' => array('ModuleComment_MapperComment'),
        'entity' => array('ModuleComment_EntityComment')
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
        if (Config::Get('plugin.newsocialcomments.use_vk_api') || Config::Get('plugin.newsocialcomments.use_fb_api') || Config::Get('plugin.newsocialcomments.use_mr_api')) {
            $this->Viewer_AppendScript(Plugin::GetTemplatePath(__CLASS__)."js/scripts.js");
        }
        Config::Set('plugin.newsocialcomments.webpath', Plugin::GetTemplateWebPath(__CLASS__));
	}
}
?>