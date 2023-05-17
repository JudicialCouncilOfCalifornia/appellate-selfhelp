<?php
    $dir                = dirname(dirname(__FILE__));
    $sqlInjectionFile   = $dir.DIRECTORY_SEPARATOR.'signature'.DIRECTORY_SEPARATOR.'APSQLI.php';
    $xssFile            = $dir.DIRECTORY_SEPARATOR.'signature'.DIRECTORY_SEPARATOR.'APXSS.php';
    $lfiFile            = $dir.DIRECTORY_SEPARATOR.'signature'.DIRECTORY_SEPARATOR.'APLFI.php';
    
    $configfilepath     = dirname(dirname(dirname(dirname(__FILE__))));
    $configfile         = $configfilepath.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'miniOrange'.DIRECTORY_SEPARATOR.'mo-waf-config.php';
    $missingFile        = 0;

    if(file_exists($configfile))
    {
        include_once($configfile);
    }
    else
    {
         $missingFile   = 1;
    }
    include_once($sqlInjectionFile);
    include_once($xssFile);
    include_once($lfiFile);

    function get_ipaddress()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = sanitize_text_field($_SERVER['HTTP_X_FORWARDED']);
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = sanitize_text_field($_SERVER['HTTP_FORWARDED_FOR']);
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = sanitize_text_field($_SERVER['HTTP_FORWARDED']);
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }
    function is_crawler()
    {
        $USER_AGENT = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
        $Botsign = array('bot','apache','crawler','elinks','http', 'java', 'spider','link','fetcher','scanner','grabber','collector','capture','seo','.com');
        foreach ($Botsign as $key => $value) 
        {
            if(preg_match('/'.$value.'/', $USER_AGENT)) 
            {
                return true;
            }
        }   
        return false;
    }
    function is_fake_googlebot($ipaddress)
    {
        $USER_AGENT = sanitize_text_field($_SERVER['HTTP_USER_AGENT']);
        if(preg_match('/Googlebot/', $USER_AGENT))
        {
            if(is_fake('Googlebot',$USER_AGENT,$ipaddress))
            {
                header('HTTP/1.1 403 Forbidden');
                include_once("mo-error.php");
            }
        }
    }
    function is_fake($crawler,$USER_AGENT,$ipaddress)
    {  
       
    }
?>