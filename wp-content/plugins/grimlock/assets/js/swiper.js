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
        var $swiperContainer = $( '.grimlock-section .swiper-container' );

        if ( $swiperContainer.length ) {

            $swiperContainer.each( function() {
                var $section = $( this ).closest( '.grimlock-section' );
                var autoSlideEnabled = $section.data( 'auto-slide-enabled' );
                var slidesPerView = $section.data( 'slides-per-view' );
                new Swiper( this, {
                    slidesPerView: slidesPerView,
                    spaceBetween: 0,
                    navigation: {
                        nextEl: $section.find( '.swiper-button-next' ) .get( 0 ),
                        prevEl: $section.find( '.swiper-button-prev' ).get( 0 ),
                    },
                    pagination: {
                        el: $section.find( '.swiper-pagination' ).get( 0 ),
                        type: 'progressbar',
                        clickable: true,
                    },
                    autoplay: !! autoSlideEnabled ? {
                        delay: 5000,
                    } : false,
                    breakpoints: {
                    	580: {
							slidesPerView: slidesPerView > 1 ? 1 : slidesPerView,
							spaceBetween: 0,
						},
                        768: {
                            slidesPerView: slidesPerView > 2 ? 2 : slidesPerView,
                            spaceBetween: 0,
                        },
                        992: {
                            slidesPerView: slidesPerView > 3 ? 3 : slidesPerView,
                            spaceBetween: 0,
                        },
                        1200: {
                            slidesPerView: slidesPerView > 5 ? 5 : slidesPerView,
                            spaceBetween: 0,
                        },
                    },
                } );
            } );

        }
    } );

} )( jQuery );
