<?php 
 
use IDP\Helper\Utilities\MoIDPUtility; 
use IDP\Helper\Utilities\TabDetails; 
use IDP\Helper\Utilities\Tabs; 
 
$registered = MoIDPUtility::micr(); 
$verified 	= MoIDPUtility::iclv(); 
$controller = MSI_DIR . 'controllers/'; 
/** @var TabDetails $tabs */ 
$tabs = TabDetails::instance(); 
$tabDetails = $tabs->_tabDetails; 
$parentSlug = $tabs->_parentSlug; 
 
/** @var \IDP\Helper\Utilities\PluginPageDetails $profileTabDetails */ 
$profileTabDetails = $tabDetails[Tabs::PROFILE]; 
/** @var \IDP\Helper\Utilities\PluginPageDetails $settingsTabDetails */ 
$settingsTabDetails = $tabDetails[Tabs::SIGN_IN_SETTINGS]; 
/** @var  \IDP\Helper\Utilities\PluginPageDetails $licenseTabDetails */ 
$licenseTabDetails = $tabDetails[Tabs::LICENSE]; 
/** @var \IDP\Helper\Utilities\PluginPageDetails $metadataTabDetails */ 
$metadataTabDetails = $tabDetails[Tabs::METADATA]; 
/** @var \IDP\Helper\Utilities\PluginPageDetails $spSettingsTabDetails */ 
$spSettingsTabDetails = $tabDetails[Tabs::IDP_CONFIG]; 
/** @var \IDP\Helper\Utilities\PluginPageDetails $attrMapTabDetails */ 
$attrMapTabDetails = $tabDetails[Tabs::ATTR_SETTINGS]; 
/** @var \IDP\Helper\Utilities\PluginPageDetails $supportSection */ 
$supportSection = $tabDetails[Tabs::SUPPORT]; 
/** @var \IDP\Helper\Utilities\PluginPageDetails $demoRequestTabDetails */  
$demoRequestTabDetails = $tabDetails[Tabs::DEMO_REQUEST];  
/** @var \IDP\Helper\Utilities\PluginPageDetails $idpAddons */
$idpAddonsTabDetails = $tabDetails[Tabs::ADDONS];
/**var \IDP\Helper\Utilities\PluginPageDetails $DashBoard */
$idpDashBoardTabDetails = $tabDetails[Tabs::DASHBOARD];


include MSI_DIR 	 . 'views/common-elements.php';
include MSI_DIR 	 . 'controllers/sso-idp-navbar.php';

if( isset( $_GET[ 'page' ]))
{
    $account = $registered ? 'sso-idp-profile.php' : 'sso-idp-registration.php';
    switch(sanitize_text_field($_GET['page'])) 
    {
        case $metadataTabDetails->_menuSlug:
            include $controller . 'sso-idp-data.php';			break;
        case $spSettingsTabDetails->_menuSlug:
            include $controller . 'sso-idp-settings.php';		break;
        case $profileTabDetails->_menuSlug:
            include $controller . $account;		                break;
        case $settingsTabDetails->_menuSlug:
            include $controller . 'sso-signin-settings.php';	break;
        case $attrMapTabDetails->_menuSlug:
            include $controller . 'sso-attr-settings.php';		break;
        case $licenseTabDetails->_menuSlug:
            include $controller . 'sso-pricing.php';			break;
        case $supportSection->_menuSlug:
            include $controller . 'sso-idp-support.php';        break;
        case $parentSlug:
            include $controller . 'plugin-details.php';         break;
        case $demoRequestTabDetails->_menuSlug:  
            include $controller . 'sso-idp-request-demo.php';   break;  
        case $idpAddonsTabDetails->_menuSlug:
            include $controller . 'sso-idp-addons.php';         break;
        case $idpDashBoardTabDetails->_menuSlug :
            include $controller . 'plugin-details.php';         break;
    }
    include $controller . 'contact-button.php';
}


