<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display FAQ configuration menu and pages
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_FAQ_Config_Page {

	public function display_getting_started() {
		echo "TEST";
	}

	/**
	 * Display FAQ configuration page
	 */
	public function display_page() {

		// retrieve default FAQ configuration
	/*	$faq_config = epkb_get_instance()->faq_config_obj->get_faq_shortcode_config( EPKB_FAQ_Config_DB::DEFAULT_FAQ_SHORTCODE_ID );
		if ( is_wp_error( $faq_config ) || empty($faq_config) || ! is_array($faq_config) || count($faq_config) < 100 ) {
			$faq_config = EPKB_FAQ_Config_Specs::get_default_faq_config( EPKB_FAQ_Config_DB::DEFAULT_FAQ_SHORTCODE_ID );
		} */   ?>

		<div class="wrap">
			<h1></h1>
		</div>

		<h1 style="color: red; line-height: 1.2em; background-color: #eaeaea; border: solid 1px #ddd; padding: 20px;" class="epkb-css-working-hide-message">The CSS for this FAQ admin page is missing. This is most likely due to page loading interuption or one of your plugins blocking our CSS.
			First, try to refresh the page. If that does not help, try to deactivate your plugins or contact us for help.</h1>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container epkb-faq-config">
			<div class="epkb-config-wrapper">

				<div class="wrap" id="ekb_core_top_heading"></div>

				<div id="epkb-config-main-info">		<?php
				//	$this->display_top_panel( $faq_config );         ?>
				</div>

				<div>          <?php
				//	$this->display_editor_panels( $faq_config );      ?>
				</div>

			</div>

         <div class="eckb-bottom-notice-message"></div>
		</div>	    <?php
	}

	/**
	 * Display top overview panel
	 * @param $faq_config
	 */
	private function display_top_panel( $faq_config ) {

		$show_overview_page = true;      ?>

		<div class="epkb-info-section epkb-kb-name-section">
			<div class="epkb-kb-name-label">FAQ: </div>
			<?php
			self::display_list_of_faqs( $faq_config ); 			?>
		</div>

		<!-- OVERVIEW -->
		<div class="epkb-info-section epkb-info-main <?php echo $show_overview_page ? 'epkb-active-page' : ''; ?>">
			<div class="overview-icon-container">
				<p><?php _e( 'Setup', 'echo-knowledge-base' ); ?></p>
				<div class="page-icon overview-icon ep_font_icon_data_report" id="epkb-config-overview"></div>
			</div>
		</div>

		<!--  FRONTEND EDITOR BUTTON -->
		<div class="epkb-info-section epkb-info-pages" id="epkb-main-page-button">
			<div class="page-icon-container">
				<p><?php _e( 'Frontend Editor', 'echo-knowledge-base' ); ?></p>
				<div class="page-icon ep_font_icon_flow_chart" id="epkb-config-editor"></div>
			</div>
		</div>

		<div class="support-icon-container">
			<a href="https://www.echoknowledgebase.com/front-end-editor-support-and-questions/" target="_blank"><?php _e( 'Need Help', 'echo-knowledge-base' ); ?> <span class="epkbfa epkbfa-question-circle-o"></span></a>
		</div>

		<div class="epkb-open-mm">
			<span class="ep_font_icon_arrow_carrot_down"></span>
		</div>      <?php
	}

	private function display_editor_panels( $faq_config ) {

		$show_overview_page = true;
		//$editor_urls = EPKB_Editor_Utilities::get_editor_urls( $faq_config );
		$editor_urls['main_page_url'] = '';
		$html = new EPKB_HTML_Elements();

		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
			return;
		}
	}

	/**
	 * Display list of FAQs.
	 *
	 * @param $faq_config
	 */
	private static function display_list_of_faqs( $faq_config ) {

		$all_faq_terms = EPKB_FAQ_Utilities::get_faq_shortcode_categories_unfiltered();
		if ( empty($all_faq_terms) ) {
			echo 'Error (11)';
			return;
		}

		// output the list of locations
		$list_output = '<select class="epkb-kb-name" id="epkb-list-of-kbs">';
		$active = true;
		foreach( $all_faq_terms as $term ) {
			// $tab_url = 'edit.php?post_type=' . EPKB_FAQ_Handler::FAQ_POST_TYPE . '&page=epkb-help-dialog-config';
			$list_output .= '<option value="' . $term->term_id . '" ' . $active . '>' . esc_html( $term->name ) . '</option>';
			$list_output .= '</a>';
			$active = false;
		}

		$list_output .= '</select>';

		echo $list_output;
	}
}
