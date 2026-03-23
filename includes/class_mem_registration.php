<?php
/**
 * Registration Handler
 *
 * @package Ultimate_Events_Manager
 */

namespace Wpcraft\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class class_mem_registration {

	/**
	 * Last error message for debugging
	 */
	private static $last_error = '';
		
	/**
	 * Initialize registration
	 */
	/* public static function init() {
		// Handle form submission
		add_action( 'wp', array( __CLASS__, 'wtmem_handle_registration_submission' ) );		
		// AJAX handler for registration
		add_action( 'wp_ajax_uem_submit_registration', array( __CLASS__, 'wtmem_ajax_submit_registration' ) );
		add_action( 'wp_ajax_nopriv_uem_submit_registration', array( __CLASS__, 'wtmem_ajax_submit_registration' ) );
	} */

    public function __construct() {
		// Handle form submission
		add_action( 'wp', array( $this, 'wtmem_handle_registration_submission' ) );
		// AJAX handler for registration
		add_action( 'wp_ajax_uem_submit_registration', array( $this, 'wtmem_ajax_submit_registration' ) );
		add_action( 'wp_ajax_nopriv_uem_submit_registration', array( $this, 'wtmem_ajax_submit_registration' ) );		

	}

	/**
	 * Handle registration submission (non-AJAX fallback)
	 */
	public static function wtmem_handle_registration_submission() {
		if ( ! isset( $_POST['uem_registration_submit'] ) ) {
			return;
		}
		
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'uem_registration_nonce' ) ) {
			return;
		}
		
		$event_id = isset( $_POST['uem_event_id'] ) ? intval( $_POST['uem_event_id'] ) : 0;
		if ( ! $event_id ) {
			return;
		}
		
		$registration_id = self::wtmem_create_registration( $event_id, $_POST );
		
		if ( $registration_id ) {
			wp_safe_redirect( add_query_arg( array(
				'uem_thank_you' => '1',
				'registration_id' => $registration_id,
			), home_url() ) );
			exit;
		}
	}
	
	/**
	 * AJAX submit registration
	 */
	public static function wtmem_ajax_submit_registration() {
		check_ajax_referer( 'uem_nonce', 'nonce' );
		
		$event_id = isset( $_POST['uem_event_id'] ) ? intval( $_POST['uem_event_id'] ) : 0;
		if ( ! $event_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid event ID.', 'momento-event-manager' ) ) );
			return;
		}
		
		$registration_id = self::wtmem_create_registration( $event_id, $_POST );
		
		if ( $registration_id ) {
			wp_send_json_success( array(
				'registration_id' => $registration_id,
				'redirect_url' => add_query_arg( array(
					'uem_thank_you' => '1',
					'registration_id' => $registration_id,
				), home_url() ),
			) );
		} else {
			wp_send_json_error( array( 'message' => self::$last_error ? self::$last_error : __( 'Registration failed. Please try again.', 'momento-event-manager' ) ) );
		}
	}
	
	/**
	 * Create registration - inserts into custom DB table and sends confirmation email
	 *
	 * @param int   $event_id The event post ID
	 * @param array $data     The submitted form data
	 * @return int|false The registration row ID or false on failure
	 */
	private static function wtmem_create_registration( $event_id, $data ) {
		global $wpdb;

		// Ensure DB table exists and schema is up to date
		self::wtmem_ensure_table();

		// Get registration form settings
		$regi_data = get_post_meta( $event_id, 'registration_form_data_types', true );
		if ( ! is_array( $regi_data ) ) {
			$regi_data = array();
		}
		$predefined_fields = isset( $regi_data['predefined_fields'] ) ? $regi_data['predefined_fields'] : array();
		$custom_fields     = isset( $regi_data['custom_fields'] ) ? $regi_data['custom_fields'] : array();

		// Collect predefined field values
		$predefined_values = array();
		foreach ( $predefined_fields as $field_id => $field_settings ) {
			if ( empty( $field_settings['enabled'] ) ) {
				continue;
			}
			$key = 'uem_regi_' . $field_id;
			if ( isset( $data[ $key ] ) ) {
				$label = ! empty( $field_settings['label'] ) ? $field_settings['label'] : ucfirst( str_replace( '_', ' ', $field_id ) );
				if ( 'email_address' === $field_id ) {
					$predefined_values[ $field_id ] = array(
						'label' => $label,
						'value' => sanitize_email( $data[ $key ] ),
					);
				} elseif ( 'address' === $field_id ) {
					$predefined_values[ $field_id ] = array(
						'label' => $label,
						'value' => sanitize_textarea_field( $data[ $key ] ),
					);
				} elseif ( 'website' === $field_id ) {
					$predefined_values[ $field_id ] = array(
						'label' => $label,
						'value' => esc_url_raw( $data[ $key ] ),
					);
				} elseif ( 'vegetarian' === $field_id ) {
					$predefined_values[ $field_id ] = array(
						'label' => $label,
						'value' => ! empty( $data[ $key ] ) ? 'Yes' : 'No',
					);
				} else {
					$predefined_values[ $field_id ] = array(
						'label' => $label,
						'value' => sanitize_text_field( $data[ $key ] ),
					);
				}
			}
		}

		// Collect custom field values
		$custom_values = array();
		foreach ( $custom_fields as $cf_id => $cf ) {
			$key = 'uem_custom_' . $cf_id;
			if ( isset( $data[ $key ] ) ) {
				$cf_label = ! empty( $cf['label'] ) ? $cf['label'] : $cf_id;
				if ( is_array( $data[ $key ] ) ) {
					$custom_values[ $cf_id ] = array(
						'label' => $cf_label,
						'value' => array_map( 'sanitize_text_field', $data[ $key ] ),
					);
				} else {
					$custom_values[ $cf_id ] = array(
						'label' => $cf_label,
						'value' => sanitize_text_field( $data[ $key ] ),
					);
				}
			}
		}

		// Get ticket quantities and calculate totals
		$tickets = get_post_meta( $event_id, '_wtmem_tk_tickets', true );
		$regular_tickets = array();
		if ( is_array( $tickets ) && isset( $tickets['regular_tickets'] ) ) {
			$regular_tickets = $tickets['regular_tickets'];
		} elseif ( is_array( $tickets ) ) {
			$regular_tickets = $tickets;
		}
		
		$ticket_details   = array();
		$total_quantity   = 0;
		$subtotal_amount  = 0;

		foreach ( $regular_tickets as $index => $ticket ) {
			$quantity = isset( $data[ 'uem_ticket_quantity_' . $index ] ) ? intval( $data[ 'uem_ticket_quantity_' . $index ] ) : 0;
			if ( $quantity > 0 ) {
				$price       = floatval( $ticket['price'] ?? 0 );
				$ticket_name = $ticket['name'] ?? '';
				$line_total  = $price * $quantity;

				$ticket_details[] = array(
					'ticket_index' => $index,
					'name'         => $ticket_name,
					'price'        => $price,
					'quantity'     => $quantity,
					'line_total'   => $line_total,
				);

				$total_quantity  += $quantity;
				$subtotal_amount += $line_total;
			}
		}

		if ( empty( $ticket_details ) ) {
			self::$last_error = __( 'Please select at least one ticket.', 'momento-event-manager' );
			return false;
		}

		// Get attendee data
		$attendees = array();
		for ( $i = 1; $i <= $total_quantity; $i++ ) {
			$attendee_name  = isset( $data[ 'uem_attendee_name_' . $i ] ) ? sanitize_text_field( $data[ 'uem_attendee_name_' . $i ] ) : '';
			$attendee_phone = isset( $data[ 'uem_attendee_phone_' . $i ] ) ? sanitize_text_field( $data[ 'uem_attendee_phone_' . $i ] ) : '';
			$attendee_email = isset( $data[ 'uem_attendee_email_' . $i ] ) ? sanitize_email( $data[ 'uem_attendee_email_' . $i ] ) : '';
			
			if ( $attendee_name || $attendee_phone || $attendee_email ) {
				$attendees[] = array(
					'name'  => $attendee_name,
					'phone' => $attendee_phone,
					'email' => $attendee_email,
				);
			}
		}

		// Build the registration_field data array (all form input data stored as array)
		$registration_field_data = array(
			'predefined_fields' => $predefined_values,
			'custom_fields'     => $custom_values,
			'tickets'           => $ticket_details,
			'attendees'         => $attendees,
		);

		// Determine the single-ticket price for the `price` column
		$first_ticket_price = ! empty( $ticket_details ) ? $ticket_details[0]['price'] : 0;

		// Insert into custom table wtmem_registrations
		$table_name = $wpdb->prefix . 'wtmem_registrations';

		$inserted = $wpdb->insert(
			$table_name,
			array(
				'event_id'           => $event_id,
				'registration_field' => maybe_serialize( $registration_field_data ),
				'quantity'           => $total_quantity,
				'price'              => $first_ticket_price,
				'subtotal_amount'    => $subtotal_amount,
				'payment_status'     => 'pending',
				'order_status'       => 'confirmed',
				'created_at'         => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%d', '%f', '%f', '%s', '%s', '%s' )
		);

		if ( false === $inserted ) {
			self::$last_error = __( 'Database error. Please contact the administrator.', 'momento-event-manager' );
			error_log( 'WTMEM Registration DB Error: ' . $wpdb->last_error );
			return false;
		}

		$registration_id = $wpdb->insert_id;

		// Extract registrant email for the confirmation email
		$registrant_email = '';
		$registrant_name  = '';

		if ( ! empty( $predefined_values['email_address']['value'] ) ) {
			$registrant_email = $predefined_values['email_address']['value'];
		}
		if ( ! empty( $predefined_values['firstname']['value'] ) ) {
			$registrant_name = $predefined_values['firstname']['value'];
		}
		if ( ! empty( $predefined_values['lastname']['value'] ) ) {
			$registrant_name .= ( $registrant_name ? ' ' : '' ) . $predefined_values['lastname']['value'];
		}

		// Send professional confirmation email
		if ( ! empty( $registrant_email ) ) {
			self::wtmem_send_confirmation_email(
				$registrant_email,
				$registrant_name,
				$event_id,
				$registration_id,
				$registration_field_data,
				$total_quantity,
				$subtotal_amount
			);
		}

		return $registration_id;
	}

	/**
	 * Send professional registration confirmation email
	 *
	 * @param string $to_email          Recipient email
	 * @param string $registrant_name   Registrant name
	 * @param int    $event_id          Event post ID
	 * @param int    $registration_id   Registration row ID
	 * @param array  $registration_data All registration field data
	 * @param int    $total_quantity    Total ticket quantity
	 * @param float  $subtotal_amount   Total amount
	 */
	private static function wtmem_send_confirmation_email( $to_email, $registrant_name, $event_id, $registration_id, $registration_data, $total_quantity, $subtotal_amount ) {
		
		$event_title = get_the_title( $event_id );
		$site_name   = get_bloginfo( 'name' );
		$site_url    = home_url();

		// Get event date info (stored as indexed arrays for multi-day support)
		$event_dates = get_post_meta( $event_id, 'wtmem_event_dates', true );
		$event_start_date = '';
		$event_start_time = '';
		$event_end_date   = '';
		$event_end_time   = '';
		if ( is_array( $event_dates ) ) {
			$event_start_date = ! empty( $event_dates['start_date'][0] ) ? $event_dates['start_date'][0] : '';
			$event_start_time = ! empty( $event_dates['start_time'][0] ) ? $event_dates['start_time'][0] : '';
			$event_end_date   = ! empty( $event_dates['end_date'][0] )   ? $event_dates['end_date'][0]   : '';
			$event_end_time   = ! empty( $event_dates['end_time'][0] )   ? $event_dates['end_time'][0]   : '';
		}

		// Get event venue/location
		$event_venue   = get_post_meta( $event_id, '_wtmem_vl_venue_name', true );
		$event_address = get_post_meta( $event_id, '_wtmem_vl_address', true );

		// Get currency symbol
		$currency        = get_option( 'my_currency', 'USD' );
		$currency_symbol = self::wtmem_get_currency_symbol( $currency );

		// Format dates for display
		$formatted_start = '';
		if ( $event_start_date ) {
			$formatted_start = date_i18n( get_option( 'date_format' ), strtotime( $event_start_date ) );
			if ( $event_start_time ) {
				$formatted_start .= ' ' . esc_html( $event_start_time );
			}
		}
		$formatted_end = '';
		if ( $event_end_date ) {
			$formatted_end = date_i18n( get_option( 'date_format' ), strtotime( $event_end_date ) );
			if ( $event_end_time ) {
				$formatted_end .= ' ' . esc_html( $event_end_time );
			}
		}

		$display_name = ! empty( $registrant_name ) ? $registrant_name : __( 'Valued Attendee', 'momento-event-manager' );

		// Build ticket rows for email
		$ticket_rows_html = '';
		if ( ! empty( $registration_data['tickets'] ) ) {
			foreach ( $registration_data['tickets'] as $ticket ) {
				$ticket_rows_html .= '<tr>';
				$ticket_rows_html .= '<td style="padding: 12px 16px; border-bottom: 1px solid #e8e8e8; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444;">' . esc_html( $ticket['name'] ) . '</td>';
				$ticket_rows_html .= '<td style="padding: 12px 16px; border-bottom: 1px solid #e8e8e8; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444; text-align: center;">' . esc_html( $ticket['quantity'] ) . '</td>';
				$ticket_rows_html .= '<td style="padding: 12px 16px; border-bottom: 1px solid #e8e8e8; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444; text-align: right;">' . esc_html( $currency_symbol . number_format( $ticket['price'], 2 ) ) . '</td>';
				$ticket_rows_html .= '<td style="padding: 12px 16px; border-bottom: 1px solid #e8e8e8; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444; text-align: right; font-weight: 600;">' . esc_html( $currency_symbol . number_format( $ticket['line_total'], 2 ) ) . '</td>';
				$ticket_rows_html .= '</tr>';
			}
		}

		// Build registrant details rows
		$registrant_details_html = '';
		if ( ! empty( $registration_data['predefined_fields'] ) ) {
			foreach ( $registration_data['predefined_fields'] as $field_id => $field ) {
				$value = is_array( $field['value'] ) ? implode( ', ', $field['value'] ) : $field['value'];
				if ( ! empty( $value ) ) {
					$registrant_details_html .= '<tr>';
					$registrant_details_html .= '<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #777777; width: 40%;">' . esc_html( $field['label'] ) . '</td>';
					$registrant_details_html .= '<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #333333; font-weight: 500;">' . esc_html( $value ) . '</td>';
					$registrant_details_html .= '</tr>';
				}
			}
		}
		if ( ! empty( $registration_data['custom_fields'] ) ) {
			foreach ( $registration_data['custom_fields'] as $cf_id => $cf ) {
				$value = is_array( $cf['value'] ) ? implode( ', ', $cf['value'] ) : $cf['value'];
				if ( ! empty( $value ) ) {
					$registrant_details_html .= '<tr>';
					$registrant_details_html .= '<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #777777; width: 40%;">' . esc_html( $cf['label'] ) . '</td>';
					$registrant_details_html .= '<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #333333; font-weight: 500;">' . esc_html( $value ) . '</td>';
					$registrant_details_html .= '</tr>';
				}
			}
		}

		// Build attendee list
		$attendee_html = '';
		if ( ! empty( $registration_data['attendees'] ) ) {
			$attendee_html .= '<tr><td style="padding: 0 40px 24px 40px;">';
			$attendee_html .= '<h3 style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 16px; color: #333333; margin: 0 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #4F46E5;">&#x1F465; ' . esc_html__( 'Attendee Information', 'momento-event-manager' ) . '</h3>';
			$attendee_html .= '<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden;">';
			$attendee_html .= '<thead><tr style="background-color: #f0f0f5;">';
			$attendee_html .= '<th style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: left; font-weight: 600;">#</th>';
			$attendee_html .= '<th style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: left; font-weight: 600;">' . esc_html__( 'Name', 'momento-event-manager' ) . '</th>';
			$attendee_html .= '<th style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: left; font-weight: 600;">' . esc_html__( 'Phone', 'momento-event-manager' ) . '</th>';
			$attendee_html .= '<th style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: left; font-weight: 600;">' . esc_html__( 'Email', 'momento-event-manager' ) . '</th>';
			$attendee_html .= '</tr></thead><tbody>';

			foreach ( $registration_data['attendees'] as $idx => $attendee ) {
				$bg = ( $idx % 2 === 0 ) ? '#ffffff' : '#fafafa';
				$attendee_html .= '<tr style="background-color: ' . $bg . ';">';
				$attendee_html .= '<td style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444;">' . ( $idx + 1 ) . '</td>';
				$attendee_html .= '<td style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444;">' . esc_html( $attendee['name'] ) . '</td>';
				$attendee_html .= '<td style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444;">' . esc_html( $attendee['phone'] ) . '</td>';
				$attendee_html .= '<td style="padding: 10px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #444444;">' . esc_html( $attendee['email'] ) . '</td>';
				$attendee_html .= '</tr>';
			}

			$attendee_html .= '</tbody></table></td></tr>';
		}

		// Event location section
		$location_html = '';
		if ( ! empty( $event_venue ) || ! empty( $event_address ) ) {
			$location_html = '<tr>';
			$location_html .= '<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #777777; width: 40%;">&#x1F4CD; ' . esc_html__( 'Location', 'momento-event-manager' ) . '</td>';
			$location_html .= '<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #333333; font-weight: 500;">';
			if ( ! empty( $event_venue ) ) {
				$location_html .= esc_html( $event_venue );
			}
			if ( ! empty( $event_address ) ) {
				$location_html .= ( ! empty( $event_venue ) ? '<br>' : '' ) . esc_html( $event_address );
			}
			$location_html .= '</td></tr>';
		}

		$current_year = date( 'Y' );
		$event_url    = get_permalink( $event_id );

		// Email subject
		$subject = sprintf(
			__( 'Registration Confirmed — %1$s (#%2$s)', 'momento-event-manager' ),
			$event_title,
			$registration_id
		);

		// Build the professional HTML email
		$message = '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>' . esc_html( $subject ) . '</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f7; -webkit-font-smoothing: antialiased;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f7;">
	<tr>
		<td align="center" style="padding: 40px 20px;">
			<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">

				<!-- Header -->
				<tr>
					<td style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); padding: 40px 40px 32px 40px; text-align: center;">
						<div style="margin-bottom: 16px;">
							<span style="display: inline-block; width: 64px; height: 64px; background-color: rgba(255,255,255,0.2); border-radius: 50%; line-height: 64px; font-size: 32px;">&#x2705;</span>
						</div>
						<h1 style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 26px; font-weight: 700; color: #ffffff; margin: 0 0 8px 0;">' . esc_html__( 'Registration Confirmed!', 'momento-event-manager' ) . '</h1>
						<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 15px; color: rgba(255,255,255,0.85); margin: 0;">' . esc_html__( 'Your spot has been successfully reserved', 'momento-event-manager' ) . '</p>
					</td>
				</tr>

				<!-- Greeting -->
				<tr>
					<td style="padding: 32px 40px 0 40px;">
						<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 16px; color: #333333; margin: 0 0 8px 0;">' . sprintf( esc_html__( 'Hi %s,', 'momento-event-manager' ), esc_html( $display_name ) ) . '</p>
						<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 15px; color: #555555; line-height: 1.6; margin: 0 0 24px 0;">' . sprintf(
							esc_html__( 'Thank you for registering for %s! We are excited to have you join us. Below are your complete registration details.', 'momento-event-manager' ),
							'<strong style="color: #4F46E5;">' . esc_html( $event_title ) . '</strong>'
						) . '</p>
					</td>
				</tr>

				<!-- Registration ID Badge -->
				<tr>
					<td style="padding: 0 40px 24px 40px;">
						<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f0f0ff; border-radius: 8px; border-left: 4px solid #4F46E5;">
							<tr>
								<td style="padding: 16px 20px;">
									<table width="100%" cellpadding="0" cellspacing="0">
										<tr>
											<td style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #777777; text-transform: uppercase; letter-spacing: 1px;">' . esc_html__( 'Registration ID', 'momento-event-manager' ) . '</td>
											<td style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #777777; text-transform: uppercase; letter-spacing: 1px; text-align: right;">' . esc_html__( 'Status', 'momento-event-manager' ) . '</td>
										</tr>
										<tr>
											<td style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 22px; color: #4F46E5; font-weight: 700;">#' . esc_html( $registration_id ) . '</td>
											<td style="text-align: right;">
												<span style="display: inline-block; background-color: #10B981; color: #ffffff; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px; text-transform: uppercase;">' . esc_html__( 'Confirmed', 'momento-event-manager' ) . '</span>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- Event Details -->
				<tr>
					<td style="padding: 0 40px 24px 40px;">
						<h3 style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 16px; color: #333333; margin: 0 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #4F46E5;">&#x1F3AA; ' . esc_html__( 'Event Details', 'momento-event-manager' ) . '</h3>
						<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
							<tr>
								<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #777777; width: 40%;">&#x1F3AF; ' . esc_html__( 'Event', 'momento-event-manager' ) . '</td>
								<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #333333; font-weight: 600;">' . esc_html( $event_title ) . '</td>
							</tr>'
							. ( $formatted_start ? '<tr>
								<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #777777; width: 40%;">&#x1F4C5; ' . esc_html__( 'Start', 'momento-event-manager' ) . '</td>
								<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #333333; font-weight: 500;">' . esc_html( $formatted_start ) . '</td>
							</tr>' : '' )
							. ( $formatted_end ? '<tr>
								<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #777777; width: 40%;">&#x1F4C5; ' . esc_html__( 'End', 'momento-event-manager' ) . '</td>
								<td style="padding: 8px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #333333; font-weight: 500;">' . esc_html( $formatted_end ) . '</td>
							</tr>' : '' )
							. $location_html . '
						</table>
					</td>
				</tr>

				<!-- Registrant Details -->
				' . ( ! empty( $registrant_details_html ) ? '<tr>
					<td style="padding: 0 40px 24px 40px;">
						<h3 style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 16px; color: #333333; margin: 0 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #4F46E5;">&#x1F464; ' . esc_html__( 'Your Details', 'momento-event-manager' ) . '</h3>
						<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse;">
							' . $registrant_details_html . '
						</table>
					</td>
				</tr>' : '' ) . '

				<!-- Ticket Summary -->
				<tr>
					<td style="padding: 0 40px 24px 40px;">
						<h3 style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 16px; color: #333333; margin: 0 0 12px 0; padding-bottom: 8px; border-bottom: 2px solid #4F46E5;">&#x1F3AB; ' . esc_html__( 'Ticket Summary', 'momento-event-manager' ) . '</h3>
						<table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; border: 1px solid #e8e8e8; border-radius: 8px; overflow: hidden;">
							<thead>
								<tr style="background-color: #f8f8fc;">
									<th style="padding: 12px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: left; font-weight: 600; border-bottom: 2px solid #e8e8e8;">' . esc_html__( 'Ticket', 'momento-event-manager' ) . '</th>
									<th style="padding: 12px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: center; font-weight: 600; border-bottom: 2px solid #e8e8e8;">' . esc_html__( 'Qty', 'momento-event-manager' ) . '</th>
									<th style="padding: 12px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: right; font-weight: 600; border-bottom: 2px solid #e8e8e8;">' . esc_html__( 'Price', 'momento-event-manager' ) . '</th>
									<th style="padding: 12px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #555555; text-align: right; font-weight: 600; border-bottom: 2px solid #e8e8e8;">' . esc_html__( 'Total', 'momento-event-manager' ) . '</th>
								</tr>
							</thead>
							<tbody>
								' . $ticket_rows_html . '
							</tbody>
							<tfoot>
								<tr style="background-color: #f8f8fc;">
									<td colspan="3" style="padding: 14px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 15px; color: #333333; font-weight: 700; text-align: right; border-top: 2px solid #4F46E5;">' . esc_html__( 'Total Amount', 'momento-event-manager' ) . '</td>
									<td style="padding: 14px 16px; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 18px; color: #4F46E5; font-weight: 700; text-align: right; border-top: 2px solid #4F46E5;">' . esc_html( $currency_symbol . number_format( $subtotal_amount, 2 ) ) . '</td>
								</tr>
							</tfoot>
						</table>
					</td>
				</tr>

				<!-- Attendees -->
				' . $attendee_html . '

				<!-- CTA Button -->
				<tr>
					<td style="padding: 8px 40px 32px 40px; text-align: center;">
						<a href="' . esc_url( $event_url ) . '" style="display: inline-block; background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%); color: #ffffff; font-family: \'Segoe UI\', Arial, sans-serif; font-size: 15px; font-weight: 600; text-decoration: none; padding: 14px 36px; border-radius: 8px;">' . esc_html__( 'View Event Details', 'momento-event-manager' ) . ' &#x2192;</a>
					</td>
				</tr>

				<!-- Important Note -->
				<tr>
					<td style="padding: 0 40px 32px 40px;">
						<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #FFF8E1; border-radius: 8px; border-left: 4px solid #F59E0B;">
							<tr>
								<td style="padding: 16px 20px;">
									<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 13px; color: #92400E; margin: 0; line-height: 1.5;">
										<strong>&#x1F4A1; ' . esc_html__( 'Important:', 'momento-event-manager' ) . '</strong> ' . esc_html__( 'Please save this email for your records. You may need to present your Registration ID at the event venue. If you have any questions, please do not hesitate to contact us.', 'momento-event-manager' ) . '
									</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<!-- Footer -->
				<tr>
					<td style="background-color: #f8f8fc; padding: 24px 40px; border-top: 1px solid #e8e8e8;">
						<table width="100%" cellpadding="0" cellspacing="0">
							<tr>
								<td style="text-align: center;">
									<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 14px; color: #555555; margin: 0 0 4px 0; font-weight: 600;">' . esc_html( $site_name ) . '</p>
									<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 12px; color: #999999; margin: 0 0 12px 0;">' . esc_html( $site_url ) . '</p>
									<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 11px; color: #bbbbbb; margin: 0;">' . sprintf( esc_html__( '&copy; %1$s %2$s. All rights reserved.', 'momento-event-manager' ), esc_html( $current_year ), esc_html( $site_name ) ) . '</p>
									<p style="font-family: \'Segoe UI\', Arial, sans-serif; font-size: 11px; color: #bbbbbb; margin: 8px 0 0 0;">' . esc_html__( 'This is an automated confirmation email. Please do not reply directly to this email.', 'momento-event-manager' ) . '</p>
								</td>
							</tr>
						</table>
					</td>
				</tr>

			</table>
		</td>
	</tr>
</table>

</body>
</html>';

		// Set email headers for HTML
		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . $site_name . ' <' . get_option( 'admin_email' ) . '>',
		);

		// Send confirmation email to registrant
		wp_mail( $to_email, $subject, $message, $headers );

		// Send notification copy to admin
		$admin_email = get_option( 'admin_email' );
		$admin_subject = sprintf(
			__( 'New Registration — %1$s by %2$s (#%3$s)', 'momento-event-manager' ),
			$event_title,
			$display_name,
			$registration_id
		);
		wp_mail( $admin_email, $admin_subject, $message, $headers );
	}

	/**
	 * Get currency symbol from currency code
	 *
	 * @param string $currency_code
	 * @return string
	 */
	private static function wtmem_get_currency_symbol( $currency_code ) {
		$symbols = array(
			'USD' => '$',    'EUR' => '€',    'GBP' => '£',    'JPY' => '¥',
			'CNY' => '¥',   'INR' => '₹',    'AUD' => 'A$',   'CAD' => 'C$',
			'CHF' => 'CHF',  'BDT' => '৳',   'BRL' => 'R$',   'KRW' => '₩',
			'RUB' => '₽',   'TRY' => '₺',    'ZAR' => 'R',    'MYR' => 'RM',
			'SGD' => 'S$',  'HKD' => 'HK$',  'NOK' => 'kr',   'SEK' => 'kr',
			'DKK' => 'kr',  'PLN' => 'zł',   'THB' => '฿',    'IDR' => 'Rp',
			'HUF' => 'Ft',  'CZK' => 'Kč',   'ILS' => '₪',   'PHP' => '₱',
			'AED' => 'د.إ', 'SAR' => '﷼',    'TWD' => 'NT$',  'PKR' => '₨',
			'EGP' => 'E£',  'NGN' => '₦',    'VND' => '₫',    'ARS' => '$',
			'CLP' => '$',   'COP' => '$',     'MXN' => '$',    'PEN' => 'S/',
		);

		return isset( $symbols[ $currency_code ] ) ? $symbols[ $currency_code ] : $currency_code . ' ';
	}

	/**
	 * Ensure the registrations table exists with correct schema
	 */
	private static function wtmem_ensure_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'wtmem_registrations';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			event_id BIGINT UNSIGNED,
			registration_field LONGTEXT,
			quantity INT,
			price DECIMAL(10,2),
			subtotal_amount DECIMAL(10,2),
			payment_status VARCHAR(50) DEFAULT 'pending',
			order_status VARCHAR(50) DEFAULT 'pending',
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

