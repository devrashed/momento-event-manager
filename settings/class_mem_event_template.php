<?php
namespace Wpcraft\Settings;
/**
 *
 *  Template Choose.
 *
 **/

class class_mem_event_template{ 

   public function __construct() { 
    
       add_action('admin_init', [$this, 'wtmem_organiz_register_my_settings']);
   }

   public function wtmem_event_web_template() {
    ?>
         <div class="wrap">
            <form method="post" action="">
               <h4><?php esc_html_e( 'Organization Template ', 'ultimate-event-manager' ); ?></h4>  

               <?php wp_nonce_field('wtmem_template_action', 'wtmem_template_nonce'); ?>

               <?php $orgatemp = get_option('wtmem_organize_template'); ?>


               <label class="temp-option">
                  <input type="radio" name="wtmem_organize_template" value="left"
                     <?php checked($orgatemp, 'left'); ?> />

                  <div class="img-box">
                     <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                     <div class="bottom-caption">Left Sidebar</div>
                  </div>
               </label>

                  
               <label class="temp-option">
                     <input type="radio" name="wtmem_organize_template" value="right"
                        <?php checked($orgatemp, 'right'); ?> />

                  <div class="img-box">
                     <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                     <div class="bottom-caption">Right Sidebar</div>
                  </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="wtmem_organize_template" value="container"
                        <?php checked($orgatemp, 'container'); ?> />
                     <div class="img-box">
                     <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                     <div class="bottom-caption">No Sidebar</div>
                  </div>
               </label>

               <h4><?php esc_html_e( 'Volunteer Template', 'ultimate-event-manager' ); ?></h4>  
               
               <?php $voltem = get_option('wtmem_volunteer_template'); ?>

               <label class="temp-option">
                     <input type="radio" name="wtmem_volunteer_template" value="left"
                        <?php checked($voltem, 'left'); ?> />
                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Left Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="wtmem_volunteer_template" value="right"
                     <?php checked($voltem, 'right'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                         <div class="bottom-caption">Right Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="wtmem_volunteer_template" value="container"
                     <?php checked($voltem, 'container'); ?> />

                     <div class="img-box">
                       <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                       <div class="bottom-caption">No Sidebar</div>
                     </div>

               </label>


               <h4><?php esc_html_e( 'sponsor Template', 'momento-event-manager' ); ?></h4>  
               
               <?php $spontempla = get_option('wtmem_sponser_template'); ?>

               <label class="temp-option">
                     <input type="radio" name="wtmem_sponser_template" value="left"
                        <?php checked($spontempla, 'left'); ?> />

                      <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Left Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="wtmem_sponser_template" value="right"
                        <?php checked($spontempla, 'right'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Right Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="wtmem_sponser_template" value="container"
                        <?php checked($spontempla, 'container'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                         <div class="bottom-caption">No Sidebar</div>
                     </div>
               </label>
               

               <h4><?php esc_html_e( 'Event Managment Template', 'ultimate-event-manager' ); ?></h4>  
               
               <?php $eventmanage = get_option('wtmem_event_management_template'); ?>

               <label class="temp-option">
                     <input type="radio" name="wtmem_event_management_template" value="left"
                        <?php checked($eventmanage, 'left'); ?> />

                      <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Left Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="wtmem_event_management_template" value="right"
                        <?php checked($eventmanage, 'right'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                        <div class="bottom-caption">Right Sidebar</div>
                     </div>
               </label>

               <label class="temp-option">
                     <input type="radio" name="wtmem_event_management_template" value="container"
                        <?php checked($eventmanage, 'container'); ?> />

                     <div class="img-box">
                        <img src="<?php echo MEM_EVENT_ASSETS . '/image/placeholder.png'; ?>">
                         <div class="bottom-caption">No Sidebar</div>
                     </div>
               </label>

               <div style="clear:both;"> </div>
               <button type="submit" name="wtmem_template" class="save-settings">
                     <?php esc_html_e( 'Save Changes', 'ultimate-event-manager' ); ?>
               </button>
           </form>

         </div>

      </div>

         <?php
   }

   public function wtmem_organiz_register_my_settings(){

      if ( isset($_POST['wtmem_template']) ) {

         if (!isset($_POST['wtmem_template_nonce']) || !wp_verify_nonce($_POST['wtmem_template_nonce'], 'wtmem_template_action')) {
               return;
         }

         if (isset($_POST['wtmem_organize_template'])) { $value = sanitize_text_field($_POST['wtmem_organize_template']); update_option('wtmem_organize_template', $value);
         }

         if (isset($_POST['wtmem_volunteer_template'])) { $value = sanitize_text_field($_POST['wtmem_volunteer_template']); update_option('wtmem_volunteer_template', $value);
         }

         if (isset($_POST['wtmem_sponser_template'])) { $value = sanitize_text_field($_POST['wtmem_sponser_template']); update_option('wtmem_sponser_template', $value);
         }

          if (isset($_POST['wtmem_event_management_template'])) { $value = sanitize_text_field($_POST['wtmem_event_management_template']); update_option('wtmem_event_management_template', $value);
          }

          add_action('admin_notices', function(){
             echo '<div class="updated"><p>Template settings saved successfully!</p></div>';
          });
      }
   }
}
new Class_mem_event_template();  