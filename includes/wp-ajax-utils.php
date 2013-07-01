<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}

if ( !class_exists( 'WPAjaxUtils' ) ) {
	class WPAjaxUtils {
		function _construct() {
		
		}
		static function wpajax_get_global_options(){  
			$wpajax_option = array();  
		  
			$wpajax_option  = get_option(WP_AJAX_SETTINGS);
			
			return $wpajax_option;  
		}
	}
}
