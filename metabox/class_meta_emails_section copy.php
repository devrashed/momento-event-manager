<?php 
/**
 *
 *  Webcartisan reminder section
 *
 **/

class Class_meta_emails_section { 

    Public function webcu_meta_emails_field($post) {
            wp_enqueue_editor();

            // Load saved reminders (JSON decoded)
            $saved = get_post_meta($post->ID, '_webcu_meta_reminders_email', true);
            $saved = !empty($saved) ? json_decode($saved, true) : [];

              $saved_data = get_post_meta( $post->ID, 'webcu_event_dates', true );
              $event_start = $saved_data['start_date'];
              $end_dates = $saved_data['end_date'];

            $counter = 0;
            ?>
            <div id="emailContainer">
              <?php if (!empty($saved)) : ?>
                <?php foreach ($saved as $index => $data): 
                $counter = $index;
                ?>
                <div class="webcu_re_box webcu_re_email-block" data-index="<?php echo esc_attr($index); ?>">
                <div class="webcu_re_expand-remove">
                    <button type="button" class="webcu_re_expand-btn"><?php echo esc_html__('Expand', 'mega-events-manager');?> </button>
                    <button type="button" class="webcu_re_remove-btn"><?php echo esc_html__('Remove', 'mega-events-manager');?> </button>
                </div>

                <div class="webcu_re_header-row">
                    <div class="webcu_re_title"><?php echo esc_html__('Event email reminder', 'mega-events-manager');?> <?php echo esc_html($index); ?></div>
                    <div class="webcu_re_top-actions">
                    <div class="info-icon" title="Info">i</div>
                    <button class="webcu_re_send-now"><?php echo esc_html__('Send Now', 'mega-events-manager');?></button>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="webcu_re_label"><?php echo esc_html__('Email Timing:', 'mega-events-manager');?></div>
                    <div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input class="webcu_re_timing" name="timing_<?php echo esc_attr($index); ?>" type="text" value="<?php echo esc_attr($data['timing']); ?>" /> 
                        <span class="webcu_re_hours-label"><?php echo esc_html__('Hours', 'mega-events-manager') ?></span>
                    </div>
                    <div class="webcu_re_small-help"> <?php echo esc_html__('Type scheduler time in Hour.<br>This reminder email will be sent when this time will be left for the start of the event.', 'mega-events-manager');?> </div>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="label"><?php echo esc_html__('Time count:', 'mega-events-manager') ?></div>
                    <div>
                    <div class="webcu_re_radios">
                        <label class="webcu_re_radio-item">
                        <input type="radio" name="timecount_<?php echo esc_attr($index); ?>" value="before" <?php checked($data['timecount'], 'before'); ?>>
                        <?php echo esc_html__('Before Event Start', 'mega-events-manager');?></label>
                        <label class="webcu_re_radio-item">
                        <input type="radio" name="timecount_<?php echo esc_attr($index); ?>" value="after" <?php checked($data['timecount'], 'after'); ?>> 
                        <?php echo esc_html__('After Event End', 'mega-events-manager');?> 
                        </label>
                    </div>
                    <div class="webcu_re_small-help"> <?php echo esc_html__('Schedule email send before event start or after event end?', 'mega-events-manager');?></div>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="label"><?php echo esc_html__('Email Receiver:', 'mega-events-manager') ?></div>
                    <div>
                    <div class="webcu_re_radios">
                       <select name="email_reciever_<?php echo esc_attr($index); ?>">
                            <option value="organizer" <?php selected($data['email_reciever'] ?? '', 'organizer'); ?>><?php echo esc_html__('organizer', 'mega-events-manager') ?></option>
                            <option value="sponsor" <?php selected($data['email_reciever'] ?? '', 'sponsor'); ?>><?php echo esc_html__('Sponsor', 'mega-events-manager') ?></option>
                            <option value="volunteer" <?php selected($data['email_reciever'] ?? '', 'volunteer'); ?>><?php echo esc_html__('Volunteer', 'mega-events-manager') ?></option>
                            <option value="attendee" <?php selected($data['email_reciever'] ?? '', 'attendee'); ?>><?php echo esc_html__('Attendee', 'mega-events-manager') ?></option>
                        </select>

                    </div>
                    <div class="webcu_re_small-help"> <?php echo esc_html__('Who Recieve the email', 'mega-events-manager');?></div>
                    </div>
                </div>   
                    

                <div class="webcu_re_form-row">
                    <div class="label"><?php echo esc_html__('Email Subject line:', 'mega-events-manager');?></div>
                    <div>
                    <input class="webcu_re_subject" name="subject_<?php echo esc_attr($index); ?>" placeholder="First Reminder email subject line" value="<?php echo esc_attr($data['subject']); ?>" />
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="label"><?php echo esc_html__('Email Content:', 'mega-events-manager');?></div>
                    <div>
                    <?php 
                        wp_editor(
                        $data['content'], 
                        'content_' . $index, 
                        [
                            'textarea_name' => 'content_' . $index,
                            'media_buttons' => true,
                            'textarea_rows' => 8,
                            'teeny' => false,
                            'tinymce' => true,
                        ]
                        );
                    ?>
                    </div>
                </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Default block if no saved data -->
                <div class="webcu_re_box webcu_re_email-block" data-index="1">
                <div class="webcu_re_expand-remove">
                    <button type="button" class="webcu_re_expand-btn"><?php echo esc_html__('Expand:', 'mega-events-manager');?></button>
                    <button type="button" class="webcu_re_remove-btn"> <?php echo esc_html__('Remove:', 'mega-events-manager');?></button>
                </div>

                <div class="webcu_re_header-row">
                    <div class="webcu_re_title"><?php echo esc_html__('Event email reminder 1', 'mega-events-manager');?></div>
                    <div class="webcu_re_top-actions">
                    <!-- <div class="webcu_re_info-icon" title="Info">i</div> -->
                    <button class="webcu_re_send-now"
                            data-post-id="<?php echo esc_attr($post->ID); ?>"
                            data-index="<?php echo esc_attr($index); ?>"
                            data-timing="<?php echo esc_attr($data['timing'] ?? ''); ?>"
                            data-timecount="<?php echo esc_attr($data['timecount'] ?? 'before'); ?>"
                            data-receiver="<?php echo esc_attr($data['email_reciever'] ?? 'organizer'); ?>"
                            data-event-start="<?php echo esc_attr($event_start); ?>"
                            data-event-end="<?php echo esc_attr($end_dates); ?>"> <?php echo esc_html__('Send Now', 'mega-events-manager');?></button>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="webcu_re_label"><?php echo esc_html__('Time count:', 'mega-events-manager');?></div>
                    <div>
                    <div class="webcu_re_radios">
                        <label class="webcu_re_radio-item"><input type="radio"  name="timecount_1" value="before" checked><?php echo esc_html__('Before Event Start', 'mega-events-manager');?></label>
                        <label class="webcu_re_radio-item"><input type="radio" name="timecount_1" value="after"><?php echo esc_html__('After Event End', 'mega-events-manager');?></label>
                    </div>
                    <div class="webcu_re_small-help"><?php echo esc_html__('Schedule email send before event start or after event end?', 'mega-events-manager');?></div>
                    </div>
                </div>  

                <div class="webcu_re_form-row">
                    <div class="webcu_re_label">  <?php echo esc_html__('Email Timing:', 'mega-events-manager');?></div>
                    <div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input class="webcu_re_timing" name="timing_1" type="text" value="168" /> 
                        <span class="webcu_re_hours-label">  <?php echo esc_html__('Hours', 'mega-events-manager');?></span>
                    </div>
                    <div class="webcu_re_small-help"> <?php echo esc_html__('Type scheduler time in Hour.<br>This reminder email will be sent when this time will be left for the start of the event.', 'mega-events-manager');?></div>
                    </div>
                </div>

              <div class="webcu_re_form-row">
                <div class="webcu_re_label">Email Subject line:</div>
                <div>
                  <input class="webcu_re_subject" name="subject_1" placeholder="First Reminder email subject line" />
                </div>
              </div>
              <div class="form-row">
                    <div class="label"><?php echo esc_html__('Email Content:', 'mega-events-manager');?></div>
                    <div>
                    <?php 
                        wp_editor('', 'content_1', [
                        'textarea_name' => 'content_1',
                        'media_buttons' => true,
                        'textarea_rows' => 8,
                        'teeny' => false,
                        'tinymce' => true,
                        ]);
                    ?>
                    </div>
                </div>
                </div>
            <?php endif; ?>
            </div>

            <div>
                <button type="button" id="addNewEmail" class="webcu_re_add-new-email"><?php echo esc_html__('Add New Email', 'mega-events-manager') ?></button>
            </div>
        <?php
        
        
    }

