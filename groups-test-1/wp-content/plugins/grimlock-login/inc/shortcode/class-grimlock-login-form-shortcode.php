<?php
/**
 * Grimlock_Login_Form_Shortcode Class
 *
 * @author  Themosaurus
 * @since   1.0.2
 * @package grimlock-login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main Grimlock_Login_Form_Shortcode class
 */
class Grimlock_Login_Form_Shortcode {
	/**
	 * @var string The ID base.
	 */
	protected $id_base;

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $id_base = 'grimlock_login_form' ) {
		$this->id_base = $id_base;

		add_shortcode( $id_base, array( $this, 'render' ) );
	}

	/**
	 * Outputs the HTML for the Grimlock section.
	 *
	 * @param  array  $atts    The array of attributes for the shortcode. Support all arguments of the wp_login_form() function
	 * @param  string $content The content for the shortcode (unused in this shortcode).
	 *
	 * @see wp_login_form()
	 *
	 * @since 1.0.2
	 *
	 * @return string The shortcode output.
	 */
	public function render( $atts = array(), $content = '' ) {
		/**
		 * This shortcode supports all arguments of the wp_login_form() function
		 * @see wp_login_form()
		 */

		if ( empty ( $atts ) ) {
			$atts = array();
		}

		// Sanitize shortcode atts
		foreach ( $atts as $key => $att ) {
			switch ( $key ) {
				case 'remember':
				case 'value_remember':
					$att = boolval( $att );
					break;
				case 'redirect':
					$att = esc_url( $att );
					break;
				case 'form_id':
				case 'id_username':
				case 'id_password':
				case 'id_remember':
				case 'id_submit':
				case 'value_username':
					$att = esc_attr( $att );
					break;
				case 'label_username':
				case 'label_password':
				case 'label_remember':
				case 'label_login':
				default:
					$att = esc_html( $att );
					break;
			}

			$atts[ $key ] = $att;
		}

		$atts['echo'] = false; // Force the "echo" arg to false because we want to return the content of wp_login_form(), not echo it

		return wp_login_form( $atts );
	}
}

return new Grimlock_Login_Form_Shortcode();
