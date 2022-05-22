<?php
/**
 * Grimlock_Animate_Section_Block Class
 *
 * @author  Themosaurus
 * @package  grimlock-animate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class that extends the Section bloc to add animation options
 */
class Grimlock_Animate_Section_Block {
	/**
	 * @var string Block base id. Used to generate hook names.
	 */
	protected $id_base;

	/**
	 * Grimlock_Animate_Section_Block constructor.
	 *
	 * @param string $id_base ID of the extended block
	 */
	public function __construct( $id_base = 'grimlock_section_block' ) {
		$this->id_base = $id_base;

		add_filter( "{$this->id_base}_panels", array( $this, 'change_panels' ), 10, 1 );

		// Animation tab
//		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_background_parallax_field' ), 100 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_thumbnail_parallax_field'  ), 110 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_content_parallax_field'    ), 120 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_parallax_speed_field'      ), 130 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_thumbnail_reveal_field'    ), 140 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_content_reveal_field'      ), 150 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_reveal_reset_field'        ), 160 );
		add_filter( "{$this->id_base}_animation_panel_fields", array( $this, 'add_reveal_mobile_field'       ), 170 );

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
	 * Add a select to set type of parallax used on the background of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_background_parallax_field( $fields ) {
		$fields[] = Grimlock_Base_Block::select_field( apply_filters( "{$this->id_base}_background_parallax_field_args", array(
			'name'    => 'background_parallax',
			'label'   => esc_html__( 'Background parallax', 'grimlock-animate' ),
			'choices' => array(
				'none'     => esc_html__( 'None', 'grimlock-animate' ),
				'natural'  => esc_html__( 'Natural', 'grimlock-animate' ),
				'inverted' => esc_html__( 'Inverted', 'grimlock-animate' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set type of parallax used on the thumbnail of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_thumbnail_parallax_field( $fields ) {
		$fields[] = Grimlock_Base_Block::select_field( apply_filters( "{$this->id_base}_thumbnail_parallax_field_args", array(
			'name'    => 'thumbnail_parallax',
			'label'   => esc_html__( 'Thumbnail parallax', 'grimlock-animate' ),
			'choices' => array(
				'none'     => esc_html__( 'None', 'grimlock-animate' ),
				'natural'  => esc_html__( 'Natural', 'grimlock-animate' ),
				'inverted' => esc_html__( 'Inverted', 'grimlock-animate' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set type of parallax used on the content of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_content_parallax_field( $fields ) {
		$fields[] = Grimlock_Base_Block::select_field( apply_filters( "{$this->id_base}_content_parallax_field_args", array(
			'name'    => 'content_parallax' ,
			'label'   => esc_html__( 'Content parallax:', 'grimlock-animate' ),
			'choices' => array(
				'none'     => esc_html__( 'None', 'grimlock-animate' ),
				'natural'  => esc_html__( 'Natural', 'grimlock-animate' ),
				'inverted' => esc_html__( 'Inverted', 'grimlock-animate' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the top margin of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_parallax_speed_field( $fields ) {
		$fields[] = Grimlock_Base_Block::range_field( apply_filters( "{$this->id_base}_parallax_speed_field_args", array(
			'name'  => 'parallax_speed',
			'min'   => 0,
			'max'   => 0.9,
			'step'  => 0.1,
			'label' => esc_html__( 'Parallax Speed', 'grimlock-animate' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the type of reveal used on the thumbnail of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_thumbnail_reveal_field( $fields ) {
		$fields[] = Grimlock_Base_Block::select_field( apply_filters( "{$this->id_base}_thumbnail_reveal_field_args", array(
			'name'    => 'thumbnail_reveal' ,
			'label'   => esc_html__( 'Thumbnail reveal:', 'grimlock-animate' ),
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
	 * Add a select to set the type of reveal used on the content of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_content_reveal_field( $fields ) {
		$fields[] = Grimlock_Base_Block::select_field( apply_filters( "{$this->id_base}_content_reveal_field_args", array(
			'name'    => 'content_reveal' ,
			'label'   => esc_html__( 'Content reveal:', 'grimlock-animate' ),
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
			'background_parallax' => 'none',
			'thumbnail_parallax'  => 'none',
			'content_parallax'    => 'none',
			'parallax_speed'      => 0.2,
			'thumbnail_reveal'    => 'none',
			'content_reveal'      => 'none',
			'reveal_reset'        => false,
			'reveal_mobile'       => false,
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
			'background_parallax' => $attributes['background_parallax'],
			'thumbnail_parallax'  => $attributes['thumbnail_parallax'],
			'content_parallax'    => $attributes['content_parallax'],
			'parallax_speed'      => $attributes['parallax_speed'],
			'thumbnail_reveal'    => $attributes['thumbnail_reveal'],
			'content_reveal'      => $attributes['content_reveal'],
			'reveal_reset'        => $attributes['reveal_reset'],
			'reveal_mobile'       => $attributes['reveal_mobile'],
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

		$attributes['background_parallax'] = isset( $new_attributes['background_parallax'] ) ? sanitize_text_field( $new_attributes['background_parallax'] ) : '';
		$attributes['thumbnail_parallax']  = isset( $new_attributes['thumbnail_parallax'] ) ? sanitize_text_field( $new_attributes['thumbnail_parallax'] ) : '';
		$attributes['content_parallax']    = isset( $new_attributes['content_parallax'] ) ? sanitize_text_field( $new_attributes['content_parallax'] ) : '';
		$attributes['parallax_speed']      = isset( $new_attributes['parallax_speed'] ) ? sanitize_text_field( $new_attributes['parallax_speed'] ) : '';
		$attributes['thumbnail_reveal']    = isset( $new_attributes['thumbnail_reveal'] ) ? sanitize_text_field( $new_attributes['thumbnail_reveal'] ) : '';
		$attributes['content_reveal']      = isset( $new_attributes['content_reveal'] ) ? sanitize_text_field( $new_attributes['content_reveal'] ) : '';
		$attributes['reveal_reset']        = filter_var( $new_attributes['reveal_reset'], FILTER_VALIDATE_BOOLEAN );
		$attributes['reveal_mobile']       = filter_var( $new_attributes['reveal_mobile'], FILTER_VALIDATE_BOOLEAN );

		return $attributes;
	}
}

return new Grimlock_Animate_Section_Block();
