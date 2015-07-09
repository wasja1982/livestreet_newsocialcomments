{literal}
<style>
	.small_vk_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -16px 0 no-repeat;}
	.small_fb_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -64px 0 no-repeat;}
	.small_mr_icon {padding:2px 0 0 16px;background:url({/literal}{$oConfig->GetValue('plugin.newsocialcomments.webpath')}{literal}images/auth_icons_small.png) -32px 0 no-repeat;}
	.login.small_vk_icon, .login.small_fb_icon, .login.small_mr_icon {cursor:pointer;margin: 0 0px 0 6px;}
</style>
{/literal}
<script type="text/javascript">
    jQuery(document).ready(function($){
        {foreach from=$aComments item=oComment}
            {if !$oComment->getDelete()}
                {if $oComment->getGuestType()}{assign var=oType value=$oComment->getGuestType()}
                    {if $oType=='fb'}
                    $("div#comment_wrapper_id_{$oComment->getId()} li.comment-author").first().prepend('<span class="small_fb_icon"/>&nbsp;');
                    {elseif $oType=='vk'}
                    $("div#comment_wrapper_id_{$oComment->getId()} li.comment-author").first().prepend('<span class="small_vk_icon"/>&nbsp;');
                    {elseif $oType=='mr'}
                    $("div#comment_wrapper_id_{$oComment->getId()} li.comment-author").first().prepend('<span class="small_mr_icon"/>&nbsp;');
                    {/if}
                {/if}
            {/if}
        {/foreach}
    });
</script>