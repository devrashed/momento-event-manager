<?php

 function webcu_display_attendee_form() {
    global $post;

    if ( empty( $post ) ) {
        return;
    }

    $post_id = $post->ID;

    $attendee_form = get_post_meta( $post_id, 'attendee_form_data_types', true );
    
    if (!is_array($attendee_form)) {
        echo '<p>' . esc_html__('No registration form available.', 'mega-events-manager') . '</p>';
        return;
    }

    ?>
    <div class="webcu-attendee-form">
        
        <div class="webcu-form-fields">
            
            <?php 
            // Display predefined fields
            if (!empty($attendee_form['fields'])) :
                foreach ($attendee_form['fields'] as $field_id => $field_data) :
                    
                    // Skip if field is not enabled
                    if (empty($field_data['enabled'])) {
                        continue;
                    }
                    
                    $field_label = !empty($field_data['label']) ? $field_data['label'] : ucwords(str_replace('_', ' ', $field_id));
                    $is_required = !empty($field_data['required']);
                    $required_attr = $is_required ? 'required' : '';
                    $required_mark = $is_required ? '<span class="required">*</span>' : '';
                    
                    ?>
                    <div class="webcu-form-group webcu-field-<?php echo esc_attr($field_id); ?>">
                        <label for="webcu_<?php echo esc_attr($field_id); ?>">
                            <?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
                        </label>
                        
                        <?php
                        // Render field based on type
                        switch ($field_id) {
                            case 'email_address':
                                ?>
                                <input type="email" 
                                       id="webcu_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                            
                            case 'phone_number':
                                ?>
                                <input type="tel" 
                                       id="webcu_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                            
                            case 'website':
                                ?>
                                <input type="url" 
                                       id="webcu_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       placeholder="https://" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                            
                            case 'address':
                                ?>
                                <textarea id="webcu_<?php echo esc_attr($field_id); ?>" 
                                          name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                          class="webcu-form-control" 
                                          rows="3" 
                                          <?php echo esc_attr($required_attr); ?>></textarea>
                                <?php
                                break;
                            
                            case 'vegetarian':
                                ?>
                                <select id="webcu_<?php echo esc_attr($field_id); ?>" 
                                        name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                        class="webcu-form-control" 
                                        <?php echo esc_attr($required_attr); ?>>
                                    <option value=""><?php echo esc_html__('Select Option', 'mega-events-manager'); ?></option>
                                    <option value="yes"><?php echo esc_html__('Yes', 'mega-events-manager'); ?></option>
                                    <option value="no"><?php echo esc_html__('No', 'mega-events-manager'); ?></option>
                                </select>
                                <?php
                                break;
                            
                            case 'gender':
                                ?>
                                <select id="webcu_<?php echo esc_attr($field_id); ?>" 
                                        name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                        class="webcu-form-control" 
                                        <?php echo esc_attr($required_attr); ?>>
                                    <option value=""><?php echo esc_html__('Select Gender', 'mega-events-manager'); ?></option>
                                    <option value="male"><?php echo esc_html__('Male', 'mega-events-manager'); ?></option>
                                    <option value="female"><?php echo esc_html__('Female', 'mega-events-manager'); ?></option>
                                    <option value="other"><?php echo esc_html__('Other', 'mega-events-manager'); ?></option>
                                </select>
                                <?php
                                break;
                            
                            case 'dob':
                                ?>
                                <input type="date" 
                                       id="webcu_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                            
                            default:
                                // Text input for all other fields
                                ?>
                                <input type="text" 
                                       id="webcu_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_attendee[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                        }
                        ?>
                    </div>
                    <?php
                endforeach;
            endif;
            
            // Display custom dynamic fields
            if (!empty($attendee_form['custom_fields'])) :
                foreach ($attendee_form['custom_fields'] as $index => $custom_field) :
                    
                    $field_id = !empty($custom_field['id']) ? sanitize_key($custom_field['id']) : 'custom_field_' . $index;
                    $field_label = !empty($custom_field['label']) ? $custom_field['label'] : 'Custom Field';
                    $field_type = !empty($custom_field['type']) ? $custom_field['type'] : 'text';
                    $is_required = !empty($custom_field['required']) && $custom_field['required'] === 'yes';
                    $required_attr = $is_required ? 'required' : '';
                    $required_mark = $is_required ? '<span class="required">*</span>' : '';
                    $field_options = !empty($custom_field['options']) ? $custom_field['options'] : '';
                    
                    ?>
                    <div class="webcu-form-group webcu-custom-field webcu-field-<?php echo esc_attr($field_id); ?>">
                        <label for="webcu_custom_<?php echo esc_attr($field_id); ?>">
                            <?php echo esc_html($field_label); ?> <?php echo $required_mark; ?>
                        </label>
                        
                        <?php
                        // Render custom field based on type
                        switch ($field_type) {
                            case 'email':
                                ?>
                                <input type="email" 
                                       id="webcu_custom_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_custom_fields[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                            
                            case 'number':
                                ?>
                                <input type="number" 
                                       id="webcu_custom_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_custom_fields[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                            
                            case 'select':
                                $options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
                                ?>
                                <select id="webcu_custom_<?php echo esc_attr($field_id); ?>" 
                                        name="webcu_custom_fields[<?php echo esc_attr($field_id); ?>]" 
                                        class="webcu-form-control" 
                                        <?php echo esc_attr($required_attr); ?>>
                                    <option value=""><?php echo esc_html__('Select Option', 'mega-events-manager'); ?></option>
                                    <?php foreach ($options_array as $option) : ?>
                                        <option value="<?php echo esc_attr($option); ?>"><?php echo esc_html($option); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php
                                break;
                            
                            case 'checkbox':
                                $options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
                                ?>
                                <div class="webcu-checkbox-group">
                                    <?php foreach ($options_array as $option) : ?>
                                        <label class="webcu-checkbox-label">
                                            <input type="checkbox" 
                                                   name="webcu_custom_fields[<?php echo esc_attr($field_id); ?>][]" 
                                                   value="<?php echo esc_attr($option); ?>" 
                                                   <?php echo esc_attr($required_attr); ?>>
                                            <?php echo esc_html($option); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <?php
                                break;
                            
                            case 'radio':
                                $options_array = !empty($field_options) ? array_map('trim', explode(',', $field_options)) : [];
                                ?>
                                <div class="webcu-radio-group">
                                    <?php foreach ($options_array as $option) : ?>
                                        <label class="webcu-radio-label">
                                            <input type="radio" 
                                                   name="webcu_custom_fields[<?php echo esc_attr($field_id); ?>]" 
                                                   value="<?php echo esc_attr($option); ?>" 
                                                   <?php echo esc_attr($required_attr); ?>>
                                            <?php echo esc_html($option); ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <?php
                                break;
                            
                            case 'text':
                            default:
                                ?>
                                <input type="text" 
                                       id="webcu_custom_<?php echo esc_attr($field_id); ?>" 
                                       name="webcu_custom_fields[<?php echo esc_attr($field_id); ?>]" 
                                       class="webcu-form-control" 
                                       <?php echo esc_attr($required_attr); ?>>
                                <?php
                                break;
                            }
                        ?>
                    </div>
                    <?php
                endforeach;
            endif;
            ?>
            
        </div>
        
    </div>
    <?php
}

webcu_display_attendee_form();
?>