<?php
if ( ! defined( 'ABSPATH' ) ) exit;
define( 'WEBINAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		global $wpdb;
		$id = absint( $_GET['aem_form']);
		$form_name = $_POST["form_name"];
		$form_url = $_POST["form_url"];	
        $form_id = $_POST["form_id"];
		$table_name = $wpdb->prefix . "prepare_doc_forms";	
		if (isset($_POST['update'])) {		
			$wpdb->update(
                $table_name,
                array('form_name' => $form_name, 'form_url' => $form_url, 'form_id' => $form_id), //data
				array( 'id' => $id ),
                array('%s','%s','%s'),//data format	
				array('%d')				
            );
            $message="Form successfully updated.";
            
		} else {
			$sql = "SELECT * FROM {$wpdb->prefix}prepare_doc_forms where id=".$id;	
			$result = $wpdb->get_results( $sql, 'ARRAY_A' );
			if($result){
				foreach($result as $value){
					if($value["id"]) $id = $value["id"];
					if($value["form_name"]) $form_name = $value["form_name"];	
					if($value["form_url"]) $form_url = $value["form_url"];
                    if($value["form_id"]) $form_id = $value["form_id"];	
				}
			}
		}
	?>
		<div class="wrap create_event">
			<h2>Update Webinar Event</h2>
			<?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">				
			<table>
				<tr>
					<th><label for="id">Id: </label></th>
					<td><input type="text" name="id" value="<?php echo $id; ?>" disabled /></td>
				</tr>
					 
				<tr>
					<th><label for="form_name">AEM Form Name<span> *</span>: </label></th>
					<td><input type="text" name="form_name" value="<?php echo $form_name; ?>" id="form_name" placeholder="aem form name" autocomplete="off" required/></td>
				</tr>	
				<tr>
					<th><label for="form_url">AEM Form Url<span> *</span>: </label></th>
					<td><input type="text" name="form_url" value="<?php echo $form_url; ?>" id="form_url" placeholder="aem form url" autocomplete="off" required/></td>
				</tr>
                <tr>
					<th><label for="form_id">Aem Form Id<span> *</span>: </label></th>
					<td><input type="text" name="form_id" value="<?php echo $form_id; ?>" id="form_id" placeholder="Aem form id" autocomplete="off" required /></td>
				</tr>
				<tr><th colspan="2"><input id="submit" type='submit' name="update" value='Update' class='button'><a id="cancel" class="cancel button" href="<?php echo home_url().'/wp-admin/admin.php?page=prepare_doc_forms'?>">Cancel</a></th></tr>
			</table>
			</form>		
		</div>
