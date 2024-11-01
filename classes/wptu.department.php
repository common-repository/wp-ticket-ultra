<?php
class WPTicketUltraDepartment
{
	var $mBusinessHours;
	var $mDaysMaping;
	
	var $ajax_p = 'wptu';
	var $table_prefix = 'wptu';
	
	function __construct() 
	{
				
		$this->ini_module();
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_display_departments', array( &$this, 'get_ajax_admin_departments' ));

		//add_action( 'wp_ajax_'.$this->ajax_p.'_display_admin_categories', array( &$this, 'get_ajax_admin_categories' ));
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_get_category_form', array( &$this, 'get_category_add_form' ));	
		add_action( 'wp_ajax_'.$this->ajax_p.'_update_category', array( &$this, 'update_category' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_update_global_business_hours', array( &$this, 'ubp_update_global_business_hours' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_update_staff_business_hours', array( &$this, 'update_staff_business_hours' ));	
			
		add_action( 'wp_ajax_'.$this->ajax_p.'_load_dw_of_staff',  array( &$this, 'get_cate_dw_ajax' ));
		add_action( 'wp_ajax_nopriv_'.$this->ajax_p.'_load_dw_of_staff',  array( &$this, 'get_cate_dw_ajax' ));		
		add_action( 'wp_ajax_'.$this->ajax_p.'_get_cate_dw_admin_ajax',  array( &$this, 'get_cate_dw_admin_ajax' ));	

		add_action( 'wp_ajax_'.$this->ajax_p.'_get_department_add_form',  array( &$this, 'get_department_add_form' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_add_department_confirm',  array( &$this, 'add_department_confirm' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_category',  array( &$this, 'delete_category' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_department_confirm',  array( &$this, 'delete_department_confirm' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_client_get_add_form',  array( &$this, 'client_get_add_form' ));
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_department_form', array( &$this, 'delete_department_form' ));
		
		

	}
	
	public function ini_module()
	{
		global $wpdb;
		   
		   $this->update_table();
	}
	
	function update_table()
	{
		global $wpdb;
		
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_departments (
				`department_id` bigint(20) NOT NULL auto_increment,
				`department_site_id` bigint(20) NOT NULL DEFAULT "1",								
				`department_name` varchar(300) NOT NULL,
				`department_color` varchar(50) NOT NULL,
				`department_order` int(11) NOT NULL DEFAULT "0",
				`department_overdue` int(11) NOT NULL DEFAULT "0",
				`department_native` int(1) NOT NULL DEFAULT "1",
				`department_public` int(1) NOT NULL DEFAULT "1",									 			
				PRIMARY KEY (`department_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_department_staff (
				`depto_id` bigint(20) NOT NULL auto_increment,
				`depto_staff_id` bigint(20) NOT NULL DEFAULT "0",
				`depto_department_id` bigint(20) NOT NULL DEFAULT "0",								
													 			
				PRIMARY KEY (`depto_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );	
			
		
	}
	
	
	public function get_ajax_admin_departments()
	{
		$site_id = NULL;
		$site_id = $_POST['site_id'];
		
		if(isset($_POST['site_id']) && $_POST['site_id'] !=""){
			
			$site_id = $_POST['site_id'];	
		}
		
		$html = $this->get_admin_departments($site_id);	
		echo $html ;		
		die();		
	
	}
	
	public function delete_department_form()
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		
		$html = '';		
	
				
		if(isset($_POST['department_id'])){
			
			$department_id = $_POST['department_id'];	
		}
		
		
		if($department_id!='') 
		{		
			
			
			//get data
			
			$department = $this->get_one_department($department_id);
			$name = $department->department_name;
			$product_id = $department->department_site_id;
			
			
			//get tickets using this priority
			$tickets_count = $this->get_department_tickets_count($department_id);
			
			
			$html .= '<div class="wptu-sect-adm-edit">';		
						
			if($tickets_count==0)
			{
				//we can delete
				$html .= '<p>'. __("Please confirm that you wish to delete this department.",'wp-ticket-ultra').'</p>';
				
				//button to delete priority
				$html .= '<button name="wptu-department-del-conf-btn" id="wptu-department-del-conf-btn" class="wptu-confirm-prioritydel-btn" department-id="'.$department_id.'" department-assign="0"><i class="fa fa-check"></i> '.__("CONFIRM",'wp-ticket-ultra').' </button>';
									
				
			}else{	
			
				$html .= '<strong>'. __("WARNING:",'wp-ticket-ultra').'</strong>'.__(" some tickets are using this department. You will have to assign them a new department.",'wp-ticket-ultra');			
					
				$html .= '<p>'.sprintf(	
				__( "Tickets using this department: %s.", 'wp-ticket-ultra' ),$tickets_count).'</p>';
					
				$html .= '<p>'. __("Assign to the following department: ",'wp-ticket-ultra').'</p>';
				$html .= '<p>'.$this->get_all_to_assign_box($department_id, $product_id).'</p>';	
				
				//button to delete priority
				$html .= '<button name="wptu-department-del-conf-btn" id="wptu-department-del-conf-btn" class="wptu-confirm-prioritydel-btn" department-id="'.$department_id.'" department-assign="1"><i class="fa fa-check"></i> '.__("CONFIRM",'wp-ticket-ultra').' </button>';
				
				
				
				
			}
			
			$html .= '<input type="hidden" name="wptu-department-id" id="wptu-department-id" value="'.$department_id.'" />';				
			
			
			$html .= '</div>';	
			
		}
		
		echo $html ;		
		die();		
	
	}
	
	public function delete_department_confirm()
	{
		
		global $wpdb, $wpticketultra;
		
		$department_id = $_POST['department_id'];
		$new_department_id = $_POST['new_department_id'];
		
		if($new_department_id!='' )
		{		
			//assign new priority to tickets.		
			$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prefix.'_tickets SET ticket_department_id  =%d  WHERE ticket_department_id = %d ;',array($new_department_id,  $department_id));		
			$results = $wpdb->query($sql);
		}		
		
		if($department_id!='' )
		{	
							
			$sql ="DELETE FROM " . $wpdb->prefix . $this->table_prefix.'_departments'. " WHERE department_id=%d ;";			
			$sql = $wpdb->prepare($sql,array($department_id));	
			$rows = $wpdb->query($sql);
		
		}
		
		
		echo $html;
		die();
		
	}
	
	public function get_all_to_assign_box ($department_id = NULL, $product_id = NULL) 
	{
		global $wpdb, $wpticketultra;
		
		$html ='';
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_departments   ' ;		
		$sql .= ' WHERE department_site_id = "'.$product_id.'" ORDER BY department_name ASC  ' ;		
		$res = $wpdb->get_results($sql);
		
		
		$html .= '<select name="ticket_department" id="ticket_department" >';
		$html .= '<option value="" selected="selected">'.__('Select Department','wp-ticket-ultra').'</option>';
		
		foreach ( $res as $department )
		{
			$selected = '';
			
			if($department_id==$department->department_id){continue;}
			
			$html .= '<option  value="'.$department->department_id.'" '.$selected.' >'.$department->department_name.'</option>';
				
		}
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	public function get_department_tickets_count($department_id)
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix . $this->table_prefix.'_tickets WHERE ticket_department_id = "'.$department_id.'" ' ;
		
		$res = $wpdb->get_results($sql);					
				
		foreach ( $res as $row )
		{
				$total= $row->total;			
			
		}
		
		return $total;
	}
	
	public function get_ajax_admin_categories()
	{
		
		if(isset($_POST['department_id']))
		{
			$department_id = $_POST['department_id'];
		
		}else{
			
			$department_id = '';
			
		}
		
		
		$html = $this->get_admin_categories($department_id);	
		echo $html ;		
		die();		
	
	}
	
	
	
	public function get_days_to_display()
	{
		global  $wpticketultra;
		
		$days = $wpticketultra->get_option('bup_calendar_days_to_display');
		
		if($days==''){
			
			$days = 7;				
		}
		
		
		return $days;
		
	}
	
	public function ubp_parse_customizer_texts($text, $service, $provider = NULL , $date_from = NULL)
	{
		global  $wpticketultra;
		
		$time_format = $this->get_time_format();		
				
		$from_at = date($time_format, strtotime($date_from));
		$from_date = $wpticketultra->commmonmethods->formatDate($date_from);
		
		$text = str_replace("[BUP_SERVICE]", $service->service_title,  $text);
		$text = str_replace("[BUP_PROVIDER]", $provider->display_name,  $text);
		
		$text = str_replace("[BUP_AT]", $from_at,  $text);
		$text = str_replace("[BUP_DAY]", $from_date,  $text);
		
		return $text;
		
	
	}
	
	
	
	//this will check if the user is within a special schedule	
	function is_in_special_schedule($staff_id, $day, $from_time, $to_time)
	{
		
		global  $wpdb, $wpticketultra, $bupcomplement;
		
		$from_time = $from_time.':00';
		$to_time = $to_time.':00';
		
		$ret = false;  
				
		if(isset($bupcomplement))
		{
				
			$sql ="SELECT * FROM " . $wpdb->prefix . "bup_staff_availability_rules  
			WHERE special_schedule_date = '".$day."' AND special_schedule_staff_id = %d  AND  (special_schedule_time_to > '".$from_time."'  AND special_schedule_time_from < '".$to_time."'  );";
			
				
			$sql = $wpdb->prepare($sql,array($staff_id));	
			$rows = $wpdb->get_results($sql);
			
			if ( !empty( $rows )) 
			{			
				$ret = true;	
						
			}else{
				
				$ret = false;
				
			}
		
		}
		
		
		return $ret;
		
		
	
	}
	
		
	
	function delete_category()
	{
		
		global  $wpdb, $wpticketultra;
		
		$category = $_POST['cate_id'];
						
		$sql ="DELETE FROM " . $wpdb->prefix . "bup_categories WHERE cate_id=%d ;";			
		$sql = $wpdb->prepare($sql,array($category));	
		$rows = $wpdb->get_results($sql);
		die();
	
	}
	
		
	
	//this will check if the user is in holiday 	
	function is_in_holiday($staff_id, $date)
	{
		
		global  $wpdb, $wpticketultra, $bupcomplement;
		
		
		if(isset($bupcomplement))
		{
			return $bupcomplement->dayoff->is_in_holiday($staff_id, $date);
							
		}else{
			
			return false;		
				
		}		
	
	}
	
	//this will check if the user is in break time 	
	function is_in_break($staff_id, $day, $from_time, $to_time)
	{
		
		global  $wpdb, $wpticketultra;
		
		$from_time = $from_time.':00';
		$to_time = $to_time.':00';
		
		$ret = false;
				
		$sql ="SELECT * FROM " . $wpdb->prefix . "bup_staff_availability_breaks  
		WHERE break_staff_day=%d AND break_staff_id = %d  AND  (break_time_to > '".$from_time."'  AND 	break_time_from < '".$to_time."'  );";
		
			
		$sql = $wpdb->prepare($sql,array($day, $staff_id));	
		$rows = $wpdb->get_results($sql);
		
		if ( !empty( $rows ))
		{			
			$ret = true;			
		}
		
		
		return $ret;
		
		
	
	}
	
	
	function is_staff_available($staff_id, $service_id, $day, $day_to)
	{
		global  $wpdb, $wpticketultra;
		
		//Is the staff member busy?
		$sql ="SELECT * FROM " . $wpdb->prefix . "bup_bookings  
		WHERE booking_staff_id = %d  AND  booking_service_id <> %d AND booking_status <> '2' AND  (booking_time_to > '".$day."'  AND 	booking_time_from < '".$day_to."'  );";	
				
		$sql = $wpdb->prepare($sql,array($staff_id, $service_id));	
		$rows = $wpdb->get_results($sql);			
		$booked = $wpdb->num_rows;	
		
		if ( !empty( $rows )) // the staff member is busy in this time.
		{			
			$busy = true;		
				
		}else{
			
			$busy = false;		
		}
		
		if($busy){
			//echo "HAS MANY Bookings: $booked  - " .$sql.'<br>.';
			}
		
		return $busy;
		
		
		
	}
	
	
	
	function get_time_format()
	{
		global  $wpticketultra;
	
		$data = $wpticketultra->get_option('bup_time_format');
		
		if($data=='')
		{
			$data = 'h:i A';
		
		}
		
		return $data;
	}	
	
	
	function get_availability_for_user($b_staff, $date_from, $b_category)
	{
		
		global $wpdb, $wpticketultra;
		
		
	
	}
	
	function get_prefered_staff($staff_id = null, $service_id)
	{
		global $wpdb, $wpticketultra;
		
		if($staff_id=='')
		{
			//get random staff providing this service			
			$staff_members = array();			
			$staff_members = $this->get_staff_offering_service($service_id);			
			$staff_id = $staff_members[array_rand($staff_members)];	
		
		}
		
		return $staff_id;
	
	}
	
	
	
	public function update_staff_business_hours()
	{
		global $wpdb, $wpticketultra;
		
		$staff_id = $_POST['staff_id'];		
		
		$bup_mon_from = $_POST['bup_mon_from'];
		$bup_mon_to = $_POST['bup_mon_to'];		
		$bup_tue_from = $_POST['bup_tue_from'];
		$bup_tue_to = $_POST['bup_tue_to'];		
		$bup_wed_from = $_POST['bup_wed_from'];
		$bup_wed_to = $_POST['bup_wed_to'];		
		$bup_thu_from = $_POST['bup_thu_from'];
		$bup_thu_to = $_POST['bup_thu_to'];
		$bup_fri_from = $_POST['bup_fri_from'];
		$bup_fri_to = $_POST['bup_fri_to'];		
		$bup_sat_from = $_POST['bup_sat_from'];
		$bup_sat_to = $_POST['bup_sat_to'];		
		$bup_sun_from = $_POST['bup_sun_from'];
		$bup_sun_to = $_POST['bup_sun_to'];
		
		$business_hours = array();
		
		if($bup_mon_from!=''){$business_hours[1] = array('from' =>$bup_mon_from, 'to' =>$bup_mon_to);}
		if($bup_tue_from!=''){$business_hours[2] = array('from' =>$bup_tue_from, 'to' =>$bup_tue_to);}
		if($bup_wed_from!=''){$business_hours[3] = array('from' =>$bup_wed_from, 'to' =>$bup_wed_to);}
		if($bup_thu_from!=''){$business_hours[4] = array('from' =>$bup_thu_from, 'to' =>$bup_thu_to);}
		if($bup_fri_from!=''){$business_hours[5] = array('from' =>$bup_fri_from, 'to' =>$bup_fri_to);}
		if($bup_sat_from!=''){$business_hours[6] = array('from' =>$bup_sat_from, 'to' =>$bup_sat_to);}
		if($bup_sun_from!=''){$business_hours[7] = array('from' =>$bup_sun_from, 'to' =>$bup_sun_to);}
		
		
		if($staff_id!='')
		{
			//clean 			
			$sql = 'DELETE FROM ' . $wpdb->prefix . 'bup_staff_availability  WHERE avail_staff_id="'.(int)$staff_id.'" ';			$wpdb->query($sql);		
			
			
			if($bup_mon_from!='')
			{
				
				$new_record = array('avail_id' => NULL,	'avail_staff_id' => $staff_id,
								'avail_day' => '1','avail_from' => $bup_mon_from,'avail_to'   => $bup_mon_to);
				$wpdb->insert( $wpdb->prefix . 'bup_staff_availability', $new_record, array( '%d', '%s', '%s', '%s', '%s'));			
			}
			
			
			if($bup_tue_from!='')
			{		
			
				//2			
				$new_record = array('avail_id' => NULL,	'avail_staff_id' => $staff_id,
									'avail_day' => '2','avail_from' => $bup_tue_from,'avail_to'   => $bup_tue_to);
						
				$wpdb->insert( $wpdb->prefix . 'bup_staff_availability', $new_record, array( '%d', '%s', '%s', '%s', '%s'));			
			}
			
			if($bup_wed_from!='')
			{			
				//3			
				$new_record = array('avail_id' => NULL,	'avail_staff_id' => $staff_id,
									'avail_day' => '3','avail_from' => $bup_wed_from,'avail_to'   => $bup_wed_to);
						
				$wpdb->insert( $wpdb->prefix . 'bup_staff_availability', $new_record, array( '%d', '%s', '%s', '%s', '%s'));
			
			}
			
			if($bup_thu_from!='')
			{
			
				//4			
				$new_record = array('avail_id' => NULL,	'avail_staff_id' => $staff_id,
									'avail_day' => '4','avail_from' => $bup_thu_from,'avail_to'   => $bup_thu_to);
						
				$wpdb->insert( $wpdb->prefix . 'bup_staff_availability', $new_record, array( '%d', '%s', '%s', '%s', '%s'));			
			}
			
			if($bup_fri_from!='')
			{
		
				//5			
				$new_record = array('avail_id' => NULL,	'avail_staff_id' => $staff_id,
									'avail_day' => '5','avail_from' => $bup_fri_from,'avail_to'   => $bup_fri_to);
						
				$wpdb->insert( $wpdb->prefix . 'bup_staff_availability', $new_record, array( '%d', '%s', '%s', '%s', '%s'));
			
			}
			
			if($bup_sat_from!='')
			{
			
				//6		
				$new_record = array('avail_id' => NULL,	'avail_staff_id' => $staff_id,
									'avail_day' => '6','avail_from' => $bup_sat_from,'avail_to'   => $bup_sat_to);
						
				$wpdb->insert( $wpdb->prefix . 'bup_staff_availability', $new_record, array( '%d', '%s', '%s', '%s', '%s'));
			
			}
			
			
			if($bup_sun_from!='')
			{			
			
				//7		
				$new_record = array('avail_id' => NULL,	'avail_staff_id' => $staff_id,
									'avail_day' => '7','avail_from' => $bup_sun_from,'avail_to'   => $bup_sun_to);
						
				$wpdb->insert( $wpdb->prefix . 'bup_staff_availability', $new_record, array( '%d', '%s', '%s', '%s', '%s'));
			}
			
			
		}
		
		
	
		//print_r($business_hours);			
		
		die();
	
	
	}
	
	
	public function ubp_update_global_business_hours()
	{
		global $wpdb, $wpticketultra;
		
		$bup_mon_from = $_POST['bup_mon_from'];
		$bup_mon_to = $_POST['bup_mon_to'];		
		$bup_tue_from = $_POST['bup_tue_from'];
		$bup_tue_to = $_POST['bup_tue_to'];		
		$bup_wed_from = $_POST['bup_wed_from'];
		$bup_wed_to = $_POST['bup_wed_to'];		
		$bup_thu_from = $_POST['bup_thu_from'];
		$bup_thu_to = $_POST['bup_thu_to'];
		$bup_fri_from = $_POST['bup_fri_from'];
		$bup_fri_to = $_POST['bup_fri_to'];		
		$bup_sat_from = $_POST['bup_sat_from'];
		$bup_sat_to = $_POST['bup_sat_to'];		
		$bup_sun_from = $_POST['bup_sun_from'];
		$bup_sun_to = $_POST['bup_sun_to'];
		
		$business_hours = array();
		
		if($bup_mon_from!=''){$business_hours[1] = array('from' =>$bup_mon_from, 'to' =>$bup_mon_to);}
		if($bup_tue_from!=''){$business_hours[2] = array('from' =>$bup_tue_from, 'to' =>$bup_tue_to);}
		if($bup_wed_from!=''){$business_hours[3] = array('from' =>$bup_wed_from, 'to' =>$bup_wed_to);}
		if($bup_thu_from!=''){$business_hours[4] = array('from' =>$bup_thu_from, 'to' =>$bup_thu_to);}
		if($bup_fri_from!=''){$business_hours[5] = array('from' =>$bup_fri_from, 'to' =>$bup_fri_to);}
		if($bup_sat_from!=''){$business_hours[6] = array('from' =>$bup_sat_from, 'to' =>$bup_sat_to);}
		if($bup_sun_from!=''){$business_hours[7] = array('from' =>$bup_sun_from, 'to' =>$bup_sun_to);}
		
		update_option('bup_business_hours', $business_hours);
		
		die();
	
	
	}
	
	
	
	public function update_category()
	{
		global $wpdb, $wpticketultra;
		
		$category_id = $_POST['category_id'];
		$category_title = $_POST['category_title'];
		$department_id = $_POST['department_id'];		
		$category_color = $_POST['category_color'];
		$category_font_color = $_POST['category_font_color'];
		
		
		if($category_id!='')
		{			
			$sql = 'UPDATE ' . $wpdb->prefix .$this->table_prefix. '_categories  SET cate_name = "'.$category_title.'",			
			cate_department_id	 = "'.$department_id.'",
			cate_color = "'.$category_color.'",
			cate_font_color = "'.$category_font_color.'"
						
			WHERE cate_id="'.(int)$category_id.'" ';
			$wpdb->query($sql);
		
		
		}else{ //this is a new category
			
			
			$new_record = array('cate_id' => NULL,	
								'cate_name' => $category_title,
								'cate_department_id' => $department_id,
								'cate_color' => $category_color,
								'cate_font_color' => $category_font_color);								
									
			$wpdb->insert( $wpdb->prefix .$this->table_prefix. '_categories', $new_record, array( '%d', '%s', '%s', '%s', '%s'));
			
		}
		
		die();
	
	
	}
	
	public function get_department_add_form()
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		$service_id = '';
		$department_id = '';
		$name = '';
		
				
		if(isset($_POST['department_id'])){
			
			$department_id = $_POST['department_id'];	
		}
		
		if($department_id!='') //we are editing
		{		
			$category = '';//$this->get_one_service($service_id);			
			$mess = __('Here you can update the information of this department. Once you have modified the information click on the save button.','wp-ticket-ultra');
			
			//get department data
			
			$department = $this->get_one_department($department_id);
			$name = $department->department_name;
			$color = $department->department_color;
			$site = $department->department_site_id;
			
			
		
		}else{
			
			$mess = __('Here you can create a new department. Once you have filled in the form click on the save button.','wp-ticket-ultra');
			
		
		}
		
		$html = '';
		
		$html .= '<div class="wptu-sect-adm-edit">';
		
		$html .= '<p>'.$mess.'</p>';
		
			$html .= '<div class="wptu-edit-service-block">';						
			
			$html .= '<div class="wptu-field-separator"><label for="wptu-box-title">'.__('Name','wp-ticket-ultra').':</label><input type="text" name="wptu-title" id="wptu-title" class="wptu-common-textfields" value="'.$name.'" /></div>';
			
			$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('Background Color','wp-ticket-ultra').':</label><input name="wptu-category-color" type="text" id="wptu-category-color" value="'.$color.'" class="color-picker" data-default-color=""/></div>';
				
								
			$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('Product','wp-ticket-ultra').':</label>'.$this->get_sites_drop_down($site).'</div>';
				
			$html .= '<input type="hidden" name="wptu-department-id" id="wptu-department-id" value="'.$department_id.'" />';				
			
			
		$html .= '</div>';
		
		
		
		$html .= '</div>';
		
		
			
		echo $html ;		
		die();		
	
	}
	
	
	
	public function add_department_confirm()
	{
		
		global $wpdb, $wptucomplement;	
		
		$html='';	
		
		$department_id = $_POST['department_id'];
		$department_name = $_POST['department_title'];
		$department_color = $_POST['department_color'];
		$department_site_id = $_POST['department_site_id'];
		
		$department_order = 0;
		
		if($department_id=='')		
		{
					
						
			$new_record = array('department_id' => NULL,	
								'department_name' => $department_name,
								'department_site_id' => $department_site_id,
								'department_order' => $department_order,
								'department_color' => $department_color,
								'department_native' => 0);								
			$wpdb->insert( $wpdb->prefix . $this->table_prefix.'_departments', $new_record, array( '%d', '%s' , '%s' , '%d', '%s','%d'));					
						
			$html ='OK INSERT';
		
	    }else{
			
			$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prefix.'_departments SET department_name =%s, department_color =%s , department_site_id =%d  WHERE department_id = %d ;',array($department_name,$department_color, $department_site_id, $department_id));
		
			$results = $wpdb->query($sql);
			$html ='OK';		
			
		}
		
		echo $html;
		die();
		
				
	
	}
	

	
	public function client_get_add_form()
	{		
		
		$html = '';		
		
		$client_id = $_POST['client_id'];		
		$category_name = '';		
				
		if($client_id!='')		
		{
			//get client			
		//	$category = $this->get_one_category( $category_id);
			//$category_name =	$category->cate_name;
		}		
		
		$html .= '<p>'.__('Name:','wp-ticket-ultra').'</p>' ;	
		$html .= '<p><input type="text" id="client_name" value="'.$category_name.'"></p>' ;
		$html .= '<p>'.__('Last Name:','wp-ticket-ultra').'</p>' ;	
		$html .= '<p><input type="text" id="client_last_name" value="'.$category_name.'"></p>' ;
		$html .= '<p>'.__('Email:','wp-ticket-ultra').'</p>' ;	
		$html .= '<p><input type="text" id="client_email" value="'.$category_name.'"></p>' ;
		$html .= '<p id="wptu-add-client-message"></p>' ;		
			
		echo $html ;		
		die();		
	
	}
	
	
	
	
	
	
	public function get_admin_departments($site_id = 1)
	{
		$html = '';
		
		$rows = $this->get_all_departments($site_id);
		
		$html .='<div class="wptu-service-header-bar">';
		$html .='<h3>'.__('Departments','wp-ticket-ultra').' ('.count($rows).')</h3>';
		
		$html .='<span class="wptu-add-service-m"><a href="#" id="wptu-add-department-btn" title="'.__('Add New Department','wp-ticket-ultra').'" ><i class="fa fa-plus"></i></a></span>';
		$html .='</div>';
		
			
		
		if ( !empty( $rows ) )
		{
			$html .= '<table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>';
			
		$html .= '<thead>
                <tr >
				    <th width="2%"><div style:background-color:></div></th>
					 <th width="4%">'.__('ID', 'wp-ticket-ultra').'</div></th>
					 <th width="18%">'.__('Product', 'wp-ticket-ultra').'</div></th>
                    <th width="14%">'.__('Name', 'wp-ticket-ultra').'</th>
                    
                    
					<th width="16%">'.__('Actions', 'wp-ticket-ultra').'</th>
                </tr>
            </thead>
            
            <tbody>';	
			
			foreach ( $rows as $row )
			{
								
				$html .= '<tr>
				    <td><div class="service-color-blet" style="background-color:'.$row->department_color.';" ></div></td>
					<td>'.$row->department_id.'</td>
					<td>'.$row->site_name.'</td>
					
                    <td>'.$row->department_name.'</td>                
                  
					
                   <td>';
				   
				  // if($row->department_native==0){
				   
				  $html .= ' <a href="#" class="wptu-department-delete"  title="'.__('Delete','wp-ticket-ultra').'" department-id="'.$row->department_id.'" ><i class="fa fa-trash-o"></i></a>';
				  
				   //}
				   
				  $html .= ' <a class="wptu-admin-edit-department" href="#" id="" department-id="'.$row->department_id.'" ><span><i class="fa fa-edit fa-lg"></i></span></a></td>';
				  
				  
              $html .= '  </tr>';			
			
			}
		}else{
		
			$html .= '<p>'.__('There are no departments ','wp-ticket-ultra').'</p>';
				
	    }
		
        $html .= '</table>';
		
		return $html ;	
		
	
	}
	
