<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/

function add_my_script() {
    wp_enqueue_script(
        'custom-script', // name your script so that you can attach other scripts and de-register, etc.
        get_stylesheet_directory_uri().'/custom.js', // this is the location of your script file
        array('jquery') // this array lists the scripts upon which your script depends
    );
}
add_action( 'wp_enqueue_scripts', 'add_my_script' );

/* adding custom link to images in gallery */

add_filter('avf_avia_builder_gallery_image_link', 'avia_change_gallery_thumbnail_link', 10, 4);
function avia_change_gallery_thumbnail_link($link, $attachment, $atts, $meta)
{
    $custom_url = get_post_meta($attachment->ID, '_gallery_link_url', true);
    if(!empty($custom_url))
    {
        $link[0] = $custom_url;
        $link['custom_link_class'] = 'aviaopeninbrowser';
    }
    return $link;
}