    public function webcu_save_emails_metabox_data($post_id) {
        $reminders = [];
        foreach ($_POST as $key => $val) {
            if (preg_match('/^timing_(\d+)$/', $key, $match)) {
                $i = (int) $match[1];
                
                $reminders[$i]['timing'] = isset($_POST['timing_' . $i]) ? sanitize_text_field(wp_unslash($_POST['timing_' . $i])) : '';
                $reminders[$i]['timecount'] = isset($_POST['timecount_' . $i]) ? sanitize_text_field(wp_unslash($_POST['timecount_' . $i])) : '';
                $reminders[$i]['email_reciever'] = isset($_POST['email_reciever_' . $i]) ? sanitize_text_field(wp_unslash($_POST['email_reciever_' . $i])) : '';
                $reminders[$i]['subject'] = isset($_POST['subject_' . $i]) ? sanitize_text_field(wp_unslash($_POST['subject_' . $i])) : '';
                $reminders[$i]['content'] = isset($_POST['content_' . $i]) ? wp_kses_post(wp_unslash($_POST['content_' . $i])) : '';
            }
        }

        if (!empty($reminders)) {
            update_post_meta($post_id, '_webcu_meta_reminders_email', wp_json_encode($reminders));
        } else {
            delete_post_meta($post_id, '_webcu_meta_reminders_email');
        }
    }


