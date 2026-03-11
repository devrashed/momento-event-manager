<?php 
namespace Wpcraft\Metabox;
/**
 *
 *  Webcartisan terms_condition
 *
 **/

class class_meta_terms_conditions { 

    public function __construct() {}

    public function wtmem_terms_condition_field($post) { 

        $display_tc   = get_post_meta($post->ID, 'display_tc', true);
        $tcs          = get_post_meta($post->ID, 'tc_items', true);
        $display_checked = !empty($display_tc) ? 'checked' : '';
        $tcs = is_array($tcs) ? $tcs : [];
        ?>

            <div class="wtmem_tc-wrapper">
                <label><strong><?php echo esc_html__('Display Term & Condition?', 'momento-event-manager') ?></strong></label> 
                <input type="checkbox" name="display_tc" id="display_tc" <?php echo $display_checked; ?>>
                <?php echo esc_html__('Yes', 'momento-event-manager') ?>
                
                <p><?php echo esc_html__('If you want to display Term and Condition please check this Yes', 'momento-event-manager') ?></p>
                
                <div id="wtmem_tc_list">
                    <?php if (!empty($tcs)) :
                foreach ($tcs as $index => $tc) : ?>
                    <div class="wtmem_tc-item" data-index="<?php echo esc_attr($index); ?>">
                        <div class="wtmem_tc-header">
                            <button type="button" class="wtmem_tc-expand"><?php echo esc_html__('Expand', 'momento-event-manager') ?></button>
                            <button type="button" class="wtmem_tc-delete"><span class="dashicons dashicons-no"></span></button>
                            <span class="wtmem_tc-drag"><span class="dashicons dashicons-fullscreen-alt"></span></span>
                        </div>
                        <div class="wtmem_tc-body">
                            <label><?php echo esc_html__('Required?', 'momento-event-manager') ?></label>
                            <select name="tc_required[<?php echo esc_attr($index); ?>]">
                                <option value=""><?php echo esc_html__('Required?', 'momento-event-manager') ?></option>
                                <option value="yes" <?php selected($tc['required'], 'yes'); ?>><?php echo esc_html__('Yes', 'momento-event-manager') ?></option>
                                <option value="no" <?php selected($tc['required'], 'no'); ?>><?php echo esc_html__('No', 'momento-event-manager') ?></option>
                            </select>

                            <label> <?php echo esc_html__('Label / Title', 'momento-event-manager') ?></label>
                            <input type="text" name="tc_title[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($tc['title']); ?>">

                            <label><?php echo esc_html__('Terms & condition Description', 'momento-event-manager') ?></label>
                            <input type="text" name="tc_url[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($tc['url']); ?>">
                        </div>
                    </div>
                <?php endforeach;
                else : ?>
                <div class="wtmem_tc-item" data-index="0">
                    <div class="wtmem_tc-header">
                        <button type="button" class="wtmem_btn wtmem_tc-expand"><span class="dashicons dashicons-arrow-down-alt2"></span><?php echo esc_html__('Expand', 'momento-event-manager') ?></button>

                        <button type="button" class="wtmem_tc-delete"><span class="dashicons dashicons-no"></span></button>
                        <span class="wtmem_tc-drag"> <span class="dashicons dashicons-fullscreen-alt"></span> </span>
                    </div>
                    <div class="wtmem_tc-body">
                        <label><?php echo esc_html__('Required?', 'momento-event-manager') ?></label>
                        <select name="tc_required[0]">
                            <option value="" selected>Required?</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>

                        <label><?php echo esc_html__('Label / Title', 'momento-event-manager') ?></label>
                        <input type="text" name="tc_title[0]" placeholder="I agree with the terms & conditions">

                        <label><?php echo esc_html__('Terms & condition Description', 'momento-event-manager') ?></label>
                        <input type="text" name="tc_url[0]" placeholder="">
                    </div>
                </div>
               <?php endif; ?>
            </div>
            <button type="button" id="wtmem_add_new_tc" class="wtmem_add-btn"><?php echo esc_html__('+ Add New Term & Condition', 'momento-event-manager') ?></button>
        </div>
    <?php
    }
    
    public function wtmem_save_meta_terms_conditions($post_id){

        $display_val = isset($_POST['display_tc']) ? 1 : 0;
        update_post_meta($post_id, 'display_tc', $display_val);

        $required = isset( $_POST['tc_required'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['tc_required'] ) ) : [];
        $titles = isset( $_POST['tc_title'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['tc_title'] ) ): [];
        $urls = isset( $_POST['tc_url'] ) ? array_map( 'sanitize_text_field', (array) wp_unslash( $_POST['tc_url'] ) ) : []; 

        $clean = [];
        
        foreach ($titles as $index => $title) {
            $title = sanitize_text_field($title);
            $req   = isset($required[$index]) ? sanitize_text_field($required[$index]) : '';
            $url   = isset($urls[$index]) ? esc_url_raw($urls[$index]) : '';

            // Skip empty blocks
            if ($title === '' && $req === '' && $url === '') {
                continue;
            }

            $clean[] = [
                'required' => $req,
                'title'    => $title,
                'url'      => $url
            ];
        }

        if (!empty($clean)) {
            update_post_meta($post_id, 'tc_items', $clean);
        } else {
            delete_post_meta($post_id, 'tc_items');
        }
    }
    
}
  
?>
