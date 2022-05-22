'use strict';

/*global
 jQuery
 */

/*eslint
 yoda: [2, "always"]
 */

/**
 * main.js
 *
 * Isotope activation.
 */

(function($) {
    $(document).ready(function() {
        var $grid = $('#posts');

        $grid.isotope({
            itemSelector: '[id*="post-"]',
            layoutMode:   'masonry',
            transitionDuration: '0.45s',
            // disable scale transform transition when hiding
            hiddenStyle: {
                opacity: 0
            },
            visibleStyle: {
                opacity: 1
            },
        });

        $grid.imagesLoaded().progress( function() {

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

        $('.posts-filter .control').on( 'click', function(e) {
            e.preventDefault();

            $('.posts-filter .control.active').removeClass('active');
            $(this).addClass('active');

            $grid.isotope({
                filter: $(this).attr('data-filter')
            });
        });
    });
})(jQuery);