        /* =========================================== 
                   Email functionality send 
           ======================================== */


        public function webcu_send_email_now_handler() {
            // Verify nonce
            if (!wp_verify_nonce($_POST['nonce'], 'webcu_send_email_nonce')) {
                wp_die('Security check failed');
            }
            

            $post_id = intval($_POST['post_id']);
            $index = intval($_POST['index']);
            $timing = intval($_POST['timing']);
            $timecount = sanitize_text_field($_POST['timecount']);
            $receiver = sanitize_text_field($_POST['receiver']);
            $subject = sanitize_text_field($_POST['subject']);
            $content = wp_kses_post($_POST['content']);
            
            $event_start = sanitize_text_field($_POST['event_start']);
            $event_end = sanitize_text_field($_POST['event_end']);
            
            // Calculate when the email should have been sent based on timing
            
            $send_time = null;
            
            if ($timecount === 'before') {
                // If sending before event start
                if ($event_start) {
                    $event_time = strtotime($event_start);
                    $send_time = $event_time - ($timing * 60); // Convert hours to seconds
                }
            } else {
                // If sending after event end
                if ($event_end) {
                    $event_time = strtotime($event_end);
                    $send_time = $event_time + ($timing * 3600); // Convert hours to seconds
                }
            }
            
            // Get recipients based on receiver type
            $recipients = $this->webcu_get_recipients_by_type($receiver, $post_id);
            
            if (empty($recipients)) {
                wp_send_json_error('No recipients found for this type.');
            }
            
            // Prepare email
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            // Send email to each recipient
            $sent_count = 0;
            foreach ($recipients as $recipient_email) {
                if (is_email($recipient_email)) {
                    $email_sent = wp_mail($recipient_email, $subject, $content, $headers);
                    if ($email_sent) {
                        $sent_count++;
                    }
                    
                    // Log the email sent
                    $this->webcu_log_email_sent($post_id, $index, $recipient_email, $subject, $send_time);
                }
            }
            
            // Return success response
            wp_send_json_success([
                'message' => sprintf(__('Email sent to %d recipients.', 'mega-events-manager'), $sent_count),
                'sent_count' => $sent_count,
                'scheduled_time' => $send_time ? date('Y-m-d H:i:s', $send_time) : null
            ]);
        }
       
