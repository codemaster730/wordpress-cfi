'use strict';

/*global

    jQuery
 */

/**
 * File grid.js
 *
 * Theme enhancements for the posts grid layout selected
 * in the Customizer.
 */

(function ($) {

    $(document).ready(function () {

        var $grid = $('#posts');

        $grid.masonry({
            itemSelector: '[id*="post-"]'
        });

        $grid.imagesLoaded().progress(function() {
            $grid.masonry('reloadItems').masonry('layout');
        });

        $( window ).load( function() {
            $grid.masonry('reloadItems').masonry('layout');
        });

        setTimeout(function(){
            $grid.masonry('reloadItems').masonry('layout');
        }, 500);

    });

})(jQuery);