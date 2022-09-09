<?php
/**
 * Adds logic for special pages like 404, maintainance mode, footer page
 *
 * @since 4.5.2
 * @author guenter
 */
if ( ! defined( 'ABSPATH' ) )   { exit; }		    // Exit if accessed directly

if( ! class_exists( 'Avia_Custom_Pages' ) )
{
	class Avia_Custom_Pages 
	{
		/**
		 * @since 4.5.2
		 * @var Avia_Custom_Pages 
		 */
		static private $_instance = null;
		
		
		/**
		 * Returns the only instance
		 * 
		 * @since 4.5.2
		 * @return Avia_Custom_Pages
		 */
		static public function instance()
		{
			if( is_null( Avia_Custom_Pages::$_instance ) )
			{
				Avia_Custom_Pages::$_instance = new Avia_Custom_Pages();
			}
			
			return Avia_Custom_Pages::$_instance;
		}
		
		/**
		 * @since 4.5.2
		 */
		protected function __construct() 
		{
			/**
			 * Callback filter to request special pages id's (e.g. used by config-yoast, config-wpml)
			 */
			add_filter( 'avf_get_special_pages_ids', array( $this, 'handler_get_special_pages_ids' ), 10, 2 );
			
			add_filter( '404_template', array( $this, 'handler_404_template' ), 999, 1 );
			add_action( 'template_include', array( $this, 'handler_maintenance_mode' ), 4000, 1 );
			add_filter( 'template_include', array( $this, 'handler_force_reroute_to_404' ), 5000, 1 );
			
			add_action( 'admin_bar_menu', array( $this, 'handler_maintenance_mode_admin_info' ), 100 );
			add_filter( 'display_post_states', array( $this, 'handler_display_post_state' ), 10, 2 );
			add_filter( 'avf_builder_button_params', array( $this, 'handler_special_page_message' ), 10000, 1 );
			
			add_action( 'pre_get_posts', array( $this, 'handler_hide_special_pages' ), 10, 1 );
			add_filter( 'wp_list_pages_excludes', array( $this, 'handler_wp_list_pages_excludes' ), 10, 1 );
			add_filter( 'avf_ajax_search_query',  array( $this, 'handler_avf_ajax_search_exclude_pages' ), 10, 1 );
		}
		
		
		/**
		 * Error 404 - Reroute to a Custom Page
		 * Hooks into the 404_template filter and performs a redirect to that page.
		 * 
		 * @author tinabillinger - modified by günter
		 * @since 4.3 - 4.4.2 - 4.5.2
		 * 
		 * @param string $template
		 * @return string
		 */
		public function handler_404_template( $template )
		{
			if( avia_get_option( 'error404_custom' ) != 'error404_custom' )
			{
				return $template;
			}

			/**
			 * Allow 3rd party (e.g. translation plugins) to change the page id
			 * (WPML already returns correct ID from avia_get_option !)
			 * 
			 * @used_by					currently unused
			 * @since 4.5.2
			 * @param int $page_id
			 * @param string $template
			 * @return int|false
			 */
			$error404_page = apply_filters( 'avf_custom_404_page_id', avia_get_option( 'error404_page' ), $template );

			if( empty( $error404_page ) || ! is_numeric( $error404_page ) || ! get_post( $error404_page ) instanceof WP_Post )
			{
				return $template;
			}

			/**
			 * Removed with 4.5.2 - kept for a possible fallback - can be removed in future versions
			 * Returns correctly the 404 with the rerouted page, but changes browser URL - not good for SEO 
			 */
//			$error404_url = get_permalink( $error404_page );
//			if( wp_redirect( $error404_url ) ) 
//			{
//				exit();
//			}

			return $this->modify_page_query( $error404_page );
		}
	
		/**
		 * Reroute to 404 if user wants to access a page he is not allowed to
		 * Currently only a page that is selected to be used as footer
		 *  
		 * @since 4.2.7
		 * @added_by Günter
		 * @param string $original_template 
		 * @return string 
		 */
		public function handler_force_reroute_to_404( $original_template )
		{
			global $avia_config, $wp_query;

			if( isset( $avia_config['modified_main_query'] ) && $avia_config['modified_main_query'] instanceof WP_Query )
			{
				return $original_template;
			}

			/**
			 * Get all pages that are not allowed to be accessed directly
			 * 
			 * @used_by					Avia_Custom_Pages						10
			 * @used_by					enfold\config-wpml\config.php			20
			 * @since 4.5.1
			 */
			$special_pages = apply_filters( 'avf_get_special_pages_ids', array(), 'page_load' );

			if( empty( $special_pages ) )
			{
				return $original_template;
			}

			$id = get_the_ID();

			if( ( false === $id ) || ! in_array( $id, $special_pages ) )
			{
				return $original_template;
			}

			if( is_user_logged_in() && current_user_can( 'edit_pages' ) )
			{
				return $original_template;
			}

			status_header( 404 );
			$new_template = $this->handler_404_template( $original_template );

			if( isset( $avia_config['modified_main_query'] ) && $avia_config['modified_main_query'] instanceof WP_Query )
			{
				return $new_template;
			}

			$wp_query->set_404();
			status_header( 404 );
			get_template_part( '404' );
			exit;
		}
		
		/**
		 * Custom Maintenance Mode Page
		 * 
		 * Returns a 503 (temporary unavailable) status header.
		 * If user forgets to set a page we return a simple message.
		 * 
		 * Logged in users with "edit_published_posts" capability are still able to view the site
		 * 
		 * @author tinabillinger  modified by günter
		 * @since 4.3 / 4.4.2 / 4.5.2
		 * @param string $template
		 * @return string
		 */
		public function handler_maintenance_mode( $template )
		{
			if( is_user_logged_in() && current_user_can( 'edit_published_posts' ) )
			{
				return $template;
			}

			if( avia_get_option('maintenance_mode') != 'maintenance_mode' )
			{
				return $template;
			}

			/**
			 * Allow 3rd party (e.g. translation plugins) to change the page id
			 * (WPML already returns correct ID from avia_get_option !)
			 * 
			 * @used_by					currently unused
			 * @since 4.5.2
			 * @param int $page_id
			 * @param string $template
			 * @return int|false
			 */
			$maintenance_page = apply_filters( 'avf_maintenance_page_id', avia_get_option( 'maintenance_page' ), $template );

			if( empty( $maintenance_page ) || ! is_numeric( $maintenance_page ) || ! get_post( $maintenance_page ) instanceof WP_Post )
			{
				status_header( 503 );
				nocache_headers();
				exit( __( 'Sorry, we are currently updating our site - please try again later.', 'avia_framework' ) );
			}
			
			status_header( 503 );
			return $this->modify_page_query( $maintenance_page );

			/**
			 * Kept for a fallback - can be removed in future versions
			 * removed 4.5.2
			 */
//			if( is_page( $maintenance_page ) )
//			{
//				status_header( 503 );
//				nocache_headers();
//				return;
//			}
//
//			$maintenance_url = get_permalink( $maintenance_page );
//
//			if( wp_redirect( $maintenance_url ) )
//			{
//				exit();
//			}
		}
		
		
		/**
		 * Maintenance Mode Admin Bar Info
		 * If maintenance mode is active, an info is displayed in the admin bar
		 *
		 * @author tinabillinger
		 * @since 4.3
		 * @param WP_Admin_Bar $admin_bar
		 * @return type
		 */		
		public function handler_maintenance_mode_admin_info( WP_Admin_Bar $admin_bar )
		{
			if( avia_get_option( 'maintenance_mode' ) == 'maintenance_mode' && avia_get_option( 'maintenance_page', false ) ) 
			{
				$admin_bar->add_menu( array(
								'id'		=> 'av-maintenance',
								'title'		=> __( '<span style="color:#D54E21 !important;">Maintenance Mode Enabled</span>','avia_framework' ),
								'parent'	=> 'top-secondary',
								'href'		=> admin_url( 'admin.php?page=avia#goto_avia' ),
								'meta'		=> array(),
							));
			}

			return $admin_bar;
		}
		
		/**
		 * Returns page id's that do not belong to normal page lists 
		 * like custom 404, custom maintainence mode page, custom footer page
		 * 
		 * @since 4.5.2
		 * @param array $post_ids
		 * @param string $context
		 * @return array
		 */
		public function handler_get_special_pages_ids( $post_ids = array(), $context = '' )
		{
					// Maintenance Page
//			if( ( avia_get_option( 'maintenance_mode' ) == 'maintenance_mode' ) && ( 0 != avia_get_option( 'maintenance_page' ) ) )
			if( 0 != avia_get_option( 'maintenance_page' ) )
			{
				$post_ids[] = avia_get_option( 'maintenance_page' );
			}

					// 404 Page
//			if( ( avia_get_option( 'error404_custom' ) == 'error404_custom' ) && ( 0 != avia_get_option( 'error404_page' ) ) )
			if( 0 != avia_get_option( 'error404_page' ) )
			{
				$post_ids[] = avia_get_option( 'error404_page' );
			}

					// Footer Page
//			if( ( strpos( avia_get_option( 'display_widgets_socket' ), 'page_in_footer' ) === 0 ) && ( 0 != avia_get_option( 'footer_page' ) ) )
			if( 0 != avia_get_option( 'footer_page' ) )
			{
				$post_ids[] = avia_get_option( 'footer_page' );
			}

			$post_ids = array_unique( $post_ids, SORT_NUMERIC );
			return $post_ids;
		}		
		
		/**
		 * Remove special pages from page lists in frontend and search results list
		 * 
		 * @since 4.5.2
		 * @param WP_Query $query
		 */
		public function handler_hide_special_pages( WP_Query $query )
		{
			if( is_admin() )
			{
				return;
			}

			/**
			 * @used_by					Avia_Custom_Pages					10
			 * @used_by					config-wpml\config.php				20
			 * @since 4.5.2
			 */
			$pages = apply_filters( 'avf_get_special_pages_ids', array(), 'pre_get_posts_filter' );
			if( empty( $pages ) )
			{
				return;
			}

			if( ! is_search() )
			{
				$post_type = (array) $query->get( 'post_type' );
				if( empty( $post_type ) )
				{
					return;
				}

				$result = array_intersect( $post_type, array( 'page', 'any' ) );
				if( empty( $result ) )
				{
					return;
				}
			}

			$not_in = (array) $query->get( 'post__not_in', array() );
			$not_in = array_unique( array_merge( $not_in, $pages ), SORT_NUMERIC );

			$query->set( 'post__not_in', $not_in );
		}

		/**
		 * Exclude our special pages from page list
		 * 
		 * @since 4.5.2
		 * @param array $exclude_array
		 * @return array
		 */
		public function handler_wp_list_pages_excludes( array $exclude_array )
		{
			if( is_admin() )
			{
				return $exclude_array;
			}

			/**
			 * @used_by					Avia_Custom_Pages					10
			 * @used_by					config-wpml\config.php				20
			 * @since 4.5.2
			 */
			$pages = apply_filters( 'avf_get_special_pages_ids', array(), 'wp_list_pages_excludes' );
			
			$exclude_array = array_unique( array_merge( $exclude_array, (array)$pages ), SORT_NUMERIC );
			
			return $exclude_array;
		}
		
		/**
		 * 
		 * @since 4.5.2
		 * @param string $query_string
		 * @return string|array
		 */
		public function handler_avf_ajax_search_exclude_pages( $query_string )
		{
			$defaults = array();
			
			$query = wp_parse_args( $query_string, $defaults );
					
			/**
			 * @used_by					Avia_Custom_Pages					10
			 * @used_by					config-wpml\config.php				20
			 * @since 4.5.2
			 */
			$pages = apply_filters( 'avf_get_special_pages_ids', array(), 'avia_ajax_search' );
			if( empty( $pages ) )
			{
				return $query_string;
			}
			
			$not_in = isset( $query['post__not_in'] ) ? (array) $query['post__not_in'] : array();
			$not_in = array_unique( array_merge( $not_in, $pages ), SORT_NUMERIC );

			$query['post__not_in'] = $not_in;
			
			return $query;
		}


		/**
		 * Display a notice that a page is used as a special page (e.g. 404, maintenance, footer) and 
		 * cannot be accessed in frontend by non logged in users
		 * 
		 * @since 4.2.7
		 * @added_by Günter
		 * @param array $params
		 * @return array
		 */
		public function handler_special_page_message( array $params )
		{
			$id = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : 0;
			
			if( 0 == $id )
			{
				return $params;
			}
			
			$maintenance_page = avia_get_option( 'maintenance_page', 0 );
			$error404_page = avia_get_option( 'error404_page', 0 );
			$footer_page = avia_get_option( 'footer_page', 0 );
			
			$note = '';
			
			if( $maintenance_page == $id )
			{
				if( 'maintenance_mode' == avia_get_option( 'maintenance_mode' ) )
				{
					$note .= __( 'This page is currently selected to be displayed as maintenance mode page. (Set in Enfold &raquo; Theme Options).', 'avia_framework' );
				}
				else
				{
					$note .= __( 'This page is currently selected to be displayed as maintenance mode page but is currently not active. (Set in Enfold &raquo; Theme Options).', 'avia_framework' );
				}
			}
			else if( $error404_page == $id )
			{
				if( 'error404_custom' == avia_get_option( 'error404_custom' ) )
				{
					$note .= __( 'This page is currently selected to be displayed as custom 404 page. (Set in Enfold &raquo; Theme Options).', 'avia_framework' );
				}
				else
				{
					$note .= __( 'This page is currently selected to be displayed as custom 404 page but is currently not active. (Set in Enfold &raquo; Theme Options).', 'avia_framework' );
				}
			}
			else if( $footer_page == $id )
			{
				if( strpos( avia_get_option( 'display_widgets_socket' ), 'page_in_footer') === 0 )
				{
					$note .= __( 'This page is currently selected to be displayed as footer. (Set in Enfold &raquo; Footer).', 'avia_framework' );
				}
				else
				{
					$note .= __( 'This page is currently selected to be displayed as footer but is currently not active. (Set in Enfold &raquo; Footer).', 'avia_framework' );
				}
			}
			else
			{
				return $params;
			}

			$note .= ' ' . __( 'Therefore it can not be accessed directly by the general public in your frontend. (Logged in users who are able to edit the page can still see it in the frontend)', 'avia_framework' );

			if( ! empty( $params['note'] ) )
			{
				$note = $params['note'] . '<br /><br />' . $note;
			}

			$params['note'] = $note;
			$params['noteclass'] = '';

			return $params;
		}


		/**
		 * Post state filter
		 * On the Page Overview screen in the backend ( wp-admin/edit.php?post_type=page ) this functions appends a descriptive post state to a page for easier recognition
		 * 
		 * @since 4.3 / 4.5.2
		 * @author Kriesi / Günter
		 * @param array $post_states
		 * @param WP_Post $post
		 * @return array
		 */
		public function handler_display_post_state( $post_states, $post )
		{
			$link = admin_url( '?page=avia#goto_' );
			$label = __( 'Change', 'avia_framework' );

			// Maintenance Page
			if( avia_get_option( 'maintenance_page' ) == $post->ID ) 
			{
				$info = avia_get_option( 'maintenance_mode' ) == 'maintenance_mode' ? __( 'Active Maintenance Page', 'avia_framework' ) : __( 'Inactive Maintenance Page', 'avia_framework' );
				$post_states['av_maintain'] = $info . " <a href='{$link}avia'><small>({$label})</small></a>";
			}

			// 404 Page
			if ( avia_get_option( 'error404_page' ) == $post->ID ) 
			{
				$info = avia_get_option( 'error404_custom' ) == 'error404_custom' ? __( 'Active Custom 404 Page', 'avia_framework' ) : __( 'Inactive Custom 404 Page', 'avia_framework' );
				$post_states['av_404'] = $info . " <a href='{$link}avia'><small>({$label})</small></a>";
			}

			// Footer Page
			if ( avia_get_option( 'footer_page' ) == $post->ID ) 
			{
				$info = strpos( avia_get_option( 'display_widgets_socket' ), 'page_in_footer' ) === 0 ? __( 'Active Custom Footer Page', 'avia_framework' ) : __( 'Inactive Custom Footer Page', 'avia_framework' );
				$post_states['av_footer'] = $info . " <a href='{$link}footer'><small>({$label})</small></a>";
			}

			return $post_states;
		}
		
		/**
		 * Query the requested page, replace $wp_query and save in $avia_config['modified_main_query']
		 * Does not check, if page exists.
		 * 
		 * @since 4.5.2
		 * @param int $page_id
		 * @return string
		 */
		protected function modify_page_query( $page_id )
		{
			global $wp_query, $avia_config;
			
			/**
			 * Modify existing query to custom 404 page
			 */
			$wp_query = null;
			$wp_query = new WP_Query();
			$wp_query->query( 'page_id=' . $page_id );
			$wp_query->the_post();
			$wp_query->rewind_posts();

			/**
			 * Save query to be able to be restored later - needed when WPML is active to restore query
			 */
			$avia_config['modified_main_query'] = $wp_query;

			return get_page_template();
		}
	}
	
	/**
	 * Returns the main instance of Avia_Custom_Pages to prevent the need to use globals
	 * 
	 * @since 4.5.2
	 * @return Avia_Custom_Pages
	 */
	function AviaCustomPages() 
	{
		return Avia_Custom_Pages::instance();
	}
}

