<?php
/**
 * Plugin Name: Momento Event Manager
 * Plugin URI: https://example.com/momento-event-manager
 * Description: A comprehensive event management plugin with WooCommerce and non-WooCommerce registration options.
 * Version: 1.0.0
 * Author: Rashed khan 
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: momento-event-manager
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once __DIR__ . '/vendor/autoload.php';

use Wpcraft\Metabox\class_organizer_meta_box;
use Wpcraft\Metabox\class_volunteer_custom_metabox;
use Wpcraft\Metabox\class_sponser_custom_metabox;
use Wpcraft\Metabox\class_event_custom_metabox;
use Wpcraft\Metabox\class_meta_emails_section;
use Wpcraft\Inc\class_mem_post_types;
use Wpcraft\Inc\class_mem_meta_boxes;
use Wpcraft\Inc\class_mem_registration;
use Wpcraft\Inc\class_mem_woocommerce;
use Wpcraft\Settings\class_mem_settings;
use Wpcraft\Settings\Class_mem_event_template;
use Wpcraft\Widget\class_event_manager_widget;
use Wpcraft\Inc\class_mem_registration_shortcode;

/**
 * Main plugin class
 */
class momento_event_manager {

	private $emailsend;
	private $customposttype;
	/**
	 * Instance of this class
	 *
	 * @var momento_event_manager
	 */
	private static $instance = null;
	
	/**
	 * Get instance of this class
	 *
	 * @return momento_event_manager
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
		$this->wtmem_define_constants();
		$this->init();
		new class_event_custom_metabox();
	}
	/**
	* Initialize plugin
	*/
	private function init() {
	
		// Initialize components
		add_action( 'init', array( $this, 'wtmem_load_textdomain' ) );
		// Register post types and meta boxes on init hook (priority 5 to run early)
		add_action( 'init', array( $this, 'wtmem_register_post_types' ), 5 );
		add_action( 'init', array( $this, 'wtmem_register_meta_boxes' ), 10 );
		
		add_action( 'admin_init', array( class_mem_settings::class, 'init' ) );
		add_action( 'admin_menu', array( class_mem_settings::class, 'add_settings_page' ) );

		$this->emailsend = new Class_meta_emails_section();

		// Initialize WooCommerce integration if enabled
		// Register AJAX handlers early
		add_action( 'wp_ajax_wtmem_uem_update_cart', array( class_mem_woocommerce::class, 'wtmem_ajax_update_cart' ) );
		add_action( 'wp_ajax_nopriv_wtmem_uem_update_cart', array( class_mem_woocommerce::class, 'wtmem_ajax_update_cart' ) );
		
		// Initialize WooCommerce integration
		if ( $this->wtmem_is_woocommerce_enabled() ) {
			add_action( 'init', array( class_mem_woocommerce::class, 'init' ), 5 );

		}
		// Initialize non-WooCommerce registration
		add_action( 'wp', array( class_mem_registration::class, 'init' ) );
		
		// Enqueue assets
		add_action( 'wp_enqueue_scripts', array( $this, 'wtmem_enqueue_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wtmem_enqueue_admin_assets' ) );
		
		//Template loader
		add_filter( 'single_template', array( $this, 'wtmem_load_event_template' ) );
		add_filter( 'template_include', array( $this, 'wtmem_load_thank_you_template' ) );
		
	    add_action('wp_ajax_wtmem_send_email_now', [$this->emailsend, 'wtmem_send_email_now_handler']);
        add_action('wp_ajax_nopriv_wtmem_send_email_now', [$this->emailsend, 'wtmem_send_email_now_handler']);
		
	}

	/**
	 * Register custom post types
	 */
	public function wtmem_register_post_types() {
		new class_mem_post_types();
	}

	/**
	 * Register meta boxes
	 */
	public function wtmem_register_meta_boxes() {
		class_mem_meta_boxes::init();
	}
	
	/**
	 * Load plugin dependencies
	 */

	private function wtmem_load_dependencies() {}

