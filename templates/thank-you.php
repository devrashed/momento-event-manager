<?php
/**
 * Thank You Page Template
 *
 * @package Ultimate_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$registration_id = isset( $_GET['registration_id'] ) ? intval( $_GET['registration_id'] ) : 0;

if ( ! $registration_id ) {
	wp_die( __( 'Invalid registration ID.', 'ultimate-events-manager' ) );
}

$registration = get_post( $registration_id );
if ( ! $registration || $registration->post_type !== 'uem_registration' ) {
	wp_die( __( 'Registration not found.', 'ultimate-events-manager' ) );
}

$event_id = get_post_meta( $registration_id, '_uem_event_id', true );
$event = get_post( $event_id );

$registration_name = get_post_meta( $registration_id, '_uem_registration_name', true );
$registration_phone = get_post_meta( $registration_id, '_uem_registration_phone', true );
$registration_email = get_post_meta( $registration_id, '_uem_registration_email', true );
$registration_address = get_post_meta( $registration_id, '_uem_registration_address', true );
$ticket_quantities = get_post_meta( $registration_id, '_uem_ticket_quantities', true );
$attendees = get_post_meta( $registration_id, '_uem_attendees', true );
$registration_date = get_post_meta( $registration_id, '_uem_registration_date', true );

$plugin = Ultimate_Events_Manager::get_instance();
$is_woocommerce = $plugin->is_woocommerce_enabled() && class_exists( 'WooCommerce' );

// If WooCommerce, try to get order data
$order_id = null;
if ( $is_woocommerce ) {
	$orders = wc_get_orders( array(
		'meta_key' => '_uem_attendees',
		'meta_value' => $registration_id,
		'limit' => 1,
	) );
	if ( ! empty( $orders ) ) {
		$order_id = $orders[0]->get_id();
	}
}
?>

<div class="uem-thank-you-page">
	<div class="uem-thank-you-content">
		<h1><?php echo esc_html( 'Thank You for Your Registration!', 'ultimate-events-manager' ); ?></h1>
		
		<div class="uem-registration-summary">
			<h2><?php echo esc_html( 'Registration Summary', 'ultimate-events-manager' ); ?></h2>
			
			<div class="uem-event-info">
				<h3><?php echo esc_html( 'Event Information', 'ultimate-events-manager' ); ?></h3>
				<?php if ( $event ) : ?>
					<p><strong><?php echo esc_html( 'Event:', 'ultimate-events-manager' ); ?></strong> <?php echo esc_html( $event->post_title ); ?></p>
					<?php
					$event_date = get_post_meta( $event_id, '_uem_event_date', true );
					$event_time = get_post_meta( $event_id, '_uem_event_time', true );
					$event_location = get_post_meta( $event_id, '_uem_event_location', true );
					$event_address = get_post_meta( $event_id, '_uem_event_address', true );
					?>
					<?php if ( $event_date ) : ?>
						<p><strong><?php echo esc_html( 'Date:', 'ultimate-events-manager' ); ?></strong>         
							<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $event_date ) ) ); ?>
							<?php if ( $event_time ) : ?>
								<?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( $event_time ) ) ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if ( $event_location ) : ?>
						<p><strong><?php echo esc_html( 'Location:', 'ultimate-events-manager' ); ?></strong> <?php echo esc_html( $event_location ); ?></p>
					<?php endif; ?>
					<?php if ( $event_address ) : ?>
						<p><strong><?php echo esc_html( 'Address:', 'ultimate-events-manager' ); ?></strong><br><?php echo nl2br( esc_html( $event_address ) ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			
			<div class="uem-registrant-info">
				<h3><?php echo esc_html( 'Registrant Information', 'ultimate-events-manager' ); ?></h3>
				<?php if ( $registration_name ) : ?>
					<p><strong><?php echo esc_html( 'Name:', 'ultimate-events-manager' ); ?></strong> <?php echo esc_html( $registration_name ); ?></p>
				<?php endif; ?>
				<?php if ( $registration_phone ) : ?>
					<p><strong><?php echo esc_html( 'Phone:', 'ultimate-events-manager' ); ?></strong> <?php echo esc_html( $registration_phone ); ?></p>
				<?php endif; ?>
				<?php if ( $registration_email ) : ?>
					<p><strong><?php echo esc_html( 'Email:', 'ultimate-events-manager' ); ?></strong> <?php echo esc_html( $registration_email ); ?></p>
				<?php endif; ?>
				<?php if ( $registration_address ) : ?>
					<p><strong><?php echo esc_html( 'Address:', 'ultimate-events-manager' ); ?></strong><br><?php echo nl2br( esc_html( $registration_address ) ); ?></p>
				<?php endif; ?>
			</div>
			
			<?php if ( ! empty( $ticket_quantities ) && is_array( $ticket_quantities ) ) : 
				$tickets = get_post_meta( $event_id, '_uem_tickets', true );
				if ( ! is_array( $tickets ) ) {
					$tickets = array();
				}
				?>
				<div class="uem-tickets-info">
					<h3><?php echo esc_html( 'Tickets', 'ultimate-events-manager' ); ?></h3>
					<table class="uem-tickets-table">
						<thead>
							<tr>
								<th><?php echo esc_html( 'Ticket Type', 'ultimate-events-manager' ); ?></th>
								<th><?php echo esc_html( 'Quantity', 'ultimate-events-manager' ); ?></th>
								<th><?php echo esc_html( 'Price', 'ultimate-events-manager' ); ?></th>
								<th><?php echo esc_html( 'Total', 'ultimate-events-manager' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$grand_total = 0;
							foreach ( $ticket_quantities as $index => $quantity ) : 
								if ( isset( $tickets[ $index ] ) && $quantity > 0 ) :
									$ticket = $tickets[ $index ];
									$total = $ticket['price'] * $quantity;
									$grand_total += $total;
									?>
									<tr>
										<td><?php echo esc_html( $ticket['name'] ); ?></td>
										<td><?php echo esc_html( $quantity ); ?></td>
										<td><?php echo wc_price( $ticket['price'] ); ?></td>
										<td><?php echo wc_price( $total ); ?></td>
									</tr>
								<?php endif;
							endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" style="text-align: right;"><strong><?php echo esc_html( 'Grand Total:', 'ultimate-events-manager' ); ?></strong></td>
								<td><strong><?php echo wc_price( $grand_total ); ?></strong></td>
							</tr>
						</tfoot>
					</table>
				</div>
			<?php endif; ?>
			
			<?php if ( ! empty( $attendees ) && is_array( $attendees ) ) : ?>
				<div class="uem-attendees-info">
					<h3><?php echo esc_html( 'Attendee Details', 'ultimate-events-manager' ); ?></h3>
					<table class="uem-attendees-table">
						<thead>
							<tr>
								<th><?php echo esc_html( 'Name', 'ultimate-events-manager' ); ?></th>
								<th><?php echo esc_html( 'Phone', 'ultimate-events-manager' ); ?></th>
								<th><?php echo esc_html( 'Email', 'ultimate-events-manager' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $attendees as $attendee ) : ?>
								<tr>
									<td><?php echo esc_html( $attendee['name'] ); ?></td>
									<td><?php echo esc_html( $attendee['phone'] ); ?></td>
									<td><?php echo esc_html( $attendee['email'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
			
			<?php if ( $registration_date ) : ?>
				<p class="uem-registration-date">
					<strong><?php echo esc_html( 'Registration Date:', 'ultimate-events-manager' ); ?></strong> 
					<?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $registration_date ) ) ); ?>
				</p>
			<?php endif; ?>
			
			<?php if ( $is_woocommerce && $order_id ) : ?>
				<div class="uem-order-info">
					<h3><?php echo esc_html( 'Order Information', 'ultimate-events-manager' ); ?></h3>
					<p>
						<strong><?php echo esc_html( 'Order Number:', 'ultimate-events-manager' ); ?></strong> 
						<a href="<?php echo esc_url( wc_get_endpoint_url( 'view-order', $order_id ) ); ?>">#<?php echo esc_html( $order_id ); ?></a>
					</p>
				</div>
			<?php endif; ?>
		</div>
		
		<p class="uem-thank-you-message">
			<?php echo esc_html( 'We look forward to seeing you at the event!', 'ultimate-events-manager' ); ?>
		</p>
	</div>
</div>

<?php
get_footer();