         // Helper function to get recipients by type
        public function webcu_get_recipients_by_type($type, $post_id) {
             $emails = [];
            
            // For testing - always include admin email
            $admin_email = get_option('admin_email');
            if ($admin_email && is_email($admin_email)) {
                $emails[] = $admin_email;
            }
            
            switch ($type) {
               // Get organizer email from event meta
                    case 'organizer':
                        $orga_ids = get_post_meta( $post_id, '_uem_organizers', true );
                                $emails = [];

                                if ( is_array( $orga_ids ) ) {
                                    foreach ( $orga_ids as $orga_id ) {

                                        error_log( 'Organizer ID: ' . $orga_id );

                                        $email = get_post_meta( $orga_id, 'webcu_orga_email', true );

                                        if ( $email ) {
                                            $emails[] = $email;
                                        } 
                                    }
                                }

                    break;
                            
                    case 'sponsor':
                            // Get sponsor emails (assuming multiple sponsors)
                            $sponsors_id = get_post_meta($post_id, '_uem_sponsors', true);
                            //$recipients = [];
                            if ( is_array( $sponsors_id ) ) {
                                foreach ( $sponsors_id as $spons_id ) {
                                    $spos_email = get_post_meta( $spons_id, 'webcu_spon_email', true );
                                    if ( $spos_email ) {
                                        $emails[] = $spos_email;
                                    }
                                }
                            }
                    break;
                            
                    case 'volunteer':
                            // Get volunteer emails (assuming multiple volunteers) 
                            $volun_id = get_post_meta($post_id, '_uem_volunteers', true);

                            //$recipients = [];
                            if ( is_array( $volun_id ) ) {
                                foreach ( $volun_id as $vol_id ) {
                                    $vol_email = get_post_meta( $vol_id, 'webcu_volun_email', true );
                                    if ( $vol_email ) {
                                        $emails[] = $vol_email;
                                    }
                                }
                            }

                            break;
                    
                    case 'attendee':

                            $product_ids = get_post_meta($post_id, '_uem_wc_products', true );
                            $product_ids = array_map( 'intval', (array) $product_ids );
                            $orders = wc_get_orders( array(
                                'status' => array( 'completed', 'processing' ),
                                'limit'  => -1,
                            ) );

                            foreach ( $orders as $order ) {
                                foreach ( $order->get_items() as $item ) {
                                    $order_product_id = (int) $item->get_product_id();
                                    // product match
                                    if ( in_array( $order_product_id, $product_ids, true ) ) {
                                        $attendees = $item->get_meta( '_uem_attendees', true );
                                        // attendees safety check
                                        if ( ! empty( $attendees ) && is_array( $attendees ) ) {

                                            foreach ( $attendees as $attendee ) {

                                                if ( ! empty( $attendee['email'] && is_email($attendee['email']) ) ) {
                                                // echo esc_html( $attendee['email'] ) . '<br>';
                                                    $emails[] = $attendee['email'];
                                                
                                                }
                                            }
                                        }
                                    }
                                }
                            }                        
                    break;


            }
            
            // Remove duplicates
            $emails = array_unique($emails);
        
            return $emails;
        }

        // Helper function to log email sent
        public function webcu_log_email_sent($post_id, $index, $recipient, $subject, $scheduled_time) {
            $log_entry = [
                'timestamp' => current_time('mysql'),
                'post_id' => $post_id,
                'reminder_index' => $index,
                'recipient' => $recipient,
                'subject' => $subject,
                'scheduled_time' => $scheduled_time ? date('Y-m-d H:i:s', $scheduled_time) : 'Immediate',
                'actual_sent_time' => current_time('mysql')
            ];
            
            $logs = get_post_meta($post_id, '_webcu_email_logs', true);
            if (empty($logs) || !is_array($logs)) {
                $logs = [];
            }
            
            $logs[] = $log_entry;
            update_post_meta($post_id, '_webcu_email_logs', $logs);
        }

        // 1. Clear scheduled emails function
        public function webcu_clear_scheduled_emails($post_id) {
            // Get all scheduled cron jobs
            $crons = _get_cron_array();
            
            if (empty($crons)) {
                return;
            }
            
            // Find and remove scheduled emails for this post
            foreach ($crons as $timestamp => $cron) {
                if (isset($cron['webcu_send_scheduled_email'])) {
                    foreach ($cron['webcu_send_scheduled_email'] as $key => $scheduled) {
                        // Check if this scheduled email belongs to our post
                        if (isset($scheduled['args'][0]) && $scheduled['args'][0] == $post_id) {
                            // Unschedule this specific email
                            wp_unschedule_event($timestamp, 'webcu_send_scheduled_email', $scheduled['args']);
                        }
                    }
                }
            }
        }

