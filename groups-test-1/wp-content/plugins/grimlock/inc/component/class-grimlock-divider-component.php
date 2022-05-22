<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Divider_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Divider_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'shape'                   => '',
			'shape_color'             => '#000000',
			'flip_shape_horizontally' => false,
			'flip_shape_vertically'   => false,

			'icon'           => '',
			'image_icon'     => '',
			'icon_size'      => 20, // px
			'icon_color'     => '#000000',
			'icon_alignment' => 'center center',

			'height'           => 150, // px
			'margin_top'       => 0, // px
			'margin_bottom'    => 0, // px
			'background_color' => '',
			'mobile_displayed' => true,
		) ) );
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes   = parent::get_class( $class );
		$classes[] = 'grimlock-divider';

		if ( ! empty( $this->props['flip_shape_horizontally'] ) ) {
			$classes[] = 'grimlock-divider--flip-shape-horizontally';
		}

		if ( ! empty( $this->props['flip_shape_vertically'] ) ) {
			$classes[] = 'grimlock-divider--flip-shape-vertically';
		}

		if ( ! empty( $this->props['icon_alignment'] ) ) {
			$alignment = str_replace( ' ', '-', $this->props['icon_alignment'] );
			$classes[] = "grimlock-divider--align-icon-{$alignment}";
		}

		if ( empty( $this->props['mobile_displayed'] ) ) {
			$classes[] = 'd-none d-md-block';
		}

		return array_unique( $classes );
	}

	/**
	 * Get inline styles as property-values pairs for the component using props.
	 *
	 * @return array The styles for the component as property-values pairs
	 */
	public function get_style() {
		return array_merge( $this->get_css_vars(), $this->props['styles'] );
	}

	/**
	 * Get css vars as property-values pairs for the component using props.
	 *
	 * @return array The css vars for the component using props.
	 */
	protected function get_css_vars() {
		$css_vars = array();

		if ( ! empty( $this->props['shape_color'] ) ) {
			$css_vars['--grimlock-divider-shape-color'] = $this->props['shape_color'];
		}

		if ( ! empty( $this->props['icon_color'] ) ) {
			$css_vars['--grimlock-divider-icon-color'] = $this->props['icon_color'];
		}

		if ( ! empty( $this->props['background_color'] ) ) {
			$css_vars['--grimlock-divider-background-color'] = $this->props['background_color'];
		}

		$css_vars['--grimlock-divider-icon-size']     = floatval( $this->props['icon_size'] ) . 'px';
		$css_vars['--grimlock-divider-height']        = floatval( $this->props['height'] ) . 'px';
		$css_vars['--grimlock-divider-margin-top']    = floatval( $this->props['margin_top'] ) . 'px';
		$css_vars['--grimlock-divider-margin-bottom'] = floatval( $this->props['margin_bottom'] ) . 'px';

		return $css_vars;
	}

	/**
	 * Render the divider icon
	 */
	protected function render_icon() {
		if ( ! empty( $this->props['icon'] ) || ! empty( $this->props['image_icon'] ) ) : ?>

			<div class="grimlock-divider__icon">

				<?php if ( ! empty( $this->props['icon'] ) ) :

					include $this->props['icon'];

				elseif ( ! empty( $this->props['image_icon'] ) ) :

					$attachment_id = attachment_url_to_postid( $this->props['image_icon'] );

					if ( ! empty( $attachment_id ) ) :
						echo wp_get_attachment_image( $attachment_id, false, false, array( 'src' => esc_url( $this->props['icon'] ), 'class' => 'grimlock-divider__icon-img img-fluid' ) );
					else : ?>
						<img class="grimlock-section__icon-img img-fluid" src="<?php echo esc_url( $this->props['icon'] ); ?>" alt="<?php esc_attr_e( 'Divider Icon', 'grimlock' ); ?>" />
					<?php endif; ?>

				<?php endif; ?>

			</div>

		<?php endif;
	}

	/**
	 * Render the divider shape
	 */
	protected function render_shape() {
		if ( ! empty( $this->props['shape'] ) ) : ?>

			<div class="grimlock-divider__shape">
				<?php include $this->props['shape']; ?>
			</div>

		<?php endif;
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>
				<?php $this->render_icon(); ?>
				<?php $this->render_shape(); ?>
			</<?php $this->render_el(); ?>>
		<?php endif;
	}
}
