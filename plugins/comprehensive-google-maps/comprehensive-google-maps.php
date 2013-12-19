<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/* 
 * Define Constants 
 */
define( 'CGM_AJAX_PLUGIN_UID', 'wp-ajax-comprehensive-google-maps' );
define( 'CGM_AJAX_PLUGIN_NAME', 'Comprehensive google maps plugin support' );
define( 'CGM_AJAX_PLUGIN_JS', WP_AJAX_PLUGIN_URL . '/plugins/comprehensive-google-maps/jquery.ajaxify.cgm.js' );

if ( !class_exists( 'WPAjaxPluginCGMClass' ) ) {
	class WPAjaxPluginCGMClass {
		
		function __construct() {
			add_filter( WP_AJAX_PLUGIN_LIST_HOOK, array(&$this, 'wp_ajax_plugin'));
			add_action( 'wp_enqueue_scripts' , array(&$this, 'enqueue_scripts'));
		}
		
		function enqueue_scripts() {
			wp_enqueue_script('cgmp-google-map-jsapi');
            wp_enqueue_script('cgmp-google-map-orchestrator-framework');
		}
		
		function wp_ajax_plugin ( $plugin_list ) {
			global $wp_scripts;
			$cgm_script = $wp_scripts->registered['cgmp-google-map-orchestrator-framework']->src;
			$plugin_list[] = array(CGM_AJAX_PLUGIN_UID, CGM_AJAX_PLUGIN_NAME, array( &$this, 'wp_ajax_process' ), CGM_AJAX_PLUGIN_JS, array('script' => $cgm_script));
			return $plugin_list;
		}
		
		function wp_ajax_process ( $render_array ) {
			$render_array['has_map'] = false;
			
			if (strpos($render_array['html'], 'google-map-placeholder') !== false) {
				$render_array['has_map'] = true;
			}
			return $render_array;
		}
		
	}
}

new WPAjaxPluginCGMClass();
