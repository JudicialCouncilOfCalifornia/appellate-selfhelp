<?php

class Mowaf_MocURL
{

	public static function create_customer($email, $company, $password, $phone = '', $first_name = '', $last_name = '')
	{
		$url = MowafConstants::HOST_NAME . '/moas/rest/customer/add';
		$fields = array (
			'companyName' 	 => $company,
			'areaOfInterest' => 'WP Malware Protection',
			'firstname' 	 => $first_name,
			'lastname' 		 => $last_name,
			'email' 		 => $email,
			'phone' 		 => $phone,
			'password' 		 => $password
		);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	public static function get_customer_key($email, $password) 
	{
		$url 	= MowafConstants::HOST_NAME. "/moas/rest/customer/key";
		$fields = array (
					'email' 	=> $email,
					'password'  => $password
				);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	function submit_contact_us( $q_email, $q_phone, $query )
	{
		$current_user = wp_get_current_user();
		$url    = MowafConstants::HOST_NAME . "/moas/rest/customer/contact-us";
		$query  = '[Web Application firewall Plugin-'.MO_WAF_VERSION.' ]: 	'. esc_html($query);
		$fields = array(
					'firstName'	=> $current_user->user_firstname,
					'lastName'	=> $current_user->user_lastname,
					'company' 	=> sanitize_text_field($_SERVER['SERVER_NAME']),
					'email' 	=> $q_email,
					'ccEmail' => '2fasupport@xecurify.com',
					'phone'		=> $q_phone,
					'query'		=> $query
				);
		$field_string = json_encode( $fields );
		$response = self::callAPI($url, $field_string);
		
		return true;
	}

	function lookupIP($ip)
	{
		$url 	= MowafConstants::HOST_NAME. "/moas/rest/security/iplookup";
		$fields = array (
					'ip' => $ip
				);
		$json = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	function send_otp_token($auth_type, $phone, $email)
	{
		
		$url 		 = MowafConstants::HOST_NAME . '/moas/api/auth/challenge';
		$customerKey = MowafConstants::DEFAULT_CUSTOMER_KEY;
		$apiKey 	 = MowafConstants::DEFAULT_API_KEY;

		$fields  	 = array(
							'customerKey' 	  => $customerKey,
							'email' 	  	  => $email,
							'phone' 	  	  => $phone,
							'authType' 	  	  => $auth_type,
							'transactionName' => 'WP Malware Protection'
						);
		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
		$response 	 = self::callAPI($url, $json, $authHeader);
		return $response;
	}

	function validate_recaptcha($ip,$response)
	{
		$url 		 = MowafConstants::RECAPTCHA_VERIFY;
		$json		 = "";
		$fields 	 = array(
							'response' => $response,
							'secret'   => get_option('mo_wpns_recaptcha_secret_key'),
							'remoteip' => $ip
						);
		foreach($fields as $key=>$value) { $json .= $key.'='.$value.'&'; }
		rtrim($json, '&');
		$response 	 = self::mollm_callAPI($url, $json, null);
		return $response;
	}
	private static function mollm_callAPI($url, $json_string, $headers = array("Content-Type: application/json")) {

		$results = wp_remote_post( $url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'body' => $json_string,
                'cookies' => array())); 
		return $results['body'];
	}

	function validate_otp_token($transactionId,$otpToken)
	{
		$url 		 = MowafConstants::HOST_NAME . '/moas/api/auth/validate';
		$customerKey = MowafConstants::DEFAULT_CUSTOMER_KEY;
		$apiKey 	 = MowafConstants::DEFAULT_API_KEY;

		$fields 	 = array(
						'txId'  => $transactionId,
						'token' => $otpToken,
					 );

		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
		$response    = self::callAPI($url, $json, $authHeader);
		return $response;
	}
	
	function check_customer($email)
	{
		$url 	= MowafConstants::HOST_NAME . "/moas/rest/customer/check-if-exists";
		$fields = array(
					'email' 	=> $email,
				);
		$json     = json_encode($fields);
		$response = self::callAPI($url, $json);
		return $response;
	}
	
	function mo_wpns_forgot_password()
	{
	
		$url 		 = MowafConstants::HOST_NAME . '/moas/rest/customer/password-reset';
		$email       = get_option('mo_wpns_admin_email');
		$customerKey = get_option('mo_wpns_admin_customer_key');
		$apiKey 	 = get_option('mo_wpns_admin_api_key');
	
		$fields 	 = array(
						'email' => $email
					 );
	
		$json 		 = json_encode($fields);
		$authHeader  = $this->createAuthHeader($customerKey,$apiKey);
		$response    = self::callAPI($url, $json, $authHeader);
		return $response;
	}


	function send_notification($toEmail,$subject,$content,$fromEmail,$fromName,$toName)
	{
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		$headers .= 'From: '.$fromName.'<'.$fromEmail.'>' . "\r\n";

		mail($toEmail,$subject,$content,$headers);

		return json_encode(array("status"=>'SUCCESS','statusMessage'=>'SUCCESS'));
	}

	//added for feedback

    function send_email_alert($email,$phone,$message,$feedback=true){

        $url = MowafConstants::HOST_NAME . '/moas/api/notify/send';
        $customerKey = MowafConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 = MowafConstants::DEFAULT_API_KEY;
        $fromEmail			= 'no-reply@xecurify.com';

        global $user;
        $user         = wp_get_current_user();

		if($feedback)
        	$subject            = "Deactivate [Feedback]: WordPress WP Firewall Plugin : ";
		else
			$subject            = "Deactivate [Feedback Skipped]: WordPress WP Firewall Plugin : ";

		$subject .=esc_html($email);

        $query        = '[WordPress WP Firewall Plugin: - V '.MO_WAF_VERSION.']: ' . $message;


        $content='<div >Hello, <br><br>First Name :'.esc_html($user->user_firstname).'<br><br>Last  Name :'.esc_html($user->user_lastname).'   <br><br>Company :<a href="'.esc_html($_SERVER['SERVER_NAME']).'" target="_blank" >'.esc_html($_SERVER['SERVER_NAME']).'</a><br><br>Phone Number :'.esc_html($phone).'<br><br>Email :<a href="mailto:'.sanitize_email($email).'" target="_blank">'.esc_html($email).'</a><br><br>Query :'.$query.'</div>';


		$fields       = array(
			'customerKey' => $customerKey,
			'sendEmail'   => true,
			'email'       => array(
				'customerKey' => $customerKey,
				'fromEmail'   => $fromEmail,
				'fromName'    => 'Xecurify',
				'toEmail'     => '2fasupport@xecurify.com',
				'toName'      => '2fasupport@xecurify.com',
				'subject'     => $subject,
				'content'     => $content
			),
		);
		$field_string = json_encode( $fields );

		$currentTimeInMillis = round( microtime( true ) * 1000 );
        $currentTimeInMillis = number_format( $currentTimeInMillis, 0, '', '' );

		$stringToHash = $customerKey . $currentTimeInMillis . $apiKey;
        $hashValue = hash( "sha512", $stringToHash );
		
		$headers = array(
            "Content-Type" => "application/json",
            "Customer-Key" => $customerKey,
            "Timestamp" => $currentTimeInMillis,
            "Authorization" => $hashValue
        );


        $args = array(
            'method' => 'POST',
            'body' => $field_string,
            'timeout' => '5',
            'redirection' => '5',
            'sslverify'  =>true,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers
        );

		$response=wp_remote_post($url, $args);

        return $response;

    }


	private static function createAuthHeader($customerKey, $apiKey) {
		$currentTimestampInMillis = round(microtime(true) * 1000);
		$currentTimestampInMillis = number_format($currentTimestampInMillis, 0, '', '');

		$stringToHash = $customerKey . $currentTimestampInMillis . $apiKey;
		$authHeader = hash("sha512", $stringToHash);

		$header = array (
			"Content-Type: application/json",
			"Customer-Key: $customerKey",
			"Timestamp: $currentTimestampInMillis",
			"Authorization: $authHeader"
		);
		return $header;
	}

	private static function callAPI($url, $json_string, $headers = array("Content-Type"=>"application/json","charset"=>"UTF-8","Authorization"=>"Basic")) {
        $response = null;
        $results = wp_remote_post( $url, array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => $headers,
                'body' => $json_string,
                'cookies' => array()
            )
        );
           
       if( isset($results) && $results['body'] == 'Query submitted.') {
        
          return true;
            
        }else{
         $result = json_decode($results['body'],true);
            if(isset($result['status'])){
                if ($result['status'] == 'SUCCESS') {
                    return $results['body'];
                }else{
                    return $results['body'];
				}
            }
        }

    }



}