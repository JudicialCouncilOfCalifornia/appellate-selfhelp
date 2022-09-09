<?php

namespace IDP\Helper\Utilities;


final class MenuItems
{
    
    private $_callback;

    
    private $_menuLogo;

    
    private $_tabDetails;

    
    private $_parentSlug;

    
    function __construct($class)
    {
        $this->_callback  = [   $class, 'mo_sp_settings' ];
        $this->_menuLogo  = MSI_ICON;
        
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
            'Wordpress IDP' ,
            'manage_options',
            $this->_parentSlug ,
            $this->_callback,
            $this->_menuLogo
        );
    }

    private function addSubMenus()
    {
        
        foreach ($this->_tabDetails as $tabDetail) {
            
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