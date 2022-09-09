<?php
	/*
	Template Name: Timeline Inner
	*/
	if ( !defined('ABSPATH') ){ die(); }
	
	global $avia_config;
	/*
	 * get_header is a basic wordpress function, used to retrieve the header.php file in your theme directory.
	 */
	 get_header();

	 /*commented*/
 	/* if( get_post_meta(get_the_ID(), 'header', true) != 'no') echo avia_title();
 	 
 	 do_action( 'ava_after_main_title' );*/
	 
	 /* custom code for menu next and prev*/
	 $pagelist = get_pages("child_of=".$post->post_parent."&parent=".$post->post_parent."&sort_column=menu_order&sort_order=asc");
	 $pages = array();
	 foreach ($pagelist as $page) {
	   $pages[] += $page->ID;
	 }	
	 $current = array_search($post->ID, $pages);
	 ?>
	 <!-- custom title container -->
	<div class="stretch_full container_wrap alternate_color light_bg_color title_container timeline-nav">
		<div class="container">
			<h1 class="main-title entry-title">
				<a href="<?php echo get_page_link($pages[$current]) ?>" rel="bookmark" title="<?php echo get_the_title($pages[$current]) ?>" itemprop="headline"><?php echo ($current+1) .". ". get_the_title($pages[$current]) ?></a>
			</h1>
			<div class='breadcrumb breadcrumbs avia-breadcrumbs timeline-inner-nav'>
				<div class='breadcrumb-trail'>
					<span><a class="<?php echo ($pages[$current-1] != '' ? "link" : 'disabled') ?>" href="<?php echo ($pages[$current-1] != '' ? get_page_link($pages[$current-1]) : 'javascript:void(0)'); ?>" rel="bookmark" title="<?php echo ($pages[$current-1] != '' ? get_the_title($pages[$current-1]) : 'no link'); ?>" itemprop="headline"><img src='<?php echo get_stylesheet_directory_uri(); ?>/img/prev.png'/><div></div><span>Prev</span></a></span>
					<span><a class="<?php echo ($post->post_parent != '' ? "link" : 'disabled') ?>" href="<?php echo ($post->post_parent != '' ? get_page_link($post->post_parent) : 'javascript:void(0)'); ?>" rel="bookmark" title="<?php echo ($post->post_parent != '' ? get_the_title($post->post_parent) : 'no link'); ?>" itemprop="headline"><img src='<?php echo get_stylesheet_directory_uri(); ?>/img/timeline-blue.png'/><span>Timeline</span></a></span>
					<span><a class="<?php echo ($pages[$current+1] != '' ? "link" : 'disabled') ?>" href="<?php echo ($pages[$current+1] != '' ? get_page_link($pages[$current+1]) : 'javascript:void(0)'); ?>" rel="bookmark" title="<?php echo ($pages[$current+1] != '' ? get_the_title($pages[$current+1]) : 'no link'); ?>" itemprop="headline"><img src='<?php echo get_stylesheet_directory_uri(); ?>/img/next.png'/><span>Next</span></a></span>
				</div>
			</div>			
		</div>
	</div>
		<div class='container_wrap container_wrap_first main_color <?php avia_layout_class( 'main' ); ?>'>

			<div class='container'>

				<main class='template-page timeline-inner content  <?php avia_layout_class( 'content' ); ?> units' <?php avia_markup_helper(array('context' => 'content','post_type'=>'page'));?>>

                    <?php
                    /* Run the loop to output the posts.
                    * If you want to overload this in a child theme then include a file
                    * called loop-page.php and that will be used instead.
                    */

                    $avia_config['size'] = avia_layout_class( 'main' , false) == 'fullsize' ? 'entry_without_sidebar' : 'entry_with_sidebar';
                    get_template_part( 'includes/loop', 'page' );
                    ?>

				<!--end content-->
				</main>

				<?php

				//get the sidebar
				$avia_config['currently_viewing'] = 'page';
				get_sidebar();

				?>

			</div><!--end container-->

		</div><!-- close default .container_wrap element -->



<?php get_footer(); ?>
