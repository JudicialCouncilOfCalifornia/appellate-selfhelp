<?php 

			
	if(current_user_can( 'manage_options' )  && isset($_POST['option']) )
	{
		$option = sanitize_text_field($_POST['option']);
		switch($option)
		{
			case "mowaf_demo_request_form":
				mowaf_handle_demo_request_form($_POST); break;
		}
	}
	include $mowaf_dirname . 'views'.DIRECTORY_SEPARATOR.'request_demo.php';
	
	function mowaf_handle_demo_request_form($post){
		$nonce 	 	= sanitize_text_field($post['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'mo2f-Request-demo' ) ){
	   			return;
	   		}
		$usecase 	= sanitize_text_field($post['mo_wafA_demo_usecase']);
		$email   	= sanitize_email($post['mo_wafA_demo_email']);
		$demo_plan  = sanitize_text_field($post['mo_wafA_demo_plan']);
		if(empty($usecase) || empty($email) || empty($demo_plan) )
		{
			do_action('mowaf_show_message',MOWAF_Messages::showMessage('DEMO_FORM_ERROR'),'SUCCESS');
			return;
		}
		else{
			$query     = 'REQUEST FOR DEMO';
			$query    .= ' =>';
			$query    .= $demo_plan;
			$query    .= ' : ';
			$query    .= $usecase;
			$contact_us = new MOWAF_cURL();
			$submited = json_decode($contact_us->submit_contact_us($email, '', $query),true);

			if(json_last_error() == JSON_ERROR_NONE) 
				{
					do_action('mowaf_show_message',MOWAF_Messages::showMessage('SUPPORT_FORM_SENT'),'SUCCESS');
					return;
				}
			else{			
				do_action('mowaf_show_message',MOWAF_Messages::showMessage('SUPPORT_FORM_ERROR'),'ERROR');
				}
			}
	}
?>