<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class MEM_Event_Attendee_Admin {

    private $table;

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }

    /* ===== Admin Menu ===== */
    public function register_menu() {
            add_submenu_page(
			'edit.php?post_type=mem_event',
			__( 'All Attendee', 'mega-events-manager' ),
			__( 'All Attendee', 'mega-events-manager' ),
			'manage_options',
			'all-attendee',
			[ $this, 'render_page' ],
		);
    }

    /* ===== Page Render ===== */
    public function render_page() {

        $this->table = new class extends WP_List_Table {

            public function __construct() {
                parent::__construct([
                    'singular' => 'attendee',
                    'plural'   => 'attendees',
                    'ajax'     => false,
                ]);
            }

            /* Columns */
            public function get_columns() {
                return [
                    'first_name' => 'First Name',
                    'last_name'  => 'Last Name',
                    'email'      => 'Email',
                ];
            }

            /* Column output */
            protected function column_default( $item, $column_name ) {
                return esc_html( $item[ $column_name ] ?? '' );
            }

            /* Prepare items */
            public function prepare_items() {

                $this->_column_headers = [
                    $this->get_columns(),
                    [],
                    [],
                ];

                $this->items = $this->get_data();
            }

            /* Fetch data */
            private function get_data() {

                global $wpdb;

                $event_id = isset($_GET['mem_event']) ? intval($_GET['mem_event']) : 0;

                $sql = "SELECT first_name, last_name, email FROM {$wpdb->prefix}mem_attendees";

                if ( $event_id ) {
                    $sql .= $wpdb->prepare(" WHERE event_id = %d", $event_id);
                }

                return $wpdb->get_results( $sql, ARRAY_A );
            }

            /* Filter dropdown */
            protected function extra_tablenav( $which ) {

                if ( $which !== 'top' ) {
                    return;
                }

                $events = get_posts([
                    'post_type'      => 'mem_event',
                    'posts_per_page' => -1,
                ]);

                $selected = $_GET['mem_event'] ?? '';
                ?>
                <div class="alignleft actions">
                    <select name="mem_event">
                        <option value="">All Events</option>
                        <?php foreach ( $events as $event ) : ?>
                            <option value="<?php echo esc_attr($event->ID); ?>"
                                <?php selected($selected, $event->ID); ?>>
                                <?php echo esc_html($event->post_title); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php submit_button( 'Filter', '', 'filter_action', false ); ?>
                </div>
                <?php
            }
        };

        $this->table->prepare_items();
        ?>
        <div class="wrap">
            <h1>Event Attendees</h1>

            <form method="get">
                <input type="hidden" name="page" value="mem-event-attendees">
                <?php $this->table->display(); ?>
            </form>
        </div>
        <?php
    }
}

/* Init */
new MEM_Event_Attendee_Admin();


?>