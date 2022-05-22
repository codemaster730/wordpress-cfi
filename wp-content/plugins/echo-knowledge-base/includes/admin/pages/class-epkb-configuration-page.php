<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display KB configuration menu and pages
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Configuration_Page {

	private $message = array(); // error/warning/success messages
	private $kb_config;
	private $kb_main_pages;
	private $settings_view_contexts = ['admin_eckb_access_order_articles_write', 'admin_eckb_access_config_write'];

	function __construct() {
		$this->message = EPKB_Configuration_Controller::handle_form_actions();
	}

	/**
	 * Displays the KB Config page with top panel + sidebar + preview panel
	 */
	public function display_kb_config_page() {

		// ensure KB config is there
		$kb_id = EPKB_KB_Handler::get_current_kb_id();
		$this->kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id, true );
		if ( is_wp_error( $this->kb_config ) || empty( $this->kb_config ) || ! is_array( $this->kb_config ) || count( $this->kb_config ) < 100 ) {
			EPKB_Logging::add_log('Could not retrieve KB configuration (715)', $this->kb_config);
			EPKB_HTML_Admin::display_config_error_page();
			return;
		}

		// ensure user KB has first KB version
		$kb_first_version = EPKB_Utilities::get_wp_option( 'epkb_version_first', null );
		if ( empty( $kb_first_version ) ) {
			EPKB_Utilities::save_wp_option( 'epkb_version_first', Echo_Knowledge_Base::$version, true );
		}

		// get current add-ons configuration
		$wizard_kb_config = $this->kb_config;
		$wizard_kb_config = apply_filters( 'epkb_all_wizards_get_current_config', $wizard_kb_config, $kb_id );
		if ( is_wp_error( $wizard_kb_config ) || empty( $wizard_kb_config ) || ! is_array( $wizard_kb_config ) || count( $wizard_kb_config ) < 100 ) {
			EPKB_HTML_Admin::display_config_error_page();
			return;
		}

		EPKB_HTML_Admin::admin_page_css_missing_message();

		// regenerate KB sequence for Categories and Articles if missing
		EPKB_KB_Handler::get_refreshed_kb_categories( $kb_id );


		//-------------------------------- SETUP WIZARD --------------------------------

		// should we display Setup Wizard or KB Configuration?
		if ( isset( $_GET['setup-wizard-on'] ) && EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			$handler = new EPKB_KB_Wizard_Setup();
			$handler->display_kb_setup_wizard( $wizard_kb_config );
			return;
		}


		//---------------------- GENERAL CONFIGURATION PAGE -----------------------

		// retrieve KB Main Pages
		$this->kb_main_pages = EPKB_KB_Handler::get_kb_main_pages( $this->kb_config );

		/**
		 * Views of the Configuration Admin Page - show limited content for users that did not complete Setup Wizard
		 */
		if ( isset( $_GET['archived-kbs'] ) ) {
			$admin_page_views = self::get_archived_kbs_views_config();

		} else {
			$admin_page_views = EPKB_Core_Utilities::is_run_setup_wizard_first_time()
				? self::get_run_setup_first_views_config()
				: $this->get_regular_views_config( $wizard_kb_config );
		}   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap">

			<div class="epkb-kb-config-page-container">    <?php

				/**
				 * ADMIN HEADER (KB logo and list of KBs dropdown)
				 */
				EPKB_HTML_Admin::admin_header( $this->kb_config, ['admin_eckb_access_order_articles_write', 'admin_eckb_access_frontend_editor_write'] );

				/**
				 * ADMIN TOOLBAR
				 */
				EPKB_HTML_Admin::admin_toolbar( $admin_page_views );

				/**
				 * ADMIN SECONDARY TABS
				 */
				EPKB_HTML_Admin::admin_secondary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPKB_HTML_Admin::admin_settings_tab_content( $admin_page_views, '' ); ?>

			</div>

		</div>  <?php

		/**
		 * Show any notifications
		 */
		foreach ( $this->message as $class => $message ) {
			echo  EPKB_HTML_Forms::notification_box_bottom( $message, '', $class );
		}
	}

	/**
	 * KB Design: Box Editors List
	 *
	 * @return false|string
	 */
	private function show_frontend_editor_links() {

		$editor_urls = EPKB_Editor_Utilities::get_editor_urls( $this->kb_config, '', '', '', false );

		ob_start();

        echo '<div class="epkb-editor-launcher-mode-title">' . __( 'Editor Launcher Mode' , 'echo-knowledge-base' ) . '</div>';

		EPKB_HTML_Elements::radio_buttons_horizontal( array(
			'name'                 => 'editor_backend_mode',
			'input_group_class'    => 'radio-buttons-horizontal',
			'label'                => __( 'Open the Editor on', 'echo-knowledge-base' ),
			'value'                => EPKB_Core_Utilities::is_kb_flag( 'editor_backend_mode' ) ? 1 : 0,
			'desc_condition'       => 0,
			'options'              => array(
			        0 => __( 'Front End', 'echo-knowledge-base') ,
			        1 => __( 'Back End', 'echo-knowledge-base' )
		),
			'desc'                 => __( 'If you experience compatibility issues on the front end, switch the Editor to back end.', 'echo-knowledge-base' ),
		) );

		// Main page link to editor
		if ( $editor_urls['main_page_url'] != '' ) {
			EPKB_HTML_Forms::call_to_action_box(array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
				'title' => __('Main Page', 'echo-knowledge-base' ),
				'btn_text' => __('Configure', 'echo-knowledge-base' ),
				'btn_url' => $editor_urls['main_page_url'],
				'btn_target' => "_blank",
				'container_class' => 'epkb-main-page-editor-link'
			) );
		} else {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
				'title'         => __( 'Main Page', 'echo-knowledge-base' ),
				'content'       => __( 'No Main Page Found', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Add Shortcode', 'echo-knowledge-base' ),
				'btn_url'       => admin_url( "edit.php?post_type=" . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . "&page=epkb-kb-configuration&wizard-global" ),
				'btn_target'	  => "_blank",
			) );
		}

		// Article page link to editor
		if ( $editor_urls['article_page_url'] != '' ) {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/article-page.jpg',
				'title'         => __( 'Article Page', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Configure', 'echo-knowledge-base' ),
				'btn_url'       => $editor_urls['article_page_url'],
				'btn_target'    => "_blank",
				'container_class' => 'epkb-article-page-editor-link'
			) );
		} else {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/article-page.jpg',
				'title'         => __( 'Article Page', 'echo-knowledge-base' ),
				'content'       => __( 'All articles have no Category. Please assign your article to categories.', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Add New Article', 'echo-knowledge-base' ),
				'btn_url'       => admin_url( "post-new.php?post_type=" . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) ),
				'btn_target'    => "_blank",
			) );
		}

		// Archive page link to editor
		if ( $this->kb_config['templates_for_kb'] == 'current_theme_templates' ) {
			EPKB_HTML_Forms::call_to_action_box(array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/category-archive-page.jpg',
				'title' => __( 'Category Archive Page', 'echo-knowledge-base' ),
				'content' => sprintf(  __( 'The KB template option is set to the Current Theme. You need to configure your Archive Page template in ' .
					'your theme settings. For details about the KB template option see %s', 'echo-knowledge-base' ),
					' <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank">' . esc_html__( 'here', 'echo-knowledge-base' ) . '.' . '</a> ' )
			) );
		} else if ( $editor_urls['archive_url'] != '' ) {
			EPKB_HTML_Forms::call_to_action_box(array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/category-archive-page.jpg',
				'title' => __('Category Archive Page', 'echo-knowledge-base'),
				'btn_text' => __('Configure', 'echo-knowledge-base'),
				'btn_url' => $editor_urls['archive_url'],
				'btn_target' => "_blank",
				'container_class' => 'epkb-archive-page-editor-link'
			) );
		} else {
			EPKB_HTML_Forms::call_to_action_box(array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/category-archive-page.jpg',
				'title' => __('Category Archive Page', 'echo-knowledge-base'),
				'content' => __('No Categories Found', 'echo-knowledge-base'),
				'btn_text' => __('Add New Category', 'echo-knowledge-base'),
				'btn_url' => admin_url('edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_config['id'] ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] )),
				'btn_target' => "_blank",
			) );
		}

		// Advanced Search Page
		if ( EPKB_Utilities::is_advanced_search_enabled() && $editor_urls['search_page_url'] != '' ) {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/search-result-page.png',
				'title'         => __( 'Search Results Page', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Configure', 'echo-knowledge-base' ),
				'btn_url'       => $editor_urls['search_page_url'],
				'btn_target'    => "_blank",
				'container_class' => 'epkb-search-page-editor-link'
			) );
		} else if ( EPKB_Utilities::is_advanced_search_enabled() ) {
			EPKB_HTML_Forms::call_to_action_box( array(
				'style' => 'style-1',
				'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
				'title'         => __( 'Search Results Page', 'echo-knowledge-base' ),
				'content'       => __( 'To edit the Search Results page, be sure you have a KB Main Page.', 'echo-knowledge-base' ),
				'btn_text'      => __( 'Configure KB Main Page', 'echo-knowledge-base' ),
				'btn_url'       => admin_url( "edit.php?post_type=" . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . "&page=epkb-kb-configuration#settings__kb-urls" ),
				'btn_target'	  => "_blank",
			) );
		}

		return ob_get_clean();
	}

	/**
	 * Help Dialog: Box Content
	 *
	 * @return false|string
	 */
	private static function show_help_dialog_option() {

		ob_start(); ?>

		<!--  Help Dialog Tab Content -->
		<div class="epkb-config-content-wrapper" id="epkb-help-dialog-option">
			<div class="epkb-help-dialog-option__img">
				<img src="<?php echo esc_url( Echo_Knowledge_Base::$plugin_url . 'img/featured-screenshots-help-dialog-example.jpg' ); ?>">
			</div>
		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Show multilingual settings
	 *
	 * @return false|string
	 */
	private function show_multilingual_settings() {

		ob_start();

		EPKB_HTML_Elements::checkbox_toggle( array(
							'id'            => 'epkb_wpml_enable',
							'name'          => 'epkb_wpml_enable',
							'text'          => 'WPML Enable',
							'textLoc'       => 'left',
							'topDesc'       => '<a href="https://www.echoknowledgebase.com/documentation/setup-wpml-for-knowledge-base/" target="_blank">' . esc_html__( 'Follow WPML setup instructions here.', 'echo-knowledge-base' ) . '</a>',
							'checked'       => ( ! empty( $this->kb_config['wpml_is_enabled'] ) && $this->kb_config['wpml_is_enabled'] == 'on' ),
						) );
		echo '<input type="hidden" id="_wpnonce_epkb_wpml_enable" name="_wpnonce_epkb_wpml_enable" value="' . esc_attr( wp_create_nonce( "_wpnonce_epkb_wpml_enable" ) ) . '"/>';
		echo '<input type="hidden" id="epkb_wpml_enable_kb_id" name="epkb_wpml_enable_kb_id" value="' . esc_attr( $this->kb_config['id'] ) . '"/>';

		return ob_get_clean();
	}

	/**
	 * Get configuration array for regular views of KB Configuration page
	 *
	 * @param $wizard_kb_config
	 * @return array[]
	 */
	private function get_regular_views_config( $wizard_kb_config ) {

		$wizard_ordering = new EPKB_KB_Wizard_Ordering();
		$wizard_global = new EPKB_KB_Wizard_Global( $wizard_kb_config );

		$errors_tab_config = $this->get_errors_view_config();

		/**
		 * VIEW: Overview
		 */
		$overview_view_config = array(

			// Shared
			'active' => empty( $errors_tab_config ),
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_author_capability(),
			'list_key' => 'overview',
			'kb_config_id' => $this->kb_config['id'],

			// Top Panel Item
			'label_text' => __( 'Overview', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cubes',

			// Show actions row with Archive/Delete buttons only for non default and active KBs
			'list_top_actions_html' => ( $this->kb_config['id'] == EPKB_KB_Config_DB::DEFAULT_KB_ID || EPKB_Core_Utilities::is_kb_archived( $this->kb_config['status'] ) ) ? '' : $this->get_kb_actions(),

			// Boxes List
			'boxes_list' => array(

				// Box: About KB
				array(
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_author_capability(),
					'class' => 'epkb-admin__boxes-list__box__about-kb',
					'title' => __( 'About KB', 'echo-knowledge-base' ),
					'description' => '',
					'html' => $this->get_about_kb_box(),
				),

				// Box: KB Name
				array(
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
					'class' => 'epkb-admin__boxes-list__box__kb-name',
					'title' => __( 'KB Nickname', 'echo-knowledge-base' ),
					'description' => __( 'Give your Knowledge Base a name. The name will show when we refer to it or when you see a list of post types.', 'echo-knowledge-base' ),
					'html' => $this->get_kb_name_box(),
				),

				// Box: KB Location
				array(
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
					'class' => 'epkb-admin__boxes-list__box__kb-location',
					'title' => __( 'KB Location', 'echo-knowledge-base' ),
					'description' => '',
					'html' => $this->get_kb_location_box(),
				)
			),
		);

		/**
		 * VIEW: KB Design
		 */
		$kb_design_view_config = array(

			// Shared
			'active' => false,
			'list_key' => 'kb-design',
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
			'kb_config_id' => $this->kb_config['id'],

			// Top Panel Item
			'label_text' => __( 'KB Design', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-paint-brush',

			// Boxes List
			'boxes_list' => array(

				// Box: Configure the Editors
				array(
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
					'class' => 'epkb-admin__boxes-list__box__editors-list',
					'title' => __( 'Visual Editor - configure KB text, fonts, colors, and style', 'echo-knowledge-base' ),
					'html' => $this->show_frontend_editor_links(),
				),
			),
		);

		/**
		 * VIEW: Widgets / Shortcode
		 */
		$kb_widgets_view_config = array(

			// Shared
			'active' => false,
			'list_key' => 'widgets',
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
			'kb_config_id' => $this->kb_config['id'],

			// Top Panel Item
			'label_text' => __( 'Widgets' ) . ' / ' . __( 'Shortcodes', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-list-alt',

			// Boxes List
			'boxes_list' => self::get_widgets_boxes( $this->kb_config )
		);

		/**
		 * VIEW: HELP DIALOG
		 */
		/* TODO $help_dialog_view_config = array(

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_editor_capability(),
			'list_key' => 'help-dialog',

			// Top Panel Item
			'label_text' => __( 'Help Dialog', 'echo-knowledge-base' ),
			'icon_class' => 'ep_font_icon_help_dialog',

			// Boxes List
			'boxes_list' => array(

				// Box: Help Dialog Content
				array(
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_editor_capability(),
					'class' => 'epkb-admin__boxes-list__box__enable-help-dialog',
					'title' => __( 'Get Help Dialog Widget', 'echo-knowledge-base' ),
					'html' => self::show_help_dialog_option(),
				),
			)
		); */

		/**
		 * VIEW: SETTINGS
		 */
		$kb_url_boxes = [];

		// Box: Help box with Docs link for URL changing
		$kb_url_boxes[] = array(
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
			'title' => __( 'About KB URL', 'echo-knowledge-base' ),
			'html' => $this->display_kb_url_help_box(),
		);

		if ( empty( $this->kb_main_pages ) ) {
			$kb_url_boxes[] = array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
				'title' => __( 'Control Your Knowledge Base URL', 'echo-knowledge-base' ),
				'html' => $this->display_no_shortcode_warning( $this->kb_config, true ),
				'class' => 'epkb-admin__warning-box',
			);

		} else {

			// Box: Category Name in KB URL
			$kb_url_boxes[] = array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
				'title' => __( 'Category Name in KB URL', 'echo-knowledge-base' ),
				'html' => EPKB_HTML_Elements::checkbox_toggle( array(
					'id'            => 'categories_in_url_enabled__toggle',
					'textLoc'       => 'right',
					'data'          => 'on',
					'toggleOnText'  => __( 'yes', 'echo-knowledge-base' ),
					'toggleOffext'  => __( 'no', 'echo-knowledge-base' ),
					'checked'       => $this->kb_config['categories_in_url_enabled'] == 'on',
					'return_html'   => true,
					'topDesc'       => __( 'Should article URLs contain the slug of their categories?', 'echo-knowledge-base' ),
				) ),
				'class' => 'epkb-admin__toggle-box',
			);

			// Box: Control Your Knowledge Base URL
			$kb_url_boxes[] = array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
				'title' => __( 'Control Your Knowledge Base URL', 'echo-knowledge-base' ),
				'html' => $wizard_global->show_kb_urls_global_wizard(),
				'class' => 'epkb-admin__wizard-box',
			);
		}

		// call first to set proper permissions
		$various_settings = $this->get_various_secondary_tab();

		$settings_view_config = array(

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( $this->settings_view_contexts ),
			'list_key' => 'settings',

			// Top Panel Item
			'label_text' => __( 'Settings', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cogs',

			// Secondary Panel Items
			'secondary' => array(

				// SECONDARY VIEW: Order Articles
				array(

					// Shared
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_order_articles_write' ),
					'active' => ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_config_write' ),
					'list_key' => 'order-articles',

					// Secondary Panel Item
					'label_text' => __( 'Order Articles', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => array(

						// Box: Ordering Settings
						array(
							'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_order_articles_write' ),
							'title' => __( 'Ordering Settings', 'echo-knowledge-base' ),
							'html' => $wizard_ordering->show_article_ordering( $wizard_kb_config ),
						),
					),
				),

				// SECONDARY VIEW: KB URLs
				array(

					// Shared
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
					'active' => true,
					'list_key' => 'kb-urls',

					// Secondary Panel Item
					'label_text' => __( 'KB URL', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => $kb_url_boxes,
				),

				// SECONDARY VIEW: ACCESS CONTROL
				array(

					// Shared
					'list_key' => 'access-control',

					// Secondary Panel Item
					'label_text' => __( 'Access Control', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'list_top_actions_html' => '<div class="epkb-admin__list-actions-row">' . EPKB_HTML_Elements::submit_button_v2( __( 'Save Access Control Settings', 'echo-knowledge-base' ), 'epkb_save_access_control', 'epkb-admin__save-access-control-btn', '', true, true, 'epkb-success-btn' ) . '</div>',
					'boxes_list' => EPKB_Admin_UI_Access::get_access_boxes( $this->kb_config ),
				),

				// SECONDARY VIEW: Various
				$various_settings,
			),
		);

		/**
		 * VIEW: TOOLS
		 */

		// Tools View config
		$tools_view_config = EPKB_Configuration_Tools_Page::get_tools_view_config( $this->kb_config );

		/**
		 * OUTPUT VIEWS CONFIG
		 */

		// compose views
		$core_views = [];

		if ( ! empty( $errors_tab_config ) ) {
			$core_views[] = $errors_tab_config;
		}

		$core_views[] = $overview_view_config;

		// Limited config for archived KBs
		if ( ! EPKB_Core_Utilities::is_kb_archived( $this->kb_config['status'] ) ) {
			$core_views[] = $kb_design_view_config;
			$core_views[] = $kb_widgets_view_config;
			$core_views[] = $settings_view_config;
			$core_views[] = $tools_view_config;
		}

		/*if ( ! EPKB_Utilities::is_help_dialog_enabled() ) {
			$core_views[] = $help_dialog_view_config;
		}*/

		/**
		 * Add-on views for KB Configuration page
		 */
		$add_on_views = apply_filters( 'eckb_admin_config_page_views', [], $this->kb_config );
		if ( empty( $add_on_views ) || ! is_array( $add_on_views ) ) {
			$add_on_views = [];
		}

		$all_views = array_merge( $core_views, $add_on_views );

		if ( ! EPKB_Articles_Setup::is_article_structure_v2( $this->kb_config ) ) {
			foreach ( $all_views as &$view ) {
				$view['boxes_list'] = [ [
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
					'class' => 'epkb-admin__boxes-list__box__editors-list',
					'title' => __( 'Deprecates Settings Error', 'echo-knowledge-base' ),
					'html' => $this->get_article_version_error_box(),
				] ];

				unset( $view['secondary'] );
			}
		}

		// Full config for published KBs
		return $all_views;
	}

	/**
	 * Get boxes for Widgets / Shortcode panel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_widgets_boxes( $kb_config ) {

		$boxes = [];

		foreach ( self::get_widgets_boxes_config( $kb_config ) as $box ) {

			$box['active_status'] = EPKB_Utilities::is_plugin_enabled( $box['plugin'] );

			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box )
			];
		}

        return $boxes;
	}

	/**
	 * Get boxes config for Widgets / Shortcode
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_widgets_boxes_config( $kb_config ) {


		return [
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => __( 'Articles Index Directory', 'echo-knowledge-base' ),
				'desc'         => __( 'Show alphabetical list of articles grouped by letter in a three-column format.', 'echo-knowledge-base' ) . __( 'Shortcode', 'echo-knowledge-base' ) . ':<br>' . EPKB_Articles_Index_Shortcode::get_embed_code( $kb_config['id'] ),
				'custom_links' => '<a class="epkb-kbnh__feature-copy-link epkb-primary-btn" href="#" data-copy="' . esc_attr( EPKB_Articles_Index_Shortcode::get_embed_code( $kb_config['id'] ) ) . '"><span>' . __( 'Copy Shortcode to Clipboard', 'echo-knowledge-base' ) . '</span></a>',
				'docs'         => 'https://www.echoknowledgebase.com/documentation/shortcode-articles-index-directory/',
			],
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => __( 'Related Articles', 'echo-knowledge-base' ),
				'desc'         => __( 'Show articles related to the current article at the end of each article. Comming soon. Let us know if you want to be a beta tester.', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/shortcode-widget-related-articles/',
			],
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-list-alt',
				'title'        => __( 'Widgets for Elementor', 'echo-knowledge-base' ),
				'desc'         => __( 'Our Elementor widgets are designed for writers. We make it easy to write great instructions, step-by-step guides, manuals and detailed documentation.', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/elementor-widgets-for-documentation/',
			],
		/*	[
				'plugin'    => 'ep'.'hd',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'Frontend Widget', 'echo-knowledge-base' ),
				'desc'      => sprintf( __( '%s Engage %s your website visitors and %s gain new customers %s with page-specific %s FAQs %s and %s knowledge base articles %s. ' .
				                            'Help users communicate with you %s without leaving the page %s using a simple %s contact form %s shown with the Help Dialog.', 'echo-knowledge-base' ),
					'<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>' ),
				'docs'      => '',
				'video'     => '',
			], */
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'KB Search Widget', 'echo-knowledge-base' ),
				'desc'      => __( 'Add a search box on your Home page, Contact Us page, and others.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/search-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'KB Categories', 'echo-knowledge-base' ),
				'desc'      => __( 'List your KB Categories for easy reference, which are typically displayed in sidebars.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/categories-list-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'List of Category Articles', 'echo-knowledge-base' ),
				'desc'      => __( 'Display a list of articles for a given category.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/category-articles-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'KB Tags', 'echo-knowledge-base' ),
				'desc'      => __( 'Display current KB tags ordered alphabetically.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/tags-list-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'List of Tagged Articles', 'echo-knowledge-base' ),
				'desc'      => __( 'Display a list of articles that have a given tag.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/tagged-articles-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'Recent Articles Widget', 'echo-knowledge-base' ),
				'desc'      => __( 'Show either recently created or recently modified KB Articles.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/recent-articles-widget/',
				'video'     => '',
			],
			[
				'plugin'    => 'widg',
				'icon'      => 'epkbfa epkbfa-list-alt',
				'title'     => __( 'KB Sidebar', 'echo-knowledge-base' ),
				'desc'      => __( 'A dedicated KB Sidebar will be shown only on the left side or right side of your KB articles.', 'echo-knowledge-base' ),
				'config'    => admin_url( '/widgets.php' ),
				'docs'      => 'https://www.echoknowledgebase.com/documentation/kb-sidebar/',
				'video'     => '',
			]
		];
	}

	/**
	 * Get configuration array for views of KB Configuration page before the first KB setup
	 *
	 * @return array[]
	 */
	private static function get_run_setup_first_views_config() {

		return array(

			// VIEW: SETUP WIZARD
			array(

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_frontend_editor_write'] ),
				'list_key' => 'setup-wizard',

				// Top Panel Item
				'label_text' => __( 'Setup Wizard', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-cogs',

				'boxes_list' => array(

					// Box: Setup Wizard Message
					array(
						'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_frontend_editor_write'] ),
						'html' => self::get_setup_wizard_message(),
						'class' => 'epkb-admin__notice'
					),
				),
			),
		);
	}

	/**
	 * Return message to complete Setup Wizard
	 *
	 * @return false|string
	 */
	private static function get_setup_wizard_message() {

		ob_start();     ?>

		<div class="epkb-admin__setup-wizard-warning">     <?php

			EPKB_HTML_Forms::notification_box_popup( array(
				'type'  => 'success',
				'title' => __( 'Thank you for installing our Knowledge Base.', 'echo-knowledge-base' ) . ' ' . __( 'Get started by running our Setup Wizard.', 'echo-knowledge-base' ),
				'desc'  => '<span>' . EPKB_Core_Utilities::get_kb_admin_page_link( 'page=epkb-kb-configuration&setup-wizard-on', __( 'Start the Setup Wizard', 'echo-knowledge-base' ), false,'epkb-success-btn' ) . '</a></span>',
			) );   ?>

		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get configuration array for Errors view of KB Configuration page
	 *
	 * @return array
	 */
	private function get_errors_view_config() {

		$error_boxes = array();

		// KB missing shortcode error message
		if ( empty( $this->kb_main_pages ) ) {
			$error_boxes[] = array(
				'icon_class' => 'epkbfa-exclamation-circle',
				'title' => __( 'Missing shortcode', 'echo-knowledge-base' ),
				'html' => $this->display_no_shortcode_warning( $this->kb_config, true ),
				'class' => 'epkb-admin__warning-box',
			);
		}

		// License issue messages from add-ons
		$add_on_messages = apply_filters( 'epkb_add_on_license_message', array() );
		if ( ( ! empty( $add_on_messages ) && is_array( $add_on_messages ) ) || did_action( 'kb_overview_add_on_errors' ) ) {

			foreach ( $add_on_messages as $add_on_name => $add_on_message ) {

				$add_on_name = str_replace( array( '2', '3', '4' ), '', $add_on_name );

				array_push( $error_boxes, array(
					'icon_class' => 'epkbfa-exclamation-circle',
					'class' => 'epkb-admin__boxes-list__box__addons-license',
					'title' => $add_on_name . ': ' . __( 'License issue', 'echo-knowledge-base' ),
					'description' => '',
					'html' => $add_on_message,
				) );
			}
		}

		return empty( $error_boxes )
			? array()
			: array(

				// Shared
				'active' => true,
				'list_key' => 'errors',

				// Top Panel Item
				'label_text' => __( 'Errors', 'echo-knowledge-base' ),
				'icon_class' => 'page-icon overview-icon epkbfa epkbfa-exclamation-triangle',

				// Boxes List
				'boxes_list' => $error_boxes,
			);
	}

	/**
	 * Get About KB settings box
	 *
	 * @return false|string
	 */
	private function get_about_kb_box() {

		ob_start();     ?>

		<div class="epkb-kb__btn-wrap">       <?php
			echo EPKB_Core_Utilities::get_current_kb_main_page_link( $this->kb_config, __( 'View My Knowledge Base', 'echo-knowledge-base' ), '' );      ?>
		</div>  <?php

		if ( EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_need_help_read' ) ) {   ?>
			<div>       <?php
				echo EPKB_Core_Utilities::get_kb_admin_page_link( 'page=epkb-kb-need-help#getting-started', __( 'Get Started', 'echo-knowledge-base' ), false );      ?>
			</div>      <?php
		}   ?>

		<div class="epkb-kb__btn-wrap">       <?php
			echo EPKB_Core_Utilities::get_kb_admin_page_link( 'page=epkb-kb-need-help#features__design', __( 'Explore Features', 'echo-knowledge-base' ), false );      ?>
		</div>
		<div class="epkb-kbnh__header__link-container">
			<span class="epkb-kbnh__link__text"><a href="https://www.echoknowledgebase.com/documentation/" target="_blank"><?php esc_html_e( 'View Online Documentation', 'echo-knowledge-base' ); ?></a></span>
			<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
		</div>
		<div class="epkb-kbnh__header__link-container">
			<span class="epkb-kbnh__link__text"><a href="https://www.echoknowledgebase.com/contact-us/" target="_blank"><?php esc_html_e( 'Contact Us', 'echo-knowledge-base' ); ?></a></span>
			<span class="epkb-kbnh__link__icon epkbfa epkbfa-external-link"></span>
		</div>  <?php

		// DEPRECATED
		do_action( 'eckb_manage_show_header' );

		return ob_get_clean();
	}

	/**
	 * Get KB Name settings box
	 * Note: KB Name is used in drop-down, reference to the KB and in CPT name
	 *
	 * @return false|string
	 */
	private function get_kb_name_box() {

		ob_start();     ?>

		<!-- Options -->
		<div class="epkb-admin__kb-rename">
			<div class="epkb-admin__kb-rename__name">
				<span class="epkb-admin__kb-rename__label"><?php esc_html_e( 'Nickname: ', 'echo-knowledge-base'); ?></span>
				<span id="epkb-admin__kb-rename__value"><?php echo esc_html( $this->kb_config['kb_name'] ); ?></span>
				<span class="epkb-edit-toggle"><i class="epkbfa epkbfa-pencil"></i></span>
			</div>
			<div class="epkb-admin__kb-rename__edit">
				<form method="post" id="epkb-admin__kb-rename__form">
					<input type="hidden" name="epkb_kb_id" value="<?php echo esc_attr( $this->kb_config['id'] ); ?>"/>
					<input type="text" name="epkb_kb_name_input" value="<?php echo esc_attr( $this->kb_config['kb_name'] ); ?>">
					<input value="<?php esc_attr_e( 'Save', 'echo-knowledge-base' ); ?>" type="submit" class="epkb-primary-btn">
				</form>
			</div>
		</div>      <?php

		// Show status only for archived KBs
		if ( EPKB_Core_Utilities::is_kb_archived( $this->kb_config['status'] ) ) {     ?>
			<div class="epkb-admin__kb-status"><span class="epkb-admin__kb-status__label"><?php esc_html_e( 'Status:', 'echo-knowledge-base' ); ?> </span><span class="epkb-admin__kb-status__value"><?php echo esc_html( ucfirst( $this->kb_config['status'] ) ); ?></span></div><?php
			do_action( 'eckb_admin_config_page_kb_status', $this->kb_config );
		}

		return ob_get_clean();
	}

	/**
	 * Get KB Location settings box
	 *
	 * @return false|string
	 */
	private function get_kb_location_box() {

        $HTML = NEW EPKB_HTML_Forms();
		ob_start();

		// If no Main Pages were detected for the current KB
		if ( empty( $this->kb_main_pages ) ) {
			$this->display_no_shortcode_warning( $this->kb_config );

		// If at least one KB Main Page exists for the current KB
		} else {
			$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );
			$kb_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $this->kb_config );     ?>

			<div class="epkb-admin__chapter"><?php esc_html_e( 'Your knowledge base will be displayed on the page with KB shortcode: ', 'echo-knowledge-base' ); ?><strong>[epkb-knowledge-base id=<?php echo esc_attr( $this->kb_config['id'] ); ?>]</strong></div>
			<table class="epkb-admin__chapter__wrap">
				<tbody>
					<tr class="epkb-admin__chapter__content">
						<td><span><?php esc_html_e( 'Page Title: ', 'echo-knowledge-base' ); ?></span></td>
						<td><span><?php echo esc_html( $this->kb_config['kb_main_pages'][$kb_page_id] ); ?></span></td>
						<td><a class="epkb-primary-btn" href="<?php echo get_edit_post_link( $kb_page_id ); ?>" target="_blank"><?php _e( 'Change Title', 'echo-knowledge-base' ); ?></a></td>
					</tr>
					<tr class="epkb-admin__chapter__content">
						<td><span><?php esc_html_e( 'Page / KB URL: ', 'echo-knowledge-base' ); ?></span></td>
						<td><a href="<?php echo esc_url( $kb_main_page_url ); ?>" target="_blank"><?php echo esc_html(  $kb_main_page_url ); ?><i class="ep_font_icon_external_link"></i></a></td>
						<td><a class="epkb-primary-btn epkb-admin__step-cta-box__link" data-target="settings__kb-urls" href="#settings__kb-urls"><?php esc_html_e( 'Change KB URL', 'echo-knowledge-base' ); ?></a></td>
					</tr>
					<tr class="epkb-admin__chapter__content"><td colspan="3"></td></tr>
					<tr class="epkb-admin__chapter__content">
						<td colspan="3"><?php esc_html_e( 'Read more about changing KB URL: ', 'echo-knowledge-base' ); ?>
							<a href="https://www.echoknowledgebase.com/documentation/changing-permalinks-urls-and-slugs/" target="_blank"><?php esc_html_e( 'here', 'echo-knowledge-base' ); ?> <i class="ep_font_icon_external_link"></i></a>
						</td>
					</tr>
				</tbody>
			</table>      <?php

			// If user has multiple pages with KB Shortcode then let them know
			if ( count( $this->kb_main_pages ) > 1 ) {        ?>
				<div class="epkb-admin__chapter"><?php echo sprintf( esc_html__( 'Note: You have other pages with KB shortcode that are currently %snot used%s: ', 'echo-knowledge-base' ), '<strong>', '</strong>' ); ?></div>
				<ul class="epkb-admin__items-list">    <?php

					foreach ( $this->kb_main_pages as $page_id => $page_info ) {

						// Do not show relevant KB Main Page in the extra Main Pages list
						if ( $page_id == $kb_page_id ) {
							continue;
						}   ?>

						<li><span><?php echo esc_html( $page_info['post_title'] ); ?></span> <a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>" target="_blank"><?php esc_html_e( 'Edit page', 'echo-knowledge-base' ); ?></a></li><?php
					}   ?>

				</ul>                <?php
                $HTML::notification_box_middle( array(
                        'type' => 'error-no-icon',
                        'desc' => 'It\'s best to delete these pages unless you have a very specific reason for having them.',
                        '' => '',
                ));
			}
		}

		return ob_get_clean();
	}

	/**
	 * Get actions row for KB - archive/activate/delete
	 *
	 * @return false|string
	 */
	private function get_kb_actions() {

		ob_start();     ?>

		<div class="epkb-admin__list-actions-row">    <?php
			do_action( 'eckb_admin_config_page_overview_actions', $this->kb_config );   ?>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get configuration array for archived KBs
	 *
	 * @return array
	 */
	private static function get_archived_kbs_views_config() {

		$views_config = array(

			// View: Archived KBs
			array(

				// Shared
				'active' => true,
				'list_key' => 'archived-kbs',

				// Top Panel Item
				'label_text' => __( 'Archived KBs', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-cubes',

				// Boxes List
				'boxes_list' => array(

				),
			),
		);

		$archived_kbs = EPKB_Core_Utilities::get_archived_kbs();
		foreach ( $archived_kbs as $one_kb_config ) {

			$views_config[0]['boxes_list'][] = array(
				'class' => '',
				'title' => $one_kb_config['kb_name'],
				'description' => '',
				'html' => self::get_archived_kb_box_html( $one_kb_config ),
			);
		}

		return $views_config;
	}

	/**
	 * Get HTML for one archived KB box
	 *
	 * @param $kb_config
	 *
	 * @return false|string
	 */
	private static function get_archived_kb_box_html( $kb_config ) {

		ob_start();

		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() ) {    ?>
			<div><?php esc_html_e( 'To manage non-default KBs you need Multiple KB add-on to be activated.', 'echo-knowledge-base' ); ?></div><?php
		}

		do_action( 'eckb_admin_config_page_kb_status', $kb_config );

		return ob_get_clean();
	}

	private function get_add_ons_settings( $filter ) {

		$add_on_setting = apply_filters( $filter, [], $this->kb_config );
		if ( empty( $add_on_setting ) || ! is_array( $add_on_setting ) ) {
			return [];
		}

		$context = empty( $add_on_setting['minimum_required_capability_context'] ) ? EPKB_Admin_UI_Access::get_admin_capability() : $add_on_setting['minimum_required_capability_context'];
		$add_on_setting['minimum_required_capability'] = EPKB_Admin_UI_Access::get_context_required_capability( [$context] );

		return $add_on_setting;
	}

	private function get_various_secondary_tab() {

		$various_settings = [];

		// Setting: WPML Settings ( EPKB_Utilities::is_wpml_plugin_active() || EPKB_Utilities::is_wpml_enabled( $this->kb_config ) )
		if ( current_user_can( EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ) ) && ! EPKB_Utilities::is_amag_on() ) {
			$various_settings[] = array(
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_config_write' ),
				'title' => __( 'WPML', 'echo-knowledge-base' ),
				'html' => $this->show_multilingual_settings() );
		}

		// Setting: Sidebar Intro Text
		$sidebar_intro_text_settings = $this->get_add_ons_settings( 'epkb_config_page_sidebar_intro_settings' );
		if ( ! empty( $sidebar_intro_text_settings ) ) {
			$various_settings = array_merge( $various_settings, [ $sidebar_intro_text_settings ] );
			$this->settings_view_contexts[] = 'admin_eckb_access_frontend_editor_write';
		}

		return empty( $various_settings )
			? null
			: array(

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( 'admin_eckb_access_frontend_editor_write' ),
				'list_key' => 'various',

				// Secondary Panel Item
				'label_text' => __( 'Various', 'echo-knowledge-base' ),

				// Secondary Boxes List
				'boxes_list' => $various_settings,
			);
	}

	/**
	 * Display warning about missing shortcode
	 *
	 * @param $kb_config
	 * @param bool $return_html
	 *
	 * @return false|string|void
	 */
	private function display_no_shortcode_warning( $kb_config, $return_html=false ) {

        $notification = EPKB_HTML_Forms::notification_box_middle( array(
            'type'  => 'error',
            'title' => 'We did not detect any page with KB shortcode for your knowledge base '.$kb_config['kb_name'].'. You can do the following:',
            'desc'  => '<ul>
                            <li>If you have this page, please re-save it and come back</li>
                            <li>Create or update a page and add KB shortcode '.$kb_config['id'].' to that page. Save the page and then come back here.</li>
                            <li>Run Setup Wizard to create a new KB Main Page <a href="'.esc_url( admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) .
                                  '&page=epkb-kb-configuration&setup-wizard-on' ) ).'" target="_blank">Run Setup Wizard</a></li>
                        </ul>'
        ), $return_html  );

        if ( $return_html ) {
            return $notification;
        } else {
            echo $notification;
        }

	}

	/**
	 * Display warning about missing shortcode
	 *
	 * @return string
	 */
	private function display_kb_url_help_box() {

		return EPKB_HTML_Forms::notification_box_middle( array(
			'type'  => 'info',
			'title' => esc_html__( 'Need help?', 'echo-knowledge-base' ),
			'desc'  => sprintf( '%s <a href="%s" class="ret" target="_blank">%s</a>', esc_html__( 'Read more about changing KB URL ', 'echo-knowledge-base' ), 'https://www.echoknowledgebase.com/documentation/changing-permalinks-urls-and-slugs/', esc_html__( 'here', 'echo-knowledge-base' ) )
		), true  );
	}

	/**
	 * Message that will show the link to change article version and warning message
	 */
	private function get_article_version_error_box() {
		ob_start(); ?>

		<div class="epkb-admin__section-wrap epkb-admin__deprecated-wizard-warning"><?php
			$editor_url = add_query_arg( [ 'action' => 'epkb_update_article_v2', '_wpnonce_manage_kbs' => wp_create_nonce( "_wpnonce_manage_kbs" ), 'emkb_kb_id' => $this->kb_config['id'] ] );

			EPKB_HTML_Forms::notification_box_popup( array(
				'type'  => 'error',
				'title' => esc_html__( 'Upgrade to Articles v2 Required.', 'echo-knowledge-base' ),
				'desc'  => '<span>' . esc_html__( 'You have an old version of articles format. Please run the upgrade to continue. After the upgrade, check your articles and make minor adjustments if required.', 'echo-knowledge-base' ) . ' ' .
				 '<a href="' . $editor_url . '" class="epkb-primary-btn">' . esc_html__( 'UPGRADE NOW', 'echo-knowledge-base' ) . '</a></span>' // TODO button
						   . '<span>' . ' ' . __( 'If you have questions or concerns, please talk to us and we will gladly help you with this upgrade.', 'echo-knowledge-base' ) . ' ' . EPKB_Utilities::contact_us_for_support() . '</span>'
			) ); ?>
		</div> <?php

		return ob_get_clean();
	}
}