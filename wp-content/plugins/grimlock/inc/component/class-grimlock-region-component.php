<?php
/**
 * Grimlock_Region_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Region_Component
 */
class Grimlock_Region_Component extends Grimlock_Component {
	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'margin_top'         => 0, // %
			'margin_bottom'      => 0, // %
			'padding_top'        => 0, // %
			'padding_bottom'     => 0, // %
			'layout'             => '',
			'container_layout'   => '',
			'inner_styles'       => array(),
		) ) );
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
		$classes   = parent::get_class( $class );
		$classes[] = 'region';
		$classes[] = 'grimlock-region';

		// TODO: Consider fetching these values from the styles array. In consequence, the later will need to hold default values for `padding-top` and `padding-bottom`.
		$classes[] = 'grimlock-region--mt-' . intval( $this->props['margin_top'] );
		$classes[] = 'grimlock-region--mb-' . intval( $this->props['margin_bottom'] );
		$classes[] = 'grimlock-region--pt-' . intval( $this->props['padding_top'] );
		$classes[] = 'grimlock-region--pb-' . intval( $this->props['padding_bottom'] );

		if ( ! empty( $this->props['layout'] ) ) {
			$classes[] = "grimlock-region--{$this->props['layout']}";
			$classes[] = "region--{$this->props['layout']}";
		}

		if ( ! empty( $this->props['container_layout'] ) ) {
			$classes[] = "grimlock-region--container-{$this->props['container_layout']}";
			$classes[] = "region--container-{$this->props['container_layout']}";
		}

		return array_unique( $classes );
	}

	/**
	 * Get inline styles as property-values pairs for the component using props.
	 *
	 * TODO: Pluralize function name as it returns an array.
	 *
	 * @since 1.0.0
	 */
	public function get_style() {
		// TODO: Add the props styles as default styles given through the component main action.
		// TODO: Fix hierarchy as styles prop is still overriden by later and more specific props such as `color`.
		$styles = $this->props['styles'];

		if ( ! empty( $this->props['color'] ) ) {
			$styles['color'] = esc_attr( $this->props['color'] );
		}

		$margin_style  = $this->get_margin_style();
		$background_image_style = $this->get_background_image_style();
		$border_style = $this->get_border_style();
		$styles = array_merge(
			$margin_style,
			$background_image_style,
			$border_style,
			$styles
		);

		return $styles;
	}

	/**
	 * Get inline border styles as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 */
	public function get_border_style() {
		$styles = array();
		$styles['border-top-color']    = ! empty( $this->props['border_top_color'] ) ? esc_attr( $this->props['border_top_color'] ) : 'transparent';
		$styles['border-top-style']    = 'solid';
		$styles['border-top-width']    = ! empty( $this->props['border_top_width'] ) ? intval( $this->props['border_top_width'] ) . 'px' : 0;
		$styles['border-bottom-color'] = ! empty( $this->props['border_bottom_color'] ) ? esc_attr( $this->props['border_bottom_color'] ) : 'transparent';
		$styles['border-bottom-style'] = 'solid';
		$styles['border-bottom-width'] = ! empty( $this->props['border_bottom_width'] ) ? intval( $this->props['border_bottom_width'] ) . 'px' : 0;
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
			$styles['padding-top'] = floatval( $this->props['padding_top'] ) . '%';
		}

		if ( ! empty( $this->props['padding_bottom'] ) ) {
			$styles['padding-bottom'] = floatval( $this->props['padding_bottom'] ) . '%';
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
			$styles['margin-top'] = floatval( $this->props['margin_top'] ) . '%';
		}

		if ( ! empty( $this->props['margin_bottom'] ) ) {
			$styles['margin-bottom'] = floatval( $this->props['margin_bottom'] ) . '%';
		}
		return $styles;
	}

	/**
	 * Get inline styles as property-values pairs for the component using props.
	 *
	 * TODO: Pluralize function name as it returns an array.
	 *
	 * @since 1.0.0
	 */
	public function get_inner_style() {
		// TODO: Add the props styles as default styles given through the component main action.
		// TODO: Fix hierarchy as styles prop is still overriden by later and more specific props such as `color`.
		$styles = $this->props['inner_styles'];

		if ( ! empty( $this->props['background_color'] ) ) {
			$styles['background-color'] = esc_attr( $this->props['background_color'] );
		}

		$padding_style = $this->get_padding_style();
		$styles        = array_merge(
			$padding_style,
			$styles
		);

		return $styles;
	}

	/**
	 * Output inline styles as property-values pairs for the inner part of the component using props.
	 *
	 * @since 1.0.0
	 */
	public function render_inner_style() {
		// TODO: Pluralize var name as it's an array.
		$style = $this->get_inner_style();
		$this->output_inline_style( $style );
	}

	/**
	 * Call a given callback during the render method to allow futher customization
	 * of the component.
	 *
	 * @since 1.0.0
	 */
	public function render_callback() {
		$callback = $this->get_callback();
		if ( ! empty( $callback ) ) {
			$callback_args = $this->props;
			unset( $callback_args['callback'] );

			// Run the callback
			call_user_func_array( $callback, array( $callback_args ) );
		}
	}

	/**
	 * Retrieve the render callback for the component.
	 *
	 * @since 1.0.0
	 *
	 * @return string.
	 */
	public function get_callback() {
		return isset( $this->props['callback'] ) && is_callable( $this->props['callback'] ) ? $this->props['callback'] : null;
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>
			<div class="grimlock-region__inner region__inner" <?php $this->render_inner_style(); ?>>
				<div class="grimlock-region__container region__container">
					<?php $this->render_callback(); ?>
				</div><!-- .region__container -->
			</div><!-- .region__inner -->
			</<?php $this->render_el(); ?>><!-- .grimlock-region -->
		<?php endif;
	}
}
