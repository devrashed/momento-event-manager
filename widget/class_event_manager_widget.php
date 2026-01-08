<?php
/**
 *
 *  webcartisan widget
 *
 **/

class class_event_manager_widget{

    public function __construct() { 
      add_action('widgets_init', [$this,'webcu_register_event_widgets']);
      add_action('widgets_init', [$this,'webcu_load_widgets']);
      
    }

    public function webcu_register_event_widgets() {

        register_sidebar( array(
            'name'          => __( 'Event Organizer Sidebar', 'ultimate-event-manager' ),
            'id'            => 'webcu_event_orgnizer_sidebar',
            'description'   => __( 'Widgets for Event Organizer section.', 'ultimate-event-manager' ),
            'before_widget' => '<div class="webcu-box">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="webcu-title">',
            'after_title'   => '</h3>',
        ) );

        register_sidebar( array(
            'name'          => __( 'Event Sponser Sidebar', 'ultimate-event-manager' ),
            'id'            => 'webcu_event_sponser_sidebar',
            'description'   => __( 'Widgets for Event Sponser section.', 'ultimate-event-manager' ),
            'before_widget' => '<div class="webcu-box">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="webcu-title">',
            'after_title'   => '</h3>',
        ) );    
        
        register_sidebar( array(
            'name'          => __( 'Event Volunteer Sidebar', 'ultimate-event-manager' ),
            'id'            => 'webcu_event_volunteer_sidebar',
            'description'   => __( 'Widgets for Event Volunteer section.', 'ultimate-event-manager' ),
            'before_widget' => '<div class="webcu-box">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="webcu-title">',
            'after_title'   => '</h3>',
        ) );  

    }

    
    public function webcu_load_widgets() {


        require_once plugin_dir_path(__FILE__) . 'class_upcoming_event_organizer_widget.php';
        register_widget('class_upcoming_event_widget');

        require_once plugin_dir_path(__FILE__) . 'class_upcoming_event_sponser_widget.php';
        register_widget('class_upcoming_event_sponser_widget');

        require_once plugin_dir_path(__FILE__) . 'class_upcoming_event_volunteer_widget.php';
        register_widget('class_upcoming_event_volunteer_widget');


    }
        

}