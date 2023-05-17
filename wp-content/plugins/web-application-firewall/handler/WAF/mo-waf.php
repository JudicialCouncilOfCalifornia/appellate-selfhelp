<?php
	$dir        = dirname(__FILE__);   
    $wafInclude = $dir.DIRECTORY_SEPARATOR.'waf-include.php';
    $wafdb      = $dir.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'mo-waf-db.php';
    $errorPage  = $dir.DIRECTORY_SEPARATOR.'mo-error.php';
    $blockPage  = $dir.DIRECTORY_SEPARATOR.'mo-block.php';

    include_once($wafInclude);
    include_once($wafdb);

    global $dbcon,$prefix;	
    $connection = dbconnection();
    if($connection)
	{
        $wafLevel = esc_attr(get_option_value('WAF'));
        if($wafLevel=='HtaccessLevel')
        {
            $ipaddress = get_ipaddress();
            if(is_ip_blocked($ipaddress))
            {
                if(!is_ip_whitelisted($ipaddress))
                {
                    header('HTTP/1.1 403 Forbidden');
                    include_once($blockPage);
                }
            }
            $fileName = setting_file();

            $SQL=0;
            $XSS=0;
            $RCE=0;
            $LFI=0;
            $RFI=0;
            $RateLimiting=0;
            $RequestsPMin=240;
            $actionRateL="ThrottleIP";

            if($fileName == 'permissionDenied')
            {
                $SQL = get_option_value("SQLInjection");
                $SQL = empty($SQL) ? 1 : $SQL;

                $XSS = get_option_value("XSSAttack");
                $XSS = empty($XSS) ? 1 : $XSS;
                
                $LFI = get_option_value("LFIAttack");
                $LFI = empty($LFI) ? 0 : $LFI;
                
                $RFI = get_option_value("RFIAttack");
                $RFI = empty($RFI) ? 0 : $RFI;
                
                $RCE = get_option_value("RCEAttack");
                $RCE = empty($RCE) ? 0 : $RCE;

                $RateLimiting = get_option_value("Rate_limiting");
                $RateLimiting = empty($RateLimiting) ? 0 : $RateLimiting;

                $RequestsPMin = get_option_value("Rate_request");
                $RequestsPMin = empty($RequestsPMin) ?  240 : $RequestsPMin;           
                
                $actionRateL = get_option_value("actionRateL");
                $actionRateL = empty($actionRateL) ? 0 : $actionRateL; 
                if($actionRateL== 0)
                    $actionRateL="ThrottleIP";
                else
                    $actionRateL="BlockIP";
            
            }
            else
                include_once($fileName);
            
            if(isset($RateLimiting) && $RateLimiting == 1)
            {
                if(!is_crawler())
                {
                    if(isset($RequestsPMin) && isset($actionRateL))
                        mo_mmp_applyRateLimiting($RequestsPMin,$actionRateL,$ipaddress,$errorPage);
                }
            }
            if(isset($RateLimCrawler) && $RateLimCrawler == 1)
            {
                if(is_crawler())
                {
                    if(is_fake_googlebot($ipaddress))
                    {
                        header('HTTP/1.1 403 Forbidden');
                        include_once($errorPage);
                    }
                    if($RateLimitingCrawler == '1')
                    {
                        mo_mmp_applyRateLimitingCrawler($ipaddress,$fileName,$errorPage); 
                    }

                }
            }
            $attack = array();
            if(isset($SQL) && $SQL==1)
            {
                array_push($attack,"SQL");
            }
            if(isset($XSS) && $XSS==1)
            {
                array_push($attack,"XSS");
            }
            if(isset($LFI) && $LFI==1)
            {
                array_push($attack,"LFI");
            }
			
            $attackC        = $attack;
            $ParanoiaLevel  = 1;
            $annomalyS      = 0;
            $SQLScore       = 0;
            $XSSScore       = 0;
            $limitAttack    = esc_attr (get_option_value("limitAttack"));

            foreach ($attackC as $key1 => $value1)
            {
                for($lev=1;$lev<=$ParanoiaLevel;$lev++)
                {
                    if(isset($regex[$value1][$lev]))
                    {	$ooo = 0;
                        for($i=0;$i<sizeof($regex[$value1][$lev]);$i++)
                        {
                            foreach (esc_attr($_REQUEST) as $key => $value) {

                                if($regex[$value1][$lev][$i] != "")
                                {
                                    if(is_string($value))
                                    {
                                        if(preg_match($regex[$value1][$lev][$i], $value))
                                        {
                                           
                                            if($value1 == "SQL")
                                            {
                                                $SQLScore += $score[$value1][$lev][$i];
                                            }
                                            elseif ($value1 == "XSS")
                                            {
                                                $XSSScore += $score[$value1][$lev][$i];
                                            }
                                            else
                                            {
                                                $annomalyS += $score[$value1][$lev][$i];
                                            }

                                            if($annomalyS>=5 || $SQLScore>=10 || $XSSScore >=10)
                                            {
                                                $attackCount = log_attack($ipaddress,$value1,$value);
                                                if($attackCount>$limitAttack)
                                                {
                                                    if(!is_ip_whitelisted($ipaddress))
                                                    {
                                                        block_ip($ipaddress,'ALE');         //Attack Limit Exceed
                                                    }
                                                }

                                                header('HTTP/1.1 403 Forbidden');
                                                include_once($errorPage);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    
    function mo_mmp_applyRateLimiting($reqLimit,$action,$ipaddress,$errorPage)
    {
        global $dbcon, $prefix;
        $rate = CheckRate($ipaddress);
        if($rate>$reqLimit)
        {
            $lastAttack     = getRLEattack($ipaddress)+60;
            $current_time   = time();
            if($current_time > $lastAttack)
            {
                log_attack($ipaddress,'RLE','RLE');
            }
            if($action != 'ThrottleIP')
            {
               if(!is_ip_whitelisted($ipaddress))
                {
                    block_ip($ipaddress,'RLE');     //Rate Limit Exceed
                }
            }
            header('HTTP/1.1 403 Forbidden');
            include_once($errorPage);        
        }
    }
    
    function mo_mmp_applyRateLimitingCrawler($ipaddress,$filename,$errorPage)
    {
        if(file_exists($filename))
        {
            include($filename);
        }
        global $dbcon,$prefix;
        $USER_AGENT = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
        if(isset($RateLimitingCrawler))
        {
            if(isset($RateLimitingCrawler) && $RateLimitingCrawler=='1')
            {
                if(isset($RequestsPMinCrawler) && isset($actionRateLCrawler) )
                {
                    $reqLimit   = $RequestsPMinCrawler;
                    $rate       = CheckRate($ipaddress);
                    if($rate>=$reqLimit)
                    {
                        $action         = $actionRateLCrawler;
                        $lastAttack     = getRLEattack($ipaddress)+60;
                        $current_time   = time();
                        if($current_time>$lastAttack)
                        {
                            log_attack($ipaddress,'RLECrawler',$USER_AGENT);
                        }
                        if($action != 'ThrottleIP')
                        {
                           if(!is_ip_whitelisted($ipaddress))
                            {
                                block_ip($ipaddress,'RLECrawler');      //Rate Limit Exceed for Crawler
                            }
                        }
                        header('HTTP/1.1 403 Forbidden');
                        include_once($errorPage);
                    } 
                }
            } 
        }
    }

	
	$dbcon->close();
?>