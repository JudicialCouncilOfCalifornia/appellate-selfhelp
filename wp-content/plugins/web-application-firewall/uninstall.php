<?php

	//if uninstall not called from WordPress exit
	if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
		exit();

	delete_option('mo_wpns_admin_customer_key');
	delete_option('mo_wpns_admin_api_key');
	delete_option('mo_wpns_customer_token');
	delete_option('mo_wpns_app_secret');
	delete_option('mo_wpns_message');
	delete_option('mo_wpns_transactionId');
	delete_option('mo_wpns_registration_status');

	delete_option('wpns_upgrade_button_clicked');

	
	
	delete_option('mo_wpns_enable_brute_force');
	delete_option('mo_wpns_show_remaining_attempts');
	delete_option('mo_wpns_enable_ip_blocked_email_to_admin');
	delete_option('mo_wpns_enable_unusual_activity_email_to_user');

	delete_option( 'mo_wpns_company');
  	delete_option( 'mo_wpns_firstName' );
 	delete_option( 'mo_wpns_lastName');
 	delete_option( 'mo_wpns_password');
 	delete_option( 'mo_wpns_admin_email');
 	delete_option( 'mo_wpns_admin_phone');

 	delete_option( 'mo_wpns_registration_status');
 	delete_option( 'mo_wpns_block_chrome');
 	delete_option( 'mo_wpns_block_firefox');
 	delete_option( 'mo_wpns_block_ie');
  delete_option( 'mo_wpns_block_safari');
  delete_option( 'mo_wpns_block_opera');
  delete_option( 'mo_wpns_block_edge');

  delete_option( 'mo_wpns_enable_htaccess_blocking');
  delete_option( 'mo_wpns_enable_user_agent_blocking');
  delete_option( 'mo_wpns_countrycodes');
  delete_option( 'mo_wpns_referrers');
  delete_option( 'protect_wp_config');
  delete_option( 'prevent_directory_browsing');
  delete_option( 'disable_file_editing');
  delete_option( 'mo_wpns_enable_comment_spam_blocking');
  delete_option( 'mo_wpns_enable_comment_recaptcha');

  delete_option( 'mo_wpns_slow_down_attacks');
  
 	delete_option( 'mo_wpns_enable_2fa');
 	delete_option( 'mo2f_activate_plugin');
	delete_option( 'mo_wpns_risk_based_access');
	delete_option( 'mo2f_deviceid_enabled');
	delete_option( 'mo_wpns_activate_recaptcha');

	delete_option( 'mo_wpns_activate_recaptcha_for_login');
	delete_option( 'mo_wpns_activate_recaptcha_for_registration');
	delete_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login');
	delete_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration');
	delete_option( 'mo_wpns_recaptcha_site_key');
 	delete_option( 'mo_wpns_recaptcha_secret_key');

 	delete_option('custom_user_template');
 	delete_option('custom_admin_template');
 	delete_option( 'mo_wpns_enable_fake_domain_blocking');
 	delete_option( 'mo_wpns_enable_advanced_user_verification');
 	delete_option('mo_customer_validation_wp_default_enable');
 	delete_option( 'mo_wpns_enable_social_integration');

 	delete_option( 'mo_mmp_scan_plugins');
 	delete_option( 'mo_mmp_scan_themes');
 	delete_option( 'mo_mmp_check_vulnerable_code');
 	delete_option( 'mo_mmp_check_sql_injection');
 	delete_option( 'mo_mmp_check_remote_file_inclusion');
 	delete_option( 'mo_mmp_scan_wp_files');
 	delete_option( 'mo_mmp_skip_folders');
 	delete_option( 'mo_mmp_check_external_link');
 	delete_option( 'mo_mmp_scan_files_with_repo');

 	delete_option('mo_mmp_dbversion');
	
	delete_option('mo2f_cron_hours');
 	delete_option('mo2f_enable_cron_backup');
 	delete_option('mo2f_cron_file_backup_hours');
 	delete_option('mo2f_enable_cron_file_backup');
 	delete_option('mo_file_backup_plugins');
 	delete_option('mo_file_backup_themes');
 	delete_option('mo_file_backup_wp_files');

 	delete_option('mo_mmp_scan_initialize');
 	delete_option( 'mo_mmp_switch_all');
	delete_option( 'mo_mmp_switch_waf');
	delete_option( 'mo_mmp_switch_loginspam');
	delete_option( 'mo_mmp_switch_backup');
	delete_option( 'mo_mmp_switch_adv_block');
	delete_option( 'mo_mmp_switch_reports');
	delete_option( 'mo_mmp_switch_notif');
	delete_option('mo_mmp_tab_count');
	delete_option('mo_mmp_scans_done');
	delete_option('mo_wpns_last_themes');
	delete_option('mo_wpns_last_plugins');
	delete_option('mo_mmp_last_scan_time');
	delete_option('infected_dismiss');
	delete_option('weekly_dismiss');
	delete_option('donot_show_infected_file_notice');
	delete_option('mo_mmp_infected_files');
	delete_option('donot_show_new_plugin_theme_notice');
	delete_option('donot_show_weekly_scan_notice');
	delete_option('mo_wpns_files_scanned');
	delete_option('downloaded_wordpress_repo_name');
	delete_option('mo_mmp_total_files');
	delete_option('mo_wpns_hide_malware_popup');
	delete_option('mo_mmp_warning_files');
	delete_option('mo2f_files_skipped');
	delete_option('mo2f_file_size_excess');
	delete_option('mo2f_download_done');
	delete_option('mo_wpns_scan_status');
	delete_option('mo_wpns_scan_mode');
	delete_option('mo_stop_scan');
	delete_option('mo_mmp_repo_status');
	delete_option('mo2f_report_id');
	delete_option('mo2f_scanverification');
	delete_option('mo_mmp_switch_malware');
	delete_option('mo_wpns_enable_log_requests');


	
	//drop custom db tables
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_transactions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_blocked_ips" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_whitelisted_ips" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_email_sent_audit" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_scan_report" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_scan_report_details" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_skip_files" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_malware_hash_file" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_files_scan" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpns_attack_logs" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wp_wpns_ip_rate_details" );

	
?>