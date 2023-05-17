<?php

	global $MowafUtility,$mmp_dirName;

	$controller = $mmp_dirName . 'controllers'.DIRECTORY_SEPARATOR;

	include $controller 	 . 'navbar.php';

	if( isset( $_GET[ 'page' ])) 
	{
		switch(sanitize_text_field($_GET['page']))
		{
			case 'mo_mmp_dashboard':
                include $controller . 'dashboard.php';			    break;
            case 'mo_mmp_login_and_spam':
				include $controller . 'login-spam.php';				break;
			case 'default':
				include $controller . 'login-security.php';			break;
			case 'mo_mmp_account':
				include $controller . 'account.php';				break;		
			case 'mo_mmp_backup':
				include $controller . 'backup.php'; 				break;
			case 'mo_mmp_upgrade':
				include $controller . 'upgrade.php';                break;
			case 'mo_mmp_waf':
				include $controller . 'waf.php';		    		break;
			case 'mo_mmp_blockedips':
				include $controller . 'ip-blocking.php';			break;
			case 'mo_mmp_advancedblocking':
				include $controller . 'advanced-blocking.php';		break;
			case 'mo_mmp_notifications':
				include $controller . 'notification-settings.php';	break;
			case 'mo_mmp_reports':
				include $controller . 'reports.php';				break;
			case 'mo_mmp_licencing':
				include $controller . 'licensing.php';				break;
			case 'mo_mmp_troubleshooting':
				include $controller . 'troubleshooting.php';		break;
			case 'mo_mmp_malwarescan':
				include $controller . 'malware_scanner'.DIRECTORY_SEPARATOR.'scan_malware.php';			break;
		}
	}

	if(isset($_GET[ 'page' ]) && sanitize_text_field( $_GET[ 'page' ] )!='mo_mmp_upgrade')
	include $controller . 'support.php';
