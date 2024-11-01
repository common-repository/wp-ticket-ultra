<?php
global $wpticketultra, $wptucomplement;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$current_user = $wpticketultra->userpanel->get_user_info();
$user_id = $current_user->ID;



?>




<div class="wptu-sect wptu-welcome-panel">


 <?php if( (isset($_GET['wptu_site']) && $_GET['wptu_site']!='')  ){	   
			    
				
			  // if(isset($wptucomplement))
			   //{
				   				   
				   $site_id = $_GET['wptu_site'];  
				   
				   //get website			   
				   $website = $wpticketultra->site->get_one($site_id);
				   
				   if($website->site_id=='')
				   {
					   $is_valid_site = false;
					   
				   }else{
					   
					   $is_valid_site = true;				   
					   
				   }
				  
				//  }else{ //using free version we retreive default siteID
				  
				 // echo "using free version";
				  
				   //get default			   
				  // $website = $wpticketultra->site->get_one_default();				   
				 //  $site_id = $website->site_id;
				   
				 //  if($website->site_id=='')
				 //  {
					//   $is_valid_site = false;
					//   
				  // }else{
					   
					 //  $is_valid_site = true;				   
					   
				  // }			  
				  
					  
				 // }
			   
			   
			   ?>
           
               <h2><?php _e('Submit A Ticket','wpticku')?> | <?php echo  $website->site_name;?></h2>          
          
               <div class="wptu-common-cont" >
               
               		<?php if($is_valid_site){?>
                    
                         
                         <?php //can this user or staff submit a ticket on this site??>                              
						 
						                         
                         <div class="wptu-front-cont" style=" margin-left:0px; margin-right:0px">


							<?php 
							
							$atts = array(  'site_id' => $site_id	);
							echo $wpticketultra->get_registration_form_on_admin($atts);?>    
                                
                            
                           </div>
                    
                    <?php }else{ //is not a valid site ID?> 
                    
                    	 <h2><?php _e('Error!','wpticku')?></h2>
                         
                         <div class="wptu-ultra-error"><span><?php _e("You've set an invalid site",'wpticku')?></span></div>        
                     
                    
                    <?php }?> 
               
                </div>
                
          <?php }else{ //display sites list?> 
          
          		 <h2><?php _e('Select a Product','wpticku')?></h2>
                 <div class="wptu-common-cont" >
                    
                    <?php //get sites this user or staff can post in
					
						echo $wpticketultra->get_sites_to_submit_by_admin($user_id);
						
					?>
                 
                 </div>    
          
          <?php }?> 

	

</div>

     <div id="wptu-client-new-box" title="<?php _e('Create New Client','wpticku')?>"></div>


 <script type="text/javascript">
    
   	 wptu_set_auto_c();
	 wptu_set_auto_staff();
	
	</script>