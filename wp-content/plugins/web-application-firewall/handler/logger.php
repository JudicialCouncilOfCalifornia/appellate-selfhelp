<?php

class Mowaf_Logger
{
	function __construct()
	{
		add_action( 'log_403' , array( $this, 'log_403' ) );
		add_action( 'template_redirect', array( $this, 'log_404' ) );
	}	


	function log_403()
	{
		global $MowafUtility;
			$mo_wpns_config = new MowafHandler();
			$userIp 		= $MowafUtility->get_client_ip();
			$url			= $MowafUtility->get_current_url();
			$user  			= wp_get_current_user();
			$username		= is_user_logged_in() ? $user->user_login : 'GUEST';
			$mo_wpns_config->add_transactions($userIp,$username,MowafConstants::ERR_403, MowafConstants::ACCESS_DENIED,$url);
	}

	function log_404()
	{
		global $MowafUtility;

		if(!is_404())
			return;
			$mo_wpns_config = new MowafHandler();
			$userIp 		= $MowafUtility->get_client_ip();
			$url			= $MowafUtility->get_current_url();
			$user  			= wp_get_current_user();
			$username		= is_user_logged_in() ? $user->user_login : 'GUEST';
			$mo_wpns_config->add_transactions($userIp,$username,MowafConstants::ERR_404, MowafConstants::ACCESS_DENIED,$url);
	}
}
new Mowaf_Logger;