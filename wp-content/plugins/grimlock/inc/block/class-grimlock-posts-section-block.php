<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Posts_Section_Block
 *
 * @author  themosaurus
 * @since   1.3.8
 * @package grimlock/inc
 */
class Grimlock_Posts_Section_Block extends Grimlock_Query_Section_Block {

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.3.8
	 */
	public function __construct( $type = 'posts-section', $domain = 'grimlock' ) {
		parent::__construct( $type, $domain );

		// Query tab
		remove_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_post_type_field'        ), 100 );

		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_post_format_field'         ), 115 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_ignore_sticky_posts_field' ), 165 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_only_sticky_posts_field'   ), 166 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Posts Section', 'grimlock' ),
			'icon' => array(
				'foreground'=> '#000000',
				'src' => 'grid-view',
			),
			'category' => 'widgets',
			'keywords' => array( __( 'query', 'grimlock' ), __( 'section', 'grimlock' ), __( 'posts', 'grimlock' ) ),
			'supports' => array(
				'html'   => false,
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
			),
		);
	}

	/**
	 * Add a multi select to manually select a set of posts to display in the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_posts_field( $fields ) {
		$fields[] = $this->post_select_field( apply_filters( "{$this->id_base}_posts_field_args", array(
			'name'      => 'posts',
			'label'     => esc_html__( 'Posts', 'grimlock' ),
			'post_type' => 'post',
			'multiple'  => true,
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the taxonomies for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_taxonomies_field( $fields ) {
		$taxonomies_choices = array();

		foreach ( $this->get_taxonomies() as $name => $label ) {
			$terms         = get_terms( array( 'taxonomy' => $name ) );
			$terms_choices = array();

			foreach ( $terms as $term ) {
				$terms_choices[ $name . '|' . $term->slug ] = $term->name;
			}

			$taxonomies_choices[ $name ] = array(
				'label'      => $label,
				'subchoices' => $terms_choices,
			);
		}

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_taxonomies_field_args", array(
			'name'     => 'taxonomies',
			'label'    => esc_html__( 'Taxonomies', 'grimlock' ),
			'choices'  => $taxonomies_choices,
			'multiple' => true,
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Get taxonomies displayed in the taxonomies field
	 *
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
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_post_format_field( $fields ) {
		$terms = get_terms( array(
			'taxonomy' => 'post_format',
		) );

		if ( ! is_wp_error( $terms ) ) {
			$choices = array(
				'post-format-all'      => esc_html__( 'All', 'grimlock' ),
				'post-format-standard' => esc_html__( 'Standard', 'grimlock' ),
			);

			foreach ( $terms as $term ) {
				$choices[ $term->slug ] = $term->name;
			}

			$fields[] = $this->select_field( apply_filters( "{$this->id_base}_post_format_field_args", array(
				'name'    => 'post_format',
				'label'   => esc_html__( 'Format', 'grimlock' ),
				'choices' => $choices,
				'conditional_logic' => array(
					array(
						'field'    => 'handpick_posts',
						'operator' => '==',
						'value'    => false,
					),
				),
			) ) );
		}

		return $fields;
	}

	/**
	 * Add a toggle to set whether the sticky posts must be ignored or not.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_ignore_sticky_posts_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_ignore_sticky_posts_field_args", array(
			'name'  => 'ignore_sticky_posts',
			'label' => esc_html__( 'Ignore sticky posts', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'only_sticky_posts',
					'operator' => '==',
					'value'    => false,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether to include only sticky posts
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_only_sticky_posts_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_only_sticky_posts_field_args", array(
			'name'  => 'only_sticky_posts',
			'label' => esc_html__( 'Only sticky posts', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'ignore_sticky_posts',
					'operator' => '==',
					'value'    => false,
				),
				array(
					'field'    => 'handpick_posts',
					'operator' => '==',
					'value'    => false,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Get default field values for the block
	 *
	 * @return array Array of default field values
	 */
	public function get_defaults() {
		$defaults = parent::get_defaults();

		$page_for_posts_url = get_permalink( get_option( 'page_for_posts' ) );

		return array_merge( $defaults, array(
			'button_text'         => esc_html__( 'More posts', 'grimlock' ),
			'button_link'         => $page_for_posts_url ? $page_for_posts_url : home_url(),

			'post_format'         => 'post-format-standard',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'only_sticky_posts'   => false,
		) );
	}

	/**
	 * Render the Gutenberg block
	 *
	 * @param $attributes
	 * @param $content
	 *
	 * @return string
	 */
	public function render_block( $attributes, $content ) {
		$attributes = $this->sanitize_attributes( $attributes );
		ob_start();
		do_action( 'grimlock_query_section', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
		return ob_get_clean();
	}

	/**
	 * Handles sanitizing attributes for the current block instance.
	 *
	 * @param array $new_attributes New attributes for the current block instance
	 *
	 * @return array Attributes to save
	 */
	public function sanitize_attributes( $new_attributes ) {
		$attributes = parent::sanitize_attributes( $new_attributes );

		$attributes['post_format']         = isset( $new_attributes['post_format'] ) ? sanitize_text_field( $new_attributes['post_format'] ) : '';
		$attributes['ignore_sticky_posts'] = isset( $new_attributes['ignore_sticky_posts'] ) && filter_var( $new_attributes['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN );
		$attributes['only_sticky_posts']   = isset( $new_attributes['only_sticky_posts'] ) && filter_var( $new_attributes['only_sticky_posts'], FILTER_VALIDATE_BOOLEAN );

		return $attributes;
	}

	/**
	 * Build the WP_Query instance that will be used in the block
	 *
	 * @param array $attributes Block attributes
	 *
	 * @return WP_Query The query for the block
	 */
	protected function make_query( $attributes ) {
		if ( ! empty( $attributes['handpick_posts'] ) ) {
			$query_args = array(
				'post_type'           => 'post',
				'ignore_sticky_posts' => $attributes['ignore_sticky_posts'],
				'post__in'            => $attributes['posts'],
				'posts_per_page'      => count( $attributes['posts'] ),
				'orderby'             => 'post__in',
				'order'               => 'ASC',
			);
		}
		else {
			$query_args = array(
				'post_type'           => 'post',
				'posts_per_page'      => $attributes['posts_per_page'],
				'ignore_sticky_posts' => $attributes['only_sticky_posts'] ? true : $attributes['ignore_sticky_posts'],
				'orderby'             => $attributes['orderby'],
				'order'               => $attributes['order'],
			);

			if ( ! empty( $attributes['only_sticky_posts'] ) ) {
				$stickies               = get_option( 'sticky_posts' );
				$query_args['post__in'] = ! empty( $stickies ) ? $stickies : array( 0 );
			}

			if ( ! empty( $attributes['taxonomies'] ) ) {
				$taxonomies_terms = array();
				foreach ( $attributes['taxonomies'] as $term ) {
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

			if ( isset( $attributes['post_format'] ) && 'post-format-all' !== $attributes['post_format'] ) {
				if ( 'post-format-standard' === $attributes['post_format'] ) {
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
							'terms'    => $attributes['post_format'],
						),
					);
				}

				$query_args['tax_query'] = isset( $query_args['tax_query'] ) ? array_merge( $query_args['tax_query'], $tax_query ) : $tax_query;
			}

			$query_args['meta_key']        = $attributes['meta_key'];
			$meta_value_arg                = empty( $attributes['meta_value_num'] ) ? 'meta_value' : 'meta_value_num';
			$query_args[ $meta_value_arg ] = $attributes['meta_value'];
			$query_args['meta_compare']    = $attributes['meta_compare'];
		}

		$query_args = apply_filters( "{$this->id_base}_query_args", $query_args, $attributes );

		$switch_to_blog = is_multisite() && ! empty( $attributes['site'] ) && ! empty( get_site( $attributes['site'] ) );
		if ( $switch_to_blog ) {
			switch_to_blog( $attributes['site'] );
		}

		$query = new WP_Query( $query_args );

		if ( $switch_to_blog ) {
			restore_current_blog();
		}

		return $query;
	}
}

return new Grimlock_Posts_Section_Block();
