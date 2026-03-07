<?php 

function web_faq(){
global $post;
?>


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
<?php } ?>                    