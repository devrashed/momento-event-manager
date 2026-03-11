<?php 
namespace Wpcraft\Metabox;
/**
 *
 *  Webcartisan setting section
 *
 **/

class class_meta_settings_sections{

public function __construct() {}

  public function wtmem_settings_sections($post) { 
 ?> 

    <div class="wtmem_setting_panel" style="max-width:980px;">

      <!-- Row 1: Show Available Seat -->
        <div class="wtmem_setting_row">
            <div class="wtmem_setting_col-left">
            <div class="label"><?php echo esc_html__('Show Available Seat?', 'momento-event-manager') ?></div>
            </div>
            <div class="wtmem_setting_col-right">
            <label class="wtmem_setting_toggle" title="Toggle">
                <input type="checkbox" id="wtmem_setting_seat" name="wtmem_setting_seat" value="1"
                            <?php checked(get_post_meta($post->ID, 'wtmem_setting_seat', true), '1');?> >
                <span class="wtmem_setting_slider"><span class="wtmem_setting_knob"></span></span>
            </label>
            </div>
        </div>

        <!-- Row 2: Reset Booking Count -->
        <!-- <div class="wtmem_setting_row">
            <div class="wtmem_setting_col-left">
            <div class="label"><?php ///echo esc_html__('Reset Booking Count :', 'momento-event-manager') ?></div>
            </div>
            <div class="wtmem_setting_col-right">
            <label class="wtmem_setting_toggle">
                <input type="checkbox" id="wtmem_setting_booking" name="wtmem_setting_booking" value="1"
                            <?php //checked(get_post_meta($post->ID, 'wtmem_setting_booking', true), '1');?>>
                <span class="wtmem_setting_slider"><span class="wtmem_setting_knob"></span></span>
            </label>
            <div class="wtmem_setting_right-caption"><?php //echo esc_html__('Current Booking Status :', 'momento-event-manager') ?></div>
            </div>
        </div>

        <div class="wtmem_setting_row" style="background:transparent;">
            <div class="wtmem_setting_col-right" style="width:100%;">
            <div class="wtmem_setting_info">
                <?php /* echo esc_html__('<strong>If you reset this count, all booking information will be removed, including the attendee list.</strong>
                &nbsp;This action is irreversible, so please be sure before you proceed.', 'momento-event-manager') */ ?>
            </div>
            </div>
        </div> -->

        <!-- Row 3: Show Attendee list? -->
        <div class="wtmem_setting_row">
            <div class="wtmem_setting_col-left">
            <div class="label"><?php echo esc_html__('Show Attendee list?', 'momento-event-manager') ?></div>
            </div>
            <div class="wtmem_setting_col-right">
            <label class="wtmem_setting_toggle">
                <input type="checkbox" id="wtmem_setting_attendee" name="wtmem_setting_attendee" value="1"
                            <?php checked(get_post_meta($post->ID, 'wtmem_setting_attendee', true), '1');?> >
                <span class="wtmem_setting_slider"><span class="wtmem_setting_knob"></span></span>
            </label>
            </div>
        </div>

        <!-- Row 4: Enable Attendee information edit -->
        <div class="wtmem_setting_row">
            <div class="wtmem_setting_col-left">
            <div class="label"><?php echo esc_html__('Enable Attendee information edit?', 'momento-event-manager') ?></div>
            <div class="wtmem_setting_sub"><?php echo esc_html__('If enable, Attendee can be edited from the frontend', 'momento-event-manager') ?></div>
            </div>
            <div class="wtmem_setting_col-right">
            <label class="wtmem_setting_toggle">
                <input type="checkbox" id="wtmem_setting_enable_attendee" name="wtmem_setting_enable_attendee" value="1"
                            <?php checked(get_post_meta($post->ID, 'wtmem_setting_enable_attendee', true), '1');?> >
                <span class="wtmem_setting_slider"><span class="wtmem_setting_knob"></span></span>
            </label>
            </div>
        </div>
     </div>
    <?php 
    }
        public function wtmem_save_meta_settings($post_id){

                $wtmem_settings = [
                    'wtmem_setting_seat', 'wtmem_setting_booking', 'wtmem_setting_attendee', 'wtmem_setting_enable_attendee', 'wtmem_setting_toggleStatus'
                ];
                foreach ($wtmem_settings as $checkbox) {
                    $value = isset($_POST[$checkbox]) ? '1' : '0';
                    update_post_meta($post_id, $checkbox, $value);
                } 

                if ( isset( $_POST['wtmem_timeline'] ) ) {
                        $timeline = wp_unslash( $_POST['wtmem_timeline'] );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                         $timeline = array_map( 'sanitize_text_field', (array) $timeline );
                        update_post_meta( $post_id, $this->meta_key, $timeline );
                  }
        }  
}