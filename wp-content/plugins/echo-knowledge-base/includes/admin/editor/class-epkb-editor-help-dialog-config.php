<?php

/**
 * Configuration for the front end editor
 */
 
class EPKB_Editor_Help_Dialog_Config extends EPKB_Editor_Config_Base {

	/** SEE DOCUMENTATION IN THE BASE CLASS **/
	protected $page_type = 'help-dialog';
	/**
	 * Help dialog zone
	 * @return array
	 */
	public static function help_dialog_zone() {

		$settings = [

			// Content Tab ----------------------------------------/

			// - Top Tabs
			'help_dialog_top_button_header'                         => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'type' => 'header',
				'content' => __( 'Top Text', 'echo-knowledge-base' ),
			],
			'help_dialog_faqs_top_tab'                              => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd-tab__faq-btn span',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_contact_us_top_tab'                        => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd-tab__contact-btn span',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_welcome_text'                              => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'text' => '1',
				'target_selector' => '.eckb-hd-header__title__faq'
			],

			// - FAQ List
			'help_dialog_title_header'                              => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'type' => 'header',
				'content' => __( 'FAQ List', 'echo-knowledge-base' ),
			],
			'help_dialog_faqs_title'                                => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd-faq__header__title',
				'text' => '1',
			],
			'help_dialog_faqs_search_placeholder'                   => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#epkb-hd__search-terms',
				'target_attr' => 'placeholder'
			],
			'help_dialog_article_read_more_text'                    => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-hd_article-link',
				'text' => '1',
			],

			// - Search Results
			'help_dialog_search_results_header'                     => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'type' => 'header',
				'content' => __( 'Search Results', 'echo-knowledge-base' ),
			],
			'help_dialog_breadcrumb_home_text'                      => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#eckb-hd__breadcrumb__home',
				'text' => '1',
			],
			'help_dialog_breadcrumb_search_result_text'             => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#eckb-hd__breadcrumb__search-results',
				'text' => '1',
			],
			'help_dialog_breadcrumb_article_text'                   => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '#eckb-hd__breadcrumb__article',
				'text' => '1',
			],
			'help_dialog_found_faqs_tab_text'                       => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-hd__search-results-title__faqs',
				'text' => '1',
			],
			'help_dialog_fount_articles_tab_text'                   => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-hd__search-results-title__articles',
				'text' => '1',
			],


			// - Contact Us Tab Screen
			'help_dialog_contact_title_header'                      => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'type' => 'header',
				'content' => __( 'Contact Us Form', 'echo-knowledge-base' ),
			],
			'help_dialog_contact_title'                             => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd-faq__header__title--contact',
				'text' => '1',
			],
			'help_dialog_contact_name_text'                         => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'text' => '1',
				'target_selector' => '.epkb-hd__contact-form-user_first_name_label'
			],
			'help_dialog_contact_user_email_text'                   => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'text' => '1',
				'target_selector' => '.epkb-hd__contact-form-email_label .epkb-hd__contact-form-field__label-text'
			],
			'help_dialog_contact_subject_text'                      => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'text' => '1',
				'target_selector' => '.epkb-hd__contact-form-subject_label .epkb-hd__contact-form-field__label-text'
			],
			'help_dialog_contact_comment_text'                      => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'text' => '1',
				'target_selector' => '.epkb-hd__contact-form-comment_label .epkb-hd__contact-form-field__label-text'
			],
			'help_dialog_contact_button_title'                      => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-hd__contact-form-btn',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_contact_success_message'                   => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],

			// Style Tab ------------------------------------------/

			// - Launcher
			'help_dialog_launcher__header'                          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Launcher', 'echo-knowledge-base' ),
			],
			'help_dialog_launcher_background_color'                 => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-toggle',
				'style_name' => 'background-color'
			],
			'help_dialog_launcher_background_hover_color'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-toggle:hover',
				'style_name' => 'background-color'
			],
			'help_dialog_launcher_icon_color'                       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-toggle',
				'style_name' => 'color'
			],
			'help_dialog_launcher_icon_hover_color'                 => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-toggle:hover',
				'style_name' => 'color'
			],

			// - Help Dialog Window
			'help_dialog_header'                                    => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Help Dialog Window', 'echo-knowledge-base' ),
			],
			'help_dialog_background_color'                          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog, #eckb-help-dialog .eckb-hd-tab--active',
				'style_name' => 'background-color'
			],
			'help_dialog_not_active_tab'                            => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-tab',
				'style_name' => 'background-color'
			],
			'help_dialog_tab_text_color'                            => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-tab__faq-btn__text, .eckb-hd-tab__contact-btn__text',
				'style_name' => 'color'
			],
			'help_dialog_main_title_text_color'                     => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-header__title__faq',
				'style_name' => 'color'
			],
			'help_dialog_welcome_text_color'                        => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-faq__header__title',
				'style_name' => 'color'
			],
			'help_dialog_welcome_background_color'                  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-faq__header__title',
				'style_name' => 'background-color'
			],
			'help_dialog_breadcrumb_arrow_color'                    => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd-faq__header__breadcrumb-container .eckb-hd-faq__header__title-arrow',
				'style_name' => 'color'
			],

			// - Search Results
			'help_dialog_search_results_header_color'               => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Search Results', 'echo-knowledge-base' ),
			],
			'help_dialog_found_faqs_article_tab_color'              => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .epkb-hd__search-results-title',
				'style_name' => 'color'
			],
			'help_dialog_found_faqs_article_active_tab_color'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .epkb-hd__search-results-title--active',
				'style_name' => 'color',
				'styles' => [
					'#eckb-help-dialog .epkb-hd__search-results-title--active' => 'border-color',
				]				
			],

			// - FAQ Questions
			'help_dialog_faqs_header'                               => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'FAQ Questions', 'echo-knowledge-base' ),
			],
			'help_dialog_faqs_qa_border_color'                      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .eckb-hd-faq__list__item-container',
				'style_name' => 'border-color'
			],
			'help_dialog_faqs_question_text_color'                  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .eckb-hd__item__question',
				'style_name' => 'color'
			],
			'help_dialog_faqs_question_background_color'            => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .eckb-hd-faq__list__item-container',
				'style_name' => 'background-color'
			],
			'help_dialog_faqs_question_active_text_color'           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '
				#eckb-help-dialog .epkb-hd__element--active .eckb-hd__item__question__icon,
				#eckb-help-dialog .epkb-hd__element--active .eckb-hd__item__question__text',
				'style_name' => 'color'
			],
			'help_dialog_faqs_question_active_background_color'     => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .epkb-hd__element--active',
				'style_name' => 'background-color'
			],
			'help_dialog_faqs_answer_text_color'                    => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .eckb-hd__item__answer__text',
				'style_name' => 'color'
			],
			'help_dialog_faqs_answer_background_color'              => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog .eckb-hd__item__answer',
				'style_name' => 'background-color'
			],

			// - Single Article
			'help_dialog_single_article_header'                     => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Single Article', 'echo-knowledge-base' ),
			],
			'help_dialog_single_article_title_color'                => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd_article-item-details .epkb-hd_article-title',
				'style_name' => 'color',
				'reload' => 1
			],
			'help_dialog_single_article_desc_color'                 => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd_article-item-details .epkb-hd_article-desc',
				'style_name' => 'color',
				'reload' => 1
			],
			'help_dialog_single_article_read_more_text_color'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog #epkb-hd__search_results-cat-article-details .epkb-hd_article-link',
				'style_name' => 'color'
			],
			'help_dialog_single_article_read_more_text_hover_color' => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog #epkb-hd__search_results-cat-article-details .epkb-hd_article-link:hover',
				'style_name' => 'color'
			],

			// - Back Button
			'help_dialog_back_icon_header'                          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Back Button', 'echo-knowledge-base' ),
			],
			'help_dialog_back_text_color'                           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '
				.epkb-hd__faq__back-btn .epkb-hd__faq__back-btn__icon, 
				.epkb-hd__faq__back-btn .epkb-hd__faq__back-btn__text',
				'style_name' => 'color'
			],
			'help_dialog_back_text_color_hover_color'               => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '
				.epkb-hd__faq__back-btn:hover .epkb-hd__faq__back-btn__icon, 
				.epkb-hd__faq__back-btn:hover .epkb-hd__faq__back-btn__text',
				'style_name' => 'color'
			],
			'help_dialog_back_background_color'                     => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__faq__back-btn',
				'style_name' => 'background-color'
			],
			'help_dialog_back_background_color_hover_color'         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__faq__back-btn:hover',
				'style_name' => 'background-color'
			],


			// - Contact Us submit button
			'help_dialog_contact_form_header'                       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Contact Form', 'echo-knowledge-base' ),
			],
			'help_dialog_contact_submit_button_color'               => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn',
				'style_name' => 'background-color'
			],
			'help_dialog_contact_submit_button_hover_color'         => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn:hover',
				'style_name' => 'background-color'
			],
			'help_dialog_contact_submit_button_text_color'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn',
				'style_name' => 'color'
			],
			'help_dialog_contact_submit_button_text_hover_color'    => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn:hover',
				'style_name' => 'color'
			],


			// Features Tab ---------------------------------------/
			'help_dialog_display_mode'                  => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'help_dialog_logo_image_url'                => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],
			'help_dialog_launcher_start_delay'          => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style'       => 'small',
				'reload' => 1
			],

			'help_dialog_container_height_header'        => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header_desc',
				'title' => __( 'Help Dialog Height', 'echo-knowledge-base' ),
				'desc' => __( 'The overall height for the Help Dialog Popup', 'echo-knowledge-base' ),
			],
		/*	'help_dialog_container_desktop_height'   => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style'       => 'small',
				'styles' => [
					'.eckb-hd-body__content-container' => 'height',
				],
				'reload' => 1
			],*/


			'help_dialog_container_breakpoint_header'   => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header',
				'content' => __( 'Screen Breakpoints', 'echo-knowledge-base' ),
			],
			'help_dialog_tablet_break_point'            => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],
			'help_dialog_mobile_break_point'            => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => '1'
			],

			'help_dialog_container_width_header'        => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'type' => 'header_desc',
				'title' => __( 'Help Dialog Width', 'echo-knowledge-base' ),
				'desc' => __( 'The overall width for Help Dialog Popup', 'echo-knowledge-base' ),
			],
			'help_dialog_container_desktop_width'       => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style'       => 'small',
				'styles' => [
					'#eckb-help-dialog' => 'width',
				]
			],
			'help_dialog_container_tablet_width'        => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style'       => 'small',
				'styles' => [
					'#eckb-help-dialog' => 'width',
				]
			],
			'help_dialog_container_mobile_width'        => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'style'       => 'small',
				'styles' => [
					'#eckb-help-dialog' => 'width',
				]
			],


		];

		return [
			'help_dialog' => [
				'title'     =>  __( 'Help dialog', 'echo-knowledge-base' ),
				'classes'   => '#eckb-help-dialog',
				'settings'  => $settings,
			]];
	}
	
	/**
	 * Retrieve Editor configuration
	 */
	public function load_setting_zones() {

		// Result config
		$this->setting_zones = [];

		// add values to kb_config for help dialog
		$this->setting_zones += self::help_dialog_zone();
	}
	
	function load_config() {
		$this->config = epkb_get_instance()->help_dialog_settings_obj->get_settings_or_default();
		if ( empty($this->config) || is_wp_error($this->config) ) {
			$this->config = [];
			return;
		}
	}
	
	function load_specs() {
		$this->specs = EPKB_Help_Dialog_Settings_Specs::get_fields_specification();
	}
}