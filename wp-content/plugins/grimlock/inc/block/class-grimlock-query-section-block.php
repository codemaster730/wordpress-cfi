<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Query_Section_Block
 *
 * @author  themosaurus
 * @since   1.3.5
 * @package grimlock/inc
 */
class Grimlock_Query_Section_Block extends Grimlock_Section_Block {

	private $post_types_shown_in_rest = array();

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.3.5
	 */
	public function __construct( $type = 'query-section', $domain = 'grimlock' ) {
		parent::__construct( $type, $domain );

		// General tab
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_thumbnail_field'      ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_thumbnail_size_field' ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_separator'            ), 110 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_field'           ), 140 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_wpautoped_field' ), 150 );

		// Query tab
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_site_field'           ), 90 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_post_type_field'      ), 100 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_handpick_posts_field' ), 110 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_posts_field'          ), 120 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_separator'            ), 130 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_taxonomies_field'     ), 140 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_separator'            ), 150 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_orderby_field'        ), 160 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_order_field'          ), 170 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_separator'            ), 180 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_posts_per_page_field' ), 190 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_separator'            ), 200 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_use_meta_query_field' ), 210 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_meta_key_field'       ), 220 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_meta_compare_field'   ), 230 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_meta_value_field'     ), 240 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_meta_value_num_field' ), 250 );

		// Layout tab
		add_filter( "{$this->id_base}_layout_panel_fields",     array( $this, 'add_posts_layout_field'       ), 70  );
		add_filter( "{$this->id_base}_layout_panel_fields",     array( $this, 'add_auto_slide_enabled_field' ), 80  );
		add_filter( "{$this->id_base}_layout_panel_fields",     array( $this, 'add_separator'                ), 90  );

		// Style tab
		remove_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_color_field'          ), 300 );

		add_filter( 'register_post_type_args',                  array( $this, 'post_type_show_in_rest' ), 1000, 2 );
		add_filter( 'use_block_editor_for_post_type',           array( $this, 'disable_block_editor_for_post_types_shown_in_rest' ), 10, 2 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Query Section', 'grimlock' ),
			'icon' => array(
				'foreground'=> '#000000',
				'src' => 'editor-code',
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
	 * Get block panels
	 *
	 * @return array Array of panels
	 */
	public function get_panels() {
		return array(
			'general' => esc_html__( 'General', 'grimlock' ),
			'query'   => esc_html__( 'Query', 'grimlock' ),
			'layout'  => esc_html__( 'Layout', 'grimlock' ),
			'style'   => esc_html__( 'Style', 'grimlock' ),
		);
	}

	/**
	 * Add a select to set the post type for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_post_type_field( $fields ) {
		$post_types = get_post_types( array( 'public' => true ), 'objects' );
		$post_types_choices = array();
		foreach ( $post_types as $post_type ) {
			$post_types_choices[ $post_type->name ] = $post_type->label;
		}
		// Remove the media post type
		unset( $post_types_choices['attachment'] );

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_post_type_field_args", array(
			'name'    => 'post_type',
			'label'   => esc_html__( 'Post type', 'grimlock' ),
			'choices' => $post_types_choices,
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the post type for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_site_field( $fields ) {
		if ( is_multisite() ) {
			/** @var WP_Site[] $sites */
			$sites = get_sites();
			$sites_choices = array(
				esc_html__( 'This site', 'grimlock' ),
			);
			foreach ( $sites as $site ) {
				if ( get_current_blog_id() != $site->blog_id && current_user_can_for_blog( $site->blog_id, 'edit_posts' ) ) {
					$sites_choices[ $site->blog_id ] = $site->blogname;
				}
			}

			$fields[] = $this->select_field( apply_filters( "{$this->id_base}_site_field_args", array(
				'name'              => 'site',
				'label'             => esc_html__( 'Query site in network', 'grimlock' ),
				'choices'           => $sites_choices,
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
	 * Add a toggle field to set whether to handpick a set of posts to display in the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_handpick_posts_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_handpick_posts_field_args", array(
			'name'  => 'handpick_posts',
			'label' => esc_html__( 'Handpick specific posts to display in the section', 'grimlock' ),
		) ) );

		return $fields;
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
			'post_type' => '{post_type}', // Brackets mean that we are dynamically pulling the value from the "post_type" field
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
		$taxonomies         = get_taxonomies( array(), 'objects' );
		$taxonomies_choices = array();

		foreach ( $taxonomies as $taxonomy ) {
			$terms         = get_terms( array( 'taxonomy' => $taxonomy->name ) );
			$terms_choices = array();

			foreach ( $terms as $term ) {
				$terms_choices[ $taxonomy->name . '|' . $term->slug ] = $term->name;
			}

			$taxonomies_choices[ $taxonomy->name ] = array(
				'label'      => $taxonomy->label,
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
	 * Add a number field to set the posts per page for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_posts_per_page_field( $fields ) {
		$fields[] = $this->number_field( apply_filters( "{$this->id_base}_posts_per_page_field_args", array(
			'name'  => 'posts_per_page',
			'label' => esc_html__( 'Number of posts to display', 'grimlock' ),
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
	 * Add a select to set the "order by" for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_orderby_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_orderby_field_args", array(
			'name'    => 'orderby',
			'label'   => esc_html__( 'Order by', 'grimlock' ),
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
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the order for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_order_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_order_field_args", array(
			'name'    => 'order',
			'label'   => esc_html__( 'Order', 'grimlock' ),
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
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether to use a meta query to filter the query results
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_use_meta_query_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_use_meta_query_field_args", array(
			'name'  => 'use_meta_query',
			'label' => esc_html__( 'Filter using metadata key and value', 'grimlock' ),
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
	 * Add a text field to set the meta key for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_meta_key_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_meta_key_field_args", array(
			'name'  => 'meta_key',
			'label' => esc_html__( 'Meta Key', 'grimlock' ),
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
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the meta compare for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_meta_compare_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_meta_compare_field_args", array(
			'name'    => 'meta_compare',
			'label'   => esc_html__( 'Meta Compare', 'grimlock' ),
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
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set the meta value for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_meta_value_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_meta_value_field_args", array(
			'name'  => 'meta_value',
			'label' => esc_html__( 'Meta Value', 'grimlock' ),
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
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether the meta value is to be considered as a number in the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_meta_value_num_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_meta_value_num_field_args", array(
			'name'  => 'meta_value_num',
			'label' => esc_html__( 'Treat meta value as a number', 'grimlock' ),
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
		) ) );

		return $fields;
	}

	/**
	 * Add a radio image field to set the items layout
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_posts_layout_field( $fields ) {
		$fields[] = $this->radio_image_field( apply_filters( "{$this->id_base}_posts_layout_field_args", array(
			'name'    => 'posts_layout',
			'label'   => esc_html__( 'Layout', 'grimlock' ),
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
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether to enable auto slide for slider layouts
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_auto_slide_enabled_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_auto_slide_enabled_field_args", array(
			'name'  => 'auto_slide_enabled',
			'label' => esc_html__( 'Enable slider autoplay', 'grimlock' ),
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'posts_layout',
						'operator' => '==',
						'value'    => '12-cols-overlay-slider',
					),
					array(
						'field'    => 'posts_layout',
						'operator' => '==',
						'value'    => '6-6-cols-overlay-slider',
					),
					array(
						'field'    => 'posts_layout',
						'operator' => '==',
						'value'    => '4-4-4-cols-overlay-slider',
					),
					array(
						'field'    => 'posts_layout',
						'operator' => '==',
						'value'    => '3-3-3-3-cols-overlay-slider',
					),
				)
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a radio image field to set the layout of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_layout_field( $fields ) {
		$fields[] = $this->radio_image_field( apply_filters( "{$this->id_base}_layout_field_args", array(
			'name'    => 'layout',
			'label'   => esc_html__( 'Alignment', 'grimlock' ),
			'choices' => array(
				'12-cols-center-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center-left.png',
				'12-cols-center'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center.png',
				'12-cols-left'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-left.png',
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

		return array_merge( $defaults, array(
			'posts_layout'        => '4-4-4-cols-classic',
			'auto_slide_enabled'  => true,
			'layout'              => '12-cols-center-left',

			'site'                => 0,
			'post_type'           => 'post',
			'handpick_posts'      => false,
			'posts'               => array(),
			'taxonomies'          => array(),
			'use_meta_query'      => false,
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
	 * Get the component args
	 *
	 * @param array $attributes Block attributes
	 *
	 * @return array Component args
	 */
	protected function get_component_args( $attributes ) {
		$args = parent::get_component_args( $attributes );

		return array_merge( $args, array(
			'posts_layout'        => $attributes['posts_layout'],
			'auto_slide_enabled'  => $attributes['auto_slide_enabled'],
			'post_thumbnail_size' => apply_filters( "{$this->id_base}_post_thumbnail_size", 'large', $attributes['posts_layout'], $attributes['post_type'] ),
			'layout'              => $attributes['layout'],
			'container_layout'    => $attributes['container_layout'],

			'site'                => $attributes['site'],
			'query'               => $this->make_query( $attributes ),
		) );
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

		$attributes['posts_layout']       = isset( $new_attributes['posts_layout'] ) ? sanitize_text_field( $new_attributes['posts_layout'] ) : '';
		$attributes['auto_slide_enabled'] = isset( $new_attributes['auto_slide_enabled'] ) && filter_var( $new_attributes['auto_slide_enabled'], FILTER_VALIDATE_BOOLEAN );

		$attributes['site']               = isset( $new_attributes['site'] ) ? intval( $new_attributes['site'] ) : 0;
		$attributes['post_type']          = isset( $new_attributes['post_type'] ) ? sanitize_text_field( $new_attributes['post_type'] ) : '';
		$attributes['handpick_posts']     = isset( $new_attributes['handpick_posts'] ) && filter_var( $new_attributes['handpick_posts'], FILTER_VALIDATE_BOOLEAN );
		$attributes['posts']              = isset( $new_attributes['posts'] ) ? $new_attributes['posts'] : array();
		$attributes['taxonomies']         = isset( $new_attributes['taxonomies'] ) ? $new_attributes['taxonomies'] : array();
		$attributes['use_meta_query']     = isset( $new_attributes['use_meta_query'] ) && filter_var( $new_attributes['use_meta_query'], FILTER_VALIDATE_BOOLEAN );
		$attributes['meta_key']           = isset( $new_attributes['meta_key'] ) ? sanitize_text_field( $new_attributes['meta_key'] ) : '';
		$attributes['meta_compare']       = isset( $new_attributes['meta_compare'] ) ? sanitize_text_field( $new_attributes['meta_compare'] ) : '';
		$attributes['meta_value']         = isset( $new_attributes['meta_value'] ) ? sanitize_text_field( $new_attributes['meta_value'] ) : '';
		$attributes['meta_value_num']     = isset( $new_attributes['meta_value_num'] ) && filter_var( $new_attributes['meta_value_num'], FILTER_VALIDATE_BOOLEAN );
		$attributes['posts_per_page']     = isset( $new_attributes['posts_per_page'] ) ? sanitize_text_field( $new_attributes['posts_per_page'] ) : '';
		$attributes['orderby']            = isset( $new_attributes['orderby'] ) ? sanitize_text_field( $new_attributes['orderby'] ) : '';
		$attributes['order']              = isset( $new_attributes['order'] ) ? sanitize_text_field( $new_attributes['order'] ) : '';

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
				'post_type'      => $attributes['post_type'],
				'post__in'       => $attributes['posts'],
				'posts_per_page' => count( $attributes['posts'] ),
				'orderby'        => 'post__in',
				'order'          => 'ASC',
			);
		}
		else {
			$query_args = array(
				'post_type'      => $attributes['post_type'],
				'posts_per_page' => $attributes['posts_per_page'],
				'orderby'        => $attributes['orderby'],
				'order'          => $attributes['order'],
			);

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

			if ( ! empty( $attributes['use_meta_query'] ) ) {
				$query_args['meta_key']        = $attributes['meta_key'];
				$meta_value_arg                = empty( $attributes['meta_value_num'] ) ? 'meta_value' : 'meta_value_num';
				$query_args[ $meta_value_arg ] = $attributes['meta_value'];
				$query_args['meta_compare']    = $attributes['meta_compare'];
			}
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

	/**
	 * Make public post types available in the rest api for users with "edit_posts" or "edit_pages" capabilities
	 *
	 * @param array $args
	 * @param array $post_type
	 *
	 * @return mixed
	 */
	public function post_type_show_in_rest( $args, $post_type ) {
		// Do it only for public post types
		if ( empty( $args['show_in_rest'] ) && ! empty( $args['public'] ) && ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) ) {
			$args['show_in_rest'] = true;

			// Forcing show_in_rest to true will automatically enable Gutenberg for this post type which is potentially not ready for Gutenberg
			// so we make sure to manually disable Gutenberg for this post type afterwards
			$this->post_types_shown_in_rest[] = $post_type;
		}

		return $args;
	}

	/**
	 * Disable the block editor for post types that we forcefully made available in the rest api
	 *
	 * @param bool $use_block_editor Whether to use the block editor for $post_type
	 * @param string $post_type The post type
	 *
	 * @return bool
	 */
	public function disable_block_editor_for_post_types_shown_in_rest( $use_block_editor, $post_type ) {
		if ( in_array( $post_type, $this->post_types_shown_in_rest ) ) {
			return false;
		}

		return $use_block_editor;
	}
}

return new Grimlock_Query_Section_Block();
