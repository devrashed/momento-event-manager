<?php 
/**
 *
 *  Webcartisan event Associate
 *
 **/

    class Class_meta_event_associate{

        public function webcu_event_associate_meta_field($post){
            ?>

            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="width:33%; text-align:left;">
                            <?php echo esc_html__('Organizer', 'mega-event-manager'); ?>
                        </th>

                        <th style="width:33%; text-align:left;">
                            <?php echo esc_html__('Volunteer', 'mega-event-manager'); ?>
                        </th>

                        <th style="width:33%; text-align:left;">
                            <?php echo esc_html__('Sponsers', 'mega-event-manager'); ?>
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>
                        <select name="webcu_event_orga_name" style="width:100%;">
                                <?php 
                                    $saved_value = get_post_meta($post->ID, 'webcu_event_orga_name', true);
                                    $organizers = get_posts([
                                        'post_type'      => 'uem_organizer',
                                        'posts_per_page' => -1,
                                        'orderby'        => 'title',
                                        'order'          => 'ASC'
                                    ]);

                                    foreach ($organizers as $org) {

                                        $orga_name = get_post_meta($org->ID, 'webcu_orga_name', true);

                                        if (!empty($orga_name)) {
                                            echo '<option value="' . esc_attr($orga_name) . '" ' . selected($saved_value, $orga_name, false) . '>' . esc_html($orga_name) . '</option>';
                                        }
                                    }
                                    ?>
                        </select>

                    </td>
                        <td>
                        <select name="webcu_event_sponsa_name" style="width:100%;">
                                <?php 
                                $saved_value = get_post_meta($post->ID, 'webcu_event_sponsa_name', true);
                                $sponsa = get_posts([
                                    'post_type'      => 'event_sponsers',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC'
                                ]);

                                foreach ($sponsa as $sponsar) {

                                    $spons_name = get_post_meta($sponsar->ID, 'webcu_spon_name', true);
                                    if (!empty($spons_name)) {
                                        echo '<option value="' . esc_attr($spons_name) . '" ' . selected($saved_value, $spons_name, false) . '>' . esc_html($spons_name) . '</option>';
                                    }
                                }
                                ?>
                        </select>                                         
                    </td>
                        <td> 
                            
                        <select name="webcu_event_volunteer_name" style="width:100%;">
                                <?php 
                                $saved_value = get_post_meta($post->ID, 'webcu_event_volunteer_name', true);
                                $volun = get_posts([
                                    'post_type'      => 'event_volunteer',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC'
                                ]);

                                foreach ($volun as $voluns) {

                                    $volun_name = get_post_meta($voluns->ID, 'webcu_volun_name', true);
                                    if (!empty($volun_name)) {
                                        echo '<option value="' . esc_attr($volun_name) . '" ' . selected($saved_value, $volun_name, false) . '>' . esc_html($volun_name) . '</option>';
                                    }
                                }
                                ?>
                        </select>     
                    
                    
                        </td>
                    </tr>
                </tbody>
            </table>
        
        <?php   
        }     

        public function webcu_save_event_associated_field($post_id) {

            if (isset($_POST['webcu_event_orga_name'])) {
                update_post_meta( $post_id, 'webcu_event_orga_name', sanitize_text_field($_POST['webcu_event_orga_name'])
                );
            }

            if (isset($_POST['webcu_event_sponsa_name'])) {
                update_post_meta( $post_id, 'webcu_event_sponsa_name', sanitize_text_field($_POST['webcu_event_sponsa_name'])
                );
            }

            if (isset($_POST['webcu_event_volunteer_name'])) {
                update_post_meta( $post_id, 'webcu_event_volunteer_name', sanitize_text_field($_POST['webcu_event_volunteer_name'])
                );
            }

        }


    }
      
  

