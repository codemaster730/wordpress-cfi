<?php

/**
 * Base class for every page configuration
 */
abstract class EPKB_Editor_Base_Config {
	
	/** 
		Options docs 
		
		editor_tab: tab, where will be shown input 
		type: most of the types are in the specs
		content: text for header setting  or html for raw_html
		
		List of the types 
			- color_hex
			- text
			- select
			- checkbox
			- number
			- units ( special view for select )
			
			- header
			- divider
			- notice (in the future)
			- hidden 
			- raw_html
			
		preview: if this parameter exist and have any "true" value, after changing setting iframe will be reloaded

	    target_selector: CSS selector for the style_name option
	    style_name: name of the style that will be changed (for live preview). Value will be used like style value (+postfix)

	    postfix: text that will be added to style's value

	    label: rewrite spec's label
		
		group_type: type of the group control (like type value for usual control)
		subfields: settings that should be shown in group fields 
		units: use this to set units for the dimensions setting. Will NOT be used like postfix
			Grouped array example for dimensions type 

			'search_button_padding' => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'group_type' => self::EDITOR_GROUP_DIMENSIONS
				subfields => [
				]
			],
			
			Grouped array example for multiple type 
			
			'styles', 'target_attr', 'text', 'html' not supported here, this type only for css changes
			
			'advanced_search_mp_title_text_shadow' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'group_type' => self::EDITOR_GROUP_MULTIPLE
				'toggler'           => 'advanced_search_mp_title_text_shadow_toggle',
				'style_template' => 'advanced_search_mp_title_text_shadow_x_offset  advanced_search_mp_title_text_shadow_y_offset advanced_search_mp_title_text_shadow_toggle advanced_search_mp_title_font_shadow_color',
				'target_selector' => '#asea-search-title',
				'style_name' => 'box-shadow',
				'subfields' => [
					'advanced_search_mp_title_font_shadow_color'    => [],
					'advanced_search_mp_title_text_shadow_x_offset' => [],
					'advanced_search_mp_title_text_shadow_y_offset' => [],
					'advanced_search_mp_title_text_shadow_blur'     => [],
				]
			],

		target_attr: attribute that should be changed in elements by target_selector. If need many, use | like separator

		text: any "true" value (1 for example) will change text of the  target_selector's element (NOT html!)

		text_style: parameter for style of the text control, full for 2 rows, inline for 1 row and 2 columns. Default full

		style: for number input can be slider - then number input will have slider under setting , default: default
		for select it can be 'prev-next' - arrows instead dropdown
		style_important: true/false - do we need to add !important to the styles. Default: true
		
		min, max: parameters for number input, standard html attributes
		
		toggler: if the field A have this parameter, then field A will be shown/hidden when field B (toggler) will be on/off. Example: 'toggler' => 'section_divider'. When section_divider will be off, then field will be not shown.
		
		Can be an array: 
		'toggler' => [
			'section_divider' => 'on',
			'section_style' => 'style_1'
		]
		
		then it will work with relation AND 
		
		styles: use it if you need additional styles for the few selectors, example:
			
			'styles' => [
				'#epkb-content-container .epkb-nav-tabs .active:after' => 'border-top-color',
				'#epkb-content-container .epkb-nav-tabs .active' => 'color'
			]
			
			will generate 
			
			#epkb-content-container .epkb-nav-tabs .active:after {
				border-top-color: [value][postfix]!important;
			}
			
			#epkb-content-container .epkb-nav-tabs .active {
				color: [value][postfix]!important;
			}
		
		parent_zone_tab_title: optional parameter in Parent setting that indicates that child zone will shows this "parent" tab with this name (can be any name). JS then automatically finds the parents (NOT using this name)
		
		description: will add description under the field (html)
		
		options: will be used for select, checkboxes, units. 
			- for usual select, checkboxes, units: [ option_slug => 'Option Label' ... ]
			- for optons groups only for select type: 
				[
					'Option Group Label 1' => [
						'grouped_option_slug1' => 'Option Label 1'
						'grouped_option_slug2' => 'Option Label 2'
						...
					],
					'out_of_groups_option_slug' => 'Label'
					...
				]
	*/

