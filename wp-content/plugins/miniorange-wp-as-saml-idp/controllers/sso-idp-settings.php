<?php

    
    global $dbIDPQueries;

	$sp_list 		= $dbIDPQueries->get_sp_list();
	$page 			= $_GET['page'];
	$action 		= isset($_GET['action']) ? $_GET['action'] : '';

    $protocol_inuse = $action=="add_wsfed_app" ? "WSFED" : ( $action=="add_jwt_app" ? "JWT" : "SAML" );

	$goback_url		= remove_query_arg (array('action','id'),$_SERVER['REQUEST_URI']);
	$post_url 		= remove_query_arg (array('action','id'),$_SERVER['REQUEST_URI']);

	$sp_page_url	= add_query_arg( array('page' 	=> $spSettingsTabDetails->_menuSlug ), $_SERVER['REQUEST_URI'] );
	$delete_url		= add_query_arg( array('action' => 'delete_sp_settings'	            ), $_SERVER['REQUEST_URI'] ).'&id=';
	$settings_url 	= add_query_arg( array('action' => 'show_idp_settings'	            ), $_SERVER['REQUEST_URI'] ).'&id=';

	$saml_doc  		= MSI_URL . 'includes' . DIRECTORY_SEPARATOR . 'resources/Generic_IdP_Plugin_Guide.pdf';
	$wsfed_doc		= MSI_URL . 'includes' . DIRECTORY_SEPARATOR . 'resources/Office365WsFedSetupGuide.pdf';

	$sp_exists 		= TRUE;

	if(isset($action) && $action=='delete_sp_settings')
	{
		$sp 		 = $dbIDPQueries->get_sp_data($_GET['id']);
		include MSI_DIR . 'views/idp-delete.php';
	}
	else if(!empty($sp_list))
	{
		$sp 		= $sp_list[0];
		$header		= 'EDIT '.(!empty($sp) ? $sp->mo_idp_sp_name : 'IDP' ).' SETTINGS';
		$sp_exists	= FALSE;
		$test_window= site_url(). '/?option=testConfig'.
                                    '&acs='.$sp->mo_idp_acs_url.
                                    '&issuer='.$sp->mo_idp_sp_issuer.
                                    '&defaultRelayState='.$sp->mo_idp_default_relayState;

        if($sp->mo_idp_protocol_type=="JWT")
            include MSI_DIR . 'views/idp-jwt-settings.php';
        else if($sp->mo_idp_protocol_type=="WSFED")
            include MSI_DIR . 'views/idp-wsfed-settings.php';
        else
            include MSI_DIR . 'views/idp-settings.php';
	}
	else
	{
        $sp          = empty($sp_list) ? '' : $sp_list[0];
        $header		 = $protocol_inuse=="SAML" ? 'ADD NEW SAML SERVICE PROVIDER' :
                        ($protocol_inuse=="JWT" ? 'ADD NEW JWT APP' : 'ADD NEW WS-FED SERVICE PROVIDER' );
		$test_window = '';
        if($protocol_inuse=="JWT")
            include MSI_DIR . 'views/idp-jwt-settings.php';
        else if($protocol_inuse=="WSFED")
            include MSI_DIR . 'views/idp-wsfed-settings.php';
        else
            include MSI_DIR . 'views/idp-settings.php';
	}