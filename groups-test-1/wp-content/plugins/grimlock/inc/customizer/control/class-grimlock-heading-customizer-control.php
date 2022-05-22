<?php
/**
 * Grimlock_Separator_Customizer_Control Class
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
 * Add a separator control for the customizer
 *
 * @see WP_Customize_Control
 */
class Grimlock_Heading_Customizer_Control extends WP_Customize_Control {
	/**
	 * The type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'heading';

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
        <span class="customize-control-title"><?php echo esc_html( $this->label ) ?></span>
        <span class="description customize-control-description"><?php echo esc_html( $this->description ) ?></span>
		<?php
	}
}