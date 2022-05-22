'use strict';

/*global
    jQuery
 */

/**
 * widgets.js
 *
 * Widgets enhancements for a better user experience.
 */

(function($){
    $(document).ready(function() {

        /**
         * Callback for the 'widget-added', 'widget-updated' and 'ajax-complete' events.
         *
         * Activate widget form inputs.
         *
         * @since 1.0.0
         */
        var init = function($parent) {
            // Activate the color picker.
            $parent.find('.grimlock_the_events_calendar_tribe_events_section_widget-date-picker').datepicker({dateFormat: 'yy-mm-dd'});
        };

        // Initialize all widgets already in page.
        init($('#wp_inactive_widgets, #widgets-right'));

        // Initialize added widgets.
        $(document).on('widget-added', function(e, widget) {
            init(widget);
        });

        // Reinitialize updated widgets.
        $(document).on('widget-updated', function(e, widget) {
            init(widget);
        });

    });
})(jQuery);
