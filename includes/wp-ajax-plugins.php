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
			
			$this->plugin_array = apply_filters( WP_AJAX_PLUGIN_LIST_HOOK, $this->plugin_array );

			foreach ($this->plugin_array as $plugin) {
				$this->plugin_names[] = $plugin[1];
				add_filter( WP_AJAX_PLUGIN_RENDER_HOOK, $plugin[2] );
			}
			
			add_action( 'wp_enqueue_scripts', array(&$this, 'enqueue_scripts') );
		}
		
		function enqueue_scripts() {
			foreach ($this->plugin_array as $plugin) {
				wp_register_script($plugin[0], $plugin[3], array(WP_AJAX_SCRIPT_UID), false, true);
			}
			if (!is_admin()) {
				foreach ($this->plugin_array as $script) {
					wp_enqueue_script($plugin[0]);
				}
			}
		}
		
		public function get_plugin_names() {
			return $this->plugin_names;
		}
	}
}
global $wpajaxplugins;
if (!isset($wpajaxplugins)) {
	$wpajaxplugins = new WPAjaxPlugins();
}
