<?php

namespace IDP\Helper\Utilities;

use IDP\Helper\Constants\MoIDPConstants;

class MoIDPcURL
{
    public static function create_customer($email, $password)
    {
        $url = MoIDPConstants::HOSTNAME . '/moas/rest/customer/add';
        $customerKey = MoIDPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey = MoIDPConstants::DEFAULT_API_KEY;
        $fields = array (
            'areaOfInterest' => MoIDPConstants::AREA_OF_INTEREST,
            'email' 		 => $email,
            'password' 		 => $password
        );
        $json = json_encode($fields);
        $authHeader = self::createAuthHeader($customerKey,$apiKey);
        $response = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function get_customer_key($email, $password)
    {
        $url 	= MoIDPConstants::HOSTNAME. "/moas/rest/customer/key";
        $customerKey = MoIDPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 = MoIDPConstants::DEFAULT_API_KEY;
        $fields = array (
                    'email' 	=> $email,
                    'password'  => $password
                );
        $json = json_encode($fields);
        $authHeader = self::createAuthHeader($customerKey,$apiKey);
        $response = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function submit_contact_us( $q_email, $q_phone, $query )
    {
        $current_user = wp_get_current_user();
        $url    = MoIDPConstants::HOSTNAME . "/moas/rest/customer/contact-us";
        $query  = '['."WP IDP Free Plugin"." - " . MSI_VERSION .']: ' . $query;
        $customerKey 	= !MoIDPUtility::isBlank(get_site_option('mo_idp_admin_customer_key'))
                        ? get_site_option('mo_idp_admin_customer_key') : MoIDPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 	= !MoIDPUtility::isBlank(get_site_option('mo_idp_admin_customer_key'))
                        ? get_site_option('mo_idp_admin_customer_key') : MoIDPConstants::DEFAULT_API_KEY;
        $fields = array(
                    'firstName'	=> $current_user->user_firstname,
                    'lastName'	=> $current_user->user_lastname,
                    'company' 	=> esc_url_raw($_SERVER['SERVER_NAME']),
                    'email' 	=> $q_email,
                    'ccEmail'   =>'samlsupport@xecurify.com',
                    'phone'		=> $q_phone,
                    'query'		=> $query
                );
        $json 	  = json_encode( $fields );
        $authHeader = self::createAuthHeader($customerKey,$apiKey);
        self::callAPI($url, $json, $authHeader);
        return true;
    }

    public static function mius($customerKey,$apiKey,$code)
    {
        $url 	= MoIDPConstants::HOSTNAME . '/moas/api/backupcode/updatestatus';
        $fields = array (
            'code' => $code,
            'customerKey' => $customerKey
        );
        $json 		 = json_encode ( $fields );
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        $response 	 = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function notify($customerKey,$apiKey,$toEmail,$content,$subject)
    {
        $url 	= MoIDPConstants::HOSTNAME . '/moas/api/notify/send';
    
        $fields = array(
                'customerKey'   => $customerKey,
                'sendEmail'     => true,
                'email'         => array(
                    'customerKey'   => $customerKey,
                    'fromEmail'     => MoIDPConstants::FEEDBACK_FROM_EMAIL,
                    'bccEmail'      => MoIDPConstants::FEEDBACK_FROM_EMAIL,
                    'fromName'      => 'miniOrange',
                    'toEmail'       => MoIDPConstants::SAMLSUPPORT_EMAIL,
                    'toName'        => MoIDPConstants::SAMLSUPPORT_EMAIL,
                    'subject'       => $subject,
                    'content'       => $content
                ),
        );
        $json 		 = json_encode ( $fields );
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        return self::callAPI($url, $json, $authHeader);
    }

    public static function ccl($customerKey,$apiKey)
    {
        $url = MoIDPConstants::HOSTNAME . '/moas/rest/customer/license';
        $fields = array(
                'customerId' 	  => $customerKey,
                'applicationName' => 'wp_saml_idp'
        );
        $json 		 = json_encode ( $fields );
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        $response 	 = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function vml($customerKey,$apiKey,$code,$field1)
    {
        $url = MoIDPConstants::HOSTNAME . '/moas/api/backupcode/verify';
        $fields = array (
                'code' => $code ,
                'customerKey' => $customerKey,
                'additionalFields' => array(
                        'field1' => $field1
                )
        );
        $json 		 = json_encode ( $fields );
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        $response 	 = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function send_otp_token($auth_type, $phone, $email)
    {
        $url 		 = MoIDPConstants::HOSTNAME . '/moas/api/auth/challenge';
        $customerKey = MoIDPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 = MoIDPConstants::DEFAULT_API_KEY;

        $fields  	 = array(
                            'customerKey' 	  => $customerKey,
                            'email' 	  	  => $email,
                            'phone' 	  	  => $phone,
                            'authType' 	  	  => $auth_type,
                            'transactionName' => MoIDPConstants::AREA_OF_INTEREST
                        );
        $json 		 = json_encode($fields);
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        $response 	 = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function validate_otp_token($transactionId,$otpToken)
    {
        $url 		 = MoIDPConstants::HOSTNAME . '/moas/api/auth/validate';
        $customerKey = MoIDPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 = MoIDPConstants::DEFAULT_API_KEY;
        $fields 	 = array(
                        'txId'  => $transactionId,
                        'token' => $otpToken,
                     );
        $json 		 = json_encode($fields);
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        $response    = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function check_customer($email)
    {
        $url 	= MoIDPConstants::HOSTNAME . "/moas/rest/customer/check-if-exists";
        $customerKey = MoIDPConstants::DEFAULT_CUSTOMER_KEY;
        $apiKey 	 = MoIDPConstants::DEFAULT_API_KEY;
        $fields = array(
                    'email' 	=> $email,
                );
        $json     = json_encode($fields);
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        $response = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    public static function forgot_password($email,$customerKey,$apiKey)
    {
        $url 		 = MoIDPConstants::HOSTNAME . '/moas/rest/customer/password-reset';
        $fields 	 = array(
                        'email' => $email
                     );
        $json 		 = json_encode($fields);
        $authHeader  = self::createAuthHeader($customerKey,$apiKey);
        $response    = self::callAPI($url, $json, $authHeader);
        return $response;
    }

    private static function createAuthHeader($customerKey, $apiKey)
    {
        $currentTimestampInMillis = round(microtime(true) * 1000);
        $currentTimestampInMillis = number_format($currentTimestampInMillis, 0, '', '');

        $stringToHash = $customerKey . $currentTimestampInMillis . $apiKey;
        $authHeader = hash("sha512", $stringToHash);

        $header = [
            "Content-Type"      =>"application/json",
            "Customer-Key"      => $customerKey,
            "Timestamp"         => $currentTimestampInMillis,
            "Authorization"     => $authHeader
        ];
        return $header;
    }

    /**
     *  Uses WordPress HTTP API to make cURL calls to miniOrange server
     *  <br/>Arguments that you can pass
     * <ol>
     *  <li>'timeout'     => 5,</li>
     *  <li>'redirection' => 5,</li>
     *  <li>'httpversion' => '1.0',</li>
     *  <li>'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),</li>
     *  <li>'blocking'    => true,</li>
     *  <li>'headers'     => array(),</li>
     *  <li>'cookies'     => array(),</li>
     *  <li>'body'        => null,</li>
     *  <li>'compress'    => false,</li>
     *  <li>'decompress'  => true,</li>
     *  <li>'sslverify'   => true,</li>
     *  <li>'stream'      => false,</li>
     *  <li>'filename'    => null</li>
     * </ol>
     *
     * @param string $url           URL to post to
     * @param string $json_string   json encoded post data
     * @param array  $headers       headers to be passed in the call
     * @return string
     */
    private static function callAPI($url, $json_string, $headers = ["Content-Type" => "application/json"])
    {
        $args = [
            'method'        =>'POST',
            'body'          => $json_string,
            'timeout'       => '10000',
            'redirection'   => '10',
            'httpversion'   => '1.0',
            'blocking'      => true,
            'headers'       => $headers,
            'sslverify'     => MSI_TEST ? false: true,
        ];

        $response = wp_remote_post( $url, $args );
     
        if ( is_wp_error( $response ) ) {
            wp_die("Something went wrong: <br/> {$response->get_error_message()}");
        }
        return wp_remote_retrieve_body($response);
    }
}