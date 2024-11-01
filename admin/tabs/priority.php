<?php
global $wpticketultra;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		
?>

        
        <div class="wptu-sect wptu-welcome-panel"> 
        
                
           <div class="wptu-customse">          
           		                
                <div class="wptu-priorities" id="wptu-priorities-list">                  
                
                
                </div>
           
           </div>
       
       
         
        
        </div>
        
        <div id="wptu-priority-editor-box"></div>
        <div id="wptu-priority-add-priority-box" title="<?php echo __('Add Priority','wpticku')?>"></div>
        <div id="wptu-priority-edit-priority-box" title="<?php echo __('Edit Priority','wpticku')?>"></div>
        
         <div id="wptu-priority-delete-box" title="<?php echo __('Delete Priority','wpticku')?>"></div>
       
        
         <script type="text/javascript">
		 
			 var err_message_category_name ="<?php _e('Please input a name.','wpticku'); ?>";  
		   		 
			 wptu_load_priorities();
		 </script>
<div id="bup-spinner" class="wptu-spinner" style="display:">
            <span> <img src="<?php echo wptu_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __('Please wait ...','wpticku')?>
	</div>