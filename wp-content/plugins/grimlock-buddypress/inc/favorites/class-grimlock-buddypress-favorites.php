<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_BuddyPress_Favorites
 *
 * @author  themoasaurus
 * @since   1.3.19
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_Favorites {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'bp_before_member_header_meta', array( $this, 'user_favorites_count'      ) );
		add_action( 'bp_setup_nav',                 array( $this, 'add_profile_favorites_tab' ) );
	}

	/**
	 * Display the user favorites count
	 */
	public function user_favorites_count() {
		$user_favorites_count = get_user_favorites_count( bp_displayed_user_id() );
		if ( empty( $user_favorites_count ) ) {
			return;
		}
		?>
		<div class="grimlock-buddypress-user-favorites-count">
			<?php printf( esc_html( _n( '%1$s Favorite Post', '%1$s Favorite Posts', $user_favorites_count, 'grimlock-buddypress' ) ), $user_favorites_count ); ?>
		</div>
		<?php
	}

	/**
	 * Add a "Favorites" tab in BP Profile
	 */
	public function add_profile_favorites_tab() {
		$user_favorites_count = get_user_favorites_count( bp_displayed_user_id() );
		$tab_name = esc_html__( 'Favorites', 'grimlock-buddypress' );
		if ( ! empty( $user_favorites_count ) ) {
			$tab_name .= '<span class="count">' . $user_favorites_count . '</span>';
		}

		bp_core_new_nav_item( array(
			'name'                => $tab_name,
			'slug'                => 'user-favorites',
			'screen_function'     => array( $this, 'user_favorites_screen' ),
			'position'            => 70,
			'parent_url'          => bp_loggedin_user_domain() . '/user-favorites/',
			'parent_slug'         => buddypress()->profile->slug,
		) );
	}

	/**
	 * Handle the "Favorites" tab render
	 */
	public function user_favorites_screen() {
		add_action( 'bp_template_title', array( $this, 'user_favorites_screen_title' ) );
		add_action( 'bp_template_content', array( $this, 'user_favorites_screen_content' ) );
		bp_core_load_template( 'buddypress/members/single/plugins' );
	}

	/**
	 * Display the "Favorites" tab title
	 */
	public function user_favorites_screen_title() {
		esc_html_e( 'Favorites', 'grimlock-buddypress' );
	}

	/**
	 * Display the "Favorites" tab content
	 */
	public function user_favorites_screen_content() {
		?>
		<div class="grimlock-buddypress-user-favorites mt-5">
			<?php the_user_favorites_list( bp_displayed_user_id(), null, true ); ?>
		</div>
		<?php
	}
}
