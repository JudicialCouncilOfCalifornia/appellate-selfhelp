<?php

/*
Plugin Name: Login using WordPress Users
Plugin URI: http://miniorange.com/
Description: Convert your WordPress into an IDP.
Version: 1.11.1
Author: miniOrange
Author URI: http://miniorange.com/
*/

if(! defined( 'ABSPATH' )) exit;
define('MSI_PLUGIN_NAME', plugin_basename(__FILE__));
$dirName = substr(MSI_PLUGIN_NAME,0,strpos(MSI_PLUGIN_NAME,"/"));
define('MSI_NAME',$dirName);
include 'autoload.php';
\IDP\MoIDP::instance();
