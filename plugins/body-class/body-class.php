<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/* 
 * Define Constants 
 */
define( 'BODY_CLASS_AJAX_PLUGIN_UID', 'wp-ajax-body-class' );
define( 'BODY_CLASS_AJAX_PLUGIN_NAME', 'Body class support' );
define( 'BODY_CLASS_AJAX_PLUGIN_JS', WP_AJAX_PLUGIN_URL . '/plugins/body-class/jquery.ajaxify.body-class.js' );

if ( !class_exists( 'WPAjaxPluginBodyClass' ) ) {
	class WPAjaxPluginBodyClass {
		
		function __construct() {
			add_filter( WP_AJAX_PLUGIN_LIST_HOOK, array(&$this, 'wp_ajax_plugin'));
		}
		
		function wp_ajax_plugin ( $plugin_list ) {
			$plugin_list[] = array(BODY_CLASS_AJAX_PLUGIN_UID, BODY_CLASS_AJAX_PLUGIN_NAME, array( &$this, 'wp_ajax_process' ), BODY_CLASS_AJAX_PLUGIN_JS);
			return $plugin_list;
		}
		
		function wp_ajax_process ( $render_array ) {
			$render_array['body_classes'] = get_body_class();
			return $render_array;
		}
		
	}
}

new WPAjaxPluginBodyClass();
