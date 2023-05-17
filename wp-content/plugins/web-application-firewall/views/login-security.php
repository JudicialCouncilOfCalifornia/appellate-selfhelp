<?php
global $mo_MoWpnsUtility,$mmp_dirName;
$setup_dirName = $mmp_dirName.'views'.DIRECTORY_SEPARATOR.'link_tracers.php';
 include $setup_dirName;
 
 ?>
<?php
echo '
		<div id="wpns_message" style= "padding-top:10px;padding-bottom:10px;" hidden></div>
		<div>
		<div class="mo_wpns_setting_layout">';


echo ' 		<h3>Brute Force Protection ( Login Protection )<a href='.$mo_waf_premium_docfile['Brute Force Protection'].' target="_blank"><span class="dashicons dashicons-external mo_wpns_doc_link" title="More information.."></span></a></h3>
			<div class="mo_wpns_subheading">This protects your site from attacks which tries to gain access / login to a site with random usernames and passwords.</div>
			
				<input id="mo_bf_button" type="checkbox" name="enable_brute_force_protection" '.esc_attr($brute_force_enabled).'> Enable Brute force protection
			<br>';

			 
				
echo'			<form id="mo_wpns_enable_brute_force_form" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_brute_force_configuration">
					<table class="mo_wpns_settings_table">
						<tr>
							<td style="width:40%">Allowed login attempts before blocking an IP  : </td>
							<td><input class="mo_wpns_table_textbox" type="number" id="allwed_login_attempts" name="allwed_login_attempts" required placeholder="Enter no of login attempts" value="'.esc_attr($allwed_login_attempts).'" /></td>
							<td></td>
						</tr>
						<tr>
							<td>Time period for which IP should be blocked  : </td>
							<td>
								<select id="time_of_blocking_type" name="time_of_blocking_type" style="width:100%;">
								  <option value="permanent" '.esc_attr($time_of_blocking_type=="permanent" ? "selected" : "").'>Permanently</option>
								  <option value="months" '.esc_attr($time_of_blocking_type=="months" ? "selected" : "").'>Months</option>
								  <option value="days" '.esc_attr($time_of_blocking_type=="days" ? "selected" : "").'>Days</option>
								  <option value="hours" '.esc_attr($time_of_blocking_type=="hours" ? "selected" : "").'>Hours</option>
								</select>
							</td>
							<td><input class="mo_wpns_table_textbox '.esc_attr($time_of_blocking_type=="permanent" ? "hidden" : "").' type="number" id="time_of_blocking_val" name="time_of_blocking_val" value="'.esc_attr($time_of_blocking_val).'" placeholder="How many?" /></td>
						</tr>
						<tr>
							<td>Show remaining login attempts to user : </td>
							<td><input  type="checkbox"  id="rem_attempt" name="show_remaining_attempts" '.esc_attr($remaining_attempts).' ></td>
							<td></td>
						</tr>
						<tr>
							<td></td>
							<td><br>
							<input type="hidden" id="brute_nonce" value ="'. wp_create_nonce("wpns-brute-force").'" />
							<input type="button" style="width:100px;" value="Save" class="button button-primary button-large" id="mo_bf_save_button">
							</td>
							<td></td>
						</tr>
					</table>
				</form>';
			
echo'	</div>';

echo'	

		<div class="mo_wpns_setting_layout">		
			<h3>Google reCAPTCHA <a href='.$mo_waf_premium_docfile['Google reCAPTCHA'].' target="_blank"><span class="dashicons dashicons-external mo_wpns_doc_link" title="More information.."></span></a></h3>
			<div class="mo_wpns_subheading">Google reCAPTCHA protects your website from spam and abuse. reCAPTCHA uses an advanced risk analysis engine and adaptive CAPTCHAs to keep automated software from engaging in abusive activities on your site. It does this while letting your valid users pass through with ease.</div>
			<form id="mo_wpns_activate_recaptcha" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_activate_recaptcha">
				<input id="enable_captcha" type="checkbox" name="mo_wpns_activate_recaptcha" '.esc_url($google_recaptcha).'> Enable Google reCAPTCHA (reCaptcha version v2)
				</br> </br>
				<strong>Google reCaptcha version v3 </strong><strong class="mo_wpns_premium_feature"><a href="admin.php?page=mo_mmp_upgrade"> [Premium Feature] </a></strong>
			</form>';
			
