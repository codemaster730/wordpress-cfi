<?php
/**
 * Members Directory Map Filters Template
 *
 * You can copy this file to your-theme/buddypress/members/
 * and then edit the layout.
 */

if ( !defined( 'ABSPATH' ) ) exit;

do_action( 'bp_before_members_page_map_filters' );

?>

	<div class="members-dir-map-search-div">

		<form action="" id="members-dir-map-search-form"  method="post">

			<?php

			if ( isset( $map_member_filter_types ) ) {

				$member_types =  bp_get_member_types();

				if ( ! empty( $member_types ) ) {

					?>

					<div id="members-dir-map-filter-type" class="members dir-list-filter-type">

						<?php _e("Member Types", "bp-member-maps"); ?>

						<select name="member-type-filter" id="member-type-filter">

							<option value="-1">All</option>

							<?php
							foreach ( $member_types as $mt ) {

								if ( strcasecmp( $mt, $member_type ) == 0 ) {
									$selected = ' selected ';
								} else {
									$selected = '';
								}
								echo '<option value="' . $mt . '"' . $selected . '>' . ucfirst( $mt ) . '</option>';
							}
							?>

						</select>

					</div>

					<?php

				}
			}

			?>

			<?php if ( isset( $map_member_filter_distance ) ) : ?>

				<div id="members-dir-map-filter-distance" class="members-dir-map-filter-distance">

					<?php _e("Search From: ", "bp-member-maps"); ?>

					<input id="pp_member_search_center" name="pp_member_search_center" type="text" value="" placeholder="<?php _e("Type - then Select...", "bp-member-maps"); ?>" class="form-control" autocomplete="off">

					<br>

					<?php

					if ( $map_member_distance_measurement == 'miles' ) {

						_e("Miles: ", "bp-member-maps");

					} else {

						_e("Kilometers: ", "bp-member-maps");
					}

					?>

					<input type="number" min="1" max="100" name="pp_member_search_radius" value="10">

					<input type="hidden" id="pp_member_search_center_coords" name="pp_member_search_center_coords" />

					<script type="text/javascript">
						function pp_member_search_radial() {
							var input = document.getElementById('pp_member_search_center');
							var options = {types: ['geocode']};
							var autocomplete = new google.maps.places.Autocomplete(input, options);
							google.maps.event.addListener(autocomplete, 'place_changed', function() {
								var place = autocomplete.getPlace();
								if ( place.geometry ) {
									//console.log('geometry');
									var lat = place.geometry.location.lat();
									var lng = place.geometry.location.lng();
									var latlng = lat + ',' + lng;
									document.getElementById('pp_member_search_center_coords').value = latlng;
								}
							});
						}
						jQuery(document).ready (pp_member_search_radial);
					</script>
				</div>

			<?php endif; ?>


			<br>

			<input type="submit" value="<?php _e("Submit", "bp-member-maps"); ?>">
			&nbsp; &nbsp; &nbsp;
			<input type="reset" value="<?php _e("Reset", "bp-member-maps"); ?>">

		</form>


		<br><br>
	</div>


<?php do_action( 'bp_after_members_page_map_filters' ); ?>
