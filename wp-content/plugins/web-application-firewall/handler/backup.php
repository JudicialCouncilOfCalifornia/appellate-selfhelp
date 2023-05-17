<?php

class Mowaf_site_backup

{

	function __construct()
	{
		add_filter( 'cron_schedules', array($this,'db_backup_interval'));
		add_action( 'bl_cron_hook', array($this,'db_cron_backup') );
		add_filter( 'cron_schedules', array($this,'file_backup_interval'));
		add_action( 'file_cron_hook', array($this,'file_cron_backup') );
	}
    
    function db_cron_backup(){
		
			$obj = new Mowaf_AjaxHandler;
			$obj->backupDB();
		
    }

    function db_backup_interval($schedules){
		$mo2f_cron_hours = get_option('mo2f_cron_hours');
		$schedules['db_backup_time'] = array(
			'interval' => $mo2f_cron_hours,
			'display'  => esc_html__( 'Cron Activated' ),
		);
	 
		return $schedules;
    }

	function bl_deactivate() {
		$timestamp = wp_next_scheduled( 'bl_cron_hook' );
		wp_unschedule_event( $timestamp, 'bl_cron_hook' );
	}

	function file_cron_backup(){
	    
			if(get_option('mo_file_backup_plugins') =='1'){
				$folderName = $this->mkdirectory('plugins');
	            $real_path=get_home_path().'wp-content/plugins';
				$filename = 'plugins-backup-'.time().'.zip';
	            $this->file_backup($real_path,$filename,$folderName,'plugins');
			}if(get_option('mo_file_backup_themes')=='1'){
	            $folderName = $this->mkdirectory('themes');
				$real_path=get_home_path().'wp-content/themes';
				$filename = 'themes-backup-'.time().'.zip';
				$this->file_backup($real_path,$filename, $folderName, 'themes');
			}if(get_option('mo_file_backup_wp_files') == '1'){
				$folderName = $this->mkdirectory('wp_files');
				$real_path=get_home_path();
				$filename = 'wp-files-backup-'.time().'.zip';
				$this->file_backup($real_path,$filename,$folderName, 'wp_files');
			}
      		
	}

	function file_backup_interval($schedules){
		$mo2f_cron_file_backup_hours = get_option('mo2f_cron_file_backup_hours');
		$schedules['cron_backup_time'] = array(
			'interval' => $mo2f_cron_file_backup_hours,
			'display'  => esc_html__( 'Cron Activated' ),
		);
	 
		return $schedules;
    }

	function file_backup_deactivate(){
		$timestamp = wp_next_scheduled( 'file_cron_hook' );
		wp_unschedule_event( $timestamp, 'file_cron_hook' );
	 }

	 function mkdirectory($foldername){
		        $folderName = date("Ymd");
			    $basepath = get_home_path();
				if(!file_exists($basepath."file-backups")){
					mkdir($basepath."file-backups");
		        }

		        $basepath = get_home_path().'file-backups/';
		       $this-> create_index_file($basepath);
		      
		        if(!file_exists($basepath.$foldername)){
		            mkdir($basepath.$foldername);
		        }

		        $basepath = get_home_path().'file-backups'.'/'.$foldername.'/';
		        if(!file_exists($basepath.$folderName)){
			     	mkdir($basepath.$folderName);
        	    }
            return $folderName;
		}

		function create_index_file($folder_path){
		
			$html_path=$folder_path."index.html";
			$htaccess_path= $folder_path.".htaccess";

			if(!file_exists($html_path)){
	            $f = fopen($html_path, "a");
	            fwrite($f, '<html><body><a href="https://miniorange.com" target="_blank">WordPress backups by miniorange</a></body></html>');
	            fclose($f);
	        }
	        if(!file_exists($htaccess_path)){
	        	$f = fopen($htaccess_path, "a");
	        	fwrite($f, "deny from all");
	        	fclose($f);
	        }
	}

	function randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 16; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	 }

	 function send_email_backup($pass,$filename) {
    $toEmail = get_option('admin_email');
    
    

    $style="<style>.button {background-color: #008CBA;border: none;color: white;text-align: center;
  text-decoration: none;display: inline-block;font-size: 16px;padding: 14px 40px;margin: 4px 2px;cursor: pointer;}</style>";

    $content=$style.'<table cellpadding="25" style="margin:0px auto"><tbody><tr><td><table cellpadding="24" width="584px" style="margin:0 auto;max-width:584px;background-color:#f6f4f4;border:1px solid #a8adad">
                <tbody><tr><td><img src="https://ci3.googleusercontent.com/proxy/bsqfwxlN_rHFOhApsbPGugF_GTN5hDO9LSLj6XI-u5TRUBW2scP-4M6HDfkRrGLKd5VLbNV_zI4V1jXKwsjOEvf0woDkXYbmbKhgnNYfbfdqari89aTVuY0mVQ=s0-d-e1-ft#https://miniorange.s3.amazonaws.com/public/images/miniorange-logo.png" style="color:#5fb336;text-decoration:none;display:block;width:auto;height:auto;max-height:35px"></td>
                </tr></tbody></table><table cellpadding="24" style="background:#fff;border:1px solid #a8adad;width:584px;border-top:none;color:#4d4b48;font-family:Arial,Helvetica,sans-serif;font-size:13px;line-height:18px">
                <tbody><tr><td>
                <p style="margin-top:0;margin-bottom:20px">Dear User,</p><p style="margin-top:0;margin-bottom:10px"><p style="margin-top:0;margin-bottom:10px">A backup for your'.esc_attr($filename).' has been created for you. The ZIP is password protected and password is <b>'.esc_attr($pass).'</b> </p></p>
                <p style="margin-top:0;margin-bottom:10px"><p style="margin-top:0;margin-bottom:10px">Your backup is created under "/WordPress/file-backups" directory<br><br><p style="margin-top:0;margin-bottom:15px">Thank you,<br>miniOrange Team</p><p style="margin-top:0;margin-bottom:0px;font-size:11px">Disclaimer: This email and any files transmitted with it are confidential and intended solely for the use of the individual or entity to whom they are addressed.</p>
                </span></td></tr></tbody></table></td></tr></tbody></table>';


    
    $headers = array('Content-Type: text/html; charset=UTF-8');
    $subject='Backup For Database';

     $sent= wp_mail( $toEmail, $subject, $content, $headers);

}

      

      function file_backup($real_path, $filename,$folderName, $foldername){
                    
      	            $basepath=get_home_path();
      	            $rootPath = realpath($real_path);
                    $zip = new ZipArchive();
					$res = $zip->open($basepath.'file-backups'.'/'.esc_attr( $foldername).'/'.esc_attr($folderName).'/'.esc_attr($filename), ZipArchive::CREATE | ZipArchive::OVERWRITE);
					$files = new RecursiveIteratorIterator(
					    new RecursiveDirectoryIterator($rootPath),
					    RecursiveIteratorIterator::LEAVES_ONLY
					);
					foreach ($files as $name => $file)
					{
					    // Skip directories (they would be added automatically)
					    if (!$file->isDir())
					    {
					        // Get real and relative path for current file
					        $filePath = $file->getRealPath();
					        $relativePath = substr($filePath, strlen($rootPath) + 1);
                            
					        // Add current file to archive
					        $zip->addFile($filePath, $relativePath);
					       
					    }
					}
					
                   
				$zip->close();
}

}new Mowaf_site_backup;