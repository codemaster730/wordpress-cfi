<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Nav_Menu_Section_Block
 *
 * @author  themosaurus
 * @since   1.3.5
 * @package grimlock/inc
 */
class Grimlock_Nav_Menu_Section_Block extends Grimlock_Section_Block {

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.3.5
	 */
	public function __construct( $type = 'nav-menu-section', $domain = 'grimlock' ) {
		parent::__construct( $type, $domain );

		// General tab
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_field'           ), 140 );
		remove_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_wpautoped_field' ), 150 );

		add_filter( "{$this->id_base}_general_panel_fields",    array( $this, 'add_menu_field'           ), 150 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Navigation Menu Section', 'grimlock' ),
			'icon' => array(
				'foreground'=> '#000000',
				'src' => 'menu-alt',
			),
			'category' => 'widgets',
			'keywords' => array( __( 'menu', 'grimlock' ), __( 'section', 'grimlock' ), __( 'navigation', 'grimlock' ) ),
			'supports' => array(
				'html'   => false,
				'align'  => array( 'wide', 'full' ),
				'anchor' => true,
			),
		);
	}

	/**
	 * Add a select to set the nav menu for the Section Component.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_menu_field( $fields ) {
		$nav_menus = get_terms( 'nav_menu', array( 'hide_empty' => false ) );
		$choices   = array(
			'' => esc_html__( '- Select -', 'grimlock' ),
		);

		foreach( $nav_menus as $term ) {
			$choices[ $term->slug ] = $term->name;
		}

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_menu_field_args", array(
			'name'  => 'menu',
			'label' => esc_html__( 'Select Menu', 'grimlock' ),
			'choices' => $choices,
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
			'name'  => 'layout',
			'label' => esc_html__( 'Layout', 'grimlock' ),
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
			'menu' => '',
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
		do_action( 'grimlock_nav_menu_section', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
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
			'menu' => $attributes['menu'],
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

		$attributes['menu'] = isset( $new_attributes['menu'] ) ? sanitize_text_field( $new_attributes['menu'] ) : '';

		return $attributes;
	}
}

return new Grimlock_Nav_Menu_Section_Block();
