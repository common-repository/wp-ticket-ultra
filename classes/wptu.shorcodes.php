<?php
class WPTicketUltraShortCode {

	function __construct() 
	{
	
		add_action( 'init',   array(&$this,'wptu_shortcodes'));	
		add_action( 'init', array(&$this,'respo_base_unautop') );	

	}
	
	/**
	* Add the shortcodes
	*/
	function wptu_shortcodes() 
	{	
	
	    add_filter( 'the_content', 'shortcode_unautop');			
		add_shortcode( 'wptu_create_ticket', array(&$this,'create_ticket') );
		add_shortcode( 'wptu_create_ticket_backend', array(&$this,'create_ticket_loggedin') );		
		
		
	}
	
	/**
	* Don't auto-p wrap shortcodes that stand alone
	*/
	function respo_base_unautop() {
		add_filter( 'the_content',  'shortcode_unautop');
	}
	
	public function  create_ticket ($atts)
	{
		global $wpticketultra;		
		return $wpticketultra->ticket->frm_create_ticket($atts);
		
		
	}
	
	public function  create_ticket_loggedin ($atts)
	{
		global $wpticketultra;		
		return $wpticketultra->ticket->frm_create_ticket($atts);
		
		
	}	
	

}
$key = "shortcode";
$this->{$key} = new WPTicketUltraShortCode();