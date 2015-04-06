<?php
	
class WPT_Related_TM_Admin {

	function __construct() {
		
		add_filter('admin_init',array($this,'admin_init'));

	}
		
		
	/**
	 * Adds settings fields to your new settings tab.
	 *
	 * The settings tabs are based on the Settings API.
	 * @see http://codex.wordpress.org/Settings_API
	 * 
	 * @return void
	 */
	function admin_init() {
		
		global $wp_theatre;	

        add_settings_section(
            'wpt_related_ticketmatic', // ID
            '', // Title
            '', // Callback
            $wp_theatre->related->slug // Page
        );  

        add_settings_field(
            'wpt_related_limit', // ID
            __('Ticketmatic sales ready','wpt_related_tm_ready'), // Title 
            array( $this, 'settings_field_ready' ), // Callback
            $wp_theatre->related->slug, // Page
            'wpt_related_ticketmatic' // Section           
        );      
	}

	/**
	 * Renders the 'limit' select field of related productions settings.
	 * 
	 * @return void
	 */	 	
	function settings_field_ready() {
		global $wp_theatre;
		
		echo 'Yes';
	}	
	
}

