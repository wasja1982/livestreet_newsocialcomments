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
 * Обрабатывает RSS
 * Автор класса vovazol(http://livestreet.ru/profile/vovazol/)
 *
 */
class PluginNewsocialcomments_ActionRss extends PluginNewsocialcomments_Inherit_ActionRss {


	protected function RssComments() {
		/**
		 * Вычисляем топики из закрытых блогов, чтобы исключить их из выдачи
		 */
		$aCloseTopics = $this->Topic_GetTopicsCloseByUser();		
		
		$aResult=$this->Comment_GetCommentsAll('topic',1,Config::Get('module.comment.per_page')*2,$aCloseTopics);
		$aComments=$aResult['collection'];
		
		$aChannel['title']=Config::Get('view.name');
		$aChannel['link']=Config::Get('path.root.web');
		$aChannel['description']=Config::Get('path.root.web').' / RSS channel';
		$aChannel['language']='ru';
		$aChannel['managingEditor']=Config::Get('general.rss_editor_mail');
		$aChannel['generator']=Config::Get('path.root.web');
		
		$comments=array();
		foreach ($aComments as $oComment){
			$item['title']='Comments: '.$oComment->getTarget()->getTitle();
			$item['guid']=$oComment->getTarget()->getUrl().'#comment'.$oComment->getId();
			$item['link']=$oComment->getTarget()->getUrl().'#comment'.$oComment->getId();
			$item['description']=$oComment->getText();
			$item['pubDate']=$oComment->getDate();
			
			if ($oComment->getUserId())
				$item['author']=$oComment->getUser()->getLogin();
			else
				$item['author']=$oComment->getGuestName();
			$item['category']='comments';
			$comments[]=$item;
		}
		
		$this->InitRss();
		$this->Viewer_Assign('aChannel',$aChannel);
		$this->Viewer_Assign('aItems',$comments);
		$this->SetTemplateAction('index');
	}

	protected function RssTopicComments() {
		$sTopicId=$this->GetParam(0);
		
		if (!($oTopic=$this->Topic_GetTopicById($sTopicId)) or !$oTopic->getPublish() or $oTopic->getBlog()->getType()=='close') {
			return parent::EventNotFound();
		}
		
		$aComments=$this->Comment_GetCommentsByTargetId($oTopic->getId(),'topic');
		$aComments=$aComments['comments'];
		
		$aChannel['title']=Config::Get('view.name');
		$aChannel['link']=Config::Get('path.root.web');
		$aChannel['description']=Config::Get('path.root.web').' / RSS channel';
		$aChannel['language']='ru';
		$aChannel['managingEditor']=Config::Get('general.rss_editor_mail');
		$aChannel['generator']=Config::Get('path.root.web');
		
		$comments=array();
		foreach ($aComments as $oComment){
			$item['title']='Comments: '.$oTopic->getTitle();
			$item['guid']=$oTopic->getUrl().'#comment'.$oComment->getId();
			$item['link']=$oTopic->getUrl().'#comment'.$oComment->getId();
			$item['description']=$oComment->getText();
			$item['pubDate']=$oComment->getDate();
			
			if ($oComment->getUserId())
				$item['author']=$oComment->getUser()->getLogin();
			else
				$item['author']=$oComment->getGuestName();
			$item['category']='comments';
			$comments[]=$item;
		}
		
		$this->InitRss();
		$this->Viewer_Assign('aChannel',$aChannel);
		$this->Viewer_Assign('aItems',$comments);
		$this->SetTemplateAction('index');	
	}
	
}
?>