	/**
		DIVIDER EXAMPLE ----------------------
		Checked setting: Divider - will show only 1 line between elements, don't need any additional attibutes, only that we have here. Id can be random, but unique

		'search_divider_1' => [
			'editor_tab' => self::EDITOR_TAB_CONTENT,
			'type' => 'divider'
		],

		HEADER EXAMPLE -----------------------
		Checked setting: Text
		text changing content of the target_selector element

		'search_header' => [
			'editor_tab' => self::EDITOR_TAB_CONTENT,
			'type' => 'header',
			'content' => 'Search Header Example'
		],

	 */

	protected $test = '';
	protected $kb_config = array();
	protected $is_asea = false;
	protected $is_elay = false;
	protected $is_basic_main_page = false;
	protected $is_tabs_main_page = false;
	protected $is_categories_main_page = false;
	protected $is_grid_main_page = false;
	protected $is_sidebar_main_page = false;

	// Frontend Editor Tabs
	const EDITOR_TAB_CONTENT = 'content';
	const EDITOR_TAB_STYLE = 'style';
	const EDITOR_TAB_FEATURES = 'features';
	const EDITOR_TAB_ADVANCED = 'advanced';
	const EDITOR_TAB_GLOBAL = 'global';
	const EDITOR_TAB_DISABLED = 'hidden';
		
	const EDITOR_GROUP_DIMENSIONS = 'dimensions';
	const EDITOR_GROUP_MULTIPLE = 'multiple';

	public function __construct( $kb_config ) {
		$this->kb_config = $kb_config;
		
		$this->is_basic_main_page = $kb_config['kb_main_page_layout'] == 'Basic';
		$this->is_tabs_main_page = $kb_config['kb_main_page_layout'] == 'Tabs';
		$this->is_categories_main_page = $kb_config['kb_main_page_layout'] == 'Categories';
		$this->is_grid_main_page = $kb_config['kb_main_page_layout'] == 'Grid';
		$this->is_sidebar_main_page = $kb_config['kb_main_page_layout'] == 'Sidebar';
			
		$this->is_asea = EPKB_Utilities::is_advanced_search_enabled( $kb_config );
		$this->is_elay = EPKB_Utilities::is_elegant_layouts_enabled();
		
		// use basic layout if Elegant Layouts was disabled
		if ( ! $this->is_elay ) {
			
			if ( $this->is_grid_main_page ) {
				$this->is_grid_main_page = false;
				$this->is_basic_main_page = true;
			}
			
			if ( $this->is_sidebar_main_page ) {
				$this->is_sidebar_main_page = false;
				$this->is_basic_main_page = true;
			}
		}
		
	}

