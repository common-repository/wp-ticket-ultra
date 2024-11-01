<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpticketultra,   $wptucomplement;

?>
<h3><?php _e('Advanced Email Options','wp-ticket-ultra'); ?></h3>
<form method="post" action="" id="b_frm_settings" name="b_frm_settings">
<input type="hidden" name="wptu_update_settings" />
<input type="hidden" name="wptu_reset_email_template" id="wptu_reset_email_template" />
<input type="hidden" name="wptu_email_template" id="wptu_email_template" />


  <p><?php _e('Here you can control how WP Ticket Ultra will send the notification to your users.','wp-ticket-ultra'); ?></p>


<div class="wptu-sect  wptu-welcome-panel">  
   <table class="form-table">
<?php 
 

$this->create_plugin_setting(
        'input',
        'messaging_send_from_name',
        __('Send From Name','wp-ticket-ultra'),array(),
        __('Enter the your name or company name here.','wp-ticket-ultra'),
        __('Enter the your name or company name here.','wp-ticket-ultra')
);

$this->create_plugin_setting(
        'input',
        'messaging_send_from_email',
        __('Send From Email','wp-ticket-ultra'),array(),
        __('Enter the email address to be used when sending emails.','wp-ticket-ultra'),
        __('Enter the email address to be used when sending emails.','wp-ticket-ultra')
);

$this->create_plugin_setting(
	'select',
	'bup_smtp_mailing_mailer',
	__('Mailer:','wp-ticket-ultra'),
	array(
		'mail' => __('Use the PHP mail() function to send emails','wp-ticket-ultra'),
		'smtp' => __('Send all emails via SMTP','wp-ticket-ultra'), 
		'mandrill' => __('Send all emails via Mandrill','wp-ticket-ultra'),
		'third-party' => __('Send all emails via Third-party plugin','wp-ticket-ultra'), 
		
		),
		
	__('Specify which mailer method the pluigin should use when sending emails.','wp-ticket-ultra'),
  __('Specify which mailer method the pluigin should use when sending emails.','wp-ticket-ultra')
       );
	   
$this->create_plugin_setting(
                'checkbox',
                'bup_smtp_mailing_return_path',
                __('Return Path','wp-ticket-ultra'),
                '1',
                __('Set the return-path to match the From Email','wp-ticket-ultra'),
                __('Set the return-path to match the From Email','wp-ticket-ultra')
        ); 
?>
 </table>

 
 </div>
 
 
 
 <div class="wptu-sect  wptu-welcome-panel">
 
 <h3><?php _e('SMTP Settings','wp-ticket-ultra'); ?></h3>
  <p> <strong><?php _e('This options should be set only if you have chosen to send email via SMTP','wp-ticket-ultra'); ?></strong></p>
 
  <table class="form-table">
 <?php
