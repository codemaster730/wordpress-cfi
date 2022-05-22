<?php
/**
 * Grimlock_The_Events_Calendar_Single_Tribe_Events_Customizer Class
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
 * The Grimlock Customizer class for the The Events Calendar single tribe_events pages.
 */
class Grimlock_The_Events_Calendar_Single_Tribe_Events_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'single_tribe_events';
		$this->section = 'grimlock_the_events_calendar_single_tribe_events_customizer_section';
		$this->title   = esc_html__( 'Single Event', 'grimlock-the-events-calendar' );

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 30, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 20, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_single_customizer_is_template',    array( $this, 'single_customizer_is_template'   ), 10, 1 );
		add_filter( 'tribe_events_get_event_link',               array( $this, 'change_event_link'               ), 10, 5 );
	}

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @param $classes
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		$classes = parent::add_body_classes( $classes );

		if ( $this->is_template() ) {
			$displayed_theme_mods = array(
				'single_tribe_events_breadcrumb_custom_header_displayed',

				'single_tribe_events_thumbnail_displayed',
				'single_tribe_events_title_displayed',
				'single_tribe_events_category_displayed',
				'single_tribe_events_date_displayed',
				'single_tribe_events_venue_displayed',
				'single_tribe_events_organizer_displayed',
				'single_tribe_events_cost_displayed',
				'single_tribe_events_website_displayed',
				'single_tribe_events_breadcrumb_displayed',
			);

			foreach ( $displayed_theme_mods as $theme_mod ) {
				if ( ! empty( $this->get_theme_mod( $theme_mod ) ) ) {
					$classes[] = "grimlock-the-events-calendar--{$theme_mod}";
				}
			}

			$classes[] = "grimlock-the-events-calendar--single_tribe_events_navigation_layout_{$this->get_theme_mod( 'single_tribe_events_navigation_layout' )}";
		}

		return $classes;
	}

	/**
	 * Add custom classes to content to modify layout.
	 *
	 * @param $classes
	 *
	 * @return string[]
	 */
	public function add_content_classes( $classes ) {
		$classes = parent::add_content_classes( $classes );

		if ( $this->is_template() ) {
			$classes[] = "grimlock-the-events-calendar--region--content-{$this->get_theme_mod( "{$this->id}_content_layout" )}";
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
		$js_data['tabs'][ $this->section ] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-the-events-calendar' ),
				'class' => 'single_tribe_events-general-tab',
				'controls' => array(
					"{$this->section}_heading_10",
					'single_tribe_events_custom_header_displayed',
					'single_tribe_events_category_custom_header_displayed',
					'single_tribe_events_date_custom_header_displayed',
					'single_tribe_events_venue_custom_header_displayed',
					'single_tribe_events_organizer_custom_header_displayed',
					'single_tribe_events_cost_custom_header_displayed',
					'single_tribe_events_breadcrumb_custom_header_displayed',

					"{$this->section}_divider_100",
					"{$this->section}_heading_100",
					'single_tribe_events_thumbnail_displayed',
					'single_tribe_events_title_displayed',
					'single_tribe_events_category_displayed',
					'single_tribe_events_date_displayed',
					'single_tribe_events_venue_displayed',
					'single_tribe_events_organizer_displayed',
					'single_tribe_events_cost_displayed',
					'single_tribe_events_website_displayed',
					'single_tribe_events_breadcrumb_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-the-events-calendar' ),
				'class' => 'single_tribe_events-layout-tab',
				'controls' => array(
					'single_tribe_events_custom_header_layout',
					"{$this->section}_divider_110",
					'single_tribe_events_custom_header_container_layout',
					"{$this->section}_divider_120",
					'single_tribe_events_layout',
					'single_tribe_events_sidebar_mobile_displayed',
					"{$this->section}_divider_140",
					'single_tribe_events_content_layout',
					"{$this->section}_divider_150",
					'single_tribe_events_container_layout',
					"{$this->section}_divider_160",
					'single_tribe_events_navigation_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-the-events-calendar' ),
				'class' => 'single_tribe_events-style-tab',
				'controls' => array(
					'single_tribe_events_custom_header_padding_y',
					"{$this->section}_divider_210",
					'single_tribe_events_content_padding_y',
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
		$this->defaults = apply_filters( 'grimlock_the_events_calendar_single_tribe_events_customizer_defaults', array(
			'single_tribe_events_custom_header_displayed'            => has_header_image(),
			'single_tribe_events_category_custom_header_displayed'   => false,
			'single_tribe_events_date_custom_header_displayed'       => false,
			'single_tribe_events_venue_custom_header_displayed'      => false,
			'single_tribe_events_organizer_custom_header_displayed'  => false,
			'single_tribe_events_cost_custom_header_displayed'       => false,

			'single_tribe_events_breadcrumb_custom_header_displayed' => true,

			'single_tribe_events_thumbnail_displayed'                => true,
			'single_tribe_events_title_displayed'                    => true,
			'single_tribe_events_category_displayed'                 => true,
			'single_tribe_events_date_displayed'                     => true,
			'single_tribe_events_venue_displayed'                    => true,
			'single_tribe_events_organizer_displayed'                => true,
			'single_tribe_events_cost_displayed'                     => true,
			'single_tribe_events_website_displayed'                  => true,
			'single_tribe_events_breadcrumb_displayed'               => true,

			'single_tribe_events_custom_header_layout'               => '12-cols-center',
			'single_tribe_events_custom_header_container_layout'     => 'classic',
			'single_tribe_events_layout'                             => '3-6-3-cols-left',
			'single_tribe_events_sidebar_mobile_displayed'           => true,
			'single_tribe_events_content_layout'                     => '9-3-cols-left',
			'single_tribe_events_container_layout'                   => 'classic',
			'single_tribe_events_navigation_layout'                  => 'classic',

			'single_tribe_events_custom_header_padding_y'            => GRIMLOCK_SECTION_PADDING_Y,
			'single_tribe_events_content_padding_y'                  => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section();

		$this->add_heading_field(                            array( 'priority' => 10, 'label' => esc_html__( 'Header Display', 'grimlock-the-events-calendar' ) ) );
		$this->add_custom_header_displayed_field(            array( 'priority' => 20  ) );
		$this->add_category_custom_header_displayed_field(   array( 'priority' => 30  ) );
		$this->add_date_custom_header_displayed_field(       array( 'priority' => 40  ) );
		$this->add_venue_custom_header_displayed_field(      array( 'priority' => 50  ) );
		$this->add_organizer_custom_header_displayed_field(  array( 'priority' => 60  ) );
		$this->add_cost_custom_header_displayed_field(       array( 'priority' => 70  ) );
		$this->add_breadcrumb_custom_header_displayed_field( array( 'priority' => 80 ) );

		if ( ! tribe( 'events.editor.compatibility' )->is_blocks_editor_toggled_on() ) {
			$this->add_divider_field(                        array( 'priority' => 100 ) );
			$this->add_heading_field(                        array( 'priority' => 100, 'label' => esc_html__( 'Content Display', 'grimlock-the-events-calendar' ) ) );
			$this->add_thumbnail_displayed_field(            array( 'priority' => 110 ) );
			$this->add_title_displayed_field(                array( 'priority' => 120 ) );
			$this->add_category_displayed_field(             array( 'priority' => 130 ) );
			$this->add_date_displayed_field(                 array( 'priority' => 140 ) );
			$this->add_venue_displayed_field(                array( 'priority' => 150 ) );
			$this->add_organizer_displayed_field(            array( 'priority' => 160 ) );
			$this->add_cost_displayed_field(                 array( 'priority' => 170 ) );
			$this->add_website_displayed_field(              array( 'priority' => 180 ) );
			$this->add_breadcrumb_displayed_field(           array( 'priority' => 190 ) );
		}

		$this->add_custom_header_layout_field(               array( 'priority' => 100 ) );
		$this->add_divider_field(                            array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field(     array( 'priority' => 110 ) );
		$this->add_divider_field(                            array( 'priority' => 120 ) );
		$this->add_layout_field(                             array( 'priority' => 120 ) );
		$this->add_sidebar_mobile_displayed_field(           array( 'priority' => 130 ) );
		if ( ! tribe( 'events.editor.compatibility' )->is_blocks_editor_toggled_on() ) {
			$this->add_divider_field(                        array( 'priority' => 140 ) ); // TODO: Uncomment when implementing content layout styles
			$this->add_content_layout_field(                 array( 'priority' => 140 ) ); // TODO: Uncomment when implementing content layout styles
		}
		$this->add_divider_field(                            array( 'priority' => 150 ) );
		$this->add_container_layout_field(                   array( 'priority' => 150 ) );
		$this->add_divider_field(                            array( 'priority' => 160 ) );
		$this->add_navigation_layout_field(                  array( 'priority' => 160 ) );

		$this->add_custom_header_padding_y_field(            array( 'priority' => 200 ) );
		$this->add_divider_field(                            array( 'priority' => 210 ) );
		$this->add_content_padding_y_field(                  array( 'priority' => 210 ) );
	}

	/**
	 * Add a Kirki checkbox to set the header category display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_category_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display category', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_category_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_category_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_category_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header event date display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_date_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display date', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_date_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_date_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_date_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header venue display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_venue_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display venue', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_venue_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_venue_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_venue_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header organizer display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_organizer_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display organizer', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_organizer_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_organizer_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_organizer_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the header cost display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_cost_custom_header_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display cost', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_cost_custom_header_displayed",
				'default'  => $this->get_default( "{$this->id}_cost_custom_header_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_cost_custom_header_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the breadcrumb display in the Customizer.
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
	 * Add a Kirki checkbox to set the title display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_title_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display title', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_title_displayed",
				'default'  => $this->get_default( "{$this->id}_title_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_title_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the thumbnail display in the Customizer.
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
	 * Add a Kirki checkbox to set the category display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_category_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display category', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_category_displayed",
				'default'  => $this->get_default( "{$this->id}_category_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_category_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the date display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_date_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display date', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_date_displayed",
				'default'  => $this->get_default( "{$this->id}_date_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_date_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the venue display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_venue_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display venue', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_venue_displayed",
				'default'  => $this->get_default( "{$this->id}_venue_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_venue_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the organizer display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_organizer_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display organizer', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_organizer_displayed",
				'default'  => $this->get_default( "{$this->id}_organizer_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_organizer_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the cost display in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_cost_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display cost', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_cost_displayed",
				'default'  => $this->get_default( "{$this->id}_cost_displayed" ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_cost_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the website display in the Customizer.
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
	 * Add a Kirki checkbox to set the back button display in the Customizer.
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
	 * Add a Kirki radio-image field to set the layout for the navigation in the Customizer.
	 *
	 * @param array $args
	 */
	protected function add_navigation_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Navigation', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_navigation_layout",
				'default'  => $this->get_default( "{$this->id}_navigation_layout" ),
				'priority' => 10,
				'choices'  => array(
					'classic'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-classic.png',
					'modern-floating' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-modern-floating.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_navigation_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the content layout in the Customizer.
	 *
	 * @param array $args
	 */
	protected function add_content_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Content Layout', 'grimlock-the-events-calendar' ),
				'settings' => "{$this->id}_content_layout",
				'default'  => $this->get_default( "{$this->id}_content_layout" ),
				'priority' => 10,
				'choices'  => array(
					'12-cols-left'  => GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/images/template-content-12-cols-left.png',
					'9-3-cols-left' => GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/images/template-content-9-3-cols-left.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_content_layout_field_args", $args ) );
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
				'priority' => 20,
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
			$args['background_image']                        = apply_filters( 'grimlock_the_events_calendar_custom_header_background_image', '' );
			$args['single_tribe_events_category_displayed']  = $this->get_theme_mod( 'single_tribe_events_category_custom_header_displayed' );
			$args['single_tribe_events_date_displayed']      = $this->get_theme_mod( 'single_tribe_events_date_custom_header_displayed' );
			$args['single_tribe_events_venue_displayed']     = $this->get_theme_mod( 'single_tribe_events_venue_custom_header_displayed' );
			$args['single_tribe_events_organizer_displayed'] = $this->get_theme_mod( 'single_tribe_events_organizer_custom_header_displayed' );
			$args['single_tribe_events_cost_displayed']      = $this->get_theme_mod( 'single_tribe_events_cost_custom_header_displayed' );
			$args['single_post_back_displayed']              = $this->get_theme_mod( 'single_tribe_events_breadcrumb_custom_header_displayed' );
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
		$is_template = is_singular( 'tribe_events' );
		return apply_filters( 'grimlock_the_events_calendar_single_tribe_events_customizer_is_template', $is_template );
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

	/**
	 * Change the event next/previous link depending on the navigation layout selected in the customizer
	 *
	 * @param string  $link Next/previous event link
	 * @param int     $current_event_id Current event id
	 * @param WP_Post $event Next/previous event
	 * @param string  $mode "previous" or "next"
	 * @param string  $anchor Link text
	 *
	 * @return string The modified link
	 */
	public function change_event_link( $link, $current_event_id, $event, $mode, $anchor ) {
		if ( ! $this->is_template() || empty( $event ) || empty( $link ) ) {
			return $link;
		}

		switch ( $this->get_theme_mod( 'single_tribe_events_navigation_layout' ) ) {
			case 'modern':
			case 'modern-floating':
				if ( ! empty( $event ) ) {
					// Remove filter to prevent infinite loop
					remove_filter( 'tribe_events_get_event_link', array( $this, 'change_event_link' ), 10 );

					if ( 'next' === $mode ) {
						$anchor = get_the_post_thumbnail( $event->ID, 'thumbnail' ) . $anchor;
					}
					else if ( 'previous' === $mode ) {
						$anchor = $anchor . get_the_post_thumbnail( $event->ID, 'thumbnail' );
					}

					$link = tribe( 'tec.adjacent-events' )->get_event_link( $mode, $anchor );

					// Re-add filter
					add_filter( 'tribe_events_get_event_link', array( $this, 'change_event_link' ), 10, 5 );
				}
				break;
		}

		return $link;
	}
}

return new Grimlock_The_Events_Calendar_Single_Tribe_Events_Customizer();
