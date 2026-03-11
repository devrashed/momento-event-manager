<?php
namespace Wpcraft\Settings;

/**
 * Dynamic Taxonomy Manager for Ultimate Event
 */

class Class_create_dynamic_taxonomy {

    private $option_key = 'wtmem_dynamic_taxonomies';
    private $slug;

    public function __construct() {
        add_action('init', [$this, 'wtmem_register_dynamic_taxonomies']);
        add_action('admin_menu', [$this, 'wtmem_add_taxonomy_submenus']);
        add_action('admin_head', [$this, 'wtmem_active_taxonomy_submenu']);
    }

     /**
     * Admin page for adding taxonomy
     */
    public function wtmem_taxonomy_settings_page() {

        if (isset($_POST['wtmem_add_taxonomy'])) {
            
            $tax_name = sanitize_text_field($_POST['taxonomy_name']);
            $tax_slug = sanitize_title($_POST['taxonomy_slug']);

            if (!empty($tax_name) && !empty($tax_slug)) {

                $taxonomies = get_option($this->option_key, []);
                $taxonomies[$tax_slug] = [
                    'label' => $tax_name,
                    'slug'  => $tax_slug
                ];

                update_option($this->option_key, $taxonomies);

                echo '<div class="updated"><p>Taxonomy created successfully!</p></div>';
            }
        }

        if (isset($_GET['delete_tax']) && !empty($_GET['delete_tax'])) {
            $delete_slug = sanitize_text_field($_GET['delete_tax']);
            $taxonomies = get_option($this->option_key, []);

            if (isset($taxonomies[$delete_slug])) {
                unset($taxonomies[$delete_slug]);
                update_option($this->option_key, $taxonomies);
                echo '<div class="updated"><p>Taxonomy deleted successfully!</p></div>';
            }
        }

        $taxonomies = get_option($this->option_key, []);
        ?>

        <div class="wrap">
            <h2> <?php echo esc_html__('Dynamic Taxonomies', 'momento-event-manager') ?></h2>

            <form method="POST">
                <table class="form-table">
                    <tr>
                        <th><label><?php echo esc_html__('Taxonomy Name', 'momento-event-manager') ?></label></th>
                        <td><input type="text" name="taxonomy_name" required class="regular-text"></td>
                    </tr>
                    <tr>
                        <th><label><?php echo esc_html__('Taxonomy Slug', 'momento-event-manager') ?></label></th>
                        <td><input type="text" name="taxonomy_slug" required class="regular-text"></td>
                    </tr>
                </table>

                <p><input type="submit" name="wtmem_add_taxonomy" class="button button-primary" value="Create Taxonomy"></p>
            </form>

            <h2> <?php echo esc_html__('Existing Taxonomies', 'momento-event-manager') ?></h2>

            <table class="widefat">
                <thead>
                <tr>
                    <th><?php echo esc_html__('Label', 'momento-event-manager') ?></th>
                    <th><?php echo esc_html__('Slug', 'momento-event-manager') ?></th>
                    <th><?php echo esc_html__('Actions', 'momento-event-manager') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($taxonomies)) : ?>
                    <?php foreach ($taxonomies as $slug => $data): ?>
                    <tr>
                        <td><?php echo esc_html($data['label']); ?></td>
                        <td><?php echo esc_html($data['slug']); ?></td>
                        <td>
                        <a href="<?php echo admin_url('edit.php?post_type=mem_event&page=mem-settings&tab=dytx&delete_tax=' . esc_attr($slug)
                            ); ?>" onclick="return confirm('Delete this taxonomy?');"><?php echo esc_html__('Delete', 'momento-event-manager') ?> </a>

                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3"> <?php echo esc_html__('No taxonomies found.', 'momento-event-manager') ?></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
                  
        <?php
    }

    /**
     * Register all dynamic taxonomies
     */
    public function wtmem_register_dynamic_taxonomies() {

        $taxonomies = get_option($this->option_key, []);
         
        if (!empty($taxonomies)) {
            foreach ($taxonomies as $slug => $data) {

                $labels = [
                    'name'          => $data['label'],
                    'singular_name' => $data['label'],
                ];
                
                register_taxonomy($slug, 'ultimate_event', [
                    'labels' => $labels,
                    'public' => true,
                    'hierarchical' => true,
                    'show_admin_column' => true,
                    'show_ui' => true,
                    'show_in_menu' => true
                ]);
            }
        }
    }


    public function wtmem_add_taxonomy_submenus() {

        // Prevent duplicate submenu creation
        static $added = false;

        if ($added) {
            return;
        }
        $added = true;

        $taxonomies = get_option($this->option_key, []);

        if (!empty($taxonomies)) {
            foreach ($taxonomies as $this->slug => $data) {

                add_submenu_page(
                    'edit.php?post_type=ultimate_event',
                    ucfirst($data['label']),
                    ucfirst($data['label']),
                    'manage_options',
                    'edit-tags.php?taxonomy=' . $this->slug . '&post_type=ultimate_event'
                );
            }
        }

    }

    public function wtmem_active_taxonomy_submenu() {

        global $parent_file, $submenu_file, $current_screen;

        if ($current_screen->post_type === 'ultimate_event') {

            $parent_file = 'edit.php?post_type=ultimate_event';

            if ($current_screen->taxonomy === $this->slug ) {
                $submenu_file = 'edit-tags.php?taxonomy=' . $this->slug . '&post_type=ultimate_event';
            } 
        }
    }


}