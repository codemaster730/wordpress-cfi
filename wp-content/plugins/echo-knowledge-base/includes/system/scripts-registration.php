<?php

/**  Register JS and CSS files  */

/**
 * FRONT-END pages using our plugin features
 */
function epkb_load_public_resources() {
    global $eckb_kb_id;
	
	epkb_register_public_resources();

    // if this is not KB Main Page or Article Page or Category Archive page then do not load public resources
    if ( empty($eckb_kb_id) ) {
        return;
    }
	
	// if user has enough capability, then add Frontend Editor button to the admin panel but only for KB Main Page
	add_action( 'admin_bar_menu', 'epkb_add_admin_bar_button', 1000 );

	epkb_enqueue_public_resources();
}
add_action( 'wp_enqueue_scripts', 'epkb_load_public_resources', 500 );

/**
 * Register for FRONT-END pages using our plugin features
 */
function epkb_register_public_resources() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'epkb-public-styles', Echo_Knowledge_Base::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );

	if ( is_rtl() ) {
		wp_register_style( 'epkb-public-styles-rtl', Echo_Knowledge_Base::$plugin_url . 'css/public-styles-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	}
	
	wp_register_style( 'epkb-icon-fonts', Echo_Knowledge_Base::$plugin_url . 'css/epkb-icon-fonts' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_register_script( 'epkb-public-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_register_script( 'epkb-materialize', Echo_Knowledge_Base::$plugin_url . 'js/lib/materialize' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );

	$epkb_vars = array(
		'ajaxurl'                       => admin_url( 'admin-ajax.php', 'relative' ),
		'msg_try_again'                 => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'                => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (16)',
		'not_saved'                     => esc_html__( 'Error occurred', 'echo-knowledge-base' ). ' (6)',
		'unknown_error'                 => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (17)',
		'reload_try_again'              => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'                   => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'                => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'load_template'                 => esc_html__( 'Loading Template...', 'echo-knowledge-base' ),
		'nonce'                         => wp_create_nonce( "_wpnonce_epkb_ajax_action" )
	);
	
	wp_localize_script( 'epkb-public-scripts', 'epkb_vars', $epkb_vars );

}

/**
 * Output the admin-icon.css file for logged in users only.
 * This is to show KB Icons in the:
 *  - Top Admin Bar ( KB Icon, Help Dialog Icon )
 *  - Left Admin Sidebar ( Help Dialog Icon )
 * For more details, see admin_icon.scss comments.
 */

// Not required anymore, keep this here for now? Maybe we might reuse this.
/*function epkb_enqueue_admin_icon_resources() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style( 'epkb-admin-icon-style', Echo_Knowledge_Base::$plugin_url . 'css/admin-icon' . $suffix . '.css' );
}
add_action( 'admin_enqueue_scripts','epkb_enqueue_admin_icon_resources' );*/

/**
 * Queue for FRONT-END pages using our plugin features
 * @noinspection PhpUnusedParameterInspection
 * @param int $kb_id - legacy
 */
function epkb_enqueue_public_resources( $kb_id=0 ) {
	wp_enqueue_style( 'epkb-public-styles' );
	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-public-styles-rtl' );
	}

	wp_enqueue_script( 'epkb-public-scripts' );
	wp_enqueue_script( 'epkb-materialize' );  // scrollSpy for TOC

	epkb_enqueue_google_fonts();
	epkb_enqueue_the_content_scripts();
}
add_action( 'epkb_enqueue_scripts', 'epkb_enqueue_public_resources' ); // use this action in any place to add scripts $kb_id as a parameter

/**
 * Queue for FRONT-END pages using our plugin features
 */
function epkb_enqueue_font() {
	wp_enqueue_style( 'epkb-icon-fonts' );
}
add_action( 'epkb_enqueue_font_scripts', 'epkb_enqueue_font' ); // use this action in any place to add scripts $kb_id as a parameter


/**
 * BACK-END: KB Config page needs front-page CSS resources
 */
function epkb_kb_config_load_public_css() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-public-styles', Echo_Knowledge_Base::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	
	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-public-styles-rtl', Echo_Knowledge_Base::$plugin_url . 'css/public-styles-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	}

	$kb_id = EPKB_KB_Handler::get_current_kb_id();
	if ( empty($kb_id) ) {
		return;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

	echo epkb_frontend_kb_theme_styles_now( $kb_config );
}

/**
 * ADMIN-PLUGIN MENU PAGES (Plugin settings, reports, lists etc.)
 */
function epkb_load_admin_plugin_pages_resources() {
	global $pagenow;
	
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	
	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-admin-plugin-pages-rtl', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	}
	
	wp_enqueue_style( 'wp-color-picker' ); //Color picker
	wp_enqueue_script( 'epkb-admin-plugin-pages-ui', Echo_Knowledge_Base::$plugin_url . 'js/admin-ui' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-admin-plugin-pages-convert', Echo_Knowledge_Base::$plugin_url . 'js/admin-convert' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-admin-plugin-pages-scripts', Echo_Knowledge_Base::$plugin_url . 'js/admin-plugin-pages' . $suffix . '.js',
					array('jquery', 'jquery-ui-core','jquery-ui-dialog','jquery-effects-core','jquery-effects-bounce', 'jquery-ui-sortable'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-plugin-pages-scripts', 'epkb_vars', array(
					'msg_try_again'                 => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
					'error_occurred'                => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (11)',
					'not_saved'                 	=> esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (12)',
					'unknown_error'                 => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (13)',
					'reload_try_again'              => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
					'save_config'                   => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
					'input_required'                => esc_html__( 'Input is required', 'echo-knowledge-base' ),
					'sending_feedback'              => esc_html__('Sending feedback ...', 'echo-knowledge-base' ),
					'changing_debug'                => esc_html__('Changing debug ...', 'echo-knowledge-base' ),
					'help_text_coming'              => esc_html__('Help text is coming soon.', 'echo-knowledge-base' ),
					'load_template'                 => esc_html__('Loading Template...', 'echo-knowledge-base' ),
					'nonce'                         => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
					'msg_reading_posts'             => esc_html__('Reading posts', 'echo-knowledge-base') . '...',
					'msg_confirm_kb'                => esc_html__('Please confirm Knowledge Base to import into.', 'echo-knowledge-base'),
					'msg_confirm_backup'            => esc_html__('Please confirm you backed up your database or understand that import can potentially make undesirable changes.', 'echo-knowledge-base'),
					'msg_empty_post_type'            => esc_html__('Please select post type.', 'echo-knowledge-base'),
					'msg_nothing_to_convert'        => esc_html__('No posts to convert.', 'echo-knowledge-base'),
					'msg_select_article'            => esc_html__('Please select posts to convert.', 'echo-knowledge-base'),
					'msg_articles_converted'        => esc_html__('Articles converted', 'echo-knowledge-base'),
					'msg_converting'                => esc_html__('Converting articles, please wait...', 'echo-knowledge-base'),
				));
	
	// used by WordPress color picker  ( wpColorPicker() )
	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n',
			array(
				'clear'            =>   esc_html__( 'Reset', 'echo-knowledge-base' ),
				'clearAriaLabel'   =>   esc_html__( 'Reset color', 'echo-knowledge-base' ),
				'defaultString'    =>   esc_html__( 'Default', 'echo-knowledge-base' ),
				'defaultAriaLabel' =>   esc_html__( 'Select default color', 'echo-knowledge-base' ),
				'pick'             =>   '',
				'defaultLabel'     =>   esc_html__( 'Color value', 'echo-knowledge-base' ),
			));
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-jquery-ui-dialog' );

	// add for Category icon upload
	if ( $pagenow == 'term.php' || $pagenow == 'edit-tags.php' ) {
		wp_enqueue_media();
	}
}

