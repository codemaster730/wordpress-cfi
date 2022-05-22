<?php
/**
 * Grimlock_Custom_Header_Customizer Class
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
 * The Grimlock Customizer class.
 */
class Grimlock_Custom_Header_Customizer extends Grimlock_Region_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'custom_header';
		$this->title   = esc_html__( 'Header', 'grimlock' );
		$this->section = 'header_image';

		add_action( 'after_setup_theme',                       array( $this, 'add_customizer_fields'                   ), 20    );
		add_action( 'customize_register',                      array( $this, 'customize_register'                      ), 20, 1 );
		add_action( 'customize_controls_print_footer_scripts', array( $this, 'customize_controls_print_footer_scripts' ), 20    );

		add_filter( 'grimlock_customizer_controls_js_data',    array( $this, 'add_customizer_controls_js_data'         ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',             array( $this, 'add_args'                                ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',              array( $this, 'add_dynamic_css'                         ), 10, 1 );
	}

	/**
	 * Add settings and custom controls for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @since 1.0.0
	 */
	public function customize_register( $wp_customize ) {
		$wp_customize->get_section( $this->section )->panel    = 'grimlock_appearance_customizer_panel';
		$wp_customize->get_section( $this->section )->title    = $this->title;
		$wp_customize->get_section( $this->section )->priority = 60;
	}

	/**
	 * Improve interactions between added and native controls.
	 *
	 * @since 1.0.0
	 */
	public function customize_controls_print_footer_scripts() {
		?>
		<script type="text/javascript">
            jQuery(document).ready(function ($) {
                var control_selector       = $('#customize-control-custom_header_padding_y');
                var control_input_selector = $('#customize-control-custom_header_padding_y .wrapper > input');

                control_input_selector.prop('disabled', true).addClass('disabled');
                control_selector.addClass('customize-control--disabled');
            });
		</script>
		<?php
	}

	/**
	 * Add arguments using theme mods to customize the region component.
	 *
	 * @param array $args The default arguments to render the region.
	 *
	 * @return array      The arguments to render the region.
	 */
	public function add_args( $args ) {
		$args                    = parent::add_args( $args );

		$args['title_color']     = $this->get_theme_mod( 'custom_header_title_color' );
		$args['title_format']    = $this->get_theme_mod( 'custom_header_title_format' );

		$args['subtitle_color']  = $this->get_theme_mod( 'custom_header_subtitle_color' );
		$args['subtitle_format'] = $this->get_theme_mod( 'custom_header_subtitle_format' );

		return $args;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
	    $this->defaults = apply_filters( 'grimlock_custom_header_customizer_defaults', array(
		    'custom_header_padding_y'                        => GRIMLOCK_SECTION_PADDING_Y,
		    'custom_header_background_color'                 => GRIMLOCK_SECTION_BACKGROUND_COLOR,

		    'custom_header_background_gradient_displayed'    => false,
		    'custom_header_background_gradient_first_color'  => 'rgba(0,0,0,0)',
		    'custom_header_background_gradient_second_color' => 'rgba(0,0,0,.35)',
		    'custom_header_background_gradient_direction'    => '0deg',
		    'custom_header_background_gradient_position'     => '0', // %

		    'custom_header_border_top_color'                 => GRIMLOCK_BORDER_COLOR,
		    'custom_header_border_top_width'                 => 0, // px
		    'custom_header_border_bottom_color'              => GRIMLOCK_BORDER_COLOR,
		    'custom_header_border_bottom_width'              => 0, // px

		    'custom_header_title_format'                     => 'display-2',
		    'custom_header_title_color'                      => GRIMLOCK_BODY_COLOR,

		    'custom_header_subtitle_format'                  => 'lead',
		    'custom_header_subtitle_color'                   => GRIMLOCK_BODY_COLOR,

		    'custom_header_link_color'                       => GRIMLOCK_LINK_COLOR,
		    'custom_header_link_hover_color'                 => GRIMLOCK_LINK_HOVER_COLOR,

		    'custom_header_layout'                           => '12-cols-left',
		    'custom_header_container_layout'                 => 'classic',
		    'custom_header_mobile_displayed'                 => true,
	    ) );

		$allowed_html = array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		);

		$this->add_layout_field(                           array( 'priority' => 100, 'description' => wp_kses( __( 'You can change the header layout through the following panels : <a href="#archive_custom_header_layout" rel="tc-control">Posts Page</a>, <a href="#search_custom_header_layout" rel="tc-control">Search Page</a>, <a href="#single_custom_header_layout" rel="tc-control">Single Post</a>, <a href="#page_custom_header_layout" rel="tc-control">Single Page</a>', 'grimlock' ), $allowed_html ) ) );
		$this->add_divider_field(                          array( 'priority' => 110 ) );
		$this->add_container_layout_field(                 array( 'priority' => 110, 'description' => wp_kses( __( 'You can change the header spread through the following panels : <a href="#archive_custom_header_container_layout" rel="tc-control">Posts Page</a>, <a href="#search_custom_header_container_layout" rel="tc-control">Search Page</a>, <a href="#single_custom_header_container_layout" rel="tc-control">Single Post</a>, <a href="#page_custom_header_container_layout" rel="tc-control">Single Page</a>', 'grimlock' ), $allowed_html ) ) );
		$this->add_divider_field(                          array( 'priority' => 120 ) );
		$this->add_heading_field(                          array( 'priority' => 120, 'label'    => esc_html__( 'Mobile Display', 'grimlock' ) ) );
		$this->add_mobile_displayed_field(                 array( 'priority' => 120 ) );

		$this->add_padding_y_field(                        array( 'priority' => 200, 'description' => wp_kses( __( 'You can change the vertical padding through the following panels : <a href="#archive_custom_header_padding_y" rel="tc-control">Posts Page</a>, <a href="#search_custom_header_padding_y" rel="tc-control">Search Page</a>, <a href="#single_custom_header_padding_y" rel="tc-control">Single Post</a>, <a href="#page_custom_header_padding_y" rel="tc-control">Single Page</a>', 'grimlock' ), $allowed_html ) ) );
		$this->add_divider_field(                          array( 'priority' => 210 ) );
		$this->add_background_color_field(                 array( 'priority' => 210 ) );
		$this->add_background_gradient_displayed_field(    array( 'priority' => 230 ) );
		$this->add_background_gradient_first_color_field(  array( 'priority' => 240 ) );
		$this->add_background_gradient_second_color_field( array( 'priority' => 250 ) );
		$this->add_background_gradient_direction_field(    array( 'priority' => 260 ) );
		$this->add_background_gradient_position_field(     array( 'priority' => 270 ) );
		$this->add_divider_field(                          array( 'priority' => 280 ) );
		$this->add_border_top_width_field(                 array( 'priority' => 280 ) );
		$this->add_border_top_color_field(                 array( 'priority' => 290 ) );
		$this->add_divider_field(                          array( 'priority' => 300 ) );
		$this->add_border_bottom_width_field(              array( 'priority' => 300 ) );
		$this->add_border_bottom_color_field(              array( 'priority' => 310 ) );
		$this->add_divider_field(                          array( 'priority' => 320 ) );
		$this->add_title_format_field(                     array( 'priority' => 320 ) );
		$this->add_title_color_field(                      array( 'priority' => 330 ) );
		$this->add_divider_field(                          array( 'priority' => 340 ) );
		$this->add_subtitle_format_field(                  array( 'priority' => 340 ) );
		$this->add_subtitle_color_field(                   array( 'priority' => 350 ) );
		$this->add_divider_field(                          array( 'priority' => 360 ) );
		$this->add_link_color_field(                       array( 'priority' => 360 ) );
		$this->add_link_hover_color_field(                 array( 'priority' => 370 ) );
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
				'label' => esc_html__( 'General', 'grimlock' ),
				'class' => 'header_image-general-tab',
				'controls' => array(
					'header_image',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock' ),
				'class' => 'header_image-layout-tab',
				'controls' => array(
					'custom_header_layout',
					"{$this->section}_divider_110",
					'custom_header_container_layout',
					"{$this->section}_divider_120",
					"{$this->section}_heading_120",
					'custom_header_mobile_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock' ),
				'class' => 'header_image-style-tab',
				'controls' => array(
					'custom_header_padding_y',
					"{$this->section}_divider_210",
					'custom_header_background_color',
					'custom_header_background_gradient_displayed',
					'custom_header_background_gradient_first_color',
					'custom_header_background_gradient_second_color',
					'custom_header_background_gradient_direction',
					'custom_header_background_gradient_position',
					"{$this->section}_divider_280",
					'custom_header_border_top_color',
					'custom_header_border_top_width',
					"{$this->section}_divider_300",
					'custom_header_border_bottom_color',
					'custom_header_border_bottom_width',
					"{$this->section}_divider_320",
					'custom_header_title_format',
					'custom_header_title_color',
					"{$this->section}_divider_340",
					'custom_header_subtitle_format',
					'custom_header_subtitle_color',
					"{$this->section}_divider_360",
					'custom_header_link_color',
					'custom_header_link_hover_color',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki color field to set the title color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_title_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_custom_header_customizer_title_color_elements', array(
				'.grimlock-custom_header .section__title',
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Title Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'custom_header_title_color',
				'default'   => $this->get_default( 'custom_header_title_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( 'custom_header_title_color' ),
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

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_title_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the title format in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_title_format_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'select',
				'section'  => $this->section,
				'label'    => esc_html__( 'Title Format', 'grimlock' ),
				'settings' => 'custom_header_title_format',
				'default'  => $this->get_default( 'custom_header_title_format' ),
				'priority' => 10,
				'choices'  => array(
					'display-1' => esc_attr__( 'Heading 1', 'grimlock' ),
					'display-2' => esc_attr__( 'Heading 2', 'grimlock' ),
					'display-3' => esc_attr__( 'Heading 3', 'grimlock' ),
					'display-4' => esc_attr__( 'Heading 4', 'grimlock' ),
					'lead'      => esc_attr__( 'Subheading', 'grimlock' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_title_format_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the subtitle color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_subtitle_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_custom_header_customizer_subtitle_color_elements', array(
				'.grimlock-custom_header .section__subtitle',
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Subtitle Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'custom_header_subtitle_color',
				'default'   => $this->get_default( 'custom_header_subtitle_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array(
					$this->get_css_var_output( 'custom_header_subtitle_color' ),
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

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_subtitle_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the subtitle format in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_subtitle_format_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'select',
				'section'  => $this->section,
				'label'    => esc_html__( 'Subtitle Format', 'grimlock' ),
				'settings' => 'custom_header_subtitle_format',
				'default'  => $this->get_default( 'custom_header_subtitle_format' ),
				'priority' => 10,
				'choices'  => array(
					'display-1' => esc_attr__( 'Heading 1', 'grimlock' ),
					'display-2' => esc_attr__( 'Heading 2', 'grimlock' ),
					'display-3' => esc_attr__( 'Heading 3', 'grimlock' ),
					'display-4' => esc_attr__( 'Heading 4', 'grimlock' ),
					'lead'      => esc_attr__( 'Subheading', 'grimlock' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_subtitle_format_field_args', $args ) );
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
				'type'      => 'radio-image',
				'section'   => $this->section,
				'label'     => esc_html__( 'Layout', 'grimlock' ),
				'settings'  => 'custom_header_layout',
				'default'   => $this->get_default( 'custom_header_layout' ),
				'priority'  => 10,
				'choices'   => array(
					'12-cols-left'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-12-cols-left.png',
					'12-cols-center'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-12-cols-center.png',
					'12-cols-right'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-12-cols-right.png',
					'6-6-cols-left-reverse'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left-reverse.png',
					'6-6-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left.png',
					'6-6-cols-left-reverse-modern' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left-reverse-modern.png',
					'6-6-cols-left-modern'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/custom_header-6-6-cols-left-modern.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_layout_field_args', $args ) );
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
				'settings' => 'custom_header_background_gradient_displayed',
				'default'  => $this->get_default( 'custom_header_background_gradient_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_background_gradient_displayed_field_args', $args ) );
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
				'settings'  => 'custom_header_background_gradient_first_color',
				'default'   => $this->get_default( 'custom_header_background_gradient_first_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'custom_header_background_gradient_first_color' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'custom_header_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_background_gradient_first_color_field_args', $args ) );
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
				'settings'  => 'custom_header_background_gradient_second_color',
				'default'   => $this->get_default( 'custom_header_background_gradient_second_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'custom_header_background_gradient_second_color' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'custom_header_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_background_gradient_second_color_field_args', $args ) );
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
				'settings' => 'custom_header_background_gradient_direction',
				'default'  => $this->get_default( 'custom_header_background_gradient_direction' ),
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
					$this->get_css_var_output( 'custom_header_background_gradient_direction' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'custom_header_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_background_gradient_direction_field_args', $args ) );
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
				'settings'  => 'custom_header_background_gradient_position',
				'default'   => $this->get_default( 'custom_header_background_gradient_position' ),
				'choices'   => array(
					'min'   => -100,
					'max'   => 100,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array(
					$this->get_css_var_output( 'custom_header_background_gradient_position', '%' ),
				),
				'active_callback' => array(
					array(
						'setting'  => 'custom_header_background_gradient_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_custom_header_customizer_background_gradient_position_field_args', $args ) );
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
		$background_gradient_displayed = $this->get_theme_mod( 'custom_header_background_gradient_displayed' );

		if ( ! empty( $background_gradient_displayed ) ) {
			$background_gradient_first_color  = $this->get_theme_mod( 'custom_header_background_gradient_first_color' );
			$background_gradient_second_color = $this->get_theme_mod( 'custom_header_background_gradient_second_color' );
			$background_gradient_direction    = $this->get_theme_mod( 'custom_header_background_gradient_direction' );
			$background_gradient_position     = $this->get_theme_mod( 'custom_header_background_gradient_position' );

			$styles .= "
			.grimlock-custom_header > .region__inner {
				background: linear-gradient({$background_gradient_direction}, {$background_gradient_second_color} {$background_gradient_position}%, {$background_gradient_first_color} 100%)
			}";

		}

		return $styles;
	}
}

return new Grimlock_Custom_Header_Customizer();
