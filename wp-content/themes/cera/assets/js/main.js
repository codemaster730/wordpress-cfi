'use strict';

/**
 * File main.js
 *
 * Theme enhancements for a better user experience.
 */

jQuery( function( $ ) {

    /**
     * Scroll to href anchor
     */

    var $navbar = $( '#navigation' );
    var $wpadminbar = $( '#wpadminbar' );
    var $body = $( 'body' );
    var additionalOffset = 20;

    if ( $navbar.length && ( $body.hasClass( 'grimlock--navigation-stick-to-top' ) || $body.hasClass( 'grimlock--navigation-unstick-to-top' ) ) ) {
        additionalOffset += $navbar.outerHeight();
    }

    if ( $wpadminbar.length ) {
        additionalOffset += $wpadminbar.outerHeight();
    }

    $( 'a[href*="#"]' ).not( '[href="#"]' ).not( '[href="#0"]' ).not( '[href*="#tab-"]' ).not( '[href*="tab"]' ).not( '[href*="link"]' ).not( '[role="tab"]' ).not( '#cancel-comment-reply-link' ).not( '[href*="#articleTOC"]' ).not( '[href*="#articleTOC"]' ).not( '[id*="acomment-reply"]' ).not( '[class*="bbp-reply-permalink"]' ).on( 'click', function( event ) {
        if ( location.pathname.replace( /^\//, '' ) === this.pathname.replace( /^\//, '' ) && location.hostname === this.hostname && location.search === this.search ) {
            var target = $( this.hash );
            target = target.length ? target : $( '[name=' + this.hash.slice( 1 ) + ']' );
            if ( target.length ) {
                event.preventDefault();
                $( 'html, body' ).animate( {
                    scrollTop: target.offset().top - additionalOffset
                }, 800 );
            }
        }
    } );


    /**
     * Bootstrap tooltip init
     */

    $( function() {
        $( '[data-toggle="tooltip"]' ).tooltip({
            trigger: 'hover',
            delay: { "show": 0, "hide": 0 },
        });
    } );


    /**
     * Prevent body to scroll when hamburger navigation is open
     */

    $( '#navigation-collapse' ).on( 'show.bs.collapse', function() {
        $( 'body' ).addClass( 'ov-h navbar-collapse-show' ).removeClass( 'navbar-collapse-hide' );
    } );

    $( '#navigation-collapse' ).on( 'hide.bs.collapse', function() {
        $( 'body' ).removeClass( 'ov-h navbar-collapse-show' ).addClass( 'navbar-collapse-hide' );
    } );


    /**
     * Opacity scroll effect for parallax hero background
     */

    var $itemHeader = $( '#hero' );
    var $coverImage = $( '.parallax-mirror img' );

    if ( $itemHeader.length && $coverImage.length ) {

        // Increase this value to decrease the effect and vice versa
        var headerOffsetBottom = $itemHeader.offset().top + $itemHeader.height();

        var scrollTop = $( window ).scrollTop();
        var opacity = scrollTop < headerOffsetBottom ? 1 - ( scrollTop / headerOffsetBottom * 2 ) : 0;

        $coverImage.css( 'opacity', opacity );

        $( window ).on( 'scroll', function() {
            scrollTop = $( window ).scrollTop();
            opacity = scrollTop < headerOffsetBottom ? 1 - ( scrollTop / headerOffsetBottom * 2 ) : 0;

            $coverImage.css( 'opacity', opacity );
        } );
    }

    /**
     * Small vertical navigation
     */
    var $slideOutWrapper = $( '.slideout-wrapper' );
    var $navBarToggler = $( '#navbar-toggler-mini' );

    if ( $slideOutWrapper.length && $navBarToggler.length ) {

        var openVerticalNav = function() {
            $body.removeClass( 'slideout-mini-hover' );
            $body.removeClass( 'slideout-mini' );
            $slideOutWrapper.removeClass( 'mini' );
            $navBarToggler.removeClass( 'collapsed' );
        };

        var closeVerticalNav = function() {
            $body.addClass( 'slideout-mini' );
            $slideOutWrapper.addClass( 'mini' );
            $navBarToggler.addClass( 'collapsed' );
            $( '.slideout-wrapper.mini .menu-item .sub-menu' ).removeClass('is-open');
        };

        if ( $body.hasClass( 'slideout-mini' ) ) {
            closeVerticalNav();
        }

        // Open / Close vertical navigation
        $navBarToggler.on( 'click', function() {
            if ( $body.hasClass( 'slideout-mini-hover' ) || $body.hasClass( 'slideout-mini' ) ) {
                openVerticalNav();
                document.cookie = 'cera_vertical_nav_state=open; path=/;';
            }
            else if ( ! $slideOutWrapper.hasClass( 'mini' ) ) {
                closeVerticalNav();
                document.cookie = 'cera_vertical_nav_state=closed; path=/;';
            }

            if ( $.fn.masonry ) {
				// Reload masonry
				$( '.masonry' ).masonry( 'reloadItems' ).masonry( 'layout' );
			}
        } );

        // Open vertical navigation on mouse enter
        $body.on( 'mouseenter', '.slideout-wrapper.mini', function() {
            $body.addClass( 'slideout-mini-hover' ).removeClass( 'slideout-mini' );
        } );

        // Close vertical navigation when mouse leaves
        $body.on( 'mouseleave', '.slideout-wrapper.mini', function() {
            $body.removeClass( 'slideout-mini-hover' ).addClass( 'slideout-mini' );
            $( '.slideout-wrapper.mini .menu-item' ).removeClass('is-toggled');
            $( '.slideout-wrapper.mini .menu-item .sub-menu' ).removeClass('is-open');
        } );
    }

    /**
     * Recalculate masonry when dashboard content changes
     */

    // Select the node that will be observed for mutations
    var $dashboardWidgetsContainer = $( '.page-template-template-dashboard #main .widget-area' );

    if ( $dashboardWidgetsContainer.length && $.fn.masonry ) {

        // Callback function to execute when mutations are observed
        var recalculateDashboardMasonry = function() {
            $( '.masonry' ).masonry('reloadItems').masonry('layout');
        };

        // Options for the observer (which mutations to observe)
        var config = {
            childList: true,
            subtree: true
        };

        // Create an observer instance linked to the callback function
        var observer = new MutationObserver( recalculateDashboardMasonry );

        // Start observing the target node for configured mutations
        observer.observe( $dashboardWidgetsContainer.get( 0 ), config );
    }

} );
