<?php
/**
 * Plugin Name: Mega Events Manager
 * Plugin URI: https://example.com/mega-events-manager
 * Description: A comprehensive event management plugin with WooCommerce and non-WooCommerce registration options.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: mega-events-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define plugin constants
define( 'UEM_VERSION', '1.0.0' );
define( 'UEM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'UEM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'UEM_PLUGIN_FILE', __FILE__ );
define( 'MEM_EVENT_ASSETS', UEM_PLUGIN_URL . '/assets' );



/**
 * Main plugin class
 */
class Ultimate_Events_Manager {
	
	/**
	 * Instance of this class
	 *
	 * @var Ultimate_Events_Manager
	 */
	private static $instance = null;
	
	/**
	 * Get instance of this class
	 *
	 * @return Ultimate_Events_Manager
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init();
	}
	/**
	* Initialize plugin
	*/

	private function init() {
		// Load plugin files
		$this->webcu_load_dependencies();
		
		// Initialize components
		add_action( 'init', array( $this, 'webcu_load_textdomain' ) );
		add_action( 'init', array( 'UEM_Post_Types', 'init' ), 5 );
		add_action( 'init', array( 'UEM_Meta_Boxes', 'init' ) );
		add_action( 'admin_init', array( 'UEM_Settings', 'init' ) );
		add_action( 'admin_menu', array( 'UEM_Settings', 'add_settings_page' ) );
		//organizer
		new class_organizer_meta_box();
		//volenteers
		new class_volunteer_custom_metabox();
		//sponser
		new class_sponser_custom_metabox();
        //event metabox
		new class_event_custom_metabox();
		//event template
		new Class_mem_event_template();
		
		new class_event_manager_widget();
		
		// Initialize WooCommerce integration if enabled
		// Register AJAX handlers early
		add_action( 'wp_ajax_webcu_uem_update_cart', array( 'UEM_WooCommerce', 'webcu_ajax_update_cart' ) );
		add_action( 'wp_ajax_nopriv_webcu_uem_update_cart', array( 'UEM_WooCommerce', 'webcu_ajax_update_cart' ) );
		
		// Initialize WooCommerce integration
		if ( $this->webcu_is_woocommerce_enabled() ) {
			// Register on init hook with early priority
			// The init method will check for WooCommerce availability
			add_action( 'init', array( 'UEM_WooCommerce', 'init' ), 5 );
			
		}
		// Initialize non-WooCommerce registration
		add_action( 'wp', array( 'UEM_Registration', 'init' ) );
		
		// Enqueue assets
		add_action( 'wp_enqueue_scripts', array( $this, 'webcu_enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'webcu_enqueue_admin_assets' ) );
		
		//Template loader
		add_filter( 'single_template', array( $this, 'webcu_load_event_template' ) );
		add_filter( 'template_include', array( $this, 'webcu_load_thank_you_template' ) );
	}
	
	/**
	 * Load plugin dependencies
	 */
	private function webcu_load_dependencies() {
		require_once UEM_PLUGIN_DIR . 'includes/class-uem-post-types.php';
		require_once UEM_PLUGIN_DIR . 'settings/class-uem-settings.php';
		require_once UEM_PLUGIN_DIR . 'settings/class-uem-create-dynamic-taxonomy.php';
		require_once UEM_PLUGIN_DIR . 'settings/class-uem-currency-setting.php';
		require_once UEM_PLUGIN_DIR . 'settings/class-uem-woocommerce-inte.php';
		require_once UEM_PLUGIN_DIR . 'settings/class-mem-event-template.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-uem-organizer-metabox.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-uem-volenteers-metabox.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-uem-sponsers-metabox.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-uem-country-list.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-event-metabox.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_attendee_form.php';  
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_dateTime_section.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_emails_section.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_faq_section.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_registration_form.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_richtext_section.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_terms_conditions.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_ticket_price.php'; 
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_timeline_details.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_venue_location.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_event_associate.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-event-meta-photogallery.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class_meta_settings_sections.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-event-meta-photogallery.php';
		require_once UEM_PLUGIN_DIR . 'metabox/class-event-meta-photogallery.php';
		require_once UEM_PLUGIN_DIR . 'widget/class_event_manager_widget.php';

		require_once UEM_PLUGIN_DIR . 'includes/class-uem-meta-boxes.php';
		require_once UEM_PLUGIN_DIR . 'includes/class-uem-woocommerce.php';
		require_once UEM_PLUGIN_DIR . 'includes/class-uem-registration.php';
		require_once UEM_PLUGIN_DIR . 'includes/class-uem-ajax.php';
		require_once UEM_PLUGIN_DIR . 'includes/class-uem-template-loader.php';
		require_once UEM_PLUGIN_DIR . 'includes/uem-template-functions.php';
	}
	
	/**
	 * Load plugin textdomain
	 */
	public function webcu_load_textdomain() {
		load_plugin_textdomain( 'ultimate-events-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	/**
	 * Check if WooCommerce registration is enabled
	 *
	 * @return bool
	 */
	public function webcu_is_woocommerce_enabled() {
		return get_option( 'uem_registration_method', 'woocommerce' ) === 'woocommerce';
	}
	
	/**
	 * Enqueue frontend assets
	 */
	public function webcu_enqueue_frontend_assets() {
		if ( is_singular( 'mem_event' ) || is_singular( 'mem_sponsor' )  || is_singular( 'mem_organizer' )  || is_singular( 'mem_volunteer' ) || is_page_template( 'mem-thank-you.php' ) ) {
			wp_enqueue_style( 'mem-frontend', UEM_PLUGIN_URL . 'assets/css/frontend.css', array(), UEM_VERSION );
			wp_enqueue_style( 'mem-template-css', UEM_PLUGIN_URL . 'assets/css/template.css', array(), UEM_VERSION );

			wp_enqueue_script( 'mem-frontend', UEM_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), UEM_VERSION, true );
			
			wp_localize_script( 'mem-frontend', 'uemData', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'uem_nonce' ),
				'isWooCommerce' => $this->webcu_is_woocommerce_enabled() && class_exists( 'WooCommerce' ),
			) );

			wp_enqueue_script('vimeo-player','https://player.vimeo.com/api/player.js', array(), null, true);  
			
			// Enqueue WooCommerce scripts if needed
			if ( $this->webcu_is_woocommerce_enabled() && class_exists( 'WooCommerce' ) && is_singular( 'uem_event' ) ) {
				if ( function_exists( 'WC' ) ) {
					WC()->frontend_includes();
					if ( is_checkout() || is_cart() ) {
						// These are already loaded, but ensure they're available
					} else {
						// Load checkout scripts on event page
						wp_enqueue_script( 'wc-checkout' );
						wp_enqueue_script( 'wc-cart' );
					}
				}
			}
		}
	}
	
	/**
	 * Enqueue admin assets
	 */
	public function webcu_enqueue_admin_assets( $hook ) {
		$screen = get_current_screen();
		//if ( $screen && in_array( $screen->post_type, array( 'uem_event', 'uem_organizer', 'uem_volunteer', 'uem_sponsor', 'uem_registration' ) ) ) {
			wp_enqueue_script('jquery');
			wp_enqueue_style( 'uem-admin', UEM_PLUGIN_URL . 'assets/css/admin.css', array(), UEM_VERSION );
			wp_enqueue_script( 'search-select', UEM_PLUGIN_URL . 'assets/js/jquery-searchbox.js',  array('jquery'), time(), true);
			
			wp_enqueue_script( 'uem-admin', UEM_PLUGIN_URL . 'assets/js/admin.js',  array('jquery'), time(), true);
			wp_localize_script('uem-admin', 'ajax_ob', array(
			  'counter'  => $counter ? $counter : 1,
			) );      
		//}
	}
	
	/**
	 * Load event template
	 *
	 * @param string $template
	 * @return string
	 */
	public function webcu_load_event_template( $template ) {
		if ( is_singular( 'mem_event' ) ) {
			$custom_template = UEM_PLUGIN_DIR . 'templates/single-mem_event.php';
			if ( file_exists( $custom_template ) ) {
				return $custom_template;
			}
		}

		//if ( $post->post_type == 'mem_sponsor' ) {
		if ( is_singular( 'mem_sponsor' ) ) {		
        $template = plugin_dir_path( __FILE__ ) . 'templates/single-mem_sponsor.php';

            if ( file_exists( $template ) ) {
                return $template;
            }
        }

        if ( is_singular( 'mem_volunteer' )) {

        $template = plugin_dir_path( __FILE__ ) . 'templates/single-mem_volunteer.php';

            if ( file_exists( $template ) ) {
                return $template;
            }
        }

        if (is_singular( 'mem_organizer' )) {  

        $template = plugin_dir_path( __FILE__ ) . 'templates/single-mem_organizer.php';

            if ( file_exists( $template ) ) {
                return $template;
            }
        }
		


		return $template;
	}
	
	/**
	 * Load thank you template
	 *
	 * @param string $template
	 * @return string
	 */
	public function webcu_load_thank_you_template( $template ) {
		if ( isset( $_GET['uem_thank_you'] ) && isset( $_GET['registration_id'] ) ) {
			$custom_template = UEM_PLUGIN_DIR . 'templates/thank-you.php';
			if ( file_exists( $custom_template ) ) {
				return $custom_template;
			}
		}
		return $template;
	}
}

/**
 * Activation hook
 */
function webcu_uem_activate() {
	// Make sure constants are defined
	if ( ! defined( 'UEM_PLUGIN_DIR' ) ) {
		define( 'UEM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}
	
	// Load dependencies first
	if ( file_exists( UEM_PLUGIN_DIR . 'includes/class-uem-post-types.php' ) ) {
		require_once UEM_PLUGIN_DIR . 'includes/class-uem-post-types.php';
		
		// Register post types
		if ( class_exists( 'UEM_Post_Types' ) ) {
			UEM_Post_Types::webcu_register_post_types();
		}
	}
	
	// Flush rewrite rules
	flush_rewrite_rules();
	
	// Set default options
	if ( ! get_option( 'uem_registration_method' ) ) {
		update_option( 'uem_registration_method', 'woocommerce' );
	}
}

/**
 * Deactivation hook
 */
function webcu_uem_deactivate() {
	flush_rewrite_rules();
}

// Register activation and deactivation hooks
register_activation_hook( __FILE__, 'webcu_uem_activate' );
register_deactivation_hook( __FILE__, 'webcu_uem_deactivate' );

// Initialize plugin
Ultimate_Events_Manager::get_instance();