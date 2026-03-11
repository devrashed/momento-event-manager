<?php 

function event_associate() {
global $post;
?>

<h3> <?php echo esc_html__( 'Associate of the event', 'momento-event-manager' ).'<br>';?> </h3>
				<div class="uem-event-associates">

						<!-- Organizer -->
					<?php
							$selected_volunteers = get_post_meta( $post->ID, '_uem_organizers', true );
							if ( ! empty( $selected_volunteers ) && is_array( $selected_volunteers ) ) : ?>
								
								<div class="uem-event-volunteers">
									<h3><?php echo esc_html__( 'Volunteers', 'momento-event-manager' ); ?></h3>
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
									<h3><?php echo esc_html__( 'Volunteers', 'momento-event-manager' ); ?></h3>
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
									<h3><?php echo esc_html__( 'Volunteers', 'momento-event-manager' ); ?></h3>
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
<?php } ?>                