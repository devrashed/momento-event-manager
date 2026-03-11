<?php 
namespace Wpcraft\Metabox;
/**
 *
 *  Webcartisan event Media
 *
 **/
    class class_meta_event_media{

        public function __construct(){
            // Save is now handled by class-event-metabox.php in wtmem_save_event_registration_meta().
        }

      public function wtmem_event_media_meta_field($post){
   
        // Add nonce field for security
        wp_nonce_field('wtmem_event_media_meta', 'wtmem_event_media_nonce');
   
        /* ======= Event Photo gallery ======== */
        // Using _wtmem_gallery_ids to preserve existing sidebar gallery data.
        $gallery_ids = get_post_meta($post->ID, '_wtmem_gallery_ids', true);
        ?>
        <div class="wtmem-gallery-container">
            <div id="wtmem-event-gallery-images" class="wtmem-event-gallery-grid">
                <?php
                if (!empty($gallery_ids)) {
                    $ids = explode(',', $gallery_ids);
                    foreach ($ids as $id) {
                        $image = wp_get_attachment_image_src($id, 'thumbnail');
                        if ($image) {
                            echo '<div class="wtmem-event-gallery-item" data-id="' . esc_attr($id) . '">';
                            echo '<img src="' . esc_url($image[0]) . '" />';
                            echo '<span class="wtmem-event-remove-image" title="Remove">&times;</span>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
            <input type="hidden" id="wtmem_event_gallery" name="wtmem_gallery_ids" value="<?php echo esc_attr($gallery_ids); ?>" />
            <button type="button" class="button button-primary" id="wtmem_event_gallery_images">
                <?php echo esc_html__('Add Images', 'momento-event-manager'); ?>
            </button>
        </div>


         <!-- ===== Hero banenr ==== -->                                     
            
        <h3><?php echo esc_html__('Hero Banner (Portrait Banner)', 'momento-event-manager') ?></h3>    
         
            <?php 
                $image_id  = get_post_meta( $post->ID, '_event_image_id', true );
                $image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
            ?>    

            <div class="rk-event-upload-image-wrap">

                <img id="rk-event-image-preview"
                 src="<?php echo esc_url( $image_url ); ?>"
                 style="width:300px; height:300px; object-fit:cover; display:<?php echo $image_url ? 'block' : 'none'; ?>;"  />

                <input type="hidden" name="event_image_id" id="event_image_id" value="<?php echo esc_attr( $image_id ); ?>" />

                <p>
                    <button type="button" class="button" id="rk-event-upload-btn">
                        <?php echo $image_url ? 'Change Image' : 'Upload Image'; ?>
                    </button>

                    <button type="button" class="button" id="rk-event-remove-btn" style="display:<?php echo $image_url ? 'inline-block' : 'none'; ?>;">
                        <?php echo esc_html__('Remove', 'momento-event-manager') ?>
                    </button>
                </p>
            </div>
        <?php

        /* === Video upload === */

        $video_id = 0;
        $video_type = get_post_meta( $post->ID, '_wtmem_events_video_type', true );
        $youtube_url  = get_post_meta( $post->ID, '_wtmem_events_youtube_url', true );
        $vimeo_url    = get_post_meta( $post->ID, '_wtmem_events_vimeo_url', true );
        $video_id     = get_post_meta( $post->ID, '_wtmem_events_self_video_id', true );
        $video_url    = $video_id ? wp_get_attachment_url( $video_id ) : '';
        ?>

        <table class="wtmem_tk_extra-table">
            <tr> 
                <th><?php echo esc_html__('Select your video type', 'momento-event-manager') ?></th>
                <th><?php echo esc_html__('Video link', 'momento-event-manager') ?></th>
            </tr>
        
            <tr>                          
            <td style="width:250px;"> 

                <select name="wtmem_events_video_type" id="wtmem-video-type">
                    <option value="">Select Type</option>
                    <option value="youtube" <?php selected($video_type, 'youtube'); ?>>YouTube</option>
                    <option value="vimeo" <?php selected($video_type, 'vimeo'); ?>>Vimeo</option>
                    <option value="ownvideo" <?php selected($video_type, 'ownvideo'); ?>>Own Video</option>
                </select>                        

            </td>
                <td>
                
                <!-- YouTube URL -->
                <p id="wtmem-youtube-field" style="display:none;">
                    <label><strong><?php echo esc_html__('YouTube URL:', 'momento-event-manager') ?></strong></label>
                    <input type="text" name="wtmem_events_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="widefat">
                </p>

                <!-- Vimeo URL -->
                <p id="wtmem-vimeo-field" style="display:none;">
                    <label><strong><?php echo esc_html__('Vimeo URL:', 'momento-event-manager') ?></strong></label>
                    <input type="text" name="wtmem_events_vimeo_url" value="<?php echo esc_attr($vimeo_url); ?>" class="widefat">
                </p>

                <!-- Own Video Upload -->
                <div id="wtmem-ownvideo-field" style="display:none;">
                    <label><strong><?php echo esc_html__('Upload Video:', 'momento-event-manager') ?></strong></label><br>
                    <video id="wtmem-video-preview" src="<?php echo esc_url($video_url); ?>" style="max-width:100%; display:<?php echo $video_url ? 'block' : 'none'; ?>;" controls></video>
                    <input type="hidden" id="wtmem-video-id" name="wtmem_events_self_video_id" value="<?php echo esc_attr($video_id); ?>" />
                    <p>
                        <button type="button" class="button" id="wtmem-upload-video-btn">
                            <?php echo $video_url ? 'Change Video' : 'Upload Video'; ?>
                        </button>

                        <button type="button" class="button" id="wtmem-remove-video-btn" style="display:<?php echo $video_url ? 'inline-block' : 'none'; ?>;">
                            <?php echo esc_html__('Remove', 'momento-event-manager') ?>
                        </button>
                    </p>                                                     
                </div>                                      
                </td>
            </tr>             
        </table>   
        <?php
    }

        public function wtmem_event_media_meta_save___sss($post_id){  

            // Verify nonce
            /* if (!isset($_POST['wtmem_event_media_nonce']) || !wp_verify_nonce($_POST['wtmem_event_media_nonce'], 'wtmem_event_media_meta')) {
                return;
            }

            // Check autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            // Check permissions
            if (!current_user_can('edit_post', $post_id)) {
                return;
            } */
            
            /* ===== Hero banner ==== */
            if ( isset($_POST['event_image_id']) ) {
                update_post_meta( $post_id, '_event_image_id', sanitize_text_field( $_POST['event_image_id'] ) );
            }
            
            /*========= Event Photo gallery ==========*/
           /*  if (isset($_POST['wtmem_event_gallery'])) {
                update_post_meta($post_id, '_wtmem_event_gallery', sanitize_text_field(wp_unslash($_POST['wtmem_event_gallery'])));
            } */

            ///if (isset($_POST['wtmem_event_gallery'])) {
                $gallery_data = sanitize_text_field( wp_unslash( $_POST['wtmem_event_gallery'] ) );
                update_post_meta($post_id, '_wtmem_event_gallery', $gallery_data);
            ///}

            /*========= Video upload ==========*/
            if (isset($_POST['wtmem_events_video_type'])) {
                update_post_meta($post_id, '_wtmem_events_video_type', sanitize_text_field($_POST['wtmem_events_video_type']));
            }
            
            if (isset($_POST['wtmem_events_youtube_url'])) {
                update_post_meta($post_id, '_wtmem_events_youtube_url', sanitize_text_field($_POST['wtmem_events_youtube_url']));
            }
            
            if (isset($_POST['wtmem_events_vimeo_url'])) {
                update_post_meta($post_id, '_wtmem_events_vimeo_url', sanitize_text_field($_POST['wtmem_events_vimeo_url']));
            }

            if (isset($_POST['wtmem_events_self_video_id'])) {
                update_post_meta($post_id, '_wtmem_events_self_video_id', sanitize_text_field($_POST['wtmem_events_self_video_id']));
            }

        } 

        public function wtmem_event_media_meta_savess($post_id){  
            // Only process event posts.
            if ( get_post_type( $post_id ) !== 'mem_event' ) {
                return;
            }

            // Verify nonce - must unslash before verification.
            $nonce = isset( $_POST['wtmem_event_media_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wtmem_event_media_nonce'] ) ) : '';
            if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'wtmem_event_media_meta' ) ) {
                return;
            }

            // Check autosave.
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
                return;
            }

            // Check permissions.
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
            
            /* ===== Hero banner ==== */
            if ( isset( $_POST['event_image_id'] ) ) {
                update_post_meta( $post_id, '_event_image_id', sanitize_text_field( wp_unslash( $_POST['event_image_id'] ) ) );
            }
            
            /*========= Event Photo gallery ==========*/
            if ( isset( $_POST['wtmem_event_gallery'] ) ) {
                $gallery_data = sanitize_text_field( wp_unslash( $_POST['wtmem_event_gallery'] ) );
                update_post_meta( $post_id, '_wtmem_event_gallery', $gallery_data );
            }

            /*========= Video upload ==========*/
            if ( isset( $_POST['wtmem_events_video_type'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_video_type', sanitize_text_field( wp_unslash( $_POST['wtmem_events_video_type'] ) ) );
            }
            
            if ( isset( $_POST['wtmem_events_youtube_url'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_youtube_url', esc_url_raw( wp_unslash( $_POST['wtmem_events_youtube_url'] ) ) );
            }
            
            if ( isset( $_POST['wtmem_events_vimeo_url'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_vimeo_url', esc_url_raw( wp_unslash( $_POST['wtmem_events_vimeo_url'] ) ) );
            }

            if ( isset( $_POST['wtmem_events_self_video_id'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_self_video_id', absint( wp_unslash( $_POST['wtmem_events_self_video_id'] ) ) );
            }
        }

    }