<?php
class Mowaf_ajax_dashboard
{
	function __construct(){
		add_action( 'admin_init'  , array( $this, 'mo_mmp_switch_functions' ) );
	}

	public function mo_mmp_switch_functions(){
		if(isset($_POST) && isset($_POST['option'])){
			$tab_count= get_site_option('mo_mmp_tab_count', 0);
			if($tab_count == 6)
				update_site_option('mo_mmp_switch_all', 1);
			else if($tab_count == 0)
				update_site_option('mo_mmp_switch_all', 0);
			switch(sanitize_text_field($_POST['option']))
			{
				case "tab_all_switch":
					$this->mo2f_handle_all_enable(isset($_POST['switch_val']));
					break;
				case "tab_waf_switch":
					$this->mo2f_handle_waf_enable(isset($_POST['switch_val']));
					break;
				case "tab_login_switch":
					$this->mo2f_handle_login_enable(isset($_POST['switch_val']));
					break;
				case "tab_backup_switch":
					$this->mo2f_handle_backup_enable(isset($_POST['switch_val']));
					break;
				case "tab_malware_switch":
					$this->mo2f_handle_malware_enable(isset($_POST['switch_val']));
					break;
				case "tab_block_switch":
					$this->mo2f_handle_block_enable(isset($_POST['switch_val']));
					break;
				case "tab_report_switch":
					$this->mo2f_handle_report_enable(isset($_POST['switch_val']));
					break;
				case "tab_notif_switch":
					$this->mo2f_handle_notif_enable(isset($_POST['switch_val']));
					break;
			}
		}
	}

	public function mo2f_handle_all_enable($POSTED){
		$this->mo2f_handle_waf_enable($POSTED);
		$this->mo2f_handle_login_enable($POSTED);
		$this->mo2f_handle_backup_enable($POSTED);
		$this->mo2f_handle_malware_enable($POSTED);
		$this->mo2f_handle_block_enable($POSTED);
		$this->mo2f_handle_report_enable($POSTED);
		$this->mo2f_handle_notif_enable($POSTED);
		if($POSTED){
			update_site_option('mo_mmp_switch_all',1);
			update_site_option('mo_mmp_tab_count', 6);
			do_action('mo_mmp_show_message',MowafMessages::showMessage('ALL_ENABLED'),'SUCCESS');
		}
		else{
			update_site_option('mo_mmp_switch_all', 0);
			update_site_option('mo_mmp_tab_count', 0);
			do_action('mo_mmp_show_message',MowafMessages::showMessage('ALL_DISABLED'),'ERROR');
		}
	}

