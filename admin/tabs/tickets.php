<?php
global $wpticketultra , $wptultimate, $wptucomplement;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$current_user = $wpticketultra->userpanel->get_user_info();
$user_id = $current_user->ID;

$currency_symbol =  $wpticketultra->get_option('paid_membership_symbol');
$date_format =  $wpticketultra->get_int_date_format();
$time_format =  $wpticketultra->get_time_format();
$datetime_format =  $wpticketultra->get_date_to_display();

$howmany = "";
$year = "";
$month = "";
$day = "";
$special_filter = "";
$bup_staff_calendar = "";

$bp_status ="";

if(isset($_GET["howmany"]))
{
  $howmany = sanitize_text_field($_GET["howmany"]);		
}

if(isset($_GET["bp_month"]))
{
  $month = sanitize_text_field($_GET["bp_month"]);		
}

if(isset($_GET["bp_day"]))
{
  $day = sanitize_text_field($_GET["bp_day"]);		
}

if(isset($_GET["bp_year"]))
{
  $year = sanitize_text_field($_GET["bp_year"]);		
}

if(isset($_GET["bp_status"]))
{
  $bp_status = sanitize_text_field($_GET["bp_status"]);		
}

if(isset($_GET["special_filter"]))
{
  $special_filter = sanitize_text_field($_GET["special_filter"]);		
}

if(isset($_GET["bp_sites"]))
{
  $bp_sites = sanitize_text_field($_GET["bp_sites"]);		
}

if(isset($_GET["bp_keyword"]))
{
  $bp_keyword = sanitize_text_field($_GET["bp_keyword"]);		
}


$provider_legend_col = __('Client', 'wp-ticket-ultra');
$ticket_legend = __('All Tickets', 'wp-ticket-ultra');

$tickets_all = $wpticketultra->ticket->get_all_filtered();

echo $wpticketultra->ticket->sucess_message;		
?>

 <div class="wptu-sect  wptu-welcome-panel">

<h2><?php echo $ticket_legend?> </h2>
         
         
         <div class="wptu-tickets-module-filters">
         
          <form action="" method="get">
         <input type="hidden" name="page" value="wpticketultra" />
         <input type="hidden" name="tab" value="tickets" />
         
         
          <input type="text" name="bp_keyword" id="bp_keyword" value="" placeholder="<?php _e('input some text here','wp-ticket-ultra'); ?>" />
          
         
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
                
            <?php if(isset($wptucomplement) ){?>
            <select name="bp_sites" id="bp_sites">
               <option value="" selected="selected"><?php _e('All Products','wp-ticket-ultra'); ?></option>
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
          
                       <button name="wptu-btn-ticket-filter-appo" id="wptu-btn-ticket-filter-appo" class="wptu-button-submit-filter" type="submit"><?php _e('Filter','wp-ticket-ultra')?>	</button>
                               
                
            
        
        
         </form>
         
                 
         
         </div>
         
         
         <form action="" method="post" name="wptu-tlist" id="wptu-tlist">
         
          
          
         <input type="hidden" name="page" value="wpticketultra" />
         <input type="hidden" name="tab" value="tickets" />
         <input type="hidden" name="wptutriggerbulk" value="wptutriggerbulk" />
         
             <div class="wptu-tickets-module-filters">
             
             <select name="bulkaction" id="bulkaction">
             
               <option value="" ><?php _e('Bulk Actions','wp-ticket-ultra')?>	</option>
                <option value="delete" ><?php _e('Delete','wp-ticket-ultra')?>	</option>
                
               
          </select>
          
                       <button name="wptu-btn-ticket-filter-appo" id="wptu-btn-ticket-filter-appo" class="wptu-button-submit-filter" type="submit"><?php _e('Apply','wp-ticket-ultra')?>	</button>
          
            </div>
         
         
    	
          
          
          
                     <?php
			
			
				
				if (!empty($tickets_all)){
				
				
				?>
       
         <table width="100%" class="">
            <thead>
                <tr>
                <th width="4%" >&nbsp;</th>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="14%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>                   
                       
                     
                      	<th width="12%"><?php _e('Product', 'wp-ticket-ultra'); ?></th>                      
                       
                       <th width="16%"><?php _e('Department', 'wp-ticket-ultra'); ?></th>
                   
                    
                    <th width="14%"><?php echo $provider_legend_col; ?></th>
                     <th width="10%"><?php _e('Last Update', 'wp-ticket-ultra'); ?></th>
                    
                   
                      <th width="10%"><?php _e('Owner', 'wp-ticket-ultra'); ?></th>
                     
                   
                   
                     <th width="22%"><?php _e('Subject', 'wp-ticket-ultra'); ?></th>
                    <th width="10%"><?php _e('Priority', 'wp-ticket-ultra'); ?></th>
                    
                     
                     <th width="14%"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
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
                    
                    
                     <td ><input name="wptu-ticket-list[]" type="checkbox" value="<?php echo $ticket->ticket_id; ?>" /></td>
                    <td class="bp_table_row_hide"><?php echo $ticket->ticket_id; ?></td>
                     <td><?php echo  $date_submited; ?>      </td>        
                     
                      
                      <td><?php echo $ticket->site_name; ?> </td>                      
                      
                       
                      <td><?php echo $ticket->department_name; ?> </td>
                     
                      <td><?php echo $owner_label; ?></td>
                       <td><?php echo $nice_time_last_update; ?></td>
                      
                    
                      	<td><?php echo $owner_name; ?></td>
                      
                                        
                    <td><?php echo $ticket->ticket_subject; ?> </td>
                    <td><?php echo  $priority_legend; ?></td>                  
                     
                      <td><?php echo $status_legend; ?></td>
                   <td> <a href="?page=wpticketultra&tab=ticketedit&see&id=<?php echo $ticket->ticket_id?>" class="wptu-appointment-edit-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Edit','wp-ticket-ultra'); ?>"><i class="fa fa-edit"></i></a> <a href="#" class="wptu-appointment-delete-module" appointment-id="<?php echo $ticket->ticket_id?>" title="<?php _e('Trash','wp-ticket-ultra'); ?>"><i class="fa fa-trash-o"></i></a>
                  
               
                  
                   
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
        
          </form>
        
       

 </div>

        
      