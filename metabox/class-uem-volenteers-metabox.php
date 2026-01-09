<?php
/**
 *
 *  Sponser custom post type
 *
 **/

class class_volunteer_custom_metabox{

        private $country;
        public function __construct() { 
        $this->country = new class_country_list();

        add_action('add_meta_boxes', [$this,'webcu_event_volunteer_meta_field']); 
        add_action('add_meta_boxes', [$this,'webcu_event_volunteer_gallery_meta_box']); 
        add_action('save_post',  [$this,'webcu_volunteer_social_media_meta']);
        add_action('save_post',  [$this,'save_volenteer_gallery_meta']);
    } 
     
        public function webcu_event_volunteer_meta_field() {
            add_meta_box(
                'webcu_event_volunteer_fields',
                __('Volunteer Information', 'mega-event-manager'),
                [$this, 'webcu_volunteer_metabox_field'],
                'mem_volunteer',
                'normal',
                'high'
            );
        }    

        public function webcu_event_volunteer_gallery_meta_box() {
            add_meta_box(
                'webcu_volun_gallery',
                'Volunteer Photo Gallery',
                [$this, 'volenteer_gallery_meta_box_callback'],
                'mem_volunteer',
                'side',
                'default'
            );
        }

        public function webcu_volunteer_metabox_field($post) { 
            wp_nonce_field('webcu_save_social_media', 'webcu_social_media_nonce');

            $extras = get_post_meta($post->ID, 'webcu_volun_extras', true);
            $extras = is_array($extras) ? $extras : [];

            $video_type   = get_post_meta( $post->ID, '_webcu_volun_video_type', true );
            $youtube_url  = get_post_meta( $post->ID, '_webcu_volun_youtube_url', true );
            $vimeo_url    = get_post_meta( $post->ID, '_webcu_volun_vimeo_url', true );
            $video_id     = get_post_meta( $post->ID, '_webcu_volun_own_video_id', true );
            $video_url    = $video_id ? wp_get_attachment_url( $video_id ) : '';

    
        ?>    

            <div class="webcu_event-location-box">
                
                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Volunteer Owner Name', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_volun_name" name="webcu_volun_name" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_name', true)); ?>"  
                        placeholder="Volunteer Name">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Designation', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_spon_desig" name="webcu_spon_desig" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_desig', true)); ?>"  placeholder="Please write your Designation">
                    </div>              
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Street:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_volun_street" name="webcu_volun_street" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_street', true)); ?>" 
                        placeholder="Enter Street Address">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('City:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_volun_city" name="webcu_volun_city" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_city', true)); ?>" 
                        placeholder="Enter City">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('State:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_volun_state" name="webcu_volun_state" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_state', true)); ?>" 
                        placeholder="Ex: NY">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label> <?php echo esc_html__('Latitude:', 'mega-event-manager') ?> <span class="latelong"> <a href="<?php echo esc_url('https://www.latlong.net'); ?>" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'Click here for the latitude and longitude', 'mega-event-manager' ); ?> </a> </span> </label>
                        <input type="text" id="webcu_volun_late" name="webcu_volun_late" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_late', true)); ?>" placeholder="Type your Latitude">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Longitude:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_volun_long" name="webcu_volun_long" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_long', true)); ?>" placeholder="Type your longitude">
                    </div>
                </div>  

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Postcode:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_volun_postcode" name="webcu_volun_postcode" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_postcode', true)); ?>" 
                        placeholder="Enter Postcode">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Country:', 'mega-event-manager') ?></label>
                        <?php 
                            $saved_country = get_post_meta(get_the_ID(), 'webcu_volun_country', true);
                            $this->country->webcu_volunteer_country_dropdown($saved_country);
                        ?>  

                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Phone Number:', 'mega-event-manager') ?></label>
                        <input type="number" id="webcu_volun_phone" name="webcu_volun_phone" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_phone', true)); ?>" 
                        placeholder="Enter Phone">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Email:', 'mega-event-manager') ?></label>
                        <input type="email" id="webcu_volun_email" name="webcu_volun_email" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_email', true)); ?>" 
                        placeholder="Enter Email Address">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Website Address:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_volun_website" name="webcu_volun_website" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_volun_website', true)); ?>" 
                        placeholder="Enter Website Address">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Comment:', 'mega-event-manager') ?></label>
                        <textarea id="webcu_volun_comment" name="webcu_volun_comment" placeholder="Write a comment" rows="4" cols="50"><?php 
                        echo esc_textarea(get_post_meta($post->ID, 'webcu_volun_comment', true)); 
                        ?></textarea>
                    </div>
                </div>


                <h3><?php echo esc_html__('Social Network', 'mega-event-manager') ?></h3>
                        
                <table class="webcu_tk_extra-table" id="webcu_volun_extraTable">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Social Media', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('URL', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Action', 'mega-event-manager') ?></th>
                        </tr>
                    </thead>
                        
                    <tbody>
                        <?php if (!empty($extras)) : ?>
                            <?php foreach ($extras as $key => $extra) : ?>
                                <tr>
                                    <td>
                                        <select name="webcu_volun_extras[<?php echo esc_attr($key); ?>][volun_social_media]">
                                            <?php
                                            $options = ['facebook', 'linkedin', 'X', 'instagram', 'pinterest', 'youtube', 'tiktok'];
                                            foreach ($options as $option) {
                                                echo '<option value="' . esc_attr($option) . '" ' . 
                                                    selected($extra['volun_social_media'], $option, false) . 
                                                    '>' . ucfirst($option) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </td>

                                    <td>
                                        <input type="url" 
                                            name="webcu_volun_extras[<?php echo esc_attr($key); ?>][url]" 
                                            value="<?php echo esc_attr($extra['url']); ?>" />
                                    </td>

                                    <td>
                                        <button type="button" 
                                            class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_remove_extra_row">
                                            ✖
                                        </button>
                                        <span class="webcu_volunteer-drag"> <span class="dashicons dashicons-fullscreen-alt"></span> 
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>        
                </table>

                <div style="margin-bottom:18px">
                    <button type="button" id="webcu_volun_addExtra" class="webcu_tk_btn webcu_tk_btn-primary">
                        <span class="dashicons dashicons-plus"></span>
                        <?php echo esc_html__('Add Social Media Link', 'mega-event-manager') ?>
                    </button>
                </div>  
                
                        
            <!-- ===== Hero banenr ==== -->                                     
                
            <h3><?php echo esc_html__('Hero Banner', 'mega-event-manager') ?></h3>    
            
                <?php 
                $image_id  = get_post_meta( $post->ID, '_event_volunteer_image_id', true );
                $image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
                ?>    
        
                        <div class="rk-upload-image-wrap">

                            <img id="rk-image-preview"
                                src="<?php echo esc_url( $image_url ); ?>"
                                style="max-width:100%;display:<?php echo $image_url ? 'block' : 'none'; ?>;" />

                            <input type="hidden" name="event_volunteer_image_id" id="event_volunteer_image_id" value="<?php echo esc_attr( $image_id ); ?>" />

                            <p>
                                <button type="button" class="button" id="rk-upload-btn">
                                    <?php echo $image_url ? 'Change Image' : 'Upload Image'; ?>
                                </button>

                                <button type="button" class="button" id="rk-remove-btn" style="display:<?php echo $image_url ? 'inline-block' : 'none'; ?>;">
                                <?php echo esc_html__('Remove', 'mega-event-manager') ?>
                                </button>
                            </p>
                        </div>
                        
                            

                <!--========= Video upload ========== -->  

                <h3><?php echo esc_html__('Video Upload', 'mega-event-manager') ?></h3>    
                                
                <table class="webcu_volun_tk_extra-table">
                <tr> 
                        <th><?php echo esc_html__('Select your video type', 'mega-event-manager') ?></th>
                        <th><?php echo esc_html__('Video link', 'mega-event-manager') ?></th>
                </tr>
                
                <tr>                          
                    <td style="width:250px;"> 

                    <select name="webcu_volun_video_type" id="webcu_volun-video-type">
                            <option value="">Select Type</option>
                            <option value="youtube" <?php selected($video_type, 'youtube'); ?>>YouTube</option>
                            <option value="vimeo" <?php selected($video_type, 'vimeo'); ?>>Vimeo</option>
                            <option value="ownvideo" <?php selected($video_type, 'ownvideo'); ?>>Own Video</option>
                        </select>                        

                    </td>
                    <td>
                        
                        <!-- YouTube URL -->
                        <p id="webcu_volun-youtube-field" style="display:none;">
                            <label><strong><?php echo esc_html__('YouTube URL:', 'mega-event-manager') ?></strong></label>
                            <input type="text" name="webcu_volun_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="widefat">
                        </p>

                        <!-- Vimeo URL -->
                        <p id="webcu_volun-vimeo-field" style="display:none;">
                            <label><strong><?php echo esc_html__('Vimeo URL:', 'mega-event-manager') ?></strong></label>
                            <input type="text" name="webcu_volun_vimeo_url" value="<?php echo esc_attr($vimeo_url); ?>" class="widefat">
                        </p>

                        <!-- Own Video Upload -->
                        <div id="webcu_volun-ownvideo-field" style="display:none;">
                            <label><strong><?php echo esc_html__('Upload Video:', 'mega-event-manager') ?></strong></label><br>
                            <video id="webcu_volun-video-preview" src="<?php echo esc_url($video_url); ?>" style="max-width:100%; display:<?php echo $video_url ? 'block' : 'none'; ?>;" controls></video>
                            <input type="hidden" id="webcu_volun-video-id" name="webcu_volun_own_video_id" value="<?php echo esc_attr($video_id); ?>" />
                            <p>
                                <button type="button" class="button" id="webcu_volun-upload-video-btn">
                                    <?php echo $video_url ? 'Change Video' : 'Upload Video'; ?>
                                </button>

                                <button type="button" class="button" id="webcu_volun-remove-video-btn" style="display:<?php echo $video_url ? 'inline-block' : 'none'; ?>;">
                                    <?php echo esc_html__('Remove', 'mega-event-manager') ?>
                                </button>
                            </p>                                                     
                        </div>                                      
                    </td>
                </tr>             
                </table>
            </div>  

        <?php 
        }

    public function webcu_volunteer_social_media_meta($post_id) {

        if (!isset($_POST['webcu_social_media_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['webcu_social_media_nonce'])), 'webcu_save_social_media')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;


        $fields = [
            'webcu_volun_name', 'webcu_spon_desig', 'webcu_volun_street', 'webcu_volun_city', 'webcu_volun_state',
            'webcu_volun_late', 'webcu_volun_long', 'webcu_volun_postcode', 'webcu_volun_country', 'webcu_volun_phone', 
            'webcu_volun_email', 'webcu_volun_website', 'webcu_volun_comment'
        ];

        foreach ($fields as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }

        if (!empty($_POST['webcu_volun_extras']) && is_array($_POST['webcu_volun_extras'])) {
            $sanitized_extras = [];

            foreach ($_POST['webcu_volun_extras'] as $key => $extra) {
                $sanitized_extras[$key] = [
                    'volun_social_media' => sanitize_text_field($extra['volun_social_media']),
                    'url'                => esc_url_raw($extra['url']),
                ];
            }

            update_post_meta($post_id, 'webcu_volun_extras', $sanitized_extras);

        } else {
            delete_post_meta($post_id, 'webcu_volun_extras');
        }


        /* ===== Hero banenr ==== */
        
        if ( isset($_POST['event_volunteer_image_id']) ) {
        update_post_meta( $post_id, '_event_volunteer_image_id', sanitize_text_field( $_POST['event_volunteer_image_id'] ) );
        }

        /*========= Video upload ==========*/

        update_post_meta($post_id, '_webcu_volun_video_type', sanitize_text_field($_POST['webcu_volun_video_type'] ?? ''));
        update_post_meta($post_id, '_webcu_volun_youtube_url', sanitize_text_field($_POST['webcu_volun_youtube_url'] ?? ''));
        update_post_meta($post_id, '_webcu_volun_vimeo_url', sanitize_text_field($_POST['webcu_volun_vimeo_url'] ?? ''));

        if (isset($_POST['webcu_volun_own_video_id'])) {
            update_post_meta($post_id, '_webcu_volun_own_video_id', sanitize_text_field($_POST['webcu_volun_own_video_id']));
        }
      
    }

    public function volenteer_gallery_meta_box_callback($post) {

        wp_nonce_field('volenteer_save_gallery', 'volenteer_gallery_nonce');

        $gallery_ids = get_post_meta($post->ID, '_volenteer_gallery_ids', true);
        ?>
        <div class="volenteer-gallery-container">
            <div id="volenteer-gallery-images" class="volenteer-gallery-grid">
                <?php
                if (!empty($gallery_ids)) {
                    $ids = explode(',', $gallery_ids);
                    foreach ($ids as $id) {
                        $image = wp_get_attachment_image_src($id, 'thumbnail');
                        if ($image) {
                            echo '<div class="volenteer-gallery-item" data-id="' . esc_attr($id) . '">';
                            echo '<img src="' . esc_url($image[0]) . '" />';
                            echo '<span class="volenteer-remove-image" title="Remove">&times;</span>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>

            <input type="hidden" id="volenteer_gallery_ids" name="volenteer_gallery_ids"
                value="<?php echo esc_attr($gallery_ids); ?>" />

            <button type="button" class="button button-primary" id="volenteer_add_gallery_images">
                <?php echo esc_html__('Add Images', 'mega-event-manager'); ?>
            </button>
        </div>

        <?php
    }

    public function save_volenteer_gallery_meta($post_id) {

        if (
            !isset($_POST['volenteer_gallery_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['volenteer_gallery_nonce'])), 'volenteer_save_gallery')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (!current_user_can('edit_post', $post_id)) return;

        if (isset($_POST['volenteer_gallery_ids'])) {
            update_post_meta( $post_id, '_volenteer_gallery_ids', sanitize_text_field(wp_unslash($_POST['volenteer_gallery_ids'])) );
        }
    }




}
