<?php

/*
* Add your own functions here. You can also copy some of the theme functions into this file. 
* Wordpress will use those functions instead of the original functions then.
*/
function ajax_script(){
    ?>
    <script type="text/javascript">
    var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
    var post_id = <?php echo get_the_id(); ?>; 
</script>
<?php
}
add_action('wp_head',"ajax_script");
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_action('init', 'avf_remove_media_element', 10);
function avf_remove_media_element()
{	
	//wp_deregister_script('wp-mediaelement');
	//wp_deregister_style('wp-mediaelement');
	//wp_deregister_script('mediaelement');
	//wp_deregister_script('mediaelement-core');
	//wp_deregister_script('mediaelement-migrate');
}
add_filter('avia_load_shortcodes', 'avia_include_shortcode_template', 15, 1);
function avia_include_shortcode_template($paths)
{
	$template_url = get_stylesheet_directory();
    	array_unshift($paths, $template_url.'/shortcodes/');
	return $paths;
}
add_theme_support('avia_template_builder_custom_css');


function add_my_script() {
   
	wp_enqueue_style( 'bot-chat', get_stylesheet_directory_uri() . '/botchat.css' );
	/*wp_enqueue_script(
        'bot-chat', // name your script so that you can attach other scripts and de-register, etc.
        get_stylesheet_directory_uri().'/botchat.js', // this is the location of your script file
        array('jquery') // this array lists the scripts upon which your script depends
    );*/
    
}
//add_action( 'wp_enqueue_scripts', 'add_my_script' );

// custom script
add_action( 'wp_footer', 'ava_custom_script' );
function ava_custom_script() {

//wp_dequeue_script( 'avia-popup' );
//wp_enqueue_script( 'avia-popup-child', get_stylesheet_directory_uri().'/js/aviapopup/jquery.magnific-popup.min.js', array('jquery'), 2, true );	

$searchPlaceholder = 'Search the Knowledge Center';
$notification ='Welcome to our new site. Please fill out <a href="https://www.surveymonkey.com/r/KD8BYHW" class="notification-popup">this survey</a> to help us better understand your experience with the site. Thank you for your help!';
if(ICL_LANGUAGE_CODE =="es"){ 
	$searchPlaceholder = 'Buscar en el Centro de Conocimiento';
    $notification ='Bienvenido a nuestro nuevo sitio web. Le pedimos que complete <a href="https://www.surveymonkey.com/r/KD8BYHW" class="notification-popup">esta encuesta</a> para ayudarnos a entender mejor su experiencia con el sitio. Gracias por su colaboración.';
}
?>
	<script type="text/javascript">
		(function($) {
			$('#footer #s').attr('placeholder', '<?php echo $searchPlaceholder; ?>').attr('title', '<?php echo $searchPlaceholder; ?>');			
		})(jQuery);		
	</script>
	<div class='jbe-notification'><div class='jbe-notification-toggle'></div><div class='jbe-notification-content'><p><?php echo $notification; ?></p></div></div>

<?php
 wp_enqueue_script(
        'custom-script', // name your script so that you can attach other scripts and de-register, etc.
        get_stylesheet_directory_uri().'/custom.js', // this is the location of your script file
        array('jquery') // this array lists the scripts upon which your script depends
    );
}


add_filter('body_class', 'body_class_filter');

//language code in body tag
function body_class_filter($css_classes)
{
$res	= $css_classes;
if(defined('ICL_SITEPRESS_VERSION'))
{
$res[] = ICL_LANGUAGE_CODE;
}
return $res;
}

add_action('after_setup_theme', 'ava_register_footer_2', 10);
function ava_register_footer_2() {
        $number = 1;
	for ($i = 1; $i <= $number; $i++)
	{
		register_sidebar(array(
			'name' => 'Footer2 - column'.$i,
			'before_widget' => '<section id="%1$s" class="widget clearfix %2$s">',
			'after_widget' => '<span class="seperator extralight-border"></span></section>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
		));
	}	
}

