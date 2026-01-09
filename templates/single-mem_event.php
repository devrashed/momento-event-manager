<?php
/**
 * Single Event Template
 *
 * @package Ultimate_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$plugin = Ultimate_Events_Manager::get_instance();
$is_woocommerce = $plugin->webcu_is_woocommerce_enabled() && class_exists( 'WooCommerce' );

while ( have_posts() ) :
	the_post();
	
	$event_id = get_the_ID();
	//$tickets = get_post_meta( $event_id, '_uem_tickets', true );
	$tickets = get_post_meta( $event_id, '_webcu_tk_tickets', true );
	if ( ! is_array( $tickets ) ) {
		$tickets = array();
	}

	//$saved_data = get_post_meta($post->ID, 'attendee_form_data_types', true);
	/* echo "<pre>";
	var_dump($saved_data);
	echo "</pre>";  */
	
	/* $organizers = get_post_meta( $event_id, 'webcu_event_orga_name', true );
	$volunteers = get_post_meta( $event_id, '_uem_volunteers', true );
	$sponsors = get_post_meta( $event_id, '_uem_sponsors', true ); */
	
	$event_date = get_post_meta( $event_id, '_uem_event_date', true );
	$event_time = get_post_meta( $event_id, '_uem_event_time', true );
	$event_end_date = get_post_meta( $event_id, '_uem_event_end_date', true );
	$event_end_time = get_post_meta( $event_id, '_uem_event_end_time', true );
	$event_location = get_post_meta( $event_id, '_uem_event_location', true );
	$event_address = get_post_meta( $event_id, '_uem_event_address', true );
	?>
	
	<div class="uem-single-event">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>
			
			<div class="entry-content">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="uem-event-featured-image">
						<?php the_post_thumbnail( 'large' ); ?>
					</div>
				<?php endif; ?>
				
				<div class="uem-event-details">          
					<?php if ( $event_date ) : ?>
						<p><strong><?php echo esc_html__( 'Event Date:', 'ultimate-events-manager' ); ?></strong> 
							<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $event_date ) ) ); ?>
							<?php if ( $event_time ) : ?>
								<?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( $event_time ) ) ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					
					<?php if ( $event_end_date ) : ?>
						<p><strong><?php echo esc_html__( 'Event End Date:', 'ultimate-events-manager' ); ?></strong> 
							<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $event_end_date ) ) ); ?>
							<?php if ( $event_end_time ) : ?>
								<?php echo esc_html( date_i18n( get_option( 'time_format' ), strtotime( $event_end_time ) ) ); ?>
							<?php endif; ?>
						</p>
					<?php endif; ?>
					
					<?php if ( $event_location ) : ?>
						<p><strong><?php echo esc_html__( 'Location:', 'ultimate-events-manager' ); ?></strong> <?php echo esc_html( $event_location ); ?></p>
					<?php endif; ?>
					
					<?php if ( $event_address ) : ?>
						<p><strong><?php echo esc_html__( 'Address:', 'ultimate-events-manager' ); ?></strong><br><?php echo nl2br( esc_html( $event_address ) ); ?></p>
					<?php endif; ?>
				</div>
				
				<div class="uem-event-content">
					<?php the_content(); ?>
				</div>
				
				<!-- organizer -->

				<?php
				    $saved_value = get_post_meta( $post->ID, 'webcu_event_orga_name', true );
					if ( ! empty( $saved_value ) ) :

						$post_id    = (int) $saved_value;
						$post_title = get_the_title( $post_id );
						$post_link  = get_permalink( $post_id );
						$thumb      = get_the_post_thumbnail( $post_id, 'thumbnail' );
				?>
				<div class="uem-event-organizers">
					<h3><?php esc_html_e( 'Organizer', 'ultimate-events-manager' ); ?></h3>

					<ul>
						<li>
							<a href="<?php echo esc_url( $post_link ); ?>" target="_blank">
								<?php
								if ( $thumb ) {
									echo $thumb;
								} else {
									echo '<img src="' . esc_url( wc_placeholder_img_src() ) . '" alt="">';
								}
								?>
								<span><?php echo esc_html( $post_title ); ?></span>
							</a>
						</li>
					</ul>
				</div>
				<?php endif; ?>

				<!-- Volunteer -->

				<?php 
					$saved_value = get_post_meta( $post->ID, 'webcu_event_volunteer_name', true );
					if ( ! empty( $saved_value ) ) :
					$post_id    = (int) $saved_value;
					$post_title = get_the_title( $post_id );
					$post_link  = get_permalink( $post_id );
					$thumb      = get_the_post_thumbnail( $post_id, 'thumbnail' );	
				?>

				<div class="uem-event-volunteers">
						<h3><?php echo esc_html__( 'Volunteers', 'ultimate-events-manager' ); ?></h3>
				    <ul>
						<li>
							<a href="<?php echo esc_url( $post_link ); ?>"  target="_blank">
								<?php
								if ( $thumb ) {
									echo $thumb;
								} else {
									echo '<img src="' . esc_url( wc_placeholder_img_src() ) . '" alt="">';
								}
								?>
								<span><?php echo esc_html( $post_title ); ?></span>
							</a>
						</li>
					</ul>
				</div>
				<?php endif; ?>
				
				<!-- Sponsor -->
				
				<?php 
					$saved_value = get_post_meta( $post->ID, 'webcu_event_sponsa_name', true );
					if ( ! empty( $saved_value ) ) :
					$post_id    = (int) $saved_value;
					$post_title = get_the_title( $post_id );
					$post_link  = get_permalink( $post_id );
					$thumb      = get_the_post_thumbnail( $post_id, 'thumbnail' );	
				?>

				<div class="uem-event-sponsors">
					<h3><?php echo esc_html__( 'Sponsers', 'ultimate-events-manager' ); ?></h3>
					<ul>
						<li>
							<a href="<?php echo esc_url( $post_link ); ?>" target="_blank">
								<?php
								if ( $thumb ) {
									echo $thumb;
								} else {
									echo '<img src="' . esc_url( wc_placeholder_img_src() ) . '" alt="" >';
								}
								?>
								<span><?php echo esc_html( $post_title ); ?></span>
							</a>
						</li>
					</ul>
				</div>
				<?php endif; ?>

			



				<?php if ( ! empty( $tickets ) ) : ?>
				<div class="uem-event-registration">
					<h2><?php echo esc_html__( 'Register for this Event', 'ultimate-events-manager' ); ?></h2>
					
					<?php if ( $is_woocommerce ) : ?>
						<?php webcu_uem_render_woocommerce_registration( $event_id, $tickets ); ?>
					<?php else : ?>
						<?php webcu_uem_render_simple_registration( $event_id, $tickets ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</article>
	</div>

<?php
endwhile;

get_footer();

