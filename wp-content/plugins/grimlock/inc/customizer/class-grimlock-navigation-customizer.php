<?php
/**
 * Grimlock_Navigation_Customizer Class
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
class Grimlock_Navigation_Customizer extends Grimlock_Base_Customizer {
	/**
	 * @var array The array of elements to target the navigation in theme.
	 * @since 1.0.0
	 */
	protected $elements;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_navigation_customizer_section';
		$this->title   = esc_html__( 'Navigation', 'grimlock' );

		add_action( 'after_setup_theme',                       array( $this, 'add_customizer_fields'           ), 25    );

		add_action( 'wp_enqueue_scripts',                      array( $this, 'enqueue_scripts'                 ), 10    );

		add_filter( 'body_class',                              array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',    array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_navigation_has_position_top',    array( $this, 'has_position_top'                ), 10, 1 );
		add_filter( 'grimlock_navigation_has_position_bottom', array( $this, 'has_position_bottom'             ), 10, 1 );
		add_filter( 'grimlock_search_modal_displayed',         array( $this, 'has_search_modal_displayed'      ), 10, 1 );

		add_filter( 'grimlock_navigation_args',                array( $this, 'add_args'                        ), 10, 1 );
		add_filter( 'grimlock_vertical_navigation_args',       array( $this, 'add_args'                        ), 10, 1 );
		add_filter( 'grimlock_site_identity_args',             array( $this, 'add_site_identity_args'          ), 10, 1 );

		add_action( 'customize_register',                      array( $this, 'add_partial'                     ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',              array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @since 1.0.0
	 * @param $classes
	 *
	 * @return string
	 */
	public function add_body_classes( $classes ) {
		$classes[] = "grimlock--navigation-{$this->get_theme_mod( 'navigation_layout' )}";
		$classes[] = "grimlock--navigation-{$this->get_theme_mod( 'navigation_position' )}";

		// Add custom body classes to adjust the sticky navbar.
		if ( true == $this->get_theme_mod( 'navigation_stick_to_top' ) ) {
			$classes[] = 'grimlock--navigation-fixed';
		}
		return $classes;
	}

	/**
	 * Add navigation component args
	 *
	 * @since 1.0.0
	 * @param array $args Component args
	 *
	 * @return array
	 */
	public function add_args( $args ) {
		$classes = array();
		if ( ! empty( $args['class'] ) ) {
			$classes = is_array( $args['class'] ) ? $args['class'] : array( $args['class'] );
		}

		$is_vertically_displayed = in_array(
			$this->get_theme_mod( 'navigation_layout' ),
			array(
				'fixed-left',
				'fixed-right',
				'hamburger-left',
				'hamburger-right',
			)
		);

		if ( ! $is_vertically_displayed && $this->get_theme_mod( 'navigation_mobile_accordion_displayed' ) ) {
			$classes[] = 'grimlock-navigation-mobile-accordion';
		}

		return wp_parse_args( array(
			'color'                       => $this->get_theme_mod( 'navigation_menu_item_color' ),
			'border_top_color'            => $this->get_theme_mod( 'navigation_border_top_color' ),
			'border_top_width'            => $this->get_theme_mod( 'navigation_border_top_width' ),
			'border_bottom_color'         => $this->get_theme_mod( 'navigation_border_bottom_color' ),
			'border_bottom_width'         => $this->get_theme_mod( 'navigation_border_bottom_width' ),
			'search_form_displayed'       => $this->get_theme_mod( 'navigation_search_form_displayed' ),
			'search_form_modal_displayed' => $this->get_theme_mod( 'navigation_search_form_modal_displayed' ),
			'layout'                      => $this->get_theme_mod( 'navigation_layout' ),
			'container_layout'            => $this->get_theme_mod( 'navigation_container_layout' ),
			'secondary_logo_displayed'    => ! empty( $this->get_theme_mod( 'navigation_secondary_logo' ) ),
			'class'                       => $classes,
		), $args );
	}

	/**
	 * Add site identity component args
	 *
	 * @param array $args Component args
	 *
	 * @return array
	 */
	public function add_site_identity_args( $args ) {
		if ( ! empty( $args['secondary'] ) ) {
			return wp_parse_args( array(
				'custom_logo_displayed' => ! empty( $this->get_theme_mod( 'navigation_secondary_logo' ) ),
				'custom_logo'           => '<a href="' . esc_url( home_url( '/' ) ) . '" rel="home"><img src="' . esc_url( $this->get_theme_mod( 'navigation_secondary_logo' ) ) . '" alt="logo" /></a>',
			), $args );
		}

		return $args;
	}

	/**
	 * Check whether navigation has top position.
	 *
	 * @param $default
	 *
	 * @return bool True when navigation has top position, false otherwise.
	 * @since  1.0.0
	 */
	public function has_position_top( $default ) {
		return 'classic-top' === $this->get_theme_mod( 'navigation_position' ) ||
		       'inside-top' === $this->get_theme_mod( 'navigation_position' );
	}

	/**
	 * Check whether navigation has bottom position.
	 *
	 * @param $default
	 *
	 * @return bool True when navigation has bottom position, false otherwise.
	 * @since  1.0.0
	 */
	public function has_position_bottom( $default ) {
		return 'classic-bottom' === $this->get_theme_mod( 'navigation_position' ) ||
		       'inside-bottom' === $this->get_theme_mod( 'navigation_position' );
	}

	/**
	 * Check whether search modal should be displayed
	 *
	 * @param $default
	 *
	 * @return bool True if the search modal should be displayed, false otherwise
	 * @since  1.3.7
	 */
	public function has_search_modal_displayed( $default ) {
		return ! empty( $this->get_theme_mod( 'navigation_search_form_modal_displayed' ) );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_navigation_customizer_defaults', array(
			'navigation_font'                                => array(
				'font-family'                                => GRIMLOCK_FONT_FAMILY_SANS_SERIF,
				'font-weight'                                => 'regular',
				'font-size'                                  => GRIMLOCK_FONT_SIZE,
				'line-height'                                => '1.25',
				'letter-spacing'                             => GRIMLOCK_LETTER_SPACING,
				'subsets'                                    => array( 'latin-ext' ),
				'text-transform'                             => 'none',
			),

			'navigation_background_color'                    => '#ffffff',
			'navigation_background_gradient_displayed'       => false,
			'navigation_background_gradient_first_color'     => 'rgba(0,0,0,0)',
			'navigation_background_gradient_second_color'    => 'rgba(0,0,0,.35)',
			'navigation_background_gradient_direction'       => '0deg',
			'navigation_background_gradient_position'        => '0', // %
			'navigation_menu_item_color'                     => GRIMLOCK_NAVIGATION_ITEM_COLOR,
			'navigation_menu_item_active_background_color'   => '#ffffff',
			'navigation_menu_item_active_color'              => 'rgba(0,0,0,.9)',
			'navigation_sub_menu_border_width'               => 0,
			'navigation_sub_menu_border_color'               => GRIMLOCK_BORDER_COLOR,
			'navigation_sub_menu_item_color'                 => 'rgba(0,0,0,.5)',
			'navigation_sub_menu_item_background_color'      => '#ffffff',

			'navigation_search_form_displayed'               => false,
			'navigation_search_form_modal_displayed'         => false,
			'navigation_search_form_color'                   => '#ffffff',
			'navigation_search_form_placeholder_color'       => 'rgba(255,255,255,0.6)',
			'navigation_search_form_background_color'        => 'rgba(0,0,0,0.2)',
			'navigation_search_form_active_background_color' => 'rgba(0,0,0,0.8)',
			'navigation_search_form_active_color'            => '#ffffff',

			'navigation_border_top_color'                    => GRIMLOCK_BORDER_COLOR,
			'navigation_border_top_width'                    => 0, // px
			'navigation_border_bottom_color'                 => GRIMLOCK_BORDER_COLOR,
			'navigation_border_bottom_width'                 => 0, // px

			'navigation_layout'                              => 'classic-left',
			'navigation_mobile_accordion_displayed'          => false,
			'navigation_secondary_logo'                      => wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' ),
			'navigation_secondary_logo_size'                 => 125,
			'navigation_position'                            => 'classic-top',
			'navigation_container_layout'                    => 'classic',
			'navigation_padding_y'                           => 1.5, // rem
			'navigation_stick_to_top'                        => false,
			'navigation_stick_to_top_background_color'       => '#ffffff',
			'navigation_mobile_background_color'             => '#ffffff',
		) );

		$this->elements = apply_filters( 'grimlock_navigation_customizer_elements', array(
			'.main-navigation',
		) );

		$this->add_section(                                   array( 'priority' => 30 ) );

		$this->add_font_field(                                array( 'priority' => 10 ) );
		$this->add_divider_field(                             array( 'priority' => 20 ) );
		$this->add_menu_item_color_field(                     array( 'priority' => 20 ) );
		$this->add_menu_item_active_color_field(              array( 'priority' => 30 ) );
		$this->add_menu_item_active_background_color_field(   array( 'priority' => 40 ) );
		$this->add_divider_field(                             array( 'priority' => 50 ) );
		$this->add_sub_menu_item_color_field(                 array( 'priority' => 50 ) );
		$this->add_sub_menu_item_background_color_field(      array( 'priority' => 60 ) );
		$this->add_sub_menu_border_width_field(               array( 'priority' => 70 ) );
		$this->add_sub_menu_border_color_field(               array( 'priority' => 80 ) );

		$this->add_heading_field( array(
			'label'    => esc_html__( 'Display', 'grimlock' ),
			'priority' => 200,
		) );

		$this->add_search_form_displayed_field(               array( 'priority' => 200 ) );
		$this->add_search_form_modal_displayed_field(         array( 'priority' => 210 ) );
		$this->add_divider_field(                             array( 'priority' => 220 ) );
		$this->add_search_form_color_field(                   array( 'priority' => 220 ) );
		$this->add_search_form_placeholder_color_field(       array( 'priority' => 230 ) );
		$this->add_search_form_background_color_field(        array( 'priority' => 240 ) );
		$this->add_divider_field(                             array( 'priority' => 250 ) );
		$this->add_search_form_active_color_field(            array( 'priority' => 250 ) );
		$this->add_search_form_active_background_color_field( array( 'priority' => 260 ) );

		$this->add_layout_field(                              array( 'priority' => 310 ) );
		$this->add_mobile_accordion_displayed_field(          array( 'priority' => 320 ) );
		$this->add_secondary_logo_field(                      array( 'priority' => 330 ) );
		$this->add_secondary_logo_size_field(                 array( 'priority' => 340 ) );
		$this->add_divider_field(                             array( 'priority' => 350 ) );
		$this->add_container_layout_field(                    array( 'priority' => 350 ) );
		if ( class_exists( 'Grimlock_Hero' ) ) {
			$this->add_divider_field(                         array( 'priority' => 360 ) );
			$this->add_position_field(                        array( 'priority' => 360 ) );
		}

		$this->add_padding_y_field(                           array( 'priority' => 410 ) );
		$this->add_divider_field(                             array( 'priority' => 420 ) );
		$this->add_background_color_field(                    array( 'priority' => 420 ) );
		$this->add_background_gradient_displayed_field(       array( 'priority' => 430 ) );
		$this->add_background_gradient_first_color_field(     array( 'priority' => 440 ) );
		$this->add_background_gradient_second_color_field(    array( 'priority' => 450 ) );
		$this->add_background_gradient_direction_field(       array( 'priority' => 460 ) );
		$this->add_background_gradient_position_field(        array( 'priority' => 470 ) );
		$this->add_divider_field(                             array( 'priority' => 480 ) );
		$this->add_heading_field( array(
			'label'    => esc_html__( 'Sticky Navigation', 'grimlock' ),
			'priority' => 480,
		) );
		$this->add_stick_to_top_field(                        array( 'priority' => 480 ) );
		$this->add_stick_to_top_background_color_field(       array( 'priority' => 490 ) );
		$this->add_divider_field(                             array( 'priority' => 500 ) );
		$this->add_mobile_background_color_field(             array( 'priority' => 500 ) );
		$this->add_divider_field(                             array( 'priority' => 510 ) );
		$this->add_border_top_width_field(                    array( 'priority' => 510 ) );
		$this->add_border_top_color_field(                    array( 'priority' => 520 ) );
		$this->add_divider_field(                             array( 'priority' => 530 ) );
		$this->add_border_bottom_width_field(                 array( 'priority' => 530 ) );
		$this->add_border_bottom_color_field(                 array( 'priority' => 540 ) );
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
				'label'    => esc_html__( 'Item', 'grimlock' ),
				'class'    => 'navigation-menu_item-tab',
				'controls' => array(
					'navigation_font',
					"{$this->section}_divider_20",
					'navigation_menu_item_color',
					'navigation_menu_item_active_background_color',
					'navigation_menu_item_active_color',
					"{$this->section}_divider_50",
					'navigation_sub_menu_item_color',
					'navigation_sub_menu_item_background_color',
					'navigation_sub_menu_border_width',
					'navigation_sub_menu_border_color',
				),
			),
			array(
				'label'    => esc_html__( 'Search', 'grimlock' ),
				'class'    => 'navigation-search_form-tab',
				'controls' => array(
					"{$this->section}_heading_200",
					'navigation_search_form_displayed',
					'navigation_search_form_modal_displayed',
					"{$this->section}_divider_220",
					'navigation_search_form_color',
					'navigation_search_form_placeholder_color',
					'navigation_search_form_background_color',
					"{$this->section}_divider_250",
					'navigation_search_form_active_color',
					'navigation_search_form_active_background_color',
				),
			),
			array(
				'label'    => esc_html__( 'Layout', 'grimlock' ),
				'class'    => 'navigation-layout-tab',
				'controls' => array(
					'navigation_layout',
					'navigation_mobile_accordion_displayed',
					'navigation_secondary_logo',
					'navigation_secondary_logo_size',
					"{$this->section}_divider_350",
					'navigation_position',
					"{$this->section}_divider_360",
					'navigation_container_layout',
				),
			),
			array(
				'label'    => esc_html__( 'Style', 'grimlock' ),
				'class'    => 'navigation-style-tab',
				'controls' => array(
					'navigation_padding_y',
					"{$this->section}_divider_420",
					'navigation_background_color',
					'navigation_background_gradient_displayed',
					'navigation_background_gradient_first_color',
					'navigation_background_gradient_second_color',
					'navigation_background_gradient_direction',
					'navigation_background_gradient_position',
					"{$this->section}_divider_530",
					"{$this->section}_heading_480",
					'navigation_stick_to_top',
					'navigation_stick_to_top_background_color',
					"{$this->section}_divider_500",
					'navigation_mobile_background_color',
					"{$this->section}_divider_510",
					'navigation_border_top_color',
					'navigation_border_top_width',
					"{$this->section}_divider_480",
					'navigation_border_bottom_color',
					'navigation_border_bottom_width',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki typography field to set the typography in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_font_elements', array(
				'.main-navigation .navbar-nav a',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_font_outputs', array(
				array(
					'element' => implode( ',', $elements ),
				),
				array(
					'element'       => $elements,
					'property'      => 'font-family',
					'choice'        => 'font-family',
					'value_pattern' => '$, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'navigation_font',
				'label'     => esc_attr__( 'Menu Item Typography', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'navigation_font' ),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'navigation_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_font_field_args', $args ) );
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
			$elements = apply_filters( 'grimlock_navigation_customizer_background_color_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_navigation_customizer_background_color_outputs', array(
				$this->get_css_var_output( 'navigation_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_background_color',
				'default'   => $this->get_default( 'navigation_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to activate the use of a gradient as background color for the Custom Header.
	 *
	 * @param array $args
	 * @since 1.0.8
	 */
	protected function add_background_gradient_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Add gradient to background color', 'grimlock' ),
				'settings' => 'navigation_background_gradient_displayed',
				'default'  => $this->get_default( 'navigation_background_gradient_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_background_gradient_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the first color of the background gradient in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_gradient_first_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Gradient First Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_background_gradient_first_color',
				'default'   => $this->get_default( 'navigation_background_gradient_first_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'navigation_background_gradient_first_color' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_background_gradient_first_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the second color of the background gradient in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_gradient_second_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Gradient Second Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_background_gradient_second_color',
				'default'   => $this->get_default( 'navigation_background_gradient_second_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'navigation_background_gradient_second_color' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_background_gradient_second_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_gradient_direction_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Background Gradient Direction', 'grimlock' ),
				'settings' => 'navigation_background_gradient_direction',
				'default'  => $this->get_default( 'navigation_background_gradient_direction' ),
				'priority' => 10,
				'choices'  => array(
					'315deg' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-315.png',
					'0deg'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-0.png',
					'45deg'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-45.png',
					'270deg' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-270.png',
					'360deg' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-360.png',
					'90deg'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-90.png',
					'225deg' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-225.png',
					'180deg' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-180.png',
					'135deg' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/gradient-direction-135.png',
				),
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'navigation_background_gradient_direction' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_background_gradient_direction_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the background gradient position in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_background_gradient_position_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Background Gradient Position', 'grimlock' ),
				'settings'  => 'navigation_background_gradient_position',
				'default'   => $this->get_default( 'navigation_background_gradient_position' ),
				'choices'   => array(
					'min'   => -100,
					'max'   => 100,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'navigation_background_gradient_position', '%' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_background_gradient_position_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the menu item color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_menu_item_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_menu_item_color_elements', array(
				'.main-navigation .navbar-nav a',
				'.main-navigation .navbar-nav > li.menu-item > a',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_menu_item_color_outputs', array(
				$this->get_css_var_output( 'navigation_menu_item_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', array(
						'.main-navigation .navbar-toggler span',
						'.main-navigation .navbar-toggler span::before',
						'.main-navigation .navbar-toggler span::after',
					) ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'     => 'color',
				'label'    => esc_html__( 'Menu Item Text Color', 'grimlock' ),
				'section'  => $this->section,
				'settings' => 'navigation_menu_item_color',
				'default'  => $this->get_default( 'navigation_menu_item_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority' => 10,
				'output'   => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_menu_item_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the menu item color when active in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_menu_item_active_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_menu_item_active_color_elements', array(
				'.main-navigation .navbar-nav > li.current-menu-item > a',
				'.main-navigation .navbar-nav > li.current-menu-parent > a',
				'.main-navigation .navbar-nav > li.current-menu-ancestor > a',
				'.main-navigation .navbar-nav > li.menu-item:hover > a',
				'.main-navigation .navbar-nav > li.menu-item > a:hover',
				'.main-navigation .navbar-nav > li.menu-item > a:active',
				'.main-navigation .navbar-nav > li.menu-item > a:focus',
				'.main-navigation .navbar-nav > li.menu-item.is-toggled > a',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_menu_item_active_color_outputs', array(
				$this->get_css_var_output( 'navigation_menu_item_active_color' ),
				array(
					'element'     => implode( ',', $elements ),
					'property'    => 'color',
					'media_query' => '@media (min-width: 992px)',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Menu Item Active Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_menu_item_active_color',
				'default'   => $this->get_default( 'navigation_menu_item_active_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_menu_item_active_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the menu item background color when active in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_menu_item_active_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_menu_item_active_background_color_elements', array(
				'.main-navigation .navbar-nav > .current-menu-item > a',
				'.main-navigation .navbar-nav > .current-menu-parent > a',
				'.main-navigation .navbar-nav > .current-menu-ancestor > a',
				'.main-navigation .navbar-nav > .menu-item > a:hover',
				'.main-navigation .navbar-nav > .menu-item > a:active',
				'.main-navigation .navbar-nav > .menu-item > a:focus',
				'.main-navigation .navbar-nav > .menu-item.is-toggled > a',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_menu_item_active_background_color_outputs', array(
				$this->get_css_var_output( 'navigation_menu_item_active_background_color' ),
				array(
					'element'     => implode( ',', $elements ),
					'property'    => 'background-color',
					'media_query' => '@media (min-width: 992px)',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Menu Item Active Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_menu_item_active_background_color',
				'default'   => $this->get_default( 'navigation_menu_item_active_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_menu_item_active_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the sub-menu item background color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_sub_menu_item_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_sub_menu_item_background_color_elements', array(
				'.main-navigation .navbar-nav > .menu-item .sub-menu',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_sub_menu_item_background_color_outputs', array(
				$this->get_css_var_output( 'navigation_sub_menu_item_background_color' ),
				array(
					'element'     => implode( ',', $elements ),
					'property'    => 'background-color',
					'media_query' => '@media (min-width: 992px)',
				),
				array(
					'element'  => implode( ',', array(
						'.dropdown-menu',
					) ),
					'property'    => 'background-color',
				),
				array(
					'element'  => implode( ',', array(
						'.main-navigation .navbar-nav > li.menu-item .sub-menu li.menu-item > a:hover',
						'.main-navigation .navbar-nav > li.menu-item .sub-menu li.menu-item > a:active',
						'.main-navigation .navbar-nav > li.menu-item .sub-menu li.menu-item > a:focus',
						'.preheader .menu > li.menu-item .sub-menu li.menu-item > a:hover',
						'.preheader .menu > li.menu-item .sub-menu li.menu-item > a:active',
						'.preheader .menu > li.menu-item .sub-menu li.menu-item > a:focus',
					) ),
					'property'    => 'color',
					'media_query' => '@media (min-width: 992px)',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Submenu Item Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_sub_menu_item_background_color',
				'default'   => $this->get_default( 'navigation_sub_menu_item_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_sub_menu_item_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the submenu border width in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_sub_menu_border_width_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Submenu Border Width', 'grimlock' ),
				'settings'  => 'navigation_sub_menu_border_width',
				'default'   => $this->get_default( 'navigation_sub_menu_border_width' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'navigation_sub_menu_border_width', 'px' )
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_sub_menu_border_width_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the sub-menu border color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_sub_menu_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Submenu Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_sub_menu_border_color',
				'default'   => $this->get_default( 'navigation_sub_menu_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'navigation_sub_menu_border_color' )
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_sub_menu_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the sub-menu item color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_sub_menu_item_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {

			$elements = apply_filters( 'grimlock_navigation_customizer_sub_menu_item_color_elements', array(
				'.main-navigation.navbar-nav > .menu-item .sub-menu',
				'.main-navigation .navbar-nav > .menu-item .sub-menu .menu-item > a',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_sub_menu_item_color_outputs', array(
				$this->get_css_var_output( 'navigation_sub_menu_item_color' ),
				array(
					'element'     => implode( ',', $elements ),
					'property'    => 'color',
					'media_query' => '@media (min-width: 992px)',
				),
				array(
					'element'  => implode( ',', array(
						'.dropdown-menu',
						'.dropdown-menu a',
					) ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', array(
						'.dropdown-divider',
						'.main-navigation .navbar-nav > li.menu-item .sub-menu li.menu-item > a:hover',
						'.main-navigation .navbar-nav > li.menu-item .sub-menu li.menu-item > a:active',
						'.main-navigation .navbar-nav > li.menu-item .sub-menu li.menu-item > a:focus',
						'.preheader .menu > li.menu-item .sub-menu li.menu-item > a:hover',
						'.preheader .menu > li.menu-item .sub-menu li.menu-item > a:active',
						'.preheader .menu > li.menu-item .sub-menu li.menu-item > a:focus',
					) ),
					'property'    => 'background-color',
					'media_query' => '@media (min-width: 992px)',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Submenu Item Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_sub_menu_item_color',
				'default'   => $this->get_default( 'navigation_sub_menu_item_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_sub_menu_item_color_field_args', $args ) );
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
			$elements = apply_filters( 'grimlock_navigation_customizer_border_top_width_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_navigation_customizer_border_top_width_outputs', array(
				$this->get_css_var_output( 'navigation_border_top_width', 'px' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-top-width',
					'units'    => 'px',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Top Width', 'grimlock' ),
				'settings'  => 'navigation_border_top_width',
				'default'   => $this->get_default( 'navigation_border_top_width' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_border_top_width_field_args', $args ) );
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
			$elements = apply_filters( 'grimlock_navigation_customizer_border_top_color_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_navigation_customizer_border_top_color_outputs', array(
				$this->get_css_var_output( 'navigation_border_top_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-top-color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Top Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_border_top_color',
				'default'   => $this->get_default( 'navigation_border_top_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_border_top_color_field_args', $args ) );
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
			$elements = apply_filters( 'grimlock_navigation_customizer_border_bottom_color_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_navigation_customizer_border_bottom_color_outputs', array(
				$this->get_css_var_output( 'navigation_border_bottom_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-bottom-color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Bottom Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_border_bottom_color',
				'default'   => $this->get_default( 'navigation_border_bottom_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_border_bottom_color_field_args', $args ) );
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
			$elements = apply_filters( 'grimlock_navigation_customizer_border_bottom_width_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_navigation_customizer_border_bottom_width_outputs', array(
				$this->get_css_var_output( 'navigation_border_bottom_width', 'px' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-bottom-width',
					'units'    => 'px',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Bottom Width', 'grimlock' ),
				'settings'  => 'navigation_border_bottom_width',
				'default'   => $this->get_default( 'navigation_border_bottom_width' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_border_bottom_width_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the search form color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_search_form_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_search_form_color_elements', array(
				'.navbar-search form.search-form input.search-field',
				'.navbar-search.navbar-search--animate .search-icon',
				'.navbar-search.navbar-search--animate:not(.navbar-search--open) .search-icon',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_search_form_color_outputs', array(
				$this->get_css_var_output( 'navigation_search_form_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Search Form Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_search_form_color',
				'default'   => $this->get_default( 'navigation_search_form_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_search_form_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the search form background color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_search_form_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_search_form_background_color_elements', array(
				'.navbar-search form.search-form input.search-field',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_search_form_background_color_outputs', array(
				$this->get_css_var_output( 'navigation_search_form_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
				array(
					'element'  => '.navbar-search form.search-form input.search-field:focus',
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Search Form Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_search_form_background_color',
				'default'   => $this->get_default( 'navigation_search_form_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_search_form_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the search form placeholder color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_search_form_placeholder_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$outputs = apply_filters( 'grimlock_navigation_customizer_search_form_placeholder_color_outputs', array(
				$this->get_css_var_output( 'navigation_search_form_placeholder_color' ),
				array(
					'element'  => '.navbar-search form.search-form input.search-field::-webkit-input-placeholder',
					'property' => 'color',
				),
				array(
					'element'  => '.navbar-search form.search-form input.search-field::-moz-placeholder',
					'property' => 'color',
				),
				array(
					'element'  => '.navbar-search form.search-form input.search-field:-ms-input-placeholder',
					'property' => 'color',
				),
				array(
					'element'  => '.navbar-search form.search-form input.search-field::placeholder',
					'property' => 'color',
				),
				array(
					'element'  => '.navbar-search form.search-form input.search-field:placeholder-shown',
					'property' => 'color',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Search Form Placeholder Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_search_form_placeholder_color',
				'default'   => $this->get_default( 'navigation_search_form_placeholder_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_search_form_placeholder_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the search form background color when active in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_search_form_active_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_search_form_active_background_color_elements', array(
				'element'  => '.navbar-search.navbar-search--open form.search-form input.search-field',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_search_form_active_background_color_outputs', array(
				$this->get_css_var_output( 'navigation_search_form_active_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
				array(
					'element'  => '.navbar-search form.search-form input.search-field',
					'property' => 'background-color',
					'suffix' => '!important',
					'media_query' => '@media (max-width: 992px)',
				),
				array(
					'element'  => array(
						'.vertical-navbar .navbar-search form.search-form input.search-field',
					),
					'property' => 'background-color',
					'suffix' => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Search Form Active Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_search_form_active_background_color',
				'default'   => $this->get_default( 'navigation_search_form_active_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_search_form_active_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the search form text color when active in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_search_form_active_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_search_form_color_elements', array(
				'.navbar-search.navbar-search--open form.search-form input.search-field',
				'.navbar-search.navbar-search--open .search-icon',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_search_form_color_outputs', array(
				$this->get_css_var_output( 'navigation_search_form_active_color' ),
				array(
					'element'     => '.navbar-search .search-icon',
					'property'    => 'color',
					'suffix'      => '!important',
					'media_query' => '@media (max-width: 992px)',
				),
				array(
					'element'  => array(
						'.vertical-navbar .navbar-search form.search-form input.search-field',
						'.vertical-navbar-search .search-form button[type="submit"]',
					),
					'property' => 'color',
					'suffix'   => '!important',
				),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Search Form Active Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'navigation_search_form_active_color',
				'default'   => $this->get_default( 'navigation_search_form_active_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_search_form_active_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the navigation layout in the Customizer.
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
				'settings' => 'navigation_layout',
				'default'  => $this->get_default( 'navigation_layout' ),
				'priority' => 10,
				'choices'  => array(
					'classic-left'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-classic-left.png',
					'classic-center'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-classic-center.png',
					'classic-right'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-classic-right.png',
					'fat-left'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-fat-left.png',
					'fat-center'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-fat-center.png',
					'fixed-left'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-fixed-left.png',
					'fixed-right'     => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-fixed-right.png',
					'hamburger-left'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-hamburger-left.png',
					'hamburger-right' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-hamburger-right.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_layout_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the mobile accordion display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.4.3
	 */
	public function add_mobile_accordion_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'label'    => esc_html__( 'Display sub menu items as accordion on mobile', 'grimlock' ),
				'section'  => $this->section,
				'settings' => 'navigation_mobile_accordion_displayed',
				'default'  => $this->get_default( 'navigation_mobile_accordion_displayed' ),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_layout',
						'operator' => 'in',
						'value'    => array(
							'classic-left',
							'classic-center',
							'classic-right',
							'fat-left',
							'fat-center',
						),
					),
				),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_mobile_accordion_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the secondary logo to display in the navbar when using hamburger style navigation
	 *
	 * @param array $args
	 */
	protected function add_secondary_logo_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Navbar logo', 'grimlock' ),
				'settings' => 'navigation_secondary_logo',
				'default'  => $this->get_default( 'navigation_secondary_logo' ),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_layout',
						'operator' => 'in',
						'value'    => array( 'hamburger-left', 'hamburger-right' ),
					),
				),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_secondary_logo_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the secondary logo size.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_secondary_logo_size_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_secondary_logo_size_elements', array(
				'.navbar-brand-secondary .grimlock-site_identity .site-logo img',
			) );

			$outputs  = apply_filters( 'grimlock_navigation_customizer_secondary_logo_size_outputs', array(
				$this->get_css_var_output( 'navigation_secondary_logo_size', 'px' ),
				array(
					'element'   => implode( ',', $elements ),
					'property'  => 'max-width',
					'units'     => 'px',
				),
				array(
					'element'   => implode( ',', $elements ),
					'property'  => 'max-height',
					'units'     => 'px',
				),
			), $elements );

			$custom_logo_args = get_theme_support( 'custom-logo' );

			$args = wp_parse_args( $args, array(
				'type'            => 'slider',
				'section'         => $this->section,
				'label'           => esc_attr__( 'Navbar logo size', 'grimlock' ),
				'settings'        => 'navigation_secondary_logo_size',
				'default'         => $this->get_default( 'navigation_secondary_logo_size' ),
				'choices'         => array(
					'min'  => 0,
					'max'  => isset( $custom_logo_args[0]['width'] ) ? $custom_logo_args[0]['width'] : 125,
					'step' => 5,
				),
				'priority'        => 10,
				'output'          => $outputs,
				'transport'       => 'postMessage',
				'js_vars'         => $this->to_js_vars( $outputs ),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_layout',
						'operator' => 'in',
						'value'    => array( 'hamburger-left', 'hamburger-right' ),
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_secondary_logo_size_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout for the navigation container in the Customizer.
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
				'settings' => 'navigation_container_layout',
				'default'  => $this->get_default( 'navigation_container_layout' ),
				'priority' => 10,
				'choices'  => array(
					'fluid'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-fluid.png',
					'classic'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-classic.png',
					'narrow'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrow.png',
					'narrower' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrower.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_container_layout_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the navigation position in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_position_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'               => 'radio-image',
				'section'            => $this->section,
				'label'              => esc_html__( 'Position', 'grimlock' ),
				'settings'           => 'navigation_position',
				'default'            => $this->get_default( 'navigation_position' ),
				'priority'           => 10,
				'choices'            => array(
					'classic-top'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-classic-top.png',
					'inside-top'     => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-inside-top.png',
					'classic-bottom' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-classic-bottom.png',
					'inside-bottom'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/navigation-inside-bottom.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_position_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the search form display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function add_search_form_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'label'    => esc_html__( 'Display search form', 'grimlock' ),
				'section'  => $this->section,
				'settings' => 'navigation_search_form_displayed',
				'default'  => $this->get_default( 'navigation_search_form_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_search_form_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the search form modal display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function add_search_form_modal_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Activate full screen mode', 'grimlock' ),
				'section'         => $this->section,
				'settings'        => 'navigation_search_form_modal_displayed',
				'default'         => $this->get_default( 'navigation_search_form_modal_displayed' ),
				'active_callback' => array(
					array(
						'setting'  => 'navigation_search_form_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority'        => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_search_form_modal_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to stick the navigation to top (or not) in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	public function add_stick_to_top_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'label'    => esc_html__( 'Stick navigation to top on scroll', 'grimlock' ),
				'section'  => $this->section,
				'settings' => 'navigation_stick_to_top',
				'default'  => $this->get_default( 'navigation_stick_to_top' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_stick_to_top_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color on scroll in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_stick_to_top_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_stick_to_top_background_color_elements', array(
				'.grimlock--navigation-stick-to-top .main-navigation',
				'body:not(.grimlock--custom_header-displayed) .grimlock-navigation',
			) );

			$outputs = apply_filters( 'grimlock_navigation_customizer_stick_to_top_background_color_outputs', array(
				$this->get_css_var_output( 'navigation_stick_to_top_background_color' ),
				array(
					'element'     => implode( ',', $elements ),
					'property'    => 'background-color',
				),
				array(
					'element'     => '.grimlock-navigation',
					'property'    => 'background-color',
					'media_query' => '@media (max-width: 992px)',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'        => 'color',
				'label'       => esc_html__( 'Background Color on Scroll', 'grimlock' ),
				'description' => esc_html__( 'This is the color of the navigation when page is scrolling. This color will also be applied when there is no custom header.', 'grimlock' ),
				'section'     => $this->section,
				'settings'    => 'navigation_stick_to_top_background_color',
				'default'     => $this->get_default( 'navigation_stick_to_top_background_color' ),
				'choices'     => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'    => 10,
				'transport'   => 'postMessage',
				'js_vars'     => $this->to_js_vars( $outputs ),
				'output'      => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_stick_to_top_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color for the mobile pages in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_mobile_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_navigation_customizer_mobile_background_color_elements', $this->elements );
			$outputs  = apply_filters( 'grimlock_navigation_customizer_mobile_background_color_outputs', array(
				$this->get_css_var_output( 'navigation_mobile_background_color' ),
				array(
					'element'     => implode( ',', $elements ),
					'property'    => 'background-color',
					'media_query' => '@media (max-width: 992px)',
				),
				array(
					'element'     => '.grimlock--navigation-hamburger-left .vertical-navbar',
					'property'    => 'background-color',
				),
				array(
					'element'     => '.grimlock--navigation-hamburger-right .vertical-navbar',
					'property'    => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'        => 'color',
				'label'       => esc_html__( 'Mobile Background Color', 'grimlock' ),
				'description' => esc_html__( 'This is the color of the navigation on mobile views. This color will also be applied to the vertical fixed navbar.', 'grimlock' ),
				'section'     => $this->section,
				'settings'    => 'navigation_mobile_background_color',
				'default'     => $this->get_default( 'navigation_mobile_background_color' ),
				'choices'     => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'    => 10,
				'output'      => $outputs,
				'transport'   => 'postMessage',
				'js_vars'     => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_mobile_background_color_field_args', $args ) );
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
			$elements = apply_filters( 'grimlock_navigation_customizer_padding_y_elements', array(
				'.grimlock-navigation.navbar--classic-left .navbar-nav > .menu-item > a',
				'.grimlock-navigation.navbar--classic-center .navbar-nav > .menu-item > a',
				'.grimlock-navigation.navbar--classic-right .navbar-nav > .menu-item > a',
				'.hamburger-navbar',
				'.navbar--fat-left',
				'.navbar--fat-center',
				'.navbar--fixed-left',
				'.navbar--fixed-right'
			) );

			$outputs  = apply_filters( 'grimlock_navigation_customizer_padding_y_outputs', array(
				$this->get_css_var_output( 'navigation_padding_y', 'rem' ),
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'padding-top',
					'units'         => 'rem',
					'media_query'   => '@media (min-width: 992px)',
				),
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'padding-bottom',
					'units'         => 'rem',
					'media_query'   => '@media (min-width: 992px)',
				),
				array(
					'element' => implode( ',', array(
						'.grimlock--navigation-fixed-left .grimlock-vertical-navigation .navbar-wrapper',
						'.grimlock--navigation-fixed-right .grimlock-vertical-navigation .navbar-wrapper',
					) ),
					'property'      => 'padding-top',
					'units'         => 'rem',
				),
				array(
					'element' => implode( ',', array(
						'.grimlock--navigation-fixed-left .grimlock-vertical-navigation .navbar-wrapper',
						'.grimlock--navigation-fixed-right .grimlock-vertical-navigation .navbar-wrapper',
					) ),
					'property'      => 'padding-bottom',
					'units'         => 'rem',
				),
				array(
					'element'       => '.grimlock--navigation-inside-top .grimlock-custom_header > .region__inner > .region__container',
					'property'      => 'margin-top',
					'value_pattern' => 'calc($rem * 2 + 15px)',
				),
				array(
					'element'       => '.grimlock--navigation-inside-bottom .grimlock-custom_header > .region__inner > .region__container',
					'property'      => 'margin-bottom',
					'value_pattern' => 'calc($rem * 2)',
					'media_query'   => '@media (min-width: 992px)',
				),
				array(
					'element'       => '.grimlock--navigation-inside-bottom .grimlock-custom_header > .region__inner > .region__container',
					'property'      => 'margin-top',
					'value_pattern' => 'calc($rem * 2)',
					'media_query'   => '@media (max-width: 992px)',
 				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Vertical Padding', 'grimlock' ),
				'tooltip'   => esc_html__( 'Adjusts the height of the navbar', 'grimlock' ),
				'settings'  => 'navigation_padding_y',
				'default'   => $this->get_default( 'navigation_padding_y' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 5,
					'step'  => .10,
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_navigation_customizer_padding_y_field_args', $args ) );
		}
	}

	/**
	 * Hack to add floating pen button for the secondary logo
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	public function add_partial( $wp_customize ) {
		$wp_customize->selective_refresh->add_partial( 'navigation_secondary_logo_partial', array(
				'selector' => '.navbar-brand-secondary .grimlock-site_identity .site-logo',
				'settings' => array( 'navigation_secondary_logo' ),
			)
		);
	}

	/**
	 * Add custom styles based on theme mods.
	 *
	 * @param string $styles The styles printed by Kirki
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_dynamic_css( $styles ) {
		$navigation_font  = $this->get_theme_mod( 'navigation_font' );
		$styles           .= ".main-navigation { font-family: {$navigation_font['font-family']}; }";

		$background_gradient_displayed = $this->get_theme_mod( 'navigation_background_gradient_displayed' );

		if ( ! empty( $background_gradient_displayed ) ) {
			$background_gradient_first_color  = $this->get_theme_mod( 'navigation_background_gradient_first_color' );
			$background_gradient_second_color = $this->get_theme_mod( 'navigation_background_gradient_second_color' );
			$background_gradient_direction    = $this->get_theme_mod( 'navigation_background_gradient_direction' );
			$background_gradient_position     = $this->get_theme_mod( 'navigation_background_gradient_position' );

			$styles .= "
			.grimlock-navigation {
				background-image: linear-gradient({$background_gradient_direction}, {$background_gradient_second_color} {$background_gradient_position}%, {$background_gradient_first_color} 100%)
			}";

		}

		return $styles;
	}

	/**
	 * Enqueue Navigation integration scripts.
	 *
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		$navigation_layout = $this->get_theme_mod( 'navigation_layout' );

		$is_fixed = in_array(
			$navigation_layout,
			array(
				'fixed-left',
				'fixed-right',
			)
		);

		$mobile_accordion_displayed = $this->get_theme_mod( 'navigation_mobile_accordion_displayed' );

		// Enqueue scripts for the navigation stick to top feature (except for the vertical fixed navigation layout).
		if ( true == $this->get_theme_mod( 'navigation_stick_to_top' ) && ! $is_fixed ) {
			wp_enqueue_script( 'grimlock-navigation-stick-to-top', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/navigation-stick-to-top.js', array( 'jquery' ), GRIMLOCK_VERSION, true );
		}

		$is_vertically_displayed = in_array(
			$navigation_layout,
			array(
				'fixed-left',
				'fixed-right',
				'hamburger-left',
				'hamburger-right',
			)
		);

		$has_position_left = in_array(
			$navigation_layout,
			array(
				'fixed-left',
				'hamburger-left',
			)
		);

		// Enqueue script for nav dropdown
		if ( $is_vertically_displayed || $mobile_accordion_displayed ) {
			wp_enqueue_script( 'grimlock-navigation-dropdown', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/navigation-dropdown.js', array( 'jquery' ), GRIMLOCK_VERSION, true );
		}

		// Enqueue scripts for the vertical navigation.
		if ( $is_vertically_displayed ) {
			wp_enqueue_script( 'slideout', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/vendor/slideout.js', array(), '0.1.12', true );
			wp_enqueue_script( 'grimlock-vertical-navigation', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/vertical-navigation.js', array( 'jquery', 'slideout' ), GRIMLOCK_VERSION, true );
			wp_localize_script( 'grimlock-vertical-navigation', 'grimlock_vertical_navigation', apply_filters( 'grimlock_vertical_navigation_js_data', array(
				'on_left' => $has_position_left,
				'padding' => 256,
			) ) );
		}

		// Enqueue scripts for the navigation search form.
		if ( true == $this->get_theme_mod( 'navigation_search_form_displayed' ) ) {
			wp_enqueue_script( 'grimlock-navigation-search', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/navigation-search.js', array( 'jquery' ), GRIMLOCK_VERSION, true );
		}
	}
}

return new Grimlock_Navigation_Customizer();
