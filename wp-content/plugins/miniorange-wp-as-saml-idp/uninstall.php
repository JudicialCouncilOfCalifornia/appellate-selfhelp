<?php
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
	    exit();

	global $wpdb;	
	delete_site_option('mo_idp_host_name');
	delete_site_option('mo_idp_transactionId');
	delete_site_option('mo_idp_admin_password');
	delete_site_option('mo_idp_registration_status');
	delete_site_option('mo_idp_admin_phone');
	delete_site_option('mo_idp_new_registration');
	delete_site_option('mo_idp_admin_customer_key');
	delete_site_option('mo_idp_admin_api_key');
	delete_site_option('mo_idp_customer_token');
	delete_site_option('mo_idp_verify_customer');
	delete_site_option('mo_idp_message');
	delete_site_option('mo_idp_admin_email');
	delete_site_option('mo_saml_idp_plugin_version');
	delete_site_option('sml_idp_lk');
	delete_site_option('t_site_status');
	delete_site_option('site_idp_ckl');
	delete_site_option('mo_idp_usr_lmt');
	delete_site_option('mo_idp_entity_id');

	
	$sql =  is_multisite() ? "DROP TABLE mo_sp_attributes" : "DROP TABLE ". $wpdb->prefix . 'mo_sp_attributes';
	$wpdb->query($sql);

	$sql = is_multisite() ? "DROP TABLE mo_sp_data" : "DROP TABLE ". $wpdb->prefix . 'mo_sp_data';
	$wpdb->query($sql);

?>