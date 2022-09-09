<?php

use IDP\Helper\Constants\MoIDPConstants;

$profile_url	= add_query_arg( array('page' => $profileTabDetails->_menuSlug      ), $_SERVER['REQUEST_URI'] );
$license_url 	= add_query_arg( array('page' => $licenseTabDetails->_menuSlug	    ), $_SERVER['REQUEST_URI'] );
$register_url	= add_query_arg( array('page' => $profileTabDetails->_menuSlug	    ), $_SERVER['REQUEST_URI'] );
$idp_settings	= add_query_arg( array('page' => $spSettingsTabDetails->_menuSlug   ), $_SERVER['REQUEST_URI'] );
$sp_settings	= add_query_arg( array('page' => $metadataTabDetails->_menuSlug	    ), $_SERVER['REQUEST_URI'] );
$login_settings	= add_query_arg( array('page' => $settingsTabDetails->_menuSlug	    ), $_SERVER['REQUEST_URI'] );
$attr_settings	= add_query_arg( array('page' => $attrMapTabDetails->_menuSlug		), $_SERVER['REQUEST_URI'] );
$help_url       = MoIDPConstants::FAQ_URL;
$support_url    = add_query_arg( array('page' => $supportSection->_menuSlug         ), $_SERVER['REQUEST_URI'] );

$active_tab 	= $_GET['page'];

include MSI_DIR . 'views/navbar.php';