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

        // Add posts to current collection.
        $(document.body).on('post-load', function() {
            var $elements = $('.infinite-wrap [id*="post-"]');
            $grid.append($elements).isotope('appended', $elements);

            $grid.imagesLoaded(function() {
                if ($grid.hasClass('posts--height-equalized')) {
                    var $posts   = $grid.find('[id*="post-"]');
                    var $highest = null;
                    var hi       = 0;

                    // Find highest post from collection.
                    $posts.each(function() {
                        var h = $(this).height();
                        if (h > hi) {
                            hi = h;
                            $highest = $(this);
                        }
                    });

                    // Match all heights with highest post.
                    $posts.matchHeight({
                        target: $highest
                    });
                }
                $grid.isotope('reloadItems');
                $grid.isotope('layout');
            });
        });

        $(document.body).on('jetpack-lazy-loaded-image', function () {
            $grid.imagesLoaded(function () {
                $grid.isotope('reloadItems');
                $grid.isotope('layout');
            });
        });

    });
})(jQuery);