// Old Wizards
function epkb_load_admin_kb_wizards_script() {
	
	global $pagenow;
	
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_script( 'epkb-admin-kb-config-menu-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-config-menu-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce', 'wp-color-picker'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-config-menu-script', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (13)',
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (5).', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (15)',
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'archive_page'          => esc_html__( 'Archive Page configuration is available only for KB Template. Switch on KB Template to continue.', 'echo-knowledge-base' ),
		'updating_preview'      => esc_html__( 'Updating page preview ...', 'echo-knowledge-base' ),
		'changing_config'       => esc_html__( 'Changing to selected configuration...', 'echo-knowledge-base' ),
		'switching_article_seq' => esc_html__( 'Switching article sequence ...', 'echo-knowledge-base' ),
		'preview'               => esc_html__( 'Preview', 'echo-knowledge-base' ),
		'load_template'         => esc_html__( 'Loading Template...', 'echo-knowledge-base' )
	));

	wp_enqueue_script( 'epkb-admin-kb-wizard-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-wizard-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce'), Echo_Knowledge_Base::$version );
	wp_enqueue_script( 'epkb-materialize', Echo_Knowledge_Base::$plugin_url . 'js/lib/materialize' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-wizard-script', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (14)',
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (5).', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (15)',
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'load_template'         => esc_html__('Loading Preview...', 'echo-knowledge-base' ),
		'wizard_help_images_path' => Echo_Knowledge_Base::$plugin_url . 'img/',
		'asea_wizard_help_images_path' => class_exists( 'Echo_Advanced_Search' ) && ! empty(Echo_Advanced_Search::$plugin_url) ? Echo_Advanced_Search::$plugin_url . 'img/' : '',
		'elay_wizard_help_images_path' => class_exists( 'Echo_Elegant_Layouts' ) && ! empty(Echo_Elegant_Layouts::$plugin_url) ? Echo_Elegant_Layouts::$plugin_url . 'img/' : '',
		'eprf_wizard_help_images_path' => class_exists( 'Echo_Article_Rating_And_Feedback' ) && ! empty(Echo_Article_Rating_And_Feedback::$plugin_url) ? Echo_Article_Rating_And_Feedback::$plugin_url . 'img/' : ''
	));

	add_filter('admin_body_class', 'epkb_admin_wizard_body_class' );
}

// To support KB admin pages with old design
function epkb_load_admin_kb_old_design_script() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'epkb-admin-kb-config-menu-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-config-menu-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce', 'wp-color-picker'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-config-menu-script', 'epkb_vars', array(
		'msg_try_again'         => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'        => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (13)',
		'not_saved'             => esc_html__( 'Error occurred - configuration NOT saved (5).', 'echo-knowledge-base' ),
		'unknown_error'         => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (15)',
		'reload_try_again'      => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'           => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'        => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'archive_page'          => esc_html__( 'Archive Page configuration is available only for KB Template. Switch on KB Template to continue.', 'echo-knowledge-base' ),
		'updating_preview'      => esc_html__( 'Updating page preview ...', 'echo-knowledge-base' ),
		'changing_config'       => esc_html__( 'Changing to selected configuration...', 'echo-knowledge-base' ),
		'switching_article_seq' => esc_html__( 'Switching article sequence ...', 'echo-knowledge-base' ),
		'preview'               => esc_html__( 'Preview', 'echo-knowledge-base' ),
		'load_template'         => esc_html__( 'Loading Template...', 'echo-knowledge-base' )
	));
	wp_enqueue_script( 'epkb-materialize', Echo_Knowledge_Base::$plugin_url . 'js/lib/materialize' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
}

