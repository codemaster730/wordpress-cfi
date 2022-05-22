<?php  if ( ! defined( 'ABSPATH' ) ) exit;

spl_autoload_register( array('EPKB_Autoloader', 'autoload') );

/**
 * A class which contains the autoload function, that the spl_autoload_register
 * will use to autoload PHP classes.
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Autoloader {

	public static function autoload( $class ) {
		static $classes = null;

		if ( $classes === null ) {
			$classes = array(

				// CORE
				'epkb_utilities'                    =>  'includes/class-epkb-utilities.php',
				'epkb_core_utilities'               =>  'includes/class-epkb-core-utilities.php',
				'epkb_html_elements'                =>  'includes/class-epkb-html-elements.php',
				'epkb_html_admin'                   =>  'includes/class-epkb-html-admin.php',
				'epkb_html_forms'    				=>  'includes/class-epkb-html-forms.php',
				'epkb_icons'                        =>  'includes/class-epkb-icons.php',
				'epkb_input_filter'                 =>  'includes/class-epkb-input-filter.php',

				// SYSTEM
				'epkb_logging'                      =>  'includes/system/class-epkb-logging.php',
				'epkb_help_pointers'                =>  'includes/system/class-epkb-help-pointers.php',
				'epkb_templates'                    =>  'includes/system/class-epkb-templates.php',
				'epkb_upgrades'                     =>  'includes/system/class-epkb-upgrades.php',
				'epkb_wpml'                         =>  'includes/system/class-epkb-wpml.php',
				'epkb_file_manager'                 =>  'includes/system/class-epkb-file-manager.php',
				'epkb_delete_kb'                	=>  'includes/system/class-epkb-delete-kb.php',
				'epkb_deactivate_feedback'          =>  'includes/system/class-epkb-deactivate-feedback.php',
				'epkb_error_handler'                =>  'includes/system/class-epkb-error-handler.php',
				'epkb_typography'                   =>  'includes/system/class-epkb-typography.php',
				'epkb_db'                           =>  'includes/system/class-epkb-db.php',
				'epkb_admin_ui_access'              =>  'includes/system/class-epkb-admin-ui-access.php',

				// ADMIN CORE
				'epkb_admin_notices'                =>  'includes/admin/class-epkb-admin-notices.php',
				'epkb_site_builders'                =>  'includes/admin/class-epkb-site-builders.php',
				'epkb_settings_controller'          =>  'includes/admin/settings/class-epkb-settings-controller.php',
				'epkb_settings_specs'               =>  'includes/admin/settings/class-epkb-settings-specs.php',
				'epkb_settings_db'                  =>  'includes/admin/settings/class-epkb-settings-db.php',

				// ADMIN PAGES
				'epkb_configuration_page'           =>  'includes/admin/pages/class-epkb-configuration-page.php',
				'epkb_configuration_tools_page'     =>  'includes/admin/pages/class-epkb-configuration-tools-page.php',
				'epkb_need_help_page'               =>  'includes/admin/pages/class-epkb-need-help-page.php',
				'epkb_need_help_features'           =>  'includes/admin/pages/class-epkb-need-help-features.php',
				'epkb_need_help_contact_us'         =>  'includes/admin/pages/class-epkb-need-help-contact-us.php',
				'epkb_analytics_page'               =>  'includes/admin/pages/class-epkb-analytics-page.php',
				'epkb_add_ons_page'                 =>  'includes/admin/pages/class-epkb-add-ons-page.php',
				'epkb_add_ons_features'             =>  'includes/admin/pages/class-epkb-add-ons-features.php',

				// convert
				'epkb_convert'                      =>  'includes/admin/convert/class-epkb-convert.php',
				'epkb_convert_ctrl'                 =>  'includes/admin/convert/class-epkb-convert-ctrl.php',

				// KB Configuration
				'epkb_kb_config_specs'              =>  'includes/admin/kb-configuration/class-epkb-kb-config-specs.php',
				'epkb_kb_config_db'                 =>  'includes/admin/kb-configuration/class-epkb-kb-config-db.php',
				'epkb_kb_config_layouts'            =>  'includes/admin/kb-configuration/class-epkb-kb-config-layouts.php',
				'epkb_kb_config_layout_basic'       =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-basic.php',
				'epkb_kb_config_layout_tabs'        =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-tabs.php',
				'epkb_kb_config_layout_categories'  =>  'includes/admin/kb-configuration/class-epkb-kb-config-layout-categories.php',
				'epkb_kb_config_sequence'           =>  'includes/admin/kb-configuration/class-epkb-kb-config-sequence.php',
				'epkb_kb_config_category'           =>  'includes/admin/kb-configuration/class-epkb-kb-config-category.php',
				'epkb_configuration_controller'     =>  'includes/admin/kb-configuration/class-epkb-configuration-controller.php',
				'epkb_export_import'                =>  'includes/admin/kb-configuration/class-epkb-export-import.php',

				// WIZARDS
				'epkb_kb_wizard_setup'              =>  'includes/admin/wizard/class-epkb-kb-wizard-setup.php',
				'epkb_kb_wizard_cntrl'              =>  'includes/admin/wizard/class-epkb-kb-wizard-cntrl.php',
				'epkb_kb_wizard_themes'             =>  'includes/admin/wizard/class-epkb-kb-wizard-themes.php',
				'epkb_kb_wizard_ordering'           =>  'includes/admin/wizard/class-epkb-kb-wizard-ordering.php',
				'epkb_kb_wizard_global'             =>  'includes/admin/wizard/class-epkb-kb-wizard-global.php',

				// FRONT END EDITOR
				'epkb_editor_controller'            =>  'includes/admin/editor/class-epkb-editor-controller.php',
				'epkb_editor_view'                  =>  'includes/admin/editor/class-epkb-editor-view.php',
				'epkb_editor_article_page_config'   =>  'includes/admin/editor/class-epkb-editor-article-page-config.php',
				'epkb_editor_archive_page_config'   =>  'includes/admin/editor/class-epkb-editor-archive-page-config.php',
				'epkb_editor_main_page_config'      =>  'includes/admin/editor/class-epkb-editor-main-page-config.php',
				'epkb_editor_search_page_config'    =>  'includes/admin/editor/class-epkb-editor-search-page-config.php',
				'epkb_editor_config_base'           =>  'includes/admin/editor/class-epkb-editor-config-base.php',
				'epkb_editor_kb_base_config'        =>  'includes/admin/editor/class-epkb-editor-kb-base-config.php',
				'epkb_editor_utilities'             =>  'includes/admin/editor/class-epkb-editor-utilities.php',
				'epkb_editor_sidebar_config'        =>  'includes/admin/editor/class-epkb-editor-sidebar-config.php',

				// FEATURES - LAYOUT
				'epkb_layout'                       =>  'includes/features/layouts/class-epkb-layout.php',
				'epkb_layout_basic'                 =>  'includes/features/layouts/class-epkb-layout-basic.php',
				'epkb_layout_tabs'                  =>  'includes/features/layouts/class-epkb-layout-tabs.php',
				'epkb_layout_categories'            =>  'includes/features/layouts/class-epkb-layout-categories.php',
				'epkb_layout_article_sidebar'       =>  'includes/features/layouts/class-epkb-layout-article-sidebar.php',
				'epkb_layouts_setup'                =>  'includes/features/layouts/class-epkb-layouts-setup.php',

				// FEATURES - KB
				'epkb_kb_handler'                   =>  'includes/features/kbs/class-epkb-kb-handler.php',
				'epkb_kb_search'                    =>  'includes/features/kbs/class-epkb-kb-search.php',

				// FEATURES - CATEGORIES
				'epkb_categories_db'                =>  'includes/features/categories/class-epkb-categories-db.php',
				'epkb_categories_admin'             =>  'includes/features/categories/class-epkb-categories-admin.php',
				'epkb_categories_array'             =>  'includes/features/categories/class-epkb-categories-array.php',

				// FEATURES - ARTICLES
				'epkb_articles_cpt_setup'           =>  'includes/features/articles/class-epkb-articles-cpt-setup.php',
				'epkb_articles_db'                  =>  'includes/features/articles/class-epkb-articles-db.php',
				'epkb_articles_admin'               =>  'includes/features/articles/class-epkb-articles-admin.php',
				'epkb_articles_array'               =>  'includes/features/articles/class-epkb-articles-array.php',
				'epkb_articles_setup'               =>  'includes/features/articles/class-epkb-articles-setup.php',

				// FEATURES - SHORTCODES
				'epkb_shortcodes'                   =>  'includes/features/shortcodes/class-epkb-shortcodes.php',
				'epkb_articles_index_shortcode'     =>  'includes/features/shortcodes/class-epkb-articles-index-shortcode.php',

				// TEMPLATES
				'epkb_templates_various'            =>  'templates/helpers/class-epkb-templates-various.php',
			);
		}

		$cn = strtolower( $class );
		if ( isset( $classes[ $cn ] ) ) {
			/** @noinspection PhpIncludeInspection */
			include_once( plugin_dir_path( Echo_Knowledge_Base::$plugin_file ) . $classes[ $cn ] );
		}
	}
}
