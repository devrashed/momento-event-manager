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

					<h3> <?php echo esc_html__( 'Photo Gallery', 'mega-events-manager' ).'<br>';?> </h3>

						<?php  $gallery_ids = get_post_meta($post->ID, '_webcu_gallery_ids', true); ?>

						<div id="webcu-event-gallery-images" class="webcu-event-gallery-grid">
							<?php
							if (!empty($gallery_ids)) {
								$ids = explode(',', $gallery_ids);
								foreach ($ids as $id) {
									$image = wp_get_attachment_image_src($id, 'thumbnail');
									if ($image) {
										echo '<div class="webcu-event-gallery-item" data-id="' . esc_attr($id) . '">';
										echo '<img src="' . esc_url($image[0]) . '" />';
										echo '</div>';
									}
								}
							}
							?>
					
					<div class="container" style="clear:both">	
						<h3> <?php echo esc_html__( 'Video', 'mega-events-manager' ).'<br>';?> </h3>
					</div>	
						<?php
							$video_id = 0;	
							$video_type   = get_post_meta( $post->ID, '_webcu_events_video_type', true );   
							$ownvideo = get_post_meta($post->ID, '_webcu_events_self_video_id', true);
							$youvideo = get_post_meta($post->ID, '_webcu_events_youtube_url', true);
							$vimeovieo = get_post_meta($post->ID, '_webcu_events_vimeo_url', true);   

							$youvideo_embed = str_replace("watch?v=", "embed/", $youvideo);

							if ( 'youtube' === $video_type) {
							?>
							
							<iframe width="560" height="315" 
								src="<?php echo esc_url($youvideo_embed); ?>" 
								title="YouTube video player" 
								frameborder="0" 
								allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
								referrerpolicy="strict-origin-when-cross-origin" 
								allowfullscreen>
						    </iframe>  

						
							<?php 
							} elseif ( 'vimeo' === $video_type) {

								$vimeovieo = get_post_meta($post->ID, '_webcu_events_vimeo_url', true);
								$video_id = preg_replace('/[^0-9]/', '', $vimeovieo);
								$vimeo_embed = "https://player.vimeo.com/video/" . $video_id;
								?>

								<iframe 
									width="460" 
									height="315"
									src="<?php echo esc_url($vimeo_embed); ?>" 
									frameborder="0" 
									allow="autoplay; fullscreen; picture-in-picture; clipboard-write; encrypted-media; web-share" 
									referrerpolicy="strict-origin-when-cross-origin"
									allowfullscreen
								></iframe>

								<script src="https://player.vimeo.com/api/player.js"></script>
						
							<?php 
							} elseif ( 'ownvideo' === $video_type) {
								
								$ownvideo = get_post_meta($post->ID, '_webcu_events_self_video_id', true);
								$image_url = wp_get_attachment_url( $ownvideo ); 
								?>
								<video width="560" height="315" controls> 
								<source src="<?php echo esc_url($image_url); ?>">
								</video> 

							<?php } ?>	
                    
					

					
					<!-- FAQ section -->

					<div class="container">	
						<?php  
							$display_faq = get_post_meta($post->ID, 'display_faq', true); 

							if ( $display_faq === '1' || $display_faq === 'yes' ) : 
								// Also check if FAQ data exists
								$faq_faq = get_post_meta($post->ID, 'faq_faq', true);
								
								if (!empty($faq_faq) && is_array($faq_faq)) :
							?>
							
						    <h3><?php echo esc_html__('FAQ', 'mega_events_manager'); ?></h3>
								
								<div class="faq-section">
									<?php foreach ($faq_faq as $item) : 
										if (!empty($item['title']) || !empty($item['content'])) :
									?>
										<div class="faq-item">
											<?php if (!empty($item['title'])) : ?>
												<h4 class="faq-question"><?php echo esc_html($item['title']); ?></h4>
											<?php endif; ?>
											
											<?php if (!empty($item['content'])) : ?>
												<div class="faq-answer">
													<?php echo wp_kses_post($item['content']); ?>
												</div>
											<?php endif; ?>
										</div>
									<?php 
										endif;
									endforeach; ?>
								</div>								
							<?php 
								endif;
							endif; 
						?>
					</div>

				<!-- Terms & Condition section -->

					<div class="container">	
						<?php  
							$display_tc = get_post_meta($post->ID, 'display_tc', true); 

							if ( $display_tc === '1' || $display_tc === 'yes' ) : 
								// Also check if FAQ data exists
								$tc_items = get_post_meta($post->ID, 'tc_items', true);

    							if (!empty($tc_items) && is_array($tc_items)) :
							?>

							<h3><?php echo esc_html__('Terms & Condition', 'mega_events_manager'); ?></h3>
								
								<div class="terms-condition">
									<?php foreach ($tc_items as $item) : 
										if (!empty($item['title'])) :
									?>
										<div class="terms-question">
											<?php if (!empty($item['title'])) : ?>
												<h4 class="terms-question"> <a href="<?php echo esc_url($item['url']); ?>"> <?php echo esc_html($item['title']); ?> </a> </h4>
											<?php endif; ?>
											
										</div>
									<?php 
										endif;
									endforeach; ?>
								</div>								
							<?php 
								endif;
							endif; 
						?>
					</div>	

							
					<!-- Google Map -->

					<?php 

					    $show_gmap = get_post_meta($post->ID, 'webcu_ve_googleMap', true); 
						if ( $show_gmap === '1') : 
						?>		
						<div class="container">	
                         <h3><?php echo esc_html('Event Location on google Map', 'mega_events_manager')?> </h3>
							
						<?php       
							$post_id= get_the_ID(); 

							$address=get_post_meta($post_id,'webcu_ve_street',true);
							$city=get_post_meta($post_id,'webcu_ve_city',true);
							$state=get_post_meta($post_id,'webcu_ve_state',true);
							$zip=get_post_meta($post_id,'webcu_ve_postcocde',true);
							$country=get_post_meta($post_id,'webcu_ve_country',true);

							$apiKey=get_option('google_map_api');
					
							$full_address = implode( ', ', array_filter( array(
								$address,
								$city,
								$state,
								$zip,
								$country
							) ) );  
							
							$encodedAddress = urlencode($full_address);
							$geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address={$encodedAddress}&key={$apiKey}";      
							$response = file_get_contents($geocodeUrl);
							$data = json_decode($response, true);
							$locations = [];
							if ($data['status'] === 'OK') {
								$lat = $data['results'][0]['geometry']['location']['lat'];
								$lng = $data['results'][0]['geometry']['location']['lng'];
								$locations[] = [
									'title' => 'My Event Location',
									'lat'   => $lat,
									'lng'   => $lng,
								];
							} else {
								echo "Geocoding failed: " . $data['status'];
							}

						if ( ! empty( $locations ) ) {  

							?>
							<div id="map" style="height: 400px; width: 100%;"></div>
							<script>

								function initMap() {
									var map = new google.maps.Map(document.getElementById('map'), {
										zoom: 12,
										center: {lat: <?php echo $locations[0]['lat']; ?>, lng: <?php echo $locations[0]['lng']; ?>}
									});

									<?php foreach ( $locations as $location ) : ?>
									var marker = new google.maps.Marker({
										position: {lat: <?php echo $location['lat']; ?>, lng: <?php echo $location['lng']; ?>},
										map: map,
										title: '<?php echo esc_js( $location['title'] ); ?>'
									});
									<?php endforeach; ?>
								}
								
							</script>
							<script async defer
								src="https://maps.googleapis.com/maps/api/js?key=<?php echo $apiKey; ?>&callback=initMap">
							</script>
							<?php
						}
						?>   
											
						</div> 
					<?php endif; ?>	

		
										

      			</div>


				  



				<!-- ===== Event associates Section ======-->
				<h3> <?php echo esc_html__( 'Associate of the event', 'mega-events-manager' ).'<br>';?> </h3>
				<div class="uem-event-associates">

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

				</div> <!-- END uem-event-associates -->
			
			
			
			
		
		</div>	<!-- end of event content -->									

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
									   foreach ( $tickets as $ticket_index => $ticket ) {
											$ticket_name = $ticket['name'] ?? '';
											// Get product ID from _uem_wc_products array using ticket index.
											$product_id  = isset( $wc_products[ $ticket_index ] ) ? (int) $wc_products[ $ticket_index ] : 0;
											
											if ( ! empty( $product_id ) ) {
											$total_sold = webcu_get_product_sold_qty_product( $product_id );

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