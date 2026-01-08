<?php 
/**
 *
 *  Webcartisan photogallery
 *
 **/


class class_event_meta_photogallery { 

    public function webcu_add_gallery_meta_box() {
        add_meta_box(
            'webcu_event_gallery',
            'Event Photo Gallery',
            [$this, 'webcu_gallery_meta_box_callback'],
            'mem_event',
            'side',
            'default'
        );
    }
   
    public function webcu_gallery_meta_box_callback($post) {

        wp_nonce_field('webcu_save_gallery', 'webcu_gallery_nonce');

        $gallery_ids = get_post_meta($post->ID, '_webcu_gallery_ids', true);
        ?>
        <div class="webcu-gallery-container">
            <div id="webcu-gallery-images" class="webcu-gallery-grid">
                <?php
                if (!empty($gallery_ids)) {
                    $ids = explode(',', $gallery_ids);
                    foreach ($ids as $id) {
                        $image = wp_get_attachment_image_src($id, 'thumbnail');
                        if ($image) {
                            echo '<div class="webcu-gallery-item" data-id="' . esc_attr($id) . '">';
                            echo '<img src="' . esc_url($image[0]) . '" />';
                            echo '<span class="webcu-remove-image" title="Remove">&times;</span>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            <input type="hidden" id="webcu_gallery_ids" name="webcu_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>" />

            <button type="button" class="button button-primary" id="webcu_add_gallery_images">
                <?php echo esc_html__('Add Images', 'ultimate-event-manager'); ?>
            </button>
        </div>
        <?php
    }


    public function webcu_save_gallery_meta($post_id) {

        if ( ! isset( $_POST['webcu_gallery_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash( $_POST['webcu_gallery_nonce'] )), 'webcu_save_gallery')
        ) {
            return;
        }    

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['webcu_gallery_ids'])) {
            update_post_meta( $post_id, '_webcu_gallery_ids', sanitize_text_field(wp_unslash($_POST['webcu_gallery_ids']))
            );
        } 
    }
 


}