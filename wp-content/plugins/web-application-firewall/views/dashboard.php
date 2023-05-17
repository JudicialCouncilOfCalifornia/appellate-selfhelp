<?php
global $MowafUtility,$mmp_dirName;
include_once $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'navbar.php';
add_action('admin_footer','mo_mmp_dashboard_switch');
$toggle = get_site_option("mo_mmp_toggle");
$two_fa_on= get_site_option("mo_mmp_switch_2fa")?"checked":"";
$all_on= get_site_option("mo_mmp_switch_all")?"checked":"";
$waf_on= get_site_option("mo_mmp_switch_waf")?"checked":"";
$login_spam_on= get_site_option("mo_mmp_switch_loginspam")?"checked":"";
$backup_on= get_site_option("mo_mmp_switch_backup")?"checked":"";
$malware_on= get_site_option("mo_mmp_switch_malware")?"checked":"";
$adv_block_on= get_site_option("mo_mmp_switch_adv_block")?"checked":"";
$report_on= get_site_option("mo_mmp_switch_reports")?"checked":"";
$notif_on= get_site_option("mo_mmp_switch_notif")?"checked":"";
echo '<div id="mo_switch_message" style=" padding:8px"></div>';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<div class="mo_wpns_divided_layout">
		
		<div class="mo_wpns_dashboard_layout">
				<div class ="mo_wpns_inside_dashboard_layout">Infected Files<p class =" mo_wpns_dashboard_text" >'.esc_attr($total_malicious).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout ">Failed Login<p class =" mo_wpns_dashboard_text" >'.esc_attr($wpns_attacks_blocked).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">Attacks Blocked <p class =" mo_wpns_dashboard_text">'.esc_attr($totalAttacks).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">Blocked IPs<p class =" mo_wpns_dashboard_text">'.esc_attr($wpns_count_ips_blocked).'</p></div>
				<div class ="mo_wpns_inside_dashboard_layout">White-listed IPs<p class =" mo_wpns_dashboard_text">'.esc_attr($wpns_count_ips_whitelisted).'</p></div>		
		</div>
		<div class="mo_wpns_small_layout_container">
			<div class="mo_wpns_small_layout">
				<form name="mmp_tab_malware" id="mmp_tab_malware" method="post">
				<h3><span class="dashicons dashicons-search"></span>  Malware Scan';
				if($toggle){
			echo ' <label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_malware_switch"/>
				 <input type=checkbox id="switch_malware" name="switch_val" value="1" ' .esc_attr($malware_on). ' />
				 <span class="mo_wpns_slider mo_wpns_round"></span>
				</label>';
				}
				else{
					echo ' <b style="color:green;">(Enabled)</b>';
				}
			echo ' </h3>
				</form>
				 A malware scanner / detector or virus scanner is a <b>software that detects the malware</b> into the system. It detects different kinds of malware and categories based on the <b>strength of vulnerability or harmfulness.</b> <br>
			</div>
			
			<div class="mo_wpns_small_layout">
				<form name="mmp_tab_waf" id="mmp_tab_waf" method="post">
				<h3><span class="dashicons dashicons-shield"></span> Web Application Firewall (WAF)
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_waf_switch"/>
				</label>
				</h3>
				</form>
				Web Application Firewall protects your website from several website attacks such as <b>SQL Injection(SQLI), Cross Site Scripting(XSS), Remote File Inclusion</b> and many more cyber attacks.It also protects your website from <b>critical attacks</b> such as <b>Dos and DDos attacks.</b><br>
			</div>

			<div class="mo_wpns_small_layout">
				<form name="mmp_tab_login" id="mmp_tab_login" method="post">
				<h3><span class="dashicons dashicons-admin-users"></span> Login and Spam
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_login_switch"/>
				</label>
				</h3>
				</form>
				Firewall protects the whole website.
				If you just want to prevent your login page from <b> password guessing attacks</b> by humans or by bots.
				 We have features such as <b> Brute Force,Enforcing Strong Password,Custom Login Page URL,Recaptcha </b> etc. <br>
			</div>

			<div class="mo_wpns_small_layout">
				<form name="mmp_tab_backup" id="mmp_tab_backup" method="post">
				<h3><span class="dashicons dashicons-database-export"></span> Encrypted Backup
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_backup_switch"/>
				</label>
				</h3>
				</form>
				Creating regular backups for your website is essential. By Creating backup you can <b>restore your website back to normal</b> within a few minutes. miniOrange creates <b>database and file Backup</b> which is stored locally in your system.
			</div>
			
			<div class="mo_wpns_small_layout">
				<form name="mmp_tab_adv_block" id="mmp_tab_adv_block" method="post">
				<h3><span class="dashicons dashicons-hidden"></span> Advanced Blocking
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_block_switch"/>
				</label>
				</h3>
				</form>
				In Advanced blocking we have features like <b> Country Blocking, IP range Blocking , Browser blocking </b> and other options you can set up specifically according to your needs 
			</div>

		    <div class="mo_wpns_small_layout">
		    	<form name="mmp_tab_report" id="mmp_tab_report" method="post">
				<h3><span class="dashicons dashicons-media-spreadsheet"></span> Reports
				<label class="mo_wpns_switch" style="float: right">
				<input type="hidden" name="option" value="tab_report_switch"/>
				</label>
				</h3>
				</form>
                Track users <b>login activity</b> on your website. You can also <b>track 404 error</b> so that if anyone tries to access it too many times you can take action.
			</div>

			</div>
	</div>	';

function mo_mmp_dashboard_switch(){
	if ( ('admin.php' != basename(  $_SERVER['PHP_SELF'] )) || (sanitize_text_field($_GET['page'] )!= 'mo_mmp_dashboard') ) {
        return;
    }
?>
	
<?php
}
?>