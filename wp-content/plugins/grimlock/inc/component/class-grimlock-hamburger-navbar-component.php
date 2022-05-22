<?php
/**
 * Grimlock_Hamburger_Navbar_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Hamburger_Navbar_Component
 */
class Grimlock_Hamburger_Navbar_Component extends Grimlock_Navbar_Component {
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
		$classes[] = 'grimlock-navbar--hamburger';
		$classes[] = 'hamburger-navbar';
		return array_unique( $classes );
	}

	/**
	 * Output the navbar toggler.
	 *
	 * @since 1.0.0
	 */
	protected function render_toggler() {
		?>
        <button id="navbar-toggler" class="navbar-toggler navbar-toggler-right collapsed" type="button" data-toggle="collapse" data-target="<?php echo "#{$this->props['id']}-collapse"; ?>">
            <span></span>
        </button>
		<?php
	}

	/**
	 * Output the navbar nav menu.
	 *
	 * @since 1.0.0
	 */
	protected function render_nav_menu() {
		do_action( 'grimlock_hamburger_navbar_nav_menu', array(
			'container'  => false,
			'menu_class' => 'hamburger-navbar-nav nav navbar-nav navbar-nav--hamburger-menu',
		) );
	}

	/**
	 * Output the navbar collapsible search form.
	 *
	 * @since 1.0.0
	 */
	protected function render_search_form() {
		if ( ! empty( $this->props['search_form_displayed'] ) ) : ?>
			<ul class="nav navbar-nav grimlock-navbar-nav--search navbar-nav--search">
				<li class="grimlock-menu-item menu-item">
					<?php if ( ! empty( $this->props['search_form_modal_displayed'] ) ) : ?>
						<div class="d-lg-none">
							<?php get_search_form(); ?>
						</div>
						<div class="grimlock-navbar-search grimlock-navbar-search--animate navbar-search navbar-search--animate d-none d-lg-block">
							<button type="button" class="grimlock-navbar-search__icon navbar-search__icon" data-toggle="modal" data-target="#grimlock-modal-search">
								<i class="fa fa-search"></i>
							</button>
						</div><!-- .navbar-search -->
					<?php else : ?>
						<div class="grimlock-navbar-search grimlock-navbar-search--animate navbar-search navbar-search--animate">
							<?php get_search_form(); ?>
							<span class="grimlock-search-icon search-icon"><i class="fa fa-search"></i></span>
						</div><!-- .navbar-search -->
					<?php endif; ?>
				</li>
			</ul>
		<?php
		endif;
	}

	/**
	 * Output the navbar brand.
	 */
	protected function render_brand() {
		if ( ! empty( $this->props['secondary_logo_displayed'] ) ) : ?>
			<div class="navbar-brand navbar-brand-secondary">
				<?php do_action( 'grimlock_site_identity', array( 'secondary' => true ) ); ?>
			</div><!-- .navbar-brand -->
		<?php endif;
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class( 'grimlock-navbar-full navbar-full' ); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?>>
			<div class="grimlock-navbar__container navbar__container">
				<div class="grimlock-navbar__header navbar__header">
					<?php
					$this->render_toggler();
					$this->render_brand(); ?>
				</div><!-- .navbar__header -->
				<div class="grimlock-hamburger-navbar-nav-menu-container hamburger-navbar-nav-menu-container d-none">
					<?php $this->render_nav_menu(); ?>
					<?php $this->render_search_form(); ?>
				</div><!-- .collapse -->
			</div><!-- .navbar__container -->
			</<?php $this->render_el(); ?>><!-- .hamburger-navbar -->
		<?php endif;
	}
}
