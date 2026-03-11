<?php   
/**
 * shortcode Handler
 *
 * @package Mege Events Manager
 */

namespace Wpcraft\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/uem-template-functions.php';

class class_mem_registration_shortcode {
    
    /**
     * Initialize shortcode
     */
    public function __construct() {
      // Register [uem_event_registration] shortcode
        add_shortcode( 'event-add-cart-section', array( $this, 'wtmem_event_add_cart_shortcode' ) );
    }
    
    public function wtmem_is_woocommerce_enabled() {
		return get_option( 'uem_registration_method', 'woocommerce' ) === 'woocommerce';
	}
    
    /**
     * Render event registration form via shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string HTML output of the registration form.
     */   

        /* ==== Shortcode register ===== */

	public function wtmem_event_add_cart_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'event' => '',
			),
			$atts,
			'event-add-cart-section'
		);
		
		$event_id = get_the_ID();
		if ( ! empty( $atts['event'] ) ) {
			$event_id = intval( $atts['event'] );
		}		

		//$event_id = intval( $atts['event'] );
		
		if ( empty( $event_id ) ) {
			return '<p>Please provide a valid event ID.</p>';
		}
		
		ob_start();
		
		$is_woocommerce = class_exists( 'WooCommerce' );
		$tickets = [];
		$tickets = get_post_meta( $event_id, '_wtmem_tk_tickets', true );

        $is_woocommerce = $this->wtmem_is_woocommerce_enabled();

        error_log('Registration method - is Woocommerce: ' . ($is_woocommerce ? 'yes' : 'no'));


		if ( $is_woocommerce ) {
			wtmem_uem_render_woocommerce_registration( $event_id, $tickets );
		} else {
			wtmem_uem_render_simple_registration( $event_id, $tickets );
		}

		$output = ob_get_clean();
		
		return $output;
	}
}

new class_mem_registration_shortcode();
	