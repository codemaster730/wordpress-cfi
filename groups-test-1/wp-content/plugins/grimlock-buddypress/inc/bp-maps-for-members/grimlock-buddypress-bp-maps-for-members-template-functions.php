<?php
/**
 * Grimlock template functions for BP Maps for Members.
 *
 * @package grimlock-buddypress
 */

 /**
  * Radial distance search members
  *
  * @param string $key
  * @param string $map_member_distance_measurement
  */
function grimlock_buddypress_bp_maps_for_members_gather_members( $key, $map_member_distance_measurement ) {

	$member_type = '';

	if ( isset( $_POST["member-type-filter"] ) && $_POST["member-type-filter"]  != '-1' ) {

		$member_type = sanitize_text_field( $_POST["member-type-filter"] );
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

		$user_ids = pp_mm_members_radial_distance( $lat, $lng, $radius, $key, $earthRadius );

		if ( empty ( $user_ids ) ) {

			$gather['members'] = $user_ids;

			$gather['member_type'] = $member_type;

			return $gather;

		}

	} elseif ( PP_BPS ) {

		$settings = get_site_option( 'bp-member-map-all-settings' );
		extract($settings);

		//use BPS to filter member ids if that option is selected in Settings
		if ( isset( $map_member_filter_bps ) ) {

			$user_ids = apply_filters( 'pp_mm_bps_filter_member_ids', $user_ids );

		}

	}

	$args = array(
		'type'				=> 'active',
		'per_page'			=> apply_filters( 'grimlock_buddypress_members_per_page', 24 ),
		'page'              => intval( $_GET['upage'] ) > 0 ? intval( $_GET['upage'] ) : 1,
		'populate_extras'	=> false,
		'member_type'		=> $member_type,
		'meta_key'			=> $key,
		'include'			=> $user_ids
	);


	$gather = array();

	$gather['members'] = new BP_User_Query( $args );

	$gather['member_type'] = $member_type;

	return $gather;

}
