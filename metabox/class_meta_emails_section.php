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

            $counter = 0;
            ?>
            <div id="emailContainer">
            <?php if (!empty($saved)) : ?>
                <?php foreach ($saved as $index => $data): 
                $counter = $index;
                ?>
                <div class="webcu_re_box webcu_re_email-block" data-index="<?php echo esc_attr($index); ?>">
                <div class="webcu_re_expand-remove">
                    <button type="button" class="webcu_re_expand-btn"><?php echo esc_html__('Expand', 'mega-event-manager');?> </button>
                    <button type="button" class="webcu_re_remove-btn"><?php echo esc_html__('Remove', 'mega-event-manager');?> </button>
                </div>

                <div class="webcu_re_header-row">
                    <div class="webcu_re_title"><?php echo esc_html__('Event email reminder', 'mega-event-manager');?> <?php echo esc_html($index); ?></div>
                    <div class="webcu_re_top-actions">
                    <div class="info-icon" title="Info">i</div>
                    <button class="webcu_re_send-now"><?php echo esc_html__('Send Now', 'mega-event-manager');?></button>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="webcu_re_label"><?php echo esc_html__('Email Timing:', 'mega-event-manager');?></div>
                    <div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input class="webcu_re_timing" name="timing_<?php echo esc_attr($index); ?>" type="text" value="<?php echo esc_attr($data['timing']); ?>" /> 
                        <span class="webcu_re_hours-label"><?php echo esc_html__('Hours', 'mega-event-manager') ?></span>
                    </div>
                    <div class="webcu_re_small-help"> <?php echo esc_html__('Type scheduler time in Hour.<br>This reminder email will be sent when this time will be left for the start of the event.', 'mega-event-manager');?> </div>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="label"><?php echo esc_html__('Time count:', 'mega-event-manager') ?></div>
                    <div>
                    <div class="webcu_re_radios">
                        <label class="webcu_re_radio-item">
                        <input type="radio" name="timecount_<?php echo esc_attr($index); ?>" value="before" <?php checked($data['timecount'], 'before'); ?>>
                        <?php echo esc_html__('Before Event Start', 'mega-event-manager');?></label>
                        <label class="webcu_re_radio-item">
                        <input type="radio" name="timecount_<?php echo esc_attr($index); ?>" value="after" <?php checked($data['timecount'], 'after'); ?>> 
                        <?php echo esc_html__('After Event End', 'mega-event-manager');?> 
                        </label>
                    </div>
                    <div class="webcu_re_small-help"> <?php echo esc_html__('Schedule email send before event start or after event end?', 'mega-event-manager');?></div>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="label"><?php echo esc_html__('Email Subject line:', 'mega-event-manager');?></div>
                    <div>
                    <input class="webcu_re_subject" name="subject_<?php echo esc_attr($index); ?>" placeholder="First Reminder email subject line" value="<?php echo esc_attr($data['subject']); ?>" />
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="label"><?php echo esc_html__('Email Content:', 'mega-event-manager');?></div>
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
                    <button type="button" class="webcu_re_expand-btn"><?php echo esc_html__('Expand:', 'mega-event-manager');?></button>
                    <button type="button" class="webcu_re_remove-btn"> <?php echo esc_html__('Remove:', 'mega-event-manager');?></button>
                </div>

                <div class="webcu_re_header-row">
                    <div class="webcu_re_title"><?php echo esc_html__('Event email reminder 1', 'mega-event-manager');?></div>
                    <div class="webcu_re_top-actions">
                    <!-- <div class="webcu_re_info-icon" title="Info">i</div> -->
                    <button class="webcu_re_send-now"><?php echo esc_html__('Send Now', 'mega-event-manager');?></button>
                    </div>
                </div>

                <div class="webcu_re_form-row">
                    <div class="webcu_re_label"><?php echo esc_html__('Time count:', 'mega-event-manager');?></div>
                    <div>
                    <div class="webcu_re_radios">
                        <label class="webcu_re_radio-item"><input type="radio"  name="timecount_1" value="before" checked><?php echo esc_html__('Before Event Start', 'mega-event-manager');?></label>
                        <label class="webcu_re_radio-item"><input type="radio" name="timecount_1" value="after"><?php echo esc_html__('After Event End', 'mega-event-manager');?></label>
                    </div>
                    <div class="webcu_re_small-help"><?php echo esc_html__('Schedule email send before event start or after event end?', 'mega-event-manager');?></div>
                    </div>
                </div>  

                <div class="webcu_re_form-row">
                    <div class="webcu_re_label">  <?php echo esc_html__('Email Timing:', 'mega-event-manager');?></div>
                    <div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <input class="webcu_re_timing" name="timing_1" type="text" value="168" /> 
                        <span class="webcu_re_hours-label">  <?php echo esc_html__('Hours', 'mega-event-manager');?></span>
                    </div>
                    <div class="webcu_re_small-help"> <?php echo esc_html__('Type scheduler time in Hour.<br>This reminder email will be sent when this time will be left for the start of the event.', 'mega-event-manager');?></div>
                    </div>
                </div>

              <div class="webcu_re_form-row">
                <div class="webcu_re_label">Email Subject line:</div>
                <div>
                  <input class="webcu_re_subject" name="subject_1" placeholder="First Reminder email subject line" />
                </div>
              </div>

              <div class="form-row">
                    <div class="label"><?php echo esc_html__('Email Content:', 'mega-event-manager');?></div>
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


      

}