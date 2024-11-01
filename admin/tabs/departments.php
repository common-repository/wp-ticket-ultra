<?php
global $wpticketultra;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		
?>

        
        <div class="wptu-sect wptu-welcome-panel"> 
        
                
           <div class="wptu-services">
           
           		<div class="wptu-categories" id="wptu-sites-list">
                
                 
                 
                                 
                </div>
                
                <div class="wptu-services" id="wptu-departments-list">
                
                 
                
                
                </div>
           
               
           
           </div>
       
       
         
        
        </div>
        
        <div id="wptu-category-editor-box"></div>
        <div id="wptu-department-add-department-box" title="<?php echo __('Add Department','wp-ticket-ultra')?>"></div>
         <div id="wptu-department-edit-department-box" title="<?php echo __('Edit Department','wp-ticket-ultra')?>"></div>
        
        
         <div id="wptu-site-add-department-box" title="<?php echo __('Add Product','wp-ticket-ultra')?>"></div>
         
         <div id="wptu-department-delete-box" title="<?php echo __('Delete Department','wp-ticket-ultra')?>"></div>
         
         <div id="wptu-edit-product-box" title="<?php echo __('Edit Product','wp-ticket-ultra')?>"></div>
         <div id="wptu-delete-product-box" title="<?php echo __('Delete Product','wp-ticket-ultra')?>"></div>
        
        
         <script type="text/javascript">
		 
			 var err_message_category_name ="<?php _e('Please input a name.','wp-ticket-ultra'); ?>";  
		   		 
			 wptu_load_sites();
			 wptu_load_departments();
		 </script>
<div id="bup-spinner" class="wptu-spinner" style="display:">
            <span> <img src="<?php echo wptu_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __('Please wait ...','wp-ticket-ultra')?>
	</div>