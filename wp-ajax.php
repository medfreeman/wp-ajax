<?php
/*
Plugin Name: WP Ajax
Plugin URI: http://www.ork.ch/
Description: Ajaxify wordpress
Author: Mehdi Lahlou
Version: 1.0.1
Author URI: http://www.ork.ch/
*/

if ( !function_exists('add_action') ) {
header('Status: 403 Forbidden');
header('HTTP/1.1 403 Forbidden');
exit();
}
if ( function_exists('add_action') ) {
	//WordPress definitions
	if ( !defined('WP_CONTENT_URL') )
	define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
	if ( !defined('WP_CONTENT_DIR') )
	define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
	if ( !defined('WP_PLUGIN_URL') )
	define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
	if ( !defined('WP_PLUGIN_DIR') )
	define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');
	if ( !defined('PLUGINDIR') )
	define( 'PLUGINDIR', 'wp-content/plugins' ); // Relative to ABSPATH. For back compat.
	if ( !defined('WP_LANG_DIR') )
	define('WP_LANG_DIR', WP_CONTENT_DIR . '/languages');
	// plugin definitions
	define( 'WP_AJAX_BASEDIR', dirname( plugin_basename(__FILE__) ) );
	define( 'WP_AJAX_CACHE_DIR', WP_PLUGIN_DIR. '/' . WP_AJAX_BASEDIR . '/cache/');
	define( 'WP_AJAX_SHORTNAME', 'wp-ajax'); // used to prefix the individual setting field id see wpajax_options_page_fields()  
	define( 'WP_AJAX_PAGE_BASENAME', 'wp-ajax-settings'); // the settings page slug
	define( 'WP_AJAX_SETTINGS', 'wp_ajax_options');
	define( 'WP_AJAX_TEXTDOMAIN', 'wp-ajax' );
	define( 'WP_AJAX_PLUGIN_LIST_HOOK', 'wp-ajax-plugin-list' );
	
	// git plugin updater
	include_once('updater.php');
}

