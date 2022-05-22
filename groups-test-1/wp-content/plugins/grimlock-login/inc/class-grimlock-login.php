<?php
/**
 * Class Grimlock_Login
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-login
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main Grimlock Login class.
 */
class Grimlock_Login {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-login', false, 'grimlock-login/languages' );

		add_action( 'login_enqueue_scripts', array( $this, 'login_enqueue_styles'            ), 10    );
		add_action( 'wp_enqueue_scripts',    array( $this, 'enqueue_styles'                  ), 10    );
		add_filter( 'login_title',           array( $this, 'change_title'                    ), 10, 2 );
		add_filter( 'login_headerurl',       array( $this, 'change_headerurl'                ), 10, 1 );
		add_filter( 'login_headertext',      array( $this, 'change_headertitle'              ), 10, 1 );
		add_filter( 'login_form_bottom',     array( $this, 'add_lost_password_register_link' ), 10, 2 );

		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-customizer.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-archive-customizer.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-button-customizer.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-control-customizer.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-global-customizer.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-navigation-customizer.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-typography-customizer.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-login-table-customizer.php';

		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/grimlock-login-template-functions.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/grimlock-login-template-hooks.php';

		// Initialize shortcodes.
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/shortcode/class-grimlock-login-form-shortcode.php';

		// Initialize components
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-login-navbar-nav-menu-component.php';
		require_once GRIMLOCK_LOGIN_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-login-form-modal-component.php';

		add_action( 'grimlock_login_navbar_nav_menu', array( $this, 'navbar_nav_menu'  ), 10, 1 );
		add_action( 'grimlock_login_form_modal',      array( $this, 'login_form_modal' ), 10, 1 );

		add_action( 'grimlock_navbar_nav_menu',           array( $this, 'add_navbar_nav_menu'  ), 50, 1 );
		add_action( 'grimlock_hamburger_navbar_nav_menu', array( $this, 'add_navbar_nav_menu'  ), 50, 1 );
		add_action( 'grimlock_vertical_navbar_nav_menu',  array( $this, 'add_navbar_nav_menu'  ), 5, 1 );
		add_action( 'wp_footer',                          array( $this, 'add_login_form_modal' ), 0, 1 );

		// Fix Recaptcha in login modal
		if ( class_exists( 'LoginNocaptcha' ) ) {
			if ( ! is_user_logged_in() &&
			     LoginNocaptcha::valid_key_secret( get_option( 'login_nocaptcha_key' ) ) &&
			     LoginNocaptcha::valid_key_secret( get_option( 'login_nocaptcha_secret' ) ) ) {

				add_action( 'wp_enqueue_scripts', function () {
					LoginNocaptcha::enqueue_scripts_css();
					wp_enqueue_script( 'login_nocaptcha_google_api' );
					wp_enqueue_style( 'login_nocaptcha_css' );
				} );

				add_filter( 'login_form_middle', function( $content ) {
					ob_start();
					LoginNocaptcha::nocaptcha_form();
					$content .= ob_get_clean();
					return $content;
				} );
			}
		}