// Setup Wizard
function epkb_load_admin_kb_setup_wizard_script() {
	
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	
	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-admin-plugin-pages-rtl', Echo_Knowledge_Base::$plugin_url . 'css/admin-plugin-pages-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	}
	
	wp_enqueue_script( 'epkb-admin-kb-setup-wizard-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-setup-wizard-script' . $suffix . '.js',
		array('jquery',	'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-bounce'), Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-admin-kb-setup-wizard-script', 'epkb_vars', array(
		'msg_try_again'             => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'            => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (14)',
		'not_saved'                 => esc_html__( 'Error occurred - configuration NOT saved (5).', 'echo-knowledge-base' ),
		'unknown_error'             => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (15)',
		'reload_try_again'          => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'input_required'            => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'load_template'             => esc_html__('Loading Preview...', 'echo-knowledge-base' ),
		'sending_error_report' 		=> esc_html__( 'Sending, please wait', 'echo-knowledge-base' ),
		'send_report_error' 	    => esc_html__( 'Could not submit the error.', 'echo-knowledge-base' ) . EPKB_Utilities::contact_us_for_support(),
		'setup_wizard_error_title'  => esc_html__( 'Setup Wizard encountered an error.', 'echo-knowledge-base' ),
		'setup_wizard_error_desc'   => esc_html__( 'We have detected an error. Please submit the issue so that we can help you fix it.', 'echo-knowledge-base' ),
		'wizard_help_images_path'   => Echo_Knowledge_Base::$plugin_url . 'img/',
		'need_help_url'             => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . EPKB_KB_Config_DB::DEFAULT_KB_ID . '&page=epkb-kb-need-help' ),
	));
}

// Article Edit Page
function epkb_load_admin_article_edit_script() {

	if ( EPKB_Utilities::get('post') == '' || EPKB_Utilities::get('action') != 'edit' ) {
		return;
	}

	$post_type = get_post_type( EPKB_Utilities::get( 'post', '', false ) );
	
	//Added only for KB Core
	if ( ! empty($post_type) && EPKB_KB_Handler::is_kb_post_type( $post_type ) && ! EPKB_Utilities::is_amag_on() ) {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		$depends = array(
				'wp-plugins',
				'wp-element',
				'wp-components'
			);
		
		if ( ! EPKB_Utilities::is_classic_editor_plugin_active() ) {
			$depends[] = 'wp-edit-post';
		}
		
		wp_enqueue_script('epkb-admin-kb-article-edit-script', Echo_Knowledge_Base::$plugin_url . 'js/admin-kb-article-edit-script' . $suffix . '.js', $depends	);
	}
}
//add_action('admin_enqueue_scripts','epkb_load_admin_article_edit_script');

// remove wordpress strings on certain pages
function epkb_admin_wizard_body_class( $classes ) {
	// Note: Add a leading space and a trailing space.
	$classes .= ' epkb-configuration-page ';
	return $classes;
}

/**
 * Add style for current KB template
 */
