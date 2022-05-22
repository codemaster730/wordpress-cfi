<?php

/**
 * Control front-end editor for KB page configuration
 */
class EPKB_Editor_Controller {

	function __construct() {
		add_action( 'wp_ajax_eckb_apply_editor_changes', array( $this, 'apply_editor_changes' ) );
		add_action( 'wp_ajax_epkb_editor_error', array( $this, 'ajax_epkb_editor_error' ) );
		add_action( 'wp_ajax_eckb_editor_get_themes_list',  array( $this, 'get_themes' ));
		
		add_action( 'wp_ajax_nopriv_eckb_apply_editor_changes', array( $this, 'apply_editor_changes_unauthorized' ) );
	}

	/**
	 * User clicked to Save their frontend changes
	 */
	public function apply_editor_changes() {

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce_apply_editor_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_editor_changes'], '_wpnonce_apply_editor_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		// ensure that user has correct permissions
		if ( ! is_admin() || ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		// get current KB ID
		$editor_kb_id = EPKB_Utilities::post( 'epkb_editor_kb_id' );
		if ( empty($editor_kb_id) || ! EPKB_Utilities::is_positive_int( $editor_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid editor id parameter. Please refresh your page', 'echo-knowledge-base' ) );
		}

		// get type of page we are saving
		$page_type = EPKB_Utilities::post( 'page_type' );
		if ( empty($page_type) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid post type parameter. Please refresh your page', 'echo-knowledge-base' ) );
		}

		// get new KB configuration
		$new_config = EPKB_Utilities::post( 'kb_config', array(), false );
		if ( empty($new_config) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid post parameters. Please refresh your page', 'echo-knowledge-base' ) );
		}

		// split plugin settings from KB configuration
		$settings = array_intersect_key( $new_config, EPKB_Settings_Specs::get_specs_item_name_keys() );
		$new_config = array_diff_assoc( $new_config, $settings );

		// get current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $editor_kb_id );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please contact us.', 'echo-knowledge-base' ) . $orig_config->get_error_message() . '(8)' );
		}

		// get current KB configuration from add-ons
		$orig_config = apply_filters( 'eckb_all_editors_get_current_config', $orig_config, $editor_kb_id );
		if ( empty($orig_config) || count($orig_config) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters (111). Please refresh your page', 'echo-knowledge-base' ) );
		}

		// get current Plugin settings
		$orig_settings = epkb_get_instance()->settings_obj->get_settings();
		if ( is_wp_error( $orig_settings ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please contact us.', 'echo-knowledge-base' ) . $orig_config->get_error_message() . '(85)' );
		}

		// overwrite current KB configuration with new configuration from this editor
		$new_config = array_merge($orig_config, $new_config);
		$settings = array_merge($orig_settings, $settings);

		// save based on type of page
		switch( $page_type ) {
			case 'main-page':
				$this->update_main_page( $editor_kb_id, $orig_config, $new_config, $settings );
				break;
			case 'article-page':
				$this->update_article_page( $editor_kb_id, $orig_config, $new_config, $settings );
				break;
			case 'archive-page':
				$this->update_archive_page( $editor_kb_id, $orig_config, $new_config, $settings );
				break;
			default:
				EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please contact us.', 'echo-knowledge-base' ) . $orig_config->get_error_message() . '(81)' );
		}

		$message = __('Configuration Saved', 'echo-knowledge-base');
		wp_die( json_encode( array( 'message' => $message, 'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Save KB Main Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @param $settings
	 */
	private function update_main_page( $editor_kb_id, $orig_config, $new_config, $settings ) {

		// if user switches layout then ensure the sidebar is set correctly; $orig_config is used to overwrite filter
		if ( $orig_config['kb_main_page_layout'] != $new_config['kb_main_page_layout'] ) {

			// when user is selecting theme preset we do not want to apply common config
			$apply_common_config = empty($new_config['theme_presets']) || $new_config['theme_presets'] == 'current';
			$new_config = self::reset_layout( $orig_config, $new_config, $apply_common_config );

			// filtering will use orig_config values for component priority
			$orig_config['article_sidebar_component_priority']['categories_left'] = $new_config['article_sidebar_component_priority']['categories_left'];
			$orig_config['article_sidebar_component_priority']['categories_right'] = $new_config['article_sidebar_component_priority']['categories_right'];
			$orig_config['article_sidebar_component_priority']['elay_sidebar_left'] = $new_config['article_sidebar_component_priority']['elay_sidebar_left'];
		}

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];

		// detect that preset was selected and add icons/images if user did not defined one
		$chosen_preset = empty($new_config['theme_presets']) || $new_config['theme_presets'] == 'current' ? '' : $new_config['theme_presets'];
		$new_config['theme_name'] = $chosen_preset;
		if ( ! empty($chosen_preset) ) {
			$this->update_category_icons( $new_config, $chosen_preset );
		}

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $editor_kb_id, $orig_config, $new_config, $settings );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(3) ' . $update_kb_msg . EPKB_Utilities::contact_us_for_support() );
		}
	}

	/**
	 * Save KB Article Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @param $settings
	 */
	private function update_article_page( $editor_kb_id, $orig_config, $new_config, $settings ) {

		// set sidebar priority
		$article_sidebar_component_priority = array();
		foreach( self::get_sidear_component_priority() as $component ) {
			if ( isset($new_config[$component]) ) {
				$article_sidebar_component_priority[$component] = $new_config[$component];
			}
		}
		// sanitize sidebar
		foreach( $article_sidebar_component_priority as $key => $value ) {
			if ( ! in_array($key, EPKB_KB_Config_Specs::get_sidebar_component_priority_names() ) ) {
				unset($article_sidebar_component_priority[$key]);
			}
			$article_sidebar_component_priority[$key] = sanitize_text_field($value);
		}

		$article_sidebar_component_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $article_sidebar_component_priority );
		$new_config['article_sidebar_component_priority'] = $article_sidebar_component_priority;

		$new_config = self::reset_sidebar_widths( $new_config );

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $editor_kb_id, $orig_config, $new_config, $settings );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(31) ' . $update_kb_msg . EPKB_Utilities::contact_us_for_support() );
		}

		epkb_get_instance()->kb_config_obj->set_value( $orig_config['id'], 'article_sidebar_component_priority', $article_sidebar_component_priority );
	}

