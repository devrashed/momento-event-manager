<?php
/**
 * Thank You Page Template
 *
 * @package momento_event_manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$registration_id = isset( $_GET['registration_id'] ) ? intval( $_GET['registration_id'] ) : 0;

if ( ! $registration_id ) {
	wp_die( __( 'Invalid registration ID.', 'momento-event-manager' ) );
}

// Fetch registration from custom table
global $wpdb;
$table_name = $wpdb->prefix . 'wtmem_registrations';
$registration = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table_name WHERE id = %d", $registration_id ) );

if ( ! $registration ) {
	wp_die( __( 'Registration not found.', 'momento-event-manager' ) );
}

$event_id       = intval( $registration->event_id );
$event          = get_post( $event_id );
$reg_data       = maybe_unserialize( $registration->registration_field );
$total_quantity = intval( $registration->quantity );
$subtotal       = floatval( $registration->subtotal_amount );
$order_status   = $registration->order_status;
$created_at     = $registration->created_at;

// Extract fields
$predefined_fields = isset( $reg_data['predefined_fields'] ) ? $reg_data['predefined_fields'] : array();
$custom_fields     = isset( $reg_data['custom_fields'] ) ? $reg_data['custom_fields'] : array();
$ticket_details    = isset( $reg_data['tickets'] ) ? $reg_data['tickets'] : array();
$attendees         = isset( $reg_data['attendees'] ) ? $reg_data['attendees'] : array();

// Get currency symbol
$currency = get_option( 'my_currency', 'USD' );
$currency_symbols = array(
	'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'BDT' => '৳', 'INR' => '₹',
	'JPY' => '¥', 'CAD' => 'C$', 'AUD' => 'A$', 'CNY' => '¥',
);
$currency_symbol = isset( $currency_symbols[ $currency ] ) ? $currency_symbols[ $currency ] : $currency . ' ';

// Get event dates (stored as arrays of dates/times for multi-day support)
$event_dates = get_post_meta( $event_id, 'wtmem_event_dates', true );
$event_start_date = '';
$event_start_time = '';
$event_end_date   = '';
$event_end_time   = '';
$event_date_format = '';
$event_time_format = '';
if ( is_array( $event_dates ) ) {
	// start_date/start_time/end_date/end_time are indexed arrays; grab the first entry
	$event_start_date  = ! empty( $event_dates['start_date'][0] ) ? $event_dates['start_date'][0] : '';
	$event_start_time  = ! empty( $event_dates['start_time'][0] ) ? $event_dates['start_time'][0] : '';
	$event_end_date    = ! empty( $event_dates['end_date'][0] )   ? $event_dates['end_date'][0]   : '';
	$event_end_time    = ! empty( $event_dates['end_time'][0] )   ? $event_dates['end_time'][0]   : '';
	$event_date_format = ! empty( $event_dates['date_format'] )   ? $event_dates['date_format']   : get_option( 'date_format' );
	$event_time_format = ! empty( $event_dates['time_format'] )   ? $event_dates['time_format']   : get_option( 'time_format' );
}

$event_venue   = get_post_meta( $event_id, '_wtmem_vl_venue_name', true );
$event_address = get_post_meta( $event_id, '_wtmem_vl_address', true );

$plugin = momento_event_manager::get_instance();
$is_woocommerce = $plugin->wtmem_is_woocommerce_enabled() && class_exists( 'WooCommerce' );
?>

<div class="uem-thank-you-page">
	<div class="uem-thank-you-content">
		<h1><?php echo esc_html__( 'Thank You for Your Registration!', 'momento-event-manager' ); ?></h1>
		<p><?php printf( esc_html__( 'Your Registration ID is: %s', 'momento-event-manager' ), '<strong>#' . esc_html( $registration_id ) . '</strong>' ); ?>
			&mdash; <span style="color: green; font-weight: 600;"><?php echo esc_html( ucfirst( $order_status ) ); ?></span>
		</p>		
		<div class="uem-registration-summary">
			<h2><?php echo esc_html__( 'Registration Summary', 'momento-event-manager' ); ?></h2>
			
			<!-- Event Info -->
			<div class="uem-event-info">
				<h3><?php echo esc_html__( 'Event Information', 'momento-event-manager' ); ?></h3>
				<?php if ( $event ) : ?>
					<p><strong><?php echo esc_html__( 'Event:', 'momento-event-manager' ); ?></strong> <?php echo esc_html( $event->post_title ); ?></p>
					<?php if ( $event_start_date ) : ?>
						<p><strong><?php echo esc_html__( 'Date:', 'momento-event-manager' ); ?></strong>         
							<?php echo esc_html( date_i18n( $event_date_format, strtotime( $event_start_date ) ) ); ?>
							<?php if ( $event_start_time ) : ?>
								<?php echo ' ' . esc_html( date_i18n( $event_time_format, strtotime( $event_start_date . ' ' . $event_start_time ) ) ); ?>
							<?php endif; ?>
							<?php if ( $event_end_date ) : ?>
								&mdash; <?php echo esc_html( date_i18n( $event_date_format, strtotime( $event_end_date ) ) ); ?>
								<?php if ( $event_end_time ) : ?>
									<?php echo ' ' . esc_html( date_i18n( $event_time_format, strtotime( $event_end_date . ' ' . $event_end_time ) ) ); ?>
								<?php endif; ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					<?php if ( $event_venue ) : ?>
						<p><strong><?php echo esc_html__( 'Venue:', 'momento-event-manager' ); ?></strong> <?php echo esc_html( $event_venue ); ?></p>
					<?php endif; ?>
					<?php if ( $event_address ) : ?>
						<p><strong><?php echo esc_html__( 'Address:', 'momento-event-manager' ); ?></strong><br><?php echo nl2br( esc_html( $event_address ) ); ?></p>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			
			<!-- Registrant Info from registration_field -->
			<div class="uem-registrant-info">
				<h3><?php echo esc_html__( 'Registrant Information', 'momento-event-manager' ); ?></h3>
				<?php foreach ( $predefined_fields as $field_id => $field ) :
					$value = is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'];
					if ( ! empty( $value ) ) : ?>
						<p><strong><?php echo esc_html( $field['label'] ); ?>:</strong> <?php echo esc_html( $value ); ?></p>
				<?php endif;
				endforeach; ?>
				<?php foreach ( $custom_fields as $cf_id => $cf ) :
					$value = is_array( $cf['value'] ) ? implode( ', ', $cf['value'] ) : $cf['value'];
					if ( ! empty( $value ) ) : ?>
						<p><strong><?php echo esc_html( $cf['label'] ); ?>:</strong> <?php echo esc_html( $value ); ?></p>
					<?php endif;
				endforeach; ?>
			</div>
			
			<!-- Tickets -->
			<?php if ( ! empty( $ticket_details ) ) : ?>
				<div class="uem-tickets-info">
					<h3><?php echo esc_html__( 'Tickets', 'momento-event-manager' ); ?></h3>
					<table class="uem-tickets-table">
						<thead>
							<tr>
								<th><?php echo esc_html__( 'Ticket Type', 'momento-event-manager' ); ?></th>
								<th><?php echo esc_html__( 'Quantity', 'momento-event-manager' ); ?></th>
								<th><?php echo esc_html__( 'Price', 'momento-event-manager' ); ?></th>
								<th><?php echo esc_html__( 'Total', 'momento-event-manager' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $ticket_details as $ticket ) : ?>
								<tr>
									<td><?php echo esc_html( $ticket['name'] ); ?></td>
									<td><?php echo esc_html( $ticket['quantity'] ); ?></td>
									<td><?php echo esc_html( $currency_symbol . number_format( $ticket['price'], 2 ) ); ?></td>
									<td><?php echo esc_html( $currency_symbol . number_format( $ticket['line_total'], 2 ) ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" style="text-align: right;"><strong><?php echo esc_html__( 'Grand Total:', 'momento-event-manager' ); ?></strong></td>
								<td><strong><?php echo esc_html( $currency_symbol . number_format( $subtotal, 2 ) ); ?></strong></td>
							</tr>
						</tfoot>
					</table>
				</div>
			<?php endif; ?>
			
			<!-- Attendees -->
			<?php if ( ! empty( $attendees ) ) : ?>
				<div class="uem-attendees-info">
					<h3><?php echo esc_html__( 'Attendee Details', 'momento-event-manager' ); ?></h3>
					<table class="uem-attendees-table">
						<thead>
							<tr>
								<th>#</th>
								<th><?php echo esc_html__( 'Name', 'momento-event-manager' ); ?></th>
								<th><?php echo esc_html__( 'Phone', 'momento-event-manager' ); ?></th>
								<th><?php echo esc_html__( 'Email', 'momento-event-manager' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $attendees as $idx => $attendee ) : ?>
								<tr>
									<td><?php echo esc_html( $idx + 1 ); ?></td>
									<td><?php echo esc_html( $attendee['name'] ); ?></td>
									<td><?php echo esc_html( $attendee['phone'] ); ?></td>
									<td><?php echo esc_html( $attendee['email'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
			
			<?php if ( $created_at ) : ?>
				<p class="uem-registration-date">
					<strong><?php echo esc_html__( 'Registration Date:', 'momento-event-manager' ); ?></strong> 
					<?php echo esc_html( wp_date ( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $created_at ) ) ); ?>
				</p>
			<?php endif; ?>
		</div>
		
		<p class="uem-thank-you-message">
			<?php echo esc_html__( 'A confirmation email has been sent to your email address. We look forward to seeing you at the event!', 'momento-event-manager' ); ?>
		</p>
	</div>
</div>

<?php
get_footer();

