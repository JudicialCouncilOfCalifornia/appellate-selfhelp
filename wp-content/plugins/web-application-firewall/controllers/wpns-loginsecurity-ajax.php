<?php
class Mowaf_ajax
{
	function __construct(){
		//add comment here
		add_action( 'admin_init'  , array( $this, 'mo_login_security_ajax' ) );
	}

	function mo_login_security_ajax(){
		 
		add_action( 'wp_ajax_wpns_login_security', array($this,'wpns_login_security') );
	}

		function wpns_login_security(){
			switch($_POST['wpns_loginsecurity_ajax'])
			{
				case "wpns_bruteforce_form":
					$this->mmp_handle_bf_configuration_form();	break;
				case "wpns_rename_loginURL":
					$this->mmp_handle_rename_login_url_configuration();break;
				case "wpns_mobile_auth":
					$this->wpns_mobile_authentication();break;
				case "wpns_save_captcha":
					$this->wpns_captcha_settings();break;			
				case 'wpns_ManualIPBlock_form':
					$this->wpns_handle_IP_blocking();break;
				case 'wpns_WhitelistIP_form':
					$this->wpns_whitelist_ip(); break;
				case 'wpns_waf_settings_form':
					$this->wpns_waf_settings_form(); break;
				case 'wpns_waf_rate_limiting_form':
					$this->wpns_waf_rate_limiting_form(); break;	
				case 'wpns_ip_lookup':
					$this->wpns_ip_lookup(); 	break;	
				case 'wpns_upgrade_button':
					$this->wpns_upgrade_button(); 	break;	
			}
		}

		function wpns_upgrade_button(){
			
			$nonce = sanitize_text_field($_POST['nonce']);
			if ( ! wp_verify_nonce( $nonce, 'wpns-upgrade-button' ) ){
				wp_send_json('ERROR');
				return;
			}

			$mo_wpns_clicks=get_site_option('wpns_upgrade_button_clicked',0);
			update_site_option('wpns_upgrade_button_clicked',$mo_wpns_clicks+1);
	
		}