add_filter( 'wp_nav_menu_items', 'add_loginout_link', 10, 2 );
function add_loginout_link( $items, $args ) {
    if (is_user_logged_in() && $args->theme_location == 'avia') {
        $items .= '<li class="login-menu menu-item"><a href="'.wp_logout_url( home_url() ).'"><span class="en">Sign Out</span><span class="es">Desconectar</span></a></li>';
    }
    elseif (!is_user_logged_in() && $args->theme_location == 'avia') {
        $items .= '<li class="login-menu menu-item"><a href="'.home_url().'/login"><span class="en">Sign In</span><span class="es">Registrarse</span></a></li>';
    }
    return $items;	
}
/* Home page most visited pages shortcode gt_get_post_view()*/
function display_top_visited_pages($atts) {
	global $wpdb, $wp, $post, $top_visit_pageIds, $top_visit_pageHtml;
	$pageID = (int) $atts['id'];
	//$rank = (int) $atts['rank'];
	$html = '';
	//if (empty($pageIds) && empty($pageHtml)) {
    if ($pageID != '' && is_int($pageID)) {
		$post = get_post($pageID);
		if (isset($post) && !empty($post) && $post != null) {		
			$args = array(
				'child_of'  => $pageID,
				'post_type' => 'page',
				'sort_order' => 'ASC',
				'sort_column' => 'ID'
			 );			
			$childrens = get_pages( $args );
						
			if ( $childrens ) {
				foreach ( $childrens as $child ) {
					$total = (int) get_post_meta( $child->ID, 'post_views_count', true );				
					$top_visit_pageIds[$child->ID] = $total;
					$top_visit_pageHtml[$child->ID] = '<a class="page-rank" data-rank="'.$total.'" data-id="'.$child->ID.'" href="' . get_page_uri($child) .'/?lang='.ICL_LANGUAGE_CODE.'">'. $child->post_title .'<div class="triangle"></div><span class="page-icon-plus">+</span></a>';
				}				
			}						
		} else {
			$html .= __('Please enter the valid post or page id.','post-views-count');
		}
    } else {
    	$html .= __('Please enter the valid post or page id.','post-views-count');
    }
	//}
	arsort($top_visit_pageIds);
	$i=1;
	$html .= '<div class="top-visited-kc-pages">';
	foreach($top_visit_pageIds as $id => $value){
		if($i <= 8){
			$html .= $top_visit_pageHtml[$id];
		}
		$i++;
	}
	$html .= '</div>';
	return $html;	
}
add_shortcode('top_page_visited_pages','display_top_visited_pages');
function display_top_visited_page($atts) {
	global $wpdb, $wp, $post, $pageIds, $pageHtml;
	$pageID = (int) $atts['id'];
	$rank = (int) $atts['rank'];
	$html = '';
	//if (empty($pageIds) && empty($pageHtml)) {
    if ($pageID != '' && is_int($pageID)) {
		$post = get_post($pageID);
		$table_name = $wpdb->prefix."page_visit";
		if (isset($post) && !empty($post) && $post != null) {		
			$args = array(
				'child_of'  => $pageID,
				'post_type' => 'page',
				'sort_order' => 'ASC',
				'sort_column' => 'ID'
			 );			
			$childrens = get_pages( $args );
						
			if ( $childrens ) {
				foreach ( $childrens as $child ) {	
					$pageCount_qry = $wpdb->prepare('SELECT SUM(page_visit) as total FROM ' . $table_name . ' WHERE page_id=%d', $child->ID);
					$pageCount = $wpdb->get_results($pageCount_qry);
					$total = (int) $pageCount[0]->total;				
					$pageIds[$child->ID] = $total;
					$pageHtml[$child->ID] = '<a class="page-rank" data-rank="'.$total.'" data-id="'.$child->ID.'" href="' . get_page_uri($child) .'/?lang='.ICL_LANGUAGE_CODE.'">'. $child->post_title .'<div class="triangle"></div><span class="page-icon-plus">+</span></a>';
				}				
			}						
		} else {
			$html .= __('Please enter the valid post or page id.','page-visit-counter');
		}
    } else {
    	$html .= __('Please enter the valid post or page id.','page-visit-counter');
    }
	//}
	arsort($pageIds);
	$i=1;
	foreach($pageIds as $pageid=>$visit_count){
		if($i == $rank){
			$html .= $pageHtml[$pageid];
		}
		$i++;
	}
	
	return $html;	
}
add_shortcode('top_page_visited_page','display_top_visited_page');

