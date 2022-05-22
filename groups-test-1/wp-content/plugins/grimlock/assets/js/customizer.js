'use strict';

/*global
    jQuery, wp
 */

/**
 * customizer.js
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

(function($) {
    // Handle the blogname preview.
    wp.customize('blogname', function(value) {
        value.bind(function(to) {
            $('#site-title a').text(to);
        });
    });

    // Handle the blogdescription preview.
    wp.customize('blogdescription', function(value) {
        value.bind(function(to) {
            $('#site-description').text(to);
        });
    });
})(jQuery);
