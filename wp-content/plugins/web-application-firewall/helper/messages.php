<?php
	
	class MowafMessages
	{
		// ip-blocking messages
		const INVALID_IP						= "Please enter a valid IP address.";
		const IP_ALREADY_BLOCKED				= "IP Address is already Blocked";
		const IP_PERMANENTLY_BLOCKED			= "IP Address is blocked permanently.";
		const IP_ALREADY_WHITELISTED			= "IP Address is already Whitelisted.";
		const IP_IN_WHITELISTED					= "IP Address is Whitelisted. Please remove it from the whitelisted list.";
		const IP_UNBLOCKED						= "IP has been unblocked successfully";
		const IP_WHITELISTED					= "IP has been whitelisted successfully";
		const IP_UNWHITELISTED					= "IP has been removed from the whitelisted list successfully";

		//login-security messages
		const BRUTE_FORCE_ENABLED				= "Brute force protection is enabled.";
		const BRUTE_FORCE_DISABLED				= "Brute force protection is disabled.";
		const DOS_ENABLED						= "DOS protection enabled.";
		const DOS_DISABLED						= "DOS protection disabled.";
		const TWOFA_ENABLED						= "Two Factor protection has been enabled.";
		const TWOFA_DISABLED					= "Two Factor protection has been disabled.";
		const RBA_ENABLED						= "Mobile Authentication and Risk based access is Enabled.";						
		const RBA_DISABLED						= "Risk based access is Disabled.";
		const RECAPTCHA_ENABLED					= "Google reCAPTCHA configuration is enabled.";
		const RECAPTCHA_DISABLED				= "Google reCAPTCHA configuration is disabled.";
		const STRONG_PASS_ENABLED				= "Strong Password has been enabled for your users.";
		const STRONG_PASS_DISABLED				= "Strong Password has been disabled for your users.";		

		//notification messages
		const NOTIFY_ON_IP_BLOCKED				= "Email notification is enabled for Admin.";
		const DONOT_NOTIFY_ON_IP_BLOCKED		= "Email notification is disabled for Admin.";
		const NOTIFY_ON_UNUSUAL_ACTIVITY		= "Email notification is enabled for user for unusual activities.";
		const DONOT_NOTIFY_ON_UNUSUAL_ACTIVITY  = "Email notification is disabled for user for unusual activities.";

		//registration security
		const DOMAIN_BLOCKING_ENABLED			= "Blocking fake user registrations is Enabled.";
		const DOMAIN_BLOCKING_DISABLED			= "Blocking fake user registration is disabled";
		const ENFORCE_STRONG_PASSWORD			= "Strong password enforcement is Enabled.";
		const ENFORCE_STRONG_PASS_DISABLED		= "Strong password enforcement is Disabled.";
		const ENABLE_ADVANCED_USER_VERIFY		= "Advanced user verification is Enabled.";
		const DISABLE_ADVANCED_USER_VERIFY		= "Advanced user verification is Disable.";
		const ENABLE_SOCIAL_LOGIN				= "Social Login Integration is Enabled.";
		const DISABLE_SOCIAL_LOGIN				= "Social Login Integration is Disabled.";

		//Advanced security
		const HTACCESS_ENABLED					= "htaccess security has been enabled";
		const HTACCESS_DISABLED					= "htaccess security has been disabled";
		const USER_AGENT_BLOCK_ENABLED			= "User Agent has block been enabled";
		const USER_AGENT_BLOCK_DISABLED			= "User Agent has block been disabled";
		const INVALID_IP_FORMAT 				= "Please enter Valid IP Range.";
		//content protection
		const CONTENT_PROTECTION_ENABLED		= "Your configuration for Content Protection has been saved.";
		const CONTENT_SPAM_BLOCKING				= "Protection for Comment SPAM has been enabled.";
		const CONTENT_RECAPTCHA					= "reCAPTCHA has been enabled for Comments.";
		const CONTENT_SPAM_BLOCKING_DISABLED	= "Protection for Comment SPAM has been disabled.";
		const CONTENT_RECAPTCHA_DISABLED		= "reCAPTCHA has been disabled for Comments.";

		//support form 
		const SUPPORT_FORM_VALUES				= "Please submit your query along with email.";
		const SUPPORT_FORM_SENT					= "Thanks for getting in touch! We shall get back to you shortly.";
		const SUPPORT_FORM_ERROR				= "Your query could not be submitted. Please try again.";
        //feedback Form
		const DEACTIVATE_PLUGIN                 		= "Plugin deactivated successfully";

		//common messages
		const UNKNOWN_ERROR						= "Error processing your request. Please try again.";
		const CONFIG_SAVED						= "Configuration saved successfully.";
		const REQUIRED_FIELDS					= "Please enter all the required fields";
		const RESET_PASS						= "You password has been reset successfully and sent to your registered email. Please check your mailbox.";
		const TEMPLATE_SAVED					= "Email template saved.";
		const FEEDBACK							= "<div class='custom-notice notice notice-warning feedback-notice'><p><p class='notice-message'>Looking for a feature? Help us make the plugin better. Send us your feedback using the Support Form below.</p><button class='feedback notice-button'><i>Dismiss</i></button></p></div>";
		const WHITELIST_SELF					= "<div class='custom-notice notice notice-warning whitelistself-notice'><p><p class='notice-message'>It looks like you have not whitelisted your IP. Whitelist your IP as you can get blocked from your site.</p><button class='whitelist_self notice-button'><i>WhiteList</i></button></p></div>";
		const SUCCESS_ACCOUNT_LOGOUT			= "You are Logged out.";
		const ERR_ACCOUNT_LOGOUT				= "Error while removing your account";


		//registration messages
		const PASS_LENGTH						= "Choose a password with minimum length 6.";
		const ERR_OTP_EMAIL						= "There was an error in sending email. Please click on Resend OTP to try again.";
		const OTP_SENT							= 'A passcode is sent to {{method}}. Please enter the otp below.';
		const REG_SUCCESS						= 'Your account has been retrieved successfully.';
		const ACCOUNT_EXISTS					= 'You already have an account with miniOrange. Please enter a valid password.';
		const INVALID_CRED						= 'Invalid username or password. Please try again.';
		const REQUIRED_OTP 						= 'Please enter a value in OTP field.';
		const INVALID_OTP 						= 'Invalid one time passcode. Please enter a valid passcode.';
		const INVALID_PHONE						= 'Please enter the phone number in the following format: <b>+##country code## ##phone number##';
		const PASS_MISMATCH						= 'Password and Confirm Password do not match.';
                const CRON_DB_BACKUP_ENABLE			    = 'Scheduled Database Backup enabled';
		const CRON_DB_BACKUP_DISABLE			= 'Scheduled Database Backup disabled';
		const CRON_FILE_BACKUP_ENABLE			= 'Scheduled File Backup enabled';
		const CRON_FILE_BACKUP_DISABLE			= 'Scheduled File Backup disabled';	
		const BACKUP_CREATED					= 'Backup created successfully';
		const WARNING  							= 'Please select folder for backup';
        const INVALID_EMAIL  					= 'Please enter valid Email ID';
        const EMAIL_SAVED 						= 'Email ID saved successfully';
        const INVALID_HOURS 					= 'For scheduled backup, please enter number of hours greater than 1.';
        const ALL_ENABLED						= "All Website security features are available.";
        const ALL_DISABLED						= 'All Website security features are disabled.';
        const TWO_FACTOR_ENABLE					= 'Two-factor is enabled. Configure it in the Two-Factor tab.';
        const TWO_FACTOR_DISABLE				= 'Two-factor is disabled.';
        const WAF_ENABLE						= 'WAF features are now available. Configure it in the Firewall tab.';
        const WAF_DISABLE						= 'WAF is disabled.';
        const LOGIN_ENABLE						= 'Login security and spam protection features are available. Configure it in the Login and Spam tab.';
        const LOGIN_DISABLE						= 'Login security and spam protection features are disabled.';
        const BACKUP_ENABLE 					= 'Encrypted backup features are available. Configure it in the Encrypted Backup tab.';
        const BACKUP_DISABLE 					= 'Encrypted Backup features are disabled.';
        const MALWARE_ENABLE					= 'Malware scan features and modes are available. Configure it in the Malware Scan tab.';
        const MALWARE_DISABLE 					= 'Malware scan features are disabled.';
        const ADV_BLOCK_ENABLE					= 'Advanced blocking features are available. Configure it in the Advanced blocking tab.';
        const ADV_BLOCK_DISABLE					= 'Advanced blocking features are disabled.';
        const REPORT_ENABLE						= 'Login and error reports are available in the Reports tab.';
        const REPORT_DISABLE					= 'Login and error reports are disabled.';
        const NOTIF_ENABLE						= 'Notification options are available. Configure it in the Notification tab.';
        const NOTIF_DISABLE						= 'Notifications are disabled.';
        const NEW_PLUGIN_THEME_CHECK			= "<div class='custom-notice notice notice-warning new_plugin_theme-notice'><p><p class='notice-message'>We detected a change in plugins/themes folder. Kindly scan for better security.</p><a class='notice-button' href='admin.php?page=mo_mmp_malwarescan' style='margin-right: 15px;'>SCAN</a><button class='new_plugin_dismiss notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='new_plugin_dismiss_always notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";
        const INFECTED_FILE						= "<div class='custom-notice notice notice-warning file_infected-notice'><p><p class='notice-message'>Your last scan found infections/warnings on your website. Kindly fix them to avoid any threats.</p><a class='notice-button' href='admin.php?page=mo_mmp_malwarescan' style='margin-right: 15px;'>SCAN</a><button class='infected_file_dismiss notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='infected_file_dismiss_always notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";
        const WEEKLY_SCAN_CHECK           	 	= "<div class='custom-notice notice notice-warning weekly_notice-notice'><p><p class='notice-message'>You last scanned your website using miniOrange plugin a week ago. Scan now to imrove security.</p><a class='notice-button' href='admin.php?page=mo_mmp_malwarescan' style='margin-right: 15px;'>SCAN</a><button class='weekly_dismiss notice-button' style='margin-right: 15px;'><i>DISMISS</i></button><button class='weekly_dismiss_always notice-button'><i>NEVER SHOW AGAIN</i></button></p></div>";



		public static function showMessage($message , $data=array())
		{
			$message = constant( "self::".$message );
		    foreach($data as $key => $value)
		    {
		        $message = str_replace("{{" . $key . "}}", $value , $message);
		    }
		    return $message;
		}

	}

?>