echo'			<p>Before you can use reCAPTCHA, you must need to register your domain/webiste <a href="'.esc_url($captcha_url).'" target="blank">here</a>.</p>
				<p>Enter Site key and Secret key that you get after registration.</p>
				<form id="mo_wpns_recaptcha_settings" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_recaptcha_settings">
					<table class="mo_wpns_settings_table">
						<tr>
							<td style="width:30%">Site key  : </td>
							<td style="width:30%"><input id="captcha_site_key" class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_site_key" required placeholder="site key" value="'.esc_attr($captcha_site_key).'" /></td>
							<td style="width:20%"></td>
						</tr>
						<tr>
							<td>Secret key  : </td>
							<td><input id="captcha_secret_key" class="mo_wpns_table_textbox" type="text" name="mo_wpns_recaptcha_secret_key" required placeholder="secret key" value="'. esc_attr($captcha_secret_key).'" /></td>
						</tr>
						<tr>
							<td style="vertical-align:top;">Enable reCAPTCHA for :</td>
							<td><input id="login_captcha" type="checkbox" name="mo_wpns_activate_recaptcha_for_login" '.esc_attr ($captcha_login).'> Login form
							<input id="reg_captcha" style="margin-left:10px" type="checkbox" name="mo_wpns_activate_recaptcha_for_registration" '.esc_attr ($captcha_reg).' > Registration form</td>
						</tr>
					</table><br/>
					<input type="hidden" id="captcha_nonce" value = "'.wp_create_nonce("wpns-captcha").'">
					<input id="captcha_button" type="button" value="Save Settings" class="button button-primary button-large" />
					<input type="button" value="Test reCAPTCHA Configuration" onclick="testcaptchaConfiguration()" class="button button-primary button-large" />
				</form>';
			

echo'	</div>
		
		<div class="mo_wpns_setting_layout">		
			<h3>Mobile authentication</h3>
			<div class="mo_wpns_subheading">Rather than relying on a password alone, which can be phished or guessed, Two Factor authentication adds a second layer of security to your WordPress accounts. We support <b>QR code</b>, <b>OTP over SMS</b> and <b>Email</b>, <b>Push</b>, <b>Soft token</b> (15+ methods to choose from). </div>
			
				<input type="hidden" id="mobile2fa" value ="'.wp_create_nonce("wpns-mobile-auth").'" />
				<input id="mobile_auth"  type="checkbox" name="mo_wpns_enable_2fa" '.esc_attr($enable_2fa).'> Enable Mobile Authentication
			';
			
			
				if($twofa_status=="ACTIVE")
				{
echo 				'<br><a href="'.esc_url($twofactor_url).'">Click here to configure or change your 2nd Factor Method.</a>';
				} 
				else if($twofa_status=="INSTALLED")
				{
echo 				'<br><span style="color:red">For Mobile Authentication you need to have miniOrange 2 Factor plugin activated.</span><br><a href="'.esc_url($activateUrl).'">Click here to activate 2 Factor Plugin</a>';
				} 
				else 
				{
echo				'<br><span style="color:red">For Mobile Authentication you need to have miniOrange 2 Factor plugin installed.</span><br><a href="'.esc_url($install_link).'">Install 2 Factor Plugin</a>';
				} 
			
				
