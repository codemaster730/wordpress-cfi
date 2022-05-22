<?php

/**
 * Template for a single Member map
 * You can copy this file to your-theme/buddypress/members/single
 * and then edit the layout.
 */

$settings_single = get_site_option( 'bp-member-map-single-settings' );
extract($settings_single);
$key = 'geocode_' . $map_location_field;

$latlng = get_user_meta( bp_displayed_user_id(), $key, true );
$address = xprofile_get_field_data( $map_location_field, bp_displayed_user_id(), 'comma' );

$avatar_url = bp_core_fetch_avatar(
	array(
		'item_id' => $member->ID,
		'type' 	  => 'thumb',
		'width'   => 40,
		'height'  => 40,
		'html'	  => false
	)
);

if ( ! empty( $latlng ) ) {
	if ( wp_script_is( 'google-places-api', 'registered' ) ) {
		wp_enqueue_script( 'google-places-api' );
		wp_print_scripts( 'google-places-api' );
	}
}
?>

<div class="member-map-profile-content bg-card p-3">

	<?php if ( ! empty( $address ) ): ?>
		<div class="member-location-value bg-black-faded p-3 mb-3 rounded-card">
			<i class="fa fa-map-marker mr-2 fa-lg"></i> <?php echo stripslashes( $address ); ?>
		</div>
	<?php endif; ?>

	<?php if ( ! empty( $latlng ) ) : ?>

		<?php $map_id = uniqid( 'pp_member_map_' ); ?>

		<div class="members-map rounded-card ov-h pos-r" id="<?php echo esc_attr( $map_id ); ?>" style="height: <?php echo $map_height; ?>px; width: 100%;"></div>

	    <script type="text/javascript">
			var map_<?php echo $map_id; ?>;
			function pp_run_map_<?php echo $map_id ; ?>(){
				var location = new google.maps.LatLng(<?php echo $latlng; ?>);
                var image = {
                    url: '<?php echo $avatar_url; ?>',
                    scaledSize: new google.maps.Size(60, 60),
                    origin: new google.maps.Point(0,0),
                    anchor: new google.maps.Point(30, 30),
                };
				var map_options = {
					zoom: <?php echo $map_zoom_level; ?>,
					center: location,
                    streetViewControl: false,
                    mapTypeControl: false,
                    zoomControlOptions: {
                        position: google.maps.ControlPosition.TOP_RIGHT
                    },
                    styles: [
                        {
                            "featureType": "administrative",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "administrative",
                            "elementType": "geometry.fill",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "administrative",
                            "elementType": "geometry.stroke",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "administrative",
                            "elementType": "labels.text",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "landscape",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "lightness": "0"
                                }
                            ]
                        },
                        {
                            "featureType": "poi.medical",
                            "elementType": "geometry.fill",
                            "stylers": [
                                {
                                    "lightness": "-5"
                                }
                            ]
                        },
                        {
                            "featureType": "poi.park",
                            "elementType": "geometry.fill",
                            "stylers": [
                                {
                                    "visibility": "on"
                                },
                                {
                                    "color": "#a7ce95"
                                },
                                {
                                    "lightness": "45"
                                }
                            ]
                        },
                        {
                            "featureType": "poi.school",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "color": "#be9b7b"
                                },
                                {
                                    "lightness": "70"
                                }
                            ]
                        },
                        {
                            "featureType": "poi.sports_complex",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "color": "#5d4b46"
                                },
                                {
                                    "lightness": "60"
                                }
                            ]
                        },
                        {
                            "featureType": "road",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "transit.station",
                            "elementType": "geometry",
                            "stylers": [
                                {
                                    "saturation": "23"
                                },
                                {
                                    "lightness": "10"
                                },
                                {
                                    "gamma": "0.8"
                                },
                                {
                                    "hue": "#b000ff"
                                }
                            ]
                        },
                        {
                            "featureType": "water",
                            "elementType": "all",
                            "stylers": [
                                {
                                    "visibility": "on"
                                }
                            ]
                        },
                        {
                            "featureType": "water",
                            "elementType": "geometry.fill",
                            "stylers": [
                                {
                                    "color": "#a2daf2"
                                }
                            ]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "labels.text",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "poi.business",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "poi.park",
                            "elementType": "labels.text",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "road.arterial",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "labels",
                            "stylers": [
                                {
                                    "visibility": "off"
                                }
                            ]
                        }
                    ],
					mapTypeId: google.maps.MapTypeId.<?php echo strtoupper( $map_type ); ?>
				}
				map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id; ?>"), map_options);

				var marker = new google.maps.Marker({
					position: location,
                    title: '<?php $member->display_name; ?>',
                    icon: image,
					map: map_<?php echo $map_id ; ?>,
				});

                var myoverlay = new google.maps.OverlayView();
                myoverlay.draw = function () {
                    this.getPanes().markerLayer.id='markerLayer';
                };
                myoverlay.setMap(map_<?php echo $map_id; ?>);

			}

			google.maps.event.addDomListener(window, "resize", function() {
				var map = map_<?php echo $map_id; ?>;
				var center = map.getCenter();
				google.maps.event.trigger(map, "resize");
				map.setCenter(center);
			});

			pp_run_map_<?php echo $map_id ; ?>();
		</script>

	<?php else : ?>
		<div class="alert alert-info">
			<?php _e( 'A map cannot be created for this Member. The geocode does not exist.', 'bp-member-maps' ); ?>
		</div>
	<?php endif; ?>

</div>
