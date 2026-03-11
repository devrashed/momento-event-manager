<?php
/**
 *
 *  Organizer metabox
 *
 **/
namespace Wpcraft\Metabox;

class class_organizer_meta_box{
    private $country;

    public function __construct() { 

     $this->country = new class_country_list();
      
      add_action('add_meta_boxes', [$this,'wtmem_event_organizer_meta_field']);
      add_action('add_meta_boxes', [$this,'wtmem_event_organizer_gallery_meta_box']); 
      add_action('save_post',  [$this,'wtmem_save_social_media_meta']);
      add_action('save_post',  [$this,'wtmem_save_organizer_gallery']);   
      add_filter('post_type_labels_event_organizer', [$this,'wtmem_rename_organizer_featured_image']);
    } 
     

    public function wtmem_rename_organizer_featured_image($labels){

        $labels->featured_image = 'Logo / Personal Image';
        $labels->set_featured_image = 'Set Personal Image';
        $labels->remove_featured_image = 'Remove Personal Image';
        $labels->use_featured_image = 'Use as Personal Image';
        return $labels;
    }

    public function wtmem_event_organizer_meta_field() {
        add_meta_box(
            'wtmem_event_organizer_fields',
            __('Organizer Information', 'momento-event-manager'),
            [$this, 'wtmem_organzier_metabox_field'],
            'mem_organizer',
            'normal',
            'high'
        );
    }    

    public function wtmem_event_organizer_gallery_meta_box() {
        add_meta_box(
            'wtmem_orga_gallery',
            'Organizer Photo Gallery',
            [$this, 'wtmem_organizer_gallery_callback'],
            'mem_organizer',
            'side',
            'default'
        );
    }
    
