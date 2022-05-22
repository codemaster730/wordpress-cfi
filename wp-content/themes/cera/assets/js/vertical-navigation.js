'use strict';

/*global
    jQuery, Slideout
 */

/**
 * File vertical-navigation.js
 *
 * Theme enhancements for the vertical navigation layout selected
 * in the Customizer.
 */

jQuery( function( $ ) {

    /**
     * jQuery plugin that makes the menu items inside the selected element into dropdowns
     *
     * @returns {jQuery}
     */
    $.fn.animateDropdown = function () {
        var $this = $(this),
            $menuItems = $this.find('.navbar-nav > .menu-item-has-children > a:not(.no-toggle)'),
            $currentMenuItem = $this.find('.current-menu-ancestor');

        if (!$('body').hasClass('slideout-mini')) {
            $currentMenuItem.addClass( 'is-toggled' );
            $currentMenuItem.find( '.sub-menu' ).addClass( 'is-open' );
        }

        $menuItems.click(function (event) {
            event.preventDefault();

            var isOpen = $(this).parent('.menu-item').hasClass('is-toggled');

            $this.find('.is-toggled').removeClass('is-toggled');
            $this.find('.sub-menu.is-open').removeClass('is-open');

            if (!isOpen) {
                $(this).next('.sub-menu').addClass('is-open');
                $(this).parent('.menu-item').addClass('is-toggled');
            }
        });

        return this;
    };

    if ($('#site').length && $('#slideout-wrapper').length) {
        /**
         * Off-canvas Menu using Slideout.js
         * @type {Slideout}
         */
        var slideout = new Slideout( {
            'panel': document.getElementById( 'site' ),
            'menu': document.getElementById( 'slideout-wrapper' ),
            'padding': 285,
            'tolerance': 70
        } );

        $( '#navbar-toggler' ).on( 'click', function() {
            slideout.toggle();
        } );

        $( '.slideout-close' ).on( 'click', function() {
            slideout.close();
        } );
    }

    /**
     * Responsive Dropdown for the vertical Navbar
     */
    $('.vertical-navbar').animateDropdown();

});
