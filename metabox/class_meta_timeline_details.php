<?php 
namespace Wpcraft\Metabox;
/**
 *
 *  Webcartisan meta timeline
 *
 **/


class class_meta_timeline_details {

    private $meta_key = 'wtmem_timelines';

    public function wtmem_timeline_settings_field($post) {
        $saved = get_post_meta($post->ID, $this->meta_key, true);
        ?>

        <div class="wtmem_wrapper">
            <ul class="wtmem_timeline-list" id="wtmem_timelineList">
                <?php
                if (!empty($saved)) {
                    foreach ($saved as $index => $item) { ?>
                        <li class="wtmem_timeline-item">
                            <div class="wtmem_timeline-top">
                                <button type="button" class="wtmem_btn wtmem_btn-expand"> <span class="dashicons dashicons-arrow-down-alt2"></span> <?php echo esc_html__('Expand', 'momento-event-manager') ?></button>
                                <button type="button" class="wtmem_btn wtmem_btn-remove"><span class="dashicons dashicons-no"></span></button>
                                <button type="button" class="wtmem_btn wtmem_btn-drag"><span class="dashicons dashicons-fullscreen-alt"></i></button>
                            </div>
                            <div class="wtmem_timeline-body" style="display:block;">
                                <label><?php echo esc_html__('Title.', 'momento-event-manager') ?></label>
                                <input type="text" name="wtmem_timeline[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="wtmem_title">

                                <label><?php echo esc_html__('Description.', 'momento-event-manager') ?></label>
                                <?php
                                wp_editor(
                                    wp_kses_post($item['content']),
                                    'wtmem_content_' . $index,
                                    [
                                        'textarea_name' => "wtmem_timeline[$index][content]",
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
                    <li class="wtmem_timeline-item">
                        <div class="wtmem_timeline-top">
                            <button type="button" class="wtmem_btn wtmem_btn-expand"><span class="dashicons dashicons-arrow-down-alt2"></span><?php echo esc_html__('Expand', 'momento-event-manager') ?></button>
                            <button type="button" class="wtmem_btn wtmem_btn-remove"><span class="dashicons dashicons-no"></span></button>
                            <button type="button" class="wtmem_btn wtmem_btn-drag"><span class="dashicons dashicons-fullscreen-alt"></i></button>
                        </div>
                        <div class="wtmem_timeline-body" style="display:block;">
                            <label><?php echo esc_html__('Title', 'momento-event-manager') ?></label>
                            <input type="text" name="wtmem_timeline[0][title]" class="wtmem_title">

                            <label><?php echo esc_html__('Description', 'momento-event-manager') ?></label>
                            <?php
                            wp_editor('', 'wtmem_content_0', [
                                'textarea_name' => 'wtmem_timeline[0][content]',
                                'media_buttons' => true,
                                'textarea_rows' => 5,
                            ]);
                            ?>
                        </div>
                    </li>
                <?php } ?>
            </ul>

            <button type="button" id="wtmem_addTimeline" class="wtmem_add-timeline">
                <span class="dashicons dashicons-plus"></span> <?php echo esc_html__('Add New Timeline', 'momento-event-manager') ?>
            </button>
        </div>
      <?php
    }

    public function wtmem_save_meta_timeline_details($post_id) {
        if (isset($_POST['wtmem_timeline'])) {
            update_post_meta($post_id, $this->meta_key, wp_unslash( $_POST['wtmem_timeline'])); // phpcs:ignore 
        }
    }
    
}
      
