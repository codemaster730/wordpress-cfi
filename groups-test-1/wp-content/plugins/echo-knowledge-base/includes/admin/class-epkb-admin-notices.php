<?php

/**
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Admin_Notices {

	static $ongoing_notice_removal = array();

	public function __construct( $dismiss_notice=false ) {
		if ( $dismiss_notice ) {
			add_action( 'wp_ajax_epkb_dismiss_ongoing_notice', array( $this, 'ajax_dismiss_ongoing_notice') );
			return;
		}

		add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
	}

	/**
	 * Show noticers for admin at the top of the page
	 */
	public function show_admin_notices() {

		$is_kb_request = EPKB_KB_Handler::is_kb_request();

		// ONE TIME notice is deleted right after it is displayed
		$notices = get_option( 'epkb_one_time_notices', array() );
		if ( ! empty($notices) && $is_kb_request ) {
			delete_option( 'epkb_one_time_notices' );
		}

		// display ONE TIME and LONG notices
		$notice = get_option( 'epkb_ongoing_notices', array() );
		if ( ! empty($notice) ) {
			$notices += $notice;
		}

		$update_notices = false;
		foreach ( $notices as $key => $notice ) {

			if ( ! isset($notice['type']) ) {
				unset($notices[$key]);
				$update_notices = true;
				continue;
			}

			if ( isset($notice['id']) && in_array($notice['id'], self::$ongoing_notice_removal) ) {
				unset($notices[$key]);
				$update_notices = true;
				continue;
			}

			// show notices on KB pages only
			if ( ! $is_kb_request ) {
				continue;
			}

			// only editors and admins should see the notice messages
			// $user = EPKB_Utilities::get_current_user();
			if ( function_exists('wp_get_current_user') && ! current_user_can('editor') && ! current_user_can('administrator') ) {
				continue;
			}

			if ( $notice['type'] == 'large-notice' || $notice['type'] == 'large-info' ) { ?>
				<div
					class="epkb-notice epkb-notice-large-box notice  notice-<?php echo $notice['type']; ?> notice-<?php echo $notice['id']; ?>">
					<div class="epkb-notice-icon"><?php echo $notice['icon']; ?></div>
					<div class="epkb-notice-text">
						<h3><?php echo $notice['title']; ?></h3>
						<p><?php echo $notice['text']; ?></p> <?php
						if ( ! empty( $notice['id'] ) ) { ?>
							<!-- TODO <a href="#" class="epkb-notice-remind epkb-notice-btn btn-green"><?php _e( 'Remind Me Later', 'echo-knowledge-base' ); ?></a> -->
							<a href="#" class="epkb-notice-dismiss epkb-notice-btn btn-grey" data-notice-id="<?php echo $notice['id']; ?>">
								 <?php _e( 'Dismiss', 'echo-knowledge-base' ); ?></a>                        <?php
						} ?>
					</div>
				</div>      <?php
			} else { ?>
				<div
					class="epkb-notice notice notice-<?php echo $notice['type']; ?> notice-<?php echo $notice['id']; ?>"
					style="display:block;">
					<p>                        <?php
						echo $notice['text'];
						if ( ! empty( $notice['id'] ) ) { ?>
							&nbsp;
							<a href="#" class="epkb-notice-dismiss" data-notice-id="<?php echo $notice['id']; ?>"><?php _e( 'Dismiss', 'echo-knowledge-base' ); ?></a>                        <?php
						} ?>
					</p>
				</div>      <?php
			}
		}

		// some notices are not valid any more or there have invalid data so remove them
		if ( $update_notices ) {
			update_option( 'epkb_ongoing_notices', $notices );
		}
	}

	/**
	 * ONE TIME notice appears only once.
	 * NOTE: this notice should not always happen. It should happen only once when the if user does some action.
	 *
	 * @param string $type - 'warning', 'error', 'info'
	 * @param string $text
	 */
	public static function add_one_time_notice( $type='warning', $text='' ) {
		$notices = get_option( 'epkb_one_time_notices', array() );
		$notices[] = array(
			'type' => $type,
			'id' => '',
			'icon'	=> '',
			'title' => '',
			'text' => $text
		);
		update_option( 'epkb_one_time_notices', $notices );
	}

	/**
	 * LONG TIME notice appears until user dismiss it. We also need to take care of case when the ongoing notice is not valid any more.
	 *
	 * @param string $type - 'warning', 'error', 'info'
	 * @param string $id - unique notice id string e.g. epkb_elementor_settings
	 * @param string $text
	 * @param string $title
	 * @param string $icon
	 */
	public static function add_ongoing_notice( $type='warning', $id='', $text='', $title='', $icon='' ) {

		// update current ongoing notices if needed
		$notices = get_option( 'epkb_ongoing_notices', array() );

		// Check if user already dismissed the notice
		if ( get_user_meta( get_current_user_id(), $id, true ) ) {
			if ( ! empty($notices[$id]) ) {
				unset( $notices[$id] );
				update_option( 'epkb_ongoing_notices', $notices );
			}
			return;
		}

		if ( ! isset($notices[$id]) ) {

			$notices[$id] = array(
				'type'  => $type,
				'id'    => $id,
				'icon'  => $icon,
				'title' => $title,
				'text'  => $text
			);
			update_option( 'epkb_ongoing_notices', $notices );
		}

		// if we are adding notice we don't want to remove it
		if ( isset(self::$ongoing_notice_removal[$id]) ) {
			unset(self::$ongoing_notice_removal[$id]);
		}
	}

	/**
	 * Do not show particular ongoing notice
	 * @param $id
	 */
	public static function remove_ongoing_notice( $id ) {
		self::$ongoing_notice_removal[$id] = $id;
	}

	/**
	 * Let user to see the ongoing notice again (notice condition is true again)
	 * @param $id
	 */
	public static function remove_dismissed_ongoing_notice( $id ) {
		delete_user_meta( get_current_user_id(), $id );
	}

	/**
	 * User dismissed ongoing notice so record it
	 */
	public static function ajax_dismiss_ongoing_notice() {
		if ( isset($_POST['epkb_dismiss_id']) ) {
			update_user_meta( get_current_user_id(), $_POST['epkb_dismiss_id'], 1 );
		   self::dismiss_ongoing_notice( $_POST['epkb_dismiss_id'] );
		}
	}

	/**
	 * Dismiss ongoing notice
	 * @param string $id
	 */
	public static function dismiss_ongoing_notice( $id = '' ) {
		if ( empty($id) ) {
			delete_option( 'epkb_ongoing_notices' );
			return;
		}

		$notices = get_option( 'epkb_ongoing_notices', array() );
		if ( isset($notices[$id]) ) {
			unset( $notices[$id] );
		   update_option( 'epkb_ongoing_notices', $notices );
	  }
	}

}