	public function wtmem_define_constants()
	{ 
		// Define plugin constants
		define( 'MEM_VERSION', '1.0.0' );
		define( 'MEM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		define( 'MEM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		define( 'MEM_PLUGIN_FILE', __FILE__ );
		define( 'MEM_EVENT_ASSETS', MEM_PLUGIN_URL . '/assets' );
	}
	
	/**
	 * Load plugin textdomain
	 */
	public function wtmem_load_textdomain() {
		load_plugin_textdomain( 'momento-event-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	/**
	 * Check if WooCommerce registration is enabled
	 *
	 * @return bool
	 */
	public function wtmem_is_woocommerce_enabled() {
		return get_option( 'uem_registration_method', 'woocommerce' ) === 'woocommerce';
	}	
	/**
	 * Enqueue frontend assets
	*/
	public function wtmem_enqueue_frontend_assets() {
		if ( is_singular( 'mem_event' ) || is_singular( 'mem_sponsor' )  || is_singular( 'mem_organizer' )  || is_singular( 'mem_volunteer' ) || is_page_template( 'mem-thank-you.php' ) ) {
			wp_enqueue_style( 'mem-frontend', MEM_PLUGIN_URL . 'assets/css/frontend.css', array(), MEM_VERSION );
			wp_enqueue_style( 'mem-template-css', MEM_PLUGIN_URL . 'assets/css/template.css', array(), MEM_VERSION );

			wp_enqueue_script( 'mem-frontend', MEM_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), MEM_VERSION, true );

			wp_localize_script( 'mem-frontend', 'uemData', array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'uem_nonce' ),
				'isWooCommerce' => $this->wtmem_is_woocommerce_enabled() && class_exists( 'WooCommerce' ),
			) );

			wp_enqueue_script('vimeo-player','https://player.vimeo.com/api/player.js', array(), null, true);  
			
			// Enqueue WooCommerce scripts if needed
			if ( $this->wtmem_is_woocommerce_enabled() && class_exists( 'WooCommerce' ) && is_singular( 'mem_event' ) ) {
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
	public function wtmem_enqueue_admin_assets( $hook ) {
		 
		$screen = get_current_screen();
		    $counter = 0;
			wp_enqueue_script('jquery');
			wp_enqueue_style( 'uem-admin', MEM_PLUGIN_URL . 'assets/css/admin.css', array(), MEM_VERSION );
			wp_enqueue_script( 'search-select', MEM_PLUGIN_URL . 'assets/js/jquery-searchbox.js',  array('jquery'), time(), true);
			
			wp_enqueue_script( 'uem-admin', MEM_PLUGIN_URL . 'assets/js/admin.js',  array('jquery'), time(), true);
			wp_localize_script('uem-admin', 'ajax_ob', array(
			   'ajax_url' => admin_url('admin-ajax.php'),
               'nonce' => wp_create_nonce('wtmem_nonce'),	
			   'counter'  => $counter ? $counter : 1,
			) );      
	}
	
	/**
	 * Load event template
	 *
	 * @param string $template
	 * @return string
	 */

	public function wtmem_load_event_template( $template ) {

		if ( is_singular( 'mem_event' ) ) {
			$template = UEM_PLUGIN_DIR . 'templates/single-mem_event.php';
			if ( file_exists( $template ) ) {
				return $template;
			}
		}		

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
	public function wtmem_load_thank_you_template( $template ) {
		if ( isset( $_GET['uem_thank_you'] ) && isset( $_GET['registration_id'] ) ) {
			$custom_template = MEM_PLUGIN_DIR . 'templates/thank-you.php';
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
function wtmem_uem_activate() {
	// Make sure constants are defined
	if ( ! defined( 'MEM_PLUGIN_DIR' ) ) {
		define( 'MEM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}
	
	// Load dependencies first
	if ( file_exists( MEM_PLUGIN_DIR . 'includes/class-uem-post-types.php' ) ) {
		require_once MEM_PLUGIN_DIR . 'includes/class-uem-post-types.php';
		
		// Register post types
		if ( class_exists( 'UEM_Post_Types' ) ) {
			UEM_Post_Types::wtmem_register_post_types();
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
function wtmem_uem_deactivate() {
	flush_rewrite_rules();
}

// Register activation and deactivation hooks
register_activation_hook( __FILE__, 'wtmem_uem_activate' );
register_deactivation_hook( __FILE__, 'wtmem_uem_deactivate' );

// Initialize plugin
momento_event_manager::get_instance();