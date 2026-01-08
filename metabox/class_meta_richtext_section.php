<?php 
/**
 *
 *  Webcartisan RichText 
 *
 **/


class Class_meta_richtext_section { 

      public function webcu_richtext_fields($post) { 
        $saved_eventstatus = get_post_meta($post->ID, 'webcu_eventstatus', true);
        $saved_attendance = get_post_meta($post->ID, 'webcu_attendance_mode', true);
      ?>  

    <div class="webcu_container">
        <div class="webcu_form-header">
            <label><?php echo esc_html__('Rich Text Status', 'mega-event-manager') ?></label>
            <select id="rich_text_status">
                <option value="enable"><?php echo esc_html__('Enable', 'mega-event-manager') ?></option>
                <option value="disable"><?php echo esc_html__('Disable', 'mega-event-manager') ?></option>
            </select>
        </div>

            <table class="webcu_form_section">
                <tr>
                    <td><?php echo esc_html__('Type:', 'mega-event-manager') ?></td>
                    <td><?php echo esc_html__('Event', 'mega-event-manager') ?></td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Name:', 'mega-event-manager') ?></td>
                    <?php
                       $post_id = get_the_ID(); // get current post ID dynamically

                        $args = array(
                            'post_type' => 'mem_event',
                            'p'         => $post_id,
                        );

                        $event_query = new \WP_Query( $args );

                        if ( $event_query->have_posts() ) :
                            while ( $event_query->have_posts() ) : $event_query->the_post();
                        ?>
                    <td><?php the_title(); ?></td>
                    <?php
                         endwhile;
                          endif;
                    wp_reset_postdata();
                    ?>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Start Date:', 'mega-event-manager') ?></td>
                <td>
                     
                <?php 
                    $dates = get_post_meta(get_the_ID(), 'webcu_event_dates', true);
                   
                    /* $start_date  = $dates['start_date'][0];   
                    $start_time  = $dates['start_time'][0];   
                    $date_format = $dates['date_format'];     
                    $time_format = $dates['time_format'];     
                    
                    $datetime_string = $start_date . ' ' . $start_time;
                    
                    $timestamp = strtotime($datetime_string);
                          
                    $final_start_date = date($date_format, $timestamp);
                    $final_start_time = date($time_format, $timestamp);

                    echo esc_attr ($final_start_date . ' at ' . $final_start_time); */
                    
                    if ( is_array($dates) ) {

                    $start_date   = isset($dates['start_date'][0]) ? $dates['start_date'][0] : '';
                    $start_time   = isset($dates['start_time'][0]) ? $dates['start_time'][0] : '';
                    $end_date     = isset($dates['end_date'][0]) ? $dates['end_date'][0] : '';
                    $end_time     = isset($dates['end_time'][0]) ? $dates['end_time'][0] : '';
                    $date_format  = isset($dates['date_format']) ? $dates['date_format'] : 'Y-m-d';
                    $time_format  = isset($dates['time_format']) ? $dates['time_format'] : 'H:i';

                    if ( $start_date && $start_time ) {
                        $timestamp = strtotime($start_date . ' ' . $start_time);
                        echo esc_html( date($date_format, $timestamp) . ' at ' . date($time_format, $timestamp) );
                    }

                } else {
                    echo esc_html__('Event date not available', 'mega-event-manager');
                }


                ?>
                </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('End Date:', 'mega-event-manager') ?></td>
                    <td>
                      <?php 
                      if ( is_array($dates) ) {
                        /* $end_date   = $dates['end_date'][0];   
                        $end_time   = $dates['end_time'][0];  */
                        $end_date     = isset($dates['end_date'][0]) ? $dates['end_date'][0] : '';
                        $end_time     = isset($dates['end_time'][0]) ? $dates['end_time'][0] : '';  
                        $date_format = $dates['date_format'];  
                        $time_format = $dates['time_format'];  

                        $end_datetime_string = $end_date . ' ' . $end_time;

                        $end_timestamp = strtotime($end_datetime_string);

                        $final_end_date = date($date_format, $end_timestamp);
                        $final_end_time = date($time_format, $end_timestamp);
                        echo esc_attr($final_end_date . ' at ' . $final_end_time);
                      }  
                     ?>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Event Status:', 'mega-event-manager') ?></td>
                    <td>
                        <select name="webcu_eventstatus" id="webcu_eventstatus">
                            <option value="event rescheduled" <?php selected($saved_eventstatus, 'event rescheduled'); ?>> <?php echo esc_html__('Event Rescheduled', 'mega-event-manager') ?></option>
                            <option value="event canceled" <?php selected($saved_eventstatus, 'event canceled'); ?>><?php echo esc_html__('Event Canceled', 'mega-event-manager') ?></option>
                            <option value="event confirmed" <?php selected($saved_eventstatus, 'event confirmed'); ?>><?php echo esc_html__('Event Confirmed', 'mega-event-manager') ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Event Attendance Mode:', 'mega-event-manager') ?></td>
                    <td>
                        <select name="webcu_attendance_mode" id="webcu_attendance_mode">
                            <option value="OfflineEventAttendanceMode" <?php selected($saved_attendance, 'OfflineEventAttendanceMode'); ?>> <?php echo esc_html__('Event Confirmed', 'mega-event-manager') ?></option>
                            <option value="OnlineEventAttendanceMode" <?php selected($saved_attendance, 'OnlineEventAttendanceMode'); ?>> <?php echo esc_html__('OnlineEventAttendanceMode', 'mega-event-manager') ?> </option>
                            <option value="MixedEventAttendanceMode" <?php selected($saved_attendance, 'MixedEventAttendanceMode'); ?>><?php echo esc_html__('MixedEventAttendanceMode', 'mega-event-manager') ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><?php echo esc_html__('Previous Start Date:', 'mega-event-manager') ?></td>
                    <td> 2025-11-02 14:00:00</td>
                </tr>
            </table>

            <div class="webcu_footer-info">
                <i>ℹ</i> <a href="#"><?php echo esc_html__('Check Rich Text Status', 'mega-event-manager') ?></a>
            </div>
     </div>
      
    <?php 
      }

    Public function webcu_save_meta_richtext($post_id){

        $webcu_richtext = [ 'webcu_eventstatus', 'webcu_attendance_mode' ];
        
        foreach ($webcu_richtext as $field) {
            $value = isset($_POST[$field]) ? sanitize_text_field(wp_unslash($_POST[$field])) : '';
            update_post_meta($post_id, $field, $value);
        }

    }

}