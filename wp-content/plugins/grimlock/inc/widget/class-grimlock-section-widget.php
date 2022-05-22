<?php
/**
 * Grimlock_Section_Widget Class
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
class Grimlock_Section_Widget extends Grimlock_Base_Widget {
	/**
	 * Sets up a new Section widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct( 'grimlock_section_widget', esc_html__( 'Grimlock Section', 'grimlock' ), array(
			'classname'   => 'widget_grimlock_section',
			'description' => esc_html__( 'A flexible component that showcase key marketing messages on front page.', 'grimlock' ),
		) );
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
		do_action( 'grimlock_section', apply_filters( "{$this->id_base}_component_args", array(), $instance, $args, $this->id ) );
		echo $args['after_widget'];
	}
}
