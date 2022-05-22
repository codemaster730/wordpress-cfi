<?php
/**
 * Grimlock_Search_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the search pages.
 */
class Grimlock_Search_Customizer extends Grimlock_Grid_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'search';
		$this->section = 'grimlock_search_customizer_section';
		$this->title   = esc_html__( 'Search Results', 'grimlock' );

		add_action( 'wp_enqueue_scripts',                        array( $this, 'enqueue_scripts'                 ), 10    );
		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );
		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_posts_class',                      array( $this, 'add_posts_classes'               ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_search_post_args',                 array( $this, 'add_search_post_args'            ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',                array( $this, 'add_dynamic_css'                 ), 10, 1 );
		add_action( 'customize_controls_print_scripts',          array( $this, 'add_scripts'                     ), 30, 1 );
	}

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args = array() ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			$header_image    = get_header_image();
			$header_image_id = attachment_url_to_postid( $header_image );
			$size            = apply_filters( "grimlock_{$this->id}_customizer_custom_header_size", 'custom-header', $this->get_theme_mod( "{$this->id}_custom_header_layout" ) );

			$args['title']            = sprintf( esc_html__( 'Search Results for: %s', 'grimlock' ), '<span>' . get_search_query() . '</span>' );
			$args['background_image'] = ! empty( $header_image_id ) ? wp_get_attachment_image_url( $header_image_id, $size ) : $header_image;
		}
		return $args;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		return apply_filters( 'grimlock_search_customizer_is_template', is_search() );
	}

	/**
	 * Add arguments using theme mods to customize the post.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the post.
	 *
	 * @return array      The arguments to render the post.
	 */
	public function add_search_post_args( $args = array() ) {
		$args['post_thumbnail_displayed'] = $this->get_theme_mod( 'search_post_thumbnail_displayed' );
		$args['post_thumbnail_size']      = apply_filters( 'grimlock_search_customizer_post_thumbnail_size', 'large', $this->get_theme_mod( 'search_posts_layout' ), get_post_type() );
		$args['post_date_displayed']      = $this->get_theme_mod( 'search_post_date_displayed' );
		$args['post_author_displayed']    = $this->get_theme_mod( 'search_post_author_displayed' );
		$args['post_more_link_displayed'] = $this->get_theme_mod( 'search_post_more_link_displayed' );
		$args['category_displayed']       = $this->get_theme_mod( 'search_category_displayed' );
		$args['post_tag_displayed']       = $this->get_theme_mod( 'search_post_tag_displayed' );
		$args['post_format_displayed']    = $this->get_theme_mod( 'search_post_format_displayed' );
		$args['comments_link_displayed']  = $this->get_theme_mod( 'search_comments_link_displayed' );
		return $args;
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock' ),
				'class' => 'search-general-tab',
				'controls' => array(
					"{$this->section}_heading_10",
					'search_custom_header_displayed',
					"{$this->section}_divider_20",
					"{$this->section}_heading_20",
					'search_post_thumbnail_displayed',
					'search_post_date_displayed',
					'search_post_author_displayed',
					'search_post_more_link_displayed',
					'search_post_tag_displayed',
					'search_category_displayed',
					'search_post_format_displayed',
					'search_comments_link_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock' ),
				'class' => 'search-layout-tab',
				'controls' => array(
					'search_custom_header_layout',
					"{$this->section}_divider_110",
					'search_custom_header_container_layout',
					"{$this->section}_divider_120",
					'search_layout',
					'search_sidebar_mobile_displayed',
					"{$this->section}_divider_140",
					'search_container_layout',
					"{$this->section}_divider_150",
					'search_posts_layout',
					'search_posts_height_equalized',
				),
			),
			array(
				'label' => esc_html__( 'Item', 'grimlock' ),
				'class' => 'search-item-tab',
				'controls' => array(
					'search_post_thumbnail_border_radius',
					"{$this->section}_divider_210",
					'search_post_border_radius',
					'search_post_border_width',
					"{$this->section}_divider_230",
					'search_post_padding',
					'search_post_margin',
					"{$this->section}_divider_250",
					'search_post_background_color',
					'search_post_border_color',
					"{$this->section}_divider_270",
					'search_post_box_shadow_x_offset',
					'search_post_box_shadow_y_offset',
					'search_post_box_shadow_blur_radius',
					'search_post_box_shadow_spread_radius',
					'search_post_box_shadow_color',
					"{$this->section}_divider_320",
					'search_post_title_color',
					'search_post_color',
					'search_post_link_color',
					'search_post_link_hover_color',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock' ),
				'class' => 'search-style-tab',
				'controls' => array(
					'search_custom_header_padding_y',
					"{$this->section}_divider_410",
					'search_content_padding_y',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_search_customizer_defaults', array(
			'search_custom_header_displayed'        => has_header_image(),
			'search_post_thumbnail_displayed'       => false,
			'search_post_date_displayed'            => true,
			'search_post_author_displayed'          => true,
			'search_post_tag_displayed'             => true,
			'search_category_displayed'             => true,
			'search_post_format_displayed'          => true,
			'search_post_more_link_displayed'       => true,
			'search_comments_link_displayed'        => true,

			'search_custom_header_layout'           => '12-cols-center',
			'search_custom_header_container_layout' => 'classic',
			'search_layout'                         => '3-6-3-cols-left',
			'search_sidebar_mobile_displayed'       => true,
			'search_container_layout'               => 'classic',
			'search_posts_layout'                   => '12-cols-classic',
			'search_posts_height_equalized'         => true,

			'search_post_padding'                   => 20, // px
			'search_post_margin'                    => 15, // px
			'search_post_background_color'          => '#ffffff',
			'search_post_border_radius'             => GRIMLOCK_BORDER_RADIUS,
			'search_post_thumbnail_border_radius'   => GRIMLOCK_BORDER_RADIUS,
			'search_post_border_width'              => GRIMLOCK_BORDER_WIDTH,
			'search_post_color'                     => GRIMLOCK_BODY_COLOR,
			'search_post_box_shadow_x_offset'       => GRIMLOCK_BOX_SHADOW_X_OFFSET,
			'search_post_box_shadow_y_offset'       => GRIMLOCK_BOX_SHADOW_Y_OFFSET,
			'search_post_box_shadow_blur_radius'    => GRIMLOCK_BOX_SHADOW_BLUR_RADIUS,
			'search_post_box_shadow_spread_radius'  => GRIMLOCK_BOX_SHADOW_SPREAD_RADIUS,
			'search_post_box_shadow_color'          => GRIMLOCK_BOX_SHADOW_COLOR,
			'search_post_title_color'               => GRIMLOCK_LINK_COLOR,
			'search_post_link_color'                => GRIMLOCK_LINK_COLOR,
			'search_post_link_hover_color'          => GRIMLOCK_LINK_HOVER_COLOR,
			'search_post_border_color'              => GRIMLOCK_BORDER_COLOR,

			'search_custom_header_padding_y'        => GRIMLOCK_SECTION_PADDING_Y,
			'search_content_padding_y'              => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->elements = apply_filters( 'grimlock_search_customizer_elements', array(
			'.search-posts .card',
		) );

		$this->add_section(                              array( 'priority' => 120 ) );

		$this->add_heading_field(                        array( 'priority' => 10, 'label' => esc_html__( 'Header Display', 'grimlock' ) ) );
		$this->add_custom_header_displayed_field(        array( 'priority' => 10  ) );
		$this->add_divider_field(                        array( 'priority' => 20  ) );
		$this->add_heading_field(                        array( 'priority' => 20, 'label' => esc_html__( 'Results Display', 'grimlock' ) ) );
		$this->add_post_thumbnail_displayed_field(       array( 'priority' => 20  ) );
		$this->add_post_date_displayed_field(            array( 'priority' => 30  ) );
		$this->add_post_author_displayed_field(          array( 'priority' => 40  ) );
		$this->add_post_more_link_displayed_field(       array( 'priority' => 50  ) );
		$this->add_post_tag_displayed_field(             array( 'priority' => 60  ) );
		$this->add_category_displayed_field(             array( 'priority' => 70  ) );
		$this->add_post_format_displayed_field(          array( 'priority' => 80  ) );
		$this->add_comments_link_displayed_field(        array( 'priority' => 90  ) );

		$this->add_custom_header_layout_field(           array( 'priority' => 100 ) );
		$this->add_divider_field(                        array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field( array( 'priority' => 110 ) );
		$this->add_divider_field(                        array( 'priority' => 120 ) );
		$this->add_layout_field(                         array( 'priority' => 120 ) );
		$this->add_sidebar_mobile_displayed_field(       array( 'priority' => 130 ) );
		$this->add_divider_field(                        array( 'priority' => 140 ) );
		$this->add_container_layout_field(               array( 'priority' => 140 ) );
		$this->add_divider_field(                        array( 'priority' => 150 ) );
		$this->add_posts_layout_field(                   array( 'priority' => 150 ) );
		$this->add_posts_height_equalized_field(         array( 'priority' => 160 ) );

		$this->add_post_thumbnail_border_radius_field(   array( 'priority' => 200 ) );
		$this->add_divider_field(                        array( 'priority' => 210 ) );
		$this->add_post_border_radius_field(             array( 'priority' => 210 ) );
		$this->add_post_border_width_field(              array( 'priority' => 220 ) );
		$this->add_divider_field(                        array( 'priority' => 230 ) );
		$this->add_post_padding_field(                   array( 'priority' => 230 ) );
		$this->add_post_margin_field(                    array( 'priority' => 240 ) );
		$this->add_divider_field(                        array( 'priority' => 250 ) );
		$this->add_post_background_color_field(          array( 'priority' => 250 ) );
		$this->add_post_border_color_field(              array( 'priority' => 260 ) );
		$this->add_divider_field(                        array( 'priority' => 270 ) );
		$this->add_post_box_shadow_x_offset_field(       array( 'priority' => 270 ) );
		$this->add_post_box_shadow_y_offset_field(       array( 'priority' => 280 ) );
		$this->add_post_box_shadow_blur_radius_field(    array( 'priority' => 290 ) );
		$this->add_post_box_shadow_spread_radius_field(  array( 'priority' => 300 ) );
		$this->add_post_box_shadow_color_field(          array( 'priority' => 310 ) );
		$this->add_divider_field(                        array( 'priority' => 320 ) );
		$this->add_post_title_color_field(               array( 'priority' => 320 ) );
		$this->add_post_color_field(                     array( 'priority' => 330 ) );
		$this->add_post_link_color_field(                array( 'priority' => 340 ) );
		$this->add_post_link_hover_color_field(          array( 'priority' => 350 ) );

		$this->add_custom_header_padding_y_field(        array( 'priority' => 400 ) );
		$this->add_divider_field(                        array( 'priority' => 410 ) );
		$this->add_content_padding_y_field(              array( 'priority' => 410 ) );
	}

	/**
	 * Add a Kirki slider field to set the margin in the Customizer.
	 *
	 * @since 1.0.0
	 * @param array $args
	 */
	protected function add_post_margin_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$outputs = apply_filters( 'grimlock_search_customizer_post_margin_outputs', array(
				$this->get_css_var_output( 'search_post_margin', 'px' ),
				array(
					'element'       => implode( ',', array(
						'.search-posts',
					) ),
					'property'      => 'margin-left',
					'value_pattern' => '-$px',
				),
				array(
					'element'       => implode( ',', array(
						'.search-posts',
					) ),
					'property'      => 'margin-right',
					'value_pattern' => '-$px',
				),
				array(
					'element'       => implode( ',', array(
						'.search-posts > [id*="post-"]',
					) ),
					'property'      => 'padding-left',
					'units'         => 'px',
				),
				array(
					'element'       => implode( ',', array(
						'.search-posts > [id*="post-"]',
					) ),
					'property'      => 'padding-right',
					'units'         => 'px',
				),
				array(
					'element'      => implode(',', array(
						'.search-posts > [id*="post-"]',
					) ),
					'property'      => 'padding-bottom',
					'value_pattern' => 'calc($px * 2)',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Margin', 'grimlock' ),
				'settings'  => 'search_post_margin',
				'default'   => $this->get_default( 'search_post_margin' ),
				'choices'   => array(
					'min'   => 0,
					'max'   => 50,
					'step'  => 1,
				),
				'priority'  => 10,
				'output'    => $outputs,
				'transport' => 'postMessage',
				'js_vars'   => $this->to_js_vars( $outputs ),
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_search_customizer_post_margin_field_args', $args ) );
		}
	}

	/**
	 * Add scripts to improve user experience in the customizer
	 */
	public function add_scripts() {
		?>
		<script type="text/javascript">
            jQuery( document ).ready( function( $ ) {
                wp.customize.section( '<?php echo esc_js( $this->section ); ?>', function( section ) {
                    section.expanded.bind( function( isExpanded ) {
                        var previewUrl = '<?php echo esc_js( home_url( '?s=' ) ); ?>';
                        var currentPreviewUrl = wp.customize.previewer.previewUrl.get();
                        if ( isExpanded && currentPreviewUrl.indexOf( '?s=' ) === -1 ) {
                            wp.customize.previewer.previewUrl.set( previewUrl );
                        }
                    } );
                } );
            } );
		</script>
		<?php
	}
}

return new Grimlock_Search_Customizer();
