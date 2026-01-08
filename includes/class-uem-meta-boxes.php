<?php
/**
 * Meta Boxes
 * @package Ultimate_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UEM_Meta_Boxes {
	
	/**
	 * Initialize meta boxes
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'webcu_add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'webcu_save_meta_boxes' ) );
	}
	/**
	 * Add meta boxes
	*/
	public static function webcu_add_meta_boxes() {
		// Event meta boxes
		add_meta_box(
			'uem_event_organizers',
			__( 'Organizers', 'ultimate-events-manager' ),
			array( __CLASS__, 'webcu_render_organizers_meta_box' ),
			'mem_event',
			'side',
			'default'
		);
		
		add_meta_box(
			'uem_event_volunteers',
			__( 'Volunteers', 'ultimate-events-manager' ),
			array( __CLASS__, 'webcu_render_volunteers_meta_box' ),
			'mem_event',
			'side',
			'default'
		);
		
		add_meta_box(
			'uem_event_sponsors',
			__( 'Sponsors', 'ultimate-events-manager' ),
			array( __CLASS__, 'webcu_render_sponsors_meta_box' ),
			'mem_event',
			'side',
			'default'
		);
		
		add_meta_box(
			'uem_event_tickets',
			__( 'Ticket Types', 'ultimate-events-manager' ),
			array( __CLASS__, 'webcu_render_tickets_meta_box' ),
			'mem_event',
			'normal',
			'default'
		);
		
		add_meta_box(
			'uem_event_details',
			__( 'Event Details', 'ultimate-events-manager' ),
			array( __CLASS__, 'webcu_render_event_details_meta_box' ),
			'mem_event',
			'normal',
			'high'
		);
	}
	
	/**
	 * Render organizers meta box
	 */
	public static function webcu_render_organizers_meta_box( $post ) {
		wp_nonce_field( 'uem_save_meta_boxes', 'uem_meta_boxes_nonce' );
		
		$organizers = get_posts( array(
			'post_type' => 'uem_organizer',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );
		
		$selected_organizers = get_post_meta( $post->ID, '_uem_organizers', true );
		if ( ! is_array( $selected_organizers ) ) {
			$selected_organizers = array();
		}
		
		?>
		<div class="uem-meta-box">
			<p>
				<label for="uem_organizers">
					<?php echo esc_html( 'Select Organizers:', 'ultimate-events-manager' ); ?>
				</label>
			</p>
			<select name="uem_organizers[]" id="uem_organizers" multiple="multiple" style="width: 100%; height: 150px;">
				<?php foreach ( $organizers as $organizer ) : ?>
					<option value="<?php echo esc_attr( $organizer->ID ); ?>" <?php selected( in_array( $organizer->ID, $selected_organizers ) ); ?>>
						<?php echo esc_html( $organizer->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description">
				<?php echo esc_html( 'Hold Ctrl/Cmd to select multiple organizers.', 'ultimate-events-manager' ); ?>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Render volunteers meta box
	 */
	public static function webcu_render_volunteers_meta_box( $post ) {
		$volunteers = get_posts( array(
			'post_type' => 'uem_volunteer',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );
		
		$selected_volunteers = get_post_meta( $post->ID, '_uem_volunteers', true );
		if ( ! is_array( $selected_volunteers ) ) {
			$selected_volunteers = array();
		}
		
		?>
		<div class="uem-meta-box">
			<p>
				<label for="uem_volunteers">
					<?php echo esc_html( 'Select Volunteers:', 'ultimate-events-manager' ); ?>
				</label>
			</p>
			<select name="uem_volunteers[]" id="uem_volunteers" multiple="multiple" style="width: 100%; height: 150px;">
				<?php foreach ( $volunteers as $volunteer ) : ?>
					<option value="<?php echo esc_attr( $volunteer->ID ); ?>" <?php selected( in_array( $volunteer->ID, $selected_volunteers ) ); ?>>
						<?php echo esc_html( $volunteer->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description">
				<?php echo esc_html( 'Hold Ctrl/Cmd to select multiple volunteers.', 'ultimate-events-manager' ); ?>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Render sponsors meta box
	 */
	public static function webcu_render_sponsors_meta_box( $post ) {
		$sponsors = get_posts( array(
			'post_type' => 'uem_sponsor',
			'posts_per_page' => -1,
			'post_status' => 'publish',
		) );
		
		$selected_sponsors = get_post_meta( $post->ID, '_uem_sponsors', true );
		if ( ! is_array( $selected_sponsors ) ) {
			$selected_sponsors = array();
		}
		
		?>
		<div class="uem-meta-box">
			<p>
				<label for="uem_sponsors">
					<?php echo esc_html( 'Select Sponsors:', 'ultimate-events-manager' ); ?>
				</label>
			</p>
			<select name="uem_sponsors[]" id="uem_sponsors" multiple="multiple" style="width: 100%; height: 150px;">
				<?php foreach ( $sponsors as $sponsor ) : ?>
					<option value="<?php echo esc_attr( $sponsor->ID ); ?>" <?php selected( in_array( $sponsor->ID, $selected_sponsors ) ); ?>>
						<?php echo esc_html( $sponsor->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
			<p class="description">
				<?php echo esc_html( 'Hold Ctrl/Cmd to select multiple sponsors.', 'ultimate-events-manager' ); ?>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Render tickets meta box
	 */
	public static function webcu_render_tickets_meta_box( $post ) {
		$tickets = get_post_meta( $post->ID, '_uem_tickets', true );
		if ( ! is_array( $tickets ) ) {
			$tickets = array();
		}
		
		?>
		<div class="uem-meta-box">
			<div id="uem-tickets-container">
				<?php if ( ! empty( $tickets ) ) : ?>
					<?php foreach ( $tickets as $index => $ticket ) : ?>
						<div class="uem-ticket-item" data-index="<?php echo esc_attr( $index ); ?>">
							<p>
								<label><?php echo esc_html( 'Ticket Name:', 'ultimate-events-manager' ); ?></label>
								<input type="text" name="uem_tickets[<?php echo esc_attr( $index ); ?>][name]" value="<?php echo esc_attr( $ticket['name'] ); ?>" class="widefat" />
							</p>
							<p>
								<label><?php echo esc_html( 'Price:', 'ultimate-events-manager' ); ?></label>
								<input type="number" name="uem_tickets[<?php echo esc_attr( $index ); ?>][price]" value="<?php echo esc_attr( $ticket['price'] ); ?>" step="0.01" min="0" class="widefat" />
							</p>
							<p>
								<label><?php echo esc_html( 'Quantity Available:', 'ultimate-events-manager' ); ?></label>
								<input type="number" name="uem_tickets[<?php echo esc_attr( $index ); ?>][quantity]" value="<?php echo esc_attr( $ticket['quantity'] ); ?>" min="0" class="widefat" />
							</p>
							<p>
								<label><?php echo esc_html( 'Description:', 'ultimate-events-manager' ); ?></label>
								<textarea name="uem_tickets[<?php echo esc_attr( $index ); ?>][description]" class="widefat" rows="3"><?php echo esc_textarea( $ticket['description'] ); ?></textarea>
							</p>
							<button type="button" class="button uem-remove-ticket"><?php echo esc_html( 'Remove Ticket', 'ultimate-events-manager' ); ?></button>
							<hr>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<button type="button" class="button uem-add-ticket"><?php echo esc_html( 'Add Ticket Type', 'ultimate-events-manager' ); ?></button>
		</div>
		<?php
	}
	
	/**
	 * Render event details meta box
	 */
	public static function webcu_render_event_details_meta_box( $post ) {
		$event_date = get_post_meta( $post->ID, '_uem_event_date', true );
		$event_time = get_post_meta( $post->ID, '_uem_event_time', true );
		$event_end_date = get_post_meta( $post->ID, '_uem_event_end_date', true );
		$event_end_time = get_post_meta( $post->ID, '_uem_event_end_time', true );
		$event_location = get_post_meta( $post->ID, '_uem_event_location', true );
		$event_address = get_post_meta( $post->ID, '_uem_event_address', true );
		
		?>
		<div class="uem-meta-box">
			<p>
				<label for="uem_event_date"><?php echo esc_html( 'Event Start Date:', 'ultimate-events-manager' ); ?></label>
				<input type="date" name="uem_event_date" id="uem_event_date" value="<?php echo esc_attr( $event_date ); ?>" class="widefat" />
			</p>
			<p>
				<label for="uem_event_time"><?php echo esc_html( 'Event Start Time:', 'ultimate-events-manager' ); ?></label>
				<input type="time" name="uem_event_time" id="uem_event_time" value="<?php echo esc_attr( $event_time ); ?>" class="widefat" />
			</p>
			<p>
				<label for="uem_event_end_date"><?php echo esc_html( 'Event End Date:', 'ultimate-events-manager' ); ?></label>
				<input type="date" name="uem_event_end_date" id="uem_event_end_date" value="<?php echo esc_attr( $event_end_date ); ?>" class="widefat" />
			</p>
			<p>
				<label for="uem_event_end_time"><?php echo esc_html( 'Event End Time:', 'ultimate-events-manager' ); ?></label>
				<input type="time" name="uem_event_end_time" id="uem_event_end_time" value="<?php echo esc_attr( $event_end_time ); ?>" class="widefat" />
			</p>
			<p>
				<label for="uem_event_location"><?php echo esc_html( 'Event Location:', 'ultimate-events-manager' ); ?></label>
				<input type="text" name="uem_event_location" id="uem_event_location" value="<?php echo esc_attr( $event_location ); ?>" class="widefat" />
			</p>
			<p>
				<label for="uem_event_address"><?php echo esc_html( 'Event Address:', 'ultimate-events-manager' ); ?></label>
				<textarea name="uem_event_address" id="uem_event_address" class="widefat" rows="3"><?php echo esc_textarea( $event_address ); ?></textarea>
			</p>
		</div>
		<?php
	}
	
	/**
	 * Save meta boxes
	 */
	public static function webcu_save_meta_boxes( $post_id ) {
		// Check nonce
		if ( ! isset( $_POST['uem_meta_boxes_nonce'] ) || ! wp_verify_nonce( $_POST['uem_meta_boxes_nonce'], 'uem_save_meta_boxes' ) ) {
			return;
		}
		
		// Check autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		
		// Check permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		// Save organizers
		if ( isset( $_POST['uem_organizers'] ) ) {
			$organizers = array_map( 'intval', $_POST['uem_organizers'] );
			update_post_meta( $post_id, '_uem_organizers', $organizers );
		} else {
			update_post_meta( $post_id, '_uem_organizers', array() );
		}
		
		// Save volunteers
		if ( isset( $_POST['uem_volunteers'] ) ) {
			$volunteers = array_map( 'intval', $_POST['uem_volunteers'] );
			update_post_meta( $post_id, '_uem_volunteers', $volunteers );
		} else {
			update_post_meta( $post_id, '_uem_volunteers', array() );
		}
		
		// Save sponsors
		if ( isset( $_POST['uem_sponsors'] ) ) {
			$sponsors = array_map( 'intval', $_POST['uem_sponsors'] );
			update_post_meta( $post_id, '_uem_sponsors', $sponsors );
		} else {
			update_post_meta( $post_id, '_uem_sponsors', array() );
		}
		
		// Save tickets
		if ( isset( $_POST['uem_tickets'] ) ) {
			$tickets = array();
			foreach ( $_POST['uem_tickets'] as $ticket ) {
				$tickets[] = array(
					'name' => sanitize_text_field( $ticket['name'] ),
					'price' => floatval( $ticket['price'] ),
					'quantity' => intval( $ticket['quantity'] ),
					'description' => sanitize_textarea_field( $ticket['description'] ),
				);
			}
			update_post_meta( $post_id, '_uem_tickets', $tickets );
			//update_post_meta( $post_id, '_webcu_tk_tickets', $tickets );
		} else {
			update_post_meta( $post_id, '_uem_tickets', array() );
			//update_post_meta( $post_id, '_webcu_tk_tickets', array() );
		}
		
		// Save event details
		if ( isset( $_POST['uem_event_date'] ) ) {
			update_post_meta( $post_id, '_uem_event_date', sanitize_text_field( $_POST['uem_event_date'] ) );
		}
		if ( isset( $_POST['uem_event_time'] ) ) {
			update_post_meta( $post_id, '_uem_event_time', sanitize_text_field( $_POST['uem_event_time'] ) );
		}
		if ( isset( $_POST['uem_event_end_date'] ) ) {
			update_post_meta( $post_id, '_uem_event_end_date', sanitize_text_field( $_POST['uem_event_end_date'] ) );
		}
		if ( isset( $_POST['uem_event_end_time'] ) ) {
			update_post_meta( $post_id, '_uem_event_end_time', sanitize_text_field( $_POST['uem_event_end_time'] ) );
		}
		if ( isset( $_POST['uem_event_location'] ) ) {
			update_post_meta( $post_id, '_uem_event_location', sanitize_text_field( $_POST['uem_event_location'] ) );
		}
		if ( isset( $_POST['uem_event_address'] ) ) {
			update_post_meta( $post_id, '_uem_event_address', sanitize_textarea_field( $_POST['uem_event_address'] ) );
		}
	}
}

