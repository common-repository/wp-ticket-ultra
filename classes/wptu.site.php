<?php
class WPTicketUltraSite
{
	var $mBusinessHours;
	var $mDaysMaping;
	
	var $ajax_p = 'wptu';
	var $table_prefix = 'wptu';
	
	function __construct() 
	{
				
		$this->ini_module();
		
		

	}
	
	public function ini_module()
	{
		global $wpdb;
		
		
		$this->update_table();
		   
		add_action( 'wp_ajax_'.$this->ajax_p.'_display_sites', array( &$this, 'get_ajax_admin_sites' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_get_site_add_form',  array( &$this, 'get_site_add_form' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_add_site_confirm',  array( &$this, 'add_site_confirm' ));
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_product_form',  array( &$this, 'delete_product_form' ));
		add_action( 'wp_ajax_'.$this->ajax_p.'_delete_product_confirm',  array( &$this, 'delete_product_confirm' ));
		
		
		
		
	}
	
	function update_table()
	{
		global $wpdb;
		
		
		$query = 'CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . ''.$this->table_prefix.'_sites (
				`site_id` bigint(20) NOT NULL auto_increment,	
				`site_woocommerce_id` bigint(20) NOT NULL DEFAULT "0",
				`site_main` int(1) NOT NULL DEFAULT "1", 							
				`site_name` varchar(300) NOT NULL,									 			
				PRIMARY KEY (`site_id`)
			) COLLATE utf8_general_ci;';
	
		$wpdb->query( $query );									
		
		
	}
	
	public function get_ajax_admin_sites()
	{
		$html = $this->get_admin_sites();	
		echo $html ;		
		die();		
	
	}
	
	public function get_admin_sites()
	{
		$rows = $this->get_all();
		
		$html = '';
		
		
		$html .='<h3>'.__('Products','wp-ticket-ultra').' ('.count($rows).')</h3>';
		
		$html .='<span class="wptu-add-department"><a href="#" id="wptu-add-sites-btn" title="'.__('Add New Product','wp-ticket-ultra').'" ><i class="fa fa-plus"></i></a></span>';
		
				
		$html .='<ul >';		
				
		if ( !empty( $rows ) )
		{
			foreach ( $rows as $row )
			{
				$html .= '<li>';
				
				$html .='<span class="wptu-action-department"><a href="#" class="wptu-product-delete"  title="'.__('Delete','wp-ticket-ultra').'" product-id="'.$row->site_id.'" ><i class="fa fa-trash-o"></i></a> <a href="#" class="wptu-edit-product-btn" product-id="'.$row->site_id.'" id="wptu-eidt-product-btn" title="'.__('Edit','wp-ticket-ultra').'" ><i class="fa fa-edit"></i></a></span>';
				
				$html .= '<a href="#" class="wptu-load-services-by-cate" data-id="'.$row->site_id.'">'.$row->site_id." - ".$row->site_name.'</a>';
				
				$html .= '<div class="wptu-sites-actions">';
				
					$html .= '<p>[wptu_create_ticket product_id="'.$row->site_id.'"]</p>';
				
				$html .= '</div>';
				
				$html .= '</li>';			
			
			}
			
			$html .= '<p><a href="#" class="wptu-load-services-by-cate" data-id=""><i class="fa fa-refresh"></i>&nbsp;'.__('reload all','wp-ticket-ultra').'</a></p>';
			
			
		}else{
		
			$html .= '<p>'.__('There are no products','wp-ticket-ultra').'</p>';
				
	    }
		
		$html .='</ul>';
       
		
		
		return $html ;	
		
	
	}
	
	public function get_site_add_form()
	{
		global $wptucomplement;
		
		$html = '';		
		
		$product_id = '';
		
		if(isset($_POST['product_id']))
		{
			$product_id = $_POST['product_id'];
			
		}
		
		$product_name = '';		
				
		if($product_id!='')		
		{
			$site = $this->get_one( $product_id);
			$product_name =	$site->site_name;
		}
		
		
		
		$display = true;		
		
		if($display)
		{		
			$html .= '<p>'.__('Name:','wp-ticket-ultra').'</p>' ;	
			$html .= '<p><input type="text" id="wptu-site-name" value="'.$product_name.'"></p>' ;
			$html .= '<input type="hidden" id="wptu_product_id" value="'.$product_id .'" />' ;
		
		}else{
			
			$html .= __( "If you need to offer support , please consider upgrading your plugin. The lite version allows you to offer support for only one Website & Product. ", 'wp-ticket-ultra' ).'<a href="https://wpticketultra.com/compare-packages.html" target="_blank">Click here</a> to upgrade your plugin.';
			
		}		
			
		echo $html ;		
		die();		
	
	}
	
	public function get_sites_total () 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix .$this->table_prefix.'_sites ' ;
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row->total;			
			
			}
			
		}	
			
	}
	
	public function add_site_confirm()
	{
		
		global $wpdb, $wptucomplement;	
		
		$html='';	
		
		$product_id = $_POST['product_id'];
		$product_name = $_POST['product_name'];
		
		if($product_id=='' && $product_name!='')		
		{					
						
			$new_record = array('site_id' => NULL,	
								'site_name' => $product_name,
								'site_main' =>0); 								
			$wpdb->insert( $wpdb->prefix . $this->table_prefix.'_sites', $new_record, array( '%d', '%s',  '%d'));					
						
			$html ='OK INSERT';
		
	    }else{
			
			$sql = $wpdb->prepare('UPDATE  ' . $wpdb->prefix . $this->table_prefix.'_sites SET site_name =%s  WHERE site_id = %d ;',array($product_name,$product_id));
		
			$results = $wpdb->query($sql);
			$html ='OK';
			
			
		}
		
		echo $html;
		die();
		
				
	
	}
	
	public function delete_product_form()
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		
		$html = '';		
	
				
		if(isset($_POST['product_id'])){
			
			$product_id = $_POST['product_id'];	
		}
		
		
			
		
		if($product_id!='') 
		{		
			
			
			//get data			
			$product = $this->get_one($product_id);
			$name = $product->site_name;
			
			$product_id	 =  $product->site_id;	
			
			//get tickets using this priority
			$used_count = $this->get_product_departments_count($product_id);
			
			
			$html .= '<div class="wptu-sect-adm-edit">';		
						
			if($used_count==0)
			{
				//we can delete
				$html .= '<p>'. __("Please confirm that you wish to delete this product.",'wp-ticket-ultra').'</p>';
				
				//button to delete priority
				$html .= '<button name="wptu-product-del-conf-btn" id="wptu-product-del-conf-btn" class="wptu-confirm-prioritydel-btn" product-id="'.$product_id.'" department-assign="0"><i class="fa fa-check"></i> '.__("CONFIRM",'wp-ticket-ultra').' </button>';
									
				
			}else{	
			
				$html .= '<strong>'. __("WARNING:",'wp-ticket-ultra').'</strong>'.__(" This product has some departments. You would need to delete all the product's departments first.",'wp-ticket-ultra');	
				
				
			}
			
			$html .= '<input type="hidden" name="wptu-department-id" id="wptu-department-id" value="'.$department_id.'" />';				
			
			
			$html .= '</div>';	
			
		}
		
		echo $html ;		
		die();		
	
	}
	
	public function delete_product_confirm()
	{
		
		global $wpdb, $wpticketultra;
		
		$product_id = $_POST['product_id'];
		
		if($product_id!='' )
		{	
							
			$sql ="DELETE FROM " . $wpdb->prefix . $this->table_prefix.'_sites'. " WHERE site_id=%d ;";			
			$sql = $wpdb->prepare($sql,array($product_id));	
			$rows = $wpdb->query($sql);
		
		}
		
		
		echo $html;
		die();
		
	}
	
	public function get_product_departments_count($product_id)
	{
		global $wpdb, $wpticketultra, $wptucomplement;
		
		$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix . $this->table_prefix.'_departments WHERE department_site_id = "'.(int)$product_id.'" ' ;
		
		$res = $wpdb->get_results($sql);					
				
		foreach ( $res as $row )
		{
				$total= $row->total;			
			
		}
		
		return $total;
	}
	
	public function get_one ($id) 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prefix. '_sites' ;
		$sql .= ' WHERE site_id = "'.(int)$id.'"' ;			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}
	
	public function get_one_default () 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prefix. '_sites' ;
		$sql .= ' ORDER BY site_id ' ;			
				
		$res = $wpdb->get_results($sql);
		
		if ( !empty( $res ) )
		{
		
			foreach ( $res as $row )
			{
				return $row;			
			
			}
			
		}	
	
	}	
	

	
	public function get_all () 
	{
		global $wpdb, $wpticketultra;
		
		$sql = ' SELECT * FROM ' . $wpdb->prefix .$this->table_prefix.'_sites ORDER BY site_name ASC  ' ;
		$res = $wpdb->get_results($sql);
		return $res ;	
	
	}
	

	
}
$key = "site";
$this->{$key} = new WPTicketUltraSite();
?>