<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if plugin upgrade to a new version requires any actions like database upgrade
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_Upgrades {

	public function __construct() {
        // will run after plugin is updated but not always like front-end rendering
		add_action( 'admin_init', array( 'EPKB_Upgrades', 'update_plugin_version' ) );
        add_filter( 'eckb_plugin_upgrade_message', array( 'EPKB_Upgrades', 'display_upgrade_message' ) );
        add_action( 'eckb_remove_upgrade_message', array( 'EPKB_Upgrades', 'remove_upgrade_message' ) );

        // show initial page after install
		add_action( 'admin_init', array( 'EPKB_Upgrades', 'initial_setup' ), 20 );

		// show additional messages on the plugins page
		add_action( "in_plugin_update_message-echo-knowledge-base/echo-knowledge-base.php",  array( $this, 'in_plugin_update_message' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
	}

	/**
	 * Trigger display of wizard setup screen on plugin first activation or upgrade; does NOT work if multiple plugins installed at the same time
	 */
	public static function initial_setup() {

		$kb_version = EPKB_Utilities::get_wp_option( 'epkb_version', null );
		if ( empty( $kb_version) ) {
			return;
		}

		// ignore if plugin not recently activated
		$plugin_installed = get_transient( '_epkb_plugin_installed' );
		if ( empty( $plugin_installed ) ) {
			return;
		}

		// return if activating from network or doing bulk activation
		if ( is_network_admin() || isset($_GET['activate-multi']) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_epkb_plugin_installed' );

		// if setup ran then do not proceed
		if ( ! EPKB_Core_Utilities::is_run_setup_wizard_first_time() ) {
			return;
		}

		// run setup wizard
		wp_safe_redirect( admin_url( 'edit.php?post_type=' . EPKB_KB_Handler::get_post_type( EPKB_KB_Config_DB::DEFAULT_KB_ID ) . '&page=epkb-kb-configuration&setup-wizard-on' ) );
		exit;
	}

    /**
     * If necessary run plugin database updates
     */
    public static function update_plugin_version() {

        $last_version = EPKB_Utilities::get_wp_option( 'epkb_version', null );
		if ( empty($last_version) ) {
			EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version, true );
			EPKB_Utilities::save_wp_option( 'epkb_version_first', Echo_Knowledge_Base::$version, true );
			return;
		}

        // if plugin is up-to-date then return
        if ( version_compare( $last_version, Echo_Knowledge_Base::$version, '>=' ) ) {
            return;
        }

		// since we need to upgrade this plugin, on the Overview Page show an upgrade message
	    EPKB_Utilities::save_wp_option( 'epkb_show_upgrade_message', true, true );

        // upgrade the plugin
        self::invoke_upgrades( $last_version );

        // update the plugin version
        $result = EPKB_Utilities::save_wp_option( 'epkb_version', Echo_Knowledge_Base::$version, true );
        if ( is_wp_error( $result ) ) {
	        EPKB_Logging::add_log( 'Could not update plugin version', $result );
            return;
        }
    }

	/**
	 * Invoke each database update as necessary.
	 *
	 * @param $last_version
	 */
    private static function invoke_upgrades( $last_version ) {

        // update all KBs
        $all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
        foreach ( $all_kb_configs as $kb_config ) {

	        $update_config = self::run_upgrade( $kb_config, $last_version );

	        // store the updated KB data
	        if ( $update_config ) {
		        epkb_get_instance()->kb_config_obj->update_kb_configuration( $kb_config['id'], $kb_config );
	        }
        }
    }

    public static function run_upgrade( &$kb_config, $last_version ) {

	    $update_config = false;

	    if ( version_compare( $last_version, '3.0.0', '<' ) ) {
		    self::upgrade_to_v210( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '3.0.0', '<' ) ) {
		    self::upgrade_to_v220( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '3.1.0', '<' ) ) {
		    self::upgrade_to_v310( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '3.1.1', '<' ) ) {
		    self::upgrade_to_v311( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '4.4.2', '<' ) ) {
		    self::upgrade_to_v442( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '6.1.0', '<' ) ) {
		    self::upgrade_to_v610( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '6.1.2', '<' ) ) {
		    self::upgrade_to_v612( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '6.4.0', '<' ) ) {
		    self::upgrade_to_v640( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '6.9.0', '<' ) ) {
		    self::upgrade_to_v690( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '7.0.0', '<' ) ) {
		    self::upgrade_to_v700( $kb_config );
		    $update_config = true;
	    }

		if ( version_compare( $last_version, '7.1.0', '<' ) ) {
			self::upgrade_to_v710( $kb_config );
			$update_config = true;
		}

		if ( version_compare( $last_version, '7.2.0', '<' ) ) {
			self::upgrade_to_v720( $kb_config );
			$update_config = true;
		}

	    if ( version_compare( $last_version, '7.3.0', '<' ) ) {
		    self::upgrade_to_v730( $kb_config );
		    $update_config = true;
	    }

		if ( version_compare( $last_version, '7.4.1', '<' ) ) {
			self::upgrade_to_v741();
		}

	    if ( version_compare( $last_version, '7.6.0', '<' ) ) {
		    self::upgrade_to_v760( $kb_config );
		    $update_config = true;
	    }
		
		if ( version_compare( $last_version, '7.7.0', '<' ) ) {
		    self::upgrade_to_v770( $kb_config );
		    $update_config = true;
	    }

		if ( version_compare( $last_version, '8.0.0', '<' ) ) {
		    self::upgrade_to_v800( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '8.1.0', '<' ) ) {
		    self::upgrade_to_v810( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '8.2.0', '<' ) ) {
		    self::upgrade_to_v820( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '9.0.0', '<' ) ) {
		    self::upgrade_to_v900( $kb_config );
		    $update_config = true;
	    }

	    if ( version_compare( $last_version, '9.1.0', '<' ) ) {
		    self::upgrade_to_v910( $kb_config );
		    $update_config = true;
	    }

	    return $update_config;
	}

	private static function upgrade_to_v910( &$kb_config ) {
		if ( isset( $kb_config['last_udpated_on_text'] ) ) {
			$kb_config['last_updated_on_text'] = $kb_config['last_udpated_on_text'];
		}

		if ( isset( $kb_config['last_udpated_on_footer_toggle'] ) ) {
			$kb_config['last_updated_on_footer_toggle'] = $kb_config['last_udpated_on_footer_toggle'];
		}
	}

	private static function upgrade_to_v900( &$kb_config ) {

		// update navigation sidebar config
		$kb_config['article_nav_sidebar_type_left'] = EPKB_Core_Utilities::get_nav_sidebar_type( $kb_config, 'left' );
		$kb_config['article_nav_sidebar_type_right'] = EPKB_Core_Utilities::get_nav_sidebar_type( $kb_config, 'right' );

		// handle Elegant Layouts upgrade
		if ( ! EPKB_Utilities::is_elegant_layouts_enabled() ) {
			return;
		}

		if ( function_exists( 'elay_get_instance' ) && isset( elay_get_instance()->kb_config_obj ) ) {
			$elay_config = elay_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_config['id'] );
		} else {
			return;
		}

		$sidebar_settings = [
			'sidebar_side_bar_height_mode',
			'sidebar_side_bar_height',
			'sidebar_scroll_bar',
			'sidebar_section_category_typography',
			'sidebar_section_category_typography_desc',
			'sidebar_section_body_typography',
			'sidebar_top_categories_collapsed',
			'sidebar_nof_articles_displayed',
			'sidebar_show_articles_before_categories',
			'sidebar_expand_articles_icon',
			'sidebar_section_head_alignment',
			'sidebar_section_head_padding_top',
			'sidebar_section_head_padding_bottom',
			'sidebar_section_head_padding_left',
			'sidebar_section_head_padding_right',
			'sidebar_section_desc_text_on',
			'sidebar_section_border_radius',
			'sidebar_section_border_width',
			'sidebar_section_box_shadow',
			'sidebar_section_divider',
			'sidebar_section_divider_thickness',
			'sidebar_section_box_height_mode',
			'sidebar_section_body_height',
			'sidebar_section_body_padding_top',
			'sidebar_section_body_padding_bottom',
			'sidebar_section_body_padding_left',
			'sidebar_section_body_padding_right',
			'sidebar_article_underline',
			'sidebar_article_active_bold',
			'sidebar_article_list_margin',
			'sidebar_article_list_spacing',
			'sidebar_background_color',
			'sidebar_article_font_color',
			'sidebar_article_icon_color',
			'sidebar_article_active_font_color',
			'sidebar_article_active_background_color',
			'sidebar_section_head_font_color',
			'sidebar_section_head_background_color',
			'sidebar_section_head_description_font_color',
			'sidebar_section_border_color',
			'sidebar_section_divider_color',
			'sidebar_section_category_font_color',
			'sidebar_section_subcategory_typography',
			'sidebar_section_category_icon_color',
			'sidebar_category_empty_msg',
			'sidebar_collapse_articles_msg',
			'sidebar_show_all_articles_msg'
		];

		foreach ( $sidebar_settings as $setting_name ) {
			if ( ! isset( $elay_config[$setting_name] ) ) {
				continue;
			}

			$kb_config[$setting_name] = $elay_config[$setting_name];
		}
	}

	private static function upgrade_to_v820( &$kb_config ) {
		$kb_config['admin_eckb_access_frontend_editor_write'] = empty( $kb_config['access_frontend_editor_write'] ) ? EPKB_Admin_UI_Access::get_admin_capability() : $kb_config['access_frontend_editor_write'];
		$kb_config['admin_eckb_access_search_analytics_read'] = empty( $kb_config['access_search_analytics_read'] ) ? EPKB_Admin_UI_Access::get_admin_capability() : $kb_config['access_search_analytics_read'];
		$kb_config['admin_eckb_access_order_articles_write'] = empty( $kb_config['access_order_articles_write'] ) ? EPKB_Admin_UI_Access::get_admin_capability() : $kb_config['access_order_articles_write'];
		$kb_config['admin_eckb_access_need_help_read'] = empty( $kb_config['access_need_help_read'] ) ? EPKB_Admin_UI_Access::get_admin_capability() : $kb_config['access_need_help_read'];
		$kb_config['admin_eckb_access_addons_news_read'] = empty( $kb_config['access_addons_news_read'] ) ? EPKB_Admin_UI_Access::get_admin_capability() : $kb_config['access_addons_news_read'];
    }

	private static function upgrade_to_v810( &$kb_config ) {
		$kb_config['admin_eckb_access_frontend_editor_write'] = EPKB_Admin_UI_Access::get_admin_capability();
		$kb_config['admin_eckb_access_search_analytics_read'] = EPKB_Admin_UI_Access::get_admin_capability();
		$kb_config['admin_eckb_access_order_articles_write'] = EPKB_Admin_UI_Access::get_admin_capability();
		$kb_config['admin_eckb_access_need_help_read'] = EPKB_Admin_UI_Access::get_editor_capability();
		$kb_config['admin_eckb_access_addons_news_read'] = EPKB_Admin_UI_Access::get_editor_capability();
	}

	private static function upgrade_to_v800( &$kb_config ) {
		$kb_config['article-meta-typography'] = array_merge( EPKB_Typography::$typography_defaults, $kb_config['breadcrumb_typography'] );
	}

	private static function upgrade_to_v770( &$kb_config ) {

		if ( ! empty($kb_config['categories_box_font_size']) ) {
			$kb_config['categories_box_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $kb_config['categories_box_font_size'] ) );
		}
		
		if ( ! empty($kb_config['article_search_title_font_size']) ) {
			$kb_config['article_search_title_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $kb_config['article_search_title_font_size'] ) );
		}
		
		if ( ! empty($kb_config['article_toc_font_size']) ) {
			$kb_config['article_toc_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $kb_config['article_toc_font_size'] ) );
			$kb_config['article_toc_header_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $kb_config['article_toc_font_size'] ) );
		}
		
		if ( ! empty($kb_config['back_navigation_font_size']) ) {
			$kb_config['back_navigation_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $kb_config['back_navigation_font_size'] ) );
		}
		if ( ! empty($kb_config['breadcrumb_font_size']) ) {
			$kb_config['breadcrumb_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $kb_config['breadcrumb_font_size'] ) );
		}

		if ( ! empty($kb_config['section_font_size']) ) {

			switch ( $kb_config['section_font_size'] ) {
				case 'section_xsmall_font':
					$section_font_size = '10';
					$section_head_typography = ( $kb_config['kb_main_page_layout'] == 'Tabs' ) ? '14' : '15';
					$section_head_description_typography = '12';
					break;
				case 'section_small_font':
					$section_font_size = '12';
					$section_head_typography =  ( $kb_config['kb_main_page_layout'] == 'Tabs' ) ? '17' :'18';
					$section_head_description_typography = '14';
					break;
				case 'section_medium_font':
					$section_font_size = '14';
					$section_head_typography =  ( $kb_config['kb_main_page_layout'] == 'Tabs' ) ? '20' :'21';
					$section_head_description_typography = '17';
					break;
				case 'section_large_font':
					$section_font_size = '16';
					$section_head_typography =  ( $kb_config['kb_main_page_layout'] == 'Tabs' ) ? '22' : '24';
					$section_head_description_typography = '19';
					break;
				default:
					$section_font_size = '14';
					$section_head_typography = '21';
					$section_head_description_typography = '14';
					break;
			}

			$kb_config['section_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $section_font_size ) );
			$kb_config['section_head_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $section_head_typography ) );
			$kb_config['section_head_description_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $section_head_description_typography ) );
		}
		
		if ( ! empty($kb_config['tab_font_size']) ) {
			
			switch ( $kb_config['tab_font_size'] ) {
				case 'tab_small_font':
					$tab_font_size = '12';
					break;
				case 'tab_medium_font':
					$tab_font_size = '14';
					break;
				case 'tab_large_font':
					$tab_font_size = '16';
					break;
				default:
					$tab_font_size = '14';
					break;
			}
			
			$kb_config['tab_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $tab_font_size ) );
		}

		if ( ! empty($kb_config['search_title_font_size']) ) {
			$kb_config['search_title_typography'] = array_merge( EPKB_Typography::$typography_defaults, array( 'font-size' => $kb_config['search_title_font_size'] ) );
		}
	}

	private static function upgrade_to_v760( &$kb_config ) {
		$kb_config['section_hyperlink_on'] = empty($kb_config['section_hyperlink_text_on']) ? 'off' : $kb_config['section_hyperlink_text_on'];
	}

	private static function upgrade_to_v741() {
		EPKB_Logging::reset_logs();
	}

	private static function upgrade_to_v730( &$kb_config ) {
	
		$settings = EPKB_Settings_Specs::get_default_settings();
		epkb_get_instance()->settings_obj->update_settings( $settings );

		// changes to template config names
		$kb_config['template_widget_sidebar_defaults'] = $kb_config['templates_for_kb_widget_sidebar_defaults'];
		$kb_config['template_main_page_display_title'] = $kb_config['templates_display_main_page_main_title'];
		$kb_config['template_main_page_padding_top'] = $kb_config['templates_for_kb_padding_top'];
		$kb_config['template_main_page_padding_bottom'] = $kb_config['templates_for_kb_padding_bottom'];
		$kb_config['template_main_page_padding_left'] = $kb_config['templates_for_kb_padding_left'];
		$kb_config['template_main_page_padding_right'] = $kb_config['templates_for_kb_padding_right'];
		$kb_config['template_main_page_margin_top'] = $kb_config['templates_for_kb_margin_top'];
		$kb_config['template_main_page_margin_bottom'] = $kb_config['templates_for_kb_margin_bottom'];
		$kb_config['template_main_page_margin_left'] = $kb_config['templates_for_kb_margin_left'];
		$kb_config['template_main_page_margin_right'] = $kb_config['templates_for_kb_margin_right'];
		$kb_config['template_article_padding_top'] = $kb_config['templates_for_kb_article_padding_top'];
		$kb_config['template_article_padding_bottom'] = $kb_config['templates_for_kb_article_padding_bottom'];
		$kb_config['template_article_padding_left'] = $kb_config['templates_for_kb_article_padding_left'];
		$kb_config['template_article_padding_right'] = $kb_config['templates_for_kb_article_padding_right'];
		$kb_config['template_article_margin_top'] = $kb_config['templates_for_kb_article_margin_top'];
		$kb_config['template_article_margin_bottom'] = $kb_config['templates_for_kb_article_margin_bottom'];
		$kb_config['template_article_margin_left'] = $kb_config['templates_for_kb_article_margin_left'];
		$kb_config['template_article_margin_right'] = $kb_config['templates_for_kb_article_margin_right'];
		$kb_config['template_category_archive_page_style'] = $kb_config['templates_for_kb_category_archive_page_style'];
		$kb_config['template_category_archive_page_heading_description'] = $kb_config['templates_for_kb_category_archive_page_heading_description'];
		$kb_config['template_category_archive_read_more'] = $kb_config['templates_for_kb_category_archive_read_more'];

		//Upgrade Demo Icons
		$categories_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, array(), true );
		$old_icon_list = array(
			'1' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/info-icon.png',
			'2' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/e-commerce-icon.png',
			'3' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/earth-icon.png',
			'4' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/payment-icon.png',
			'5' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/price-tag-icon.png',
			'6' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/women-icon.png',
			'7' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/info-icon.png',
			'8' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/e-commerce-icon.png',
			'9' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/earth-icon.png',
			'10' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/payment-icon.png',
			'11' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/price-tag-icon.png',
			'12' => Echo_Knowledge_Base::$plugin_url . 'img/features-wizard/demo-icons/women-icon.png',
		);
		$icon_list = array(
			'1' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/info-icon.png',
			'2' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/e-commerce-icon.png',
			'3' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/earth-icon.png',
			'4' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/payment-icon.png',
			'5' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/price-tag-icon.png',
			'6' => Echo_Knowledge_Base::$plugin_url . 'img/demo-icons/icons/users.png',
		);

		$old_image_found = false;
		foreach( $categories_icons as $id => $category_icon ) {
			if ( empty($category_icon['image_thumbnail_url']) ) {
				continue;
			}
			if ( ( $key = array_search($category_icon['image_thumbnail_url'], $old_icon_list) ) !== false ) {
				$key = ( $key > 6 ) ? $key - 6 : $key;
				if ( isset($icon_list[$key]) ) {
					$categories_icons[$id]['image_thumbnail_url'] = $icon_list[$key];
					$old_image_found = true;
				}
			}
		}

		if ( $old_image_found ) {
			EPKB_Utilities::save_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, $categories_icons, true );
		}
	}

	private static function upgrade_to_v720( &$kb_config ) {
		$kb_config['breadcrumb_enable'] = isset($kb_config['article_content_enable_breadcrumb']) ? $kb_config['article_content_enable_breadcrumb'] : $kb_config['breadcrumb_enable'];
		$kb_config['print_button_enable'] = $kb_config['article_content_enable_print_button'];  // hide for old users
	}

	private static function upgrade_to_v710( &$kb_config ) {

		// current user will not start with the article content rows or print button enabled
		$kb_config['article_content_enable_rows'] = 'off';
		$kb_config['article_content_enable_print_button'] = 'off';
		$kb_config['breadcrumb_margin_bottom_old'] = $kb_config['breadcrumb_margin_bottom'];
		$kb_config['breadcrumb_margin_bottom'] = 0;

		// prepare for article content rows
		$kb_config['last_updated_date_icon_on'] = $kb_config['article_meta_icon_on'];
		$kb_config['created_date_icon_on'] = $kb_config['article_meta_icon_on'];
		$kb_config['author_icon_on'] = $kb_config['article_meta_icon_on'];

		$kb_config['article_content_enable_last_updated_date'] = isset( $kb_config['last_udpated_on_header_toggle'] ) ? $kb_config['last_udpated_on_header_toggle'] : 'off';
		$kb_config['article_content_enable_created_date'] = $kb_config['created_on_header_toggle'];
		$kb_config['article_content_enable_author'] = $kb_config['author_header_toggle'];

		$kb_config['article_content_enable_breadcrumb'] = $kb_config['breadcrumb_toggle'];
		$kb_config['article_content_enable_back_navigation'] = $kb_config['back_navigation_toggle'];
	}

	private static function upgrade_to_v700( &$kb_config ) {

    	$kb_config['article_search_title_html_tag'] = $kb_config['search_title_html_tag'];

    	// add padding all around
		if ( isset($kb_config['article-left-sidebar-padding-v2']) ) {
			$kb_config['article-left-sidebar-padding-v2_top'] = $kb_config['article-left-sidebar-padding-v2'];
			$kb_config['article-left-sidebar-padding-v2_right'] = $kb_config['article-left-sidebar-padding-v2'];
			$kb_config['article-left-sidebar-padding-v2_bottom'] = $kb_config['article-left-sidebar-padding-v2'];
			$kb_config['article-left-sidebar-padding-v2_left'] = $kb_config['article-left-sidebar-padding-v2'];
		}

		if ( isset($kb_config['article-right-sidebar-padding-v2']) ) {
			$kb_config['article-right-sidebar-padding-v2_top'] = $kb_config['article-right-sidebar-padding-v2'];
			$kb_config['article-right-sidebar-padding-v2_right'] = $kb_config['article-right-sidebar-padding-v2'];
			$kb_config['article-right-sidebar-padding-v2_bottom'] = $kb_config['article-right-sidebar-padding-v2'];
			$kb_config['article-right-sidebar-padding-v2_left'] = $kb_config['article-right-sidebar-padding-v2'];
		}

		// upgrade config for back navigation from TEXT to NUMBER
		$kb_config['back_navigation_font_size'] = is_numeric( $kb_config['back_navigation_font_size'] ) ? intval( $kb_config['back_navigation_font_size'] ) : '16';
		$kb_config['back_navigation_font_size'] = empty($kb_config['back_navigation_font_size'] ) ? '16' : $kb_config['back_navigation_font_size'];

		$kb_config['back_navigation_border_radius'] = is_numeric( $kb_config['back_navigation_border_radius'] ) ? intval( $kb_config['back_navigation_border_radius'] ) : '3';
		$kb_config['back_navigation_border_radius'] = empty($kb_config['back_navigation_border_radius'] ) ? '3' : $kb_config['back_navigation_border_radius'];

		$kb_config['back_navigation_border_width'] = is_numeric( $kb_config['back_navigation_border_width'] ) ? intval( $kb_config['back_navigation_border_width'] ) : '1';
		$kb_config['back_navigation_border_width'] = empty($kb_config['back_navigation_border_width'] ) ? '1' : $kb_config['back_navigation_border_width'];

		// setup left sidebar
		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );
		$is_v2_version = $kb_config['article-structure-version']  == 'version-2' || $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME;
		$left_sidebar =     $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT ||
		                    ( $is_v2_version && $sidebar_priority['toc_left'] ) ||                                                        // TOC
		                    ( $is_v2_version && $sidebar_priority['kb_sidebar_left'] ) ||                                                 // KB Sidebar
		                    ( $is_v2_version && ! empty( $sidebar_priority['elay_sidebar_left'] ) ) ||                                               // Elegant Layouts Navigation bar
		                    ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME && ! empty( $sidebar_priority['categories_left'] ) ); // Categories Layout always on the left */
		$kb_config['article-left-sidebar-toggle'] = $left_sidebar ? 'on' : 'off';

		// setup right sidebar
		$right_sidebar =    ( $is_v2_version && $sidebar_priority['toc_right'] ) ||                                                       // TOC v2
		                    ( ! $is_v2_version && $kb_config['article_toc_enable'] == 'on' ) ||                                           // TOC v1
		                    ( $is_v2_version && $sidebar_priority['kb_sidebar_right'] ) ||                                                // KB Sidebar
                            ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME && ! empty( $sidebar_priority['categories_right'] ) );              // Categories Layout always on the right
		$kb_config['article-right-sidebar-toggle'] = $right_sidebar ? 'on' : 'off';

		// meta data
		if ( isset($kb_config['created_on']) && isset($kb_config['last_udpated_on']) && isset($kb_config['author_mode']) ) {
			$check_value = 'article_top';
			$is_created_on      = $kb_config['created_on'] == $check_value;
			$is_last_updated_on = $kb_config['last_udpated_on'] == $check_value;
			$is_author          = $kb_config['author_mode'] == $check_value;
			$is_meta_on = EPKB_Utilities::is_article_rating_enabled();
			$kb_config['meta-data-header-toggle'] = ( $is_created_on || $is_last_updated_on || $is_author || $is_meta_on ) ? 'on' : 'off';

			$check_value = 'article_bottom';
			$is_created_on      = $kb_config['created_on'] == $check_value;
			$is_last_updated_on = $kb_config['last_udpated_on'] == $check_value;
			$is_author          = $kb_config['author_mode'] == $check_value;
			$is_meta_on = EPKB_Utilities::is_article_rating_enabled();
			$kb_config['meta-data-footer-toggle'] = ( $is_created_on || $is_last_updated_on || $is_author || $is_meta_on ) ? 'on' : 'off';

			$kb_config['created_on_header_toggle'] = $kb_config['created_on'] == 'article_top' ? 'on' : 'off';
			$kb_config['created_on_footer_toggle'] = $kb_config['created_on'] == 'article_bottom' ? 'on' : 'off';
			$kb_config['last_udpated_on_header_toggle'] = $kb_config['last_udpated_on'] == 'article_top' ? 'on' : 'off';
			$kb_config['last_udpated_on_footer_toggle'] = $kb_config['last_udpated_on'] == 'article_bottom' ? 'on' : 'off';
			$kb_config['author_header_toggle'] = $kb_config['author_mode'] == 'article_top' ? 'on' : 'off';
			$kb_config['author_footer_toggle'] = $kb_config['author_mode'] == 'article_bottom' ? 'on' : 'off';
		}

		// handle Elegant Layouts upgrade
    	if ( ! EPKB_Utilities::is_elegant_layouts_enabled() ) {
			return;
		}

		if ( function_exists('elay_get_instance' ) && isset(elay_get_instance()->kb_config_obj) ) {
			$elay_config = elay_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_config['id'] );
		} else {
			return;
		}

		$main_page = $kb_config['kb_main_page_layout'];
		if ( $main_page == 'Grid' || $main_page == 'Sidebar' ) {

			$prefix = $main_page == 'Grid' ? 'grid_' : 'sidebar_';

			$kb_config['search_title_font_color'] = $elay_config[$prefix . 'search_title_font_color'];
			$kb_config['search_background_color'] = $elay_config[$prefix . 'search_background_color'];
			$kb_config['search_text_input_background_color'] = $elay_config[$prefix . 'search_text_input_background_color'];
			$kb_config['search_text_input_border_color'] = $elay_config[$prefix . 'search_text_input_border_color'];
			$kb_config['search_btn_background_color'] = $elay_config[$prefix . 'search_btn_background_color'];
			$kb_config['search_btn_border_color'] = $elay_config[$prefix . 'search_btn_border_color'];

			$kb_config['search_layout'] = str_replace( 'elay', 'epkb', $elay_config[$prefix . 'search_layout'] ) ;

			$kb_config['search_input_border_width'] = $elay_config[$prefix . 'search_input_border_width'];
			$kb_config['search_box_padding_top'] = $elay_config[$prefix . 'search_box_padding_top'];
			$kb_config['search_box_padding_bottom'] = $elay_config[$prefix . 'search_box_padding_bottom'];
			$kb_config['search_box_padding_left'] = $elay_config[$prefix . 'search_box_padding_left'];
			$kb_config['search_box_padding_right'] = $elay_config[$prefix . 'search_box_padding_right'];
			$kb_config['search_box_margin_top'] = $elay_config[$prefix . 'search_box_margin_top'];
			$kb_config['search_box_margin_bottom'] = $elay_config[$prefix . 'search_box_margin_bottom'];
			$kb_config['search_box_input_width'] = $elay_config[$prefix . 'search_box_input_width'];

			if ( $main_page == 'Sidebar' ) {
				$kb_config['search_box_results_style'] = $elay_config[$prefix . 'search_box_results_style'];
			}

			$kb_config['search_title'] = $elay_config[$prefix . 'search_title'];
			$kb_config['search_box_hint'] = $elay_config[$prefix . 'search_box_hint'];
			$kb_config['search_button_name'] = $elay_config[$prefix . 'search_button_name'];
			$kb_config['search_results_msg'] = $elay_config[$prefix . 'search_results_msg'];
		}

		$prefix = 'sidebar_';

		$kb_config['article_search_title_font_color'] = $elay_config[$prefix . 'search_title_font_color'];
		$kb_config['article_search_background_color'] = $elay_config[$prefix . 'search_background_color'];
		$kb_config['article_search_text_input_background_color'] = $elay_config[$prefix . 'search_text_input_background_color'];
		$kb_config['article_search_text_input_border_color'] = $elay_config[$prefix . 'search_text_input_border_color'];
		$kb_config['article_search_btn_background_color'] = $elay_config[$prefix . 'search_btn_background_color'];
		$kb_config['article_search_btn_border_color'] = $elay_config[$prefix . 'search_btn_border_color'];

		$kb_config['article_search_layout'] = str_replace( 'elay', 'epkb', $elay_config[$prefix . 'search_layout'] ) ;

		$kb_config['article_search_input_border_width'] = $elay_config[$prefix . 'search_input_border_width'];
		$kb_config['article_search_box_padding_top'] = $elay_config[$prefix . 'search_box_padding_top'];
		$kb_config['article_search_box_padding_bottom'] = $elay_config[$prefix . 'search_box_padding_bottom'];
		$kb_config['article_search_box_padding_left'] = $elay_config[$prefix . 'search_box_padding_left'];
		$kb_config['article_search_box_padding_right'] = $elay_config[$prefix . 'search_box_padding_right'];
		$kb_config['article_search_box_margin_top'] = $elay_config[$prefix . 'search_box_margin_top'];
		$kb_config['article_search_box_margin_bottom'] = $elay_config[$prefix . 'search_box_margin_bottom'];
		$kb_config['article_search_box_input_width'] = $elay_config[$prefix . 'search_box_input_width'];
		$kb_config['article_search_box_results_style'] = $elay_config[$prefix . 'search_box_results_style'];

		$kb_config['article_search_title'] = $elay_config[$prefix . 'search_title'];
		$kb_config['article_search_box_hint'] = $elay_config[$prefix . 'search_box_hint'];
		$kb_config['article_search_button_name'] = $elay_config[$prefix . 'search_button_name'];
		$kb_config['article_search_results_msg'] = $elay_config[$prefix . 'search_results_msg'];

		$kb_config['article_search_box_collapse_mode'] = $elay_config[$prefix . 'search_box_collapse_mode'];
		
	}

	private static function upgrade_to_v690( &$kb_config ) {
		if ( isset($kb_config['article_toc_start_level']) ) {
			$kb_config['article_toc_hx_level'] = $kb_config['article_toc_start_level'];
			$kb_config['article_toc_hy_level'] = 6;
		}
	}

	private static function upgrade_to_v640( &$kb_config ) {
		
		if ( isset($kb_config['article-left-sidebar-width-v2']) ) {
			$kb_config['article-left-sidebar-desktop-width-v2'] = $kb_config['article-left-sidebar-width-v2'];
		}
		
		if ( isset($kb_config['article-content-width-v2']) ) {
			$kb_config['article-content-desktop-width-v2'] = $kb_config['article-content-width-v2'];
		}
		
		if ( isset($kb_config['article-right-sidebar-width-v2']) ) {
			$kb_config['article-right-sidebar-desktop-width-v2'] = $kb_config['article-right-sidebar-width-v2'];
		}
		
		if ( isset($kb_config['article-container-width-v2']) ) {
			$kb_config['article-container-desktop-width-v2'] = $kb_config['article-container-width-v2'];
		}
		
		if ( isset($kb_config['article-container-width-units-v2']) ) {
			$kb_config['article-container-desktop-width-units-v2'] = $kb_config['article-container-width-units-v2'];
		}
	}
	
	private static function upgrade_to_v612( &$kb_config ) {

		// if KB Main Page is Grid then use Elegant icons
		if ( $kb_config['kb_main_page_layout'] != 'Grid' ) {
			return;
		}

		$old_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], 'elay_categories_icons', array(), true );

		// update the existing icons to new format
		$new_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, array(), true );
		foreach ( $old_icons as $term_id => $icon_name ) {
			$new_icons[$term_id] = array(
				'type' => 'font',
				'name' => $icon_name,
				'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
				'image_size' => '',
				'image_thumbnail_url' => '',
				'color' => '#000000'
			);
		}

		EPKB_Utilities::save_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, $new_icons, true );

		// update category link from old to new config
		$kb_config['section_hyperlink_text_on'] = ( $kb_config['kb_main_page_category_link'] == 'default' ) ? 'off' : 'on';
	}

	private static function upgrade_to_v610( &$kb_config ) {

		// get old categories icons
		$old_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], 'epkb_categories_icons', array(), true );

		// if KB Main Page is Grid then use Elegant Layouts icons
		if ( $kb_config['kb_main_page_layout'] == 'Grid' ) {
			$old_grid_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], 'elay_categories_icons', array(), true );
			if ( ! empty($old_grid_icons) ) {
				$old_icons = $old_grid_icons;
			}
		}

		// update the existing icons to new format
		$new_icons = EPKB_Utilities::get_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, array(), true );
		foreach ( $old_icons as $term_id => $icon_name ) {
			$new_icons[$term_id] = array(
				'type' => 'font',
				'name' => $icon_name,
				'image_id' => EPKB_Icons::DEFAULT_CATEGORY_IMAGE_ID,
				'image_size' => '',
				'image_thumbnail_url' => '',
				'color' => '#000000'
			);
		}
		
		EPKB_Utilities::save_kb_option( $kb_config['id'], EPKB_Icons::CATEGORIES_ICONS, $new_icons, true );
		
		// delete_option( 'epkb_categories_icons_' . $kb_config['id'] );

		// update category link from old to new config
		$kb_config['section_hyperlink_text_on'] = ( $kb_config['kb_main_page_category_link'] == 'default' ) ? 'off' : 'on';
	}
	
	private static function upgrade_to_v442( &$kb_config ) {
		$wpml_enabled = EPKB_Utilities::get_wp_option( 'epkb_wpml_enabled', false );
		$kb_config['wpml_is_enabled'] = $wpml_enabled === 'true';
	}

	private static function upgrade_to_v311( &$kb_config ) {
		$kb_config['breadcrumb_icon_separator'] = str_replace( 'ep_icon', 'ep_font_icon', $kb_config['breadcrumb_icon_separator'] );
		$kb_config['expand_articles_icon'] = str_replace( 'ep_icon', 'ep_font_icon', $kb_config['expand_articles_icon'] );
	}

	private static function upgrade_to_v310( &$kb_config ) {
		if ( empty($kb_config['css_version']) ) {
			$kb_config['css_version'] = 'css-legacy';
		}
	}

	private static function upgrade_to_v220( &$kb_config ) {
		if ( empty($kb_config['templates_for_kb']) ) {
			$kb_config['templates_for_kb'] = 'current_theme_templates';
		}

		if ( $kb_config['kb_main_page_layout'] == 'Sidebar' ) {
			$kb_config['kb_article_page_layout'] = 'Sidebar';
		}
	}

	private static function upgrade_to_v210( &$kb_config ) {
		if ( isset($kb_config['expand_articles_icon']) && substr($kb_config['expand_articles_icon'], 0, strlen('ep_' )) !== 'ep_' ) {
			$kb_config['expand_articles_icon'] = str_replace( 'icon_plus-box', 'ep_font_icon_plus_box', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'icon_plus', 'ep_font_icon_plus', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'arrow_triangle-right', 'ep_font_icon_right_arrow', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'arrow_carrot-right_alt2', 'ep_font_icon_arrow_carrot_right_circle', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'arrow_carrot-right', 'ep_font_icon_arrow_carrot_right', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'icon_folder-add_alt', 'ep_font_icon_folder_add', $kb_config['expand_articles_icon'] );
			$kb_config['expand_articles_icon'] = str_replace( 'ep_ep_', 'ep_', $kb_config['expand_articles_icon'] );
		}
		if ( $kb_config['expand_articles_icon'] == 'ep_font_icon_arrow_carrot_right_alt2' ) {
			$kb_config['expand_articles_icon'] = 'ep_font_icon_arrow_carrot_right';
		}
	}

    /**
     * Show upgrade message on Overview Page.
     *
     * @param $output
     * @return string
     */
	public static function display_upgrade_message( $output ) {

		if ( EPKB_Utilities::get_wp_option( 'epkb_show_upgrade_message', false ) ) {
			
			$plugin_name = '<strong>' . esc_html__('Knowledge Base', 'echo-knowledge-base') . '</strong>';
			$output .= '<p>' . esc_html( $plugin_name ) . ' ' . sprintf( esc_html( _x( 'plugin was updated to version %s.', ' version number, link to what is new page', 'echo-knowledge-base' ) ),
									Echo_Knowledge_Base::$version ) . '</p>';
		}

		return $output;
	}
    
    public static function remove_upgrade_message() {
        delete_option('epkb_show_upgrade_message');
    }

	/**
	 * Function for major updates
	 *
	 * @param $args
	 */
	function in_plugin_update_message( $args ) {

		$current_version = Echo_Knowledge_Base::$version;
		$new_version = empty( $args['new_version'] ) ? $current_version : $args['new_version'];

		if ( ! preg_match( '/.*\.\d0\..*/', $new_version ) ) {
			return;
		}

		echo '<style> .epkb-update-warning+p { opacity: 0; height: 0;} </style> ';
		echo '<hr style="clear:left"><div class="epkb-update-warning"><span class="dashicons dashicons-info" style="float:left;margin-right: 6px;color: #d63638;"></span>';
		echo '<div class="epkb-update-warning__title">' . esc_html__( 'We highly recommend you back up your site before upgrading, and make sure you first update in a staging environment.', 'echo-knowledge-base' ) . '</div>';
		echo '<div class="epkb-update-warning__message">' .	esc_html__( 'The latest update includes some substantial changes across different areas of the plugin', 'echo-knowledge-base' ) . '</div></div>';
	}

	function after_switch_theme() {
		EPKB_Core_Utilities::update_kb_flag( 'epkb_the_content_fix', false );
	}
}