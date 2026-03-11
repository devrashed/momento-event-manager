<?php 

function wtmem_video_media(){
global $post;
?>
<div class="container" style="clear:both">	
						<h3> <?php echo esc_html__( 'Video', 'momento-event-manager' ).'<br>';?> </h3>
					</div>	
						<?php
							$video_id = 0;	
							$video_type   = get_post_meta( $post->ID, '_wtmem_events_video_type', true );   
							$ownvideo = get_post_meta($post->ID, '_wtmem_events_self_video_id', true);
							$youvideo = get_post_meta($post->ID, '_wtmem_events_youtube_url', true);
							$vimeovieo = get_post_meta($post->ID, '_wtmem_events_vimeo_url', true);   

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

								$vimeovieo = get_post_meta($post->ID, '_wtmem_events_vimeo_url', true);
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
								
								$ownvideo = get_post_meta($post->ID, '_wtmem_events_self_video_id', true);
								$image_url = wp_get_attachment_url( $ownvideo ); 
								?>
								<video width="560" height="315" controls> 
								<source src="<?php echo esc_url($image_url); ?>">
								</video> 

							<?php } ?>	
                    
<?php } ?>                            