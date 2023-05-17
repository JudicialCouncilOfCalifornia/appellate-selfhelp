<?php

global $MowafUtility,$mmp_dirName;


if(current_user_can( 'manage_options' )  && isset($_POST['option']))
{
	switch(sanitize_text_field($_POST['option']))
	{
		case "mo_wpns_content_protection":
			mmp_handle_content_protection($_POST);						break;
		case "mo_wpns_enable_comment_spam_blocking":
			mmp_handle_comment_spam_blocking($_POST);					break;
		case "mo_wpns_enable_comment_recaptcha":
			mmp_handle_comment_recaptcha($_POST);						break;
		case "mo_wpns_comment_recaptcha_settings":
			mmp_save_comment_recaptcha($_POST);						break;		
	}
}


$protect_wp_config 		= get_option('protect_wp_config') 		   			 ? "checked" : "";
$wp_config 		   		= site_url().'/wp-config.php';
$protect_wp_uploads		= get_option('prevent_directory_browsing') 			 ? "checked" : "";
$wp_uploads 	   		= get_site_url().'/wp-content/uploads';
$disable_file_editing	= get_option('disable_file_editing') 	   			 ? "checked" : ""; 
$plugin_editor			= get_site_url().'/wp-admin/plugin-editor.php';
$comment_spam_protect	= get_option('mo_wpns_enable_comment_spam_blocking') ? "checked" : "";
$enable_recaptcha 		= get_option('mo_wpns_enable_comment_recaptcha')     ? "checked" : "";
$test_recaptcha_url		= "";

if($enable_recaptcha)
{
	$test_recaptcha_url	= add_query_arg( array('option'=>'testrecaptchaconfig'), $_SERVER['REQUEST_URI'] );	
	$captcha_site_key	= get_option('mo_wpns_recaptcha_site_key'  );
	$captcha_secret_key = get_option('mo_wpns_recaptcha_secret_key');
}

include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'content-protection.php';

/* CONTENT PROTECTION FUNCTIONS */

//Function to save content protection settings
function mmp_handle_content_protection()
{
	isset($_POST['protect_wp_config']) 			? update_option('protect_wp_config'			, $_POST['protect_wp_config'])			: update_option('protect_wp_config'			,0);
	isset($_POST['prevent_directory_browsing']) ? update_option('prevent_directory_browsing', $_POST['prevent_directory_browsing'])	: update_option('prevent_directory_browsing',0);
	isset($_POST['disable_file_editing']) 		? update_option('disable_file_editing'		, $_POST['disable_file_editing'])		: update_option('disable_file_editing'		,0);
	$mo_wpns_htaccess_handler = new MowafHandler();
	$mo_wpns_htaccess_handler->update_htaccess_configuration();
	do_action('mo_mmp_show_message',MowafMessages::showMessage('CONTENT_PROTECTION_ENABLED'),'SUCCESS');
}


//Function to handle comment spam blocking
function mmp_handle_comment_spam_blocking($postvalue)
{
	$enable  = isset($postvalue['mo_wpns_enable_comment_spam_blocking']) ? true : false;
	update_option('mo_wpns_enable_comment_spam_blocking', $enable);
	if($enable)
		do_action('mo_mmp_show_message',MowafMessages::showMessage('CONTENT_SPAM_BLOCKING'),'SUCCESS');
	else
		do_action('mo_mmp_show_message',MowafMessages::showMessage('CONTENT_SPAM_BLOCKING_DISABLED'),'ERROR');
}


//Function to handle reCAPTCHA for comments
function mmp_handle_comment_recaptcha($postvalue)
{
	$enable  = isset($postvalue['mo_wpns_enable_comment_recaptcha']) ? true : false;
	update_option('mo_wpns_enable_comment_recaptcha', $enable);
	if($enable)
		do_action('mo_mmp_show_message',MowafMessages::showMessage('CONTENT_RECAPTCHA'),'SUCCESS');
	else
		do_action('mo_mmp_show_message',MowafMessages::showMessage('CONTENT_RECAPTCHA_DISABLED'),'ERROR');
}

function mmp_save_comment_recaptcha($postvalue){
	update_option('mo_wpns_recaptcha_site_key', $postvalue['mo_wpns_recaptcha_site_key']);
	update_option('mo_wpns_recaptcha_secret_key', $postvalue['mo_wpns_recaptcha_secret_key']);
	do_action('mo_mmp_show_message',MowafMessages::showMessage('RECAPTCHA_ENABLED'),'SUCCESS');
}