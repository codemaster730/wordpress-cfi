<?php
/**
 * Grimlock_BuddyPress_Members_Customizer Class
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
class Grimlock_BuddyPress_Members_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'members';
		$this->section = 'grimlock_buddypress_members_section';
		$this->title   = esc_html__( 'Members Directory', 'grimlock-buddypress' );

		add_filter( 'body_class',                           array( $this, 'add_body_classes'                ), 10, 1 );
		add_action( 'after_setup_theme',                    array( $this, 'add_customizer_fields'           ), 20    );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'grimlock_custom_header_args',      array( $this, 'add_custom_header_args'      ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed', array( $this, 'has_custom_header_displayed' ), 10, 1 );

		add_filter( 'bp_ajax_querystring',                                            array( $this, 'change_members_query_args'           ), 100, 2 );
		add_filter( 'grimlock_buddypress_members_per_page',                           array( $this, 'members_per_page'                    ), 10,  1 );
		add_filter( 'grimlock_buddypress_members_default_order',                      array( $this, 'members_default_order'               ), 10,  1 );
		add_action( 'grimlock_buddypress_member_xprofile_custom_fields',              array( $this, 'add_member_custom_fields'            ), 10,  1 );
		add_filter( 'grimlock_buddypress_members_actions_text_displayed',             array( $this, 'is_members_actions_text_displayed'   ), 10,  1 );
		add_filter( 'grimlock_buddypress_members_last_activity_displayed',            array( $this, 'is_members_last_activity_displayed'  ), 10,  1 );
		add_filter( 'grimlock_buddypress_bp_profile_search_age_range_slider_enabled', array( $this, 'bps_age_range_slider_enabled'        ), 10,  1 );
		add_filter( 'bp_member_name',                                                 array( $this, 'change_member_name'                  ), 10,  1 );
		add_filter( 'grimlock_content_class',                                         array( $this, 'member_type_directory_content_class' ), 20,  1 );

		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_members_customizer_defaults', array(
			'members_custom_header_displayed'               => false,
			'members_per_page'                              => '24',
			'members_default_order'                         => 'active',
			'members_exclude_logged_in_user'                => false,
			'members_counts_displayed'                      => true,
			'members_displayed_name'                        => 'fullname',
			'members_displayed_profile_fields'              => array(),
			'members_member_types_displayed'                => false,
			'members_actions_text_displayed'                => false,
			'members_last_activity_displayed'               => true,

			'members_custom_header_layout'                  => '12-cols-center',
			'members_custom_header_container_layout'        => 'classic',
			'members_items_layout'                          => '2-2-2-2-2-cols-classic',
			//'members_container_layout'                      => 'fluid', TODO: uncomment if the container layout field is implemented

			'members_custom_header_padding_y'               => GRIMLOCK_SECTION_PADDING_Y,
			'bps_age_range_slider_enabled'                  => false,
			'bps_form_labels_displayed'                     => true,
			'friend_icons'                                  => 'add',
			'follow_icons'                                  => 'add',
			'member_actions_button_background_color'        => '#ffffff',
			'friend_button_background_color'                => '#004085',
			'follow_button_background_color'                => '#155724',
			'message_button_background_color'               => '#0c5460',
			'success_button_background_color'               => '#155724',
			'delete_button_background_color'                => '#721c24',
			'miscellaneous_actions_button_background_color' => '#818182',
		) );

		// TODO: remove deprecated defaults filter
		$this->defaults = apply_filters( 'grimlock_buddypress_customizer_defaults', $this->defaults );

		$this->add_section();

		// General Tab
		$this->add_heading_field(                                       array( 'priority' => 100, 'label' => esc_html__( 'Header Display', 'grimlock-buddypress' ) ) );
		$this->add_custom_header_displayed_field(                       array( 'priority' => 100 ) );
		$this->add_divider_field(                                       array( 'priority' => 110 ) );
		$this->add_per_page_field(                                      array( 'priority' => 110 ) );
		$this->add_default_order_field(                                 array( 'priority' => 120 ) );
		$this->add_exclude_logged_in_user_field(                        array( 'priority' => 130 ) );
		$this->add_counts_displayed_field(                              array( 'priority' => 140 ) );
		$this->add_displayed_name_field(                                array( 'priority' => 150 ) );
		if ( bp_is_active( 'xprofile' ) ) {
			$this->add_displayed_profile_fields_field(                  array( 'priority' => 160 ) );
		}
		$this->add_member_types_displayed_field(                        array( 'priority' => 170 ) );
		$this->add_actions_text_displayed_field(                        array( 'priority' => 180 ) );
		$this->add_last_activity_displayed_field(                       array( 'priority' => 190 ) );

		// Layout Tab
		$this->add_custom_header_layout_field(                          array( 'priority' => 200 ) );
		$this->add_divider_field(                                       array( 'priority' => 210 ) );
		$this->add_custom_header_container_layout_field(                array( 'priority' => 210 ) );
		$this->add_divider_field(                                       array( 'priority' => 220 ) );
		$this->add_items_layout_field(                                  array( 'priority' => 220 ) );
		$this->add_divider_field(                                       array( 'priority' => 240 ) );
		//$this->add_container_layout_field(                              array( 'priority' => 240 ) ); TODO: uncomment if the container layout field is implemented

		// Style Tab
		$this->add_custom_header_padding_y_field(                       array( 'priority' => 300 ) );
		$this->add_divider_field(                                       array( 'priority' => 310 ) );
		if ( function_exists( 'bps_templates' ) ) {
			$this->add_bps_age_range_slider_enabled_field(              array( 'priority' => 310 ) );
			$this->add_bps_form_labels_displayed_field(                 array( 'priority' => 315 ) );
		}
		if ( bp_is_active( 'friends' ) ) {
			$this->add_friend_icons_field(                              array( 'priority' => 320 ) );
		}
		if ( function_exists( 'bp_follow_init' ) ) {
			$this->add_follow_icons_field(                              array( 'priority' => 330 ) );
		}
		$this->add_member_actions_button_background_color_field(        array( 'priority' => 340 ) );
		if ( bp_is_active( 'friends' ) ) {
			$this->add_friend_button_background_color_field(            array( 'priority' => 350 ) );
		}
		if ( function_exists( 'bp_follow_init' ) ) {
			$this->add_follow_button_background_color_field(            array( 'priority' => 360 ) );
		}
		if ( bp_is_active( 'messages' ) ) {
			$this->add_message_button_background_color_field(           array( 'priority' => 370 ) );
		}
		$this->add_success_button_background_color_field(               array( 'priority' => 380 ) );
		$this->add_delete_button_background_color_field(                array( 'priority' => 390 ) );
		$this->add_miscellaneous_actions_button_background_color_field( array( 'priority' => 400 ) );
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
				'label'    => esc_html__( 'Directory', 'grimlock-buddypress' ),
				'class'    => 'members-directory-tab',
				'controls' => array(
					"{$this->section}_heading_100",
					'members_custom_header_displayed',
					"{$this->section}_divider_110",
					'members_per_page',
					'members_default_order',
					'members_exclude_logged_in_user',
					'members_counts_displayed',
					'members_displayed_name',
					'members_displayed_profile_fields',
					'members_member_types_displayed',
					'members_actions_text_displayed',
					'members_last_activity_displayed',
				),
			),
			array(
				'label'    => esc_html__( 'Layout', 'grimlock-buddypress' ),
				'class'    => 'members-layout-tab',
				'controls' => array(
					'members_custom_header_layout',
					"{$this->section}_divider_210",
					'members_custom_header_container_layout',
					"{$this->section}_divider_220",
					'members_items_layout',
					"{$this->section}_divider_240",
					//'members_container_layout' TODO: uncomment if the container layout field is implemented
				),
			),
			array(
				'label'    => esc_html__( 'Style', 'grimlock-buddypress' ),
				'class'    => 'members-style-tab',
				'controls' => array(
					'members_custom_header_padding_y',
					"{$this->section}_divider_310",
					'bps_age_range_slider_enabled',
					'bps_form_labels_displayed',
					'friend_icons',
					'follow_icons',
					'member_actions_button_background_color',
					'friend_button_background_color',
					'follow_button_background_color',
					'message_button_background_color',
					'success_button_background_color',
					'delete_button_background_color',
					'miscellaneous_actions_button_background_color',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.3.19
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		return bp_is_members_directory();
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the member types are displayed.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_member_types_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display member types', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_member_types_displayed",
				'default'     => $this->get_default( "{$this->id}_member_types_displayed" ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_members_customizer_member_types_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the members action text is displayed.
	 *
	 * @since 1.0.5
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_actions_text_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display members actions text', 'grimlock-buddypress' ),
				'description' => esc_html__( 'If this field is checked, BuddyPress action buttons for members lists will have a text in addition to the icon.', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_actions_text_displayed",
				'default'     => $this->get_default( "{$this->id}_actions_text_displayed" ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_members_customizer_actions_text_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the members last activity is displayed.
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_last_activity_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display last activity', 'grimlock-buddypress' ),
				'settings' => "{$this->id}_last_activity_displayed",
				'default'  => $this->get_default( "{$this->id}_last_activity_displayed" ),
				'priority' => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_members_customizer_last_activity_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the members list layout in the Customizer
	 *
	 * @param array $args
	 * @since 1.4.0
	 */
	protected function add_items_layout_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Members Layout', 'grimlock-buddypress' ),
				'settings' => "{$this->id}_items_layout",
				'default'  => $this->get_default( "{$this->id}_items_layout" ),
				'priority' => 10,
				'choices'  => array(
					'2-2-2-2-2-2-cols-classic'         => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-2-2-2-2-2-2-cols-classic.png',
					'2-2-2-2-2-cols-classic'           => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-2-2-2-2-2-cols-classic.png',
					'3-3-3-3-cols-classic'             => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-3-3-3-3-cols-classic.png',
					'4-4-4-cols-classic'               => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-4-4-4-cols-classic.png',
					'6-6-cols-classic'                 => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-6-6-cols-classic.png',
					'12-cols-inline'                   => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/members-items-12-cols-inline.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_items_layout_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki sortable field in the Customizer to choose which member fields are displayed on member cards
	 *
	 * @since 1.0.5
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_displayed_profile_fields_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) && function_exists( 'buddypress' ) && bp_is_active( 'xprofile' ) ) {

			// Initialize necessary BP components
			bp_setup_xprofile();
			$bp = buddypress();
			$bp->core->setup_globals();
			$bp->profile->setup_globals();

			// Get profile field groups
			$field_groups = bp_xprofile_get_groups( array(
				'profile_group_id'       => false,
				'user_id'                => false,
				'member_type'            => false,
				'hide_empty_groups'      => false,
				'hide_empty_fields'      => false,
				'fetch_fields'           => true,
				'fetch_field_data'       => false,
				'fetch_visibility_level' => false,
				'exclude_groups'         => false,
				'exclude_fields'         => false,
				'update_meta_cache'      => true,
			) );

			// Store fields in a key => value array
			$fields = array();
			foreach ( $field_groups as $field_group ) {
				/** @var BP_XProfile_Field $field */
				foreach ( $field_group->fields as $field ) {
					$fields[ $field->id ] = $field->name;
				}
			}

			$args = wp_parse_args( $args, array(
				'type'     => 'sortable',
				'label'    => esc_html__( 'Members Displayed Profile Fields', 'grimlock-buddypress' ),
				'settings' => "{$this->id}_displayed_profile_fields",
				'section'  => $this->section,
				'default'  => array(),
				'choices'  => $fields,
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_displayed_profile_fields_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether a range slider should be used for age range fields in BP Profile Search
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_bps_age_range_slider_enabled_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Enable range slider for age range fields in members search form', 'grimlock-buddypress' ),
				'settings'    => 'bps_age_range_slider_enabled',
				'default'     => $this->get_default( 'bps_age_range_slider_enabled' ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_members_customizer_bps_age_range_slider_enabled_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether a the field labels should be displayed in BP Profile Search forms
	 *
	 * @since 1.4.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_bps_form_labels_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display field labels in members search form', 'grimlock-buddypress' ),
				'settings'    => 'bps_form_labels_displayed',
				'default'     => $this->get_default( 'bps_form_labels_displayed' ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_members_customizer_bps_form_labels_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the icons for friend related buttons in the Customizer.
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 * @since 1.0.5
	 */
	protected function add_friend_icons_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Friend Icons', 'grimlock-buddypress' ),
				'settings' => 'friend_icons',
				'default'  => $this->get_default( 'friend_icons' ),
				'priority' => 10,
				'choices'  => array(
					'add'    => get_template_directory_uri() . '/assets/images/customizer/icons/icon-add.png',
					'person' => get_template_directory_uri() . '/assets/images/customizer/icons/icon-person.png',
					'heart'  => get_template_directory_uri() . '/assets/images/customizer/icons/icon-heart.png',
					'like'   => get_template_directory_uri() . '/assets/images/customizer/icons/icon-like.png',
					'smile'  => get_template_directory_uri() . '/assets/images/customizer/icons/icon-smile.png',
					'star'   => get_template_directory_uri() . '/assets/images/customizer/icons/icon-star.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_friend_icons_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki radio-image field to set the icons for follow related buttons in the Customizer.
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 * @since 1.0.5
	 */
	protected function add_follow_icons_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'radio-image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Follow Icons', 'grimlock-buddypress' ),
				'settings' => 'follow_icons',
				'default'  => $this->get_default( 'follow_icons' ),
				'priority' => 10,
				'choices'  => array(
					'add'    => get_template_directory_uri() . '/assets/images/customizer/icons/icon-add.png',
					'person' => get_template_directory_uri() . '/assets/images/customizer/icons/icon-person.png',
					'heart'  => get_template_directory_uri() . '/assets/images/customizer/icons/icon-heart.png',
					'like'   => get_template_directory_uri() . '/assets/images/customizer/icons/icon-like.png',
					'smile'  => get_template_directory_uri() . '/assets/images/customizer/icons/icon-smile.png',
					'star'   => get_template_directory_uri() . '/assets/images/customizer/icons/icon-star.png',
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_follow_icons_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_member_actions_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$background_color_elements = apply_filters( 'grimlock_buddypress_member_actions_button_background_color_elements', array(
				'.grimlock_buddypress_member_actions_button_background_color_elements_selector',
			) );

			$color_elements = apply_filters( 'grimlock_buddypress_member_actions_button_color_elements', array(
				'.grimlock_buddypress_member_actions_button_color_elements_selector',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_member_actions_button_background_color_outputs', array(
				$this->get_css_var_output( 'member_actions_button_background_color' ),
				array(
					'element'  => implode( ',', $background_color_elements ),
					'property' => 'background',
					'suffix'   => '!important',
				),
				array(
					'element'  => implode( ',', $color_elements ),
					'property' => 'color',
					'suffix'   => '!important',
				),
			), $background_color_elements, $color_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Actions Buttons Background Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => "member_actions_button_background_color",
				'default'   => $this->get_default( "member_actions_button_background_color" ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_member_actions_button_background_color_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_friend_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$color_elements = apply_filters( 'grimlock_buddypress_friend_button_color_elements', array(
				'.grimlock_buddypress_friend_button_color_elements_selector',
			) );

			$background_color_elements = apply_filters( 'grimlock_buddypress_friend_button_background_color_elements', array(
				'.grimlock_buddypress_friend_button_background_color_elements',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_friend_button_background_color_outputs', array(
				$this->get_css_var_output( 'friend_button_background_color' ),
				array(
					'element'  => implode( ',', $color_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $background_color_elements ),
					'property' => 'background-color',
				),
			), $background_color_elements, $color_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Friend Buttons Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'friend_button_background_color',
				'default'   => $this->get_default( 'friend_button_background_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_friend_button_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_follow_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$output = array( $this->get_css_var_output( 'follow_button_background_color' ) );
			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Follow Buttons Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'follow_button_background_color',
				'default'   => $this->get_default( 'follow_button_background_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $output ),
				'output'    => $output,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_follow_button_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_message_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$color_elements = apply_filters( 'grimlock_buddypress_message_button_color_elements', array(
				'.grimlock_buddypress_message_button_color_elements_selector',
			) );

			$background_color_elements = apply_filters( 'grimlock_buddypress_message_button_background_color_elements', array(
				'.grimlock_buddypress_message_button_background_color_elements',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_message_button_background_color_outputs', array(
				$this->get_css_var_output( 'message_button_background_color' ),
				array(
					'element'  => implode( ',', $color_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $background_color_elements ),
					'property' => 'background-color',
				),
			), $background_color_elements, $color_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Message Buttons Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'message_button_background_color',
				'default'   => $this->get_default( 'message_button_background_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_message_button_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_success_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$color_elements = apply_filters( 'grimlock_buddypress_success_button_color_elements', array(
				'.grimlock_buddypress_success_button_color_elements_selector'
			) );

			$background_color_elements = apply_filters( 'grimlock_buddypress_success_button_background_color_elements', array(
				'.grimlock_buddypress_success_button_background_color_elements',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_success_button_background_color_outputs', array(
				$this->get_css_var_output( 'success_button_background_color' ),
				array(
					'element'  => implode( ',', $color_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $background_color_elements ),
					'property' => 'background-color',
				),
			), $background_color_elements, $color_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Success Buttons Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'success_button_background_color',
				'default'   => $this->get_default( 'success_button_background_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_success_button_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_delete_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$color_elements = apply_filters( 'grimlock_buddypress_delete_button_color_elements', array(
				'.grimlock_buddypress_delete_button_color_elements',
				'.card-body-meta .banned',
			) );

			$background_color_elements = apply_filters( 'grimlock_buddypress_delete_button_background_color_elements', array(
				'.grimlock_buddypress_success_button_background_color_elements',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_delete_button_background_color_outputs', array(
				$this->get_css_var_output( 'delete_button_background_color' ),
				array(
					'element'  => implode( ',', $color_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $background_color_elements ),
					'property' => 'background-color',
				),
			), $background_color_elements, $color_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Delete Buttons Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'delete_button_background_color',
				'default'   => $this->get_default( 'delete_button_background_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_delete_button_background_color_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki color field to set the color in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_miscellaneous_actions_button_background_color_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$color_elements = apply_filters( 'grimlock_buddypress_miscellaneous_actions_button_color_elements', array(
				'.grimlock_buddypress_miscellaneous_actions_button_color_elements_selector',
			) );

			$background_color_elements = apply_filters( 'grimlock_buddypress_miscellaneous_actions_button_background_color_elements', array(
				'.grimlock_buddypress_miscellaneous_actions_button_background_color_elements',
			) );

			$background_elements = apply_filters( 'grimlock_buddypress_miscellaneous_actions_button_background_elements', array(
				'.grimlock_buddypress_miscellaneous_actions_button_background_elements_selector',
			) );

			$outputs = apply_filters( 'grimlock_buddypress_miscellaneous_actions_button_background_color_outputs', array(
				$this->get_css_var_output( 'miscellaneous_actions_button_background_color' ),
				array(
					'element'  => implode( ',', $color_elements ),
					'property' => 'color',
				),
				array(
					'element'  => implode( ',', $background_color_elements ),
					'property' => 'background-color',
				),
				array(
					'element'  => implode( ',', $background_elements ),
					'property' => 'background',
					'suffix'   => '!important',
				),
			), $background_color_elements, $color_elements, $background_elements );

			$args = wp_parse_args( $args, array(
				'type'      => 'color',
				'label'     => esc_html__( 'Miscellaneous Buttons Color', 'grimlock-buddypress' ),
				'section'   => $this->section,
				'settings'  => 'miscellaneous_actions_button_background_color',
				'default'   => $this->get_default( 'miscellaneous_actions_button_background_color' ),
				'choices'   => array(
					'alpha'    => false,
					'palettes' => apply_filters( 'grimlock_color_field_palettes', array() ),
				),
				'priority'  => 10,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
				'output'    => $outputs,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_miscellaneous_actions_button_background_color_field_args', $args ) );
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
	 * Add a Kirki select control to change the members per page
	 *
	 * @param array $args
	 * @since 1.0.8
	 */
	protected function add_per_page_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'number',
				'section'   => $this->section,
				'label'     => esc_html__( 'Members per Page', 'grimlock-buddypress' ),
				'description' => esc_html__( 'Insert 0 to remove the page limit.', 'grimlock-buddypress' ),
				'settings'  => "{$this->id}_per_page",
				'default'   => $this->get_default( "{$this->id}_per_page" ),
				'priority'  => 10,
				'transport' => 'refresh',
				'choices'   => array(
					'min'  => 0,
					'step' => 1,
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_per_page_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki select control to change the members default order
	 *
	 * @param array $args
	 * @since 1.0.8
	 */
	protected function add_default_order_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'      => 'select',
				'section'   => $this->section,
				'label'     => esc_html__( 'Default order', 'grimlock-buddypress' ),
				'settings'  => "{$this->id}_default_order",
				'default'   => $this->get_default( "{$this->id}_default_order" ),
				'priority'  => 10,
				'transport' => 'refresh',
				'choices'   => array(
					'active' => esc_html__( 'Last active', 'grimlock-buddypress' ),
					'newest' => esc_html__( 'Newest registered', 'grimlock-buddypress' ),
				),
			) );

			if ( function_exists( 'bp_is_active' ) && bp_is_active( 'xprofile' ) ) {
				$args['choices']['alphabetical'] = esc_html__( 'Alphabetical', 'grimlock-buddypress' );
			}

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_default_order_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the current user should be excluded from the members directory
	 *
	 * @since 1.3.19
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_exclude_logged_in_user_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Hide myself', 'grimlock-buddypress' ),
				'description' => esc_html__( 'Hide the logged in user from the members directory.', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_exclude_logged_in_user",
				'default'     => $this->get_default( "{$this->id}_exclude_logged_in_user" ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_exclude_logged_in_user_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox field in the Customizer to set whether the members counts should be displayed
	 *
	 * @since 1.3.19
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_counts_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki') ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'checkbox',
				'section'     => $this->section,
				'label'       => esc_html__( 'Display members counts', 'grimlock-buddypress' ),
				'settings'    => "{$this->id}_counts_displayed",
				'default'     => $this->get_default( "{$this->id}_counts_displayed" ),
				'priority'    => 20,
			) );

			Kirki::add_field( 'grimlock', apply_filters( "grimlock_buddypress_{$this->id}_customizer_counts_displayed_field_args", $args ) );
		}
	}

	/**
	 * Add a Kirki select control to change the name format displayed in members items
	 *
	 * @param array $args
	 * @since 1.4.5
	 */
	protected function add_displayed_name_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'select',
				'section'  => $this->section,
				'label'    => esc_html__( 'Displayed Name in Members Cards', 'grimlock-buddypress' ),
				'settings' => 'members_displayed_name',
				'default'  => $this->get_default( 'members_displayed_name' ),
				'priority' => 10,
				'choices'  => array(
					'fullname'          => esc_html__( 'Full Name', 'grimlock-buddypress' ),
					'username'          => esc_html__( 'Username', 'grimlock-buddypress' ),
					'fullname_username' => esc_html__( 'Full Name + Username', 'grimlock-buddypress' ),
				),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_profile_customizer_members_displayed_name_field_args', $args ) );
		}
	}

	/**
	 * Change members/groups per page using customizer value
	 *
	 * @param string $query_string The query string used by BuddyPress to build the members/groups query
	 * @param string $object       Whether this query is for members or groups
	 *
	 * @return string The modified query string
	 */
	public function change_members_query_args( $query_string, $object ) {
		if ( ! is_string( $query_string ) || 'members' !== $object ) {
			return $query_string;
		}

		$query_args = explode( '&', $query_string );

		// Handle members per page
		$per_page = $this->get_theme_mod( 'members_per_page' );
		if ( ! empty( $per_page ) || $per_page == 0 ) {
			foreach ( $query_args as $key => $query_arg ) {
				if ( strpos( $query_arg, 'per_page' ) !== false ) {
					unset( $query_args[ $key ] );
					break;
				}
			}

			$query_args[] = "per_page={$per_page}";
		}

		// Handle excluding current user
		$members_exclude_logged_in_user = $this->get_theme_mod( 'members_exclude_logged_in_user' );
		if ( $members_exclude_logged_in_user ) {
			$exclude_added = false;
			foreach ( $query_args as $key => $query_arg ) {
				if ( strpos( $query_arg, 'exclude' ) !== false ) {
					$query_args[ $key ] = $query_arg . ',' . get_current_user_id();
					$exclude_added      = true;
					break;
				}
			}

			if ( ! $exclude_added ) {
				$query_args[] = 'exclude=' . get_current_user_id();
			}
		}

		// Handle default order
		$default_order = $this->get_theme_mod( 'members_default_order' );
		if ( ! empty( $default_order ) ) {
			$has_type = false;
			foreach ( $query_args as $key => $query_arg ) {
				if ( strpos( $query_arg, 'type' ) !== false ) {
					$has_type = true;
					break;
				}
			}

			if ( ! $has_type ) {
				$query_args[] = "type={$default_order}";
			}
		}

		$query_string = implode( '&', $query_args );

		return $query_string;
	}

	/**
	 * Return members per page
	 */
	public function members_per_page() {
		return $this->get_theme_mod( 'members_per_page' );
	}

	/**
	 * Return members default order
	 */
	public function members_default_order() {
		return $this->get_theme_mod( 'members_default_order' );
	}

	/**
	 * Add custom classes to body to modify friend icons.
	 *
	 * @since 1.0.5
	 * @param array $classes The array of body classes.
	 *
	 * @return array The updated array of body classes.
	 */
	public function add_body_classes( $classes ) {
		$classes = parent::add_body_classes( $classes );

		$members_actions_text_displayed = $this->get_theme_mod( 'members_actions_text_displayed' );
		$members_counts_displayed       = $this->get_theme_mod( 'members_counts_displayed' );
		$bps_form_labels_displayed      = $this->get_theme_mod( 'bps_form_labels_displayed' );

		if ( ! empty( $members_actions_text_displayed ) ) {
			$classes[] = 'grimlock-buddypress--members-actions-text-displayed';
		}

		if ( ! empty( $members_counts_displayed ) ) {
			$classes[] = 'grimlock-buddypress--members-counts-displayed';
		}

		if ( ! empty( $bps_form_labels_displayed ) ) {
			$classes[] = 'grimlock-buddypress--bps-form-labels-displayed';
		}

		if ( ! in_array( 'friend_icons', Kirki::$controls_to_remove, true ) ) {
			$classes[] = "grimlock-buddypress--friend-icons-{$this->get_theme_mod( 'friend_icons' )}";
		}

		if ( ! in_array( 'follow_icons', Kirki::$controls_to_remove, true ) ) {
			$classes[] = "grimlock-buddypress--follow-icons-{$this->get_theme_mod( 'follow_icons' )}";
		}

		$classes[] = "grimlock-buddypress--members-items-layout-{$this->get_theme_mod( 'members_items_layout' )}";

		return $classes;
	}

	/**
	 * Display xprofile fields in members using theme mods
	 *
	 * @param int $user_id The id of the user.
	 */
	public function add_member_custom_fields( $user_id ) {
		if ( function_exists( 'buddypress' ) && bp_is_active( 'xprofile' ) ) {
			$field_ids = $this->get_theme_mod( 'members_displayed_profile_fields' );

			if ( empty( $user_id ) ) {
				$user_id = bp_get_member_user_id();
			}

			if ( empty( $user_id ) ) {
				$user_id = bp_displayed_user_id();
			}

			$allowed_html = array(
				'a' => array(
					'href' => array(),
					'rel'  => array(),
				),
			);

			foreach ( $field_ids as $field_id ) {
				$field = xprofile_get_field( $field_id, $user_id, false );

				if ( ! empty( $field ) ) {

					$visibility = xprofile_get_field_visibility_level( $field->id, $user_id );

					$is_visible =
						'public' === $visibility ||
						get_current_user_id() === intval( $user_id ) ||
						current_user_can( 'administrator' ) ||
						( 'loggedin' === $visibility && is_user_logged_in() ) ||
						( bp_is_active( 'friends' ) && 'friends' === $visibility && friends_check_friendship( $user_id, get_current_user_id() ) );

					if ( ! $is_visible ) {
						continue;
					}

					$value = xprofile_get_field_data( $field->id, $user_id, 'comma' );

					switch ( $field->type ) {
						case 'datebox':
							$date_field_settings = BP_XProfile_Field_Type_Datebox::get_field_settings( $field->id );

							if ( 'elapsed' !== $date_field_settings['date_format'] ) :
								$date_object = DateTime::createFromFormat( $date_field_settings['date_format'], $value );
								if ( ! empty( $date_object ) ) :
									$value = $date_object->diff( new DateTime( 'now' ) )->y;
								endif;
							endif;
							break;
					}

					if ( ! empty( $value ) ) {
						echo '<div class="bp-member-xprofile-custom-field bp-member-' . esc_attr( $field->name ) . '">' . wp_kses( $value, $allowed_html ) . '</div>';
					}
				}
			}

			if ( ! empty( $this->get_theme_mod( 'members_member_types_displayed' ) ) ) {

				$member_types = bp_get_member_type( $user_id, false );
				if ( ! empty( $member_types ) ) {
					echo '<div class="grimlock-buddypress-member-types mt-1">';

					foreach ( $member_types as $member_type ) {
						$member_type_label = bp_get_member_type_object( $member_type )->labels['singular_name'];

						if ( $member_type_url = bp_get_member_type_directory_permalink( $member_type ) ) {
							echo '<a href="' . esc_url( $member_type_url ) . '" class="badge badge-primary px-2 py-1 mr-1 mt-1">' . $member_type_label . '</a>';
						} else {
							echo '<span class="badge badge-primary px-2 py-1 mx-1 mt-1">' . $member_type_label . '</span>';
						}
					}

					echo '</div>';
				}

			}
		}
	}

	/**
	 * Check whether BP member action text need to be displayed.
	 *
	 * @since 1.0.5
	 *
	 * @param  bool $default The value for the text display.
	 *
	 * @return bool          True if the text needs to be displayed, false otherwise.
	 */
	public function is_members_actions_text_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'members_actions_text_displayed' );
	}

	/**
	 * Check whether members last activity needs to be displayed.
	 *
	 * @since 1.4.0
	 *
	 * @param  bool $default The value for the last activity display.
	 *
	 * @return bool          True if the last activity needs to be displayed, false otherwise.
	 */
	public function is_members_last_activity_displayed( $default ) {
		return (bool) $this->get_theme_mod( 'members_last_activity_displayed' );
	}

	/**
	 * Returns whether a range slider should be used for age range fields in BPS forms
	 *
	 * @since 1.4.2
	 *
	 * @param  bool $default The value for the last activity display.
	 *
	 * @return bool          True if the last activity needs to be displayed, false otherwise.
	 */
	public function bps_age_range_slider_enabled( $default ) {
		return (bool) $this->get_theme_mod( 'bps_age_range_slider_enabled' );
	}

	public function change_member_name( $name ) {
		if ( did_action( 'bp_before_directory_members_list' ) && ! did_action( 'bp_after_directory_members_list' ) ) {
			global $members_template;

			switch ( $this->get_theme_mod( 'members_displayed_name' ) ) {
				case 'username':
					$name = esc_html( $members_template->member->user_nicename );
					break;
				case 'fullname_username':
					$name = '<div class="user-nicename-small mb-1 small"><span class="user-nicename-at">@</span>' . esc_html( $members_template->member->user_nicename ) . '</div>' . $members_template->member->fullname;
					break;
			}
		}

		return $name;
	}

	/**
	 * Change content classes in member type directories for compatibility with page templates
	 *
	 * @param string[] $classes Array of content classes
	 *
	 * @return string[] New array of content classes
	 */
	public function member_type_directory_content_class( $classes ) {
		if ( ! function_exists( 'bp_is_members_directory' ) ||
		     ! bp_is_members_directory() ||
		     ! function_exists( 'bp_get_current_member_type' ) ||
		     empty( bp_get_current_member_type() ) ) {
			return $classes;
		}


		$directory_pages = bp_get_option( 'bp-pages' );

		if ( empty( $directory_pages['members'] ) ) {
			return $classes;
		}

		$page_template = get_page_template_slug( $directory_pages['members'] );

		switch ( $page_template ) {
			case 'template-classic-3-9-cols-left.php':
			case 'template-sidebar-left.php':
				$classes[] = 'region--container-classic';
				$classes[] = 'region--3-9-cols-left';
				$classes = array_diff( $classes, array( 'region--3-6-3-cols-left' ) );
				break;
			case 'template-classic-9-3-cols-left.php':
			case 'template-sidebar-right.php':
				$classes[] = 'region--container-classic';
				$classes[] = 'region--9-3-cols-left';
				$classes = array_diff( $classes, array( 'region--3-6-3-cols-left' ) );
				break;
			case 'template-classic-12-cols-left.php':
			case 'template-minimal.php':
			case 'template-full-width.php':
			case 'template-full-width-no-header.php':
				$classes[] = 'region--12-cols-left';
				$classes[] = 'region--container-classic';
				$classes = array_diff( $classes, array( 'region--3-6-3-cols-left' ) );
				break;
			case 'template-narrow-12-cols-left.php':
			case 'template-full-width-narrow.php':
				$classes[] = 'region--container-narrow';
				$classes = array_diff( $classes, array( 'region--3-6-3-cols-left', 'region--container-classic' ) );
				break;
			case 'template-narrower-12-cols-left.php':
			case 'template-full-width-narrower.php':
				$classes[] = 'region--container-narrower';
				$classes = array_diff( $classes, array( 'region--3-6-3-cols-left', 'region--container-classic' ) );
				break;
			case 'template-canvas.php':
				$classes[] = 'region--container-fluid';
				$classes = array_diff( $classes, array( 'region--3-6-3-cols-left', 'region--container-classic' ) );
				break;
		}

		return $classes;
	}

	/**
	 * Add scripts to improve user experience in the customizer
	 */
	public function add_scripts() {
		?>
		<script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                // Clear members filter cookie when changing the default order
                wp.customize( 'members_default_order', function( setting ) {
                    setting.bind( function( defaultOrder ) {
                        document.cookie = "bp-members-filter=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                    } );
                } );

                // Redirect to directory when opening the panel
                wp.customize.section( '<?php echo esc_js( $this->section ); ?>', function( section ) {
                    section.expanded.bind( function( isExpanded ) {
                        var previewUrl = '<?php echo esc_js( trailingslashit( bp_get_members_directory_permalink() ) ); ?>';
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

return new Grimlock_BuddyPress_Members_Customizer();
