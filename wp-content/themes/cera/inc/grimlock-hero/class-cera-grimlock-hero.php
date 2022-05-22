<?php
/**
 * Cera_Grimlock_Hero Class
 *
 * @package  cera
 * @author   Themosaurus
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Cera Grimlock Hero integration class.
 */
class Cera_Grimlock_Hero {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		require_once get_template_directory() . '/inc/grimlock-hero/customizer/class-cera-grimlock-hero-customizer.php';

		// Priority 100 to add the button AFTER the hero.
		add_action( 'grimlock_custom_header',  array( $this, 'add_scroll_to_content_button' ), 100, 1 );

		add_filter( 'grimlock_hero_displayed', array( $this, 'is_displayed'                 ), 10,  1 );
		add_filter( 'grimlock_hero_customizer_layout_field_args', array( $this, 'change_hero_customizer_layout_field_args' ), 10,  1 );
	}

	/**
	 * Add a scroll to content button after the hero.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The custom header arguments.
	 */
	public function add_scroll_to_content_button( $args ) {
		if ( is_front_page() && is_page_template( 'template-homepage.php' ) ) :
			$args = apply_filters( 'grimlock_hero_args', (array) $args );
			if ( ! empty( $args['scroll_to_content_button_displayed'] ) ) : ?>
				<a href="#main" id="homepage-anchor" class="d-none d-lg-block">
					<i class="cera-icon cera-arrow-down-circle"></i>
					<?php if ( ! empty( $args['scroll_to_content_button_text'] ) ) : ?>
						<span><?php echo esc_html( $args['scroll_to_content_button_text'] ); ?></span>
					<?php endif; ?>
				</a>
				<?php
			endif;
		endif;
	}

	/**
	 * Add conditions for the hero display.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool $default True when the hero has to be displayed, false otherwise.
	 *
	 * @return bool          True when the hero has to be displayed, false otherwise.
	 */
	public function is_displayed( $default ) {
		return $default && is_page_template( 'template-homepage.php' );
	}

	/**
	 * Change default Hero layouts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The array of layouts. Keys are filenames, values are translated names.
	 *
	 * @return array           The array of 404 layouts.
	 */
	public function change_hero_customizer_layout_field_args( $args ) {
		unset( $args['choices']['6-6-cols-left-modern'] );
		unset( $args['choices']['6-6-cols-left-reverse-modern'] );
		return $args;
	}

}

return new Cera_Grimlock_Hero();
