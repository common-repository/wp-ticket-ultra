<?php
global $wpticketultra, $wptu_staff_profile;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<form method="post" action="">
<input type="hidden" name="update_settings" />

<div class="wptu-ultra-sect ">

 <h3><?php _e('Ticket Ultra Pro Staff Profile Pages','wp-ticket-ultra'); ?></h3>
        
              <p><?php _e('Here you can set your custom pages for the staff profiles.','wp-ticket-ultra'); ?></p>
        
  <table class="form-table">
<?php 



	$wpticketultra->admin->create_plugin_setting(
            'select',
            'wptu_registration_page',
            __('Registration Page','wp-ticket-ultra'),
            $wpticketultra->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[wptu_user_signup]</code> shortcode on this page.','wp-ticket-ultra'),
            __('This page is where users will be able to sign up to your website.','wp-ticket-ultra')
    );

	
	$wpticketultra->admin->create_plugin_setting(
            'select',
            'bup_my_account_page',
            __('My Account Page','wp-ticket-ultra'),
            $wpticketultra->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[wptu_account]</code> shortcode on this page.','wp-ticket-ultra'),
            __('This page is where users and staff members will be able to manage their appointments.','wp-ticket-ultra')
    );
	
	$wpticketultra->admin->create_plugin_setting(
            'select',
            'bup_user_login_page',
            __('Users Login Page','wp-ticket-ultra'),
            $wpticketultra->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[wptu_user_login]</code> shortcode on this page.','wp-ticket-ultra'),
            __('This page is where users and staff members & clients will be able to recover to login to their accounts.','wp-ticket-ultra')
    );
	
	
		$wpticketultra->admin->create_plugin_setting(
            'select',
            'bup_password_reset_page',
            __('Password Recover Page','wp-ticket-ultra'),
            $wpticketultra->admin->get_all_sytem_pages(),
            __('Make sure you have the <code>[wptu_user_recover_password]</code> shortcode on this page.','wp-ticket-ultra'),
            __('This page is where users and staff members will be able to recover their passwords.','wp-ticket-ultra')
    );
	
	
			
	$wpticketultra->admin->create_plugin_setting(
	'select',
	'hide_admin_bar',
	__('Hide WP Admin Tool Bar?','wp-ticket-ultra'),
	array(
		0 => __('NO','wp-ticket-ultra'), 		
		1 => __('YES','wp-ticket-ultra')),
		
	__('If checked, User will not see the WP Admin Tool Bar','wp-ticket-ultra'),
  __('If checked, User will not see the WP Admin Tool Bar','wp-ticket-ultra')
       );
	   
	   $wpticketultra->admin->create_plugin_setting(
	'select',
	'hide_site_column',
	__('Hide Product Column On User Account?','wp-ticket-ultra'),
	array(
		1 => __('YES','wp-ticket-ultra'), 		
		0 => __('NO','wp-ticket-ultra')),
		
	__("If YES is selected, the client will not see the Product Column in the tickets list. This is useful if you're offering support for just one product. However, if you're offering support for multiple product this colum will help clients to organize their requests ",'wp-ticket-ultra'),
  __("If YES is selected, the client will not see the Product Column in the tickets list. This is useful if you're offering support for just one product. However, if you're offering support for multiple product this colum will help clients to organize their requests ",'wp-ticket-ultra')
       );
	   
	
	   
		
?>
</table>      
   

             

</div>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
	
</p>

</form>

