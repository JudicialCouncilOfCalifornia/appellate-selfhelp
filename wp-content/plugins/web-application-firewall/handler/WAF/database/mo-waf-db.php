<?php
	global $dbcon,$prefix;
    function log_attack($ipaddress,$value1,$value)
    {
        global $prefix,$dbcon;
        $value      = htmlspecialchars($value);
        $query      = 'insert into '.$prefix.'wpns_attack_logs values ("'.esc_attr($ipaddress).'","'.esc_attr($value1).'",'.time().',"'.esc_attr($value).'");';
        $results    = mysqli_query($dbcon,$query);
        $query      = "select count(*) from ".$prefix."wpns_attack_logs where ip='".esc_attr($ipaddress)."' and input != 'RLE';";
        $results    = mysqli_query($dbcon,$query);
        $rows       = mysqli_fetch_array($results);
        return $rows['count(*)'];
    }
   
    function setting_file()
    {
        global $prefix,$dbcon;
        $dir_name    = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
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
            $file   = fopen($fileName, "a+");
            $string = "<?php".PHP_EOL;

            $sqlInjection = get_option_value("SQLInjection");
            $sqlInjection = empty($sqlInjection) ?  1 : $sqlInjection;
            $string      .= '$SQL='.$sqlInjection.';'.PHP_EOL;

            $XSSAttack    = get_option_value("XSSAttack");
            $XSSAttack    = empty($XSSAttack) ?  1 : $XSSAttack;
            $string      .= '$XSS='.$XSSAttack.';'.PHP_EOL;
            
            $RFIAttack    = get_option_value("RFIAttack");
            $RFIAttack    = empty($RFIAttack) ?  0 : $RFIAttack;
            $string      .= '$RFI='.$RFIAttack.';'.PHP_EOL;

            $LFIAttack    = get_option_value("LFIAttack");
            $LFIAttack    = empty($LFIAttack) ?  0 : $LFIAttack;           
            $string      .= '$LFI='.$LFIAttack.';'.PHP_EOL;
            
            $RCEAttack    = get_option_value("RCEAttack");
            $RCEAttack    = empty($RCEAttack) ?  0 : $RCEAttack;           
            $string      .= '$RCE='.$RCEAttack.';'.PHP_EOL;

            $Rate_limiting = empty($Rate_limiting) ?  0 : $Rate_limiting;           
            $Rate_limiting = get_option_value("Rate_limiting");
            if($Rate_limiting!='')
                $string .= '$RateLimiting='.esc_attr($Rate_limiting).';'.PHP_EOL;
            else
                $string .= '$RateLimiting=0;'.PHP_EOL;

            $Rate_request = get_option_value("Rate_request");
            $Rate_request = empty($Rate_request) ?  240 : $Rate_request;           
           
            $string .= '$RequestsPMin='.$Rate_request.';'.PHP_EOL;
           
            $actionRateL = get_option_value("actionRateL");
            $actionRateL = empty($actionRateL) ? 0 : $actionRateL; 
            if($actionRateL== 0)
                $string .= '$actionRateL="ThrottleIP";'.PHP_EOL;
            else
                $string .= '$actionRateL="BlockIP";'.PHP_EOL;

            fwrite($file, $string);
            fclose($file); 
        }
        return $fileName;
    }
    function is_ip_whitelisted($ipaddress)
    {   
        global $dbcon,$prefix;
        $query      = 'select * from '.$prefix.'wpns_whitelisted_ips where ip_address="'.esc_attr($ipaddress).'";';
        $results    = mysqli_query($dbcon,$query);
        if($results)
        {
            $row = mysqli_fetch_array($results);
            if(is_null($row))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return false;   
    }
    function is_ip_blocked($ipaddress)
    {
        global $dbcon,$prefix;
        $query =  'select * from '.$prefix.'wpns_blocked_ips where ip_address="'.esc_attr($ipaddress).'";';
        $results = mysqli_query($dbcon,$query);
        if($results)
        {
            $row = mysqli_fetch_array($results);
            if(is_null($row))
            {
                return false;
            }
            else
            {
                return true;
            }
        }
        return false;       
    }
    function block_ip($ipaddress,$reason)
    {
        global $dbcon, $prefix;
        $query ="insert into ".$prefix."wpns_blocked_ips values(NULL,'".$ipaddress."','".esc_attr($reason)."',NULL,".time().");";
        $results = mysqli_query($dbcon,$query);
    }
    function dbconnection()
    {
        global $dbcon,$prefix;
        $dir = dirname(__FILE__);
        $dir = str_replace('\\', "/", $dir);
        $dir_name = explode('wp-content', $dir);    
        $file = file_get_contents($dir_name[0].'wp-config.php');
        $content =  explode("\n", $file);
        $len = sizeof($content);
        $Ismultisite    = 0;
        $dbD = array('DB_NAME' =>'' ,'DB_USER' => '' ,'DB_PASSWORD' =>'','DB_HOST' =>'','DB_CHARSET' =>'','DB_COLLATE' =>'' );
        
        $prefix = 'wp_';

        for($i=0;$i<$len;$i++)
        {   

            if(preg_match("/define/", $content[$i])) 
            {
                 $cont = explode(",", $content[$i]);
                 $string = str_replace(array('define(',' ','\''), '', $cont[0]);
                 switch ($string) {
                    case "DB_NAME":
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_NAME'] = $res;
                        break;
                    case 'DB_USER':
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_USER'] = $res;
                        break;
                    case "DB_PASSWORD":
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_PASSWORD'] = $res;
                        break;
                    case 'DB_HOST':
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_HOST'] = $res;
                        break;
                    case "DB_CHARSET":
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_CHARSET'] = $res;
                        break;
                    case 'DB_COLLATE':
                        $res = str_replace(array('\'',')',';',' '), '', $cont[1]);
                        $res = preg_replace('/\s/', '', $res);
                        $dbD['DB_COLLATE'] = $res;
                        break;
                    default:
                    
                        break;
                 }
            }
            if(preg_match('/\$table_prefix/', $content[$i]))
            {
                $cont = explode("'", $content[$i]);

                $prefix = $cont['1'];
            }
        }
        $dbcon = new mysqli($dbD['DB_HOST'],$dbD['DB_USER'],$dbD['DB_PASSWORD']);
        if(!$dbcon)
        {
            echo "database connection error";
            exit;
        }
        $connection = mysqli_select_db($dbcon,$dbD['DB_NAME']);
        return $connection;
    }
    function get_option_value($option)
    {   
        global $dbcon,$prefix;
        $query          = 'select option_value from '.$prefix.'options where option_name ="'.esc_attr($option).'";';
        $results        = mysqli_query($dbcon,$query);
        if($results)
        {
            $rows           = mysqli_fetch_array($results);
            if(!is_null($rows['option_value']))
            {
                $option_value   = intval($rows['option_value']);  
                return $option_value;
            }
        }
        return '';
    }
    
    function getRLEattack($ipaddress)
    {
        global $dbcon,$prefix;
        $query = "select time from ".$prefix."wpns_attack_logs where ip ='".esc_attr($ipaddress)."' and type = 'RLE' ORDER BY time DESC LIMIT 1;";
        $results = mysqli_query($dbcon,$query);
        if($results)
        {
            $results = mysqli_fetch_array($results);
            return $results['time'];
        }
        return 0;
    }
    function CheckRate($ipaddress)
    {
        global $dbcon,$prefix;
        $time       = 60;
        clearRate($time);
        insertRate($ipaddress);
        $query = "select count(*) from ".$prefix."wpns_ip_rate_details where ip='".esc_attr($ipaddress)."';";
        $results = mysqli_query($dbcon,$query);

        if($results)
        {
            $row = mysqli_fetch_array($results);
            return $row['count(*)'];
        }
        return 0;
    }
    function clearRate($time)
    {
        global $dbcon,$prefix;
        $query = "delete from ".$prefix."wpns_ip_rate_details where time<".(time()-$time);
        $results = mysqli_query($dbcon,$query);
    }
    function insertRate($ipaddress)
    {
        global $dbcon,$prefix;
        $query = "insert into ".$prefix."wpns_ip_rate_details values('".esc_attr($ipaddress)."',".time().");";
        $results = mysqli_query($dbcon,$query);
    }
    
?>