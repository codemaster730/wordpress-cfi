<?php

/**
 * Grimlock_Nav_Menu_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
class Grimlock_Nav_Menu_Section_Widget_Fields extends Grimlock_Section_Widget_Fields {

	/**
	 * Setup class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_nav_menu_section_widget' ) {
		parent::__construct( $id_base );

		// General tab
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_field'           ), 130 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_wpautoped_field' ), 140 );

		add_action( "{$this->id_base}_general_tab",    array( $this, 'add_menu_field'           ), 140, 2 );
	}

	/**
	 * Add a select to set the nav menu for the Section Component.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_menu_field( $instance, $widget ) {
		$nav_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		$choices   = array(
			'' => esc_html__( '- Select -', 'grimlock' ),
		);

		foreach( $nav_menus as $term ) {
			$choices[$term->slug] = $term->name;
		}

		$args  = array(
			'id'      => $widget->get_field_id( 'menu' ),
			'name'    => $widget->get_field_name( 'menu' ),
			'label'   => esc_html__( 'Select Menu', 'grimlock' ),
			'value'   => $instance['menu'],
			'choices' => $choices,
		);

		$this->select( apply_filters( "{$this->id_base}_menu_field_args", $args, $instance ) );
	}

	/**
	 * Add a radio image field to set the layout of the section
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_layout_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'layout' ),
			'name'    => $widget->get_field_name( 'layout' ),
			'label'   => esc_html__( 'Layout', 'grimlock' ),
			'value'   => $instance['layout'],
			'choices' => array(
				'12-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-12-cols-left.png',
				'12-cols-center'              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-12-cols-center.png',
				'12-cols-right'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-12-cols-right.png',
				'6-6-cols-left'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-6-6-cols-left.png',
				'6-6-cols-left-reverse'       => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-6-6-cols-left-reverse.png',
				'8-4-cols-grid'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-8-4-cols-grid.png',
				'4-4-4-cols-grid'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-4-4-4-cols-grid.png',
				'12-cols-left-blank'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-12-cols-left-blank.png',
				'12-cols-center-blank'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-12-cols-center-blank.png',
				'12-cols-right-blank'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-12-cols-right-blank.png',
				'6-6-cols-left-blank'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-6-6-cols-left-blank.png',
				'6-6-cols-left-reverse-blank' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-menu-6-6-cols-left-reverse-blank.png',
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
			'menu' => '',
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
			'menu' => $instance['menu'],
		) );
	}

	/**
	 * Handles sanitizing settings for the current widget instance.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function sanitize_instance( $new_instance, $old_instance ) {
		$instance = parent::sanitize_instance( $new_instance, $old_instance );

		$instance['menu'] = isset( $new_instance['menu'] ) ? sanitize_text_field( $new_instance['menu'] ) : '';

		return $instance;
	}
}

return new Grimlock_Nav_Menu_Section_Widget_Fields();
