<?php
defined( 'ABSPATH' ) || exit();

/**
 * Display the Help dialog
 */
class EPKB_Help_Dialog_View {

	public function __construct() {
		add_action( 'wp_footer', array( $this, 'output_help_dialog'), 1, 2 );
	}

	/**
	 * Display Help Dialog on the current page
	 */
	public function output_help_dialog() {

		if ( defined( 'E'.'PHD_PLUGIN_NAME' ) ) {
			return;
		}

		// should the help dialog be displayed on this page?
		$location_category = self::is_display_dialog();
		if ( empty( $location_category ) || empty( $location_category->location_id ) ) {
			return;
		}

		do_action( 'epkb_enqueue_font_scripts' );
		do_action( 'epkb_enqueue_help_dialog_scripts' );

		$this->display_help_dialog( $location_category );
	}

	/**
	 * Display the Help Dialog box on frontend or admin pages.
	 * @param $location_category
	 */
	private function display_help_dialog( $location_category ) {

		$settings = epkb_get_instance()->help_dialog_settings_obj->get_settings_or_default();

		$help_dialog_display_mode = $settings['help_dialog_display_mode'];
		$activeWPTheme = 'eckb_hd_active_theme_'. EPKB_Utilities::get_wp_option( 'stylesheet', 'unknown' );		?>

		<div id="eckb-help-dialog" class="<?php echo $activeWPTheme; ?>" style="display:none;" data-tab="<?php echo $help_dialog_display_mode == 'contact' ? 'contact' : 'faqs'; ?>" data-step="1" data-sub-step="1"><?php 
			if ( $help_dialog_display_mode == 'both' ) { ?>
				<!-- TAB CONTAINER -->
				<div id="eckb-hd-top-tab-container" role="tablist" aria-label="Help Dialog Top Tabs"><?php

					if ( $help_dialog_display_mode != 'contact' ) {	?>
						<div id="eckb-hd-faq-tab" role="tab" aria-selected="true" tabindex="0" class="eckb-hd-tab eckb-hd-tab__faq-btn eckb-hd-tab--active" data-target-tab="faqs">
							<span class="eckb-hd-tab__faq-btn__text"><?php echo $settings['help_dialog_faqs_top_tab']; ?></span>
						</div>      <?php
					}

					if ( $help_dialog_display_mode != 'faqs' ) {	?>
						<div id="eckb-hd-contact-us-tab" role="tab" aria-selected="false" tabindex="-1" class="eckb-hd-tab eckb-hd-tab__contact-btn" data-target-tab="contact">
							<span class="eckb-hd-tab__contact-btn__text"><?php echo $settings['help_dialog_contact_us_top_tab']; ?></span>
						</div>      <?php
					} ?>

				</div><?php
			} ?>

			<!-- HEADER CONTAINER -->
			<div id="eckb-hd-header-container">

				<div class="eckb-hd-header__title-container">					<?php

					if ( ! empty( $settings['help_dialog_logo_image_url'] ) ) {	?>
						<div class="eckb-hd-header__logo">
								<img class="eckb-hd-header__logo__img" src="<?php echo $settings['help_dialog_logo_image_url']; ?>">
						</div>      <?php
					} ?>

					<div class="eckb-hd-header__title">
						<div class="eckb-hd-header__title__faq"><?php echo $settings['help_dialog_welcome_text']; ?></div>
					</div>

				</div>

				<div class="eckb-hd-faq__header">

					<!-- TITLE -->
					<div class="eckb-hd-faq__header__title-wrap">
						<h2 class="eckb-hd-faq__header__title" data-tab="faqs" data-step="1"><?php echo $settings['help_dialog_faqs_title']; ?></h2>
						<h2 class="eckb-hd-faq__header__title eckb-hd-faq__header__title--contact" data-tab="contact"><?php echo $settings['help_dialog_contact_title']; ?></h2>
					</div>

					<!-- BREADCRUMB -->
					<div class="eckb-hd-faq__header__breadcrumb-container" data-tab="faqs" data-step="2,3">

						<nav class="eckb-hd__breadcrumb__nav" aria-label="Breadcrumb">

							<ol>
								<li>
									<span id="eckb-hd__breadcrumb__home" class="eckb-hd__breadcrumb_text" data-target-step="1"><?php echo $settings['help_dialog_breadcrumb_home_text']; ?></span>
									<span  aria-hidden="true" id="eckb-search-home-arrow" class=" eckb-hd-faq__header__title-arrow epkbfa epkbfa-caret-right"></span>
								</li>

								<li>
									<span id="eckb-hd__breadcrumb__search-results" class="eckb-hd__breadcrumb_text" data-target-step="2"><?php echo $settings['help_dialog_breadcrumb_search_result_text']; ?></span>
									<span aria-hidden="true" id="eckb-search-result-arrow" class=" eckb-hd-faq__header__title-arrow epkbfa epkbfa-caret-right"></span>
								</li>

								<li>
									<span id="eckb-hd__breadcrumb__article" data-step="3" class="eckb-hd__breadcrumb_text" data-target-step="3"><?php echo $settings['help_dialog_breadcrumb_article_text']; ?></span>
								</li>

							</ol>

						</nav>

					</div>

				</div>

			</div>

			<!-- BODY CONTAINER -->
			<div id="eckb-hd-body-container">    <?php

				if ( $help_dialog_display_mode != 'contact' ) {   ?>
					<!-- FAQs Container -->
					<div class="eckb-hd-body__content-container" role="tabpanel" tabindex="0" aria-labelledby="eckb-hd-faq-tab" data-tab="faqs">

						<!-- FAQ List -->
						<div class="eckb-hd-faq-container">

							<div class="eckb-hd-faq__list">

								<!-- FAQ Wrap -->
								<div class="eckb-hd-faq__faqs-container" data-step="1">
									<?php self::display_faqs_box( $location_category );  ?>
								</div>

								<!-- Search Results Container -->
								<div class="eckb-hd-kb__search-results-container">
									<?php self::search_result_box( $settings );  ?>
								</div>

							</div>

						</div>

						<!-- Search Box -->					<?php 
						self::display_search_input_box( $settings, $location_category );     ?>

					</div>      <?php
				}

				if ( $help_dialog_display_mode != 'faqs' ) {	?>
					<!-- Contact Us form -->    <?php
					self::display_contact_box( $settings, $location_category );
				} ?>

				<div class="eckb-hd__loading-spinner__container" data-sub-step="4">
					<div class="eckb-hd__loading-spinner"></div>
				</div>

			</div>

			<!-- FOOTER CONTAINER -->
			<div id="eckb-hd-footer-container">
				<span class="eckb-hd-footer__poweredBy"><?php _e( 'Powered By', 'echo-knowledge-base' ); ?></span>
				<img class="eckb-hd-footer__icon" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png'; ?>">
				<a class="eckb-hd-footer__link" href="https://www.echoknowledgebase.com/help-dialog/" target="_blank"><?php _e( 'Echo Knowledge Base', 'echo-knowledge-base' ); ?></a>
			</div>
		</div><?php 
		
		$help_dialog_launcher_start_delay = 0;
		if ( empty( $_REQUEST['epkb-editor-page-loaded'] ) && ! empty( $settings['help_dialog_launcher_start_delay'] ) ) { 
			$help_dialog_launcher_start_delay = $settings['help_dialog_launcher_start_delay'];
		} ?>

		<div class="eckb-hd-toggle eckb-hd-toggle--off" data-start-delay="<?php echo $help_dialog_launcher_start_delay; ?>" style="display:none;">
			<span class="eckb-hd-toggle__icon epkbfa epkbfa-comments-o"></span>
		</div><?php 
		
		if ( ! empty( $_GET['epkb-editor-page-loaded'] ) && ! empty( $_GET['epkb-editor-page-type'] ) && $_GET['epkb-editor-page-type'] == 'help-dialog' ) { ?>
			<div class="eckb-hd-overlay">
				<div class="eckb-editor-hd-title">
					<?php _e( 'You are editing the Help Dialog', 'echo-knowledge-base' ); ?>
				</div>
			</div><?php 
		} ?>
		
		<style id="help-dialog-styles">
			#eckb-help-dialog{
				background-color: <?php echo $settings['help_dialog_background_color']; ?>;
			}

