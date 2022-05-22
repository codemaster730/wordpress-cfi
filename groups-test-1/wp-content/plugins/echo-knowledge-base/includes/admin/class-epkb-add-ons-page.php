<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Add-ons page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Add_Ons_Page {

	public function display_add_ons_page() {

		ob_start(); ?>

		<div class="wrap">
			<h1></h1>
		</div>
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-add-ons-container">
			<div class="epkb-config-wrapper">
				<div class="wrap" id="ekb_core_top_heading"></div>
				<div class="eckb-top-notice-message"></div>      <?php
				self::display_add_ons_details();  ?>
			</div>

		</div>      <?php

		echo ob_get_clean();
	}

	/**
	 * Display all add-ons
	 */
	private static function display_add_ons_details() {

		// only administrator can see licenses
		$license_content = '<h4>' . sprintf( __( 'You can access your license account %s here%s' , 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/account-dashboard/" target="_blank" rel="noopener">', '</a>' ) . '</h4>' .
		'<h5>' . sprintf( __( 'Please refer to the %s documentation%s for help with your license account and any other issues.', 'echo-knowledge-base' ), '<a href="https://www.echoknowledgebase.com/documentation/my-account-and-license-faqs/" target="_blank" rel="noopener">', '</a>') . '</h5> ';
		if ( current_user_can('manage_options') ) {
			$license_content = apply_filters( 'epkb_license_fields', $license_content );
		}

		$tab = EPKB_Utilities::get( 'epkb-tab', 'add-ons' );
		$tab = ( $tab == 'licenses' && empty($license_content) ) ? 'add-ons' : $tab;    ?>

		<div id="epkb-tabs" class="add_on_container">
			<section class="epkb-main-nav">
				<ul class="epkb-admin-pages-nav-tabs">
					<li class="nav_tab <?php echo ($tab == 'add-ons' ? 'active' : ''); ?>">
						<h2><span class="epkbfa epkbfa-cubes icon_green"></span> <?php _e( 'Add-ons', 'echo-knowledge-base' ); ?></h2>
						<p><?php _e( 'More Possibilities', 'echo-knowledge-base' ); ?></p>
					</li> <?php
					 if ( ! EPKB_Utilities::is_creative_addons_widgets_enabled() ) { ?>
						<li class="nav_tab <?php echo ($tab == 'elementor-plugin' ? 'active' : ''); ?>">
							<h2><span class="epkbfa epkbfa-info-circle icon_blue"></span> <?php _e( 'Elementor Plugin', 'echo-knowledge-base' ); ?></h2>
							<p><?php _e( 'Create Amazing Articles', 'echo-knowledge-base' ); ?></p>
						</li> <?php
					 } ?>
					<!-- <li class="nav_tab <?php //echo ($tab == 'plugins' ? 'active' : ''); ?>">
						<h2><?php //_e( 'Plugins', 'echo-knowledge-base' ); ?></h2>
						<p><?php //_e( 'More Possibilities', 'echo-knowledge-base' ); ?></p>
					</li>--> 	<?php

					if ( ! empty($license_content) ) {  ?>
						<li id="eckb_license_tab" class="nav_tab <?php echo ($tab == 'licenses' ? 'active' : ''); ?>">
							<h2><span class="epkbfa epkbfa-certificate icon_blue"></span> <?php _e( 'Licenses', 'echo-knowledge-base' ); ?></h2>
							<p><?php _e( 'Licenses for add-ons', 'echo-knowledge-base' ); ?></p>
						</li>					<?php
					}       ?>

					<li class="nav_tab <?php echo ($tab == 'debug' ? 'active' : ''); ?>">
						<h2><span class="epkbfa epkbfa-info-circle icon_red"></span> <?php esc_html_e( 'Debug', 'echo-knowledge-base' ); ?></h2>
						<p><?php esc_html_e( 'Information required for support.', 'echo-knowledge-base' ); ?></p>
					</li>
					<li class="nav_tab <?php echo ($tab == 'other' ? 'active' : ''); ?>">
						<h2><span class="ep_font_icon_tools"></span> <?php esc_html_e( 'Other', 'echo-knowledge-base' ); ?></h2>
						<p><?php esc_html_e( 'Site wide settings', 'echo-knowledge-base' ); ?></p>
					</li>
				</ul>
			</section>
			<div id="add_on_panels" class="ekb-admin-pages-panel-container">
				<div class="ekb-admin-page-tab-panel container-fluid <?php echo ($tab == 'add-ons' ? 'active' : ''); ?>">
					<div class="row">   <?php

						// http://www.echoknowledgebase.com/wp-content/uploads/2017/09/product_preview_coming_soon.png

						self::add_on_product( array(
							'id'                => 'epkb-add-on-bundle',
							'title'             => __( 'Add-on Bundle', 'echo-knowledge-base' ),
							'special_note'      => __( 'Save money with bundle discount', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/add-on-bundle-2.jpg',
							'desc'              => __( 'Save up to 50% when buying multiple add-ons together.', 'echo-knowledge-base' ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/bundle-pricing/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=bundle',
						) );

						$i18_grid = '<strong>' . __( 'Grid Layout', 'echo-knowledge-base' ) . '</strong>';
						$i18_sidebar = '<strong>' . __( 'Sidebar Layout', 'echo-knowledge-base' ) . '</strong>';

						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Elegant Layouts', 'echo-knowledge-base' ),
							'special_note'      => __( 'More ways to design your KB', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-EL'.'AY-1.1.jpg',
							'desc'              => sprintf( __( 'Use %s or %s for KB Main page or combine Basic, Tabs, Grid and Sidebar layouts in many cool ways.', 'echo-knowledge-base' ), $i18_grid, $i18_sidebar ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/elegant-layouts/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=elegant-layouts',
						) );

						$i18_list = '<strong>' . __( 'product, service or team', 'echo-knowledge-base' ) . '</strong>';
						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Multiple Knowledge Bases', 'echo-knowledge-base' ),
							'special_note'      => __( 'Expand your documentation', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-MKB-1.jpg',
							'desc'              => sprintf( _x( 'Create a separate Knowledge Base for each %s.',
                                                    'product, service and team.', 'echo-knowledge-base' ), $i18_list ),
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

						$i18_what = '<strong>' . __( 'Widgets and shortcodes', 'echo-knowledge-base' ) . '</strong>';
						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Widgets', 'echo-knowledge-base' ),
							'special_note'      => __( 'Shortcodes, Widgets, Sidebars', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-WIDG-2.jpg',
							'desc'              => sprintf( __( 'Add KB Search, Most Recent Articles and other %s to your articles, sidebars and pages.',
                                                'echo-knowledge-base' ), $i18_what ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/widgets/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=widgets'
						) );

					   $i18_objects = '<strong>' . __( 'PDFs, pages, posts and websites', 'echo-knowledge-base' ) . '</strong>';
						self::add_on_product( array(
							'id'                => '',
							'title'             => __( 'Links Editor for PDFs and More', 'echo-knowledge-base' ),
							'special_note'      => __( 'Link to PDFs, posts and pages', 'echo-knowledge-base' ),
							'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-LINK-2.jpg',
							'desc'              => sprintf( __( 'Set Articles to links to %s. On KB Main Page, choose icons for your articles.', 'echo-knowledge-base' ), $i18_objects ),
							'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/links-editor-for-pdfs-and-more/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=links-editor'
						) );

					   $i18_groups = '<strong>' . __( 'Groups', 'echo-knowledge-base' ) . '</strong>';
					   $i18_roles = '<strong>' . __( 'KB Roles', 'echo-knowledge-base' ) . '</strong>';
					   self::add_on_product( array(
						   'id'                => '',
						   'title'             => __( 'Access Manager', 'echo-knowledge-base' ),
						   'special_note'      => __( 'Protect your KB content', 'echo-knowledge-base' ),
						   'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/07/featured-image-AMGR-1.jpg',
						   'desc'              => sprintf( __( 'Restrict your Articles to certain %s using KB Categories. Assign users to specific %s within Groups.', 'echo-knowledge-base' ), $i18_groups, $i18_roles ),
						   'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/access-manager/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=access-manager'
					   ) );

						// TODO remove
					   self::add_on_product( array(
						   'id'                => '',
						   'title'             => __( 'Article Rating and Feedback', 'echo-knowledge-base' ),
						   'special_note'      => __( 'Let users rate your articles', 'echo-knowledge-base' ),
						   'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/2019/11/EP'.'RF-featured-image.jpg',
						   'desc'              => sprintf( __( 'Let your readers rate the quality of your articles and submit insightful feedback. Utilize analytics on most and least rated articles.', 'echo-knowledge-base' ), $i18_objects ),
						   'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/article-rating-and-feedback/?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=article-rating'
					   ) );

					   self::add_on_product( array( // TODO
						   'id'                => '',
						   'title'             => __( 'KB Articles Import', 'echo-knowledge-base' ),
						   'special_note'      => __( 'Import Articles and Categoies', 'echo-knowledge-base' ),
						   'img'               => 'https://www.echoknowledgebase.com/wp-content/uploads/edd/2020/08/KB-Import-Export-Banner.jpg',
						   'desc'              => sprintf( __( 'Import articles and categories into your knowledge base from CSV file. Migrate and move your articles from another KB.', 'echo-knowledge-base' ), $i18_grid, $i18_sidebar ),
						   'learn_more_url'    => 'https://www.echoknowledgebase.com/wordpress-plugin/kb-import-export//?utm_source=plugin&utm_medium=addons&utm_content=home&utm_campaign=kb-import-export/',
					   ) );  ?>

					</div>
				</div>				<?php

				if ( ! EPKB_Utilities::is_creative_addons_widgets_enabled() ) { ?>
					<div class="ekb-admin-page-tab-panel container-fluid epkb-features-container <?php echo ($tab == 'elementor-plugin' ? 'active' : ''); ?>">
						<p class="epkb-elementor-msg"><span class="epkbfa epkbfa-info-circle"></span><?php echo ' ' . __('Create amazing documentation using our Elementor Widgets from our new plugin called Creative Add-ons', 'echo-knowledge-base') . ': '; ?>
								<a href="https://wordpress.org/plugins/creative-addons-for-elementor/" target="_blank"><?php _e('click here', 'echo-knowledge-base'); ?></a> </p>
						<?php EPKB_New_Features_Page::display_crel_features_details();  ?>
					</div> <?php
				} ?>

				<!--   LICENSES ONLY -->		<?php
				if ( ! empty($license_content) ) { ?>
					<div class="ekb-admin-page-tab-panel container-fluid <?php echo ($tab == 'licenses' ? 'active' : ''); ?>">
						<section id="ekcb-licenses">
							<ul>  	<!-- Add-on name / License input / status  -->   <?php
								echo $license_content;      ?>
							</ul>
						</section>
					</div>				<?php
				}  ?>

				<!-- DEBUG INFO -->
				<div class="ekb-admin-page-tab-panel container-fluid <?php echo ($tab == 'debug' ? 'active' : ''); ?>">
                    <p><?php _e( 'Enable debugging when instructed by the Echo team.', 'echo-knowledge-base' ); ?></p>
                    <?php
					self::display_debug_info( new EPKB_HTML_Elements() );       ?>
				</div>

				<!-- Other -->
				<div class="ekb-admin-page-tab-panel container-fluid <?php echo ($tab == 'other' ? 'active' : ''); ?>">
					<?php
					$delete_kb = new EPKB_Delete_KB();
					$delete_kb->display_delete_tab( new EPKB_HTML_Elements() );       ?>
				</div>
			</div>
		</div>   <?php
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
			<div class="button_container">
				<?php if ( ! empty($values['coming_when']) ) { ?>
					<div class="coming_soon"><?php esc_html_e( $values['coming_when'] ); ?></div>
				<?php } else { ?>
					<a class="epkb-primary-btn" href="<?php echo $values['learn_more_url']; ?>" target="_blank"><?php _e( 'Learn More', 'echo-knowledge-base' ); ?></a>
				<?php } ?>
			</div>

		</div>    <?php
	}

	/**
	 * @param EPKB_HTML_Elements $form
	 */
	private static function display_debug_info( $form ) {

		$is_debug_on = EPKB_Utilities::get_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, false );
		$heading = $is_debug_on ? esc_html__( 'Debug Information:', 'echo-knowledge-base' ) :
								  esc_html__( 'Enable debug when asked by Echo KB support team.', 'echo-knowledge-base' );     ?>

		<div id="epkb_debug_info_tab_page">

			<section style="padding-top: 20px;" class="save-settings">    <?php
				$button_text = $is_debug_on ? __('Disable Debug', 'echo-knowledge-base') : __( 'Enable Debug', 'echo-knowledge-base' );
				$form->submit_button( $button_text, 'epkb_toggle_debug', 'epkb_toggle_debug','' ,true ,'' ,'epkb-primary-btn' ); ?>
			</section>

			<section>
				<h3><?php echo $heading; ?></h3>
			</section>     <?php

			if ( $is_debug_on ) {
				echo self::display_debug_data();        ?>

				<form action="<?php echo esc_url( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . '1&page=epkb-add-ons' ) ); ?>" method="post" dir="ltr">
                    <?php
                        $form->checkbox( [
		                    'name'  => 'epkb_show_full_debug',
		                    'label' => esc_html__( 'Output full debug information (after instructed by support staff)', 'echo-knowledge-base' ),
		                    'input_class' => 'epkb-checkbox-input',
		                    'input_group_class' => 'epkb-input-group',
	                    ] ); ?>

                    <section style="padding-top: 20px;" class="save-settings checkbox-input"><?php
						$form->submit_button( __( 'Download System Information', 'echo-knowledge-base' ), 'epkb_download_debug_info', 'epkb_download_debug_info', '',true ,'' ,'epkb-primary-btn' ); ?>
					</section>
				</form>     <?php
			}    ?>

			<div id='epkb-ajax-in-progress-debug-switch' style="display:none;">
				<?php esc_html_e( 'Switching debug... ', 'echo-knowledge-base' ); ?><img class="epkb-ajax waiting" style="height: 30px;"
			                                                                         src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
			</div>

		</div>      		<?php
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

		$all_kb_ids = epkb_get_instance()->kb_config_obj->get_kb_ids();

		// retrieve KB config directly from the database
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

		// display PHP and WP settings
		$output .= self::get_system_info();

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
				'KB - Links Editor','KB Articles Import',
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
}

