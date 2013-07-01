<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/* 
 * Define Constants 
 */
define( 'WP_NONCE_AJAX_PLUGIN_UID', 'wp-ajax-nonce' );
define( 'WP_NONCE_AJAX_PLUGIN_NAME', 'Wordpress nonce support' );
define( 'WP_NONCE_AJAX_PLUGIN_JS', WP_AJAX_PLUGIN_URL . '/plugins/wp-nonce/jquery.ajaxify.wp-nonce.js' );
define( 'WP_NONCE_AJAX_PLUGIN_NONCE', 'wp-ajax-plugin-wp-nonce' );

if ( !class_exists( 'WPAjaxPluginNonceClass' ) ) {
	class WPAjaxPluginNonceClass {
		
		function __construct() {
			add_filter( WP_AJAX_PLUGIN_LIST_HOOK, array(&$this, 'wp_ajax_plugin'));
		}
		
		function wp_ajax_plugin ( $plugin_list ) {
			$plugin_list[] = array(WP_NONCE_AJAX_PLUGIN_UID, WP_NONCE_AJAX_PLUGIN_NAME, array( &$this, 'wp_ajax_process' ), WP_NONCE_AJAX_PLUGIN_JS, array('nonce' => wp_create_nonce(WP_NONCE_AJAX_PLUGIN_NONCE)));
			return $plugin_list;
		}
		
		function wp_ajax_process ( $render_array ) {
			check_ajax_referer(WP_NONCE_AJAX_PLUGIN_NONCE);
			return $render_array;
		}
		
	}
}

new WPAjaxPluginNonceClass();
