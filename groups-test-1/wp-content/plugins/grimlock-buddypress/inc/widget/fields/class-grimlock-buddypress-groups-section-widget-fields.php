<?php

/**
 * Grimlock_BuddyPress_Groups_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
class Grimlock_BuddyPress_Groups_Section_Widget_Fields extends Grimlock_Section_Widget_Fields {

	/**
	 * Setup class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_buddypress_groups_section_widget' ) {
		parent::__construct( $id_base );

		add_filter( "{$this->id_base}_tabs",           array( $this, 'change_tabs'              ), 10, 1 );

		// General tab
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_field'      ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_size_field' ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_field'           ), 130 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_wpautoped_field' ), 140 );

		// Query tab
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_max_groups_field'     ), 100, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_group_default_field'  ), 110, 2 );

		// Layout tab
		add_action( "{$this->id_base}_layout_tab",     array( $this, 'add_groups_layout_field' ), 90, 2 );
	}

	/**
	 * Change the list of tabs in the widget
	 *
	 * @param array $tabs The array containing the current tabs
	 *
	 * return array The new array of tabs
	 */
	public function change_tabs( $tabs ) {
		return array_merge( $tabs, array(
			'general' => esc_html__( 'General', 'grimlock' ),
			'query'   => esc_html__( 'Query',   'grimlock' ),
			'layout'  => esc_html__( 'Layout',  'grimlock' ),
			'style'   => esc_html__( 'Style',   'grimlock' ),
		) );
	}

	/**
	 * Add a text field to to set the maximum number of groups.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_max_groups_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'max_groups' ),
			'name'  => $widget->get_field_name( 'max_groups' ),
			'label' => esc_html__( 'Max groups to show:', 'grimlock' ),
			'value' => $instance['max_groups'],
		);

		$this->textfield( apply_filters( "{$this->id_base}_max_groups_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the default group.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_group_default_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'group_default' ),
			'name'    => $widget->get_field_name( 'group_default' ),
			'label'   => esc_html__( 'Default groups to show:', 'grimlock' ),
			'value'   => $instance['group_default'],
			'choices' => array(
				'newest'       => esc_html__( 'Newest', 'grimlock-buddypress' ),
				'active'       => esc_html__( 'Active', 'grimlock-buddypress' ),
				'popular'      => esc_html__( 'Popular', 'grimlock-buddypress' ),
				'alphabetical' => esc_html__( 'Alphabetical', 'grimlock-buddypress' ),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_group_default_field_args", $args, $instance ) );
	}

	/**
	 * Add a radio image field to set the layout of groups for the section
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_groups_layout_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'groups_layout' ),
			'name'    => $widget->get_field_name( 'groups_layout' ),
			'label'   => esc_html__( 'Layout:', 'grimlock-buddypress' ),
			'value'   => $instance['groups_layout'],
			'choices' => array(
				'4-4-4-cols-classic'    => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/template-groups-4-4-4-cols-classic.png',
				'3-3-3-3-cols-classic'  => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/template-groups-3-3-3-3-cols-classic.png',
				'2-2-2-2-2-2-cols-grid' => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/template-groups-2-2-2-2-2-2-cols-grid.png',
			),
		);

		$this->radio_image( apply_filters( "{$this->id_base}_groups_layout_field_args", $args, $instance ) );
	}

	/**
	 * Add a radio image field to set the alignment of the section
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_layout_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'layout' ),
			'name'    => $widget->get_field_name( 'layout' ),
			'label'   => esc_html__( 'Alignment:', 'grimlock-buddypress' ),
			'value'   => $instance['layout'],
			'choices' => array(
				'12-cols-left'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-left.png',
				'12-cols-center'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center.png',
				'12-cols-center-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center-left.png',
			),
		);

		$this->radio_image( apply_filters( "{$this->id_base}_layout_field_args", $args, $instance ) );
	}

	/**
	 * Change the default settings for the widget
	 *
	 * @param array $defaults The default settings for the widget.
	 *
	 * @return array The updated default settings for the widget.
	 */
	public function change_defaults( $defaults ) {
		$defaults = parent::change_defaults( $defaults );

		return array_merge( $defaults, array(
			'title'               => esc_html__( 'Groups', 'grimlock-buddypress' ),

			'button_text'         => esc_html__( 'More groups', 'grimlock-buddypress' ),
			'button_link'         => function_exists( 'bp_get_groups_directory_permalink' ) ? bp_get_groups_directory_permalink() : '#',
			'button_target_blank' => true,

			'groups_layout'       => '3-3-3-3-cols-classic',
			'layout'              => '12-cols-center-left',

			'max_groups'          => 5,
			'group_default'       => 'newest',
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
		$component_args = parent::change_component_args( $component_args, $instance, $widget_args, $widget_id );

		return array_merge( $component_args, array(
			'groups_layout'       => $instance['groups_layout'],

			'max_groups'          => $instance['max_groups'],
			'group_default'       => $instance['group_default'],
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
		$instance = parent::sanitize_instance( $new_instance, $old_instance );

		$instance['groups_layout']       = isset( $new_instance['groups_layout'] ) ? sanitize_text_field( $new_instance['groups_layout'] ) : '';

		$instance['max_groups']          = isset( $new_instance['max_groups'] ) ? strip_tags( $new_instance['max_groups'] ) : '';
		$instance['group_default']       = isset( $new_instance['group_default'] ) ? strip_tags( $new_instance['group_default'] ) : '';

		return $instance;
	}

	/**
	 * Get the widget classes
	 *
	 * @param array $instance Settings for the current widget instance.
	 *
	 * @return array The widget classes
	 */
	protected function get_classes( $instance ) {
		$classes   = array( $instance['classes'] );
		$classes[] = "grimlock-section--{$instance['button_format']}";

		$title = ! empty( $instance['title'] ) ? sanitize_title( $instance['title'] ) : '';
		if ( '' !== $title ) {
			$classes[] = "grimlock-buddypress-groups-section--{$title}";
		}

		return $classes;
	}
}

return new Grimlock_BuddyPress_Groups_Section_Widget_Fields();