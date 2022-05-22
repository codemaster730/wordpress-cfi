<?php
/**
 * Cera_Grimlock_Typography_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The typography class for the Customizer.
 */
class Cera_Grimlock_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_typography_customizer_defaults',                        array( $this, 'change_defaults'                        ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_text_color_elements',             array( $this, 'add_text_color_elements'                ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_text_color_outputs',              array( $this, 'add_text_color_outputs'                 ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_heading_color_outputs',           array( $this, 'add_heading_color_outputs'              ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_display_heading1_font_outputs',   array( $this, 'add_display_heading1_font_outputs'      ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_display_heading3_font_elements',  array( $this, 'add_display_heading3_font_elements'     ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_heading3_font_elements',          array( $this, 'add_heading3_font_elements'             ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_heading_font_outputs',            array( $this, 'add_heading_font_outputs'               ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_link_color_elements',             array( $this, 'add_link_color_elements'                ), 10, 1 );
		add_filter( 'grimlock_typography_customizer_text_font_field_args',            array( $this, 'change_text_font_field_args'            ), 20, 1 );
		add_filter( 'grimlock_typography_customizer_display_heading_font_field_args', array( $this, 'change_display_heading_font_field_args' ), 20, 1 );
		add_filter( 'grimlock_typography_customizer_link_hover_color_outputs',        array( $this, 'add_link_hover_color_outputs'           ), 10, 1 );
	}

	/**
	 * Change default values and control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $defaults The array of default values for the Customizer controls.
	 *
	 * @return array           The updated array of default values for the Customizer controls.
	 */
	public function change_defaults( $defaults ) {
		$defaults['text_font'] = array(
			'font-family'    => CERA_TYPOGRAPHY_TEXT_FONT_FONT_FAMILY,
			'font-weight'    => CERA_TYPOGRAPHY_TEXT_FONT_FONT_WEIGHT,
			'font-size'      => CERA_TYPOGRAPHY_TEXT_FONT_FONT_SIZE,
			'line-height'    => CERA_TYPOGRAPHY_TEXT_FONT_LINE_HEIGHT,
			'letter-spacing' => CERA_TYPOGRAPHY_TEXT_FONT_LETTER_SPACING,
			'subsets'        => array( 'latin-ext' ),
			'text-transform' => CERA_TYPOGRAPHY_TEXT_FONT_TEXT_TRANSFORM,
		);
		$defaults['text_color']                      = CERA_TYPOGRAPHY_TEXT_COLOR;
		$defaults['text_selection_background_color'] = CERA_TYPOGRAPHY_TEXT_SELECTION_BACKGROUND_COLOR;

		$defaults['heading_font'] = array(
			'font-family'    => CERA_TYPOGRAPHY_HEADING_FONT_FONT_FAMILY,
			'font-weight'    => CERA_TYPOGRAPHY_HEADING_FONT_FONT_WEIGHT,
			'letter-spacing' => CERA_TYPOGRAPHY_HEADING_FONT_LETTER_SPACING,
			'subsets'        => array( 'latin-ext' ),
			'text-transform' => CERA_TYPOGRAPHY_HEADING_FONT_TEXT_TRANSFORM,
		);
		$defaults['heading_color'] = CERA_TYPOGRAPHY_HEADING_COLOR;

		$defaults['heading1_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_HEADING1_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_HEADING1_FONT_LINE_HEIGHT,
		);
		$defaults['heading2_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_HEADING2_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_HEADING2_FONT_LINE_HEIGHT,
		);
		$defaults['heading3_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_HEADING3_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_HEADING3_FONT_LINE_HEIGHT,
		);
		$defaults['heading4_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_HEADING4_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_HEADING4_FONT_LINE_HEIGHT,
		);
		$defaults['heading5_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_HEADING5_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_HEADING5_FONT_LINE_HEIGHT,
		);
		$defaults['heading6_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_HEADING6_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_HEADING6_FONT_LINE_HEIGHT,
		);
		$defaults['display_heading_font'] = array(
			'font-family'    => CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_FONT_FAMILY,
			'font-weight'    => CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_FONT_WEIGHT,
			'letter-spacing' => CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_LETTER_SPACING,
			'subsets'        => array( 'latin-ext' ),
			'text-transform' => CERA_TYPOGRAPHY_DISPLAY_HEADING_FONT_TEXT_TRANSFORM,
		);
		$defaults['display_heading_color'] = CERA_TYPOGRAPHY_DISPLAY_HEADING_COLOR;

		$defaults['display_heading1_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_DISPLAY_HEADING1_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_DISPLAY_HEADING1_FONT_LINE_HEIGHT,
		);
		$defaults['display_heading2_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_DISPLAY_HEADING2_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_DISPLAY_HEADING2_FONT_LINE_HEIGHT,
		);
		$defaults['display_heading3_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_DISPLAY_HEADING3_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_DISPLAY_HEADING3_FONT_LINE_HEIGHT,
		);
		$defaults['display_heading4_font'] = array(
			'font-size'   => CERA_TYPOGRAPHY_DISPLAY_HEADING4_FONT_FONT_SIZE,
			'line-height' => CERA_TYPOGRAPHY_DISPLAY_HEADING4_FONT_LINE_HEIGHT,
		);
		$defaults['subheading_font'] = array(
			'font-family'    => CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_FAMILY,
			'font-weight'    => CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_WEIGHT,
			'font-size'      => CERA_TYPOGRAPHY_SUBHEADING_FONT_FONT_SIZE,
			'line-height'    => CERA_TYPOGRAPHY_SUBHEADING_FONT_LINE_HEIGHT,
			'letter-spacing' => CERA_TYPOGRAPHY_SUBHEADING_FONT_LETTER_SPACING,
			'subsets'        => array( 'latin-ext' ),
			'text-transform' => CERA_TYPOGRAPHY_SUBHEADING_FONT_TEXT_TRANSFORM,
		);
		$defaults['subheading_color'] = CERA_TYPOGRAPHY_SUBHEADING_COLOR;

		$defaults['link_color']       = CERA_TYPOGRAPHY_LINK_COLOR;
		$defaults['link_hover_color'] = CERA_TYPOGRAPHY_LINK_HOVER_COLOR;

		$defaults['blockquote_font'] = array(
			'font-family'    => CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_FAMILY,
			'font-weight'    => CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_WEIGHT,
			'font-size'      => CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_FONT_SIZE,
			'line-height'    => CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_LINE_HEIGHT,
			'letter-spacing' => CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_LETTER_SPACING,
			'subsets'        => array( 'latin-ext' ),
			'text-transform' => CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_TEXT_TRANSFORM,
			'text-align'     => CERA_TYPOGRAPHY_BLOCKQUOTE_FONT_TEXT_ALIGN,
		);
		$defaults['blockquote_color']            = CERA_TYPOGRAPHY_BLOCKQUOTE_COLOR;
		$defaults['blockquote_background_color'] = CERA_TYPOGRAPHY_BLOCKQUOTE_BACKGROUND_COLOR;
		$defaults['blockquote_icon_color']       = CERA_TYPOGRAPHY_BLOCKQUOTE_ICON_COLOR;
		$defaults['blockquote_border_color']     = CERA_TYPOGRAPHY_BLOCKQUOTE_BORDER_COLOR;
		$defaults['blockquote_margin']           = CERA_TYPOGRAPHY_BLOCKQUOTE_MARGIN;
		return $defaults;
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the heading color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the heading color.
	 *
	 * @return array          The updated array of CSS selectors for the heading color.
	 */
	public function add_heading_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'mark',
					'ul.wpp-list li a:not(:hover)',
				) ),
				'property' => 'color',
				'suffix'   => '!important',
			),
			array(
				'element'  => implode( ',', array(
					'.bs-tooltip-top .arrow::before',
					'.bs-tooltip-auto[x-placement^="top"] .arrow::before',
				) ),
				'property' => 'border-top-color',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the heading 1 font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the heading 1 font.
	 *
	 * @return array          The updated array of CSS selectors for the heading 1 font.
	 */
	public function add_display_heading1_font_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.grimlock--singlet #custom_header .section__title.display-2',
				) ),
				'property'      => 'font-size',
				'media_query'   => '@media (min-width: 768px)',
			),
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the display heading 3 font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the display heading 3 font.
	 *
	 * @return array           The updated array of CSS selectors for the display heading 3 font.
	 */
	public function add_display_heading3_font_elements( $elements ) {
		return array_merge( $elements, array(
			'body:not(.page-template-template-dashboard) #primary > #main > .widget-area .widget:not([class*="widget_grimlock_"]) .widget-title',
			'body:not(.page-template-template-dashboard) #before_content > .widget-area .widget:not([class*="widget_grimlock_"]) .widget-title',
			'body:not(.page-template-template-dashboard) #after_content > .widget-area .widget:not([class*="widget_grimlock_"]) .widget-title',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the heading 3 font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the display heading 3 font.
	 *
	 * @return array           The updated array of CSS selectors for the display heading 3 font.
	 */
	public function add_heading3_font_elements( $elements ) {
		return array_merge( $elements, array(
			'.posts.archive-posts > article[class*="sfwd-"] h2.entry-title',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the link color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the link color.
	 *
	 * @return array           The updated array of CSS selectors for the link color.
	 */
	public function add_link_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.uppy-Dashboard-browse',
		) );
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the text color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the text color.
	 *
	 * @return array           The updated array of CSS selectors for the text color.
	 */
	public function add_text_color_elements( $elements ) {
		return array_merge( $elements, array(
			'#homepage-anchor',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the text color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the text color.
	 *
	 * @return array          The updated array of CSS selectors for the text color.
	 */
	public function add_text_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.badge-dark',
				) ),
				'property' => 'background-color',
			),
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the heading font.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the heading font.
	 *
	 * @return array          The updated array of CSS selectors for the heading font.
	 */
	public function add_heading_font_outputs( $outputs ) {
		$elements_headings = array(
			'#homepage-anchor',
			'.select-style select',
			'.nav-register a',
			'.media-heading',
			'ul.wpp-list .wpp-meta',
			'ul.wpp-list .post-stats',
			'.widget_grimlock_term_query_section .terms.grimlock-term-query-section__terms [id^="term-"] h2.card__title.entry-title',
			'ul.wpp-list li .wpp-post-title',
			'ul.wpp-list li > a:not(.wpp-post-title):before',
			'#secondary-left .widget-title',
			'#secondary-right .widget-title',
			'.page-template-template-dashboard #main .widget-area .widget .widget-title',
		);

		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-family',
				'choice'   => 'font-family',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'text-transform',
				'choice'   => 'text-transform',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-weight',
				'choice'   => 'font-weight',
			),
			array(
				'element'  => implode( ',', $elements_headings ),
				'property' => 'font-style',
				'choice'   => 'font-style',
			),
		) );
	}



	/**
	 * Add selectors and properties to the CSS rule-set for the link hover.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the link hover.
	 *
	 * @return array          The updated array of CSS selectors for the link hover.
	 */
	public function add_link_hover_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element' => implode( ',', array(
					'ul.wpp-list li > a:not(.wpp-post-title):before',
				) ),
				'property' => 'color',
			),
		) );
	}

	/**
	 * Change control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of control settings for the Customizer controls.
	 *
	 * @return array       The updated array of control settings for the Customizer controls.
	 */
	public function change_text_font_field_args( $args ) {
		$args['choices']['variant'] = array(
			'regular',
			'italic',
			'600',
			'600italic',
			'700',
			'700italic',
		);
		return $args;
	}

	/**
	 * Change control settings for the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of control settings for the Customizer controls.
	 *
	 * @return array       The updated array of control settings for the Customizer controls.
	 */
	public function change_display_heading_font_field_args( $args ) {
		$args['choices']['variant'] = array(
			'300',
			'regular',
			'italic',
			'600',
			'600italic',
			'700',
			'700italic',
		);
		return $args;
	}

}

return new Cera_Grimlock_Typography_Customizer();