function epkb_frontend_kb_theme_styles() {
	global $eckb_kb_id;

	$kb_id = empty($eckb_kb_id) ? EPKB_KB_Handler::get_kb_id_from_kb_main_shortcode() : $eckb_kb_id;
	if ( empty( $kb_id ) ) {
		return;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

	echo epkb_frontend_kb_theme_styles_now( $kb_config );
}
add_action( 'wp_head', 'epkb_frontend_kb_theme_styles' );

/**
 * Certain styles need to be inserted in the header.
 *
 * @param $kb_config
 * @return string
 */
function epkb_frontend_kb_theme_styles_now( $kb_config ) {

	global $eckb_is_kb_main_page;

	$is_kb_main_page = ! empty($eckb_is_kb_main_page);

	// get any style from add-ons
	$add_on_output = apply_filters( 'eckb_frontend_kb_theme_style', '', $kb_config['id'], $is_kb_main_page );
	if ( empty($add_on_output) || ! is_string($add_on_output) )  {
		$add_on_output = '';
	}

	$kb_config['section_typography'] = array_merge( EPKB_Typography::$typography_defaults, $kb_config['section_typography'] );

	$kb_config['article_typography'] = array_merge( EPKB_Typography::$typography_defaults, $kb_config['article_typography'] );

	$kb_config['sidebar_section_subcategory_typography'] = array_merge( EPKB_Typography::$typography_defaults, $kb_config['sidebar_section_subcategory_typography'] );

	$output = '<style type="text/css" id="epkb-advanced-style">
		/* KB Core 
		-----------------------------------------------------------------------*/
		#epkb-content-container .epkb-nav-tabs .active:after {
			border-top-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active {
			background-color: ' . $kb_config['tab_nav_active_background_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active .epkb-category-level-1,
		#epkb-content-container .epkb-nav-tabs .active p {
			color: ' . $kb_config['tab_nav_active_font_color'] . '!important
		}
		#epkb-content-container .epkb-nav-tabs .active:before {
			border-top-color: ' . $kb_config['tab_nav_border_color'] . '!important
		}
		
		/* Section  Settings ( Basic, Tab, Category ) Layouts.
		-----------------------------------------------------------------------*/
		#epkb-main-page-container .epkb-category-level-2-3__cat-name {
		    font-family: ' . $kb_config['section_typography']['font-family'] . ' !important;
		    font-weight: ' . $kb_config['section_typography']['font-weight'] . ' !important;
		    font-size: ' . $kb_config['section_typography']['font-size'].'px' . ' !important;
		}
		
		#epkb-main-page-container .epkb-category-level-2-3 {
		    font-size: ' . $kb_config['section_typography']['font-size'].'px' . ' !important;
		 }
		
		/* Article list Settings ( Basic, Tab, Category ) Layouts.
		-----------------------------------------------------------------------*/
		#epkb-main-page-container .epkb-section-body .eckb-article-title__text {
		    font-family: ' . $kb_config['article_typography']['font-family'] . ' !important;
		    font-weight: ' . $kb_config['article_typography']['font-weight'] . ' !important;
		    font-size: ' . $kb_config['article_typography']['font-size'].'px' . ' !important;
		}
		#epkb-main-page-container .epkb-section-body .eckb-article-title__icon {
		    font-size: ' . $kb_config['article_typography']['font-size'].'px' . ' !important;
		}

        /* Article Page - Sidebar
		-----------------------------------------------------------------------*/
		#epkb-sidebar-container-v2 .epkb-category-level-2-3 .epkb-category-level-2-3__cat-name {
		    font-family: ' . $kb_config['sidebar_section_subcategory_typography']['font-family'] . ' !important;
		    font-weight: ' . $kb_config['sidebar_section_subcategory_typography']['font-weight'] . ' !important;
		    font-size: ' . $kb_config['sidebar_section_subcategory_typography']['font-size'].'px' . ' !important;
		    color: ' . $kb_config['sidebar_section_category_font_color'] . '!important
		}	
	';

	$output .= $add_on_output;

	$output .= '</style>';

	return $output;
}

/**
 * Load TOC classes to counter theme issues
 * @param $classes
 * @return array
 */
function epkb_front_end_body_classes( $classes ) {
	global $eckb_kb_id;

	// load only on article pages
	if ( empty($eckb_kb_id) )  {
		return $classes;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );

	// load only if TOC is active
	if ( 'on' != $kb_config['article_toc_enable'] ) {
		return $classes;
	}

	// get current post
	$post = isset($GLOBALS['post']) ? $GLOBALS['post'] : '';
	if ( empty($post) || ! $post instanceof WP_Post ) {
		return $classes;
	}

	// is this KB Main Page ?
	$eckb_is_kb_main_page = false;
	$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs( true );
	foreach ( $all_kb_configs as $one_kb_config ) {
		if ( ! empty($one_kb_config['kb_main_pages']) && is_array($one_kb_config['kb_main_pages']) &&
		     in_array($post->ID, array_keys($one_kb_config['kb_main_pages']) ) ) {
			$eckb_is_kb_main_page = true;
			break;  // found matching KB Main Page
		}
	}

	if ( $eckb_is_kb_main_page ) {
		return $classes;
	}

	$classes[] = 'eckb-front-end-body';

	return $classes;

}
add_filter( 'body_class','epkb_front_end_body_classes' );

// load style for Admin Article Page
function epkb_load_admin_article_page_styles() {
	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_style( 'epkb-admin-plugin-pages-styles', Echo_Knowledge_Base::$plugin_url . 'css/admin-article-page' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
}

/**
 * Register KB areas for widgets to be added to
 */
function epkb_register_kb_sidebar() {

	$kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids( true );
	foreach( $kb_ids as $kb_id ) {

		$widget_seq_num = count($kb_ids) > 1 ? ' #' . $kb_id : '';
		$widget_id = $kb_id == 1 ? 'eckb_articles_sidebar' : 'eckb_articles_sidebar_' . $kb_id;

		// add KB sidebar area
		register_sidebar(array(
			'name' => __('Echo KB' . $widget_seq_num . ' Articles Sidebar' , 'echo-knowledge-base'),
			'id' => $widget_id,
			'before_widget' => '<div id="eckb-%1$s" class="eckb-article-widget-sidebar-body__widget">',
			'after_widget' => '</div> <!-- end Widget -->',
			'before_title' => '<h4>',
			'after_title' => '</h4>'
		));
	}
}
add_action( 'widgets_init', 'epkb_register_kb_sidebar' );

/**
 * Add KB filters for other plugins to use
 */
add_filter( 'kb_core/kb_config/get_kb_configs', function() {
	return epkb_get_instance()->kb_config_obj->get_kb_configs();
} );

/**
 * Add KB filters for other plugins to use
 */
add_filter( 'kb_core/kb_config/get_kb_config', function( $kb_id ) {
	return epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
} );

/**
 * Add KB filters for other plugins to use
 */
add_filter( 'kb_core/kb_icons/get_category_icon', function( $term_id, $categories_icons ) {
	return EPKB_KB_Config_Category::get_category_icon( $term_id, $categories_icons );
}, 10, 2 );


/**************  Frontend Editor  *****************/

/**
 * Load scripts for Frontend Editor
 */
function epkb_load_front_end_editor() {
	global $eckb_kb_id, $post;

	$editor_page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();

	// do not load the Editor and thus the strip-down KB Main page if not necessary
	if ( empty($post->post_type ) || // not a page
		defined( 'DOING_AJAX' ) && DOING_AJAX || // return if we get page by ajax 
		! empty( $_REQUEST['elementor-preview'] ) || // elementor preview
		! empty( $_REQUEST['et_fb'] ) // if we are on DIVI page 
	) {
		return;
	}

	$post_id = is_object( $post ) && ! empty( $post->ID ) ? $post->ID : 0;

	// see this page is actually KB Main Page
	$eckb_kb_id = epkb_check_kb_main_page( $eckb_kb_id, $post_id );

	if ( empty($eckb_kb_id) ) {
		return;
	}

	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );
	if ( EPKB_Core_Utilities::is_frontend_editor_hidden( $kb_config ) != '' ) {
		return;
	}

	// is this frontend Editor page?
	if ( ! in_array( $editor_page_type, EPKB_Editor_Config_Base::EDITOR_PAGE_TYPES ) ) {
		return;
	}

	// add config from addons 
	$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
	if ( empty($kb_config)  || is_wp_error($kb_config) ) {
		return;
	}

	$config_settings = EPKB_Editor_Utilities::get_editor_settings( $editor_page_type );
	if ( empty($config_settings) ) {
		return;
	}

	EPKB_Error_Handler::add_assets();
	
	wp_enqueue_style( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'css/editor.css', array(), Echo_Knowledge_Base::$version );
	
	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-editor-rtl', Echo_Knowledge_Base::$plugin_url . 'css/editor-rtl.css', array(), Echo_Knowledge_Base::$version );
	}

	echo	"<script data-cfasync='false'>
				var epkb_editor_config = " . wp_json_encode( $config_settings, ENT_QUOTES ) . ";
			</script>";
}

