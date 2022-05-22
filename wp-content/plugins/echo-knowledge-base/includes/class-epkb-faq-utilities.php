<?php

/**
 * Various utility functions
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_FAQ_Utilities {

	/**
	 *
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 *
	 * Get all existing FAQ Shortcode categories.
	 *
	 * @param string $order_by
	 * @return array|null - return array of categories (empty if not found) or null on error
	 */
	public static function get_faq_shortcode_categories_unfiltered( $order_by='name' ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		$order = $order_by == 'name' ? 'ASC' : 'DESC';
		$order_by = $order_by == 'date' ? 'term_id' : $order_by;   // terms don't have date so use id
		$faq_category_taxonomy_name = EPKB_FAQ_Handler::get_faq_shortcode_taxonomy_name();
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.*, tm.*, tm.meta_value AS shortcode
												   		FROM $wpdb->terms AS t 
												   		INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
												   		LEFT JOIN $wpdb->termmeta AS tm ON t.term_id = tm.term_id AND tm.meta_key = %s
												   		WHERE tt.taxonomy = %s 
												   		ORDER BY " . esc_sql('t.' . $order_by) . ' ' . $order . ' ',
											EPKB_FAQ_HANDLER::FAQ_PAGE_SHORTCODE__META, $faq_category_taxonomy_name ) );

		return isset($result) && is_array($result) ? $result : null;
	}

	/**
	 * USED TO HANDLE ALL CATEGORIES REGARDLESS OF USER PERMISSIONS.
	 * Get all existing Help Dialog Location categories.
	 *
	 * @param string $order_by
	 *
	 * @return array|null - return array of categories (empty if not found) or null on error
	 */
	public static function get_help_dialog_location_categories_unfiltered( $order_by='name' ) {
		/** @var wpdb $wpdb */
		global $wpdb;

		$order = $order_by == 'name' ? 'ASC' : 'DESC';
		$order_by = $order_by == 'date' ? 'term_id' : $order_by;   // terms don't have date so use id
		$faq_category_taxonomy_name = EPKB_Help_Dialog_Handler::get_help_dialog_location_taxonomy_name();
		$result = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.description, tm.term_id, tm.meta_value, tm.meta_key
												   		FROM $wpdb->terms AS t 
												   		INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id
												   		LEFT JOIN $wpdb->termmeta AS tm ON t.term_id = tm.term_id
												   		WHERE tt.taxonomy = %s AND tm.term_id IN (
												   			SELECT term_id
												   			FROM $wpdb->termmeta
												   			WHERE meta_key = %s
												   		)
												   		ORDER BY " . esc_sql('t.' . $order_by) . ' ' . $order . ' ',
										$faq_category_taxonomy_name,
										EPKB_Help_Dialog_Handler::HELP_DIALOG_LOCATION_META	 ) );

		if ( ! isset($result) || ! is_array($result) ) {
			return null;
		}

		$named_result = [];
		foreach( $result as $row ) {

			if ( empty($row->term_id) ) {
				continue;
			}

			if ( empty( $named_result[$row->term_id] ) ) {
				$named_result[$row->term_id] = $row;
				$named_result[$row->term_id]->location_id = $row->term_id;
			}

			if ( $row->meta_key == EPKB_Help_Dialog_Handler::HELP_DIALOG_LOCATION_META ) {
				$named_result[$row->term_id]->locations = maybe_unserialize( $row->meta_value );
				continue;
			}

			if ( $row->meta_key == EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_META ) {
				$named_result[$row->term_id]->status = $row->meta_value;
				continue;
			}

			if ( $row->meta_key == EPKB_Help_Dialog_Handler::HELP_DIALOG_KB_IDS ) {
				$named_result[$row->term_id]->kb_ids = maybe_unserialize( $row->meta_value );
				continue;
			}
		}
		
		foreach( $named_result as $key => $row ) {
			unset( $named_result[$key]->meta_key );
			unset( $named_result[$key]->meta_value );
			unset( $named_result[$key]->term_id );
			unset( $named_result[$key]->term_group );
			unset( $named_result[$key]->slug );
		}

		// normalize
		foreach( $named_result as $key => $row ) {

			if ( empty( $named_result[$key]->status ) || $named_result[$key]->status !== EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_PUBLIC ) {
				$named_result[$key]->status = EPKB_Help_Dialog_Handler::HELP_DIALOG_STATUS_DRAFT;
			}

			if ( empty( $named_result[$key]->kb_ids ) || ! is_array( $named_result[$key]->kb_ids ) ) {
				$named_result[$key]->kb_ids = [];
			}

			if ( empty( $named_result[$key]->locations ) || ! is_array( $named_result[$key]->locations ) ) {
				$named_result[$key]->locations = [
					'selected_pages' => [
						'page' => [],
						'post' => [],
						'cpt' => []
					],
					'excluded_pages' => [
						'page' => [],
						'post' => [],
						'cpt' => []
					]
				];
			}

			if ( empty( $named_result[$key]->locations['selected_pages'] ) ) {
				$named_result[$key]->locations['selected_pages'] = [
					'page' => [],
					'post' => [],
					'cpt' => []
				];
			}

			if ( empty( $named_result[$key]->locations['excluded_pages'] ) ) {
				$named_result[$key]->locations['excluded_pages'] = [
					'page' => [],
					'post' => [],
					'cpt' => []
				];
			}

			if ( empty( $named_result[$key]->name ) ) {
				$named_result[$key]->name = '';
			}
		}

		return $named_result;
	}

	/**
	 * Get FAQ-SPECIFIC option. Function adds FAQ ID suffix. Prefix represent core or ADD-ON prefix.
	 * WARN: Use ep.faq_get_instance()->faq_config_obj->get_faq_configs() to get FAQ specific configuration.
	 *
	 * @param $faq_shortcode_id - assuming it is a valid ID
	 * @param $option_name - without faq suffix
	 * @param $default - use if FAQ option not found
	 * @param bool $is_array - ensure returned value is an array, otherwise return default
	 *
	 * @return string|array|null or default
	 */
	public static function get_faq_shortcode_option( $faq_shortcode_id, $option_name, $default, $is_array=false ) {
		$full_option_name = $option_name . '_' . $faq_shortcode_id;
		return EPKB_Utilities::get_wp_option( $full_option_name, $default, $is_array );
	}

	/**
	 * Save FAQ-SPECIFIC option. Function adds FAQ ID suffix. Prefix represent core or ADD-ON prefix.
	 *
	 * @param $faq_shortcode_id - assuming it is a valid ID
	 * @param $option_name - without faq suffix
	 * @param array $option_value
	 * @param $sanitized - ensures input is sanitized
	 *
	 * @return array|WP_Error if option cannot be serialized or db insert failed
	 */
	public static function save_faq_shortcode_option( $faq_shortcode_id, $option_name, $option_value, $sanitized ) {
		$full_option_name = $option_name . '_' . $faq_shortcode_id;
		return EPKB_Utilities::save_wp_option( $full_option_name, $option_value, $sanitized );
	}

	/**
	 * Check if FAQ is DRAFT.
	 *
	 * @param $faq_status
	 * @return bool
	 */
	public static function is_faq_draft( $faq_status ) {
		return $faq_status === 'draft';
	}

	/**
	 * Check if FAQ is ARCHIVED.
	 *
	 * @param $faq_status
	 * @return bool
	 */
	public static function is_faq_archived( $faq_status ) {
		return $faq_status === 'archived';
	}

	/**
	 * Retrieve user IP address if possible.
	 *
	 * @return string
	 */
	public static function get_ip_address() {

		$ip_params = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR' );
		foreach ( $ip_params as $ip_param ) {
			if ( ! empty($_SERVER[$ip_param]) ) {
				foreach ( explode( ',', $_SERVER[$ip_param] ) as $ip ) {
					$ip = trim( $ip );

					// validate IP address
					if ( filter_var( $ip, FILTER_VALIDATE_IP ) !== false ) {
						return esc_attr( $ip );
					}
				}
			}
		}

		return '';
	}

	/**
	 * Return url of a random article where the location will shown.
	 * $location = object from get_help_dialog_location_categories_unfiltered()
	 *
	 * @param $location
	 * @return false|string
	 */
	public static function get_first_location_page_url( $location ) {

		if ( empty( $location ) || empty( $location->locations ) || empty( $location->locations['selected_pages']['page'] ) ) {
			return home_url();
		}
		
		// check home page 
		if ( in_array( 0, $location->locations['selected_pages']['page'] ) ) {
			return home_url();
		}
			
		$types = ['page', 'post', 'cpt'];
		
		// check if there some included pages/posts/cpt 
		foreach ( $types as $type ) {
			
			if ( empty( $location->locations['selected_pages'][$type] ) ) {
				continue;
			}
			
			foreach ( $location->locations['selected_pages'][$type] as $page_id ) {
				
				$post = get_post( $page_id );
				if ( $post ) {
					return get_the_permalink( $post );
				}
			}
		}
		
		// check if there some excluded pages/posts/cpt 
		foreach ( $types as $type ) {
			
			if ( empty( $location->locations['excluded_pages'][$type] ) ) {
				continue;
			}
			
			$post_types = 'cpt' ?  get_post_types( [ '_builtin'  => 0, 'public'    => 1 ] ) : $type;
			
			$posts = get_posts( [
				'post_type'              => $post_types,
				'post_status'            => array('publish', 'private'),
				'numberposts'         => 1,
				'post__not_in' => $location->locations['excluded_pages'][$type]
			] );
			
			if ( $posts ) {
				return get_the_permalink( $posts[0] );
			}
		}
			
		return false;
	}

	/**
	 * Find location ID for given page or false if page does not belong to any location.
	 * @param $page_id
	 * @param $page_type
	 * @param array $locations
	 * @return bool
	 */
	public static function is_page_in_locations( $page_id,  $page_type, $locations = [] ) {

		if ( ! is_array( $locations ) ) {
			return false;
		}
		
		foreach ( $locations as $location ) {
			if ( ! empty( $location->location_id ) && self::is_page_in_location( $page_id, $page_type, $location ) ) {
				return $location->location_id;
			}
		}
		
		return false;
	}

	/**
	 * Is given page part of the location and not excluded?
	 * @param $page_id
	 * @param $page_type
	 * @param $location
	 * @return bool
	 */
	public static function is_page_in_location( $page_id, $page_type, $location ) {
		
		if ( empty( $location ) || empty( $location->locations ) || ! isset ( $location->locations['selected_pages'][$page_type] ) ) {
			return false;
		}

		$selected_pages = empty( $location->locations['selected_pages'] ) ? [] : $location->locations['selected_pages'];
		$excluded_pages = empty( $location->locations['excluded_pages'] ) ? [] : $location->locations['excluded_pages'];
		
		if ( in_array( $page_id, $selected_pages[$page_type] ) ) {
			return true;
		}
		
		if ( empty( $excluded_pages[$page_type] ) ) {
			return false;
		}
		
		if ( in_array( -1, $excluded_pages[$page_type] ) || ! in_array( $page_id, $excluded_pages[$page_type] ) ) {
			return true;
		}
		
		return false;
	}

	public static function show_remove_hd_notice() { ?>
		<div class="epkb-admin__section-wrap epkb-admin__section-wrap__hd-remove-notice"><?php

	    $descr = '<p>' . sprintf( esc_html__( 'Thank you for trying our beta Help Dialog feature. We decided to move Help Dialog to a separate plugin so it can be available to a larger community. Unfortunately you will need to move your data to the new plugin once it is available.', 'echo-knowledge-base'), '<a class="" href="https://www.helpdialog.com/" target="_blank">' . __( 'here', 'echo-knowledge-base' ) . '</a>');
	    $descr .= '</p><p>';
	    $descr .=  EPKB_Utilities::contact_us_for_support();
		$descr .= '</p><p>';
		$descr .= esc_html__( 'This Help Dialog version will be removed in the next release.', 'echo-knowledge-base');
        $descr .= '</p>';

        EPKB_HTML_Forms::notification_box_top( array(
        'type' => 'warning',
        'title' => '',
        'desc' => $descr,
        ) ); ?>
		</div> <?php
	}
} 