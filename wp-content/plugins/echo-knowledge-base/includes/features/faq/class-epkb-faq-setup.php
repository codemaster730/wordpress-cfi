<?php  if ( ! defined( 'ABSPATH' ) ) exit;

class EPKB_FAQ_Setup {

	public function __construct() {

		$faq_categories = EPKB_FAQ_Utilities::get_faq_shortcode_categories_unfiltered();
		if ( ! empty($faq_categories) ) {
			foreach( $faq_categories as $faq_category ) {
				if ( ! empty($faq_category->shortcode) ) {
					$shortcode = substr($faq_category->shortcode, -1);
					if ( empty($shortcode) || ! is_numeric($shortcode) || $shortcode < 0 || $shortcode > 999 || strlen($shortcode) > 3 ) {
						continue;
					}

					add_shortcode( EPKB_FAQ_Handler::FAQ_SHORTCODE_PREFIX . $shortcode, array( 'EPKB_FAQ_Setup', 'output_faq_shortcode' ) );
				}
			}
		}
	}

	/**
	 * FAQ shortcode output [epkb-faq-1] etc.
	 *
	 * @param array $shortcode_attributes are shortcode attributes that the user added with the shortcode
	 * @return string of HTML output replacing the shortcode itself
	 */
	public static function output_faq_shortcode( $shortcode_attributes ) {
		global $post;

		// TODO do_action( 'epkb_enqueue_scripts', $kb_config['id'] );

		return 'FAQ shortcode';
	}
}