        // 2. Schedule emails for event
        public function webcu_schedule_emails_for_event($post_id) {
            // Clear existing scheduled emails for this event
            $this->webcu_clear_scheduled_emails($post_id);
            
            $saved = get_post_meta($post_id, '_webcu_meta_reminders_email', true);
            $saved = !empty($saved) ? json_decode($saved, true) : [];
            
            if (empty($saved)) {
                return;
            }
            
            $event_start = get_post_meta($post_id, '_event_start_date', true);
            $event_end = get_post_meta($post_id, '_event_end_date', true);
            
            foreach ($saved as $index => $data) {
                if (empty($data['timing']) || empty($data['subject']) || empty($data['content'])) {
                    continue;
                }
                
                $timing = intval($data['timing']);
                $timecount = $data['timecount'];
                
                // Calculate when to send
                $send_timestamp = null;
                
                if ($timecount === 'before' && $event_start) {
                    $event_time = strtotime($event_start);
                    $send_timestamp = $event_time - ($timing * 3600);
                } elseif ($timecount === 'after' && $event_end) {
                    $event_time = strtotime($event_end);
                    $send_timestamp = $event_time + ($timing * 3600);
                }
                
                if ($send_timestamp && $send_timestamp > time()) {
                    // Schedule the email
                    wp_schedule_single_event(
                        $send_timestamp,
                        'webcu_send_scheduled_email',
                        [$post_id, $index, $data]
                    );
                    
                    error_log("Scheduled email for post {$post_id}, index {$index} at " . date('Y-m-d H:i:s', $send_timestamp));
                }
            }
        }

        // 3. Handle scheduled email when it's time
        public function webcu_handle_scheduled_email($post_id, $index, $data) {
            error_log("Processing scheduled email for post {$post_id}, index {$index}");
            
            // Get the email data
            $subject = isset($data['subject']) ? $data['subject'] : '';
            $content = isset($data['content']) ? $data['content'] : '';
            $receiver = isset($data['email_reciever']) ? $data['email_reciever'] : 'organizer';
            
            if (empty($subject) || empty($content)) {
                error_log("Email subject or content is empty for post {$post_id}");
                return;
            }
            
            // Get recipients
            $recipients = webcu_get_recipients_by_type($receiver, $post_id);
            
            if (empty($recipients)) {
                error_log("No recipients found for type {$receiver} in post {$post_id}");
                return;
            }
            
            // Prepare email
            $headers = array('Content-Type: text/html; charset=UTF-8');
            
            // Send email to each recipient
            $sent_count = 0;
            foreach ($recipients as $recipient_email) {
                if (is_email($recipient_email)) {
                    $email_sent = wp_mail($recipient_email, $subject, $content, $headers);
                    
                    if ($email_sent) {
                        $sent_count++;
                        // Log the email sent
                        webcu_log_email_sent($post_id, $index, $recipient_email, $subject, time());
                        error_log("Sent email to: {$recipient_email}");
                    } else {
                        error_log("Failed to send email to: {$recipient_email}");
                    }
                }
            }
            
            error_log("Total emails sent for post {$post_id}, index {$index}: {$sent_count}");
        }

        // 4. Hook to save post - schedule emails when event is saved
        public function webcu_schedule_emails_on_save($post_id, $post) {
            // Check if this is your event post type (change 'event' to your actual post type)
            if (get_post_type($post_id) != 'event') {
                return;
            }
            
            // Check if it's not an autosave
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            
            // Check permissions
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
            
            // Check if email reminders meta exists
            $saved = get_post_meta($post_id, '_webcu_meta_reminders_email', true);
            if (empty($saved)) {
                return;
            }
            
            // Schedule emails
            $this->webcu_schedule_emails_for_event($post_id);
        }

        // 5. Optional: Add admin notice to show scheduled emails
        public function webcu_show_scheduled_emails_notice() {
            global $post;
            
            if (!is_admin() || !$post || get_post_type($post->ID) != 'event') {
                return;
            }
            $crons = _get_cron_array();
            $scheduled_count = 0;
            
            if (!empty($crons)) {
                foreach ($crons as $timestamp => $cron) {
                    if (isset($cron['webcu_send_scheduled_email'])) {
                        foreach ($cron['webcu_send_scheduled_email'] as $key => $scheduled) {
                            if (isset($scheduled['args'][0]) && $scheduled['args'][0] == $post->ID) {
                                $scheduled_count++;
                            }
                        }
                    }
                }
            }
            
            if ($scheduled_count > 0) {
                ?>
                <div class="notice notice-info">
                    <p><?php echo sprintf(__('%d email reminder(s) scheduled for this event.', 'mega-events-manager'), $scheduled_count); ?></p>
                </div>
                <?php
            }
        }                


}