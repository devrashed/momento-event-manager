<?php 
/**
 *
 *  Webcartisan reminder section
 *
 **/

class Class_meta_emails_section { 

    public function __construct() {
   
    }

    public function webcu_meta_emails_field($post) {
        wp_enqueue_editor();
 
        // Load saved reminders (JSON decoded)
        $saved = get_post_meta($post->ID, '_webcu_meta_reminders_email', true);
        $saved = !empty($saved) ? json_decode($saved, true) : [];

        $saved_data = get_post_meta($post->ID, 'webcu_event_dates', true);
        $event_start = isset($saved_data['start_date']) ? $saved_data['start_date'] : '';
        $event_end = isset($saved_data['end_date']) ? $saved_data['end_date'] : '';

        $counter = 0;
        ?>
        <div id="emailContainer">
            <?php if (!empty($saved)) : ?>
                <?php foreach ($saved as $index => $data): 
                    $counter = $index;
                ?>
                <div class="webcu_re_box webcu_re_email-block" data-index="<?php echo esc_attr($index); ?>">
                    <div class="webcu_re_expand-remove">
                        <button type="button" class="webcu_re_expand-btn"><?php echo esc_html__('Expand', 'mega-events-manager'); ?> </button>
                        <button type="button" class="webcu_re_remove-btn"><?php echo esc_html__('Remove', 'mega-events-manager'); ?> </button>
                    </div>

                    <div class="webcu_re_header-row">
                        <div class="webcu_re_title"><?php echo esc_html__('Event email reminder', 'mega-events-manager'); ?> <?php echo esc_html($index); ?></div>
                        <div class="webcu_re_top-actions">
                            <div class="info-icon" title="Info">i</div>
                            <button class="webcu_re_send-now"
                                    data-post-id="<?php echo esc_attr($post->ID); ?>"
                                    data-index="<?php echo esc_attr($index); ?>"
                                    data-timing="<?php echo esc_attr($data['timing'] ?? ''); ?>"
                                    data-timecount="<?php echo esc_attr($data['timecount'] ?? 'before'); ?>"
                                    data-receiver="<?php echo esc_attr($data['email_reciever'] ?? 'organizer'); ?>"
                                    data-subject="<?php echo esc_attr($data['subject'] ?? ''); ?>"
                                    data-content="<?php echo esc_attr(wp_strip_all_tags($data['content'] ?? '')); ?>"
                                    data-event-start="<?php echo esc_attr($event_start); ?>"
                                    data-event-end="<?php echo esc_attr($event_end); ?>">
                                <?php echo esc_html__('Send Now', 'mega-events-manager'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="webcu_re_form-row">
                        <div class="webcu_re_label"><?php echo esc_html__('Email Timing:', 'mega-events-manager'); ?></div>
                        <div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input class="webcu_re_timing" name="timing_<?php echo esc_attr($index); ?>" type="text" value="<?php echo esc_attr($data['timing']); ?>" /> 
                                <span class="webcu_re_hours-label"><?php echo esc_html__('Hours', 'mega-events-manager') ?></span>
                            </div>
                            <div class="webcu_re_small-help"> <?php echo esc_html__('Type scheduler time in Hour.<br>This reminder email will be sent when this time will be left for the start of the event.', 'mega-events-manager'); ?> </div>
                        </div>
                    </div>

                    <div class="webcu_re_form-row">
                        <div class="label"><?php echo esc_html__('Time count:', 'mega-events-manager') ?></div>
                        <div>
                            <div class="webcu_re_radios">
                                <label class="webcu_re_radio-item">
                                    <input type="radio" name="timecount_<?php echo esc_attr($index); ?>" value="before" <?php checked($data['timecount'], 'before'); ?>>
                                    <?php echo esc_html__('Before Event Start', 'mega-events-manager'); ?>
                                </label>
                                <label class="webcu_re_radio-item">
                                    <input type="radio" name="timecount_<?php echo esc_attr($index); ?>" value="after" <?php checked($data['timecount'], 'after'); ?>> 
                                    <?php echo esc_html__('After Event End', 'mega-events-manager'); ?> 
                                </label>
                            </div>
                            <div class="webcu_re_small-help"> <?php echo esc_html__('Schedule email send before event start or after event end?', 'mega-events-manager'); ?></div>
                        </div>
                    </div>

                    <div class="webcu_re_form-row">
                        <div class="label"><?php echo esc_html__('Email Receiver:', 'mega-events-manager') ?></div>
                        <div>
                            <div class="webcu_re_radios">
                                <select name="email_reciever_<?php echo esc_attr($index); ?>">
                                    <option value="organizer" <?php selected($data['email_reciever'] ?? '', 'organizer'); ?>><?php echo esc_html__('Organizer', 'mega-events-manager') ?></option>
                                    <option value="sponsor" <?php selected($data['email_reciever'] ?? '', 'sponsor'); ?>><?php echo esc_html__('Sponsor', 'mega-events-manager') ?></option>
                                    <option value="volunteer" <?php selected($data['email_reciever'] ?? '', 'volunteer'); ?>><?php echo esc_html__('Volunteer', 'mega-events-manager') ?></option>
                                    <option value="attendee" <?php selected($data['email_reciever'] ?? '', 'attendee'); ?>><?php echo esc_html__('Attendee', 'mega-events-manager') ?></option>
                                </select>
                            </div>
                            <div class="webcu_re_small-help"> <?php echo esc_html__('Who Receive the email', 'mega-events-manager'); ?></div>
                        </div>
                    </div>   

                    <div class="webcu_re_form-row">
                        <div class="label"><?php echo esc_html__('Email Subject line:', 'mega-events-manager'); ?></div>
                        <div>
                            <input class="webcu_re_subject" name="subject_<?php echo esc_attr($index); ?>" placeholder="First Reminder email subject line" value="<?php echo esc_attr($data['subject']); ?>" />
                        </div>
                    </div>

                    <div class="webcu_re_form-row">
                        <div class="label"><?php echo esc_html__('Email Content:', 'mega-events-manager'); ?></div>
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
                        <button type="button" class="webcu_re_expand-btn"><?php echo esc_html__('Expand:', 'mega-events-manager'); ?></button>
                        <button type="button" class="webcu_re_remove-btn"> <?php echo esc_html__('Remove:', 'mega-events-manager'); ?></button>
                    </div>

                    <div class="webcu_re_header-row">
                        <div class="webcu_re_title"><?php echo esc_html__('Event email reminder 1', 'mega-events-manager'); ?></div>
                        <div class="webcu_re_top-actions">
                            <button class="webcu_re_send-now"
                                    data-post-id="<?php echo esc_attr($post->ID); ?>"
                                    data-index="1"
                                    data-timing="168"
                                    data-timecount="before"
                                    data-receiver="organizer"
                                    data-subject=""
                                    data-content=""
                                    data-event-start="<?php echo esc_attr($event_start); ?>"
                                    data-event-end="<?php echo esc_attr($event_end); ?>">
                                <?php echo esc_html__('Send Now', 'mega-events-manager'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="webcu_re_form-row">
                        <div class="webcu_re_label"><?php echo esc_html__('Time count:', 'mega-events-manager'); ?></div>
                        <div>
                            <div class="webcu_re_radios">
                                <label class="webcu_re_radio-item"><input type="radio" name="timecount_1" value="before" checked><?php echo esc_html__('Before Event Start', 'mega-events-manager'); ?></label>
                                <label class="webcu_re_radio-item"><input type="radio" name="timecount_1" value="after"><?php echo esc_html__('After Event End', 'mega-events-manager'); ?></label>
                            </div>
                            <div class="webcu_re_small-help"><?php echo esc_html__('Schedule email send before event start or after event end?', 'mega-events-manager'); ?></div>
                        </div>
                    </div>  

                    <div class="webcu_re_form-row">
                        <div class="webcu_re_label"><?php echo esc_html__('Email Timing:', 'mega-events-manager'); ?></div>
                        <div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <input class="webcu_re_timing" name="timing_1" type="text" value="168" /> 
                                <span class="webcu_re_hours-label"><?php echo esc_html__('Hours', 'mega-events-manager'); ?></span>
                            </div>
                            <div class="webcu_re_small-help"> <?php echo esc_html__('Type scheduler time in Hour.<br>This reminder email will be sent when this time will be left for the start of the event.', 'mega-events-manager'); ?></div>
                        </div>
                    </div>

                    <div class="webcu_re_form-row">
                        <div class="label"><?php echo esc_html__('Email Receiver:', 'mega-events-manager'); ?></div>
                        <div>
                            <div class="webcu_re_radios">
                                <select name="email_reciever_1">
                                    <option value="organizer"><?php echo esc_html__('Organizer', 'mega-events-manager') ?></option>
                                    <option value="sponsor"><?php echo esc_html__('Sponsor', 'mega-events-manager') ?></option>
                                    <option value="volunteer"><?php echo esc_html__('Volunteer', 'mega-events-manager') ?></option>
                                    <option value="attendee"><?php echo esc_html__('Attendee', 'mega-events-manager') ?></option>
                                </select>
                            </div>
                            <div class="webcu_re_small-help"> <?php echo esc_html__('Who Receive the email', 'mega-events-manager'); ?></div>
                        </div>
                    </div>

                    <div class="webcu_re_form-row">
                        <div class="webcu_re_label">Email Subject line:</div>
                        <div>
                            <input class="webcu_re_subject" name="subject_1" placeholder="First Reminder email subject line" />
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="label"><?php echo esc_html__('Email Content:', 'mega-events-manager'); ?></div>
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
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

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


    public function webcu_send_email_now_handler() {
        // Set header for JSON response
        header('Content-Type: application/json');
        
        try {
            // Check if it's an AJAX request
            if (!defined('DOING_AJAX') || !DOING_AJAX) {
                throw new Exception('Invalid request');
            }
            
            // Verify nonce
            if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'webcu_nonce')) {
                throw new Exception('Security check failed. Please refresh the page and try again.');
            }
            
            // Get and validate POST data
            $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
            $receiver = isset($_POST['receiver']) ? sanitize_text_field($_POST['receiver']) : '';
            $subject = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
            $content = isset($_POST['content']) ? wp_kses_post($_POST['content']) : '';
            $index = isset($_POST['index']) ? intval($_POST['index']) : 1;
            
            // Validation
            if ($post_id <= 0) {
                throw new Exception('Invalid post ID');
            }
            
            if (empty($subject)) {
                throw new Exception('Email subject is required');
            }
            
            if (empty($content)) {
                throw new Exception('Email content is required');
            }
            
            if (empty($receiver)) {
                throw new Exception('Email receiver is required');
            }
            
            // Check if post exists
            $post = get_post($post_id);
            if (!$post) {
                throw new Exception('Post not found');
            }
            
            // Get email addresses based on receiver type
            $email_addresses = $this->get_receiver_emails($post_id, $receiver);
            
                     
            if (empty($email_addresses)) {
                // For testing, use admin email if no emails found
                if (defined('WP_DEBUG') && WP_DEBUG) {
                    $email_addresses = [get_option('admin_email')];
                } else {
                    throw new Exception('No email addresses found for ' . ucfirst($receiver) . '. Please check your event settings.');
                }
            }
            
            // Send emails
            $sent_count = 0;
            $failed_emails = [];
            
            foreach ($email_addresses as $email) {
                $email = sanitize_email($email);
                
                if (is_email($email)) {
                    $headers = ['Content-Type: text/html; charset=UTF-8'];
                    
                    // Add From header
                    $headers[] = 'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>';
                    
                    // Prepare email content with HTML
                    $email_content = '<!DOCTYPE html>
                    <html>
                    <head>
                        <meta charset="UTF-8">
                        <title>' . esc_html($subject) . '</title>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .email-container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .email-header { background: #f8f8f8; padding: 20px; text-align: center; }
                            .email-content { padding: 20px; }
                            .email-footer { background: #f8f8f8; padding: 15px; text-align: center; font-size: 12px; color: #666; }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <div class="email-header">
                                <h1>' . esc_html($subject) . '</h1>
                            </div>
                            <div class="email-content">
                                ' . wpautop($content) . '
                            </div>
                            <div class="email-footer">
                                <p>This email was sent from ' . get_bloginfo('name') . '</p>
                            </div>
                        </div>
                    </body>
                    </html>';
                    
                    // Test email function
                    add_filter('wp_mail_content_type', function() {
                        return 'text/html';
                    });
                    
                    $mail_sent = wp_mail($email, $subject, $email_content, $headers);
                    
                    remove_filter('wp_mail_content_type', function() {
                        return 'text/html';
                    });
                    
                    if ($mail_sent) {
                        $sent_count++;
                    } else {
                        $failed_emails[] = $email;
                        
                        // Log the error
                        error_log('Failed to send email to: ' . $email . ' for post ID: ' . $post_id);
                    }
                }
            }
            
            // Log the email sending
            $this->log_email_sending($post_id, $index, $receiver, $sent_count, count($email_addresses) - $sent_count);
            
            if ($sent_count > 0) {
                $message = sprintf(
                    'Email sent successfully to %d %s(s)',
                    $sent_count,
                    ucfirst($receiver)
                );
                
                if (!empty($failed_emails)) {
                    $message .= sprintf(
                        '. Failed to send to %d email(s).',
                        count($failed_emails)
                    );
                }
                
                wp_send_json_success([
                    'message' => $message,
                    'sent_count' => $sent_count,
                    'total_count' => count($email_addresses)
                ]);
            } else {
                throw new Exception('Failed to send email to any recipients. Please check your email configuration.');
            }
            
        } catch (Exception $e) {
            error_log('Email sending error: ' . $e->getMessage());
            
            wp_send_json_error([
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
        }
        
        wp_die(); // Always die at the end of AJAX handlers
    }

    private function get_receiver_emails($post_id, $receiver_type) {
        $emails = [];
        
        // For testing - always include admin email
        $admin_email = get_option('admin_email');
        if ($admin_email && is_email($admin_email)) {
            $emails[] = $admin_email;
        }
        
        // Add dummy emails for testing other receiver types
        switch ($receiver_type) {
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


}