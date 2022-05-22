<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Section_Block
 *
 * @author  themosaurus
 * @since   1.3.5
 * @package grimlock/inc
 */
class Grimlock_Section_Block extends Grimlock_Base_Block {

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.3.5
	 */
	public function __construct( $type = 'section', $domain = 'grimlock' ) {
		parent::__construct( $type, $domain );

		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_thumbnail_field'                     ), 100, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_thumbnail_size_field'                ), 100, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_separator'                           ), 110, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_title_field'                         ), 120, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_subtitle_field'                      ), 130, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_field'                          ), 140, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_text_wpautoped_field'                ), 150, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_separator'                           ), 160, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_displayed_field'              ), 170, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_text_field'                   ), 180, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_link_field'                   ), 190, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_target_blank_field'           ), 200, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_separator'                           ), 210, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_extra_displayed_field'        ), 220, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_extra_text_field'             ), 230, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_extra_link_field'             ), 240, 1 );
		add_filter( "{$this->id_base}_general_panel_fields", array( $this, 'add_button_extra_target_blank_field'     ), 250, 1 );

		// Layout tab
		add_filter( "{$this->id_base}_layout_panel_fields",  array( $this, 'add_layout_field'                        ), 100, 1 );
		add_filter( "{$this->id_base}_layout_panel_fields",  array( $this, 'add_separator'                           ), 110, 1 );
		add_filter( "{$this->id_base}_layout_panel_fields",  array( $this, 'add_container_layout_field'              ), 120, 1 );

		// Style tab fields
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_background_image_field'              ), 100, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 110, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_margin_top_field'                    ), 120, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_margin_bottom_field'                 ), 130, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_padding_top_field'                   ), 140, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_padding_bottom_field'                ), 145, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 150, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_background_color_field'              ), 160, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_background_gradient_displayed_field' ), 170, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_background_gradient_field'           ), 180, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_content_background_color_field'      ), 185, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 190, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_borders_displayed_field'             ), 200, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_border_top_width_field'              ), 210, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_border_top_color_field'              ), 220, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_border_bottom_width_field'           ), 230, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_border_bottom_color_field'           ), 240, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 250, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_title_format_field'                  ), 260, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_title_color_field'                   ), 270, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 275, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_subtitle_format_field'               ), 280, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_subtitle_color_field'                ), 290, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_color_field'                         ), 300, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 310, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_button_format_field'                 ), 320, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_button_size_field'                   ), 330, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 340, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_button_extra_format_field'           ), 350, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_button_extra_size_field'             ), 360, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 370, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_forms_color_scheme_field'            ), 380, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_separator'                           ), 390, 1 );
		add_filter( "{$this->id_base}_style_panel_fields",   array( $this, 'add_z_index_field'                       ), 400, 1 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Section', 'grimlock' ),
			'icon'     => array(
				'foreground' => '#000000',
				'src'        => 'align-left',
			),
			'category' => 'widgets',
			'keywords' => array( __( 'section', 'grimlock' ) ),
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
			'layout'  => esc_html__( 'Layout', 'grimlock' ),
			'style'   => esc_html__( 'Style', 'grimlock' ),
		);
	}

	/**
	 * Add an image field to set the featured image of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_thumbnail_field( $fields ) {
		$fields[] = $this->image_field( apply_filters( "{$this->id_base}_thumbnail_field_args", array(
			'name'  => 'thumbnail',
			'label' => esc_html__( 'Thumbnail', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a select field to set the featured image size of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_thumbnail_size_field( $fields ) {
		// Get named image sizes
		$choices = apply_filters(
			'image_size_names_choose',
			array(
				''          => esc_html__( 'Default', 'grimlock' ),
				'full'      => __( 'Full Size' ),
				'thumbnail' => __( 'Thumbnail' ),
				'medium'    => __( 'Medium' ),
				'large'     => __( 'Large' ),
			)
		);

		// Add non-named image sizes
		$image_sizes = get_intermediate_image_sizes();
		foreach ( $image_sizes as $image_size ) {
			if ( isset( $choices[ $image_size ] ) ) {
				continue;
			}

			$label = str_replace( '-', ' ', $image_size );
			$label = ucwords( $label );
			$choices[ $image_size ] = $label;
		}

		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_thumbnail_size_field_args", array(
			'name'  => 'thumbnail_size',
			'label' => esc_html__( 'Thumbnail size', 'grimlock' ),
			'choices' => $choices,
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set the title of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_title_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_title_field_args", array(
			'name'  => 'title',
			'label' => esc_html__( 'Title', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set the subtitle of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_subtitle_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_subtitle_field_args", array(
			'name'  => 'subtitle',
			'label' => esc_html__( 'Subtitle', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a text area to set the text of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_text_field( $fields ) {
		$fields[] = $this->textarea_field( apply_filters( "{$this->id_base}_text_field_args", array(
			'name'  => 'text',
			'label' => esc_html__( 'Text', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether new paragraphs are automatically added in the text of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_text_wpautoped_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_text_wpautoped_field_args", array(
			'name'  => 'text_wpautoped',
			'label' => esc_html__( 'Automatically add paragraphs', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether the button is displayed in the widget
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_button_displayed_field_args", array(
			'name'  => 'button_displayed',
			'label' => esc_html__( 'Display button', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set the text of the button in the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_text_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_button_text_field_args", array(
			'name'              => 'button_text',
			'label'             => esc_html__( 'Button Text', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set the link of the button in the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_link_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_button_link_field_args", array(
			'name'              => 'button_link',
			'label'             => esc_html__( 'Button Link', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox to set whether the button link should open in a new page
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_target_blank_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_button_target_blank_field_args", array(
			'name'              => 'button_target_blank',
			'label'             => esc_html__( 'Open link in a new page', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether the extra button is displayed in the widget
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_extra_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_button_extra_displayed_field_args", array(
			'name'  => 'button_extra_displayed',
			'label' => esc_html__( 'Display extra button', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set the text of the extra button in the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_extra_text_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_button_extra_text_field_args", array(
			'name'              => 'button_extra_text',
			'label'             => esc_html__( 'Extra button Text', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'button_extra_displayed',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a text field to set the link of the extra button in the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_extra_link_field( $fields ) {
		$fields[] = $this->text_field( apply_filters( "{$this->id_base}_button_extra_link_field_args", array(
			'name'              => 'button_extra_link',
			'label'             => esc_html__( 'Extra button Link', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'button_extra_displayed',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a checkbox to set whether the extra button link should open in a new page
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_extra_target_blank_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_button_extra_target_blank_field_args", array(
			'name'              => 'button_extra_target_blank',
			'label'             => esc_html__( 'Open extra link in a new page', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'button_extra_displayed',
					'operator' => '==',
					'value'    => true,
				),
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
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
			'label'   => esc_html__( 'Layout', 'grimlock' ),
			'choices' => array(
				'12-cols-left'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-left.png',
				'12-cols-center'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-center.png',
				'12-cols-right'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-right.png',
				'6-6-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left.png',
				'6-6-cols-left-reverse'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-reverse.png',
				'4-8-cols-left'                => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-4-8-cols-left.png',
				'4-8-cols-left-reverse'        => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-4-8-cols-left-reverse.png',
				'6-6-cols-left-modern'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-modern.png',
				'6-6-cols-left-reverse-modern' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-reverse-modern.png',
				'8-4-cols-left-modern'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-8-4-cols-left-modern.png',
				'8-4-cols-left-reverse-modern' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-8-4-cols-left-reverse-modern.png',
				'6-6-cols-left-boxed'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-boxed.png',
				'6-6-cols-left-boxed-reverse'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-6-6-cols-left-boxed-reverse.png',
				'12-cols-left-boxed'           => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-left-boxed.png',
				'12-cols-left-boxed-reverse'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-left-boxed-reverse.png',
				'12-cols-center-boxed'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-center-boxed.png',
				'12-cols-left-content-inline'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/section-12-cols-left-content-inline.png',
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a radio image field to set the spread of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_container_layout_field( $fields ) {
		$fields[] = $this->radio_image_field( apply_filters( "{$this->id_base}_container_layout_field_args", array(
			'name'    => 'container_layout',
			'label'   => esc_html__( 'Spread', 'grimlock' ),
			'choices' => array(
				'classic'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-classic.png',
				'fluid'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-fluid.png',
				'narrow'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrow.png',
				'narrower' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/region-container-narrower.png',
			),
		) ) );

		return $fields;
	}

	/**
	 * Add an image field to set the background image of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_background_image_field( $fields ) {
		$fields[] = $this->image_field( apply_filters( "{$this->id_base}_background_image_field_args", array(
			'name'  => 'background_image',
			'label' => esc_html__( 'Background Image', 'grimlock' ),
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
	public function add_margin_top_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_margin_top_field_args", array(
			'name'  => 'margin_top',
			'min'   => -25,
			'max'   => 25,
			'unit'  => '%',
			'label' => esc_html__( 'Top Margin', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the bottom margin of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_margin_bottom_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_margin_bottom_field_args", array(
			'name'  => 'margin_bottom',
			'min'   => -25,
			'max'   => 25,
			'unit'  => '%',
			'label' => esc_html__( 'Bottom Margin', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the top padding of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_padding_top_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_padding_top_field_args", array(
			'name'     => 'padding_top',
			'old_name' => 'padding_y',
			'min'      => 0,
			'max'      => 25,
			'unit'     => '%',
			'label'    => esc_html__( 'Top Padding', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the bottom padding of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_padding_bottom_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_padding_bottom_field_args", array(
			'name'     => 'padding_bottom',
			'old_name' => 'padding_y',
			'min'      => 0,
			'max'      => 25,
			'unit'     => '%',
			'label'    => esc_html__( 'Bottom Padding', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the background color of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_background_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_background_color_field_args", array(
			'name'  => 'background_color',
			'label' => esc_html__( 'Background Color', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether to add a gradient to the background
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_background_gradient_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_background_gradient_displayed_field_args", array(
			'name'  => 'background_gradient_displayed',
			'label' => esc_html__( 'Add Gradient to Background Color', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether to add a gradient to the background
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_background_gradient_field( $fields ) {
		$fields[] = $this->gradient_field( apply_filters( "{$this->id_base}_background_gradient_field_args", array(
			'name'  => 'background_gradient',
			'label' => esc_html__( 'Background Gradient', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'background_gradient_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the background color of content box for "boxed" layouts
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_content_background_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_content_background_color_field_args", array(
			'name'  => 'content_background_color',
			'label' => esc_html__( 'Content Background Color', 'grimlock' ),
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'layout',
						'operator' => '===',
						'value'    => '6-6-cols-left-boxed',
					),
					array(
						'field'    => 'layout',
						'operator' => '===',
						'value'    => '6-6-cols-left-boxed-reverse',
					),
					array(
						'field'    => 'layout',
						'operator' => '===',
						'value'    => '12-cols-left-boxed',
					),
					array(
						'field'    => 'layout',
						'operator' => '===',
						'value'    => '12-cols-left-boxed-reverse',
					),
					array(
						'field'    => 'layout',
						'operator' => '===',
						'value'    => '12-cols-center-boxed',
					),
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle to set whether to add a top and/or a bottom border to the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_borders_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_borders_displayed_field_args", array(
			'name'  => 'borders_displayed',
			'label' => esc_html__( 'Add borders', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the border top width of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_border_top_width_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_border_top_width_field_args", array(
			'name'  => 'border_top_width',
			'min'   => 0,
			'max'   => 25,
			'unit'  => 'px',
			'step'  => 1,
			'label' => esc_html__( 'Border Top Width', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'borders_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the top border color of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_border_top_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_border_top_color_field_args", array(
			'name'  => 'border_top_color',
			'label' => esc_html__( 'Border Top Color', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'borders_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the border bottom width of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_border_bottom_width_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_border_bottom_width_field_args", array(
			'name'  => 'border_bottom_width',
			'min'   => 0,
			'max'   => 25,
			'unit'  => 'px',
			'step'  => 1,
			'label' => esc_html__( 'Border Bottom Width', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'borders_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the bottom border color of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_border_bottom_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_border_bottom_color_field_args", array(
			'name'  => 'border_bottom_color',
			'label' => esc_html__( 'Border Bottom Color', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'borders_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the title format for the Section Component.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_title_format_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_title_format_field_args", array(
			'name'  => 'title_format',
			'label' => esc_html__( 'Title Format', 'grimlock' ),
			'choices' => array(
				'display-1' => esc_html__( 'Heading 1', 'grimlock' ),
				'display-2' => esc_html__( 'Heading 2', 'grimlock' ),
				'display-3' => esc_html__( 'Heading 3', 'grimlock' ),
				'display-4' => esc_html__( 'Heading 4', 'grimlock' ),
				'lead'      => esc_html__( 'Subheading', 'grimlock' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the title color of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_title_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_title_color_field_args", array(
			'name'  => 'title_color',
			'label' => esc_html__( 'Title Color', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the subtitle format for the Section Component.
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_subtitle_format_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_subtitle_format_field_args", array(
			'name'  => 'subtitle_format',
			'label' => esc_html__( 'Subtitle Format', 'grimlock' ),
			'choices' => array(
				'display-1' => esc_html__( 'Heading 1', 'grimlock' ),
				'display-2' => esc_html__( 'Heading 2', 'grimlock' ),
				'display-3' => esc_html__( 'Heading 3', 'grimlock' ),
				'display-4' => esc_html__( 'Heading 4', 'grimlock' ),
				'lead'      => esc_html__( 'Subheading', 'grimlock' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the subtitle color of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_subtitle_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_subtitle_color_field_args", array(
			'name'  => 'subtitle_color',
			'label' => esc_html__( 'Subtitle Color', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the text color of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_color_field_args", array(
			'name'  => 'color',
			'label' => esc_html__( 'Text Color', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the button format for the button of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_format_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_button_format_field_args", array(
			'name'              => 'button_format',
			'label'             => esc_html__( 'Button Format', 'grimlock' ),
			'choices'           => array(
				'btn-primary'           => esc_html__( 'Primary', 'grimlock' ),
				'btn-secondary'         => esc_html__( 'Secondary', 'grimlock' ),
				'btn-primary-outline'   => esc_html__( 'Primary Outline', 'grimlock' ),
				'btn-secondary-outline' => esc_html__( 'Secondary Outline', 'grimlock' ),
				'btn-primary-inverse'   => esc_html__( 'Primary Inverse', 'grimlock' ),
				'btn-secondary-inverse' => esc_html__( 'Secondary Inverse', 'grimlock' ),
				'btn-current-outline'   => esc_html__( 'Current Outline', 'grimlock' ),
				'btn-link'              => esc_html__( 'Link', 'grimlock' ),
			),
			'conditional_logic' => array(
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the button size for the button of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_size_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_button_size_field_args", array(
			'name'              => 'button_size',
			'label'             => esc_html__( 'Button Size', 'grimlock' ),
			'choices'           => array(
				'btn-sm'    => esc_html__( 'Small',      'grimlock' ),
				''          => esc_html__( 'Regular',    'grimlock' ),
				'btn-lg'    => esc_html__( 'Large',      'grimlock' ),
				'btn-block' => esc_html__( 'Full Width', 'grimlock' ),
			),
			'conditional_logic' => array(
				array(
					'field'    => 'button_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the extra button format for the button of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_extra_format_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_button_extra_format_field_args", array(
			'name'              => 'button_extra_format',
			'label'             => esc_html__( 'Extra button format', 'grimlock' ),
			'choices'           => array(
				'btn-primary'           => esc_html__( 'Primary', 'grimlock' ),
				'btn-secondary'         => esc_html__( 'Secondary', 'grimlock' ),
				'btn-primary-outline'   => esc_html__( 'Primary Outline', 'grimlock' ),
				'btn-secondary-outline' => esc_html__( 'Secondary Outline', 'grimlock' ),
				'btn-primary-inverse'   => esc_html__( 'Primary Inverse', 'grimlock' ),
				'btn-secondary-inverse' => esc_html__( 'Secondary Inverse', 'grimlock' ),
				'btn-current-outline'   => esc_html__( 'Current Outline', 'grimlock' ),
				'btn-link'              => esc_html__( 'Link', 'grimlock' ),
			),
			'conditional_logic' => array(
				array(
					'field'    => 'button_extra_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the extra button size for the button of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_button_extra_size_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_button_extra_size_field_args", array(
			'name'              => 'button_extra_size',
			'label'             => esc_html__( 'Extra button size', 'grimlock' ),
			'choices'           => array(
				'btn-sm'    => esc_html__( 'Small',      'grimlock' ),
				''          => esc_html__( 'Regular',    'grimlock' ),
				'btn-lg'    => esc_html__( 'Large',      'grimlock' ),
				'btn-block' => esc_html__( 'Full Width', 'grimlock' ),
			),
			'conditional_logic' => array(
				array(
					'field'    => 'button_extra_displayed',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a select to set the button size for the button of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_forms_color_scheme_field( $fields ) {
		$fields[] = $this->select_field( apply_filters( "{$this->id_base}_forms_color_scheme_field_args", array(
			'name'    => 'forms_color_scheme',
			'label'   => esc_html__( 'Forms Color Scheme', 'grimlock' ),
			'choices' => array(
				''      => esc_html__( 'Default', 'grimlock' ),
				'light' => esc_html__( 'Light',   'grimlock' ),
				'dark'  => esc_html__( 'Dark',    'grimlock' ),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the z-index of the section
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_z_index_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_z_index_field_args", array(
			'name'  => 'z_index',
			'min'   => -100,
			'max'   => 100,
			'step'  => 1,
			'label' => esc_html__( 'Z-index', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a separator to space the fields
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function add_separator( $fields ) {
		$fields[] = $this->separator();

		return $fields;
	}

	/**
	 * Get default field values for the block
	 *
	 * @return array Array of default field values
	 */
	public function get_defaults() {
		return array(
			'margin_top'                       => 0, // %
			'margin_bottom'                    => 0, // %
			'padding_top'                      => GRIMLOCK_SECTION_PADDING_Y, // %
			'padding_bottom'                   => GRIMLOCK_SECTION_PADDING_Y, // %

			'background_image'                 => 0,
			'background_color'                 => GRIMLOCK_SECTION_BACKGROUND_COLOR,
			'background_gradient_displayed'    => false,
			'content_background_color'         => '',

			'thumbnail'                        => 0,
			'thumbnail_size'                   => '',

			'borders_displayed'                => false,
			'border_top_color'                 => GRIMLOCK_BORDER_COLOR,
			'border_top_width'                 => 0, // px
			'border_bottom_color'              => GRIMLOCK_BORDER_COLOR,
			'border_bottom_width'              => 0, // px

			'title'                            => esc_html__( 'Title goes here...', 'grimlock' ),
			'title_color'                      => '',
			'title_format'                     => 'display-3',

			'subtitle'                         => esc_html__( 'Subtitle goes here...', 'grimlock' ),
			'subtitle_color'                   => '',
			'subtitle_format'                  => 'lead',

			'text'                             => esc_html__( 'Text goes here...', 'grimlock' ),
			'color'                            => '',
			'text_wpautoped'                   => true,

			'button_displayed'                 => true,
			'button_text'                      => esc_html__( 'Learn more', 'grimlock' ),
			'button_link'                      => '#',
			'button_target_blank'              => false,
			'button_format'                    => 'btn-primary',
			'button_size'                      => '',

			'button_extra_displayed'           => false,
			'button_extra_text'                => esc_html__( 'View now', 'grimlock' ),
			'button_extra_link'                => '#',
			'button_extra_target_blank'        => false,
			'button_extra_format'              => 'btn-secondary',
			'button_extra_size'                => '',

			'forms_color_scheme'               => '',

			'layout'                           => '12-cols-center',
			'container_layout'                 => 'classic',
			'z_index'                          => 0,
		);
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
		do_action( 'grimlock_section', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
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
		return array(
			'id'                        => $attributes['anchor'],
			'class'                     => $this->get_classes( $attributes ),

			'margin_top'                => $attributes['margin_top'],
			'margin_bottom'             => $attributes['margin_bottom'],
			'padding_top'               => isset( $attributes['padding_y'] ) ? $attributes['padding_y'] : $attributes['padding_top'],
			'padding_bottom'            => isset( $attributes['padding_y'] ) ? $attributes['padding_y'] : $attributes['padding_bottom'],

			'background_image'          => $this->get_background_image_url( $attributes ),
			'background_color'          => $attributes['background_color'],
			'content_background_color'  => $attributes['content_background_color'],

			'thumbnail'                 => $this->get_thumbnail_url( $attributes ),
			'thumbnail_alt'             => $this->get_thumbnail_alt( $attributes ),
			'thumbnail_caption'         => $this->get_thumbnail_caption( $attributes ),

			'border_top_color'          => $attributes['border_top_color'],
			'border_top_width'          => ! empty( $attributes['borders_displayed'] ) ? $attributes['border_top_width'] : 0,
			'border_bottom_color'       => $attributes['border_bottom_color'],
			'border_bottom_width'       => ! empty( $attributes['borders_displayed'] ) ? $attributes['border_bottom_width'] : 0,

			'title'                     => $attributes['title'],
			'title_color'               => $attributes['title_color'],
			'title_format'              => $attributes['title_format'],
			'title_displayed'           => ! empty( $attributes['title'] ),

			'subtitle'                  => $attributes['subtitle'],
			'subtitle_color'            => $attributes['subtitle_color'],
			'subtitle_format'           => $attributes['subtitle_format'],
			'subtitle_displayed'        => ! empty( $attributes['subtitle'] ),

			'text'                      => $attributes['text'],
			'text_wpautoped'            => $attributes['text_wpautoped'],
			'color'                     => $attributes['color'],
			'text_displayed'            => ! empty( $attributes['text'] ),

			'button_displayed'          => $attributes['button_displayed'],
			'button_text'               => $attributes['button_text'],
			'button_link'               => $attributes['button_link'],
			'button_target_blank'       => $attributes['button_target_blank'],
			'button_format'             => $attributes['button_format'],
			'button_size'               => $attributes['button_size'],

			'button_extra_displayed'    => $attributes['button_extra_displayed'],
			'button_extra_text'         => $attributes['button_extra_text'],
			'button_extra_link'         => $attributes['button_extra_link'],
			'button_extra_target_blank' => $attributes['button_extra_target_blank'],
			'button_extra_format'       => $attributes['button_extra_format'],
			'button_extra_size'         => $attributes['button_extra_size'],

			'layout'                    => $attributes['layout'],
			'container_layout'          => $attributes['container_layout'],

			'inner_styles'              => ! empty( $attributes['background_gradient_displayed'] ) && ! empty( $attributes['background_gradient'] ) ? array( 'background' => $attributes['background_gradient'] ) : array(),
			'styles'                    => array(
				'z-index' => $attributes['z_index'],
			),
		);
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

		$classes = isset( $new_attributes['className'] ) ? explode( ' ', str_replace( ',', ' ', $new_attributes['className'] ) ) : array();
		for( $i = 0; $i < count( $classes ); $i++ ) {
			$classes[$i] = sanitize_html_class( $classes[$i] );
		}
		$attributes['className'] = implode( ' ', $classes );

		$attributes['anchor'] = isset( $new_attributes['anchor'] ) ? esc_attr( $new_attributes['anchor'] ) : '';

		if ( current_user_can( 'unfiltered_html' ) ) {
			$attributes['text'] = isset( $new_attributes['text'] ) ? $this->sanitize_text( $new_attributes['text'], true ) : '';
		} else {
			$attributes['text'] = isset( $new_attributes['text'] ) ? $this->sanitize_text( $new_attributes['text'] ) : '';
		}

		$attributes['margin_top']                = isset( $new_attributes['margin_top'] ) ? floatval( $new_attributes['margin_top'] ) : 0;
		$attributes['margin_bottom']             = isset( $new_attributes['margin_bottom'] ) ? floatval( $new_attributes['margin_bottom'] ) : 0;
		$attributes['padding_top']               = isset( $new_attributes['padding_top'] ) ? floatval( $new_attributes['padding_top'] ) : 0;
		$attributes['padding_bottom']            = isset( $new_attributes['padding_bottom'] ) ? floatval( $new_attributes['padding_bottom'] ) : 0;

		$attributes['background_image']          = isset( $new_attributes['background_image'] ) ? intval( $new_attributes['background_image'] ) : 0;
		$attributes['background_color']          = isset( $new_attributes['background_color'] ) ? sanitize_text_field( $new_attributes['background_color'] ) : '';
		$attributes['background_gradient']       = isset( $new_attributes['background_gradient'] ) ? sanitize_text_field( $new_attributes['background_gradient'] ) : '';
		$attributes['content_background_color']  = isset( $new_attributes['content_background_color'] ) ? sanitize_text_field( $new_attributes['content_background_color'] ) : '';

		$attributes['thumbnail']                 = isset( $new_attributes['thumbnail'] ) ? intval( $new_attributes['thumbnail'] ) : 0;
		$attributes['thumbnail_size']            = isset( $new_attributes['thumbnail_size'] ) ? sanitize_text_field( $new_attributes['thumbnail_size'] ) : '';

		$attributes['borders_displayed']         = isset( $new_attributes['borders_displayed'] ) && filter_var( $new_attributes['borders_displayed'], FILTER_VALIDATE_BOOLEAN );
		$attributes['border_top_color']          = isset( $new_attributes['border_top_color'] ) ? sanitize_text_field( $new_attributes['border_top_color'] ) : '';
		$attributes['border_top_width']          = isset( $new_attributes['border_top_width'] ) ? intval( $new_attributes['border_top_width'] ) : 0;
		$attributes['border_bottom_color']       = isset( $new_attributes['border_bottom_color'] ) ? sanitize_text_field( $new_attributes['border_bottom_color'] ) : '';
		$attributes['border_bottom_width']       = isset( $new_attributes['border_bottom_width'] ) ? intval( $new_attributes['border_bottom_width'] ) : 0;

		$attributes['title']                     = isset( $new_attributes['title'] ) ? $this->sanitize_text( $new_attributes['title'] ) : '';
		$attributes['title_color']               = isset( $new_attributes['title_color'] ) ? sanitize_text_field( $new_attributes['title_color'] ) : '';
		$attributes['title_format']              = isset( $new_attributes['title_format'] ) ? sanitize_text_field( $new_attributes['title_format'] ) : '';

		$attributes['subtitle']                  = isset( $new_attributes['subtitle'] ) ? $this->sanitize_text( $new_attributes['subtitle'] ) : '';
		$attributes['subtitle_format']           = isset( $new_attributes['subtitle_format'] ) ? sanitize_text_field( $new_attributes['subtitle_format'] ) : '';
		$attributes['subtitle_color']            = isset( $new_attributes['subtitle_color'] ) ? sanitize_text_field( $new_attributes['subtitle_color'] ) : '';

		$attributes['color']                     = isset( $new_attributes['color'] ) ? sanitize_text_field( $new_attributes['color'] ) : '';
		$attributes['text_wpautoped']            = isset( $new_attributes['text_wpautoped'] ) && filter_var( $new_attributes['text_wpautoped'], FILTER_VALIDATE_BOOLEAN );

		$attributes['button_text']               = isset( $new_attributes['button_text'] ) ? $this->sanitize_text( $new_attributes['button_text'] ) : '';
		$attributes['button_link']               = isset( $new_attributes['button_link'] ) ? esc_url( $new_attributes['button_link'] ) : '';
		$attributes['button_target_blank']       = isset( $new_attributes['button_target_blank'] ) && filter_var( $new_attributes['button_target_blank'], FILTER_VALIDATE_BOOLEAN );
		$attributes['button_displayed']          = isset( $new_attributes['button_displayed'] ) && filter_var( $new_attributes['button_displayed'], FILTER_VALIDATE_BOOLEAN );
		$attributes['button_format']             = isset( $new_attributes['button_format'] ) ? sanitize_text_field( $new_attributes['button_format'] ) : '';
		$attributes['button_size']               = isset( $new_attributes['button_size'] ) ? sanitize_text_field( $new_attributes['button_size'] ) : '';

		$attributes['button_extra_text']         = isset( $new_attributes['button_extra_text'] ) ? $this->sanitize_text( $new_attributes['button_extra_text'] ) : '';
		$attributes['button_extra_link']         = isset( $new_attributes['button_extra_link'] ) ? esc_url( $new_attributes['button_extra_link'] ) : '';
		$attributes['button_extra_target_blank'] = isset( $new_attributes['button_extra_target_blank'] ) && filter_var( $new_attributes['button_extra_target_blank'], FILTER_VALIDATE_BOOLEAN );
		$attributes['button_extra_displayed']    = isset( $new_attributes['button_extra_displayed'] ) && filter_var( $new_attributes['button_extra_displayed'], FILTER_VALIDATE_BOOLEAN );
		$attributes['button_extra_format']       = isset( $new_attributes['button_extra_format'] ) ? sanitize_text_field( $new_attributes['button_extra_format'] ) : '';
		$attributes['button_extra_size']         = isset( $new_attributes['button_extra_size'] ) ? sanitize_text_field( $new_attributes['button_extra_size'] ) : '';

		$attributes['forms_color_scheme']        = isset( $new_attributes['forms_color_scheme'] ) ? sanitize_text_field( $new_attributes['forms_color_scheme'] ) : '';

		$attributes['z_index']                   = isset( $new_attributes['z_index'] ) ? intval( $new_attributes['z_index'] ) : 0;

		$attributes['layout']                    = isset( $new_attributes['layout'] ) ? sanitize_text_field( $new_attributes['layout'] ) : '';
		$attributes['container_layout']          = isset( $new_attributes['container_layout'] ) ? sanitize_text_field( $new_attributes['container_layout'] ) : '';
		$attributes['align']                     = isset( $new_attributes['align'] ) ? sanitize_text_field( $new_attributes['align'] ) : '';

		return $attributes;
	}

	/**
	 * Filter HTML and encode emojis for database save
	 *
	 * @param $text
	 * @param bool $allow_unfiltered_html
	 *
	 * @return string
	 */
	protected function sanitize_text( $text, $allow_unfiltered_html = false ) {
		if ( ! empty( apply_filters( "{$this->id_base}_allow_unfiltered_html", $allow_unfiltered_html ) ) ) {
			return wp_encode_emoji( $text );
		}

		return wp_kses_post( wp_encode_emoji( $text ) );
	}

	/**
	 * Get the block classes
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return array The block classes
	 */
	protected function get_classes( $attributes ) {
		$classes   = array( $attributes['className'] );
		$classes[] = "wp-block-{$this->domain}-{$this->type}";
		$classes[] = "{$this->domain}-{$this->type}--{$attributes['button_format']}";
		$title     = ! empty( $attributes['title'] ) ? sanitize_title( $attributes['title'] ) : '';

		if ( '' !== $title ) {
			$classes[] = "{$this->domain}-{$this->type}--{$title}";
		}

		if ( ! empty( $attributes['align'] ) ) {
			$classes[] = "align{$attributes['align']}";
		}

		if ( ! empty( $attributes['forms_color_scheme'] ) ) {
			$classes[] = "{$this->domain}-{$this->type}--forms-{$attributes['forms_color_scheme']}";
		}

		return $classes;
	}

	/**
	 * Get the thumbnail url for the block
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return string The thumbnail url
	 */
	protected function get_thumbnail_url( $attributes ) {
		$thumbnail_url = '';

		if ( ! empty( $attributes['thumbnail'] ) ) {
			$thumbnail_url = wp_get_attachment_image_url( $attributes['thumbnail'], $this->get_thumbnail_size( $attributes ) );
		}

		return $thumbnail_url;
	}

	/**
	 * Get the thumbnail size for the block
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return string The thumbnail size
	 */
	protected function get_thumbnail_size( $attributes ) {
		return ! empty( $attributes['thumbnail_size'] ) ? $attributes['thumbnail_size'] : apply_filters( "{$this->id_base}_thumbnail_size", "thumbnail-{$attributes['layout']}", $attributes['layout'] );
	}

	/**
	 * Get the thumbnail alt for the block
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return string The thumbnail alt
	 */
	protected function get_thumbnail_alt( $attributes ) {
		$thumbnail_alt = '';

		if ( ! empty( $attributes['thumbnail'] ) ) {
			$thumbnail_alt = trim( strip_tags( get_post_meta( $attributes['thumbnail'], '_wp_attachment_image_alt', true ) ) );
		}
		return $thumbnail_alt;
	}

	/**
	 * Get the thumbnail caption for the block
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return string The thumbnail caption
	 */
	protected function get_thumbnail_caption( $attributes ) {
		$thumbnail_caption = '';

		if ( ! empty( $attributes['thumbnail'] ) ) {
			$thumbnail_caption = wp_get_attachment_caption( $attributes['thumbnail'] );
		}

		return $thumbnail_caption;
	}

	/**
	 * Get the background image url for the block
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return string The background image url
	 */
	protected function get_background_image_url( $attributes ) {
		$background_image_url = '';

		if ( ! empty( $attributes['background_image'] ) ) {
			$background_image_url = wp_get_attachment_image_url( $attributes['background_image'], apply_filters( "{$this->id_base}_background_image_size", 'custom-header', $attributes['layout'] ) );
		}

		return $background_image_url;
	}
}

return new Grimlock_Section_Block();
