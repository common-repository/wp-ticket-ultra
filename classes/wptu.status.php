<?php
class WPTicketUltraStatus
{

	var $ajax_p = 'wptu';
	var $table_prefix = 'wptu';
	
	//status type 0 is default
	
	
	// 1 - New  -- Needs an agent and a first reply.
	// 2 - Open -- Ticket was replied at least once and an agent was assigned.
	
	// 3 - Pending -- means that the assigned agent has a follow-up question for the requester. The agent may 	need more information about the support issue. Requests that are set to Pending typically remain that way until the requester responds and provides the information the agent needs to continue resolving the request.
	
	// 4 - On-hold -- Needs a solution or reply from third-party
	// 5 - Solved 
	// 6 - Closed
	// 7 - Trash
	
	function __construct() 
	{
				
		$this->ini_module();		

	}
	
	public function ini_module()
	{
		global $wpdb;
		
		
		
		   
		   $this->update_table();
		   
		   
		  		   
		
	}
	
	function update_table()
	{
		global $wpdb;
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_statuses (
				`status_id` bigint(20) NOT NULL auto_increment,
				`status_type` int(1) NOT NULL DEFAULT "0", 
				`status_visible_for_user` int(1) NOT NULL DEFAULT "1",
				`status_name` varchar(300) NOT NULL,
				`status_name_client` varchar(300) NOT NULL,	
				`status_color` varchar(10) DEFAULT NULL,
			    `status_font_color` varchar(10) DEFAULT NULL,
				  								 			
				PRIMARY KEY (`status_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
		//use on pro verion only
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_statuses_workflow (
				`status_workflow_id` bigint(20) NOT NULL auto_increment,				
				`status_workflow_name` varchar(300) NOT NULL,
				`status_workflow_name_client` varchar(300) NOT NULL,				
				`status_workflow_color` varchar(10) DEFAULT NULL,
			    `status_workflow_font_color` varchar(10) DEFAULT NULL,
				  								 			
				PRIMARY KEY (`status_workflow_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );	
		
		$this->create_basic_data();			
								
		
		
	}
	
	function create_basic_data()
	{
		
		global $wpdb, $wpticketultra;
		
		
		$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix . ''.$this->table_prefix.'_statuses ' ;
		$statuses = $wpdb->get_results($sql);	
		
		$total = $this->fetch_result($statuses);
		$total = $total->total;	
		
		if ($total==0)
		{		
		
			$new_record = array('status_id' => 1,	
								'status_name' => __('New','wp-ticket-ultra'),
								'status_name_client' => __('New','wp-ticket-ultra'),
								'status_color' => '#428bca',
								
								
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_statuses', $new_record, array( '%d', '%s' , '%s' , '%s'));
			
			$new_record = array('status_id' => 2,	
								'status_name' => __('Open','wp-ticket-ultra'),
								'status_name_client' => __('Open','wp-ticket-ultra'),
								'status_color' => '#8BB467',
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_statuses', $new_record, array( '%d', '%s' , '%s' , '%s'));
			$new_record = array('status_id' => 3,	
								'status_name' => __('Pending','wp-ticket-ultra'),
								'status_name_client' => __('Pending','wp-ticket-ultra'),
								'status_color' => '#e3dc58',
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_statuses', $new_record, array( '%d', '%s' , '%s' , '%s'));
			$new_record = array('status_id' => 4,	
								'status_name' => __('On-hold','wp-ticket-ultra'),
								'status_name_client' => __('On-hold','wp-ticket-ultra'),								
								'status_color' => '#d4120f',
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_statuses', $new_record, array( '%d', '%s' , '%s' , '%s'));
			
			$new_record = array('status_id' => 5,	
								'status_name' => __('Solved','wp-ticket-ultra'),
								'status_name_client' => __('Solved','wp-ticket-ultra'),
								'status_color' => '#c0c0c0',
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_statuses', $new_record, array( '%d', '%s' , '%s' , '%s'));
			$new_record = array('status_id' => 6,	
								'status_name' => __('Closed','wp-ticket-ultra'),
								'status_name_client' => __('Closed','wp-ticket-ultra'),
								'status_color' => '#5f5f5f',
								
								);	
								
								
										
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_statuses', $new_record, array( '%d', '%s' , '%s' , '%s'));
			
			$new_record = array('status_id' => 7,	
								'status_name' => __('Trash','wp-ticket-ultra'),
								'status_name_client' => __('Trash','wp-ticket-ultra'),
								'status_color' => '#333',
								'status_visible_for_user' => 0,
								
								);	
								
								
										
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_statuses', $new_record, array( '%d', '%s' , '%s' , '%s','%d'));
		
			
			
		}
		
	
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
	
	public function get_all_statuses_list_box ($status_id = NULL) 
	{
		global $wpdb, $wpticketultra;
		
		$html ='';
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_statuses   ' ;		
		
		$sql .= ' ORDER BY status_name ASC  ' ;
		
		$res = $wpdb->get_results($sql);
		
		
		$html .= '<select name="ticket_status" id="ticket_status" data-errormessage-value-missing="'.__(' * This field is required!','wp-ticket-ultra').'" class="validate[required]">';
		$html .= '<option value="" selected="selected">'.__('Select Status','wp-ticket-ultra').'</option>';
		
		foreach ( $res as $status )
		{
			$selected = '';
			
			if($status_id==$status->status_id){$selected='selected="selected"';}
			
			$html .= '<option  value="'.$status->status_id.'" '.$selected.' >'.$status->status_name.'</option>';
				
		}			
		
		
		$html .= '</select>';
		return $html ;	
	
	}
	
	
	public function get_all_statuses_list_box_back_end ($status_id = NULL) 
	{
		global $wpdb, $wpticketultra;
		
		$html ='';
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_statuses   ' ;		
		
		$sql .= ' ORDER BY status_name ASC  ' ;
		
		$res = $wpdb->get_results($sql);		
		
		$html .= '<select name="ticket_status_reply" id="ticket_status_reply" >';
		$html .= '<option value="" selected="selected">'.__('Select Status','wp-ticket-ultra').'</option>';
		
		foreach ( $res as $status )
		{			
			//$selected = '';
			
			//if($status_id==$status->status_id){$selected='selected="selected"';}
			
			$html .= '<option  value="'.$status->status_id.'"  >'.$status->status_name.'</option>';
							
		}			
		
		
		$html .= '</select>';
		return $html ;	
	
	}
	
	public function get_all_statuses ($department_id = NULL) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_statuses   ' ;		
		
		$sql .= ' ORDER BY status_name ASC  ' ;
		
		$res = $wpdb->get_results($sql);
		return $res ;	
	
	}
	
	public function get_one ($id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prefix. '_statuses  ' ;
		$sql .= ' WHERE status_id = "'.$id.'"' ;			
				
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
$key = "status";
$this->{$key} = new WPTicketUltraStatus();
?>