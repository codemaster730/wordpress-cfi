<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class EPKB_KB_Config_Layouts {

	const KB_ARTICLE_PAGE_NO_LAYOUT = 'Article';
	const SIDEBAR_LAYOUT = 'Sidebar';
	const GRID_LAYOUT = 'Grid';
	const CATEGORIES_LAYOUT = 'Categories';

	/**
	 * Get all known layouts including add-ons
	 * @return array all defined layout names
	 */
	public static function get_main_page_layout_name_value() {
		$core_layouts = array (
			EPKB_KB_Config_Layout_Basic::LAYOUT_NAME => __( 'Basic', 'echo-knowledge-base' ),
			EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME  => __( 'Tabs', 'echo-knowledge-base' ),
			EPKB_KB_Config_Layout_Categories::LAYOUT_NAME  => __( 'Category Focused', 'echo-knowledge-base' )
		);
		return apply_filters( 'epkb_layout_names', $core_layouts );
	}

	/**
	 * Get all known layouts including add-ons
	 * @return array all defined layout names
	 */
	public static function get_main_page_layout_names() {
		$layout_name_values = self::get_main_page_layout_name_value();
		return array_keys($layout_name_values);
	}

	/**
	 * Return current layout or default layout if not found.
	 *
	 * @param $kb_config
	 * @return string
	 */
	public static function get_kb_main_page_layout_name( $kb_config ) {
		$chosen_main_page_layout = EPKB_Utilities::post('epkb_chosen_main_page_layout');
		$layout = empty($kb_config['kb_main_page_layout']) || ! in_array($kb_config['kb_main_page_layout'], self::get_main_page_layout_names() )
						? EPKB_KB_Config_Layout_Basic::LAYOUT_NAME
						: (  empty($chosen_main_page_layout) ? $kb_config['kb_main_page_layout'] : $chosen_main_page_layout );
		return $layout;
	}

	/**
	 * Mapping between Page 1 and Page 2
	 *
	 * @return array all defined layout mapping
	 */
	public static function get_layout_mapping() {
		$core_layouts = array (
			array( EPKB_KB_Config_Layout_Basic::LAYOUT_NAME => self::KB_ARTICLE_PAGE_NO_LAYOUT ),
			array( EPKB_KB_Config_Layout_Tabs::LAYOUT_NAME => self::KB_ARTICLE_PAGE_NO_LAYOUT ),
			array( EPKB_KB_Config_Layout_Categories::LAYOUT_NAME => self::KB_ARTICLE_PAGE_NO_LAYOUT )
		);
		return apply_filters( 'epkb_layout_mapping', $core_layouts );
	}
}
