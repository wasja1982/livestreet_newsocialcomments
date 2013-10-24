
function vk_auth(response) {
	if (response.status === 'connected') {
		var uid = response.session.mid
		var sid = response.session.sid

		var get_avatar=function(response){
			var avatar = response.response[0].photo;
			$("#form_comment").append("<input type='hidden' name='social_avatar' id='social_avatar' value='"+avatar+"' />");
			var uid = response.response[0].uid;
			$("#form_comment").append("<input type='hidden' name='social_profile' id='social_profile' value='https://vk.com/id"+uid+"' />");
		}

		VK.Api.call('getProfiles',{
			uids: uid ,
			fields:'photo'},
			get_avatar
		);

		var name = response.session.user.first_name + ' ' + response.session.user.last_name
		$("#guest_name").attr("value",name);
		$("#social_info span.name").text(name);
		$("#social_info span.icon").addClass("small_vk_icon");
		$("#social_info").show();
		$("#social_chooser, #capcha, #guest_input, #guest_email").hide();
		$("#form_comment").append("<input type='hidden' name='social' id='social' value='vk' />");
		$("#sc_exit").addClass("vk");

	} else {
		FB.Event.subscribe('auth.statusChange', facebook_auth);
		if (!FB.init({appId: fb_id, xfbml: true, cookie: true, oauth: true})) {
		}
	}
}

function facebook_auth(response) {
	if (response.authResponse) {
		var uid = response.authResponse.userID;
		var token = response.authResponse.accessToken;

		FB.api('/me', function(response) {
			$("#guest_name").attr("value",response.name);
			$("#social_info span.name").text(response.name);
			$("#social_info span.icon").addClass("small_fb_icon");
			$("#social_info").show();
			$("#social_chooser, #capcha, #guest_input, #guest_email").hide();
			$("#form_comment").append("<input type='hidden' name='social' id='social' value='fb' />");
			$("#sc_exit").addClass("fb");

			var avatar = 'http://graph.facebook.com/' + response.id + '/picture';
			$("#form_comment").append("<input type='hidden' name='social_avatar' id='social_avatar' value='"+avatar+"' />");
			$("#form_comment").append("<input type='hidden' name='social_profile' id='social_profile' value='https://www.facebook.com/profile.php?id="+uid+"' />");
		});
	} else {
		$("#guest_name").attr("value","");
		$("#social_info span.name").text("");
		$("#social_info span.icon").removeClass("small_fb_icon");
		$("#social_info").hide();
		$("#social_chooser, #capcha, #guest_input, #guest_email").show();
		$("#form_comment").find("#social").remove();
		$("#sc_exit").removeClass("fb");
		$("#form_comment").find("#social_avatar").remove();
		$("#form_comment").find("#social_profile").remove();
	}
}

function check_vk_status(response) {
	if (response.status != 'connected') {
		$("#guest_name").attr("value","");
		$("#social_info span.name").text("");
		$("#social_info span.icon").removeClass("small_vk_icon");
		$("#social_info").hide();
		$("#social_chooser, #capcha, #guest_input, #guest_email").show();
		$("#form_comment").find("#social").remove();
		$("#sc_exit").removeClass("vk");
		$("#form_comment").find("#social_avatar").remove();
		$("#form_comment").find("#social_profile").remove();
	}
}

$(function() {
	$('.login.small_vk_icon').live("click",function(){
		VK.Auth.login(vk_auth, 1027);
		return false
	});
	$('.login.small_fb_icon').live("click",function(){
		if (!FB.init({appId: fb_id, xfbml: true, cookie: true, oauth: true})) {
		}
		FB.login(function(){}, {scope : 'user_relationships,publish_stream,offline_access'})
		return false
	});
	$('#sc_exit.vk').live("click",function(){
		VK.Auth.logout(check_vk_status);
		return false;
	});
	$('#sc_exit.fb').live("click",function(){
		FB.logout(facebook_auth);
		return false;
	});
	VK.init({apiId: vk_id});
	VK.Auth.getLoginStatus(vk_auth, true);

});
