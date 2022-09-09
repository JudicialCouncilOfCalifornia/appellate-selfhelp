<?php

namespace IDP;

use IDP\Actions\RegistrationActions;
use IDP\Actions\SettingsActions;
use IDP\Actions\SSOActions;
use IDP\Helper\Constants\MoIdPDisplayMessages;
use IDP\Helper\Database\MoDbQueries;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MenuItems;
use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\RewriteRules;
use IDP\Helper\Utilities\TabDetails;
use IDP\Helper\Utilities\Tabs;
use IDP\VisualTour\PointersManager;

final class MoIDP
{
    use Instance;

    
    private function __construct()
    {
        $this->initializeGlobalVariables();
        $this->initializeActions();
        $this->addHooks();
    }

    function initializeGlobalVariables()
    {
        global $dbIDPQueries;
        $dbIDPQueries = MoDbQueries::instance();
    }

    function addHooks()
    {
        add_action( 'mo_idp_show_message',  		        array( $this, 'mo_show_message' 				), 1 , 2 );
        add_action( 'admin_menu', 					        array( $this, 'mo_idp_menu' 					) 		 );
        add_action( 'admin_enqueue_scripts', 		        array( $this, 'mo_idp_plugin_settings_style' 	) 		 );
        add_action( 'admin_enqueue_scripts', 		        array( $this, 'mo_idp_plugin_settings_script' 	) 		 );
        add_action( 'enqueue_scripts', 				        array( $this, 'mo_idp_plugin_settings_style' 	) 		 );
        add_action( 'enqueue_scripts', 				        array( $this, 'mo_idp_plugin_settings_script' 	) 		 );
        add_action( 'admin_enqueue_scripts', 		        array( $this, 'load_pointers' 	                ) 		 );
        add_action( 'admin_footer',                         array( $this, 'feedback_request'  				)        );
        register_activation_hook  ( MSI_PLUGIN_NAME, 	array( $this, 'mo_plugin_activate'			    ) 		 );
    }

    function initializeActions()
    {
        RewriteRules::instance();
        SettingsActions::instance();
        RegistrationActions::instance();
        SSOActions::instance();
    }

    function mo_idp_menu()
    {
        new MenuItems($this);
    }

    function mo_sp_settings()
    {
        include 'controllers/sso-main-controller.php';
    }

    function mo_idp_plugin_settings_style()
    {
        wp_enqueue_style( 'mo_idp_admin_settings_style'	,MSI_CSS_URL				 );
        wp_enqueue_style( 'wp-pointer' );
    }

    function mo_idp_plugin_settings_script()
    {
        wp_enqueue_script( 'mo_idp_admin_settings_script',MSI_JS_URL , array('jquery'));
    }

    function load_pointers($page)
    {
        $pointers = PointersManager::instance()->parse()->filter($page);
        if(MoIDPUtility::isBlank($pointers)) return;

        wp_enqueue_script( MSI_POINTER_PREFIX, MSI_POINTER_JS, array('wp-pointer'), NULL, TRUE );
                $data = [
            'next_label'    => __( 'Next' ),
            'close_label'   => __('Close'),
            'pointers'      => $pointers,
            'registerURL'   => getRegistrationURL()
        ];
        wp_localize_script( MSI_POINTER_PREFIX, 'idppointers', $data );
    }

    function mo_plugin_activate()
    {
        
        global $dbIDPQueries;
        $dbIDPQueries->checkTablesAndRunQueries();
    }

    function mo_show_message($content,$type)
    {
        new MoIdPDisplayMessages($content,$type);
    }

    function feedback_request()
    {
        include MSI_DIR . 'controllers/feedback.php';
    }
}