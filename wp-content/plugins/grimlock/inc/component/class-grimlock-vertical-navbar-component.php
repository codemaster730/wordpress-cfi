<?php
/**
 * Grimlock_Vertical_Navbar_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Vertical_Navbar_Component
 */
class Grimlock_Vertical_Navbar_Component extends Grimlock_Component {
	/**
	 * Grimlock_Navbar_Component constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param array $props
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'search_form_displayed' => true,
			'class'                 => '',
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
		$classes[] = 'grimlock-vertical-navbar';
		$classes[] = 'vertical-navbar';
		return array_unique( $classes );
	}

	/**
	 * Output the navbar brand.
	 *
	 * @since 1.0.0
	 */
	protected function render_brand() {
		?>
		<div class="grimlock-vertical-navbar-brand vertical-navbar-brand">
			<?php do_action( 'grimlock_site_identity' ); ?>
		</div><!-- .vertical-navbar-brand -->
		<?php
	}

	/**
	 * Output the navbar nav menu.
	 *
	 * @since 1.0.0
	 */
	protected function render_nav_menu() {
		do_action( 'grimlock_vertical_navbar_nav_menu', array(
			'container'  => false,
			'menu_class' => 'vertical-navbar-nav sidebar-nav nav navbar-nav navbar-nav--main-menu nav-pills nav-stacked',
		) );
	}

	/**
	 * Output the navbar collapsible search form.
	 *
	 * @since 1.0.0
	 */
	protected function render_search_form() {
		if ( true == $this->props['search_form_displayed'] ) : ?>
			<?php if ( ! empty( $this->props['search_form_modal_displayed'] ) ) : ?>
				<div class="grimlock-vertical-navbar-search grimlock-navbar-search vertical-navbar-search navbar-search">
					<div class="d-lg-none">
						<?php get_search_form(); ?>
					</div>
					<div class="d-none d-lg-block">
						<button type="button" class="grimlock-navbar-search__icon navbar-search__icon" data-toggle="modal" data-target="#grimlock-modal-search">
							<i class="fa fa-search"></i>
						</button>
					</div>
				</div><!-- .vertical-navbar-search -->
			<?php else : ?>
				<div class="grimlock-vertical-navbar-search grimlock-navbar-search vertical-navbar-search navbar-search">
					<?php get_search_form(); ?>
				</div><!-- .vertical-navbar-search -->
			<?php endif; ?>
		<?php endif;
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?>>
				<div class="grimlock-navbar-wrapper navbar-wrapper">
					<?php
					$this->render_brand();
					$this->render_search_form();
					$this->render_nav_menu(); ?>
				</div>
			</<?php $this->render_el(); ?>><!-- .vertical-navbar -->
		<?php endif;
	}
}
