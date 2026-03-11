<?php 
namespace Wpcraft\Metabox;
/**
 *
 *  Webcartisan venu/location
 *
 **/

class class_meta_venue_location { 
    private $country;

    public function __construct() {
        $this->country = new Class_country_list();
    }

    public function wtmem_venue_location_field($post) {   
     ?>

        <label class="wtmem_switch">
            <input type="checkbox" id="toggleStatus">
             <span class="wtmem_slider wtmem_round"></span>
        </label> <span id="vistual_on_meeting"><?php echo esc_html__('Enable for virtuial Meeting') ?> </span> 
        <span id="offline_meeting"><?php echo esc_html__('Disbale for physical Onsite meeting') ?> </span>

            <div id="onlineDiv" style="display:none;">

            <div class="wtmem_info-box">
                <p><?php echo esc_html__('If your event is online or virtual, please ensure that this option is enabled.', 'momento-event-manager') ?> </p>
            </div>

                <?php 
                    $content = get_post_meta($post->ID, 'wtmem_custom_editor_content', true);
                    wp_editor($content, 'wtmem_custom_editor_field', [
                        'textarea_name' => 'wtmem_custom_editor_field',
                        'media_buttons' => true,  // Show Add Media button
                        'textarea_rows' => 20,
                        'teeny' => false,         // Full editor
                        'tinymce' => true,        // Enable TinyMCE
                        'quicktags' => true       // Enable text/HTML mode
                    ]);
                ?>
            </div>

            <div id="offlineDiv" style="display:block;">
        
                <div class="wtmem_event-location-box">
                <h3> <?php echo esc_html__('Events Location:', 'momento-event-manager') ?> </h3>

                <!-- Info Box -->
                <div class="wtmem_info-box">
        
                <p><?php echo esc_html__('If you have saved organizer details, please select the "Organizer" option. 
                    Please note that if you select "Organizer" and have not checked the organizer 
                    from the Event Organizer list on the right sidebar, the Event Location section will 
                    not populate on the front end.', 'momento-event-manager') ?> </p>
                </div>

                <!-- Two Column Fields -->
                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Location/Venue:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_ve_location" name="wtmem_ve_location" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_ve_location', true)); ?>"  placeholder="Enter Venue">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('Street:', 'momento-event-manager') ?> </label>
                        <input type="text" id="wtmem_ve_street" name="wtmem_ve_street" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_ve_street', true)); ?>" placeholder="Enter Street">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label> <?php echo esc_html__('City:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_ve_city" name="wtmem_ve_city" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_ve_city', true)); ?>" placeholder="Enter City">
                    </div>
                    <div class="form-group">
                        <label><?php echo esc_html__('State:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_ve_state" name="wtmem_ve_state" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_ve_state', true)); ?>" placeholder="Ex: NY">
                    </div>
                </div>

                <div class="two-col">
                    <div class="form-group">
                        <label><?php echo esc_html__('Postcode:', 'momento-event-manager') ?></label>
                        <input type="text" id="wtmem_ve_postcocde" name="wtmem_ve_postcocde" value="<?php echo esc_attr(get_post_meta($post->ID, 'wtmem_ve_postcocde', true)); ?>" placeholder="Enter Postcode">
                    </div>
                    <div class="form-group">
                        <label> <?php echo esc_html__('Country:', 'momento-event-manager') ?> </label>
                        <?php 
                            $saved_country = get_post_meta(get_the_ID(), 'wtmem_ve_country', true);
                            $this->country->wtmem_event_manaegr_country_dropdown($saved_country) 
                        ?>
                    </div>
                </div>

                <!-- Checkbox -->
                    <div class="wtmem_checkbox">
                        <input type="checkbox" class="wodgc_check" name="wtmem_ve_googleMap" id="wtmem_ve_googleMap" value="1" 
                        <?php checked(get_post_meta($post->ID, 'wtmem_ve_googleMap', true), '1'); ?>>
                        <label for="wtmem_ve_googleMap">Show Google Map</label>
                    </div>                                      
                </div>  
               <?php 
                    /* global $wpdb;
                  $meta_data = $wpdb->get_results("
                        SELECT order_item_id, meta_value 
                        FROM {$wpdb->prefix}woocommerce_order_itemmeta 
                        WHERE meta_key = '_uem_attendees'
                    ");

                    foreach ($meta_data as $data) {
                        $attendees = maybe_unserialize($data->meta_value);
                        
                        if (is_array($attendees)) {
                            foreach ($attendees as $attendee) {
                                echo 'Name: ' . $attendee['name'] . '<br>';
                                echo 'Email: ' . $attendee['email'] . '<br>';
                                echo 'Ticket: ' . $attendee['ticket'] . '<br>';
                       
                            }
                        }
                    } */
                                                            
               ?>                          
            </div>  
        <?php
    }
            
    public function wtmem_save_meta_venue_location($post_id){

            if (isset($_POST['wtmem_custom_editor_field'])) {
                    update_post_meta( $post_id,'wtmem_custom_editor_content',
                        wp_kses_post(wp_unslash($_POST['wtmem_custom_editor_field'])) // Secure save
                    );
            } 
            $wtmem_vanue = [
                'wtmem_ve_location', 'wtmem_ve_street', 'wtmem_ve_city', 'wtmem_ve_state',
                'wtmem_ve_postcocde', 'wtmem_ve_country', 'wtmem_googleMap_Api'
            ];

            foreach ($wtmem_vanue as $field) {
                $value = isset($_POST[$field]) ? sanitize_text_field( wp_unslash($_POST[$field])) : '';
                update_post_meta($post_id, $field, $value);
            }    

            $venue_googlemap = [
                'wtmem_ve_googleMap'
            ];

            foreach ($venue_googlemap as $checkbox) {
                $value = isset($_POST[$checkbox]) ? '1' : '0';
                update_post_meta($post_id, $checkbox, $value);
            } 

    } 
   
} /* end the class */


