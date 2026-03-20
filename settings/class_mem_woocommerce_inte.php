<?php
namespace Wpcraft\Settings;
/**
 *
 *  woocommerce intgration check
 *
 **/

class class_mem_woocommerce_inte {
    
    public function __construct() {
        add_action( 'admin_init', array( $this, 'wtmem_save_woo_integration_settings' ) );
        add_action( 'admin_notices', array( $this, 'wtmem_display_save_notice' ) );
    }
  
    public function wtmem_woo_inte_page() {

        // Ensure any POST from this form is processed immediately (hook may be added too late)
        if ( ! empty( $_POST ) ) {
            $this->wtmem_save_woo_integration_settings();
        }

        $woo_inte = get_option('wtmem_wooIntegration_status');
        $checked = ($woo_inte == 'on') ? 'checked' : '';
        var_dump($checked);

        ?>

        <div class="wrap">
            <h1><?php echo esc_html__('Ultimate Event - WooCommerce Integration', 'momento-event-manager'); ?></h1>

            <form method="post" action="<?php echo esc_url( admin_url( 'edit.php?post_type=mem_event&page=mem-settings' ) ); ?>">
                <?php wp_nonce_field('save_woo_integration_settings'); ?>

                <label class="switch-label">
                    <?php echo esc_html__('WooCommerce Integration', 'momento-event-manager'); ?>
                    <label class="switch">

                    <?php 
                        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

                        if (!is_plugin_active('woocommerce/woocommerce.php')) {
                            ?>
                            <input type="checkbox" name="wtmem_wooIntegration_status" value="on" disabled />
                            <?php
                        } else {
                            ?>
                            <input type="checkbox" name="wtmem_wooIntegration_status" value="on" <?php echo esc_attr($checked); ?> />
                            <?php
                        }
                    ?>
                    <span class="slider round"></span>
                    </label>
                </label>

                <br><br>
               <input type="submit" name="wtmem_woo_inte_save" id="wtmem_woo_inte_save" class="button button-primary" value="<?php echo esc_attr__('Save Settings', 'momento-event-manager'); ?>" />
            </form>

           <?php 
            if (is_plugin_active('woocommerce/woocommerce.php')) {
                echo "<p style='color:green;'>WooCommerce plugin active</p>";
            } else {
                echo "<p style='color:red;font-size:12px'>WooCommerce not active</p>";
            }
            ?>
           </div>
        <?php
    }
    
    public function wtmem_save_woo_integration_settings() {
        if ( isset( $_POST['wtmem_woo_inte_save'] ) ) {

            if ( ! check_admin_referer( 'save_woo_integration_settings' ) ) {
                return;
            }

            if ( isset( $_POST['wtmem_wooIntegration_status'] ) ) {
                update_option( 'wtmem_wooIntegration_status', 'on' );
            } else {
                update_option( 'wtmem_wooIntegration_status', 'off' );
            }

            // Redirect to refresh the page so Currency option visibility updates immediately.
            wp_safe_redirect( add_query_arg( 'woo_saved', '1', admin_url( 'edit.php?post_type=mem_event&page=mem-settings&tab=woointe' ) ) );
            exit;
        }
    }

    /**
     * Display admin notice after settings are saved.
     */
    public function wtmem_display_save_notice() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Only displaying notice, no data processing.
        if ( isset( $_GET['woo_saved'] ) && '1' === $_GET['woo_saved'] ) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'momento-event-manager' ) . '</p></div>';
        }
    }
} 