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

		if ((isset($_SERVER["HTTPS"])) && (sanitize_text_field($_SERVER["HTTPS"]) == "on"))
			$pageURL .= "s";

		$pageURL .= "://";

		if (sanitize_text_field($_SERVER["SERVER_PORT"]) != "80")
			$pageURL .= esc_url_raw($_SERVER["SERVER_NAME"]).":".sanitize_text_field($_SERVER["SERVER_PORT"]).esc_url_raw($_SERVER["REQUEST_URI"]);

		else
			$pageURL .= esc_url_raw($_SERVER["SERVER_NAME"]).esc_url_raw($_SERVER["REQUEST_URI"]);

		if ( function_exists('apply_filters') ) apply_filters('wppb_curpageurl', $pageURL);

        return $pageURL;
	}

	public static function addSPCookie($issuer)
	{
		$COOKIES    = MoIDPUtility::sanitizeAssociateArray($_COOKIE);
		if(isset($COOKIES['mo_sp_count'])){
			for($i=1;$i<=$COOKIES['mo_sp_count'];$i++){
				if($COOKIES['mo_sp_' . $i . '_issuer'] == $issuer)
					return;
			}
		}
		$sp_count = isset($COOKIES['mo_sp_count']) ? $COOKIES['mo_sp_count'] + 1 : 1;
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
        /** @global \IDP\Helper\Database\MoDbQueries $dbIDPQueries */
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

	public static function getNewPublicCert()
	{
		return file_get_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt');
	}

	public static function getPrivateKey()
	{
		return file_get_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key');
	}

	public static function getPublicCertURL()
	{
		return MSI_URL . 'includes/resources/idp-signing.crt';
	}

	public static function getNewPublicCertURL()
	{
		return MSI_URL . 'includes/resources/idp-signing-new.crt';
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
		$newCert	 = NULL;
		
		if(!get_site_option("mo_idp_new_certs"))
		{
			$newCert = self::getNewPublicCert();
		}

		$generator 	= new MetadataGenerator($entity_id,TRUE,$certificate,$newCert,$login_url,$login_url,$logout_url,$logout_url);
		$metadata 	= $generator->generateMetadata();
		if(MSI_DEBUG) MoIDPUtility::mo_debug("Metadata Generated: " . $metadata);
		$metadataFile = fopen(MSI_DIR . "metadata.xml", "w");
		fwrite($metadataFile,$metadata);
		fclose($metadataFile);
	}

	public static function showMetadata()
	{
		$blogs 		 = is_multisite() ? get_sites() : null;
		$login_url   = is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$logout_url  = is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$entity_id   = get_site_option('mo_idp_entity_id') ?  get_site_option('mo_idp_entity_id') : MSI_URL;
		$certificate = self::getPublicCert();
		$newCert	 = NULL;
		
		if(!get_site_option("mo_idp_new_certs"))
		{
			$newCert = self::getNewPublicCert();
		}

		$generator 	= new MetadataGenerator($entity_id,TRUE,$certificate,$newCert,$login_url,$login_url,$logout_url,$logout_url);
		$metadata 	= $generator->generateMetadata();
		
		if(ob_get_contents())
			ob_clean();
		
		header( 'Content-Type: text/xml' );
		echo $metadata;
		exit;
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

	/**
	 * This function is created to check whether a person is having 
	 * admin access or not
	 * 
	 * @return True or False
	 */
	public static function isAdmin(){
		$user = wp_get_current_user();
		if ( in_array( 'administrator', (array) $user->roles )  ) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * This function is created to update the certificates. The validity of the certificates
	 * is set to 365 days, and this function will replace the existing certificates with the 
	 * new certificates. This will also update the plugin metadata XML file once the certificates
	 * are updated.
	 *
	 * @return void
	 */
	public static function useNewCerts()
	{
		$oldCert = fopen(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt',"w");
		$newCert = fopen(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt',"r");
		$oldKey = fopen(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key',"w");
		$newKey = fopen(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.key',"r");

		file_put_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt', file_get_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt'));
		file_put_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.key', file_get_contents(MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.key'));

		fclose($oldCert);
		fclose($newCert);
		fclose($oldKey);
		fclose($newKey);

		update_site_option("mo_idp_new_certs",TRUE);

		$metadata_dir		= MSI_DIR . "metadata.xml";
		if(file_exists($metadata_dir) && filesize($metadata_dir) > 0 ) {
			unlink($metadata_dir);
            MoIDPUtility::createMetadataFile();
		}
	}

	/**
	 * This function compares the expiry of the old and the new certificate files in the plugin.
	 * If the new certificate expiry is longer than the current certificate, it will return True, else False.
	 *
	 * @return boolean
	 */
	public static function checkCertExpiry()
	{
        $currentCert = MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing.crt';
        $newCert = MSI_DIR . 'includes' . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'idp-signing-new.crt';

        $currentCertExpiry =  (openssl_x509_parse(file_get_contents($currentCert))['validTo_time_t']) - time();
		$newCertExpiry =  (openssl_x509_parse(file_get_contents($newCert))['validTo_time_t']) - time();

        if ( $newCertExpiry > $currentCertExpiry )
            return TRUE;
        else
            return FALSE;
	}


    /*
     | ------------------------------------------------------------------------------------------
     | FREE PLUGIN SPECIFIC FUNCTIONS
     | ------------------------------------------------------------------------------------------
     */

    public static function iclv()
    {
        return TRUE;
    }

	// SP MetaData Upload 
	public static function mo_saml_wp_remote_get($url, $args = array()){
		$response = wp_remote_get($url, $args);
	
		if(!is_wp_error($response)){
				return $response;
		} else {
				return null;   
		}
	} 

	/**
	 * Used to sanitize an associative array.
	 * 
	 * @param array $rawArray
	 * @return array $sanitizedArray
	 */
	public static function sanitizeAssociateArray($rawArray) 
	{
		$sanitizedArray = array();
		foreach($rawArray as $key => $value) {
			$sanitizedArray[$key] = sanitize_text_field( $value );
		}
		return $sanitizedArray;
	}

	/**
	 * Checks whether the file was uploaded via HTTP POST
	 * and returns the contents
	 *
	 * @param string $fileName
	 * @return file $file | null
	 */
	public static function handleFileUpload($fileName)
	{
		if(is_uploaded_file($fileName))
			$file = @file_get_contents($fileName);
		else
			$file = null;

		return $file;
	}
}