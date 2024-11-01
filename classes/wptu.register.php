<?php
class WPTicketUltraRegister {
	
	

	function __construct() 
	{			
		add_action( 'init', array($this, 'wptu_handle_hooks_actions') );			
		add_action( 'init', array($this, 'wptu_handle_post') );			

	}
	
	function wptu_handle_hooks_actions ()	
	{
		if (function_exists('wptu_registration_hook')) 
		{		
			add_action( 'user_register', 'wptu_registration_hook' );	
		
		}
		
		if (function_exists('wptu_after_login_hook')) 
		{		
			//add_action( 'wp_login', 'wptu_after_login_hook' , 102,2);			
		}			
		
				
	}
	

	function wptu_handle_post () 
	{		
		
		/*Form is fired*/	    
		if (isset($_POST['wptu-register-form'])) {
			
			/* Prepare array of fields */
			$this->prepare_request( $_POST );
       			
			/* Validate, get errors, etc before we create account */
			$this->handle_errors();
			
			/* Create account */
			$this->create_account();
				
		}		
		
	}
		
	/*Prepare user meta*/
	function prepare_request ($array ) 
	{
		foreach($array as $k => $v) 
		{
			
			if ($k == 'wptu-registration-form' || $k == 'user_pass_confirm' || $k == 'user_pass' || $k == 'wptu-register-form' || $k == 'wptu-priority' || $k == 'wptu-category' || $k == 'wptu_subject' || $k == 'user_email' || $k == 'user_email_2'  || $k == 'display_name'  ) continue; 
			
			
			$this->usermeta[$k] = $v;
		}
		return $this->usermeta;
	}
	
	/*Handle/return any errors*/
	function handle_errors() 
	{
	    global $wpticketultra, $wptu_recaptcha;
		
		
		    foreach($this->usermeta as $key => $value) 
			{
		    
		        /* Validate username */
		        if ($key == 'user_login') 
				{
		            if (esc_attr($value) == '') {
						
		                $this->errors[] = __('<strong>ERROR:</strong> Please enter a username.','wp-ticket-ultra');
						
		            } elseif (username_exists($value)) {
						
		               // $this->errors[] = __('<strong>ERROR:</strong> This username is already registered. Please choose another one.','wp-ticket-ultra');
		            }
		        }
		    
		        /* Validate email */
		        if ($key == 'user_email') 
				{
		            if (esc_attr($value) == '') 
					{
		                $this->errors[] = __('<strong>ERROR:</strong> Please type your e-mail address.','wp-ticket-ultra');
						
		            } elseif (!is_email($value)) 
					{
		                $this->errors[] = __('<strong>ERROR:</strong> The email address isn\'t correct.','wp-ticket-ultra');
					
					} elseif ($value!=$_POST['user_email_2']) 
					{
		               // $this->errors[] = __('<strong>ERROR:</strong> The emails are different.','wp-ticket-ultra');
						
		            } elseif (email_exists($value)) 
					{
		                
		            }
		        }
				
		    
		    }
			
			//captcha here       
			
        
            //CHECK NONCE
            if(!isset($_POST['wpticketultra_csrf_token'])){

                $this->errors[] = __('<strong>ERROR:</strong> Nonce not received.','wp-ticket-ultra');    

            }else{

                if(wp_verify_nonce($_POST['wpticketultra_csrf_token'], 'wpticketultra_reg_action')){

                         // Nonce is matched and valid. do whatever you want now.

                 }else{

                          // Invalid nonce. you can throw an error here.
                           $this->errors[] = __('<strong>ERROR:</strong> Invalid Nonce.','wp-ticket-ultra');
                 }

            }

            //END NONCE
        
            $g_recaptcha_response = '';		
            if(isset($_POST['g-recaptcha-response']) && $_POST['g-recaptcha-response']!=''){

                $g_recaptcha_response = sanitize_text_field($_POST['g-recaptcha-response']);		
            }

            //check reCaptcha
            $is_valid_recaptcha = true;	
            if(isset($wptu_recaptcha) && $wpticketultra->get_option('recaptcha_site_key')!='' && $wpticketultra->get_option('recaptcha_secret_key')!='' && $wpticketultra->get_option('recaptcha_display_registration')=='1' ){

                $is_valid_recaptcha = $wptu_recaptcha->validate_recaptcha_field($g_recaptcha_response);	

            }
        
            if(!$is_valid_recaptcha){
                
                $this->errors[] = __('<strong>ERROR:</strong> Invalid reCaptcha.','wp-ticket-ultra');
             }
        
        
			
			
	}
	
	
	
