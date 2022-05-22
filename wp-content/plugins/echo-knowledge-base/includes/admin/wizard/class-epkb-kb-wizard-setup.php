<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Show setup wizard when plugin is installed
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Setup {

	private $is_setup_run_first_time;

	function __construct() {
		add_action( 'wp_ajax_epkb_apply_setup_wizard_changes',  array( $this, 'apply_setup_wizard_changes' ) );
		add_action( 'wp_ajax_nopriv_epkb_apply_setup_wizard_changes', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		add_action( 'wp_ajax_epkb_report_admin_error',  array( 'EPKB_Core_Utilities', 'handle_report_admin_error' ) );
		add_action( 'wp_ajax_nopriv_epkb_report_admin_error', array( 'EPKB_Utilities', 'user_not_logged_in' ) );

		$this->is_setup_run_first_time = EPKB_Core_Utilities::is_run_setup_wizard_first_time() || EPKB_Utilities::post( 'emkb_admin_notice' ) == 'kb_add_success';
	}

	/**
	 * Show KB Setup page
	 *
	 * @param $kb_config
	 * @return boolean
	 */
	public function display_kb_setup_wizard( $kb_config ) {    ?>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-wizard-container">
			<div class="" id="epkb-config-wizard-content">
				<div class="epkb-config-wizard-inner">

					<!------- Wizard Header ------------>
					<div class="epkb-wizard-header">
						<div class="epkb-wizard-header__info">
							<h1 class="epkb-wizard-header__info__title">
								<?php _e( 'Setup Your Knowledge Base', 'echo-knowledge-base' ); ?>
							</h1>
						</div>
						<div class="epkb-setup-wizard-theme-header">
							<h2 class="epkb-setup-wizard-theme-header__info__title" data-title-id="1">
								<?php _e( 'Set your Knowledge Base nickname, slug and add it to menu.', 'echo-knowledge-base' ); ?>
							</h2>
							<h2 class="epkb-setup-wizard-theme-header__info__title hidden" data-title-id="2">
								<?php _e( 'Choose an initial Knowledge Base design. You can easily adjust colors and other elements later.', 'echo-knowledge-base' ); ?>
							</h2>
							<h2 class="epkb-setup-wizard-theme-header__info__title hidden" data-title-id="3">
								<?php _e( 'Article pages can have navigation links in the left sidebar or in the right sidebar.', 'echo-knowledge-base' ); ?>
							</h2>
						</div>
					</div>

					<!------- Top Button Bar -------->
					<div class="epkb-wizard-footer epkb-wizard-top-bar">
						<!----Step 2 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-2-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="1" id="epkb-setup-wizard-button-prev" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__text">&lt;&nbsp;<?php _e( 'Previous Step', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="3" class="epkb-wizard-button epkb-setup-wizard-button-next">
									<span class="epkb-setup-wizard-button-next__text"><?php _e( 'Next Step', 'echo-knowledge-base' ); ?>&nbsp;&gt;</span>
								</button>
							</div>
						</div>
						<!----Step 3 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-3-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="2" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__text">&lt;&nbsp;<?php _e( 'Previous Step', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="apply" class="epkb-wizard-button epkb-setup-wizard-button-apply" data-wizard-type="setup"><?php _e( 'Finish Set Up', 'echo-knowledge-base' ); ?></button>
							</div>
						</div>
					</div>

					<!------- Wizard Content ---------->
					<div class="epkb-wizard-content">
						<?php $this->wizard_step_title_url( $kb_config ); ?>
						<?php $this->setup_wizard_theme( $kb_config ); ?>
						<?php $this->wizard_step_navigation( $kb_config ); ?>
					</div>

					<!------- Wizard Footer ---------->
					<div class="epkb-wizard-footer">

						<!----Step 1 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-1-panel-button epkb-wc-step-panel-button epkb-wc-step-panel-button--active">
							<div class="epkb-wizard-button-container__inner">
								<button value="2" class="epkb-wizard-button epkb-setup-wizard-button-next">
									<span class="epkb-setup-wizard-button-next__text"><?php _e( 'Next Step', 'echo-knowledge-base' ); ?>&nbsp;&gt;</span>
								</button>
							</div>
						</div>

						<!----Step 2 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-2-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="1" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__text">&lt;&nbsp;<?php _e( 'Previous Step', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="3" class="epkb-wizard-button epkb-setup-wizard-button-next">
									<span class="epkb-setup-wizard-button-next__text"><?php _e( 'Next Step', 'echo-knowledge-base' ); ?>&nbsp;&gt;</span>
								</button>
							</div>
						</div>

						<!----Step 3 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-3-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="2" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__text">&lt;&nbsp;<?php _e( 'Previous Step', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="apply" class="epkb-wizard-button epkb-setup-wizard-button-apply" data-wizard-type="setup"><?php _e( 'Finish Set Up', 'echo-knowledge-base' ); ?></button>

								<input type="hidden" id="_wpnonce_apply_wizard_changes" name="_wpnonce_apply_wizard_changes" value="<?php echo wp_create_nonce( "_wpnonce_apply_wizard_changes" ); ?>">
							</div>
						</div>

					</div>

					<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo $kb_config['id']; ?>"/>

					<div class="eckb-bottom-notice-message"></div>

				</div>
			</div>

		</div>		<?php

		// Report error form
		EPKB_HTML_Admin::display_report_admin_error_form();

		return true;
	}

	/**
	 * Setup Wizard: Step 1 - Title & URL
	 *
	 * @param $kb_config
	 */
	private function wizard_step_title_url( $kb_config ) { 	   ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1  epkb-wc-step-panel--active epkb-wizard-theme-step-1 ">  <?php

			// KB Name
		    EPKB_HTML_Elements::text(
				array(
					'label'             => __('Knowledge Base Nickname', 'echo-knowledge-base'),
					'placeholder'       => __('Knowledge Base', 'echo-knowledge-base'),
					'main_tag'          => 'div',
					'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-name',
					'value'             => $kb_config['kb_name']
				)
			);      ?>
			<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p class="epkb-wizard-input-desc"><?php
						echo __( 'Give your Knowledge Base a name. The name will show when we refer to it or when you see a list of post types.', 'echo-knowledge-base' ) .
						     '</br>' . __('Examples: Knowledge Base, Help, Support', 'echo-knowledge-base');							?>
					</p>
				</div>
			</div>			<?php

			// KB Slug - if Setup Wizard is run first time or no KB Main Pages exist, then show input field
			$main_pages = EPKB_KB_Handler::get_kb_main_pages( $kb_config );
			if ( $this->is_setup_run_first_time || empty( $main_pages ) ) {
				EPKB_HTML_Elements::text(
					array(
						'label'             => __( 'Knowledge Base Slug', 'echo-knowledge-base' ),
						'placeholder'       => 'knowledge-base',
						'main_tag'          => 'div',
						'readonly'          => ! EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ),
						'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-slug',
						'value'             => $kb_config['kb_articles_common_path'],
					)
				);      ?>
				<div class="epkb-wizard-row-form-input">
					<div class="epkb-wizard-col2">
						<p id="epkb-wizard-slug-error"><?php _e( 'The slug should not contain full KB URL.', 'echo-knowledge-base' ); ?></p>
						<p class="epkb-wizard-input-desc"><?php _e( 'This KB slug is part of your full knowledge base URL:', 'echo-knowledge-base' ); ?></p>
						<p class="epkb-wizard-input-desc"><span><?php echo site_url(); ?></span> / <span id="epkb-wizard-slug-target"><?php echo $kb_config['kb_articles_common_path']; ?></span></p>
					</div>
				</div>				<?php

			// KB Slug - if user re-run Setup Wizard, then only show slug with Link to change it (KB URL)
			} else {
				$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $kb_config );
				$main_page_slug = EPKB_Core_Utilities::get_main_page_slug( $main_page_id );
				$main_page_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
				EPKB_HTML_Elements::text(
					array(
						'label'             => __( 'Knowledge Base Slug', 'echo-knowledge-base' ),
						'placeholder'       => 'knowledge-base',
						'main_tag'          => 'div',
						'readonly'          => ! ( EPKB_Utilities::get_wp_option( 'epkb_not_completed_setup_wizard_' . $kb_config['id'], false ) && EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ),
						'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-slug',
						'value'             => $main_page_slug,
					)
				);      ?>
				<div class="epkb-wizard-row-form-input">
					<div class="epkb-wizard-col2">
						<p class="epkb-wizard-input-desc"><?php _e( 'This is KB slug that is part of your full knowledge base URL:', 'echo-knowledge-base' ); ?></p>
						<a class="epkb-wizard-input-desc" href="<?php echo $main_page_url; ?>" target="_blank"><?php echo $main_page_url; ?></a><?php
						if ( current_user_can( EPKB_Admin_UI_Access::get_admin_capability() ) ) {   ?>
							<p class="epkb-wizard-input-desc"><a href="<?php echo admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-kb-configuration#settings__kb-urls' ); ?>" target="_blank"><?php _e( 'Change KB URL', 'echo-knowledge-base' ); ?></a></p>    <?php
						}   ?>
					</div>
				</div>				<?php
			}

			// if we have menus and menus without link
			$menus = $this->kb_menus_without_item( $kb_config );
			if ( is_array($menus) && ! empty($menus) ) {      ?>

				<div class="input_group epkb-wizard-row-form-input epkb-wizard-menus" >
					<label><?php _e( 'Add KB to Website Menu', 'echo-knowledge-base' ); ?></label>
					<ul>	<?php
						foreach ($menus as $menu_id => $menu_title) {
							EPKB_HTML_Elements::checkbox( array(
								'name'              => 'epkb_menu_' . $menu_id,
								'label'             => $menu_title,
								'input_group_class' => 'epkb-menu-checkbox',
								'value'             => 'off'
							) );
						}           ?>
					</ul>
				</div>
				<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p class="epkb-wizard-input-desc"><?php _e( 'Choose the website menu(s) where users will access the Knowledge Base. You can change it at any time in WordPress -> Appearance -> Menus.', 'echo-knowledge-base' ); ?></p>
				</div>
				</div><?php

			}       ?>
		</div>	<?php
	}

	/**
	 * Setup Wizard: Step 2 - Choose Design
	 *
	 * @param $kb_config
	 */
	private function setup_wizard_theme( $kb_config ) {

		$preset_options = array();
		$theme_description = EPKB_KB_Wizard_Themes::get_themes_description();
		foreach ( EPKB_KB_Wizard_Themes::get_all_presets( $kb_config ) as $theme_slug => $theme_data ) {
			$preset_options[$theme_data['kb_main_page_layout']][$theme_slug] = $theme_data['kb_name'];
		}       ?>

		<div id="epkb-wsb-step-2-panel" class="epkb-setup-wizard-theme epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-setup-wizard-theme-preview">

				<!-- THEME BUTTONS -->
				<div class="epkb-wizard-theme-tab-container">
					<input type="hidden" id="_wpnonce_setup_wizard_templates" name="_wpnonce_setup_wizard_templates" value="<?php echo wp_create_nonce( "_wpnonce_setup_wizard_templates" ); ?>"/>		<?php

					// add categories  	?>
					<div class="epkb-setup-wizard-group__container"> <?php

					// Pre-select first theme if Setup Wizard is running first time or KB > 1
					$pre_select_theme = false;
					if ( $this->is_setup_run_first_time ) {
						$pre_select_theme = true;
					} else { ?>
						<div class="epkb-setup-wt-tc__themes-group__list config-input-group">

                            <div class="epkb-setup-current">
                                <div class="epkb-setup-option-container epkb-setup-option-container--description">
                                    <h4><?php esc_html_e( 'Your current settings', 'echo-knowledge-base'); ?></h4>
                                    <p><?php esc_html_e( 'You can either keep the current colors and design or choose a different one below.', 'echo-knowledge-base'); ?></p>
                                </div>
                                <div id="epkb-setup-wt-theme-current-panel" class="epkb-setup-option-container epkb-setup-option-container--active">
                                    <div class="epkb-setup-option__inner">
                                        <div class="epkb-setup-option__selection">
                                            <div class="epkb-setup-option__option-container">
                                                <label class="epkb-setup-option__option__label">
                                                    <input type="radio" name="epkp-theme" value="current" checked>
                                                </label>
                                            </div>
                                            <div class="epkb-setup-option__featured-img-container">
                                                <img class="epkb-setup-option__featured-img" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/' . EPKB_KB_Wizard_Themes::$theme_images['current_design']; ?>" title="<?php esc_html_e( 'Current Colors', 'echo-knowledge-base' ); ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

						</div><?php
					}

					foreach ( $preset_options as $title => $group ) {

						$theme_title = '';
						switch ( $title ) {
							case 'Basic':
								$theme_title = __( 'Basic Layout', 'echo-knowledge-base' );
								break;
							case 'Tabs':
								$theme_title = __( 'Tabs Layout', 'echo-knowledge-base' );
								break;
							case 'Categories':
								$theme_title = __( 'Category Focused Layout', 'echo-knowledge-base' );
								break;
							case 'Grid':
								$theme_title = __( 'Grid Layout', 'echo-knowledge-base' );
								break;
							case 'Sidebar':
								$theme_title = __( 'Sidebar Layout', 'echo-knowledge-base' );
								break;
							default:
								break;
						}       ?>

						<div class="epkb-setup-wizard-group__container-inner">
							<div class="epkb-setup-wt-tc__themes-group__header__title"><?php echo $theme_title; ?></div>
						    <div class="epkb-setup-wt-tc__themes-group__header__desc"><?php echo $theme_description[$title]; ?></div>

							<div class="epkb-setup-wt-tc__themes-group__list config-input-group">       <?php

								foreach ( $group as $template_id => $template_name ) {      ?>
									<div id="epkb-setup-wt-theme-<?php echo $template_id; ?>-panel" class="epkb-setup-option-container">
										<div class="epkb-setup-option__inner">
											<div class="epkb-setup-option__selection">
												<div class="epkb-setup-option__option-container">
													<label class="epkb-setup-option__option__label">
														<input type="radio" name="epkp-theme" value="<?php echo $template_id; ?>"<?php echo $pre_select_theme ? ' checked' : ''; ?>>
														<span><?php echo $template_name; ?></span>
													</label>
												</div>
												<div class="epkb-setup-option__featured-img-container">
													<img class="epkb-setup-option__featured-img" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/' . EPKB_KB_Wizard_Themes::$theme_images[$template_id]; ?>" title="<?php echo $template_name; ?>" />
												</div>
											</div>
										</div>
									</div>		<?php

									$pre_select_theme = false;
								}       ?>

							</div>

						</div><?php
					} ?>
					</div>

				</div>

			</div>
		</div>	<?php
	}

	/**
	 * Setup Wizard: Step 3 - Choose navigation on left or right sidebar
	 *
	 * @param $kb_config
	 */
	private function wizard_step_navigation( $kb_config ) {

		$groups = EPKB_KB_Wizard_Themes::get_sidebar_groups();

		$selected_option_id = 1;
		if ( ! $this->is_setup_run_first_time ) {
			$groups = array_merge( [
				[
					'title' => __( 'Your current settings', 'echo-knowledge-base' ),
                    'class' => 'epkb-setup-current-article',
					'description' => __( 'You can either keep the current sidebars setup or choose a different one below.', 'echo-knowledge-base' ),
					'learn_more_url' => '',
					'options' => [
						0 => __( 'Current Navigation', 'echo-knowledge-base' ),
					]
				],
			], $groups );

			$selected_option_id = 0;
		} ?>

		<div id="epkb-wsb-step-3-panel" class="epkb-setup-wizard-sidebar epkb-wc-step-panel eckb-wizard-step-3">
			<div class="epkb-setup-wizard-theme-preview">
				<div class="epkb-wizard-theme-tab-container">
					<div class="epkb-setup-wizard-group__container"><?php
						foreach ( $groups as $group ) { ?>
							<div class="epkb-setup-wizard-group__container-inner <?php echo esc_html( $group['class'] ); ?>">                                <?php

								if ( $group['class'] == 'epkb-setup-current-article') { ?>
									<div class="epkb-setup-current-article__text"> <?php
								}       ?>

								    <div class="epkb-setup-wt-tc__themes-group__header__title"><?php echo esc_html( $group['title'] ); ?></div>
								    <div class="epkb-setup-wt-tc__themes-group__header__desc"><?php
									echo esc_html( $group['description'] );
									if ( ! empty( $group['learn_more_url'] ) ) { ?>
										<a href="<?php echo esc_url( $group['learn_more_url'] ); ?>" target="_blank"><?php esc_html_e( 'See navigation demo page', 'echo-knowledge-base' ); ?></a><?php
									} ?>
									</div>                                <?php

								if ( $group['class'] == 'epkb-setup-current-article') { ?>
									</div>
									<div class="epkb-setup-current-article__image"> <?php
								}   ?>

								    <div class="epkb-setup-wt-tc__themes-group__list config-input-group">       <?php

									foreach ( $group['options'] as $id => $option_title ) {
										$image_id = $id ? $id : self::get_current_sidebar_selection( $kb_config );
										$image_url = Echo_Knowledge_Base::$plugin_url . 'img/' . EPKB_KB_Wizard_Themes::$sidebar_images[ $image_id ]; ?>
										<div id="epkb-setup-wt-sidebar-<?php echo $id; ?>-panel"
											 class="epkb-setup-option-container <?php echo $selected_option_id == $id ? 'epkb-setup-option-container--active' : ''; ?>">
											<div class="epkb-setup-option__inner">
												<div class="epkb-setup-option__selection">
													<div class="epkb-setup-option__option-container">
														<label class="epkb-setup-option__option__label">
															<input type="radio" name="epkb-sidebar" value="<?php echo $id; ?>" <?php checked( $selected_option_id, $id ); ?>>
															<span><?php echo $option_title; ?></span>
														</label>
													</div>
													<div class="epkb-setup-option__featured-img-container">
														<img class="epkb-setup-option__featured-img"
															 src="<?php echo $image_url; ?>"
															 title="<?php echo $option_title; ?>"/>
													</div>
												</div>
											</div>
										</div><?php
									} ?>

									</div>                                <?php

								if ( $group['class'] == 'epkb-setup-current-article') { ?>
									</div> <?php
								}       ?>

							</div><?php
						} ?>
					</div>
				</div>
			</div>
		</div>    <?php
	}

	/**
	 * Find menu items with a link to KB
	 *
	 * @param $kb_config
	 * @return array|bool - true on ERROR,
	 *                      false if found a menu with KB link
	 *                      empty array if no menu exists
	 *                      non-empty array for existing menus.
	 */
	private function kb_menus_without_item( $kb_config ) {

		$menus = wp_get_nav_menus();
		if ( empty($menus) || ! is_array($menus) ) {
			return array();
		}

		$kb_main_pages_info = EPKB_KB_Handler::get_kb_main_pages( $kb_config );

		// check if we have any menu item with KB page
		$menu_without_kb_links = array();
		foreach ( $menus as $menu ) {

			// does menu have any menu items?
			$menu_items = wp_get_nav_menu_items($menu);
			if ( empty($menu_items) && ! is_array($menu_items) )  {
				continue;
			}

			foreach ( $menu_items as $item ) {

				// true if we already have KB link in menu
				if ( $item->object == 'page' && isset( $kb_main_pages_info[$item->object_id]) ) {
					return false; // use this string to show menus without KB link only if ALL menus have no KB links
				}
			}

			$menu_without_kb_links[$menu->term_id] = $menu->name;
		}

		return $menu_without_kb_links;
	}


	/***************************************************************************
	 *
	 * Setup Wizards Functions
	 *
	 ***************************************************************************/

	/**
	 * User submit theme to use for the new Knowledge Base
	 */
	public function apply_setup_wizard_changes() {

		// wp_die if nonce invalid or user does not have correct permission
		EPKB_Utilities::ajax_verify_nonce_and_admin_permission_or_error_die( '_wpnonce_apply_wizard_changes', 'admin_eckb_access_frontend_editor_write' );

		// get current KB ID
		$wizard_kb_id = EPKB_Utilities::post( 'epkb_wizard_kb_id' );
		if ( empty( $wizard_kb_id ) || ! EPKB_Utilities::is_positive_int( $wizard_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 159 ) );
		}

		// get selected Theme Name
		$theme_name = EPKB_Utilities::post( 'theme_name' );
		if ( empty( $theme_name ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 160 ) );
		}

		// create demo KB only for the first time and save it
		if ( $this->is_setup_run_first_time ) {
			$theme_name = $theme_name == 'current' ? 'standard' : $theme_name;
			$selected_layout = EPKB_KB_Wizard_Themes::get_theme_layout( $theme_name );
			$new_kb_config = EPKB_KB_Handler::add_new_knowledge_base( EPKB_KB_Config_DB::DEFAULT_KB_ID, '', '', $selected_layout );
			if ( is_wp_error( $new_kb_config ) ) {
				EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 158, $new_kb_config ) );
			}
		}

		// get current KB configuration (or new one if first time setup)
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $wizard_kb_id, true );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 8, $orig_config ) );
		}

		// get current Add-ons configuration
		$orig_config = apply_filters( 'epkb_all_wizards_get_current_config', $orig_config, $wizard_kb_id );
		if ( empty( $orig_config ) || ! is_array($orig_config) || count($orig_config) < 3 ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 500, EPKB_Utilities::get_variable_string( $orig_config ) ) );
		}

		// if user selected a theme then apply it
		$new_config = $theme_name == 'current' ? $orig_config : $this->get_themed_kb_config( $theme_name, $orig_config );
		$kb_id = $new_config['id'];

		// add menu link
		$this->add_kb_link_to_top_menu( $new_config['kb_main_pages'] );

		// get and sanitize KB Nickname
		$kb_nickname = EPKB_Utilities::post( 'kb_name', '', 'text', 50 );
		if ( empty( $kb_nickname ) ) {
			$kb_nickname = __( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : ' ' . $kb_id );
		}
		$new_config['kb_name'] = $kb_nickname;

		delete_option( 'epkb_run_setup' );

		$this->create_main_page_if_missing( $new_config );

		$main_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $new_config );

		// allow change slug only for users with admin capability
		if ( EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {

			// allow change slug if Setup Wizard is running for the first time
			if ( $this->is_setup_run_first_time || EPKB_Utilities::get_wp_option( 'epkb_not_completed_setup_wizard_' . $kb_id, false )  ) {
				$kb_slug = EPKB_Utilities::post( 'kb_slug', '', 'text', 100 );
				$kb_slug = empty( $kb_slug ) ? EPKB_KB_Handler::get_default_slug( $kb_id ) : sanitize_title_with_dashes( $kb_slug );
				wp_update_post( array( 'ID' => $main_page_id, 'post_name' => $kb_slug ) );
			}

			// ensure that KB URL and article common path are the same; if not make them so
			$main_page_slug = EPKB_Core_Utilities::get_main_page_slug( $main_page_id );
			if ( empty( $new_config['kb_articles_common_path'] ) || $new_config['kb_articles_common_path'] != $main_page_slug ) {
				$new_config['kb_articles_common_path'] = $main_page_slug;
			}
		}

		// update article sidebar
		$sidebar_settings_id = (int)EPKB_Utilities::post( 'sidebar', 0 );
		if ( $sidebar_settings_id ) {

			foreach ( EPKB_KB_Wizard_Themes::$sidebar_compacted as $setting_name => $values ) {
				// something went wrong with the settings
				if ( ! isset( $values[ $sidebar_settings_id ] ) ) {
					continue;
				}

				if ( isset( $new_config[ $setting_name ] ) ) {
					if ( $new_config[ $setting_name ] != $values[ $sidebar_settings_id ] ) {
						$new_config[ $setting_name ] = $values[ $sidebar_settings_id ];
					}
					continue;
				}

				if ( $new_config['article_sidebar_component_priority'][ $setting_name ] != $values[ $sidebar_settings_id ] ) {
					$new_config['article_sidebar_component_priority'][ $setting_name ] = $values[ $sidebar_settings_id ];
				}
			}

			// if user has KB Sidebar on, then preserve it
			if ( $new_config['article_sidebar_component_priority']['kb_sidebar_left'] != '0' ) {
				$new_config['article-left-sidebar-toggle'] = 'on';
				$new_config['article_sidebar_component_priority']['kb_sidebar_left'] = '2';
			}

			if ( $new_config['article_sidebar_component_priority']['kb_sidebar_right'] != '0' ) {
				$new_config['article-right-sidebar-toggle'] = 'on';
				$new_config['article_sidebar_component_priority']['kb_sidebar_right'] = '2';
			}
		}

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $kb_id, $orig_config, $new_config );
		if ( ! empty( $update_kb_msg ) ) {
			EPKB_Utilities::ajax_show_error_die( EPKB_Utilities::report_generic_error( 39, $update_kb_msg ) );
		}

		// update icons if user chose another theme design
		if ( $theme_name != 'current' ) {
			// if user selects Image theme then change font icons to image icons
			$categories_icons = EPKB_Icons::get_demo_category_icons( $new_config, $theme_name );
			if ( ! empty( $categories_icons ) ) {
				EPKB_Utilities::save_kb_option( $kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );
			}
		}

		if ( EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {

			// in case user changed article common path, flush the rules
			EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

			// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
			flush_rewrite_rules( false );
			update_option( 'epkb_flush_rewrite_rules', true );

			EPKB_Admin_Notices::remove_ongoing_notice( 'epkb_changed_slug' );
		}

		// mark setup wizard was completed at least once for the current KB - does not matter admin or editor user
		delete_option( 'epkb_not_completed_setup_wizard_' . $kb_id );

		// update KB ids list option that indicates for which KBs the Setup Wizard is completed at least once
		EPKB_Core_Utilities::update_kb_flag( 'completed_setup_wizard_' . $new_config['id'] );

		wp_die( json_encode( array(
				'message' => 'success',
				'redirect_to_url' => admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $new_config['id'] ) . '&page=epkb-kb-need-help&epkb_after_kb_setup' ) ) ) );
	}

	/**
	 * SETUP WIZARD - get updated configuration from selected pre-made design and add-ons
	 *
	 * @param $theme_name
	 * @param $orig_config
	 * @return array
	 */
	private function get_themed_kb_config( $theme_name, $orig_config ) {

		$kb_id = $orig_config['id'];
		$kb_status = $orig_config['status'];
		$kb_articles_common_path = $orig_config['kb_articles_common_path'];

		// get selected theme config
		$theme_config = EPKB_KB_Wizard_Themes::get_theme( $theme_name, $orig_config );

		// overwrite current KB configuration with new configuration from this Wizard
		$new_config = array_merge( $orig_config, $theme_config );

		$new_config['id'] = $kb_id;
		$new_config['status'] = $kb_status;
		$new_config['kb_articles_common_path'] = $kb_articles_common_path;

		return $new_config;
	}

	/**
	 * if no KB Main Page found, e.g. user deleted it after running Setup Wizard the first time, then try to create a new one
	 *
	 * @param $new_config
	 */
	private function create_main_page_if_missing( &$new_config ) {

		$kb_id = $new_config['id'];
		$kb_nickname = $new_config['kb_name'];

		$kb_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $new_config );
		if ( ! empty( $kb_page_id ) ) {
			return;
		}

		// get and sanitize KB slug
		$kb_slug = EPKB_Utilities::post( 'kb_slug', '', 'text', 100 );
		$kb_slug = empty( $kb_slug ) ? EPKB_KB_Handler::get_default_slug( $kb_id ) : sanitize_title_with_dashes( $kb_slug );

		$new_kb_main_page = EPKB_KB_Handler::create_kb_main_page( $kb_id, $kb_nickname, $kb_slug );
		if ( is_wp_error( $new_kb_main_page ) ) {
			EPKB_Logging::add_log( 'Could not create KB main page', $kb_id, $new_kb_main_page );
		} else {
			$new_config['kb_articles_common_path'] = urldecode(sanitize_title_with_dashes( $new_kb_main_page->post_name, '', 'save' ));
			$kb_main_pages[ $new_kb_main_page->ID ] = $new_kb_main_page->post_title;
			$new_config['kb_main_pages'] = $kb_main_pages;
		}
	}

	/**
	 * Add KB link to top menu
	 *
	 * @param $kb_main_pages
	 */
	private function add_kb_link_to_top_menu( $kb_main_pages ) {

		// add items to menus if needed
		$menu_ids = EPKB_Utilities::post( 'menu_ids', [] );
		if ( $menu_ids && ! empty( $kb_main_pages ) ) {
			foreach ( $menu_ids as $id ) {
				$itemData =  array(
					'menu-item-object-id'   => key($kb_main_pages),
					'menu-item-parent-id'   => 0,
					'menu-item-position'    => 99,
					'menu-item-object'      => 'page',
					'menu-item-type'        => 'post_type',
					'menu-item-status'      => 'publish'
				);

				wp_update_nav_menu_item( $id, 0, $itemData );
			}
		}
	}

	/**
	 * Triggered when user submits changes to KB configuration
	 *
	 * @param $kb_id
	 * @param $orig_config
	 * @param $new_config
	 * @return string
	 */
	private function update_kb_configuration( $kb_id, $orig_config, $new_config ) {

		// core handles only default KB
		if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			EPKB_Logging::add_log('Invalid kb_id (yx1)', $kb_id);
			return __('Ensure that Multiple KB add-on is active and refresh this page', 'echo-knowledge-base');
		}

		// if user switches layout then ensure the sidebar is set correctly; $orig_config is used to overwrite filter
		$new_config = EPKB_Editor_Controller::reset_layout( $orig_config, $new_config, false );

		// save add-ons configuration
		$result = apply_filters( 'eckb_kb_config_save_input_v3', '', $kb_id, $new_config );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not save the new configuration . (4)', $result );
		}

		// save KB core configuration
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_id, $new_config );
		if ( is_wp_error( $result ) ) {
			EPKB_Logging::add_log( 'Could not save the new configuration . (5)', $result );

			/* @var $result WP_Error */
			$message = $result->get_error_message();
			if ( empty($message) ) {
				return __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(3)';
			} else {
				return __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' );
			}
		}

		// we are done here
		return '';
	}

	/**
	 * Determine what sidebar setup the user has and return corresponding selection id.
	 *
	 * @param $kb_config
	 * @return int
	 */
	public static function get_current_sidebar_selection( $kb_config ) {

		if ( $kb_config['article-left-sidebar-toggle'] == 'on' && isset( $kb_config['article_sidebar_component_priority']['nav_sidebar_left'] ) && (int)$kb_config['article_sidebar_component_priority']['nav_sidebar_left'] ) {

			// Articles and Categories Navigation: Left Side
			if ( $kb_config['article_nav_sidebar_type_left'] == 'eckb-nav-sidebar-v1' ) {
				return 1;
			}

			// Top Categories Navigation: Left Side
			if ( $kb_config['article_nav_sidebar_type_left'] == 'eckb-nav-sidebar-categories' ) {
				return 3;
			}
		}

		if ( $kb_config['article-right-sidebar-toggle'] == 'on' && isset( $kb_config['article_sidebar_component_priority']['nav_sidebar_right'] ) && (int)$kb_config['article_sidebar_component_priority']['nav_sidebar_right'] ) {

			// Articles and Categories Navigation: Right Side
			if ( $kb_config['article_nav_sidebar_type_right'] == 'eckb-nav-sidebar-v1' ) {
				return 2;
			}

			// Top Categories Navigation: Right Side
			if ( $kb_config['article_nav_sidebar_type_right'] == 'eckb-nav-sidebar-categories' ) {
				return 4;
			}
		}

		// No Navigation/Default
		return 5;
	}
}
