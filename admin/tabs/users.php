<?php
global $wpticketultra;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$howmany = "20";
$year = "";
$month = "";
$day = "";
$status = "";

$edit = $_GET["edit"];
$avatar = $_GET["avatar"];
$load_staff_id = $wpticketultra->userpanel->get_first_staff_on_list();


if($_GET["ui"]!=''){
	
	$load_staff_id=$_GET["ui"];
}



?>



     
        <div class="wptu-sect ">
        
        <div class="wptu-staff ">
        
        	
            
            
             <?php if($avatar==''){?>	
             
             
                 <div class="wptu-staff-left " id="wptu-staff-list">
            	
                          
            	
            	 </div>
                 
                 <div class="wptu-staff-right " id="wptu-staff-details">
                 </div>
            
            <?php }else{ //upload avatar?>
            
           <?php  
		   
		   $crop_image = $_POST['crop_image'];
		   if( $crop_image=='crop_image') //displays image cropper
			{
			
			 $image_to_crop = $_POST['image_to_crop'];
			 
			
			 ?>
             
             <div class="wptu-staff-right-avatar " >
           		  <div class="pr_tipb_be">
                              
                            <?php echo $wpticketultra->userpanel->display_avatar_image_to_crop($image_to_crop, $avatar);?>                          
                              
                   </div>
                   
             </div>
            
           
		    <?php }else{  
			
			$user = get_user_by( 'id', $avatar );
			?> 
            
            <div class="wptu-staff-right-avatar " >
            
           
                   <div class="wptu-avatar-drag-drop-sector"  id="wptu-drag-avatar-section">
                   
                   <h3> <?php echo $user->display_name?><?php _e("'s Picture",'wp-ticket-ultra')?></h3>
                        
                             <?php echo $wpticketultra->userpanel->get_user_pic( $avatar, 80, 'avatar', 'rounded', 'dynamic')?>

                                                    
                             <div class="uu-upload-avatar-sect">
                              
                                     <?php echo $wpticketultra->userpanel->avatar_uploader($avatar)?>  
                              
                             </div>
                             
                        </div>  
                    
             </div>
             
             
              <?php }  ?>
            
             <?php }?>
        
        	
        </div>        
        </div>
        
        <div id="wptu-breaks-new-box" title="<?php _e('Add Breaks','wpticku')?>"></div>
        
        <div id="bup-spinner" class="wptu-spinner" style="display:">
            <span> <img src="<?php echo wptu_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __('Please wait ...','wpticku')?>
	</div>
        
         <div id="wptu-staff-editor-box" title="<?php _e('Add New Staff Member','wpticku')?>"></div>
        
  

 <script type="text/javascript">
	
			
			 var message_wait_availability ='<img src="<?php echo wptu_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __("Please wait ...","wpticku")?>'; 
			 
			 jQuery("#bup-spinner").hide();		 
			  
			  
			  
			  <?php if($avatar==''){?>	
			  
			   wptu_load_staff_list_adm();
			   
				   <?php if($load_staff_id!=''){?>		  
				  
					setTimeout("wptu_load_staff_details(<?php echo $load_staff_id?>)", 1000);
				  
				  <?php }?>
			  
			   <?php }?>	
				  
			  
		
	</script>
