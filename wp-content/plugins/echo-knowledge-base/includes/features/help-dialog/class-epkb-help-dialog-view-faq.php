<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display KB configuration for Help Dialog
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Help_Dialog_View_FAQ {

	/**
	 * Show Overview Tab
	 * @return false|string
	 */
	public static function show_help_dialog_overview() {
		ob_start();
		$html = New EPKB_HTML_Elements();		?>

		<div class="epkb-help-dialog-overview-container">

			<div class="epkb-help-dialog-overview-header-container">
				<h3 class="epkb-overview-header__title">Overview</h3>
			</div>

			<!-- Enable switch -->
			<div id="epkb-help-dialog-overview-form-container">
				<form method="post" id="epkb-overview-form" class="epkb-form" action="">
					<div class="epkb-overview-checkbox-container">									<?php

						$html->checkbox_toggle(array(
							'id'    => 'epkb_help_dialog_enable',
							'text'  => 'Enable',
							'data'  => 'enable',
							'textLoc'  => 'left',
							'checked' => EPKB_Help_Dialog_View::is_help_dialog_enabled(),
						));						?>

						<input type="hidden" id="_wpnonce_epkb_enable_help_dialog" name="_wpnonce_epkb_enable_help_dialog" value="<?php echo wp_create_nonce( "_wpnonce_epkb_enable_help_dialog" ); ?>"/>
					</div>
					<div class="epkb-overview-checkbox-note">
						<span class="epkbfa epkbfa-info-circle"></span> Note that Help Dialog feature is in BETA. We might change the way we store or present its data. <?php echo EPKB_Utilities::contact_us_for_feedback(); ?>
					</div>
				</form>

			</div>
		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Show FAQs tab
	 * @return false|string
	 */
	public static function show_help_dialog() {
		ob_start();
		$html = New EPKB_HTML_Elements();		?>

		<div class="epkb-help-dialog-faq-container">

			<div class="epkb-help-dialog-faq-header-container">
				<h3 class="epkb-faq-header__title">Questions</h3>
				<div class="epkb-faq-header__add">				<?php
					$html->submit_button( 'Add Question', 'epkb-faq-add', 'epkb-faq-add-question-btn', '', true, '', 'epkb-primary-btn' );				?>
				</div>
			</div>

			<!-- Add Question Form -->
			<form method="post" id="epkb-add-faq-form" class="epkb-form" action="">

				<fieldset class="epkb-form-fields-container">
					<legend class="epkb-fields-legend">Enter Your New Question</legend>	<?php
					$html->text( array(
						'name'              => 'epkb-question-input',
						'label'             => 'Question',
						'value'             => '',
						'main_tag'          => 'div',
						'input_group_class' => 'epkb-input-field',
						'max'               => '500'
					) );
					$html->textarea( array(
						'name'              => 'epkb-answer-input',
						'label'             => 'Answer',
						'value'             => '',
						'main_tag'          => 'div',
						'input_group_class' => 'epkb-input-field',
					) );
					echo '<span>Accepts HTML</span>'; ?>

					<input type="hidden" id="epkb-faq-id" value="0">
				</fieldset>

				<div class="epkb-form-action-container">						<?php
					$html->submit_button( 'Create','epkb_create_faq','epkb-hd-create-question__btn', '', true, '', 'epkb-success-btn' );
					$html->submit_button( 'Update','epkb_update_faq','epkb-hd-update-question__btn', '', true, '', 'epkb-primary-btn' );
					$html->submit_button( 'Cancel','epkb_cancel_faq','epkb-hd-cancel-question__btn', '', true, '', 'epkb-error-btn' );						?>
				</div>

			</form>

		</div>

		<!-- Display Questions -->			<?php
		self::display_list_of_records();

		return ob_get_clean();
	}

	/**
	 * Display existing FAQs in a list
	 *
	 * @return bool
	 */
	public static function display_list_of_records() {

		$html = NEW EPKB_HTML_Elements();
		$faqs = EPKB_Utilities::get_wp_option( EPKB_Help_Dialog_Ctrl_FAQ::OPTION_EPKB_FAQS, array() );
		if ( is_wp_error( $faqs ) ) {
			EART_Logging::add_log( 'Error retrieving FAQs', $faqs );
			return false;
		}   ?>

		<div class="epkb-faqs-list">
			<section class="epkb-list"><?php

				$html->table(
					array(
						'headings'          => [ __( 'Question', 'echo-knowledge-base' ), __( 'Answer', 'echo-knowledge-base' ), __( 'Action', 'echo-knowledge-base' ) ],
						'row_id_name'       => 'id',
						'rows'              => $faqs,
						'column_keys'       => ['question', 'answer'],
						'container_class'   => '',
						'container_ID'      => 'epkb-current-list-faqs',
						'buttons'           => [[ 'label' => 'Edit', 'action' => 'epkb_edit_faq', 'main_class' => 'epkb-create-faq-question-edit-btn', 'unique' => false, 'input_class' => 'epkb-primary-btn' ],
												[ 'label' => 'Delete', 'action' => 'epkb_delete_faq', 'main_class' => 'epkb-create-faq-question-delete-btn', 'unique' => false , 'input_class' => 'epkb-error-btn']],
						'colSizes'          => array(
							'col1' => 10,
							'col2' => 40,
							'col3' => 20,
							'col4' => 20,
							'col5' => 10,
						),
					)
				);				?>

				<input type="hidden" id="_wpnonce_epkb_faq" name="_wpnonce_epkb_faq" value="<?php echo wp_create_nonce( "_wpnonce_epkb_faq" ); ?>"/>

			</section>
		</div>    <?php

		return true;
	}
}
