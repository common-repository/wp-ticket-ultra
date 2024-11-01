<?php
class WPTicketUltraUser
{
	var $ajax_prefix = 'wptu';
	var $table_prefix = 'wptu';

	
	function __construct() 
	{
				
		$this->ini_module();
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_get_new_staff', array( &$this, 'get_new_staff' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_get_staff_details_ajax', array( &$this, 'get_staff_details_ajax' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_staff_services', array( &$this, 'update_staff_services' ));
		
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_add_staff_confirm', array( &$this, 'add_staff_confirm' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_add_client_confirm', array( &$this, 'ubp_add_client_confirm' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_autocomple_clients_tesearch', array( &$this, 'get_users_auto_complete' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_get_staff_list_admin_ajax', array( &$this, 'get_staff_list_admin_ajax' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_get_staff_details_admin', array( &$this, 'get_staff_details_admin_ajax' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_staff_admin', array( &$this, 'update_staff_admin' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_delete_staff_admin', array( &$this, 'bup_delete_staff_admin' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_ajax_upload_avatar', array( &$this, 'ajax_upload_avatar' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_crop_avatar_user_profile_image', array( &$this, 'crop_avatar_user_profile_image' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_delete_user_avatar', array( &$this, 'delete_user_avatar' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_owner_get_form', array( &$this, 'update_owner_get_form' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_owner_get_actions', array( &$this, 'update_owner_get_actions' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_owner_confirm', array( &$this, 'update_owner_confirm' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_ticket_status_get_actions', array( &$this, 'ticket_status_get_actions' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_ticket_status_confirm', array( &$this, 'update_ticket_status_confirm' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_ticket_priority_get_actions', array( &$this, 'ticket_priority_get_actions' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_ticket_priority_confirm', array( &$this, 'update_ticket_priority_confirm' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_update_user_account_settings', array( &$this, 'update_user_account_settings' ));
		
		
		
		
		

	}
	
	public function ini_module()
	{
		global $wpdb;
		
		   
		
	}
	
	public function get_user_info()
	{
		$current_user = wp_get_current_user();
		return $current_user;

		
	}
	
	public function ticket_priority_get_actions()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		$ticket_id = $_POST['ticket_id'];
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$staff_id = $ticket->ticket_staff_id;
		
		
				
			
		$html .='<div class="wptu-staff-list-change-owner">';
		$html .='<p>'.__('This screen allows you to change the priority of this ticket. Just select the new priority and click on the submit button. ','wp-ticket-ultra').'</p>';
		
		$html .='<h1>'.__('New Priority:','wp-ticket-ultra').'</h1>';
		
		$html .='<div class="wptu-ticket-action-details-choices">	'; //priorities
		
		$html .= $wpticketultra->priority->get_all_list_box($ticket->ticket_priority);	
		$html .='</div>';
		$html .='<input type="hidden" name="ticket_id" id="ticket_id" value="'.$ticket_id.'">	';
		
		$html .='</div>';
		
		echo $html;
		die();
	
	}
	
	
	
	public function ticket_status_get_actions()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		$ticket_id = $_POST['ticket_id'];
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$staff_id = $ticket->ticket_staff_id;
		
		$html .='<div class="wptu-staff-list-change-owner">';
		$html .='<p>'.__('This screen allows you to change the status of this ticket. Just select the new status and click on the submit button. ','wp-ticket-ultra').'</p>';
		
		$html .='<h1>'.__('New Status:','wp-ticket-ultra').'</h1>';
		
		$html .='<div class="wptu-ticket-action-details-choices">	'; //status
		
		$html .= $wpticketultra->status->get_all_statuses_list_box($ticket->ticket_status);	
		$html .='</div>';
		
		$html .='<div class="wptu-ticket-action-details-choices">	';
				
				$html .='<ul>';			
								
					$html .='<li><input class="" title="'.__('Change Status','wp-ticket-ultra').'" name="wptu-change-ticket-status" id="wptu-change-ticket-status" value="1" type="checkbox" checked="checked" ><label for="wptu-change-ticket-status">'.__("Yes, notify user about this change.",'wp-ticket-ultra').'</label>'.'</li>';
				
				$html .='</ul>';
				
				$html .='<input type="hidden" name="ticket_id" id="ticket_id" value="'.$ticket_id.'">	';
			
			$html .='</div>	';
		
		
		$html .='</div>';
		
		
		
		
		echo $html;
		die();
	
	}
	
	public function update_ticket_priority_confirm()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		$ticket_id = $_POST['ticket_id'];
		$ticket_priority = $_POST['ticket_priority'];		
		$notify_user = $_POST['notify_user'];
		
		$wpticketultra->ticket->update_ticket_priority ($ticket_id,$ticket_priority);		
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$ticket_priority = $wpticketultra->priority->get_one($ticket->ticket_priority);
		
		$user = get_user_by( 'id', $ticket->ticket_user_id );
		
		if($notify_user=='1')
		{
			//notify user
			//$wpticketultra->messaging->send_status_change($user, $ticket);		
		}
		
		$response = array('content' => $ticket_priority->priority_name);	
		echo json_encode($response) ;	
			
		die();
	
	}
	
	public function update_ticket_status_confirm()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		$ticket_id = $_POST['ticket_id'];
		$ticket_status = $_POST['ticket_status'];		
		$notify_user = $_POST['notify_user'];
		
		$wpticketultra->ticket->update_ticket_status ($ticket_id,$ticket_status);		
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$ticket_status = $wpticketultra->status->get_one($ticket->ticket_status);
		
		$user = get_user_by( 'id', $ticket->ticket_user_id );
		
		if($notify_user=='1')
		{
			//notify user
			$wpticketultra->messaging->send_status_change($user, $ticket);		
		}
		
		$response = array('content' => $ticket_status->status_name, 'color' => $ticket_status->status_color);	
		echo json_encode($response) ;	
			
		die();
	
	}
	
	public function update_owner_get_form()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		$ticket_id = $_POST['ticket_id'];
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$staff_id = $ticket->ticket_staff_id;
		
		
		$display = true;
		
		if(!isset($wptucomplement))
		{
									
			$display = false;
						
		}				
		
		
		$html='';
		$uultra_combined_search = '';
		
		$relation = "AND";
		$args= array('keyword' => $uultra_combined_search ,  'relation' => $relation,  'sortby' => 'ID', 'order' => 'DESC');
		$users = $wpticketultra->userpanel->get_staff_filtered($args);
		
		$total = $users['total'];
		
		if (empty($users['users']))
		{
			$total = 0;		
		
		}
		
		if($display)
		{		
				
			$html .='<div class="wptu-staff-list-change-owner">';
			$html .='<p>'.__('Select the new owner of this ticket.  ','wp-ticket-ultra').'</p>';
			
			$html .='<h1>'.__('Staff','wp-ticket-ultra').'('.$total.')</h1>';
	
			
			
			if (!empty($users['users']))
			{
				$html .='<ul>';
				$c_c =0;
				
				foreach($users['users'] as $user) {
					
					$user_id = $user->ID;
					
					if($staff_id==$user_id){continue;}
					
					$c_c++;
					
					if($c_c==1){$html .='<input type="hidden" id="wptu-first-staff-id" value="'.$user_id.'">';}
				
					$html .='<li>';
					$html .='<a href="#" id="wptu-owner-actions-load" class="wptu-owner-actions-load" staff-id="'.$user_id.'" ticket-id="'.$ticket_id.'"> ';
					
					$html .= $this->get_user_pic( $user_id, 50, 'avatar', null, null, false);
					$html .='<h3>'.$user->display_name.'</h3>';
					$html .='</a>';
					
					$html .='</li>';
					$html .='<div class="wptu-change-owner-actions" id="wptu-change-actions-id-'.$user_id.'"></div>';
					
				}
				
				$html .='</ul>';
			
			}else{
				
				$html .=__('There are no staff members.','wp-ticket-ultra');
				
			
			}
			
			$html .='</div>';
		
		
		}else{
			
			$html .= __( "If you need to manage more than one staff member, please consider upgrading your plugin. The lite version allows you to manage only one staff member. ", 'wp-ticket-ultra' ).'<a href="https://wpticketultra.com/compare-packages.html" target="_blank">Click here</a> to upgrade your plugin.';
			
		}			
		
		echo $html;
		die();
	
	}
	
	public function update_owner_confirm()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		$ticket_id = $_POST['ticket_id'];
		$staff_id = $_POST['staff_id'];
		
		
		$add_depto = $_POST['add_depto'];
		$share_ticket = $_POST['share_ticket'];
		
		$wpticketultra->ticket->update_ticket_owner ($ticket_id,$staff_id);
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$ticket_department = $ticket->department_id;
		
		$staff = get_user_by( 'id', $staff_id );		
		
		// Do we have to add this staff to the depto		
		if($add_depto=='1')
		{
			$this->delete_staff_from_depto($staff_id, $ticket_department);
			$this->ubp_assign_staff_deptos($staff_id, $ticket_department);		
		}
		
		// Do we have to invite this staff to participate on this ticket 	
		if($share_ticket=='1')
		{
			$this->unshared_ticket($staff_id, $ticket_id);
			$this->share_ticket_with_staff($staff_id, $ticket_id);		
		}
		
		
		//notify new owner
		$wpticketultra->messaging->send_owner_change($staff, $ticket);	
		
		echo $staff->display_name;
		die();
	
	}
	
	public function unshared_ticket($staff_id, $ticket_id)
	{
		global $wpdb;
		
		$sql = 'DELETE FROM ' . $wpdb->prefix .$this->table_prefix. '_tickets_permissions  WHERE perm_staff_id="'.(int)$staff_id.'" AND perm_ticket_id="'.(int)$ticket_id.'"';
		$wpdb->query($sql);		
	}
		
	public function share_ticket_with_staff($staff_id, $ticket_id)
	{
		global $wpdb;
		
		
		$new_record = array(
						'perm_id'        => NULL,
						'perm_ticket_id' => $ticket_id,
						'perm_staff_id' => $staff_id					
						
						
						
					);
					
		$wpdb->insert( $wpdb->prefix .$this->table_prefix. '_tickets_permissions', $new_record, array( '%d', '%s', '%s'));
						
	}
	
	public function update_owner_get_actions()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		$ticket_id = $_POST['ticket_id'];
		$staff_id = $_POST['staff_id'];
		
		
		$ticket = $wpticketultra->ticket->get_one($ticket_id);
		$ticket_department = $ticket->department_id;
		
		//is staff within this department?	
		
		$departments_ids = $this->get_all_staff_allowed_deptos_list($staff_id);
		$deptos_array = explode(',',$departments_ids);
		
		if(in_array($ticket_department,$deptos_array)) //the staff is within this depto
		{
			//display options
			
			$html .='<p><i class="fa fa-info-circle red"></i> '.__('The Ticket # ','wp-ticket-ultra').$ticket_id.__(' will be assigned to the selected staff member. ','wp-ticket-ultra').'</p>';
			
						
			$html .='<div class="wptu-ticket-action-details">		          
         <button name="wptu-ticket-owner-conf-btn" id="wptu-ticket-owner-conf-btn" class="wptu-confirm-ownerchange-btn" ticket-id="'.$ticket_id .'"  staff-id="'.$staff_id .'"><i class="fa fa-check"></i> '.__("CONFIRM",'wp-ticket-ultra').' </button> <button name="wptu-ticket-update-details-btn" id="wptu-ticket-update-details-btn" class="wptu-confirm-ownerchange-btn"><i class="fa fa-times"></i> '.__("CANCEL",'wp-ticket-ultra').' </button>     
          </div>';
			
			
		
		}else{ //the staff is not within the department, we need to display choices
		
			$html .= '<div class="wptu-ultra-warning"><span><i class="fa fa-check"></i>'.__("This staff member member doesn't belong to the ticket's department. ",'wp-ticket-ultra').'</span></div>';
			
			$html .='<div class="wptu-ticket-action-details-choices">	';
				
				$html .='<ul>';
				
					$html .='<li><input class="" title="Multi Select Checkbox" name="wptu-change-owner-act-add-to-depto" id="wptu-change-owner-act-add-to-depto" value="1" type="checkbox"><label for="wptu-change-owner-act-add-to-depto">'.__("Add Staff member to the ticket's deparment?",'wp-ticket-ultra').'</label>'.'</li>';
					
					$html .='<li><input class="" title="Multi Select Checkbox" name="wptu-change-owner-act-share-ticket" id="wptu-change-owner-act-share-ticket" value="1" type="checkbox"><label for="wptu-change-owner-act-share-ticket">'.__("Share this ticket with the the selected staff member?",'wp-ticket-ultra').'</label>'.'</li>';
				
				$html .='</ul>';
			
			$html .='</div>	';
			
			
			$html .='<div class="wptu-ticket-action-details">		          
         <button name="wptu-ticket-owner-conf-btn" id="wptu-ticket-owner-conf-btn" class="wptu-confirm-ownerchange-btn" ticket-id="'.$ticket_id .'"  staff-id="'.$staff_id .'"><i class="fa fa-check"></i> '.__("CONFIRM",'wp-ticket-ultra').' </button> <button name="wptu-ticket-update-details-btn" id="wptu-ticket-update-details-btn" class="wptu-confirm-ownerchange-btn"><i class="fa fa-times"></i> '.__("CANCEL",'wp-ticket-ultra').' </button>     
          </div>';
		
		
		}
			
		
		
		echo $html;
		die();
	
	}
	
	public function get_new_staff()
	{	
	
		global $wpdb, $wpticketultra, $wptucomplement;	
		
		
		$display = true;	
				
		if(!isset($wptucomplement))
		{
			//check for amount of staff members
			$total = $this->get_staff_members_total();				
			if($total!=0)
			{					
				$display = false;
			}			
		}
		
		
		$html = '';
		
		$html .= '<div class="wptu-sect-adm-edit">';
		
		if($display)
		{
			$html .= '<p>'.__('Here you can add new staff members. Please feel in with the full name and email then click on the Add button.','wp-ticket-ultra').'</p>';
		
		}
		
		$html .= '<div class="wptu-edit-service-block">';
		
		
		
		if($display){
			
				$html .= '<div class="wptu-field-separator"><label for="wptu-box-title">'.__('Full Name','wp-ticket-ultra').':</label><input type="text" name="staff_name" id="staff_name" class="wptu-common-textfields" /></div>';				
				
				$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('Email','wp-ticket-ultra').':</label><input type="text" name="staff_email" id="staff_email" class="wptu-common-textfields" /></div>';					
				$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('Username','wp-ticket-ultra').':</label><input type="text" name="staff_nick" id="staff_nick" class="wptu-common-textfields" /></div>';			
			
				$html .= '<div class="wptu-field-separator" id="wptu-err-message"></div>';	
		}else{
			
			$html .= __( "If you need to add more than one staff member, please consider upgrading your plugin. The lite version allows you to have only one Staff Member. ", 'wp-ticket-ultra' ).'<a href="https://wpticketultra.com/compare-packages.html" target="_blank">Click here</a> to upgrade your plugin.';
			
		}
			
			
			$html .= '</div>';
		
		$html .= '</div>';
		
		
			
		echo $html ;		
		die();		
	
	}
	
	function get_staff_members_total()
	{
		global $bookingultrapro;
		$relation = "AND";
		$args= array('keyword' => $uultra_combined_search ,  'relation' => $relation,  'sortby' => 'ID', 'order' => 'DESC');
		$users = $this->get_staff_filtered($args);
		
		$total = $users['total'];
		if(!isset($users['total'])){$total=0;}
		
		return $total;
	}
	
	public function get_staff_details_ajax()
	{
		session_start();
		$staff_id = $_POST['staff_id']	;
		
		$_SESSION["current_staff_id"] =$staff_id ;		
		echo $this->get_staff_details($staff_id);
		die();
	
	}
	
	
	
	public function update_staff_services()
	{
		$staff_id = $_POST['staff_id']	;
		
		$service_list = array();
		$modules = $_POST["service_list"]; 		
		
		//delete all services from this staff member
		if($staff_id!='')
		{
			$this->delete_staff_deptos($staff_id);
		}
		
		if($modules!="" && $staff_id!='')
		{
			$modules =rtrim($modules,"|");
			$service_list = explode("|", $modules);
			
						
			foreach($service_list as  $service)
			{
				$details = explode("-", $service);
				
			
				$service_id = $details[0];
				$service_price= $details[1];
				$service_qty= $details[2];
				
				//add in db				
				$this->ubp_assign_staff_deptos($staff_id, $service_id);
			
			
			}
			
									
		}
		
		
		
		
		die();
	
	}
	
	function get_me_wphtml_editor($meta, $content)
	{
		// Turn on the output buffer
		ob_start();
		
		$editor_id = $meta;				
		$editor_settings = array('media_buttons' => false , 'textarea_rows' => 15 , 'teeny' =>true); 
							
					
		wp_editor( $content, $editor_id , $editor_settings);
		
		// Store the contents of the buffer in a variable
		$editor_contents = ob_get_clean();
		
		// Return the content you want to the calling function
		return $editor_contents;

	
	
	}
	
	
	
	public function delete_staff_deptos($staff_id)
	{
		global $wpdb;
		
		$sql = 'DELETE FROM ' . $wpdb->prefix .$this->table_prefix. '_department_staff  WHERE depto_staff_id="'.(int)$staff_id.'" ';
		$wpdb->query($sql);		
	}
	
	public function delete_staff_from_depto($staff_id, $depto_id)
	{
		global $wpdb;
		
		$sql = 'DELETE FROM ' . $wpdb->prefix .$this->table_prefix. '_department_staff  WHERE depto_staff_id="'.(int)$staff_id.'" AND depto_department_id="'.(int)$depto_id.'"';
		$wpdb->query($sql);		
	}
		
	public function ubp_assign_staff_deptos($staff_id, $service_id)
	{
		global $wpdb;
		
		
		$new_record = array(
						'depto_id'        => NULL,
						'depto_staff_id' => $staff_id,
						'depto_department_id' => $service_id					
						
						
						
					);
					
		$wpdb->insert( $wpdb->prefix .$this->table_prefix. '_department_staff', $new_record, array( '%d', '%s', '%s'));
						
	}
	
	
	
	public function add_staff_confirm()
	{
		global $blog_id;
		$staff_name = $_POST['staff_name']	;
		$email = $_POST['staff_email'];
		$user_name = $_POST['staff_nick'];
		$convert = $_POST['bup_create_auto'];		
		$user_pass = wp_generate_password( 12, false);		
		
		/* Create account, update user meta */
		$sanitized_user_login = sanitize_user($user_name);
		
		if(email_exists($email))
		{			
			
			//$error .=__('<strong>ERROR:</strong> This email is already registered. Please choose another one.','wp-ticket-ultra');
		
		}elseif(username_exists($user_name)){
			
			$error .=__('<strong>ERROR:</strong> This username is already registered. Please choose another one.','wp-ticket-ultra');
		
		}elseif($staff_name=='' || $email=='' || $user_name==''){
			
			$error .=__('<strong>ERROR:</strong> All fields are mandatory.','wp-ticket-ultra');		
		
		}
		
		if($error=='')
		{
			
			if(email_exists($email))
			{
				
				/* We Update Already user */
				$user = get_user_by( 'email', $email );
				$user_id = $user->ID;
				update_user_meta ($user_id, 'wptu_is_staff_member',1);
				
				
				//check multisite				
				if ( is_multisite() ) 
				{					
					if ($user_id && !is_user_member_of_blog($user_id, $blog_id)) 
					{
						//Exist's but is not user to the current blog id
						$result = add_user_to_blog( $blog_id, $user_id, 'subscriber');

   					 }
		
		
				} 
				
			
			}else{
				
				/* We create the New user */
				$user_id = wp_create_user( $sanitized_user_login, $user_pass, $email);
				
				if($user_id)
				{
					update_user_meta ($user_id, 'wptu_is_staff_member',1);
					wp_update_user( array('ID' => $user_id, 'display_name' => esc_attr($staff_name)) );
					
									
				}
				
			
			}
			
			//set role					
			$u = new WP_User( $user_id );
			$u->add_role( 'wptu_staff' );			
			update_user_meta($user_id, 'wptu_staff_role', 'wptu_staff');	
				
			
			
			echo $user_id;		
		
		}else{
			
			echo $error;		
		
		}
		
			
		
		die();
	
	}
	
	public function ubp_add_client_confirm()
	{
		$user_id = '';
		$client_name = $_POST['client_name']	;
		$client_last_name = $_POST['client_last_name'];
		$email = $_POST['client_email'];
		
		$user_name = strtolower($client_name.$this->genRandomString());		
		
		$user_pass = wp_generate_password( 12, false);		
		
		/* Create account, update user meta */
		$sanitized_user_login = sanitize_user($user_name);
		
		if(email_exists($email))
		{			
			
					
			$error .= '<div class="wptu-ultra-error"><span><i class="fa fa-check"></i>'.__('This email is already registered. Please choose another one.','wp-ticket-ultra').'</span></div>';
		
		}elseif(username_exists($user_name)){
			
			
			$error .= '<div class="wptu-ultra-error"><span><i class="fa fa-check"></i>'.__('This username is already registered. Please choose another one.','wp-ticket-ultra').'</span></div>';
		
		}elseif($client_name=='' || $email=='' || $client_last_name==''){
					
			$error .= '<div class="wptu-ultra-error"><span><i class="fa fa-check"></i>'.__('All fields are mandatory.','wp-ticket-ultra').'</span></div>';		
		
		}
		
		if($error=='')
		{			
			/* We create the New user */
			$user_id = wp_create_user( $sanitized_user_login, $user_pass, $email);
			
			if($user_id)
			{
				$display_name =$client_name.' '.$client_last_name ;
				$respon = $display_name.' ('.$email.')';
				wp_update_user( array('ID' => $user_id, 'display_name' => esc_attr($display_name)) );
				
				$user = new WP_User( $user_id );
				$user->set_role( 'wptu_user' );
			
			}
			
			$response = array('response' => 'OK', 'content' => $respon, 'user_id' => $user_id);	
		
		}else{
			
			$response = array('response' => 'ERROR', 'content' => $error, 'user_id' => $user_id);	
		
		}
		
		
		
		echo json_encode($response) ;
		
			
		
		die();
	
	}
	
	public function update_staff_admin()
	{
		$staff_id = $_POST['staff_id']	;
		$staff_name = $_POST['display_name']	;
		$reg_telephone = $_POST['reg_telephone'];
		
		$email = $_POST['reg_email'];
		$email2 = $_POST['reg_email2'];
		
		
		if($email=='')
		{
			$error .=__('<strong>ERROR:</strong> Please input an email address.','wp-ticket-ultra');			
		
				
		}elseif($staff_name==''){
			
			$error .=__('<strong>ERROR:</strong> Pleaes input a Full Name.','wp-ticket-ultra');		
		
		}
		
		if($email!=$email2)
		{
			if(email_exists($email))
			{
				$error .=__('<strong>ERROR:</strong> This email is already registered. Please choose another one.','wp-ticket-ultra');
			
			}else{
				
				wp_update_user( array('ID' => $staff_id, 'user_email' => esc_attr($email)) );
				
			}
		
		}	
		
		if($error=='')
		{			
						
			if($staff_id)
			{
				update_user_meta ($staff_id, 'reg_telephone',$reg_telephone);
				update_user_meta ($staff_id, 'display_name',$staff_name);
				wp_update_user( array('ID' => $staff_id, 'display_name' => esc_attr($staff_name)) );
				
				
			
			}
			
			echo __('<strong>Done!</strong>','wp-ticket-ultra');			;		
		
		}else{
			
			echo $error;		
		
		}
		
			
		
		die();
	
	}
	
	public function bup_delete_staff_admin()
	{
		global $wpdb,  $bookingultrapro;
		
		$html = '';		
		
		//close
		$current_user = $_POST["staff_id"];
		
		if(!is_super_admin( $current_user ))
		{
			//delete meta data		
			$sql = 'DELETE FROM ' . $wpdb->prefix . 'usermeta WHERE user_id = "'.$current_user.'" ' ;			
			$wpdb->query( $sql );
			
			//delete availability
			$sql = 'DELETE FROM ' . $wpdb->prefix . 'bup_staff_availability WHERE avail_staff_id = "'.$current_user.'" ' ;			
			$wpdb->query( $sql );
						
			//delete breaks
			$sql = 'DELETE FROM ' . $wpdb->prefix . 'bup_staff_availability_breaks WHERE break_staff_id = "'.$current_user.'" ' ;			
			$wpdb->query( $sql );
			
			//delete rates
			$sql = 'DELETE FROM ' . $wpdb->prefix . 'bup_service_rates WHERE rate_staff_id = "'.$current_user.'" ' ;			
			$wpdb->query( $sql );
			
			//delete user					
			wp_delete_user( $current_user );		
				
			$html  = $this->get_first_staff_on_list();
		}else{
			
			delete_user_meta ($current_user, 'wptu_is_staff_member'	);	
			$html  = $this->get_first_staff_on_list();	
			
				
			
		}
		echo $html;
		die();		
			
	}
	
	//this returns the staff permissions and settings
	function get_staff_backend_setting_dropdown($settings, $setting_id)
	{
		$html ='';
		
		if(isset($settings[$setting_id]) && $settings[$setting_id]=='NO')
		{
			$selected_yes = '';
			$selected_no = 'selected="selected"';
			
		}else{
			
			$selected_yes = 'selected="selected"';
			$selected_no = '';
			
		}
		
		
		
		$html .= '<select name="'.$setting_id.'" size="1" id="'.$setting_id.'">
  					<option '.$selected_yes.' value="YES">'.__('YES','wp-ticket-ultra').'</option>
					<option '.$selected_no.' value="NO">'.__('NO','wp-ticket-ultra').'</option>
				</select>';
		
		return $html;
		
	}
	
	
	
	//this returns the staff permissions and settings
	function get_staff_backend_settings( $staff_id)
	{
		global $wpdb, $wpticketultra, $wptu_bugtracker, $wptu_roles, $wptu_wooco;
		
		$settings = array();
		$settings = get_user_meta( $staff_id, 'wptu_staff_acc_setting', true ); 
		
		if(!is_array($settings)){$settings== array();}
		
		$html ='';
		
				
		$html .='<div class="wptu-profile-field" >';		
		$html .='<label class="wptu-field-type" for="display_name"><span>'.__('Backend Access?','wp-ticket-ultra').'</span></label>';
		$html .='<div class="wptu-field-value" >'.$this->get_staff_backend_setting_dropdown($settings, "wptu_per_backend_access").'</div>';		
		$html .= '</div>';
		
		
		$html .='<div class="wptu-profile-field" >';		
		$html .='<label class="wptu-field-type" for="display_name"><span>'.__('Bugs & Issues Access?','wp-ticket-ultra').'</span></label>';
		$html .='<div class="wptu-field-value" >'.$this->get_staff_backend_setting_dropdown($settings, "wptu_per_bugs_access").'</div>';		
		$html .= '</div>';
		
		if(isset($wptu_wooco))
		{
		
			$html .='<div class="wptu-profile-field" >';		
			$html .='<label class="wptu-field-type" for="display_name"><span>'.__('WooCommerce Orders?','wp-ticket-ultra').'</span></label>';
			$html .='<div class="wptu-field-value" >'.$this->get_staff_backend_setting_dropdown($settings, "wptu_woo_orders_access").'</div>';		
			$html .= '</div>';
		
		}
		
		
		$html .='<div class="wptu-profile-field" >';		
		$html .='<label class="wptu-field-type" for="display_name"><span>'.__('Can Update Picture?','wp-ticket-ultra').'</span></label>';	
		
		$html .='<div class="wptu-field-value" >'.$this->get_staff_backend_setting_dropdown($settings, "wptu_upload_picture").'</div>';		
		$html .= '</div>';
		
		if(isset($wptu_roles))
		{
			
			$user_role = get_user_meta( $staff_id, 'wptu_staff_role', true );		
			$html .='<div class="wptu-profile-field" >';		
			$html .='<label class="wptu-field-type" for="display_name"><span>'.__('Role','wp-ticket-ultra').'</span></label>';
			$html .='<div class="wptu-field-value" >'.$wptu_roles->get_all_list_box($user_role).'</div>';		
			$html .= '</div>';	
		
		}
			
				
				
		$html .=' <p class="submit">
	<button name="wptu-save-acc-settings-staff" id="wptu-save-acc-settings-staff" class="bup-button-submit-changes" ubp-staff-id= "'.$staff_id.'">'.__('Save Settings','wp-ticket-ultra').'	</button>&nbsp; <span id="wptu-loading-animation-acc-setting-staff">  <img src="'.wptu_url.'admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; '.__('Please wait ...','wp-ticket-ultra').' </span>
	
	</p>';
	
		$html .= '<p><i class="fa fa-info-circle"></i> '.__('You can use the following button to send a password reset link to this staff member. The reset will allow the staff meber to login and manage their appointments','wp-ticket-ultra').'</p>';
			
		$html .=' <p class="submit">
		<button name="wptu-save-acc-send-reset-link-staff" id="wptu-save-acc-send-reset-link-staff" class="wptu-button-submit-changes" wptu-staff-id= "'.$staff_id.'"><i class="fa fa-refresh "></i> '.__('Send Password Reset Link','wp-ticket-ultra').'	</button>&nbsp; <span id="wptu-loading-animation-acc-resetlink-staff">  <img src="'.wptu_url.'admin/images/loaderB16.gif" width="16" height="16" /> &nbsp; '.__('Please wait ...','wp-ticket-ultra').' </span> <p id="wptu-acc-resetlink-staff-message">   </p>
		
		</p>';
		
		$reset_link_page = $wpticketultra->get_option("bup_password_reset_page");
		
		if($reset_link_page=='')
		{	
			$html .= '<p class="wptu-backend-info-tool">'.'<i class="fa fa-info-circle"></i> <strong>'.__("You haven't set a password reset page, this is very imporant. Click on Staff & Client Account link and set a page for the reset password shortcode. ",'wp-ticket-ultra').'</strong>'.'</p>';
		
		}
		
				
		return $html;
		
	}
	
	public function has_account_permision($staff_id, $setting_id)
	{
		global $wpdb, $wpticketultra;
		
		$settings = array();
		$settings = get_user_meta( $staff_id, 'wptu_staff_acc_setting', true ); 
		
		if(!is_array($settings)){$settings== array();}
		
		if(isset($settings[$setting_id]) && $settings[$setting_id]=='YES')
		{
			return true;
			
		}else{
			
			return false;
			
		}		
		
	}
	
	
	
	public function update_user_account_settings()
	{
		global $wpdb, $wpticketultra;
		
		
		$wptu_per_backend_access = $_POST['wptu_per_backend_access'];		
		$wptu_upload_picture = $_POST['wptu_upload_picture'];
		
		
		$staff_id = $_POST['staff_id'];
		$role = $_POST['role'];
		$bugs = $_POST['bugs'];
		$woo_orders = $_POST['woo_orders'];
		
		$settings = array('wptu_per_backend_access' =>$wptu_per_backend_access, 
						  'wptu_upload_picture' =>$wptu_upload_picture,
						  'wptu_per_bugs_access' =>$bugs,
						  'wptu_woo_orders_access' =>$woo_orders
						  );		
		update_user_meta($staff_id, 'wptu_staff_acc_setting', $settings);
		
		//update role
		
		if($role!='')
		{			
			// NOTE: Of course change 3 to the appropriate user ID
			$u = new WP_User( $staff_id );
			
			$current_role = get_user_meta( $staff_id, 'wptu_staff_role', true );
			
			if($current_role!='')
			{
				// Remove role
				$u->remove_role( $current_role );			
			}
			
			// Add role
			$u->add_role( $role );
			
			update_user_meta($staff_id, 'wptu_staff_role', $role);		
		
		}
		
		
		
		die();
	
	
	}
		
	public function get_staff_details($staff_id)
	{
		global  $wpticketultra, $wptucomplement, $wptuultimate, $wptu_reply_signature, $wptu_bugtracker;
		
		
		$html = '';
		
		$html .= '<div class="wptu-sect-adm-edit">';
		$html .= '<input type="hidden" value="'.$staff_id.'" id="staff_id" name="staff_id">';
		
		$html .= '<ul class="wptu-details-staff-sections">';
		
		$html .='<li class="left_widget_customizer_li">';
			
		$html .='<div class="wptu-staff-details-header" widget-id="1"><h3> '.__('Details','wp-ticket-ultra').'<h3>';
				
		$html .='<span class="wptu-widgets-icon-close-open" id="wptu-widgets-icon-close-open-id-1"  widget-id="1" style="background-position: 0px 0px;"></span>';
		
		$html .= '</div>';
		
		$html .='<div id="wptu-widget-adm-cont-id-1" class="wptu-staff-details">';
		$html .='<span class="wptu-action-staff"><a href="#" id="wptu-staff-member-delete"  title="'.__('Delete','wp-ticket-ultra').'" staff-id="'.$staff_id.'" ><i class="fa fa-trash-o"></i></a> </span>';
		
		$html .= $this->get_staff_personal_details($staff_id);
		$html .= '</div>';
		$html .='</li>';
		
		//account and backend		
		$html .='<li class="left_widget_customizer_li">';
		$html .='<div class="wptu-staff-details-header"  widget-id="8"><h3> '.__('Account & Backend','wp-ticket-ultra').'<h3>';
		
		$html .='<span class="wptu-widgets-icon-close-open" id="wptu-widgets-icon-close-open-id-8"  widget-id="8" style="background-position: 0px 0px;"></span>';
		
		$html .= '</div>';
		
		$html .='<div id="wptu-widget-adm-cont-id-8" class="wptu-staff-details" style=" display:none">';
		$html .=  $this->get_staff_backend_settings($staff_id);
		$html .= '</div>';
		$html .='</li>';
		
		
		$html .='<li class="left_widget_customizer_li">';
		$html .='<div class="wptu-staff-details-header" widget-id="2" ><h3> '.__('Products & Departments','wp-ticket-ultra').'<h3>';
		
		$html .='<span class="wptu-widgets-icon-close-open" id="wptu-widgets-icon-close-open-id-2"  widget-id="2" style="background-position: 0px 0px;"></span>';
		
		$html .= '</div>';
				
		$html .='<div id="wptu-widget-adm-cont-id-2" class="wptu-tabs-sections-staff-services wptu-services-list-adm" style=" display:none">';
		$html .= $this->get_staff_department_admin($staff_id);
		$html .= '</div>';
		$html .='</li>';
		
		//bugs and components	
		
		$html .='<li class="left_widget_customizer_li">';
		$html .='<div class="wptu-staff-details-header" widget-id="222" ><h3> '.__('Bugs & Issues Permissions','wp-ticket-ultra').'<h3>';
		
		$html .='<span class="wptu-widgets-icon-close-open" id="wptu-widgets-icon-close-open-id-222"  widget-id="222" style="background-position: 0px 0px;"></span>';
		
		$html .= '</div>';
				
		$html .='<div id="wptu-widget-adm-cont-id-222" class="wptu-tabs-sections-staff-services wptu-services-list-adm" style=" display:none">';
		
		if(isset($wptucomplement) && isset($wptu_bugtracker))
		{
		
			$html .= $this->get_staff_bugs_products_admin($staff_id);
		
		}else{
				
			$html .= __('Please consider upgrading your plugin if you need to manage bugs & issues.','wp-ticket-ultra');
				
		}
		
		
		$html .= '</div>';
		$html .='</li>';
		
		
		
		/*$html .='<li class="left_widget_customizer_li">';
		$html .='<div class="wptu-staff-details-header" widget-id="66" ><h3> '.__('Reply Signatures','wp-ticket-ultra').'<h3>';
		
		$html .='<span class="wptu-widgets-icon-close-open" id="wptu-widgets-icon-close-open-id-66"  widget-id="2" style="background-position: 0px 0px;"></span>';
		
		$html .= '</div>';
				
		$html .='<div id="wptu-widget-adm-cont-id-66" class="wptu-tabs-sections-staff-services wptu-services-list-adm" style=" display:none">';
		
		if(isset($wptucomplement) && isset($wptu_reply_signature))
		{
			
			$html .= $wptu_reply_signature->signatures_module($staff_id);
			
		}else{
				
			$html .= __('Please consider upgrading your plugin if you need to add personalized signatures to your replies.','wp-ticket-ultra');
				
		}
			
		$html .= '</div>';
		$html .='</li>';*/
		
						
			
		
		
		$html .= '</ul>';
		
		$html .= '</div>';
			
		return $html ;		
			
	
	}
	
	//this returns the service for a particular user, if it has not been set we will take the defaul.	
	function get_staff_personal_details( $staff_id )
	{
		global $wpdb, $wpticketultra, $wptucomplement;		
		
		$user = get_user_by( 'id', $staff_id );
		
		$html = '';
		
		
		$html .='<div class="wptu-profile-field" >';		
		$html .='<label class="wptu-field-type" for="display_name"><span>'.$wpticketultra->userpanel->get_user_pic( $staff_id, 80, 'avatar', null, null, false).' <div class="wptu-div-for-avatar-upload"> <a href="?page=wpticketultra&tab=users&avatar='.$staff_id.'"><button name="wptu-button-change-avatar" id="wptu-button-change-avatar" class="wptu-button-change-avatar" type="link"><span><i class="fa fa-camera"></i></span>'.__('Update Pic','wp-ticket-ultra').'	</button></a></div></span>
		
		</label>';
		
		$html .='<div class="wptu-field-value" ></div>';		
		$html .= '</div>';
		
		$html .='<div class="wptu-profile-field" >';		
		$html .='<label class="wptu-field-type" for="display_name"><span>'.__('Full Name','wp-ticket-ultra').'</span></label>';
		$html .='<div class="wptu-field-value" ><input type="text" class=" bup-input " name="display_name" id="reg_display_name" value="'.$user->display_name.'" title="'.__('Your Full Name','wp-ticket-ultra').'" ></div>';		
		$html .= '</div>';
		
		$html .='<div class="wptu-profile-field" >';		
		$html .='<label class="wptu-field-type" for="display_name"><span>'.__('Phone','wp-ticket-ultra').'</span></label>';
		$html .='<div class="wptu-field-value" ><input type="text" class=" bup-input " name="reg_telephone" id="reg_telephone" value="'.$wpticketultra->wptu_get_user_meta($staff_id, 'reg_telephone').'" title="'.__('Your Phone Number','wp-ticket-ultra').'" ></div>';		
		$html .= '</div>';
		
		$html .='<div class="wptu-profile-field" >';		
		$html .='<label class="wptu-field-type" for="display_name"><span>'.__('E-mail','wp-ticket-ultra').'</span></label>';
		$html .='<div class="wptu-field-value" > <input type="text" class=" bup-input " name="reg_email" id="reg_email" value="'.$user->user_email.'" title="'.__('Your Email','wp-ticket-ultra').'" > <input type="hidden" class=" bup-input " name="reg_email2" id="reg_email2" value="'.$user->user_email.'"  ></div>';		
		$html .= '</div>';
		
		
		$html .= '<div class="wptu-field ">';
		$html .= '				<label class="wptu-field-type "><button name="wptu-btn-user-details-confirm" id="wptu-btn-user-details-confirm" class="wptu-button-submit-changes">'.__('Submit','wp-ticket-ultra').'	</button></label>';
		
	
		$html .= '<div class="wptu-field-value">
						    <input type="hidden" name="wptu-register-form" value="wptu-register-form">								
							
							
				   </div>';
		$html .= '</div>';
		
		$html .= '<div class="bup-field "><span id="wptu-edit-details-message">&nbsp;</span>';
		$html .= '</div>';
				
		
		return $html;
	
	
	}
	
		//this is used on admin dashboard
	public function get_user_auth_status_staff($staff_id)
	{
		global $wpticketultra;
		
		$html = '';
		
		$client_id = $wpticketultra->get_option('google_calendar_client_id');
		$client_secret = $wpticketultra->get_option('google_calendar_client_secret');
		
		//get client access token		
		$accessToken = $wpticketultra->bup_get_user_meta($staff_id, 'google_cal_access_token');
		
		if($accessToken=='') //get auth url
		{
			if($client_id=='' || $client_secret=='')
			{				
				$html = "<p>".__('Please set client ID and client Secret!','wp-ticket-ultra')."</p>";
				
			
			}else{
				
				//$auth_url = $this->get_auth_url_staff();			
				//$html = "<p><a href='$auth_url'>".__('Connect Me!','wp-ticket-ultra')."</a></p>";
				
			}	
		
		}else{
			
			if($client_id=='' || $client_secret=='')
			{				
				$html = "<p>".__('Please set client ID and client Secret!','wp-ticket-ultra')."</p>";
				
			
			}else{	
								
				$html .= "<p>".__('Select Your Calendar:','wp-ticket-ultra')."</p>";
				$html .= "<p>".$this->get_calendar_list_drop($staff_id)."</p>";
					
				$html .= '<p> <button name="bup-backenedb-set-gacal-adm" id="bup-backenedb-set-gacal-adm" class="bup-button-submit-changes" staff-id="'.$staff_id.'">'.__('SET CALENDAR','wp-ticket-ultra').'	</button> </p>';				
				$html .= "<p id='bup-gcal-message3'>&nbsp;</p>";			
				
				$google_calendar_default = $wpticketultra->bup_get_user_meta($staff_id, 'google_calendar_default');
				
				if($google_calendar_default=='')
				{
					$html .= "<p id='bup-gcal-message1' ><strong class='bup-backend-info-tool-warning'>".__("IMPORTANT: You haven't set a calendar, yet.",'wp-ticket-ultra')."</strong></p>";
					
					$html .= "<p class='bup-backend-info-tool' id='bup-gcal-message2'><i class='fa fa-info-circle'></i><strong>&nbsp;".__("If you don't set a calendar the primary calendar will be used by default. ",'wp-ticket-ultra')."</strong></p>";
				
				}else{
					
					$html .= "<p class='bup-backend-info-tool' id='bup-gcal-message44'><i class='fa fa-info-circle'></i><strong>&nbsp;".__("If you don't see your new calendars, plase disconnect and connect again. ",'wp-ticket-ultra')."</strong></p>";
					
					
				
				}							
			
			}
			
			
		}		
		
		return $html;
		
			
	}
	
	public function set_default_google_calendar()
	{
		
		global $wpdb, $wpticketultra;
		
		$staff_id =$_POST['staff_id'];		
		$google_calendar = $_POST['google_calendar'];		
		update_user_meta ($staff_id, 'google_calendar_default', $google_calendar);		
		
		$html =__("Your calendar has been set! ", 'wp-ticket-ultra');
		
		echo $html;
		
		die();
	
	}
	
	function get_calendar_list_drop($staff_id)	
	{
		global $wpticketultra;
		
		$html = '<select name="bup_staff_calendar_list" size="1" id="bup_staff_calendar_list">';
		
		//display calendars list				
		$google_calendar_list = $wpticketultra->bup_get_user_meta($staff_id, 'google_calendar_list');
		$google_calendar_default = $wpticketultra->bup_get_user_meta($staff_id, 'google_calendar_default');
		
		 foreach ($google_calendar_list as $calendar) 
		 {
			 $sel =  '';
			 if($calendar['id']==$google_calendar_default){$sel =  'selected="selected"';}
			 
 			$html .= '<option value="'.$calendar['id'].'" '.$sel.'>'.$calendar['summary'].'</option>'; 		 			   
			   
    	 }
		 
		 $html .= '</select>';
		 
		 if(empty($google_calendar_list))
		 {
		 
			 $html .= "<p class='bup-backend-info-tool' id='bup-gcal-message2'><i class='fa fa-info-circle'></i><strong>&nbsp;".__("If the calendars list is empty, please disconnect and connect again. ",'wp-ticket-ultra')."</strong></p>";
		 
		  }
				
				
		return $html;
	
	}
	
	//this returns the service for a particular user, if it has not been set we will take the defaul.	
	function get_staff_service_rate( $staff_id, $service_id )
	{
		global $wpdb, $bookingultrapro;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . 'bup_service_rates WHERE rate_service_id =  "'.$service_id.'" AND	rate_staff_id= "'.$staff_id.'" ' ;			
				
		$res = $wpdb->get_results($sql);
		
				
		$ret = array();
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				$ret = array('price'=>$row->rate_price, 'capacity'=>$row->rate_capacity);			
			
			}
			
		}else{
			
			//we need to get the default values for this service
			$serv = $bookingultrapro->service->get_one_service($service_id);
			
			$ret = array('price'=>$serv->service_price, 'capacity'=>$serv->service_capacity);
		}
		
