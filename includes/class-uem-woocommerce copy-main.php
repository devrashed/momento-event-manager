<?php
/**
 * WooCommerce Integration
 *
 * @package Ultimate_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UEM_WooCommerce {
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
		add_action( 'save_post_uem_event', array( __CLASS__, 'webcu_create_ticket_products' ), 20, 2 );
		
		// Delete products when event is deleted
		add_action( 'before_delete_post', array( __CLASS__, 'webcu_delete_ticket_products' ) );
		
		// Clear cart and add tickets when viewing event
		add_action( 'template_redirect', array( __CLASS__, 'webcu_handle_event_page' ) );
		
		// Prevent redirect on empty cart for event pages
		add_filter( 'woocommerce_checkout_redirect_empty_cart', array( __CLASS__, 'webcu_prevent_empty_cart_redirect' ), 10, 1 );
		
		// Display attendee fields section after billing form
		add_action( 'woocommerce_after_checkout_billing_form', array( __CLASS__, 'webcu_display_attendee_fields_section' ), 10 );
		
		// Validate attendee fields - only use one hook to prevent duplicate errors
		add_action( 'woocommerce_checkout_process', array( __CLASS__, 'webcu_validate_attendee_fields' ) );
		
		// Save attendee data to order
		add_action( 'woocommerce_checkout_create_order_line_item', array( __CLASS__, 'webcu_save_attendee_data_to_order_item' ), 10, 4 );
		add_action( 'woocommerce_checkout_update_order_meta', array( __CLASS__, 'webcu_save_attendee_data_to_order' ) );
		
		// Display attendee data in order details

		add_action( 'woocommerce_order_item_meta_end', array( __CLASS__, 'webcu_display_attendee_data_in_order' ), 10, 3 );
		add_action( 'woocommerce_admin_order_data_after_order_details', array( __CLASS__, 'webcu_display_attendee_data_in_admin' ) );
		
		// AJAX handlers are registered in main plugin file
		// Update checkout fields when cart updates
		add_action( 'woocommerce_checkout_update_order_review', array( __CLASS__, 'webcu_update_checkout_fields' ) );
	}
	
	/**
	 * Delete WooCommerce products when event is deleted
	 */
	public static function webcu_delete_ticket_products( $post_id ) {
		// Only process event posts
		if ( get_post_type( $post_id ) !== 'uem_event' ) {
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
	public static function webcu_create_ticket_products( $post_id, $post ) {
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
		$tickets = get_post_meta( $post_id, '_webcu_tk_tickets', true );

		if ( ! is_array( $tickets ) ) {
			$tickets = array();
		}
		
		// Get existing product IDs
		$existing_products = array();
		$all_product_ids = get_post_meta( $post_id, '_uem_wc_products', true );
		if ( is_array( $all_product_ids ) ) {
			$existing_products = $all_product_ids;
		}
		
		// Create or update products for each ticket
		$product_ids = array();
		foreach ( $tickets as $index => $ticket ) {
			$product_id = self::webcu_create_or_update_ticket_product( $post_id, $ticket, $index, isset( $existing_products[ $index ] ) ? $existing_products[ $index ] : 0 );
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
	private static function webcu_create_or_update_ticket_product( $event_id, $ticket, $index, $existing_product_id = 0 ) {
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
		$product = new WC_Product_Simple();
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
	public static function webcu_handle_event_page() {
		if ( ! is_singular( 'uem_event' ) ) {
			return;
		}
		
		global $post;
		
		// Clear cart
		if ( function_exists( 'WC' ) && WC()->cart ) {
			WC()->cart->empty_cart();
		}
		
		// Get event tickets
		//$tickets = get_post_meta( $post->ID, '_uem_tickets', true );
		$tickets = get_post_meta( $post->ID, '_webcu_tk_tickets', true );
		if ( ! is_array( $tickets ) || empty( $tickets ) ) {
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
					$product_id = self::webcu_create_or_update_ticket_product( $post->ID, $ticket, $index, 0 );
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
	public static function webcu_prevent_empty_cart_redirect( $redirect ) {
		if ( is_singular( 'uem_event' ) ) {
			return false;
		}
		return $redirect;
	}
	
	/**
	 * Display attendee fields section after billing form
	 */
	public static function webcu_display_attendee_fields_section_MainMethod() {
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
					// Display fields for each attendee separately using simple HTML inputs
					for ( $i = 1; $i <= $total_quantity; $i++ ) {
						$name_key = 'uem_attendee_name_' . $i;
						$phone_key = 'uem_attendee_phone_' . $i;
						$email_key = 'uem_attendee_email_' . $i;
						
						// Get saved values if any
						$name_value = isset( $_POST[ $name_key ] ) ? sanitize_text_field( $_POST[ $name_key ] ) : '';
						$phone_value = isset( $_POST[ $phone_key ] ) ? sanitize_text_field( $_POST[ $phone_key ] ) : '';
						$email_value = isset( $_POST[ $email_key ] ) ? sanitize_email( $_POST[ $email_key ] ) : '';
						
						?>
						<div class="uem-attendee-group" data-attendee-number="<?php echo esc_attr( $i ); ?>">
							<div class="uem-attendee-row">
								<div class="uem-attendee-field-inline">
									<label for="<?php echo esc_attr( $name_key ); ?>">
										<?php echo esc_html( 'Name', 'ultimate-events-manager' ); ?>
										<span class="required">*</span>
									</label>
									<input 
										type="text" 
										id="<?php echo esc_attr( $name_key ); ?>" 
										name="<?php echo esc_attr( $name_key ); ?>" 
										class="input-text uem-attendee-input" 
										value="<?php echo esc_attr( $name_value ); ?>" 
										required 
									/>
								</div>
								
								<div class="uem-attendee-field-inline">
									<label for="<?php echo esc_attr( $phone_key ); ?>">
										<?php echo esc_html( 'Phone', 'ultimate-events-manager' ); ?>
										<span class="required">*</span>
									</label>
									<input 
										type="tel" 
										id="<?php echo esc_attr( $phone_key ); ?>" 
										name="<?php echo esc_attr( $phone_key ); ?>" 
										class="input-text uem-attendee-input" 
										value="<?php echo esc_attr( $phone_value ); ?>" 
										required 
									/>
								</div>
								
								<div class="uem-attendee-field-inline">
									<label for="<?php echo esc_attr( $email_key ); ?>">
										<?php echo esc_html( 'Email', 'ultimate-events-manager' ); ?>
										<span class="optional">(<?php echo esc_html( 'optional', 'ultimate-events-manager' ); ?>)</span>
									</label>
									<input 
										type="email" 
										id="<?php echo esc_attr( $email_key ); ?>" 
										name="<?php echo esc_attr( $email_key ); ?>" 
										class="input-text uem-attendee-input" 
										value="<?php echo esc_attr( $email_value ); ?>" 
									/>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
	}


	public static function webcu_display_attendee_fields_section() {
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
							self::webcu_display_attendee_form( $i );
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

	public static function webcu_display_attendee_form( $attendee_index = null ) {
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
		<div class="webcu-attendee-form attendee-form-<?php echo esc_attr( $attendee_index ); ?>">
			
			<div class="webcu-form-fields">
				
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
						$field_key = 'webcu_attendee_' . $field_id . $index_suffix;
						$field_value = isset( $_POST[ $field_key ] ) ? sanitize_text_field( $_POST[ $field_key ] ) : '';
						
						?>
						<div class="webcu-form-group webcu-field-<?php echo esc_attr($field_id); ?>">
							<label for="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>">
								<?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
							</label>
							
							<?php
							// Render field based on type
							switch ($field_id) {
								case 'email_address':
									?>
									<input type="email" 
										id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'phone_number':
									?>
									<input type="tel" 
										id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'website':
									?>
									<input type="url" 
										id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
										placeholder="https://" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'address':
									?>
									<textarea id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="webcu-form-control uem-attendee-input" 
											rows="3" 
											<?php echo esc_attr($required_attr); ?>><?php echo esc_textarea( $field_value ); ?></textarea>
									<?php
									break;
								
								case 'vegetarian':
									?>
									<select id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="webcu-form-control uem-attendee-input" 
											<?php echo esc_attr($required_attr); ?>>
										<option value=""><?php echo esc_html__('Select Option', 'mega-event-manager'); ?></option>
										<option value="yes" <?php selected( $field_value, 'yes' ); ?>><?php echo esc_html__('Yes', 'mega-event-manager'); ?></option>
										<option value="no" <?php selected( $field_value, 'no' ); ?>><?php echo esc_html__('No', 'mega-event-manager'); ?></option>
									</select>
									<?php
									break;
								
								case 'gender':
									?>
									<select id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="webcu-form-control uem-attendee-input" 
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
										id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								default:
									// Text input for all other fields
									?>
									<input type="text" 
										id="webcu_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_attendee_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
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
						$custom_field_key = 'webcu_custom_' . $field_id . $index_suffix;
						$custom_field_value = isset( $_POST[ $custom_field_key ] ) ? sanitize_text_field( $_POST[ $custom_field_key ] ) : '';
						
						?>
						<div class="webcu-form-group webcu-custom-field webcu-field-<?php echo esc_attr($field_id); ?>">
							<label for="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>">
								<?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
							</label>
							
							<?php
							// Render custom field based on type
							switch ($field_type) {
								case 'email':
									?>
									<input type="email" 
										id="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $custom_field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'number':
									?>
									<input type="number" 
										id="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr( $custom_field_value ); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'select':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<select id="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											name="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
											class="webcu-form-control uem-attendee-input" 
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
									<div class="webcu-checkbox-group">
										<?php foreach ($options_array as $option) : 
											$checked = '';
											if ( is_array( $custom_field_value ) && in_array( $option, $custom_field_value ) ) {
												$checked = 'checked';
											}
											?>
											<label class="webcu-checkbox-label">
												<input type="checkbox" 
													name="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>[]" 
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
									<div class="webcu-radio-group">
										<?php foreach ($options_array as $option) : ?>
											<label class="webcu-radio-label">
												<input type="radio" 
													name="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
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
										id="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										name="webcu_custom_<?php echo esc_attr($field_id . $index_suffix); ?>" 
										class="webcu-form-control uem-attendee-input" 
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


	
	/**
	 * Display attendee fields section for AJAX updates (without static duplicate check)
	 */
	private static function webcu_display_attendee_fields_section_ajax____main() {

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
					// Display fields for each attendee separately using simple HTML inputs
					for ( $i = 1; $i <= $total_quantity; $i++ ) {
						$name_key = 'uem_attendee_name_' . $i;
						$phone_key = 'uem_attendee_phone_' . $i;
						$email_key = 'uem_attendee_email_' . $i;
						
						// Get saved values if any
						$name_value = isset( $_POST[ $name_key ] ) ? sanitize_text_field( $_POST[ $name_key ] ) : '';
						$phone_value = isset( $_POST[ $phone_key ] ) ? sanitize_text_field( $_POST[ $phone_key ] ) : '';
						$email_value = isset( $_POST[ $email_key ] ) ? sanitize_email( $_POST[ $email_key ] ) : '';
						
						?>
						<div class="uem-attendee-group" data-attendee-number="<?php echo esc_attr( $i ); ?>">
							<div class="uem-attendee-row">
								<div class="uem-attendee-field-inline">
									<label for="<?php echo esc_attr( $name_key ); ?>">
										<?php _e( 'Name', 'ultimate-events-manager' ); ?>
										<span class="required">*</span>
									</label>
									<input 
										type="text" 
										id="<?php echo esc_attr( $name_key ); ?>" 
										name="<?php echo esc_attr( $name_key ); ?>" 
										class="input-text uem-attendee-input" 
										value="<?php echo esc_attr( $name_value ); ?>" 
										required 
									/>
								</div>
								
								<div class="uem-attendee-field-inline">
									<label for="<?php echo esc_attr( $phone_key ); ?>">
										<?php _e( 'Phone', 'ultimate-events-manager' ); ?>
										<span class="required">*</span>
									</label>
									<input 
										type="tel" 
										id="<?php echo esc_attr( $phone_key ); ?>" 
										name="<?php echo esc_attr( $phone_key ); ?>" 
										class="input-text uem-attendee-input" 
										value="<?php echo esc_attr( $phone_value ); ?>" 
										required 
									/>
								</div>
								
								<div class="uem-attendee-field-inline">
									<label for="<?php echo esc_attr( $email_key ); ?>">
										<?php _e( 'Email', 'ultimate-events-manager' ); ?>
										<span class="optional">(<?php _e( 'optional', 'ultimate-events-manager' ); ?>)</span>
									</label>
									<input 
										type="email" 
										id="<?php echo esc_attr( $email_key ); ?>" 
										name="<?php echo esc_attr( $email_key ); ?>" 
										class="input-text uem-attendee-input" 
										value="<?php echo esc_attr( $email_value ); ?>" 
									/>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
		}
	}


    /* === Generated Script ===*/

	private static function webcu_display_attendee_fields_section_ajax() {

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
							// Display the attendee form for each attendee
							self::webcu_display_attendee_form_single( $i );
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
	private static function webcu_display_attendee_form_single( $attendee_number ) {
		
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
		<div class="webcu-attendee-form">
			<div class="webcu-form-fields">
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
						
						// Create unique field name with attendee number
						$field_name = 'uem_attendee_' . $attendee_number . '_' . $field_id;
						
						// Get saved value if any
						$field_value = isset( $_POST[ $field_name ] ) ? sanitize_text_field( $_POST[ $field_name ] ) : '';
						
						?>
						<div class="webcu-form-group webcu-field-<?php echo esc_attr($field_id); ?>">
							<label for="<?php echo esc_attr($field_name); ?>">
								<?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
							</label>
							
							<?php
							// Render field based on type
							switch ($field_id) {
								case 'email_address':
									?>
									<input type="email" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'phone_number':
									?>
									<input type="tel" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'website':
									?>
									<input type="url" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo esc_attr($field_value); ?>"
										placeholder="https://" 
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'address':
									?>
									<textarea id="<?php echo esc_attr($field_name); ?>" 
											name="<?php echo esc_attr($field_name); ?>" 
											class="webcu-form-control uem-attendee-input" 
											rows="3" 
											<?php echo esc_attr($required_attr); ?>><?php echo esc_textarea($field_value); ?></textarea>
									<?php
									break;
								
								case 'vegetarian':
									?>
									<select id="<?php echo esc_attr($field_name); ?>" 
											name="<?php echo esc_attr($field_name); ?>" 
											class="webcu-form-control uem-attendee-input" 
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
											class="webcu-form-control uem-attendee-input" 
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
										class="webcu-form-control uem-attendee-input" 
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
										class="webcu-form-control uem-attendee-input" 
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
						$field_name = 'uem_attendee_' . $attendee_number . '_custom_' . $field_id;
						
						// Get saved value if any
						$field_value = isset( $_POST[ $field_name ] ) ? (is_array($_POST[ $field_name ]) ? array_map('sanitize_text_field', $_POST[ $field_name ]) : sanitize_text_field($_POST[ $field_name ])) : '';
						
						?>
						<div class="webcu-form-group webcu-custom-field webcu-field-<?php echo esc_attr($field_id); ?>">
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
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo is_string($field_value) ? esc_attr($field_value) : ''; ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'number':
									?>
									<input type="number" 
										id="<?php echo esc_attr($field_name); ?>" 
										name="<?php echo esc_attr($field_name); ?>" 
										class="webcu-form-control uem-attendee-input" 
										value="<?php echo is_string($field_value) ? esc_attr($field_value) : ''; ?>"
										<?php echo esc_attr($required_attr); ?>>
									<?php
									break;
								
								case 'select':
									$options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
									?>
									<select id="<?php echo esc_attr($field_name); ?>" 
											name="<?php echo esc_attr($field_name); ?>" 
											class="webcu-form-control uem-attendee-input" 
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
									<div class="webcu-checkbox-group">
										<?php foreach ($options_array as $option) : 
											$option_value = esc_attr($option);
											$checked = '';
											if (is_array($field_value) && in_array($option_value, $field_value)) {
												$checked = 'checked';
											}
										?>
											<label class="webcu-checkbox-label">
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
									<div class="webcu-radio-group">
										<?php foreach ($options_array as $option) : ?>
											<label class="webcu-radio-label">
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
										class="webcu-form-control uem-attendee-input" 
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


	/**
	 * Validate attendee fields during checkout
	 */
	public static function webcu_validate_attendee_fields ( $data = null, $errors = null ) {
		// Prevent duplicate validation - use static flag at the very start
		static $validated = false;
		if ( $validated ) {
			return;
		}
		
		// Mark as validated immediately to prevent any duplicate calls
		$validated = true;
		
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
			// Validate each attendee's required fields
			for ( $i = 1; $i <= $total_quantity; $i++ ) {
				$name_key = 'uem_attendee_name_' . $i;
				$phone_key = 'uem_attendee_phone_' . $i;
				$email_key = 'uem_attendee_email_' . $i;
				
				$name = isset( $_POST[ $name_key ] ) ? trim( sanitize_text_field( $_POST[ $name_key ] ) ) : '';
				$phone = isset( $_POST[ $phone_key ] ) ? trim( sanitize_text_field( $_POST[ $phone_key ] ) ) : '';
				$email = isset( $_POST[ $email_key ] ) ? trim( sanitize_email( $_POST[ $email_key ] ) ) : '';
				
				if ( empty( $name ) ) {
					$error_message = sprintf( __( 'Attendee %d: Name is required.', 'ultimate-events-manager' ), $i );
					wc_add_notice( $error_message, 'error' );
					if ( $errors && is_a( $errors, 'WP_Error' ) ) {
						$errors->add( 'uem_attendee_name_' . $i, $error_message );
					}
				}
				
				if ( empty( $phone ) ) {
					$error_message = sprintf( __( 'Attendee %d: Phone is required.', 'ultimate-events-manager' ), $i );
					wc_add_notice( $error_message, 'error' );
					if ( $errors && is_a( $errors, 'WP_Error' ) ) {
						$errors->add( 'uem_attendee_phone_' . $i, $error_message );
					}
				}
				
				// Validate email format if provided
				if ( ! empty( $email ) && ! is_email( $email ) ) {
					$error_message = sprintf( __( 'Attendee %d: Please enter a valid email address.', 'ultimate-events-manager' ), $i );
					wc_add_notice( $error_message, 'error' );
					if ( $errors && is_a( $errors, 'WP_Error' ) ) {
						$errors->add( 'uem_attendee_email_' . $i, $error_message );
					}
				}
			}
		}
	}
	
	/**
	 * Save attendee data to order item
	 */
	public static function webcu_save_attendee_data_to_order_item( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['uem_event_id'] ) ) {
			$item->update_meta_data( '_uem_event_id', $values['uem_event_id'] );
			$item->update_meta_data( '_uem_ticket_index', $values['uem_ticket_index'] );
			$item->update_meta_data( '_uem_ticket_name', $values['uem_ticket_name'] );
		}
	}
	
	/**
	 * Save attendee data to order
	 */
	public static function webcu_save_attendee_data_to_order( $order_id ) {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}
		
		$order = wc_get_order( $order_id );
		if ( ! $order ) {
			return;
		}
		
		$attendees = array();
		$total_quantity = 0;
		
		// Get total quantity
		foreach ( $order->get_items() as $item_id => $item ) {
			if ( $item->get_meta( '_uem_event_id' ) ) {
				$total_quantity += $item->get_quantity();
			}
		}
		
		// Save attendee data
		for ( $i = 1; $i <= $total_quantity; $i++ ) {
			$name = isset( $_POST['uem_attendee_name_' . $i] ) ? sanitize_text_field( $_POST['uem_attendee_name_' . $i] ) : '';
			$phone = isset( $_POST['uem_attendee_phone_' . $i] ) ? sanitize_text_field( $_POST['uem_attendee_phone_' . $i] ) : '';
			$email = isset( $_POST['uem_attendee_email_' . $i] ) ? sanitize_email( $_POST['uem_attendee_email_' . $i] ) : '';
			
			if ( $name || $phone || $email ) {
				$attendees[] = array(
					'name' => $name,
					'phone' => $phone,
					'email' => $email,
				);
			}
		}
		
		if ( ! empty( $attendees ) ) {
			update_post_meta( $order_id, '_uem_attendees', $attendees );
		}
	}
	
	/**
	 * Display attendee data in order details
	 */
	public static function webcu_display_attendee_data_in_order( $item_id, $item, $order ) {
		$event_id = $item->get_meta( '_uem_event_id' );
		if ( ! $event_id ) {
			return;
		}
		
		$attendees = get_post_meta( $order->get_id(), '_uem_attendees', true );
		if ( ! is_array( $attendees ) || empty( $attendees ) ) {
			return;
		}
		
		echo '<div class="uem-attendee-data">';
		echo '<h4>' . __( 'Attendee Details:', 'ultimate-events-manager' ) . '</h4>';
		echo '<ul>';
		foreach ( $attendees as $index => $attendee ) {
			echo '<li>';
			echo '<strong>' . __( 'Attendee', 'ultimate-events-manager' ) . ' ' . ( $index + 1 ) . ':</strong><br>';
			if ( ! empty( $attendee['name'] ) ) {
				echo __( 'Name:', 'ultimate-events-manager' ) . ' ' . esc_html( $attendee['name'] ) . '<br>';
			}
			if ( ! empty( $attendee['phone'] ) ) {
				echo __( 'Phone:', 'ultimate-events-manager' ) . ' ' . esc_html( $attendee['phone'] ) . '<br>';
			}
			if ( ! empty( $attendee['email'] ) ) {
				echo __( 'Email:', 'ultimate-events-manager' ) . ' ' . esc_html( $attendee['email'] ) . '<br>';
			}
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
	}
	
	/**
	 * Display attendee data in admin order details
	 */
	public static function webcu_display_attendee_data_in_admin( $order ) {
		$attendees = get_post_meta( $order->get_id(), '_uem_attendees', true );
		if ( ! is_array( $attendees ) || empty( $attendees ) ) {
			return;
		}
		
		echo '<div class="uem-attendee-data">';
		echo '<h3>' . __( 'Attendee Details', 'ultimate-events-manager' ) . '</h3>';
		echo '<table class="widefat">';
		echo '<thead><tr><th>' . __( 'Name', 'ultimate-events-manager' ) . '</th><th>' . __( 'Phone', 'ultimate-events-manager' ) . '</th><th>' . __( 'Email', 'ultimate-events-manager' ) . '</th></tr></thead>';
		echo '<tbody>';
		foreach ( $attendees as $attendee ) {
			echo '<tr>';
			echo '<td>' . esc_html( $attendee['name'] ) . '</td>';
			echo '<td>' . esc_html( $attendee['phone'] ) . '</td>';
			echo '<td>' . esc_html( $attendee['email'] ) . '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		echo '</div>';
	}
	
	/**
	 * Update checkout fields when cart updates
	 */
	public static function webcu_update_checkout_fields() {
		// This will trigger the checkout fields to be regenerated
		// The add_attendee_fields method will be called again with updated cart
	}
	
	/**
	 * AJAX update cart
	 */
	public static function webcu_ajax_update_cart() {
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
			$tickets = get_post_meta( $event_id, '_webcu_tk_tickets', true );
			if ( ! is_array( $tickets ) || ! isset( $tickets[ $ticket_index ] ) ) {
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
				$product_id = self::webcu_create_or_update_ticket_product( $event_id, $ticket, $ticket_index, 0 );
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
				$reflection = new ReflectionClass( $checkout );
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
			self::webcu_display_attendee_fields_section_ajax();
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

