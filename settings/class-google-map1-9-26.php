<?php

/**
 *
 *  Google Map integration
 *
 **/

class Class_uem_google_map__ssss { 

     public function __construct() {
        add_action('admin_init', [$this, 'webcu_googleMap_save_currency']);
    }

        public function webcu_google_map_integration() {
        wp_nonce_field('webcu_api_action', 'webcu_api_nonce'); 
            ?>
            <div class="wrap">
                <form method="post" action="">
                <h4><?php esc_html_e( 'Organization Template ', 'mega-event-manager' ); ?></h4>  

                    <label><?php esc_html_e( 'Api', 'mega-events-manager' ); ?></label>
                    <input type="text" name="google_api" id="google_api" value="<?php echo get_option('google_map_api'); ?>" placeholder="">  

                <div style="clear:both;"> </div>
                    <button type="submit" name="webcu_api" class="save-settings">
                    <?php esc_html_e( 'Save Changes', 'mega-events-manager' ); ?>
                </button>
                    
                </form>   
            </div>
            
            <?php
        }

        public function webcu_googleMap_save_currency() {

            if ( isset($_POST['webcu_api']) ) {
                if (!isset($_POST['webcu_api_nonce']) || !wp_verify_nonce($_POST['webcu_api_nonce'], 'webcu_api_action')) {
                    return;
                }
                $google_api = sanitize_text_field($_POST['google_api']);
                update_option('google_map_api', $google_api);    

                           
            }

        }

      
}

class Class_uem_google_map_dddddddddd { 

    public function __construct() {
        add_action('admin_init', [$this, 'webcu_googleMap_save']);
    }

    public function webcu_google_map_integration() {
        // Display success message if option was just saved
        if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
            echo '<div class="updated"><p>' . esc_html__('Settings saved successfully!', 'mega-events-manager') . '</p></div>';
        }
        
        wp_nonce_field('webcu_api_action', 'webcu_api_nonce'); 
        ?>
        <div class="wrap">
            <form method="post" action="">
                <h4><?php esc_html_e('Google Map APi key', 'mega-event-manager'); ?></h4>  

                <label><?php esc_html_e('Api', 'mega-events-manager'); ?></label>
                <input type="text" name="google_api" id="google_api" 
                       value="<?php echo esc_attr(get_option('google_map_api')); ?>" placeholder="">  

                <div style="clear:both;"></div>
                <button type="submit" name="webcu_api" class="save-settings">
                    <?php esc_html_e('Save Changes', 'mega-events-manager'); ?>
                </button>
            </form>   
        </div>
        <?php
    }

    public function webcu_googleMap_save() {
        if (isset($_POST['webcu_api'])) {
            if (!isset($_POST['webcu_api_nonce']) || !wp_verify_nonce($_POST['webcu_api_nonce'], 'webcu_api_action')) {
                return;
            }
            
            $google_api = sanitize_text_field($_POST['google_api']);
            
            update_option('google_map_api', $google_api);
            
            // Redirect to avoid form resubmission
            wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
            exit;
        }
    }
}

class Class_uem_google_map { 
    
    public function __construct() {
        add_action('admin_init', [$this, 'webcu_googleMap_save']);
    }

    public function webcu_google_map_integration() {
        // Get the saved value at the beginning
        $saved_api = get_option('google_map_api');
        
        // Add nonce field
        wp_nonce_field('webcu_api_action', 'webcu_api_nonce'); 
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Google Map Settings', 'mega-event-manager'); ?></h1>
            
            <?php if (isset($_GET['settings-updated'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e('Settings saved successfully!', 'mega-events-manager'); ?></p>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                
                <label for="google_api"><?php esc_html_e('Google Maps API Key', 'mega-events-manager'); ?></label>
                <input type="text" name="google_api" id="google_api" 
                       value="<?php echo esc_attr($saved_api); ?>" 
                       placeholder="Enter your Google Maps API key" 
                       style="width: 400px; max-width: 100%;">
                
                <div style="clear:both; margin: 20px 0;"></div>
                
                <button type="submit" name="webcu_api_submit" class="button button-primary">
                    <?php esc_html_e('Save Changes', 'mega-events-manager'); ?>
                </button>
            </form>   
        </div>
        <?php
    }

    public function webcu_googleMap_save() {
        // Check if our specific submit button was clicked
        if (isset($_POST['webcu_api_submit'])) {
            // Verify nonce
            if (!isset($_POST['webcu_api_nonce']) || !wp_verify_nonce($_POST['webcu_api_nonce'], 'webcu_api_action')) {
                wp_die('Security check failed');
            }
            
            // Save the API key
            if (isset($_POST['google_api'])) {
                $google_api = sanitize_text_field($_POST['google_api']);
                update_option('google_map_api', $google_api);
                
                // Redirect to prevent form resubmission
                wp_redirect(add_query_arg('settings-updated', 'true', wp_get_referer()));
                exit;
            }
        }
    }
}