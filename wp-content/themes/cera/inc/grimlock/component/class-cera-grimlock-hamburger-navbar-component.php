<?php
/**
 * Class Cera_Grimlock_Hamburger_Navbar_Component
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_Grimlock_Hamburger_Navbar_Component' ) ) :

	/**
	 * Cera Grimlock Haburger Navbar Component class.
	 */
	class Cera_Grimlock_Hamburger_Navbar_Component extends Grimlock_Hamburger_Navbar_Component {

		/**
		 * Output the navbar brand.
		 */
		protected function render_brand() {
			if ( ! empty( $this->props['secondary_logo_displayed'] ) ) : ?>

				<div class="navbar-brand navbar-brand-secondary">
					<?php do_action( 'grimlock_site_identity', array( 'secondary' => true ) ); ?>
				</div><!-- .navbar-brand-secondary -->

			<?php endif; ?>

			<div class="navbar-brand">
				<?php do_action( 'grimlock_site_identity' ); ?>
			</div><!-- .navbar-brand -->

			<?php
		}

		/**
		 * Output the navbar collapsible search form.
		 *
		 * @since 1.0.0
		 */
		function render_search_form() {
			if ( true == $this->props['search_form_displayed'] ) : ?>
				<ul class="nav navbar-nav navbar-nav--search d-none d-lg-flex pr-5">
					<li class="menu-item">
						<div class="navbar-search d-flex align-items-center">
							<?php get_search_form(); ?>
						</div><!-- .navbar-search -->
					</li><!-- .menu-item -->
				</ul><!-- .navbar-nav -->
			<?php
			endif;
		}

		/**
		 * Output the navbar collapsible search form.
		 *
		 * @since 1.0.0
		 */
		function render_secondary_nav_menu() {
			do_action( 'cera_grimlock_navbar_secondary_nav_menu', array(
				'container'  => false,
				'menu_class' => 'hamburger-navbar-nav nav navbar-nav navbar-nav--hamburger-secondary-menu d-none d-lg-flex',
			) );
		}

		/**
		 * Display the current component with props data on page.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class( 'navbar-full' ); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?>>

			<div class="navbar__container">

				<div class="navbar__header">
					<button id="navbar-hidden-toggler" class="navbar-toggler navbar-toggler-right collapsed" type="button" data-toggle="collapse" data-target="#navigation-hidden-collapse"><span></span></button>
					<?php
						$this->render_toggler();
						$this->render_brand(); ?>
				</div><!-- .navbar__header -->

				<?php $this->render_search_form(); ?>
				<?php $this->render_secondary_nav_menu(); ?>

				<div class="hamburger-navbar-nav-menu-container d-none d-lg-flex col-auto pr-0">
					<?php $this->render_nav_menu(); ?>
				</div><!-- .collapse -->

			</div><!-- .navbar__container -->

			<!-- TODO : Add condition to show only for hidden navigation layout option -->
			<div class="collapse navbar-collapse" id="navigation-hidden-collapse">
				<div class="navbar-collapse-content">
					<?php $this->render_nav_menu(); ?>
					<?php $this->render_secondary_nav_menu(); ?>
				</div><!-- .navbar-collapse-content -->
			</div><!-- .navbar-collapse -->

			</<?php $this->render_el(); ?>><!-- .navbar -->
			<?php
		}
	}
endif;
