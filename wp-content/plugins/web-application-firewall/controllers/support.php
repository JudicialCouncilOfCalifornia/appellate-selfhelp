<?php

	global $mmp_dirName;
	
	if(current_user_can( 'manage_options' )  && isset($_POST['option']))
	{
		switch(sanitize_text_field($_POST['option']))
		{
			case "mo_wpns_send_query":
				mmp_handle_support_form(sanitize_email($_POST['query_email']),sanitize_text_field($_POST['query']),sanitize_text_field($_POST['query_phone']));		break;
		}
	}

	$current_user 	= wp_get_current_user();
	$email 			= get_option("mo_wpns_admin_email");
	$phone 			= get_option("mo_wpns_admin_phone");

	
	if(empty($email))
		$email 		= $current_user->user_email;

	include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'support.php';


	/* SUPPORT FORM RELATED FUNCTIONS */

	//Function to handle support form submit
	function mmp_handle_support_form($email,$query,$phone)
	{

		if( empty($email) || empty($query) )
		{
			do_action('mo_mmp_show_message',MowafMessages::showMessage('SUPPORT_FORM_VALUES'),'SUCCESS');
			return;
		}


		$query = sanitize_text_field( $query );
		$email = sanitize_text_field( $email );
		$phone = sanitize_text_field( $phone );
		$contact_us = new Mowaf_MocURL();
		$submited = json_decode($contact_us->submit_contact_us($email, $phone, $query),true);

		if(json_last_error() == JSON_ERROR_NONE && $submited) 
		{
			do_action('mo_mmp_show_message',MowafMessages::showMessage('SUPPORT_FORM_SENT'),'SUCCESS');
			return;
		}
			
		do_action('mo_mmp_show_message',MowafMessages::showMessage('SUPPORT_FORM_ERROR'),'ERROR');
	}