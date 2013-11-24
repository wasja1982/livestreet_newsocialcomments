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

class PluginNewsocialcomments_ModuleComment_EntityComment extends PluginNewsocialcomments_Inherit_ModuleComment_EntityComment
{
	/**
	 * Возвращает объект пользователя
	 *
	 * @return ModuleUser_EntityUser|null
	 */
	public function getUser() {
        $oUser = parent::getUser();
        if ($oUser && $oUser->getId() == 0) {
            $oUser->setGuestName($this->getGuestName());
            $oUser->setGuestEmail($this->getGuestEmail());
            $oUser->setGuestAvatar($this->getGuestAvatar());
            $oUser->setGuestProfile($this->getGuestProfile());
        }
		return $oUser;
	}

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
		if (isset($extra['avatar'])) {
            return $extra['avatar'];
        } else if (isset($extra['type']) && isset($extra['id'])) {
            switch ($extra['type']) {
                case "fb": return 'http://graph.facebook.com/' . $extra['id'] . '/picture';
            }
        } else {
            return null;
        }
	}
	public function getGuestProfile() {
		$extra = $this->getGuestExtra();
		$extra = (empty($extra) ? array() : unserialize($extra));
        if (isset($extra['profile'])) {
            return $extra['profile'];
        } else if (isset($extra['type']) && isset($extra['id'])) {
            switch ($extra['type']) {
                case "vk": return 'https://vk.com/id' . $extra['id'];
                case "fb": return 'https://www.facebook.com/profile.php?id=' . $extra['id'];
            }
        } else {
            return null;
        }
	}
    public function getGuestType() {
        if ($this->getUserId() != 0) {
            return "user";
        } else {
            $extra = $this->getGuestExtra();
            $extra = (empty($extra) ? array() : unserialize($extra));
            return (isset($extra['type']) && !empty($extra['type']) ? $extra['type'] : "guest");
        }
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
	private function setGuestExtraField($field, $data) {
		$extra = $this->getGuestExtra();
		$extra = (empty($extra) ? array() : unserialize($extra));
		$extra[$field] = $data;
		$this->setGuestExtra(serialize($extra));
	}
	public function setGuestAvatar($data) {
        $this->setGuestExtraField('avatar', $data);
	}
	public function setGuestProfile($data) {
        $this->setGuestExtraField('profile', $data);
	}
	public function setGuestType($data) {
        $this->setGuestExtraField('type', $data);
	}
	public function setGuestId($data) {
        $this->setGuestExtraField('id', $data);
	}
}
?>