	/**
	 * Save KB Archive Page configuration
	 *
	 * @param $editor_kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @param $settings
	 */
	private function update_archive_page( $editor_kb_id, $orig_config, $new_config, $settings ) {

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $editor_kb_id, $orig_config, $new_config, $settings );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(32) ' . $update_kb_msg . EPKB_Utilities::contact_us_for_support() );
		}
	}

	/**
	 * Triggered when user submits changes to KB configuration
	 *
	 * @param $kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @param $settings
	 * @return string
	 */
	private function update_kb_configuration( $kb_id, $orig_config, $new_config, $settings ) {

		// core handles only default KB
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			return __('Ensure that Multiple KB add-on is active and refresh this page', 'echo-knowledge-base');
		}

		// sanitize all fields in POST message
		$field_specs = EPKB_KB_Config_Controller::retrieve_all_kb_specs( $kb_id );
		// get Plugin settings specs
		$plugin_field_specs = EPKB_Settings_Specs::get_fields_specification();
		$field_specs = array_merge($plugin_field_specs, $field_specs);

		$form_fields = EPKB_Utilities::retrieve_and_sanitize_form( $new_config, $field_specs );
		if ( empty($form_fields) ) {
			EPKB_Logging::add_log("form fields missing");
			return __( 'Form fields missing. Please refresh your browser', 'echo-knowledge-base' );
		} else if ( count($form_fields) < 100 ) {
			return __( 'Some form fields are missing. Please refresh your browser and try again or contact support', 'echo-knowledge-base' );
		}

		// sanitize fields based on each field type
		$input_handler = new EPKB_Input_Filter();
		$new_kb_config = $input_handler->retrieve_and_sanitize_form_fields( $form_fields, $field_specs, $orig_config );

		// save add-ons configuration
		$result = apply_filters( 'epkb_kb_config_save_input_v2', '', $kb_id, $form_fields, $new_kb_config['kb_main_page_layout'] );
		if ( is_wp_error( $result ) ) { 
			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(4)';
			} else {
				return __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' );
			}
		}

		// ensure kb id is preserved
		$new_kb_config['id'] = $kb_id;

		// save KB core configuration
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_kb_config );
		if ( is_wp_error( $result ) ) {

			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(31)';
			} else {
				return __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' );
			}
		}

		// save Plugin settings
		$result = epkb_get_instance()->settings_obj->update_settings( $settings );
		if ( is_wp_error( $result ) ) {

			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return __( 'Could not save the new Plugin settings', 'echo-knowledge-base' ) . '(32)';
			} else {
				return __( 'Plugin settings NOT saved due to following problem:' . $message, 'echo-knowledge-base' );
			}
		}
		
		// we are done here
		return '';
	}

	public static function reset_sidebar_widths( $new_config ) {
		
		$is_left_sidebar_on = EPKB_Articles_Setup::is_left_sidebar_on( $new_config );
		$is_right_sidebar_on = EPKB_Articles_Setup::is_right_sidebar_on( $new_config );
		
		$left_sidebar_width = 0;
		
		if ( $is_left_sidebar_on ) {
			$left_sidebar_width = $new_config['article-left-sidebar-desktop-width-v2'] ? $new_config['article-left-sidebar-desktop-width-v2'] : 20;
		}
		
		$right_sidebar_width = 0;
		
		if ( $is_right_sidebar_on ) {
			$right_sidebar_width = $new_config['article-right-sidebar-desktop-width-v2'] ? $new_config['article-right-sidebar-desktop-width-v2'] : 20;
		}
		
		$content_width = 100 - $left_sidebar_width - $right_sidebar_width;
		
		$new_config['article-left-sidebar-desktop-width-v2'] = $left_sidebar_width;
		$new_config['article-left-sidebar-tablet-width-v2'] = $left_sidebar_width;
		$new_config['article-right-sidebar-desktop-width-v2'] = $right_sidebar_width;
		$new_config['article-right-sidebar-tablet-width-v2'] = $right_sidebar_width;
		$new_config['article-content-desktop-width-v2'] = $content_width;
		$new_config['article-content-tablet-width-v2'] = $content_width;

		return $new_config;
	}

	public static function reset_layout( $orig_config, $new_config, $apply_common_config = true ) {

		$from_layout = $orig_config['kb_main_page_layout'];
		$to_layout = $new_config['kb_main_page_layout'];

		// 1. from old layout operation
		switch ( $from_layout ) {
			case 'Basic':
			case 'Tabs':
				$new_config['article_sidebar_component_priority']['elay_sidebar_left'] = 0;
				break;
			case 'Categories':
				$new_config['article_sidebar_component_priority']['categories_left'] = 0;
				$new_config['article_sidebar_component_priority']['categories_right'] = 0;
				break;
			case 'Grid':
				$new_config['article_sidebar_component_priority']['elay_sidebar_left'] = 0;
				break;
			case 'Sidebar':
				$new_config['article_sidebar_component_priority']['elay_sidebar_left'] = 0;
				break;
		}

		// 2. to new layout operation
		switch ( $to_layout ) {
			case 'Basic':
			case 'Tabs':
				$new_config['archive-content-width-v2'] = 100;
				if ( EPKB_Utilities::is_elegant_layouts_enabled() ) {
					$new_config['article-left-sidebar-toggle'] = 'on';
					$new_config['archive-content-width-v2'] = 80;
					$new_config['article_sidebar_component_priority'] = self::level_up_sidebar_priorities( $new_config['article_sidebar_component_priority'], 'elay_sidebar_left' );
					$new_config['article_sidebar_component_priority']['elay_sidebar_left'] = 1;
				}

				$new_config = self::copy_settings_from_grid( $from_layout, $orig_config, $new_config, $apply_common_config );

				break;
			case 'Categories':
				$new_config['article-left-sidebar-toggle'] = 'on';
				$new_config['archive-content-width-v2'] = 80;
				$new_config['article_sidebar_component_priority'] = self::level_up_sidebar_priorities( $new_config['article_sidebar_component_priority'], 'categories_left' );
				$new_config['article_sidebar_component_priority']['categories_left'] = 1;
				
				$new_config = self::copy_settings_from_grid( $from_layout, $orig_config, $new_config, $apply_common_config );

				break;
			case 'Grid':
				$new_config['article-left-sidebar-toggle'] = 'on';
				$new_config['archive-content-width-v2'] = 100;
				$new_config['article_sidebar_component_priority'] = self::level_up_sidebar_priorities( $new_config['article_sidebar_component_priority'], 'elay_sidebar_left' );
				$new_config['article_sidebar_component_priority']['elay_sidebar_left'] = 1;
				
				$new_config = self::copy_settings_from_basic( $from_layout, $orig_config, $new_config, $apply_common_config );

				break;
			case 'Sidebar':
				$new_config['article-left-sidebar-toggle'] = 'on';
				$new_config['archive-content-width-v2'] = 100;
				$new_config['article_sidebar_component_priority'] = self::level_up_sidebar_priorities( $new_config['article_sidebar_component_priority'], 'elay_sidebar_left' );
				$new_config['article_sidebar_component_priority']['elay_sidebar_left'] = 1;
				break;
		}

		// 3. cleanup
		// remove empty sidebars
		$sidebar_priority = $new_config['article_sidebar_component_priority'];
		if ( empty($sidebar_priority['toc_left']) && empty($sidebar_priority['kb_sidebar_left']) && empty($sidebar_priority['categories_left']) && empty($sidebar_priority['elay_sidebar_left']) ) {
			$new_config['article-left-sidebar-toggle'] = 'off';
		}
		if ( empty($sidebar_priority['toc_right']) && empty($sidebar_priority['kb_sidebar_right']) && empty($sidebar_priority['categories_right']) ) {
			$new_config['article-right-sidebar-toggle'] = 'off';
		}

		// recalculate width
		$new_config = self::reset_sidebar_widths( $new_config );

		return $new_config;
	}

	/** Move all sidebar priorities on level up
	 * @param $sidebar_component_priority
	 * @param $first_panel
	 * @return mixed
	 */
	public static function level_up_sidebar_priorities( $sidebar_component_priority, $first_panel ) {

		// get all panels that are visible
		$ordered_list = array();
		foreach( $sidebar_component_priority as $panel => $level ) {
			if ( $panel == $first_panel || $level == 0 || strpos($panel, '_left' ) === false ) {
				continue;
			}
			$ordered_list += array( $panel => $level );
		}

		// sort them
		asort($ordered_list);

		// assign order
		$ix = 2;
		foreach( $ordered_list as $panel => $priority ) {
			$sidebar_component_priority[$panel] = $ix++;
		}

		return $sidebar_component_priority;
	}
	
	private static function copy_settings_from_basic( $from_layout, $orig_config, $new_config, $apply_common_config ) {
		
		if ( ! $apply_common_config || ( $from_layout !== 'Basic' && $from_layout !== 'Tabs' && $from_layout !== 'Categories' ) ) {
			return $new_config;
		}
		
		$relation = array(
			'grid_section_head_icon_color' => 'section_head_category_icon_color',
			'grid_section_body_text_color' => 'section_category_font_color',
			'grid_section_border_radius' => 'section_border_radius',
			'grid_section_border_width' => 'section_border_width',
			'grid_section_border_color' => 'section_border_color',
			'grid_section_body_background_color' => 'section_body_background_color',
			'grid_section_head_background_color' => 'section_head_background_color',
			'grid_section_divider_color' => 'section_divider_color',
			'grid_section_head_font_color' => 'section_head_font_color',
			'grid_section_head_description_font_color' => 'section_head_description_font_color',
			'grid_category_empty_msg' => 'category_empty_msg',
		);
		
		foreach ( $relation as $from => $to ) {
			if ( isset( $orig_config[$to] ) ) {
				$new_config[$from] = $orig_config[$to];
			}
		}
		
		return $new_config;
	}
	
	private static function copy_settings_from_grid( $from_layout, $orig_config, $new_config, $apply_common_config ) {
		
		if ( $from_layout !== 'Grid' || ! $apply_common_config ) {
			return $new_config;
		}
		
		$relation = array(
			'section_head_category_icon_color' => 'grid_section_head_icon_color',
			'section_category_font_color' => 'grid_section_body_text_color',
			'section_border_radius' => 'grid_section_border_radius',
			'section_border_width' => 'grid_section_border_width',
			'section_border_color' => 'grid_section_border_color',
			'section_body_background_color' => 'grid_section_body_background_color',
			'section_head_background_color' => 'grid_section_head_background_color',
			'section_divider_color' => 'grid_section_divider_color',
			'section_head_font_color' => 'grid_section_head_font_color',
			'section_head_description_font_color' => 'grid_section_head_description_font_color',
			'category_empty_msg' => 'grid_category_empty_msg',
		);
		
		foreach ( $relation as $from => $to ) {
			if ( isset( $orig_config[$to] ) ) {
				$new_config[$from] = $orig_config[$to];
			}
		}
		
		return $new_config;
	}
	
	/**
	 * When reloading page after making changes in the Editor, populate KB config with the new values while rendering the page
	 * @param $kb_config
	 * @return mixed
	 */
	public static function filter_kb_config( $kb_config ) {

		// do not make any changes to config unless Editor is active
		if ( empty( $kb_config['id'] ) || empty( $_REQUEST['epkb-editor-page-loaded'] ) || empty( $_REQUEST['epkb-editor-settings'] ) || ( isset($_REQUEST['epkb-editor-kb-id']) && $kb_config['id'] != $_REQUEST['epkb-editor-kb-id'] ) ) {
			return $kb_config;
		}
	
		$orig_config = $kb_config;

		$new_kb_config = json_decode(stripcslashes($_REQUEST['epkb-editor-settings'] ), true);
		
		if ( ! empty ( $_REQUEST['epkb-editor-preset'] ) ) {
			
			$preset_fields = EPKB_KB_Wizard_Themes::$theme_fields;  // TODO replace with reading from kb and add-on configs get_all_configuration_defaults()
			$preset_values = json_decode(stripcslashes($_REQUEST['epkb-editor-preset'] ), true);
			foreach ( $preset_fields as $field_name ) {
				if ( ! isset( $preset_values[$field_name] ) || $preset_values[$field_name] === '' ) {
					continue;
				}
				
				$kb_config[$field_name] = $preset_values[$field_name];
				
			}		
		}
		
		foreach ( $new_kb_config as $zone_name => $zone ) {
			foreach ( $zone['settings'] as $field_name => $field ) {

				if ( ! isset( $field['value'] ) ) {
					continue;
				}

				// handle sidebar components priority differently TODO FUTURE remove if live preview instead of reload for sidebar config changes
				if ( EPKB_Editor_Base_Config::is_sidebar_priority( $field_name ) ) {
					$kb_config['article_sidebar_component_priority'][$field_name] = $field['value'];
				} else {
					$kb_config[$field_name] = $field['value'];
				}
			}
		}

		// update layouts if it changed
		if ( $orig_config['kb_main_page_layout'] != $kb_config['kb_main_page_layout'] ) {
			$kb_config = self::reset_layout( $orig_config, $kb_config );
		}
		
		return $kb_config;
	}

	private static function get_sidear_component_priority() {
		return ['elay_sidebar_left', 'toc_left', 'kb_sidebar_left', 'categories_left', 'toc_content', 'toc_right', 'kb_sidebar_right', 'categories_right'];
	}

	/**
	 * When reloading page after making changes in the Editor, populate Plugin settings with the new values while rendering the page
	 * @param $settings
	 * @return mixed
	 */
	public static function filter_plugin_settings( $settings ) {

		// do not make any changes to config unless Editor is active
		if ( empty( $_REQUEST['epkb-editor-page-loaded'] ) || empty( $_REQUEST['epkb-editor-settings'] ) ) {
			return $settings;
		}
		
		$new_kb_config = json_decode(stripcslashes($_REQUEST['epkb-editor-settings'] ), true);

		foreach ( $new_kb_config as $zone_name => $zone ) {
			foreach ( $zone['settings'] as $field_name => $field ) {
				if ( ! isset( $settings[$field_name] ) ) {
					continue;
				}

				$settings[$field_name] = $field['value'];
			}
		}

		return $settings;
	}

	/**
	 * User is submitting error form - submit for troubleshooting
	 */
	public function ajax_epkb_editor_error() {
		global $wp_version;

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], '_epkb_editor_submit_error_form_nonce' ) ) {
			wp_send_json_error( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		// ensure that user has correct permissions
		if ( ! is_admin() || ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			wp_send_json_error( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}

		$first_version = get_option( 'epkb_version_first' );
		$active_theme = wp_get_theme();
		$theme_info = $active_theme->get( 'Name' ) . ' ' . $active_theme->get( 'Version' );

		$email = EPKB_Utilities::post( 'email' );
		$email = empty($email) ? '[Email name is missing]' : substr( $email, 0, 50 );

		$first_name = EPKB_Utilities::post( 'first_name' );
		$first_name = empty($first_name) ? '[First name is missing]' : substr( $first_name, 0, 30 );

		$error = EPKB_Utilities::post( 'editor_error' );
		$error = empty($error) ? '[Editor error name is missing]' : substr( $error, 0, 5000 );

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );

		// send feedback
		$api_params = array(
			'epkb_action' => 'epkb_report_error',
			'plugin_name' => EPKB_Utilities::is_amag_on() ? 'Access Manager' : 'EPKB',
			'plugin_version' => class_exists( 'Echo_Knowledge_Base' ) ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version' => empty( $first_version ) ? 'N/A' : $first_version,
			'wp_version' => $wp_version,
			'theme_info' => $theme_info,
			'email' => $email,
			'first_name' => $first_name,
			'editor_error' => $error,
			'kb_main_page' => $kb_main_page_url
		);

		// Call the API
		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout' => 15,
				'body' => $api_params,
				'sslverify' => false
			)
		);

		// let user know if it succeeded
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			wp_send_json_success(__('We will get back to you soon.', 'echo-knowledge-base'));
		} else {
			wp_send_json_error(  __( 'Could not submit the error. ', 'echo-knowledge-base' ) . EPKB_Utilities::contact_us_for_support() );
		}
	}
	
	public static function get_themes() {

		// get current KB ID
		$editor_kb_id = EPKB_Utilities::post( 'epkb_editor_kb_id' );
		if ( empty($editor_kb_id) || ! EPKB_Utilities::is_positive_int( $editor_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid editor id parameter. Please refresh your page', 'echo-knowledge-base' ) );
		}

		// get current KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $editor_kb_id );
		if ( ! is_wp_error($kb_config) ) {
			$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
		}
		if ( empty($kb_config) || is_wp_error($kb_config) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please contact us.', 'echo-knowledge-base' ) . $kb_config->get_error_message() . '(18)' );
		}

		// get KB specs
		$field_specification = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );

		// get the plugin settings specs
		$plugin_field_specification = EPKB_Settings_Specs::get_fields_specification();

		$field_specification = array_merge($plugin_field_specification, $field_specification);

		// get add-ons specs
		$field_specification = apply_filters( 'eckb_editor_fields_specs', $field_specification, $kb_config['id'] );
		if ( empty($field_specification) || is_wp_error($field_specification) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Please contact us.', 'echo-knowledge-base' ) . $kb_config->get_error_message() . '(38)' );
		}

		// combine defaults with theme presets
		$theme_presets = [];
		
		foreach ( EPKB_KB_Wizard_Themes::get_all_presets( [] ) as $theme_name => $theme_values ) {
			foreach ( $theme_values as $name => $val ) {
				
				if ( $val === '' && ! empty ( $field_specification[$name] ) && isset(  $field_specification[$name]['default'] ) )  {
					$val = $field_specification[$name]['default'];
				} else if ( $val === '' ) {
					continue;
				}
				
				$theme_presets[$theme_name][$name] = $val;
			}
		}
		
		wp_send_json_success( [
			'theme_presets' => $theme_presets,
			'search_presets' => EPKB_KB_Wizard_Themes::get_search_presets(),
			'categories_presets' => EPKB_KB_Wizard_Themes::get_categories_presets(),
		] );
	}

	/**
	 * If user switches theme presets replace the icons intelligently i.e. replace all non-user defined icons.
	 * @param $new_config
	 * @param $chosen_preset
	 */
	private function update_category_icons( $new_config, $chosen_preset ) {

		$current_category_icons = EPKB_KB_Config_Category::get_category_icons_option( $new_config['id'] );
		$categories_icons_ids = array();
		foreach( $current_category_icons as $term_id => $categories_icon ) {
			$categories_icons_ids[] = $term_id;
		}

		// find and replace defaults with preset default image
		$kb_categories = EPKB_Categories_DB::get_top_level_categories( $new_config['id'] );
		if ( ! empty($kb_categories) && $new_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME ) {
			$kb_categories_child = array();
			foreach( $kb_categories as $kb_category ) {
				$child_categories = EPKB_Categories_DB::get_child_categories( $new_config['id'], $kb_category->term_id );
				foreach( $child_categories as $child_category ) {
					$kb_categories_child[] = $child_category;
				}
			}
			$kb_categories = $kb_categories_child;
		}

		$new_icon_type = EPKB_Icons::is_theme_with_image_icons( $new_config ) ? 'image' : 'font';

		$icons_updated = false;
		foreach ( $kb_categories as $kb_category ) {

			if ( empty($kb_category->term_id) ) {
				continue;
			}
			$term_id = $kb_category->term_id;
			if ( empty($current_category_icons[$term_id]) ) {
				continue;
			}

			// ignore icons that were set by user already
			$user_defined = true;
			$current_icon_type = $current_category_icons[$term_id]['type'];
			if ( ! in_array($term_id, $categories_icons_ids) ) {    // category icon is not in the database at all
				$user_defined = false;
			} else if ( $new_icon_type == 'font' ) {
				$user_defined = $current_icon_type == 'font' && ! in_array($current_category_icons[$term_id]['name'], array(EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME, 'epkbfa-book', 'ep_font_icon_gears', 'epkbfa-cube'));
			} else if ( $new_icon_type == 'image' ) {
				$user_defined = $current_icon_type == 'image' && strpos($current_category_icons[$term_id]['image_thumbnail_url'], 'img/demo-icons') == false &&
				                                                 strpos($current_category_icons[$term_id]['image_thumbnail_url'], 'www.echoknowledgebase.com') == false;
			}

			// do not change user-defined icons
			if ( $user_defined ) {
				continue;
			}

			// update category icon data
			$image_icon = array(
				'type' => $new_icon_type,
				'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
				'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
				'image_thumbnail_url' => EPKB_Icons::is_theme_with_image_icons( $new_config ) ?
											( Echo_Knowledge_Base::$plugin_url . ( EPKB_Icons::is_theme_with_photo_icons( $chosen_preset ) ? 'img/demo-icons/photos/photo-icon-example.jpg' :	EPKB_Icons::DEFAULT_IMAGE_SLUG ) ) :
											EPKB_Icons::DEFAULT_CATEGORY_ICON_NAME,
				'color' => '#000000'
			);
			$current_category_icons[$term_id] = $image_icon;
			$icons_updated = true;
		}

		if ( $icons_updated ) {
			EPKB_Utilities::save_kb_option( $new_config['id'], EPKB_Icons::CATEGORIES_ICONS, $current_category_icons, true );
		}
	}

	public function apply_editor_changes_unauthorized() {

		$link = sprintf( '<a href="%s">%s</a>', wp_login_url( empty( $_REQUEST['current_url'] ) ? '' : $_REQUEST['current_url'] ), __( 'here', 'echo-knowledge-base' ) );

		EPKB_Utilities::ajax_show_error_die( __( 'Your login has expired. Please log in.', 'echo-knowledge-base' ) . ' ' . __( 'To keep your changes, login through another browser tab and press Save again.', 'echo-knowledge-base' ) . ' ' . $link);
	}
}