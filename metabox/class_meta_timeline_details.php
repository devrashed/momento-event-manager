<?php 
/**
 *
 *  Webcartisan meta timeline
 *
 **/


class Class_meta_timeline_details {

    private $meta_key = 'webcu_timelines';

    public function webcu_timeline_settings_field($post) {
        $saved = get_post_meta($post->ID, $this->meta_key, true);
        ?>

        <div class="webcu_wrapper">
            <ul class="webcu_timeline-list" id="webcu_timelineList">
                <?php
                if (!empty($saved)) {
                    foreach ($saved as $index => $item) { ?>
                        <li class="webcu_timeline-item">
                            <div class="webcu_timeline-top">
                                <button type="button" class="webcu_btn webcu_btn-expand"> <span class="dashicons dashicons-arrow-down-alt2"></span> <?php echo esc_html__('Expand', 'mega-event-manager') ?></button>
                                <button type="button" class="webcu_btn webcu_btn-remove"><span class="dashicons dashicons-no"></span></button>
                                <button type="button" class="webcu_btn webcu_btn-drag"><span class="dashicons dashicons-fullscreen-alt"></i></button>
                            </div>
                            <div class="webcu_timeline-body" style="display:block;">
                                <label><?php echo esc_html__('Title.', 'mega-event-manager') ?></label>
                                <input type="text" name="webcu_timeline[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="webcu_title">

                                <label><?php echo esc_html__('Description.', 'mega-event-manager') ?></label>
                                <?php
                                wp_editor(
                                    wp_kses_post($item['content']),
                                    'webcu_content_' . $index,
                                    [
                                        'textarea_name' => "webcu_timeline[$index][content]",
                                        'media_buttons' => true,
                                        'textarea_rows' => 5,
                                        'teeny'         => false,
                                        'tinymce'       => true,
                                    ]
                                );
                                ?>
                            </div>
                        </li>
                <?php   }
                } else { ?>
                    <li class="webcu_timeline-item">
                        <div class="webcu_timeline-top">
                            <button type="button" class="webcu_btn webcu_btn-expand"><span class="dashicons dashicons-arrow-down-alt2"></span><?php echo esc_html__('Expand', 'mega-event-manager') ?></button>
                            <button type="button" class="webcu_btn webcu_btn-remove"><span class="dashicons dashicons-no"></span></button>
                            <button type="button" class="webcu_btn webcu_btn-drag"><span class="dashicons dashicons-fullscreen-alt"></i></button>
                        </div>
                        <div class="webcu_timeline-body" style="display:block;">
                            <label><?php echo esc_html__('Title', 'mega-event-manager') ?></label>
                            <input type="text" name="webcu_timeline[0][title]" class="webcu_title">

                            <label><?php echo esc_html__('Description', 'mega-event-manager') ?></label>
                            <?php
                            wp_editor('', 'webcu_content_0', [
                                'textarea_name' => 'webcu_timeline[0][content]',
                                'media_buttons' => true,
                                'textarea_rows' => 5,
                            ]);
                            ?>
                        </div>
                    </li>
                <?php } ?>
            </ul>

            <button type="button" id="webcu_addTimeline" class="webcu_add-timeline">
                <span class="dashicons dashicons-plus"></span> <?php echo esc_html__('Add New Timeline', 'mega-event-manager') ?>
            </button>
        </div>
      <?php
    }

    public function webcu_save_meta_timeline_details($post_id) {
        if (isset($_POST['webcu_timeline'])) {
            update_post_meta($post_id, $this->meta_key, wp_unslash( $_POST['webcu_timeline'])); // phpcs:ignore 
        }
    }
    
}
      
