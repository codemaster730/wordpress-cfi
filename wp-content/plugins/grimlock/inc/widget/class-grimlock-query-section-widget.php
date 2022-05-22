<?php
/**
 * Grimlock_Query_Section_Widget Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends Grimlock_Base_Widget to create a card query section widget.
 *
 * @see Grimlock_Query_Section_Widget
 */
class Grimlock_Query_Section_Widget extends Grimlock_Base_Widget {
	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		parent::__construct( 'grimlock_query_section_widget', esc_html__( 'Grimlock Query Section', 'grimlock' ), array(
			'classname'   => 'widget_grimlock_query_section',
			'description' => esc_html__( 'A flexible component that displays a query for post type objects.', 'grimlock' ),
		) );
	}

	/**
	 * Outputs the content for the current widget instance.
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
		do_action( 'grimlock_query_section', apply_filters( "{$this->id_base}_component_args", array(), $instance, $args, $this->id ) );
		echo $args['after_widget'];
	}
}
