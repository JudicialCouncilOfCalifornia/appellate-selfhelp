<?php

namespace IDP\Helper\Utilities;

/**
 * This class simply adds menu items for the plugin
 * in the WordPress dashboard.
 */
final class MenuItems
{
    /**
     * The URL for the plugin icon to be shown in the dashboard
     * @var string
     */
    private $_callback;

    /**
     * The slug for the main menu
     * @var string
     */
    private $_menuLogo;

    /**
     * Array of PluginPageDetails Object detailing
     * all the page menu options.
     *
     * @var array $_tabDetails
     */
    private $_tabDetails;

    /**
     * The Parent Slug of the plugin
     * @var string
     */
    private $_parentSlug;

    /**
     * MenuItems constructor.
     *
     * @param $class
     */
    function __construct($class)
    {
        $this->_callback  = [   $class, 'mo_sp_settings' ];
        $this->_menuLogo  = MSI_ICON;
        /** @var TabDetails $tabDetails */
        $tabDetails = TabDetails::instance();
        $this->_tabDetails = $tabDetails->_tabDetails;
        $this->_parentSlug = $tabDetails->_parentSlug;
        $this->addMainMenu();
        $this->addSubMenus();
    }

    private function addMainMenu()
    {
        add_menu_page (
            'SAML IDP' ,
            'WordPress IDP' ,
            'manage_options',
            $this->_parentSlug ,
            $this->_callback,
            $this->_menuLogo
        );
    }

    private function addSubMenus()
    {
        /** @var array $tabDetail */
        foreach ($this->_tabDetails as $tabDetail) {
            /** @var PluginPageDetails$tabDetail $tabDetail */
            if($tabDetail->_menuTitle != 'Dashboard')
            {     
                add_submenu_page(
                    $this->_parentSlug,
                    $tabDetail->_pageTitle,
                    $tabDetail->_menuTitle,
                    'manage_options',
                    $tabDetail->_menuSlug,
                    $this->_callback
                );
            }
        }
    }
}