<?php
class WPTicketUltra
{
	public $classes_array = array();
	public $registration_fields;
	public $login_fields;
	public $fields;
	public $allowed_inputs;
	public $use_captcha = "no";
	
	var $ajax_p = 'wptu';
	var $table_prefix = 'wptu';
	var $upload_folder_temp = 'wptu_temp_files';

		
	public function __construct()
	{		
		
		$this->logged_in_user = 0;
		$this->login_code_count = 0;
		$this->current_page = $_SERVER['REQUEST_URI'];
		
    }
	
	public function plugin_init() 
	{	
		
		/*Load Amin Classes*/		
		if (is_admin()) 
		{
			$this->set_admin_classes();
			$this->load_classes();					
		
		}else{
			
			/*Load Main classes*/
			$this->set_main_classes();
			$this->load_classes();
			
		
		}
		
		//ini settings
		$this->intial_settings();
		
		
	}
	
 
	public function set_main_classes()
	{
		 $this->classes_array = array( "commmonmethods" =>"wptu.common",
		 
		 "shortcode" =>"wptu.shorcodes",
		 "ticket" =>"wptu.ticket",		 		 
		 "register" =>"wptu.register",		
		 "userpanel" =>"wptu.user",		 
		 "imagecrop" =>"wptu.cropimage",
		 "messaging" =>"wptu.messaging",
		 "department" =>"wptu.department",
		 "site" =>"wptu.site",
		 "profile" =>"wptu.profile",
		 "status" =>"wptu.status",
		 "priority" =>"wptu.priority",
		  "type" =>"wptu.type"				 
		   ); 	
	
	}
	
	public function set_admin_classes()
	{
				 
		 $this->classes_array = array( "commmonmethods" =>"wptu.common" , 
			
		 "shortcode" =>"wptu.shorcodes",
		 "ticket" =>"wptu.ticket",		 
		 "breaks" =>"wptu.break",		
		 "register" =>"wptu.register",		
		 "admin" =>"wptu.admin"	,		
		 "userpanel" =>"wptu.user",
		 "imagecrop" =>"wptu.cropimage",		 
		 "adminshortcode" =>"wptu.adminshortcodes",
		 "messaging" =>"wptu.messaging",
		 "department" =>"wptu.department"	,
		 "site" =>"wptu.site",
		 "profile" =>"wptu.profile",
		 "status" =>"wptu.status"	,
		 "priority" =>"wptu.priority",
		 "type" =>"wptu.type"	 
		  
		   ); 	
		 
		
	}
	
	public  function get_int_date_format( )
    {
		global  $wpticketultra;
		
		$date_format = $this->get_option('bup_date_admin_format');
		
		if($date_format==''){			
			
			$date_format = 'm/d/Y';					
		}
        return $date_format;
		
	
	}
	
	// Modified version of the timezone list function from http://stackoverflow.com/a/17355238/507629
	// Includes current time for each timezone (would help users who don't know what their timezone is)

	function generate_timezone_list() 
	{
		static $regions = array(
			DateTimeZone::AFRICA,
			DateTimeZone::AMERICA,
			DateTimeZone::ANTARCTICA,
			DateTimeZone::ASIA,
			DateTimeZone::ATLANTIC,
			DateTimeZone::AUSTRALIA,
			DateTimeZone::EUROPE,
			DateTimeZone::INDIAN,
			DateTimeZone::PACIFIC,
		);
	
		$timezones = array();
		foreach( $regions as $region )
		{
			$timezones = array_merge( $timezones, DateTimeZone::listIdentifiers( $region ) );
		}
	
		$timezone_offsets = array();
		foreach( $timezones as $timezone )
		{
			$tz = new DateTimeZone($timezone);
			$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
		}
	
		// sort timezone by timezone name
		ksort($timezone_offsets);
	
		$timezone_list = array();
		foreach( $timezone_offsets as $timezone => $offset )
		{
			$offset_prefix = $offset < 0 ? '-' : '+';
			$offset_formatted = gmdate( 'H:i', abs($offset) );
	
			$pretty_offset = "UTC${offset_prefix}${offset_formatted}";
			
			$t = new DateTimeZone($timezone);
			$c = new DateTime(null, $t);
			$current_time = $c->format('g:i A');
	
			$timezone_list[$timezone] = "(${pretty_offset}) $timezone - $current_time";
		}
	
		return $timezone_list;
	}
	
	function get_status_label($status_name, $status_color)
	{
		global  $wpticketultra;
		
		$html ='<span class="wptu-status-label" style="color:'. $status_color.'">'.$status_name.'</span>';
		
				
		return $html;
	}
	
	function get_priority_label($priority_name, $priority_color)
	{
		global  $wpticketultra;
		
		$html ='<span class="wptu-priority-label" style="color:'. $priority_color.'">'.$priority_name.'</span>';
		
				
		return $html;
	}
	
	function get_time_format()
	{
		global  $wpticketultra;
	
		$data = $this->get_option('bup_time_format');
		
		if($data=='')
		{
			$data = 'h:i A';
		
		}
		
		return $data;
	}
	
	function isWeekend($date) 
	{
		$weekDay = date('w', strtotime($date));
		return ($weekDay == 0 || $weekDay == 6);
	}	
	
	function get_date_to_display()
	{
		global  $wpticketultra;
		
		$ret = '';
	
		$time_format = $this->get_option('bup_time_format');
		
		if($time_format=='')
		{
			$time_format = 'h:i A';
		}
		
		$date_format = $this->get_option('bup_date_admin_format');
		
		if($date_format==''){			
			
			$date_format = 'm/d/Y';					
		}
		
		$ret = $date_format.' '.$time_format;
		
		return $ret;
	}	
	
	public function get_user_meta($user_id, $meta) 
	{
		$data = get_user_meta($user_id, $meta, true);
		
		return $data;
	}
	
	public function cut_string($txt, $length) 
	{
		$txt =mb_strimwidth($txt, 0,$length, "...");
		$txt = strtolower($txt);
		$txt = ucwords($txt);
				
		return $txt;
	}
	
	public function format_subject_string($txt) 
	{
		$txt = strtolower($txt);
		$txt = ucwords($txt);
				
		return $txt;
	}
	
	function text_message_formatting($content)
	 {
		global $wpticketultra;
				
		$target = 'target="_blank"';			
			
		$c =  preg_replace('!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i', '<a href="$1" rel="nofollow" '.$target.' >$1</a>', $content);
		$content =  $c ;	
		 
		 return $content;
		
	 }
	
	
	
	
	
	public function pluginname_ajaxurl() 
	{
		echo '<script type="text/javascript">var ajaxurl = "'. admin_url("admin-ajax.php") .'";
</script>';
	}
	
	
	public function intial_settings()
	{							
			 			 
		$this->include_for_validation = array('text','fileupload','textarea','select','radio','checkbox','password');
			
		add_action('wp_enqueue_scripts', array(&$this, 'add_front_end_styles'), 10); 
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles_scripts'), 9);
		
		/*Create a generic profile page*/
		add_action( 'init', array(&$this, 'activate_profile_module'), 9);

		
		/* Remove bar except for admins */
		add_action('init', array(&$this, 'remove_admin_bar'), 9);	
		
		/* Create Standar Fields */		
		add_action('init', array(&$this, 'create_standard_fields'));
		add_action('admin_init', array(&$this, 'create_standard_fields'));	
		
		add_action('init', array(&$this, 'set_priority_options'));
		add_action('admin_init', array(&$this, 'set_priority_options'));
				
		/*Setup redirection*/
		add_action( 'wp_head', array(&$this, 'pluginname_ajaxurl'));
		//add_action( 'mce_css', array(&$this, 'my_theme_add_editor_styles'));
		
	    add_action( 'wp_ajax_'.$this->ajax_p.'_get_custom_department_fields',  array( &$this, 'get_custom_department_fields_ajax' ));
		
		add_action( 'wp_ajax_nopriv_'.$this->ajax_p.'_get_custom_department_fields',  array( &$this, 'get_custom_department_fields_ajax' ));		
		
		add_action( 'wp_ajax_'.$this->ajax_p.'_front_upload_files', array( &$this, 'front_upload_files' ));
		add_action( 'wp_ajax_nopriv_'.$this->ajax_p.'_front_upload_files', array( &$this, 'front_upload_files' ));
		
	}
	
	public function activate_profile_module ()
	{
		$this->create_initial_pages();
		
	}
	
		
	public  function get_date_picker_format( )
    {
		global  $wpticketultra;
		
		$date_format = $wpticketultra->get_option('wptu_date_picker_format');
		
		if($date_format=='d/m/Y'){			
			
			$date_format = 'dd/mm/yy';
			
		}elseif($date_format=='m/d/Y'){
			
			$date_format = 'mm/dd/yy';			
			
		}else{
			
			$date_format = 'mm/dd/yy';
			
		}
        return $date_format;
		
	
	}
	
	public  function get_date_picker_date( )
    {
		global  $wpticketultra;
		
		$date_format = $wpticketultra->get_option('wptu_date_picker_format');
		
		if($date_format==''){			
			
			$date_format = 'm/d/Y';					
		}
        return $date_format;
		
	
	}
	
	public function get_login_page_url($with_a_tag=false)
    {
		global $wpticketultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		
		$account_page_id = $wpticketultra->get_option('bup_user_login_page');		
		$my_account_url = get_permalink($account_page_id);
		
		if($with_a_tag)
		{
			$my_account_url = '<a href="'.$my_account_url.'" target="_blank">'.$my_account_url.'</a>';
			
		}
		
		return $my_account_url;
	
	}
	