		return $ret;
	
		
	}
	
	
	function get_all_staff_allowed_deptos( $staff_id )
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_department_staff WHERE 	depto_staff_id= "'.$staff_id.'" ' ;			
				
		$res = $wpdb->get_results($sql);		
		return $res;
	
	}
	
	function get_all_staff_allowed_deptos_list( $staff_id )
	{
		global $wpdb, $wpticketultra;
		
		$list = '';					
				
		$deptos = $this->get_all_staff_allowed_deptos($staff_id);
		
		if ( !empty( $deptos ) )
		{
			$total = count($deptos);
			$i = 0;
		
			foreach ( $deptos as $depto )
			{
				$i++;
				
				$list .= $depto->depto_department_id;	
				
				if($i<$total){$list .=',';}	
				
			
			}
			
		}else{
			
			$list = '0';	
			
			
		}	
		
			
		return $list;
	
	}
	
	//returns true or false if the department is within this department	
	function staff_depto_allowed( $staff_id, $depto_id )
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_department_staff WHERE depto_department_id =  "'.$depto_id.'" AND	depto_staff_id= "'.$staff_id.'" ' ;			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return true			;
			
			}
			
		}else{
			
			return false;
		}
	
	}
	
	//returns true or false if the department is within this department	
	function staff_component_allowed( $staff_id, $component_id )
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_bug_sub_item_staff WHERE subitemstaff_component_id =  "'.$component_id.'" AND subitemstaff_staff_id= "'.$staff_id.'" ' ;			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return true			;
			
			}
			
		}else{
			
			return false;
		}
	
	}
	
	function get_staff_bugs_products_admin( $staff_id )
	{
		global $wpdb, $wpticketultra, $wptu_bugtracker;

		
		$html = '';
		
		$site_list = $wpticketultra->site->get_all(); 
		
		if ( !empty( $site_list ) )
		{		
		
			foreach ( $site_list as $site )
			{
				$html .='<div class="wptu-serv-category-title">';
				
				
				    $html .='<div class="wptu-col1">';					
						$html .='<input type="checkbox" class="wptu-cate-bug-checked wptu-bug-cate-parent" value="'.$site->site_id.'" name="bup-cate[]" data-category-id="'.$site->site_id.'" id="wptubug-cate-'.$site->site_id.'"><label for="wptubug-cate-'.$site->site_id.'"><span></span>'.$site->site_name.'</label>';					
					$html .='</div>';	
					
					
					
				$html .='</div>';
				
				//get components				
				$comp_list = $wptu_bugtracker->component->get_all_product_components($site->site_id); 
				
				if ( !empty( $comp_list ) )
				{
				
					$html .='<ul>';
					
					foreach ( $comp_list as $depto )
					{
						
						
						$checked_service = 'checked="checked"';
						$disable_service = '';
						if(!$this->staff_component_allowed($staff_id, $depto->subitem_id))	
						{
							 $checked_service = '';
							 $disable_service = 'disabled="disabled"'; 
						}		
						
						$html .='<li>';
						
						$html .='<div class="wptu-services-left">';						
						
						$html .='<input type="checkbox" class="wptu-cate-bug-checked ubp-bug-cate bup-bug-cate-'.$site->site_id.'" value="'.$depto->subitem_id.'" name="wptu-bugs-components[]" data-category-id="'.$site->site_id.'" id="wptubug-service-'.$depto->subitem_id.'" '. $checked_service.'><label for="wptubug-service-'.$depto->subitem_id.'"><span></span>'.$depto->subitem_name .'</label>';	
						
						$html .='</div>';
						
						
						$html .'<div style="border-bottom: 1px dotted black; overflow: hidden; padding-top: 15px;"></div>';
						
						$html .='</li>';
					
					}			
					
					
					$html .='</ul>'; //end categories
					
				
				}
						
			
			}		
			
			
			$html .=' <p> <button name="wptu-admin-edit-bug-permissions-save" id="wptu-admin-edit-bug-permissions-save" class="wptu-button-submit-changes" ubp-staff-id= "'.$staff_id.'">'.__('Save Changes','wp-ticket-ultra').'</button>&nbsp; <span id="wptu-loading-animation-bugs">  </span></p>';
			
			
		}	
		
		
		return $html;		
	
	}
	
	
	
	function get_staff_department_admin( $staff_id )
	{
		global $wpdb, $wpticketultra;

		
		$html = '';
		
		$site_list = $wpticketultra->site->get_all(); 
		
		if ( !empty( $site_list ) )
		{		
		
			foreach ( $site_list as $site )
			{
				$html .='<div class="wptu-serv-category-title">';
				
				
				    $html .='<div class="wptu-col1">';					
						$html .='<input type="checkbox" class="ubp-cate-service-checked wptu-service-cate-parent" value="'.$site->site_id.'" name="bup-cate[]" data-category-id="'.$site->site_id.'" id="bup-cate-'.$site->site_id.'"><label for="bup-cate-'.$site->site_id.'"><span></span>'.$site->site_name.'</label>';					
					$html .='</div>';	
					
					
					
				$html .='</div>';
				
				//get services
				
				$dep_list = $wpticketultra->department->get_all_departments($site->site_id); 
				
				if ( !empty( $dep_list ) )
				{
				
					$html .='<ul>';
					
					foreach ( $dep_list as $depto )
					{
						//get service data						
						//$serv_data = $this->get_staff_service_rate($staff_id, $service->service_id);
						
						//print_r($serv_data);
						
						$checked_service = 'checked="checked"';
						$disable_service = '';
						if(!$this->staff_depto_allowed($staff_id, $depto->department_id))	
						{
							 $checked_service = '';
							 $disable_service = 'disabled="disabled"'; 
						}		
						
						$html .='<li>';
						
						$html .='<div class="wptu-services-left">';						
						
						$html .='<input type="checkbox" class="ubp-cate-service-checked ubp-service-cate bup-service-cate-'.$site->site_id.'" value="'.$depto->department_id.'" name="bup-service[]" data-category-id="'.$site->site_id.'" id="bup-service-'.$depto->department_id.'" '. $checked_service.'><label for="bup-service-'.$depto->department_id.'"><span></span>'.$depto->department_name.'</label>';	
						
						$html .='</div>';
						
										
					
						
						
						
						$html .'<div style="border-bottom: 1px dotted black; overflow: hidden; padding-top: 15px;"></div>';
						
						$html .='</li>';
					
					}			
					
					
					$html .='</ul>'; //end categories
					
				
				}
						
			
			}		
			
			
			$html .=' <p> <button name="wptu-admin-edit-staff-service-save" id="wptu-admin-edit-staff-service-save" class="wptu-button-submit-changes" ubp-staff-id= "'.$staff_id.'">'.__('Save Changes','wp-ticket-ultra').'</button>&nbsp; <span id="wptu-loading-animation-services">  </span></p>';
			
			
		}	
		
		
		return $html;		
	
	}
	
	function get_staff_member($staff_id)
	{
		 global $wpdb,$blog_id, $wp_query;	
		 
		$args = array( 	
						
			'meta_key' => 'wptu_is_staff_member',                    
			'meta_value' => 1,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);		

		$user_query = new WP_User_Query( $args );
		$users= $user_query->get_results();
		
		
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				if($user->ID==$staff_id)
				{
				
					return $user;
				}			
				
				
			}				
		
		}
		
		return $users;
	
	}
	
	//get all staf for FULL Calendar		
	function get_staff_list_fc($location_id = NULL)
	{
		 global $wpdb,$blog_id, $wp_query;	
		 
		 
		if($location_id=='' || $location_id=='undefined' )
		{
		 
			$args = array( 	
							
				'meta_key' => 'wptu_is_staff_member',                    
				'meta_value' => 1,                  
				'meta_compare' => '=',  
				'count_total' => true,   
	
	
				);			
	
			 // Create the WP_User_Query object
			$user_query = new WP_User_Query( $args );
			$users= $user_query->get_results();			
		
		}else{			
			
			$sql =  "SELECT  usu.*, staff_location.* 	" ;		
			$sql .= " FROM " . $wpdb->users . " usu ";				
			$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_filter_staff staff_location ON (staff_location.	fstaff_staff_id = usu.ID)";		
					
			$sql .= " WHERE staff_location.	fstaff_staff_id = usu.ID AND  staff_location.		fstaff_location_id  = '".$location_id."'  ";
			
			$users = $wpdb->get_results($sql);		
		
		}
		
		
		return $users;
	
	}
	
	function get_staff_list_calendar_filter( $service_id=null )
	{
		 global $wpdb, $wp_query;	
		 
		$args = array( 	
						
			'meta_key' => 'wptu_is_staff_member',                    
			'meta_value' => 1,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
			
			
		if(isset($_GET["bup-staff-calendar"]))
		{
			$bup_staff_calendar = $_GET["bup-staff-calendar"];		
		}
		

		 // Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		$users= $user_query->get_results();
		
		$selected ='';

		
		$count = 0;
		
		//$html = '';
		
		$htm = '<select id="bup-staff-calendar" name="bup-staff-calendar"> ';		
		$htm .= '<option value="" selected="selected" >'.__('All Staff Members', 'wp-ticket-ultra').'</option>';
				
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				
				$selected = '';				
				if($bup_staff_calendar==$user->ID){$selected = 'selected="selected"';}
				
				$htm .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.'</option>';
				
				
				
			}
			$htm .= '</select>';
		
		}
		
		return $htm;
	
	}
	
	function get_not_staff_users_to_convert()
	{
		 global $wpdb,$blog_id, $wp_query;	
		 
		
		$args = array( 	
						
			'meta_key' => 'wptu_is_staff_member',                    
			'meta_value' => '1',                  
			'meta_compare' => '!=',  
			'count_total' => true,   


			);
		

		// Create the WP_User_Query object
		$user_query = new WP_User_Query( $args );
		$users= $user_query->get_results();	
		
		
		
		
		$selected ='';

		
		$count = 0;
		
		$html = '';
		
		$html .= '<select name="bup-staff" id="bup-staff">';
		$html .= '<option value="" selected="selected" >'.__('Select User', 'wp-ticket-ultra').'</option>';
		
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				
		
				$html .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.'</option>';
				
				
				
			}
			$html .= '</select>';
		
		
		
					
		
		}
		
		return $html;
	
	}
	
	function get_staff_list_front( $location_id=null )
	{
		 global $wpdb,$blog_id, $wp_query;	
		 
		
		if($location_id=='')
		{
			$args = array( 	
						
			'meta_key' => 'wptu_is_staff_member',                    
			'meta_value' => 1,                  
			'meta_compare' => '=',  
			'count_total' => true,   


			);
		

			 // Create the WP_User_Query object
			$user_query = new WP_User_Query( $args );
			$users= $user_query->get_results();
		
		}else{
		
		
			$sql = ' SELECT  user.*, staff_location.*  FROM ' . $wpdb->users . '  user ' ;		
			
			$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_filter_staff staff_location ON (staff_location.fstaff_staff_id = user.ID)";
			
			$sql .= " WHERE staff_location.fstaff_staff_id = user.ID AND  staff_location.fstaff_location_id = '".$location_id."' " ;					
			$sql .= ' ORDER BY user.display_name ASC  ' ;
			$users = $wpdb->get_results($sql);
			
		}
		
		
		
		
		
		$selected ='';

		
		$count = 0;
		
		$html = '';
		
		$html .= '<select name="bup-staff" id="bup-staff">';
		$html .= '<option value="" selected="selected" >'.__('Any', 'wp-ticket-ultra').'</option>';
		
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				
		
				$html .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.'</option>';
				
				
				
			}
			$html .= '</select>';
		
		
		
					
		
		}
		
		return $html;
	
	}
	
	
	function get_staff_filtered( $args )
	{

        global $wpdb,$blog_id, $wp_query;			
		
		extract($args);		
		$memberlist_verified = 1;		
		$blog_id = get_current_blog_id();

		$paged = (!empty($_GET['paged'])) ? $_GET['paged'] : 1;	
		$offset = ( ($paged -1) * $per_page);	
		
		$query['search_columns']= array('display_name', 'user_email');					
		$query['meta_query'] = array('relation' => strtoupper($relation) );	
	  	
				
		if ($uultra_meta)
		{
			
			$query['meta_query'][] = array(
					'key' => $uultra_meta,
					'value' => $keyword,
					'compare' => 'LIKE'
				);				
		}
		
		$query['meta_query'][] = array(
					'key' => 'wptu_is_staff_member',
					'value' => 1,
					'compare' => '='
		);			
		
				
				
    	if ($sortby) $query['orderby'] = $sortby;			
	    if ($order) $query['order'] = strtoupper($order); // asc to ASC
			
		/** QUERY ARGS END **/
			
		$query['number'] = $per_page;
		$query['offset'] = $offset;
			
		/* Search mode */
		if ( ( isset($_GET['bup_search']) && !empty($_GET['bup_search']) ) || count($query['meta_query']) > 1 )
		{
			$count_args = array_merge($query, array('number'=>10000));
			unset($count_args['offset']);
			$user_count_query = new WP_User_Query($count_args);
						
		}

		if ($per_page) 
		{			
		
			/* Get Total Users */
			if ( ( isset($_GET['bup_search']) && !empty($_GET['bup_search']) ) || count($query['meta_query']) > 1 )
			{
				$user_count = $user_count_query->get_results();								
				$total_users = $user_count ? count($user_count) : 1;
				
			} else {
				
			
				$result = count_users();
				$total_users = $result['total_users'];
				
			}
			
			$total_pages = ceil($total_users / $per_page);
		
		}
		
		$user_count = $user_count_query->get_results();								
		$total_users = $user_count ? count($user_count) : 1;
		
		$wp_user_query = new WP_User_Query($query);
		
	
		if (! empty( $wp_user_query->results )) 
		{
			$arr['total'] = $total_users;
			$arr['paginate'] = paginate_links( array(
					'base'         => @add_query_arg('paged','%#%'),
					'total'        => $total_pages,
					'current'      => $paged,
					'show_all'     => false,
					'end_size'     => 1,
					'mid_size'     => 2,
					'prev_next'    => true,
					'prev_text'    => __(' Previous','wp-ticket-ultra'),
					'next_text'    => __('Next ','wp-ticket-ultra'),
					'type'         => 'plain',
				));
			$arr['users'] = $wp_user_query->results;
		}
		
				
		return $arr;
		
		
	}
	
	function get_staff_details_admin_ajax()
	{
		global $wpdb, $bookingultrapro;
		
		$html='';
		
		$staff_id = $_POST['staff_id'];		
		$html .= $this->get_staff_details($staff_id);
					
		
		echo $html;
		die();
		
	}
	
	function get_first_staff_on_list()
	{
		global $wpdb, $wpticketultra;
		
		$relation = "AND";
		$args= array('per_page' => $howmany, 'keyword' => $uultra_combined_search , 'bup_meta' => $uultra_meta,  'relation' => $relation,  'sortby' => 'ID', 'order' => 'DESC');
		$users = $wpticketultra->userpanel->get_staff_filtered($args);
		
		$c_c =0;
		$user_id = '';
		
		if(!empty($users['users']))
		{
			foreach($users['users'] as $user) 
			{
					
					$user_id = $user->ID;				
					$c_c++;				
					if($c_c==1){return $user_id;}
			}
		}
	}
	
	function get_staff_list_admin_ajax()
	{
		global $wpdb, $wpticketultra;
		
		$html='';
		$uultra_combined_search = '';
		
		$relation = "AND";
		$args= array('keyword' => $uultra_combined_search ,  'relation' => $relation,  'sortby' => 'ID', 'order' => 'DESC');
		$users = $wpticketultra->userpanel->get_staff_filtered($args);
		
		$total = $users['total'];
		
		if (empty($users['users']))
		{
			$total = 0;		
		
		}
		
			
		$html .='<div class="wptu-staff-list-act">';
		$html .='<h1>'.__('Staff','wp-ticket-ultra').'('.$total.')</h1>';
		$html .='<span class="wptu-add-staff"><a href="#" id="wptu-add-staff-btn" title="'.__('Add New Staff Member','wp-ticket-ultra').'" ><i class="fa fa-plus"></i></a></span>';
		$html .='</div>';
		
		if (!empty($users['users']))
		{
			$html .='<ul>';
			$c_c =0;
			
			foreach($users['users'] as $user) {
				
				$user_id = $user->ID;
				
				$c_c++;
				
				if($c_c==1){$html .='<input type="hidden" id="wptu-first-staff-id" value="'.$user_id.'">';}
			
				$html .='<li>';
				$html .='<a href="#" id="wptu-staff-load" class="wptu-staff-load" staff-id="'.$user_id.'"> ';
				
				$html .= $wpticketultra->userpanel->get_user_pic( $user_id, 50, 'avatar', null, null, false);
				$html .='<h3>'.$user->display_name.'</h3>';
				$html .='</a>';
				$html .='</li>';
				
			}
			
			$html .='</ul>';
		
		}else{
			
			$html .=__('There are no staff members.','wp-ticket-ultra');
			
		
		}
		
		
		
		echo $html;
		die();
		
	}
	
		/* Get picture by ID */
	function get_user_pic( $id, $size, $pic_type=NULL, $pic_boder_type= NULL, $size_type=NULL, $with_url=true ) 
	{
		
		 global  $wpticketultra;
		 
		
		$site_url = site_url()."/";
		
		//rand_val_cache		
		$cache_rand = time();
			 
		$avatar = "";
		$pic_size = "";
		
				
		$upload_dir = wp_upload_dir(); 
		$path =   $upload_dir['baseurl']."/".$id."/";
				
		$author_pic = get_the_author_meta('user_pic', $id);
		
		//get user url
		//$user_url=$this->get_user_profile_permalink($id);
		
		if($pic_boder_type=='none'){$pic_boder_type='uultra-none';}
		
		
		if($size_type=="fixed" || $size_type=="")
		{
			$dimension = "width:";
			$dimension_2 = "height:";
		}
		
		if($size_type=="dynamic" )
		{
			$dimension = "max-width:";
		
		}
		
		if($size!="")
		{
			$pic_size = $dimension.$size."px".";".$dimension_2.$size."px";
		
		}
		
		if($wpticketultra->get_option('bup_force_cache_issue')=='yes')
		{
			$cache_by_pass = '?rand_cache='.$cache_rand;
		
		}
		
		$user = get_user_by( 'id', $id );
		
			
		
		if ($author_pic  != '') 
			{
				$avatar_pic = $path.$author_pic;
				
				
				if($with_url)
				{
		 
					$avatar= '<a href="'.$user_url.'">'. '<img src="'.$avatar_pic.'" class="avatar '.$pic_boder_type.'" style="'.$pic_size.' "   id="wptu-avatar-img-'.$id.'" title="'.$user->display_name.'" /></a>';
				
				}else{
					
					$avatar=  '<img src="'.$avatar_pic.'" class="avatar '.$pic_boder_type.'" style="'.$pic_size.' "   id="wptu-avatar-img-'.$id.'" title="'.$user->display_name.'" />';
				
				}
				
				
				
			} else {
				
				$user = get_user_by( 'id', $id );		
				$avatar = get_avatar( $user->user_email, $size );
		
	    	}
		
		return $avatar;
	}
	
	/* delete avatar */
	function delete_user_avatar() 
	{
				
		$user_id =   $_POST['user_id'];			
		update_user_meta($user_id, 'user_pic', '');
		die();
	}
	
	public function avatar_uploader($staff_id=NULL) 
	{
		
	   // Uploading functionality trigger:
	  // (Most of the code comes from media.php and handlers.js)
	      $template_dir = get_template_directory_uri();
?>
		
		<div id="uploadContainer" style="margin-top: 10px;">
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
				<div id="plupload-upload-ui-avatar" class="hide-if-no-js">
                
					<div id="drag-drop-area-avatar">
                    
						<div class="drag-drop-inside">
                        
							<p class="drag-drop-info"><?php	_e('Drop '.$avatar_is_called.' here', 'wp-ticket-ultra') ; ?></p>
							<p><?php _ex('or', 'Uploader: Drop files here - or - Select Files'); ?></p>
							                            
                            
							<p>
                                                      
                            <button name="plupload-browse-button-avatar" id="plupload-browse-button-avatar" class="wptu-button-upload-avatar" ><span><i class="fa fa-camera"></i></span> <?php	_e('Select Image', 'wp-ticket-ultra') ; ?>	</button>
                            </p>
                            
                            <p>
                                                      
                            <button name="plupload-browse-button-avatar" id="wptu-btn-delete-user-avatar" class="wptu-button-delete-avatar" user-id="<?php echo $staff_id?>" redirect-avatar="yes"><span><i class="fa fa-times"></i></span> <?php	_e('Remove', 'wp-ticket-ultra') ; ?>	</button>
                            </p>
                            
                            <p>
                            <a href="?page=wpticketultra&tab=users&ui=<?php echo $staff_id?>" class="uultra-remove-cancel-avatar-btn"><?php	_e('Cancel', 'wp-ticket-ultra') ; ?></a>
                            </p>
                                                        
                           
														
						</div>
                        
                        <div id="progressbar-avatar"></div>                 
                         <div id="bup_filelist_avatar" class="cb"></div>
					</div>
				</div>
                
                 
			
			</div>
            
           
		</div>
        
         <form id="wptu_frm_img_cropper" name="wptu_frm_img_cropper" method="post">                
                
                	<input type="hidden" name="image_to_crop" value="" id="image_to_crop" />
                    <input type="hidden" name="crop_image" value="crop_image" id="crop_image" />
                    
                    <input type="hidden" name="site_redir" value="<?php echo $my_account_url."?page=wpticketultra&tab=users&ui=".$staff_id.""?>" id="site_redir" />                   
                
                </form>

		<?php
			
			$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'plupload-browse-button-avatar',
				'container'           => 'plupload-upload-ui-avatar',
				'drop_element'        => 'wptu-drag-avatar-section',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => false,
				'max_file_size'       => wp_max_upload_size().'b',
				//'max_file_size'       => get_option('drag-drop-filesize').'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				//'filters'             => array(array('title' => __('Allowed Files', $this->text_domain), 'extensions' => "jpg,png,gif,bmp,mp4,avi")),
				'filters'             => array(array('title' => __('Allowed Files', "xoousers"), 'extensions' => "jpg,png,gif,jpeg")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'staff_id' => $staff_id,
					'action'      => 'wptu_ajax_upload_avatar' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init); ?>

			<script type="text/javascript">
			
				jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_avatar = new plupload.Uploader(<?php echo json_encode($plupload_init); ?>);

					// Check for drag'n'drop functionality:
					uploader_avatar.bind('Init', function(up){
						
						var uploaddiv_avatar = $('#plupload-upload-ui-avatar');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_avatar.addClass('drag-drop');
							
							$('#drag-drop-area-avatar')
								.bind('dragover.wp-uploader', function(){ uploaddiv_avatar.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_avatar.removeClass('drag-over'); });

						} else{
							uploaddiv_avatar.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_avatar.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_avatar.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
						// Limit to one limit:
						if (files.length > 1){
							alert("<?php _e('You may only upload one image at a time!', 'wp-ticket-ultra'); ?>");
							return false;
						}
						
						// Remove extra files:
						if (up.files.length > 1){
							up.removeFile(uploader_avatar.files[0]);
							up.refresh();
						}
						
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert("<?php _e('The file you selected exceeds the maximum filesize limit.', 'wp-ticket-ultra'); ?>");
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							jQuery('#bup_filelist_avatar').append('<div class="addedFile" id="' + file.id + '">' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_avatar.start();
						
					});
					
					// A new file was uploaded:
					uploader_avatar.bind('FileUploaded', function(up, file, response){					
						
						
						
						var obj = jQuery.parseJSON(response.response);												
						var img_name = obj.image;							
						
						$("#image_to_crop").val(img_name);
						$("#wptu_frm_img_cropper").submit();

						
						
						
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: {"action": "wptu_refresh_avatar"},
							
							success: function(data){
								
								//$( "#uu-upload-avatar-box" ).slideUp("slow");								
								$("#wptu-backend-avatar-section").html(data);
								
								//jQuery("#uu-message-noti-id").slideDown();
								//setTimeout("hidde_noti('uu-message-noti-id')", 3000)	;
								
								
								}
						});
						
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_avatar.bind('Error', function(up, err) {
						alert("Error: " + err.code + ", Message: " + err.message + (err.file ? ", File: " + err.file.name : "") + "");
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_avatar.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: progressBarValue
						});
						
						jQuery('#progressbar-avatar').html('<span class="progressTooltip">' + up.total.percent + '%</span>');
					});
					
					// Close window after upload ///////////////////////////////
					uploader_avatar.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-avatar').fadeIn().progressbar({
							value: 0
						});
						
						
					});
					
					
					
				});
				
					
			</script>
			
		<?php
	
	
	}
	
	//crop avatar image
	function crop_avatar_user_profile_image()
	{
		global $wpticketultra;
		global $wpdb;
		
		$site_url = site_url()."/";		
	
		/// Upload file using Wordpress functions:
		$x1 = $_POST['x1'];
		$y1 = $_POST['y1'];
		
		$x2 = $_POST['x2'];
		$y2= $_POST['y2'];
		$w = $_POST['w'];
		$h = $_POST['h'];	
		
		$image_id =   $_POST['image_id'];
		$user_id =   $_POST['user_id'];		
		
		if($user_id==''){echo 'error';exit();}
				
		
		$wpticketultra->imagecrop->setDimensions($x1, $y1, $w, $h)	;
		
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];		
		$src = $path_pics.'/'.$user_id.'/'.$image_id;
		
		//new random image and crop procedure				
		$wpticketultra->imagecrop->setImage($src);
		$wpticketultra->imagecrop->createThumb();		
		$info = pathinfo($src);
        $ext = $info['extension'];
		$ext=strtolower($ext);		
		$new_i = time().".". $ext;		
		$new_name =  $path_pics.'/'.$user_id.'/'.$new_i;				
		$wpticketultra->imagecrop->renderImage($new_name);
		//end cropping
		
		//check if there is another avatar						
		$user_pic = get_user_meta($user_id, 'user_pic', true);	
		
		//resize
		//check max width		
		$original_max_width = $wpticketultra->get_option('media_avatar_width'); 
        $original_max_height =$wpticketultra->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height=="")
		{			
			$original_max_width = 80;			
			$original_max_height = 80;			
		}
														
		list( $source_width, $source_height, $source_type ) = getimagesize($new_name);
		
		if($source_width > $original_max_width) 
		{
			if ($this->image_resize($new_name, $new_name, $original_max_width, $original_max_height,0)) 
			{
				$old = umask(0);
				chmod($new_name, 0755);
				umask($old);										
			}		
		}					
						
		if ( $user_pic!="" )
		{
				
			 //there is a pending avatar - delete avatar																					
			 	
			 $path_avatar = $path_pics['baseurl']."/".$user_id."/".$image_id;					
										  
			 //delete								
			 //update meta
			  update_user_meta($user_id, 'user_pic', $new_i);		  
			  
		  }else{
			  
			  //update meta
			  update_user_meta($user_id, 'user_pic', $new_i);
								  
		  
		  }
		  
		  
		  if(file_exists($src))
		  {
			  unlink($src);
		  }
			 
	
		// Create response array:
		$uploadResponse = array('image' => $new_name);
		
		// Return response and exit:
		echo json_encode($uploadResponse);
		
		die();
		
	}
	
	function image_resize($src, $dst, $width, $height, $crop=0)
	{
		
		  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";
		
		  $type = strtolower(substr(strrchr($src,"."),1));
		  if($type == 'jpeg') $type = 'jpg';
		  switch($type){
			case 'bmp': $img = imagecreatefromwbmp($src); break;
			case 'gif': $img = imagecreatefromgif($src); break;
			case 'jpg': $img = imagecreatefromjpeg($src); break;
			case 'png': $img = imagecreatefrompng($src); break;
			default : return "Unsupported picture type!";
		  }
		
		  // resize
		  if($crop){
			if($w < $width or $h < $height) return "Picture is too small!";
			$ratio = max($width/$w, $height/$h);
			$h = $height / $ratio;
			$x = ($w - $width / $ratio) / 2;
			$w = $width / $ratio;
		  }
		  else{
			if($w < $width and $h < $height) return "Picture is too small!";
			$ratio = min($width/$w, $height/$h);
			$width = $w * $ratio;
			$height = $h * $ratio;
			$x = 0;
		  }
		
		  $new = imagecreatetruecolor($width, $height);
		
		  // preserve transparency
		  if($type == "gif" or $type == "png"){
			imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
			imagealphablending($new, false);
			imagesavealpha($new, true);
		  }
		
		  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);
		
		  switch($type){
			case 'bmp': imagewbmp($new, $dst); break;
			case 'gif': imagegif($new, $dst); break;
			case 'jpg': imagejpeg($new, $dst,100); break;
			case 'jpeg': imagejpeg($new, $dst,100); break;
			case 'png': imagepng($new, $dst,9); break;
		  }
		  return true;
	}
	
	function display_avatar_image_to_crop($image, $user_id=NULL)	
	{
		 global $wpticketultra;
		
		/* Custom style */		
		wp_register_style( 'wptu_image_cropper_style', wptu_url.'js/cropper/cropper.min.css');
		wp_enqueue_style('wptu_image_cropper_style');	
					
		wp_enqueue_script('simple_cropper',  wptu_url.'js/cropper/cropper.min.js' , array('jquery'), false, false);
		
	  
	    $template_dir = get_template_directory_uri();		  
				
		$site_url = site_url()."/";
		
		$html = "";
		
		$upload_dir = wp_upload_dir(); 
		$upload_folder =   $upload_dir['basedir'];		
				
		$user_pic = get_user_meta($user_id, 'user_profile_bg', true);		
		
		if($image!="")
		{
			$url_image_to_crop = $upload_dir['baseurl'].'/'.$user_id.'/'.$image;			
			$html_image = '<img src="'.$url_image_to_crop.'" id="uultra-profile-cover-horizontal" />';					
			
		}
		
		$my_account_url = $wpticketultra->userpanel->get_my_account_direct_link 
		
		
		
		?>
        
        
      	<div id="uultra-dialog-user-bg-cropper-div" class="wptu-dialog-user-bg-cropper"  >	
				<?php echo $html_image ?>                   
		</div>
            
            
             
             
             <p>
                                                      
                            <button name="plupload-browse-button-avatar" id="wptu-confirm-avatar-cropping" class="wptu-button-upload-avatar" type="link"><span><i class="fa fa-crop"></i></span> <?php	_e('Crop & Save', 'wp-ticket-ultra') ; ?>	</button>
                            
                            
                            <div class="wptu-please-wait-croppingmessage" id="wptu-cropping-avatar-wait-message">&nbsp;</div>
                            </p>
                            
                            
                            <div class="uultra-uploader-buttons-delete-cancel" id="btn-cancel-avatar-cropping" >
                            <a href="?page=wpticketultra&tab=users&ui=<?php echo $user_id?>" class="uultra-remove-cancel-avatar-btn"><?php	_e('Cancel', 'wp-ticket-ultra') ; ?></a>
                            </div>
            
     			<input type="hidden" name="x1" value="0" id="x1" />
				<input type="hidden" name="y1" value="0" id="y1" />				
				<input type="hidden" name="w" value="<?php echo $w?>" id="w" />
				<input type="hidden" name="h" value="<?php echo $h?>" id="h" />
                <input type="hidden" name="image_id" value="<?php echo $image?>" id="image_id" />
                <input type="hidden" name="user_id" value="<?php echo $user_id?>" id="user_id" />
                <input type="hidden" name="site_redir" value="<?php echo $my_account_url."?page=wpticketultra&tab=users&ui=".$user_id.""?>" id="site_redir" />
                
		
		<script type="text/javascript">
		
		
				jQuery(document).ready(function($){
					
				
					<?php
					
					
					
					$source_img = $upload_folder.'/'.$user_id.'/'.$image;	
									 
					 $r_width = $this->getWidth($source_img);
					 $r_height= $this->getHeight($source_img);
					 
					$original_max_width = $wpticketultra->get_option('media_avatar_width'); 
					$original_max_height =$wpticketultra->get_option('media_avatar_height'); 
					
					if($original_max_width=="" || $original_max_height=="")
					{			
						$original_max_width = 80;			
						$original_max_height = 80;
						
					}
					
					$aspectRatio = $original_max_width/$original_max_height;
					
					
					 
						 ?>
						var $image = jQuery(".wptu-dialog-user-bg-cropper img"),
						$x1 = jQuery("#x1"),
						$y1 = jQuery("#y1"),
						$h = jQuery("#h"),
						$w = jQuery("#w");
					
					$image.cropper({
								  aspectRatio: <?php echo $aspectRatio?>,
								  autoCropArea: 0.6, // Center 60%
								  zoomable: false,
								  preview: ".img-preview",
								  done: function(data) {
									$x1.val(Math.round(data.x));
									$y1.val(Math.round(data.y));
									$h.val(Math.round(data.height));
									$w.val(Math.round(data.width));
								  }
								});
			
			})	
				
									
			</script>
		
		
	<?php	
		
	}
	
	//You do not need to alter these functions
	function getHeight($image) {
		$size = getimagesize($image);
		$height = $size[1];
		return $height;
	}

	//You do not need to alter these functions
	function getWidth($image) {
		$size = getimagesize($image);
		$width = $size[0];
		return $width;
	}
	
	
	// File upload handler:
	function ajax_upload_avatar()
	{
		global $wpticketultra;
		global $wpdb;
		
		$site_url = site_url()."/";
		
		// Check referer, die if no ajax:
		check_ajax_referer('photo-upload');
		
		/// Upload file using Wordpress functions:
		$file = $_FILES['async-upload'];
		
		
		$original_max_width = $wpticketultra->get_option('media_avatar_width'); 
        $original_max_height =$wpticketultra->get_option('media_avatar_height'); 
		
		if($original_max_width=="" || $original_max_height=="")
		{			
			$original_max_width = 80;			
			$original_max_height = 80;
			
		}
		
			
				
		$o_id = $_POST['staff_id'];
		
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $this->genRandomString();
		
		$rand_name = "avatar_".$rand."_".session_id()."_".time(); 
		
	
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];
			
			
		if($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif') 
		{
			if($o_id != '')
			{
				
				   if(!is_dir($path_pics."/".$o_id."")) 
				   {
						//$this->CreateDir($path_pics."/".$o_id);	
						 wp_mkdir_p( $path_pics."/".$o_id );							   
					}					
										
					$pathBig = $path_pics."/".$o_id."/".$rand_name.".".$ext;						
					
					
					if (copy($file['tmp_name'], $pathBig)) 
					{
						//check auto-rotation						
						if($wpticketultra->get_option('avatar_rotation_fixer')=='yes')
						{
							$this->orient_image($pathBig);
						
						}
						
						$upload_folder = $wpticketultra->get_option('media_uploading_folder');				
						$path = $site_url.$upload_folder."/".$o_id."/";
						
						//check max width												
						list( $source_width, $source_height, $source_type ) = getimagesize($pathBig);
						
						if($source_width > $original_max_width) 
						{
							//resize
						//	if ($this->createthumb($pathBig, $pathBig, $original_max_width, $original_max_height,$ext)) 
							//{
								//$old = umask(0);
								//chmod($pathBig, 0755);
								//umask($old);
														
							//}
						
						
						}
						
						
						
						$new_avatar = $rand_name.".".$ext;						
						$new_avatar_url = $path.$rand_name.".".$ext;				
						
						
						//check if there is another avatar						
						$user_pic = get_user_meta($o_id, 'user_pic', true);						
						
						if ( $user_pic!="" )
			            {
							//there is a pending avatar - delete avatar																					
							$path_avatar = $path_pics."/".$o_id."/".$user_pic;					
														
							//delete								
							if(file_exists($path_avatar))
							{
								unlink($path_avatar);
							}
							
												
							
						}else{
							
																	
						
						}
						
						//update user meta
						
					}
									
					
			     }  		
			
        } // image type
		
		// Create response array:
		$uploadResponse = array('image' => $new_avatar);
		
		// Return response and exit:
		echo json_encode($uploadResponse);
		
		//echo $new_avatar_url;
		die();
		
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
	
	public function orient_image($file_path) 
	{
        if (!function_exists('exif_read_data')) {
            return false;
        }
        $exif = @exif_read_data($file_path);
        if ($exif === false) {
            return false;
        }
        $orientation = intval(@$exif['Orientation']);
        if (!in_array($orientation, array(3, 6, 8))) {
            return false;
        }
        $image = @imagecreatefromjpeg($file_path);
        switch ($orientation) {
            case 3:
                $image = @imagerotate($image, 180, 0);
                break;
            case 6:
                $image = @imagerotate($image, 270, 0);
                break;
            case 8:
                $image = @imagerotate($image, 90, 0);
                break;
            default:
                return false;
        }
        $success = imagejpeg($image, $file_path);
        // Free up memory (imagedestroy does not delete files):
        @imagedestroy($image);
        return $success;
    }
	
	function validate_if_user_has_gravatar($user_id)
	{
		
		$has_gravatar = get_user_meta( $user_id, 'wptu_has_gravatar', true);
		
		if($has_gravatar=='' || $has_gravatar=='0')
		{			
			//check if user has a valid gravatar
			if($this->validate_gravatar($user_id))
			{
				//has a valid gravatar				
				update_user_meta($user_id, 'wptu_has_gravatar', 1);			
			
			}else{
				
				delete_user_meta($user_id, 'wptu_has_gravatar')	;		
				
			}
		
		
		}
	
	}
	
	
	/**
	 * Utility function to check if a gravatar exists for a given email or id
	 * @param int|string|object $id_or_email A user ID,  email address, or comment object
	 * @return bool if the gravatar exists or not
	 */
	
	function validate_gravatar($id_or_email) 
	{
	  //id or email code borrowed from wp-includes/pluggable.php
		$email = '';
		if ( is_numeric($id_or_email) ) {
			$id = (int) $id_or_email;
			$user = get_userdata($id);
			if ( $user )
				$email = $user->user_email;
		} elseif ( is_object($id_or_email) ) {
			// No avatar for pingbacks or trackbacks
			$allowed_comment_types = apply_filters( 'get_avatar_comment_types', array( 'comment' ) );
			if ( ! empty( $id_or_email->comment_type ) && ! in_array( $id_or_email->comment_type, (array) $allowed_comment_types ) )
				return false;
	
			if ( !empty($id_or_email->user_id) ) {
				$id = (int) $id_or_email->user_id;
				$user = get_userdata($id);
				if ( $user)
					$email = $user->user_email;
			} elseif ( !empty($id_or_email->comment_author_email) ) {
				$email = $id_or_email->comment_author_email;
			}
		} else {
			$email = $id_or_email;
		}
	
		$hashkey = md5(strtolower(trim($email)));
		$uri = 'http://www.gravatar.com/avatar/' . $hashkey . '?d=404';
	
		$data = wp_cache_get($hashkey);
		if (false === $data) {
			$response = wp_remote_head($uri);
			if( is_wp_error($response) ) {
				$data = 'not200';
			} else {
				$data = $response['response']['code'];
			}
			wp_cache_set($hashkey, $data, $group = '', $expire = 60*5);
	
		}		
		if ($data == '200'){
			return true;
		} else {
			return false;
		}
	}
	
	function wp_validate_gravatar($email) 
	{
		// Craft a potential url and test its headers
		/*$hash = md5(strtolower(trim($email)));
		$uri = 'http://www.gravatar.com/avatar/' . $hash . '?d=404';
		$headers = @get_headers($uri);
		if (!preg_match("|200|", $headers[0])) {
			$has_valid_avatar = FALSE;
		} else {
			$has_valid_avatar = TRUE;
		}*/
		$has_valid_avatar = TRUE;
		return $has_valid_avatar;
	}

	function get_avatar_url( $avatar) 
	{

		preg_match( '#src=["|\'](.+)["|\']#Uuis', $avatar, $matches );
	
		return ( isset( $matches[1] ) && ! empty( $matches[1]) ) ?
			(string) $matches[1] : '';  
	
	}
	
		
	function get_users_auto_complete()
	{
		global $wpdb, $wpticketultra;
		
		$term     = sanitize_text_field( $_GET['term'] );
		$type     = sanitize_text_field( $_GET['type'] );
		
		// Initialise suggestions array
    	$suggestions=array();
		
		if($type=='staff') //only add staff members
		{
			
					
			$sql =  'SELECT  usu.*, meta.*	 FROM ' . $wpdb->users . ' usu  ' ;				
			$sql .= " RIGHT JOIN ". $wpdb->prefix."usermeta meta ON (meta.user_id = usu.ID)";		
			$sql .= ' WHERE (usu.display_name LIKE  "%'.$term.'%" OR usu.user_email LIKE "%'.$term.'%" )  AND meta.meta_key = "wptu_is_staff_member" AND meta.user_id = usu.ID  LIMIT 20  ';	
		//echo $sql ;
		
		
		 }else{
			 
			 $sql = ' SELECT * FROM ' . $wpdb->users . ' WHERE display_name LIKE  "%'.$term.'%" OR user_email LIKE "%'.$term.'%" LIMIT 20' ;	
			 
			 
		 
		 }	
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
			$count = 0;
		
			foreach ( $res as $row )
			{
				 // Initialise suggestion array
				 
				$user_id = $row->ID;
				
				$legend = '';
				$is_client = $wpticketultra->profile->is_client($user_id);
				
				if(!$is_client){
					
					$legend = __(' - Staff','wp-ticket-ultra');
				
				}
				
				if($type=='' && !$is_client) //only add staff members
				{
					continue;
					
				}
				 
									
				$options['results'][] = array(
								'id' => $row->ID,
								'value'    => $row->display_name,
								'label'    => $row->display_name.' '. '('.$row->user_email.')'.$legend,
							); 
							
							
				$count++;
							
			} //end for
			
			if(!isset($options['results'])){
				
				$options['results'][] = array(
						'id' => '0',
						'value'    => '0',
						'label'    => __('No results found','wp-ticket-ultra'),
					);
				
			
			}
			
			
		
		}else{
			
			$options['results'][] = array(
						'id' => '0',
						'value'    => '0',
						'label'    => __('No results found','wp-ticket-ultra'),
					);
			
		}
		
		
		$response = json_encode( $options );
    	echo $response;
    	exit();
	
	}
	
	
	
	function get_one($id)
	{
		global $wpdb, $wpticketultra;
		
		$user = get_user_by( 'id', $id );
		
		return $user;
		
	}
	
}
$key = "userpanel";
$this->{$key} = new WPTicketUltraUser();
?>