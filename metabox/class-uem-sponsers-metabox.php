<?php
/**
 *
 *  Sponser custom metabox
 *
 **/

class class_sponser_custom_metabox{
     private $country;

    public function __construct() { 

      $this->country = new class_country_list();

      //add_action('init', [$this, 'webcu_register_event_sponsers'], 5);           
      add_action('add_meta_boxes', [$this,'webcu_event_sponsers_meta_field']); 
      add_action('add_meta_boxes', [$this,'webcu_Sponsor_gallery_meta_box']);
      add_action('save_post',  [$this,'webcu_sponser_social_media_meta']);
      add_action('save_post',  [$this,'save_sponser_gallery_meta']); 
      add_filter( 'post_type_labels_event_sponsers', [$this,'webcu_rename_event_sponser_featured_image']);
    } 

    public function webcu_rename_event_sponser_featured_image($labels){

        $labels->featured_image = 'Logo / Personal Image';
        $labels->set_featured_image = 'Set Personal Image';
        $labels->remove_featured_image = 'Remove Personal Image';
        $labels->use_featured_image = 'Use as Personal Image';
        return $labels;

    }

    public function webcu_event_sponsers_meta_field() {
        add_meta_box(
            'webcu_event_sponsers_fields',
            __('Sponsers Information', 'mega-event-manager'),
            [$this, 'webcu_sponsers_metabox_field'],
            'mem_sponsor',
            'normal',
            'high'
        );
    }    

    public function webcu_Sponsor_gallery_meta_box() {
        add_meta_box(
            'webcu_sponser_gallery',
            'Sponsor Photo Gallery',
            [$this, 'sponser_gallery_meta_box_callback'],
            'mem_sponsor',
            'side',
            'default'
        );
    }


