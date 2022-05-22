<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Cntrl {

	function __construct() {
		add_action( 'wp_ajax_epkb_apply_wizard_changes', array( $this, 'apply_wizard_changes' ) );
		add_action( 'wp_ajax_epkb_wizard_update_order_view', array( $this, 'wizard_update_order_view' ) );
	}

	public function apply_wizard_changes() {

		// get Wizard type
		$wizard_type = EPKB_Utilities::post( 'wizard_type' );
		if ( empty( $wizard_type ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 164 ) );
		}

		// wp_die if nonce invalid or user does not have correct permission
		if ( $wizard_type == 'ordering' ) {
			EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_apply_wizard_changes', 'admin_eckb_access_order_articles_write' );
		} else if ( $wizard_type == 'global' ) {
			EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_apply_wizard_changes', 'admin_eckb_access_config_write' );
		} else {
			EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_apply_wizard_changes' );
		}

		// get current KB ID
		$wizard_kb_id = EPKB_Utilities::post( 'epkb_wizard_kb_id' );
		if ( empty($wizard_kb_id) || ! EPKB_Utilities::is_positive_int( $wizard_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 163 ) );
		}

		// get new KB template related configuration
		$new_config_post = EPKB_Utilities::post( 'kb_config', [], 'db-config' );
		if ( empty($new_config_post) || count($new_config_post) < 100 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 165 ) );
		}

		// get Wizard type specific filter
		switch( $wizard_type ) {
			case 'setup':
				$wizard_fields = apply_filters( 'epkb_kb_theme_fields_list', EPKB_KB_Wizard_Themes::$theme_fields );
				break;
			case 'ordering':
				$wizard_fields = EPKB_KB_Wizard_Ordering::$ordering_fields;
				break;
			case 'global':
				$wizard_fields = EPKB_KB_Wizard_Global::$global_fields;
				break;
			default:
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 166 ) );
				return;
		}

		// filter fields from Wizard to ensure we are saving only configuration that is applicable for this Wizard
		$new_config = array();
		foreach( $new_config_post as $field_name => $field_value ) {
			if ( in_array( $field_name, $wizard_fields ) ) {
				$new_config[$field_name] = $field_value;
			}
		}

		// get current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $wizard_kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
		}

		// get current Add-ons configuration
		$orig_config = apply_filters( 'epkb_all_wizards_get_current_config', $orig_config, $wizard_kb_id );
		if ( empty( $orig_config ) || count( $orig_config ) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 169, $orig_config ) );
		}

		// overwrite current KB configuration with new configuration from this Wizard
		$new_config = array_merge( $orig_config, $new_config );

		// call Wizard type specific saving function
		switch( $wizard_type ) {
			case 'setup':
				$this->apply_setup_wizard_changes( $wizard_kb_id, $orig_config, $new_config );
				break;
			case 'ordering':
				$this->apply_ordering_wizard_changes( $orig_config, $new_config );
				break;
			case 'global':
				$this->apply_global_wizard_changes( $orig_config, $new_config );
				break;
			default:
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 170 ) );
				return;
		}
	}

	/**
	 * Apply SETUP WIZARD changes
	 *
	 * @param $wizard_kb_id
	 * @param $orig_config
	 * @param $new_config
	 */
	private function apply_setup_wizard_changes( $wizard_kb_id, $orig_config, $new_config ) {

		// get and sanitize KB name
		$kb_name = EPKB_Utilities::post('kb_name');
		$kb_name = empty($kb_name) ? '' : substr( $kb_name, 0, 50 );
		$kb_name = sanitize_text_field($kb_name);
		if ( empty($kb_name) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 171 ) );
		}

		// if user selectes Image theme then change font icons to image icons
		if ( EPKB_Icons::is_theme_with_image_icons( $new_config ) ) {

			$categories_icons = EPKB_KB_Config_Category::get_category_icons_option( $wizard_kb_id );
			$categories_icons_ids = array();
			foreach( $categories_icons as $term_id => $categories_icon ) {
				$categories_icons_ids[] = $term_id;
			}

			$kb_categories = EPKB_Categories_DB::get_top_level_categories( $wizard_kb_id );
			foreach ( $kb_categories as $kb_category ) {
				$term_id = $kb_category->term_id;
				if ( in_array( $term_id, $categories_icons_ids) ) {
					$categories_icons[$term_id]['type'] = 'image';
					$categories_icons[$term_id]['image_thumbnail_url'] = empty($categories_icons[$term_id]['image_thumbnail_url']) ? Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG: $categories_icons[$term_id]['image_thumbnail_url'];
				} else {
					$image_icon = array(
						'type' => 'image',
						'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
						'image_size' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_SIZE,
						'image_thumbnail_url' => Echo_Knowledge_Base::$plugin_url . EPKB_Icons::DEFAULT_IMAGE_SLUG,
						'color' => '#000000'
					);
					$categories_icons[$term_id] = $image_icon;
				}
			}

			EPKB_Utilities::save_kb_option( $wizard_kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );
		}

		// set sidebar priority
		$article_sidebar_component_priority = EPKB_Utilities::post('article_sidebar_component_priority', []);
		if ( empty($article_sidebar_component_priority) || ! array( $article_sidebar_component_priority ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 172 ) );
		}

		// sanitize
		foreach( $article_sidebar_component_priority as $key => $value ) {
			if ( ! in_array($key, EPKB_KB_Config_Specs::get_sidebar_component_priority_names() ) ) {
				unset($article_sidebar_component_priority[$key]);
			}
			$article_sidebar_component_priority[$key] = sanitize_text_field($value);
		}

		$article_sidebar_component_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $article_sidebar_component_priority );
		$new_config['article_sidebar_component_priority'] = $article_sidebar_component_priority;

		// set TOC position from v2 article settings
		if ( $new_config['article-structure-version'] == 'version-2'
		     && $new_config['article_sidebar_component_priority'] !== $orig_config['article_sidebar_component_priority'] ) {
			if ( $new_config['article_sidebar_component_priority']['toc_left'] != '0' ) {
				$new_config['article_toc_position'] = 'left';
			} else if ( $new_config['article_sidebar_component_priority']['toc_right'] != '0' ) {
				$new_config['article_toc_position'] = 'right';
			} else if ( $new_config['article_sidebar_component_priority']['toc_content'] != '0' ) {
				$new_config['article_toc_position'] = 'middle';
			}
		}

		// auto-determine whether we need sidebar or let user override it to be displayed
		$is_left_sidebar_on = EPKB_Articles_Setup::is_left_sidebar_on( $new_config );
		$is_right_sidebar_on = EPKB_Articles_Setup::is_right_sidebar_on( $new_config );
		$new_config['article-left-sidebar-desktop-width-v2'] = $is_left_sidebar_on ? '20' : '0';
		$new_config['article-left-sidebar-tablet-width-v2']  = $is_left_sidebar_on ? '20' : '0';
		$new_config['article-content-desktop-width-v2'] = $is_left_sidebar_on && $is_right_sidebar_on ? '60' : '80';
		$new_config['article-content-tablet-width-v2'] = $is_left_sidebar_on && $is_right_sidebar_on ? '60' : '80';
		$new_config['article-right-sidebar-desktop-width-v2'] = $is_right_sidebar_on ? '20' : '0';
		$new_config['article-right-sidebar-tablet-width-v2'] = $is_right_sidebar_on ? '20' : '0';

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_name'] = $kb_name;
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $wizard_kb_id, $orig_config, $new_config );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 36, esc_html( $update_kb_msg ) ) );
		}

		// save priority
		epkb_get_instance()->kb_config_obj->set_value( $orig_config['id'], 'article_sidebar_component_priority', $article_sidebar_component_priority );

		// add items to menus if needs
		$menu_ids = EPKB_Utilities::post( 'menu_ids', [] );
		if ( $menu_ids && ! empty($new_config['kb_main_pages']) ) {
			$kb_main_pages = $new_config['kb_main_pages'];
			foreach ( $menu_ids as $id ) {
				$itemData =  array(
					'menu-item-object-id'   => key( $kb_main_pages ),
					'menu-item-parent-id'   => 0,
					'menu-item-position'    => 99,
					'menu-item-object'      => 'page',
					'menu-item-type'        => 'post_type',
					'menu-item-status'      => 'publish'
				  );

				wp_update_nav_menu_item( $id, 0, $itemData );
			}
		}

		// in case user changed article common path, flush the rules
		EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

		// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
		flush_rewrite_rules( false );
		update_option('epkb_flush_rewrite_rules', true);

		wp_die( json_encode( array(
			'message' => esc_html__('Configuration Saved', 'echo-knowledge-base'),
			'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Apply GLOBAL WIZARD changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 */
	private function apply_global_wizard_changes( $orig_config, $new_config ) {

		// make sure currently active KB Main Page is at the top of KB Main Pages list if the current KB has more than one Main Page
		$active_kb_main_page_id = EPKB_Utilities::post( 'kb_main_page_id' );
		if ( count( $orig_config['kb_main_pages'] ) > 1 && isset( $orig_config['kb_main_pages'][$active_kb_main_page_id] ) ) {
			$active_kb_main_page_title = $orig_config['kb_main_pages'][$active_kb_main_page_id];
			unset( $orig_config['kb_main_pages'][$active_kb_main_page_id] );
			$orig_config['kb_main_pages'] = array( $active_kb_main_page_id => $active_kb_main_page_title ) + $orig_config['kb_main_pages'];
		}

		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];

		// ensure the common path is always set
		$articles_common_path = empty( $new_config['kb_articles_common_path'] ) ? EPKB_KB_Handler::get_default_slug( $orig_config['id'] ) : $new_config['kb_articles_common_path'];

		// sanitize article path 
		$pieces = explode('/', $articles_common_path);
        $articles_common_path_out = '';
        $first_piece = true;
        foreach( $pieces as $piece ) {
            $articles_common_path_out .= ( $first_piece ? '' : '/' ) . urldecode(sanitize_title_with_dashes( $piece, '', 'save' ));
            $first_piece = false;
        }
		
		$new_config['kb_articles_common_path'] = $articles_common_path_out;

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty( $update_kb_msg ) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}

		// in case user changed article common path, flush the rules
		if ( $new_config['kb_articles_common_path'] != $orig_config['kb_articles_common_path'] || $new_config['categories_in_url_enabled'] != $orig_config['categories_in_url_enabled'] ) {
			EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

			// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
			flush_rewrite_rules( false );
			update_option( 'epkb_flush_rewrite_rules', true );

			EPKB_Admin_Notices::remove_ongoing_notice( 'epkb_changed_slug' );
		}

		wp_die( json_encode( array(
			'message' => esc_html__( 'Configuration Saved', 'echo-knowledge-base' ),
			'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Apply ORDERING changes
	 *
	 * @param $orig_config
	 * @param $new_config
	 */
	private function apply_ordering_wizard_changes( $orig_config, $new_config ) {
		
		// prevent new config to overwrite essential fields
		$new_config['id'] = $orig_config['id'];
		$new_config['status'] = $orig_config['status'];
		$new_config['kb_main_pages'] = $orig_config['kb_main_pages'];
		$new_config['kb_articles_common_path'] = $orig_config['kb_articles_common_path'];
		
		global $eckb_kb_id;
		$eckb_kb_id = $new_config['id'];
		
		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $orig_config['id'], $orig_config, $new_config );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( $update_kb_msg );
		}
		
		// update sequence of articles and categories
		$sync_sequence = new EPKB_KB_Config_Sequence();
		
		$sync_sequence->update_articles_sequence( $orig_config['id'], $new_config );
		$sync_sequence->update_categories_sequence( $orig_config['id'], $new_config );

		wp_die( json_encode( array(
			'message' => esc_html__( 'Configuration Saved', 'echo-knowledge-base' ),
			'kb_main_page_link' => EPKB_KB_Handler::get_first_kb_main_page_url( $new_config ) ) ) );
	}

	/**
	 * Triggered when user submits changes to KB configuration
	 *
	 * @param $kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @return string
	 */
	 // TODO if future: refractor this function and the same in kb-config-controller
	public function update_kb_configuration( $kb_id, $orig_config, $new_config ) {

		// core handles only default KB
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! defined( 'E' . 'MKB_PLUGIN_NAME' ) ) {
			return __('Ensure that Multiple KB add-on is active and refresh this page', 'echo-knowledge-base');
		}

		// sanitize all fields in POST message
		$field_specs = EPKB_Core_Utilities::retrieve_all_kb_specs( $kb_id );
		if ( empty( $field_specs ) ) {
			return __( 'Error occurred. Please refresh your browser and try again.', 'echo-knowledge-base' ) . ' (91)';
		}

		$form_fields = EPKB_Utilities::retrieve_and_sanitize_form( $new_config, $field_specs );
		if ( empty($form_fields) ) {
			EPKB_Logging::add_log("form fields missing");
			return __( 'Error occurred. Please refresh your browser and try again.', 'echo-knowledge-base' ) . ' (92)';
		} else if ( count($form_fields) < 100 ) {
			return __( 'Error occurred. Please refresh your browser and try again.', 'echo-knowledge-base' ) . ' (93)';
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

		// TODO for now save previous configuration
		EPKB_Utilities::save_kb_option( $kb_id, 'epkb_orignal_config', $orig_config, true );

		// save KB core configuration
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_kb_config );
		if ( is_wp_error( $result ) ) {

			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(3)';
			} else {
				return __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' );
			}
		}

		// we are done here
		return '';
	}

	/**
	 * Based on user selection of article/category ordering, setup the nex step
	 */
	public function wizard_update_order_view() {
		global $eckb_is_kb_main_page;

		if ( empty( $_REQUEST['_wpnonce_apply_wizard_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_wizard_changes'], '_wpnonce_apply_wizard_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
		}
		
		$sequence_settings = EPKB_Utilities::post( 'sequence_settings', [] );
		$kb_id = EPKB_Utilities::post( 'epkb_kb_id', 0 );
		if ( empty( $sequence_settings ) || empty( $kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameters (14). Please refresh your page', 'echo-knowledge-base' ) . ' (174)' );
		}
		
		$_GET['wizard-on'] = true;
		
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
		$new_kb_config = array_merge($orig_config, $sequence_settings);
		
		$articles_sequence_new_value = $new_kb_config['articles_display_sequence'];
		$categories_sequence_new_value = $new_kb_config['categories_display_sequence'];
		
		$articles_order_method = $articles_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $articles_sequence_new_value;
		
		$articles_admin = new EPKB_Articles_Admin();
		$article_seq = $articles_admin->get_articles_sequence_non_custom( $kb_id, $articles_order_method );
		if ( $article_seq === false ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 175 ) );
		}

		// ARTICLES: change to custom sequencde if necessary
		if ( $articles_sequence_new_value == 'user-sequenced' ) {
			$article_seq_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, null, true );
			if ( ! empty($article_seq_data) ) {
				$article_seq = $article_seq_data;
			}
		}

		// get non-custom ordering regardless (default to by title if this IS custom order)
		$categories_order_method = $categories_sequence_new_value == 'user_sequenced' ? 'alphabetical-title' : $categories_sequence_new_value;
		$cat_admin = new EPKB_Categories_Admin();
		$category_seq = $cat_admin->get_categories_sequence_non_custom( $kb_id, $categories_order_method );
		if ( $category_seq === false ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 176 ) );
		}

		// CATEGORIES: change to custom sequence if necessary
		if ( $categories_sequence_new_value == 'user-sequenced' ) {
			$custom_categories_data = EPKB_Utilities::get_kb_option( $kb_id, EPKB_Categories_Admin::KB_CATEGORIES_SEQ_META, null, true );
			if ( ! empty($custom_categories_data) ) {
				$category_seq = $custom_categories_data;
			}
		}

		if ( ! is_array( $article_seq ) || ! is_array( $category_seq ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 177 ) );
		}

		// ensure user can order articles and categories easily
		$new_kb_config['nof_articles_displayed'] = '200';
		$new_kb_config['sidebar_top_categories_collapsed'] = 'off';
		$new_kb_config['article_toc_title'] = '';

		$new_kb_config['kb_main_page_layout'] = EPKB_KB_Config_Layout_Basic::LAYOUT_NAME;
		$new_kb_config['expand_articles_icon'] = "ep_font_icon_arrow_carrot_right";

		$new_kb_config['search_layout'] = 'epkb-search-form-0';

		//Plain Colors
		$new_kb_config['section_head_category_icon_color'] = '#000000';
		$new_kb_config['section_head_font_color'] = '#000000';
		$new_kb_config['article_font_color'] = '#000000';
		$new_kb_config['article_icon_color'] = '#459fed';
		$new_kb_config['section_category_font_color'] = '#000000';
		$new_kb_config['section_category_icon_color'] = '#000000';
		$new_kb_config['section_body_background_color'] = '#f5f5f5';
		$new_kb_config['section_head_background_color'] = '#f5f5f5';
		$new_kb_config['background_color'] = '#fff';

		$eckb_is_kb_main_page = true;   // pretend this is Main Page
		$main_page_output = EPKB_Layouts_Setup::output_main_page( $new_kb_config, true, $article_seq, $category_seq );
		
		wp_die( json_encode( array( 'message' => '', 'html' => $main_page_output ) ) );
	}
}
