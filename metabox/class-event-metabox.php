<?php 
/**
 *
 *  Webcartisan event custom metabox
 *
 **/

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

    public function __construct() { 

        $this->photogallery = new class_event_meta_photogallery();

        add_action('add_meta_boxes', [$this, 'webcu_event_meta_field']);
        add_action('save_post', [$this, 'webcu_save_event_registration_meta']);
        add_action('add_meta_boxes', [$this->photogallery,'webcu_add_gallery_meta_box']);
        add_action('save_post_mem_event', [$this->photogallery,'webcu_save_gallery_meta']);  

    }

    public function webcu_event_meta_field() {

        add_meta_box(
            'event_details_metabox',
            __('Event Information', 'mega-events-manager'),
            [$this, 'webcu_render_event_details_metabox'],
            'mem_event',
            'normal',
            'high'
        );
    }

    public function webcu_render_event_details_metabox($post) {
        // Nonce for verification when saving
        wp_nonce_field('save_event_registration_meta', 'event_registration_meta_nonce');
        ?>
            <div class="event-metabox-wrapper">
                <!-- Sidebar Navigation -->
                <div class="event-sidebar">
                    <ul>
                        <li class="active" data-tab="venue"><?php echo esc_html__('Venue / Location', 'mega-events-manager') ?> </li>
                        <li data-tab="ticket"> <?php echo esc_html__('Ticket & Pricing', 'mega-events-manager') ?></li>
                        <li data-tab="datetime"> <?php echo esc_html__('Date & Time', 'mega-events-manager') ?> </li>
                        <li data-tab="settings"> <?php echo esc_html__('Settings', 'mega-events-manager') ?>  </li>
                        <!-- <li data-tab="richtext"> <?php //echo esc_html__('Rich Text', 'mega-events-manager') ?>  </li> -->
                        <li data-tab="emails"> <?php echo esc_html__('Emails', 'mega-events-manager') ?></li>
                        <li data-tab="regis"> <?php echo esc_html__('Registration Form', 'mega-events-manager') ?></li>
                        <li data-tab="attendee_form"> <?php echo esc_html__('Attendee Form', 'mega-events-manager') ?> </li>
                        <li data-tab="faq"> <?php echo esc_html__('F.A.Q', 'mega-events-manager') ?> </li>
                        <!-- <li data-tab="timeline_details"> <?php //echo esc_html__('Additional content', 'mega-events-manager') ?></li> -->
                        <li data-tab="terms_conditions"> <?php echo esc_html__('Terms & Conditios', 'mega-events-manager') ?>  </li>
                        <li data-tab="event_assot"> <?php echo esc_html__('Event Associates', 'mega-events-manager') ?>  </li>
                    </ul>
                </div>

                <!-- Right Colmun -->

                <div class="event-content">
                    <!-- Venue Tab -->
                    <div class="event-tab active" id="venue">
                        <h3><?php echo esc_html__('Venue / Location', 'mega-events-manager'); ?></h3>
                        <?php     
                            $venuelocation = new Class_meta_venue_location();
                            $venuelocation->webcu_venue_location_field($post);
                        ?>   
                    </div>
                    <!-- Ticket Tab -->
                    <div class="event-tab" id="ticket">
                        <h3><?php echo esc_html__('Ticket & Pricing', 'mega-events-manager') ?></h3>
    
                        <?php 
                            $ticket = new Class_meta_ticket_price();
                            $ticket->webcu_ticket_price_sections($post);  
                        ?>
                    </div>

                    <!-- Date & Time Tab -->
                    <div class="event-tab" id="datetime">
                       <h3><?php echo esc_html__('Events Date & Time', 'mega-events-manager') ?></h3>        
                        <?php
                           $datetime = new Class_meta_dateTime_section();
                           $datetime->webcu_meta_dateTime_field($post);
                        ?>
                    </div>
                    <!-- Settings Tab -->
                    <div class="event-tab" id="settings">
                        <h3><?php echo esc_html__('Events Settings:', 'mega-events-manager') ?></h3>         
                        <?php                         
                            $settings = new Class_meta_settings_sections();
                            $settings->webcu_settings_sections($post);
                        ?>
                    </div>  

                    <!-- Rich Text -->
                    <!-- <div class="event-tab" id="richtext">
                         <h3><?php //echo esc_html__('Events Rich Texts for SEO & Google Schema Text :', 'mega-events-manager') ?></h3>
                         <?php 
                           /*  $richtext = new Class_meta_richtext_section();
                            $richtext->webcu_richtext_fields($post); */
                         ?>
                    </div>   -->
             
                    <!-- Emails -->
                    <div class="event-tab" id="emails">
                        <h3><?php echo esc_html__('Emails', 'mega-events-manager') ?></h3>
                        <?php 
                            $reminder = new Class_meta_emails_section(); 
                            $reminder->webcu_meta_emails_field($post);
                         ?>    
                    </div>

                    <!-- Registration Form -->  

                    <div class="event-tab" id="regis">
                        <h3><?php echo esc_html__('Registration Form', 'mega-events-manager') ?></h3>
                        <?php 
                            $registra = new Class_meta_registration_form(); 
                            $registra->webcu_meta_registration_form_field($post);
                         ?>    
                    </div>


                    <div class="event-tab" id="attendee_form">
                        <h3><?php echo esc_html__('Attendee Form', 'mega-events-manager') ?></h3>
                        <?php
                            $attendeeform = new Class_meta_attendee_form();
                            $attendeeform->webcu_attendee_form($post);                                    
                        ?> 
                    </div>
                    <!-- F.A.Q -->    
                    <div class="event-tab" id="faq">
                        <h3><?php echo esc_html__('F.A.Q', 'mega-events-manager') ?></h3>
                         <?php 
                            $faq = new Class_meta_faq_section();
                            $faq->webcu_faq_fields($post);
                         ?>
                    </div>

                    <!-- timeline details -->
                    <!-- <div class="event-tab" id="timeline_details">
                        <h3><?php //echo esc_html__('Timeline Details', 'mega-events-manager') ?></h3>
                        <?php 
                            /* $timeline = new Class_meta_timeline_details();
                            $timeline->webcu_timeline_settings_field($post); */
                        ?>
                    </div> -->

                    <!-- terms_conditions -->
                    <div class="event-tab" id="terms_conditions">
                        <?php
                            $termscondition = new Class_meta_terms_conditions();
                            $termscondition->webcu_terms_condition_field($post);
                        ?>     
                    </div>
                    
                      <!-- event_associates -->
                    <div class="event-tab" id="event_assot">
                         <h3><?php echo esc_html__('Event Associates', 'mega-events-manager') ?></h3>
                        <?php
                            $associate = new Class_meta_event_associate();
                            $associate->webcu_event_associate_meta_field($post);
                        ?>     
                    </div> 
            </div>
        <?php
    }

    public function webcu_save_event_registration_meta($post_id) {

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

        $venuelocation = new Class_meta_venue_location();
        $venuelocation->webcu_save_meta_venue_location($post_id);

        /* === Ticket Price ===== */

        $attendeeform = new Class_meta_ticket_price();   
        $attendeeform->webcu_save_meta_ticket_price($post_id);

        /* === Registration Form ===== */

        $attendeeform = new Class_meta_registration_form();   
        $attendeeform->webcu_save_regitype_form($post_id);   

        /* === Attendee Form ===== */

        $attendeeform = new Class_meta_attendee_form();   
        $attendeeform->webcu_save_attendee_form($post_id);       
        
        /* === Date & Time ===== */

        $datetime = new Class_meta_dateTime_section();
        $datetime->webcu_save_meta_dateTime_data($post_id);

        /* ====== Setting sections ======= */ 

        $settings = new Class_meta_settings_sections();    
        $settings->webcu_save_meta_settings($post_id);

        /* ====== Rich text sections ======= */ 

        /* $richtext = new Class_meta_richtext_section();    
        $richtext->webcu_save_meta_richtext($post_id); */

        /* ====== Emails sections ======= */ 

        $richtext = new Class_meta_emails_section();    
        $richtext->webcu_save_emails_metabox_data($post_id);

        /* ====== F.A.Q ======= */
        
        $timeline = new Class_meta_faq_section();
        $timeline->webcu_save_meta_faq_details($post_id);

        /* ====== Time line & Details ======= */
        
     /*    $timeline = new Class_meta_timeline_details();
        $timeline->webcu_save_meta_timeline_details($post_id); */

        /* ====== terms & conditions ======= */ 

        $termscondition = new Class_meta_terms_conditions();
        $termscondition->webcu_save_meta_terms_conditions($post_id);

        /* ====== Associated ======= */ 

        $associate = new Class_meta_event_associate();
        $associate->webcu_save_event_associated_field($post_id);
    } 

    

}