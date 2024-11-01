<?php
/*
Plugin Name: WP Ticket Ultra
Plugin URI: https://wpticketsultra.com
Description: Help Desk and Ticketing Plugin. This plugin allows you to offer support to your users and clients.
Version: 1.0.5
Author: WP Ticket Ultra
Text Domain: wp-ticket-ultra
Domain Path: /languages
Author URI: https://wpticketsultra.com/
*/
define('wptu_url',plugin_dir_url(__FILE__ ));
define('wptu_path',plugin_dir_path(__FILE__ ));
define('WPTU_PLUGIN_SETTINGS_URL',"?page=wpticketultra&tab=pro");
define('WPTU_PLUGIN_WELCOME_URL',"?page=wpticketultra&tab=welcome");

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


$plugin = plugin_basename(__FILE__);

/* Loading Function */
require_once (wptu_path . 'functions/functions.php');

/* Init */
define('wptu_pro_url','https://wpticketsultra.com/');


function wptu_load_textdomain() 
{     	   
	   $locale = apply_filters( 'plugin_locale', get_locale(), 'wp-ticket-ultra' );	   
       $mofile = wptu_path . "languages/wptu-$locale.mo";
			
		// Global + Frontend Locale
		load_textdomain( 'wp-ticket-ultra', $mofile );
		load_plugin_textdomain( 'wp-ticket-ultra', false, dirname(plugin_basename(__FILE__)).'/languages/' );
}

/* Load plugin text domain (localization) */
add_action('init', 'wptu_load_textdomain');	
		
add_action('init', 'wptu_output_buffer');
function wptu_output_buffer() {
		ob_start();
}

/* Master Class  */
require_once (wptu_path . 'classes/wptu.class.php');

// Helper to activate a plugin on another site without causing a fatal error by
register_activation_hook( __FILE__, 'wptu_activation');
 
function  wptu_activation( $network_wide ) 
{
	$plugin_path = '';
	$plugin = "wp-ticket-ultra/index.php";	
	
	if ( is_multisite() && $network_wide ) // See if being activated on the entire network or one blog
	{ 
		activate_plugin($plugin_path,NULL,true);
			
		
	} else { // Running on a single blog		   	
			
		activate_plugin($plugin_path,NULL,false);		
		
	}
}

$wpticketultra = new WPTicketUltra();
$wpticketultra->plugin_init();

register_activation_hook(__FILE__, 'wptu_my_plugin_activate');
add_action('admin_init', 'wptu_my_plugin_redirect');

function wptu_my_plugin_activate() 
{
    add_option('wptu_plugin_do_activation_redirect', true);
}

function wptu_my_plugin_deactivate() 
{

}

function wptu_my_plugin_redirect() 
{
    if (get_option('wptu_plugin_do_activation_redirect', false)) {
        delete_option('wptu_plugin_do_activation_redirect');
		
		if (! get_option('wptu_ini_setup')) 
		{
			wp_redirect(WPTU_PLUGIN_WELCOME_URL);
        
		
		}else{
				
			wp_redirect(WPTU_PLUGIN_SETTINGS_URL);
			
		}
    }
}

require_once wptu_path . 'addons/profiles/index.php';