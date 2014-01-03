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
{literal}
<script type="text/javascript">
	var vk_id = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.vk_id')}{literal}';
	var fb_id = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.fb_id')}{literal}';
	var mr_id = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.mr_id')}{literal}';
	var mr_private = '{/literal}{$oConfig->GetValue('plugin.newsocialcomments.mr_private')}{literal}';
	var guest_enabled = {/literal}{if $oConfig->GetValue('plugin.newsocialcomments.enabled')}true{else}false{/if}{literal};
	var use_auto_login = {/literal}{if $oConfig->GetValue('plugin.newsocialcomments.use_auto_login')}true{else}false{/if}{literal};
	var use_vk_api = {/literal}{if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api')}true{else}false{/if}{literal};
	var use_fb_api = {/literal}{if $oConfig->GetValue('plugin.newsocialcomments.use_fb_api')}true{else}false{/if}{literal};
	var use_mr_api = {/literal}{if $oConfig->GetValue('plugin.newsocialcomments.use_mr_api')}true{else}false{/if}{literal};
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
    jQuery(document).ready(function($){
        {foreach from=$aComments item=oComment}
            {if !$oComment->getDelete()}
                $("div#comment_wrapper_id_{$oComment->getId()} ul.comment-info").first().append('<li><a href="#" onclick="ls.comments.toggleCommentForm({$oComment->getId()}); return false;" class="reply-link link-dotted">{$aLang.comment_answer}</a></li>');
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
    });
</script>
{include file='editor.tpl' sImgToLoad='form_comment_text' sSettingsTinymce='ls.settings.getTinymceComment()' sSettingsMarkitup='ls.settings.getMarkitupComment()'}

<h4 class="reply-header" id="comment_id_0">
    <a href="#" class="{if $oConfig->GetValue('plugin.newsocialcomments.is_mobile')}button{else}link-dotted{/if}" onclick="ls.comments.toggleCommentForm(0);document.getElementById('commentCaptcha').src='{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}&n='+Math.random(); return false;">{$sNoticeCommentAdd}</a>
</h4>
<div id="reply" class="reply">
    <form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
        {if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api') or $oConfig->GetValue('plugin.newsocialcomments.use_fb_api')}
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

        <div id="guest_text">
        {hook run='form_add_comment_begin'}

        <textarea name="comment_text" id="form_comment_text" class="mce-editor markitup-editor input-width-full"></textarea>

        {hook run='form_add_comment_end'}

        <div id="capcha">
                <img src="{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}" id="commentCaptcha" onclick="this.src='{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}&n='+Math.random();" class="captcha-image">
                <input type="text" name="captcha" maxlength="3" value="" class="input-text input-width-300" placeholder="{$aLang.plugin.newsocialcomments.newsocialcomments_captcha}"><br /><br />
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