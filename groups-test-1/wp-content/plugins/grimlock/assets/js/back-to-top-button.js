'use strict';

/*global
    jQuery
 */

/**
 * File back-to-top-button.js
 *
 * Theme enhancements for the back to top button selected in the customizer
 */

(function ($) {

    /**
     * jQuery plugin that makes the selected element into a back to top button
     *
     * @param offset The height above witch the button should disappear
     * @param scrollDuration The duration of the scroll to top when the button is clicked
     * @returns {jQuery}
     */
    $.fn.backToTop = function (offset, scrollDuration) {
        var $this = $(this);

        var showHideButton = function () {
            if ($(document).scrollTop() > offset) {
                $this.addClass('btn__visible');
            } else {
                $this.removeClass('btn__visible');
            }
        };

        showHideButton();

        $(document).scroll(function () {
            showHideButton();
        });

        $this.on('click', function (event) {
            event.preventDefault();
            $('body,html').animate({scrollTop: 0}, scrollDuration);
        });

        return this;
    };

    $(document).ready(function () {

        /**
         * Back to top button
         */
        $('.btn-back-to-top').backToTop(300, 700);

    });

})(jQuery);
