<?php
/**
 * Grimlock_Fat_Navbar_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Fat_Navbar_Component
 */
class Grimlock_Fat_Navbar_Component extends Grimlock_Navbar_Component {
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
		$classes[] = 'grimlock-navbar--fat';
		$classes[] = 'fat-navbar';
		return array_unique( $classes );
	}

	/**
	 * Output the standard search form template.
	 *
	 * @since 1.0.0
	 */
	protected function render_search_form() {
		if ( $this->props['search_form_displayed'] ) {
			get_search_form();
		}
	}
}
