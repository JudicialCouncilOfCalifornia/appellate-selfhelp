<?php
class Mowaf_backup_ajax
{
	function __construct(){

		add_action( 'admin_init'  , array( $this, 'mo_wpns_backup' ) );
	}

	function mo_wpns_backup(){
		 
		add_action( 'wp_ajax_mo_wpns_backup_ajax', array($this,'mo_wpns_backup_ajax') );
	}

		function mo_wpns_backup_ajax(){
           
			switch ($_POST['mo_wpns_backup_ajax_forms']) 
			{
				case 'wpns_filebackup_form':
					 $this->handle_save_backup_config($_POST); break;
				case 'wpns_instant_backup':
				     $this->instant_backup($_POST); break;	 
				
				
				
			}
		}
	function instant_backup($postData){
		if(! isset($postData['backup_plugin']) && ! isset($postData['backup_themes']) && ! isset($postData['backup_wp_files'])){
			wp_send_json('folder_error'); 
            return;
		}else{
			 $handler_obj = new Mowaf_site_backup;
             $handler_obj->file_cron_backup();
			 wp_send_json('success');
             return;
		}
	}	

	function handle_save_backup_config($postData){
		if(! isset($postData['backup_plugin']) && ! isset($postData['backup_themes']) && ! isset($postData['backup_wp_files'])){
			wp_send_json('folder_error'); 
            return;
		}

		 $handler_obj = new Mowaf_site_backup;
    	isset($postData['backup_plugin']) ?  update_option( 'mo_file_backup_plugins', sanitize_text_field($postData['backup_plugin'])) : update_option( 'mo_file_backup_plugins', 0);
		isset($postData['backup_themes']) ? update_option( 'mo_file_backup_themes', sanitize_text_field($postData['backup_themes'])) : update_option( 'mo_file_backup_themes', 0);
		isset($postData['backup_wp_files']) ? update_option( 'mo_file_backup_wp_files', sanitize_text_field($postData['backup_wp_files'])) : update_option( 'mo_file_backup_wp_files', 0);

		  if(isset($postData['file_backup_hour'])){		
		      $mo2f_cron_file_backup_hours = $postData['file_backup_hour'] * 60 *60;	
            if($mo2f_cron_file_backup_hours < 3600){
                wp_send_json('invalid_hours');
                return;
            }else{
                update_option('mo2f_cron_file_backup_hours', $mo2f_cron_file_backup_hours);
                 $handler_obj-> file_backup_deactivate();
                    if (!wp_next_scheduled( 'file_cron_hook')) {
                        wp_schedule_event( time(), 'cron_backup_time', 'file_cron_hook' );
                    }
                   wp_send_json('schedule_backup');
                   return; 
            }   
          }else{
             $handler_obj->file_cron_backup(); 

             wp_send_json('manual_backup');
             return;    
        }      		

	}	
}
new Mowaf_backup_ajax();
?>