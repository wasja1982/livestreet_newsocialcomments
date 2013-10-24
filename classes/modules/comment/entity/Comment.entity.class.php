<?php
/**
 * OpenСomments - плагин для гостевых комментариев
 *
 * Автор:	flexbyte
 * Профиль:	http://livestreet.ru/profile/flexbyte/
 * Сайт:	http://flexbyte.com
 **/

class PluginOpencomments_ModuleComment_EntityComment extends PluginOpencomments_Inherit_ModuleComment_EntityComment
{
	public function getGuestName() {
		return (isset($this->_aData['guest_name']) ? $this->_aData['guest_name'] : '');
	}
	public function getGuestEmail() {
		return (isset($this->_aData['guest_email']) ? $this->_aData['guest_email'] : '');
	}
	public function getGuestExtra() {
		return (isset($this->_aData['guest_extra']) ? $this->_aData['guest_extra'] : '');
	}
	public function getGuestAvatar() {
		$extra = $this->getGuestExtra();
		$extra = (empty($extra) ? array() : unserialize($extra));
		return (isset($extra['avatar']) ? $extra['avatar'] : '');
	}
	public function getGuestProfile() {
		$extra = $this->getGuestExtra();
		$extra = (empty($extra) ? array() : unserialize($extra));
		return (isset($extra['profile']) ? $extra['profile'] : '');
	}

	public function setGuestName($data) {
		$this->_aData['guest_name']=$data;
	}
	public function setGuestEmail($data) {
		$this->_aData['guest_email']=$data;
	}
	public function setGuestExtra($data) {
		$this->_aData['guest_extra']=$data;
	}
	public function setGuestAvatar($data) {
		$extra = $this->getGuestExtra();
		$extra = (empty($extra) ? array() : unserialize($extra));
		$extra['avatar'] = $data;
		$this->setGuestExtra(serialize($extra));
	}
	public function setGuestProfile($data) {
		$extra = $this->getGuestExtra();
		$extra = (empty($extra) ? array() : unserialize($extra));
		$extra['profile'] = $data;
		$this->setGuestExtra(serialize($extra));
	}
}
?>