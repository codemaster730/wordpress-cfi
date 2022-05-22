<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Base_Block
 *
 * @author  themosaurus
 * @since   1.3.5
 * @package grimlock/inc
 */
abstract class Grimlock_Base_Block {
	/**
	 * @var string Block type
	 */
	protected $type;

	/**
	 * @var string Block domain. Used to generate js handle, filenames, text-domain, ...
	 */
	protected $domain;

	/**
	 * @var string Block base id. Used to generate hook names.
	 */
	protected $id_base;

	/**
	 * @var array Default values for the block
	 */
	protected $defaults;

	/**
	 * @var array Supported attributes for the block (used by Gutenberg for block validation)
	 */
	protected $supported_attributes;

	/**
	 * @var array Field panels for the block
	 */
	protected $panels;

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.3.5
	 */
	public function __construct( $type, $domain ) {
		$this->type            = $type;
		$this->domain          = $domain;
		$this->id_base         = str_replace( '-', '_', $this->domain ) . '_' . str_replace( '-', '_', $this->type ) . '_block';

		add_action( 'init',                    array( $this, 'init_block'        ), 1000 ); // Low priority to make sure all hooks, post types, etc... had time to register
		add_filter( 'grimlock_blocks_js_data', array( $this, 'add_block_js_data' ), 10   );
	}

	/**
	 * Initialize the block
	 */
	public function init_block() {
		$this->defaults             = apply_filters( "{$this->id_base}_defaults", $this->get_defaults() );
		$this->supported_attributes = apply_filters( "{$this->id_base}_supported_attributes", array(
			'align'     => array(
				'type' => 'string',
			),
			'className' => array(
				'type' => 'string',
			),
			'anchor'    => array(
				'type' => 'string',
			),
		) );

		// Get the field panels registered for this block
		$this->panels = apply_filters( "{$this->id_base}_panels", $this->get_panels() );

		// Loop over each panel
		if ( ! empty( $this->panels ) ) {
			foreach ( $this->panels as $slug => $label ) {

				// Get the fields that belong to this panel
				$panel_fields = apply_filters( "{$this->id_base}_{$slug}_panel_fields", array() );

				// Populate the $this->supported_attributes property with each field
				$this->add_supported_attributes( $panel_fields );

				// Prepare the panel data that will be sent to the JS handler for this block
				$this->panels[ $slug ] = array(
					'label'  => $label,
					'fields' => $panel_fields,
				);
			}
		}

		$this->register_block();
	}

	/**
	 * Add data for this block to the blocks JS data
	 *
	 * @param array $data The array of JS data
	 *
	 * @return array The modified array of JS data
	 */
	public function add_block_js_data( $data ) {
		$data['blocks'][ $this->id_base ] = array(
			'name'   => "{$this->domain}/{$this->type}",
			'args'   => array_merge( array(
				'example' => array(), // This enables the block preview in the Blocks panel
			), $this->get_block_js_args() ),
			'panels' => $this->panels,
		);

		return $data;
	}

	/**
	 * Register the block
	 */
	protected function register_block() {
		register_block_type( "{$this->domain}/{$this->type}", array(
			'editor_script'   => 'grimlock-blocks',
			'editor_style'    => 'grimlock-blocks',
			'render_callback' => array( $this, 'render_block' ),
			'attributes'      => apply_filters( "{$this->id_base}_supported_attributes", $this->supported_attributes ),
		) );
	}

	/**
	 * Add fields to the supported attributes of the block
	 *
	 * @param array $fields Array of fields
	 */
	protected function add_supported_attributes( $fields ) {
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			return;
		}

