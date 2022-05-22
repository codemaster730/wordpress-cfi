<?php

/**
 * HTML Elements for admin pages excluding boxes
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_HTML_Admin {

	/**
	 * Show Admin Header
	 *
	 * @param $kb_config
	 * @param $permissions
	 * @param string $content_type
	 * @param string $position
	 */
	public static function admin_header( $kb_config, $permissions, $content_type='header', $position = '' ) {  ?>

		<!-- Admin Header -->
		<div class="epkb-admin__header">
			<div class="epkb-admin__section-wrap <?php echo empty( $position ) ? '' : 'epkb-admin__section-wrap--' . esc_attr( $position ); ?> epkb-admin__section-wrap__header">   <?php

				switch ( $content_type ) {
					case 'header':
					default:
						echo self::admin_header_content( $kb_config, $permissions ) ;
						break;
					case 'logo':
						echo self::get_admin_header_logo();
						break;
				}  ?>

			</div>
		</div>  <?php
	}

	/**
	 * Content for Admin Header - KB Logo, List of KBs
	 *
	 * @param $kb_config
	 * @param array $contexts
	 * @return string
	 */
	public static function admin_header_content( $kb_config, $contexts=[] ) {

		ob_start();

		$link_output = EPKB_Core_Utilities::get_current_kb_main_page_link( $kb_config, __( 'View KB', 'echo-knowledge-base' ), 'epkb-admin__header__view-kb__link' );
		if ( empty( $link_output ) && EPKB_Admin_UI_Access::is_user_access_to_context_allowed( 'admin_eckb_access_frontend_editor_write' ) ) {
			$link_output = '<a href="' . admin_url( '/edit.php?post_type=' . EPKB_KB_Handler::get_post_type( $kb_config['id'] ) . '&page=epkb-kb-configuration&setup-wizard-on' ) .
			               '" class="epkb-admin__header__view-kb__link" target="_blank">' . __( "Setup KB", "echo-knowledge-base" ) . '</a>';
		}

		echo self::get_admin_header_logo();    ?>

		<div class="epkb-admin__header__controls-wrap">

			<!-- KBs List -->
			<p class="epkb-admin__header__label"><?php esc_html__( 'Select KB', 'echo-knowledge-base' ); ?></p>
			<div class="epkb-admin__header__dropdown">      <?php
				EPKB_Core_Utilities::admin_list_of_kbs( $kb_config, $contexts ); 			?>
			</div>

			<!-- Link to KB View -->
			<div class="epkb-admin__header__view-kb">
				<?php echo $link_output; ?>
			</div>  <?php    ?>
		</div>      <?php

		$result = ob_get_clean();

		return empty( $result ) ? '' : $result;
	}

	/**
	 * Fill missing fields in single admin page view configuration array with default values
	 *
	 * @param $page_view
	 * @return array
	 */
	private static function admin_page_view_fill_missing_with_default( $page_view ){

		// Do not fill empty or not valid array
		if ( empty( $page_view ) || ! is_array( $page_view ) ) {
			return $page_view;
		}

		// Default page view
		$default_page_view = array(

			// Shared
			'active'                    => false,
			'minimum_required_capability' => EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
			'list_id'                   => '',
			'list_key'                  => '',
			'kb_config_id'				=> '',

			// Top Panel Item
			'label_text'                => '',
			'main_class'                => '',
			'label_class'               => '',
			'icon_class'                => '',

			// Secondary Panel Items
			'secondary'                 => array(),

			// Boxes List
			'list_top_actions_html'     => '',
			'top_actions_minimum_required_capability' => EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
			'list_bottom_actions_html'  => '',
			'bottom_actions_minimum_required_capability' => EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
			'boxes_list'                => array(),

			// List footer HTML
			'list_footer_html'          => '',
		);

		// Default secondary view
		$default_secondary = array(

			// Shared
			'list_key'                  => '',
			'active'                    => false,
			'minimum_required_capability' => EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,

			// Secondary Panel Item
			'label_text'                => '',
			'main_class'                => '',
			'label_class'               => '',
			'icon_class'                => '',

			// Secondary Boxes List
			'list_top_actions_html'     => '',
			'top_actions_minimum_required_capability' => EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
			'list_bottom_actions_html'  => '',
			'bottom_actions_minimum_required_capability' => EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
			'boxes_list'                => array(),
		);

		// Default box
		$default_box = array(
			'minimum_required_capability' => EPKB_Admin_UI_Access::EPKB_ADMIN_CAPABILITY,
			'icon_class'    => '',
			'class'         => '',
			'title'         => '',
			'description'   => '',
			'html'          => '',
			'return_html'   => false,
			'extra_tags'    => [],
		);

		// Set default view
		$page_view = array_merge( $default_page_view, $page_view );

		// Set default boxes
		foreach ( $page_view['boxes_list'] as $box_index => $box_content ) {

			// Do not fill empty or not valid array
			if ( empty( $page_view['boxes_list'][$box_index] ) || ! is_array( $page_view['boxes_list'][$box_index] ) ) {
				continue;
			}

			$page_view['boxes_list'][$box_index] = array_merge( $default_box, $box_content );
		}

		// Set default secondary views
		foreach ( $page_view['secondary'] as $secondary_index => $secondary_content ) {

			// Do not fill empty or not valid array
			if ( empty( $page_view['secondary'][$secondary_index] ) || ! is_array( $page_view['secondary'][$secondary_index] ) ) {
				continue;
			}

			$page_view['secondary'][$secondary_index] = array_merge( $default_secondary, $secondary_content );

			// Set default boxes
			foreach ( $page_view['secondary'][$secondary_index]['boxes_list'] as $box_index => $box_content ) {

				// Do not fill empty or not valid array
				if ( empty(  $page_view['secondary'][$secondary_index]['boxes_list'][$box_index] ) || ! is_array(  $page_view['secondary'][$secondary_index]['boxes_list'][$box_index] ) ) {
					continue;
				}

				$page_view['secondary'][$secondary_index]['boxes_list'][$box_index] = array_merge( $default_box, $box_content );
			}
		}

		return $page_view;
	}

	/**
	 * Show Admin Toolbar
	 *
	 * @param $admin_page_views
	 */
	public static function admin_toolbar( $admin_page_views ) {     ?>

		<!-- Admin Top Panel -->
		<div class="epkb-admin__top-panel">
			<div class="epkb-admin__section-wrap epkb-admin__section-wrap__top-panel">      <?php

				foreach( $admin_page_views as $page_view ) {

					// Optionally we can have null in $page_view, make sure we handle it correctly
					if ( empty( $page_view ) || ! is_array( $page_view ) ) {
						continue;
					}

					// Fill missing fields in admin page view configuration array with default values
					$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

					// Do not render toolbar tab if the user does not have permission
					if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
						continue;
					}   ?>

					<div class="epkb-admin__top-panel__item epkb-admin__top-panel__item--<?php echo esc_attr( $page_view['list_key'] );
						echo empty( $page_view['secondary'] ) ? '' : ' epkb-admin__top-panel__item--parent ';
						echo esc_attr( $page_view['main_class'] ); ?>"
					    <?php echo empty( $page_view['list_id'] ) ? '' : ' id="' . esc_attr( $page_view['list_id'] ) . '"'; ?> data-target="<?php echo esc_attr( $page_view['list_key'] ); ?>">
						<div class="epkb-admin__top-panel__icon epkb-admin__top-panel__icon--<?php echo esc_attr( $page_view['list_key'] ); ?> <?php echo esc_attr( $page_view['icon_class'] ); ?>"></div>
						<p class="epkb-admin__top-panel__label epkb-admin__boxes-list__label--<?php echo esc_attr( $page_view['list_key'] ); ?>"><?php echo wp_kses_post( $page_view['label_text'] ); ?></p>
					</div> <?php
				}       ?>

			</div>
		</div>  <?php
	}

	/**
	 * Display admin second-level tabs below toolbar
	 *
	 * @param $admin_page_views
	 */
	public static function admin_secondary_tabs( $admin_page_views ) {  ?>

		<!-- Admin Secondary Panels List -->
		<div class="epkb-admin__secondary-panels-list">
			<div class="epkb-admin__section-wrap epkb-admin__section-wrap__secondary-panel">  <?php

				foreach ( $admin_page_views as $page_view ) {

					// Optionally we can have null in $page_view, make sure we handle it correctly
					if ( empty( $page_view ) || ! is_array( $page_view ) ) {
						continue;
					}

					// Optionally we can have empty in $page_view['secondary'], make sure we handle it correctly
					if ( empty( $page_view['secondary'] ) || ! is_array( $page_view['secondary'] ) ) {
						continue;
					}

					// Fill missing fields in admin page view configuration array with default values
					$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

					// Do not render toolbar tab if the user does not have permission
					if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
						continue;
					}   ?>

					<!-- Admin Secondary Panel -->
					<div id="epkb-admin__secondary-panel__<?php echo esc_attr( $page_view['list_key'] ); ?>" class="epkb-admin__secondary-panel">  <?php

						foreach ( $page_view['secondary'] as $secondary ) {

							// Optionally we can have empty in $secondary, make sure we handle it correctly
							if ( empty( $secondary ) || ! is_array( $secondary ) ) {
								continue;
							}

							// Do not render secondary toolbar tab if the user does not have permission
							if ( ! current_user_can( $secondary['minimum_required_capability'] ) ) {
								continue;
							}       ?>

							<div class="epkb-admin__secondary-panel__item epkb-admin__secondary-panel__<?php echo esc_attr( $secondary['list_key'] ); ?> <?php
								echo ( $secondary['active'] ? 'epkb-admin__secondary-panel__item--active' : '' );
								echo esc_attr( $secondary['main_class'] ); ?>" data-target="<?php echo esc_attr( $page_view['list_key'] ) . '__' .esc_attr( $secondary['list_key'] ); ?>">     <?php

								// Optional icon for secondary panel item
								if ( ! empty( $secondary['icon_class'] ) ) {        ?>
									<span class="epkb-admin__secondary-panel__icon <?php echo esc_attr( $secondary['icon_class'] ); ?>"></span>     <?php
								}       ?>

								<p class="epkb-admin__secondary-panel__label epkb-admin__secondary-panel__<?php echo esc_attr( $secondary['list_key'] ); ?>__label"><?php echo wp_kses_post( $secondary['label_text'] ); ?></p>
							</div>  <?php

						}   ?>
					</div>  <?php

				}   ?>

			</div>
		</div>  <?php
	}

	/**
	 * Show list of settings for each setting in a tab
	 *
	 * @param $admin_page_views
	 * @param string $content_class
	 */
	public static function admin_settings_tab_content( $admin_page_views, $content_class='' ) {    ?>

		<!-- Admin Content -->
		<div class="epkb-admin__content <?php echo esc_attr( $content_class ); ?>"> <?php

			foreach ( $admin_page_views as $page_view ) {

				// Optionally we can have null in $page_view, make sure we handle it correctly
				if ( empty( $page_view ) || ! is_array( $page_view ) ) {
					continue;
				}

				// Fill missing fields in admin page view configuration array with default values
				$page_view = self::admin_page_view_fill_missing_with_default( $page_view );

				// Do not render view if the user does not have permission
				if ( ! current_user_can( $page_view['minimum_required_capability'] ) ) {
					continue;
				}   ?>

				<!-- Admin Boxes List -->
				<div id="epkb-admin__boxes-list__<?php echo esc_attr( $page_view['list_key'] ); ?>" class="epkb-admin__boxes-list">     <?php

					// List body
					self::admin_setting_boxes_for_tab( $page_view );

					// Optional list footer
					if ( ! empty( $page_view['list_footer_html'] ) ) {   ?>
							<div class="epkb-admin__section-wrap epkb-admin__section-wrap__<?php echo esc_attr( $page_view['list_key'] ); ?>">
								<div class="epkb-admin__boxes-list__footer"><?php echo wp_kses_post( $page_view['list_footer_html'] ); ?></div>
						</div>      <?php
					}   ?>

				</div><?php
			}   ?>

		</div><?php
	}

	/**
	 * Show single List of Settings Boxes for Admin Page
	 *
	 * @param $page_view
	 */
	private static function admin_setting_boxes_for_tab( $page_view ) {

		// Boxes List for view without secondary panel
		if ( empty( $page_view['secondary'] ) ) {

			// Make sure we can handle empty boxes list correctly
			if ( empty( $page_view['boxes_list'] ) || ! is_array( $page_view['boxes_list'] ) ) {
				return;
			}   ?>

			<!-- Admin Section Wrap -->
			<div class="epkb-admin__section-wrap epkb-admin__section-wrap__<?php echo esc_attr( $page_view['list_key'] ); ?>">  <?php

				self::admin_settings_display_boxes_list( $page_view );   ?>

			</div>      <?php

		// Boxes List for view with secondary tabs
		} else {

			// Secondary Lists of Boxes
			foreach ( $page_view['secondary'] as $secondary ) {

				// Make sure we can handle empty boxes list correctly
				if ( empty( $secondary['boxes_list'] ) || ! is_array( $secondary['boxes_list'] ) ) {
					continue;
				}   ?>

				<!-- Admin Section Wrap -->
				<div class="epkb-setting-box-container epkb-setting-box-container-type-<?php echo esc_attr( $page_view['list_key'] ); ?>">

					<!-- Secondary Boxes List -->
					<div id="epkb-setting-box__list-<?php echo esc_attr( $page_view['list_key'] ) . '__' . esc_attr( $secondary['list_key'] ); ?>"
					     class="epkb-setting-box__list <?php echo ( $secondary['active'] ? 'epkb-setting-box__list--active' : '' ); ?>">   <?php

						self::admin_settings_display_boxes_list( $secondary );   ?>

					</div>

				</div>  <?php
			}
		}
	}

	/**
	 * Display boxes list for admin settings
	 *
	 * @param $page_view
	 */
	private static function admin_settings_display_boxes_list( $page_view ) {

		// Optional buttons row displayed at the top of the boxes list
		if ( ! empty( $page_view['list_top_actions_html'] ) && current_user_can( $page_view['top_actions_minimum_required_capability'] ) ) {
			echo $page_view['list_top_actions_html'];
		}

		// Admin Boxes with configuration
		foreach ( $page_view['boxes_list'] as $box_options ) {

			// Do not render empty or not valid array
			if ( empty( $box_options ) || ! is_array( $box_options ) ) {
				continue;
			}

			// Do not render box if the user does not have permission
			if ( ! current_user_can( $box_options['minimum_required_capability'] ) ) {
				continue;
			}

			EPKB_HTML_Forms::admin_settings_box( $box_options );
		}

		// Optional buttons row displayed at the bottom of the boxes list
		if ( ! empty( $page_view['list_bottom_actions_html'] ) && current_user_can( $page_view['top_actions_minimum_required_capability'] )) {
			echo $page_view['list_bottom_actions_html'];
		}
	}

	/**
	 * Get logo container for the admin header
	 *
	 * @return string
	 */
	public static function get_admin_header_logo() {

		ob_start();     ?>

		<!-- Echo Logo -->
		<div class="epkb-admin__header__logo-wrap">
			<img class="epkb-admin__header__logo-mobile" alt="<?php esc_html_e( 'Echo Knowledge Base Logo', 'echo-knowledge-base' ); ?>" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png'; ?>">
			<img class="epkb-admin__header__logo-desktop" alt="<?php esc_html_e( 'Echo Knowledge Base Logo', 'echo-knowledge-base' ); ?>" src="<?php echo Echo_Knowledge_Base::$plugin_url . 'img/echo-kb-logo' . ( is_rtl() ? '-rtl' : '' ) . '.png'; ?>">
		</div>  <?php

		$result = ob_get_clean();

		return empty( $result ) ? '' : $result;
	}


	/********************************************************************************
	 *
	 *                                   VARIOUS
	 *
	 ********************************************************************************/

	/**
	 * We need to add this HTML to admin page to catch WP admin JS functionality
	 *
	 * @param false $include_no_css_message
	 * @param false $support_for_old_design
	 */
	public static function admin_page_css_missing_message( $include_no_css_message=false, $support_for_old_design=false ) {  ?>

		<!-- This is to catch WP JS garbage -->
		<div class="wrap epkb-wp-admin<?php echo ( $support_for_old_design ? ' epkb-admin-old-design-support' : '' ); ?>">
			<h1></h1>
		</div>
		<div class=""></div>  <?php

		if ( $include_no_css_message ) {    ?>
			<!-- This is for cases of CSS incorrect loading -->
			<h1 style="color: red; line-height: 1.2em; background-color: #eaeaea; border: solid 1px #ddd; padding: 20px;" class="epkb-css-working-hide-message">
				<?php _e( 'Please reload the page to refresh CSS styles. That should correctly render the page. This issue is typically caused by timeout or other plugins blocking CSS.' .
				          'If that does not help, contact us for help.', 'echo-knowledge-base' ); ?></h1>   <?php
		}
	}

	/**
	 * Display modal form in admin area for user to submit an error to support. For example Setup Wizard/Editor encounters error.
	 */
	public static function display_report_admin_error_form() {

		$current_user = wp_get_current_user();      ?>

		<!-- Submit Error Form -->
		<div class="epkb-admin__error-form__container" style="display:none!important;">
			<div class="epkb-admin__error-form__wrap">
				<div class="epkb-admin__scroll-container">
					<div class="epkb-admin__white-box">

						<h4 class="epkb-admin__error-form__title"></h4>
						<div class="epkb-admin__error-form__desc"></div>

						<form id="epkb-admin__error-form" method="post">				<?php

							EPKB_HTML_Admin::nonce();				?>

							<input type="hidden" name="action" value="epkb_report_admin_error" />
							<div class="epkb-admin__error-form__body">

								<label for="epkb-admin__error-form__first-name"><?php esc_html_e( 'Name', 'echo-knowledge-base' ); ?>*</label>
								<input name="first_name" type="text" value="<?php echo esc_attr( $current_user->display_name ); ?>" required  id="epkb-admin__error-form__first-name">

								<label for="epkb-admin__error-form__email"><?php esc_html_e( 'Email', 'echo-knowledge-base' ); ?>*</label>
								<input name="email" type="email" value="<?php echo esc_attr( $current_user->user_email ); ?>" required id="epkb-admin__error-form__email">

								<label for="epkb-admin__error-form__message"><?php esc_html_e( 'Error Details', 'echo-knowledge-base' ); ?>*</label>
								<textarea name="admin_error" class="admin_error" required id="epkb-admin__error-form__message"></textarea>

								<div class="epkb-admin__error-form__btn-wrap">
									<input type="submit" name="submit_error" value="<?php esc_attr_e( 'Submit', 'echo-knowledge-base' ); ?>" class="epkb-admin__error-form__btn epkb-admin__error-form__btn-submit">
									<span class="epkb-admin__error-form__btn epkb-admin__error-form__btn-cancel"><?php esc_html_e( 'Cancel', 'echo-knowledge-base' ); ?></span>
								</div>

								<div class="epkb-admin__error-form__response"></div>
							</div>
						</form>

						<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>

					</div>
				</div>
			</div>
		</div>      <?php
	}

	/**
	 * Display or return HTML input for wpnonce
	 *
	 * @param false $return_html
	 *
	 * @return false|string|void
	 */
	public static function nonce( $return_html=false ) {

		if ( $return_html ) {
			ob_start();
		}   ?>

		<input type="hidden" name="_wpnonce_epkb_ajax_action" value="<?php echo wp_create_nonce( '_wpnonce_epkb_ajax_action' ); ?>">	<?php

		if ( $return_html ) {
			return ob_get_clean();
		}
	}

	/**

	/**
     * Display a tooltip for admin form fields
     *
	 * @param $title
	 * @param $tooltip
	 * @param $args
	 *
	 */
	public static function display_tooltip( $title, $tooltip, $args = array() ) {
		if ( $tooltip == '' ) {
			return;
		}

		$defaults = array(
			'class'       => '',
	        'open-icon'   => 'info-circle',
	        'open-text'   => '',
            'link_text'   => __( 'Learn More', 'echo-knowledge-base' ),
            'link_url'    => '',
            'link_target' => '_blank'
        );
		$args = array_merge( $defaults, $args );  ?>
		<div class="epkb__option-tooltip <?php echo esc_attr( $args['class'] ); ?>">
			<span class="epkb__option-tooltip__button <?php echo $args['open-icon'] ? 'epkbfa epkbfa-' . esc_attr( $args['open-icon'] ) : ''; ?>">  <?php
				echo esc_html( $args['open-text'] );  ?>
            </span>
            <div class="epkb__option-tooltip__contents">    <?php
				if ( $title != '' ) {   ?>
                <div class="epkb__option-tooltip__header">
                    <?php echo esc_html( $title ); ?>
	                </div>  <?php
				}   ?>
                <div class="epkb__option-tooltip__body">
                    <?php echo wp_kses_post( $tooltip );    ?>
                </div>  <?php
				    if ( ! empty( $args['link_url'] ) ) { ?>
					<div class="epkb__option-tooltip__footer">
                        <a href="<?php echo esc_url( $args['link_url'] ); ?>" class="epkb__option-tooltip__button" target="<?php echo esc_attr( $args['link_target'] ); ?>">  <?php
					        echo esc_html( $args['link_text'] ); ?>
						</a>
					</div>  <?php
	            }  ?>
            </div>
		</div>  <?php
	}

	/**
	 *  Display a PRO Tag for settings and a Tool tip if user clicks on the settings.
	 *
	 * @param $title
	 */
    public static function display_pro_setting_tag( $title ) {

        if( EPKB_Utilities::is_help_dialog_pro_enabled() ) {
            return;
        }        ?>

        <span class="epkb__option-pro-tag">PRO</span>
        <div class="epkb__option-pro-tooltip">

            <div class="epkb__option-pro-tooltip__contents">
                <div class="epkb__option-pro-tooltip__header"><?php echo esc_html( $title ); ?></div>
                <div class="epkb__option-pro-tooltip__body">
                    You need to upgrade to the PRO version to use this feature.
                </div>
                <div class="epkb__option-pro-tooltip__footer">
                    <a class="epkb__option-pro-tooltip__button epkb-success-btn" href="https://www.echoknowledgebase.com/bundle-pricing/" target="_blank" rel="nofollow">Get PRO</a>
                </div>
            </div>
        </div>    <?php 
	}

	/**
	 * Generic admin page to display message on configuration error
	 */
	public static function display_config_error_page() {    ?>
		<div id="ekb-admin-page-wrap" class="ekb-admin-page-wrap--config-error">    <?php
			EPKB_HTML_Forms::notification_box_middle( [ 'type' => 'error', 'title' => __( 'Cannot load configuration.', 'echo-knowledge-base' ), 'desc' =>  EPKB_Utilities::contact_us_for_support() ] );  ?>
		</div>  <?php
	}
}
