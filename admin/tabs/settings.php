<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpticketultra, $wptucomplement, $wptu_aweber, $wptu_mailchimp, $wptu_recaptcha;
?>
<h3><?php _e('Plugin Settings','wp-ticket-ultra'); ?></h3>
<form method="post" action="">
<input type="hidden" name="wptu_update_settings" />


<div id="tabs-bupro-settings" class="wptu-multi-tab-options">

<ul class="nav-tab-wrapper bup-nav-pro-features">

<li class="nav-tab bup-pro-li"><a href="#tabs-1" title="<?php _e('General','wp-ticket-ultra'); ?>"><?php _e('General','wp-ticket-ultra'); ?></a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-messaging" title="<?php _e('Advanced Messaging Settings','wp-ticket-ultra'); ?>"><?php _e('Advanced Messaging Settings','wp-ticket-ultra'); ?></a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-notifications" title="<?php _e('Advanced Announcements','wp-ticket-ultra'); ?>"><?php _e('Announcements','wp-ticket-ultra'); ?></a></li>


<li class="nav-tab bup-pro-li"><a href="#tabs-bup-newsletter" title="<?php _e('Newsletter','wp-ticket-ultra'); ?>"><?php _e('Newsletter','wp-ticket-ultra'); ?> </a></li>

<li class="nav-tab bup-pro-li"><a href="#tabs-wptu-recaptcha" title="<?php _e('reCaptcha','wp-ticket-ultra'); ?>"><?php _e('reCaptcha','wp-ticket-ultra'); ?> </a></li>



</ul>



<div id="tabs-wptu-recaptcha">


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('reCaptcha','wp-ticket-ultra'); ?></h3>
  
  <?php if(!isset($wptu_recaptcha)){
	  
	  $html = '<div class="wptu-ultra-warning">'. __("Please make sure that WP Ticket Ultra reCaptcha (Add-on) plugin is active.", 'wp-ticket-ultra').'</div>';
	  
	  echo $html ;
	  ?>
  
  
  
  <?php }?>
  
    
  <p><?php _e('This is a free add-on which was developed to help you to protect your ticket system against spammers.','wp-ticket-ultra'); ?></p>
  
    <p><?php _e("You can get the Site Key and Secret Key on Google reCaptcha Dashboard",'wp-ticket-ultra'); ?>. <a href="https://www.google.com/recaptcha/admin" target="_blank"> <?php _e("Click here",'wp-ticket-ultra'); ?> </a> </p>
    
    <p><?php _e("You may check the reCaptcha setup tutorial as well. ",'wp-ticket-ultra'); ?> <a href="http://docs.wpticketultra.com/installing-recaptcha/" target="_blank"> <?php _e("Click here",'wp-ticket-ultra'); ?> </a> </p>
    
    <p><?php _e("Download the reCaptcha plugin . ",'wp-ticket-ultra'); ?> <a href="https://wordpress.org/plugins/wp-ticket-ultra-recaptcha/" target="_blank"> <?php _e("here",'wp-ticket-ultra'); ?> </a> </p>
  
  
  
  <table class="form-table">
<?php


	$this->create_plugin_setting(
			'input',
			'recaptcha_site_key',
			__('Site Key:','wp-ticket-ultra'),array(),
			__('Enter your site key here.','wp-ticket-ultra'),
			__('Enter your site key here.','wp-ticket-ultra')
	);
	
	$this->create_plugin_setting(
			'input',
			'recaptcha_secret_key',
			__('Secret Key:','wp-ticket-ultra'),array(),
			__('Enter your site secret here.','wp-ticket-ultra'),
			__('Enter your site secret here.','wp-ticket-ultra')
	);

	
?>
</table>
</div>


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Where to display?','wp-ticket-ultra'); ?></h3>
  
    
  <p><?php _e('Select what forms will be protected by reCaptcha','wp-ticket-ultra'); ?></p>
  
  <table class="form-table">
