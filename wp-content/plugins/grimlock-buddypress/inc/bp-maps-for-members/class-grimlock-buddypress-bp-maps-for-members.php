<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_BuddyPress_BP_Maps_For_Members
 *
 * @author  themosaurus
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_BP_Maps_For_Members {
	/**
	 * Setup class.
	 */
	public function __construct() {
		add_filter( 'body_class',   array( $this, 'add_body_class' ), 10 );
		add_action( 'widgets_init', array( $this, 'widgets_init'   ), 20 );
	}

	/**
	 * Add body class on members map page
	 *
	 * @param array $class The array of body classes
	 * 
	 * @return array The modified array of body classes
	 */
	public function add_body_class( $class ) {
		$requested_url = bp_get_requested_url();
		if( ( strpos( $requested_url, BP_MEMBERS_SLUG ) !== false ) && ( strpos( $requested_url, 'membersmap' ) !== false ) ) {
			$class[] = 'grimlock-buddypress-membersmap';
		}

		return $class;
	}

	/**
	 * Register widget areas
	 */
	public function widgets_init(){
		register_sidebar( array(
			'id'            => 'bp-members-map-filters',
			'name'          => esc_html__( 'BP Members Map Filters', 'grimlock-buddypress' ),
			'description'   => esc_html__( 'The filters area for the members map', 'grimlock-buddypress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) );
	}
}
