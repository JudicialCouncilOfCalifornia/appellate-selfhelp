<?php

class MowafUtility
{

	public static function icr() 
	{
		$email 			= get_option('mo_wpns_admin_email');
		$customerKey 	= get_option('mo_wpns_admin_customer_key');
		if( ! $email || ! $customerKey || ! is_numeric( trim( $customerKey ) ) )
			return 0;
		else
			return 1;
	}
	
	public static function check_empty_or_null( $value )
	{
		if( ! isset( $value ) || empty( $value ) )
			return true;
		return false;
	}
	
	public static function is_curl_installed()
	{
		if  (in_array  ('curl', get_loaded_extensions()))
			return 1;
		else 
			return 0;
	}
	
	public static function is_extension_installed($name)
	{
		if  (in_array  ($name, get_loaded_extensions()))
			return true;
		else
			return false;
	}
	
	public static function get_client_ip() 
	{
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
		} else {
			return sanitize_text_field($_SERVER['REMOTE_ADDR']);
		}

		return '';
	}

	public static function check_if_valid_email($email)
	{
		$emailarray = explode("@",$email);
		if(sizeof($emailarray)==2)
			return in_array(trim($emailarray[1]), MowafConstants::$domains);
		else
			return false;
	}

	

	public static function check_if_strong_password_enabled_for_user_role($userroles)
	{
		$enforce_strong_pass = get_option('mo_wpns_enforce_strong_passswords_for_accounts');

		switch($enforce_strong_pass)
		{
			case "all":
				return true;
																	break;
			case "admin":
				if(!in_array("administrator", $userroles))
					return false;									
																	break;
			case "user":
				if(in_array("administrator", $userroles))
					return false;
																	break;
		}
		return true;
	}

	public static function get_current_url()
	{
		$protocol  = (!empty($_SERVER['HTTPS']) && sanitize_text_field($_SERVER['HTTPS']) !== 'off' || sanitize_text_field($_SERVER['SERVER_PORT']) == 443) ? "https://" : "http://";
		$url	   = $protocol . sanitize_text_field($_SERVER['HTTP_HOST'])  . sanitize_text_field($_SERVER['REQUEST_URI']);
		return $url;
	}

	//Function to handle recptcha
	function verify_recaptcha($response)
	{
		$error = new WP_Error();
		if(!empty($response))
		{
			if(!Mowaf_reCaptcha::recaptcha_verify($response))
				$error->add('recaptcha_error', __( '<strong>ERROR</strong> : Invalid Captcha. Please verify captcha again.'));
			else
				return true;
		}
		else
			$error->add('recaptcha_error', __( '<strong>ERROR</strong> : Please verify the captcha.'));
		return $error;
	}


	function sendIpBlockedNotification($ipAddress, $reason)
	{
		global $MowafUtility;
		$subject = 'User with IP address '.$ipAddress.' is blocked | '.get_bloginfo();
		$toEmail = get_option('admin_email_address');
        	$content = "";
		if(get_option('custom_admin_template'))
		{
			$content = get_option('custom_admin_template');
			$content = str_replace("##ipaddress##",$ipAddress,$content);
		}
		else
			$content = $this->getMessageContent($reason,$ipAddress);
		if(isset($content))
			return $this->wp_mail_send_notification($toEmail,$subject,$content);
			
	}

	function wp_mail_send_notification($toEmail,$subject,$content){
		$headers = array('Content-Type: text/html; charset=UTF-8');
		wp_mail( $toEmail, $subject, $content, $headers);

	}
	
	
	function sendNotificationToUserForUnusualActivities($username, $ipAddress, $reason)
	{
		$content = "";
		//check if email not already sent
		if(get_option($ipAddress.$reason)){
			return json_encode(array("status"=>'SUCCESS','statusMessage'=>'SUCCESS'));
		}
		
		global $MowafUtility;

		$user = get_user_by( 'login', $username );
		if($user && !empty($user->user_email))
			$toEmail = $user->user_email;
		else
			return;
		
		$mo_wpns_config = new MowafHandler();
		if($mo_wpns_config->is_email_sent_to_user($username,$ipAddress))
			return;
	
		$fromEmail = get_option('mo_wpns_admin_email');
		$subject   = 'Sign in from new location for your user account | '.get_bloginfo();

		if(get_option('custom_user_template'))
		{
			$content = get_option('custom_user_template');
			$content = str_replace("##ipaddress##",$ipAddress,$content);
			$content = str_replace("##username##",$username,$content);
		}
		else
			$content = $this->getMessageContent($reason,$ipAddress,$username,$fromEmail);
		
		return $this->wp_mail_send_notification($toEmail,$subject,$content,$fromEmail);
	}

	//Check if null what will be the message
	function getMessageContent($reason,$ipAddress,$username=null,$fromEmail=null)
	{
		switch($reason)
		{
			case MowafConstants::LOGIN_ATTEMPTS_EXCEEDED:
				$content = "Hello,<br><br>The user with IP Address <b>".$ipAddress."</b> has exceeded allowed failed login attempts on your website <b>".get_bloginfo()."</b> and we have blocked his IP address for further access to website.<br><br>You can login to your WordPress dashaboard to check more details.<br><br>Thanks,<br>miniOrange" ;
				return $content;
			case MowafConstants::IP_RANGE_BLOCKING:
				$content = "Hello,<br><br>The user's IP Address <b>".$ipAddress."</b> was found in IP Range specified by you in Advanced IP Blocking and we have blocked his IP address for further access to your website <b>".get_bloginfo()."</b>.<br><br>You can login to your WordPress dashaboard to check more details.<br><br>Thanks,<br>miniOrange" ;
				return $content;
			case MowafConstants::LOGGED_IN_FROM_NEW_IP:
				$content = "Hello ".$username.",<br><br>Your account was logged in from new IP Address <b>".$ipAddress."</b> on website <b>".get_bloginfo()."</b>. Please <a href='mailto:".$fromEmail."'>contact us</a> if you don't recognise this activity.<br><br>Thanks,<br>".get_bloginfo() ;
				return $content;
			case MowafConstants::FAILED_LOGIN_ATTEMPTS_FROM_NEW_IP:
				$subject = 'Someone trying to access you account | '.get_bloginfo();
				$content =  "Hello ".$username.",<br><br>Someone tried to login to your account from new IP Address <b>".$ipAddress."</b> on website <b>".get_bloginfo()."</b> with failed login attempts. Please <a href='mailto:".$fromEmail."'>contact us</a> if you don't recognise this activity.<br><br>Thanks,<br>".get_bloginfo() ;
				return $content;
			default:
				if(is_null($username))
					$content = "Hello,<br><br>The user with IP Address <b>".$ipAddress."</b> has exceeded allowed trasaction limit on your website <b>".get_bloginfo()."</b> and we have blocked his IP address for further access to website.<br><br>You can login to your WordPress dashaboard to check more details.<br><br>Thanks,<br>miniOrange" ;
				else
					$content   = "Hello ".$username.",<br><br>Your account was logged in from new IP Address <b>".$ipAddress."</b> on website <b>".get_bloginfo()."</b>. Please <a href='mailto:".$fromEmail."'>contact us</a> if you don't recognise this activity.<br><br>Thanks,<br>".get_bloginfo() ;
				return $content;
		}
	}

	function getCurrentBrowser()
	{
		$useragent = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
		if(empty($useragent))
			return false;

		$useragent = strtolower($useragent);
		if(strpos($useragent, 'edge') 		!== false)
			return 'edge';
		else if(strpos($useragent, 'opr') 	!== false)
			return 'opera';
		else if(strpos($useragent, 'chrome') !== false || strpos($useragent, 'CriOS') !== false)
			return 'chrome';
		else if(strpos($useragent, 'firefox') 	!== false)
			return 'firefox';
		else if(strpos($useragent, 'msie') 	  	!== false || strpos($useragent, 'trident') 	!==false)
			return 'ie';
		else if(strpos($useragent, 'safari') 	!== false)
			return 'safari';
	}
	
}