<?php
/**
 *
 *  Organizer metabox
 *
 **/

class class_organizer_meta_box{
    private $country;

    public function __construct() { 

        $this->country = new class_country_list();
      
      add_action('add_meta_boxes', [$this,'webcu_event_organizer_meta_field']);
      add_action('add_meta_boxes', [$this,'webcu_event_organizer_gallery_meta_box']); 
      add_action('save_post',  [$this,'webcu_save_social_media_meta']);
      add_action('save_post',  [$this,'webcu_save_organizer_gallery']);   
      add_filter('post_type_labels_event_organizer', [$this,'webcu_rename_organizer_featured_image']);
    } 
     

    public function webcu_rename_organizer_featured_image($labels){

        $labels->featured_image = 'Logo / Personal Image';
        $labels->set_featured_image = 'Set Personal Image';
        $labels->remove_featured_image = 'Remove Personal Image';
        $labels->use_featured_image = 'Use as Personal Image';
        return $labels;
    }

    public function webcu_event_organizer_meta_field() {
        add_meta_box(
            'webcu_event_organizer_fields',
            __('Organizer Information', 'mega-event-manager'),
            [$this, 'webcu_organzier_metabox_field'],
            'mem_organizer',
            'normal',
            'high'
        );
    }    

    public function webcu_event_organizer_gallery_meta_box() {
        add_meta_box(
            'webcu_orga_gallery',
            'Organizer Photo Gallery',
            [$this, 'webcu_organizer_gallery_callback'],
            'mem_organizer',
            'side',
            'default'
        );
    }
    
