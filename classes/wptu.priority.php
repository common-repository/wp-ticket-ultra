<?php
class WPTicketUltraPriority
{

	var $ajax_p = 'wptu';
	var $table_prefix = 'wptu';
	
	function __construct() 
	{
				
		$this->ini_module();
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_display_priorities', array( &$this, 'get_admin_priorities' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_get_priority_add_form', array( &$this, 'get_priority_add_form' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_add_priority_confirm', array( &$this, 'add_priority_confirm' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_priority_form', array( &$this, 'delete_priority_form' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_priority_confirm', array( &$this, 'delete_priority_confirm' ));
		
		
		
		
		

	}
	
	public function ini_module()
	{
		global $wpdb;
		
		   
		   $this->update_table();
		   
	}
	
	function update_table()
	{
		global $wpdb;
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_priorities (
				`priority_id` bigint(20) NOT NULL auto_increment,
				`priority_type` int(1) NOT NULL DEFAULT "0",
				`priority_public_visible` int(1) NOT NULL DEFAULT "1",
				`priority_name` varchar(300) NOT NULL,					
				`priority_color` varchar(10) DEFAULT NULL,
			    `priority_font_color` varchar(10) DEFAULT NULL,
				`priority_respond_within` int(11) NOT NULL DEFAULT "7200",
				`priority_resolve_within` int(11) NOT NULL DEFAULT "10800",
				  								 			
				PRIMARY KEY (`priority_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );
		
				
		$this->create_basic_data();			
								
		
		
	}
	
	public function get_all_list_box ($priority_id = NULL) 
	{
		global $wpdb, $wpticketultra;
		
		$html ='';
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_priorities   ' ;		
		
		$sql .= ' ORDER BY priority_name ASC  ' ;
		
		$res = $wpdb->get_results($sql);
		
		
		$html .= '<select name="ticket_priority" id="ticket_priority" data-errormessage-value-missing="'.__(' * This field is required!','wp-ticket-ultra').'" class="validate[required]">';
		$html .= '<option value="" selected="selected">'.__('Select Priority','wp-ticket-ultra').'</option>';
		
		foreach ( $res as $priority )
		{
			$selected = '';
			
			if($priority_id==$priority->priority_id){$selected='selected="selected"';}
			
			$html .= '<option  value="'.$priority->priority_id.'" '.$selected.' >'.$priority->priority_name.'</option>';
				
		}
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	public function get_all_to_assign_box ($priority_id = NULL) 
	{
		global $wpdb, $wpticketultra;
		
		$html ='';
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_priorities   ' ;		
		
		$sql .= ' ORDER BY priority_name ASC  ' ;
		
		$res = $wpdb->get_results($sql);
		
		
		$html .= '<select name="ticket_priority" id="ticket_priority" >';
		$html .= '<option value="" selected="selected">'.__('Select Priority','wp-ticket-ultra').'</option>';
		
		foreach ( $res as $priority )
		{
			$selected = '';
			
			if($priority_id==$priority->priority_id){continue;}
			
			$html .= '<option  value="'.$priority->priority_id.'" '.$selected.' >'.$priority->priority_name.'</option>';
				
		}
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	function create_basic_data()
	{
		
		global $wpdb, $wpticketultra;
		
		
		$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix . ''.$this->table_prefix.'_priorities ' ;
		$statuses = $wpdb->get_results($sql);	
		
		$total = $this->fetch_result($statuses);
		$total = $total->total;	
		
		if ($total==0)
		{		
		
			$new_record = array('priority_id' => 1,	
								'priority_name' => __('Low','wp-ticket-ultra'),
								'priority_color' => '#c0c0c0',								
								
								
								);		
									
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_priorities', $new_record, array( '%d', '%s' , '%s'));
			
			$new_record = array('priority_id' => 2,	
								'priority_name' => __('Normal','wp-ticket-ultra'),								
								'priority_color' => '#8BB467',	
								
								);
											
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_priorities', $new_record, array( '%d', '%s' , '%s'));
			$new_record = array('priority_id' => 3,	
								'priority_name' => __('High','wp-ticket-ultra'),
								'priority_color' => '#e3dc58',
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_priorities', $new_record, array( '%d', '%s' , '%s'));
			$new_record = array('priority_id' => 4,	
								'priority_name' => __('Urgent','wp-ticket-ultra'),
								'priority_color' => '#b30000',
								
								);			
			$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_priorities', $new_record, array( '%d', '%s' , '%s'));
			
					
			
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
	
	public function get_admin_priorities()
	{
		global $wpticketultra;
		
		$html = '';
		
		$rows = $this->get_all();
		
		$html .='<div class="wptu-service-header-bar">';
		$html .='<h3>'.__('Priorities','wp-ticket-ultra').' ('.count($rows).')</h3>';
		
		$html .='<span class="wptu-add-priority-m"><a href="#" id="wptu-add-priority-btn" title="'.__('Add New Priority','wp-ticket-ultra').'" ><i class="fa fa-plus"></i></a></span>';
		$html .='</div>';
		
			
		
		if ( !empty( $rows ) )
		{
			$html .= '<table width="100%" class="wp-list-table widefat fixed posts table-generic">
            <thead>';
			
		$html .= '<thead>
                <tr >
				    <th width="4%"><div style:background-color:></div></th>	
					<th width="5%">'.__('ID', 'wp-ticket-ultra').'</th>		

                    <th width="24%">'.__('Name', 'wp-ticket-ultra').'</th>
					<th width="24%">'.__('First Reply Within', 'wp-ticket-ultra').' <a href="#" title="'.__('Service Level Agreement', 'wp-ticket-ultra').'" >'.__('(SLA)', 'wp-ticket-ultra').' </a></th>
					
					<th width="24%">'.__('Resolved Within', 'wp-ticket-ultra').'</th> 
					<th width="24%">'.__('Visibility', 'wp-ticket-ultra').'</th>              
                    
					<th width="16%">'.__('Actions', 'wp-ticket-ultra').'</th>
                </tr>
            </thead>
            
            <tbody>';	
			
			foreach ( $rows as $row )
			{
				$first_reply_within = $wpticketultra->get_time_duration_format($row->priority_respond_within);
				$resolved_within = $wpticketultra->get_time_duration_format($row->priority_resolve_within);
				
				if($row->priority_public_visible==1 )
				{
					
					$visibility = __('Public','wp-ticket-ultra');
					
				}else{
					
					$visibility = __('Private','wp-ticket-ultra');
				
				}
								
				$html .= '<tr>
				    <td><div class="service-color-blet" style="background-color:'.$row->priority_color.';" ></div></td>
					<td>'.$row->priority_id.'</td>
					<td>'.$row->priority_name.'</td>				
					<td>'.$first_reply_within.'</td>
					<td>'.$resolved_within.'</td>
					
					<td>'.$visibility.'</td>
					
                    
                  
					
                   <td>';
				   
				  if($row->priority_type==1){
					  
					  $html .= ' <a href="#" class="wptu-priority-delete"  title="'.__('Delete','wp-ticket-ultra').'" priority-id="'.$row->priority_id.'" ><i class="fa fa-trash-o"></i></a> ';
				  
				  }
				   
				 $html .= '  <a class="wptu-admin-edit-priority" href="#" id="" priority-id="'.$row->priority_id.'" ><span><i class="fa fa-edit fa-lg"></i></span></a></td>
                </tr>';			
			
			}
		}else{
		
			$html .= '<p>'.__('There are no priorities ','wp-ticket-ultra').'</p>';
				
	    }
		
        $html .= '</table>';
		
		echo $html ;
		die();	
		
	
	}
	public function delete_priority_form()
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		
		$html = '';		
	
				
		if(isset($_POST['priority_id'])){
			
			$priority_id = $_POST['priority_id'];	
		}
		
		
		if(!$wptucomplement)
		{			
			$html .= __( "This functionality is available on premium versions only, please consider upgrading your plugin. The premium versions allow you to customize the priorities. ", 'wp-ticket-ultra' ).'<a href="https://wpticketultra.com/compare-packages.html" target="_blank">Click here</a> to upgrade your plugin.';
			
			echo $html;
			die();
		
		}
		
		
		if($priority_id!='') //we are editing
		{		
			
			
			//get priority data
			
			$priority = $this->get_one($priority_id);
			$name = $priority->priority_name;
			$color = $priority->priority_color;
			$reply_within = $priority->priority_respond_within;
			$resolve_within = $priority->priority_resolve_within;
			
			//get tickets using this priority
			$tickets_count = $this->get_priority_tickets_count($priority_id);
			
			
			$html .= '<div class="wptu-sect-adm-edit">';		
						
			if($tickets_count==0)
			{
				//we can delete
				$html .= '<p>'. __("Please confirm that you wish to delete this priority.",'wp-ticket-ultra').'</p>';
				
				//button to delete priority
				$html .= '<button name="wptu-priority-del-conf-btn" id="wptu-priority-del-conf-btn" class="wptu-confirm-prioritydel-btn" priority-id="'.$priority_id.'" priority-assign="0"><i class="fa fa-check"></i> '.__("CONFIRM",'wp-ticket-ultra').' </button>';
									
				
			}else{	
			
				$html .= '<strong>'. __("WARNING:",'wp-ticket-ultra').'</strong>'.__(" some tickets are using this priority. You will have to set a new priority.",'wp-ticket-ultra');			
					
				$html .= '<p>'.sprintf(	
				__( "Tickets using this priority: %s.", 'wp-ticket-ultra' ),$tickets_count).'</p>';
					
				$html .= '<p>'. __("Assign the following priority: ",'wp-ticket-ultra').'</p>';
				$html .= '<p>'.$this->get_all_to_assign_box($priority_id).'</p>';	
				
				//button to delete priority
				$html .= '<button name="wptu-priority-del-conf-btn" id="wptu-priority-del-conf-btn" class="wptu-confirm-prioritydel-btn" priority-id="'.$priority_id.'" priority-assign="1"><i class="fa fa-check"></i> '.__("CONFIRM",'wp-ticket-ultra').' </button>';
				
				
				
				
			}
			
			$html .= '<input type="hidden" name="wptu-priority-id" id="wptu-priority-id" value="'.$priority_id.'" />';				
			
			
			$html .= '</div>';	
			
		}
		
		echo $html ;		
		die();		
	
	}
	
	
	public function get_priority_tickets_count($priority_id)
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix . $this->table_prefix.'_tickets WHERE ticket_priority = "'.$priority_id.'" ' ;
		
		$res = $wpdb->get_results($sql);					
				
		foreach ( $res as $row )
		{
				$total= $row->total;			
			
		}
		
		//echo $sql;
		
		return $total;		
		
				
	}
	
	public function get_priority_add_form()
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		$service_id = '';
		$department_id = '';
		$name = '';
		
				
		if(isset($_POST['priority_id'])){
			
			$priority_id = $_POST['priority_id'];	
		}
		
		
		if(!$wptucomplement)
		{			
			$html .= __( "This functionality is available on premium versions only, please consider upgrading your plugin. The premium versions allow you to customize the priorities. ", 'wp-ticket-ultra' ).'<a href="https://wpticketultra.com/compare-packages.html" target="_blank">Click here</a> to upgrade your plugin.';
			
			echo $html;
			die();
		
		}
		
		
		if($priority_id!='') //we are editing
		{		
			$category = '';//$this->get_one_service($service_id);			
			$mess = __('Here you can update the information of this prioirty. Once you have modified the information click on the save button.','wp-ticket-ultra');
			
			//get priority data
			
			$priority = $this->get_one($priority_id);
			$name = $priority->priority_name;
			$color = $priority->priority_color;
			$reply_within = $priority->priority_respond_within;
			$resolve_within = $priority->priority_resolve_within;
			$is_public = $priority->priority_public_visible ;			
			
		
		}else{
			
			$mess = __('Here you can create a new priority. Once you have filled in the form click on the save button.','wp-ticket-ultra');
			
		
		}
		
		$html = '';
		
		$html .= '<div class="wptu-sect-adm-edit">';
		
		$html .= '<p>'.$mess.'</p>';
		
			$html .= '<div class="wptu-edit-service-block">';						
			
			$html .= '<div class="wptu-field-separator"><label for="wptu-box-title">'.__('Name','wp-ticket-ultra').':</label><input type="text" name="wptu-title" id="wptu-title" class="wptu-common-textfields" value="'.$name.'" /></div>';
			
			$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('Background Color','wp-ticket-ultra').':</label><input name="wptu-priority-color" type="text" id="wptu-priority-color" value="'.$color.'" class="color-picker" data-default-color=""/></div>';
			
			
			$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('Is Private?','wp-ticket-ultra').':</label>';
			
			
			$selected_private = '';
			$selected_public= '';		
			if($is_public==1)
			{
				$selected_private = 'selected="selected"';
				
			}else{
				
				$selected_public= 'selected="selected"';
				
			}
			
			 ///			 
			 $html .= '<select name="wptu-priority-private" id="wptu-priority-private">';			 
			 $html .= '<option value="0" '.$selected_public.' >'.__('YES','wp-ticket-ultra').'</option>';
			 $html .= '<option value="1" '.$selected_private.'>'.__('NO','wp-ticket-ultra').'</option>';			 
			 $html .= '</select>';		
			
			$html .= '</div>';
						
			$html .= '</br><div class="wptu-field-separator"><strong>'.__('Service Level Agreement','wp-ticket-ultra').':</strong></div>';			
								
			$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('First Reply Within','wp-ticket-ultra').':</label>'.$this->get_reply_time_drop_down($reply_within, "wptu-reply-within").'</div>';
			
			$html .= '<div class="wptu-field-separator"><label for="textfield">'.__('Resolved Within','wp-ticket-ultra').':</label>'.$this->get_reply_time_drop_down($resolve_within, "wptu-resolve-within").'</div>';
			
			
			
				
			$html .= '<input type="hidden" name="wptu-priority-id" id="wptu-priority-id" value="'.$priority_id.'" />';				
			
			
		$html .= '</div>';	
		$html .= '</div>';	
			
		echo $html ;		
		die();		
	
	}
	
	public function delete_priority_confirm()
	{
		
		global $wpdb, $wpticketultra;
		
		$priority_id = $_POST['priority_id'];
		$new_priority_id = $_POST['new_priority_id'];
		
		if($new_priority_id!='' )
		{
		
			//assign new priority to tickets.		
			$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prefix.'_tickets SET ticket_priority  =%d  WHERE ticket_priority = %d ;',array($new_priority_id,  $priority_id));		
			$results = $wpdb->query($sql);
		}		
		
		if($priority_id!='' )
		{	
							
			$sql ="DELETE FROM " . $wpdb->prefix . $this->table_prefix.'_priorities'. " WHERE priority_id=%d ;";			
			$sql = $wpdb->prepare($sql,array($priority_id));	
			$rows = $wpdb->query($sql);
		
		}
		
		
		echo $html;
		die();
		
	}
	
	public function add_priority_confirm()
	{
		
		global $wpdb, $wptucomplement;	
		
		$html='';	
		
		$priority_id = $_POST['priority_id'];
		$priority_name = $_POST['priority_title'];
		$priority_color = $_POST['priority_color'];		
		$reply_within = $_POST['reply_within'];
		$resolve_within = $_POST['resolve_within'];
		$priority_visibility = $_POST['visibility'];
				
		if($priority_id=='')		
		{
			$new_record = array('priority_id' => NULL,	
								'priority_name' => $priority_name,							
								'priority_color' => $priority_color,
								'priority_respond_within' => $reply_within,
								'priority_resolve_within' => $resolve_within,
								'priority_type' => 1,
								'priority_public_visible' => $priority_visibility);								
			$wpdb->insert( $wpdb->prefix . $this->table_prefix.'_priorities', $new_record, array( '%d', '%s' , '%s' , '%d', '%d','%d' ,'%d'));					
						
			$html ='OK INSERT';
		
	    }else{
			
			$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prefix.'_priorities SET priority_name =%s, priority_color =%s , priority_respond_within =%d, priority_resolve_within =%d, priority_public_visible =%d   WHERE priority_id = %d ;',array($priority_name,$priority_color, $reply_within, $resolve_within, $priority_visibility,  $priority_id));
		
			$results = $wpdb->query($sql);
			$html ='OK';
			
			
		}
		
		echo $html;
		die();
		
	
	}
	
	function get_reply_time_drop_down($seconds = null, $box_id)
	{
		global  $wpticketultra, $wptucomplement;
		
		$html = '';
		
		//$max_hours = 43200; //12 hours in seconds	
		$max_hours = 604800; //12 hours in seconds	 = 	43200, 24 hours = 86400, 7 days = 604800
		$min_minutes = 15;
		
		$min_minutes=$min_minutes*60;
		
		$html .= '<select name="'.$box_id.'" id="'.$box_id.'">';
		
		for ($x = $min_minutes; $x <= $max_hours; $x=$x+$min_minutes)
		{
			$selected = '';
			if($seconds==$x){$selected='selected="selected"';}
		
			$html .= '<option value="'.$x.'" '.$selected.'>'.$wpticketultra->get_time_duration_format($x).'</option>';
			
		}
		
		if(isset($wptucomplement))
		{
			$selected = '';		
			if($seconds==86400){$selected='selected="selected"';}		
			//$html .= '<option value="86400" '.$selected.'>'.__('All Day ','wp-ticket-ultra').'</option>';
		}
		
		
		
		$html .= '</select>';
		
		return $html;
	
	}
	
	public function get_all () 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_priorities    ' ;		
		$res = $wpdb->get_results($sql);
		return $res; 
	}
	
	public function get_all_public () 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_priorities WHERE priority_public_visible  = 1   ' ;		
		$res = $wpdb->get_results($sql);
		return $res; 
	}
	
	public function get_one ($id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prefix. '_priorities  ' ;
		$sql .= ' WHERE priority_id = "'.$id.'"' ;			
				
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
$key = "priority";
$this->{$key} = new WPTicketUltraPriority();
?>