	//validate password one letter and one number	
	function validate_password_numbers_letters ($myString)
	{
		$ret = false;
		
		
		if (preg_match('/[A-Za-z]/', $myString) && preg_match('/[0-9]/', $myString))
		{
			$ret = true;
		}
					
		return $ret;
	
	
	}
	
	//at least one upper case character 	
	function validate_password_one_uppercase ($myString)
	{	
		
		if( preg_match( '~[A-Z]~', $myString) ){
   			 $ret = true;
		} else {
			
			$ret = false;
		  
		}
					
		return $ret;
	
	}
	
	//at least one lower case character 	
	function validate_password_one_lowerrcase ($myString)
	{	
		
		if( preg_match( '~[a-z]~', $myString) ){
   			 $ret = true;
		} else {
			
			$ret = false;
		  
		}
					
		return $ret;	
	
	}
	
	
	public function genRandomStringActivation($length) 
	{
			
			$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";
			
			$real_string_legnth = strlen($characters) ;
			//$real_string_legnth = $real_string_legnth– 1;
			$string="ID";
			
			for ($p = 0; $p < $length; $p++)
			{
				$string .= $characters[mt_rand(0, $real_string_legnth-1)];
			}
			
			return strtolower($string);
	}
	
		
	
	
	public function genRandomString() 
	{
		$length = 5;
		$characters = "0123456789ABCDEFGHIJKLMNOPQRSTUVWZYZ";
		
		$real_string_legnth = strlen($characters) ;
		//$real_string_legnth = $real_string_legnth– 1;
		$string="ID";
		
		for ($p = 0; $p < $length; $p++)
		{
			$string .= $characters[mt_rand(0, $real_string_legnth-1)];
		}
		
		return strtolower($string);
	}
	
		
	
