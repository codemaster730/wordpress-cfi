<?php
/**
 * Grimlock_The_Events_Calendar_Customizer Class
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
 * The Grimlock Customizer class for the product archive pages.
 */
class Grimlock_The_Events_Calendar_Archive_Tribe_Events_Customizer extends Grimlock_Template_Customizer {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->id      = 'the_events_calendar';
		$this->title   = esc_html__( 'Events Page', 'grimlock-the-events-calendar' );
		$this->section = 'grimlock_the_events_calendar_archive_tribe_events_customizer_section';

		add_action( 'wp_footer',                                 array( $this, 'remove_customizer_css'           ), 10    );

		add_action( 'after_setup_theme',                         array( $this, 'add_customizer_fields'           ), 20    );

		add_filter( 'body_class',                                array( $this, 'add_body_classes'                ), 10, 1 );
		add_filter( 'grimlock_customizer_controls_js_data',      array( $this, 'add_customizer_controls_js_data' ), 10, 1 );
		add_filter( 'grimlock_content_class',                    array( $this, 'add_content_classes'             ), 10, 1 );
		add_filter( 'grimlock_custom_header_args',               array( $this, 'add_custom_header_args'          ), 30, 1 );
		add_filter( 'grimlock_custom_header_displayed',          array( $this, 'has_custom_header_displayed'     ), 20, 1 );
		add_filter( 'grimlock_template_sidebar_right_displayed', array( $this, 'has_sidebar_right_displayed'     ), 10, 1 );
		add_filter( 'grimlock_template_sidebar_left_displayed',  array( $this, 'has_sidebar_left_displayed'      ), 10, 1 );
		add_filter( 'grimlock_archive_customizer_is_template',   array( $this, 'archive_customizer_is_template'  ), 10, 1 );
		add_filter( 'grimlock_single_customizer_is_template',    array( $this, 'single_customizer_is_template'   ), 10, 1 );

		add_filter( 'grimlock_the_events_calendar_custom_header_background_image', array( $this, 'custom_header_background_image' ), 10, 1 );

		add_filter( 'grimlock_custom_header_customizer_padding_y_field_args',        array( $this, 'add_custom_header_customizer_padding_y_field_description'        ), 10,  1 );
		add_filter( 'grimlock_custom_header_customizer_layout_field_args',           array( $this, 'add_custom_header_customizer_layout_field_description'           ), 10,  1 );
		add_filter( 'grimlock_custom_header_customizer_container_layout_field_args', array( $this, 'add_custom_header_customizer_container_layout_field_description' ), 10,  1 );

