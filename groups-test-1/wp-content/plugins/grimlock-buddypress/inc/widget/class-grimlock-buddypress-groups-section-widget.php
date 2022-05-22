<?php
/**
 * Grimlock_BuddyPress_Groups_Section_Widget Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends Grimlock_Base_Widget to create a section widget.
 *
 * @see WP_Widget
 */
class Grimlock_BuddyPress_Groups_Section_Widget extends Grimlock_Base_Widget {
	/**
	 * Sets up a new Section widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct( 'grimlock_buddypress_groups_section_widget', esc_html__( 'Grimlock BuddyPress Groups Section', 'grimlock-buddypress' ), array(
			'classname'   => 'widget_grimlock_buddypress_groups_section',
			'description' => esc_html__( 'A flexible component that showcase BuddyPress groups.', 'grimlock-buddypress' ),
		) );

		if ( is_customize_preview() || is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'bp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		if ( function_exists( 'buddypress' ) ) {
			$min = bp_core_get_minified_asset_suffix();
			wp_enqueue_script( 'groups_widget_groups_list-js', buddypress()->plugin_url . "bp-groups/js/widget-groups{$min}.js", array( 'jquery' ), bp_get_version() );
		}
	}

	/**
	 * Outputs the content for the current Text widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current Text widget instance.
	 */
	public function widget( $args, $instance ) {
		$instance  = wp_parse_args( $instance, $this->defaults );
		echo $args['before_widget'];
		do_action( 'grimlock_buddypress_groups_section', apply_filters( "{$this->id_base}_component_args", array(), $instance, $args, $this->id ) );
		echo $args['after_widget'];
	}
}
