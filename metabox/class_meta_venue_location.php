<?php 
/**
 *
 *  Webcartisan venu/location
 *
 **/

class Class_meta_venue_location { 
    private $country;

    public function __construct() {
        $this->country = new Class_country_list();
    }

    public function webcu_venue_location_field($post) {   
     ?>

        <label class="webcu_switch">
            <input type="checkbox" id="toggleStatus">
             <span class="webcu_slider webcu_round"></span>
        </label> <span id="vistual_on_meeting"><?php echo esc_html__('Enable for virtuial Meeting') ?> </span> 
        <span id="offline_meeting"><?php echo esc_html__('Disbale for physical Onsite meeting') ?> </span>

            <div id="onlineDiv" style="display:none;">

            <div class="webcu_info-box">
                <p><?php echo esc_html__('If your event is online or virtual, please ensure that this option is enabled.', 'mega-events-manager') ?> </p>
            </div>

                <?php 
                    $content = get_post_meta($post->ID, 'webcu_custom_editor_content', true);
                    wp_editor($content, 'webcu_custom_editor_field', [
                        'textarea_name' => 'webcu_custom_editor_field',
                        'media_buttons' => true,  // Show Add Media button
                        'textarea_rows' => 20,
                        'teeny' => false,         // Full editor
                        'tinymce' => true,        // Enable TinyMCE
                        'quicktags' => true       // Enable text/HTML mode
                    ]);
                ?>
            </div>

            <div id="offlineDiv" style="display:block;">
        
                <div class="webcu_event-location-box">
                <h3> <?php echo esc_html__('Events Location:', 'mega-events-manager') ?> </h3>

                <!-- Info Box -->
                <div class="webcu_info-box">
        
                <p><?php echo esc_html__('If you have saved organizer details, please select the "Organizer" option. 
                    Please note that if you select "Organizer" and have not checked the organizer 
                    from the Event Organizer list on the right sidebar, the Event Location section will 
                    not populate on the front end.', 'mega-events-manager') ?> </p>
                </div>

                <!-- Two Column Fields -->
                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Location/Venue:', 'mega-events-manager') ?></label>
                        <input type="text" id="webcu_ve_location" name="webcu_ve_location" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_ve_location', true)); ?>"  placeholder="Enter Venue">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Street:', 'mega-events-manager') ?> </label>
                        <input type="text" id="webcu_ve_street" name="webcu_ve_street" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_ve_street', true)); ?>" placeholder="Enter Street">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label> <?php echo esc_html__('City:', 'mega-events-manager') ?></label>
                        <input type="text" id="webcu_ve_city" name="webcu_ve_city" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_ve_city', true)); ?>" placeholder="Enter City">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('State:', 'mega-events-manager') ?></label>
                        <input type="text" id="webcu_ve_state" name="webcu_ve_state" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_ve_state', true)); ?>" placeholder="Ex: NY">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Postcode:', 'mega-events-manager') ?></label>
                        <input type="text" id="webcu_ve_postcocde" name="webcu_ve_postcocde" value="<?php echo esc_attr(get_post_meta($post->ID, 'webcu_ve_postcocde', true)); ?>" placeholder="Enter Postcode">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Country:', 'mega-events-manager') ?> </label>
                        <?php 
                            $saved_country = get_post_meta(get_the_ID(), 'webcu_ve_country', true);
                            $this->country->webcu_event_manaegr_country_dropdown($saved_country) 
                        ?>
                    </div>
                </div>

                <!-- Checkbox -->
                    <div class="webcu_checkbox">
                        <input type="checkbox" class="wodgc_check" name="webcu_ve_googleMap" id="webcu_ve_googleMap" value="1" 
                        <?php checked(get_post_meta($post->ID, 'webcu_ve_googleMap', true), '1'); ?>>
                        <label for="webcu_ve_googleMap">Show Google Map</label>
                    </div>                                      
                </div>       
            </div>  
        <?php
    }
            
    public function webcu_save_meta_venue_location($post_id){

            if (isset($_POST['webcu_custom_editor_field'])) {
                    update_post_meta( $post_id,'webcu_custom_editor_content',
                        wp_kses_post(wp_unslash($_POST['webcu_custom_editor_field'])) // Secure save
                    );
            } 
            $webcu_vanue = [
                'webcu_ve_location', 'webcu_ve_street', 'webcu_ve_city', 'webcu_ve_state',
                'webcu_ve_postcocde', 'webcu_ve_country', 'webcu_googleMap_Api'
            ];

            foreach ($webcu_vanue as $field) {
                $value = isset($_POST[$field]) ? sanitize_text_field( wp_unslash($_POST[$field])) : '';
                update_post_meta($post_id, $field, $value);
            }    

            $venue_googlemap = [
                'webcu_ve_googleMap'
            ];

            foreach ($venue_googlemap as $checkbox) {
                $value = isset($_POST[$checkbox]) ? '1' : '0';
                update_post_meta($post_id, $checkbox, $value);
            } 

    } 
   
} /* end the class */


