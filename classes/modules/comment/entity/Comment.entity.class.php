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

class PluginNewsocialcomments_ModuleComment_EntityComment extends PluginNewsocialcomments_Inherit_ModuleComment_EntityComment
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