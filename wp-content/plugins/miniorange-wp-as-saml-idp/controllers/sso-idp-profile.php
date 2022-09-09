<?php

use IDP\Handler\RegistrationHandler;

$current_user 	= wp_get_current_user();
$email 			= get_site_option('mo_idp_admin_email');
$customerID		= get_site_option('mo_idp_admin_customer_key');
$apiKey			= get_site_option('mo_idp_admin_api_key');
$tokenKey		= get_site_option('mo_idp_customer_token');
$regnonce       = RegistrationHandler::instance()->_nonce;

include MSI_DIR. 'views/user-profile.php';
