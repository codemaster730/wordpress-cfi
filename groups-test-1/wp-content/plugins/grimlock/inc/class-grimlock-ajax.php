<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc
 */
class Grimlock_Ajax {
	/**
	 * Setup plugin class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_grimlock_ajax_terms', array( $this, 'terms' ), 10 );
	}

	/**
	 * Retrieve terms for a given taxonomy and print HTML.
	 *
	 * @since 1.0.0
	 */
	public function terms() {
		check_ajax_referer( 'grimlock_ajax_terms', 'ajax_nonce' );

		$html  = '<option value="">' . esc_html__( '- Select -', 'grimlock' ) . '</option>';
		$terms = get_terms( array(
			'taxonomy'   => $_POST['taxonomy'],
			'hide_empty' => false,
		) );

		if ( ! is_wp_error( $terms ) && is_array( $terms ) ) {
			foreach( $terms as $term ) {
				$html .= "<option value='{$term->term_id}'>{$term->name}</option>";
			}
		}

		echo $html;
		wp_die();
	}
}

return new Grimlock_Ajax();