<?php
/**
 * Grimlock_Archive_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the posts page and the archive pages.
 */
class Grimlock_Archive_Customizer extends Grimlock_Grid_Template_Customizer {

	/**
	 * @since 1.0.7
	 *
	 * @var WP_Post The posts page post
	 */
	protected $posts_page;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id         = 'archive';
		$this->section    = 'grimlock_archive_customizer_section';
		$this->title      = esc_html__( 'Posts Page', 'grimlock' );
		$this->posts_page = get_post( get_option( 'page_for_posts' ) );

		add_action( 'wp_enqueue_scripts',                           array( $this, 'enqueue_scripts'                 ), 10    );
		add_action( 'after_setup_theme',                            array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                   array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',         array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                       array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',                  array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',             array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed',    array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',     array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_posts_class',                         array( $this, 'add_posts_classes'               ), 10, 1 );
		add_filter( 'grimlock_post_args',                           array( $this, 'add_post_args'                   ), 10, 1 );
		add_filter( 'grimlock_post_args',                           array( $this, 'add_post_thumbnail_size_arg'     ), 10, 1 );
		add_filter( 'grimlock_query_post_args',                     array( $this, 'add_post_args'                   ), 10, 1 );
		add_filter( 'grimlock_archive_category_terms_displayed',    array( $this, 'has_category_terms_displayed'    ), 10, 1 );
		add_filter( 'grimlock_archive_post_format_terms_displayed', array( $this, 'has_post_format_terms_displayed' ), 10, 1 );
		add_filter( 'grimlock_archive_post_tag_terms_displayed',    array( $this, 'has_post_tag_terms_displayed'    ), 10, 1 );

