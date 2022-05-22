<?php
/**
 * Plugin Name: Knowledge Base for Documents and FAQs
 * Plugin URI: https://www.echoknowledgebase.com
 * Description: Create knowledgebase docs and FAQs easily, works with any theme.
 * Version: 9.3.0
 * Author: Echo Plugins
 * Author URI: https://www.echoknowledgebase.com
 * Text Domain: echo-knowledge-base
 * Domain Path: /languages
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Knowledge Base for Documents and FAQs is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Knowledge Base for Documents and FAQs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Knowledge Base for Documents and FAQs. If not, see <http://www.gnu.org/licenses/>.
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'EPKB_PLUGIN_NAME' ) ) {
	define( 'EPKB_PLUGIN_NAME', 'Echo Knowledge Base' );
}

if ( ! class_exists( 'Echo_Knowledge_Base' ) && ! epkb_is_amag_conflict() ) :

/**
 * Main class to load the plugin.
 *
 * Singleton
 */
final class Echo_Knowledge_Base {

	/* @var Echo_Knowledge_Base */
	private static $instance;

	public static $version = '9.3.0';
	public static $plugin_dir;
	public static $plugin_url;
	public static $plugin_file = __FILE__;
	public static $needs_min_add_on_version = array( 'LAY' => '1.2.1', 'MKB' => '1.10.0', 'RTD' => '1.0.0', 'IDG' => '1.0.0', 'BLK' => '1.0.0',
													 'SEA' => '1.0.0', 'PRF' => '1.0.0', 'PIE' => '1.0.0', 'ART' => '1.0.0' );

	/* @var EPKB_KB_Config_DB */
	public $kb_config_obj;

	/* @var EPKB_Settings_DB */
	public $settings_obj;

	/**
	 * Initialise the plugin
	 */
	private function __construct() {
		self::$plugin_dir = plugin_dir_path(  __FILE__ );
		self::$plugin_url = plugin_dir_url( __FILE__ );
	}

	/**
	 * Retrieve or create a new instance of this main class (avoid global vars)
	 *
	 * @static
	 * @return Echo_Knowledge_Base
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Echo_Knowledge_Base ) ) {
			self::$instance = new Echo_Knowledge_Base();

			self::$instance->setup_system();
			self::$instance->setup_plugin();

			add_action( 'plugins_loaded', array( self::$instance, 'load_text_domain' ), 11 );
		}
		return self::$instance;
	}

	/**
	 * Setup class auto-loading and other support functions. Setup custom core features.
	 */
	private function setup_system() {

		// autoload classes ONLY when needed by executed code rather than on every page request
		require_once self::$plugin_dir . 'includes/system/class-epkb-autoloader.php';

		// register settings
		self::$instance->kb_config_obj = new EPKB_KB_Config_DB();
		self::$instance->settings_obj = new EPKB_Settings_DB();

		// load non-classes
		require_once self::$plugin_dir . 'includes/system/plugin-setup.php';
		require_once self::$plugin_dir . 'includes/system/scripts-registration.php';
		require_once self::$plugin_dir . 'includes/system/plugin-links.php';

		add_action( 'init', array( self::$instance, 'epkb_stop_heartbeat' ), 1 );

		new EPKB_Upgrades();

		// setup custom core features
		new EPKB_Articles_CPT_Setup();
		new EPKB_Articles_Admin();

		// subscribe to category actions create/edit/delete including for REST requests in Gutenberg
		new EPKB_Categories_Admin();
	}

	/**
	 * Setup plugin before it runs. Include functions and instantiate classes based on user action
	 */
	private function setup_plugin() {

		$action = EPKB_Utilities::get( 'action' );

		// process action request if any
		if ( ! empty( $action ) ) {
			$this->handle_action_request( $action );
		}

		// handle AJAX front & back-end requests (no admin, no admin bar)
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->handle_ajax_requests( $action );
			return;
		}

		// ADMIN or CLI
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {	// || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
            if ( $this->is_kb_plugin_active_for_network( 'echo-knowledge-base/echo-knowledge-base.php' ) ) {
                add_action( 'plugins_loaded', array( self::$instance, 'setup_backend_classes' ), 11 );
            } else {
                $this->setup_backend_classes();
            }
			return;
		}

