<?php
/**
 * Cera_Grimlock_Dashboard_Customizer Class
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
 * The Grimlock Customizer class for the single pages.
 */
class Cera_Grimlock_Dashboard_Customizer extends Grimlock_Base_Customizer {
	public $id;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'dashboard';
		$this->section = 'cera_grimlock_dashboard_customizer_section';
		$this->title   = esc_html__( 'Dashboard', 'cera' );

		add_action( 'after_setup_theme', array( $this, 'add_customizer_fields'           ), 20    );
		add_filter( 'body_class',        array( $this, 'add_content_classes'             ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'cera_grimlock_dashboard_customizer_defaults', array(
			'dashboard_layout'                    => '3-3-3-3-cols-left',
			'dashboard_widgets_height_equalized'  => false,
		) );

		$this->add_section(                        array( 'priority' => 120 ) );

		$this->add_heading_field(                  array( 'priority' => 10, 'description' => esc_html__( 'These settings will affect pages that are using the "Dashboard" page template.', 'cera' ) ) );
		$this->add_layout_field(                   array( 'priority' => 100 ) );
		$this->add_widgets_height_equalized_field( array( 'priority' => 110 ) );
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => 10,
				'panel'    => 'grimlock_pages_customizer_panel',
			) ) );
		}
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		return apply_filters( 'cera_grimlock_dashboard_customizer_is_template', is_page_template( 'template-dashboard.php' ) );
	}

	/**
	 * Add a Kirki radio-image field to set the template content layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'radio-image',
				'section'     => $this->section,
				'label'       => esc_html__( 'Layout', 'cera' ),
				'description' => esc_html__( 'Note that the columns will still collapse as the screen size gets smaller.', 'cera' ),
				'settings'    => "{$this->id}_layout",
				'default'     => $this->get_default( "{$this->id}_layout" ),
				'priority'    => 10,
				'choices'     => array(
					'3-3-3-3-cols-left'            => get_stylesheet_directory_uri() . '/assets/images/customizer/dashboard-3-3-3-3-cols-left.png',
					'4-4-4-cols-left'              => get_stylesheet_directory_uri() . '/assets/images/customizer/dashboard-4-4-4-cols-left.png',
					'6-6-cols-left'                => get_stylesheet_directory_uri() . '/assets/images/customizer/dashboard-6-6-cols-left.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "cera_grimlock_{$this->id}_customizer_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set whether to use masonry in dashboard pages or not
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_widgets_height_equalized_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Equalize widgets heights', 'cera' ),
				'settings' => "{$this->id}_widgets_height_equalized",
				'default'  => $this->get_default( "{$this->id}_widgets_height_equalized" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "cera_grimlock_{$this->id}_customizer_widgets_height_equalized_field_args", $args ) );
		}
	}

	/**
	 * Add custom classes to content to modify layout.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_content_classes( $classes ) {
		if ( $this->is_template() ) {
			$classes[] = "dashboard--{$this->get_theme_mod( "{$this->id}_layout")}";

			if ( $this->get_theme_mod( 'dashboard_widgets_height_equalized' ) ) {
				$classes[] = 'dashboard--items-height-equalized';
			}
		}
		return $classes;
	}
}

return new Cera_Grimlock_Dashboard_Customizer();
