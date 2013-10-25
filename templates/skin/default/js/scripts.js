function vk_auth(response) {
    if (use_vk_api && response.status === 'connected') {
        var uid = response.session.mid;
        var name = response.session.user.first_name + ' ' + response.session.user.last_name

        VK.Api.call('getProfiles',{
            uids: uid,
            fields:'photo'
        }, function(response){
            var avatar = response.response[0].photo;
            var profile = 'https://vk.com/id' + response.response[0].uid;
            fill_form("vk", name, avatar, profile);
        });
    } else {
        if (use_fb_api) {
            FB.getLoginStatus(facebook_auth, true);
        }
    }
}

function facebook_auth(response) {
    if (use_fb_api && response.status === 'connected') {
        var uid = response.authResponse.userID;

        FB.api('/me', function(response) {
            var avatar = 'http://graph.facebook.com/' + response.id + '/picture';
            var profile = 'https://www.facebook.com/profile.php?id=' + uid;
            fill_form("fb", response.name, avatar, profile);
        });
    }
}

function fill_form(type, name, avatar, profile) {
    $("#guest_name").attr("value",name);
    $("#social_info span.name").text(name);
    $("#social_info span.icon").addClass("small_" + type + "_icon");
    $("#social_info, #guest_text").show();
    $("#social_chooser, #capcha, #guest_input, #guest_email").hide();
    $("#form_comment").append("<input type='hidden' name='social' id='social' value='" + type + "' />");
    $("#sc_exit").addClass(type);
    $("#form_comment").append("<input type='hidden' name='social_avatar' id='social_avatar' value='"+avatar+"' />");
    $("#form_comment").append("<input type='hidden' name='social_profile' id='social_profile' value='"+profile+"' />");
}

function clear_form(type) {
    $("#guest_name").attr("value","");
    $("#social_info span.name").text("");
    $("#social_info span.icon").removeClass("small_" + type + "_icon");
    $("#social_info").hide();
    $("#social_chooser").show();
    if (guest_enabled) {
        $("#capcha, #guest_input, #guest_email, #guest_text").show();
    } else {
        $("#capcha, #guest_input, #guest_email, #guest_text").hide();
    }
    $("#form_comment").find("#social").remove();
    $("#sc_exit").removeClass(type);
    $("#form_comment").find("#social_avatar").remove();
    $("#form_comment").find("#social_profile").remove();
}

$(function() {
    $("#social_info").hide();
    $("#social_chooser").show();
    if (guest_enabled) {
        $("#capcha, #guest_input, #guest_email, #guest_text").show();
    } else {
        $("#capcha, #guest_input, #guest_email, #guest_text").hide();
    }
    if (use_vk_api) {
        VK.init({
            apiId: vk_id
        });
    }
    if (use_fb_api) {
        FB.init({
            appId: fb_id,
            xfbml: true,
            cookie: true,
            oauth: true
        });
    }
    $('.login.small_vk_icon').live("click",function(){
        VK.Auth.login(vk_auth, 0);
        return false
    });
    $('.login.small_fb_icon').live("click",function(){
        FB.login(function(){
            FB.getLoginStatus(facebook_auth, true);
        }, {
            scope : ''
        })
        return false
    });
    $('#sc_exit.vk').live("click",function(){
        clear_form("vk");
        VK.Auth.logout(function(){
            if (use_fb_api) {
                FB.getLoginStatus(facebook_auth, true);
            }
        });
        return false;
    });
    $('#sc_exit.fb').live("click",function(){
        clear_form("fb");
        FB.logout(function(){
            if (use_vk_api) {
                VK.Auth.getLoginStatus(vk_auth, true);
            }
        });
        return false;
    });
    if (use_vk_api) {
        VK.Auth.getLoginStatus(vk_auth, true);
    } else if (use_fb_api) {
        FB.getLoginStatus(facebook_auth, true);
    }
});
