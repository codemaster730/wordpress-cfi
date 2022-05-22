<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Divider_Block
 *
 * @author  themosaurus
 * @since   1.3.5
 * @package grimlock/inc
 */
class Grimlock_Divider_Block extends Grimlock_Base_Block {

	/**
	 * @var array Array of available shapes
	 */
	private $shapes = array();

	/**
	 * @var array Array of available icons
	 */
	private $icons = array();

	/**
	 * Setup class.
	 *
	 * @param string $type Block type
	 * @param string $domain Block domain
	 *
	 * @since 1.3.5
	 */
	public function __construct( $type = 'divider', $domain = 'grimlock' ) {
		parent::__construct( $type, $domain );

		// Shape panel fields
		add_filter( "{$this->id_base}_shape_panel_fields", array( $this, 'add_shape_field'                   ), 100, 1 );
		add_filter( "{$this->id_base}_shape_panel_fields", array( $this, 'add_shape_color_field'             ), 110, 1 );
		add_filter( "{$this->id_base}_shape_panel_fields", array( $this, 'add_flip_shape_horizontally_field' ), 120, 1 );
		add_filter( "{$this->id_base}_shape_panel_fields", array( $this, 'add_flip_shape_vertically_field'   ), 130, 1 );

		// Icon panel fields
		add_filter( "{$this->id_base}_icon_panel_fields", array( $this, 'add_use_image_icon_field' ), 100, 1 );
		add_filter( "{$this->id_base}_icon_panel_fields", array( $this, 'add_icon_field'           ), 110, 1 );
		add_filter( "{$this->id_base}_icon_panel_fields", array( $this, 'add_image_icon_field'     ), 120, 1 );
		add_filter( "{$this->id_base}_icon_panel_fields", array( $this, 'add_icon_size_field'      ), 130, 1 );
		add_filter( "{$this->id_base}_icon_panel_fields", array( $this, 'add_icon_color_field'     ), 140, 1 );
		add_filter( "{$this->id_base}_icon_panel_fields", array( $this, 'add_icon_alignment_field' ), 150, 1 );

		// Style panel fields
		add_filter( "{$this->id_base}_style_panel_fields", array( $this, 'add_height_field'           ), 100, 1 );
		add_filter( "{$this->id_base}_style_panel_fields", array( $this, 'add_margin_top_field'       ), 110, 1 );
		add_filter( "{$this->id_base}_style_panel_fields", array( $this, 'add_margin_bottom_field'    ), 120, 1 );
		add_filter( "{$this->id_base}_style_panel_fields", array( $this, 'add_background_color_field' ), 130, 1 );
		add_filter( "{$this->id_base}_style_panel_fields", array( $this, 'add_z_index_field'          ), 140, 1 );
		add_filter( "{$this->id_base}_style_panel_fields", array( $this, 'add_mobile_displayed_field' ), 150, 1 );
	}

	/**
	 * Get block args used for JS registering of the block
	 *
	 * @return array Array of block args
	 */
	public function get_block_js_args() {
		return array(
			'title'    => __( 'Grimlock Divider', 'grimlock' ),
			'icon'     => array(
				'foreground' => '#000000',
				'src'        => 'editor-insertmore',
			),
			'category' => 'design',
			'keywords' => array( __( 'divider', 'grimlock' ), __( 'separator', 'grimlock' ), __( 'spacer', 'grimlock' ) ),
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
			'shape' => esc_html__( 'Shape', 'grimlock' ),
			'icon'  => esc_html__( 'Icon', 'grimlock' ),
			'style' => esc_html__( 'Style', 'grimlock' ),
		);
	}

