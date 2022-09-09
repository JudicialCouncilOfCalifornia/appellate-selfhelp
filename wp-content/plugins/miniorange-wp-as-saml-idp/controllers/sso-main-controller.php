<?php

use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\TabDetails;
use IDP\Helper\Utilities\Tabs;

$registered = MoIDPUtility::micr();
$verified 	= MoIDPUtility::iclv();
$controller = MSI_DIR . 'controllers/';

$tabs = TabDetails::instance();
$tabDetails = $tabs->_tabDetails;
$parentSlug = $tabs->_parentSlug;


$profileTabDetails = $tabDetails[Tabs::PROFILE];

$settingsTabDetails = $tabDetails[Tabs::SIGN_IN_SETTINGS];

$licenseTabDetails = $tabDetails[Tabs::LICENSE];

$metadataTabDetails = $tabDetails[Tabs::METADATA];

$spSettingsTabDetails = $tabDetails[Tabs::IDP_CONFIG];

$attrMapTabDetails = $tabDetails[Tabs::ATTR_SETTINGS];

$supportSection = $tabDetails[Tabs::SUPPORT];


include MSI_DIR 	 . 'views/common-elements.php';
include MSI_DIR 	 . 'controllers/sso-idp-navbar.php';

if( isset( $_GET[ 'page' ]))
{
    $account = $registered ? 'sso-idp-profile.php' : 'sso-idp-registration.php';
    switch($_GET['page'])
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
    }
}

echo '<form name="f" method="post" id="show_pointers">
        <input type="hidden" name="option" value="clear_pointers"/>
      </form>';


