{add_block group='toolbar' name='toolbar_comment.tpl'
	aPagingCmt=$aPagingCmt
	iTargetId=$iTargetId
	sTargetType=$sTargetType
	iMaxIdComment=$iMaxIdComment
}

{hook run='comment_tree_begin' iTargetId=$iTargetId sTargetType=$sTargetType}

{if $oConfig->GetValue('plugin.newsocialcomments.use_vk_api')}
	<script src="http://vkontakte.ru/js/api/openapi.js" type="text/javascript"></script>
{/if}
{if $oConfig->GetValue('plugin.newsocialcomments.use_fb_api')}
	<script src="http://connect.facebook.net/ru_RU/all.js" type="text/javascript"></script>
	<div id="fb-root"></div>
{/if}
{literal}
<script type="text/javascript">
	var vk_id = {/literal}{$oConfig->GetValue('plugin.newsocialcomments.vk_id')}{literal};
	var fb_id = {/literal}{$oConfig->GetValue('plugin.newsocialcomments.fb_id')}{literal};
</script>
<style>
	#social_info .icon {position:relative;top:2px;left:4px;padding:0;margin:0 2px 0 0;display:inline-block;width:16px;height:16px;}
	.small_vk_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -16px 0 no-repeat;}
	.small_fb_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -64px 0 no-repeat;}
	.login.small_vk_icon, .login.small_fb_icon {cursor:pointer;margin: 0 0px 0 6px;}
	#social_info .name {padding-left:3px;}
</style>
{/literal}

<div class="comments" id="comments">
	<header class="comments-header">
		<h3><span id="count-comments">{$iCountComment}</span> {$iCountComment|declension:$aLang.comment_declension:'russian'}</h3>
		
		{if $bAllowSubscribe and $oUserCurrent}
			<div class="subscribe">
				<input {if $oSubscribeComment and $oSubscribeComment->getStatus()}checked="checked"{/if} type="checkbox" id="comment_subscribe" class="input-checkbox" onchange="ls.subscribe.toggle('{$sTargetType}_new_comment','{$iTargetId}','',this.checked);">
				<label for="comment_subscribe">{$aLang.comment_subscribe}</label>
			</div>
		{/if}
	
		<a name="comments"></a>
	</header>

	{assign var="nesting" value="-1"}
	{foreach from=$aComments item=oComment name=rublist}
		{assign var="cmtlevel" value=$oComment->getLevel()}
		
		{if $cmtlevel>$oConfig->GetValue('module.comment.max_tree')}
			{assign var="cmtlevel" value=$oConfig->GetValue('module.comment.max_tree')}
		{/if}
		
		{if $nesting < $cmtlevel} 
		{elseif $nesting > $cmtlevel}
			{section name=closelist1  loop=$nesting-$cmtlevel+1}</div>{/section}
		{elseif not $smarty.foreach.rublist.first}
			</div>
		{/if}
		
		<div class="comment-wrapper" id="comment_wrapper_id_{$oComment->getId()}">
		
		{include file='comment.tpl'} 
		{assign var="nesting" value=$cmtlevel}
		{if $smarty.foreach.rublist.last}
			{section name=closelist2 loop=$nesting+1}</div>{/section}    
		{/if}
	{/foreach}
</div>				
	
	
{include file='comment_paging.tpl' aPagingCmt=$aPagingCmt}

{hook run='comment_tree_end' iTargetId=$iTargetId sTargetType=$sTargetType}

