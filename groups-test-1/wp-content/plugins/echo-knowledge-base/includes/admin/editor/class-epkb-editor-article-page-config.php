<?php

/**
 * Configuration for the front end editor
 */
 
class EPKB_Editor_Article_Page_Config extends EPKB_Editor_Base_Config {

	/** SEE DOCUMENTATION IN THE BASE CLASS **/

	/**
	 * Article Page zone
	 *
	 * @param $kb_config
	 * @param bool $with_presets
	 *
	 * @return array
	 */
	public static function page_zone( $kb_config, $with_presets = false ) {

		$theme = EPKB_Utilities::get_wp_option( 'stylesheet', 'unknown' );

		// use theme-specific text
		$default_width_info_text = '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> ' .
				             __('If your width is not expanding the way you want, it is because the theme is controlling the total width. ' .
				                'You have two options: either try switch to the KB Template option or check your theme settings to expand the width.', 'echo-knowledge-base') .
				                ' <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank">' .
		                           __( 'Learn More', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"></span></div>';
		if ( $theme == 'astra' ) {
			$default_width_info_text = '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> ' .
			         __( 'Astra theme is controlling the total width of the Article page. To increase the width, first configure the Astra setting for the page.', 'echo-knowledge-base' ) .
			         ' <a href="https://www.echoknowledgebase.com/documentation/astra-theme/" target="_blank">' . __( 'Learn More', 'echo-knowledge-base' ) . '</a> <span class="epkbfa epkbfa-external-link"></span></div>';
		}

		$settings = [

			// Content Tab

			// Style Tab

			// Features Tab
			'article-container-desktop-width-units-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'units',
				'target_selector' => '#eckb-article-page-container-v2',
			],
			'article-container-desktop-width-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'postfix' => 'article-container-desktop-width-units-v2',
				'styles' => [
					'#eckb-article-page-container-v2' => 'width',
				]
			],
			'article-width_info'                               => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'raw_html',
				'content' => $default_width_info_text
			],
			
