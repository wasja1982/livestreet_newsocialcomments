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

class PluginNewsocialcomments_ModuleStream extends PluginNewsocialcomments_Inherit_ModuleStream {
    public function ReadEvents($aEventTypes,$aUsersList,$iCount=null,$iFromId=null) {
        $aEvents = parent::ReadEvents($aEventTypes,$aUsersList,$iCount,$iFromId);
        if ($aEvents && count($aEvents)) {
            foreach ($aEvents as $key => $oEvent) {
                if ($oEvent && $oEvent->getEventType() == 'add_comment' && $oEvent->getUser() && $oEvent->getUser()->getId() == 0) {
                    $oComment = $oEvent->getTarget();
                    if ($oComment) {
                        $oUser = clone $oComment->getUser();
                        $oComment->setUser($oUser);
                        $oEvent->setUser($oUser);
                    }
                }
            }
        }
        return $aEvents;
    }
}