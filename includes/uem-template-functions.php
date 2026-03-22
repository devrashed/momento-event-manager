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

function wtmem_uem_render_simple_registration( $event_id, $tickets ) {
	?>
	<div class="uem-simple-registration">
		<form id="uem-registration-form" method="post">
			<?php wp_nonce_field( 'uem_registration_nonce' ); ?>
			<input type="hidden" name="uem_event_id" value="<?php echo esc_attr( $event_id ); ?>">
			
			<div class="uem-registration-details">
				<h3><?php echo esc_html__( 'Your Details', 'mega-event-manager' ); ?></h3>
					<p>
						<label for="uem_registration_name"><?php echo esc_html__( 'Name', 'mega-event-manager' ); ?> <span class="required">*</span></label>
						<input type="text" name="uem_registration_name" id="uem_registration_name" required>
					</p>
					<p>
						<label for="uem_registration_phone"><?php echo esc_html__( 'Phone', 'mega-event-manager' ); ?> <span class="required">*</span></label>
						<input type="tel" name="uem_registration_phone" id="uem_registration_phone" required>
					</p>
					<p>
						<label for="uem_registration_email"><?php echo esc_html__( 'Email', 'mega-event-manager' ); ?></label>
						<input type="email" name="uem_registration_email" id="uem_registration_email">
					</p>
					<p>
						<label for="uem_registration_address"><?php echo esc_html__( 'Address', 'mega-event-manager' ); ?></label>
						<textarea name="uem_registration_address" id="uem_registration_address" rows="3"></textarea>
					</p>
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
									<strong><?php echo esc_html( $ticket['name'] ); ?></strong>
									<?php if ( ! empty( $ticket['description'] ) ) : ?>
										<br><small><?php echo esc_html( $ticket['description'] ); ?></small>
									<?php endif; ?>
								</td>
								<td><?php echo wc_price( $ticket['price'] ); ?></td>
								<td><?php echo esc_html( $ticket['quantity'] ); ?></td>
								<td>
									<input type="number" 
										class="uem-ticket-quantity" 
										name="uem_ticket_quantity_<?php echo esc_attr( $index ); ?>"
										data-ticket-index="<?php echo esc_attr( $index ); ?>"
										data-ticket-price="<?php echo esc_attr( $ticket['price'] ); ?>"
										min="0" 
										max="<?php echo esc_attr( $ticket['quantity'] ); ?>"
										value="0"
										style="width: 80px;">
								</td>
								<td class="uem-ticket-total" data-ticket-index="<?php echo esc_attr( $index ); ?>"><?php echo wc_price( 0 ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="4" style="text-align: right;"><strong><?php echo esc_html__( 'Grand Total:', 'mega-event-manager' ); ?></strong></td>
							<td class="uem-grand-total"><strong><?php echo wc_price( 0 ); ?></strong></td>
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