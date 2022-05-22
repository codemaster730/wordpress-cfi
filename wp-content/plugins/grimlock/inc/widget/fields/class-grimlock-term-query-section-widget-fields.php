<?php

/**
 * Grimlock_Term_Query_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
class Grimlock_Term_Query_Section_Widget_Fields extends Grimlock_Section_Widget_Fields {

	/**
	 * Setup class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_term_query_section_widget' ) {
		parent::__construct( $id_base );

		add_filter( "{$this->id_base}_tabs",           array( $this, 'change_tabs'              ), 10, 1 );

		// General tab
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_field'      ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_size_field' ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_separator'            ), 110 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_field'           ), 130 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_wpautoped_field' ), 140 );

		// Query tab
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_taxonomy_field'       ), 100, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_handpick_terms_field' ), 110, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_terms_field'          ), 120, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_parent_field'         ), 130, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_separator'            ), 140, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_number_field'         ), 140, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_separator'            ), 150, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_orderby_field'        ), 150, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_order_field'          ), 160, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_separator'            ), 170, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_hide_empty_field'     ), 170, 2 );

		// Layout tab
		add_action( "{$this->id_base}_layout_tab",     array( $this, 'add_separator'            ), 90,  2 );
		add_action( "{$this->id_base}_layout_tab",     array( $this, 'add_terms_layout_field'   ), 90,  2 );

		// Style tab
		remove_action( "{$this->id_base}_style_tab",   array( $this, 'add_color_field'          ), 210 );
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
	 * Add a select to set the taxonomy for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_taxonomy_field( $instance, $widget ) {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$choices    = array();

		foreach ( $taxonomies as $taxonomy ) {
			$choices[$taxonomy->name] = $taxonomy->label;
		}

		$args = array(
			'id'      => $widget->get_field_id( 'taxonomy' ),
			'name'    => $widget->get_field_name( 'taxonomy' ),
			'label'   => esc_html__( 'Taxonomy', 'grimlock' ),
			'value'   => $instance['taxonomy'],
			'choices' => $choices,
		);

		$this->select( apply_filters( "{$this->id_base}_taxonomy_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox field to set whether to handpick a set of terms to display in the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_handpick_terms_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'handpick_terms' ),
			'name'  => $widget->get_field_name( 'handpick_terms' ),
			'label' => esc_html__( 'Handpick specific terms to display in the section', 'grimlock' ),
			'value' => $instance['handpick_terms'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_handpick_terms_field_args", $args, $instance ) );
	}

	/**
	 * Add a multi select to manually select a set of terms to display in the section
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_terms_field( $instance, $widget ) {
		$args = array(
			'id'        => $widget->get_field_id( 'terms' ),
			'name'      => $widget->get_field_name( 'terms' ),
			'label'     => esc_html__( 'Terms', 'grimlock' ),
			'value'     => $instance['terms'],
			'taxonomy' => '{taxonomy}', // Brackets mean that we are dynamically pulling the value from the "taxonomy" field
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => true,
				),
			),
		);

		$this->term_select( apply_filters( "{$this->id_base}_terms_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the parent term ID for the query.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 */
	public function add_parent_field( $instance, $widget ) {
		$terms = get_terms( array(
			'taxonomy'   => $instance['taxonomy'],
			'hide_empty' => false,
		) );

		$choices = array(
			'' => esc_html__( '- Select -', 'grimlock' ),
		);

		foreach ( $terms as $term ) {
			$choices[$term->term_id] = $term->name;
		}

		$args = array(
			'id'      => $widget->get_field_id( 'parent' ),
			'name'    => $widget->get_field_name( 'parent' ),
			'label'   => esc_html__( 'Parent term', 'grimlock' ),
			'value'   => $instance['parent'],
			'choices' => $choices,
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_parent_field_args", $args, $instance ) );
	}

	/**
	 * Add a number field to set the terms per page for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_number_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'number' ),
			'name'    => $widget->get_field_name( 'number' ),
			'label' => esc_html__( 'Number of terms to display', 'grimlock' ),
			'value' => $instance['number'],
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->numberfield( apply_filters( "{$this->id_base}_number_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the "order by" for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_orderby_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'orderby' ),
			'name'    => $widget->get_field_name( 'orderby' ),
			'label'   => esc_html__( 'Order by', 'grimlock' ),
			'value'   => $instance['orderby'],
			'choices' => array(
				'none'        => esc_html__( 'No order', 'grimlock' ),
				'term_id'     => esc_html__( 'ID', 'grimlock' ),
				'name'        => esc_html__( 'Name', 'grimlock' ),
				'slug'        => esc_html__( 'Slug', 'grimlock' ),
				'term_order'  => esc_html__( 'Order', 'grimlock' ),
				'description' => esc_html__( 'Description', 'grimlock' ),
				'count'       => esc_html__( 'Count', 'grimlock' ),
			),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_orderby_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the order for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_order_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'order' ),
			'name'    => $widget->get_field_name( 'order' ),
			'label'   => esc_html__( 'Order', 'grimlock' ),
			'value'   => $instance['order'],
			'choices' => array(
				'ASC'  => esc_html__( 'Ascending', 'grimlock' ),
				'DESC' => esc_html__( 'Descending', 'grimlock' ),
			),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_order_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox to set whether the empty terms must be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 */
	public function add_hide_empty_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'hide_empty' ),
			'name'    => $widget->get_field_name( 'hide_empty' ),
			'label' => esc_html__( 'Hide empty terms', 'grimlock' ),
			'value' => $instance['hide_empty'],
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->checkbox( apply_filters( "{$this->id_base}_hide_empty_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the items layout
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_terms_layout_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'terms_layout' ),
			'name'    => $widget->get_field_name( 'terms_layout' ),
			'label'   => esc_html__( 'Layout', 'grimlock' ),
			'value'   => $instance['terms_layout'],
			'choices' => array(
				'12-cols-classic'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-overlay.png',
				'6-6-cols-classic'     => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-overlay.png',
				'4-4-4-cols-classic'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-4-4-4-cols-overlay.png',
				'3-3-3-3-cols-classic' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-3-3-3-3-cols-overlay.png',
			),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->radio_image( apply_filters( "{$this->id_base}_terms_layout_field_args", $args, $instance ) );
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
			'label'   => esc_html__( 'Alignment', 'grimlock' ),
			'value'   => $instance['layout'],
			'choices' => array(
				'12-cols-center-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center-left.png',
				'12-cols-center'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center.png',
				'12-cols-left'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-left.png',
			),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
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
			'button_text'         => esc_html__( 'More items', 'grimlock' ),

			'terms_layout'        => '3-3-3-3-cols-classic',

			'taxonomy'            => 'category',
			'handpick_terms'      => 'false',
			'terms'               => array(),
			'parent'              => '',
			'number'              => '',
			'orderby'             => '',
			'order'               => 'ASC',
			'hide_empty'          => false,
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
			'terms_layout'        => $instance['terms_layout'],
			'term_thumbnail_size' => apply_filters( "{$this->id_base}_term_thumbnail_size", 'large', $instance['terms_layout'], $instance['taxonomy'] ),
			'query'               => $this->make_query( $instance ),
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

		$instance['terms_layout']   = isset( $new_instance['terms_layout'] ) ? sanitize_text_field( $new_instance['terms_layout'] ) : '';
		$instance['taxonomy']       = isset( $new_instance['taxonomy'] ) ? sanitize_text_field( $new_instance['taxonomy'] ) : '';
		$instance['handpick_terms'] = ! empty( $new_instance['handpick_terms'] );
		$instance['terms']          = isset( $new_instance['terms'] ) ? $new_instance['terms'] : array();
		$instance['parent']         = isset( $new_instance['parent'] ) ? intval( $new_instance['parent'] ) : 0;
		$instance['number']         = isset( $new_instance['number'] ) ? sanitize_text_field( $new_instance['number'] ) : '';
		$instance['orderby']        = isset( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : '';
		$instance['order']          = isset( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : '';
		$instance['hide_empty']     = ! empty( $new_instance['hide_empty'] );

		return $instance;
	}

	/**
	 * Build the WP_Term_Query instance that will be used in the widget
	 *
	 * @param array $instance Settings for the current widget instance.
	 *
	 * @return WP_Term_Query The term query for the widget
	 */
	protected function make_query( $instance ) {
		if ( ! empty( $instance['handpick_terms'] ) ) {
			$args = array(
				'taxonomy'   => $instance['taxonomy'],
				'include'    => $instance['terms'],
				'number'     => count( $instance['terms'] ),
				'orderby'    => 'include',
				'order'      => 'ASC',
				'count'      => true,
				'hide_empty' => false,
			);
		}
		else {
			$args = array(
				'taxonomy'   => $instance['taxonomy'],
				'number'     => $instance['number'],
				'orderby'    => $instance['orderby'],
				'order'      => $instance['order'],
				'count'      => true,
				'hide_empty' => ! empty( $instance['hide_empty'] ),
			);

			// Add parent argument for hierarchical taxonomy terms only.
			if ( ! empty( $instance['parent'] ) ) {
				$args['parent'] = $instance['parent'];
			}
		}

		return new WP_Term_Query( $args );
	}

	/**
	 * Get the widget classes
	 *
	 * TODO: Consider removing this method to keep `grimlock-section--btn-*` and other styling classes.
	 *
	 * @param array $instance Settings for the current widget instance.
	 *
	 * @return array The widget classes
	 */
	protected function get_classes( $instance ) {
		$classes   = array( $instance['classes'] );
		$classes[] = "grimlock-terms-query-section--{$instance['button_format']}";
		$title     = ! empty( $instance['title'] ) ? sanitize_title( $instance['title'] ) : '';

		if ( '' !== $title ) {
			$classes[] = "grimlock-terms-query-section--{$title}";
		}

		return $classes;
	}
}

return new Grimlock_Term_Query_Section_Widget_Fields();
