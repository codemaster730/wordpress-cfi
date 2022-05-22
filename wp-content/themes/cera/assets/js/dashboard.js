'use strict';

/*global
 jQuery
 */

/**
 * File dashboard.js
 *
 * Theme enhancements for a better user experience when visiting the dashboard page.
 */

jQuery( function( $ ) {

    /**
     * Initiate Masonry grid for posts.
     */
    var $grid = $('body.page-template-template-dashboard:not(.dashboard--items-height-equalized) #main .widget-area');

    if ( $grid.length ) {
        $grid.on( 'layoutComplete', function() {
            setTimeout( function() {
                $grid.addClass( 'dashboard--loaded' );
				$( 'body.page-template-template-dashboard' ).addClass( 'dashboard--loaded' );
            }, 1500 );
        } );

        $grid.masonry( {
            itemSelector: '.widget',
            percentPosition: true,
            columnWidth: '.grid-sizer',
            gutter: 20,
            stagger: 30,
            transitionDuration: '.4s'
        } );

        $grid.masonry( 'reloadItems' ).masonry( 'layout' );
    }
    else {
        // Add masonry-loaded class immediately if we're not using masonry
        $( 'body.page-template-template-dashboard.dashboard--items-height-equalized #main .widget-area' ).addClass( 'dashboard--loaded' );
        $( 'body.page-template-template-dashboard' ).addClass( 'dashboard--loaded' );
    }

});
