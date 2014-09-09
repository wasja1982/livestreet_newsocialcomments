{if !$bAllowNewComment}
{if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api') or $oConfig->GetValue('plugin.newsocialcomments.use_fb_api') or $oConfig->GetValue('plugin.newsocialcomments.use_mr_api')}
	<script src="{$oConfig->GetValue('plugin.newsocialcomments.webpath')}js/socialcomments.js" type="text/javascript"></script>
{/if}
{if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api')}
	<script src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript"></script>
{/if}
{if $oConfig->GetValue('plugin.newsocialcomments.use_fb_api')}
	<script src="http://connect.facebook.net/ru_RU/all.js" type="text/javascript"></script>
	<div id="fb-root"></div>
{/if}
{if $oConfig->GetValue('plugin.newsocialcomments.use_mr_api')}
    <script src="http://cdn.connect.mail.ru/js/loader.js" type="text/javascript"></script>
{/if}
{if bQuoteComment}
<script type="text/javascript">
    var parent_url = "{$sParentUrl}";
    var add_link = {if $sTargetType == 'talk'}{if $oConfig->GetValue('plugin.quotecomment.add_link_talk')}true{else}false{/if}{else}{if $oConfig->GetValue('plugin.quotecomment.add_link_topic')}true{else}false{/if}{/if};
    var copy_whole = {if $oConfig->GetValue('plugin.quotecomment.copy_whole')}true{else}false{/if};
    var selected_empty_warning = "{$aLang.plugin.quotecomment.selected_empty}";
    var add_name = {if $sTargetType == 'talk'}{if $oConfig->GetValue('plugin.quotecomment.add_name_talk')}true{else}false{/if}{else}{if $oConfig->GetValue('plugin.quotecomment.add_name_topic')}true{else}false{/if}{/if};
    var add_name_message = "{$aLang.plugin.quotecomment.name_message}";
</script>
{/if}
{literal}
<script type="text/javascript">
    var social_enabled = [];{/literal}
    {if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api')}
    {literal}ls.socialcomments.options.vk_id = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.vk_id')}{literal}';
    social_enabled.push(ls.socialcomments.vk.options.type);{/literal}
    {/if}
    {if $oConfig->GetValue('plugin.newsocialcomments.use_fb_api')}
    {literal}ls.socialcomments.options.fb_id = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.fb_id')}{literal}';
    social_enabled.push(ls.socialcomments.fb.options.type);{/literal}
    {/if}
    {if $oConfig->GetValue('plugin.newsocialcomments.use_mr_api')}
    {literal}ls.socialcomments.options.mr_id = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.mr_id')}{literal}';
    ls.socialcomments.options.mr_private = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.mr_private')}{literal}';
    social_enabled.push(ls.socialcomments.mr.options.type);{/literal}
    {/if}
    {literal}ls.socialcomments.options.guest_enabled = {/literal}{if $oConfig->GetValue('plugin.newsocialcomments.enabled')}true{else}false{/if}{literal};
    ls.socialcomments.options.use_auto_login = {/literal}{if $oConfig->GetValue('plugin.newsocialcomments.use_auto_login')}true{else}false{/if}{literal};
</script>
<style>
	#social_info .icon {position:relative;top:2px;left:4px;padding:0;margin:0 2px 0 0;display:inline-block;}
	.small_vk_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -16px 0 no-repeat;width:16px;height:16px;}
	.small_fb_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -64px 0 no-repeat;width:16px;height:16px;}
	.small_mr_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -32px 0 no-repeat;width:16px;height:16px;}
	.vk_icon {padding:10px 0 0 24px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons.png) -24px 0 no-repeat;width:24px;height:24px;}
	.fb_icon {padding:10px 0 0 24px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons.png) -96px 0 no-repeat;width:24px;height:24px;}
	.mr_icon {padding:10px 0 0 24px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons.png) -48px 0 no-repeat;width:24px;height:24px;}
	.login.small_vk_icon, .login.small_fb_icon, .login.small_mr_icon, .login.vk_icon, .login.fb_icon, .login.mr_icon {cursor:pointer;margin: 0 3px 0 3px;}
	#social_info .name {padding-left:3px;}
</style>
{/literal}
<script type="text/javascript">
    function reloadCaptcha() {
        {hookb run="newsocialcomments_captcha_reload"}
        document.getElementById('commentCaptcha').src='{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}&n='+Math.random();
        {/hookb}
    }
    jQuery(document).ready(function($){
        {foreach from=$aComments item=oComment}
            {if !$oComment->getDelete()}
                $("div#comment_wrapper_id_{$oComment->getId()} ul.comment-info").first().append('<li><a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()});reloadCaptcha();return false;" class="reply-link link-dotted">{$aLang.comment_answer}</a></li>');
                {if $bQuoteComment}$("div#comment_wrapper_id_{$oComment->getId()} ul.comment-info").first().append('<li><a href="#" class="comment-delete link-dotted" onclick="ls.comments.addQuotedText({$oComment->getId()});reloadCaptcha();return false;">{$aLang.plugin.quotecomment.quote}</a></li>');{/if}
            {/if}
        {/foreach}
        $("div#content:contains('{$sNoticeCommentUnregistered}')")
        .contents()
        .filter(function(){
            return this.nodeType === 3;
        })
        .filter(function(){
            return this.nodeValue.indexOf('{$sNoticeCommentUnregistered}') != -1;
        })
        .each(function(){
            this.nodeValue = '';
        });

        ls.socialcomments.init(social_enabled);
    });
