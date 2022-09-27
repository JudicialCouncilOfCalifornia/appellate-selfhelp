<?php 
if (!defined('ABSPATH'))
{
	exit;
}


function tooltips_pro_disable_tooltip_in_mobile_free()
{
	 $disabletooltipmobile = get_option("disabletooltipmobile");
	 if ($disabletooltipmobile == 'YES')
	 {
		 if ( wp_is_mobile() )
		 {
		 	return true;
		 }
		 else 
		 {
		 	return false;
		 }
	 }
	 else 
	 {
	 	return false;
	 }
}

