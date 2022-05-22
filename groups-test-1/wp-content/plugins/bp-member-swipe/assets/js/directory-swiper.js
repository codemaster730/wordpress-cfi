jQuery( function( $ ) {
    var $membersContainer = $( '#members-index-swipe ul.bp-member-swipe-list' );

    if ( $membersContainer.length ) {

        var $previousButton = $( '.bp-member-swipe-pagination button.bp-member-swipe-pagination__link--prev' ),
            $nextButton = $( '.bp-member-swipe-pagination button.bp-member-swipe-pagination__link--next' ),
            maxPage = $( '.bp-member-swipe-pagination .bp-member-swipe-pagination__links' ).data( 'max-page' ),
            currentPage = 1,
            queryArgs = $membersContainer.data( 'query-args' ) || {},
            loading = false;

        // Load next page if going past half of the current page
        var maybeLoadNextPage = function() {
            if ( membersSwiper.activeIndex > membersSwiper.slides.length - Math.floor( parseInt( queryArgs[ 'per_page' ] ) / 2 ) && currentPage !== maxPage && !loading ) {
                queryArgs[ 'page' ] = currentPage + 1;

                var data = {
                    'action': 'load_member_swipe_page',
                    'query_args': queryArgs,
                };

                loading = true;

                jQuery.post( bp_member_swipe_directory_swiper.ajax_url, data, function( response ) {
                    if ( response.success ) {
                        // Increment current page number
                        currentPage++;

                        // Append new loaded members to members list and trigger custom event
                        membersSwiper.appendSlide( response.data );
                        $membersContainer.trigger( 'bp-member-swipe/loaded-page', [ $( response.data ) ] );

                        // Stop loading
                        $nextButton.removeClass( 'loading' );
                        loading = false;
                    }
                } );
            }
        }

        // Initialize Swiper
        var membersSwiper = new Swiper( '#members-index-swipe #members-dir-list', {
            speed: 400,
            spaceBetween: 30,
            centeredSlides: true,
            threshold: 5,
            keyboard: {
                enabled: true,
            },
            slideClass: 'bp-member-swipe-list__item',
            wrapperClass: 'bp-member-swipe-list',
            navigation: {
                nextEl: '.bp-member-swipe-pagination__link--next',
                prevEl: '.bp-member-swipe-pagination__link--prev',
            },
            on: {
                slideNextTransitionEnd: function() {
                    maybeLoadNextPage();
                },
                reachBeginning: function() {
                    $previousButton.hide();
                },
                reachEnd: function() {
                    if ( currentPage >= maxPage ) {
                        $nextButton.hide();
                    } else {
                        $nextButton.addClass( 'loading' );
                    }
                },
                fromEdge: function() {
                    $nextButton.show();
                    $previousButton.show();
                }
            }
        } );
    }
} );
