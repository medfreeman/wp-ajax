<?php
if ( !function_exists('add_action') ) {
	header('Status: 403 Forbidden');
	header('HTTP/1.1 403 Forbidden');
	exit();
}
/* TODO : Remove duplicate saving message in admin */
/* 
 * Define Constants 
 */
define( 'WP_AJAX_TRANSITIONS_DIR', WP_PLUGIN_DIR. '/' . WP_AJAX_BASEDIR . '/transitions');
define( 'WP_AJAX_LOADING_DIR', WP_PLUGIN_DIR. '/' . WP_AJAX_BASEDIR . '/loading');

if ( !class_exists( 'WPAjaxSettings' ) ) {
	class WPAjaxSettings {
		function __construct() {
			add_action( 'admin_menu', array(&$this,'wpajax_add_menu') );
			add_action( 'admin_init', array(&$this,'wpajax_register_settings') );
			add_action( 'admin_notices', array(&$this,'wpajax_global_admin_msgs') );
			add_action( 'admin_notices', array(&$this,'wpajax_admin_msgs') );
		}
		function wpajax_add_menu() {
			$wpajax_settings_page = add_options_page(__('WP Ajax',WP_AJAX_TEXTDOMAIN), __('WP Ajax',WP_AJAX_TEXTDOMAIN), 'manage_options', WP_AJAX_PAGE_BASENAME, array($this,'wpajax_settings_page_fn'));
			 // css & js  
			add_action( 'load-'. $wpajax_settings_page, array(&$this, 'wpajax_settings_scripts') );
		}
		 /* 
		 * Group scripts (js & css) 
		 */  
		function wpajax_settings_scripts() {
			wp_enqueue_style('wp-ajax_settings_css', WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/css/admin.css');
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'wp-ajax_settings_js', WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR . '/js/admin.js', array('jquery'));  
			wp_localize_script( 'wp-ajax_settings_js', 'wpAjaxSettings', array( 'plugin_url' => WP_PLUGIN_URL . '/' . WP_AJAX_BASEDIR) );
		}  
		function wpajax_get_settings() {  
  
			$output = array();  
		  
			// put together the output array  
			$output['wp-ajax_option_name']       = WP_AJAX_SETTINGS; // the option name as used in the get_option() call.  
			$output['wp-ajax_page_title']        = __( 'WP Ajax Settings page',WP_AJAX_TEXTDOMAIN); // the settings page title  
			$output['wp-ajax_page_sections']     = $this->wpajax_options_page_sections(); // the setting section  
			$output['wp-ajax_page_fields']       = $this->wpajax_options_page_fields();  ; // the setting fields  
			$output['wp-ajax_contextual_help']   = ''; // the contextual help  
		  
			return $output;  
		}
		function wpajax_register_settings() {
			// get the settings sections array  
			$settings_output    = $this->wpajax_get_settings();  
			$wpajax_option_name = $settings_output['wp-ajax_option_name'];  
		  
			//setting  
			//register_setting( $option_group, $option_name, $sanitize_callback );  
			register_setting($wpajax_option_name, $wpajax_option_name, array(&$this, 'wpajax_validate_options') ); 
			
			//sections
			if(!empty($settings_output['wp-ajax_page_sections'])){  
				// call the "add_settings_section" for each!
				foreach ( $settings_output['wp-ajax_page_sections'] as $id => $title ) {  
					add_settings_section( $id, $title, array($this, 'wpajax_section_fn'), __FILE__);  
				}
			}
			
			//fields  
			if(!empty($settings_output['wp-ajax_page_fields'])){  
				// call the "add_settings_field" for each!  
				foreach ($settings_output['wp-ajax_page_fields'] as $option) {  
					$this->wpajax_create_settings_field($option);  
				}  
			}  
		}
		function wpajax_settings_page_fn() {
			global $wp_settings_sections;
			// get the settings sections array  
			$settings_output = $this->wpajax_get_settings();  
		?>  
			<div class="wrap">  
				<div class="icon32" id="icon-options-general"></div>  
				<h2><?php echo $settings_output['wp-ajax_page_title']; ?></h2>
		  
				<form action="options.php" method="post">
				<?php
					// http://codex.wordpress.org/Function_Reference/settings_fields  
					settings_fields($settings_output['wp-ajax_option_name']);
					
					echo '<div class="ui-tabs">
							<ul class="ui-tabs-nav">';

					foreach ( $wp_settings_sections[__FILE__] as $id => $section )
						echo '<li><a href="#' . strtolower( str_replace( ' ', '_', $section['title'] ) ) . '">' . $section['title'] . '</a></li>';

					echo '</ul>';
		  
					// http://codex.wordpress.org/Function_Reference/do_settings_sections  
					do_settings_sections(__FILE__);  
				?>
					</div>
					<p class="submit">  
						<input name="Submit" type="submit" class="button-primary" value="<?php _e('Save Changes',WP_AJAX_TEXTDOMAIN); ?>" />  
					</p>  
		  
				</form>  
			</div><!-- wrap -->  
		<?php 
		}
		function wpajax_options_page_sections() {  
  
			$sections = array();
			$sections['cache_section']    = __('Cache', WP_AJAX_TEXTDOMAIN);
			$sections['links_section']    = __('Links', WP_AJAX_TEXTDOMAIN);
			$sections['jquery_section']    = __('jQuery', WP_AJAX_TEXTDOMAIN);  
			$sections['loading_section']     = __('Loading animation', WP_AJAX_TEXTDOMAIN);
			$sections['transition_section']     = __('Transition animation', WP_AJAX_TEXTDOMAIN);
			$sections['forms_section']     = __('Forms', WP_AJAX_TEXTDOMAIN);
			$sections['plugin_support_section']     = __('Plugin Support', WP_AJAX_TEXTDOMAIN);
			$sections['plugins_section']     = __('Plugins', WP_AJAX_TEXTDOMAIN);
			//$sections['export_section']     = __('Import / Export settings', WP_AJAX_TEXTDOMAIN);
		  
			return $sections;  
		}
		function wpajax_options_page_fields() { 
			// Text Form Fields section
			
			$options[] = array(  
				"section" => "cache_section",  
				"id"      => WP_AJAX_SHORTNAME . "_cache_enabled",  
				"title"   => __( 'Enable caching', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Enable automatic caching of ajax-ready template files (enable in production for performance gain, disable if you\'re modifying template files).', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "checkbox",  
				"std"     => 0 // 0 for off  
			);
			
			$options[] = array(  
				"section" => "cache_section",  
				"id"      => WP_AJAX_SHORTNAME . "_cache_files",  
				"title"   => __( 'Cached files', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'The number of template files modified and cached for ajax requests. Even if caching is disabled files exist (so they can be php include\'d), but are rewritten every request.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "caption",  
				"std"     => $this->wpajax_num_cache_files(),  
			); 
			
			$options[] = array(  
				"section" => "links_section",  
				"id"      => WP_AJAX_SHORTNAME . "_links_selector",  
				"title"   => __( 'Links Selector', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'The jquery selector(s) of links which have to be converted.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => 'a:not(external):not(.no-ajax):not(.ab-item)',  
				"class"   => "nohtml"  
			);  
			
			$options[] = array(  
				"section" => "jquery_section",  
				"id"      => WP_AJAX_SHORTNAME . "_container_selector",  
				"title"   => __( 'Container Selector', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'The jquery selector of an HTML Element used as container, opening must be contained in header.php and closing in footer.php.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => '#content',  
				"class"   => "nohtml"  
			);  
			
			$options[] = array(  
				"section" => "jquery_section",  
				"id"      => WP_AJAX_SHORTNAME . "_custom_pre_code",  
				"title"   => __( 'Custom jquery code (before ajax request)', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Custom code to execute before the ajax request for the content is made.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => '',
				"class"   => "allowlinebreaks"
			);
			
			$options[] = array(  
				"section" => "jquery_section",  
				"id"      => WP_AJAX_SHORTNAME . "_custom_post_code",  
				"title"   => __( 'Custom jquery code (after ajax request)', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Custom code to execute after the ajax request for the content is made.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => '',
				"class"   => "allowlinebreaks"
			);
			
			$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_test_mode",  
				"title"   => __( 'Loading animation test mode', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'When activated, requests for content will never be made, instead the loading animation will go on forever..', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "checkbox",  
				"std"     => 0
			);
			
			$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_container_selector",  
				"title"   => __( 'Loading container Selector', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'The jquery selector of an HTML Element used as container for the loading animation.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => '#container',  
				"class"   => "nohtml"  
			);
			
			$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_graphics",  
				"title"   => __( 'Loading animation', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Choose loading animation. For custom mode, you have to enter HTML, CSS3 and jQuery animation code.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "select2",  
				"std"    => "",  
				"choices" => $this->wpajax_get_loading_options()
			);
			
			$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_container_position_selector",  
				"title"   => __( 'Loading container Positionment Selector', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'The jquery selector of an HTML Element used as reference for loading animation placement.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => '#content',  
				"class"   => "nohtml"  
			);
			
			$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_graphics_position",  
				"title"   => __( 'Loading animation placement', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Choose loading animation placement, relative to selected element. For custom mode, you have to enter HTML, CSS3 and jQuery animation code.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "select2",  
				"std"    => "",  
				"choices" => $this->wpajax_get_loading_position_options()
			);
			
			/*$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_container_wrapper",  
				"title"   => __( 'Loading container HTML Code', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'The HTML code used as loading animation. Allowed tags : &lt;div>, &lt;span>', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => '',
				"attr"	  => "readonly"
			);
			
			$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_css",  
				"title"   => __( 'Custom loading animation css code', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Enter loading animation css code, use css3 animations to animate.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => '',
				"class"   => "allowlinebreaks",
				"attr"	  => "readonly"
			);
			
			$options[] = array(  
				"section" => "loading_section",  
				"id"      => WP_AJAX_SHORTNAME . "_loading_js",  
				"title"   => __( 'Custom loading animation jquery code', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Enter loading animation jquery code (use the function \'animatePreloader\'), used as fallback if css3 animation isn\'t supported in the client\'s browser.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => '',
				"class"   => "allowlinebreaks",
				"attr"	  => "readonly"
			);*/
			
			$options[] = array(  
				"section" => "transition_section",  
				"id"      => WP_AJAX_SHORTNAME . "_transition_graphics",  
				"title"   => __( 'Transition animation', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Choose transition animation (between contents). For custom mode, you have to enter jQuery animation code.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "select2",  
				"std"    => '',  
				"choices" => $this->wpajax_get_transition_options()
			);
			
			/*$options[] = array(  
				"section" => "transition_section",  
				"id"      => WP_AJAX_SHORTNAME . "_transition_js",  
				"title"   => __( 'Custom transition animation jquery code (OUT)', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Enter transition animation jquery code for content disappearing. Use the function \'addPreloader\' as callback.', WP_AJAX_TEXTDOMAIN ).'<br/>'.__( 'You can use wpAjax.container or wpAjax.loading_container as container element.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => '',
				"class"   => "allowlinebreaks",
				"attr"	  => "readonly"
			);
			
			$options[] = array(  
				"section" => "transition_section",  
				"id"      => WP_AJAX_SHORTNAME . "_transition_js_in",  
				"title"   => __( 'Custom transition animation jquery code (IN)', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Enter transition animation jquery code for content reappearing.', WP_AJAX_TEXTDOMAIN ).'<br/>'.__( 'You can use wpAjax.container or wpAjax.loading_container as container element.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => '',
				"class"   => "allowlinebreaks",
				"attr"	  => "readonly"
			);*/
			
			$options[] = array(  
				"section" => "transition_section",  
				"id"      => WP_AJAX_SHORTNAME . "_last_tab",  
				"title"   => "",
				"type"    => "hidden",  
				"std"     => 0
			);
			
			
			$plugin_list = "";
			global $wpajaxplugins;
			foreach ($wpajaxplugins->get_plugin_names() as $plugin) {
				$plugin_list .= $plugin."<br/>";
			}
			$options[] = array(  
				"section" => "plugins_section",  
				"id"      => WP_AJAX_SHORTNAME . "_plugin_list",  
				"title"   => __( 'Plugin List', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( '', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "caption",  
				"std"     => $plugin_list,  
			); 
			
			/*$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_txt_input",  
				"title"   => __( 'Text Input - Some HTML OK!', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A regular text input field. Some inline HTML (&lt;a>, &lt;b>, &lt;em>, &lt;i>, &lt;strong>) is allowed.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => __('Some default value',WP_AJAX_TEXTDOMAIN)  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_nohtml_txt_input",  
				"title"   => __( 'No HTML!', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A text input field where no html input is allowed.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => __('Some default value',WP_AJAX_TEXTDOMAIN),  
				"class"   => "nohtml"  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_numeric_txt_input",  
				"title"   => __( 'Numeric Input', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A text input field where only numeric input is allowed.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => "123",  
				"class"   => "numeric"  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_multinumeric_txt_input",  
				"title"   => __( 'Multinumeric Input', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A text input field where only multible numeric input (i.e. comma separated numeric values) is allowed.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => "123,234,345",  
				"class"   => "multinumeric"  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_url_txt_input",  
				"title"   => __( 'URL Input', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A text input field which can be used for urls.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => "http://wp.tutsplus.com",  
				"class"   => "url"  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_email_txt_input",  
				"title"   => __( 'Email Input', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A text input field which can be used for email input.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "text",  
				"std"     => "email@email.com",  
				"class"   => "email"  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_multi_txt_input",  
				"title"   => __( 'Multi-Text Inputs', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A group of text input fields', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "multi-text",  
				"choices" => array( __('Text input 1',WP_AJAX_TEXTDOMAIN) . "|txt_input1", __('Text input 2',WP_AJAX_TEXTDOMAIN) . "|txt_input2", __('Text input 3',WP_AJAX_TEXTDOMAIN) . "|txt_input3", __('Text input 4',WP_AJAX_TEXTDOMAIN) . "|txt_input4"),  
				"std"     => ""  
			);
		  
			// Textarea Form Fields section  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_txtarea_input",  
				"title"   => __( 'Textarea - HTML OK!', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A textarea for a block of text. HTML tags allowed!', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => __('Some default value',WP_AJAX_TEXTDOMAIN)  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_nohtml_txtarea_input",  
				"title"   => __( 'No HTML!', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A textarea for a block of text. No HTML!', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => __('Some default value',WP_AJAX_TEXTDOMAIN),  
				"class"   => "nohtml"  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_allowlinebreaks_txtarea_input",  
				"title"   => __( 'No HTML! Line breaks OK!', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'No HTML! Line breaks allowed!', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => __('Some default value',WP_AJAX_TEXTDOMAIN),  
				"class"   => "allowlinebreaks"  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_inlinehtml_txtarea_input",  
				"title"   => __( 'Some Inline HTML ONLY!', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A textarea for a block of text. 
					Only some inline HTML 
					(&lt;a>, &lt;b>, &lt;em>, &lt;strong>, &lt;abbr>, &lt;acronym>, &lt;blockquote>, &lt;cite>, &lt;code>, &lt;del>, &lt;q>, &lt;strike>) 
					is allowed!', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "textarea",  
				"std"     => __('Some default value',WP_AJAX_TEXTDOMAIN),  
				"class"   => "inlinehtml"  
			);    
		  
			// Select Form Fields section  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_select_input",  
				"title"   => __( 'Select (type one)', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A regular select form field', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "select",  
				"std"    => "3",  
				"choices" => array( "1", "2", "3")  
			);  
		  
			$options[] = array(  
				"section" => "appearance_section",  
				"id"      => WP_AJAX_SHORTNAME . "_select2_input",  
				"title"   => __( 'Select (type two)', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'A select field with a label for the option and a corresponding value.', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "select2",  
				"std"    => "",  
				"choices" => array( __('Option 1',WP_AJAX_TEXTDOMAIN) . "|opt1", __('Option 2',WP_AJAX_TEXTDOMAIN) . "|opt2", __('Option 3',WP_AJAX_TEXTDOMAIN) . "|opt3", __('Option 4',WP_AJAX_TEXTDOMAIN) . "|opt4")  
			);  
		  
			// Checkbox Form Fields section  
			$options[] = array(  
				"section" => "checkbox_section",  
				"id"      => WP_AJAX_SHORTNAME . "_checkbox_input",  
				"title"   => __( 'Checkbox', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Some Description', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "checkbox",  
				"std"     => 1 // 0 for off  
			);  
		  
			$options[] = array(  
				"section" => "checkbox_section",  
				"id"      => WP_AJAX_SHORTNAME . "_multicheckbox_inputs",  
				"title"   => __( 'Multi-Checkbox', WP_AJAX_TEXTDOMAIN ),  
				"desc"    => __( 'Some Description', WP_AJAX_TEXTDOMAIN ),  
				"type"    => "multi-checkbox",  
				"std"     => '',  
				"choices" => array( __('Checkbox 1',WP_AJAX_TEXTDOMAIN) . "|chckbx1", __('Checkbox 2',WP_AJAX_TEXTDOMAIN) . "|chckbx2", __('Checkbox 3',WP_AJAX_TEXTDOMAIN) . "|chckbx3", __('Checkbox 4',WP_AJAX_TEXTDOMAIN) . "|chckbx4")  
			);  */
		  
			return $options;
		}
		function wpajax_create_settings_field( $args = array() ) {  
			// default array to overwrite when calling the function  
			$defaults = array(  
				'id'      => 'default_field',                    // the ID of the setting in our options array, and the ID of the HTML form element  
				'title'   => 'Default Field',                    // the label for the HTML form element  
				'desc'    => 'This is a default description.',  // the description displayed under the HTML form element  
				'std'     => '',                                 // the default value for this setting  
				'type'    => 'text',                             // the HTML form element to use  
				'section' => 'main_section',                     // the section this setting belongs to — must match the array key of a section in wpajax_options_page_sections()  
				'choices' => array(),                            // (optional): the values in radio buttons or a drop-down menu  
				'class'   => ''                                  // the HTML form element class. Also used for validation purposes!  
			);  
		  
			// "extract" to be able to use the array keys as variables in our function output below  
			extract( wp_parse_args( $args, $defaults ) );  
		  
			// additional arguments for use in form field output in the function wpajax_form_field_fn!  
			$field_args = array(  
				'type'      => $type,  
				'id'        => $id,  
				'desc'      => $desc,  
				'std'       => $std,  
				'choices'   => $choices,  
				'label_for' => $id,  
				'class'     => $class,
				'attr'		=> $attr
			);  
		  
			add_settings_field( $id, $title, array($this, 'wpajax_form_field_fn'), __FILE__, $section, $field_args );
		}
		function  wpajax_section_fn($desc) {
			//echo "<div>" . __('Settings for this section',WP_AJAX_TEXTDOMAIN) . "</div>";  
		}
		/* 
		 * Form Fields HTML 
		 * All form field types share the same function!! 
		 * @return echoes output 
		 */  
		function wpajax_form_field_fn($args = array()) {
		  
			extract( $args );  
		  
			// get the settings sections array  
			$settings_output    = $this->wpajax_get_settings();  
		  
			$wpajax_option_name = $settings_output['wp-ajax_option_name'];  
			$options            = get_option($wpajax_option_name);  
		  
			// pass the standard value if the option is not yet set in the database  
			if ( !isset( $options[$id] ) && 'type' != 'checkbox' ) {  
				$options[$id] = $std;  
			}  
		  
			// additional field class. output only if the class is defined in the create_setting arguments  
			$field_class = ($class != '') ? ' ' . $class : '';  
			$field_attr = ($attr == "readonly") ? ' readonly="readonly"' : '';
		  
			// switch html display based on the setting type.  
			switch ( $type ) {
				case 'hidden':
					echo "<input type='hidden' id='$id' name='" . $wpajax_option_name . "[$id]' value='$options[$id]' $field_attr />";
				break;
				case 'caption':
					echo "<p class='caption$field_class' id='$id'>$options[$id]</p>";  
					echo ($desc != '') ? "<span class='description'>$desc</span>" : "";
				break;
				case 'text':  
					$options[$id] = stripslashes($options[$id]);  
					$options[$id] = esc_attr( $options[$id]);  
					echo "<input class='regular-text$field_class' type='text' id='$id' name='" . $wpajax_option_name . "[$id]' value='$options[$id]' $field_attr />";  
					echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
				break;  
		  
				case "multi-text":  
					foreach($choices as $item) {  
						$item = explode("|",$item); // cat_name|cat_slug  
						$item[0] = esc_html__($item[0], WP_AJAX_TEXTDOMAIN);  
						if (!empty($options[$id])) {  
							foreach ($options[$id] as $option_key => $option_val){  
								if ($item[1] == $option_key) {  
									$value = $option_val;  
								}  
							}  
						} else {  
							$value = '';  
						}  
						echo "<span>$item[0]:</span> <input class='$field_class' type='text' id='$id|$item[1]' name='" . $wpajax_option_name . "[$id|$item[1]]' value='$value' $field_attr /><br/>";  
					}  
					echo ($desc != '') ? "<span class='description'>$desc</span>" : "";  
				break;  
		  
				case 'textarea':  
					$options[$id] = stripslashes($options[$id]);  
					$options[$id] = esc_html( $options[$id]);  
					echo "<textarea class='textarea$field_class' type='text' id='$id' name='" . $wpajax_option_name . "[$id]' rows='10' cols='80' $field_attr>$options[$id]</textarea>";  
					echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
				break;  
		  
				case 'select':  
					echo "<select id='$id' class='select$field_class' name='" . $wpajax_option_name . "[$id]' $field_attr >";  
						foreach($choices as $item) {  
							$value  = esc_attr($item, WP_AJAX_TEXTDOMAIN);  
							$item   = esc_html($item, WP_AJAX_TEXTDOMAIN);  
		  
							$selected = ($options[$id]==$value) ? 'selected="selected"' : '';  
							echo "<option value='$value' $selected>$item</option>";  
						}  
					echo "</select>";  
					echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
				break;  
		  
				case 'select2':  
					echo "<select id='$id' class='select$field_class' name='" . $wpajax_option_name . "[$id]' $field_attr >";  
					foreach($choices as $item) {  
		  
						$item = explode("|",$item);  
						$item[0] = esc_html($item[0], WP_AJAX_TEXTDOMAIN);  
		  
						$selected = ($options[$id]==$item[1]) ? 'selected="selected"' : '';  
						echo "<option value='$item[1]' $selected>$item[0]</option>";  
					}  
					echo "</select>";  
					echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
				break;  
		  
				case 'checkbox':  
					echo "<input class='checkbox$field_class' type='checkbox' id='$id' name='" . $wpajax_option_name . "[$id]' value='1' " . checked( $options[$id], 1, false ) . " $field_attr />";  
					echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
				break;  
		  
				case "multi-checkbox":  
					foreach($choices as $item) {  
		  
						$item = explode("|",$item);  
						$item[0] = esc_html($item[0], WP_AJAX_TEXTDOMAIN);  
		  
						$checked = '';  
		  
						if ( isset($options[$id][$item[1]]) ) {  
							if ( $options[$id][$item[1]] == 'true') {  
								$checked = 'checked="checked"';  
							}  
						}  
		  
						echo "<input class='checkbox$field_class' type='checkbox' id='$id|$item[1]' name='" . $wpajax_option_name . "[$id|$item[1]]' value='1' $checked $field_attr /> $item[0] <br/>";  
					}  
					echo ($desc != '') ? "<br /><span class='description'>$desc</span>" : "";  
				break;  
			}  
		}
		function wpajax_validate_options($input) {  
			// for enhanced security, create a new empty array  
			$valid_input = array();  
		  
			// collect only the values we expect and fill the new $valid_input array i.e. whitelist our option IDs  
		  
				// get the settings sections array  
				$settings_output = $this->wpajax_get_settings();  
		  
				$options = $settings_output['wp-ajax_page_fields'];  
		  
				// run a foreach and switch on option type  
				foreach ($options as $option) {  
		  
					switch ( $option['type'] ) {  
						case 'text':  
							//switch validation based on the class!  
							switch ( $option['class'] ) {  
								//for numeric  
								case 'numeric':  
									//accept the input only when numeric!  
									$input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
									$valid_input[$option['id']] = (is_numeric($input[$option['id']])) ? $input[$option['id']] : 'Expecting a Numeric value!';  
		  
									// register error  
									if(is_numeric($input[$option['id']]) == FALSE) {  
										add_settings_error(  
											$option['id'], // setting title  
											wpajax_SHORTNAME . '_txt_numeric_error', // error ID  
											__('Expecting a Numeric value! Please fix.',WP_AJAX_TEXTDOMAIN), // error message  
											'error' // type of message  
										);  
									}  
								break;  
		  
								//for multi-numeric values (separated by a comma)  
								case 'multinumeric':  
									//accept the input only when the numeric values are comma separated  
									$input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
		  
									if($input[$option['id']] !=''){  
										// /^-?\d+(?:,\s?-?\d+)*$/ matches: -1 | 1 | -12,-23 | 12,23 | -123, -234 | 123, 234  | etc.  
										$valid_input[$option['id']] = (preg_match('/^-?\d+(?:,\s?-?\d+)*$/', $input[$option['id']]) == 1) ? $input[$option['id']] : __('Expecting comma separated numeric values',WP_AJAX_TEXTDOMAIN);  
									}else{  
										$valid_input[$option['id']] = $input[$option['id']];  
									}  
		  
									// register error  
									if($input[$option['id']] !='' && preg_match('/^-?\d+(?:,\s?-?\d+)*$/', $input[$option['id']]) != 1) {  
										add_settings_error(  
											$option['id'], // setting title  
											WP_AJAX_SHORTNAME . '_txt_multinumeric_error', // error ID  
											__('Expecting comma separated numeric values! Please fix.',WP_AJAX_TEXTDOMAIN), // error message  
											'error' // type of message  
										);  
									}  
								break;  
		  
								//for no html  
								case 'nohtml':  
									//accept the input only after stripping out all html, extra white space etc!  
									$input[$option['id']]       = sanitize_text_field($input[$option['id']]); // need to add slashes still before sending to the database  
									$valid_input[$option['id']] = addslashes($input[$option['id']]);  
								break;  
		  
								//for url  
								case 'url':  
									//accept the input only when the url has been sanited for database usage with esc_url_raw()  
									$input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
									$valid_input[$option['id']] = esc_url_raw($input[$option['id']]);  
								break;  
		  
								//for email  
								case 'email':  
									//accept the input only after the email has been validated  
									$input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
									if($input[$option['id']] != ''){  
										$valid_input[$option['id']] = (is_email($input[$option['id']])!== FALSE) ? $input[$option['id']] : __('Invalid email! Please re-enter!',WP_AJAX_TEXTDOMAIN);  
									}elseif($input[$option['id']] == ''){  
										$valid_input[$option['id']] = __('This setting field cannot be empty! Please enter a valid email address.',WP_AJAX_TEXTDOMAIN);  
									}  
		  
									// register error  
									if(is_email($input[$option['id']])== FALSE || $input[$option['id']] == '') {  
										add_settings_error(  
											$option['id'], // setting title  
											WP_AJAX_SHORTNAME . '_txt_email_error', // error ID  
											__('Please enter a valid email address.',WP_AJAX_TEXTDOMAIN), // error message  
											'error' // type of message  
										);  
									}  
								break;  
		  
								// a "cover-all" fall-back when the class argument is not set  
								default:  
									// accept only a few inline html elements  
									$allowed_html = array(  
										'a' => array('href' => array (),'title' => array ()),  
										'b' => array(),  
										'em' => array (),  
										'i' => array (),  
										'strong' => array()  
									);  
		  
									$input[$option['id']]       = trim($input[$option['id']]); // trim whitespace  
									$input[$option['id']]       = force_balance_tags($input[$option['id']]); // find incorrectly nested or missing closing tags and fix markup  
									$input[$option['id']]       = wp_kses( $input[$option['id']], $allowed_html); // need to add slashes still before sending to the database  
									$valid_input[$option['id']] = addslashes($input[$option['id']]);  
								break;  
							}  
						break;  
		  
						case "multi-text":  
							// this will hold the text values as an array of 'key' => 'value'  
							unset($textarray);  
		  
							$text_values = array();  
							foreach ($option['choices'] as $k => $v ) {  
								// explode the connective  
								$pieces = explode("|", $v);  
		  
								$text_values[] = $pieces[1];  
							}  
		  
							foreach ($text_values as $v ) {       
		  
								// Check that the option isn't empty  
								if (!empty($input[$option['id'] . '|' . $v])) { 
									// If it's not null, make sure it's sanitized, add it to an array 
									switch ($option['class']) { 
										// different sanitation actions based on the class create you own cases as you need them 
		 
										//for numeric input 
										case 'numeric': 
											//accept the input only if is numberic! 
											$input[$option['id'] . '|' . $v]= trim($input[$option['id'] . '|' . $v]); // trim whitespace 
											$input[$option['id'] . '|' . $v]= (is_numeric($input[$option['id'] . '|' . $v])) ? $input[$option['id'] . '|' . $v] : ''; 
										break; 
		 
										// a "cover-all" fall-back when the class argument is not set 
										default: 
											// strip all html tags and white-space. 
											$input[$option['id'] . '|' . $v]= sanitize_text_field($input[$option['id'] . '|' . $v]); // need to add slashes still before sending to the database 
											$input[$option['id'] . '|' . $v]= addslashes($input[$option['id'] . '|' . $v]); 
										break; 
									} 
									// pass the sanitized user input to our $textarray array 
									$textarray[$v] = $input[$option['id'] . '|' . $v]; 
		 
								} else { 
									$textarray[$v] = ''; 
								} 
							} 
							// pass the non-empty $textarray to our $valid_input array 
							if (!empty($textarray)) { 
								$valid_input[$option['id']] = $textarray; 
							} 
						break; 
		 
						case 'textarea':  
							//switch validation based on the class!  
							switch ( $option['class'] ) { 
								//for only inline html 
								case 'inlinehtml':
									// accept only inline html 
									$input[$option['id']]       = trim($input[$option['id']]); // trim whitespace 
									$input[$option['id']]       = force_balance_tags($input[$option['id']]); // find incorrectly nested or missing closing tags and fix markup 
									$input[$option['id']]       = addslashes($input[$option['id']]); //wp_filter_kses expects content to be escaped! 
									$valid_input[$option['id']] = wp_filter_kses($input[$option['id']]); //calls stripslashes then addslashes 
								break; 
		 
								//for no html 
								case 'nohtml': 
									//accept the input only after stripping out all html, extra white space etc! 
									$input[$option['id']]       = sanitize_text_field($input[$option['id']]); // need to add slashes still before sending to the database 
									$valid_input[$option['id']] = addslashes($input[$option['id']]); 
								break; 
		 
								//for allowlinebreaks 
								case 'allowlinebreaks': 
									//accept the input only after stripping out all html, extra white space etc! 
									$input[$option['id']]       = wp_strip_all_tags($input[$option['id']]); // need to add slashes still before sending to the database 
									$valid_input[$option['id']] = addslashes($input[$option['id']]); 
								break; 
		 
								// a "cover-all" fall-back when the class argument is not set 
								default: 
									// accept only limited html 
									//my allowed html 
									$allowed_html = array( 
										/*'a'             => array('href' => array (),'title' => array ()), 
										'b'             => array(), 
										'blockquote'    => array('cite' => array ()), 
										'br'            => array(), 
										'dd'            => array(),  
										'dl'            => array(), 
										'dt'            => array(),  
										'em'            => array (), 
										'i'             => array (), 
										'li'            => array(),  
										'ol'            => array(), 
										'p'             => array(), 
										'q'             => array('cite' => array ()), 
										'strong'        => array(), 
										'ul'            => array(), 
										'h1'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
										'h2'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
										'h3'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
										'h4'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
										'h5'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()), 
										'h6'            => array('align' => array (),'class' => array (),'id' => array (), 'style' => array ()),*/
										'div'			=> array('class' => array (), 'id' => array ()),
										'span'			=> array('class' => array (), 'id' => array ())
									); 
		 
									$input[$option['id']]       = trim($input[$option['id']]); // trim whitespace 
									$input[$option['id']]       = force_balance_tags($input[$option['id']]); // find incorrectly nested or missing closing tags and fix markup 
									$input[$option['id']]       = wp_kses( $input[$option['id']], $allowed_html); // need to add slashes still before sending to the database 
									$valid_input[$option['id']] = addslashes($input[$option['id']]); 
								break; 
							} 
						break; 
		 
						case 'select': 
							// check to see if the selected value is in our approved array of values! 
							$valid_input[$option['id']] = (in_array( $input[$option['id']], $option['choices']) ? $input[$option['id']] : '' ); 
						break; 
		 
						case 'select2': 
							// process $select_values 
								$select_values = array(); 
								foreach ($option['choices'] as $k => $v) { 
									// explode the connective 
									$pieces = explode("|", $v); 
		 
									$select_values[] = $pieces[1]; 
								} 
							// check to see if selected value is in our approved array of values! 
							$valid_input[$option['id']] = (in_array( $input[$option['id']], $select_values) ? $input[$option['id']] : '' ); 
						break; 
		 
						case 'checkbox':  
							// if it's not set, default to null!  
							if (!isset($input[$option['id']])) {  
								$input[$option['id']] = null;  
							}  
							// Our checkbox value is either 0 or 1  
							$valid_input[$option['id']] = ( $input[$option['id']] == 1 ? 1 : 0 );  
						break;  
		  
						case 'multi-checkbox':  
							unset($checkboxarray);  
							$check_values = array();  
							foreach ($option['choices'] as $k => $v ) {  
								// explode the connective  
								$pieces = explode("|", $v);  
		  
								$check_values[] = $pieces[1];  
							}  
		  
							foreach ($check_values as $v ) {          
		  
								// Check that the option isn't null  
								if (!empty($input[$option['id'] . '|' . $v])) { 
									// If it's not null, make sure it's true, add it to an array 
									$checkboxarray[$v] = 'true'; 
								} 
								else { 
									$checkboxarray[$v] = 'false'; 
								} 
							} 
							// Take all the items that were checked, and set them as the main option 
							if (!empty($checkboxarray)) { 
								$valid_input[$option['id']] = $checkboxarray;  
							}  
						break;
						
						case 'hidden':
							$valid_input[$option['id']] = $input[$option['id']];
						break;
		  
					}  
				}
		return $valid_input; // return validated input  
		}
		/** 
		 * Helper function for creating admin messages 
		 * src: http://www.wprecipes.com/how-to-show-an-urgent-message-in-the-wordpress-admin-area 
		 * 
		 * @param (string) $message The message to echo 
		 * @param (string) $msgclass The message class 
		 * @return echoes the message 
		 */  
		function wpajax_show_msg($message, $msgclass = 'info') {
			echo "<div id='message' class='$msgclass'><p>$message</p></div>";
		}
		 /** 
		 * Callback function for displaying admin messages 
		 * 
		 * @return calls wpajax_show_msg() 
		 */  
		function wpajax_admin_msgs() {
			// check for our settings page - need this in conditional further down  
			$wpajax_settings_pg = strpos($_GET['page'], WP_AJAX_PAGE_BASENAME);  
			// collect setting errors/notices: //http://codex.wordpress.org/Function_Reference/get_settings_errors  
			$set_errors = get_settings_errors();   
		  
			//display admin message only for the admin to see, only on our settings page and only when setting errors/notices are returned!  
			if(current_user_can ('manage_options') && $wpajax_settings_pg !== FALSE && !empty($set_errors)){
		  
				// have our settings succesfully been updated?  
				if($set_errors[0]['code'] == 'settings_updated' && isset($_GET['settings-updated'])){  
					$this->wpajax_show_msg("<p>" . $set_errors[0]['message'] . "</p>", 'updated');  
		  
				// have errors been found?  
				}else{  
					// there maybe more than one so run a foreach loop.  
					foreach($set_errors as $set_error){
						// set the title attribute to match the error "setting title" - need this in js file  
						$this->wpajax_show_msg("<p class='setting-error-message' title='" . $set_error['setting'] . "'>" . $set_error['message'] . "</p>", 'error');  
					}  
				}  
			}  
		}
		function wpajax_global_admin_msgs() {
			$wpajax_options = WPAjaxUtils::wpajax_get_global_options();
			$loading_test_mode = $wpajax_options[WP_AJAX_SHORTNAME . "_loading_test_mode"];
			// Add global Error / Warning Messages
			if ($loading_test_mode) {
				$this->wpajax_show_msg(__('WP Ajax Warning : Loading animation test mode activated, no wonder if nothings seems to work except the loading animation ! ', WP_AJAX_TEXTDOMAIN), 'error', false );
			}
		}
		function wpajax_num_cache_files() {
			if (glob(WP_AJAX_CACHE_DIR . "*.php") != false)
			{
				return count(glob(WP_AJAX_CACHE_DIR . "*.php"));
			}
			return 0;
		}
		function wpajax_get_transition_options() {
			return $this->wpajax_get_transitions();
		}
		function wpajax_get_transitions() {
			$transitions = array();
			
			$dir = WP_AJAX_TRANSITIONS_DIR . '/';

			// Open a known directory, and proceed to read its contents
			foreach(glob($dir.'*') as $file) 
			{
				if (filetype($file)=='dir') {
					$trans = end((explode('/', $file)));
					if (file_exists($file.'/'.$trans.'.css')) {
						array_push($transitions, __($trans,WP_AJAX_TEXTDOMAIN) . "|" . $trans);
					}
				}
			}
			return $transitions;
		}
		function wpajax_get_loading_options() {
			return $this->wpajax_get_loadings();
		}
		function wpajax_get_loadings() {
			$loadings = array();
			
			$dir = WP_AJAX_LOADING_DIR . '/';

			// Open a known directory, and proceed to read its contents
			foreach(glob($dir.'*') as $file) 
			{
				if (filetype($file)=='dir') {
					$loading = end((explode('/', $file)));
					if (file_exists($file.'/'.$loading.'.html')) {
						array_push($loadings, __($loading,WP_AJAX_TEXTDOMAIN) . "|" . $loading);
					}
				}
			}
			return $loadings;
		}
		function wpajax_get_loading_position_options() {
			return array(__('Top Left', WP_AJAX_TEXTDOMAIN) . '|topleft', __('Top', WP_AJAX_TEXTDOMAIN) . '|top', __('Top Right', WP_AJAX_TEXTDOMAIN) . '|topright',
				__('Left', WP_AJAX_TEXTDOMAIN) . '|left', __('Center', WP_AJAX_TEXTDOMAIN) . '|center', __('Right', WP_AJAX_TEXTDOMAIN) . '|right',
				__('Bottom Left', WP_AJAX_TEXTDOMAIN) . '|bottomleft', __('Bottom', WP_AJAX_TEXTDOMAIN) . '|bottom', __('Bottom Right', WP_AJAX_TEXTDOMAIN) . '|bottomright');
		}
	}
}
global $wpajaxsettings;
if (!isset($wpajaxsettings)) {
	$wpajaxsettings = new WPAjaxSettings();
}