echo		'<br>
		</div>
		
		
		
		<div class="mo_wpns_setting_layout">	
			<h3>Risk Based Access</h3>';
				
			
			if(!empty($enable_2fa))
			{ 
echo'			<form id="mo_wpns_risk_based_access" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_risk_based_access">
					<input type="checkbox" name="mo_wpns_risk_based_access" '.esc_attr($rba_enabled).' > Enable risk based access<br><br>
					<b>Note:</b> Checking this option will display an option \'Remember this device\' on 2nd factor screen. In the next login from the same device, user will bypass 2nd factor, i.e. user will be logged in through username + password only.
					<br><br>
					<input type="submit" name="submit" style="width:100px;" value="Save" class="button button-primary button-large">
				</form>';
				
				if($twofa_status=="INSTALLED")
				{
echo'				<br><span style="color:red">For Risk Based Access you need to have miniOrange 2 Factor plugin activated.</span><br><a href="'.esc_url($activateUrl).'">Click here to activate 2 Factor Plugin</a>';
				} 
				else if( $twofa_status!="ACTIVE" && $twofa_status!="INSTALLED")
				{
echo'				<br><span style="color:red">For Risk Based Access you need to have miniOrange 2 Factor plugin installed.</span><br><a href="'.esc_attr($install_link).'">Install 2 Factor Plugin</a>';
				} 
			} 
			else 
			{ 
echo'				<form id="mo_wpns_rba_enable_2fa" method="post" action="">
						<input type="hidden" name="option" value="mo_wpns_rba_enable_2fa">
					</form>
					<span style="color:red">Mobile authentication (2 Factor) need to be enabled to use this option. <a style="cursor:pointer;" onclick="document.getElementById(\'mo_wpns_rba_enable_2fa\').submit();">Click here</a> to enable mobile authentication.</span><br>';
			}

echo '<script>

		function testcaptchaConfiguration(){
			var myWindow = window.open("'.esc_attr($test_recaptcha_url).'", "Test Google reCAPTCHA Configuration", "width=600, height=600");	
		}
	</script>';			

			
