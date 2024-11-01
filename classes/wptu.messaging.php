<?php
class WPTicketUltraMessaging extends WPTicketUltraCommon 
{
	var $mHeader;
	var $mEmailPlainHTML;
	var $mHeaderSentFromName;
	var $mHeaderSentFromEmail;
	var $mCompanyName;
	
	var $include_ticket_subject;
	var $include_ticket_number;
	

	function __construct() 
	{
		$this->setContentType();
		$this->setFromEmails();				
		$this->set_headers();	
		
	}
	
	function setFromEmails() 
	{
		global $wpticketultra;
			
		$from_name =  $this->get_option('messaging_send_from_name'); 
		$from_email = $this->get_option('messaging_send_from_email'); 	
		if ($from_email=="")
		{
			$from_email =get_option('admin_email');
			
		}		
		$this->mHeaderSentFromName=$from_name;
		$this->mHeaderSentFromEmail=$from_email;
		
		$this->include_ticket_subject=$this->get_option('ticket_subject_structure_subject');
		$this->include_ticket_number=$this->get_option('ticket_subject_structure_ticket_number');
		
		
			
		
    }
	
	function setContentType() 
	{
		global $wpticketultra;			
				
		$this->mEmailPlainHTML="text/html";
    }
	
	/* get setting */
	function get_option($option) 
	{
		$settings = get_option('wptu_options');
		if (isset($settings[$option])) 
		{
			return $settings[$option];
			
		}else{
			
		    return '';
		}
		    
	}
	
	public function set_headers() 
	{   			
		//Make Headers aminnistrators	
		$headers[] = "Content-type: ".$this->mEmailPlainHTML."; charset=UTF-8";
		$headers[] = "From: ".$this->mHeaderSentFromName." <".$this->mHeaderSentFromEmail.">";
		$headers[] = "Organization: ".$this->mCompanyName;	
		$this->mHeader = $headers;		
    }
	
	
	public function  send ($to, $subject, $message)
	{
		global $wpticketultra , $phpmailer;
		
		$message = nl2br($message);
		//check mailing method	
		$bup_emailer = $wpticketultra->get_option('bup_smtp_mailing_mailer');
		
		if($bup_emailer=='mail' || $bup_emailer=='' ) //use the defaul email function
		{
			$err = wp_mail( $to , $subject, $message, $this->mHeader);
			
			//echo $err. 'message: '.$message;
		
		}elseif($bup_emailer=='mandrill' && is_email($to)){ //send email via Mandrill
		
			$this->send_mandrill( $to , $recipient_name, $subject, $message);
		
		}elseif($bup_emailer=='third-party' && is_email($to)){ //send email via Third-Party
		
			if (function_exists('wptu_third_party_email_sender')) 
			{
				
				wptu_third_party_email_sender($to , $subject, $message);				
				
			}
			
		}elseif($bup_emailer=='smtp' &&  is_email($to)){ //send email via SMTP
		
			// Make sure the PHPMailer class has been instantiated 
			// (copied verbatim from wp-includes/pluggable.php)
			// (Re)create it, if it's gone missing
			if ( !is_object( $phpmailer ) || !is_a( $phpmailer, 'PHPMailer' ) ) {
				require_once ABSPATH . WPINC . '/class-phpmailer.php';
				require_once ABSPATH . WPINC . '/class-smtp.php';
				$phpmailer = new PHPMailer( true );
			}
			
			
			$phpmailer->IsSMTP(); // use SMTP
			
			
			// Empty out the values that may be set
			$phpmailer->ClearAddresses();
			$phpmailer->ClearAllRecipients();
			$phpmailer->ClearAttachments();
			$phpmailer->ClearBCCs();			
			
			// Set the mailer type as per config above, this overrides the already called isMail method
			$phpmailer->Mailer = $bup_emailer;
						
			$phpmailer->From     = $wpticketultra->get_option('messaging_send_from_email');
			$phpmailer->FromName =  $wpticketultra->get_option('messaging_send_from_name');
			
			//Set the subject line
			$phpmailer->Subject = $subject;			
			$phpmailer->CharSet     = 'UTF-8';
			
			//Set who the message is to be sent from
			//$phpmailer->SetFrom($phpmailer->FromName, $phpmailer->From);
			
			//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
			
			
			// Set the Sender (return-path) if required
			if ($wpticketultra->get_option('bup_smtp_mailing_return_path')=='1')
				$phpmailer->Sender = $phpmailer->From; 
			
			// Set the SMTPSecure value, if set to none, leave this blank
			$uultra_encryption = $wpticketultra->get_option('bup_smtp_mailing_encrytion');
			$phpmailer->SMTPSecure = $uultra_encryption == 'none' ? '' : $uultra_encryption;
			
			// If we're sending via SMTP, set the host
			if ($bup_emailer == "smtp")
			{				
				// Set the SMTPSecure value, if set to none, leave this blank
				$phpmailer->SMTPSecure = $uultra_encryption == 'none' ? '' : $uultra_encryption;
				
				// Set the other options
				$phpmailer->Host = $wpticketultra->get_option('bup_smtp_mailing_host');
				$phpmailer->Port = $wpticketultra->get_option('bup_smtp_mailing_port');
				
				// If we're using smtp auth, set the username & password
				if ($wpticketultra->get_option('bup_smtp_mailing_authentication') == "true") 
				{
					$phpmailer->SMTPAuth = TRUE;
					$phpmailer->Username = $wpticketultra->get_option('bup_smtp_mailing_username');
					$phpmailer->Password = $wpticketultra->get_option('bup_smtp_mailing_password');
				}
				
			}
			
			//html plain text			
			$phpmailer->IsHTML(true);	
			$phpmailer->MsgHTML($message);	
			
			//Set who the message is to be sent to
			$phpmailer->AddAddress($to);
			
			//$phpmailer->SMTPDebug = 2;	
			
			//Send the message, check for errors
			if(!$phpmailer->Send()) {
			  echo "Mailer Error: " . $phpmailer->ErrorInfo;
			  exit();
			} else {
			//  echo "Message sent!";
			  
			 
			}
			
		
			//exit;

		
		}
		
		
		
	}
	