		add_action( 'customize_controls_print_scripts', array( $this, 'add_scripts' ), 30, 1 );
	}

	/**
	 * Remove the Tribe Customizer css <script>
	 *
	 * @since 1.0.0
	 */
	public function remove_customizer_css() {
		if ( class_exists( 'Tribe__Customizer' ) ) {
			remove_action( 'wp_print_footer_scripts', array( Tribe__Customizer::instance(), 'print_css_template' ), 15 );
		}
	}

	/**
	 * Add tabs to the Customizer to group controls.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $js_data The array of data for the Customizer controls.
	 *
	 * @return array          The filtred array of data for the Customizer controls.
	 */
	public function add_customizer_controls_js_data( $js_data ) {
		$js_data['tabs'][$this->section] = array(
			array(
				'label' => esc_html__( 'General', 'grimlock-the-events-calendar' ),
				'class' => 'the_events_calendar-general-tab',
				'controls' => array(
					'the_events_calendar_title',
					"{$this->section}_divider_20",
					'the_events_calendar_description',
					"{$this->section}_heading_30",
					"{$this->section}_divider_30",
					'the_events_calendar_custom_header_displayed',
				),
			),
			array(
				'label' => esc_html__( 'Layout', 'grimlock-the-events-calendar' ),
				'class' => 'the_events_calendar-layout-tab',
				'controls' => array(
					'the_events_calendar_custom_header_layout',
					"{$this->section}_divider_110",
					'the_events_calendar_custom_header_container_layout',
					"{$this->section}_divider_120",
					'the_events_calendar_layout',
					"{$this->section}_divider_140",
					'the_events_calendar_sidebar_mobile_displayed',
					'the_events_calendar_container_layout',
				),
			),
			array(
				'label' => esc_html__( 'Style', 'grimlock-the-events-calendar' ),
				'class' => 'the_events_calendar-style-tab',
				'controls' => array(
					'the_events_calendar_custom_header_background_image',
					"{$this->section}_divider_210",
					'the_events_calendar_custom_header_padding_y',
					"{$this->section}_divider_220",
					'the_events_calendar_content_padding_y',
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
		$post_type_obj = get_post_type_object( 'tribe_events' );
		$archive_title = esc_html__( 'Events', 'grimlock-the-events-calendar' );

		if ( is_object( $post_type_obj ) && isset( $post_type_obj->label ) && $post_type_obj->label !== '' ) {
			$archive_title = $post_type_obj->label;
		}

		$this->defaults = apply_filters( 'grimlock_the_events_calendar_archive_tribe_events_customizer_defaults', array(
			'the_events_calendar_title'                             => $archive_title,
			'the_events_calendar_description'                       => '',
			'the_events_calendar_custom_header_displayed'           => has_header_image(),

			'the_events_calendar_custom_header_layout'              => '6-6-cols-left-reverse',
			'the_events_calendar_custom_header_container_layout'    => 'classic',
			'the_events_calendar_layout'                            => '12-cols-left',
			'the_events_calendar_sidebar_mobile_displayed'          => true,
			'the_events_calendar_container_layout'                  => 'classic',

			'the_events_calendar_custom_header_background_image'    => get_header_image(),
			'the_events_calendar_custom_header_padding_y'           => GRIMLOCK_SECTION_PADDING_Y,
			'the_events_calendar_content_padding_y'                 => GRIMLOCK_CONTENT_PADDING_Y,
		) );

		$this->add_section();

		$this->add_title_field(                          array( 'priority' => 10  ) );
		$this->add_divider_field(                        array( 'priority' => 20  ) );
		$this->add_description_field(                    array( 'priority' => 20  ) );
		$this->add_divider_field(                        array( 'priority' => 30  ) );
		$this->add_heading_field(                        array( 'priority' => 30, 'label' => esc_html__( 'Display', 'grimlock-the-events-calendar' ) ) );
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
			Kirki::add_panel( 'grimlock_the_events_calendar_tribe_events_customizer_panel', array(
				'priority' => 140,
				'title'    => esc_html__( 'The Events Calendar', 'grimlock-the-events-calendar' ),
			) );

			Kirki::add_section( $this->section, apply_filters( "{$this->section}_args", array(
				'title'    => $this->title,
				'priority' => isset( $args['priority'] ) ? $args['priority'] :  10,
				'panel'    => 'grimlock_the_events_calendar_tribe_events_customizer_panel',
			) ) );
		}
	}

	/**
	 * Add a Kirki text field to set the title in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_title_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'text',
				'label'             => esc_html__( 'Title', 'grimlock-the-events-calendar' ),
				'section'           => $this->section,
				'settings'          => 'the_events_calendar_title',
				'default'           => $this->get_default( 'the_events_calendar_title' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_the_events_calendar_customizer_title_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki textarea field to set the description in the Customizer.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	protected function add_description_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'              => 'textarea',
				'label'             => esc_html__( 'Description', 'grimlock-the-events-calendar' ),
				'section'           => $this->section,
				'settings'          => 'the_events_calendar_description',
				'default'           => $this->get_default( 'the_events_calendar_description' ),
				'priority'          => 10,
				'sanitize_callback' => 'wp_kses_post',
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_the_events_calendar_customizer_description_field_args', $args ) );
		}
	}

	/**
	 * Add a Kirki image field to set the background image for the Custom Header in the Customizer.
	 *
	 * @since 1.0.2
	 *
	 * @param array $args The array of arguments for the Kirki field.
	 */
	protected function add_custom_header_background_image_field( $args = array() ) {
		if ( class_exists( 'Kirki' ) ) {
			$args = wp_parse_args( $args, array(
				'type'     => 'image',
				'section'  => $this->section,
				'label'    => esc_html__( 'Header image', 'grimlock-the-events-calendar' ),
				'settings' => 'the_events_calendar_custom_header_background_image',
				'default'  => $this->get_default( 'the_events_calendar_custom_header_background_image' ),
				'priority' => 10,
			) );

			Kirki::add_field( 'grimlock', apply_filters( 'grimlock_the_events_calendar_customizer_background_image_field_args', $args ) );
		}
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
	public function add_custom_header_args( $args ) {
		$args = parent::add_custom_header_args( $args );

		if ( $this->is_template() ) {

			if ( is_tax() ) {
				$args['title']    = single_term_title( '', false );
				$args['subtitle'] = term_description();
			}
			else {
				$args['title']    = $this->get_theme_mod( 'the_events_calendar_title' );
				$args['subtitle'] = $this->get_theme_mod( 'the_events_calendar_description' );
			}

			$args['background_image'] = $this->get_theme_mod( 'the_events_calendar_custom_header_background_image' );

		}

		return $args;
	}

	/**
	 * Check whether Custom Header has to be displayed.
	 *
	 * @since 1.0.3
	 *
	 * @return bool True when Custom Header has to be displayed, false otherwise.
	 */
	public function has_custom_header_displayed( $default ) {
		if ( $this->is_template() ) {
			return true == $this->get_theme_mod( 'the_events_calendar_custom_header_displayed' );
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
		$is_template = ( function_exists( 'tribe_is_events_home' ) && tribe_is_events_home() ) || // Events home
		               ( function_exists( 'tribe_is_event_category' ) && tribe_is_event_category() ) || // Events category
		               ( function_exists( 'tribe_is_month' ) && tribe_is_month() && ! is_tax() ) || // Month View Page
		               ( function_exists( 'tribe_is_month' ) && tribe_is_month() && is_tax() ) || // Month View Category Page
		               ( function_exists( 'tribe_is_past' ) && tribe_is_past() || function_exists( 'tribe_is_upcoming' ) && tribe_is_upcoming() && ! is_tax() ) || // List View Page
		               ( function_exists( 'tribe_is_past' ) && tribe_is_past() || function_exists( 'tribe_is_upcoming' ) && tribe_is_upcoming() && is_tax() ) || // List View Category Page
		               ( function_exists( 'tribe_is_week' ) && tribe_is_week() && ! is_tax() ) || // Week View Page
		               ( function_exists( 'tribe_is_week' ) && tribe_is_week() && is_tax() ) || // Week View Category Page
		               ( function_exists( 'tribe_is_day' ) && tribe_is_day() && ! is_tax() ) || // Day View Page
		               ( function_exists( 'tribe_is_day' ) && tribe_is_day() && is_tax() ) || // Day View Category Page
		               ( function_exists( 'tribe_is_map' ) && tribe_is_map() && ! is_tax() ) || // Map View Page
		               ( function_exists( 'tribe_is_map' ) && tribe_is_map() && is_tax() ) || // Map View Category Page
		               ( function_exists( 'tribe_is_photo' ) && tribe_is_photo() && ! is_tax() ) || // Photo View Page
		               ( function_exists( 'tribe_is_photo' ) && tribe_is_photo() && is_tax() ) || // Photo View Category Page
		               ( function_exists( 'tribe_is_showing_all' ) && tribe_is_showing_all() ); // All events in a recurrence

		return apply_filters( 'grimlock_the_events_calendar_customizer_is_template', $is_template );
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
	 * Disinherit single customizer settings
	 *
	 * @param bool $default True if we are on a default single page
	 *
	 * @return bool
	 */
	public function single_customizer_is_template( $default ) {
		// The page showing all events in a recurrence is considered as a single by WP, so we disinherit the single options for that specific case
		return $default && ! ( function_exists( 'tribe_is_showing_all' ) && tribe_is_showing_all() );
	}

	/**
	 * Return the custom header background image to use on events templates
	 *
	 * @param string $background_image Background image url
	 *
	 * @return string Modified background image url
	 */
	public function custom_header_background_image( $background_image ) {
		return $this->get_theme_mod( 'the_events_calendar_custom_header_background_image' );
	}

	/**
	 * Add a link in the description pointing to the job listing archive vertical padding field.
	 *
	 * @since 1.1.5
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_padding_y_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#the_events_calendar_custom_header_padding_y" rel="tc-control">Events Page</a>', 'grimlock-the-events-calendar' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
	}

	/**
	 * Add a link in the description pointing to the job listing archive layout field.
	 *
	 * @since 1.1.5
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_layout_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#the_events_calendar_custom_header_layout" rel="tc-control">Events Page</a>', 'grimlock-the-events-calendar' ), array(
			'a' => array(
				'href' => array(),
				'rel'  => array(),
			),
		) );
		return $args;
	}

	/**
	 * Add a link in the description pointing to the job listing archive container layout field.
	 *
	 * @since 1.1.5
	 *
	 * @param  array $args The array of arguments for the Kirki field.
	 *
	 * @return array       The updated array of arguments for the Kirki field.
	 */
	public function add_custom_header_customizer_container_layout_field_description( $args ) {
		$args['description'] .= wp_kses( __( ', <a href="#the_events_calendar_custom_header_container_layout" rel="tc-control">Events Page</a>', 'grimlock-the-events-calendar' ), array(
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
                        var previewUrl = '<?php echo esc_js( trailingslashit( tribe_get_events_link() ) ); ?>';
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

return new Grimlock_The_Events_Calendar_Archive_Tribe_Events_Customizer();
