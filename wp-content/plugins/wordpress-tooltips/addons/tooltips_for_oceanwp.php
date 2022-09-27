<?php
if (!defined('ABSPATH'))
{
	exit;
}

add_filter('wp_trim_words','tooltipsInContent',20,1);

function oeachwparchivetooltip()
{
	if (is_archive())
	{
	echo '<script type="text/javascript">';
?>
					jQuery(document).ready(function () {
						jQuery('.tooltipsall').each
						(function()
						{
						disabletooltipinhtmltagSinglei = jQuery(this).html();
						jQuery(this).replaceWith(disabletooltipinhtmltagSinglei);
						})
					})
<?php 
	echo '</script>';
	}
}

add_action('wp_footer','oeachwparchivetooltip');
add_filter( 'nav_menu_description', 'tooltipsmenucheckaddonpro' , 30, 1 );

function tooltipsmenucheckaddonpro( $description )
{
	 remove_filter('wp_trim_words','tooltipsInContent');
	 remove_filter('the_content','tooltipsInContent');
	 remove_filter('the_excerpt','tooltipsInContent');
	 remove_filter('the_title','tooltipsInContent');
	 
	 remove_filter('wp_trim_words','showTooltips');
	 remove_filter('the_content','showTooltips');
	 remove_filter('the_excerpt','showTooltips');
	 remove_filter('the_title','showTooltips');
	 
	 remove_filter('wp_trim_words','tooltipsInContent');
	 remove_filter('the_content','tooltipsInContent');
	 remove_filter('the_excerpt','tooltipsInContent');
	 remove_filter('the_title','tooltipsInContent');
	 
	 remove_filter('wp_trim_words','showTooltips');
	 remove_filter('the_content','showTooltips');
	 remove_filter('the_excerpt','showTooltips');
	 remove_filter('the_title','showTooltips');
	 
	 remove_action('wp_trim_words','showTooltips');
	 remove_action('the_content','showTooltips');
	 remove_action('the_excerpt','showTooltips');
	 remove_action('the_title','showTooltips');
	 
	 return $description;

}

return;
