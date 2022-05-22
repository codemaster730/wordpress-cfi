<?php
/**
 * Grimlock_Control_Customizer Class
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
class Grimlock_Control_Customizer extends Grimlock_Base_Customizer {
	/**
	 * @var array The array of elements to target the form controls in theme.
	 * @since 1.0.0
	 */
	protected $elements;

	/**
	 * @var array The array of elements to target the disabled form controls in theme.
	 * @since 1.0.0
	 */
	protected $disabled_elements;

	/**
	 * @var array The array of elements to target the focus form controls in theme.
	 * @since 1.0.0
	 */
	protected $focus_elements;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_control_customizer_section';
		$this->title   = esc_html__( 'Form Controls', 'grimlock' );

		add_action( 'after_setup_theme', array( $this, 'add_customizer_fields' ), 20 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_control_customizer_defaults', array(
			'control_background_color'       => '#ffffff',
			'control_color'                  => GRIMLOCK_GRAY,
			'control_placeholder_color'      => '#999999',
			'control_border_color'           => 'rgba(0,0,0,.15)',

			'control_focus_background_color' => '#ffffff',
			'control_focus_color'            => GRIMLOCK_GRAY,
			'control_focus_border_color'     => '#66afe9',

			'control_border_width'           => 1, // px
			'control_border_radius'          => 0.25, // rem
		) );

		$this->elements = apply_filters( 'grimlock_control_customizer_elements', array(
			'.form-control',
			'.comment-form input[type="text"]',
			'.comment-form input[type="email"]',
			'.comment-form input[type="url"]',
			'.comment-form input[type="password"]',
			'.comment-form textarea',
			'input[type="tel"]',
			'input[type="url"]',
			'input[type="text"]',
			'input[type="week"]',
			'input[type="date"]',
			'input[type="datetime"]',
			'input[type="time"]',
			'input[type="email"]',
			'input[type="month"]',
			'input[type="number"]',
			'input[type="search"]',
			'input[type="website"]',
			'input[type="password"]',
			'select[multiple="multiple"]',
			'textarea',
			'select',
		) );

		$this->disabled_elements = array();
		foreach( $this->elements as $element ) {
			$this->disabled_elements[] = "{$element}:disabled";
			$this->disabled_elements[] = "{$element}[readonly]";
		}
		$this->disabled_elements = apply_filters( 'grimlock_control_customizer_disabled_elements', $this->disabled_elements );

		$this->focus_elements = array();
		foreach( $this->elements as $element ) {
			$this->focus_elements[] = "{$element}:focus";
		}
		$this->focus_elements = apply_filters( 'grimlock_control_customizer_focus_elements', $this->focus_elements );

		$this->add_section(                      array( 'priority' => 90  ) );

		$this->add_border_radius_field(          array( 'priority' => 10  ) );
		$this->add_border_width_field(           array( 'priority' => 20  ) );
		$this->add_divider_field(                array( 'priority' => 30  ) );
		$this->add_background_color_field(       array( 'priority' => 30  ) );
		$this->add_color_field(                  array( 'priority' => 40  ) );
		$this->add_placeholder_color_field(      array( 'priority' => 50  ) );
		$this->add_border_color_field(           array( 'priority' => 60  ) );
		$this->add_divider_field(                array( 'priority' => 80  ) );
		$this->add_focus_background_color_field( array( 'priority' => 80  ) );
		$this->add_focus_color_field(            array( 'priority' => 90  ) );
		$this->add_focus_border_color_field(     array( 'priority' => 100 ) );
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_control_customizer_border_color_elements', array_merge(
				$this->elements,
				$this->disabled_elements
			) );

			$outputs = apply_filters( 'grimlock_control_customizer_border_color_outputs', array(
				$this->get_css_var_output( 'control_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'control_border_color',
				'default'   => $this->get_default( 'control_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border radius in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_radius_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_control_customizer_border_radius_elements', array_merge(
				$this->elements,
				$this->disabled_elements
			) );

			$textarea_elements = apply_filters( 'grimlock_control_customizer_border_radius_textarea_elements', array(
				'textarea',
				'select[multiple]',
			) );

			$outputs = apply_filters( 'grimlock_control_customizer_border_radius_outputs', array(
				$this->get_css_var_output( 'control_border_radius', 'rem' ),
				array(
					'element'       => implode( ',', $elements ),
					'property'      => 'border-radius',
					'units'         => 'rem',
				),
				array(
					'element'       => implode( ',', $textarea_elements ),
					'property'      => 'border-radius',
					'value_pattern' => 'clamp(0px, $rem, 20px)',
					'suffix' => '!important',
				),
			), $elements, $textarea_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Radius', 'grimlock' ),
				'settings'  => 'control_border_radius',
				'default'   => $this->get_default( 'control_border_radius' ),
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

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_border_radius_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the border width in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_border_width_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_control_customizer_border_width_elements', array_merge(
				$this->elements,
				$this->disabled_elements,
				array(
					'.input-group .input-group-btn .btn',
				)
			) );

			$outputs = apply_filters( 'grimlock_control_customizer_border_width_outputs', array(
				$this->get_css_var_output( 'control_border_width', 'px' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-width',
					'units'    => 'px',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Border Width', 'grimlock' ),
				'settings'  => 'control_border_width',
				'default'   => $this->get_default( 'control_border_width' ),
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

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_border_width_field_args', $args ) );
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
			$elements = apply_filters( 'grimlock_control_customizer_background_color_elements', array_merge(
				$this->elements,
				$this->disabled_elements
			) );

			$outputs = apply_filters( 'grimlock_control_customizer_background_color_outputs', array(
				$this->get_css_var_output( 'control_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'control_background_color',
				'default'   => $this->get_default( 'control_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_control_customizer_color_elements', array_merge(
				$this->elements,
				$this->disabled_elements
			) );

			$outputs = apply_filters( 'grimlock_control_customizer_color_outputs', array(
				$this->get_css_var_output( 'control_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'control_color',
				'default'   => $this->get_default( 'control_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the text color for the control placeholder in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_placeholder_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements        = array_merge( $this->elements, $this->disabled_elements );
			$webkit_elements = array();
			$moz_elements    = array();
			$ms_elements     = array();

			foreach( $elements as $element ) {
				$webkit_elements[] = "{$element}::-webkit-input-placeholder";
			}

			foreach( $elements as $element ) {
				$moz_elements[] = "{$element}::-moz-placeholder";
			}

			foreach( $elements as $element ) {
				$ms_elements[] = "{$element}:-ms-input-placeholder";
			}


			$outputs = apply_filters( 'grimlock_control_customizer_placeholder_color_outputs', array(
				$this->get_css_var_output( 'control_placeholder_color' ),
				array(
					'element'  => implode( ',', $webkit_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $moz_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $ms_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', array(
						'form.search-form button[type="submit"]',
						'form.search-form button[type="submit"]:hover',
						'form.search-form button[type="submit"]:focus',
						'form.search-form button[type="submit"]:active',
					) ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements, $webkit_elements, $moz_elements, $ms_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Placeholder Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'control_placeholder_color',
				'default'   => $this->get_default( 'control_placeholder_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_placeholder_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color when focused in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_focus_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_control_customizer_focus_color_elements', $this->focus_elements );
			$outputs  = apply_filters( 'grimlock_control_customizer_focus_color_outputs', array(
				$this->get_css_var_output( 'control_focus_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', array(
						'form.search-form .search-field:focus::placeholder',
						'form.search-form .search-field:focus + [type="submit"]',
					) ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color on Focus', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'control_focus_color',
				'default'   => $this->get_default( 'control_focus_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_focus_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color when focused in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_focus_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_control_customizer_focus_border_color_elements', $this->focus_elements );
			$outputs  = apply_filters( 'grimlock_control_customizer_focus_border_color_outputs', array(
				$this->get_css_var_output( 'control_focus_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color on Focus', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'control_focus_border_color',
				'default'   => $this->get_default( 'control_focus_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_focus_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color when focused in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_focus_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_control_customizer_focus_background_color_elements', $this->focus_elements );
			$outputs  = apply_filters( 'grimlock_control_customizer_focus_background_color_outputs', array(
				$this->get_css_var_output( 'control_focus_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color on Focus', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'control_focus_background_color',
				'default'   => $this->get_default( 'control_focus_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_control_customizer_focus_background_color_field_args', $args ) );
		}
	}
}

return new Grimlock_Control_Customizer();
