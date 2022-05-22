<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display analytics
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Analytics_Page {

	private $kb_config = array();

	public function __construct( $kb_config=array() ) {
		$this->kb_config = empty($kb_config) ? epkb_get_instance()->kb_config_obj->get_current_kb_configuration() : $kb_config;
		if ( is_wp_error( $this->kb_config ) ) {
			$this->kb_config = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
		}
	}

	/**
	 * Display analytics page with toolbar and content.
	 */
	public function display_plugin_analytics_page() { ?>

		<div class="wrap">
			<h1></h1><!-- This is a honeypot for WP JS injected garbage -->
		</div>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container epkb-analytics-container <?php do_action( 'eckb_add_container_classes'); ?>">
			<div class="epkb-config-wrapper">
				<div class="wrap" id="ekb_core_top_heading"></div>

					<div id="epkb-config-main-info"> <?php
						$this->display_analytics_page_top_panel(); ?>
					</div>				    <?php

					$this->display_analytics_page_details();   ?>

			</div>
		</div>

		<div id="epkb-dialog-info-icon" title="" style="display: none;">
			<p id="epkb-dialog-info-icon-msg"><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span></p>
		</div>      <?php
	}

	/**
	 * Display top panel with buttons
	 */
	private function display_analytics_page_top_panel() { ?>

		<div class="eckb-nav-section epkb-kb-name-section">			<?php
			$this->display_list_of_kbs(); 			?>
		</div>

		<!--  CORE STATISTICS PAGE BUTTON -->
		<div class="eckb-nav-section epkb-active-nav">
			<div class="page-icon-container">
				<p><?php _e( 'KB Stats', 'echo-knowledge-base' ); ?></p>
				<div class="page-icon ep_font_icon_data_report" id="epkb-statistics-data"></div>
			</div>
		</div> <?php

		if ( ! EPKB_Utilities::is_advanced_search_enabled() ) { ?>
			<!--  CORE SEARCH DATA BUTTON -->
			<div class="eckb-nav-section">
				<div class="page-icon-container">
					<p><?php _e( 'Search Data', 'echo-knowledge-base' ); ?></p>
					<div class="page-icon epkbfa epkbfa-search" id="epkb-search-data"></div>
				</div>
			</div> <?php
		}   ?>

		<!-- DISPLAY BUTTONS FOR OTHER ANALYTICS PAGES -->  <?php
		do_action( 'eckb_analytics_navigation_bar');
	}

	/**
	 * Display all configuration fields
	 */
	private function display_analytics_page_details() {

		$kb_id = EPKB_KB_Handler::get_current_kb_id();  ?>

		<div class="eckb-config-content epkb-active-content" id="epkb-statistics-data-content">
			<div class="epkb-config-content-wrapper">
				<?php $this->display_core_content_analytics( $kb_id ); ?>
			</div>
		</div>		<?php

		if ( ! EPKB_Utilities::is_advanced_search_enabled() ) { ?>
			<div class="eckb-config-content" id="epkb-search-data-content">
				<div class="epkb-config-content-wrapper">
					<?php $this->display_core_search_data( $kb_id ); ?>
				</div>
				<div class="epkb-config-content-ad-wrapper">
					<?php $this->display_advanced_search_ad(); ?>
				</div>
			</div> <?php
		}

		// display add-on analytics pages
		do_action( 'eckb_analytics_content', $kb_id );
	}

	/**
	 * Show KB core statistics
	 *
	 * @param $kb_id
	 */
	private function display_core_content_analytics( $kb_id ) {

		$all_kb_terms      = EPKB_Utilities::get_kb_categories_unfiltered( $kb_id );
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

		<div class="overview-info-widget">
			<div class="widget-header"><h4><?php _e( 'Articles Found Success Rate', 'echo-knowledge-base' ); ?></h4></div>
			<div class="widget-content">
				<div class="widget-count"><?php echo empty($user_search_total) ? 'N/A' : number_format(100 * $user_search_found_count / $user_search_total, 0) . '%'; ?></div>
			</div>
		</div>  <?php
	}

	/**
	 * Display a list of KBs if Multiple KB is available.
	 */
	private function display_list_of_kbs() {

		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			$kb_name = $this->kb_config[ 'kb_name' ];
			echo '<h1 class="epkb-kb-name">' . esc_html( $kb_name ) . '</h1>';
			return;
		}

		// output the list
		$list_output = '<select class="epkb-kb-name" id="epkb-list-of-kbs">';
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $one_kb_config ) {

			if ( $one_kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
				continue;
			}

			$kb_name = $one_kb_config[ 'kb_name' ];
			$active = ( $this->kb_config['id'] == $one_kb_config['id'] ? 'selected' : '' );
			$tab_url = 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . $one_kb_config['id'] . '&page=epkb-plugin-analytics';

			$list_output .= '<option value="' . $one_kb_config['id'] . '" ' . $active . ' data-kb-admin-url=' . esc_url($tab_url) . '>' . esc_html( $kb_name ) . '</option>';
			$list_output .= '</a>';
		}

		$list_output .= '</select>';

		echo $list_output;
	}

	private function display_advanced_search_ad() {

		if ( defined('AS'.'EA_PLUGIN_NAME') ) {
			return;
		}

		$HTML = New EPKB_HTML_Elements();

		$HTML->advertisement_ad_box( array(
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
			'box_type'			   => 'new-feature',
		));
	}
}