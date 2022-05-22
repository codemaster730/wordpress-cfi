<?php
/**
 * Grimlock_BuddyPress_Profile_Customizer Class
 *
 * @author   Themosaurus
 * @since    1.3.19
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for BuddyPress.
 */
class Grimlock_BuddyPress_Profile_Customizer extends Grimlock_Base_Customizer {
	public $id;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'profile';
		$this->section = 'grimlock_buddypress_profile_section';
		$this->title   = esc_html__( 'Profile', 'grimlock-buddypress' );

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'body_class',                                             array( $this, 'add_body_classes'                      ), 10, 1 );
		add_filter( 'bp_core_avatar_thumb',                                   array( $this, 'change_default_member_avatar'          ), 20, 2 );
		add_filter( 'bp_core_default_avatar',                                 array( $this, 'change_default_member_avatar'          ), 20, 2 );
		add_filter( 'bp_before_members_cover_image_settings_parse_args',      array( $this, 'change_members_cover_image_settings'   ), 20, 1 );
		add_filter( 'grimlock_buddypress_member_displayed_name',              array( $this, 'member_displayed_name'                 ), 10, 0 );
		add_action( 'grimlock_buddypress_member_header_author_bio_displayed', array( $this, 'is_member_header_author_bio_displayed' ), 10, 1 );
		add_filter( 'grimlock_buddypress_profile_layout',                     array( $this, 'get_profile_layout'                    ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_profile_customizer_defaults', array(
			'default_member_avatar'                         => '',
			'default_profile_cover_image'                   => '',
			'member_displayed_name'                         => 'username',
			'profile_header_author_bio_displayed'           => false,
			'profile_nav_mobile_default_state'              => 'closed',
			'profile_secondary_nav_mobile_text_displayed'   => true,

			'profile_layout'                                => 'inside-9-3-cols-left',

			'profile_header_background_color'               => GRIMLOCK_BRAND_PRIMARY,
			'profile_header_text_color'                     => '#ffffff',
		) );

		// TODO: remove deprecated defaults filter
		$this->defaults = apply_filters( 'grimlock_buddypress_customizer_defaults', $this->defaults );

		$this->add_section();

		// General Tab
		$this->add_default_member_avatar_field(               array( 'priority' => 100 ) );
		if ( ! bp_disable_cover_image_uploads() ) {
			$this->add_default_profile_cover_image_field(     array( 'priority' => 110 ) );
		}
		$this->add_member_displayed_name_field(               array( 'priority' => 120 ) );
		$this->add_header_author_bio_displayed_field(         array( 'priority' => 130 ) );
		$this->add_nav_mobile_default_state_field(            array( 'priority' => 140 ) );
		$this->add_secondary_nav_mobile_text_displayed_field( array( 'priority' => 150 ) );

		// Layout Tab
		$this->add_layout_field(                              array( 'priority' => 200 ) );

		// Style Tab
		$this->add_header_background_color_field(             array( 'priority' => 210 ) );
		$this->add_header_text_color_field(                   array( 'priority' => 220 ) );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][ $this->section ] = array(
			array(
				'label'    => esc_html__( 'General', 'grimlock-buddypress' ),
				'class'    => 'profile-general-tab',
				'controls' => array(
					'default_member_avatar',
					'default_profile_cover_image',
					'member_displayed_name',
					'profile_header_author_bio_displayed',
					'profile_nav_mobile_default_state',
					'profile_secondary_nav_mobile_text_displayed',
				),
			),
			array(
				'label'    => esc_html__( 'Layout', 'grimlock-buddypress' ),
				'class'    => 'profile-layout-tab',
				'controls' => array(
					'profile_layout',
				),
			),
			array(
				'label'    => esc_html__( 'Style', 'grimlock-buddypress' ),
				'class'    => 'profile-style-tab',
				'controls' => array(
					'profile_header_background_color',
					'profile_header_text_color',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki color field to set the background color of the profile header in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_header_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_buddypress_background_color_elements', array(
				'#buddypress #header-cover-image',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_background_color_outputs', array(
				$this->get_css_var_output( 'profile_header_background_color' ),
				array(
					'element'  => implode( ',', $elements ),
					'property' => 'background-color',
					'suffix'   => '!important',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Profile Header Background Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'profile_header_background_color',
				'default'   => $this->get_default( 'profile_header_background_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color of the profile header text in the Customizer.
	 *
	 * @since 1.0.6
	 *
	 * @param array $args
	 */
	protected function add_header_text_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$elements = apply_filters( 'grimlock_buddypress_profile_header_text_color_elements', array(
				'#buddypress:not(.youzer) div#item-header #profile-header',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_profile_header_text_color_outputs', array(
				$this->get_css_var_output( 'profile_header_text_color' ),
				array(
					'element'  => $elements,
					'property' => 'color',
					'suffix'   => '!important',
				),
				array(
					'element'  => implode( ',', array(
						'#buddypress:not(.youzer) #members-following-personal-li > a',
						'#buddypress:not(.youzer) #members-followers-personal-li > a',
						'#buddypress:not(.youzer) #members-following-personal-li > a:hover',
						'#buddypress:not(.youzer) #members-followers-personal-li > a:hover',
					) ),
					'property'    => 'color',
					'suffix'      => '!important',
					'media_query' => '@media (min-width: 992px)',
				),
			), $elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Profile Header Text Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'profile_header_text_color',
				'default'   => $this->get_default( 'profile_header_text_color' ),
				'choices'   => array(
					'alpha'    => true,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_header_text_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki section.
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => isset( $args['priority'] ) ? $args['priority'] :  10,
				'panel'    => 'grimlock_buddypress_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki image field to set the default member avatar
	 *
	 * @param array $args
	 * @since 1.0.6
	 */
	protected function add_default_member_avatar_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Default Member Avatar', 'grimlock-buddypress' ),
				'settings' => 'default_member_avatar',
				'default'  => $this->get_default( 'default_member_avatar' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_default_member_avatar_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the default cover image for the BuddyPress profiles
	 *
	 * @param array $args
	 * @since 1.0.6
	 */
	protected function add_default_profile_cover_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Default Profile Cover', 'grimlock-buddypress' ),
				'settings' => 'default_profile_cover_image',
				'default'  => $this->get_default( 'default_profile_cover_image' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_default_profile_cover_image_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki select control to change the name format displayed in members profiles
	 *
	 * @param array $args
	 * @since 1.0.6
	 */
	protected function add_member_displayed_name_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'select',
				'section'  => $this->section,
				'label'    => esc_html__( 'Displayed Name on Profile', 'grimlock-buddypress' ),
				'settings' => 'member_displayed_name',
				'default'  => $this->get_default( 'member_displayed_name' ),
				'priority' => 10,
				'choices'  => array(
					'fullname'          => esc_html__( 'Full Name', 'grimlock-buddypress' ),
					'username'          => esc_html__( 'Username', 'grimlock-buddypress' ),
					'fullname_username' => esc_html__( 'Full Name + Username', 'grimlock-buddypress' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_member_displayed_name_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the author bio is displayed in the profile header.
	 *
	 * @since 1.3.5
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_header_author_bio_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Replace Last Activity by Author Bio in Profile Header', 'grimlock-buddypress' ),
				'settings'    => 'profile_header_author_bio_displayed',
				'default'     => $this->get_default( 'profile_header_author_bio_displayed' ),
				'priority'    => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_header_author_bio_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether secondary nav text is displayed on mobile
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_secondary_nav_mobile_text_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display secondary nav text on mobile', 'grimlock-buddypress' ),
				'description' => esc_html__( 'When unchecked, only icons will be displayed on mobile.', 'grimlock-buddypress' ),
				'settings'    => 'profile_secondary_nav_mobile_text_displayed',
				'default'     => $this->get_default( 'profile_secondary_nav_mobile_text_displayed' ),
				'priority'    => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_secondary_nav_mobile_text_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio field set the default state of the profile nav on mobile
	 *
	 * @param array $args
	 */
	public function add_nav_mobile_default_state_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'radio',
				'label'     => esc_html__( 'Mobile navigation default state', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'profile_nav_mobile_default_state',
				'default'   => $this->get_default( 'profile_nav_mobile_default_state' ),
				'priority'  => 10,
				'choices'   => array(
					'open'   => esc_attr__( 'Open', 'grimlock-buddypress' ),
					'closed' => esc_attr__( 'Closed', 'grimlock-buddypress' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_nav_mobile_default_state_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the template content layout in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Sidebars', 'grimlock-buddypress' ),
				'settings' => "{$this->id}_layout",
				'default'  => $this->get_default( "{$this->id}_layout" ),
				'priority' => 10,
				'choices'  => array(
					'inside-3-6-3-cols-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-3-6-3-cols-left.png',
					'inside-12-cols-left'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-12-cols-left.png',
					'inside-3-9-cols-left'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-3-9-cols-left.png',
					'inside-9-3-cols-left'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/template-content-9-3-cols-left.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_{$this->id}_customizer_layout_field_args", $args ) );
		}
	}

	/**
	 * Add custom classes to the body
	 *
	 * @since 1.4.0
	 * @param array $classes The array of body classes.
	 *
	 * @return array The updated array of body classes.
	 */
	public function add_body_classes( $classes ) {
		$profile_secondary_nav_text_displayed = $this->get_theme_mod( 'profile_secondary_nav_mobile_text_displayed' );

		if ( ! empty( $profile_secondary_nav_text_displayed ) ) {
			$classes[] = 'grimlock-buddypress--profile-secondary-nav-mobile-text-displayed';
		}

		$profile_nav_mobile_state = isset( $_COOKIE['grimlock_buddypress_profile_nav_mobile_state'] ) ? sanitize_text_field( $_COOKIE['grimlock_buddypress_profile_nav_mobile_state'] ) : '';
		if ( ! in_array( $profile_nav_mobile_state, array( 'open', 'closed' ) ) ) {
			$profile_nav_mobile_state = $this->get_theme_mod( 'profile_nav_mobile_default_state' );
		}

		$classes[] = 'grimlock-buddypress--profile-nav-mobile-default-state-closed';
		if ( $profile_nav_mobile_state === 'open' ) {
			$classes[] = 'grimlock-buddypress--profile-nav-mobile-default-state-open';
		}

		return $classes;
	}

	/**
	 * Change the default member avatar
	 *
	 * @param string $avatar Avatar url
	 * @param array $params Array of avatar params
	 *
	 * @return string
	 */
	public function change_default_member_avatar( $avatar, $params ) {
		if ( ( ! isset( $params['object'] ) || 'user' === $params['object'] ) && ! empty( $this->get_theme_mod( 'default_member_avatar' ) ) ) {
			$avatar = esc_url( $this->get_theme_mod( 'default_member_avatar' ) );
		}

		return $avatar;
	}

	/**
	 * Change the settings for the BuddyPress cover image.
	 *
	 * @param array $settings The array of default settings for the BuddyPress cover image.
	 *
	 * @return array           The array of settings for the BuddyPress cover image.
	 */
	public function change_members_cover_image_settings( $settings = array() ) {
		$settings['default_cover'] = $this->get_theme_mod( 'default_profile_cover_image' );
		$settings['width']         = get_custom_header()->width;
		$settings['height']        = get_custom_header()->height;
		return $settings;
	}

	/**
	 * Return the type of name that should be displayed on BuddyPress profiles
	 *
	 * @return string The type of name that should be displayed
	 */
	public function member_displayed_name() {
		return $this->get_theme_mod( 'member_displayed_name' );
	}

	/**
	 * Return whether the author bio should be displayed in the member header
	 *
	 * @since 1.3.5
	 *
	 * @param  bool $default The value for the text display.
	 *
	 * @return bool          True if the bio needs to be displayed, false otherwise.
	 */
	public function is_member_header_author_bio_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'profile_header_author_bio_displayed' );
	}

	/**
	 * Return the layout for the member profile.
	 *
	 * @param  string $layout The layout for the member profile.
	 *
	 * @return string         The updated layout for the member profile.
	 */
	public function get_profile_layout( $layout ) {
		return $this->get_theme_mod( 'profile_layout' );
	}
}

return new Grimlock_BuddyPress_Profile_Customizer();
