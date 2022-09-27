<?php

namespace IDP\Helper\Constants;

final class MoIDPConstants
{
    const HOSTNAME   			= 'https://login.xecurify.com';
    const DEFAULT_CUSTOMER_KEY 	= '16555';
    const DEFAULT_API_KEY 		= 'fFd2XcvTGDemZvbw1bcUesNJWEqKbbUq';
    const AREA_OF_INTEREST 		= 'WP IDP Plugin';
    const UPGRADE_USERS_PLAN 	= 'wp_saml_idp_upgrade_plan';
    const PATTERN_PHONE			= '/^[\+]\d{1,4}\d{7,12}$|^[\+]\d{1,4}[\s]\d{7,12}$/';
    const SAMLSUPPORT_EMAIL     = 'samlsupport@xecurify.com';
    const FEEDBACK_FROM_EMAIL   = 'no-reply@xecurify.com';

    //SSO related constants
    const WS_FED 				= 'WS-FED';
    const SAML 	 				= 'SAML';
    const JWT					= 'JWT';
    const AUTHN_REQUEST 		= 'AuthnRequest';
    const LOGOUT_REQUEST 		= 'LogoutRequest';
    const SAML_RESPONSE 		= 'SamlResponse';
    const LOGOUT_RESPONSE 		= 'LogoutResponse';
    const WS_FED_RESPONSE 		= 'WsFedResponse';
    const JWT_RESPONSE 			= 'JwtResponse';
    const FAQ_URL               = 'https://faq.miniorange.com/kb/saml-single-sign-on/';
    const SAML_DOC              = 'https://plugins.miniorange.com/step-by-step-guide-for-wordpress-saml-idp';
    const WSFED_DOC             = 'https://www.miniorange.com/office-365-single-sign-on-(sso)-using-wsfed-protocol';
}