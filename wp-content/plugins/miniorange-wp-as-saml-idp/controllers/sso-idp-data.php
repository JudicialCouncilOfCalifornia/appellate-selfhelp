<?php

	use IDP\Helper\Utilities\MoIDPUtility;
	use IDP\Helper\Utilities\SAMLUtilities;

    // $metadata_url 		= MSI_URL . 'metadata.xml';
	$metadata_url		= home_url( '/?option=mo_idp_metadata' );
    $metadata_dir		= MSI_DIR . "metadata.xml";

	$protocol_type 		= get_site_option('mo_idp_protocol');
	$plugins_url 		= MSI_URL;
	$blogs 				= is_multisite() ? get_sites() : null;
	$site_url 			= is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
	$certificate_url 	= MoIDPUtility::getPublicCertURL();
	$new_certificate_url= MoIDPUtility::getNewPublicCertURL();
	$certificate 		= SAMLUtilities::desanitize_certificate(MoIDPUtility::getPublicCert());
	$idp_settings 		= add_query_arg( array('page' => $spSettingsTabDetails->_menuSlug ), esc_url_raw($_SERVER['REQUEST_URI']) );
	$idp_entity_id 		= get_site_option('mo_idp_entity_id') ?  get_site_option('mo_idp_entity_id') : $plugins_url;

	$wsfed_command 		= 'Set-MsolDomainAuthentication -Authentication Federated -DomainName '.
                            ' <b>&lt;your_domain&gt;</b> '.
                            '-IssuerUri "'.$idp_entity_id.
                            '" -LogOffUri "'.$site_url.
                            '" -PassiveLogOnUri "'.$site_url.
                            '" -SigningCertificate "'.$certificate.
                            '" -PreferredAuthenticationProtocol WSFED';

	$expired_cert		= get_site_option("mo_idp_new_certs") ? get_site_option("mo_idp_new_certs") : FALSE;

	//Generate the metadata file if no file exists.
	if(!file_exists($metadata_dir) || filesize($metadata_dir) == 0 ) 
	{
        MoIDPUtility::createMetadataFile();
	}
	
	if(!get_site_option("mo_idp_new_certs"))
	{
		MoIDPUtility::createMetadataFile();
	}
	else
	{
		$useNewCert 	= MoIDPUtility::checkCertExpiry();
		if ($useNewCert == TRUE)
			update_site_option ("mo_idp_new_certs", FALSE);
	}

include MSI_DIR . 'views/idp-data.php';