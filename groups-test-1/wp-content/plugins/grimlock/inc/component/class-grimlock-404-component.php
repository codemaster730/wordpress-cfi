<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_404_Component
 *
 * @author  themosaurus
 * @since   1.0.6
 * @package grimlock
 */
class Grimlock_404_Component extends Grimlock_Section_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'full_screen_displayed' => false,
			'search_form_displayed' => true,
			'class'                 => 'grimlock-404 error-404 not-found',
		) ) );
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @since 1.0.6
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 *
	 * @return       array        Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes = parent::get_class( $class );

		if ( ! empty( $this->props['full_screen_displayed'] ) ) {
			$classes[] = 'grimlock-404--full-screen-displayed';
		}

		return array_unique( $classes );
	}

	/**
	 * Display the search form.
	 *
	 * @since 1.0.6
	 */
	protected function render_search_form() {
		if ( ! empty( $this->props['search_form_displayed'] ) ) : ?>
			<div class="grimlock-section__search_form section__search_form">
				<?php get_search_form(); ?>
			</div>
		<?php endif;
	}

	/**
	 * Display the current component content.
	 *
	 * @since 1.0.6
	 */
	protected function render_content() {
		?>
		<div class="grimlock-section__content section__content">
			<?php $this->render_text(); ?>
			<?php $this->render_search_form(); ?>
		</div><!-- .section__content -->
		<?php
	}

	/**
	 * Display the current component header.
	 *
	 * @since 1.0.6
	 */
	protected function render_header() {
		?>
		<div class="grimlock-section__header section__header">
			<?php
			$this->render_title( 'h1' );
			$this->render_subtitle( 'h2' ); ?>
		</div><!-- .section__header -->
		<?php
	}
}
