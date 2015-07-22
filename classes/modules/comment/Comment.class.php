<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.7
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 **/

class PluginNewsocialcomments_ModuleComment extends PluginNewsocialcomments_Inherit_ModuleComment {
    public function IsUniqueName($name, $email) {
        return (empty($name) || empty($email) ? true : $this->oMapper->IsUniqueName(strtolower($name), strtolower($email)));
    }
}