    public function webcu_organzier_metabox_field($post) { 
        wp_nonce_field('webcu_save_social_media', 'webcu_social_media_nonce');

        $extras = get_post_meta($post->ID, 'webcu_orga_extras', true);
        $extras = is_array($extras) ? $extras : [];

        
        $video_type   = get_post_meta( $post->ID, '_webcu_video_type', true );
        $youtube_url  = get_post_meta( $post->ID, '_webcu_youtube_url', true );
        $vimeo_url    = get_post_meta( $post->ID, '_webcu_vimeo_url', true );
        $video_id     = get_post_meta( $post->ID, '_webcu_own_video_id', true );
        $video_url    = $video_id ? wp_get_attachment_url( $video_id ) : '';

       ?>    

        <div class="webcu_event-location-box">
            
                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Company Name', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_orga_name" name="webcu_orga_name" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_name', true)); ?>"  placeholder="Please write your organization name">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Designation', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_orga_desig" name="webcu_orga_desig" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_desig', true)); ?>"  placeholder="Please write your Designation">
                     </div>    
                </div>

                <div class="two-col">

                    <div class="form-group">
                        <label><?php echo esc_html__('Street:', 'mega-event-manager') ?> </label>
                        <input type="text" id="webcu_orga_street" name="webcu_orga_street" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_street', true)); ?>" placeholder="Enter Street Address">
                    </div>

                    <div class="form-group">
                        <label> <?php echo esc_html__('City:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_orga_city" name="webcu_orga_city" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_city', true)); ?>" placeholder="Enter City">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('State:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_orga_state" name="webcu_orga_state" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_state', true)); ?>" placeholder="Ex: NY">
                    </div>
                </div>

                <div class="two-col">

                    <div class="form-group">
                        <label> <?php echo esc_html__('Latitude:', 'mega-event-manager') ?> <span class="latelong"> <a href="<?php echo esc_url('https://www.latlong.net'); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Click here for the latitude and longitude', 'mega-event-manager' ); ?> </a> </span> </label>
                        <input type="number" id="webcu_orga_late" name="webcu_orga_late" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_late', true)); ?>" placeholder="Enter City">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Longitude:', 'mega-event-manager') ?></label>
                        <input type="number" id="webcu_orga_long" name="webcu_orga_long" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_long', true)); ?>" placeholder="Ex: NY">
                    </div>

                </div>    

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Postcode:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_orga_postcocde" name="webcu_orga_postcocde" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_postcocde', true)); ?>" placeholder="Enter Postcode">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Country:', 'mega-event-manager') ?> </label>
                        <?php 
                            $saved_country = get_post_meta(get_the_ID(), 'webcu_orga_country', true);
                            $this->country->webcu_orga_render_country_dropdown($saved_country);
                        ?>   

                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Phone Number:', 'mega-event-manager') ?></label>
                        <input type="number" id="webcu_orga_phone" name="webcu_orga_phone" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_phone', true)); ?>" placeholder="Enter Phone">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Email:', 'mega-event-manager') ?> </label>
                        <input type="email" id="webcu_orga_email" name="webcu_orga_email" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_email', true)); ?>" placeholder="Enter email address">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Website Address:', 'mega-event-manager') ?></label>
                        <input type="text" id="webcu_orga_website" name="webcu_orga_website" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_orga_website', true)); ?>" placeholder="Enter website address">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Special Note:', 'mega-event-manager') ?> </label>
                        <textarea id="webcu_orga_coment" name="webcu_orga_coment" placeholder="Special Note" rows="4" cols="50"><?php echo esc_textarea( get_post_meta( $post->ID, 'webcu_orga_coment', true ) ); ?></textarea>
                    </div>
                </div>
                
            <h3><?php echo esc_html__('Social Network', 'mega-event-manager') ?></h3>
                


            <table class="webcu_tk_extra-table" id="webcu_orga_extraTable">
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
                                    <select name="webcu_orga_extras[<?php echo esc_attr($key); ?>][org_social_media]">
                                        <?php
                                        $options = ['facebook', 'linkedin', 'X', 'instagram', 'pinterest', 'tiktok'];
                                        foreach ($options as $option) {
                                            echo '<option value="' . esc_attr($option) . '" ' . selected($extra['org_social_media'], $option, false) . '>' . ucfirst($option) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><input type="url" name="webcu_orga_extras[<?php echo esc_attr($key); ?>][url]" value="<?php echo esc_attr($extra['url']); ?>" /></td>
                                <td>
                                <button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_remove_extra_row">✖</button>
                                <span class="webcu_orga-drag"> <span class="dashicons dashicons-fullscreen-alt"></span> 
                            
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>        
            </table>

            <div style="margin-bottom:18px">
                <button type="button" id="webcu_orga_addExtra" class="webcu_tk_btn webcu_tk_btn-primary"><span class="dashicons dashicons-plus"></span><?php echo esc_html__('Add Social Media Link', 'mega-event-manager') ?></button>
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

             <br>   
             <br>
                
             
                <!--========= Video upload ========== -->  

        <h3><?php echo esc_html__('Video Upload', 'mega-event-manager') ?></h3>    
                               
            <table class="webcu_tk_extra-table">
               <tr> 
                    <th><?php echo esc_html__('Select your video type', 'mega-event-manager') ?></th>
                    <th><?php echo esc_html__('Video link', 'mega-event-manager') ?></th>
               </tr>
            
              <tr>                          
                <td style="width:250px;"> 

                   <select name="webcu_video_type" id="webcu-video-type">
                        <option value="">Select Type</option>
                        <option value="youtube" <?php selected($video_type, 'youtube'); ?>>YouTube</option>
                        <option value="vimeo" <?php selected($video_type, 'vimeo'); ?>>Vimeo</option>
                        <option value="ownvideo" <?php selected($video_type, 'ownvideo'); ?>>Own Video</option>
                    </select>                        

                </td>
                  <td>
                    
                    <!-- YouTube URL -->
                    <p id="webcu-youtube-field" style="display:none;">
                        <label><strong><?php echo esc_html__('YouTube URL:', 'mega-event-manager') ?></strong></label>
                        <input type="text" name="webcu_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="widefat">
                    </p>

                    <!-- Vimeo URL -->
                    <p id="webcu-vimeo-field" style="display:none;">
                        <label><strong><?php echo esc_html__('Vimeo URL:', 'mega-event-manager') ?></strong></label>
                        <input type="text" name="webcu_vimeo_url" value="<?php echo esc_attr($vimeo_url); ?>" class="widefat">
                    </p>

                    <!-- Own Video Upload -->
                    <div id="webcu-ownvideo-field" style="display:none;">
                        <label><strong><?php echo esc_html__('Upload Video:', 'mega-event-manager') ?></strong></label><br>
                        <video id="webcu-video-preview" src="<?php echo esc_url($video_url); ?>" style="max-width:100%; display:<?php echo $video_url ? 'block' : 'none'; ?>;" controls></video>
                        <input type="hidden" id="webcu-video-id" name="webcu_own_video_id" value="<?php echo esc_attr($video_id); ?>" />
                        <p>
                            <button type="button" class="button" id="webcu-upload-video-btn">
                                <?php echo $video_url ? 'Change Video' : 'Upload Video'; ?>
                            </button>

                            <button type="button" class="button" id="webcu-remove-video-btn" style="display:<?php echo $video_url ? 'inline-block' : 'none'; ?>;">
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

    public function webcu_save_social_media_meta($post_id) {
        // Verify nonce
        if (!isset($_POST['webcu_social_media_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['webcu_social_media_nonce'])), 'webcu_save_social_media')) {
            return;
        }

        // Check autosave or lack of permissions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;


        $wodgcfields = [
                    'webcu_orga_name','webcu_orga_desig','webcu_orga_street', 'webcu_orga_city', 'webcu_orga_state',
                    'webcu_orga_late', 'webcu_orga_long','webcu_orga_postcocde', 'webcu_orga_country', 'webcu_orga_phone', 'webcu_orga_email',
                    'webcu_orga_website', 'webcu_orga_coment'
                ];

        foreach ($wodgcfields as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }

        
        /* === Social Media === */

        if (!empty($_POST['webcu_orga_extras']) && is_array($_POST['webcu_orga_extras'])) {
            $sanitized_extras = [];

                foreach ($_POST['webcu_orga_extras'] as $key => $extra) {
                    $sanitized_extras[$key] = [
                        'org_social_media' => sanitize_text_field($extra['org_social_media']),
                        'url'              => esc_url_raw($extra['url']),
                    ];
                }

                update_post_meta($post_id, 'webcu_orga_extras', $sanitized_extras);
            } else {
                delete_post_meta($post_id, 'webcu_orga_extras');
            }


        /* ===== Hero banenr ==== */
        
        if ( isset($_POST['event_sponser_image_id']) ) {
        update_post_meta( $post_id, '_event_sponser_image_id', sanitize_text_field( $_POST['event_sponser_image_id'] ) );
        }


        /*========= Video upload ==========*/

        update_post_meta($post_id, '_webcu_video_type', sanitize_text_field($_POST['webcu_video_type'] ?? ''));
        update_post_meta($post_id, '_webcu_youtube_url', sanitize_text_field($_POST['webcu_youtube_url'] ?? ''));
        update_post_meta($post_id, '_webcu_vimeo_url', sanitize_text_field($_POST['webcu_vimeo_url'] ?? ''));

        if (isset($_POST['webcu_own_video_id'])) {
            update_post_meta($post_id, '_webcu_own_video_id', sanitize_text_field($_POST['webcu_own_video_id']));
        }
        
    }

    public function webcu_organizer_gallery_callback($post){
        wp_nonce_field('webcu_save_gallery', 'webcu_gallery_nonce');

        $gallery_ids = get_post_meta($post->ID, '_webcu_organizer_gallery', true);
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
            <input type="hidden" id="webcu_organizer_gallery" name="webcu_organizer_gallery" value="<?php echo esc_attr($gallery_ids); ?>" />
            <button type="button" class="button button-primary" id="webcu_organizer_gallery_images">
                <?php echo esc_html__('Add Images', 'mega-event-manager'); ?>
            </button>
        </div>
        <?php
    } 

    public function webcu_save_organizer_gallery($post_id) {

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

        if (isset($_POST['webcu_organizer_gallery'])) {
            update_post_meta( $post_id, '_webcu_organizer_gallery', sanitize_text_field(wp_unslash($_POST['webcu_organizer_gallery']))
            );
        } 
    }
 
}