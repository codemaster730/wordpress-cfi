<?php
/**
 * Grimlock_Echo_Knowledge_Base_Single_EPKB_Post_Type_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-echo-knowledge-base
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the single knowledge base article
 */
class Grimlock_Echo_Knowledge_Base_Single_EPKB_Post_Type_Customizer extends Grimlock_Singular_Template_Customizer {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'single_epkb_post_type';
		$this->section = 'grimlock_echo_knowledge_base_single_epkb_post_type_customizer_section';
		$this->title   = esc_html__( 'Knowledge Base Article', 'grimlock-echo-knowledge-base' );

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );

		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_single_customizer_is_template',    array( $this, 'single_customizer_is_template'   ), 10, 1 );

		add_action( 'get_header',                                array( $this, 'display_before_content'          ), 10, 2 );
		add_action( 'get_footer',                                array( $this, 'display_after_content'           ), 10, 2 );

		add_filter( 'kirki_grimlock_dynamic_css',                array( $this, 'add_dynamic_css'                 ), 10, 1 );
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-echo-knowledge-base' ),
				'class' => 'single_epkb_post_type-general-tab',
				'controls' => array(
					"{$this->section}_heading_10",
					'single_epkb_post_type_custom_header_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-echo-knowledge-base' ),
				'class' => 'single_epkb_post_type-layout-tab',
				'controls' => array(
					'single_epkb_post_type_custom_header_layout',
					"{$this->section}_divider_110",
					'single_epkb_post_type_custom_header_container_layout',
					"{$this->section}_divider_120",
					'single_epkb_post_type_layout',
					'single_epkb_post_type_sidebar_mobile_displayed',
					"{$this->section}_divider_140",
					'single_epkb_post_type_container_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-echo-knowledge-base' ),
				'class' => 'single_epkb_post_type-style-tab',
				'controls' => array(
					'single_epkb_post_type_custom_header_padding_y',
					"{$this->section}_divider_210",
					'single_epkb_post_type_content_padding_y',
				),
			),
		);
		return $js_data;
	}

	/**
	 * Disinherit single customizer settings
	 *
	 * @param bool $default True if we are on a default single page
	 *
	 * @return bool
	 */
	public function single_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
	}

	/**
	 * Register default values, settings and custom controls for the Theme Customizer.
	 *
	 * @since 1.0.0
	 */
	public function add_customizer_fields() {
		$this->defaults = apply_filters( 'grimlock_echo_knowledge_base_single_epkb_post_type_customizer_defaults', array(
			'single_epkb_post_type_custom_header_displayed'                => has_header_image(),

			'single_epkb_post_type_custom_header_layout'                   => '12-cols-center',
			'single_epkb_post_type_custom_header_container_layout'         => 'classic',
			'single_epkb_post_type_layout'                                 => '12-cols-left',
			'single_epkb_post_type_sidebar_mobile_displayed'               => true,
			'single_epkb_post_type_container_layout'                       => 'classic',

			'single_epkb_post_type_custom_header_padding_y'                => GRIMLOCK_SECTION_PADDING_Y,
			'single_epkb_post_type_content_padding_y'                      => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section();

		$this->add_heading_field(                                array( 'priority' => 10, 'label' => esc_html__( 'Header Display', 'grimlock-echo-knowledge-base' ) ) );
		$this->add_custom_header_displayed_field(                array( 'priority' => 20  ) );

		$this->add_custom_header_layout_field(                   array( 'priority' => 100 ) );
		$this->add_divider_field(                                array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field(         array( 'priority' => 110 ) );
		$this->add_divider_field(                                array( 'priority' => 120 ) );
		$this->add_layout_field(                                 array( 'priority' => 120 ) );
		$this->add_sidebar_mobile_displayed_field(               array( 'priority' => 130 ) );
		$this->add_divider_field(                                array( 'priority' => 140 ) );
		$this->add_container_layout_field(                       array( 'priority' => 140 ) );

		$this->add_custom_header_padding_y_field(                array( 'priority' => 200 ) );
		$this->add_divider_field(                                array( 'priority' => 210 ) );
		$this->add_content_padding_y_field(                      array( 'priority' => 210 ) );
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
			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => 30,
				'panel'    => 'grimlock_echo_knowledge_base_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add arguments using theme mods to customize the Custom Header.
	 *
	 * @param array $args The default arguments to render the Custom Header.
	 *
	 * @return array      The arguments to render the Custom Header.
	 */
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			$args['background_image'] = get_header_image();
			$kb_config                = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
			$kb_page                  = get_post( EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config ) );

			if ( ! empty( $kb_page ) && $kb_page instanceof WP_Post ) {
				$kb_page_thumbnail_id   = get_post_thumbnail_id( $kb_page->ID );
				$kb_page_thumbnail_atts = ! empty( $kb_page_thumbnail_id ) ? wp_get_attachment_image_src( $kb_page_thumbnail_id, 'custom-header' ) : false;

				if ( ! empty( $kb_page_thumbnail_atts[0] ) ) {
					$args['background_image'] = $kb_page_thumbnail_atts[0];
				}
			}
		}

		return $args;
	}

	/**
	 * Display sidebar and container before content
	 *
	 * @param string $name Name for the header
	 * @param array $args Args for the header
	 */
	public function display_before_content( $name, $args ) {
		if ( $this->is_template() ) {
			// Prevent infinite loop
			remove_action( 'get_header', array( $this, 'display_before_content' ), 10 );

			get_header( $name, $args );
			get_sidebar( 'left' );
			?>
			<div id="primary" class="content-area region__col region__col--2">
				<main id="main" class="site-main">
			<?php
		}
	}

	/**
	 * Display sidebar and container after content
	 *
	 * @param string $name Name for the footer
	 * @param array $args Args for the footer
	 */
	public function display_after_content( $name, $args ) {
		if ( $this->is_template() ) {
			// Prevent infinite loop
			remove_action( 'get_footer', array( $this, 'display_after_content' ), 10 );

			?>
				</main>
			</div>
			<?php
			get_sidebar( 'right' );
			get_footer( $name, $args );
		}
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = is_singular() && class_exists( 'EPKB_KB_Handler' ) && EPKB_KB_Handler::is_kb_post_type( get_post_type() );
		return apply_filters( 'grimlock_echo_knowledge_base_single_epkb_post_type_customizer_is_template', $is_template );
	}
}

return new Grimlock_Echo_Knowledge_Base_Single_EPKB_Post_Type_Customizer();
