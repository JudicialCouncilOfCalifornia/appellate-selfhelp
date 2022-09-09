<?php

    
	global $dbIDPQueries;
	$sp_list 				= $dbIDPQueries->get_sp_list();
	$disabled				= !$registered || !$verified ? "" : NULL;
	$sp                     = empty($sp_list) ? '' : $sp_list[0];

	include MSI_DIR . 'views/attr-settings.php';