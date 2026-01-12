<?php
/**
 * Single Event Template
 *
 * @package Mega_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$plugin = Ultimate_Events_Manager::get_instance();
$is_woocommerce = $plugin->webcu_is_woocommerce_enabled() && class_exists( 'WooCommerce' );

$webcu_memEvent = get_option('webcu_event_management_template');

/**
 * Get product sold quantity - only counts completed and processing orders.
 * Uses WooCommerce API to be compatible with both legacy and HPOS storage.
 */
if ( ! function_exists( 'webcu_get_product_sold_qty_product' ) ) {
	function webcu_get_product_sold_qty_product( $product_id ) {
		if ( empty( $product_id ) || ! function_exists( 'wc_get_orders' ) ) {
			return 0;
		}
	    $total_qty = 0;
		// Get completed and processing orders containing this product.
		$orders = wc_get_orders( array(
			'status' => array( 'completed', 'processing' ),
			'limit'  => -1,
			'return' => 'ids',
		) );
		
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				continue;
			}
			
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_product_id() == $product_id || $item->get_variation_id() == $product_id ) {
					$total_qty += $item->get_quantity();
				}
			}
		}
		
		return (int) $total_qty;
	}
}

/**
 * Get cancelled/refunded orders for this product.
 */
if ( ! function_exists( 'get_cancelled_refunded_orders_by_product' ) ) {
	function get_cancelled_refunded_orders_by_product( $product_id ) {
		$order_ids = array();
		$args = array(
			'status' => array('cancelled', 'refunded', 'on-hold'),
			'limit' => -1,
			'return' => 'ids',
		);
		$orders = wc_get_orders( $args );
		
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_product_id() == $product_id || 
					$item->get_variation_id() == $product_id ) {
					$order_ids[] = $order_id;
					break;
				}
			}
		}
		
		return $order_ids;
	}
}

