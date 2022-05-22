<?php

/**
 * Lists all KB configuration settings and adds filter to get configuration from add-ons.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Specs {

	private static $cached_specs = array();

	public static function get_defaults() {
		return array(
			'label'       => __( 'Label', 'echo-knowledge-base' ),
			'type'        => EPKB_Input_Filter::TEXT,
			'mandatory'   => true,
			'max'         => '20',
			'min'         => '3',
			'options'     => array(),
			'internal'    => false,
			'default'     => ''
		);
	}

	public static function get_categories_display_order() {
		return array( 'alphabetical-title' => __( 'Alphabetical by Name', 'echo-knowledge-base' ),
							 'created-date' => __( 'Chronological by Date Created or Published', 'echo-knowledge-base' ),
							 'user-sequenced' => __( 'Custom - Drag and Drop Categories', 'echo-knowledge-base' ) );
	}

	public static function get_articles_display_order() {
		return array( 'alphabetical-title' => __( 'Alphabetical by Title', 'echo-knowledge-base' ),
		                     'created-date' => __( 'Chronological by Date Created or Published', 'echo-knowledge-base' ),
                             'modified-date' => __( 'Chronological by Date Modified', 'echo-knowledge-base' ),
		                     'user-sequenced' => __( 'Custom - Drag and Drop articles', 'echo-knowledge-base' ) );
	}

	private static $sidebar_component_priority_defaults = array(
		'kb_sidebar_left' => '0',
		'kb_sidebar_right' => '0',
		'toc_left' => '0',
		'toc_content' => '0',
		'toc_right' => '1',
		'nav_sidebar_left' => '',   // FUTURE TODO set to '0'; '' means not initialized
		'nav_sidebar_right' => '0'
	);

	public static function add_sidebar_component_priority_defaults( $article_sidebar_component_priority ) {
		return array_merge(self::$sidebar_component_priority_defaults, $article_sidebar_component_priority);
	}

	public static function get_sidebar_component_priority_names() {
		return array_keys(self::$sidebar_component_priority_defaults);
	}

	/**
	 * Defines how KB configuration fields will be displayed, initialized and validated/sanitized
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @param int $kb_id is the ID of knowledge base to get default config for
	 * @return array with KB config specification
	 */
	public static function get_fields_specification( $kb_id ) {

		// if kb_id is invalid use default KB
		if ( ! EPKB_Utilities::is_positive_int( $kb_id ) ) {
			EPKB_Logging::add_log( 'setting kb_id to 0 because kb_id is not positive int', $kb_id );
			$kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
		}

		// retrieve settings if already cached
		if ( ! empty(self::$cached_specs[$kb_id]) && is_array(self::$cached_specs[$kb_id]) ) {
			return self::$cached_specs[$kb_id];
		}


		// all CORE settings are listed here; 'name' used for HTML elements
		$config_specification = array(

			/******************************************************************************
			 *
			 *  Internal settings
			 *
			 ******************************************************************************/
			'id' => array(
				'label'       => 'kb_id',
				'type'        => EPKB_Input_Filter::ID,
				'internal'    => true,
				'default'     => $kb_id
			),
			'status' => array(
				'label'       => 'status',
				'type'        => EPKB_Input_Filter::ENUMERATION,
				'options'     => array( EPKB_KB_Status::BLANK, EPKB_KB_Status::PUBLISHED, EPKB_KB_Status::ARCHIVED ),
				'internal'    => true,
				'default'     => EPKB_KB_Status::PUBLISHED
			),
			'kb_main_pages' => array(
				'label'       => 'kb_main_pages',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => array()
			),
			'article_sidebar_component_priority' => array(
				'label'       => 'article_sidebar_component_priority',
				'type'        => EPKB_Input_Filter::INTERNAL_ARRAY,
				'internal'    => true,
				'default'     => self::$sidebar_component_priority_defaults
			),
			'article_nav_sidebar_type_left' => array(
				'label'       => __( 'Sidebar Navigation', 'echo-knowledge-base' ),
				'name'        => 'article_nav_sidebar_type_left',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-nav-sidebar-none' => __( 'None', 'echo-knowledge-base' ),
					'eckb-nav-sidebar-categories' => __( 'Top Categories', 'echo-knowledge-base' ),
					'eckb-nav-sidebar-v1' => __( 'Categories and Articles', 'echo-knowledge-base' ), // core or elay
					// FUTURE example:	'eckb-nav-sidebar-v2' => __( 'Advanced Navigation', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-nav-sidebar-v1'
			),
			'article_nav_sidebar_type_right' => array(
				'label'       => __( 'Sidebar Navigation', 'echo-knowledge-base' ),
				'name'        => 'article_nav_sidebar_type_right',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-nav-sidebar-none' => __( 'None', 'echo-knowledge-base' ),
					'eckb-nav-sidebar-categories' => __( 'Top Categories', 'echo-knowledge-base' ),
					'eckb-nav-sidebar-v1' => __( 'Categories and Articles', 'echo-knowledge-base' ), // core or elay
					// FUTURE example:	'eckb-nav-sidebar-v2' => __( 'Advanced Navigation', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-nav-sidebar-none'
			),


			/******************************************************************************
			 *
			 *  Overview
			 *
			 ******************************************************************************/
			'kb_name' => array(
				'label'       => __( 'CPT Name', 'echo-knowledge-base' ),
				'name'        => 'kb_name',
				'size'        => '50',
				'max'         => '70',
				'min'         => '1',
				'reload'      => true,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == 1 ? '' : ' ' . $kb_id)
			),
			'kb_articles_common_path' => array(
				'label'       => __( 'Common Path for Articles', 'echo-knowledge-base' ),
				'name'        => 'kb_articles_common_path',
				'size'        => '20',
				'max'         => '70',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::URL,
				'default'     => EPKB_KB_Handler::get_default_slug( $kb_id )
			),
			'kb_main_page_layout' => array(
				'label'       => __( 'Main Page Layout', 'echo-knowledge-base' ),
				'name'        => 'kb_main_page_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => EPKB_KB_Config_Layouts::get_main_page_layout_name_value(),
				'default'     => EPKB_KB_Config_Layout_Basic::LAYOUT_NAME,
			),
			'kb_article_page_layout' => array( // DEPRICATED
					'label'       => __( 'Article Page Layout', 'echo-knowledge-base' ),
					'name'        => 'kb_article_page_layout',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => ['Article' => 'Article', 'Sidebar' => 'Sidebar'],
					'default'     => 'Article',
			),
			'kb_sidebar_location' => array(  // TODO remove after archive page done
					'label'       => __( 'Article Sidebar Location', 'echo-knowledge-base' ),
					'name'        => 'kb_sidebar_location',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => array(
							'left-sidebar'   => is_rtl() ? _x( 'Right Sidebar', 'echo-knowledge-base' ) : _x( 'Left Sidebar', 'echo-knowledge-base' ),
							'right-sidebar'  => is_rtl() ? _x( 'Left Sidebar', 'echo-knowledge-base' ) : _x( 'Right Sidebar', 'echo-knowledge-base' ),
							'no-sidebar'     => _x( 'No Sidebar', 'echo-knowledge-base' ) ),
					'default'     => 'no-sidebar'
			),
			'article-left-sidebar-toggle' => array(
				'label'       => is_rtl() ? __( 'Enable Right Sidebar', 'echo-knowledge-base' ) : __( 'Enable Left Sidebar', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article-right-sidebar-toggle' => array(
				'label'       => is_rtl() ? __( 'Enable Left Sidebar', 'echo-knowledge-base' ) : __( 'Enable Right Sidebar', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),


			/******************************************************************************
			 *
			 *  ARTICLE STRUCTURE v2
			 *
			 ******************************************************************************/

			'article-structure-version' => array(
					'label'       => __( 'Article Page Structure', 'echo-knowledge-base' ),
					'name'        => 'article-structure-version',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     =>
							array(
									'version-1' => __( 'Legacy Style', 'echo-knowledge-base' ),
									'version-2' => __( 'Modern Style (Recommended)', 'echo-knowledge-base' ),
							),
					'default'     => 'version-2',
			),

			// Article Version 2 settings
			'article-container-desktop-width-v2' => array(
				'label'       => __( 'Width', 'echo-knowledge-base' ),
				'name'        => 'article-container-desktop-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 100
			),
			'article-container-desktop-width-units-v2' => array(
				'label'       => __( 'Width - Units', 'echo-knowledge-base' ),
				'name'        => 'article-container-desktop-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),
			'article-container-tablet-width-v2' => array(
				'label'       => __( 'Width (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-container-tablet-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 100
			),
			'article-container-tablet-width-units-v2' => array(
				'label'       => __( 'Width - Units(Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-container-tablet-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),

			// Article Version 2 - Body Container
			'article-body-desktop-width-v2' => array(
				'label'       => __( 'Width', 'echo-knowledge-base' ),
				'name'        => 'article-body-desktop-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 1140
			),
			'article-body-desktop-width-units-v2' => array(
				'label'       => __( 'Width Units', 'echo-knowledge-base' ),
				'name'        => 'article-body-desktop-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => 'px'
			),
			'article-body-tablet-width-v2' => array(
				'label'       => __( 'Width (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-body-tablet-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 100
			),
			'article-body-tablet-width-units-v2' => array(
				'label'       => __( 'Width - Units (Tablets)', 'echo-knowledge-base' ),
				'name'        => 'article-body-tablet-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'    => _x( '%',  'echo-knowledge-base' )
				),
				'default'     => '%'
			),

			// Article Version 2 - Left Sidebar
			'article-left-sidebar-desktop-width-v2' => array(
				'label'       => __( 'Desktop ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-desktop-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article-left-sidebar-tablet-width-v2' => array(
				'label'       => __( 'Tablet ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-tablet-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 20
			),
			'article-left-sidebar-padding-v2_top' => array(
				'label'       => __( 'Top ', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_top',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-padding-v2_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_right',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-padding-v2_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_bottom',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-padding-v2_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-padding-v2_left',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-left-sidebar-background-color-v2' => array(
				'label'       => __( 'Background Color', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-left-sidebar-starting-position' => array(
				'label'       => __( 'Top Offset ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-starting-position',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 0
			),
			'article-left-sidebar-match' => array(
				'label'       => __( 'Align sidebar to article content', 'echo-knowledge-base' ),
				'name'        => 'article-left-sidebar-match',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			
			// Article Version 2 - Article Content
			'article-content-desktop-width-v2' => array(
				'label'       => __( 'Desktop Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-content-desktop-width-v2',
				'max'         => 100,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 80
			),
			'article-content-tablet-width-v2' => array(
				'label'       => __( 'Tablet Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-content-tablet-width-v2',
				'max'         => 100,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 60
			),
			'article-content-padding-v2' => array(
				'label'       => __( 'Content Area Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-content-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 20
			),
			'article-content-background-color-v2' => array(
				'label'       => __( 'Content Area Background', 'echo-knowledge-base' ),
				'name'        => 'article-content-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-meta-typography' => array(
				'label'       => __( 'Meta Typography', 'echo-knowledge-base' ),
				'name'        => 'article-meta-typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article-meta-color' => array(
				'label'       => __( 'Meta Color', 'echo-knowledge-base' ),
				'name'        => 'article-meta-color',
				'size'        => '10',
                'max'         => '7',
                'min'         => '7',
                'type'        => EPKB_Input_Filter::COLOR_HEX,
                'default'     => '#000000'
			),

			// Article Version 2 - Right Sidebar
			'article-right-sidebar-desktop-width-v2' => array(
				'label'       => __( 'Desktop ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-desktop-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 20
			),
			'article-right-sidebar-tablet-width-v2' => array(
				'label'       => __( 'Tablet ( % )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-tablet-width-v2',
				'max'         => 80,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 20
			),
			'article-right-sidebar-padding-v2_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_top',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-padding-v2_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_right',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-padding-v2_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_bottom',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-padding-v2_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-padding-v2_left',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'article-right-sidebar-background-color-v2' => array(
				'label'       => is_rtl() ? __( 'Left Sidebar Background', 'echo-knowledge-base' ) : __( 'Right Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article-right-sidebar-starting-position' => array(
				'label'       => __( 'Top Offset ( px )', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-starting-position',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 0
			),
			'article-right-sidebar-match' => array(
				'label'       => __( 'Align sidebar to article content', 'echo-knowledge-base' ),
				'name'        => 'article-right-sidebar-match',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			// Article Version 2 - Advanced
			'article-mobile-break-point-v2' => array(
				'label'       => __( 'Mobile (px)', 'echo-knowledge-base' ),
				'name'        => 'article-mobile-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 768
			),
			'article-tablet-break-point-v2' => array(
				'label'       => __( 'Tablet (px)', 'echo-knowledge-base' ),
				'name'        => 'article-tablet-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 1025
			),


			/******************************************************************************
			 *
			 *  ARTICLE SIDEBAR V1
			 *
			 ******************************************************************************/

			/***  Article Sidebar -> General ***/

			'sidebar_side_bar_height_mode' => array(
				'label'       => __( 'Height Mode', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_side_bar_height_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'side_bar_no_height' => __( 'Variable', 'echo-elegant-layouts' ),
					'side_bar_fixed_height' => __( 'Fixed (Scrollbar)', 'echo-elegant-layouts' ) ),
				'default'     => 'side_bar_no_height'
			),
			'sidebar_side_bar_height' => array(
				'label'       => __( 'Height ( px )', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_side_bar_height',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => '350'
			),
			'sidebar_scroll_bar' => array(
				'label'       => __( 'Scroll Bar style', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_scroll_bar',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'slim_scrollbar'    => _x( 'Slim','echo-elegant-layouts' ),
					'default_scrollbar' => _x( 'Default', 'echo-elegant-layouts' ) ),
				'default'     => 'slim_scrollbar'
			),

			'sidebar_section_category_typography' => array(
				'label'       => __( 'Category Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_category_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '18',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_section_category_typography_desc' => array(
				'label'       => __( 'Category Description Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_category_typography_desc',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '16',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_section_body_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_body_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '16',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_top_categories_collapsed' => array(
				'label'       => __( 'Top Categories Collapsed', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_top_categories_collapsed',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'sidebar_nof_articles_displayed' => array(
				'label'       => __( 'Number of Articles Listed', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_nof_articles_displayed',
				'max'         => '200',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 15,
			),
			'sidebar_show_articles_before_categories' => array(
				'label'       => __( 'Show Articles', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_show_articles_before_categories',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => __( 'Before Categories', 'echo-elegant-layouts' ),
					'off' => __( 'After Categories', 'echo-elegant-layouts' ),
				),
				'default'     => 'off'
			),
			'sidebar_expand_articles_icon' => array(
				'label'       => __( 'Icon to Expand/Collapse Articles', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_expand_articles_icon',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'ep_font_icon_plus_box' => _x( 'Plus Box', 'icon type', 'echo-elegant-layouts' ),
										'ep_font_icon_plus' => _x( 'Plus Sign', 'icon type', 'echo-elegant-layouts' ),
										'ep_font_icon_right_arrow' => _x( 'Arrow Triangle', 'icon type', 'echo-elegant-layouts' ),
										'ep_font_icon_arrow_carrot_right' => _x( 'Arrow Caret', 'icon type', 'echo-elegant-layouts' ),
										'ep_font_icon_arrow_carrot_right_circle' => _x( 'Arrow Caret 2', 'icon type', 'echo-elegant-layouts' ),
										'ep_font_icon_folder_add' => _x( 'Folder', 'icon type', 'echo-elegant-layouts' ) ),
				'default'     => 'ep_font_icon_arrow_carrot_right'
			),

			/***  Article Sidebar -> Articles Listed in Sub-Category ***/

			'sidebar_section_head_alignment' => array(
				'label'       => __( 'Category Text Alignment', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left' => __( 'Left', 'echo-elegant-layouts' ),
					'center' => __( 'Centered', 'echo-elegant-layouts' ),
					'right' => __( 'Right', 'echo-elegant-layouts' )
				),
				'default'     => 'left'
			),
			'sidebar_section_head_padding_top' => array(
				'label'       => __( 'Top', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_padding_top',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_head_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_padding_bottom',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_head_padding_left' => array(
				'label'       => __( 'Left', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_padding_left',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_head_padding_right' => array(
				'label'       => __( 'Right', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_padding_right',
				'max'         => '20',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_desc_text_on' => array(
				'label'       => __( 'Category Description', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_desc_text_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'sidebar_section_border_radius' => array(
				'label'       => __( 'Radius', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_border_radius',
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 5
			),
			'sidebar_section_border_width' => array(
				'label'       => __( 'Width', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1
			),
			'sidebar_section_box_shadow' => array(
				'label'       => __( 'Navigation Shadow', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_box_shadow',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_shadow' => __( 'No Shadow', 'echo-elegant-layouts' ),
					'section_light_shadow' => __( 'Light Shadow', 'echo-elegant-layouts' ),
					'section_medium_shadow' => __( 'Medium Shadow', 'echo-elegant-layouts' ),
					'section_bottom_shadow' => __( 'Bottom Shadow', 'echo-elegant-layouts' )
				),
				'default'     => 'section_medium_shadow'
			),
			'sidebar_section_divider' => array(
				'label'       => __( 'On/Off', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_divider',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'sidebar_section_divider_thickness' => array(
				'label'       => __( 'Thickness ( px )', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_divider_thickness',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 1
			),
			'sidebar_section_box_height_mode' => array(
				'label'       => __( 'Height Mode', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_box_height_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'section_no_height' => __( 'Variable', 'echo-elegant-layouts' ),
					'section_min_height' => __( 'Minimum', 'echo-elegant-layouts' ),
					'section_fixed_height' => __( 'Maximum', 'echo-elegant-layouts' )  ),
				'default'     => 'section_no_height'
			),
			'sidebar_section_body_height' => array(
				'label'       => __( 'Height ( px )', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_body_height',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 350
			),
			'sidebar_section_body_padding_top' => array(
				'label'       => __( 'Top', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_body_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),
			'sidebar_section_body_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_body_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'sidebar_section_body_padding_left' => array(
				'label'       => __( 'Left', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_body_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'sidebar_section_body_padding_right' => array(
				'label'       => __( 'Right', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_body_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 5
			),
			'sidebar_article_underline' => array(
				'label'       => __( 'Article Underline Hover', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_underline',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'sidebar_article_active_bold' => array(
				'label'       => __( 'Article Active Bold', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_active_bold',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'sidebar_article_list_margin' => array(
				'label'       => __( 'Indentation', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_list_margin',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'sidebar_article_list_spacing' => array(
				'label'       => __( 'Between', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_list_spacing',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 8
			),

			/***  Article Sidebar -> Colors -> General  ***/

			'sidebar_background_color' => array(
				'label'       => __( 'Background', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fdfdfd'
			),


			/***  Article Sidebar -> Colors -> Articles Listed in Category Box ***/

			'sidebar_article_font_color' => array(
				'label'       => __( 'Article Color', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b3b3b3'
			),
			'sidebar_article_icon_color' => array(
				'label'       => __( 'Icon Color', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#525252'
			),
			'sidebar_article_active_font_color' => array(
				'label'       => __( 'Active Article Color', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_active_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'sidebar_article_active_background_color' => array(
				'label'       => __( 'Active Article Background', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_article_active_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#e8e8e8'
			),
			'sidebar_section_head_font_color' => array(
				'label'       => __( 'Category Text', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#525252'
			),
			'sidebar_section_head_background_color' => array(
				'label'       => __( 'Category Text Background', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f1f1f1'
			),
			'sidebar_section_head_description_font_color' => array(
				'label'       => __( 'Category Description', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_head_description_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b3b3b3'
			),
			'sidebar_section_border_color' => array(
				'label'       => __( 'Border', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F7F7F7'
			),
			'sidebar_section_divider_color' => array(
				'label'       => __( 'Color', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_divider_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CDCDCD'
			),
			'sidebar_section_category_font_color' => array(
				'label'       => __( 'Subcategory Text', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_category_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#868686'
			),
			'sidebar_section_subcategory_typography' => array(
				'label'       => __( 'Subcategory Typography', 'echo-knowledge-base' ),
				'name'        => 'sidebar_section_subcategory_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '16',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'sidebar_section_category_icon_color' => array(
				'label'       => __( 'Subcategory Expand Icon', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_section_category_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#868686'
			),

			 /*** Article Sidebar -> Front-End Text ***/

			'sidebar_category_empty_msg' => array(
				'label'       => __( 'Empty Category Notice', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_category_empty_msg',
				'size'        => '60',
				'max'         => '150',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Articles coming soon', 'echo-elegant-layouts' )
			),
			'sidebar_collapse_articles_msg' => array(
				'label'       => __( 'Collapse Articles Text', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_collapse_articles_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Collapse Articles', 'echo-elegant-layouts' )
			),
			'sidebar_show_all_articles_msg' => array(
				'label'       => __( 'Show All Articles Text', 'echo-elegant-layouts' ),
				'name'        => 'sidebar_show_all_articles_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Show all articles', 'echo-elegant-layouts' )
			),


			/******************************************************************************
			 *
			 *  CATEGORY ARCHIVE v2
			 *
			 ******************************************************************************/

			/* 'category-archive-structure-version' => array( // TODO NOT USED RIGHT NOW, not in UI, auto determined
					'label'       => __( 'Category Archive Structure', 'echo-knowledge-base' ),
					'name'        => 'category-archive-structure-version',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     =>
							array(
									'version-1' => 'Legacy Style',
									'version-2' => 'Modern Style (Recommended)'
							),
					'default'     => 'version-1',
			), */

			// Archive Version 2 - Left Sidebar
			/* 'archive-left-sidebar-on-v2' => array(
				'label'       => __( 'Turn on Left Sidebar', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-on-v2',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			), */
			'archive-left-sidebar-width-v2' => array(
				'label'       => __( 'Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-width-v2',
				'max'         => 80,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'archive-left-sidebar-padding-v2' => array(
				'label'       => __( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-left-sidebar-background-color-v2' => array(
				'label'       => __( 'Left Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'archive-left-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),

			// Archive Version 2 - Archive Content
			'archive-container-width-v2' => array(
				'label'       => __( 'Archive Container Width', 'echo-knowledge-base' ),
				'name'        => 'archive-container-width-v2',
				'max'         => 3000,
				'min'         => 10,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1080
			),
			'archive-container-width-units-v2' => array(
				'label'       => __( 'Archive Container Width Units', 'echo-knowledge-base' ),
				'name'        => 'archive-container-width-units-v2',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'px'         => _x( 'px', 'echo-knowledge-base' ),
					'%'          => _x( '%',  'echo-knowledge-base' ),

				),
				'default'     => 'px'
			),
			'archive-content-width-v2' => array(
				'label'       => __( 'Width (%)', 'echo-knowledge-base' ),
				'name'        => 'archive-content-width-v2',
				'max'         => 100,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 100
			),
			'archive-show-sub-categories' => array(
				'label'       => __( 'Show Articles from Sub-Categories', 'echo-knowledge-base' ),
				'name'        => 'archive-show-sub-categories',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'archive-content-padding-v2' => array(
				'label'       => __( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-content-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-content-background-color-v2' => array(
				'label'       => __( 'Content Background', 'echo-knowledge-base' ),
				'name'        => 'archive-content-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),

			// Archive Version 2 - Right Sidebar
			/* 'archive-right-sidebar-on-v2' => array(
				'label'       => __( 'Turn on Right Sidebar', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-on-v2',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'archive-right-sidebar-width-v2' => array(
				'label'       => __( 'Width ( % )', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-width-v2',
				'max'         => 80,
				'min'         => 5,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'archive-right-sidebar-padding-v2' => array(
				'label'       => __( 'Padding ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-padding-v2',
				'max'         => 200,
				'min'         => 0,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'archive-right-sidebar-background-color-v2' => array(
				'label'       => __( 'Right Sidebar Background', 'echo-knowledge-base' ),
				'name'        => 'archive-right-sidebar-background-color-v2',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7f7f7'
			),*/

			// Archive Version 2 - Advanced
			'archive-mobile-break-point-v2' => array(
				'label'       => __( 'Small Screen Break point ( px )', 'echo-knowledge-base' ),
				'name'        => 'archive-mobile-break-point-v2',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1000
			),


			/******************************************************************************
			 *
			 *  CATEGORIES BOX
			 *
			 ******************************************************************************/
			'categories_box_top_margin' => array(
                'label'       => __( 'Container Top Margin, (px)', 'echo-knowledge-base' ),
                'name'        => 'categories_box_top_margin',
                'max'         => '100',
                'min'         => '-100',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
			'categories_box_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'categories_box_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'category_box_title_text_color' => array(
				'label'       => __( 'Title Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_title_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#666666'
			),
			'category_box_container_background_color' => array(
				'label'       => __( 'Container Background Color', 'echo-knowledge-base' ),
				'name'      => 'category_box_container_background_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fcfcfc'
			),
			'category_box_category_text_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_category_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'category_box_count_background_color' => array(
				'label'       => __( 'Count Background', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_background_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'category_box_count_text_color' => array(
				'label'       => __( 'Count Text', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'category_box_count_border_color' => array(
				'label'       => __( 'Count Border', 'echo-knowledge-base' ),
				'name'      => 'category_box_count_border_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CCCCCC'
			),
			
			/******************************************************************************
			 *
			 *  OTHER
			 *
			 ******************************************************************************/
			'categories_in_url_enabled' => array(
					'label'       => __( 'Categories in URL', 'echo-knowledge-base' ),
					'name'        => 'categories_in_url_enabled',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     => array(
							'on'     => __( 'on', 'echo-knowledge-base' ),
							'off'    => __( 'off', 'echo-knowledge-base' )
					),
					'default'     => 'off'
			),
			'kb_main_page_category_link' => array(
					'label'       => __( 'Main Page Category Link', 'echo-knowledge-base' ),
					'name'        => 'kb_main_page_category_link',
					'type'        => EPKB_Input_Filter::SELECTION,
					'options'     =>
							array(
									'default'          => __( 'Article Page', 'echo-knowledge-base' ),
									'category_archive' => __( 'Category Archive Page', 'echo-knowledge-base' )
							),
					'default'     => 'default',
			),
			'categories_display_sequence' => array(
				'label'       => __( 'Categories Sequence', 'echo-knowledge-base' ),
				'name'        => 'categories_display_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_categories_display_order(),
				'default'     => 'alphabetical-title'
			),
			'articles_display_sequence' => array(
				'label'       => __( 'Articles Sequence', 'echo-knowledge-base' ),
				'name'        => 'articles_display_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_articles_display_order(),
				'default'     => 'alphabetical-title'
			),
			'templates_for_kb' => array(
				'label'       => __( 'Choose Template', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'current_theme_templates'    => __( 'Current Theme Template', 'echo-knowledge-base'  ),
					'kb_templates'       => __( 'Knowledge Base Template', 'echo-knowledge-base'  ),
				),
				'default'     => 'kb_templates'
			),
			'wpml_is_enabled' => array(
					'label'       => __( 'Enable WPML', 'echo-knowledge-base' ),
					'name'        => 'wpml_is_enabled',
					'type'        => EPKB_Input_Filter::CHECKBOX,
					//'internal'    => true,  // field update handled separately
					'default'     => 'off'
			),
			'articles_comments_global' => array(
				'label'       => __( 'Comments', 'echo-knowledge-base' ),
				'name'        => 'articles_comments_global',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'		=> __( "Enabled for all articles", 'echo-knowledge-base' ),
					'off'		=> __( "Disabled for all articles", 'echo-knowledge-base' ),
					'article'	=> __( "Determined by individual article's comments option", 'echo-knowledge-base' ),
				),
				'default'     => 'off'
			),
			'category_focused_menu_heading_text' => array(
				'label'       => __( 'Categories Heading', 'echo-knowledge-base' ),
				'name'        => 'category_focused_menu_heading_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Categories', 'echo-knowledge-base' )
			),
			'template_widget_sidebar_defaults'  => array(
				'label'       => __( 'Widget Sidebar Styling', 'echo-knowledge-base' ),
				'name'        => 'template_widget_sidebar_defaults',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),


			/******************************************************************************
			 *
			 *  KB TEMPLATE settings
			 *
			 ******************************************************************************/

			// TEMPLATES for Main Page
			'template_main_page_display_title' => array(
				'label'       => __( 'Display Page Title', 'echo-knowledge-base' ),
				'name'        => 'template_main_page_display_title',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
            'template_main_page_padding_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_padding_top',
                'max'         => '300',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'template_main_page_padding_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_padding_bottom',
                'max'         => '500',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '50'
            ),
            'template_main_page_padding_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_padding_left',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'template_main_page_padding_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_padding_right',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'template_main_page_margin_top' => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_margin_top',
                'max'         => '300',
                'min'         => '-300',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'template_main_page_margin_bottom' => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_margin_bottom',
                'max'         => '500',
                'min'         => '-500',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '50'
            ),
            'template_main_page_margin_left' => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_margin_left',
                'max'         => '50',
                'min'         => '-50',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'template_main_page_margin_right' => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'template_main_page_margin_right',
                'max'         => '50',
                'min'         => '-50',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),

			// TEMPLATES ofr Article Page
			'templates_for_kb_article_reset'            => array(
				'label'       => __( 'Article Content - Remove Theme Style', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_reset',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'templates_for_kb_article_defaults'         => array(
				'label'       => __( 'Article Content - Add KB Style', 'echo-knowledge-base' ),
				'name'        => 'templates_for_kb_article_defaults',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'template_article_padding_top'      => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_top',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_padding_bottom'   => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_bottom',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_padding_left'     => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_left',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_padding_right'    => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'template_article_padding_right',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_margin_top'       => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_top',
				'max'         => '300',
				'min'         => '-300',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_margin_bottom'    => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_bottom',
				'max'         => '500',
				'min'         => '-500',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '50'
			),
			'template_article_margin_left'      => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_left',
				'max'         => '300',
				'min'         => '-300',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'template_article_margin_right'     => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'template_article_margin_right',
				'max'         => '300',
				'min'         => '-300',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),

			// Category Archive Page
			'template_category_archive_page_style' => array(
				'label'       => __( 'Pre-made Designs', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_page_style',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'eckb-category-archive-style-1' => __( 'Style 1 ( Basic List )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-2' => __( 'Style 2 ( Standard )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-3' => __( 'Style 3 ( Standard 2 )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-4' => __( 'Style 4 ( Box )', 'echo-knowledge-base' ),
					'eckb-category-archive-style-5' => __( 'Style 5 ( Grid )', 'echo-knowledge-base' ),
				),
				'default'     => 'eckb-category-archive-style-2'
			),
			'template_category_archive_page_heading_description' => array(
				'label'       => __( 'Heading Description', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_page_heading_description',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Category - ', 'echo-knowledge-base' )
			),
			'template_category_archive_read_more' => array(
				'label'       => __( 'Read More', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_read_more',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Read More', 'echo-knowledge-base' )
			),
			'template_category_archive_date' => array(
				'label'       => __( 'Date Text', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_date',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Date:', 'echo-knowledge-base' )
			),
			'template_category_archive_author' => array(
				'label'       => __( 'Author Text', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_author',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'By:', 'echo-knowledge-base' )
			),
			'template_category_archive_categories' => array(
				'label'       => __( 'Categories Text', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_categories',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Categories:', 'echo-knowledge-base' )
			),

			'template_category_archive_date_on'         => array(
				'label'       => __( 'Show date', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_date_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'template_category_archive_author_on'         => array(
				'label'       => __( 'Show author', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_author_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'template_category_archive_categories_on'         => array(
				'label'       => __( 'Show categories', 'echo-knowledge-base' ),
				'name'        => 'template_category_archive_categories_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  TOC
			 *
			 ******************************************************************************/

			'article_toc_enable' => array(
				'label'       => __( 'Show Table of Contents', 'echo-knowledge-base' ),
				'name'        => 'article_toc_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_toc_hx_level' => array(
				'label'       => __( 'From Hx', 'echo-knowledge-base' ),
				'name'        => 'article_toc_hx_level',
				'max'         => '6',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => '2'
			),
			'article_toc_hy_level' => array(
				'label'       => __( 'To Hy', 'echo-knowledge-base' ),
				'name'        => 'article_toc_hy_level',
				'max'         => '6',
				'min'         => '2',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => '6'
			),
			'article_toc_exclude_class' => array(
                'label'       => __( 'CSS Class to exclude headers from the TOC', 'echo-knowledge-base' ),
                'name'        => 'article_toc_exclude_class',
                'size'        => '200',
				'max'         => '200',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => ''
            ),
			'article_toc_active_bg_color' => array(
				'label'       => __( 'Active Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_active_bg_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#1e73be'
			),
			'article_toc_title_color' => array(
				'label'       => __( 'Title', 'echo-knowledge-base' ),
				'name'      => 'article_toc_title_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_text_color' => array(
				'label'       => __( 'Headings', 'echo-knowledge-base' ),
				'name'      => 'article_toc_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_active_text_color' => array(
				'label'       => __( 'Active Heading', 'echo-knowledge-base' ),
				'name'      => 'article_toc_active_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_toc_cursor_hover_bg_color' => array(
				'label'       => __( 'Hover: Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_cursor_hover_bg_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#e1ecf7'
			),
			'article_toc_cursor_hover_text_color' => array(
				'label'       => __( 'Hover: Text', 'echo-knowledge-base' ),
				'name'      => 'article_toc_cursor_hover_text_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_toc_scroll_offset' => array(
                'label'       => __( 'Heading position is relative to the screen after scroll (px)', 'echo-knowledge-base' ),
                'name'        => 'article_toc_scroll_offset',
                'max'         => '200',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
                'default'     => '130'
            ),
			// article_toc_position - V1
			'article_toc_position' => array(
                'label'       => __( 'Location', 'echo-knowledge-base' ),
                'name'        => 'article_toc_position',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'left'   => __( 'Left',   'echo-knowledge-base' ),
                    'right'   => __( 'Right', 'echo-knowledge-base' ),
					'middle'   => __( 'Middle', 'echo-knowledge-base' ),
                ),
                'default'     => 'right'
            ),
			'article_toc_border_mode' => array(
                'label'       => __( 'Border Style', 'echo-knowledge-base' ),
                'name'        => 'article_toc_border_mode',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'none'   => __( 'None',   'echo-knowledge-base' ),
                    'between'   => __( 'Between Article and TOC', 'echo-knowledge-base' ),
					'around'   => __( 'Around TOC', 'echo-knowledge-base' ),
                ),
                'default'     => 'between'
            ),
			'article_toc_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'      => 'article_toc_border_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#2b98e5'
			),
			'article_toc_header_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_toc_header_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '15',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article_toc_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_toc_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'article_toc_position_from_top' => array(
                'label'       => __( 'Starting Position (px)', 'echo-knowledge-base' ),
                'name'        => 'article_toc_position_from_top',
                'max'         => '1000',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '300'
            ),

			'article_toc_background_color' => array(
				'label'       => __( 'Container Background', 'echo-knowledge-base' ),
				'name'      => 'article_toc_background_color',
				'size'        => '10',
				'max'        => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#fcfcfc'
			),
			'article_toc_title' => array(
				'label'       => __( 'Title (optional)', 'echo-knowledge-base' ),
				'name'        => 'article_toc_title',
				'size'        => '200',
				'max'         => '200',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Table of Contents', 'echo-knowledge-base' )
			),
			'article_toc_scroll_speed' => array(
				'label'       => __( 'Scroll Time', 'echo-knowledge-base' ),
				'name'        => 'article_toc_scroll_speed',
				'max'         => '5000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '300',
			),

			/******************************************************************************
			 *
			 *  ARTICLE CONTENT - zone - header rows
			 *
			 ******************************************************************************/
			'article_content_enable_rows'               => array(
				'label'       => __( 'Enable Article Header Rows', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_content_enable_rows_1_gap'         => array(
				'label'       => __( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_1_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '8'
			),
			'article_content_enable_rows_1_alignment'   => array(
				'label'       => __( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_1_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'center'
			),
			'article_content_enable_rows_2_gap'         => array(
				'label'       => __( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_2_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_enable_rows_2_alignment'   => array(
				'label'       => __( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_2_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'flex-end'
			),
			'article_content_enable_rows_3_gap'         => array(
				'label'       => __( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_3_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '32'
			),
			'article_content_enable_rows_3_alignment'   => array(
				'label'       => __( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_4_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'flex-end'
			),
			'article_content_enable_rows_4_gap'         => array(
				'label'       => __( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_4_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_enable_rows_4_alignment'   => array(
				'label'       => __( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_4_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'flex-end'
			),
			'article_content_enable_rows_5_gap'         => array(
				'label'       => __( 'Bottom Gap', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_5_gap',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '100'
			),
			'article_content_enable_rows_5_alignment'   => array(
				'label'       => __( 'Features Vertical Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_rows_5_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'flex-start'    => _x( 'Row Top', 'echo-knowledge-base' ),
					'center'        => _x( 'Row Center', 'echo-knowledge-base' ),
					'flex-end'      => _x( 'Row Bottom', 'echo-knowledge-base' ) ),
				'default'     => 'center'
			),


			/******************************************************************************
			 *
			 *  Article Title
			 *
			 ******************************************************************************/
			'article_content_enable_article_title'      => array(
				'label'       => __( 'Article Title', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_article_title',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_title_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_title_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => EPKB_Typography::$typography_defaults
			),
			'article_title_row'                         => array(
				'label'       => __( 'Row', 'echo-knowledge-base' ),
				'name'        => 'article_title_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					// '0'  => _x( 'Off', 'echo-knowledge-base' ),
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '2'
			),
			'article_title_alignment'                   => array(
				'label'       => __( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_title_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'article_title_sequence'                    => array(
				'label'       => __( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_title_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),

			/******************************************************************************
			 *
			 *  BACK NAVIGATION
			 *
			 ******************************************************************************/
			'article_content_enable_back_navigation'    => array(
				'label'       => __( 'Back Navigation', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_back_navigation',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'back_navigation_row'           => array(
				'label'       => __( 'Row', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					// '0'  => _x( 'Off', 'echo-knowledge-base' ),
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'back_navigation_alignment'     => array(
				'label'       => __( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'back_navigation_sequence'      => array(
				'label'       => __( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'back_navigation_mode'          => array(
				'label'       => __( 'Navigation Mode', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'navigate_browser_back'   => __( 'Browser Go Back Action',   'echo-knowledge-base' ),
					'navigate_kb_main_page'   => __( 'Redirect to KB Main Page', 'echo-knowledge-base' ),
				),
				'default'     => 'navigate_browser_back'
			),
			'back_navigation_text'          => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => '< ' . __( 'All Topics', 'echo-knowledge-base' )
			),
			'back_navigation_text_color'    => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'back_navigation_bg_color'      => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_bg_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'back_navigation_border_color'  => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b5b5b5'
			),
			'back_navigation_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'back_navigation_border'        => array(
				'label'       => __( 'Button Border', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'none'    => __( '-- No Border --', 'echo-knowledge-base' ),
					'solid'   => __( 'Solid', 'echo-knowledge-base' ),
				),
				'default'     => 'solid'
			),
			'back_navigation_border_radius' => array(
				'label'       => __( 'Border Radius', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_radius',
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => '3'
			),
			'back_navigation_border_width'  => array(
				'label'       => __( 'Border Width', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_border_width',
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => '1'
			),
			'back_navigation_margin_top'    => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_top',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_bottom',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_left'   => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_left',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '4'
			),
			'back_navigation_margin_right'  => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_margin_right',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '15'
			),
			'back_navigation_padding_top'   => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '5'
			),
			'back_navigation_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '5'
			),
			'back_navigation_padding_left'  => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'back_navigation_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'back_navigation_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),

			'meta-data-header-toggle' => array(  // old article content header
				'label'       => __( 'Enable Header Meta Data', 'echo-knowledge-base' ),
				'name'        => 'meta-data-header-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'meta-data-footer-toggle' => array(
				'label'       => __( 'Enable Meta Data at the Bottom', 'echo-knowledge-base' ),
				'name'        => 'meta-data-footer-toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			/******************************************************************************
			 *
			 *  Author
			 *
			 ******************************************************************************/
			'article_content_enable_author' => array(
				'label'       => __( 'Author', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_author',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'author_footer_toggle'          => array(
				'label'       => __( 'Show Author', 'echo-knowledge-base' ),
				'name'        => 'author_footer_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'author_text'                   => array(
				'label'       => __( 'Author Text', 'echo-knowledge-base' ),
				'name'        => 'author_text',
				'size'        => '30',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'By', 'echo-knowledge-base' )
			),
			'author_row'                    => array(
				'label'       => __( 'Row', 'echo-knowledge-base' ),
				'name'        => 'author_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					// '0'  => _x( 'Off', 'echo-knowledge-base' ),
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'author_alignment'              => array(
				'label'       => __( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'author_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'author_sequence'               => array(
				'label'       => __( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'author_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'author_icon_on'                => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'author_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => __( 'Show icon', 'echo-knowledge-base' ),
					'off'    => __( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  Created Date
			 *
			 ******************************************************************************/
			'article_content_enable_created_date' => array(
				'label'       => __( 'Created Date', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_created_date',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'created_on_footer_toggle'      => array(
				'label'       => __( 'Show Created On', 'echo-knowledge-base' ),
				'name'        => 'created_on_footer_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'created_on_text'               => array(
				'label'       => __( 'Created Date Prefix', 'echo-knowledge-base' ),
				'name'        => 'created_on_text',
				'size'        => '30',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Posted', 'echo-knowledge-base' )
			),
			'created_date_row'              => array(
				'label'       => __( 'Row', 'echo-knowledge-base' ),
				'name'        => 'created_date_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					// '0'  => _x( 'Off', 'echo-knowledge-base' ),
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'created_date_alignment'        => array(
				'label'       => __( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'created_date_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'created_date_sequence'         => array(
				'label'       => __( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'created_date_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'created_date_icon_on'          => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'created_date_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => __( 'Show icon', 'echo-knowledge-base' ),
					'off'    => __( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  Last Updated Date
			 *
			 ******************************************************************************/
			'article_content_enable_last_updated_date'  => array(
				'label'       => __( 'Last Updated Date', 'echo-knowledge-base' ),
				'name'        => 'article_content_enable_last_updated_date',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'last_updated_on_footer_toggle' => array(
				'label'       => __( 'Show Last Updated On', 'echo-knowledge-base' ),
				'name'        => 'last_updated_on_footer_toggle',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'last_updated_on_text'          => array(
				'label'       => __( 'Updated Date Prefix', 'echo-knowledge-base' ),
				'name'        => 'last_updated_on_text',
				'size'        => '30',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Updated', 'echo-knowledge-base' )
			),
			'last_updated_date_row'         => array(
				'label'       => __( 'Row', 'echo-knowledge-base' ),
				'name'        => 'last_updated_date_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					// '0'  => _x( 'Off', 'echo-knowledge-base' ),
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '3'
			),
			'last_updated_date_alignment'   => array(
				'label'       => __( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'created_date_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'last_updated_date_sequence'    => array(
				'label'       => __( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'last_updated_date_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '2'
			),
			'last_updated_date_icon_on'     => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'last_updated_date_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => __( 'Show icon', 'echo-knowledge-base' ),
					'off'    => __( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),

			/******************************************************************************
			 *
			 *  Breadcrumb
			 *
			 ******************************************************************************/
			'breadcrumb_enable'  => array(
				'label'       => __( 'Breadcrumb', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'breadcrumb_row'                => array(
				'label'       => __( 'Row', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					// '0'  => _x( 'Off', 'echo-knowledge-base' ),
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'breadcrumb_alignment'          => array(
				'label'       => __( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'left'
			),
			'breadcrumb_sequence'           => array(
				'label'       => __( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '2'
			),
			'breadcrumb_icon_separator'     => array(
				'label'       => __( 'Breadcrumb Separator', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_icon_separator',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'ep_font_icon_none'    => __( '-- No Icon --',   'echo-knowledge-base' ),
					'ep_font_icon_right_arrow'   => __( 'Right Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_left_arrow'    => __( 'Left Arrow', 'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right_circle'    => __( 'Arrow Right Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left_circle'    => __( 'Arrow Left Circle',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_left'    => __( 'Arrow Caret Left',   'echo-knowledge-base' ),
					'ep_font_icon_arrow_carrot_right'    => __( 'Arrow Caret Right',   'echo-knowledge-base' ),
				),
				'default'     => 'ep_font_icon_arrow_carrot_right'
			),
            'breadcrumb_padding_top'        => array(
                'label'       => __( 'Top', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_top',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'breadcrumb_padding_bottom'     => array(
                'label'       => __( 'Bottom', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_bottom',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '0'
            ),
            'breadcrumb_padding_left'       => array(
                'label'       => __( 'Left', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_left',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
            'breadcrumb_padding_right'      => array(
                'label'       => __( 'Right', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_padding_right',
                'max'         => '50',
                'min'         => '0',
                'type'        => EPKB_Input_Filter::NUMBER,
                'default'     => '4'
            ),
			'breadcrumb_margin_top'         => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_top',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_bottom'      => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_bottom',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_left'        => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_left',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'breadcrumb_margin_right'       => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_right',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
            'breadcrumb_text_color'         => array(
                'label'       => __( 'Breadcrumb Text', 'echo-knowledge-base' ),
                'name'        => 'breadcrumb_text_color',
                'size'        => '10',
                'max'         => '7',
                'min'         => '7',
                'type'        => EPKB_Input_Filter::COLOR_HEX,
                'default'     => '#000000'
            ),
			'breadcrumb_description_text'   => array(
				'label'       => __( 'Breadcrumb Description', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_description_text',
				'size'        => '50',
				'max'         => '70',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => ''
			),
			'breadcrumb_home_text'          => array(
				'label'       => __( 'Breadcrumb Home Text', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_home_text',
				'size'        => '50',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Main', 'echo-knowledge-base' )
			),
			'breadcrumb_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),


			/******************************************************************************
			 *
			 *  ARTICLE CONTENT TOOLBAR - zone
			 *
			 ******************************************************************************/
			'article_content_toolbar_enable'                => array(
				'label'       => __( 'Enable Content Toolbar', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_content_toolbar_row'                   => array(
				'label'       => __( 'Row', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_row',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					// '0'  => _x( 'Off', 'echo-knowledge-base' ),
					'1'  => _x( 'Row 1', 'echo-knowledge-base' ),
					'2'  => _x( 'Row 2', 'echo-knowledge-base' ),
					'3'  => _x( 'Row 3', 'echo-knowledge-base' ),
					'4'  => _x( 'Row 4', 'echo-knowledge-base' ),
					'5'  => _x( 'Row 5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'article_content_toolbar_alignment'             => array(
				'label'       => __( 'Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'left'  	        => is_rtl() ? _x( 'Right', 'echo-knowledge-base' ) : _x( 'Left', 'echo-knowledge-base' ),
					'right'  	        => is_rtl() ? _x( 'Left', 'echo-knowledge-base' ) : _x( 'Right', 'echo-knowledge-base' ) ),
				'default'     => 'right'
			),
			'article_content_toolbar_sequence'              => array(
				'label'       => __( 'Sequence in the Alignment', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_sequence',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'1'  => _x( '1', 'echo-knowledge-base' ),
					'2'  => _x( '2', 'echo-knowledge-base' ),
					'3'  => _x( '3', 'echo-knowledge-base' ),
					'4'  => _x( '4', 'echo-knowledge-base' ),
					'5'  => _x( '5', 'echo-knowledge-base' ) ),
				'default'     => '1'
			),
			'article_content_toolbar_button_background'     => array(
				'label'       => __( 'Button Background Color', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_background',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_content_toolbar_button_background_hover' => array(
				'label'       => __( 'Button Background Hover Color', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_background_hover',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_content_toolbar_button_format'         => array(
				'label'       => __( 'Button Format', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_format',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'icon'      => _x( 'Icon', 'echo-knowledge-base' ),
					'text'      => _x( 'Text', 'echo-knowledge-base' ),
					'icon_text' => _x( 'Icon and Text', 'echo-knowledge-base' ),
					'text_icon' => _x( 'Text and Icon', 'echo-knowledge-base' ) ),
				'default'     => 'text_icon'
			),
			'article_content_toolbar_icon_size'             => array(
				'label'       => __( 'Icon Size (px)', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_icon_size',
				'max'         => '50',
				'min'         => '12',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '20'
			),
			'article_content_toolbar_icon_color'            => array(
				'label'       => __( 'Icon Color', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_icon_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_icon_hover_color'      => array(
				'label'       => __( 'Icon Hover Color', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_icon_hover_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_border_color'          => array(
				'label'       => __( 'Border Color', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_border_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#ffffff'
			),
			'article_content_toolbar_border_radius'         => array(
				'label'       => __( 'Border Radius', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_border_radius',
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 0
			),
			'article_content_toolbar_border_width'          => array(
				'label'       => __( 'Border Width', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 0
			),
			'article_content_toolbar_text_size'             => array(
				'label'       => __( 'Text Size (px)', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_text_size',
				'max'         => '30',
				'min'         => '12',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '15'
			),
			'article_content_toolbar_text_color'            => array(
				'label'       => __( 'Text Color', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_text_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_text_hover_color'      => array(
				'label'       => __( 'Text Hover Color', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_hover_color',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'article_content_toolbar_button_padding_top'    => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_top',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_bottom',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_padding_left'   => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_left',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_padding_right'  => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_padding_right',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'article_content_toolbar_button_margin_top'     => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_top',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_toolbar_button_margin_bottom'  => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_bottom',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_toolbar_button_margin_left'    => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_left',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'article_content_toolbar_button_margin_right'   => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article_content_toolbar_button_margin_right',
				'max'         => '100',
				'min'         => '-100',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),


			/******************************************************************************
			 *
			 *  Print Button
			 *
			 ******************************************************************************/
			'print_button_enable'                           => array(
				'label'       => __( 'Print Button', 'echo-knowledge-base' ),
				'name'        => 'print_button_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'print_button_text'                             => array(
				'label'       => __( 'Print Text', 'echo-knowledge-base' ),
				'name'        => 'print_button_text',
				'size'        => '30',
				'max'         => '60',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Print', 'echo-knowledge-base' )
			),
			'print_button_doc_padding_top'                  => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_top',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'print_button_doc_padding_bottom'               => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_bottom',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'print_button_doc_padding_left'                 => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_left',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),
			'print_button_doc_padding_right'                => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'print_button_doc_padding_right',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '10'
			),


			/******  PREV/NEXT NAVIGATION  ******/
			'prev_next_navigation_enable' => array(
				'label'       => __( 'Show Prev/Next Navigation', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'next_navigation_text' => array(
				'label'       => __( 'Next Text', 'echo-knowledge-base' ),
				'name'        => 'next_navigation_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     =>  __( 'Next', 'echo-knowledge-base' )
			),
			'prev_navigation_text' => array(
				'label'       => __( 'Previous Text', 'echo-knowledge-base' ),
				'name'        => 'prev_navigation_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     =>  __( 'Previous', 'echo-knowledge-base' )
			),
			'prev_next_navigation_text_color' => array(
				'label'       => __( 'Text Color', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#1e73be'
			),
			'prev_next_navigation_bg_color' => array(
				'label'       => __( 'Background Color', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_bg_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7f7f7'
			),
			'prev_next_navigation_hover_text_color' => array(
				'label'       => __( 'Hover: Text Color', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_hover_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#6d6d6d'
			),
			'prev_next_navigation_hover_bg_color' => array(
				'label'       => __( 'Hover: Background Color', 'echo-knowledge-base' ),
				'name'        => 'prev_next_navigation_hover_bg_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#dee3e5'
			),

			// old Article Content Header
			'article_meta_icon_on' => array(
				'label'       => __( 'Article Meta Icon', 'echo-knowledge-base' ),
				'name'        => 'article_meta_icon_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on'    => __( 'Show icon', 'echo-knowledge-base' ),
					'off'    => __( 'Hide icon', 'echo-knowledge-base' )
				),
				'default'     => 'on'
			),
			'breadcrumb_margin_bottom_old'      => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'breadcrumb_margin_bottom_old',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),/* option postponed
            'date_format' => array(
                'label'       => __( 'Date Format', 'echo-knowledge-base' ),
                'name'        => 'date_format',
                'type'        => EPKB_Input_Filter::SELECTION,
                'options'     => array(
                    'F j, Y'    => __( 'January 1, 2020', 'echo-knowledge-base' ),
                    'M j, Y'    => __( 'Jan 1, 2020', 'echo-knowledge-base' ),
                    'j F Y'    => __( '1 January 2020', 'echo-knowledge-base' ),
                    'j M Y'    => __( '1 Jan 2020', 'echo-knowledge-base' ),
                    'm/d/Y'    => __( '01/30/2020', 'echo-knowledge-base' ),
                    'Y/m/d'    => __( '2020/01/30', 'echo-knowledge-base' ),
                ),
                'default'     => 'M j, Y'
            ), */


			/******************************************************************************
			 *
			 *  Admin UI Access - CONTEXTs
			 *
			 ******************************************************************************/

			// Access to visual Editor (write)
			'admin_eckb_access_frontend_editor_write' => array(
				'label'       => __( 'Edit KB colors, fonts, labels and features', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_frontend_editor_write',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY
			),

			// Access to Order Articles (write)
			'admin_eckb_access_order_articles_write' => array(
				'label'       => __( 'Order Articles and Categories', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_order_articles_write',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY
			),

			// Access to Search Analytics (read)
			'admin_eckb_access_search_analytics_read' => array(
				'label'       => __( 'Search Analytics', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_search_analytics_read',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY, EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY
			),

			// Access to Need Help? (read)
			'admin_eckb_access_need_help_read' => array(
				'label'       => __( 'Need Help?', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_need_help_read',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY, EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY
			),

			// Access to Add-ons / News (read)
			'admin_eckb_access_addons_news_read' => array(
				'label'       => __( 'Add-ons / News', 'echo-knowledge-base' ),
				'name'        => 'admin_eckb_access_addons_news_read',
				'type'        => EPKB_Input_Filter::TEXT,
				'max'         => '60',
				'min'         => '3',
				'allowed_access'  => array( EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY, EPKB_Admin_UI_Access::EPKB_WP_EDITOR_CAPABILITY ),
				'default'     => EPKB_Utilities::is_amag_on() ? EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY : EPKB_Admin_UI_Access::EPKB_WP_AUTHOR_CAPABILITY
			)
		);

		// add CORE LAYOUTS SHARED configuration
		$config_specification = array_merge( $config_specification, self::shared_configuration() );

		// add CORE LAYOUTS non-shared configuration
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Basic::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Tabs::get_fields_specification() );
		$config_specification = array_merge( $config_specification, EPKB_KB_Config_Layout_Categories::get_fields_specification() );

		self::$cached_specs[$kb_id] = empty($config_specification_temp) || count($config_specification) > count($config_specification_temp)
										? $config_specification : $config_specification_temp;

		return self::$cached_specs[$kb_id];
	}

	/**
	 * Shared STYLE, COLOR and TEXT configuration between CORE LAYOUTS
	 *
	 * @return array
	 */
	public static function shared_configuration() {
		
		/**
		 * Layout/color settings shared among layouts and color sets are listed here.
		 * If a setting becomes unique to color/layout, move it to its file.
		 * If a setting becomes common, move it from its file to this file.
		 */
		$shared_specification = array(

			/******************************************************************************
			 *
			 *  KB Main Layout - Layout and Style
			 *
			 ******************************************************************************/

			/***  KB Main Page -> General ***/

			'width' => array(
				'label'       => __( 'Search Box Width', 'echo-knowledge-base' ),
				'name'        => 'width',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-boxed' => __( 'Boxed Width', 'echo-knowledge-base' ),
					'epkb-full' => __( 'Full Width', 'echo-knowledge-base' ) ),
				'default'     => 'epkb-full'
			),
			'section_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'section_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'show_articles_before_categories' => array(
				'label'       => __( 'Show Articles', 'echo-knowledge-base' ),
				'name'        => 'show_articles_before_categories',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => __( 'Before Categories', 'echo-knowledge-base' ),
					'off' => __( 'After Categories', 'echo-knowledge-base' ),
					),
				'default'     => 'on'
			),
			'categories_layout_list_mode' => array(
				'label'       => __( 'Categories to Display', 'echo-knowledge-base' ),
				'name'        => 'categories_layout_list_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'list_top_categories' => __( 'Top Categories', 'echo-knowledge-base' ),
					'list_sibling_categories' => __( 'Sibling Categories', 'echo-knowledge-base' ),
					),
				'default'     => 'list_top_categories'
			),
			'nof_columns' => array(
				'label'       => __( 'Number of Columns', 'echo-knowledge-base' ),
				'name'        => 'nof_columns',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array( 'one-col' => '1', 'two-col' => '2', 'three-col' => '3', 'four-col' => '4' ),
				'default'     => 'three-col'
			),
			'nof_articles_displayed' => array(
				'label'       => __( 'Number of Articles Listed', 'echo-knowledge-base' ),
				'name'        => 'nof_articles_displayed',
				'max'         => '2000',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 8
			),
			'expand_articles_icon' => array(
				'label'       => __( 'Icon Type', 'echo-knowledge-base' ),
				'name'        => 'expand_articles_icon',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array( 'ep_font_icon_plus_box' => _x( 'Plus Box', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_plus' => _x( 'Plus Sign', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_right_arrow' => _x( 'Arrow Triangle', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right' => _x( 'Arrow Caret', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_arrow_carrot_right_circle' => _x( 'Arrow Caret 2', 'icon type', 'echo-knowledge-base' ),
				                        'ep_font_icon_folder_add' => _x( 'Folder', 'icon type', 'echo-knowledge-base' ) ),
				'default'     => 'ep_font_icon_arrow_carrot_right'
			),


			/***  KB Main Page -> Search Box ***/

			'search_layout' => array(
				'label'       => __( 'Layout', 'echo-knowledge-base' ),
				'name'        => 'search_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-search-form-1' => __( 'Rounded search button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-4' => __( 'Squared search Button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-2' => __( 'Search button is below', 'echo-knowledge-base' ),
					'epkb-search-form-3' => __( 'No search button', 'echo-knowledge-base' ),
					'epkb-search-form-0' => __( 'No search box', 'echo-knowledge-base' )
				),
				'default'     => 'epkb-search-form-1'
			),
			'search_input_border_width' => array(
				'label'       => __( 'Border (px)', 'echo-knowledge-base' ),
				'name'        => 'search_input_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 1
			),
			'search_box_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_top',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'search_box_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_bottom',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'search_box_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_left',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'search_box_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'search_box_padding_right',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'search_box_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_top',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'search_box_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'search_box_margin_bottom',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 40
			),
			'search_box_input_width' => array(
				'label'       => __( 'Width (%)', 'echo-knowledge-base' ),
				'name'        => 'search_box_input_width',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 40
			),
			'search_box_results_style' => array(
				'label'       => __( 'Search Results: Match Article Colors', 'echo-knowledge-base' ),
				'name'        => 'search_box_results_style',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'search_title_html_tag' => array(
				'label'       => __( 'Search Title Html Tag', 'echo-knowledge-base' ),
				'name'        => 'search_title_html_tag',
				'size'        => '10',
				'max'         => '10',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => 'div'
			),
			'search_title_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'search_title_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '36',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),

			/***  KB Article Page -> Search Box ***/

			'article_search_layout' => array(
				'label'       => __( 'Layout', 'echo-knowledge-base' ),
				'name'        => 'article_search_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'epkb-search-form-1' => __( 'Rounded search button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-4' => __( 'Squared search Button is on the right', 'echo-knowledge-base' ),
					'epkb-search-form-2' => __( 'Search button is below', 'echo-knowledge-base' ),
					'epkb-search-form-3' => __( 'No search button', 'echo-knowledge-base' ),
					'epkb-search-form-0' => __( 'No search box', 'echo-knowledge-base' )
				),
				'default'     => 'epkb-search-form-1'
			),
			'article_search_input_border_width' => array(
				'label'       => __( 'Border (px)', 'echo-knowledge-base' ),
				'name'        => 'article_search_input_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 1
			),
			'article_search_box_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_top',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article_search_box_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_bottom',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'article_search_box_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_left',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_search_box_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_padding_right',
				'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_search_box_margin_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_margin_top',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 0
			),
			'article_search_box_margin_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_margin_bottom',
				'max'         => '200',
				'min'         => '-200',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 40
			),
			'article_search_box_input_width' => array(
				'label'       => __( 'Width (%)', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_input_width',
				'max'         => '100',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 40
			),
			'article_search_box_results_style' => array(
				'label'       => __( 'Search Results: Match Article Colors', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_results_style',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'article_search_title_html_tag' => array(
				'label'       => __( 'Search Title Html Tag', 'echo-knowledge-base' ),
				'name'        => 'article_search_title_html_tag',
				'size'        => '10',
				'max'         => '10',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => 'div'
			),
			'article_search_title_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'article_search_title_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '36',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),


			/***   KB Main Page -> Tuning -> Categories ***/

			// Style
			'section_head_alignment' => array(
				'label'       => __( 'Text Alignment', 'echo-knowledge-base' ),
				'name'        => 'section_head_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'left' => is_rtl() ? __( 'Right', 'echo-knowledge-base' ) : __( 'Left', 'echo-knowledge-base' ),
					'center' => __( 'Centered', 'echo-knowledge-base' ),
					'right' => is_rtl() ? __( 'Left', 'echo-knowledge-base' ) : __( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),

			// Style - Icons
			'section_head_category_icon_location' => array(
				'label'       => __( 'Icons Location / Turn Off', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_location',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'no_icons' => __( 'No Icons', 'echo-knowledge-base' ),
					'top'   => __( 'Top',   'echo-knowledge-base' ),
					'left' => is_rtl() ? __( 'Start', 'echo-knowledge-base' ) : __( 'Left', 'echo-knowledge-base' ),
					'right' => is_rtl() ? __( 'End', 'echo-knowledge-base' ) : __( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),
			'section_head_category_icon_size' => array(
				'label'       => __( 'Icon Size ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_head_category_icon_size',
				'max'         => '300',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => '21'
			),

			'section_divider' => array(
				'label'       => __( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'section_divider_thickness' => array(
				'label'       => __( 'Divider Thickness ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_divider_thickness',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 5
			),
			'section_desc_text_on' => array(
				'label'       => __( 'Category Description', 'echo-knowledge-base' ),
				'name'        => 'section_desc_text_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'section_hyperlink_text_on' => array(   // Grid Layout only
				'label'       => __( 'Click on Category', 'echo-knowledge-base' ),
				'name'        => 'section_hyperlink_text_on',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'on' => __( 'Go to Category Archive Page', 'echo-knowledge-base' ),
					'off' => __( 'Go to the first Article', 'echo-knowledge-base' ),
				),
				'default'     => 'off'
			),
			'section_hyperlink_on' => array(   // Basic and Tabs Layouts
				'label'       => __( 'Category link to Archive page', 'echo-knowledge-base' ),
				'name'        => 'section_hyperlink_on',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),

			// Advanced
			'section_box_shadow' => array(
				'label'       => __( 'Article List Shadow', 'echo-knowledge-base' ),
				'name'        => 'section_box_shadow',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'no_shadow' => __( 'No Shadow', 'echo-knowledge-base' ),
					'section_light_shadow' => __( 'Light Shadow', 'echo-knowledge-base' ),
					'section_medium_shadow' => __( 'Medium Shadow', 'echo-knowledge-base' ),
					'section_bottom_shadow' => __( 'Bottom Shadow', 'echo-knowledge-base' )
				),
				'default'     => 'no_shadow'
			),
			'section_head_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_top',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'section_head_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_bottom',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 20
			),
			'section_head_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_left',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_head_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_head_padding_right',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
            'section_border_radius' => array(
				'label'       => __( 'Border Radius', 'echo-knowledge-base' ),
				'name'        => 'section_border_radius',
				'max'         => '30',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
	            'style'       => 'small',
				'default'     => 4
			),
			'section_border_width' => array(
				'label'       => __( 'Border Width', 'echo-knowledge-base' ),
				'name'        => 'section_border_width',
				'max'         => '10',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 0
			),

			/***   KB Main Page -> Articles Listed in Sub-Category ***/
			'section_box_height_mode' => array(
				'label'       => __( 'Height Mode', 'echo-knowledge-base' ),
				'name'        => 'section_box_height_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'section_no_height' => __( 'Variable', 'echo-knowledge-base' ),
					'section_min_height' => __( 'Minimum', 'echo-knowledge-base' ),
					'section_fixed_height' => __( 'Maximum', 'echo-knowledge-base' )  ),
				'default'     => 'section_min_height'
			),
			'section_body_height' => array(
				'label'       => __( 'Height ( px )', 'echo-knowledge-base' ),
				'name'        => 'section_body_height',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 200
			),
			'section_body_padding_top' => array(
				'label'       => __( 'Top', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_top',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_body_padding_bottom' => array(
				'label'       => __( 'Bottom', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_bottom',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 4
			),
			'section_body_padding_left' => array(
				'label'       => __( 'Left', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_left',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'section_body_padding_right' => array(
				'label'       => __( 'Right', 'echo-knowledge-base' ),
				'name'        => 'section_body_padding_right',
                'max'         => '200',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => 10
			),
			'section_article_underline' => array(
				'label'       => __( 'Article Underline Hover', 'echo-knowledge-base' ),
				'name'        => 'section_article_underline',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'article_list_margin' => array(
				'label'       => __( 'Left offset for Articles List', 'echo-knowledge-base' ),
				'name'        => 'article_list_margin',
				'max'         => '50',
				'min'         => '-50',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 10
			),
			'article_list_spacing' => array(
				'label'       => __( 'Space Between Items', 'echo-knowledge-base' ),
				'name'        => 'article_list_spacing',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 8
			),


			/******************************************************************************
			 *
			 *  KB Main Colors - All Colors Settings
			 *
			 ******************************************************************************/

			/***  Main Page Search Box COLORS ***/
			'search_title_font_color' => array(
				'label'       => __( 'Title', 'echo-knowledge-base' ),
				'name'        => 'search_title_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'search_background_color' => array(
				'label'       => __( 'Search Background', 'echo-knowledge-base' ),
				'name'        => 'search_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'search_text_input_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'search_text_input_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_text_input_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CCCCCC'
			),
			'search_btn_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'search_btn_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'search_btn_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'search_btn_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F1F1F1'
			),

			/***  Article Page Search Box COLORS ***/
			'article_search_title_font_color' => array(
				'label'       => __( 'Title', 'echo-knowledge-base' ),
				'name'        => 'article_search_title_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article_search_background_color' => array(
				'label'       => __( 'Search Background', 'echo-knowledge-base' ),
				'name'        => 'article_search_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'article_search_text_input_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'article_search_text_input_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'article_search_text_input_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'article_search_text_input_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#CCCCCC'
			),
			'article_search_btn_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'article_search_btn_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'article_search_btn_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'article_search_btn_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F1F1F1'
			),

			/***  Content ***/
			'background_color' => array(
				'label'       => __( 'Container Background', 'echo-knowledge-base' ),
				'name'        => 'background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     =>  '#FFFFFF'
			),

			/***  List of Articles ***/
            'article_typography' => array(
                'label'       => __( 'Typography', 'echo-knowledge-base' ),
                'name'        => 'article_typography',
                'type'        => EPKB_Input_Filter::TYPOGRAPHY,
                'default'     => array(
                    'font-family' => '',
                    'font-size' => '14',
                    'font-size-units' => 'px',
                    'font-weight' => '',
                )
            ),
			'article_font_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'article_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#459fed'
			),
			'article_icon_color' => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'article_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b3b3b3'
			),
			'section_body_background_color' => array(
				'label'       => __( 'Body Background', 'echo-knowledge-base' ),
				'name'        => 'section_body_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'section_border_color' => array(
				'label'       => __( 'Border Color', 'echo-knowledge-base' ),
				'name'        => 'section_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F7F7F7'
			),

			/***  Categories ***/
			'section_head_font_color' => array(
				'label'       => __( 'Category Text Color', 'echo-knowledge-base' ),
				'name'        => 'section_head_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'section_head_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'section_head_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'section_head_description_font_color' => array(
				'label'       => __( 'Category Description Color', 'echo-knowledge-base' ),
				'name'        => 'section_head_description_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#b3b3b3'
			),
			'section_divider_color' => array(
				'label'       => __( 'Divider', 'echo-knowledge-base' ),
				'name'        => 'section_divider_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#edf2f6'
			),
			'section_category_font_color' => array(
				'label'       => __( 'Text Color', 'echo-knowledge-base' ),
				'name'        => 'section_category_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#40474f'
			),
			'section_category_icon_color' => array(
				'label'       => __( 'Icon Color', 'echo-knowledge-base' ),
				'name'        => 'section_category_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#f7941d'
			),
			'section_head_category_icon_color' => array(
				'label'             => __( 'Category Icon', 'echo-knowledge-base' ),
				'name'              => 'section_head_category_icon_color',
			    'size'              => '10',
				'max'               => '7',
				'min'               => '7',
				'type'              => EPKB_Input_Filter::COLOR_HEX,
				'default'           => '#f7941d'
			),
			'section_head_typography' => array(
				'label'       => __( 'Name Typography', 'echo-knowledge-base' ),
				'name'        => 'section_head_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
								'font-family'       => '',
								'font-size'         => '21',
								'font-size-units'   => 'px',
								'font-weight'       => '',
				)
			),
			'section_head_description_typography' => array(
				'label'       => __( 'Description Typography', 'echo-knowledge-base' ),
				'name'        => 'section_head_description_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family'       => '',
					'font-size'         => '14',
					'font-size-units'   => 'px',
					'font-weight'       => '',
				)
			),


			/******************************************************************************
			 *
			 *  Front-End Text
			 *
			 ******************************************************************************/

            /***   Search  ***/

			'search_title' => array(
				'label'       => __( 'Search Title', 'echo-knowledge-base' ),
				'name'        => 'search_title',
				'size'        => '60',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'How Can We Help?', 'echo-knowledge-base' )
			),
			'search_box_hint' => array(
				'label'       => __( 'Search Hint', 'echo-knowledge-base' ),
				'name'        => 'search_box_hint',
				'size'        => '60',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search the documentation...', 'echo-knowledge-base' )
			),
			'search_button_name' => array(
				'label'       => __( 'Search Button Name', 'echo-knowledge-base' ),
				'name'        => 'search_button_name',
				'size'        => '25',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search', 'echo-knowledge-base' )
			),
			'search_results_msg' => array(
				'label'       => __( 'Search Results Message', 'echo-knowledge-base' ),
				'name'        => 'search_results_msg',
				'size'        => '60',
				'max'         => '80',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search Results for', 'echo-knowledge-base' )
			),

			'article_search_title' => array(
				'label'       => __( 'Search Title', 'echo-knowledge-base' ),
				'name'        => 'article_search_title',
				'size'        => '60',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'How Can We Help?', 'echo-knowledge-base' )
			),
			'article_search_box_hint' => array(
				'label'       => __( 'Search Hint', 'echo-knowledge-base' ),
				'name'        => 'article_search_box_hint',
				'size'        => '60',
				'max'         => '100',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search the documentation...', 'echo-knowledge-base' )
			),
			'article_search_button_name' => array(
				'label'       => __( 'Search Button Name', 'echo-knowledge-base' ),
				'name'        => 'article_search_button_name',
				'size'        => '25',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search', 'echo-knowledge-base' )
			),
			'article_search_results_msg' => array(
				'label'       => __( 'Search Results Message', 'echo-knowledge-base' ),
				'name'        => 'article_search_results_msg',
				'size'        => '60',
				'max'         => '80',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search Results for', 'echo-knowledge-base' )
			),

			'no_results_found' => array(
				'label'       => __( 'No Matches Found Text', 'echo-knowledge-base' ),
				'name'        => 'no_results_found',
				'size'        => '80',
				'max'         => '80',
				'min'         => '1',
				'allowed_tags' => array('a' => array(
													'href'  => true,
													'title' => true,
												)),
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'No matches found', 'echo-knowledge-base' )
			),
			'min_search_word_size_msg' => array(
				'label'       => __( 'Minimum Search Word Size Message', 'echo-knowledge-base' ),
				'name'        => 'min_search_word_size_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Enter a word with at least one character.', 'echo-knowledge-base' )
			),


            /***   Categories and Articles ***/

			'category_empty_msg' => array(
				'label'       => __( 'Empty Category Notice', 'echo-knowledge-base' ),
				'name'        => 'category_empty_msg',
				'size'        => '60',
				'max'         => '150',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Articles coming soon', 'echo-knowledge-base' )
			),
			'collapse_articles_msg' => array(
				'label'       => __( 'Collapse Articles Text', 'echo-knowledge-base' ),
				'name'        => 'collapse_articles_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Collapse Articles', 'echo-knowledge-base' )
			),
			'show_all_articles_msg' => array(
				'label'       => __( 'Show All Articles Text', 'echo-knowledge-base' ),
				'name'        => 'show_all_articles_msg',
				'size'        => '60',
				'max'         => '150',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Show all articles', 'echo-knowledge-base' )
			),
			'choose_main_topic' => array(
				'label'       => __( 'Drop Down Title', 'echo-knowledge-base' ),
				'name'        => 'choose_main_topic',
				'size'        => '60',
				'max'         => '150',
				'mandatory' => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Choose a Main Topic', 'echo-knowledge-base' )
			),
		);

		return $shared_specification;
	}

	/**
	 * Get KB default configuration
	 *
	 * @param int $kb_id is the ID of knowledge base to get default config for
	 * @return array contains default values for KB configuration
	 */
	public static function get_default_kb_config( $kb_id ) {
		$config_specs = self::get_fields_specification( $kb_id );

		$default_configuration = array();
		foreach( $config_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}

	/**
	 * Get names of all configuration items for KB configuration
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID ) );
	}

	/**
	 * Get names of all configuration items for KB configuration
	 * @return array
	 */
	public static function get_specs_item_name_keys() {
		$keys = array();
		foreach ( self::get_fields_specification( EPKB_KB_Config_DB::DEFAULT_KB_ID ) as $key => $spec ) {
			$keys[$key] = '';
		}
		return $keys;
	}
}

/** used by MKB as well */
abstract class EPKB_KB_Status
{
	const BLANK = 'blank';
	const ARCHIVED = 'archived';
	const PUBLISHED = 'published';
}