/**
 * Add Frontend Editor option in the WordPress admin bar.
 * Fired by `admin_bar_menu` filter.
 * @param WP_Admin_Bar $wp_admin_bar
 */
function epkb_add_admin_bar_button( WP_Admin_Bar $wp_admin_bar ) {

	if ( ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
		return;
	}

	//epkb_enqueue_admin_icon_resources();

	global $eckb_kb_id;

	if ( ! empty( $eckb_kb_id ) ) {
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );

		// Check if Frontend Editor disabled
		if ( EPKB_Core_Utilities::is_frontend_editor_hidden( $kb_config ) ) {
			return;
		}
	}

	// show frontend Editor link on KB Main Page, KB Article Pages and Category Archive page that has at least one article
	$title = epkb_front_end_editor_title();
	if ( ! empty( $title ) ) {

		$url = EPKB_Core_Utilities::is_kb_flag( 'editor_backend_mode' ) ? admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#kb-design' ) : add_query_arg( [ 'action' => 'epkb_load_editor' ] );
		$wp_admin_bar->add_menu( array( 'id' => 'epkb-edit-mode-button', 'title' => $title, 'href' => $url ) );
		return;
	}
}

function epkb_front_end_editor_title() {
	
	$title = '';
	switch ( EPKB_Editor_Utilities::epkb_front_end_editor_type() ) {
		case 'article-page':
			$title = __( 'Edit KB Article Page', 'echo-knowledge-base' );
			break;
		case 'main-page':
			$title = __( 'Edit KB Main Page', 'echo-knowledge-base' );
			break;
		case 'archive-page':
			$title = __( 'Edit KB Archive Page', 'echo-knowledge-base' );
			break;
		case 'search-page':
			$title = __( 'Edit KB Search Page', 'echo-advanced-search' );
			break;
	}
	
	return $title;
}

