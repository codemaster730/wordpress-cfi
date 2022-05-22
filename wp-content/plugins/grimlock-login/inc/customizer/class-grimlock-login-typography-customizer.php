<?php
/**
 * Grimlock_Login_Typography_Customizer Class
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
 * The Grimlock Login Typography Customizer class.
 */
class Grimlock_Login_Typography_Customizer extends Grimlock_Typography_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_styles' ), 10    );
		add_filter( 'grimlock_login_fonts',  array( $this, 'add_fonts'      ), 10, 1 );
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$text_font = $this->get_theme_mod( 'text_font' );

		// Fix notice in customizer
		$text_font['font-weight'] = ! empty( $text_font['font-weight'] ) ? $text_font['font-weight'] : 'inherit';

		$styles    = "
		::selection { background-color: {$this->get_theme_mod( 'text_selection_background_color' )}; }
		::-moz-selection { background-color: {$this->get_theme_mod( 'text_selection_background_color' )}; }
		.login,
		.login form .input,
		.login input[type=\"text\"],
		.login #backtoblog a {
			font-family: {$text_font['font-family']}, -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, \"Helvetica Neue\", Arial, sans-serif, \"Apple Color Emoji\", \"Segoe UI Emoji\", \"Segoe UI Symbol\", \"Noto Color Emoji\";
			font-size: {$text_font['font-size']};
			font-weight: {$text_font['font-weight']};
		    letter-spacing: {$text_font['letter-spacing']};
		    line-height: {$text_font['line-height']};
		    text-transform: {$text_font['text-transform']};
		    color: {$this->get_theme_mod( 'text_color' )};
		}

		.login .message {
			background-color: {$this->get_theme_mod( 'text_color' )};
		}

		.login label,
		.login form .forgetmenot label {
			font-size: {$text_font['font-size']};
		}

		a {
			color: {$this->get_theme_mod( 'link_color' )};
		}
		a:active,
		a:hover,
		a:focus,
		.login #backtoblog a:hover,
		.login #backtoblog a:focus {
			color: {$this->get_theme_mod( 'link_hover_color' )};
		}";
		wp_add_inline_style( 'grimlock-login-login', $styles );
	}

	/**
	 * Add new fonts to fetch from Google Fonts API.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $fonts The array of fonts.
	 *
	 * @return array       The updated array of fonts.
	 */
	public function add_fonts( $fonts ) {
		$fonts[] = $this->get_theme_mod( 'text_font' );
		return $fonts;
	}
}

return new Grimlock_Login_Typography_Customizer();
