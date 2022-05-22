<?php

/**
 * Grimlock_Base_Widget_Fields Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */
abstract class Grimlock_Base_Widget_Fields {

	protected $id_base;

	public function __construct( $id_base = '' ) {
		$this->id_base = $id_base;

		add_filter( "{$this->id_base}_defaults",          array( $this, 'change_defaults'       ), 10, 1 );
		add_filter( "{$this->id_base}_component_args",    array( $this, 'change_component_args' ), 10, 4 );
		add_filter( "{$this->id_base}_sanitize_instance", array( $this, 'sanitize_instance'     ), 10, 2 );
	}

	/**
	 * Display a color picker for the widget form.
	 *
	 * @param $args
	 */
	protected function color_picker( $args ) {
	    if ( empty( $args ) ) {
	        return;
        }

		$args = wp_parse_args( $args, array(
            'id' => '',
            'name' => '',
            'value' => '',
            'label' => '',
            'description' => '',
        ) );

        $color_palettes = apply_filters( 'grimlock_color_field_palettes', array() );
        $color_palettes_json = json_encode( $color_palettes ); ?>
        <p <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
            <label for="<?php echo $args['id']; ?>" style="display: block;"><?php echo $args['label']; ?></label>
            <input type="text"
                   class="<?php echo "grimlock_section_widget-color-picker grimlock_section_widget-{$args['id']}"; ?>"
                   id="<?php echo $args['id']; ?>"
                   name="<?php echo $args['name']; ?>"
                   value="<?php echo esc_attr( $args['value'] ); ?>"
                   data-alpha="true"
                   data-palettes="<?php echo esc_attr( $color_palettes_json ); ?>"
            />
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
        </p>
		<?php
	}

