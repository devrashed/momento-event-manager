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
	 * Initialize registration
	 */
	public static function init() {
		// Handle form submission
		add_action( 'wp', array( __CLASS__, 'wtmem_handle_registration_submission' ) );		
		// AJAX handler for registration
		add_action( 'wp_ajax_uem_submit_registration', array( __CLASS__, 'wtmem_ajax_submit_registration' ) );
		add_action( 'wp_ajax_nopriv_uem_submit_registration', array( __CLASS__, 'wtmem_ajax_submit_registration' ) );
	}
	
	/**
	 * Handle registration submission (non-AJAX fallback)
	 */
	public static function wtmem_handle_registration_submission() {
		if ( ! isset( $_POST['uem_registration_submit'] ) ) {
			return;
		}
		
		if ( ! isset( $_POST['uem_registration_nonce'] ) || ! wp_verify_nonce( $_POST['uem_registration_nonce'], 'uem_registration_nonce' ) ) {
			return;
		}
		
		$event_id = isset( $_POST['uem_event_id'] ) ? intval( $_POST['uem_event_id'] ) : 0;
		if ( ! $event_id ) {
			return;
		}
		
		$registration_id = self::create_registration( $event_id, $_POST );
		
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
		
		$event_id = isset( $_POST['event_id'] ) ? intval( $_POST['event_id'] ) : 0;
		if ( ! $event_id ) {
			wp_send_json_error( array( 'message' => __( 'Invalid event ID.', 'ultimate-events-manager' ) ) );
		}
		
		$registration_id = self::create_registration( $event_id, $_POST );
		
		if ( $registration_id ) {
			wp_send_json_success( array(
				'registration_id' => $registration_id,
				'redirect_url' => add_query_arg( array(
					'uem_thank_you' => '1',
					'registration_id' => $registration_id,
				), home_url() ),
			) );
		} else {
			wp_send_json_error( array( 'message' => __( 'Registration failed. Please try again.', 'ultimate-events-manager' ) ) );
		}
	}
	
	/**
	 * Create registration
	 */
	private static function wtmem_create_registration( $event_id, $data ) {
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
				if ( 'email_address' === $field_id ) {
					$predefined_values[ $field_id ] = sanitize_email( $data[ $key ] );
				} elseif ( 'address' === $field_id ) {
					$predefined_values[ $field_id ] = sanitize_textarea_field( $data[ $key ] );
				} elseif ( 'website' === $field_id ) {
					$predefined_values[ $field_id ] = esc_url_raw( $data[ $key ] );
				} else {
					$predefined_values[ $field_id ] = sanitize_text_field( $data[ $key ] );
				}
			}
		}

		// Collect custom field values
		$custom_values = array();
		foreach ( $custom_fields as $cf_id => $cf ) {
			$key = 'uem_custom_' . $cf_id;
			if ( isset( $data[ $key ] ) ) {
				if ( is_array( $data[ $key ] ) ) {
					$custom_values[ $cf_id ] = array_map( 'sanitize_text_field', $data[ $key ] );
				} else {
					$custom_values[ $cf_id ] = sanitize_text_field( $data[ $key ] );
				}
			}
		}

		// Backward compatible: map common fields
		$name  = ! empty( $predefined_values['firstname'] ) ? $predefined_values['firstname'] : '';
		if ( ! empty( $predefined_values['lastname'] ) ) {
			$name .= ( $name ? ' ' : '' ) . $predefined_values['lastname'];
		}
		$phone   = isset( $predefined_values['phone_number'] ) ? $predefined_values['phone_number'] : '';
		$email   = isset( $predefined_values['email_address'] ) ? $predefined_values['email_address'] : '';
		$address = isset( $predefined_values['address'] ) ? $predefined_values['address'] : '';
		
		// Get ticket quantities
		$tickets = get_post_meta( $event_id, '_wtmem_tk_tickets', true );
		if ( ! is_array( $tickets ) ) {
			$tickets = array();
		}
		
		$ticket_quantities = array();
		$total_attendees = 0;
		
		foreach ( $tickets as $index => $ticket ) {
			$quantity = isset( $data['uem_ticket_quantity_' . $index] ) ? intval( $data['uem_ticket_quantity_' . $index] ) : 0;
			if ( $quantity > 0 ) {
				$ticket_quantities[ $index ] = $quantity;
				$total_attendees += $quantity;
			}
		}
		
		if ( empty( $ticket_quantities ) ) {
			return false;
		}
		
		// Get attendee data
		$attendees = array();
		for ( $i = 1; $i <= $total_attendees; $i++ ) {
			$attendee_name = isset( $data['uem_attendee_name_' . $i] ) ? sanitize_text_field( $data['uem_attendee_name_' . $i] ) : '';
			$attendee_phone = isset( $data['uem_attendee_phone_' . $i] ) ? sanitize_text_field( $data['uem_attendee_phone_' . $i] ) : '';
			$attendee_email = isset( $data['uem_attendee_email_' . $i] ) ? sanitize_email( $data['uem_attendee_email_' . $i] ) : '';
			
			if ( $attendee_name || $attendee_phone || $attendee_email ) {
				$attendees[] = array(
					'name' => $attendee_name,
					'phone' => $attendee_phone,
					'email' => $attendee_email,
				);
			}
		}
		
		// Create registration post
		$registration_data = array(
			'post_title' => sprintf( __( 'Registration for %s', 'ultimate-events-manager' ), get_the_title( $event_id ) ),
			'post_type' => 'uem_registration',
			'post_status' => 'publish',
		);
		
		$registration_id = wp_insert_post( $registration_data );
		
		if ( is_wp_error( $registration_id ) ) {
			return false;
		}
		
		// Save registration meta
		update_post_meta( $registration_id, '_uem_event_id', $event_id );
		update_post_meta( $registration_id, '_uem_registration_name', $name );
		update_post_meta( $registration_id, '_uem_registration_phone', $phone );
		update_post_meta( $registration_id, '_uem_registration_email', $email );
		update_post_meta( $registration_id, '_uem_registration_address', $address );
		update_post_meta( $registration_id, '_uem_ticket_quantities', $ticket_quantities );
		update_post_meta( $registration_id, '_uem_attendees', $attendees );
		update_post_meta( $registration_id, '_uem_registration_date', current_time( 'mysql' ) );
		update_post_meta( $registration_id, '_uem_predefined_fields', $predefined_values );
		update_post_meta( $registration_id, '_uem_custom_fields', $custom_values );
		
		return $registration_id;
	}
}

