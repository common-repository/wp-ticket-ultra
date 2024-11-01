<?php
global $wpticketultra , $wptultimate, $wptucomplement, $wptu_bugtracker, $wptu_wooco, $wptu_staffbackend, $wptu_guest_ticket;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$current_user = $wpticketultra->userpanel->get_user_info();
$user_id = $current_user->ID;			 
$is_client  = $wpticketultra->profile->is_client($user_id);

//check if guest tickt allowed

if(!is_user_logged_in())
{
	//Do we allow guest users
	
	if(!isset($wptu_guest_ticket))
	{
	
		echo '<div class="wptu-ultra-warning"><span><i class="fa fa-check"></i>'.__("Please login to your account to create a new ticket.",'wp-ticket-ultra').'</span></div>';
		
		return;	
	
	}
	
			 
}


if(!$is_client && !$wptu_staffbackend)
{
	
	echo '<div class="wptu-ultra-warning"><span><i class="fa fa-check"></i>'.__("Oops! You don't have permission to access to this section",'wp-ticket-ultra').'</span></div>';
	
	return;		
}

$alignment = '';

if(isset($display_alignment) && $display_alignment=='left' ) 
{
	$alignment = 'style=" margin-left:0px; margin-right:0px"';

}

//check amount of open and pending tickets:
if($is_client && isset($wptucomplement))
{
	$wptucomplement->check_max_allowed_tickets($user_id);	
}

?>

<div class="wptu-front-cont" <?php echo $alignment;?>>


<?php echo $wpticketultra->get_registration_form($atts);?>    
    

</div>

<div id="wptu-client-new-box" title="<?php _e('Create New Client','wpticku')?>"></div>


