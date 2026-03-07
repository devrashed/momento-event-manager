
<?php  
function terms_condiation() {
global $post;

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
}    
?>