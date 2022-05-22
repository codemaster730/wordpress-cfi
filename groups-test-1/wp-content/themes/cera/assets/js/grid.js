'use strict';

/**
 * File grid.js
 *
 * Theme enhancements for the posts grid layout selected
 * in the Customizer.
 */

jQuery( function( $ ) {

    /**
     * Initiate Masonry grid for posts.
     */
    var $grid = $('#posts');

    $grid.masonry( {
        itemSelector: '[id*="post-"]'
    } );

    $grid.masonry('reloadItems').masonry('layout');

    setTimeout( function() {
        $grid.masonry('reloadItems').masonry('layout');
    }, 2500 );

} );