function epkb_load_editor_styles() {
	global $eckb_kb_id, $post;

	if ( empty($post->post_type) || // not a page
		defined( 'DOING_AJAX' ) && DOING_AJAX || // return if we get page by ajax 
		! empty( $_REQUEST['elementor-preview'] ) || // elementor preview
		! empty( $_REQUEST['et_fb'] ) // if we are on DIVI page 
	) {
		return;
	}
	
	$post_id = is_object( $post ) && ! empty( $post->ID ) ? $post->ID : 0;
	
	$eckb_kb_id = epkb_check_kb_main_page( $eckb_kb_id, $post_id );
	if ( empty( $eckb_kb_id ) ) {
		return;
	}

	// is this frontend Editor page?
	$editor_page_type = EPKB_Editor_Utilities::epkb_front_end_editor_type();
	if ( ! in_array( $editor_page_type, EPKB_Editor_Config_Base::EDITOR_PAGE_TYPES ) ) {
		return;
	}

	// add config from addons
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $eckb_kb_id );
	$kb_config = apply_filters( 'eckb_kb_config', $kb_config );
	
	if ( empty($kb_config)  || is_wp_error($kb_config) ) {
		return;
	}
		
	$config_settings = EPKB_Editor_Utilities::get_editor_settings( $editor_page_type );
	if ( empty($config_settings) ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	$epkb_editor_params = array(
		'_wpnonce_apply_editor_changes' => wp_create_nonce( '_wpnonce_apply_editor_changes' ),
		'ajaxurl' 						=> admin_url( 'admin-ajax.php', 'relative' ),
		'kb_url' 						=> admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . $eckb_kb_id ),
		'epkb_editor_kb_id' 			=> $eckb_kb_id,
		'page_type' 					=> $editor_page_type,
		'turned_on'         			=> __( 'Hide KB Settings', 'echo-knowledge-base' ),
		'loading'           			=> __( 'Loading...', 'echo-knowledge-base' ),
		'turned_off'        			=> epkb_front_end_editor_title(),
		'default_header'    			=> __( 'Settings panel', 'echo-knowledge-base' ),
		'epkb_name'         			=> __( 'Echo Knowledge Base', 'echo-knowledge-base' ),
		'tab_content'       			=> __( 'Content', 'echo-knowledge-base' ),
		'tab_style'         			=> __( 'Style', 'echo-knowledge-base' ),
		'tab_features'      			=> __( 'Features', 'echo-knowledge-base' ),
		'tab_advanced'      			=> __( 'Advanced', 'echo-knowledge-base' ),
		'tab_global'      				=> __( 'General Settings', 'echo-knowledge-base' ),
		'tab_hidden'      				=> __( 'Disabled Sections', 'echo-knowledge-base' ),
		'save_button'       			=> __( 'Save', 'echo-knowledge-base' ),
		'exit_button'       			=> __( 'Exit Editor', 'echo-knowledge-base' ),
		'clear_modal_notice' 			=> __( 'Click on any page element to change its settings', 'echo-knowledge-base' ),
		'no_settings'     				=> __( 'This zone have no settings yet', 'echo-knowledge-base' ),
		'checkbox_on'    				=> __( 'Yes', 'echo-knowledge-base' ),
		'checkbox_off'    				=> __( 'No', 'echo-knowledge-base' ),
		'wrong_dimensions' 				=> __( 'Invalid dimensions', 'echo-knowledge-base' ),
		'left_panel' 					=> __( 'Left Panel', 'echo-knowledge-base' ),
		'right_panel' 					=> __( 'Right Panel', 'echo-knowledge-base' ),
		'edit_button' 					=> __( 'Edit', 'echo-knowledge-base' ),
		'preopen_zone' 					=> EPKB_Utilities::post( 'preopen_zone', '' ),
		'preopen_setting' 				=> EPKB_Utilities::post( 'preopen_setting', '' ),
		'settings_html' 				=> EPKB_Editor_View::get_editor_settings_html( $kb_config ),
		'menu_links_html'				=> EPKB_Editor_View::get_editor_modal_menu_links( $editor_page_type, $kb_config ),
		'urls_and_slug' 				=> __( 'URLs and Slug', 'echo-knowledge-base' ),
		'urls_and_slug_url'				=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#settings__kb-urls' ),
		'order_categories' 				=> __( 'Order Categories and Articles', 'echo-knowledge-base' ),
		'order_categories_url'			=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#settings__order-articles' ),
		'rename_kb' 					=> __( 'Rename KB Name and Title', 'echo-knowledge-base' ),
		'rename_kb_url'					=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration#overview' ),
		'theme_link' 					=> __( 'Theme Compatibility Mode', 'echo-knowledge-base' ),
		'layouts_link' 					=> __( 'Layouts', 'echo-knowledge-base' ),
		'color_value' 					=> __( 'Color value', 'echo-knowledge-base' ),
		'select_color' 					=> __( 'Select Color', 'echo-knowledge-base' ),
		'default' 						=> __( 'Default', 'echo-knowledge-base' ),
		'inherit' 						=> __( 'Inherit', 'echo-knowledge-base' ),
		'select_default_color' 			=> __( 'Select default color', 'echo-knowledge-base' ),
		'clear' 						=> __( 'Clear', 'echo-knowledge-base' ),
		'clear_color' 					=> __( 'Clear color', 'echo-knowledge-base' ),
		'sidebar_settings'				=> __( 'The Sidebar setting can be changed on the article page.', 'echo-knowledge-base' ),
		'navigation' 					=> __( 'Navigation', 'echo-knowledge-base' ),
		'enabled_list' 					=> __( 'Enabled Sections', 'echo-knowledge-base' ),
		'enable_disable_sections_link' 	=> __( 'Disabled Sections', 'echo-knowledge-base' ),
		'all_zones_active' 				=> __( 'All Sections are enabled', 'echo-knowledge-base' ),
		'edit_zone' 					=> __( 'Edit Section', 'echo-knowledge-base' ),
		'need_help' 					=> __( 'Need Help', 'echo-knowledge-base' ),
		'sending_error_report' 			=> __( 'Sending, please wait', 'echo-knowledge-base' ),
		'send_report_error' 			=> __( 'Error occurred', 'echo-knowledge-base' ) . ' - ' . EPKB_Utilities::contact_us_for_support(),
		'timeout2_error' 				=> EPKB_Error_Handler::timeout2_error(),
		'other_error_found' 			=> EPKB_Error_Handler::other_error_found(),
		'csr_error' 					=> EPKB_Error_Handler::get_csr_error_text(),
		'ns_error_unexpected_error' 	=> EPKB_Error_Handler::get_ns_error_text(),
		'wrong_select' 					=> __( 'No value to select', 'echo-knowledge-base' ),
		'article_header_rows' 			=> __( 'Article Header Rows', 'echo-knowledge-base' ),
		'typography_defaults' 			=> EPKB_Typography::$typography_defaults,
		'typography_fonts' 				=> EPKB_Typography::$font_data, 
		'typography_title' 				=> __( 'Typograhy', 'echo-knowledge-base' ),
		'typography_font_family' 		=> __( 'Font Family', 'echo-knowledge-base' ),
		'typography_font_size' 			=> __( 'Font Size (px)', 'echo-knowledge-base' ),
		'typography_font_weight' 		=> __( 'Font Weight', 'echo-knowledge-base' ),
		'zone_absent_error'				=> __( 'Cannot open settings for', 'echo-knowledge-base' ),
		'zone_disabled_error'			=> __( 'Cannot open settings because the section is disabled. You can turn it on here', 'echo-knowledge-base' ) . ':',
		'zone_disabled_text'			=> __( 'Settings Zone was disabled. You can enable it back.', 'echo-knowledge-base' ),
		'config_backend_mode_link'  	=> admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) . '&page=epkb-kb-configuration&action=enable_editor_backend_mode&_wpnonce_manage_kbs=' . wp_create_nonce( '_wpnonce_manage_kbs' ) . '#kb-design' ),
	);

	$epkb_editor_params = apply_filters( 'epkb_editor_localize', $epkb_editor_params );
	$epkb_editor_addon_data = apply_filters( 'epkb_editor_addon_data', array(), $kb_config );   // Advanced Search presets

	wp_register_style( 'epkb-js-error-handlers', Echo_Knowledge_Base::$plugin_url . 'css/error-handlers' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-js-error-handlers' ) );

	// Should be before all else scripts
	wp_register_script( 'epkb-js-error-handlers', Echo_Knowledge_Base::$plugin_url . 'js/error-handlers' .  $suffix . '.js', array(), Echo_Knowledge_Base::$version );
	wp_print_scripts( array( 'epkb-js-error-handlers' ) );

	wp_register_style( 'epkb-editor-ui', Echo_Knowledge_Base::$plugin_url . 'css/editor-ui' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-editor-ui' ) );

	wp_register_style( 'epkb-public-styles', Echo_Knowledge_Base::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-public-styles' ) );

	if ( is_rtl() ) {
		wp_register_style( 'epkb-public-styles-rtl', Echo_Knowledge_Base::$plugin_url . 'css/public-styles-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		wp_print_styles( array( 'epkb-public-styles-rtl' ) );
	}

	wp_register_style( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'css/editor' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-editor' ) );

	if ( is_rtl() ) {
		wp_register_style( 'epkb-editor-rtl', Echo_Knowledge_Base::$plugin_url . 'css/editor-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		wp_print_styles( array( 'epkb-editor-rtl' ) );
	}

	wp_register_script( 'iris', admin_url() . 'js/iris.min.js' );
	wp_print_scripts( array( 'jquery', 'jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-slider', 'jquery-ui-draggable','jquery-touch-punch', 'iris' ) );

	wp_register_script( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'js/editor' . $suffix . '.js', array(), Echo_Knowledge_Base::$version );
	wp_add_inline_script( 'epkb-editor', '
		var epkb_editor_config = ' . wp_json_encode( $config_settings, ENT_QUOTES ) . ';
		var epkb_editor = ' . wp_json_encode( $epkb_editor_params, ENT_QUOTES ) . ';
		var epkb_editor_font_links = ' . wp_json_encode( EPKB_Typography::$google_fonts_10_links, ENT_QUOTES ) . ';
		var epkb_editor_addon_data = ' . wp_json_encode( $epkb_editor_addon_data, ENT_QUOTES ) . ';' );
	wp_print_scripts( array( 'epkb-editor' ) );

	wp_register_script( 'epkb-color-picker', Echo_Knowledge_Base::$plugin_url . 'js/lib/color-picker' . $suffix . '.js', array(), Echo_Knowledge_Base::$version );
	wp_print_scripts( array( 'epkb-color-picker' ) );
}

function epkb_check_kb_main_page( $eckb_kb_id, $post_id ) {

	if ( ! empty($eckb_kb_id) ) {
		return $eckb_kb_id;
	}

	$all_kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids();
	foreach ( $all_kb_ids as $kb_id ) {
		$kb_main_pages = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'kb_main_pages' );
		if ( empty( $kb_main_pages ) || ! is_array($kb_main_pages) ) {
			continue;
		}

		if ( isset($kb_main_pages[$post_id]) ) {
			$kb_id = epkb_get_instance()->kb_config_obj->get_value( $kb_id, 'id', null );
			if ( empty($kb_id) ) {
				return $eckb_kb_id;
			}

			return $kb_id;
		}
	}

	return $eckb_kb_id;
}

/**
 * Enqueue fonts that are configured in KB config
 */
function epkb_enqueue_google_fonts() {
	global $eckb_kb_id;

	$kb_id = empty( $eckb_kb_id ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $eckb_kb_id;
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
	foreach ( $kb_config as $name => $value ) {
		if ( is_array( $value ) && ! empty( $value['font-family'] ) ) {
			$font_link = EPKB_Typography::get_google_font_link( $value['font-family'] );
			if ( ! empty($font_link) ) {
				wp_enqueue_style( 'epkb-font-' . sanitize_title( $value['font-family']), $font_link );
			}
		}
	}
}

/**
 * Enqueue resources on Pages with KB widget
 */
function epkb_load_public_resources_enqueue() {

	wp_enqueue_style( 'epkb-public-styles' );

	if ( is_rtl() ) {
		wp_enqueue_style( 'epkb-public-styles-rtl' );
	}

	wp_enqueue_style( 'wp-jquery-ui-dialog' );
	wp_enqueue_script( 'epkb-public-scripts');
	do_action( 'epkb_enqueue_font_scripts' );
}

/**
 * Load assets to fix double article content
 * - Only admins
 * - Only if selected theme template
 * - Only if not applied yet
 * - One time fix - will not work if applied once
 */
function epkb_enqueue_the_content_scripts() {

	// for KB article, ignore if not post, is archive or current theme with any layout
	$post = empty( $GLOBALS['post'] ) ? '' : $GLOBALS['post'];
	if ( empty( $post ) || ! $post instanceof WP_Post || empty( $post->post_type ) || is_archive() || ! is_main_query() ) {
		return;
	}

	// exit if NOT KB Article URL
	if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
		return;
	}

	// we have KB Article
	$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
	if ( is_wp_error( $kb_id ) ) {
		return;
	}

	if ( ! EPKB_Utilities::is_new_user_2( '9.2.0' ) ) {
		return;
	}

	// if flag is already set - don't need the scripts any more
	if ( EPKB_Core_Utilities::is_kb_flag( 'epkb_the_content_fix' ) ) {
		return;
	}

	// initialize KB config to be accessible to templates
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );

	// check template used to prevent the_content filtering for our KB template
	if ( empty( $kb_config['templates_for_kb'] ) || $kb_config['templates_for_kb'] != 'current_theme_templates' ) {
		return;
	}

	// fix the content issue only if author, editor, admin is reviewing the page
	if ( ! EPKB_Admin_UI_Access::is_user_admin_editor_author() ) {
		return;
	}

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script( 'epkb-public-the-content-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-the-content' . $suffix . '.js', [], Echo_Knowledge_Base::$version );
	wp_localize_script( 'epkb-public-the-content-scripts', 'epkb_the_content_i18n', array(
		'nonce'         => wp_create_nonce( "_wpnonce_epkb_ajax_action" ),
		'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
	));
}

/**
 * Use for backend mode iframe
 */
function epkb_load_editor_backend_mode_styles_inline() {

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

	wp_register_style( 'epkb-public-styles', Echo_Knowledge_Base::$plugin_url . 'css/public-styles' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-public-styles' ) );

	if ( is_rtl() ) {
		wp_register_style( 'epkb-public-styles-rtl', Echo_Knowledge_Base::$plugin_url . 'css/public-styles-rtl' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
		wp_print_styles( array( 'epkb-public-styles-rtl' ) );
	}

	wp_register_style( 'epkb-icon-fonts', Echo_Knowledge_Base::$plugin_url . 'css/epkb-icon-fonts' . $suffix . '.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-icon-fonts' ) );

	wp_register_script( 'epkb-public-scripts', Echo_Knowledge_Base::$plugin_url . 'js/public-scripts' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );

	$epkb_vars = array(
		'ajaxurl'                       => admin_url( 'admin-ajax.php', 'relative' ),
		'msg_try_again'                 => esc_html__( 'Please try again later.', 'echo-knowledge-base' ),
		'error_occurred'                => esc_html__( 'Error occurred', 'echo-knowledge-base' ) . ' (16)',
		'not_saved'                     => esc_html__( 'Error occurred', 'echo-knowledge-base' ). ' (6)',
		'unknown_error'                 => esc_html__( 'Unknown error', 'echo-knowledge-base' ) . ' (17)',
		'reload_try_again'              => esc_html__( 'Please reload the page and try again.', 'echo-knowledge-base' ),
		'save_config'                   => esc_html__( 'Saving configuration', 'echo-knowledge-base' ),
		'input_required'                => esc_html__( 'Input is required', 'echo-knowledge-base' ),
		'load_template'                 => esc_html__( 'Loading Template...', 'echo-knowledge-base' ),
		'nonce'                         => wp_create_nonce( "_wpnonce_epkb_ajax_action" )
	);

	wp_add_inline_script( 'epkb-public-scripts', '
		var epkb_vars = ' . wp_json_encode( $epkb_vars, ENT_QUOTES ) . ';' );

	wp_print_scripts( array( 'jquery', 'epkb-public-scripts' ) );

	wp_register_script( 'epkb-materialize', Echo_Knowledge_Base::$plugin_url . 'js/lib/materialize' . $suffix . '.js', array('jquery'), Echo_Knowledge_Base::$version );
	wp_print_scripts( array( 'epkb-materialize' ) );

	wp_register_style( 'epkb-editor', Echo_Knowledge_Base::$plugin_url . 'css/editor.css', array(), Echo_Knowledge_Base::$version );
	wp_print_styles( array( 'epkb-editor' ) );

	if ( is_rtl() ) {
		wp_register_style( 'epkb-editor-rtl', Echo_Knowledge_Base::$plugin_url . 'css/editor-rtl.css', array(), Echo_Knowledge_Base::$version );
		wp_print_styles( array( 'epkb-editor-rtl' ) );
	}

	global $eckb_kb_id;

	$kb_id = empty( $eckb_kb_id ) ? EPKB_KB_Config_DB::DEFAULT_KB_ID : $eckb_kb_id;
	$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
	foreach ( $kb_config as $name => $value ) {
		if ( is_array( $value ) && ! empty( $value['font-family'] ) ) {
			$font_link = EPKB_Typography::get_google_font_link( $value['font-family'] );
			if ( ! empty($font_link) ) {
				wp_register_style( 'epkb-font-' . sanitize_title( $value['font-family']), $font_link );
				wp_print_styles( array( 'epkb-font-' . sanitize_title( $value['font-family']) ) );
			}
		}
	}

	// for addons
	do_action( 'epkb_load_editor_backend_mode_styles_inline' );
}