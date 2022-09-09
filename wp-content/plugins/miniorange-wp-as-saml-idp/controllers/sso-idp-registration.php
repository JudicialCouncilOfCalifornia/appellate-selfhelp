<?php

	use IDP\Helper\Constants\MoIDPConstants;
	use IDP\Handler\RegistrationHandler;

	$hostname 		= MoIDPConstants::HOSTNAME;
	
	$handler        = RegistrationHandler::instance();
	$url 			= $hostname.'/moas/login'.'?redirectUrl='.$hostname.'/moas/viewlicensekeys';
	$email 			= get_site_option('mo_idp_admin_email');
	$dir 			= MSI_DIR . 'views/registration/';
	$regnonce       = $handler->_nonce;

	if (get_site_option ('mo_idp_verify_customer' ))
	{
		include $dir . 'verify-customer.php';
	}
	else if (
	    trim ( get_site_option ( 'mo_idp_admin_email' ) ) != ''
        && trim ( get_site_option ( 'mo_idp_admin_api_key' ) ) == ''
        && get_site_option ( 'mo_idp_new_registration' ) != 'true'
    )
	{
		include $dir . 'verify-customer.php';
	}
	else if(
	    get_site_option('mo_idp_registration_status') == 'MO_OTP_DELIVERED_SUCCESS'
        || get_site_option('mo_idp_registration_status') == 'MO_OTP_VALIDATION_FAILURE'
        || get_site_option('mo_idp_registration_status') == 'MO_OTP_DELIVERED_FAILURE'
    )
	{
		include $dir . 'verify-otp.php';
	}
	else if (!$registered)
	{
		delete_site_option ( 'password_mismatch' );
		update_site_option ( 'mo_idp_new_registration', true);
		$current_user 	= wp_get_current_user();
		include $dir . 'new-registration.php';
	}
	else
	{
		include MSI_DIR . 'controllers/sso-idp-settings.php';
	}