if ( !class_exists( 'WPAjax' ) ) {
	class WPAjax {
		static $ajax_request = false;
		static $plugin_list = array();
	
		/*--------------------------------------------*
		 * Constructor
		 *--------------------------------------------*/
		
		/**
		 * Initializes the plugin by setting localization, filters, and administration functions.
		 */
		function __construct() {
			add_action( 'init', array(&$this, 'load_settings') );
			add_action( 'wp_head' , array(&$this, 'load_loading_style') );
			add_action( 'wp_enqueue_scripts', array(&$this, 'enqueue_script') );
			add_action( 'wp_ajax_nopriv_wp-ajax-submit-url', array(&$this, 'wpajax_url_submitted') );
			add_action( 'wp_ajax_wp-ajax-submit-url', array(&$this, 'wpajax_url_submitted') );
			add_action( 'wp_ajax_nopriv_wp-ajax-submit-form', array(&$this, 'wpajax_form_submitted') );
			add_action( 'wp_ajax_wp-ajax-submit-form', array(&$this, 'wpajax_form_submitted') );
			add_action( 'parse_request', array(&$this, 'wpajax_get_query') );
			add_action( 'admin_enqueue_scripts', array(&$this, 'edit_admin_preview_button') );
			add_filter( 'template_include', array(&$this, 'wpajax_override_template'), 200 );
			
			$this->plugin_list = array();
			$this->plugin_list = apply_filters( WP_AJAX_PLUGIN_LIST_HOOK, $this->plugin_list );
			
			if (is_admin()) { // note the use of is_admin() to double check that this is happening in the admin
				$config = array(
					'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
					'proper_folder_name' => 'wp-ajax', // this is the name of the folder your plugin lives in
					'api_url' => 'https://api.github.com/repos/medfreeman/wp-ajax', // the github API url of your github repo
					'raw_url' => 'https://raw.github.com/medfreeman/wp-ajax/master', // the github raw url of your github repo
					'github_url' => 'https://github.com/medfreeman/wp-ajax', // the github url of your github repo
					'zip_url' => 'https://github.com/medfreeman/wp-ajax/zipball/master', // the zip url of the github repo
					'sslverify' => true, // wether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
					'requires' => '3.5', // which version of WordPress does your plugin require?
					'tested' => '3.5.2', // which version of WordPress is your plugin tested up to?
					'readme' => 'README.md', // which file to use as the readme for the version number
					'access_token' => '' // Access private repositories by authorizing under Appearance > Github Updates when this example plugin is installed
				);
				new WP_GitHub_Updater($config);
			}
		}
		
		public function get_plugin_list () {
			return $this->plugin_list;
		}
		
		function load_settings() {
			load_plugin_textdomain(WP_AJAX_TEXTDOMAIN, false, WP_AJAX_BASEDIR . '/lang/');
			require_once('wp-ajax-utils.php');
			if (is_admin()) {
				require_once('wp-ajax-settings.php');
			}
		}
		
		function load_loading_style() {
			$wpajax_options = WPAjaxUtils::wpajax_get_global_options();
			$loadingcontainer_css = $wpajax_options[WP_AJAX_SHORTNAME . "_loading_css"];
			echo '<style type="text/css">'.$loadingcontainer_css.'</style>';
		}
		
		function enqueue_script() {
			wp_enqueue_script('jquery');
			if (!is_admin()) {
				wp_enqueue_script('jquery-address', WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/js/jquery.address-1.5.min.js', array('jquery'), false, true );
				wp_enqueue_script('jquery-form', WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/js/jquery.form.js', array('jquery'), false, true );
				wp_enqueue_script('jquery-ajax', WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/js/jquery.wp-ajax.js', array('jquery','jquery-address','jquery-form'), false, true );
				
				$plugins = array();
				foreach ($this->plugin_list as $plugin) {
					wp_enqueue_script($plugin[0]);
					$plugins[] = $plugin[3];
				}
				
				$wpajax_options = WPAjaxUtils::wpajax_get_global_options();
				//die(maybe_serialize($wpajax_options));
				$container = $wpajax_options[WP_AJAX_SHORTNAME . "_container_selector"];
				$precode = $wpajax_options[WP_AJAX_SHORTNAME . "_custom_pre_code"];
				$postcode = $wpajax_options[WP_AJAX_SHORTNAME . "_custom_post_code"];
				
				$loading_container = $wpajax_options[WP_AJAX_SHORTNAME . "_loading_container_selector"];
				$loading_transition = $wpajax_options[WP_AJAX_SHORTNAME . "_loading_graphics"];
				
				$transition = $wpajax_options[WP_AJAX_SHORTNAME . "_transition_graphics"];
				$link_selector = $wpajax_options[WP_AJAX_SHORTNAME . "_links_selector"];
				$loading_test_mode = $wpajax_options[WP_AJAX_SHORTNAME . "_loading_test_mode"];
				
				$loading_html = file_get_contents(dirname(__FILE__) . '/loading/' . $loading_transition . '/' . $loading_transition . '.html');
				wp_localize_script( 'jquery-ajax', 'wpAjax', array( 'ajaxurl' => WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/wp-ajax-client.php', 'baseurl'=> home_url().'/', 'container'=> $container, 'pre_code' => $precode, 'post_code' => $postcode, 'loading_container' => $loading_container, 'loading_html' => $loading_html, 'links_selector' => $link_selector, 'plugins' => $plugins, 'loading_test_mode' => $loading_test_mode ));
				wp_enqueue_style( 'wp-ajax-transition',  WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/transitions/' . $transition . '/' . $transition . '.css');
				wp_enqueue_style( 'wp-ajax-loading',  WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/loading/' . $loading_transition . '/' . $loading_transition . '.css');
			}
		}
		
		function wpajax_override_template($file) {
			if ($this->ajax_request) {
				$wpajax_options = WPAjaxUtils::wpajax_get_global_options();
				
				$filename = basename($file);
				$ajaxfile = WP_AJAX_CACHE_DIR.substr(basename($filename),0,strlen($filename)-4)."-ajax.php";
				if ($wpajax_options[WP_AJAX_SHORTNAME . "_cache_enabled"]) {
					if (file_exists($ajaxfile)) {
						$this->wpajax_submit_ajax_template($ajaxfile);
					} else {
						$ajaxfile_content = $this->wpajax_convert_template_file($file);
						if ($this->wpajax_create_template_file($ajaxfile,$ajaxfile_content)) {
							$this->wpajax_submit_ajax_template($ajaxfile);
						} else {
							$this->wpajax_submit_html(__('Error creating ajax template', WP_AJAX_TEXTDOMAIN));
						}
					}
				} else {
					$ajaxfile_content = $this->wpajax_convert_template_file($file);
					if ($this->wpajax_create_template_file($ajaxfile,$ajaxfile_content)) {
						$this->wpajax_submit_ajax_template($ajaxfile);
					} else {
						$this->wpajax_submit_html(__('Error creating ajax template', WP_AJAX_TEXTDOMAIN));
					}
				}
			}
			return $file;
		}
		
		function wpajax_submit_ajax_template($ajaxfile) {
			ob_start();
			include($ajaxfile);
			$html = ob_get_clean();
			$this->wpajax_submit_html($html);
		}
		
		function wpajax_submit_html($html) {
			header( "Content-Type: application/json; charset=utf-8" );
			$render_array = array('html' => $html);
			foreach ($this->plugin_list as $plugin) {
				$render_array = call_user_func ( $plugin[2] , $render_array );
			}
			echo json_encode($render_array);
			
			// IMPORTANT: don't forget to "exit"
			exit;
		}
	
		function wpajax_url_submitted() {
			// get the submitted parameters
			$_SERVER['REQUEST_URI'] = $_POST['url'];
			$_SERVER['QUERY_STRING'] = parse_url($_POST['url'],PHP_URL_QUERY);
		//die($_SERVER['QUERY_STRING']);
			//Nasty hack to prevent php warning breaking json
			if (!isset($this->public_query_vars))
				$this->public_query_vars = array();
			$qs = 'ajax=true';
			if ($_SERVER['QUERY_STRING'] != '')
				$qs .= '&'.$_SERVER['QUERY_STRING'];
			WP::parse_request($qs);
		}
		
		function wpajax_form_submitted() {
			// get the submitted parameters
			$_SERVER['REQUEST_URI'] = $_POST['url'];
			//Nasty hack to prevent php warning breaking json
			if (!isset($this->public_query_vars))
				$this->public_query_vars = array();
			WP::parse_request('ajax=true');
		}
		
		function wpajax_get_query(&$query_vars) {
			if(isset($query_vars->extra_query_vars['ajax'])) {
				global $wp_query;
				$wp_query->parse_query($query_vars->matched_query);
		
				$wp_query->query_vars = wp_parse_args( $wp_query->query_vars, $query_vars->query_vars );
				$wp_query->query = array_filter($wp_query->query_vars);
				
				foreach($wp_query->query as $key=>$value) {
						WP::set_query_var($key, $value);
				}
				
				WP::build_query_string();
				$wp_query->get_posts();
				WP::register_globals();
				
				//Manque une fonction par rapport à la détermination de quoi afficher en home
				
				define('WP_USE_THEMES',true);
				$this->ajax_request = true;
				include(ABSPATH."wp-includes/template-loader.php");
			} else {
				$this->ajax_request = false;
			}
		}
		
		function wpajax_convert_template_file($file) {
			$fp = fopen($file, "r");
			$output = "";
			while(!feof( $fp )) {
				$data = fgets($fp, 4096);
				
				$pos = strpos($data, "get_header();");
				if( $pos !== false) {
					$data = substr($data,0,$pos).substr($data,$pos+13);
				}
				
				$pos = strpos($data, "get_footer();");
				if( $pos !== false) {
					$data = substr($data,0,$pos).substr($data,$pos+13);
				}
				$output .= $data;
			}
			fclose($fp);
			return $output;
		}
		
		function wpajax_create_template_file($file,$content) {
			if(!$fh = fopen($file, 'w')){
				return false;
			}
			fwrite($fh, $content);
			fclose($fh);
			return true;
		}
		
		function edit_admin_preview_button($hook) {
				if( 'post.php' == $hook || 'post-new.php' == $hook) {
					wp_enqueue_script( 'jquery-edit-preview-button', plugins_url('/js/jquery.edit.preview.button.js', __FILE__) );
					wp_localize_script( 'jquery-edit-preview-button', 'wpAjax', array( 'baseurl'=> home_url().'/' ) );
				}
		}
	}
}
global $wpajax;
if (!isset($wpajax)) {
	$wpajax = new WPAjax();
}
