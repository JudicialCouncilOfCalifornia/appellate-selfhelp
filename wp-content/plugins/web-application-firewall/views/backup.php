<?php
global $mo_MoWpnsUtility,$mmp_dirName;
$setup_dirName = $mmp_dirName.'views'.DIRECTORY_SEPARATOR.'link_tracers.php';
 include $setup_dirName;
 
 ?>
<?php

echo '
		<div id="wpns_backup_message" style=" padding-top:8px ;padding-bottom : 8px;"></div>
		
		';	
echo'
	<div class="mo_wpns_divided_layout">
		<div class="mo_wpns_setting_layout">';

echo'		<h3>Manual Database Backup <a href='.$mo_waf_premium_docfile['Manual Database Backup'].' target="_blank"><span class="dashicons dashicons-external mo_wpns_doc_link" title="More information.."></span></a></h3>
			<form id="mo_wpns_db_backup" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_db_backup" />
				<p>Backup your WordPress database easily with a single click. Your backup will be saved in <b>'.site_url().'/db-backups</b> .</p>
				<input type="submit" name="submit" value="Backup Now" class="button button-primary button-large" />
			</form>
			<div class="db_backup_desc" hidden></div>
	
	<script>
		var message = "'.esc_attr($message).'";
		jQuery(document).ready(function() {
			$("#mo_wpns_db_backup").on("submit",function (e){
				$(".db_backup_desc").empty();
			    $(".db_backup_desc").append(message);
			    $(".db_backup_desc").slideDown(400);
			    setInterval(function(){  $("#inprogress").fadeOut(700); }, 1000);
			    setInterval(function(){  $("#inprogress").fadeIn(700); }, 1000);
			    $.ajax({
			        url: "'.esc_attr($page_url).'",
			        type: "GET",
			        data: "option=backupDB",
			        crossDomain: !0,
			        dataType: "json",
			        contentType: "application/json; charset=utf-8",
			        success: function(o) {
			        	$("#dbloader").empty();
			        	var result = JSON.stringify(o);
			        	$("#dbloader").append("'.$message2a.' "+result+" '.esc_attr($message2b).'");
			        	$(".backupmessage").css("background-color","#1EC11E");
			        	$(".backupmessage h2").empty();
			        	$(".backupmessage h2").append("DATABASE BACKUP COMPLETED");
			        },
			        error: function(o, e, n) {}
			    });
			    e.preventDefault();
			});
		} );
	</script>';
	echo   '</div>
	<div class="mo_wpns_setting_layout">
            <h3>Automatic Database Backup <a href='.$mo_waf_premium_docfile['Automatic Database Backup'].' target="_blank"><span class="dashicons dashicons-external mo_wpns_doc_link" title="More information.."></span></a></h3>';?>
        
	<form id="mo2f_enable_cron_backup_form" method="post" action="" >
	<table>
	
	<input type="hidden" name="option" value="mo2f_enable_cron_backup">
	
	<tr>
	<td>
		<input type="checkbox"  name="mo2f_enable_cron_backup_timely" value="1" 
		<?php if(get_option('mo2f_enable_cron_backup')) echo "checked"; 
		?>
		onchange="document.getElementById('mo2f_enable_cron_backup_form').submit();"> Enable automatic DB Backup.
	</td>
	</tr>
	</table>
	</form>
	<?php if(get_option('mo2f_enable_cron_backup')){
		$mo2f_cron_hours = (get_option('mo2f_cron_hours')/3600);	
	?>
	<form id="mo2f_enable_cron_backup" method="post" action="">
	<input type="hidden" name="option" value="mo2f_cron_backup_configuration">
	<table class="mo2f_ns_settings_table" style="width:100%;">
		<tr>
			<td>Backup is created in the folder <b>"<?php echo (esc_attr(site_url())).'/db-backups';
			 ?>"</b></td>
		</tr>
	  <tr>
			<td style="width:40%">Number of hours after which a backup should be created:
			<input class="mo2f_ns_table_textbox" style="width:15%;" type="number" id="mo2f_cron_hours" name="mo2f_cron_hours" required placeholder="12" value="<?php echo (esc_attr($mo2f_cron_hours));?>" min="1"/></td>
			<td style="width:25%"></td>
		</tr>
		
	   <tr>
			<td><br><input type="submit" name="submit" value="Save Settings" class="button button-primary button-large"	></td>
		</tr>
	</table>
	</form>
	<?php }?> 
	


