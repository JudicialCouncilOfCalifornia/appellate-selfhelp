<?php
/*
Plugin Name: Jbe Prepare Doc
Description: Jbe Prepare Documentation
Version: 1.0
Author: Yugandhararao T
Author URI: https://jbe.azurewebsites.net/
Text Domain: Jbe
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
//do stuff on activation
function jbe_create_db(){
	if (!file_exists('wp-content/uploads/jbe_global_xmls')) {
		mkdir('wp-content/uploads/jbe_global_xmls', 0777, true);
	}
	if(!current_user_can('activate_plugins'))return;
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . "prepare_doc_case";
	$sql = "CREATE TABLE $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
            case_id INT(10) NOT NULL,
			case_name varchar(200) NOT NULL,
			user_type varchar(10) NOT NULL,
			user_id INT(10) NOT NULL,		
			timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate; ";
	$table_name = $wpdb->prefix . "prepare_doc_forms";
	$sql = "CREATE TABLE $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			form_name varchar(200) NOT NULL,
			form_url varchar(200) NOT NULL,
            form_id varchar(200) NOT NULL,
			timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate; ";
	$table_name = $wpdb->prefix . "prepare_doc_case_forms";
	$sql = "CREATE TABLE $table_name (
			id INT(10) NOT NULL AUTO_INCREMENT,
			case_id INT(10) NOT NULL,
			user_id INT(10) NOT NULL,
			userdataID varchar(200) NOT NULL,
			timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY userdataID (userdataID),
			PRIMARY KEY (id)
		) $charset_collate; ";
	dbDelta($sql);
}
register_activation_hook( __FILE__, 'jbe_create_db' );

//do stuff on deactivation
function plugin_deactivation(){
	if(!current_user_can('activate_plugins'))return;
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'plugin_deactivation' );

//do stuff on uninstall
function delete_prepare_doc_db_tables(){
	if(!current_user_can('activate_plugins'))return;
 global $wpdb;
    $tableArray = [   
          $wpdb->prefix . "prepare_doc_case",
			$wpdb->prefix . "prepare_doc_forms",
			$wpdb->prefix . "prepare_doc_case_forms"
    ];
    foreach ($tableArray as $tablename) {
        $wpdb->query("DROP TABLE IF EXISTS $tablename");
    }	
}
register_uninstall_hook( __FILE__, 'delete_prepare_doc_db_tables' );
class PD_Plugin {

	// class instance
	static $instance;

	// Aem Forms WP_List_Table object
	public $aem_form_obj;
	public $user;

	// class constructor
	public function __construct() {		
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
		add_action( 'admin_init', [ $this, 'page_init' ] );
		//add_action( 'admin_enqueue_scripts', [ $this, 'load_custom_wp_admin_style'] );
		$current_user = wp_get_current_user();
		$this->user = $current_user->user_login;		
	}	
	public function load_custom_wp_admin_style() {
		wp_enqueue_style( 'jbe_css', plugins_url('/css/case-css.css', __FILE__) );
        wp_enqueue_style( 'jbe_css' );
		wp_enqueue_script( 'jbe_js', plugins_url('/js/case-script.js', __FILE__) );
		wp_enqueue_script( 'jbe_js' );
	}
		
	public function plugin_menu() {
		add_menu_page(
			'Prepare doc',
			'Prepare doc',
			'manage_options',
			'prepare_doc_data',
			[ $this, 'plugin_settings_page' ]
		);
		add_submenu_page( 
		'prepare_doc_data', 
		'Forms Create', 
		'Forms Create',
		'manage_options', 
		'prepare_doc_forms_create',
		[ $this, 'prepare_doc_forms_create' ]);
		add_submenu_page( 
		'prepare_doc_data', 
		'Forms Update', 
		'Forms Update',
		'manage_options', 
		'prepare_doc_forms_update',
		[ $this, 'prepare_doc_forms_update' ]);
		add_submenu_page( 
		'prepare_doc_data', 
		'Forms', 
		'Forms',
		'manage_options', 
		'prepare_doc_forms',
		[ $this, 'plugin_forms_page' ]);
	}
	public function plugin_settings_page() {			
		 // Set class property
        $this->options = get_option( 'prepare_doc_info' );
        ?>
        <div class="wrap">
            <h1>My Settings</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'prepare_doc' );
                do_settings_sections( 'prepare-doc-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
	}
	public function plugin_forms_page() {	
		
        require_once plugin_dir_path( __FILE__ ).'/includes/jbe-forms-list.php';
		
		$this->aem_form_obj = new AEM_Forms_List();	
							
		?>
		<div class="wrap">
		<h2><?php _e('AEM Forms', 'we')?> <a class="add-new-h2"
										href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page=prepare_doc_forms_create');?>"><?php _e('Add new', 'we')?></a>
		</h2>			
		<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form method="post">								
									<?php 
										$this->aem_form_obj->prepare_items();
										$this->aem_form_obj->display();  
									?>
								</form>
							</div>
						</div>
					</div>
					<br class="clear">
				</div>
	</div>
	<?php 
	}
	public function prepare_doc_forms_create(){
		require_once plugin_dir_path( __FILE__ ).'/includes/jbe-forms-create.php';
	}
	public function prepare_doc_forms_update(){
		require_once plugin_dir_path( __FILE__ ).'/includes/jbe-forms-update.php';
	}
	public function page_init() {        
        register_setting(
            'prepare_doc', // Option group
            'prepare_doc_info', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );
        add_settings_section(
            'prepare_doc_id', // ID
            'Prepare Doc Data:', // Title
            array( $this, 'print_section_info' ), // Callback
            'prepare-doc-admin' // Page
        );
        add_settings_field(
            'intro', 
            'Introduction', 
            array( $this, 'intro_callback' ), 
            'prepare-doc-admin', 
            'prepare_doc_id'
        ); 
		add_settings_field(
            'video_url', 
            'Video Url', 
            array( $this, 'video_url_callback' ), 
            'prepare-doc-admin', 
            'prepare_doc_id'
        );
		add_settings_field(
            'video_image', 
            'Video Thumb image Url', 
            array( $this, 'video_image_callback' ), 
            'prepare-doc-admin', 
            'prepare_doc_id'
        );	
		add_settings_field(
            'video_title', 
            'Video Title', 
            array( $this, 'video_title_callback' ), 
            'prepare-doc-admin', 
            'prepare_doc_id'
        );
        add_settings_field(
            'aem_forms_server_url', 
            'Aem Forms Sever Url', 
            array( $this, 'aem_forms_server_callback' ), 
            'prepare-doc-admin', 
            'prepare_doc_id'
        );
		add_settings_field(
            'aem_forms_directory', 
            'Aem Forms Directory Url', 
            array( $this, 'aem_forms_directory_callback' ), 
            'prepare-doc-admin', 
            'prepare_doc_id'
        );
        
    }
 /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();      

        if( isset( $input['intro'] ) )
            $new_input['intro'] = sanitize_textarea_field( $input['intro'] );		
		if( isset( $input['video_url'] ) )
            $new_input['video_url'] = sanitize_text_field( $input['video_url'] );
		if( isset( $input['video_image'] ) )
            $new_input['video_image'] = sanitize_text_field( $input['video_image'] );
		if( isset( $input['video_title'] ) )
            $new_input['video_title'] = sanitize_text_field( $input['video_title'] );
        if( isset( $input['aem_forms_server_url'] ) )
            $new_input['aem_forms_server_url'] = sanitize_text_field( $input['aem_forms_server_url'] );
		if( isset( $input['aem_forms_directory'] ) )
            $new_input['aem_forms_directory'] = sanitize_text_field( $input['aem_forms_directory'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter information below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function intro_callback()
    {      
		printf(
            '<textarea cols="100" rows="2" name="prepare_doc_info[intro]">%s</textarea>',
            isset( $this->options['intro'] ) ? esc_attr( $this->options['intro']) : ''
        );
    }
	public function video_url_callback()
    {      
		printf(
            '<input type="text" id="video_url" name="prepare_doc_info[video_url]" value="%s" />',
            isset( $this->options['video_url'] ) ? esc_attr( $this->options['video_url']) : ''
        );
    }	
	public function video_image_callback()
    {      
		printf(
            '<input type="text" id="video_image" name="prepare_doc_info[video_image]" value="%s" />',
            isset( $this->options['video_image'] ) ? esc_attr( $this->options['video_image']) : ''
        );
    }
	public function video_title_callback()
    {      
		printf(
            '<input type="text" id="video_title" name="prepare_doc_info[video_title]" value="%s" />',
            isset( $this->options['video_title'] ) ? esc_attr( $this->options['video_title']) : ''
        );
    }	
    public function aem_forms_server_callback()
    {      
		printf(
            '<input type="text" id="aem_forms_server_url" name="prepare_doc_info[aem_forms_server_url]" value="%s" />',
            isset( $this->options['aem_forms_server_url'] ) ? esc_attr( $this->options['aem_forms_server_url']) : ''
        );
    }
	public function aem_forms_directory_callback()
    {      
		printf(
            '<input type="text" id="aem_forms_directory" name="prepare_doc_info[aem_forms_directory]" value="%s" />',
            isset( $this->options['aem_forms_directory'] ) ? esc_attr( $this->options['aem_forms_directory']) : ''
        );
    }
	
	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
add_action( 'plugins_loaded', function () {
	PD_Plugin::get_instance();
} );
function frontend_css_js() {
	if ( is_admin() ) return;
	wp_enqueue_style( 'jbe_css', plugins_url('/css/case-css.css', __FILE__) );
    //wp_enqueue_style( 'jbe_css' );
	wp_enqueue_script( 'jbe_js', plugins_url('/js/case-script.js', __FILE__) );
	//wp_enqueue_script( 'jbe_js' );
}
add_action( 'wp_head', 'frontend_css_js' );	
function frontend_prepare_doc_case_create(){	
	require_once plugin_dir_path( __FILE__ ).'/includes/jbe-case-create.php';
	wp_enqueue_script( 'jbe_js' );
	wp_enqueue_style( 'jbe_css' );
}
/*shortcode calling*/
add_shortcode('prepare_doc_case', 'frontend_prepare_doc_case_create');
function frontend_prepare_doc_case_forms(){	
	require_once plugin_dir_path( __FILE__ ).'/includes/jbe-case-forms-list.php';
	wp_enqueue_script( 'jbe_js' );
	wp_enqueue_style( 'jbe_css' );
}
/*shortcode calling*/
add_shortcode('prepare_doc_case_forms_list', 'frontend_prepare_doc_case_forms');
    
function frontend_prepare_doc_case_form(){	
	require_once plugin_dir_path( __FILE__ ).'/includes/jbe-case-form.php';
	wp_enqueue_script( 'jbe_js' );
	wp_enqueue_style( 'jbe_css' );
}
/*shortcode calling*/
add_shortcode('prepare_doc_case_form', 'frontend_prepare_doc_case_form');

function frontend_prepare_doc_case_global_form(){	
	require_once plugin_dir_path( __FILE__ ).'/includes/jbe-case-global-form.php';
	wp_enqueue_script( 'jbe_js' );
	wp_enqueue_style( 'jbe_css' );
}
/*shortcode calling*/
add_shortcode('prepare_doc_case__global_form', 'frontend_prepare_doc_case_global_form');