$this->create_plugin_setting(
        'input',
        'bup_smtp_mailing_host',
        __('SMTP Host:','wp-ticket-ultra'),array(),
        __('Specify host name or ip address.','wp-ticket-ultra'),
        __('Specify host name or ip address.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'input',
        'bup_smtp_mailing_port',
        __('SMTP Port:','wp-ticket-ultra'),array(),
        __('Specify Port.','wp-ticket-ultra'),
        __('Specify Port.','wp-ticket-ultra')
); 


$this->create_plugin_setting(
	'select',
	'bup_smtp_mailing_encrytion',
	__('Encryption:','wp-ticket-ultra'),
	array(
		'none' => __('No encryption','wp-ticket-ultra'),
		'ssl' => __('Use SSL encryption','wp-ticket-ultra'), 
		'tls' => __('Use TLS encryption','wp-ticket-ultra'), 
		
		),
		
	__('Specify the encryption method.','wp-ticket-ultra'),
  __('Specify the encryption method.','wp-ticket-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'bup_smtp_mailing_authentication',
	__('Authentication:','wp-ticket-ultra'),
	array(
		'false' => __('No. Do not use SMTP authentication','wp-ticket-ultra'),
		'true' => __('Yes. Use SMTP Authentication','wp-ticket-ultra'), 
		
		),
		
	__('Specify the authentication method.','wp-ticket-ultra'),
  __('Specify the authentication method.','wp-ticket-ultra')
       );

$this->create_plugin_setting(
        'input',
        'bup_smtp_mailing_username',
        __('Username:','wp-ticket-ultra'),array(),
        __('Specify Username.','wp-ticket-ultra'),
        __('Specify Username.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'input',
        'bup_smtp_mailing_password',
        __('Password:','wp-ticket-ultra'),array(),
        __('Input Password.','wp-ticket-ultra'),
        __('Input Password.','wp-ticket-ultra')
); 


 ?>
 
 </table>
 
 
 </div>
 
 <?php if(isset($wptucomplement))
{?>




<div class="wptu-sect wptu-welcome-panel ">

 <p><strong><?php _e('This options should be set only if you have chosen to send email via Mandrill','wp-ticket-ultra'); ?></strong></p>
  <table class="form-table">
 <?php
$this->create_plugin_setting(
        'input',
        'bup_mandrill_api_key',
        __('Mandrill API Key:','xoousers'),array(),
        __('Specify Mandrill API. Find out more info here: https://mandrillapp.com/api/docs/','wp-ticket-ultra'),
        __('Specify Mandrill API.','wp-ticket-ultra')
); 

?>
 
 </table>
</div>

<?php }?>

<div class="wptu-sect  wptu-welcome-panel">

   
   
   <h3><?php _e('Email Subject Structure','wp-ticket-ultra'); ?></h3>  
   <p><?php _e("These settings allow you to set a custom structure for the subject of the ticket email.",'wp-ticket-ultra'); ?></p>
   
   <table class="form-table">
 <?php
 
 
 $this->create_plugin_setting(
	'select',
	'ticket_subject_structure_subject',
	__("Include Ticket's Subject ?:",'wp-ticket-ultra'),
	array(
		'yes' => __('YES','wp-ticket-ultra'),
		'no' => __('NO','wp-ticket-ultra') 
		),
		
	__('If YES, the subject submited by the client will be included in the notification email.','wp-ticket-ultra'),
  __('If YES, the subject submited by the client will be included in the notification email.','wp-ticket-ultra')
       );
	   
$this->create_plugin_setting(
	'select',
	'ticket_subject_structure_ticket_number',
	__("Include Ticket's Number?:",'wp-ticket-ultra'),
	array(
		'yes' => __('YES','wp-ticket-ultra'),
		'no' => __('NO','wp-ticket-ultra') 
		),
		
	__('If YES, the number of the ticket will be included in the email.','wp-ticket-ultra'),
  __('If YES, the number of the ticket will be included in the email.','wp-ticket-ultra')
       );
	   

?>
 
 </table>
   
</div>


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Admin Message New Ticket','wp-ticket-ultra'); ?> <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="1"><i class="fa fa-sort-desc" id="wptu-close-open-icon-1"></i></a></span></h3>
  
  <p><?php _e('This is the welcome email that is sent to the admin when a new ticket is created.','wp-ticket-ultra'); ?></p>
 
 <div class="wptu-messaging-hidden" id="wptu-main-cont-home-1">  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_new_ticket_subject_admin',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_new_ticket_admin',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);


?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_new_ticket_admin'></td>

</tr>	

</table> 
</div>

</div>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Staff Message New Ticket','wp-ticket-ultra'); ?> <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="2"><i class="fa fa-sort-desc" id="wptu-close-open-icon-2"></i></a></span></h3>
  
  <p><?php _e('This email is sent to the staff members when a tikcet is created.','wp-ticket-ultra'); ?></p>
  
  
   <div class="wptu-messaging-hidden" id="wptu-main-cont-home-2"> 
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_new_ticket_subject_staff',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_new_ticket_staff',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);

	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_new_ticket_staff'></td>

</tr>	
</table> 
</div>

</div>


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Client Message New Ticket','wp-ticket-ultra'); ?> <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="3"><i class="fa fa-sort-desc" id="wptu-close-open-icon-3"></i></a></span></h3>
  
  <p><?php _e('This is the welcome email that is sent to the client when a new booking is generated.','wp-ticket-ultra'); ?></p>
  
     <div class="wptu-messaging-hidden" id="wptu-main-cont-home-3"> 

  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_new_ticket_subject_client',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_new_ticket_client',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);



	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_new_ticket_client'></td>

</tr>	
</table> 
</div>

</div>


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Staff Password Change','wp-ticket-ultra'); ?> <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="4"><i class="fa fa-sort-desc" id="wptu-close-open-icon-4"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the staff member every time the password is changed in the staff account.','wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-4">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_password_change_staff_subject',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_password_change_staff',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);



	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_password_change_staff'></td>

</tr>	
</table> 
</div>

</div>


<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Password Reset Link','wp-ticket-ultra'); ?>  <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="5"><i class="fa fa-sort-desc" id="wptu-close-open-icon-5"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the either the client or staff member, this message will containg a reset link.','wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-5">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_reset_link_message_subject',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_reset_link_message_body',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);



	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_reset_link_message_body'></td>

</tr>	
</table> 
</div>

</div>

<?php
	$label_pro= '';
	if(!isset($wptucomplement)){$label_pro='<span class="wptu-pro-only">'.__('PRO Only','wp-ticket-ultra').'</span>';}?>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Welcome Email For Staff Members','wp-ticket-ultra'); ?> <?php echo $label_pro?> <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="6"><i class="fa fa-sort-desc" id="wptu-close-open-icon-6"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the staff member and it includes a welcome message along with a reset link, this will allow the staff members to manage their tickets','wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-6">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_welcome_staff_link_message_subject',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_welcome_staff_link_message_body',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_welcome_staff_link_message_body'></td>

</tr>	
</table> 
</div>

</div>


