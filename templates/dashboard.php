<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpticketultra , $wptultimate, $wptucomplement, $wptu_bugtracker, $wptu_wooco, $wptu_staffbackend;

$current_user = $wpticketultra->userpanel->get_user_info();

$user_id = $current_user->ID;
$user_email = $current_user->user_email;
$howmany = 5;

$currency_symbol =  $wpticketultra->get_option('paid_membership_symbol');
$date_format =  $wpticketultra->get_int_date_format();
$time_format =  $wpticketultra->get_time_format();
$datetime_format =  $wpticketultra->get_date_to_display();


$tickets_all_my_latest = $wpticketultra->ticket->get_my_latest_tickets($user_id); // Status 1 - New

$tickets_first_reply = $wpticketultra->ticket->get_first_reply($user_id); // Status 1 - New
$tickets_pending = $wpticketultra->ticket->get_by_status($user_id, 3); // Pending
$tickets_open = $wpticketultra->ticket->get_by_status($user_id, 2); // Open
$tickets_hold = $wpticketultra->ticket->get_by_status($user_id, 4); // On-hold

$module = "";
$act= "";
$view= "";
$reply= "";


if(isset($_GET["module"])){	$module =  sanitize_text_field($_GET["module"]);	}
if(isset($_GET["act"])){$act =  sanitize_text_field($_GET["act"]);	}
if(isset($_GET["view"])){	$view =  sanitize_text_field($_GET["view"]);}
if(isset($_GET["reply"])){	$reply =  sanitize_text_field($_GET["reply"]);}

$is_client  = $this->is_client($user_id);
$hide_site_column =  $wpticketultra->get_option('hide_site_column');

if(!$is_client && !$wptucomplement){echo "staff section not available"; return;}

$user_type_legend = 'User';
if(!$is_client)
{
	$user_type_legend = 'Staff';
	
}

$tickets_total_new= $wpticketultra->ticket->get_tickets_total_first_reply_needed($user_id, 1); // New
$tickets_total_open= $wpticketultra->ticket->get_tickets_total_by_status($user_id, 2); // Open
$tickets_total_pending= $wpticketultra->ticket->get_tickets_total_by_status($user_id, 3); // Pending
$tickets_total_hold= $wpticketultra->ticket->get_tickets_total_by_status($user_id, 4); // Hold
$tickets_shared= $wpticketultra->ticket->get_tickets_total_shared($user_id); // Shared

//check if staff has access to backend		
if($wpticketultra->userpanel->has_account_permision($user_id, 'wptu_per_backend_access') || $is_client  )
{
	//check if the user's role allows
	
}else{
	
	echo '<div class="wptu-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! You don't have permission to access to this section",'wp-ticket-ultra').'</span></div>';
	
	return;					

}




if(!$is_client && !$wptu_staffbackend)
{
	
	echo '<div class="wptu-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! You don't have permission to access to this section",'wp-ticket-ultra').'</span></div>';
	
	return;		
}
	



