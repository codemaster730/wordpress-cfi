<?php

/**
 * Lists settings, default values and display of TABS layout.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_KB_Config_Layout_Tabs {

	const LAYOUT_NAME = 'Tabs';
	const CATEGORY_LEVELS = 6;

	/**
	 * Defines KB configuration for this theme.
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => 'false' )
	 *
	 * @return array with both basic and theme-specific configuration
	 */
	public static function get_fields_specification() {

		$config_specification = array(

			/***  KB Main Page STYLE -> Category Tabs ***/
			'tab_typography' => array(
				'label'       => __( 'Typography', 'echo-knowledge-base' ),
				'name'        => 'tab_typography',
				'type'        => EPKB_Input_Filter::TYPOGRAPHY,
				'default'     => array(
					'font-family' => '',
					'font-size' => '14',
					'font-size-units' => 'px',
					'font-weight' => '',
				)
			),
			'tab_down_pointer' => array(
				'label'       => __( 'Down Pointer', 'echo-knowledge-base' ),
				'name'        => 'tab_down_pointer',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),


			/***  KB Main Page COLORS -> Category Tabs  ***/

			'tab_nav_active_font_color' => array(
				'label'       => __( 'Active Text Color', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_active_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#000000'
			),
			'tab_nav_active_background_color' => array(
				'label'       => __( 'Active Background Color', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_active_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#F1F1F1'
			),
			'tab_nav_font_color' => array(
				'label'       => __( 'Text', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_font_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#B3B3B3'
			),
			'tab_nav_background_color' => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#FFFFFF'
			),
			'tab_nav_border_color' => array(
				'label'       => __( 'Border', 'echo-knowledge-base' ),
				'name'        => 'tab_nav_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => '#686868'
			),
		);

		return $config_specification;
	}
}
