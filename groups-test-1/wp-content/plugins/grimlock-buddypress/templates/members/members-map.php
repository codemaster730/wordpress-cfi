<?php
/**
 * Members Directory Map Template
 *
 * You can copy this file to your-theme/buddypress/members/
 * and then edit the layout.
 */

do_action( 'bp_before_members_page_map' );

$directory_pages = bp_get_option( 'bp-pages' );
$settings = get_site_option( 'bp-member-map-all-settings' );
extract($settings);

if ( ! isset( $map_zoom_level_all ) ) {
	$map_zoom_level_all = 18;
}

if ( isset( $map_location_field_all ) ) {

	$key = 'geocode_' . $map_location_field_all;
	// make sure the field exists.
	$location_field = pp_mm_check_xprofile_field_location( $map_location_field_all );

} else {

	if ( is_super_admin() ) {
		_e( 'You have not set a Location Field for use with this Map. Please go to wp-admin > Settings > BP Maps for Members, select a Location field and Save Settings.', 'bp-member-maps' );
	} else {
		_e( 'A Location Field has not been set for use with this Map. Please contact the Site Administrator.', 'bp-member-maps' );
	}
}
?>

<?php if ( isset( $key ) && $location_field ) : ?>

	<?php

	// set flag to show the PhiloPress filter interface
	$show_pp_map_filters = true;

	// check for the $map_member_filter_bps option from 'bp-member-map-all-settings'
	if ( isset( $map_member_filter_bps ) ) {
		// check if BP Profile Search is active
		if ( PP_BPS ) {
			$show_pp_map_filters = false;
		}
	}

	if ( ! isset( $map_member_distance_measurement ) ) {
		$map_member_distance_measurement = 'miles';
	}

	$gather = grimlock_buddypress_bp_maps_for_members_gather_members( $key, $map_member_distance_measurement );

	$member_type = $gather['member_type'];

	//$members = $gather['members'];

	$members = apply_filters( 'pp_members_map_members_filter', $gather['members'] );

	if ( empty( $members ) ) {
		settype( $members, "object" );
		$members->results = array();
		$members->total_users = 0;
	}

	$geo_locations = array();
	$geo_names = array();
	$geo_content = array();
	$geo_avatars = array();

	foreach ( $members->results as $member ) {

		$latlng = get_user_meta( $member->ID, $key, true );
		$address = xprofile_get_field_data( $map_location_field_all, $member->ID, 'comma' );

		// $member_login_modified = pp_mm_member_login_modified($member->user_login);

		$avatar = '';

		// uncomment to add avatars
		$avatar = bp_core_fetch_avatar(
			array(
				'item_id' => $member->ID,
				'type' 	  => 'thumb',
				'width'   => 80,
				'height'  => 80,
				'class'   => 'avatar',
				'html'	  => true
			)
		);

		$avatar_url = bp_core_fetch_avatar(
			array(
				'item_id' => $member->ID,
				'type' 	  => 'thumb',
				'width'   => 40,
				'height'  => 40,
				'html'	  => false
			)
		);

		$member_url = bp_core_get_user_domain( $member->ID );

		ob_start(); ?>

		<div class="members-map-pin-popup">

			<a href="<?php echo esc_url( $member_url ); ?>" target="maptab">
				<?php echo $avatar; ?>
			</a>

			<h4 class="members-map-pin-popup__name h5">
				<a href="<?php echo esc_url( $member_url ); ?>" target="maptab">
					<?php echo esc_html( $member->display_name ); ?>
				</a>
			</h4>

			<div class="bp-member-xprofile-custom-fields members-map-pin-popup__fields"><?php do_action( 'grimlock_buddypress_member_xprofile_custom_fields', $member->ID ); ?></div><!-- .bp-member-xprofile-custom-fields -->

		</div><!-- .members-map-pin-popup -->

		<?php $geo_content[] = ob_get_clean();

		$geo_locations[] = explode(",", $latlng);
		$geo_names[] = $member->display_name;
		$geo_avatars[] = $avatar_url;
	}
	?>

	<div id="buddypress" class="members-dir-map">

		<?php do_action( 'bp_before_members_map' ); ?>

		<?php do_action( 'bp_members_page_map_scripts' ); ?>

		<div class="row">

			<div class="col-12 col-md-6 col-lg-4 col-xl-5 p-4 pb-5 order-2 order-md-1 members-dir-map-sidebar bg-card ov-a">

				<div class="members-dir-map-sidebar__content">

					<?php do_action( 'template_notices' ); ?>

					<div class="members-dir-map-sidebar__header mb-4">
						<div class="row align-items-center small">
							<?php if ( ! empty( $directory_pages['members'] ) ) : ?>
								<a href="<?php echo esc_url( get_permalink( $directory_pages['members'] ) ); ?>" class="link-back-to-directory col-auto text-muted font-weight-bold text-uppercase">
									<i class="fa fa-chevron-left mr-1"></i> <?php esc_html_e( 'View directory', 'grimlock-buddypress' ); ?>
								</a>
							<?php endif; ?>
							<?php if ( is_active_sidebar( 'bp-members-map-filters' ) ) : ?>
								<a href="#" class="col-auto ml-auto text-muted" data-toggle="collapse" data-target="#collapseFilters" aria-expanded="false" aria-controls="collapseFilters">
									<span class="visible--collapsed">
										<?php esc_html_e( 'Show filters', 'grimlock-buddypress' ); ?>
									</span>
									<span class="hidden--collapsed">
										<?php esc_html_e( 'Hide filters', 'grimlock-buddypress' ); ?>
									</span>
								</a>
							<?php endif; ?>
						</div><!-- .row -->
					</div><!-- .members-dir-map-sidebar__header -->

					<div class="collapse show" id="collapseFilters">

						<?php if ( $show_pp_map_filters ) : ?>
							<?php if ( isset( $map_member_filter_distance ) || isset( $map_member_filter_types ) ) : ?>
								<?php
								if ( isset( $map_member_filter_distance ) ) {
									set_query_var( 'map_member_filter_distance', $map_member_filter_distance );
									set_query_var( 'map_member_distance_measurement', $map_member_distance_measurement );
								}
								if ( isset( $map_member_filter_types ) ) {
									set_query_var( 'map_member_filter_types', $map_member_filter_types );
									set_query_var( 'member_type', $member_type );  //  set_query_var( 'user_id', absint( $user->ID ) );
								}
								bp_get_template_part( 'members/members-map-filters' );
								?>
								<hr />
							<?php endif; ?>
						<?php else : ?>
							<?php if ( is_active_sidebar( 'bp-members-map-filters' ) ) : ?>
								<?php do_action( 'bp_before_directory_members_content' ); // place to add the BPS search args ?>
								<?php //do_action( 'bp_before_directory_members_tabs' ); // place to add a BPS search form ?>
								<?php dynamic_sidebar( 'bp-members-map-filters' ); ?>
								<hr />
							<?php endif; ?>
						<?php endif; ?>

					</div><!-- .collapse -->

					<?php bp_get_template_part( 'members/members-loop-map' ); ?>

					<?php if ( ! empty( $directory_pages['members'] ) ) : ?>
						<a href="<?php echo esc_url( get_permalink( $directory_pages['members'] ) ); ?>" class="btn btn-outline-primary btn-block btn-back-to-directory">
							<?php esc_html_e( 'View all members', 'grimlock-buddypress' ); ?>
						</a>
					<?php endif; ?>

				</div><!-- .members-dir-map-sidebar__content -->

			</div><!-- .col-* -->

			<div class="col-12 col-md-6 col-lg-8 col-xl-7 order-1 order-md-2 px-0 members-dir-map-content">

				<button class="btn btn-primary w-100 btn-block d-md-none rounded-0 collapsed" type="button" data-toggle="collapse" data-target="#collapseMap" aria-expanded="false" aria-controls="collapseMap">
					<span class="visible--collapsed"><i class="fa fa-map"></i> <?php esc_html_e( 'Show map', 'grimlock-buddypress' ); ?></span>
					<span class="hidden--collapsed"><i class="fa fa-times"></i> <?php esc_html_e( 'Hide map', 'grimlock-buddypress' ); ?></span>
				</button>

				<div class="collapse" id="collapseMap">

					<div id="members-dir-map" class="members dir-list">

						<?php if ( $members->total_users > 0 ) : ?>

							<?php $map_id = uniqid( 'members_' ); ?>

							<div class="members-map-wrapper">
								<div id="<?php echo esc_attr( $map_id ); ?>" class="members-map" style="height: <?php echo $map_height_all; ?>px; width: 100%;"></div>
							</div>

							<script type="text/javascript">

	                            var map_<?php echo $map_id; ?>;

	                            var markerBounds = new google.maps.LatLngBounds();

	                            var latLongMap = new Object();

	                            function readLatLongMap( key ) {
	                                return latLongMap[key];
	                            }

	                            function jiggleMarkers( locations ) {

	                                var currentLat;
	                                var currentLong;

	                                for ( var i = 0; i < locations.length; i++) {

	                                    currentLat = +(locations[i][0]);
	                                    currentLong = +(locations[i][1]);
	                                    if( Math.abs(readLatLongMap( currentLat ) - currentLong) < .0005 ) {
	                                        var longChange = +(2*( Math.random() - 0.5) * .01);
	                                        var latChange = +(2*( Math.random() - 0.5) * .01);
	                                        latLongMap[ (currentLat + latChange) ] = currentLong + longChange;
	                                        locations[i][0] = currentLat + latChange;
	                                        locations[i][1] = currentLong + longChange;

	                                    } else {
	                                        latLongMap[ currentLat ] = currentLong;
	                                    }
	                                }
	                            }

	                            function pp_run_map_<?php echo $map_id ; ?>(){

	                                var locations = <?php echo json_encode( $geo_locations ); ?>;
	                                var titles = <?php echo json_encode( $geo_names ); ?>;
	                                var markers_content = <?php echo json_encode( $geo_content ); ?>;
	                                var markers_url = <?php echo json_encode( $geo_avatars ); ?>;
	                                var infoWindow = new google.maps.InfoWindow( { maxWidth: 250 });

	                                jiggleMarkers( locations );

	                                var map_options = {
	                                    maxZoom: <?php echo $map_zoom_level_all; ?>,
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
	                                    mapTypeId: google.maps.MapTypeId.<?php echo strtoupper( $map_type_all ); ?>
	                                };
	                                map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id ; ?>"), map_options);

	                                var markers = [];
	                                for(var i=0;i<locations.length;i++){
	                                    var data = markers_content[i];
	                                    var lat = locations[i][0];
	                                    var lng = locations[i][1];
	                                    var location = new google.maps.LatLng(lat,lng);
	                                    var icon = "<?php echo pp_mm_load_dot(); ?>";
	                                    var image = {
	                                        url: markers_url[i],
	                                        scaledSize: new google.maps.Size(60, 60),
	                                        origin: new google.maps.Point(0,0),
	                                        anchor: new google.maps.Point(30, 30),
	                                    };

	                                    var marker = new google.maps.Marker({
	                                        position: location,
	                                        title: decode_title( titles[i] ),
	                                        map: map_<?php echo $map_id ; ?>,
	                                        icon: image,
	                                        optimized: false,
	                                    });

	                                    (function (marker, data) {
	                                        google.maps.event.addListener(marker, "click", function (e) {
	                                            infoWindow.setContent(data);
	                                            infoWindow.open(map_<?php echo $map_id ; ?>, marker);
	                                        });
	                                    })(marker, data);

	                                    markers.push(marker);

	                                    markerBounds.extend(location);

	                                }

	                                var markerCluster = new MarkerClusterer(map_<?php echo $map_id; ?>, markers, {
	                                    styles:[{
	                                        url: "<?php echo esc_url( GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/map/m1.png' ); ?>",
	                                        width: 55,
	                                        height: 55,
	                                        textSize: 14,
	                                        textColor: "#ffffff",
	                                    },
	                                    {
	                                        url: "<?php echo esc_url( GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/map/m2.png' ); ?>",
	                                        width: 55,
	                                        height: 55,
	                                        textSize: 14,
	                                        textColor: "#ffffff",
	                                    },
	                                    {
	                                        url: "<?php echo esc_url( GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/map/m3.png' ); ?>",
	                                        width: 66,
	                                        height: 66,
	                                        textSize: 14,
	                                        textColor: "#ffffff",
	                                    },
	                                    {
	                                        url: "<?php echo esc_url( GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/map/m4.png' ); ?>",
	                                        width: 78,
	                                        height: 78,
	                                        textSize: 14,
	                                        textColor: "#ffffff",
	                                    }, {
	                                        url: "<?php echo esc_url( GRIMLOCK_BUDDYPRESS_PLUGIN_DIR_URL . 'assets/images/map/m5.png' ); ?>",
	                                        width: 90,
	                                        height: 90,
	                                        textSize: 16,
	                                        textColor: "#ffffff",
	                                    }],
	                                    imagePath: '<?php echo pp_mm_load_cluster_icons(); ?>',
	                                });

	                                map_<?php echo $map_id; ?>.fitBounds(markerBounds);

	                                var myoverlay = new google.maps.OverlayView();
	                                myoverlay.draw = function () {
	                                    this.getPanes().markerLayer.id='markerLayer';
	                                };
	                                myoverlay.setMap(map_<?php echo $map_id; ?>);

	                            }

	                            function decode_title(txt){
	                                var sp = document.createElement('span');
	                                sp.innerHTML = txt;
	                                return sp.innerHTML;
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
							<div class="alert alert-primary">
								<?php _e( 'No Members with valid locations were found.', 'bp-member-maps' ); ?>
							</div>
						<?php endif; ?>

					</div><!-- .member-dir-map -->

				</div><!-- .collapse -->

			</div><!-- .col-* -->

		</div><!-- .row -->

	</div><!-- #buddypress -->

<?php endif; ?>

<?php
do_action( 'bp_after_members_page_map' );
