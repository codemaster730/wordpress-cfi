<?php
/**
 * Grimlock_bbPress_Customizer Class
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
 * The Grimlock Customizer class for the bbPress pages.
 */
class Grimlock_bbPress_Customizer extends Grimlock_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'archive_forum';
		$this->section = 'grimlock_bbpress_customizer_section';
		$this->title   = esc_html__( 'bbPress', 'grimlock-bbpress' );

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_id',         array( $this, 'change_sidebar_left_id'          ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_id',        array( $this, 'change_sidebar_right_id'         ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_is_template',   array( $this, 'archive_customizer_is_template'  ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',                array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			$args['title']            = $this->get_theme_mod( 'archive_forum_title' );
			$args['subtitle']         = $this->get_theme_mod( 'archive_forum_description' );
			$args['background_image'] = $this->get_theme_mod( 'archive_forum_custom_header_background_image' );
		}
		return $args;
	}

	/**
	 * Check if the current template is a bbpress template.
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = ( function_exists( 'bbp_is_forum_archive' ) && bbp_is_forum_archive() );
		return apply_filters( 'grimlock_bbpress_customizer_is_template', $is_template );
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
		$js_data['tabs'][ $this->section ] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-bbpress' ),
				'class' => 'archive_forum-general-tab',
				'controls' => array(
					'archive_forum_title',
					"{$this->section}_divider_20",
					'archive_forum_description',
					"{$this->section}_divider_30",
					'archive_forum_custom_header_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-bbpress' ),
				'class' => 'archive_forum-layout-tab',
				'controls' => array(
					'archive_forum_custom_header_layout',
					"{$this->section}_divider_110",
					'archive_forum_custom_header_container_layout',
					"{$this->section}_divider_120",
					'archive_forum_layout',
					'archive_forum_sidebar_mobile_displayed',
					"{$this->section}_divider_140",
					'archive_forum_container_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-bbpress' ),
				'class' => 'archive_forum-style-tab',
				'controls' => array(
					'archive_forum_custom_header_background_image',
					"{$this->section}_divider_210",
					'archive_forum_custom_header_padding_y',
					"{$this->section}_divider_220",
					'archive_forum_content_padding_y',
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
		$post_type     = get_post_type_object( 'forum' );
		$archive_title = esc_html__( 'Forums', 'grimlock-bbpress' );

		if ( is_object( $post_type ) && isset( $post_type->label ) && $post_type->label !== '' ) {
			$archive_title = $post_type->label;
		}

		$this->defaults = apply_filters( 'grimlock_bbpress_customizer_defaults', array(
			'archive_forum_title'                          => $archive_title,
			'archive_forum_description'                    => '',
			'archive_forum_custom_header_displayed'        => has_header_image(),

			'archive_forum_custom_header_layout'           => '6-6-cols-left-reverse',
			'archive_forum_custom_header_container_layout' => 'classic',

			'archive_forum_layout'                         => '12-cols-left',
			'archive_forum_sidebar_mobile_displayed'       => true,
			'archive_forum_container_layout'               => 'classic',

			'archive_forum_custom_header_background_image' => get_header_image(),
			'archive_forum_custom_header_padding_y'        => GRIMLOCK_SECTION_PADDING_Y,
			'archive_forum_content_padding_y'              => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section(                              array( 'priority' => 130 ) );

		$this->add_title_field(                          array( 'priority' => 10  ) );
		$this->add_divider_field(                        array( 'priority' => 20  ) );
		$this->add_description_field(                    array( 'priority' => 20  ) );
		$this->add_divider_field(                        array( 'priority' => 30  ) );
		$this->add_custom_header_displayed_field(        array( 'priority' => 30  ) );

		$this->add_custom_header_layout_field(           array( 'priority' => 100 ) );
		$this->add_divider_field(                        array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field( array( 'priority' => 110 ) );
		$this->add_divider_field(                        array( 'priority' => 120 ) );
		$this->add_layout_field(                         array( 'priority' => 120  ) );
		$this->add_sidebar_mobile_displayed_field(       array( 'priority' => 130  ) );
		$this->add_divider_field(                        array( 'priority' => 140  ) );
		$this->add_container_layout_field(               array( 'priority' => 140  ) );

		$this->add_custom_header_background_image_field( array( 'priority' => 200 ) );
		$this->add_divider_field(                        array( 'priority' => 210 ) );
		$this->add_custom_header_padding_y_field(        array( 'priority' => 210 ) );
		$this->add_divider_field(                        array( 'priority' => 220 ) );
		$this->add_content_padding_y_field(              array( 'priority' => 220 ) );

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
				'priority' => isset( $args['priority'] ) ? $args['priority'] : 20,
			) ) );
		}
	}

	/**
	 * Add a Kirki text field to set the title in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_title_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'text',
				'label'             => esc_html__( 'Title', 'grimlock-bbpress' ),
				'section'           => $this->section,
				'settings'          => 'archive_forum_title',
				'default'           => $this->get_default( 'archive_forum_title' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_bbpress_customizer_archive_forum_title_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki textarea field to set the description in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_description_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'textarea',
				'label'             => esc_html__( 'Description', 'grimlock-bbpress' ),
				'section'           => $this->section,
				'settings'          => 'archive_forum_description',
				'default'           => $this->get_default( 'archive_forum_description' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_bbpress_customizer_archive_forum_description_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the Custom Header in the Customizer.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_custom_header_background_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Header Image', 'grimlock-bbpress' ),
				'settings' => 'archive_forum_custom_header_background_image',
				'default'  => $this->get_default( 'archive_forum_custom_header_background_image' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_bbpress_customizer_archive_forum_custom_header_background_image_field_args', $args ) );
		}
	}

	/**
	 * Change the sidebar left id to display a bbPress specific sidebar
	 *
	 * @param string $sidebar_id The id of the sidebar to modify
	 *
	 * @return string The modified id of the sidebar
	 */
	public function change_sidebar_left_id( $sidebar_id ) {
		if ( $this->is_template() ) {
			return 'bbp-sidebar-1';
		}
		return $sidebar_id;
	}

	/**
	 * Change the sidebar right id to display a bbPress specific sidebar
	 *
	 * @param string $sidebar_id The id of the sidebar to modify
	 *
	 * @return string The modified id of the sidebar
	 */
	public function change_sidebar_right_id( $sidebar_id ) {
		if ( $this->is_template() ) {
			return 'bbp-sidebar-2';
		}
		return $sidebar_id;
	}

	/**
	 * Disinherit archive customizer settings
	 *
	 * @param bool $default True if we are on a default archive page
	 *
	 * @return bool
	 */
	public function archive_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}
}

return new Grimlock_bbPress_Customizer();
