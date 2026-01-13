<?php 
/**
 *
 *  Webcartisan setting section
 *
 **/

class Class_meta_settings_sections{

public function __construct() {}

  public function webcu_settings_sections($post) { 
 ?> 

    <div class="webcu_setting_panel" style="max-width:980px;">

      <!-- Row 1: Show Available Seat -->
        <div class="webcu_setting_row">
            <div class="webcu_setting_col-left">
            <div class="label"><?php echo esc_html__('Show Available Seat?', 'mega-events-manager') ?></div>
            </div>
            <div class="webcu_setting_col-right">
            <label class="webcu_setting_toggle" title="Toggle">
                <input type="checkbox" id="webcu_setting_seat" name="webcu_setting_seat" value="1"
                            <?php checked(get_post_meta($post->ID, 'webcu_setting_seat', true), '1');?> >
                <span class="webcu_setting_slider"><span class="webcu_setting_knob"></span></span>
            </label>
            </div>
        </div>

        <!-- Row 2: Reset Booking Count -->
        <!-- <div class="webcu_setting_row">
            <div class="webcu_setting_col-left">
            <div class="label"><?php ///echo esc_html__('Reset Booking Count :', 'mega-events-manager') ?></div>
            </div>
            <div class="webcu_setting_col-right">
            <label class="webcu_setting_toggle">
                <input type="checkbox" id="webcu_setting_booking" name="webcu_setting_booking" value="1"
                            <?php //checked(get_post_meta($post->ID, 'webcu_setting_booking', true), '1');?>>
                <span class="webcu_setting_slider"><span class="webcu_setting_knob"></span></span>
            </label>
            <div class="webcu_setting_right-caption"><?php //echo esc_html__('Current Booking Status :', 'mega-events-manager') ?></div>
            </div>
        </div>

        <div class="webcu_setting_row" style="background:transparent;">
            <div class="webcu_setting_col-right" style="width:100%;">
            <div class="webcu_setting_info">
                <?php /* echo esc_html__('<strong>If you reset this count, all booking information will be removed, including the attendee list.</strong>
                &nbsp;This action is irreversible, so please be sure before you proceed.', 'mega-events-manager') */ ?>
            </div>
            </div>
        </div> -->

        <!-- Row 3: Show Attendee list? -->
        <div class="webcu_setting_row">
            <div class="webcu_setting_col-left">
            <div class="label"><?php echo esc_html__('Show Attendee list?', 'mega-events-manager') ?></div>
            </div>
            <div class="webcu_setting_col-right">
            <label class="webcu_setting_toggle">
                <input type="checkbox" id="webcu_setting_attendee" name="webcu_setting_attendee" value="1"
                            <?php checked(get_post_meta($post->ID, 'webcu_setting_attendee', true), '1');?> >
                <span class="webcu_setting_slider"><span class="webcu_setting_knob"></span></span>
            </label>
            </div>
        </div>

        <!-- Row 4: Enable Attendee information edit -->
        <div class="webcu_setting_row">
            <div class="webcu_setting_col-left">
            <div class="label"><?php echo esc_html__('Enable Attendee information edit?', 'mega-events-manager') ?></div>
            <div class="webcu_setting_sub"><?php echo esc_html__('If enable, Attendee can be edited from the frontend', 'mega-events-manager') ?></div>
            </div>
            <div class="webcu_setting_col-right">
            <label class="webcu_setting_toggle">
                <input type="checkbox" id="webcu_setting_enable_attendee" name="webcu_setting_enable_attendee" value="1"
                            <?php checked(get_post_meta($post->ID, 'webcu_setting_enable_attendee', true), '1');?> >
                <span class="webcu_setting_slider"><span class="webcu_setting_knob"></span></span>
            </label>
            </div>
        </div>
     </div>
    <?php 
    }
        public function webcu_save_meta_settings($post_id){

                $webcu_settings = [
                    'webcu_setting_seat', 'webcu_setting_booking', 'webcu_setting_attendee', 'webcu_setting_enable_attendee', 'webcu_setting_toggleStatus'
                ];
                foreach ($webcu_settings as $checkbox) {
                    $value = isset($_POST[$checkbox]) ? '1' : '0';
                    update_post_meta($post_id, $checkbox, $value);
                } 

                if ( isset( $_POST['webcu_timeline'] ) ) {
                        $timeline = wp_unslash( $_POST['webcu_timeline'] );  // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                         $timeline = array_map( 'sanitize_text_field', (array) $timeline );
                        update_post_meta( $post_id, $this->meta_key, $timeline );
                  }
        }  
}