			#eckb-help-dialog .eckb-hd-tab--active {
				background-color: <?php echo $settings['help_dialog_background_color']; ?>;
			}
			.eckb-hd-tab {
				background-color: <?php echo $settings['help_dialog_not_active_tab']; ?>;
			}
			.eckb-hd-tab {
				background-color: <?php echo $settings['help_dialog_not_active_tab']; ?>;
			}
			/* DESKTOP */
			#eckb-help-dialog .eckb-hd-tab__faq-btn__text, #eckb-help-dialog .eckb-hd-tab__contact-btn__text{
				color: <?php echo $settings['help_dialog_tab_text_color']; ?>;

			}
			/*#eckb-help-dialog #eckb-hd-body-container {
				!*max-height: *!<?php //echo $settings['help_dialog_container_desktop_height']; ?>!*px;*!
			}*/

			#eckb-help-dialog {
				width: <?php echo $settings['help_dialog_container_desktop_width']; ?>px;
			}

			/* Launcher */
			.eckb-hd-toggle {
				background-color: <?php echo $settings['help_dialog_launcher_background_color']; ?>;
			}
			.eckb-hd-toggle:hover {
				background-color: <?php echo $settings['help_dialog_launcher_background_hover_color']; ?>;
			}
			.eckb-hd-toggle {
				color: <?php echo $settings['help_dialog_launcher_icon_color']; ?>;
			}
			.eckb-hd-toggle:hover {
				color: <?php echo $settings['help_dialog_launcher_icon_hover_color']; ?>;
			}

			/* General*/
			#eckb-help-dialog .eckb-hd-header__title__faq {
				color: <?php echo $settings['help_dialog_main_title_text_color']; ?>;
			}
			#eckb-help-dialog .eckb-hd-faq__header__title,
			#eckb-help-dialog .eckb-hd__breadcrumb__nav {
				color: <?php echo $settings['help_dialog_welcome_text_color']; ?>;
			}
			#eckb-help-dialog .eckb-hd-faq__header__title,
			#eckb-help-dialog .eckb-hd__breadcrumb__nav {
				background-color: <?php echo $settings['help_dialog_welcome_background_color']; ?>;
			}
			#eckb-help-dialog .eckb-hd__breadcrumb__nav .eckb-hd-faq__header__title-arrow {
				color: <?php echo $settings['help_dialog_breadcrumb_arrow_color']; ?>;
			}
			/* Back Navigation */
			#eckb-help-dialog .epkb-hd__faq__back-btn .epkb-hd__faq__back-btn__icon,
			#eckb-help-dialog .epkb-hd__faq__back-btn .epkb-hd__faq__back-btn__text {
				color: <?php echo $settings['help_dialog_back_text_color']; ?>;
			}
			#eckb-help-dialog .epkb-hd__faq__back-btn:hover .epkb-hd__faq__back-btn__icon,
			#eckb-help-dialog .epkb-hd__faq__back-btn:hover .epkb-hd__faq__back-btn__text {
				color: <?php echo $settings['help_dialog_back_text_color_hover_color']; ?>;
			}
			#eckb-help-dialog .epkb-hd__faq__back-btn {
				background-color: <?php echo $settings['help_dialog_back_background_color']; ?>;
			}
			#eckb-help-dialog .epkb-hd__faq__back-btn:hover {
				background-color: <?php echo $settings['help_dialog_back_background_color_hover_color']; ?>;
			}

			/* Search Results */
			#eckb-help-dialog .epkb-hd__search-results-title {
				color: <?php echo $settings['help_dialog_found_faqs_article_tab_color']; ?> !important;
			}
			#eckb-help-dialog .epkb-hd__search-results-title--active {
				color: <?php echo $settings['help_dialog_found_faqs_article_active_tab_color']; ?> !important;
				border-bottom: 1px solid <?php echo $settings['help_dialog_found_faqs_article_active_tab_color']; ?>;
			}

			/* FAQs */
			#eckb-help-dialog .eckb-hd-faq__list__item-container {
				border-color: <?php echo $settings['help_dialog_faqs_qa_border_color']; ?> !important;
			}
			#eckb-help-dialog .eckb-hd__item__question {
				color: <?php echo $settings['help_dialog_faqs_question_text_color']; ?> !important;
			}
			#eckb-help-dialog .epkb-hd__element--active .eckb-hd__item__question {
				color: <?php echo $settings['help_dialog_faqs_question_active_text_color']; ?> !important;
			}
			#eckb-help-dialog .eckb-hd__item__answer__text {
				color: <?php echo $settings['help_dialog_faqs_answer_text_color']; ?> !important;
			}
			#eckb-help-dialog .eckb-hd__item__answer {
				background-color: <?php echo $settings['help_dialog_faqs_answer_background_color']; ?> !important;
			}
			#eckb-help-dialog .eckb-hd-faq__list__item-container {
				background-color: <?php echo $settings['help_dialog_faqs_question_background_color']; ?> !important;
			}
			#eckb-help-dialog .epkb-hd__element--active {
				background-color: <?php echo $settings['help_dialog_faqs_question_active_background_color']; ?> !important;
			}

			/* Single Article */


			#eckb-help-dialog #epkb-hd__search_results-cat-article-details .epkb-hd_article-link {
				color: <?php echo $settings['help_dialog_single_article_read_more_text_color']; ?>;
			}
			#eckb-help-dialog #epkb-hd__search_results-cat-article-details .epkb-hd_article-link:hover {
				color: <?php echo $settings['help_dialog_single_article_read_more_text_hover_color']; ?>;
			}

			/* Contact Form */
			.epkb-hd__contact-form-btn {
				background-color: <?php echo $settings['help_dialog_contact_submit_button_color']; ?>!important;
				color: <?php echo $settings['help_dialog_contact_submit_button_text_color']; ?>!important;
			}
			.epkb-hd__contact-form-btn:hover {
				background-color: <?php echo $settings['help_dialog_contact_submit_button_hover_color']; ?>!important;
				color: <?php echo $settings['help_dialog_contact_submit_button_text_hover_color']; ?>!important;
			}

			/* --- Mobile Settings ---*/

			/* TABLET */
			@media only screen and ( max-width: <?php echo $settings['help_dialog_tablet_break_point']; ?>px ) {
				#eckb-help-dialog {
					width: <?php echo $settings['help_dialog_container_tablet_width']; ?>px;
				}
			}
			/* MOBILE */
			@media only screen and ( max-width: <?php echo $settings['help_dialog_mobile_break_point']; ?>px ) {
				#eckb-help-dialog {
					width: <?php echo $settings['help_dialog_container_mobile_width']; ?>px;
					right:0 !important;

				}
				#eckb-help-dialog #eckb-hd-body-container {
					/*height: calc(100vh - 286px) !important;*/
				}
				#eckb-help-dialog .eckb-hd-header__title {
					font-size: 20px !important;
				}


			}


		</style>
		<style id="help-dialog-iframe-styles">
			.epkb-hd_article-title {
				color: <?php echo $settings['help_dialog_single_article_title_color'] . '!important'; ?>;
			}
			.epkb-hd_article-desc__body ,
			.epkb-hd_article-desc__body p{
				color: <?php echo $settings['help_dialog_single_article_desc_color'] . '!important'; ?>;
			}
		</style>
		<?php
	}

	/**
	 * List Questions in Help Dialog
	 * @param $location_category
	 */
	private function display_faqs_box( $location_category ) {

		// retrieve questions for the help dialog
		$questions = get_posts( array(
						'post_type' => EPKB_Help_Dialog_Handler::get_post_type(),
						'tax_query' => array(
							array(
								'taxonomy' => EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(),
								'terms' => $location_category->location_id
							)
						),
						'order' => 'ASC',
						'posts_per_page' => -1,
						'meta_key' => 'epkb_faq_order_' . $location_category->location_id,
						'orderby' => 'meta_value_num',
		) );

		// No Questions found
		if ( empty( $questions ) ) {    ?>
			<div class="epkb-hd__no-questions-set">
				<span class="epkb-hd__contact-us__message"><?php _e( 'Search your question below or', 'echo-knowledge-base' ); ?></span>
				<span class="epkb-hd__contact-us__link" data-target-tab="contact"> <?php _e( 'contact us here', 'echo-knowledge-base' ); ?></span>
			</div>   <?php

		// Found Questions
		} else {

			$faqs = [];
			foreach( $questions as $question ) {
				$faqs[] = ['question' => $question->post_title, 'answer' => $question->post_content];
			}

			foreach ( $faqs as $faq ) {
				echo EPKB_HTML_Forms::get_faq_item_html( $faq['question'], $faq['answer'] );
			}
		}
	}

	/**
	 * Display Search Input and Results
	 *
	 * @param $settings
	 * @param $location_category
	 */
	private function display_search_input_box( $settings, $location_category ) {

		$kb_ids = '';
		if ( isset( $location_category->kb_ids ) && is_array( $location_category->kb_ids ) ) {
			$kb_ids = implode( ',', $location_category->kb_ids );
		}		?>
		
		<div class="eckb-hd-search-container">

			<!----- Search Box ------>
			<div class="epkb-hd__search-box">
				<form id="epkb-hd__search-form"  method="post" action="" onSubmit="return false;">
					<input type="text" id="epkb-hd__search-terms" name="epkb-hd__search-terms" value=""
						   placeholder="<?php echo $settings['help_dialog_faqs_search_placeholder']; ?>" data-kb-ids="<?php echo $kb_ids; ?>" maxlength="<?php echo EPKB_Help_Dialog_Search::SEARCH_INPUT_LENGTH; ?>" />
					<span class="epkb-hd__search-terms__icon epkbfa epkbfa-search"></span>
				</form>
			</div>

		</div>		<?php
	}

	/**
	 * Display search results box
	 *
	 * @param $settings
	 */
	private function search_result_box( $settings ) {

		$activeWPTheme = 'eckb_hd_iframe_active_theme_'. EPKB_Utilities::get_wp_option( 'stylesheet', 'unknown' );		?>

		<!----- Search Box Results ------>
		<div class="epkb-hd-search-results-container" data-step="2,3">

			<div class="epkb-hd__search-results-title-wrap" data-step="2" data-sub-step="1,2">
				<span class="epkb-hd__search-results-title epkb-hd__search-results-title__faqs epkb-hd__search-results-title--active" data-target-sub-step="1"><?php echo $settings['help_dialog_found_faqs_tab_text']; ?></span>
				<span> | </span>
				<span class="epkb-hd__search-results-title epkb-hd__search-results-title__articles" data-target-sub-step="2"><?php echo $settings['help_dialog_fount_articles_tab_text']; ?></span>
			</div>
			
			<div id="epkb-hd__search_results__errors" class="epkb-hd-search-results__article-list" data-sub-step="3"></div>
			
			<div id="epkb-hd__search_results__faqs" class="epkb-hd-search-results__faqs-list" data-step="2" data-sub-step="1,5"></div>

			<div id="epkb-hd__search_results__articles" class="epkb-hd-search-results__article-list" data-step="2" data-sub-step="2,6"></div>

			<div id="epkb-hd__search_results-cat-article-details" class="epkb-hd__search_step" data-step="3">

				<div class="epkb-hd_article-item-details">
					<iframe id="epkb-hd_article-desc" data-active-theme-class="<?php echo $activeWPTheme; ?>" frameborder="0" scrolling="no"></iframe>
					<div class="epkb-hd_article-item-details__fade--container">
						<div class="epkb-hd_fade-level-1"></div>
						<div class="epkb-hd_fade-level-2"></div>
						<div class="epkb-hd_fade-level-3"></div>
					</div>
				</div>

				<div class="epkb-hd_article-item-footer">
					<a class="epkb-hd_article-link" href="" target="_blank"><?php echo $settings['help_dialog_article_read_more_text']; ?></a>
					<div class="epkb-hd__faq__back-btn" data-sub-step="1,2,3,5,6">
						<div class="epkb-hd__faq__back-btn__icon epkbfa epkbfa-caret-left"></div>
						<div class="epkb-hd__faq__back-btn__text"><?php _e( 'Back', 'echo-knowledge-base' ); ?></div>
					</div>
				</div>

			</div>

		</div> <?php
	}

	/**
	 * Display Contact Box
	 *
	 * @param $settings
	 * @param $location
	 */
	private function display_contact_box( $settings, $location ) {    ?>
		<div id="epkb-hd-body__contact-container" role="tabpanel" tabindex="0" aria-labelledby="eckb-hd-contact-us-tab" data-tab="contact">
			<form id="epkb-hd__contact-form" method="post" enctype="multipart/form-data">				<?php
				wp_nonce_field( '_wpnonce_epkb_ajax_action' );				?>
				<input type="hidden" name="action" value="epkb_help_dialog_contact" />
				<input type="hidden" name="location_name" value="<?php echo $location->name; ?>" />
				<div id="epkb-hd__contact-form-body">					<?php

						// check editor target_selector for second design
						$design_version = 1;    ?>

						<div class="epkb-hd__contact-form-field">   <?php
							if ( $design_version == 1 ) {   ?>
								<label class="epkb-hd__contact-form-user_first_name_label" for="epkb-hd__contact-form-user_first_name"><?php echo $settings['help_dialog_contact_name_text']; ?></label>     <?php
							}   ?>
							<input name="user_first_name" type="text" value="" id="epkb-hd__contact-form-user_first_name" placeholder="<?php echo $design_version == 1 ? '' : $settings['help_dialog_contact_name_text']; ?>" maxlength="<?php echo EPKB_Help_Dialog_Submissions_DB::USER_NAME_LENGTH; ?>">
						</div>      <?php

						// Set fake input field that is visibile only for spam bots     ?>
						<div class="epkb-hd__contact-form-field epkb-hd__contact-form-field--catch-details">
							<label class="epkb-hd__contact-form-comment_label" for="epkb-hd__contact-form-catch-details"><span class="epkb-hd__contact-form-field__label-text"><?php _e( 'Catch Details', 'echo-knowledge-base' ); ?></span></label>
							<input name="catch_details" type="text" value="" id="epkb-hd__contact-form-catch-details" placeholder="" maxlength="100" tabindex="-1" autocomplete="off">
						</div>

						<div class="epkb-hd__contact-form-field">   <?php
							if ( $design_version == 1 ) {   ?>
								<label class="epkb-hd__contact-form-email_label" for="epkb-hd__contact-form-email"><span class="epkb-hd__contact-form-field__label-text"><?php echo $settings['help_dialog_contact_user_email_text']; ?></span><span class="epkb-hd__contact-form-field__required-tag">*</span></label>     <?php
							}   ?>
							<input name="email" type="email" value="" required id="epkb-hd__contact-form-email" placeholder="<?php echo $design_version == 1 ? '' : $settings['help_dialog_contact_user_email_text']; ?>" maxlength="<?php echo EPKB_Help_Dialog_Submissions_DB::USER_EMAIL_LENGTH; ?>">
						</div>

						<div class="epkb-hd__contact-form-field">   <?php
							if ( $design_version == 1 ) {   ?>
								<label class="epkb-hd__contact-form-subject_label" for="epkb-hd__contact-form-subject"><span class="epkb-hd__contact-form-field__label-text"><?php echo $settings['help_dialog_contact_subject_text']; ?></span><span class="epkb-hd__contact-form-field__required-tag">*</span></label>     <?php
							}   ?>
							<input name="subject" type="text" value="" required id="epkb-hd__contact-form-subject" placeholder="<?php echo $design_version == 1 ? '' : $settings['help_dialog_contact_subject_text']; ?>" maxlength="<?php echo EPKB_Help_Dialog_Submissions_DB::SUBJECT_LENGTH; ?>">
						</div>

						<div class="epkb-hd__contact-form-field">   <?php
							if ( $design_version == 1 ) {   ?>
								<label class="epkb-hd__contact-form-comment_label" for="epkb-hd__contact-form-comment"><span class="epkb-hd__contact-form-field__label-text"><?php echo $settings['help_dialog_contact_comment_text']; ?></span><span class="epkb-hd__contact-form-field__required-tag">*</span></label>     <?php
							}   ?>
							<textarea name="comment" required id="epkb-hd__contact-form-comment" rows="4" placeholder="<?php echo $design_version == 1 ? '' : $settings['help_dialog_contact_comment_text']; ?>" maxlength="<?php echo EPKB_Help_Dialog_Submissions_DB::COMMENT_LENGTH; ?>"></textarea>
						</div>

					<div class="epkb-hd__contact-form-btn-wrap">
						<input type="submit" name="submit" value="<?php echo $settings['help_dialog_contact_button_title']; ?>" class="epkb-hd__contact-form-btn">
						<div class="epkb-hd__contact-form-response"></div>
					</div>
				</div>
			</form>
		</div>		<?php
	}
	
	// return current location
	private static function is_display_dialog() {

		// if Help Dialog is disabled then do not show it
		if ( ! EPKB_Help_Dialog_View::is_help_dialog_enabled() ) {
			return false;
		}

		// is this page or post or main page to display the Help Dialog on?
		$is_front_page = is_front_page();
		$post = get_queried_object();

		if ( ! $is_front_page && ( empty( $post ) || get_class( $post ) !== 'WP_Post' || empty( $post->ID ) ) ) {
			return false;
		}

		// get all defined locations  (location name and related category id)
		$locations = EPKB_FAQ_Utilities::get_help_dialog_location_categories_unfiltered();

		// if no location is defined then do not display Help Dialog
		if ( empty( $locations ) || ! is_array( $locations ) ) {
			return false;
		}

		// if the frontend Editor is on then show the first Help Dialog
		if ( ! empty( $_GET['epkb-editor-page-loaded'] ) && $_GET['epkb-editor-page-loaded'] == '1' ) {
			reset($locations);
			return current($locations);
		}

		if ( $is_front_page ) {
			$post_type = 'page';
		} else if ( $post->post_type == 'post' || $post->post_type == 'page' ) {
			$post_type = $post->post_type;
		} else {
			$post_type = 'cpt';
		}

		$matching_location = [];
		$post_id = $is_front_page ? 0 : $post->ID;
		foreach ( $locations as $location_id => $location ) {
			if ( EPKB_FAQ_Utilities::is_page_in_location( $post_id, $post_type, $location ) ) {
				$matching_location = $location;
				break;
			}
		}

		// main page set to static page - search by post ID
		if ( $is_front_page && ! empty( $post ) && ! empty( $post->ID ) ) {
			$post_id = $post->ID;
			foreach ( $locations as $location_id => $location ) {
				if ( EPKB_FAQ_Utilities::is_page_in_location( $post_id, $post_type, $location ) ) {
					$matching_location = $location;
					break;
				}
			}
		}

		// we found matching post or page
		if ( ! empty( $matching_location ) ) {

			// hide draft for non-admins
			if ( ! empty( $matching_location->status ) && $matching_location->status == EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT && ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			return $matching_location;
		}

		return false;
	}

	public static function is_help_dialog_enabled() {
		$settings = epkb_get_instance()->help_dialog_settings_obj->get_settings_or_default();
		return ! empty( $settings['help_dialog_enable'] ) && $settings['help_dialog_enable'] == 'on';
	}
}