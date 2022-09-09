<?php

namespace IDP\Helper\Utilities;

use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\SAML2\MetadataGenerator;
use IDP\Exception\InvalidSSOUserException;
use IDP\Exception\InvalidOperationException;

class MoIDPUtility
{

	public static function getHiddenPhone($phone)
	{
		$hidden_phone = 'xxxxxxx' . substr($phone,strlen($phone) - 3);
		return $hidden_phone;
	}

	public static function isBlank( $value )
	{
		if( ! isset( $value ) || empty( $value ) ) return TRUE;
		return FALSE;
	}

	public static function isCurlInstalled()
	{
		if  (in_array  ('curl', get_loaded_extensions())) {
			return 1;
		} else
			return 0;
	}

	public static function startSession()
	{
		if( ! session_id() || session_id() == '' || !isset($_SESSION) ) {
			session_start();
		}
	}

	public static function validatePhoneNumber($phone)
	{
		if(!preg_match(MoIDPConstants::PATTERN_PHONE,$phone,$matches))
			return FALSE;
		else
			return TRUE;
	}

	public static function getCurrPageUrl()
	{
		$pageURL = 'http';

		if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on"))
			$pageURL .= "s";

		$pageURL .= "://";

		if ($_SERVER["SERVER_PORT"] != "80")
			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

		else
			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

		if ( function_exists('apply_filters') ) apply_filters('wppb_curpageurl', $pageURL);

        return $pageURL;
	}

	public static function addSPCookie($issuer)
	{
		if(isset($_COOKIE['mo_sp_count'])){
			for($i=1;$i<=$_COOKIE['mo_sp_count'];$i++){
				if($_COOKIE['mo_sp_' . $i . '_issuer'] == $issuer)
					return;
			}
		}
		$sp_count = isset($_COOKIE['mo_sp_count']) ? $_COOKIE['mo_sp_count'] + 1 : 1;
		setcookie('mo_sp_count', $sp_count);
		setcookie('mo_sp_' . $sp_count . '_issuer', $issuer);
	}

	public static function getHiddenEmail($email)
	{
        if(!isset($email) || trim($email)==='')
			return "";

		$emailsize = strlen($email);
		$partialemail = substr($email,0,1);
		$temp = strrpos($email,"@");
		$endemail = substr($email,$temp-1,$emailsize);
		for($i=1;$i<$temp;$i++)
			$partialemail = $partialemail . 'x';

		$hiddenemail = $partialemail . $endemail;

        return $hiddenemail;
    }

	public static function micr()
	{
		$email 			= get_site_option('mo_idp_admin_email');
		$customerKey 	= get_site_option('mo_idp_admin_customer_key');
        return !$email || !$customerKey || !is_numeric(trim($customerKey)) ? 0 : 1;
	}

	public static function gssc()
	{
        
		global $dbIDPQueries;
		return $dbIDPQueries->get_sp_count();
	}

	public static function createCustomer()
	{
		$email 			= get_site_option('mo_idp_admin_email');
		$password 		= get_site_option('mo_idp_admin_password');
		$content 		= MoIDPcURL::create_customer($email,$password);
		return $content;
	}

	public static function getCustomerKey($email,$password)
	{
		$content 	= MoIDPcURL::get_customer_key($email,$password);
		return $content;
	}

	public static function checkCustomer()
	{
		$email 	 = get_site_option("mo_idp_admin_email");
		$content = MoIDPcURL::check_customer($email);
		return $content;
	}

	public static function sendOtpToken($authType,$email='',$phone='')
	{
		$content = MoIDPcURL::send_otp_token($authType, $phone, $email);
		return $content;
	}

	public static function validateOtpToken($transactionId,$otpToken)
	{
		$content = MoIDPcURL::validate_otp_token($transactionId, $otpToken);
		return $content;
	}

	public static function submitContactUs( $email, $phone, $query )
	{
		MoIDPcURL::submit_contact_us($email, $phone, $query);
		return true;
	}

	public static function forgotPassword($email)
	{
		$email       = get_site_option('mo_idp_admin_email');
		$customerKey = get_site_option('mo_idp_admin_customer_key');
		$apiKey 	 = get_site_option('mo_idp_admin_api_key');
		$content	 = MoIDPcURL::forgot_password($email, $customerKey, $apiKey);
		return $content;
	}

	public static function ccl()
	{
		$customerKey = get_site_option ( 'mo_idp_admin_customer_key' );
		$apiKey 	 = get_site_option ( 'mo_idp_admin_api_key' );
		$content 	 = MoIDPcURL::ccl($customerKey, $apiKey);
		return $content;
	}

	public static function unsetCookieVariables($vars)
	{
		foreach ($vars as $var)
		{
			unset($_COOKIE[$var]);
			setcookie($var, '', time() - 3600);
		}
	}

	public static function getPublicCertPath()
	{
		return MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt';
	}

	public static function getPrivateKeyPath()
	{
		return MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key';
	}

	public static function getPublicCert()
	{
		return file_get_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt');
	}

	public static function getPrivateKey()
	{
		return file_get_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key');
	}

	public static function getPublicCertURL()
	{
		return MSI_URL . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt';
	}

	public static function mo_debug($message)
	{
		error_log("[MO-MSI-LOG][".date('m-d-Y', time())."]: " . $message);
	}

	public static function createMetadataFile()
	{
		$blogs 		 = is_multisite() ? get_sites() : null;
		$login_url   = is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$logout_url  = is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$entity_id   = get_site_option('mo_idp_entity_id') ?  get_site_option('mo_idp_entity_id') : MSI_URL;
		$certificate = self::getPublicCert();

		$generator 	= new MetadataGenerator($entity_id,TRUE,$certificate,$login_url,$login_url,$logout_url,$logout_url);
		$metadata 	= $generator->generateMetadata();
		if(MSI_DEBUG) MoIDPUtility::mo_debug("Metadata Generated: " . $metadata);
		$metadataFile = fopen(MSI_DIR . "metadata.xml", "w");
		fwrite($metadataFile,$metadata);
		fclose($metadataFile);
	}

	public static function generateRandomAlphanumericValue($length)
	{
		$chars = "abcdef0123456789";
		$chars_len = strlen($chars);
		$uniqueID = "";
		for ($i = 0; $i < $length; $i++)
			$uniqueID .= substr($chars,rand(0,15),1);
		return 'a'.$uniqueID;
	}


    

    public static function iclv()
    {
        return TRUE;
    }
}