<?php
/**
 * Post Types
 *
 */
namespace Wpcraft\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class class_mem_post_types {	
	/**
	 * Initialize post types
	 */
	public static function init() {
		self::wtmem_register_post_types();
	}
	
	/**
	 * Register all custom post types
	 */
	public static function wtmem_register_post_types() {
		self::wtmem_register_event_post_type();
		self::wtmem_register_organizer_post_type();
		self::wtmem_register_volunteer_post_type();
		self::wtmem_register_sponsor_post_type();
		self::wtmem_register_registration_post_type();
		add_filter( 'manage_mem_event_posts_columns', [ self::class, 'wtmem_all_event_columns' ] );
		add_action( 'manage_mem_event_posts_custom_column', [self::class, 'wtmem_show_event_columns_value'], 10, 2 );
	
		add_action( 'admin_post_wtmem_download_attendee_csv',  [self::class, 'download_attendee_csv']);
	}
	
	/**
	 * Register Event post type
	 */
	private static function wtmem_register_event_post_type() {
		$labels = array(
			'name'                  => _x( 'Mega Events Manager', 'Post Type General Name', 'momento-event-manager' ),
			'singular_name'         => _x( 'Mega Events Manager', 'Post Type Singular Name', 'momento-event-manager' ),
			'menu_name'             => __( 'Mega Events Manager', 'momento-event-manager' ),
			'name_admin_bar'        => __( 'Mega Events Manager', 'momento-event-manager' ),
			'archives'              => __( 'Mega Events Manager Archives', 'momento-event-manager' ),
			'attributes'            => __( 'Mega Events Manager Attributes', 'momento-event-manager' ),
			'parent_item_colon'     => __( 'Parent Mega Events Manager:', 'momento-event-manager' ),
			'all_items'             => __( 'Mega Events Manager', 'momento-event-manager' ),
			'add_new_item'          => __( 'Add New Mega Events Manager', 'momento-event-manager' ),
			'add_new'               => __( 'Add New', 'momento-event-manager' ),
			'new_item'              => __( 'New Mega Events Manager', 'momento-event-manager' ),
			'edit_item'             => __( 'Edit Mega Events Manager', 'momento-event-manager' ),
			'update_item'           => __( 'Update Mega Events Manager', 'momento-event-manager' ),
			'view_item'             => __( 'View Mega Events Manager', 'momento-event-manager' ),
			'view_items'            => __( 'View Mega Events Manager', 'momento-event-manager' ),
			'search_items'          => __( 'Search Mega Events Manager', 'momento-event-manager' ),
			'not_found'             => __( 'Not found', 'momento-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'momento-event-manager' ),
			'featured_image'        => __( 'Featured Image', 'momento-event-manager' ),
			'set_featured_image'    => __( 'Set featured image', 'momento-event-manager' ),
			'remove_featured_image' => __( 'Remove featured image', 'momento-event-manager' ),
			'use_featured_image'    => __( 'Use as featured image', 'momento-event-manager' ),
			'insert_into_item'      => __( 'Insert into event', 'momento-event-manager' ),
			'uploaded_to_this_item' => __( 'Uploaded to this event', 'momento-event-manager' ),
			'items_list'            => __( 'Mega Events Manager list', 'momento-event-manager' ),
			'items_list_navigation' => __( 'Mega Events Manager list navigation', 'momento-event-manager' ),
			'filter_items_list'     => __( 'Filter events list', 'momento-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Mega Events Manager', 'momento-event-manager' ),
			'description'           => __( 'Event post type', 'momento-event-manager' ),
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
		self::wtmem_register_eventCategory_taxonomy();
	}

	
	public static function wtmem_register_eventCategory_taxonomy() {
        $labels = [
            'name'              => __('Event Category', 'momento-event-manager'),
            'singular_name'     => __('Event Category', 'momento-event-manager'),
            'menu_name'         => __('Event Category', 'momento-event-manager'),
            'search_items'      => __('Search Event Category', 'momento-event-manager'),
            'all_items'         => __('All Event Category', 'momento-event-manager'),
            'edit_item'         => __('Edit Event Category', 'momento-event-manager'),
            'update_item'       => __('Update Event Category', 'momento-event-manager'),
            'add_new_item'      => __('Add New Event Category', 'momento-event-manager'),
            'new_item_name'     => __('New Event Category Name', 'momento-event-manager'),
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

	

	public static function wtmem_get_product_sold_qty( $product_id ) {

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


	public static function wtmem_get_product_refunded_qty($product_id) {
		global $wpdb;
		
		if (empty($product_id)) {
			return 0;
		}
	
		$query = $wpdb->prepare("
			SELECT SUM(qty_meta.meta_value) as total_refunded_qty
			FROM {$wpdb->prefix}woocommerce_order_items AS order_items
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta
				ON order_items.order_item_id = product_meta.order_item_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS qty_meta
				ON order_items.order_item_id = qty_meta.order_item_id
			INNER JOIN {$wpdb->posts} AS posts
				ON order_items.order_id = posts.ID
			WHERE order_items.order_item_type = 'line_item'
			AND product_meta.meta_key = '_product_id'
			AND product_meta.meta_value = %d
			AND qty_meta.meta_key = '_qty'
			AND posts.post_status = 'wc-refunded'
		", $product_id);
		
		$result = $wpdb->get_var($query);
		
    	return (int) $result;
	}

	///order_id
	public static function wtmem_get_order_ids_by_product_id___111($product_id) {
		global $wpdb;
		
		if (empty($product_id)) {
			return array();
		}
		$query = $wpdb->prepare("
			SELECT DISTINCT order_items.order_id
			FROM {$wpdb->prefix}woocommerce_order_items AS order_items
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta
				ON order_items.order_item_id = product_meta.order_item_id
			WHERE order_items.order_item_type = 'line_item'
			AND product_meta.meta_key = '_product_id'
			AND product_meta.meta_value = %d
			ORDER BY order_items.order_id DESC
		", $product_id);
		
		$results = $wpdb->get_col($query);
		
		return $results ?: array();
	}

	public static function wtmem_get_refunded_order_ids_by_product_id____2222($product_id) {
		global $wpdb;
		
		if (empty($product_id)) {
			return array();
		}
		
		$query = $wpdb->prepare("
			SELECT DISTINCT order_items.order_id
			FROM {$wpdb->prefix}woocommerce_order_items AS order_items
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta
				ON order_items.order_item_id = product_meta.order_item_id
			INNER JOIN {$wpdb->posts} AS posts
				ON order_items.order_id = posts.ID
			WHERE order_items.order_item_type = 'line_item'
			AND product_meta.meta_key = '_product_id'
			AND product_meta.meta_value = %d
			AND posts.post_status IN ('wc-cancelled', 'wc-refunded', 'wc-on-hold')
			ORDER BY order_items.order_id DESC
		", $product_id);		
		$results = $wpdb->get_col($query);
		
		return $results ?: array();
	}	

	/* === cancelled refund order === */
	public static function get_cancelled_refunded_orders_by_product($product_id) {
		$order_ids = array();
		$args = array(
			'status' => array('cancelled', 'refunded', 'on-hold'),
			'limit' => -1,
			'return' => 'ids',
		);
		
		$orders = wc_get_orders($args);
		
		foreach ($orders as $order_id) {
			$order = wc_get_order($order_id);
			foreach ($order->get_items() as $item) {
				if ($item->get_product_id() == $product_id || 
					$item->get_variation_id() == $product_id) {
					$order_ids[] = $order_id;
					break;
				}
			}
		}
		
		return $order_ids;
	}

	//Get all order IDs by product ID
	public static function wtmem_get_order_ids_by_products( array $product_ids ) {

		global $wpdb;

		if ( empty( $product_ids ) ) {
			return array();
		}

		$placeholders = implode( ',', array_fill( 0, count( $product_ids ), '%d' ) );
	
	    $query = "
			SELECT DISTINCT order_items.order_id
			FROM {$wpdb->prefix}woocommerce_order_items AS order_items
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta
			ON order_items.order_item_id = product_meta.order_item_id
			WHERE order_items.order_item_type = 'line_item'
			AND product_meta.meta_key = '_product_id'
			AND product_meta.meta_value IN ($placeholders)
		";

		return $wpdb->get_col( $wpdb->prepare( $query, $product_ids ) );

	}

	//only completed order
	public static function wtmem_get_product_sold_qty_product( $product_id ) {
		if ( empty( $product_id ) || ! function_exists( 'wc_get_orders' ) ) {
			return 0;
		}
	    $total_qty = 0;
		// Get completed and processing orders containing this product.
		$orders = wc_get_orders( array(
			'status' => array( 'completed', 'processing' ),
			'limit'  => -1,
			'return' => 'ids',
		) );
		
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				continue;
			}
			
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_product_id() == $product_id || $item->get_variation_id() == $product_id ) {
					$total_qty += $item->get_quantity();
				}
			}
		}
		
		return (int) $total_qty;
	}


	//CSV download
	public static function download_attendee_csv() {
		if ( ! ini_get( 'safe_mode' ) ) {
			set_time_limit( 0 );
		}

		wp_suspend_cache_addition( true );

		if ( empty( $_GET['product_ids'] ) ) {
			wp_die( 'Invalid Product IDs' );
		}

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( 'Unauthorized access' );
		}

		$product_ids = array_map(
			'absint',
			explode( ',', wp_unslash( $_GET['product_ids'] ) )
		);
		$product_ids = array_filter( $product_ids );

		if ( empty( $product_ids ) ) {
			wp_die( 'Invalid product list' );
		}

		if (
			empty( $_GET['_wpnonce'] ) ||
			! wp_verify_nonce(
				$_GET['_wpnonce'],
				'wtmem_attendee_csv_' . md5( implode( ',', $product_ids ) )
			)
		) {
			wp_die( 'Security check failed' );
		}

		$order_ids = self::wtmem_get_order_ids_by_products( $product_ids );

		if ( empty( $order_ids ) ) {
			wp_die( 'No orders found for selected products.' );
		}

		header( 'Content-Type: text/csv; charset=utf-8' );
		header(
			'Content-Disposition: attachment; filename=attendees-products-' .
			implode( '-', $product_ids ) .
			'.csv'
		);
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		$output = fopen( 'php://output', 'w' );

		fputcsv( $output, array(
			'Order ID',
			'Order Date',
			'Product ID',
			'Product Name',
			'Quantity',
			'Attendee #',
			'First Name',
			'Last Name',
			'Email',
			'Has Attendee Data',
		) );

		$global_attendee_index = 0;
		$order_counter  = 0;

		foreach ( $order_ids as $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				continue;
			}

			foreach ( $order->get_items() as $item ) {
				$item_product_id   = (int) $item->get_product_id();
				$item_variation_id = (int) $item->get_variation_id();
				$quantity          = (int) $item->get_quantity();

				if (
					! in_array( $item_product_id, $product_ids, true ) &&
					! in_array( $item_variation_id, $product_ids, true )
				) {
					continue;
				}

				$attendees = $item->get_meta( '_uem_attendees', true );

				if ( is_string( $attendees ) ) {
					$attendees = maybe_unserialize( $attendees );
				}

				if ( ! empty( $attendees ) && is_array( $attendees ) ) {
					// If there are attendees, export each attendee on a separate row
					foreach ( $attendees as $attendee ) {
						$global_attendee_index++;

						fputcsv( $output, array(
							$order_id,
							$order->get_date_created()
								? $order->get_date_created()->date( 'Y-m-d' )
								: '',
							$item_product_id,
							$item->get_name(),
							$quantity,
							$global_attendee_index,
							$attendee['first_name'] ?? '',
							$attendee['last_name'] ?? '',
							$attendee['email'] ?? '',
							'Yes',
						) );
					}
				} else {
					// If no attendees, export the order/item with empty attendee fields
					// For quantity > 1, we create multiple rows (one per unit)
					for ( $i = 0; $i < $quantity; $i++ ) {
						$global_attendee_index++;

						fputcsv( $output, array(
							$order_id,
							$order->get_date_created()
								? $order->get_date_created()->date( 'Y-m-d' )
								: '',
							$item_product_id,
							$item->get_name(),
							$quantity,
							$global_attendee_index,
							'', // Empty first name
							'', // Empty last name
							'', // Empty email
							'No', // No attendee data
						) );
					}
				}
			}

			$order_counter++;

			if ( $order_counter % 50 === 0 ) {
				wp_cache_flush();
			}

			unset( $order );
		}

		fclose( $output );
		exit;
	}

	public static function wtmem_all_event_columns( $columns ) {
		$new = array();

		foreach ( $columns as $key => $label ) {

			$new[ $key ] = $label;

			if ( $key === 'title' ) {
				$new['start_event_dates'] = __( 'Start Event Dates', 'momento-event-manager' );
				$new['end_event_dates']   = __( 'End Event Dates', 'momento-event-manager' );
				$new['total_seat']   = __( 'Total Seat', 'momento-event-manager' );
				$new['reserved_seat']   = __( 'Reserved Seat', 'momento-event-manager' );
				$new['booking_seat']   = __( 'Booking seat', 'momento-event-manager' );
				$new['available_seat']   = __( 'Available Seat', 'momento-event-manager' );
				$new['atteende_data']   = __( 'Atteende Data', 'momento-event-manager' );
						
			}
		}

		return $new;
	}

	public static function wtmem_show_event_columns_value( $column, $post_id ) {

        $dates = get_post_meta($post_id, 'wtmem_event_dates', true );

	
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
                echo '<em>No Start Dates</em>';
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
                echo '<em>No End Dates</em>';
            }
        }

		if ( $column == 'total_seat' ) {

			$totalseat = get_post_meta($post_id, '_wtmem_tk_tickets', true );
			//print_r($totalseat);
			/* $ticket = reset( $totalseat );
			$quantity = $ticket['capacity'];
			echo esc_html( $quantity ); */
			
			if ( is_array( $totalseat ) && ! empty( $totalseat ) ) {
				$ticket   = reset( $totalseat );
				$quantity = isset( $ticket['capacity'] ) ? $ticket['capacity'] : 0;
			} else {
				$quantity = 0;
			}

			echo esc_html( $quantity );
		} 
		
		if ($column == 'reserved_seat') {

			$totalseat = get_post_meta( $post_id, '_wtmem_tk_tickets', true );

			if ( is_array( $totalseat ) && ! empty( $totalseat ) ) {
				$ticket      = reset( $totalseat );
				$reserve_qty = isset( $ticket['reserve_qty'] ) ? $ticket['reserve_qty'] : 0;
			} else {
				$reserve_qty = 0;
			}

			echo esc_html( $reserve_qty );
			
		} if ( $column == 'booking_seat' ) {

			$total_sold = 0;
			$products = get_post_meta( $post_id, '_uem_wc_products', true );
				if ( is_array( $products ) ) {
					foreach ( $products as $product_id ) {
						$total_sold += self::wtmem_get_product_sold_qty_product( (int) $product_id );						
					
					}
				}
			echo esc_html( $total_sold );

				
		 } if($column == 'available_seat') {

			$totalseat = 0;
			$totalseat = get_post_meta( $post_id, '_wtmem_tk_tickets', true );
			$reseverd  = isset( $ticket['reserve_qty'] ) ? absint( $ticket['reserve_qty'] ) : 0;
            $products    = get_post_meta( $post_id, '_uem_wc_products', true );
			$total_sold  = 0;
			
			if ( is_array( $products ) ) {
				foreach ( $products as $product_id ) {
					$total_sold += absint(
						self::wtmem_get_product_sold_qty_product( (int) $product_id )
						
					);
				}
			}
			/* echo esc_html( 'sold' ) . ': ' . esc_html( $total_sold ) . '<br>';
			echo esc_html( 'reserved' ) . ': ' . esc_html( $reseverd ) . '<br>'; */
			if ( is_array( $totalseat ) && ! empty( $totalseat ) ) {
				$ticket   = reset( $totalseat );
				$quantity = isset( $ticket['capacity'] ) ? $ticket['capacity'] : 0;
			}
			echo esc_html( $quantity - $total_sold - $reseverd )	 . '<br>';
			

		} 

		if ( $column === 'atteende_data' ) {

			$products = get_post_meta( $post_id, '_uem_wc_products', true );

			// Normalize product IDs
			if ( empty( $products ) ) {
				echo '—';
				return;
			}

			if ( ! is_array( $products ) ) {
				$products = array_map( 'absint', explode( ',', $products ) );
			}

			$products = array_filter( $products );

			if ( empty( $products ) ) {
				echo '—';
				return;
			}

			$product_ids = implode( ',', $products );
			
			$url = wp_nonce_url(
				admin_url(
					'admin-post.php?action=wtmem_download_attendee_csv&product_ids=' . $product_ids
				),
				'wtmem_attendee_csv_' . md5( $product_ids )
			);

			echo '<a href="' . esc_url( $url ) . '" class="button button-small">
					CSV Export
				</a>';
        } 

    }

