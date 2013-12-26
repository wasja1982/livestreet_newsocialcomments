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
 * Класс обработки ajax запросов
 *
 */
class PluginNewsocialcomments_ActionAjax extends PluginNewsocialcomments_Inherit_ActionAjax {


	/**
	 * Предпросмотр текста
	 *
	 */
	protected function EventPreviewText() {

		if ($this->oUserCurrent || Config::Get('plugin.newsocialcomments.use_parser')) {
			parent::EventPreviewText();
			return;
		}

		$sText=getRequestStr('text',null,'post');
		$sTextResult = nl2br(strip_tags($sText));
		$this->Viewer_AssignAjax('sText',$sTextResult);
	}


}
?>