	/**
	 * Add a radio images field to set the shape of the divider
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_shape_field( $fields ) {
		$args = apply_filters( "{$this->id_base}_shape_field_args", array(
			'name'    => 'shape',
			'label'   => esc_html__( 'Shape', 'grimlock' ),
			'choices' => array(
				'angle'                              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-angle.svg',
				'angle-layered'                      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-angle-layered.svg',
				'angle-layered-2'                    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-angle-layered-2.svg',
				'angle-layered-3'                    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-angle-layered-3.svg',
				'triangle'                           => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-triangle.svg',
				'triangle-asymmetrical'              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-triangle-asymmetrical.svg',
				'triangle-inverse'                   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-triangle-inverse.svg',
				'corner'                             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-corner.svg',
				'corner-inverse'                     => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-corner-inverse.svg',
				'curve'                              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-curve.svg',
				'curve-inverse'                      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-curve-inverse.svg',
				'curve-asymmetrical'                 => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-curve-asymmetrical.svg',
				'curve-layered-asymmetrical'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-curve-layered-asymmetrical.svg',
				'curve-layered-asymmetrical-inverse' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-curve-layered-asymmetrical-inverse.svg',
				'rounded'                            => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-rounded.svg',
				'rounded-asymmetrical'               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-rounded-asymmetrical.svg',
				'wave'                               => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-wave.svg',
				'wave-2'                             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-wave-2.svg',
				'wave-layered'                       => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-wave-layered.svg',
				'trapeze-rounded'                    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-trapeze-rounded.svg',
//				'arrow'                              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-arrow.svg',
				'drips'                              => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-drips.svg',
				''                                   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/shapes/shape-empty.svg',
			),
		) );

		$this->shapes = $args['choices'];

		$fields[] = $this->radio_image_field( $args );

		return $fields;
	}

	/**
	 * Add a color picker to set the color of the divider shape
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_shape_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_shape_color_field_args", array(
			'name'  => 'shape_color',
			'label' => esc_html__( 'Shape Color', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether the divider shape should be horizontally flipped
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_flip_shape_horizontally_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_flip_shape_horizontally_field_args", array(
			'name'  => 'flip_shape_horizontally',
			'label' => esc_html__( 'Flip shape horizontally', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether the divider shape should be vertically flipped
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_flip_shape_vertically_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_flip_shape_vertically_field_args", array(
			'name'  => 'flip_shape_vertically',
			'label' => esc_html__( 'Flip shape vertically', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a toggle field to set whether to use a custom image as the divider icon
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_use_image_icon_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_use_image_icon_field_args", array(
			'name'  => 'use_image_icon',
			'label' => esc_html__( 'Upload an image to use as the divider icon', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a radio images field to set the icon of the divider
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_icon_field( $fields ) {
		$args = apply_filters( "{$this->id_base}_icon_field_args", array(
			'name'    => 'icon',
			'label'   => esc_html__( 'Icon', 'grimlock' ),
			'choices' => array(
				''                      => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-empty.svg',
				'icon-star'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-star.svg',
				'icon-disc'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-disc.svg',
				'icon-disc-outline'     => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-disc-outline.svg',
				'icon-square'           => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-square.svg',
				'icon-square-outline'   => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-square-outline.svg',
				'icon-triangle'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-triangle.svg',
				'icon-triangle-outline' => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-triangle-outline.svg',
				'icon-diamond'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-diamond.svg',
				'icon-diamond-outline'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-diamond-outline.svg',
				'icon-hexagon'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-hexagon.svg',
				'icon-hexagon-outline'  => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-hexagon-outline.svg',
				'icon-line'             => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-line.svg',
				'icon-line-vertical'    => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-line-vertical.svg',
				'icon-asterisk'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-asterisk.svg',
				'icon-asterisk-2'       => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-asterisk-2.svg',
				'icon-bubbles'          => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-bubbles.svg',
				'icon-sparkles'         => GRIMLOCK_PLUGIN_DIR_URL . 'assets/images/icons/icon-sparkles.svg',
			),
			'conditional_logic' => array(
				array(
					'field'    => 'use_image_icon',
					'operator' => '==',
					'value'    => false,
				),
			),
		) );

		$this->icons = $args['choices'];

		$fields[] = $this->radio_image_field( $args );

		return $fields;
	}

	/**
	 * Add an image field to set the icon to use in the divider
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_image_icon_field( $fields ) {
		$fields[] = $this->image_field( apply_filters( "{$this->id_base}_image_icon_field_args", array(
			'name'  => 'image_icon',
			'label' => esc_html__( 'Icon', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'use_image_icon',
					'operator' => '==',
					'value'    => true,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the size of the icon in the divider
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_icon_size_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_icon_size_field_args", array(
			'name'  => 'icon_size',
			'min'   => 1,
			'max'   => 200,
			'unit'  => 'px',
			'label' => esc_html__( 'Icon size', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a color picker to set the color of the divider icon
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_icon_color_field( $fields ) {
		$fields[] = $this->color_field( apply_filters( "{$this->id_base}_icon_color_field_args", array(
			'name'  => 'icon_color',
			'label' => esc_html__( 'Icon Color', 'grimlock' ),
			'conditional_logic' => array(
				array(
					'field'    => 'use_image_icon',
					'operator' => '==',
					'value'    => false,
				),
			),
		) ) );

		return $fields;
	}

	/**
	 * Add an alignment matrix field to set the alignment of the icon in the divider
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_icon_alignment_field( $fields ) {
		$fields[] = $this->alignment_matrix_field( apply_filters( "{$this->id_base}_icon_alignment_field_args", array(
			'name'  => 'icon_alignment',
			'label' => esc_html__( 'Icon alignment', 'grimlock' ),
		) ) );

		return $fields;
	}

	/**
	 * Add a range field to set the height of the divider
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_height_field( $fields ) {
		$fields[] = $this->range_field( apply_filters( "{$this->id_base}_height_field_args", array(
			'name'  => 'height',
			'label' => esc_html__( 'Height', 'grimlock' ),
			'min'   => 10,
			'max'   => 500,
			'unit'  => 'px',
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
			'min'   => -500,
			'max'   => 500,
			'unit'  => 'px',
			'step'  => 1,
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
			'min'   => -500,
			'max'   => 500,
			'unit'  => 'px',
			'step'  => 1,
			'label' => esc_html__( 'Bottom Margin', 'grimlock' ),
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
	 * Add a toggle field to set whether to display the divider on mobile
	 *
	 * @param array $fields Array of block fields
	 *
	 * @return array Modified array of block fields
	 */
	public function add_mobile_displayed_field( $fields ) {
		$fields[] = $this->toggle_field( apply_filters( "{$this->id_base}_mobile_displayed_field_args", array(
			'name'  => 'mobile_displayed',
			'label' => esc_html__( 'Display divider on mobile', 'grimlock' ),
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
			'shape'                   => '',
			'shape_color'             => '#000000',
			'flip_shape_horizontally' => false,
			'flip_shape_vertically'   => false,

			'use_image_icon' => false,
			'icon'           => '',
			'image_icon'     => 0,
			'icon_size'      => 20,
			'icon_color'     => '#000000',
			'icon_alignment' => 'center center',

			'height'           => 150,
			'margin_top'       => 0,
			'margin_bottom'    => 0,
			'background_color' => '',
			'z_index'          => 0,
			'mobile_displayed' => true,
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
		do_action( 'grimlock_divider', apply_filters( "{$this->id_base}_component_args", $this->get_component_args( $attributes ), $attributes ) );
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
			'id'    => $attributes['anchor'],
			'class' => $this->get_classes( $attributes ),

			'shape'                   => $this->url_to_path( $this->shapes[ $attributes['shape'] ] ),
			'shape_color'             => $attributes['shape_color'],
			'flip_shape_horizontally' => $attributes['flip_shape_horizontally'],
			'flip_shape_vertically'   => $attributes['flip_shape_vertically'],

			'icon'           => empty( $attributes['use_image_icon'] ) ? $this->url_to_path( $this->icons[ $attributes['icon'] ] ) : '',
			'image_icon'     => ! empty( $attributes['use_image_icon'] ) ? $this->get_image_icon_url( $attributes ) : '',
			'icon_size'      => $attributes['icon_size'],
			'icon_color'     => $attributes['icon_color'],
			'icon_alignment' => $attributes['icon_alignment'],

			'height'           => $attributes['height'],
			'margin_top'       => $attributes['margin_top'],
			'margin_bottom'    => $attributes['margin_bottom'],
			'background_color' => $attributes['background_color'],
			'mobile_displayed' => $attributes['mobile_displayed'],

			'styles' => array(
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

		$attributes['className'] = isset( $new_attributes['className'] ) ? sanitize_html_class( $new_attributes['className'] ) : '';
		$attributes['anchor']    = isset( $new_attributes['anchor'] ) ? esc_attr( $new_attributes['anchor'] ) : '';

		$attributes['shape']                   = isset( $this->shapes[ $new_attributes['shape'] ] ) ? sanitize_text_field( $new_attributes['shape'] ) : '';
		$attributes['shape_color']             = isset( $new_attributes['shape_color'] ) ? sanitize_text_field( $new_attributes['shape_color'] ) : '';
		$attributes['flip_shape_horizontally'] = isset( $new_attributes['flip_shape_horizontally'] ) && filter_var( $new_attributes['flip_shape_horizontally'], FILTER_VALIDATE_BOOLEAN );
		$attributes['flip_shape_vertically']   = isset( $new_attributes['flip_shape_vertically'] ) && filter_var( $new_attributes['flip_shape_vertically'], FILTER_VALIDATE_BOOLEAN );

		$attributes['use_image_icon']          = isset( $new_attributes['use_image_icon'] ) && filter_var( $new_attributes['use_image_icon'], FILTER_VALIDATE_BOOLEAN );
		$attributes['icon']                    = isset( $this->icons[ $new_attributes['icon'] ] ) ? sanitize_text_field( $new_attributes['icon'] ) : '';
		$attributes['image_icon']              = isset( $new_attributes['image_icon'] ) ? intval( $new_attributes['image_icon'] ) : 0;
		$attributes['icon_size']               = isset( $new_attributes['icon_size'] ) ? floatval( $new_attributes['icon_size'] ) : 0;
		$attributes['icon_color']              = isset( $new_attributes['icon_color'] ) ? sanitize_text_field( $new_attributes['icon_color'] ) : '';
		$attributes['icon_alignment']          = isset( $new_attributes['icon_alignment'] ) ? sanitize_text_field( $new_attributes['icon_alignment'] ) : '';

		$attributes['height']                  = isset( $new_attributes['height'] ) ? floatval( $new_attributes['height'] ) : 0;
		$attributes['margin_top']              = isset( $new_attributes['margin_top'] ) ? floatval( $new_attributes['margin_top'] ) : 0;
		$attributes['margin_bottom']           = isset( $new_attributes['margin_bottom'] ) ? floatval( $new_attributes['margin_bottom'] ) : 0;
		$attributes['background_color']        = isset( $new_attributes['background_color'] ) ? sanitize_text_field( $new_attributes['background_color'] ) : '';
		$attributes['z_index']                 = isset( $new_attributes['z_index'] ) ? intval( $new_attributes['z_index'] ) : 0;
		$attributes['mobile_displayed']        = isset( $new_attributes['mobile_displayed'] ) && filter_var( $new_attributes['mobile_displayed'], FILTER_VALIDATE_BOOLEAN );

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
		$classes   = array( $attributes['className'] );
		$classes[] = "wp-block-{$this->domain}-{$this->type}";

		if ( ! empty( $attributes['align'] ) ) {
			$classes[] = "align{$attributes['align']}";
		}

		return $classes;
	}

	/**
	 * Get the image icon url for the divider
	 *
	 * @param array $attributes Settings for the current block instance.
	 *
	 * @return string The image icon url
	 */
	protected function get_image_icon_url( $attributes ) {
		$image_icon_url = '';

		if ( ! empty( $attributes['image_icon'] ) ) {
			$image_icon_url = wp_get_attachment_image_url( $attributes['image_icon'], 'full' );
		}

		return $image_icon_url;
	}

	/**
	 * Convert a url into an absolute path
	 *
	 * @param string $url Url to convert
	 *
	 * @return string|bool Absolute path or false if failed
	 */
	protected function url_to_path( $url ) {
		$site_url      = get_site_url();
		$relative_path = str_replace( $site_url, '', $url );
		$home_path     = ABSPATH;
		$full_path     = trailingslashit( $home_path ) . ltrim( $relative_path, '/' );

		if ( ! file_exists( $full_path ) ) {
			return false;
		}

		return $full_path;
	}
}

return new Grimlock_Divider_Block();
