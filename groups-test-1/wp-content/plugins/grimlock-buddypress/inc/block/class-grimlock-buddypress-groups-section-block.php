<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_BuddyPress_Groups_Section_Block
 *
 * @author  themosaurus
 * @since   1.3.11
 * @package grimlock-buddypress/inc
 */
class Grimlock_BuddyPress_Groups_Section_Block extends Grimlock_Section_Block {

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.1.5
	 */
	public function __construct( $type = 'groups-section', $domain = 'grimlock-buddypress' ) {
		parent::__construct( $type, $domain );

		// General Panel
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_thumbnail_field'          ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_thumbnail_size_field'     ), 100 );
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_text_field'               ), 140 );
		remove_filter( "{$this->id_base}_general_panel_fields",  array( $this, 'add_text_wpautoped_field'     ), 150 );

		// Query Panel
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_max_groups_field'     ), 100, 2 );
		add_action( "{$this->id_base}_query_panel_fields",      array( $this, 'add_group_default_field'  ), 110, 2 );

		// Layout Panel
		add_action( "{$this->id_base}_layout_panel_fields",     array( $this, 'add_groups_layout_field'  ), 90,  2 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock BuddyPress Groups Section', 'grimlock-buddypress' ),
			'icon' => array(
				'foreground'=> '#000000',
				'src' => 'groups',
			),
			'category' => 'widgets',
			'keywords' => array( __( 'query', 'grimlock-buddypress' ), __( 'section', 'grimlock-buddypress' ), __( 'groups', 'grimlock-buddypress' ) ),
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
			'general' => esc_html__( 'General', 'grimlock-buddypress' ),
			'query'   => esc_html__( 'Query', 'grimlock-buddypress' ),
			'layout'  => esc_html__( 'Layout', 'grimlock-buddypress' ),
			'style'   => esc_html__( 'Style', 'grimlock-buddypress' ),
		);
	}

	/**
	 * Add a text field to to set the maximum number of groups.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_max_groups_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_max_groups_field_args", array(
			'name'     => 'max_groups',
			'label'    => esc_html__( 'Max groups to show', 'grimlock-buddypress' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the default group.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_group_default_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_group_default_field_args", array(
			'name'  => 'group_default',
			'label' => esc_html__( 'Default groups to show', 'grimlock-buddypress' ),
			'choices' => array(
				'newest'       => esc_html__( 'Newest', 'grimlock-buddypress' ),
				'active'       => esc_html__( 'Active', 'grimlock-buddypress' ),
				'popular'      => esc_html__( 'Popular', 'grimlock-buddypress' ),
				'alphabetical' => esc_html__( 'Alphabetical', 'grimlock-buddypress' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a radio image field to set the layout of groups for the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_groups_layout_field( $fields ) {
		$fields[] = $this->radio_image_field( apply_filters( "{$this->id_base}_groups_layout_field_args", array(
			'name'    => 'groups_layout',
			'label'   => esc_html__( 'Layout', 'grimlock-buddypress' ),
			'choices' => array(
				'4-4-4-cols-classic'    => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/template-groups-4-4-4-cols-classic.png',
				'3-3-3-3-cols-classic'  => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/template-groups-3-3-3-3-cols-classic.png',
				'2-2-2-2-2-2-cols-grid' => GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/template-groups-2-2-2-2-2-2-cols-grid.png',
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
			'label'   => esc_html__( 'Alignment', 'grimlock-buddypress' ),
			'choices' => array(
				'12-cols-left'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-left.png',
				'12-cols-center'      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center.png',
				'12-cols-center-left' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-alignment-12-cols-center-left.png',
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
	 * Render the Gutenberg block
	 *
	 * @param $attributes
	 * @param $content
	 *
	 * @return string
	 */
	public function render_block( $attributes, $content ) {
		if ( function_exists( 'buddypress' ) ) {
			$min = bp_core_get_minified_asset_suffix();
			wp_enqueue_script( 'groups_widget_groups_list-js', buddypress()->plugin_url . "bp-groups/js/widget-groups{$min}.js", array( 'jquery' ), bp_get_version(), true );
		}

		$attributes = $this->sanitize_attributes( $attributes );
		ob_start();
		do_action( 'grimlock_buddypress_groups_section', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
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
			'groups_layout'       => $attributes['groups_layout'],

			'max_groups'          => $attributes['max_groups'],
			'group_default'       => $attributes['group_default'],
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

		$attributes['groups_layout'] = isset( $new_attributes['groups_layout'] ) ? sanitize_text_field( $new_attributes['groups_layout'] ) : '';

		$attributes['max_groups']    = isset( $new_attributes['max_groups'] ) ? strip_tags( $new_attributes['max_groups'] ) : '';
		$attributes['group_default'] = isset( $new_attributes['group_default'] ) ? strip_tags( $new_attributes['group_default'] ) : '';

		return $attributes;
	}

	/**
	 * Get the block classes
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return array The block classes
	 */
	protected function get_classes( $attributes ) {
		$classes = parent::get_classes( $attributes );

		// Necessary for the js of the groups widget to work in the block
		$classes[] = 'widget';

		return $classes;
	}
}

return new Grimlock_BuddyPress_Groups_Section_Block();