	function get_sites_drop_down($site = null)
	{
		global  $wpticketultra;
		
		$html = '';
		
		$rows = $wpticketultra->site->get_all();	
		
		$html .= '<select name="wptu-sites" id="wptu-sites">';
		
		foreach ( $rows as $row)
		{
			$selected = '';
			if($site==$row->site_id){$selected='selected="selected"';}
		
			$html .= '<option value="'.$row->site_id.'" '.$selected.'>'.$row->site_name.'</option>';
			
		}
		
		$html .= '</select>';
		
		return $html;
	
	}
	
	function get_departments_drop_down($department = null)
	{
		global  $wpticketultra;
		
		$html = '';
		
		$departments_rows = $this->get_all_departments();	
		
		$html .= '<select name="bup-departments" id="bup-departments">';
		
		foreach ( $departments_rows as $depa)
		{
			$selected = '';
			if($department==$depa->department_id){$selected='selected="selected"';}
		
			$html .= '<option value="'.$depa->department_id.'" '.$selected.'>'.$depa->department_name.'</option>';
			
		}
		
		$html .= '</select>';
		
		return $html;
	
	}
	
	
	function get_staff_offering_service($service_id)
	{
		global  $wpticketultra, $wpdb;
		
		$html = array();
		
		$category_id = $_POST['b_category'];		
		
		$sql = ' SELECT serv.*,user.*  FROM ' . $wpdb->users . '  user ' ;		
		$sql .= "RIGHT JOIN ".$wpdb->prefix ."bup_service_rates serv ON (serv.rate_service_id = '".(int)$service_id.
		"')";
		$sql .= ' WHERE user.ID = serv.rate_staff_id' ;					
		$sql .= ' ORDER BY user.display_name ASC  ' ;
		
		$users = $wpdb->get_results($sql);		
		
		if (!empty($users))
		{
			
			foreach($users as $user) 
			{
				$html[$user->ID] = $user->ID;				
				
			}
		
		
		}
		
		
		return $html;
		
	
	}
	