			'article-container-tablet-width-units-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'units',
				'reload' => 1
			],
			'article-container-tablet-width-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],
			
			'article-container-breakpoint-header'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => __( 'Screen Breakpoints', 'echo-knowledge-base' ),
			],
			'article-tablet-break-point-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article-mobile-break-point-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

			'article-body-header'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header_desc',
				'title' => __( 'Body Container', 'echo-knowledge-base' ),
				'desc' => __( 'The container for the Left / Right Sidebars and the center content', 'echo-knowledge-base' ),
			],
			'article-body-desktop-width-units-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'units',
				'target_selector' => '#eckb-article-page-container-v2 #eckb-article-body'
			],
			'article-body-desktop-width-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'postfix' => 'article-body-desktop-width-units-v2',
				'styles' => [
					'#eckb-article-page-container-v2 #eckb-article-body' => 'width',
				]
			],

			'article-body-tablet-width-units-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'units',
				'target_selector' => '#eckb-article-page-container-v2 #eckb-article-body'
			],
			'article-body-tablet-width-v2' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'postfix' => 'article-body-tablet-width-units-v2',
				'styles' => [
					'#eckb-article-page-container-v2 #eckb-article-body' => 'width',
				]
			],

			// Advanced Tab
			'template_article_padding_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Padding', 'echo-knowledge-base' ),
				'subfields' => [
					'template_article_padding_left' => [
						'style_name' => 'padding-left',
					],
					'template_article_padding_top' => [
						'style_name' => 'padding-top',
					],
					'template_article_padding_right' => [
						'style_name' => 'padding-right',
					],
					'template_article_padding_bottom' => [
						'style_name' => 'padding-bottom',
					],
				]
			],
			'template_article_margin_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Margin', 'echo-knowledge-base' ),
				'subfields' => [
					'template_article_margin_left' => [
						'style_name' => 'margin-left',
					],
					'template_article_margin_top' => [
						'style_name' => 'margin-top',
					],
					'template_article_margin_right' => [
						'style_name' => 'margin-right',
					],
					'template_article_margin_bottom' => [
						'style_name' => 'margin-bottom',
					],
				]
			],
		];
		
		if ( $with_presets ) {
			$preset_options = [];
			$preset_options['current'] = __( 'Current', 'echo-knowledge-base' );
			
			foreach ( EPKB_KB_Wizard_Themes::get_all_presets( $kb_config ) as $theme_slug => $theme_data ) {
				$preset_options[$theme_data['kb_main_page_layout']][$theme_slug] = $theme_data['kb_name'];
			}

			$settings = array('theme_presets' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'default' => 'current',
				'label' => __( 'Theme Presets', 'echo-knowledge-base' ),
				'name' => 'theme',
				'options' => $preset_options,
				'type' => 'select'
			]) + $settings;
		}

		return [
			'content_zone' => [
				'title'     =>  __( 'Page Content', 'echo-knowledge-base' ),
				'classes'   => '#epkb-main-page-container, #eckb-article-page-container-v2',
				'settings'  => $settings,
				'parent_zone_tab_title' => __( 'Page Content', 'echo-knowledge-base' )
			]];
	}

	/**
	 * Serach Box zone
	 * @return array
	 */
	private static function article_search_box_zone() {

		$settings = [

			// Content Tab

			// Style Tab
			'article_search_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-doc-search-container',
				'style_name' => 'background-color'
			],

			// Features Tab
			'article_search_layout' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],

			// Advanced Tab
			'article_search_box_padding' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'article_search_box_padding_left' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-left',
						'postfix' => 'px'
					],
					'article_search_box_padding_top' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-top',
						'postfix' => 'px'
					],
					'article_search_box_padding_right' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-right',
						'postfix' => 'px'
					],
					'article_search_box_padding_bottom' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'padding-bottom',
						'postfix' => 'px'
					],
				]
			],
			'article_search_box_margin' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Margin', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'article_search_box_margin_top' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'margin-top',
						'postfix' => 'px'
					],
					'article_search_box_margin_bottom' => [
						'target_selector' => '.epkb-doc-search-container',
						'style_name' => 'margin-bottom',
						'postfix' => 'px'
					],
				]
			],
		];

		return [
			'article_search_box' => [
				'title'     =>  __( 'Search Box', 'echo-knowledge-base' ),
				'classes'   => '.epkb-doc-search-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'article_search_layout' => 'epkb-search-form-0'
				],
				'parent_zone_tab_title' => __( 'Search Box', 'echo-knowledge-base' ),
			]];
	}

	/**
	 * Search Title zone
	 * @return array
	 */
	private static function article_search_title_zone() {

		$settings = [
			'article_search_title' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-doc-search-container__title',
				'target_attr' => 'value',
				'text' => 1
			],
			'article_search_title_html_tag' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-doc-search-container__title',
				'reload' => 1,
				'text_style' => 'inline'
			],
			'article_search_title_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-doc-search-container__title',
			],
			'article_search_title_font_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-doc-search-container__title',
				'style_name' => 'color'
			],
		];

		return [
			'article_search_title_zone' => [
				'title'     =>  __( 'Search Title', 'echo-knowledge-base' ),
				'classes'   => '.epkb-doc-search-container__title',
				'settings'  => $settings
			]];
	}

	/**
	 * Search Input box zone
	 * @return array
	 */
	private static function article_search_input_zone() {

		$settings = [
			'article_search_box_hint' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#epkb_search_terms',
				'target_attr' => 'placeholder|aria-label',
			],
			'article_search_results_msg' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],
			'no_results_found' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],
			'min_search_word_size_msg' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],
			'article_search_input_border_width' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box input[type=text]',
				'style_name' => 'border-width',
				'postfix' => 'px',
				'style'       => 'small',
			],
			'article_search_text_input_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box input[type=text]',
				'style_name' => 'border-color'
			],
			'article_search_text_input_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box input[type=text]',
				'style_name' => 'background-color'
			],
			'article_search_box_results_style' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'target_selector' => '#epkb_article_search_results',
			],
			'article_search_box_input_width' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'target_selector' => '#epkb_search_form',
				'style_name' => 'width',
				'postfix' => '%'
			],
		];

		return [
			'article_search_input_zone' => [
				'title'     =>  __( 'Search Input Box', 'echo-knowledge-base' ),
				'classes'   => '.epkb-doc-search-container input',
				'settings'  => $settings
			]];
	}

	/**
	 * Serach Button zone
	 * @return array
	 */
	private static function article_search_button_zone() {

		$settings = [
			'article_search_button_name' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#epkb-search-kb',
				'target_attr' => 'value',
				'text' => 1
			],
			'article_search_btn_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box button',
				'style_name' => 'background-color'
			],
			'article_search_btn_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-search-box button',
				'style_name' => 'border-color'
			],
		];

		return [
			'article_search_button_zone' => [
				'title'     =>  __( 'Search Button', 'echo-knowledge-base' ),
				'classes'   => '.epkb-search-box button',
				'disabled_settings' => [
					'article_search_layout' => 'epkb-search-form-3'
				],
				'settings'  => $settings
			]];
	}

	/**
	 * Left Sidebar zone
	 * @param $kb_config
	 * @return array
	 */
	private static function left_sidebar_zone( $kb_config ) {

		$options = array(
			'0' => __( 'Not displayed', 'echo-knowledge-base' ),
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5'
		);
		$options2 = array(
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5'
		);

		$settings = [

			// Content Tab

			// Style Tab
			'article-left-sidebar-background-color-v2' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-left-sidebar',
				'style_name' => 'background-color'
			],

			// Features Tab
			'article-left-sidebar-toggle'                => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article-left-sidebar-match'                => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article-left-sidebar-starting-position'    => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '#eckb-article-page-container-v2 #eckb-article-left-sidebar',
				'style_name' => 'margin-top',
				'postfix' => 'px',
				'reload' => '1',
				'style_important' => 0
			],
			'article-left-sidebar-header-desktopWidth'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => __( 'Sidebar Width', 'echo-knowledge-base' ),
			],
			'article-left-sidebar-desktop-width-v2'     => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style' => 'slider',
				'max' => 40
			],
			'article-left-sidebar-tablet-width-v2'      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

			// Advanced Tab
			'article-left-sidebar-padding' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'article-left-sidebar-padding-v2_left' => [
						'target_selector' => '#eckb-article-left-sidebar',
						'style_name' => 'padding-left',
						'postfix' => 'px'
					],
					'article-left-sidebar-padding-v2_top' => [
						'target_selector' => '#eckb-article-left-sidebar',
						'style_name' => 'padding-top',
						'postfix' => 'px'
					],
					'article-left-sidebar-padding-v2_right' => [
						'target_selector' => '#eckb-article-left-sidebar',
						'style_name' => 'padding-right',
						'postfix' => 'px'
					],
					'article-left-sidebar-padding-v2_bottom' => [
						'target_selector' => '#eckb-article-left-sidebar',
						'style_name' => 'padding-bottom',
						'postfix' => 'px'
					],
				]
			],

			// sidebar components priority
			'article-left-sidebar-header-locations'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header_desc',
				'title' => __( 'Widget Sidebar Location', 'echo-knowledge-base' ),
				'desc' => __( 'Number 1 places the Widget Sidebar first, 2 below it, and so on.', 'echo-knowledge-base' )
			],
			'kb_sidebar_left' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'label'            => __( 'Widgets', 'echo-knowledge-base' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'    => $options,
				'default'     => '0',
				'reload' => '1'
			],
		];

		return [
			'left_sidebar' => [
				'title'     =>  is_rtl() ? __( 'Right Sidebar', 'echo-knowledge-base' ) : __( 'Left Sidebar', 'echo-knowledge-base' ),
				'classes'   => '#eckb-article-left-sidebar',
				'settings'  => $settings,
				'parent_zone_tab_title' => __( 'Sidebar', 'echo-knowledge-base' ),
				'disabled_settings' => [
					'article-left-sidebar-toggle' => 'off'
				]
			]];
	}

	/**
	 * Article Content (Center Content) zone
	 * @return array
	 */
	private static function article_content_zone() {

		$settings = [

			// Content Tab

			// Style Tab
			'article-content-background-color-v2'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-content',
				'style_name' => 'background-color'
			],

			'article_content_enable_rows_1_header'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header_desc',
				'toggler' => 'article_content_enable_rows',
				'title' => 'Article Header Row #1',
				'desc' => ''
			],
			'article_content_enable_rows_1_gap'             => [
				'editor_tab'        => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'target_selector'   => '#eckb-article-content-header-row-1',
				'style'             => 'slider',
				'postfix'           => 'px',
				'styles' => [
					'#eckb-article-content-header-row-1' => 'margin-bottom',
				]
			],
			'article_content_enable_rows_1_alignment'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'style' => 'small',
				'styles' => [
					'#eckb-article-content-header-row-1 .eckb-article-content-header-row-left-group' => 'align-items',
					'#eckb-article-content-header-row-1 .eckb-article-content-header-row-right-group' => 'align-items',
				]
			],

			'article_content_enable_rows_2_header'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'type' => 'header_desc',
				'title' => 'Article Header Row #2',
				'desc' => ''
			],
			'article_content_enable_rows_2_gap'             => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'target_selector' => '#eckb-article-content-header-row-2',
				'style' => 'slider',
				'postfix' => 'px',
				'styles' => [
					'#eckb-article-content-header-row-2' => 'margin-bottom',
				]

			],
			'article_content_enable_rows_2_alignment'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'style' => 'small',
				'styles' => [
					'#eckb-article-content-header-row-2 .eckb-article-content-header-row-left-group' => 'align-items',
					'#eckb-article-content-header-row-2 .eckb-article-content-header-row-right-group' => 'align-items',
				]
			],

			'article_content_enable_rows_3_header'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'type' => 'header_desc',
				'title' => 'Article Header Row #3',
				'desc' => ''
			],
			'article_content_enable_rows_3_gap'             => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'target_selector' => '#eckb-article-content-header-row-3',
				'style' => 'slider',
				'postfix' => 'px',
				'styles' => [
					'#eckb-article-content-header-row-3' => 'margin-bottom',
				]
			],
			'article_content_enable_rows_3_alignment'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'style' => 'small',
				'styles' => [
					'#eckb-article-content-header-row-3 .eckb-article-content-header-row-left-group' => 'align-items',
					'#eckb-article-content-header-row-3 .eckb-article-content-header-row-right-group' => 'align-items',
				]
			],

			'article_content_enable_rows_4_header'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'type' => 'header_desc',
				'title' => 'Article Header Row #4',
				'desc' => ''
			],
			'article_content_enable_rows_4_gap'             => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'target_selector' => '#eckb-article-content-header-row-4',
				'style' => 'slider',
				'postfix' => 'px',
				'styles' => [
					'#eckb-article-content-header-row-4' => 'margin-bottom',
				]
			],
			'article_content_enable_rows_4_alignment'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'style' => 'small',
				'styles' => [
					'#eckb-article-content-header-row-4 .eckb-article-content-header-row-left-group' => 'align-items',
					'#eckb-article-content-header-row-4 .eckb-article-content-header-row-right-group' => 'align-items',
				]
			],

			'article_content_enable_rows_5_header'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'type' => 'header_desc',
				'title' => 'Article Header Row #5',
				'desc' => ''
			],
			'article_content_enable_rows_5_gap'             => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'target_selector' => '#eckb-article-content-header-row-5',
				'style' => 'slider',
				'postfix' => 'px',
				'styles' => [
					'#eckb-article-content-header-row-5' => 'margin-bottom',
				]
			],
			'article_content_enable_rows_5_alignment'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'toggler'           => 'article_content_enable_rows',
				'style' => 'small',
				'styles' => [
					'#eckb-article-content-header-row-5 .eckb-article-content-header-row-left-group' => 'align-items',
					'#eckb-article-content-header-row-5 .eckb-article-content-header-row-right-group' => 'align-items',
				]
			],

			// Features Tab
			'article_content_enable_rows_header'            => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header_desc',
				'title' => 'Article Header Rows',
				'desc' => 'Use this option to control where each feature will be positioned above the article.'
			],
			'article_content_enable_rows'                   => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_content_other__header'                 => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header_desc',
				'title' => 'Other',
				'desc' => ''
			],
			'article-content-desktop-width-v2'              => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'hidden'
			],
			'article-content-tablet-width-v2'               => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'hidden'
			],
			'articles_comments_global'                      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				'info_url' => 'https://www.echoknowledgebase.com/documentation/wordpress-enabling-article-comments/',
			],

			// Advanced Tab
			'article-content-padding-v2'                    => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'target_selector' => '#eckb-article-page-container-v2 #eckb-article-content',
				'style_name' => 'padding',
				'style' => 'slider',
				'postfix' => 'px'
			],
			'template_article_reset_header'         => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'type' => 'header_desc',
				'title' => 'Content Styling',
				'desc' => 'If you are having content styling issues, such as missing bullets, try the settings below.'
			],
			'templates_for_kb_article_reset'                => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'description' => __( 'If enabled, you should Add KB style as well (below).',  'echo-knowledge-base' ),
				'reload' => '1',
			],
			'templates_for_kb_article_defaults'             => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'description' => __( 'Enable this option if your theme does not add styling to the article.',  'echo-knowledge-base' ),
				'reload' => '1',
			],

		];

		return [
			'article_content' => [
				'title'     =>  __( 'Article Content', 'echo-knowledge-base' ),
				'classes'   => '#eckb-article-content',
				'disabled_settings' => [
					'article_content_enable_rows' => 'off'
				],
				'settings'  => $settings
			]];
	}

	/**
	 * Right Sidebar zone
	 * @return array
	 */
	private static function right_sidebar_zone() {

		$options = array(
			'0' => __( 'Not displayed', 'echo-knowledge-base' ),
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5'
		);

		$settings = [

			// Content Tab

			// Style Tab
			'article-right-sidebar-background-color-v2' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-right-sidebar',
				'style_name' => 'background-color'
			],

			// Features Tab
			'article-right-sidebar-toggle'                => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article-right-sidebar-match'               => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],
			'article-right-sidebar-starting-position'   => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				'target_selector' => '#eckb-article-page-container-v2 #eckb-article-right-sidebar',
				'style_name' => 'margin-top',
				'postfix' => 'px',
				'style_important' => 0
			],
			'article-left-sidebar-header-desktopWidth'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => 'Sidebar Width'
			],
			'article-right-sidebar-desktop-width-v2'    => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style' => 'slider',
				'max' => 40
			],
			'article-right-sidebar-tablet-width-v2'     => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],

			// Advanced Tab
			'article-right-sidebar-padding' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'article-right-sidebar-padding-v2_left' => [
						'target_selector' => '#eckb-article-right-sidebar',
						'style_name' => 'padding-left',
						'postfix' => 'px'
					],
					'article-right-sidebar-padding-v2_top' => [
						'target_selector' => '#eckb-article-right-sidebar',
						'style_name' => 'padding-top',
						'postfix' => 'px'
					],
					'article-right-sidebar-padding-v2_right' => [
						'target_selector' => '#eckb-article-right-sidebar',
						'style_name' => 'padding-right',
						'postfix' => 'px'
					],
					'article-right-sidebar-padding-v2_bottom' => [
						'target_selector' => '#eckb-article-right-sidebar',
						'style_name' => 'padding-bottom',
						'postfix' => 'px'
					],
				]
			],

			// sidebar components priority
			'article-right-sidebar-header-locations'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header_desc',
				'title' => __( 'Widget Sidebar Location', 'echo-knowledge-base' ),
				'desc' => __( 'Number 1 places the Widget Sidebar first, 2 below it, and so on.', 'echo-knowledge-base' ),
			],

			'kb_sidebar_right' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'label'            => __( 'Widgets', 'echo-knowledge-base' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'    => $options,
				'default'     => '0',
				'reload' => '1'
			],
		];

		return [
			'right_sidebar' => [
				'title'     =>  is_rtl() ? __( 'Left Sidebar', 'echo-knowledge-base' ) : __( 'Right Sidebar', 'echo-knowledge-base' ),
				'parent_zone_tab_title' => __( 'Sidebar', 'echo-knowledge-base' ), // example
				'classes'   => '#eckb-article-right-sidebar',
				'settings'  => $settings,
				'disabled_settings' => [
					'article-right-sidebar-toggle' => 'off'
				]
			]];
	}

	/**
	 * Meta Data HEADER zone
	 * @return array
	 */
	private static function meta_data_header_zone() {

		$settings = [

			// Content Tab
			'last_udpated_on_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-ach__article-meta__date-updated__text',
				'target_attr' => 'value',
				'text' => '1',
			],
			'created_on_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-ach__article-meta__date-created__text',
				'target_attr' => 'value',
				'text' => '1',
			],
			'author_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-ach__article-meta__author__text',
				'target_attr' => 'value',
				'text' => '1',
			],

			// Feature Tab
			'meta-data-header-toggle'   => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_content_enable_last_updated_date' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_content_enable_created_date' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_content_enable_author' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_meta_icon_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

		];

		return [
			'meta_data_header' => [
				'title'     =>  __( 'Top Author and Dates', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-content-header__article-meta',
				'settings'  => $settings,
				'disabled_settings' => [
					'meta-data-header-toggle' => 'off',
				]
			]];
	}

	/**
	 * TOC zone
	 * @return array
	 */
	private static function toc_zone() {

		$settings = [

			// Content Tab
			'article_toc_title' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-toc__title',
				'target_attr' => 'value',
				'text' => '1',
			],

			// Style Tab
			'article_toc_header' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'TOC Title', 'echo-knowledge-base' )
			],
			'article_toc_header_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-body .eckb-article-toc__title',
			],
			
			'article_toc_title_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-body .eckb-article-toc__title',
				'style_name' => 'color'
			],
			
			'article_toc_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-toc__inner',
				'style_name' => 'background-color'
			],
			'article_toc_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-toc__inner',
				'style_name' => 'border-color'
			],
			'article_toc_headings' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'TOC Headings', 'echo-knowledge-base' )
			],
			'article_toc_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-body .eckb-article-toc__inner, #eckb-article-body .eckb-article-toc__inner a',
			],
			'article_toc_text_color' => [
				'editor_tab'        => self::EDITOR_TAB_STYLE,
				'target_selector'   => '.eckb-article-toc__inner a',
				'style_name'        => 'color',
			],
			'article_toc_active_bg_color' => [
				'editor_tab'        => self::EDITOR_TAB_STYLE,
				'target_selector'   => '#eckb-article-body .eckb-article-toc ul a.active',
				'style_name' => 'background-color'
			],
			'article_toc_active_text_color' => [
				'editor_tab'        => self::EDITOR_TAB_STYLE,
				'target_selector'   => '#eckb-article-body .eckb-article-toc ul a.active',
				'style_name' => 'color'
			],
			'article_toc_cursor_hover_bg_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-body .eckb-article-toc ul a:hover',
				'style_name' => 'background-color'
			],
			'article_toc_cursor_hover_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-body .eckb-article-toc ul a:hover',
				'style_name' => 'color'
			],

			// Features Tab
			'toc_left' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'label'            => is_rtl() ? __( 'Display on the Right', 'echo-knowledge-base' ) : __( 'Display on the Left', 'echo-knowledge-base' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'description' => __( 'Number 1 places the element at the top, 2 below it, and so on.', 'echo-knowledge-base' ),
				'options'    => array(
					'0' => __( 'Not displayed', 'echo-knowledge-base' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5'
				),
				'default'     => '0',
				'reload' => '1'
			],
			'toc_content'               => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'label'            => __( 'Display Above the Article', 'echo-knowledge-base' ),
				'default'     => '0',
				'reload' => '1',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'    => array(
					'0' => __( 'Not displayed', 'echo-knowledge-base' ),
					'1' => __( 'Displayed', 'echo-knowledge-base' )
				),
			],
			'toc_right' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'label'            => is_rtl() ? __( 'Display on the Left', 'echo-knowledge-base' ) : __( 'Display on the Right', 'echo-knowledge-base' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'description' => __( 'Number 1 places the element at the top, 2 below it, and so on.', 'echo-knowledge-base' ),
				'options'    => array(
					'0' => __( 'Not displayed', 'echo-knowledge-base' ),
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5'
				),
				'default'     => '1',
				'reload' => '1'
			],

			'article_toc_border_mode'   => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_toc_lvl_header'    => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => __( 'Header Range', 'echo-knowledge-base' )
			],
			'article_toc_hx_level'      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],
			'article_toc_hy_level'      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_toc_scroll_offset' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				'separator_above'   => 'yes',
			],

			//Advanced Tab
			'article_toc_exclude_class' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'reload' => '1'
			],

			'article_toc_width_header'  => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'type' => 'header',
				'content' => __( 'Container Width', 'echo-knowledge-base' )
			],
			'article_toc_width_1'       => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
			],
			'article_toc_width_2'       => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
			],

			'article_toc_resolutions_header'  => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'type' => 'header',
				'content' => __( 'Starting Resolutions for Widths', 'echo-knowledge-base' )
			],

			'article_toc_media_1' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
			],

			'article_toc_media_2' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
			],
			'article_toc_media_3' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
			],
		];

		return [
			'toc' => [
				'title'     =>  __( 'TOC', 'echo-knowledge-base' ),
				'zone_tab_title'     =>  __( 'TOC', 'echo-knowledge-base' ), // example
				'classes'   => '.eckb-article-toc',
				'disabled_settings' => [
					'toc_left' => '0',
					'toc_content' => '0',
					'toc_right' => '0'
				],
				'settings'  => $settings
			]];
	}

	/**
	 * Article Title zone
	 * @return array
	 */
	private static function article_title_zone() {

		$settings = [

			// Style Tab
			'link_to_article_content_style'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content" 
								class="epkb-editor-navigation__link">' . __( 'Edit the row style under the Article Content here' , 'echo-knowledge-base' ) . '</a></div>'
			],
			'article_title_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-article-body .eckb-article-title',
			],
			
			// Features Tab
			'article_content_enable_article_title'          => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'separator_above'   => 'yes',
				'reload' => '1'
			],
			'article_title_row' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_title_alignment' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			//	'style' => 'prev-next',
			],
			'article_title_sequence' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
			//	'style' => 'prev-next',
				'target_selector' => '#eckb-article-content-title-container',
			//	'style_name' => 'order',
				'reload' => '1',
			],
		];

		return [
			'article_title' => [
				'title'     =>  __( 'Article Title', 'echo-knowledge-base' ),
				'classes'   => '#eckb-article-content-title-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'article_content_enable_article_title' => 'off'
				],
			]];
	}

	/**
	 * Back Navigation zone
	 * @return array
	 */
	private static function back_navigation_zone() {

		$settings = [

			// Content Tab
			'back_navigation_text'          => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#eckb-article-back-navigation-container .eckb-navigation-button',
				'target_attr' => 'value',
				'text' => '1',
			],

			// Style Tab
			'back_navigation_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '
				#eckb-article-back-navigation-container .eckb-navigation-button a, 
				#eckb-article-back-navigation-container .eckb-navigation-button',
			],
			'back_navigation_text_color'    => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '
				#eckb-article-back-navigation-container .eckb-navigation-back .eckb-navigation-button a, 
				#eckb-article-back-navigation-container .eckb-navigation-back .eckb-navigation-button',
				'style_name' => 'color'
			],
			'back_navigation_bg_color'      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '
				#eckb-article-back-navigation-container .eckb-navigation-back .eckb-navigation-button a, 
				#eckb-article-back-navigation-container .eckb-navigation-back .eckb-navigation-button',
				'style_name' => 'background-color'
			],
			'back_navigation_border_color'  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '
				#eckb-article-back-navigation-container .eckb-navigation-back .eckb-navigation-button a, 
				#eckb-article-back-navigation-container .eckb-navigation-back .eckb-navigation-button',
				'style_name' => 'border-color'
			],
			'link_to_article_content_style'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content" 
								class="epkb-editor-navigation__link">' . __( 'Edit the row style under the Article Content here', 'echo-knowledge-base' ) . '</a></div>'
			],

			// Features Tab
			'article_content_enable_back_navigation'        => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'back_navigation_row' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'toggler'           => 'article_content_enable_rows',
				'reload' => '1'
			],
			'back_navigation_alignment' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'toggler'           => 'article_content_enable_rows',
				'reload' => '1',
			//	'style' => 'prev-next',
			],
			'back_navigation_sequence' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'toggler'           => 'article_content_enable_rows',
			//	'style' => 'prev-next',
				'target_selector' => '#eckb-article-back-navigation-container',
			//	'style_name' => 'order',
				'reload' => '1',
			],
			'back_navigation_mode'          => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
			],
			'back_navigation_border'        => [
				'editor_tab'        => self::EDITOR_TAB_FEATURES,
				'target_selector' => '#eckb-article-back-navigation-container  .eckb-navigation-button',
				'style_name' => 'border-style',
				'separator_above'   => 'yes',
			],
			'back_navigation_border_radius' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '#eckb-article-back-navigation-container  .eckb-navigation-button',
				'style_name' => 'border-radius',
				'postfix' => 'px'
			],
			'back_navigation_border_width'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'target_selector' => '#eckb-article-back-navigation-container  .eckb-navigation-button',
				'style_name' => 'border-width',
				'postfix' => 'px'
			],

			// Advanced Tab
			'back_navigation_padding_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Padding', 'echo-knowledge-base' ),
				'subfields' => [
					'back_navigation_padding_left' => [
						'style_name' => 'padding-left',
						'target_selector' => '.eckb-navigation-button',
						'postfix' => 'px',
					],
					'back_navigation_padding_top' => [
						'style_name' => 'padding-top',
						'target_selector' => '.eckb-navigation-button',
						'postfix' => 'px',
					],
					'back_navigation_padding_right' => [
						'style_name' => 'padding-right',
						'target_selector' => '.eckb-navigation-button',
						'postfix' => 'px',
					],
					'back_navigation_padding_bottom' => [
						'style_name' => 'padding-bottom',
						'target_selector' => '.eckb-navigation-button',
						'postfix' => 'px',
					],
				]
			],
			'back_navigation_margin_group'  => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Margin', 'echo-knowledge-base' ),
				'subfields' => [
					'back_navigation_margin_left' => [
						'style_name' => 'margin-left',
						'target_selector' => '.eckb-navigation-back',
						'postfix' => 'px',
					],
					'back_navigation_margin_top' => [
						'style_name' => 'margin-top',
						'target_selector' => '.eckb-navigation-back',
						'postfix' => 'px',
					],
					'back_navigation_margin_right' => [
						'style_name' => 'margin-right',
						'target_selector' => '.eckb-navigation-back',
						'postfix' => 'px',
					],
					'back_navigation_margin_bottom' => [
						'style_name' => 'margin-bottom',
						'target_selector' => '.eckb-navigation-back',
						'postfix' => 'px',
					],
				]
			],

		];

		return [
			'back_navigation' => [
				'title'     =>  __( 'Back Navigation', 'echo-knowledge-base' ),
				'classes'   => '.eckb-navigation-back  ',
				'settings'  => $settings,
				'disabled_settings' => [
					'article_content_enable_back_navigation' => 'off'
				]
			]];
	}

	/**
	 * Author zone
	 * @return array
	 */
	private static function author_zone() {

		$settings = [

			// Content Tab
			'author_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-content-author-container .eckb-meta-data-feature-text',
				'target_attr' => 'value',
				'text' => '1',
			],

			// Style Tab
			'link_to_article_content_style'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content" 
								class="epkb-editor-navigation__link">' . __( 'Edit the row style under the Article Content here', 'echo-knowledge-base' ) . '</a></div>'
			],

			// Features Tab
			'article_content_enable_author'                 => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'author_row' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'author_alignment' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			//	'style' => 'prev-next',
			],
			'author_sequence' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
			//	'style' => 'prev-next',
				'target_selector' => '.eckb-article-content-author-container',
			//	'style_name' => 'order',
				'reload' => '1',
			],
			'author_icon_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

		];

		return [
			'author' => [
				'title'     =>  __( 'Author', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-content-author-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'article_content_enable_author' => 'off'
				]
			]];
	}

	/**
	 * Created Date zone
	 * @return array
	 */
	private static function created_date_zone() {

		$settings = [

			// Content Tab
			'created_on_text'                       => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-content-created-date-container .eckb-meta-data-feature-text',
				'target_attr' => 'value',
				'text' => '1',
			],

			// Style Tab
			'link_to_article_content_style'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content" 
								class="epkb-editor-navigation__link">'. __( 'Edit the row style under the Article Content here' , 'echo-knowledge-base' ) .'</a></div>'
			],

			// Features Tab
			'article_content_enable_created_date'   => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'created_date_row'                      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'created_date_alignment'                => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				//'style' => 'prev-next',
			],
			'created_date_sequence'                 => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
			//	'style' => 'prev-next',
				'target_selector' => '.eckb-article-content-created-date-container',
			//	'style_name' => 'order',
				'reload' => '1',
			],
			'created_date_icon_on'                  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
		];

		return [
			'created_date' => [
				'title'     =>  __( 'Created Date', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-content-created-date-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'article_content_enable_created_date' => 'off'
				]
			]];
	}

	/**
	 * Last Updated Date zone
	 * @return array
	 */
	private static function last_updated_date_zone() {

		$settings = [

			//Content Tab
			'last_udpated_on_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-article-content-last-updated-date-container .eckb-meta-data-feature-text',
				'target_attr' => 'value',
				'text' => '1',
			],

			// Style Tab
			'link_to_article_content_style'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content" 
								class="epkb-editor-navigation__link">' . __( 'Edit the row style under the Article Content here', 'echo-knowledge-base' ). '</a></div>'
			],

			// Features Tab
			'article_content_enable_last_updated_date'      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'last_updated_date_row' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'last_updated_date_alignment' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			//	'style' => 'prev-next',
			],
			'last_updated_date_sequence' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
			//	'style' => 'prev-next',
				'target_selector' => '.eckb-article-content-last-updated-date-container',
			//	'style_name' => 'order',
				'reload' => '1',
			],
			'last_updated_date_icon_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

		];

		return [
			'last_updated_date' => [
				'title'     =>  __( 'Last Updated Date', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-content-last-updated-date-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'article_content_enable_last_updated_date' => 'off'
				]
			]];
	}

	/**
	 * Breadcrumb zone
	 * @return array
	 */
	private static function breadcrumb_zone() {

		$settings = [

			// Content Tab
			'breadcrumb_description_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-breadcrumb-label',
				'target_attr' => 'value',
				'text' => '1',
			],
			'breadcrumb_home_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-breadcrumb-nav li:first-child a span, .eckb-breadcrumb-nav li:first-child .eckb-breadcrumb-link span:first-child',
				'target_attr' => 'value',
				'text' => '1',
			],

			// Style Tab
			'breadcrumb_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-breadcrumb, .eckb-breadcrumb li',
			],
			'breadcrumb_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-breadcrumb-link span:not(.eckb-breadcrumb-link-icon)',
				'style_name' => 'color'
			],
			'link_to_article_content_style'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content"
                           class="epkb-editor-navigation__link">' . __( 'Edit the row style under the Article Content here', 'echo-knowledge-base' ) .'</a></div>'
			],

			// Features Tab
			'breadcrumb_enable' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'breadcrumb_row' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'toggler'           => 'article_content_enable_rows',
				'reload' => '1'
			],
			'breadcrumb_alignment' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'toggler'           => 'article_content_enable_rows',
				'reload' => '1',
				//'style' => 'prev-next',
			],
			'breadcrumb_sequence' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'toggler'           => 'article_content_enable_rows',
			//	'style' => 'prev-next',
				'target_selector' => '#eckb-article-content-breadcrumb-container',
				//'style_name' => 'order',
				'reload' => '1',
			],
			'breadcrumb_icon_separator' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'breadcrumb_padding_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Padding', 'echo-knowledge-base' ),
				'subfields' => [
					'breadcrumb_padding_left' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'padding-left',
					],
					'breadcrumb_padding_top' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'padding-top',
					],
					'breadcrumb_padding_right' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'padding-right',
					],
					'breadcrumb_padding_bottom' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'padding-bottom',
					],
				]
			],
			'breadcrumb_margin_group' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'toggler'         => 'article_content_enable_rows',
				'label' => __( 'Margin', 'echo-knowledge-base' ),
				'subfields' => [
					'breadcrumb_margin_left' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'margin-left',
					],
					'breadcrumb_margin_top' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'margin-top',
					],
					'breadcrumb_margin_right' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'margin-right',
					],
					'breadcrumb_margin_bottom' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'margin-bottom',
					],
				]
			],
			'breadcrumb_margin_group_old' => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Margin', 'echo-knowledge-base' ),
				'toggler' => [
					'article_content_enable_rows' => 'off',
				],
				'subfields' => [
					'breadcrumb_margin_left' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'margin-left',
					],
					'breadcrumb_margin_top' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'margin-top',
					],
					'breadcrumb_margin_right' => [
						'target_selector' => '.eckb-breadcrumb',
						'postfix' => 'px',
						'style_name' => 'margin-right',
					],
					'breadcrumb_margin_bottom_old' => [
						'target_selector' => '.eckb-breadcrumb',
						'toggler'         => 'article_content_enable_rows',
						'postfix' => 'px',
						'style_name' => 'margin-bottom',
					],
				]
			],
		];

		return [
			'breadcrumb' => [
				'title'     =>  __( 'Breadcrumb', 'echo-knowledge-base' ),
				'classes'   => '.eckb-breadcrumb',
				'settings'  => $settings,
				'disabled_settings' => [
					'breadcrumb_enable' => 'off'
				]
			]];
	}

	/**
	 * Article Content Toolbar zone
	 * @return array
	 */
	private static function article_content_toolbar_zone() {

		$settings = [

			// Content Tab

			// Style Tab
			'article_content_toolbar_button_TabStyle_header'             => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Buttons', 'echo-knowledge-base' ),
			],
			'article_content_toolbar_button_background'                  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-content-toolbar-button-container',
				'style_name' => 'background-color',
			],
			'article_content_toolbar_button_background_hover'            => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
			],
			'article_content_toolbar_text_color'                         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-content-toolbar-button-container .eckb-toolbar-button-text',
				'style_name' => 'color',
			],
			'article_content_toolbar_text_hover_color'                   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
			],
			'article_content_toolbar_text_size'                          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'style' => 'slider',
				'style_name' => 'font-size',
				'postfix' => 'px',
				'target_selector' => '.eckb-article-content-toolbar-button-container .eckb-toolbar-button-text',
			],
			'article_content_toolbar_icon_color'                         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-content-toolbar-button-container .eckb-toolbar-button-icon',
				'style_name' => 'color',
			],
			'article_content_toolbar_icon_hover_color'                   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
			],
			'article_content_toolbar_icon_size'                          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'style' => 'slider',
				'style_name' => 'font-size',
				'postfix' => 'px',
				'target_selector' => '.eckb-article-content-toolbar-button-container .eckb-toolbar-button-icon',
			],
			'article_content_toolbar_border_width'                       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-content-toolbar-button-container',
				'style_name' => 'border-width',
				'postfix' => 'px',
			],
			'article_content_toolbar_border_radius'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-content-toolbar-button-container',
				'style_name' => 'border-radius',
				'postfix' => 'px',
			],
			'article_content_toolbar_border_color'                       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-content-toolbar-button-container',
				'style_name' => 'border-color',
				'description' => __( 'The color appears only if the border width is larger than zero', 'echo-knowledge-base' ),
			],
			'link_to_article_content_style'                              => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content" 
							class="epkb-editor-navigation__link">' . __( 'Edit the row style under the Article Content here', 'echo-knowledge-base' ) . '</a></div>'
			],

			// Features Tab
			'article_content_toolbar_button_format'                      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
			],
			'article_content_toolbar_row' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_content_toolbar_alignment'                          => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				//'style' => 'prev-next',
			],
			'article_content_toolbar_sequence'                           => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1',
				//'style' => 'prev-next',
			],

			// Advanced Tab
			'article_content_toolbar_button_heading'                     => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'type' => 'header',
				'content' => __( 'Buttons', 'echo-knowledge-base' ),
			],
			'article_content_toolbar_button_padding'                     => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'article_content_toolbar_button_padding_left' => [
						'style_name' => 'padding-left',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
					'article_content_toolbar_button_padding_top' => [
						'style_name' => 'padding-top',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
					'article_content_toolbar_button_padding_right' => [
						'style_name' => 'padding-right',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
					'article_content_toolbar_button_padding_bottom' => [
						'style_name' => 'padding-bottom',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
				]
			],
			'article_content_toolbar_button_margin'                      => [
				'editor_tab' => self::EDITOR_TAB_ADVANCED,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Margin', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'article_content_toolbar_button_margin_left' => [
						'style_name' => 'margin-left',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
					'article_content_toolbar_button_margin_top' => [
						'style_name' => 'margin-top',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
					'article_content_toolbar_button_margin_right' => [
						'style_name' => 'margin-right',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
					'article_content_toolbar_button_margin_bottom' => [
						'style_name' => 'margin-bottom',
						'postfix' => 'px',
						'target_selector' => '.eckb-article-content-toolbar-button-container',
					],
				]
			],
		];

		return [
			'article_content_toolbar' => [
				'title'     =>  __( 'Article Content Toolbar', 'echo-knowledge-base' ),
				'classes'   => '#eckb-article-content-toolbar-container',
				'settings'  => $settings,
				'parent_zone_tab_title' => __( 'Article Content Toolbar', 'echo-knowledge-base' ),
				'disabled_settings' => [
					'article_content_toolbar_enable' => 'off',
				],
			]];
	}

	/**
	 * Breadcrumb zone
	 * @return array
	 */
	private static function print_button_zone() {

		$settings = [

			// Content tab
			'print_button_text'                               => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'reload' => '1',
			],
			'print_button_info'                               => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'type' => 'raw_html',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> ' . __( "Text is displayed if the enclosing Toolbar has it enabled.", "echo-knowledge-base" ) . '</div>'
			],

			// Style Tab
			'link_to_toolbar_style'                           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'raw_html',
				'toggler'           => 'article_content_enable_rows',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> <a href="#" data-zone="article_content_toolbar" class="epkb-editor-navigation__link">' . __( 'Edit the toolbar style here', 'echo-knowledge-base' ) . '</a></div>'
			],

			// Features tab
			'print_button_enable'             => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'print_button_button_header'                      => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => __( 'Print Settings', 'echo-knowledge-base' ),
			],
			'print_button_doc_padding'                        => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS,
				'label' => __( 'Print Document Padding', 'echo-knowledge-base' ),
				'units' => 'px',
				'subfields' => [
					'print_button_doc_padding_left' => [
						'style_name' => 'padding-left',
						'postfix' => 'px'
					],
					'print_button_doc_padding_top' => [
						'style_name' => 'padding-top',
						'postfix' => 'px'
					],
					'print_button_doc_padding_right' => [
						'style_name' => 'padding-right',
						'postfix' => 'px'
					],
					'print_button_doc_padding_bottom' => [
						'style_name' => 'padding-bottom',
						'postfix' => 'px'
					],
				]
			],
		];

		return [
			'print_button' => [
				'title'     =>  __( 'Print Button', 'echo-knowledge-base' ),
				'classes'   => '.eckb-print-button-container, .eckb-print-button-meta-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'print_button_enable' => 'off'
				]
			]];
	}

	/**
	 * Meta Data FOOTER zone
	 * @return array
	 */
	private static function meta_data_footer_zone() {

		$settings = [

			// setup
			'meta-data-footer-toggle'  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'article_meta_icon_on' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'last_udpated_on_footer_toggle' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'created_on_footer_toggle' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'author_footer_toggle' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

			// text
			'last_udpated_on_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-ach__article-meta__date-updated__text',
				'target_attr' => 'value',
				'text' => '1',
			],
			'created_on_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-ach__article-meta__date-created__text',
				'target_attr' => 'value',
				'text' => '1',
			],
			'author_text' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-ach__article-meta__author__text',
				'target_attr' => 'value',
				'text' => '1',
			],
		];

		return [
			'metadata_footer' => [
				'title'     =>  __( 'Bottom Author and Dates', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-content-footer__article-meta',
				'settings'  => $settings,
				'disabled_settings' => [
					'meta-data-footer-toggle' => 'off'
				]
			]];
	}

	/**
	 * Prev/Next Navigation zone
	 * @return array
	 */
	private static function prev_next_zone() {

		$settings = [

			// setup
			'prev_next_navigation_enable'           => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

			'prev_navigation_text'                  => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-article-navigation__previous a .epkb-article-navigation__label',
				'target_attr' => 'value',
				'text' => '1',
			],
			'next_navigation_text'                  => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-article-navigation__next a .epkb-article-navigation__label',
				'target_attr' => 'value',
				'text' => '1',
			],
			'prev_next_navigation_text_color'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-article-navigation-container a',
				'style_name' => 'color'
			],
			'prev_next_navigation_bg_color'         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-article-navigation-container a',
				'style_name' => 'background-color'
			],
			'prev_next_navigation_hover_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-article-navigation-container a:hover',
				'style_name' => 'color'
			],
			'prev_next_navigation_hover_bg_color'   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-article-navigation-container a:hover',
				'style_name' => 'background-color'
			],
		];

		return [
			'prev_next' => [
				'title'     =>  __( 'Prev/Next Navigation', 'echo-knowledge-base' ),
				'classes'   => '.epkb-article-navigation-container',
				'settings'  => $settings,
				'disabled_settings' => [
					'prev_next_navigation_enable' => 'off'
				]
			]];
	}

	/**
	 * Categories List zone
	 * @return array
	 */
	private static function categories_layout_list_zone() {

		$options = array(
			'0' => __( 'Not displayed', 'echo-knowledge-base' ),
			'1' => '1',
			'2' => '2',
			'3' => '3',
			'4' => '4',
			'5' => '5'
		);

		$settings = [

			// Content Tab

			// Style Tab
			'categories_box_typography' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-cat-layout-list, .eckb-article-cat-layout-list a, body .eckb-acll__cat-item__name',
			],
			'category_box_title_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => ' .eckb-acll__title',
				'style_name' => 'color'
			],
			'category_box_container_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-article-cat-layout-list',
				'style_name' => 'background-color'
			],
			'category_box_category_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__name',
				'style_name' => 'color'
			],
			'category_box_count_background_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__count',
				'style_name' => 'background-color'
			],
			'category_box_count_text_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__count',
				'style_name' => 'color'
			],
			'category_box_count_border_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-acll__cat-item__count',
				'style_name' => 'border-color'
			],

			// Features Tab
			'categories_left' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'label'            => is_rtl() ? __( 'Display on the right', 'echo-knowledge-base' ) : __( 'Display on the left', 'echo-knowledge-base' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'    => $options,
				'default'     => '0',
				'reload' => '1'
			],
			'categories_right' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'label'            => is_rtl() ? __( 'Display on the left', 'echo-knowledge-base' ) : __( 'Display on the right', 'echo-knowledge-base' ),
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'    => $options,
				'default'     => '0',
				'reload' => '1'
			],
			'categories_layout_list_mode' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
		];

		return [
			'categories_list' => [
				'title'     =>  __( 'Categories List', 'echo-knowledge-base' ),
				'classes'   => '.eckb-article-cat-layout-list',
				'settings'  => $settings
			]];
	}

	/**
	 * Retrieve Editor configuration
	 * @param $kb_config
	 * @return array
	 */
	public function get_config( $kb_config ) {

		// Result config
		$editor_config = [];

		// Advanced Search has its own search box settings so exclude the KB core ones
		if ( ! $this->is_asea ) {
			$editor_config += self::article_search_box_zone();
			$editor_config += self::article_search_title_zone();
			$editor_config += self::article_search_input_zone();
			$editor_config += self::article_search_button_zone();
		}
		
		$editor_config += self::page_zone( $kb_config );

		// Left Sidebar
		$editor_config += self::left_sidebar_zone( $kb_config );
		$editor_config += self::categories_layout_list_zone();

		// Article Content
		$editor_config += self::article_content_zone();

		// Right Sidebar
		$editor_config += self::right_sidebar_zone();
		$editor_config += self::toc_zone();

		// Article Content Header
		if ( $kb_config['article_content_enable_rows'] == 'off' ) {
			$editor_config += self::meta_data_header_zone();
		} else {
			$editor_config += self::article_title_zone();
			$editor_config += self::author_zone();
			$editor_config += self::created_date_zone();
			$editor_config += self::last_updated_date_zone();
			$editor_config += self::article_content_toolbar_zone();
		}

		$editor_config += self::print_button_zone();
		$editor_config += self::back_navigation_zone();
		$editor_config += self::breadcrumb_zone();

		// Article Footer
		$editor_config += self::meta_data_footer_zone();
		$editor_config += self::prev_next_zone();
		
		// add values to kb_config for help dialog
		$kb_config += epkb_get_instance()->settings_obj->get_settings_or_default();
		if ( EPKB_Help_Dialog_View::is_help_dialog_enabled() ) {
			$editor_config += EPKB_Editor_Common_Config::help_dialog_zone();
		}

		$unset_settings = [];

		// new users can't switch to old meta data
		$plugin_first_version = EPKB_Utilities::get_wp_option( 'epkb_version_first', '7.0.0' );
		if ( version_compare( $plugin_first_version, '7.1.0', '>=' ) ) {
			echo '<style type="text/css">[data-field="article_content_enable_rows_header"] { 
				  display:none;
				}
				[data-field="article_content_enable_rows"] { 
				  display:none;
				}</style>';
		}

		// only Categories Layout has categories sidebar
		if ( ! $this->is_categories_main_page ) {
			$unset_settings = array_merge($unset_settings, [
				'categories_left',
				'categories_right',
			]);
		}

		if ( ! EPKB_Utilities::is_elegant_layouts_enabled() ) {
			$unset_settings = array_merge($unset_settings, [
				'elay_sidebar_left',
			]);
		}

		// Article Content Toolbar
		// TODO future with more buttons - if all buttons off then disable toolbar
		$kb_config['article_content_toolbar_enable'] = $kb_config['print_button_enable'];

		return self::get_editor_config( $kb_config, $editor_config, $unset_settings, 'article-page' );
	}
}