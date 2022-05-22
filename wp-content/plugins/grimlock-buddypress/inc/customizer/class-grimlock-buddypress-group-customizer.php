<?php
/**
 * Grimlock_BuddyPress_Group_Customizer Class
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
class Grimlock_BuddyPress_Group_Customizer extends Grimlock_Base_Customizer {
	public $id;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'group';
		$this->section = 'grimlock_buddypress_group_section';
		$this->title   = esc_html__( 'Group Page', 'grimlock-buddypress' );

		add_action( 'after_setup_theme', array( $this, 'add_customizer_fields' ), 20 );
		add_filter( 'grimlock_customizer_controls_js_data', array( $this, 'add_customizer_controls_js_data' ), 10, 1 );

		add_filter( 'bp_before_groups_cover_image_settings_parse_args', array( $this, 'change_groups_cover_image_settings' ), 20, 1 );
		add_filter( 'grimlock_buddypress_group_layout',                 array( $this, 'get_group_layout'                   ), 10, 1 );
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_buddypress_group_customizer_defaults', array(
			'default_group_cover_image' => '',
			'group_layout'              => 'inside-9-3-cols-left',
		) );

		// TODO: remove deprecated defaults filter
		$this->defaults = apply_filters( 'grimlock_buddypress_customizer_defaults', $this->defaults );

		if ( bp_is_active( 'groups' ) ) {
			$this->add_section();
		}

		// General tab
		$this->add_default_group_cover_image_field( array( 'priority' => 100 ) );

		// Layout tab
		$this->add_layout_field( array( 'priority' => 200 ) );
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
				'class'    => 'group-general-tab',
				'controls' => array(
					'default_group_cover_image',
				),
			),
			array(
				'label'    => esc_html__( 'Layout', 'grimlock-buddypress' ),
				'class'    => 'group-layout-tab',
				'controls' => array(
					'group_layout',
				),
			),
		);
		return $js_data;
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
	 * Add a Kirki image field to set the default cover image for the BuddyPress groups
	 *
	 * @param array $args
	 * @since 1.0.6
	 */
	protected function add_default_group_cover_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Default Group Cover', 'grimlock-buddypress' ),
				'settings' => 'default_group_cover_image',
				'default'  => $this->get_default( 'default_group_cover_image' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_buddypress_customizer_default_group_cover_image_field_args', $args ) );
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
	 * Change the settings for the BuddyPress group cover image.
	 *
	 * @param  array $settings The array of default settings for the BuddyPress cover image.
	 *
	 * @return array           The array of settings for the BuddyPress cover image.
	 */
	public function change_groups_cover_image_settings( $settings = array() ) {
		$settings['default_cover'] = $this->get_theme_mod( 'default_group_cover_image' );
		$settings['width']         = get_custom_header()->width;
		$settings['height']        = get_custom_header()->height;
		return $settings;
	}

	/**
	 * Return the layout for the group profile.
	 *
	 * @param  string $layout The layout for the group profile.
	 *
	 * @return string         The updated layout for the group profile.
	 */
	public function get_group_layout( $layout ) {
		return $this->get_theme_mod( 'group_layout' );
	}
}

return new Grimlock_BuddyPress_Group_Customizer();
