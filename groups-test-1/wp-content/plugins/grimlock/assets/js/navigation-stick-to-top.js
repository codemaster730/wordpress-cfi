'use strict';

/*global
    jQuery
 */

/**
 * File navigation-stick-to-top.js
 *
 * Theme enhancements for the stick-to-top feature selected in the customizer
 */

(function ($) {

    /**
     * jQuery plugin that adds classes to the body when scrolling to make the navigation sticky according to the selected element's height
     *
     * @returns {jQuery}
     */
    $.fn.stickToTop = function () {
        if (!$(this).length)
            return this;

        var $this = $(this),
            $body = $('body'),
            isNavBarInside = $body.hasClass('grimlock--navigation-inside-top') || $body.hasClass('grimlock--navigation-inside-bottom'),
            siteHeader = $('.grimlock-header').outerHeight(),
            isHeaderDisplayed = $body.hasClass('grimlock--custom_header-displayed'),
            navBarHeight = $this.outerHeight(),
            navbarOffsetTop = $this.offset().top;

        var makeNavigationSticky = function () {
            if ($(window).scrollTop() > navbarOffsetTop + navBarHeight + siteHeader) {
                if (!isNavBarInside || !isHeaderDisplayed) {
                    $body.css('padding-top', navBarHeight);
                }

                $body.addClass('grimlock--navigation-stick-to-top').removeClass('grimlock--navigation-unstick-to-top');
            } else {
                if (!isNavBarInside || !isHeaderDisplayed) {
                    $body.css('padding-top', '0');
                }

                $body.removeClass('grimlock--navigation-stick-to-top').addClass('grimlock--navigation-unstick-to-top');
            }
        };

        makeNavigationSticky();

        $(window).bind('scroll', function () {
            makeNavigationSticky();
        });

        return this;
    };

    $(document).ready(function () {

        /**
         * Custom Navbar fixed from header
         */
        $('#navigation').stickToTop();

    });

})(jQuery);
