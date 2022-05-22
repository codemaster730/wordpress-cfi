<?php
/**
 * Grimlock_BuddyPress_Customizer Class
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
 * The Grimlock Customizer class for BuddyPress.
 */
class Grimlock_BuddyPress_Customizer extends Grimlock_Base_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'buddypress';
		$this->section = 'grimlock_buddypress_section';
		$this->title   = esc_html__( 'Global', 'grimlock-buddypress' );

		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'grimlock_buddypress_navbar_nav_menu_item_friends_displayed',            array( $this, 'is_navbar_nav_menu_item_friends_displayed'            ), 10, 1 );
		add_filter( 'grimlock_buddypress_navbar_nav_menu_item_groups_displayed',             array( $this, 'is_navbar_nav_menu_item_groups_displayed'             ), 10, 1 );
		add_filter( 'grimlock_buddypress_navbar_nav_menu_item_notifications_displayed',      array( $this, 'is_navbar_nav_menu_item_notifications_displayed'      ), 10, 1 );
		add_filter( 'grimlock_buddypress_navbar_nav_menu_item_notifications_list_displayed', array( $this, 'is_navbar_nav_menu_item_notifications_list_displayed' ), 10, 1 );
		add_filter( 'grimlock_buddypress_navbar_nav_menu_item_messages_displayed',           array( $this, 'is_navbar_nav_menu_item_messages_displayed'           ), 10, 1 );
		add_filter( 'grimlock_buddypress_navbar_nav_menu_item_settings_displayed',           array( $this, 'is_navbar_nav_menu_item_settings_displayed'           ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_customizer_defaults', array(
			'navbar_nav_menu_item_friends_displayed'            => true,
			'navbar_nav_menu_item_groups_displayed'             => true,
			'navbar_nav_menu_item_notifications_displayed'      => true,
			'navbar_nav_menu_item_notifications_list_displayed' => false,
			'navbar_nav_menu_item_messages_displayed'           => true,
			'navbar_nav_menu_item_settings_displayed'           => false,
		) );

		$this->add_section();

		// Navigation Tab
		if ( bp_is_active( 'friends' ) ) {
			$this->add_navbar_nav_menu_item_friends_displayed_field(                array( 'priority' => 10 ) );
		}
		if ( bp_is_active( 'groups' ) ) {
			$this->add_navbar_nav_menu_item_groups_displayed_field(                 array( 'priority' => 20 ) );
		}
		if ( bp_is_active( 'notifications' ) ) {
			$this->add_navbar_nav_menu_item_notifications_displayed_field(          array( 'priority' => 30 ) );
			if ( class_exists( 'BuddyDev_BP_Notifications_Widget_Helper' ) ) {
				// $this->add_navbar_nav_menu_item_notifications_list_displayed_field( array( 'priority' => 40 ) ); // TODO: @JP uncomment to activate notification list option
			}
		}
		if ( bp_is_active( 'messages' ) ) {
			$this->add_navbar_nav_menu_item_messages_displayed_field(               array( 'priority' => 50 ) );
		}
		if ( bp_is_active( 'settings' ) ) {
			$this->add_navbar_nav_menu_item_settings_displayed_field(               array( 'priority' => 60 ) );
		}
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
				'label'    => esc_html__( 'Navigation', 'grimlock-buddypress' ),
				'class'    => 'buddypress-navigation-tab',
				'controls' => array(
					'navbar_nav_menu_item_friends_displayed',
					'navbar_nav_menu_item_groups_displayed',
					'navbar_nav_menu_item_notifications_displayed',
					'navbar_nav_menu_item_notifications_list_displayed',
					'navbar_nav_menu_item_messages_displayed',
					'navbar_nav_menu_item_settings_displayed',
				),
			),
