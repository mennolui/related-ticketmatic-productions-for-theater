<?php

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	/**
	 * WPT_Related_TM class.
	 *
	 * Adds related productions to the Theater for WordPress plugin, based on Ticketmatic sales.
	 */
	 
	class WPT_Related_TM {

		
		function __construct() {

			// Set version
			global $wpt_related_tm_version;
			$this->wpt_related_tm_version = $wpt_related_tm_version;

			// A unique identifier for your plugin.
			$this->slug = 'wpt_related_tm';
			
			/*
			 * Load the options for your plugin.
			 * @see WPT_Related_TM_Admin
			 */
			$this->options = get_option('wpt_related_tm');
			
			$this->load_sub_classes();


			add_filter('wpt_related_prods_manual_after',array($this,'wpt_related_prods_manual_after'));

		}
		
		/**
		 * Loads and initializes the sub classes of your plugin.
		 * 
		 * @access private
		 * @return void
		 */
		private function load_sub_classes() {
			if (is_admin()) {
				require_once(dirname(__FILE__) . '/wpt_related_tm_admin.php');
				$this->admin = new WPT_Related_TM_Admin();
			}			
		}

		function wpt_related_prods_manual_after($prods) {

			global $wp_theatre;

			$args = array(
				'upcoming' => true,
			);

			$random_prods = $wp_theatre->productions->get($args);
			if (is_array($random_prods)) {
				$prods = array_merge($prods, $random_prods);
			}

			return $prods;
		}
	}