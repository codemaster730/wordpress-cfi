<?php
/**
 * Cera_Grimlock_Global_Customizer Class
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
 * The background image class for the Customizer.
 */
class Cera_Grimlock_Global_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_global_customizer_defaults',                          array( $this, 'change_defaults'                       ), 10, 1 );
		add_filter( 'grimlock_global_customizer_content_background_color_elements', array( $this, 'add_content_background_color_elements' ), 10, 1 );
		add_filter( 'grimlock_global_customizer_content_background_color_outputs',  array( $this, 'add_content_background_color_outputs'  ), 10, 1 );

		add_filter( 'grimlock_global_customizer_wrapper_layout_field_args',         array( $this, 'change_wrapper_layout_field_args'      ), 10, 1 );

		add_action( 'wp_enqueue_scripts',                                           array( $this, 'enqueue_styles'                        ), 1000  );
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
		$defaults['background_color']         = CERA_BODY_BACKGROUND;
		$defaults['wrapper_layout']           = CERA_WRAPPER_LAYOUT;
		$defaults['content_background_color'] = CERA_CONTENT_BACKGROUND;
		return $defaults;
	}

	/**
	 * Add CSS selectors to the array of CSS selectors for the content background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $elements The array of CSS selectors for the content background color.
	 *
	 * @return array           The updated array of CSS selectors for the content background color.
	 */
	public function add_content_background_color_elements( $elements ) {
		return array_merge( $elements, array(
			'.before_content',
			'.after_content',
			'#content',
		) );
	}

	/**
	 * Add selectors and properties to the CSS rule-set for the content background color.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $outputs The array of CSS selectors and properties for the content background color.
	 *
	 * @return array          The updated array of CSS selectors for the content background color.
	 */
	public function add_content_background_color_outputs( $outputs ) {
		return array_merge( $outputs, array(
			array(
				'element'  => implode( ',', array(
					'.card .author img:hover',
				) ),
				'property' => 'border-color',
			),
			array(
				'element'  => implode( ',', array(
					'.badge-dark',
				) ),
				'property' => 'color',
			),
			array(
				'element'  => implode( ',', array(
					'.bg-light',
					'a.bg-light:hover',
					'a.bg-light:focus',
				) ),
				'property' => 'background-color',
				'suffix'   => '!important',
			),
		) );
	}

	/**
	 * Change wrapper layout field args
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of field args
	 *
	 * @return array       The updated array of field args
	 */
	public function change_wrapper_layout_field_args( $args ) {
		unset( $args['choices']['bordered'] );

		return $args;
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$background_color = get_theme_mod( 'background_color' );
		if ( empty( $background_color ) ) {
			$background_color = '#' . get_theme_mod( 'background_color', CERA_BODY_BACKGROUND );
			$styles           = "
			body,
			body:after {
				background-color: {$background_color};
			}";
			wp_add_inline_style( apply_filters( 'grimlock_stylesheet', 'kirki-styles-grimlock' ), $styles );
		}
	}
}

return new Cera_Grimlock_Global_Customizer();