		add_filter( 'kirki_grimlock_dynamic_css',                   array( $this, 'add_dynamic_css'                 ), 10, 1 );
		add_action( 'customize_controls_print_scripts',             array( $this, 'add_scripts'                     ), 30, 1 );
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
				'class' => 'archive-general-tab',
				'controls' => array(
					'archive_title',
					"{$this->section}_divider_20",
					'archive_description',
					"{$this->section}_divider_30",
					"{$this->section}_heading_30",
					'archive_custom_header_displayed',
					"{$this->section}_divider_40",
					"{$this->section}_heading_40",
					'archive_post_tag_terms_displayed',
					'archive_category_terms_displayed',
					'archive_post_format_terms_displayed',
					"{$this->section}_divider_70",
					"{$this->section}_heading_70",
					'archive_post_thumbnail_displayed',
					'archive_post_date_displayed',
					'archive_post_author_displayed',
					'archive_post_content_displayed',
					'archive_post_excerpt_displayed',
					'archive_post_more_link_displayed',
					'archive_post_tag_displayed',
					'archive_category_displayed',
					'archive_post_format_displayed',
					'archive_comments_link_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock' ),
				'class' => 'archive-layout-tab',
				'controls' => array(
					'archive_custom_header_layout',
					"{$this->section}_divider_210",
					'archive_custom_header_container_layout',
					"{$this->section}_divider_220",
					'archive_layout',
					'archive_sidebar_mobile_displayed',
					"{$this->section}_divider_240",
					'archive_container_layout',
					"{$this->section}_divider_250",
					'archive_posts_layout',
					'archive_posts_height_equalized',
				),
			),
			array(
				'label' => esc_html__( 'Item', 'grimlock' ),
				'class' => 'archive-item-tab',
				'controls' => array(
					'archive_post_thumbnail_border_radius',
					"{$this->section}_divider_310",
					'archive_post_border_radius',
					'archive_post_border_width',
					"{$this->section}_divider_330",
					'archive_post_padding',
					'archive_post_margin',
					"{$this->section}_divider_350",
					'archive_post_background_color',
					'archive_post_border_color',
					"{$this->section}_divider_370",
					'archive_post_box_shadow_x_offset',
					'archive_post_box_shadow_y_offset',
					'archive_post_box_shadow_blur_radius',
					'archive_post_box_shadow_spread_radius',
					'archive_post_box_shadow_color',
					"{$this->section}_divider_420",
					'archive_post_title_color',
					'archive_post_color',
					'archive_post_link_color',
					'archive_post_link_hover_color',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock' ),
				'class' => 'archive-style-tab',
				'controls' => array(
					'archive_custom_header_background_image',
					"{$this->section}_divider_510",
					'archive_custom_header_padding_y',
					"{$this->section}_divider_520",
					'archive_content_padding_y',
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
		$this->defaults = apply_filters( 'grimlock_archive_customizer_defaults', array(
			'archive_title'                          => $this->has_posts_page() ? get_the_title( $this->posts_page->ID ) : '',
			'archive_description'                    => $this->has_posts_page() ? $this->posts_page->post_excerpt : '',
			'archive_custom_header_displayed'        => has_header_image(),
			'archive_post_tag_terms_displayed'       => false,
			'archive_category_terms_displayed'       => false,
			'archive_post_format_terms_displayed'    => false,
			'archive_post_thumbnail_displayed'       => false,
			'archive_post_date_displayed'            => true,
			'archive_post_author_displayed'          => true,
			'archive_post_content_displayed'         => false,
			'archive_post_excerpt_displayed'         => true,
			'archive_post_more_link_displayed'       => true,
			'archive_post_tag_displayed'             => true,
			'archive_category_displayed'             => true,
			'archive_post_format_displayed'          => true,

			'archive_post_padding'                   => 20, // px
			'archive_post_margin'                    => 15, // px
			'archive_post_background_color'          => '#ffffff',
			'archive_post_border_radius'             => GRIMLOCK_BORDER_RADIUS,
			'archive_post_thumbnail_border_radius'   => GRIMLOCK_BORDER_RADIUS,
			'archive_post_border_width'              => GRIMLOCK_BORDER_WIDTH,
			'archive_post_color'                     => GRIMLOCK_BODY_COLOR,
			'archive_post_box_shadow_x_offset'       => GRIMLOCK_BOX_SHADOW_X_OFFSET,
			'archive_post_box_shadow_y_offset'       => GRIMLOCK_BOX_SHADOW_Y_OFFSET,
			'archive_post_box_shadow_blur_radius'    => GRIMLOCK_BOX_SHADOW_BLUR_RADIUS,
			'archive_post_box_shadow_spread_radius'  => GRIMLOCK_BOX_SHADOW_SPREAD_RADIUS,
			'archive_post_box_shadow_color'          => GRIMLOCK_BOX_SHADOW_COLOR,
			'archive_post_title_color'               => GRIMLOCK_LINK_COLOR,
			'archive_post_link_color'                => GRIMLOCK_LINK_COLOR,
			'archive_post_link_hover_color'          => GRIMLOCK_LINK_HOVER_COLOR,
			'archive_post_border_color'              => GRIMLOCK_BORDER_COLOR,

			'archive_custom_header_layout'           => '12-cols-center',
			'archive_custom_header_container_layout' => 'classic',
			'archive_layout'                         => '3-6-3-cols-left',
			'archive_sidebar_mobile_displayed'       => true,
			'archive_container_layout'               => 'classic',
			'archive_posts_layout'                   => '12-cols-classic',
			'archive_posts_height_equalized'         => true,

			'archive_custom_header_background_image' => get_header_image(),
			'archive_custom_header_padding_y'        => GRIMLOCK_SECTION_PADDING_Y,
			'archive_content_padding_y'              => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->elements = apply_filters( 'grimlock_archive_customizer_elements', array(
			'.card',
			'.blog-posts .card',
			'.archive-posts .card',
			'.grimlock-query-section__posts .card',
			'.grimlock-term-query-section__terms .card',
			'.modal .modal-content',
			'.post-password-form',
			'.single .wp-playlist-light',
			'article[id*="post-"] > .entry-content > #loginform',
		) );

		$this->add_section(                              array( 'priority' => 20  ) );

		$this->add_title_field(                          array( 'priority' => 10  ) );
		$this->add_divider_field(                        array( 'priority' => 20  ) );
		$this->add_description_field(                    array( 'priority' => 20  ) );
		$this->add_divider_field(                        array( 'priority' => 30  ) );
		$this->add_heading_field(                        array( 'priority' => 30, 'label' => esc_html__( 'Header Display', 'grimlock' ) ) );
		$this->add_custom_header_displayed_field(        array( 'priority' => 30  ) );
		$this->add_divider_field(                        array( 'priority' => 40  ) );
		$this->add_heading_field(                        array( 'priority' => 40, 'label' => esc_html__( 'Filter Display', 'grimlock' ) ) );
		$this->add_post_tag_terms_displayed_field(       array( 'priority' => 40  ) );
		$this->add_category_terms_displayed_field(       array( 'priority' => 50  ) );
		$this->add_post_format_terms_displayed_field(    array( 'priority' => 60  ) );
		$this->add_divider_field(                        array( 'priority' => 70  ) );
		$this->add_heading_field(                        array( 'priority' => 70, 'label' => esc_html__( 'Post Display', 'grimlock' ) ) );
		$this->add_post_thumbnail_displayed_field(       array( 'priority' => 70  ) );
		$this->add_post_date_displayed_field(            array( 'priority' => 80  ) );
		$this->add_post_author_displayed_field(          array( 'priority' => 90  ) );
		$this->add_post_content_displayed_field(         array( 'priority' => 100 ) );
		$this->add_post_excerpt_displayed_field(         array( 'priority' => 110 ) );
		$this->add_post_more_link_displayed_field(       array( 'priority' => 120 ) );
		$this->add_post_tag_displayed_field(             array( 'priority' => 130 ) );
		$this->add_category_displayed_field(             array( 'priority' => 140 ) );
		$this->add_post_format_displayed_field(          array( 'priority' => 150 ) );
		$this->add_comments_link_displayed_field(        array( 'priority' => 160 ) );

		$this->add_custom_header_layout_field(           array( 'priority' => 200 ) );
		$this->add_divider_field(                        array( 'priority' => 210 ) );
		$this->add_custom_header_container_layout_field( array( 'priority' => 210 ) );
		$this->add_divider_field(                        array( 'priority' => 220 ) );
		$this->add_layout_field(                         array( 'priority' => 220 ) );
		$this->add_sidebar_mobile_displayed_field(       array( 'priority' => 230 ) );
		$this->add_divider_field(                        array( 'priority' => 240 ) );
		$this->add_container_layout_field(               array( 'priority' => 240 ) );
		$this->add_divider_field(                        array( 'priority' => 250 ) );
		$this->add_posts_layout_field(                   array( 'priority' => 250 ) );
		$this->add_posts_height_equalized_field(         array( 'priority' => 260 ) );

		$this->add_post_thumbnail_border_radius_field(   array( 'priority' => 300 ) );
		$this->add_divider_field(                        array( 'priority' => 310 ) );
		$this->add_post_border_radius_field(             array( 'priority' => 310 ) );
		$this->add_post_border_width_field(              array( 'priority' => 320 ) );
		$this->add_divider_field(                        array( 'priority' => 330 ) );
		$this->add_post_padding_field(                   array( 'priority' => 330 ) );
		$this->add_post_margin_field(                    array( 'priority' => 340 ) );
		$this->add_divider_field(                        array( 'priority' => 350 ) );
		$this->add_post_background_color_field(          array( 'priority' => 350 ) );
		$this->add_post_border_color_field(              array( 'priority' => 360 ) );
		$this->add_divider_field(                        array( 'priority' => 370 ) );
		$this->add_post_box_shadow_x_offset_field(       array( 'priority' => 370 ) );
		$this->add_post_box_shadow_y_offset_field(       array( 'priority' => 380 ) );
		$this->add_post_box_shadow_blur_radius_field(    array( 'priority' => 390 ) );
		$this->add_post_box_shadow_spread_radius_field(  array( 'priority' => 400 ) );
		$this->add_post_box_shadow_color_field(          array( 'priority' => 410 ) );
		$this->add_divider_field(                        array( 'priority' => 420 ) );
		$this->add_post_title_color_field(               array( 'priority' => 420 ) );
		$this->add_post_color_field(                     array( 'priority' => 430 ) );
		$this->add_post_link_color_field(                array( 'priority' => 440 ) );
		$this->add_post_link_hover_color_field(          array( 'priority' => 450 ) );

		$this->add_custom_header_background_image_field( array( 'priority' => 500 ) );
		$this->add_divider_field(                        array( 'priority' => 510 ) );
		$this->add_custom_header_padding_y_field(        array( 'priority' => 510 ) );
		$this->add_divider_field(                        array( 'priority' => 520 ) );
		$this->add_content_padding_y_field(              array( 'priority' => 520 ) );
	}

	/**
	 * Check if posts page has been set.
	 *
	 * @since 1.0.7
	 *
	 * @return bool True when posts page has been set, false otherwise.
	 */
	protected function has_posts_page() {
		return ! empty( $this->posts_page ) && $this->posts_page instanceof WP_Post;
	}

	/**
	 * Get the default title.
	 *
	 * @since 1.0.7
	 *
	 * @return string The posts page title.
	 */
	protected function get_title_default() {
		return is_home() ? $this->get_default( 'archive_title' ) : get_the_archive_title();
	}

	/**
	 * Get the default description.
	 *
	 * @since 1.0.7
	 *
	 * @return string The default description.
	 */
	protected function get_description_default() {
		return is_home() ? $this->get_default( 'archive_description' ) : get_the_archive_description();
	}

	/**
	 * Get the default URL for the custom header background image.
	 *
	 * @since 1.0.7
	 *
	 * @return string The default URL for the custom header background image.
	 */
	protected function get_custom_header_background_image_default() {
		$posts_page_thumbnail_url = '';

		$size = apply_filters( "grimlock_{$this->id}_customizer_custom_header_size", 'custom-header', $this->get_theme_mod( 'archive_custom_header_layout' ) );

		if ( $this->has_posts_page() ) {
			$posts_page_thumbnail_id   = get_post_thumbnail_id( $this->posts_page->ID );
			$posts_page_thumbnail_atts = ! empty( $posts_page_thumbnail_id ) ? wp_get_attachment_image_src( $posts_page_thumbnail_id, $size ) : false;
			$posts_page_thumbnail_url  = ! empty( $posts_page_thumbnail_atts[0] ) ? $posts_page_thumbnail_atts[0] : '';
		}

		$header_image_url            = $this->get_default( 'archive_custom_header_background_image' );
		$header_image_id             = attachment_url_to_postid( $header_image_url );
		$default_custom_header_image = ! empty( $header_image_id ) ? wp_get_attachment_image_url( $header_image_id, $size ) : $header_image_url;

		return ! empty( $posts_page_thumbnail_url ) ? $posts_page_thumbnail_url : $default_custom_header_image;
	}

	/**
	 * Add a Kirki section in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_section( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			Kirki::add_panel( 'grimlock_posts_customizer_panel', array(
				'priority' => 120,
				'title'    => esc_html__( 'Posts', 'grimlock' ),
			) );

			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => isset( $args['priority'] ) ? $args['priority'] :  20,
				'panel'    => 'grimlock_posts_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki text field to set the title in the Customizer.
	 *
	 * @since 1.0.7
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_title_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'text_disabled',
				'label'             => esc_html__( 'Title', 'grimlock' ),
				'description'       => esc_html__( 'You can change the header title for the posts page by editing its title in the admin.', 'grimlock' ),
				'section'           => $this->section,
				'settings'          => 'archive_title',
				'default'           => $this->get_default( 'archive_title' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_archive_customizer_title_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki textarea field to set the description in the Customizer.
	 *
	 * @since 1.0.7
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_description_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'textarea_disabled',
				'label'             => esc_html__( 'Description', 'grimlock' ),
				'description'       => esc_html__( 'You can change the header text for the posts page by editing its excerpt in the admin.', 'grimlock' ),
				'section'           => $this->section,
				'settings'          => 'archive_description',
				'default'           => $this->get_default( 'archive_description' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_archive_customizer_description_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the Custom Header in the Customizer.
	 *
	 * @since 1.0.7
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_custom_header_background_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'image',
				'section'     => $this->section,
				'label'       => esc_html__( 'Header Image', 'grimlock' ),
				'description' => esc_html__( 'You can change the header image for the posts page by editing its featured image in the admin.', 'grimlock' ),
				'settings'    => 'archive_custom_header_background_image',
				'default'     => $this->get_custom_header_background_image_default(),
				'priority'    => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_archive_customizer_custom_header_background_image_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post tag terms display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_tag_terms_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display tag filter', 'grimlock' ),
				'settings' => 'archive_post_tag_terms_displayed',
				'default'  => $this->get_default( 'archive_post_tag_terms_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_archive_customizer_post_tag_terms_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the category terms display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_category_terms_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display category filter', 'grimlock' ),
				'settings' => 'archive_category_terms_displayed',
				'default'  => $this->get_default( 'archive_category_terms_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_archive_customizer_category_terms_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki checkbox to set the post format terms display in the Customizer.
	 *
	 * @param array $args
	 * @since 1.0.0
	 */
	protected function add_post_format_terms_displayed_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'checkbox',
				'section'  => $this->section,
				'label'    => esc_html__( 'Display format filter', 'grimlock' ),
				'settings' => 'archive_post_format_terms_displayed',
				'default'  => $this->get_default( 'archive_post_format_terms_displayed' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_archive_customizer_post_format_terms_displayed_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki slider field to set the margin in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_post_margin_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$outputs = apply_filters( 'grimlock_archive_customizer_post_margin_outputs', array(
				$this->get_css_var_output( 'archive_post_margin', 'px' ),
				array(
					'element'       => implode( ',', array(
						'.blog-posts',
						'.archive-posts',
					) ),
					'property'      => 'margin-left',
					'value_pattern' => '-$px',
					'suffix'        => '!important',
				),
				array(
					'element'       => implode( ',', array(
						'.blog-posts',
						'.archive-posts',
					) ),
					'property'      => 'margin-right',
					'value_pattern' => '-$px',
					'suffix'        => '!important',
				),
				array(
					'element'       => implode( ',', array(
						'.blog-posts > [id*="post-"]',
						'.archive-posts > [id*="post-"]',
					) ),
					'property'      => 'padding-left',
					'units'         => 'px',
					'suffix'        => '!important',
				),
				array(
					'element'       => implode( ',', array(
						'.blog-posts > [id*="post-"]',
						'.archive-posts > [id*="post-"]',
					) ),
					'property'      => 'padding-right',
					'units'         => 'px',
					'suffix'        => '!important',
				),
				array(
					'element' => implode(',', array(
						'.blog-posts > [id*="post-"]',
						'.archive-posts > [id*="post-"]',
						'.posts > [id*="post-"]',
					) ),
					'property'      => 'padding-bottom',
					'value_pattern' => 'calc($px * 2)',
					'suffix'        => '!important',
				),
			) );

			$args = wp_parse_args( $args, array(
				'type'      => 'slider',
				'section'   => $this->section,
				'label'     => esc_attr__( 'Margin', 'grimlock' ),
				'settings'  => 'archive_post_margin',
				'default'   => $this->get_default( 'archive_post_margin' ),
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

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_archive_customizer_post_margin_field_args', $args ) );
		}
	}

	/**
	 * Add custom classes to body to modify layout.
	 *
	 * @param $classes
	 * @since 1.3.9
	 *
	 * @return string
	 */
	public function add_body_classes( $classes ) {
		$classes = parent::add_body_classes( $classes );

		if ( $this->is_template()
		     && ( true == $this->get_theme_mod( 'archive_category_terms_displayed' )
		          || true == $this->get_theme_mod( 'archive_post_tag_terms_displayed' )
		          || true == $this->get_theme_mod( 'archive_post_format_terms_displayed' ) ) ) {
			$classes[] = "grimlock--posts-filters-displayed";
		}
		return $classes;
	}

	/**
	 * Add arguments using theme mods to customize the region component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the region.
	 *
	 * @return array      The arguments to render the region.
	 */
	public function add_custom_header_args( $args = array() ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			// Use default values for background image, title and subtitle as they read only with the Customizer.
			$args['background_image'] = $this->get_custom_header_background_image_default();
			$args['title']            = $this->get_title_default();
			$args['subtitle']         = "<span class='excerpt'>{$this->get_description_default()}</span>";

			if ( is_author() ) {
				$args['thumbnail'] = get_avatar_url( get_the_author_meta( 'ID' ), 128 );
			}
		}
		return $args;
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
	public function add_post_args( $args = array() ) {
		$args['post_thumbnail_displayed'] = $this->get_theme_mod( 'archive_post_thumbnail_displayed' );
		$args['post_date_displayed']      = $this->get_theme_mod( 'archive_post_date_displayed' );
		$args['post_author_displayed']    = $this->get_theme_mod( 'archive_post_author_displayed' );
		$args['post_content_displayed']   = $this->get_theme_mod( 'archive_post_content_displayed' );
		$args['post_excerpt_displayed']   = $this->get_theme_mod( 'archive_post_excerpt_displayed' );
		$args['post_more_link_displayed'] = $this->get_theme_mod( 'archive_post_more_link_displayed' );
		$args['category_displayed']       = $this->get_theme_mod( 'archive_category_displayed' );
		$args['post_tag_displayed']       = $this->get_theme_mod( 'archive_post_tag_displayed' );
		$args['post_format_displayed']    = $this->get_theme_mod( 'archive_post_format_displayed' );
		$args['comments_link_displayed']  = $this->get_theme_mod( 'archive_comments_link_displayed' );
		return $args;
	}

	/**
	 * Add post thumbnail argument using theme mod to customize the post display on the archive page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The default arguments to render the post.
	 *
	 * @return array      The arguments to render the post.
	 */
	public function add_post_thumbnail_size_arg( $args = array() ) {
		$args['post_thumbnail_size'] = apply_filters( 'grimlock_archive_customizer_post_thumbnail_size', 'large', $this->get_theme_mod( 'archive_posts_layout' ), get_post_type() );
		return $args;
	}

	/**
	 * Check if the current template is the expected template, the blog or a similar template.
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		return apply_filters( 'grimlock_archive_customizer_is_template', is_archive() );
	}

	/**
	 * Check if the category terms have to be displayed on posts page.
	 *
	 * @since 1.0.0
	 *
	 * @param $default
	 *
	 * @return bool True if the terms have to be displayed, false otherwise.
	 */
	public function has_category_terms_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( 'archive_category_terms_displayed' );
		}
		return $default;
	}

	/**
	 * Check if the post tags have to be displayed on posts page.
	 *
	 * @since 1.0.0
	 *
	 * @param $default
	 *
	 * @return bool True if the terms have to be displayed, false otherwise.
	 */
	public function has_post_tag_terms_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( 'archive_post_tag_terms_displayed' );
		}
		return $default;
	}

	/**
	 * Check if the post format terms have to be displayed on posts page.
	 *
	 * @since 1.0.0
	 *
	 * @param $default
	 *
	 * @return bool True if the terms have to be displayed, false otherwise.
	 */
	public function has_post_format_terms_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( 'archive_post_format_terms_displayed' );
		}
		return $default;
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
                        var pageForPosts = wp.customize.control( 'page_for_posts' ).setting();
                        if ( !! pageForPosts ) {
                            var previewUrl = wp.customize.settings.url.home + '?page_id=' + pageForPosts;
                            if ( isExpanded && wp.customize.previewer.previewUrl.get() !== previewUrl ) {
                                wp.customize.previewer.previewUrl.set( previewUrl );
                            }
                        }
                    } );
                } );
            } );
		</script>
		<?php
	}
}

return new Grimlock_Archive_Customizer();
