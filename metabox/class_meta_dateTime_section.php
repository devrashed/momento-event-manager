<?php 
/**
 *
 *  Wpcraft reminder section
 *
 **/
namespace Wpcraft\Metabox;

class class_meta_datetime_section {  
    
    private $meta_key = 'wtmem_event_dates';

    public function __construct(){}

    public function wtmem_meta_datetime_field($post) {


        $saved_data = get_post_meta($post->ID, $this->meta_key, true);
        ?>
        <div class="wtmem_date_container">
            <table class="wtmem_date_table" id="wtmem_date_dateTable">
                <thead>
                    <tr>
                        <th><?php echo esc_html__('Start Date', 'momento-event-manager'); ?></th>
                        <th><?php echo esc_html__('Start Time', 'momento-event-manager'); ?></th>
                        <th><?php echo esc_html__('End Date', 'momento-event-manager'); ?></th>
                        <th><?php echo esc_html__('End Time', 'momento-event-manager'); ?></th>
                        <th><?php echo esc_html__('Action', 'momento-event-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($saved_data['start_date'])) {
                        foreach ($saved_data['start_date'] as $index => $start_date) {
                            $start_time = $saved_data['start_time'][$index] ?? '';
                            $end_date   = $saved_data['end_date'][$index] ?? '';
                            $end_time   = $saved_data['end_time'][$index] ?? '';
                            echo '<tr class="wtmem_date_date-row">
                                <td><input type="date" name="wtmem_start_date[]" value="' . esc_attr($start_date) . '"></td>
                                <td><input type="time" name="wtmem_start_time[]" value="' . esc_attr($start_time) . '"></td>
                                <td><input type="date" name="wtmem_end_date[]" value="' . esc_attr($end_date) . '"></td>
                                <td><input type="time" name="wtmem_end_time[]" value="' . esc_attr($end_time) . '"></td>
                                <td><button type="button" class="wtmem_date_remove-row"> <span class="dashicons dashicons-no"></span> </button></td>
                            </tr>';
                        }
                    }
                    ?>
                </tbody>
                    
            </table>
            

            <button type="button" class="wtmem_date_add-btn" id="wtmem_date_addDate"><?php echo esc_html__('+ Add More Dates', 'momento-event-manager'); ?></button>

            <p class="wtmem_date_info-text"><?php echo esc_html__('You can change the date and time format by going to the settings Events (Off/On):', 'momento-event-manager'); ?></p>

            <div class="wtmem_date_toggle-wrapper">
                <label class="wtmem_switch">
                    <input type="checkbox" id="wtmem_date_toggleSwitch" checked>
                    <span class="wtmem_slider wtmem_round"></span>
                </label>
            </div><br>
            
            <div class="wtmem_date_format-section" id="wtmem_date_formatSection">
                
                <div class="wtmem_date_info-box">ℹ️ <?php echo esc_html__('Please select your preferred date format. If you wish to use a custom format, select Custom and enter your desired date format.', 'momento-event-manager'); ?></div>

                <label><?php echo esc_html__('Date Format', 'momento-event-manager'); ?></label>
                <select name="wtmem_date_format" id="wtmem_date_format">
                    <?php 
                    $dateformats = array(
                        'F j, Y' => date_i18n('F j, Y'),
                        'Y-m-d'  => date_i18n('Y-m-d'),
                        'm/d/Y'  => date_i18n('m/d/Y'),
                        'd/m/Y'  => date_i18n('d/m/Y'),
                        'd M Y'  => date_i18n('d M Y'),
                        'l, F jS, Y' => date_i18n('l, F jS, Y'),
                        'D, M j' => date_i18n('D, M j'),                        
                    );
                    foreach ($dateformats as $dateformat => $showdate) {
                        echo '<option value="' . esc_attr($dateformat) . '">' . esc_html($showdate) . '</option>';
                    }
                    ?>   
                </select>
            
                <div class="wtmem_date_info-box">ℹ️ <?php echo esc_html__('Please select the time format from the list. If you want a custom time format, select Custom and write it.', 'momento-event-manager'); ?></div>
                
                <label><?php echo esc_html__('Time Format', 'momento-event-manager'); ?></label>
                <?php
                $time_formats = array('g:i a', 'g:i A', 'H:i', 'H:i:s', 'g:i:s a');
                echo '<select name="wtmem_time_format" id="wtmem_time_format">';
                foreach ($time_formats as $format) {
                    echo '<option value="' . esc_attr($format) . '">' . esc_html(date_i18n($format)) . '</option>';
                }
                echo '</select>';
                ?>
                <div class="wtmem_date_info-box">ℹ️ <?php _e('If you want to show date and time in your local timezone, please select Yes.', 'momento-event-manager'); ?></div>

                <label><?php echo esc_html__('Show Timezone', 'momento-event-manager'); ?></label>
                <select name="wtmem_show_timezone">
                    <option <?php selected($saved_data['timezone'] ?? '', 'No'); ?>>No</option>
                    <option <?php selected($saved_data['timezone'] ?? '', 'Yes'); ?>>Yes</option>
                </select>
            </div>
        </div>        
    <?php



    }

    public function wtmem_save_meta_dateTime_data($post_id) {
        if (!isset($_POST['wtmem_start_date'])) return;

        $data = [
            'start_date'  => isset($_POST['wtmem_start_date']) ? array_map('sanitize_text_field', (array) wp_unslash($_POST['wtmem_start_date'])) : [],
            'start_time'  => isset($_POST['wtmem_start_time']) ? array_map('sanitize_text_field', (array) wp_unslash($_POST['wtmem_start_time'])) : [],
            'end_date'    => isset($_POST['wtmem_end_date']) ? array_map('sanitize_text_field', (array) wp_unslash($_POST['wtmem_end_date'])) : [],
            'end_time'    => isset($_POST['wtmem_end_time']) ? array_map('sanitize_text_field', (array) wp_unslash($_POST['wtmem_end_time'])) : [],
            'date_format' => isset($_POST['wtmem_date_format']) ? sanitize_text_field(wp_unslash($_POST['wtmem_date_format'])) : '',
            'time_format' => isset($_POST['wtmem_time_format']) ? sanitize_text_field(wp_unslash($_POST['wtmem_time_format'])) : '',
            'timezone'    => isset($_POST['wtmem_show_timezone']) ? sanitize_text_field(wp_unslash($_POST['wtmem_show_timezone'])) : '',
        ];
        update_post_meta($post_id, $this->meta_key, $data);
    }    


    
}

