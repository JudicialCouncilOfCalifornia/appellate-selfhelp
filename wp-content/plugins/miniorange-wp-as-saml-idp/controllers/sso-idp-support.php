<?php

	$current_user 	= wp_get_current_user();
	$email 			= get_site_option("mo_idp_admin_email");
	$phone 			= get_site_option("mo_idp_admin_phone");

	include MSI_DIR . 'views/idp-support.php';