		// Fix Super Socializer social login buttons in login form
		if ( function_exists( 'the_champ_login_button' ) ) {
			global $theChampLoginOptions;
			if ( isset( $theChampLoginOptions['enableAtLogin'] ) && $theChampLoginOptions['enableAtLogin'] == 1 ) {
				add_filter( 'login_form_middle', function( $content ) {
					return $content . the_champ_login_button( true );
				} );
			}
		}
	}

	/**
	 * Enqueue custom login styles.
	 *
	 * @since 1.0.0
	 */
	public function login_enqueue_styles() {
		wp_enqueue_style( 'grimlock-login-login', GRIMLOCK_LOGIN_PLUGIN_DIR_URL . 'assets/css/login.css', array( 'login' ), GRIMLOCK_LOGIN_VERSION );

		/*
		 * Load login-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-login-login', 'rtl', 'replace' );

		wp_enqueue_style( 'grimlock-login-google-fonts', $this->get_google_fonts_url() );
	}

	/**
	 * Enqueue custom styles.
	 *
	 * @since 1.1.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'grimlock-login', GRIMLOCK_LOGIN_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_LOGIN_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-login', 'rtl', 'replace' );
	}

	/**
	 * Change the login page title.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $login_title The formatted title for the login page.
	 * @param  string $title       The title for the login page.
	 *
	 * @return string        The updated title for the login page.
	 */
	public function change_title( $login_title, $title ) {
		return sprintf( esc_html__( '%1$s – %2$s', 'grimlock-login' ), $title, get_bloginfo( 'name', 'display' ) );
	}

	/**
	 * Change the login header logo link target.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $url The link target for the header logo.
	 *
	 * @return string      The updated link target for the header logo.
	 */
	public function change_headerurl( $url ) {
		return home_url( '/' );
	}

	/**
	 * Change the login header logo link title.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $title The link title for the header logo.
	 *
	 * @return string      The updated link title for the header logo.
	 */
	public function change_headertitle( $title ) {
		return sprintf( __( '%1$s – %2$s', 'grimlock-login' ), get_bloginfo( 'name', 'display' ), get_bloginfo( 'description' ) );
	}

	/**
	 * Add a lost password and register link to WP login form
	 *
	 * @param string $content The HTML content of the login form bottom
	 * @param array $args The array of args for the login form
	 *
	 * @since 1.0.9
	 *
	 * @return string The modified HTML
	 */
	public function add_lost_password_register_link( $content, $args ) {
		ob_start(); ?>

		<div class="login-footer">
			<?php if ( get_option( 'users_can_register' ) ) {
				wp_register( '<div class="login-register">', '</div>', true );
			}
			echo sprintf(
				'<div class="login-lostpassword"><a href="%1$s">%2$s</a></div>',
				esc_url( wp_lostpassword_url() ),
				esc_html__( 'Lost your password?', 'grimlock-login' )
			); ?>
		</div>

		<?php return $content . ob_get_clean();
	}

	/**
	 * Get the URL for the Google Fonts to enqueue.
	 *
	 * @since 1.0.0
	 *
	 * @return string The URL for the Google Fonts.
	 */
	public function get_google_fonts_url() {
		$fonts_url = '';
		$fonts     = apply_filters( 'grimlock_login_fonts', array() );

		// Extract font families with desired font weights and look for duplicate.
		$font_families = array();

		foreach ( $fonts as $font ) {
			if ( ! empty( $font['font-family'] ) && 1 === count( explode( ',', $font['font-family'] ) ) &&
			     ! in_array( $font['font-family'], array( 'initial', 'inherit', 'serif', 'sans-serif', 'monospace' ) ) ) {
				if ( ! array_key_exists( $font['font-family'], $font_families ) ) {
					$font_families[ $font['font-family'] ] = array(
						'font-family' => $font['font-family'],
						'variant'     => array( isset( $font['variant'] ) ? $font['variant'] : $font['font-weight'] ),
					);
				} else {
					$font_families[ $font['font-family'] ]['variant'][] = isset( $font['variant'] ) ? $font['variant'] : $font['font-weight'];

					// Sort font weights ascending.
					asort( $font_families[ $font['font-family'] ]['variant'] );
				}
			}
		}

		if ( ! empty( $font_families ) ) {

			// Cast array to string.
			foreach ( $font_families as $font_family ) {
				$font_families[ $font_family['font-family'] ] = str_replace( ' ', '+', $font_family['font-family'] . ':' . implode( ',', $font_family['variant'] ) );
			}

			// Make Google Fonts URL parameters.
			$query_args = array(
				'family' => implode( '|', $font_families ),
				'subset' => 'latin,latin-ext',
			);

			$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
		}

		return esc_url_raw( $fonts_url );
	}

	/**
	 * Display the Grimlock Login Navbar Nav Menu Component.
	 *
	 * @param array $args
	 * @since 1.0.3
	 */
	public function navbar_nav_menu( $args = array() ) {
		$args = apply_filters( 'grimlock_login_navbar_nav_menu_args', wp_parse_args( $args, array(
			'id' => 'login-navbar_nav_menu',
		) ) );
		$component = new Grimlock_Login_Navbar_Nav_Menu_Component( $args );
		$component->render();
	}

	/**
	 * Display the login form modal component.
	 *
	 * @param array $args
	 * @since 1.0.3
	 */
	public function login_form_modal( $args = array() ) {
		$component = new Grimlock_Login_Form_Modal_Component( apply_filters( 'grimlock_login_form_modal_args', (array) $args ) );
		$component->render();
	}

	/**
	 * Add navbar login and register buttons for the Grimlock Navbar.
	 *
	 * @param $args
	 * @since 1.0.3
	 */
	public function add_navbar_nav_menu( $args ) {
		$class = isset( $args['menu_class'] ) ? str_replace( 'main-menu', 'login', $args['menu_class'] ) : '';
		do_action( 'grimlock_login_navbar_nav_menu', array(
			'class' => $class,
		) );
	}

	/**
	 * Add login form modal in wp_footer
	 *
	 * @since 1.0.3
	 */
	public function add_login_form_modal() {
		do_action( 'grimlock_login_form_modal' );
	}
}
