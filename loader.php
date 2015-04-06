<?php
/*
Plugin Name: Related Ticketmatic Productions for Theater
Version: 0.1.0
Description: Add related productions to the Theater for WordPress plugin, based on Ticketmatic sales. Requires the Ticketmatic for Theater and Related Productions for Theater plugins.
Author: Menno Luitjes
Author URI: http://mennoluitjes.nl
Text Domain: wpt_related_tm
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$wpt_related_tm_version = '0.1.0';

/**
 * Loads the WPT_Related_TM class.
 *
 * Triggered by the `wpt_loaded` action, which is fired after the Theater for WordPress plugin is loaded.
 * 
 * @access public
 * @return void
 */
function wpt_related_tm_loader() {
	global $wp_theatre;
	
	if (isset($wp_theatre->ticketmatic) && isset($wp_theatre->related)) {

		require_once(dirname(__FILE__) . '/includes/wpt_related_tm.php');	
	
		/**
		 * Add an instance of your class to the global Theater object.
		 * 
		 * Requires Theater 0.9.4.
		 */
		$wp_theatre->related_tm = new WPT_Related_TM();	

	}
}

add_action('wpt_loaded', 'wpt_related_tm_loader');