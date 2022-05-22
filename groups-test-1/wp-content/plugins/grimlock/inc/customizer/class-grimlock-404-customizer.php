<?php
/**
 * Grimlock_404_Customizer Class
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
 * The Grimlock Customizer class for the 404 pages.
 */
class Grimlock_404_Customizer extends Grimlock_Region_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = '404';
		$this->title   = esc_html__( '404 Page', 'grimlock' );
		$this->section = 'grimlock_404_customizer_section';

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_action( 'customize_register',                   array( $this, 'add_partial'                     ), 10    );

		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_404_args',                    array( $this, 'add_args'                        ), 10, 1 );
		add_filter( 'grimlock_custom_header_displayed',     array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_prefooter_args',              array( $this, 'change_args'                     ), 10, 1 );
		add_filter( 'grimlock_footer_args',                 array( $this, 'change_args'                     ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_404_customizer_defaults', array(
			'404_background_image'              => '',
			'404_full_screen_displayed'         => false,
			'404_padding_y'                     => 16, // %
			'404_background_color'              => GRIMLOCK_SECTION_BACKGROUND_COLOR,

			'404_border_top_color'              => GRIMLOCK_BORDER_COLOR,
			'404_border_top_width'              => 0, // px
			'404_border_bottom_color'           => GRIMLOCK_BORDER_COLOR,
			'404_border_bottom_width'           => 0, // px

			'404_thumbnail'                     => '',

			'404_title'                         => esc_html__( 'Oops! That page can&rsquo;t be found.', 'grimlock' ),
			'404_title_format'                  => 'display-3',
			'404_title_color'                   => GRIMLOCK_BODY_COLOR,

			'404_subtitle'                      => esc_html__( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'grimlock' ),
			'404_subtitle_format'               => 'lead',
			'404_subtitle_color'                => GRIMLOCK_BODY_COLOR,

			'404_text'                          => '',
			'404_text_color'                    => GRIMLOCK_BODY_COLOR,
			'404_text_wpautoped'                => true,

			'404_search_form_displayed'         => true,

			'404_button_displayed'              => true,
			'404_button_text'                   => esc_html__( 'Back to homepage', 'grimlock' ),
			'404_button_link'                   => home_url( '/' ),
			'404_button_color'                  => GRIMLOCK_BUTTON_PRIMARY_COLOR,
			'404_button_background_color'       => GRIMLOCK_BUTTON_PRIMARY_BACKGROUND_COLOR,
			'404_button_border_color'           => GRIMLOCK_BUTTON_PRIMARY_BORDER_COLOR,
			'404_button_hover_color'            => GRIMLOCK_BUTTON_PRIMARY_HOVER_COLOR,
			'404_button_hover_background_color' => GRIMLOCK_BUTTON_PRIMARY_HOVER_BACKGROUND_COLOR,
			'404_button_hover_border_color'     => GRIMLOCK_BUTTON_PRIMARY_HOVER_BORDER_COLOR,

			'404_layout'                        => '12-cols-left',
			'404_container_layout'              => 'classic',
		) );

		$this->add_section(                             array( 'priority' => 120 ) );

		// Add fields to the General tab.
		$this->add_thumbnail_field(                     array( 'priority' => 10  ) );
		$this->add_divider_field(                       array( 'priority' => 20  ) );
		$this->add_title_field(                         array( 'priority' => 20  ) );
		$this->add_divider_field(                       array( 'priority' => 30  ) );
		$this->add_subtitle_field(                      array( 'priority' => 30  ) );
		$this->add_divider_field(                       array( 'priority' => 40  ) );
		$this->add_text_field(                          array( 'priority' => 40  ) );
		$this->add_text_wpautoped_field(                array( 'priority' => 50  ) );
		$this->add_divider_field(                       array( 'priority' => 60  ) );
		$this->add_search_form_displayed_field(         array( 'priority' => 60  ) );
		$this->add_divider_field(                       array( 'priority' => 70  ) );
		$this->add_button_text_field(                   array( 'priority' => 70  ) );
		$this->add_button_link_field(                   array( 'priority' => 80  ) );
		$this->add_button_displayed_field(              array( 'priority' => 90  ) );

		// Add fields to the Layout tab.
		$this->add_layout_field(                        array( 'priority' => 100 ) );
		$this->add_divider_field(                       array( 'priority' => 110 ) );
		$this->add_container_layout_field(              array( 'priority' => 110 ) );

		// Add fields to the Style tab.
		$this->add_background_image_field(              array( 'priority' => 200 ) );
		$this->add_divider_field(                       array( 'priority' => 210 ) );
		$this->add_padding_y_field(                     array( 'priority' => 210 ) );
		$this->add_full_screen_displayed_field(         array( 'priority' => 220 ) );
		$this->add_divider_field(                       array( 'priority' => 230 ) );
		$this->add_background_color_field(              array( 'priority' => 230 ) );
		$this->add_divider_field(                       array( 'priority' => 240 ) );
		$this->add_border_top_width_field(              array( 'priority' => 240 ) );
		$this->add_border_top_color_field(              array( 'priority' => 250 ) );
		$this->add_divider_field(                       array( 'priority' => 260 ) );
		$this->add_border_bottom_width_field(           array( 'priority' => 260 ) );
		$this->add_border_bottom_color_field(           array( 'priority' => 270 ) );
		$this->add_divider_field(                       array( 'priority' => 280 ) );
		$this->add_title_format_field(                  array( 'priority' => 280 ) );
		$this->add_title_color_field(                   array( 'priority' => 290 ) );
		$this->add_divider_field(                       array( 'priority' => 300 ) );
		$this->add_subtitle_format_field(               array( 'priority' => 300 ) );
		$this->add_subtitle_color_field(                array( 'priority' => 310 ) );
		$this->add_divider_field(                       array( 'priority' => 320 ) );
		$this->add_text_color_field(                    array( 'priority' => 320 ) );
		$this->add_divider_field(                       array( 'priority' => 330 ) );
		$this->add_button_background_color_field(       array( 'priority' => 330 ) );
		$this->add_button_color_field(                  array( 'priority' => 340 ) );
		$this->add_button_border_color_field(           array( 'priority' => 350 ) );
		$this->add_divider_field(                       array( 'priority' => 360 ) );
		$this->add_button_hover_background_color_field( array( 'priority' => 360 ) );
		$this->add_button_hover_color_field(            array( 'priority' => 370 ) );
		$this->add_button_hover_border_color_field(     array( 'priority' => 380 ) );
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.6
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
				'priority' => 20,
				'panel'    => 'grimlock_pages_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki image field to set the featured image for the section in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_thumbnail_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Featured Image', 'grimlock' ),
				'settings' => '404_thumbnail',
				'default'  => $this->get_default( '404_thumbnail' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_thumbnail_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki text field to set the title in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_title_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'text',
				'label'             => esc_html__( 'Title', 'grimlock' ),
				'section'           => $this->section,
				'settings'          => '404_title',
				'default'           => $this->get_default( '404_title' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_title_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the title color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_title_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_title_color_elements', array(
				'.grimlock-404 .section__title',
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Title Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_title_color',
				'default'   => $this->get_default( '404_title_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( '404_title_color' ),
				),
				'js_vars'   => array(
					array(
						'function'      => 'style',
						'element'       => implode( ',', $elements ),
						'property'      => 'color',
						'value_pattern' => '$ !important',
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_title_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the title format in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_title_format_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'select',
				'section'  => $this->section,
				'label'    => esc_html__( 'Title Format', 'grimlock' ),
				'settings' => '404_title_format',
				'default'  => $this->get_default( '404_title_format' ),
				'priority' => 10,
				'choices'  => array(
					'display-1' => esc_attr__( 'Heading 1', 'grimlock' ),
					'display-2' => esc_attr__( 'Heading 2', 'grimlock' ),
					'display-3' => esc_attr__( 'Heading 3', 'grimlock' ),
					'display-4' => esc_attr__( 'Heading 4', 'grimlock' ),
					'lead'      => esc_attr__( 'Subheading', 'grimlock' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_title_format_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki text field to set the subtitle in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_subtitle_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'text',
				'label'             => esc_html__( 'Subtitle', 'grimlock' ),
				'section'           => $this->section,
				'settings'          => '404_subtitle',
				'default'           => $this->get_default( '404_subtitle' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_subtitle_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the subtitle color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_subtitle_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_subtitle_color_elements', array(
				'.grimlock-404 .section__subtitle',
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Subtitle Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_subtitle_color',
				'default'   => $this->get_default( '404_subtitle_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( '404_subtitle_color' ),
				),
				'js_vars'   => array(
					array(
						'function'      => 'style',
						'element'       => implode( ',', $elements ),
						'property'      => 'color',
						'value_pattern' => '$ !important',
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_subtitle_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the subtitle format in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_subtitle_format_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'select',
				'section'  => $this->section,
				'label'    => esc_html__( 'Subtitle Format', 'grimlock' ),
				'settings' => '404_subtitle_format',
				'default'  => $this->get_default( '404_subtitle_format' ),
				'priority' => 10,
				'choices'  => array(
					'display-1' => esc_attr__( 'Heading 1', 'grimlock' ),
					'display-2' => esc_attr__( 'Heading 2', 'grimlock' ),
					'display-3' => esc_attr__( 'Heading 3', 'grimlock' ),
					'display-4' => esc_attr__( 'Heading 4', 'grimlock' ),
					'lead'      => esc_attr__( 'Subheading', 'grimlock' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_subtitle_format_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki textarea field to set the text in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_text_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'textarea',
				'label'             => esc_html__( 'Text', 'grimlock' ),
				'section'           => $this->section,
				'settings'          => '404_text',
				'default'           => $this->get_default( '404_text' ),
				'sanitize_callback' => 'wp_kses_post',
				'priority'          => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_text_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the display for the text paragraphs in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_text_wpautoped_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Automatically add paragraphs' ),
				'settings' => '404_text_wpautoped',
				'default'  => $this->get_default( '404_text_wpautoped' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_text_wpautoped_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_text_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_text_color_elements', array(
				'.grimlock-404 .section__text',
			) );

			$outputs = apply_filters( 'grimlock_404_customizer_text_color_outputs', array(
				array(
					'element'  => $elements,
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_text_color',
				'default'   => $this->get_default( '404_text_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( '404_text_color' ),
				),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_text_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the display for the search form in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_search_form_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display search form', 'grimlock' ),
				'settings' => '404_search_form_displayed',
				'default'  => $this->get_default( '404_search_form_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_search_form_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'radio-image',
				'section'   => $this->section,
				'label'     => esc_html__( 'Layout', 'grimlock' ),
				'settings'  => '404_layout',
				'default'   => $this->get_default( '404_layout' ),
				'priority'  => 10,
				'choices'   => array(
					'12-cols-left'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-left.png',
					'12-cols-center'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-center.png',
					'12-cols-right'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-right.png',
					'6-6-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left.png',
					'6-6-cols-left-reverse'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-reverse.png',
					'4-8-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-4-8-cols-left.png',
					'4-8-cols-left-reverse'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-4-8-cols-left-reverse.png',
					'6-6-cols-left-modern'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-modern.png',
					'6-6-cols-left-reverse-modern' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-reverse-modern.png',
					'8-4-cols-left-modern'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-8-4-cols-left-modern.png',
					'8-4-cols-left-reverse-modern' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-8-4-cols-left-reverse-modern.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_layout_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to activate the full screen mode in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_full_screen_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Activate full screen mode', 'grimlock' ),
				'settings' => '404_full_screen_displayed',
				'default'  => $this->get_default( '404_full_screen_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_full_screen_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki text field to set the button label in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_text_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_button_text_elements', array(
				'.grimlock-404 .section__btn',
				'.grimlock-404 .section__content [type="submit"]',
				'.grimlock-404 .section__content [type="button"]',
			) );

			$args = wp_parse_args( $args, array(
				'type'              => 'text',
				'label'             => esc_html__( 'Button Text', 'grimlock' ),
				'section'           => $this->section,
				'settings'          => '404_button_text',
				'default'           => $this->get_default( '404_button_text' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
				'transport'         => 'postMessage',
				'js_vars'           => array(
					array(
						'function'  => 'html',
						'element'   => implode( ',', $elements ),
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_text_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki text field to set the button link in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_link_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'text',
				'label'    => esc_html__( 'Button Link', 'grimlock' ),
				'section'  => $this->section,
				'settings' => '404_button_link',
				'default'  => $this->get_default( '404_button_link' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_link_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the button link should open in a new page.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_target_blank_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Open link in a new page', 'grimlock' ),
				'settings' => '404_button_target_blank',
				'default'  => $this->get_default( '404_button_target_blank' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_target_blank_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to set the button display in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display button', 'grimlock' ),
				'settings' => '404_button_displayed',
				'default'  => $this->get_default( '404_button_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the button_background color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_button_background_color_elements', array(
				'.grimlock-404 .section__btn',
				'.grimlock-404 .section__content [type="submit"]',
				'.grimlock-404 .section__content [type="button"]',
			) );

			$outputs = apply_filters( 'grimlock_404_customizer_button_background_color_outputs', array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
				$this->get_css_var_output( '404_button_background_color' ),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Button Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_button_background_color',
				'default'   => $this->get_default( '404_button_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the button color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_button_color_elements', array(
				'.grimlock-404 .section__btn',
				'.grimlock-404 .section__content [type="submit"]',
				'.grimlock-404 .section__content [type="button"]',
			) );

			$outputs = apply_filters( 'grimlock_404_customizer_button_color_outputs', array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
				$this->get_css_var_output( '404_button_color' ),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Button Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_button_color',
				'default'   => $this->get_default( '404_button_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the button_border color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_button_color_elements', array(
				'.grimlock-404 .section__btn',
				'.grimlock-404 .section__content [type="submit"]',
				'.grimlock-404 .section__content [type="button"]',
			) );

			$outputs = apply_filters( 'grimlock_404_customizer_button_color_outputs', array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
				$this->get_css_var_output( '404_button_border_color' ),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Button Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_button_border_color',
				'default'   => $this->get_default( '404_button_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the button_hover_background color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_hover_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_button_hover_background_color_elements', array(
				'.grimlock-404 .section__btn:hover',
				'.grimlock-404 .section__btn:focus',
				'.grimlock-404 .section__content [type="submit"]:hover',
				'.grimlock-404 .section__content [type="submit"]:focus',
				'.grimlock-404 .section__content [type="button"]:hover',
				'.grimlock-404 .section__content [type="button"]:focus',
			) );

			$outputs = apply_filters( 'grimlock_404_customizer_button_hover_background_color_outputs', array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
				$this->get_css_var_output( '404_button_hover_background_color' ),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Button Background Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_button_hover_background_color',
				'default'   => $this->get_default( '404_button_hover_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_hover_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the button hover color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_button_hover_color_elements', array(
				'.grimlock-404 .section__btn:hover',
				'.grimlock-404 .section__btn:focus',
				'.grimlock-404 .section__content [type="submit"]:hover',
				'.grimlock-404 .section__content [type="submit"]:focus',
				'.grimlock-404 .section__content [type="button"]:hover',
				'.grimlock-404 .section__content [type="button"]:focus',
			) );

			$outputs = apply_filters( 'grimlock_404_customizer_button_hover_color_outputs', array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
				$this->get_css_var_output( '404_button_hover_color' ),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Button Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_button_hover_color',
				'default'   => $this->get_default( '404_button_hover_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_hover_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the button_hover_border color in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_hover_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_404_customizer_button_hover_border_color_elements', array(
				'.grimlock-404 .section__btn:hover',
				'.grimlock-404 .section__btn:focus',
				'.grimlock-404 .section__content [type="submit"]:hover',
				'.grimlock-404 .section__content [type="submit"]:focus',
				'.grimlock-404 .section__content [type="button"]:hover',
				'.grimlock-404 .section__content [type="button"]:focus',
			) );

			$outputs = apply_filters( 'grimlock_404_customizer_button_hover_border_color_outputs', array(
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
				$this->get_css_var_output( '404_button_hover_border_color' ),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Button Border Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => '404_button_hover_border_color',
				'default'   => $this->get_default( '404_button_hover_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_hover_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the button size in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_button_size_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'select',
				'section'  => $this->section,
				'label'    => esc_html__( 'Button Size', 'grimlock' ),
				'settings' => '404_button_size',
				'default'  => $this->get_default( '404_button_size' ),
				'priority' => 10,
				'choices'  => array(
					'btn-sm' => esc_attr__( 'Small', 'grimlock' ),
					' '      => esc_attr__( 'Regular', 'grimlock' ),
					'btn-lg' => esc_attr__( 'Large', 'grimlock' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_404_customizer_button_size_field_args', $args ) );
		}
	}

	/**
	 * Add edit shortcut (blue pen)
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	public function add_partial( $wp_customize ) {
		$wp_customize->selective_refresh->add_partial( '404_partial', array(
				'selector' => '.grimlock-404 .region__col--2',
				'settings' => array( '404_title' ),
			)
		);
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @since 1.0.6
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock' ),
				'class' => 'header_image-general-tab',
				'controls' => array(
					'404_thumbnail',
					"{$this->section}_divider_20",
					'404_title',
					"{$this->section}_divider_30",
					'404_subtitle',
					"{$this->section}_divider_40",
					'404_text',
					'404_text_wpautoped',
					"{$this->section}_divider_60",
					'404_search_form_displayed',
					"{$this->section}_divider_70",
					'404_button_text',
					'404_button_link',
					'404_button_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock' ),
				'class' => 'header_image-layout-tab',
				'controls' => array(
					'404_layout',
					"{$this->section}_divider_110",
					'404_container_layout',
					"{$this->section}_divider_120",
					"{$this->section}_heading_120",
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock' ),
				'class' => 'header_image-style-tab',
				'controls' => array(
					'404_background_image',
					"{$this->section}_divider_210",
					'404_padding_y',
					'404_full_screen_displayed',
					"{$this->section}_divider_230",
					'404_background_color',
					"{$this->section}_divider_240",
					'404_border_top_color',
					'404_border_top_width',
					"{$this->section}_divider_260",
					'404_border_bottom_color',
					'404_border_bottom_width',
					"{$this->section}_divider_280",
					'404_title_format',
					'404_title_color',
					"{$this->section}_divider_300",
					'404_subtitle_format',
					'404_subtitle_color',
					"{$this->section}_divider_320",
					'404_text_color',
					"{$this->section}_divider_330",
					'404_button_background_color',
					'404_button_color',
					'404_button_border_color',
					"{$this->section}_divider_360",
					'404_button_hover_background_color',
					'404_button_hover_color',
					'404_button_hover_border_color',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add arguments using theme mods to customize the region component.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args The default arguments to render the region.
	 *
	 * @return array      The arguments to render the region.
	 */
	public function add_args( $args ) {
		$args                          = parent::add_args( $args );

		$args['full_screen_displayed'] = $this->get_theme_mod( '404_full_screen_displayed' );

		$background_image_url          = $this->get_theme_mod( '404_background_image' );
		$attachment_id                 = attachment_url_to_postid( $this->get_theme_mod( '404_background_image' ) );
		if ( ! empty( $attachment_id ) ) {
			$size                      = apply_filters( "grimlock_{$this->id}_customizer_custom_header_size", 'custom-header', $this->get_theme_mod( '404_layout' ) );
			$background_image_url      = wp_get_attachment_image_url( $attachment_id, $size );
		}
		$args['background_image']      = $background_image_url;

		$args['title_displayed']       = '' !== $this->get_theme_mod( '404_title' );
		$args['title']                 = $this->get_theme_mod( '404_title' );
		$args['title_color']           = $this->get_theme_mod( '404_title_color' );
		$args['title_format']          = $this->get_theme_mod( '404_title_format' );

		$args['subtitle_displayed']    = '' !== $this->get_theme_mod( '404_subtitle' );
		$args['subtitle']              = $this->get_theme_mod( '404_subtitle' );
		$args['subtitle_color']        = $this->get_theme_mod( '404_subtitle_color' );
		$args['subtitle_format']       = $this->get_theme_mod( '404_subtitle_format' );

		$args['text_displayed']        = '' !== $this->get_theme_mod( '404_text' );
		$args['text']                  = $this->get_theme_mod( '404_text' );
		$args['color']                 = $this->get_theme_mod( '404_text_color' );
		$args['text_wpautoped']        = $this->get_theme_mod( '404_text_wpautoped' );
		$args['thumbnail']             = $this->get_theme_mod( '404_thumbnail' );

		$args['search_form_displayed'] = $this->get_theme_mod( '404_search_form_displayed' );

		$args['button_displayed']      = $this->get_theme_mod( '404_button_displayed' );
		$args['button_text']           = $this->get_theme_mod( '404_button_text' );
		$args['button_link']           = $this->get_theme_mod( '404_button_link' );

		return $args;
	}

	/**
	 * Check whether Custom Header has to be displayed.
	 *
	 * @since 1.0.7
	 *
	 * @return bool True when Custom Header has to be displayed, false otherwise.
	 */
	public function has_custom_header_displayed( $default ) {
		return ! is_404() && $default;
	}

	/**
	 * Change the region arguments to remove it from the 404 page.
	 *
	 * @since 1.0.7
	 *
	 * @param  array $args The array of arguments for the region.
	 *
	 * @return array       The updated array of arguments for the region.
	 */
	public function change_args( $args ) {
		$args['displayed'] = ! is_404();
		return $args;
	}
}

return new Grimlock_404_Customizer();
