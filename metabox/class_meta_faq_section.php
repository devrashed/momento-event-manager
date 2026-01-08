<?php 
/**
 *
 *  Webcartisan F.A.Q
 *
 **/

class Class_meta_faq_section {

    private $meta_key = 'faq_faq';

    public function __construct() {}

    public function webcu_faq_fields($post) {
        
        $saved = get_post_meta($post->ID, $this->meta_key, true);
        ?>

        <div class="faq_wrapper">
            <ul class="faq_timeline-list" id="faq_timelineList">
                <?php
                if (!empty($saved)) {
                    foreach ($saved as $index => $item) { ?>
                        <li class="faq_timeline-item">
                            <div class="faq_timeline-top">
                                <button type="button" class="faq_btn faq_btn-expand">
                                   <span class="dashicons dashicons-arrow-down-alt2"></span> <?php echo esc_html__('Expand', 'mega-event-manager'); ?>
                                </button>
                                <button type="button" class="faq_btn faq_btn-remove">
                                   <span class="dashicons dashicons-no"></span>
                                </button>
                                <button type="button" class="faq_btn faq_btn-drag">
                                    <span class="dashicons dashicons-fullscreen-alt"></span>
                                </button>
                            </div>
                            <div class="faq_timeline-body" style="display:block;">
                                <label><?php echo esc_html__('Title', 'mega-event-manager'); ?></label>
                                <input type="text" name="faq_faq[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="faq_title">

                                <label><?php echo esc_html__('Description', 'mega-event-manager'); ?></label>
                                <?php
                                wp_editor(
                                    wp_kses_post($item['content']),
                                    'faq_faq_content_' . $index,
                                    [
                                        'textarea_name' => "faq_faq[$index][content]",
                                        'media_buttons' => true,
                                        'textarea_rows' => 5,
                                        'teeny'         => false,
                                        'tinymce'       => true,
                                    ]
                                );
                                ?>
                            </div>
                        </li>
                <?php }
                } else { ?>
                    <li class="faq_timeline-item">
                        <div class="faq_timeline-top">
                            <button type="button" class="faq_btn faq_btn-expand">
                               <span class="dashicons dashicons-arrow-down-alt2"></span> <?php echo esc_html__('Expand', 'mega-event-manager'); ?>
                            </button>
                            <button type="button" class="faq_btn faq_btn-remove">
                               <span class="dashicons dashicons-no"></span>
                            </button>
                            <button type="button" class="faq_btn faq_btn-drag">
                                <span class="dashicons dashicons-fullscreen-alt"></span>
                            </button>
                        </div>
                        <div class="faq_timeline-body" style="display:block;">
                            <label><?php echo esc_html__('Title', 'mega-event-manager'); ?></label>
                            <input type="text" name="faq_faq[0][title]" class="faq_title">

                            <label><?php echo esc_html__('Description', 'mega-event-manager'); ?></label>
                            <?php
                            wp_editor('', 'faq_faq_content_0', [
                                'textarea_name' => 'faq_faq[0][content]',
                                'media_buttons' => true,
                                'textarea_rows' => 5,
                            ]);
                            ?>
                        </div>
                    </li>
                <?php } ?>
            </ul>

            <button type="button" id="faq_addTimeline" class="faq_add-timeline">
               <span class="dashicons dashicons-plus"></span> <?php echo esc_html__('Add New F.A.Q', 'mega-event-manager'); ?>
            </button>
        </div>
        <?php
    }

    // Save meta
    public function webcu_save_meta_faq_details($post_id) {
        if (isset($_POST['faq_faq'])) {
            update_post_meta($post_id, $this->meta_key, wp_unslash($_POST['faq_faq'])); // phpcs:ignore
        }      

    }

}


