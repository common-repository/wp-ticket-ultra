<?php
class WPTicketUltraTicket
{
	var $table_prfix = 'wptu';
	var $ajax_p = 'wptu';
	var $sucess_message = '';	
	var $wptu_reply_files ='wptu_reply_files';
	
	
	
	function __construct() 
	{
		$this->ini_db();
		$this->include_for_validation = array('text','fileupload','textarea','select','radio','checkbox','password');	
		
		add_action( 'init', array($this, 'wptu_handle_ticket') );
		add_action( 'wp_ajax_'.$this->ajax_p.'_update_ticket_info', array( &$this, 'update_ticket_info' ));	
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_reply_file', array( &$this, 'delete_reply_file' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_tickets_by_client', array( &$this, 'tickets_by_client' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_trash_ticket', array( &$this, 'trash_ticket' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_ticket_reply_confirm', array( &$this, 'delete_ticket_reply_confirm' ));
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_mark_as_resolved_confirm', array( &$this, 'mark_as_resolved_confirm' ));
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_mark_as_closed_confirm', array( &$this, 'mark_as_closed_confirm' ));
		
			
	
	}
	
	function wptu_handle_ticket () 
	{		
		
		/*Form is fired*/	    
		if (isset($_POST['wptu-ticket-reply-sub-conf'])) {	     			
			
			/* Create ticket */
			$this->create_ticket_reply();
				
		}
		
		if (isset($_POST['wptutriggerbulk']) && isset($_POST['bulkaction']) && $_POST['bulkaction']!='') {	     			
			
			/* Create ticket */
			$this->bulk_actions();
				
		}
		
		
		
		
		
	}
	