//			array(
//				'label'    => esc_html__( 'Layout', 'grimlock-buddypress' ),
//				'class'    => 'buddypress-layout-tab',
//				'controls' => array(
//					// TODO: Add sidebar options
//				),
//			),
		);
		return $js_data;
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the navbar friends icon
	 * need to be displayed or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_navbar_nav_menu_item_friends_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Navbar Friends Icon', 'grimlock-buddypress' ),
				'settings' => 'navbar_nav_menu_item_friends_displayed',
				'default'  => $this->get_default( 'navbar_nav_menu_item_friends_displayed' ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_friends_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the navbar groups icon
	 * need to be displayed or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_navbar_nav_menu_item_groups_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Navbar Groups Icon', 'grimlock-buddypress' ),
				'settings' => 'navbar_nav_menu_item_groups_displayed',
				'default'  => $this->get_default( 'navbar_nav_menu_item_groups_displayed' ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_groups_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the navbar notifications icon
	 * need to be displayed or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_navbar_nav_menu_item_notifications_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Navbar Notifications Icon', 'grimlock-buddypress' ),
				'settings' => 'navbar_nav_menu_item_notifications_displayed',
				'default'  => $this->get_default( 'navbar_nav_menu_item_notifications_displayed' ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_notifications_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the navbar notifications list should be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_navbar_nav_menu_item_notifications_list_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Navbar Notifications List', 'grimlock-buddypress' ),
				'settings' => 'navbar_nav_menu_item_notifications_list_displayed',
				'default'  => $this->get_default( 'navbar_nav_menu_item_notifications_list_displayed' ),
				'active_callback' => array(
					array(
						'setting'  => 'navbar_nav_menu_item_notifications_displayed',
						'operator' => '==',
						'value'    => true,
					),
				),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_notifications_list_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the navbar messages icon
	 * need to be displayed or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_navbar_nav_menu_item_messages_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Navbar Messages Icon', 'grimlock-buddypress' ),
				'settings' => 'navbar_nav_menu_item_messages_displayed',
				'default'  => $this->get_default( 'navbar_nav_menu_item_messages_displayed' ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_messages_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the navbar settings icon
	 * need to be displayed or not.
	 *
	 * @since 1.1.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_navbar_nav_menu_item_settings_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display Navbar Settings Icon', 'grimlock-buddypress' ),
				'settings' => 'navbar_nav_menu_item_settings_displayed',
				'default'  => $this->get_default( 'navbar_nav_menu_item_settings_displayed' ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_navbar_nav_menu_item_settings_displayed_field_args', $args ) );
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
			Kirki::add_panel( 'grimlock_buddypress_customizer_panel', array(
				'priority' => 120,
				'title'    => esc_html__( 'BuddyPress', 'grimlock-buddypress' ),
			) );

			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => isset( $args['priority'] ) ? $args['priority'] :  10,
				'panel'    => 'grimlock_buddypress_customizer_panel',
			) ) );
		}
	}

	/**
	 * Check whether navbar friends icon need to be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @param  bool $default The value for the icon display.
	 *
	 * @return bool          True if the icon needs to be displayed, false otherwise.
	 */
	public function is_navbar_nav_menu_item_friends_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'navbar_nav_menu_item_friends_displayed' );
	}

	/**
	 * Check whether navbar groups icon need to be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @param  bool $default The value for the icon display.
	 *
	 * @return bool          True if the icon needs to be displayed, false otherwise.
	 */
	public function is_navbar_nav_menu_item_groups_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'navbar_nav_menu_item_groups_displayed' );
	}

	/**
	 * Check whether navbar notifications icon need to be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @param  bool $default The value for the icon display.
	 *
	 * @return bool          True if the icon needs to be displayed, false otherwise.
	 */
	public function is_navbar_nav_menu_item_notifications_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'navbar_nav_menu_item_notifications_displayed' );
	}

	/**
	 * Check whether navbar notifications list need to be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @param  bool $default The value for the list display.
	 *
	 * @return bool          True if the list needs to be displayed, false otherwise.
	 */
	public function is_navbar_nav_menu_item_notifications_list_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'navbar_nav_menu_item_notifications_list_displayed' );
	}

	/**
	 * Check whether navbar messages icon need to be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @param  bool $default The value for the icon display.
	 *
	 * @return bool          True if the icon needs to be displayed, false otherwise.
	 */
	public function is_navbar_nav_menu_item_messages_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'navbar_nav_menu_item_messages_displayed' );
	}

	/**
	 * Check whether navbar settings icon need to be displayed.
	 *
	 * @since 1.1.0
	 *
	 * @param  bool $default The value for the icon display.
	 *
	 * @return bool          True if the icon needs to be displayed, false otherwise.
	 */
	public function is_navbar_nav_menu_item_settings_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'navbar_nav_menu_item_settings_displayed' );
	}
}

return new Grimlock_BuddyPress_Customizer();
