<?php 
/**
 *
 *  Webcartisan Ticket price
 *
 **/
class Class_meta_ticket_price {
        
    public function webcu_ticket_price_sections($post) {  
        // Get saved data
        $registration_enabled = get_post_meta($post->ID, '_webcu_tk_registration_enabled', true);
        $tickets = get_post_meta($post->ID, '_webcu_tk_tickets', true);
        $advanced_toggle_state = get_post_meta($post->ID, '_webcu_tk_advanced_toggle', true);
        
        // Set defaults
        if ($registration_enabled === '') {
            $registration_enabled = '1';
        }
        if (!is_array($tickets)) {
            $tickets = array();
        }
        
        // Separate tickets and extras from the same array
        $ticket_items = array();
        $extra_items = array();
        
        if (!empty($tickets)) {
            foreach ($tickets as $key => $item) {
                if (isset($item['type']) && $item['type'] === 'extra') {
                    $extra_items[$key] = $item;
                } else {
                    // Ensure all tickets have the type set
                    $item['type'] = 'ticket';
                    $ticket_items[$key] = $item;
                }
            }
        }
        
        $toggle_class = $registration_enabled == '1' ? 'webcu_tk_on' : '';
        $section_style = $registration_enabled == '1' ? '' : 'display:none;';
        $advanced_toggle_class = $advanced_toggle_state == '1' ? 'webcu_tk_on' : '';
        $advanced_column_style = $advanced_toggle_state == '1' ? '' : 'display:none;';    
        ?>
        <div class="webcu_tk_container">
            <div class="webcu_tk_shortcode-box">
                <div style="font-size:13px;color:#666;margin-bottom:6px"><?php echo esc_html__('Add To Cart Form Shortcode', 'mega-event-manager') ?></div>
                <div class="webcu_tk_shortcode">[event-add-cart-section event="<?php echo esc_attr($post->ID); ?>"]</div>
                <div class="webcu_tk_registration-row">
                    <div style="font-size:13px;color:#666"><?php echo esc_html__('Registration Off/On:', 'mega-event-manager') ?></div>
                    <div id="webcu_tk_registrationToggle" class="webcu_tk_toggle <?php echo esc_attr($toggle_class); ?>">
                        <span class="webcu_tk_knob"></span>
                    </div>
                    <input type="hidden" id="webcu_tk_registration_enabled" name="webcu_tk_registration_enabled" value="<?php echo esc_attr($registration_enabled); ?>" >
                </div>
            </div>
            
            <!-- Ticket Type & Price Settings -->
            <div id="webcu_tk_registrationSection" style="<?php echo esc_attr($section_style); ?>">
                <h2 class="webcu_tk_section-title"><?php echo esc_html__('Ticket Type List :', 'mega-event-manager') ?></h2>
                <div class="webcu_tk_controls-row">
                    <label class="webcu_tk_switch-label"> <?php echo esc_html__('Show Advanced Column:', 'mega-event-manager') ?></label>
                    <div id="webcu_tk_advancedToggle" class="webcu_tk_toggle <?php echo esc_attr($advanced_toggle_class); ?>">
                        <span class="webcu_tk_knob"></span>
                    </div>
                    <input type="hidden" id="webcu_tk_advanced_toggle" name="webcu_tk_advanced_toggle" value="<?php echo esc_attr($advanced_toggle_state); ?>"  >
                </div>
                
                <table class="webcu_tk_ticket-table" id="webcu_tk_ticketTable">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Name', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Short Desc.', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Price', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Quantity', 'mega-event-manager') ?></th>
                            <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Default Qty', 'mega-event-manager') ?></th>
                            <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Reserve Qty', 'mega-event-manager') ?></th>
                            <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Sale End Date', 'mega-event-manager') ?></th>
                            <th class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><?php echo esc_html__('Sale End Time', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Qty Box', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Action ', 'mega-event-manager') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ticket_items)) : ?>
                            <?php foreach ($ticket_items as $key => $ticket) : ?>
                                <tr class="webcu_tk_ticket-row">
                                    <input type="hidden" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][type]" value="ticket">
                                    <td><input type="text" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][name]" value="<?php echo esc_attr($ticket['name'] ?? ''); ?>"></td>
                                    <td><input type="text" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][description]" value="<?php echo esc_attr($ticket['description'] ?? ''); ?>" placeholder="Short description"></td>
                                    <td><input type="number" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][price]" value="<?php echo esc_attr($ticket['price'] ?? '0'); ?>" step="0.01"></td>
                                    <td><input type="number" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][quantity]" value="<?php echo esc_attr($ticket['quantity'] ?? ''); ?>" placeholder="Ex:1"></td>
                                    <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="number" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][default_qty]" value="<?php echo esc_attr($ticket['default_qty'] ?? ''); ?>" placeholder="Ex:1"></td>
                                    <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="number" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][reserve_qty]" value="<?php echo esc_attr($ticket['reserve_qty'] ?? ''); ?>" placeholder="Ex:1"></td>
                                    <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="date" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][sale_end_date]" value="<?php echo esc_attr($ticket['sale_end_date'] ?? ''); ?>"></td>
                                    <td class="webcu_tk_advanced" style="<?php echo esc_attr($advanced_column_style); ?>"><input type="time" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][sale_end_time]" value="<?php echo esc_attr($ticket['sale_end_time'] ?? ''); ?>"></td>
                                    <td>
                                        <select name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][qty_box]">
                                            <option value="Input Box" <?php selected($ticket['qty_box'] ?? '', 'Input Box'); ?>><?php echo esc_html__('Input Box', 'mega-event-manager') ?></option>
                                            <option value="Dropdown" <?php selected($ticket['qty_box'] ?? '', 'Dropdown'); ?>><?php echo esc_html__('Dropdown', 'mega-event-manager') ?></option>
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
                    <button type="button" id="webcu_tk_addTicket" class="webcu_tk_btn webcu_tk_btn-primary"><span class="dashicons dashicons-plus"></span><?php echo esc_html__('Add New Ticket Type', 'mega-event-manager') ?></button>
                </div>
                
                <h3><?php echo esc_html__('Extra service Area :', 'mega-event-manager') ?></h3>
                <div class="webcu_tk_info-bar"><i class="fa fa-info-circle"></i> <?php echo esc_html__('Extra Service as Product that you can sell and it is not included on event package ', 'mega-event-manager') ?></div>
                
                <table class="webcu_tk_extra-table" id="webcu_tk_extraTable">
                    <thead>
                        <tr>
                            <th><?php echo esc_html__('Name', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Price', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Available Qty', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Qty Box', 'mega-event-manager') ?></th>
                            <th><?php echo esc_html__('Action', 'mega-event-manager') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($extra_items)) : ?>
                            <?php foreach ($extra_items as $key => $extra) : ?>
                                <tr class="webcu_tk_extra-row">
                                    <input type="hidden" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][type]" value="extra">
                                    <td><input type="text" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][name]" value="<?php echo esc_attr($extra['name'] ?? ''); ?>" placeholder="Name" /></td>
                                    <td><input type="number" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][price]" value="<?php echo esc_attr($extra['price'] ?? ''); ?>" placeholder="Price" step="0.01" /></td>
                                    <td><input type="number" name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][available_qty]" value="<?php echo esc_attr($extra['available_qty'] ?? ''); ?>" placeholder="Available Qty" /></td>
                                    <td>
                                        <select name="webcu_tk_tickets[<?php echo esc_attr($key); ?>][qty_box]">
                                            <option value="Input Box" <?php selected($extra['qty_box'] ?? '', 'Input Box'); ?>><?php echo esc_html__('Input Box', 'mega-event-manager') ?></option>
                                            <option value="Dropdown" <?php selected($extra['qty_box'] ?? '', 'Dropdown'); ?>><?php echo esc_html__('Dropdown', 'mega-event-manager') ?></option>
                                        </select>
                                    </td>
                                    <td><button type="button" class="webcu_tk_btn webcu_tk_btn-danger webcu_tk_btn-small webcu_tk_remove_extra_row"> ✖</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr class="webcu_tk_empty-row"><td colspan="5"> <?php echo esc_html__('No extra service added yet.', 'mega-event-manager') ?></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div style="margin-bottom:18px">
                    <button type="button" id="webcu_tk_addExtra" class="webcu_tk_btn webcu_tk_btn-primary"><span class="dashicons dashicons-plus"></span><?php echo esc_html__('Add Extra Price', 'mega-event-manager') ?></button>
                </div>
                
                <div class="webcu_tk_links-list">
                    <div>
                        ⚙️<?php echo esc_html__( 'Setup Event Common QTY of All Ticket Types — get', 'mega-event-manager' ); ?>
                        <a href="#"><?php echo esc_html__( 'Global QTY Addon', 'mega-event-manager' ); ?></a>
                    </div>
                    <div>
                        🔖<?php echo esc_html__( 'Special Price Option for each user type or membership — get', 'mega-event-manager' ); ?>
                        <a href="#"><?php echo esc_html__( 'Membership Pricing Addon', 'mega-event-manager' ); ?></a>
                    </div>
                    <div>
                        🔢<?php echo esc_html__( 'Set maximum/minimum quantity buying option with', 'mega-event-manager' ); ?>
                        <a href="#"><?php echo esc_html__( 'Max/Min Qty Addon', 'mega-event-manager' ); ?></a>
                    </div>
                </div>
                
                <div style="text-align:right;color:#999;font-size:11px;margin-top:12px"><?php echo esc_html__( '#WC:', 'mega-event-manager' ); ?><?php echo esc_html($post->ID); ?></div>
            </div>
        </div>
        <?php
    }

    public function webcu_save_meta_ticket_price($post_id) { 
        // Save registration enabled
        if (isset($_POST['webcu_tk_registration_enabled'])) {
            update_post_meta($post_id, '_webcu_tk_registration_enabled', sanitize_text_field(wp_unslash($_POST['webcu_tk_registration_enabled'])));
        }
        
        // Save advanced toggle
        if (isset($_POST['webcu_tk_advanced_toggle'])) {
            update_post_meta($post_id, '_webcu_tk_advanced_toggle', sanitize_text_field(wp_unslash($_POST['webcu_tk_advanced_toggle'])));
        }
        
        // ====== Save ALL tickets (both regular and extra) in one array ========
        $all_tickets = array();
        
        if (isset($_POST['webcu_tk_tickets']) && is_array($_POST['webcu_tk_tickets'])) {
            $raw_tickets = wp_unslash($_POST['webcu_tk_tickets']);
            
            foreach ($raw_tickets as $key => $item) {
                $type = isset($item['type']) ? sanitize_text_field($item['type']) : 'ticket';
                
                // Only save if name is not empty
                if (!empty(trim($item['name'] ?? ''))) {
                    if ($type === 'extra') {
                        // Save as extra service
                        $all_tickets[$key] = array(
                            'type' => 'extra',
                            'name' => sanitize_text_field($item['name']),
                            'price' => floatval($item['price']),
                            'available_qty' => intval($item['available_qty']),
                            'qty_box' => sanitize_text_field($item['qty_box'])
                        );
                    } else {
                        // Save as regular ticket
                        $all_tickets[$key] = array(
                            'type' => 'ticket',
                            'name' => sanitize_text_field($item['name']),
                            'description' => sanitize_text_field($item['description'] ?? ''),
                            'price' => floatval($item['price']),
                            'quantity' => intval($item['quantity']),
                            'default_qty' => isset($item['default_qty']) ? intval($item['default_qty']) : 0,
                            'reserve_qty' => isset($item['reserve_qty']) ? intval($item['reserve_qty']) : 0,
                            'sale_end_date' => sanitize_text_field($item['sale_end_date'] ?? ''),
                            'sale_end_time' => sanitize_text_field($item['sale_end_time'] ?? ''),
                            'qty_box' => sanitize_text_field($item['qty_box'])
                        );
                    }
                }
            }
        }
        
        if (!empty($all_tickets)) {
            update_post_meta($post_id, '_webcu_tk_tickets', $all_tickets);
        } else {
            delete_post_meta($post_id, '_webcu_tk_tickets');
        }
        
        // Remove any old extras meta if it exists
        delete_post_meta($post_id, '_webcu_tk_extras');
    }
}