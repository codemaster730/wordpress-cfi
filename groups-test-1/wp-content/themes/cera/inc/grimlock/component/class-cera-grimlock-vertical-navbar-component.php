<?php
/**
 * Class Cera_Grimlock_Vertical_Navbar_Component
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Cera_Grimlock_Vertical_Navbar_Component' ) ) :

	/**
	 * Cera Grimlock Haburger Navbar Component class.
	 */
	class Cera_Grimlock_Vertical_Navbar_Component extends Grimlock_Vertical_Navbar_Component {

		/**
		 * Output the navbar brand.
		 *
		 * @since 1.0.0
		 */
		protected function render_brand() {
			?>
			<div class="vertical-navbar-brand">
				<?php do_action( 'grimlock_site_identity' ); ?>
				<button id="navbar-toggler-mini" class="navbar-toggler collapsed d-none d-lg-flex" type="button">
					<i class="cera-icon cera-menu-arrow"></i>
				</button>
				<button id="navbar-toggler-mobile" class="navbar-toggler slideout-close d-lg-none" type="button">
					<span></span>
				</button>
			</div><!-- .vertical-navbar-brand -->
			<?php
		}

		/**
		 * Display the current component with props data on page.
		 *
		 * @since 1.0.0
		 */
		public function render() {
			?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?>>
			<div class="navbar-wrapper">
				<?php
				$this->render_brand();
				$this->render_search_form();
				do_action('cera_vertical_navbar_sidebar_top');
				$this->render_nav_menu();
				do_action('cera_vertical_navbar_sidebar_bottom'); ?>
			</div><!-- .navbar-wrapper-->
			</<?php $this->render_el(); ?>><!-- .vertical-navbar -->
			<?php
		}
	}
endif;
