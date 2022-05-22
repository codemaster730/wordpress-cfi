/**
 * Clear fixed nav state cookie when the default state of the fixed nav is changed in the customizer
 * This allows us to see the default state change when the customizer preview reloads
 */
wp.customize.state.bind( 'change', function () {
	var $profileNavMobileDefaultStateInput = jQuery( '#customize-control-profile_nav_mobile_default_state' );
	$profileNavMobileDefaultStateInput.on( 'change', function() {
		document.cookie = 'grimlock_buddypress_profile_nav_mobile_state=; path=/;';
	} );
} );