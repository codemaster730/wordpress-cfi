<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BP_Member_Swipe
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package bp-member-swipe/inc
 */
class BP_Member_Swipe {

	public $bp_user_query_random_seed;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'bp-member-swipe', false, 'bp-member-swipe/languages' );

		add_action( 'bp_init',                               array( $this, 'register_bp_template_stack' ), 5  );
		add_action( 'wp_enqueue_scripts',                    array( $this, 'register_scripts'           ), 9  );
		add_action( 'wp_enqueue_scripts',                    array( $this, 'enqueue_scripts'            ), 10 );

		add_action( 'wp_ajax_load_member_swipe_page',        array( $this, 'load_member_swipe_page'     ), 10 );
		add_action( 'wp_ajax_nopriv_load_member_swipe_page', array( $this, 'load_member_swipe_page'     ), 10 );

		add_filter( 'bp_user_query_uid_clauses',             array( $this, 'change_bp_user_query_sql'   ), 10, 2 );

		// Initialize shortcodes.
		require_once BP_MEMBER_SWIPE_PLUGIN_DIR_PATH . 'inc/shortcode/class-bp-member-swipe-directory-shortcode.php';

		// Initialize Gutenberg blocks
		require_once BP_MEMBER_SWIPE_PLUGIN_DIR_PATH . 'inc/block/class-bp-member-swipe-directory-block.php';
	}

	/**
	 * Register a new template location in the BuddyPress template stack
	 */
	public function register_bp_template_stack() {
		bp_register_template_stack( array( $this, 'get_bp_templates_location' ), 20 );
	}

	/**
	 * Get the BuddyPress templates location in the plugin
	 *
	 * @return string
	 */
	public function get_bp_templates_location() {
		return BP_MEMBER_SWIPE_PLUGIN_DIR_PATH . 'templates/';
	}

	/**
	 * Register scripts and styles on the init hook to be able to enqueue them from any place after (e.g. in the Gutenberg block)
	 */
	public function register_scripts() {
		// Register swiper
		if ( ! wp_script_is( 'swiper', 'registered' ) ) {
			wp_register_script( 'swiper', BP_MEMBER_SWIPE_PLUGIN_DIR_URL . 'assets/js/vendor/swiper-bundle.min.js', array(), '6.0.4', true );
		}
		wp_register_style( 'swiper', BP_MEMBER_SWIPE_PLUGIN_DIR_URL . 'assets/css/vendor/swiper-bundle.min.css', array(), '6.0.4' );

		// Register directory script
		wp_register_script( 'bp-member-swipe-directory-swiper', BP_MEMBER_SWIPE_PLUGIN_DIR_URL . 'assets/js/directory-swiper.js', array( 'jquery', 'swiper' ), BP_MEMBER_SWIPE_VERSION, true );
		wp_localize_script( 'bp-member-swipe-directory-swiper', 'bp_member_swipe_directory_swiper', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
		) );
		wp_register_style( 'bp-member-swipe-directory-swiper', BP_MEMBER_SWIPE_PLUGIN_DIR_URL . 'assets/css/directory-swiper.css', array( 'swiper' ), BP_MEMBER_SWIPE_VERSION );

		// Load directory-rtl.css instead of directory.css for RTL compatibility
		wp_style_add_data( 'bp-member-swipe-directory-swiper', 'rtl', 'replace' );
	}

	/**
	 * Register/enqueue scripts and styles
	 */
	public function enqueue_scripts() {
		// Enqueue swiper style
		wp_enqueue_style( 'swiper' );

		// Enqueue swipe directory style
		wp_enqueue_style( 'bp-member-swipe-directory-swiper' );

		// Enqueue dashicons
		wp_enqueue_style( 'dashicons' );
	}

	/**
	 * Load more members for the Member Swipe page template.
	 *
	 * @since 1.0.0
	 */
	public function load_member_swipe_page() {
		if ( ! empty( $_POST['query_args'] ) ) {
			$query_args = $this->sanitize_bp_query_args( $_POST[ 'query_args' ] );

			if ( $query_args['type'] === 'random' && ! empty( $query_args['random_seed'] ) ) {
				$this->bp_user_query_random_seed = $query_args['random_seed'];
			}

			if ( bp_has_members( $query_args ) ) {
				ob_start();
				bp_get_template_part( 'members/members-swipe-loop-items' );
				$result = ob_get_clean();
				wp_send_json_success( $result );
			}
		}
		wp_die();
	}

	/**
	 * Sanitize array of arguments to be used in a BP query
	 *
	 * @param array $args The unsanitized array of args
	 *
	 * @return array The sanitized array of args
	 */
	private function sanitize_bp_query_args( $args ) {
		if ( empty( $args ) || ! is_array( $args ) ) {
			return array();
		}

		$args_list = array(
			'type'                => 'text',
			'random_seed'         => 'int',
			'page'                => 'int',
			'per_page'            => 'int',
			'max'                 => 'bool',
			'page_arg'            => 'text',
			'include'             => 'list',
			'exclude'             => 'list',
			'user_id'             => 'int',
			'member_type'         => 'text',
			'member_type__in'     => 'list',
			'member_type__not_in' => 'list',
			'search_terms'        => 'text',
			'meta_key'            => 'text',
			'meta_value'          => 'text',
			'populate_extras'     => 'bool'
		);

		$sanitized_args = array();

		foreach ( $args_list as $arg_name => $arg_type ) {
			if ( ! isset( $args[ $arg_name ] ) ) {
				continue;
			}

			switch ( $arg_type ) {
				case 'int':
					$sanitized_args[ $arg_name ] = intval( $args[ $arg_name ] );
					break;
				case 'bool':
					$sanitized_args[ $arg_name ] = boolval( $args[ $arg_name ] );
					break;
				case 'list':
					if ( is_string( $args[ $arg_name ] ) ) {
						$sanitized_args[ $arg_name ] = sanitize_text_field( $args[ $arg_name ] );
					}
					elseif ( is_array( $args[ $arg_name ] ) ) {
						$sanitized_args[ $arg_name ] = array();
						foreach ( $args[ $arg_name ] as $item ) {
							$sanitized_args[ $arg_name ][] = sanitize_text_field( strval( $item ) );
						}
					}
					else {
						$sanitized_args[ $arg_name ] = boolval( $args[ $arg_name ] );
					}
					break;
				case 'text':
				default:
					$sanitized_args[ $arg_name ] = sanitize_text_field( strval( $args[ $arg_name ] ) );
					break;
			}
		}

		return $sanitized_args;
	}

	/**
	 * Change BP_User_Query sql
	 *
	 * @param array $sql
	 * @param BP_User_Query $bp_user_query
	 *
	 * @return array
	 */
	public function change_bp_user_query_sql( $sql, $bp_user_query ) {
		if ( $bp_user_query->query_vars['type'] === 'random' && ! empty( $this->bp_user_query_random_seed ) ) {
			$sql['orderby'] = "ORDER BY rand({$this->bp_user_query_random_seed})";
			$this->bp_user_query_random_seed = false;
		}

		return $sql;
	}

	/**
	 * Fired on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {}

	/**
	 * Fired on plugin deactivation.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {}
}