	public function ini_db()
	{
		global $wpdb;	

				
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prfix.'_tickets (
				`ticket_id` bigint(20) NOT NULL auto_increment,
				`ticket_website_id` int(11) NOT NULL DEFAULT "1",
				`ticket_user_id` int(11) NOT NULL,
				`ticket_staff_id` int(11) NOT NULL DEFAULT "0",
				`ticket_woocomerce_prod_id` bigint(20) NOT NULL DEFAULT "0",
				`ticket_woocomerce_order_id` bigint(20) NOT NULL DEFAULT "0",
				`ticket_last_reply_staff_id` int(11) NOT NULL,				
				`ticket_department_id` int(11) NOT NULL,				
				`ticket_subject` varchar(200) NOT NULL,									
				`ticket_message` longtext ,
				`ticket_date` datetime NOT NULL,
				`ticket_date_last_change` datetime NOT NULL,				
				`ticket_first_reply_within` datetime NOT NULL,
				`ticket_resolved_within` datetime NOT NULL,					
				`ticket_first_reply_time` datetime NOT NULL,	
				`ticket_resolved_date` datetime NOT NULL,									
				`ticket_status` int(11) NOT NULL DEFAULT "1",
				`ticket_status_workflow` int(11) NOT NULL DEFAULT "1",
				`ticket_priority` int(11) NOT NULL DEFAULT "1",
				`ticket_severity` int(11) NOT NULL DEFAULT "1",
				`ticket_type` int(11) NOT NULL DEFAULT "1",	
				`ticket_first_reply` int(1) NOT NULL DEFAULT "0",
				`ticket_is_premium` int(1) NOT NULL DEFAULT "0",
				`ticket_is_private` int(1) NOT NULL DEFAULT "0",
				`ticket_reply_back_promissed` int(1) NOT NULL DEFAULT "0",							
				`ticket_key` varchar(250) NOT NULL,					 			
				PRIMARY KEY (`ticket_id`)
			) COLLATE utf8_general_ci;';
	
	
		$wpdb->query( $query );	
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prfix.'_ticket_replies (
				`reply_id` bigint(20) NOT NULL auto_increment,
				`reply_ticket_id` int(11) NOT NULL,
				`reply_user_id` int(11) NOT NULL,
				`reply_message` longtext ,
				`reply_date` datetime NOT NULL	,							 			
				PRIMARY KEY (`reply_id`)
			) COLLATE utf8_general_ci;';
	
	
		$wpdb->query( $query );	
		
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prfix.'_ticket_reply_attachments (
				`attachment_id` bigint(20) NOT NULL auto_increment,
				`attachment_reply_id` bigint(20) NOT NULL,
				`attachment_file` varchar(300) NOT NULL,											 			
				PRIMARY KEY (`attachment_id`)
			) COLLATE utf8_general_ci;';
	
	
		$wpdb->query( $query );			
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prfix.'_tickets_meta (
				`meta_id` bigint(20) NOT NULL auto_increment,
				`meta_ticket_id` int(11) NOT NULL,				
				`meta_ticket_name` varchar(300) NOT NULL,
				`meta_ticket_value` longtext,					 			
				PRIMARY KEY (`meta_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prfix.'_tickets_permissions (
				`perm_id` bigint(20) NOT NULL auto_increment,
				`perm_ticket_id` bigint(20) NOT NULL,
				`perm_staff_id` bigint(20) NOT NULL	,		 			
				PRIMARY KEY (`perm_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
			
	}
	
	/*Attach Files to a Reply*/
	public function ticket_reply_move_files($reply_id, $ticket_temp_folder)
	{
		global $wpticketultra, $wptucomplement;
		
		$temp_upload_folder = 'wptu_temp_files'; //temp uploads when creating the reply
		$dest_upload_folder = 'wptu_reply_files'; // final folder when the reply is confirmed
		
		$upload_dir = wp_upload_dir(); 
		$upload_folder =   $upload_dir['basedir'];		
		
		if(!is_dir($upload_folder."/".$dest_upload_folder)) 
		{
			wp_mkdir_p( $upload_folder."/".$dest_upload_folder );							   
		}				
		
		if($reply_id!='' && $ticket_temp_folder!='')
		{
			
			$open = $upload_folder."/".$temp_upload_folder.'/'.$ticket_temp_folder; //this is the folder to loop
			$dest = $upload_folder."/".$dest_upload_folder.'/'; //this is final folder
			
			if ($files = glob($open . "/*")) 
			{
												
				foreach ($files as $file) 
				{ 
				
					$info = pathinfo($file);
					$real_name = $info['basename'];
					
					$dest_folder = $dest.$real_name;
					
					if (copy($file, $dest_folder)) 
					{
						
						//update database
						$this->attach_image_to_ticket($reply_id, $real_name);
						
						//delete file in temp folder
						unlink($file);

					}
					
				
				} //endfor
				
				//delete temp folder				
				rmdir($open);
				
			} else {
				
				//no files
			}
			
		}
	
	}
	
	/*Attach files to reply Ticket*/
	public function attach_image_to_ticket($reply_id, $image_name)
	{
		global $wpdb,  $wpticketultra;
				
		//update database
		$query = "INSERT INTO " . $wpdb->prefix .$this->table_prfix."_ticket_reply_attachments (
		`attachment_reply_id`,`attachment_file` ) VALUES ('$reply_id','$image_name')";
		
		//echo $query;						
		$wpdb->query( $query );		
		
		return $wpdb->insert_id;				
						
	}
	
	public function bulk_actions()
	{
		global $wpdb,  $wpticketultra;
		
		$tickets_list =  $_POST['wptu-ticket-list'];
		$bulkaction =  $_POST['bulkaction'];
		

		if(!empty($tickets_list))
		{
		
			foreach ($tickets_list as $ticket_id) 
			{
				
				//get ticket replies			
				$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prfix. '_ticket_replies  ' ;
				$sql .= ' WHERE reply_ticket_id = "'.$ticket_id.'"' ;			
						
				$res = $wpdb->get_results($sql);
				
				if ( !empty( $res ) )
				{
				
					foreach ( $res as $reply )
					{
						$reply_id = $reply->reply_id;
						
						//delete reply images
						$sql ="DELETE FROM " . $wpdb->prefix . $this->table_prfix.'_ticket_reply_attachments'. " WHERE attachment_reply_id =%d ;";			
						$sql = $wpdb->prepare($sql,array($ticket_id));	
						$rows = $wpdb->query($sql);						
						
						//delete reply			
						$sql ="DELETE FROM " . $wpdb->prefix . $this->table_prfix.'_ticket_replies'. " WHERE reply_id=%d ;";			
						$sql = $wpdb->prepare($sql,array($reply_id));	
						$rows = $wpdb->query($sql);	
						
						//delete file		
					}			
				}
				
				
				//delete ticket metas				
				$sql ="DELETE FROM " . $wpdb->prefix . $this->table_prfix.'_tickets_meta'. " WHERE meta_ticket_id=%d ;";			
				$sql = $wpdb->prepare($sql,array($ticket_id));	
				$rows = $wpdb->query($sql);	
				
				//delete ticket				
				$sql ="DELETE FROM " . $wpdb->prefix . $this->table_prfix.'_tickets'. " WHERE ticket_id=%d ;";			
				$sql = $wpdb->prepare($sql,array($ticket_id));	
				$rows = $wpdb->query($sql);	
			
			} //end for each
			
			$this->sucess_message = '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__("The ticket(s) were permanently deleted.",'wp-ticket-ultra').'</span></div>';
		
		}else{ //there was not slection of items
			
			$this->sucess_message = '<div class="wptu-ultra-warning"><span>'.__("You have to select at least one ticket",'wp-ticket-ultra').'</span></div>';
			
			
		
		}
	}
	
	
	
	/*Create Ticket*/
	public function create_ticket_reply()
	{
		global $wpticketultra, $wptucomplement, $wptu_activity_tracker;
		
		$ticket_id =  $_POST['ticket-id'];
		$ticket_description =  $_POST['wptu_ticket_reply_message'];
		
		$pattern = '/\<script.*\<\/script\>/iU'; //notice the U flag - it is important here
		$ticket_description = preg_replace($pattern, '', $ticket_description);
		
		$pattern = '/\<script.*\<\/script\>/iU'; //notice the U flag - it is important here
		$ticket_description = preg_replace($pattern, '', $ticket_description);

		$ticket_temp_upload_folder =  $_POST['wptu_temp_ticket_id'];
		
		$ticket_reply_status =  $_POST['ticket_status_reply'];		
		
		$current_user = $wpticketultra->userpanel->get_user_info();
		$user_id = $current_user->ID;
		
		$is_client = $wpticketultra->profile->is_client($user_id);
		
		
		
		if($ticket_id !='' && $ticket_description !='')	// Let's create the reply			
		{
			
			//echo "called ticket";						
			$site_date = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
						
			$t_data = array(									
								 'reply_ticket_id' => $ticket_id,
								 'reply_user_id' => $user_id,	
								 'reply_message' => $ticket_description,						
								 'reply_date' => $site_date
								 
								 ); 
								 
									
			
			$reply_id = $this->create_ticket_reply_db($t_data);
			
						
			// 1 move files
			$this->ticket_reply_move_files($reply_id, $ticket_temp_upload_folder);
					
			// 2 update files path on server
			
			if($is_client && !is_admin())
			{
				//the reply is posted by the user				
				$this->update_ticket_status ($ticket_id,2);
				
				//track event				
				if(isset($wptu_activity_tracker))				
				{
					$wptu_activity_tracker->create_action(2, $user_id, $ticket_id , $reply_id);	
				
				}	
						
			
			}else{
				
				//the reply is posted by staff member or admin
				$this->update_ticket_status ($ticket_id,3);
				
				//update has first reply					
				$this->update_ticket_has_first_reply ($ticket_id,'1');
				
				//Do i have to update the owner"
				
				$ticket = $this->get_one($ticket_id);
				
				if($ticket->ticket_staff_id=='0'){
					
					//update owner					
					$this->update_ticket_owner ($ticket_id,$user_id);				
				}
				
				//track event				
				if(isset($wptu_activity_tracker))				
				{
					$wptu_activity_tracker->create_action(3, $user_id, $ticket_id , $reply_id);	
				
				}					
			
			}
			
			//update last replier					
			$this->update_ticket_last_replier ($ticket_id,$user_id);
			
			//update last mod date					
			$this->update_ticket_last_mod ($ticket_id, $site_date);	
			
			//update if this is the first reply			
			if($ticket->ticket_first_reply_time=='0000-00-00 00:00:00' || $ticket->ticket_first_reply_time=='' )
			{
				//update first reply date				
				$this->update_ticket_first_reply($ticket_id, $site_date);				
			
			}
			
			
			//reply and mark as?
			if(isset($ticket_reply_status) && $ticket_reply_status!='') 
			{
				$this->update_ticket_status ($ticket_id,$ticket_reply_status);
				
			}
			
			
			//3 Notify 
			
			$reply = $this->get_one_reply($reply_id);
			
			$wpticketultra->register->handle_reply_notifications($ticket_id, $reply, $is_client);			
			
			$this->sucess_message = '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__("The reply has been posted successfully.",'wp-ticket-ultra').'</span></div>';
		
		}			
								
				
	}
	
	function nicetime($date)
	{
		if(empty($date)) {
			return "No date provided";
				}
	   
		$periods         = array(__("second", 'wp-ticket-ultra'), 
							     __("minute", 'wp-ticket-ultra'), 
								 __("hour", 'wp-ticket-ultra'), 
								 __("day", 'wp-ticket-ultra'), 
								 __("week", 'wp-ticket-ultra'), 
								 __("month", 'wp-ticket-ultra'), 
								 __("year", 'wp-ticket-ultra'), 
								 __("decade", 'wp-ticket-ultra'));
		$lengths         = array("60","60","24","7","4.35","12","10");
	   
		$now             = time();
		$now =  current_time( 'timestamp', 0 );
		$unix_date         = strtotime($date);
		
		
	   
		   // check validity of date
		if(empty($unix_date)) {   
			return "Bad date";
		}
	
		// is it future date or past date
		if($now > $unix_date) {   
			$difference     = $now - $unix_date;
			$tense         =  __("ago", 'wp-ticket-ultra');
		   
		} else {
			$difference     = $unix_date - $now;
			$tense         =  __("from now", 'wp-ticket-ultra');
		}
	   
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
	   
		$difference = round($difference);
	   
		if($difference != 1) 
		{
			$periods[$j].= "s";
		}
	   
		return "$difference $periods[$j] {$tense}";
	}
	
	public function update_ticket_has_first_reply ($id,$value)
	{
		global $wpdb,  $wpticketultra;
		
		$query = 'UPDATE ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_first_reply = "'.$value.'" WHERE ticket_id = "'.$id.'"';
		$wpdb->query( $query );
	
	}
	
	public function update_ticket_last_mod ($id,$date)
	{
		global $wpdb,  $wpticketultra;
		
		$query = 'UPDATE ' . $wpdb->prefix . $this->table_prfix.'_tickets SET 	ticket_date_last_change = "'.$date.'" WHERE ticket_id = "'.$id.'"';
		$wpdb->query( $query );
	
	}
	
	public function update_ticket_first_reply ($id,$date)
	{
		global $wpdb,  $wpticketultra;
		
		$query = 'UPDATE ' . $wpdb->prefix . $this->table_prfix.'_tickets SET 	ticket_first_reply_time = "'.$date.'" WHERE ticket_id = "'.$id.'"';
		$wpdb->query( $query );
	
	}
	
	
	public function update_ticket_last_replier ($id,$user)
	{
		global $wpdb,  $wpticketultra;
		
		$query = 'UPDATE ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_last_reply_staff_id = "'.$user.'" WHERE ticket_id = "'.$id.'"';
		$wpdb->query( $query );
	
	}
	
	public function update_ticket_owner ($id,$user)
	{
		global $wpdb,  $wpticketultra;
		
		$query = 'UPDATE ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_staff_id = "'.$user.'" WHERE ticket_id = "'.$id.'"';
		$wpdb->query( $query );
	
	}
	
	/*Create Ticket*/
	public function create_ticket_reply_db($orderdata)
	{
		global $wpdb,  $wpticketultra;
		
		extract($orderdata);
		
		//update database
		$query = "INSERT INTO " . $wpdb->prefix .$this->table_prfix."_ticket_replies (
		`reply_ticket_id`,`reply_user_id`, `reply_message`, `reply_date` ) VALUES ('$reply_ticket_id','$reply_user_id','$reply_message','".$reply_date."')";
		
		//echo $query;
		
		//echo $query;						
		$wpdb->query( $query );		
		
		return $wpdb->insert_id;				
						
	}
	
	/*Create Ticket*/
	public function create_ticket($orderdata)
	{
		global $wpdb,  $wpticketultra;
		
	
		extract($orderdata);
		
		//update database 
		$query = "INSERT INTO " . $wpdb->prefix .$this->table_prfix."_tickets (
		`ticket_user_id`, 
		`ticket_staff_id`,
        `ticket_department_id`, 
        `ticket_subject`, 
        `ticket_message` ,
        `ticket_date` 	,
        `ticket_date_last_change` ,
		`ticket_priority` ,
		 `ticket_key`, `ticket_website_id` , 
		 `ticket_resolved_within` ,		
		`ticket_first_reply_within`,
		`ticket_woocomerce_prod_id`,
		`ticket_woocomerce_order_id`
        
        ) VALUES ('$user_id', '$staff_id','$department','$subject','$message','".$date."', '".$date."', '$priority',  '$transaction_key', '$site_id' , '$resolved_within', '$reply_within' , '$ticket_woocomerce_prod_id' , '$ticket_woocomerce_order_id')"; 
		
		//echo $query;						
		$wpdb->query( $query );		
		
		return $wpdb->insert_id;				
						
	}
	
	
	function frm_create_ticket ($atts) 
	{
		$atts2 = $atts;
		extract( $atts2, EXTR_SKIP );
		
		//turn on output buffering to capture script output
        ob_start();		
		include(wptu_path."templates/create_ticket.php");
        $content = ob_get_clean();		
		return $content ;			
	}
	
	function get_me_wphtml_editor($meta, $content)
	{
		// Turn on the output buffer
		ob_start();
		
		$editor_id = $meta;				
		$editor_settings = array('media_buttons' => false , 'textarea_rows' => 18 , 'teeny' =>true); 
							
					
		wp_editor( $content, $editor_id , $editor_settings);
		
		// Store the contents of the buffer in a variable
		$editor_contents = ob_get_clean();
		
		// Return the content you want to the calling function
		return $editor_contents;

	
	
	}
	
	function edit_ticket($ticket_id, $user_id){
		
		global $wpticketultra;		
		
		//turn on output buffering to capture script output
        ob_start();
		
        //include the specified file			
		$theme_path = get_template_directory();		
		
		if(file_exists($theme_path."/wptu/edit_ticket.php"))
		{
			
			include($theme_path."/wptu/edit_ticket.php");
		
		}else{
			
			include(wptu_path.'/templates/edit_ticket.php');
		
		}		
		//assign the file output to $content variable and clean buffer
        $content = ob_get_clean();
		return  $content;		
	
	}
	
	
	
	
	
	
	
	public function ini_module()
	{
		global $wpdb;	
		
		
	}
	
	public function update_ticket_meta($ticket_id, $key, $value)
	{
		
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets_meta  WHERE meta_ticket_id = "'.$ticket_id.'"  AND meta_ticket_name= "'.$key.'" ' ;				
		$rows = $wpdb->get_results($sql);	
		
		
		if ( !empty( $rows ))
		{
			$query = "UPDATE " . $wpdb->prefix .$this->table_prfix."_tickets_meta SET meta_ticket_value = '$value' WHERE meta_ticket_name = '$key' AND meta_ticket_id = '$ticket_id' ";
			$wpdb->query( $query );		
		
		}else{
			
			$query = "INSERT INTO " . $wpdb->prefix .$this->table_prfix."_tickets_meta ( meta_ticket_value, meta_ticket_name ,meta_ticket_id ) VALUES('".$value."' , '".$key."', '".$ticket_id."') ";
			$wpdb->query( $query );
		
		}
		
	
	}
	
		
	
	
	public function get_ticket_meta($ticket_id, $key)
	{
		
		global $wpdb, $wpticketultra;
		
		$html='';
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prfix. '_tickets_meta  WHERE meta_ticket_id = "'.$ticket_id.'"  AND meta_ticket_name= "'.$key.'" ' ;				
		$rows = $wpdb->get_results($sql);	
		
		
		if ( !empty( $rows ))
		{
			foreach ( $rows as $row )
			{				
				$html =stripslashes($row->meta_ticket_value);		
			
			}	
		
		}
		
		return $html;		
	
	}
	
	public function can_delete_replies($user_id)
	{
		global $wpdb, $wpticketultra;	
		
		$is_client = $wpticketultra->profile->is_client($user_id);		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);
		$res = false;
		
		if(!$is_client || $is_super_admin)		
		{
			
			$res = true;
						
		}else{
			
			$res = false;
			
		
		}
		
		return $res;
		
	}
	
	public function get_ticket_replies($ticket_id)
	{
		
		global $wpdb, $wpticketultra;		
		
		$current_user = $wpticketultra->userpanel->get_user_info();
		$user_id = $current_user->ID;
			
		
		$datetime_format =  $wpticketultra->get_date_to_display();
				
		$html='';		
			
		$sql = ' SELECT rep.*, usu.* FROM ' . $wpdb->prefix . $this->table_prfix.'_ticket_replies as rep ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = rep.reply_user_id)";					
		$sql .= " WHERE usu.ID = rep.reply_user_id AND rep.	reply_ticket_id = '".$ticket_id."'" ;		
		$sql .= " ORDER BY rep.reply_id DESC" ;	
		
		$rows = $wpdb->get_results($sql);	
		
		
		if ( !empty( $rows ))
		{
			$html .='<ul>';
			
			foreach ( $rows as $reply )
			{
				
				$submited_on=  date($datetime_format, strtotime($reply->reply_date));
				$nice_time_last_reply = $this->nicetime($reply->reply_date);

				
				$is_client = $wpticketultra->profile->is_client($reply->ID);
				
				if($is_client){
					
					$user_legend = __('User','wp-ticket-ultra');
				
				}else{
					
					$user_legend = __('Staff','wp-ticket-ultra');
					
				}
				
				$ticket_message_format = $wpticketultra->text_message_formatting(nl2br($reply->reply_message));
				
				//get poster
								
				$html .='<li id="wptu-reply-unique-id-box-'.$reply->reply_id.'">';
				
				
					$html .='<div class="wptu-reply-col1">';
					
						$html .='<div class="wptu-reply-staff-info">';

							$html .='<div class="wptu-avatar">'.$wpticketultra->profile->get_user_pic( $reply->ID, 80, 'avatar', null, null, false). '</div>';		
						
							$html .='<h2>'.$reply->display_name.'</h2>';
							$html .='<div class="wptu-u-type">'.$user_legend .'</div>';
						
						$html .='</div>';
					
					
					$html .='</div>';
					
					
					$html .='<div class="wptu-reply-col2">';
					
					
					if($this->can_delete_replies($user_id))
					{						
						$html .='<span class="wptu-delete-ticket-conver"><a href="#" title="'.__('Delete','wp-ticket-ultra').'"  class="wptu-del-reply" reply-id="'.$reply->reply_id.'"><i class="fa fa-times" aria-hidden="true"></i></a>
</span>';
			        }
					
						$html .='<div class="wptu-reply-details">';
						
							$html .= '<i class="fa fa-calendar-o"></i> '.__('Posted on: ','wp-ticket-ultra').$submited_on.' (<span class="wptu-ticket-last-update">'.$nice_time_last_reply.'</span>)';			
					
						$html .='</div>';
						
						$html .='<div class="wptu-reply-text">';
						
							$html .=$ticket_message_format;		
					
						$html .='</div>';			
						
						$html .=$this->get_reply_images($reply->reply_id);		
					
					$html .='</div>';
				
						
				
				$html .='</li>';			
						
			
			}
			
			$html .='</ul>';
			
				
		
		}else{
			
			
		}
		
		return $html;		
	
	}
	
	function get_reply_images ($reply_id) 
	{
		
		global $wpdb, $wpticketultra;	
		
		$html ='';
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prfix.'_ticket_reply_attachments ' ;						
		$sql .= " WHERE attachment_reply_id = '".$reply_id."'" ;	
				
					
		$rows = $wpdb->get_results($sql);	
		
		
		if ( !empty( $rows ))
		{
			$html .='<div class="wptu-reply-images">';
			
			$html .='<div class="wptu-reply-img-header">';
			$html .=__("Attached Files ","wpticku")."(".count($rows)."):";
			$html .='</div>';
			
			$html .='<ul>';
			
				foreach ( $rows as $image )
				{
					
					$html .='<li id="wptu-attached-image-id-'.$image->attachment_id.'">';
					$html .=$this->get_ticket_image_url($image->attachment_file, $reply_id, $image->attachment_id);
					$html .='</li>';
					
				
				} //endfor
			
			
			$html .='</ul>';			
			$html .='</div>';
		
		}else{
		
		} //end if
				
		
		return $html;
	
	}
	
	function get_reply_file ($file_id) 
	{
		
		global $wpdb, $wpticketultra;	
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prfix.'_ticket_reply_attachments ' ;						
		$sql .= " WHERE attachment_id = '".$file_id."'" ;	
				
					
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	}
	
	function get_one_reply ($reply_id) 
	{
		
		global $wpdb, $wpticketultra;	
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prfix.'_ticket_replies ' ;						
		$sql .= " WHERE reply_id = '".$reply_id."'" ;	
		
					
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	}
	
	function delete_ticket() 
	{
		
		global $wpdb, $wpticketultra;
		
		$upload_dir = wp_upload_dir(); 
		$upload_folder =   $upload_dir['basedir'];
		
		$dest_upload_folder = $this->wptu_reply_files;
		
		$ticket_id = $_POST['ticket_id'];
		
		$this->trash_ticket_bulk ($ticket_id) ;	
		
		
		
		die();		
		
		
	}
	
	function trash_ticket() 
	{
		
		global $wpdb, $wpticketultra;
		
				
		$ticket_id = $_POST['ticket_id'];
		
		$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_status  ="7", ticket_first_reply  ="1" WHERE ticket_id = "%d" ;',array( $ticket_id));
		
		//echo $sql;		
		$results = $wpdb->query($sql);
		
		die();		
		
		
	}
	
	function mark_as_resolved_confirm() 
	{
		global $wpdb, $wpticketultra;
				
		$ticket_id = $_POST['ticket_id'];
		
		$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_status  ="5", ticket_first_reply  ="1"  WHERE ticket_id = "%d" ;',array( $ticket_id));	
		
		///echo $sql;
			
		$results = $wpdb->query($sql);
		
		
		
		die();	
	}
	
	function mark_as_closed_confirm() 
	{
		global $wpdb, $wpticketultra;
				
		$ticket_id = $_POST['ticket_id'];
		
		$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_status  ="6", ticket_first_reply  ="1"  WHERE ticket_id = "%d" ;',array( $ticket_id));	
		
		//echo $sql;	
		$results = $wpdb->query($sql);
		
		die();	
	}
	
		
	function trash_ticket_bulk ($ticket_id) 
	{
		
		global $wpdb, $wpticketultra;
		
		$upload_dir = wp_upload_dir(); 
		$upload_folder =   $upload_dir['basedir'];
		
		$dest_upload_folder = $this->wptu_reply_files;
				
		//delete ticket meta

		$sql = ' DELETE FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets_meta ' ;						
		$sql .= " WHERE meta_ticket_id = '".$ticket_id."'" ;
		$rows = $wpdb->get_results($sql);
		
		
		
		$file = $this->get_reply_file($file_id);
		
		if($file->attachment_reply_id==$reply_id && $file->attachment_id==$file_id )
		{
			//delete file
			$file_to_delete = $upload_folder."/".$dest_upload_folder.'/'.$file->attachment_file;
			
			if(unlink($file_to_delete))
			{
				//delete database
				$sql = ' DELETE FROM ' . $wpdb->prefix . $this->table_prfix.'_ticket_reply_attachments ' ;						
				$sql .= " WHERE attachment_id = '".$file_id."'" ;
				$rows = $wpdb->get_results($sql);
				
			}else{
				
				echo "no file ";
				
			
			}
			
			
			
		}
		
			
		
		
	}
	
	function delete_ticket_reply_confirm () 
	{
		
		global $wpdb, $wpticketultra;
		
		$reply_id = $_POST['reply_id'];
		$sql = ' DELETE FROM ' . $wpdb->prefix . $this->table_prfix.'_ticket_replies ' ;						
		$sql .= " WHERE reply_id = '".$reply_id."'" ;
		$rows = $wpdb->get_results($sql);
		die();
		
	}
	
	function delete_reply_file () 
	{
		
		global $wpdb, $wpticketultra;
		
		$upload_dir = wp_upload_dir(); 
		$upload_folder =   $upload_dir['basedir'];
		
		$dest_upload_folder = $this->wptu_reply_files;
		
		$file_id = $_POST['attachment_id'];	
		$reply_id = $_POST['reply_id'];
		
		$file = $this->get_reply_file($file_id);
		
		if($file->attachment_reply_id==$reply_id && $file->attachment_id==$file_id )
		{
			//delete file
			$file_to_delete = $upload_folder."/".$dest_upload_folder.'/'.$file->attachment_file;
			
			if(unlink($file_to_delete))
			{
				//delete database
				$sql = ' DELETE FROM ' . $wpdb->prefix . $this->table_prfix.'_ticket_reply_attachments ' ;						
				$sql .= " WHERE attachment_id = '".$file_id."'" ;
				$rows = $wpdb->get_results($sql);
				
			}else{
				
				echo "no file ";
				
			
			}
			
			
			
		}
		
		die();
		
	}
	
	
	function get_ticket_image_url ($filename, $reply_id , $attachment_id) 
	{
		
		global $wpdb, $wpticketultra;
		
		$dest_upload_folder = $this->wptu_reply_files; 		
		$upload_dir = wp_upload_dir(); 
		$url_folder =   $upload_dir['baseurl'];
		//print_r($upload_dir);
		
		$url = $url_folder.'/'.$dest_upload_folder.'/'.$filename;
		
		$html = '<span class="wptu-del-reply-attach"><a href="#" id="wptu-delete-reply-file" title="'.__("Delete File","wpticku").'" reply-id="'.$reply_id.'" attachment-id="'.$attachment_id.'"><i class="fa fa-trash-o"></i></a></span>'.'<a href="'.$url.'" target="_blank">'.$filename.'</a>';
		
		return $html;
	
	}
	
	function get_ticket_edition_form_fields ($ticket_id, $department_id) 
	{
		
		global $wpdb, $wpticketultra;	
			
		
		$display ='';
		
		$custom_form = 'wptu_profile_fields_'.$department_id;		
		$array = get_option($custom_form);			
		$fields_set_to_update =$custom_form;
		
		if(!is_array($array)){$array = array();}

		foreach($array as $key=>$field) 
		{		     
		    $exclude_array = array('user_pass', 'user_pass_confirm', 'user_email');
		    if(isset($field['meta']) && in_array($field['meta'], $exclude_array))
		    {
		        unset($array[$key]);
		    }
		}
		
		$i_array_end = end($array);
		
		if(isset($i_array_end['position']))
		{
		    $array_end = $i_array_end['position'];
		    
			if (isset($array[$array_end]['type']) && $array[$array_end]['type'] == 'seperator') 
			{
				if(isset($array[$array_end]))
				{
					unset($array[$array_end]);
				}
			}
		}
		
		$count_fields = count($array);
		
		if($count_fields>0){
			
			/*Display custom profile fields added by the user*/		
			foreach($array as $key => $field) 
			{
	
				extract($field);
				
				// WP 3.6 Fix
				if(!isset($deleted))
					$deleted = 0;
				
				if(!isset($private))
					$private = 0;
				
				if(!isset($required))
					$required = 0;
				
				$required_class = '';
				$required_text = '';
				
				if($required == 1 )
				{				
					$required_class = 'validate[required] ';
					$required_text = '(*)';				
				}
				
				
				/* This is a Fieldset seperator */
							
				/* separator */
				if ($type == 'separator' && isset($array[$key]['show_in_register']) ) 
				{
					   $display .= '<div class="wptu-profile-separator">'.$name.'</div>';
					   
				}
				
						
				//check if display emtpy				
					
				if ($type == 'usermeta' &&  isset($array[$key]['show_in_register'])) 
				{
									
					$display .= '<div class="wptu-profile-field">';
					
					/* Show the label */
					if (isset($array[$key]['name']) && $name)
					 {
						$display .= '<label class="wptu-field-type" for="'.$meta.'">';	
						
						if (isset($array[$key]['icon']) && $icon) 
						{
							
								$display .= '<i class="fa fa-' . $icon . '"></i>';
								
						} else {
							
							   // $display .= '<i class="fa fa-icon-none"></i>';
						}
						
						
												
						$tooltipip_class = '';					
						if (isset($array[$key]['tooltip']) && $tooltip)
						{
							$qtip_classes = 'qtip-light ';	
							$qtip_style = '';					
						
							 //$tooltipip_class = '<a class="'.$qtip_classes.' uultra-tooltip" title="' . $tooltip . '" '.$qtip_style.'><i class="fa fa-info-circle reg_tooltip"></i></a>';
						} 
						
												
						$display .= '<span>'.$name. ' '.$required_text.' '.$tooltipip_class.'</span></label>';
						
						
					} else {
						
						$display .= '<label class="">&nbsp;</label>';
					}
					
					$display .= '<div class="wptu-field-value">';
						
						switch($field) {
						
							case 'textarea':
								$display .= '<textarea class="'.$required_class.' wptu-custom-field wptu-input wptu-input-text-area" rows="10" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'">'.$this->get_ticket_meta($ticket_id, $meta).'</textarea>';
								break;
								
							case 'text':
								$display .= '<input type="text" class="'.$required_class.' wptu-custom-field wptu-input"  name="'.$meta.'" id="'.$meta.'" value="'.$this->get_ticket_meta($ticket_id, $meta).'"  title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
								break;							
								
							case 'datetime':						
								$display .= '<input type="text" class="'.$required_class.' wptu-custom-field wptu-input wptu-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_ticket_meta($ticket_id, $meta).'"  title="'.$name.'" />';
								break;
								
							case 'select':
													
								if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' )
								
								{
									$loop = $wpticketultra->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
									
								}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
									
																
									$loop = $wpticketultra->uultra_one_line_checkbox_on_window_fix($choices);
										
									
								}
								
								if (isset($loop)) 
								{
									$display .= '<select class="'.$required_class.' wptu-custom-field wptu-input" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'">';
									
									foreach($loop as $option)
									{
										
										$option = trim(stripslashes($option));							
										
										$display .= '<option value="'.$option.'" '.selected( $wpticketultra->appointment->get_booking_meta($booking_id, $meta), $option, 0 ).'>'.$option.'</option>';
										
									}
									$display .= '</select>';
								}
								
								break;
								
							case 'radio':	
							
							$display .= '<ul>';						
							
								if($required == 1 && in_array($field, $this->include_for_validation))
								{
									$required_class = "validate[required] radio ";
								}
							
								if (isset($array[$key]['choices']))
								{				
														
									
									 $loop = $wpticketultra->uultra_one_line_checkbox_on_window_fix($choices);
									
								}
								if (isset($loop) && $loop[0] != '') 
								{
								  $counter =0;
								  
									foreach($loop as $option)
									{
										if($counter >0)
											$required_class = '';
										
										$option = trim(stripslashes($option));
										$display .= '<li>';	
										$display .= '<input type="radio" class="'.$required_class.' wptu-custom-field" title="'.$name.'" name="'.$meta.'" id="wptu_multi_radio_'.$meta.'_'.$counter.'" value="'.$option.'" '.checked( $this->get_ticket_meta($ticket_id, $meta), $option, 0 );
										$display .= '/> <label for="wptu_multi_radio_'.$meta.'_'.$counter.'"><span></span>'.$option.'</label>';
										
										$display .= '</li>';	
										
										$counter++;
										
									}
								}
								
								$display .= '</ul>';	
								
								break;
								
							case 'checkbox':
							
							$display .= '<ul>';
							
							
								if($required == 1 && in_array($field, $this->include_for_validation))
								{
									$required_class = "validate[required] checkbox ";
								}						
							
								if (isset($array[$key]['choices'])) 
								{
																	
									 $loop = $wpticketultra->uultra_one_line_checkbox_on_window_fix($choices);
									
									
								}
								
								$saved_choices = $this->get_ticket_meta($ticket_id, $meta);
								$saved_choices = explode(',',$saved_choices);
								$saved_choices=array_map('trim',$saved_choices);
								
								if (isset($loop) && $loop[0] != '') 
								{
								  $counter =0;
								  
									foreach($loop as $option)
									{
									   
									   if($counter >0)
											$required_class = '';
									  
									  $option = trim(stripslashes($option));
									  
									  $display .= '<li>';	
									  
									  $display .= '<div class="wptu-checkbox wptu-custom-field"><input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="wptu_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" ';
										if (in_array($option, $saved_choices )) 
										
										{
											$display .= 'checked="checked"';
											
										}
										$display .= '/> <label for="wptu_multi_box_'.$meta.'_'.$counter.'"> '.$option.'</label> </div>';
										
										$display .= '</li>';	
										
										
										$counter++;
									}
								}
								
								$display .= '</ul>';	
								
								break;	
							
														
							
								
						}
						
						
						if (isset($array[$key]['help_text']) && $help_text != '') 
						{
							$display .= '<div class="wptu-help">'.$help_text.'</div>';
						}
								
						
										
										
						
					$display .= '</div>';
					$display .= '</div>';
					
				}
				
			} //end for each
			
			//update ticket details
			
			$display .= '<div class="wptu-ticket-update-details"> 
			
			          
         <span id="wptu-update-info-msg"></span> <button name="wptu-ticket-update-details-btn" id="wptu-ticket-update-details-btn" class="wptu-btn-update-ticket-details-btn" ><i class="fa fa-pencil-square-o"></i> '.__('UPDATE','wp-ticket-ultra').' </button>     
      </div>';
			
		}else{
		
		} //end if empty
		
		return $display;
	}
	
	public function update_ticket_info()
	{
		
		global $wpdb, $wpticketultra;	
		
		
		$current_user = $wpticketultra->userpanel->get_user_info();
		$staff_id = $current_user->ID;	
		
		$html='';	
			
		$custom_fields = $_POST['custom_fields'];
		$ticket_id = $_POST['ticket_id'];	
		
		$exploded = array();
		parse_str($custom_fields, $exploded);
		
		//print_r($exploded);
		
		//check if this staff membe can update the ticket
		
		//if($this->is_my_ticket($ticket_id, $staff_id))
		//{
		
			foreach($exploded as $field => $value)
			{	
			
				if (is_array($value))   // checkboxes
				{
					$value = implode(',', $value);
				}	
						
							
				//$this->update_ticket_meta($ticket_id, $field, $value);
				
				$this->update_ticket_meta($ticket_id, $field, esc_attr($value));
			
			}
			
			$html .=__("Done! ", 'wp-ticket-ultra');
		
		//}else{
			
			//$html .=__("Not Allowed ", 'wp-ticket-ultra');
		//}
		
		
		echo $html;
		die();
		
				
	
	}
	
	public function update_ticket_priority ($id,$priority)
	{
		global $wpdb,  $wpticketultra;
		
		$query = 'UPDATE ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_priority = "'.$priority.'" WHERE ticket_id = "'.$id.'"';
		$wpdb->query( $query );
	
	}	
	
	public function update_ticket_status ($id,$status)
	{
		global $wpdb,  $wpticketultra;
		
		$query = 'UPDATE ' . $wpdb->prefix . $this->table_prfix.'_tickets SET ticket_status = "'.$status.'" WHERE ticket_id = "'.$id.'"';
		$wpdb->query( $query );
	
	}
	
	public function get_cancel_link_of_appointment ($appointment_key)
	{
		global   $wpticketultra;		
		
		$site_url =site_url("/");		
		$link = $site_url.'?bupcancelappointment='.$appointment_key;
		
		$link = '<a href="'.$link.'">'.$link.'</a>';
		
		return $link;
	
	}
	
	

	
	public function get_one ($ticket_id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT ticket.*, dep.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.	ticket_department_id)";	
				
		$sql .= " WHERE dep.department_id = ticket.	ticket_department_id AND ticket_id = '".$ticket_id."'" ;			
					
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	public function get_one_as_user ($ticket_id, $user_id=null) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority AND ticket.ticket_id = '".$ticket_id."' " ;
		
		//echo $sql;				
					
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	
	public function can_i_see_this_ticket ($ticket_id, $user_id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT ticket.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
		$sql .= " WHERE ticket.ticket_id = '".$ticket_id."' AND   ticket.ticket_user_id =  '".$user_id."' " ;
		
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				$res = true;			
			
			}
			
		}else{
			
			$res = false;
			
		
		}
		
		return $res;	
	
	}
	
	public function get_one_with_key ($key) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prfix. '_tickets  ' ;		
		$sql .= " WHERE ticket_key = '".$key."'" ;			
					
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	
	
	function get_week_date_range ($current_day)
	{
		$range = array();		
		
		$range = array('from' => date("Y-m-d",strtotime('monday this week', $current_day)), 'to' => date("Y-m-d",strtotime("sunday this week", $current_day)));
		
		return $range;

	
	
	}
	
	public function tickets_by_client()
	{	
	
		global $wpdb, $wpticketultra;	
		
		$html = '';		
		
		$user_id = $_POST['client_id'];	
		
		$currency_symbol =  $wpticketultra->get_option('paid_membership_symbol');
		$date_format =  $wpticketultra->get_int_date_format();
		$time_format =  $wpticketultra->get_time_format();
		$datetime_format =  $wpticketultra->get_date_to_display();
	
		
		$html .= '<div class="wptu-welcome-panel">' ;
		
		$tickets_rows = $this->get_all_actives_by_user($user_id);
		
		if (!empty($tickets_rows))
		
		{
		
		
		$html .= ' <table width="100%" class="">
            <thead>
                <tr>
                    <th width="4%" class="bp_table_row_hide" id="bp_table_row_id">'.__('#', 'wp-ticket-ultra').'</th>
                    <th width="13%">'.__('Date', 'wp-ticket-ultra').'</th> ';
                    
                    
                     if(!$is_client ){                   
                     
                    $html .= '  <th width="12%" id="wptu-ticket-col-site">'.__('Site', 'wp-ticket-ultra').'</th>';
                      
                     	} 
                      
                     $html .= '  <th width="14%" id="wptu-ticket-col-department">'.__('Department', 'wp-ticket-ultra').'</th>
                   
                    
                    <th width="14%" id="wptu-ticket-col-staff">'.__('Last Replier', 'wp-ticket-ultra').'</th>
                   
                   
                     <th width="18%">'.__('Subject', 'wp-ticket-ultra').'</th>
                     <th width="12%"  id="wptu-ticket-col-lastupdate">'.__('Last Update', 'wp-ticket-ultra').'</th>
                    <th width="10%">'.__('Priority', 'wp-ticket-ultra').'</th>
                    
                     
                     <th width="14%" id="wptu-ticket-col-status">'.__('Status', 'wp-ticket-ultra').'</th>
					 <th width="14%" id="wptu-ticket-col-actions">'.__('Actions', 'wp-ticket-ultra').'</th>
                    
                </tr>
            </thead>
            
            <tbody>';
            
           
			$filter_name= '';
			$phone= '';
			foreach($tickets_rows as $ticket) {
				
				
				$date_submited=  date($datetime_format, strtotime($ticket->ticket_date));			
				$client_id = $ticket->ticket_user_id;
				
				$status_legend = $wpticketultra->get_status_label($ticket->status_name, $ticket->status_color);
				$priority_legend = $wpticketultra->get_priority_label($ticket->priority_name, $ticket->priority_color);	
				
				$nice_time_last_update = $wpticketultra->ticket->nicetime($ticket->ticket_date_last_change);	
				
				if($ticket->ticket_staff_id=='' || $ticket->ticket_staff_id=='0')
				{
					$owner_legend = __('Unassigned', 'wp-ticket-ultra');
				
				}else{
					
					$owner = get_user_by( 'id', $ticket->ticket_staff_id );					
					$owner_legend = $owner->display_name;					
				
				}
				
				$last_replier = get_user_by( 'id', $ticket->ticket_last_reply_staff_id );	
				$last_replier_label =	$last_replier->display_name;
				
								
								

               $html .= ' <tr>
                    <td class="bp_table_row_hide">'.$ticket->ticket_id.'</td>
                     <td>'. $date_submited.' </td>';        
                     
                      if(!$is_client )
					  { 
                     	$html .= ' <td id="wptu-ticket-col-site">'. $ticket->site_name.' </td>';
                      }
                    
					  $html .= '  <td id="wptu-ticket-col-department">'.$ticket->department_name.' </td>
                     
                      <td id="wptu-ticket-col-staff">'.$last_replier_label .'</td>
                   
                    <td>'.$wpticketultra->cut_string($ticket->ticket_subject,20).' </td>
                     <td  id="wptu-ticket-col-lastupdate">'. $nice_time_last_update.'</td>
                    
                    
                    <td>'.  $priority_legend.'</td>                  
                     
                      <td id="wptu-ticket-col-status">'.$status_legend.'</td>
                   <td> <a href="?page=wpticketultra&tab=ticketedit&see&id='.$ticket->ticket_id.'" class="wptu-appointment-edit-module" appointment-id="'.$ticket->ticket_id.'" title="'.__('Edit','wp-ticket-ultra').'"><i class="fa fa-edit"></i></a>
                   
				
                  
                    </td> </tr>';         
                
              
				} //end for each
				
				 $html .= ' </tbody> </table>';
		
					
			}else{
			
					$html .= " <p>".__("There are no tickets .","wpticku")."</p>";
			} 

          
		
		$html .= '</div>' ;	
		
	
			
		echo $html ;		
		die();		
	
	}
	
	//get open tickets by users
	public function get_current_open_tickets_by_user ($user_id = null) 
	{
		global $wpdb, $wpticketultra;
		
				
		$sql = ' SELECT count(*) as total, ticket.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
						
		$sql .= " WHERE  ticket.ticket_user_id  = '".$user_id."' AND  (ticket.ticket_status = '1' OR  ticket.ticket_status = '2' OR  ticket.ticket_status = '3') " ;
				
		$res = $wpdb->get_results($sql);
		
		//echo $sql;
		
		$total = 0;
		
		foreach ( $res as $ticket )
		{
				$total= $ticket->total;			
			
		}
		
		return $total;
		
			
	}
	
	
	
	//get shared
	public function get_total_active_tickets_by_user ($user_id = null) 
	{
		global $wpdb, $wpticketultra;
		
				
		$sql = ' SELECT count(*) as total, ticket.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
						
		$sql .= " WHERE  ticket.ticket_user_id  = '".$user_id."' AND  ticket.ticket_status <> '6' AND  ticket.ticket_status <> '7' AND  ticket.ticket_status <> '5' " ;
				
		$res = $wpdb->get_results($sql);
		
		$total = 0;
		
		foreach ( $res as $ticket )
		{
				$total= $ticket->total;			
			
		}
		
		return $total;
		
			
	}
	
	//get shared
	public function get_tickets_total_shared ($user_id = null) 
	{
		global $wpdb, $wpticketultra;
		
		$is_client = $wpticketultra->profile->is_client($user_id);		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);		
				
		$sql = ' SELECT count(*) as total, ticket.*, shared.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_tickets_permissions shared ON (shared.perm_ticket_id = ticket.ticket_id)";					
		$sql .= " WHERE shared.perm_ticket_id = ticket.ticket_id AND shared.perm_staff_id = '".(int)$user_id."' " ;			
				
		$res = $wpdb->get_results($sql);
		
		$total = 0;
		
		foreach ( $res as $ticket )
		{
				$total= $ticket->total;			
			
		}
		
		return $total;
		
			
	}
	
		
	public function get_tickets_total_by_status ($user_id = null, $status) 
	{
		global $wpdb, $wpticketultra;
		
		$is_client = $wpticketultra->profile->is_client($user_id);		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);
		
		if(!$is_client)	 // get staff departments
		{
			$departments_ids = $wpticketultra->userpanel->get_all_staff_allowed_deptos_list($user_id);
			//echo "allowed deptos ID: " .$departments_ids;
						
		}	

		
		$sql = ' SELECT count(*) as total, ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority AND ticket.ticket_status = '".$status."' " ;	
		
		if($is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND ticket.ticket_user_id = '".$user_id."' ";
			
		}
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_department_id  IN (".$departments_ids.") ";
			
		}
		
		$sql .= " ORDER BY  ticket.ticket_date_last_change ASC ";
		
		$total = 0;			
				
		$res = $wpdb->get_results($sql);
		
		foreach ( $res as $ticket )
		{
				$total= $ticket->total;			
			
		}
		
		return $total;
		
			
	}
	
	
	public function get_tickets_total_first_reply_needed ($user_id = null, $status) 
	{
		global $wpdb, $wpticketultra;
		
		$is_client = $wpticketultra->profile->is_client($user_id);		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);
		
		if(!$is_client)	 // get staff departments
		{
			$departments_ids = $wpticketultra->userpanel->get_all_staff_allowed_deptos_list($user_id);
		}	
		
		$sql = ' SELECT count(*) as total, ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority AND ticket.ticket_first_reply = '0' AND ticket.ticket_status <> '7'" ;	
		
		if($is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND ticket.ticket_user_id = '".$user_id."' ";
			
		}
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_department_id  IN (".$departments_ids.") ";
			
		}
		
		$sql .= " ORDER BY  ticket.ticket_date_last_change ASC ";
		
		//echo $sql;
					
		$total = 0;	
		$res = $wpdb->get_results($sql);
		
		foreach ( $res as $ticket )
		{
				$total= $ticket->total;			
			
		}
		
		return $total;
		
	}
	
		
	public function get_ticket_with_key($key)
	{
		
		global $wpdb, $wpticketultra;
			
       	
		$sql =  'SELECT appo.*, usu.*, serv.*	 FROM ' . $wpdb->prefix . 'bup_bookings appo  ' ;				
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = appo.booking_staff_id)";	
		$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_services serv ON (serv.service_id = appo.booking_service_id)";		
		$sql .= " WHERE  appo.booking_key = '".$key."'  ";	
			
		$appointments = $wpdb->get_results($sql );	
		
		if ( !empty( $appointments ) )
		{
			
			foreach ( $appointments as  $appointment ) 
			{
				return $appointment;
			
			}
		
		}else{
			
			return false;
			
			
		}
	
	
	}
	
	public function get_all_actives_by_user ($user_id = null) 
	{
		global $wpdb, $wpticketultra;
		
		
		
		$sql = ' SELECT ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority  AND ticket.ticket_status <> '5'   AND ticket.ticket_status <> '6' AND ticket.ticket_status <> '7' "  ;	
		
		
			//display only the client's ticket			
		$sql .= " AND ticket.ticket_user_id = '".$user_id."' ";	
		
		$sql .= " ORDER BY  ticket.ticket_id ASC ";
					
				
		$res = $wpdb->get_results($sql);
		
		return $res;
		
			
	}
	
	
	public function get_first_reply ($user_id = null) 
	{
		global $wpdb, $wpticketultra;
		
		$is_client = $wpticketultra->profile->is_client($user_id);
		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);
		
		if(!$is_client)	 // get staff departments
		{
			$departments_ids = $wpticketultra->userpanel->get_all_staff_allowed_deptos_list($user_id);
						
		}	

		
		$sql = ' SELECT ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority AND ticket.ticket_first_reply = '0' AND ticket.ticket_status <> '7' "  ;	
		
		if($is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND ticket.ticket_user_id = '".$user_id."' ";
			
		}	
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_department_id  IN (".$departments_ids.") ";
			
		}	
		
		$sql .= " ORDER BY  ticket.ticket_id ASC ";
					
				
		$res = $wpdb->get_results($sql);
		
		return $res;
		
			
	}
	
	public function is_ticket_within_allowed_departments ($user_id = null, $ticket) 
	{
		global $wpdb, $wpticketultra;
		
		
		$departments_ids = $wpticketultra->userpanel->get_all_staff_allowed_deptos_list($user_id);
		//echo "allowed deptos ID: " .$departments_ids;	
		
		$deptos_array = array();		
		$deptos_array = explode(',',$departments_ids);
		
		if(in_array($ticket->ticket_department_id,$deptos_array)){
			
			return true;
			
		}else{
			
			return false;
		}
		
		
			
	}
	
	public function get_by_status ($user_id = null, $status) 
	{
		global $wpdb, $wpticketultra;
		
		$is_client = $wpticketultra->profile->is_client($user_id);		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);
		
		if(!$is_client)	 // get staff departments
		{
			$departments_ids = $wpticketultra->userpanel->get_all_staff_allowed_deptos_list($user_id);
			//echo "allowed deptos ID: " .$departments_ids;
						
		}	

		
		$sql = ' SELECT ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority AND ticket.ticket_status = '".$status."' " ;	
		
		if($is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND ticket.ticket_user_id = '".$user_id."' ";
			
		}
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_department_id  IN (".$departments_ids.") ";
			
		}
		
		$sql .= " ORDER BY  ticket.ticket_date_last_change ASC ";
					
				
		$res = $wpdb->get_results($sql);
		
		return $res;
		
			
	}
	
	//latest tickets used for the client
	public function get_my_latest_tickets ($user_id = null) 
	{
		global $wpdb, $wpticketultra;
		
		$is_client = $wpticketultra->profile->is_client($user_id);		
		
		$sql = ' SELECT ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority " ;
		
		$sql .= " AND ticket.ticket_user_id = '".$user_id."' AND ticket.ticket_status <> '7' ";
		
		$sql .= " ORDER BY  ticket.ticket_date_last_change ASC ";
		
		
				
		$res = $wpdb->get_results($sql);
		
		return $res;
		
			
	}
	
	
	/*Get all*/
	public function get_all_filtered ()
	{
		global $wpdb,  $wpticketultra;
		
		$current_user = $wpticketultra->userpanel->get_user_info();
		$user_id = $current_user->ID;
		//echo "USER ID " . $user_id;
		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);

		
		$keyword = "";
		$month = "";
		$day = "";
		$year = "";
		$howmany = "";
		$ini = "";
		
		$special_filter='';
		
		if(isset($_GET["bp_keyword"]))
		{
			$keyword = sanitize_text_field($_GET["bp_keyword"]);		
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
		
		if(isset($_GET["bp_howmany"]))
		{
			$howmany = sanitize_text_field($_GET["bp_howmany"]);		
		}
		
		if(isset($_GET["bp_special_filter"]))
		{
			$special_filter = sanitize_text_field($_GET["bp_special_filter"]);		
		}
		
		if(isset($_GET["bp_status"]))
		{
			$bp_status = sanitize_text_field($_GET["bp_status"]);		
		}
		
		if(isset($_GET["bp_sites"]))
		{
			$bp_sites = sanitize_text_field($_GET["bp_sites"]);		
		}
		
		
				
		$uri= $_SERVER['REQUEST_URI'] ;
		$url = explode("&ini=",$uri);
		
		if(is_array($url ))
		{
			//print_r($url);
			if(isset($url["1"]))
			{
				$ini = $url["1"];
			    if($ini == ""){$ini=1;}
			
			}
		
		}		
		
		
		
		if($howmany == ""){$howmany=50;}
		
		
		$is_client = $wpticketultra->profile->is_client($user_id);
		
		if(!$is_client)	 // get staff departments
		{
			$departments_ids = $wpticketultra->userpanel->get_all_staff_allowed_deptos_list($user_id);
						
		}	

		//get total	
		$sql = ' SELECT count(*) as total, ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority " ;	
		
		if($is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND ticket.ticket_user_id = '".(int)$user_id."' ";
			
		}
		
		
		if($is_client && !$is_super_admin)	
		{
			//display public tickets. Private Tickets are not displayed	
			//$sql .= "  AND ticket.ticket_is_private = '0' ";			
		}	
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_department_id  IN (".$departments_ids.") ";
			
		}
		
		if($bp_status !='')	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_status = '".$bp_status."' ";
			
		}
		
		if($bp_sites !='')	
		{				
			$sql .= " AND ticket.ticket_website_id = '".$bp_sites."' ";
			
		}
		
		if($keyword !='')	
		{				
			$sql .= " AND usu.display_name LIKE '%".$keyword."%' ";
			
		}
		
		
		
		
		if($day!=""){$sql .= " AND DAY(ticket.ticket_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ticket.ticket_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(ticket.ticket_date) = '$year'";}	
		
		$orders = $wpdb->get_results($sql );
		$orders_total = $this->fetch_result($orders);
		$orders_total = $orders_total->total;
		$this->total_result = $orders_total ;
		
		$total_pages = $orders_total;
				
		$limit = "";
		$current_page = $ini;
		$target_page =  site_url()."/wp-admin/admin.php?page=bookingultra&tab=appointments";
		
		$how_many_per_page =  $howmany;
		
		$to = $how_many_per_page;
		
		//caluculate from
		$from = $this->calculate_from($ini,$how_many_per_page,$orders_total );
		
		//get all	
		
		$sql = ' SELECT ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority " ;	
		
		if($is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND ticket.ticket_user_id = '".(int)$user_id."' ";
			
		}
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_department_id  IN (".$departments_ids.") ";
			
		}
		
		if($is_client && !$is_super_admin)	
		{
			//display public tickets. Private Tickets are not displayed	
			//$sql .= "  AND ticket.ticket_is_private = '0' ";			
		}		
		
		if($bp_status !='')	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_status = '".$bp_status."' ";
			
		}
		
		if($bp_sites !='')	
		{				
			$sql .= " AND ticket.ticket_website_id = '".$bp_sites."' ";
			
		}
		
		if($keyword !='')	
		{				
			$sql .= " AND usu.display_name LIKE '%".$keyword."%' ";
			
		}
		
		if($day!=""){$sql .= " AND DAY(ticket.ticket_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ticket.ticket_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(ticket.ticket_date) = '$year'";}	
		
		$sql .= " ORDER BY ticket.ticket_id DESC";		
		
	    if($from != "" && $to != ""){	$sql .= " LIMIT $from,$to"; }
	 	if($from == 0 && $to != ""){	$sql .= " LIMIT $from,$to"; }
		
		//echo $sql;
		
					
		$orders = $wpdb->get_results($sql );
		
		return $orders ;
		
	
	}
	
	/*Get all Shared*/
	public function get_all_shared_filtered ()
	{
		global $wpdb,  $wpticketultra;
		
		$current_user = $wpticketultra->userpanel->get_user_info();
		$user_id = $current_user->ID;
		//echo "USER ID " . $user_id;
		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);

		
		$keyword = "";
		$month = "";
		$day = "";
		$year = "";
		$howmany = "";
		$ini = "";
		
		$special_filter='';
		