function home_carousel_videos_to_knowledge_videos($atts) {
	
	$carouselID = (int) $atts['id'];
	$html = '';
	$images_ids = array_filter( explode( ',', get_post_meta( $carouselID, '_wpdh_image_ids', true ) ) );
	if ( count( $images_ids ) < 1 ) {
		return;
	}
	$_image_target            = get_post_meta( $carouselID, '_image_target', true );
	$_image_target            = empty( $_image_target ) ? '_self' : $_image_target;
	$_image_size              = get_post_meta( $carouselID, '_image_size', true );
	$_lazy_load_image         = get_post_meta( $carouselID, '_lazy_load_image', true );
	$_show_attachment_title   = get_post_meta( $carouselID, '_show_attachment_title', true );
	$_show_attachment_caption = get_post_meta( $carouselID, '_show_attachment_caption', true );
	$_show_lightbox           = get_post_meta( $carouselID, '_image_lightbox', true );
	$html .= '<div class="carousel-grid-outer carousel-grid-outer-images carousel-grid-outer-'.$carouselID.'">';
    $html .= '<div>';

		foreach ( $images_ids as $image_id ):

			$_post = get_post( $image_id );	
			$image_title       = $_post->post_title;
			$image_caption     = $_post->post_excerpt;
			$image_description = $_post->post_content;
			$image_alt_text    = trim( strip_tags( get_post_meta( $image_id, '_wp_attachment_image_alt', true ) ) );
			$image_link_url    = get_post_meta( $image_id, "_carousel_slider_link_url", true );

			$html .= '<div class="carousel-grid__item">';

			$title   = sprintf( '<h4 class="title">%1$s</h4>', $image_title );
			$caption = sprintf( '<p class="caption">%1$s</p>', $image_caption );

			if ( $_show_attachment_title == 'on' && $_show_attachment_caption == 'on' ) {

				$full_caption = sprintf( '<div class="carousel-grid__caption">%1$s%2$s</div>', $title, $caption );

			} elseif ( $_show_attachment_title == 'on' ) {

				$full_caption = sprintf( '<div class="carousel-grid__caption">%s</div>', $title );

			} elseif ( $_show_attachment_caption == 'on' ) {

				$full_caption = sprintf( '<div class="carousel-grid__caption">%s</div>', $caption );

			} else {
				$full_caption = '';
			}

			if ( $_lazy_load_image == 'on' ) {

				$image_src = wp_get_attachment_image_src( $image_id, $_image_size );
				/*$image     = sprintf(
					'<img class="owl-lazy" data-src="%1$s" width="%2$s" height="%3$s" alt="%4$s" />',
					$image_src[0],
					$image_src[1],
					$image_src[2],
					$image_alt_text
				);*/
				$image     = sprintf(
					'<img class="owl-lazy" data-src="%1$s" width="250" height="166" alt="%2$s" />',
					$image_src[0],
					$image_alt_text
				);

			} else {
				$image = wp_get_attachment_image( $image_id, $_image_size, false, array( 'alt' => $image_alt_text ) );
			}
			/*custom update*/
			if ( $_show_lightbox == 'on' && filter_var( $image_link_url, FILTER_VALIDATE_URL )) {
				wp_enqueue_script( 'magnific-popup' );
				$image_src = wp_get_attachment_image_src( $image_id, 'full' );								
				$html .= sprintf( '<a href="%1$s" class="magnific-popup video-img-icon"><span class="overlay-icon"><span class="icon-overlay-inside"></span></span>%2$s%3$s</a>', esc_url( $image_link_url ), $image, $full_caption, $id );
			} elseif( $_show_lightbox == 'on' ) {
				wp_enqueue_script( 'magnific-popup' );
				$image_src = wp_get_attachment_image_src( $image_id, 'full' );
				$html .= sprintf( '<a href="%1$s" class="magnific-popup">%2$s%3$s</a>', esc_url( $image_src[0] ), $image, $full_caption, $id );
			} elseif ( filter_var( $image_link_url, FILTER_VALIDATE_URL ) ) {

				$html .= sprintf( '<a href="%1$s" target="%4$s">%2$s%3$s</a>', esc_url( $image_link_url ), $image, $full_caption, $_image_target );

			} else {

				$html .= $image . $full_caption;
			}

			$html .= '</div>';

		endforeach;
    $html .='</div>';
	$html .='</div>';
	
	return $html;	
}
add_shortcode('carousel_videos_grid','home_carousel_videos_to_knowledge_videos');