?>
<div class="wptu-user-dahsboard-cont">


	<div class="wptu-top-header">   
    
    	<?php echo $wpticketultra->profile->get_user_avatar_top($user_id);?>   
        
        
        <div class="wptu-staff-profile-name">
        	<h1><?php echo $current_user->display_name?></h1>
            <small><?php echo $user_type_legend?></small>
        </div>
        
        <div class="wptu-top-options-book">            
            	                
               
                                
           </div>
        
        
        <div class="wptu-top-options"> 
             <ul>            
             
                 <li><?php echo $wpticketultra->profile->get_user_backend_menu_new('home', 'Main','fa-home');?></li>
                                              
                 <li><?php echo $wpticketultra->profile->get_user_backend_menu_new('displayall', 'Tickets','fa-calendar');?></li>
                 
                 <?php if(isset($wptu_bugtracker) && $wpticketultra->userpanel->has_account_permision($user_id, 'wptu_per_bugs_access') && !$is_client){?>
                 
                	 <li><?php echo $wpticketultra->profile->get_user_backend_menu_new('issues_dashboard', 'Bugs','fa-bug');?></li>   
                 
                 <?php } ?> 
                 
                 
                  <?php if( (isset($wptu_wooco) && $wpticketultra->userpanel->has_account_permision($user_id, 'wptu_woo_orders_access')) || ($is_client && isset($wptu_wooco)) ){?>
                 
                	 <li><?php echo $wpticketultra->profile->get_user_backend_menu_new('orders_list', 'Orders','fa-list');?></li>   
                 
                 <?php } ?>  
                 
                 
                  <li><?php echo $wpticketultra->profile->get_user_backend_menu_new('submit', 'Submit a Ticket','fa-edit');?></li>
                  <li><?php echo $wpticketultra->profile->get_user_backend_menu_new('account', 'Account','fa-address-card-o');?></li>
                  
                 
                  <li><?php echo $wpticketultra->profile->get_user_backend_menu_new('logout', 'Logout','fa-sign-out');?></li>
            
             </ul>
         
         </div> 
             
    </div>
    
    
    <div class="wptu-centered-cont">
    
     <?php 
	 
	 if($is_client && isset($wptucomplement))
	 {
		 
		 $display_message = $wpticketultra->get_option('advanced_noti_on_weekend');
		 
		 if( $display_message=='' || $display_message=='yes')
		 {
    
			 $site_date = date( 'Y-m-d', current_time( 'timestamp', 0 ) );
			 
			 if($wpticketultra->isWeekend($site_date))
			 {
				 $display_message_text = $wpticketultra->get_option('advanced_noti_on_weekend_backend_message');
				 
				 if( $display_message_text=='')
				 {
					 $msg =  __("It's the weekend!. We Work From Monday To Friday From 8AM To 5PM. Since we're on weekend a reply should be received on either Monday or Tuesday. Thank you very much for your understanding.",'wp-ticket-ultra');
					 
				 }else{
					 
					 $msg =  $display_message_text;		 
					 
				}				 
				 
				 $message_weekend = '<div class="wptu-ultra-info"><span>'.$msg.'</span></div>';
				
				echo $message_weekend ;
				 
			 }
		
		}
		 
		 
		 
	 }
	 
	  ?>
    
    
    <?php if($module=='' || $module=='home'){?>  
    
  
      <?php if(!$is_client){
		
				
		?>
    
    
    <h2><?php _e('Ticket Summary','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?> " class="wptu-widget-backend-colapsable" widget-id="0"><i class="fa fa-sort-asc" id="wptu-close-open-icon-0"></i></a></span></h2>
    
     <div class="wptu-main-app-summary" id="wptu-backend-landing-0">
     
     		 <div class="wptu-main-ticket-summary" >
             
             	<ul>
                
              	  <li>  
                    
                    <a href="?module=displayoverdue" title="<?php _e('Overdue','wp-ticket-ultra')?>">                  
                      <small><?php _e('Overdue','wp-ticket-ultra')?> </small>
                      <p style="color:#333"> <?php echo $tickets_total_new?></p> 
                      
                      </a>                   
                    </li>
                
                	<li>  
                    
                    <a href="?module=displayall" title="<?php _e('Need Reply','wp-ticket-ultra')?>">                  
                      <small><?php _e('Need Reply','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(1)->status_color;?>"> <?php echo $tickets_total_new?></p> 
                      
                      </a>                   
                    </li>
                    
                    <li>
                    
                    <a href="?module=displayall&bp_status=2" title="<?php _e('Open','wp-ticket-ultra')?>">                 
                      <small><?php _e('Open','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(2)->status_color;?>"> <?php echo $tickets_total_open?></p>  
                      
                      </a>                  
                    </li>
                    
                    <li>  
                    
                       <a href="?module=displayall&bp_status=3" title="<?php _e('Pending','wp-ticket-ultra')?>">                  
                      <small><?php _e('Pending','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(3)->status_color;?>"> <?php echo $tickets_total_pending?></p> 
                      
                      </a>                   
                    </li>
                    
                    <li id="wptu-stats-on-hold"> 
                      
                      <a href="?module=displayall&bp_status=4" title="<?php _e('On-hold','wp-ticket-ultra')?>">                   
                      <small><?php _e('On-hold','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(4)->status_color;?>"> <?php echo $tickets_total_hold?></p>   
                      
                      </a>                 
                    </li>
                    
                    <li id="wptu-stats-shared">
                    
                      <a href="?module=displayshared" title="<?php _e('Shared','wp-ticket-ultra')?>">                   
                      <small><?php _e('Shared','wp-ticket-ultra')?> </small>
                      <p style="color: #999"> <?php echo $tickets_shared?></p>   
                      </a>                 
                    </li>
                
                </ul>             
             </div>
     
     	
     
     </div>
     
     
     <?php }else{ //this is a client we display custom summary?>
     
     
     
      
    <h2><?php _e('Ticket Summary','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?> " class="wptu-widget-backend-colapsable" widget-id="0"><i class="fa fa-sort-asc" id="wptu-close-open-icon-0"></i></a></span></h2>
    
     <div class="wptu-main-app-summary" id="wptu-backend-landing-0">
     
     		 <div class="wptu-main-ticket-summary" >
             
             	<ul>
                
                <li class="wptuclientd">  
                    
                    <a href="?module=displayall" title="<?php _e('Unassigned','wp-ticket-ultra')?>">                  
                      <small><?php _e('Unassigned','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(1)->status_color;?>"> <?php echo $tickets_total_new?></p> 
                      
                      </a>                   
                    </li>
                
              	    
                    
                    <li class="wptuclientd">
                    
                    <a href="?module=displayall&bp_status=2" title="<?php _e('Open','wp-ticket-ultra')?>">                 
                      <small><?php _e('Open','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(2)->status_color;?>"> <?php echo $tickets_total_open?></p>  
                      
                      </a>                  
                    </li>
                    
                    <li class="wptuclientd">  
                    
                       <a href="?module=displayall&bp_status=3" title="<?php _e('Pending','wp-ticket-ultra')?>">                  
                      <small><?php _e('Pending','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(3)->status_color;?>"> <?php echo $tickets_total_pending?></p> 
                      
                      </a>                   
                    </li>
                    
                    <li id="wptu-stats-on-hold" class="wptuclientd"> 
                      
                      <a href="?module=displayall&bp_status=4" title="<?php _e('On-hold','wp-ticket-ultra')?>">                   
                      <small><?php _e('On-hold','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(4)->status_color;?>"> <?php echo $tickets_total_hold?></p>   
                      
                      </a>                 
                    </li>                  
                   
                
                </ul>             
             </div>
     
     	
     
     </div>
     
     
      <?php }?>
     
     
       <?php if(!$is_client){
		
		$provider_legend_col = __('Staff', 'wp-ticket-ultra');		
		$owner_label = __('Unassigned', 'wp-ticket-ultra');
		
		?>
     
    
          <h2><?php _e('First Reply Needed','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="1"><i class="fa fa-sort-asc" id="wptu-close-open-icon-1"></i></a></span></h2>
    	  <div class="wptu-main-app-list" id="wptu-backend-landing-1">
        
         <?php	if (!empty($tickets_first_reply)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="13%"><?php _e('Date', 'wp-ticket-ultra'); ?></th> 
                    
                    
                      <?php if(!$is_client ){ //display this colum only for staff?>                  
                     
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                      
                      <?php	} ?>
                      
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="14%" id="wptu-ticket-col-staff"><?php echo $provider_legend_col; ?></th>
                   
                   
                     <th width="18%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                     <th width="12%"  id="wptu-ticket-col-lastupdate"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="7%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_first_reply as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));			
				$client_id = $ticket->ticket_user_id;
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);	
				
				$nice_time_last_update = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);	
				
				if($ticket->ticket_staff_id=='' || $ticket->ticket_staff_id=='0')
				{
					$owner_legend = __('Unassigned', 'wp-ticket-ultra');
				
				}else{
					
					$owner = get_user_by( 'id', $ticket->ticket_staff_id );					
					$owner_legend = $owner->display_name;					
				
				}
								
								
					
			?>
              

                <tr>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      <?php if(!$is_client ){ //display this colum only for staff?>
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                      <?php }?>
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td id="wptu-ticket-col-staff"><?php echo $owner_legend; ?></td>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,30); ?> </td>
                     <td  id="wptu-ticket-col-lastupdate"><?php echo $nice_time_last_update; ?></td>
                    
                    
                    <td><?php echo $priority_legend; ?></td>                  
                     
                      <td id="wptu-ticket-col-status"><?php echo $status_legend; ?></td>
                   <td> <a href="?module=see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                   <?php if(!$is_client){?>
                   &nbsp;<a href="#" class="wptu-trash-ticket" ticket-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Send To Trash','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
                   <?php }?>
                  
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no tickets without a first reply.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
        <?php }else{ //this is a client
		
		
		if($is_client){
			
			$provider_legend_col = __('Last-replier', 'wp-ticket-ultra');
			
		}else{
			
			$provider_legend_col = __('Client', 'wp-ticket-ultra');
		
		}
		
		?>
        
        
                  <h2><?php _e('Your Latest Tickets','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="1"><i class="fa fa-sort-asc" id="wptu-close-open-icon-1"></i></a></span></h2>
    	  <div class="wptu-main-app-list" id="wptu-backend-landing-1">
        
         <?php	if (!empty($tickets_all_my_latest)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="13%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                     
                      <?php if(!$is_client ){ //display this colum only for staff?>
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                       <?php }?>
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="16%"><?php echo $provider_legend_col; ?></th>
                     <th width="12%"  id="wptu-ticket-col-lastupdate"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                    
                   
                   
                     <th width="22%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="7%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_all_my_latest as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));			
				$client_id = $ticket->ticket_user_id;		
				
				
				if($is_client){
					
					if($ticket->ticket_staff_id=='0')
					{
						$owner_label =	__('Unassigned','wp-ticket-ultra');			
									
					}else{
						
						$owner = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
						$owner_label =	$owner->display_name;					
					}
					
				
				}else{ //staff member
					
					$client = get_user_by( 'id', $client_id );
					$owner_label =	$client->display_name;					
				}
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);	
				
				$nice_time_last_update = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);	
				
				
								
					
			?>
              

                <tr>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      <?php if(!$is_client ){ //display this colum only for staff?>
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                      <?php	} ?>
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td><?php echo $owner_label; ?></td>
                       <td  id="wptu-ticket-col-lastupdate"><?php echo $nice_time_last_update; ?></td>
                      
                      
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,30); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td id="wptu-ticket-col-status"><?php echo $status_legend; ?></td>
                   <td> <a href="?module=see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                   <?php if(!$is_client){?>
                   &nbsp;<a href="#" class="wptu-appointment-delete-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
                   <?php }?>
                  
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no tickets.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
        
        <?php } //end if staff member?>
        
        
               
          </div>
          
          
           <?php if(!$is_client){
			   
			 
			
				$provider_legend_col = __('Client', 'wp-ticket-ultra');
			   
			   ?>
          
          <h2><?php _e('Open Tickets','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="2"><i class="fa fa-sort-asc" id="wptu-close-open-icon-2"></i></a></span></h2>
    	  <div class="wptu-main-app-list" id="wptu-backend-landing-2">
          
          
               <?php
			
			
				
				if (!empty($tickets_open)){
					
					
								
				?>
       
          <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="14%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                       
                      <?php if(!$is_client ){ //display this colum only for staff?>
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                      <?php }?>
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="16%"><?php echo $provider_legend_col; ?></th>
                     <th width="14%"  id="wptu-ticket-col-lastupdate"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                   
                   
                     <th width="22%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="7%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_open as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));
				$nice_time_last_reply = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);
							
				$client_id = $ticket->ticket_user_id;		
				
				
				if($is_client){
					
					if($ticket->ticket_staff_id=='0')
					{
						$owner_label =	__('Unassigned','wp-ticket-ultra');			
									
					}else{
						
						$owner = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
						$owner_label =	$owner->display_name;					
					}
					
				
				}else{ //staff member
					
					$client = get_user_by( 'id', $client_id );
					$owner_label =	$client->display_name;					
				}
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);	
				
								
					
			?>
              

                <tr>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      <?php if(!$is_client ){ //display this colum only for staff?>
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                      <?php }?>
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td><?php echo $owner_label; ?></td>
                      <td  id="wptu-ticket-col-lastupdate"><?php echo $nice_time_last_reply; ?></td>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,30); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td id="wptu-ticket-col-status"><?php echo $status_legend; ?></td>
                   <td> <a href="?module=see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                   <?php if(!$is_client){?>
                   &nbsp;<a href="#" class="wptu-appointment-delete-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
                   <?php }?>
                  
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no open tickets.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
          </div>
          
          
          <?php	} ?>
          
          
          
          
          
          
           <?php
			
			
				
						
					
					if($is_client){
	
						$title_legend = __('Waiting For Your Reply', 'wp-ticket-ultra');
						
					}else{
						
						$title_legend = __("Waiting For The Client's Reply", 'wp-ticket-ultra');
					
					}
				
				
				?>
          
          
           <h2><?php echo $title_legend;?>  <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="4"><i class="fa fa-sort-asc" id="wptu-close-open-icon-4"></i></a></span></h2>
    	  <div class="wptu-main-app-list" id="wptu-backend-landing-4">
          
          
               <?php
			
			
				
				if (!empty($tickets_pending)){
					
					
								
				?>
       
          <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="14%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                     <?php if(!$is_client ){ //display this colum only for staff?>
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                        <?php }?>
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="16%"><?php echo $provider_legend_col; ?></th>
                     <th width="14%"  id="wptu-ticket-col-lastupdate"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                   
                   
                     <th width="22%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="7%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_pending as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));
				$nice_time_last_reply = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);			
				$client_id = $ticket->ticket_user_id;		
				
				
				if($is_client){
					
					if($ticket->ticket_staff_id=='0')
					{
						$owner_label =	__('Unassigned','wp-ticket-ultra');			
									
					}else{
						
						$owner = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
						$owner_label =	$owner->display_name;					
					}
					
				
				}else{ //staff member
					
					$client = get_user_by( 'id', $client_id );
					$owner_label =	$client->display_name;					
				}
				
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);	
				
								
					
			?>
              

                <tr>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      <?php if(!$is_client ){ //display this colum only for staff?>
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                      <?php }?>
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td><?php echo $owner_label; ?></td>
                      <td  id="wptu-ticket-col-lastupdate"><?php echo $nice_time_last_reply; ?></td>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,30); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td id="wptu-ticket-col-status"><?php echo $status_legend; ?></td>
                   <td> <a href="?module=see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                   <?php if(!$is_client){?>
                   &nbsp;<a href="#" class="wptu-appointment-delete-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
                   <?php }?>
                  
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no tickets without a first reply.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
          </div>
          
          
         <?php if(!$is_client){ //this is for staff only ?>
          
          <h2><?php _e('Requests On-hold','wp-ticket-ultra')?>   <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-backend-colapsable" widget-id="3"><i class="fa fa-sort-asc" id="wptu-close-open-icon-3"></i></a></span></h2>
    	  <div class="wptu-main-app-list" id="wptu-backend-landing-3">
          
          
               <?php
			
			
				
				if (!empty($tickets_hold)){
				
				
				?>
       
            <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="13%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                     <?php if(!$is_client ){ //display this colum only for staff?>
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                        <?php }?>
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="16%"><?php echo $provider_legend_col; ?></th>
                   
                   
                     <th width="22%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="7%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_pending as $ticket) {		
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));			
				$client_id = $ticket->ticket_user_id;		
				
				
				if($is_client){
					
					if($ticket->ticket_staff_id=='0')
					{
						$owner_label =	__('Unassigned','wp-ticket-ultra');			
									
					}else{
						
						$owner = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
						$owner_label =	$owner->display_name;					
					}
					
				
				}else{ //staff member
					
					$client = get_user_by( 'id', $client_id );
					$owner_label =	$client->display_name;					
				}
				
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);	
				
								
					
			?>
              

                <tr>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      <?php if(!$is_client ){ //display this colum only for staff?>
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                      <?php }?>
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td><?php echo $owner_label; ?></td>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,30); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td id="wptu-ticket-col-status"><?php echo $status_legend; ?></td>
                   <td> <a href="?module=see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                   <?php if(!$is_client){?>
                   &nbsp;<a href="#" class="wptu-appointment-delete-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
                   <?php }?>
                  
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no tickets on-hold.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
          </div>
          
          <?php	} ?>
          
          
       <?php }elseif($module=='see'){
		   
		   $ticket_id = (int)$_GET['id'];
		   ?>
       
       
         
      
           <div class="wptu-common-cont">
           
               <?php
			   
			    if ( (isset($_GET['wptu_ticket_key']) && $_GET['wptu_ticket_key'] !='' ) && (isset($_GET['wptu_status']) && $_GET['wptu_status'] =='ok' ) ) 
						{
							echo '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Your request has been sent successfully','wp-ticket-ultra').'</span></div>';
						 
						 }
			   
			    ?>    
           
                <?php  echo $wpticketultra->ticket->edit_ticket($ticket_id, $user_id);?>
           
            </div>
            
           
      <?php }elseif($module=='submit'){
		   
		  // $ticket_id = $_GET['id'];
		   ?>
           
           <?php if( isset($_GET['wptu_site']) && $_GET['wptu_site']!='' )  {	  
				
			  				   				   
				   $site_id = $_GET['wptu_site'];  
				   
				   //get website			   
				   $website = $wpticketultra->site->get_one($site_id);
				   
				   if($website->site_id=='')
				   {
					   $is_valid_site = false;
					   
				   }else{
					   
					   $is_valid_site = true;				   
					   
				   }	 
			   
			   
			   ?>
           
               <h2><?php _e('Submit A Ticket','wp-ticket-ultra')?> | <?php echo  $website->site_name;?></h2>          
          
               <div class="wptu-common-cont" >
               
               		<?php if($is_valid_site){?>
                    
                         
                         <?php //can this user or staff submit a ticket on this site??>                              
						 
						 <?php  echo do_shortcode("[wptu_create_ticket_backend site_id='".$site_id."' display_alignment='left' on_backend='true']");?>
                    
                    <?php }else{ //is not a valid site ID?> 
                    
                    	 <h2><?php _e('Error!','wp-ticket-ultra')?></h2>
                         
                         <div class="wptu-ultra-error"><span><?php _e("You've set an invalid site",'wp-ticket-ultra')?></span></div>        
                     
                    
                    <?php }?> 
               
                </div>
                
          <?php }else{ //display sites list?> 
          
          		 <h2><?php _e('Select a Product','wp-ticket-ultra')?></h2>
                 <div class="wptu-common-cont" >
                    
                    <?php //get sites this user or staff can post in
					
						echo $wpticketultra->get_sites_to_submit_by_staff($user_id);
						
					?>
                 
                 </div>    
          
          <?php }?> 
          
          
      <?php }elseif($module=='orders_list' && isset($wptu_wooco) ){?>
     
     
     <div class="wptu-common-cont" >
     
     				<?php
					
						//issues dashboard
						
										
						echo $wptu_wooco->frm_orders_list();
						
						
						
					?>
     
      </div> 
      
      
       <?php }elseif($module=='order_details' && isset($wptu_wooco) ){?>
     
     
     <div class="wptu-common-cont" >
     
     				<?php
					
							
										
						echo $wptu_wooco->frm_order_details();
						
						
						
					?>
     
      </div> 
          
     <?php }elseif($module=='issues_dashboard' && isset($wptu_bugtracker) ){?>
     
     
     <div class="wptu-common-cont" >
     
     				<?php
					
						//issues dashboard
						
										
						echo $wptu_bugtracker->frm_bugs_dahsboard($user_id);
						
						
						
					?>
     
      </div> 
      
     
      <?php }elseif($module=='issues_edit' && isset($wptu_bugtracker)){?>
     
     
     <div class="wptu-common-cont" >
     
     				<?php
					
						//edit issue 
										
						echo $wptu_bugtracker->frm_bugs_edit($user_id);
						
					?>
     
      </div> 
      
      
      
       <?php }elseif($module=='issues_all' && isset($wptu_bugtracker)){?>
     
     
     <div class="wptu-common-cont" >
     
     				<?php
					
						// display all issues
										
						echo $wptu_bugtracker->frm_bugs_all($user_id);
						
					?>
     
      </div>  
      
      
        
     
          
     <?php }elseif($module=='upload_avatar'){?>
     
     
     
     
      <?php if($user_id==''){?>	
             
             
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
                              
                            <?php echo $wpticketultra->profile->display_avatar_image_to_crop($image_to_crop, $user_id);?>                          
                              
                   </div>
                   
             </div>
            
           
		    <?php }else{  
			
			$user = get_user_by( 'id', $user_id );
			?> 
            
            <div class="wptu-staff-right-avatar " >
            
           
                   <div class="wptu-avatar-drag-drop-sector"  id="wptu-drag-avatar-section">
                   
                   <h3> <?php echo $user->display_name?><?php _e("'s Picture",'wp-ticket-ultra')?></h3>
                        
                             <?php echo $wpticketultra->userpanel->get_user_pic( $user_id, 80, 'avatar', 'rounded', 'dynamic')?>

                                                    
                             <div class="uu-upload-avatar-sect">
                              
                                     <?php echo $wpticketultra->profile->avatar_uploader($user_id)?>  
                              
                             </div>
                             
                        </div>  
                    
             </div>
             
             
              <?php }  ?>
            
             <?php }?>
             
       <?php }elseif($module=='settings'){?>
       
       
        <h2><?php _e('Settings','wp-ticket-ultra')?> </h2>
      
       <div class="wptu-common-cont"> 
       
       
       			           
                                           
                     
                                                                 
        </div>
                     
                     
          
          
     <?php }elseif($module=='account'){?>
     
              
       <h2><i class="fa fa-id-card-o"></i> <?php _e('Personal Details','wp-ticket-ultra')?> </h2>
       
       <?php
       
	   $user_description = get_user_meta( $user_id, 'description', true ); 
	   
	   ?>
       
       <div class="wptu-common-cont"> 
       
       <p><?php  _e('Your Display Name','wp-ticket-ultra');?></p>
       <p><input type="text" name="bup_display_name" id="bup_display_name" value="<?php echo $current_user->display_name?>" /></p>
       
        <p><?php  _e('Your Address','wp-ticket-ultra');?></p>
       <p><input type="text" name="bup_address" id="bup_address" value="<?php echo get_user_meta( $user_id, 'address', true );?>" /></p>
       
        <p><?php  _e('Your City','wp-ticket-ultra');?></p>
       <p><input type="text" name="bup_city" id="bup_city" value="<?php echo get_user_meta( $user_id, 'city', true )?>" /></p>
       
        <p><?php  _e('Your Country','wp-ticket-ultra');?></p>
       <p><input type="text" name="bup_country" id="bup_country" value="<?php echo get_user_meta( $user_id, 'country', true );?>" /></p>
       
       <p><?php  _e('Summary','wp-ticket-ultra');?></p>
       <p><input type="text" name="bup_summary" id="bup_summary" value="<?php echo get_user_meta( $user_id, 'bup_summary', true );?>" /></p>
       
       <p><?php  _e('Description','wp-ticket-ultra');?></p>
       <p>
       <?php  echo $this->get_me_wphtml_editor('bup_description', get_user_meta( $user_id, 'bup_description', true ));?>
       </p>
       
       <p>
                                                  
                         <button name="wptu-backenedb-update-personaldata" id="wptu-backenedb-update-personaldata" class="wptu-button-submit-changes"><?php  _e('UPDATE INFORMATION','wp-ticket-ultra');?>	</button>
                         
                         </p>
                         
                         <p id="wptu-p-update-profile-msg"></p>
       
       </div> 
       
       <?php if(isset($wptu_wooco)){ ?>
       
       		<?php echo $wptu_wooco->get_billing_shipping_info();?>
       
       <?php }?>
     
      <h2><i class="fa fa-lock"></i> <?php _e('Update your Password','wp-ticket-ultra')?> </h2>
      
       <div class="wptu-common-cont">                      
                                           
                     
                       <form method="post" name="wptu-close-account" >
                       <p><?php  _e('Type your New Password','wp-ticket-ultra');?></p>
                 			 <p><input type="password" name="p1" id="p1" /></p>
                            
                             <p><?php  _e('Re-type your New Password','wp-ticket-ultra');?></p>
                 			 <p><input type="password"  name="p2" id="p2" /></p>
                            
                         <p>
                                                  
                         <button name="wptu-backenedb-eset-password" id="wptu-backenedb-eset-password" class="wptu-button-submit-changes" ><?php  _e('RESET PASSWORD','wp-ticket-ultra');?>	</button>
                         
                         </p>
                         
                         <p id="wptu-p-reset-msg"></p>
               		  </form> 
                                           
                     </div>
                     
                     
           <h2> <i class="fa fa-envelope-o"></i> <?php  _e('Update Your Email','wp-ticket-ultra');?>  </h2> 
           
                   <div class="wptu-common-cont">                                           
                     
                       <form method="post" name="wptu-change-email" >
                       <p><?php  _e('Type your New Email','wp-ticket-ultra');?></p>
                 			 <p><input type="text" name="bup_email" id="bup_email" value="<?php echo $user_email?>" /></p>
                                                        
                         <p>
                                                  
                         <button name="wptu-backenedb-update-email" id="wptu-backenedb-update-email" class="wptu-button-submit-changes"><?php  _e('CHANGE EMAIL','wp-ticket-ultra');?>	</button>
                         
                         </p>                         
                         <p id="wptu-p-changeemail-msg"></p>
               		  </form>
                      
                      </div>
                      
        <?php }elseif($module=='displayshared' && !$is_client && isset($wptucomplement)){ //only for staff members
		
		
		
		$howmany = "";
		$year = "";
		$month = "";
		$day = "";
		$special_filter = "";
		$bup_staff_calendar = "";
		
		$bp_status ="";
		
		if(isset($_GET["howmany"]))
		{
			$howmany = $_GET["howmany"];		
		}
		
		if(isset($_GET["bp_month"]))
		{
			$month = $_GET["bp_month"];		
		}
		
		if(isset($_GET["bp_day"]))
		{
			$day = $_GET["bp_day"];		
		}
		
		if(isset($_GET["bp_year"]))
		{
			$year = $_GET["bp_year"];		
		}
		
		if(isset($_GET["bp_status"]))
		{
			$bp_status = $_GET["bp_status"];		
		}
		
		if(isset($_GET["special_filter"]))
		{
			$special_filter = $_GET["special_filter"];		
		}
		
		if($is_client){
			
			$provider_legend_col = __('Last-replier', 'wp-ticket-ultra');
			$ticket_legend = __('All My Tickets', 'wp-ticket-ultra');
			
		}else{
			
			$provider_legend_col = __('Client', 'wp-ticket-ultra');
			$ticket_legend = __('All Tickets', 'wp-ticket-ultra');
		
		}
		
		$tickets_all = $wpticketultra->ticket->get_all_shared_filtered();

		?>
        
            	 <h2><?php _e('All My Shared Tickets','wp-ticket-ultra'); ?> </h2>
         
         
         <div class="wptu-appointments-module-filters">
         
          <form action="" method="get">
         <input type="hidden" name="module" value="displayall" />
          
         
              <select name="bp_month" id="bp_month">
               <option value="" selected="selected"><?php _e('All Months','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=12){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$month) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="bp_day" id="bp_day">
               <option value="" selected="selected"><?php _e('All Days','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=31){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$day) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="bp_year" id="bp_year">
               <option value="" selected="selected"><?php _e('All Years','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 2014;
              
			  while($i <=2020){
			  ?>
               <option value="<?php echo $i?>" <?php if($i==$year) echo 'selected="selected"';?> ><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select>
                
                      
            
            <select name="bp_status" id="bp_status">
            
               <option value="" <?php if($bp_status =="" ) echo 'selected="selected"';?>><?php _e('All Status','wp-ticket-ultra'); ?></option>
               
               <?php
			   
			   $statuses = $wpticketultra->status->get_all_statuses();
			   
			   $list_b = '';
			   
			   foreach($statuses as $status) {
				   
				   $sel = '';
				   
				   if($bp_status==$status->status_id){$sel = 'selected="selected"';}
				   
				    $list_b .= '  <option value="'.$status->status_id.'" '.$sel.' >'.$status->status_name.'</option>';
				   
			   }
			   
			   echo  $list_b;
			   
			   
			   ?>             
               
               
                   
               
          </select>     
                                             
            <select name="howmany" id="howmany">
               <option value="20" <?php if(50==$howmany ||$howmany =="" ) echo 'selected="selected"';?>>50 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                <option value="40" <?php if(80==$howmany ) echo 'selected="selected"';?>>80 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                 <option value="50" <?php if(100==$howmany ) echo 'selected="selected"';?>>100 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                  <option value="80" <?php if(150==$howmany ) echo 'selected="selected"';?>>150 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                   <option value="100" <?php if(200==$howmany ) echo 'selected="selected"';?>>200 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
               
          </select>
          
                       <button name="wptu-btn-calendar-filter-appo" id="wptu-btn-calendar-filter-appo" class="wptu-button-submit-filter"><?php _e('Filter','wp-ticket-ultra')?>	</button>
                </div>  
                
                
            
        
        
         </form>
         
                 
         
         </div>
         
          <div class="wptu-main-app-list">
          
          
          
                     <?php
			
			
				
				if (!empty($tickets_all)){
				
				
				?>
       
         <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="14%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                       
                        <?php if(!$is_client ){ //display this colum only for staff?>
                      	<th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>                      
                        <?php }?>
                       <th width="16%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="14%"><?php echo $provider_legend_col; ?></th>
                     <th width="10%" ><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                    
                     <?php if(!$is_client){ //display this colum only for staff?>
                      <th width="10%"><?php _e('Owner', 'wp-ticket-ultra'); ?></th>
                      <?php }?>
                   
                   
                     <th width="22%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="7%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_all as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));			
				$client_id = $ticket->ticket_user_id;	
				
				$nice_time_last_update = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);	
				
				
				if($is_client){
					
					if($ticket->ticket_staff_id=='0')
					{
						$owner_label =	__('Unassigned','wp-ticket-ultra');			
									
					}else{
						
						$owner = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
						$owner_label =	$owner->display_name;					
					}
					
				
				}else{ //staff member
					
					$client = get_user_by( 'id', $client_id );
					$owner_label =	$client->display_name;					
				}
				
				//get Owner				
				$owner = get_user_by( 'id', $ticket->ticket_staff_id );				
				if($owner->ID==''){$owner_name = __('Unassigned','wp-ticket-ultra'); }else{$owner_name = $owner->display_name; }
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);
				
								
					
			?>
              

                <tr>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                       <?php if(!$is_client){ //display this colum only for staff?>
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>                      
                       <?php }?>
                       
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td><?php echo $owner_label; ?></td>
                       <td><?php echo $nice_time_last_update; ?></td>
                      
                      <?php if(!$is_client){ //display this colum only for staff?>
                      	<td><?php echo $owner_name; ?></td>
                      
                       <?php }?>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,30); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td id="wptu-ticket-col-status"><?php echo $status_legend; ?></td>
                   <td> <a href="?module=see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                                    
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no shared tickets.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
          </div>
          
    <?php }elseif($module=='displayall'){
		
		
		
		$howmany = "";
		$year = "";
		$month = "";
		$day = "";
		$special_filter = "";
		$bup_staff_calendar = "";
		
		$bp_status ="";
		
		if(isset($_GET["howmany"]))
		{
			$howmany = $_GET["howmany"];		
		}
		
		if(isset($_GET["bp_month"]))
		{
			$month = $_GET["bp_month"];		
		}
		
		if(isset($_GET["bp_day"]))
		{
			$day = $_GET["bp_day"];		
		}
		
		if(isset($_GET["bp_year"]))
		{
			$year = $_GET["bp_year"];		
		}
		
		if(isset($_GET["bp_status"]))
		{
			$bp_status = $_GET["bp_status"];		
		}
		
		if(isset($_GET["special_filter"]))
		{
			$special_filter = $_GET["special_filter"];		
		}
		
		if($is_client){
			
			$provider_legend_col = __('Last-replier', 'wp-ticket-ultra');
			$ticket_legend = __('All My Tickets', 'wp-ticket-ultra');
			
		}else{
			
			$provider_legend_col = __('Client', 'wp-ticket-ultra');
			$ticket_legend = __('All Tickets', 'wp-ticket-ultra');
		
		}
		
		$tickets_all = $wpticketultra->ticket->get_all_filtered();

		?>
    
    	 <h2><?php echo $ticket_legend?> </h2>
         
         
         <div class="wptu-appointments-module-filters">
         
          <form action="" method="get">
         <input type="hidden" name="module" value="displayall" />
          
         
              <select name="bp_month" id="bp_month">
               <option value="" selected="selected"><?php _e('All Months','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=12){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$month) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="bp_day" id="bp_day">
               <option value="" selected="selected"><?php _e('All Days','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=31){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$day) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select>
             
             <select name="bp_year" id="bp_year">
               <option value="" selected="selected"><?php _e('All Years','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 2014;
              
			  while($i <=2020){
			  ?>
               <option value="<?php echo $i?>" <?php if($i==$year) echo 'selected="selected"';?> ><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select>
                
            <?php if(isset($wptucomplement) && !$is_client ){?>
            <select name="bp_sites" id="bp_sites">
               <option value="" selected="selected"><?php _e('All Websites','wp-ticket-ultra'); ?></option>
               <?php
			   
			   $websites = $wpticketultra->get_my_allowed_sites_list($user_id);
			   
			   $list_b = '';
			   
			   foreach($websites as $site) {
				   
				   $sel = '';
				   
				   if($bp_sites==$site->site_id){$sel = 'selected="selected"';}
				   
				    $list_b .= '  <option value="'.$site->site_id.'" '.$sel.' >'.$site->site_name.'</option>';
				   
			   }
			   
			   echo  $list_b;
			   
			   
			   ?>      
             </select>
             
            <?php  }?>  
            
            <select name="bp_status" id="bp_status">
            
               <option value="" <?php if($bp_status =="" ) echo 'selected="selected"';?>><?php _e('All Status','wp-ticket-ultra'); ?></option>
               
               <?php
			   
			   $statuses = $wpticketultra->status->get_all_statuses();
			   
			   $list_b = '';
			   
			   foreach($statuses as $status) {
				   
				   $sel = '';
				   
				   if($bp_status==$status->status_id){$sel = 'selected="selected"';}
				   
				    $list_b .= '  <option value="'.$status->status_id.'" '.$sel.' >'.$status->status_name.'</option>';
				   
			   }
			   
			   echo  $list_b;
			   
			   
			   ?>             
               
               
                   
               
          </select>     
                                             
            <select name="howmany" id="howmany">
               <option value="20" <?php if(50==$howmany ||$howmany =="" ) echo 'selected="selected"';?>>50 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                <option value="40" <?php if(80==$howmany ) echo 'selected="selected"';?>>80 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                 <option value="50" <?php if(100==$howmany ) echo 'selected="selected"';?>>100 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                  <option value="80" <?php if(150==$howmany ) echo 'selected="selected"';?>>150 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
                   <option value="100" <?php if(200==$howmany ) echo 'selected="selected"';?>>200 <?php _e('Per Page','wp-ticket-ultra'); ?></option>
               
          </select>
          
                       <button name="wptu-btn-calendar-filter-appo" id="wptu-btn-calendar-filter-appo" class="wptu-button-submit-filter"><?php _e('Filter','wp-ticket-ultra')?>	</button>
                </div>  
                
                
            
        
        
         </form>
         
                 
         
         </div>
    	  <div class="wptu-main-app-list">
          
          
          
                     <?php
			
			
				
				if (!empty($tickets_all)){
				
				
				?>
       
         <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="14%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                       
                        <?php if(!$is_client ){ //display this colum only for staff?>
                      	<th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>                      
                        <?php }?>
                       <th width="16%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="14%"><?php echo $provider_legend_col; ?></th>
                     <th width="10%"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                    
                     <?php if(!$is_client){ //display this colum only for staff?>
                      <th width="10%"><?php _e('Owner', 'wp-ticket-ultra'); ?></th>
                      <?php }?>
                   
                   
                     <th width="22%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="7%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_all as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));			
				$client_id = $ticket->ticket_user_id;	
				
				$nice_time_last_update = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);	
				
				
				if($is_client){
					
					if($ticket->ticket_staff_id=='0')
					{
						$owner_label =	__('Unassigned','wp-ticket-ultra');			
									
					}else{
						
						$owner = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
						$owner_label =	$owner->display_name;					
					}
					
				
				}else{ //staff member
					
					$client = get_user_by( 'id', $client_id );
					$owner_label =	$client->display_name;					
				}
				
				//get Owner				
				$owner = get_user_by( 'id', $ticket->ticket_staff_id );				
				if($owner->ID==''){$owner_name = __('Unassigned','wp-ticket-ultra'); }else{$owner_name = $owner->display_name; }
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);
				
								
					
			?>
              

                <tr>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                       <?php if(!$is_client){ //display this colum only for staff?>
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>                      
                       <?php }?>
                       
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td><?php echo $owner_label; ?></td>
                       <td><?php echo $nice_time_last_update; ?></td>
                      
                      <?php if(!$is_client){ //display this colum only for staff?>
                      	<td><?php echo $owner_name; ?></td>
                      
                       <?php }?>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,30); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td id="wptu-ticket-col-status"><?php echo $status_legend; ?></td>
                   <td> <a href="?module=see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                   <?php if(!$is_client){?>
                   &nbsp;<a href="#" class="wptu-appointment-delete-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
                   <?php }?>
                  
                   
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no tickets.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
          </div>
    
    
    
    
    <?php }?>
    
    </div>
   
   


</div>

 <div id="wptu-spinner" class="wptu-spinner" style="display:none">
            <span> <img src="<?php echo wptu_url?>templates/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo _e('Please wait ...','wp-ticket-ultra')?>
	</div>
    
    <script type="text/javascript">
    
   	 wptu_set_auto_c();
	
	</script>
    
     <div id="wptu-appointment-change-status" title="<?php _e('Appointment Status','wp-ticket-ultra')?>"></div>
     <div id="wptu-new-payment-cont" title="<?php _e('Add Payment','wp-ticket-ultra')?>"></div>
     <div id="wptu-new-note-cont" title="<?php _e('Add Note','wp-ticket-ultra')?>"></div>
    
