<?php

/**
 * Settings
 *
 * @package mega_Events_Manager
 */
namespace Wpcraft\Settings;
use Wpcraft\Settings\class_mem_woocommerce_inte;
use Wpcraft\Settings\Class_create_dynamic_taxonomy;
use Wpcraft\Settings\Class_currency_setting;
use Wpcraft\Settings\class_google_map;
use Wpcraft\Settings\Class_mem_event_template;
use Wpcraft\Settings\class_event_custom_metabox;
use Wpcraft\Settings\class_mem_create_dynamic_taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class class_mem_settings {

     /**
	 * Initialize settings
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'wtmem_register_settings' ) );
	}
	
	/**
	 * Add settings page to menu
	 */
	public static function add_settings_page() {
		add_submenu_page(
			'edit.php?post_type=mem_event',
			__( 'Settings', 'momento-event-manager' ),
			__( 'Settings', 'momento-event-manager' ),
			'manage_options',
			'mem-settings',
			array( __CLASS__, 'wtmem_admin_event_setting_page' )
		);
	}	
	/**
	 * Register settings
	 */
	public static function wtmem_register_settings() {
		register_setting( 'uem_settings', 'uem_registration_method' );
	}
	
	public static function wtmem_admin_event_setting_page(){
     ?>

		<div class="wrap">
                <h2> <?php echo esc_html__('Settings', 'momento-event-manager') ?></h2>
           <?php

            if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( __( 'Insufficient permissions' ) );
            }

            $allowed_tabs = ['woointe', 'registration', 'dytx', 'currency', 'template', 'mapg'];
            $tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'woointe';
            if ( ! in_array( $tab, $allowed_tabs, true ) ) $tab = 'woointe';

            $base_url = menu_page_url( 'vertical-admin-nav', false ); 
            
            function van_tab_link( $base_url, $tab_name ) {
                return esc_url( add_query_arg( 'tab', $tab_name, $base_url ) );
            } 

			$woo_inte = get_option('wtmem_wooIntegration_status', 'off');
			
			
			?>
		    <div class="van-wrapper">

                    <nav class="van-nav" aria-label="<?php esc_attr_e( 'Plugin navigation', 'momento-event-manager'); ?>">
                        <ul>
							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=woointe" class="<?php echo $tab === 'woointe' ? 'van-active' : ''; ?>" data-tab="woointe"><?php esc_html_e( 'WooCommerce integration', 'momento-event-manager' ); ?></a></li>

                            <li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=registration" class="<?php echo $tab === 'registration' ? 'van-active' : ''; ?>" data-tab="registration"><?php esc_html_e( 'Registration Method', 'momento-event-manager' ); ?></a></li>	
							<?php if( get_option('wtmem_wooIntegration_status', 'off') == 'off') {?>

							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=currency" class="<?php echo $tab === 'currency' ? 'van-active' : ''; ?>" data-tab="currency"><?php esc_html_e( 'Currency Option', 'momento-event-manager' ); ?></a></li>
							<?php } ?>	
							
							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=dytx" class="<?php echo $tab === 'dytx' ? 'van-active' : ''; ?>" data-tab="dytx"><?php esc_html_e( 'Dynamic Taxonomy', 'momento-event-manager' ); ?></a></li>

							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=template" class="<?php echo $tab === 'template' ? 'van-active' : ''; ?>" data-tab="template"><?php esc_html_e( 'Template', 'momento-event-manager' ); ?></a></li>	

							<li><a href="edit.php?post_type=mem_event&page=mem-settings&tab=mapg" class="<?php echo $tab === 'mapg' ? 'van-active' : ''; ?>" data-tab="mapg"><?php esc_html_e( 'Google Map Integration', 'momento-event-manager' ); ?></a></li>	
						</ul>		
                    </nav>              

                    <section class="van-content" role="main">

						<div id="van-tab-woointe" class="van-tab" style="<?php echo $tab === 'woointe' ? '' : 'display:none;'; ?>">
                            <h2><?php esc_html_e( 'Woocommerce Integration', 'momento-event-manager' ); ?></h2>   
                             <?php 
							    $wooIntegration = new class_mem_woocommerce_inte();
							 	$wooIntegration->wtmem_woo_inte_page()
							 ?>
                        </div>

    					<div id="van-tab-woointe" class="van-tab" style="<?php echo $tab === 'registration' ? '' : 'display:none;'; ?>">
                            <h2><?php esc_html_e( 'Registration Method', 'momento-event-manager' ); ?></h2>   
                            <?php self::wtmem_render_settings_page() ?>
                        </div>

					    <div id="van-tab-dytx" class="van-tab" style="<?php echo $tab === 'dytx' ? '' : 'display:none;'; ?>">
                            <h2><?php esc_html_e( 'Dynamic Taxonomy', 'momento-event-manager' ); ?></h2>   
                            <?php 
								$taxonomy = new class_create_dynamic_taxonomy();
								$taxonomy->wtmem_taxonomy_settings_page();
							?>
                        </div>

                        <div id="van-tab-currency" class="van-tab" style="<?php echo $tab === 'currency' ? '' : 'display:none;'; ?>">
                             <h2><?php esc_html_e( 'Currency Options', 'momento-event-manager' ); ?></h2>   
                             <?php 
							    $currency = new class_mem_currency_setting();
								$currency->wtmem_event_currency_fields();
							 ?>
                        </div>


						<div id="van-tab-mapg" class="van-tab" style="<?php echo $tab === 'mapg' ? '' : 'display:none;'; ?>">	
							<h2><?php esc_html_e( 'Google Map Integration', 'momento-event-manager' ); ?></h2>  							
								<?php 
									$google = new class_google_map();
									$google->wtmem_google_map_integration();
								?>
						</div>
								
						
						<div id="van-tab-template" class="van-tab" style="<?php echo $tab === 'template' ? '' : 'display:none;'; ?>">								
								<?php 
									$temp = new class_mem_event_template();
									$temp->wtmem_event_web_template(); 
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
	public static function wtmem_render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}		
		// Save settings
		if ( isset( $_POST['uem_save_settings'] ) && check_admin_referer( 'uem_settings_nonce' ) ) {
			$registration_method = isset( $_POST['uem_registration_method'] ) ? sanitize_text_field( $_POST['uem_registration_method'] ) : 'woocommerce';
			update_option( 'uem_registration_method', $registration_method );
			echo '<div class="notice notice-success"><p>' . __( 'Settings saved successfully!', 'momento-event-manager' ) . '</p></div>';
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
								<label for="uem_registration_method"><?php echo esc_html( 'Registration Method', 'momento-event-manager' ); ?></label>
							</th>
							<td>
								<select name="uem_registration_method" id="uem_registration_method">

									<?php if( get_option('wtmem_wooIntegration_status', 'on') == 'on') {?>

										<option value="woocommerce" <?php selected( $registration_method, 'woocommerce' ); ?>>
											<?php echo esc_html( 'WooCommerce', 'momento-event-manager' ); ?>
										</option>

									<?php } ?>

									<option value="simple" <?php selected( $registration_method, 'simple' ); ?>>
										<?php echo esc_html( 'Simple Registration (Without WooCommerce)', 'momento-event-manager' ); ?>
									</option>
								</select>
								<?php if ( ! $woocommerce_active && $registration_method === 'woocommerce' ) : ?>
									<p class="description" style="color: #d63638;">
										<?php echo esc_html( 'Warning: WooCommerce is not active. Please install and activate WooCommerce to use this registration method.', 'momento-event-manager' ); ?>
									</p>
								<?php endif; ?>
								<p class="description">
									<?php echo esc_html( 'Choose how event registrations will be processed.', 'momento-event-manager'); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Save Settings', 'momento-event-manager'), 'primary', 'uem_save_settings' ); ?>
			</form>
		</div>
		<?php
	}
}