	/**
	 * Retrieve Editor configuration
	 * @param $kb_config
	 * @param $editor_config
	 * @param array $unset_settings
	 * @param string $page_type
	 * @return array
	 */
	public static function get_editor_config( $kb_config, $editor_config, $unset_settings = [], $page_type = '' ) {
		
		// get all specs
		$field_specification = EPKB_KB_Config_Specs::get_fields_specification( $kb_config['id'] );

		// get Plugin settings specs
		$plugin_field_specification = EPKB_Settings_Specs::get_fields_specification();

		$field_specification = array_merge($plugin_field_specification, $field_specification);

		// specs for add-on fields
		$field_specification = apply_filters( 'eckb_editor_fields_specs', $field_specification, $kb_config['id'] );
		
		// unset not used settings 
		$editor_config = self::unset_settings( $unset_settings, $editor_config );
		
		// configuration for add-on fields
		$editor_config = apply_filters( 'eckb_editor_fields_config', $editor_config, $kb_config, $page_type );
		
		foreach ( $editor_config as $zone => $zone_data ) {

			foreach( $zone_data['settings'] as $field_name => $field_data ) {

				// handle special types without inputs 
				if ( ! empty( $field_data['type'] ) && in_array( $field_data['type'], ['header','divider','notice','header_desc', 'preset', 'raw_html'] ) || ( ! isset( $field_specification[$field_name] ) && empty( $field_data['group_type'] ) ) && ! self::is_sidebar_priority( $field_name ) ) {
					continue;
				}
				
				// handle regular control
				if ( empty( $field_data['group_type'] ) ) {

					// sidebar priority is handled differently
					if ( self::is_sidebar_priority( $field_name ) ) {

						// add current value
						if ( isset( $kb_config['article_sidebar_component_priority'][$field_name] ) ) {
							$editor_config[$zone]['settings'][$field_name]['value'] = $kb_config['article_sidebar_component_priority'][$field_name];
						} else {
							$editor_config[$zone]['settings'][$field_name]['value'] = $field_data['default'];
						}

					} else {

						$editor_config[$zone]['settings'][$field_name] += $field_specification[$field_name];
						
						// add current value
						if ( isset( $kb_config[$field_name] ) ) {
							$editor_config[$zone]['settings'][$field_name]['value'] = $kb_config[$field_name];
						} else {
							$editor_config[$zone]['settings'][$field_name]['value'] = $editor_config[$zone]['settings'][$field_name]['default'];
						}

					}

				// handle group control
				} else {

					foreach ( $field_data['subfields'] as $subfield_name => $subfield_data ) {
						
						$editor_config[$zone]['settings'][$field_name]['subfields'][$subfield_name] += $field_specification[$subfield_name];
						
						// add current value
						if ( isset( $kb_config[$subfield_name] ) ) {
							$editor_config[$zone]['settings'][$field_name]['subfields'][$subfield_name]['value'] = $kb_config[$subfield_name];
						} else {
							$editor_config[$zone]['settings'][$field_name]['subfields'][$subfield_name]['value'] = $editor_config[$zone]['settings'][$field_name]['subfields'][$subfield_name]['default'];
						}
					}
				}
			}
		}
		
		return $editor_config;
	}

	public static function is_sidebar_priority( $field_name ) {
		return in_array( $field_name, ['elay_sidebar_left', 'toc_left', 'kb_sidebar_left', 'categories_left', 'toc_content', 'toc_right', 'kb_sidebar_right', 'categories_right'] );
	}

	/**
	 * Unset Settings that should not be shown if certain conditions are true
	 * @param $unset_settings
	 * @param $editor_config
	 * @return mixed
	 */
	public static function unset_settings( $unset_settings, $editor_config ) {
		foreach( $unset_settings as $field_name ) {
			foreach ( $editor_config as $zone => $data ) {
				if ( isset( $editor_config[$zone]['settings'][$field_name] ) ) {
					unset( $editor_config[$zone]['settings'][$field_name] );
				}
			}
		}
		return $editor_config;
	}

	/**
	 * Retrieve Editor configuration for Settings panel
	 * @param $kb_config
	 * @return array
	 */
	public function get_editor_panel_config( $kb_config ) {
		
		$global_settings = [
			'wpml_is_enabled' => [
				'editor_tab' => self::EDITOR_TAB_GLOBAL,
				'reload' => 1,
			],
			'wpml_info'                               => [
				'editor_tab' => self::EDITOR_TAB_GLOBAL,
				'type' => 'raw_html',
				'content' => '<div class="epkb-editor__info"><span class="epkbfa epkbfa-info-circle"></span> ' . __('Follow WPML setup instructions here.', 'echo-knowledge-base') . ' <a href="https://www.echoknowledgebase.com/documentation/setup-wpml-for-knowledge-base/" target="_blank"><span class="epkbfa epkbfa-external-link"></span></a></div>'
			],
			'kb_main_page_layout' => [
				'type' => 'none',
				'reload' => 1,
			],
			'templates_for_kb' => [
				'type' => 'none',
				'reload' => 1,
			],
		];
		
		$editor_panel_config = [
			'settings_zone' => [
				'settings' => $global_settings
			],
		];
		
		return self::get_editor_config( $this->kb_config, $editor_panel_config, [], 'settings' );
	}
}