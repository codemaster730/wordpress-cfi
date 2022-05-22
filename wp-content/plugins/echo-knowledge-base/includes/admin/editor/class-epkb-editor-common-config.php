<?php

/**
 * Configuration for the front end editor
 */
 
class EPKB_Editor_Common_Config extends EPKB_Editor_Base_Config {

	/**
	 * Help dialog zone
	 * @return array
	 */
	public static function help_dialog_zone() {

		$settings = [

			// Content Tab

			// - FAQs
			'help_dialog_faqs_title_header'             => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'type' => 'header',
				'content' => __( 'FAQs', 'echo-knowledge-base' ),
			],
			'help_dialog_faqs_title'                       => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd__header_faq__title',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_faqs_top_button_title'                       => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd__header-button-search span',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_faqs_search_placeholder'          => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'reload' => 1,
			],
			'help_dialog_faqs_read_more_text'              => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],

			// - Contact Us
			'help_dialog_contact_title_header'             => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'type' => 'header',
				'content' => __( 'Contact Us Form', 'echo-knowledge-base' ),
			],
			'help_dialog_contact_title'                    => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd__header_contact__title',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_contact_top_button_title'                       => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.eckb-hd__header-button-contact span',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_contact_name_placeholder'         => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'reload' => 1,
			],
			'help_dialog_contact_user_email_placeholder'        => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'reload' => 1,
			],
			'help_dialog_contact_subject_placeholder'      => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'reload' => 1,
			],
			'help_dialog_contact_comment_placeholder'      => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'reload' => 1,
			],
			'help_dialog_contact_button_title'             => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
				'target_selector' => '.epkb-hd__contact-form-btn',
				'target_attr' => 'value',
				'text' => '1',
			],
			'help_dialog_contact_success_message'          => [
				'editor_tab' => self::EDITOR_TAB_CONTENT,
			],

			// Style Tab
			'help_dialog_launcher__header'                 => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Launcher', 'echo-knowledge-base' ),
			],
			'help_dialog_launcher_background_color'        => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-help-dialog-toggle',
				'style_name' => 'background-color'
			],
			'help_dialog_launcher_background_hover_color'  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-help-dialog-toggle:hover',
				'style_name' => 'background-color'
			],
			'help_dialog_launcher_icon_color'              => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-help-dialog-toggle',
				'style_name' => 'color'
			],
			'help_dialog_launcher_icon_hover_color'        => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-help-dialog-toggle:hover',
				'style_name' => 'color'
			],

			'help_dialog_header'                           => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Help dialog', 'echo-knowledge-base' ),
			],
			'help_dialog_background_color'                 => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '#eckb-help-dialog',
				'style_name' => 'background-color'
			],
			'help_dialog_text_color'                       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__body',
				'style_name' => 'color'
			],
			'help_dialog_text_hover_color'                 => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' =>
					'.eckb-hd__body .epkb-hd_cat-item:hover,
					 .eckb-hd__body .epkb-hd_article-item:hover
					',
				'style_name' => 'color'
			],

			// - top toggle button
			'help_dialog_buttons_header'                   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Top Toggle Button', 'echo-knowledge-base' ),
			],
			'help_dialog_top_button_color'                     => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-btn',
				'style_name' => 'background-color'
			],
			'help_dialog_top_button_hover_color'               => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-btn:hover',
				'style_name' => 'background-color'
			],
			'help_dialog_top_button_text_color'                => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-btn',
				'style_name' => 'color'
			],
			'help_dialog_top_button_text_hover_color'          => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-btn:hover',
				'style_name' => 'color'
			],

			// - Contact Us submit button
			'help_dialog_contact_form_header'              => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Contact Form', 'echo-knowledge-base' ),
			],
			'help_dialog_contact_button_color'             => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn',
				'style_name' => 'background-color'
			],
			'help_dialog_contact_button_hover_color'       => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn:hover',
				'style_name' => 'background-color'
			],
			'help_dialog_contact_button_text_color'        => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn',
				'style_name' => 'color'
			],
			'help_dialog_contact_button_text_hover_color'  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd__contact-form-btn:hover',
				'style_name' => 'color'
			],

			'help_dialog_faqs_read_more_header'            => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Read More Link', 'echo-knowledge-base' ),
			],
			'help_dialog_faqs_read_more_text_color'        => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.epkb-hd_article-link',
				'style_name' => 'color'
			],
			'help_dialog_faqs_read_more_text_hover_color'  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' =>
					'.epkb-hd_article-link:hover
					',
				'style_name' => 'color'
			],

			// - back button icon
			'help_dialog_back_icon_header'              => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'type' => 'header',
				'content' => __( 'Back Button', 'echo-knowledge-base' ),
			],
			'help_dialog_back_icon_color'                  => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-back-icon',
				'style_name' => 'color'
			],
			'help_dialog_back_icon_color_hover_color'      => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-back-icon:hover',
				'style_name' => 'color'
			],
			'help_dialog_back_icon_bg_color'               => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-back-icon',
				'style_name' => 'background-color'
			],
			'help_dialog_back_icon_bg_color_hover_color'   => [
				'editor_tab' => self::EDITOR_TAB_STYLE,
				'target_selector' => '.eckb-hd__header-back-icon:hover',
				'style_name' => 'background-color'
			],


			// Features Tab
			'help_dialog_display_mode' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'help_dialog_logo_image_url' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],
			'help_dialog_launcher_when_to_display' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1
			],
			'help_dialog_faqs_kb' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'reload' => 1,
			],
			'help_dialog_faqs_category_ids' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
				'description' => 'Enter comma separated category ids.'
			],
			'help_dialog_contact_submission_email' => [
				'editor_tab' => self::EDITOR_TAB_FEATURES,
			],

		];

		return [
			'help_dialog' => [
				'title'     =>  __( 'Help dialog', 'echo-knowledge-base' ),
				'classes'   => '#eckb-help-dialog',
				'settings'  => $settings,
			]];
	}
}