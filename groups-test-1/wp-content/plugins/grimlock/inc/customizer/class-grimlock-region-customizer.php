<?php
/**
 * Grimlock_Region_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer region class.
 */
abstract class Grimlock_Region_Customizer extends Grimlock_Base_Customizer {
	/**
	 * @var string $id The ID for the group of features in the Customizer.
	 * @since 1.0.0
	 */
	protected $id;

	/**
	 * Add arguments using theme mods to customize the region component.
	 *
	 * @param array $args The default arguments to render the region.
	 *
	 * @return array      The arguments to render the region.
	 */
	public function add_args( $args ) {
		$args['layout']              = $this->get_theme_mod( "{$this->id}_layout" );
		$args['container_layout']    = $this->get_theme_mod( "{$this->id}_container_layout" );

		$args['padding_top']         = $this->get_theme_mod( "{$this->id}_padding_y" );
		$args['padding_bottom']      = $this->get_theme_mod( "{$this->id}_padding_y" );

		$args['background_image']    = $this->get_theme_mod( "{$this->id}_background_image" );
		$args['background_color']    = $this->get_theme_mod( "{$this->id}_background_color" );

		$args['border_top_color']    = $this->get_theme_mod( "{$this->id}_border_top_color" );
		$args['border_top_width']    = $this->get_theme_mod( "{$this->id}_border_top_width" );
		$args['border_bottom_color'] = $this->get_theme_mod( "{$this->id}_border_bottom_color" );
		$args['border_bottom_width'] = $this->get_theme_mod( "{$this->id}_border_bottom_width" );

		$args['color']               = $this->get_theme_mod( "{$this->id}_color" );

		return $args;
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label'    => esc_html__( 'Layout', 'grimlock' ),
				'class'    => "{$this->id}-layout-tab",
				'controls' => array(
					"{$this->id}_layout",
					"{$this->section}_divider_20",
					"{$this->id}_container_layout",
					"{$this->section}_divider_30",
					"{$this->section}_heading_30",
					"{$this->id}_mobile_displayed",
				),
			),
			array(
				'label'    => esc_html__( 'Style', 'grimlock' ),
				'class'    => "{$this->id}-style-tab",
				'controls' => array(
					"{$this->id}_background_image",
					"{$this->section}_divider_110",
					"{$this->id}_padding_y",
					"{$this->section}_divider_120",
					"{$this->id}_background_color",
					"{$this->section}_divider_130",
					"{$this->id}_border_top_color",
					"{$this->id}_border_top_width",
					"{$this->section}_divider_150",
					"{$this->id}_border_bottom_color",
					"{$this->id}_border_bottom_width",
					"{$this->section}_divider_170",
					"{$this->id}_heading_color",
					"{$this->id}_color",
					"{$this->id}_link_color",
					"{$this->id}_link_hover_color",
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'panel'    => 'grimlock_appearance_customizer_panel',
				'title'    => $this->title,
			) );

			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the component display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display in pages', 'grimlock' ),
				'settings' => "{$this->id}_displayed",
				'default'  => $this->get_default( "{$this->id}_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the top border width in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_top_width_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_border_top_width_elements", array(
				".grimlock-{$this->id}",
			) );

			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_border_top_width_outputs", array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-top-width',
					'units'    => 'px',
					'suffix'   => '!important'
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Top Width', 'grimlock' ),
				'settings'  => "{$this->id}_border_top_width",
				'default'   => $this->get_default( "{$this->id}_border_top_width" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 25,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_border_top_width", 'px' ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_border_top_width_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the top border color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_top_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_border_top_color_elements", array(
				".grimlock-{$this->id}",
			) );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_border_top_color_outputs", array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-top-color',
					'suffix'   => '!important'
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Top Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_border_top_color",
				'default'   => $this->get_default( "{$this->id}_border_top_color" ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_border_top_color" ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_border_top_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the bottom border color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_bottom_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_border_bottom_color_elements", array(
				".grimlock-{$this->id}",
			) );

			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_border_bottom_color_outputs", array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-bottom-color',
					'suffix'   => '!important'
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Bottom Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_border_bottom_color",
				'default'   => $this->get_default( "{$this->id}_border_bottom_color" ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_border_bottom_color" ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_border_bottom_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the bottom border width in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_bottom_width_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_border_bottom_width_elements", array(
				".grimlock-{$this->id}",
			) );

			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_border_bottom_width_outputs", array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-bottom-width',
					'units'    => 'px',
					'suffix'   => '!important'
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Bottom Width', 'grimlock' ),
				'settings'  => "{$this->id}_border_bottom_width",
				'default'   => $this->get_default( "{$this->id}_border_bottom_width" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 25,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_border_bottom_width", 'px' ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_border_bottom_width_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_color_elements", array(
				".grimlock-{$this->id}",
			) );

			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_color_outputs", array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_color",
				'default'   => $this->get_default( "{$this->id}_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_color" ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the link hover color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_link_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_link_color_elements", array(
				".grimlock-{$this->id} a:not(.btn):not(.button)",
			) );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_link_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_link_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_link_color",
				'default'   => $this->get_default( "{$this->id}_link_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_link_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the link hover color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_link_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_link_hover_color_elements", array(
				".grimlock-{$this->id} a:not(.btn):not(.button):hover",
				".grimlock-{$this->id} a:not(.btn):not(.button):focus",
			) );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_link_hover_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_link_hover_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_link_hover_color",
				'default'   => $this->get_default( "{$this->id}_link_hover_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_link_hover_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Layout', 'grimlock' ),
				'settings' => "{$this->id}_layout",
				'default'  => $this->get_default( "{$this->id}_layout" ),
				'priority' => 10,
				'choices'  => array(
					'3-3-3-3-cols-left'            => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-3-3-3-3-cols-left.png',
					'3-3-3-3-cols-center'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-3-3-3-3-cols-center.png',
					'4-4-4-cols-center'            => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-4-4-4-cols-center.png',
					'4-4-4-cols-left-center-right' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-4-4-4-cols-left-center-right.png',
					'4-4-4-cols-left'              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-4-4-4-cols-left.png',
					'4-8-cols-left-right'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-4-8-cols-left-right.png',
					'4-8-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-4-8-cols-left.png',
					'6-6-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-6-6-cols-left.png',
					'6-6-cols-center'              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-6-6-cols-center.png',
					'6-6-cols-right'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-6-6-cols-right.png',
					'6-6-cols-left-right'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-6-6-cols-left-right.png',
					'8-4-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-8-4-cols-left.png',
					'8-4-cols-left-right'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-8-4-cols-right.png',
					'12-cols-left'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-12-cols-left.png',
					'12-cols-center'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-12-cols-center.png',
					'12-cols-right'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-12-cols-right.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout for the region container in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_container_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Spread', 'grimlock' ),
				'settings' => "{$this->id}_container_layout",
				'default'  => $this->get_default( "{$this->id}_container_layout" ),
				'priority' => 10,
				'choices'  => array(
					'fluid'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-fluid.png',
					'classic'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-classic.png',
					'narrow'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrow.png',
					'narrower' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrower.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_container_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the region in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_heading_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_heading_color_elements", array(
				".grimlock-{$this->id} h1",
				".grimlock-{$this->id} h2",
				".grimlock-{$this->id} h3",
				".grimlock-{$this->id} h4",
				".grimlock-{$this->id} h5",
				".grimlock-{$this->id} h6",
				".grimlock-{$this->id} .h1",
				".grimlock-{$this->id} .h2",
				".grimlock-{$this->id} .h3",
				".grimlock-{$this->id} .h4",
				".grimlock-{$this->id} .h5",
				".grimlock-{$this->id} .h6",
				".grimlock-{$this->id} .display-1",
				".grimlock-{$this->id} .display-2",
				".grimlock-{$this->id} .display-3",
				".grimlock-{$this->id} .display-4",
				".grimlock-{$this->id} .widget-title",
			) );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_heading_color_outputs", array(
				$this->get_css_var_output( "{$this->id}_heading_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Headings Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_heading_color",
				'default'   => $this->get_default( "{$this->id}_heading_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_heading_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the region in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Background Image', 'grimlock' ),
				'settings' => "{$this->id}_background_image",
				'default'  => $this->get_default( "{$this->id}_background_image" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_background_image_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the component display for mobile in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_mobile_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display in mobile pages', 'grimlock' ),
				'settings' => "{$this->id}_mobile_displayed",
				'default'  => $this->get_default( "{$this->id}_mobile_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_mobile_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_background_color_elements", array(
				".grimlock-{$this->id} > .region__inner",
			) );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_background_color_outputs", array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => "{$this->id}_background_color",
				'default'   => $this->get_default( "{$this->id}_background_color" ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_background_color" ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_background_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the vertical padding in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_padding_y_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_padding_y_elements", array(
				".grimlock-{$this->id} > .region__inner",
			) );

			$outputs = apply_filters( "grimlock_{$this->id}_customizer_padding_y_outputs", array(
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'padding-top',
					'value_pattern' => '$% !important',
				),
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'padding-bottom',
					'value_pattern' => '$% !important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Vertical Padding', 'grimlock' ),
				'settings'  => "{$this->id}_padding_y",
				'default'   => $this->get_default( "{$this->id}_padding_y" ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 25,
					'step'  => .25,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_padding_y", '%' ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_padding_y_field_args", $args ) );
		}
	}

	/**
	 * Add custom styles based on theme mods.
	 *
	 * @param string $styles The styles printed by Kirki
	 *
	 * @since 1.0.0
	 */
	public function add_dynamic_css( $styles ) {
		if ( false == $this->get_theme_mod( "{$this->id}_mobile_displayed" ) ) {
			$styles .= "
			@media (max-width: 768px) {
				.grimlock-{$this->id} {
					display: none;
				}
			}";
		}

		return $styles;
	}
}
