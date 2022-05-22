<?php
/**
 * Grimlock template functions for BP Maps for Members.
 *
 * @package grimlock-buddypress
 */

 /**
  * Radial distance search members
  */
function grimlock_buddypress_bp_maps_for_members_gather_members( $key, $map_member_distance_measurement ) {

	$settings = get_site_option( 'bp-member-map-all-settings' );

	extract($settings);

	if ( isset( $map_limit_all ) && $map_limit_all > 0 ) {

	} else {
		$map_limit_all = 0;
	}
	$member_type = '';

	if ( isset( $_POST["member-type-filter"] ) && $_POST["member-type-filter"]  != '-1' ) {

		$member_type = sanitize_text_field( $_POST["member-type-filter"] );

	} else {

		if ( isset( $map_member_types ) ) {

			//write_log( 'from pp_mm_gather_members()' );
			//write_log($map_member_types);

			if ( ! empty( $map_member_types ) ) {

				$member_type = $map_member_types;
			}

		}

	}

	$user_ids = array ();

	if ( isset( $_POST['pp_member_search_center_coords'] ) && ! empty( $_POST['pp_member_search_center_coords'] ) ) {

		$coords = sanitize_text_field( $_POST['pp_member_search_center_coords'] );

		$coords = explode( ',', $coords );

		$lat = (float) $coords[0];
		$lng = (float) $coords[1];

		$radius = (int) sanitize_text_field( $_POST['pp_member_search_radius'] );

		$user_ids = array();

		$earthRadius = 3959;  // miles
		if ( $map_member_distance_measurement == 'kilometers' ) {
			$earthRadius = 6371;
		}

		// function is in the BP xProfile Location plugin
		$user_ids = pp_location_members_radial_distance( $lat, $lng, $radius, $key, $earthRadius );

		if ( empty ( $user_ids ) ) {

			$gather['members'] = $user_ids;

			$gather['member_type'] = $member_type;

			return $gather;

		}


	} elseif ( PP_BPS ) {

		//use BPS to filter member ids if that option is selected in Settings, filter func is in bp xprofile location
		if ( isset( $map_member_filter_bps ) ) {

			$user_ids = bps_filter_pp_location_member_ids( $user_ids );

		}

	} elseif ( PP_BOSS ) {

		if ( function_exists('bp_ps_get_request') ) {

			$request = bp_ps_get_request( 'search' );

			$request_keys = array_keys( $request );

			if ( ! empty( $request_keys ) ) {

				$members_boss = bp_ps_search( $request);

				if ( $members_boss['validated'] ) {

					$user_ids = $members_boss['users'];
				}

			}

		}

	}


	if ( isset( $_POST['pp_member_search_keywords'] ) && ! empty( $_POST['pp_member_search_keywords'] ) ) {
		$search_terms = bp_esc_like( wp_kses_normalize_entities( $_POST['pp_member_search_keywords'] ) );
	} else {
		$search_terms = false;
	}

	$user_ids = apply_filters( 'bp_maps_for_members_user_ids_filter', $user_ids );



	$member_type = apply_filters( 'bp_maps_for_members_type_filter', $member_type );

	$args = array(
		'type'				=> 'active',
		'per_page'			=> apply_filters( 'grimlock_buddypress_members_per_page', 24 ),
		'page'              => ! empty( $_GET['upage'] ) && intval( $_GET['upage'] ) > 0 ? intval( $_GET['upage'] ) : 1,
		'populate_extras'	=> false,
		'member_type'		=> $member_type,
		'meta_key'			=> $key,
		'include'			=> $user_ids,
		'search_terms'      => $search_terms,
	);


	$gather = array();

	$gather['members'] = new BP_User_Query( $args );


	//write_log( 'pp_mm_gather_members() in pp-mm-functions.php' );
	//write_log( $gather['members'] );

	$gather['member_type'] = $member_type;

	return $gather;

}
