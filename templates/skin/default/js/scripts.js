var ls = ls || {};

ls.socialcomments = (function ($) {
    this.options = {
        vk_id: '0',
        fb_id: '0',
        mr_id: '0',
        mr_private: '0',
        use_vk_api: false,
        use_fb_api: false,
        use_mr_api: false,
        use_auto_login: true,
        guest_enabled: false
    };

    this.vk = (function ($) {
        this.options = {
            next: null,
            type: "vk"
        };
        this.init = function() {
            VK.init({
                apiId: ls.socialcomments.options.vk_id
            });
        };
        this.checkStatus = function() {
            VK.Auth.getLoginStatus(ls.socialcomments.vk.changeStatus, true);
        };
        this.changeStatus = function(response) {
            if (response.status === 'connected') {
                var uid = response.session.mid;
                var name = response.session.user.first_name + ' ' + response.session.user.last_name

                VK.Api.call('getProfiles',{
                    uids: uid,
                    fields:'photo'
                }, function(response){
                    var avatar = response.response[0].photo;
                    ls.socialcomments.fillForm(ls.socialcomments.vk.options.type, name, avatar, null, null);
                });
            } else if (ls.socialcomments.vk.options.next) {
                ls.socialcomments.vk.options.next.checkStatus();
            }
        };
        this.login = function() {
            VK.Auth.login(ls.socialcomments.vk.changeStatus, 0);
            return false;
        };
        this.logout = function() {
            ls.socialcomments.clearForm(ls.socialcomments.vk.options.type);
            VK.Auth.logout(function(){
                if (ls.socialcomments.vk.options.next) {
                    ls.socialcomments.vk.options.next.checkStatus();
                }
            });
            return false;
        };
        return this;
    }).call(this.vk || {},jQuery);

    this.fb = (function ($) {
        this.options = {
            next: null,
            type: "fb"
        };
        this.init = function() {
            FB.init({
                appId: ls.socialcomments.options.fb_id,
                xfbml: true,
                cookie: true,
                oauth: true
            });
        };
        this.checkStatus = function() {
            FB.getLoginStatus(ls.socialcomments.fb.changeStatus, true);
        };
        this.changeStatus = function(response) {
            if (response.status === 'connected') {
                var uid = response.authResponse.userID;

                FB.api('/me?fields=id,name,email', function(response) {
                    ls.socialcomments.fillForm(ls.socialcomments.fb.options.type, response.name, null, response.email, null);
                });
            } else if (ls.socialcomments.fb.options.next) {
                ls.socialcomments.fb.options.next.checkStatus();
            }
        };
        this.login = function() {
            FB.login(function(){
                FB.getLoginStatus(ls.socialcomments.fb.changeStatus, true);
            }, {
                scope: 'email'
            });
            return false;
        };
        this.logout = function() {
            ls.socialcomments.clearForm(ls.socialcomments.fb.options.type);
            FB.logout(function(){
                if (ls.socialcomments.fb.options.next) {
                    ls.socialcomments.fb.options.next.checkStatus();
                }
            });
            return false;
        };
        return this;
    }).call(this.fb || {},jQuery);

    this.mr = (function ($) {
        this.options = {
            next: null,
            type: "mr"
        };
        this.init = function() {
            mailru.loader.require('api', function() {
                mailru.connect.init(ls.socialcomments.options.mr_id, ls.socialcomments.options.mr_private);
                mailru.events.listen(mailru.connect.events.login, this.checkStatus);
                mailru.events.listen(mailru.connect.events.logout, function(){
                    if (ls.socialcomments.mr.options.next) {
                        ls.socialcomments.mr.options.next.checkStatus();
                    }
                });
            });
        };
        this.checkStatus = function() {
            mailru.connect.getLoginStatus(ls.socialcomments.mr.changeStatus);
        };
        this.changeStatus = function(response) {
            if (response.is_app_user == 1) {
                mailru.common.users.getInfo(function(user_list) {
                    var name = user_list[0].first_name + ' ' + user_list[0].last_name;
                    var avatar = (user_list[0].has_pic ? user_list[0].pic : null);
                    var email = user_list[0].email;
                    var profile = user_list[0].link;

                    ls.socialcomments.fillForm(ls.socialcomments.mr.options.type, name, avatar, email, profile);
                }, response.vid);
            } else if (ls.socialcomments.mr.options.next) {
                ls.socialcomments.mr.options.next.checkStatus();
            }
        };
        this.login = function() {
            mailru.connect.login([]);
            return false;
        };
        this.logout = function() {
            ls.socialcomments.clearForm(ls.socialcomments.mr.options.type);
            mailru.connect.logout();
            return false;
        };
        return this;
    }).call(this.mr || {},jQuery);

    this.fillForm = function(type, name, avatar, email, profile) {
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
    };

    this.clearForm = function(type) {
        $("#guest_name").attr("value","");
        $("#social_info span.name").text("");
        $("#social_info span.icon").removeClass("small_" + type + "_icon");
        $("#social_info").hide();
        $("#social_chooser").show();
        if (this.options.guest_enabled) {
            $("#capcha, #guest_input, #guest_email, #guest_text").show();
        } else {
            $("#capcha, #guest_input, #guest_email, #guest_text").hide();
        }
        $("#form_comment").find("#social").remove();
        $("#sc_exit").removeClass(type);
        $("#form_comment").find("#social_avatar").remove();
        $("#form_comment").find("#social_profile").remove();
    };

    this.init = function() {
        $("#social_info").hide();
        $("#social_chooser").show();
        if (this.options.guest_enabled) {
            $("#capcha, #guest_input, #guest_email, #guest_text").show();
        } else {
            $("#capcha, #guest_input, #guest_email, #guest_text").hide();
        }
        if (this.options.use_vk_api) {
            this.vk.init();
        }
        if (this.options.use_fb_api) {
            this.fb.init();
        }
        if (this.options.use_mr_api) {
            this.mr.init();
        }
        $('.login.small_vk_icon, .login.vk_icon').live("click", this.vk.login);
        $('.login.small_fb_icon, .login.fb_icon').live("click", this.fb.login);
        $('.login.small_mr_icon, .login.mr_icon').live("click", this.mr.login);
        $('#sc_exit.vk').live("click", this.vk.logout);
        $('#sc_exit.fb').live("click", this.fb.logout);
        $('#sc_exit.mr').live("click", this.mr.logout);
        if (this.options.use_auto_login) {
            if (this.options.use_vk_api) {
                if (this.options.use_fb_api) {
                    this.vk.options.next = this.fb;
                } else if (this.options.use_mr_api) {
                    this.vk.options.next = this.mr;
                }
            }
            if (this.options.use_fb_api && this.options.use_mr_api) {
                this.fb.options.next = this.mr;
            }
            if (this.options.use_vk_api) {
                this.vk.checkStatus();
            } else if (this.options.use_fb_api) {
                this.fb.checkStatus();
            } else if (this.options.use_mr_api) {
                this.mr.checkStatus();
            }
        }
    };

	return this;
}).call(ls.socialcomments || {},jQuery);

jQuery(document).ready(function() {
    ls.socialcomments.options.vk_id = vk_id;
    ls.socialcomments.options.fb_id = fb_id;
    ls.socialcomments.options.mr_id = mr_id;
    ls.socialcomments.options.mr_private = mr_private;
    ls.socialcomments.options.use_vk_api = use_vk_api;
    ls.socialcomments.options.use_fb_api = use_fb_api;
    ls.socialcomments.options.use_mr_api = use_mr_api;
    ls.socialcomments.options.use_auto_login = use_auto_login;
    ls.socialcomments.options.guest_enabled = guest_enabled;

    ls.socialcomments.init();
});