<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
/* TODO: externalize plugin example */
/* 
 * Define Constants 
 */
define( 'PLUGIN_EXAMPLE_AJAX_PLUGIN_UID', 'wp-ajax-plugin-example' );
define( 'PLUGIN_EXAMPLE_AJAX_PLUGIN_NAME', 'Plugin example' );
define( 'PLUGIN_EXAMPLE_AJAX_PLUGIN_JS', WP_AJAX_PLUGIN_URL . '/plugins/plugin-example/jquery.ajaxify.plugin-example.js' );

if ( !class_exists( 'WPAjaxPluginExample' ) ) {
	class WPAjaxPluginExampleClass {
		
		function __construct() {
			/* Register plugin function to wp-ajax plugins filter */
			add_filter( 'wp-ajax-plugin-list', array(&$this, 'wp_ajax_plugin'));
		}
		
		function wp_ajax_plugin ( $plugin_list ) {
			/* Register plugin */
			$plugin_list[] = array(PLUGIN_EXAMPLE_AJAX_PLUGIN_UID, PLUGIN_EXAMPLE_AJAX_PLUGIN_NAME, array( &$this, 'wp_ajax_process' ), PLUGIN_EXAMPLE_AJAX_PLUGIN_JS, 
			/* Optional js params array */ array('test' => 'test'), /* Optional wordpress js dependencies (handlers) */ array('my-script-handle'));
			return $plugin_list;
		}
		
		function wp_ajax_process ( $render_array ) {
			$render_array['example'] = 'example';
			return $render_array;
		}
		
	}
}

new WPAjaxPluginExampleClass();
