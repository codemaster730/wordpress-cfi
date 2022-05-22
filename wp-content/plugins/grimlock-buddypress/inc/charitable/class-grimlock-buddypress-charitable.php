<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_BuddyPress_Charitable
 *
 * @author  themoasaurus
 * @since   1.4.0
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_Charitable {
	/**
	 * Setup class.
	 */
	public function __construct() {
		add_action( 'bp_setup_nav', array( $this, 'add_profile_donations_tab' ) );
	}

	/**
	 * Add a "Donations" tab in BP Profile
	 */
	public function add_profile_donations_tab() {
		bp_core_new_nav_item( array(
			'name'                    => esc_html__( 'Donations', 'grimlock-buddypress' ),
			'slug'                    => 'donations',
			'screen_function'         => array( $this, 'donations_screen' ),
			'show_for_displayed_user' => false,
			'position'                => 70,
			'parent_url'              => bp_loggedin_user_domain() . '/donations/',
			'parent_slug'             => buddypress()->profile->slug,
		) );
	}

	/**
	 * Handle the "Donations" tab render
	 */
	public function donations_screen() {
		add_action( 'bp_template_title', array( $this, 'donations_screen_title' ) );
		add_action( 'bp_template_content', array( $this, 'donations_screen_content' ) );
		bp_core_load_template( 'buddypress/members/single/plugins' );
	}

	/**
	 * Display the "Donations" tab title
	 */
	public function donations_screen_title() {
		esc_html_e( 'Donations', 'grimlock-buddypress' );
	}

	/**
	 * Display the "Donations" tab content
	 */
	public function donations_screen_content() {
		?>
		<div class="grimlock-buddypress-donations mt-5">
			<?php echo do_shortcode( '[charitable_donations]' ); ?>
		</div>
		<?php
	}
}
