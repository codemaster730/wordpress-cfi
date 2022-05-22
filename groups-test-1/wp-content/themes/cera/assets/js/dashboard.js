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
    var $grid = $('.page-template-template-dashboard #main .widget-area');

    $grid.on( 'layoutComplete', function() {
        setTimeout(function(){
            $grid.addClass('masonry--loaded');
        }, 1500);
    });

    $grid.masonry( {
        itemSelector: '.widget',
        percentPosition: true,
        columnWidth: '.grid-sizer',
        gutter: 20,
        stagger: 30,
        transitionDuration: '.4s'
    } );

    $grid.masonry('reloadItems').masonry('layout');

});
