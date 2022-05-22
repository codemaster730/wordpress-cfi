<?php

/**
 * Lists all FAQ configuration settings and adds filter to get configuration from add-ons.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_FAQ_Config_Specs {

	private static $cached_specs = array();

	/**
	 * Defines how FAQ configuration fields will be displayed, initialized and validated/sanitized
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @param int $faq_id is the ID of FAQ base to get default config for
	 * @return array with FAQ config specification
	 */
	public static function get_fields_specification( $faq_id ) {

		// if faq_id is invalid use default FAQ
		if ( ! EPKB_Utilities::is_positive_int( $faq_id ) ) {
			EPKB_Logging::add_log( 'setting faq_id to 0 because faq_id is not positive int', $faq_id );
			$faq_id = EPKB_FAQ_Config_DB::DEFAULT_FAQ_SHORTCODE_ID;
		}

		// retrieve settings if already cached
		if ( ! empty(self::$cached_specs[$faq_id]) && is_array(self::$cached_specs[$faq_id]) ) {
			return self::$cached_specs[$faq_id];
		}

		// all CORE settings are listed here; 'name' used for HTML elements
		$config_specification = array(

			/******************************************************************************
			 *
			 *  Internal settings
			 *
			 ******************************************************************************/
			'id' => array(
				'label'       => 'faq_id',
				'type'        => EPKB_Input_Filter::ID,
				'internal'    => true,
				'default'     => $faq_id
			),
			'status' => array(
				'label'       => 'status',
				'type'        => EPKB_Input_Filter::ENUMERATION,
				'options'     => array( EPKB_FAQ_Status::DRAFT, EPKB_FAQ_Status::PUBLISHED, EPKB_FAQ_Status::ARCHIVED ),
				'internal'    => true,
				'default'     => EPKB_FAQ_Status::PUBLISHED
			),

			/******************************************************************************
			 *
			 *  Overview
			 *
			 ******************************************************************************/
			'faq_main_page_layout' => array(
				'label'       => __( 'Main Page Layout', 'echo-knowledge-base' ),
				'name'        => 'faq_main_page_layout',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => 'TODO',
				'default'     => 'TODO',
			),
		);

		// add CORE LAYOUTS SHARED configuration
		$config_specification = array_merge( $config_specification, self::shared_configuration() );

		// add CORE LAYOUTS non-shared configuration
		// TODO $config_specification = array_merge( $config_specification, EPKB_FAQ_Config_Layout_Basic::get_fields_specification() );

		self::$cached_specs[$faq_id] = empty($config_specification_temp) || count($config_specification) > count($config_specification_temp)
										? $config_specification : $config_specification_temp;

		return self::$cached_specs[$faq_id];
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

			/***  General ***/
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
					'font-size' => '14'
				)
			),

			/***  Categories ***/
			'section_head_alignment' => array(
				'label'       => __( 'Text Alignment', 'echo-knowledge-base' ),
				'name'        => 'section_head_alignment',
				'type'        => EPKB_Input_Filter::SELECTION,
				'style'       => 'small',
				'options'     => array(
					'left' => is_rtl() ? __( 'Start', 'echo-knowledge-base' ) : __( 'Left', 'echo-knowledge-base' ),
					'center' => __( 'Centered', 'echo-knowledge-base' ),
					'right' => is_rtl() ? __( 'End', 'echo-knowledge-base' ) : __( 'Right', 'echo-knowledge-base' )
				),
				'default'     => 'left'
			),
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

			/***  Advanced  ***/
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

			/***   Articles Listed in Sub-Category ***/
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
				'default'     => array()
			),
			'section_head_description_typography' => array(
				'label'       => __( 'Description Typography', 'echo-knowledge-base' ),
				'name'        => 'section_head_description_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array()
			),
		);

		return $shared_specification;
	}

	/**
	 * Get FAQ default configuration
	 * @param $faq_shortcode_id
	 * @return array contains default values for FAQ configuration
	 */
	public static function get_default_faq_config( $faq_shortcode_id ) {
		$config_specs = self::get_fields_specification( $faq_shortcode_id );

		$default_configuration = array();
		foreach( $config_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}

	/**
	 * Get names of all configuration items for FAQ configuration
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification( EPKB_FAQ_Config_DB::DEFAULT_FAQ_SHORTCODE_ID ) );
	}
}

abstract class EPKB_FAQ_Status
{
	const DRAFT = 'draft';
	const PUBLISHED = 'published';
	const ARCHIVED = 'archived';
}
