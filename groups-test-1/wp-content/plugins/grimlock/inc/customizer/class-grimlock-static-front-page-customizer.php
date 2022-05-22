<?php
/**
 * Grimlock_Static_Front_Page_Customizer Class
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
 * The Grimlock Customizer style class.
 */
class Grimlock_Static_Front_Page_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'static_front_page';
		add_action( 'after_setup_theme',  array( $this, 'add_customizer_fields' ), 20    );
		add_action( 'customize_register', array( $this, 'customize_register'    ), 20, 1 );
	}

	/**
	 * Add settings and custom controls for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 *
	 * @since 1.0.0
	 */
	public function customize_register( $wp_customize ) {
		// Add the 'Static Front Page' section in the 'Pages' panel.
		$wp_customize->get_section( 'static_front_page' )->panel   = 'grimlock_pages_customizer_panel';
		$wp_customize->get_section( 'background_image' )->priority = 10;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_panel( 'grimlock_pages_customizer_panel', array(
				'priority' => 120,
				'title'    => esc_html__( 'Pages', 'grimlock' ),
			) );
		}
	}
}

return new Grimlock_Static_Front_Page_Customizer();