{if $bAllowNewComment}
	{$sNoticeNotAllow}
{else}
	{if $oUserCurrent}

		{include file='editor.tpl' sImgToLoad='form_comment_text' sSettingsTinymce='ls.settings.getTinymceComment()' sSettingsMarkitup='ls.settings.getMarkitupComment()'}
	
		<h4 class="reply-header" id="comment_id_0">
			<a href="#" class="link-dotted" onclick="ls.comments.toggleCommentForm(0); return false;">{$sNoticeCommentAdd}</a>
		</h4>
		
		
		<div id="reply" class="reply">		
			<form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">
				{hook run='form_add_comment_begin'}
				
				<textarea name="comment_text" id="form_comment_text" class="mce-editor markitup-editor input-width-full"></textarea>
				
				{hook run='form_add_comment_end'}
				
				<button type="submit"  name="submit_comment" 
						id="comment-button-submit" 
						onclick="ls.comments.add('form_comment',{$iTargetId},'{$sTargetType}'); return false;" 
						class="button button-primary">{$aLang.comment_add}</button>
				<button type="button" onclick="ls.comments.preview();" class="button">{$aLang.comment_preview}</button>
				
				<input type="hidden" name="reply" value="0" id="form_comment_reply" />
				<input type="hidden" name="cmt_target_id" value="{$iTargetId}" />
			</form>
		</div>
	{else}
		{include file='editor.tpl' sImgToLoad='form_comment_text' sSettingsTinymce='ls.settings.getTinymceComment()' sSettingsMarkitup='ls.settings.getMarkitupComment()'}
		
		<h4 class="reply-header" id="comment_id_0">
			<a href="#" class="link-dotted" onclick="ls.comments.toggleCommentForm(0);document.getElementById('commentCaptcha').src='{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}&n='+Math.random(); return false;">{$sNoticeCommentAdd}</a>
		</h4>	
		<div id="reply" class="reply">
			<form method="post" id="form_comment" onsubmit="return false;" enctype="multipart/form-data">	
				<div id="social_chooser">
					{$aLang.plugin.newsocialcomments.newsocialcomments_comment}: 
					<a class="small_vk_icon login" title="{$aLang.plugin.newsocialcomments.newsocialcomments_comment_vk}"></a>
					<a class="small_fb_icon login"  title="{$aLang.plugin.newsocialcomments.newsocialcomments_comment_fb}"></a>
				</div>
				<div id="social_info" style="display:none">
					{$aLang.plugin.newsocialcomments.newsocialcomments_hello}<span class="icon"></span> <span class="name"></span>
					(<a href="" id="sc_exit">{$aLang.plugin.newsocialcomments.newsocialcomments_exit}</a>)
				</div>
          		<div id="guest_input" style="padding-top:15px; padding-bottom:5px;"><b>{$aLang.plugin.newsocialcomments.newsocialcomments_name}:</b><input type="text" id="guest_name" class="input-text" name="guest_name" value="" style="width:200px;margin-left: 18px;" /></div>
          		{if $oConfig->GetValue('plugin.newsocialcomments.ask_mail')}
          		<div id="guest_email" style="padding-bottom:10px;"><b>E-Mail:</b> <input type="text" class="input-text" name="guest_email" value="" style="width:200px;" /> </div>
          		{/if}
          		

				{hook run='form_add_comment_begin'}
				
				<textarea name="comment_text" id="form_comment_text" class="mce-editor markitup-editor input-width-full"></textarea>
				
				{hook run='form_add_comment_end'}
				
          
          		<div id="capcha" style="padding-top:15px;">
						<b>{$aLang.plugin.newsocialcomments.newsocialcomments_captcha}:</b><br />
						<img src="{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}" id="commentCaptcha" onclick="this.src='{cfg name='path.root.engine_lib'}/external/kcaptcha/index.php?{$_sPhpSessionName}={$_sPhpSessionId}&n='+Math.random();"><br />
						<input type="text" name="captcha" value="" size="9" class="input-text" style="text-align: center;width:80px;"><br /><br />
				</div>
				<button type="submit"  name="submit_comment" 
						id="comment-button-submit" 
						onclick="ls.comments.add('form_comment',{$iTargetId},'{$sTargetType}'); return false;" 
						class="button button-primary">{$aLang.comment_add}</button>
				<button type="button" onclick="ls.comments.preview();" class="button">{$aLang.comment_preview}</button>
				
				<input type="hidden" name="reply" value="0" id="form_comment_reply" />
				<input type="hidden" name="cmt_target_id" value="{$iTargetId}" />
			</form>
		</div>
		
	{/if}
{/if}	


