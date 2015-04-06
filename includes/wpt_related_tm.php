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

		public function wpt_related_prods_manual_after($prods) {
			global $post;

			$related_prods = $this->get_related_for_prod($post->ID);
			if (is_array($related_prods)) {
				$prods = array_merge($prods, $related_prods);
			}

			return $prods;
		}

		private function get_related_for_prod($prodid) {

			if (false === ($related_data = get_transient($this->slug))) {
				// no valid transient found, load from api
				$related_data = $this->get_related_data();
			}

			// valid transient found, or data loaded from api
			if (isset($related_data[$prodid])) {
				$related_prods = array();
				$related_prods[] = $related_data[$prodid];
				return $related_prods;
			}
		}

		private function get_related_data() {
			global $wp_theatre;

			// @todo: https://codex.wordpress.org/Function_Reference/wp_schedule_event

			$related_data = array();

			$orders = $wp_theatre->ticketmatic->api('sales/orders');
			if (is_array($orders)) {
				foreach ($orders as $order) {

					$productions = array();
					$tickets = $wp_theatre->ticketmatic->api('sales/orders/'.$order->id.'/tickets');
					if (is_array($tickets)) {

						$tm_events = array();
						foreach ($tickets as $ticket) {
							$tm_events[] = $ticket->eventid;
						}
						$tm_events = array_unique($tm_events);
var_dump($tm_events);
						foreach ($tm_events as $tm_event) {
							$args = array(
								'post_type' => WPT_Event::post_type_name,
								'posts_per_page' => 1,
								'meta_query' => array(
									array(
										'key' => '_wpt_source_ref',
										'value' => $tm_event,
									)
								),
							);

							$event = get_posts($args);
							if (is_array($event)) {
								$production = get_post_meta($event[0]->ID, WPT_Production::post_type_name);
//var_dump($production);
								if (is_array($production)) {
									$productions[] = $production[0];
								}
							}
						}
					}
$productions = array_unique($productions);
//var_dump($productions);

					foreach ($productions as $p) {
						if (isset($related_data[$p])) {
							$related_data[$p] = array_merge($related_data[$p], $productions);
						}
						else {
							$related_data[$p] = $productions;
						}
					}

				}
			}

			set_transient($this->slug, $related_data, 30 * 1); //DAY_IN_SECONDS
			return $related_data;
		}
	}