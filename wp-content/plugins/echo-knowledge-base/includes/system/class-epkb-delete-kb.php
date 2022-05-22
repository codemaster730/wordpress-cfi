<?php

/**
 * Delete All KB
 *
 */
class EPKB_Delete_KB {

	private $message = array(); // error/warning/success messages

	function __construct() {
		// Handle manage kb buttons and other, set messages here
		$this->handle_form_actions();
	}

	/**
	 * Return HTML form to delete all KBs data
	 */
	public function get_delete_all_kbs_data_form() {

		// only administrators can handle this page
		if ( ! current_user_can('manage_options') ) {
			return '';
		}

		ob_start();

		// Deletion message
		if ( get_transient( '_epkb_delete_all_kb_data' ) ) {    ?>
			<div class="epkb-delete-all-data__message">
				<p><?php esc_html_e( 'All data will be deleted upon plugin uninstallation.', 'echo-knowledge-base' ); ?></p>
			</div>      <?php

		// Deletion form
		} else {    ?>
			<form class="epkb-delete-all-data__form" action="" method="post">
				<input type="hidden" name="_wpnonce_epkb_delete_all" value="<?php echo wp_create_nonce( '_wpnonce_epkb_delete_all' ); ?>">

				<p class="epkb-delete-all-data__form-title"><?php echo sprintf( esc_html__( 'Write "%s" in the below input box if you want to delete ALL KB data when plugin uninstalled. ' .
				                                                                    'This includes Articles, Categories, and KB options.', 'echo-knowledge-base' ), 'delete' ); ?></p>    <?php

				EPKB_HTML_Elements::text_basic( array(
					'value' => '',
					'name'    => 'delete_text',
				) );
				EPKB_HTML_Elements::submit_button_v2( __( 'Delete All', 'echo-knowledge-base' ), 'epkb_delete_all', '', '', false, '', 'epkb-error-btn' );   ?>

			</form>     <?php
		}

		// show any notifications
		foreach ( $this->message as $class => $message ) {
			echo  EPKB_HTML_Forms::notification_box_bottom( $message, '', $class );
		}

		return ob_get_clean();
	}

	// Handle actions that need reload of the page - manage tab and other from addons
	private function handle_form_actions() {
		/** @global wpdb $wpdb */
		global $wpdb;

		if ( empty( $_REQUEST['action'] ) ) {
			return;
		}

		// clear any messages
		$this->message = array();

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epkb_delete_all'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epkb_delete_all'], '_wpnonce_epkb_delete_all' ) ) {
		  $this->message['error'] = __( 'Error occurred', 'echo-knowledge-base' ) . ' (1)';
		  return;
		}

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
		  $this->message['error'] = __( 'You do not have permission.', 'echo-knowledge-base' );
		  return;
		}

		// ensure user wants to delete the KB data
		$action = EPKB_Utilities::post( 'action' );
		if ( empty($action) || $action != 'epkb_delete_all' ) {
          $this->message['error'] = __( 'Error occurred', 'echo-knowledge-base' ) . ' (2)';
          return;
		}

		// ensure user typed delete word
		if ( EPKB_Utilities::post( 'delete_text' ) != 'delete' ) {
			$this->message['error'] = sprintf( __( 'Write "%s" in input box to delete ALL KB data', 'echo-knowledge-base' ), 'delete' );
			return;
		}

		$db_kb_config = new EPKB_KB_Config_DB();
		$all_kb_ids = $db_kb_config->get_kb_ids();
		foreach ( $all_kb_ids as $kb_id ) {
			self::delete_kb_data( $kb_id );
		}

		// Remove all database tables
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "epkb_kb_search_data" );
		$wpdb->query( "DROP TABLE IF EXISTS " . $wpdb->prefix . "epkb_article_ratings" );

		set_transient( '_epkb_delete_all_kb_data', true, DAY_IN_SECONDS );

       $this->message['success'] = __( 'All articles and categories deleted. Options will be deleted when plugin is uninstalled.', 'echo-knowledge-base' );
	}

	/**
	 * Delete given KB data
	 * @param $kb_id
	 */
	private function delete_kb_data( $kb_id ) {

		// delete all KB post type posts
		$post_type = EPKB_KB_Handler::get_post_type( $kb_id );
		$kb_posts = get_posts( array(
				'post_type'   => $post_type,
				'post_status' => 'any',
				'posts_per_page' => -1,
			)
		);
		if ( ! empty($kb_posts) ) {
			foreach ($kb_posts as $post) {
				if ( EPKB_KB_Handler::is_kb_post_type($post->post_type) && $post->post_type == $post_type ) {
					wp_delete_post($post->ID, true);
				}
			}
		}

		// delete all KB categories and terms
		$kb_category = EPKB_KB_Handler::get_category_taxonomy_name( $kb_id );
		$kb_tag = EPKB_KB_Handler::get_tag_taxonomy_name( $kb_id );

		// Delete all KB CATEGORIES
		$terms = get_terms( $kb_category, array('hide_empty' => false) );
		if ( ! is_wp_error($terms) && is_array($terms) ) {
			foreach( $terms as $term ) {
				if ( isset($term->term_id) && $term->taxonomy == $kb_category )
					wp_delete_term( $term->term_id, $term->taxonomy );
			}
		}

		// Delete all KB TERMS
		$terms = get_terms( $kb_tag, array('hide_empty' => false) );
		if ( ! is_wp_error($terms) && is_array($terms) ) {
			foreach( $terms as $term ) {
				if ( isset($term->term_id) && $term->taxonomy == $kb_tag )
					wp_delete_term( $term->term_id, $term->taxonomy );
			}
		}
	}
}