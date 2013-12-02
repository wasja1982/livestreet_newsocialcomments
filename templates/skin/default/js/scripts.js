function vk_auth(response) {
    if (use_vk_api && response.status === 'connected') {
        var uid = response.session.mid;
        var name = response.session.user.first_name + ' ' + response.session.user.last_name

        VK.Api.call('getProfiles',{
            uids: uid,
            fields:'photo'
        }, function(response){
            var avatar = response.response[0].photo;
            fill_form("vk", name, avatar, null, null);
        });
    } else {
        if (use_auto_login) {
            if (use_fb_api) {
                FB.getLoginStatus(facebook_auth, true);
            } else if (use_mr_api) {
                mailru.connect.getLoginStatus(mr_auth);
            }
        }
    }
}

function facebook_auth(response) {
    if (use_fb_api && response.status === 'connected') {
        var uid = response.authResponse.userID;

        FB.api('/me?fields=id,name,email', function(response) {
            fill_form("fb", response.name, null, response.email, null);
        });
    } else {
        if (use_auto_login) {
            if (use_mr_api) {
                mailru.connect.getLoginStatus(mr_auth);
            }
        }
    }
}

function mr_auth(response) {
    if (use_mr_api && response.is_app_user == 1) {
        mailru.common.users.getInfo(function(user_list) {
            var name = user_list[0].first_name + ' ' + user_list[0].last_name;
            var avatar = (user_list[0].has_pic ? user_list[0].pic : null);
            var email = user_list[0].email;
            var profile = user_list[0].link;
            
            fill_form("mr", name, avatar, email, profile);
        }, response.vid);
    }
}

function fill_form(type, name, avatar, email, profile) {
    $("#guest_name").attr("value",name);
    $("#social_info span.name").text(name);
    $("#social_info span.icon").addClass("small_" + type + "_icon");
    $("#social_info, #guest_text").show();
    $("#social_chooser, #capcha, #guest_input, #guest_email").hide();
    if ($("#form_comment > input#social").length) $("#form_comment > input#social").attr("value", type);
    else $("#form_comment").append("<input type='hidden' name='social' id='social' value='" + type + "' />");
    if (email) $("#guest_email > input").attr("value", email);
    $("#sc_exit").addClass(type);
    if (avatar) 
        if ($("#form_comment > input#social_avatar").length) $("#form_comment > input#social_avatar").attr("value", avatar);
        else $("#form_comment").append("<input type='hidden' name='social_avatar' id='social_avatar' value='"+avatar+"' />");
    if (profile)
        if ($("#form_comment > input#social_profile").length) $("#form_comment > input#social_profile").attr("value", profile);
        else $("#form_comment").append("<input type='hidden' name='social_profile' id='social_profile' value='"+profile+"' />");
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

jQuery(document).ready(function() {
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
    if (use_mr_api) {
        mailru.loader.require('api', function() {
            mailru.connect.init(mr_id, mr_private);
            mailru.events.listen(mailru.connect.events.login, function(){
                mailru.connect.getLoginStatus(mr_auth);
            });
            mailru.events.listen(mailru.connect.events.logout, function(){
                if (use_auto_login) {
                    if (use_fb_api) {
                        FB.getLoginStatus(facebook_auth, true);
                    } else if (use_vk_api) {
                        VK.Auth.getLoginStatus(vk_auth, true);
                    }
                }
            });
        });
    }
    $('.login.small_vk_icon, .login.vk_icon').live("click",function(){
        VK.Auth.login(vk_auth, 0);
        return false
    });
    $('.login.small_fb_icon, .login.fb_icon').live("click",function(){
        FB.login(function(){
            FB.getLoginStatus(facebook_auth, true);
        }, {
            scope: 'email'
        })
        return false
    });
    $('.login.small_mr_icon, .login.mr_icon').live("click",function(){
        mailru.connect.login([]);
        return false
    });
    $('#sc_exit.vk').live("click",function(){
        clear_form("vk");
        VK.Auth.logout(function(){
            if (use_auto_login) {
                if (use_fb_api) {
                    FB.getLoginStatus(facebook_auth, true);
                } else if (use_mr_api) {
                    mailru.connect.getLoginStatus(mr_auth);
                }
            }
        });
        return false;
    });
    $('#sc_exit.fb').live("click",function(){
        clear_form("fb");
        FB.logout(function(){
            if (use_auto_login) {
                if (use_mr_api) {
                    mailru.connect.getLoginStatus(mr_auth);
                } else if (use_vk_api) {
                    VK.Auth.getLoginStatus(vk_auth, true);
                }
            }
        });
        return false;
    });
    $('#sc_exit.mr').live("click",function(){
        clear_form("mr");
        mailru.connect.logout();
        return false;
    });
    if (use_auto_login) {
        if (use_vk_api) {
            VK.Auth.getLoginStatus(vk_auth, true);
        } else if (use_fb_api) {
            FB.getLoginStatus(facebook_auth, true);
        } else if (use_mr_api) {
            mailru.connect.getLoginStatus(mr_auth);
        }
    }
});