    public function webcu_sponsers_metabox_field($post) { 

        wp_nonce_field('webcu_save_social_media', 'webcu_social_media_nonce');

        $extras = get_post_meta($post->ID, 'webcu_spon_extras', true);
        $extras = is_array($extras) ? $extras : [];

        $video_type   = get_post_meta( $post->ID, '_webcu_spon_video_type', true );
        $youtube_url  = get_post_meta( $post->ID, '_webcu_spon_youtube_url', true );
        $vimeo_url    = get_post_meta( $post->ID, '_webcu_spon_vimeo_url', true );
        $video_id     = get_post_meta( $post->ID, '_webcu_spon_own_video_id', true );
        $video_url    = $video_id ? wp_get_attachment_url( $video_id ) : '';

        ?>    

        <div class="webcu_event-location-box">
            
                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Sponsor Name', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_spon_name" name="webcu_spon_name" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_name', true)); ?>"  placeholder="Sponsor Name">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Designation:', 'mega-event-manager') ?> </label>
                        <input type="text" id="webcu_spon_desig" name="webcu_spon_desig" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_desig', true)); ?>" placeholder="Enter Street Address">
                    </div>
                </div>

                <div class="two-col">

                    <div class="form-group">
                        <label><?php echo esc_html__('Street:', 'mega-event-manager') ?> </label>
                        <input type="text" id="webcu_spon_street" name="webcu_spon_street" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_street', true)); ?>" placeholder="Enter Street Address">
                    </div>    

                    <div class="form-group">
                        <label> <?php echo esc_html__('City:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_spon_city" name="webcu_spon_city" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_city', true)); ?>" placeholder="Enter City">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('State:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_spon_state" name="webcu_spon_state" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_state', true)); ?>" placeholder="Ex: NY">
                    </div>
                </div>

                <div class="two-col">

                    <div class="form-group">
                        <label> <?php echo esc_html__('Latitude:', 'mega-event-manager') ?> <span class="latelong"> <a href="<?php echo esc_url('https://www.latlong.net'); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Click here for the latitude and longitude', 'mega-event-manager' ); ?> </a> </span> </label> 
                        <input type="number" id="webcu_sponser_late" name="webcu_sponser_late" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_sponser_late', true)); ?>" placeholder="Enter City">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Longitude:', 'mega-event-manager') ?></label>
                        <input type="number" id="webcu_sponser_long" name="webcu_sponser_long" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_sponser_long', true)); ?>" placeholder="Ex: NY">
                    </div>
                </div>    

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Postcode:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_spon_postcode" name="webcu_spon_postcode" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_postcode', true)); ?>" placeholder="Enter Postcode">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Country:', 'mega-event-manager') ?> </label>
                        <?php 
                           $saved_country = get_post_meta(get_the_ID(), 'webcu_spon_country', true);
                           $this->country->webcu_sponser_country_dropdown($saved_country);
                        ?> 
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Phone Number:', 'mega-event-manager') ?></label>
                        <input type="number" id="webcu_spon_phone" name="webcu_spon_phone" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_phone', true)); ?>" placeholder="Enter Phone">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Email:', 'mega-event-manager') ?> </label>
                        <input type="email" id="webcu_spon_email" name="webcu_spon_email" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_email', true)); ?>" placeholder="Enter email address">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Website Address:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_spon_website" name="webcu_spon_website" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_spon_website', true)); ?>" placeholder="Enter website address">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Comment:', 'mega-event-manager') ?> </label>
                        <textarea id="webcu_spon_comment" name="webcu_spon_comment" placeholder="Write a comment" rows="4" cols="50"><?php echo esc_textarea( get_post_meta( $post->ID, 'webcu_spon_comment', true ) ); ?></textarea>
                    </div>
                </div>
                
            <h3><?php echo esc_html__('Social Network', 'mega-event-manager') ?></h3>
                
            <table class="webcu_tk_extra-table" id="webcu_spon_extraTable">
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
                                    <select name="webcu_spon_extras[<?php echo esc_attr($key); ?>][spon_social_media]">
                                        <?php
                                        $options = ['facebook', 'linkedin', 'X', 'instagram', 'pinterest', 'tiktok'];
                                        foreach ($options as $option) {
                                            echo '<option value="' . esc_attr($option) . '" ' . selected($extra['spon_social_media'], $option, false) . '>' . ucfirst($option) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><input type="url" name="webcu_spon_extras[<?php echo esc_attr($key); ?>][url]" value="<?php echo esc_attr($extra['url']); ?>" /></td>
                                <td><button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_remove_extra_row">✖</button>
                                    <span class="webcu_sponser-drag"> <span class="dashicons dashicons-fullscreen-alt"></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>        

            </table>

            <div style="margin-bottom:18px">
              <button type="button" id="webcu_spon_addExtra" class="webcu_tk_btn webcu_tk_btn-primary"><span class="dashicons dashicons-plus"></span><?php echo esc_html__('Add Social Media Link', 'mega-event-manager') ?></button>
            </div>   
            
                       
         <!-- ===== Hero banenr ==== -->                                     
            
         <h3><?php echo esc_html__('Hero Banner (Portrait Banner)', 'mega-event-manager') ?></h3>    
         
         <?php 
               $image_id  = get_post_meta( $post->ID, '_event_sponser_image_id', true );
               $image_url = $image_id ? wp_get_attachment_url( $image_id ) : '';
         ?>    
       
                    <div class="rk-upload-image-wrap">

                        <img id="rk-image-preview"
                            src="<?php echo esc_url( $image_url ); ?>"
                            style="max-width:100%;display:<?php echo $image_url ? 'block' : 'none'; ?>;" />

                        <input type="hidden" name="event_sponser_image_id" id="event_sponser_image_id" value="<?php echo esc_attr( $image_id ); ?>" />

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
                               
            <table class="webcu_spon_tk_extra-table">
               <tr> 
                    <th><?php echo esc_html__('Select your video type', 'mega-event-manager') ?></th>
                    <th><?php echo esc_html__('Video link', 'mega-event-manager') ?></th>
               </tr>
            
              <tr>                          
                <td style="width:250px;"> 

                   <select name="webcu_spon_video_type" id="webcu_spon-video-type">
                        <option value="">Select Type</option>
                        <option value="youtube" <?php selected($video_type, 'youtube'); ?>>YouTube</option>
                        <option value="vimeo" <?php selected($video_type, 'vimeo'); ?>>Vimeo</option>
                        <option value="ownvideo" <?php selected($video_type, 'ownvideo'); ?>>Own Video</option>
                    </select>                        

                </td>
                  <td>
                    
                    <!-- YouTube URL -->
                    <p id="webcu_spon-youtube-field" style="display:none;">
                        <label><strong><?php echo esc_html__('YouTube URL:', 'mega-event-manager') ?></strong></label>
                        <input type="text" name="webcu_spon_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="widefat">
                    </p>

                    <!-- Vimeo URL -->
                    <p id="webcu_spon-vimeo-field" style="display:none;">
                        <label><strong><?php echo esc_html__('Vimeo URL:', 'mega-event-manager') ?></strong></label>
                        <input type="text" name="webcu_spon_vimeo_url" value="<?php echo esc_attr($vimeo_url); ?>" class="widefat">
                    </p>

                    <!-- Own Video Upload -->
                    <div id="webcu_spon-ownvideo-field" style="display:none;">
                        <label><strong><?php echo esc_html__('Upload Video:', 'mega-event-manager') ?></strong></label><br>
                        <video id="webcu_spon-video-preview" src="<?php echo esc_url($video_url); ?>" style="max-width:100%; display:<?php echo $video_url ? 'block' : 'none'; ?>;" controls></video>
                        <input type="hidden" id="webcu_spon-video-id" name="webcu_spon_own_video_id" value="<?php echo esc_attr($video_id); ?>" />
                        <p>
                            <button type="button" class="button" id="webcu_spon-upload-video-btn">
                                <?php echo $video_url ? 'Change Video' : 'Upload Video'; ?>
                            </button>

                            <button type="button" class="button" id="webcu_spon-remove-video-btn" style="display:<?php echo $video_url ? 'inline-block' : 'none'; ?>;">
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

    public function webcu_sponser_social_media_meta($post_id) {
        // Verify nonce
        if (!isset($_POST['webcu_social_media_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['webcu_social_media_nonce'])), 'webcu_save_social_media')) {
            return;
        }

        // Check autosave or lack of permissions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;


        $fields = [
                    'webcu_spon_name', 'webcu_spon_street', 'webcu_spon_street', 'webcu_sponser_late', 'webcu_sponser_long', 'webcu_spon_city', 'webcu_spon_state',
                    'webcu_spon_postcode', 'webcu_spon_country', 'webcu_spon_phone', 'webcu_spon_email',
                    'webcu_spon_website', 'webcu_spon_comment'
                ];

        foreach ($fields as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }


        if (!empty($_POST['webcu_spon_extras']) && is_array($_POST['webcu_spon_extras'])) {
            $sanitized_extras = [];

            foreach ($_POST['webcu_spon_extras'] as $key => $extra) {
                $sanitized_extras[$key] = [
                    'spon_social_media' => sanitize_text_field($extra['spon_social_media']),
                    'url'              => esc_url_raw($extra['url']),
                ];
            }

            update_post_meta($post_id, 'webcu_spon_extras', $sanitized_extras);
        } else {
            delete_post_meta($post_id, 'webcu_spon_extras');
        }

        /* ===== Hero banenr ==== */
        
        if ( isset($_POST['event_sponser_image_id']) ) {
        update_post_meta( $post_id, '_event_sponser_image_id', sanitize_text_field( $_POST['event_sponser_image_id'] ) );
        }


        /*========= Video upload ==========*/

        update_post_meta($post_id, '_webcu_spon_video_type', sanitize_text_field($_POST['webcu_spon_video_type'] ?? ''));
        update_post_meta($post_id, '_webcu_spon_youtube_url', sanitize_text_field($_POST['webcu_spon_youtube_url'] ?? ''));
        update_post_meta($post_id, '_webcu_spon_vimeo_url', sanitize_text_field($_POST['webcu_spon_vimeo_url'] ?? ''));

        if (isset($_POST['webcu_spon_own_video_id'])) {
            update_post_meta($post_id, '_webcu_spon_own_video_id', sanitize_text_field($_POST['webcu_spon_own_video_id']));
        }

    }

   
    public function sponser_gallery_meta_box_callback($post) {
        wp_nonce_field('sponser_save_gallery', 'sponser_gallery_nonce');

        $gallery_ids = get_post_meta($post->ID, '_sponser_gallery_ids', true);
        ?>
        <div class="sponser-gallery-container">
            <div id="sponser-gallery-images" class="sponser-gallery-grid">
                <?php
                if (!empty($gallery_ids)) {
                    $ids = explode(',', $gallery_ids);
                    foreach ($ids as $id) {
                        $image = wp_get_attachment_image_src($id, 'thumbnail');
                        if ($image) {
                            echo '<div class="sponser-gallery-item" data-id="' . esc_attr($id) . '">';
                            echo '<img src="' . esc_url($image[0]) . '" />';
                            echo '<span class="sponser-remove-image" title="Remove">&times;</span>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>

            <input type="hidden" id="sponser_gallery_ids" name="sponser_gallery_ids"
                value="<?php echo esc_attr($gallery_ids); ?>" />

            <button type="button" class="button button-primary" id="sponser_add_gallery_images">
                <?php echo esc_html__('Add Images', 'mega-event-manager'); ?>
            </button>
        </div>


    <?php
    }
    

    public function save_sponser_gallery_meta($post_id) {

        if (!isset($_POST['sponser_gallery_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['sponser_gallery_nonce'])), 'sponser_save_gallery')
        ) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['sponser_gallery_ids'])) {
            update_post_meta(
                $post_id,
                '_sponser_gallery_ids',
                sanitize_text_field(wp_unslash($_POST['sponser_gallery_ids']))
            );
        }
    }    
}

