<?php 
/**
 *
 *  Wpcraft event Associate
 *
 **/
namespace Wpcraft\Metabox;

    class class_meta_event_associate{

        public function wtmem_event_associate_meta_field($post){
            ?>

            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="width:33%; text-align:left;">
                            <?php echo esc_html__('Organizer', 'momento-event-manager'); ?>
                        </th>

                        <th style="width:33%; text-align:left;">
                            <?php echo esc_html__('Volunteer', 'momento-event-manager'); ?>
                        </th>

                        <th style="width:33%; text-align:left;">
                            <?php echo esc_html__('Sponsers', 'momento-event-manager'); ?>
                        </th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>
                            <select name="wtmem_event_orga_name" style="width:100%;">
                                <?php 
                                    $saved_value = get_post_meta($post->ID, 'wtmem_event_orga_name', true);
                                    $organizers = get_posts([
                                        'post_type'      => 'mem_organizer',
                                        'posts_per_page' => -1,
                                        'orderby'        => 'title',
                                        'order'          => 'ASC'
                                    ]);

                                    foreach ($organizers as $org) {
                                        $post_title = $org->post_title;
                                        $post_id = $org->ID;
                                    ?>
                                    <option value="<?php echo esc_attr($post_id); ?>"
                                        <?php selected($saved_value, $post_id); ?>>
                                        <?php echo esc_html($post_title); ?>
                                    </option>    
                                <?php
                                    }
                                  ?>
                            </select>
                        </td>
                        <td>
                        <select name="wtmem_event_volunteer_name" style="width:100%;">
                                <?php 
                                $saved_value = get_post_meta($post->ID, 'wtmem_event_volunteer_name', true);
                                $sponsa = get_posts([
                                    'post_type'      => 'mem_volunteer',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC'
                                ]);

                                foreach ($sponsa as $sponsar) {

                                    $post_title = $sponsar->post_title;
                                    $post_id = $sponsar->ID;
                                ?>

                                <option value="<?php echo esc_attr($post_id); ?>"
                                    <?php selected($saved_value, $post_id); ?>>
                                    <?php echo esc_html($post_title); ?>
                                </option>    
                                <?php    
                                }
                                ?>
                        </select>                                         
                    </td>
                        <td> 
                            
                        <select name="wtmem_event_sponsa_name" style="width:100%;">
                                <?php 
                                $saved_value = get_post_meta($post->ID, 'wtmem_event_sponsa_name', true);
                                $volun = get_posts([
                                    'post_type'      => 'mem_sponsor',
                                    'posts_per_page' => -1,
                                    'orderby'        => 'title',
                                    'order'          => 'ASC'
                                ]);

                                foreach ($volun as $voluns) {

                                    $post_title = $voluns->post_title;
                                    $post_id = $voluns->ID;
                                ?>
                                <option value="<?php echo esc_attr($post_id); ?>"
                                    <?php selected($saved_value, $post_id); ?>>
                                    <?php echo esc_html($post_title); ?>
                                </option>    
                                <?php    
                                }
                                ?>
                        </select>     
                    
                          </td>
                    </tr>
                </tbody>
            </table>
        
        <?php   
        }     

        public function wtmem_save_event_associated_field($post_id) {

            if (isset($_POST['wtmem_event_orga_name'])) {
                update_post_meta( $post_id, 'wtmem_event_orga_name', sanitize_text_field($_POST['wtmem_event_orga_name'])
                );
            }

            if (isset($_POST['wtmem_event_sponsa_name'])) {
                update_post_meta( $post_id, 'wtmem_event_sponsa_name', sanitize_text_field($_POST['wtmem_event_sponsa_name'])
                );
            }

            if (isset($_POST['wtmem_event_volunteer_name'])) {
                update_post_meta( $post_id, 'wtmem_event_volunteer_name', sanitize_text_field($_POST['wtmem_event_volunteer_name'])
                );
            }

        }

    }