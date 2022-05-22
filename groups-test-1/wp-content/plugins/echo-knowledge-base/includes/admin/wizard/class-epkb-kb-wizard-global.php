<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Display KB configuration Wizard
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Global {

	var $kb_config = array();
	/** @var  EPKB_KB_Config_Elements */
	var $form;
	var $global_specs = array();
	/** @var EPKB_HTML_Elements */
	var $html;
	var $kb_id;

	function __construct() {
		$_POST['epkb-wizard-demo-data'] = true;
	}

	/**
	 * Show Wizard page
	 * @param $kb_config
	 */
	public function display_kb_wizard( $kb_config ) {

		$this->kb_config              = $kb_config;
		$this->kb_id                  = $this->kb_config['id'];
		$this->global_specs          = EPKB_KB_Config_Specs::get_fields_specification( $this->kb_config['id'] );
		$this->form                   = new EPKB_KB_Config_Elements();
		$this->html                   = new EPKB_HTML_Elements();
		
		// core handles only default KB
		if ( $this->kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			echo '<div class="epkb-wizard-error-note">' . __('Ensure that Multiple KB add-on is active and refresh this page. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support() . '</div>';
			return;
		}       ?>
		
		<div class="eckb-wizard-global-page" id="epkb-config-wizard-content">
			<div class="epkb-config-wizard-inner">

				<!------- Wizard Header ------------>
				<div class="epkb-wizard-header">
					<div class="epkb-wizard-header__info">
						<h1 class="epkb-wizard-header__info__title">
							<?php _e( 'Global Settings', 'echo-knowledge-base'); ?>
						</h1>
						<span class="epkb-wizard-header__info__current-kb">							<?php
							$kb_name = $this->kb_config['kb_name'];
							echo __( 'for', 'echo-knowledge-base' ) . ' ' . '<span id="epkb_current_kb_name" class="epkb-wizard-header__info__current-kb__name">' . esc_html( $kb_name ) . '</span>';  ?>
						</span>
					</div>

				</div>

				<!------- Wizard Status Bar ------->
				<div class="epkb-wizard-status-bar">
					<ul>
						<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Slugs', 'echo-knowledge-base'); ?></li>
						<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Finish', 'echo-knowledge-base'); ?></li>
					</ul>
				</div>

				<!------- Wizard Content ---------->
				<div class="epkb-wizard-content">
					<?php self::show_loader_html(); ?>
					<?php $this->slug_options(); ?>
					<?php $this->wizard_step_finish(); ?>
				</div>

				<!------- Wizard Footer ---------->
				<div class="epkb-wizard-footer">
					<?php $this->wizard_buttons(); ?>
				</div>
				
				<div id='epkb-ajax-in-progress' style="display:none;">
					<?php esc_html__( 'Saving configuration', 'echo-knowledge-base' ); ?> <img class="epkb-ajax waiting" style="height: 30px;" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/loading_spinner.gif'; ?>">
				</div>
				<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo $this->kb_id; ?>"/>
				<input type="hidden" id="eckb_current_theme_values" value="<?php echo EPKB_KB_Wizard_Themes::get_theme_data( $this->kb_config ); ?>">

				<div class="eckb-bottom-notice-message"></div>
			</div>
		</div> <?php
	}

	// Wizard: Step 1 - Template Options
	private function slug_options() {

		// check if we have any KB Main Pages
		if ( empty( $this->kb_config['kb_main_pages'] ) ) {  ?>

			<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active">				<?php
				EPKB_Utilities::kb_page_with_shortcode_missing_msg( $this->kb_config );

				$link = esc_url( admin_url( 'post-new.php?post_type=page' ) );				?>

				<div class="epkb-center-wrap"><a href="<?php echo $link; ?>" target="_blank" class="epkb-add-shortcode-btn">Add Shortcode&nbsp;&nbsp;<i class="epkbfa epkbfa-exclamation-triangle"></i></a></div>
			</div>  <?php
			return;
		}

		// get a list of the pages
		// with WMPL we want to show just the main language URLs
		$kb_main_pages = array();
		if ( class_exists('SitePress') ) {
			global $sitepress;

			// get pages that are only for the default language
			foreach ( $this->kb_config['kb_main_pages'] as $post_id => $title ) {
				$post_language_information = apply_filters( 'wpml_post_language_details', NULL, $post_id );
				
				if ( ! empty($post_language_information['language_code']) && $post_language_information['language_code'] == $sitepress->get_default_language() ) {
					$kb_main_pages[$post_id] = $title;
				}
			}

		} else {
			$kb_main_pages = $this->kb_config['kb_main_pages'];
		}

		// get the main page slug and check if any Main Page matches Article Common Path
		$main_page_slugs = array();
		$article_path_matches = false;
		$has_single_main_page = count( $kb_main_pages ) == 1;
		foreach ( $kb_main_pages as $post_id => $title ) {
			$main_page_slugs[$post_id] = EPKB_Utilities::get_main_page_slug( $post_id );
			$article_path_matches = $article_path_matches || $main_page_slugs[$post_id] == $this->kb_config['kb_articles_common_path'];
		}

		$main_page_link = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );
		$site_url = site_url();		?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1 epkb-wc-step-panel--active">
			<h4 class="epkb-wizard-error-note"><?php _e( 'If you are changing articles URL ensure that you update or redirect existing links.', 'echo-knowledge-base' ); ?></h4>

			<!-- STEP 1 -->

			<h3><?php _e('STEP 1: Your KB Main Page address (URL):', 'echo-knowledge-base'); ?></h3> <?php

			if ( empty( $main_page_link ) ) {
				EPKB_Utilities::kb_page_with_shortcode_missing_msg( $this->kb_config );

			} else {	   ?>

				<p><?php _e( 'If you need to change the Main Page address (slug), you have to edit the page below.', 'echo-knowledge-base'); ?> </p>			<?php
				if ( ! $has_single_main_page ) { ?>
					<p><?php _e( 'Typically you only need one Main Page. You can remove pages that you do not need.', 'echo-knowledge-base'); ?> </p>			<?php
				}   ?>

				<div class="epkb-wizard-main-page-slug-container">
					<ul>					<?php

						foreach ( $kb_main_pages as $post_id => $title ) {

							$kb_page_post = get_post( $post_id );
							if ( empty($kb_page_post) || empty($kb_page_post->post_name) ) {
								continue;
							}

							$page_url = $site_url . '/' . urldecode(sanitize_title_with_dashes( $kb_page_post->post_name, '', 'save' )) . '/' ; ?>

							<li>
								<div class="epkb-wizard-mps-title"><?php echo $title; ?></div>
								<div class="epkb-wizard-mps-url"><a href="<?php echo $page_url; ?>" target="_blank"><?php echo $page_url; ?></a></div>
								<div class="epkb-wizard-mps-edit"><a href="<?php echo get_admin_url( null, 'post.php?post=' . $post_id . '&action=edit' ); ?>"><?php _e('Edit', 'echo-knowledge-base'); ?></a></div>
							</li>					<?php
						}       ?>

					</ul>
				</div>
				<br> <?php
			} ?>

			<!-- STEP 2 -->

			<h3><?php _e( 'STEP 2: Your KB Article Page address:', 'echo-knowledge-base' ); ?></h3>
			<p><?php
				if ( $article_path_matches ) {
					_e( 'Choose the article path that represents all the knowledge bases in step 1.', 'echo-knowledge-base' );
				} else {
					_e( 'Your article slug does not match your KB Main Page slug above (STEP 1). Choose the desired path.', 'echo-knowledge-base' );
				}							?></p>
			<div class="epkb-wizard-slug-options">
			<ul>						<?php

				// display all pages with KB shortcode
				$input_1 = 0;
				$input_2 = 50;
				foreach ( $kb_main_pages as $post_id => $title ) {
					$input_1++;
					$input_2++;

					$kb_main_page_slug = empty($main_page_slugs[$post_id]) ? '' : $main_page_slugs[$post_id];
					if ( empty($kb_main_page_slug) ) {
						continue;
					}       ?>

					<li>    <?php

						// currently only Category Focused Layout has that option					?>
						<!-- With Category -->
						<div class="epkb-wso-option-container epkb-wso-option--with-category">
							<input id="q<?php echo $input_2; ?>" type="radio" data-path="<?php echo $kb_main_page_slug; ?>" data-category="on" class="eckb_slug" name="eckb_slug">
							<label for="q<?php echo $input_2; ?>" class="epkb-global-wizard-slug-label">
								<span class="epkb-wso-with-category__site-url">         <?php echo $site_url; ?></span> /
								<span class="epkb-wso-with-category__main-page-slug">   <?php echo $kb_main_page_slug; ?></span> /
								<span class="epkb-wso-with-category__category">         <?php _e( 'kb-category', 'echo-knowledge-base' ); ?></span> /
								<span class="epkb-wso-with-category__article">          <?php echo 'kb-article'; ?></span>
							</label>
						</div>

						<!-- Without Category -->
						<div class="epkb-wso-option-container epkb-wso-option--without-category">
							<input id="q<?php echo $input_1; ?>" type="radio" data-path="<?php echo $kb_main_page_slug; ?>" data-category="off" class="eckb_slug" name="eckb_slug">
							<label for="q<?php echo $input_1; ?>" class="epkb-global-wizard-slug-label">
								<span class="epkb-wso-with-category__site-url">         <?php echo $site_url; ?> </span> /
								<span class="epkb-wso-with-category__main-page-slug">   <?php echo $kb_main_page_slug; ?></span> /
								<span class="epkb-wso-with-category__article">          <?php _e( 'kb-article', 'echo-knowledge-base' ); ?></span>
							</label>
						</div>

					</li>						<?php
				} //Foreach

				// if no Main Page matches Article Common Path then show the article path here
				if ( ! $article_path_matches ) {
					$input_2++;
					$input_1++;
					if ( $this->kb_config['categories_in_url_enabled'] == 'on' ) {	?>
						<div class="epkb-wso-option-container epkb-wso-option--with-category">
						<input id="q<?php echo $input_2; ?>" type="radio" data-path="<?php echo $this->kb_config['kb_articles_common_path']; ?>" data-category="on" class="eckb_slug" name="eckb_slug">
						<label for="q<?php echo $input_2; ?>" class="epkb-global-wizard-slug-label">
							<span class="epkb-wso-with-category__site-url">         <?php echo $site_url; ?></span> /
							<span class="epkb-wso-with-category__main-page-slug">   <?php echo $this->kb_config['kb_articles_common_path']; ?></span> /
							<span class="epkb-wso-with-category__category">         <?php _e( 'kb-category', 'echo-knowledge-base' ); ?></span> /
							<span class="epkb-wso-with-category__article">          <?php echo 'kb-article'; ?></span>
						</label>
						</div><?php
					} else { ?>
						<div class="epkb-wso-option-container epkb-wso-option--without-category">
							<input id="q<?php echo $input_1; ?>" type="radio" data-path="<?php echo $this->kb_config['kb_articles_common_path']; ?>" data-category="off" class="eckb_slug" name="eckb_slug">
							<label for="q<?php echo $input_1; ?>" class="epkb-global-wizard-slug-label">
								<span class="epkb-wso-with-category__site-url">         <?php echo $site_url; ?> </span> /
								<span class="epkb-wso-with-category__main-page-slug">   <?php echo $this->kb_config['kb_articles_common_path']; ?></span> /
								<span class="epkb-wso-with-category__article">          <?php _e( 'kb-article', 'echo-knowledge-base' ); ?></span>
							</label>
						</div> <?php
					}
				} ?>

				</ul>
			</div>

			<input type="hidden" name="categories_in_url_enabled" id="categories_in_url_enabled" value="<?php echo $this->kb_config['categories_in_url_enabled']; ?>">
			<input type="hidden" name="kb_articles_common_path" id="kb_articles_common_path" value="<?php echo $this->kb_config['kb_articles_common_path']; ?>">

			<h4 class="epkb-wizard-warning-note" style="display: none;"><?php _e('Your article slug does not match your new KB Main Page slug. Please complete the Wizard to fix this mismatch.', 'echo-knowledge-base'); ?></h4>

		</div>	<?php
	}

	// Wizard: Step 2 - Finish
	private function wizard_step_finish() {     ?>

		<div id="epkb-wsb-step-2-panel" class="epkb-wc-step-panel eckb-wizard-step-5" >
			<h2><?php _e( 'Final Step: Update Your Knowledge Base', 'echo-knowledge-base'); ?></h2>
			<p><?php _e( 'Click Apply to update your Knowledge Base configuration based on selection from previous Wizard screens.', 'echo-knowledge-base'); ?></p>
		</div>	<?php

		// display link to KB Main Page if any
		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $this->kb_config );     ?>

		<div id="epkb-wsb-step-3-panel" class="epkb-wc-step-panel eckb-wizard-step-5" style="display: none">
			<div class="epkb-wizard-row-1">
				<p><?php _e( 'See your KB on the front-end:', 'echo-knowledge-base' ); ?></p>
				<a id="epkb-kb-main-page-link" href="<?php echo empty($link_output) ? '' : $link_output; ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'View My Knowledge base', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon dashicons-before dashicons-welcome-learn-more"></span>
				</a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Create Categories from the Categories menu.', 'echo-knowledge-base' ); ?></p>
				<a href="<?php echo admin_url('edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $this->kb_id ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id )); ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'Create Categories', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-book"></span></a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Create Articles from the Add New Article menu.', 'echo-knowledge-base' ); ?></p>
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_id )) ); ?>" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'Create Articles', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-file-text-o "></span>
				</a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Documentation for Knowledge Base and add-ons.', 'echo-knowledge-base' ); ?></p>
				<a href="https://www.echoknowledgebase.com/documentation/setup-your-initial-knowledge-base/" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'KB Documentation', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-book"></span></a>
			</div>

			<div class="epkb-wizard-row-1">
				<p><?php _e( 'Submit a technical support question.', 'echo-knowledge-base' ); ?></p>
				<a href="https://www.echoknowledgebase.com/contact-us/" target="_blank" class="epkb-wizard-button">
					<span class="epkb-wizard-btn-text"><?php _e( 'Support', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-btn-icon epkbfa epkbfa-book"></span></a>
			</div>
		</div>			<?php
	}

	//Wizard: Previous / Next Buttons / Apply Buttons
	public function wizard_buttons() {

	   if ( empty( $this->kb_config['kb_main_pages'] ) ) {
	   	return;
      }  ?>

		<div class="epkb-wizard-button-container epkb-wizard-button-container--first-step">
			<div class="epkb-wizard-button-container__inner">
				<button value="0" id="epkb-wizard-button-prev" class="epkb-wizard-button epkb-wizard-button-prev">
					<span class="epkb-wizard-button-prev__icon epkbfa epkbfa-caret-left"></span>
					<span class="epkb-wizard-button-prev__text"><?php _e( 'Previous', 'echo-knowledge-base' ); ?></span>
				</button>
				<button value="2" id="epkb-wizard-button-next" class="epkb-wizard-button epkb-wizard-button-next">
					<span class="epkb-wizard-button-next__text"><?php _e( 'Next', 'echo-knowledge-base' ); ?></span>
					<span class="epkb-wizard-button-next__icon epkbfa epkbfa-caret-right"></span>
				</button>
				<button value="apply" id="epkb-wizard-button-apply" class="epkb-wizard-button epkb-wizard-button-apply"  data-wizard-type="global"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

				<input type="hidden" id="_wpnonce_apply_wizard_changes" name="_wpnonce_apply_wizard_changes" value="<?php echo wp_create_nonce( "_wpnonce_apply_wizard_changes" ); ?>">
			</div>
			<div class="epkb-wizard-link epkb-wizard-button-container__support-wizard">
				<a href="<?php echo esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $this->kb_config['id'] ) . '&epkb-wizard-tab' ) ); ?>&page=epkb-kb-configuration" class="epkb-wizard-button epkb-wizard-button__exit" >
					<?php _e( 'Exit', 'echo-knowledge-base' ); ?>
				</a>
				<a href="https://www.echoknowledgebase.com/technical-support/" target="_blank">
					<?php _e( 'Support', 'echo-knowledge-base' ); ?>
					<span class="epkbfa epkbfa-external-link"></span>
				</a>
			</div>
		</div>	<?php
	}

	/**
	 * Call all hooks for given Wizard section.
	 *
	 * @param $hook - both hook name and div id
	 * @param $args
	 */
	public function wizard_section( $hook, $args ) {
		do_action( $hook, $args );
	}
	
	/**
	 * THis configuration defines fields that are part of this wizard configuration related to text.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	public static $global_fields = array(
		'kb_articles_common_path',
		'categories_in_url_enabled'
	);

	public static function show_loader_html() { ?>
		 <div class="epkb-admin-dialog-box-loading">
			 <div class="epkb-admin-dbl__header">
				 <div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>
				 <div class="epkb-admin-text"><?php _e( 'Loading...', 'echo-knowledge-base' ); ?></div>
			 </div>
		 </div>
		 <div class="epkb-admin-dialog-box-overlay"></div> <?php
	}
}
