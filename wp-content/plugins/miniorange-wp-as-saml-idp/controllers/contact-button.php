<?php

use IDP\Helper\Constants\MoIDPConstants;

global $_wp_admin_css_colors;

$adminColor     = get_user_option('admin_color');
$colors         = $_wp_admin_css_colors[$adminColor]->colors;

$current_user   = wp_get_current_user();
$email          = get_site_option("mo_idp_admin_email");
$phone          = get_site_option("mo_idp_admin_phone");
$phone          = $phone ? $phone : '';
$support        = MoIDPConstants::SAMLSUPPORT_EMAIL;

include MSI_DIR . 'views/contact-button.php';