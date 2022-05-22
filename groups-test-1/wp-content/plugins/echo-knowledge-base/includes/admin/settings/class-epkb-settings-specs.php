<?php

/**
 * Handles settings specifications.
 */
class EPKB_Settings_Specs {

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

			// Setup
			'help_dialog_enable'                              => array(
				'label'       => __( 'Help Dialog', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_enable',
				'type'        => EPKB_Input_Filter::CHECKBOX,
				'default'     => 'on'
			),
			'help_dialog_display_mode'                              => array(
				'label'       => __( 'Display Mode', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_display_mode',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'faqs' => 'Show FAQs',
					'contact' => 'Show Contact Us',
					'both' => 'Show FAQs & Contact Us'
				),
				'default'     => 'both'
			),
			'help_dialog_logo_image_url'                           => array(
				'label'       => __( 'Logo Image URL', 'echo-advanced-search' ),
				'name'        => 'help_dialog_logo_image_url',
				'size'        => '60',
				'max'         => '300',
				'min'         => '0',
				'mandatory'    => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => Echo_Knowledge_Base::$plugin_url . 'img/kb-icon.png'
			),

			// Launcher
			'help_dialog_launcher_when_to_display'                 => array(
				'label'       => __( 'When to display Help Dialog?', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_when_to_display',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => array(
					'right_away' => 'Right Away',
					'after_delay' => 'After Delay'
				),
				'default'     => 'right_away'
			),
			'help_dialog_launcher_background_color'                => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#fc5d7d"
			),
			'help_dialog_launcher_background_hover_color'          => array(
				'label'       => __( 'Background Hover', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_launcher_background_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#459fed"
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

			// Help Dialog
			'help_dialog_background_color'                         => array(
				'label'       => __( 'Background', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_background_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#fc5d7d"
			),
			'help_dialog_text_color'                               => array(
				'label'       => __( 'Text Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_text_hover_color'                         => array(
				'label'       => __( 'Text Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_text_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#666666"
			),
			'help_dialog_back_icon_color'                          => array(
				'label'       => __( 'Icon Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_back_icon_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#5dc2fc"
			),
			'help_dialog_back_icon_color_hover_color'              => array(
				'label'       => __( 'Icon Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_back_icon_color_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_back_icon_bg_color'                       => array(
				'label'       => __( 'Background Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_back_icon_bg_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'help_dialog_back_icon_bg_color_hover_color'           => array(
				'label'       => __( 'Background Color Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_text_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),

			// FAQs
			'help_dialog_faqs_kb'                                  => array(
				'label'       => __( 'Knowledge Base for FAQs', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_kb',
				'type'        => EPKB_Input_Filter::SELECTION,
				'options'     => self::get_kb_list(),
				'default'     => '1'
			),
			'help_dialog_faqs_title'                               => array(
				'label'       => __( 'Title', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_title',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'FAQs', 'echo-knowledge-base' )
			),
			'help_dialog_faqs_top_button_title'                    => array(
				'label'       => __( 'Top Button Title', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_top_button_title',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'FAQs', 'echo-knowledge-base' )
			),
			'help_dialog_faqs_search_placeholder'                  => array(
				'label'       => __( 'Search Placeholder', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_search_placeholder',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Search For Answers', 'echo-knowledge-base' )
			),
			'help_dialog_faqs_category_ids'                        => array(
				'label'       => __( 'List of Category IDs', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_category_ids',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => ''
			),
			'help_dialog_faqs_read_more_text'                      => array(
				'label'       => __( 'Read More Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_read_more_text',
				'size'        => '30',
				'max'         => '100',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Read Full Article', 'echo-knowledge-base' )
			),
			'help_dialog_faqs_read_more_text_color'                => array(
				'label'       => __( 'Read More Text Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_read_more_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_faqs_read_more_text_hover_color'          => array(
				'label'       => __( 'Read More Text Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_faqs_read_more_text_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#666666"
			),
			'help_dialog_top_button_color'                        => array(
				'label'       => __( 'Button Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_top_button_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#5dc2fc"
			),
			'help_dialog_top_button_hover_color'                  => array(
				'label'       => __( 'Button Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_top_button_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_top_button_text_color'                   => array(
				'label'       => __( 'Button Text Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_top_button_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'help_dialog_top_button_text_hover_color'             => array(
				'label'       => __( 'Button Text Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_top_button_text_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),

			// Contact Us
			'help_dialog_contact_title'                            => array(
				'label'       => __( 'Title', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_title',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Contact Us', 'echo-knowledge-base' )
			),
			'help_dialog_contact_top_button_title'                 => array(
				'label'       => __( 'Top Button Title', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_top_button_title',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Contact Us', 'echo-knowledge-base' )
			),
			'help_dialog_contact_submission_email'                 => array(
				'label'       => __( 'Email To Receive Contact Form Submissions', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_submission_email',
				'size'        => '30',
				'max'         => '50',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => ''
			),
			'help_dialog_contact_success_message'                  => array(
				'label'       => __( 'Email Sent Success Message', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_success_message',
				'size'        => '30',
				'max'         => '100',
				'min'         => '0',
				'mandatory'   => false,
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Thank you. We will get back to you soon.', 'echo-knowledge-base' )
			),
			'help_dialog_contact_name_placeholder'                 => array(
				'label'       => __( 'Name Placeholder', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_name_placeholder',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Name', 'echo-knowledge-base' )
			),
			'help_dialog_contact_user_email_placeholder'           => array(
				'label'       => __( 'Email Placeholder', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_user_email_placeholder',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Email', 'echo-knowledge-base' )
			),
			'help_dialog_contact_subject_placeholder'              => array(
				'label'       => __( 'Subject Placeholder', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_subject_placeholder',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Subject', 'echo-knowledge-base' )
			),
			'help_dialog_contact_comment_placeholder'              => array(
				'label'       => __( 'Comment Placeholder', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_comment_placeholder',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Comments', 'echo-knowledge-base' )
			),
			'help_dialog_contact_button_title'                     => array(
				'label'       => __( 'Submit Button Text', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_button_title',
				'size'        => '30',
				'max'         => '50',
				'min'         => '1',
				'type'        => EPKB_Input_Filter::TEXT,
				'default'     => __( 'Submit', 'echo-knowledge-base' )
			),
			'help_dialog_contact_button_color'                     => array(
				'label'       => __( 'Button Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_button_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#5dc2fc"
			),
			'help_dialog_contact_button_hover_color'               => array(
				'label'       => __( 'Button Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_button_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#000000"
			),
			'help_dialog_contact_button_text_color'                => array(
				'label'       => __( 'Button Text Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_text_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
			'help_dialog_contact_button_text_hover_color'          => array(
				'label'       => __( 'Button Text Hover Color', 'echo-knowledge-base' ),
				'name'        => 'help_dialog_contact_text_hover_color',
				'size'        => '10',
				'max'         => '7',
				'min'         => '7',
				'type'        => EPKB_Input_Filter::COLOR_HEX,
				'default'     => "#ffffff"
			),
		);

		return apply_filters( 'epkb_settings_specs', $plugin_settings );
	}

	private static function get_kb_list() {
		$all_kb_configs = epkb_get_instance()->kb_config_obj->get_kb_configs();
		$kb_id_options = array();
		foreach ( $all_kb_configs as $one_kb_config ) {
			if ( $one_kb_config['id'] !== EPKB_KB_Config_DB::DEFAULT_KB_ID && EPKB_Utilities::is_kb_archived( $one_kb_config['status'] ) ) {
				continue;
			}
			$kb_id_options[$one_kb_config['id']] = esc_html( $one_kb_config[ 'kb_name' ] );
		}
		return $kb_id_options;
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