	public function  send_mandrill ($to, $recipient_name, $subject, $message_html)
	{
		global $wpticketultra , $phpmailer;
		require_once(wptu_path."libs/mandrill/Mandrill.php");
		
		$from_email     = $wpticketultra->get_option('messaging_send_from_email');
		$from_name =  $wpticketultra->get_option('messaging_send_from_name');
		$api_key =  $wpticketultra->get_option('bup_mandrill_api_key');
		
					
		$text_html =  $message_html;
		$text_txt =  "";
			
		
		try {
				$mandrill = new Mandrill($api_key);
				$message = array(
					'html' => $text_html,
					'text' => $text_txt,
					'subject' => $subject,
					'from_email' => $from_email,
					'from_name' => $from_name,
					'to' => array(
						array(
							'email' => $to,
							'name' => $recipient_name,
							'type' => 'to'
						)
					),
					'headers' => array('Reply-To' => $from_email, 'Content-type' => $this->mEmailPlainHTML),
					'important' => false,
					'track_opens' => null,
					'track_clicks' => null,
					'auto_text' => null,
					'auto_html' => null,
					'inline_css' => null,
					'url_strip_qs' => null,
					'preserve_recipients' => null,
					'view_content_link' => null,
					/*'bcc_address' => 'message.bcc_address@example.com',*/
					'tracking_domain' => null,
					'signing_domain' => null,
					'return_path_domain' => null
					/*'merge' => true,
					'global_merge_vars' => array(
						array(
							'name' => 'merge1',
							'content' => 'merge1 content'
						)
					),
					
					
					/*'google_analytics_domains' => array('example.com'),
					'google_analytics_campaign' => 'message.from_email@example.com',
					'metadata' => array('website' => 'www.example.com'),*/
					
				);
				$async = false;
				$ip_pool = 'Main Pool';
				$send_at = date("Y-m-d H:i:s");
				//$result = $mandrill->messages->send($message, $async, $ip_pool, $send_at);
				$result = $mandrill->messages->send($message, $async);
				//print_r($result);
				
			} catch(Mandrill_Error $e) {
				// Mandrill errors are thrown as exceptions
				echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
				// A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
				throw $e;
			}
	}
	
