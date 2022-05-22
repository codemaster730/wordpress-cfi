<?php
/**
 * Grimlock_Divider_Customizer_Control Class
 *
 * @author  themosaurus
 * @since   1.0.0
 * @access  public
 * @package grimlock/inc
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Divider Control class for the Customizer.
 *
 * @see WP_Customize_Control
 */
class Grimlock_Divider_Customizer_Control extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'divider';

	/**
	 * The default setting value of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $settings = 'blogname';

	/**
	 * Render the control's content.
	 *
	 * Allows the content to be overridden without having to rewrite the wrapper in `$this::render()`.
	 *
	 * Supports basic input types `text`, `checkbox`, `textarea`, `radio`, `select` and `dropdown-pages`.
	 * Additional input types such as `email`, `url`, `number`, `hidden` and `date` are supported implicitly.
	 *
	 * Control content can alternately be rendered in JS. See WP_Customize_Control::print_template().
	 *
	 * @since 3.4.0
	 */
	public function render_content() {
		?>
        <hr />
		<?php
	}
}