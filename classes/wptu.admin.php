<?php
class WPTicketUltraAdmin extends WPTicketUltraCommon 
{

	var $options;
	var $wp_all_pages = false;
	var $wptu_default_options;
	var $valid_c;
	
	var $ajax_prefix = 'wptu';
	
	var $table_prefix = 'wptu';
	
	var $notifications_email = array();

	function __construct() {
	
		/* Plugin slug and version */
		$this->slug = 'wpticketultra';
		
		$this->set_default_email_messages();				
		$this->update_default_option_ini();		
		$this->set_font_awesome();
		
		
		add_action('admin_menu', array(&$this, 'add_menu'), 11);
	
		add_action('admin_enqueue_scripts', array(&$this, 'add_styles'), 9);
		add_action('admin_head', array(&$this, 'admin_head'), 9 );
		add_action('admin_init', array(&$this, 'admin_init'), 9);
		add_action('admin_init', array(&$this, 'do_valid_checks'), 9);
				
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_save_fields_settings', array( &$this, 'save_fields_settings' ));
				
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_add_new_custom_profile_field', array( &$this, 'add_new_custom_profile_field' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_delete_profile_field', array( &$this, 'delete_profile_field' ));
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_sort_fileds_list', array( &$this, 'sort_fileds_list' ));
		
		//user to get all fields
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_reload_custom_fields_set', array( &$this, 'reload_custom_fields_set' ));
		
		//used to edit a field
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_reload_field_to_edit', array( &$this, 'reload_field_to_edit' ));			
		
		add_action( 'wp_ajax_custom_fields_reset', array( &$this, 'custom_fields_reset' ));			
		add_action( 'wp_ajax_create_uploader_folder', array( &$this, 'create_uploader_folder' ));
		
		add_action( 'wp_ajax_'.$this->ajax_prefix.'_reset_email_template', array( &$this, 'reset_email_template' ));
		