echo'		<br>
		</div>
	</div>
	
	<script>
		jQuery(document).ready(function(){
			$("#time_of_blocking_type").change(function() {
				if($(this).val()=="permanent")
					$("#time_of_blocking_val").addClass("hidden");
				else
					$("#time_of_blocking_val").removeClass("hidden");	
			});
		});	
	</script>



	<script>
		jQuery(document).ready(function(){
				
			jQuery("#mobile_auth").click(function(){
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery("#wpns_message").show();
				jQuery("#wpns_message").removeClass();
				var data = {
					"action"                 :"wpns_login_security",  
					"wpns_loginsecurity_ajax":"wpns_mobile_auth",
					"mobile_auth_status"     :jQuery("#mobile_auth").is(":checked"),
					"nonce"					 :jQuery("#mobile2fa").val(), 

					 
				}
				jQuery.post(ajaxurl, data, function(response) {
				
					if(data.mobile_auth_status == true){
						jQuery("#wpns_message").addClass("notice notice-success is-dismissible");
					}
					else if(data.mobile_auth_status == false){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
					}
					jQuery("#wpns_message").append(response);
					window.scrollTo({ top: 200, behavior: "smooth" });
				});
			});
			

			jQuery("#mo_bf_save_button").click(function(){
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery("#wpns_message").show();
				jQuery("#wpns_message").removeClass();
				var data =  {
				"action": "wpns_login_security",
				"wpns_loginsecurity_ajax" : "wpns_bruteforce_form", 
				"bf_enabled/disabled"     : jQuery("#mo_bf_button").is(":checked"),
				"allwed_login_attempts"   : jQuery("#allwed_login_attempts").val(),
				"time_of_blocking_type"   : jQuery("#time_of_blocking_type").val(),
				"time_of_blocking_val"    : jQuery("#time_of_blocking_val").val(),
				"show_remaining_attempts" : jQuery("#rem_attempt").is(":checked"),
				"nonce" 				  : jQuery("#brute_nonce").val(),	
			};
				jQuery.post(ajaxurl, data, function(response) {
					if (response == "empty"){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("Please fill out all the fields.");
					}
					else if(response == "true"){
						jQuery("#wpns_message").addClass("notice notice-success is-dismissible");
						jQuery("#wpns_message").append("Brute force is enabled and configuration has been saved.");
						
					}
					else if(response == "false"){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("Brute force is disabled.");

					}
					else if(response == "ERROR" ){ 
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("ERROR."); 
					}
					window.scrollTo({ top: 200, behavior: "smooth" });
				});
			});


			jQuery("#rename_login_config_url").click(function(){
				jQuery("#loginURL").empty();
				jQuery("#loginURL").hide();
				jQuery("#loginURL").show();
				jQuery("#wpns_message").empty();
				jQuery("#wpns_message").hide();
				jQuery("#wpns_message").show();
				jQuery("#wpns_message").removeClass();
				var data = {
					"action"                 :"wpns_login_security",
					"wpns_loginsecurity_ajax":"wpns_rename_loginURL",
					"enable_rename_loginurl" :jQuery("#rename_url_chkbx").is(":checked"),
					"input_url"				 :jQuery("#login_page_url").val(), 
					"nonce"                  :jQuery("#wpns_url").val(), 
				}
				jQuery.post(ajaxurl, data, function(response) {
				
					if (response == "empty"){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("Please fill all the fields.");
					}
					else if(response == "true"){
						jQuery("#wpns_message").addClass("notice notice-success is-dismissible");
						jQuery("#wpns_message").append("Login Page URL has been changed.");
						jQuery("#loginURL").append(data.input_url);
					}
					else if(response == "false"){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("Your custom login page URL is DISABLED.");
						jQuery("#loginURL").append("wp-login.php");
					}
					else if(response == "ERROR" ){ 
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("ERROR.");
					}
				window.scrollTo({ top: 200, behavior: "smooth" });
				});
			});
			jQuery("#captcha_button").click(function(){
					jQuery("#wpns_message").empty();
					jQuery("#wpns_message").hide();
					jQuery("#wpns_message").show();
					jQuery("#wpns_message").removeClass();
					var data = {
					"action"                 :"wpns_login_security",  
					"wpns_loginsecurity_ajax":"wpns_save_captcha",
					"site_key"  			 : jQuery("#captcha_site_key").val(),
					"secret_key"			 : jQuery("#captcha_secret_key").val(), 
					"enable_captcha"		 : jQuery("#enable_captcha").is(":checked"),
					"login_form"			 : jQuery("#login_captcha").is(":checked"),
					"registeration_form"	 : jQuery("#reg_captcha").is(":checked"),
					"nonce"		           	 : jQuery("#captcha_nonce").val(),
				}
				jQuery.post(ajaxurl, data, function(response) {
				
					if (response == "empty"){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("Please fill out all the fields.");
					}
					else if(response == "true"){
						jQuery("#wpns_message").addClass("notice notice-success is-dismissible");
						jQuery("#wpns_message").append("CAPTCHA is enabled.");
					}
					else if(response == "false"){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("CAPTCHA is disabled.");
					}
					else if(response == "ERROR" ){ 
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("ERROR.");
					}
					window.scrollTo({ top: 200, behavior: "smooth" });
				});
			});
			jQuery("#strong_password").click(function(){
					jQuery("#wpns_message").empty();
					jQuery("#wpns_message").hide();
					jQuery("#wpns_message").show();
					jQuery("#wpns_message").removeClass();
					var data = {
					"action"                 :"wpns_login_security",  
					"wpns_loginsecurity_ajax":"save_strong_password",
					"enable_strong_pass"	 :jQuery("#strong_password_check").is(":checked"),
					"accounts_strong_pass"	 :jQuery("#mo_wpns_enforce_strong_passswords_for_accounts").val(),
					"nonce"					 :jQuery("#str_pass").val(), 
				}
				jQuery.post(ajaxurl, data, function(response) {
				
					if(response == "true"){
						jQuery("#wpns_message").addClass("notice notice-success is-dismissible");
						jQuery("#wpns_message").append("Strong password is enabled.");
					}
					else if(response == "false"){
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("Strong password is disabled.");
					}
					else if(response == "ERROR" ){ 
						jQuery("#wpns_message").addClass("notice notice-error is-dismissible");
						jQuery("#wpns_message").append("ERROR.");
					}
					window.scrollTo({ top: 200, behavior: "smooth" });
				});
			});

		});

	</script>
';
	
?>

		