<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display analytics
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Analytics_Page {

	private $kb_config;

	public function __construct( $kb_config=array() ) {
		$this->kb_config = empty( $kb_config ) ? epkb_get_instance()->kb_config_obj->get_current_kb_configuration() : $kb_config;
	}

	/**
	 * Display analytics page with toolbar and content.
	 */
	public function display_plugin_analytics_page() {

		$admin_page_views = $this->get_regular_views_config();

		EPKB_HTML_Admin::admin_page_css_missing_message( false, true );   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap">

			<div class="epkb-analytics-page-container <?php do_action( 'eckb_add_container_classes' ); ?>"> <?php

				/**
				 * ADMIN HEADER (KB logo and list of KBs dropdown)
				 */
				EPKB_HTML_Admin::admin_header( $this->kb_config, ['admin_eckb_access_search_analytics_read'] );

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

		</div>

		<div id="epkb-dialog-info-icon" title="" style="display: none;">
			<p id="epkb-dialog-info-icon-msg"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span></p>
		</div>      <?php
	}

	/**
	 * Show KB core statistics
	 *
	 * @param $kb_id
	 */
	private function display_core_content_analytics( $kb_id ) {

		$all_kb_terms      = EPKB_Core_Utilities::get_kb_categories_unfiltered( $kb_id );
		$nof_kb_categories = $all_kb_terms === null ? 'unknown' : count( $all_kb_terms );
		$nof_kb_articles   = EPKB_Articles_DB::get_count_of_all_kb_articles( $kb_id );  ?>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php _e( 'Categories', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo EPKB_Utilities::sanitize_int( $nof_kb_categories ); ?></div>
				<div class="widget-desc"><?php _e( 'Categories help you to organize articles into groups and hierarchies.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="widget-toggle"><?php
				$url = admin_url('edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_id ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $kb_id ));  ?>
				<a href="<?php echo esc_url( $url ); ?>" target="_blank"><?php _e( 'View Categories', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php _e( 'Articles', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo EPKB_Utilities::sanitize_int( $nof_kb_articles ); ?></div>
				<div class="widget-desc"><?php _e( 'Article belongs to one or more categories or sub-categories.', 'echo-knowledge-base' ); ?></div>
			</div>
			<div class="widget-toggle">
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_id )) ); ?>" target="_blank"><?php _e( 'View Articles', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>	<?php

		// DEPRECATED: for older Advanced Search and Article Rating output it here
		do_action( 'eckb_analytics_content', $kb_id );
	}

	/**
	 * Show KB core statistics
	 *
	 * @param $kb_id
	 */
	private function display_core_search_data( $kb_id ) {

		$user_search_found_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_hit_search_counter', 0 );
		$user_search_not_found_count = EPKB_Utilities::get_kb_option( $kb_id, 'epkb_miss_search_counter', 0 );
		$user_search_total = $user_search_found_count + $user_search_not_found_count;   ?>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php _e( 'Searches with Articles Found', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo $user_search_found_count; ?></div>
			</div>
			<div><?php _e( 'Are you interested in searched-for keywords?', 'echo-knowledge-base' ); ?></div>
			<br>
			<a href="https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/" target="_blank">Learn More</a>
		</div>

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php _e( 'Searches with No Results', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo $user_search_not_found_count; ?></div>
			</div>
			<div><?php _e( 'Do you need to know what keywords were not found?', 'echo-knowledge-base' ); ?></div>
			<br>
			<a href="https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/" target="_blank">Learn More</a>

		</div>

		<div class="overview-info-widget overview-info-widget__content-center">
			<div class="widget-header"><h4><?php _e( 'Articles Found Success Rate', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo empty($user_search_total) ? 'N/A' : number_format(100 * $user_search_found_count / $user_search_total, 0) . '%'; ?></div>
			</div>
		</div>  <?php
	}

	/**
	 * Get HTML for KB Stats box
	 *
	 * @return false|string
	 */
	private function get_kb_stats_box_html() {

		ob_start();     ?>

		<div class="eckb-config-content epkb-active-content" id="epkb-statistics-data-content">
			<div class="epkb-config-content-wrapper">
				<?php $this->display_core_content_analytics( $this->kb_config['id'] ); ?>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get HTML for Search Data box
	 *
	 * @return false|string
	 */
	private function get_search_data_box_html() {

		ob_start();     ?>

		<div class="eckb-config-content" id="epkb-search-data-content">
			<div class="epkb-config-content-wrapper">
				<?php $this->display_core_search_data( $this->kb_config['id'] ); ?>
			</div>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Get HTML for Advanced Search add-on ad box
	 *
	 * @return false|string
	 */
	private static function get_asea_addon_ad_box_html() {

		return EPKB_HTML_Forms::advertisement_ad_box( array(
			'icon'              => 'epkbfa-linode',
			'title'             => __( 'Advanced Search Add-on', 'echo-knowledge-base' ),
			'img_url'           => 'https://www.echoknowledgebase.com/wp-content/uploads/2020/10/advanced-search-analytics-example.jpg',
			'desc'              => __( "Enhance users' search experience and view search analytics, including popular searches and no results searches.", 'echo-knowledge-base' ),
			'list'              => array(
				__( 'Access analytics for the most popular searches', 'echo-knowledge-base' ),
				__( 'Discover analytics for no results searches', 'echo-knowledge-base' ),
				__( 'Write articles for topics that are not covered', 'echo-knowledge-base' ),
				__( 'Add missing search keywords to existing articles', 'echo-knowledge-base' )
			),
			'btn_text'          => __( 'Buy Now', 'echo-knowledge-base' ),
			'btn_url'           => 'https://www.echoknowledgebase.com/wordpress-plugin/advanced-search/',
			'btn_color'         => 'green',

			'more_info_text'    => __( 'More Information', 'echo-knowledge-base' ),
			'more_info_url'     => 'https://www.echoknowledgebase.com/documentation/advanced-search-overview/',
			'more_info_color'   => 'orange',
			'box_type'			=> 'new-feature',
			'return_html'       => true,
		) );
	}

	/**
	 * Get configuration array for regular views
	 *
	 * @return array
	 */
	private function get_regular_views_config() {

		$core_views = [];

		/**
		 * View: KB Stats
		 */
		$core_views[] = [

			// Shared
			'active' => true,
			'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
			'list_key' => 'kb-stats',

			// Top Panel Item
			'label_text' => __( 'KB Stats', 'echo-knowledge-base' ),
			'icon_class' => 'ep_font_icon_data_report',

			// Boxes List
			'boxes_list' => array(

				// Box: KB Stats
				array(
					'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
					'html' => $this->get_kb_stats_box_html(),
				),
			),
		];

		/**
		 * View: Search Data
		 */
		if ( ! EPKB_Utilities::is_advanced_search_enabled() ) {

			$core_views[] = [

				// Shared
				'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
				'list_key' => 'search-data',

				// Top Panel Item
				'label_text' => __( 'Search Data', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-search',

				// Boxes List
				'boxes_list' => array(

					// Box: Search Data
					array(
						'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
						'html' => $this->get_search_data_box_html(),
					),

					// Box: Advanced Search add-on ad
					array(
						'minimum_required_capability' => EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] ),
						'class' => 'epkb-admin__boxes-list__box__search-data__asea-ad',
						'html' => self::get_asea_addon_ad_box_html(),
					),
				),
			];
		}

		$add_on_views = apply_filters( 'eckb_admin_analytics_page_views', [], $this->kb_config );
		if ( empty( $add_on_views ) || ! is_array( $add_on_views ) ) {
			$add_on_views = [];
		}

		// Set minimum required capability for search analytics data passed from add-ons
		foreach ( $add_on_views as $view_index => $view ) {

			// Apply for certain add-ons only
			if ( ! isset( $view['list_key'] ) || ! in_array( $view['list_key'], ['search-data', 'rating-data'] ) ) {
				continue;
			}

			// Access for View
			$add_on_views[$view_index]['minimum_required_capability'] = EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] );

			// Access for Boxes
			if ( isset( $view['boxes_list'] ) && is_array( $view['boxes_list'] ) ) {
				foreach ( $view['boxes_list'] as $box_index => $box ) {
					$add_on_views[$view_index]['boxes_list'][$box_index]['minimum_required_capability'] = EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] );
					if ( ! current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {
						$add_on_views[$view_index]['boxes_list'][$box_index]['class'] = isset( $box['class'] ) ? $box['class'] . ' epkb-admin__limit-access-control' : ' epkb-admin__limit-access-control';
					}
				}
			}

			// Access for Secondary Views
			if ( isset( $view['secondary'] ) && is_array( $view['secondary'] ) ) {
				foreach ( $view['secondary'] as $secondary_view_index => $secondary_view ) {
					$add_on_views[$view_index]['secondary'][$secondary_view_index]['minimum_required_capability'] = EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] );

					// Access for Secondary Boxes
					if ( isset( $secondary_view['boxes_list'] ) && is_array( $secondary_view['boxes_list'] ) ) {
						foreach ( $secondary_view['boxes_list'] as $secondary_box_index => $secondary_box ) {
							$add_on_views[$view_index]['secondary'][$secondary_view_index]['boxes_list'][$secondary_box_index]['minimum_required_capability'] = EPKB_Admin_UI_Access::get_context_required_capability( ['admin_eckb_access_search_analytics_read'] );
							if ( ! current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {
								$add_on_views[$view_index]['secondary'][$secondary_view_index]['boxes_list'][$secondary_box_index]['class'] =
																isset( $secondary_box['class'] ) ? $secondary_box['class'] . ' epkb-admin__limit-access-control' : ' epkb-admin__limit-access-control';
							}
						}
					}
				}
			}
		}

		return array_merge( $core_views, $add_on_views );
	}
}