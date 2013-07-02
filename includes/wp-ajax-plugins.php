<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/* 
 * Define Constants 
 */
define( 'WP_AJAX_PLUGIN_RENDER_HOOK', 'wp-ajax-plugin-render' );

if ( !class_exists( 'WPAjaxPlugins' ) ) {
	class WPAjaxPlugins {
		static $plugin_array;
		static $plugin_names;
		
		function __construct() {
			$this->plugin_array = array();
			$this->plugin_names = array();
			
			/* OPTIMIZE: add plugin order */
			/* OPTIMIZE: add plugin options */
			/* TODO: add plugin js dependencies */
			$this->plugin_array = apply_filters( WP_AJAX_PLUGIN_LIST_HOOK, $this->plugin_array );

			foreach ($this->plugin_array as $plugin) {
				$this->plugin_names[] = $plugin[1];
				add_filter( WP_AJAX_PLUGIN_RENDER_HOOK, $plugin[2] );
				/* TODO : Sanitize plugin input */
			}
			
			add_action( 'wp_enqueue_scripts', array(&$this, 'enqueue_scripts') );
		}
		
		function enqueue_scripts() {
			foreach ($this->plugin_array as $plugin) {
				wp_register_script($plugin[0], $plugin[3], array(WP_AJAX_SCRIPT_UID), false, true);
			}
			if (!is_admin()) {
				foreach ($this->plugin_array as $plugin) {
					wp_enqueue_script($plugin[0]);
					if (isset($plugin[4]) && is_array($plugin[4])) {
						wp_localize_script($plugin[0], $this->dash2Camelcase($plugin[0]), $plugin[4]);
					}
				}
			}
		}
		
		function dash2Camelcase($str) { // Split string in words. 
			$words = explode('-', strtolower($str));
			if(empty($words)) return '';
			$return = $words[0];
			for($i=1;$i<sizeof($words);$i++) {
				$return .= ucfirst(trim($words[$i]));
			} 
			return $return;
		} // - See more at: http://www.mrleong.net/97/php-underscore-to-camelcase/#sthash.NLQiIZE5.dpuf
		
		public function get_plugin_names() {
			return $this->plugin_names;
		}
	}
}
global $wpajaxplugins;
if (!isset($wpajaxplugins)) {
	$wpajaxplugins = new WPAjaxPlugins();
}
