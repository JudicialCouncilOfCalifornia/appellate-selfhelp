<?php 

	class Mowaf_LoginHandler
	{
		function __construct()
		{
			add_action( 'init' , array( $this, 'mo_wpns_init' ) );

			
			if(get_option('mo_wpns_enable_brute_force'))
			{
				add_action('wp_login'				 , array( $this, 'mo_wpns_login_success' 	       )		);
				add_action('wp_login_failed'		 , array( $this, 'mo_wpns_login_failed'	 	       ) 	    );
			}
                        if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration') ){
				add_action( 'woocommerce_register_post', array( $this,'wooc_validate_user_captcha_register'), 1, 3);
			} 
		}	


		function mo_wpns_init()
		{
			global $MowafUtility,$mmp_dirName;
			$WAFEnabled = get_option('WAFEnabled');
			$WAFLevel = get_option('WAF');

			$Mowaf_scanner_parts = new Mowaf_scanner_parts();
			$Mowaf_scanner_parts->file_cron_scan();

			if($WAFEnabled == 1)
			{
				if($WAFLevel == 'PluginLevel')
				{
					if(file_exists($mmp_dirName .'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php'))
						include_once($mmp_dirName .'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf-plugin.php');
					else
					{
						//UNable to find file. Please reconfigure.
					}
				}
			}
			
				$userIp 			= $MowafUtility->get_client_ip();
				$mo_wpns_config = new MowafHandler();
				$isWhitelisted   = $mo_wpns_config->is_whitelisted($userIp);
				$isIpBlocked = false;
				if(!$isWhitelisted){
				$isIpBlocked = $mo_wpns_config->is_ip_blocked_in_anyway($userIp);
				}
				 if($isIpBlocked)
				 	include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'403.php';


				$requested_uri = sanitize_text_field($_SERVER["REQUEST_URI"]);
				$option = false;
				if (is_user_logged_in()) { //chr?
					if (strpos($requested_uri, chr(get_option('login_page_url'))) != false) {
						wp_redirect(site_url());
						die;
					}
				} else {
					$option = get_option('mo_wpns_enable_rename_login_url');
				}
				if ($option) {
                    if (strpos($requested_uri, '/wp-login.php?checkemail=confirm') !== false) {
                        $requested_uri = str_replace("wp-login.php","",$requested_uri);
                        wp_redirect($requested_uri);
                        die;
                    } else if (strpos($requested_uri, '/wp-login.php?checkemail=registered') !== false) {
                        $requested_uri = str_replace("wp-login.php","",$requested_uri);
                        wp_redirect($requested_uri);
                        die;
                    }

                    if (strpos($requested_uri, '/wp-login.php') !== false) {
						wp_redirect(site_url());
					}
					else if (strpos($requested_uri, get_option('login_page_url')) !== false ) {
						@require_once ABSPATH . 'wp-login.php';
						die;
					}
				}

				if(isset($_POST['option']))
				{
						switch(sanitize_text_field($_POST['option']))
						{
							case "mo_wpns_change_password":
								$this->handle_change_password(sanitize_text_field($_POST['username'])
									,$_POST['new_password'],$_POST['confirm_password']);		break;
						}
				}

		}

		function wooc_validate_user_captcha_register($username, $email, $validation_errors) {

			if (empty($_POST['g-recaptcha-response'])) {
				$validation_errors->add( 'woocommerce_recaptcha_error', __('Please verify the captcha', 'woocommerce' ) );
			}
		}

		//Function to Handle Change Password Form
		function handle_change_password($username,$newpassword,$confirmpassword)
		{
			global $mmp_dirName;
			$user  = get_user_by("login",$username);
			$error = wp_authenticate_username_password($user,$username,$newpassword);

			if(is_wp_error($error))
			{
				$this->mo_wpns_login_failed($username);
				return $error;
			}

			if($this->update_strong_password($username,$newpassword,$confirmpassword)=="success")
			{
				wp_set_auth_cookie($user->ID,false,false);
				$this->mo_wpns_login_success($username);
				wp_redirect(get_option('siteurl'),301);
			}
		}


		//Function to Update User password
		function update_strong_password($username,$newpassword,$confirmpassword)
		{
			global $mmp_dirName;

			if(strlen($newpassword) > 5 && preg_match("#[0-9]+#", $newpassword) && preg_match("#[a-zA-Z]+#", $newpassword)
				&& preg_match('/[^a-zA-Z\d]/', $newpassword) && $newpassword==$confirmpassword)
			{
				$user = get_user_by("login",$username);
				wp_set_password($_POST['new_password'],$user->ID);
				return "success";
			}
			else
				include $mmp_dirName . 'controllers'.DIRECTORY_SEPARATOR.'change-password.php';
		}


		


		//Function to handle successful user login
		function mo_wpns_login_success($username)
		{
			global $MowafUtility;

				$mo_wpns_config = new MowafHandler();
				$userIp 		= $MowafUtility->get_client_ip();

				$mo_wpns_config->move_failed_transactions_to_past_failed($userIp);

				if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
					$MowafUtility->sendNotificationToUserForUnusualActivities($username, $userIp, MowafConstants::LOGGED_IN_FROM_NEW_IP);


				$mo_wpns_config->add_transactions($userIp, $username, MowafConstants::LOGIN_TRANSACTION, MowafConstants::SUCCESS);
		}


		//Function to handle failed user login attempt
		function mo_wpns_login_failed($username)
		{
			global $MowafUtility;
				$userIp 		= $MowafUtility->get_client_ip();

				if(empty($userIp) || empty($username) || !get_option('mo_wpns_enable_brute_force'))
					return;

				$mo_wpns_config = new MowafHandler();
				$isWhitelisted  = $mo_wpns_config->is_whitelisted($userIp);

				$mo_wpns_config->add_transactions($userIp, $username, MowafConstants::LOGIN_TRANSACTION, MowafConstants::FAILED);



					if(get_option('mo_wpns_enable_unusual_activity_email_to_user'))
							$MowafUtility->sendNotificationToUserForUnusualActivities($username, $userIp, MowafConstants::FAILED_LOGIN_ATTEMPTS_FROM_NEW_IP);

					$failedAttempts 	 = $mo_wpns_config->get_failed_attempts_count($userIp);
					$allowedLoginAttepts = get_option('mo_wpns_allwed_login_attempts') ? get_option('mo_wpns_allwed_login_attempts') : 5;

					if($allowedLoginAttepts - $failedAttempts<=0)
						$this->handle_login_attempt_exceeded($userIp);
					else if(get_option('mo_wpns_show_remaining_attempts'))
						$this->show_limit_login_left($allowedLoginAttepts,$failedAttempts);
		}


		


		//Function to show number of attempts remaining
		function show_limit_login_left($allowedLoginAttepts,$failedAttempts)
		{
			global $error;
			$diff = $allowedLoginAttepts - $failedAttempts;
			$error = "<br>You have <b>".esc_attr($diff)."</b> login attempts remaining.";
		}


		//Function to handle login limit exceeded
		function handle_login_attempt_exceeded($userIp)
		{
			global $MowafUtility, $mmp_dirName;
			$mo_wpns_config = new MowafHandler();
			$mo_wpns_config->block_ip($userIp, MowafConstants::LOGIN_ATTEMPTS_EXCEEDED, false);
			include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'error'.DIRECTORY_SEPARATOR.'403.php';
		}

	}
	new Mowaf_LoginHandler;
