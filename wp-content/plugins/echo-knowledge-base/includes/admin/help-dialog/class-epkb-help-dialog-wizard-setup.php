<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Show setup wizard for Help Dialog
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Help_Dialog_Wizard_Setup {

	public static $theme_images = array(
		'standard'  => 'setup-wizard/Basic-Layout-Standard.jpg',
		'elegant'   => 'setup-wizard/Basic-Layout-Elegant.jpg',
	);

	function __construct() {
		add_action( 'wp_ajax_epkb_help_dialog_apply_setup_wizard_changes',  array( $this, 'apply_setup_wizard_changes' ) );
		add_action( 'wp_ajax_nopriv_epkb_help_dialog_apply_setup_wizard_changes', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_report_admin_error',  array( 'EPKB_Core_Utilities', 'handle_report_admin_error' ) );
		add_action( 'wp_ajax_nopriv_epkb_report_admin_error', array( 'EPKB_Utilities', 'user_not_logged_in' ) );
	}

	/**
	 * Show KB Setup page
	 */
	public function display_setup_wizard() {		?>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-wizard-container">
			<div class="" id="epkb-config-wizard-content">
				<div class="epkb-config-wizard-inner">

					<!------- Wizard Header ------------>
					<div class="epkb-wizard-header">
						<div class="epkb-wizard-header__info">
							<h1 class="epkb-wizard-header__info__title">
								<?php _e( 'Setup Your Help Dialog', 'echo-knowledge-base' ); ?>
							</h1>
						</div>
						<div class="epkb-setup-wizard-theme-header">
							<h2 class="epkb-setup-wizard-theme-header__info__title">
								<?php _e( 'Choose an initial Help Dialog design and then adjust colors and other elements in our front-end Editor.', 'echo-knowledge-base' ); ?>
							</h2>
						</div>
					</div>

					<!------- Wizard Content ---------->
					<div class="epkb-wizard-content">
						<?php self::setup_wizard_theme(); ?>
					</div>

					<!------- Wizard Footer ---------->
					<div class="epkb-wizard-footer">

						<!----Step 1 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-themes-panel-button epkb-wc-step-panel-button epkb-wc-step-panel-button--active">
							<div class="epkb-wizard-button-container__inner epkb-wizard-button-container__align-center">
								<button value="apply" id="epkb-setup-wizard-button-apply" class="epkb-wizard-button epkb-setup-wizard-button-apply" data-wizard-type="theme"><?php _e( 'Finish Set Up', 'echo-knowledge-base' ); ?></button>
								<input type="hidden" id="_wpnonce_apply_wizard_changes" name="_wpnonce_apply_wizard_changes" value="<?php echo wp_create_nonce( "_wpnonce_apply_wizard_changes" ); ?>">
							</div>
						</div>

					</div>

					<div class="eckb-bottom-notice-message"></div>

				</div>
			</div>
		</div>		<?php

		// Report error form
		EPKB_HTML_Admin::display_report_admin_error_form();
	}

	// Setup Wizard: Step Themes - Choose Design
	private function setup_wizard_theme() {

		$themes_list = array(
			array(
				'id'    => 'standard',
				'name'  => 'Standard',
			),
			array(
				'id'    => 'elegant',
				'name'  => 'Elegant',
			),
		);      ?>

		<div id="epkb-wsb-step-themes-panel" class="epkb-setup-wizard-theme epkb-wc-step-panel  epkb-wc-step-panel--active eckb-wizard-step-themes">
			<div class="epkb-setup-wizard-theme-preview">

				<!-- THEME BUTTONS -->
				<div class="epkb-wizard-theme-tab-container">
					<input type="hidden" id="_wpnonce_help_dialog_setup_wizard_templates" name="_wpnonce_help_dialog_setup_wizard_templates" value="<?php echo wp_create_nonce( "_wpnonce_help_dialog_setup_wizard_templates" ); ?>"/>

					<div class="epkb-setup-wizard-group__container">

						<div class="epkb-setup-wizard-group__container-inner">

							<div class="epkb-setup-wt-tc__themes-group__list config-input-group epkb-hd__themes-group__list">   <?php

								foreach ( $themes_list as $theme ) { ?>
									<div id="epkb-setup-wt-theme-<?php echo $theme['id']; ?>-panel" class="epkb-setup-option-container">
										<div class="epkb-setup-option__inner">
											<div class="epkb-setup-option__selection">
												<div class="epkb-setup-option__option-container">
													<label class="epkb-setup-option__option__label">
														<input type="radio" name="epkp-theme" value="<?php echo $theme['id']; ?>">
														<span><?php echo $theme['name']; ?></span>
													</label>
												</div>
												<div class="epkb-setup-option__featured-img-container">
													<img class="epkb-setup-option__featured-img" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/' . self::$theme_images[$theme['id']]; ?>" title="<?php echo $theme['name']; ?>" />
												</div>
											</div>
										</div>
									</div>		<?php
								} ?>

							</div>

						</div>

					</div>

				</div>

			</div>
		</div>	<?php
	}


	/***************************************************************************
	 *
	 * Setup Wizards Functions
	 *
	 ***************************************************************************/

	/**
	 * User submit theme to use for the Help Dialog
	 */
	public function apply_setup_wizard_changes() {

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce_apply_wizard_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_wizard_changes'], '_wpnonce_apply_wizard_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) . ' (257)' );
		}

		// ensure that user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) . ' (258)' );
		}

		// Create a demo Location only for the first time - if no locations exists
		$all_locations = EPKB_FAQ_Utilities::get_help_dialog_location_categories_unfiltered();
		if ( empty( $all_locations ) ) {
			EPKB_Help_Dialog_Handler::create_demo_help_dialog();
		}

		// get selected Theme Name
		$theme_name = EPKB_Utilities::post( 'theme_name' );
		if ( empty( $theme_name ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 260 ) );
		}

		// get current Help Dialog settings
		$orig_settings = epkb_get_instance()->help_dialog_settings_obj->get_settings_or_default();

		// get selected theme settings
		$theme_settings = self::get_theme( $theme_name );

		// overwrite current Help Dialog configuration with new configuration from this Wizard
		$new_settings = array_merge( $orig_settings, $theme_settings );

		$update_settings_msg = epkb_get_instance()->help_dialog_settings_obj->update_settings( $new_settings );
		if ( is_wp_error( $update_settings_msg ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 239, $update_settings_msg ) );
		}

		wp_die( json_encode( array(
			'message' => __( 'Configuration Saved', 'echo-knowledge-base' ),
			'redirect_to_url' => admin_url( 'admin.php?page=epkb-help-dialog#getting-started' ) ) ) );
	}

	/**
	 * Get theme settings by theme name
	 *
	 * @param $theme_name
	 * @return array
	 */
	public static function get_theme( $theme_name ) {

		switch( $theme_name ) {

			/**
			 * Elegant theme
			 */
			case 'elegant':
				return array(

			/******************************************************************************
			 *
			 *  Setup
			 *
			 ******************************************************************************/
					// 'help_dialog_enable'                                 => 'on',
					// 'help_dialog_faqs_kb'                                => '1',
					'help_dialog_display_mode'                              => 'both',
					'help_dialog_logo_image_url'                            => Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png',
					'help_dialog_welcome_text'                              => __( 'Welcome to Support', 'echo-knowledge-base' ),
					//'help_dialog_container_desktop_height'                => '400',
					'help_dialog_container_desktop_width'                   => '400',
					'help_dialog_container_tablet_width'                    => '400',
					'help_dialog_container_mobile_width'                    => '400',
					'help_dialog_tablet_break_point'                        => '1025',
					'help_dialog_mobile_break_point'                        => '768',

					// - Top buttons
					'help_dialog_back_text_color'                           => "#7d7d7d",
					'help_dialog_back_text_color_hover_color'               => "#000000",
					'help_dialog_back_background_color'                     => "#f0f0f0",
					'help_dialog_back_background_color_hover_color'         => "#f0f0f0",


			/******************************************************************************
			 *
			 *  Launcher
			 *
			 ******************************************************************************/
					'help_dialog_launcher_start_delay'                      => '0',
					'help_dialog_launcher_background_color'                 => "#7b00a6",
					'help_dialog_launcher_background_hover_color'           => "#a5a5a5",
					'help_dialog_launcher_icon_color'                       => "#ffffff",
					'help_dialog_launcher_icon_hover_color'                 => "#000000",


			/******************************************************************************
			 *
			 *  FAQ List Tab
			 *
			 ******************************************************************************/
					'help_dialog_faqs_top_tab'                              => __( 'FAQs', 'echo-knowledge-base' ),
					'help_dialog_faqs_title'                                => __( 'How can we help you?', 'echo-knowledge-base' ),
					'help_dialog_faqs_search_placeholder'                   => __( 'Search for help', 'echo-knowledge-base' ),
					'help_dialog_article_read_more_text'                    => __( 'Read More', 'echo-knowledge-base' ),
					'help_dialog_background_color'                          => "#aa2dd6",
					'help_dialog_not_active_tab'                            => "#6d3687",
					'help_dialog_tab_text_color'                            => "#ffffff",
					'help_dialog_main_title_text_color'                     => "#FFFFFF",
					'help_dialog_welcome_text_color'                        => "#000000",
					'help_dialog_welcome_background_color'                  => "#f6deff",
					'help_dialog_breadcrumb_arrow_color'                    => "#000000",
					'help_dialog_faqs_qa_border_color'                      => "#CCCCCC",
					'help_dialog_faqs_question_text_color'                  => "#000000",
					'help_dialog_faqs_question_background_color'            => "#f7f7f7",
					'help_dialog_faqs_question_active_text_color'           => "#000000",
					'help_dialog_faqs_question_active_background_color'     => "#ffffff",
					'help_dialog_faqs_answer_text_color'                    => "#000000",
					'help_dialog_faqs_answer_background_color'              => "#ffffff",

					// - Search Results
					'help_dialog_breadcrumb_home_text'    => __( 'Home', 'echo-knowledge-base' ),
					'help_dialog_breadcrumb_search_result_text'    => __( 'Search Results', 'echo-knowledge-base' ),
					'help_dialog_breadcrumb_article_text'    => __( 'Article', 'echo-knowledge-base' ),
					'help_dialog_found_faqs_tab_text'    =>  __( 'Found FAQs', 'echo-knowledge-base' ),
					'help_dialog_fount_articles_tab_text'    => _( 'Found Articles', 'echo-knowledge-base' ),
					'help_dialog_found_faqs_article_tab_color'    => "#000000",
					'help_dialog_found_faqs_article_active_tab_color'    => "#0f9beb",

					// - Single Article
					'help_dialog_single_article_title_color'    => "#000000",
					'help_dialog_single_article_desc_color'    => "#424242",
					'help_dialog_single_article_read_more_text_color'    => "#0f9beb",
					'help_dialog_single_article_read_more_text_hover_color'    => "007eed",

			/******************************************************************************
			 *
			 *  Contact Us Tab
			 *
			 ******************************************************************************/
					'help_dialog_contact_us_top_tab'                        => __( 'Contact Us', 'echo-knowledge-base' ),
					'help_dialog_contact_title'                             => __( 'Get in Touch', 'echo-knowledge-base' ),
					'help_dialog_contact_name_text'                         => __( 'Name', 'echo-knowledge-base' ),
					'help_dialog_contact_user_email_text'                   => __( 'Email', 'echo-knowledge-base' ),
					'help_dialog_contact_subject_text'                      => __( 'Subject', 'echo-knowledge-base' ),
					'help_dialog_contact_comment_text'                      => __( 'How can we help you?', 'echo-knowledge-base' ),
					'help_dialog_contact_button_title'                      => __( 'Submit', 'echo-knowledge-base' ),
					'help_dialog_contact_submission_email'                  => '',
					'help_dialog_contact_success_message'                   => __( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' ),
					'help_dialog_contact_submit_button_color'               => "#aa2dd6",
					'help_dialog_contact_submit_button_hover_color'         => "#9039af",
					'help_dialog_contact_submit_button_text_color'          => "#ffffff",
					'help_dialog_contact_submit_button_text_hover_color'    => "#ffffff",
				);

			/**
			 * Standard theme (default)
			 */
			case 'standard':
			default:
				return array(

					/******************************************************************************
					 *
					 *  Setup
					 *
					 ******************************************************************************/
					// 'help_dialog_enable'                                 => 'on',
					// 'help_dialog_faqs_kb'                                => '1',
					'help_dialog_display_mode'                              => 'both',
					'help_dialog_logo_image_url'                            => Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png',
					'help_dialog_welcome_text'                              => __( 'Welcome to Support', 'echo-knowledge-base' ),
					//'help_dialog_container_desktop_height'                => '400',
					'help_dialog_container_desktop_width'                   => '400',
					'help_dialog_container_tablet_width'                    => '400',
					'help_dialog_container_mobile_width'                    => '400',
					'help_dialog_tablet_break_point'                        => '1025',
					'help_dialog_mobile_break_point'                        => '768',

					// - Top buttons
					'help_dialog_back_text_color'                           => "#7d7d7d",
					'help_dialog_back_text_color_hover_color'               => "#000000",
					'help_dialog_back_background_color'                     => "#f0f0f0",
					'help_dialog_back_background_color_hover_color'         => "#f0f0f0",


					/******************************************************************************
					 *
					 *  Launcher
					 *
					 ******************************************************************************/
					'help_dialog_launcher_start_delay'                      => '0',
					'help_dialog_launcher_background_color'                 => "#7b00a6",
					'help_dialog_launcher_background_hover_color'           => "#a5a5a5",
					'help_dialog_launcher_icon_color'                       => "#ffffff",
					'help_dialog_launcher_icon_hover_color'                 => "#000000",


					/******************************************************************************
					 *
					 *  FAQ List Tab
					 *
					 ******************************************************************************/
					'help_dialog_faqs_top_tab'                              => __( 'FAQs', 'echo-knowledge-base' ),
					'help_dialog_faqs_title'                                => __( 'How can we help you?', 'echo-knowledge-base' ),
					'help_dialog_faqs_search_placeholder'                   => __( 'Search for help', 'echo-knowledge-base' ),
					'help_dialog_article_read_more_text'                    => __( 'Read More', 'echo-knowledge-base' ),
					'help_dialog_background_color'                          => "#aa2dd6",
					'help_dialog_not_active_tab'                            => "#6d3687",
					'help_dialog_tab_text_color'                            => "#ffffff",
					'help_dialog_main_title_text_color'                     => "#FFFFFF",
					'help_dialog_welcome_text_color'                        => "#000000",
					'help_dialog_welcome_background_color'                  => "#f6deff",
					'help_dialog_breadcrumb_arrow_color'                    => "#000000",
					'help_dialog_faqs_qa_border_color'                      => "#CCCCCC",
					'help_dialog_faqs_question_text_color'                  => "#000000",
					'help_dialog_faqs_question_background_color'            => "#f7f7f7",
					'help_dialog_faqs_question_active_text_color'           => "#000000",
					'help_dialog_faqs_question_active_background_color'     => "#ffffff",
					'help_dialog_faqs_answer_text_color'                    => "#000000",
					'help_dialog_faqs_answer_background_color'              => "#ffffff",

					// - Search Results
					'help_dialog_breadcrumb_home_text'    => __( 'Home', 'echo-knowledge-base' ),
					'help_dialog_breadcrumb_search_result_text'    => __( 'Search Results', 'echo-knowledge-base' ),
					'help_dialog_breadcrumb_article_text'    => __( 'Article', 'echo-knowledge-base' ),
					'help_dialog_found_faqs_tab_text'    =>  __( 'Found FAQs', 'echo-knowledge-base' ),
					'help_dialog_fount_articles_tab_text'    => _( 'Found Articles', 'echo-knowledge-base' ),
					'help_dialog_found_faqs_article_tab_color'    => "#000000",
					'help_dialog_found_faqs_article_active_tab_color'    => "#0f9beb",

					// - Single Article
					'help_dialog_single_article_title_color'    => "#000000",
					'help_dialog_single_article_desc_color'    => "#424242",
					'help_dialog_single_article_read_more_text_color'    => "#0f9beb",
					'help_dialog_single_article_read_more_text_hover_color'    => "007eed",

					/******************************************************************************
					 *
					 *  Contact Us Tab
					 *
					 ******************************************************************************/
					'help_dialog_contact_us_top_tab'                        => __( 'Contact Us', 'echo-knowledge-base' ),
					'help_dialog_contact_title'                             => __( 'Get in Touch', 'echo-knowledge-base' ),
					'help_dialog_contact_name_text'                         => __( 'Name', 'echo-knowledge-base' ),
					'help_dialog_contact_user_email_text'                   => __( 'Email', 'echo-knowledge-base' ),
					'help_dialog_contact_subject_text'                      => __( 'Subject', 'echo-knowledge-base' ),
					'help_dialog_contact_comment_text'                      => __( 'How can we help you?', 'echo-knowledge-base' ),
					'help_dialog_contact_button_title'                      => __( 'Submit', 'echo-knowledge-base' ),
					'help_dialog_contact_submission_email'                  => '',
					'help_dialog_contact_success_message'                   => __( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' ),
					'help_dialog_contact_submit_button_color'               => "#aa2dd6",
					'help_dialog_contact_submit_button_hover_color'         => "#9039af",
					'help_dialog_contact_submit_button_text_color'          => "#ffffff",
					'help_dialog_contact_submit_button_text_hover_color'    => "#ffffff",
				);
		}
	}
}