<?php
echo   '</div>
   		 <div class="mo_wpns_setting_layout">
           <h3>Files Backup <a href='.$mo_waf_premium_docfile['Files Backup '].' target="_blank"><span class="dashicons dashicons-external mo_wpns_doc_link" title="More information.."></span></a>  </h3>';?>
        <form id="mo2f_enable_cron_file_backup_form" method="post" action="" >
	<table>
	<tr>
	<td><input type="hidden" name="option" value="mo2f_enable_cron_file_backup"></td>
	</tr>
	<tr>
	<td><input type="checkbox"  name="mo2f_enable_cron_file_backup_timely" value="1" <?php if(get_option('mo2f_enable_cron_file_backup')) echo "checked";?> onchange="document.getElementById('mo2f_enable_cron_file_backup_form').submit();"> Tick the checkbox if you want take <b>sheduled backup</b> and <b>Save Setting</b> for enable, otherwise create backup manually</td>
	</tr>
	<tr><td></td></tr>
	</table>
	</form>
	<?php if(get_option('mo2f_enable_cron_file_backup')){ 
		$mo2f_cron_file_backup_hours = get_option('mo2f_cron_file_backup_hours')/3600;
		}?>	
	
	  
	<form id="" method="post" action="">
		<input type="hidden" name="option" value="mo_mmp_filebackup_configuration">
		<table class="mo2f_ns_settings_table" style="width:100%;">
			
			<?php if(get_option('mo2f_enable_cron_file_backup')){ ?>
				
			<tr>
				<td style="width:40%">Number of hours after which a backup should be created:
				<input class="mo2f_ns_table_textbox" style="width:7%;" type="number" id="mo2f_cron_file_backup_hours" name="mo2f_cron_file_backup_hours" required placeholder="1" value="<?php echo (esc_attr($mo2f_cron_file_backup_hours));?>" min="1"/></td>
				
			</tr>
			
			<?php } ?>
			<tr>
			    <td>Backup created in your computer under <b>"/wordpress/file-backups".</b></td>
			</tr>
			
		</table>
		<table class="mo_wpns_settings_table">

		<tr>
			<td style="width:30%"><b>Select Folders to Backup : </b></td>
			<td>
			<input type="checkbox"id="mo_file_backup_plugins" name="mo_file_backup_plugins" value="1" <?php checked(get_option('mo_file_backup_plugins') == 1);?>> WordPress Plugins folder<br>
			<input type="checkbox" id="mo_file_backup_themes" name="mo_file_backup_themes" value="1" <?php checked(get_option('mo_file_backup_themes') == 1);?>> WordPress Themes folder<br>
		 <input type="checkbox" id="mo_file_backup_wp_files" name="mo_file_backup_wp_files" value="1" <?php checked(get_option('mo_file_backup_wp_files') == 1);?>> WordPress files
			</td>
		</tr>
		
		</table>
		<br>
		<input type="hidden" name="mo_file_backup_plugins" id="mo_file_backup_plugins"
               	value="<?php echo wp_create_nonce( 'mo_file_backup_plugins' ); ?>"/>
		<input type="button" name="create_backup" id="create_backup" value="<?php if(get_option('mo2f_enable_cron_file_backup'))echo 'Save Settings'; else echo 'Backup Now';?>" class="button button-primary button-large">
				 
		<input type="button" name="instant_backup" id="instant_backup" class="button button-primary button-large" style="<?php if(!get_option('mo2f_enable_cron_file_backup'))echo 'display: none';?>; " value="Instant Backup" >
		</form>
		<form id="instant_file_backup" method="post" action="">
			<input type="hidden" name="mo_file_backup_plugins" id="mo_file_backup_plugins"
               	value="<?php echo wp_create_nonce( 'mo_file_backup_plugins' ); ?>"/>
		<input type="hidden" name="option" value="instant_file_backup">
       </form>


	<?php 

	echo '
		<script>
            	jQuery(document).ready(function(){
	             jQuery("#create_backup").click(function(){
			     var data = {
				"action"					: "mo_wpns_backup_ajax",
				"mo_wpns_backup_ajax_forms"	: "wpns_filebackup_form",
				"nonce"                       : jQuery("input[name=mo_file_backup_plugins]:input").val(),
				"backup_plugin":jQuery("input[name= mo_file_backup_plugins]:checked").val(),
				"backup_themes":jQuery("input[name= mo_file_backup_themes]:checked").val(),
				"backup_wp_files":jQuery("input[name= mo_file_backup_wp_files]:checked").val(),
				"file_backup_hour":jQuery("input[name= mo2f_cron_file_backup_hours]:input").val(),
			    };
			    
	        jQuery.post(ajaxurl ,data, function(resposnse){
	        	jQuery("#wpns_backup_message").empty();
				jQuery("#wpns_backup_message").hide();
				jQuery("#wpns_backup_message").show();
				if (resposnse == "folder_error"){
					jQuery("#wpns_backup_message").addClass("notice notice-error is-dismissible");
					jQuery("#wpns_backup_message").append("Please select at least one folder for backup.");
					window.scrollTo({ top: 0, behavior: "smooth" });
				}else if(resposnse == "invalid_hours"){
                  	jQuery("#wpns_backup_message").addClass("notice notice-error is-dismissible");
					jQuery("#wpns_backup_message").append("Invalid number of hours.");
					window.scrollTo({ top: 0, behavior: "smooth" });
				}else if(resposnse == "schedule_backup"){
                  	jQuery("#wpns_backup_message").addClass("notice notice-success is-dismissible");
					jQuery("#wpns_backup_message").append("Automatic Backup Scheduled Successfully.");
					window.scrollTo({ top: 0, behavior: "smooth" });
				}else if(resposnse == "manual_backup"){
					jQuery("#wpns_backup_message").addClass("notice notice-success is-dismissible");
					jQuery("#wpns_backup_message").append("Backup Created Successfully.");
					window.scrollTo({ top: 0, behavior: "smooth" });
				}
             
	          });     
	             
	    });
	    jQuery("#instant_backup").click(function(){
			jQuery("#wpns_backup_message").empty();
			jQuery("#wpns_backup_message").hide();
			jQuery("#wpns_backup_message").show();
			jQuery("input[name=instant_backup]").attr("disabled", true);
	    	document.getElementById("instant_backup").style.backgroundColor = "#b0d2cf";
	    	var intant_value = {
	    		"action"					: "mo_wpns_backup_ajax",
	    		"mo_wpns_backup_ajax_forms"	: "wpns_instant_backup",
	    		"nonce"                       : jQuery("#mo_file_backup_plugins").val(),
	    		"backup_plugin":jQuery("input[name=mo_file_backup_plugins]:checked").val(),
				"backup_themes":jQuery("input[name= mo_file_backup_themes]:checked").val(),
				"backup_wp_files":jQuery("input[name= mo_file_backup_wp_files]:checked").val(),

	    	};
	    	 jQuery.post(ajaxurl ,intant_value, function(resposnse){
				jQuery("input[name=instant_backup]").removeAttr("disabled");
                document.getElementById("instant_backup").style.backgroundColor = "#2271b1";
	        	
				if (resposnse == "folder_error"){
					jQuery("#wpns_backup_message").addClass("notice notice-error is-dismissible");
					jQuery("#wpns_backup_message").append("Please select at least one folder for backup.");
					window.scrollTo({ top: 200, behavior: "smooth" });
			} else if(resposnse == "success"){
					jQuery("#wpns_backup_message").addClass("notice notice-success is-dismissible");
					jQuery("#wpns_backup_message").append("Backup Created Successfully.");
					window.scrollTo({ top: 200, behavior: "smooth" });
			}
	    });
	});
    });
            </script>
	';
       
   
   ?>
       
<?php 


   echo '</div></div>';
?>