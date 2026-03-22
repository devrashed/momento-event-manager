<?php
/**
 * Template Functions
 *
 * @package Ultimate_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render WooCommerce registration
 */

function wtmem_uem_render_woocommerce_registration( $event_id, $tickets ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return;
	}
	
	$cart_items = WC()->cart->get_cart();
	?>
	<div class="uem-woocommerce-registration" data-event-id="<?php echo esc_attr( $event_id ); ?>">
		<div class="uem-ticket-selection">
			<h3><?php echo esc_html( 'Select Tickets', 'mega-event-manager' ); ?></h3>
			<table class="uem-tickets-table">
				<thead>
					<tr>
						<th><?php echo esc_html( 'Ticket Type', 'mega-event-manager' ); ?></th>
						<th><?php echo esc_html( 'Price', 'mega-event-manager' ); ?></th>
						<th><?php echo esc_html( 'Quantity', 'mega-event-manager' ); ?></th>
						<th><?php echo esc_html( 'Total', 'mega-event-manager' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					foreach ( $tickets as $index => $ticket ):

						$cart_item_key = '';
						$cart_quantity = 0;
						
						foreach ( $cart_items as $key => $item ) {
							if ( isset( $item['uem_event_id'] ) && $item['uem_event_id'] == $event_id && isset( $item['uem_ticket_index'] ) && $item['uem_ticket_index'] == $index ) {
								$cart_item_key = $key;
								$cart_quantity = $item['quantity'];
								break;
							}
						}
						?>
						<tr>
							<td>
								<strong><?php echo esc_html($ticket['name'] ?? ''); ?></strong>
								<?php if ( ! empty( $ticket['description'] ) ) : ?>
									<br><small><?php echo esc_html( $ticket['description'] ); ?></small>
								<?php endif; ?>
							</td>
							<td><?php echo wc_price( $ticket['price'] ?? 0 ); ?></td>
							<td>
								<input type="number" 
									class="uem-ticket-quantity" 
									data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>"
									data-ticket-index="<?php echo esc_attr( $index ); ?>"
									data-ticket-price="<?php echo esc_attr( $ticket['price'] ?? 0 ); ?>"
									min="0" 
									max="5"
									value="<?php echo esc_attr( $cart_quantity ); ?>"
									style="width: 80px;">
							</td>
							<td class="uem-ticket-total" data-ticket-index="<?php echo esc_attr( $index ); ?>"><?php echo wc_price( ( $ticket['price'] ?? 0 ) * $cart_quantity ); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td colspan="3" style="text-align: right;"><strong><?php echo esc_html( 'Grand Total:', 'mega-event-manager' ); ?></strong></td>
						<td class="uem-grand-total"><strong><?php 
							$grand_total = 0;
							foreach ( $tickets as $index => $ticket ) {
								$cart_quantity = 0;
								foreach ( $cart_items as $key => $item ) {
									if ( isset( $item['uem_event_id'] ) && $item['uem_event_id'] == $event_id && isset( $item['uem_ticket_index'] ) && $item['uem_ticket_index'] == $index ) {
										$cart_quantity = $item['quantity'];
										break;
									}
								}
								$grand_total += ( $ticket['price'] ?? 0 ) * $cart_quantity;
							}
							echo wc_price( $grand_total ); 
						?></strong></td>
					</tr>
				</tfoot>
			</table>
		</div>
		
		<div class="uem-checkout-form">
			<?php
			// Display checkout form even with empty cart
			
			if ( function_exists( 'WC' ) && WC()->cart ) {
				// Calculate totals (even if cart is empty)
				WC()->cart->calculate_totals();
				
				// Get checkout object
				$checkout = WC()->checkout();
				
				// Output checkout form
				echo '<div class="woocommerce">';
				wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) );
				echo '</div>';
			}


			?>
		</div>
	</div>
	<?php
}

/**
 * Render simple registration form
 */

