<?php
/**
 * Grimlock_Animate_BuddyPress_Groups_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends WP_Widget to create a section widget.
 *
 * @see WP_Widget
 */
class Grimlock_Animate_BuddyPress_Groups_Section_Widget_Fields extends Grimlock_Base_Widget_Fields {
	/**
	 * Sets up a new Section widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_buddypress_groups_section_widget' ) {
		parent::__construct( $id_base );

		add_filter( "{$this->id_base}_tabs", array( $this, 'change_tabs' ), 10, 1 );

		// Animation tab
		add_action( "{$this->id_base}_animation_tab", array( $this, 'add_groups_reveal_field' ), 100, 2 );
		add_action( "{$this->id_base}_animation_tab", array( $this, 'add_reveal_reset_field'   ), 110, 2 );
		add_action( "{$this->id_base}_animation_tab", array( $this, 'add_reveal_mobile_field'  ), 120, 2 );
	}

	/**
	 * Change the list of tabs in the widget
	 *
	 * @param array $tabs The array containing the current tabs
	 *
	 * @return array The new array of tabs
	 */
	public function change_tabs( $tabs ) {
		$tabs['animation'] = esc_html__( 'Animation', 'grimlock-animate' );
		return $tabs;
	}

	/**
	 * @param array $instance
	 * @param WP_Widget $widget
	 */
	public function add_groups_reveal_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'groups_reveal' ),
			'name'    => $widget->get_field_name( 'groups_reveal' ),
			'label'   => esc_html__( 'Groups Reveal:', 'grimlock-animate' ),
			'value'   => $instance['groups_reveal'],
			'choices' => array(
				'none'   => esc_html__( 'None', 'grimlock-animate' ),
				'bottom' => esc_html__( 'Bottom', 'grimlock-animate' ),
				'top'    => esc_html__( 'Top', 'grimlock-animate' ),
				'left'   => esc_html__( 'Left', 'grimlock-animate' ),
				'right'  => esc_html__( 'Right', 'grimlock-animate' ),
				'fade'   => esc_html__( 'Fade', 'grimlock-animate' ),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_groups_reveal_field_args", $args, $instance ) );
	}

	/**
	 * @param array $instance
	 * @param WP_Widget $widget
	 */
	public function add_reveal_reset_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'reveal_reset' ),
			'name'  => $widget->get_field_name( 'reveal_reset' ),
			'label' => esc_html__( 'Play reveal animations every time the element becomes visible', 'grimlock-animate' ),
			'value' => $instance['reveal_reset'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_reveal_reset_field_args", $args, $instance ) );
	}

	/**
	 * @param array $instance
	 * @param WP_Widget $widget
	 */
	public function add_reveal_mobile_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'reveal_mobile' ),
			'name'  => $widget->get_field_name( 'reveal_mobile' ),
			'label' => esc_html__( 'Play reveal animations on mobile', 'grimlock-animate' ),
			'value' => $instance['reveal_mobile'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_reveal_mobile_field_args", $args, $instance ) );
	}

	/**
	 * Change the default settings for the widget
	 *
	 * @param array $defaults The default settings for the widget.
	 *
	 * @return array The updated default settings for the widget.
	 */
	public function change_defaults( $defaults ) {
		return array_merge( $defaults, array(
			'groups_reveal' => 'none',
			'reveal_reset'  => false,
			'reveal_mobile' => false,
		) );
	}

	/**
	 * Change the arguments sent to the component in charge of rendering the widget
	 *
	 * @param array $component_args The arguments for the component in charge of rendering the widget
	 * @param array $instance Settings for the current widget instance.
	 * @param array $widget_args Display arguments including 'before_title', 'after_title',
	 *                           'before_widget', and 'after_widget'.
	 *
	 * @return array The updated arguments for the component in charge of rendering the widget
	 */
	public function change_component_args( $component_args, $instance, $widget_args, $widget_id ) {
		return array_merge( $component_args, array(
			'groups_reveal' => $instance['groups_reveal'],
			'reveal_reset'  => $instance['reveal_reset'],
			'reveal_mobile' => $instance['reveal_mobile'],
		) );
	}

	/**
	 * Handles sanitizing settings for the current widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function sanitize_instance( $new_instance, $old_instance ) {
		$instance = $new_instance;

		$instance['groups_reveal'] = isset( $new_instance['groups_reveal'] ) ? sanitize_text_field( $new_instance['groups_reveal'] ) : '';
		$instance['reveal_reset']  = ! empty( $new_instance['reveal_reset'] );
		$instance['reveal_mobile'] = ! empty( $new_instance['reveal_mobile'] );

		return $instance;
	}
}

return new Grimlock_Animate_BuddyPress_Groups_Section_Widget_Fields();