		// catch saving and creating of Post in Gutenberg
		if ( ! empty( $_SERVER['HTTP_REFERER'] ) && ( strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/post.php' ) !== false || strpos( $_SERVER['HTTP_REFERER'], '/wp-admin/post-new.php' ) !== false ) ) {
			require_once self::$plugin_dir . 'includes/admin/admin-functions.php';
		}

		// FRONT-END (no ajax, possibly admin bar)
		new EPKB_Layouts_Setup();      // KB Main page shortcode, list of themes
		new EPKB_Articles_Setup();
		new EPKB_Templates();
		new EPKB_Shortcodes();
	}

	/**
	 * Handle plugin actions here such as saving settings
	 * @param $action
	 */
	private function handle_action_request( $action ) {

		if ( $action == 'eckb_apply_editor_changes' ) {
			new EPKB_Editor_Controller();
			return;
		}
		
		if ( $action == 'epkb_load_editor' ) {
			new EPKB_Editor_View();
			return;
		}

		if ( $action == 'epkb_download_debug_info' ) {
			new EPKB_Settings_Controller();
			return;
		}

		if ( empty( $action ) || ! EPKB_KB_Handler::is_kb_request() ) {
			return;
		}
	}

	/**
	 * Handle AJAX requests coming from front-end and back-end
	 * @param $action
	 */
	private function handle_ajax_requests( $action ) {

        if ( empty( $action ) ) {
            return;
        }

		if ( $action == 'epkb-search-kb' ) {  // user searching KB
			new EPKB_KB_Search();
			return;
		} else if ( in_array( $action, array( 'epkb_toggle_debug', 'epkb_enable_advanced_search_debug' ) ) ) {
			new EPKB_Settings_Controller();
			return;
		} else if ( in_array( $action, array( 'epkb_get_wizard_template', 'epkb_apply_wizard_changes', 'epkb_wizard_update_order_view') ) ) {
			new EPKB_KB_Wizard_Cntrl();
			return;
		} else if ( in_array( $action, array( 'epkb_apply_setup_wizard_changes', 'epkb_report_admin_error' ) ) ) {
			new EPKB_KB_Wizard_Setup();
			return;
		} else if ( in_array( $action, array( EPKB_Need_Help_Features::FEATURES_TAB_VISITED_ACTION ) ) ) {
			new EPKB_Need_Help_Features();
			return;
		} else if ( in_array( $action, array( 'epkb_editor_backend_mode', 'epkb_wpml_enable', 'epkb_save_access_control', 'epkb_update_kb_name', 'epkb_create_kb_demo_data' ) ) ) {
			new EPKB_Configuration_Controller();
			return;
		}
		
		if ( $action == 'add-tag' ) {
			new EPKB_KB_Config_Category();
			return;
		}

		if ( $action == 'epkb_dismiss_ongoing_notice' ) {
			new EPKB_Admin_Notices( true );
			return;
		}

		if ( $action == 'epkb_editor_error' || $action == 'eckb_editor_get_themes_list' ) {
			new EPKB_Editor_Controller();
			return;
		}

		if ( $action == 'epkb_deactivate_feedback' ) {
			new EPKB_Deactivate_Feedback();
			return;
		}

		if ( in_array( $action, array( 'epkb_load_articles_list', 'epkb_convert_kb_content' ) )  ) {
			new EPKB_Convert_Ctrl();
			return;
		}

		if ( $action == 'epkb_update_the_content_flag' ) {
			new EPKB_Articles_Setup();
			return;
		}
	}

