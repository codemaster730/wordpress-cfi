<?php

/**
 * Various KB Core utility functions
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Core_Utilities {

	/**
	 * Retrieve a KB article with security checks
	 *
	 * @param $post_id
	 * @return null|WP_Post - return null if this is NOT KB post
	 */
	public static function get_kb_post_secure( $post_id ) {

		if ( empty($post_id) ) {
			return null;
		}

		// ensure post_id is valid
		$post_id = EPKB_Utilities::sanitize_int( $post_id );
		if ( empty($post_id) ) {
			return null;
		}

		// retrieve the post and ensure it is one
		$post = get_post( $post_id );
		if ( empty($post) || ! is_object($post) || ! $post instanceof WP_Post ) {
			return null;
		}

		// verify it is a KB article
		if ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) ) {
			return null;
		}

		return $post;
	}

	/**
	 * Retrieve KB ID.
	 *
	 * @param WP_Post $post
	 * @return int|NULL on ERROR
	 */
	public static function get_kb_id( $post=null ) {
		global $eckb_kb_id;

		$kb_id = '';
		$post = $post === null ? get_post() : $post;
		if ( ! empty($post) && $post instanceof WP_Post ) {
			$kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type );
		}

		$kb_id = empty($kb_id) || is_wp_error($kb_id) ? ( empty($eckb_kb_id) ? '' : $eckb_kb_id ) : $kb_id;
		if ( empty($kb_id) ) {
			EPKB_Logging::add_log("KB ID not found", $kb_id);
			return null;
		}

		return $kb_id;
	}

	/**
	 * Verify kb id is number and is an existing KB ID
	 * @param $kb_id
	 * @return int
	 */
	public static function sanitize_kb_id( $kb_id ) {
		$kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids();
		$kb_id = EPKB_Utilities::sanitize_int( $kb_id, EPKB_KB_Config_DB::DEFAULT_KB_ID );
		return in_array( $kb_id, $kb_ids ) ? $kb_id : EPKB_KB_Config_DB::DEFAULT_KB_ID;
	}

	public static function is_run_setup_wizard_first_time() {

		$kb_main_pages = epkb_get_instance()->kb_config_obj->get_value( EPKB_KB_Config_DB::DEFAULT_KB_ID, 'kb_main_pages' );

		// not null if demo KB not yet created after installation
		$run_setup = EPKB_Utilities::get_wp_option( 'epkb_run_setup', null );

		return empty( $kb_main_pages ) && $run_setup !== null;
	}

	/**
	 * Handle submission of admin error
	 */
	public static function handle_report_admin_error() {
		global $wp_version;

		// die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die();

		$first_version = get_option( 'epkb_version_first' );
		$active_theme = wp_get_theme();
		$theme_info = $active_theme->get( 'Name' ) . ' ' . $active_theme->get( 'Version' );

		$email = EPKB_Utilities::post( 'email', '[Email name is missing]', 'email', 50 );
		$first_name = EPKB_Utilities::post( 'first_name' );
		$first_name = empty($first_name) ? '[First name is missing]' : substr( $first_name, 0, 30 );

		$error = EPKB_Utilities::post( 'admin_error' );
		$error = empty($error) ? '[Error details are missing]' : substr( $error, 0, 5000 );

		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );

		// send feedback
		$api_params = array(
			'epkb_action' => 'epkb_report_error',
			'plugin_name' => EPKB_Utilities::is_amag_on() ? 'Access Manager' : 'EPKB',
			'plugin_version' => class_exists( 'Echo_Knowledge_Base' ) ? Echo_Knowledge_Base::$version : 'N/A',
			'first_version' => empty( $first_version ) ? 'N/A' : $first_version,
			'wp_version' => $wp_version,
			'theme_info' => $theme_info,
			'email' => $email,
			'first_name' => $first_name,
			'editor_error' => $error,
			'kb_main_page' => $kb_main_page_url
		);

		// Call the API
		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout' => 15,
				'body' => $api_params,
				'sslverify' => false
			)
		);

		// let user know if it succeeded
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			wp_send_json_success( esc_html__( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' ) );
		} else {
			wp_send_json_error( EPKB_Utilities::report_generic_error( 0, esc_html__( 'Error occurred', 'echo-knowledge-base' ) ) );
		}
	}

	/**
	 * Merge core KB config with add-ons KB specs
	 *
	 * @param $kb_id
	 *
	 * @return array|false
	 */
	public static function retrieve_all_kb_specs( $kb_id ) {

		$feature_specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );

		// get add-on configuration from user changes if applicable
		$add_on_specs = apply_filters( 'epkb_add_on_config_specs', array() );
		if ( ! is_array( $add_on_specs ) || is_wp_error( $add_on_specs ) ) {
			return false;
		}

		// merge core and add-on specs
		return array_merge( $add_on_specs, $feature_specs );
	}

	/**
	 * Get list of archived KBs
	 *
	 * @return array
	 */
	public static function get_archived_kbs() {
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		$archived_kbs = [];
		foreach ( $all_kb_configs as $one_kb_config ) {
			if ( $one_kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && self::is_kb_archived( $one_kb_config['status'] ) ) {
				$archived_kbs[] = $one_kb_config;
			}
		}
		return $archived_kbs;
	}

	/**
	 * For given Main Page, retrieve its slug by passed page ID
	 *
	 * @param $kb_main_page_id
	 *
	 * @return string
	 */
	public static function get_main_page_slug( $kb_main_page_id ) {

		$kb_page = get_post( $kb_main_page_id );
		if ( empty( $kb_page ) ) {
			return '';
		}

		$slug = urldecode( sanitize_title_with_dashes( $kb_page->post_name, '', 'save' ) );
		$ancestors = get_post_ancestors( $kb_page );
		foreach ( $ancestors as $ancestor_id ) {
			$post_ancestor = get_post( $ancestor_id );
			if ( empty( $post_ancestor ) ) {
				continue;
			}
			$slug = urldecode( sanitize_title_with_dashes( $post_ancestor->post_name, '', 'save' ) ) . '/' . $slug;
			if ( $kb_main_page_id == $ancestor_id ) {
				break;
			}
		}

		return $slug;
	}

	/**
	 * For given Main Page, retrieve its slug by passed page object
	 *
	 * @param $kb_main_page
	 * @return string
	 */
	public static function get_main_page_slug_by_obj( $kb_main_page ) {

		if ( empty( $kb_main_page ) || empty( $kb_main_page->post_name ) ) {
			return '';
		}

		$slug = urldecode( sanitize_title_with_dashes( $kb_main_page->post_name, '', 'save' ) );
		$ancestors = get_post_ancestors( $kb_main_page );
		foreach ( $ancestors as $ancestor_id ) {
			$post_ancestor = get_post( $ancestor_id );
			if ( empty( $post_ancestor ) ) {
				continue;
			}
			$slug = urldecode( sanitize_title_with_dashes( $post_ancestor->post_name, '', 'save' ) ) . '/' . $slug;
			if ( $kb_main_page->ID == $ancestor_id ) {
				break;
			}
		}

		return $slug;
	}

	public static function is_kb_main_page() {
		global $eckb_is_kb_main_page;
		$ix = (isset($eckb_is_kb_main_page) && $eckb_is_kb_main_page) || EPKB_Utilities::get('is_kb_main_page') == 1 ? 'mp' : 'ap';
		return $ix == 'mp';
	}

	/**
	 * Check if KB is ARCHIVED.
	 *
	 * @param $kb_status
	 * @return bool
	 */
	public static function is_kb_archived( $kb_status ) {
		return $kb_status === 'archived';
	}

	/**
	 * Detect whether the frontend Editor can be shown
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function is_frontend_editor_hidden( $kb_config ) {
		$issues_found = '';

		if ( $kb_config['article-structure-version'] == 'version-1' ) {
			$issues_found .= 'Before accessing the frontend Editor, we need to update your setup to use our new article HTML structure. ' .
			                 'You might need to make adjustments to your article page after the update. Would you like to proceed? ';
		}

		if ( class_exists('Echo_Elegant_Layouts') && version_compare(Echo_Elegant_Layouts::$version, '2.6.0', '<') ) {
			$issues_found .= 'Please upgrade Elegant Layouts plugin to the 2.6.0 version before accessing the frontend Editor. ';  // do not translate
		}

		if ( class_exists('Echo_Advanced_Search') && version_compare(Echo_Advanced_Search::$version, '2.14.0', '<') ) {
			$issues_found .= 'Please upgrade Advanced Search plugin to the 2.14.0 version before accessing the frontend Editor. ';
		}

		if ( class_exists('Echo_Article_Rating_And_Feedback') && version_compare(Echo_Article_Rating_And_Feedback::$version, '1.4.0', '<') ) {
			$issues_found .= 'Please upgrade Article Rating & Feedback plugin to the 1.4.0 version before accessing the frontend Editor. ';
		}

		if ( class_exists('Echo_Widgets') && version_compare(Echo_Widgets::$version, '1.9.0', '<') ) {
			$issues_found .= 'Please upgrade KB Widgets plugin to the 1.9.0 version before accessing the frontend Editor. ';
		}

		return $issues_found;
	}

	/**
	 * Detect whether the backend Visual Editor can be shown
	 *
	 * @return string
	 */
	public static function is_backend_editor_hidden() {
		$issues_found = '';

		if ( class_exists('Echo_Elegant_Layouts') && version_compare(Echo_Elegant_Layouts::$version, '2.10.0', '<') ) {
			$issues_found .= 'Please upgrade Elegant Layouts plugin to the 2.10.0 version before accessing the Backend visual Editor. ' . '<br>'; // do not translate
		}

		if ( class_exists('Echo_Advanced_Search') && version_compare(Echo_Advanced_Search::$version, '2.26.0', '<') ) {
			$issues_found .= 'Please upgrade Advanced Search plugin to the 2.26.0 version before accessing the Backend visual Editor. ' . '<br>';
		}

		if ( class_exists('Echo_Article_Rating_And_Feedback') && version_compare(Echo_Article_Rating_And_Feedback::$version, '1.8.0', '<') ) {
			$issues_found .= 'Please upgrade Article Rating & Feedback plugin to the 1.8.0 version before accessing the Backend visual Editor. ' . '<br>';
		}

		if ( class_exists('Echo_Widgets') && version_compare(Echo_Widgets::$version, '1.10.2', '<') ) {
			$issues_found .= 'Please upgrade KB Widgets plugin to the 1.10.2 version before accessing the Backend visual Editor. ' . '<br>';
		}

		return $issues_found;
	}


	/**************************************************************************************************************************
	 *
	 *                     CATEGORIES
	 *
	 *************************************************************************************************************************/

	/**
	 *
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Get all existing KB categories.
	 *
	 * @param $kb_id
	 * @param string $order_by
	 * @return array|null - return array of KB categories (empty if not found) or null on error
	 */
	public static function get_kb_categories_unfiltered( $kb_id, $order_by='name' ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		$order = $order_by == 'name' ? 'ASC' : 'DESC';
		$order_by = $order_by == 'date' ? 'term_id' : $order_by;   // terms don't have date so use id
		$kb_category_taxonomy_name = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$result = $wpdb->get_results( $wpdb->prepare("SELECT t.*, tt.*
												   FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
												   WHERE tt.taxonomy IN (%s) ORDER BY " . esc_sql('t.' . $order_by) . ' ' . $order . ' ', $kb_category_taxonomy_name ) );
		return isset($result) && is_array($result) ? $result : null;
	}

	/**
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Get KB Article categories.
	 *
	 * @param $kb_id
	 * @param $article_id
	 * @return array|null - categories belonging to the given KB Article or null on error
	 */
	public static function get_article_categories_unfiltered( $kb_id, $article_id ) {
		/** @var $wpdb Wpdb */
		global $wpdb;

		if ( empty($article_id) ) {
			return null;
		}

		// get article categories
		$post_taxonomy_objs = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $wpdb->term_taxonomy
																	 WHERE taxonomy = '%s' and term_taxonomy_id in
																	(SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = %d) ",
			EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ), $article_id ) );
		if ( ! empty($wpdb->last_error) ) {
			return null;
		}

		return $post_taxonomy_objs === null || ! is_array($post_taxonomy_objs) ? array() : $post_taxonomy_objs;
	}

	/**
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Retrieve KB Category.
	 *
	 * @param $kb_id
	 * @param $kb_category_id
	 * @return WP_Term|false
	 */
	public static function get_kb_category_unfiltered( $kb_id, $kb_category_id ) {
		$term = get_term_by('id', $kb_category_id, EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) );
		if ( empty($term) || ! $term instanceof WP_Term ) {
			EPKB_Logging::add_log( "Category is not KB Category: " . $kb_category_id . " (35)", $kb_id );
			return false;
		}

		return $term;
	}

	/**
	 * Retrieve KB Flag value.
	 *
	 * @param $flag_name
	 * @return true|false
	 */

	public static function is_kb_flag( $flag_name ) {
		$kb_flags = EPKB_Utilities::get_wp_option( 'epkb_flags', [], true );

		$kb_flags = is_array( $kb_flags ) ? $kb_flags : [];

		return in_array( $flag_name, $kb_flags );
	}

	/**
	 * Update KB Flag value.
	 * Return true if the value was changed, false if not, WP_Error if something went wrong
	 *
	 * @param $flag_name
	 * @param $add_flag
	 *
	 * @return mixed|WP_Error
	 */

	public static function update_kb_flag( $flag_name, $add_flag = true ) {

		$kb_flags = EPKB_Utilities::get_wp_option( 'epkb_flags', [], true );

		// need value true and already true
		if ( $add_flag && in_array( $flag_name, $kb_flags ) ) {
			return false;
		}

		// need false and already false
		if ( empty( $add_flag ) && ! in_array( $flag_name, $kb_flags ) ) {
			return false;
		}

		// need false but true
		if ( empty( $add_flag ) && in_array( $flag_name, $kb_flags ) ) {
			$kb_flags = array_diff( $kb_flags, [ $flag_name ] );
		}

		// need true but false
		if ( $add_flag && ! in_array( $flag_name, $kb_flags ) ) {
			array_push( $kb_flags, $flag_name );
		}

		$result = EPKB_Utilities::save_wp_option( 'epkb_flags', $kb_flags, true );
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return true;
	}

	/**
	 * Return sales page for given plugin
	 *
	 * @param $plugin_name
	 * @return String
	 */
	public static function get_plugin_sales_page( $plugin_name ) {
		switch( $plugin_name ) {
			case 'amgr':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/';
			case 'kblk':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/';
			case 'elay':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/';
			case 'eprf':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/';
			case 'asea':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/';
			case 'widg':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/widgets/';
			case 'amcr':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/custom-roles/';
			case 'amgp':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/kb-groups/';
			case 'emkb':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/';
			case 'epie':
				return 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/';
			case 'crel':
				return 'https://wordpress.org/plugins/creative-addons-for-elementor/?utm_source=wp-repo&utm_medium=link&utm_campaign=readme';
			case 'ep'.'hd':
				return 'https://wordpress.org/plugins/help-dialog/?utm_source=wp-repo&utm_medium=link&utm_campaign=readme';
		}

		return 'https://www.echoknowledgebase.com/wordpress-add-ons/';
	}


	/********************************************************************************
	 *
	 *                                   VARIOUS
	 *
	 ********************************************************************************/

	/**
	 * Get link to the current KB main page
	 *
	 * @param $kb_config
	 * @param $link_text
	 * @param string $link_class
	 * @return string
	 */
	public static function get_current_kb_main_page_link( $kb_config, $link_text, $link_class='' ) {

		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
		if ( empty( $link_output ) ) {
			return false;
		}
		return '<a href="' . $link_output . '" target="_blank" class="' . $link_class . '">' . $link_text . '</a>';
	}

	/**
	 * Get link to KB admin page
	 *
	 * @param $url_param
	 * @param $label_text
	 * @param bool $target_blank
	 * @return string
	 */
	public static function get_kb_admin_page_link( $url_param, $label_text, $target_blank=true, $css_class='' ) {
		return '<a class="epkb-kb__wizard-link ' .$css_class. '" href="' . admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Handler::get_current_kb_id() ) .
		                                                             ( empty($url_param) ? '' : '&' ) . $url_param ) . '"' . ( empty( $target_blank ) ? '' : ' target="_blank"' ) . '>' . $label_text . '</a>';
	}

	/**
	 * Show list of KBs.
	 *
	 * @param $kb_config
	 * @param array $contexts
	 */
	public static function admin_list_of_kbs( $kb_config, $contexts=[] ) {    ?>

		<select id="epkb-list-of-kbs" data-active-kb-id="<?php echo $kb_config['id']; ?>">      <?php

			$found_archived_kbs = false;
			$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
			foreach ( $all_kb_configs as $one_kb_config ) {

				$one_kb_id = $one_kb_config['id'];

				// Do not show archived KBs
				if ( $one_kb_id !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Core_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
					$found_archived_kbs = true;
					continue;
				}

				// Do not render the KB into the dropdown if the current user does not have at least minimum required capability (covers KB Groups)
				$required_capability = EPKB_Admin_UI_Access::get_author_capability( $one_kb_id );
				if ( ! current_user_can( $required_capability ) ) {
					continue;
				}

				// Redirect to All Articles page if the user does not have access for the current page for this KB in drop down
				$redirect_url = '';
				if ( ! empty( $contexts ) ) {
					$required_capability = EPKB_Admin_UI_Access::get_context_required_capability( $contexts, $one_kb_config );
					if ( ! current_user_can( $required_capability ) ) {
						$redirect_url = admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $one_kb_id ) );
					}
				}

				$kb_name = $one_kb_config[ 'kb_name' ];
				$active = ( $kb_config['id'] == $one_kb_id && ! isset( $_GET['archived-kbs'] ) ? 'selected' : '' );   ?>

				<option data-plugin="core" value="<?php echo empty( $redirect_url ) ? esc_attr( $one_kb_id ) : 'closed'; ?>"<?php echo empty( $redirect_url ) ? '' : ' data-target="' . esc_url( $redirect_url ) . '"'; ?> <?php echo $active; ?>><?php
					esc_html_e( $kb_name ); ?>
				</option>      <?php
			}

			if ( $found_archived_kbs && EPKB_Utilities::post( 'page' ) == 'epkb-kb-configuration' ) {    ?>
				<option data-plugin="core" value="archived"<?php echo isset( $_GET['archived-kbs'] ) ? ' selected' : ''; ?>><?php _e( 'View Archived KBs', 'echo-knowledge-base' ); ?></option>  <?php
			}

			if ( ! EPKB_Utilities::is_multiple_kbs_enabled() && count($all_kb_configs) == 1 ) {     ?>
				<option data-plugin="core" data-link="https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/"><?php _e( 'Get Additional Knowledge Bases', 'echo-knowledge-base' ); ?></option>  <?php
			}

			// Hook to add new options to the admin header dropdown
			if ( current_user_can( EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY ) ) {
				do_action( 'eckb_kb_admin_header_dropdown' );
			}   ?>

		</select>   <?php
	}

	public static function get_nav_sidebar_type( $kb_config, $side ) {

		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );

		// nav_sidebar_left is set to '' if not updated for old installations
		$is_nav_initialized = isset( $sidebar_priority['nav_sidebar_left'] ) && $sidebar_priority['nav_sidebar_left'] != '';

		// new nav sidebar system
		if ( $is_nav_initialized ) {

			// nav sidebar is set to Not displayed
			if ( $side == 'left' && $sidebar_priority['nav_sidebar_left'] == '0' ) {
				return 'eckb-nav-sidebar-none';
			}

			if ( $side == 'right' && $sidebar_priority['nav_sidebar_right'] == '0' ) {
				return 'eckb-nav-sidebar-none';
			}

			return $side == 'left' ? $kb_config['article_nav_sidebar_type_left'] : $kb_config['article_nav_sidebar_type_right'];
		}

		// following is previous sidebars
		$type = 'eckb-nav-sidebar-none';
		$nav_side = '';
		$priority = '0';

		// Categories Focused Layout is enabled
		if ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME ) {

			$type = 'eckb-nav-sidebar-categories';
			$categories_left = empty( $sidebar_priority['categories_left'] ) ? '' : $sidebar_priority['categories_left'];
			$categories_right = empty( $sidebar_priority['categories_right'] ) ? '' : $sidebar_priority['categories_right'];
			$nav_side = empty( $categories_left ) ? ( empty( $categories_right ) ? 'left' : 'right' ) : 'left';
			$priority = $nav_side == 'left' ? $categories_left : $categories_right;

		// Elegant Layouts enabled
		} else if ( EPKB_Utilities::is_elegant_layouts_enabled() ) {

			$type = 'eckb-nav-sidebar-v1';
			$nav_side = empty( $sidebar_priority['elay_sidebar_left'] ) ? '' : 'left';

			// ensure Sidebar Layout always have some sidebar
			if ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT ) {
				$nav_side = 'left';
				$kb_config['article_nav_sidebar_type_left'] = 'eckb-nav-sidebar-v1';
			}

			$priority = $nav_side == 'left' ? ( empty( $sidebar_priority['elay_sidebar_left'] ) ? '0' : $sidebar_priority['elay_sidebar_left'] ) : '0';
		}

		$kb_config['article_nav_sidebar_type_left'] = $nav_side == 'left' ? $type : 'eckb-nav-sidebar-none';
		$sidebar_priority['nav_sidebar_left'] = $nav_side == 'left' ? ( empty( $priority ) ? '1' : $priority ) : '0';

		$kb_config['article_nav_sidebar_type_right'] = $nav_side == 'right' ? $type : 'eckb-nav-sidebar-none';
		$sidebar_priority['nav_sidebar_right'] = $nav_side == 'right' ? ( empty( $priority ) ? '1' : $priority ) : '0';

		$kb_config['article_sidebar_component_priority'] = $sidebar_priority;

		epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_config['id'], $kb_config );

		return $side == 'left' ? $kb_config['article_nav_sidebar_type_left'] : $kb_config['article_nav_sidebar_type_right'];
	}

	public static function get_nav_sidebar_priority( $kb_config, $side ) {

		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );

		$nav_left_priority = empty( $sidebar_priority['nav_sidebar_left'] ) ? '0' : $sidebar_priority['nav_sidebar_left'];
		$nav_right_priority = empty( $sidebar_priority['nav_sidebar_right'] ) ? '0' : $sidebar_priority['nav_sidebar_right'];

		return $side == 'left' ? $nav_left_priority : $nav_right_priority;
	}
}