	public function create_initial_pages ()
	{
		global $wpticketultra;
		
		$fresh_page_creation  = get_option( 'wptu_auto_page_creation' );			
		$profile_page_id = $this->get_option('profile_page_id');
		
		if($profile_page_id!='' && is_page($profile_page_id))		
		{
			$profile_page = get_post($profile_page_id);
			$slug =  $profile_page->post_name;
			
			if($fresh_page_creation==1) //user wants to recreate pages
			{						
				 //pages created
				 update_option('wptu_auto_page_creation',0);			 
				
				add_rewrite_rule("$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&wptu_username=$matches[1]', 'top');		
				//this rules is for displaying the user's profiles
				add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&wptu_username=$matches[2]', 'top');
				
				flush_rewrite_rules(false);
			
			 }else{			
					
				add_rewrite_rule("$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&wptu_username=$matches[1]', 'top');		
				//this rules is for displaying the user's profiles
				add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&wptu_username=$matches[2]', 'top');
			
			}
		
		}
			
		/* Setup query variables */
		 add_filter( 'query_vars',   array(&$this, 'wptu_uid_query_var') );				
			
	}
	
	public function wptu_uid_query_var( $query_vars )
	{
		$query_vars[] = 'wptu_username';
		//$query_vars[] = 'searchuser';
		return $query_vars;
	}
	
	public function create_rewrite_rules() 
	{
		global  $wpticketultra;
		
		//$slug = $bookingultrapro->get_option("bup_slug"); // Profile Slug
		$profile_page_id = $this->get_option('profile_page_id');
		$profile_page = get_post($profile_page_id);
		$slug =  $profile_page->post_name;
		
		add_rewrite_rule("$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&wptu_username=$matches[1]', 'top');		
			//this rules is for displaying the user's profiles
		add_rewrite_rule("([^/]+)/$slug/([^/]+)/?",'index.php?page_id='.$profile_page_id.'&wptu_username=$matches[2]', 'top');
		
		flush_rewrite_rules(false);
	
	
	}
	
	
	
	/******************************************
	Check if user exists by ID
	******************************************/
	function user_exists( $user_id ) 
	{
		$aux = get_userdata( $user_id );
		if($aux==false){
			return false;
		}
		return true;
	}
	
	
		
	
	
	
	public function create_default_pages_auto () 
	{
		update_option('wptu_auto_page_creation',1);
		
	}
	
	
	//display message
	public function uultra_fresh_install_message ($message) 
	{
		if ($errormsg) 
		{
			echo '<div id="message" class="error">';
			
		}else{
			
			echo '<div id="message" class="updated fade">';
		}
	
		echo "<p><strong>$message</strong></p></div>";
	
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
	

	function remove_admin_bar() 
	{
		if (!current_user_can('manage_options') && !is_admin())
		{
			
			if ($this->get_option('hide_admin_bar')==1) 
			{
				
				show_admin_bar(false);
			}
		}
	}
	
	function convert_date($date) 
	{
		
		$custom_date_format = $this->get_option('wptu_date_format');
			
		if ($custom_date_format) 
		{
			$date = date($custom_date_format, strtotime($date));
		}
		
		
		return $date;
	}
	
	public function get_currency_symbol() 
	{
		
		$currency_symbol = $this->get_option('currency_symbol');
			
		if ($currency_symbol=='') 
		{
			$currency_symbol = '$';
		}
		
		
		return $currency_symbol;
	}
	
	
	
	public function get_logout_url ()
	{
		
		$redirect_to = $this->current_page;
			
		return wp_logout_url($redirect_to);
	}
	
	
	public function custom_logout_page ($atts)
	{
		global $xoouserultra, $wp_rewrite ;
		
		$wp_rewrite = new WP_Rewrite();		
		
		extract( shortcode_atts( array(	
			
			'redirect_to' => '', 		
							
			
		), $atts ) );
		
		
		
		//check redir		
		$account_page_id = get_option('bup_my_account_page');
		$my_account_url = get_permalink($account_page_id);
		
		if($redirect_to=="")
		{
				$redirect_to =$my_account_url;
		
		}
		$logout_url = wp_logout_url($redirect_to);
		
		//quick patch =
		
		$logout_url = str_replace("amp;","",$logout_url);
	
		wp_redirect($logout_url);
		exit;
		
	}
	
	public function get_redirection_link ($module)
	{
		$url ="";
		
		if($module=="profile")
		{
			//get profile url
			$url = $this->get_option('profile_page_id');			
		
		}
		
		return $url;
		
	}
	
		
		
	
			
	
	/*Create login page */
	public function create_login_page() 
	{
		
	}
	
	/*Create register page */
	public function create_register_page() 
	{
		
	}
	
		
		
	public function wptu_set_option($option, $newvalue)
	{
		$settings = get_option('wptu_options');
		$settings[$option] = $newvalue;
		update_option('wptu_options', $settings);
	}
	
	
	public function get_fname_by_userid($user_id) 
	{
		$f_name = get_user_meta($user_id, 'first_name', true);
		$l_name = get_user_meta($user_id, 'last_name', true);
		
		$f_name = str_replace(' ', '_', $f_name);
		$l_name = str_replace(' ', '_', $l_name);
		$name = $f_name . '-' . $l_name;
		return $name;
	}
	
	public function wptu_get_user_meta($user_id, $meta) 
	{
		$data = get_user_meta($user_id, $meta, true);
		
		return $data;
	}
	
	public function get_priority_options_drop_down ()	
	{
		
	}
	
		
	
	public function set_priority_options ()	
	{
		
		/* Core login fields */
		$priority_options = array( 
			1 => array( 
				'value' => 1, 
				'text' => __('Low','wp-ticket-ultra') 
				
			),
			2 => array( 
				'value' =>2, 
				'text' => __('Normal','wp-ticket-ultra') 
				
			),
			3 => array( 
				'value' =>3, 
				'text' => __('High','wp-ticket-ultra') 
				
			),
			4 => array( 
				'value' =>4, 
				'text' => __('Urgent','wp-ticket-ultra') 
				
			)
		);
		
		
		if (!get_option('wptu_priority_options'))
		{
			update_option('wptu_priority_options', $priority_options);
		}	
		
	}
	
	public function get_priority ($priority)	
	{	
		if (get_option('wptu_priority_options'))
		{
			$priorities = get_option('wptu_priority_options');
			
			$value = $priorities[$priority]['text'];
			
		}else{
			
			$value = 'n/a';
			
		
		}
		
		return $value;
		
	}
	
	
	public function create_standard_fields ()	
	{
		
		/* Allowed input types */
		$this->allowed_inputs = array(
			'text' => __('Text','wp-ticket-ultra'),			
			'textarea' => __('Textarea','wp-ticket-ultra'),
			'select' => __('Select Dropdown','wp-ticket-ultra'),
			'radio' => __('Radio','wp-ticket-ultra'),
			'checkbox' => __('Checkbox','wp-ticket-ultra'),			
		    'datetime' => __('Date Picker','wp-ticket-ultra')
		);
		
		/* Core registration fields */
		$set_pass = $this->get_option('set_password');
		if ($set_pass) 
		{
			$this->registration_fields = array( 
			50 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'display_name', 
				'name' => __('Your Name', 'wp-ticket-ultra'),
				'required' => 1
			),
			
			70 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'last_name', 
				'name' => __('Last Name', 'wp-ticket-ultra'),
				'required' => 1
			),
			100 => array( 
				'icon' => 'envelope', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_email', 
				'name' => __('E-mail','wp-ticket-ultra'),
				'required' => 1,
				'can_hide' => 1,
			),
			
			250 => array( 
				'icon' => 'phone', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'wptu_subject', 
				'name' => __('Subject','wp-ticket-ultra'),
				'required' => 1,
				'can_hide' => 1
			),
			
			450 => array( 
			  'position' => '200',
				'icon' => 'pencil',
				'field' => 'textarea',
				'type' => 'usermeta',
				'meta' => 'special_notes',
				'name' => __('Comments','wp-ticket-ultra'),
				'can_hide' => 0,
				'can_edit' => 1,
				'show_in_register' => 1,
				'private' => 0,
				'social' => 0,
				'deleted' => 0,
				'allow_html' => 1,				
				'help_text' => ''
			
			)
			
			
		);
		
		
		} else {
			
		$this->registration_fields = array( 
			50 => array( 
				 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'display_name', 
				'name' => __('Your Name','wp-ticket-ultra'),
				'required' => 1,
				'width' => 'full'
				
			),
			
			70 => array( 
				 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'last_name', 
				'name' => __('Last Name', 'wp-ticket-ultra'),
				'required' => 1,
				'width' => 'full'
				
				
			),
			100 => array( 
				
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_email', 
				'name' => __('E-mail','wp-ticket-ultra'),
				'required' => 1,
				'width' => 'full',
				'can_hide' => 1,
				'help' => __('A confirmation email will be sent to this email address. If you are already a client  please use the <strong>same email</strong> used to create your previous requests. Login information will be sent so you can check the status of your request.','wp-ticket-ultra')
			),
			
			250 => array( 
				 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'wptu_subject', 
				'name' => __('Subject','wp-ticket-ultra'),
				'required' => 1,
				'can_hide' => 1,
				'width' => 'full',
			),
			
			450 => array( 
			  'position' => '200',
				'icon' => 'pencil',
				'field' => 'textarea',
				'type' => 'usermeta',
				'meta' => 'special_notes',
				'name' => __('Comments','wp-ticket-ultra'),
				'can_hide' => 0,
				'can_edit' => 1,
				'show_in_register' => 1,
				'private' => 0,
				'social' => 0,
				'deleted' => 0,
				'allow_html' => 1,				
				'help_text' => '',
				'width' => 'full',
			
			)
			
			
		);
		}
		
		/* Core login fields */
		$this->login_fields = array( 
			50 => array( 
				'icon' => 'user', 
				'field' => 'text', 
				'type' => 'usermeta', 
				'meta' => 'user_login', 
				'name' => __('Username or Email','wp-ticket-ultra'),
				'required' => 1
				
			),
			100 => array( 
				'icon' => 'lock', 
				'field' => 'password', 
				'type' => 'usermeta', 
				'meta' => 'login_user_pass', 
				'name' => __('Password','wp-ticket-ultra'),
				'required' => 1
			)
		);
		
		
				/* These are the basic profile fields */
		$this->fields = array(
			80 => array( 
			  'position' => '50',
				'type' => 'separator', 
				'name' => __('Appointment Info','wp-ticket-ultra'),
				'private' => 0,
				'show_in_register' => 1,
				'deleted' => 0,
				'show_to_user_role' => 0
			),			
			
			170 => array( 
			  'position' => '200',
				'icon' => 'pencil',
				'field' => 'textarea',
				'type' => 'usermeta',
				'meta' => 'special_notes',
				'name' => __('Comments','wp-ticket-ultra'),
				'can_hide' => 0,
				'can_edit' => 1,
				'show_in_register' => 1,
				'private' => 0,
				'social' => 0,
				'deleted' => 0,
				'allow_html' => 1,				
				'help_text' => ''
			
			)
		);
		
		
		
		
		/* Store default profile fields for the first time */
		if (!get_option('wptu_profile_fields'))
		{
			update_option('wptu_profile_fields', $this->fields);
		}	
		
		
		
		
	}
	
	
	
	
	
		
	function get_the_guid( $id = 0 )
	{
		$post = get_post($id);
		return apply_filters('get_the_guid', $post->guid);
	}
	   	
	function load_classes() 
	{	
		
		foreach ($this->classes_array as $key => $class) 
		{
			if (file_exists(wptu_path."classes/$class.php")) 
			{
				require_once(wptu_path."classes/$class.php");
						
					
			}
				
		}	
	}
	
	
	
	
	function theme_add_editor_styles( $mce_css ) 
	{
	  if ( !empty( $mce_css ) )
		$mce_css .= ',';
		$mce_css .=  wptu_url.'templates/'.bup_template.'/css/editor-style.css';
		return $mce_css;
	  }
	  
	  
	  /* register admin scripts */
	public function add_styles_scripts()
	{	
		
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script('jquery-ui-datepicker' );
		
		wp_enqueue_script('plupload-all');	
		wp_enqueue_script('jquery-ui-progressbar');	
		
				
		wp_register_script( 'form-validate-lang', wptu_url.'js/languages/jquery.validationEngine-en.js',array('jquery'));
			
		wp_enqueue_script('form-validate-lang');			
		wp_register_script( 'form-validate', wptu_url.'js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('form-validate');		
	}
	
	/* register styles */
	public function add_front_end_styles()
	{
		global $wp_locale;
		
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_style("wp-jquery-ui-dialog");
		wp_enqueue_script('jquery-ui-datepicker');	
		
		
		/*uploader*/					
		wp_enqueue_script('jquery-ui');			
		wp_enqueue_script('plupload-all');	
		wp_enqueue_script('jquery-ui-progressbar');				

		/* Font Awesome */
		wp_register_style('wptu_font_awesome', wptu_url.'css/css/font-awesome.min.css');
		wp_enqueue_style('wptu_font_awesome');
		
		//----MAIN STYLES		
				
		/* Custom style */		
		wp_register_style('wptu_style', wptu_url.'templates/css/styles.css');
		wp_enqueue_style('wptu_style');			
				
		
		/*Users JS*/		
		wp_register_script( 'wptu-front_js', wptu_url.'js/wptu-front.js',array('jquery'),  null);
		wp_enqueue_script('wptu-front_js');
		
		
		/* Jquery UI style */		
		
				
				
		/*Validation Engibne JS*/		
			
		wp_register_script( 'wptu-form-validate-lang', wptu_url.'js/languages/jquery.validationEngine-en.js',array('jquery'));			
		wp_enqueue_script('wptu-form-validate-lang');	
				
		wp_register_script('wptu-form-validate', wptu_url.'js/jquery.validationEngine.js',array('jquery'));
		wp_enqueue_script('wptu-form-validate');
		
		$message_wait_submit ='<img src="'.wptu_url.'admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; '.__("Please wait ...","wpticku").'';		
		
				
		
//localize our js
		$date_picker_array = array(
					'closeText'         => __( 'Done', "bookingup" ),
					'currentText'       => __( 'Today', "bookingup" ),
					'prevText' =>  __('Prev',"bookingup"),
		            'nextText' => __('Next',"bookingup"),				
					'monthNames'        => array_values( $wp_locale->month ),
					'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
					'monthStatus'       => __( 'Show a different month', "bookingup" ),
					'dayNames'          => array_values( $wp_locale->weekday ),
					'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
					'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),					
					// get the start of week from WP general setting
					'firstDay'          => get_option( 'start_of_week' ),
					// is Right to left language? default is false
					'isRTL'             => $wp_locale->is_rtl(),
				);
				
		
		
	}
	
	/* Custom WP Query*/
	public function get_results( $query ) 
	{
		$wp_user_query = new WP_User_Query($query);						
		return $wp_user_query;
		
	
	}
	
	


	
	/* Show registration form on booking steps */
	function get_registration_form( $args=array() )
	{

		global $post;		
		
				
		/* Arguments */
		$defaults = array(       
			'redirect_to' => null,
			'form_header_text' => __('Sign Up','wp-ticket-ultra'),			
			'service_id' => '',
			'site_id' => '',
			'product_id' => '',
			'display_sites' => '',	
			'display_alignment' => '',	
			'on_backend' => false,	
			'staff_id' => '' 			
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		$args_2 = $args;
		extract( $args, EXTR_SKIP );
						
		// Default set to blank
		$this->captcha = '';
		
		
		$display = null;
		
		
		
		   $display .= '<div class="wptu-user-data-registration-form">					';				
								
								
						 /*Display sucess message*/	
						 
						 if ( (isset($_GET['wptu_ticket_key']) && $_GET['wptu_ticket_key'] !='' ) && (isset($_GET['wptu_status']) && $_GET['wptu_status'] =='ok' ) ) 
						{
							 $display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Your request has been sent successfully. Please check your email.','wp-ticket-ultra').'</span></div>';
						 
						 }
													
													
						/*Display errors*/
						if (isset($_POST['wptu-register-form'])) 
						{
							$display .= $this->register->get_errors();
						}
						
						if($on_backend){
							
							$display .= $this->display_ticket_form( $redirect_to, $args_2);						
							
						
						}else{
							
							if(is_user_logged_in())
		 					{
								
								$display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Please submit a ticket through your account.','wp-ticket-ultra').'</span></div>';
								
								return $display;
								
							
							}else{
								
								$display .= $this->display_ticket_form( $redirect_to, $args_2);							
							
							}				
						
						}
						
						

				$display .= '';
		
		
		return $display;
		
	}
	
	function get_time_duration_format($seconds)
	{
		global $wpdb, $wpticketultra;
		
		$time_formated = $wpticketultra->commmonmethods->secondsToTime($seconds);
		
		
		if($seconds<3600) //less than an hour
		{
			$str = $time_formated["m"] . " min ";		
		
		}else{
			
			$str = $time_formated["h"] ." h ";
			
			
			if($time_formated["m"] > 0)
			 {
				$str =  $str." ".$time_formated["m"]." min ";
			
			}
			
		
		
		}
		
		
		
		return $str;
	
	
	}
	
	
	/* Show ticket form as admin */
	function get_registration_form_on_admin( $args=array() )
	{

		global $post;		
		
				
		/* Arguments */
		$defaults = array(       
			'redirect_to' => null,
			'form_header_text' => __('Sign Up','wp-ticket-ultra'),			
			'service_id' => '',
			'site_id' => '',
			'product_id' => '',
			'display_sites' => '',	
			'display_alignment' => '',		
			'staff_id' => '' 			
        		    
		);
		$args = wp_parse_args( $args, $defaults );
		$args_2 = $args;
		extract( $args, EXTR_SKIP );
						
		// Default set to blank
		$this->captcha = '';
		
		
		$display = null;
		
		
		
		   $display .= '<div class="wptu-user-data-registration-form">					';				
								
								
						 /*Display sucess message*/	
						 
						 if ( (isset($_GET['wptu_ticket_key']) && $_GET['wptu_ticket_key'] !='' ) && (isset($_GET['wptu_status']) && $_GET['wptu_status'] =='ok' ) ) 
						{
							 $display .= '<div class="wptu-ultra-success"><span><i class="fa fa-check"></i>'.__('Your request has been sent successfully. Please check your email.','wp-ticket-ultra').'</span></div>';
						 
						 }
													
													
						/*Display errors*/
						if (isset($_POST['wptu-register-form'])) 
						{
							$display .= $this->register->get_errors();
						}
						
						$display .= $this->display_ticket_form_as_admin( $redirect_to, $args_2);

				$display .= '';
		
		
		return $display;
		
	}
	
	function get_priority_values()
	{
		
				
		$html = '';
		
		$val_rows = $this->priority->get_all_public();
		
		
		$html .= '<select name="wptu-priority" id="wptu-priority" data-errormessage-value-missing="'.__(' * This field is required!','wp-ticket-ultra').'" class="validate[required]">';
		$html .= '<option value="" selected="selected">'.__('Select','wp-ticket-ultra').'</option>';
		
		foreach ( $val_rows as $val )
		{		
			
			$html .= '<option value="'.$val->priority_id.'" '.$selected.' >'.$val->priority_name.'</option>';
				
		}			
		
		
		$html .= '</select>';
		
		return $html;
		
	}
	
	function get_custom_department_fields_ajax()
	{
		$depto_id = sanitize_text_field($_POST['department_id']); 
		$html =$this->get_custom_department_fields($depto_id);		
		echo $html;
		die();
		
	}
	
	function get_custom_department_fields($department_id = null)
	{
		global $wptu_register,  $wptu_captcha_loader, $wptucomplement;
		$display = null;
		
		
		$array = array();
		
		$custom_form = 'wptu_profile_fields_'.$department_id;		
		$array = get_option($custom_form);			
		$fields_set_to_update =$custom_form;
		
				
		
		if(!is_array($array))$array = array();
		

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
			if($required == 1 && in_array($field, $this->include_for_validation))
			{				
			    $required_class = 'validate[required] ';
				$required_text = '(*)';				
			}
			
			
			$name = stripslashes($name);
			
			
			/* This is a Fieldset seperator */
						
			/* separator */
            if ($type == 'separator' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
			{
                   $display .= '<div class="wptu-profile-separator">'.$name.'</div>';
				   
            }
			
					
			//check if display emtpy				
				
			if ($type == 'usermeta' && $deleted == 0 && $private == 0 && isset($array[$key]['show_in_register']) && $array[$key]['show_in_register'] == 1) 
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
					
						 $tooltipip_class = '<a class="'.$qtip_classes.' wptu-tooltip" title="' . $tooltip . '" '.$qtip_style.'><i class="fa fa-info-circle reg_tooltip"></i></a>';
					} 
					
											
					$display .= '<span>'.stripslashes($name). ' '.$required_text.' '.$tooltipip_class.'</span></label>';
					
					
				} else {
					$display .= '<label class="">&nbsp;</label>';
				}
				
				$display .= '<div class="wptu-field-value">';
					
					switch($field) {
					
						case 'textarea':
							$display .= '<textarea class="'.$required_class.' wptu-input wptu-input-text-area" rows="10" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','xoousers').'">'.$this->get_post_value($meta).'</textarea>';
							break;
							
						case 'text':
							$display .= '<input type="text" class="'.$required_class.' wptu-input"  name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
							break;							
							
						case 'datetime':						
						    $display .= '<input type="text" class="'.$required_class.' wptu-input wptu-datepicker" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'"  title="'.$name.'" />';
						    break;
							
						case 'select':						
							if (isset($array[$key]['predefined_options']) && $array[$key]['predefined_options']!= '' && $array[$key]['predefined_options']!= '0' )
							
							{
								$loop = $this->commmonmethods->get_predifined( $array[$key]['predefined_options'] );
								
							}elseif (isset($array[$key]['choices']) && $array[$key]['choices'] != '') {
								
															
								$loop = $this->uultra_one_line_checkbox_on_window_fix($choices);
								 	
								
							}
							
							if (isset($loop)) 
							{
								$display .= '<select class="'.$required_class.' wptu-input" name="'.$meta.'" id="'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'">';
								
								foreach($loop as $option)
								{
									
								$option = trim(stripslashes($option));
								
								    
								$display .= '<option value="'.$option.'" '.selected( $this->get_post_value($meta), $option, 0 ).'>'.$option.'</option>';
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
													
								
								 $loop = $this->uultra_one_line_checkbox_on_window_fix($choices);
								
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
									
									$display .= '<input type="radio" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="wptu_multi_radio_'.$meta.'_'.$counter.'" value="'.$option.'" '.checked( $this->get_post_value($meta), $option, 0 );
									$display .= '/> <label for="wptu_multi_radio_'.$meta.'_'.$counter.'"><span>'.$option.'</span></label>';
									
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
																
								 $loop = $this->uultra_one_line_checkbox_on_window_fix($choices);
								
								
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
								  
								  $display .= '<input type="checkbox" class="'.$required_class.'" title="'.$name.'" name="'.$meta.'[]" id="wptu_multi_box_'.$meta.'_'.$counter.'" value="'.$option.'" ';
									if (is_array($this->get_post_value($meta)) && in_array($option, $this->get_post_value($meta) )) {
									$display .= 'checked="checked"';
									}
									$display .= '/> <label for="wptu_multi_box_'.$meta.'_'.$counter.'"> '.$option.'</label> ';
									
									$display .= '<li>';
									$counter++;
								}
							}
							
							$display .= '</ul>';	
							
							break;
							
						
													
						case 'password':						
							$display .= '<input type="password" class="bup-input'.$required_class.'" title="'.$name.'" name="'.$meta.'" id="'.$meta.'" value="'.$this->get_post_value($meta).'" />';
							
							
							break;
							
					}
					
					
					if (isset($array[$key]['help_text']) && $help_text != '') 
					{
						$display .= '<div class="wptu-help">'.$help_text.'</div>';
					}
							
					
				$display .= '</div>';
				$display .= '</div>';
				
			}
		} //end while
		
		
		return $display;
		
		
	}
	