	/**
     * Display a radio image for the widget form.
     *
	 * @param $args
	 */
	protected function radio_image( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => '',
			'label' => '',
            'choices' => array(),
            'description' => '',
		) ); ?>
		<div id="<?php echo $args['id']; ?>" class="grimlock_section_widget-radio-image wp-clearfix" <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
            <label class="grimlock_section_widget-radio-image__title"><?php echo $args['label']; ?></label>
            <div class="grimlock_section_widget-radio-image__buttonset">
                <?php foreach ( $args['choices'] as $key => $choice ) : ?>
                    <div class="grimlock_section_widget-radio-image__button ui-button-col">
                        <input type="radio"
                               value="<?php echo $key; ?>"
                               name="<?php echo $args['name']; ?>"
                               id="<?php echo $args['id']; ?>-<?php echo $key; ?>"
                            <?php echo $key === $args['value'] ? 'checked="checked"' : ''; ?>
                        />
                        <label for="<?php echo $args['id']; ?>-<?php echo $key; ?>">
                            <img src="<?php echo $choice; ?>" />
	                        <span class="ui-button__tooltip"><?php echo $key; ?></span>
                        </label>
                    </div><!-- .grimlock_section_widget-radio-image__button -->
                <?php endforeach; ?>
            </div><!-- .grimlock_section_widget-radio-image__buttons -->
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
        </div>
		<?php
	}

	/**
     * Display a text field for the widget form.
     *
	 * @param $args
	 */
	protected function textfield( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => '',
			'label' => '',
            'description' => '',
		) ); ?>
		<p <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
            <label for="<?php echo $args['id']; ?>"><?php echo $args['label']; ?></label>
            <input class="widefat" id="<?php echo $args['id']; ?>" name="<?php echo $args['name']; ?>" type="text" value="<?php echo esc_attr( $args['value'] ); ?>" />
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
        </p>
		<?php
	}

	/**
     * Display a number field for the widget form.
     *
	 * @param $args
	 */
	protected function numberfield( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => '',
			'label' => '',
            'min' => '',
            'max' => '',
            'description' => '',
		) ); ?>
        <p <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
            <label for="<?php echo $args['id']; ?>"><?php echo $args['label']; ?></label>
            <input class="widefat" id="<?php echo $args['id']; ?>" name="<?php echo $args['name']; ?>" type="number" value="<?php echo esc_attr( $args['value'] ); ?>" <?php echo '' !== $args['min'] ? 'min="' . $args['min'] . '"' : '' ?> <?php echo '' !== $args['max'] ? 'max="' . $args['max'] . '"' : '' ?> />
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
        </p>
		<?php
	}

	/**
     * Display a text area for the widget form.
     *
	 * @param $args
	 */
	protected function textarea( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => '',
			'label' => '',
            'description' => '',
		) ); ?>
		<p <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
            <label for="<?php echo $args['id']; ?>"><?php echo $args['label']; ?></label>
			<textarea class="widefat" rows="8" cols="20" id="<?php echo $args['id']; ?>" name="<?php echo $args['name']; ?>"><?php echo esc_textarea( $args['value'] ); ?></textarea>
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
        </p>
		<?php
	}

	/**
     * Display a checkbox for the widget form.
     *
	 * @param $args
	 */
	protected function checkbox( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id'          => '',
			'value'       => '',
			'label'       => '',
            'description' => '',
		) ); ?>
		<p <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
            <input id="<?php echo $args['id']; ?>" name="<?php echo $args['name']; ?>" type="checkbox"<?php checked( $args['value'] ); ?> />&nbsp;
			<label for="<?php echo $args['id']; ?>"><?php echo $args['label']; ?></label>
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
        </p>
		<?php
	}

	/**
     * Display an image field for the widget form.
     *
	 * @param $args
	 */
	protected function image( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => '',
			'label' => '',
            'description' => '',
		) ); ?>
		<div class="grimlock_section_widget-image wp-clearfix" <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>

            <div class="grimlock_section_widget-image__title"><?php echo $args['label']; ?></div>
            <input id="<?php echo $args['id']; ?>" name="<?php echo $args['name']; ?>" class="attachment-media-src" type="hidden" value="<?php echo esc_attr( $args['value'] ); ?>" />

            <div class="attachment-media-view">
                <div class="thumbnail thumbnail-image upload-button <?php echo empty( $args['value'] ) ? 'hidden' : ''; ?>">
                    <img class="attachment-thumb" src="<?php echo wp_get_attachment_image_url( $args['value'], 'full' ); ?>" draggable="false" />
                </div><!-- .thumbnail-image -->

                <div class="placeholder upload-button <?php echo !empty( $args['value'] ) ? 'hidden' : ''; ?>"><?php esc_html_e( 'No image selected' ); ?></div>

                <div class="actions upload-actions <?php echo !empty( $args['value'] ) ? 'hidden' : ''; ?>">
                    <input type="button" class="button upload-button right" value="<?php esc_html_e( 'Select Image', 'grimlock' ); ?>" />
                    <br class="clear"/>
                </div><!-- .upload-actions -->

                <div class="actions remove-actions <?php echo empty( $args['value'] ) ? 'hidden' : ''; ?>">
                    <input type="button" class="button remove-button left" value="<?php esc_html_e( 'Remove', 'grimlock' ); ?>" />
                    <input type="button" class="button upload-button right" value="<?php esc_html_e( 'Change Image', 'grimlock' ); ?>" />
                    <br class="clear"/>
                </div><!-- .remove-actions -->
            </div><!-- .attachment-media-view -->
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>

        </div><!-- .grimlock_section_widget-image -->
		<?php
	}

	/**
     * Display a select for the widget form.
     *
	 * @param $args
	 */
	protected function select( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id'          => '',
			'value'       => ! empty( $args['multiple'] ) ? array() : '',
			'label'       => '',
            'choices'     => array(),
            'multiple'    => false,
            'description' => '',
		) ); ?>
		<p <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
			<label for="<?php echo $args['id']; ?>" style="display: block;"><?php echo $args['label']; ?></label>
			<select class="widefat" id="<?php echo $args['id']; ?>" name="<?php echo $args['multiple'] ? $args['name'] . '[]' : $args['name'] ?>" <?php echo $args['multiple'] ? 'multiple' : '' ?>>
				<?php foreach ( $args['choices'] as $key => $choice ): ?>
					<?php if ( is_array( $choice ) && isset( $choice['label'] ) && isset( $choice['subchoices'] ) ): ?>
						<optgroup label="<?php echo esc_attr( $choice['label'] ) ?>">
							<?php foreach ( $choice['subchoices'] as $subkey => $subchoice ): ?>
                                <?php $selected = is_array( $args['value'] ) ? in_array( $subkey, $args['value'] ) : $subkey == $args['value']; ?>
								<option value="<?php echo esc_attr( $subkey ); ?>" <?php echo $selected ? 'selected' : '' ?>>
									<?php echo $subchoice ?>
								</option>
							<?php endforeach; ?>
						</optgroup>
					<?php else: ?>
						<?php $selected = is_array( $args['value'] ) ? in_array( $key, $args['value'] ) : $key == $args['value']; ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected ? 'selected' : '' ?>>
							<?php echo $choice ?>
						</option>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
			<?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
		</p>
		<?php
	}

	/**
     * Display a range input for the widget form.
     *
	 * @param $args
	 */
	protected function range( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => '',
			'label' => '',
			'min' => '',
			'max' => '',
            'step' => 0.25,
			'unit' => '',
			'description' => '',
		) ); ?>
        <p <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
            <label for="<?php echo $args['id']; ?>"><?php echo $args['label']; ?></label>
            <input class="widefat" id="<?php echo $args['id']; ?>" name="<?php echo $args['name']; ?>" type="range" value="<?php echo esc_attr( $args['value'] ); ?>" <?php echo '' !== $args['min'] ? 'min="' . $args['min'] . '"' : '' ?> <?php echo '' !== $args['max'] ? 'max="' . $args['max'] . '"' : '' ?> <?php echo '' !== $args['step'] ? 'step="' . $args['step'] . '"' : '' ?> <?php echo '' !== $args['unit'] ? 'data-unit="' . $args['unit'] . '"' : '' ?> />
	        <?php if ( ! empty( $args['description'] ) ) : ?>
                <small style="display: inline-block;"><?php echo $args['description'] ?></small>
	        <?php endif; ?>
        </p>
		<?php
	}

	/**
	 * Display a post select field for the widget.
	 *
	 * @param $args
	 */
	protected function post_select( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => array(),
			'label' => '',
			'post_type' => 'post',
			'description' => '',
		) ); ?>
		<div class="wp-clearfix" <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
			<label for="<?php echo $args['id']; ?>" style="display: block;"><?php echo $args['label']; ?></label>
			<select id="<?php echo $args['id']; ?>" class="widefat grimlock-widget-post-select" name="<?php echo $args['name'] . '[]'; ?>" multiple data-grimlock-widget-post-type="<?php echo esc_attr( $args['post_type'] ); ?>">
				<?php foreach ( $args['value'] as $value ) :
					$post = get_post( $value ); ?>
					<option value="<?php echo esc_attr( $post->ID ); ?>" selected><?php echo $post->post_title; ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Display a term select field for the widget.
	 *
	 * @param $args
	 */
	protected function term_select( $args ) {
		if ( empty( $args ) ) {
			return;
		}
		$args = wp_parse_args( $args, array(
			'id' => '',
			'name' => '',
			'value' => array(),
			'label' => '',
			'taxonomy' => 'category',
			'description' => '',
		) ); ?>
		<div class="wp-clearfix" <?php echo ! empty( $args['conditional_logic'] ) ? 'data-grimlock-widget-conditional-logic="' . esc_attr( json_encode( $args['conditional_logic'] ) ) . '"' : ''; ?>>
			<label for="<?php echo $args['id']; ?>" style="display: block;"><?php echo $args['label']; ?></label>
			<select id="<?php echo $args['id']; ?>" class="widefat grimlock-widget-term-select" name="<?php echo $args['name'] . '[]'; ?>" multiple data-grimlock-widget-taxonomy="<?php echo esc_attr( $args['taxonomy'] ); ?>">
				<?php foreach ( $args['value'] as $value ) :
					$term = get_term( $value ); ?>
					<option value="<?php echo esc_attr( $term->term_id ); ?>" selected><?php echo $term->name; ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ( ! empty( $args['description'] ) ) : ?>
				<small style="display: inline-block;"><?php echo $args['description'] ?></small>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Display a separator
	 */
	public function add_separator() {
		?><hr style="margin: 1rem 0;"><?php
	}

	/**
	 * Filter HTML and encode emojis for database save
	 *
	 * @param $text
	 * @param bool $allow_unfiltered_html
	 *
	 * @return string
	 */
	protected function sanitize_text( $text, $allow_unfiltered_html = false ) {
		if ( ! empty( $allow_unfiltered_html ) ) {
			return wp_encode_emoji( $text );
		}

		return wp_kses_post( wp_encode_emoji( $text ) );
	}

	public abstract function change_defaults( $defaults );
	public abstract function change_component_args( $component_args, $instance, $widget_args, $widget_id );
	public abstract function sanitize_instance( $new_instance, $old_instance );
}
