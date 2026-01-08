<?php
/**
 *
 *  woocommerce intgration check
 *
 **/

class Class_uem_woocommerce_inte {

    public function __construct() {
        add_action('admin_init', [$this, 'webcu_save_woo_integration_settings']);
    }
  
    public function webcu_woo_inte_page() {

        // Ensure any POST from this form is processed immediately (hook may be added too late)
        if ( ! empty( $_POST ) ) {
            $this->webcu_save_woo_integration_settings();
        }

        $woo_inte = get_option('webcu_wooIntegration_status', 'off');
        $checked = ($woo_inte == 'on') ? 'checked' : '';

        ?>

        <div class="wrap">
            <h1><?php echo esc_html__('Ultimate Event - WooCommerce Integration', 'mega-event-manager'); ?></h1>

            <form method="post" action="<?php echo esc_url( admin_url( 'edit.php?post_type=mem_event&page=mem-settings' ) ); ?>">
                <?php wp_nonce_field('save_woo_integration_settings'); ?>

                <label class="switch-label">
                    <?php echo esc_html__('WooCommerce Integration', 'mega-event-manager'); ?>
                    <label class="switch">

                    <?php 
                        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

                        if (!is_plugin_active('woocommerce/woocommerce.php')) {
                            ?>
                            <input type="checkbox" name="webcu_wooIntegration_status" value="on" disabled />
                            <?php
                        } else {
                            ?>
                            <input type="checkbox" name="webcu_wooIntegration_status" value="on" <?php echo esc_attr($checked); ?> />
                            <?php
                        }
                    ?>
                    <span class="slider round"></span>
                    </label>
                </label>

                <br><br>
               <input type="submit" name="webcu_woo_inte_save" class="button button-primary" value="<?php echo esc_attr__('Save Settings', 'mega-event-manager'); ?>" />
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
    
    public function webcu_save_woo_integration_settings() {
        if ( isset( $_POST['webcu_woo_inte_save'] ) ) {

            if ( ! check_admin_referer( 'save_woo_integration_settings' ) ) {
                return;
            }

            if ( isset( $_POST['webcu_wooIntegration_status'] ) ) {
                update_option( 'webcu_wooIntegration_status', 'on' );
            } else {
                update_option( 'webcu_wooIntegration_status', 'off' );
            }

            // Show admin notice after save
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'mega-event-manager' ) . '</p></div>';
            } );
        }
    }

} 