<?php
	
	global $MowafUtility,$mmp_dirName;

	if( isset( $_GET[ 'page' ])){
		$tab_count= get_site_option('mo_mmp_tab_count', 0);
		if($tab_count == 6)
				update_site_option('mo_mmp_switch_all', 1);
		else
			update_site_option('mo_mmp_switch_all', 0);
		switch(sanitize_text_field($_GET['page']))
		{
            case 'mo_mmp_login_and_spam':
            	update_option('mo_mmp_switch_loginspam', 1);
            	if($tab_count < 6)
            		update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
																	break;
			case 'mo_mmp_backup':
				update_option('mo_mmp_switch_backup', 1);
				if($tab_count < 6)
					update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
													 				break;
			case 'mo_mmp_waf':
				update_option('mo_mmp_switch_waf', 1);
				if($tab_count < 6)
					update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
														    		break;
			case 'mo_mmp_advancedblocking':
				update_option('mo_mmp_switch_adv_block', 1);
				if($tab_count < 6)
					update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
																	break;
			case 'mo_mmp_notifications':
				update_option('mo_mmp_switch_notif', 1);
				if($tab_count < 6)
					update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
																	break;
			case 'mo_mmp_reports':
				update_option('mo_mmp_switch_reports', 1);
				if($tab_count < 6)
					update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
																	break;
		}
	}

	$profile_url	= add_query_arg( array('page' => 'mo_mmp_account'		), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$login_security	= add_query_arg( array('page' => 'default'			), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$waf			= add_query_arg( array('page' => 'mo_mmp_waf'				), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$login_and_spam = add_query_arg( array('page' => 'mo_mmp_login_and_spam'   ), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$register_url	= add_query_arg( array('page' => 'mo_mmp_registration'		), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$blocked_ips	= add_query_arg( array('page' => 'mo_mmp_blockedips'		), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$advance_block	= add_query_arg( array('page' => 'mo_mmp_advancedblocking'	), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$notif_url		= add_query_arg( array('page' => 'mo_mmp_notifications'	), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$reports_url	= add_query_arg( array('page' => 'mo_mmp_reports'			), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$license_url	= add_query_arg( array('page' => 'mo_mmp_upgrade'  		), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$help_url		= add_query_arg( array('page' => 'mo_mmp_troubleshooting'	), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$content_protect= add_query_arg( array('page' => 'mo_mmp_content_protect'	), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$backup			= add_query_arg( array('page' => 'mo_mmp_backup'			),sanitize_text_field($_SERVER['REQUEST_URI'] ));
	$scan_url       = add_query_arg( array('page' => 'mo_mmp_malwarescan'      ), sanitize_text_field($_SERVER['REQUEST_URI'] ));
	//Added for new design
    $dashboard_url	= add_query_arg(array('page' => 'mo_mmp_dashboard'			), sanitize_text_field($_SERVER['REQUEST_URI']));
    $upgrade_url	= add_query_arg(array('page' => 'mo_mmp_upgrade'				), sanitize_text_field($_SERVER['REQUEST_URI']));
   //dynamic
    $logo_url = plugin_dir_url(dirname(__FILE__)) . 'includes/images/miniorange_logo.png';
    $shw_feedback	= get_option('donot_show_feedback_message') ? false: true;

    $moPluginHandler= new MowafHandler();
    $safe			= $moPluginHandler->is_whitelisted($MowafUtility->get_client_ip());

    $active_tab 	= sanitize_text_field($_GET['page']);

	include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'navbar.php';