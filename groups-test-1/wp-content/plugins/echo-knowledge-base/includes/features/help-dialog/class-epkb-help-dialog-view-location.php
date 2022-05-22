<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display KB configuration for Help Dialog
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Help_Dialog_View_Location {

	/**
	 * Show LOCATIONs tab
	 * @return false|string
	 */
	public static function show_help_dialog() {
		ob_start();
		$html = New EPKB_HTML_Elements();		?>

		<div class="epkb-help-dialog-location-container">

			<div class="epkb-help-dialog-location-header-container">
				<h3 class="epkb-location-header__title">Locations</h3>
				<div class="epkb-location-header__add">				<?php
					$html->submit_button( 'Add Location', 'epkb-location-add', 'epkb-location-add-location-btn', '', true, '', 'epkb-primary-btn' );				?>
				</div>
			</div>

			<!-- Add Location Form -->
			<form method="post" id="epkb-add-location-form" class="epkb-form" action="">

				<div class="epkb-hd-keywords__title">Display on These Pages</div>
				<p>Define where to display Help Dialog and what FAQs it will show.</p>
				<div class="epkb-hd-keywords__input-group">
					<div class="epkb_search_posts_form">
						<ul>
							<li class="epkb_selected_posts"></li>
							<li class="epkb_search_input">
								<label for="epkb_help_dialog_display_settings">Choose Page</label>
								<input type="text" class="epkb__search_posts" placeholder="<?php _e('Enter page name...', 'echo-knowledge-base'); ?>" data-post_type="only_pages"/><span class="spinner"></span>
								<input type="hidden" id="epkb_help_dialog_display_settings" name="epkb_help_dialog_display_settings" value="" />
							</li>
							<li class="epkb__live_search_res">
								<ul class="epkb_search_results"></ul>
							</li>
						</ul>
						<input type="hidden" id="_wpnonce_epkb_post_search" name="_wpnonce_epkb_post_search" value="<?php echo wp_create_nonce( "_wpnonce_epkb_post_search" ); ?>"/>
					</div>
					<div class="epkb_select_faqs_form">
						<label for="post-keywords-post-id">Questions</label>					<?php

						$faqs = EPKB_Utilities::get_wp_option( EPKB_Help_Dialog_Ctrl_FAQ::OPTION_EPKB_FAQS, array() );
						if ( is_wp_error( $faqs ) ) {
							EPKB_Logging::add_log( 'Error retrieving Questions', $faqs );
							EPKB_Utilities::ajax_show_error_die(__( 'Error retrieving Questions', 'echo-knowledge-base' ) . ' (27)');
						}

						// update the FAQ
						$faqs_list = [];
						foreach( $faqs as $ix => $faq ) {
							$faqs_list[$faq['id']] = $faq['question'];
						}
						$html->checkboxes_multi_select(	array( 'name' => 'epkb-question-checkbox', 'options' => $faqs_list) );					?>

						<input type="hidden" id="epkb-faq-location" value="0">
					</div>
				</div>

				<div class="epkb-form-action-container">						<?php
					$html->submit_button( 'Create','epkb_create_location','epkb-hd-create-location__btn', '', true, '', 'epkb-success-btn' );
					$html->submit_button( 'Update','epkb_update_location','epkb-hd-update-location__btn', '', true, '', 'epkb-primary-btn' );
					$html->submit_button( 'Cancel','epkb_cancel_location','epkb-hd-cancel-location__btn', '', true, '', 'epkb-error-btn' );						?>
				</div>

			</form>

		</div>

		<!-- Display Locations -->			<?php
		self::display_list_of_records();

		return ob_get_clean();
	}

	/**
	 * Display existing LOCATIONs in a list
	 *
	 * @return bool
	 */
	public static function display_list_of_records() {

		$html = NEW EPKB_HTML_Elements();
		$locations = EPKB_Utilities::get_wp_option( EPKB_Help_Dialog_Ctrl_LOCATION::OPTION_EPKB_LOCATIONS, array() );
		if ( is_wp_error( $locations ) ) {
			EART_Logging::add_log( 'Error retrieving LOCATIONs', $locations );
			return false;
		}   ?>

		<div class="epkb-locations-list">
			<section class="epkb-list"><?php

			/*	$html->table(
					array(
						'headings'          => [ __( 'Location', 'echo-knowledge-base' ), __( 'Answer', 'echo-knowledge-base' ), __( 'Action', 'echo-knowledge-base' ) ],
						'row_id_name'       => 'id',
						'rows'              => $locations,
						'column_keys'       => ['location', 'answer'],
						'container_class'   => '',
						'container_ID'      => 'epkb-current-list-locations',
						'buttons'           => [[ 'label' => 'Edit', 'action' => 'epkb_edit_location', 'main_class' => 'epkb-create-location-location-edit-btn', 'unique' => false, 'input_class' => 'epkb-primary-btn' ],
												[ 'label' => 'Delete', 'action' => 'epkb_delete_location', 'main_class' => 'epkb-create-location-location-delete-btn', 'unique' => false , 'input_class' => 'epkb-error-btn']],
						'colSizes'          => array(
							'col1' => 10,
							'col2' => 40,
							'col3' => 20,
							'col4' => 20,
							'col5' => 10,
						),
					)
				);		*/		?>

				<input type="hidden" id="_wpnonce_epkb_location" name="_wpnonce_epkb_location" value="<?php echo wp_create_nonce( "_wpnonce_epkb_location" ); ?>"/>

			</section>
		</div>    <?php

		return true;
	}
}