<?php
	$label_pro= '';
	if(!isset($wptucomplement)){$label_pro='<span class="wptu-pro-only">'.__('PRO Only','wp-ticket-ultra').'</span>';}?>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Client Registration Email','wp-ticket-ultra'); ?> <?php echo $label_pro?> <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="666"><i class="fa fa-sort-desc" id="wptu-close-open-icon-666"></i></a></span></h3>
  
  <p><?php _e('This message will be sent to the client and it includes the password.','wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-666">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_registration_subject',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_registration_body',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_registration_body'></td>

</tr>	
</table> 
</div>

</div>



<?php
	$label_pro= '';
	if(!isset($wptucomplement)){$label_pro='<span class="wptu-pro-only">'.__('PRO Only','wp-ticket-ultra').'</span>';}?>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Assign Ticket To A Staff Member','wp-ticket-ultra'); ?> <?php echo $label_pro;?>  <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="7"><i class="fa fa-sort-desc" id="wptu-close-open-icon-7"></i></a></span></h3>
  
  <p><?php _e("This email will be sent when a ticket is assigned to a new staff member. For example if the owner of Ticket #6588 is John and it's assigned to Mary, then Mary will receive a notification.",'wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-7">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_owner_change_message_subject',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_owner_change_message_body',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_owner_change_message_body'></td>

</tr>	
</table> 
</div>

</div>


<?php
	$label_pro= '';
	?>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Ticket Status Change','wp-ticket-ultra'); ?> <?php echo $label_pro;?>  <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="8"><i class="fa fa-sort-desc" id="wptu-close-open-icon-8"></i></a></span></h3>
  
  <p><?php _e("This email will be sent to the user when the status of a ticket has changed.",'wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-8">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_ticket_status_change_message_subject',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_ticket_status_change_message_body',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_ticket_status_change_message_body'></td>

</tr>	
</table> 
</div>

</div>


<?php
	$label_pro= '';
	?>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('New Reply Staff Notification','wp-ticket-ultra'); ?> <?php echo $label_pro;?>  <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="9"><i class="fa fa-sort-desc" id="wptu-close-open-icon-9"></i></a></span></h3>
  
  <p><?php _e("This email will be sent to the staff member when a new reply is added to the ticket",'wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-9">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_new_reply_subject_staff',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_new_reply_body_staff',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_new_reply_body_staff'></td>

</tr>	
</table> 
</div>

</div>


<?php
	$label_pro= '';
	?>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('New Reply Admin Notification','wp-ticket-ultra'); ?> <?php echo $label_pro;?>  <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="99"><i class="fa fa-sort-desc" id="wptu-close-open-icon-99"></i></a></span></h3>
  
  <p><?php _e("This email will be sent to the admin when a new reply is added to the ticket",'wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-99">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_new_reply_subject_admin',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_new_reply_body_admin',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_new_reply_body_admin'></td>

</tr>	
</table> 
</div>

</div>

<?php
	$label_pro= '';
	?>

<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('New Reply User Notification','wp-ticket-ultra'); ?> <?php echo $label_pro;?>  <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="10"><i class="fa fa-sort-desc" id="wptu-close-open-icon-10"></i></a></span></h3>
  
  <p><?php _e("This email will be sent to the user when either the admin or staff member add a new rely to a ticket.",'wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-10">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'email_new_reply_subject_client',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'email_new_reply_client',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='email_new_reply_client'></td>

</tr>	
</table> 
</div>

</div>



<div class="wptu-sect  wptu-welcome-panel">
  <h3><?php _e('Bugs & Issues Assigned Bug Email','wp-ticket-ultra'); ?>  <span class="wptu-main-close-open-tab"><a href="#" title="<?php _e('Close','wp-ticket-ultra'); ?>" class="wptu-widget-home-colapsable" widget-id="bugs10"><i class="fa fa-sort-desc" id="wptu-close-open-icon-bugs10"></i></a></span></h3>
  
  <p><?php _e("This email will be sent to the assigned user when on the bugs & issues module.",'wp-ticket-ultra'); ?></p>
<div class="wptu-messaging-hidden" id="wptu-main-cont-home-bugs10">  
  
   <table class="form-table">

<?php 


$this->create_plugin_setting(
        'input',
        'bugs_assigned_subject_client',
        __('Subject:','wp-ticket-ultra'),array(),
        __('Set Email Subject.','wp-ticket-ultra'),
        __('Set Email Subject.','wp-ticket-ultra')
); 

$this->create_plugin_setting(
        'textarearich',
        'bugs_assigned_reply_client',
        __('Message','wp-ticket-ultra'),array(),
        __('Set Email Message here.','wp-ticket-ultra'),
        __('Set Email Message here.','wp-ticket-ultra')
);
	
?>

<tr>

<th></th>
<td><input type="button" value="<?php _e('RESTORE DEFAULT TEMPLATE','wp-ticket-ultra'); ?>" class="wptu_restore_template button" b-template-id='bugs_assigned_reply_client'></td>

</tr>	
</table> 
</div>

</div>




<p class="submit">
	<input type="submit" name="mail_setting_submit" id="mail_setting_submit" class="button button-primary" value="<?php _e('Save Changes','wp-ticket-ultra'); ?>"  />

</p>

</form>