<?php

/**
 * Handles settings specifications.
 */
class EPKB_Help_Dialog_Settings_Specs {

	/**
	 * Defines data needed for display, initialization and validation/sanitation of settings
	 *
	 * ALL FIELDS ARE MANDATORY by default ( otherwise use 'mandatory' => false )
	 *
	 * @return array with settings specification
	 */
	public static function get_fields_specification() {

		// all default settings are listed here
		$plugin_settings = array(

			/******************************************************************************
			 *
			 *  Setup
			 *
			 ******************************************************************************/
			'help_dialog_enable'                                    => array(
				'label'       => __( 'Help Dialog', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'off'
			),
			'help_dialog_faqs_kb'                                   => array(
				'label'       => __( 'Knowledge Base for FAQs', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_kb',
				'type'        => EPKB_Input_Filter::SELECTION,
				// do not automatically populate options here. do it in UI
				'default'     => '1'
			),
			'help_dialog_display_mode'                              => array(
				'label'       => __( 'Display Mode', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_display_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'faqs'      => 'Show FAQs',
					'contact'   => 'Show Contact Us',
					'both'      => 'Show FAQs and Contact Us'
				),
				'default'     => 'both'
			),
			'help_dialog_logo_image_url'                            => array(
				'label'       => __( 'Logo Image URL', 'echo-advanced-search' ),
				'name'        => 'help_dialog_logo_image_url',
				'size'        => '60',
				'max'         => '300',
				'min'         => '0',
				'mandatory'    => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png'
			),
			
			'help_dialog_welcome_text'                              => array(
				'label'       => __( 'Logo Welcome Text', 'echo-advanced-search' ),
				'name'        => 'help_dialog_welcome_text',
				'size'        => '30',
				'max'         => '70',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Welcome to Support', 'echo-knowledge-base' )
			),

			/*'help_dialog_container_desktop_height'                  => array(
				'label'       => __( 'Max Height (px)', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_container_desktop_height',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '400'
			),*/

			'help_dialog_container_desktop_width'                   => array(
				'label'       => __( 'Desktop Width (px)', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_container_desktop_width',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '400'
			),
			'help_dialog_container_tablet_width'                    => array(
				'label'       => __( 'Tablet Width (px)', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_container_tablet_width',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '400'
			),
			'help_dialog_container_mobile_width'                    => array(
				'label'       => __( 'Mobile Width (px)', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_container_mobile_width',
				'max'         => '1000',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '400'
			),
			'help_dialog_tablet_break_point'                        => array(
				'label'       => __( 'Tablet (px)', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_tablet_break_point',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 1025
			),
			'help_dialog_mobile_break_point'                        => array(
				'label'       => __( 'Mobile (px)', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_mobile_break_point',
				'max'         => 2000,
				'min'         => 100,
				'type'        => EPKB_Input_Filter::NUMBER,
				'style'       => 'small',
				'default'     => 768
			),

			// - Top buttons
			'help_dialog_back_text_color'                          => array(
				'label'       => __( 'Text/Icon Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_back_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#7d7d7d"
			),
			'help_dialog_back_text_color_hover_color'              => array(
				'label'       => __( 'Text/Icon Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_back_text_color_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_back_background_color'                       => array(
				'label'       => __( 'Background Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_back_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#f0f0f0"
			),
			'help_dialog_back_background_color_hover_color'           => array(
				'label'       => __( 'Background Color Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_back_background_color_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#f0f0f0"
			),


			/******************************************************************************
			 *
			 *  Launcher
			 *
			 ******************************************************************************/
			'help_dialog_launcher_start_delay'                      => array(
				'label'       => __( 'Delay Displaying Help Dialog Launcher', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_start_delay',
				'max'         => '500',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::NUMBER,
				'default'     => '0'
			),
			'help_dialog_launcher_background_color'                => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#7b00a6"
			),
			'help_dialog_launcher_background_hover_color'          => array(
				'label'       => __( 'Background Hover', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_background_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#a5a5a5"
			),
			'help_dialog_launcher_icon_color'                      => array(
				'label'       => __( 'Icon', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'help_dialog_launcher_icon_hover_color'                => array(
				'label'       => __( 'Icon Hover', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_icon_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),


			/******************************************************************************
			 *
			 *  FAQ List Tab
			 *
			 ******************************************************************************/
			'help_dialog_faqs_top_tab'                              => array(
				'label'       => __( 'FAQs Tab Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_top_tab',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'FAQs', 'echo-knowledge-base' )
			),
			'help_dialog_faqs_title'                                => array(
				'label'       => __( 'FAQ Title', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_title',
				'size'        => '30',
				'max'         => '70',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'How can we help you?', 'echo-knowledge-base' )
			),
			'help_dialog_faqs_search_placeholder'                   => array(
				'label'       => __( 'Search Placeholder', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_search_placeholder',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search for help', 'echo-knowledge-base' )
			),
			'help_dialog_article_read_more_text'                    => array(
				'label'       => __( 'Articles Read More Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_article_read_more_text',
				'size'        => '30',
				'max'         => '100',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Read More', 'echo-knowledge-base' )
			),
			'help_dialog_background_color'                          => array(
				'label'       => __( 'Main Background / Active Tab', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#aa2dd6"
			),
			'help_dialog_not_active_tab'                            => array(
				'label'       => __( 'Not Active Tab', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_not_active_tab',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#6d3687"
			),
			'help_dialog_tab_text_color'                            => array(
				'label'       => __( 'Tab text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_tab_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'help_dialog_main_title_text_color'                     => array(
				'label'       => __( 'Main Title', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_main_title_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#FFFFFF"
			),
			'help_dialog_welcome_text_color'                        => array(
				'label'       => __( 'Welcome Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_welcome_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_welcome_background_color'                  => array(
				'label'       => __( 'Welcome Text Background Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_welcome_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#f6deff"
			),
			'help_dialog_breadcrumb_arrow_color'                    => array(
				'label'       => __( 'Breadcrumb Arrow', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_breadcrumb_arrow_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_faqs_qa_border_color'                      => array(
				'label'       => __( 'Question Border', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_qa_border_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#CCCCCC"
			),
			'help_dialog_faqs_question_text_color'                  => array(
				'label'       => __( 'Question Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_question_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_faqs_question_background_color'            => array(
				'label'       => __( 'Question Background', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_question_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#f7f7f7"
			),
			'help_dialog_faqs_question_active_text_color'           => array(
				'label'       => __( 'Question Active text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_question_active_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_faqs_question_active_background_color'     => array(
				'label'       => __( 'Question Active Background', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_question_active_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'help_dialog_faqs_answer_text_color'                    => array(
				'label'       => __( 'Answer Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_answer_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_faqs_answer_background_color'              => array(
				'label'       => __( 'Answer Background', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_answer_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),

			// - Search Results
			'help_dialog_breadcrumb_home_text'                      => array(
				'label'       => __( 'Breadcrumb - Home', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_breadcrumb_home_text',
				'size'        => '30',
				'max'         => '20',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Home', 'echo-knowledge-base' )
			),
			'help_dialog_breadcrumb_search_result_text'                      => array(
				'label'       => __( 'Breadcrumb - Search Results', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_breadcrumb_search_result_text',
				'size'        => '30',
				'max'         => '20',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search Results', 'echo-knowledge-base' )
			),
			'help_dialog_breadcrumb_article_text'                      => array(
				'label'       => __( 'Breadcrumb - Article', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_breadcrumb_article_text',
				'size'        => '30',
				'max'         => '20',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Article', 'echo-knowledge-base' )
			),
			'help_dialog_found_faqs_tab_text'                       => array(
				'label'       => __( 'Found FAQs Tab', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_found_faqs_tab_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Found FAQs', 'echo-knowledge-base' )
			),
			'help_dialog_fount_articles_tab_text'                   => array(
				'label'       => __( 'Found Articles Tab', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_fount_articles_tab_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Found Articles', 'echo-knowledge-base' )
			),
			'help_dialog_found_faqs_article_tab_color'              => array(
				'label'       => __( 'Tab Colors', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_found_faqs_article_tab_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_found_faqs_article_active_tab_color'       => array(
				'label'       => __( 'Active Tab', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_found_faqs_article_active_tab_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#0f9beb"
			),

			// - Single Article
			'help_dialog_single_article_title_color'                => array(
				'label'       => __( 'Article Title Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_single_article_title_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_single_article_desc_color'                 => array(
				'label'       => __( 'Description Text Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_single_article_desc_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#424242"
			),
			'help_dialog_single_article_read_more_text_color'       => array(
				'label'       => __( 'Read More Text Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_single_article_read_more_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#0f9beb"
			),
			'help_dialog_single_article_read_more_text_hover_color' => array(
				'label'       => __( 'Read More Text Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_single_article_read_more_text_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#007eed"
			),

			/******************************************************************************
			 *
			 *  Contact Us Tab
			 *
			 ******************************************************************************/
			'help_dialog_contact_us_top_tab'                        => array(
				'label'       => __( 'Contact US Tab Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_us_top_tab',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Contact Us', 'echo-knowledge-base' )
			),
			'help_dialog_contact_title'                             => array(
				'label'       => __( 'Contact Us Title', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_title',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Get in Touch', 'echo-knowledge-base' )
			),
			'help_dialog_contact_name_text'                         => array(
				'label'       => __( 'Name Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_name_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '0',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Name', 'echo-knowledge-base' )
			),
			'help_dialog_contact_user_email_text'                   => array(
				'label'       => __( 'Email Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_user_email_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Email', 'echo-knowledge-base' )
			),
			'help_dialog_contact_subject_text'                      => array(
				'label'       => __( 'Subject Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_subject_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Subject', 'echo-knowledge-base' )
			),
			'help_dialog_contact_comment_text'                      => array(
				'label'       => __( 'Comment Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_comment_text',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'How can we help you?', 'echo-knowledge-base' )
			),
			'help_dialog_contact_button_title'                      => array(
				'label'       => __( 'Submit Button Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_button_title',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Submit', 'echo-knowledge-base' )
			),
			'help_dialog_contact_submission_email'                  => array(
				'label'       => __( 'Email To Receive Contact Form Submissions', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_submission_email',
				'size'        => '30',
				'max'         => '50',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::EMAIL,
				'default'     => ''
			),
			'help_dialog_contact_success_message'                   => array(
				'label'       => __( 'Email Sent Success Message', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_success_message',
				'size'        => '30',
				'max'         => '100',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' )
			),
			'help_dialog_contact_submit_button_color'               => array(
				'label'       => __( 'Submit Button Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_submit_button_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#aa2dd6"
			),
			'help_dialog_contact_submit_button_hover_color'         => array(
				'label'       => __( 'Submit Button Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_submit_button_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#9039af"
			),
			'help_dialog_contact_submit_button_text_color'          => array(
				'label'       => __( 'Submit Button Text Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_submit_button_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'help_dialog_contact_submit_button_text_hover_color'    => array(
				'label'       => __( 'Submit Button Text Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_submit_button_text_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
		);

		return $plugin_settings;
	}

	/**
	 * Get Plugin default configuration
	 *
	 * @return array contains default setting values
	 */
	public static function get_default_settings() {
		$setting_specs = self::get_fields_specification();

		$default_configuration = array();
		foreach( $setting_specs as $key => $spec ) {
			$default = isset($spec['default']) ? $spec['default'] : '';
			$default_configuration += array( $key => $default );
		}

		return $default_configuration;
	}

	/**
	 * Get names of all configuration items for Plugin settings
	 * @return array
	 */
	public static function get_specs_item_names() {
		return array_keys( self::get_fields_specification() );
	}

	/**
	 * Get names of all configuration items for Plugin settings
	 * @return array
	 */
	public static function get_specs_item_name_keys() {
		$keys = array();
		foreach ( self::get_default_settings() as $key => $spec ) {
			$keys[$key] = '';
		}
		return $keys;
	}
}