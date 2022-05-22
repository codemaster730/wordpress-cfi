'use strict';

/*global
    jQuery, Swiper
 */

/**
 * swiper.js
 *
 * Handle sliders with the Swiper lib
 */
( function( $ ) {

    $( document ).ready( function() {

        /**
         * Section Slider
         */
        var $swiperContainer = $( '.grimlock-query-section .swiper-container' );

        if ( $swiperContainer.length ) {

            new Swiper( '.grimlock-query-section .swiper-container', {
                slidesPerView: 3,
                spaceBetween: 0,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                pagination: {
                    el: '.swiper-pagination',
                    type: 'progressbar',
                    clickable: true,
                },
                autoplay: {
                    delay: 5000,
                },
				breakpoints: {
                    580: {
                        slidesPerView: 1,
                        spaceBetween: 0,
                    },
                    992: {
						slidesPerView: 2,
                        spaceBetween: 0,
                    },
                    1200: {
						slidesPerView: 3,
                        spaceBetween: 0,
                    },
                },
            } );

        }
    } );

} )( jQuery );
