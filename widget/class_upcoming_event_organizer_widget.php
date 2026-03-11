<?php

/**
 *
 *  Wpcraft event widget
 *
 **/
namespace Wpcraft\Widget;

class class_upcoming_event_widget extends \WP_Widget{

    public function __construct() {
        parent::__construct(
            'Class_upcoming_event_widget',
            __('Upcoming Organizer Events', 'ultimate-event-manager'),
            ['description' => __('Displays upcoming events for organizer.', 'ultimate-event-manager')]
        );
    }

    // FRONTEND OUTPUT
    public function widget($args, $instance) {

        echo $args['before_widget'];

        echo $args['before_title'] . __('Upcoming Events', 'ultimate-event-manager') . $args['after_title'];

        // Query events
        $events = get_posts([
            'post_type'      => 'ultimate_event',
            'posts_per_page' => 5,
            'orderby'        => 'date',
            'order'          => 'ASC'
        ]);

        echo '<ul>';

        if ($events) {
            $today = strtotime(date('Y-m-d'));

            foreach ($events as $event) {
                // Get organizer
                $organizer = get_post_meta($event->ID, 'wtmem_event_orga_name', true);
                $dates = get_post_meta($event->ID, 'wtmem_event_dates', true);

                if (!empty($dates['start_date'][0])) {

                    $start_date = $dates['start_date'][0];
                    $event_timestamp = strtotime($start_date);
                    // Show only upcoming events
                    if ($event_timestamp >= $today) {
                        $format_date = date('F j, Y', strtotime($start_date));

                        echo '<li>';
                        echo '<strong>' . esc_html($event->post_title) . '</strong><br>';
                        echo '<span>' . esc_html($format_date) . '</span>';
                        echo '</li>';
                    }
                }
            }

        } else {
            echo '<li>' . __('No upcoming events found.', 'ultimate-event-manager') . '</li>';
        }

        echo '</ul>';

        echo $args['after_widget'];
    }

    // BACKEND FORM – (Not required but keeping minimal)
    public function form($instance) {
        echo "<p>No settings required.</p>";
    }
}

