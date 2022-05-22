<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Store Wizard theme data
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_KB_Wizard_Themes {

	/**
	 * Retreive themes-specific configuration for core and add-ons
	 *
	 * @param $kb_config - KB and add-ons configuration
	 * @return array
	 */
	public static function get_all_presets( $kb_config ) {

		$themes = self::get_themes_array();

		// add here all Wizard theme options that should be translated
		$translate_fields = array(
			'kb_name',
			'search_title',
			'article_search_title'
		);

		// add translations
		foreach ( $themes as $theme_name => $theme ) {
			foreach ( $translate_fields as $field ) {
				if ( isset($theme[$field]) && isset($themes[$theme_name][$field]) ) {
					$themes[$theme_name][$field] = __( $theme[$field], 'echo-knowledge-base' );
				}
			}
		}

		// populate KB Config with the theme
		foreach ( $themes as $theme_name => $theme ) {
			$Kb_theme_config = array_merge( $kb_config, $theme );
			// copy Main Page search settings to Article Page
			$themes[$theme_name] = self::copy_search_mp_to_ap( $Kb_theme_config );
		}

		return $themes;
	}

	/**
	 * Return specific theme configuration + all other core and add-ons configuration so we can display preview
	 *
	 * @param $theme_name
	 * @param $kb_config
	 *
	 * @return array
	 */
	public static function get_theme( $theme_name, $kb_config ) {
		$themes = self::get_all_presets( $kb_config );
		$theme_config = empty($themes[ $theme_name]) ? $themes['standard'] : $themes[ $theme_name];
		return $theme_config;
	}

	public static function get_theme_layout( $theme_name ) {

		foreach ( self::$themes_compacted['theme_name'] as $preset_seq_id => $preset_name ) {
			if ( $theme_name == $preset_name && ! empty( self::$themes_compacted['kb_main_page_layout'][$preset_seq_id] ) ) {
				return self::$themes_compacted['kb_main_page_layout'][$preset_seq_id];
			}
		}

		return 'Basic';
	}

	private static $themes_compacted = [

		// Setup
		'theme_name' => [1=>'standard', 2=>'elegant', 3=>'modern', 4=>'image', 5=>'informative', 6=>'formal', 7=>'bright', 8=>'disctinct', 9=>'basic', 10=>'organized', 11=>'organized_2', 12=>'products_based', 13=>'clean', 14=>'standard_2', 15=>'icon_focused', 16=>'business', 17=>'minimalistic'],
		'kb_name' => [1=>'Standard', 2=>'Elegant', 3=>'Modern', 4=>'Image', 5=>'Informative', 6=>'Formal', 7=>'Bright', 8=>'Distinct', 9=>'Basic', 10=>'Organized', 11=>'Organized 2', 12=>'Product Based', 13=>'Clean', 14=>'Standard', 15=>'Icon Focused', 16=>'Business', 17=>'Minimalistic'],
		'kb_main_page_layout' => [1=>'Basic', 2=>'Basic', 3=>'Basic', 4=>'Basic', 5=>'Basic', 6=>'Basic', 7=>'Basic', 8=>'Basic', 9=>'Tabs', 10=>'Tabs', 11=>'Tabs', 12=>'Tabs', 13=>'Tabs', 14=>'Categories', 15=>'Categories', 16=>'Categories', 17=>'Categories'],

		// General
		'width' => [1=>'', 2=>'epkb-boxed', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'epkb-boxed', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'epkb-boxed', 16=>'epkb-boxed', 17=>'epkb-boxed'],
		'nof_columns' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'two-col', 14=>'', 15=>'', 16=>'', 17=>'',],
		'expand_articles_icon' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'ep_font_icon_right_arrow', 7=>'ep_font_icon_right_arrow', 8=>'ep_font_icon_plus_box', 9=>'', 10=>'ep_font_icon_plus_box', 11=>'', 12=>'ep_font_icon_right_arrow', 13=>'ep_font_icon_right_arrow', 14=>'ep_font_icon_right_arrow', 15=>'ep_font_icon_folder_add', 16=>'ep_font_icon_folder_add', 17=>'ep_font_icon_folder_add'],

		// Search
		'search_background_color' => [1=>'#f7941d', 2=>'#c9418e', 3=>'#b1d5e1', 4=>'#B1D5E1', 5=>'#904e95', 6=>'#edf2f6', 7=>'#FFFFFF', 8=>'#f4f8ff', 9=>'#dd9933', 10=>'#8c1515', 11=>'#43596e', 12=>'#6e6767', 13=>'#f2f2f2', 14=>'#1e73be', 15=>'#fcfcfc', 16=>'#fbfbfb', 17=>'#fbfbfb',],
		'search_box_input_width' => [1=>'', 2=>'', 3=>40, 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'40', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'40', 15=>'', 16=>'', 17=>'',],
		'search_box_margin_bottom' => [1=>'', 2=>'0', 3=>40, 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'23', 10=>'', 11=>'23', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'0', 17=>'0',],
		'search_box_padding_bottom' => [1=>'', 2=>'', 3=>40, 4=>'', 5=>'50', 6=>'', 7=>'', 8=>30, 9=>'', 10=>'', 11=>'50', 12=>'60', 13=>'', 14=>'50', 15=>'', 16=>'90', 17=>'90',],
		'search_box_padding_top' => [1=>'', 2=>'', 3=>40, 4=>'', 5=>'50', 6=>'', 7=>'', 8=>30, 9=>'', 10=>'', 11=>'50', 12=>'60', 13=>'', 14=>'20', 15=>'', 16=>'', 17=>'',],
		'search_btn_background_color' => [1=>'#40474f', 2=>'#40474f', 3=>'#686868', 4=>'#686868', 5=>'#686868', 6=>'#666666', 7=>'#f4c60c', 8=>'#bf25ff', 9=>'#636567', 10=>'#878787', 11=>'#40474f', 12=>'#686868', 13=>'#000000', 14=>'#757069', 15=>'#f4c60c', 16=>'#40474f', 17=>'#6fb24c',],
		'search_btn_border_color' => [1=>'#F1F1F1', 2=>'#F1F1F1', 3=>'#f1f1f1', 4=>'#F1F1F1', 5=>'#F1F1F1', 6=>'#666666', 7=>'#f4c60c', 8=>'#bf25ff', 9=>'#636567', 10=>'#000000', 11=>'#F1F1F1', 12=>'#F1F1F1', 13=>'#000000', 14=>'#000000', 15=>'#0bcad9', 16=>'#F1F1F1', 17=>'#6fb24c',],
		'search_input_border_width' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>3, 8=>'', 9=>'0', 10=>'', 11=>'', 12=>'5', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'search_layout' => [1=>'', 2=>'', 3=>'epkb-search-form-1', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'epkb-search-form-3', 9=>'epkb-search-form-3', 10=>'', 11=>'epkb-search-form-3', 12=>'epkb-search-form-3', 13=>'epkb-search-form-1', 14=>'', 15=>'', 16=>'', 17=>'',],
		'search_text_input_background_color' => [1=>'#FFFFFF', 2=>'#FFFFFF', 3=>'#FFFFFF', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#FFFFFF', 10=>'', 11=>'#FFFFFF', 12=>'', 13=>'', 14=>'#FFFFFF', 15=>'#FFFFFF', 16=>'#FFFFFF', 17=>'#FFFFFF',],
		'search_text_input_border_color' => [1=>'#CCCCCC', 2=>'#CCCCCC', 3=>'#CCCCCC', 4=>'#CCCCCC', 5=>'#CCCCCC', 6=>'#d1d1d1', 7=>'#f4c60c', 8=>'#bf25ff', 9=>'#636567', 10=>'#000000', 11=>'#CCCCCC', 12=>'#000000', 13=>'#000000', 14=>'#000000', 15=>'#0bcad9', 16=>'#CCCCCC', 17=>'#6fb24c',],
		'search_title_font_color' => [1=>'#FFFFFF', 2=>'#FFFFFF', 3=>'#FFFFFF', 4=>'#ffffff', 5=>'#ffffff', 6=>'#000000', 7=>'#f4c60c', 8=>'#528ffe', 9=>'#FFFFFF', 10=>'#ffffff', 11=>'#e69e4a', 12=>'', 13=>'#000000', 14=>'#FFFFFF', 15=>'#fcfcfc', 16=>'#000000', 17=>'#6fb24c',],

		// Section
		'section_article_underline' => [1=>'', 2=>'on', 3=>'on', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'on', 10=>'', 11=>'on', 12=>'', 13=>'', 14=>'on', 15=>'', 16=>'', 17=>'',],
		'section_body_background_color' => [1=>'#FFFFFF', 2=>'#ffffff', 3=>'#FFFFFF', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#ffffff', 10=>'', 11=>'#ffffff', 12=>'#f9f9f9', 13=>'#ffffff', 14=>'#FFFFFF', 15=>'#FFFFFF', 16=>'#FEFEFE', 17=>'#FEFEFE',],
		'section_body_height' => [1=>'', 2=>'200', 3=>150, 4=>150, 5=>150, 6=>150, 7=>150, 8=>150, 9=>'200', 10=>150, 11=>'200', 12=>'', 13=>'', 14=>'150', 15=>150, 16=>130, 17=>130,],
		'section_body_padding_bottom' => [1=>'', 2=>'4', 3=>4, 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'4', 10=>'', 11=>'4', 12=>'', 13=>'', 14=>'4', 15=>'', 16=>'', 17=>'',],
		'section_body_padding_left' => [1=>'', 2=>'10', 3=>30, 4=>30, 5=>30, 6=>10, 7=>10, 8=>10, 9=>'10', 10=>'22', 11=>'10', 12=>'22', 13=>'22', 14=>'22', 15=>'22', 16=>'22', 17=>'22',],
		'section_body_padding_right' => [1=>'', 2=>'10', 3=>10, 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'10', 10=>'4', 11=>'10', 12=>'4', 13=>'4', 14=>'4', 15=>'4', 16=>'4', 17=>'4',],
		'section_body_padding_top' => [1=>'', 2=>'4', 3=>5, 4=>'5', 5=>'5', 6=>'5', 7=>'5', 8=>'5', 9=>'4', 10=>'', 11=>'4', 12=>'', 13=>'', 14=>'4', 15=>'', 16=>'', 17=>'',],
		'section_border_color' => [1=>'#F7F7F7', 2=>'#f7f7f7', 3=>'#DBDBDB', 4=>'#DBDBDB', 5=>'#DBDBDB', 6=>'#DBDBDB', 7=>'#DBDBDB', 8=>'#528ffe', 9=>'#e0e0e0', 10=>'#bababa', 11=>'#f7f7f7', 12=>'', 13=>'', 14=>'#F7F7F7', 15=>'#F7F7F7', 16=>'#CACACE', 17=>'#CACACE',],
		'section_border_radius' => [1=>'', 2=>'4', 3=>4, 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'4', 10=>'', 11=>'4', 12=>'', 13=>'', 14=>'4', 15=>'', 16=>'', 17=>'',],
		'section_border_width' => [1=>'', 2=>0, 3=>0, 4=>'0', 5=>'0', 6=>'0', 7=>'0', 8=>'0', 9=>0, 10=>'1', 11=>0, 12=>'1', 13=>'1', 14=>'1', 15=>'1', 16=>'1', 17=>'1',],
		'section_box_shadow' => [1=>'', 2=>'', 3=>'section_light_shadow', 4=>'section_light_shadow', 5=>'section_light_shadow', 6=>'', 7=>'', 8=>'section_medium_shadow', 9=>'section_light_shadow', 10=>'section_light_shadow', 11=>'', 12=>'', 13=>'', 14=>'section_medium_shadow', 15=>'section_light_shadow', 16=>'section_light_shadow', 17=>'',],
		'section_category_font_color' => [1=>'#40474f', 2=>'#40474f', 3=>'#868686', 4=>'#868686', 5=>'#868686', 6=>'#868686', 7=>'#868686', 8=>'#868686', 9=>'#40474f', 10=>'#868686', 11=>'#40474f', 12=>'#000000', 13=>'#000000', 14=>'#40474f', 15=>'#40474f', 16=>'#40474f', 17=>'#40474f',],
		'section_category_icon_color' => [1=>'#f7941d', 2=>'#ca428f', 3=>'#868686', 4=>'#868686', 5=>'#868686', 6=>'#e3474b', 7=>'#dddddd', 8=>'#528ffe', 9=>'#ca428f', 10=>'#8c1515', 11=>'#ca428f', 12=>'#00b4b3', 13=>'#00b4b3', 14=>'#1e73be', 15=>'#2991a3', 16=>'#eb5a46', 17=>'#6fb24c',],
		'section_divider' => [1=>'', 2=>'', 3=>'off', 4=>'off', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'off', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'section_divider_color' => [1=>'#edf2f6', 2=>'#edf2f6', 3=>'', 4=>'', 5=>'#DADADA', 6=>'#edf2f6', 7=>'#edf2f6', 8=>'#528ffe', 9=>'#edf2f6', 10=>'#CDCDCD', 11=>'#edf2f6', 12=>'#1e73be', 13=>'#888888', 14=>'#1e73be', 15=>'#0bcad9', 16=>'#FFFFFF', 17=>'#FFFFFF',],
		'section_divider_thickness' => [1=>'', 2=>'5', 3=>0, 4=>'0', 5=>'0', 6=>'2', 7=>'2', 8=>'2', 9=>'5', 10=>'1', 11=>'5', 12=>'2', 13=>'2', 14=>'2', 15=>'2', 16=>'1', 17=>'1',],
		'section_typography' => [1=>'', 2=>'', 3=>['font-size' => '14'], 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>['font-size' => '12'], 11=>['font-size' => '14'], 12=>'', 13=>'', 14=>'', 15=>['font-size' => '12'], 16=>['font-size' => '12'], 17=>['font-size' => '12'],],
		'section_head_alignment' => [1=>'', 2=>'left', 3=>'center', 4=>'center', 5=>'center', 6=>'left', 7=>'left', 8=>'center', 9=>'left', 10=>'center',  11=>'left', 12=>'center', 13=>'center', 14=>'center', 15=>'center', 16=>'left', 17=>'left'],
		'section_head_background_color' => [1=>'#FFFFFF', 2=>'#f9f9f9', 3=>'#FFFFFF', 4=>'', 5=>'', 6=>'', 7=>'#fcfcfc', 8=>'', 9=>'#ffffff', 10=>'#eeeeee', 11=>'#b1d5e1', 12=>'#6e6767', 13=>'#ffffff', 14=>'#fcfcfc', 15=>'#fcfcfc', 16=>'#FFFFFF', 17=>'#FFFFFF',],
		'section_head_category_icon_color' => [1=>'#f7941d', 2=>'#ca428f', 3=>'#904e95', 4=>'#904e95', 5=>'#904e95', 6=>'#e3474b', 7=>'#f4c60c', 8=>'#bf25ff', 9=>'#ca428f', 10=>'#8c1515', 11=>'#ca428f', 12=>'#868686', 13=>'#868686', 14=>'#1e73be', 15=>'#f4c60c', 16=>'#eb5a46', 17=>'#4EB3C4',],
		'section_head_category_icon_location' => [1=>'', 2=>'left', 3=>'top', 4=>'top', 5=>'top', 6=>'left', 7=>'left', 8=>'left', 9=>'left', 10=>'top', 11=>'left', 12=>'no_icons', 13=>'no_icons', 14=>'top', 15=>'top', 16=>'left', 17=>'no_icons',],
		'section_head_category_icon_size' => [1=>'', 2=>'57', 3=>121, 4=>219, 5=>50, 6=>25, 7=>25, 8=>25, 9=>'87', 10=>'', 11=>'57', 12=>'', 13=>'', 14=>'40', 15=>'40', 16=>'30', 17=>'30',],
		'section_head_description_font_color' => [1=>'#b3b3b3', 2=>'#b3b3b3', 3=>'#b3b3b3', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#b3b3b3', 10=>'', 11=>'#b3b3b3', 12=>'#828282', 13=>'#828282', 14=>'#b3b3b3', 15=>'#b3b3b3', 16=>'#b3b3b3', 17=>'#b3b3b3',],
		'section_head_font_color' => [1=>'#40474f', 2=>'#40474f', 3=>'#827a74', 4=>'#827a74', 5=>'#827a74', 6=>'#e3474b', 7=>'#0b6ea0', 8=>'#528ffe', 9=>'#000000', 10=>'#000000', 11=>'#40474f', 12=>'#ffffff', 13=>'#000000', 14=>'#666666', 15=>'#666666', 16=>'#000000', 17=>'#6fb24c',],
		'section_head_padding_bottom' => [1=>'', 2=>'20', 3=>0, 4=>'0', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'20', 10=>'10', 11=>'20', 12=>'', 13=>'', 14=>'20', 15=>20, 16=>20, 17=>20,],
		'section_head_padding_left' => [1=>'', 2=>'4', 3=>0, 4=>'0', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'4', 10=>'', 11=>'4', 12=>30, 13=>30, 14=>'20', 15=>20, 16=>20, 17=>20,],
		'section_head_padding_right' => [1=>'', 2=>'4', 3=>0, 4=>'0', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'4', 10=>'', 11=>'4', 12=>'', 13=>'', 14=>'20', 15=>20, 16=>20, 17=>20,],
		'section_head_padding_top' => [1=>'', 2=>'20', 3=>0, 4=>'0', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'20', 10=>'10', 11=>'20', 12=>'', 13=>'', 14=>'20', 15=>20, 16=>20, 17=>20,],

		// Tabs
		'tab_down_pointer' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'on', 10=>'on', 11=>'on', 12=>'', 13=>'', 14=>'', 15=>'on', 16=>'on', 17=>'on',],
		'tab_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>[ 'font-size' => '14'], 10=>'', 11=>[ 'font-size' => '14'], 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'tab_nav_active_background_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#f7f7f7', 10=>'#F1F1F1', 11=>'#43596e', 12=>'#6e6767', 13=>'#ffffff', 14=>'', 15=>'#F1F1F1', 16=>'#F1F1F1', 17=>'#F1F1F1',],
		'tab_nav_active_font_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#3a3a3a', 10=>'#8c1515', 11=>'#e69e4a', 12=>'#ffffff', 13=>'#000000', 14=>'', 15=>'#8c1515', 16=>'#8c1515', 17=>'#8c1515'],
		'tab_nav_background_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#ffffff', 10=>'', 11=>'#f7f7f7', 12=>'#f7f7f7', 13=>'#ffffff', 14=>'', 15=>'', 16=>'', 17=>''],
		'tab_nav_border_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#f7941d', 10=>'#000000', 11=>'#686868', 12=>'#1e73be', 13=>'#888888', 14=>'', 15=>'#000000', 16=>'#000000', 17=>'#000000'],
		'tab_nav_font_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'#000000', 10=>'#686868', 11=>'#e69e4a', 12=>'#686868', 13=>'#adadad', 14=>'', 15=>'#686868', 16=>'#686868', 17=>'#686868'],

		// Articles
		'article_font_color' => [1=>'#459fed', 2=>'#459fed', 3=>'#606060', 4=>'#606060', 5=>'#606060', 6=>'#616161', 7=>'#0bcad9', 8=>'#566e8b', 9=>'#000000', 10=>'#8c1515', 11=>'#000000', 12=>'#000000', 13=>'#000000', 14=>'#1e73be', 15=>'#0bcad9', 16=>'#666666', 17=>'#666666'],
		'article_icon_color' => [1=>'#b3b3b3', 2=>'#b3b3b3', 3=>'#525252', 4=>'#525252', 5=>'#904e95', 6=>'#e3474b', 7=>'#1e1e1e', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'#00b4b3', 12=>'#1e73be', 13=>'#adadad', 14=>'#000000', 15=>'#2991a3', 16=>'#e8a298', 17=>'#6fb24c'],
		'article_list_spacing' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'8', 10=>'4', 11=>'8', 12=>'4', 13=>'4', 14=>'4', 15=>'4', 16=>'4', 17=>'4'],
		'article_typography' => [1=>'', 2=>'', 3=>['font-size' => '14'], 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>['font-size' => '12'], 11=>['font-size' => '14'], 12=>'', 13=>'', 14=>'', 15=>['font-size' => '12'], 16=>['font-size' => '12'], 17=>['font-size' => '12'],],

		'breadcrumb_icon_separator' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'ep_font_icon_right_arrow', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'breadcrumb_text_color' => [1=>'', 2=>'#1e73be', 3=>'#b1d5e1', 4=>'#b1d5e1', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'#00b4b3', 12=>'#6e6767', 13=>'#1e73be', 14=>'', 15=>'#1e73be', 16=>'#1e73be', 17=>'#1e73be'],

		'back_navigation_text_color' => [1=>'', 2=>'#1e73be', 3=>'#b1d5e1', 4=>'#b1d5e1', 5=>'#ffffff', 6=>'#ffffff', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'#00b4b3', 12=>'#6e6767', 13=>'#1e73be', 14=>'', 15=>'#1e73be', 16=>'#1e73be', 17=>'#1e73be'],
		'back_navigation_bg_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'back_navigation_padding_top' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'10', 6=>'10', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'back_navigation_padding_right' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'15', 6=>'10', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'back_navigation_padding_bottom' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'10', 6=>'10', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'back_navigation_padding_left' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'15', 6=>'10', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'back_navigation_border_radius' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'1', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'back_navigation_border_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'back_navigation_border' => [1=>'', 2=>'', 3=>'none', 4=>'none', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],

		'article-meta-color' => [1=>'', 2=>'', 3=>'#b1d5e1', 4=>'#b1d5e1', 5=>'#904e95', 6=>'', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],

		'article_content_toolbar_icon_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#ffffff', 6=>'#ffffff', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_content_toolbar_text_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#ffffff', 6=>'#ffffff', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_content_toolbar_text_hover_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#ffffff', 6=>'#ffffff', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_content_toolbar_button_background' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_content_toolbar_button_background_hover' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#bc68c9', 6=>'#ea8577', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_content_toolbar_border_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],

		'article_toc_text_color' => [1=>'', 2=>'', 3=>'#b1d5e1', 4=>'#b1d5e1', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_toc_active_bg_color' => [1=>'', 2=>'', 3=>'#b1d5e1', 4=>'#b1d5e1', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_toc_title_color' => [1=>'', 2=>'', 3=>'#000000', 4=>'#000000', 5=>'#000000', 6=>'#000000', 7=>'', 8=>'#000000', 9=>'#000000', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_toc_border_color' => [1=>'', 2=>'', 3=>'#b1d5e1', 4=>'#b1d5e1', 5=>'#904e95', 6=>'#eb5a46', 7=>'', 8=>'#566e8b', 9=>'#dd9933', 10=>'', 11=>'#000000', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],

		'sidebar_article_icon_color' => [1=>'#b3b3b3', 2=>'#b3b3b3', 3=>'#525252', 4=>'#525252', 5=>'#904e95', 6=>'#e3474b', 7=>'#1e1e1e', 8=>'#566e8b', 9=>'#dd9933', 10=>'#000000', 11=>'#00b4b3', 12=>'#1e73be', 13=>'#adadad', 14=>'#000000', 15=>'#2991a3', 16=>'#e8a298', 17=>'#6fb24c'],
		'sidebar_section_head_font_color' => [1=>'', 2=>'', 3=>'#ffffff', 4=>'#ffffff', 5=>'#000000', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'sidebar_section_head_background_color' => [1=>'', 2=>'', 3=>'#90b4c4', 4=>'#90b4c4', 5=>'#ffffff', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'sidebar_section_category_font_color' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'#000000', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'sidebar_article_font_color' => [1=>'', 2=>'', 3=>'#90b4c4', 4=>'#90b4c4', 5=>'#b3b3b3', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'sidebar_article_active_font_color' => [1=>'', 2=>'', 3=>'#000000', 4=>'#000000', 5=>'#000000', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'sidebar_article_active_background_color' => [1=>'', 2=>'', 3=>'#f9f9f9', 4=>'#f9f9f9', 5=>'#f7f7f7', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>''],
		'article_nav_sidebar_type_left' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'eckb-nav-sidebar-categories', 15=>'eckb-nav-sidebar-categories', 16=>'eckb-nav-sidebar-categories', 17=>'eckb-nav-sidebar-categories'],

		// Other
		'search_title' => [1=>'', 2=>'Welcome to our Support Center', 3=>'How can we help?', 4=>'What can we help you with?', 5=>'Looking for help?', 6=>'Welcome to our Knowledge Base', 7=>'What are you looking for?', 8=>'Self Help Documentation', 9=>'Help Center', 10=>'Have a Question?',
		                   11=>'How can we help you today?', 12=>'Customer Help Portal', 13=>'Help Center', 14=>'Have a Question?', 15=>'Hey, what answers do you need?', 16=>'Knowledge Base Help Center', 17=>'Howdy! How can we help you?'],

		// Typography is reset for each preset
		'section_head_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'categories_box_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'article_search_title_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'article_toc_header_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'article_toc_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'article_title_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'back_navigation_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'breadcrumb_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'search_title_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
		'section_head_description_typography' => [1=>'', 2=>'', 3=>'', 4=>'', 5=>'', 6=>'', 7=>'', 8=>'', 9=>'', 10=>'', 11=>'', 12=>'', 13=>'', 14=>'', 15=>'', 16=>'', 17=>'',],
	];

	public static $theme_images = array(
			// Basic Layout
			'standard'            => 'setup-wizard/Basic-Layout-Standard.jpg',
			'elegant'             => 'setup-wizard/Basic-Layout-Elegant.jpg',
			'modern'              => 'setup-wizard/Basic-Layout-Modern.jpg',
			'image'               => 'setup-wizard/Basic-Layout-Image.jpg',
			'informative'         => 'setup-wizard/Basic-Layout-Informative.jpg',
			'formal'              => 'setup-wizard/Basic-Layout-Formal.jpg',
			'bright'              => 'setup-wizard/Basic-Layout-Bright.jpg',
			'disctinct'           => 'setup-wizard/Basic-Layout-Distinct.jpg',
			// Tabs Layout
			'basic'               => 'setup-wizard/Tabs-Layout-Basic.jpg',
			'organized'           => 'setup-wizard/Tabs-Layout-Organized.jpg',
			'organized_2'         => 'setup-wizard/Tabs-Layout-Organized-2.jpg',
			'products_based'      => 'setup-wizard/Tabs-Layout-Product-Based.jpg',
			'clean'               => 'setup-wizard/Tabs-Layout-Clean.jpg',
			// Category Focused Layout
			'standard_2'          => 'setup-wizard/CategoryFocused-Layout-Standard.jpg',
			'icon_focused'        => 'setup-wizard/CategoryFocused-Layout-Icon-Focused.jpg',
			'business'            => 'setup-wizard/CategoryFocused-Layout-Business.jpg',
			'minimalistic'        => 'setup-wizard/CategoryFocused-Layout-Minimalistic.jpg',
			// Grid Layout
			'grid_basic'          => 'setup-wizard/Grid-Layout-Basic.jpg',
			'grid_demo_5'         => 'setup-wizard/Grid-Layout-Informative.jpg',
			'grid_demo_6'         => 'setup-wizard/Grid-Layout-Simple.jpg',
			'grid_demo_7'         => 'setup-wizard/Grid-Layout-Left-Icon-Style.jpg',
			// Sidebar Layout
			'sidebar_basic'       => 'setup-wizard/Sidebar-Layout-Basic.jpg',
			'sidebar_colapsed'    => 'setup-wizard/Sidebar-Layout-Collapsed.jpg',
			'sidebar_formal'      => 'setup-wizard/Sidebar-Layout-Formal.jpg',
			'sidebar_compact'     => 'setup-wizard/Sidebar-Layout-Compact.jpg',
			'sidebar_plain'       => 'setup-wizard/Sidebar-Layout-Plain.jpg',
			'current_design'             => 'setup-wizard/Current-design.jpg',
	);

	public static function get_themes_description() {
		$themes_description = array(
			'Basic' => __( 'Popular layout that nicely arranges categories and articles.', 'echo-knowledge-base' ),
			'Tabs' => __( 'Use tabs to organize your documents by team, products, and services.', 'echo-knowledge-base' ),
			'Categories' => __( 'Show top Categories with its articles counter.', 'echo-knowledge-base' ),
			'Grid' => __( 'Display only top categories for users to choose from easily.', 'echo-knowledge-base' ),
			'Sidebar' => __( 'Show navigation sidebar with categories and articles on both the Main and Article Pages.', 'echo-knowledge-base' ),
		);
		return $themes_description;
	}

	public static $sidebar_images = array(
		0 => 'setup-wizard/Article-Setup-No-sidebar.jpg',
		1 => 'setup-wizard/Article-Setup-Left-Sidebar-Category-and-Article.jpg',
		2 => 'setup-wizard/Article-Setup-Right-Sidebar-Category-and-Article.jpg',
		3 => 'setup-wizard/Article-Setup-Left-Sidebar-Top-Category-Navigation.jpg',
		4 => 'setup-wizard/Article-Setup-Right-Sidebar-Top-Category-Navigation.jpg',
		5 => 'setup-wizard/Article-Setup-No-Sidebar.jpg',
	);

	public static $sidebar_compacted = array(
		'nav_sidebar_left' => [ 1 => '1', 2 => '0', 3 => '1', 4 => '0', 5 => '0' ],
		'article_nav_sidebar_type_left' => [ 1 => 'eckb-nav-sidebar-v1', 2 => 'eckb-nav-sidebar-none', 3 => 'eckb-nav-sidebar-categories', 4 => 'eckb-nav-sidebar-none', 5 => 'eckb-nav-sidebar-none' ],
		'nav_sidebar_right' => [ 1 => '0', 2 => '1', 3 => '0', 4 => '1', 5 => '0' ],
		'article_nav_sidebar_type_right' => [ 1 => 'eckb-nav-sidebar-none', 2 => 'eckb-nav-sidebar-v1', 3 => 'eckb-nav-sidebar-none', 4 => 'eckb-nav-sidebar-categories', 5 => 'eckb-nav-sidebar-none' ],
		'toc_left' => [ 1 => '0', 2 => '1', 3 => '0', 4 => '1', 5 => '0' ],
		'toc_right' => [ 1 => '1', 2 => '0', 3 => '1', 4 => '0', 5 => '1' ],
		'toc_content' => [ 1 => '0', 2 => '0', 3 => '0', 4 => '0', 5 => '0'  ],
		'article-left-sidebar-toggle' => [ 1 => 'on', 2 => 'on', 3 => 'on', 4 => 'on', 5 => 'off' ],
		'article-right-sidebar-toggle' => [ 1 => 'on', 2 => 'on', 3 => 'on', 4 => 'on', 5 => 'on' ]
	);

	/**
	 * Get names of the sidebar presets
	 * @return array
	 */
	public static function  get_sidebar_groups() {
		return [
			[
				'title' => __( 'Articles and Categories Navigation', 'echo-knowledge-base' ),
                'class' => '',
				'description' => __( 'This navigation sidebar shows a list of links to all categories and their articles. Users can navigate your KB using the links in the navigation sidebar.', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-1-knowledge-base-basic-layout/administration/demo-article-1/',
				'options' => [
					1 => __( 'Left Side', 'echo-knowledge-base' ),
					2 => __( 'Right Side', 'echo-knowledge-base' )
				]
			],
			[
				'title' => __( 'Top Categories Navigation', 'echo-knowledge-base' ),
                'class' => '',
				'description' => __( 'This navigation sidebar shows only top-level categories. Each category displays a counter of articles within the category.', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-14-category-layout/demo-article-2/',
				'options' => [
					3 => __( 'Left Side', 'echo-knowledge-base' ),
					4 => __( 'Right Side', 'echo-knowledge-base' )
				]
			],
			[
				'title' => __( 'No Navigation', 'echo-knowledge-base' ),
                'class' => '',
				'description' => __( 'Articles do not show any navigation links. The table of content and KB widgets sidebar can still be displayed.', 'echo-knowledge-base' ),
				'learn_more_url' => 'https://www.echoknowledgebase.com/demo-12-knowledge-base-image-layout/demo-article-3/',
				'options' => [
					5 => __( 'No Navigation', 'echo-knowledge-base' ),
				]
			],
		];
	}

	public static function get_search_presets() {
		return array(
		);
	}

	public static function get_categories_presets() {

		$themes_array = self::get_themes_array();

		// retrieve theme specific configuration from add-ons
		$themes = apply_filters( 'eckb_theme_wizard_get_themes_v2', $themes_array );
		if ( empty( $themes ) || ! is_array( $themes ) ) {
			$themes = $themes_array;
		}

		// TODO use get_search_themes() and remove search config from the $themes

		// TODO return $themes

		// TODO validate layout change bug

		return array();
	}

	private static function copy_search_mp_to_ap( $kb_config ) {

		$config_names = array( 'search_input_border_width', 'search_box_padding_top', 'search_box_padding_bottom', 'search_box_padding_left', 'search_box_padding_right', 'search_box_margin_top',
								'search_box_margin_bottom', 'search_box_input_width', 'search_box_results_style', 'search_title_html_tag', 'search_title_font_color',
								'search_background_color', 'search_text_input_background_color', 'search_text_input_border_color', 'search_btn_background_color', 'search_btn_border_color', 'search_title',
								'search_box_hint', 'search_button_name', 'search_results_msg', 'search_layout' );
		foreach( $config_names as $config_name ) {
			if ( isset($kb_config[$config_name]) ) {
				$kb_config['article_' . $config_name] = $kb_config[$config_name];
			}
		}

		return $kb_config;
	}

	private static function get_themes_array() {

		// retrieve preset specific configuration from add-ons like Elegant Layouts
		$add_on_themes_compacted = apply_filters( 'eckb_theme_wizard_get_themes_v2', array() );
		if ( empty( $add_on_themes_compacted ) || ! is_array( $add_on_themes_compacted ) ) {
			$add_on_themes_compacted = array();
		}

		// get all presets from core and add-ons
		$themes_compacted = self::$themes_compacted;
		foreach ( $add_on_themes_compacted as $config_name => $preset_values ) {
			if ( isset($themes_compacted[$config_name]) ) {
				$themes_compacted[$config_name] += $preset_values;
			} else {
				$themes_compacted[$config_name] = $preset_values;
			}
		}

		// get preset names
		$preset_names = array();
		foreach ( $themes_compacted['theme_name'] as $preset_seq_id => $preset_name ) {
			$preset_names[$preset_seq_id] = $preset_name;
		}

		$themes_array = array();
		$all_default_configuration = self::get_all_configuration_defaults();
		foreach ( $themes_compacted as $config_name => $preset_values ) {
			foreach ( $preset_values as $preset_seq_id => $preset_value ) {

				// if Elegant Layouts is disabled do not included its Advanced Search settings
				if ( empty($preset_names[$preset_seq_id]) ) {
					continue;
				}

				// if empty then use default
				if ( $preset_value == '' ) {
					$new_value= isset($all_default_configuration[$config_name]) ? $all_default_configuration[$config_name] : '';
				} else {
					$new_value = $preset_value;
				}

				if ( isset($themes_array[$preset_names[$preset_seq_id]][$config_name]) ) {
					$themes_array[$preset_names[$preset_seq_id]][$config_name] += $new_value;
				} else {
					$themes_array[$preset_names[$preset_seq_id]][$config_name] = $new_value;
				}
			}
		}

		return $themes_array;
	}

	/**
	 * Get default values for themes for both core and add-ons
	 * @return array
	 */
	public static function get_all_configuration_defaults() {

		$kb_defaults = EPKB_KB_Config_Specs::get_default_kb_config( EPKB_KB_Config_DB::DEFAULT_KB_ID );

		// add all configuration defaults from addons
		$kb_all_defaults = apply_filters( 'eckb_editor_get_default_config', $kb_defaults );
		if ( empty($kb_all_defaults) || is_wp_error($kb_all_defaults) ) {
			$kb_all_defaults = $kb_defaults;
		}

		return $kb_all_defaults;
	}

	/**
	 * Get JSON string with default theme data ready to use in html
	 *
	 * @param $theme
	 *
	 * @return string
	 */
	public static function get_theme_data( $theme ) {
		return htmlspecialchars( json_encode( $theme ), ENT_QUOTES, 'UTF-8' );
	}

	/**
	 * THis configuration defines fields that are part of this wizard configuration related to layout and colors.
	 * All other fields will be excluded when applying changes.
	 * @var array
	 */
	// TODO remove advanced search and elegant layout fields
	public static $theme_fields = array(

		// GENERAL
		'kb_main_page_layout',
		'templates_for_kb',
		'width',

		// CORE MAIN PAGE
		'search_title_html_tag',
		'article_search_title_html_tag',

		// OTHER
		'show_articles_before_categories',
		'nof_columns',
		'expand_articles_icon',
		'nof_articles_displayed',
		'template_category_archive_page_style',

		// TEMPLATE FOR MAIN PAGE
		'template_main_page_padding_top',
		'template_main_page_padding_bottom',
		'template_main_page_padding_left',
		'template_main_page_padding_right',
		'template_main_page_margin_top',
		'template_main_page_margin_bottom',
		'template_main_page_margin_left',
		'template_main_page_margin_right',

		// TEMPLATE FOR ARTICLE PAGE
		'templates_for_kb_article_reset',
		'templates_for_kb_article_defaults',
		'template_article_padding_top',
		'template_article_padding_bottom',
		'template_article_padding_left',
		'template_article_padding_right',
		'template_article_margin_top',
		'template_article_margin_bottom',
		'template_article_margin_left',
		'template_article_margin_right',

		// TABS LAYOUT
		'tab_down_pointer',
		'tab_nav_active_font_color',
		'tab_nav_active_background_color',
		'tab_nav_font_color',
		'tab_nav_background_color',
		'tab_nav_border_color',
		'section_desc_text_on',

		// SEARCH
		'search_layout',
		'search_input_border_width',
		'search_box_padding_top',
		'search_box_padding_bottom',
		'search_box_margin_bottom',
		'search_box_margin_top',
		'search_title',        // main search title each theme sets; keep
		'article_search_layout',
		'article_search_input_border_width',
		'article_search_box_padding_top',
		'article_search_box_padding_bottom',
		'article_search_box_margin_bottom',
		'article_search_box_margin_top',
		'article_search_title',        // main search title each theme sets; keep

		// SECTION HEAD
		'section_head_alignment',
		'section_head_category_icon_location',
		'section_head_category_icon_size',
		'section_divider',
		'section_divider_thickness',
		'section_box_shadow',
		'section_head_padding_top',
		'section_head_padding_bottom',
		'section_head_padding_left',
		'section_head_padding_right',
		'section_border_width',
		'section_box_height_mode',
		'section_body_height',
		'section_body_padding_top',
		'section_body_padding_bottom',
		'section_body_padding_left',
		'section_body_padding_right',
		'article_list_spacing',

		// COLORS
		'search_title_font_color',
		'search_background_color',
		'search_text_input_background_color',
		'search_text_input_border_color',
		'search_btn_background_color',
		'search_btn_border_color',
		'search_box_input_width',

		'article_search_title_font_color',
		'article_search_background_color',
		'article_search_text_input_background_color',
		'article_search_text_input_border_color',
		'article_search_btn_background_color',
		'article_search_btn_border_color',
		'article_search_box_input_width',

		'article-meta-color',

		'background_color',
		'article_font_color',
		'article_icon_color',
		'section_body_background_color',
		'section_border_color',
		'section_head_font_color',
		'section_head_background_color',
		'section_head_description_font_color',
		'section_divider_color',
		'section_category_font_color',
		'section_category_icon_color',
		'section_head_category_icon_color',

		// TOC
		'article_toc_title_color',
		'article_toc_text_color',
		'article_toc_active_bg_color',
		'article_toc_active_text_color',
		'article_toc_cursor_hover_bg_color',
		'article_toc_cursor_hover_text_color',
		'article_toc_border_color',
		'article_toc_scroll_offset',
		'article_toc_position_from_top',
		'article_toc_background_color',

		// BREADCRUMB
		'breadcrumb_icon_separator',
		'breadcrumb_text_color',

		// BACK NAVIGATION
		'article_content_toolbar_icon_color',
		'article_content_toolbar_text_color',
		'article_content_toolbar_text_hover_color',
		'article_content_toolbar_button_background',
		'article_content_toolbar_button_background_hover',
		'article_content_toolbar_border_color',

		'back_navigation_text_color',
		'back_navigation_bg_color',
		'back_navigation_padding_top',
		'back_navigation_padding_right',
		'back_navigation_padding_bottom',
		'back_navigation_padding_left',
		'back_navigation_border_radius',
		'back_navigation_border_color',
		'back_navigation_border',

		// PREV/NEXT NAVIGATION
		'prev_next_navigation_text_color',
		'prev_next_navigation_bg_color',
		'prev_next_navigation_hover_text_color',
		'prev_next_navigation_hover_bg_color',

		// GRID COLORS
		'grid_background_color',
		'grid_search_title_font_color',
		'grid_search_background_color',
		'grid_search_text_input_background_color',
		'grid_search_text_input_border_color',
		'grid_search_btn_background_color',
		'grid_search_btn_border_color',
		'grid_section_head_font_color',
		'grid_section_head_background_color',
		'grid_section_head_description_font_color',
		'grid_section_body_background_color',
		'grid_section_border_color',
		'grid_section_divider_color',
		'grid_section_head_icon_color',
		'grid_section_body_text_color',

		// CATEGORY BOX
		'category_box_title_text_color',
		'category_box_container_background_color',
		'category_box_category_text_color',
		'category_box_count_background_color',
		'category_box_count_text_color',
		'category_box_count_border_color',

		// SIDEBAR COLORS
		'sidebar_background_color',
		'sidebar_search_title_font_color',
		'sidebar_search_background_color',
		'sidebar_search_text_input_background_color',
		'sidebar_search_text_input_border_color',
		'sidebar_search_btn_background_color',
		'sidebar_search_btn_border_color',
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
		'sidebar_section_category_icon_color',

		// GRID STYLE
		'grid_nof_columns',
		'grid_category_icon_location',
		'grid_category_icon_thickness',
		'grid_section_icon_size',
		'grid_section_article_count',
		'grid_search_layout',
		'grid_search_input_border_width',
		'grid_search_box_padding_top',
		'grid_search_box_padding_bottom',
		'grid_search_box_padding_left',
		'grid_search_box_padding_right',
		'grid_search_box_margin_top',
		'grid_search_box_margin_bottom',
		'grid_search_box_input_width',
		'grid_section_head_alignment',
		'grid_section_head_padding_top',
		'grid_section_head_padding_bottom',
		'grid_section_head_padding_left',
		'grid_section_head_padding_right',
		'grid_section_body_alignment',
		'grid_section_cat_name_padding_top',
		'grid_section_cat_name_padding_bottom',
		'grid_section_cat_name_padding_left',
		'grid_section_cat_name_padding_right',
		'grid_section_desc_padding_top',
		'grid_section_desc_padding_bottom',
		'grid_section_desc_padding_left',
		'grid_section_desc_padding_right',
		'grid_section_border_radius',
		'grid_section_border_width',
		'grid_section_box_shadow',
		'grid_section_box_hover',
		'grid_section_divider',
		'grid_section_divider_thickness',
		'grid_section_box_height_mode',
		'grid_section_body_height',
		'grid_section_body_padding_top',
		'grid_section_body_padding_bottom',
		'grid_section_body_padding_left',
		'grid_section_body_padding_right',
		'grid_article_list_spacing',
		'grid_section_icon_padding_top',
		'grid_section_icon_padding_bottom',
		'grid_section_icon_padding_left',
		'grid_section_icon_padding_right',

		// SIDEBAR STYLE
		'sidebar_side_bar_width',
		'sidebar_side_bar_height_mode',
		'sidebar_side_bar_height',
		'sidebar_scroll_bar',
		'sidebar_top_categories_collapsed',
		'sidebar_nof_articles_displayed',
		'sidebar_show_articles_before_categories',
		'sidebar_expand_articles_icon',
		'sidebar_search_layout',
		'sidebar_search_box_collapse_mode',
		'sidebar_search_input_border_width',
		'sidebar_search_box_padding_top',
		'sidebar_search_box_padding_bottom',
		'sidebar_search_box_padding_left',
		'sidebar_search_box_padding_right',
		'sidebar_search_box_margin_top',
		'sidebar_search_box_margin_bottom',
		'sidebar_search_box_input_width',
		'sidebar_search_box_results_style',
		'sidebar_section_head_alignment',
		'sidebar_section_head_padding_top',
		'sidebar_section_head_padding_bottom',
		'sidebar_section_head_padding_left',
		'sidebar_section_head_padding_right',
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

		// ADVANCED SEARCH COLORS - MAIN PAGE
		'advanced_search_mp_title_text_shadow_toggle',
		'advanced_search_mp_title_font_color',
		'advanced_search_mp_title_font_shadow_color',
		'advanced_search_mp_description_below_title_font_shadow_color',
		'advanced_search_mp_link_font_color',
		'advanced_search_mp_background_color',
		'advanced_search_mp_text_input_background_color',
		'advanced_search_mp_text_input_border_color',
		'advanced_search_mp_btn_background_color',
		'advanced_search_mp_btn_border_color',
		'advanced_search_mp_background_gradient_from_color',
		'advanced_search_mp_background_gradient_to_color',
		'advanced_search_mp_filter_box_font_color',
		'advanced_search_mp_filter_box_background_color',
		'advanced_search_mp_search_result_category_color',
		'advanced_search_mp_show_top_category', // need to hide default search
		'advanced_search_mp_background_image_url',

		'advanced_search_mp_input_box_shadow_x_offset',
		'advanced_search_mp_input_box_shadow_y_offset',
		'advanced_search_mp_input_box_shadow_blur',
		'advanced_search_mp_input_box_shadow_spread',
		'advanced_search_mp_input_box_shadow_rgba',
		'advanced_search_mp_input_box_shadow_position_group',
		'advanced_search_mp_input_box_shadow_position_group',
		'advanced_search_mp_background_image_position_x',
		'advanced_search_mp_background_image_position_y',
		'advanced_search_mp_background_pattern_image_url',
		'advanced_search_mp_background_pattern_image_position_x',
		'advanced_search_mp_background_pattern_image_position_y',
		'advanced_search_mp_background_pattern_image_opacity',
		'advanced_search_mp_background_gradient_degree',
		'advanced_search_mp_background_gradient_opacity',
		'advanced_search_mp_description_below_title',
		'advanced_search_mp_description_below_input',
		'advanced_search_mp_background_gradient_toggle',
		'advanced_search_mp_text_title_shadow_position_group',
		'advanced_search_mp_title_text_shadow_x_offset',
		'advanced_search_mp_title_text_shadow_y_offset',
		'advanced_search_mp_title_text_shadow_blur',
		'advanced_search_mp_description_below_title_text_shadow_x_offset',
		'advanced_search_mp_description_below_title_text_shadow_y_offset',
		'advanced_search_mp_description_below_title_text_shadow_blur',
		'advanced_search_mp_description_below_title_text_shadow_toggle',
		'advanced_search_mp_box_visibility',
		'advanced_search_mp_input_box_radius',
		// ADVANCED SEARCH COLORS - ARTICLE PAGE
		'advanced_search_ap_title_text_shadow_toggle',
		'advanced_search_ap_title_font_color',
		'advanced_search_ap_title_font_shadow_color',
		'advanced_search_ap_description_below_title_font_shadow_color',
		'advanced_search_ap_link_font_color',
		'advanced_search_ap_background_color',
		'advanced_search_ap_text_input_background_color',
		'advanced_search_ap_text_input_border_color',
		'advanced_search_ap_btn_background_color',
		'advanced_search_ap_btn_border_color',
		'advanced_search_ap_background_gradient_from_color',
		'advanced_search_ap_background_gradient_to_color',
		'advanced_search_ap_filter_box_font_color',
		'advanced_search_ap_filter_box_background_color',
		'advanced_search_ap_search_result_category_color',
		'advanced_search_ap_background_gradient_toggle',
		'advanced_search_ap_background_image_url',
		'advanced_search_ap_input_box_radius',
		'advanced_search_ap_text_title_shadow_position_group',
		'advanced_search_ap_title_text_shadow_x_offset',
		'advanced_search_ap_title_text_shadow_y_offset',
		'advanced_search_ap_title_text_shadow_blur',
		'advanced_search_ap_input_box_shadow_x_offset',
		'advanced_search_ap_input_box_shadow_y_offset',
		'advanced_search_ap_input_box_shadow_blur',
		'advanced_search_ap_input_box_shadow_spread',
		'advanced_search_ap_input_box_shadow_rgba',
		'advanced_search_ap_input_box_shadow_position_group',
		'advanced_search_ap_background_image_position_x',
		'advanced_search_ap_background_image_position_y',
		'advanced_search_ap_background_pattern_image_url',
		'advanced_search_ap_background_pattern_image_position_x',
		'advanced_search_ap_background_pattern_image_position_y',
		'advanced_search_ap_background_pattern_image_opacity',
		'advanced_search_ap_background_gradient_degree',
		'advanced_search_ap_background_gradient_opacity',
		'advanced_search_ap_description_below_title_text_shadow_x_offset',
		'advanced_search_ap_description_below_title_text_shadow_y_offset',
		'advanced_search_ap_description_below_title_text_shadow_blur',
		'advanced_search_ap_description_below_title_text_shadow_toggle',
		'advanced_search_ap_filter_indicator_text',
		'advanced_search_ap_box_visibility',
		'advanced_search_ap_description_below_title',
		'advanced_search_ap_description_below_input',

		// RATING ARTICLE
		'rating_element_color',
		'rating_like_color',
		'rating_dislike_color',
		'rating_text_color',
		'rating_dropdown_color',
		'rating_feedback_button_color',

		// Theme Name
		'theme_name',

		// article v2 template
		'article-left-sidebar-background-color-v2',
		'article-content-background-color-v2',
		'article-right-sidebar-background-color-v2',
		'article-left-sidebar-desktop-width-v2',
		'article-left-sidebar-tablet-width-v2',
		'article-content-desktop-width-v2',
		'article-content-tablet-width-v2',
		'article_sidebar_component_priority',
		'article-right-sidebar-desktop-width-v2',
		'article-right-sidebar-tablet-width-v2',

		// Widgets
		'widg_search_preset_styles',
	);
}

// add any strings from the themes settings to add them in the pot file
function epkb_dont_delete_text_from_arrays_for_translators() {
	return array(

		// Preset Categories
		__( 'Basic Layout', 'echo-knowledge-base' ),
		__( 'Tabs Layout', 'echo-knowledge-base' ),
		__( 'Category Focused Layout', 'echo-knowledge-base' ),

		// Preset Names
		__( 'Standard', 'echo-knowledge-base' ),
		__( 'Elegant', 'echo-knowledge-base' ),
		__( 'Modern', 'echo-knowledge-base' ),
		__( 'Image', 'echo-knowledge-base' ),
		__( 'Informative', 'echo-knowledge-base' ),
		__( 'Formal', 'echo-knowledge-base' ),
		__( 'Bright', 'echo-knowledge-base' ),
		__( 'Distinct', 'echo-knowledge-base' ),
		__( 'Basic', 'echo-knowledge-base' ),
		__( 'Organized', 'echo-knowledge-base' ),
		__( 'Organized 2', 'echo-knowledge-base' ),
		__( 'Icon Focused', 'echo-knowledge-base' ),
		__( 'Product Based', 'echo-knowledge-base' ),
		__( 'Clean', 'echo-knowledge-base' ),
		__( 'Business', 'echo-knowledge-base' ),
		__( 'Minimalistic', 'echo-knowledge-base' ),

		// Elegant Layouts
		__( 'Simple', 'echo-knowledge-base' ),
		__( 'Left Icon Style', 'echo-knowledge-base' ),
		__( 'Collapsed', 'echo-knowledge-base' ),
		__( 'Formal', 'echo-knowledge-base' ),
		__( 'Compact', 'echo-knowledge-base' ),
		__( 'Plain', 'echo-knowledge-base' ),

		// Search Box
		__( 'Welcome to our Support Center', 'echo-knowledge-base' ),
		__( 'How can we help?', 'echo-knowledge-base' ),
		__( 'What can we help you with?', 'echo-knowledge-base' ),
		__( 'Looking for help?', 'echo-knowledge-base' ),
		__( 'Welcome to our Knowledge Base', 'echo-knowledge-base' ),
		__( 'What are you looking for?', 'echo-knowledge-base' ),
		__( 'Self Help Documentation', 'echo-knowledge-base' ),
		__( 'Help Center', 'echo-knowledge-base' ),
		__( 'Have a Question?', 'echo-knowledge-base' ),
		__( 'How can we help you today?', 'echo-knowledge-base' ),
		__( 'Customer Help Portal', 'echo-knowledge-base' ),
		__( 'Hey, what answers do you need?', 'echo-knowledge-base' ),
		__( 'Howdy! How can we help you?', 'echo-knowledge-base' ),
		__( 'Knowledge Base Help Center', 'echo-knowledge-base' ),

		// Seacrch Box - Elegant Layouts
		__( 'Support Center', 'echo-knowledge-base' ),
	);
}