		foreach ( $fields as $field ) {
			if ( empty( $field['name'] ) || empty( $field['attribute_type'] ) ) {
				continue;
			}

			$this->supported_attributes[ $field['name'] ] = array(
				'type'    => $field['attribute_type'],
				'default' => isset( $this->defaults[ $field['name'] ] ) ? $this->defaults[ $field['name'] ] : null,
			);

			if ( ! empty( $field['old_name'] ) ) {
				$this->supported_attributes[ $field['old_name'] ] = array( 'type' => $field['attribute_type'] );
			}
		}
	}

	/**
	 * Parses and returns args for a text field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function text_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'text',
			'name'           => '',
			'label'          => '',
			'placeholder'    => '',
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Parses and returns args for a date field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function date_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'date',
			'name'           => '',
			'label'          => '',
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Parses and returns args for a number field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function number_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'number',
			'name'           => '',
			'label'          => '',
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Parses and returns args for a textarea field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function textarea_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'textarea',
			'name'           => '',
			'label'          => '',
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Parses and returns args for an image field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function image_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'image',
			'name'           => '',
			'label'          => '',
			'gallery'        => false,
			'attribute_type' => empty( $args['gallery'] ) ? 'integer' : 'array',
		) );
	}

	/**
	 * Parses and returns args for a toggle field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function toggle_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'toggle',
			'name'           => '',
			'label'          => '',
			'attribute_type' => 'boolean',
		) );
	}

	/**
	 * Parses and returns args for a select field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function select_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'select',
			'name'           => '',
			'label'          => '',
			'choices'        => array(),
			'multiple'       => false,
			'attribute_type' => empty( $args['multiple'] ) ? 'string' : 'array',
		) );
	}

	/**
	 * Parses and returns args for a select field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function term_select_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'termSelect',
			'name'           => '',
			'label'          => '',
			'taxonomy'       => 'category',
			'query_args'     => array(),
			'empty_choice'   => esc_html__( '- Select -', 'grimlock' ),
			'multiple'       => false,
			'attribute_type' => empty( $args['multiple'] ) ? 'string' : 'array',
		) );
	}

	/**
	 * Parses and returns args for a select field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function post_select_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'postSelect',
			'name'           => '',
			'label'          => '',
			'post_type'      => 'post',
			'query_args'     => array(),
			'multiple'       => false,
			'attribute_type' => empty( $args['multiple'] ) ? 'string' : 'array',
		) );
	}

	/**
	 * Parses and returns args for a radio image field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function radio_image_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'radioImage',
			'name'           => '',
			'label'          => '',
			'choices'        => array(),
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Parses and returns args for an alignment matrix field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function alignment_matrix_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'alignmentMatrix',
			'name'           => '',
			'label'          => '',
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Parses and returns args for a range field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function range_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'range',
			'name'           => '',
			'label'          => '',
			'min'            => '',
			'max'            => '',
			'unit'           => '%',
			'step'           => 0.25,
			'attribute_type' => 'number',
		) );
	}

	/**
	 * Parses and returns args for a color field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function color_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'color',
			'name'           => '',
			'label'          => '',
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Parses and returns args for a gradient field
	 *
	 * @param array $args Field args
	 *
	 * @return array Parsed field args
	 */
	public static function gradient_field( $args = array() ) {
		return wp_parse_args( $args, array(
			'type'           => 'gradient',
			'name'           => '',
			'label'          => '',
			'attribute_type' => 'string',
		) );
	}

	/**
	 * Returns args for an "Edit Menu" link
	 *
	 * @param array $args Field args
	 *
	 * @return array Link args
	 */
	public static function edit_menu_link( $args = array() ) {
		$nav_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		$menu_ids  = array();

		foreach( $nav_menus as $term ) {
			$menu_ids[ $term->slug ] = $term->term_id;
		}

		return wp_parse_args( $args, array(
			'type'       => 'editMenuLink',
			'menu'       => '',
			'menu_ids'   => $menu_ids,
			'admin_url'  => admin_url(),
			'link_label' => esc_html__( 'Edit menu', 'grimlock' ),
		) );
	}

	/**
	 * Returns args for a separator
	 *
	 * @param array $args Field args
	 *
	 * @return array Separator args
	 */
	public static function separator( $args = array() ) {
		return wp_parse_args( $args, array(
			'type' => 'separator',
		) );
	}

	/**
	 * Get block args for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	abstract function get_block_js_args();

	/**
	 * Get block panels
	 *
	 * @return array Array of panels
	 */
	abstract function get_panels();

	/**
	 * Get default field values for the block
	 *
	 * @return array Array of default field values
	 */
	abstract function get_defaults();

	/**
	 * Render the Gutenberg block
	 *
	 * @param $attributes
	 * @param $content
	 *
	 * @return string Block HTML
	 */
	public abstract function render_block( $attributes, $content );
}
