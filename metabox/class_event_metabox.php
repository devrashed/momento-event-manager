<?php 
/**
 *
 *  Webcartisan event custom metabox
 *
 **/

namespace Wpcraft\Metabox;

class class_event_custom_metabox {

    private $ticket;
    private $datetime;
    private $attendeeform;
    private $venuelocation;
    private $settings;
    private $richtext;
    private $termscondition;
    private $timeline;
    private $faq;
    private $emailtext;
    private $reminder;
    private $photogallery;
    private $associate;
    private $media;

    public function __construct() { 

        $this->photogallery = new class_event_meta_photogallery();

        add_action('add_meta_boxes', [$this, 'wtmem_event_meta_field']);

        add_action('save_post', [$this, 'wtmem_save_event_registration_meta']);
        // Removed sidebar gallery metabox - gallery is now in Media tab.
        // add_action('add_meta_boxes', [$this->photogallery,'wtmem_add_gallery_meta_box']);
        // add_action('save_post_mem_event', [$this->photogallery,'wtmem_save_gallery_meta']);  

    }

    public function wtmem_event_meta_field() {
        add_meta_box(
            'event_details_metabox',
            __('Event Information', 'momento-event-manager'),
            [$this, 'wtmem_render_event_details_metabox'],
            'mem_event',
            'normal',
            'high'
        );
    }
        


