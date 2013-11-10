<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.3
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 **/

class PluginNewsocialcomments_ModuleUser_EntityUser extends PluginNewsocialcomments_Inherit_ModuleUser_EntityUser {
    private $guestName = "";
    private $guestEmail = "";
    private $guestAvatar = "";
    private $guestProfile = "";

	public function setGuestName($guestName) {
        $this->guestName = $guestName;
    }
	public function setGuestEmail($guestEmail) {
        $this->guestEmail = $guestEmail;
    }
	public function setGuestAvatar($guestAvatar) {
        $this->guestAvatar = $guestAvatar;
    }
	public function setGuestProfile($guestProfile) {
        $this->guestProfile = $guestProfile;
    }

	/**
	 * Возвращает логин
	 *
	 * @return string|null
	 */
	public function getLogin() {
        if ($this->getId() || empty ($this->guestName)) return parent::getLogin();
        else return $this->guestName;
	}
	/**
	 * Возвращает емайл
	 *
	 * @return string|null
	 */
	public function getMail() {
        if ($this->getId()) return parent::getMail();
        else return $this->guestEmail;
	}
	/**
	 * Возвращает статус онлайн пользователь или нет
	 *
	 * @return bool
	 */
	public function isOnline() {
		return false;
	}
	/**
	 * Возвращает полный веб путь до аватара нужного размера
	 *
	 * @param int $iSize	Размер
	 * @return string
	 */
	public function getProfileAvatarPath($iSize=100) {
        if ($this->getId() || empty ($this->guestAvatar)) return parent::getProfileAvatarPath($iSize);
        else return $this->guestAvatar;
	}
	/**
	 * Возвращает веб путь до профиля пользователя
	 *
	 * @return string
	 */
	public function getUserWebPath() {
        if ($this->getId() || empty ($this->guestProfile)) return parent::getUserWebPath();
        else return $this->guestProfile;
	}
}
?>