while ( have_posts() ) :
	the_post();
	
	$event_id = get_the_ID();
	//$tickets = get_post_meta( $event_id, '_uem_tickets', true );
	$tickets = get_post_meta( $event_id, '_webcu_tk_tickets', true );

	
	if ( ! is_array( $tickets ) ) {
		$tickets = array();
	}
	?>
	
	<div class="webcu-container">
	    <div class="webcu-wrapper">
			<main class="webcu-content">
				<div class="uem-event-content">
						<?php the_content(); ?>

					  <?php 
						/* echo "<pre>";
						print_r($tickets);
						echo "</pre>";*/
					   ?>	
					</div>	

					<div class="uem-event-people">
						     <!-- Organizer -->
						<?php
								$selected_volunteers = get_post_meta( $post->ID, '_uem_organizers', true );
								if ( ! empty( $selected_volunteers ) && is_array( $selected_volunteers ) ) : ?>
									
									<div class="uem-event-volunteers">
										<h3><?php echo esc_html__( 'Volunteers', 'mega-events-manager' ); ?></h3>
										<ul>

										<?php foreach ( $selected_volunteers as $volunteer_id ) :

											$post_id    = (int) $volunteer_id;
											$post_title = get_the_title( $post_id );
											$post_link  = get_permalink( $post_id );
											$thumb      = get_the_post_thumbnail( $post_id, 'thumbnail' );
											?>

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

										<?php endforeach; ?>

										</ul>
									</div>
							<?php endif; ?>

						   <!-- Volunteer -->
						    <?php
								$selected_volunteers = get_post_meta( $post->ID, '_uem_volunteers', true );
								if ( ! empty( $selected_volunteers ) && is_array( $selected_volunteers ) ) : ?>
									
									<div class="uem-event-volunteers">
										<h3><?php echo esc_html__( 'Volunteers', 'mega-events-manager' ); ?></h3>
										<ul>

										<?php foreach ( $selected_volunteers as $volunteer_id ) :

											$post_id    = (int) $volunteer_id;
											$post_title = get_the_title( $post_id );
											$post_link  = get_permalink( $post_id );
											$thumb      = get_the_post_thumbnail( $post_id, 'thumbnail' );
											?>

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

										<?php endforeach; ?>

										</ul>
									</div>

								<?php endif; ?>


						    <!-- Sponsor -->

					       	<?php
								$selected_volunteers = get_post_meta( $post->ID, '_uem_sponsors', true );
								if ( ! empty( $selected_volunteers ) && is_array( $selected_volunteers ) ) : ?>
									
									<div class="uem-event-volunteers">
										<h3><?php echo esc_html__( 'Volunteers', 'mega-events-manager' ); ?></h3>
										<ul>

										<?php foreach ( $selected_volunteers as $volunteer_id ) :

											$post_id    = (int) $volunteer_id;
											$post_title = get_the_title( $post_id );
											$post_link  = get_permalink( $post_id );
											$thumb      = get_the_post_thumbnail( $post_id, 'thumbnail' );
											?>

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

										<?php endforeach; ?>

										</ul>
									</div>

							<?php endif; ?>

					</div> <!-- END uem-event-people -->

				 <!-- ===== checkout Registration Section ======-->

					<?php if ( ! empty( $tickets ) ) : ?>
						<div class="uem-event-registration">


							<div class="ticket_container">

							 

								<div class="item"> 
									<?php echo esc_html__( 'Ticket Sales Start Date & Time', 'mega-events-manager' ).'<br>';

									foreach ($tickets as $ticket_id => $ticket) {
										//continue;	
										$sale_start_date = $ticket['sale_start_date'] ?? '';
										$sale_start_time = $ticket['sale_start_time'] ?? '';
										$sale_start_name = $ticket['name'] ?? '';

										echo esc_html( $sale_start_name ) . ': ' . esc_html( $sale_start_date ) . ' ' . esc_html( $sale_start_time ) . '<br>';
									}
									?>

								</div>
								<div class="item">

									<?php echo esc_html__( 'Ticket Sales End Date & Time', 'mega-events-manager' ).'<br>'; 

										foreach ($tickets as $ticket_id => $ticket) {

											$sale_end_date   = $ticket['sale_end_date'] ?? '';
											$sale_end_time   = $ticket['sale_end_time'] ?? '';
											$sale_end_name   = $ticket['name'] ?? '';

											echo esc_html( $sale_end_name ) . ': ' . esc_html( $sale_end_date ) . ' ' . esc_html( $sale_end_time ) . '<br>';
										}
									?>

								</div>
								<?php 
								 if ( get_post_meta( get_the_ID(), 'webcu_setting_seat', true ) == '1' ) :					
								?>
								<div class="item total-ticket"> 
									<?php echo esc_html__( 'Total Ticket', 'mega-events-manager' ); ?>
									<br>
										<?php 
										foreach ( $tickets as $ticket_id => $ticket ) {

											$availability     = $ticket['capacity'] ?? '';
											$availability_name = $ticket['name'] ?? '';

											echo esc_html( "$availability_name: $availability" ) . '<br>';
										}
									?>

								</div>
								<?php 
								endif; 
							    if ( get_post_meta( get_the_ID(), 'webcu_setting_attendee', true ) == '1' ) :	
								// Get WooCommerce product IDs from the same meta the admin uses.
								$wc_products = get_post_meta( get_the_ID(), '_uem_wc_products', true );
								if ( ! is_array( $wc_products ) ) {
									$wc_products = array();
								}
								?>		
								<div class="item tickets-sold"> 

									<?php echo esc_html__( 'Tickets Sold', 'mega-events-manager' ); ?>
									<br>
									<?php 
									    $post_id = get_the_ID();
									    $totalseat = 0;
										$totalseat = get_post_meta( $post_id, '_webcu_tk_tickets', true );
										$ticket    = is_array( $totalseat ) ? reset( $totalseat ) : [];
										$quantity  = isset( $ticket['quantity'] ) ? absint( $ticket['quantity'] ) : 0;

										foreach ( $tickets as $ticket_index => $ticket ) {
											$ticket_name = $ticket['name'] ?? '';
											$availability = $ticket['capacity'] ?? 0;

											// Get product ID from _uem_wc_products array using ticket index.
											$product_id  = isset( $wc_products[ $ticket_index ] ) ? (int) $wc_products[ $ticket_index ] : 0;
											
											if ( ! empty( $product_id ) ) {
												// Get total sold (only counts completed/processing orders).
												$total_sold = webcu_get_product_sold_qty_product( $product_id );
												// Get cancelled/refunded orders for this product.
												$cancelled_order_ids = get_cancelled_refunded_orders_by_product( $product_id );
																													
    										/* 	echo esc_html( $ticket_name ) . ': ' . esc_html( $total_sold ) . '<br>';
												echo esc_html('Cancelled Orders') . ': ' . esc_html( count($cancelled_order_ids) ) . '<br>';
												echo esc_html('Available') . ': ' . esc_html( $availability ) . '<br>'; */
										
												echo esc_html('Booking') . ': ' . max(0, $availability -  $total_sold - count($cancelled_order_ids ) ). '<br>';
											
												
											} /* else {
												// If no product yet, show 0 sold.
												echo esc_html( 'Booking' ) . ': 0<br>';
											} */
										}
									?>

								</div>
								<?php endif; ?>

								<div class="clear"></div>
							</div>

								<h2><?php echo esc_html__( 'Register for this Event', 'mega-events-manager' ); ?></h2>

								<?php if ( $is_woocommerce ) : ?>
									<?php webcu_uem_render_woocommerce_registration( $event_id, $tickets ); ?>
								<?php else : ?>
									<?php webcu_uem_render_simple_registration( $event_id, $tickets ); ?>
								<?php endif; ?>

							</div>
					<?php endif; ?>		
				<!-- ===== End checkout Registration Section ======-->
									
			</main>

				<?php
				 if ( "right" === $webcu_memEvent ){
				?>
					<aside class="webcu-sidebar">

						<?php 
							if ( is_singular('mem_event') ) {
								dynamic_sidebar( 'webcu_event_sidebar' );
							}                        
						?>                        
							
					</aside>	
					<?php
					}
				?>

	    </div>					
	</div>


<?php
endwhile;

get_footer();