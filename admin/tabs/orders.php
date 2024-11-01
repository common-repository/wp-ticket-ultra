<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
		
?>

        
       <div class="bup-sect bup-welcome-panel">
        
        <h3><?php _e('Payments','wp-ticket-ultra'); ?></h3>
        
       
       
        <form action="" method="get">
         <input type="hidden" name="page" value="bookingultra" />
          <input type="hidden" name="tab" value="orders" />
        
        <div class="bup-ultra-success bup-notification"><?php _e('Success ','wp-ticket-ultra'); ?></div>
        
        <div class="user-ultra-sect-second user-ultra-rounded" >
        
                  
        
         
        
         
           <table width="100%" border="0" cellspacing="0" cellpadding="0">
           <tr>
             <td width="17%"><?php _e('Keywords: ','wp-ticket-ultra'); ?></td>
             <td width="5%"><?php _e('Month: ','wp-ticket-ultra'); ?></td>
             <td width="5%"><?php _e('Day: ','wp-ticket-ultra'); ?></td>
             <td width="52%"><?php _e('Year: ','wp-ticket-ultra'); ?></td>
             <td width="21%">&nbsp;</td>
           </tr>
           <tr>
             <td><input type="text" name="keyword" id="keyword" placeholder="<?php _e('write some text here ...','wp-ticket-ultra'); ?>" /></td>
             <td><select name="month" id="month">
               <option value="" selected="selected"><?php _e('All','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=12){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$month) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td><select name="day" id="day">
               <option value="" selected="selected"><?php _e('All','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 1;
              
			  while($i <=31){
			  ?>
               <option value="<?php echo $i?>"  <?php if($i==$day) echo 'selected="selected"';?>><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td><select name="year" id="year">
               <option value="" selected="selected"><?php _e('All','wp-ticket-ultra'); ?></option>
               <?php
			  
			  $i = 2014;
              
			  while($i <=2020){
			  ?>
               <option value="<?php echo $i?>" <?php if($i==$year) echo 'selected="selected"';?> ><?php echo $i?></option>
               <?php 
			    $i++;
			   }?>
             </select></td>
             <td>&nbsp;</td>
           </tr>
          </table>
         
         <p>
         
         <button><?php _e('Filter','wp-ticket-ultra'); ?></button>
        </p>
        
       
        </div>
        
        
          <p> <?php _e('Total: ','wp-ticket-ultra'); ?> <?php echo $bookingultrapro->order->total_result;?> | <?php _e('Displaying per page: ','wp-ticket-ultra'); ?>: <select name="howmany" id="howmany">
               <option value="20" <?php if(20==$howmany ||$howmany =="" ) echo 'selected="selected"';?>>20</option>
                <option value="40" <?php if(40==$howmany ) echo 'selected="selected"';?>>40</option>
                 <option value="50" <?php if(50==$howmany ) echo 'selected="selected"';?>>50</option>
                  <option value="80" <?php if(80==$howmany ) echo 'selected="selected"';?>>80</option>
                   <option value="100" <?php if(100==$howmany ) echo 'selected="selected"';?>>100</option>
               
          </select></p>
        
         </form>
         
                 
         
         </div>
         
         
         <div class="bup-sect bup-welcome-panel">
        
         <?php
			
			
				
				if (!empty($orders)){
				
				
				?>
       
           <table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>
                <tr>
                    <th width="4%"><?php _e('#', 'wp-ticket-ultra'); ?></th>
                    <th width="6%"><?php _e('A. #', 'wp-ticket-ultra'); ?></th>
                     <th width="11%"><?php _e('Date', 'wp-ticket-ultra'); ?></th>
                    
                    <th width="23%"><?php _e('Client', 'wp-ticket-ultra'); ?></th>
                     <th width="18%"><?php _e('Service', 'wp-ticket-ultra'); ?></th>
                    <th width="16%"><?php _e('Transaction ID', 'wp-ticket-ultra'); ?></th>
                    
                     <th width="9%"><?php _e('Method', 'wp-ticket-ultra'); ?></th>
                     <th width="9%"><?php _e('Status', 'wp-ticket-ultra'); ?></th>
                    <th width="9%"><?php _e('Amount', 'wp-ticket-ultra'); ?></th>
                </tr>
            </thead>
            
            <tbody>
            
            <?php 
			foreach($orders as $order) {
				
				$client_id = $order->booking_user_id;				
				$client = get_user_by( 'id', $client_id );
					
			?>
              

                <tr>
                    <td><?php echo $order->order_id; ?></td>
                    <td><?php echo  $order->booking_id; ?></td>
                     <td><?php echo  date("m/d/Y", strtotime($order->order_date)); ?></td>
                    <td><?php echo $client->display_name; ?> (<?php echo $client->user_login; ?>)</td>
                    <td><?php echo $order->service_title; ?> </td>
                    <td><?php echo $order->order_txt_id; ?></td>
                     
                      <td><?php echo $order->order_method_name; ?></td>
                      <td><?php echo $order->order_status; ?></td>
                   <td> <?php echo $currency_symbol.$order->order_amount; ?></td>
                </tr>
                
                
                <?php
					}
					
					} else {
			?>
			<p><?php _e('There are no transactions yet.','wp-ticket-ultra'); ?></p>
			<?php	} ?>

            </tbody>
        </table>
        
        
        </div>
        
