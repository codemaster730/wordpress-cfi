<?php  if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Show setup wizard when plugin is installed
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Setup_Wizard {

	function __construct() {
		add_action( 'wp_ajax_epkb_apply_setup_wizard_changes',  array( $this, 'apply_setup_wizard_changes' ) );
	}

	/**
	 * Show KB Setup page
	 * @param int $kb_id
	 */
	public function display_kb_setup_wizard( $kb_id=EPKB_KB_Config_DB::DEFAULT_KB_ID ) {

		// ensure KB config is there
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) || empty($kb_config) || ! is_array($kb_config) || count($kb_config) < 100 ) {
			EPKB_Logging::add_log('Could not retrieve KB configuration (715)', $kb_config);
			$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
			//echo '<div class="epkb-wizard-error-note">' . __('Error occurred', 'echo-knowledge-base') . ' (x1) ' . EPKB_Utilities::contact_us_for_support() . '</div>';
		}

	   // core handles only default KB
	   if ( $kb_id != EPKB_KB_Config_DB::DEFAULT_KB_ID && ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
	      EPKB_Logging::add_log('Invalid kb_id (yx)', $kb_id);
	      echo '<div class="epkb-wizard-error-note">' . __('Ensure that Multiple KB add-on is active and refresh this page. ', 'echo-knowledge-base') . EPKB_Utilities::contact_us_for_support() . '</div>';
		   return;
	   }	?>

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
					</div>

					<!------- Wizard Status Bar ------->
					<div class="epkb-wizard-status-bar">
						<ul>
							<li id="epkb-wsb-step-1" class="epkb-wsb-step epkb-wsb-step--active"><?php _e( 'Title & URL', 'echo-knowledge-base' ); ?></li>
							<li id="epkb-wsb-step-2" class="epkb-wsb-step"><?php _e( 'Design', 'echo-knowledge-base' ); ?></li>
							<li id="epkb-wsb-step-3" class="epkb-wsb-step"><?php _e( 'Setup Completed', 'echo-knowledge-base' ); ?></li>
						</ul>
					</div>

					<!------- Top Button Bar -------->
					<div class="epkb-wizard-footer epkb-wizard-top-bar">
						<!----Step 2 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-2-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="1" id="epkb-setup-wizard-button-prev" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__icon epkbfa epkbfa-caret-left"></span>
									<span class="epkb-setup-wizard-button-prev__text"><?php _e( 'Previous', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="apply" id="epkb-setup-wizard-button-apply" class="epkb-wizard-button epkb-setup-wizard-button-apply" data-wizard-type="theme"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

								<input type="hidden" id="_wpnonce_apply_wizard_changes" name="_wpnonce_apply_wizard_changes" value="<?php echo wp_create_nonce( "_wpnonce_apply_wizard_changes" ); ?>">
							</div>
						</div>
					</div>

					<!------- Wizard Content ---------->
					<div class="epkb-wizard-content">
						<?php self::wizard_step_title_url( $kb_config ); ?>
						<?php self::setup_wizard_theme( $kb_config ); ?>
						<?php self::setup_wizard_frontend_editor( $kb_config ); ?>
					</div>

					<!------- Wizard Footer ---------->
					<div class="epkb-wizard-footer">

						<!----Step 1 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-1-panel-button epkb-wc-step-panel-button epkb-wc-step-panel-button--active">
							<div class="epkb-wizard-button-container__inner">
								<button value="2" id="epkb-setup-wizard-button-next" class="epkb-wizard-button epkb-setup-wizard-button-next">
									<span class="epkb-setup-wizard-button-next__text"><?php _e( 'Next', 'echo-knowledge-base' ); ?></span>
									<span class="epkb-setup-wizard-button-next__icon epkbfa epkbfa-caret-right"></span>
								</button>
							</div>
						</div>

						<!----Step 2 Buttons---->
						<div class="epkb-wizard-button-container epkb-wsb-step-2-panel-button epkb-wc-step-panel-button">
							<div class="epkb-wizard-button-container__inner">
								<button value="1" id="epkb-setup-wizard-button-prev" class="epkb-wizard-button epkb-setup-wizard-button-prev">
									<span class="epkb-setup-wizard-button-prev__icon epkbfa epkbfa-caret-left"></span>
									<span class="epkb-setup-wizard-button-prev__text"><?php _e( 'Previous', 'echo-knowledge-base' ); ?></span>
								</button>
								<button value="apply" id="epkb-setup-wizard-button-apply" class="epkb-wizard-button epkb-setup-wizard-button-apply" data-wizard-type="theme"><?php _e( 'Apply', 'echo-knowledge-base' ); ?></button>

								<input type="hidden" id="_wpnonce_apply_wizard_changes" name="_wpnonce_apply_wizard_changes" value="<?php echo wp_create_nonce( "_wpnonce_apply_wizard_changes" ); ?>">
							</div>
						</div>

					</div>

					<input type="hidden" id="epkb_wizard_kb_id" name="epkb_wizard_kb_id" value="<?php echo $kb_id; ?>"/>

					<div class="eckb-bottom-notice-message"></div>
				</div>
			</div>
		</div>		<?php
	}

	// Setup Wizard: Step 1 - Title & URL
	private function wizard_step_title_url( $kb_config ) {

	   $html = new EPKB_HTML_Elements(); 	   ?>

		<div id="epkb-wsb-step-1-panel" class="epkb-wc-step-panel eckb-wizard-step-1  epkb-wc-step-panel--active epkb-wizard-theme-step-1 ">  <?php

			// KB Name
		   $html->text(
				array(
					'label'             => __('Knowledge Base Title', 'echo-knowledge-base'),
					'placeholder'       => __('Knowledge Base', 'echo-knowledge-base'),
					'main_tag'          => 'div',
					'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-name',
					'value'             => $kb_config['kb_name']
				)
			);      ?>
			<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p class="epkb-wizard-input-desc"><?php
						_e( 'Name and page title of your knowledge base<br/>Examples: Knowledge Base, Help, Support', 'echo-knowledge-base' );							?>
					</p>
				</div>
			</div>			<?php

			// KB Slug
		   $html->text(
				array(
					'label'             => __('Knowledge Base Slug', 'echo-knowledge-base'),
					'placeholder'       => 'knowledge-base',
					'main_tag'          => 'div',
					'readonly'          => false,
					'input_group_class' => 'epkb-wizard-row-form-input epkb-wizard-slug',
					'value'             => $kb_config['kb_articles_common_path'],
				)
			);      ?>
			<div class="epkb-wizard-row-form-input">
				<div class="epkb-wizard-col2">
					<p id="epkb-wizard-slug-error">
						<?php _e('The slug should not contain full KB URL.', 'echo-knowledge-base'); ?>
					</p>
					<p class="epkb-wizard-input-desc"><?php _e( 'This is KB slug that is be part of your full knowledge base URL.<br/>Example of KB URL: &nbsp;&nbsp;www.your-domain.com/your-KB-slug', 'echo-knowledge-base' ); ?>
					</p>
				</div>
			</div>				<?php

			// if we have menus and menus without link
			$menus = $this->kb_menus_without_item( $kb_config );
			if ( is_array($menus) && ! empty($menus) ) {      ?>

				<div class="input_group epkb-wizard-row-form-input epkb-wizard-menus" >
					<label><?php _e( 'Add KB to Website Menu', 'echo-knowledge-base' ); ?></label>
					<ul>	<?php
						foreach ($menus as $menu_id => $menu_title) {
					   $html->checkbox( array(
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

	// Setup Wizard: Step 2 - Choose Design
	private function setup_wizard_theme( $kb_config ) {

		$preset_options = array();
		$theme_description = EPKB_KB_Wizard_Themes::get_themes_description();
		foreach ( EPKB_KB_Wizard_Themes::get_all_presets( $kb_config ) as $theme_slug => $theme_data ) {
			$preset_options[$theme_data['kb_main_page_layout']][$theme_slug] = $theme_data['kb_name'];
		}		?>

		<div id="epkb-wsb-step-2-panel" class="epkb-setup-wizard-theme epkb-wc-step-panel eckb-wizard-step-2">
			<div class="epkb-setup-wizard-theme-header">
				<h2 class="epkb-setup-wizard-theme-header__info__title">
					<?php _e( 'Choose initial Knowledge Base design and then change colors and other elements in our front-end Editor.', 'echo-knowledge-base' ); ?>
				</h2>
			</div>
			<div class="epkb-setup-wizard-theme-preview">

				<!-- THEME BUTTONS -->
				<div class="epkb-wizard-theme-tab-container">
					<input type="hidden" id="_wpnonce_setup_wizard_templates" name="_wpnonce_setup_wizard_templates" value="<?php echo wp_create_nonce( "_wpnonce_setup_wizard_templates" ); ?>"/>		<?php

					// add categories  	?>
					<div class="epkb-setup-wizard-group__container"> <?php
					foreach ( $preset_options as $title => $group ) { ?>

						<div class="epkb-setup-wizard-group__container-inner">
							<div class="epkb-setup-wt-tc__themes-group__header__title"><?php echo $title; ?></div>
						    <div class="epkb-setup-wt-tc__themes-group__header__desc"><?php echo $theme_description[$title]; ?></div>

							<div class="epkb-setup-wt-tc__themes-group__list config-input-group"><?php
							foreach ( $group as $template_id => $template_name ) { ?>
								<div id="epkb-setup-wt-theme-<?php echo $template_id; ?>-panel" class="epkb-setup-option-container">
									<div class="epkb-setup-option__inner">
										<div class="epkb-setup-option__featured-img-container">
											<img class="epkb-setup-option__featured-img" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/' . EPKB_KB_Wizard_Themes::$theme_images[$template_id]; ?>" title="<?php echo $template_name; ?>" />
										</div>
										<div class="epkb-setup-option__option-container">
											<label class="epkb-setup-option__option__label">
												<input type="radio" name="epkp-theme" value="<?php echo $template_id; ?>"><?php echo $template_name; ?>
											</label>
										</div>
									</div>
								</div>		<?php
							} ?>
							</div>

						</div><?php
					} ?>
					</div>

				</div>

			</div>
		</div>	<?php
	}

	// Setup Wizard: Step 3 - Frontend Editor
	private function setup_wizard_frontend_editor( $kb_config ) {

	   $main_url = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );
	   $editor_urls = EPKB_Editor_Utilities::get_editor_urls( $kb_config, 'templates' );     ?>

		<div id="epkb-wsb-step-3-panel" class="epkb-setup-wizard-frontend-editor epkb-wc-step-panel eckb-wizard-step-3">
			<div class="epkb-setup-wizard-theme-header">
				<h1 class="epkb-setup-wizard-theme-header__info__main_title">
					<span class="epkbfa epkbfa-check-circle epkb-success-icon"></span>
					<span><?php _e( 'Your Knowledge Base is now ready!', 'echo-knowledge-base' );  ?></span>
				</h1>
				<h2 class="epkb-setup-wizard-theme-header__info__title">
					<?php _e( 'What would you like to do next?', 'echo-knowledge-base' ); ?>
				</h2>
			</div>

			<div class="epkb-setup-info-box-container">

				<div class="epkb-setup-info-box">
					<img src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/editor/basic-layout-light.jpg'; ?>">
					<h3>View My Knowledge Base</h3>
					<p><?php echo __( 'I want to see my new Knowledge Base (with demo content)', 'echo-knowledge-base' ) . ':'; ?></p>
					<a href="<?php echo $main_url; ?>" class="epkb-setup-info-box__link epkb-setup-info-box__link--view" target="_blank">
						<?php _e( 'View', 'echo-knowledge-base' ); ?></a>
				</div>

				<div class="epkb-setup-info-box">
					<img src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/editor/basic-layout-light-KB-Editor.jpg'; ?>">
					<h3>Frontend Editor</h3>
					<p><?php echo __( 'I want to jump into customizing the Knowledge Base colors, style, and features', 'echo-knowledge-base' ) . ':'; ?></p>
					<a href="<?php echo $editor_urls['main_page_url']; ?>" class="epkb-setup-info-box__link epkb-setup-info-box__link--edit" target="_blank"><?php _e( 'Start Editing', 'echo-knowledge-base' ); ?></a>
				</div>

			</div>

			<strong style="padding: 10px 0 50px 0; display: block;"><?php echo sprintf(__( 'Please use the Knowledge Base admin menu on the left to add %s and %s.', 'echo-knowledge-base' ), "<a href='edit.php?post_type=" . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . "'>articles</a>" ,"<a href='edit-tags.php?post_type=" . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . "&taxonomy=" . EPKB_KB_Handler::get_category_taxonomy_name( $kb_config['id'] ) . "'>categories</a>"); ?></strong>

		</div>	<?php
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

		// verify that request is authentic
		if ( empty( $_REQUEST['_wpnonce_apply_wizard_changes'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_apply_wizard_changes'], '_wpnonce_apply_wizard_changes' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) . ' (157)' );
		}

		// ensure that user has correct permissions
		if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) . ' (158)' );
		}

		// get current KB ID
		$wizard_kb_id = EPKB_Utilities::post('epkb_wizard_kb_id');
		if ( empty($wizard_kb_id) || ! EPKB_Utilities::is_positive_int( $wizard_kb_id ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameter. Please refresh your page', 'echo-knowledge-base' ) . ' (159)' );
		}

		// get selected Theme Name
		$theme_name = EPKB_Utilities::post('theme_name');
		if ( empty($theme_name) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Invalid parameter. Please refresh your page', 'echo-knowledge-base' ) . ' (160)' );
		}

		// get current KB configuration
		$orig_config = epkb_get_instance()->kb_config_obj->get_kb_config( $wizard_kb_id );
		if ( is_wp_error( $orig_config ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred.', 'echo-knowledge-base' ) . EPKB_Utilities::contact_us_for_support() . ' ' . $orig_config->get_error_message() . ' (8)' );
		}

	   // get current Add-ons configuration
		$orig_config = apply_filters( 'epkb_all_wizards_get_current_config', $orig_config, $wizard_kb_id );
		if ( empty($orig_config) || count($orig_config) < 3 ) {
		  EPKB_Utilities::ajax_show_error_die( __( 'Error occurred.', 'echo-knowledge-base' ) . EPKB_Utilities::contact_us_for_support() . ' ' . $orig_config . ' (8)' );
		}

		// get selected theme config
		$theme_config = EPKB_KB_Wizard_Themes::get_theme( $theme_name, $orig_config );

		// overwrite current KB configuration with new configuration from this Wizard
		$new_config = array_merge($orig_config, $theme_config);

		$kb_id = $orig_config['id'];

		// if KB Main Page exists then use it
		$kb_page_id = EPKB_KB_Handler::get_first_kb_main_page_id( $orig_config );
		if ( ! empty($kb_page_id) ) {

			// get and sanitize KB name
			$kb_name = EPKB_Utilities::post('kb_name');
			$kb_name = empty($kb_name) ? '' : substr( $kb_name, 0, 50 );
			$kb_name = sanitize_text_field($kb_name);
			if ( empty($kb_name) ) {
				$kb_name = __( 'Knowledge Base', 'echo-knowledge-base' ) . ( $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : ' ' . $kb_id );
			}

			// get and sanitize KB slug
			$kb_slug = EPKB_Utilities::post('kb_slug');
			$kb_slug = empty($kb_slug) ? '' : substr( $kb_slug, 0, 100 );
			$kb_slug = sanitize_title($kb_slug);

			// update the post
			$my_post = array(
			  'ID'           => $kb_page_id,
			  'post_title'   => $kb_name,
			  'post_name'    => $kb_slug
			);
			$post_id = wp_update_post( $my_post );
			if ( is_wp_error($post_id) ) {
				EPKB_Logging::add_log( 'Could not update KB Name and Slug', $kb_page_id, $post_id );

			} else {
				 $post = WP_Post::get_instance( $post_id );
				 if ( ! empty($post) || ! empty($post->ID) ) {
					$new_config['kb_articles_common_path'] = urldecode(sanitize_title_with_dashes( $post->post_name, '', 'save' ));
					$new_config['kb_name'] = $post->post_title;
					$kb_main_pages[ $post->ID ] = $post->post_title;
					$new_config['kb_main_pages'] = $kb_main_pages;
				}
			}
		}

		// prevent new config to overwrite essential fields
		$new_config['id'] = $kb_id;
		$new_config['status'] = $orig_config['status'];

		// update KB and add-ons configuration
		$update_kb_msg = $this->update_kb_configuration( $kb_id, $orig_config, $new_config );
		if ( ! empty($update_kb_msg) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Error occurred. Could not create KB (36). ' . $update_kb_msg, 'echo-knowledge-base' ) . EPKB_Utilities::contact_us_for_support() );
		}

		// if user selectes Image theme then change font icons to image icons
		$categories_icons = EPKB_Icons::get_demo_category_icons( $new_config, $new_config['theme_name'] );
		if ( ! empty($categories_icons) ) {
			EPKB_Utilities::save_kb_option( $kb_id, EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );
		}

		// add items to menus if needed
		$menu_ids = EPKB_Utilities::post( 'menu_ids', array(), false );
		if ( $menu_ids && ! empty($new_config['kb_main_pages']) ) {
			$kb_main_pages = $new_config['kb_main_pages'];
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

		// in case user changed article common path, flush the rules
		EPKB_Articles_CPT_Setup::register_custom_post_type( $new_config, $new_config['id'] );

		// always flush the rules; this will ensure that proper rewrite rules for layouts with article visible will be added
		flush_rewrite_rules( false );
		update_option('epkb_flush_rewrite_rules', true);

		EPKB_Admin_Notices::remove_ongoing_notice( 'epkb_changed_slug' );
		$editor_urls = EPKB_Editor_Utilities::get_editor_urls( $new_config, 'templates' );

		$message = __('Configuration Saved', 'echo-knowledge-base');
		$main_url = EPKB_KB_Handler::get_first_kb_main_page_url( $new_config );
		wp_die( json_encode( array( 'message' => $message, 'kb_main_page_link' => $editor_urls['main_page_url'], 'kb_main_page_view_link' => $main_url ) ) );
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
}
