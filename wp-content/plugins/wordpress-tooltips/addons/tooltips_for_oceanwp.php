<?php
if (!defined('ABSPATH'))
{
	exit;
}

add_filter('wp_trim_words','tooltipsInContent',20,1);
add_action('wp_trim_words','showTooltips',10,1); 
