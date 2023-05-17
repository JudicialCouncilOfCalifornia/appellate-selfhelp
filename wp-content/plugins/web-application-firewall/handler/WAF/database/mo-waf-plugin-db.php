<?php
	function setting_file()
	{
		global $prefix,$dbcon;
        $dir_name    = dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))));
        $uploadsF    = $dir_name.DIRECTORY_SEPARATOR.'uploads';
        $fileName    = $dir_name.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'miniOrange'.DIRECTORY_SEPARATOR.'mo-waf-config.php';
        $missingFile = 0;
        if(!file_exists($fileName))
        {
            $missingFile = 1;
        }
        if($missingFile==1)
        {
        	if(!is_writable($uploadsF))
            {
                return 'permissionDenied';
            }
           
        	$file 	= fopen($fileName, "a+");
			$string = "<?php".PHP_EOL;
			$sqlI 	= empty(get_option("SQLInjection")) ? 1 : get_option("SQLInjection");
			
			$string	.= '$SQL='.esc_attr($sqlI).';'.PHP_EOL;

			$xssA 	= empty(get_option("XSSAttack")) ? 1 : get_option("XSSAttack");
			$string .= '$XSS='.esc_attr($xssA).';'.PHP_EOL;
			
			$rfiA 	= empty(get_option("RFIAttack")) ? 0 : get_option("RFIAttack");
			$string .= '$RFI='.esc_attr($rfiA).';'.PHP_EOL;
		
			$lfiA 	= empty(get_option("LFIAttack")) ? 0 : get_option("LFIAttack");
			$string .= '$LFI='.esc_attr($lfiA).';'.PHP_EOL;
			
			$rceA 	= empty(get_option("RCEAttack")) ? 0 : get_option("RCEAttack");
			$string .= '$RCE='.esc_attr($rceA).';'.PHP_EOL;

			$rleA 	= empty(get_option("Rate_limiting")) ? 0 : get_option("Rate_limiting");
			$string .= '$RateLimiting='.esc_attr($rleA).';'.PHP_EOL;

			$req 	= empty(get_option("Rate_request")) ? 240 : get_option("Rate_request");
			$string .= '$RequestsPMin='.esc_attr($req).';'.PHP_EOL;

			$action = empty(get_option("actionRateL")) ? 0 : get_option("actionRateL");

			if($action == 0)
				$string .= '$actionRateL="ThrottleIP";'.PHP_EOL;
			else
				$string .= '$actionRateL="BlockIP";'.PHP_EOL;
		
			fwrite($file, $string);
			fclose($file);
			
        }
        return $fileName;

	}
	
	function getRLEAttack($ipaddress)
	{
		global $wpdb;
		$query 	 = "select time from ".esc_attr($wpdb->base_prefix)."wpns_attack_logs where ip ='".esc_attr($ipaddress)."' ORDER BY time DESC LIMIT 1;";
		$results = $wpdb->get_results($query);
		if(!empty($results))
			return $results[0]->time;
		return 0;
	}
	
	function log_attack($ipaddress,$value1,$value)
    {
        global $wpdb;
        $value      = htmlspecialchars($value);
        $query      = 'insert into '.esc_attr($wpdb->base_prefix).'wpns_attack_logs values ("'.esc_attr($ipaddress).'","'.esc_attr($value1).'",'.time().',"'.esc_attr($value).'");';
        $results 	= $wpdb->get_results($query);
		$query      = "select count(*) as count from ".esc_attr($wpdb->base_prefix)."wpns_attack_logs where ip='".esc_attr($ipaddress)."' and input != 'RLE';";
        $results 	= $wpdb->get_results($query);
        return $results[0]->count;
    }
   
	function CheckRate($ipaddress)
	{
		global $wpdb;
		$time 		= 60;
		clearRate($time);
        insertRate($ipaddress);
	    $query = "select count(*) as count from ".esc_attr($wpdb->base_prefix)."wpns_ip_rate_details where ip='".esc_attr($ipaddress)."';";
		$results = $wpdb->get_results($query);

	    if(isset($results[0]->count))
	    {
	    	return $results[0]->count;
	    }
	    return 0;
	    
	}
	function clearRate($time)
	{
		global $wpdb;
		$query = "delete from ".esc_attr($wpdb->base_prefix)."wpns_ip_rate_details where time<".(time()-$time);
	    $results = $wpdb->get_results($query);
	}
	function insertRate($ipaddress)
	{
		global $wpdb;
		$query = "insert into ".$wpdb->base_prefix."wpns_ip_rate_details values('".esc_attr($ipaddress)."',".time().");";
		$results = $wpdb->get_results($query);
	}

?>