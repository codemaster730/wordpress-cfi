<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Term_Query_Section_Block
 *
 * @author  themosaurus
 * @since   1.3.8
 * @package grimlock/inc
 */
class Grimlock_Term_Query_Section_Block extends Grimlock_Section_Block {

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.3.8
	 */
	public function __construct( $type = 'term-query-section', $domain = 'grimlock' ) {
		parent::__construct( $type, $domain );

		// General tab
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_thumbnail_field'      ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_thumbnail_size_field' ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_separator'            ), 110 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_field'           ), 140 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_wpautoped_field' ), 150 );

		// Query tab
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_taxonomy_field'       ), 100 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_handpick_terms_field' ), 110 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_terms_field'          ), 120 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_parent_field'         ), 130 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_separator'            ), 140 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_number_field'         ), 150 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_separator'            ), 160 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_orderby_field'        ), 170 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_order_field'          ), 180 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_separator'            ), 190 );
		add_filter( "{$this->id_base}_query_panel_fields",      array( $this, 'add_hide_empty_field'     ), 200 );

		// Layout tab
		add_filter( "{$this->id_base}_layout_panel_fields",     array( $this, 'add_terms_layout_field'   ), 80  );
		add_filter( "{$this->id_base}_layout_panel_fields",     array( $this, 'add_separator'            ), 90  );

		// Style tab
		remove_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_color_field'          ), 300 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Term Query Section', 'grimlock' ),
			'icon' => array(
				'foreground'=> '#000000',
				'src' => 'list-view',
			),
			'category' => 'widgets',
			'keywords' => array( __( 'query', 'grimlock' ), __( 'section', 'grimlock' ), __( 'terms', 'grimlock' ), __( 'taxonomies', 'grimlock' ) ),
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
	 * Add a select to set the taxonomy for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_taxonomy_field( $fields ) {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$choices    = array();

		foreach ( $taxonomies as $taxonomy ) {
			$choices[ $taxonomy->name ] = $taxonomy->label;
		}

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_taxonomy_field_args", array(
			'name'              => 'taxonomy',
			'label'             => esc_html__( 'Taxonomy', 'grimlock' ),
			'choices'           => $choices,
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether to handpick a set of terms to display in the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_handpick_terms_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_handpick_terms_field_args", array(
			'name'  => 'handpick_terms',
			'label' => esc_html__( 'Handpick specific terms to display in the section', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a multi select to manually select a set of terms to display in the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_terms_field( $fields ) {
		$fields[] = $this->term_select_field( apply_filters( "{$this->id_base}_terms_field_args", array(
			'name'      => 'terms',
			'label'     => esc_html__( 'Terms', 'grimlock' ),
			'taxonomy'  => '{taxonomy}', // Brackets mean that we are dynamically pulling the value from the "taxonomy" field
			'multiple'  => true,
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the parent term ID for the query.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_parent_field( $fields ) {
		$fields[] = $this->term_select_field( apply_filters( "{$this->id_base}_parent_field_args", array(
			'name'       => 'parent',
			'label'      => esc_html__( 'Parent term', 'grimlock' ),
			'taxonomy'   => '{taxonomy}', // Value in brackets {} means that we are using the value of another attribute from this block
//			'query_args' => array(
//				'hide_empty' => false,
//			),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a number field to set the number of terms to display for the query
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_number_field( $fields ) {
		$fields[] = $this->number_field( apply_filters( "{$this->id_base}_number_field_args", array(
			'name'  => 'number',
			'label' => esc_html__( 'Number of terms to display', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
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
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox to set whether the empty terms must be displayed.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_hide_empty_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_hide_empty_field_args", array(
			'name'  => 'hide_empty',
			'label' => esc_html__( 'Hide empty terms', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
					'operator' => '==',
					'value'    => false,
				),
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
	public function add_terms_layout_field( $fields ) {
		$fields[] = $this->radio_image_field( apply_filters( "{$this->id_base}_terms_layout_field_args", array(
			'name'    => 'terms_layout',
			'label'   => esc_html__( 'Layout', 'grimlock' ),
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
			'conditional_logic' => array(
				array(
					'field'    => 'handpick_terms',
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

		return array_merge( $defaults, array(
			'button_text'         => esc_html__( 'More items', 'grimlock' ),

			'terms_layout'        => '3-3-3-3-cols-classic',

			'taxonomy'            => 'category',
			'handpick_terms'      => false,
			'terms'               => array(),
			'parent'              => '',
			'number'              => '',
			'orderby'             => '',
			'order'               => 'ASC',
			'hide_empty'          => false,
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
		do_action( 'grimlock_term_query_section', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
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
			'terms_layout'        => $attributes['terms_layout'],
			'term_thumbnail_size' => apply_filters( "{$this->id_base}_term_thumbnail_size", 'large', $attributes['terms_layout'], $attributes['taxonomy'] ),
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

		$attributes['terms_layout']   = isset( $new_attributes['terms_layout'] ) ? sanitize_text_field( $new_attributes['terms_layout'] ) : '';
		$attributes['taxonomy']       = isset( $new_attributes['taxonomy'] ) ? sanitize_text_field( $new_attributes['taxonomy'] ) : '';
		$attributes['handpick_terms'] = isset( $new_attributes['handpick_terms'] ) && filter_var( $new_attributes['handpick_terms'], FILTER_VALIDATE_BOOLEAN );
		$attributes['terms']          = isset( $new_attributes['terms'] ) ? $new_attributes['terms'] : array();
		$attributes['taxonomy']       = isset( $new_attributes['taxonomy'] ) ? sanitize_text_field( $new_attributes['taxonomy'] ) : '';
		$attributes['parent']         = isset( $new_attributes['parent'] ) ? intval( $new_attributes['parent'] ) : 0;
		$attributes['number']         = isset( $new_attributes['number'] ) ? sanitize_text_field( $new_attributes['number'] ) : '';
		$attributes['orderby']        = isset( $new_attributes['orderby'] ) ? sanitize_text_field( $new_attributes['orderby'] ) : '';
		$attributes['order']          = isset( $new_attributes['order'] ) ? sanitize_text_field( $new_attributes['order'] ) : '';
		$attributes['hide_empty']     = isset( $new_attributes['hide_empty'] ) && filter_var( $new_attributes['hide_empty'], FILTER_VALIDATE_BOOLEAN );

		return $attributes;
	}

	/**
	 * Build the WP_Term_Query instance that will be used in the widget
	 *
	 * @param array $attributes Block attributes
	 *
	 * @return WP_Term_Query The term query for the widget
	 */
	protected function make_query( $attributes ) {
		if ( ! empty( $attributes['handpick_terms'] ) ) {
			$args = array(
				'taxonomy'   => $attributes['taxonomy'],
				'include'    => $attributes['terms'],
				'number'     => count( $attributes['terms'] ),
				'orderby'    => 'include',
				'order'      => 'ASC',
				'count'      => true,
				'hide_empty' => false,
			);
		}
		else {
			$args = array(
				'taxonomy'   => $attributes['taxonomy'],
				'number'     => $attributes['number'],
				'orderby'    => $attributes['orderby'],
				'order'      => $attributes['order'],
				'count'      => true,
				'hide_empty' => ! empty( $attributes['hide_empty'] ),
			);

			// Add parent argument for hierarchical taxonomy terms only.
			if ( ! empty( $attributes['parent'] ) ) {
				$args['parent'] = $attributes['parent'];
			}
		}

		return new WP_Term_Query( $args );
	}
}

return new Grimlock_Term_Query_Section_Block();
