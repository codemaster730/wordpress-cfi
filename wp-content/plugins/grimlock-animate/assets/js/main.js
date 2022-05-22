jQuery( function ( $ ) {
    var isBlockEditor = $( 'body' ).hasClass( 'block-editor-page' );

    // Init ScrollReveal for later use
    window.sr = ScrollReveal();

    // This will hold the event handler to update the elements parallax effect on scroll
    var updateParallax;

    var initAnimations = function( $container ) {
        if ( ! $container )
            $container = $( document );

        var $scrollContainer = isBlockEditor ? $( '.block-editor .interface-interface-skeleton__content, .blocks-widgets-container .interface-interface-skeleton__content' ) : $( window );

        // Bail if container doesn't exist
        if ( ! $scrollContainer.length )
            return;

        /**
         * Parallax
         */

        // Get parallax elements
        var $parallaxElements = $( '[data-grimlock-animate-parallax]' );

        // Function to calculate the next offset of the element that has the parallax effect
        var calcPos = function( $el, scrollCenter ) {
            // Get effect speed
            var speed = ( $el.data( 'grimlock-animate-parallax' ) / 2 ) || 0.2;

            // Reset transform3d before getting the position of the element
            $el.css( 'transform', function() {
                return 'translate3d(0px, 0px, 0)';
            } );
            var elementCenter = $el.offset().top + ( $el.height() / 2 );

            if ( isBlockEditor )
                elementCenter += $scrollContainer.scrollTop() - $scrollContainer.offset().top;

            // Change the position of the element
            $el.css( 'transform', function() {
                var offset = parseInt( ( elementCenter - scrollCenter ) * speed );
                return 'translate3d(0px, ' + offset + 'px, 0)';
            } );
        };

        // Remove the previous handler if there is one
        if ( updateParallax ) {
            $scrollContainer.off( 'scroll', updateParallax );
        }

        // Update the handler function
        updateParallax = function() {
            var scrollCenter = $scrollContainer.scrollTop() + ( $scrollContainer.height() / 2 );

            $parallaxElements.each( function() {
                calcPos( $( this ), scrollCenter );
            } );
        };

        // Initialize parallax elements positions
        $parallaxElements.each( function() {
            $( this ).css( 'will-change', 'transform' );

            updateParallax.call( this );
        } );

        // Attach the handler
        $scrollContainer.on( 'scroll', updateParallax );

        /**
         * Scroll Reveal
         */
        var $revealElements = $container.find( '[data-grimlock-animate-scroll-reveal]' );

        $revealElements.each( function() {
            var revealOptions = $( this ).data( 'grimlock-animate-scroll-reveal' ) || {};

            revealOptions.container = isBlockEditor ? $scrollContainer.get( 0 ) : document.documentElement;

            var $elements = $( this );
            if ( revealOptions.selector ) {
                $elements = $( this ).find( revealOptions.selector );
            }

            $elements.css( 'visibility', 'hidden' );

            if ( $elements.length ) {
                sr.reveal( $elements.get(), revealOptions, revealOptions.interval );
            }
        } );
    };

    initAnimations();

    if ( isBlockEditor ) {
        // Create a mutation observer to detect blocks re-render
        var blocksObserver = new MutationObserver( function( mutationList, observer ) {

            var $renderedBlocks = $([]);

            // Loop over each mutation to find if some blocks have re-rendered
            mutationList.forEach( function( mutation ) {
                // We only care about grimlock section blocks mutations
                if ( mutation.type === 'childList' && $( mutation.target ).parent().hasClass( 'wp-block' ) && $( mutation.target ).find( '.grimlock-section' ).length )
                    $renderedBlocks = $renderedBlocks.add( $( mutation.target ) );
            } );

            // Init animations for all blocks that re-rendered
            $renderedBlocks.each( function() {
                initAnimations( $( this ) );
            } );
        } );

        var $blockEditorContainer = $( '.block-editor, .edit-widgets-block-editor' );

        if ( $blockEditorContainer.length ) {
            blocksObserver.observe( $blockEditorContainer.get( 0 ), {
                subtree: true,
                childList: true
            } );
        }
    }


	/* Custom Animation fo Block WP */
	var html = $('html');

	// On Screen
	$.fn.isOnScreen = function() {
		var elementTop = $(this).offset().top,
			elementBottom = elementTop + $(this).outerHeight(),
			viewportTop = $(window).scrollTop(),
			viewportBottom = viewportTop + $(window).height();
		return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	function detection() {
		for (var i = 0; i < items.length; i++) {
			var el = $(items[i]);
			if (el.isOnScreen()) {
				el.addClass("is-animated");
			}
		}
	}

	var items = document.querySelectorAll(
		".grimlock-animate__wp-block-columns"
		),
		waiting = false,
		w = $(window);

    detection();

	w.on("resize scroll", function() {
		if (waiting) {
			return;
		}
		waiting = true;
		detection();

		setTimeout(function() {
			waiting = false;
		}, 250);
	});


} );
