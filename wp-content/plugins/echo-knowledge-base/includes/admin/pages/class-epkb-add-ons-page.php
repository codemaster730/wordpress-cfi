<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Add-ons page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Add_Ons_Page {

	/**
	 * Get menu item title
	 *
	 * @return string
	 */
	public static function get_menu_item_title() {
		return '<span style="color:#5cb85c;">' . __( 'Add-ons / News', 'echo-knowledge-base' ) . '</span>';
	}

	/**
	 * Display add-ons page
	 */
	public function display_add_ons_page() {

		$admin_page_views = self::get_regular_views_config();

		EPKB_HTML_Admin::admin_page_css_missing_message();   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap">

			<div class="epkb-add-ons-page-container">   <?php

				/**
				 * ADMIN HEADER (KB logo and list of KBs dropdown)
				 */
				EPKB_HTML_Admin::admin_header( [], [], 'logo' );

				/**
				 * ADMIN TOOLBAR
				 */
				EPKB_HTML_Admin::admin_toolbar( $admin_page_views );

				/**
				 * ADMIN SECONDARY TABS
				 */
				EPKB_HTML_Admin::admin_secondary_tabs( $admin_page_views );

				/**
				 * LIST OF SETTINGS IN TABS
				 */
				EPKB_HTML_Admin::admin_settings_tab_content( $admin_page_views );   ?>

			</div>

		</div>      <?php
	}

	private static function add_on_product( $values = array () ) {    ?>

		<div id="<?php echo $values['id']; ?>" class="add_on_product">
			<div class="top_heading">
				<h3><?php esc_html_e($values['title']); ?></h3>
				<p><i><?php esc_html_e($values['special_note']); ?></i></p>
			</div>
			<div class="featured_img">
				<img src="<?php echo $values['img']; ?>">
			</div>
			<div class="description">
				<p>
					<?php echo wp_kses_post($values['desc']); ?>
				</p>
			</div>
			<div class="button_container">				<?php
				if ( ! empty($values['coming_when']) ) { ?>
					<div class="coming_soon"><?php esc_html_e( $values['coming_when'] ); ?></div>				<?php
				} else {        ?>
					<a class="epkb-primary-btn" href="<?php echo $values['learn_more_url']; ?>" target="_blank"><?php _e( 'Learn More', 'echo-knowledge-base' ); ?></a>				<?php
				}       ?>
			</div>

		</div>    <?php
	}

	/**
	 * Display Debug page.
	 * @return false|string
	 */
	private static function display_debug_info() {

		$is_debug_on = EPKB_Utilities::get_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, false );
		$heading = $is_debug_on ? esc_html__( 'Debug Information:', 'echo-knowledge-base' ) : '';

		ob_start();     ?>

		<div id="epkb_debug_info_tab_page">

			<section class="save-settings">    <?php
				$button_text = $is_debug_on ? __('Disable Debug', 'echo-knowledge-base') : __( 'Enable Debug', 'echo-knowledge-base' );
				EPKB_HTML_Elements::submit_button_v2( $button_text, 'epkb_toggle_debug', 'epkb_toggle_debug','' ,true ,'' ,'epkb-primary-btn' ); ?>
			</section>  <?php

			if ( EPKB_Utilities::is_advanced_search_enabled() ) {   ?>
				<section class="save-settings">    <?php
					$button_text = __( 'Enable Advanced Search Debug', 'echo-knowledge-base' );
					EPKB_HTML_Elements::submit_button_v2( $button_text, 'epkb_enable_advanced_search_debug', 'epkb_enable_advanced_search_debug','' ,true ,'' ,'epkb-primary-btn' ); ?>
				</section>  <?php
			}   ?>

			<section>
				<h3><?php echo $heading; ?></h3>
			</section>     <?php

			if ( $is_debug_on ) {
				echo self::display_debug_data();        ?>

				<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-add-ons' ) ); ?>" method="post" dir="ltr">                    <?php
					EPKB_HTML_Elements::checkbox( [
		                    'name'  => 'epkb_show_full_debug',
		                    'label' => esc_html__( 'Output full debug information (after instructed by support staff)', 'echo-knowledge-base' ),
		                    'input_class' => 'epkb-checkbox-input',
		                    'input_group_class' => 'epkb-input-group',
	                    ] ); ?>

                    <section style="padding-top: 20px;" class="save-settings checkbox-input"><?php
	                    EPKB_HTML_Elements::submit_button_v2( __( 'Download System Information', 'echo-knowledge-base' ), 'epkb_download_debug_info', 'epkb_download_debug_info', '', true, '' , 'epkb-primary-btn' ); ?>
					</section>
				</form>     <?php
			}    ?>

			<div id='epkb-ajax-in-progress-debug-switch' style="display:none;">
				<?php esc_html_e( 'Switching debug... ', 'echo-knowledge-base' ); ?><img class="epkb-ajax waiting" style="height: 30px;"
			                                                                         src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
			</div>

		</div>      		<?php

		return ob_get_clean();
	}

	public static function display_debug_data() {
		/** @var $wpdb Wpdb */
		global $wpdb;

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			return __( 'No access', 'echo-knowledge-base' );
		}

		$epkb_version_first = EPKB_Utilities::get_wp_option( 'epkb_version_first', 'N/A' );
		$epkb_version = EPKB_Utilities::get_wp_option( 'epkb_version', 'N/A' );

		$output = '<textarea rows="30" cols="150" style="overflow:scroll;">';

		// display KB configuration
		$output .= "KB Configurations:\n";
		$output .= "==================\n";
		$output .= "KB first version: " . $epkb_version_first . "\n";
		$output .= "KB version: " . $epkb_version . "\n\n\n";

		// display PHP and WP settings
		$output .= self::get_system_info();

		// retrieve KB config directly from the database
		$all_kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids();
		foreach ( $all_kb_ids as $kb_id ) {

			// retrieve specific KB configuration
			$kb_config = $wpdb->get_var("SELECT option_value FROM $wpdb->options WHERE option_name = '" . EPKB_KB_Config_DB::KB_CONFIG_PREFIX . $kb_id . "'" );
			if ( ! empty($kb_config) ) {
				$kb_config = maybe_unserialize( $kb_config );
			}

			// with WPML we need to trigger hook to have configuration names translated
			if ( EPKB_Utilities::is_wpml_enabled( $kb_config ) ) {
				$output .= "WPML Enabled---------- for KB ID " . $kb_id . "\n";
				$kb_config = get_option( EPKB_KB_Config_DB::KB_CONFIG_PREFIX . $kb_id );
			}

			// if KB configuration is missing then return error
			if ( empty($kb_config) || ! is_array($kb_config) ) {
				$output .= "Did not find KB configuration (DB231) for KB ID " . $kb_id . "\n";
				continue;
			}

			if ( count($kb_config) < 100 ) {
				$output .= "Found KB configuration is incomplete with only " . count($kb_config) . " items.\n";
			}

			$output .= 'KB Config ' . $kb_id . "\n\n";
			$specs = EPKB_KB_Config_Specs::get_fields_specification( $kb_id );
			$output .= '- KB URL  => ' . EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config ) . "\n";
			foreach( $kb_config as $name => $value ) {

				if ( ! isset( $_POST['epkb_show_full_debug'] ) && ! in_array($name, array('id','kb_main_pages','kb_name','kb_articles_common_path','article-structure-version','categories_in_url_enabled',
											'templates_for_kb', 'wpml_is_enabled', 'kb_main_page_layout', 'kb_article_page_layout')) ) {
					continue;
				}

				if ( is_array($value) ) {
					$value = EPKB_Utilities::get_variable_string($value);
				}
				$label = empty($specs[$name]['label']) ? 'unknown' : $specs[$name]['label'];
				$output .= '- ' . $label . ' [' . $name . ']' . ' => ' . $value . "\n";
			}

			// other configuration - not needed yet
			//$output .= "\nArticles Sequence:\n\n";
			//$output .= EPKB_Utilities::get_variable_string( EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Articles_Admin::KB_ARTICLES_SEQ_META, array(), true ) );

			$output .= "\n\n";
		}

		// display error logs
		$output .= "\n\nERROR LOG:\n";
		$output .= "==========\n";
		$logs = EPKB_Logging::get_logs();
		foreach( $logs as $log ) {
			$output .= empty($log['plugin']) ? '' : $log['plugin'] . " ";
			$output .= empty($log['kb']) ? '' : $log['kb'] . " ";
			$output .= empty($log['date']) ? '' : $log['date'] . "\n";
			$output .= empty($log['message']) ? '' : $log['message'] . "\n";
			$output .= empty($log['trace']) ? '' : $log['trace'] . "\n\n";
		}

		/* future if needed foreach( $eckb_log_messages as $eckb_log_message ) {
			$output .= $eckb_log_message[0] . ' - ' . $eckb_log_message[1] . ' - ' . $eckb_log_message[2] . "\n";
		} */

		// retrieve add-on data
		$add_on_output = apply_filters( 'eckb_add_on_debug_data', '' );
		$output .= is_string($add_on_output) ? $add_on_output : '';

		$output .= '</textarea>';

		return $output;
	}

	/**
	 * Based on EDD system-info.php file
	 * @return string
	 */
	private static function get_system_info() {
		/** @var $wpdb Wpdb */
		global $wpdb;

		$host = defined( 'WPE_APIKEY' ) ? "Host: WP Engine" : '<unknown>';
		/** @var $theme_data WP_Theme */
		$theme_data = wp_get_theme();
		/** @noinspection PhpUndefinedFieldInspection */
		$theme = $theme_data->Name . ' ' . $theme_data->Version;

		ob_start();     ?>

		PHP and WordPress Information:
		==============================

		Multisite:                <?php echo is_multisite() ? 'Yes' . "\n" : 'No' . "\n" ?>

		SITE_URL:                 <?php echo site_url() . "\n"; ?>
		HOME_URL:                 <?php echo home_url() . "\n"; ?>

		WordPress Version:        <?php echo get_bloginfo( 'version' ) . "\n"; ?>
		Permalink Structure:      <?php echo get_option( 'permalink_structure' ) . "\n"; ?>
		Active Theme:             <?php echo $theme . "\n"; ?>
		Host:                     <?php echo $host . "\n"; ?>
		WP Memory Limit:          <?php echo size_format( (int) WP_MEMORY_LIMIT * 1048576 ) . "\n"; ?>
		PHP Version:              <?php echo PHP_VERSION . "\n"; ?>

		PHP Post Max Size:        <?php echo ini_get( 'post_max_size' ) . "\n"; ?>
		PHP Upload Max File Size:  <?php echo ini_get( 'upload_max_filesize' ) . "\n"; ?>
		PHP Time Limit:           <?php echo ini_get( 'max_execution_time' ) . "\n"; ?>
		PHP Max Input Vars:       <?php echo ini_get( 'max_input_vars' ) . "\n"; ?>
		WP_DEBUG:                 <?php echo defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' . "\n" : 'Disabled' . "\n" : 'Not set' . "\n" ?>

		WP Table Prefix:          <?php echo "Length: ". strlen( $wpdb->prefix );

		/* $params = array(
			'sslverify'		=> false,
			'timeout'		=> 60,
			'user-agent'	=> 'EDD/' . EDD_VERSION,
			'body'			=> '_notify-validate'
		);

		$response = wp_remote_post( 'https://www.paypal.com/cgi-bin/webscr', $params );
		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
			$WP_REMOTE_POST =  'wp_remote_post() works' . "\n";
		} else {
			$WP_REMOTE_POST =  'wp_remote_post() does not work' . "\n";
		}		?>

		WP Remote Post:           <?php echo $WP_REMOTE_POST; ?> */  ?>

		DISPLAY ERRORS:           <?php echo ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A'; ?><?php echo "\n"; ?>
		FSOCKOPEN:                <?php echo ( function_exists( 'fsockopen' ) ) ? 'Your server supports fsockopen.' : 'Your server does not support fsockopen.'; ?><?php echo "\n"; ?>
		cURL:                     <?php echo ( function_exists( 'curl_init' ) ) ? 'Your server supports cURL:' : 'Your server does not support cURL.'; ?><?php echo "\n";

									if ( function_exists( 'curl_init' ) ) {
										$curl_values = curl_version();
										echo "\n\t\t\t\tVersion: " . $curl_values["version"];
										echo "\n\t\t\t\tSSL Version: " . $curl_values["ssl_version"];
										echo "\n\t\t\t\tLib Version: " . $curl_values["libz_version"] . "\n";
									}		?>

		SOAP Client:              <?php echo ( class_exists( 'SoapClient' ) ) ? 'Your server has the SOAP Client enabled.' : 'Your server does not have the SOAP Client enabled.'; ?><?php echo "\n";

		$plugins = get_plugins();
		$active_plugins = get_option( 'active_plugins', array() );

		$kb_plugins = array(
				'KB - Article Rating and Feedback',
				'KB - Links Editor','Articles Import and Export',
				'KB - Multiple Knowledge Bases','KB - Widgets',
				'Knowledge Base for Documents and FAQs',
				'KB - Elegant Layouts',
				'KB - Advanced Search',
				'Knowledge Base with Access Manager',
				'KB - Custom Roles',
				'KB Groups',
				'Blocks for Documents, Articles and FAQs',
				'Creative Addons for Elementor' );

		echo "\n\n";
		echo "KB PLUGINS:	         \n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			if ( in_array($plugin['Name'], $kb_plugins)) {
				echo "		" . $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
			}
		}

		echo "\n\n";
		echo "OTHER PLUGINS:	         \n\n";

		foreach ( $plugins as $plugin_path => $plugin ) {
			// If the plugin isn't active, don't show it.
			if ( ! in_array( $plugin_path, $active_plugins ) )
				continue;

			if ( ! in_array($plugin['Name'], $kb_plugins)) {
				echo "		" . $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
			}
		}

		if ( is_multisite() ) {		?>
			NETWORK ACTIVE PLUGINS:		<?php  echo "\n";

			$plugins = wp_get_active_network_plugins();
			$active_plugins = get_site_option( 'active_sitewide_plugins', array() );

			foreach ( $plugins as $plugin_path ) {
				$plugin_base = plugin_basename( $plugin_path );

				// If the plugin isn't active, don't show it.
				if ( ! array_key_exists( $plugin_base, $active_plugins ) ) {
					continue;
				}

				$plugin = get_plugin_data( $plugin_path );

				echo "		" . $plugin['Name'] . ': ' . $plugin['Version'] ."\n";
			}
		}

		return ob_get_clean();
	}

	/**
	 * Show Add-ons box
	 *
	 * @return false|string
	 */
	private static function show_addons_box() {

		ob_start();     ?>

		<div class="add_on_container">      <?php

			// http://www.echoknowledgebase.com/wp-content/uploads/2017/09/product_preview_coming_soon.png

			self::add_on_product( array(
				'id'                => 'epkb-add-on-bundle',
				'title'             => __( 'Add-on Bundle', 'echo-knowledge-base' ),
				'special_note'      => __( 'Save money with bundle discount', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/add-on-bundle-2.jpg',
				'desc'              => __( 'Save up to 50% when buying multiple add-ons together.', 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/bundle-pricing/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=bundle',
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Elegant Layouts', 'echo-knowledge-base' ),
				'special_note'      => __( 'More ways to design your KB', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-EL'.'AY-1.1.jpg',
				'desc'              => sprintf( __( 'Use %sGrid Layout%s or %sSidebar Layout%s for KB Main page or combine Basic, Tabs, Grid and Sidebar layouts in many cool ways.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=elegant-layouts',
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Multiple Knowledge Bases', 'echo-knowledge-base' ),
				'special_note'      => __( 'Expand your documentation', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-MKB-1.jpg',
				'desc'              => sprintf( __( 'Create a separate Knowledge Base for each %sproduct, service or team%s.', 'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/multiple-knowledge-bases/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=multiple-kbs'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Advanced Search', 'echo-knowledge-base' ),
				'special_note'      => __( 'Enhance and analyze user searches', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-AS'.'EA-1.jpg',
				'desc'              => __( "Enhance users' search experience and view search analytics, including popular searches and no results searches.", 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=advanced-search'
			) );

			/** TODO self::add_on_product( array(
			'id'                => '',
			'title'             => __( 'Article Features', 'echo-knowledge-base' ),
			'special_note'      => __( 'Includes article rating and article change notifications.', 'echo-knowledge-base' ),
			'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
			'desc'              => __( 'Current features: article rating with analytics, and email notifications for new or updated articles.', 'echo-knowledge-base' ),
			'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=article-rating'
			) ); */

			self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Widgets', 'echo-knowledge-base' ),
				'special_note'      => __( 'Shortcodes, Widgets, Sidebars', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-WI'.'DG-2.jpg',
				'desc'              => sprintf( __( 'Add KB Search, Most Recent Articles and other %sWidgets and shortcodes%s to your articles, sidebars and pages.',
					'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/widgets/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=widgets'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Links Editor for PDFs and More', 'echo-knowledge-base' ),
				'special_note'      => __( 'Link to PDFs, posts and pages', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-LINK-2.jpg',
				'desc'              => sprintf( __( 'Set Articles to links to %sPDFs, pages, posts and websites%s. On KB Main Page, choose icons for your articles.', 'echo-knowledge-base' ), '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=links-editor'
			) );

			self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Access Manager', 'echo-knowledge-base' ),
				'special_note'      => __( 'Protect your KB content', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-AM'.'GR-1.jpg',
				'desc'              => sprintf( __( 'Restrict your Articles to certain %sGroups%s using KB Categories. Assign users to specific %sKB Roles%s within Groups.', 'echo-knowledge-base' ), '<strong>', '</strong>', '<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=access-manager'
			) );

			/* self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Article Rating and Feedback', 'echo-knowledge-base' ),
				'special_note'      => __( 'Let users rate your articles', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
				'desc'              => sprintf( __( 'Let your readers rate the quality of your articles and submit insightful feedback. Utilize analytics on the most and least rated articles.', 'echo-knowledge-base' ), $i18_objects ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=article-rating'
			) ); */

			self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Migrate, Copy, Import and Export', 'echo-knowledge-base' ),
				'special_note'      => __( 'Import, export and copy Articles, images and more', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2022/01/KB-Import-Export-Banner-v2.jpg',
				'desc'              => __( "Powerful import and export plugin to migrate, create and copy articles and images from your Knowledge Base. You can also import articles from CSV and other sources.", 'echo-knowledge-base' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export//?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=kb-import-export/',
			) );

			/* TODO self::add_on_product( array(
				'id'                => '',
				'title'             => __( 'Help Dialog', 'echo-knowledge-base' ),
				'special_note'      => __( 'FAQs, Articles and Contact Form', 'echo-knowledge-base' ),
				'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2020/08/KB-Import-Export-Banner.jpg',
				'desc'              => sprintf( __( '%s Engage %s your website visitors and %s gain new customers %s with page-specific %s FAQs %s and %s knowledge base articles %s. Help users communicate with you ' .
										'%s without leaving the page %s by using a simple % scontact form %s shown with the Help Dialog.', 'echo-knowledge-base' ),
										'<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>','<strong>', '</strong>' ),
				'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export//?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=kb-import-export/',
			) ); */   ?>

		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Show Elementor plugin box
	 *
	 * @return false|string
	 */
	private static function show_elementor_plugin_box() {

		ob_start();     ?>

		<div class="epkb-features-container">   <?php
			EPKB_Add_Ons_Features::display_crel_features_details();    ?>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Show License boxes
	 *
	 * @param $license_content
	 * @return array[]
	 */
	private static function show_license_boxes( $license_content ) {

		ob_start();

		if ( ! empty( $license_content ) ) {    ?>
			<div class="add_on_container">
				<section id="ekcb-licenses">
					<ul>  	<!-- Add-on name / License input / status  -->   <?php
						echo $license_content;      ?>
					</ul>
				</section>
			</div>      <?php
		}

		$license_content = ob_get_clean();

		return array(

			// Box: Licenses
			array(
				'title' => __( 'Licenses for add-ons', 'echo-knowledge-base' ),
				'description' => self::get_licenses_box_description(),
				'html' => $license_content,
			)
		);
	}

	/**
	 * Get description for Licenses box
	 *
	 * @return string
	 */
	private static function get_licenses_box_description() {
		return sprintf( __( 'You can access your license account %s here%s' , 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/account-dashboard/" target="_blank" rel="noopener">', '</a>' ) .
				'<br />' . sprintf( __( 'Please refer to the %s documentation%s for help with your license account and any other issues.', 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/documentation/my-account-and-license-faqs/" target="_blank" rel="noopener">', '</a>');
	}

	/**
	 * Get configuration array for regular views
	 *
	 * @return array
	 */
	private static function get_regular_views_config() {

		$views_config = [];

		/**
		 * View: Add-ons
		 */
		$views_config[] = [

			// Shared
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
			'list_key' => 'add-ons',

			// Top Panel Item
			'label_text' => __( 'Add-ons', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cubes',

			// Boxes List
			'boxes_list' => array(
				array(
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
					'title' => __( 'Go Further With Add-ons', 'echo-knowledge-base' ),
					'html' => self::show_addons_box(),
				)
			),
		];

		/**
		 * View: Elementor Plugin
		 */
		if ( ! EPKB_Utilities::is_creative_addons_widgets_enabled() ) {
			$views_config[] = [

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
				'list_key' => 'elementor-plugin',

				// Top Panel Item
				'label_text' => __( 'Elementor Plugin', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-info-circle',

				// Boxes List
				'boxes_list' => array(

					// Box: Create Amazing Articles
					array(
						'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
						'title' => __( 'Create Amazing Articles', 'echo-knowledge-base' ),
						'description' => __( 'Create amazing documentation using our Elementor Widgets from our new plugin called Creative Add-ons', 'echo-knowledge-base' ),
						'html' => self::show_elementor_plugin_box(),
						'extra_tags' => ['iframe']
					)
				),
			];
		}

		/**
		 * View: New Features
		 */
		$views_config[] = [

			// Shared
			'active' => true,
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
			'list_key' => 'new-features',

			// Top Panel Item
			'label_text' => __( 'New Features', 'echo-knowledge-base' ),
			'main_class' => '',
			'label_class' => '',
			'icon_class' => 'epkbfa epkbfa-rocket',

			// Secondary Panel Items
			'secondary' => array(

				// Secondary View: Year 2022
				array(

					// Shared
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
					'list_key' => 'year-2022',
					'active' => true,

					// Secondary Panel Item
					'label_text' => __( 'Year 2022', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2022' ),
						)
					),
				),

				// Secondary View: Year 2021
				array(

					// Shared
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
					'list_key' => 'year-2021',

					// Secondary Panel Item
					'label_text' => __( 'Year 2021', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2021' ),
						)
					),
				),

				// Secondary View: Year 2020
				array(

					// Shared
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
					'list_key' => 'year-2020',

					// Secondary Panel Item
					'label_text' => __( 'Year 2020', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2020' ),
						)
					),
				),

				// Secondary View: Year 2019
				array(

					// Shared
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
					'list_key' => 'year-2019',

					// Secondary Panel Item
					'label_text' => __( 'Year 2019', 'echo-knowledge-base' ),
					'main_class' => '',
					'label_class' => '',

					// Secondary Boxes List
					'list_top_actions_html' => '',
					'list_bottom_actions_html' => '',
					'boxes_list' => array(
						array(
							'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_addons_news_read'] ),
							'html' => EPKB_Add_Ons_Features::get_new_features_box_by_year( 'Year 2019' ),
						)
					),
				),
			),

			// Boxes List
			'list_top_actions_html' => '',
			'list_bottom_actions_html' => '',
		];

		$license_content = '';
		if ( current_user_can('manage_options') ) {
			$license_content = apply_filters( 'epkb_license_fields', $license_content );
		}

		/**
		 * View: Licenses
		 */
		if ( ! empty( $license_content ) ) {
			$views_config[] = [

				// Shared
				'list_id'    => 'eckb_license_tab',
				'list_key'   => 'licenses',

				// Top Panel Item
				'label_text' => __( 'Licenses', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-key',

				// Boxes List
				'boxes_list' => self::show_license_boxes( $license_content ),
			];
		}

		/**
		 * View: Debug
		 */
		$views_config[] = [

			// Shared
			'list_key' => 'debug',

			// Top Panel Item
			'label_text' => __( 'Debug', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-info-circle',

			// Boxes List
			'boxes_list' => array(

				// Box: Information required for support
				array(
					'title' => __( 'Information required for support', 'echo-knowledge-base' ),
					'description' => __( 'Enable debug when instructed by the support team.', 'echo-knowledge-base' ),
					'html' => self::display_debug_info(),
				)
			),
		];

		/**
		 * View: Other
		 */
		$delete_kb_handler = new EPKB_Delete_KB();
		$views_config[] = [

			// Shared
			'list_key' => 'other',

			// Top Panel Item
			'label_text' => __( 'Other', 'echo-knowledge-base' ),
			'icon_class' => 'ep_font_icon_tools',

			// Boxes List
			'boxes_list' => array(

				// Box: Delete All KBs Data
				array(
					'title' => __( 'Delete All KBs Data', 'echo-knowledge-base' ),
					'html' => $delete_kb_handler->get_delete_all_kbs_data_form(),
				)
			),
		];

		return $views_config;
	}
}