	function get_cate_dw_admin_ajax()
	{
		global  $wpticketultra, $wpdb;
		
		$html = '';
		
		$currency_symbol = $wpticketultra->get_currency_symbol();
		$display_price = $wpticketultra->get_option('price_on_staff_list_front');
		$price_label = '';
		$staff_id = '';
		
		$category_id = '';
		$appointment_id = '';
		if(isset($_POST['b_category']))
		{
			$category_id = $_POST['b_category'];
			
		}
		
		if(isset($_POST['appointment_id']))
		{
			$appointment_id = $_POST['appointment_id'];	
			
		}
		
		
		//get appointment			
		$appointment = $wpticketultra->appointment->get_one($appointment_id);
		$staff_id = $appointment->booking_staff_id;	
		
		$sql = ' SELECT serv.*,user.*  FROM ' . $wpdb->users . '  user ' ;		
		$sql .= "RIGHT JOIN ".$wpdb->prefix ."bup_service_rates serv ON (serv.rate_service_id = '".(int)$category_id.
		"')";
		$sql .= ' WHERE user.ID = serv.rate_staff_id' ;					
		$sql .= ' ORDER BY user.display_name ASC  ' ;
		
		$users = $wpdb->get_results($sql);

	
		$html = '';
		
		$html .= '<div class="field-header">'.__('With','wp-ticket-ultra').'</div>';		
		$html .= '<select name="bup-staff" id="bup-staff">';
		$html .= '<option value="" selected="selected" >'.__('Any', 'wp-ticket-ultra').'</option>';
		
		if (!empty($users))
		{			
			foreach($users as $user) 
			{
				$service_details = $wpticketultra->userpanel->get_staff_service_rate( $user->ID, $category_id );				
				$service_price = 	$service_details['price'];
				
				if($display_price=='' || $display_price=='yes')
				{
					$price_label= '('.$currency_symbol.''.$service_price.')';				
				}
				
				$selected='';
				if($staff_id==$user->ID)
				{
					$selected='selected';				
				}
						
				$html .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.' '.$price_label.'</option>';		
				
				
			}
			$html .= '</select>';
		
		
		}
		
		
		echo $html;
		die();
	
	}
	
