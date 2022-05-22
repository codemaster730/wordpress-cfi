<?php
/**
 * Grimlock_Animate_Gallery_Section_Component Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package  grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to generate section in page.
 */
class Grimlock_Animate_Gallery_Section_Component extends Grimlock_Gallery_Section_Component {
	/**
	 * Grimlock_Animate_Section_Component constructor.
	 *
	 * @param array $props Component props
	 */
	public function __construct( array $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'gallery_items_reveal_selector'    => '.tiled-gallery-item, .gallery-item, .section__footer',
			'gallery_items_reveal'             => 'none',
			'content_reveal'                   => 'none',
			'gallery_items_reveal_duration'    => 750,
			'content_reveal_duration'          => 750,
			'gallery_items_reveal_interval'    => 50,
			'gallery_items_reveal_distance'    => '80px',
			'content_reveal_distance'          => '80px',
			'gallery_items_reveal_delay'       => 500,
			'content_reveal_delay'             => 0,
			'gallery_items_reveal_rotate_x'    => 0,
			'content_reveal_rotate_x'          => 0,
			'gallery_items_reveal_rotate_y'    => 0,
			'content_reveal_rotate_y'          => 0,
			'gallery_items_reveal_rotate_z'    => 0,
			'content_reveal_rotate_z'          => 0,
			'gallery_items_reveal_opacity'     => 0,
			'content_reveal_opacity'           => 0,
			'gallery_items_reveal_scale'       => 1,
			'content_reveal_scale'             => 1,
			'gallery_items_reveal_easing'      => 'cubic-bezier(0.6, 0.2, 0.1, 1)',
			'content_reveal_easing'            => 'cubic-bezier(0.6, 0.2, 0.1, 1)',
			'gallery_items_reveal_view_factor' => 0.5,
			'content_reveal_view_factor'       => 0.5,
			'reveal_mobile'                    => true,
			'reveal_reset'                     => false,
		) ) );
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
			$reveal_options['selector']   = ! empty( $this->props["{$element}_reveal_selector"] ) ? $this->props["{$element}_reveal_selector"] : false;
			$reveal_options['duration']   = $this->props["{$element}_reveal_duration"];
			$reveal_options['interval']   = ! empty( $this->props["{$element}_reveal_interval"] ) ? $this->props["{$element}_reveal_interval"] : false;
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
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) :
			$classes = array(
				'grimlock-section',
				'section',
				'region',
				$this->get_element_reveal_class( 'gallery_items' ),
				$this->get_element_reveal_class( 'content' ),
			); ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class( $classes ); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>
			<div class="region__inner" <?php $this->render_inner_style(); ?>>
				<div class="region__container">
					<div class="region__row">
						<div class="region__col region__col--1">
							<div class="grimlock-reveal-element grimlock-reveal-element--thumbnail" <?php $this->render_element_reveal_data_attributes( 'gallery_items' ) ?>>
								<?php $this->render_thumbnail(); ?>
							</div>
						</div><!-- .region__col -->
						<div class="region__col region__col--2">
							<div class="grimlock-reveal-element grimlock-reveal-element--content" <?php $this->render_element_reveal_data_attributes( 'content' ) ?>>
								<?php
								$this->render_header();
								$this->render_content();
								$this->render_footer(); ?>
							</div>
						</div><!-- .region__col -->
					</div><!-- .region__row -->
				</div><!-- .region__container -->
			</div><!-- .region__inner -->
			</<?php $this->render_el(); ?>><!-- .grimlock-section -->
		<?php
		endif;
	}
}