    public function wtmem_render_event_details_metabox($post) {
        // Nonce for verification when saving
        wp_nonce_field('save_event_registration_meta', 'event_registration_meta_nonce');
        ?>
            <div class="event-metabox-wrapper">
                <!-- Sidebar Navigation -->
                <div class="event-sidebar">
                    <ul>
                        <li class="active" data-tab="venue"><?php echo esc_html__('Venue / Location', 'momento-event-manager') ?> </li>
                        <li data-tab="ticket"> <?php echo esc_html__('Ticket & Pricing', 'momento-event-manager') ?></li>
                        <li data-tab="datetime"> <?php echo esc_html__('Date & Time', 'momento-event-manager') ?> </li>
                        <li data-tab="settings"> <?php echo esc_html__('Settings', 'momento-event-manager') ?>  </li>
                        <!-- <li data-tab="richtext"> <?php //echo esc_html__('Rich Text', 'momento-event-manager') ?>  </li> -->
                        <li data-tab="emails"> <?php echo esc_html__('Emails', 'momento-event-manager') ?></li>
                        <li data-tab="regis"> <?php echo esc_html__('Registration Form', 'momento-event-manager') ?></li>
                        <li data-tab="attendee_form"> <?php echo esc_html__('Attendee Form', 'momento-event-manager') ?> </li>
                        <li data-tab="faq"> <?php echo esc_html__('F.A.Q', 'momento-event-manager') ?> </li>
                        <li data-tab="media"> <?php echo esc_html__('Media', 'momento-event-manager') ?> </li>
                        <!-- <li data-tab="timeline_details"> <?php //echo esc_html__('Additional content', 'momento-event-manager') ?></li> -->
                        <li data-tab="terms_conditions"> <?php echo esc_html__('Terms & Conditios', 'momento-event-manager') ?>  </li>
                        <!-- <li data-tab="event_assot"> <?php //echo esc_html__('Event Associates', 'momento-event-manager') ?>  </li> -->
                    </ul>
                </div>

                <!-- Right Colmun -->

                <div class="event-content">
                    <!-- Venue Tab -->
                    <div class="event-tab active" id="venue">
                        <h3><?php echo esc_html__('Venue / Location', 'momento-event-manager'); ?></h3>
                        <?php     
                            $venuelocation = new Class_meta_venue_location();
                            $venuelocation->wtmem_venue_location_field($post);
                        ?>   
                    </div>
                    <!-- Ticket Tab -->
                    <div class="event-tab" id="ticket">
                        <h3><?php echo esc_html__('Ticket & Pricing', 'momento-event-manager') ?></h3>
    
                        <?php 
                            $ticket = new Class_meta_ticket_price();
                            $ticket->wtmem_ticket_price_sections($post);  
                        ?>
                    </div>

                    <!-- Date & Time Tab -->
                    <div class="event-tab" id="datetime">
                       <h3><?php echo esc_html__('Events Date & Time', 'momento-event-manager') ?></h3>        
                        <?php
                           $datetime = new Class_meta_dateTime_section();
                           $datetime->wtmem_meta_dateTime_field($post);
                        ?>
                    </div>
                    <!-- Settings Tab -->
                    <div class="event-tab" id="settings">
                        <h3><?php echo esc_html__('Events Settings:', 'momento-event-manager') ?></h3>         
                        <?php                         
                            $settings = new Class_meta_settings_sections();
                            $settings->wtmem_settings_sections($post);
                        ?>
                    </div>  

                    <!-- Rich Text -->
                    <!-- <div class="event-tab" id="richtext">
                         <h3><?php //echo esc_html__('Events Rich Texts for SEO & Google Schema Text :', 'momento-event-manager') ?></h3>
                         <?php 
                           /*  $richtext = new Class_meta_richtext_section();
                            $richtext->wtmem_richtext_fields($post); */
                         ?>
                    </div>   -->
             
                    <!-- Emails -->
                    <div class="event-tab" id="emails">
                        <h3><?php echo esc_html__('Emails', 'momento-event-manager') ?></h3>
                        <?php 
                            $reminder = new Class_meta_emails_section(); 
                            $reminder->wtmem_meta_emails_field($post);
                         ?>    
                    </div> 

                    <!-- Registration Form -->

                    <div class="event-tab" id="regis">
                        <h3><?php echo esc_html__('Registration Form', 'momento-event-manager') ?></h3>
                        <?php 
                            $registra = new Class_meta_registration_form(); 
                            $registra->wtmem_meta_registration_form_field($post);
                         ?>    
                    </div>


                    <div class="event-tab" id="attendee_form">
                        <h3><?php echo esc_html__('Attendee Form', 'momento-event-manager') ?></h3>
                        <?php
                            $attendeeform = new Class_meta_attendee_form();
                            $attendeeform->wtmem_attendee_form($post);                                    
                        ?> 
                    </div>
                    <!-- F.A.Q -->    
                    <div class="event-tab" id="faq">
                        <h3><?php echo esc_html__('F.A.Q', 'momento-event-manager') ?></h3>
                         <?php 
                            $faq = new Class_meta_faq_section();
                            $faq->wtmem_faq_fields($post);
                         ?>
                    </div>

                    <!-- timeline details -->
                    <!-- <div class="event-tab" id="timeline_details">
                        <h3><?php //echo esc_html__('Timeline Details', 'momento-event-manager') ?></h3>
                        <?php 
                            /* $timeline = new Class_meta_timeline_details();
                            $timeline->wtmem_timeline_settings_field($post); */
                        ?>
                    </div> -->

                    <!-- terms_conditions -->
                    <div class="event-tab" id="terms_conditions">
                        <?php
                            $termscondition = new Class_meta_terms_conditions();
                            $termscondition->wtmem_terms_condition_field($post);
                        ?>     
                    </div>
                    
                    <!-- media -->
                    <div class="event-tab" id="media">
                        <?php
                            $media = new Class_meta_event_media();
                            $media->wtmem_event_media_meta_field($post);
                        ?>     
                    </div>


                      <!-- event_associates -->
                    <!-- <div class="event-tab" id="event_assot">
                         <h3><?php echo esc_html__('Event Associates', 'momento-event-manager') ?></h3>
                        <?php
                            $associate = new Class_meta_event_associate();
                            $associate->wtmem_event_associate_meta_field($post);
                        ?>     
                    </div> --> 
            </div>
        <?php
    }

