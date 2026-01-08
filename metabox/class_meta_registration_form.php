<?php 
/**
 *
 *  Webcartisan registration form metafield class
 *
 **/
class Class_meta_registration_form {
    
    public function webcu_meta_registration_form_field($post) {

        wp_nonce_field('save_event_registration_meta', 'event_registration_meta_nonce');

        /* ======= Registration field =======*/
        
        // Get ALL registration form data from single meta key
        $regi_data = get_post_meta($post->ID, 'registration_form_data_types', true);
        if (!is_array($regi_data)) {
            $regi_data = [];
        }

        // Ensure arrays exist
        if (!isset($regi_data['predefined_fields'])) {
            $regi_data['predefined_fields'] = [];
        }
        if (!isset($regi_data['custom_fields'])) {
            $regi_data['custom_fields'] = [];
        }

        $regi_fields = [
            'firstname' => ['label' => 'First Name'],
            'lastname'  => ['label' => 'Last Name'],
            'email_address' => ['label' => 'Email Address'],
            'phone_number'  => ['label' => 'Phone Number'],
            'address'       => ['label' => 'Address'],
            'designation'   => ['label' => 'Designation'],
            'website'       => ['label' => 'Website'],
            'vegetarian'    => ['label' => 'Vegetarian'],
            'company_name'  => ['label' => 'Company Name'],
            'gender'        => ['label' => 'Gender'],
            'dob'           => ['label' => 'Date of Birth'],
        ];
        ?>

        <div class="webcu_attendee_metabox">
            <?php foreach ($regi_fields as $regi_fid => $regi_fdata):
                $regi_saved = $regi_data['predefined_fields'][$regi_fid] ?? [];
                $enabled  = !empty($regi_saved['enabled']);
                $required = !empty($regi_saved['required']);
                $label    = $regi_saved['label'] ?? $regi_fdata['label'];
            ?>

            <div class="webcu_regi_field-row">
                <!-- Enable -->
                <label> 
                <input type="checkbox"
                    name="webcu_regi_fields[<?php echo esc_attr($regi_fid); ?>][enabled]"
                    value="1"
                    <?php checked($enabled, 1); ?> />

                <?php echo esc_html($regi_fdata['label']); ?></label>

                <!-- Label -->
                <input type="text" 
                       name="webcu_regi_fields[<?php echo esc_attr($regi_fid); ?>][label]"
                       value="<?php echo esc_attr($label); ?>" 
                       placeholder="<?php echo esc_attr($regi_fdata['label']); ?>" />

                <!-- Required -->
                <label>
                    <input type="checkbox"
                        name="webcu_regi_fields[<?php echo esc_attr($regi_fid); ?>][required]"
                        value="1"
                        <?php checked($required, 1); ?> />
                    Required
                </label>
            </div>
            <?php endforeach; ?>
        </div>  

        <!-- ==================================================
              Registration form dynamicly row add & Remove 
              ================================================== -->
        
        <br><br>
        
        <h3><?php echo esc_html__('Custom Registration Fields', 'mega-event-manager'); ?></h3>
        <p class="description"><?php echo esc_html__('Add custom fields to your registration form', 'mega-event-manager'); ?></p>

        <table id="ue_regi-fields-table" class="attendee-fields-table" style="width:100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Field Label', 'mega-event-manager'); ?></th>
                    <th><?php echo esc_html__('Unique ID', 'mega-event-manager'); ?></th>
                    <th><?php echo esc_html__('Field Type', 'mega-event-manager'); ?></th>
                    <th><?php echo esc_html__('Options (for select/radio/checkbox)', 'mega-event-manager'); ?></th>
                    <th><?php echo esc_html__('Required', 'mega-event-manager'); ?></th>
                    <th><?php echo esc_html__('Action', 'mega-event-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($regi_data['custom_fields'])) : 
                    foreach ($regi_data['custom_fields'] as $field) : ?>
                        <tr>
                            <td>
                                <input type="text" 
                                       name="ue_regi_field_label[]" 
                                       value="<?php echo esc_attr($field['label']); ?>"
                                       class="widefat"
                                       placeholder="<?php echo esc_attr__('Field Label', 'mega-event-manager'); ?>">
                            </td>
                            <td>
                                <input type="text" 
                                       name="ue_regi_field_id[]" 
                                       value="<?php echo esc_attr($field['id']); ?>"
                                       class="widefat"
                                       placeholder="<?php echo esc_attr__('field_id', 'mega-event-manager'); ?>">
                            </td>
                            <td>
                                <select name="ue_regi_field_type[]" class="ue_regi-field-type widefat">
                                    <option value="text" <?php selected($field['type'], 'text'); ?>><?php echo esc_html__('Text', 'mega-event-manager'); ?></option>
                                    <option value="email" <?php selected($field['type'], 'email'); ?>><?php echo esc_html__('Email', 'mega-event-manager'); ?></option>
                                    <option value="number" <?php selected($field['type'], 'number'); ?>><?php echo esc_html__('Number', 'mega-event-manager'); ?></option>
                                    <option value="select" <?php selected($field['type'], 'select'); ?>><?php echo esc_html__('Select', 'mega-event-manager'); ?></option>
                                    <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>><?php echo esc_html__('Checkbox', 'mega-event-manager'); ?></option>
                                    <option value="radio" <?php selected($field['type'], 'radio'); ?>><?php echo esc_html__('Radio', 'mega-event-manager'); ?></option>
                                    <option value="textarea" <?php selected($field['type'], 'textarea'); ?>><?php echo esc_html__('Textarea', 'mega-event-manager'); ?></option>
                                </select>
                            </td>
                            <td>
                                <input type="text"
                                    name="ue_regi_field_options[]"
                                    class="ue_regi-field-options widefat"
                                    value="<?php echo esc_attr($field['options'] ?? ''); ?>"
                                    placeholder="<?php echo esc_attr__('Option1, Option2', 'mega-event-manager'); ?>"
                                    style="<?php echo in_array($field['type'], ['select','checkbox','radio']) ? '' : 'display:none;'; ?>">
                            </td>
                            <td>
                                <select name="ue_field_required[]" class="widefat">
                                    <option value="no" <?php selected($field['required'], 'no'); ?>> <?php echo esc_html__('Not Required', 'mega-event-manager'); ?></option>
                                    <option value="yes" <?php selected($field['required'], 'yes'); ?>> <?php echo esc_html__('Required', 'mega-event-manager'); ?></option>
                                </select>
                            </td>
                            <td>
                                <button type="button" class="custom_form_remove-row button button-danger">
                                    <span class="dashicons dashicons-no"></span>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; 
                else: ?>
                    <!-- Empty row template -->
                    <tr class="empty-row" style="display: none;">
                        <td>
                            <input type="text" 
                                   name="ue_regi_field_label[]" 
                                   class="widefat"
                                   placeholder="<?php echo esc_attr__('Field Label', 'mega-event-manager'); ?>">
                        </td>
                        <td>
                            <input type="text" 
                                   name="ue_regi_field_id[]" 
                                   class="widefat"
                                   placeholder="<?php echo esc_attr__('field_id', 'mega-event-manager'); ?>">
                        </td>
                        <td>
                            <select name="ue_regi_field_type[]" class="ue_regi-field-type widefat">
                                <option value="text"><?php echo esc_html__('Text', 'mega-event-manager'); ?></option>
                                <option value="email"><?php echo esc_html__('Email', 'mega-event-manager'); ?></option>
                                <option value="number"><?php echo esc_html__('Number', 'mega-event-manager'); ?></option>
                                <option value="select"><?php echo esc_html__('Select', 'mega-event-manager'); ?></option>
                                <option value="checkbox"><?php echo esc_html__('Checkbox', 'mega-event-manager'); ?></option>
                                <option value="radio"><?php echo esc_html__('Radio', 'mega-event-manager'); ?></option>
                                <option value="textarea"><?php echo esc_html__('Textarea', 'mega-event-manager'); ?></option>
                            </select>
                        </td>
                        <td>
                            <input type="text" 
                                   name="ue_regi_field_options[]" 
                                   class="ue_regi-field-options widefat" 
                                   placeholder="<?php echo esc_attr__('Option1, Option2', 'mega-event-manager'); ?>"
                                   style="display:none;">
                        </td>
                        <td>
                            <select name="ue_field_required[]" class="widefat">
                                <option value="no"><?php echo esc_html__('Not Required', 'mega-event-manager'); ?></option>
                                <option value="yes"><?php echo esc_html__('Required', 'mega-event-manager'); ?></option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="custom_form_remove-row button button-danger">
                                <span class="dashicons dashicons-no"></span>
                            </button>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <button type="button" id="add-field-regi-row" class="button button-primary" style="margin-top:10px;">
            <?php echo esc_html__('+ Add New Field', 'mega-event-manager'); ?>
        </button>
    
        <?php
    }

    /* =====  Save Meta Data ===== */

    public function webcu_save_regitype_form($post_id) {
        
        
        // Initialize combined data array
        $regi_data = [
            'predefined_fields' => [],
            'custom_fields' => []
        ];
        
        /* ===== Save Predefined Fields ===== */

        if (!empty($_POST['webcu_regi_fields'])) {
            $regi_fields = wp_unslash($_POST['webcu_regi_fields']);
            
            foreach ($regi_fields as $regi_fid => $reg_info) {
                $regi_fid = sanitize_key($regi_fid);
                
                $regi_required = !empty($reg_info['required']) ? 1 : 0;
                // Force required for certain fields
                if (in_array($regi_fid, ['gender', 'dob'], true)) {
                    $regi_required = 1;
                }
                
                $regi_data['predefined_fields'][$regi_fid] = [
                    'label'    => sanitize_text_field($reg_info['label'] ?? ''),
                    'enabled'  => !empty($reg_info['enabled']) ? 1 : 0,
                    'required' => $regi_required,
                ];
            }
        }
        
        /* ===== Save Custom Fields ===== */
        if (isset($_POST['ue_regi_field_label'])) {
            $custom_fields = [];
            
            foreach ($_POST['ue_regi_field_label'] as $index => $label) {
                // Skip if label is empty
                if (empty(trim($label))) {
                    continue;
                }
                
                // Get field ID or generate from label
                $field_id = '';
                if (!empty($_POST['ue_regi_field_id'][$index])) {
                    $field_id = sanitize_title_with_dashes($_POST['ue_regi_field_id'][$index]);
                } else {
                    // Generate field ID from label if not provided
                    $field_id = sanitize_title_with_dashes($label);
                }
                
                // Ensure field ID is unique
                $original_id = $field_id;
                $counter = 1;
                while (isset($custom_fields[$field_id])) {
                    $field_id = $original_id . '_' . $counter;
                    $counter++;
                }
                
                // Validate field type
                $field_type = 'text';
                if (!empty($_POST['ue_regi_field_type'][$index])) {
                    $valid_types = ['text', 'email', 'number', 'select', 'checkbox', 'radio', 'textarea'];
                    $submitted_type = sanitize_text_field($_POST['ue_regi_field_type'][$index]);
                    $field_type = in_array($submitted_type, $valid_types) ? $submitted_type : 'text';
                }
                
                // Sanitize options
                $options = '';
                if (!empty($_POST['ue_regi_field_options'][$index])) {
                    $options = sanitize_text_field($_POST['ue_regi_field_options'][$index]);
                }
                
                $custom_fields[$field_id] = [
                    'label'    => sanitize_text_field($label),
                    'id'       => $field_id,
                    'type'     => $field_type,
                    'required' => (!empty($_POST['ue_field_required'][$index]) && $_POST['ue_field_required'][$index] === 'yes') ? 'yes' : 'no',
                    'options'  => $options,
                ];
            }
            $regi_data['custom_fields'] = $custom_fields;
        }
        
        // Save all data in single meta key
        update_post_meta($post_id, 'registration_form_data_types', $regi_data);
        
        // Clean up old meta key if exists
        delete_post_meta($post_id, 'ue_registrationform_fields');
    }
}