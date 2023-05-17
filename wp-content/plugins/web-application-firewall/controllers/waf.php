<?php
    global $MowafUtility,$mmp_dirName;
	$mo_wpns_handler 	= new MowafHandler();
	$sqlC 			= $mo_wpns_handler->get_blocked_attacks_count("SQL");
	$rceC 			= $mo_wpns_handler->get_blocked_attacks_count("RCE");
	$rfiC 			= $mo_wpns_handler->get_blocked_attacks_count("RFI");
	$lfiC 			= $mo_wpns_handler->get_blocked_attacks_count("LFI");
	$xssC 			= $mo_wpns_handler->get_blocked_attacks_count("XSS");
	$totalAttacks	= $sqlC+$lfiC+$rfiC+$xssC+$rceC;
	$manualBlocks 	= $mo_wpns_handler->get_manual_blocked_ip_count();
	$realTime		= 0;
	$countryBlocked = $mo_wpns_handler->get_blocked_countries();
	$IPblockedByWAF = $mo_wpns_handler->get_blocked_ip_waf();
	$totalIPBlocked = $manualBlocks+$realTime+$IPblockedByWAF;
	$mo_waf 		= get_site_option('WAFEnabled');
	if($mo_waf)
	{
		$mo_waf = false;
	}
	else
	{
		$mo_waf = true;	
	}

	$img_loader_url	= plugins_url('Web-Application-Firewall/includes/images/loader.gif');
	
	if($totalIPBlocked>999)
	{
		$totalIPBlocked = strval(intval($totalIPBlocked/1000)).'k+';
	}
	
	if($totalAttacks>999)
	{
		$totalAttacks = strval(intval($totalAttacks/1000)).'k+';
	}
	

    include $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'waf.php';
    



