<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_BuddyPress
 *
 * @author  themoasaurus
 * @since   1.0.0
 * @package grimlock-buddypress/inc
 */
class Grimlock_BuddyPress {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-buddypress', false, 'grimlock-buddypress/languages' );

		add_action( 'bp_init', array( $this, 'register_bp_template_stack' ), 5 );

		add_action( 'after_setup_theme', array( $this, 'register_navbar_nav_menus' ), 10 );

		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-buddypress-groups-section-component.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-buddypress-navbar-nav-menu-component.php';

		add_action( 'grimlock_buddypress_groups_section',  array( $this, 'groups_section'              ), 10,  1 );
		add_action( 'grimlock_buddypress_navbar_nav_menu', array( $this, 'navbar_nav_menu'             ), 10,  1 );
		add_action( 'grimlock_navbar_nav_menu',            array( $this, 'add_navbar_nav_menu'         ), 110, 1 );
		add_action( 'grimlock_vertical_navbar_nav_menu',   array( $this, 'add_navbar_nav_menu'         ), 10,  1 );
		add_action( 'grimlock_hamburger_navbar_nav_menu',  array( $this, 'add_navbar_nav_menu'         ), 10,  1 );
		add_filter( 'grimlock_custom_header_displayed',    array( $this, 'has_custom_header_displayed' ), 10,  1 );

		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/widget/class-grimlock-buddypress-groups-section-widget.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/widget/fields/class-grimlock-buddypress-groups-section-widget-fields.php';

		add_action( 'widgets_init', array( $this, 'widgets_init' ), 10 );

		// Initialize blocks.
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/block/class-grimlock-buddypress-groups-section-block.php';

		// Initialize customizer classes
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-customizer.php';

		// Store this one in a global cause we need it somewhere else
		global $grimlock_buddypress_members_customizer;
		$grimlock_buddypress_members_customizer = require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-members-customizer.php';

		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-profile-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-groups-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-group-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-activities-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-archive-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-global-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-navigation-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-table-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-typography-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-button-customizer.php';
		require_once GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-buddypress-control-customizer.php';

		add_filter( 'theme_mod_navigation_position',      array( $this, 'force_navigation_position'                       ), 10    );
		add_action( 'wp_enqueue_scripts',                 array( $this, 'enqueue_scripts'                                 ), 10    );
		add_action( 'bp_enqueue_scripts',                 array( $this, 'bp_enqueue_scripts'                              ), 20    );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_controls_scripts'             ), 20    );
		add_action( 'admin_enqueue_scripts',              array( $this, 'admin_enqueue_scripts'                           ), 10    );
		add_action( 'enqueue_block_editor_assets',        array( $this, 'enqueue_block_editor_assets'                     ), 10    );
		add_action( 'after_setup_theme',                  array( $this, 'setup'                                           ), 10    );
		add_filter( 'bp_get_theme_package_id',            array( $this, 'bp_get_theme_package_id'                         ), 10    );
		add_filter( 'option__bp_theme_package_id',        array( $this, 'bp_get_theme_package_id'                         ), 10    );

