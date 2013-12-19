<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

/* 
 * Define Constants 
 */
define( 'CFM7_AJAX_PLUGIN_UID', 'wp-ajax-contact-form-7' );
define( 'CFM7_AJAX_PLUGIN_NAME', 'Contact form 7 plugin support' );
define( 'CFM7_AJAX_PLUGIN_JS', WP_AJAX_PLUGIN_URL . '/plugins/contact-form-7/jquery.ajaxify.cfm7.js' );

if ( !class_exists( 'WPAjaxPluginCFM7Class' ) ) {
	class WPAjaxPluginCFM7Class {
		
		function __construct() {
			add_filter( WP_AJAX_PLUGIN_LIST_HOOK, array(&$this, 'wp_ajax_plugin'));
			add_action( 'wp_enqueue_scripts' , array(&$this, 'enqueue_scripts'), 201);
		}
		
		function enqueue_scripts() {
			global $wp_scripts;
			$cfm7_script = $wp_scripts->registered['contact-form-7']->src;
			
			wp_localize_script(CFM7_AJAX_PLUGIN_UID, WPAjaxPlugins::dash2Camelcase(CFM7_AJAX_PLUGIN_UID), array('script' => $cfm7_script));
		}
		
		function wp_ajax_plugin ( $plugin_list ) {
			$plugin_list[] = array(CFM7_AJAX_PLUGIN_UID, CFM7_AJAX_PLUGIN_NAME, array( &$this, 'wp_ajax_process' ), CFM7_AJAX_PLUGIN_JS);
			return $plugin_list;
		}
		
		function wp_ajax_process ( $render_array ) {
			require_once('includes/simple_html_dom.php');
			
			$render_array['has_contact'] = false;
			
			$html = str_get_html($render_array['html']);
			$form = $html->find('form[class=wpcf7-form]', 0);
			
			if ($form) {
				$render_array['has_contact'] = true;
				if (preg_match('/(#[A-z_]\w+)/', $form->action, $matches) !== false) {
					$form->action = $matches[1];
					$render_array['html'] = $html->save();
				}
			}
			return $render_array;
		}
		
	}
}

new WPAjaxPluginCFM7Class();
