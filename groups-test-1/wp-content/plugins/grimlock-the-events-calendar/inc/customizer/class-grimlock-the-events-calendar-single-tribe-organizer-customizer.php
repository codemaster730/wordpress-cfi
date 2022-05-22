<?php
/**
 * Grimlock_The_Events_Calendar_Single_Tribe_Organizer_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the The Events Calendar single tribe_organizer pages.
 */
class Grimlock_The_Events_Calendar_Single_Tribe_Organizer_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'single_tribe_organizer';
		$this->section = 'grimlock_the_events_calendar_single_tribe_organizer_customizer_section';
		$this->title   = esc_html__( 'Single Organizer', 'grimlock-the-events-calendar' );

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_single_customizer_is_template',    array( $this, 'single_customizer_is_template'   ), 10, 1 );
	}

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_body_classes( $classes ) {
		$classes = parent::add_body_classes( $classes );

		if ( $this->is_template() ) {
			$displayed_theme_mods = array(
				'single_tribe_organizer_breadcrumb_custom_header_displayed',

				'single_tribe_organizer_thumbnail_displayed',
				'single_tribe_organizer_phone_displayed',
				'single_tribe_organizer_website_displayed',
				'single_tribe_organizer_email_displayed',
				'single_tribe_organizer_breadcrumb_displayed',
			);

			foreach ( $displayed_theme_mods as $theme_mod ) {
				if ( ! empty( $this->get_theme_mod( $theme_mod ) ) ) {
					$classes[] = "grimlock-the-events-calendar--{$theme_mod}";
				}
			}
		}

		return $classes;
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-the-events-calendar' ),
				'class' => 'single_tribe_organizer-general-tab',
				'controls' => array(
					"{$this->section}_heading_10",
					'single_tribe_organizer_custom_header_displayed',
					'single_tribe_organizer_phone_custom_header_displayed',
					'single_tribe_organizer_website_custom_header_displayed',
					'single_tribe_organizer_email_custom_header_displayed',
					'single_tribe_organizer_breadcrumb_custom_header_displayed',

					"{$this->section}_divider_100",
					"{$this->section}_heading_100",
					'single_tribe_organizer_thumbnail_displayed',
					'single_tribe_organizer_phone_displayed',
					'single_tribe_organizer_website_displayed',
					'single_tribe_organizer_email_displayed',
					'single_tribe_organizer_breadcrumb_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-the-events-calendar' ),
				'class' => 'single_tribe_organizer-layout-tab',
				'controls' => array(
					'single_tribe_organizer_custom_header_layout',
					"{$this->section}_divider_110",
					'single_tribe_organizer_custom_header_container_layout',
					"{$this->section}_divider_120",
					'single_tribe_organizer_layout',
					'single_tribe_organizer_sidebar_mobile_displayed',
					"{$this->section}_divider_140",
					'single_tribe_organizer_container_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-the-events-calendar' ),
				'class' => 'single_tribe_organizer-style-tab',
				'controls' => array(
					'single_tribe_organizer_custom_header_padding_y',
					"{$this->section}_divider_210",
					'single_tribe_organizer_content_padding_y',
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
		$this->defaults = apply_filters( 'grimlock_the_events_calendar_single_tribe_organizer_customizer_defaults', array(
			'single_tribe_organizer_custom_header_displayed'            => has_header_image(),
			'single_tribe_organizer_phone_custom_header_displayed'      => false,
			'single_tribe_organizer_website_custom_header_displayed'    => false,
			'single_tribe_organizer_email_custom_header_displayed'      => false,
			'single_tribe_organizer_breadcrumb_custom_header_displayed' => true,

			'single_tribe_organizer_thumbnail_displayed'                => true,
			'single_tribe_organizer_phone_displayed'                    => true,
			'single_tribe_organizer_website_displayed'                  => true,
			'single_tribe_organizer_email_displayed'                    => true,
			'single_tribe_organizer_breadcrumb_displayed'               => true,

			'single_tribe_organizer_custom_header_layout'               => '12-cols-center',
			'single_tribe_organizer_custom_header_container_layout'     => 'classic',
			'single_tribe_organizer_layout'                             => '12-cols-left',
			'single_tribe_organizer_sidebar_mobile_displayed'           => true,
			'single_tribe_organizer_container_layout'                   => 'classic',

			'single_tribe_organizer_custom_header_padding_y'            => GRIMLOCK_SECTION_PADDING_Y,
			'single_tribe_organizer_content_padding_y'                  => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		if ( class_exists( 'Tribe__Events__Pro__Main' ) ) {
			$this->add_section();
		}

		$this->add_heading_field(                            array( 'priority' => 10, 'label' => esc_html__( 'Header Display', 'grimlock' ) ) );
		$this->add_custom_header_displayed_field(            array( 'priority' => 20 ) );
		$this->add_phone_custom_header_displayed_field(      array( 'priority' => 30 ) );
		$this->add_website_custom_header_displayed_field(    array( 'priority' => 40 ) );
		$this->add_email_custom_header_displayed_field(      array( 'priority' => 50 ) );
		$this->add_breadcrumb_custom_header_displayed_field( array( 'priority' => 60 ) );

		$this->add_divider_field(                            array( 'priority' => 100 ) );
		$this->add_heading_field(                            array( 'priority' => 100, 'label' => esc_html__( 'Content Display', 'grimlock' ) ) );
		$this->add_thumbnail_displayed_field(                array( 'priority' => 110 ) );
		$this->add_phone_displayed_field(                    array( 'priority' => 120 ) );
		$this->add_website_displayed_field(                  array( 'priority' => 130 ) );
		$this->add_email_displayed_field(                    array( 'priority' => 140 ) );
		$this->add_breadcrumb_displayed_field(               array( 'priority' => 150 ) );

		$this->add_custom_header_layout_field(               array( 'priority' => 100 ) );
		$this->add_divider_field(                            array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field(     array( 'priority' => 110 ) );
		$this->add_divider_field(                            array( 'priority' => 120 ) );
		$this->add_layout_field(                             array( 'priority' => 120 ) );
		$this->add_sidebar_mobile_displayed_field(           array( 'priority' => 130 ) );
		$this->add_divider_field(                            array( 'priority' => 140 ) );
		$this->add_container_layout_field(                   array( 'priority' => 140 ) );

		$this->add_custom_header_padding_y_field(            array( 'priority' => 200 ) );
		$this->add_divider_field(                            array( 'priority' => 210 ) );
		$this->add_content_padding_y_field(                  array( 'priority' => 210 ) );
	}

	/**
	 * Add a Kirki checkbox to set the phone display in the custom header.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_phone_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display phone', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_phone_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_phone_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_phone_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the website display in the custom header.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_website_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display website', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_website_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_website_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_website_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the email display in the custom header.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_email_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display email', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_email_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_email_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_email_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the breadcrumb display in the custom header.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_breadcrumb_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display breadcrumb', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_breadcrumb_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_breadcrumb_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_breadcrumb_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the thumbnail display in the content.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_thumbnail_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display featured image', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_thumbnail_displayed",
				'default'  => $this->get_default( "{$this->id}_thumbnail_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_thumbnail_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the phone display in the content.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_phone_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display phone', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_phone_displayed",
				'default'  => $this->get_default( "{$this->id}_phone_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_phone_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the website display in the content.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_website_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display website', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_website_displayed",
				'default'  => $this->get_default( "{$this->id}_website_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_website_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the email display in the content.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_email_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display email', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_email_displayed",
				'default'  => $this->get_default( "{$this->id}_email_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_email_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the breadcrumb display in the content.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_breadcrumb_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display breadcrumb', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_breadcrumb_displayed",
				'default'  => $this->get_default( "{$this->id}_breadcrumb_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_breadcrumb_displayed_field_args", $args ) );
		}
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
				'priority' => 30,
				'panel'    => 'grimlock_the_events_calendar_tribe_events_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			$args['background_image']                         = apply_filters( 'grimlock_the_events_calendar_custom_header_background_image', '' );
			$args['single_post_back_displayed']               = $this->get_theme_mod( 'single_tribe_organizer_breadcrumb_custom_header_displayed' );
			$args['single_tribe_organizer_phone_displayed']   = $this->get_theme_mod( 'single_tribe_organizer_phone_custom_header_displayed' );
			$args['single_tribe_organizer_email_displayed']   = $this->get_theme_mod( 'single_tribe_organizer_email_custom_header_displayed' );
			$args['single_tribe_organizer_website_displayed'] = $this->get_theme_mod( 'single_tribe_organizer_website_custom_header_displayed' );
		}

		return $args;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.1.5
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	public function is_template() {
		$is_template = is_singular( 'tribe_organizer' );
		return apply_filters( 'grimlock_the_events_calendar_single_tribe_organizer_customizer_is_template', $is_template );
	}

	/**
	 * Disinherit single customizer settings
	 *
	 * @param bool $default True if we are on a default single page
	 *
	 * @return bool
	 */
	public function single_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}
}

return new Grimlock_The_Events_Calendar_Single_Tribe_Organizer_Customizer();
