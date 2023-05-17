<?php
/**
 * Plugin Name: Web Application Firewall
 * Description: Detect and prevent DoS attacks made by bots, crawlers. Restrict access based on country and IP ranges.
 * Version: 2.1.1
 * Author: miniOrange
 * Author URI: https://miniorange.com
 * License: GPL2
 */	
	define( 'MO_WAF_VERSION', '2.1.1' );
	define( 'TEST_MODE', false );
	class MOWAF{

		function __construct()
		{

            add_action('admin_menu', array( $this,'my_plugin_add_thickbox'));

			if (is_admin()) {
				add_filter('plugin_action_links', array($this, 'mo_waf_add_plugin_action_links'), 10, 2);
			}

            register_deactivation_hook(__FILE__		 , array( $this, 'mo_wpns_deactivate'		       )		);
			register_activation_hook  (__FILE__		 , array( $this, 'mo_wpns_activate'			       )		);
			register_activation_hook  (__FILE__		 , array( $this, 'mo_wpns_scan_automatic'		   )		);
			add_action( 'admin_menu'				 , array( $this, 'mo_wpns_widget_menu'		  	   )		);
			add_action( 'admin_enqueue_scripts'		 , array( $this, 'mo_wpns_settings_style'	       )		);
			add_action( 'admin_enqueue_scripts'		 , array( $this, 'mo_wpns_settings_script'	       )	    );
			add_action( 'mo_mmp_show_message'		 	 , array( $this, 'mo_show_message' 				   ), 1 , 2 );
			add_action( 'wp_footer'					 , array( $this, 'footer_link'					   ),100	);
            add_action( 'admin_footer', array( $this, 'feedback_request' ) );
            add_action( 'plugins_loaded', array( $this, 'mo_mmp_update_db' ) );
            add_action('admin_notices',array( $this, 'mo_wpns_malware_notices' ) );
			if(get_option('disable_file_editing')) 	 define('DISALLOW_FILE_EDIT', true);
			$this->includes();
			$notify = new Mowaf_miniorange_security_notification;
		    add_action('wp_dashboard_setup', array($notify,'my_custom_dashboard_widgets'));
		    add_action( 'scan_cron_hook', array($this, 'mo_wpns_scheduled_scan'));
		    add_action('admin_init',array($this, 'mo_mmp_redirect_page'));
		}
        // As on plugins.php page not in the plugin

		function mo_waf_add_plugin_action_links($links, $file) {
			if ($file == plugin_basename(dirname(__FILE__) . '/miniorange_firewall_settings.php')) {
				$links[]= '<a href="admin.php?page=mo_mmp_dashboard" style="font-weight:bold" >Dashboard</a>';
				$links[]='<a href="admin.php?page=mo_mmp_waf" style="font-weight:bold" >Firewall</a>';
				$links[]='<a href="admin.php?page=mo_mmp_upgrade" style="color:orange;font-weight:bold">Upgrade</a>';

			}
			return $links;
		}

        function feedback_request() {
            if ( 'plugins.php' != basename( $_SERVER['PHP_SELF'] ) ) {
                return;
            }
            global $mmp_dirName;

             $email = get_option("mo_wpns_admin_email");
            if(empty($email)){
                $user = wp_get_current_user();
                $email = $user->user_email;
            }
            $imagepath=plugins_url( '/includes/images/', __FILE__ );

            wp_enqueue_style( 'wp-pointer' );
            wp_enqueue_script( 'wp-pointer' );
            wp_enqueue_script( 'utils' );
            wp_enqueue_style( 'mo_wpns_admin_plugins_page_style', plugins_url( '/includes/css/style_settings.css', __FILE__ ),[],MO_WAF_VERSION);

            include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'feedback_form.php';;

        }
        function mo_mmp_redirect_page()
    	{
    		update_site_option('mo_mmp_switch_all', 1);

        	if(get_site_option('mo_mmp_plugin_redirect'))
        	{
            	delete_site_option('mo_mmp_plugin_redirect');
            	wp_redirect(admin_url().'admin.php?page=mo_mmp_dashboard');
                exit();
       	    }
    	}
    

        function mo_mmp_update_db(){
        	
        	global $wpnsDbQueries;
			$wpnsDbQueries->mo_plugin_activate();
        }

        function mo_wpns_malware_notices(){
        	$args=array();
			$theme_current= wp_get_themes($args);
			$theme_last = get_option('mo_wpns_last_themes');
			$flag_theme = 0;
			if(is_array($theme_last)){
				if(sizeof($theme_current) == sizeof($theme_last)){
					foreach ($theme_current as $key => $value) {
						if($theme_current[$key] != $theme_last[$key]){
							$flag_theme=1;
							break;
						}
					}
				}else{
					$flag_theme=1;
				}
			}else{
				$flag_theme=1;
			}

			$plugins_found = get_plugins();
			$plugin_last = get_option('mo_wpns_last_plugins');

			$flag_plugin = 0;
			if(is_array($plugin_last) && is_array($plugins_found)){
				if(sizeof($plugins_found) == sizeof($plugin_last)){
					foreach ($plugins_found as $key => $value ) {
						if($plugins_found[$key] != $plugin_last[$key]){
							$flag_plugin=1;
							break;
						}
					}
				}else{
					$flag_plugin=1;
				}
			}else{
				$flag_plugin=1;
			}
		    $days =(time()-get_option('mo_mmp_last_scan_time'))/(60*60*24);
		    $days = (int)$days;

		    $day_infected= (time()-get_option('infected_dismiss'))/(60*60*24);
		    $day_infected = floor($day_infected);
		    $day_weekly= (time()-get_option('weekly_dismiss'))/(60*60*24);
		    $day_weekly = floor($day_weekly);

	    	if(!get_option('donot_show_infected_file_notice') && (get_option('mo_mmp_infected_files') != 0 || get_option('mo_mmp_warning_files') !=0) && ($day_infected >= 1)){
	    		echo MowafMessages::showMessage('INFECTED_FILE');
	    	}else if(!get_option('donot_show_new_plugin_theme_notice') && ($flag_plugin || $flag_theme)){
	    		echo MowafMessages::showMessage('NEW_PLUGIN_THEME_CHECK');
	    	}else if(!get_option('donot_show_weekly_scan_notice') && ($days >= 7) && ($day_weekly >= 1)){
	    		echo MowafMessages::showMessage('WEEKLY_SCAN_CHECK');
	    	}
        }

		function mo_wpns_widget_menu()
		{
			$menu_slug = 'mo_mmp_malwarescan';
			add_menu_page (	'miniOrange Firewall ' , 'Firewall ' , 'activate_plugins', $menu_slug , array( $this, 'mo_wpns'), plugin_dir_url(__FILE__) . 'includes/images/miniorange_icon.png' );
			add_submenu_page( $menu_slug	,'Malware Protection'	,'Dashboard'		    ,'administrator', 'mo_mmp_dashboard'	, array( $this, 'mo_wpns'));
			add_submenu_page( $menu_slug	,'Malware Protection'	,'Malware Scan'			,'administrator','mo_mmp_malwarescan'  	, array( $this, 'mo_wpns'));
			add_submenu_page( $menu_slug	,'Malware Protection'	,'Firewall'		   			,'administrator','mo_mmp_waf'				, array( $this, 'mo_wpns'));
			add_submenu_page( $menu_slug	,'Malware Protection'	,'Login and Spam'		,'administrator','mo_mmp_login_and_spam'	, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Malware Protection'	,'Backup'				,'administrator','mo_mmp_backup'			, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Malware Protection'	,'Advanced Blocking'	,'administrator','mo_mmp_advancedblocking'	, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Malware Protection'	,'Notifications'		,'administrator','mo_mmp_notifications'	, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Malware Protection'	,'Reports'				,'administrator','mo_mmp_reports'			, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Malware Protection'	,'Troubleshooting'		,'administrator','mo_mmp_troubleshooting'	, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Malware Protection'	,'Account'				,'administrator','mo_mmp_account'			, array( $this, 'mo_wpns'));
            add_submenu_page( $menu_slug	,'Malware Protection'	,'<strong style="color:orange">Upgrade</strong>','administrator','mo_mmp_upgrade'			, array( $this, 'mo_wpns'));
        }

		function mo_wpns()
		{
			global $wpnsDbQueries;
			$wpnsDbQueries->mo_plugin_activate();
			
			add_option( 'mo_wpns_enable_brute_force' , true);
			add_option( 'mo_wpns_show_remaining_attempts' , true);
			add_option( 'mo_wpns_enable_ip_blocked_email_to_admin', true);
			add_option('SQLInjection', 1);
			add_option('WAFEnabled' ,0);
			add_option('XSSAttack' ,1);
			add_option('RFIAttack' ,0);
			add_option('LFIAttack' ,0);
			add_option('RCEAttack' ,0);
			add_option('actionRateL',0);
			add_option('Rate_limiting',0);
			add_option('Rate_request',240);
			add_option('limitAttack',10);
			add_option( 'mo_mmp_check_vulnerable_code', 1);
			add_option( 'mo_mmp_check_sql_injection', 1);
			add_option( 'mo_mmp_scan_plugins', true);
			add_option( 'mo_mmp_scan_themes', true);
			include 'controllers/main_controller.php';
		}

		function mo_wpns_activate() 
		{
			
    		if (class_exists('MalwareProtection')) {
			 do_action('mo_mmp_show_message','malware plugin is already activated.','ERROR');
			 exit;    
			}
			
			global $wpnsDbQueries;
			$wpnsDbQueries->mo_plugin_activate();
			update_site_option('mo_mmp_plugin_redirect', true);
			add_option('mo_mmp_scan_initialize',1);
			add_option( 'mo_mmp_last_scan_time', time());
		}

		function mo_wpns_scan_automatic()
		{
			if (! wp_next_scheduled ( 'scan_cron_hook' )) {
		    	wp_schedule_single_event( time() + 21600, 'scan_cron_hook' );
		    }
		}

		function mo_wpns_scheduled_scan()
		{
			if(get_option('mo_mmp_scan_initialize')){
				$nonce= wp_create_nonce('wpns-quick-scan');
				$config_array= array('scan'=>'scan_start', 'scantype'=>'quick_scan', 'nonce'=> $nonce);
				$scan_obj= new Mowaf_scan_malware();
				$scan_obj->mo_wpns_start_malware_scan($config_array);
			}
		}

		function mo_wpns_deactivate() 
		{
			global $MowafUtility;
			if( !$MowafUtility->check_empty_or_null( get_option('mo_wpns_registration_status') ) ) {
				delete_option('mo_wpns_admin_email');
			}

			delete_option('mo_wpns_admin_customer_key');
			delete_option('mo_wpns_admin_api_key');
			delete_option('mo_wpns_customer_token');
			delete_option('mo_wpns_transactionId');
			delete_option('mo_wpns_registration_status');
		}

		function mo_wpns_settings_style($hook)
		{
			if(strpos($hook, 'page_mo_mmp')){
				wp_enqueue_style( 'mo_wpns_admin_settings_style'			, plugins_url('includes/css/style_settings.css', __FILE__),[],MO_WAF_VERSION);
				wp_enqueue_style( 'mo_wpns_admin_settings_phone_style'		, plugins_url('includes/css/phone.css', __FILE__),[],MO_WAF_VERSION);
				wp_enqueue_style( 'mo_wpns_admin_settings_datatable_style'	, plugins_url('includes/css/jquery.dataTables.min.css', __FILE__),[],MO_WAF_VERSION);
				wp_enqueue_style( 'mo_wpns_button_settings_style'			, plugins_url('includes/css/button_styles.css',__FILE__),[],MO_WAF_VERSION);
                wp_enqueue_style( 'mo_wpns_other_plugins'			, plugins_url('includes/css/other_plugins.css',__FILE__),[],MO_WAF_VERSION);

            }

		}

        function my_plugin_add_thickbox() {
            add_thickbox();
        }

		function mo_wpns_settings_script($hook)
		{
			wp_enqueue_script( 'mo_wpns_admin_settings_script'			, plugins_url('includes/js/settings_page.js', __FILE__ ), array('jquery'));
			if(strpos($hook, 'page_mo_mmp')){
				wp_enqueue_script( 'mo_wpns_admin_settings_phone_script'	, plugins_url('includes/js/phone.js', __FILE__ ));
				wp_enqueue_script( 'mo_wpns_admin_datatable_script'			, plugins_url('includes/js/jquery.dataTables.min.js', __FILE__ ), array('jquery'));
			}
		}
		function mo_show_message($content,$type) 
		{
			if($type=="CUSTOM_MESSAGE")
				echo $content;
			if($type=="NOTICE")
				echo '	<div class="is-dismissible notice notice-warning"> <p>'.$content.'</p> </div>';
			if($type=="ERROR")
				echo '	<div class="notice notice-error is-dismissible"> <p>'.$content.'</p> </div>';
			if($type=="SUCCESS")
				echo '	<div class="notice notice-success is-dismissible"> <p>'.$content.'</p> </div>';
		}

		function footer_link()
		{
			echo MowafConstants::FOOTER_LINK;
		}

		function includes()
		{
			require('helper/pluginUtility.php');
			require('database/database_functions.php');
			require('helper/utility.php');
			require('handler/ajax.php');
			require('handler/backup.php');
			require('handler/feedback_form.php');
			require('handler/recaptcha.php');
			require('handler/login.php');
			require('handler/registration.php');
			require('handler/logger.php');
			require('handler/spam.php');
			require('helper/curl.php');
			require('helper/plugins.php');
			require('helper/constants.php');
			require('helper/messages.php');
			require('views/common-elements.php');
			require('helper/dashboard_security_notification.php');
			 
			require('controllers/wpns-loginsecurity-ajax.php');
			require('controllers/malware_scanner/malware_scan_ajax.php');
			require('controllers/backup_ajax.php');
			require('controllers/dashboard_ajax.php');
			require('handler/malware_scanner/malware_scanner_cron.php');
			require('handler/malware_scanner/scanner_set_cron.php');
		}

	}
new MOWAF;
?>