	//--- Parse Custom Fields
	public function  parse_custom_fields($content, $ticket )
	{
		global $wpticketultra, $wptucomplement;
		
		if(isset($wptucomplement))
		{
			
			preg_match_all("/\[([^\]]*)\]/", $content, $matches);
			$results = $matches[1];			
			$custom_fields_col = array();
			
			foreach ($results as $field){
				
				//clean field
				$clean_field = str_replace("WPTU_CUSTOM_", "", $field);
				$custom_fields_col[] = $clean_field;
			
			}
			
			foreach ($custom_fields_col as $field)
			{
				//get field data from booking table				
				$field_data = $wpticketultra->ticket->get_ticket_meta($ticket->ticket_id, $field);
				//replace data in template				
				$content = str_replace("[WPTU_CUSTOM_".$field."]", $field_data, $content);				
							
			}
			
			

			
		}
		
		return $content;
		
	}
	
	//--- Send status change
	public function  send_status_change($receiver, $ticket)
	{
		global $wpticketultra;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$login_url =$wpticketultra->get_login_page_url(true);
		
		$u_email = $receiver->user_email;
		
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);
		$status = $wpticketultra->status->get_one($ticket->ticket_status);
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		$template_client =stripslashes($this->get_option('email_ticket_status_change_message_body'));
		$subject = $this->get_option('email_ticket_status_change_message_subject');
		
		$template_client = str_replace("{{wptu_client_name}}", $receiver->display_name,  $template_client);				
		$template_client = str_replace("{{wptu_client_login_url}}", $login_url,  $template_client);
		