		add_action( 'wp_ajax_bup_vv_c_de_a', array( &$this, 'bup_vv_c_de_a' ));
		
		
		
	}
	
	function admin_init() 
	{
		
		$this->tabs = array(
		    'main' => __('Dashboard','wp-ticket-ultra'),
			'tickets' => __('Tickets','wp-ticket-ultra'),
			'departments' => __('Products & Departments','wp-ticket-ultra'),
			'priority' => __('Ticket Property','wp-ticket-ultra'),
			'users' => __('Staff','wp-ticket-ultra'),						
			'fields' => __('Fields','wp-ticket-ultra'),
			'settings' => __('Settings','wp-ticket-ultra'),				
			'mail' => __('Notifications','wp-ticket-ultra'),		
			'help' => __('Doc','wp-ticket-ultra'),
			'pro' => __('GO PRO!','wp-ticket-ultra'),
		);
		
		$this->tabs_icons = array(
		    'main' => '',
			'tickets' => '',
			'departments' => '',
			'priority' =>'',
			'users' =>'',						
			'fields' => '',
			'settings' => '',				
			'mail' => '',		
			'help' => '',
		);		
		$this->default_tab = 'main';			
		
		$this->default_tab_membership = 'main';
		
		
	}
	
	public function update_default_option_ini () 
	{
		$this->options = get_option('wptu_options');		
		$this->bup_set_default_options();
		
		if (!get_option('wptu_options')) 
		{
			
			update_option('wptu_options', $this->wptu_default_options );
		}
		
		if (!get_option('wptu_pro_active')) 
		{
			
			update_option('wptu_pro_active', true);
		}	
		
		
	}
	
		
		
	
	
	function get_pending_verify_requests_count()
	{
		$count = 0;
		
		
		if ($count > 0){
			return '<span class="upadmin-bubble-new">'.$count.'</span>';
		}
	}
	
	function get_pending_verify_requests_count_only(){
		$count = 0;
		
		
		if ($count > 0){
			return $count;
		}
	}
	
	
	
	
	function admin_head(){
		$screen = get_current_screen();
		$slug = $this->slug;
		
	}

	function add_styles()
	{
		
		 global $wp_locale, $wpticketultra , $pagenow;
		 
		 if('customize.php' != $pagenow )
        {
		 
			wp_register_style('wptu_admin', wptu_url.'admin/css/admin.css');
			wp_enqueue_style('wptu_admin');
			
			wp_register_style('wptu_datepicker', wptu_url.'admin/css/datepicker.css');
			wp_enqueue_style('wptu_datepicker');
			
							
				
			//color picker		
			 wp_enqueue_style( 'wp-color-picker' );	
				 
			 wp_register_script( 'wptu_color_picker', wptu_url.'admin/scripts/color-picker-js.js', array( 
				'wp-color-picker'
			) );
			wp_enqueue_script( 'wptu_color_picker' );
			
			
			wp_register_script( 'wptu_admin',wptu_url.'admin/scripts/admin.js', array( 
				'jquery','jquery-ui-core','jquery-ui-draggable','jquery-ui-droppable',	'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-autocomplete', 'jquery-ui-widget', 'jquery-ui-position'	), null );
			wp_enqueue_script( 'wptu_admin' );
			
			
			/* Font Awesome */
			wp_register_style( 'wptu_font_awesome', wptu_url.'css/css/font-awesome.min.css');
			wp_enqueue_style('wptu_font_awesome');
			
			
			
			// Using imagesLoaded? Do this.
			//wp_enqueue_script('imagesloaded',  wptu_url.'js/qtip/imagesloaded.pkgd.min.js' , null, false, true);
			
			// Add the styles first, in the <head> (last parameter false, true = bottom of page!)
			wp_enqueue_style('qtip', wptu_url.'js/qtip/jquery.qtip.min.css' , null, false, false);
			wp_enqueue_script('qtip',  wptu_url.'js/qtip/jquery.qtip.min.js', array('jquery', 'imagesloaded'), false, true);
			
		}
		 
		
		 wp_localize_script( 'wptu_admin', 'wptu_admin_v98', array(
            'msg_cate_delete'  => __( 'Are you totally sure that you wan to delete this category?', 'wp-ticket-ultra' ),
			'msg_department_delete'  => __( 'Are you totally sure that you wan to delete this department?', 'wp-ticket-ultra' ),
			
			'msg_trash_ticket'  => __( 'Are you totally sure that you wan to send this ticket to the trash?', 'wp-ticket-ultra' ),
			
			'are_you_sure'  => __( 'Are you totally sure?', 'wp-ticket-ultra' ),
			'set_new_priority'  => __( 'Set a new priority', 'wp-ticket-ultra' ),
			
			
			
			'msg_department_edit'  => __( 'Edit Department', 'wp-ticket-ultra' ),
			'msg_department_add'  => __( 'Add Department', 'wp-ticket-ultra' ),
			'msg_department_input_title'  => __( 'Please input a name', 'wp-ticket-ultra' ),
			
			'msg_priority_edit'  => __( 'Edit Priority', 'wp-ticket-ultra' ),
			'msg_priority_add'  => __( 'Add Priority', 'wp-ticket-ultra' ),
			'msg_priority_input_title'  => __( 'Please input a name', 'wp-ticket-ultra' ),
			
			'msg_ticket_empty_reply'  => '<div class="wptu-ultra-error"><span><i class="fa fa-ok"></i>'.__('ERROR!. Please write a message ',"wp-ticket-ultra").'</span></div>' ,
			
			'msg_ticket_submiting_reply'  => '<div class="wptu-ultra-wait"><span><i class="fa fa-ok"></i>'.__(' <img src="'.wptu_url.'/templates/images/loaderB16.gif" width="16" height="16" /> &nbsp; Please wait ... ',"wp-ticket-ultra").'</span></div>' ,
			'msg_wait'  => __( '<img src="'.wptu_url.'/templates/images/loaderB16.gif" width="16" height="16" /> &nbsp; Please wait ... ', 'wp-ticket-ultra' ) ,
			
			'msg_site_edit'  => __( 'Edit Product', 'wp-ticket-ultra' ),
			'msg_site_add'  => __( 'Add Product', 'wp-ticket-ultra' ),
			
			'msg_note_edit'  => __( 'Edit Note', 'wp-ticket-ultra' ),
			
			'msg_category_edit'  => __( 'Edit Category', 'wp-ticket-ultra' ),
			'msg_category_add'  => __( 'Add Category', 'wp-ticket-ultra' ),
			
			'msg_category_input_title'  => __( 'Please input a title', 'wp-ticket-ultra' ),
			'msg_category_delete'  => __( 'Are you totally sure that you wan to delete this service?', 'wp-ticket-ultra' ),
			'msg_user_delete'  => __( 'Are you totally sure that you wan to delete this user?', 'wp-ticket-ultra' ),
			
			'msg_status_change'  => __( 'Please set a new status', 'wp-ticket-ultra' ),
			'msg_priority_change'  => __( 'Please set a new priority', 'wp-ticket-ultra' ),
			
			'message_wait_staff_box'     => __("Please wait ...","wp-ticket-ultra"),
			'msg_input_site_name'  => __( 'Please input a name', 'wp-ticket-ultra' ),
			
			'msg_input_note_name'  => __( 'Please input a name', 'wp-ticket-ultra' ),

           
            
        ) );
				
		
	}
	
	public  function convertFormat( $source_format, $to )
    {
		global $bookingultrapro ;
		
        switch ( $source_format ) 
		{
            case 'date':
                $php_format = get_option( 'date_format', 'Y-m-d' );
                break;
            case 'time':
                $php_format = get_option( 'time_format', 'H:i' );
                break;
            default:
                $php_format = $source_format;
        }
		
		 switch ( $to ) {
            case 'fc' :
			
                $replacements = array(
                    'd' => 'DD',   '\d' => '[d]',
                    'D' => 'ddd',  '\D' => '[D]',
                    'j' => 'D',    '\j' => 'j',
                    'l' => 'dddd', '\l' => 'l',
                    'N' => 'E',    '\N' => 'N',
                    'S' => 'o',    '\S' => '[S]',
                    'w' => 'e',    '\w' => '[w]',
                    'z' => 'DDD',  '\z' => '[z]',
                    'W' => 'W',    '\W' => '[W]',
                    'F' => 'MMMM', '\F' => 'F',
                    'm' => 'MM',   '\m' => '[m]',
                    'M' => 'MMM',  '\M' => '[M]',
                    'n' => 'M',    '\n' => 'n',
                    't' => '',     '\t' => 't',
                    'L' => '',     '\L' => 'L',
                    'o' => 'YYYY', '\o' => 'o',
                    'Y' => 'YYYY', '\Y' => 'Y',
                    'y' => 'YY',   '\y' => 'y',
                    'a' => 'a',    '\a' => '[a]',
                    'A' => 'A',    '\A' => '[A]',
                    'B' => '',     '\B' => 'B',
                    'g' => 'h',    '\g' => 'g',
                    'G' => 'H',    '\G' => 'G',
                    'h' => 'hh',   '\h' => '[h]',
                    'H' => 'HH',   '\H' => '[H]',
                    'i' => 'mm',   '\i' => 'i',
                    's' => 'ss',   '\s' => '[s]',
                    'u' => 'SSS',  '\u' => 'u',
                    'e' => 'zz',   '\e' => '[e]',
                    'I' => '',     '\I' => 'I',
                    'O' => '',     '\O' => 'O',
                    'P' => '',     '\P' => 'P',
                    'T' => '',     '\T' => 'T',
                    'Z' => '',     '\Z' => '[Z]',
                    'c' => '',     '\c' => 'c',
                    'r' => '',     '\r' => 'r',
                    'U' => 'X',    '\U' => 'U',
                    '\\' => '',
                );
                return strtr( $php_format, $replacements );
			}
	}
	
	function add_menu() 
	{
		global $wptucomplement ;
		
		//$pending_count = $bookingultrapro->appointment->get_appointments_total_by_status(0);;
		
		
	
		$pending_title = esc_attr( sprintf(__( '%d new manual activation requests','wp-ticket-ultra'), $pending_count ) );
		if ($pending_count > 0)
		{
			$menu_label = sprintf( __( 'WP Ticket Ultra %s','wp-ticket-ultra' ), "<span class='update-plugins count-$pending_count' title='$pending_title'><span class='update-count'>" . number_format_i18n($pending_count) . "</span></span>" );
			
		} else {
			
			$menu_label = __('WP Ticket Ultra','wp-ticket-ultra');
		}
		
		add_menu_page( __('WP Ticket Ultra','wp-ticket-ultra'), $menu_label, 'manage_options', $this->slug, array(&$this, 'admin_page'), wptu_url .'admin/images/small_logo_16x16.png', '159.140');
		
		//
		
		
		if(!isset($wptucomplement))
		{
		
			add_submenu_page( $this->slug, __('More Functionality!','wp-ticket-ultra'), __('More Functionality!','wp-ticket-ultra'), 'manage_options', 'wpticketultra&tab=pro', array(&$this, 'admin_page') );
		
		}
		
		if(isset($wptucomplement))
		{
			add_submenu_page( $this->slug, __('Licensing','wp-ticket-ultra'), __('Licensing','wp-ticket-ultra'), 'manage_options', 'wpticketultra&tab=licence', array(&$this, 'admin_page') );
		
		
		}
		
		do_action('wptu_admin_menu_hook');
		
			
	}
	
	

	function admin_tabs( $current = null ) {
		
		global $wptucomplement, $wptu_custom_fields;
		
			$tabs = $this->tabs;
			$links = array();
			if ( isset ( $_GET['tab'] ) ) {
				$current = $_GET['tab'];
			} else {
				$current = $this->default_tab;
			}
			foreach( $tabs as $tab => $name ) :
			
			
			    if($tab=="pro"){
					
					$custom_badge = 'wptu-pro-tab-bubble ';
					
				}
				
				if($tab=="fields" && !isset($wptu_custom_fields)){continue;}
				
				if(isset($wptucomplement) && $tab=="pro"){continue;}
				
				
				if ( $tab == $current ) :
					$links[] = "<a class='nav-tab nav-tab-active ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'><span class='wptu-adm-tab-legend'>".$name."</span></a>";
				else :
					$links[] = "<a class='nav-tab ".$custom_badge."' href='?page=".$this->slug."&tab=$tab'><span class='wptu-adm-tab-legend'>".$name."</span></a>";
				endif;
			endforeach;
			foreach ( $links as $link )
				echo $link;
	}

	
	
	function do_action(){
		global $userultra;
				
		
	}
		
	
	/* set a global option */
	function wptu_set_option($option, $newvalue)
	{
		$settings = get_option('wptu_options');		
		$settings[$option] = $newvalue;
		update_option('wptu_options', $settings);
	}
	
	/* default options */
	function bup_set_default_options()
	{
	
		$this->wptu_default_options = array(									
						
						'messaging_send_from_name' => __('WP Ticket Ultra Plugin','wp-ticket-ultra'),
						
						'bup_noti_admin' => 'yes',
						'bup_noti_staff' => 'yes',
						'bup_noti_client' => 'yes',
						'messaging_send_from_email' => get_option( 'admin_email' ),
						'company_name' => get_option('blogname'),	
						
						'allowed_extensions' => 'jpg,png,gif,jpeg,pdf,doc,docx,xls',	
						
						'advanced_noti_on_weekend_backend_message'  => __( "Finally, it's the weekend!. We Work From Monday To Friday From 8AM To 5PM. Since we're on weekend a reply should be received on either Monday or Tuesday. Thank you very much for your understanding.", 'wp-ticket-ultra' ),
			
						'advanced_noti_on_weekend_front_message'  => __( "We Work From Monday To Friday From 8AM To 5PM. Since we're on weekend a reply should be received on either Monday or Tuesday. Thank you very much for your understanding.", 'wp-ticket-ultra' ),	
											
									
						'email_new_ticket_admin' => $this->get_email_template('email_new_ticket_admin'),
						'email_new_ticket_subject_admin' => __('New ticket','wp-ticket-ultra'),
						
						'email_new_ticket_staff' => $this->get_email_template('email_new_ticket_staff'),
						'email_new_ticket_subject_staff' => __('New ticket','wp-ticket-ultra'),
						
						'email_new_reply_body_staff' => $this->get_email_template('email_new_reply_body_staff'),
						'email_new_reply_subject_staff' => __('New reply','wp-ticket-ultra'),
						
						'email_new_reply_body_admin' => $this->get_email_template('email_new_reply_body_admin'),
						'email_new_reply_subject_admin' => __('New reply','wp-ticket-ultra'),
																		
						'email_new_ticket_client' => $this->get_email_template('email_new_ticket_client'),
						'email_new_ticket_subject_client' => __('Customer Support','wp-ticket-ultra'),
						
						'email_new_reply_client' => $this->get_email_template('email_new_reply_client'),
						'email_new_reply_subject_client' => __('User Reply','wp-ticket-ultra'),
						
						'email_password_change_staff' => $this->get_email_template('email_password_change_staff'),
						'email_password_change_staff_subject' => __('Password Changed','wp-ticket-ultra'),
						
						'email_reset_link_message_body' => $this->get_email_template('email_reset_link_message_body'),
						'email_reset_link_message_subject' => __('Password Reset','wp-ticket-ultra'),
						
						'email_welcome_staff_link_message_body' => $this->get_email_template('email_welcome_staff_link_message_body'),
						'email_welcome_staff_link_message_subject' => __('Your Account Details','wp-ticket-ultra'),
						
						'email_registration_body' => $this->get_email_template('email_registration_body'),
						'email_registration_subject' => __('Your Account Details','wp-ticket-ultra'),
						
						'email_owner_change_message_body' => $this->get_email_template('email_owner_change_message_body'),
						'email_owner_change_message_subject' => __('New Ticket Assigned','wp-ticket-ultra'),
						
						'email_ticket_status_change_message_body' => $this->get_email_template('email_ticket_status_change_message_body'),
						'email_ticket_status_change_message_subject' => __('Ticket Status Changed','wp-ticket-ultra'),
						
						
						'bugs_assigned_reply_client' => $this->get_email_template('bugs_assigned_reply_client'),
						'bugs_assigned_subject_client' => __('Bug Assigned','wp-ticket-ultra'),
						
											
						
				);
		
	}
	
	public function set_default_email_messages()
	{
		$line_break = "\r\n";	
						
		//notify admin 		
		$email_body = __("A new support ticket has been submitted.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .= "<strong>".__("Ticket Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Date: {{wptu_date}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Department: {{wptu_department}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Priority: {{wptu_priority}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Subject: {{wptu_subject}}','wp-ticket-ultra') . $line_break;
		$email_body .= '---------------------------------------------' . $line_break;
		$email_body .= __('{{wptu_message}}','wp-ticket-ultra') . $line_break.$line_break;
		
		
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break;
	    $this->notifications_email['email_new_ticket_admin'] = $email_body;
		
			
		//notify staff
		$email_body = __("A new support ticket has been submitted.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .= "<strong>".__("Ticket Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Date: {{wptu_date}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Department: {{wptu_department}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Priority: {{wptu_priority}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Subject: {{wptu_subject}}','wp-ticket-ultra') . $line_break;
		$email_body .= '---------------------------------------------' . $line_break;
		$email_body .= __('{{wptu_message}}','wp-ticket-ultra') . $line_break.$line_break;
		
		
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break;
	    $this->notifications_email['email_new_ticket_staff'] = $email_body;
		
		//notify new reply on ticket staff
		$email_body = __("A new reply has been added to the following ticket.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .= "<strong>".__("Ticket Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Priority: {{wptu_priority}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Subject: {{wptu_subject}}','wp-ticket-ultra') . $line_break.$line_break;
		
		$email_body .= '{{wptu_ticket_text}}' . $line_break.$line_break;
				
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break;
	    $this->notifications_email['email_new_reply_body_staff'] = $email_body;
		
		//notify new reply on ticket admin
		$email_body = __("Admin, A new reply has been added to the following ticket.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .= "<strong>".__("Ticket Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Priority: {{wptu_priority}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Subject: {{wptu_subject}}','wp-ticket-ultra') . $line_break.$line_break;
		
		$email_body .= '{{wptu_ticket_text}}' . $line_break.$line_break;
				
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break;
	    $this->notifications_email['email_new_reply_body_admin'] = $email_body;
		
		//notify client 		
		$email_body =  '{{wptu_client_name}},'.$line_break.$line_break;
		$email_body .= __("Thanks for getting in touch. We're looking into your request.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .= __("Generally all inquiries are answered in the order they are created. Occasionally, due to some requests requiring more research than others and also due to excessive demand, a reply may take longer than one business day. Please accept our apologies in advance for any reply that exceeds this time frame, but be assured we are working hard to get back to you as quickly as possible to provide a considerate response","wp-ticket-ultra") .  $line_break.$line_break;		
		
		$email_body .= __("Thank you for your patience!.","wp-ticket-ultra") .  $line_break.$line_break;		
		$email_body .= __("To review the status of the request and add comments you have to login to your account.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .= __("Please use the link below to login to your account.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .='{{wptu_client_login_url}}'.  $line_break.$line_break;	
		
		$email_body .= "<strong>".__("Request Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Date: {{wptu_date}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Department: {{wptu_department}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Priority: {{wptu_priority}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Subject: {{wptu_subject}}','wp-ticket-ultra') . $line_break;
		$email_body .= '---------------------------------------------' . $line_break;
		$email_body .= __('{{wptu_message}}','wp-ticket-ultra') . $line_break. $line_break;
		
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
			
	    $this->notifications_email['email_new_ticket_client'] = $email_body;
		
		//notify client 		
		$email_body =  '{{wptu_client_name}},'.$line_break.$line_break;
		$email_body .= __("A new reply has been added to your ticket.","wp-ticket-ultra") .  $line_break.$line_break;
		
		
		$email_body .= __("To review the status of the request and add comments you have to login to your account.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .= __("Please use the link below to login to your account.","wp-ticket-ultra") .  $line_break.$line_break;
		
		$email_body .='{{wptu_client_login_url}}'.  $line_break.$line_break;	
		
		$email_body .= "<strong>".__("Request Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= '---------------------------------------------' . $line_break.$line_break;
		$email_body .= __('{{wptu_message}}','wp-ticket-ultra') . $line_break. $line_break;
		
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
			
	    $this->notifications_email['email_new_reply_client'] = $email_body;
		
		
		//Staff Password Change	
		$email_body =  '{{wptu_staff_name}},'.$line_break.$line_break;
		$email_body .= __("This is a notification that your password has been changed. ","wp-ticket-ultra") .  $line_break.$line_break;
				
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_password_change_staff'] = $email_body;
		
		//Staff Password Reset	
		$email_body =  '{{wptu_staff_name}},'.$line_break.$line_break;
		$email_body .= __("Please use the following link to reset your password.","wp-ticket-ultra") . $line_break.$line_break;			
		$email_body .= "{{wptu_reset_link}}".$line_break.$line_break;
		$email_body .= __('If you did not request a new password delete this email.','wp-ticket-ultra'). $line_break.$line_break;	
			
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_reset_link_message_body'] = $email_body;
		
		//Staff Welcome Account Reset Link	
		$email_body =  '{{wptu_staff_name}},'.$line_break.$line_break;
		$email_body .= __("Your login details for your account are as follows:","wp-ticket-ultra") . $line_break.$line_break;
		$email_body .= __('Username: {{wptu_user_name}}','wp-ticket-ultra') . $line_break;
		$email_body .= __("Please use the following link to reset your password.","wp-ticket-ultra") . $line_break.$line_break;			
		$email_body .= "{{wptu_reset_link}}".$line_break.$line_break;
			
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_welcome_staff_link_message_body'] = $email_body;
		
		//User Registration Email
		$email_body =  __('Hello ','wp-ticket-ultra') .'{{wptu_client_name}},'.$line_break.$line_break;
		$email_body .= __("Thank you for your registration. Your login details for your account are as follows:","wp-ticket-ultra") . $line_break.$line_break;
		$email_body .= __('Username: {{wptu_user_name}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Password: {{wptu_user_password}}','wp-ticket-ultra') . $line_break;
		$email_body .= __("Please use the following link to login to your account.","wp-ticket-ultra") . $line_break.$line_break;			
		$email_body .= "{{wptu_login_link}}".$line_break.$line_break;
			
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_registration_body'] = $email_body;	
		
		//Staff New Ticket Assigned	
		$email_body =  '{{wptu_staff_name}},'.$line_break.$line_break;
		$email_body .= __("A new ticket has been assigned to you:","wp-ticket-ultra") . $line_break.$line_break;
		
		$email_body .= __("Please login to your account to check the ticket's details.","wp-ticket-ultra") . $line_break.$line_break;			
		$email_body .= "{{wptu_client_login_url}}".$line_break.$line_break;
		
		$email_body .= "<strong>".__("Ticket Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Date: {{wptu_date}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Department: {{wptu_department}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Priority: {{wptu_priority}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Subject: {{wptu_subject}}','wp-ticket-ultra') . $line_break;
		$email_body .= '---------------------------------------------' . $line_break;
		$email_body .= __('{{wptu_message}}','wp-ticket-ultra') . $line_break.$line_break;
			
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_owner_change_message_body'] = $email_body;
		
		//Ticket Status Change Client	
		$email_body =  '{{wptu_client_name}},'.$line_break.$line_break;
		$email_body .= __("This email is to notify you that the status of the ticket #{{wptu_ticket_number}} has changed.","wp-ticket-ultra") . $line_break.$line_break;
		
		$email_body .= __("Please login to your account to check the ticket's details.","wp-ticket-ultra") . $line_break.$line_break;			
		$email_body .= "{{wptu_client_login_url}}".$line_break.$line_break;
		
		$email_body .= "<strong>".__("Ticket Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Ticket Number: <strong>{{wptu_ticket_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('New Status: {{wptu_ticket_status}}','wp-ticket-ultra') . $line_break.$line_break;	
					
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['email_ticket_status_change_message_body'] = $email_body;	
		
		
		//BUGS and ISSUES TEMPLATES
		
		//Staff New Bug Assigned	
		$email_body =  '{{wptu_staff_name}},'.$line_break.$line_break;
		$email_body .= __("A new bug or issue has been assigned to you:","wp-ticket-ultra") . $line_break.$line_break;
		
		$email_body .= __("Please login to your account to check the details details.","wp-ticket-ultra") . $line_break.$line_break;			
		$email_body .= "{{wptu_client_login_url}}".$line_break.$line_break;
		
		$email_body .= "<strong>".__("Details:","wp-ticket-ultra")."</strong>".  $line_break.$line_break;
		$email_body .= __('Product: <strong>{{wptu_website_name}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Issue Number: <strong>{{wptu_bug_number}} </strong>','wp-ticket-ultra') . $line_break;
		$email_body .= __('Assignment Date: {{wptu_date}}','wp-ticket-ultra') . $line_break;	
		$email_body .= __('Status: {{wptu_bug_status}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Priority: {{wptu_bug_priority}}','wp-ticket-ultra') . $line_break;
		$email_body .= __('Subject: {{wptu_bug_subject}}','wp-ticket-ultra') . $line_break;
		$email_body .= '---------------------------------------------' . $line_break;
			
		$email_body .= __('Best Regards!','wp-ticket-ultra'). $line_break;
		$email_body .= '{{wptu_company_name}}'. $line_break;
		$email_body .= '{{wptu_company_phone}}'. $line_break;
		$email_body .= '{{wptu_company_url}}'. $line_break. $line_break;
		
	    $this->notifications_email['bugs_assigned_reply_client'] = $email_body;	
		
		
		
		
		
	
	}
	
	public function get_email_template($key)
	{
		return $this->notifications_email[$key];
	
	}
	
	public function set_font_awesome()
	{
		        /* Store icons in array */
        $this->fontawesome = array(
                'cloud-download','cloud-upload','lightbulb','exchange','bell-alt','file-alt','beer','coffee','food','fighter-jet',
                'user-md','stethoscope','suitcase','building','hospital','ambulance','medkit','h-sign','plus-sign-alt','spinner',
                'angle-left','angle-right','angle-up','angle-down','double-angle-left','double-angle-right','double-angle-up','double-angle-down','circle-blank','circle',
                'desktop','laptop','tablet','mobile-phone','quote-left','quote-right','reply','github-alt','folder-close-alt','folder-open-alt',
                'adjust','asterisk','ban-circle','bar-chart','barcode','beaker','beer','bell','bolt','book','bookmark','bookmark-empty','briefcase','bullhorn',
                'calendar','camera','camera-retro','certificate','check','check-empty','cloud','cog','cogs','comment','comment-alt','comments','comments-alt',
                'credit-card','dashboard','download','download-alt','edit','envelope','envelope-alt','exclamation-sign','external-link','eye-close','eye-open',
                'facetime-video','film','filter','fire','flag','folder-close','folder-open','gift','glass','globe','group','hdd','headphones','heart','heart-empty',
                'home','inbox','info-sign','key','leaf','legal','lemon','lock','unlock','magic','magnet','map-marker','minus','minus-sign','money','move','music',
                'off','ok','ok-circle','ok-sign','pencil','picture','plane','plus','plus-sign','print','pushpin','qrcode','question-sign','random','refresh','remove',
                'remove-circle','remove-sign','reorder','resize-horizontal','resize-vertical','retweet','road','rss','screenshot','search','share','share-alt',
                'shopping-cart','signal','signin','signout','sitemap','sort','sort-down','sort-up','spinner','star','star-empty','star-half','tag','tags','tasks',
                'thumbs-down','thumbs-up','time','tint','trash','trophy','truck','umbrella','upload','upload-alt','user','volume-off','volume-down','volume-up',
                'warning-sign','wrench','zoom-in','zoom-out','file','cut','copy','paste','save','undo','repeat','text-height','text-width','align-left','align-right',
                'align-center','align-justify','indent-left','indent-right','font','bold','italic','strikethrough','underline','link','paper-clip','columns',
                'table','th-large','th','th-list','list','list-ol','list-ul','list-alt','arrow-down','arrow-left','arrow-right','arrow-up','caret-down',
                'caret-left','caret-right','caret-up','chevron-down','chevron-left','chevron-right','chevron-up','circle-arrow-down','circle-arrow-left',
                'circle-arrow-right','circle-arrow-up','hand-down','hand-left','hand-right','hand-up','play-circle','play','pause','stop','step-backward',
                'fast-backward','backward','forward','step-forward','fast-forward','eject','fullscreen','resize-full','resize-small','phone','phone-sign',
                'facebook','facebook-sign','twitter','twitter-sign','github','github-sign','linkedin','linkedin-sign','pinterest','pinterest-sign',
                'google-plus','google-plus-sign','sign-blank'
        );
        asort($this->fontawesome);
		
	
	
	}
	
		
	
	/*This Function Change the Profile Fields Order when drag/drop */	
	public function sort_fileds_list() 
	{
		global $wpdb;
	
		$order = explode(',', $_POST['order']);
		$counter = 0;
		$new_pos = 10;
		
		//multi fields		
		$custom_form = $_POST["wptu_custom_form"];
		
		$custom_form = 'wptu_profile_fields_'.$custom_form;		
		$fields = get_option($custom_form);			
		$fields_set_to_update =$custom_form;
		
		$new_fields = array();
		
		$fields_temp = $fields;
		ksort($fields);
		
		foreach ($fields as $field) 
		{
			
			$fields_temp[$order[$counter]]["position"] = $new_pos;			
			$new_fields[$new_pos] = $fields_temp[$order[$counter]];				
			$counter++;
			$new_pos=$new_pos+10;
		}
		
		ksort($new_fields);		
		
		
		update_option($fields_set_to_update, $new_fields);		
		die(1);
		
    }
	/*  delete profile field */
    public function delete_profile_field() 
	{						
		
		if($_POST['_item']!= "")
		{
			
			//multi fields		
			$custom_form = sanitize_text_field($_POST["custom_form"]);
			
			if($custom_form!="")
			{
				$custom_form = 'wptu_profile_fields_'.$custom_form;		
				$fields = get_option($custom_form);			
				$fields_set_to_update =$custom_form;
				
			}else{
				
				$fields = get_option('wptu_profile_fields');
				$fields_set_to_update ='wptu_profile_fields';
			
			}
			
			$pos = $_POST['_item'];
			
			unset($fields[$pos]);
			
			ksort($fields);
			print_r($fields);
			update_option($fields_set_to_update, $fields);
			
		
		}
	
	}
	
	
	 /* create new custom profile field */
    public function add_new_custom_profile_field() 
	{				
		
		
		if($_POST['_meta']!= "")
		{
			$meta = sanitize_text_field($_POST['_meta']);
		
		}else{
			
			$meta = sanitize_text_field($_POST['_meta_custom']);
		}
		
		//if custom fields
		
		
		//multi fields		
		$custom_form = sanitize_text_field( $_POST["custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'wptu_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('wptu_profile_fields');
			$fields_set_to_update ='wptu_profile_fields';
		
		}
		
		$min = min(array_keys($fields)); 
		
		$pos = $min-1;
		
		$fields[$pos] =array(
			    'position' => $pos,
				'icon' => sanitize_text_field($_POST['_icon']),
				'type' => sanitize_text_field($_POST['_type']),
				'field' => sanitize_text_field($_POST['_field']),
				'meta' => sanitize_text_field($meta),
				'name' => sanitize_text_field($_POST['_name']),				
				'tooltip' => sanitize_text_field($_POST['_tooltip']),
				'help_text' => sanitize_text_field($_POST['_help_text']),							
				'can_edit' => sanitize_text_field($_POST['_can_edit']),
				'allow_html' => sanitize_text_field($_POST['_allow_html']),
				'can_hide' => sanitize_text_field($_POST['_can_hide']),				
				'private' => sanitize_text_field($_POST['_private']),
				'required' => sanitize_text_field($_POST['_required']),
				'show_in_register' => sanitize_text_field($_POST['_show_in_register']),
				'predefined_options' => sanitize_text_field($_POST['_predefined_options']),				
				'choices' => sanitize_text_field($_POST['_choices']),												
				'deleted' => 0
				

			);			
					
			ksort($fields);
			print_r($fields);			
		   update_option($fields_set_to_update, $fields);         


    }
	


    // save form
    public function save_fields_settings() 
	{		
		
		$pos = sanitize_text_field($_POST['pos']); 
		
		if($_POST['_meta']!= "")
		{
			$meta = sanitize_text_field($_POST['_meta']);
		
		}else{
			
			$meta = sanitize_text_field($_POST['_meta_custom']);
		}
		
		//if custom fields
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'wptu_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('wptu_profile_fields');
			$fields_set_to_update ='wptu_profile_fields';
		
		}
		
		$fields[$pos] =array(
			  'position' => $pos,
				'icon' => sanitize_text_field($_POST['_icon']),
				'type' => sanitize_text_field($_POST['_type']),
				'field' => sanitize_text_field($_POST['_field']),
				'meta' => sanitize_text_field($meta),
				'name' => sanitize_text_field($_POST['_name']),
				'ccap' => sanitize_text_field($_POST['_ccap']),
				'tooltip' => sanitize_text_field($_POST['_tooltip']),
				'help_text' => sanitize_text_field($_POST['_help_text']),
				'social' =>  sanitize_text_field($_POST['_social']),
				'is_a_link' =>  sanitize_text_field($_POST['_is_a_link']),
				'can_edit' => sanitize_text_field($_POST['_can_edit']),
				'allow_html' => sanitize_text_field($_POST['_allow_html']),				
				'required' => sanitize_text_field($_POST['_required']),
				'show_in_register' => sanitize_text_field($_POST['_show_in_register']),
				
				'predefined_options' => sanitize_text_field($_POST['_predefined_options']),				
				'choices' => sanitize_text_field($_POST['_choices']),												
				'deleted' => 0,
				'show_to_user_role' => sanitize_text_field($_POST['_show_to_user_role']),
                'edit_by_user_role' => sanitize_text_field($_POST['_edit_by_user_role'])
			);
			
			
						
			print_r($fields);
			
		    update_option($fields_set_to_update , $fields);
		
         


    }
	
		
	/*This load a custom field to be edited Implemented on 08-08-2014*/
	function reload_field_to_edit()	
	{
		global $wpticketultra;
		
		//get field
		$pos = sanitize_text_field($_POST["pos"]);
		
		
		//multi fields		
		$custom_form = sanitize_text_field($_POST["custom_form"]);
		
		if($custom_form!="")
		{
			$custom_form = 'wptu_profile_fields_'.$custom_form;		
			$fields = get_option($custom_form);			
			$fields_set_to_update =$custom_form;
			
		}else{
			
			$fields = get_option('wptu_profile_fields');
			$fields_set_to_update ='wptu_profile_fields';
		
		}
		
		$array = $fields[$pos];
		
		
		extract($array); $i++;

		if(!isset($required))
		       $required = 0;

		    if(!isset($fonticon))
		        $fonticon = '';				
				
			if ($type == 'seperator' || $type == 'separator') {
			   
				$class = "separator";
				$class_title = "";
			} else {
			  
				$class = "profile-field";
				$class_title = "profile-field";
			}
		
		
		?>
		
		

				<p>
					<label for="uultra_<?php echo $pos; ?>_position"><?php _e('Position','wp-ticket-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_position"
						type="text" id="uultra_<?php echo $pos; ?>_position"
						value="<?php echo $pos; ?>" class="small-text" /> <i
						class="uultra_icon-question-sign uultra-tooltip2"
						title="<?php _e('Please use a unique position. Position lets you place the new field in the place you want exactly in Profile view.','wp-ticket-ultra'); ?>"></i>
				</p>

				<p>
					<label for="uultra_<?php echo $pos; ?>_type"><?php _e('Field Type','wp-ticket-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_type"
						id="uultra_<?php echo $pos; ?>_type">
						<option value="usermeta" <?php selected('usermeta', $type); ?>>
							<?php _e('Profile Field','wp-ticket-ultra'); ?>
						</option>
						<option value="separator" <?php selected('separator', $type); ?>>
							<?php _e('Separator','wp-ticket-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('You can create a separator or a usermeta (profile field)','wp-ticket-ultra'); ?>"></i>
				</p> 
				
				<?php if ($type != 'separator') { ?>

				<p class="uultra-inputtype">
					<label for="uultra_<?php echo $pos; ?>_field"><?php _e('Field Input','wp-ticket-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_field"
						id="uultra_<?php echo $pos; ?>_field">
						<?php
						
						 foreach($wpticketultra->allowed_inputs as $input=>$label) { ?>
						<option value="<?php echo $input; ?>"
						<?php selected($input, $field); ?>>
							<?php echo $label; ?>
						</option>
						<?php } ?>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','wp-ticket-ultra'); ?>"></i>
				</p>

				
				<p>
					<label for="uultra_<?php echo $pos; ?>_meta_custom"><?php _e('Custom Meta Field','wp-ticket-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>C"
						type="text" id="uultra_<?php echo $pos; ?>_meta_custom"
						value="<?php if (!isset($all_meta_for_user[$meta])) echo $meta; ?>" />
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','wp-ticket-ultra'); ?>"></i>
				</p> <?php } ?>

				
                
                
                <p>
					<label for="uultra_<?php echo $pos; ?>_name"><?php _e('Label / Name','wp-ticket-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_name" type="text"
						id="uultra_<?php echo $pos; ?>_name" value="<?php echo $name; ?>" />
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','wp-ticket-ultra'); ?>"></i>
				</p>
                
                

			<?php if ($type != 'separator' ) { ?>

				
				<p>
					<label for="uultra_<?php echo $pos; ?>_tooltip"><?php _e('Tooltip Text','wp-ticket-ultra'); ?>
					</label> <input name="uultra_<?php echo $pos; ?>_tooltip" type="text"
						id="uultra_<?php echo $pos; ?>_tooltip"
						value="<?php echo $tooltip; ?>" /> <i
						class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('A tooltip text can be useful for social buttons on profile header.','wp-ticket-ultra'); ?>"></i>
				</p> 
                
               <p>
               
               <label for="uultra_<?php echo $pos; ?>_help_text"><?php _e('Help Text','wp-ticket-ultra'); ?>
                </label><br />
                    <textarea class="uultra-help-text" id="uultra_<?php echo $pos; ?>_help_text" name="uultra_<?php echo $pos; ?>_help_text" title="<?php _e('A help text can be useful for provide information about the field.','wp-ticket-ultra'); ?>" ><?php echo $help_text; ?></textarea>
                    <i class="uultra-icon-question-sign uultra-tooltip2"
                                title="<?php _e('Show this help text under the profile field.','wp-ticket-ultra'); ?>"></i>
                              
               </p> 
				
				
				
                
               				
				<?php 
				if(!isset($can_edit))
				    $can_edit = '1';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_can_edit"><?php _e('User can edit','wp-ticket-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_can_edit"
						id="uultra_<?php echo $pos; ?>_can_edit">
						<option value="1" <?php selected(1, $can_edit); ?>>
							<?php _e('Yes','wp-ticket-ultra'); ?>
						</option>
						<option value="0" <?php selected(0, $can_edit); ?>>
							<?php _e('No','wp-ticket-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Users can edit this profile field or not.','wp-ticket-ultra'); ?>"></i>
				</p> 
				
				<?php if (!isset($array['allow_html'])) { 
				    $allow_html = 0;
				} ?>
								
				
				
				<?php 
				if(!isset($required))
				    $required = '0';
				?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_required"><?php _e('This field is Required','wp-ticket-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_required"
						id="uultra_<?php echo $pos; ?>_required">
						<option value="0" <?php selected(0, $required); ?>>
							<?php _e('No','wp-ticket-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $required); ?>>
							<?php _e('Yes','wp-ticket-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Selecting yes will force user to provide a value for this field at registration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','wp-ticket-ultra'); ?>"></i>
				</p> <?php } ?> <?php

				/* Show Registration field only when below condition fullfill
				1) Field is not private
				2) meta is not for email field
				3) field is not fileupload */
				if(!isset($private))
				    $private = 0;

				if(!isset($meta))
				    $meta = '';

				if(!isset($field))
				    $field = '';


				//if($type == 'separator' ||  ($private != 1 && $meta != 'user_email' ))
				if($type == 'separator' ||  ($private != 1 && $meta != 'user_email' ))
				{
				    if(!isset($show_in_register))
				        $show_in_register= 0;
						
					 if(!isset($show_in_widget))
				        $show_in_widget= 0;
				    ?>
				<p>
					<label for="uultra_<?php echo $pos; ?>_show_in_register"><?php _e('Show on Registration Form','wp-ticket-ultra'); ?>
					</label> <select name="uultra_<?php echo $pos; ?>_show_in_register"
						id="uultra_<?php echo $pos; ?>_show_in_register">
						<option value="0" <?php selected(0, $show_in_register); ?>>
							<?php _e('No','wp-ticket-ultra'); ?>
						</option>
						<option value="1" <?php selected(1, $show_in_register); ?>>
							<?php _e('Yes','wp-ticket-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Show this profile field on the registration form','wp-ticket-ultra'); ?>"></i>
				</p>    
               
                
                 <?php } ?>
                 
			<?php if ($type != 'seperator' || $type != 'separator') { ?>

		  <?php if (in_array($field, array('select','radio','checkbox')))
				 {
				    $show_choices = null;
				} else { $show_choices = 'uultra-hide';
				
				
				} ?>

				<p class="uultra-choices <?php echo $show_choices; ?>">
					<label for="uultra_<?php echo $pos; ?>_choices"
						style="display: block"><?php _e('Available Choices','wp-ticket-ultra'); ?> </label>
					<textarea name="uultra_<?php echo $pos; ?>_choices" type="text" id="uultra_<?php echo $pos; ?>_choices" class="large-text"><?php if (isset($array['choices'])) echo trim($choices); ?></textarea>
                    
                    <?php
                    
					if($wpticketultra->uultra_if_windows_server())
					{
						echo ' <p>'.__('<strong>PLEASE NOTE: </strong>Enter values separated by commas, example: 1,2,3. The choices will be available for front end user to choose from.').' </p>';					
					}else{
						
						echo ' <p>'.__('<strong>PLEASE NOTE:</strong> Enter one choice per line please. The choices will be available for front end user to choose from.').' </p>';
					
					
					}
					
					?>
                    <p>
                    
                    
                    </p>
					<i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('Enter one choice per line please. The choices will be available for front end user to choose from.','wp-ticket-ultra'); ?>"></i>
				</p> <?php //if (!isset($array['predefined_loop'])) $predefined_loop = 0;
				
				if (!isset($predefined_options)) $predefined_options = 0;
				
				 ?>

				<p class="uultra_choices <?php echo $show_choices; ?>">
					<label for="uultra_<?php echo $pos; ?>_predefined_options" style="display: block"><?php _e('Enable Predefined Choices','wp-ticket-ultra'); ?>
					</label> 
                    <select name="uultra_<?php echo $pos; ?>_predefined_options"id="uultra_<?php echo $pos; ?>_predefined_options">
						<option value="0" <?php selected(0, $predefined_options); ?>>
							<?php _e('None','wp-ticket-ultra'); ?>
						</option>
						<option value="countries" <?php selected('countries', $predefined_options); ?>>
							<?php _e('List of Countries','wp-ticket-ultra'); ?>
						</option>
                        
                        <option value="age" <?php selected('age', $predefined_options); ?>>
							<?php _e('Age','wp-ticket-ultra'); ?>
						</option>
					</select> <i class="uultra-icon-question-sign uultra-tooltip2"
						title="<?php _e('You can enable a predefined filter for choices. e.g. List of countries It enables country selection in profiles and saves you time to do it on your own.','wp-ticket-ultra'); ?>"></i>
				</p>

				
				<div class="clear"></div> 
				
				<?php } ?>


  <div class="wptu-ultra-success wptu-notification" id="bup-sucess-fields-<?php echo $pos; ?>"><?php _e('Success ','wp-ticket-ultra'); ?></div>
				<p>
                
               
                 
				<input type="button" name="submit"	value="<?php _e('Update','wp-ticket-ultra'); ?>"						class="button button-primary wptu-btn-submit-field"  data-edition="<?php echo $pos; ?>" /> 
                   <input type="button" value="<?php _e('Cancel','wp-ticket-ultra'); ?>"
						class="button button-secondary wptu-btn-close-edition-field" data-edition="<?php echo $pos; ?>" />
				</p>
                
      <?php
	  
	  die();
		
	}
	
	public function create_standard_form_fields ($form_name )	
	{		
	
		/* These are the basic profile fields */
		$fields_array = array(
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
		if (!get_option($form_name))
		{
			if($form_name!="")
			{
				update_option($form_name,$fields_array);
			
			}
			
		}	
		
		
	}
	
	/*Loads all field list */	
	function reload_custom_fields_set ()	
	{
		
		global $bookingultrapro;
		
		$custom_form = $_POST["custom_form"];
		$custom_form = 'wptu_profile_fields_'.$custom_form;	
		
		$fields = get_option($custom_form);
		
		
		if(!is_array($fields)){$fields = array();}
		ksort($fields);		
		
		$i = 0;
		foreach($fields as $pos => $array) 
		{
		    extract($array); $i++;

		    if(!isset($required))
		        $required = 0;

		    if(!isset($fonticon))
		        $fonticon = '';
				
				
			if ($type == 'seperator' || $type == 'separator') {
			   
				$class = "separator";
				$class_title = "";
			} else {
			  
				$class = "profile-field";
				$class_title = "profile-field";
			}
		    ?>
            
          <li class="wptu-profile-fields-row <?php echo $class_title?>" id="<?php echo $pos; ?>">
            
            
            <div class="heading_title  <?php echo $class?>">
            
            <h3>
            <?php
			
			if (isset($array['name']) && $array['name'])
			{
			    echo  stripslashes($array['name']);
			}
			?>
            
            <?php
			if ($type == 'separator') {
				
			    echo __(' - Separator','wp-ticket-ultra');
				
			} else {
				
			    echo __(' - Profile Field','wp-ticket-ultra');
				
			}
			?>
            
            </h3>
            
            
              <div class="options-bar">
             
                 <p>                
                    <input type="submit" name="submit" value="<?php _e('Edit','wp-ticket-ultra'); ?>"						class="button wptu-btn-edit-field button-primary" data-edition="<?php echo $pos; ?>" /> <input type="button" value="<?php _e('Delete','wp-ticket-ultra'); ?>"	data-field="<?php echo $pos; ?>" class="button button-secondary wptu-delete-profile-field-btn" />
                    </p>
            
             </div>
            
            
          

            </div>
            
             
             <div class="wptu-ultra-success wptu-notification" id="wptu-sucess-delete-fields-<?php echo $pos; ?>"><?php _e('Success! This field has been deleted ','wp-ticket-ultra'); ?></div>
            
           
        
          <!-- edit field -->
          
          <div class="user-ultra-sect-second uultra-fields-edition user-ultra-rounded"  id="wptu-edit-fields-bock-<?php echo $pos; ?>">
        
          </div>
          
          
          <!-- edit field end -->

       </li>







	<?php
	
	}
		
		die();
		
	
	}
		
	// update settings
    function update_settings() 
	{
		foreach($_POST as $key => $value) 
		{
            if ($key != 'submit')
			{
				if (strpos($key, 'html_') !== false)
                {
                      //$this->userultra_default_options[$key] = stripslashes($value);
                }else{
					
					 // $this->userultra_default_options[$key] = esc_attr($value);
                 }
					
								
					  
					
					$this->wptu_set_option($key, $value) ; 
					
					//special setting for page
					if($key=="wptu_my_account_page")
					{						
						//echo "Page : " . $value;
						 update_option('wptu_my_account_page',$value);				 
						 
						 
					}  

            }
        }
		
		//get checks for each tab
		
		
		 if ( isset ( $_GET['tab'] ) )
		 {
			 
			    $current = $_GET['tab'];
				
          } else {
                $current = $this->default_tab;
          }	 
            
		$special_with_check = $this->get_special_checks($current);
         
        foreach($special_with_check as $key)
        {
           
            
                if(!isset($_POST[$key]))
				{			
                    $value= '0';
					
				 } else {
					 
					  $value= $_POST[$key];
				}	 	
         
			
			$this->wptu_set_option($key, $value) ;  
			
			
            
        }
         
      $this->options = get_option('wptu_options');

        echo '<div class="updated"><p><strong>'.__('Settings saved.','wp-ticket-ultra').'</strong></p></div>';
    }
	
	public function get_special_checks($tab) 
	{
		$special_with_check = array();
		
		if($tab=="settings")
		{				
		
		 $special_with_check = array( 'uultra_loggedin_activated', 'private_message_system','redirect_backend_profile','redirect_backend_registration', 'redirect_registration_when_social','redirect_backend_login', 'social_media_fb_active',  'social_media_google', 'twitter_connect',  'mailchimp_active', 'mailchimp_auto_checked',  'aweber_active', 'aweber_auto_checked',  'uultra_password_1_letter_1_number' , 'uultra_password_one_uppercase' , 'uultra_password_one_lowercase', 'recaptcha_display_registration', 'recaptcha_display_loginform' ,'recaptcha_display_ticketform','recaptcha_display_forgot_password');
		 
		}elseif($tab=="gateway"){
			
			 $special_with_check = array('gateway_paypal_active', 'gateway_bank_active', 'gateway_stripe_active', 'gateway_stripe_success_active' ,'gateway_bank_success_active', 'gateway_free_success_active',  'gateway_paypal_success_active' ,  'appointment_cancellation_active');
		
		}elseif($tab=="mail"){
			
			 $special_with_check = array('bup_smtp_mailing_return_path', 'bup_smtp_mailing_html_txt');
		 
		}
	
	return  $special_with_check ;
	
	}	
	
	public function do_valid_checks()
	{
		
		global $wptucomplement ;
		
		$va = get_option('wptu_c_key');
		
		if(isset($wptucomplement))		
		{		
			if($va=="")
			{
				//
				$this->valid_c = "no";
			
			}
		
		}	
	
	}
	
	public function bup_vv_c_de_a () 
	{		
		global $wptucomplement, $wpdb ;
		
		 	
		$p = sanitize_text_field($_POST["p_s_le"]);		
		
		//validate ulr
		
		$domain = $_SERVER['SERVER_NAME'];		
		$server_add = $_SERVER['SERVER_ADDR'];
		
		
		$url = wptu_pro_url."check_l_p.php";	
		
		
		$response = wp_remote_post(
            $url,
            array(
                'body' => array(
                    'd'   => $domain,
                    'server_ip'     => $server_add,
                    'sial_key' => $p,
					'action' => 'validate',
					
                )
            )
        );

		
		
		$response = json_decode($response["body"]);
		
		$message =$response->{'message'}; 
		$result =$response->{'result'}; 
		$expiration =$response->{'expiration'};
		$serial =$response->{'serial'};
		
		//validate
		
		if ( is_multisite() ) // See if being activated on the entire network or one blog
		{		
			
	 
			// Get this so we can switch back to it later
			$current_blog = $wpdb->blogid;
			// For storing the list of activated blogs
			$activated = array();
			
			// Get all blogs in the network and activate plugin on each one
			
			$args = array(
				'network_id' => $wpdb->siteid,
				'public'     => null,
				'archived'   => null,
				'mature'     => null,
				'spam'       => null,
				'deleted'    => null,
				'limit'      => 100,
				'offset'     => 0,
			);
			$blog_ids = wp_get_sites( $args ); 
		   // print_r($blog_ids);
		
		
			foreach ($blog_ids as $key => $blog)
			{
				$blog_id = $blog["blog_id"];

				switch_to_blog($blog_id);				
				
				if($result =="OK")
				{
					update_option('wptu_c_key',$serial );
					update_option('wptu_c_expiration',$expiration );
					
					$html = '<div class="wptu-ultra-success">'. __("Congratulations!. Your copy has been validated", 'wp-ticket-ultra').'</div>';
				
				}elseif($result =="EXP"){
					
					update_option('wptu_c_key',"" );
					update_option('wptu_c_expiration',$expiration );
					
					$html = '<div class="wptu-ultra-error">'. __("We are sorry the serial key you have used has expired", 'wp-ticket-ultra').'</div>';
				
				}elseif($result =="NO"){
					
					update_option('wptu_c_key',"" );
					update_option('wptu_c_expiration',$expiration );
					
					$html = '<div class="wptu-ultra-error">'. __("We are sorry your serial key is not valid", 'wp-ticket-ultra').'</div>';
				
				}
				
				
			} //end for each
			
			//echo "current blog : " . $current_blog;
			// Switch back to the current blog
			switch_to_blog($current_blog); 
			
			
		}else{
			
			//this is not a multisite
			
			if($result =="OK")
			{
				update_option('wptu_c_key',$serial );
				update_option('wptu_c_expiration',$expiration );
				
				$html = '<div class="wptu-ultra-success">'. __("Congratulations!. Your copy has been validated", 'wp-ticket-ultra').'</div>';
			
			}elseif($result =="EXP"){
				
				update_option('wptu_c_key',"" );
				update_option('wptu_c_expiration',$expiration );
				
				$html = '<div class="wptu-ultra-error">'. __("We are sorry the serial key you have used has expired", 'wp-ticket-ultra').'</div>';
			
			}elseif($result =="NO"){
				
				update_option('wptu_c_key',"" );
				update_option('wptu_c_expiration',$expiration );
				
				$html = '<div class="wptu-ultra-error">'. __("We are sorry your serial key is not valid", 'wp-ticket-ultra').'</div>';
			
			}
			
			
			
		
		}
		
		//
		echo "Domain: " .$domain;
		echo $html;		 
		
		
		die();
		
	}
	
	function initial_setup() {
		
		global $wpticketultra, $wpdb, $wptucomplement ;
		
		$inisetup   = get_option('wptu_ini_setup');
		
		if (!$inisetup) 
		{
			//create product if it doesn't exist
			
			$sql = ' SELECT count(*) as total FROM ' . $wpdb->prefix . ''.$this->table_prefix.'_sites ' ;
			$statuses = $wpdb->get_results($sql);	
			
			$total = $this->fetch_result($statuses);
			$total = $total->total;	
			
			if ($total==0)
			{		
			
				$blog_title = get_bloginfo( 'name' );
			
				$new_record = array('site_id' => 1,	
									'site_name' => $blog_title								
									
									);		
										
				$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_sites', $new_record, array( '%d', '%s' ));
				
				//create departments				
				$product_id = $wpdb->insert_id;
				
				$new_record = array('department_id' => 1,	
									'department_site_id' => 1,
									'department_name' => __('Technical Support','wp-ticket-ultra')																
									
									);	
										
				$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_departments', $new_record, array( '%d', '%d', '%s' ));
				
				$new_record = array('department_id' => 2,	
									'department_site_id' =>1,
									'department_name' => __('Billing Issue','wp-ticket-ultra')																
									
									);	
										
				$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_departments', $new_record, array( '%d', '%d', '%s' ));
				
				$new_record = array('department_id' => 3,	
									'department_site_id' => 1,
									'department_name' => __('Feature Request','wp-ticket-ultra')																
									
									);	
										
				$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_departments', $new_record, array( '%d', '%d', '%s' ));
				
				$new_record = array('department_id' => 4,	
									'department_site_id' => 1,
									'department_name' => __('Refund','wp-ticket-ultra')																
									
									);	
										
				$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_departments', $new_record, array( '%d', '%d', '%s' ));
				
				$new_record = array('department_id' =>5,	
									'department_site_id' =>1,
									'department_name' => __('Other','wp-ticket-ultra')																
									
									);	
										
				$wpdb->insert( $wpdb->prefix .$this->table_prefix.'_departments', $new_record, array( '%d', '%d', '%s' ));
				
				
				
				
			}
			
			update_option('wptu_ini_setup', true);
		}
		
		
	}
	
	function include_tab_content() {
		
		global $wpticketultra, $wpdb, $wptucomplement ;
		
		$screen = get_current_screen();
		
		if( strstr($screen->id, $this->slug ) ) 
		{
			if ( isset ( $_GET['tab'] ) ) 
			{
				$tab = $_GET['tab'];
				
			} else {
				
				$tab = $this->default_tab;
			}
			
			//
			
			
			if (! get_option('wptu_ini_setup')) 
			{
				//this is the first time
				$this->initial_setup();
				
				$tab = "welcome";				
				require_once (wptu_path.'admin/tabs/'.$tab.'.php');				
				
				
			}else{
			
				if($this->valid_c=="" )
				{
					require_once (wptu_path.'admin/tabs/'.$tab.'.php');			
				
				}else{ //no validated
					
					$tab = "licence";				
					require_once (wptu_path.'admin/tabs/'.$tab.'.php');
					
				}
			
			}
			
			
		}
	}
	
	function reset_email_template() 	
	{
		global  $wpticketultra;
		
		$template = $_POST['email_template'];
		$new_template = $this->get_email_template($template);
		$this->wptu_set_option($template, $new_template);
		//subject
		//$subject = $template.'_subjectt';
		//$new_subject = $wpticketultra->get_option($subject);
		//$this->wptu_set_option($subject, $new_subject);
		
		die();
		
		
	}
	
	function get_sites_drop_down_admin($department_id = null)
	{
		global  $wpticketultra;
		
		$html = '';
		
		$site_rows = $wpticketultra->site->get_all();
		
		
		$html .= '<select name="wptu__custom_registration_form" id="wptu__custom_registration_form">';
		$html .= '<option value="" selected="selected">'.__('Select a Department','wp-ticket-ultra').'</option>';
		
		foreach ( $site_rows as $site )
		{		
			
			$html .= '<optgroup label="'.$site->site_name.'" >';
			
			//get services						
			$deptos_rows = $wpticketultra->department->get_all_departments($site->site_id);
			foreach ( $deptos_rows as $depto )
			{
				$selected = '';
				
						
				if($depto->department_id==$service_id){$selected = 'selected';}
				$html .= '<option value="'.$depto->department_id.'" '.$selected.' >'.$depto->department_name.'</option>';
				
			}
			
			$html .= '</optgroup>';
			
		}
		
		$html .= '</select>';
		
		return $html;
	
	}
	
	function admin_page() 
	{
		global $wpticketultra;

		
		
		if (isset($_POST['wptu_update_settings']) &&  $_POST['wptu_reset_email_template']=='') {
            $this->update_settings();
        }
		
		if (isset($_POST['wptu_update_settings']) && $_POST['wptu_reset_email_template']=='yes' && $_POST['wptu_email_template']!='') {
           
			echo '<div class="updated"><p><strong>'.__('Email Template has been restored.','wp-ticket-ultra').'</strong></p></div>';
        }
		
		
		if (isset($_POST['update_bup_slugs']) && $_POST['update_bup_slugs']=='bup_slugs')
		{
           $bookingultrapro->create_rewrite_rules();
           flush_rewrite_rules();
			echo '<div class="updated"><p><strong>'.__('Rewrite Rules were Saved.','wp-ticket-ultra').'</strong></p></div>';
        }
		

		
		
		
			
	?>
	
		<div class="wrap <?php echo $this->slug; ?>-admin"> 
        
           
           
           <?php if (get_option('wptu_ini_setup')) 
				{?>
            
                <h2 class="nav-tab-wrapper"><?php $this->admin_tabs(); ?>
                
                     <div class="wptu-top-options-book">            
                                        
                        <a class="wptu-btn-top1-book" href="?page=wpticketultra&tab=createticket" title="<?php _e('Submit A Ticket','wp-ticket-ultra')?>"><span><i class="fa fa-edit fa-2x"></i><?php _e('Submit A Ticket','wp-ticket-ultra')?></span></a>                     
                                        
                   </div>  
                
                
                 
                
                </h2>  
                
            <?php } ?>       
            

			<div class="<?php echo $this->slug; ?>-admin-contain">    
            
               
			
				<?php 		
				
				
					$this->include_tab_content(); 
				
				
				?>
				
				<div class="clear"></div>
				
			</div>
			
		</div>

	<?php }

}

$key = "admin";
$this->{$key} = new WPTicketUltraAdmin();