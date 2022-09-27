<?php

use IDP\Helper\Utilities\TabDetails;
use IDP\Helper\Utilities\Tabs;
use IDP\SplClassLoader;

define('MSI_VERSION', '1.14.2');
define('MSI_DB_VERSION', '1.4');
define('MSI_DIR', plugin_dir_path(__FILE__));
define('MSI_URL', plugin_dir_url(__FILE__));
define('MSI_CSS_URL', MSI_URL . 'includes/css/mo_idp_style.min.css?version='.MSI_VERSION);
define('MSI_JS_URL', MSI_URL . 'includes/js/settings.min.js?version='.MSI_VERSION);
define('MSI_PRICING_JS_URL', MSI_URL . 'includes/js/pricing.min.js?version='.MSI_VERSION);
define('MSI_ICON', MSI_URL . 'includes/images/miniorange_icon.png');
define('MSI_LOGO_URL', MSI_URL . 'includes/images/logo.png');
define('MSI_LOADER', MSI_URL . 'includes/images/loader.gif');
define('MSI_TEST', FALSE);
define('MSI_DEBUG', FALSE);
define('MSI_LK_DEBUG', FALSE);
define('MSI_LOCK',MSI_URL.'includes/images/lock.png');

includeLibFiles();

function includeLibFiles()
{
    if(!class_exists("RobRichards\XMLSecLibs\XMLSecurityKey")) include 'helper/common/XMLSecurityKey.php';
    if(!class_exists("RobRichards\XMLSecLibs\XMLSecEnc")) include 'helper/common/XMLSecEnc.php';
    if(!class_exists("RobRichards\XMLSecLibs\XMLSecurityDSig")) include 'helper/common/XMLSecurityDSig.php';
    if(!class_exists("RobRichards\XMLSecLibs\Utils\XPath")) include 'helper/common/Utils/XPath.php';
}

function getRegistrationURL()
{
    return add_query_arg(
        [ 'page' => TabDetails::instance()->_tabDetails[Tabs::PROFILE]->_menuSlug ],
        esc_url_raw($_SERVER['REQUEST_URI'])
    );
}

include "SplClassLoader.php";
/** @var SplClassLoader $idpClassLoader */
$idpClassLoader = new SplClassLoader('IDP', realpath(__DIR__ . DIRECTORY_SEPARATOR . '..'));
$idpClassLoader->register();


