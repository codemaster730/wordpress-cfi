<?php

/**
 * Grimlock_Query_Section_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
class Grimlock_Query_Section_Widget_Fields extends Grimlock_Section_Widget_Fields {

	/**
	 * Setup class
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct( $id_base = 'grimlock_query_section_widget' ) {
		parent::__construct( $id_base );

		add_filter( "{$this->id_base}_tabs",           array( $this, 'change_tabs'              ), 10, 1 );

		// General tab
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_field'      ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_thumbnail_size_field' ), 100 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_separator'            ), 110 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_field'           ), 130 );
		remove_action( "{$this->id_base}_general_tab", array( $this, 'add_text_wpautoped_field' ), 140 );

		// Query tab
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_post_type_field'      ), 100, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_handpick_posts_field' ), 100, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_posts_field'          ), 100, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_separator'            ), 110, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_taxonomies_field'     ), 110, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_separator'            ), 120, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_posts_per_page_field' ), 120, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_separator'            ), 130, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_orderby_field'        ), 130, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_order_field'          ), 140, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_separator'            ), 150, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_use_meta_query_field' ), 150, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_meta_key_field'       ), 160, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_meta_compare_field'   ), 170, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_meta_value_field'     ), 180, 2 );
		add_action( "{$this->id_base}_query_tab",      array( $this, 'add_meta_value_num_field' ), 190, 2 );

		// Layout tab
		add_action( "{$this->id_base}_layout_tab",     array( $this, 'add_separator'            ), 90,  2 );
		add_action( "{$this->id_base}_layout_tab",     array( $this, 'add_posts_layout_field'   ), 90,  2 );

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
	 * Add a select to set the post type for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_post_type_field( $instance, $widget ) {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$post_types_choices = array();
		foreach ( $post_types as $post_type ) {
			$post_types_choices[ $post_type->name ] = $post_type->label;
		}
		// Remove the media post type
		unset( $post_types_choices['attachment'] );

		$args = array(
			'id'      => $widget->get_field_id( 'post_type' ),
			'name'    => $widget->get_field_name( 'post_type' ),
			'label'   => esc_html__( 'Post type', 'grimlock' ),
			'value'   => $instance['post_type'],
			'choices' => $post_types_choices,
		);

		$this->select( apply_filters( "{$this->id_base}_post_type_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox field to set whether to handpick a set of posts to display in the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_handpick_posts_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'handpick_posts' ),
			'name'  => $widget->get_field_name( 'handpick_posts' ),
			'label' => esc_html__( 'Handpick specific posts to display in the section', 'grimlock' ),
			'value' => $instance['handpick_posts'],
		);

		$this->checkbox( apply_filters( "{$this->id_base}_handpick_posts_field_args", $args, $instance ) );
	}

	/**
	 * Add a multi select to manually select a set of posts to display in the section
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_posts_field( $instance, $widget ) {
		$args = array(
			'id'        => $widget->get_field_id( 'posts' ),
			'name'      => $widget->get_field_name( 'posts' ),
			'label'     => esc_html__( 'Posts', 'grimlock' ),
			'value'     => $instance['posts'],
			'post_type' => '{post_type}', // Brackets mean that we are dynamically pulling the value from the "post_type" field
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => true,
				),
			),
		);

		$this->post_select( apply_filters( "{$this->id_base}_posts_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the taxonomies for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_taxonomies_field( $instance, $widget ) {
		$taxonomies         = get_taxonomies( array(), 'objects' );
		$taxonomies_choices = array();

		foreach ( $taxonomies as $taxonomy ) {
			$terms         = get_terms( array( 'taxonomy' => $taxonomy->name ) );
			$terms_choices = array();

			foreach ( $terms as $term ) {
				$terms_choices[$taxonomy->name . '|' . $term->slug] = $term->name;
			}

			$taxonomies_choices[$taxonomy->name] = array(
				'label' => $taxonomy->label,
				'subchoices' => $terms_choices,
			);
		}

		$args = array(
			'id'       => $widget->get_field_id( 'taxonomies' ),
			'name'     => $widget->get_field_name( 'taxonomies' ),
			'label'    => esc_html__( 'Taxonomies', 'grimlock' ),
			'value'    => $instance['taxonomies'],
			'choices'  => $taxonomies_choices,
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
	 * Add a checkbox field to set whether to use a meta query to filter the query results
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_use_meta_query_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'use_meta_query' ),
			'name'  => $widget->get_field_name( 'use_meta_query' ),
			'label' => esc_html__( 'Filter using metadata key and value', 'grimlock' ),
			'value' => isset( $instance['use_meta_query'] ) ? $instance['use_meta_query'] : false,
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->checkbox( apply_filters( "{$this->id_base}_use_meta_query_field_args", $args, $instance ) );
	}

	/**
	 * Add a text field to set the meta key for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_meta_key_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'meta_key' ),
			'name'  => $widget->get_field_name( 'meta_key' ),
			'label' => esc_html__( 'Meta key', 'grimlock' ),
			'value' => $instance['meta_key'],
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
				array(
					'field'    => 'use_meta_query',
					'operator' => '==',
					'value'    => true,
				),
			),
		);

		$this->textfield( apply_filters( "{$this->id_base}_meta_key_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the meta compare for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_meta_compare_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'meta_compare' ),
			'name'    => $widget->get_field_name( 'meta_compare' ),
			'label'   => esc_html__( 'Meta compare', 'grimlock' ),
			'value'   => $instance['meta_compare'],
			'choices' => array(
				'='          => '=',
				'!='         => '!=',
				'>'          => '>',
				'>='         => '>=',
				'<'          => '<',
				'<='         => '<=',
				'LIKE'       => 'LIKE',
				'NOT LIKE'   => 'NOT LIKE',
				'NOT EXISTS' => 'NOT EXISTS'
			),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
				array(
					'field'    => 'use_meta_query',
					'operator' => '==',
					'value'    => true,
				),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_meta_compare_field_args", $args, $instance ) );
	}

	/**
	 * Add a text field to set the meta value for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_meta_value_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'meta_value' ),
			'name'  => $widget->get_field_name( 'meta_value' ),
			'label' => esc_html__( 'Meta value', 'grimlock' ),
			'value' => $instance['meta_value'],
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
				array(
					'field'    => 'use_meta_query',
					'operator' => '==',
					'value'    => true,
				),
			),
		);

		$this->textfield( apply_filters( "{$this->id_base}_meta_value_field_args", $args, $instance ) );
	}

	/**
	 * Add a checkbox field to set whether the meta value is to be considered as a number in the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_meta_value_num_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'meta_value_num' ),
			'name'  => $widget->get_field_name( 'meta_value_num' ),
			'label' => esc_html__( 'Treat meta value as a number', 'grimlock' ),
			'value' => $instance['meta_value_num'],
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
				array(
					'field'    => 'use_meta_query',
					'operator' => '==',
					'value'    => true,
				),
			),
		);

		$this->checkbox( apply_filters( "{$this->id_base}_meta_value_num_field_args", $args, $instance ) );
	}

	/**
	 * Add a number field to set the posts per page for the query
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_posts_per_page_field( $instance, $widget ) {
		$args = array(
			'id'    => $widget->get_field_id( 'posts_per_page' ),
			'name'  => $widget->get_field_name( 'posts_per_page' ),
			'label' => esc_html__( 'Number of posts to display', 'grimlock' ),
			'value' => $instance['posts_per_page'],
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->numberfield( apply_filters( "{$this->id_base}_posts_per_page_field_args", $args, $instance ) );
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
				'none'          => esc_html__( 'No order', 'grimlock' ),
				'ID'            => esc_html__( 'ID', 'grimlock' ),
				'author'        => esc_html__( 'Author', 'grimlock' ),
				'title'         => esc_html__( 'Title', 'grimlock' ),
				'name'          => esc_html__( 'Slug', 'grimlock' ),
				'type'          => esc_html__( 'Post type', 'grimlock' ),
				'date'          => esc_html__( 'Creation date', 'grimlock' ),
				'modified'      => esc_html__( 'Last modified date', 'grimlock' ),
				'parent'        => esc_html__( 'Post/Page parent ID', 'grimlock' ),
				'rand'          => esc_html__( 'Random order', 'grimlock' ),
				'comment_count' => esc_html__( 'Number of comments', 'grimlock' ),
				'relevance'     => esc_html__( 'Relevance (the ones matching the search best first)', 'grimlock' ),
				'menu_order'    => esc_html__( 'Menu order', 'grimlock' ),
			),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
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
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
			),
		);

		$this->select( apply_filters( "{$this->id_base}_order_field_args", $args, $instance ) );
	}

	/**
	 * Add a select to set the items layout
	 *
	 * @param array $instance
	 * @param WP_Widget $widget
	 * @since 1.0.0
	 */
	public function add_posts_layout_field( $instance, $widget ) {
		$args = array(
			'id'      => $widget->get_field_id( 'posts_layout' ),
			'name'    => $widget->get_field_name( 'posts_layout' ),
			'label'   => esc_html__( 'Layout', 'grimlock' ),
			'value'   => $instance['posts_layout'],
			'choices' => array(
				'4-4-4-cols-classic'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-4-4-4-cols-classic.png',
				'3-3-3-3-cols-classic'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-3-3-3-3-cols-classic.png',
				'6-6-cols-classic'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-classic.png',
				'12-cols-classic'                  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-classic.png',
				'4-4-4-cols-overlay'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-4-4-4-cols-overlay.png',
				'3-3-3-3-cols-overlay'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-3-3-3-3-cols-overlay.png',
				'6-6-cols-overlay'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-overlay.png',
				'12-cols-overlay'                  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-overlay.png',
				'12-cols-lateral-modern-alternate' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-lateral-modern-alternate.png',
				'6-6-cols-lateral'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-lateral.png',
				'6-6-cols-lateral-reverse'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-lateral-reverse.png',
				'12-cols-lateral'                  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-lateral.png',
				'12-cols-lateral-reverse'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-lateral-reverse.png',
				'4-4-4-cols-overlay-grid'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-4-4-4-cols-overlay-grid.png',
				'3-3-3-3-cols-overlay-grid'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-3-3-3-3-cols-overlay-grid.png',
				'8-4-cols-featured-grid'           => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-8-4-cols-featured-grid.png',
				'4-4-4-cols-overlay-slider'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-4-4-4-cols-overlay-slider.png',
				'3-3-3-3-cols-overlay-slider'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-3-3-3-3-cols-overlay-slider.png',
				'6-6-cols-overlay-slider'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-overlay-slider.png',
				'12-cols-overlay-slider'           => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-overlay-slider.png',
				'4-4-4-cols-classic-slider'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-4-4-4-cols-classic-slider.png',
				'3-3-3-3-cols-classic-slider'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-3-3-3-3-cols-classic-slider.png',
				'6-6-cols-classic-slider'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-6-6-cols-classic-slider.png',
				'12-cols-classic-slider'           => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/posts-12-cols-classic-slider.png',
			),
		);

		$this->radio_image( apply_filters( "{$this->id_base}_posts_layout_field_args", $args, $instance ) );
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
			'posts_layout'        => '4-4-4-cols-classic',
			'layout'              => '12-cols-center-left',

			'post_type'           => 'post',
			'handpick_posts'      => false,
			'posts'               => array(),
			'taxonomies'          => array(),
			'meta_key'            => '',
			'meta_compare'        => '=',
			'meta_value'          => '',
			'meta_value_num'      => false,
			'posts_per_page'      => 10,
			'orderby'             => 'date',
			'order'               => 'DESC',
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
			'posts_layout'        => $instance['posts_layout'],
			'post_thumbnail_size' => apply_filters( "{$this->id_base}_post_thumbnail_size", 'large', $instance['posts_layout'], $instance['post_type'] ),
			'layout'              => $instance['layout'],
			'container_layout'    => $instance['container_layout'],

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

		$instance['posts_layout']   = isset( $new_instance['posts_layout'] ) ? sanitize_text_field( $new_instance['posts_layout'] ) : '';

		$instance['post_type']      = isset( $new_instance['post_type'] ) ? sanitize_text_field( $new_instance['post_type'] ) : '';
		$instance['handpick_posts'] = ! empty( $new_instance['handpick_posts'] );
		$instance['posts']          = isset( $new_instance['posts'] ) ? $new_instance['posts'] : array();
		$instance['taxonomies']     = isset( $new_instance['taxonomies'] ) ? $new_instance['taxonomies'] : array();
		$instance['use_meta_query'] = ! empty( $new_instance['use_meta_query'] );
		$instance['meta_key']       = isset( $new_instance['meta_key'] ) ? sanitize_text_field( $new_instance['meta_key'] ) : '';
		$instance['meta_compare']   = isset( $new_instance['meta_compare'] ) ? sanitize_text_field( $new_instance['meta_compare'] ) : '';
		$instance['meta_value']     = isset( $new_instance['meta_value'] ) ? sanitize_text_field( $new_instance['meta_value'] ) : '';
		$instance['meta_value_num'] = ! empty( $new_instance['meta_value_num'] );
		$instance['posts_per_page'] = isset( $new_instance['posts_per_page'] ) ? sanitize_text_field( $new_instance['posts_per_page'] ) : '';
		$instance['orderby']        = isset( $new_instance['orderby'] ) ? sanitize_text_field( $new_instance['orderby'] ) : '';
		$instance['order']          = isset( $new_instance['order'] ) ? sanitize_text_field( $new_instance['order'] ) : '';

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
				'post_type'      => $instance['post_type'],
				'post__in'       => $instance['posts'],
				'posts_per_page' => count( $instance['posts'] ),
				'orderby'        => 'post__in',
				'order'          => 'ASC',
			);
		}
		else {
			$query_args = array(
				'post_type'      => $instance['post_type'],
				'posts_per_page' => $instance['posts_per_page'],
				'orderby'        => $instance['orderby'],
				'order'          => $instance['order'],
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

			if ( ! isset( $instance['use_meta_query'] ) || ! empty( $instance['use_meta_query'] ) ) {
				$query_args['meta_key']        = $instance['meta_key'];
				$meta_value_arg                = empty( $instance['meta_value_num'] ) ? 'meta_value' : 'meta_value_num';
				$query_args[ $meta_value_arg ] = $instance['meta_value'];
				$query_args['meta_compare']    = $instance['meta_compare'];
			}
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
		$classes[] = "grimlock-query-section--{$instance['button_format']}";
		$title     = ! empty( $instance['title'] ) ? sanitize_title( $instance['title'] ) : '';

		if ( '' !== $title ) {
			$classes[] = "grimlock-query-section--{$title}";
		}

		return $classes;
	}
}

return new Grimlock_Query_Section_Widget_Fields();
