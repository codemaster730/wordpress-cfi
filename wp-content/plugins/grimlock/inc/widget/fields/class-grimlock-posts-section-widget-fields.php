<?php

/**
 * Grimlock_Posts_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
class Grimlock_Posts_Section_Widget_Fields extends Grimlock_Query_Section_Widget_Fields {

	/**
	 * Setup class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_posts_section_widget' ) {
		parent::__construct( $id_base );

		// Query tab
		remove_action( "{$this->id_base}_query_tab", array( $this, 'add_post_type_field'           ), 100 );

		add_action( "{$this->id_base}_query_tab",    array( $this, 'add_post_format_field'         ), 115, 2 );
		add_action( "{$this->id_base}_query_tab",    array( $this, 'add_ignore_sticky_posts_field' ), 165, 2 );
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

		$page_for_posts_url = get_permalink( get_option( 'page_for_posts' ) );

		return array_merge( $defaults, array(
			'button_text'         => esc_html__( 'More posts', 'grimlock' ),
			'button_link'         => $page_for_posts_url ? $page_for_posts_url : home_url(),

			'post_format'         => 'post-format-standard',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => 1,
		) );
	}

	/**
	 * Add a select to set the taxonomies for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_taxonomies_field( $instance, $widget ) {
		$choices = array();

		foreach ( $this->get_taxonomies() as $name => $label ) {
			$terms      = get_terms( array( 'taxonomy' => $name ) );
			$subchoices = array();

			foreach ( $terms as $term ) {
				$subchoices[$name . '|' . $term->slug] = $term->name;
			}

			$choices[$name] = array(
				'label'      => $label,
				'subchoices' => $subchoices,
			);
		}

		$args = array(
			'id'       => $widget->get_field_id( 'taxonomies' ),
			'name'     => $widget->get_field_name( 'taxonomies' ),
			'label'    => esc_html__( 'Taxonomies', 'grimlock' ),
			'value'    => $instance['taxonomies'],
			'choices'  => $choices,
			'multiple' => true,
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_taxonomies_field_args", $args, $instance ) );
	}

	/**
	 * @return array
	 */
	protected function get_taxonomies() {
		return array(
			'category' => esc_html__( 'Categories', 'grimlock' ),
			'post_tag' => esc_html__( 'Tags', 'grimlock' ),
		);
	}

	/**
	 * Add a select to set the post formats for the query
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 */
	public function add_post_format_field( $instance, $widget ) {
		$terms = get_terms( array(
			'taxonomy' => 'post_format',
		) );

		if ( ! is_wp_error( $terms ) ) {
			$choices = array(
				'post-format-all'      => esc_html__( 'All', 'grimlock' ),
				'post-format-standard' => esc_html__( 'Standard', 'grimlock' ),
			);

			foreach ( $terms as $term ) {
				$choices[$term->slug] = $term->name;
			}

			$args = array(
				'id'       => $widget->get_field_id( 'post_format' ),
				'name'     => $widget->get_field_name( 'post_format' ),
				'label'    => esc_html__( 'Format', 'grimlock' ),
				'value'    => $instance['post_format'],
				'choices'  => $choices,
				'conditional_logic' => array(
					array(
						'field'    => 'handpick_posts',
						'operator' => '==',
						'value'    => false,
					),
				),
			);

			$this->select( apply_filters( "{$this->id_base}_post_format_field_args", $args, $instance ) );
		}
	}

	/**
	 * Add a checkbox to set whether the sticky posts must be ignored or not.
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_ignore_sticky_posts_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'ignore_sticky_posts' ),
			'name'     => $widget->get_field_name( 'ignore_sticky_posts' ),
			'label' => esc_html__( 'Ignore sticky posts', 'grimlock' ),
			'value' => $instance['ignore_sticky_posts'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_ignore_sticky_posts_field_args", $args, $instance ) );
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

		$instance['post_format']         = isset( $new_instance['post_format'] ) ? sanitize_text_field( $new_instance['post_format'] ) : '';
		$instance['ignore_sticky_posts'] = ! empty( $new_instance['ignore_sticky_posts'] );

		return $instance;
	}

	/**
	 * Build the WP_Query instance that will be used in the widget
	 *
	 * @param array $instance Settings for the current widget instance.
	 *
	 * @return WP_Query The query for the widget
	 */
	protected function make_query( $instance ) {
		if ( ! empty( $instance['handpick_posts'] ) ) {
			$query_args = array(
				'post_type'           => 'post',
				'ignore_sticky_posts' => $instance['ignore_sticky_posts'],
				'post__in'            => $instance['posts'],
				'posts_per_page'      => count( $instance['posts'] ),
				'orderby'             => 'post__in',
				'order'               => 'ASC',
			);
		}
		else {
			$query_args = array(
				'post_type'           => 'post',
				'posts_per_page'      => $instance['posts_per_page'],
				'ignore_sticky_posts' => $instance['ignore_sticky_posts'],
				'orderby'             => $instance['orderby'],
				'order'               => $instance['order'],
			);

			if ( ! empty( $instance['taxonomies'] ) ) {
				$taxonomies_terms = array();
				foreach ( $instance['taxonomies'] as $term ) {
					$taxonomy_term = explode( '|', $term, 2 );
					if ( ! isset( $taxonomies_terms[ $taxonomy_term[0] ] ) ) {
						$taxonomies_terms[ $taxonomy_term[0] ] = array();
					}
					$taxonomies_terms[ $taxonomy_term[0] ][] = $taxonomy_term[1];
				}

				$tax_query = array();
				foreach ( $taxonomies_terms as $taxonomy => $terms ) {
					$tax_query[] = array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $terms
					);
				}

				$query_args['tax_query'] = $tax_query;
			}

			if ( isset( $instance['post_format'] ) && 'post-format-all' !== $instance['post_format'] ) {
				if ( 'post-format-standard' === $instance['post_format'] ) {
					$tax_query = array(
						array(
							'taxonomy' => 'post_format',
							'field'    => 'slug',
							'operator' => 'NOT IN',
							'terms'    => get_terms( array(
								'taxonomy' => 'post_format',
								'fields'   => 'id=>slug',
							) ),
						),
					);
				} else {
					$tax_query = array(
						array(
							'taxonomy' => 'post_format',
							'field'    => 'slug',
							'terms'    => $instance['post_format'],
						),
					);
				}

				$query_args['tax_query'] = isset( $query_args['tax_query'] ) ? array_merge( $query_args['tax_query'], $tax_query ) : $tax_query;
			}

			$query_args['meta_key']        = $instance['meta_key'];
			$meta_value_arg                = empty( $instance['meta_value_num'] ) ? 'meta_value' : 'meta_value_num';
			$query_args[ $meta_value_arg ] = $instance['meta_value'];
			$query_args['meta_compare']    = $instance['meta_compare'];
		}

		$query_args = apply_filters( "{$this->id_base}_query_args", $query_args, $instance );

		return new WP_Query( $query_args );
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
		$classes[] = "grimlock-query-section--posts";
		$classes[] = "grimlock-section--{$instance['button_format']}";
		$title     = ! empty( $instance['title'] ) ? sanitize_title( $instance['title'] ) : '';

		if ( '' !== $title ) {
			$classes[] = "grimlock-query-section--{$title}";
		}

		return $classes;
	}
}

return new Grimlock_Posts_Section_Widget_Fields();