	//used when using service_id shortcode only	
	function get_cate_list_front($category_id, $template_id)
	{
		global  $wpticketultra, $wpdb;
		
		$html = '';
		
		$currency_symbol = $wpticketultra->get_currency_symbol();
		$display_price = $wpticketultra->get_option('price_on_staff_list_front');
		$price_label = '';
		
		$filter_id = $_POST['filter_id'];
		
		if($template_id!='')
		{
			$select_label = $wpticketultra->get_template_label("select_provider_label",$template_id);
		
		}else{
			
			$select_label = __('With','wp-ticket-ultra');			
		
		}
		
		$selected = '';	
		
		if($filter_id=='')
		{
			
			$sql = ' SELECT serv.*,user.*  FROM ' . $wpdb->users . '  user ' ;		
			$sql .= "RIGHT JOIN ".$wpdb->prefix ."bup_service_rates serv ON (serv.rate_service_id = '".$category_id.
			"')";
			$sql .= ' WHERE user.ID = serv.rate_staff_id' ;					
			$sql .= ' ORDER BY user.display_name ASC  ' ;
		
		}else{
			
			$sql = ' SELECT serv.*, user.*, staff_location.*  FROM ' . $wpdb->users . '  user ' ;		
			$sql .= "RIGHT JOIN ".$wpdb->prefix ."bup_service_rates serv ON (serv.rate_service_id = '".$category_id.
			"')";
			$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_filter_staff staff_location ON (staff_location.fstaff_staff_id = user.ID)";
			
			$sql .= " WHERE user.ID = serv.rate_staff_id AND staff_location.fstaff_staff_id = user.ID AND  staff_location.fstaff_location_id = '".$filter_id."' " ;					
			$sql .= ' ORDER BY user.display_name ASC  ' ;
			
		}
		
		
		$users = $wpdb->get_results($sql);

	
		$html = '';
		
		$html .= '<label>'.$select_label.'</label>';		
		$html .= '<select name="bup-staff" id="bup-staff">';
		$html .= '<option value="" selected="selected" >'.__('Any', 'wp-ticket-ultra').'</option>';
		
		if (!empty($users))
		{			
			foreach($users as $user) 
			{
				$service_details = $wpticketultra->userpanel->get_staff_service_rate( $user->ID, $category_id );				
				$service_price = 	$service_details['price'];
				
				if($display_price=='' || $display_price=='yes')
				{
					$price_label= '('.$currency_symbol.''.$service_price.')';				
				}
						
				$html .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.' '.$price_label.'</option>';		
				
				
			}
			$html .= '</select>';
		
		
		}
		
		
		echo $html;
		die();
	
	}
	
