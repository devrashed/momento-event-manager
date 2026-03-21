<?php
namespace Wpcraft\Metabox;
/**
 *
 *  Sponser custom post type
 *
 **/


class class_mem_volenteers_metabox{

        private $country;
        public function __construct() { 

        $this->country = new class_mem_country_list();

        add_action('add_meta_boxes', [$this,'wtmem_event_volunteer_meta_field']); 
        add_action('add_meta_boxes', [$this,'wtmem_event_volunteer_gallery_meta_box']); 
        add_action('save_post',  [$this,'wtmem_volunteer_social_media_meta']);
        add_action('save_post',  [$this,'save_volenteer_gallery_meta']);

        // Add custom columns
        add_filter('manage_mem_volunteer_posts_columns', [$this, 'wtmem_add_volunteer_columns']);
        add_action('manage_mem_volunteer_posts_custom_column', [$this, 'wtmem_display_volunteer_column_values'], 10, 2);

        // Make columns sortable
        add_filter('manage_edit-mem_volunteer_sortable_columns', [$this, 'wtmem_volunteer_sortable_columns']);
        add_filter('pre_get_posts', [$this, 'wtmem_volunteer_orderby_meta']);
    } 
     
        public function wtmem_event_volunteer_meta_field() {
            add_meta_box(
                'wtmem_event_volunteer_fields',
                __('Volunteer Information', 'momento-event-manager'),
                [$this, 'wtmem_volunteer_metabox_field'],
                'mem_volunteer',
                'normal',
                'high'
            );
        }    

        public function wtmem_event_volunteer_gallery_meta_box() {
            add_meta_box(
                'wtmem_volun_gallery',
                'Volunteer Photo Gallery',
                [$this, 'volenteer_gallery_meta_box_callback'],
                'mem_volunteer',
                'side',
                'default'
            );
        }

