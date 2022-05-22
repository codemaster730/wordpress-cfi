<?php
/**
 * Grimlock_Animate_Author_Avatars_Section_Block Class
 *
 * @author  Themosaurus
 * @package  grimlock-animate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends the Author Avatars Section bloc to add animation options
 */
class Grimlock_Animate_Author_Avatars_Section_Block {
	/**
	 * @var string Block base id. Used to generate hook names.
	 */
	protected $id_base;

	/**
	 * Grimlock_Animate_Author_Avatars_Section_Block constructor.
	 *
	 * @param string $id_base ID of the extended block
	 */
	public function __construct( $id_base = 'grimlock_author_avatars_author_avatars_section_block' ) {
		$this->id_base = $id_base;

		add_filter( "{$this->id_base}_panels", array( $this, 'change_panels' ), 10, 1 );

		// Animation tab
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_members_reveal_field'  ), 100 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_reveal_reset_field'    ), 110 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_reveal_mobile_field'   ), 120 );

		add_filter( "{$this->id_base}_defaults",       array( $this, 'change_defaults'       ), 10    );
		add_filter( "{$this->id_base}_component_args", array( $this, 'change_component_args' ), 10, 2 );
	}

	/**
	 * Change block panels
	 *
	 * @param array $panels The array of block panels
	 *
	 * @return array Modified array of block panels
	 */
	public function change_panels( $panels ) {
		$panels['animation'] = esc_html__( 'Animation', 'grimlock-animate' );
		return $panels;
	}

	/**
	 * Add a select to set the type of reveal used on the posts of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_members_reveal_field( $fields ) {
		$fields[] = Grimlock_Base_Block::select_field( apply_filters( "{$this->id_base}_members_reveal_field_args", array(
			'name'    => 'members_reveal' ,
			'label'   => esc_html__( 'Members reveal', 'grimlock-animate' ),
			'choices' => array(
				'none'   => esc_html__( 'None', 'grimlock-animate' ),
				'bottom' => esc_html__( 'Bottom', 'grimlock-animate' ),
				'top'    => esc_html__( 'Top', 'grimlock-animate' ),
				'left'   => esc_html__( 'Left', 'grimlock-animate' ),
				'right'  => esc_html__( 'Right', 'grimlock-animate' ),
				'fade'   => esc_html__( 'Fade', 'grimlock-animate' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether to reset the reveal animation when the element goes out of screen
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_reveal_reset_field( $fields ) {
		$fields[] = Grimlock_Base_Block::toggle_field( apply_filters( "{$this->id_base}_reveal_reset_field_args", array(
			'name'  => 'reveal_reset',
			'label' => esc_html__( 'Play reveal animations every time the element becomes visible', 'grimlock-animate' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether to reset the reveal animation plays on mobile devices
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_reveal_mobile_field( $fields ) {
		$fields[] = Grimlock_Base_Block::toggle_field( apply_filters( "{$this->id_base}_reveal_mobile_field_args", array(
			'name'  => 'reveal_mobile',
			'label' => esc_html__( 'Play reveal animations on mobile', 'grimlock-animate' ),
		) ) );

		return $fields;
	}

	/**
	 * Change the default settings for the block
	 *
	 * @param array $defaults The default settings for the block.
	 *
	 * @return array The updated default settings for the block.
	 */
	public function change_defaults( $defaults ) {
		return array_merge( $defaults, array(
			'members_reveal'  => 'none',
			'reveal_reset'    => false,
			'reveal_mobile'   => false,
		) );
	}

	/**
	 * Change the arguments sent to the component in charge of rendering the block
	 *
	 * @param array $component_args The arguments for the component in charge of rendering the block
	 * @param array $attributes Block attributes
	 *
	 * @return array The updated arguments for the component in charge of rendering the block
	 */
	public function change_component_args( $component_args, $attributes ) {
		$attributes = $this->sanitize_attributes( $attributes );

		return array_merge( $component_args, array(
			'members_reveal'  => $attributes['members_reveal'],
			'reveal_reset'    => $attributes['reveal_reset'],
			'reveal_mobile'   => $attributes['reveal_mobile'],
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
		$attributes = $new_attributes;

		$attributes['members_reveal']  = isset( $new_attributes['members_reveal'] ) ? sanitize_text_field( $new_attributes['members_reveal'] ) : '';
		$attributes['reveal_reset']    = filter_var( $new_attributes['reveal_reset'], FILTER_VALIDATE_BOOLEAN );
		$attributes['reveal_mobile']   = filter_var( $new_attributes['reveal_mobile'], FILTER_VALIDATE_BOOLEAN );

		return $attributes;
	}
}

return new Grimlock_Animate_Author_Avatars_Section_Block();