		add_action( 'xprofile_field_after_contentbox', array( $this, 'add_profile_field_synced_with_author_bio_field'  ), 10, 1 );
		add_action( 'xprofile_fields_saved_field',     array( $this, 'save_profile_field_synced_with_author_bio_field' ), 10, 1 );
		add_action( 'xprofile_data_after_save',        array( $this, 'maybe_sync_author_bio_with_profile_field'        ), 10, 1 );
		add_action( 'xprofile_data_after_delete',      array( $this, 'maybe_sync_author_bio_with_profile_field'        ), 10, 1 );
		add_action( 'bp_core_signup_user',             array( $this, 'sync_author_bio_with_profile_field'              ), 10, 1 );
		add_action( 'bp_core_activated_user',          array( $this, 'sync_author_bio_with_profile_field'              ), 10, 1 );
		add_action( 'user_profile_update_errors',      array( $this, 'sync_profile_field_with_author_bio'              ), 10, 3 );
	}

	/**
	 * Register a new template location in the BuddyPress template stack
	 */
	public function register_bp_template_stack() {
		bp_register_template_stack( 'get_template_directory' );
		bp_register_template_stack( array( $this, 'get_bp_templates_location' ) );
	}

	/**
	 * Get the BuddyPress template location in the plugin
	 *
	 * @return string
	 */
	public function get_bp_templates_location() {
		return GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_PATH . 'templates/';
	}

	/**
	 * Check if the custom header is displayed or not.
	 *
	 * @since 1.0.5
	 *
	 * @return bool True if the custom header is displayed, false otherwise.
	 */
	public function has_custom_header_displayed( $default ) {
		return ! ( function_exists( 'is_buddypress' ) && is_buddypress() ) && $default;
	}

	/**
	 * Register nav menus for the Grimlock Navbar component.
	 *
	 * @since 1.0.0
	 */
	public function register_navbar_nav_menus() {
		register_nav_menus( apply_filters( 'grimlock_buddypress_nav_menus', array(
			'user_logged_in'  => esc_html__( 'Logged In Users', 'grimlock-buddypress' ),
			'user_logged_out' => esc_html__( 'Logged Out Users', 'grimlock-buddypress' ),
		) ) );
	}

	/**
	 * Add BuddyPress navbar cart for the Grimlock Navbar.
	 *
	 * @since 1.0.0
	 *
	 * @param $args
	 */
	public function add_navbar_nav_menu( $args ) {
		$class = isset( $args['menu_class'] ) ? str_replace( 'main-menu', 'profile', $args['menu_class'] ) : '';
		do_action( 'grimlock_buddypress_navbar_nav_menu', array(
			'class' => $class,
		) );
	}

	/**
	 * Display the Grimlock BuddyPress Cart Component for the Navbar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	public function navbar_nav_menu( $args = array() ) {
		$args      = apply_filters( 'grimlock_buddypress_navbar_nav_menu_args', wp_parse_args( $args, array(
			'id' => 'buddypress-navbar_nav_menu',
		) ) );
		$component = new Grimlock_BuddyPress_Navbar_Nav_Menu_Component( $args );
		$component->render();
	}

	/**
	 * Display the Grimlock BuddyPress Groups Section Component for the Widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	public function groups_section( $args = array() ) {
		$component = new Grimlock_BuddyPress_Groups_Section_Component( apply_filters( 'grimlock_buddypress_groups_section_args', $args ) );
		$component->render();
	}

	/**
	 * Register the custom widgets.
	 *
	 * @since 1.0.0
	 */
	public function widgets_init() {
		register_sidebar( apply_filters( 'grimlock_buddypress_sidebar_1_args', array(
			'id'            => 'bp-sidebar-1',
			'name'          => esc_html__( 'BuddyPress Left Sidebar', 'grimlock-buddypress' ),
			'description'   => esc_html__( 'The left hand area for BuddyPress pages.', 'grimlock-buddypress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) ) );

		register_sidebar( apply_filters( 'grimlock_buddypress_sidebar_args', array(
			'id'            => 'bp-sidebar',
			'name'          => esc_html__( 'BuddyPress', 'grimlock-buddypress' ),
			'description'   => esc_html__( 'The right hand area for BuddyPress pages.', 'grimlock-buddypress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) ) );
		
		if ( ! is_active_sidebar( 'bp-sidebar' ) ) {
			unregister_sidebar( 'bp-sidebar' );
			register_sidebar( apply_filters( 'grimlock_buddypress_sidebar_2_args', array(
				'id'            => 'bp-sidebar-2',
				'name'          => esc_html__( 'BuddyPress Right Sidebar', 'grimlock-buddypress' ),
				'description'   => esc_html__( 'The right hand area for BuddyPress pages.', 'grimlock-buddypress' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) ) );
		}

		register_widget( 'Grimlock_BuddyPress_Groups_Section_Widget' );
	}

	/**
	 * Force the navigation position on BuddyPress pages.
	 *
	 * @param  string $position The navigation position.
	 *
	 * @return string           The updated navigation position.
	 */
	public function force_navigation_position( $position ) {
		if ( is_buddypress() ) {
			return 'classic-top';
		}
		return $position;
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style( 'grimlock-buddypress', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_BUDDYPRESS_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-buddypress', 'rtl', 'replace' );

		wp_enqueue_script( 'jquery-effects-drop' );
		wp_enqueue_script( 'hammerjs', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/js/vendor/hammer.min.js', array(), '2.0.7', true );

		wp_enqueue_script( 'grimlock-buddypress', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/js/main.js', array( 'jquery', 'jquery-effects-drop', 'hammerjs' ), GRIMLOCK_BUDDYPRESS_VERSION, true );
		wp_localize_script( 'grimlock-buddypress', 'grimlock_buddypress', array(
			'priority_nav_dropdown_breakpoint_label' => esc_html_x( 'Menu', 'bp_profile_menu_mobile_label', 'grimlock-buddypress' ),
			'notifications_list_empty'               => esc_html__( 'You have no new notification', 'grimlock-buddypress' ),
			'ajax_url'                               => admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * Enqueue BP scripts
	 */
	public function bp_enqueue_scripts() {
		if ( bp_is_messages_component() && bp_is_current_action( 'compose' ) ) {
			// Dequeue jquery.dimensions in compose view to fix compatibility bug
			wp_dequeue_script( 'bp-jquery-dimensions' );
		}
	}

	/**
	 * Enqueue customizer scripts.
	 *
	 * @since 1.4.2
	 */
	public function enqueue_customizer_controls_scripts() {
		wp_enqueue_script( 'grimlock-buddypress-customizer-controls', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/js/customizer-controls.js', array( 'customize-controls' ), GRIMLOCK_BUDDYPRESS_VERSION, true );
	}

	/**
	 * Enqueue scripts and stylesheets in admin pages for the widgets.
	 *
	 * @since 1.0.0
	 */

	public function admin_enqueue_scripts() {
		wp_enqueue_script( 'grimlock-buddypress-admin', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/js/admin.js', array( 'jquery' ), GRIMLOCK_BUDDYPRESS_VERSION, true );
	}

	/**
	 * Enqueue scripts and stylesheets in the block editor
	 *
	 * @since 1.3.10
	 */
	public function enqueue_block_editor_assets() {
		wp_enqueue_style( 'grimlock-buddypress-blocks', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . "assets/css/blocks-editor-styles.css", array( 'wp-edit-blocks', 'grimlock-blocks' ), GRIMLOCK_BUDDYPRESS_VERSION );
	}

	/**
	 * BuddyPress setup function.
	 *
	 * @since 1.0.0
	 */
	public function setup() {
		// Add theme support for BuddyPress Legacy template pack.
		add_theme_support( 'buddypress-use-legacy' );
	}

	/**
	 * Use theme support values to get current bp package id if they are defined
	 *
	 * @param string $package_id The current package id
	 *
	 * @return string The bp package id
	 */
	public function bp_get_theme_package_id( $package_id ) {
		if ( current_theme_supports( 'buddypress-use-legacy' ) ) {
			return 'legacy';
		}
		elseif ( current_theme_supports( 'buddypress-use-nouveau' ) ) {
			return 'nouveau';
		}

		return $package_id;
	}

	/**
	 * Add a checkbox in textarea fields settings to set whether the field should be synchronized with the author bio
	 *
	 * @param BP_XProfile_Field $xprofile_field The profile field object
	 */
	public function add_profile_field_synced_with_author_bio_field( $xprofile_field ) {
		$profile_field_synced_with_author_bio = get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 );
		$checked = ! empty( $xprofile_field->id ) && $profile_field_synced_with_author_bio == $xprofile_field->id;
		?>

		<div id="sync_with_bio_postbox" class="postbox" style="display: <?php echo esc_attr( $xprofile_field->type === 'textarea' ? 'block' : 'none' ); ?>;">
			<h2><?php esc_html_e( 'Biographical Info', 'grimlock-buddypress' ); ?></h2>
			<div class="inside" aria-live="polite" aria-atomic="true" aria-relevant="all">
				<label for="sync_with_bio">
					<input type="checkbox" name="sync_with_bio" id="sync_with_bio" <?php echo $checked ? esc_html( 'checked="checked"' ) : '' ?> />
					<?php esc_html_e( 'Synchronize this field with the author biography', 'grimlock-buddypress' ); ?>
				</label>
			</div>
		</div>

		<?php
	}

	/**
	 * Save whether a profile field should be synchronized with the author bio
	 *
	 * @param BP_XProfile_Field $xprofile_field The profile field object
	 */
	public function save_profile_field_synced_with_author_bio_field( $xprofile_field ) {
		$sync_current_field_with_bio = boolval( $_POST['sync_with_bio'] );

		$profile_field_synced_with_author_bio = get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 );

		// If the current field isn't a textarea but is somehow selected to be synced with the bio, we just clear the option then return
		if ( $xprofile_field->type !== 'textarea' ) {
			if ( $profile_field_synced_with_author_bio == $xprofile_field->id ) {
				update_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 );
			}
			return;
		}

		// If the checkbox is checked, save this field id in the option
		if ( $sync_current_field_with_bio ) {
			update_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', $xprofile_field->id );

			// Sync all bios after saving the option
			$user_ids = get_users( array( 'fields' => 'ID' ) );
			foreach ( $user_ids as $user_id ) {
				if ( ! empty( xprofile_get_field_data( get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 ), $user_id ) ) ) {
					$this->sync_author_bio_with_profile_field( $user_id );
				}
			}
		}
		// If the checkbox is unchecked but was previously checked, we clear the option
		elseif ( $profile_field_synced_with_author_bio == $xprofile_field->id ) {
			update_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 );
		}
	}

	/**
	 * Update the author bio with the value of the profile field selected to be synced if there is one
	 *
	 * @param int $user_id The id of the user
	 */
	public function sync_author_bio_with_profile_field( $user_id = 0 ) {
		if ( empty( get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 ) ) || empty( $user_id ) ) {
			return;
		}

		$bio = xprofile_get_field_data( get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 ), $user_id );

		update_user_meta( $user_id, 'description', wp_kses_post( $bio ) );
	}

	/**
	 * Update the author bio when a profile field is updated if
	 * that profile field was selected to be synced with the author bio.
	 *
	 * @param BP_XProfile_ProfileData $data The xprofile data object containing information about the updated profile field
	 */
	public function maybe_sync_author_bio_with_profile_field( $data ) {
		if ( $data->field_id == get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 ) ) {
			$this->sync_author_bio_with_profile_field( $data->user_id );
		}
	}

	/**
	 * Update the profile field selected to be synced with the author bio when the bio changes
	 *
	 * @param object $errors Array of errors. Passed by reference.
	 * @param bool   $update Whether or not being updated.
	 * @param object $user   User object whose profile is being synced. Passed by reference.
	 */
	public function sync_profile_field_with_author_bio( &$errors, $update, &$user ) {
		if ( empty( get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 ) ) || ! $update || $errors->get_error_codes() ) {
			return;
		}

		xprofile_set_field_data( get_option( 'grimlock_buddypress_profile_field_synced_with_author_bio', 0 ), $user->ID, $user->description );
	}
}