<?php


	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_registration',
                __('Registration Form','wp-ticket-ultra'),
                '1',
                __('If checked, the reCaptcha will be displayed in the registration form.','wp-ticket-ultra'),
                __('If checked, the reCaptcha will be displayed in the registration form.','wp-ticket-ultra')
        );
		
	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_loginform',
                __('Login Form','wp-ticket-ultra'),
                '1',
                __('If checked, the reCaptcha will be displayed in the login form.','wp-ticket-ultra'),
                __('If checked, the reCaptcha will be displayed in the login form.','wp-ticket-ultra')
        );
		
	
	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_forgot_password',
                __('Forgot Password Form','wp-ticket-ultra'),
                '1',
                __('If checked, the reCaptcha will be displayed in the forgot password form.','wp-ticket-ultra'),
                __('If checked, the reCaptcha will be displayed in the forgot password form.','wp-ticket-ultra')
        ); 
		
	
	$this->create_plugin_setting(
                'checkbox',
                'recaptcha_display_ticketform',
                __('Public Ticket Form','wp-ticket-ultra'),
                '1',
                __('If checked, the reCaptcha will be displayed in the ticket form.','wp-ticket-ultra'),
                __('If checked, the reCaptcha will be displayed in the ticket form.','wp-ticket-ultra')
        ); 
	
?>
</table>
</div>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
</p>

  
</div>


<div id="tabs-1">


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Premium  Settings','wp-ticket-ultra'); ?></h3>
  
    <?php if(isset($wptucomplement))
{?>

  <p><?php _e('This section includes some advanced settings that help you to offer a faster and optimized support to your clients.','wp-ticket-ultra'); ?></p>
  
  <table class="form-table">
<?php


	$this->create_plugin_setting(
	'select',
	'allow_to_close_ticket_by_user',
	__("Allow Users To Close or Resolve a Ticket?",'wp-ticket-ultra'),
	array(
		'YES' => __('YES','wp-ticket-ultra'), 		
		'NO' => __('NO','wp-ticket-ultra')),
		
	__("This setting allows clients to close a ticket.",'wp-ticket-ultra'),
  __("This setting allows clients to close a ticket.",'wp-ticket-ultra')
       );
	   
	  
	  $this->create_plugin_setting(
	'select',
	'allow_to_delete_reply',
	__("Allow Staff to delete ticket replies?",'wp-ticket-ultra'),
	array(
		'YES' => __('YES','wp-ticket-ultra'), 		
		'NO' => __('NO','wp-ticket-ultra')),
		
	__("This allows staff members to delete ticket replies. By the default the staff members can delete a reply within the ticket.",'wp-ticket-ultra'),
  __("This setting allows clients to close a ticket.",'wp-ticket-ultra')
       );


	$this->create_plugin_setting(
	'select',
	'limit_max_open_tickets',
	__("Limit Open Tickets By Users?",'wp-ticket-ultra'),
	array(
		'NO' => __('NO','wp-ticket-ultra'), 		
		'YES' => __('YES','wp-ticket-ultra')),
		
	__("This feature will control how many opened tickets a user might have. This is useful to organize your team. Some users use to open many tickets the same day, many of those tickets might be duplicated.",'wp-ticket-ultra'),
  __("This feature will control how many opened tickets a user might have. This is useful to organize your team. Some users use to open many tickets the same day, many of those tickets might be duplicated.",'wp-ticket-ultra')
       );
	   
	   	$this->create_plugin_setting(
	'select',
	'how_many_max_open_tickets',
	__("How many open tickets?",'wp-ticket-ultra'),
	array(
		1 => 1, 		
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => 7,
		8 => 8,
		9 => 9,
		10 => 10),
		
	__("Set the max amount of open tickets a user can have.",'wp-ticket-ultra'),
  __("Set the max amount of open tickets a user can have.",'wp-ticket-ultra')
       );
	   
	  
   
		
	
?>
</table>

<?php }else{?>

<p><?php _e('These settings are included in the premium version of WP Ticket Ulra. If you find the plugin useful for your business please consider buying a licence for the full version.','wp-ticket-ultra'); ?>. Click <a href="https://wpticketultra.com/compare-packages.php">here</a> to upgrade </p>

<strong>The following settings are included in Premium Version</strong>
<p>- Limit how many open tickets a user may have. </p>

<?php }?> 

  
</div>


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Miscellaneous  Settings','wp-ticket-ultra'); ?></h3>
  
  <p><?php _e('.','wp-ticket-ultra'); ?></p>
  
  
  <p style="text-align:right" class="wptu-timestamp-features"> <?php _e('Site Time: ','wp-ticket-ultra')?><?php echo date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) )?> (Offset: <?php echo get_option('gmt_offset');?>) | <?php _e('GMT: ','wp-ticket-ultra')?>  <?php echo date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) )?></p>
  
  
  <table class="form-table">
<?php 


