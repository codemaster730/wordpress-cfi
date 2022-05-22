'use strict';

/*global
    jQuery, grimlock_loader
 */

/**
 * loader.js
 *
 * Display the loader on page reload.
 */

(function($) {

    $(document).ready(function () {

        /**
         * Hide loader after a given time.
         */
        setTimeout(function() {
            $('body').addClass('grimlock--loader-hidden');
        }, grimlock_loader.animation_duration);

    });

})(jQuery);
