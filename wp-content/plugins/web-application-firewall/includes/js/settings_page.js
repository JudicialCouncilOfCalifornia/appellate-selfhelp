jQuery(document).ready(function () {
	
    $ = jQuery;

	//show and hide instructions
    $("#auth_help").click(function () {
        $("#auth_troubleshoot").toggle();
    });
	$("#conn_help").click(function () {
        $("#conn_troubleshoot").toggle();
    });
	
	$("#conn_help_user_mapping").click(function () {
        $("#conn_user_mapping_troubleshoot").toggle();
    });
	
	//show and hide attribute mapping instructions
    $("#toggle_am_content").click(function () {
        $("#show_am_content").toggle();
    });

	 //Instructions
    $("#mo_wpns_help_curl_title").click(function () {
    	$("#mo_wpns_help_curl_desc").slideToggle(400);
    });
	
	$("#mo_wpns_help_mobile_auth_title").click(function () {
    	$("#mo_wpns_help_mobile_auth_desc").slideToggle(400);
    });
	
	$("#mo_wpns_help_disposable_title").click(function () {
    	$("#mo_wpns_help_disposable_desc").slideToggle(400);
    });
	
	$("#mo_wpns_help_strong_pass_title").click(function () {
    	$("#mo_wpns_help_strong_pass_desc").slideToggle(400);
    });
	
	$("#mo_wpns_help_adv_user_ver_title").click(function () {
    	$("#mo_wpns_help_adv_user_ver_desc").slideToggle(400);
    });
	
	$("#mo_wpns_help_social_login_title").click(function () {
    	$("#mo_wpns_help_social_login_desc").slideToggle(400);
    });
	
	$("#mo_wpns_help_custom_template_title").click(function () {
    	$("#mo_wpns_help_custom_template_desc").slideToggle(400);
    });

    $(".feedback").click(function(){
         ajaxCall("dissmissfeedback",".feedback-notice",true);
    });

    $(".whitelist_self").click(function(){
        ajaxCall("whitelistself",".whitelistself-notice",true);
    });

    $(".infected_file_dismiss").click(function(){
        ajaxCall("dismissinfected",".file_infected-notice",true);
    });

    $(".infected_file_dismiss_always").click(function(){
        ajaxCall("dismissinfected_always",".file_infected-notice",true);
    });

    $(".new_plugin_dismiss").click(function(){
        ajaxCall("dismissplugin",".new_plugin_theme-notice",true);
    });

    $(".new_plugin_dismiss_always").click(function(){
        ajaxCall("dismissplugin_always",".new_plugin_theme-notice",true);
    });

    $(".weekly_dismiss").click(function(){
        ajaxCall("dismissweekly",".weekly_notice-notice",true);
    });

    $(".weekly_dismiss_always").click(function(){
        ajaxCall("dismissweekly_always",".weekly_notice-notice",true);
    });

    $(".wpns_premium_option :input").attr("disabled",true);

});


function ajaxCall(option,element,hide)
{
    jQuery.ajax({
            url: "",
            type: "GET",
            data: "option="+option,
            crossDomain: !0,
            dataType: "json",
            contentType: "application/json; charset=utf-8",
            success: function(o) {
                if (hide!=undefined)
                    jQuery(element).slideUp();
            },
            error: function(o, e, n) {}
        });
}