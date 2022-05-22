<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Help Dialog Locations & FAQs page
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Help_Dialog_Locations_Page {

	private $all_locations = [];
	private $current_location;

	private $questions = [];
	private $other_questions = [];

	/**
	 * Displays the Help Dialog Locations & FAQs page with top panel
	 */
	public function display_page() {

		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
			return;
		}

		$this->get_location_data();
		
		if ( empty( $this->current_location->location_id ) ) {
			$admin_page_views[] = $this->get_empty_location_view_config();
		} else {
			$admin_page_views = $this->get_regular_views_config();
		}

		EPKB_HTML_Admin::admin_page_css_missing_message( true );

		if ( isset($_GET['setup-wizard-on']) ) {
			$handler = new EPKB_Help_Dialog_Wizard_Setup();
			$handler->display_setup_wizard();
			return;
		}   ?>

		<!-- Admin Page Wrap -->
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container epkb-help-dialog-config">    <?php

			/**
			 * ADMIN HEADER
			 */
			$help_dialog_header = EPKB_HTML_Admin::admin_help_dialog_header_content( $this->get_list_of_locations(), $this->current_location );
			EPKB_HTML_Admin::admin_header( $help_dialog_header );

			/**
			 * ADMIN TOP PANEL
			 */
			EPKB_HTML_Admin::admin_toolbar( $admin_page_views );

			EPKB_FAQ_Utilities::show_remove_hd_notice();

			/**
			 * LIST OF SETTINGS IN TABS
			 */
			EPKB_HTML_Admin::admin_settings_tab_content( $admin_page_views, 'epkb-config-wrapper' );

			/**
			 * Display WP Editor
			 */
			if ( ! empty( $this->current_location ) ) {
				$this->display_wp_editor();

				EPKB_Utilities::dialog_box_form( array(
					'id' => 'epkb_help_dialog_delete_confirmation',
					'title' => __( 'Deleting Question', 'echo-knowledge-base' ),
					'body' => __( 'Are you sure you want to delete the question? You cannot undo this action.', 'echo-knowledge-base' ),
					'accept_label' => __( 'Delete', 'echo-knowledge-base' ),
					'accept_type' => 'warning',
					'form_inputs' => array(
						'<input type="hidden" value="" id="epkb_help_dialog_delete_confirmation_id">'
					)
				) );

				EPKB_Utilities::dialog_box_form( array(
					'id' => 'epkb_help_location_delete_confirmation',
					'title' => __( 'Deleting Location', 'echo-knowledge-base' ),
					'body' => __( 'Are you sure you want to delete this location? You cannot undo this action.', 'echo-knowledge-base' ),
					'accept_label' => __( 'Delete', 'echo-knowledge-base' ),
					'accept_type' => 'warning',
					'form_inputs' => array(
						'<input type="hidden" value="' . $this->current_location->location_id . '" id="epkb_help_location_delete_confirmation_id">'
					)
				) );
			}   ?>
			<div class="eckb-bottom-notice-message fadeOutDown"></div>
		</div>	    <?php
	}

	/**
	 * Retrieve all data for this location
	 */
	private function get_location_data() {
		
		// create default settings 
		$this->current_location = EPKB_Help_Dialog_Handler::get_location_by_id_or_default();

		// get all locations or nothing if the user removed all 
		$this->all_locations = EPKB_FAQ_Utilities::get_help_dialog_location_categories_unfiltered();
		if ( $this->all_locations === null ) {
			// TODO show error we could not retrieve locations (DB error)
			return;
		}

		// no location defined. Show defaults on the New Location form
		if ( empty($this->all_locations) ) {
			return;
		}
		
		$first_location_term_id = 0;
		foreach ( $this->all_locations as $key => $val ) {
			$first_location_term_id = $key;
			break;
		}
		
		// check if user selected some location in select, or get the first location from the list by default
		$current_location_id = (int)EPKB_Utilities::get( 'epkb-help-dialog-location', $first_location_term_id );

		// new location, if the user come to wrong id or to id = 0. Not an error, the way to add new location for "Create location" and outdated links
		if ( empty( $current_location_id ) || ! isset( $this->all_locations[ $current_location_id ] ) ) {
			return;
		}

		$this->current_location = $this->all_locations[ $current_location_id ];

		// get questions for location 
		$this->questions = get_posts( array(
			'post_type' => EPKB_Help_Dialog_Handler::get_post_type(),
			'tax_query' => array(
				array(
					'taxonomy' => EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(),
					'terms' => $current_location_id
				)
			),
			'order' => 'ASC',
			'posts_per_page' => -1,
			'meta_key' => 'epkb_faq_order_' . $current_location_id,
			'orderby' => 'meta_value_num',
		) );

		// questions that are not from the current location
		$this->other_questions = get_posts( array(
			'post_type' => EPKB_Help_Dialog_Handler::get_post_type(),
			'tax_query' => array(
				array(
					'taxonomy' => EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name(),
					'terms' => $current_location_id,
					'operator' => 'NOT IN'
				)
			),
			'order' => 'DESC',
			'posts_per_page' => -1,
			'orderby' => 'modified',
		) );
	}

	/**
	 * Show Location Name box of Location tab for Help Dialog.
	 *
	 * @return false | string
	 */
	private function location_tab_location_name_box() {

		ob_start();		?>

		<div class="epkb-help-dialog-new-loc-container">
			<div class="epkb-s__input-row-container">
				<input type="text" value="<?php echo $this->current_location->name; ?>" id="epkb-location-name" maxlength="<?php echo EPKB_Help_Dialog_Locations_Ctrl::LOCATION_NAME_LENGTH; ?>">
			</div>
		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Show Visibility box of Location tab for Help Dialog.
	 *
	 * @return false | string
	 */
	private function location_tab_visibility_box() {

		ob_start();		?>

		<ul class="epkb__radio-wrap">
			<li>
				<input
					type="radio"
					id="draft"
					name="epkb-location-status"
					value="<?php echo EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT; ?>" <?php echo checked( $this->current_location->status, EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT, false ); ?>>
				<label for="draft"><?php _e( 'Draft', 'echo-knowledge-base' ); ?></label>
			</li>
			<li>
				<input
					type="radio"
					id="published"
					name="epkb-location-status"
					value="<?php echo EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_PUBLIC; ?>" <?php echo checked( $this->current_location->status, EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_PUBLIC, false ); ?>>
				<label for="published"><?php _e( 'Published', 'echo-knowledge-base' ); ?></label>
			</li>
		</ul>       <?php

		return ob_get_clean();
	}

	/**
	 * Show KB choose box of Location tab for Help Dialog.
	 *
	 * @return false | string
	 */
	private function location_tab_kb_box() {

		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		
		ob_start();	?>
		<div class="epkb-help-dialog-kbs-container"><?php 
			// check if we add new location and make first KB active
			$new_location = empty( $this->current_location->location_id );
			
			foreach ( $all_kb_configs as $kb_config ) {
				EPKB_HTML_Elements::checkbox_toggle( array(
					'textLoc' => 'right',
					'data' => $kb_config['id'],
					'text' => $kb_config['kb_name'],
					'checked' => ( $new_location || in_array( $kb_config['id'], $this->current_location->kb_ids ) ) ? 'on' : ''
				) );
				
				$new_location = false;
			} ?>
		</div><?php 

		return ob_get_clean();
	}

	/**
	 * Show Locations box of Location tab for Help Dialog.
	 *
	 * @return false | string
	 */
	private function location_tab_locations_box() {

		$selected_pages = [
			'page' => [],
			'post' => [],
			'cpt' => []
		];
		
		$excluded_pages = [
			'page' => [],
			'post' => [],
			'cpt' => []
		];
		
		// post type that has no selected elements but radio button set to "All posts except selected" i.e. show on all posts
		$empty_except_types = [];

		$locations = empty( $this->current_location->locations ) ? [] : $this->current_location->locations;
		$location_selected_pages = empty( $locations ) || empty( $locations['selected_pages'] ) ? [] : $locations['selected_pages'];
		$location_excluded_pages = empty( $locations ) || empty( $locations['excluded_pages'] ) ? [] : $locations['excluded_pages'];

		foreach ( $location_selected_pages as $type => $pages ) {
			foreach ( $pages as $page_id ) {
				if ( $page_id == 0 ) {
					$selected_pages[$type][] = [
						'post_id' => $page_id,
						'post_type' => 'page',
						'title' => __( 'Home Page', 'echo-knowledge-base' )
					];
				}

				$post = get_post( $page_id );
				if ( empty( $post ) ) {
					continue;
				}

				$selected_pages[$type][] = [
					'post_id' => $page_id,
					'post_type' => $post->post_type,
					'title' => get_the_title( $post )
				];
			}
		}

		foreach ( $location_excluded_pages as $type => $pages ) {

			if ( ! empty( $location_selected_pages[$type] ) ) {
				continue;
			}

			foreach ( $pages as $page_id ) {
				if ( $page_id == 0 ) {
					$selected_pages[$type][] = [
						'post_id' => $page_id,
						'post_type' => 'page',
						'title' => __( 'Home Page', 'echo-knowledge-base' )
					];
				}

				if ( $page_id == '-1' ) {
					$empty_except_types[] = $type;
					continue;
				}

				$post = get_post( $page_id );
				if ( empty( $post ) ) {
					continue;
				}

				$excluded_pages[$type][] = [
					'post_id' => $page_id,
					'post_type' => $post->post_type,
					'title' => get_the_title( $post )
				];
			}
		}

		ob_start(); ?>
		
		<div class="epkb-hd-location-container">
			<div class="epkb-hd-location-title"><?php _e( 'Pages', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-hd-location-option-wrap">
				<div class="epkb-hd-location-option-radio">
					<input type="radio" name="page" value="in" <?php checked( count($selected_pages['page']) || ! ( count($excluded_pages['page']) || in_array( 'page', $empty_except_types ) ), true ); ?>>
				</div>
				<div class="epkb-hd-location-option-body">
					<div class="epkb-hd-location-option-title"><?php _e( 'Display on these pages:', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-hd-location-option-select">
						<ul class="epkb-hd-location-option-list epkb-hd-location-option-list__page-in"><?php 
							foreach ( $selected_pages['page'] as $page ) { ?>
								<li class="epkb-hd-location-option" data-post-id="<?php echo $page['post_id']; ?>" data-location-type="<?php echo $page['post_type']; ?>"><?php echo $page['title']; ?></li><?php 
							} ?>
						</ul>
						<input class="epkb-hd-location-option-input" data-post-type="page" data-include-type="in" placeholder="<?php _e( 'start typing page name...', 'echo-knowledge-base' ); ?>">
						<ul class="epkb-hd-location-option-search-results"></ul>
					</div>
				</div>
			</div>
			<div class="epkb-hd-location-option-wrap">
				<div class="epkb-hd-location-option-radio">
					<input type="radio" name="page" value="except" <?php checked( ( count($excluded_pages['page']) || in_array( 'page', $empty_except_types ) ), true ); ?>>
				</div>
				<div class="epkb-hd-location-option-body">
					<div class="epkb-hd-location-option-title"><?php _e( 'Display on all pages except these:', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-hd-location-option-select">
						<ul class="epkb-hd-location-option-list epkb-hd-location-option-list__page-except"><?php 
							foreach ( $excluded_pages['page'] as $page ) { ?>
								<li class="epkb-hd-location-option" data-post-id="<?php echo $page['post_id']; ?>" data-location-type="<?php echo $page['post_type']; ?>"><?php echo $page['title']; ?></li><?php 
							} ?>
						</ul>
						<input class="epkb-hd-location-option-input" data-post-type="page" data-include-type="except" placeholder="<?php _e( 'start typing page name...', 'echo-knowledge-base' ); ?>">
						<ul class="epkb-hd-location-option-search-results"></ul>
					</div>
				</div>
			</div>
		</div> 
		<div class="epkb-hd-location-container">
			<div class="epkb-hd-location-title"><?php _e( 'Posts', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-hd-location-option-wrap">
				<div class="epkb-hd-location-option-radio">
					<input type="radio" name="post" value="in" <?php checked( count($selected_pages['post']) || ! ( count($excluded_pages['post']) || in_array( 'post', $empty_except_types ) ), true ); ?>>
				</div>
				<div class="epkb-hd-location-option-body">
					<div class="epkb-hd-location-option-title"><?php _e( 'Display on these posts:', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-hd-location-option-select">
						<ul class="epkb-hd-location-option-list epkb-hd-location-option-list__post-in"><?php 
							foreach ( $selected_pages['post'] as $page ) { ?>
								<li class="epkb-hd-location-option" data-post-id="<?php echo $page['post_id']; ?>" data-location-type="<?php echo $page['post_type']; ?>"><?php echo $page['title']; ?></li><?php 
							} ?>
						</ul>
						<input class="epkb-hd-location-option-input" data-post-type="post" data-include-type="in" placeholder="<?php _e( 'start typing page name...', 'echo-knowledge-base' ); ?>">
						<ul class="epkb-hd-location-option-search-results"></ul>
					</div>
				</div>
			</div>
			<div class="epkb-hd-location-option-wrap">
				<div class="epkb-hd-location-option-radio">
					<input type="radio" name="post" value="except" <?php checked( ( count($excluded_pages['post']) || in_array( 'post', $empty_except_types ) ), true ); ?>>
				</div>
				<div class="epkb-hd-location-option-body">
					<div class="epkb-hd-location-option-title"><?php _e( 'Display on all posts except these:', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-hd-location-option-select">
						<ul class="epkb-hd-location-option-list epkb-hd-location-option-list__post-except"><?php 
							foreach ( $excluded_pages['post'] as $page ) { ?>
								
								<li class="epkb-hd-location-option" data-post-id="<?php echo $page['post_id']; ?>" data-location-type="<?php echo $page['post_type']; ?>"><?php echo $page['title']; ?></li><?php 
							} ?>
						</ul>
						<input class="epkb-hd-location-option-input" data-post-type="post" data-include-type="except" placeholder="<?php _e( 'start typing page name...', 'echo-knowledge-base' ); ?>">
						<ul class="epkb-hd-location-option-search-results"></ul>
					</div>
				</div>
			</div>
		</div> 
		<div class="epkb-hd-location-container">
			<div class="epkb-hd-location-title"><?php _e( 'Custom Post Types (CPTs)', 'echo-knowledge-base' ); ?></div>
			<div class="epkb-hd-location-option-wrap">
				<div class="epkb-hd-location-option-radio">
					<input type="radio" name="cpt" value="in" <?php checked( count($selected_pages['cpt']) || ! ( count($excluded_pages['cpt']) || in_array( 'cpt', $empty_except_types ) ), true ); ?>>
				</div>
				<div class="epkb-hd-location-option-body">
					<div class="epkb-hd-location-option-title"><?php _e( 'Display on these CPTs:', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-hd-location-option-select">
						<ul class="epkb-hd-location-option-list epkb-hd-location-option-list__cpt-in"><?php 
							foreach ( $selected_pages['cpt'] as $page ) { ?>
								<li class="epkb-hd-location-option" data-post-id="<?php echo $page['post_id']; ?>" data-location-type="<?php echo $page['post_type']; ?>"><?php echo $page['title']; ?></li><?php 
							} ?>
						</ul>
						<input class="epkb-hd-location-option-input" data-post-type="cpt" data-include-type="in" placeholder="<?php _e( 'start typing page name...', 'echo-knowledge-base' ); ?>">
						<ul class="epkb-hd-location-option-search-results"></ul>
					</div>
				</div>
			</div>
			<div class="epkb-hd-location-option-wrap">
				<div class="epkb-hd-location-option-radio">
					<input type="radio" name="cpt" value="except" <?php checked( ( count($excluded_pages['cpt']) || in_array( 'cpt', $empty_except_types ) ), true ); ?>>
				</div>
				<div class="epkb-hd-location-option-body">
					<div class="epkb-hd-location-option-title"><?php _e( 'Display on all CPTs except these:', 'echo-knowledge-base' ); ?></div>
					<div class="epkb-hd-location-option-select">
						<ul class="epkb-hd-location-option-list epkb-hd-location-option-list__cpt-except"><?php 
							foreach ( $excluded_pages['cpt'] as $page ) { ?>
								<li class="epkb-hd-location-option" data-post-id="<?php echo $page['post_id']; ?>" data-location-type="<?php echo $page['post_type']; ?>"><?php echo $page['title']; ?></li><?php 
							} ?>
						</ul>
						<input class="epkb-hd-location-option-input" data-post-type="cpt" data-include-type="except" placeholder="<?php _e( 'start typing page name...', 'echo-knowledge-base' ); ?>">
						<ul class="epkb-hd-location-option-search-results"></ul>
					</div>
				</div>
			</div>
		</div> <?php

		return ob_get_clean();
	}

	/**
	 * Show actions row for Location tab
	 */
	private function location_tab_actions_row() {

		ob_start();		?>

		<div class="epkb-admin__list-actions-row">    <?php
			if ( $this->current_location->location_id ) {
				EPKB_HTML_Elements::submit_button_v2( __( 'Save Location', 'echo-knowledge-base' ), 'epkb_location', 'epkb__hdl__action__save', '', true, '', 'epkb-success-btn');
				EPKB_HTML_Elements::submit_button_v2( __( 'Delete Location', 'echo-knowledge-base' ), '', 'epkb__hdl__action__delete', '', '', '', 'epkb-error-btn');
			} else {
				EPKB_HTML_Elements::submit_button_v2( __( 'Add New Location', 'echo-knowledge-base' ), 'epkb_location', 'epkb__hdl__action__save', '', true, '', 'epkb-success-btn');
				EPKB_HTML_Elements::submit_button_v2( __( 'Cancel', 'echo-knowledge-base' ), '', 'epkb__hdl__action__cancel', '', '', '', 'epkb-error-btn');
			} ?>
		</div>      <?php

		return ob_get_clean();
	}
	
	/**
	 * Show actions row for Location tab
	 */
	private function questions_tab_actions_row() {

		ob_start();		?>

		<div class="epkb-admin__list-actions-row">    <?php
			EPKB_HTML_Elements::submit_button_v2( __( 'Save Questions Order', 'echo-knowledge-base' ), 'epkb_order', 'epkb__hdl__action__save_order', '', true, '', 'epkb-success-btn');
			EPKB_HTML_Elements::submit_button_v2( __( 'Cancel', 'echo-knowledge-base' ), '', 'epkb__hdl__action__reload', '', '', '', 'epkb-error-btn');  ?>
		</div>      <?php

		return ob_get_clean();
	}

	/**
	 * List of Questions for this location and all other questions.
	 *
	 * @return false | string
	 */
	private function questions_tab() {

		ob_start();		?>

		<div class="epkb-help-dialog-questions-container">

			<div class="epkb-help-dialog-location-questions">

				<div class="epkb-questions-header-container">
					<div class="epkb-header__title">
						<h4><?php _e( 'Questions For This Location', 'echo-knowledge-base' ); ?></h4>
						<div class="epkb-header__link">
							<a href="#" target="_blank" id="epkb_help_add_new_question" class="epkb-info-btn"><?php _e('Add New Question', 'echo-knowledge-base' ); ?></a>
						</div>
					</div>
					<p><?php _e( 'Choose which questions should be shown for this location.', 'echo-knowledge-base' ); ?></p>
				</div>

				<ul class="epkb-questions-list-container">					<?php
					foreach ( $this->questions as $article ) {
						self::display_single_article( array(
							'container_ID' => $article->ID,
							'name' => $article->post_title,
							'type' => 'left',
							'modified' => strtotime ($article->post_modified_gmt)
						) );
					}					?>
				</ul>

			</div>

			<div class="epkb-help-dialog-all-questions">
				<div class="epkb-questions-header-container">
					<div class="epkb-header__title">
						<h4><?php _e( 'All Available Questions', 'echo-knowledge-base' ); ?></h4>
					</div>
					<p><?php _e( 'The following list has all defined questions.', 'echo-knowledge-base' ); ?></p>
					<div class="epkb-header__filter">
						<label for="epkb_all_questions_filter"><?php _e( 'Filter by name', 'echo-knowledge-base' ); ?></label>
						<input id="epkb_all_questions_filter" type="text">
					</div>
				</div>
				<ul class="epkb-all-questions-list-container">					<?php

					foreach ( $this->other_questions as $article ) {
						self::display_single_article( array(
							'container_ID' => $article->ID,
							'name' => $article->post_title,
							'modified' => strtotime ($article->post_modified_gmt)
						) );
					}

					foreach ( $this->questions as $article ) {
						self::display_single_article( array(
							'container_ID' => $article->ID,
							'name' => $article->post_title,
							'disabled' => true,
							'modified' => strtotime ($article->post_modified_gmt)
						) );
					} ?>
				</ul>
			</div>

			<div id="epkb-admin__no-question-message" class="epkb-admin__warning epkb-admin__warning--white-red" style="display:none;"><?php _e( 'You have not created any questions yet.', 'echo-knowledge-base' ); ?></div>
		</div>	<?php

		return ob_get_clean();
	}

	/**
	 * Display Single Article
	 *
	 * @param array $args
	 */
	public static function display_single_article( $args = array() ) {	?>
		<li data-id="<?php echo $args['container_ID']; ?>" class="epkb-question-<?php echo $args['container_ID']; ?> epkb-question-container <?php echo empty( $args['disabled'] ) ? '' : 'epkb-question-container-disabled'; ?>" data-modified="<?php echo $args['modified']; ?>">
			<div class="epkb-question__move_left epkbfa epkbfa-arrow-left" title="<?php _e( 'Add to Location', 'echo-knowledge-base' ); ?>"></div>
			<div class="epkb-question__text"><?php echo $args['name']; ?></div>
			<div class="epkb-question__buttons">
				<div class="epkb-question__icon epkbfa epkbfa-bars" title="<?php _e( 'Move Top/Down', 'echo-knowledge-base' ); ?>"></div>
				<div class="epkb-question__edit epkbfa epkbfa-pencil-square" title="<?php _e( 'Edit Question', 'echo-knowledge-base' ); ?>"></div>
				<div class="epkb-question__delete epkbfa epkbfa-trash" title="<?php _e( 'Delete Question', 'echo-knowledge-base' ); ?>"></div>
				<div class="epkb-question__move_right epkbfa epkbfa-times" title="<?php _e( 'Remove from Location', 'echo-knowledge-base' ); ?>"></div>
			</div>
		</li>	<?php
	}

	/**
	 * Get list of Help Dialog locations.
	 */
	private function get_list_of_locations() {

		$output_options = '';
		
		foreach( $this->all_locations as $location_category ) {
			$output_options .= '<option value="' . $location_category->location_id . '"  ' . selected( $this->current_location->location_id, $location_category->location_id, false ) . '>' . esc_html( $location_category->name ) . '</option>';
		}
		
		if ( empty( $this->current_location->location_id ) ) {
			$output_options .= '<option value="0"  selected="selected">' . __( 'Create New Location', 'echo-knowledge-base' ) . '</option>';
		} else {
			$output_options .= '<option value="0">' . __( 'Create New Location', 'echo-knowledge-base' ) . '</option>';
		}

		return '
			<form id="epkb-change-help-dialog-location" method="get">
				<input type="hidden" name="page" value="epkb-help-dialog-locations">
				<select class="epkb-kb-name" id="epkb-list-of-kbs" name="epkb-help-dialog-location">' .
		            $output_options . '
				</select>
			</form>';
	}

	/**
	 * Add html for wp editor to the page
	 */
	private function display_wp_editor() {  ?>
		
		<div class="epkb-help-wp-editor">
			<div class="epkb-help-wp-editor__overlay"></div>
			<form id="epkb-help-article-form">
				<input type="hidden" id="epkb_help_location" name="epkb_help_location" value="<?php echo $this->current_location->location_id; ?>">
				<input type="hidden" id="epkb_help_question_id" name="epkb_help_question_id" placeholder="<?php _e( 'Question', 'echo-knowledge-base' ); ?>">
				<div class="epkb-help-wp-editor__question">
					<h4><?php _e( 'Question', 'echo-knowledge-base' ); ?></h4>
					<input type="text" id="epkb_help_question" name="epkb_help_question" required maxlength="200">
					<div class="epkb-characters_left"><span class="epkb-characters_left-counter">200</span>/<span>200</span></div>
				</div>
				<div class="epkb-help-wp-editor__answer">
					<h4><?php _e( 'Answer', 'echo-knowledge-base' ); ?></h4><?php
					wp_editor( '', 'epkb_help_editor', array( 'media_buttons' => false ) ); ?>
					<div class="epkb-characters_left"><span class="epkb-characters_left-counter">1500</span>/<span>1500</span></div>
				</div>
				<div class="epkb-help-wp-editor__buttons">				<?php
					EPKB_HTML_Elements::submit_button_v2( __( 'Save', 'echo-knowledge-base' ), 'epkb_help_question_form', 'epkb__help_editor__action__save', '', true, '', 'epkb-success-btn');
					EPKB_HTML_Elements::submit_button_v2( __( 'Cancel', 'echo-knowledge-base' ), '', 'epkb__help_editor__action__cancel', '', '', '', 'epkb-error-btn');				?>
				</div>
			</form>
		</div><?php
	}
	
	/**
	 * Show information if the user add first location 
	 *
	 * @return false | string
	 */
	private static function location_tab_new_location_notice() {
		ob_start();

		EPKB_HTML_Forms::notification_box_top( array(
			'type' => 'info',
			'title' => __( 'New Location', 'echo-knowledge-base' ),
			'desc' => __( 'Create your first location. A location is assigned pages or posts where the Help Dialog will show up.', 'echo-knowledge-base' ),
		) );
		
		return ob_get_clean();
	}

	/**
	 * Show information if the current location does not have questions
	 *
	 * @return false | string
	 */
	private static function no_questions_notice() {
		ob_start();

		EPKB_HTML_Forms::notification_box_top( array(
			'type' => 'error',
			'title' => __( 'No Questions assigned', 'echo-knowledge-base' ),
			'desc' => __( 'This location has no FAQs assigned. Assign Questions.', 'echo-knowledge-base' ) . ' ' . '<a class="epkb-admin__reload-link" href="#questions">' . __( 'here', 'echo-knowledge-base' ) . '</a>',
		) );

		return ob_get_clean();
	}

	/**
	 * Get configuration array for regular views of Help Dialog admin page
	 *
	 * @return array[]
	 */
	private function get_regular_views_config() {

		$location_tab = $this->get_empty_location_view_config();

		return array(

			$location_tab,

			// VIEW: QUESTIONS
			array(

				// Shared
				'list_key' => 'questions',

				// Top Panel Item
				'label_text' => __( 'Questions', 'echo-knowledge-base' ),
				'icon_class' => 'epkbfa epkbfa-question-circle epkb-icon--black',

				// Boxes List
				'list_top_actions_html' => $this->questions_tab_actions_row(),
				'boxes_list' => array(

					// Box: Questions
					array(
						'html' => $this->questions_tab(),
					),
				),
			),
		);
	}

	/**
	 * Get configuration array for Location view of Help Dialog admin page
	 *
	 * @return array
	 */
	private function get_empty_location_view_config() {

		// if we don't have any location show notice
		if ( empty( $this->all_locations ) ) {
			$box_list[] =
				// Box: Location notice
				array(
					'html' => self::location_tab_new_location_notice(),
					'class' => 'epkb-location-notice'
				);

		// optional top message if there is no Questions assigned for the current location - no need to display for Location creation view
		} elseif ( ! empty( $this->current_location->location_id ) ) {
			$box_list[] =
				array(
					'html' => self::no_questions_notice(),
					'class' => 'epkb-location-notice epkb-location-notice--no-questions epkb-location-notice--hidden'
				);
		}

		$box_list[] =
				// Box: Location name
				array(
					'title' => __( 'Name', 'echo-knowledge-base' ),
					'description' => __( 'Name this location for future reference. You will see this name in the list of locations in the drop down above.', 'echo-knowledge-base' ),
					'html' => $this->location_tab_location_name_box(),
				);

		$box_list[] =
				// Box: Visibility
				array(
					'title' => __( 'Visibility', 'echo-knowledge-base' ),
					'description' => __( 'Draft location makes this location hidden from users. Published location shows a Help Dialog on the chosen page.', 'echo-knowledge-base' ),
					'html' => $this->location_tab_visibility_box(),
				);

		$box_list[] =
				// Box: Visibility
				array(
					'title' => __( 'Knowledge Base', 'echo-knowledge-base' ),
					'description' => __( 'The Knowledge Bases in which the user can search for answers.', 'echo-knowledge-base' ),
					'html' => $this->location_tab_kb_box(),
				);

		$box_list[] =
			// Box: Locations
				array(
					'title' => __( 'Where is the Help Dialog displayed?', 'echo-knowledge-base' ),
					'description' => '', 
					'html' => $this->location_tab_locations_box(),
				);

		return array(

			// Shared
			'active' => true,
			'list_key' => 'location',

			// Top Panel Item
			'label_text' => __( 'Location', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-cog epkb-icon--black',

			// Boxes List
			'list_top_actions_html' => $this->location_tab_actions_row(),
			'boxes_list' =>
				$box_list
			,
		);
	}
}
