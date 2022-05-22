'use strict';

/*global
    Slideout, grimlock_vertical_navigation
 */

/**
 * File vertical-navigation.js
 *
 * Theme enhancements for the vertical navigation layout selected
 * in the Customizer.
 */

jQuery( function ( $ ) {
	if ( $( '#site' ).length && $( '#slideout-wrapper' ).length ) {
		/**
		 * Off-canvas Menu using Slideout.js
		 *
		 * @type {Slideout}
		 */
		const slideout = new Slideout( {
			panel: document.getElementById( 'site' ),
			menu: document.getElementById( 'slideout-wrapper' ),
			padding: grimlock_vertical_navigation.on_left
				? grimlock_vertical_navigation.padding
				: -grimlock_vertical_navigation.padding,
			tolerance: 70,
		} );

		$( '#navbar-toggler' ).on( 'click', function () {
			slideout.toggle();
		} );

		$( '.slideout-close' ).on( 'click', function () {
			slideout.close();
		} );
	}
} );
