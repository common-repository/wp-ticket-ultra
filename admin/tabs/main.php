<?php
global $wpticketultra, $wptucomplement;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$how_many_upcoming_app = 20;

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

$tickets_total_new= $wpticketultra->ticket->get_tickets_total_first_reply_needed($user_id, 1); // New
$tickets_total_open= $wpticketultra->ticket->get_tickets_total_by_status($user_id, 2); // Open
$tickets_total_pending= $wpticketultra->ticket->get_tickets_total_by_status($user_id, 3); // Pending
$tickets_total_hold= $wpticketultra->ticket->get_tickets_total_by_status($user_id, 4); // Hold



        
?>

<div class="wptu-welcome-panel">

<h1>WP Ticket Ultra</h1>

	<h2><?php _e('Ticket Summary','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?> " class="wptu-widget-home-colapsable" widget-id="0"><i class="fa fa-sort-asc" id="wptu-close-open-icon-0"></i></a></span></h2>
    
     <div class="wptu-main-app-summary" id="wptu-main-cont-home-0">
     
     		 <div class="wptu-main-ticket-summary" >
             
             	<ul>
                
                   <li>                    
                      <small><?php _e('Overdue','wp-ticket-ultra')?> </small>
                      <p style="color: #333"> <?php echo $tickets_total_new?></p>                    
                    </li>
                
                	<li>                    
                      <small><?php _e('First Reply','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(1)->status_color;?>"> <?php echo $tickets_total_new?></p>                    
                    </li>
                    
                    <li> 
                    
                    <a href="?page=wpticketultra&tab=tickets&bp_status=2" title="<?php _e('Open','wp-ticket-ultra')?>">                   
                      <small><?php _e('Open','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(2)->status_color;?>"> <?php echo $tickets_total_open?></p>  
                      
                      </a>                  
                    </li>
                    
                    <li>     
                    
                       <a href="?page=wpticketultra&tab=tickets&bp_status=3" title="<?php _e('Pending','wp-ticket-ultra')?>">               
                      <small><?php _e('Pending','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(3)->status_color;?>"> <?php echo $tickets_total_pending?></p> 
                      
                       </a>                     
                    </li>
                    
                    <li id="wptu-stats-on-hold">
                    
                    <a href="?page=wpticketultra&tab=tickets&bp_status=4" title="<?php _e('On-hold','wp-ticket-ultra')?>">                                  
                      <small><?php _e('On-hold','wp-ticket-ultra')?> </small>
                      <p style="color:<?php echo $wpticketultra->status->get_one(4)->status_color;?>"> <?php echo $tickets_total_hold?></p> 
                      
                      </a>                   
                    </li>
                
                </ul>             
             </div>
     
     	
     
     </div>
     
      
    

	<h2><?php _e('First Reply Needed','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?> " class="wptu-widget-home-colapsable" widget-id="1"><i class="fa fa-sort-asc" id="wptu-close-open-icon-1"></i></a></span></h2>
    	  <div class="wptu-main-app-list" id="wptu-main-cont-home-1">
        
         <?php	if (!empty($tickets_first_reply)){ ?>
       
           <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                 
                    
                                  
                     
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                      
                    
                      
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="12%" id="wptu-ticket-col-staff"><?php _e('Staff', 'wp-ticket-ultra'); ?></th>
                   
                   
                     <th width="16%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                     <th width="10%"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                     
                    <th width="9%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="7%"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_first_reply as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));
				$nice_time_last_update = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);
				
				$date_submited = $date_submited;
							
				$client_id = $ticket->ticket_user_id;	
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);
				
				if($ticket->ticket_staff_id=='' ||$ticket->ticket_staff_id=='0' )
				{
					$owner_legend = __('Unassigned', 'wp-ticket-ultra');
				
				}else{
					
					$owner = get_user_by( 'id', $ticket->ticket_staff_id );					
					$owner_legend = $owner->display_name;					
				
				}
					
			?>
              

                <tr id="wptu_ticket_row_id_<?php echo $ticket->ticket_id; ?>">
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                     
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                     
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                     
                      <td id="wptu-ticket-col-staff"><?php echo $owner_legend; ?></td>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,20); ?> </td>
                     <td ><?php echo  $nice_time_last_update; ?></td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td><?php echo $status_legend; ?></td>
                   <td> <a href="?page=wpticketultra&tab=ticketedit&see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                  
                   &nbsp;<a href="#" class="wptu-trash-ticket" ticket-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
              
                  
                   
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
        
                  <h2><?php _e('Open Tickets','wp-ticket-ultra')?> <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-home-colapsable" widget-id="2"><i class="fa fa-sort-asc" id="wptu-close-open-icon-2"></i></a></span></h2>
    	  <div class="wptu-main-app-list" id="wptu-main-cont-home-2">
          
          
               <?php
						
				
				if (!empty($tickets_open)){
					
					
								
				?>
       
          <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="11%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                       
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    <th width="10%" id="wptu-ticket-col-owner"><?php _e('Owner', 'wp-ticket-ultra'); ?></th>
                    <th width="10%" id="wptu-ticket-col-staff"><?php _e('Last Replier', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                   
                   
                     <th width="12%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="9%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>               
                     
                    
                    <th width="18%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			$filter_name= '';
			$phone= '';
			foreach($tickets_open as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));			
				$client_id = $ticket->ticket_user_id;	
				
				$nice_time_last_reply = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);	
				
				
								
				$last_replier = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
				$last_replier_label =	$last_replier->display_name;
				
				$owner = get_user_by( 'id', $ticket->ticket_staff_id );	
				$owner_label =	$owner->display_name;
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);
								
					
			?>
              

                <tr id="wptu_ticket_row_id_<?php echo $ticket->ticket_id; ?>">
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>
                       <td id="wptu-ticket-col-owner"><?php echo $owner_label; ?></td>
                     
                      <td id="wptu-ticket-col-staff"><?php echo $last_replier_label; ?></td>
                       <td><?php echo $nice_time_last_reply; ?></td>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,20); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                     
                   <td> <a href="?page=wpticketultra&tab=ticketedit&see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                 
                   &nbsp;<a href="#" class="wptu-trash-ticket" ticket-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                
                  
                   
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
          
            <h2><?php _e("Waiting For The Client's Reply",'wp-ticket-ultra')?>  <span class="wptu-widget-backend-colspan"><a href="#" title="<?php _e('Close','wp-ticket-ultra')?>" class="wptu-widget-home-colapsable" widget-id="3"><i class="fa fa-sort-asc" id="wptu-close-open-icon-3"></i></a></span></h2>
            
    	  <div class="wptu-main-app-list" id="wptu-main-cont-home-3">
          
          
               <?php
			
			
				
				if (!empty($tickets_pending)){
					
					
								
				?>
       
          <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="11%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                      <th width="12%" id="wptu-ticket-col-site"><?php _e('Product', 'wp-ticket-ultra'); ?></th>
                       <th width="14%" id="wptu-ticket-col-department"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                    <th width="10%" id="wptu-ticket-col-owner"><?php _e('Owner', 'wp-ticket-ultra'); ?></th>
                    
                    <th width="10%" id="wptu-ticket-col-staff"><?php _e('Last Replier', 'wp-ticket-ultra'); ?></th>
                     <th width="10%"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                   
                   
                     <th width="12%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="9%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                    
                    <th width="18%"><?php _e('Actions', 'wp-ticket-ultra'); ?></th>
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
				
				
				$staff_last_repler = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );
				$last_replier =	$staff_last_repler->display_name;
				
				
				$owner = get_user_by( 'id', $ticket->ticket_staff_id );	
				$owner_label =	$owner->display_name;
				
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);
				
								
					
			?>
              

                <tr id="wptu_ticket_row_id_<?php echo $ticket->ticket_id; ?>">
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      <td id="wptu-ticket-col-site"><?php echo $ticket->site_name; ?> </td>
                      <td id="wptu-ticket-col-department"><?php echo $ticket->department_name; ?> </td>              
                      
                       <td id="wptu-ticket-col-owner"><?php echo  $owner_label; ?></td>                     
                      <td id="wptu-ticket-col-staff"><?php echo $last_replier; ?></td>
                      <td><?php echo $nice_time_last_reply; ?></td>
                   
                    <td><?php echo $wpticketultra->cut_string($ticket->ticket_subject,20); ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                    
                   <td> <a href="?page=wpticketultra&tab=ticketedit&see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a>
                   
                   &nbsp;<a href="#" class="wptu-trash-ticket" ticket-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Delete','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
                  
                   </td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no tickets without a reply from the client.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
          </div>


</div>

     
