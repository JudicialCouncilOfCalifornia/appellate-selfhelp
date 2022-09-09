<?php

	namespace IDP\Helper\Constants;

	class MoIDPMessages
	{
				const REQUIRED_REGISTRATION_FIELDS 	= 'Email, CompanyName, Password and Confirm Password are required fields. Please enter valid entries.';
		const INVALID_PASS_STRENGTH 		= 'Choose a password with minimum length 6.';
		const PASS_MISMATCH					= 'Passwords do not match.';
		const INVALID_EMAIL					= 'Please match the format of Email. No special characters are allowed.';
		const ERROR_EMAIL_OTP 				= 'There was an error in sending email. Please click on Resend OTP to try again.';
		const ERROR_PHONE_OTP				= 'There was an error in sending sms. Please click on Resend OTP link next to phone number textbox.';
		const ACCOUNT_EXISTS				= 'You already have an account with miniOrange. Please enter a valid password.';
		const ERROR_PHONE_FORMAT			= '{{phone}} is not a valid phone number. Please enter a valid Phone Number. E.g:+1XXXXXXXXXX';

		const RESEND_EMAIL_OTP				= 'Another One Time Passcode has been sent <b>( {{count}} )</b> for verification to {{email}}';
		const EMAIL_OTP_SENT				= 'A passcode is sent to {{email}}. Please enter the otp here to verify your email.';
		const RESEND_PHONE_OTP				= 'Another One Time Passcode has been sent <b>( {{count}} )</b> for verification to {{phone}}';
		const PHONE_OTP_SENT				= 'One Time Passcode has been sent for verification to {{phone}}';
		const REG_SUCCESS					= 'Your account has been retrieved successfully.';
		const NEW_REG_SUCCES				= 'Registration complete!';

				const REQUIRED_OTP 					= 'Please enter a value in OTP field.';
		const INVALID_OTP_FORMAT			= 'Please enter a valid value in OTP field.';
		const INVALID_OTP 					= 'Invalid one time passcode. Please enter a valid passcode.';
		const INVALID_CRED					= 'Invalid username or password. Please try again.';

				const REQUIRED_FIELDS  				= 'Please fill in the required fields.';
		const ERROR_OCCURRED 				= 'An error occured while processing your request. Please try again.';
		const NOT_REG_ERROR					= 'Please register and verify your account before trying to configure your settings.';
		const INVALID_OP 					= 'Invalid Operation. Please Try Again.';

				const INVALID_LICENSE 				= 'License key for this instance is incorrect. Make sure you have not tampered with it at all. Please enter a valid license key.';
		const LICENSE_KEY_IN_USE			= 'License key you have entered has already been used. Please enter a key which has not been used before on any other instance or if you have exausted all your keys then contact us at info@xecurify.com to buy more keys.';
		const ENTERED_INVALID_KEY 			= 'You have entered an invalid license key. Please enter a valid license key.';
		const LICENSE_VERIFIED				= 'Your license is verified. You can now setup the plugin.';
		const NOT_UPGRADED_YET				= 'You have not upgraded yet. <a href="{{url}}">Click here</a> to upgrade to premium version.';

				const PASS_RESET 					= 'You password has been reset successfully. Please enter the new password sent to your registered mail here.';

				const CURL_ERROR 					= 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Query submit failed.';

				const REQUIRED_QUERY_FIELDS 		= 'Please fill up Email and Query fields to submit your query.';
		const ERROR_QUERY 					= 'Your query could not be submitted. Please try again.';

		const QUERY_SENT					= 'Thanks for getting in touch! We shall get back to you shortly.';

				const ISSUER_EXISTS 				= 'You seem to already have a Service Provider for that issuer configured under : <i>{{name}}</i>';
		const SP_EXISTS						= 'You have already configured a Service Provider under that name';
		const INVALID_ENCRYPT_CERT			= 'You have not provided a certificate for encrypted assertion.';
		const NO_SP_CONFIG					= 'Please Configure a Service Provider.';

		const SETTINGS_SAVED				= 'Settings saved successfully.';
		const SP_DELETED 					= 'Service Provider settings deleted successfully.';
		const IDP_ENTITY_ID_CHANGED 		= 'IdP Entity ID changed successfully.';
		const IDP_ENTITY_ID_NULL			= 'IdP EntityID/Issuer cannot be NULL.';

				const INVALID_REQUEST_INSTANT 		= '<strong>INVALID_REQUEST: </strong>Request time is greater than the current time.<br/>';
		const INVALID_SAML_VERSION 			= 'We only support SAML 2.0! Please send a SAML 2.0 request.<br/>';
		const INVALID_SP 					= '<strong>INVALID_SP: </strong>No Service Provider configuration found. Please configure your Service Provider.<br/>';
		const INVALID_REQUEST_SIGNATURE 	= '<strong>INVALID_SIGNATURE: </strong>Invalid Signature!<br/>';
		const SAML_INVALID_OPERATION 		= '<strong>INVALID_OPERATION: </strong>Invalid Operation! Please contact your site administrator.<br/>';
		const INVALID_USER 					= 'SSO Failed. Please contact your Administrator for more details.';
		const MISSING_NAMEID 				= 'Missing <saml:NameID> or <saml:EncryptedID> in <samlp:LogoutRequest>.';
		const INVALID_NO_OF_NAMEIDS 		= 'More than one <saml:NameID> or <saml:EncryptedD> in <samlp:LogoutRequest>.';
		const MISSING_ID_FROM_REQUEST 		= 'Missing ID attribute on SAML message.';
		const MISSING_ISSUER_VALUE 			= 'Missing <saml:Issuer> in assertion.';

				const MISSING_WA_ATTR 				= 'The WS-Fed request has missing wa attribute.';
		const MISSING_WTREALM_ATTR 			= 'The WS-Fed request has missing wtrealm attribute.';

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