	function get_cate_dw_ajax()
	{
		global  $wpticketultra, $wpdb;
		
		$html = '';
		
		$currency_symbol = $wpticketultra->get_currency_symbol();
		$display_price = $wpticketultra->get_option('price_on_staff_list_front');
		$price_label = '';
		
		$category_id = $_POST['b_category'];
		$filter_id = $_POST['filter_id'];
		$template_id = $_POST['template_id'];
		
		if($template_id!='')
		{
			$select_label = $wpticketultra->get_template_label("select_provider_label",$template_id);
		
		}else{
			
			$select_label = __('With','wp-ticket-ultra');			
		
		}
		
		$selected = '';	
		
		if($filter_id=='')
		{
			
			$sql = ' SELECT serv.*,user.*  FROM ' . $wpdb->users . '  user ' ;		
			$sql .= "RIGHT JOIN ".$wpdb->prefix ."bup_service_rates serv ON (serv.rate_service_id = '".(int)$category_id.
			"')";
			$sql .= ' WHERE user.ID = serv.rate_staff_id' ;					
			$sql .= ' ORDER BY user.display_name ASC  ' ;
		
		}else{
			
			$sql = ' SELECT serv.*, user.*, staff_location.*  FROM ' . $wpdb->users . '  user ' ;		
			$sql .= "RIGHT JOIN ".$wpdb->prefix ."bup_service_rates serv ON (serv.rate_service_id = '".(int)$category_id.
			"')";
			$sql .= " RIGHT JOIN ". $wpdb->prefix."bup_filter_staff staff_location ON (staff_location.fstaff_staff_id = user.ID)";
			
			$sql .= " WHERE user.ID = serv.rate_staff_id AND staff_location.fstaff_staff_id = user.ID AND  staff_location.fstaff_location_id = '".$filter_id."' " ;					
			$sql .= ' ORDER BY user.display_name ASC  ' ;
			
		}
		
		
		$users = $wpdb->get_results($sql);

	
		$html = '';
		
		$html .= '<label>'.$select_label.'</label>';		
		$html .= '<select name="bup-staff" id="bup-staff">';
		$html .= '<option value="" selected="selected" >'.__('Any', 'wp-ticket-ultra').'</option>';
		
		if (!empty($users))
		{			
			foreach($users as $user) 
			{
				$service_details = $wpticketultra->userpanel->get_staff_service_rate( $user->ID, $category_id );				
				$service_price = 	$service_details['price'];
				
				if($display_price=='' || $display_price=='yes')
				{
					$price_label= '('.$currency_symbol.''.$service_price.')';				
				}
						
				$html .= '<option value="'.$user->ID.'" '.$selected.'>'.$user->display_name.' '.$price_label.'</option>';		
				
				
			}
			$html .= '</select>';
		
		
		}
		
		
		echo $html;
		die();
	
	}
	