	public function mo2f_handle_waf_enable($POSTED){
		if($POSTED){
			update_site_option('mo_mmp_switch_waf', 1);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
			if(sanitize_text_field($_POST['option']) == 'tab_waf_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('WAF_ENABLE'),'SUCCESS');
		}
		else{
			update_site_option('mo_mmp_switch_waf', 0);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')-1);
			update_site_option('WAFEnabled', 0);
			update_site_option('WAF','wafDisable');
			update_site_option('Rate_limiting', 0);
			$dir_name 	=  dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			$dir_name1  =  $dir_name.DIRECTORY_SEPARATOR.'.htaccess';
			$filePath 	= $dir_name.DIRECTORY_SEPARATOR.'mo-check.php';
			$filePath 	= str_replace('\\', '/', $filePath);
		 	$file 		=  file_get_contents($dir_name1);
		 	$cont 	 = PHP_EOL.'# BEGIN miniOrange WAF'.PHP_EOL;
		 	$cont 	.= 'php_value auto_prepend_file '.$filePath.PHP_EOL;
		 	$cont 	.= '# END miniOrange WAF'.PHP_EOL;
		 	$file =str_replace($cont,'',$file);
			file_put_contents($dir_name1, $file);
			if($_POST['option'] == 'tab_waf_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('WAF_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_login_enable($POSTED){
		if($POSTED){
			update_site_option('mo_mmp_switch_loginspam', 1);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
			if($_POST['option'] == 'tab_login_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('LOGIN_ENABLE'),'SUCCESS');
		}
		else{
			update_site_option('mo_mmp_switch_loginspam', 0);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')-1);
			update_site_option('mo_wpns_enable_brute_force', 0);
			update_site_option('mo_wpns_activate_recaptcha', false);
			update_site_option('mo_wpns_activate_recaptcha_for_login', false);
			update_site_option('mo_wpns_activate_recaptcha_for_woocommerce_login', false);
			update_site_option('mo_wpns_activate_recaptcha_for_registration', false);
			update_site_option('mo_wpns_activate_recaptcha_for_woocommerce_registration', false);
			
			update_site_option('mo_wpns_enable_fake_domain_blocking', false);
			update_site_option('mo_wpns_enable_advanced_user_verification', false);
			update_site_option('mo_wpns_enable_social_integration', false);
			update_site_option('protect_wp_config', 0);
			update_site_option('prevent_directory_browsing', 0);
			update_site_option('disable_file_editing', 0);
			update_site_option('mo_wpns_enable_comment_spam_blocking', false);
			update_site_option('mo_wpns_enable_comment_recaptcha', false);
			if($_POST['option'] == 'tab_login_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('LOGIN_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_backup_enable($POSTED){
		if($POSTED){
			update_site_option('mo_mmp_switch_backup', 1);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
			if($_POST['option'] == 'tab_backup_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('BACKUP_ENABLE'),'SUCCESS');
		}
		else{
			update_site_option('mo_mmp_switch_backup', 0);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')-1);
			$handler_obj = new Mowaf_site_backup;
        	$handler_obj->bl_deactivate();
        	update_site_option('mo2f_enable_cron_backup', 0);
        	$handler_obj->file_backup_deactivate();
        	update_site_option('mo2f_enable_cron_file_backup', 0);
        	if($_POST['option'] == 'tab_backup_switch')
        		do_action('mo_mmp_show_message',MowafMessages::showMessage('BACKUP_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_malware_enable($POSTED){
		if($POSTED){
			update_site_option('mo_mmp_switch_malware', 1);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
			if($_POST['option'] == 'tab_malware_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('MALWARE_ENABLE'),'SUCCESS');
		}else{
			update_site_option('mo_mmp_switch_malware', 0);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')-1);
			if($_POST['option'] == 'tab_malware_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('MALWARE_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_block_enable($POSTED){
		if($POSTED){
			update_site_option('mo_mmp_switch_adv_block', 1);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
			if($_POST['option'] == 'tab_block_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('ADV_BLOCK_ENABLE'),'SUCCESS');
		}
		else{
			update_site_option('mo_mmp_switch_adv_block', 0);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')-1);
			update_site_option('mo_wpns_iprange_count', 0);
			update_site_option('mo_wpns_enable_htaccess_blocking', 0);
			update_site_option('mo_wpns_enable_user_agent_blocking', 0);
			update_site_option('mo_wpns_referrers', false);
			update_site_option('mo_wpns_countrycodes', false);
			if($_POST['option'] == 'tab_block_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('ADV_BLOCK_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_report_enable($POSTED){
		if($POSTED){
			update_site_option('mo_mmp_switch_reports', 1);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
			if($_POST['option'] == 'tab_report_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('REPORT_ENABLE'),'SUCCESS');
		}
		else{
			update_site_option('mo_mmp_switch_reports', 0);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')-1);
			if($_POST['option'] == 'tab_report_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('REPORT_DISABLE'),'ERROR');
		}
	}

	public function mo2f_handle_notif_enable($POSTED){
		if($POSTED){
			update_site_option('mo_mmp_switch_notif', 1);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')+1);
			if($_POST['option'] == 'tab_notif_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('NOTIF_ENABLE'),'SUCCESS');
		}
		else{
			update_site_option('mo_mmp_switch_notif', 0);
			update_site_option('mo_mmp_tab_count', get_site_option('mo_mmp_tab_count')-1);
			update_site_option('mo_wpns_enable_ip_blocked_email_to_admin', false);
			update_site_option('mo_wpns_enable_unusual_activity_email_to_user', false);
			if($_POST['option'] == 'tab_notif_switch')
				do_action('mo_mmp_show_message',MowafMessages::showMessage('NOTIF_DISABLE'),'ERROR');
		}
	}

}
new Mowaf_ajax_dashboard();
?>