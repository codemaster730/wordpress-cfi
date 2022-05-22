<?php
/**
 * Grimlock_Echo_Knowledge_Base_Archive_EPKB_Post_Type_Customizer Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock-echo-knowledge-base
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Grimlock Customizer class for the knowledge base archive pages.
 */
class Grimlock_Echo_Knowledge_Base_Archive_EPKB_Post_Type_Customizer extends Grimlock_Template_Customizer {
	/**
	 * @since 1.0.0
	 *
	 * @var WP_Post The knowledge base page post
	 */
	protected $kb_page;
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id           = 'archive_epkb_post_type';
		$this->title        = esc_html__( 'Knowledge Base Page', 'grimlock-echo-knowledge-base' );
		$this->section      = 'grimlock_echo_knowledge_base_archive_epkb_post_type_customizer_section';
		$kb_config          = function_exists( 'epkb_get_instance' ) ? epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID ) : array();
		$this->kb_page      = class_exists( 'EPKB_KB_Handler' ) ? get_post( EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config ) ) : 0;

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );
		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 20, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 20, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_is_template',   array( $this, 'archive_customizer_is_template'  ), 10, 1 );

		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );

		add_action( 'get_header',                                array( $this, 'display_before_content'          ), 10, 2 );
		add_action( 'get_footer',                                array( $this, 'display_after_content'           ), 10, 2 );

		add_filter( 'grimlock_echo_knowledge_base_custom_header_background_image', array( $this, 'custom_header_background_image' ), 10, 1 );

		add_filter( 'grimlock_custom_header_customizer_padding_y_field_args',        array( $this, 'add_custom_header_customizer_padding_y_field_description'        ), 10,  1 );
		add_filter( 'grimlock_custom_header_customizer_layout_field_args',           array( $this, 'add_custom_header_customizer_layout_field_description'           ), 10,  1 );
		add_filter( 'grimlock_custom_header_customizer_container_layout_field_args', array( $this, 'add_custom_header_customizer_container_layout_field_description' ), 10,  1 );

		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30, 1 );
	}

	/**
	 * Add custom classes to content to modify layout.
	 *
	 * @param string[] $classes
	 *
	 * @return string[]
	 */
	public function add_content_classes( $classes ) {
		if ( $this->is_template() ) {
			foreach ( $classes as $key => $class ) {
			    if ( strpos( $class, 'region--' ) !== false ) {
			    	unset( $classes[ $key ] );
			    }
			}
			$classes = parent::add_content_classes( $classes );
		}
		return $classes;
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtered array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-echo-knowledge-base' ),
				'class' => 'echo-knowledge-base-general-tab',
				'controls' => array(
					'archive_epkb_post_type_title',
					"{$this->section}_divider_20",
					'archive_epkb_post_type_description',
					"{$this->section}_heading_30",
					"{$this->section}_divider_30",
					'archive_epkb_post_type_custom_header_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-echo-knowledge-base' ),
				'class' => 'echo-knowledge-base-layout-tab',
				'controls' => array(
					'archive_epkb_post_type_custom_header_layout',
					"{$this->section}_divider_110",
					'archive_epkb_post_type_custom_header_container_layout',
					"{$this->section}_divider_120",
					'archive_epkb_post_type_layout',
					"{$this->section}_divider_140",
					'archive_epkb_post_type_sidebar_mobile_displayed',
					'archive_epkb_post_type_container_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-echo-knowledge-base' ),
				'class' => 'echo-knowledge-base-style-tab',
				'controls' => array(
					'archive_epkb_post_type_custom_header_background_image',
					"{$this->section}_divider_210",
					'archive_epkb_post_type_custom_header_padding_y',
					"{$this->section}_divider_220",
					'archive_epkb_post_type_content_padding_y',
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
		$this->defaults = apply_filters( 'grimlock_echo_knowledge_base_archive_epkb_post_type_customizer_defaults', array(
			'archive_epkb_post_type_title'                             => '',
			'archive_epkb_post_type_description'                       => '',
			'archive_epkb_post_type_custom_header_displayed'           => has_header_image(),

			'archive_epkb_post_type_custom_header_layout'              => '6-6-cols-left-reverse',
			'archive_epkb_post_type_custom_header_container_layout'    => 'classic',
			'archive_epkb_post_type_layout'                            => '12-cols-left',
			'archive_epkb_post_type_sidebar_mobile_displayed'          => true,
			'archive_epkb_post_type_container_layout'                  => 'classic',

			'archive_epkb_post_type_custom_header_background_image'    => get_header_image(),
			'archive_epkb_post_type_custom_header_padding_y'           => GRIMLOCK_SECTION_PADDING_Y,
			'archive_epkb_post_type_content_padding_y'                 => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section();

		$this->add_title_field(                          array( 'priority' => 10  ) );
		$this->add_divider_field(                        array( 'priority' => 20  ) );
		$this->add_description_field(                    array( 'priority' => 20  ) );
		$this->add_divider_field(                        array( 'priority' => 30  ) );
		$this->add_heading_field(                        array( 'priority' => 30, 'label' => esc_html__( 'Display', 'grimlock-echo-knowledge-base' ) ) );
		$this->add_custom_header_displayed_field(        array( 'priority' => 30  ) );

		$this->add_custom_header_layout_field(           array( 'priority' => 100 ) );
		$this->add_divider_field(                        array( 'priority' => 110 ) );
		$this->add_custom_header_container_layout_field( array( 'priority' => 110 ) );
		$this->add_divider_field(                        array( 'priority' => 120 ) );
		$this->add_layout_field(                         array( 'priority' => 120 ) );
		$this->add_sidebar_mobile_displayed_field(       array( 'priority' => 130 ) );
		$this->add_divider_field(                        array( 'priority' => 140 ) );
		$this->add_container_layout_field(               array( 'priority' => 140 ) );

		$this->add_custom_header_background_image_field( array( 'priority' => 200 ) );
		$this->add_divider_field(                        array( 'priority' => 210 ) );
		$this->add_custom_header_padding_y_field(        array( 'priority' => 210 ) );
		$this->add_divider_field(                        array( 'priority' => 220 ) );
		$this->add_content_padding_y_field(              array( 'priority' => 220 ) );
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
			Kirki::add_panel( 'grimlock_echo_knowledge_base_customizer_panel', array(
				'priority' => 120,
				'title'    => esc_html__( 'Knowledge Base', 'grimlock-echo-knowledge-base' ),
			) );

			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => isset( $args['priority'] ) ? $args['priority'] :  20,
				'panel'    => 'grimlock_echo_knowledge_base_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki text field to set the title in the Customizer.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_title_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'text_disabled',
				'label'             => esc_html__( 'Title', 'grimlock-echo-knowledge-base' ),
				'description'       => esc_html__( 'You can change the header title of the courses page by editing its title in the admin.', 'grimlock-echo-knowledge-base' ),
				'section'           => $this->section,
				'settings'          => 'archive_epkb_post_type_title',
				'default'           => $this->get_title_default(),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_echo_knowledge_base_archive_epkb_post_type_customizer_title_field_args', $args ) );
		}
	}

	/**
	 * Check if courses page has been set.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when courses page has been set, false otherwise.
	 */
	protected function has_kb_page() {
		return ! empty( $this->kb_page ) && $this->kb_page instanceof WP_Post;
	}

	/**
	 * Get the default title.
	 *
	 * @since 1.0.0
	 *
	 * @return string The courses page title.
	 */
	protected function get_title_default() {
		return $this->has_kb_page() ? get_the_title( $this->kb_page->ID ) : $this->get_default( 'archive_epkb_post_type_title' );
	}

	/**
	 * Get the default description.
	 *
	 * @since 1.0.0
	 *
	 * @return string The default description.
	 */
	protected function get_description_default() {
		return $this->has_kb_page() ? $this->kb_page->post_excerpt : $this->get_default( 'archive_epkb_post_type_description' );
	}

	/**
	 * Get the default URL for the custom header background image.
	 *
	 * @since 1.0.0
	 *
	 * @return string The default URL for the custom header background image.
	 */
	protected function get_custom_header_background_image_default() {
		$kb_page_thumbnail_url = '';

		if ( $this->has_kb_page() ) {
			$kb_page_thumbnail_id   = get_post_thumbnail_id( $this->kb_page->ID );
			$kb_page_thumbnail_atts = ! empty( $kb_page_thumbnail_id ) ? wp_get_attachment_image_src( $kb_page_thumbnail_id, 'custom-header' ) : false;
			$kb_page_thumbnail_url  = ! empty( $kb_page_thumbnail_atts[0] ) ? $kb_page_thumbnail_atts[0] : '';
		}

		return ! empty( $kb_page_thumbnail_url ) ? $kb_page_thumbnail_url : $this->get_default( 'archive_epkb_post_type_custom_header_background_image' );
	}

	/**
	 * Add a Kirki textarea field to set the description in the Customizer.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_description_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'textarea_disabled',
				'label'             => esc_html__( 'Description', 'grimlock-echo-knowledge-base' ),
				'description'       => esc_html__( 'You can change the header text of the courses page by editing its excerpt in the admin.', 'grimlock-echo-knowledge-base' ),
				'section'           => $this->section,
				'settings'          => 'archive_epkb_post_type_description',
				'default'           => $this->get_description_default(),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_echo_knowledge_base_archive_epkb_post_type_customizer_description_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the Custom Header in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_custom_header_background_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'        => 'image',
				'section'     => $this->section,
				'label'       => esc_html__( 'Header Image', 'grimlock-echo-knowledge-base' ),
				'description' => esc_html__( 'You can change the header image of the courses page by editing its featured image in the admin.', 'grimlock-echo-knowledge-base' ),
				'settings'    => 'archive_epkb_post_type_custom_header_background_image',
				'default'     => $this->get_custom_header_background_image_default(),
				'priority'    => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_echo_knowledge_base_archive_epkb_post_type_customizer_custom_header_background_image_field_args', $args ) );
		}
	}

	/**
	 * Add Custom Header args
	 *
	 * @since 1.0.0
	 *
	 * @param $args
	 *
	 * @return array
	 */
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {
			// Use default values for background image, title and subtitle as they read only with the Customizer.
			$args['background_image'] = $this->get_custom_header_background_image_default();
			$args['title']            = $this->get_title_default();
			$args['subtitle']         = "<span class='excerpt'>{$this->get_description_default()}</span>";
		}
		return $args;
	}

	/**
	 * Check whether Custom Header has to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when Custom Header has to be displayed, false otherwise.
	 */
	public function has_custom_header_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( 'archive_epkb_post_type_custom_header_displayed' );
		}
		return $default;
	}

	/**
	 * Check if the current template is the expected template.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the template is the expected template, false otherwise.
	 */
	protected function is_template() {
		$is_template = ( is_archive() && class_exists( 'EPKB_KB_Handler' ) && EPKB_KB_Handler::is_kb_post_type( get_post_type() ) ) || ( $this->has_kb_page() && get_queried_object_id() === $this->kb_page->ID );
		return apply_filters( 'grimlock_echo_knowledge_base_archive_epkb_post_type_customizer_is_template', $is_template );
	}

	/**
	 * Disinherit archive customizer settings
	 *
	 * @param bool $default True if we are on a default archive page
	 *
	 * @return bool
	 */
	public function archive_customizer_is_template( $default ) {
		return $default && ! $this->is_template();
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
	 * Return the custom header background image to use on courses templates
	 *
	 * @param string $background_image Background image url
	 *
	 * @return string Modified background image url
	 */
	public function custom_header_background_image( $background_image ) {
		return $this->get_theme_mod( 'archive_epkb_post_type_custom_header_background_image' );
	}

	/**
	 * Add a link in the description pointing to the course archive vertical padding field.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_padding_y_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#archive_epkb_post_type_custom_header_padding_y" rel="tc-control">Knowledge Base Page</a>', 'grimlock-echo-knowledge-base' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
	}

	/**
	 * Add a link in the description pointing to the course archive layout field.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_layout_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#archive_epkb_post_type_custom_header_layout" rel="tc-control">Knowledge Base Page</a>', 'grimlock-echo-knowledge-base' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
	}

	/**
	 * Add a link in the description pointing to the course archive container layout field.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_container_layout_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#archive_epkb_post_type_custom_header_container_layout" rel="tc-control">Knowledge Base Page</a>', 'grimlock-echo-knowledge-base' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
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
                        var previewUrl = '<?php echo esc_js( trailingslashit( get_permalink( $this->kb_page ) ) ); ?>';
                        if ( isExpanded && wp.customize.previewer.previewUrl.get() !== previewUrl ) {
                            wp.customize.previewer.previewUrl.set( previewUrl );
                        }
                    } );
                } );
            } );
		</script>
		<?php
	}
}

return new Grimlock_Echo_Knowledge_Base_Archive_EPKB_Post_Type_Customizer();
