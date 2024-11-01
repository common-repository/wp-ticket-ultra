<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpticketultra, $wptucomplement, $wptultimate, $wptu_private_credentials, $wptu_activity_tracker, $wptu_notes, $wptu_wooco, $wptu_canned_responses, $wptu_staffbackend;

$current_user = $wpticketultra->userpanel->get_user_info();
$user_id = $current_user->ID;

$is_client = $wpticketultra->profile->is_client($user_id);
$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);


if(!$is_client && !$wptu_staffbackend)
{
	
	echo '<div class="wptu-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! You don't have permission to access to this section",'wp-ticket-ultra').'</span></div>';
	
	return;		
}



$datetime_format =  $wpticketultra->get_date_to_display();
$can_i_see_ticket = $wpticketultra->ticket->can_i_see_this_ticket($ticket_id, $user_id);
$ticket = $wpticketultra->ticket->get_one_as_user($ticket_id, $user_id);

if(!$can_i_see_ticket && $is_client){
	
	echo __('Oops! This is not your ticket.','wp-ticket-ultra');
	return;
}

//I Am allowed to see this ticket?
if(!$is_client && !$is_super_admin)
{
	//this user is a staff member
	
	$is_within_my_departments =  $wpticketultra->ticket->is_ticket_within_allowed_departments($user_id, $ticket);
	
	if(!$is_within_my_departments)
	{	
		echo __("Oops! You're not allowed to see this ticket.",'wp-ticket-ultra');
		return;	
	}
}

//track event				
if(isset($wptu_activity_tracker))				
{
	if($is_client)
	{
		
		$wptu_activity_tracker->create_action(1, $user_id, $ticket_id,0 );	
	
	}else{
		
		$wptu_activity_tracker->create_action(6, $user_id, $ticket_id,0 );
		
	
	}

}
				



$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));
$date_updated=  date($datetime_format, strtotime($ticket->ticket_date_last_change));
$nice_time_last_reply = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);

$status_bg_color = $wpticketultra->status->get_one($ticket->ticket_status);

if($ticket->ticket_staff_id=='0')
{
	$owner_label =	__('Unassigned','wp-ticket-ultra');			
				
}else{
	
	$owner = get_user_by( 'id', $ticket->ticket_staff_id );	
	$owner_label =	$owner->display_name;	
}

//get client
$client = get_user_by( 'id', $ticket->ticket_user_id );

echo $this->sucess_message;

if(isset($wptucomplement))
{

	$sla= $wptucomplement->sla->get_ticket_sla_label($ticket);

}



?>


