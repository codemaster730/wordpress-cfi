'use strict';

/*global
    jQuery, wp
 */

/**
 * Clear fixed nav state cookie when the default state of the fixed nav is changed in the customizer
 * This allows us to see the default state change when the customizer preview reloads
 */
wp.customize.state.bind('change', function() {
    var $navigationFixedDefaultStateInput = jQuery( '#customize-control-navigation_fixed_default_state' );
    $navigationFixedDefaultStateInput.on( 'change', function() {
        document.cookie = 'cera_vertical_nav_state=; path=/;';
    } );
} );
