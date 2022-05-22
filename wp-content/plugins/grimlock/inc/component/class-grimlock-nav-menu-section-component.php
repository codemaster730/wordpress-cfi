<?php
/**
 * Grimlock_Nav_Menu_Section_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to generate section in page.
 */
class Grimlock_Nav_Menu_Section_Component extends Grimlock_Section_Component {
	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'title_displayed'    => false,
            'title'              => '',
			'subtitle_displayed' => false,
            'subtitle'           => '',
            'theme_location'     => '',
			'menu'               => '',
			'menu_depth'         => 0,
			'button_displayed'   => false,
			'layout'             => '12-cols-left',
			'container_layout'   => 'fluid',
		) ) );
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param  string|array $class One or more classes to add to the class list.
	 *
	 * @return array               Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes   = parent::get_class( $class );
		$classes[] = 'grimlock-nav-menu-section';
		return array_unique( $classes );
	}

	/**
	 * Display the current component content.
	 *
	 * @since 1.0.0
	 */
	protected function render_content() {
		?>
        <div class="grimlock-section__content section__content">
            <div class="grimlock-section__nav-menu section__nav-menu">
		        <?php
		        if ( term_exists( $this->props['menu'], 'nav_menu' ) || has_nav_menu( $this->props['theme_location'] ) ) :
			        wp_nav_menu( array(
				        'theme_location' => $this->props['theme_location'],
                        'menu'           => $this->props['menu'],
				        'container'      => false,
				        'depth'          => $this->props['menu_depth'],
			        ) );
		        endif; ?>
            </div><!-- .section__nav-menu -->
        </div><!-- .section__content -->
		<?php
	}
}
