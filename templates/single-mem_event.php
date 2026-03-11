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


require_once __DIR__ . '/google_map_location.php';
require_once __DIR__ . '/media_video_upload.php';
require_once __DIR__ . '/faq_questions_answer.php';
require_once __DIR__ . '/single_photogallery.php';
require_once __DIR__ . '/terms_condiation.php';
require_once __DIR__ . '/event_associate.php';

$plugin = Ultimate_Events_Manager::get_instance();
$is_woocommerce = $plugin->wtmem_is_woocommerce_enabled() && class_exists( 'WooCommerce' );

$wtmem_memEvent = get_option('wtmem_event_management_template');

/**
 * Get product sold quantity - only counts completed and processing orders.
 * Uses WooCommerce API to be compatible with both legacy and HPOS storage.
 */
if ( ! function_exists( 'wtmem_get_product_sold_qty_product' ) ) {
	function wtmem_get_product_sold_qty_product( $product_id ) {
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
	$tickets = get_post_meta( $event_id, '_wtmem_tk_tickets', true );
	
	if ( ! is_array( $tickets ) ) {
		$tickets = array();
	}
	?>
	
	<div class="wtmem-container">
	    <div class="wtmem-wrapper">
			<main class="wtmem-content">

	          <div class="hero_banner_section">
					<?php
					$image_id  = get_post_meta( get_the_ID(), '_event_image_id', true );
					$image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';	
					?>	
					<img id="rk-event-image-preview"
					src="<?php echo esc_url( $image_url ); ?>"
					/>
			  
			  </div> <!-- end the hero section -->

				<div class="uem-event-content">

				  <?php the_content(); ?>

				</div>	

				<div class="event_content">

					<!-- photogallery check -->

				   <?php single_photogallery(); ?>
					
					<!-- Video check -->
					
					<?php wtmem_video_media(); ?>
				
					<!-- FAQ section -->

					 <?php web_faq(); ?>

				<!-- Terms & Condition section -->

					<div class="container">	
						<?php terms_condiation(); ?>
					</div>	

							
					<!-- Google Map -->

					<?php 

					$show_gmap = get_post_meta($post->ID, 'wtmem_ve_googleMap', true); 
						if ( $show_gmap === '1') : 
					     google_map_location();
						?>			
					<?php endif; ?>	
				
     			</div>

				<!-- ===== Event associates Section ======-->
				
				 <?php  event_associate(); ?>

			
	    </div>	<!-- end of event content -->									

				 <!-- ===== checkout Registration Section ======-->
				
			    <?php 
					$show_registration = false;
				    foreach ($tickets as $ticket_id => $ticket): 

				   
					echo $sale_start_date = $ticket['sale_start_date'];
					echo $sale_end_date   = $ticket['sale_end_date'];
					echo $sale_start_time = $ticket['sale_start_time'];
					echo $sale_end_time   = $ticket['sale_end_time'];

					
					
					// Current date and time
					$current_date = date('Y-m-d');
					$current_time = date('H:i:s');
					
					// Convert times to comparable format
					$current_timestamp = strtotime($current_date . ' ' . $current_time);
					$start_timestamp = strtotime($sale_start_date . ' ' . $sale_start_time);
					$end_timestamp = strtotime($sale_end_date . ' ' . $sale_end_time);
					
					// Check if current time is within the sale period
					$show_registration = ($current_timestamp >= $start_timestamp && 
					$current_timestamp <= $end_timestamp);

					if ($show_registration):
                ?>
			

					<?php if ( ! empty( $tickets ) ) : ?>
						<div class="uem-event-registration">

							<div class="ticket_container">
						 
								<div class="item"> 
									<?php echo esc_html__( 'Ticket Sales Start Date & Time', 'momento-event-manager' ).'<br>';

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

									<?php echo esc_html__( 'Ticket Sales End Date & Time', 'momento-event-manager' ).'<br>'; 

										foreach ($tickets as $ticket_id => $ticket) {

											$sale_end_date   = $ticket['sale_end_date'] ?? '';
											$sale_end_time   = $ticket['sale_end_time'] ?? '';
											$sale_end_name   = $ticket['name'] ?? '';

											echo esc_html( $sale_end_name ) . ': ' . esc_html( $sale_end_date ) . ' ' . esc_html( $sale_end_time ) . '<br>';
										}
									?>

								</div>
								<?php 
								 if ( get_post_meta( get_the_ID(), 'wtmem_setting_seat', true ) == '1' ) :					
								?>
								<div class="item total-ticket"> 
									<?php echo esc_html__( 'Total Ticket', 'momento-event-manager' ); ?>
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
							    if ( get_post_meta( get_the_ID(), 'wtmem_setting_attendee', true ) == '1' ) :	
								// Get WooCommerce product IDs from the same meta the admin uses.
								$wc_products = get_post_meta( get_the_ID(), '_uem_wc_products', true );
								if ( ! is_array( $wc_products ) ) {
									$wc_products = array();
								}
								?>		
								<div class="item tickets-sold"> 

									<?php echo esc_html__( 'Tickets Sold', 'momento-event-manager' ); ?>
									<br>
									<?php 
									   foreach ( $tickets as $ticket_index => $ticket ) {
											$ticket_name = $ticket['name'] ?? '';
											// Get product ID from _uem_wc_products array using ticket index.
											$product_id  = isset( $wc_products[ $ticket_index ] ) ? (int) $wc_products[ $ticket_index ] : 0;
											
											if ( ! empty( $product_id ) ) {
											$total_sold = wtmem_get_product_sold_qty_product( $product_id );

											echo esc_html( 'sold' ) . ': ' . esc_html( $total_sold ) . '<br>';
											$availability     = $ticket['capacity'] ?? '';
											$available_qty    = $availability - $total_sold;
											echo esc_html__( 'Available' ) . ': ' . esc_html( max(0, $available_qty ) ) . '<br>';							
				
											}	 	
										}
									?>

								</div>
								<?php endif; ?>

								<div class="clear"></div>
							</div>
								<h2><?php echo esc_html__( 'Register for this Event', 'momento-event-manager' ); ?></h2>

								<?php if ( $is_woocommerce ) : ?>
									<?php wtmem_uem_render_woocommerce_registration( $event_id, $tickets ); ?>
								<?php else : ?>
									<?php wtmem_uem_render_simple_registration( $event_id, $tickets ); ?>
								<?php endif; ?>
	  					</div>

				    <?php 
					   endif;
					endif; 
				endforeach;	
				?>		

				<!-- ===== End checkout Registration Section ======-->
			</main>

				<?php
				 if ( "right" === $wtmem_memEvent ){
				?>
					<aside class="wtmem-sidebar">

						<?php 
							if ( is_singular('mem_event') ) {
								dynamic_sidebar( 'wtmem_event_sidebar' );
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