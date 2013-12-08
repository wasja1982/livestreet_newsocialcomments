<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.5
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 **/

class PluginNewsocialcomments_HookAddcomment extends Hook
{
    public function RegisterHook()
    {
        $this->AddHook('template_comment_tree_end', 'InjectAddLink');
        if (Config::Get('plugin.newsocialcomments.show_icon')) {
            $this->AddHook('template_comment_tree_end', 'InjectIcon');
        }
    }

    public function InjectAddLink($aParam)
    {
        $sTemplatePath = Plugin::GetTemplatePath(__CLASS__) . 'inject_addcomment_command.tpl';
        if ($this->Viewer_TemplateExists($sTemplatePath)) {
            if (!$this->User_GetUserCurrent() && (
                Config::Get('plugin.newsocialcomments.enabled') ||
                Config::Get('plugin.newsocialcomments.use_vk_api') ||
                Config::Get('plugin.newsocialcomments.use_fb_api') ||
                Config::Get('plugin.newsocialcomments.use_mr_api'))) {
                    $this->Viewer_Assign('iTargetId', $aParam['iTargetId']);
                    $this->Viewer_Assign('sTargetType', $aParam['sTargetType']);
                    $this->Viewer_Assign('oUserCurrent', $this->User_GetUserCurrent());
                    $oTopic = $this->Topic_GetTopicById($aParam['iTargetId']);
                    if ($oTopic) $this->Viewer_Assign('bAllowNewComment', $oTopic->getForbidComment());
                    $this->Viewer_Assign('sNoticeCommentUnregistered', $this->Lang_Get('comment_unregistered'));
                    $this->Viewer_Assign('sNoticeCommentAdd', $this->Lang_Get('topic_comment_add'));
                    return $this->Viewer_Fetch($sTemplatePath);
            }
        }
    }

    public function InjectIcon($aParam)
    {
        $sTemplatePath = Plugin::GetTemplatePath(__CLASS__) . 'inject_comment_icon.tpl';
        if ($this->Viewer_TemplateExists($sTemplatePath)) {
            return $this->Viewer_Fetch($sTemplatePath);
        }
    }
}
?>