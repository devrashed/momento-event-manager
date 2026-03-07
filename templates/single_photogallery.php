<?php 

function single_photogallery(){
 global $post;
?>
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
                        } 
                     
                     ?>