<div class="wptu-ticket-detail-cont">


	
     

    <div class="wptu-ticket-header-quickoptions">
    
        
            <ul>
            
                 <?php if($ticket->ticket_status==1 || $ticket->ticket_status==2 || $ticket->ticket_status==3 || $ticket->ticket_status==4){?>
            
                 <li> <button class="wptu-btn-quick-actions-btn resolve" id="wptu-user-mark-as-resolved" title="<?php _e('Mark As Solved','wp-ticket-ultra')?>" ticket-id="<?php echo $ticket_id;?>"><i class="fa fa-check"></i><?php _e('Mark As Solved','wp-ticket-ultra')?> </button></li>
                 
                 <?php }?> 
                 
                 <?php if($ticket->ticket_status==1 || $ticket->ticket_status==2 || $ticket->ticket_status==3 || $ticket->ticket_status==4 || $ticket->ticket_status==5){?>    
            
                <li> <button class="wptu-btn-quick-actions-btn close" title="<?php _e('Mark As Closed','wp-ticket-ultra')?>" ticket-id="<?php echo $ticket_id;?>" id="wptu-user-mark-as-closed"><i class="fa fa-calendar-o"></i><?php _e('Close Ticket','wp-ticket-ultra')?> </button></li> 
                
                 <?php }?>          
            
            </ul>
           
           
        </div>
        
         

	<div class="wptu-ticket-header"> 
    
     	<h2><?php echo $ticket->ticket_subject?> <span class="wptu-ticket-number"><?php echo $ticket->site_name?> <?php _e('#','wp-ticket-ultra')?><?php echo $ticket_id;?> </span></h2> 
    	 
        <div class="ticket-general-info"><i class="fa fa-calendar-o"></i> <?php _e('Created: ','wp-ticket-ultra')?> <?php echo $date_submited?>   &nbsp;<i class="fa fa-clock-o"></i> <?php _e('Updated: ','wp-ticket-ultra')?> <?php echo $date_updated?> (<span class="wptu-ticket-last-update"><?php echo $nice_time_last_reply;?></span>) <span class="wptu-ticket-client-info"><i class="fa fa-user-o"></i> <?php _e('User: ','wp-ticket-ultra')?><?php echo $client->display_name." ".$client->last_name;?></span></div>  
        
         
         <?php if(isset($wptucomplement) && !$is_client){?>
         
             <div class="wptu-ticket-sla-details">
                
                <?php echo $sla;?>
                
             </div> 
         
         <?php }?>  

	</div> 
    
    
    <div class="wptu-ticket-geninfo-cont" style="background-color:<?php echo $status_bg_color->status_color?>">
    	<ul>
        
        	<li>
            	 <div class="wptu-general-info-title"><?php _e('DEPARTMENT','wp-ticket-ultra')?> </div>
                  <div class="wptu-general-info-value"><?php echo $ticket->department_name;?> </div>
            </li> 
            
            <li>
            	 <div class="wptu-general-info-title"><?php _e('OWNER','wp-ticket-ultra')?> </div>
                  <div class="wptu-general-info-value"><?php echo $owner_label?> </div>
            </li>
            
            <li>
            	 <div class="wptu-general-info-title"><?php _e('TYPE','wp-ticket-ultra')?> </div>
                  <div class="wptu-general-info-value"><?php echo $status_bg_color->status_name?> </div>
            </li> 
            
            <li>
            	 <div class="wptu-general-info-title"><?php _e('STATUS','wp-ticket-ultra')?> </div>
                 <div class="wptu-general-info-value"><?php echo $status_bg_color->status_name?> </div>
            </li> 
            
            <li>
            	 <div class="wptu-general-info-title"><?php _e('PRIORITY','wp-ticket-ultra')?> </div>
                  <div class="wptu-general-info-value"><?php echo $ticket->priority_name;?> </div>
            </li>     
           
           
        </ul>
        
    
    </div>
    
         <h2><span class="wptu-ico-priv"><i class="fa fa-key "></i></span><?php _e('Private Credentials','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="0"><i class="fa fa-sort-desc" id="wptu-close-open-icon-0"></i></a></span></h2>
         
         <div class="wptu-ticket-priv-cred-box" id="wptu-backend-landing-0" style="display:">
         
           <?php if(isset($wptu_private_credentials)){?>
         
             <p><?php _e('Here you can add multiple credentials for multiple systems. The data is encrypted. ','wp-ticket-ultra')?></p>
             
              <div class="wptu-private-credentials-box" id="wptu-private-credentials-list" >
              
                  <?php echo $wptu_private_credentials->get_private_credentials($ticket_id);?>
              
              </div>
              
                     
           <?php }else{?>
           
           	 <p><?php _e('This feature is available as add-on only. ','wp-ticket-ultra')?></p>
            
            
            <?php }?> 
             
             
         </div>
         
         
         <h2><span class="wptu-ico-priv"><i class="fa fa-sticky-note-o  "></i></span><?php _e('Notes','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="000"><i class="fa fa-sort-desc" id="wptu-close-open-icon-000"></i></a></span></h2>
         
         <div class="wptu-ticket-priv-cred-box" id="wptu-backend-landing-000" style="display:none">
         
           <?php if(isset($wptu_notes)){?>
         
            
              <div class="wptu-private-credentials-box" id="wptu-private-notes-list" >
              
                  <?php echo $wptu_notes->ticket_notes($ticket_id);?>
              
              </div>
              
                     
           <?php }else{?>
           
           	 <p><?php _e('This feature is available as add-on only. ','wp-ticket-ultra')?></p>
            
            
            <?php }?> 
             
             
         </div>
         
         <?php if(isset($wptu_wooco) && class_exists( 'WooCommerce') && $ticket->ticket_woocomerce_order_id !=0){?>
         
         <h2><span class="wptu-ico-priv"><i class="fa fa-list  "></i></span><?php _e('Products','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="0099"><i class="fa fa-sort-asc" id="wptu-close-open-icon-0099"></i></a></span></h2>
         
         <div class="" id="wptu-backend-landing-0099" style="display:">
         
              <div class="wptu-ticket-woo-prod-box" id="wptu-private-notes-list" >
              
                  <?php echo $wptu_wooco->ticket_product($ticket);?>
              
              </div>
             
         </div>
         
          <?php }?> 
         
         
    
     <h2><?php _e('Ticket Details','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="Close" class="wptu-widget-backend-colapsable" widget-id="1"><i class="fa fa-sort-desc" id="wptu-close-open-icon-1"></i></a></span></h2>
    
    <div class="wptu-ticket-customo-fields-box" id="wptu-backend-landing-1">
    
       <form action=""  id="wptu-ticket-info-form" name="wptu-ticket-info-form" >
    
       <?php echo $wpticketultra->ticket->get_ticket_edition_form_fields($ticket_id,$ticket->department_id);?>
       
       </form>
    
    </div>
    
     <div class="wptu-ticket-open-close-replies">       
         
          <button name="wptu-ticket-reply-btn" id="wptu-ticket-reply-btn" class="wptu-btn-add-new-replies-ticket" ><i class="fa fa-comments-o"></i> <?php _e('Add reply','wp-ticket-ultra')?> </button>     
      </div>
    
    <div class="wptu-add-reply-cont wptu-ticket-reply-cont" id="wp-add-reply-box">
      
      <div class="wptu-add-reply-header">
      <h3><?php _e('Your Message','wp-ticket-ultra')?> </h3>      
      </div>
      
      <?php //
	  
	  
	  ?>
      
     
    
    <form action="" method="post" id="wptu-submit-reply-form" name="wptu-submit-reply-form" enctype="multipart/form-data">
    
         
         <?php echo $wpticketultra->ticket->get_me_wphtml_editor('wptu_ticket_reply_message', null)?>
        
        
        <input type="hidden" name="wptu-ticket-reply-sub-conf" id="wptu-ticket-reply-sub-conf" value="wptu-conf-ticket">
         <input type="hidden" name="ticket-id" id="ticket-id" value="<?php echo $ticket_id?>">
         
         
         <?php if( (isset($wptu_canned_responses) && !$is_client) || (isset($wptu_canned_responses) && $is_super_admin) ){?>
         <h2><?php _e('Canned Responses','wp-ticket-ultra')?> </h2>
        
         <div class="wptu-ticket-canned-responses-box" >         
             
              <?php echo $wptu_canned_responses->get_canned_responses($ticket_id, $ticket->ticket_website_id);?>           
         
         </div>
     
     <?php }?>
     
         
         <div class="wptu-ticket-open-close-uploader">
         
         <a href="#" id="wptu-ticket-file-uploader-btn"><i class="fa fa-plus"></i> <?php _e('Add files','wp-ticket-ultra')?> </a>
         
         </div>
    
		 <?php //add new reply
		 
		 $display = '';
		 $display .= '<div class="wptu-custom-fields wptu-files-uploader-cont" id="wp-file-uploader-front">';
		
		 $display .= $wpticketultra->backend_end_file_uploader();
		 	
		 $display .= '</div>';
		 
		 echo $display	;
		 
		
		 
		  ?>
          
           <script type="text/javascript">
		 
		
		 
		 
		 </script>
         
          <?php if(isset($wptucomplement) && !$is_client){?>
         
              <div class="wptu-ticket-reply-auto-cont">
                 <p><strong><?php _e('Reply and mark as: ','wp-ticket-ultra')?></strong> </p>
                 
                 <?php  echo $wpticketultra->status->get_all_statuses_list_box_back_end($ticket->ticket_status); ?>
               
               </div>
           
           <?php }?>

		 
		 <div class="wptu-ticket-submit-btn-cont">
		 
			 <button name="wptu-treply-submit" id="wptu-treply-submit" class="wptu-btn-submit-ticket" ><?php _e('SUBMIT','wp-ticket-ultra')?></button>
		 
		</div>
        
        <div class="wptu-ticket-submit-btn-message" id="wptu-reply-message">
        
        </div>
			
		 
        
        
         
         
         
         </form>
     
    </div>
    
    <div class="wptu-replies-cont">
    
    
		<?php //replies
        
         echo $this->get_ticket_replies($ticket->ticket_id);
        
        ?>
    
    </div>
    
    

</div>

 <div id="wptu-spinner" class="wptu-spinner" style="display:none">
            <span> <img src="<?php echo wptu_url?>templates/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo _e('Please wait ...','wp-ticket-ultra')?>
            
	</div>

     <div id="wptu-private-credentials-add-modify" title="<?php _e('Add Private Credential','wp-ticket-ultra')?>"></div>
        <div id="wptu-private-notes-add-modify" title="<?php _e('Add Notes','wp-ticket-ultra')?>"></div>
         <div id="wptu-private-notes-add-viewonly" title="<?php _e('View Note','wp-ticket-ultra')?>"></div>
        
