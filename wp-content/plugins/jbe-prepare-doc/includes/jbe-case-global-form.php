<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PREPARE_DOC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
$uri = $_SERVER['REQUEST_URI'];
global $wpdb; $case_id = ""; $style="";
$user_id = wp_get_current_user()->ID;
if(isset($_POST['submit'])){	
	$cname = $_POST['eHelp-DCA-AppDistrict'];
	$utype = $_POST['eHelp-DCA-AppDivision'];
	unset($_POST['submit']);
	var_dump($_POST);
	$data = array('caseName'=>$cname, 'userType'=>$utype);
	var_dump(print_r_xml($_POST, "afData"));
}
/* print the contents of a url */
function print_r_xml($arr,$wrapper = 'data',$cycle = 1)
{
	//useful vars
	$new_line = "\n";

	//start building content
	if($cycle == 1) { $output = '<?xml version="1.0" encoding="UTF-8" ?>'.$new_line; }
	$output.= tabify($cycle - 1).'<'.$wrapper.'>'.$new_line;
	$output.= tabify($cycle - 2).'<afBoundData>'.$new_line;
	$output.= tabify($cycle - 3).'<data>'.$new_line;
	$output.= tabify($cycle - 4).'<Global-Schema>'.$new_line;
	foreach($arr as $key => $val) {
		if(!is_array($val)) {
			$output.= tabify($cycle).'<'.htmlspecialchars($key).'>'.$val.'</'.htmlspecialchars($key).'>'.$new_line;
		} else {
			$output.= print_r_xml($val,$key,$cycle + 1).$new_line;
		}
	}
	$output.= tabify($cycle - 4).'</Global-Schema>'.$new_line;
	$output.= tabify($cycle - 3).'</data>'.$new_line;
	$output.= tabify($cycle - 2).'</afBoundData>'.$new_line;	
	$output.= tabify($cycle - 1).'</'.$wrapper.'>';
	

	//return the value
	//return $output;
	if (!file_exists('wp-content/uploads/jbe_global_xmls')) {
		mkdir('wp-content/uploads/jbe_global_xmls', 0777, true);
	}
	$xml = simplexml_load_string($output);
	$doc = new DOMDocument('1.0');
	$doc->formatOutput = true;
	$doc->preserveWhiteSpace = true;
	$doc->loadXML($xml->asXML(), LIBXML_NOBLANKS);
	$doc->save('wp-content/uploads/jbe_global_xmls/sample.xml');
	return $output;
}

/* tabify */
function tabify($num_tabs)
{
	for($x = 1; $x <= $num_tabs; $x++) { $return.= "\t"; }
	return $return;
}
?>
<form id='formcase' method="POST" name="addCase">
	
	<div class='case-details-container'>
		<h3>Gloabl Fields</h3>
		<div class='case-table'><div class='case-cell'>eHelp-DCA-AppDistrict&nbsp;</div><div class='case-cell'><input type="text" id="eHelp-DCA-AppDistrict" name='eHelp-DCA-AppDistrict'></div></div>
		<div class='case-table'><div class='case-cell'>eHelp-DCA-AppDivision&nbsp;</div><div class='case-cell'><input type="text" id="eHelp-DCA-AppDivision" name='eHelp-DCA-AppDivision'></div></div>
		<div class='button-container'><input type='submit' class='form-submit' name="submit" value="Save"><input type='submit' class='form-submit' name="submit" value="Submit"></div>
	</div>

</form>
