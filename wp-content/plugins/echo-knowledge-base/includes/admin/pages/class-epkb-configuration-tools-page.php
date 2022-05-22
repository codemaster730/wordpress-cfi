<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helper class for Display KB configuration menu and pages (Tools Tab)
 *
 * @copyright   Copyright (C) 2021, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Configuration_Tools_Page {

	/**
	 * Get Tools View Config
	 *
	 * @param $kb_config
	 * @return array
	 */

	public static function get_tools_view_config( $kb_config ) {
		return array(

			// Shared
			'list_key'   => 'tools',

			// Top Panel Item
			'label_text' => __( 'Tools', 'echo-knowledge-base' ),
			'icon_class' => 'epkbfa epkbfa-wrench',

			// Secondary Panel Items
			'secondary'  => array(

				// SECONDARY VIEW: EXPORT
				array(

					// Shared
					'list_key'   => 'export',
					'active'     => true,

					// Secondary Panel Item
					'label_text' => __( 'Export KB', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => self::get_export_boxes( $kb_config )
				),

				// SECONDARY VIEW: IMPORT
				array(

					// Shared
					'list_key'   => 'import',

					// Secondary Panel Item
					'label_text' => __( 'Import KB', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => self::get_import_boxes( $kb_config )
				),

				// THIRD VIEW: CONVERT
				array(
					// Shared
					'list_key'   => 'convert',

					// Secondary Panel Item
					'label_text' => __( 'Convert Posts to Articles', 'echo-knowledge-base' ),

					// Secondary Boxes List
					'boxes_list' => self::get_convert_boxes( $kb_config )
				),
			),
		);
	}

	/**
	 * Get Import Box
	 *
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_import_box( $kb_config ) {

		// reset cache and get latest KB config
		epkb_get_instance()->kb_config_obj->reset_cache();

		ob_start(); ?>

		<!-- Import Config -->
		<div class="epkb-admin-info-box">
			<div class="epkb-admin-info-box__body">
				<p><?php echo __( 'This import will overwrite the following KB settings:', 'echo-knowledge-base' ); ?></p>
				<?php self::display_import_export_info(); ?>
				<form class="epkb-import-kbs"
					  action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_config['id'], 'active_action_tab' => 'import' ) ) . '#tools__import' ); ?>"
					  method="post" enctype="multipart/form-data">
					<input type="hidden" name="_wpnonce_manage_kbs"
						   value="<?php echo wp_create_nonce( "_wpnonce_manage_kbs" ); ?>"/>
					<input type="hidden" name="action" value="epkb_import_knowledge_base"/>
					<input type="hidden" name="emkb_kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>"/>
					<input class="epkb-form-label__input epkb-form-label__input--text" type="file" name="import_file"
						   required><br>
					<input type="button" class="epkb-kbnh-back-btn epkb-default-btn"
						   value="<?php esc_attr_e( 'Back', 'echo-knowledge-base' ); ?>"/>
					<input type="submit" class="epkb-primary-btn"
						   value="<?php esc_attr_e( 'Import Configuration', 'echo-knowledge-base' ); ?>"/><br/>
				</form>
			</div>
		</div>  <?php

		return ob_get_clean();
	}

	private static function display_import_export_info() { ?>
		<ul>
			<li><?php _e( 'Configuration for all text, styles, features.', 'echo-knowledge-base' ); ?></li>
			<li><?php _e( 'Configuration for all add-ons.', 'echo-knowledge-base' ); ?></li>
		</ul>
		<p><?php _e( 'Instructions:', 'echo-knowledge-base' ); ?></p>
		<ul>
			<li><?php _e( 'Test import and export on your staging or test site before importing configuration in production.', 'echo-knowledge-base' ); ?></li>
			<li><?php _e( 'Always back up your database before starting the import.', 'echo-knowledge-base' ); ?></li>
			<li><?php _e( 'Preferably run import outside of business hours.', 'echo-knowledge-base' ); ?></li>
		</ul> <?php
	}

	/**
	 * Get boxes for Tools panel, export subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_export_boxes( $kb_config ) {
		$boxes = [];

		foreach ( self::get_export_boxes_config( $kb_config ) as $box ) {

			if ( $box['plugin'] == 'epie' ) {
				if ( EPKB_Utilities::is_export_import_enabled() ) {
					$box['active_status'] = true;
				} else {
					$box['upgrade_link'] = EPKB_Core_Utilities::get_plugin_sales_page( $box['plugin'] );
				}
			} else {
				$box['active_status'] = true;
			}

			// box with the button
			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box )
			];
		}

		foreach ( self::get_export_boxes_config( $kb_config ) as $box ) {
			// panel that will be opened with the button
			$box_panel_class = 'epkb-kbnh__feature-panel-container ' . ( empty( $box['button_id'] ) ? '' : 'epkb-kbnh__feature-panel-container--' . $box['button_id'] );

			$boxes[] = [
				'title' => $box['title'],
				'class' => $box_panel_class,
				'html'  => apply_filters( 'epkb_config_page_export_import_panel_html', '', $kb_config, $box ),
			];
		}

		return $boxes;
	}

	/**
	 * Get boxes config for Tools panel, export subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_export_boxes_config( $kb_config ) {

		return [
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-upload',
				'title'        => esc_html__( 'Export KB Configuration', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Export core and add-ons configuration including colors, fonts, labels, and features settings.', 'echo-knowledge-base' ),
				'custom_links' => self::get_export_button_html( $kb_config ),
				'button_id'    => 'epkb_core_export',
				'button_title' => esc_html__( 'Export Configuration', 'echo-knowledge-base' ),
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-upload',
				'title'        => esc_html__( 'Export Articles - Basic Data (CSV)', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name--pro',
				'desc'         => esc_html__( 'Export basic article information: title, content, categories, and tags.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_export_data_csv' : '',
				'button_title' => esc_html__( 'Run Export', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/export-articles-as-csv/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-upload',
				'title'        => esc_html__( 'Export Articles - All Data (XML)', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name--pro',
				'desc'         => esc_html__( 'Export articles, including content, comments, authors, categories, meta data, and references to attachments.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_export_data_xml' : '',
				'button_title' => esc_html__( 'Run Export', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/export-articles-as-xml/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
		];
	}

	/**
	 * Get hidden block to make export working
	 *
	 * @param $kb_config
	 * @return string
	 */
	private static function get_export_button_html( $kb_config ) {

		ob_start(); ?>

	<form class="epkb-export-kbs"
		  action="<?php echo esc_url( add_query_arg( array( 'active_kb_tab' => $kb_config['id'], 'active_action_tab' => 'export#tools__export' ) ) ); ?>"
		  method="post">
		<input type="hidden" name="_wpnonce_manage_kbs"
			   value="<?php echo esc_attr( wp_create_nonce( "_wpnonce_manage_kbs" ) ); ?>"/>
		<input type="hidden" name="action" value="epkb_export_knowledge_base"/>
		<input type="hidden" name="emkb_kb_id" value="<?php echo esc_attr( $kb_config['id'] ); ?>"/>
		<input type="submit" class="epkb-primary-btn"
			   value="<?php esc_html_e( 'Export Configuration', 'echo-knowledge-base' ); ?>"/>
		</form><?php

		return ob_get_clean();
	}

	/**
	 * Get boxes for Tools panel, import subpanel
	 *
	 * @param $kb_config
	 * @return array
	 */
	private static function get_import_boxes( $kb_config ) {
		$boxes = [];

		foreach ( self::get_import_boxes_config() as $box ) {

			if ( $box['plugin'] == 'epie' ) {
				if ( EPKB_Utilities::is_export_import_enabled() ) {
					$box['active_status'] = true;
				} else {
					$box['upgrade_link'] = EPKB_Core_Utilities::get_plugin_sales_page( $box['plugin'] );
				}
			} else {
				$box['active_status'] = true;
			}

			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box )
			];
		}

		foreach ( self::get_import_boxes_config() as $box ) {
			// panel that will be opened with the button
			$box_panel_class = 'epkb-kbnh__feature-panel-container ' . ( empty( $box['button_id'] ) ? '' : 'epkb-kbnh__feature-panel-container--' . $box['button_id'] );

			$panel_html = '';

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_core_import' ) {
				$panel_html = self::get_import_box( $kb_config );
			}

			$boxes[] = [
				'title' => $box['title'],
				'class' => $box_panel_class,
				'html'  => apply_filters( 'epkb_config_page_export_import_panel_html', $panel_html, $kb_config, $box ),
			];
		}

		return $boxes;
	}

	/**
	 * Get config for boxes for Tools panel, import subpanel
	 * @return array
	 */
	private static function get_import_boxes_config() {

		return [
			[
				'plugin'       => 'core',
				'icon'         => 'epkbfa epkbfa-download',
				'title'        => esc_html__( 'Import KB Configuration', 'echo-knowledge-base' ),
				'desc'         => esc_html__( 'Import core and add-ons configuration including colors, fonts, labels, and features settings.', 'echo-knowledge-base' ),
				'button_id'    => 'epkb_core_import',
				'button_title' => esc_html__( 'Import Configuration', 'echo-knowledge-base' ),
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-download',
				'title'        => esc_html__( 'Import Articles - Basic Data (CSV)', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name--pro',
				'desc'         => esc_html__( 'Import basic article information: title, content, categories and tags.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_import_data_csv' : '',
				'button_title' => esc_html__( 'Run Import', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/how-to-import-csv-file/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
			[
				'plugin'       => 'epie',
				'icon'         => 'epkbfa epkbfa-download',
				'title'        => esc_html__( 'Import Articles - All Data (XML)', 'echo-knowledge-base' ),
				'title_class'  => 'epkb-kbnh__feature-name--pro',
				'desc'         => esc_html__( 'Import articles including content, comments, authors, categories, meta data, attachments.', 'echo-knowledge-base' ),
				'button_id'    => EPKB_Utilities::is_export_import_enabled() ? 'epie_import_data_xml' : '',
				'button_title' => esc_html__( 'Run Import', 'echo-knowledge-base' ),
				'docs'         => 'https://www.echoknowledgebase.com/documentation/how-to-import-xml-file/',
				'learn_more'   => EPKB_Utilities::is_export_import_enabled() ? '' : 'https://www.echoknowledgebase.com/wordpress-plugin/kb-articles-import-export/'
			],
		];
	}

	/**
	 * Get boxes for Tools panel, convert subpanel
	 * @param $kb_config
	 * @return array
	 */
	private static function get_convert_boxes( $kb_config ) {
		$boxes = [];

		foreach ( self::get_convert_boxes_config() as $box ) {
			$boxes[] = [
				'class' => 'epkb-kbnh__feature-container',
				'html'  => EPKB_HTML_Forms::get_feature_box_html( $box )
			];
		}

		foreach ( self::get_convert_boxes_config() as $box ) {
			// panel that will be opened with the button
			$box_panel_class = 'epkb-kbnh__feature-panel-container ' . ( empty( $box['button_id'] ) ? '' : 'epkb-kbnh__feature-panel-container--' . $box['button_id'] );

			$panel_html = '';

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_core_import' ) {
				$panel_html = self::get_import_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_convert_posts' ) {
				$panel_html = self::get_convert_posts_box( $kb_config );
			}

			if ( ! empty( $box['button_id'] ) && $box['button_id'] == 'epkb_convert_cpt' ) {
				$panel_html = self::get_convert_cpt_box( $kb_config );
			}

			$boxes[] = [
				'title' => $box['title'],
				'class' => $box_panel_class,
				'html'  => apply_filters( 'epkb_config_page_export_import_panel_html', $panel_html, $kb_config, $box ),
			];
		}

		return $boxes;
	}

	/**
	 * Get config for boxes for Tools panel, convert subpanel
	 * @return array
	 */
	private static function get_convert_boxes_config() {

		return [
			[
				'plugin'        => 'core',
				'icon'          => 'epkbfa epkbfa-map-signs',
				'title'         => esc_html__( 'Convert Posts to KB Articles', 'echo-knowledge-base' ),
				'desc'          => esc_html__( 'Convert your blog or regular posts into Knowledge Base articles.', 'echo-knowledge-base' ),
				'button_id'     => 'epkb_convert_posts',
				'button_title'  => esc_html__( 'Convert Posts', 'echo-knowledge-base' ),
				'docs'          => 'https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/',
				'active_status' => true
			],
			[
				'plugin'        => 'core',
				'icon'          => 'epkbfa epkbfa-download',
				'title'         => esc_html__( 'Convert Custom Post Types to KB', 'echo-knowledge-base' ),
				'desc'          => esc_html__( 'Convert your blog or custom post types into Knowledge Base articles.', 'echo-knowledge-base' ),
				'button_id'     => 'epkb_convert_cpt',
				'button_title'  => esc_html__( 'Convert CPTs', 'echo-knowledge-base' ),
				'active_status' => true
			]
		];
	}

	/**
	 * Convert Posts to Articles.
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_convert_posts_box( $kb_config ) {
		ob_start(); ?>
		<div class="epkb-form-wrap epkb-import-form epkb-convert-form epkb-convert-form--posts"><?php
		self::show_convert_header_html(); ?>
		<div class="epkb-import-body">
		<div class="epkb-import-step epkb-import-step--1"><?php
			self::show_convert_posts_step_1( $kb_config ); ?>
		</div>
		<div class="epkb-import-step epkb-import-step--2 epkb-hidden"><?php
			self::show_convert_posts_step_2(); ?>
		</div>
		<div class="epkb-import-step epkb-import-step--3 epkb-hidden"><?php
			self::show_convert_posts_step_3(); ?>
		</div>
		<div class="epkb-import-step epkb-import-step--4 epkb-hidden"><?php
			self::show_convert_posts_step_4(); ?>
		</div>
		</div><?php
		self::show_convert_footer_html( $kb_config ); ?>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * HTML for convert header
	 *
	 * @param string $type
	 */
	private static function show_convert_header_html( $type = 'post' ) {

		$step_4_title = $type == 'cpt' ? __( 'Convert CPT', 'echo-knowledge-base' ) : __( 'Convert Posts', 'echo-knowledge-base' );		?>

		<div class="epkb-import-header">
			<div class="epkb-import-step-label epkb-import-step--1 epkb-import-step--done" data-step="1">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php esc_html_e( 'Begin', 'echo-knowledge-base' ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--2" data-step="2">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php esc_html_e( 'Select Articles', 'echo-knowledge-base' ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--3" data-step="3">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php esc_html_e( 'Choose Options', 'echo-knowledge-base' ); ?></span>
			</div>
			<div class="epkb-import-step-label epkb-import-step--4 " data-step="4">
				<i class="epkbfa epkbfa-check"></i>
				<span><?php echo esc_html( $step_4_title ); ?></span>
			</div>
		</div><?php

		self::maybe_show_wp_version_warning();
	}

	/**
	 * Show user warning if wordpress version less than 5.6
	 */
	private static function maybe_show_wp_version_warning() {
		global $wp_version;

		if ( version_compare( $wp_version, '5.6', '>=' ) ) {
			return;
		}

		EPKB_HTML_Forms::notification_box_middle( [
			'title'  => __( 'Old version of WordPress detected', 'echo-knowledge-base' ),
			'type'   => 'error',
			'static' => true,
			'desc'   => __( 'This website is using an old version of WordPress. Unpredictable behaviour and errors during conversion can occur for this old WordPress version. Please update to the latest version of WordPress. ' .
							'Support is very limited for old versions of WordPress.', 'echo-knowledge-base' ),
		] );
	}

	/**
	 * HTML for convert post step
	 *
	 * @param $kb_config
	 */
	private static function show_convert_posts_step_1( $kb_config ) { ?>
		<form class="convert-main-form">
		<div class="epkb-form-field-instruction-wrap">
			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Convert Posts', 'echo-kb-import-export' ); ?>
					</div>
				</div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Copy or Move Categories', 'echo-kb-import-export' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-close"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Categories hierarchy', 'echo-kb-import-export' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instructions">
				<p><?php esc_html_e( 'Instructions:', 'echo-knowledge-base' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'Test conversion on your staging or test site before converting posts in production.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Always back up your database before starting the conversion.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Ensure that you are converting posts into the correct KB.', 'echo-knowledge-base' ); ?></li>
				</ul>
				<p><a href="https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/"
					  class="epkb-form-field-instructions__link" target="_blank"><?php esc_html_e( 'Read complete instructions here', 'echo-knowledge-base' ); ?></a>
				</p>
			</div>

		</div>
		<input type="hidden" name="epkb_convert_post_type" value="post"><?php

		if ( EPKB_Utilities::is_multiple_kbs_enabled() ) { ?>
			<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-kb-name-checkbox"
				   type="checkbox" name="epkb_convert_post" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I want to convert articles into this KB:', 'echo-kb-import-export' ); ?>
                <span class="epkb-admin__distinct-box epkb-admin__distinct-box--middle"><?php echo esc_html( $kb_config['kb_name'] ); ?></span></span>
			</label><?php
		} ?>

		<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-backup-checkbox"
				   type="checkbox" name="epkb_convert_backup" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-kb-import-export' ); ?></span>
		</label>
		</form><?php
	}

	/**
	 * HTML for convert step
	 */
	private static function show_convert_posts_step_2() {
		self::progress_bar_html( __( 'Reading posts', 'echo-knowledge-base' ) );
	}

	/**
	 * HTML for import step
	 */
	private static function show_convert_posts_step_3() {
		// Will be filled with AJAX
	}

	/**
	 * HTML for import step
	 */
	private static function show_convert_posts_step_4() {
		self::progress_bar_html( __( 'Convert Progress', 'echo-knowledge-base' ) ); ?>

		<div class="epkb-import-error-messages epkb-hidden"><?php
		$title = esc_html__( 'Errors during convert', 'echo-knowledge-base' );
		$description = '';
		$table_header = [
			esc_html__( 'Article Title', 'echo-knowledge-base' ),
			esc_html__( 'File Link', 'echo-knowledge-base' ),
			' ',
			' '
		];

		echo EPKB_Convert::display_import_table( $title, $description, $table_header, [], 'error', '' ); ?>
		</div><?php
	}

	/**
	 * HTML for convert footer
	 * @param $kb_config
	 */
	private static function show_convert_footer_html( $kb_config ) { ?>
		<div class="epkb-import-footer">
		<button type="button" class="epkb-default-btn epkb-convert-button-back">
			<?php esc_html_e( '< Back', 'echo-knowledge-base' ); ?>
		</button>
		<button type="button" class="epkb-default-btn epkb-hidden epkb-convert-button-exit">
			<?php esc_html_e( 'Exit', 'echo-knowledge-base' ); ?>
		</button>
		<button type="button" class="epkb-error-btn epkb-hidden epkb-convert-button-cancel">
			<?php esc_html_e( 'Stop', 'echo-knowledge-base' ); ?>
		</button>
		<button type="button" class="epkb-primary-btn epkb-convert-button-next"
				data-kb_id="<?php echo esc_attr( $kb_config['id'] ); ?>">
			<?php esc_html_e( 'Next Step >', 'echo-knowledge-base' ); ?>
		</button>
		<button type="button" class="epkb-primary-btn epkb-hidden epkb-convert-button-start_convert"
				data-kb_id="<?php echo esc_attr( $kb_config['id'] ); ?>">
			<?php esc_html_e( 'Start Converting', 'echo-knowledge-base' ); ?>
		</button>
		</div><?php
	}

	/**
	 * HTML for progress bar. Working with admin-ui.js bar function
	 * @param $title
	 */
	public static function progress_bar_html( $title ) { ?>
		<div class="epkb-progress">
			<h3><?php echo esc_html( $title ); ?> <span class="epkb-progress__percentage"></span></h3>
			<div class="epkb-progress__bar ">
				<div style="width:0;"></div>
			</div>
			<div class="epkb-progress__log"></div>
		</div>
		<div class="epkb-data-status-log"></div><?php
	}

	/**
	 * @param $kb_config
	 * @return false|string
	 */
	private static function get_convert_cpt_box( $kb_config ) {
		ob_start(); ?>
		<div class="epkb-form-wrap epkb-import-form epkb-convert-form epkb-convert-form--posts"><?php
		self::show_convert_header_html( 'cpt' ); ?>
		<div class="epkb-import-body">
		<div class="epkb-import-step epkb-import-step--1"><?php
			self::show_convert_cpt_step_1( $kb_config ); ?>
		</div>
		<div class="epkb-import-step epkb-import-step--2 epkb-hidden"><?php
			self::show_convert_posts_step_2(); ?>
		</div>
		<div class="epkb-import-step epkb-import-step--3 epkb-hidden"><?php
			self::show_convert_posts_step_3(); ?>
		</div>
		<div class="epkb-import-step epkb-import-step--4 epkb-hidden"><?php
			self::show_convert_posts_step_4(); ?>
		</div>
		</div><?php
		self::show_convert_footer_html( $kb_config ); ?>
		</div><?php

		return ob_get_clean();
	}

	/**
	 * HTML for convert post step
	 *
	 * @param $kb_config
	 */
	private static function show_convert_cpt_step_1( $kb_config ) {
		$custom_post_types = self::get_eligible_cpts(); ?>
		<form class="convert-main-form">
		<div class="epkb-form-field-instruction-wrap">
			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Features', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Convert CPT', 'echo-kb-import-export' ); ?>
					</div>
				</div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-check"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Copy or Move Categories', 'echo-kb-import-export' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instruction-column">
				<div class="epkb-form-field-instruction-title"><?php esc_html_e( 'Not Supported', 'echo-kb-import-export' ); ?></div>
				<div class="epkb-form-field-instruction-item">
					<div class="epkb-form-field-instruction-icon">
						<i class="epkbfa epkbfa-close"></i>
					</div>
					<div class="epkb-form-field-instruction-text">
						<?php esc_html_e( 'Categories hierarchy', 'echo-kb-import-export' ); ?>
					</div>
				</div>
			</div>

			<div class="epkb-form-field-instructions">
				<p><?php esc_html_e( 'Instructions:', 'echo-knowledge-base' ); ?></p>
				<ul>
					<li><?php esc_html_e( 'Test conversion on your staging or test site before converting posts in production.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Always back up your database before starting the conversion.', 'echo-knowledge-base' ); ?></li>
					<li><?php esc_html_e( 'Ensure that you are converting posts into the correct KB.', 'echo-knowledge-base' ); ?></li>
				</ul>
				<p><a href="https://www.echoknowledgebase.com/documentation/convert-posts-cpts-to-articles/"
					  class="epkb-form-field-instructions__link"><?php esc_html_e( 'Read complete instructions here', 'echo-knowledge-base' ); ?></a>
				</p>
			</div>

		</div>

		<label class="epkb-form-label">
			<span class="epkb-form-label__select"><?php esc_html_e( 'Convert CPT:', 'echo-kb-import-export' ); ?></span>
			<select name="epkb_convert_post_type">
				<option value="" selected><?php esc_html_e( 'Select Post Type', 'echo-kb-import-export' ); ?></option><?php
				foreach ( $custom_post_types as $post_type => $post_label ) { ?>
					<option value="<?php echo esc_attr( $post_type ); ?>"><?php echo esc_html( $post_label ); ?></option><?php
				} ?>
			</select>
		</label><?php

		if ( EPKB_Utilities::is_multiple_kbs_enabled() ) { ?>
			<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-kb-name-checkbox"
				   type="checkbox" name="epkb_convert_post" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I want to convert articles into this KB:', 'echo-kb-import-export' ); ?> <strong
						class="epkb-admin__distinct-box epkb-admin__distinct-box--middle"><?php echo esc_html( $kb_config['kb_name'] ); ?></strong></span>
			</label><?php
		} ?>

		<label class="epkb-form-label">
			<input class="epkb-form-label__input epkb-form-label__input--checkbox import-backup-checkbox"
				   type="checkbox" name="epkb_convert_backup" required>
			<span class="epkb-form-label__checkbox"><?php esc_html_e( 'I have backed up my database and read all import instructions above.', 'echo-kb-import-export' ); ?></span>
		</label>
		</form><?php
	}

	/**
	 * Return array of slug => name pairs eligible for CPT converting
	 */
	private static function get_eligible_cpts() {

		$disallowed_post_types = [ 'page', 'post', 'attachment', 'elementor_library' ];

		$cpts = EPKB_Utilities::get_post_type_labels( $disallowed_post_types, [], true );

		// for epie
		return apply_filters( 'epkb_convert_post_types', $cpts );
	}
}
