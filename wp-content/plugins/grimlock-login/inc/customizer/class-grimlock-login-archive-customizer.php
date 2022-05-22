<?php
/**
 * Grimlock_Login_Archive_Customizer Class
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
 * The Grimlock Login Archive Customizer class.
 */
class Grimlock_Login_Archive_Customizer extends Grimlock_Archive_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
	}

	/**
	 * Enqueue custom styles based on theme mods.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
		$styles = "
		#login,
		 .language-switcher  {
			padding: {$this->get_theme_mod( 'archive_post_padding' )}px;
			border-radius: {$this->get_theme_mod( 'archive_post_border_radius' )}rem;
			border-width: {$this->get_theme_mod( 'archive_post_border_width' )}px;
			border-color: {$this->get_theme_mod( 'archive_post_border_color' )};
			border-style: solid;
			background-color: {$this->get_theme_mod( 'archive_post_background_color' )};
		}
		.login #wfls-prompt-overlay {
			background-color: {$this->get_theme_mod( 'archive_post_background_color' )};
		}
		#login:after {
			background-color: {$this->get_theme_mod( 'archive_post_background_color' )};
			box-shadow: 0 0 0 10000px {$this->get_theme_mod( 'archive_post_background_color' )};
		}
		.login #nav {
			border-top-color: {$this->get_theme_mod( 'archive_post_border_color' )};
		}
		#login #login_error,
		#login .message,
		#login .success,
		#login h1.admin-email__heading {
			border-radius: {$this->get_theme_mod( 'archive_post_border_radius' )}rem;
		}
		.login #login > h1 {
			border-top-left-radius: {$this->get_theme_mod( 'archive_post_border_radius' )}rem;
			border-top-right-radius: {$this->get_theme_mod( 'archive_post_border_radius' )}rem;
		}
		.login #nav {
			border-top-width: {$this->get_theme_mod( 'archive_post_border_width' )}px;
			border-bottom-left-radius: {$this->get_theme_mod( 'archive_post_border_radius' )}rem;
			border-bottom-right-radius: {$this->get_theme_mod( 'archive_post_border_radius' )}rem;
		}
		#login a {
			color: {$this->get_theme_mod( 'archive_post_link_color' )} !important;
		}
		#loginform [id*='nsl-custom-login-form'] {
			border-bottom: {$this->get_theme_mod( 'archive_post_border_width' )}px solid {$this->get_theme_mod( 'archive_post_border_color' )};
		}
		#login a:hover,
	    #login a:active,
        #login a:focus {
			color: {$this->get_theme_mod( 'archive_post_link_hover_color' )} !important;
		}
		#login,
		.login,
		.login label,
		 .language-switcher {
			color: {$this->get_theme_mod( 'archive_post_color' )};
		}";
		wp_add_inline_style( 'grimlock-login-login', $styles );
	}
}

return new Grimlock_Login_Archive_Customizer();
