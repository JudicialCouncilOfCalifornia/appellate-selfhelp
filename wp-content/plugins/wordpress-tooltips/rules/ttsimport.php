<?php
if (!defined('ABSPATH'))
{
	exit;
}

function tooltipsImportFree()
{
	?>
<div class="wrap tooltipsaddonclass">
	<h2>
	<?php
		echo __("Import Tooltips", "wordpress-tooltips");
	?>
	</h2>
	<table class="wp-list-table widefat fixed" style="margin-top:20px;">
		<tr><td>
			<form enctype="multipart/form-data" action="" method="POST">
    			<h3><?php echo __("Import tooltips from csv", "wordpress-tooltips"); ?></h3>
    			<label for="Your CSV File"> <?php echo __("Your CSV File:", "wordpress-tooltips"); ?> </label>
    			<?php 
    			wp_nonce_field ( 'tooltipscsvuploadfilenonce' );
    			?>
    			<input name="tooltips_csv_upload_file" type="file" />
			    <div style="margin-top:30px !important;margin-bottom:30px  !important;">
   				<input type="submit" value=" <?php echo __("Import", "wordpress-tooltips"); ?> " name="import" />
    			</div>
			</form>
			<div>
			<hr />
				<h4>Please note:</h4>
				<div style="margin-bottom:10px;">
				<span style="color:#888;">#1</span> You can find sample.csv in the folder "tooltips-pro", we have make sample in this csv file, you can just follow our format to build your csv file 
				</div>
				<div style="margin-bottom:10px;">
				<span style="color:#888;">#2</span> In sample.csv, there are two fields, "tooltips term" and "tooltips content", tooltips term will be imported as title of tooltips, and "tooltips content" will be imported as content of tooltips.  
				</div>
				<div style="margin-bottom:10px;">
				<span style="color:#888;">#3</span> In sample.csv, we use comma "," to split fields, if you have comma (,) in your content field, it maybe caused the import failed, the solution is use double quotes (") to warp your content field, it looks like this:
				<span style='color:darkgreen'>"the world, need goods"</span> 
				</div>
				<div style="margin-bottom:10px;">
				<span style="color:#888;">#4</span> In general, #3 will works well, but in your tooltip content, maybe you have double quotes (") already, in this case, because there are a lot of double quotes ("), so import will failed again, in this case, the solution will looks like this:
				<span style='color:darkgreen'>"the world, \"need goods\""</span>, just add \ before your own ", it will works well
				</div>
				<div style="margin-bottom:10px;">
				<span style="color:#888;">#5</span> If you want to add mages in your tooltips, that is easy, just do it like this:
				<span style='color:darkgreen'>hi this is image import sample < img class="alignnone size-medium wp-image-259" src="http://yourdomain.com/wp-content/uploads/2018/07/yourimagenam.png" /></span> , just change class, image path, image name as your values. 
				</div>
				<div style="margin-bottom:10px;">
				<span style="color:#888;">#6</span> You can find all these samples in sample.csv in the folder "tooltips-pro"
				</div>				
				<div style="margin-bottom:10px;">
				<span style="color:#888;">#7</span>  
				You will find video tutorial "import wordpress tooltips from csv" and more documents at <a href='https://tooltips.org/?s=import'>How to Import WordPress Tooltips</a>
				</div>
			</div>
		</td></tr>
	</table>
<?php
	global $wpdb;
	if (isset($_POST['import']))
	{
		check_admin_referer ( 'tooltipscsvuploadfilenonce' );
		
		if (!current_user_can('upload_files'))
			wp_die(__('Sorry, you are not allowed to upload files.'));
		
		$file = $_FILES ['tooltips_csv_upload_file'];
		$file_type = substr ( strstr ( $file ['name'], '.' ), 1 );
		if ($file_type != 'csv') {
			echo __ ( "<h4 style='color:firebrick'>Sorry, We only support csv file, please upload csv file again.</h4>", "wordpress-tooltips" );
			exit ();
		}
		$handle = fopen ( $file ['tmp_name'], "r" );
		delete_option ( 'existed_tooltips_post' );
		
		$existed_tooltips_post = get_option ( 'existed_tooltips_post' );
		if (empty ( $existed_tooltips_post )) {
			$existed_tooltips_post = array ();
		}
		
		$row = 0;
		while ( $data = fgetcsv ( $handle, 1000, ',' ) ) {
			$row ++;
			if ($row == 1)
				continue;
			$num = count ( $data );
			$term_id = 0;
			$new_post = '';
			$post_title = '';
			$post_content = '';

			for($i = 0; $i < $num; $i ++) {
				if ($i == 0) {
					$post_title = $data [0];
				}
				
				if ($i == 1) {
					$post_content = $data [1];
				}
			}
				
			$new_post = array (
				'post_title' => @$post_title,
				'post_content' => @$post_content,
				'post_status' => 'publish',
				'post_type' => 'tooltips',
				'post_author' => '1' 
			);
			
			$post_table = $wpdb->prefix . 'posts';
			$sql = 'select `ID` from `' . $post_table . "` where `post_title` = '" . $post_title . "' and `post_status` = 'publish' and `post_type` = 'tooltips' limit 1";
			$result = $wpdb->get_var ( $sql );
			$is_dup = '';
			if ($result) {
				$is_dup = true;
			}
			
			if ($is_dup == true) {
			} else {
				
				$id = wp_insert_post ( $new_post );
				if (! (empty ( $id ))) {
					
					if (in_array ( $id, $existed_tooltips_post )) {
					} else {
						$existed_tooltips_post [] = $id;
					}
				}
			}
			update_option ( 'existed_tooltips_post', $existed_tooltips_post );
		}
		fclose ( $handle );
		$checkImportedTooltipsURL = get_option ( 'siteurl' ) . '/wp-admin/edit.php?post_type=tooltips';
		
		echo '<br />';
		echo __ ( "<h4 style='color:firebrick'>Tooltips imported, Please click <a href='$checkImportedTooltipsURL'>All Tooltips</a> to check the result, thanks</h4>", "wordpress-tooltips" );
	}
}



