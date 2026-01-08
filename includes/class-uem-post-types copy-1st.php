<?php
/**
 * Post Types
 *
 * @package Ultimate_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UEM_Post_Types {
	
	/**
	 * Initialize post types
	 */
	public static function init() {
		self::webcu_register_post_types();
	}
	
	/**
	 * Register all custom post types
	 */
	public static function webcu_register_post_types() {
		self::webcu_register_event_post_type();
		self::webcu_register_organizer_post_type();
		self::webcu_register_volunteer_post_type();
		self::webcu_register_sponsor_post_type();
		self::webcu_register_registration_post_type();
		add_filter( 'manage_mem_event_posts_columns', [ self::class, 'webcu_all_event_columns' ] );
		add_action( 'manage_mem_event_posts_custom_column', [self::class, 'webcu_show_event_columns_value'], 10, 2 );
		add_action( 'woocommerce_order_status_changed', [self::class,'order_change_status']);

	}
	
	/**
	 * Register Event post type
	 */
	private static function webcu_register_event_post_type() {
		$labels = array(
			'name'                  => _x( 'Mega Events Manager', 'Post Type General Name', 'mega-event-manager' ),
			'singular_name'         => _x( 'Mega Events Manager', 'Post Type Singular Name', 'mega-event-manager' ),
			'menu_name'             => __( 'Mega Events Manager', 'mega-event-manager' ),
			'name_admin_bar'        => __( 'Mega Events Manager', 'mega-event-manager' ),
			'archives'              => __( 'Mega Events Manager Archives', 'mega-event-manager' ),
			'attributes'            => __( 'Mega Events Manager Attributes', 'mega-event-manager' ),
			'parent_item_colon'     => __( 'Parent Mega Events Manager:', 'mega-event-manager' ),
			'all_items'             => __( 'Mega Events Manager', 'mega-event-manager' ),
			'add_new_item'          => __( 'Add New Mega Events Manager', 'mega-event-manager' ),
			'add_new'               => __( 'Add New', 'mega-event-manager' ),
			'new_item'              => __( 'New Mega Events Manager', 'mega-event-manager' ),
			'edit_item'             => __( 'Edit Mega Events Manager', 'mega-event-manager' ),
			'update_item'           => __( 'Update Mega Events Manager', 'mega-event-manager' ),
			'view_item'             => __( 'View Mega Events Manager', 'mega-event-manager' ),
			'view_items'            => __( 'View Mega Events Manager', 'mega-event-manager' ),
			'search_items'          => __( 'Search Mega Events Manager', 'mega-event-manager' ),
			'not_found'             => __( 'Not found', 'mega-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mega-event-manager' ),
			'featured_image'        => __( 'Featured Image', 'mega-event-manager' ),
			'set_featured_image'    => __( 'Set featured image', 'mega-event-manager' ),
			'remove_featured_image' => __( 'Remove featured image', 'mega-event-manager' ),
			'use_featured_image'    => __( 'Use as featured image', 'mega-event-manager' ),
			'insert_into_item'      => __( 'Insert into event', 'mega-event-manager' ),
			'uploaded_to_this_item' => __( 'Uploaded to this event', 'mega-event-manager' ),
			'items_list'            => __( 'Mega Events Manager list', 'mega-event-manager' ),
			'items_list_navigation' => __( 'Mega Events Manager list navigation', 'mega-event-manager' ),
			'filter_items_list'     => __( 'Filter events list', 'mega-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Mega Events Manager', 'mega-event-manager' ),
			'description'           => __( 'Event post type', 'mega-event-manager' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-calendar-alt',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'show_in_rest'          => true,
			'rewrite'               => array( 'slug' => 'events' ),
		);
		
		register_post_type( 'mem_event', $args );
		self::webcu_register_eventCategory_taxonomy();
	}

	
	public static function webcu_register_eventCategory_taxonomy() {
        $labels = [
            'name'              => __('Event Category', 'mega-event-manager'),
            'singular_name'     => __('Event Category', 'mega-event-manager'),
            'menu_name'         => __('Event Category', 'mega-event-manager'),
            'search_items'      => __('Search Event Category', 'mega-event-manager'),
            'all_items'         => __('All Event Category', 'mega-event-manager'),
            'edit_item'         => __('Edit Event Category', 'mega-event-manager'),
            'update_item'       => __('Update Event Category', 'mega-event-manager'),
            'add_new_item'      => __('Add New Event Category', 'mega-event-manager'),
            'new_item_name'     => __('New Event Category Name', 'mega-event-manager'),
        ];

        register_taxonomy('event_category', ['mem_event'], [
            'hierarchical'      => true, 
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'rewrite'           => ['slug' => 'Eventcategory'],
        ]);
    }

	
	public static function webcu_get_product_sold_qty( $product_id ) {

		global $wpdb;

		if ( empty( $product_id ) ) {
			return 0;
		}

		$qty = $wpdb->get_var( $wpdb->prepare("
			SELECT SUM(qty_meta.meta_value)
			FROM {$wpdb->prefix}woocommerce_order_items AS order_items
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta
				ON order_items.order_item_id = product_meta.order_item_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS qty_meta
				ON order_items.order_item_id = qty_meta.order_item_id
			WHERE order_items.order_item_type = 'line_item'
			AND product_meta.meta_key = '_product_id'
			AND product_meta.meta_value = %d
			AND qty_meta.meta_key = '_qty'
		", $product_id ) );

		return (int) $qty;
	}

	public static function webcu_get_single_order_qty_if_released( $order_id, $product_id ) {

		if ( empty( $order_id ) || empty( $product_id ) ) {
			return 0;
		}

		$order = wc_get_order( $order_id );
	
		if ( ! $order ) {
			return 0;
		}

		$released_statuses = array(
			'cancelled',
			'refunded',
			'on-hold',
		);

		if ( ! in_array( $order->get_status(), $released_statuses, true ) ) {
			return 0; // Seat not released
		}

		$qty = 0;

		foreach ( $order->get_items() as $item ) {

			if ( (int) $item->get_product_id() === (int) $product_id ) {
				$qty += (int) $item->get_quantity();
			}
		}

		return (int) $qty;
	}

	public static function webcu_get_product_sales_report( $product_id ) {
		global $wpdb;

		if ( empty( $product_id ) ) {
			return array(
				'sold_qty' => 0,
				'refunded_qty' => 0,
				'net_qty' => 0
			);
		}

		// total sale quantity

		$sold_qty = $wpdb->get_var( $wpdb->prepare("
			SELECT SUM(qty_meta.meta_value)
			FROM {$wpdb->prefix}woocommerce_order_items AS order_items
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta
				ON order_items.order_item_id = product_meta.order_item_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS qty_meta
				ON order_items.order_item_id = qty_meta.order_item_id
			INNER JOIN {$wpdb->prefix}posts AS orders
				ON order_items.order_id = orders.ID
			WHERE order_items.order_item_type = 'line_item'
			AND product_meta.meta_key = '_product_id'
			AND product_meta.meta_value = %d
			AND qty_meta.meta_key = '_qty'
			AND orders.post_type = 'shop_order'
			AND orders.post_status IN ('wc-completed', 'wc-processing', 'wc-on-hold')
		", $product_id ) );

		// Total refund quantity

		$refunded_qty = $wpdb->get_var( $wpdb->prepare("
			SELECT SUM(qty_meta.meta_value)
			FROM {$wpdb->prefix}woocommerce_order_items AS order_items
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta
				ON order_items.order_item_id = product_meta.order_item_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS qty_meta
				ON order_items.order_item_id = qty_meta.order_item_id
			INNER JOIN {$wpdb->prefix}posts AS orders
				ON order_items.order_id = orders.ID
			WHERE order_items.order_item_type = 'line_item'
			AND product_meta.meta_key = '_product_id'
			AND product_meta.meta_value = %d
			AND qty_meta.meta_key = '_qty'
			AND orders.post_type = 'shop_order_refund'
			AND orders.post_status = 'wc-completed'
		", $product_id ) );

		$sold_qty = (int) $sold_qty;
		$refunded_qty = (int) $refunded_qty;
		$net_qty = $sold_qty - $refunded_qty;

		return array(
			'sold_qty' => $sold_qty,
			'refunded_qty' => $refunded_qty,
			'net_qty' => $net_qty
		);
	}


	public static function webcu_all_event_columns( $columns ) {
		$new = array();

		foreach ( $columns as $key => $label ) {

			$new[ $key ] = $label;

			if ( $key === 'title' ) {
				$new['start_event_dates'] = __( 'Start Event Dates', 'mega-event-manager' );
				$new['end_event_dates']   = __( 'End Event Dates', 'mega-event-manager' );
				$new['total_seat']   = __( 'Total Seat', 'mega-event-manager' );
				$new['booking_seat']   = __( 'Booking seat', 'mega-event-manager' );
				$new['available_seat']   = __( 'Available Seat', 'mega-event-manager' );				
			}
		}

		return $new;
	}

	public static function webcu_show_event_columns_value( $column, $post_id ) {

        $dates = get_post_meta($post_id, 'webcu_event_dates', true );

	
        $today = strtotime( date('Y-m-d') );

        if ( $column == 'start_event_dates' ) {

            if ( ! empty( $dates['start_date'] ) ) {

                foreach ( $dates['start_date'] as $i => $sd ) {

                    $start_date = $dates['start_date'][$i] ?? '';
                    $start_time = $dates['start_time'][$i] ?? '';

                    $end_date   = $dates['end_date'][$i]   ?? '';
                    $end_time   = $dates['end_time'][$i]   ?? '';

                    $start_ts = strtotime( $start_date );
                    $end_ts   = strtotime( $end_date );

                    // Determine color
                    if ( $today < $start_ts ) {
                        $color = 'green';     // Upcoming
                    } elseif ( $today >= $start_ts && $today <= $end_ts ) {
                        $color = 'orange';    // Running (yellow/orange)
                    } else {
                        $color = 'red';       // Past
                    }

                    $format_date = date( $dates['date_format'], strtotime( $start_date ) );
                    $format_time = date( $dates['time_format'], strtotime( $start_time ) );

                    echo "<span style='color:{$color}; font-weight:bold;'>{$format_date} {$format_time}</span><br>";
                }

            } else {
                echo '<em>No Dates</em>';
            }
        }

        if ( $column == 'end_event_dates' ) {

            if ( ! empty( $dates['end_date'] ) ) {

                foreach ( $dates['end_date'] as $i => $sd ) {

                    $start_date = $dates['start_date'][$i] ?? '';
                    $end_date   = $dates['end_date'][$i]   ?? '';

                    $start_ts = strtotime( $start_date );
                    $end_ts   = strtotime( $end_date );

                    // Determine color
                    if ( $today < $start_ts ) {
                        $color = 'green';     // Upcoming
                    } elseif ( $today >= $start_ts && $today <= $end_ts ) {
                        $color = 'orange';    // Running
                    } else {
                        $color = 'red';       // Past
                    }

                    $format_date = date( $dates['date_format'], strtotime( $end_date ) );
                    $format_time = date( $dates['time_format'], strtotime( $dates['end_time'][$i] ) );

                    echo "<span style='color:{$color}; font-weight:bold;'>{$format_date} {$format_time}</span><br>";
                }

			} else {
                echo '<em>No Dates</em>';
            }
        }

		 if ( $column == 'total_seat' ) {

			$totalseat = get_post_meta($post_id, '_webcu_tk_tickets', true );

			$ticket = reset( $totalseat );
			$quantity = $ticket['quantity'];
			echo esc_html( $quantity );

		 } if ( $column == 'booking_seat' ) {

			$products = get_post_meta( $post_id, '_uem_wc_products', true );
			
			$total_sold = 0;

				if ( is_array( $products ) ) {
					foreach ( $products as $product_id ) {
						$total_sold += self::webcu_get_product_sold_qty( (int) $product_id );
		    			//$order_id = self::webcu_get_order_ids_by_product_id( (int) $product_id );
						//$sales_report = self::webcu_get_product_sales_report( (int) $product_id );
						}
				}

			$total_sold = isset( $total_sold ) ? (int) $total_sold : 0;
		    echo esc_html( $total_sold );

          
		
		 } if($column == 'available_seat') {

			$totalseat = get_post_meta( $post_id, '_webcu_tk_tickets', true );
			$ticket    = is_array( $totalseat ) ? reset( $totalseat ) : [];
			$quantity  = isset( $ticket['quantity'] ) ? absint( $ticket['quantity'] ) : 0;

			$products    = get_post_meta( $post_id, '_uem_wc_products', true );
			$total_sold  = 0;

			if ( is_array( $products ) ) {
				foreach ( $products as $product_id ) {
					$total_sold += absint(
						self::webcu_get_product_sold_qty( (int) $product_id )
					);
				}
			}
			$remaining = max( 0, $quantity - $total_sold );
			echo esc_html( $remaining );
		}
    }





	/**
	 * Register Organizer post type
	 */
	private static function webcu_register_organizer_post_type() {
		$labels = array(
			'name'                  => _x( 'Organizers', 'Post Type General Name', 'mega-event-manager' ),
			'singular_name'         => _x( 'Organizer', 'Post Type Singular Name', 'mega-event-manager' ),
			'menu_name'             => __( 'Organizers', 'mega-event-manager' ),
			'name_admin_bar'        => __( 'Organizer', 'mega-event-manager' ),
			'archives'              => __( 'Organizer Archives', 'mega-event-manager' ),
			'all_items'             => __( 'All Organizers', 'mega-event-manager' ),
			'add_new_item'          => __( 'Add New Organizer', 'mega-event-manager' ),
			'add_new'               => __( 'Add New', 'mega-event-manager' ),
			'new_item'              => __( 'New Organizer', 'mega-event-manager' ),
			'edit_item'             => __( 'Edit Organizer', 'mega-event-manager' ),
			'update_item'           => __( 'Update Organizer', 'mega-event-manager' ),
			'view_item'             => __( 'View Organizer', 'mega-event-manager' ),
			'search_items'          => __( 'Search Organizer', 'mega-event-manager' ),
			'not_found'             => __( 'Not found', 'mega-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mega-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Organizer', 'mega-event-manager' ),
			'description'           => __( 'Organizer post type', 'mega-event-manager' ),
			'labels'                => $labels,
			'supports'              => array(  'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=mem_event',
			'menu_position'         => 6,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => array( 'slug' => 'organizers' ),
			'capability_type'       => 'post',
			'show_in_rest'          => true,
		);
		
		register_post_type( 'mem_organizer', $args );
	}
	
	/**
	 * Register Volunteer post type
	 */
	private static function webcu_register_volunteer_post_type() {
		$labels = array(
			'name'                  => _x( 'Volunteers', 'Post Type General Name', 'mega-event-manager' ),
			'singular_name'         => _x( 'Volunteer', 'Post Type Singular Name', 'mega-event-manager' ),
			'menu_name'             => __( 'Volunteers', 'mega-event-manager' ),
			'name_admin_bar'        => __( 'Volunteer', 'mega-event-manager' ),
			'archives'              => __( 'Volunteer Archives', 'mega-event-manager' ),
			'all_items'             => __( 'All Volunteers', 'mega-event-manager' ),
			'add_new_item'          => __( 'Add New Volunteer', 'mega-event-manager' ),
			'add_new'               => __( 'Add New', 'mega-event-manager' ),
			'new_item'              => __( 'New Volunteer', 'mega-event-manager' ),
			'edit_item'             => __( 'Edit Volunteer', 'mega-event-manager' ),
			'update_item'           => __( 'Update Volunteer', 'mega-event-manager' ),
			'view_item'             => __( 'View Volunteer', 'mega-event-manager' ),
			'search_items'          => __( 'Search Volunteer', 'mega-event-manager' ),
			'not_found'             => __( 'Not found', 'mega-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mega-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Volunteer', 'mega-event-manager' ),
			'description'           => __( 'Volunteer post type', 'mega-event-manager' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=mem_event',
			'menu_position'         => 7,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => array( 'slug' => 'volunteers' ),
			'capability_type'       => 'post',
			'show_in_rest'          => false,
		);
		
		register_post_type( 'mem_volunteer', $args );
	}
	
	/**
	 * Register Sponsor post type
	 */
	private static function webcu_register_sponsor_post_type() {
		$labels = array(
			'name'                  => _x( 'Sponsors', 'Post Type General Name', 'mega-event-manager' ),
			'singular_name'         => _x( 'Sponsor', 'Post Type Singular Name', 'mega-event-manager' ),
			'menu_name'             => __( 'Sponsors', 'mega-event-manager' ),
			'name_admin_bar'        => __( 'Sponsor', 'mega-event-manager' ),
			'archives'              => __( 'Sponsor Archives', 'mega-event-manager' ),
			'all_items'             => __( 'All Sponsors', 'mega-event-manager' ),
			'add_new_item'          => __( 'Add New Sponsor', 'mega-event-manager' ),
			'add_new'               => __( 'Add New', 'mega-event-manager' ),
			'new_item'              => __( 'New Sponsor', 'mega-event-manager' ),
			'edit_item'             => __( 'Edit Sponsor', 'mega-event-manager' ),
			'update_item'           => __( 'Update Sponsor', 'mega-event-manager' ),
			'view_item'             => __( 'View Sponsor', 'mega-event-manager' ),
			'search_items'          => __( 'Search Sponsor', 'mega-event-manager' ),
			'not_found'             => __( 'Not found', 'mega-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mega-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Sponsor', 'mega-event-manager' ),
			'description'           => __( 'Sponsor post type', 'mega-event-manager' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'thumbnail' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=mem_event',
			'menu_position'         => 8,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'rewrite'               => array( 'slug' => 'sponsors' ),
			'capability_type'       => 'post',
			'show_in_rest'          => false,
		);
		
		register_post_type( 'mem_sponsor', $args );
	}
	
	/**
	 * Register Event Registration post type
	 */
	private static function webcu_register_registration_post_type() {
		$labels = array(
			'name'                  => _x( 'Event Registrations', 'Post Type General Name', 'mega-event-manager' ),
			'singular_name'         => _x( 'Event Registration', 'Post Type Singular Name', 'mega-event-manager' ),
			'menu_name'             => __( 'Registrations', 'mega-event-manager' ),
			'name_admin_bar'        => __( 'Event Registration', 'mega-event-manager' ),
			'archives'              => __( 'Registration Archives', 'mega-event-manager' ),
			'all_items'             => __( 'All Registrations', 'mega-event-manager' ),
			'add_new_item'          => __( 'Add New Registration', 'mega-event-manager' ),
			'add_new'               => __( 'Add New', 'mega-event-manager' ),
			'new_item'              => __( 'New Registration', 'mega-event-manager' ),
			'edit_item'             => __( 'Edit Registration', 'mega-event-manager' ),
			'update_item'           => __( 'Update Registration', 'mega-event-manager' ),
			'view_item'             => __( 'View Registration', 'mega-event-manager' ),
			'search_items'          => __( 'Search Registration', 'mega-event-manager' ),
			'not_found'             => __( 'Not found', 'mega-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mega-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Event Registration', 'mega-event-manager' ),
			'description'           => __( 'Event Registration post type', 'mega-event-manager' ),
			'labels'                => $labels,
			'supports'              => array( 'title' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => 'edit.php?post_type=mem_event',
			'menu_position'         => 9,
			'show_in_admin_bar'     => false,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
			'show_in_rest'          => false,
		);
		
		register_post_type( 'mem_registration', $args );
	}
}


/* add_action( 'woocommerce_order_status_changed', function ( $order_id, $old, $new ) {

	if ( in_array( $new, array( 'cancelled', 'refunded', 'on-hold' ), true ) ) {

		$product_id = 86; // example
		$qty = Class_Name::webcu_get_single_order_qty_if_released( $order_id, $product_id );

		// Now adjust seat logic
	}
}, 10, 3 );

add_action( 'woocommerce_order_status_changed', 'order_change_status') */


/* public static function order_change_status( $order_id, $old, $new ){

	if ( in_array( $new, array( 'cancelled', 'refunded', 'on-hold' ), true ) ) {

		$product_id = 86; // example
		$qty = Class_Name::webcu_get_single_order_qty_if_released( $order_id, $product_id );


	}

} */