AviaCustomPages();


		
/**
 * Kept in case we need a fallback - can be removed in future versions
 * removed < 4.5 ??
 * ===================================================================
 */
//        if (avia_get_option('maintenance_mode') == "maintenance_mode") {
//            global $wp_query;
//            $maintenance_page = avia_get_option('maintenance_page');
//            
//            // check if maintenance page is defined
//            if ($maintenance_page) {
//                $maintenance_url = get_permalink($maintenance_page);
//                $current_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
//                // make sure site is accessible for logged in users, and the login page is not redirected
//                if ( ($GLOBALS['pagenow'] !== 'wp-login.php') && !current_user_can('edit_published_posts')) {
//                    // avoid infinite loop by making sure that maintenance page is NOT curently viewed
//                    if ($maintenance_url !== $current_url) {
//
//                        // do a simple redirect if WPML or Yoast is active
//                        $use_wp_redirect = false;
//
//                        if( defined('ICL_SITEPRESS_VERSION') && defined('ICL_LANGUAGE_CODE')) {
//                            $use_wp_redirect = true;
//                        }
//
//                        if( (defined('ICL_SITEPRESS_VERSION') && defined('ICL_LANGUAGE_CODE')) || defined('WPSEO_VERSION')) {
//                            $use_wp_redirect = true;
//                        }
//
//                        if( $use_wp_redirect ) {
//                            if (wp_redirect($maintenance_url)) {
//                                exit();
//                            }
//                        }
//                        else {
//                            // hook into the query
//                            $wp_query = null;
//                            $wp_query = new WP_Query();
//                            $wp_query->query('page_id=' . $maintenance_page);
//                            $wp_query->the_post();
//                            $template = get_page_template();
//                            rewind_posts();
//                            status_header(503);
//                            return $template;
//                        }
//                    }
//                }
//            }
//        }
//    }
	