        public function wtmem_volunteer_metabox_field($post) { 
            wp_nonce_field('wtmem_save_social_media', 'wtmem_social_media_nonce');

            $extras = get_post_meta($post->ID, 'wtmem_volun_extras', true);
            $extras = is_array($extras) ? $extras : [];

            $video_type   = get_post_meta( $post->ID, '_wtmem_volun_video_type', true );
            $youtube_url  = get_post_meta( $post->ID, '_wtmem_volun_youtube_url', true );
            $vimeo_url    = get_post_meta( $post->ID, '_wtmem_volun_vimeo_url', true );
            $video_id     = get_post_meta( $post->ID, '_wtmem_volun_own_video_id', true );
            $video_url    = $video_id ? wp_get_attachment_url( $video_id ) : '';

        ?>    

            <div class="wtmem_event-location-box">
                
                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Volunteer Owner Name', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_volun_name" name="wtmem_volun_name" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_name', true)); ?>"  
                        placeholder="Volunteer Name">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Designation', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_spon_desig" name="wtmem_spon_desig" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_spon_desig', true)); ?>"  placeholder="Please write your Designation">
                    </div>              
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Street:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_volun_street" name="wtmem_volun_street" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_street', true)); ?>" 
                        placeholder="Enter Street Address">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('City:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_volun_city" name="wtmem_volun_city" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_city', true)); ?>" 
                        placeholder="Enter City">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('State:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_volun_state" name="wtmem_volun_state" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_state', true)); ?>" 
                        placeholder="Ex: NY">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label> <?php echo esc_html__('Latitude:', 'momento-event-manager') ?> <span class="latelong"> <a href="<?php echo esc_url('https://www.latlong.net'); ?>" target="_blank" rel="noopener noreferrer">
                            <?php esc_html_e( 'Click here for the latitude and longitude', 'momento-event-manager' ); ?> </a> </span> </label>
                        <input type="text" id="wtmem_volun_late" name="wtmem_volun_late" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_late', true)); ?>" placeholder="Type your Latitude">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Longitude:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_volun_long" name="wtmem_volun_long" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_long', true)); ?>" placeholder="Type your longitude">
                    </div>
                </div>  

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Postcode:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_volun_postcode" name="wtmem_volun_postcode" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_postcode', true)); ?>" 
                        placeholder="Enter Postcode">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Country:', 'momento-event-manager') ?></label>
                        <?php 
                            $saved_country = get_post_meta(get_the_ID(), 'wtmem_volun_country', true);
                            $this->country->wtmem_volunteer_country_dropdown($saved_country);
                        ?>  

                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Phone Number:', 'momento-event-manager') ?></label>
                        <input type="number" id="wtmem_volun_phone" name="wtmem_volun_phone" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_phone', true)); ?>" 
                        placeholder="Enter Phone">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Email:', 'momento-event-manager') ?></label>
                        <input type="email" id="wtmem_volun_email" name="wtmem_volun_email" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_email', true)); ?>" 
                        placeholder="Enter Email Address">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Website Address:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_volun_website" name="wtmem_volun_website" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_volun_website', true)); ?>" 
                        placeholder="Enter Website Address">
                    </div>

                    <div class="form-group">
                        <label><?php echo esc_html__('Comment:', 'momento-event-manager') ?></label>
                        <textarea id="wtmem_volun_comment" name="wtmem_volun_comment" placeholder="Write a comment" rows="4" cols="50"><?php 
                        echo esc_textarea(get_post_meta($post->ID, 'wtmem_volun_comment', true)); 
                        ?></textarea>
                    </div>
                </div>


                <h3><?php echo esc_html__('Social Network', 'momento-event-manager') ?></h3>
                        
                <table class="wtmem_tk_extra-table" id="wtmem_volun_extraTable">
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
                                        <select name="wtmem_volun_extras[<?php echo esc_attr($key); ?>][volun_social_media]">
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
                                            name="wtmem_volun_extras[<?php echo esc_attr($key); ?>][url]" 
                                            value="<?php echo esc_attr($extra['url']); ?>" />
                                    </td>

                                    <td>
                                        <button type="button" 
                                            class="wtmem_tk_btn wtmem_tk_btn-danger wtmem_tk_btn-small wtmem_remove_extra_row">
                                            ✖
                                        </button>
                                        <span class="wtmem_volunteer-drag"> <span class="dashicons dashicons-fullscreen-alt"></span> 
                                        
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>        
                </table>

                <div style="margin-bottom:18px">
                    <button type="button" id="wtmem_volun_addExtra" class="wtmem_tk_btn wtmem_tk_btn-primary">
                        <span class="dashicons dashicons-plus"></span>
                        <?php echo esc_html__('Add Social Media Link', 'momento-event-manager') ?>
                    </button>
                </div>  
                
                        
            <!-- ===== Hero banenr ==== -->                                     
                
            <h3><?php echo esc_html__('Hero Banner', 'momento-event-manager') ?></h3>    
            
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
                                <?php echo esc_html__('Remove', 'momento-event-manager') ?>
                                </button>
                            </p>
                        </div>
                        
                            

                <!--========= Video upload ========== -->  

                <h3><?php echo esc_html__('Video Upload', 'momento-event-manager') ?></h3>    
                                
                <table class="wtmem_volun_tk_extra-table">
                <tr> 
                        <th><?php echo esc_html__('Select your video type', 'momento-event-manager') ?></th>
                        <th><?php echo esc_html__('Video link', 'momento-event-manager') ?></th>
                </tr>
                
                <tr>                          
                    <td style="width:250px;"> 

                    <select name="wtmem_volun_video_type" id="wtmem_volun-video-type">
                            <option value="">Select Type</option>
                            <option value="youtube" <?php selected($video_type, 'youtube'); ?>>YouTube</option>
                            <option value="vimeo" <?php selected($video_type, 'vimeo'); ?>>Vimeo</option>
                            <option value="ownvideo" <?php selected($video_type, 'ownvideo'); ?>>Own Video</option>
                        </select>                        

                    </td>
                    <td>
                        
                        <!-- YouTube URL -->
                        <p id="wtmem_volun-youtube-field" style="display:none;">
                            <label><strong><?php echo esc_html__('YouTube URL:', 'momento-event-manager') ?></strong></label>
                            <input type="text" name="wtmem_volun_youtube_url" value="<?php echo esc_attr($youtube_url); ?>" class="widefat">
                        </p>

                        <!-- Vimeo URL -->
                        <p id="wtmem_volun-vimeo-field" style="display:none;">
                            <label><strong><?php echo esc_html__('Vimeo URL:', 'momento-event-manager') ?></strong></label>
                            <input type="text" name="wtmem_volun_vimeo_url" value="<?php echo esc_attr($vimeo_url); ?>" class="widefat">
                        </p>

                        <!-- Own Video Upload -->
                        <div id="wtmem_volun-ownvideo-field" style="display:none;">
                            <label><strong><?php echo esc_html__('Upload Video:', 'momento-event-manager') ?></strong></label><br>
                            <video id="wtmem_volun-video-preview" src="<?php echo esc_url($video_url); ?>" style="max-width:100%; display:<?php echo $video_url ? 'block' : 'none'; ?>;" controls></video>
                            <input type="hidden" id="wtmem_volun-video-id" name="wtmem_volun_own_video_id" value="<?php echo esc_attr($video_id); ?>" />
                            <p>
                                <button type="button" class="button" id="wtmem_volun-upload-video-btn">
                                    <?php echo $video_url ? 'Change Video' : 'Upload Video'; ?>
                                </button>

                                <button type="button" class="button" id="wtmem_volun-remove-video-btn" style="display:<?php echo $video_url ? 'inline-block' : 'none'; ?>;">
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

    public function wtmem_volunteer_social_media_meta($post_id) {

        if (!isset($_POST['wtmem_social_media_nonce']) || 
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wtmem_social_media_nonce'])), 'wtmem_save_social_media')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;


        $fields = [
            'wtmem_volun_name', 'wtmem_spon_desig', 'wtmem_volun_street', 'wtmem_volun_city', 'wtmem_volun_state',
            'wtmem_volun_late', 'wtmem_volun_long', 'wtmem_volun_postcode', 'wtmem_volun_country', 'wtmem_volun_phone', 
            'wtmem_volun_email', 'wtmem_volun_website', 'wtmem_volun_comment'
        ];

        foreach ($fields as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }

        if (!empty($_POST['wtmem_volun_extras']) && is_array($_POST['wtmem_volun_extras'])) {
            $sanitized_extras = [];

            foreach ($_POST['wtmem_volun_extras'] as $key => $extra) {
                $sanitized_extras[$key] = [
                    'volun_social_media' => sanitize_text_field($extra['volun_social_media']),
                    'url'                => esc_url_raw($extra['url']),
                ];
            }

            update_post_meta($post_id, 'wtmem_volun_extras', $sanitized_extras);

        } else {
            delete_post_meta($post_id, 'wtmem_volun_extras');
        }


        /* ===== Hero banenr ==== */
        
        if ( isset($_POST['event_volunteer_image_id']) ) {
        update_post_meta( $post_id, '_event_volunteer_image_id', sanitize_text_field( $_POST['event_volunteer_image_id'] ) );
        }

        /*========= Video upload ==========*/

        update_post_meta($post_id, '_wtmem_volun_video_type', sanitize_text_field($_POST['wtmem_volun_video_type'] ?? ''));
        update_post_meta($post_id, '_wtmem_volun_youtube_url', sanitize_text_field($_POST['wtmem_volun_youtube_url'] ?? ''));
        update_post_meta($post_id, '_wtmem_volun_vimeo_url', sanitize_text_field($_POST['wtmem_volun_vimeo_url'] ?? ''));

        if (isset($_POST['wtmem_volun_own_video_id'])) {
            update_post_meta($post_id, '_wtmem_volun_own_video_id', sanitize_text_field($_POST['wtmem_volun_own_video_id']));
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
                <?php echo esc_html__('Add Images', 'momento-event-manager'); ?>
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

    /**
     * Add custom columns to volunteer list
     */
    public function wtmem_add_volunteer_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $label) {
            $new_columns[$key] = $label;
            // Insert custom columns after title
            if ($key === 'title') {
                $new_columns['volunteer_owner_name']  = __('Owner Name',   'momento-event-manager');
                $new_columns['volunteer_designation'] = __('Designation',  'momento-event-manager');
                $new_columns['volunteer_email']       = __('Email',        'momento-event-manager');
                $new_columns['volunteer_phone']       = __('Phone',        'momento-event-manager');
                $new_columns['volunteer_city']        = __('City',         'momento-event-manager');
                $new_columns['volunteer_state']       = __('State',        'momento-event-manager');
            }
        }
        return $new_columns;
    }

    /**
     * Display custom column values
     */
    public function wtmem_display_volunteer_column_values($column, $post_id) {
        switch ($column) {
            case 'volunteer_owner_name':
                $owner_name = get_post_meta($post_id, 'wtmem_volun_name', true);
                echo !empty($owner_name) ? esc_html($owner_name) : '—';
                break;

            case 'volunteer_designation':
                $designation = get_post_meta($post_id, 'wtmem_spon_desig', true);
                echo !empty($designation) ? esc_html($designation) : '—';
                break;

            case 'volunteer_email':
                $email = get_post_meta($post_id, 'wtmem_volun_email', true);
                if (!empty($email)) {
                    echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                } else {
                    echo '—';
                }
                break;

            case 'volunteer_phone':
                $phone = get_post_meta($post_id, 'wtmem_volun_phone', true);
                echo !empty($phone) ? esc_html($phone) : '—';
                break;

            case 'volunteer_city':
                $city = get_post_meta($post_id, 'wtmem_volun_city', true);
                echo !empty($city) ? esc_html($city) : '—';
                break;

            case 'volunteer_state':
                $state = get_post_meta($post_id, 'wtmem_volun_state', true);
                echo !empty($state) ? esc_html($state) : '—';
                break;
        }
    }

    /**
     * Define sortable columns
     */
    public function wtmem_volunteer_sortable_columns($columns) {
        $columns['volunteer_owner_name'] = 'wtmem_volun_name';
        $columns['volunteer_designation'] = 'wtmem_spon_desig';
        $columns['volunteer_email'] = 'wtmem_volun_email';
        $columns['volunteer_phone'] = 'wtmem_volun_phone';
        $columns['volunteer_city'] = 'wtmem_volun_city';
        $columns['volunteer_state'] = 'wtmem_volun_state';

        return $columns;
    }

    /**
     * Handle sorting by meta values
     */
    public function wtmem_volunteer_orderby_meta($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        // Guard: only apply to the mem_volunteer post type
        if ($query->get('post_type') !== 'mem_volunteer') {
            return;
        }

        $orderby = $query->get('orderby');

        $sortable_keys = [
            'wtmem_volun_name',
            'wtmem_spon_desig',
            'wtmem_volun_email',
            'wtmem_volun_phone',
            'wtmem_volun_city',
            'wtmem_volun_state',
        ];

        if (in_array($orderby, $sortable_keys, true)) {
            $query->set('meta_key', $orderby);
            $query->set('orderby', 'meta_value');
        }
    }


}

