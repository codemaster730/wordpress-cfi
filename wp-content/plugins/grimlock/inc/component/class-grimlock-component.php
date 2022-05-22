<?php
/**
 * Grimlock_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Component
 */
abstract class Grimlock_Component {
	/**
	 * @var $props - an array of variables to pass to component template.
	 */
	protected $props;

	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		$this->props = wp_parse_args( $props, array(
			'el'        => 'div',
			'id'        => '',
			'class'     => '',
			'role'      => '',
			'displayed' => true,
			'styles'    => array(),
		) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public abstract function render();

	/**
	 * Display the id for the component.
	 *
	 * @since 1.0.0
	 */
	public function render_id() {
		echo '' !== $this->props['id'] ? 'id="' . esc_attr( $this->props['id'] ) . '"' : '';
	}

	/**
	 * Display the classes for the component.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 */
	public function render_class( $class = '' ) {
		$classes = $this->get_class( $class );
		$this->output_class( $classes );
	}

	/**
	 * Output CSS classes for the component using props.
	 *
	 * @since 1.0.0
	 *
	 * @param array $classes One or more classes to output in component.
	 */
	protected function output_class( $classes ) {
		if ( ! empty( $classes ) ) {
			echo 'class="' . esc_attr( join( ' ', $classes ) ) . '"';
		}
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * TODO: Pluralize function name as it returns an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes = $this->parse_array( $class );

		if ( ! empty( $this->props['id'] ) ) {
			$classes[] = "grimlock-{$this->props['id']}";
		}

		if ( ! empty( $this->props['class'] ) ) {
			$classes = array_merge( $classes, $this->parse_array( $this->props['class'] ) );
		}

		return array_unique( $classes );
	}

	/**
	 * Parse a given argument `$args` into an array.
	 *
	 * @param $args string|array to cast as an array.
	 *
	 * @return array
	 */
	protected function parse_array( $args ) {
		if ( ! empty( $args ) && ( is_string( $args ) || is_array( $args ) ) ) {
			if ( is_string( $args ) ) {
				$args = preg_split( '#\s+#', $args );
			}
			return array_map( 'esc_attr', $args );
		}
		return array();
	}

	/**
	 * Get inline background styles as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function get_background_image_style() {
		$styles = array();
		if ( ! empty( $this->props['background_image'] ) ) {
			$styles['background-image']        = 'url(' . esc_url( $this->props['background_image'] ) . ')';
			$styles['background-repeat']       = ! empty( $this->props['background_repeat'] ) ? esc_attr( $this->props['background_repeat'] ) : 'no-repeat';
			$styles['background-position']     = ! empty( $this->props['background_position'] ) ? esc_attr( $this->props['background_position'] ) : 'center';
			$styles['background-size']         = ! empty( $this->props['background_size'] ) ? esc_attr( $this->props['background_size'] ) : 'cover';
			$styles['-webkit-background-size'] = ! empty( $this->props['background_size'] ) ? esc_attr( $this->props['background_size'] ) : 'cover';
			$styles['background-attachment']   = ! empty( $this->props['background_attachment'] ) ? esc_attr( $this->props['background_attachment'] ) : 'scroll';
		}
		return $styles;
	}

	/**
	 * Get inline padding styles as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function get_padding_style() {
		$styles = array();
		if ( ! empty( $this->props['padding_top'] ) ) {
			$styles['padding-top'] = floatval( $this->props['padding_top'] ) . 'rem';
		}

		if ( ! empty( $this->props['padding_bottom'] ) ) {
			$styles['padding-bottom'] = floatval( $this->props['padding_bottom'] ) . 'rem';
		}

		if ( ! empty( $this->props['padding_right'] ) ) {
			$styles['padding-right'] = floatval( $this->props['padding_right'] ) . 'rem';
		}

		if ( ! empty( $this->props['padding_left'] ) ) {
			$styles['padding-left'] = floatval( $this->props['padding_left'] ) . 'rem';
		}
		return $styles;
	}

	/**
	 * Get inline margin styles as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function get_margin_style() {
		$styles = array();
		if ( ! empty( $this->props['margin_top'] ) ) {
			$styles['margin-top'] = floatval( $this->props['margin_top'] ) . 'rem';
		}

		if ( ! empty( $this->props['margin_bottom'] ) ) {
			$styles['margin-bottom'] = floatval( $this->props['margin_bottom'] ) . 'rem';
		}

		if ( ! empty( $this->props['margin_right'] ) ) {
			$styles['margin-right'] = floatval( $this->props['margin_right'] ) . 'rem';
		}

		if ( ! empty( $this->props['margin_left'] ) ) {
			$styles['margin-left'] = floatval( $this->props['margin_left'] ) . 'rem';
		}
		return $styles;
	}

	/**
	 * Get inline border styles as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function get_border_style() {
		$styles = array();
		$styles['border-color'] = ! empty( $this->props['border_color'] ) ? esc_attr( $this->props['border_color'] ) : 'transparent';
		$styles['border-style'] = 'solid';
		$styles['border-width'] = ! empty( $this->props['border_width'] ) ? floatval( $this->props['border_width'] ) . 'px' : 0;
		return $styles;
	}

	/**
	 * Get inline styles as property-values pairs for the component using props.
	 *
	 * TODO: Pluralize function name as it returns an array.
	 *
	 * @since 1.0.0
	 *
	 * @return array The styles for the component as property-values pairs
	 */
	public function get_style() {
		// TODO: Add the props styles as default styles given through the component main action.
		// TODO: Fix hierarchy as styles prop is still overriden by later and more specific props such as `background_color`.
		$styles = $this->props['styles'];

		if ( ! empty( $this->props['background_color'] ) ) {
			$styles['background-color'] = esc_attr( $this->props['background_color'] );
		}

		if ( ! empty( $this->props['color'] ) ) {
			$styles['color'] = esc_attr( $this->props['color'] );
		}

		$background_image_style = $this->get_background_image_style();
		$border_style           = $this->get_border_style();
		$margin_style           = $this->get_margin_style();
		$padding_style          = $this->get_padding_style();
		$styles                 = array_merge(
			$background_image_style,
			$border_style,
			$margin_style,
			$padding_style,
			$styles
		);

		// TODO: Consider using array_unique before returning styles.
		return $styles;
	}

	/**
	 * Display inline styles for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function render_style() {
		// TODO: Pluralize var name as it's an array.
		$style = $this->get_style();
		$this->output_inline_style( $style );
	}

	/**
	 * Output inline styles for the component using props.
	 *
	 * @param array $styles An array of CSS property-value pairs.
	 * @since 1.0.0
	 */
	protected function output_inline_style( $styles ) {
		if ( ! empty( $styles ) ) {
			$style = '';
			foreach( $styles as $prop => $val ) {
				$style .= "{$prop}:{$val};";
			}
			// TODO: Only the final output needs to be validated and escaped. Further sanitizations have to be removed.
			echo 'style="' . esc_attr( $style ) . '"';
		}
	}

	/**
	 * Get data attributes as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function get_data_attributes() {
		$data_attributes = !empty( $this->props['data_attributes'] ) ? $this->props['data_attributes'] : array();

		foreach ( $data_attributes as $attribute => $value ) {
			$data_attributes[ $attribute ] = esc_attr( $value );
		}

		return $data_attributes;
	}

	/**
	 * Display data attributes for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function render_data_attributes() {
		$data_attributes = $this->get_data_attributes();
		$this->output_data_attributes( $data_attributes );
	}

	/**
	 * Output data attributes for the component using props.
	 *
	 * @param array $data_attributes An array of data attributes property-value pairs.
	 * @since 1.0.0
	 */
	protected function output_data_attributes( $data_attributes ) {
		$output = array();
		foreach ( $data_attributes as $attribute => $value ) {
			$output[] = "data-{$attribute}=\"{$value}\"";
		}
		echo implode( ' ', $output );
	}

	/**
	 * Display the role for the component.
	 *
	 * @since 1.0.0
	 */
	public function render_role() {
		echo '' !== $this->props['role'] ? 'role="' . esc_attr( $this->props['role'] ) . '"' : '';
	}

	/**
	 * Display the el for the component.
	 *
	 * @since 1.0.0
	 */
	public function render_el() {
		$el = $this->props['el'];
		if ( '' !== $el ) {
			echo $this->props['el'];
		} else {
			echo 'div';
		}
	}

	/**
	 * Check if the component has to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True when the component has to be displayed, false otherwise.
	 */
	public function is_displayed() {
		return true == $this->props['displayed'];
	}
}
