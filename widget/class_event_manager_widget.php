<?php

/**
 *
 *  Wpcraft widget
 *
 **/

namespace Wpcraft\Widget;

class class_event_manager_widget{

    public function __construct() { 
      add_action('widgets_init', [$this,'wtmem_register_event_widgets']);
      add_action('widgets_init', [$this,'wtmem_load_widgets']);
      
    }

    public function wtmem_register_event_widgets() {

        register_sidebar( array(
            'name'          => __( 'Event Sidebar', 'momento-event-manager' ),
            'id'            => 'wtmem_event_sidebar',
            'description'   => __( 'Widgets for Event section.', 'momento-event-manager' ),
            'before_widget' => '<div class="wtmem-box">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="wtmem-title">',
            'after_title'   => '</h3>',
        ) );

        register_sidebar( array(
            'name'          => __( 'Event Organizer Sidebar', 'momento-event-manager' ),
            'id'            => 'wtmem_event_orgnizer_sidebar',
            'description'   => __( 'Widgets for Event Organizer section.', 'momento-event-manager' ),
            'before_widget' => '<div class="wtmem-box">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="wtmem-title">',
            'after_title'   => '</h3>',
        ) );

        register_sidebar( array(
            'name'          => __( 'Event Sponser Sidebar', 'momento-event-manager' ),
            'id'            => 'wtmem_event_sponser_sidebar',
            'description'   => __( 'Widgets for Event Sponser section.', 'momento-event-manager' ),
            'before_widget' => '<div class="wtmem-box">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="wtmem-title">',
            'after_title'   => '</h3>',
        ) );    
        
        register_sidebar( array(
            'name'          => __( 'Event Volunteer Sidebar', 'momento-event-manager' ),
            'id'            => 'wtmem_event_volunteer_sidebar',
            'description'   => __( 'Widgets for Event Volunteer section.', 'momento-event-manager' ),
            'before_widget' => '<div class="wtmem-box">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="wtmem-title">',
            'after_title'   => '</h3>',
        ) );  

    }

    
    public function wtmem_load_widgets() {

        require_once plugin_dir_path(__FILE__) . 'class_upcoming_event_organizer_widget.php';
        register_widget('class_upcoming_event_widget');

        require_once plugin_dir_path(__FILE__) . 'class_upcoming_event_sponser_widget.php';
        register_widget('class_upcoming_event_sponser_widget');

        require_once plugin_dir_path(__FILE__) . 'class_upcoming_event_volunteer_widget.php';
        register_widget('class_upcoming_event_volunteer_widget');

        require_once plugin_dir_path(__FILE__) . 'class_gmap_widget.php';
        register_widget( 'wtmem_gmap_Info_Widget' );
        
    }
       
}
new class_event_manager_widget();