	/**
	 * Setup up classes when on ADMIN pages
	 */
	public function setup_backend_classes() {
		global $pagenow;

		$is_kb_request = EPKB_KB_Handler::is_kb_request();
		$request_page = empty($_REQUEST['page']) ? '' : EPKB_Utilities::request_key( 'page' );
		$admin_pages = [ 'post.php', 'edit.php', 'post-new.php', 'edit-tags.php', 'term.php' ];

		// show KB notice on our pages or when potential KB Main Page is being edited
		if ( $is_kb_request && in_array( $pagenow, $admin_pages ) ) {
			new EPKB_Admin_Notices();
		}

		// article new page
		if ( $is_kb_request && $pagenow == 'post-new.php' ) {
			add_action( 'admin_enqueue_scripts', 'epkb_load_admin_article_page_styles' );
		}

		// article edit page - include scripts to show categories box
		if ( $pagenow == 'post.php' && ! empty( $_REQUEST['post'] ) && ! empty( $_REQUEST['action'] ) && $_REQUEST['action'] == 'edit' ) {
			$kb_post_type = get_post_type( $_REQUEST['post'] );
			if ( EPKB_KB_Handler::is_kb_post_type( $kb_post_type ) ) {
				add_action( 'admin_enqueue_scripts', 'epkb_load_admin_article_page_styles' );
			}
		}

		// include our admin scripts on our admin pages (submenus of KB menu) but not on Edit/Add page due to blocks etc.
		if ( $is_kb_request && $pagenow != 'post-new.php' && $pagenow != 'post.php' ) {

			// KB Configuration Page
			if ( $request_page == 'epkb-kb-configuration' ) {

				// Setup Wizard
				if ( isset( $_GET['setup-wizard-on'] ) ) {
					add_action( 'admin_enqueue_scripts', 'epkb_load_admin_kb_setup_wizard_script' );
				} else {
					add_action( 'admin_enqueue_scripts', 'epkb_load_admin_kb_wizards_script' );
					add_action( 'admin_enqueue_scripts', 'epkb_load_admin_plugin_pages_resources' );
				}

				// KB Config page needs front-page CSS resources
				add_action('admin_enqueue_scripts', 'epkb_kb_config_load_public_css');

			// KB Admin Pages (not config)
			} else {
				add_action('admin_enqueue_scripts', 'epkb_load_admin_kb_old_design_script');  // to support old design pages
				add_action( 'admin_enqueue_scripts', 'epkb_load_admin_plugin_pages_resources' );
			}
		}

		// on Category page show category icon selection feature
		if ( $is_kb_request && ( $pagenow == 'term.php' || $pagenow == 'edit-tags.php' ) ) {
			new EPKB_KB_Config_Category();
		}

		// admin core classes
		require_once self::$plugin_dir . 'includes/admin/admin-menu.php';
		require_once self::$plugin_dir . 'includes/admin/admin-functions.php';

		if ( ! empty( $pagenow ) && in_array( $pagenow, [ 'plugins.php', 'plugins-network.php' ] ) ) {
			new EPKB_Deactivate_Feedback();
		}
	}

	/**
	/**
	 * Loads the plugin language files from ./languages directory.
	 */
	public function load_text_domain() {
		load_plugin_textdomain( 'echo-knowledge-base', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	// Don't allow this singleton to be cloned.
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}

	// Don't allow un-serializing of the class except when testing
	public function __wakeup() {
		if ( strpos($GLOBALS['argv'][0], 'phpunit') === false ) {
			_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
		}
	}

	/**
	 * When developing and debugging we don't need heartbeat
	 */
	public function epkb_stop_heartbeat() {
		if ( defined( 'RUNTIME_ENVIRONMENT' ) && RUNTIME_ENVIRONMENT == 'ECHODEV' ) {
			wp_deregister_script( 'heartbeat' );
			// EPKB_Utilities::save_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, true, true );
		}
	}

    private function is_kb_plugin_active_for_network( $plugin ) {
        if ( ! is_multisite() ) {
            return false;
        }

        $plugins = get_site_option( 'active_sitewide_plugins' );
        if ( isset( $plugins[ $plugin ] ) ) {
            return true;
        }

        return false;
    }
}

/**
 * Returns the single instance of this class
 *
 * @return Echo_Knowledge_Base - this class instance
 */
function epkb_get_instance() {
	return Echo_Knowledge_Base::instance();
}
epkb_get_instance();

endif; // end class_exists() check

function epkb_is_amag_conflict() {
	/** @var $wpdb Wpdb */
	global $wpdb;
	$table = $wpdb->prefix . 'am'.'gr_kb_groups';
	$result = $wpdb->get_var( "SHOW TABLES LIKE '" . $table ."'" );
	return ( ! empty($result) && ( $table == $result ) );
}