	function get_categories_drop_down_public($site_id = null)
	{
		global  $wpticketultra;
		
		$html = '';
		
		$departments = $this->get_all_departments($site_id);
		
		
		$html .='<ul>';
		foreach ( $departments as $department )
		{
			$html .='<li>';			
			
			$html .='<input type="radio" class="validate[required] radio wptu-location-checked wptu-location-front " value="'.$department->department_id.'" depto-id="'.$department->department_id.'" name="wptu-category" id="wptu-category-'.$department->department_id.'" data-errormessage-value-missing="'.__(' * Please select a category!','wp-ticket-ultra').'"><label for="wptu-category-'.$department->department_id.'"><span>'.$department->department_name.'</span></label>';	
			
			$html .='</li>';
			
		}
		
		$html .='</ul>';
		
		
		
		return $html;
	
	}
	
	function get_categories_drop_down_admin($service_id = null)
	{
		global  $wpticketultra;
		
		$html = '';
		
		$cate_rows = $this->get_all_categories();
		
		
		$html .= '<select name="bup-category" id="bup-category">';
		$html .= '<option value="" selected="selected">'.__('Select a Service','wp-ticket-ultra').'</option>';
		
		foreach ( $cate_rows as $cate )
		{
			
			
			
			$html .= '<optgroup label="'.$cate->cate_name.'" >';
			
			//get services						
			$servi_rows = $this->get_all_services($cate->cate_id);
			foreach ( $servi_rows as $serv )
			{
				$selected = '';
				
						
				if($serv->service_id==$service_id){$selected = 'selected';}
				$html .= '<option value="'.$serv->service_id.'" '.$selected.' >'.$serv->service_title.'</option>';
				
			}
			
			$html .= '</optgroup>';
			
		}
		
		$html .= '</select>';
		
		return $html;
	
	}
	
