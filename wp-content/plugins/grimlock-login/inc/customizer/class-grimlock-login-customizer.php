<?php
/**
 * Grimlock_Login_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.3
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the single product.
 */
class Grimlock_Login_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.3
	 */
	public function __construct() {
		$this->id      = 'login';
		$this->section = 'grimlock_login_customizer_section';
		$this->title   = esc_html__( 'Login', 'grimlock-login' );

		add_action( 'after_setup_theme',                   array( $this, 'add_customizer_fields'               ), 30    );

		add_filter( 'body_class',                          array( $this, 'add_body_classes'                    ), 10, 1 );
		add_filter( 'login_body_class',                    array( $this, 'add_login_body_classes'              ), 10, 1 );
		add_filter( 'grimlock_login_navbar_nav_menu_args', array( $this, 'add_login_navbar_nav_menu_args'      ), 10, 1 );
		add_filter( 'grimlock_login_form_modal_args',      array( $this, 'add_login_form_modal_args'           ), 10, 1 );
		add_filter( 'grimlock_login_custom_logo',          array( $this, 'change_login_custom_logo'            ), 10, 1 );
		add_filter( 'grimlock_login_custom_logo_size',     array( $this, 'change_login_custom_logo_size'       ), 10, 1 );

		add_filter( 'login_errors',                        array( $this, 'disable_login_errors_in_customizer'  ), 10, 1 );
		add_action( 'login_footer',                        array( $this, 'login_customize_preview_back_button' ) );
		add_action( 'customize_preview_init',              array( $this, 'login_customize_preview_init'        ), 10, 1 );

		add_action( 'login_enqueue_scripts',               array( $this, 'add_dynamic_css'                     ), 20    );
		add_action( 'customize_controls_print_scripts',    array( $this, 'add_scripts'                         ), 30, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.3
	 */
	public function add_customizer_fields() {

		$this->defaults = apply_filters( 'grimlock_login_customizer_defaults', array(
			'navbar_nav_menu_login_register_buttons_displayed' => false,
			'navbar_nav_menu_login_button_action'              => 'modal',
			'login_layout'                                     => 'classic',
			'login_custom_logo'                                => '',
			'login_custom_logo_size'                           => 125,
			'login_modal_custom_logo_displayed'                => false,
			'login_custom_logo_background_color'               => '#ffffff',
			'login_background_image'                           => '',
		) );

		$this->add_section();

		$this->add_navbar_nav_menu_login_register_buttons_displayed_field( array( 'priority' => 10 ) );
		$this->add_navbar_nav_menu_login_button_action_field(              array( 'priority' => 20 ) );
		$this->add_divider_field(                                          array( 'priority' => 30 ) );
		$this->add_layout_field(                                           array( 'priority' => 30 ) );
		$this->add_divider_field(                                          array( 'priority' => 40 ) );
		$this->add_custom_logo_field(                                      array( 'priority' => 40 ) );
		$this->add_custom_logo_size_field(                                 array( 'priority' => 50 ) );
		$this->add_custom_logo_background_color_field(                     array( 'priority' => 60 ) );
		$this->add_modal_custom_logo_displayed_field(                      array( 'priority' => 70 ) );
		$this->add_divider_field(                                          array( 'priority' => 80 ) );
		$this->add_background_image_field(                                 array( 'priority' => 80 ) );
	}

	/**
	 * Add custom classes to body when the login and register buttons are displayed.
	 *
	 * @param $classes
	 * @since 1.0.3
	 *
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		if ( ! empty( $this->get_theme_mod( 'navbar_nav_menu_login_register_buttons_displayed' ) ) && ! is_user_logged_in() ) {
			$classes[] = 'grimlock--navigation-login-displayed';
		}

		return $classes;
	}

	/**
	 * Add custom classes to the login page body.
	 *
	 * @param $classes
	 * @since 1.0.9
	 *
	 * @return array
	 */
	public function add_login_body_classes( $classes ) {
		$classes[] = "grimlock-login--{$this->get_theme_mod( 'login_layout' )}";

		return $classes;
	}

	/**
	 * Add a Kirki checkbox field to set the login and register buttons display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.3
	 */
	public function add_navbar_nav_menu_login_register_buttons_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'label'       => esc_html__( 'Display login and register buttons in the navbar', 'grimlock-login' ),
				'description' => esc_html__( 'The buttons are only visible for logged out users, therefore it will not show in the preview. The register button will only be displayed if the "Anyone can register" option is enabled in your general settings.', 'grimlock-login' ),
				'section'     => $this->section,
				'settings'    => 'navbar_nav_menu_login_register_buttons_displayed',
				'default'     => $this->get_default( 'navbar_nav_menu_login_register_buttons_displayed' ),
				'priority'    => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_login_customizer_navbar_nav_menu_login_register_buttons_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki select control to set the navbar login button action in the Customizer.
	 *
	 * @since 1.1.5
	 *
	 * @param array $args
	 */
	protected function add_navbar_nav_menu_login_button_action_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'select',
				'section'     => $this->section,
				'label'       => esc_html__( 'Login button behaviour', 'grimlock' ),
				'description' => esc_html__( 'Change the login button behaviour and/or login form display', 'grimlock-login' ),
				'settings'    => 'navbar_nav_menu_login_button_action',
				'default'     => empty( get_theme_mod( 'navbar_nav_menu_login_button_display_modal', 'default' ) ) ? 'link' : $this->get_default( 'navbar_nav_menu_login_button_action' ), // Condition added for back-compat (19/07/2021). TODO: remove condition in future version
				'active_callback' => array(
					array(
						'setting'  => 'navbar_nav_menu_login_register_buttons_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority'    => 10,
				'choices'     => array(
					'link'     => esc_html__( 'Link to login page', 'grimlock-login' ),
					'modal'    => esc_html__( 'Display login form in a modal', 'grimlock-login' ),
					'dropdown' => esc_html__( 'Display login form in a dropdown', 'grimlock-login' ),
					'inline'   => esc_html__( 'Display login form directly in the navbar', 'grimlock-login' )
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_login_customizer_navbar_nav_menu_login_button_action_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the custom logo for the login screen in the Customizer.
	 *
	 * @param array $args
	 * @since 1.1.2
	 */
	protected function add_custom_logo_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Custom Logo', 'grimlock-login' ),
				'settings' => 'login_custom_logo',
				'default'  => $this->get_default( 'login_custom_logo' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_login_customizer_custom_logo_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki slider control to set the size for the login custom logo.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_custom_logo_size_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Custom Logo Size', 'grimlock-login' ),
				'settings'  => 'login_custom_logo_size',
				'default'   => $this->get_default( 'login_custom_logo_size' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 400,
					'step'  => 5,
				),
				'priority'  => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_login_customizer_custom_logo_size_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the custom logo background color in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_custom_logo_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Custom Logo Background Color', 'grimlock-login' ),
				'section'   => $this->section,
				'settings'  => 'login_custom_logo_background_color',
				'default'   => $this->get_default( 'login_custom_logo_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_login_customizer_custom_logo_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field to choose whether the login modal should display the login logo
	 *
	 * @param array $args
	 * @since 1.1.5
	 */
	public function add_modal_custom_logo_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'            => 'checkbox',
				'label'           => esc_html__( 'Display custom logo in login modal', 'grimlock-login' ),
				'section'         => $this->section,
				'settings'        => 'login_modal_custom_logo_displayed',
				'default'         => $this->get_default( 'login_modal_custom_logo_displayed' ),
				'active_callback' => array(
					array(
						'setting'  => 'navbar_nav_menu_login_button_action',
						'operator' => '==',
						'value'    => 'modal',
					),
				),
				'priority'        => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_login_customizer_modal_custom_logo_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the region in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.9
	 */
	protected function add_background_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Background Image', 'grimlock-login' ),
				'settings' => 'login_background_image',
				'default'  => $this->get_default( 'login_background_image' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_login_customizer_background_image_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.9
	 */
	protected function add_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Layout', 'grimlock-login' ),
				'settings' => 'login_layout',
				'default'  => $this->get_default( 'login_layout' ),
				'priority' => 10,
				'choices'  => array(
					'classic'          => GRIMLOCK_LOGIN_PLUGIN_DIR_URL . 'assets/images/form-boxed.png',
					'fullscreen-left'  => GRIMLOCK_LOGIN_PLUGIN_DIR_URL . 'assets/images/form-full-screen-left.png',
					'fullscreen-right' => GRIMLOCK_LOGIN_PLUGIN_DIR_URL . 'assets/images/form-full-screen-right.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_login_customizer_layout_field_args', $args ) );
		}
	}

	/**
	 * Add arguments using theme mods to the login nav menu component
	 *
	 * @since 1.0.3
	 *
	 * @param array $args The default arguments to render the component
	 *
	 * @return array      The arguments to render the component.
	 */
	public function add_login_navbar_nav_menu_args( $args ) {
		$args['displayed']           = $this->get_theme_mod( 'navbar_nav_menu_login_register_buttons_displayed' );
		$args['login_button_action'] = $this->get_theme_mod( 'navbar_nav_menu_login_button_action' );
		return $args;
	}

	/**
	 * Add arguments using theme mods to the login form modal component
	 *
	 * @since 1.1.1
	 *
	 * @param array $args The default arguments to render the component
	 *
	 * @return array      The arguments to render the component.
	 */
	public function add_login_form_modal_args( $args ) {
		$args['displayed']             = $this->get_theme_mod( 'navbar_nav_menu_login_button_action' ) === 'modal' && $this->get_theme_mod( 'navbar_nav_menu_login_register_buttons_displayed' );
		$args['custom_logo_displayed'] = $this->get_theme_mod( 'login_modal_custom_logo_displayed' );
		$args['custom_logo']           = $this->get_theme_mod( 'login_custom_logo' );
		return $args;
	}

	/**
	 * Change the url of the custom logo for the login screen
	 *
	 * @since 1.1.2
	 *
	 * @param string $logo_url The default logo url
	 *
	 * @return string The updated logo url
	 */
	public function change_login_custom_logo( $logo_url ) {
		return $this->get_theme_mod( 'login_custom_logo' );
	}

	/**
	 * Change the size of the custom logo for the login screen
	 *
	 * @since 1.1.2
	 *
	 * @param string $logo_size The default logo size
	 *
	 * @return string The updated logo size
	 */
	public function change_login_custom_logo_size( $logo_size ) {
		return $this->get_theme_mod( 'login_custom_logo_size' );
	}

	/**
	 * Disable login errors when customizing the login page
	 *
	 * @param string $error The error message
	 *
	 * @return string
	 */
	public function disable_login_errors_in_customizer( $error ) {
		if ( is_customize_preview() ) {
			return '';
		}

		return $error;
	}

	public function login_customize_preview_back_button() {
		if ( is_customize_preview() ) : ?>
			<a href="<?php echo esc_url( home_url() ); ?>" class="button" style="z-index: 10000; position: fixed; bottom: 15px;">
				<?php esc_html_e( 'Return to frontend', 'grimlock-login' ); ?>
			</a>
		<?php endif;
	}

	/**
	 * Do some init for the login customizer preview
	 *
	 * @param WP_Customize_Manager $customize_manager
	 */
	public function login_customize_preview_init( $customize_manager ) {
		add_action( 'login_head',   array( $customize_manager, 'customize_preview_loading_style'            ) );
		add_action( 'login_head',   array( $customize_manager, 'remove_frameless_preview_messenger_channel' ) );
		add_action( 'login_footer', array( $customize_manager, 'customize_preview_settings'                 ), 20 );

		// Kirki CSS vars
		if ( class_exists( 'Kirki_Modules_CSS_Vars' ) ) {
			add_action( 'login_head', array( Kirki_Modules_CSS_Vars::get_instance(), 'the_style' ), 999 );
		}
	}

	/**
	 * Add custom styles based on theme mods.
	 *
	 * @since 1.0.9
	 */
	public function add_dynamic_css() {
		$styles = '';

		$custom_logo_url = apply_filters( 'grimlock_login_custom_logo', $this->get_theme_mod( 'login_custom_logo' ) );

		// If there's no login custom logo, try to fallback on the site logo
		if ( empty( $custom_logo_url ) ) {
			$custom_logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );

			if ( is_array( $custom_logo ) ) {
				$custom_logo_url = esc_url( $custom_logo[0] );
			}
		}

		$styles .= "
		:root {
			--grimlock-login-custom-logo: url('{$custom_logo_url}');
			--grimlock-login-custom-logo-background-color: {$this->get_theme_mod( 'login_custom_logo_background_color' )};
			--grimlock-login-custom-logo-size: {$this->get_theme_mod( 'login_custom_logo_size' )}px;
		}";

		$background_image_url = $this->get_theme_mod( 'login_background_image' );

		if ( ! empty( $background_image_url ) ) {
			$styles .= "
			body.login {
				background-image: url('{$background_image_url}');
			}
			body.login:after {
				opacity: 1;
			}
			body.login #login #backtoblog {
				color: #fff !important;
			}
			body.login #login {
				border: 0 !important;
			}
			body.login:before {
				border: 0 !important;
			}
			body.login #login .privacy-policy-page-link a,
			body.login #login .privacy-policy-page-link a:hover {
				color: #fff !important;
			    background: rgba(0, 0, 0, 0.3) !important;
			}";
		}

		if ( ! empty( $styles ) ) {
			wp_add_inline_style( 'grimlock-login-login', $styles );
		}
	}

	/**
	 * Add scripts to improve user experience in the customizer
	 */
	public function add_scripts() {
		?>
		<script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                wp.customize.section( '<?php echo esc_js( $this->section ); ?>', function( section ) {
                    section.expanded.bind( function( isExpanded ) {
                        var previewUrl = '<?php echo esc_js( esc_url( home_url( 'wp-login.php?customize-login=grimlock-login' ) ) ); ?>';
                        if ( isExpanded && wp.customize.previewer.previewUrl.get() !== previewUrl ) {
                            wp.customize.previewer.previewUrl.set( previewUrl );
                        }
                    } );
                } );
            } );
		</script>
		<?php
	}
}

return new Grimlock_Login_Customizer();
