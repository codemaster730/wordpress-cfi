<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_The_Events_Calendar
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-the-events-calendar
 */
class Grimlock_The_Events_Calendar {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-the-events-calendar', false, 'grimlock-the-events-calendar/languages' );

		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/grimlock-the-events-calendar-template-functions.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/grimlock-the-events-calendar-template-hooks.php';

		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-archive-tribe-events-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-single-tribe-events-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-single-tribe-venue-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-single-tribe-organizer-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-archive-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-button-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-control-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-navigation-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-table-customizer.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-the-events-calendar-typography-customizer.php';

		// Initialize widgets.
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-the-events-calendar-tribe-events-section-widget.php';
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-the-events-calendar-tribe-events-section-widget-fields.php';

		// Initialize blocks.
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-the-events-calendar-tribe-events-section-block.php';

		add_action( 'widgets_init',          array( $this, 'widgets_init'          ), 10 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10 );
		add_action( 'wp_enqueue_scripts',    array( $this, 'enqueue_scripts'       ), 10 );

		add_filter( 'tribe_events_views_v2_view_breakpoints',    array( $this, 'change_tribe_events_breakpoints'          ), 100, 2 );

		add_filter( 'tribe_events_template_paths',               array( $this, 'change_tribe_events_template_paths'       ), 10    );
		add_filter( 'tribe_template_path_list',                  array( $this, 'change_tribe_template_path_list'          ), 10    );
		add_action( 'tribe_events_single_event_before_the_meta', array( 'Tribe__Events__iCal', 'single_event_links'       ), 100   );
		add_filter( 'tribe_display_settings_tab_fields',         array( $this, 'change_tribe_display_settings_tab_fields' ), 100   );
		add_action( 'init',                                      array( $this, 'refresh_tec_context_cache'                ), 100   );

		// Initialize components.
		require_once GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-the-events-calendar-tribe-events-component.php';
		add_action( 'grimlock_query_tribe_events', array( $this, 'query_tribe_events' ), 10, 1 );
		add_action( 'grimlock_modal_tribe_events', array( $this, 'modal_tribe_events' ), 10, 1 );
	}

	/**
	 * Register the custom widgets.
	 *
	 * @since 1.0.0
	 */
	public function widgets_init() {
		register_widget( 'Grimlock_The_Events_Calendar_Tribe_Events_Section_Widget' );
	}

	/**
	 * Enqueue scripts and stylesheets in admin pages for the widgets.
	 *
	 * @since 1.0.0
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'grimlock-the-events-calendar-widgets', GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/js/widgets.js', array( 'jquery', 'jquery-ui-datepicker' ), GRIMLOCK_THE_EVENTS_CALENDAR_VERSION, true );
	}

	/**
	 * Enqueue scripts
	 */
	public function enqueue_scripts() {
		if ( function_exists( 'tribe_events_views_v2_is_enabled' ) && tribe_events_views_v2_is_enabled() ) {
			wp_enqueue_style( 'grimlock-the-events-calendar', GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/css/style-v2.css', array(), GRIMLOCK_THE_EVENTS_CALENDAR_VERSION );
		}
		else {
			wp_enqueue_style( 'grimlock-the-events-calendar', GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_URL . 'assets/css/style-v1.css', array(), GRIMLOCK_THE_EVENTS_CALENDAR_VERSION );
		}

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-the-events-calendar', 'rtl', 'replace' );
	}


	/**
	 * Modify the plugin views breakpoints to match Grimlock breakpoints
	 *
	 * @since 1.2.0
	 */
	public function change_tribe_events_breakpoints( $breakpoints, $view ) {
		$breakpoints['xsmall'] = 546;
		$breakpoints['medium'] = 738;
		$breakpoints['full']   = 932;
		$breakpoints['xlarge'] = 1140;

		return $breakpoints;
	}


	/**
	 * Modify the template hierarchy to locate them in the "templates" folder of this plugin if not overridden in the theme
	 *
	 * @param array $template_paths The array of paths to look into
	 *
	 * @return array The updated array of paths to look into
	 *
	 * @since 1.1.1
	 */
	public function change_tribe_events_template_paths( $template_paths ) {
		if ( function_exists( 'tribe_events_views_v2_is_enabled' ) && tribe_events_views_v2_is_enabled() ) {
			array_unshift( $template_paths, GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'templates/v2/' );
		}
		else {
			array_unshift( $template_paths, GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'templates/v1/' );
		}

		return $template_paths;
	}

	/**
	 * Modify the template hierarchy to locate them in the "templates" folder of this plugin if not overridden in the theme
	 * (For "v2" TEC templates)
	 *
	 * @param array $folders The array of folders to look into
	 *
	 * @return array The updated array of folders to look into
	 *
	 * @since 1.2.0
	 */
	public function change_tribe_template_path_list( $folders ) {
		if ( function_exists( 'tribe_events_views_v2_is_enabled' ) && tribe_events_views_v2_is_enabled() ) {
			$folders['grimlock-the-events-calendar'] = array(
				'id'       => 'grimlock-the-events-calendar',
				'priority' => 15,
				'path'     => GRIMLOCK_THE_EVENTS_CALENDAR_PLUGIN_DIR_PATH . 'templates/v2/src/views/v2',
			);
		}

		return $folders;
	}

	/**
	 * Disable some TEC display options that are being forced for maximum compatibility
	 *
	 * @param array $display_settings Array of options in the "Display" tab of TEC settings
	 *
	 * @return array Modified array of options in the "Display" tab of TEC settings
	 */
	public function change_tribe_display_settings_tab_fields( $display_settings ) {
		unset( $display_settings['stylesheet_mode'] );
		unset( $display_settings['stylesheetOption'] );
		unset( $display_settings['tribeEventsTemplate'] );
		return $display_settings;
	}

	/**
	 * Force refresh TEC context cache to fix some compatibility issues with other plugins (e.g. ECS)
	 */
	public function refresh_tec_context_cache() {
		tribe_context()->refresh( 'view' );
	}

	/**
	 * Display the event component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	public function query_tribe_events( $args = array() ) {
		$component = new Grimlock_The_Events_Calendar_Tribe_Events_Component( apply_filters( 'grimlock_query_tribe_events_args', $args ) );
		$component->render();
	}

	/**
	 * Display the event modal component.
	 */
	public function modal_tribe_events() {
		tribe_get_view( 'single-event' );
	}
}