	function get_duration_drop_down($seconds = null)
	{
		global  $wpticketultra, $bupcomplement;
		
		$html = '';
		
		//$max_hours = 43200; //12 hours in seconds	
		$max_hours = 43200; //12 hours in seconds		
		$min_minutes = $wpticketultra->get_option('bup_time_slot_length');
		
		if($min_minutes ==''){$min_minutes=15;}		
		$min_minutes=$min_minutes*60;
		
		$html .= '<select name="bup-duration" id="bup-duration">';
		
		for ($x = $min_minutes; $x <= $max_hours; $x=$x+$min_minutes)
		{
			$selected = '';
			if($seconds==$x){$selected='selected="selected"';}
		
			$html .= '<option value="'.$x.'" '.$selected.'>'.$this->get_service_duration_format($x).'</option>';
			
		}
		
		if(isset($bupcomplement))
		{
			$selected = '';		
			if($seconds==86400){$selected='selected="selected"';}		
			$html .= '<option value="86400" '.$selected.'>'.__('All Day ','wp-ticket-ultra').'</option>';
		}
		
		
		
		$html .= '</select>';
		
		return $html;
	
	}
	
	
	
	public function get_all_departments ($site_id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT dep.*, site.* FROM ' . $wpdb->prefix .$this->table_prefix.'_departments dep   ' ;
		
		$sql .= " RIGHT JOIN ".$wpdb->prefix .$this->table_prefix."_sites site ON (site.site_id = dep.department_site_id)";
		$sql .= " WHERE site.site_id = dep.department_site_id ";
		
		if($site_id!=''){$sql .= " AND site.site_id = '$site_id'  ";}
		
		$sql .= " ORDER BY site.site_name ASC, dep.department_order ASC, dep.department_name ASC";
		
		//echo $sql;
		
		$res = $wpdb->get_results($sql);
		return $res ;	
	
	}
	
		
	
	public function get_one_department ($department_id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prefix. '_departments  ' ;
		$sql .= ' WHERE department_id = "'.(int)$department_id.'"' ;			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}

	
}
$key = "department";
$this->{$key} = new WPTicketUltraDepartment();
?>