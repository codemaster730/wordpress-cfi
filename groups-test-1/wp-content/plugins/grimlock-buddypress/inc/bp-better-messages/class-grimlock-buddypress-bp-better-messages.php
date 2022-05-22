<?php
/**
 * Grimlock_BuddyPress_BP_Better_Messages Class
 *
 * @author   Themosaurus
 * @since    1.0.0
 * @package  grimlock-buddypress
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_BuddyPress_BP_Better_Messages
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock-buddypress
 */
class Grimlock_BuddyPress_BP_Better_Messages {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_buddypress_member_send_message_button_url', array( $this, 'change_buddypress_member_send_private_message_button_url' ), 10, 2 );
		add_action( 'wp_enqueue_scripts',                                 array( $this, 'enqueue_scripts'                                          ), 20    );
	}

	/**
	 * Change the url of the "Send Private Message" button
	 *
	 * @since 1.0.0
	 * @param string $url The url of the button.
	 * @param int    $user_id The id of the user to send the message to.
	 *
	 * @return string
	 */
	public function change_buddypress_member_send_private_message_button_url( $url, $user_id ) {
		$query_args = array(
			'new-message' => '',
			'to'          => bp_core_get_username( $user_id ),
		);

		if ( BP_Better_Messages()->settings['fastStart'] ) {
			$query_args['fast'] = '1';
		}

		$url = add_query_arg( $query_args, bp_loggedin_user_domain() . 'bp-messages' );

		return $url;
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.3.19
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'grimlock-buddypress-bp-better-messages-style', GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/css/bp-better-messages.css', array(), GRIMLOCK_BUDDYPRESS_VERSION );
	}
}

return new Grimlock_BuddyPress_BP_Better_Messages();
