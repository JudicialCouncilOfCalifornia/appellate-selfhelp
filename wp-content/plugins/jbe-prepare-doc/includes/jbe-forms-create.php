<?php
if ( ! defined( 'ABSPATH' ) ) exit;
define( 'PREPARE_DOC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
global $wpdb;
$form_name = $_POST["form_name"];
$form_url = $_POST["form_url"];
$form_id = $_POST["form_id"];
	//insert
	$table_name = $wpdb->prefix . "prepare_doc_forms";
	if (isset($_POST['insert'])) {
		$wpdb->insert(
                $table_name,
                array('form_name' => $form_name, 'form_url' => $form_url, 'form_id' => $form_id), //data
                array('%s', '%s', '%s') //data format			
        );
		$message = "Aem form creaed";
	}	

	$last = $wpdb->get_row("SHOW TABLE STATUS LIKE '$table_name'");
	
	$id = $last->Auto_increment;

	?>
		<div class="wrap create_event">
			<h2>Add New Aem Form</h2>
			<?php if (isset($message)): ?><div class="updated"><p><?php echo $message; ?></p></div><?php endif; ?>
			<form method="post" id="webinar_list_create" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">				
			<table>
				<tr>
					<th><label for="id">Id: </label></th>
					<td><input type="text" name="id" id="id"  value="<?php echo $id; ?>" readonly /></td>
				</tr>
				<tr>
					<th><label for="form_name">Aem Form Name<span> *</span>: </label></th>
					<td><input type="text" name="form_name" value="" id="form_name" placeholder="Aem form name" autocomplete="off" required /></td>
				</tr>
				<tr>
					<th><label for="form_url">Aem Form Url<span> *</span>: </label></th>
					<td><input type="text" name="form_url" value="" id="form_url" placeholder="Aem form url" autocomplete="off" required /></td>
				</tr>
                <tr>
					<th><label for="form_id">Aem Form Id<span> *</span>: </label></th>
					<td><input type="text" name="form_id" value="" id="form_id" placeholder="Aem form id" autocomplete="off" required /></td>
				</tr>
				<tr><th colspan="2"><input id="submit" type='submit' name="insert" value='Add' class='button button-fimary'> <a id="cancel" class="cancel button" href="<?php echo home_url().'/wp-admin/admin.php?page=prepare_doc_forms'?>">Cancel</a></th></tr>
			</table>
			</form>		
		</div>
