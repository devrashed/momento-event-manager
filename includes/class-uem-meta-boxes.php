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
			__( 'Organizers', 'mega-events-manager' ),
			array( __CLASS__, 'webcu_render_organizers_meta_box' ),
			'mem_event',
			'side',
			'default'
		);
		
		add_meta_box(
			'uem_event_volunteers',
			__( 'Volunteers', 'mega-events-manager' ),
			array( __CLASS__, 'webcu_render_volunteers_meta_box' ),
			'mem_event',
			'side',
			'default'
		);
		
		add_meta_box(
			'uem_event_sponsors',
			__( 'Sponsors', 'mega-events-manager' ),
			array( __CLASS__, 'webcu_render_sponsors_meta_box' ),
			'mem_event',
			'side',
			'default'
		);
	}
	
	/**
	 * Render organizers meta box
	 */

	public static function webcu_render_organizers_meta_box( $post ) {

		wp_nonce_field( 'uem_save_meta_boxes', 'uem_meta_boxes_nonce' );

		$organizers = get_posts( array(
			'post_type'      => 'mem_organizer',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'DESC',
		) );

		$selected_organizers = get_post_meta( $post->ID, '_uem_organizers', true );
		if ( ! is_array( $selected_organizers ) ) {
			$selected_organizers = array();
		}
		?>

		<div class="uem-meta-box">
			<p><strong><?php esc_html_e( 'Select Organizers', 'mega-events-manager' ); ?></strong></p>

			<?php if ( $organizers ) : ?>
				<?php foreach ( $organizers as $organizer ) : ?>
					<label style="display:block; margin-bottom:6px;">
						<input type="checkbox"
							name="uem_organizers[]"
							value="<?php echo esc_attr( $organizer->ID ); ?>"
							<?php checked( in_array( $organizer->ID, $selected_organizers, true ) ); ?>
						/>
						<?php echo esc_html( $organizer->post_title ); ?>
					</label>
				<?php endforeach; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No organizers found.', 'mega-events-manager' ); ?></p>
			<?php endif; ?>
		</div>

		<?php
	}

	/**
	 * Render volunteers meta box
	 */

	public static function webcu_render_volunteers_meta_box( $post ) {

		wp_nonce_field( 'uem_save_meta_boxes', 'uem_meta_boxes_nonce' );

		$volunteers = get_posts( array(
			'post_type'      => 'mem_volunteer',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'DESC',
		) );

		$selected_volunteers = get_post_meta( $post->ID, '_uem_volunteers', true );
		if ( ! is_array( $selected_volunteers ) ) {
			$selected_volunteers = array();
		}
		?>

		<div class="uem-meta-box">
			<p><strong><?php esc_html_e( 'Select Volunteers', 'mega-events-manager' ); ?></strong></p>

			<?php if ( $volunteers ) : ?>
				<?php foreach ( $volunteers as $volunteer ) : ?>
					<label style="display:block; margin-bottom:6px;">
						<input type="checkbox"
							name="uem_volunteers[]"
							value="<?php echo esc_attr( $volunteer->ID ); ?>"
							<?php checked( in_array( $volunteer->ID, $selected_volunteers, true ) ); ?>
						/>
						<?php echo esc_html( $volunteer->post_title ); ?>
					</label>
				<?php endforeach; ?>
			<?php else : ?>
				<p><?php esc_html_e( 'No volunteers found.', 'mega-events-manager' ); ?></p>
			<?php endif; ?>
		</div>

		<?php
	}


	/**
	 * Render sponsors meta box
	 */
	public static function webcu_render_sponsors_meta_box( $post ) {	
		wp_nonce_field( 'uem_save_meta_boxes', 'uem_meta_boxes_nonce' );
		$sponsors = get_posts( array(
			'post_type'      => 'mem_sponsor',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'DESC',
		) );
		$selected_sponsors = get_post_meta( $post->ID, '_uem_sponsors', true );
		if ( ! is_array( $selected_sponsors ) ) {
			$selected_sponsors = array();
		}

		?>
		<div class="uem-meta-box">
			<p><strong><?php esc_html_e( 'Select Sponsors', 'mega-events-manager'
				 ); ?></strong></p>
			<?php if ( $sponsors ) : ?>
				<?php foreach ( $sponsors as $sponsor ) : ?>
					<label style="display:block; margin-bottom:6px;">
						<input type="checkbox"
							name="uem_sponsors[]"
							value="<?php echo esc_attr( $sponsor->ID ); ?>"
							<?php checked( in_array( $sponsor->ID, $selected_sponsors, true ) ); ?>
						/>
						<?php echo esc_html( $sponsor->post_title ); ?>
					</label>
				<?php endforeach; ?>
			<?php else : ?>
				<p><?php esc_html_e('No sponsors found.', 'mega-events-manager' ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Save meta boxes data
	 */
	public static function webcu_save_meta_boxes( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['uem_meta_boxes_nonce'] ) ) {
			return;
		}
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['uem_meta_boxes_nonce'], 'uem_save_meta_boxes' ) ) {
			return;
		}		
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}		
		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'mem_event' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		} else {
			return;
		}		
		// Sanitize and save the data	
		update_post_meta($post_id, '_uem_organizers',
		isset( $_POST['uem_organizers'] ) ? array_map( 'intval', $_POST['uem_organizers'] ) : array()
	   );
		update_post_meta(
		$post_id,
		'_uem_volunteers',
		isset( $_POST['uem_volunteers'] ) ? array_map( 'intval', $_POST['uem_volunteers'] ) : array()
	   );
		update_post_meta(
		$post_id,
		'_uem_sponsors',
		isset( $_POST['uem_sponsors'] ) ? array_map( 'intval', $_POST['uem_sponsors'] ) : array()
	  );
   }

}