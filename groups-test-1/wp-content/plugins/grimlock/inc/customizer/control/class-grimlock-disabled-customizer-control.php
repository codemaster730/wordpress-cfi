<?php
/**
 * Grimlock_Disabled_Customizer_Control Class
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
class Grimlock_Disabled_Customizer_Control extends WP_Customize_Control {
	/**
	 * The default type of customize control being rendered.
	 *
	 * @since  1.0.0
	 * @access public
	 * @var    string
	 */
	public $type = 'text_disabled';

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
	protected function render_content() {
		switch( $this->type ) :
			case 'textarea_disabled':
				?>
				<label>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif;
					if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo $this->description; ?></span>
					<?php endif; ?>
					<textarea rows="5" <?php $this->input_attrs(); ?> <?php $this->link(); ?> disabled><?php echo esc_textarea( $this->value() ); ?></textarea>
				</label>
				<?php
				break;
			default:
				?>
				<label>
					<?php if ( ! empty( $this->label ) ) : ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php endif;
					if ( ! empty( $this->description ) ) : ?>
						<span class="description customize-control-description"><?php echo $this->description; ?></span>
					<?php endif; ?>
					<input type="text" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> disabled/>
				</label>
				<?php
				break;
		endswitch;
	}
}