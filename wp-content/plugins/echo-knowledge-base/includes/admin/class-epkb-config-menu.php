<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display KB configuration menu and pages
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Config_Menu {

	private $message = array(); // error/warning/success messages

	/**
	 * Displays the KB Config page with top panel + sidebar + preview panel
	 */
	public function display_kb_config_page() {

	   // retrieve current KB configuration
	   $kb_config = epkb_get_instance()->kb_config_obj->get_current_kb_configuration();
	   if ( is_wp_error( $kb_config ) || empty($kb_config) || ! is_array($kb_config) || count($kb_config) < 100 ) {
		   $kb_config = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );
	   }

	   // handle user interactions
		$kb_config = $this->switch_user_to_article_v2( $kb_config );

		// get current add-ons configuration
		$wizard_kb_config = $kb_config;
		$wizard_kb_config = apply_filters( 'epkb_all_wizards_get_current_config', $wizard_kb_config, EPKB_KB_Handler::get_current_kb_id() );
		if ( is_wp_error( $wizard_kb_config ) ) {
			echo '<p>' . __( 'Could not retrieve KB configuration.', 'echo-knowledge-base' ) . ' (777: ' . $wizard_kb_config->get_error_message() . ') ' . EPKB_Utilities::contact_us_for_support() . '</p>';
			return;
		}
		if ( empty($wizard_kb_config) || ! is_array($wizard_kb_config) || count($wizard_kb_config) < 100 ) {
			echo '<p>' . __( 'Could not retrieve KB configuration.', 'echo-knowledge-base' ) . ' (7782) ' . EPKB_Utilities::contact_us_for_support() . '</p>';
			return;
		}

		// should we display Setup Wizard or KB Configuration?
		if ( isset($_GET['setup-wizard-on']) ) {
			$handler = new EPKB_KB_Setup_Wizard();
			$handler->display_kb_setup_wizard( $wizard_kb_config['id'] );
			return;
		}

		// should we display Ordering Wizard or KB Configuration?
		if ( isset($_GET['wizard-ordering']) ) {
			$handler = new EPKB_KB_Wizard_Ordering();
			$handler->display_kb_wizard( $wizard_kb_config );
			return;
		}

	   // should we display Wizard or KB Configuration?
	   if ( isset($_GET['wizard-theme']) ) {
		   $handler = new EPKB_KB_Wizard();
		   $handler->display_kb_wizard( $wizard_kb_config );
		   return;
	   }

	   // should we display Text Wizard or KB Configuration?
	   if ( isset($_GET['wizard-text']) ) {
		   $handler = new EPKB_KB_Wizard_Text();
		   $handler->display_kb_wizard( $wizard_kb_config );
		   return;
	   }

	   // should we display Features Wizard or KB Configuration?
	   if ( isset($_GET['wizard-features']) ) {
		   $handler = new EPKB_KB_Wizard_Features();
		   $handler->display_kb_wizard( $wizard_kb_config );
		   return;
	   }

	   // should we display Search Wizard or KB Configuration?
	   if ( isset($_GET['wizard-search']) ) {
		   $handler = new EPKB_KB_Wizard_Search();
		   $handler->display_kb_wizard( $wizard_kb_config );
		   return;
	   }

	   // should we display Global Wizard or KB Configuration?
		if ( isset($_GET['wizard-global']) ) {
			//Remove Slug change warning
			$notice_id = 'epkb_changed_slug_' . $wizard_kb_config['id'];
			EPKB_Admin_Notices::dismiss_ongoing_notice( $notice_id );

			$handler = new EPKB_KB_Wizard_Global();
			$handler->display_kb_wizard( $wizard_kb_config );
			return;
		}
		
		// display all elements of the configuration page
		$this->display_page( $kb_config );

		// show any notifications
		foreach ( $this->message as $class => $message ) {
			echo  EPKB_Utilities::get_bottom_notice_message_box( $message, '', $class );
		}
	}

	/**
	 * Display KB Config content areas
	 * @param $kb_config
	 */
	private function display_page( $kb_config ) {        ?>
		<div class="wrap">
			<h1></h1>
		</div>

		<h1 style="color: red; line-height: 1.2em; background-color: #eaeaea; border: solid 1px #ddd; padding: 20px;" class="epkb-css-working-hide-message">The CSS for this KB admin page is missing. This is most likely due to page loading interuption or one of your plugins blocking our CSS.
			First, try to refresh the page. If that does not help, try to deactivate your plugins or contact us for help.</h1>

		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap epkb-config-container">
			<div class="epkb-config-wrapper">

				<div class="wrap" id="ekb_core_top_heading"></div>

				<div id="epkb-config-main-info">		<?php
					$this->display_top_panel( $kb_config );         ?>
				</div>

				<div>          <?php
					$this->display_editor_panels( $kb_config );      ?>
				</div>

			</div>

         <div class="eckb-bottom-notice-message"></div>
		</div>	    <?php
	}

	/**
	 * Display top overview panel
	 * @param $kb_config
	 */
	private function display_top_panel( $kb_config ) {

		// first cleanup KB Main Pages
		$kb_main_pages = $kb_config['kb_main_pages'];
		if ( count($kb_main_pages) > 1 ) {
		  EPKB_KB_Handler::get_kb_main_pages( $kb_config );
		}

		// display link to KB Main Page if any
		$link_output = EPKB_KB_Handler::get_first_kb_main_page_url( $kb_config );

		$style = '';
		if ( empty($link_output) ) {
			$global_wizard_link = esc_url( admin_url('edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-kb-configuration&wizard-global' ) );
            $link_output = '<a href="' . $global_wizard_link . '" class="shortcode_error" style="text-decoration:underline">' . __( "Add Shortcode", "echo-knowledge-base" ) . '</a>';
            $style = 'style="padding: 20px 10px;"';
		} else {
			$link_output = '<a href="' . $link_output . '" target="_blank"><div class="epkb-view ep_font_icon_external_link"></div></a>';
		}

		$page_url = 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] );

		$show_overview_page = true;      ?>

		<div class="epkb-info-section epkb-kb-name-section">   <?php
			self::display_list_of_kbs( $kb_config ); 			?>
		</div>
		<div class="epkb-info-section epkb-info-main has-margin" id="epkb-view-kb-button" <?php echo $style; ?>>
			<div class="overview-icon-container">
				<p><?php _e( 'View KB', 'echo-knowledge-base' ); ?></p>
            	<?php echo $link_output; ?>
			</div>
      </div>

		<!-- OVERVIEW -->
		<div class="epkb-info-section epkb-info-main <?php echo $show_overview_page ? 'epkb-active-page' : ''; ?>">
			<div class="overview-icon-container">
				<p><?php _e( 'Overview', 'echo-knowledge-base' ); ?></p>
				<div class="page-icon overview-icon ep_font_icon_data_report" id="epkb-config-overview"></div>
			</div>
		</div>

		<!--  FRONTEND EDITOR BUTTON -->		 <?php
		$epkp_open_dialog_class = '';
		if ( self::is_frontend_editor_hidden( $kb_config ) != '' ) {

			$epkp_open_dialog_class = 'epkb-article-structure-dialog';			?>

			<div id='epkb-editor-disabled'>  <?php
				EPKB_Utilities::dialog_box_form( array(
					'id' => 'epkb-editor-disabled-popup',
					'title' => __( 'Warning', 'echo-knowledge-base' ),
					'body' => self::is_frontend_editor_hidden( $kb_config ),
					'accept_type' => 'warning',
					'accept_label' => __( 'OK', 'echo-knowledge-base' ),
					'form_inputs' => array(
						0 => '<input type="hidden" name="_wpnonce_update_kbs" value="' . wp_create_nonce( "_wpnonce_update_kbs" ) . '">',
						1 => '<input type="hidden" name="action" value="epkb_update_article_v2">',
						2 => '<input type="hidden" name="epkb_kb_id" value="'.$kb_config['id'].'">'
					),
				) ); ?>
			</div>			<?php
		} 	?>

		<div class="epkb-info-section epkb-info-pages" id="epkb-main-page-button">
			<div class="page-icon-container">
				<p><?php _e( 'Frontend Editor', 'echo-knowledge-base' ); ?></p>
				<div class="page-icon ep_font_icon_flow_chart <?php echo $epkp_open_dialog_class; ?>" id="epkb-config-editor"></div>
			</div>
		</div>

		<!--  Ordering BUTTON -->
		<div class="epkb-info-section epkb-info-pages" id="epkb-article-page-button">
			<div class="page-icon-container">
				<p><?php _e( 'Order Articles', 'echo-knowledge-base' ); ?></p>
				<a href="<?php echo admin_url( $page_url ) . '&page=epkb-kb-configuration&wizard-ordering'; ?>"><div class="page-icon ep_font_icon_document" id="epkb-article-page"></div></a>
			</div>
		</div>

		<!--  KB URLs BUTTON -->
		<div class="epkb-info-section epkb-info-pages" id="epkb-archive-page-button">
			<div class="page-icon-container">
				<p><?php _e( 'KB URLs', 'echo-knowledge-base' ); ?></p>
				<a href="<?php echo admin_url( $page_url ) . '&page=epkb-kb-configuration&&wizard-global'; ?>"><div class="page-icon epkbfa epkbfa-archive" id="epkb-archive-page"></div></a>
			</div>
		</div>

		<!--  Help Dialog BUTTON -->
		<!-- <div class="epkb-info-section epkb-info-pages" id="epkb-help-dialog-page-button">
			<div class="page-icon-container">
				<p><?php //_e( 'Help Dialog', 'echo-knowledge-base' ); ?></p>
				<div class="page-icon ep_font_icon_info <?php //echo $epkp_open_dialog_class; ?>" id="epkb-help-dialog"></div>
			</div>
		</div> -->

		<!--  OLD CONFIG BUTTON -->		 <?php
		if ( self::is_frontend_editor_hidden( $kb_config ) != '' ) { ?>
			<div class="epkb-info-section epkb-info-pages">
				<div class="page-icon-container">
					<p><?php _e( 'Old Config', 'echo-knowledge-base' ); ?></p>
					<div class="page-icon wizard-icon epkbfa epkbfa-sitemap" id="epkb-old-config"></div>
				</div>
			</div>   <?php
	   }

		do_action( 'eckb_admin_config_menu', $kb_config['id'] ); ?>

		<div class="support-icon-container">
			<a href="https://www.echoknowledgebase.com/front-end-editor-support-and-questions/" target="_blank"><?php _e( 'Need Help', 'echo-knowledge-base' ); ?> <span class="epkbfa epkbfa-question-circle-o"></span></a>
		</div>

		<div class="epkb-open-mm">
			<span class="ep_font_icon_arrow_carrot_down"></span>
		</div>      <?php
	}

	private function display_editor_panels( $kb_config ) {

		$show_overview_page = true;
		$editor_urls = EPKB_Editor_Utilities::get_editor_urls( $kb_config );
		$html = new EPKB_HTML_Elements();

		if ( ! current_user_can( EPKB_Utilities::EPKB_ADMIN_CAPABILITY ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission to edit this knowledge base', 'echo-knowledge-base' ) );
			return;
		}        ?>

		<!-- OVERVIEW Tab Content -->
		<div class="epkb-config-content epkb-config-content-wrapper" id="epkb-config-overview-content" <?php echo $show_overview_page ? '' : 'style="display: none;"'; ?>>   <?php
			EPKB_KB_Config_Overview::display_overview( $kb_config );  	?>
		</div>

		<!--  FRONTEND EDITOR Tab Content -->
		<div class="epkb-wizards epkb-config-content-wrapper" id="epkb-config-editor-content" style="display: none;">
			<section class="epkb-wizards__row-3-col">				<?php

				// MAIN PAGE link to editor
				if ( $editor_urls['main_page_url'] != '' ) {
					self::display_wizard_box(array(
						'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
						'title' => __('Main Page', 'echo-knowledge-base'),
						'btn_text' => __('Configure', 'echo-knowledge-base'),
						'btn_url' => $editor_urls['main_page_url'],
						'btn_target' => "_blank"
					));
				} else {
					self::display_wizard_box( array(
					   'icon_img_url'  => 'img/editor/basic-layout-light.jpg',
						'title'         => __( 'Main Page', 'echo-knowledge-base' ),
						'content'       => __( 'No Main Page Found', 'echo-knowledge-base' ),
						'btn_text'      => __( 'Add Shortcode', 'echo-knowledge-base' ),
						'btn_url'       => admin_url("edit.php?post_type=" . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . "&page=epkb-kb-configuration&wizard-global"),
						'btn_target'	  => "_blank",
						'wizard_type'   => "not_found"
					));
				}

				// ARTICLE PAGE link to editor
				if ( $editor_urls['article_page_url'] != '' ) {
					self::display_wizard_box( array(
						'icon_img_url'  => 'img/editor/article-page.jpg',
						'title'         => __( 'Article Page', 'echo-knowledge-base' ),
						'btn_text'      => __( 'Configure', 'echo-knowledge-base' ),
						'btn_url'       => $editor_urls['article_page_url'],
						'btn_target'    => "_blank"
					));
				} else {
					self::display_wizard_box( array(
					   'icon_img_url'  => 'img/editor/article-page.jpg',
						'title'         => __( 'Article Page', 'echo-knowledge-base' ),
						'content'       => __( 'No Articles with at least one Category were found.', 'echo-knowledge-base' ),
						'btn_text'      => __( 'Add New Article', 'echo-knowledge-base' ),
						'btn_url'       => admin_url("post-new.php?post_type=" . EPKB_KB_Handler::get_post_type( $kb_config['id'] )),
						'btn_target'    => "_blank",
						'wizard_type'   => "not_found"
					));
				}

				// ARCHIVE PAGE link to editor
				if ( $kb_config['templates_for_kb'] == 'current_theme_templates' ) {
					self::display_wizard_box(array(
						'icon_img_url'  => 'img/editor/category-archive-page.jpg',
						'title' => __('Category Archive Page', 'echo-knowledge-base'),
						'content' => sprintf(  __('The KB template option is set to the Current Theme. You need to configure your Archive Page template in ' .
						                          'your theme settings. For details about the KB template option see %s', 'echo-knowledge-base'),
					                        ' <a href="https://www.echoknowledgebase.com/documentation/current-theme-template-vs-kb-template/" target="_blank">' . __( 'here', 'echo-knowledge-base' ) . '.' . '</a> ' )
					));
				} else if ( $editor_urls['archive_url'] != '' ) {
					self::display_wizard_box(array(
						'icon_img_url'  => 'img/editor/category-archive-page.jpg',
						'title' => __('Category Archive Page', 'echo-knowledge-base'),
						'btn_text' => __('Configure', 'echo-knowledge-base'),
						'btn_url' => $editor_urls['archive_url'],
						'btn_target' => "_blank"
					));
				} else {
					self::display_wizard_box(array(
					   'icon_img_url'  => 'img/editor/category-archive-page.jpg',
						'title' => __('Category Archive Page', 'echo-knowledge-base'),
						'content' => __('No Categories Found', 'echo-knowledge-base'),
						'btn_text' => __('Add New Category', 'echo-knowledge-base'),
						'btn_url' => admin_url('edit-tags.php?taxonomy=' . EPKB_KB_Handler::get_category_taxonomy_name( $kb_config['id'] ) .'&post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] )),
						'btn_target' => "_blank",
						'wizard_type'   => "not_found"
					));
				}	?>
			</section>
		</div>

		<!--  Help Dialog Tab Content -->
		<div class="epkb-config-settings epkb-config-content-wrapper" id="epkb-help-dialog-content" style="display: none;">			<?php
			
			$tabs = array( 'tabs' => [
				'overview' => array(
					'icon' => 'epkbfa-info-circle',
					'text' => 'Overview',
					'content' => EPKB_Help_Dialog_View_FAQ::show_help_dialog_overview()
				)]
			);
			if ( EPKB_Help_Dialog_View::is_help_dialog_enabled() ) {
				$tabs['tabs']['locations'] = array(
					'icon' => 'epkbfa-cogs',
					'text' => 'Display Location',
					'content' => EPKB_Help_Dialog_View_Location::show_help_dialog()
				);
				$tabs['tabs']['faqs'] =  array(
					'icon' => 'epkbfa-clipboard',
					'text' => 'FAQs',
					'content' => EPKB_Help_Dialog_View_FAQ::show_help_dialog()
				);
			}

			$html->tabs( $tabs );    ?>

		</div>

 		<!--  OLD CONFIG BUTTON -->         <?php
		if ( self::is_frontend_editor_hidden( $kb_config ) != '' ) { ?>
		   <div class="epkb-wizards epkb-config-content-wrapper" id="epkb-old-config-content"
		        style="display: none;">   <?php
		     EPKB_KB_Config_Wizards::display_page( $kb_config['id'], true ); ?>
		   </div>   <?php
		}

		do_action( 'eckb_admin_config_page', $kb_config );
	}

	public static function is_frontend_editor_hidden( $kb_config ) {
		$issues_found = '';

		if ( $kb_config['article-structure-version'] == 'version-1' ) {
		  $issues_found .= 'Before accessing the frontend Editor, we need to update your setup to use our new article HTML structure. ' .
		                   'You might need to make adjustments to your article page after the update. Would you like to proceed? ';
		}

		if ( class_exists('Echo_Elegant_Layouts') && version_compare(Echo_Elegant_Layouts::$version, '2.6.0', '<') ) {
		  $issues_found .= 'Please upgrade Elegant Layouts plugin to the 2.6.0 version before accessing the frontend Editor. ';  // do not translate
		}

		if ( class_exists('Echo_Advanced_Search') && version_compare(Echo_Advanced_Search::$version, '2.14.0', '<') ) {
			$issues_found .= 'Please upgrade Advanced Search plugin to the 2.14.0 version before accessing the frontend Editor. ';
		}

		if ( class_exists('Echo_Article_Rating_And_Feedback') && version_compare(Echo_Article_Rating_And_Feedback::$version, '1.4.0', '<') ) {
			$issues_found .= 'Please upgrade Article Rating & Feedback plugin to the 1.4.0 version before accessing the frontend Editor. ';
		}

		if ( class_exists('Echo_Widgets') && version_compare(Echo_Widgets::$version, '1.9.0', '<') ) {
		 	$issues_found .= 'Please upgrade KB Widgets plugin to the 1.9.0 version before accessing the frontend Editor. ';
		}

	   return $issues_found;
	}

	/**
	 * Show a box with Icon, Title, Description and Link
	 *
	 * @param $args array

	 * - ['icon_class']     Top Icon to display ( Choose between these available ones: https://fontawesome.com/v4.7.0/icons/ )
	 * - ['title']          H3 title of the box.
	 * - ['content']        Body content of the box.
	 * - ['btn_text']       Show button and the text of the button at the bottom of the box, if no text is defined no button will show up.
	 * - ['btn_url']        Button URL.
	 */
	private static function display_wizard_box( $args ) { ?>

		 <div class="epkb-wizard-box-container_1 <?php echo isset( $args['wizard_type'] )  ? 'epkb-wizard-type__' . $args['wizard_type'] : '' ?>">

			 <!-- Header -------------------->
			 <div class="epkb-wizard-box__header">
				 <h3 class="epkb-wizard-box__header__title"><?php echo $args['title']; ?></h3>

				 <?php if ( isset( $args['icon_class'] ) ) { ?>
					 <i class="epkb-wizard-box__header__icon epkbfa <?php echo $args['icon_class']; ?>"></i>
				 <?php } elseif ( isset($args['icon_img_url'] ) ) { ?>
					 <span class="epkb-wizard-box__header__img">
						 <img src="<?php echo Echo_Knowledge_Base::$plugin_url . '' . $args['icon_img_url']; ?>">
					 </span>
				<?php  }				 ?>

			 </div>

			 <!-- Body ---------------------->
			<?php if ( isset( $args['content'] ) ) { ?>
			 <div class="epkb-wizard-box__body">
			  <?php echo empty($args['content']) ? '' : $args['content']; ?>
			 </div>
			<?php  }

			if ( ! empty($args['btn_target']) ) {    ?>
				 <!-- Footer ---------------------->
				 <div class="epkb-wizard-box__footer">
					 <a class="epkb-wizard-box__footer__button" href="<?php echo esc_url( $args['btn_url'] ); ?>" target="<?php echo isset( $args['btn_target'] ) ? esc_attr( $args['btn_target'] ) : ''; ?>"><?php echo $args['btn_text']; ?></a>
				 </div>  <?php
			 } ?>

		 </div>	<?php
	}

	/**
	 * Display list of KBs.
	 *
	 * @param $kb_config
	 * @param bool $is_wizard_on
	 */
	private static function display_list_of_kbs( $kb_config, $is_wizard_on=false ) {

		if ( ! EPKB_Utilities::is_multiple_kbs_enabled() ) {
			$kb_name = $kb_config[ 'kb_name' ];
			echo '<h1 class="epkb-kb-name">' . esc_html( $kb_name ) . '</h1>';
			return;
		}

		// output the list
		$list_output = '<select class="epkb-kb-name" id="epkb-list-of-kbs">';
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		foreach ( $all_kb_configs as $one_kb_config ) {

			if ( $one_kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
				continue;
			}

			$kb_name = $one_kb_config[ 'kb_name' ];
			$active = ( $kb_config['id'] == $one_kb_config['id'] ? 'selected' : '' );
			$tab_url = 'edit.php?post_type=' . EPKB_KB_Handler::KB_POST_TYPE_PREFIX . $one_kb_config['id'] . '&page=epkb-kb-configuration' . ( $is_wizard_on ? '&wizard-on' : '' );

			$list_output .= '<option value="' . $one_kb_config['id'] . '" ' . $active . ' data-kb-admin-url=' . esc_url($tab_url) . '>' . esc_html( $kb_name ) . '</option>';
			$list_output .= '</a>';
		}

		$list_output .= '</select>';

		echo $list_output;
	}

	/***
	 * Handle Form Action
	 *
	 * @param $kb_config
	 * @return mixed
	 */
	private function switch_user_to_article_v2( $kb_config ) {

		if ( EPKB_Utilities::post('action') != 'epkb_update_article_v2' ) {
			return $kb_config;
		}

		// convert article structure to version 2
		$result = epkb_get_instance()->kb_config_obj->set_value( $kb_config['id'], 'article-structure-version', 'version-2' );
		if ( is_wp_error( $result ) ) {
			$this->message['error'] = __( 'Something went wrong', 'echo-knowledge-base' ) . ' (64)';
			return $kb_config;
		}

		if ( $kb_config['article_toc_enable'] == 'on' ) {

			if ( $kb_config['article_toc_position'] == 'left' ) {
				$kb_config['article_sidebar_component_priority']['toc_left'] = 1;
			   $kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'right' ) {
				$kb_config['article_sidebar_component_priority']['toc_right'] = 1;
			   $kb_config['article-right-sidebar-toggle'] = 'on';
			} else if ( $kb_config['article_toc_position'] == 'middle' ) {
				$kb_config['article_sidebar_component_priority']['toc_content'] = 1;
				$kb_config['article-right-sidebar-toggle'] = 'on';
			}
		}

		$kb_config['article-structure-version'] = 'version-2';

		$new_config = EPKB_Editor_Controller::reset_layout( $kb_config, $kb_config );
		$result = epkb_get_instance()->kb_config_obj->update_kb_configuration( $new_config['id'], $new_config );
		if ( is_wp_error( $result ) ) {

		   /* @var $result WP_Error */
		   $message = $result->get_error_message();
		   if ( empty($message) ) {
		     $this->message['error'] = __( 'Could not save the new configuration', 'echo-knowledge-base' ) . '(3)';
		   } else {
		     $this->message['error'] = __( 'Configuration NOT saved due to following problem:' . $message, 'echo-knowledge-base' );
		   }

		} else {

	      $this->message['success'] = "Article version was updated.";  // temporary no need to translate
		}

		return $kb_config;
	}
}
