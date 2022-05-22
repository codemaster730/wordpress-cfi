<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Help Dialog configuration page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Help_Dialog_Config_Page {

	/**
	 * Displays the Help Dialog Config page with top panel
	 */
	public function display_page() {

		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
			return;
		}

		$all_locations = EPKB_FAQ_Utilities::get_help_dialog_location_categories_unfiltered();
		if ( empty( $all_locations ) ) {
			$admin_page_views = $this->get_empty_views_config();
		} else {
			$admin_page_views = $this->get_regular_views_config();
		}

		EPKB_HTML_Admin::admin_page_css_missing_message( true );

		if ( isset( $_GET['setup-wizard-on'] ) ) {
			$handler = new EPKB_Help_Dialog_Wizard_Setup();
			$handler->display_setup_wizard();
			return;
		}   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container epkb-help-dialog-config">    <?php

			/**
			 * ADMIN HEADER
			 */
			$help_dialog_header = EPKB_HTML_Admin::admin_help_dialog_header_content( [], '' );
			EPKB_HTML_Admin::admin_header( $help_dialog_header );

			/**
			 * ADMIN TOP PANEL
			 */
			EPKB_HTML_Admin::admin_toolbar( $admin_page_views );

			EPKB_FAQ_Utilities::show_remove_hd_notice();

			/**
			 * LIST OF SETTINGS IN TABS
			 */
			EPKB_HTML_Admin::admin_settings_tab_content( $admin_page_views, 'epkb-config-wrapper' );    ?>

			<div class="eckb-bottom-notice-message fadeOutDown"></div>
		</div>	    <?php
	}

	/**
	 * Show link to frontend Editor
	 *
	 * @return false | string
	 */
	private function frontend_editor_box() {

		ob_start();

		$location = EPKB_Help_Dialog_Handler::get_location_by_id_or_default();
		$first_location_page_url = EPKB_FAQ_Utilities::get_first_location_page_url( $location );

		if ( empty( $first_location_page_url ) ) { ?>
			<p><?php echo EPKB_Utilities::report_generic_error( 332 ); ?></p>  <?php

		} else {

			$editor_link = add_query_arg( [
				'action' => 'epkb_load_editor',
				'epkb_editor_type' => 'help-dialog',
				'preopen_zone' => 'help_dialog'
			], $first_location_page_url ); ?>

			<p><a href="<?php echo $editor_link; ?>" target="_blank"><?php _e( 'Configure', 'echo-knowledge-base' ); ?></a></p>  <?php
		}

		return ob_get_clean();
	}

	/**
	 * Get HTML for Email To Receive Contact Form Submissions box on Settings tab
	 *
	 * @return false|string
	 */
	private static function settings_tab_email_to_receive_box() {

		$epkb_hd_settings = epkb_get_instance()->help_dialog_settings_obj->get_settings_or_default();
		$hd_settings_specs = EPKB_Help_Dialog_Settings_Specs::get_fields_specification();

		ob_start();     ?>

		<div class="epkb-admin__text-field">
			<input type="<?php echo $hd_settings_specs['help_dialog_contact_submission_email']['type']; ?>" class="epkb-admin__input-field"
			       name="help_dialog_contact_submission_email"
			       value="<?php echo $epkb_hd_settings['help_dialog_contact_submission_email']; ?>"
			       maxlength="<?php echo $hd_settings_specs['help_dialog_contact_submission_email']['max']; ?>" />
		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Show actions row for Settings tab
	 *
	 * @return false|string
	 */
	private static function settings_tab_actions_row() {

		ob_start();		?>

		<div class="epkb-admin__list-actions-row"><?php
			EPKB_HTML_Elements::submit_button_v2( __( 'Save Settings', 'echo-knowledge-base' ), 'epkb_hd_save_settings_btn', 'epkb__hdl__action__save_order', '', true, '', 'epkb-success-btn');    ?>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * Show information if the user add first location
	 *
	 * @return false | string
	 */
	private static function no_location_notice() {
		ob_start();

		EPKB_HTML_Forms::notification_box_top( array(
			'type' => 'info',
			'title' => __( 'No Help Dialog location defined.', 'echo-knowledge-base' ),
			'desc' => '<p>' . __( 'Create a location before configuring it.', 'echo-knowledge-base' ) .
			          ' <a href="' . admin_url( 'admin.php?page=epkb-help-dialog-locations&epkb-help-dialog-location=0#location' ) . '">' . __( 'Click here', 'echo-knowledge-base' ) . '</a></p>',
		) );

		return ob_get_clean();
	}

	/**
	 * Get configuration array for empty views of Help Dialog Configuration page
	 *
	 * @return array[]
	 */
	private function get_empty_views_config() {

		return array(

			// VIEW: SETTINGS
			array(

				// Shared
				'active' => true,
				'list_key' => 'settings',

				// Top Panel Item
				'label_text' => __( 'Settings', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-cogs',

				// Boxes List
				'list_top_actions_html' => '',
				'boxes_list' => array(
					array(
						'html' => self::no_location_notice(),
						'class' => 'epkb-location-notice'
					),
				),
			),
		);
	}

	/**
	 * Get configuration array for regular views of Help Dialog Configuration page
	 *
	 * @return array[]
	 */
	private function get_regular_views_config() {

		return array(

			// VIEW: SETTINGS
			array(

				// Shared
				'active' => true,
				'list_key' => 'settings',

				// Top Panel Item
				'label_text' => __( 'Settings', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-cogs',

				// Boxes List
				'list_top_actions_html' => $this->settings_tab_actions_row(),
				'boxes_list' => array(

					// Box: Email To Receive Contact Form Submissions
					array(
						'title' => __( 'Email To Receive Contact Form Submissions', 'echo-knowledge-base' ),
						'description' => __( 'Enter an email in order to receive email notifications when a user submits a message through the Contact Us form.', 'echo-knowledge-base' ),
						'html' => self::settings_tab_email_to_receive_box(),
					)
				),
			),

			// VIEW: FRONTEND EDITOR
			array(

				// Shared
				'list_key' => 'frontend-editor',

				// Top Panel Item
				'label_text' => __( 'Frontend Editor', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-edit epkb-icon--black',

				// Boxes List
				'boxes_list' => array(

					// Box: frontend Editor link
					array(
						'title' => __( 'Configure text, fonts, colors, and style for Help Dialog', 'echo-knowledge-base' ),
						'html' => $this->frontend_editor_box(),
					),
				),
			),
		);
	}
}
