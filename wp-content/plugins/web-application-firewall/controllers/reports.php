<?php
	
	global $MowafUtility,$mmp_dirName;

	if(isset($_POST['option']) &&  sanitize_text_field($_POST['option'])=='mo_wpns_manual_clear' && wp_verify_nonce(sanitize_text_field($_POST['nonce']),'mo_wpns_report') ){
		global $wpdb;
		$wpdb->query("DELETE FROM ".$wpdb->prefix."wpns_transactions WHERE Status='success' or Status= 'pastfailed' or Status='failed' ");

	}

	if(isset($_POST['mo_wpns_manual_errorclear']) && sanitize_text_field($_POST['mo_wpns_manual_errorclear'])=='mo_wpns_manual_errorclear' && wp_verify_nonce(sanitize_text_field($_POST['nonce']),'mo_wpns_report') ){
		global $wpdb;
		$wpdb->query("DELETE FROM ".$wpdb->prefix."wpns_transactions WHERE Status='accessDenied'");

	}

	$mo_wpns_handler   = new MowafHandler();
	$logintranscations = $mo_wpns_handler->get_login_transaction_report();
	$errortranscations = $mo_wpns_handler->get_error_transaction_report();

	include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'reports.php';

?>
		
