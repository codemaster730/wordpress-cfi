<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Animate_Custom_Header_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-animate
 */
class Grimlock_Animate_Custom_Header_Component extends Grimlock_Custom_Header_Component {
	/**
	 * Grimlock_Animate_Custom_Header_Component constructor.
	 *
	 * @param array $props Component props
	 */
	public function __construct( array $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'background_parallax'          => 'none',
			'thumbnail_parallax'           => 'none',
			'content_parallax'             => 'none',
			'parallax_speed'               => 0.2,
			'thumbnail_reveal'             => 'none',
			'content_reveal'               => 'none',
			'thumbnail_reveal_duration'    => 750,
			'content_reveal_duration'      => 750,
			'thumbnail_reveal_distance'    => '80px',
			'content_reveal_distance'      => '80px',
			'thumbnail_reveal_delay'       => 0,
			'content_reveal_delay'         => 0,
			'thumbnail_reveal_rotate_x'    => 0,
			'content_reveal_rotate_x'      => 0,
			'thumbnail_reveal_rotate_y'    => 0,
			'content_reveal_rotate_y'      => 0,
			'thumbnail_reveal_rotate_z'    => 0,
			'content_reveal_rotate_z'      => 0,
			'thumbnail_reveal_opacity'     => 0,
			'content_reveal_opacity'       => 0,
			'thumbnail_reveal_scale'       => 1,
			'content_reveal_scale'         => 1,
			'thumbnail_reveal_easing'      => 'cubic-bezier(0.6, 0.2, 0.1, 1)',
			'content_reveal_easing'        => 'cubic-bezier(0.6, 0.2, 0.1, 1)',
			'thumbnail_reveal_view_factor' => 0.5,
			'content_reveal_view_factor'   => 0.5,
			'reveal_mobile'                => true,
			'reveal_reset'                 => false,
		) ) );
	}

	/**
	 * Get data attributes as property-values pairs for the component using props.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_data_attributes() {
		$data_attributes = parent::get_data_attributes();

		// Configure data attributes for the background image parallax
		if ( ! empty( $this->props['background_image'] ) ) {

			switch ( $this->props['background_parallax'] ) {
				case 'natural':
				case 'inverted':
					$data_attributes['parallax'] = 'scroll';
					$data_attributes['src']      = esc_url( $this->props['background_image'] );
					break;
			}

			if ( $this->props['background_parallax'] === 'inverted' ) {
				$data_attributes['speed'] = 1.1 + $this->props['parallax_speed'];
			}
			elseif ( $this->props['background_parallax'] === 'natural' ) {
				$data_attributes['speed'] = 0.9 - $this->props['parallax_speed'];
			}

		}

		return $data_attributes;
	}

	/**
	 * Remove background image style when parallax is enabled for the background
	 *
	 * @return array
	 */
	public function get_background_image_style() {
		if ( wp_is_mobile() ) {
			return parent::get_background_image_style();
		}

		switch ( $this->props['background_parallax'] ) {
			case 'natural':
			case 'inverted':
				return array();
			default:
				return parent::get_background_image_style();
		}
	}

	/**
	 * Generate data-attributes to enable parallax on the given element
	 *
	 * @param String $element The name of an element from the component
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_element_parallax_data_attributes( $element ) {
		$data_attributes = array();

		switch ( $this->props["{$element}_parallax"] ) {
			case 'natural':
				$data_attributes['grimlock-animate-parallax'] = - $this->props['parallax_speed'];
				break;
			case 'inverted':
				$data_attributes['grimlock-animate-parallax'] = $this->props['parallax_speed'];
				break;
		}

		return $data_attributes;
	}

	/**
	 * Render the parallax data-attributes for the given element
	 *
	 * @param String $element The name of an element from the component
	 *
	 * @since 1.0.0
	 */
	public function render_element_parallax_data_attributes( $element ) {
		if ( !wp_is_mobile() ) {
			$data_attributes = $this->get_element_parallax_data_attributes( $element );
			$this->output_data_attributes( $data_attributes );
		}
	}

	/**
	 * Generate data-attributes to enable scroll reveal on the given element
	 *
	 * @param String $element The name of an element from the component
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_element_reveal_data_attributes( $element ) {
		$data_attributes = array();
		$reveal_options  = array();
		switch ( $this->props["{$element}_reveal"] ) {
			case 'bottom':
			case 'top':
			case 'left':
			case 'right':
				$reveal_options['origin']   = $this->props["{$element}_reveal"];
				$reveal_options['distance'] = $this->props["{$element}_reveal_distance"];
				break;
			case 'fade':
				$reveal_options['origin']   = 'top';
				$reveal_options['distance'] = 0;
				break;
		}

		if ( !empty( $reveal_options ) ) {
			$reveal_options['duration']   = $this->props["{$element}_reveal_duration"];
			$reveal_options['delay']      = $this->props["{$element}_reveal_delay"];
			$reveal_options['rotate']     = array(
				'x' => $this->props["{$element}_reveal_rotate_x"],
				'y' => $this->props["{$element}_reveal_rotate_y"],
				'z' => $this->props["{$element}_reveal_rotate_z"],
			);
			$reveal_options['opacity']    = $this->props["{$element}_reveal_opacity"];
			$reveal_options['scale']      = $this->props["{$element}_reveal_scale"];
			$reveal_options['easing']     = $this->props["{$element}_reveal_easing"];
			$reveal_options['viewFactor'] = $this->props["{$element}_reveal_view_factor"];
			$reveal_options['mobile']     = $this->props['reveal_mobile'];
			$reveal_options['reset']      = $this->props['reveal_reset'];

			$data_attributes['grimlock-animate-scroll-reveal'] = esc_attr( wp_json_encode( $reveal_options ) );
		}

		return $data_attributes;
	}

	/**
	 * Render the scroll reveal data-attributes for the given element
	 *
	 * @param String $element The name of an element from the component
	 *
	 * @since 1.0.0
	 */
	public function render_element_reveal_data_attributes( $element ) {
		$data_attributes = $this->get_element_reveal_data_attributes( $element );
		$this->output_data_attributes( $data_attributes );
	}

	/**
	 * Generate a class to add to the component if the given element has a parallax effect
	 *
	 * @param $element
	 *
	 * @return string
	 */
	public function get_element_parallax_class( $element ) {
		switch ( $this->props["{$element}_parallax"] ) {
			case 'natural':
			case 'inverted':
				return "section_{$element}_parallax";
			default:
				return '';
		}
	}

	/**
	 * Generate a class to add to the component if the given element has a reveal effect
	 *
	 * @since 1.0.0
	 *
	 * @param $element
	 *
	 * @return string
	 */
	public function get_element_reveal_class( $element ) {
		switch ( $this->props["{$element}_reveal"] ) {
			case 'bottom':
			case 'top':
			case 'left':
			case 'right':
			case 'fade':
				return "section_{$element}_reveal";
			default:
				return '';
		}
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
		$classes[] = $this->get_element_parallax_class( 'thumbnail' );
		$classes[] = $this->get_element_parallax_class( 'content' );
		$classes[] = $this->get_element_reveal_class( 'thumbnail' );
		$classes[] = $this->get_element_reveal_class( 'content' );

		return array_unique( $classes );
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>
			<div class="region__inner" <?php $this->render_inner_style(); ?>>
				<div class="region__container">
					<div class="region__row">
						<div class="region__col region__col--1" <?php $this->render_element_parallax_data_attributes( 'thumbnail' ) ?>>
							<div class="grimlock-reveal-element grimlock-reveal-element--thumbnail" <?php $this->render_element_reveal_data_attributes( 'thumbnail' ) ?>>
								<?php $this->render_thumbnail(); ?>
							</div>
						</div><!-- .region__col -->
						<div class="region__col region__col--2" <?php $this->render_element_parallax_data_attributes( 'content' ) ?>>
							<div class="grimlock-reveal-element grimlock-reveal-element--content" <?php $this->render_element_reveal_data_attributes( 'content' ) ?>>
								<?php $this->render_header(); ?>
							</div>
						</div><!-- .region__col -->
					</div><!-- .region__row -->
				</div><!-- .region__container -->
			</div><!-- .region__inner -->
			</<?php $this->render_el(); ?>><!-- .grimlock-section -->
			<?php // TODO: Migrate CSS bit into a compiled SCSS file. ?>
			<style>
				.grimlock-custom_header .grimlock-section__thumbnail {
					display: none;
				}
			</style>
		<?php
		endif;
	}
}