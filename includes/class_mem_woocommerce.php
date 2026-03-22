<?php
/**
 * WooCommerce Integration
 *
 * @package Ultimate_Events_Manager
 */

namespace Wpcraft\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class class_mem_woocommerce {
	/**
	 * Initialize WooCommerce integration
	 */
	public static function init() {
		// Prevent duplicate initialization
		static $initialized = false;
		if ( $initialized ) {
			return;
		}
		
		// Check WooCommerce availability - use AND, not OR
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		
		$initialized = true;
		
		// Create/update products when event is saved
		add_action( 'save_post_mem_event', array( __CLASS__, 'wtmem_create_ticket_products' ), 20, 2 );
		
		// Delete products when event is deleted
		add_action( 'before_delete_post', array( __CLASS__, 'wtmem_delete_ticket_products' ) );
		
		// Clear cart and add tickets when viewing event
		add_action( 'template_redirect', array( __CLASS__, 'wtmem_handle_event_page' ) );
		
		// Tell WooCommerce the event page is a checkout page (enables payment gateways & order review)
		add_filter( 'woocommerce_is_checkout', array( __CLASS__, 'wtmem_force_is_checkout' ) );
		
		// Prevent redirect on empty cart for event pages
		add_filter( 'woocommerce_checkout_redirect_empty_cart', array( __CLASS__, 'wtmem_prevent_empty_cart_redirect' ), 10, 1 );

		// Also prevent the "cart is empty" notice from hiding the checkout form
		add_filter( 'woocommerce_checkout_update_order_review_expired', array( __CLASS__, 'wtmem_prevent_checkout_expired' ) );
		
		// Display attendee fields section after billing form
		add_action( 'woocommerce_after_checkout_billing_form', array( __CLASS__, 'wtmem_display_attendee_fields_section' ), 10 );
		
		// Validate attendee fields - only use one hook to prevent duplicate errors
		//add_action( 'woocommerce_checkout_process', array( __CLASS__, 'wtmem_validate_attendee_fields' ) );
		
		// Save attendee data to order
			
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'wtmem_save_attendee_data_to_order_item' ), 10, 4 );

        add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__,'wtmem_save_attendee_data_to_as_order_item'), 10, 4 );

			/* ==== attendee data show in order details === */
		add_action( 'woocommerce_order_item_meta_end', array( __CLASS__, 'display_attendee_data_in_thank_you_page' ), 10, 3 );		

         /* Order Details info Admin Order Details page */
		add_action( 'woocommerce_after_order_itemmeta', array( __CLASS__, 'display_attendee_data_in_admin_order_edit'), 10, 3 );

	  	// AJAX handlers are registered in main plugin file
		// Update checkout fields when cart updates
		add_action( 'woocommerce_checkout_update_order_review', array( __CLASS__, 'wtmem_update_checkout_fields' ) );

    }
	
	/**
	 * Delete WooCommerce products when event is deleted
	 */
	public static function wtmem_delete_ticket_products( $post_id ) {
		// Only process event posts
		if ( get_post_type( $post_id ) !== 'mem_event' ) {
			return;
		}
		
		// Get product IDs
		$product_ids = get_post_meta( $post_id, '_uem_wc_products', true );
		if ( is_array( $product_ids ) ) {
			foreach ( $product_ids as $product_id ) {
				if ( $product_id && get_post_type( $product_id ) === 'product' ) {
					wp_delete_post( $product_id, true );
				}
			}
		}
	}
	
	/**
	 * Create/update WooCommerce products for event tickets
	 */
	public static function wtmem_create_ticket_products( $post_id, $post ) {
		// Check if WooCommerce is enabled
		if ( get_option( 'uem_registration_method', 'woocommerce' ) !== 'woocommerce' ) {
			return;
		}
		
		// Skip autosave and revisions
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		
		// Check if user has permission
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		// Get tickets
		//$tickets = get_post_meta( $post_id, '_uem_tickets', true );
		$tickets_data = get_post_meta( $post_id, '_wtmem_tk_tickets', true );

		if ( ! is_array( $tickets_data ) ) {
			$tickets_data = array();
		}
		
		// Extract regular tickets from nested structure
		$tickets = isset( $tickets_data['regular_tickets'] ) && is_array( $tickets_data['regular_tickets'] )
			? $tickets_data['regular_tickets']
			: $tickets_data;
		
		// Get existing product IDs
		$existing_products = array();
		$all_product_ids = get_post_meta( $post_id, '_uem_wc_products', true );
		if ( is_array( $all_product_ids ) ) {
			$existing_products = $all_product_ids;
		}
		
		// Create or update products for each ticket
		$product_ids = array();
		foreach ( $tickets as $index => $ticket ) {
			$product_id = self::wtmem_create_or_update_ticket_product( $post_id, $ticket, $index, isset( $existing_products[ $index ] ) ? $existing_products[ $index ] : 0 );
			if ( $product_id ) {
				$product_ids[ $index ] = $product_id;
			}
		}
		
		// Delete products for removed tickets
		foreach ( $existing_products as $index => $product_id ) {
			if ( ! isset( $product_ids[ $index ] ) && $product_id ) {
				// Ticket was removed, delete the product
				wp_delete_post( $product_id, true );
			}
		}
		
		// Save product IDs
		update_post_meta( $post_id, '_uem_wc_products', $product_ids );
	}
	
	/**
	 * Create or update WooCommerce product for a ticket
	 */
	private static function wtmem_create_or_update_ticket_product( $event_id, $ticket, $index, $existing_product_id = 0 ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return false;
		}
		
		$event_title = get_the_title( $event_id );
		$product_name = $ticket['name'] . ' - ' . $event_title;
		
		// Check if product exists and is valid
		if ( $existing_product_id && get_post_type( $existing_product_id ) === 'product' ) {
			// Update existing product
			$product = wc_get_product( $existing_product_id );
			if ( $product ) {
				$product->set_name( $product_name );
				$product->set_price( $ticket['price'] );
				$product->set_regular_price( $ticket['price'] );
				$product->set_description( ! empty( $ticket['description'] ) ? $ticket['description'] : '' );
				$product->save();
				return $existing_product_id;
			}
		}
		
		// Create new product
		$product = new \WC_Product_Simple();
		$product->set_name( $product_name );
		$product->set_price( $ticket['price'] );
		$product->set_regular_price( $ticket['price'] );
		$product->set_description( ! empty( $ticket['description'] ) ? $ticket['description'] : '' );
		$product->set_virtual( true );
		$product->set_sold_individually( false );
		$product->set_manage_stock( false );
		$product->set_status( 'publish' );
		
		$product_id = $product->save();
		
		if ( $product_id ) {
			// Store event and ticket info in product meta
			update_post_meta( $product_id, '_uem_event_id', $event_id );
			update_post_meta( $product_id, '_uem_ticket_index', $index );
			update_post_meta( $product_id, '_uem_ticket_name', $ticket['name'] );
		}
		
		return $product_id;
	}
	
	/**
	 * Handle event page - clear cart and add tickets
	 */
	public static function wtmem_handle_event_page() {
		if ( ! is_singular( 'mem_event' ) ) {
			return;
		}
		
		global $post;
		
		// Clear cart
		if ( function_exists( 'WC' ) && WC()->cart ) {
			WC()->cart->empty_cart();
		}
		
		// Get event tickets
		//$tickets = get_post_meta( $post->ID, '_uem_tickets', true );
		$tickets_data = get_post_meta( $post->ID, '_wtmem_tk_tickets', true );
		if ( ! is_array( $tickets_data ) || empty( $tickets_data ) ) {
			return;
		}
		
		// Extract regular tickets from nested structure
		$tickets = isset( $tickets_data['regular_tickets'] ) && is_array( $tickets_data['regular_tickets'] )
			? $tickets_data['regular_tickets']
			: $tickets_data;
		
		if ( empty( $tickets ) ) {
			return;
		}
		
		// Add tickets to cart
		if ( function_exists( 'WC' ) && WC()->cart ) {
			// Get product IDs from event meta
			$product_ids = get_post_meta( $post->ID, '_uem_wc_products', true );
			if ( ! is_array( $product_ids ) ) {
				$product_ids = array();
			}
			
			foreach ( $tickets as $index => $ticket ) {
				// Get product ID for this ticket
				$product_id = isset( $product_ids[ $index ] ) ? $product_ids[ $index ] : 0;
				
				// If product doesn't exist, create it (fallback)
				if ( ! $product_id || get_post_type( $product_id ) !== 'product' ) {
					$product_id = self::wtmem_create_or_update_ticket_product( $post->ID, $ticket, $index, 0 );
					if ( $product_id ) {
						$product_ids[ $index ] = $product_id;
						update_post_meta( $post->ID, '_uem_wc_products', $product_ids );
					}
				}
				
				if ( $product_id ) {
					WC()->cart->add_to_cart( $product_id, 1, 0, array(), array(
						'uem_event_id' => $post->ID,
						'uem_ticket_index' => $index,
						'uem_ticket_name' => $ticket['name'],
					) );
				}
			}
		}
	}
	
	
	/**
	 * Prevent redirect on empty cart for event pages
	 */
	public static function wtmem_prevent_empty_cart_redirect( $redirect ) {
		if ( is_singular( 'mem_event' ) ) {
			return false;
		}
		return $redirect;
	}

	/**
	 * Tell WooCommerce the event page is a checkout page
	 * This ensures payment gateways, order review, and subtotals are rendered.
	 */
	public static function wtmem_force_is_checkout( $is_checkout ) {
		if ( is_singular( 'mem_event' ) ) {
			return true;
		}
		return $is_checkout;
	}
	
	public static function wtmem_display_attendee_fields_section() {
		// Prevent duplicate output
		static $displayed = false;
		if ( $displayed ) {
			return;
		}
		
		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return;
		}
		
		// Get total quantity of tickets
		$total_quantity = 0;
		$cart_items = WC()->cart->get_cart();
		
		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['uem_event_id'] ) ) {
				$total_quantity += $cart_item['quantity'];
			}
		}
		
		if ( $total_quantity > 0 ) {
			$displayed = true;
			?>
			<div class="uem-attendee-details-section" id="uem-attendee-details-section">
				<h3><?php echo esc_html( 'Attendee Details', 'ultimate-events-manager' ); ?></h3>
				<div class="uem-attendee-fields-wrapper">
					<?php
					// Display fields for each attendee separately
					for ( $i = 1; $i <= $total_quantity; $i++ ) {
						?>
						<div class="uem-attendee-group" data-attendee-number="<?php echo esc_attr( $i ); ?>">
							<h4 class="attendee-group-title"><?php echo esc_html( 'Attendee', 'ultimate-events-manager' ); ?> #<?php echo esc_attr( $i ); ?></h4>
							<?php
							// Call the dynamic form function with attendee index
							self::wtmem_display_attendee_form( $i );
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
    }
}

	public static function wtmem_display_attendee_form( $attendee_index = null ) {
		global $post;
		
		if ( empty( $post ) ) {
			return;
		}
		
		$post_id = $post->ID;
		$attendee_form = get_post_meta( $post_id, 'attendee_form_data_types', true );
		
		if (!is_array($attendee_form)) {
			echo '<p>' . esc_html__('No registration form available.', 'mega-event-manager') . '</p>';
			return;
		}
		
		// Add attendee index to field names to make them unique
		$index_suffix = $attendee_index ? '_' . $attendee_index : '';
		
		?>
		<div class="wtmem-attendee-form attendee-form-<?php echo esc_attr( $attendee_index ); ?>">
			
			<div class="wtmem-form-fields">
				
				<?php 
				// Display predefined fields
				if (!empty($attendee_form['fields'])) :
					foreach ($attendee_form['fields'] as $field_id => $field_data) :
						
						// Skip if field is not enabled
						if (empty($field_data['enabled'])) {
							continue;
						}
						
						$field_label = !empty($field_data['label']) ? $field_data['label'] : ucwords(str_replace('_', ' ', $field_id));
						$is_required = !empty($field_data['required']);
						$required_attr = $is_required ? 'required' : '';
						$required_mark = $is_required ? '<span class="required">*</span>' : '';
						
						// Get saved values if any
						$field_key = 'uem_attendee_' . $field_id . $index_suffix;
						$field_value = isset( $_POST[ $field_key ] ) ? sanitize_text_field( $_POST[ $field_key ] ) : '';
						
						?>
						<div class="wtmem-form-group wtmem-field-<?php echo esc_attr($field_id); ?>">
							<label for="wtmem_<?php echo esc_attr($field_id . $index_suffix); ?>">
								<?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
							</label>
							
							<?php
							// Render field based on type
							switch ($field_id) {
								case 'email_address':
									?>
									<input type="email" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'phone_number':
									?>
									<input type="tel" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'website':
									?>
									<input type="url" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										placeholder="https://" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'address':
									?>
									<textarea id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											rows="3" 
											<?php echo esc_attr($required_attr); ?>><?php echo esc_textarea( $field_value ); ?></textarea>
									<?php
									break;
								
								case 'vegetarian':
									?>
									<select id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											<?php echo esc_attr($required_attr); ?>>
										<option value=""><?php echo esc_html__('Select Option', 'mega-event-manager'); ?></option>
										<option value="yes" <?php selected( $field_value, 'yes' ); ?>><?php echo esc_html__('Yes', 'mega-event-manager'); ?></option>
										<option value="no" <?php selected( $field_value, 'no' ); ?>><?php echo esc_html__('No', 'mega-event-manager'); ?></option>
									</select>
									<?php
									break;
								
								case 'gender':
									?>
									<select id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											<?php echo esc_attr($required_attr); ?>>
										<option value=""><?php echo esc_html__('Select Gender', 'mega-event-manager'); ?></option>
										<option value="male" <?php selected( $field_value, 'male' ); ?>><?php echo esc_html__('Male', 'mega-event-manager'); ?></option>
										<option value="female" <?php selected( $field_value, 'female' ); ?>><?php echo esc_html__('Female', 'mega-event-manager'); ?></option>
										<option value="other" <?php selected( $field_value, 'other' ); ?>><?php echo esc_html__('Other', 'mega-event-manager'); ?></option>
									</select>
									<?php
									break;
								
								case 'dob':
									?>
									<input type="date" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								default:
									// Text input for all other fields
									?>
									<input type="text" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
							}
							?>
						</div>
						<?php
					endforeach;
				endif;
				
				// Display custom dynamic fields
				if (!empty($attendee_form['custom_fields'])) :
					foreach ($attendee_form['custom_fields'] as $index => $custom_field) :
						
						$field_id = !empty($custom_field['id']) ? sanitize_key($custom_field['id']) : 'custom_field_' . $index;
						$field_label = !empty($custom_field['label']) ? $custom_field['label'] : 'Custom Field';
						$field_type = !empty($custom_field['type']) ? $custom_field['type'] : 'text';
						$is_required = !empty($custom_field['required']) && $custom_field['required'] === 'yes';
						$required_attr = $is_required ? 'required' : '';
						$required_mark = $is_required ? '<span class="required">*</span>' : '';
						$field_options = !empty($custom_field['options']) ? $custom_field['options'] : '';
						
						// Get saved values if any
						$custom_field_key = 'uem_attendee_' . $field_id . $index_suffix;
						$custom_field_value = isset( $_POST[ $custom_field_key ] ) ? sanitize_text_field( $_POST[ $custom_field_key ] ) : '';
						
						?>
						<div class="wtmem-form-group wtmem-custom-field wtmem-field-<?php echo esc_attr($field_id); ?>">
							<label for="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>">
								<?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
							</label>
							
							<?php
							// Render custom field based on type
							switch ($field_type) {
								case 'email':
									?>
									<input type="email" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $custom_field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'number':
									?>
									<input type="number" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $custom_field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'select':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<select id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											<?php echo esc_attr($required_attr); ?>>
										<option value=""><?php echo esc_html__('Select Option', 'mega-event-manager'); ?></option>
										<?php foreach ($options_array as $option) : ?>
											<option value="<?php echo esc_attr($option); ?>" <?php selected( $custom_field_value, $option ); ?>><?php echo esc_html($option); ?></option>
										<?php endforeach; ?>
									</select>
									<?php
									break;
								
								case 'checkbox':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<div class="wtmem-checkbox-group">
										<?php foreach ($options_array as $option) : 
											$checked = '';
											if ( is_array( $custom_field_value ) && in_array( $option, $custom_field_value ) ) {
												$checked = 'checked';
											}
											?>
											<label class="wtmem-checkbox-label">
												<input type="checkbox" 
													name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>[]" 
													value="<?php echo esc_attr($option); ?>" 
													<?php echo esc_attr($checked); ?>
													<?php echo esc_attr($required_attr); ?>>
												<?php echo esc_html($option); ?>
											</label>
										<?php endforeach; ?>
									</div>
									<?php
									break;
								
								case 'radio':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<div class="wtmem-radio-group">
										<?php foreach ($options_array as $option) : ?>
											<label class="wtmem-radio-label">
												<input type="radio" 
													name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
													value="<?php echo esc_attr($option); ?>" 
													<?php checked( $custom_field_value, $option ); ?>
													<?php echo esc_attr($required_attr); ?>>
												<?php echo esc_html($option); ?>
											</label>
										<?php endforeach; ?>
									</div>
									<?php
									break;
								
								case 'text':
								default:
									?>
									<input type="text" 
										id="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="uem_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $custom_field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
							}
							?>
						</div>
						<?php
					endforeach;
				endif;
				?>
			</div>
		</div>
		<?php
	}


    /* === Generated Script ===*/

	private static function wtmem_display_attendee_fields_section_ajax() {

		if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
			return;
		}
		
		// Get total quantity of tickets
		$total_quantity = 0;
		$cart_items = WC()->cart->get_cart();
		
		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['uem_event_id'] ) ) {
				$total_quantity += $cart_item['quantity'];
			}
		}
		
		if ( $total_quantity > 0 ) {
			?>
			<div class="uem-attendee-details-section" id="uem-attendee-details-section">
				<h3><?php _e( 'Attendee Details', 'ultimate-events-manager' ); ?></h3>
				<div class="uem-attendee-fields-wrapper">
					<?php
					// Display fields for each attendee
					for ( $i = 1; $i <= $total_quantity; $i++ ) {
						?>
						<div class="uem-attendee-group" data-attendee-number="<?php echo esc_attr( $i ); ?>">
							<h4><?php printf( __('Attendee #%d', 'ultimate-events-manager'), $i ); ?></h4>
							<?php 
							self::wtmem_display_attendee_form_single( $i );
							?>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
       }
    }

	// Modified attendee form function for single attendee
	private static function wtmem_display_attendee_form_single( $attendee_number ) {
    
		// Get event ID from cart (first event found)
		$event_id = 0;
		$cart_items = WC()->cart->get_cart();
		
		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			if ( isset( $cart_item['uem_event_id'] ) ) {
				$event_id = $cart_item['uem_event_id'];
				break; // Use first event found
			}
		}
		
		if ( empty( $event_id ) ) {
			return;
		}

		$attendee_form = get_post_meta( $event_id, 'attendee_form_data_types', true );
		
		if (!is_array($attendee_form)) {
			echo '<p>' . esc_html__('No registration form available.', 'mega-event-manager') . '</p>';
			return;
		}

		?>
		<div class="wtmem-attendee-form">
			<div class="wtmem-form-fields">
				<?php 
				// Display predefined fields
				if (!empty($attendee_form['fields'])) :
					foreach ($attendee_form['fields'] as $field_id => $field_data) :
						
						// Skip if field is not enabled
						if (empty($field_data['enabled'])) {
							continue;
						}
						
						$field_label = !empty($field_data['label']) ? $field_data['label'] : ucwords(str_replace('_', ' ', $field_id));
						$is_required = !empty($field_data['required']);
						$required_attr = $is_required ? 'required' : '';
						$required_mark = $is_required ? '<span class="required">*</span>' : '';
						
						// Create unique field name with attendee number - CORRECT
						$field_name = 'uem_attendee_' . $field_id . '_' . $attendee_number;
							
						// Get saved value if any
						$field_value = isset( $_POST[ $field_name ] ) ? sanitize_text_field( $_POST[ $field_name ] ) : '';
						
						?>
						<div class="wtmem-form-group wtmem-field-<?php echo esc_attr($field_id); ?>">
							<label for="<?php echo esc_attr($field_name); ?>">
								<?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
							</label>
							
							<?php
							// Render field based on type - CORRECTED
							switch ($field_id) {
								case 'first_name':
								case 'last_name':
								case 'company_name':
								case 'job_title':
								case 'city':
								case 'state_province':
								case 'zip_postal':
								case 'country':
									?>
									<input type="text" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
									
								case 'email_address':
									?>
									<input type="email" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'phone_number':
									?>
									<input type="tel" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'website':
									?>
									<input type="url" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										placeholder="https://" 
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'address':
									?>
									<textarea id="<?php echo esc_attr($field_name); ?>" 
											name="<?php echo esc_attr($field_name); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											rows="3" 
											<?php echo esc_attr($required_attr); ?>><?php echo esc_textarea($field_value); ?></textarea>
									<?php
									break;
								
								case 'vegetarian':
									?>
									<select id="<?php echo esc_attr($field_name); ?>" 
											name="<?php echo esc_attr($field_name); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											<?php echo esc_attr($required_attr); ?>>
										<option value=""><?php echo esc_html__('Select Option', 'mega-event-manager'); ?></option>
										<option value="yes" <?php selected($field_value, 'yes'); ?>><?php echo esc_html__('Yes', 'mega-event-manager'); ?></option>
										<option value="no" <?php selected($field_value, 'no'); ?>><?php echo esc_html__('No', 'mega-event-manager'); ?></option>
									</select>
									<?php
									break;
								
								case 'gender':
									?>
									<select id="<?php echo esc_attr($field_name); ?>" 
											name="<?php echo esc_attr($field_name); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											<?php echo esc_attr($required_attr); ?>>
										<option value=""><?php echo esc_html__('Select Gender', 'mega-event-manager'); ?></option>
										<option value="male" <?php selected($field_value, 'male'); ?>><?php echo esc_html__('Male', 'mega-event-manager'); ?></option>
										<option value="female" <?php selected($field_value, 'female'); ?>><?php echo esc_html__('Female', 'mega-event-manager'); ?></option>
										<option value="other" <?php selected($field_value, 'other'); ?>><?php echo esc_html__('Other', 'mega-event-manager'); ?></option>
									</select>
									<?php
									break;
								
								case 'dob':
									?>
									<input type="date" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								default:
									// Text input for all other fields
									?>
									<input type="text" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
							}
							?>
						</div>
						<?php
					endforeach;
				endif;
				
				// Display custom dynamic fields
				if (!empty($attendee_form['custom_fields'])) :
					foreach ($attendee_form['custom_fields'] as $index => $custom_field) :
						
						$field_id = !empty($custom_field['id']) ? sanitize_key($custom_field['id']) : 'custom_field_' . $index;
						$field_label = !empty($custom_field['label']) ? $custom_field['label'] : 'Custom Field';
						$field_type = !empty($custom_field['type']) ? $custom_field['type'] : 'text';
						$is_required = !empty($custom_field['required']) && $custom_field['required'] === 'yes';
						$required_attr = $is_required ? 'required' : '';
						$required_mark = $is_required ? '<span class="required">*</span>' : '';
						$field_options = !empty($custom_field['options']) ? $custom_field['options'] : '';
						
						// Create unique field name with attendee number
						$field_name = 'uem_attendee_custom_' . $field_id . '_' . $attendee_number;
						
						// Get saved value if any
						$field_value = isset( $_POST[ $field_name ] ) ? (is_array($_POST[ $field_name ]) ? array_map('sanitize_text_field', $_POST[ $field_name ]) : sanitize_text_field($_POST[ $field_name ])) : '';
						
						?>
						<div class="wtmem-form-group wtmem-custom-field wtmem-field-<?php echo esc_attr($field_id); ?>">
							<label for="<?php echo esc_attr($field_name); ?>">
								<?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
							</label>
							
							<?php
							// Render custom field based on type
							switch ($field_type) {
								case 'email':
									?>
									<input type="email" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo is_string($field_value) ? esc_attr($field_value) : ''; ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'number':
									?>
									<input type="number" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo is_string($field_value) ? esc_attr($field_value) : ''; ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'select':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<select id="<?php echo esc_attr($field_name); ?>" 
											name="<?php echo esc_attr($field_name); ?>" 
											class="wtmem-form-control uem-attendee-input" 
											<?php echo esc_attr($required_attr); ?>>
										<option value=""><?php echo esc_html__('Select Option', 'mega-event-manager'); ?></option>
										<?php foreach ($options_array as $option) : ?>
											<option value="<?php echo esc_attr($option); ?>" <?php selected($field_value, $option); ?>>
												<?php echo esc_html($option); ?>
											</option>
										<?php endforeach; ?>
									</select>
									<?php
									break;
								
								case 'checkbox':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<div class="wtmem-checkbox-group">
										<?php foreach ($options_array as $option) : 
											$option_value = esc_attr($option);
											$checked = '';
											if (is_array($field_value) && in_array($option_value, $field_value)) {
												$checked = 'checked';
											}
										?>
											<label class="wtmem-checkbox-label">
												<input type="checkbox" 
													name="<?php echo esc_attr($field_name); ?>[]" 
													value="<?php echo $option_value; ?>" 
													<?php echo $checked; ?>
													<?php echo esc_attr($required_attr); ?>>
												<?php echo esc_html($option); ?>
											</label>
										<?php endforeach; ?>
									</div>
									<?php
									break;
								
								case 'radio':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<div class="wtmem-radio-group">
										<?php foreach ($options_array as $option) : ?>
											<label class="wtmem-radio-label">
												<input type="radio" 
													name="<?php echo esc_attr($field_name); ?>" 
													value="<?php echo esc_attr($option); ?>" 
													<?php checked($field_value, $option); ?>
													<?php echo esc_attr($required_attr); ?>>
												<?php echo esc_html($option); ?>
											</label>
										<?php endforeach; ?>
									</div>
									<?php
									break;
								
								case 'text':
								default:
									?>
									<input type="text" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="wtmem-form-control uem-attendee-input" 
										value="<?php echo is_string($field_value) ? esc_attr($field_value) : ''; ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								}
							?>
						</div>
						<?php
					endforeach;
				endif;
				?>
			</div>
		</div>
	  <?php
    }

	/* ==== generated Save attendee data to order item ==== */

	public static function wtmem_save_attendee_data_to_order_item( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['uem_event_id'] ) ) {
			$item->update_meta_data( '_uem_event_id', $values['uem_event_id'] );
			$item->update_meta_data( '_uem_ticket_index', $values['uem_ticket_index'] );
			$item->update_meta_data( '_uem_ticket_name', $values['uem_ticket_name'] );
			
			// Also save attendee form configuration if available
			if ( isset( $values['attendee_form_config'] ) ) {
				$item->update_meta_data( '_uem_attendee_form_config', $values['attendee_form_config'] );
			}
		}
    }
	
    
   /* === Order Iteam meta ====*/

	public static function wtmem_save_attendee_data_to_as_order_item( $item, $cart_item_key, $values, $order ) {
		
		// Check if this item has an event ID
		$order_id = $order->get_id();

		$event_id = $item->get_meta( '_uem_event_id' );
		if ( ! $event_id ) {
			// Try to get event ID from cart item data
			if ( isset( $values['uem_event_id'] ) ) {
				$event_id = $values['uem_event_id'];
				$item->update_meta_data( '_uem_event_id', $event_id );
			} else {
				// Not an event ticket - check if it should be
				$product = $item->get_product();
				if ( $product ) {
					// Check if product has event ID meta
					$product_event_id = $product->get_meta( '_uem_event_id' );
					if ( $product_event_id ) {
						$event_id = $product_event_id;
						$item->update_meta_data( '_uem_event_id', $event_id );
					} else {
						return; // Not an event ticket
					}
				} else {
					return; // Not an event ticket
				}
			}
		}
		
		$quantity = $item->get_quantity();
		$item_attendees = [];
		
		// SIMPLIFIED APPROACH: Let's debug what fields are actually available
		// This will help us understand the field structure
		$all_attendee_fields = [];
		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, 'uem_attendee' ) !== false ) {
				$all_attendee_fields[] = $key . ' = ' . $value;
			}
		}
		if ( ! empty( $all_attendee_fields ) ) {
			error_log( 'Found attendee fields: ' . print_r( $all_attendee_fields, true ) );
		}
		
		// Determine which attendees belong to this item
		// We need to know the position of this item in the cart
		$item_index = 0;
		$attendees_before = 0;
		
		foreach ( $order->get_items() as $order_item_id => $order_item ) {
			if ( $order_item->get_id() === $item->get_id() ) {
				break;
			}
			if ( $order_item->get_meta( '_uem_event_id' ) || 
				( isset( $values['uem_event_id'] ) && $values['uem_event_id'] ) ) {
				$attendees_before += $order_item->get_quantity();
			}
			$item_index++;
		}
		
		error_log( 'Item index: ' . $item_index );
		error_log( 'Attendees before this item: ' . $attendees_before );
		
		// Now collect data for each attendee in this item
		for ( $attendee_in_item = 1; $attendee_in_item <= $quantity; $attendee_in_item++ ) {
			$global_attendee_number = $attendees_before + $attendee_in_item;
			$attendee_data = [];
			
			error_log( 'Processing global attendee #' . $global_attendee_number . ' for item #' . $attendee_in_item );
			
			// TRY DIFFERENT FIELD PATTERNS:
			
			// Pattern 1: uem_attendee_first_name_1
			$first_name_key = 'uem_attendee_first_name_' . $global_attendee_number;
			if ( isset( $_POST[$first_name_key] ) ) {
				$attendee_data['first_name'] = sanitize_text_field( $_POST[$first_name_key] );
				error_log( 'Found first name via pattern 1: ' . $attendee_data['first_name'] );
			}
			
			// Pattern 2: uem_attendee_firstname_1 (without underscore)
			if ( empty( $attendee_data['first_name'] ) ) {
				$first_name_key2 = 'uem_attendee_firstname_' . $global_attendee_number;
				if ( isset( $_POST[$first_name_key2] ) ) {
					$attendee_data['first_name'] = sanitize_text_field( $_POST[$first_name_key2] );
					error_log( 'Found first name via pattern 2: ' . $attendee_data['first_name'] );
				}
			}
			
			// Pattern 3: Check all fields to find first name
			if ( empty( $attendee_data['first_name'] ) ) {
				foreach ( $_POST as $key => $value ) {
					if ( strpos( $key, 'first' ) !== false && strpos( $key, (string)$global_attendee_number ) !== false ) {
						$attendee_data['first_name'] = sanitize_text_field( $value );
						error_log( 'Found first name via pattern 3 in field: ' . $key . ' = ' . $value );
						break;
					}
				}
			}
			
			// Last name - same patterns
			$last_name_key = 'uem_attendee_last_name_' . $global_attendee_number;
			if ( isset( $_POST[$last_name_key] ) ) {
				$attendee_data['last_name'] = sanitize_text_field( $_POST[$last_name_key] );
				error_log( 'Found last name via pattern 1: ' . $attendee_data['last_name'] );
			}
			
			if ( empty( $attendee_data['last_name'] ) ) {
				$last_name_key2 = 'uem_attendee_lastname_' . $global_attendee_number;
				if ( isset( $_POST[$last_name_key2] ) ) {
					$attendee_data['last_name'] = sanitize_text_field( $_POST[$last_name_key2] );
					error_log( 'Found last name via pattern 2: ' . $attendee_data['last_name'] );
				}
			}
			
			// Collect other fields with the correct pattern
			$field_patterns = [
				'email_address' => 'email',
				'phone_number' => 'phone',
				'company_name' => 'company',
				'job_title' => 'job_title',
				'website' => 'website',
				'address' => 'address',
				'city' => 'city',
				'state_province' => 'state',
				'zip_postal' => 'zip',
				'country' => 'country',
				'vegetarian' => 'vegetarian',
				'gender' => 'gender',
				'dob' => 'dob',
			];
			
			foreach ( $field_patterns as $field_key => $field_name ) {
				$post_key = 'uem_attendee_' . $field_key . '_' . $global_attendee_number;
				if ( isset( $_POST[$post_key] ) && ! empty( $_POST[$post_key] ) ) {
					$attendee_data[$field_name] = sanitize_text_field( $_POST[$post_key] );
				}
			}
			
			// Check for custom fields
			foreach ( $_POST as $key => $value ) {
				if ( strpos( $key, 'uem_attendee_custom_' ) === 0 ) {
					// Check if this is for our attendee
					if ( preg_match( '/_(' . $global_attendee_number . ')$/', $key ) ) {
						// Extract custom field name
						$custom_field_name = str_replace( 'uem_attendee_custom_', '', $key );
						$custom_field_name = preg_replace( '/_' . $global_attendee_number . '$/', '', $custom_field_name );
						
						if ( ! empty( $custom_field_name ) ) {
							$attendee_data['custom_' . $custom_field_name] = is_array( $value )
								? array_map( 'sanitize_text_field', $value )
								: sanitize_text_field( $value );
						}
					}
				}
			}
			
			// If we have at least first or last name, save the attendee
			if ( ! empty( $attendee_data['first_name'] ) || ! empty( $attendee_data['last_name'] ) ) {
				$item_attendees[$attendee_in_item] = $attendee_data;
				error_log( 'Saved attendee data: ' . print_r( $attendee_data, true ) );
			} else {
				// No data for this attendee
				$item_attendees[$attendee_in_item] = [
					'first_name' => '',
					'last_name'  => '',
					'note'       => 'No data submitted for attendee #' . $global_attendee_number
				];
				error_log( 'No data found for attendee #' . $global_attendee_number );
			}
		}
		
		// Save attendees to order item meta
			if ( ! empty( $item_attendees ) ) {
			$item->update_meta_data( '_uem_attendees', $item_attendees );
			$item->update_meta_data( '_uem_attendees_count', count( $item_attendees ) );
			
			// Debug: Log what we're saving
			error_log( 'Saving to item meta: ' . print_r( $item_attendees, true ) );
			
			// Save the item
			$item->save();
			
			// Also verify it was saved
			$saved_data = $item->get_meta( '_uem_attendees' );
			error_log( 'Verified saved data: ' . print_r( $saved_data, true ) );
		} else {
			error_log( 'No attendees data to save for this item' );
		}
	}


    /* ==== Display Attendee Data in Thank you  page === */

	public static function display_attendee_data_in_thank_you_page( $item_id, $item, $order ) {
		// Only show for line items (not shipping, fees, etc.)
		if ( ! $item->is_type( 'line_item' ) ) {
			return;
		}
		
		// Get attendee data from item meta
		$attendees = $item->get_meta( '_uem_attendees' );
		
		if ( ! empty( $attendees ) && is_array( $attendees ) ) {
			echo '<div class="uem-attendee-data" style="margin-top: 10px; padding: 10px; background: #f5f5f5; border: 1px solid #ddd;">';
			echo '<strong style="display: block; margin-bottom: 8px; color: #333;">📝 Attendee Information:</strong>';
			
			$attendee_count = 1;
			foreach ( $attendees as $attendee_num => $attendee_data ) {
				echo '<div style="margin-bottom: 15px; padding: 10px; background: white; border: 1px solid #eee;">';
				echo '<strong style="color: #0073aa;">Attendee #' . $attendee_count . ':</strong><br/>';
				
				// Display standard fields
				$display_fields = [
					'first_name' => 'First Name',
					'last_name' => 'Last Name',
					'email' => 'Email',
					'phone' => 'Phone',
					'company' => 'Company',
					'job_title' => 'Job Title',
					'address' => 'Address',
					'city' => 'City',
					'state' => 'State',
					'zip' => 'ZIP Code',
					'country' => 'Country',
					'vegetarian' => 'Dietary Preference',
					'gender' => 'Gender',
					'dob' => 'Date of Birth'
				];
				
				foreach ( $display_fields as $field_key => $field_label ) {
					if ( isset( $attendee_data[ $field_key ] ) && ! empty( $attendee_data[ $field_key ] ) ) {
						echo '<span style="display: inline-block; width: 120px; font-weight: bold;">' . $field_label . ':</span> ';
						echo '<span>' . esc_html( $attendee_data[ $field_key ] ) . '</span><br/>';
					}
				}
				
				// Display custom fields
				foreach ( $attendee_data as $key => $value ) {
					if ( strpos( $key, 'custom_' ) === 0 ) {
						$custom_field_name = str_replace( 'custom_', '', $key );
						$custom_field_name = ucwords( str_replace( '_', ' ', $custom_field_name ) );
						
						echo '<span style="display: inline-block; width: 120px; font-weight: bold;">' . $custom_field_name . ':</span> ';
						
						if ( is_array( $value ) ) {
							echo '<span>' . esc_html( implode( ', ', $value ) ) . '</span><br/>';
						} else {
							echo '<span>' . esc_html( $value ) . '</span><br/>';
						}
					}
				}
				
				echo '</div>';
				$attendee_count++;
			}
			
			echo '</div>';
		}
		
		// Also show event ID if available
		$event_id = $item->get_meta( '_uem_event_id' );
		if ( $event_id ) {
			echo '<div style="margin-top: 5px; font-size: 12px; color: #666;">';
			echo '<strong>Event ID:</strong> ' . esc_html( $event_id );
			echo '</div>';
		}
    }

	

	// This hook works in ADMIN ORDER EDIT PAGE only

	public static function display_attendee_data_in_admin_order_edit( $item_id, $item, $order ) {
		// Only show for line items
		if ( ! $item->is_type( 'line_item' ) ) {
			return;
		}
		
		// Get attendee data from item meta
		$attendees = $item->get_meta( '_uem_attendees' );
		
		if ( ! empty( $attendees ) && is_array( $attendees ) ) {
			echo '<div class="uem-attendee-data" style="margin-top: 10px; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; border-radius: 4px;">';
			echo '<strong style="display: block; margin-bottom: 8px; color: #333;">Attendee Information:</strong>';
			
			$attendee_count = 1;
			foreach ( $attendees as $attendee_num => $attendee_data ) {
				echo '<div style="margin-bottom: 15px; padding: 10px; background: white; border: 1px solid #eee; border-radius: 3px;">';
				echo '<strong style="color: #0073aa;">Attendee #' . $attendee_count . ':</strong><br/>';
				
				// Display fields
				if ( ! empty( $attendee_data['first_name'] ) || ! empty( $attendee_data['last_name'] ) ) {
					echo '<span style="display: inline-block; width: 120px; font-weight: bold;">Name:</span> ';
					echo '<span>' . esc_html( $attendee_data['first_name'] . ' ' . $attendee_data['last_name'] ) . '</span><br/>';
				}
				
				if ( ! empty( $attendee_data['email'] ) ) {
					echo '<span style="display: inline-block; width: 120px; font-weight: bold;">Email:</span> ';
					echo '<span>' . esc_html( $attendee_data['email'] ) . '</span><br/>';
				}
				
				if ( ! empty( $attendee_data['phone'] ) ) {
					echo '<span style="display: inline-block; width: 120px; font-weight: bold;">Phone:</span> ';
					echo '<span>' . esc_html( $attendee_data['phone'] ) . '</span><br/>';
				}
				
				if ( ! empty( $attendee_data['company'] ) ) {
					echo '<span style="display: inline-block; width: 120px; font-weight: bold;">Company:</span> ';
					echo '<span>' . esc_html( $attendee_data['company'] ) . '</span><br/>';
				}
				
				// Display other fields
				$other_fields = [
					'job_title' => 'Job Title',
					'address' => 'Address',
					'city' => 'City',
					'state' => 'State',
					'zip' => 'ZIP',
					'country' => 'Country',
					'vegetarian' => 'Dietary',
					'gender' => 'Gender',
					'dob' => 'Date of Birth'
				];
				
				foreach ( $other_fields as $field_key => $field_label ) {
					if ( ! empty( $attendee_data[ $field_key ] ) ) {
						echo '<span style="display: inline-block; width: 120px; font-weight: bold;">' . $field_label . ':</span> ';
						echo '<span>' . esc_html( $attendee_data[ $field_key ] ) . '</span><br/>';
					}
				}
				
				echo '</div>';
				$attendee_count++;
			}
			
			echo '</div>';
		}
	}


	/* ==== generated Display attendee data to order details ====*/

	public static function wtmem_display_attendee_data_in_order( $item_id, $item, $order ) {
		// Check if this is an event ticket
		$event_id = $item->get_meta( '_uem_event_id' );
		if ( ! $event_id ) {
			return;
		}
		
		// Get attendee data
		$attendees = get_post_meta( $order->get_id(), '_uem_attendees', true );
		if ( ! is_array( $attendees ) || empty( $attendees ) ) {
			return;
		}
		
		echo '<div class="wtmem-attendee-data">';
		echo '<h4>' . esc_html__( 'Attendee Details:', 'mega-event-manager' ) . '</h4>';
		
		// Display attendees in new format
		echo '<div class="wtmem-attendee-list">';
		
		foreach ( $attendees as $index => $attendee ) {
			echo '<div class="wtmem-attendee-item">';
			echo '<h5>' . sprintf( esc_html__( 'Attendee %d', 'mega-event-manager' ), $index + 1 ) . '</h5>';
			echo '<table class="wtmem-attendee-details">';
			
			// Define field order and labels for new format
			$field_order = array(
				'first_name'    => __( 'First Name', 'mega-event-manager' ),
				'last_name'     => __( 'Last Name', 'mega-event-manager' ),
				'email_address' => __( 'Email Address', 'mega-event-manager' ),
				'phone_number'  => __( 'Phone Number', 'mega-event-manager' ),
				'company_name'  => __( 'Company', 'mega-event-manager' ),
				'job_title'     => __( 'Job Title', 'mega-event-manager' ),
				'website'       => __( 'Website', 'mega-event-manager' ),
				'address'       => __( 'Address', 'mega-event-manager' ),
				'city'          => __( 'City', 'mega-event-manager' ),
				'state_province'=> __( 'State/Province', 'mega-event-manager' ),
				'zip_postal'    => __( 'Zip/Postal Code', 'mega-event-manager' ),
				'country'       => __( 'Country', 'mega-event-manager' ),
				'vegetarian'    => __( 'Vegetarian', 'mega-event-manager' ),
				'gender'        => __( 'Gender', 'mega-event-manager' ),
				'dob'           => __( 'Date of Birth', 'mega-event-manager' ),
			);
			
			// Display standard fields
			foreach ( $field_order as $field => $label ) {
				if ( isset( $attendee[$field] ) && ! empty( $attendee[$field] ) ) {
					echo '<tr>';
					echo '<th>' . esc_html( $label ) . ':</th>';
					echo '<td>' . esc_html( $attendee[$field] ) . '</td>';
					echo '</tr>';
				}
			}
			
			// Display custom fields (prefixed with 'custom_')
			foreach ( $attendee as $key => $value ) {
				if ( strpos( $key, 'custom_' ) === 0 && ! empty( $value ) ) {
					$field_label = ucwords( str_replace( '_', ' ', substr( $key, 7 ) ) );
					echo '<tr>';
					echo '<th>' . esc_html( $field_label ) . ':</th>';
					echo '<td>';
					if ( is_array( $value ) ) {
						echo esc_html( implode( ', ', $value ) );
					} else {
						echo esc_html( $value );
					}
					echo '</td>';
					echo '</tr>';
				}
			}
			
			echo '</table>';
			echo '</div>';
			
			// Add separator between attendees
			if ( $index < count( $attendees ) - 1 ) {
				echo '<hr class="wtmem-attendee-separator">';
			}
		}
		
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Display attendee data in admin order sidebar
	 */
	public static function wtmem_display_attendee_data_in_order_details( $order ) {

		$attendees = get_post_meta( $order->get_id(), '_uem_attendees', true );

		if ( ! is_array( $attendees ) || empty( $attendees ) ) {
			return;
		}
		
		?>
		<div class="order_data_column" style="width:100%;">
			<h3><?php esc_html_e( 'Attendee Information', 'mega-event-manager' ); ?></h3>
			<div class="address">
				<div class="wtmem-admin-attendee-data">
					<table class="widefat striped" style="margin-bottom:20px;">
						<thead>
							<tr>
								<th width="10%"><?php esc_html_e( '#', 'mega-event-manager' ); ?></th>
								<th width="15%"><?php esc_html_e( 'First Name', 'mega-event-manager' ); ?></th>
								<th width="15%"><?php esc_html_e( 'Last Name', 'mega-event-manager' ); ?></th>
								<th width="20%"><?php esc_html_e( 'Email', 'mega-event-manager' ); ?></th>
								<th width="15%"><?php esc_html_e( 'Phone', 'mega-event-manager' ); ?></th>
								<th width="25%"><?php esc_html_e( 'Company', 'mega-event-manager' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $attendees as $index => $attendee ) : ?>
								<tr>
									<td><?php echo esc_html( $index + 1 ); ?></td>
									<td><?php echo esc_html( $attendee['first_name'] ?? '—' ); ?></td>
									<td><?php echo esc_html( $attendee['last_name'] ?? '—' ); ?></td>
									<td><?php echo esc_html( $attendee['email_address'] ?? '—' ); ?></td>
									<td><?php echo esc_html( $attendee['phone_number'] ?? '—' ); ?></td>
									<td><?php echo esc_html( $attendee['company_name'] ?? '—' ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
					<div class="wtmem-full-details-link">
						<button type="button" class="button button-secondary" onclick="jQuery('.wtmem-admin-attendee-items').slideToggle();">
							<?php esc_html_e( 'View Full Attendee Details', 'mega-event-manager' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display full attendee data in admin order items table
	 */
	public static function wtmem_display_attendee_data_in_admin_items( $order_id ) {
		$order = wc_get_order( $order_id );
		$attendees = get_post_meta( $order_id, '_uem_attendees', true );
		
		if ( ! is_array( $attendees ) || empty( $attendees ) ) {
			return;
		}
		
		?>
		<div class="wtmem-admin-attendee-items" style="display:none; margin-top:30px;">
			<h3><?php esc_html_e( 'Complete Attendee Details', 'mega-event-manager' ); ?></h3>
			
			<?php foreach ( $attendees as $index => $attendee ) : ?>
				<div class="wtmem-attendee-full-details" style="margin-bottom:30px; padding:20px; background:#f5f5f5; border-radius:5px;">
					<h4 style="margin-top:0;">
						<?php echo sprintf( esc_html__( 'Attendee %d', 'mega-event-manager' ), $index + 1 ); ?>
					</h4>
					
					<table class="widefat striped" style="background:white;">
						<tbody>
							<?php
							// Standard fields display
							$standard_fields = array(
								'first_name'    => __( 'First Name', 'mega-event-manager' ),
								'last_name'     => __( 'Last Name', 'mega-event-manager' ),
								'email_address' => __( 'Email Address', 'mega-event-manager' ),
								'phone_number'  => __( 'Phone Number', 'mega-event-manager' ),
								'company_name'  => __( 'Company', 'mega-event-manager' ),
								'job_title'     => __( 'Job Title', 'mega-event-manager' ),
								'website'       => __( 'Website', 'mega-event-manager' ),
								'address'       => __( 'Address', 'mega-event-manager' ),
								'city'          => __( 'City', 'mega-event-manager' ),
								'state_province'=> __( 'State/Province', 'mega-event-manager' ),
								'zip_postal'    => __( 'Zip/Postal Code', 'mega-event-manager' ),
								'country'       => __( 'Country', 'mega-event-manager' ),
								'vegetarian'    => __( 'Vegetarian', 'mega-event-manager' ),
								'gender'        => __( 'Gender', 'mega-event-manager' ),
								'dob'           => __( 'Date of Birth', 'mega-event-manager' ),
							);
							
							foreach ( $standard_fields as $field => $label ) {
								if ( isset( $attendee[$field] ) && ! empty( $attendee[$field] ) ) {
									?>
									<tr>
										<th width="25%" style="padding:10px;"><?php echo esc_html( $label ); ?>:</th>
										<td width="75%" style="padding:10px;"><?php echo esc_html( $attendee[$field] ); ?></td>
									</tr>
									<?php
								}
							}
							
							// Custom fields display
							foreach ( $attendee as $key => $value ) {
								if ( strpos( $key, 'custom_' ) === 0 && ! empty( $value ) ) {
									$field_label = ucwords( str_replace( '_', ' ', substr( $key, 7 ) ) );
									?>
									<tr>
										<th width="25%" style="padding:10px;"><?php echo esc_html( $field_label ); ?>:</th>
										<td width="75%" style="padding:10px;">
											<?php 
											if ( is_array( $value ) ) {
												echo esc_html( implode( ', ', $value ) );
											} else {
												echo esc_html( $value );
											}
											?>
										</td>
									</tr>
									<?php
								}
							}
							?>
						</tbody>
					</table>
				</div>
			<?php endforeach; ?>
		</div>
		
		<style>
		.wtmem-attendee-full-details:last-child {
			margin-bottom: 0;
		}
		</style>
		<?php
	}


	/**
	 * CSV import
	 */

	public static function add_attendee_data_to_csv_export( $row, $order, $csv_generator ) {
		$attendee_data = [];
		$attendee_count = 0;
		
		foreach ( $order->get_items() as $item_id => $item ) {
			$attendees = $item->get_meta( '_uem_attendees' );
			
			if ( ! empty( $attendees ) && is_array( $attendees ) ) {
				foreach ( $attendees as $attendee_num => $attendee ) {
					$attendee_count++;
					$attendee_data[] = 'Attendee ' . $attendee_count . ': ' . 
						( isset( $attendee['first_name'] ) ? $attendee['first_name'] : '' ) . ' ' . 
						( isset( $attendee['last_name'] ) ? $attendee['last_name'] : '' ) . ' (' . 
						( isset( $attendee['email'] ) ? $attendee['email'] : '' ) . ')';
				}
			}
		}
		
		if ( ! empty( $attendee_data ) ) {
			$row['attendee_information'] = implode( ' | ', $attendee_data );
		}
		
		return $row;
	}


    
	/* ==== END generated Display attendee data to order details ====*/



	/**
	 * Update checkout fields when cart updates
	 */
	public static function wtmem_update_checkout_fields() {
		// This will trigger the checkout fields to be regenerated
		// The add_attendee_fields method will be called again with updated cart
	}
	
	/**
	 * AJAX update cart
	 */
	public static function wtmem_ajax_update_cart() {
		try {
			// Verify nonce
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'uem_nonce' ) ) {
				wp_send_json_error( array( 
					'message' => __( 'Security check failed. Please refresh the page.', 'ultimate-events-manager' ),
					'debug' => 'Nonce verification failed'
				) );
				return;
			}
			
			if ( ! function_exists( 'WC' ) ) {
				wp_send_json_error( array( 'message' => __( 'WooCommerce is not available.', 'ultimate-events-manager' ) ) );
				return;
			}
			
			if ( ! WC()->cart ) {
				wp_send_json_error( array( 'message' => __( 'WooCommerce cart is not available.', 'ultimate-events-manager' ) ) );
				return;
			}
		
			$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( $_POST['cart_item_key'] ) : '';
			error_log('AJAX Update Cart called. cart_item_key: ' . $cart_item_key);
			
			$quantity = isset( $_POST['quantity'] ) ? intval( $_POST['quantity'] ) : 0;
			$ticket_index = isset( $_POST['ticket_index'] ) ? intval( $_POST['ticket_index'] ) : -1;
			$event_id = isset( $_POST['event_id'] ) ? intval( $_POST['event_id'] ) : 0;
			
			if ( $quantity < 0 ) {
				wp_send_json_error( array( 'message' => __( 'Invalid quantity.', 'ultimate-events-manager' ) ) );
				return;
			}
			
			if ( $ticket_index < 0 ) {
				wp_send_json_error( array( 'message' => __( 'Invalid ticket index.', 'ultimate-events-manager' ) ) );
				return;
			}
		
			// If no event ID provided, try to get it from cart item
			if ( ! $event_id && $cart_item_key ) {
				$cart_item = WC()->cart->get_cart_item( $cart_item_key );
				$event_id = isset( $cart_item['uem_event_id'] ) ? $cart_item['uem_event_id'] : 0;
			}
			
			if ( ! $event_id ) {
				wp_send_json_error( array( 'message' => __( 'Event ID not found.', 'ultimate-events-manager' ) ) );
				return;
			}
			
			// Get tickets
			//$tickets = get_post_meta( $event_id, '_uem_tickets', true );
			$tickets_data = get_post_meta( $event_id, '_wtmem_tk_tickets', true );
			if ( ! is_array( $tickets_data ) ) {
				wp_send_json_error( array( 'message' => __( 'Ticket data not found.', 'ultimate-events-manager' ) ) );
				return;
			}
			
			// Extract regular tickets from nested structure
			$tickets = isset( $tickets_data['regular_tickets'] ) && is_array( $tickets_data['regular_tickets'] )
				? $tickets_data['regular_tickets']
				: $tickets_data;
			
			if ( ! isset( $tickets[ $ticket_index ] ) ) {
				wp_send_json_error( array( 'message' => __( 'Ticket not found.', 'ultimate-events-manager' ) ) );
				return;
			}
			
			$ticket = $tickets[ $ticket_index ];
		
			// Get product ID from event meta
			$product_ids = get_post_meta( $event_id, '_uem_wc_products', true );
			if ( ! is_array( $product_ids ) ) {
				$product_ids = array();
			}
			$product_id = isset( $product_ids[ $ticket_index ] ) ? $product_ids[ $ticket_index ] : 0;
			
			// If product doesn't exist, create it (fallback)
			if ( ! $product_id || get_post_type( $product_id ) !== 'product' ) {
				$product_id = self::wtmem_create_or_update_ticket_product( $event_id, $ticket, $ticket_index, 0 );
				if ( $product_id ) {
					$product_ids[ $ticket_index ] = $product_id;
					update_post_meta( $event_id, '_uem_wc_products', $product_ids );
				}
			}
			
			if ( ! $product_id ) {
				wp_send_json_error( array( 'message' => __( 'Product not found for this ticket.', 'ultimate-events-manager' ) ) );
				return;
			}
			
			// Handle cart update
			$cart_item_exists = false;
			if ( $cart_item_key ) {
				$cart_item = WC()->cart->get_cart_item( $cart_item_key );
				$cart_item_exists = ! empty( $cart_item );
			}
			
			if ( $cart_item_exists ) {
				// Item exists in cart
				if ( $quantity > 0 ) {
					// Update quantity
					WC()->cart->set_quantity( $cart_item_key, $quantity );
				} else {
					// Remove item
					WC()->cart->remove_cart_item( $cart_item_key );
					$cart_item_key = ''; // Item removed
				}
			} else {
				// Item doesn't exist, need to add it
				if ( $quantity > 0 ) {
					$new_cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), array(
						'uem_event_id' => $event_id,
						'uem_ticket_index' => $ticket_index,
						'uem_ticket_name' => $ticket['name'],
					) );
					if ( $new_cart_item_key ) {
						$cart_item_key = $new_cart_item_key;
					} else {
						wp_send_json_error( array( 'message' => __( 'Failed to add item to cart.', 'ultimate-events-manager' ) ) );
						return;
					}
				}
			}
			
			// Calculate ticket totals and get all cart item keys
			$ticket_totals = array();
			$cart_item_keys = array();
			$grand_total = 0;
			
			$cart_items = WC()->cart->get_cart();
			foreach ( $tickets as $index => $ticket_data ) {
				$ticket_quantity = 0;
				$item_key = '';
				foreach ( $cart_items as $key => $item ) {
					if ( isset( $item['uem_event_id'] ) && $item['uem_event_id'] == $event_id && isset( $item['uem_ticket_index'] ) && $item['uem_ticket_index'] == $index ) {
						$ticket_quantity = $item['quantity'];
						$item_key = $key;
						break;
					}
				}
				$ticket_total = $ticket_data['price'] * $ticket_quantity;
				$ticket_totals[ $index ] = $ticket_total;
				$cart_item_keys[ $index ] = $item_key;
				$grand_total += $ticket_total;
			}
			
			// Calculate cart totals
			WC()->cart->calculate_totals();
			
			// Return updated cart fragments
			$fragments = array();
			
			// Get cart totals
			ob_start();
			if ( function_exists( 'woocommerce_cart_totals' ) ) {
				woocommerce_cart_totals();
			}
			$fragments['.cart_totals'] = ob_get_clean();
			
			// Clear checkout fields cache to ensure attendee fields are regenerated
			$checkout = WC()->checkout();
			if ( $checkout ) {
				$reflection = new \ReflectionClass( $checkout );
				$property = $reflection->getProperty( 'fields' );
				$property->setAccessible( true );
				$property->setValue( $checkout, null );
			}
			
			// Get order review with payment section (includes payment options and place order button)
			ob_start();
			do_action( 'woocommerce_checkout_order_review' );
			$fragments['.woocommerce-checkout-review-order'] = ob_get_clean();
			
			// Get billing fields section with attendee fields
			ob_start();
			do_action( 'woocommerce_checkout_billing' );
			$fragments['.woocommerce-billing-fields'] = ob_get_clean();
			
			// Also get just the attendee fields section for targeted updates
			ob_start();
			self::wtmem_display_attendee_fields_section_ajax();
			$attendee_section = ob_get_clean();
			if ( ! empty( $attendee_section ) ) {
				$fragments['#uem-attendee-details-section'] = $attendee_section;
			} else {
				// If no attendees, remove the section
				$fragments['#uem-attendee-details-section'] = '';
			}
			
			wp_send_json_success( array(
				'fragments' => $fragments,
				'cart_total' => WC()->cart->get_cart_total(),
				'ticket_totals' => $ticket_totals,
				'grand_total' => $grand_total,
				'cart_item_key' => $cart_item_key,
				'cart_item_keys' => $cart_item_keys,
			) );
		} catch ( Exception $e ) {
			wp_send_json_error( array( 
				'message' => __( 'An error occurred: ', 'ultimate-events-manager' ) . $e->getMessage(),
				'debug' => $e->getTraceAsString()
			) );
		} catch ( Error $e ) {
			wp_send_json_error( array( 
				'message' => __( 'A fatal error occurred: ', 'ultimate-events-manager' ) . $e->getMessage(),
				'debug' => $e->getTraceAsString()
			) );
		}
	}
}

