<?php

defined( 'ABSPATH' ) || exit();

/**
 * Display the Help dialog
 */
class EPKB_Help_Dialog_View {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'output_help_dialog'), 1, 2 );
	}

	public function output_help_dialog() {
		global $eckb_kb_id;

		$settings = epkb_get_instance()->settings_obj->get_settings_or_default();

		// do not show Help dialog if both FAQs and Contact are disabled
		if ( $settings['help_dialog_enable'] != 'on' ) {
			return;
		}

		// is this page or post to display the Help Dialog on?
		$page_id = get_the_ID();
		if ( empty($page_id) ) {
			return;
		}

		$kb_id = epkb_get_instance()->settings_obj->get_value( 'help_dialog_faqs_kb', EPKB_KB_Config_DB::DEFAULT_KB_ID );
		$kb_id = EPKB_Utilities::sanitize_kb_id( $kb_id );

		// If empty then display help dialog only on ALL KB / KB Article pages
		$pages_to_display_on = '';  // TODO use admin settings
		if ( empty($eckb_kb_id) && empty($pages_to_display_on) ) {
			return;
		}

		$is_editor_on = EPKB_Utilities::get( 'epkb-editor-page-loaded' ) == '1';
		if ( ! $is_editor_on && ! empty($pages_to_display_on) && ! in_array( $page_id, array_map('trim', explode(',', $pages_to_display_on)) ) ) {
			return;
		}

		$help_dialog_display_mode = $settings['help_dialog_display_mode'];		?>

		<script>
			var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
		</script> <?php

		do_action( 'epkb_enqueue_font_scripts');
		do_action( 'epkb_enqueue_help_dialog_scripts');  ?>

		<div id="eckb-help-dialog" style="display:none;">

			<!-- HEADER CONTAINER -->
			<div class="eckb-hd-header-container">
				<div class="eckb-hd-header__logo">
					<?php
					if ( $settings['help_dialog_logo_image_url'] != '' ) {	?>
						<img class="eckb-hd-header__logo__img" src="<?php echo $settings['help_dialog_logo_image_url']; ?>">
						<?php
					} ?>
				</div>
				<div class="eckb-hd-header__title"> <?php
					if ( $help_dialog_display_mode != 'contact' ) {
						echo '<div class="eckb-hd-header__title__faq">'.$settings['help_dialog_faqs_title'].'</div>';
					}
					if ( $help_dialog_display_mode != 'faqs' ) {
						echo '<div class="eckb-hd-header__title__contact">'.$settings['help_dialog_contact_title'].'</div>';
					} ?>
				</div>
				<div class="eckb-hd-header__button-container"><?php
						if ( $help_dialog_display_mode != 'faqs' ) {	?>

							<div class="eckb-hd-button eckb-hd-button__contact-btn">
								<span class="eckb-hd-button__contact-btn__text"><?php echo $settings['help_dialog_contact_top_button_title']; ?></span>
								<span class="eckb-hd-button__contact-btn__icon epkbfa epkbfa-comment"></span>
							</div>
							<?php
						}
						if ( $help_dialog_display_mode != 'contact' ) {	?>
							<div class="eckb-hd-button eckb-hd-button__faq-btn">
								<span class="eckb-hd-button__faq-btn__text"><?php echo $settings['help_dialog_faqs_top_button_title']; ?></span>
								<span class="eckb-hd-button__faq-btn__icon epkbfa epkbfa-search"></span>
							</div>
							<?php
						} ?>
				</div>
			</div>

			<!-- BODY CONTAINER -->
			<div class="eckb-hd-body-container">

				<!-- Left Container -->
				<div class="eckb-hd-body__left-content-container">
					<!-- FAQ List -->
					<div class="eckb-hd-faq-container">

						<div class="eckb-hd-faq__header">
							<h2 class="eckb-hd-faq__header__title"><?php echo __('How can we help you?', 'echo-knowledge-base'); ?></h2>
						</div>

						<div class="eckb-hd-faq__list">
							<?php self::display_faqs_box();  ?>
						</div>


					</div>

					<!-- Search Box -->
					<?php self::display_search_input_box( $kb_id, $settings ); //TODO - kb_id ?>
				</div>

				<!-- Right Container -->
				<div class="eckb-hd-body__right-content-container">

					<div class="eckb-hd-kb-articles-container">
						<div class="eckb-hd-kb-articles__header">
							<h2 class="eckb-hd-kb-articles__header__title"><?php echo __('KB Articles Found', 'echo-knowledge-base'); ?></h2>
							<div class="eckb-hd__loading-spinner"></div>
						</div>
						<?php self::search_result_box();  ?>
					</div>
				</div>

				<!-- KB Articles -->
				<?php
				/*if ( $help_dialog_display_mode != 'contact' ) {	?>
					<div class="eckb-help_dialog__search">
						<?php //self::display_search_input_and_results_box( $kb_id, $settings ); //TODO - kb_id ?>
					</div> <?php
				}*/
				if ( $help_dialog_display_mode != 'faqs' ) {	?>

						<?php self::display_contact_box( $settings );
				} ?>

			</div>

			<!-- FOOTER CONTAINER -->
			<div id="eckb-hd__footer">
				<?php echo __( 'Powered By', 'echo-knowledge-base' ); ?>
				<img class="eckb-hd__kb_icon" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png'; ?>">
				<a href="https://www.echoknowledgebase.com/" target="_blank"><?php echo __( 'Echo Knowledge Base', 'echo-knowledge-base' ); ?></a>
			</div>
		</div>
		<div class="eckb-hd-toggle eckb-hd-toggle__<?php echo $settings['help_dialog_launcher_when_to_display']; ?>" style="display:none;">
			<i class="epkbfa epkbfa-comments-o"></i>
		</div>
		<style id="help-dialog-styles">
			#eckb-help-dialog {
				background-color: <?php echo $settings['help_dialog_background_color']; ?>;
			}

			/* Launcher */
			.eckb-hd-toggle {
				background-color: <?php echo $settings['help_dialog_launcher_background_color']; ?>;
			}
			.eckb-help-dialog-toggle:hover {
				background-color: <?php echo $settings['help_dialog_launcher_background_hover_color']; ?>;
			}
			.eckb-hd-toggle {
				color: <?php echo $settings['help_dialog_launcher_icon_color']; ?>;
			}
			.eckb-hd-toggle:hover {
				color: <?php echo $settings['help_dialog_launcher_icon_hover_color']; ?>;
			}

			/* General*/
			.eckb-hd-body-container {
				color: <?php echo $settings['help_dialog_text_color']; ?>;
			}
			.eckb-hd-body-container .epkb-hd_cat-item:hover {
				color: <?php echo $settings['help_dialog_text_hover_color']; ?>;
			}
			.eckb-hd-body-container .epkb-hd_article-item:hover {
				color: <?php echo $settings['help_dialog_text_hover_color']; ?>;
			}

			/* Back Navigation */
			.eckb-hd__header .eckb-hd__header-back-icon {
				background-color: <?php echo $settings['help_dialog_back_icon_bg_color']; ?>;
				color: <?php echo $settings['help_dialog_back_icon_color']; ?>;
			}
			.eckb-hd__header .eckb-hd__header-back-icon:hover {
				background-color: <?php echo $settings['help_dialog_back_icon_bg_color_hover_color']; ?>;
				color: <?php echo $settings['help_dialog_back_icon_color_hover_color']; ?>;
			}

			/* Read More */
			.eckb-hd-body-container .epkb-hd_article-link {
				color: <?php echo $settings['help_dialog_faqs_read_more_text_color']; ?>;
			}
			.eckb-hd-body-container .epkb-hd_article-link:hover {
				color: <?php echo $settings['help_dialog_faqs_read_more_text_hover_color']; ?>;
			}

			/* Header Toggle Button */
			.eckb-hd-button {
				background-color: <?php echo $settings['help_dialog_top_button_color']; ?>;
				color: <?php echo $settings['help_dialog_top_button_text_color']; ?>;
			}
			.eckb-hd-button:hover {
				background-color: <?php echo $settings['help_dialog_top_button_hover_color']; ?>;
				color: <?php echo $settings['help_dialog_top_button_text_hover_color']; ?>;
			}

			/* Contact Form */
			.epkb-hd__contact-form-btn {
				background-color: <?php echo $settings['help_dialog_contact_button_color']; ?>!important;
				color: <?php echo $settings['help_dialog_contact_button_text_color']; ?>!important;
			}
			.epkb-hd__contact-form-btn:hover {
				background-color: <?php echo $settings['help_dialog_contact_button_hover_color']; ?>!important;
				color: <?php echo $settings['help_dialog_contact_button_text_hover_color']; ?>!important;
			}

		</style>	<?php
	}

	private function display_faqs_box() {

		$faqs = [
			1   => [
				'question' => 'I am looking for a solution to manage shared inboxes',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. '
			],
			2   => [
				'question' => 'Just checking out the content',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. '
			],
			3   => [
				'question' => 'I already use KB and want to talk to support',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate the visual form of a document or a typeface without relying on meaningful content. '
			],
			4   => [
			'question' => 'I have a Question',
			'answer' => 'In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 '
		],
			5   => [
				'question' => 'I want to know how Gmail can be used as a Help Desk',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 '
			],
			6   => [
				'question' => 'Anything to do with productivity',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 '
			],
			7   => [
				'question' => 'After my license expires what will happen?',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 '
			],
			8   => [
				'question' => 'Can I request a refund?',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 '
			],
			9   => [
				'question' => 'Will Echo Knowledge Base and its add-ons work with WordPress.com?',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 '
			],
			10  => [
				'question' => 'What are subscriptions?',
				'answer' => 'In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 
						 In publishing and graphic design, Lorem ipsum is a 
						 placeholder text commonly used to demonstrate the visual form of a document 
						 or a typeface without relying on meaningful content. 
						 '
			],
		]; ?>

			<!----- faq Box ------>
			<?php

			foreach ( $faqs as $faq ) { ?>

				<div class="eckb-hd-faq__list__item-container">
					<div class="eckb-hd__item__question">
						<?php echo $faq['question']; ?>
					</div>
					<div class="eckb-hd__item__answer">

						<div class="eckb-hd__item__answer__text">
							<?php echo $faq['answer']; ?>
						</div>

						<div class="eckb-hd__item__answer__link">
							<a href="">Learn More</a>
						</div>

					</div>


				</div> <?php
			}
			?>

		 <?php

	}

	/**
	 * Display Search Input and Results
	 *
	 * @param $kb_id
	 * @param $settings
	 */
	private function display_search_input_box( $kb_id, $settings ) {    ?>

		<div class="eckb-hd-search-container">

			<!----- Search Box ------>
			<div class="epkb-hd__search-box">
				<span class="eckb-hd__search-box_title"><?php echo __('Search for article', 'echo-knowledge-base'); ?></span>
				<form id="epkb-hd__search-form"  method="post" action="" onSubmit="return false;">
					<input type="text" id="epkb-hd__search-terms" name="epkb-hd__search-terms" value=""
						   placeholder="<?php echo $settings['help_dialog_faqs_search_placeholder']; ?>" data-kb-id="<?php echo $kb_id; ?>" />
				</form>
			</div>

		</div>		<?php
	}

	private function search_result_box() { ?>
		<!----- Search Box Results ------>
		<div class="epkb-hd-search-results-container">

			<div id="epkb-hd__search_results" class="epkb-hd-search-results__article-list" data-step="1"></div>

			<div id="epkb-hd__cat" class="epkb-hd__search_step epkb-hd__search_step_active" data-step="2"></div>

			<div id="epkb-hd__cat-article" class="epkb-hd__search_step" data-step="3"></div>

			<div id="epkb-hd__search_results-cat-article-details" class="epkb-hd__search_step" data-step="4"></div>


		</div> <?php

	}

	/**
	 * Display Contact Box
	 * @param $settings
	 */
	private function display_contact_box( $settings ) {    ?>

		<div id="epkb-hd-body__contact-container">
			<form id="epkb-hd__contact-form" method="post" enctype="multipart/form-data">				<?php
				wp_nonce_field( '_epkb_help_dialog_contact_form_nonce' );				?>
				<input type="hidden" name="action" value="epkb_help_dialog_contact" />
				<div id="epkb-hd__contact-form-body">

					<div class="epkb-hd__contact-form-field">
						<input name="user_first_name" type="text" value="" required  id="epkb-hd__contact-form-user_first_name" placeholder="<?php echo $settings['help_dialog_contact_name_placeholder']; ?>">
					</div>

					<div class="epkb-hd__contact-form-field">
						<input name="email" type="email" value="" required id="epkb-hd__contact-form-email" placeholder="<?php echo $settings['help_dialog_contact_user_email_placeholder']; ?>">
					</div>

					<div class="epkb-hd__contact-form-field">
						<input name="subject" type="text" value="" required id="epkb-hd__contact-form-subject" placeholder="<?php echo $settings['help_dialog_contact_subject_placeholder']; ?>">
					</div>

					<div class="epkb-hd__contact-form-field">
						<textarea name="comment" required id="epkb-hd__contact-form-comment" placeholder="<?php echo $settings['help_dialog_contact_comment_placeholder']; ?>"></textarea>
					</div>

					<div class="epkb-hd__contact-form-btn-wrap">
						<input type="submit" name="submit" value="<?php echo $settings['help_dialog_contact_button_title']; ?>" class="epkb-hd__contact-form-btn">
					</div>

					<div class="epkb-hd__contact-form-response"></div>
				</div>
			</form>
			<div class="eckb-hd__loading-spinner"></div>
		</div>		<?php
	}

	public static function is_help_dialog_enabled() {
		return false;

		/* TODO $settings = epkb_get_instance()->settings_obj->get_settings_or_default();
		return $settings['help_dialog_enable'] == 'on'; */
	}
}