$this->create_plugin_setting(
        'input',
        'company_name',
        __('Company Name:','wp-ticket-ultra'),array(),
        __('Enter your company name here.','wp-ticket-ultra'),
        __('Enter your company name here.','wp-ticket-ultra')
);

$this->create_plugin_setting(
        'input',
        'company_phone',
        __('Company Phone Nunber:','wp-ticket-ultra'),array(),
        __('Enter your company phone number here.','wp-ticket-ultra'),
        __('Enter your company phone number here.','wp-ticket-ultra')
);

$this->create_plugin_setting(
        'input',
        'allowed_extensions',
        __('Allowed Extensions:','wp-ticket-ultra'),array(),
        __('Enter the allowed extensions separated by commas. Example:  jpg,png,gif,jpeg,pdf,doc,docx,xls','wp-ticket-ultra'),
        __('Enter the allowed extensions separated by commas. Example: jpg,png,gif,jpeg,pdf,doc,docx,xls','wp-ticket-ultra')
);

	   

$this->create_plugin_setting(
        'textarea',
        'gateway_free_success_message',
        __('Custom Message for Ticket Submit','wp-ticket-ultra'),array(),
        __('Input here a custom message that will be displayed to the client once the ticket has been submited at the front page.','wp-ticket-ultra'),
        __('Input here a custom message that will be displayed to the client once the ticket has been submited at the front page.','wp-ticket-ultra')
);

 $data = array(
		 				'm/d/Y' => date('m/d/Y'),
                        'm/d/y' => date('m/d/y'),
                        'Y/m/d' => date('Y/m/d'),
                        'dd/mm/yy' => date('d/m/Y'),
                        'Y-m-d' => date('Y-m-d'),
                        'd-m-Y' => date('d-m-Y'),
                        'm-d-Y' => date('m-d-Y'),
                        'F j, Y' => date('F j, Y'),
                        'j M, y' => date('j M, y'),
                        'j F, y' => date('j F, y'),
                        'l, j F, Y' => date('l, j F, Y')
                    );
					
		 $data_time = array(
		 				'5' => 5,
                        '10' =>10,
                        '12' => 12,
                        '15' => 15,
                        '20' => 20,
                        '30' =>30,                       
                        '60' =>60
                       
                    );
		
		$data_time_format = array(
		 				
                        'H:i' => date('H:i'),
                        'h:i A' => date('h:i A')
                    );
		 $days_availability = array(
		 				'7' => 7,
                        '10' =>10,
                        '15' => 15,
                        '20' => 20,
                        '25' => 25,
                        '30' =>30,                       
                        '35' =>35,
						'40' =>40,
                       
                    );
   
		
		$this->create_plugin_setting(
            'select',
            'bup_date_format',
            __('Date Format:','wp-ticket-ultra'),
            $data,
            __('Select the date format to be used','wp-ticket-ultra'),
            __('Select the date format to be used','wp-ticket-ultra')
    );
	
	$this->create_plugin_setting(
            'select',
            'bup_time_format',
            __('Display Time Format:','wp-ticket-ultra'),
            $data_time_format,
            __('Select the time format to be used','wp-ticket-ultra'),
            __('Select the time format to be used','wp-ticket-ultra')
    );
	
	
	
	
	$this->create_plugin_setting(
	'select',
	'bup_override_avatar',
	__('Use WP Ticket Ultra Avatar','xoousers'),
	array(
		'no' => __('No','xoousers'), 
		'yes' => __('Yes','xoousers'),
		),
		
	__('If you select "yes", WP Ticket Ultra will override the default WordPress Avatar','wp-ticket-ultra'),
  __('If you select "yes",  WP Ticket Ultra will override the default WordPress Avatar','wp-ticket-ultra')
       );
	
	$this->create_plugin_setting(
	'select',
	'avatar_rotation_fixer',
	__('Auto Rotation Fixer','xoousers'),
	array(
		'no' => __('No','xoousers'), 
		'yes' => __('Yes','xoousers'),
		),
		
	__("If you select 'yes',  WP Ticket Ultra will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'wp-ticket-ultra'),
  __("If you select 'yes',  WP Ticket Ultra will Automatically fix the rotation of JPEG images using PHP's EXIF extension, immediately after they are uploaded to the server. This is implemented for iPhone rotation issues",'wp-ticket-ultra')
       );
	   
	   $this->create_plugin_setting(
        'input',
        'media_avatar_width',
        __('Avatar Width:','wp-ticket-ultra'),array(),
        __('Width in pixels','wp-ticket-ultra'),
        __('Width in pixels','wp-ticket-ultra')
);

$this->create_plugin_setting(
        'input',
        'media_avatar_height',
        __('Avatar Height','wp-ticket-ultra'),array(),
        __('Height in pixels','wp-ticket-ultra'),
        __('Height in pixels','wp-ticket-ultra')
);
	
	
	
	 								
	
	  
		
?>
</table>



 <h3><?php _e('Ticket Settings','wp-ticket-ultra'); ?></h3>
  
  <p><?php _e('.','wp-ticket-ultra'); ?></p>
  
    <table class="form-table">
<?php 


$this->create_plugin_setting(
        'input',
        'ticket_sub_help_texte',
        __('Custom help text for email field on new ticket form:','wp-ticket-ultra'),array(),
        __('Enter your custom text that will be displyed under the email field on the submit new tiket form.','wp-ticket-ultra'),
        __('Enter your custom text that will be displyed under the email field on the submit new tiket form.','wp-ticket-ultra')
);

?>
</table>

</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
</p>




</div>


<div id="tabs-notifications">

<div class="wptu-sect  wptu-welcome-panel">
   
   
   <h3><?php _e('Notifications','wp-ticket-ultra'); ?></h3>  
   <p><?php _e('These settings allow you to display different messages on the staff & client account. This is useful to display messages on weekends, holidays etc.','wp-ticket-ultra'); ?></p>
   
   <?php if(isset($wptucomplement)){?>
 
 
   <table class="form-table">
 <?php
 
 
 $this->create_plugin_setting(
	'select',
	'advanced_noti_on_weekend',
	__('Display Message At Private Panel On Weekends?:','wp-ticket-ultra'),
	array(
		'yes' => __('YES','wp-ticket-ultra'),
		'no' => __('NO','wp-ticket-ultra') 
		),
		
	__("This allows you to dispaly a message within the client's backend on weekends. ",'wp-ticket-ultra'),
  __("This allows you to dispaly a message within the client's backend on weekends.",'wp-ticket-ultra')
       );
	   
	   $this->create_plugin_setting(
        'textarea',
        'advanced_noti_on_weekend_backend_message',
        __('Custom Message On Weekend','wp-ticket-ultra'),array(),
        __('Input here a custom message that will be displayed to the client once the ticket has been submited at the front page.','wp-ticket-ultra'),
        __('Input here a custom message that will be displayed to the client once the ticket has been submited at the front page.','wp-ticket-ultra')
);

	   
$this->create_plugin_setting(
	'select',
	'advanced_noti_on_weekend_front',
	__('Display Message At Public Side On Weekends?:','wp-ticket-ultra'),
	array(
		'yes' => __('YES','wp-ticket-ultra'),
		'no' => __('NO','wp-ticket-ultra') 
		),
		
	__("This allows you to dispaly a message on the front-end when it's weekend. ",'wp-ticket-ultra'),
  __("This allows you to dispaly a message on the front-end when it's weekend. ",'wp-ticket-ultra')
       );
	   
	   $this->create_plugin_setting(
        'textarea',
        'advanced_noti_on_weekend_front_message',
        __('Custom Message On Weekend','wp-ticket-ultra'),array(),
        __('Input here a custom message that will be displayed to the client once the ticket has been submited at the front page.','wp-ticket-ultra'),
        __('Input here a custom message that will be displayed to the client once the ticket has been submited at the front page.','wp-ticket-ultra')
);
	   

?>
 
 </table>
 
<?php }else{?>

 <p><?php _e("This feature is available on Premium versions only. Please consider upgrading your plugin.",'wp-ticket-ultra'); ?><a href="https://wpticketultra.com/compare-packages.html" target="_blank"><?php _e(" Click here",'wp-ticket-ultra'); ?></a> <?php _e(" to upgrade.",'wp-ticket-ultra'); ?></p>

<?php }?>
 
   
 

  
</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
</p>


</div>

<div id="tabs-messaging">

<div class="wptu-sect  wptu-welcome-panel">
   
   
   <h3><?php _e('General Rules','wp-ticket-ultra'); ?></h3>  
   <p><?php _e('These settings allow you to define rules on how the staff members and admin are notified when a ticket is created.','wp-ticket-ultra'); ?></p>
 
 
   <table class="form-table">
 <?php
 
 
 $this->create_plugin_setting(
	'select',
	'noti_admin',
	__('Send Email Notifications to Admin?:','wp-ticket-ultra'),
	array(
		'yes' => __('YES','wp-ticket-ultra'),
		'no' => __('NO','wp-ticket-ultra') 
		),
		
	__('This allows you to block email notifications that are sent to the admin.','wp-ticket-ultra'),
  __('This allows you to block email notifications that are sent to the admin.','wp-ticket-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'noti_staff',
	__('Send Email Notifications to Staff Members?:','wp-ticket-ultra'),
	array(
		'yes' => __('YES','wp-ticket-ultra'),
		'no' => __('NO','wp-ticket-ultra') 
		),
		
	__('This allows you to block email notifications that are sent to the staff members.','wp-ticket-ultra'),
  __('This allows you to block email notifications that are sent to the staff members.','wp-ticket-ultra')
       );
	   

$this->create_plugin_setting(
	'select',
	'noti_client',
	__('Send Email Notifications to Clients?:','wp-ticket-ultra'),
	array(
		'yes' => __('YES','wp-ticket-ultra'),
		'no' => __('NO','wp-ticket-ultra') 
		),
		
	__('This allows you to block email notifications that are sent to the clients.','wp-ticket-ultra'),
  __('This allows you to block email notifications that are sent to the clients.','wp-ticket-ultra')
       );
  

?>
 
 </table>
 
   <h3><?php _e('Advanced Rules','wp-ticket-ultra'); ?></h3>  
   <p><?php _e('These settings allow you to define rules on how the staff members and admin are notified when a ticket is created.','wp-ticket-ultra'); ?></p>
   
  
  <table class="form-table">
 <?php
$this->create_plugin_setting(
	'select',
	'notification_rules_new_ticket',
	__('New Ticket Notification Rule:','wp-ticket-ultra'),
	array(
		'1' => __("Notify all staff members within the department",'wp-ticket-ultra'),
		'2' => __('Notify only website admin','wp-ticket-ultra'), 
		'3' => __("Don't send notifications (Only the client receives a confirmation email)",'wp-ticket-ultra'),
		
		),
		
	__('Specify the authentication method.','wp-ticket-ultra'),
  __('Specify the authentication method.','wp-ticket-ultra')
       );
	   
	   $this->create_plugin_setting(
	'select',
	'notification_rules_new_reply',
	__('New Replies Notification Rule:','wp-ticket-ultra'),
	array(
		'1' => __("Notify all staff members within the department",'wp-ticket-ultra'),
		'2' => __('Notify only website admin','wp-ticket-ultra'),
		'3' => __('Notify only the topic owner','wp-ticket-ultra'), 
		'4' => __("Don't send notifications",'wp-ticket-ultra'),
		
		),
		
	__('Specify the authentication method.','wp-ticket-ultra'),
  __('Specify the authentication method.','wp-ticket-ultra')
       );

?>
 
 </table>
 
   
 
 

  
</div>


<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
</p>


</div>



<div id="tabs-bup-newsletter">
  
  <?php if(isset($wptu_aweber) || isset($wptu_mailchimp))
{?>


<div class="wptu-sect wptu-welcome-panel ">
<h3><?php _e('Newsletter Preferences','wp-ticket-ultra'); ?></h3>
  
  <p><?php _e('Here you can activate your preferred newsletter tool.','wp-ticket-ultra'); ?></p>

<table class="form-table">
<?php 
   
$this->create_plugin_setting(
	'select',
	'newsletter_active',
	__('Activate Newsletter','wp-ticket-ultra'),
	array(
		'no' => __('No','wp-ticket-ultra'), 
		'aweber' => __('AWeber','wp-ticket-ultra'),
		'mailchimp' => __('MailChimp','wp-ticket-ultra'),
		),
		
	__('Just set "NO" to deactivate the newsletter tool.','wp-ticket-ultra'),
  __('Just set "NO" to deactivate the newsletter tool.','wp-ticket-ultra')
       );

	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
</p>


</div>


<?php }else{?>


<div class="wptu-sect  wptu-welcome-panel">

<p><?php _e('This function is available only on certain versions.','wp-ticket-ultra'); ?>. Click <a href="https://wpticketultra.com/compare-packages.php">here</a> to compare packages </p>


</div>

<?php }?> 
  <?php if(isset($wptu_aweber))
{?>


<div class="wptu-sect wptu-welcome-panel ">
<h3><?php _e('Aweber Settings','wp-ticket-ultra'); ?></h3>
  
  <p><?php _e('This module gives you the capability to subscribe your clients automatically to any of your Aweber List when they submit a ticket.','wp-ticket-ultra'); ?></p>
  
  
<table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'aweber_app_id',
        __('APP ID','wp-ticket-ultra'),array(),
        __('Fill out this field with your AWeber APP ID.','wp-ticket-ultra'),
        __('Fill out this field with your AWeber APP ID.','wp-ticket-ultra')
);

$this->create_plugin_setting(
        'input',
        'aweber_consumer_key',
        __('Consumer Key','wp-ticket-ultra'),array(),
        __('Fill out this field your AWeber Consumer Key.','wp-ticket-ultra'),
        __('Fill out this field your AWeber Consumer Key.','wp-ticket-ultra')
);

$this->create_plugin_setting(
        'input',
        'aweber_consumer_secret',
        __('Consumer Secret','wp-ticket-ultra'),array(),
        __('Fill out this field your AWeber Consumer Secret.','wp-ticket-ultra'),
        __('Fill out this field your AWeber Consumer Secret.','wp-ticket-ultra')
);




$this->create_plugin_setting(
                'checkbox',
                'aweber_auto_text',
                __('Auto Checked Aweber','wp-ticket-ultra'),
                '1',
                __('If checked, the user will not need to click on the AWeber checkbox. It will appear checked already.','wp-ticket-ultra'),
                __('If checked, the user will not need to click on the AWeber checkbox. It will appear checked already.','wp-ticket-ultra')
        );
$this->create_plugin_setting(
        'input',
        'aweber_text',
        __('Aweber Text','wp-ticket-ultra'),array(),
        __('Please input the text that will appear when asking users to get periodical updates.','wp-ticket-ultra'),
        __('Please input the text that will appear when asking users to get periodical updates.','wp-ticket-ultra')
);

	$this->create_plugin_setting(
        'input',
        'aweber_header_text',
        __('Aweber Header Text','wp-ticket-ultra'),array(),
        __('Please input the text that will appear as header when AWeber is active.','wp-ticket-ultra'),
        __('Please input the text that will appear as header when AWeber is active.','wp-ticket-ultra')
);
	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
</p>


</div>

<?php }?> 


  <?php if(isset($wptu_mailchimp))
{?>


<div class="wptu-sect wptu-welcome-panel ">
<h3><?php _e('MailChimp Settings','wp-ticket-ultra'); ?></h3>
  
  <p><?php _e('.','wp-ticket-ultra'); ?></p>
  
  
<table class="form-table">
<?php 
   
		
$this->create_plugin_setting(
        'input',
        'mailchimp_api',
        __('MailChimp API Key','wp-ticket-ultra'),array(),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','wp-ticket-ultra'),
        __('Fill out this field with your MailChimp API key here to allow integration with MailChimp subscription.','wp-ticket-ultra')
);

$this->create_plugin_setting(
        'input',
        'mailchimp_list_id',
        __('MailChimp List ID','wp-ticket-ultra'),array(),
        __('Fill out this field your list ID.','wp-ticket-ultra'),
        __('Fill out this field your list ID.','wp-ticket-ultra')
);



$this->create_plugin_setting(
                'checkbox',
                'mailchimp_auto_checked',
                __('Auto Checked MailChimp','wp-ticket-ultra'),
                '1',
                __('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','wp-ticket-ultra'),
                __('If checked, the user will not need to click on the mailchip checkbox. It will appear checked already.','wp-ticket-ultra')
        );
$this->create_plugin_setting(
        'input',
        'mailchimp_text',
        __('MailChimp Text','wp-ticket-ultra'),array(),
        __('Please input the text that will appear when asking users to get periodical updates.','wp-ticket-ultra'),
        __('Please input the text that will appear when asking users to get periodical updates.','wp-ticket-ultra')
);

	$this->create_plugin_setting(
        'input',
        'mailchimp_header_text',
        __('MailChimp Header Text','wp-ticket-ultra'),array(),
        __('Please input the text that will appear as header when mailchip is active.','wp-ticket-ultra'),
        __('Please input the text that will appear as header when mailchip is active.','wp-ticket-ultra')
);
	
?>
</table>

<p class="submit">
	<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />
</p>


</div>



<?php }?>  
  
  


</div>



</div>




</form>