	function get_sites_to_submit_by_admin($user_id)
	{
		
		global $wptu_register,  $wptu_captcha_loader, $wptucomplement;
		
		$html = '';
		
		
		$sites = $this->get_my_allowed_sites_list($user_id);
		
		if (!empty($sites))
		{
			$html .= '<ul>';				
			foreach($sites as $site) 
			{
				
						
				$html .= '<li><a href="?page=wpticketultra&tab=createticket&wptu_site='.$site->site_id.'">'.$site->site_name.'</a></li>';		
				
				
			}
			$html .= '</ul>';	
		}
		
		return $html;
		
	}
	
	function get_sites_to_submit_by_staff($user_id)
	{
		
		global $wptu_register,  $wptu_captcha_loader, $wptucomplement;
		
		$html = '';
		
		
		$sites = $this->get_my_allowed_sites_list($user_id);
		
		if (!empty($sites))
		{
			$html .= '<ul class="wptu-products-select">';				
			foreach($sites as $site) 
			{
				
						
				$html .= '<li><a href="?module=submit&wptu_site='.$site->site_id.'">'.$site->site_name.'</a></li>';		
				
				
			}
			$html .= '</ul>';	
		}
		
		return $html;
		
	}
	
	function get_my_allowed_sites_list($user_id)
	{
		
		global $wpticketultra, $wpdb  , $wptucomplement;
		
		
		$is_client  = $wpticketultra->profile->is_client($user_id); 
		
		$is_super_admin =  $wpticketultra->profile->is_user_admin($user_id);
		
		if($is_client || $is_super_admin)	 // is a client or admin, then get all websites
		{
			$sql = ' SELECT * FROM ' . $wpdb->prefix . $this->table_prefix.'_sites ORDER BY site_name ' ;
			
		}else{ //is a staff member then get all websites the staff can submit a post in
			
			$departments_ids = $wpticketultra->userpanel->get_all_staff_allowed_deptos_list($user_id);			

		
			$sql = ' SELECT  site.*, dep.* ,  deptoallowed.* FROM ' . $wpdb->prefix . $this->table_prefix.'_sites as site ' ;
				
			$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix."_departments dep ON (dep.department_site_id = site.site_id)";				
				
			$sql .= " RIGHT JOIN ".$wpdb->prefix . $this->table_prefix."_department_staff deptoallowed ON (deptoallowed.depto_department_id = dep.department_id)";		
						
			$sql .= " WHERE dep.department_site_id = site.site_id AND deptoallowed.depto_department_id = dep.department_id AND deptoallowed.depto_staff_id = '".$user_id."' " ;			
		
			//display only allowed departments		
			$sql .= " AND dep.department_id  IN (".$departments_ids.") ";
			$sql .= " GROUP BY site.site_id ";
		
		}
		
		//echo $sql;
			
		$res = $wpdb->get_results($sql);
		return $res;
	
	}
	
