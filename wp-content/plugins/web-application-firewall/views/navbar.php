<?php

	if($shw_feedback)
		do_action('mo_mmp_show_message',MowafMessages::showMessage('FEEDBACK'),'CUSTOM_MESSAGE');
	if(!$safe)
		do_action('mo_mmp_show_message',MowafMessages::showMessage('WHITELIST_SELF'),'CUSTOM_MESSAGE');

	echo'<div class="wrap">
				<div><img  style="float:left;margin-top:5px;" src="'.esc_url($logo_url).'"></div>
				<h1>
					miniOrange Firewall &nbsp;
					<a class="add-new-h2" href="'.esc_url($profile_url).'">Account</a>
					<a class="add-new-h2" href="'.esc_url($help_url).'">Troubleshooting</a>
					<a class="license-button add-new-h2" href="'.esc_url($license_url).'">Upgrade</a>
				</h1></div>';?>
		<br>
		<div class="nav-tab-wrapper">
			<?php
			echo '<a id="mo_2fa_waf" class="nav-tab '.($active_tab == 'mo_mmp_dashboard' ? 'nav-tab-active' : '').'" href="'.esc_url($dashboard_url).'"><span class="dashicons dashicons-dashboard"></span>Dashboard</a>';
	 		echo '<a id="mo_2fa_waf" class="nav-tab '.($active_tab == 'mo_mmp_waf' ? 'nav-tab-active' : '').'" href="'.esc_url($waf).'"><span class="dashicons dashicons-shield"></span>Firewall</a>';
	 		echo '<a id="malware_tab" class="nav-tab '.($active_tab == 'mo_mmp_malwarescan'	?	'nav-tab-active' : '').'" href="'.esc_attr($scan_url) .'"><span class="dashicons dashicons-search"></span>Malware Scan</a>';
	 		if(get_site_option('mo_mmp_switch_loginspam') || true){
	 			echo '<a id="login_spam_tab" class="nav-tab '.($active_tab == 'mo_mmp_login_and_spam'  ? 'nav-tab-active' : '').'" href="'.esc_url($login_and_spam)	.'"><span class="dashicons dashicons-admin-users"></span>Login and Spam</a>';
	 		}
	 		if(get_site_option('mo_mmp_switch_backup') || true){
				echo '<a id="backup_tab" class="nav-tab '.esc_attr($active_tab == 'mo_mmp_backup' 	  	  ? 'nav-tab-active' : '').'" href="'.esc_url($backup).'"><span class="dashicons dashicons-database-export"></span>Encrypted Backup</a>';
			}
			if(get_site_option('mo_mmp_switch_adv_block') || true){
				echo '<a id="adv_block_tab" class="nav-tab '.esc_attr($active_tab == 'mo_mmp_advancedblocking'? 'nav-tab-active' : '').'" href="'.esc_url($advance_block).'"><span class="dashicons dashicons-hidden"></span>Advanced Blocking</a>';
			}
			if(get_site_option('mo_mmp_switch_notif') || true){
				echo '<a id="notif_tab" class="nav-tab '.esc_attr($active_tab == 'mo_mmp_notifications'	  ? 'nav-tab-active' : '').'" href="'.esc_url($notif_url) .'"><span class="dashicons dashicons-bell"></span>Notifications</a>';
			}
			if(get_site_option('mo_mmp_switch_reports') || true){
				echo '<a id="report_tab" class="nav-tab '.esc_attr($active_tab == 'mo_mmp_reports'	  	  ?	'nav-tab-active' : '').'" href="'.esc_url($reports_url)	.'"><span class="dashicons dashicons-media-spreadsheet"></span>Reports</a>';
			} 
			echo '<a class="nav-tab '.esc_attr($active_tab == 'mo_mmp_upgrade'	  	  ?	'nav-tab-active' : '').'" href="'.esc_url($upgrade_url)		.'"><span class="dashicons dashicons-star-filled"></span>Upgrade</a>';
			?>
		</div>