	/*Create user*/
	function create_account() 
	{
		
		global $wpticketultra, $wptucomplement, $wptu_aweber, $wptu_activity_tracker, $wptu_aweber;
		session_start();
		
		$custom_form =  $_POST['bup-custom-form-id'];
		$filter_id =  $_POST['bup-filter-id'];
		$ticket_temp_upload_folder =  $_POST['wptu_temp_ticket_id'];
        
        		
		//echo "error " . print_r($this->errors);
			
			/* Create profile when there is no error */
			if (!isset($this->errors)) 
			{
				 if(is_user_logged_in())
				 {
					 			 
					 //get current user info
					 $current_user = $wpticketultra->userpanel->get_user_info();
					 $user_id = $current_user->ID;				 			 
					 $is_client  = $wpticketultra->profile->is_client($user_id);
					 $is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);
					 
					 
					 if(!$is_client) //this is either a staff member or the damin
					 {
						 //user id submited by the staff member
						 $user_id = $_POST['wptu_client_id'];
						 
						 if(isset($_POST['wptu_staff_id']) && $_POST['wptu_staff_id']!='')					 
						 {
							 $is_from_admin = true;
							 //the staff ID would is set via POST	
							 $staff_id = $_POST['wptu_staff_id'];
							 
						 }else{
							 
							  $is_from_admin = false;					 
							 		 
							  //the staff ID would be the current logged in user ID	
						 	  $staff_id = $current_user->ID;			
						
						}
						
						
					 }else{	 //this is a client	
					 
					 	
						 $user_id =$current_user->ID;
						 $staff_id = '';						 
					 
					 } //end if client
					 
				
				 }else{ //the user is NOT logged in which is treated as a simple user not a staff member
		 			
					
					/* Create account, update user meta */				
					$visitor_ip = $_SERVER['REMOTE_ADDR'];	
									
					if(email_exists($_POST['user_email']))
					{
						
						$user_d = get_user_by( 'email', $_POST['user_email'] );
						$user_id  = $user_d->ID;
					
					}else{ // new user we have to create it.
						
						$sanitized_user_login = sanitize_user($_POST['user_email']);
					
						/* We create the New user */
						$user_pass = wp_generate_password( 12, false);
						$user_id = wp_create_user( $sanitized_user_login, $user_pass, $_POST['user_email'] );	
						wp_update_user( array('ID' => $user_id, 'display_name' => esc_attr($_POST['display_name'])) );
						
						//assign default role for this user						
						$new_role = 'wptu_user';
						
						//set custom role for this user
						if($new_role!="")
						{
							$user = new WP_User( $user_id );
							$user->set_role( $new_role );						
						}		
					
					}
					
					if (  $user_id ) 
					{
						
						$is_client  = $wpticketultra->profile->is_client($user_id);
						
						if($is_client) //only if is a client
						{ 
						
							$visitor_ip = $_SERVER['REMOTE_ADDR'];
							update_user_meta($user_id, 'wptu_user_registered_ip', $visitor_ip);					
							update_user_meta($user_id, 'wptu_is_client', 1);					
							update_user_meta($user_id, 'last_name', $_POST['last_name']);							
							update_user_meta($user_id, 'first_name',$_POST['display_name']);
																
												
							//set account status						
							$verify_key = $this->get_unique_verify_account_id();					
							update_user_meta ($user_id, 'wptu_ultra_very_key', $verify_key);
						
						} //if is client
						
					
					  }
					
					
				
				} //end if user is logged in
				
				
						
								
							
				if (  $user_id ) 
				{
					
					//create transaction
					$transaction_key = session_id()."_".time();			
											
					//create ticket				
					$subject = $wpticketultra->format_subject_string($_POST['wptu_subject']);
					$site = $_POST['wptu_site'];
					$priority = $_POST['wptu-priority']; 
					$department = $_POST['wptu-category'];
					$message = $_POST['special_notes'];	
					
					$site_date = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
									
					//is resolved overdue?	
					$priority_data = $wpticketultra->priority->get_one($priority);	
					$replied_within = $priority_data->priority_respond_within; //in seconds
					$resolve_within = $priority_data->priority_resolve_within; //in seconds
		
					$due_resolve_date =  date("Y-m-d H:i:s", (strtotime($site_date) + $resolve_within));
					$due_first_reply_date =  date("Y-m-d H:i:s", (strtotime($site_date) + $replied_within));
					
					//if(!isset($wptucomplement)){$staff_id = $user_id;} //tweak for lite
					
					$ticket_woocomerce_prod_id = '';
					$ticket_woocomerce_order_id = '';
					
					if(isset($_POST['woo_order_id']) && $_POST['woo_order_id']!=''){
						
						$ticket_woocomerce_order_id = $_POST['woo_order_id'];					
					}
					
					if(isset($_POST['wptu-woo-product-id']) && $_POST['wptu-woo-product-id']!=''){
						
						$ticket_woocomerce_prod_id = $_POST['wptu-woo-product-id'];					
					}
					
					$t_data = array(
									
							 'user_id' => $user_id,
							 'staff_id' => $staff_id,
							 'site_id' => $site,	
							 'transaction_key' => $transaction_key,					 
							 'subject' => $subject,
							 'priority' => $priority,
							 'department' => $department,
							 'message' => $message,
							 'date' => $site_date,
							 'resolved_within' => $due_resolve_date,
							 'reply_within' => $due_first_reply_date,
							 'ticket_woocomerce_prod_id' => $ticket_woocomerce_prod_id,
							 'ticket_woocomerce_order_id' => $ticket_woocomerce_order_id				 
							 
							 ); 
							 
							 	
					if($site !='')	// Let's create the ticket which needs a site id			
					{						
						$ticket_id = $wpticketultra->ticket->create_ticket($t_data);
						
						
						//track event
						if( $is_client)
						{									
							if(isset($wptu_activity_tracker))				
							{
								$wptu_activity_tracker->create_action(8, $user_id, $ticket_id , $reply_id);	
							
							}	
						
						}else{
							
							if(isset($wptu_activity_tracker))				
							{
								$wptu_activity_tracker->create_action(7, $user_id, $ticket_id , $reply_id);	
							
							}					
							
						}
						
						if($ticket_id !='')	// Let's create the first reply			
						{							
														
							if(is_user_logged_in())
				 			{
								$user_id = $current_user->ID;
								
								if(!$is_client || $is_super_admin || is_admin()) //only if not is client or not super admin
								{
								
									//the reply is posted by staff member or admin
									$wpticketultra->ticket->update_ticket_status ($ticket_id,3)	;
									
									if(!$is_from_admin)
									{
										//update owner					
										$wpticketultra->ticket->update_ticket_owner ($ticket_id,$user_id);
										
									}else{
										
										//update owner set by admin			
										$wpticketultra->ticket->update_ticket_owner ($ticket_id,$staff_id);
										
									}
									
									//update has first reply					
									$wpticketultra->ticket->update_ticket_has_first_reply ($ticket_id,'1');
									
									//update last replier					
									$wpticketultra->ticket->update_ticket_last_replier ($ticket_id,$user_id);
									
									//update first reply date				
									$wpticketultra->ticket->update_ticket_first_reply($ticket_id, $site_date);				
								
								}else{ //this is the client
									
								}							
								
											
							}				
							
							
							$t_data = array(									
								 'reply_ticket_id' => $ticket_id,
								 'reply_user_id' => $user_id,	
								 'reply_message' => $message,						
								 'reply_date' => $site_date
								 
								 ); 
								 					
							$reply_id = $wpticketultra->ticket->create_ticket_reply_db($t_data);
							
							// 1 move files
							$wpticketultra->ticket->ticket_reply_move_files($reply_id, $ticket_temp_upload_folder);
						
						}
					
					}				
				}
				
								
				if($ticket_id!='' && $site !='')				
				{
					/*We've got a valid id then let's create the meta informaion*/						
					foreach($this->usermeta as $key => $value) 
					{					 
						if (is_array($value))   // checkboxes
						{
							$value = implode(',', $value);
						}						
						
						$wpticketultra->ticket->update_ticket_meta($ticket_id, $key, esc_attr($value));
												
						
					}
					
					// update from public form including custom fields
					$wpticketultra->ticket->update_ticket_meta($ticket_id, $key, esc_attr($value));
					
				}
				
				if(isset($wptu_mailchimp))
				{
				
					//mailchimp					 
					 if(isset($_POST["wptu-mailchimp-confirmation"]) && $_POST["wptu-mailchimp-confirmation"]==1)				 {
						 $list_id =  $wptucomplement->get_option('mailchimp_list_id');					 
						 $wptu_aweber->mailchimp_subscribe($user_id, $list_id);
						 update_user_meta ($user_id, 'wptu_mailchimp', 1);				 						
						
					 }
					 
					
				
				}
				
				
				if(isset($wptu_aweber)){
					
					 //aweber	
					 $list_id = get_option( "wpturoaw_aweber_list");				 
					 if(isset($_POST["wptu-aweber-confirmation"]) && $_POST["wptu-aweber-confirmation"]==1 && $list_id !='')				 {
						 
						 $user_l = get_user_by( 'id', $user_id ); 				 
						 $wptu_aweber->wpturoaw_subscribe($user_l, $list_id);
						 update_user_meta ($user_id, 'wptu_aweber', 1);				 						
						
					 }
				
				}
				
				$this->handle_notifications($ticket_id);
				
				//redir
				$this->handle_redir_success($transaction_key, $ticket_id);		
				
			} //end error link
			
	}
	
	//this manages the notifications
	public function handle_notifications($ticket_id)
	{
		global $wpticketultra, $wptucomplement, $wp_rewrite ;
		
		$notificaton_rule = '';
		
		if(isset($wptucomplement))
		{
			$notificaton_rule = $wpticketultra->get_option('notification_rules_new_ticket');		
		
		}		
		
		//get ticket
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$client = get_user_by( 'id', $ticket->ticket_user_id );
		
		if(	$ticket->ticket_is_private==1) 
		{
			$staff = get_user_by( 'id', $ticket->ticket_staff_id );
			
			//only staff and client will receive notifications. 
			//Private ticket can be created only by staff and admin.
						
			//send email to staff member/ticket owner
			$wpticketultra->messaging->send_ticket_front_message_staff($ticket_id, $staff);	
			
			//notify client
			$wpticketultra->messaging->send_ticket_front_message_client($ticket_id, $client);		
		
		
		}else{ //this is not a private ticket
			
			//Do we have to notify all staff members within a department?
			
			if( ($notificaton_rule==1 || $notificaton_rule=="") && isset($wptucomplement) ) 
			{
				//we have to notify all staff members within the department
				
				//get staff members within a department				
				$staff_members = $wptucomplement->profile->get_staff_within_depto($ticket->ticket_department_id);
				
				if ( !empty( $staff_members ) )
				{
				
					foreach ( $staff_members as $staff )
					{
						//send email to staff members
						$wpticketultra->messaging->send_ticket_front_message_staff($ticket_id, $staff);					
					
					}					
				
				}
				
				//notify website admin
				$wpticketultra->messaging->send_ticket_front_message_admin($ticket_id);	
				
				//notify client
				$wpticketultra->messaging->send_ticket_front_message_client($ticket_id, $client);				
				
			
			}elseif( ($notificaton_rule==1 || $notificaton_rule=="") && !isset($wptucomplement) ){
				
				//we notify only to the admin and client
				
				//notify website admin
				$wpticketultra->messaging->send_ticket_front_message_admin($ticket_id);
				
				//notify client
				$wpticketultra->messaging->send_ticket_front_message_client($ticket_id, $client);
			
			}elseif( ($notificaton_rule==2 )  ){
				
				//notify website admin ONLY
				$wpticketultra->messaging->send_ticket_front_message_admin($ticket_id);
				
				//notify client
				$wpticketultra->messaging->send_ticket_front_message_client($ticket_id, $client);
				
			
			}elseif( ($notificaton_rule==3 )  ){
				
				//DO NOT SEND NOTIFICTIONS	ONLY THE CLIENT RECEIVES A NOTIFICATION
				
				//notify client
				$wpticketultra->messaging->send_ticket_front_message_client($ticket_id, $client);		
				
			
			
			}
			
			
			
		
		} //end if private
		
	
	}
	
	//this manages the notifications when replying a ticket
	public function handle_reply_notifications($ticket_id, $reply, $is_client)
	{
		global $wpticketultra, $wptucomplement, $wp_rewrite ;
		
		$notificaton_rule = '';
		
		if(isset($wptucomplement))
		{
			$notificaton_rule = $wpticketultra->get_option('notification_rules_new_reply');		
		
		}		
		
		//get ticket
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$client = get_user_by( 'id', $ticket->ticket_user_id );
		
		if(	$ticket->ticket_is_private==1) 
		{
			$staff = get_user_by( 'id', $ticket->ticket_staff_id );
			
			//only staff and client will receive notifications. 
			//Private ticket can be created only by staff and admin.
						
			//send email to staff member/ticket owner
			$wpticketultra->messaging->send_ticket_reply_message_staff($ticket_id, $staff, $reply);
			
			
			if(!$is_client)
			{							
				//notify client only if the reply has been posted by the
				$wpticketultra->messaging->send_ticket_front_reply_client($ticket_id, $reply, $client);
			
			}
		
		
		}else{ //this is not a private ticket
			
			//Do we have to notify all staff members within a department?
			
			if( ($notificaton_rule==1 || $notificaton_rule=="") && isset($wptucomplement) ) 
			{
				//we have to notify all staff members within the department
				
				//get staff members within a department				
				$staff_members = $wptucomplement->profile->get_staff_within_depto($ticket->ticket_department_id);
				
				if ( !empty( $staff_members ) )
				{
				
					foreach ( $staff_members as $staff )
					{
						//send email to staff members
						$wpticketultra->messaging->send_ticket_reply_message_staff($ticket_id, $staff, $reply);					
					
					}					
				
				}
				
				if($is_client)
				{				
					//notify website admin
					$wpticketultra->messaging->send_ticket_reply_message_admin($ticket_id, $reply);
				
				}
				
				if(!$is_client)
				{								
					//notify client only if the reply has been posted by the
					$wpticketultra->messaging->send_ticket_front_reply_client($ticket_id,  $reply, $client);
				
				}				
				
			
			}elseif( ($notificaton_rule==1 || $notificaton_rule=="") && !isset($wptucomplement) ){
				
				//we notify only to the admin and client
				
				//notify website admin
				$wpticketultra->messaging->send_ticket_reply_message_admin($ticket_id);
				
				if(!$is_client)
				{								
					//notify client only if the reply has been posted by the
					$wpticketultra->messaging->send_ticket_front_reply_client($ticket_id, $reply, $client);
				
				}
			
			}elseif( ($notificaton_rule==2 )  ){
				
				//notify website admin ONLY if posted by the client
				if($is_client)
				{
					$wpticketultra->messaging->send_ticket_reply_message_admin($ticket_id, $reply);				
				}
				
				if(!$is_client)
				{								
					//notify client only if the reply has been posted by the admin or the staff member
					$wpticketultra->messaging->send_ticket_front_reply_client($ticket_id, $reply, $client);
				
				}
				
			
			}elseif( ($notificaton_rule==3 )  ){
				
				//NOTIFY ONLY CLIENT AND TICKET OWNER				
				if(!$is_client)
				{								
					//notify client only if the reply has been posted by the staff member
					$wpticketultra->messaging->send_ticket_front_reply_client($ticket_id, $reply, $client);
				
				}else{
					
					$staff = get_user_by( 'id', $ticket->ticket_staff_id );					
					//send email to staff members
					$wpticketultra->messaging->send_ticket_reply_message_staff($ticket_id, $staff, $reply);					
				
				}
				
							
			
			}		
			
		
		} //end if private
		
	
	}	
	
	
	//this is the custom redirecton after ticket submission sucess
	public function handle_redir_success($key, $ticket_id)
	{
		global $wpticketultra, $wptucomplement, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		
		$url = '';
		$my_success_url = '';	
		
		$url_info = array_merge($_GET, $_POST, $_COOKIE);
		
		if(isset($url_info['redirect_to']) && $url_info['redirect_to']!='')
		{
			
			$my_success_url = $url_info['redirect_to'];	
			
		//	echo "URL REDIR "  .$my_success_url; 	
		//	exit;	
		}
		
		if(is_user_logged_in())
		{
			
			$account_page_id = $wpticketultra->get_option('bup_my_account_page');				
			$my_account_url = get_page_link($account_page_id);
					 			 
			 //get current user info
			 $current_user = $wpticketultra->userpanel->get_user_info();
			 $user_id = $current_user->ID;				 			 
			 $is_client  = $wpticketultra->profile->is_client($user_id);
			 
			 
			 if(is_admin() ) //check if this is admin and if it's within the WP admin dashboard.
			 {
				 $url = '?page=wpticketultra&tab=ticketedit&id='.$ticket_id.'&wptu_status=ok&wptu_ticket_key='.$key;
				 
			 }else{
				 
				 
				 $url = $my_account_url.'?module=see&id='.$ticket_id.'&wptu_status=ok&wptu_ticket_key='.$key;
				
				 
			 }
			 
			 
		}else{ //user is not logged in which means the ticket is ceated on public site
				
			if($my_success_url=="")
			{
				$url = $_SERVER['REQUEST_URI'].'?wptu_status=ok&cus=0&wptu_ticket_key='.$key;
					
			}else{
									
				$url = $my_success_url.'?wptu_status=ok&cus=1&wptu_ticket_key='.$key;				
					
			}
		
		}	
		 		  
		wp_redirect( $url );
		exit;
		  
		 
	}
	
	
	public function get_unique_verify_account_id()
	{
		  session_start();
		  $rand = $this->genRandomStringActivation(8);
		  $key = session_id()."_".time()."_".$rand;
		  
		  return $key;
		  
		 
	  }
	
	
	
	/*Get errors display*/
	function get_errors() {
		global $wpticketultra;
		$display = null;
		if (isset($this->errors) && count($this->errors)>0) 
		{
		$display .= '<div class="wptu-errors">';
			foreach($this->errors as $newError) {
				
				$display .= '<span class="wptu-error "><i class="wptu-icon-remove"></i>'.$newError.'</span>';
			
			}
		$display .= '</div>';
		} else {
		
			$this->registered = 1;
			
			$uultra_settings = get_option('wptu_options');

            // Display custom registraion message
            if (isset($uultra_settings['msg_register_success']) && !empty($uultra_settings['msg_register_success']))
			{
                $display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>' . remove_script_tags($uultra_settings['msg_register_success']) . '</span></div>';
            
			}else{
				
                $display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Your request has been sent successfully. Please check your email.','wp-ticket-ultra').'</span></div>';
            }

            // Add text/HTML setting to be displayed after registration message
            if (isset($uultra_settings['html_register_success_after']) && !empty($uultra_settings['html_register_success_after'])) 
			
			{
                $display .= '<div class="wptu-ultra-success-html">' . remove_script_tags($uultra_settings['html_register_success_after']) . '</div>';
            }
			
			
			
			if (isset($_POST['redirect_to'])) {
				wp_redirect( $_POST['redirect_to'] );
			}
			
		}
		return $display;
	}

}

$key = "register";
$this->{$key} = new WPTicketUltraRegister();