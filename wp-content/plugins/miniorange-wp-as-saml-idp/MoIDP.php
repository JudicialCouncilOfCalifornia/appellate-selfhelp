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

final class MoIDP
{
    use Instance;

    /** Private constructor to avoid direct object creation */
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
        add_action( 'admin_footer',                         array( $this, 'feedback_request'  				)        );
        add_filter( 'plugin_action_links_'.MSI_PLUGIN_NAME, array($this , 'mo_idp_plugin_anchor_links'      )        );
        register_activation_hook  ( MSI_PLUGIN_NAME, 	    array( $this, 'mo_plugin_activate'			    ) 		 );
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
    }

    function mo_idp_plugin_settings_script()
    {
        wp_enqueue_script( 'mo_idp_admin_settings_script', MSI_JS_URL, array('jquery') );
    }


    function mo_plugin_activate()
    {
        /** @var MoDbQueries $dbIDPQueries */
        global $dbIDPQueries;
        $dbIDPQueries->checkTablesAndRunQueries();
        if (!get_site_option("mo_idp_new_certs"))
        {
            MoIDPUtility::useNewCerts();
        }
        $metadata_dir		= MSI_DIR . "metadata.xml";
        if (file_exists($metadata_dir) && filesize($metadata_dir) > 0) {
            unlink($metadata_dir);
            MoIDPUtility::createMetadataFile();
        }
        if (get_site_option("idp_keep_settings_intact", NULL) === NULL)
        {
            update_site_option( "idp_keep_settings_intact", TRUE );
        }
    }

    function mo_show_message($content,$type)
    {
        new MoIdPDisplayMessages($content, $type);
    }

    function feedback_request()
    {
        include MSI_DIR . 'controllers/feedback.php';
    }

    function mo_idp_plugin_anchor_links( $links ) 
    {
        if(array_key_exists("deactivate", $links))
        {
            $arr = array();
            $data = [
                'Settings'          => 'idp_configure_idp',
                'Purchase License'  => 'idp_upgrade_settings'
            ];

            foreach ($data as $key => $val) {
                $url = esc_url(add_query_arg(
                    'page',
                    $val,
                    get_admin_url() . 'admin.php?'
                ));
                $anchor_link = "<a href='$url'>" . __($key) . '</a>' ;
                array_push($arr, $anchor_link);
            }
            $links = $arr + $links;
        }
        return $links ;
    }
}