if(isset($_GET["bp_keyword"]))
		{
			$keyword = sanitize_text_field($_GET["bp_keyword"]);		
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
		
		if(isset($_GET["bp_howmany"]))
		{
			$howmany = sanitize_text_field($_GET["bp_howmany"]);		
		}
		
		if(isset($_GET["bp_special_filter"]))
		{
			$special_filter = sanitize_text_field($_GET["bp_special_filter"]);		
		}
		
		if(isset($_GET["bp_status"]))
		{
			$bp_status = sanitize_text_field($_GET["bp_status"]);		
		}
		
		if(isset($_GET["bp_sites"]))
		{
			$bp_sites = sanitize_text_field($_GET["bp_sites"]);		
		}		
		
				
		$uri= $_SERVER['REQUEST_URI'] ;
		$url = explode("&ini=",$uri);
		
		if(is_array($url ))
		{
			//print_r($url);
			if(isset($url["1"]))
			{
				$ini = $url["1"];
			    if($ini == ""){$ini=1;}
			
			}
		
		}		
		
		
		
		if($howmany == ""){$howmany=50;}
		
		
		$is_client = $wpticketultra->profile->is_client($user_id);
		

		//get total	
		$sql = ' SELECT count(*) as total, ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.*, shared.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_tickets_permissions shared ON (shared.perm_ticket_id = ticket.ticket_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority AND shared.perm_ticket_id = ticket.ticket_id " ;	
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND shared.perm_staff_id = '".(int)$user_id."' ";
			
		}
		
		
		
		
		
		if($bp_status !='')	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_status = '".$bp_status."' ";
			
		}
		
		if($bp_sites !='')	
		{				
			$sql .= " AND ticket.ticket_website_id = '".$bp_sites."' ";
			
		}
		
		
		if($day!=""){$sql .= " AND DAY(ticket.ticket_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ticket.ticket_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(ticket.ticket_date) = '$year'";}	
		
		$orders = $wpdb->get_results($sql );
		$orders_total = $this->fetch_result($orders);
		$orders_total = $orders_total->total;
		$this->total_result = $orders_total ;
		
		$total_pages = $orders_total;
				
		$limit = "";
		$current_page = $ini;
		$target_page =  site_url()."/wp-admin/admin.php?page=bookingultra&tab=appointments";
		
		$how_many_per_page =  $howmany;
		
		$to = $how_many_per_page;
		
		//caluculate from
		$from = $this->calculate_from($ini,$how_many_per_page,$orders_total );
		
		//get all	
		
		$sql = ' SELECT ticket.*, dep.* , site.* , usu.*  , status.* , depto.*, priority.*, shared.* FROM ' . $wpdb->prefix . $this->table_prfix.'_tickets as ticket ' ;
				
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments dep ON (dep.department_id = ticket.ticket_department_id)";	
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_sites site ON (site.site_id = ticket.ticket_website_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_statuses status ON (status.status_id = ticket.ticket_status)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_departments depto ON (depto.department_id = ticket.ticket_department_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_priorities priority ON (priority.priority_id = ticket.ticket_priority)";
		
			$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prfix."_tickets_permissions shared ON (shared.perm_ticket_id = ticket.ticket_id)";
		
		$sql .= " RIGHT JOIN ".$wpdb->users ." usu ON (usu.ID = ticket.ticket_user_id)";
				
		$sql .= " WHERE dep.department_id = ticket.ticket_department_id AND site.site_id = ticket.ticket_website_id AND usu.ID = ticket.ticket_user_id AND status.status_id = ticket.ticket_status AND  depto.department_id = ticket.ticket_department_id AND priority.priority_id = ticket.ticket_priority AND shared.perm_ticket_id = ticket.ticket_id " ;	
		
		if(!$is_client && !$is_super_admin)	
		{
			//display only the client's ticket			
			$sql .= " AND shared.perm_staff_id = '".(int)$user_id."' ";
			
		}
		
	
		
		if($bp_status !='')	
		{
			//display only ticket assigned to the staff'de partment			
			$sql .= " AND ticket.ticket_status = '".$bp_status."' ";
			
		}
		
		if($bp_sites !='')	
		{				
			$sql .= " AND ticket.ticket_website_id = '".$bp_sites."' ";
			
		}
		
		if($day!=""){$sql .= " AND DAY(ticket.ticket_date) = '$day'  ";	}
		if($month!=""){	$sql .= " AND MONTH(ticket.ticket_date) = '$month'  ";	}		
		if($year!=""){$sql .= " AND YEAR(ticket.ticket_date) = '$year'";}	
		
		$sql .= " ORDER BY ticket.ticket_id DESC";		
		
	    if($from != "" && $to != ""){	$sql .= " LIMIT $from,$to"; }
	 	if($from == 0 && $to != ""){	$sql .= " LIMIT $from,$to"; }
		
		//echo $sql;
		
					
		$orders = $wpdb->get_results($sql );
		
		return $orders ;
		
	
	}
	
	public function fetch_result($results)
	{
		if ( empty( $results ) )
		{
		
		
		}else{
			
			
			foreach ( $results as $result )
			{
				return $result;			
			
			}
			
		}
		
	}
	
	public function calculate_from($ini, $howManyPagesPerSearch, $total_items)	
	{
		if($ini == ""){$initRow = 0;}else{$initRow = $ini;}
		
		if($initRow<= 1) 
		{
			$initRow =0;
		}else{
			
			if(($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch>= $total_items) {
				$initRow = $totalPages-$howManyPagesPerSearch;
			}else{
				$initRow = ($howManyPagesPerSearch * $ini)-$howManyPagesPerSearch;
			}
		}
		
		
		return $initRow;
		
		
	}

	
}
$key = "ticket";
$this->{$key} = new WPTicketUltraTicket();
?>