function wtmem_uem_render_simple_registration___old( $event_id, $tickets ) {
	
	// Get registration form field settings from the metabox
	$regi_data = get_post_meta( $event_id, 'registration_form_data_types', true );
	if ( ! is_array( $regi_data ) ) {
		$regi_data = array();
	}
	$predefined_fields = isset( $regi_data['predefined_fields'] ) ? $regi_data['predefined_fields'] : array();
	$custom_fields     = isset( $regi_data['custom_fields'] ) ? $regi_data['custom_fields'] : array();

	// Map predefined field IDs to their input types
	$field_type_map = array(
		'firstname'     => 'text',
		'lastname'      => 'text',
		'email_address' => 'email',
		'phone_number'  => 'tel',
		'address'       => 'textarea',
		'designation'   => 'text',
		'website'       => 'url',
		'vegetarian'    => 'checkbox',
		'company_name'  => 'text',
		'gender'        => 'select',
		'dob'           => 'date',
	);

	// Gender options
	$gender_options = array(
		''       => esc_html__( '-- Select --', 'mega-event-manager' ),
		'male'   => esc_html__( 'Male', 'mega-event-manager' ),
		'female' => esc_html__( 'Female', 'mega-event-manager' ),
		'other'  => esc_html__( 'Other', 'mega-event-manager' ),
	);
	?>
	<div class="uem-simple-registration">
		<form id="uem-registration-form" method="post">
			<?php wp_nonce_field( 'uem_registration_nonce' ); ?>
			<input type="hidden" name="uem_event_id" value="<?php echo esc_attr( $event_id ); ?>">
			
			<div class="uem-registration-details">
				<h3><?php echo esc_html__( 'Your Details', 'mega-event-manager' ); ?></h3>

				<?php
				// Render enabled predefined fields
				foreach ( $predefined_fields as $field_id => $field_settings ) :
					if ( empty( $field_settings['enabled'] ) ) {
						continue;
					}
					$label      = ! empty( $field_settings['label'] ) ? $field_settings['label'] : ucfirst( str_replace( '_', ' ', $field_id ) );
					$is_required = ! empty( $field_settings['required'] );
					$input_type = isset( $field_type_map[ $field_id ] ) ? $field_type_map[ $field_id ] : 'text';
					$field_name = 'uem_regi_' . $field_id;
				?>
					<p>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php echo esc_html( $label ); ?>
							<?php if ( $is_required ) : ?>
								<span class="required">*</span>
							<?php endif; ?>
						</label>

						<?php if ( 'textarea' === $input_type ) : ?>
							<textarea name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>" 
								rows="3"
								<?php echo $is_required ? 'required' : ''; ?>></textarea>

						<?php elseif ( 'select' === $input_type && 'gender' === $field_id ) : ?>
							<select name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>"
								<?php echo $is_required ? 'required' : ''; ?>>
								<?php foreach ( $gender_options as $val => $text ) : ?>
									<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $text ); ?></option>
								<?php endforeach; ?>
							</select>

						<?php elseif ( 'checkbox' === $input_type ) : ?>
							<input type="checkbox" 
								name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>" 
								value="1"
								<?php echo $is_required ? 'required' : ''; ?>>

						<?php else : ?>
							<input type="<?php echo esc_attr( $input_type ); ?>" 
								name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>"
								<?php echo $is_required ? 'required' : ''; ?>>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>

				<?php
				// Render enabled custom fields
				foreach ( $custom_fields as $cf_id => $cf ) :
					$cf_label    = ! empty( $cf['label'] ) ? $cf['label'] : $cf_id;
					$cf_type     = ! empty( $cf['type'] ) ? $cf['type'] : 'text';
					$cf_required = ( isset( $cf['required'] ) && 'yes' === $cf['required'] );
					$cf_options  = ! empty( $cf['options'] ) ? array_map( 'trim', explode( ',', $cf['options'] ) ) : array();
					$cf_name     = 'uem_custom_' . $cf_id;
				?>
					<p>
						<label for="<?php echo esc_attr( $cf_name ); ?>">
							<?php echo esc_html( $cf_label ); ?>
							<?php if ( $cf_required ) : ?>
								<span class="required">*</span>
							<?php endif; ?>
						</label>

						<?php if ( 'textarea' === $cf_type ) : ?>
							<textarea name="<?php echo esc_attr( $cf_name ); ?>" 
								id="<?php echo esc_attr( $cf_name ); ?>" 
								rows="3"
								<?php echo $cf_required ? 'required' : ''; ?>></textarea>

						<?php elseif ( 'select' === $cf_type && ! empty( $cf_options ) ) : ?>
							<select name="<?php echo esc_attr( $cf_name ); ?>" 
								id="<?php echo esc_attr( $cf_name ); ?>"
								<?php echo $cf_required ? 'required' : ''; ?>>
								<option value=""><?php echo esc_html__( '-- Select --', 'mega-event-manager' ); ?></option>
								<?php foreach ( $cf_options as $opt ) : ?>
									<option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
								<?php endforeach; ?>
							</select>

						<?php elseif ( 'radio' === $cf_type && ! empty( $cf_options ) ) : ?>
							<?php foreach ( $cf_options as $opt ) : ?>
								<label class="uem-radio-label">
									<input type="radio" 
										name="<?php echo esc_attr( $cf_name ); ?>" 
										value="<?php echo esc_attr( $opt ); ?>"
										<?php echo $cf_required ? 'required' : ''; ?>>
									<?php echo esc_html( $opt ); ?>
								</label>
							<?php endforeach; ?>

						<?php elseif ( 'checkbox' === $cf_type && ! empty( $cf_options ) ) : ?>
							<?php foreach ( $cf_options as $opt ) : ?>
								<label class="uem-checkbox-label">
									<input type="checkbox" 
										name="<?php echo esc_attr( $cf_name ); ?>[]" 
										value="<?php echo esc_attr( $opt ); ?>">
									<?php echo esc_html( $opt ); ?>
								</label>
							<?php endforeach; ?>

						<?php else : ?>
							<input type="<?php echo esc_attr( $cf_type ); ?>" 
								name="<?php echo esc_attr( $cf_name ); ?>" 
								id="<?php echo esc_attr( $cf_name ); ?>"
								<?php echo $cf_required ? 'required' : ''; ?>>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>

			</div>
			
			<div class="uem-ticket-selection">
				<h3><?php echo esc_html__( 'Select Tickets', 'mega-event-manager' ); ?></h3>
				<table class="uem-tickets-table">
					<thead>
                        	<tr>
                        		<th><?php echo esc_html__( 'Ticket Type', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Price', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Available', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Quantity', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Total', 'mega-event-manager' ); ?></th>
                        	</tr>
					</thead>
					<tbody>
						<?php foreach ( $tickets as $index => $ticket ) : ?>
							<tr>
								<td>
									<strong><?php echo esc_html( $ticket['name'] ?? '' ); ?></strong>
									<?php if ( ! empty( $ticket['description'] ) ) : ?>
										<br><small><?php echo esc_html( $ticket['description'] ); ?></small>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( number_format( $ticket['price'] ?? 0, 2 ) ); ?></td>
								<td><?php echo esc_html( $ticket['quantity'] ?? 0 ); ?></td>
								<td>
									<input type="number" 
										class="uem-ticket-quantity" 
										name="uem_ticket_quantity_<?php echo esc_attr( $index ); ?>"
										data-ticket-index="<?php echo esc_attr( $index ); ?>"
										data-ticket-price="<?php echo esc_attr( $ticket['price'] ?? 0 ); ?>"
										min="0" 
										max=""
										value="0"
										style="width: 80px;">
								</td>
								<td class="uem-ticket-total" data-ticket-index="<?php echo esc_attr( $index ); ?>"><?php echo esc_html( number_format( 0, 2 ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4" style="text-align: right;"><strong><?php echo esc_html__( 'Grand Total:', 'mega-event-manager' ); ?></strong></td>
							<td class="uem-grand-total"><strong><?php echo esc_html( number_format( 0, 2 ) ); ?></strong></td>
						</tr>
					</tfoot>
				</table>
			</div>
			
			<div class="uem-attendee-details" id="uem-attendee-details" style="display: none;">
				<h3><?php echo esc_html__( 'Attendee Details', 'mega-event-manager' ); ?></h3>
				<div id="uem-attendee-fields"></div>
			</div>
			
			<p>
				<button type="submit" name="uem_registration_submit" class="button uem-submit-registration">
					<?php echo esc_html__( 'Submit Registration', 'mega-event-manager' ); ?>
				</button>
			</p>
		</form>
	</div>
	<?php
}


function wtmem_uem_render_simple_registration( $event_id, $tickets ) {
	
	// Get registration form field settings from the metabox
	$regi_data = get_post_meta( $event_id, 'registration_form_data_types', true );
	if ( ! is_array( $regi_data ) ) {
		$regi_data = array();
	}
	$predefined_fields = isset( $regi_data['predefined_fields'] ) ? $regi_data['predefined_fields'] : array();
	$custom_fields     = isset( $regi_data['custom_fields'] ) ? $regi_data['custom_fields'] : array();

	// Map predefined field IDs to their input types
	$field_type_map = array(
		'firstname'     => 'text',
		'lastname'      => 'text',
		'email_address' => 'email',
		'phone_number'  => 'tel',
		'address'       => 'textarea',
		'designation'   => 'text',
		'website'       => 'url',
		'vegetarian'    => 'checkbox',
		'company_name'  => 'text',
		'gender'        => 'select',
		'dob'           => 'date',
	);

	// Gender options
	$gender_options = array(
		''       => esc_html__( '-- Select --', 'mega-event-manager' ),
		'male'   => esc_html__( 'Male', 'mega-event-manager' ),
		'female' => esc_html__( 'Female', 'mega-event-manager' ),
		'other'  => esc_html__( 'Other', 'mega-event-manager' ),
	);
	?>
	<div class="uem-simple-registration">
		<form id="uem-registration-form" method="post">
			<?php wp_nonce_field( 'uem_registration_nonce' ); ?>
			<input type="hidden" name="uem_event_id" value="<?php echo esc_attr( $event_id ); ?>">
			
			<div class="uem-registration-details">
				<h3><?php echo esc_html__( 'Your Details', 'mega-event-manager' ); ?></h3>

				<?php
				// Render enabled predefined fields
				foreach ( $predefined_fields as $field_id => $field_settings ) :
					if ( empty( $field_settings['enabled'] ) ) {
						continue;
					}
					$label      = ! empty( $field_settings['label'] ) ? $field_settings['label'] : ucfirst( str_replace( '_', ' ', $field_id ) );
					$is_required = ! empty( $field_settings['required'] );
					$input_type = isset( $field_type_map[ $field_id ] ) ? $field_type_map[ $field_id ] : 'text';
					$field_name = 'uem_regi_' . $field_id;
				?>
					<p>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php echo esc_html( $label ); ?>
							<?php if ( $is_required ) : ?>
								<span class="required">*</span>
							<?php endif; ?>
						</label>

						<?php if ( 'textarea' === $input_type ) : ?>
							<textarea name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>" 
								rows="3"
								<?php echo $is_required ? 'required' : ''; ?>></textarea>

						<?php elseif ( 'select' === $input_type && 'gender' === $field_id ) : ?>
							<select name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>"
								<?php echo $is_required ? 'required' : ''; ?>>
								<?php foreach ( $gender_options as $val => $text ) : ?>
									<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $text ); ?></option>
								<?php endforeach; ?>
							</select>

						<?php elseif ( 'checkbox' === $input_type ) : ?>
							<input type="checkbox" 
								name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>" 
								value="1"
								<?php echo $is_required ? 'required' : ''; ?>>

						<?php else : ?>
							<input type="<?php echo esc_attr( $input_type ); ?>" 
								name="<?php echo esc_attr( $field_name ); ?>" 
								id="<?php echo esc_attr( $field_name ); ?>"
								<?php echo $is_required ? 'required' : ''; ?>>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>

				<?php
				// Render enabled custom fields
				foreach ( $custom_fields as $cf_id => $cf ) :
					$cf_label    = ! empty( $cf['label'] ) ? $cf['label'] : $cf_id;
					$cf_type     = ! empty( $cf['type'] ) ? $cf['type'] : 'text';
					$cf_required = ( isset( $cf['required'] ) && 'yes' === $cf['required'] );
					$cf_options  = ! empty( $cf['options'] ) ? array_map( 'trim', explode( ',', $cf['options'] ) ) : array();
					$cf_name     = 'uem_custom_' . $cf_id;
				?>
					<p>
						<label for="<?php echo esc_attr( $cf_name ); ?>">
							<?php echo esc_html( $cf_label ); ?>
							<?php if ( $cf_required ) : ?>
								<span class="required">*</span>
							<?php endif; ?>
						</label>

						<?php if ( 'textarea' === $cf_type ) : ?>
							<textarea name="<?php echo esc_attr( $cf_name ); ?>" 
								id="<?php echo esc_attr( $cf_name ); ?>" 
								rows="3"
								<?php echo $cf_required ? 'required' : ''; ?>></textarea>

						<?php elseif ( 'select' === $cf_type && ! empty( $cf_options ) ) : ?>
							<select name="<?php echo esc_attr( $cf_name ); ?>" 
								id="<?php echo esc_attr( $cf_name ); ?>"
								<?php echo $cf_required ? 'required' : ''; ?>>
								<option value=""><?php echo esc_html__( '-- Select --', 'mega-event-manager' ); ?></option>
								<?php foreach ( $cf_options as $opt ) : ?>
									<option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
								<?php endforeach; ?>
							</select>

						<?php elseif ( 'radio' === $cf_type && ! empty( $cf_options ) ) : ?>
							<?php foreach ( $cf_options as $opt ) : ?>
								<label class="uem-radio-label">
									<input type="radio" 
										name="<?php echo esc_attr( $cf_name ); ?>" 
										value="<?php echo esc_attr( $opt ); ?>"
										<?php echo $cf_required ? 'required' : ''; ?>>
									<?php echo esc_html( $opt ); ?>
								</label>
							<?php endforeach; ?>

						<?php elseif ( 'checkbox' === $cf_type && ! empty( $cf_options ) ) : ?>
							<?php foreach ( $cf_options as $opt ) : ?>
								<label class="uem-checkbox-label">
									<input type="checkbox" 
										name="<?php echo esc_attr( $cf_name ); ?>[]" 
										value="<?php echo esc_attr( $opt ); ?>">
									<?php echo esc_html( $opt ); ?>
								</label>
							<?php endforeach; ?>

						<?php else : ?>
							<input type="<?php echo esc_attr( $cf_type ); ?>" 
								name="<?php echo esc_attr( $cf_name ); ?>" 
								id="<?php echo esc_attr( $cf_name ); ?>"
								<?php echo $cf_required ? 'required' : ''; ?>>
						<?php endif; ?>
					</p>
				<?php endforeach; ?>

			</div>
			
			<div class="uem-ticket-selection">
				<h3><?php echo esc_html__( 'Select Tickets', 'mega-event-manager' ); ?></h3>
				<table class="uem-tickets-table">
					<thead>
                        	<tr>
                        		<th><?php echo esc_html__( 'Ticket Type', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Price', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Available', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Quantity', 'mega-event-manager' ); ?></th>
                        		<th><?php echo esc_html__( 'Total', 'mega-event-manager' ); ?></th>
                        	</tr>
					</thead>
					<tbody>
						<?php foreach ( $tickets as $index => $ticket ) : ?>
							<tr>
								<td>
									<strong><?php echo esc_html( $ticket['name'] ?? '' ); ?></strong>
									<?php if ( ! empty( $ticket['description'] ) ) : ?>
										<br><small><?php echo esc_html( $ticket['description'] ); ?></small>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( number_format( $ticket['price'] ?? 0, 2 ) ); ?></td>
								<td><?php echo esc_html( $ticket['quantity'] ?? 0 ); ?></td>
								<td>
									<input type="number" 
										class="uem-ticket-quantity" 
										name="uem_ticket_quantity_<?php echo esc_attr( $index ); ?>"
										data-ticket-index="<?php echo esc_attr( $index ); ?>"
										data-ticket-price="<?php echo esc_attr( $ticket['price'] ?? 0 ); ?>"
										min="0" 
										max=""
										value="0"
										style="width: 80px;">
								</td>
								<td class="uem-ticket-total" data-ticket-index="<?php echo esc_attr( $index ); ?>"><?php echo esc_html( number_format( 0, 2 ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4" style="text-align: right;"><strong><?php echo esc_html__( 'Grand Total:', 'mega-event-manager' ); ?></strong></td>
							<td class="uem-grand-total"><strong><?php echo esc_html( number_format( 0, 2 ) ); ?></strong></td>
						</tr>
					</tfoot>
				</table>
			</div>
			
			<div class="uem-attendee-details" id="uem-attendee-details" style="display: none;">
				<h3><?php echo esc_html__( 'Attendee Details', 'mega-event-manager' ); ?></h3>
				<div id="uem-attendee-fields"></div>
			</div>
			
			<p>
				<button type="submit" name="uem_registration_submit" class="button uem-submit-registration">
					<?php echo esc_html__( 'Submit Registration', 'mega-event-manager' ); ?>
				</button>
			</p>
		</form>
	</div>
	<?php
}