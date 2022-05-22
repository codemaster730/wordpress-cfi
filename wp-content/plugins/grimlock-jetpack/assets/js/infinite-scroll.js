'use strict';

/*global
 jQuery
 */

/*eslint
 yoda: [2, "always"]
 */

/**
 * inifinite-scroll.js
 *
 * Jetpack Infinite Scroll integration.
 */

(function($) {

    $(document).ready(function() {
        var $grid = $('#posts');

        $(document.body).on('post-load', function() {
            var $elements = $('.infinite-wrap [id*="post-"]');
            $grid.append($elements).masonry('appended', $elements);

            $grid.imagesLoaded().progress(function() {
                $grid.masonry('reloadItems').masonry('layout');
            });
        });

        $(document.body).on('jetpack-lazy-loaded-image', function () {
            $grid.imagesLoaded().progress(function () {
                $grid.masonry('reloadItems').masonry('layout');
            });
        });
    });

})(jQuery);
