<?php
/**
 * Grimlock_Typography_Customizer Class
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
class Grimlock_Typography_Customizer extends Grimlock_Base_Customizer {
	/**
	 * @var array The array of elements to target the links in theme.
	 * @since 1.0.0
	 */
	protected $link_elements;

	/**
	 * @var array The array of elements to target the links on hover in theme.
	 * @since 1.0.0
	 */
	protected $link_hover_elements;

	/**
	 * @var array The array of elements to target the blockquotes in theme.
	 * @since 1.0.0
	 */
	protected $blockquote_elements;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->section = 'grimlock_typography_customizer_section';
		$this->title   = esc_html__( 'Typography', 'grimlock' );

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_action( 'after_setup_theme',                    array( $this, 'add_editor_color_palette'        ), 100   );
		add_action( 'after_setup_theme',                    array( $this, 'add_editor_font_sizes'           ), 100   );

		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',           array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_typography_customizer_defaults', array(
			'text_font'                       => array(
				'font-family'                 => GRIMLOCK_FONT_FAMILY_SANS_SERIF,
				'font-weight'                 => 'regular',
				'font-size'                   => GRIMLOCK_FONT_SIZE,
				'line-height'                 => GRIMLOCK_LINE_HEIGHT,
				'letter-spacing'              => GRIMLOCK_LETTER_SPACING,
				'subsets'                     => array( 'latin-ext' ),
				'text-transform'              => 'none',
			),
			'text_color'                      => GRIMLOCK_BODY_COLOR,
			'text_selection_background_color' => 'rgba(172, 206, 247, 0.5)',

			'heading_font'                    => array(
				'font-family'                 => GRIMLOCK_FONT_FAMILY_SANS_SERIF,
				'letter-spacing'              => GRIMLOCK_LETTER_SPACING,
				'subsets'                     => array( 'latin-ext' ),
				'text-transform'              => 'none',
			),
			'heading_color'                   => GRIMLOCK_BODY_COLOR,
			'heading1_font'                   => array(
				'font-size'                   => GRIMLOCK_HEADING1_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'heading2_font'                   => array(
				'font-size'                   => GRIMLOCK_HEADING2_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'heading3_font'                   => array(
				'font-size'                   => GRIMLOCK_HEADING3_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'heading4_font'                   => array(
				'font-size'                   => GRIMLOCK_HEADING4_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'heading5_font'                   => array(
				'font-size'                   => GRIMLOCK_HEADING5_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'heading6_font'                   => array(
				'font-size'                   => GRIMLOCK_HEADING6_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),

			'display_heading_font'            => array(
				'font-family'                 => GRIMLOCK_FONT_FAMILY_SANS_SERIF,
				'letter-spacing'              => GRIMLOCK_LETTER_SPACING,
				'subsets'                     => array( 'latin-ext' ),
				'text-transform'              => 'none',
			),
			'display_heading_color'           => GRIMLOCK_BODY_COLOR,
			'display_heading1_font'           => array(
				'font-size'                   => GRIMLOCK_DISPLAY_HEADING1_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'display_heading2_font'           => array(
				'font-size'                   => GRIMLOCK_DISPLAY_HEADING2_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'display_heading3_font'           => array(
				'font-size'                   => GRIMLOCK_DISPLAY_HEADING3_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),
			'display_heading4_font'           => array(
				'font-size'                   => GRIMLOCK_DISPLAY_HEADING4_FONT_SIZE,
				'line-height'                 => GRIMLOCK_HEADINGS_LINE_HEIGHT,
			),

			'subheading_font'                 => array(
				'font-family'                 => GRIMLOCK_FONT_FAMILY_SANS_SERIF,
				'font-weight'                 => 'regular',
				'font-size'                   => '1.25rem',
				'line-height'                 => '1.5',
				'letter-spacing'              => GRIMLOCK_LETTER_SPACING,
				'subsets'                     => array( 'latin-ext' ),
				'text-transform'              => 'none',
			),
			'subheading_color'                => GRIMLOCK_BODY_COLOR,

			'link_color'                      => GRIMLOCK_LINK_COLOR,
			'link_hover_color'                => GRIMLOCK_LINK_HOVER_COLOR,

			'blockquote_font'                 => array(
				'font-family'                 => GRIMLOCK_FONT_FAMILY_SERIF,
				'font-weight'                 => 'italic',
				'font-size'                   => '1.25rem',
				'line-height'                 => GRIMLOCK_LINE_HEIGHT,
				'letter-spacing'              => GRIMLOCK_LETTER_SPACING,
				'subsets'                     => array( 'latin-ext' ),
				'text-transform'              => 'none',
			),
			'blockquote_color'                => GRIMLOCK_BODY_COLOR,
			'blockquote_background_color'     => '#ffffff',
			'blockquote_icon_color'           => GRIMLOCK_BODY_COLOR,
			'blockquote_border_color'         => GRIMLOCK_BORDER_COLOR,
			'blockquote_margin'               => 1.875, // rem.
		) );

		$this->link_elements = apply_filters( 'grimlock_typography_customizer_link_elements', array(
			'a:not([class*="btn"])',
			'.btn-link',
		) );

		$this->link_hover_elements = array();

		foreach( $this->link_elements as $element ) {
			$this->link_hover_elements[] = "$element:hover";
			$this->link_hover_elements[] = "$element:focus";
			$this->link_hover_elements[] = "$element:active";
		}
		$this->link_hover_elements = apply_filters( 'grimlock_typography_customizer_link_hover_elements', $this->link_hover_elements );

		$this->blockquote_elements = apply_filters( 'grimlock_typography_customizer_blockquote_elements', array(
			'blockquote:not(.card-blockquote)',
			'.blockquote',
		) );

		// @codingStandardsIgnoreStart
		// Allow associative array to be declared in a single line.
		$this->add_section(                               array( 'priority' => 20 ) );

		$this->add_text_font_field(                       array( 'priority' => 10  ) );
		$this->add_divider_field(                         array( 'priority' => 20  ) );
		$this->add_text_color_field(                      array( 'priority' => 20  ) );
		$this->add_text_selection_background_color_field( array( 'priority' => 30  ) );

		$this->add_heading_font_field(                    array( 'priority' => 100 ) );
		$this->add_divider_field(                         array( 'priority' => 110 ) );
		$this->add_heading_color_field(                   array( 'priority' => 110 ) );
		$this->add_divider_field(                         array( 'priority' => 120 ) );
		$this->add_heading1_font_field(                   array( 'priority' => 120 ) );
		$this->add_heading2_font_field(                   array( 'priority' => 130 ) );
		$this->add_heading3_font_field(                   array( 'priority' => 140 ) );
		$this->add_heading4_font_field(                   array( 'priority' => 150 ) );
		$this->add_heading5_font_field(                   array( 'priority' => 160 ) );
		$this->add_heading6_font_field(                   array( 'priority' => 170 ) );
		$this->add_divider_field(                         array( 'priority' => 180 ) );
		$this->add_display_heading_font_field(            array( 'priority' => 180 ) );
		$this->add_divider_field(                         array( 'priority' => 190 ) );
		$this->add_display_heading_color_field(           array( 'priority' => 190 ) );
		$this->add_divider_field(                         array( 'priority' => 200 ) );
		$this->add_display_heading1_font_field(           array( 'priority' => 200 ) );
		$this->add_display_heading2_font_field(           array( 'priority' => 210 ) );
		$this->add_display_heading3_font_field(           array( 'priority' => 220 ) );
		$this->add_display_heading4_font_field(           array( 'priority' => 230 ) );
		$this->add_divider_field(                         array( 'priority' => 240 ) );
		$this->add_subheading_font_field(                 array( 'priority' => 240 ) );
		$this->add_divider_field(                         array( 'priority' => 250 ) );
		$this->add_subheading_color_field(                array( 'priority' => 250 ) );

		$this->add_link_color_field(                      array( 'priority' => 300 ) );
		$this->add_link_hover_color_field(                array( 'priority' => 310 ) );

		$this->add_blockquote_font_field(                 array( 'priority' => 400 ) );
		$this->add_divider_field(                         array( 'priority' => 410 ) );
		$this->add_blockquote_color_field(                array( 'priority' => 410 ) );
		$this->add_blockquote_background_color_field(     array( 'priority' => 420 ) );
		$this->add_blockquote_icon_color_field(           array( 'priority' => 430 ) );
		$this->add_blockquote_border_color_field(         array( 'priority' => 440 ) );
		$this->add_divider_field(                         array( 'priority' => 450 ) );
		$this->add_blockquote_margin_field(               array( 'priority' => 450 ) );
		// @codingStandardsIgnoreEnd
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
				'label'    => esc_html__( 'Text', 'grimlock' ),
				'class'    => 'typography-text-tab',
				'controls' => array(
					'text_font',
					"{$this->section}_divider_20",
					'text_color',
					'text_selection_background_color',
				),
			),
			array(
				'label'    => esc_html__( 'Headings', 'grimlock' ),
				'class'    => 'typography-heading-tab',
				'controls' => array(
					'heading_font',
					"{$this->section}_divider_110",
					'heading_color',
					"{$this->section}_divider_120",
					'heading1_font',
					'heading2_font',
					'heading3_font',
					'heading4_font',
					'heading5_font',
					'heading6_font',
					"{$this->section}_divider_180",
					'display_heading_font',
					"{$this->section}_divider_190",
					'display_heading_color',
					"{$this->section}_divider_200",
					'display_heading1_font',
					'display_heading2_font',
					'display_heading3_font',
					'display_heading4_font',
					"{$this->section}_divider_240",
					'subheading_font',
					"{$this->section}_divider_250",
					'subheading_color',
				),
			),
			array(
				'label'    => esc_html__( 'Links', 'grimlock' ),
				'class'    => 'typography-link-tab',
				'controls' => array(
					'link_color',
					'link_hover_color',
				),
			),
			array(
				'label'    => esc_html__( 'Quotes', 'grimlock' ),
				'class'    => 'typography-blockquote-tab',
				'controls' => array(
					'blockquote_font',
					"{$this->section}_divider_410",
					'blockquote_color',
					'blockquote_icon_color',
					'blockquote_background_color',
					'blockquote_border_color',
					"{$this->section}_divider_450",
					'blockquote_margin',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki typography field to set the typography in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_text_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_text_font_elements', array(
				'body',
			) );

			$outputs  = apply_filters( 'grimlock_typography_customizer_text_font_outputs', array(
				array(
					'element' => $elements,
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
				'settings'  => 'text_font',
				'label'     => esc_attr__( 'Text', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'text_font' ),
				'choices'   => array(
					'variant' => array(
						'regular',
						'italic',
						'700',
						'700italic',
					),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'text_font' ) ),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_text_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_text_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_text_color_elements', array(
				'body',
			) );

			$outputs  = apply_filters( 'grimlock_typography_customizer_text_color_outputs', array(
				$this->get_css_var_output( 'text_color' ),
				array(
					'element'  => $elements,
					'property' => 'color',
				),
				array(
					'element'  => '.bg-text-color',
					'property' => 'background-color',
					'suffix' => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'text_color',
				'default'   => $this->get_default( 'text_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_text_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_text_selection_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$outputs  = apply_filters( 'grimlock_typography_customizer_text_selection_background_color_outputs', array(
				$this->get_css_var_output( 'text_selection_background_color' ),
				array(
					'element'  => '::selection',
					'property' => 'background-color',
				),
				array(
					'element'  => '::-moz-selection',
					'property' => 'background-color',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Text Selection Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'text_selection_background_color',
				'default'   => $this->get_default( 'text_selection_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_text_selection_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_heading_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading_font_elements', array(
				'h1', '.h1',
				'h2', '.h2',
				'h3', '.h3',
				'h4', '.h4',
				'h5', '.h5',
				'h6', '.h6',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_heading_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
				array(
					'element'       => $elements,
					'property'      => 'font-family',
					'choice'        => 'font-family',
					'value_pattern' => '$, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'        => 'typography',
				'settings'    => 'heading_font',
				'label'       => esc_html__( 'Headings', 'grimlock' ),
				'description' => esc_html__( 'Headings are found in posts, page, other post types and built-in widgets.', 'grimlock' ),
				'section'     => $this->section,
				'choices'   => array(
					'variant' => array(
						'regular',
					),
				),
				'default'     => $this->get_default( 'heading_font' ),
				'priority'    => 10,
				'transport'   => 'postMessage',
				'output'      => array_merge( $outputs, $this->get_typography_css_vars_output( 'heading_font' ) ),
				'js_vars'     => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_heading_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading_color_elements', array(
				'h1', '.h1',
				'h2', '.h2',
				'h3', '.h3',
				'h4', '.h4',
				'h5', '.h5',
				'h6', '.h6',
			) );

			$outputs  = apply_filters( 'grimlock_typography_customizer_heading_color_outputs', array(
				$this->get_css_var_output( 'heading_color' ),
				array(
					'element'  => $elements,
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Headings Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'heading_color',
				'default'   => $this->get_default( 'heading_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the heading 1 in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_heading1_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading1_font_elements', array(
				'h1', '.h1',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_heading1_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'heading1_font',
				'label'     => esc_html__( 'Heading 1', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'heading1_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'heading1_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading1_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the heading 2 in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_heading2_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading2_font_elements', array(
				'h2', '.h2',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_heading2_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'heading2_font',
				'label'     => esc_html__( 'Heading 2', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'heading2_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'heading2_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading2_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the heading 3 in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_heading3_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading3_font_elements', array(
				'h3', '.h3',
				'[class*="posts--4-4-4"] h2.entry-title',
				'[class*="posts--6-6"] h2.entry-title',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_heading3_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'heading3_font',
				'label'     => esc_html__( 'Heading 3', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'heading3_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'heading3_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading3_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the heading 4 in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_heading4_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading4_font_elements', array(
				'h4', '.h4',
				'[class*="posts--3-3-3-3"] h2.entry-title',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_heading4_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'heading4_font',
				'label'     => esc_html__( 'Heading 4', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'heading4_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'heading4_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading4_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the heading 5 in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_heading5_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading5_font_elements', array(
				'h5', '.h5',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_heading5_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'heading5_font',
				'label'     => esc_html__( 'Heading 5', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'heading5_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'heading5_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading5_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the heading 6 in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_heading6_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_heading6_font_elements', array(
				'h6', '.h6',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_heading6_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'heading6_font',
				'label'     => esc_html__( 'Heading 6', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'heading6_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'heading6_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_heading6_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the display headings in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_display_heading_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_display_heading_font_elements', array(
				'.display-1',
				'.display-2',
				'.display-3',
				'.display-4',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_display_heading_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
				array(
					'element'       => $elements,
					'property'      => 'font-family',
					'choice'        => 'font-family',
					'value_pattern' => '$, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'        => 'typography',
				'settings'    => 'display_heading_font',
				'label'       => esc_html__( 'Display Headings', 'grimlock' ),
				'description' => esc_html__( 'Display headings are big headlines found in the custom header and the section widgets.', 'grimlock' ),
				'section'     => $this->section,
				'default'     => $this->get_default( 'display_heading_font' ),
				'choices'   => array(
					'variant' => array(
						'regular',
						'italic',
						'700',
						'700italic',
					),
				),
				'priority'    => 10,
				'transport'   => 'postMessage',
				'output'      => array_merge( $outputs, $this->get_typography_css_vars_output( 'display_heading_font' ) ),
				'js_vars'     => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_display_heading_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_display_heading_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_display_heading_color_elements', array(
				'.display-1',
				'.display-2',
				'.display-3',
				'.display-4',
			) );

			$outputs  = apply_filters( 'grimlock_typography_customizer_display_heading_color_outputs', array(
				$this->get_css_var_output( 'display_heading_color' ),
				array(
					'element'  => $elements,
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Display Headings Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'display_heading_color',
				'default'   => $this->get_default( 'display_heading_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_display_heading_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the display heading 1 in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_display_heading1_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_display_heading1_font_elements', array(
				'.display-1',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_display_heading1_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'display_heading1_font',
				'label'     => esc_html__( 'Display Heading 1', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'display_heading1_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'display_heading1_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_display_heading1_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the display heading 2 in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_display_heading2_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_display_heading2_font_elements', array(
				'.display-2',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_display_heading2_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'display_heading2_font',
				'label'     => esc_html__( 'Display Heading 2', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'display_heading2_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'display_heading2_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_display_heading2_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the display heading 3 in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_display_heading3_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_display_heading3_font_elements', array(
				'.display-3',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_display_heading3_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'display_heading3_font',
				'label'     => esc_html__( 'Display Heading 3', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'display_heading3_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'display_heading3_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_display_heading3_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the display heading 4 in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_display_heading4_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_display_heading4_font_elements', array(
				'.display-4',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_display_heading4_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'typography',
				'settings'  => 'display_heading4_font',
				'label'     => esc_html__( 'Display Heading 4', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'display_heading4_font' ),
				'priority'  => 10,
				'transport' => 'auto',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'display_heading4_font' ) ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_display_heading4_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography for the subheadings in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_subheading_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_subheading_font_elements', array(
				'.lead',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_subheading_font_outputs', array(
				array(
					'element' => implode( ',', $elements )
				),
				array(
					'element'       => $elements,
					'property'      => 'font-family',
					'choice'        => 'font-family',
					'value_pattern' => '$, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'        => 'typography',
				'settings'    => 'subheading_font',
				'label'       => esc_html__( 'Subheadings', 'grimlock' ),
				'description' => esc_html__( 'Subheadings appear after the headings of the custom header or the section widgets.', 'grimlock' ),
				'section'     => $this->section,
				'default'     => $this->get_default( 'subheading_font' ),
				'priority'    => 10,
				'transport'   => 'auto',
				'output'      => array_merge( $outputs, $this->get_typography_css_vars_output( 'subheading_font' ) ),
				'js_vars'     => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_subheading_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_subheading_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_subheading_color_elements', array(
				'.lead',
			) );

			$outputs  = apply_filters( 'grimlock_typography_customizer_subheading_color_outputs', array(
				$this->get_css_var_output( 'subheading_color' ),
				array(
					'element'  => $elements,
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Subheadings Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'subheading_color',
				'default'   => $this->get_default( 'subheading_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_subheading_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_link_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_link_color_elements', $this->link_elements );
			$outputs  = apply_filters( 'grimlock_typography_customizer_link_color_outputs', array(
				$this->get_css_var_output( 'link_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'link_color',
				'default'   => $this->get_default( 'link_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_link_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color on hover in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_link_hover_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_link_hover_color_elements', $this->link_hover_elements );
			$outputs  = apply_filters( 'grimlock_typography_customizer_link_hover_color_outputs', array(
				$this->get_css_var_output( 'link_hover_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Link Color on Hover', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'link_hover_color',
				'default'   => $this->get_default( 'link_hover_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_link_hover_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_blockquote_icon_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_blockquote_icon_color_elements', array(
				'blockquote:not(.has-text-color):not(.card-blockquote):before',
				'blockquote:not(.has-text-color):before',
			) );

			$outputs = apply_filters( 'grimlock_typography_customizer_blockquote_icon_color_outputs', array(
				$this->get_css_var_output( 'blockquote_icon_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Icon Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'blockquote_icon_color',
				'default'   => $this->get_default( 'blockquote_icon_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_blockquote_icon_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the background color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_blockquote_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_blockquote_background_color_elements', $this->blockquote_elements );
			$outputs  = apply_filters( 'grimlock_typography_customizer_blockquote_background_color_outputs', array(
				$this->get_css_var_output( 'blockquote_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
				),
				array(
					'element'  => '.wp-block-pullquote.is-style-solid-color:not([class*="-background-color"]) blockquote',
					'property' => 'background-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Background Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'blockquote_background_color',
				'default'   => $this->get_default( 'blockquote_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_blockquote_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the border color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_blockquote_border_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_blockquote_border_color_elements', $this->blockquote_elements );
			$outputs  = apply_filters( 'grimlock_typography_customizer_blockquote_border_color_outputs', array(
				$this->get_css_var_output( 'blockquote_border_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'border-color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Border Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'blockquote_border_color',
				'default'   => $this->get_default( 'blockquote_border_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_blockquote_border_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki typography field to set the typography in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_blockquote_font_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$elements = apply_filters( 'grimlock_typography_customizer_blockquote_font_elements', $this->blockquote_elements );
			$outputs  = apply_filters( 'grimlock_typography_customizer_blockquote_font_outputs', array(
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
				'settings'  => 'blockquote_font',
				'label'     => esc_attr__( 'Typography', 'grimlock' ),
				'section'   => $this->section,
				'default'   => $this->get_default( 'blockquote_font' ),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => array_merge( $outputs, $this->get_typography_css_vars_output( 'blockquote_font' ) ),
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_blockquote_font_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_blockquote_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_blockquote_color_elements', $this->blockquote_elements );
			$outputs  = apply_filters( 'grimlock_typography_customizer_blockquote_color_outputs', array(
				$this->get_css_var_output( 'blockquote_color' ),
				array(
					'element'  => $elements,
					'property' => 'color',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Color', 'grimlock' ),
				'section'   => $this->section,
				'settings'  => 'blockquote_color',
				'default'   => $this->get_default( 'blockquote_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'output'    => $outputs,
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_blockquote_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the margin in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_blockquote_margin_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_typography_customizer_blockquote_margin_elements', $this->blockquote_elements );
			$outputs  = apply_filters( 'grimlock_typography_customizer_blockquote_margin_outputs', array(
				$this->get_css_var_output( 'blockquote_margin', 'rem' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'margin-top',
					'units'    => 'rem',
				),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'margin-bottom',
					'units'    => 'rem',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Margin', 'grimlock' ),
				'settings'  => 'blockquote_margin',
				'default'   => $this->get_default( 'blockquote_margin' ),
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

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_typography_customizer_blockquote_margin_field_args', $args ) );
		}
	}

	/**
	 * Add colors to the editor color palette
	 *
	 * @since 1.3.12
	 */
	public function add_editor_color_palette() {
		$color_palette = ! empty( get_theme_support( 'editor-color-palette' ) ) ? current( get_theme_support( 'editor-color-palette' ) ) : array();
		$colors        = ! empty( $color_palette ) ? array_map( 'strtolower', array_column( $color_palette, 'color' ) ) : array();

		$display_heading_color = strtolower( $this->get_theme_mod( 'display_heading_color' ) );
		if ( ! in_array( $display_heading_color, $colors ) ) {
			$color_palette[] = array(
				'name'  => esc_html__( 'Display Heading', 'grimlock' ),
				'slug'  => 'display-heading',
				'color' => $display_heading_color,
			);
		}

		$text_color = strtolower( $this->get_theme_mod( 'text_color' ) );
		if ( ! in_array( $text_color, $colors ) ) {
			$color_palette[] = array(
				'name'  => esc_html__( 'Text', 'grimlock' ),
				'slug'  => 'text-color',
				'color' => $text_color,
			);
		}

		add_theme_support( 'editor-color-palette', $color_palette );
	}

	/**
	 * Add font sizes to the editor
	 */
	public function add_editor_font_sizes() {
		$font_sizes = array(
			array(
				'name' => esc_html__( 'Text', 'grimlock' ),
				'size' => $this->get_theme_mod( 'text_font' )['font-size'],
				'slug' => 'text',
			),
			array(
				'name' => esc_html__( 'Text Smaller', 'grimlock' ),
				'size' => '0.92em',
				'slug' => 'text-smaller',
			),
			array(
				'name' => esc_html__( 'Text Bigger', 'grimlock' ),
				'size' => '1.16em',
				'slug' => 'text-bigger',
			),
			array(
				'name' => esc_html__( 'H1', 'grimlock' ),
				'size' => $this->get_theme_mod( 'heading1_font' )['font-size'],
				'slug' => 'h1',
			),
			array(
				'name' => esc_html__( 'H2', 'grimlock' ),
				'size' => $this->get_theme_mod( 'heading2_font' )['font-size'],
				'slug' => 'h2',
			),
			array(
				'name' => esc_html__( 'H3', 'grimlock' ),
				'size' => $this->get_theme_mod( 'heading3_font' )['font-size'],
				'slug' => 'h3',
			),
			array(
				'name' => esc_html__( 'H4', 'grimlock' ),
				'size' => $this->get_theme_mod( 'heading4_font' )['font-size'],
				'slug' => 'h4',
			),
			array(
				'name' => esc_html__( 'H5', 'grimlock' ),
				'size' => $this->get_theme_mod( 'heading5_font' )['font-size'],
				'slug' => 'h5',
			),
			array(
				'name' => esc_html__( 'H6', 'grimlock' ),
				'size' => $this->get_theme_mod( 'heading6_font' )['font-size'],
				'slug' => 'h6',
			),
			array(
				'name' => esc_html__( 'DH1', 'grimlock' ),
				'size' => $this->get_theme_mod( 'display_heading1_font' )['font-size'],
				'slug' => 'dh1',
			),
			array(
				'name' => esc_html__( 'DH2', 'grimlock' ),
				'size' => $this->get_theme_mod( 'display_heading2_font' )['font-size'],
				'slug' => 'dh2',
			),
			array(
				'name' => esc_html__( 'DH3', 'grimlock' ),
				'size' => $this->get_theme_mod( 'display_heading3_font' )['font-size'],
				'slug' => 'dh3',
			),
			array(
				'name' => esc_html__( 'DH4', 'grimlock' ),
				'size' => $this->get_theme_mod( 'display_heading4_font' )['font-size'],
				'slug' => 'dh4',
			),
			array(
				'name' => esc_html__( 'Subheading', 'grimlock' ),
				'size' => $this->get_theme_mod( 'subheading_font' )['font-size'],
				'slug' => 'subheading',
			),
		);

		foreach ( $font_sizes as $key => $font_size ) {
			// Approximately convert rem to px because editor font sizes only work with px values
			$font_sizes[ $key ]['size'] = intval( floatval( $font_size['size'] ) * 16 );
		}

		add_theme_support( 'editor-font-sizes', $font_sizes );
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @param string $styles The styles printed by Kirki
	 *
	 * @since 1.0.0
	 *
	 * @return string The modified styles to be printed by Kirki
	 */
	public function add_dynamic_css( $styles ) {
		$heading1_font         = $this->get_theme_mod( 'heading1_font' );
		$heading2_font         = $this->get_theme_mod( 'heading2_font' );
		$heading3_font         = $this->get_theme_mod( 'heading3_font' );
		$heading4_font         = $this->get_theme_mod( 'heading4_font' );
		$heading5_font         = $this->get_theme_mod( 'heading5_font' );
		$heading6_font         = $this->get_theme_mod( 'heading6_font' );
		$display_heading1_font = $this->get_theme_mod( 'display_heading1_font' );
		$display_heading2_font = $this->get_theme_mod( 'display_heading2_font' );
		$display_heading3_font = $this->get_theme_mod( 'display_heading3_font' );
		$display_heading4_font = $this->get_theme_mod( 'display_heading4_font' );
		$subheading_font       = $this->get_theme_mod( 'subheading_font' );

		$styles .= "
		hr { border-top-color: {$this->get_theme_mod( 'text_color' )}; }

		/* Gutenberg */
		.wp-block-separator { border-bottom-color: {$this->get_theme_mod( 'text_color' )}; }

		@media screen and (max-width: 992px) {
			h1, .h1 { font-size: calc( ({$heading1_font['font-size']}) / 1.5); }
			h2, .h2 { font-size: calc( ({$heading2_font['font-size']}) / 1.3); }
			h3, .h3 { font-size: calc( ({$heading3_font['font-size']}) / 1.2); }
			h4, .h4 { font-size: calc( ({$heading4_font['font-size']}) / 1.2); }
			h5, .h5 { font-size: calc( ({$heading5_font['font-size']}) / 1.1); }
			h6, .h6 { font-size: calc(  {$heading6_font['font-size']}       ); }

			.display-1 { font-size: calc( ({$display_heading1_font['font-size']}) / 1.6); }
			.display-2 { font-size: calc( ({$display_heading2_font['font-size']}) / 1.3); }
			.display-3 { font-size: calc( ({$display_heading3_font['font-size']}) / 1.2); }
			.display-4 { font-size: calc( ({$display_heading4_font['font-size']}) / 1.2); }

			.lead { font-size: calc( ({$subheading_font['font-size']}) / 1.2); }
		}

		@media screen and (max-width: 576px) {
			h1, .h1 { font-size: calc( ({$heading1_font['font-size']}) / 1.6); }
			h2, .h2 { font-size: calc( ({$heading2_font['font-size']}) / 1.4); }
			h3, .h3 { font-size: calc( ({$heading3_font['font-size']}) / 1.3); }
			h4, .h4 { font-size: calc( ({$heading4_font['font-size']}) / 1.3); }

			.display-1 { font-size: calc( ({$display_heading1_font['font-size']}) / 1.8); }
			.display-2 { font-size: calc( ({$display_heading2_font['font-size']}) / 1.6); }
			.display-3 { font-size: calc( ({$display_heading3_font['font-size']}) / 1.4); }
			.display-4 { font-size: calc( ({$display_heading4_font['font-size']}) / 1.3); }

			.lead { font-size: calc( ({$subheading_font['font-size']}) / 1.2); line-height: calc( ({$subheading_font['line-height']}) / 1.2); }
		}";

		return $styles;
	}
}

return new Grimlock_Typography_Customizer();