</script>
{include file='editor.tpl' sImgToLoad='form_comment_text' sSettingsTinymce='ls.settings.getTinymceComment()' sSettingsMarkitup='ls.settings.getMarkitupComment()'}

<h4 class="reply-header" id="comment_id_0">
    <a href="#" class="{if $oConfig->GetValue('plugin.newsocialcomments.is_mobile')}button{else}link-dotted{/if}" onclick="ls.comments.toggleCommentForm(0);reloadCaptcha();return false;">{$sNoticeCommentAdd}</a>
</h4>
<div id="reply" class="reply">
    <form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
        {if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api') or $oConfig->GetValue('plugin.newsocialcomments.use_fb_api') or $oConfig->GetValue('plugin.newsocialcomments.use_mr_api')}
            <div id="social_chooser">
                {$aLang.plugin.newsocialcomments.newsocialcomments_comment}:
                {if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api')}<a class="{if $oConfig->GetValue('plugin.newsocialcomments.use_small_icon')}small_{/if}vk_icon login" title="{$aLang.plugin.newsocialcomments.newsocialcomments_comment_vk}"></a>{/if}
                {if $oConfig->GetValue('plugin.newsocialcomments.use_fb_api')}<a class="{if $oConfig->GetValue('plugin.newsocialcomments.use_small_icon')}small_{/if}fb_icon login" title="{$aLang.plugin.newsocialcomments.newsocialcomments_comment_fb}"></a>{/if}
                {if $oConfig->GetValue('plugin.newsocialcomments.use_mr_api')}<a class="{if $oConfig->GetValue('plugin.newsocialcomments.use_small_icon')}small_{/if}mr_icon login" title="{$aLang.plugin.newsocialcomments.newsocialcomments_comment_mr}"></a>{/if}
            </div>
            <div id="social_info" style="display:none">
                {$aLang.plugin.newsocialcomments.newsocialcomments_hello}<span class="icon"></span> <span class="name"></span>
                (<a href="" id="sc_exit">{$aLang.plugin.newsocialcomments.newsocialcomments_exit}</a>)
            </div>
        {/if}
        <div id="guest_input" style="padding-top:15px; padding-bottom:5px;"><input type="text" id="guest_name" class="input-text input-width-full" name="guest_name" placeholder="{$aLang.plugin.newsocialcomments.newsocialcomments_name}" value="" /></div>
        {if $oConfig->GetValue('plugin.newsocialcomments.ask_mail')}
        <div id="guest_email" style="padding-bottom:10px;"><input type="text" class="input-text input-width-full" name="guest_email" placeholder="{$aLang.plugin.newsocialcomments.newsocialcomments_mail}" value="" /> </div>
        {/if}
        {if $oConfig->GetValue('plugin.newsocialcomments.add_field')}
        <div id="{$oConfig->GetValue('plugin.newsocialcomments.field_name')}" style="padding-bottom:10px;display:none"><input type="text" class="input-text input-width-full" name="{$oConfig->GetValue('plugin.newsocialcomments.field_name')}" value="" /> </div>
        {/if}

        <div id="guest_text">
        {hook run='form_add_comment_begin'}

        <textarea name="comment_text" id="form_comment_text" class="mce-editor markitup-editor input-width-full"></textarea>

        {hook run='form_add_comment_end'}

        <div id="captcha">
            {hookb run="newsocialcomments_captcha"}
            <img src="{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}" id="commentCaptcha" onclick="this.src='{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}&n='+Math.random();" class="captcha-image">
            <input type="text" name="captcha" maxlength="3" value="" class="input-text input-width-300" placeholder="{$aLang.plugin.newsocialcomments.newsocialcomments_captcha}">
            {/hookb}
        </div>
        <button type="submit"  name="submit_comment"
                id="comment-button-submit"
                onclick="ls.comments.add('form_comment',{$iTargetId},'{$sTargetType}'); return false;"
                class="button button-primary">{$aLang.comment_add}</button>
        <button type="button" onclick="ls.comments.preview();" class="button">{$aLang.comment_preview}</button>
        </div>

        <input type="hidden" name="reply" value="0" id="form_comment_reply" />
        <input type="hidden" name="cmt_target_id" value="{$iTargetId}" />
    </form>
</div>
{/if}