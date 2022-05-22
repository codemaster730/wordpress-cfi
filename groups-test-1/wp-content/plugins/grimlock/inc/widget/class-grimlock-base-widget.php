<?php

/**
 * Grimlock_Base_Widget Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
abstract class Grimlock_Base_Widget extends WP_Widget {
	/**
	 * @var array $defaults The array of Widget fields defaults as key-value pairs.
	 *
	 * @since 1.0.0
	 */
	protected $defaults;

	public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		parent::__construct( $id_base, $name, $widget_options, $control_options );
		$this->defaults = apply_filters( "{$this->id_base}_defaults", array() );
	}

	/**
	 * Handles updating settings for the current Text widget instance.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {
		return apply_filters( "{$this->id_base}_sanitize_instance", $new_instance, $old_instance );
	}

	/**
	 * Outputs the Text widget settings form.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 * @return void
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$tabs = apply_filters( "{$this->id_base}_tabs", array() );

		if ( !empty( $tabs ) ) : ?>

			<div class="categorydiv grimlock-widget">

				<ul class="grimlock-widget-tabs category-tabs">

					<?php $count = 0;
					foreach ( $tabs as $key => $name ) : ?>

						<li class="<?php echo $count === 0 ? 'tabs' : ''; ?>">
							<a href="<?php echo "#{$key}-{$this->id}" ?>"><?php echo esc_html( $name ); ?></a>
						</li>

						<?php $count++;
					endforeach; ?>

				</ul>

				<?php $count = 0;
				foreach ( $tabs as $key => $name ) : ?>

					<div class="tabs-panel" id="<?php echo "{$key}-{$this->id}" ?>" style="max-height: 100%; <?php echo $count > 0 ? 'display: none;' : ''; ?>">
						<?php do_action( "{$this->id_base}_{$key}_tab", $instance, $this ); ?>
					</div>

					<?php $count++;
				endforeach; ?>

			</div>

		<?php endif;
	}
}
