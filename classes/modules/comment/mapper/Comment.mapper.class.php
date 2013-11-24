<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.4
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 * Основан на плагинах:
 * 1) "OpenComments" (автор: flexbyte, модификации: iMind) - https://catalog.livestreetcms.com/addon/view/39/, https://github.com/iM1nd/opencomments
 * 2) "Social Comments" (автор: 4ever4you) - https://catalog.livestreetcms.com/addon/view/201/
 *
 **/

class PluginNewsocialcomments_ModuleComment_MapperComment extends PluginNewsocialcomments_Inherit_ModuleComment_MapperComment {	
	
	public function AddComment(ModuleComment_EntityComment $oComment) {
		if($oComment->getTargetType()=='topic'){
		$sql = "INSERT INTO ".Config::Get('db.table.comment')." 
			(comment_pid,
			target_id,
			target_type,
			target_parent_id,
			user_id,
			comment_text,
			comment_date,
			comment_user_ip,
			comment_text_hash,
            guest_name,
            guest_email,
            guest_extra
			)
			VALUES(?, ?d, ?, ?d, ?d, ?, ?, ?, ?, ?, ?, ?)
		";			
		if ($iId=$this->oDb->query($sql,$oComment->getPid(),$oComment->getTargetId(),$oComment->getTargetType(),$oComment->getTargetParentId(),$oComment->getUserId(),$oComment->getText(),$oComment->getDate(),$oComment->getUserIp(),$oComment->getTextHash(),$oComment->getGuestName(),$oComment->getGuestEmail(),$oComment->getGuestExtra())) 
		{
			return $iId;
		}	
		return false;
		}else{
		$sql = "INSERT INTO ".Config::Get('db.table.comment')." 
			(comment_pid,
			target_id,
			target_type,
			target_parent_id,
			user_id,
			comment_text,
			comment_date,
			comment_user_ip,
			comment_publish,
			comment_text_hash	
			)
			VALUES(?, ?d, ?, ?d, ?d, ?, ?, ?, ?d, ?)
		";
		if ($iId=$this->oDb->query($sql,$oComment->getPid(),$oComment->getTargetId(),$oComment->getTargetType(),$oComment->getTargetParentId(),$oComment->getUserId(),$oComment->getText(),$oComment->getDate(),$oComment->getUserIp(),$oComment->getPublish(),$oComment->getTextHash()))
		{
			return $iId;
		}
		return false;
		}
	}
	
}
?>