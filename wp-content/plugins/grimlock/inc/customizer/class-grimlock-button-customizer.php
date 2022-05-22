<?php
/**
 * Grimlock_Button_Customizer Class
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
class Grimlock_Button_Customizer extends Grimlock_Base_Customizer {
	/**
	 * @var array The array of elements to target the buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_elements;

	/**
	 * @var array The array of elements to target the large buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_lg_elements;

	/**
	 * @var array The array of elements to target the small buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_sm_elements;

	/**
	 * @var array The array of elements to target the extra-small buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_xs_elements;

	/**
	 * @var array The array of elements to target the primary buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_primary_elements;

	/**
	 * @var array The array of elements to target the disabled primary buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_primary_disabled_elements;

	/**
	 * @var array The array of elements to target the hovered primary buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_primary_hover_elements;

	/**
	 * @var array The array of elements to target the hovered primary outline buttons in theme.
	 * @since 1.0.0
	 */
	protected $card_primary_elements;

	/**
	 * @var array The array of elements to target the secondary buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_secondary_elements;

	/**
	 * @var array The array of elements to target the hovered secondary buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_secondary_hover_elements;

	/**
	 * @var array The array of elements to target the disabled secondary buttons in theme.
	 * @since 1.0.0
	 */
	protected $button_secondary_disabled_elements;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_button_customizer_section';
		$this->title   = esc_html__( 'Buttons', 'grimlock' );

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );

		add_action( 'after_setup_theme',                    array( $this, 'add_editor_color_palette'        ), 100   );

		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',           array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_button_customizer_defaults', array(
			'button_font'                             => array(
				'font-family'                         => GRIMLOCK_FONT_FAMILY_SANS_SERIF,
				'font-weight'                         => 'regular',
				'font-size'                           => GRIMLOCK_FONT_SIZE,
				'line-height'                         => '1.25rem',
				'letter-spacing'                      => GRIMLOCK_LETTER_SPACING,
				'subsets'                             => array( 'latin-ext' ),
				'text-transform'                      => 'none',
			),
			'button_border_radius'                    => GRIMLOCK_BORDER_RADIUS,
			'button_border_width'                     => GRIMLOCK_BORDER_WIDTH,
			'button_padding_y'                        => .5, // rem
			'button_padding_x'                        => 1, // rem

			'button_primary_background_color'         => GRIMLOCK_BUTTON_PRIMARY_BACKGROUND_COLOR,
			'button_primary_color'                    => GRIMLOCK_BUTTON_PRIMARY_COLOR,
			'button_primary_border_color'             => GRIMLOCK_BUTTON_PRIMARY_BORDER_COLOR,
			'button_primary_hover_background_color'   => GRIMLOCK_BUTTON_PRIMARY_HOVER_BACKGROUND_COLOR,
			'button_primary_hover_color'              => GRIMLOCK_BUTTON_PRIMARY_HOVER_COLOR,
			'button_primary_hover_border_color'       => GRIMLOCK_BUTTON_PRIMARY_HOVER_BORDER_COLOR,

			'button_secondary_background_color'       => '#ffffff',
			'button_secondary_color'                  => GRIMLOCK_BODY_COLOR,
			'button_secondary_border_color'           => '#cccccc',
			'button_secondary_hover_background_color' => '#e6e6e6',
			'button_secondary_hover_color'            => GRIMLOCK_BODY_COLOR,
			'button_secondary_hover_border_color'     => '#adadad',
		) );

		$this->button_elements = apply_filters( 'grimlock_button_customizer_elements', array(
			'.btn',
			'button',
			'input[type="button"]',
			'input[type="submit"]',
			'button[type="submit"]',
		) );

		$this->button_lg_elements = apply_filters( 'grimlock_button_customizer_lg_elements', array(
			'.btn.btn-lg:not(.btn-link)',
			'.grimlock-section--btn-lg input[type="button"]',
			'.grimlock-section--btn-lg input[type="submit"]',
			'.grimlock-section--btn-lg button[type="submit"]',
		) );

		$this->button_sm_elements = apply_filters( 'grimlock_button_customizer_sm_elements', array(
			'.btn.btn-sm:not(.btn-link)',
			'.grimlock-section--btn-sm input[type="button"]',
			'.grimlock-section--btn-sm input[type="submit"]',
			'.grimlock-section--btn-sm button[type="submit"]',
		) );

		$this->button_xs_elements = apply_filters( 'grimlock_button_customizer_xs_elements', array(
			'.btn.btn-xs:not(.btn-link)',
		    '.grimlock-section--btn-xs input[type="button"]',
			'.grimlock-section--btn-xs input[type="submit"]',
			'.grimlock-section--btn-xs button[type="submit"]',
		) );

		$this->button_primary_elements = apply_filters( 'grimlock_button_customizer_primary_elements', array(
			'.btn-primary',
			'input[type="button"]',
			'input[type="submit"]',
			'button[type="submit"]',
		) );

		$this->button_primary_hover_elements = array(
			'.btn-primary.active',
			'.btn-primary.active:hover',
			'.btn-primary.active:focus',
			'.btn-primary.active:active',
		);

		foreach( $this->button_primary_elements as $element ) {
			$this->button_primary_hover_elements[] = "$element:hover";
			$this->button_primary_hover_elements[] = "$element:focus";
			$this->button_primary_hover_elements[] = "$element:active";
			$this->button_primary_hover_elements[] = "$element:disabled";
		}
		$this->button_primary_hover_elements = apply_filters( 'grimlock_button_customizer_primary_hover_elements', $this->button_primary_hover_elements );

		$this->button_primary_disabled_elements = apply_filters( 'grimlock_button_customizer_primary_disabled_elements', array(
			'.btn-primary:disabled',
			'.btn-primary.disabled',
		) );

		$this->card_primary_elements = apply_filters( 'grimlock_button_customizer_card_primary_elements', array(
			'.btn-outline-primary:hover',
			'.btn-outline-primary:active',
			'.btn-outline-primary:focus',
			'html body #site-wrapper .badge-primary',
			'html body #site-wrapper a.badge-primary[href]',
			'html body #site-wrapper a.badge-primary[href]:hover',
			'.list-group-item.active',
			'.list-group-item.active:focus',
			'.list-group-item.active:hover',
			'a.list-group-item:hover',
			'a.list-group-item:focus',
			'a.list-group-item.active',
			'a.list-group-item.active:focus',
			'a.list-group-item.active:hover',
			'.nav a.nav-link:hover',
			'.nav a.nav-link:active',
			'.nav a.nav-link:focus',
			'.nav a.nav-link.active',
			'.nav a.nav-link.active:hover',
			'.nav a.nav-link.active:active',
			'.nav a.nav-link.active:focus',
			'.nav .nav-item.open .nav-link',
			'.nav .nav-item.open .nav-link:focus',
			'.nav .nav-item.open .nav-link:hover',
		) );

		$this->button_secondary_elements = apply_filters( 'grimlock_button_customizer_secondary_elements', array(
			'.btn-secondary',
			'.button',
			'a.list-group-item',
			'button.list-group-item',
			'input[type="submit"].btn-secondary',
			'input[type="button"].btn-secondary',
			'button[type="submit"].btn-secondary',
			'.grimlock-section--btn-secondary input[type="submit"]',
			'.grimlock-section--btn-secondary input[type="button"]',
			'.grimlock-section--btn-secondary button[type="submit"]',
		) );

		$this->button_secondary_hover_elements = array(
			'.btn-secondary.active',
		);

		foreach( $this->button_secondary_elements as $element ) {
			$this->button_secondary_hover_elements[] = "$element:hover";
			$this->button_secondary_hover_elements[] = "$element:focus";
			$this->button_secondary_hover_elements[] = "$element:active";
		}
		$this->button_secondary_hover_elements = apply_filters( 'grimlock_button_customizer_secondary_hover_elements', $this->button_secondary_hover_elements );

		$this->button_secondary_disabled_elements = apply_filters( 'grimlock_button_customizer_secondary_disabled_elements', array(
			'.btn-secondary:disabled',
			'.btn-secondary.disabled',
		) );

		$this->add_section(                                       array( 'priority' => 40 ) );

		$this->add_button_font_field(                             array( 'priority' => 10  ) );
		$this->add_divider_field(                                 array( 'priority' => 20  ) );
		$this->add_button_border_radius_field(                    array( 'priority' => 20  ) );
		$this->add_button_border_width_field(                     array( 'priority' => 30  ) );
		$this->add_divider_field(                                 array( 'priority' => 40  ) );
		$this->add_button_padding_y_field(                        array( 'priority' => 40  ) );
		$this->add_button_padding_x_field(                        array( 'priority' => 50  ) );

		$this->add_button_primary_background_color_field(         array( 'priority' => 100 ) );
		$this->add_button_primary_color_field(                    array( 'priority' => 110 ) );
		$this->add_button_primary_border_color_field(             array( 'priority' => 120 ) );
		$this->add_divider_field(                                 array( 'priority' => 130 ) );
		$this->add_button_primary_hover_background_color_field(   array( 'priority' => 130 ) );
		$this->add_button_primary_hover_color_field(              array( 'priority' => 140 ) );
		$this->add_button_primary_hover_border_color_field(       array( 'priority' => 150 ) );

		$this->add_button_secondary_background_color_field(       array( 'priority' => 200 ) );
		$this->add_button_secondary_color_field(                  array( 'priority' => 210 ) );
		$this->add_button_secondary_border_color_field(           array( 'priority' => 220 ) );
		$this->add_divider_field(                                 array( 'priority' => 230 ) );
		$this->add_button_secondary_hover_background_color_field( array( 'priority' => 230 ) );
		$this->add_button_secondary_hover_color_field(            array( 'priority' => 240 ) );
		$this->add_button_secondary_hover_border_color_field(     array( 'priority' => 250 ) );
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
				'label'    => esc_html__( 'General', 'grimlock' ),
				'class'    => 'button-tab',
				'controls' => array(
					'button_font',
					"{$this->section}_divider_20",
					'button_border_radius',
					'button_border_width',
					"{$this->section}_divider_40",
					'button_padding_y',
					'button_padding_x',
				),
			),
			array(
				'label'    => esc_html__( 'Primary', 'grimlock' ),
				'class'    => 'button-primary-tab',
				'controls' => array(
					'button_primary_background_color',
					'button_primary_color',
					'button_primary_border_color',
					"{$this->section}_divider_130",
					'button_primary_hover_background_color',
					'button_primary_hover_color',
					'button_primary_hover_border_color',
				),
			),
			array(
				'label'    => esc_html__( 'Secondary', 'grimlock' ),
				'class'    => 'button-secondary-tab',
				'controls' => array(
					'button_secondary_background_color',
					'button_secondary_color',
					'button_secondary_border_color',
					"{$this->section}_divider_230",
					'button_secondary_hover_background_color',
					'button_secondary_hover_color',
					'button_secondary_hover_border_color',
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
	protected function add_button_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_button_customizer_font_elements', $this->button_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_font_outputs', array(
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
				'settings'  => 'button_font',
				'label'     => esc_attr__( 'Typography', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'button_font' ),
				'priority'  => 10,
				'transport' => 'refresh',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'button_font' ) ),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border radius in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_border_radius_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_border_radius_elements', array_merge( $this->button_elements, array(
				'.btn-group > .btn',
				'.navbar-search .search-form .search-field',
				'.posts-filter > .control',
			) ) );

			$outputs = apply_filters( 'grimlock_button_customizer_border_radius_outputs', array(
				$this->get_css_var_output( 'button_border_radius', 'rem' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-radius',
					'units'    => 'rem',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Radius', 'grimlock' ),
				'settings'  => 'button_border_radius',
				'default'   => $this->get_default( 'button_border_radius' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => .05,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_border_radius_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border width in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_border_width_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_border_width_elements', $this->button_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_border_width_outputs', array(
				$this->get_css_var_output( 'button_border_width', 'px' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-width',
					'units'    => 'px',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Width', 'grimlock' ),
				'settings'  => 'button_border_width',
				'default'   => $this->get_default( 'button_border_width' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 10,
					'step'  => 1,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_border_width_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the horinzontal padding in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_padding_x_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements    = apply_filters( 'grimlock_button_customizer_padding_x_elements',    $this->button_elements    );
			$lg_elements = apply_filters( 'grimlock_button_customizer_padding_x_lg_elements', $this->button_lg_elements );
			$sm_elements = apply_filters( 'grimlock_button_customizer_padding_x_sm_elements', $this->button_sm_elements );
			$xs_elements = apply_filters( 'grimlock_button_customizer_padding_x_xs_elements', $this->button_xs_elements );

			$outputs = apply_filters( 'grimlock_button_customizer_padding_x_outputs', array(
				$this->get_css_var_output( 'button_padding_x', 'rem' ),
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'padding-left',
					'units'         => 'rem',
				),
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'padding-right',
					'units'         => 'rem',
				),
				array(
					'element'       => implode( ',', $lg_elements ),
					'property'      => 'padding-right',
					'value_pattern' => 'calc($rem * 1.25)',
				),
				array(
					'element'       => implode( ',', $lg_elements ),
					'property'      => 'padding-left',
					'value_pattern' => 'calc($rem * 1.25)',
				),
				array(
					'element'       => implode( ',', $sm_elements ),
					'property'      => 'padding-right',
					'value_pattern' => 'calc($rem * 0.6)',
				),
				array(
					'element'       => implode( ',', $sm_elements ),
					'property'      => 'padding-left',
					'value_pattern' => 'calc($rem * 0.6)',
				),
				array(
					'element'       => implode( ',', $xs_elements ),
					'property'      => 'padding-right',
					'value_pattern' => 'calc($rem * 0.3)',
				),
				array(
					'element'       => implode( ',', $xs_elements ),
					'property'      => 'padding-left',
					'value_pattern' => 'calc($rem * 0.3)',
				),
			), $elements, $lg_elements, $sm_elements, $xs_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Horizontal Padding', 'grimlock' ),
				'settings'  => 'button_padding_x',
				'default'   => $this->get_default( 'button_padding_x' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 5,
					'step'  => .25,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_padding_x_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the vertical padding in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_padding_y_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_padding_y_elements', array_merge( $this->button_elements, array(
				'input.form-control-lg',
				'select.form-control-lg',
			) ) );

			$lg_elements = apply_filters( 'grimlock_button_customizer_padding_y_lg_elements', $this->button_lg_elements );

			$sm_elements = apply_filters( 'grimlock_button_customizer_padding_y_sm_elements', array_merge( $this->button_sm_elements, array(
				'input.form-control-sm',
				'select.form-control-sm',
			) ) );

			$xs_elements = apply_filters( 'grimlock_button_customizer_padding_y_xs_elements', array_merge( $this->button_xs_elements, array(
				'input.form-control-sm',
				'select.form-control-sm',
			) ) );

			$outputs = apply_filters( 'grimlock_button_customizer_padding_y_outputs', array(
				$this->get_css_var_output( 'button_padding_y', 'rem' ),
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'padding-top',
					'units'         => 'rem',
				),
				array(
					'element'       =>  implode( ',', $elements ),
					'property'      => 'padding-bottom',
					'units'         => 'rem',
				),
				array(
					'element'       => implode( ',', $lg_elements ),
					'property'      => 'padding-top',
					'value_pattern' => 'calc($rem * 1.25)',
				),
				array(
					'element'       => implode( ',', $lg_elements ),
					'property'      => 'padding-bottom',
					'value_pattern' => 'calc($rem * 1.25)',
				),
				array(
					'element'       => implode( ',', $sm_elements ),
					'property'      => 'padding-top',
					'value_pattern' => 'calc($rem * 0.6)',
				),
				array(
					'element'       => implode( ',', $sm_elements ),
					'property'      => 'padding-bottom',
					'value_pattern' => 'calc($rem * 0.6)',
				),
				array(
					'element'       => implode( ',', $xs_elements ),
					'property'      => 'padding-top',
					'value_pattern' => 'calc($rem * 0.3)',
				),
				array(
					'element'       => implode( ',', $xs_elements ),
					'property'      => 'padding-bottom',
					'value_pattern' => 'calc($rem * 0.3)',
				),
			), $elements, $lg_elements, $sm_elements, $xs_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Vertical Padding', 'grimlock' ),
				'settings'  => 'button_padding_y',
				'default'   => $this->get_default( 'button_padding_y' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 5,
					'step'  => .25,
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_padding_y_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_primary_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_primary_border_color_elements', array_merge( $this->button_primary_elements, array(
				'.btn-outline-primary:hover',
				'.btn-outline-primary:active',
				'.btn-outline-primary:focus',
			) ) );

			$disabled_elements = apply_filters( 'grimlock_button_customizer_primary_border_color_disabled_elements', $this->button_primary_disabled_elements );

			$outputs = apply_filters( 'grimlock_button_customizer_primary_border_color_outputs', array(
				$this->get_css_var_output( 'button_primary_border_color' ),
				array(
					'element'  => implode( ',',$elements ),
					'property' => 'border-color',
				),
				array(
					'element'  => implode( ',', $disabled_elements ),
					'property' => 'border-color',
					'suffix'   => '!important',
				),
			), $elements, $disabled_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_primary_border_color',
				'default'   => $this->get_default( 'button_primary_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_primary_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_primary_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_primary_background_color_elements', array_merge(
				$this->button_primary_elements,
				$this->card_primary_elements
			) );

			$disabled_elements = apply_filters( 'grimlock_button_customizer_primary_color_disabled_elements', array_merge( $this->button_primary_disabled_elements, array(
				'.bg-primary',
			) ) );

			$outputs = apply_filters( 'grimlock_button_customizer_primary_background_color_outputs', array(
				$this->get_css_var_output( 'button_primary_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
				array(
					'element'  => implode( ',', $disabled_elements ),
					'property' => 'background-color',
					'suffix'   => '!important',
				),
				array(
					'element'  => implode( ',', array(
						'.btn-outline-primary',
					) ),
					'property' => 'color',
				),
				array(
					'element'  => '.text-primary',
					'property' => 'color',
					'suffix'   => '!important',
				),
				array(
					'element'  => implode( ',', array(
						'.btn-outline-primary',
						'a.list-group-item:hover',
						'a.list-group-item:focus',
						'a.list-group-item.active',
						'a.list-group-item.active:hover',
						'a.list-group-item.active:focus',
					) ),
					'property' => 'border-color'
				),
				array(
					'element'  => implode( ',', array(
						'.border-primary',
					) ),
					'property' => 'border-color',
					'suffix' => '!important'
				),
			), $elements, $disabled_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_primary_background_color',
				'default'   => $this->get_default( 'button_primary_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_primary_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_primary_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_primary_color_elements', array_merge(
				$this->button_primary_elements,
				$this->card_primary_elements,
				array(
					'.btn-outline-primary:hover',
				)
			) );

			$disabled_elements = apply_filters( 'grimlock_button_customizer_primary_color_disabled_elements', $this->button_primary_disabled_elements );

			$outputs = apply_filters( 'grimlock_button_customizer_primary_color_outputs', array(
				$this->get_css_var_output( 'button_primary_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $disabled_elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements, $disabled_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_primary_color',
				'default'   => $this->get_default( 'button_primary_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_primary_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color on hover in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_primary_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_primary_hover_color_elements', $this->button_primary_hover_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_primary_hover_color_outputs', array(
				$this->get_css_var_output( 'button_primary_hover_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_primary_hover_color',
				'default'   => $this->get_default( 'button_primary_hover_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_primary_hover_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color on hover in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_primary_hover_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_primary_hover_border_color_elements', $this->button_primary_hover_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_primary_hover_border_color_outputs', array(
				$this->get_css_var_output( 'button_primary_hover_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_primary_hover_border_color',
				'default'   => $this->get_default( 'button_primary_hover_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_primary_hover_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color on hover in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_primary_hover_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_primary_hover_background_color_elements', $this->button_primary_hover_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_primary_hover_background_color_outputs', array(
				$this->get_css_var_output( 'button_primary_hover_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_primary_hover_background_color',
				'default'   => $this->get_default( 'button_primary_hover_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_primary_hover_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_secondary_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_secondary_color_elements', array_merge( $this->button_secondary_elements, array(
				'.btn-outline-secondary:hover',
				'.btn-outline-secondary:active',
				'.btn-outline-secondary:focus',
			) ) );

			$disabled_elements = apply_filters( 'grimlock_button_customizer_secondary_color_disabled_elements', $this->button_secondary_disabled_elements );

			$outputs = apply_filters( 'grimlock_button_customizer_secondary_color_outputs', array(
				$this->get_css_var_output( 'button_secondary_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $disabled_elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements, $disabled_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_secondary_color',
				'default'   => $this->get_default( 'button_secondary_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_secondary_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_secondary_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_secondary_background_color_elements', array_merge( $this->button_secondary_elements, array(
				'.btn-outline-secondary:hover',
				'.btn-outline-secondary:active',
				'.btn-outline-secondary:focus',
			) ) );


			$disabled_elements = apply_filters( 'grimlock_button_customizer_secondary_background_color_disabled_elements', array_merge( $this->button_secondary_disabled_elements, array(
				'.bg-secondary',
			) ) );

			$outputs = apply_filters( 'grimlock_button_customizer_secondary_background_color_outputs', array(
				$this->get_css_var_output( 'button_secondary_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
				array(
					'element'  => implode( ',', $disabled_elements ),
					'property' => 'background-color',
					'suffix'   => '!important',
				),
				array(
					'element'  => '.text-secondary',
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements, $disabled_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_secondary_background_color',
				'default'   => $this->get_default( 'button_secondary_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_secondary_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_secondary_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_secondary_border_color_elements', array_merge(
				$this->button_secondary_elements,
				array(
					'.btn-outline-secondary',
					'.btn-outline-secondary:hover',
					'.btn-outline-secondary:active',
					'.btn-outline-secondary:focus',
				)
			) );

			$disabled_elements = apply_filters( 'grimlock_button_customizer_secondary_border_color_disabled_elements', $this->button_secondary_disabled_elements );

			$outputs = apply_filters( 'grimlock_button_customizer_secondary_border_color_outputs', array(
				$this->get_css_var_output( 'button_secondary_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
				array(
					'element'  => implode( ',', $disabled_elements ),
					'property' => 'border-color',
					'suffix'   => '!important',
				),
				array(
					'element'  => '.btn-outline-secondary',
					'property' => 'color',
				),
			), $elements, $disabled_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_secondary_border_color',
				'default'   => $this->get_default( 'button_secondary_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_secondary_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color on hover in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_secondary_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_secondary_hover_color_elements', $this->button_secondary_hover_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_secondary_hover_color_outputs', array(
				$this->get_css_var_output( 'button_secondary_hover_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_secondary_hover_color',
				'default'   => $this->get_default( 'button_secondary_hover_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_secondary_hover_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color on hover in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_secondary_hover_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_secondary_hover_border_color_elements', $this->button_secondary_hover_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_secondary_hover_border_color_outputs', array(
				$this->get_css_var_output( 'button_secondary_hover_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_secondary_hover_border_color',
				'default'   => $this->get_default( 'button_secondary_hover_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_secondary_hover_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color on hover in the Customizer.
	 *
	 * @since 1.0.0
	 */
	protected function add_button_secondary_hover_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_button_customizer_secondary_hover_background_color_elements', $this->button_secondary_hover_elements );
			$outputs  = apply_filters( 'grimlock_button_customizer_secondary_hover_background_color_outputs', array(
				$this->get_css_var_output( 'button_secondary_hover_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'button_secondary_hover_background_color',
				'default'   => $this->get_default( 'button_secondary_hover_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_button_customizer_secondary_hover_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add colors to the editor color palette
	 *
	 * @since 1.3.8
	 */
	public function add_editor_color_palette() {
		/**
		 * Color palette
		 */
		$color_palette = ! empty( get_theme_support( 'editor-color-palette' ) ) ? current( get_theme_support( 'editor-color-palette' ) ) : array();
		$colors        = ! empty( $color_palette ) ? array_map( 'strtolower', array_column( $color_palette, 'color' ) ) : array();

		$button_primary_background_color = strtolower( $this->get_theme_mod( 'button_primary_background_color' ) );
		if ( ! in_array( $button_primary_background_color, $colors ) ) {
			$color_palette[] = array(
				'name'  => esc_html__( 'Button Primary', 'grimlock' ),
				'slug'  => 'button-primary',
				'color' => $button_primary_background_color,
			);
		}

		$button_secondary_background_color = strtolower( $this->get_theme_mod( 'button_secondary_background_color' ) );
		if ( ! in_array( $button_secondary_background_color, $colors ) ) {
			$color_palette[] = array(
				'name'  => esc_html__( 'Button Secondary', 'grimlock' ),
				'slug'  => 'button-secondary',
				'color' => $button_secondary_background_color,
			);
		}

		add_theme_support( 'editor-color-palette', $color_palette );

		/**
		 * Gradient presets
		 */
		if ( class_exists( 'Kirki_Color' ) ) {
			$gradient_presets = ! empty( get_theme_support( 'editor-gradient-presets' ) ) ? current( get_theme_support( 'editor-gradient-presets' ) ) : array();
			$gradients        = ! empty( $gradient_presets ) ? array_map( 'strtolower', array_column( $gradient_presets, 'gradient' ) ) : array();

			$button_primary_hover_background_color = $this->get_theme_mod( 'button_primary_hover_background_color' );
			$primary_to_primary_hover_gradient     = strtolower( 'linear-gradient(135deg, ' . Kirki_Color::get_rgba( $button_primary_background_color, true ) . ' 0%, ' . Kirki_Color::get_rgba( $button_primary_hover_background_color, true ) . ' 100%)' );
			if ( ! in_array( $primary_to_primary_hover_gradient, $gradients ) ) {
				$gradient_presets[] = array(
					'name'     => esc_html__( 'Primary to primary hover', 'grimlock' ),
					'slug'     => 'primary-to-primary-hover',
					'gradient' => $primary_to_primary_hover_gradient,
				);
			}

			$button_secondary_hover_background_color = $this->get_theme_mod( 'button_secondary_hover_background_color' );
			$secondary_to_secondary_hover_gradient   = strtolower( 'linear-gradient(135deg, ' . Kirki_Color::get_rgba( $button_secondary_background_color, true ) . ' 0%, ' . Kirki_Color::get_rgba( $button_secondary_hover_background_color, true ) . ' 100%)' );
			if ( ! in_array( $secondary_to_secondary_hover_gradient, $gradients ) ) {
				$gradient_presets[] = array(
					'name'     => esc_html__( 'Secondary to secondary hover', 'grimlock' ),
					'slug'     => 'secondary-to-secondary-hover',
					'gradient' => $secondary_to_secondary_hover_gradient,
				);
			}

			$black_to_transparent_gradient = 'linear-gradient(135deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 100%)';
			if ( ! in_array( $black_to_transparent_gradient, $gradients ) ) {
				$gradient_presets[] = array(
					'name'     => esc_html__( 'Black to transparent', 'grimlock' ),
					'slug'     => 'secondary-to-secondary-hover',
					'gradient' => $black_to_transparent_gradient,
				);
			}

			add_theme_support( 'editor-gradient-presets', $gradient_presets );
		}
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @param string $styles The styles printed by Kirki
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function add_dynamic_css( $styles ) {
		$button_font = $this->get_theme_mod( 'button_font' );

		$lg_element = implode( ',', $this->button_lg_elements );
		$sm_element = implode( ',', $this->button_sm_elements );
		$xs_element = implode( ',', $this->button_xs_elements );

		$styles .= "
		{$lg_element} { font-size: calc({$button_font['font-size']} * 1.05); }
		{$sm_element} { font-size: calc({$button_font['font-size']} * .9);  }
		{$xs_element} { font-size: calc({$button_font['font-size']} * .75); }
		";

		return $styles;
	}
}

return new Grimlock_Button_Customizer();
