<?php

use IDP\Helper\Utilities\MoIDPUtility;

	$current_user 	= wp_get_current_user();
	$email 			= get_site_option("mo_idp_admin_email");
	$phone 			= get_site_option("mo_idp_admin_phone");

	$REQUESTS		= MoIDPUtility::sanitizeAssociateArray($_REQUEST);

	if( array_key_exists('plan_name', $REQUESTS) )
	{
		if($REQUESTS['plan_name'] == 'lite_monthly')
		{
			$plan = "mo_lite_monthly";
			$plan_desc = "LITE PLAN - Monthly";
			$users = "5000+";
		}
		else if($REQUESTS['plan_name'] == 'lite_yearly')
		{
			$plan = "mo_lite_yearly";
			$plan_desc = "LITE PLAN - Yearly";
			$users = "5000+";
		}
		else if($REQUESTS['plan_name'] == 'wp_yearly')
		{
			$plan = "mo_wp_yearly";
			$plan_desc = "PREMIUM PLAN - Yearly";
			if($REQUESTS['plan_users'] == '5K')
				$users = "5000+";
			else
				$users = "Unlimited";
		}
		else if($REQUESTS['plan_name'] == 'all_inclusive')
		{
			$plan = "mo_all_inclusive";
			$plan_desc = "All Inclusive Plan";
			$users = "";
		}	
	}

	if(isset($plan) || isset($users))
	{
		$request_quote = "Any Special Requirements: ";
	}
	else
	{
		$request_quote = "";
	}

	include MSI_DIR . 'views/idp-support.php';