<?php

	class Mowaf_reCaptcha
	{
		function __construct()
		{
			add_filter( 'admin_init' 	, array($this, 'handle_recaptcha'     ), 11, 3 	);
			add_action( 'login_form' 	, array($this, 'custom_login_fields'  )			);
			add_action( 'register_form' , array($this, 'register_with_captcha')			);
			add_action( 'woocommerce_register_form' , array($this, 'woocommerce_register_with_captcha'));
			add_action( 'woocommerce_login_form', array($this, 'woocommerce_login_with_captcha'));
			add_action( 'woocommerce_review_order_before_submit', array($this, 'woocommerce_register_with_captcha_checkout'));
		}


		//Function to handle Testing reCaptcha
		function handle_recaptcha()
		{
			global $MowafUtility,$mmp_dirName;
			if (current_user_can( 'manage_options' ))
			{ 
				$current_url = home_url($_SERVER['REQUEST_URI']);

				if(isset($current_url) && strpos($current_url, 'testrecaptchaconfig') !== false)
				{	
						if(array_key_exists('g-recaptcha-response',$_POST))
						{
							$userIp 	= $MowafUtility->get_client_ip();
							$Mowaf_MocURL 	= new Mowaf_MocURL;
							$response 	= $Mowaf_MocURL->validate_recaptcha($userIp,sanitize_text_field($_POST['g-recaptcha-response']));
							$content	= json_decode($response, true);
							if(isset($content['error-codes']) && in_array("invalid-input-secret", $content['error-codes']))
								echo "<br><br><h2 style=color:red;text-align:center>Invalid Secret Key.</h2>";
							else if(isset($content['success']) && $content['success']==1)
								echo "<br><br><h2 style=color:green;text-align:center>Test was successful and captcha verified.</h2>";
							else
								echo "<br><br><h2 style=color:red;text-align:center>Invalid captcha. Please try again.</h2>";
						}
						Mowaf_show_google_recaptcha_form();
				}
			}
		}


		function custom_login_fields()
		{
			if(get_option('mo_wpns_activate_recaptcha_for_login'))
			{
				
				echo "<script src='".MowafConstants::RECAPTCHA_URL."'></script>";
				echo '<div class="g-recaptcha" data-sitekey="'.get_option("mo_wpns_recaptcha_site_key").'"></div>';
				echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#loginform{padding-bottom:20px;}</style>';
			}
		}
		
		function register_with_captcha(){
			if(get_option('mo_wpns_activate_recaptcha_for_registration'))
			{
				echo "<script src='".MowafConstants::RECAPTCHA_URL."'></script>";
				echo '<div class="g-recaptcha" data-sitekey="'.get_option("mo_wpns_recaptcha_site_key").'"></div>';
				echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
			}
		}

                 function woocommerce_register_with_captcha(){
			if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration'))
			{
				echo "<script src='".MowafConstants::RECAPTCHA_URL."'></script>";
				echo '<div class="g-recaptcha" data-sitekey="'.get_option("mo_wpns_recaptcha_site_key").'"></div>';
				echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
			}
		}
		
		function woocommerce_login_with_captcha(){
			if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_login'))
			{
				
				echo "<script src='".MowafConstants::RECAPTCHA_URL."'></script>";
				     
				echo '<div class="g-recaptcha" data-sitekey="'.esc_attr (get_option("mo_wpns_recaptcha_site_key")).'"></div>';
				echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#loginform{padding-bottom:20px;}</style>';
			}
		}
	
		function woocommerce_register_with_captcha_checkout(){
			
			if (!is_user_logged_in()){
				if(get_option('mo_wpns_activate_recaptcha_for_woocommerce_registration'))
				{
					echo "<script src='".MowafConstants::RECAPTCHA_URL."'></script>";
					echo '<div class="g-recaptcha" data-sitekey="'.get_option("mo_wpns_recaptcha_site_key").'"></div>';
					echo '<style>#login{ width:349px;padding:2% 0 0; }.g-recaptcha{margin-bottom:5%;}#registerform{padding-bottom:20px;}</style>';
				}
			}
		}
		
		public static function recaptcha_verify($response)
		{
			global $MowafUtility;
			$userIp 	= $MowafUtility->get_client_ip();
			$Mowaf_MocURL 	= new Mowaf_MocURL;
			$response 	= $Mowaf_MocURL->validate_recaptcha($userIp,$response);
			$content	= json_decode($response, true);
			$isvalid 	= isset($content['success']) && $content['success']==1 ? true : false;
			return $isvalid;
		}

	}
	new Mowaf_reCaptcha;
