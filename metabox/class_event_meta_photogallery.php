<?php 
namespace Wpcraft\Metabox;
/**
 *
 *  Webcartisan photogallery
 *
 **/


class class_event_meta_photogallery { 

    public function wtmem_add_gallery_meta_box() {
        add_meta_box(
            'wtmem_event_gallery',
            'Event Photo Gallery',
            [$this, 'wtmem_gallery_meta_box_callback'],
            'mem_event',
            'side',
            'default'
        );
    }
   
    public function wtmem_gallery_meta_box_callback($post) {

        wp_nonce_field('wtmem_save_gallery', 'wtmem_gallery_nonce');

        $gallery_ids = get_post_meta($post->ID, '_wtmem_gallery_ids', true);
        ?>
        <div class="wtmem-gallery-container">
            <div id="wtmem-gallery-images" class="wtmem-gallery-grid">
                <?php
                if (!empty($gallery_ids)) {
                    $ids = explode(',', $gallery_ids);
                    foreach ($ids as $id) {
                        $image = wp_get_attachment_image_src($id, 'thumbnail');
                        if ($image) {
                            echo '<div class="wtmem-gallery-item" data-id="' . esc_attr($id) . '">';
                            echo '<img src="' . esc_url($image[0]) . '" />';
                            echo '<span class="wtmem-remove-image" title="Remove">&times;</span>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            <input type="hidden" id="wtmem_gallery_ids" name="wtmem_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>" />

            <button type="button" class="button button-primary" id="wtmem_add_gallery_images">
                <?php echo esc_html__('Add Images', 'ultimate-event-manager'); ?>
            </button>
        </div>
        <?php
    }


    public function wtmem_save_gallery_meta($post_id) {

        if ( ! isset( $_POST['wtmem_gallery_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash( $_POST['wtmem_gallery_nonce'] )), 'wtmem_save_gallery')
        ) {
            return;
        }    

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['wtmem_gallery_ids'])) {
            update_post_meta( $post_id, '_wtmem_gallery_ids', sanitize_text_field(wp_unslash($_POST['wtmem_gallery_ids']))
            );
        } 
    }
 


}