	function get_staff_sites()
	{
		
		global $wptu_register,  $wptu_recaptcha, $wptucomplement;
		
		$display = '';
		
		//priority
		$display .= '<div class="wptu-profile-field">';				
									
		$display .= '<label class="wptu-field-type" for="user_email_2">';
		$display .= '<span>'.__('Site', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
					
		$display .= '<div class="wptu-field-value">';				
		$display .= $this->get_priority_values();					
		$display .= '</div>';		
		$display .= '</div>';
	
	
	}
	
	/* This is the Ticket Form  */
	function display_ticket_form( $redirect_to=null , $args)
	{
		global $wptu_register,  $wptu_recaptcha, $wptucomplement, $wptu_wooco, $wptu_aweber, $wptu_guest_ticket;
		$display = null;
		
		 if(is_user_logged_in())
		 {			 
			 //remove name and email from registration array.
			 
			 //get current user info
			 $current_user = $this->userpanel->get_user_info();
			 $user_id = $current_user->ID;			 
			 $is_client  = $this->profile->is_client($user_id); 
		
		 }
		 
		 
		
		
		//print_r($args);
						
		extract( $args, EXTR_SKIP );
		
		if(isset($_GET['wptu_site']) && $_GET['wptu_site']!=''){
			
			$site_id = $_GET['wptu_site'];
			
		}
		
		$order_id = '';
		
		if(isset($_GET['order_id']) && $_GET['order_id']!=''){
			
			$order_id = $_GET['order_id'];
			
		}
		
		if(isset($product_id) && $product_id!=''){
			
			$site_id =$product_id;
			
		}
				
		// Optimized condition and added strict conditions
		if (!isset($wptu_register->registered) || $wptu_register->registered != 1)
		{
		
		$display .= '<form action="" method="post" id="wptu-registration-form" name="wptu-registration-form" enctype="multipart/form-data">';
	//	$display .= wp_nonce_field('ticketultra_nonce_action','ticketultra_nonce');
            
        $display .= wp_nonce_field('wpticketultra_reg_action', 'wpticketultra_csrf_token');

		if($display_sites == 'yes')
		{			
			$display .= $this->get_staff_sites();		
		
		}else{
			
			$display .= '<input type="hidden" name="wptu_site" id="wptu_site" value="'.$site_id.'" />';			
		
		}
		
		 if(is_user_logged_in() && !$is_client)
		 {
			
			//is a woocommerce order?
			
			if(isset($_GET['order_id']) && $_GET['order_id']!='' && isset($wptu_wooco) && class_exists( 'WooCommerce' ))
			{
				$order_id = $_GET['order_id'];
				
				$order = wc_get_order($order_id);				
				$client_id = $order->get_user_id() ;
				
				$display .= '<div class="wptu-profile-field">';	
				
				$display .= '<input type="hidden" name="wptu_client_id" id="wptu_client_id" value="'.$client_id.'" />';
				
				$display .= '</div>';
				
			
			}else{
			 
				$required_class = ' validate[required]';
				$required_text = '(*)';	
			
				//select client
				$display .= '<div class="wptu-profile-field">';				
											
				$display .= '<label class="wptu-field-type" for="user_email_2">';
				$display .= '<span>'.__('Select a User', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
							
				$display .= '<div class="wptu-field-value">';				
				$display .=  '<input type="text" class="wptu-client-selector '.$required_class.'" id="wptuclientsel" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"  name="wptuclientsel" placeholder="'.__('Input Name or Email Address','wp-ticket-ultra').'" />'	;
				
				$display .= '<input type="hidden" name="wptu_client_id" id="wptu_client_id" value="" />';	
				
				$display .=  ' <span class="wptu-add-client-m"><a href="#" id="wptu-btn-client-new-admin" title="'.__('Add New User','wp-ticket-ultra').'"><i class="fa fa-user-plus"></i></a></span> ';			
				$display .= '</div>';		
				$display .= '</div>';
			
			}
		
		}
		
		
		 if(is_user_logged_in() && class_exists( 'WooCommerce' ) && isset($wptu_wooco) && isset($order_id) && $order_id!='') 
		 {
			 
			$product_id = '';
			if(isset($_GET['product_id']) && $_GET['product_id']!=''){
				
				$product_id = $_GET['product_id'];
			}			 
			 
			$required_class = ' validate[required]';
			$required_text = '(*)';	
		
			//select client
			$display .= '<div class="wptu-profile-field">';				
										
			$display .= '<label class="wptu-field-type" for="user_email_2">';
			$display .= '<span>'.__('Select a Product', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
						
			$display .= '<div class="wptu-field-value">';				
			$display .= $wptu_wooco->get_order_items($order_id, $product_id);
			$display .= '<input type="hidden" name="woo_order_id" id="woo_order_id" value="'.$order_id.'" />';
					
			$display .= '</div>';		
			$display .= '</div>';
		
		}
		
		$exclude_from_form = array('display_name', 'last_name' , 'user_email');
		
		
		/* These are the basic registrations fields */		
		foreach($this->registration_fields as $key=>$field) 
		{
			extract($field);
			
			//check if exclude user from registration.
			
			$include_username =  true;
			
			if($this->get_option('allow_registering_only_with_email')=='yes')
			{
				if($meta=='user_login')
				{
					$include_username =  false;
				
				}
			
			}
			
			if(is_user_logged_in() && in_array($meta,$exclude_from_form))
		 	{
				continue;
				
			}			
			
			if ( $type == 'usermeta' && $include_username) {
				
				$display .= '<div class="wptu-profile-field">';
				
				if(!isset($required))
				    $required = 0;
				
				$required_class = '';				
				$required_text = '';
				
				if($required == 1 && in_array($field, $this->include_for_validation))
				{
					$required_class = ' validate[required]';
					$required_text = '(*)';
				}
				
				/* Show the label */
				if (isset($this->registration_fields[$key]['name']) && $name) 
				{
					$display .= '<label class="wptu-field-type" for="'.$meta.'">';
					
					if (isset($this->registration_fields[$key]['icon']) && $icon)
					 {
						$display .= '<i class="fa fa-'.$icon.'"></i>';
					} else {
						//$display .= '<i class="fa fa-none"></i>';
					}
					
					$display .= '<span>'.$name.' '.$required_text.'</span></label>';
					
				} else {
					$display .= '<label class="wptu-field-type">&nbsp;</label>';
				}
				
				
				
				$display .= '<div class="wptu-field-value">';				
				
					
					switch($field) {					
						
						case 'textarea':
							$display .= '<textarea class="'.$required_class.' wptu-input wptu-input-text-area" name="'.$meta.'" id="reg_'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'">'.$this->get_post_value($meta).'</textarea>';
							break;
						
						case 'text':
							$display .= '<input type="text" class="'.$required_class.' wptu-input " name="'.$meta.'" id="reg_'.$meta.'" value="'.$this->get_post_value($meta).'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
							
							if (isset($this->registration_fields[$key]['help']) && $help != '') {
								
								if($meta=='user_email' && $this->get_option('ticket_sub_help_texte')!='')
								{
									$help = $this->get_option('ticket_sub_help_texte');
									
								}								
								
								$display .= '<div class="wptu-help">'.$help.'</div>';
							}
							
							break;
							
							case 'datetime':
							    
							    $display .= '<input type="text" class="'.$required_class.' bup-input wptu-input-datepicker" name="'.$meta.'" id="reg_'.$meta.'" value="'.$this->get_post_value($meta).'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
							    
							    if (isset($this->registration_fields[$key]['help']) && $help != '') {
							        $display .= '<div class="wptu-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							    }
							    break;							
					   
							
						case 'password':

							$display .= '<input type="password" class="'.$required_class.' wptu-input password" name="'.$meta.'" id="reg_'.$meta.'" value="" autocomplete="off" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'" />';
							
							if (isset($this->registration_fields[$key]['help']) && $help != '') {
								$display .= '<div class="wptu-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							}

							break;												
							
							
						case 'password_indicator':
							$display .= '<div class="password-meter"><div class="password-meter-message" id="password-meter-message">&nbsp;</div></div>';
							break;
							
					}								
					
					
					
				$display .= '</div>';				
				$display .= '</div>';
								
				
				//re-type password				
				if($meta=='user_email')
				{
					$required_class = ' validate[required]';
					$required_text = '(*)';
					
					$display .= '<div class="wptu-profile-field">';
					
									
					$display .= '<label class="wptu-field-type" for="user_email_2">';
					//$display .= '<i class="fa fa-envelope"></i>';	
					$display .= '<span>'.__('Re-type your email', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
					
					$display .= '<div class="wptu-field-value">';
				
					$display .= '<input type="text" class="'.$required_class.' wptu-input " name="user_email_2" id="reg_user_email_2" value="'.$this->get_post_value('user_email_2').'" title="Re-type your email." data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
					
					
					$display .= '</div>';
					$display .= '</div>';
					
				
				}
				
				
			}
			
								
		}
		
		//priority
		$display .= '<div class="wptu-profile-field">';				
									
		$display .= '<label class="wptu-field-type" for="user_email_2">';
		$display .= '<span>'.__('Priority', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
					
		$display .= '<div class="wptu-field-value">';				
		$display .= $this->get_priority_values();					
		$display .= '</div>';		
		$display .= '</div>';
		
		//department			
		$required_class = ' validate[required]';
		$required_text = '(*)';	
		$display .= '<div class="wptu-profile-field">';								
							
		$display .= '<div class="wptu-field-value">';
				
		$display .= $this->department->get_categories_drop_down_public($site_id);
					
					
		$display .= '</div>';		
		$display .= '</div>';
		
		
		//custom fields for department		
		$display .= '<div class="wptu-custom-fields" id="wp-custom-fields-public">';
		
		$display .= '</div>';
		
		//uploader		
		$display .= '<div class="wptu-custom-fields" id="wp-file-uploader-front">';
		
		$display .= $this->front_end_file_uploader();
		
		
		
		$display .= '</div>';
		
		
		
		
		$custom_site = '';

		if(isset($_GET["wptu-custom-site-id"]))
		{ 
			$custom_site=$_GET["wptu-custom-site-id"];
		}
		
		
		///
		
		////
		
				
		/*If mailchimp*/		
		if($this->get_option('newsletter_active')=='mailchimp' && $this->get_option('mailchimp_api')!="" && isset($bupcomplement))
		{
			
			//new mailchimp field			
			$mailchimp_text = stripslashes($this->get_option('mailchimp_text'));
			$mailchimp_header_text = stripslashes($this->get_option('mailchimp_header_text'));
			
			if($mailchimp_header_text==''){
				
				$mailchimp_header_text = __('Receive Daily Updates ', 'wp-ticket-ultra');				
			}			
			
			
			//
			
			$mailchimp_autchecked = $this->get_option('mailchimp_auto_checked');
			
			$mailchimp_auto = '';
			if($mailchimp_autchecked==1){
				
				$mailchimp_auto = 'checked="checked"';				
			}
			
			 $display .= '<div class="bup-profile-separator">'.$mailchimp_header_text.'</div>';
			 
			 $display .= '<div class="bup-profile-field " style="text-align:left">';
			
						
			// $display .= '<label class="bup-field-type" for="'.$meta.'">';			
			//$display .= '<span>&nbsp;</span></label>';
			
			//$display .= '<div class="bup-field-value">';
			 $display .= '<input type="checkbox"  title="'.$mailchimp_header_text.'" name="bup-mailchimp-confirmation"  id="bup-mailchimp-confirmation" value="1"  '.$mailchimp_auto.' > <label for="bup-mailchimp-confirmation"><span></span>'.$mailchimp_text.'</label>' ;
			
			//$display .= '</div>';
			
			// $display .= '</label>';
			
			 $display .= '<div class="bup-field-value "></div>';
									
			 $display .= '</div>';
			
		
		}
		
		/*If aweber*/		
		if($this->get_option('newsletter_active')=='aweber' && $this->get_option('aweber_consumer_key')!="" && isset($wptu_aweber) && !is_user_logged_in())
		{
			
			//new aweber field			
			$aweber_text = stripslashes($this->get_option('aweber_text'));
			$aweber_header_text = stripslashes($this->get_option('aweber_header_text'));
			
			if($aweber_header_text==''){
				
				$aweber_header_text = __('Receive Daily Updates ', 'wp-ticket-ultra');				
			}	
			
			if($aweber_text==''){
				
				$aweber_text = __('Yes, I want to receive daily updates. ', 'wp-ticket-ultra');				
			}			
			
			
			//
			
			$aweber_autchecked = $this->get_option('aweber_auto_checked');
			
			$aweber_auto = '';
			if($aweber_autchecked==1){
				
				$aweber_auto = 'checked="checked"';				
			}
			
			 $display .= '<div class="wptu-profile-separator">'.$aweber_header_text.'</div>';			 
			 $display .= '<div class="wptu-profile-field " style="text-align:left">';			
						
			 $display .= '<input type="checkbox"  title="'.$aweber_header_text.'" name="wptu-aweber-confirmation"  id="wptu-aweber-confirmation" value="1"  '.$aweber_auto.' > <label for="wptu-aweber-confirmation"><span></span>'.$aweber_text.'</label>' ;								
			 $display .= '</div>';
			
		
		}		
		
		//recaptcha			
		if(isset($wptu_recaptcha) && $this->get_option('recaptcha_site_key')!='' && $this->get_option('recaptcha_secret_key')!='' && $this->get_option('recaptcha_display_registration')=='1'){	
		
			$display .= '<div class="wptu-profile-field">';			
			$display .= $wptu_recaptcha->recaptcha_field(); 				
			$display .= '</div>'; 		
		}
		
		
				
		$display .= '<p>&nbsp;</p>';
		$display .= '<div class="wptu-field ">
						<label class="wptu-field-type "><button name="wptu-btn-app-confirm" id="wptu-btn-app-confirm" class="wptu-btn-submit-ticket">'.__('Submit','wp-ticket-ultra').'	</button></label>
						<div class="wptu-field-value">
						    <input type="hidden" name="wptu-register-form" value="wptu-register-form" />
														
							
							
						</div>
					</div>';
					
		$display .= '<div class="wptu-profile-field-cc" id="wptu-stripe-payment-errors"></div>';
					
					
					
					
		if ($redirect_to != '' )
		{
			$display .= '<input type="hidden" name="redirect_to" value="'.$redirect_to.'" />';
		}
		
		$display .= '</form>';
		
		} 
		
		
		return $display;
	}
	
	/* This is the Ticket Form  */
	function display_ticket_form_as_admin( $redirect_to=null , $args)
	{
		global $wptu_register,  $wptu_captcha_loader, $wptucomplement;
		$display = null;
		
		 if(is_user_logged_in())
		 {
			 
			 //remove name and email from registration array.
			 
			 //get current user info
			 $current_user = $this->userpanel->get_user_info();
			 $user_id = $current_user->ID;			 
			 $is_client  = $this->profile->is_client($user_id);
			 $is_super_admin =  $this->profile->is_user_admin($user_id);

			 
		
		 }else{
			 
			 echo "error";
			 return;
			 
		  }
		  
		 // if( $is_client) {echo "is client";}
		
		
		
						
		extract( $args, EXTR_SKIP );
		
		if(isset($_GET['wptu_site']) && $_GET['wptu_site']!=''){
			
			$site_id = $_GET['wptu_site'];
			
		}
				
		// Optimized condition and added strict conditions
		if (!isset($wptu_register->registered) || $wptu_register->registered != 1)
		{
		
		$display .= '<form action="" method="post" id="wptu-registration-form" name="wptu-registration-form" enctype="multipart/form-data">';
		
		
		if($display_sites == 'yes')
		{			
			$display .= $this->get_staff_sites();		
		
		}else{
			
			$display .= '<input type="hidden" name="wptu_site" id="wptu_site" value="'.$site_id.'" />';			
		
		}
		
		 if( (is_user_logged_in() && !$is_client) || $is_super_admin)
		 {
			$required_class = ' validate[required]';
			$required_text = '(*)';	
			
			
			
			//select staff
			$display .= '<div class="wptu-profile-field">';				
										
			$display .= '<label class="wptu-field-type" for="user_email_2">';
			$display .= '<span>'.__('Select a Staff Member', 'wp-ticket-ultra').' </span></label>';
						
			$display .= '<div class="wptu-field-value">';				
			$display .=  '<input type="text" class="wptu-client-selector " id="wptustaffsel"  name="wptustaffsel" placeholder="'.__('Input Name or Email Address','wp-ticket-ultra').'" />'	;
			$display .=  '<div class="wptu-help">'.__("Leave it blank if you want to create a ticket without an assigned staff. Only staff members will be displayed in this list.",'wp-ticket-ultra').'</div>'	;
			
			
			
			$display .= '<input type="hidden" name="wptu_staff_id" id="wptu_staff_id" value="" />';			
		
			$display .= '</div>';		
			$display .= '</div>';

			//select client
			$display .= '<div class="wptu-profile-field">';				
										
			$display .= '<label class="wptu-field-type" for="user_email_2">';
			$display .= '<span>'.__('Select a User', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
						
			$display .= '<div class="wptu-field-value">';				
			$display .=  '<input type="text" class="wptu-client-selector '.$required_class.'" id="wptuclientsel" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"  name="wptuclientsel" placeholder="'.__('Input Name or Email Address','wp-ticket-ultra').'" />'	;
			$display .=  '<div class="wptu-help">'.__("A user is required to create a new ticket. Only users that are not staff members will be displayed in this list.",'wp-ticket-ultra').'</div>'	;
			
			$display .= '<input type="hidden" name="wptu_client_id" id="wptu_client_id" value="" />';	
			
			$display .=  ' <span class="wptu-add-client-m"><a href="#" id="wptu-btn-client-new-admin" title="'.__('Add New User','wp-ticket-ultra').'"><i class="fa fa-user-plus"></i></a></span> ';			
			$display .= '</div>';		
			$display .= '</div>';
		
		}
		
		$exclude_from_form = array('display_name', 'last_name' , 'user_email');
		
		
		/* These are the basic registrations fields */		
		foreach($this->registration_fields as $key=>$field) 
		{
			extract($field);
			
			//check if exclude user from registration.
			
			$include_username =  true;
			
			if($this->get_option('allow_registering_only_with_email')=='yes')
			{
				if($meta=='user_login')
				{
					$include_username =  false;
				
				}
			
			}
			
			if(is_user_logged_in() && in_array($meta,$exclude_from_form))
		 	{
				continue;
				
			}			
			
			if ( $type == 'usermeta' && $include_username) {
				
				$display .= '<div class="wptu-profile-field">';
				
				if(!isset($required))
				    $required = 0;
				
				$required_class = '';				
				$required_text = '';
				
				if($required == 1 && in_array($field, $this->include_for_validation))
				{
					$required_class = ' validate[required]';
					$required_text = '(*)';
				}
				
				/* Show the label */
				if (isset($this->registration_fields[$key]['name']) && $name) 
				{
					$display .= '<label class="wptu-field-type" for="'.$meta.'">';
					
					if (isset($this->registration_fields[$key]['icon']) && $icon)
					 {
						$display .= '<i class="fa fa-'.$icon.'"></i>';
					} else {
						//$display .= '<i class="fa fa-none"></i>';
					}
					
					$display .= '<span>'.$name.' '.$required_text.'</span></label>';
					
				} else {
					$display .= '<label class="wptu-field-type">&nbsp;</label>';
				}
				
				
				
				$display .= '<div class="wptu-field-value">';				
				
					
					switch($field) {					
						
						case 'textarea':
							$display .= '<textarea class="'.$required_class.' wptu-input wptu-input-text-area" name="'.$meta.'" id="reg_'.$meta.'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'">'.$this->get_post_value($meta).'</textarea>';
							break;
						
						case 'text':
							$display .= '<input type="text" class="'.$required_class.' wptu-input " name="'.$meta.'" id="reg_'.$meta.'" value="'.$this->get_post_value($meta).'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
							
							if (isset($this->registration_fields[$key]['help']) && $help != '') {
								$display .= '<div class="wptu-help">'.$help.'</div>';
							}
							
							break;
							
							case 'datetime':
							    
							    $display .= '<input type="text" class="'.$required_class.' bup-input wptu-input-datepicker" name="'.$meta.'" id="reg_'.$meta.'" value="'.$this->get_post_value($meta).'" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
							    
							    if (isset($this->registration_fields[$key]['help']) && $help != '') {
							        $display .= '<div class="wptu-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							    }
							    break;							
					   
							
						case 'password':

							$display .= '<input type="password" class="'.$required_class.' wptu-input password" name="'.$meta.'" id="reg_'.$meta.'" value="" autocomplete="off" title="'.$name.'" data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'" />';
							
							if (isset($this->registration_fields[$key]['help']) && $help != '') {
								$display .= '<div class="wptu-help">'.$help.'</div><div class="xoouserultra-clear"></div>';
							}

							break;												
							
							
						case 'password_indicator':
							$display .= '<div class="password-meter"><div class="password-meter-message" id="password-meter-message">&nbsp;</div></div>';
							break;
							
					}								
					
					
					
				$display .= '</div>';				
				$display .= '</div>';
								
				
				//re-type password				
				if($meta=='user_email')
				{
					$required_class = ' validate[required]';
					$required_text = '(*)';
					
					$display .= '<div class="wptu-profile-field">';
					
									
					$display .= '<label class="wptu-field-type" for="user_email_2">';
					$display .= '<i class="fa fa-envelope"></i>';	
					$display .= '<span>'.__('Re-type your email', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
					
					$display .= '<div class="wptu-field-value">';
				
					$display .= '<input type="text" class="'.$required_class.' wptu-input " name="user_email_2" id="reg_user_email_2" value="'.$this->get_post_value('user_email_2').'" title="Re-type your email." data-errormessage-value-missing="'.__(' * This input is required!','wp-ticket-ultra').'"/>';
					
					
					$display .= '</div>';
					$display .= '</div>';
					
				
				}
				
				
			}
			
								
		}
		
		//priority
		$display .= '<div class="wptu-profile-field">';				
									
		$display .= '<label class="wptu-field-type" for="user_email_2">';
		$display .= '<span>'.__('Priority', 'wp-ticket-ultra').' '.$required_text.'</span></label>';
					
		$display .= '<div class="wptu-field-value">';				
		$display .= $this->get_priority_values();					
		$display .= '</div>';		
		$display .= '</div>';
		
		//department			
		$required_class = ' validate[required]';
		$required_text = '(*)';	
		$display .= '<div class="wptu-profile-field">';								
							
		$display .= '<div class="wptu-field-value">';
				
		$display .= $this->department->get_categories_drop_down_public($site_id);
					
					
		$display .= '</div>';		
		$display .= '</div>';
		
		
		//custom fields for department		
		$display .= '<div class="wptu-custom-fields" id="wp-custom-fields-public">';
		
		$display .= '</div>';
		
		//uploader		
		$display .= '<div class="wptu-custom-fields" id="wp-file-uploader-front">';
		
		$display .= $this->front_end_file_uploader();
		
		
		
		$display .= '</div>';
		
		
		
		
		$custom_site = '';

		if(isset($_GET["wptu-custom-site-id"]))
		{ 
			$custom_site=$_GET["wptu-custom-site-id"];
		}
		
		
		///
		
					
				
		$display .= '<p>&nbsp;</p>';
		$display .= '<div class="wptu-field ">
						<label class="wptu-field-type "><button name="wptu-btn-app-confirm" id="wptu-btn-app-confirm" class="wptu-btn-submit-ticket">'.__('Submit','wp-ticket-ultra').'	</button></label>
						<div class="wptu-field-value">
						    <input type="hidden" name="wptu-register-form" value="wptu-register-form" />
														
							
							
						</div>
					</div>';
					
		$display .= '<div class="wptu-profile-field-cc" id="wptu-stripe-payment-errors"></div>';
					
					
					
					
		
		$display .= '</form>';
		
		} 
		
		
		return $display;
	}
	
	// File upload handler:
	function front_upload_files()
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
		
		$allowed_extensions =$wpticketultra->get_option('allowed_extensions');
		$allowed_extensions = explode(",", $allowed_extensions);
		
		
		if($original_max_width=="" || $original_max_height=="")
		{			
			$original_max_width = 80;			
			$original_max_height = 80;
			
		}
		
			
				
		$o_id = sanitize_text_field($_POST['temp_ticket_id']);
		$upload_folder_temp = $this->upload_folder_temp;
		
				
		$info = pathinfo($file['name']);
		$real_name = $file['name'];
        $ext = $info['extension'];
		$ext=strtolower($ext);
		
		$rand = $this->userpanel->genRandomString();
		
		$rand_name = "file_".$rand."_".time(); 		
	
		$upload_dir = wp_upload_dir(); 
		$path_pics =   $upload_dir['basedir'];
			
			
		if(in_array($ext,$allowed_extensions)) 
		{
			if($o_id != '')
			{
				
				if(!is_dir($path_pics."/".$upload_folder_temp."")) 
				{
					 wp_mkdir_p( $path_pics."/".$upload_folder_temp );							   
				}
				
				if(!is_dir($path_pics."/".$o_id."")) 
				{
					 wp_mkdir_p( $path_pics."/".$upload_folder_temp."/".$o_id );							   
				}					
										
				$pathBig = $path_pics."/".$upload_folder_temp."/".$o_id."/".$rand_name.".".$ext;						
					
					
					if (copy($file['tmp_name'], $pathBig)) 
					{
						//check auto-rotation						
						if($wpticketultra->get_option('avatar_rotation_fixer')=='yes')
						{
							//$this->orient_image($pathBig);
						
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
	
	
	public function front_end_file_uploader() 
	{
		
		$rand = $this->userpanel->genRandomString();
		
		$temp_ticket_id = "wptu_".$rand."_".time();
		
		$allowed_extensions = $this->get_option('allowed_extensions');
		
				
		// Uploading functionality trigger:
		// (Most of the code comes from media.php and handlers.js)
		 $template_dir = get_template_directory_uri();
		 

		
		$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'wptu-browse-button-files',
				'container'           => 'wp-file-uploader-front',
				'drop_element'        => 'drag-drop-area-sitewidewall',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => true,
				'max_file_size'       => wp_max_upload_size().'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				
				'filters'             => array(array('title' => __('Allowed Files', "wpticku"), 'extensions' => "$allowed_extensions")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'temp_ticket_id' => $temp_ticket_id,
					'action'      => 'wptu_front_upload_files' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			
		
		 
		   
		   $html = '';
		   
				
		   $html = '<div id="uploadContainer" style="margin-top: 10px;" class="wptu-avatar-drag-drop-sector">
		   
		   <input type="hidden" name="wptu_temp_ticket_id" value="'.$temp_ticket_id.'" id="wptu_temp_ticket_id" />
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
			
				<div id="plupload-upload-ui-sitewidewall" class="hide-if-no-js">
				
				
                
					<div id="drag-drop-area-sitewidewall">';					
					
					$html .= '<div class="drag-drop-inside">';
					
						$html .= '<p class="drag-drop-info">'.__('Drop files here or', 'wp-ticket-ultra').'</p>';
																	
								
						$html .= '<p>
														  
								<button name="wptu-browse-button-files" type="button" id="wptu-browse-button-files" class="wptu-button-upload-avatar" ><span><i class="fa fa-files-o"></i></span>'.__('Select Files', 'wp-ticket-ultra').'</button>
								</p>';
					
					$html .= '</div>';
					
					
					
					
						                        
                   $html .= '     <div id="progressbar-sitewidewall"></div>                 
                         <div id="wptu_filelist_sitewidewall" class="cb"></div>
						 
						 
						 
					</div>
					
				
				</div>
                
                 
			
			</div>
            
           
		</div>';
		
		
			 
			 
			$js_messages_one_file = __("'You may only upload one image at a time!'", 'wp-ticket-ultra');
			$js_messages_file_size_limit = __("'The file you selected exceeds the maximum filesize limit.'", 'wp-ticket-ultra');
			$js_messages_upload_completed = __("Upload Completed!", 'wp-ticket-ultra');
			
			

			$html .= '<script type="text/javascript">';
			
			$html .= "jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_sitewidewall = new plupload.Uploader(".json_encode($plupload_init).");

					// Check for drag'n'drop functionality:
					uploader_sitewidewall.bind('Init', function(up){
						
					var uploaddiv_sitewidewall = $('#wp-file-uploader-front');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_sitewidewall.addClass('drag-drop');
							
							$('#drag-drop-area-sitewidewall')
								.bind('dragover.wp-uploader', function(){ uploaddiv_sitewidewall.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_sitewidewall.removeClass('drag-over'); });

						} else{
							uploaddiv_sitewidewall.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_sitewidewall.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_sitewidewall.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
												
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert($js_messages_file_size_limit);
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							
							//fix this
							
							jQuery('#wptu_filelist_sitewidewall').append('<div class=addedFile id=' + file.id + '>' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_sitewidewall.start();
						
						//alert('start here');
						$( '#wptu-wall-photo-uploader-box' ).slideDown();
						$( '#progressbar-sitewidewall' ).slideDown();
						
						
					});
					
					// A new file was uploaded:
					uploader_sitewidewall.bind('FileUploaded', function(up, file, response){
						
						var obj = jQuery.parseJSON(response.response);												
						var img_name = obj.image;
						
						var reset_value = 0;
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + reset_value + '%</span>');
						
						
						
						
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_sitewidewall.bind('Error', function(up, err) {
						alert('Error: ' + err.code + ', Message: ' + err.message + (err.file ? ', File: ' + err.file.name : '') );
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_sitewidewall.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: progressBarValue
						});
						
						//fix this
						
						//jQuery('#progressbar-sitewidewall').html('<span class='progressTooltip'>' + up.total.percent + '%</span>');
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + up.total.percent + '%</span>');
						
						
						
					});
					
					// Close window after upload ///////////////////////////////
					uploader_sitewidewall.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: 0
						});
						
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>".$js_messages_upload_completed."</span>');
						
						
					});
					
					
					
				}); ";
				
					
			$html .= '</script>';
			
			
			//}
			
			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init);
			
		
		
		return $html;
	
	
	}
	
	public function backend_end_file_uploader() 
	{
		
		$rand = $this->userpanel->genRandomString();
		
		$temp_ticket_id = "wptu_".$rand."_".time();
		
				
		// Uploading functionality trigger:
		// (Most of the code comes from media.php and handlers.js)
		 $template_dir = get_template_directory_uri();
		 
		 $allowed_extensions = $this->get_option('allowed_extensions');
		 
		 ///"php","asp","aspx","cmd","csh","bat","html","htm","hta","jar","exe","com","js","lnk","htaccess","phtml","ps1","ps2","php3","php4","php5","php6","py","rb","tmp"
		
		$plupload_init = array(
				'runtimes'            => 'html5,silverlight,flash,html4',
				'browse_button'       => 'wptu-browse-button-files',
				'container'           => 'plupload-upload-ui-sitewidewall',
				'drop_element'        => 'wp-file-uploader-front',
				'file_data_name'      => 'async-upload',
				'multiple_queues'     => true,
				'multi_selection'	  => true,
				'max_file_size'       => wp_max_upload_size().'b',
				'url'                 => admin_url('admin-ajax.php'),
				'flash_swf_url'       => includes_url('js/plupload/plupload.flash.swf'),
				'silverlight_xap_url' => includes_url('js/plupload/plupload.silverlight.xap'),
				//'filters'             => array(array('title' => __('Allowed Files', $this->text_domain), 'extensions' => "jpg,png,gif,bmp,mp4,avi")),
				'filters'             => array(array('title' => __('Allowed Files', "wpticku"), 'extensions' => "$allowed_extensions")),
				'multipart'           => true,
				'urlstream_upload'    => true,

				// Additional parameters:
				'multipart_params'    => array(
					'_ajax_nonce' => wp_create_nonce('photo-upload'),
					'temp_ticket_id' => $temp_ticket_id,
					'action'      => 'wptu_front_upload_files' // The AJAX action name
					
				),
			);
			
			//print_r($plupload_init);

			
		
		 
		   
		   
		 //  if(!is_user_logged_in())
		  // {
			  // $html .="<p>".__("You have to be logged in to upload photos ",'d')."</p>";
			
		  // }else{

		
		   $html = '<div style="margin-top: 10px;" class="wptu-avatar-drag-drop-sector">
		   
		   <input type="hidden" name="wptu_temp_ticket_id" value="'.$temp_ticket_id.'" id="wptu_temp_ticket_id" />
			
			
			<!-- Uploader section -->
			<div id="uploaderSection" style="position: relative;">
			
				<div id="plupload-upload-ui-sitewidewall" class="hide-if-no-js">	
				
                
					<div id="drag-drop-area-sitewidewall">';					
					
					$html .= '<div class="drag-drop-inside">';
					
						$html .= '<p class="drag-drop-info">'.__('Drop files here or', 'wp-ticket-ultra').'</p>';
																	
								
						/*$html .= '<p>
														  
								<button name="wptu-browse-button-files" type="button" id="wptu-browse-button-files" class="wptu-button-upload-avatar" ><span><i class="fa fa-files-o"></i></span>'.__('Select Files', 'wp-ticket-ultra').'</button>
								</p>';*/
								
					$html .= '
														  
								<div name="wptu-browse-button-files" id="wptu-browse-button-files" class="wptu-button-upload-avatar" ><span><i class="fa fa-files-o"></i></span>'.__('Select Files', 'wp-ticket-ultra').'</div>
								';
					
					$html .= '</div>';
					
					
					
					
						                        
                   $html .= '     <div id="progressbar-sitewidewall"></div>                 
                         <div id="wptu_filelist_sitewidewall" class="cb"></div>
						 
						 
						 
					</div>
					
					
				
				</div>
                
                 
			
			</div>
            
           
		</div>';
			 
			 
			$js_messages_one_file = __("'You may only upload one image at a time!'", 'wp-ticket-ultra');
			$js_messages_file_size_limit = __("'The file you selected exceeds the maximum filesize limit.'", 'wp-ticket-ultra');
			$js_messages_upload_completed = __("Upload Completed!", 'wp-ticket-ultra');
			
			

			$html .= '<script type="text/javascript">';
			
			$html .= "jQuery(document).ready(function($){
					
					// Create uploader and pass configuration:
					var uploader_sitewidewall = new plupload.Uploader(".json_encode($plupload_init).");

					// Check for drag'n'drop functionality:
					uploader_sitewidewall.bind('Init', function(up){
						
					var uploaddiv_sitewidewall = $('#plupload-upload-ui-sitewidewall');
						
						// Add classes and bind actions:
						if(up.features.dragdrop){
							uploaddiv_sitewidewall.addClass('drag-drop');
							
							$('#drag-drop-area-sitewidewall')
								.bind('dragover.wp-uploader', function(){ uploaddiv_sitewidewall.addClass('drag-over'); })
								.bind('dragleave.wp-uploader, drop.wp-uploader', function(){ uploaddiv_sitewidewall.removeClass('drag-over'); });

						} else{
							uploaddiv_sitewidewall.removeClass('drag-drop');
							$('#drag-drop-area').unbind('.wp-uploader');
						}

					});

					
					// Init ////////////////////////////////////////////////////
					uploader_sitewidewall.init(); 
					
					// Selected Files //////////////////////////////////////////
					uploader_sitewidewall.bind('FilesAdded', function(up, files) {
						
						
						var hundredmb = 100 * 1024 * 1024, max = parseInt(up.settings.max_file_size, 10);
						
												
						// Loop through files:
						plupload.each(files, function(file){
							
							// Handle maximum size limit:
							if (max > hundredmb && file.size > hundredmb && up.runtime != 'html5'){
								alert($js_messages_file_size_limit);
								return false;
							}
						
						});
						
						jQuery.each(files, function(i, file) {
							
							//fix this
							
							jQuery('#wptu_filelist_sitewidewall').append('<div class=addedFile id=' + file.id + '>' + file.name + '</div>');
						});
						
						up.refresh(); 
						uploader_sitewidewall.start();
						
						//alert('start here');
						$( '#wptu-wall-photo-uploader-box' ).slideDown();
						$( '#progressbar-sitewidewall' ).slideDown();
						
						
					});
					
					// A new file was uploaded:
					uploader_sitewidewall.bind('FileUploaded', function(up, file, response){
						
						var obj = jQuery.parseJSON(response.response);												
						var img_name = obj.image;
						
						var reset_value = 0;
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + reset_value + '%</span>');
						
						
											
						
					
					});
					
					// Error Alert /////////////////////////////////////////////
					uploader_sitewidewall.bind('Error', function(up, err) {
						alert('Error: ' + err.code + ', Message: ' + err.message + (err.file ? ', File: ' + err.file.name : '') );
						up.refresh(); 
					});
					
					// Progress bar ////////////////////////////////////////////
					uploader_sitewidewall.bind('UploadProgress', function(up, file) {
						
						var progressBarValue = up.total.percent;
						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: progressBarValue
						});
						
						//fix this
						
						//jQuery('#progressbar-sitewidewall').html('<span class='progressTooltip'>' + up.total.percent + '%</span>');
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>' + up.total.percent + '%</span>');
						
						
						
					});
					
					// Close window after upload ///////////////////////////////
					uploader_sitewidewall.bind('UploadComplete', function() {
						
						//jQuery('.uploader').fadeOut('slow');						
						jQuery('#progressbar-sitewidewall').fadeIn().progressbar({
							value: 0
						});
						
						
						jQuery('#progressbar-sitewidewall').html('<span class=progressTooltip>".$js_messages_upload_completed."</span>');
						
						
					});
					
					
					
					
					
					
				}); ";
				
					
			$html .= ' </script>';
			
			
			//}
			
			
			// Apply filters to initiate plupload:
			$plupload_init = apply_filters('plupload_init', $plupload_init);
			
		
		
		return $html;
	
	
	}
	
	
	
	/**
	 * This has been added to avoid the window server issues
	 */
	public function uultra_one_line_checkbox_on_window_fix($choices)
	{		
		
		if($this->uultra_if_windows_server()) //is window
		{
			$loop = array();		
			$loop = explode(",", $choices);
		
		}else{ //not window
		
			$loop = array();		
			$loop = explode(PHP_EOL, $choices);	
			
		}	
		
		
		return $loop;
	
	}
	
	public function uultra_if_windows_server()
	{
		$os = PHP_OS;
		$os = strtolower($os);			
		$pos = strpos($os, "win");	
		
		if ($pos === false) {
			
			//echo "NO, It's not windows";
			return false;
		} else {
			//echo "YES, It's windows";
			return true;
		}			
	
	}
	
		
	
	
	


	

	
		
		
	public function get_current_url()
	{
		$result = 'http';
		$script_name = "";
		if(isset($_SERVER['REQUEST_URI'])) 
		{
			$script_name = $_SERVER['REQUEST_URI'];
		} 
		else 
		{
			$script_name = $_SERVER['PHP_SELF'];
			if($_SERVER['QUERY_STRING']>' ') 
			{
				$script_name .=  '?'.$_SERVER['QUERY_STRING'];
			}
		}
		
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') 
		{
			$result .=  's';
		}
		$result .=  '://';
		
		if($_SERVER['SERVER_PORT']!='80')  
		{
			$result .= $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'].$script_name;
		} 
		else 
		{
			$result .=  $_SERVER['HTTP_HOST'].$script_name;
		}
	
		return $result;
	}
	
	/* get setting */
	function get_option($option) 
	{
		$settings = get_option('wptu_options');
		if (isset($settings[$option])) 
		{
			if(is_array($settings[$option]))
			{
				return $settings[$option];
			
			}else{
				
				return stripslashes($settings[$option]);
			}
			
		}else{
			
		    return '';
		}
		    
	}
	
	/* Get post value */
	function uultra_admin_post_value($key, $value, $post){
		if (isset($_POST[$key])){
			if ($_POST[$key] == $value)
				echo 'selected="selected"';
		}
	}
	
	/*Post value*/
	function get_post_value($meta) {
				
				
		if (isset($_POST[$meta]) ) {
				return $_POST[$meta];
			}
			
			
	}
	
		

}
?>