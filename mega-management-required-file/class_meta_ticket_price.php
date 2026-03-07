<?php 
/**
 *
 *  Webartisan setting section
 *
 **/

class Class_meta_ticket_price{ 
        
    public function webcu_ticket_price_sections($post) {  
    
           // Get saved data
            $registration_enabled = get_post_meta($post->ID, '_webcu_tk_registration_enabled', true);
            $all_tickets = get_post_meta($post->ID, '_webcu_tk_tickets', true);
            $advanced_toggle_state = get_post_meta($post->ID, '_webcu_tk_advanced_toggle', true);
            
            // Set defaults
            if ($registration_enabled === '') {
                $registration_enabled = '1';
            }
            
            // Separate regular and extra tickets from combined data
            $tickets = array();
            $extras = array();
            
            if (is_array($all_tickets)) {
                if (isset($all_tickets['regular_tickets'])) {
                    $tickets = $all_tickets['regular_tickets'];
                }
                if (isset($all_tickets['extra_tickets'])) {
                    $extras = $all_tickets['extra_tickets'];
                }
            }
            
            if (!is_array($tickets)) {
                $tickets = array();
            }
            if (!is_array($extras)) {
                $extras = array();
            }
            
            $toggle_class = $registration_enabled == '1' ? 'webcu_tk_on' : '';
            $section_style = $registration_enabled == '1' ? '' : 'display:none;';
            $advanced_toggle_class = $advanced_toggle_state == '1' ? 'webcu_tk_on' : '';
            $advanced_column_style = $advanced_toggle_state == '1' ? '' : 'display:none;';    
        ?>
        <div class="webcu_tk_container">
          <div class="webcu_tk_shortcode-box">
            <div style="font-size:13px;color:#666;margin-bottom:6px"><?php echo esc_html__('Add To Cart Form Shortcode', 'ultimate-event-manager') ?></div>
            <div class="webcu_tk_shortcode">[event-add-cart-section event="<?php echo esc_attr($post->ID); ?>"]</div>
            <div class="webcu_tk_registration-row">
              <div style="font-size:13px;color:#666"><?php echo esc_html__('Registration Off/On:', 'ultimate-event-manager') ?></div>
              <div id="webcu_tk_registrationToggle" class="webcu_tk_toggle <?php echo esc_attr($toggle_class); ?>">
                <span class="webcu_tk_knob"></span>
              </div>
              <input type="hidden" id="webcu_tk_registration_enabled" name="webcu_tk_registration_enabled" value="<?php echo esc_attr($registration_enabled); ?>" >
            </div>
          </div>
          

          <!-- Ticket Type & Price Settings -->


          <div id="webcu_tk_registrationSection" style="<?php echo esc_attr($section_style); ?>">
            <h2 class="webcu_tk_section-title"><?php echo esc_html__('Ticket Type List :', 'ultimate-event-manager') ?></h2>
            <div class="webcu_tk_controls-row">
              <label class="webcu_tk_switch-label"> <?php echo esc_html__('Show Advanced Column:', 'ultimate-event-manager') ?></label>
              <div id="webcu_tk_advancedToggle" class="webcu_tk_toggle <?php echo esc_attr($advanced_toggle_class); ?>">
                <span class="webcu_tk_knob"></span>
              </div>
              <input type="hidden" id="webcu_tk_advanced_toggle" name="webcu_tk_advanced_toggle" value="<?php echo esc_attr($advanced_toggle_state); ?>"  >
            </div>
            
            <table class="webcu_tk_ticket-table" id="webcu_tk_ticketTable">
              <thead>
                <tr>
                  <th><?php echo esc_html__('Name', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Short Desc.', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Price', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Quantity', 'ultimate-event-manager') ?></th>
                  <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Default Qty', 'ultimate-event-manager') ?></th>
                  <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Reserve Qty', 'ultimate-event-manager') ?></th>

                  <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Sale Start Date', 'ultimate-event-manager') ?></th>
                  <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Sale Start Time', 'ultimate-event-manager') ?></th>

                  <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Sale End Date', 'ultimate-event-manager') ?></th>
                  <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Sale End Time', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Qty Box', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Action ', 'ultimate-event-manager') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($tickets)) : ?>
                  <?php foreach ($tickets as $key => $ticket) : ?>
                    <tr class="webcu_tk_ticket-row">
                      <td><input type="text" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][name]" value="<?php echo esc_attr($ticket['name']); ?>"></td>
                      <td><input type="text" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][desc]" value="<?php echo esc_attr($ticket['desc']); ?>" placeholder="Short description"></td>
                      <td><input type="number" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][price]" value="<?php echo esc_attr($ticket['price']); ?>"></td>
                      <td><input type="number" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][capacity]" value="<?php echo esc_attr($ticket['capacity']); ?>" placeholder="Ex:1"></td>

                      <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="number" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][default_qty]" value="<?php echo esc_attr(isset($ticket['default_qty']) ? $ticket['default_qty'] : ''); ?>" placeholder="Ex:1"></td>
                      <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="number" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][reserve_qty]" value="<?php echo esc_attr(isset($ticket['reserve_qty']) ? $ticket['reserve_qty'] : ''); ?>" placeholder="Ex:1"></td>

                      <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="date" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][sale_start_date]" value="<?php echo esc_attr(isset($ticket['sale_start_date']) ? $ticket['sale_start_date'] : ''); ?>"></td>
                      <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="time" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][sale_start_time]" value="<?php echo esc_attr(isset($ticket['sale_start_time']) ? $ticket['sale_start_time'] : ''); ?>"></td>

                      <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="date" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][sale_end_date]" value="<?php echo esc_attr(isset($ticket['sale_end_date']) ? $ticket['sale_end_date'] : ''); ?>"></td>
                      <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="time" name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][sale_end_time]" value="<?php echo esc_attr(isset($ticket['sale_end_time']) ? $ticket['sale_end_time'] : ''); ?>"></td>
                      <td>
                        <select name="webcu_tk_regular_tickets[<?php echo esc_attr($key); ?>][qty_box]">
                          <option value="Input Box" <?php selected($ticket['qty_box'], 'Input Box'); ?>><?php echo esc_html__('Input Box', 'ultimate-event-manager') ?></option>
                          <option value="Dropdown" <?php selected($ticket['qty_box'], 'Dropdown'); ?>><?php echo esc_html__('Dropdown', 'ultimate-event-manager') ?></option>
                        </select>
                      </td>
                      <td class="webcu_tk_action-icons">
                        <button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_tk_remove-row" title="Remove">✖</button>
                        <button type="button" class="webcu_tk_btn webcu_tk_btn-outline webcu_tk_btn-small webcu_tk_move-row" title="Drag Top">☰</button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>



            <!-- Extra service Area -->          
            
            <div style="margin-bottom:18px">
              <button type="button" id="webcu_tk_addTicket" class="webcu_tk_btn webcu_tk_btn-primary"><span class="dashicons dashicons-plus"></span><?php echo esc_html__('Add New Ticket Type', 'ultimate-event-manager') ?></button>
            </div>
            
            <h3><?php echo esc_html__('Extra service Area :', 'ultimate-event-manager') ?></h3>
            <div class="webcu_tk_info-bar"><i class="fa fa-info-circle"></i> <?php echo esc_html__('Extra Service as Product that you can sell and it is not included on event package ', 'ultimate-event-manager') ?></div>
            
            <table class="webcu_tk_extra-table" id="webcu_tk_extraTable">
              <thead>
                <tr>
                  <th><?php echo esc_html__('Name', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Price', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Available Qty', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Qty Box', 'ultimate-event-manager') ?></th>
                  <th><?php echo esc_html__('Action', 'ultimate-event-manager') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($extras)) : ?>
                  <?php foreach ($extras as $key => $extra) : ?>
                    <tr>
                      <td><input type="text" name="webcu_tk_extra_tickets[<?php echo esc_attr($key); ?>][name]" value="<?php echo esc_attr($extra['name']); ?>" placeholder="Name" /></td>
                      <td><input type="number" name="webcu_tk_extra_tickets[<?php echo esc_attr($key); ?>][price]" value="<?php echo esc_attr($extra['price']); ?>" placeholder="Price" /></td>
                      <td><input type="number" name="webcu_tk_extra_tickets[<?php echo esc_attr($key); ?>][available_qty]" value="<?php echo esc_attr($extra['available_qty']); ?>" placeholder="Available Qty" /></td>
                      <td>
                        <select name="webcu_tk_extra_tickets[<?php echo esc_attr($key); ?>][qty_box]">
                          <option value="Input Box" <?php selected($extra['qty_box'], 'Input Box'); ?>><?php echo esc_html__('Input Box', 'ultimate-event-manager') ?></option>
                          <option value="Dropdown" <?php selected($extra['qty_box'], 'Dropdown'); ?>><?php echo esc_html__('Dropdown', 'ultimate-event-manager') ?></option>
                        </select>
                      </td>
                      <td><button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_tk_remove_extra_row"> ✖</button></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else : ?>
                  <tr class="webcu_tk_empty-row"><td colspan="5"> <?php echo esc_html__('No extra service added yet.', 'ultimate-event-manager') ?></td></tr>
                <?php endif; ?>
              </tbody>
            </table>
            
            <div style="margin-bottom:18px">
              <button type="button" id="webcu_tk_addExtra" class="webcu_tk_btn webcu_tk_btn-primary"><span class="dashicons dashicons-plus"></span><?php echo esc_html__('Add Extra Price', 'ultimate-event-manager') ?></button>
            </div>
            
            <div class="webcu_tk_links-list">
              <div>
                 ⚙️<?php echo esc_html__( 'Setup Event Common QTY of All Ticket Types — get', 'ultimate-event-manager' ); ?>
                    <a href="#"><?php echo esc_html__( 'Global QTY Addon', 'ultimate-event-manager' ); ?></a>
                </div>

                <div>
                  🔖<?php echo esc_html__( 'Special Price Option for each user type or membership — get', 'ultimate-event-manager' ); ?>
                    <a href="#"><?php echo esc_html__( 'Membership Pricing Addon', 'ultimate-event-manager' ); ?></a>
                </div>

                <div>
                 🔢<?php echo esc_html__( 'Set maximum/minimum quantity buying option with', 'ultimate-event-manager' ); ?>
                    <a href="#"><?php echo esc_html__( 'Max/Min Qty Addon', 'ultimate-event-manager' ); ?></a>
                </div>

            </div>
            
            <div style="text-align:right;color:#999;font-size:11px;margin-top:12px"><?php echo esc_html__( '#WC:', 'ultimate-event-manager' ); ?><?php echo esc_html($post->ID); ?></div>
          </div>
        </div>
    
  
        <?php
      }

      public function webcu_save_meta_ticket_price($post_id){ 

        // Save registration enabled
        if (isset($_POST['webcu_tk_registration_enabled'])) {
            update_post_meta($post_id, '_webcu_tk_registration_enabled', sanitize_text_field(wp_unslash($_POST['webcu_tk_registration_enabled'])));
        }
        
        // Save advanced toggle state
        if (isset($_POST['webcu_tk_advanced_toggle'])) {
            update_post_meta($post_id, '_webcu_tk_advanced_toggle', sanitize_text_field(wp_unslash($_POST['webcu_tk_advanced_toggle'])));
        }
        
        // ====== Combine and Save Both Tickets and Extras in Single Meta Key ========

        $all_tickets = array();
        
        // Process regular tickets
        if (isset($_POST['webcu_tk_regular_tickets']) && is_array($_POST['webcu_tk_regular_tickets'])) {
            $raw_tickets = wp_unslash($_POST['webcu_tk_regular_tickets']);
            $regular_tickets = array();
            
            foreach ($raw_tickets as $key => $ticket) {
                if (!empty($ticket['name'])) { // Only save if name is not empty
                    $regular_tickets[$key] = array(
                        'name'         => sanitize_text_field($ticket['name']),
                        'desc'         => sanitize_text_field($ticket['desc']),
                        'price'        => floatval($ticket['price']),
                        'capacity'     => intval($ticket['capacity']),
                        'default_qty'  => isset($ticket['default_qty']) ? intval($ticket['default_qty']) : '',
                        'reserve_qty'  => isset($ticket['reserve_qty']) ? intval($ticket['reserve_qty']) : '',
                        'sale_start_date'=> isset($ticket['sale_start_date']) ? sanitize_text_field($ticket['sale_start_date']) : '',
                        'sale_start_time'=> isset($ticket['sale_start_time']) ? sanitize_text_field($ticket['sale_start_time']) : '',
                        'sale_end_date'=> isset($ticket['sale_end_date']) ? sanitize_text_field($ticket['sale_end_date']) : '',
                        'sale_end_time'=> isset($ticket['sale_end_time']) ? sanitize_text_field($ticket['sale_end_time']) : '',
                        'qty_box'      => sanitize_text_field($ticket['qty_box']),
                    );
                }
            }
            $all_tickets['regular_tickets'] = $regular_tickets;
        }
        
        // Process extra tickets
        if (isset($_POST['webcu_tk_extra_tickets']) && is_array($_POST['webcu_tk_extra_tickets'])) {
            $raw_extras = wp_unslash($_POST['webcu_tk_extra_tickets']);
            $extra_tickets = array();
            
            foreach ($raw_extras as $key => $extra) {
                if (!empty($extra['name'])) { // Only save if name is not empty
                    $extra_tickets[$key] = array(
                        'name' => sanitize_text_field($extra['name']),
                        'price' => floatval($extra['price']),
                        'available_qty' => intval($extra['available_qty']),
                        'qty_box' => sanitize_text_field($extra['qty_box'])
                    );
                }
            }
            $all_tickets['extra_tickets'] = $extra_tickets;
        }
        
        // Save combined data
        if (!empty($all_tickets)) {
            update_post_meta($post_id, '_webcu_tk_tickets', $all_tickets);
        } else {
            update_post_meta($post_id, '_webcu_tk_tickets', array(
                'regular_tickets' => array(),
                'extra_tickets' => array()
            ));
        }
      }

}