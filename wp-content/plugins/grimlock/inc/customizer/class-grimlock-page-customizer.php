<?php
/**
 * Grimlock_Page_Customizer Class
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
class Grimlock_Page_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'page';
		$this->section = 'grimlock_page_customizer_section';
		$this->title   = esc_html__( 'Single Page', 'grimlock' );

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                           array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',          array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',     array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_page_args',                   array( $this, 'add_page_args'                   ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',           array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Add arguments using theme mods to customize the post.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the post.
	 *
	 * @return array      The arguments to render the post.
	 */
	public function add_page_args( $args ) {
		$args['post_thumbnail_displayed'] = false;
		return $args;
 	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock' ),
				'class' => 'page-general-tab',
				'controls' => array(
					"{$this->section}_heading_10",
					'page_custom_header_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock' ),
				'class' => 'page-layout-tab',
				'controls' => array(
					'page_custom_header_layout',
					"{$this->section}_divider_110",
					'page_custom_header_container_layout',
					"{$this->section}_divider_120",
					"{$this->section}_heading_120",
					'page_sidebar_mobile_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock' ),
				'class' => 'page-style-tab',
				'controls' => array(
					'page_custom_header_padding_y',
					"{$this->section}_divider_210",
					'page_content_padding_y',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_page_customizer_defaults', array(
			'page_custom_header_displayed'        => has_header_image(),

			'page_custom_header_layout'           => '12-cols-center',
			'page_custom_header_container_layout' => 'classic',

			'page_sidebar_mobile_displayed'       => true,

			'page_custom_header_padding_y'        => GRIMLOCK_SECTION_PADDING_Y,
			'page_content_padding_y'              => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section(                              array( 'priority' => 120 ) );

		$this->add_heading_field(                        array( 'priority' => 10, 'label' => esc_html__( 'Display', 'grimlock' ) ) );
		$this->add_custom_header_displayed_field(        array( 'priority' => 10  ) );

		$this->add_custom_header_layout_field(           array( 'priority' => 100 ) );
		$this->add_divider_field(                        array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field( array( 'priority' => 110 ) );
		$this->add_divider_field(                        array( 'priority' => 120 ) );
		$this->add_heading_field(                        array( 'priority' => 120, 'label' => esc_html__( 'Sidebars', 'grimlock' ) ) );
		$this->add_sidebar_mobile_displayed_field(       array( 'priority' => 120 ) );

		$this->add_custom_header_padding_y_field(        array( 'priority' => 200 ) );
		$this->add_divider_field(                        array( 'priority' => 210 ) );
		$this->add_content_padding_y_field(              array( 'priority' => 210 ) );
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
			Kirki::add_panel( 'grimlock_pages_customizer_panel', array(
				'priority' => $args['priority'],
				'title'    => esc_html__( 'Pages', 'grimlock' ),
			) );

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
		return apply_filters( 'grimlock_page_customizer_is_template', is_page() );
	}
}

return new Grimlock_Page_Customizer();