	   function mmp_handle_bf_configuration_form(){

	   		$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'wpns-brute-force' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}
	   		$brute_force        = sanitize_text_field($_POST['bf_enabled/disabled']);
	  		if($brute_force == 'true'){$brute_force = "on";}else if($brute_force == 'false') {$brute_force = "";}  
			$login_attempts 	= sanitize_text_field($_POST['allwed_login_attempts']);
			$blocking_type  	= sanitize_text_field($_POST['time_of_blocking_type']);
			$blocking_value 	= isset($_POST['time_of_blocking_val'])	 ? sanitize_text_field($_POST['time_of_blocking_val'])	: false;
			$show_login_attempts= sanitize_text_field($_POST['show_remaining_attempts']);
			if($show_login_attempts == 'true'){$show_login_attempts = "on";} else if($show_login_attempts == 'false') { $show_login_attempts = "";}
			if($brute_force == 'on' && $login_attempts == "" ){
				wp_send_json('empty');
				return;
			}
	  		update_option( 'mo_wpns_enable_brute_force' 	, $brute_force 		  	  );
			update_option( 'mo_wpns_allwed_login_attempts'	, $login_attempts 		  );
			update_option( 'mo_wpns_time_of_blocking_type'	, $blocking_type 		  );
			update_option( 'mo_wpns_time_of_blocking_val' 	, $blocking_value   	  );
			update_option('mo_wpns_show_remaining_attempts' , $show_login_attempts    );
			if($brute_force == "on"){
				wp_send_json('true');
			}
			else if($brute_force == ""){
				wp_send_json('false');
			} 
			
		}
	function wpns_handle_IP_blocking()
	{
	
		global $mmp_dirName;	
		if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']),'manualIPBlockingNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{	
			include_once($mmp_dirName.'controllers'.DIRECTORY_SEPARATOR.'ip-blocking.php');
		}
	}
	function wpns_whitelist_ip()
	{
		global $mmp_dirName;
		if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']),'IPWhiteListingNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{
			include_once($mmp_dirName.'controllers'.DIRECTORY_SEPARATOR.'ip-blocking.php');
		}
	}
	function wpns_ip_lookup()
	{

		if(!wp_verify_nonce(sanitize_text_field($_POST['nonce']),'IPLookUPNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{
			$ip  = filter_var($_POST['IP']);
	        if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/',$ip))
			{
				echo("INVALID_IP_FORMAT");
				exit;
			}
			else if(! filter_var($ip, FILTER_VALIDATE_IP)){
				echo("INVALID_IP");
				exit;
			}
	        $result=@json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip),true);
			$hostname 	= gethostbyaddr($result["geoplugin_request"]);
			try{
	            $timeoffset	= timezone_offset_get(new DateTimeZone($result["geoplugin_timezone"]),new DateTime('now'));
	            $timeoffset = $timeoffset/3600;

	        }catch(Exception $e){
	            $result["geoplugin_timezone"]="";
	            $timeoffset="";
	        }
			$ipLookUpTemplate  = MowafConstants::IP_LOOKUP_TEMPLATE;
			if($result['geoplugin_request']==$ip) {

	            $ipLookUpTemplate = str_replace("{{status}}", $result["geoplugin_status"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{ip}}", $result["geoplugin_request"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{region}}", $result["geoplugin_region"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{country}}", $result["geoplugin_countryName"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{city}}", $result["geoplugin_city"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{continent}}", $result["geoplugin_continentName"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{latitude}}", $result["geoplugin_latitude"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{longitude}}", $result["geoplugin_longitude"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{timezone}}", $result["geoplugin_timezone"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{curreny_code}}", $result["geoplugin_currencyCode"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{curreny_symbol}}", $result["geoplugin_currencySymbol"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{per_dollar_value}}", $result["geoplugin_currencyConverter"], $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{hostname}}", $hostname, $ipLookUpTemplate);
	            $ipLookUpTemplate = str_replace("{{offset}}", $timeoffset, $ipLookUpTemplate);

	            $result['ipDetails'] = $ipLookUpTemplate;
	        }else{
	            $result["ipDetails"]["status"]="ERROR";
	        }
	      	wp_send_json( $result );
		}
	}
	function wpns_waf_settings_form()
	{
		$dir_name 	= dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'uploads';
		
		if(!is_writable($dir_name))
		{
			update_option('NotWritable',1);		
		}

		$dir_name  .= DIRECTORY_SEPARATOR.'miniOrange';			
		$fileName 	= $dir_name.DIRECTORY_SEPARATOR.'mo-waf-config.php';
		

		if(!file_exists($dir_name))
		{
			mkdir($dir_name, 0777, true); // Creating folder in uploads
		
			$file 	 = fopen($fileName, "a+");
			$string  = "<?php".PHP_EOL;
			$string .= '$SQL=1;'.PHP_EOL;
			$string .= '$XSS=1;'.PHP_EOL;
			$string .= '$RCE=0;'.PHP_EOL;
			$string .= '$LFI=0;'.PHP_EOL;
			$string .= '$RFI=0;'.PHP_EOL;
			$string .= '$RateLimiting=1;'.PHP_EOL;
			$string .= '$RequestsPMin=120;'.PHP_EOL;
			$string .= '$actionRateL="ThrottleIP";'.PHP_EOL;
			
			fwrite($file, $string);
			fclose($file);
		}
		
		
		if(!wp_verify_nonce($_POST['nonce'],'WAFsettingNonce'))
		{
			var_dump("NonceDidNotMatch");
			exit;
		}
		else
		{
			
			switch (sanitize_text_field($_POST['optionValue'])) {
				case "SQL": 
					$this->saveAttack($fileName,'SQLInjection','SQL'); 	break;
				case "XSS": 
					$this->saveAttack($fileName,'XSSAttack','XSS'); 	break;
				case "RCE": 
					$this->saveAttack($fileName,'RCEAttack','RCE'); 	break;
				case "RFI": 
					$this->saveAttack($fileName,'RFIAttack','RFI'); 	break;
				case "LFI": 
					$this->saveAttack($fileName,'LFIAttack','LFI'); 	break;
				case "WAF": 
					$this->saveWAF();			break;
				case "HWAF": 
					$this->saveHWAF();			break;
				case "backupHtaccess":
					$this->backupHtaccess();	break;
				case "limitAttack":
					$this->limitAttack();		break;
				default:
					break;
			}
				
		}	

	}
    function wpns_waf_rate_limiting_form()
	{
		if(!wp_verify_nonce($_POST['nonce'],'RateLimitingNonce'))
		{
			echo "NonceDidNotMatch";
			exit;
		}
		else
		{
			if(get_site_option('WAFEnabled') != 1)
			{
				echo "WAFNotEnabled";
				exit;
			}

			if(sanitize_text_field($_POST['Requests'])!='')
			{
				if(is_numeric($_POST['Requests']))
				{
				$dir_name = dirname(dirname(dirname(dirname(__FILE__))));
				$fileName = $dir_name.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'miniOrange'.DIRECTORY_SEPARATOR.'mo-waf-config.php';
				
				$file = file_get_contents($fileName);
				$data = $file;
			
				$req  =	sanitize_text_field($_POST['Requests']);
				if($req >1)
				{
					update_option('Rate_request',$req);
					if(isset($_POST['rateCheck']))
					{
						if(sanitize_text_field($_POST['rateCheck']) == 'on')
						{
							update_option('Rate_limiting','1');
							echo "RateEnabled";
							if(strpos($file, 'RateLimiting')!=false)
							{
								$file = str_replace('$RateLimiting=0;', '$RateLimiting=1;', $file);
								$data = $file;
								file_put_contents($fileName,$file);	
								
							}
							else
							{
								$file .= '$RateLimiting=1;'.PHP_EOL;
								file_put_contents($fileName,$file);
								$data = $file;
							}
						

						}
					}	
					else
					{
						update_option('Rate_limiting','0');
						echo "Ratedisabled";
						if(strpos($file, 'RateLimiting')!=false)
						{
							$file = str_replace('$RateLimiting=1;', '$RateLimiting=0;', $file);
							$data = $file;
							file_put_contents($fileName,$file);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$RateLimiting=0;'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
							$data = $file;
						}

					}				

					
					$file = $data;
					if(strpos($file, 'RequestsPMin')!=false)
					{
						$content = explode(PHP_EOL, $file);
						$con = '';
						$len =  sizeof($content);
						
						for($i=0;$i<$len;$i++)
						{
							if(strpos($content[$i], 'RequestsPMin')!=false)
							{
								$con.='$RequestsPMin='.$req.';'.PHP_EOL;
							}
							else
							{
								$con .= $content[$i].PHP_EOL;
							}
						}
					
						file_put_contents($fileName,$con);
						$data = $con;
						
					}

					else
					{
						$file .= '$RequestsPMin='.$req.';'.PHP_EOL;
						file_put_contents($fileName,$file);
						$data = $file;
					}
				
					if(sanitize_text_field($_POST['actionOnLimitE'])=='BlockIP' || sanitize_text_field($_POST['actionOnLimitE']) == 1)
					{
						update_option('actionRateL',1);

						$file = $data;
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="BlockIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$file .= '$actionRateL="BlockIP";'.PHP_EOL;
							file_put_contents($fileName,$file);
							$file = $data;
						}
					}
					else if(sanitize_text_field($_POST['actionOnLimitE'])=='ThrottleIP' || sanitize_text_field($_POST['actionOnLimitE']) == 0)
					{

						$file = $data;
						update_option('actionRateL',0);
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="ThrottleIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$file .= '$actionRateL="ThrottleIP";'.PHP_EOL;
							file_put_contents($fileName,$file);
						}	
					}

			}
			exit;
		}
		
			
			
		}
		echo("Error");
		exit;
		}
		
		
	}
	function add_mo_waf_config_cont($string,$value)
	{
		$dir_name = dirname(dirname(dirname(dirname(__FILE__))));
		$fileName = $dir_name.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'miniOrange'.DIRECTORY_SEPARATOR.'mo-waf-config.php';
		$file 	  = file_get_contents($fileName);
		if(strpos($file, $string)!=false)
		{
			$content 	= explode(PHP_EOL, $file);
			$con 		= '';
			foreach ($content as $line => $lineV) {
				if(strpos($lineV, $string)!=false)
				{
					$con.='$'.$string.'='.$value.';'.PHP_EOL;
				}
				else
				{
					$con .= $lineV.PHP_EOL;
				}
			}
			file_put_contents($fileName,$con);	
		}
		else
		{
			$file .= '$'.$string.'='.$value.';'.PHP_EOL;
			file_put_contents($fileName,$file);
		}
		$file = file_get_contents($fileName);
		$file = preg_replace('/^[ \t]*[\r\n]+/m', '', $file);
		file_put_contents($fileName, $file);		
	}

	private function saveWAF()
	{	
		if(isset($_POST['pluginWAF']))
		{
			if(sanitize_text_field($_POST['pluginWAF'])=='on')
			{
				update_option('WAF','PluginLevel');
				update_option('WAFEnabled','1');
				echo("PWAFenabled");exit;
			}
		}
		else
		{
			update_option('WAFEnabled','0');
			update_option('WAF','wafDisable');
			update_option('SQLInjection',0);
			update_option('XSSAttack',0);
			update_option('LFIAttack',0);
			$this->add_mo_waf_config_cont('SQL',0);
			$this->add_mo_waf_config_cont('XSS',0);
			$this->add_mo_waf_config_cont('LFI',0);
			echo("PWAFdisabled");exit;
		}
	}
	private function saveHWAF()
	{
		if(!function_exists('mysqli_connect'))
		{
			echo "mysqliDoesNotExit";
			exit;
		}
		if(isset($_POST['htaccessWAF']))
		{
			if(sanitize_text_field($_POST['htaccessWAF'])=='on')
			{
				
				$dir_name   = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
				$file_name  = $dir_name.DIRECTORY_SEPARATOR.'.htaccess';
			 	
				$HtaccessWrite = 1;

				if(!file_exists($file_name))
				{
					if(!is_writable($dir_name))
					{
						$HtaccessWrite = 2;
						echo "NotWritable";
					}
					$HtaccessWrite = 0;
					echo "htaccessMissing";
				}

			 	$file 	   =  file_get_contents($file_name);
			 	if(strpos($file, 'php_value auto_prepend_file')!=false)
			 	{
			 		if(strpos($file, 'miniOrange')==false)
			 		{	
			 			echo("WAFConflicts");
			 			exit;
			 		}
			 		else
			 		{
			 			update_option('WAF','HtaccessLevel');
						update_option('WAFEnabled','1');
						echo "HWAFEnabled";
						exit;
			 		}
			 	}

			 	
			 	$cont 	 = $file.PHP_EOL.'# BEGIN miniOrange WAF'.PHP_EOL;
			 	$cont 	.= 'php_value auto_prepend_file '.$dir_name.DIRECTORY_SEPARATOR.'mo-check.php'.PHP_EOL;
			 	$cont 	.= '# END miniOrange WAF';
			 	
			 	if($HtaccessWrite == 1)
			 	{	
				 	file_put_contents($file_name, $cont);
				 	update_option('WAF','HtaccessLevel');
					update_option('WAFEnabled','1');
				}
				else
				{
					update_option('HtaccessContent',$cont);
				}

				$filecontent = file_get_contents($file_name);

				$file_name = $dir_name.DIRECTORY_SEPARATOR.'mo-check.php';
			
				$file 	  = fopen($file_name, 'w+');
				$dir_name = dirname(dirname(__FILE__));
				$filepath = $dir_name.DIRECTORY_SEPARATOR.'handler'.DIRECTORY_SEPARATOR.'WAF'.DIRECTORY_SEPARATOR.'mo-waf.php';	

				$string   = '<?php'.PHP_EOL;
				$string  .= 'if(file_exists("'.$filepath.'"))'.PHP_EOL;
				$string  .= 'include_once("'.$filepath.'");'.PHP_EOL;
				$string  .= '?>';
				
				if($HtaccessWrite == 1)			
				{
					fwrite($file, $string);
					fclose($file);
				}
				else
				{
					update_option('moCheckContent',$string);
					exit;
				}
				if(strpos($filecontent,'mo-check.php')!=false)
				{
					echo "HWAFEnabled";
					exit;
				}
				else
				{
					echo "HWAFEnabledFailed";
					exit;
				}
			}
		}
		else
		{
			update_option('WAF','wafDisable');
			if(isset($_POST['pluginWAF']))
			{
				if(sanitize_text_field($_POST['pluginWAF']) == 'on')
				{
					update_option('WAFEnabled',1);
					update_option('WAF','PluginLevel');
				}
			}
			else
			{
				update_option('WAFEnabled',0);
				update_option('SQLInjection',0);
				update_option('XSSAttack',0);
				update_option('LFIAttack',0);
				$this->add_mo_waf_config_cont('SQLInjection',0);
				$this->add_mo_waf_config_cont('XSSAttack',0);
				$this->add_mo_waf_config_cont('LFIAttack',0);
		
			}
			$dir_name 	= dirname(dirname(dirname(dirname(dirname(__FILE__)))));
			$file_name  = $dir_name.DIRECTORY_SEPARATOR.'.htaccess';
		 	$file 		= file_get_contents($file_name);

		 	$cont 	 = PHP_EOL.'# BEGIN miniOrange WAF'.PHP_EOL;
		 	$cont 	.= 'php_value auto_prepend_file '.$dir_name.DIRECTORY_SEPARATOR.'mo-check.php'.PHP_EOL;
		 	$cont 	.= '# END miniOrange WAF';
		 	$file =str_replace($cont,'',$file);
			file_put_contents($file_name, $file);

			$filecontent = file_get_contents($file_name);
			
			if(strpos($filecontent,'mo-check.php')==false)
			{
				echo "HWAFdisabled";
				exit;
			}
			else
			{
				echo "HWAFdisabledFailed";
				exit;
			}
		}


	}
	private function saveAttack($fileName,$type,$name)
	{
		$file  = file_get_contents($fileName);
		$value   = empty(sanitize_text_field($_POST[$name])) ? 0 : 1;
		
		$opValue = $value == 1 ? 0 : 1;
	
		if (isset($_POST['SQL'])|| isset($_POST['XSS']) || isset($_POST['LFI']) || isset($_POST['RFI']  )) {
			
			update_option($type,1);
		}else{
			update_option($type,0);
		}
		
		if(strpos($file, $name)!=false)
		{
			$file = str_replace('$'.$name.'='.$opValue.';', '$'.$name.'='.$value.';', $file);
			file_put_contents($fileName,$file);	
		}
		else
		{
			$file .= '$'.$name.'='.$value.';'.PHP_EOL;
			file_put_contents($fileName,$file);
		}
		if($value == 1 )
		{
			echo($name."enable");
			exit;
		}
		else
		{
			echo($name."disable");
			exit;
		}

	}
	private function saveRateL()
	{
		
		if(sanitize_text_field($_POST['time'])!='' && sanitize_text_field($_POST['req'])!='')
		{
			if(is_numeric($_POST['time']) && is_numeric($_POST['req']))
			{
				$dir_name =  dirname(__FILE__);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$filepath = str_replace('\\', '/', $dir_name1[0]);
				$fileName = $filepath.'/wp-includes/mo-waf-config.php';
				
				$file = file_get_contents($fileName);
				$data = $file;
				$time = sanitize_text_field($_POST['time']);
				$req  =	sanitize_text_field($_POST['req']);
				if($time>0 && $req >0)
				{
					update_option('Rate_time',$time);
					update_option('Rate_request',$req);
					update_option('Rate_limiting','1');

					if(strpos($file, 'RateLimiting')!=false)
					{
						$file = str_replace('$RateLimiting=0;', '$RateLimiting=1;', $file);
						$data = $file;
						file_put_contents($fileName,$file);	
					}
					else
					{
						$content = explode('?>', $file);
						$file = $content[0];
						$file .= PHP_EOL;
						$file .= '$RateLimiting=1;'.PHP_EOL;
						$file .='?>';
						file_put_contents($fileName,$file);
						$data = $file;
					}
					
					$file = $data;
					if(strpos($file, 'RequestsPMin')!=false)
					{
						$content = explode(PHP_EOL, $file);
						$con = '';
						$len =  sizeof($content);
						
						for($i=0;$i<$len;$i++)
						{
							if(strpos($content[$i], 'RequestsPMin')!=false)
							{
								$con.='$RequestsPMin='.$req.';'.PHP_EOL;
							}
							else
							{
								$con .= $content[$i].PHP_EOL;
							}
						}
						
						file_put_contents($fileName,$con);
						$data = $con;
						
					}

					else
					{
						$content = explode('?>', $file);
						$file = $content[0];
						$file .= PHP_EOL;
						$file .= '$RequestsPMin='.$req.';'.PHP_EOL;
						$file .='?>';
						file_put_contents($fileName,$file);
						$data = $file;
					}
				

					
					if(sanitize_text_field($_POST['action'])=='BlockIP')
					{
						update_option('actionRateL',1);

						$file = $data;
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="BlockIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$actionRateL="BlockIP";'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
							$file = $data;
						}
					}
					elseif(sanitize_text_field($_POST['action'])=='ThrottleIP')
					{
						$file = $data;
						update_option('actionRateL',0);
						if(strpos($file, 'actionRateL')!=false)
						{
							$content = explode(PHP_EOL, $file);
							$con = '';
							foreach ($content as $line => $lineV) {
								if(strpos($lineV, 'actionRateL')!=false)
								{
									$con.='$actionRateL="ThrottleIP";'.PHP_EOL;
								}
								else
								{
									$con .= $lineV.PHP_EOL;
								}
							}
							file_put_contents($fileName,$con);	
						}
						else
						{
							$content = explode('?>', $file);
							$file = $content[0];
							$file .= PHP_EOL;
							$file .= '$actionRateL="ThrottleIP";'.PHP_EOL;
							$file .='?>';
							file_put_contents($fileName,$file);
						}	
					}

			}

		}	
			
		}

	}
	private function disableRL()
	{
		update_option('Rate_limiting',0);

		$dir_name =  dirname(__FILE__);
		$dir_name1 = explode('wp-content', $dir_name);
		$dir_name = $dir_name1[0];
		$filepath = str_replace('\\', '/', $dir_name1[0]);
		$fileName = $filepath.'/wp-includes/mo-waf-config.php';
		$file = file_get_contents($fileName);
			
		if(strpos($file, 'RateLimiting')!=false)
		{
			$file = str_replace('$RateLimiting=1;', '$RateLimiting=0;', $file);
			file_put_contents($fileName,$file);	
		}
		else
		{
			$content = explode('?>', $file);
			$file = $content[0];
			$file .= PHP_EOL;
			$file .= '$RateLimiting=0;'.PHP_EOL;
			$file .='?>';
			file_put_contents($fileName,$file);
		}

	}
	private function backupHtaccess()
	{
		if(isset($_POST['htaccessWAF']))
		{
			if(sanitize_text_field($_POST['htaccessWAF'])=='on')
			{
				$dir_name =  dirname(__FILE__);
				$dirN = $dir_name;
				$dirN = str_replace('\\', '/', $dirN);
				$dir_name1 = explode('wp-content', $dir_name);
				$dir_name = $dir_name1[0];
				$dir_name1 = str_replace('\\', '/', $dir_name1[0]);
				$dir_name =$dir_name1.'.htaccess';
			 	$file =  file_get_contents($dir_name);
				$dir_backup = $dir_name1.'htaccess';
				$handle = fopen($dir_backup, 'c+');
				fwrite($handle,$file);
			}
		}
	}
	private function limitAttack()
	{
		if(isset($_POST['limitAttack']))
		{
			$value = sanitize_text_field($_POST['limitAttack']);
			if($value>1)
			{
				update_option('limitAttack',$value);
				echo "limitSaved";
				exit;
			}
			else 
			{
				echo "limitIsLT1";
				exit;
			}

		}
	}
	function mmp_handle_rename_login_url_configuration(){

		$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'wpns-edit-url' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}
		$rename_login = sanitize_text_field($_POST['enable_rename_loginurl']);
		if($rename_login == 'true'){$rename_login = "on";}else if($rename_login == 'false') {$rename_login = "";}
		$login_url = sanitize_text_field($_POST['input_url']);
		if($login_url == "" && $rename_login =="on" ){
			wp_send_json('empty');
			return;
		}
		update_option('mo_wpns_enable_rename_login_url', $rename_login );
		if($login_url){
			update_option('login_page_url', sanitize_text_field($login_url));
		}
		else{
			update_option('login_page_url', sanitize_text_field('wp-login.php'));
		}

		if($rename_login == "on"){
			
				wp_send_json('true');
			}
			else if($rename_login == ""){
				wp_send_json('false');
			}
		
		}

	function wpns_mobile_authentication(){
		$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'wpns-mobile-auth' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}
		$enable_2fa = sanitize_text_field($_POST['mobile_auth_status']);
		if($enable_2fa == 'true'){$enable_2fa = 1;}else if($enable_2fa == 'false') {$enable_2fa = 0;}
		update_option( 'mo_wpns_enable_2fa',  $enable_2fa);
		if($enable_2fa){
			wp_send_json("true");
		}
		else{
			wp_send_json("false");
		}
	}

	function wpns_captcha_settings(){
		$nonce = sanitize_text_field($_POST['nonce']);
	   		if ( ! wp_verify_nonce( $nonce, 'wpns-captcha' ) ){
	   			wp_send_json('ERROR');
	   			return;
	   		}
		$site_key = sanitize_text_field($_POST['site_key']);
		$secret_key = sanitize_text_field($_POST['secret_key']);
		$enable_captcha = sanitize_text_field($_POST['enable_captcha']);
		if($enable_captcha == 'true'){$enable_captcha = "on";}else if($enable_captcha == 'false') {$enable_captcha = "";}
		$login_form_captcha = sanitize_text_field($_POST['login_form']);
		if($login_form_captcha == 'true'){$login_form_captcha = "on";}else if($login_form_captcha == 'false') {$login_form_captcha = "";}
		$reg_form_captcha = sanitize_text_field($_POST['registeration_form']);
		if($reg_form_captcha == 'true'){$reg_form_captcha = "on";}else if($reg_form_captcha == 'false') {$reg_form_captcha = "";}

		if(($site_key == "" || $secret_key == "")){
			wp_send_json('empty');
			return;
		} 

		update_option( 'mo_wpns_recaptcha_site_key'			 		, $site_key     );
		update_option( 'mo_wpns_recaptcha_secret_key'				, $secret_key   );
		update_option( 'mo_wpns_activate_recaptcha'			 		,  $enable_captcha );
		
		if($enable_captcha == "on"){
				update_option( 'mo_wpns_activate_recaptcha_for_login'	, $login_form_captcha );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login', $login_form_captcha );
				update_option('mo_wpns_activate_recaptcha_for_registration', $reg_form_captcha   );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration',$reg_form_captcha   );
				wp_send_json('true');
			}
			else if($enable_captcha == ""){
				update_option( 'mo_wpns_activate_recaptcha_for_login'	, '' );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_login', '' );
				update_option('mo_wpns_activate_recaptcha_for_registration', ''   );
				update_option( 'mo_wpns_activate_recaptcha_for_woocommerce_registration','' );
				wp_send_json('false');
			}
		
	}	

	
	
}
new Mowaf_ajax;

?>