		$template_client = str_replace("{{wptu_website_name}}", $site->site_name,  $template_client);
		$template_client = str_replace("{{wptu_ticket_status}}", $status->status_name,  $template_client);
		$template_client = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_client);
				
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);	
		
		$this->send($u_email, $subject, $template_client);				
		
	}
	
	//--- New Ticket Assigned	
	public function  send_owner_change($receiver, $ticket)
	{
		global $wpticketultra;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$login_url =$wpticketultra->get_login_page_url(true);
		
		$u_email = $receiver->user_email;
		
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		$template_staff =stripslashes($this->get_option('email_owner_change_message_body'));
		$subject = $this->get_option('email_owner_change_message_subject');
		
		$template_staff = str_replace("{{wptu_staff_name}}", $receiver->display_name,  $template_staff);				
		$template_staff = str_replace("{{wptu_client_login_url}}", $login_url,  $template_staff);
		
		$template_staff = str_replace("{{wptu_website_name}}", $site->site_name,  $template_staff);
		$template_staff = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_staff);
		$template_staff = str_replace("{{wptu_client_name}}", $staff->display_name,  $template_staff);
		$template_staff = str_replace("{{wptu_date}}", $ticket_day,  $template_staff);
		$template_staff = str_replace("{{wptu_department}}", $ticket->department_name,  $template_staff);
		$template_staff = str_replace("{{wptu_priority}}", $priority,  $template_staff);	
		$template_staff = str_replace("{{wptu_subject}}", $ticket->ticket_subject,  $template_staff);
		$template_staff = str_replace("{{wptu_message}}", $ticket->ticket_message,  $template_staff);
		$template_staff = str_replace("{{wptu_ticket_text}}", $ticket->ticket_message,  $template_staff);
		
		
		
		$template_staff = str_replace("{{wptu_company_name}}", $company_name,  $template_staff);
		$template_staff = str_replace("{{wptu_company_phone}}", $company_phone,  $template_staff);
		$template_staff = str_replace("{{wptu_company_url}}", $site_url,  $template_staff);	
		
		$this->send($u_email, $subject, $template_staff);				
		
	}
	
	//--- Reset Link	
	public function  send_reset_link($receiver, $link)
	{
		global $wpticketultra;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('email_reset_link_message_body'));
		$subject = $this->get_option('email_reset_link_message_subject');
		
		$template_client = str_replace("{{wptu_staff_name}}", $receiver->display_name,  $template_client);				
		$template_client = str_replace("{{wptu_reset_link}}", $link,  $template_client);
		
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);	
		
		$this->send($u_email, $subject, $template_client);				
		
	}
	//--- Welcome Email Link	
	public function  send_welcome_email_link($receiver, $link)
	{
		global $bookingultrapro;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('email_welcome_staff_link_message_body'));
		$subject = $this->get_option('email_welcome_staff_link_message_subject');
		
		$template_client = str_replace("{{wptu_staff_name}}", $receiver->display_name,  $template_client);
		$template_client = str_replace("{{wptu_user_name}}", $receiver->user_login,  $template_client);				
		$template_client = str_replace("{{wptu_reset_link}}", $link,  $template_client);
		
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);	
		
		$this->send($u_email, $subject, $template_client);				
		
	}
	
	
		//--- Registration Link
	public function  send_client_registration_link($receiver, $link, $password)
	{
		global $bookingultrapro;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$u_email = $receiver->user_email;
		
		$template_client =stripslashes($this->get_option('email_registration_body'));
		$subject = $this->get_option('email_registration_subject');
		
		$template_client = str_replace("{{wptu_client_name}}", $receiver->display_name,  $template_client);
		$template_client = str_replace("{{wptu_user_name}}", $receiver->user_login,  $template_client);	
		$template_client = str_replace("{{wptu_user_password}}", $password,  $template_client);			
		$template_client = str_replace("{{wptu_login_link}}", $link,  $template_client);
		
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);	
		
		$this->send($u_email, $subject, $template_client);				
		
	}
	
	
	//--- New Password Backend
	public function  send_new_password_to_user($staff, $password1)
	{
		global $bookingultrapro;
				
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		//get templates	
		$template_client =stripslashes($this->get_option('email_password_change_staff'));
		
		$site_url =site_url("/");
	
		$subject_client = $this->get_option('email_password_change_staff_subject');				
		//client		
		$template_client = str_replace("{{wptu_staff_name}}", $staff->display_name,  $template_client);	
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);										
		//send to client
		$this->send($staff->user_email, $subject_client, $template_client);		
		
	}
	
	public function  get_subject_format($subject_text, $ticket_subject,  $ticket_number )
	{	
		
		if($this->include_ticket_subject != 'NO' )
		{			
			$subject_text = $subject_text.' - '.$ticket_subject;			
		}
		
		if($this->include_ticket_number != 'NO' )
		{			
			$subject_text = $subject_text.' Ticket #'.$ticket_number;			
		}
		
		
		return $subject_text;
		
	}
	
	//--- Send Reply Notification to super admin
	public function  send_ticket_reply_message_admin($ticket_id, $reply = null)
	{
		global $wpticketultra;
			
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$ticket_subject = $ticket->ticket_subject;
		
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		//get templates	
		$template_admin =stripslashes($this->get_option('email_new_reply_body_admin'));
		
		$site_url =site_url("/");		
		$login_url =$wpticketultra->get_login_page_url(true);	
		
		$subject_admin = $this->get_subject_format($this->get_option('email_new_reply_subject_admin'), $ticket_subject,  $ticket->ticket_id );			
		
		//department		
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);
		
		//staff			
		$template_admin = str_replace("{{wptu_website_name}}", $site->site_name,  $template_admin);
		$template_admin = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_admin);		
		$template_admin = str_replace("{{wptu_priority}}", $priority,  $template_admin);	
		$template_admin = str_replace("{{wptu_subject}}", $ticket->ticket_subject,  $template_admin);
		
		$template_admin = str_replace("{{wptu_ticket_text}}",  $reply->reply_message,  $template_admin);
		$template_admin = str_replace("{{wptu_message}}",  $reply->reply_message,  $template_admin);
				
		$template_admin = str_replace("{{wptu_company_name}}", $company_name,  $template_admin);
		$template_admin = str_replace("{{wptu_company_phone}}", $company_phone,  $template_admin);
		$template_admin = str_replace("{{wptu_company_url}}", $site_url,  $template_admin);
		
		//parse custom fields
		$template_admin = $this->parse_custom_fields($template_admin, $ticket );							
				
		//send to admin
		$this->send($admin_email, $subject_admin, $template_admin);
		
	}
	
	//--- Send Reply Notification to Staff Members
	public function  send_ticket_reply_message_staff($ticket_id, $staff, $reply)
	{
		global $wpticketultra;
		
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$ticket_subject = $ticket->ticket_subject;
		
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		//get templates	
		$template_staff =stripslashes($this->get_option('email_new_reply_body_staff'));
		
		$site_url =site_url("/");		
		$login_url =$wpticketultra->get_login_page_url(true);	
		
		$subject_staff = $this->get_subject_format($this->get_option('email_new_reply_subject_staff'), $ticket_subject,  $ticket->ticket_id );			
		
		//department		
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);
		
		//staff			
		$template_staff = str_replace("{{wptu_website_name}}", $site->site_name,  $template_staff);
		$template_staff = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_staff);		
		$template_staff = str_replace("{{wptu_priority}}", $priority,  $template_staff);	
		$template_staff = str_replace("{{wptu_subject}}", $ticket->ticket_subject,  $template_staff);
		
		$template_staff = str_replace("{{wptu_ticket_text}}",  $reply->reply_message,  $template_staff);
		$template_staff = str_replace("{{wptu_message}}",  $reply->reply_message,  $template_staff);
		
					
		$template_staff = str_replace("{{wptu_company_name}}", $company_name,  $template_staff);
		$template_staff = str_replace("{{wptu_company_phone}}", $company_phone,  $template_staff);
		$template_staff = str_replace("{{wptu_company_url}}", $site_url,  $template_staff);
		
		//parse custom fields
		$template_staff = $this->parse_custom_fields($template_staff, $ticket );							
				
		//send to staff
		$this->send($staff->user_email, $subject_staff, $template_staff);
		
	}
	
	//--- Send Notification To Client Of Reply
	public function  send_ticket_front_reply_client($ticket_id, $reply, $client)
	{
		global $wpticketultra;
		
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);		
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		
		$ticket_subject = $ticket->ticket_subject;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		//get templates	
		$template_client =stripslashes($this->get_option('email_new_reply_client'));
		
		$site_url =site_url("/");
		
		$login_url =$wpticketultra->get_login_page_url(true);			
			
		$subject_client = $this->get_subject_format($this->get_option('email_new_reply_subject_client'), $ticket_subject,  $ticket->ticket_id );	
		
		//department		
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);		
						
		//client	
		$template_client = str_replace("{{wptu_client_name}}", $client->display_name,  $template_client);	
		$template_client = str_replace("{{wptu_website_name}}", $site->site_name,  $template_client);
		$template_client = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_client);
		$template_client = str_replace("{{wptu_ticket_text}}", $reply->reply_message,  $template_client);
		$template_client = str_replace("{{wptu_message}}", $reply->reply_message,  $template_client);		
		$template_client = str_replace("{{wptu_client_login_url}}", $login_url,  $template_client);
				
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);
		
		//parse custom fields
		$template_client = $this->parse_custom_fields($template_client, $ticket );
				
		//send to client
		$this->send($client->user_email, $subject_client, $template_client);		
					
		
	}
	
	
	//--- Send Notification to staff members
	public function  send_ticket_front_message_staff($ticket_id, $staff)
	{
		global $wpticketultra;
		
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$ticket_subject = $ticket->ticket_subject;
		
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		//get templates	
		$template_staff =stripslashes($this->get_option('email_new_ticket_staff'));
		
		$site_url =site_url("/");
		
		$login_url =$wpticketultra->get_login_page_url(true);	
		
		$subject_staff = $this->get_subject_format($this->get_option('email_new_ticket_subject_staff'), $ticket_subject,  $ticket->ticket_id );				
		
		
		//department		
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);
		
		//staff			
		$template_staff = str_replace("{{wptu_website_name}}", $site->site_name,  $template_staff);
		$template_staff = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_staff);
		$template_staff = str_replace("{{wptu_client_name}}", $staff->display_name,  $template_staff);
		$template_staff = str_replace("{{wptu_date}}", $ticket_day,  $template_staff);
		$template_staff = str_replace("{{wptu_department}}", $ticket->department_name,  $template_staff);
		$template_staff = str_replace("{{wptu_priority}}", $priority,  $template_staff);	
		$template_staff = str_replace("{{wptu_subject}}", $ticket->ticket_subject,  $template_staff);
		$template_staff = str_replace("{{wptu_message}}", $ticket->ticket_message,  $template_staff);
		$template_staff = str_replace("{{wptu_ticket_text}}", $ticket->ticket_message,  $template_staff);
				
		$template_staff = str_replace("{{wptu_company_name}}", $company_name,  $template_staff);
		$template_staff = str_replace("{{wptu_company_phone}}", $company_phone,  $template_staff);
		$template_staff = str_replace("{{wptu_company_url}}", $site_url,  $template_staff);
		
		//parse custom fields
		$template_staff = $this->parse_custom_fields($template_staff, $ticket );							
				
		//send to staff
		$this->send($staff->user_email, $subject_staff, $template_staff);
		
					
		
	}
	
	
	//--- Send Notification To Client
	public function  send_ticket_front_message_client($ticket_id, $client)
	{
		global $wpticketultra;
		
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);		
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		
		$ticket_subject = $ticket->ticket_subject;
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		//get templates	
		$template_client =stripslashes($this->get_option('email_new_ticket_client'));
		
		$site_url =site_url("/");
		
		$login_url =$wpticketultra->get_login_page_url(true);			
			
		$subject_client = $this->get_subject_format($this->get_option('email_new_ticket_subject_client'), $ticket_subject,  $ticket->ticket_id );	
		
		//department		
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);		
						
		//client		
		$template_client = str_replace("{{wptu_website_name}}", $site->site_name,  $template_client);
		$template_client = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_client);
		$template_client = str_replace("{{wptu_client_name}}", $client->display_name,  $template_client);
		$template_client = str_replace("{{wptu_date}}", $ticket_day,  $template_client);
		$template_client = str_replace("{{wptu_department}}", $ticket->department_name,  $template_client);
		$template_client = str_replace("{{wptu_priority}}", $priority,  $template_client);	
		$template_client = str_replace("{{wptu_subject}}", $ticket->ticket_subject,  $template_client);
		$template_client = str_replace("{{wptu_message}}", $ticket->ticket_message,  $template_client);	
		$template_client = str_replace("{{wptu_ticket_text}}", $ticket->ticket_message,  $template_client);	
		
			
		$template_client = str_replace("{{wptu_client_login_url}}", $login_url,  $template_client);
				
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);
		
		//parse custom fields
		$template_client = $this->parse_custom_fields($template_client, $ticket );
				
		//send to client
		$this->send($client->user_email, $subject_client, $template_client);		
					
		
	}
	
	//--- Send Notification To Admin
	public function  send_ticket_front_message_admin($ticket_id)
	{
		global $wpticketultra;
		
	
		$ticket = $wpticketultra->ticket->get_one($ticket_id);	
		$ticket_subject = $ticket->ticket_subject;	
		$site = $wpticketultra->site->get_one($ticket->ticket_website_id);
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($ticket->ticket_date ));
		
		//get templates	
		$template_admin =stripslashes($this->get_option('email_new_ticket_admin'));
		
		$site_url =site_url("/");
		
		$login_url =$wpticketultra->get_login_page_url(true);			
				
		$subject_admin = $this->get_subject_format($this->get_option('email_new_ticket_subject_admin'), $ticket_subject,  $ticket->ticket_id );
		
		//department		
		$priority = $wpticketultra->get_priority($ticket->ticket_priority);		
		
		//admin	
		$template_admin = str_replace("{{wptu_website_name}}", $site->site_name,  $template_admin);
		$template_admin = str_replace("{{wptu_ticket_number}}", $ticket->ticket_id,  $template_admin);	
		$template_admin = str_replace("{{wptu_date}}", $ticket_day,  $template_admin);
		$template_admin = str_replace("{{wptu_department}}", $ticket->department_name,  $template_admin);
		$template_admin = str_replace("{{wptu_priority}}", $priority,  $template_admin);	
		$template_admin = str_replace("{{wptu_subject}}", $ticket->ticket_subject,  $template_admin);
		$template_admin = str_replace("{{wptu_message}}", $ticket->ticket_message,  $template_admin);
		$template_admin = str_replace("{{wptu_ticket_text}}", $ticket->ticket_message,  $template_admin);
		
		
		
		$template_admin = str_replace("{{wptu_company_name}}", $company_name,  $template_admin);
		$template_admin = str_replace("{{wptu_company_phone}}", $company_phone,  $template_admin);
		$template_admin = str_replace("{{wptu_company_url}}", $site_url,  $template_admin);	
		
		//parse custom fields
		$template_admin = $this->parse_custom_fields($template_admin, $ticket );
				
		//send to admin
		$this->send($admin_email, $subject_admin, $template_admin);		
					
		
	}
	
	
	//--- Send Bug Assignee
	public function  send_bug_assignee($receiver, $bug_id)
	{
		global $wpticketultra, $wptu_bugtracker;
		
		
		$admin_email =get_option('admin_email'); 
		$company_name = $this->get_option('company_name');
		$company_phone = $this->get_option('company_phone');
		
		$login_url =$wpticketultra->get_login_page_url(true);		
		$u_email = $receiver->user_email;		
		$datetime_format =  $wpticketultra->get_date_to_display();
		
		$site_url =site_url("/");
		
		//get bug		
		$bug = $wptu_bugtracker->get_one_as_user ($bug_id,null);
		
		$site = $wpticketultra->site->get_one($bug->site_id);
		$priority = $wptu_bugtracker->priority->get_one($bug->bug_priority_id );
		$status = $wptu_bugtracker->status->get_one($bug->bug_status_id );
		
		$time_format = $wpticketultra->get_time_format();		
		$ticket_day = date('l, j F, Y', strtotime($bug->bug_date ));
		$assigned_date = date( $datetime_format , current_time( 'timestamp', 0 ) );
		
		$template_client =stripslashes($this->get_option('bugs_assigned_reply_client'));
		$subject = $this->get_option('bugs_assigned_subject_client');
		
		$template_client = str_replace("{{wptu_staff_name}}", $receiver->display_name,  $template_client);				
		$template_client = str_replace("{{wptu_client_login_url}}", $login_url,  $template_client);
		
		$template_client = str_replace("{{wptu_website_name}}", $site->site_name,  $template_client);
		$template_client = str_replace("{{wptu_bug_status}}", $status->status_name,  $template_client);
		$template_client = str_replace("{{wptu_bug_priority}}", $priority->priority_name,  $template_client);
		$template_client = str_replace("{{wptu_bug_number}}", $bug_id,  $template_client);
		$template_client = str_replace("{{wptu_date}}", $assigned_date,  $template_client);
		$template_client = str_replace("{{wptu_bug_subject}}", $bug->bug_subject,  $template_client);
				
		$template_client = str_replace("{{wptu_company_name}}", $company_name,  $template_client);
		$template_client = str_replace("{{wptu_company_phone}}", $company_phone,  $template_client);
		$template_client = str_replace("{{wptu_company_url}}", $site_url,  $template_client);	
		
		$this->send($u_email, $subject, $template_client);				
		
	}
	
	
	
	public function  paypal_ipn_debug( $message)
	{
		global $bookingultrapro;
		$admin_email =get_option('admin_email'); 	
		
		
		$this->send($admin_email, "IPN notification", $message);
					
		
	}
	
	
	

}
$key = "messaging";
$this->{$key} = new WPTicketUltraMessaging();