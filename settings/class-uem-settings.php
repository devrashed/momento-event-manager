<?php
/**
 * Settings
 *
 * @package mega_Events_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class UEM_Settings {

     /**
	 * Initialize settings
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'webcu_register_settings' ) );
	}
	
	/**
	 * Add settings page to menu
	 */
	public static function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=mem_event',
			__( 'Settings', 'mega-event-manager' ),
			__( 'Settings', 'mega-event-manager' ),
			'manage_options',
			'mem-settings',
			array( __CLASS__, 'webcu_admin_event_setting_page' )
		);
	}	
	/**
	 * Register settings
	 */
	public static function webcu_register_settings() {
		register_setting( 'uem_settings', 'uem_registration_method' );
	}
	
	public static function webcu_admin_event_setting_page(){
     ?>

		<div class="wrap">
                <h2> <?php echo esc_html__('Settings', 'mega-event-manager') ?></h2>
           <?php

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'Insufficient permissions' ) );
            }

            $allowed_tabs = [ 'registration', 'dytx', 'currency','template'];
            $tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'woointe';
            if ( ! in_array( $tab, $allowed_tabs, true ) ) $tab = 'woointe';

            $base_url = menu_page_url( 'vertical-admin-nav', false ); 
            
            function van_tab_link( $base_url, $tab_name ) {
                return esc_url( add_query_arg( 'tab', $tab_name, $base_url ) );
            } 
			$woo_inte = get_option('webcu_wooIntegration_status', 'off');
			
			
			?>
		    <div class="van-wrapper">

                    <nav class="van-nav" aria-label="<?php esc_attr_e( 'Plugin navigation', 'mega-event-manager' ); ?>">
                        <ul>
							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=woointe" class="<?php echo $tab === 'woointe' ? 'van-active' : ''; ?>" data-tab="woointe"><?php esc_html_e( 'WooCommerce integration', 'mega-event-manager' ); ?></a></li>
                            <li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=dytx" class="<?php echo $tab === 'dytx' ? 'van-active' : ''; ?>" data-tab="dytx"><?php esc_html_e( 'Dynamic Taxonomy', 'mega-event-manager' ); ?></a></li>
   
							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=registration" class="<?php echo $tab === 'registration' ? 'van-active' : ''; ?>" data-tab="registration"><?php esc_html_e( 'Registration Method', 'mega-event-manager' ); ?></a></li>	
							<?php if($woo_inte === 'off') {?>
							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=currency" class="<?php echo $tab === 'currency' ? 'van-active' : ''; ?>" data-tab="currency"><?php esc_html_e( 'Currency Option', 'mega-event-manager' ); ?></a></li>
							<?php } ?>	
							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=template" class="<?php echo $tab === 'template' ? 'van-active' : ''; ?>" data-tab="template"><?php esc_html_e( 'Template', 'mega-event-manager' ); ?></a></li>	
						</ul>		
                    </nav>

                    <section class="van-content" role="main">

						<div id="van-tab-woointe" class="van-tab" style="<?php echo $tab === 'woointe' ? '' : 'display:none;'; ?>">
                            <h2><?php esc_html_e( 'Woocommerce Integration', 'mega-event-manager' ); ?></h2>   
                             <?php 
							    $wooIntegration = new Class_uem_woocommerce_inte();
							 	$wooIntegration->webcu_woo_inte_page()
							 ?>
                        </div>

    					<div id="van-tab-woointe" class="van-tab" style="<?php echo $tab === 'registration' ? '' : 'display:none;'; ?>">
                            <h2><?php esc_html_e( 'Registration Method', 'mega-event-manager' ); ?></h2>   
                            <?php self::webcu_render_settings_page()?>
                        </div>

					    <div id="van-tab-dytx" class="van-tab" style="<?php echo $tab === 'dytx' ? '' : 'display:none;'; ?>">
                            <h2><?php esc_html_e( 'Dynamic Taxonomy', 'mega-event-manager' ); ?></h2>   
                            <?php 
								$taxonomy = new Class_create_dynamic_taxonomy();
								$taxonomy->webcu_taxonomy_settings_page();
							?>
                        </div>

                        <div id="van-tab-currency" class="van-tab" style="<?php echo $tab === 'currency' ? '' : 'display:none;'; ?>">
                             <h2><?php esc_html_e( 'Currency Options', 'mega-event-manager' ); ?></h2>   
                             <?php 
							    $currency = new Class_currency_setting();
								$currency->webcu_event_currency_fields();
							 ?>
                        </div>
						
						<div id="van-tab-currency" class="van-tab" style="<?php echo $tab === 'currency' ? '' : 'display:none;'; ?>">
                             <h2><?php esc_html_e( 'Currency Options', 'mega-event-manager' ); ?></h2>   
                             <?php 
							    $currency = new Class_currency_setting();
								$currency->webcu_event_currency_fields();
							 ?>
                        </div>

						<div id="van-tab-template" class="van-tab" style="<?php echo $tab === 'template' ? '' : 'display:none;'; ?>">
                             <h2><?php esc_html_e( 'Template ', 'mega-event-manager' ); ?></h2>   
                             <?php 
							   $temp = new Class_mem_event_template();
							   $temp->webcu_event_web_template(); 
							 ?>
                        </div>
                     
                    </section>
            </div>
		</div>	
   	<?php
	}

     /**
	 * Render settings page
	 */
	public static function webcu_render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		
		// Save settings
		if ( isset( $_POST['uem_save_settings'] ) && check_admin_referer( 'uem_settings_nonce' ) ) {
			$registration_method = isset( $_POST['uem_registration_method'] ) ? sanitize_text_field( $_POST['uem_registration_method'] ) : 'woocommerce';
			update_option( 'uem_registration_method', $registration_method );
			echo '<div class="notice notice-success"><p>' . __( 'Settings saved successfully!', 'mega-event-manager' ) . '</p></div>';
		}
		
		$registration_method = get_option( 'uem_registration_method', 'woocommerce' );
		$woocommerce_active = class_exists( 'WooCommerce' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="">
				<?php wp_nonce_field( 'uem_settings_nonce' ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">
								<label for="uem_registration_method"><?php echo esc_html( 'Registration Method', 'mega-event-manager' ); ?></label>
							</th>
							<td>
								<select name="uem_registration_method" id="uem_registration_method">
									<option value="woocommerce" <?php selected( $registration_method, 'woocommerce' ); ?>>
										<?php echo esc_html( 'WooCommerce', 'mega-event-manager' ); ?>
									</option>
									<option value="simple" <?php selected( $registration_method, 'simple' ); ?>>
										<?php echo esc_html( 'Simple Registration (Without WooCommerce)', 'mega-event-manager' ); ?>
									</option>
								</select>
								<?php if ( ! $woocommerce_active && $registration_method === 'woocommerce' ) : ?>
									<p class="description" style="color: #d63638;">
										<?php echo esc_html( 'Warning: WooCommerce is not active. Please install and activate WooCommerce to use this registration method.', 'mega-event-manager' ); ?>
									</p>
								<?php endif; ?>
								<p class="description">
									<?php echo esc_html( 'Choose how event registrations will be processed.', 'mega-event-manager' ); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Save Settings', 'mega-event-manager' ), 'primary', 'uem_save_settings' ); ?>
			</form>
		</div>
		<?php
	}
}


