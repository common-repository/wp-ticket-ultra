<?php
global $wpticketultra;

define('wptu_profiles_url',plugin_dir_url(__FILE__ ));
define('wptu_profiles_path',plugin_dir_path(__FILE__ ));

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(isset($wpticketultra)){

	
	/* administration */
	if (is_admin()){
		foreach (glob(wptu_profiles_path . 'admin/*.php') as $filename) { include $filename; }
	}
	
}