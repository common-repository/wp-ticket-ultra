<?php
global $bookingultrapro, $bupcomplement;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
?>


        
       <div class="wptu-welcome-panel">
       
       <div class="wptu-upgrade-pro-btn">
      <a href="https://wpticketsultra.com/compare-packages.php" target="_blank"> <button class="wptu-button-upgrade">Compare Versions	</button> </a>
       
       </div>

<div class="welcome-panel-content">
	<span class="wptulogoadmin"><img src="<?php echo wptu_url?>admin/images/logo-welcome.png" width="250" height="150" /></span>
<h3 class="wptu-extended">WP Tickets Ultra</h3>
    <p class="wptu-extended-p">Thank you very much for checking this page.</p> 
    
    
  
     <br />
    
    <?php 
	
	if (! get_option('wptu_ini_setup')) 
	{
		//display message for ini setup
		
		$this->initial_setup();
		
		
		
	?>
    
     <h3>Welcome!</h3>
    
    <p class="bup-extended-p">This is your first time using this plugin. The following links may help you out.</p>
    
    
	<?php }else{ ?>
    
    <?php } ?>
    

     
     <br />
     
         <h3>Empower Your Help Desk System </h3>

	
	<div class="welcome-panel-column-container">
    
    
        <p class="wptu-extended-p">The <a href="https://wpticketsultra.com/compare-packages.php" target="_blank">Premium Versions</a> come with useful modules such as WooCommerce, canned responses, sensitive data encryption, <br /> ticket notes, staff backend and much more..</p>
          
    <p class="wptu-extended-p">The following Add-ons are avaialable on the Premium Versionss.</p>   
    
    
    <div class="welcome-panel-column-pro">
    
   <a href="https://wpticketsultra.com/extensions/woocommerce/" target="_blank"> <img src="<?php echo wptu_url?>/admin/images/iconos-panel/woo-commerce-add-ons.png" width="450" height="155" /></a>
<h4> <a href="https://wpticketsultra.com/extensions/woocommerce/" target="_blank">WooCommerce</a></h4>
            
            <p><?php _e("This add-on helps you to improve your client's satisfaction. It Allows your clients to open support tickets with just one click from their Orders dashboard. If you're selling by using WooCommerce, offering support to your clients after the purchase is very important.",'wp-ticket-ultra'); ?></p>             
			
	</div>
    
     <div class="welcome-panel-column-pro">
    
   <a href="https://wpticketsultra.com/extensions/private-credentials/" target="_blank"> <img src="<?php echo wptu_url?>/admin/images/iconos-panel/private-credential-add-ons.png" width="450" height="155" /></a>
<h4><a href="https://wpticketsultra.com/extensions/private-credentials/" target="_blank"> <?php _e("Private Credentials",'wp-ticket-ultra'); ?></a></h4>
            
            <p><?php _e("The Private Credentials Add-on allows your clients to provide critical information with confidence. All sensitive information is encrypted in a secure database. This means that even if your database hacked, hackers won't be able to obtain the client's information.",'wp-ticket-ultra'); ?></p>
            
	</div>
    
     <div class="welcome-panel-column-pro">
    
   <a href="https://wpticketsultra.com/extensions/canned-responses/" target="_blank">  <img src="<?php echo wptu_url?>/admin/images/iconos-panel/canned-reponses-add-ons.png" width="450" height="155" /></a>
<h4> <a href="https://wpticketsultra.com/extensions/canned-responses/" target="_blank"><?php _e("Canned Responses",'wp-ticket-ultra'); ?></a></h4>
            
            <p><?php _e("Stop writing the same replies over and over again. This tiny add-on saves you a lot of time. It's perfect for decreasing time spent creating replies and optimizes the time you or your staff spend on replying to tickets.",'wp-ticket-ultra'); ?></p>
                    
			
	</div>
    
   
    
    <div class="welcome-panel-column-pro ">
    <a href="https://wpticketsultra.com/extensions/ticket-notes/" target="_blank">   <img src="<?php echo wptu_url?>/admin/images/iconos-panel/notes-add-ons.png" width="450" height="155" /></a>
		<h4><a href="https://wpticketsultra.com/extensions/ticket-notes/" target="_blank"> <?php _e("Private Notes",'wp-ticket-ultra'); ?></a></h4>
        
          <p><?php _e("Adding either privates or public notes to a ticket is very easy.  This add-on allows users to attach public notes to a ticket and staff members and admin to attach public and private notes.",'wp-ticket-ultra'); ?></p>  
               
        
	</div> 
    
    
     <div class="welcome-panel-column-pro ">
     <a href="https://wpticketsultra.com/extensions/staff-backend/" target="_blank"><img src="<?php echo wptu_url?>/admin/images/iconos-panel/staff-add-ons.png" width="450" height="155" /></a>
		<h4><a href="https://wpticketsultra.com/extensions/staff-backend/" target="_blank"> <?php _e("Staff Backend",'wp-ticket-ultra'); ?></a></h4>
        
         <p><?php _e("By installing either Team, Professional, or Enterprise versions, your staff members will be able to login to their accounts and manage tickets opened by your clients.",'wp-ticket-ultra'); ?></p> 
         
        
	</div> 
    
    <div class="welcome-panel-column-pro ">
    <a href="https://wpticketsultra.com/extensions/activity-tracker/" target="_blank"><img src="<?php echo wptu_url?>/admin/images/iconos-panel/activity-log-add-ons.png" width="450" height="155" /></a>
		<h4><a href="https://wpticketsultra.com/extensions/activity-tracker/" target="_blank"> <?php _e("Activity Log",'wp-ticket-ultra'); ?></a></h4>
        
         <p><?php _e("At some point you will need to check what's happening between staff members and clients in real time. For that reason we have created this little but useful add-on that lets you keep a log of all the main actions when creating or replying tickets.",'wp-ticket-ultra'); ?></p> 
               
	</div>    
   
   
	</div>
	</div>
    
</div>       
                                          