	/**
	 * Register Organizer post type
	 */
	private static function wtmem_register_organizer_post_type() {
		$labels = array(
			'name'                  => _x( 'Event Organizers', 'Post Type General Name', 'momento-event-manager' ),
			'singular_name'         => _x( 'Event Organizer', 'Post Type Singular Name', 'momento-event-manager' ),
			'menu_name'             => __( 'Event Organizers', 'momento-event-manager' ),
			'name_admin_bar'        => __( 'Event Organizer', 'momento-event-manager' ),
			'archives'              => __( 'Event Organizer Archives', 'momento-event-manager' ),
			'all_items'             => __( 'Event Organizers', 'momento-event-manager' ),
			'add_new_item'          => __( 'Add New Event Organizer', 'momento-event-manager' ),
			'add_new'               => __( 'Add New Event Organizer', 'momento-event-manager' ),
			'new_item'              => __( 'New Event Organizer', 'momento-event-manager' ),
			'edit_item'             => __( 'Edit Event Organizer', 'momento-event-manager' ),
			'update_item'           => __( 'Update Event Organizer', 'momento-event-manager' ),
			'view_item'             => __( 'View Event Organizer', 'momento-event-manager' ),
			'search_items'          => __( 'Search Event Organizer', 'momento-event-manager' ),
			'not_found'             => __( 'Not found', 'momento-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'momento-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Organizer', 'momento-event-manager' ),
			'description'           => __( 'Organizer post type', 'momento-event-manager' ),
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
	private static function wtmem_register_volunteer_post_type() {
		$labels = array(
			'name'                  => _x( 'Event Volunteers', 'Post Type General Name', 'momento-event-manager' ),
			'singular_name'         => _x( 'Event Volunteer', 'Post Type Singular Name', 'momento-event-manager' ),
			'menu_name'             => __( 'Event Volunteers', 'momento-event-manager' ),
			'name_admin_bar'        => __( 'Event Volunteer', 'momento-event-manager' ),
			'archives'              => __( 'Event Volunteer Archives', 'momento-event-manager' ),
			'all_items'             => __( 'Event Volunteers', 'momento-event-manager' ),
			'add_new_item'          => __( 'Add New Event Volunteer', 'momento-event-manager' ),
			'add_new'               => __( 'Add New Event Volunteer', 'momento-event-manager' ),
			'new_item'              => __( 'New Event Volunteer', 'momento-event-manager' ),
			'edit_item'             => __( 'Edit Event Volunteer', 'momento-event-manager' ),
			'update_item'           => __( 'Update Event Volunteer', 'momento-event-manager' ),
			'view_item'             => __( 'View Event Volunteer', 'momento-event-manager' ),
			'search_items'          => __( 'Search Event Volunteer', 'momento-event-manager' ),
			'not_found'             => __( 'Not found', 'momento-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'momento-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Volunteer', 'momento-event-manager' ),
			'description'           => __( 'Volunteer post type', 'momento-event-manager' ),
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
	private static function wtmem_register_sponsor_post_type() {
		$labels = array(
			'name'                  => _x( 'Event Sponsors', 'Post Type General Name', 'momento-event-manager' ),
			'singular_name'         => _x( 'Event Sponsor', 'Post Type Singular Name', 'momento-event-manager' ),
			'menu_name'             => __( 'Event Sponsors', 'momento-event-manager' ),
			'name_admin_bar'        => __( 'Event Sponsor', 'momento-event-manager' ),
			'archives'              => __( 'Event Sponsor Archives', 'momento-event-manager' ),
			'all_items'             => __( 'Event Sponsors', 'momento-event-manager' ),
			'add_new_item'          => __( 'Add New Event Sponsor', 'momento-event-manager' ),
			'add_new'               => __( 'Add New Event Sponsor', 'momento-event-manager' ),
			'new_item'              => __( 'New Event Sponsor', 'momento-event-manager' ),
			'edit_item'             => __( 'Edit Event Sponsor', 'momento-event-manager' ),
			'update_item'           => __( 'Update Event Sponsor', 'momento-event-manager' ),
			'view_item'             => __( 'View Event Sponsor', 'momento-event-manager' ),
			'search_items'          => __( 'Search Event Sponsor', 'momento-event-manager' ),
			'not_found'             => __( 'Not found', 'momento-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'momento-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Sponsor', 'momento-event-manager' ),
			'description'           => __( 'Sponsor post type', 'momento-event-manager' ),
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
	private static function wtmem_register_registration_post_type() {
		$labels = array(
			'name'                  => _x( 'Event Registrations', 'Post Type General Name', 'momento-event-manager' ),
			'singular_name'         => _x( 'Event Registration', 'Post Type Singular Name', 'momento-event-manager' ),
			'menu_name'             => __( 'Registrations', 'momento-event-manager' ),
			'name_admin_bar'        => __( 'Event Registration', 'momento-event-manager' ),
			'archives'              => __( 'Registration Archives', 'momento-event-manager' ),
			'all_items'             => __( 'All Registrations', 'momento-event-manager' ),
			'add_new_item'          => __( 'Add New Registration', 'momento-event-manager' ),
			'add_new'               => __( 'Add New', 'momento-event-manager' ),
			'new_item'              => __( 'New Registration', 'momento-event-manager' ),
			'edit_item'             => __( 'Edit Registration', 'momento-event-manager' ),
			'update_item'           => __( 'Update Registration', 'momento-event-manager' ),
			'view_item'             => __( 'View Registration', 'momento-event-manager' ),
			'search_items'          => __( 'Search Registration', 'momento-event-manager' ),
			'not_found'             => __( 'Not found', 'momento-event-manager' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'momento-event-manager' ),
		);
		
		$args = array(
			'label'                 => __( 'Event Registration', 'momento-event-manager' ),
			'description'           => __( 'Event Registration post type', 'momento-event-manager' ),
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