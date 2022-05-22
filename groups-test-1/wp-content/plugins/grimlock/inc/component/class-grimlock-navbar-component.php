<?php
/**
 * Grimlock_Navbar_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Navbar_Component
 */
class Grimlock_Navbar_Component extends Grimlock_Component {
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
			'layout'                => '',
			'container_layout'      => '',
			'class'                 => '',
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
		$classes[] = 'grimlock-navbar';
		$classes[] = 'navbar';

		if ( ! empty( $this->props['layout'] ) ) {
			$classes[] = "grimlock-navbar--{$this->props['layout']}";
			$classes[] = "navbar--{$this->props['layout']}";
		}

		if ( ! empty( $this->props['container_layout'] ) ) {
			$classes[] = "grimlock-navbar--container-{$this->props['container_layout']}";
			$classes[] = "navbar--container-{$this->props['container_layout']}";
		}

		return array_unique( $classes );
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
	 * Output the navbar toggler.
	 *
	 * @since 1.0.0
	 */
	protected function render_toggler() {
		?>
        <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="<?php echo "#{$this->props['id']}-collapse"; ?>" aria-controls="<?php echo "{$this->props['id']}-collapse"; ?>" aria-expanded="false" aria-label="Toggle navigation">
            <span></span>
        </button>
		<?php
	}

	/**
	 * Output the navbar brand.
	 *
	 * @since 1.0.0
	 */
	protected function render_brand() {
		?>
        <div class="navbar-brand">
            <?php do_action( 'grimlock_site_identity' ); ?>
        </div><!-- .navbar-brand -->
		<?php
	}

	/**
	 * Output the navbar nav menu.
	 *
	 * @since 1.0.0
	 */
	protected function render_nav_menu() {
		do_action( 'grimlock_navbar_nav_menu', array(
			'container'  => false,
			'menu_class' => 'nav navbar-nav grimlock-navbar-nav--main-menu navbar-nav--main-menu',
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
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class( 'grimlock-navbar-expand-lg navbar-expand-lg' ); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?>>
			<div class="grimlock-navbar__container navbar__container">
				<div class="grimlock-navbar__header navbar__header">
					<?php
					$this->render_toggler();
					$this->render_brand(); ?>
				</div><!-- .navbar__header -->
				<div class="collapse grimlock-navbar-collapse navbar-collapse" id="<?php echo "{$this->props['id']}-collapse"; ?>">
					<div class="grimlock-navbar-collapse-content navbar-collapse-content">
						<?php
						$this->render_nav_menu();
						$this->render_search_form(); ?>
					</div>
				</div><!-- .collapse -->
			</div><!-- .navbar__container -->
			</<?php $this->render_el(); ?>><!-- .navbar -->
		<?php endif;
	}
}