function avia_remove_main_menu_flags(){
	remove_filter( 'wp_nav_menu_items', 'avia_append_lang_flags', 9998, 2 );
	remove_filter( 'avf_fallback_menu_items', 'avia_append_lang_flags', 9998, 2 );
	remove_action( 'avia_meta_header', 'avia_wpml_language_switch', 10);
}
add_action('after_setup_theme','avia_remove_main_menu_flags');

//Remove tool bar for subscribers
function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}
add_action('after_setup_theme', 'remove_admin_bar');

function wrap_id_content_shortcode_callback($atts, $content){
	$output = '<section id="'.$atts['id'].'" data-name="'.$atts['name'].'" class="jbe-anchor">' . $content . '</section>';
	return $output;
}
add_shortcode('wrap_id','wrap_id_content_shortcode_callback');

/*function my_search_form( $form ) {
    $form = '<form role="search" method="get" id="searchform" class="searchform" action="' . home_url( '/' ) . '" >
    <div><label class="screen-reader-text" for="s">' . __( 'Search for:' ) . '</label>
    <input type="text" value="' . get_search_query() . '" name="s" id="s" />
    <input type="submit" id="searchsubmit" value="'. esc_attr__( 'Search' ) .'" />
    </div>
    </form>';

    return $form;
}

add_filter( 'get_search_form', 'my_search_form', 100 );*/

add_filter('avf_title_args', 'fix_single_post_title_h1', 100, 2);
function fix_single_post_title_h1($args,$id)
{
    $args['title'] = get_the_title($id);
	if($args['title'] == "How to prepare for oral argument" || $args['title'] == "What to do the day of oral argument"){
		$args['link'] = "";
		$args['heading'] = 'h1';		
	}
	return $args;
}
function defer_parsing_of_js ( $url ) {
if ( FALSE === strpos( $url, '.js' ) ) return $url;
if ( strpos( $url, 'jquery.js' ) || strpos( $url, 'qtip' ) || strpos( $url, 'directory' ) ) return $url;
return "$url' defer ";
}
//add_filter( 'clean_url', 'defer_parsing_of_js', 11, 1 );
add_filter( 'manage_pages_columns', 'gt_posts_column_views' );
add_action( 'manage_pages_custom_column', 'gt_posts_custom_column_views' );
//add_action( 'wp_footer', 'gt_set_post_view' );

function gt_get_post_view() {
    $count = get_post_meta( get_the_ID(), 'post_views_count', true );
    if($count=='')$count=0;
    return "$count";
}
function gt_set_post_view() {
    $key = 'post_views_count';
    if ( isset ( $_POST['id'] ) ) {
        $post_id =  $_POST['id'];
    }
    //$post_id = get_the_ID();
    $count = (int) get_post_meta( $post_id, $key, true );
    $count++;
    update_post_meta( $post_id, $key, $count );
    echo json_encode ($post_id);
    die();
}
function gt_posts_column_views( $columns ) {
    $columns['post_views'] = 'Views';
    return $columns;
}
function gt_posts_custom_column_views( $column ) {
    if ( $column === 'post_views') {
        echo gt_get_post_view();
    }
}
add_action('wp_ajax_set_post_view', 'gt_set_post_view');
add_action ( 'wp_ajax_nopriv_set_post_view', 'gt_set_post_view' );

