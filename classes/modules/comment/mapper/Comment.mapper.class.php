<?php
/**
 * OpenСomments - плагин для гостевых комментариев
 *
 * Автор:	flexbyte 
 * Профиль:	http://livestreet.ru/profile/flexbyte/
 * Сайт:	http://flexbyte.com
 **/

class PluginOpencomments_ModuleComment_MapperComment extends PluginOpencomments_Inherit_ModuleComment_MapperComment {	
	
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