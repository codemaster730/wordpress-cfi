<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Help Dialog Submissions page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Help_Dialog_Submissions_Page {

	private $current_submissions = [];
	private $total_submissions_number = 0;

	private $messages = array(); // error/warning/success messages

	/**
	 * Displays the Help Dialog Submissions page with top panel
	 */
	public function display_page() {

		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			echo '<p>' . __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) . '</p>';
			return;
		}

		$this->get_submissions_data();

		$admin_page_views = $this->get_regular_views_config();

		EPKB_HTML_Admin::admin_page_css_missing_message( true );    ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container epkb-admin__help-dialog__submissions epkb-admin__help-dialog__submissions--hide-toolbar">    <?php

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
			EPKB_HTML_Admin::admin_settings_tab_content( $admin_page_views, 'epkb-submissions-wrapper' );   ?>

		</div>      <?php

		/**
		* Show any notifications
		*/
		foreach ( $this->messages as $class => $message ) {
			echo  EPKB_Utilities::get_bottom_notice_message_box( $message, '', $class );
		}   ?>
		<div class="eckb-bottom-notice-message fadeOutDown"></div>  <?php
	}

	/**
	 * Retrieve all submissions
	 */
	private function get_submissions_data() {

		$handler = new EPKB_Help_Dialog_Submissions_DB();
		$this->current_submissions = $handler->get_submissions();
		if ( is_wp_error($this->current_submissions) ) {
			$this->messages['error'] = EPKB_Utilities::report_generic_error( 411, $this->current_submissions );
			$this->current_submissions = [];
		}

		$this->total_submissions_number = $handler->get_total_number_of_submissions();
	}

	/**
	 * Get configuration array for regular views of Help Dialog Submissions admin page
	 *
	 * @return array[]
	 */
	private function get_regular_views_config() {

		return array(

			// VIEW: Overview
			array(
				'active' => true,

				// Shared
				'list_key' => 'submissions',

				// Top Panel Item
				'label_text' => __( 'Submissions', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-envelope-o epkb-icon--black',

				// Boxes List
				'list_bottom_actions_html' => count( $this->current_submissions ) > 0 ? self::get_submissions_actions() : '',
				'boxes_list' => array(

					// Box: Submissions
					array(
						'class' => 'epkb-admin__submissions-list',
						'title' => __( 'Contact Us Entries', 'echo-knowledge-base' ),
						'description' => $this->get_submissions_list_description(),
						'html' => EPKB_HTML_Forms::get_html_table(
									$this->current_submissions,
									$this->total_submissions_number,
									EPKB_Help_Dialog_Submissions_DB::PRIMARY_KEY,
									EPKB_Help_Dialog_Submissions_DB::get_submission_column_fields(),
									EPKB_Help_Dialog_Submissions_DB::get_submission_row_fields(),
									EPKB_Help_Dialog_Submissions_DB::get_submission_optional_row_fields(),
									EPKB_Help_Dialog_Submissions_Ctrl::DELETE_ACTION,
									EPKB_Help_Dialog_Submissions_Ctrl::LOAD_MORE_ACTION
						),
					),
				),
			)
		);
	}

	/**
	 * Get actions for Submissions view
	 *
	 * @return false|string
	 */
	private static function get_submissions_actions() {

		ob_start();		?>

		<!-- Delete All Items -->
		<div class="epkb-admin__list-actions-row">    <?php

			EPKB_HTML_Elements::submit_button_v2( __( 'Clear Table', 'echo-knowledge-base' ), '', 'epkb-admin__items-list__delete-all', '', '', '', 'epkb-error-btn' );

			// Dialog box form
			EPKB_Utilities::dialog_box_form( array(
				'id' => 'epkb-admin__items-list__delete-all_confirmation',
				'title' => __( 'Deleting Submissions', 'echo-knowledge-base' ),
				'body' => __( 'Are you sure you want to delete all submissions? You cannot undo this action.', 'echo-knowledge-base' ),
				'accept_label' => __( 'Delete', 'echo-knowledge-base' ),
				'accept_type' => 'warning',
				'form_inputs' => array(
					'<input type="hidden" name="action" value="' . EPKB_Help_Dialog_Submissions_Ctrl::DELETE_ALL_ACTION . '">',
				)
			) );    ?>

		</div>  <?php

		return ob_get_clean();
	}

	/**
	 * Get description for Submissions list
	 *
	 * @return false|string
	 */
	private function get_submissions_list_description() {

		ob_start();     ?>

		<span><?php _e( 'Listed are user submissions from the Contact Us form in the Help Dialog. Total submissions found: ', 'echo-knowledge-base' ); ?></span>
		<span class="epkb-admin__items-list__totally-found"><?php echo $this->total_submissions_number; ?></span><?php

		return ob_get_clean();
	}
}