    public function wtmem_save_event_registration_meta($post_id) {

        // Stop autosave, quick edit, bulk edit
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (! isset($_POST['event_registration_meta_nonce']) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['event_registration_meta_nonce'])), 
                'save_event_registration_meta'
            )
        ) {
            return;
        }   
        // Ensure only for our post type
        if (get_post_type($post_id) != 'mem_event') {
            return;
        }

        if ($parent_id = wp_is_post_revision($post_id)) {
            $post_id = $parent_id;
        }

        /* === Venue/Virtual Event ===== */

        $venuelocation = new class_meta_venue_location();
        $venuelocation->wtmem_save_meta_venue_location($post_id);

        /* === Ticket Price ===== */

        $attendeeform = new class_meta_ticket_price();   
        $attendeeform->wtmem_save_meta_ticket_price($post_id);

        /* === Registration Form ===== */

        $attendeeform = new class_meta_registration_form();   
        $attendeeform->wtmem_save_regitype_form($post_id);   

        /* === Attendee Form ===== */

        $attendeeform = new class_meta_attendee_form();   
        $attendeeform->wtmem_save_attendee_form($post_id);       
        
        /* === Date & Time ===== */

        $datetime = new class_meta_datetime_section();
        $datetime->wtmem_save_meta_datetime_data($post_id);

        /* ====== Setting sections ======= */ 

        $settings = new class_meta_settings_sections();    
        $settings->wtmem_save_meta_settings($post_id);

        /* ====== Rich text sections ======= */ 

        /* $richtext = new Class_meta_richtext_section();    
        $richtext->wtmem_save_meta_richtext($post_id); */

        /* ====== Emails sections ======= */ 

        $richtext = new class_meta_emails_section();    
        $richtext->wtmem_save_emails_metabox_data($post_id);

        /* ====== F.A.Q ======= */

        $timeline = new class_meta_faq_section();
        $timeline->wtmem_save_meta_faq_details($post_id);

        /* ====== Time line & Details ======= */
        
     /*    $timeline = new Class_meta_timeline_details();
        $timeline->wtmem_save_meta_timeline_details($post_id); */

        /* ====== terms & conditions ======= */ 

        $termscondition = new class_meta_terms_conditions();
        $termscondition->wtmem_save_meta_terms_conditions($post_id);

        /* ====== Media ======= */
        // Save hero banner, gallery, and video fields.
        if ( isset( $_POST['wtmem_event_media_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wtmem_event_media_nonce'] ) ), 'wtmem_event_media_meta' ) ) {
            // Hero banner.
            if ( isset( $_POST['event_image_id'] ) ) {
                update_post_meta( $post_id, '_event_image_id', sanitize_text_field( wp_unslash( $_POST['event_image_id'] ) ) );
            }
            
            // Event photo gallery (using same meta key as old sidebar gallery).
            if ( isset( $_POST['wtmem_gallery_ids'] ) ) {
                update_post_meta( $post_id, '_wtmem_gallery_ids', sanitize_text_field( wp_unslash( $_POST['wtmem_gallery_ids'] ) ) );
            }

            // Video type.
            if ( isset( $_POST['wtmem_events_video_type'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_video_type', sanitize_text_field( wp_unslash( $_POST['wtmem_events_video_type'] ) ) );
            }
            
            // YouTube URL.
            if ( isset( $_POST['wtmem_events_youtube_url'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_youtube_url', esc_url_raw( wp_unslash( $_POST['wtmem_events_youtube_url'] ) ) );
            }
            
            // Vimeo URL.
            if ( isset( $_POST['wtmem_events_vimeo_url'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_vimeo_url', esc_url_raw( wp_unslash( $_POST['wtmem_events_vimeo_url'] ) ) );
            }

            // Self-hosted video.
            if ( isset( $_POST['wtmem_events_self_video_id'] ) ) {
                update_post_meta( $post_id, '_wtmem_events_self_video_id', absint( wp_unslash( $_POST['wtmem_events_self_video_id'] ) ) );
            }
        }

        /* ====== Associated ======= */ 

        /* $associate = new Class_meta_event_associate();
        $associate->wtmem_save_event_associated_field($post_id); */
    }

}

new class_event_custom_metabox();