    public function wtmem_organzier_metabox_field($post) { 
        wp_nonce_field('wtmem_save_social_media', 'wtmem_social_media_nonce');

        $extras = get_post_meta($post->ID, 'wtmem_orga_extras', true);
        $extras = is_array($extras) ? $extras : [];

        
        $video_type   = get_post_meta( $post->ID, '_wtmem_video_type', true );
        $youtube_url  = get_post_meta( $post->ID, '_wtmem_youtube_url', true );
        $vimeo_url    = get_post_meta( $post->ID, '_wtmem_vimeo_url', true );
        $video_id     = get_post_meta( $post->ID, '_wtmem_own_video_id', true );
        $video_url    = $video_id ? wp_get_attachment_url( $video_id ) : '';

       ?>    

        <div class="wtmem_event-location-box">
            
                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Organizer Owner Name', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_orga_name" name="wtmem_orga_name" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_name', true)); ?>"  placeholder="Please write your organization name">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Designation', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_orga_desig" name="wtmem_orga_desig" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_desig', true)); ?>"  placeholder="Please write your Designation">
                     </div>    
                </div>

                <div class="two-col">

                    <div class="form-group">
                        <label><?php echo esc_html__('Street:', 'momento-event-manager') ?> </label>
                        <input type="text" id="wtmem_orga_street" name="wtmem_orga_street" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_street', true)); ?>" placeholder="Enter Street Address">
                    </div>

                    <div class="form-group">
                        <label> <?php echo esc_html__('City:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_orga_city" name="wtmem_orga_city" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_city', true)); ?>" placeholder="Enter City">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('State:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_orga_state" name="wtmem_orga_state" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_state', true)); ?>" placeholder="Ex: NY">
                    </div>
                </div>

                <div class="two-col">

                    <div class="form-group">
                        <label> <?php echo esc_html__('Latitude:', 'momento-event-manager') ?> <span class="latelong"> <a href="<?php echo esc_url('https://www.latlong.net'); ?>" target="_blank" rel="noopener noreferrer">
                        <?php esc_html_e( 'Click here for the latitude and longitude', 'momento-event-manager' ); ?> </a> </span> </label>
                        <input type="text" id="wtmem_orga_late" name="wtmem_orga_late" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_late', true)); ?>" placeholder="Latitude">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Longitude:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_orga_long" name="wtmem_orga_long" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_long', true)); ?>" placeholder="Longitude">
                    </div>

                </div>    

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Postcode:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_orga_postcocde" name="wtmem_orga_postcocde" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_postcocde', true)); ?>" placeholder="Enter Postcode">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Country:', 'momento-event-manager') ?> </label>
                        <?php 
                            $saved_country = get_post_meta(get_the_ID(), 'wtmem_orga_country', true);
                            $this->country->wtmem_orga_render_country_dropdown($saved_country);
                        ?>   

                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Phone Number:', 'momento-event-manager') ?></label>
                        <input type="number" id="wtmem_orga_phone" name="wtmem_orga_phone" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_phone', true)); ?>" placeholder="Enter Phone">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Email:', 'momento-event-manager') ?> </label>
                        <input type="email" id="wtmem_orga_email" name="wtmem_orga_email" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_email', true)); ?>" placeholder="Enter email address">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Website Address:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_orga_website" name="wtmem_orga_website" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_orga_website', true)); ?>" placeholder="Enter website address">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Special Note:', 'momento-event-manager') ?> </label>
                        <textarea id="wtmem_orga_coment" name="wtmem_orga_coment" placeholder="Special Note" rows="4" cols="50"><?php echo esc_textarea( get_post_meta( $post->ID, 'wtmem_orga_coment', true ) ); ?></textarea>
                    </div>
                </div>
                
            <h3><?php echo esc_html__('Social Network', 'momento-event-manager') ?></h3>
                


            <table class="wtmem_tk_extra-table" id="wtmem_orga_extraTable">
                <thead>
                    <tr>
                    <th><?php echo esc_html__('Social Media', 'momento-event-manager') ?></th>
                    <th><?php echo esc_html__('URL', 'momento-event-manager') ?></th>
                    <th><?php echo esc_html__('Action', 'momento-event-manager') ?></th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php if (!empty($extras)) : ?>
                        <?php foreach ($extras as $key => $extra) : ?>
                            <tr>
                                <td>
                                    <select name="wtmem_orga_extras[<?php echo esc_attr($key); ?>][org_social_media]">
                                        <?php
                                        $options = ['facebook', 'linkedin', 'X', 'instagram', 'pinterest', 'tiktok'];
                                        foreach ($options as $option) {
                                            echo '<option value="' . esc_attr($option) . '" ' . selected($extra['org_social_media'], $option, false) . '>' . ucfirst($option) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td><input type="url" name="wtmem_orga_extras[<?php echo esc_attr($key); ?>][url]" value="<?php echo esc_attr($extra['url']); ?>" /></td>
                                <td>
                                <button type="button" class="wtmem_tk_btn wtmem_tk_btn-danger wtmem_tk_btn-small wtmem_remove_extra_row">✖</button>
                                <span class="wtmem_orga-drag"> <span class="dashicons dashicons-fullscreen-alt"></span> 
                            
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>        
            </table>

            <div style="margin-bottom:18px">
                <button type="button" id="wtmem_orga_addExtra" class="wtmem_tk_btn wtmem_tk_btn-primary"><span class="dashicons dashicons-plus"></span><?php echo esc_html__('Add Social Media Link', 'momento-event-manager') ?></button>
            </div>    
        

         <!-- ===== Hero banenr ==== -->                                     
            
         <h3><?php echo esc_html__('Hero Banner (Portrait Banner)', 'momento-event-manager') ?></h3>    
         
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
                               <?php echo esc_html__('Remove', 'momento-event-manager') ?>
                            </button>
                        </p>

                    </div>

             <br>   
             <br>
                
             
                <!--========= Video upload ========== -->  

        <h3><?php echo esc_html__('Video Upload', 'momento-event-manager') ?></h3>    
                               
            <table class="wtmem_tk_extra-table">
               <tr> 
                    <th><?php echo esc_html__('Select your video type', 'momento-event-manager') ?></th>
                    <th><?php echo esc_html__('Video link', 'momento-event-manager') ?></th>
               </tr>
            
              <tr>                          
                <td style="width:250px;"> 

                   <select name="wtmem_video_type" id="wtmem-video-type">
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
                        <input type="text" name="wtmem_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="widefat">
                    </p>

                    <!-- Vimeo URL -->
                    <p id="wtmem-vimeo-field" style="display:none;">
                        <label><strong><?php echo esc_html__('Vimeo URL:', 'momento-event-manager') ?></strong></label>
                        <input type="text" name="wtmem_vimeo_url" value="<?php echo esc_attr($vimeo_url); ?>" class="widefat">
                    </p>

                    <!-- Own Video Upload -->
                    <div id="wtmem-ownvideo-field" style="display:none;">
                        <label><strong><?php echo esc_html__('Upload Video:', 'momento-event-manager') ?></strong></label><br>
                        <video id="wtmem-video-preview" src="<?php echo esc_url($video_url); ?>" style="max-width:100%; display:<?php echo $video_url ? 'block' : 'none'; ?>;" controls></video>
                        <input type="hidden" id="wtmem-video-id" name="wtmem_own_video_id" value="<?php echo esc_attr($video_id); ?>" />
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
       
        </div>           
    <?php 
    }

    public function wtmem_save_social_media_meta($post_id) {
        // Verify nonce
        if (!isset($_POST['wtmem_social_media_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wtmem_social_media_nonce'])), 'wtmem_save_social_media')) {
            return;
        }

        // Check autosave or lack of permissions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;


        $wodgcfields = [
                    'wtmem_orga_name','wtmem_orga_desig','wtmem_orga_street', 'wtmem_orga_city', 'wtmem_orga_state',
                    'wtmem_orga_late', 'wtmem_orga_long','wtmem_orga_postcocde', 'wtmem_orga_country', 'wtmem_orga_phone', 'wtmem_orga_email',
                    'wtmem_orga_website', 'wtmem_orga_coment'
                ];

        foreach ($wodgcfields as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }

        
        /* === Social Media === */

        if (!empty($_POST['wtmem_orga_extras']) && is_array($_POST['wtmem_orga_extras'])) {
            $sanitized_extras = [];

                foreach ($_POST['wtmem_orga_extras'] as $key => $extra) {
                    $sanitized_extras[$key] = [
                        'org_social_media' => sanitize_text_field($extra['org_social_media']),
                        'url'              => esc_url_raw($extra['url']),
                    ];
                }

                update_post_meta($post_id, 'wtmem_orga_extras', $sanitized_extras);
            } else {
                delete_post_meta($post_id, 'wtmem_orga_extras');
            }


        /* ===== Hero banenr ==== */
        
        if ( isset($_POST['event_sponser_image_id']) ) {
        update_post_meta( $post_id, '_event_sponser_image_id', sanitize_text_field( $_POST['event_sponser_image_id'] ) );
        }


        /*========= Video upload ==========*/

        update_post_meta($post_id, '_wtmem_video_type', sanitize_text_field($_POST['wtmem_video_type'] ?? ''));
        update_post_meta($post_id, '_wtmem_youtube_url', sanitize_text_field($_POST['wtmem_youtube_url'] ?? ''));
        update_post_meta($post_id, '_wtmem_vimeo_url', sanitize_text_field($_POST['wtmem_vimeo_url'] ?? ''));

        if (isset($_POST['wtmem_own_video_id'])) {
            update_post_meta($post_id, '_wtmem_own_video_id', sanitize_text_field($_POST['wtmem_own_video_id']));
        }
        
    }

    public function wtmem_organizer_gallery_callback($post){
        wp_nonce_field('wtmem_save_gallery', 'wtmem_gallery_nonce');

        $gallery_ids = get_post_meta($post->ID, '_wtmem_organizer_gallery', true);
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
            <input type="hidden" id="wtmem_organizer_gallery" name="wtmem_organizer_gallery" value="<?php echo esc_attr($gallery_ids); ?>" />
            <button type="button" class="button button-primary" id="wtmem_organizer_gallery_images">
                <?php echo esc_html__('Add Images', 'momento-event-manager'); ?>
            </button>
        </div>
        <?php
    } 

    public function wtmem_save_organizer_gallery($post_id) {

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

        if (isset($_POST['wtmem_organizer_gallery'])) {
            update_post_meta( $post_id, '_wtmem_organizer_gallery', sanitize_text_field(wp_unslash($_POST['wtmem_organizer_gallery']))
            );
        } 
    } 
}

new class_organizer_meta_box();