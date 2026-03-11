<?php
namespace Wpcraft\Settings;

/**
 *
 *  Google Map integration
 *
 **/

class class_google_map { 
    
    public function __construct() {
        add_action('admin_init', [$this, 'wtmem_googleMap_save']);
    }

    public function wtmem_google_map_integration() {
        // Get the saved value at the beginning

        if ( ! empty( $_POST ) ) {
            $this->wtmem_googleMap_save();
        }

        $saved_api = get_option('google_map_api');
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Google Map Settings', 'mega-event-manager'); ?></h1>
            
            <?php if (isset($_GET['settings-updated'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Settings saved successfully!', 'momento-event-manager'); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <?php wp_nonce_field('wtmem_api_action', 'wtmem_api_nonce'); ?>
                
                <label for="google_map"><?php esc_html_e('Google Maps API Key', 'momento-event-manager'); ?></label>
                <input type="text" name="google_map" id="google_map" 
                       value="<?php echo get_option('google_map_api'); ?>" 
                       placeholder="Enter your Google Maps API key" 
                       style="width: 400px; max-width: 100%;">
                <div style="clear:both; margin: 20px 0;"></div>

                <input type="submit" name="wtmem_api_submit" class="button button-primary" value="<?php echo esc_attr__('Save Settings', 'momento-event-manager'); ?>" />
            </form>   
        </div>
        <?php
    }

    public function wtmem_googleMap_save() {
        // Check if our specific submit button was clicked
        if (isset($_POST['wtmem_api_submit'])) {
            // Verify nonce
            if (!isset($_POST['wtmem_api_nonce']) || !wp_verify_nonce($_POST['wtmem_api_nonce'], 'wtmem_api_action')) {
                wp_die('Security check failed');
            }
            
            // Save the API key
            if (isset($_POST['google_map'])) {

                $google_map = sanitize_text_field($_POST['google_map']);

                update_option('google_map_api', $google_map);

                wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
                exit;
            }
        }
    }
}