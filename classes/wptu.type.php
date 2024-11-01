<?php
class WPTicketUltraType
{

	var $ajax_p = 'wptu';
	var $table_prefix = 'wptu';
	
	// 1 - Issue  -- Needs an agent and a first reply.
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
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_ticket_types (
				`ticket_type_id` bigint(20) NOT NULL auto_increment,				
				`ticket_type_name` varchar(300) NOT NULL,
				`ticket_type_native` int(1) NOT NULL DEFAULT "1",				  								 			
				PRIMARY KEY (`ticket_type_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );				
		$this->create_basic_data();	
		
		
	}
	
	function create_basic_data()
	{
		
		global $wpdb, $wpticketultra;
		
		
		$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix . ''.$this->table_prefix.'_ticket_types ' ;
		$statuses = $wpdb->get_results($sql);	
		
		$total = $this->fetch_result($statuses);
		$total = $total->total;	
		
		if ($total==0)
		{		
		
			$new_record = array('ticket_type_id' => 1,	
								'ticket_type_name' => __('Issue','wp-ticket-ultra')				
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_ticket_types', $new_record, array( '%d', '%s' ));
					
		}		
	
	}
	
	public function get_all_statuses ($department_id = NULL) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_ticket_types   ' ;			
		$sql .= ' ORDER BY ticket_type_name ASC  ' ;
		
		$res = $wpdb->get_results($sql);
		return $res ;	
	
	}
	
	public function get_one ($id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prefix. '_ticket_types  ' ;
		$sql .= ' WHERE ticket_type_id = "'.$id.'"' ;			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
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
	
	
}
$key = "type";
$this->{$key} = new WPTicketUltraType();
?>