<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Need Help page for Help Dialog
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Help_Dialog_Need_Help_Page {

	/**
	 * Display Need Help page for Help Dialog
	 */
	public function display_need_help_page() {

		$admin_page_views = $this->get_regular_views_config();

		EPKB_HTML_Admin::admin_page_css_missing_message( true );   ?>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-kb-need-help">    <?php

			// Notification after successful completion of Setup Wizard
			if ( isset( $_GET['epkb_after_kb_setup'] ) ) {  ?>
				<div id="epkb-kb__need-help__after-setup-wizard-dialog">   <?php
					EPKB_HTML_Forms::notification_box_top( array(
						'type'              => 'success',
						'title'             => __( 'Congratulations!', 'echo-knowledge-base' ),
						'desc'              => __( 'Your Knowledge Base setup is complete. You can now add your articles and customize the KB.', 'echo-knowledge-base' ),
						'button_confirm'    => __( 'OK', 'echo-knowledge-base' ),
						'close_target'      => '#epkb-kb__need-help__after-setup-wizard-dialog',
					) );    ?>
				</div>      <?php
			}

			/**
			 * ADMIN HEADER (KB logo and list of KBs dropdown)
			 */
			EPKB_HTML_Admin::admin_header( EPKB_HTML_Admin::get_help_dialog_admin_header_logo() );

			/**
			 * ADMIN TOOLBAR
			 */
			EPKB_HTML_Admin::admin_toolbar( $admin_page_views );

			EPKB_FAQ_Utilities::show_remove_hd_notice();

			/**
			 * LIST OF SETTINGS IN TABS
			 */
			EPKB_HTML_Admin::admin_settings_tab_content( $admin_page_views );    ?>

			<div class="eckb-bottom-notice-message"></div>
		</div>	    <?php
	}

	/**
	 * Get HTML for Getting Started tab
	 *
	 * @return false|string
	 */
	private function getting_started_tab() {

		// Setup Wizard
		$steps_list = [];
		/* TODO $steps_list = array(
			array(
				'icon_class'    => '',
				'title'         => __( '1. Setup Wizard', 'echo-knowledge-base' ),
				'desc'          => __( 'Set up the demo FAQs and choose a pre-made design.', 'echo-knowledge-base' ),
				'html'          => EPKB_HTML_Admin::get_admin_page_link( 'page=epkb-help-dialog-config&setup-wizard-on', __( 'Launch Setup Wizard', 'echo-knowledge-base' ) ),
			)
		); */

		// Frontend Editor
		$steps_list[] = array(
			'icon_class'    => '',
			'title'         => __( '1. Customize Colors, Labels, and Fonts', 'echo-knowledge-base' ),
			'desc'          => __( 'Easily change the style and look of the Help Dialog with our frontend Editor.', 'echo-knowledge-base' ),
			'html'          => '<a class="epkb-kb__wizard-link" href="' . home_url( '?action=epkb_load_editor&epkb_editor_type=help-dialog&preopen_zone=help_dialog' ) . '" target="_blank">' . __( 'Customize Help Dialog', 'echo-knowledge-base' ) . '</a>' );

		// Create a new Location & Set a list of FAQs
		$steps_list[] = array(
			'icon_class'    => '',
			'title'         => __( '2. Enter Your FAQs', 'echo-knowledge-base' ),
			'desc'          => __( 'Choose pages on which to display your Help Dialog. Then create a list of FAQs to display in the Help Dialog.', 'echo-knowledge-base' ),
			'html'          => EPKB_HTML_Admin::get_admin_page_link( 'page=epkb-help-dialog-config&epkb-help-dialog-location=0#location', __( 'Create a New Location', 'echo-knowledge-base' ) ) .
								EPKB_HTML_Admin::get_admin_page_link( 'page=epkb-help-dialog-config#questions', __( 'Create FAQs', 'echo-knowledge-base' ) ) );

		ob_start();     ?>

		<div class="epkb-kbnh__getting-started-container">

			<!-- Getting Started - header container  -->
			<div class="epkb-kbnh__gs__header-container">
				<div class="epkb-kbnh__header__img">
					<img src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/guy-on-laptop.jpg' ?>">
				</div>
				<div class="epkb-kbnh__header__text">
					<h2 class="epkb-kbnh__header__title"><?php _e( 'Welcome to Help Dialog!', 'echo-knowledge-base' ); ?></h2>
					<p class="epkb-kbnh__header__desc"><?php _e( 'Thank you for trying Help Dialog.', 'echo-knowledge-base' ); ?></p>
					<ul>
						<li>Help your customers by providing FAQs tailored to your pages.</li>
						<li>The Help Dialog also comes with a KB search and Contact Us form.</li>
					</ul>
				</div>
			</div>

			<!-- Gettings Started - content container -->
			<div class="epkb-admin__content">   <?php

				foreach ( $steps_list as $step ) {
					EPKB_HTML_Forms::display_step_cta_box( $step );
				}   ?>

			</div>

		</div>		<?php

		return ob_get_clean();
	}

	/**
	 * Get configuration for regular views
	 *
	 * @return array
	 */
	private function get_regular_views_config() {

		return array(

			// VIEW: Getting Started
			array(

				// Shared
				'active' => true,
				'list_key' => 'getting-started',

				// Top Panel Item
				'label_text' => __( 'Getting Started', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-graduation-cap',

				// Boxes List
				'boxes_list' => array(

					// Box: Getting Started
					array(
						'html' => $this->getting_started_tab(),
					),
				),
			),

			// VIEW: Contact Us
			EPKB_Need_Help_Contact_Us::get_page_view_config(),
		);
	}
}
