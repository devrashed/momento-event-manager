<?php
/**
 *
 *  Template Choose.
 *
 **/

class Class_mem_event_template{ 

   public function __construct() { 
    
       add_action('admin_init', [$this, 'webcu_organiz_register_my_settings']);
   }

   public function webcu_event_web_template() {
    ?>
         <div class="wrap">
            <form method="post" action="">
               <h4><?php esc_html_e( 'Organization Template ', 'ultimate-event-manager' ); ?></h4>  

               <?php wp_nonce_field('webcu_template_action', 'webcu_template_nonce'); ?>

               <?php $orgatemp = get_option('webcu_organize_template'); ?>


               <label class="temp-option">
                  <input type="radio" name="webcu_organize_template" value="left"
                     <?php checked($orgatemp, 'left'); ?> />

                  <div class="img-box">
                     <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                     <div class="bottom-caption">Left Sidebar</div>
                  </div>
               </label>

                  
               <label class="temp-option">
                     <input type="radio" name="webcu_organize_template" value="right"
                        <?php checked($orgatemp, 'right'); ?> />

                  <div class="img-box">
                     <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                     <div class="bottom-caption">Right Sidebar</div>
                  </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="webcu_organize_template" value="container"
                        <?php checked($orgatemp, 'container'); ?> />
                     <div class="img-box">
                     <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                     <div class="bottom-caption">No Sidebar</div>
                  </div>
               </label>

               <h4><?php esc_html_e( 'Volunteer Template', 'ultimate-event-manager' ); ?></h4>  
               
               <?php $voltem = get_option('webcu_volunteer_template'); ?>

               <label class="temp-option">
                     <input type="radio" name="webcu_volunteer_template" value="left"
                        <?php checked($voltem, 'left'); ?> />
                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Left Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="webcu_volunteer_template" value="right"
                     <?php checked($voltem, 'right'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                         <div class="bottom-caption">Right Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="webcu_volunteer_template" value="container"
                     <?php checked($voltem, 'container'); ?> />

                     <div class="img-box">
                       <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                       <div class="bottom-caption">No Sidebar</div>
                     </div>

               </label>


               <h4><?php esc_html_e( 'sponsor Template', 'ultimate-event-manager' ); ?></h4>  
               
               <?php $spontempla = get_option('webcu_sponser_template'); ?>

               <label class="temp-option">
                     <input type="radio" name="webcu_sponser_template" value="left"
                        <?php checked($spontempla, 'left'); ?> />

                      <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Left Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="webcu_sponser_template" value="right"
                        <?php checked($spontempla, 'right'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Right Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="webcu_sponser_template" value="container"
                        <?php checked($spontempla, 'container'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                         <div class="bottom-caption">No Sidebar</div>
                     </div>
               </label>
               

               <h4><?php esc_html_e( 'Event Managment Template', 'ultimate-event-manager' ); ?></h4>  
               
               <?php $eventmanage = get_option('webcu_event_management_template'); ?>

               <label class="temp-option">
                     <input type="radio" name="webcu_event_management_template" value="left"
                        <?php checked($eventmanage, 'left'); ?> />

                      <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Left Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="webcu_event_management_template" value="right"
                        <?php checked($eventmanage, 'right'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Right Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="webcu_event_management_template" value="container"
                        <?php checked($eventmanage, 'container'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                         <div class="bottom-caption">No Sidebar</div>
                     </div>
               </label>

               <div style="clear:both;"> </div>
               <button type="submit" name="webcu_template" class="save-settings">
                     <?php esc_html_e( 'Save Changes', 'ultimate-event-manager' ); ?>
               </button>
           </form>

         </div>

      </div>

         <?php
   }

   public function webcu_organiz_register_my_settings(){

      if ( isset($_POST['webcu_template']) ) {

         if (!isset($_POST['webcu_template_nonce']) || !wp_verify_nonce($_POST['webcu_template_nonce'], 'webcu_template_action')) {
               return;
         }

         if (isset($_POST['webcu_organize_template'])) { $value = sanitize_text_field($_POST['webcu_organize_template']); update_option('webcu_organize_template', $value);
         }

         if (isset($_POST['webcu_volunteer_template'])) { $value = sanitize_text_field($_POST['webcu_volunteer_template']); update_option('webcu_volunteer_template', $value);
         }

         if (isset($_POST['webcu_sponser_template'])) { $value = sanitize_text_field($_POST['webcu_sponser_template']); update_option('webcu_sponser_template', $value);
         }

          if (isset($_POST['webcu_event_management_template'])) { $value = sanitize_text_field($_POST['webcu_event_management_template']); update_option('webcu_event_management_template', $value);
          }

          add_action('admin_notices', function(){
             echo '<div class="updated"><p>Template settings saved successfully!</p></div>';
          });
      }
   }



}