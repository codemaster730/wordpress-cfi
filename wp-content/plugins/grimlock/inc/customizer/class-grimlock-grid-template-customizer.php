<?php
/**
 * Grimlock_Grid_Template_Customizer Class
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
 * The base Grimlock Customizer class for archive templates.
 */
abstract class Grimlock_Grid_Template_Customizer extends Grimlock_Template_Customizer {
	/**
	 * Add custom classes to modify layout for #posts div.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_posts_classes( $classes ) {
		if ( $this->is_template() ) {
			$classes[] = "posts--{$this->get_theme_mod( "{$this->id}_posts_layout" )}";

			if ( true == $this->get_theme_mod( "{$this->id}_posts_height_equalized" ) ) {
				$classes[] = 'posts--height-equalized';
			} else {
				$classes[] = 'posts--height-not-equalized';
			}
		}
		return $classes;
	}

	/**
	 * Add a Kirki slider control to set the horizontal offset for the bow shadows.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_box_shadow_x_offset_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'slider',
				'section'  => $this->section,
				'label'    => esc_attr__( 'Shadow Horizontal Offset', 'grimlock' ),
				'settings' => "{$this->id}_post_box_shadow_x_offset",
				'default'  => $this->get_default( "{$this->id}_post_box_shadow_x_offset" ),
				'choices'  => array(
					'min'  => -10,
					'max'  => 10,
					'step' => 1,
				),
				'priority' => 10,
				'output'   => array(
					$this->get_css_var_output( "{$this->id}_post_box_shadow_x_offset", 'px' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_box_shadow_x_offset_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the vertical offset for the bow shadows.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_box_shadow_y_offset_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'slider',
				'section'  => $this->section,
				'label'    => esc_attr__( 'Shadow Vertical Offset', 'grimlock' ),
				'settings' => "{$this->id}_post_box_shadow_y_offset",
				'default'  => $this->get_default( "{$this->id}_post_box_shadow_y_offset" ),
				'choices'  => array(
					'min'  => -10,
					'max'  => 10,
					'step' => 1,
				),
				'priority' => 10,
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_post_box_shadow_y_offset", 'px' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_box_shadow_y_offset_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the blur radius for the bow shadows.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_box_shadow_blur_radius_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'slider',
				'section'  => $this->section,
				'label'    => esc_attr__( 'Shadow Blur Radius', 'grimlock' ),
				'settings' => "{$this->id}_post_box_shadow_blur_radius",
				'default'  => $this->get_default( "{$this->id}_post_box_shadow_blur_radius" ),
				'choices'  => array(
					'min'  => 0,
					'max'  => 30,
					'step' => 1,
				),
				'priority' => 10,
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_post_box_shadow_blur_radius", 'px' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_box_shadow_blur_radius_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the spread radius for the bow shadows.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_box_shadow_spread_radius_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'slider',
				'section'  => $this->section,
				'label'    => esc_attr__( 'Shadow Spread Radius', 'grimlock' ),
				'settings' => "{$this->id}_post_box_shadow_spread_radius",
				'default'  => $this->get_default( "{$this->id}_post_box_shadow_spread_radius" ),
				'choices'  => array(
					'min'  => -10,
					'max'  => 10,
					'step' => 1,
				),
				'priority' => 10,
				'output'    => array(
					$this->get_css_var_output( "{$this->id}_post_box_shadow_spread_radius", 'px' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_box_shadow_spread_radius_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the box shadow color.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_box_shadow_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( "grimlock_{$this->id}_customizer_post_box_shadow_color_elements", $this->elements );
			$outputs  = apply_filters( "grimlock_{$this->id}_customizer_post_box_shadow_color_outputs",  array(
				$this->get_css_var_output( "{$this->id}_post_box_shadow_color" ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => '--post-box-shadow-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'     => 'color',
				'label'    => esc_html__( 'Shadow Color', 'grimlock' ),
				'section'  => $this->section,
				'settings' => "{$this->id}_post_box_shadow_color",
				'default'  => $this->get_default( "{$this->id}_post_box_shadow_color" ),
				'choices'  => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority' => 10,
				'output'   => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_post_box_shadow_color_field_args", $args ) );
		}
	}

	/**
	 * Enqueue scripts for the posts grid.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( $this->is_template() && false == $this->get_theme_mod( "{$this->id}_posts_height_equalized" ) ) {
			wp_enqueue_script( 'jquery-masonry' );
			wp_enqueue_script( 'grimlock-grid', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/grid.js', array( 'jquery', 'jquery-masonry' ), GRIMLOCK_VERSION, true );
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
		$x_offset      = $this->get_theme_mod( "{$this->id}_post_box_shadow_x_offset" );
		$y_offset      = $this->get_theme_mod( "{$this->id}_post_box_shadow_y_offset" );
		$blur_radius   = $this->get_theme_mod( "{$this->id}_post_box_shadow_blur_radius" );
		$spread_radius = $this->get_theme_mod( "{$this->id}_post_box_shadow_spread_radius" );
		$color         = $this->get_theme_mod( "{$this->id}_post_box_shadow_color" );

		$elements = implode( ',', $this->elements );
		$styles   .= "
		{$elements} {
			box-shadow: {$x_offset}px {$y_offset}px {$blur_radius}px {$spread_radius}px {$color};
		}";

		if ( false == $this->get_theme_mod( "{$this->id}_sidebar_mobile_displayed" ) ) {
			$styles .= "
			@media (max-width: 768px) {
				.grimlock--{$this->id} .sidebar {
					display: none;
				}
			}";
		}

		return $styles;
	}
}
