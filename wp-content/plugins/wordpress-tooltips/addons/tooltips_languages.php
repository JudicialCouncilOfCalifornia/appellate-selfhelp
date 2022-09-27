<?php
if (!defined('ABSPATH'))
{
	exit;
}

function tooltips_free_language_menu_addon()
{
	add_submenu_page('edit.php?post_type=tooltips',__('Languages','wordpress-tooltips'), __('Languages','wordpress-tooltips'),"manage_options", 'tooltipsFreeLanguageMenu','tooltipsFreeLanguageMenu');
}

add_action('admin_menu', 'tooltips_free_language_menu_addon');



function tooltips_free_language_setting_panel($title = '', $content = '')
{
	?>
<div class="wrap tooltipsaddonclass">
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder">
			<div id="post-body">
				<div id="dashboard-widgets-main-content">
					<div class="postbox-container" style="width: 90%;">
						<div class="postbox">					
							<h3 class='hndle' style='padding: 10px 0px; border-bottom: 0px solid #eee !important;'>
							<span>
								<?php	echo $title; 	?>
							</span>
							</h3>
						
							<div class="inside postbox" style='padding-top:10px; padding-left: 10px; ' >
								<?php echo $content; ?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear: both"></div>
<?php	
}



function tooltips_free_language_setting_panel_head($title)
{
	?>
		<div style='padding-top:20px; font-size:22px;'><?php echo $title; ?></div>
		<div style='clear:both'></div>
<?php 
}

function tooltipsFreeLanguageMenu()
{
	global $wpdb,$table_prefix;

	if (isset($_POST['glossaryLanguageCustomNavALLSubmit']))
	{
		check_admin_referer ( 'tooltipslanguagenonce' );	
		//7.8.7
		// $glossaryLanguageCustomNavALL = $_POST['glossaryLanguageCustomNavALL'];
		$glossaryLanguageCustomNavALL = sanitize_textarea_field($_POST['glossaryLanguageCustomNavALL']);
		update_option('glossaryLanguageCustomNavALL', $glossaryLanguageCustomNavALL);
		tooltipsMessage('Glossary language has been changed');
	}

	$glossaryLanguageCustomNavALL = get_option('glossaryLanguageCustomNavALL');
	if (empty($glossaryLanguageCustomNavALL))
	{
		$glossaryLanguageCustomNavALL = 'ALL';
	}
	
	$languageselectboxURL = get_option('siteurl'). '/wp-admin/edit.php?post_type=tooltips&page=glossarysettingsfree';
	//7.9.3
	//$title = "Custom Language of Tooltip and Glossary <p><i style='color:gray;'>(please select '<a href='$languageselectboxURL' target='_blank'>custom my language</a>' option in <a href='$languageselectboxURL' target='_blank'>language selectbox</a> first )</i></p>";
	$title = "Custom Language of Tooltip and Glossary <p><i style='color:gray;'>(please select '<a href='".esc_url($languageselectboxURL)."' target='_blank'>custom my language</a>' option in <a href='$languageselectboxURL' target='_blank'>language selectbox</a> first )</i></p>";
	tooltips_free_language_setting_panel_head($title);

	$title = 'Custom Glossary to Your Own Language -- word "ALL" on Navigation Bar:';
	$content = '';
	
	$content .= '<form class="formTooltips" name="formTooltips" action="" method="POST">';

	$content .= wp_nonce_field ( 'tooltipslanguagenonce');

	$content .= '<table id="tableTooltips" width="100%">';
	
	$content .= '<tr style="text-align:left;">';
	$content .= '<td width="25%"  style="text-align:left;">';
	$content .= 'Custom the word "ALL" on Nav Bar: ';
	$content .= '</td>';
	$content .= '<td width="30%"  style="text-align:left;">';
	//7.9.3
	//$content .=  '<input type="text" id="glossaryLanguageCustomNavALL" name="glossaryLanguageCustomNavALL" value="'.  $glossaryLanguageCustomNavALL .'" required placeholder="for example:ALL">';
	$content .=  '<input type="text" id="glossaryLanguageCustomNavALL" name="glossaryLanguageCustomNavALL" value="'.  esc_attr($glossaryLanguageCustomNavALL) .'" required placeholder="for example:ALL">';
	$content .= '</td>';
	$content .= '<td width="30%"  style="text-align:left;">';
	$content .= '<input type="submit" class="button-primary" id="glossaryLanguageCustomNavALLSubmit" name="glossaryLanguageCustomNavALLSubmit" value=" Submit ">';
	$content .= '</td>';
	$content .= '</tr>';
	
	$content .= '</table>';
	$content .= '</form>';
	
	tooltips_free_language_setting_panel($title, $content);
}