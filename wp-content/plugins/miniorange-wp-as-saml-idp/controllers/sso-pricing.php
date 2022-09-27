<?php

	use IDP\Helper\Constants\MoIDPConstants;

    wp_enqueue_script( 'mo_idp_pricing_script', MSI_PRICING_JS_URL, array('jquery') );

	$disabled    	= !$registered ? 'disabled' : '';
	$hostname    	= MoIDPConstants::HOSTNAME;
	$login_url   	= $hostname . '/moas/login';
	$username    	= get_site_option('mo_idp_admin_email');
	$payment_url 	= $hostname . '/moas/initializepayment';

	$okgotit_url	= 'admin.php?page='.$spSettingsTabDetails->_menuSlug;

	$amount = '0';

    $basic_features = [
        "<span class='available'></span>&nbsp;Unlimited Authentications with Multiple SPs",
        "<span class='available'></span>&nbsp;SAML SP and IDP initiated login",
        "<span class='available'></span>&nbsp;WS-FED SP initiated login",
        "<span class='unavailable'></span>&nbsp;JWT IDP initiated login",
        "<span class='unavailable'></span>&nbsp;Customized Role Mapping",
        "<span class='unavailable'></span>&nbsp;Customized Attribute Mapping",
        "<span class='unavailable'></span>&nbsp;Signed Assertion",
        "<span class='unavailable'></span>&nbsp;Signed Response",
        "<span class='unavailable'></span>&nbsp;Encrypted Assertion",
        "<span class='unavailable'></span>&nbsp;HTTP-POST Binding",
        "<span class='unavailable'></span>&nbsp;Metadata XML File",
        "<span class='unavailable'></span>&nbsp;Single Logout",
        "<span class='unavailable'></span>&nbsp;End to End Configuration **"
    ];

    $premium_features = [
        "<span class='available'></span>&nbsp;Unlimited Authentications with Multiple SPs",
        "<span class='available'></span>&nbsp;SAML SP and IDP initiated login",
        "<span class='available'></span>&nbsp;WS-FED SP initiated login",
        "<span class='available'></span>&nbsp;JWT IDP initiated login",
        "<span class='available'></span>&nbsp;Customized Role Mapping",
        "<span class='available'></span>&nbsp;Customized Attribute Mapping",
        "<span class='available'></span>&nbsp;Signed Assertion",
        "<span class='available'></span>&nbsp;Signed Response",
        "<span class='available'></span>&nbsp;Encrypted Assertion",
        "<span class='available'></span>&nbsp;HTTP-POST Binding",
        "<span class='available'></span>&nbsp;Metadata XML File",
        "<span class='available'></span>&nbsp;Single Logout",
        "<span class='available'></span>&nbsp;End to End Configuration **"
    ];

	$sp_pricing = [
	    '1' => '$50',
        '2' => '$100',
        '3' => '$150',
        '4' => '$200',
        '5' => '$250',
        '10' => '$400',
        '15' => '$525',
        '20' => '$600',
    ];

    $user_pricing = [
        "Upto 100"      =>  "$99",
        "Upto 200"      =>  "$199",
        "Upto 300"      =>  "$299",
        "Upto 400"      =>  "$349",
        "Upto 500"      =>  "$399",
        "Upto 600"      =>  "$449",
        "Upto 700"      =>  "$499",
        "Upto 800"      =>  "$549",
        "Upto 900"      =>  "$599",
        "Upto 1000"     =>  "$649",
        "Upto 1500"     =>  "$749",
        "Upto 2000"     =>  "$849",
        "Upto 2500"     =>  "$949",
        "Upto 3000"     =>  "$1049",
        "Upto 3500"     =>  "$1149",
        "Upto 4000"     =>  "$1249",
        "Upto 4500"     =>  "$1349",
        "Upto 5000"     =>  "$1449",
        "Above 5000"    =>  "Contact Us"
    ];


    $options = $userOptions= '';

    foreach ($sp_pricing as $key => $value):
        $options .= '<option>' . $key . ' - ' . $value . '</option>';
    endforeach;


    foreach ($user_pricing as $key => $value):
        $userOptions .= '<option>' . $key . ' - ' . $value . '</option>';
    endforeach;

	include MSI_DIR . 'views/pricing.php';