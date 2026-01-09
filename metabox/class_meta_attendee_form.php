<?php 
/**
 *
 *  Webcartisan attending message
 *
 **/

class Class_meta_attendee_form {

    public function __construct() {}
    
    public function webcu_attendee_form($post) {

        wp_nonce_field('save_event_registration_meta', 'event_registration_meta_nonce');

        $attendee_form = get_post_meta($post->ID, 'attendee_form_data_types', true);
        if (!is_array($attendee_form)) {
            $attendee_form = [];
        }

        /* === Attendee form Fields ====*/

        $atten_fields = [
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
            <?php foreach ($atten_fields as $atten_fid => $atten_fdata):

                $atten_saved = $attendee_form['fields'][$atten_fid] ?? [];
                $enabled  = !empty($atten_saved['enabled']);
                $required = !empty($atten_saved['required']);
                $label    = $atten_saved['label'] ?? $atten_fdata['label'];
            ?>

            <div class="webcu_regi_field-row">

                <!-- Enable -->
                <label> 
                    <input type="checkbox" name="webcu_attendee_fields[<?php echo esc_attr($atten_fid); ?>][enabled]" value="1"
                    <?php checked($enabled, 1); ?> />
                    <?php echo esc_html($atten_fdata['label']); ?>
                </label>

                <!-- Label -->
                <input type="text" name="webcu_attendee_fields[<?php echo esc_attr($atten_fid); ?>][label]" value="<?php echo esc_attr($label); ?>" />

                <!-- Required -->
                <label>
                    <input type="checkbox" name="webcu_attendee_fields[<?php echo esc_attr($atten_fid); ?>][required]" value="1"
                      <?php checked($required, 1); ?> />
                    Required
                </label>
            </div>
            <?php endforeach; ?>
        </div>         

        <!-- ==== Attendee dynamically row add & Remove ===== -->
        
        <br>
        <h3><?php echo esc_html__('Custom Attendee Fields', 'mega-events-manager'); ?></h3>
        <p class="description"><?php echo esc_html__('Add custom fields to your Attendee form', 'mega-events-manager'); ?></p>

        <br>
        
        <?php 
        // Get custom fields from the consolidated meta key
        $saved_custom_fields = $attendee_form['custom_fields'] ?? [];
        ?>

        <table id="ue-fields-table" class="attendee-fields-table" style="width:100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th><?php echo esc_html__('Field Label', 'mega-events-manager'); ?></th>
                    <th><?php echo esc_html__('Unique ID', 'mega-events-manager'); ?></th>
                    <th><?php echo esc_html__('Field Type', 'mega-events-manager'); ?></th>
                    <th><?php echo esc_html__('Options', 'mega-events-manager'); ?></th>
                    <th><?php echo esc_html__('Required', 'mega-events-manager'); ?></th>
                    <th><?php echo esc_html__('Action', 'mega-events-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($saved_custom_fields)) : foreach ($saved_custom_fields as $field) : ?>
                    <tr>
                        <td><input type="text" name="ue_field_label[]" value="<?php echo esc_attr($field['label']); ?>"></td>
                        <td><input type="text" name="ue_field_id[]" value="<?php echo esc_attr($field['id']); ?>"></td>
                        <td>
                            <select name="ue_field_type[]" class="ue-field-type">
                                <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                <option value="email" <?php selected($field['type'], 'email'); ?>>Email</option>
                                <option value="number" <?php selected($field['type'], 'number'); ?>>Number</option>
                                <option value="select" <?php selected($field['type'], 'select'); ?>>Select</option>
                                <option value="checkbox" <?php selected($field['type'], 'checkbox'); ?>>Checkbox</option>
                                <option value="radio" <?php selected($field['type'], 'radio'); ?>>Radio</option>
                            </select>
                        </td>
                        <td>
                            <input type="text"
                                name="ue_field_options[]"
                                class="ue-field-options"
                                value="<?php echo esc_attr($field['options'] ?? ''); ?>"
                                placeholder="Option1, Option2"
                                style="<?php echo in_array($field['type'], ['select','checkbox','radio']) ? '' : 'display:none;'; ?>">
                        </td>
                        <td>
                            <select name="ue_field_required[]">
                                <option value="no" <?php selected($field['required'], 'no'); ?>><?php echo esc_html__('Not Required', 'mega-events-manager'); ?></option>
                                <option value="yes" <?php selected($field['required'], 'yes'); ?>><?php echo esc_html__('Required', 'mega-events-manager'); ?></option>
                            </select>
                        </td>
                        <td><button type="button" id="custom_form_remove-row" class="button button-danger"><span class="dashicons dashicons-no"></span></button></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
        <button type="button" id="add-field-row" class="button button-primary" style="margin-top:10px;"><?php echo esc_html__('+ Add New Field', 'mega-events-manager'); ?></button>
        <?php
    }

    
    public function webcu_save_attendee_form($post_id) {
        
        $data = [
            'fields' => [],
            'custom_fields' => []
        ];

        // Save predefined attendee fields
        if (!empty($_POST['webcu_attendee_fields'])) {
            $fields = wp_unslash($_POST['webcu_attendee_fields']);

            foreach ($fields as $fid => $info) {
                $fid = sanitize_key($fid);
                $required = !empty($info['required']) ? 1 : 0;
                
                // Force required for specific fields
                if (in_array($fid, ['gender', 'dob'], true)) {
                    $required = 1;
                }
                
                $data['fields'][$fid] = [
                    'label'    => sanitize_text_field($info['label'] ?? ''),
                    'enabled'  => !empty($info['enabled']) ? 1 : 0,
                    'required' => $required,
                ];
            }
        }

        // Save custom dynamic fields
        if (isset($_POST['ue_field_label']) && is_array($_POST['ue_field_label'])) {
            foreach ($_POST['ue_field_label'] as $index => $label) {
                
                if (!empty($label)) {
                    $field_id = isset($_POST['ue_field_id'][$index]) 
                        ? sanitize_text_field(wp_unslash($_POST['ue_field_id'][$index])) 
                        : '';
                    
                    $field_type = isset($_POST['ue_field_type'][$index]) 
                        ? sanitize_text_field(wp_unslash($_POST['ue_field_type'][$index])) 
                        : 'text';
                    
                    $field_required = isset($_POST['ue_field_required'][$index]) 
                        ? sanitize_text_field(wp_unslash($_POST['ue_field_required'][$index])) 
                        : 'no';
                    
                    $field_options = isset($_POST['ue_field_options'][$index]) 
                        ? sanitize_text_field(wp_unslash($_POST['ue_field_options'][$index])) 
                        : '';

                    $data['custom_fields'][] = [
                        'label'    => sanitize_text_field(wp_unslash($label)),
                        'id'       => $field_id,
                        'type'     => $field_type,
                        'required' => $field_required,
                        'options'  => $field_options,
                    ];
                }
            }
        }

        // Update post meta with consolidated data
        update_post_meta($post_id, 'attendee_form_data_types', $data);
        
    }    

}


