( function ( $ ) {
	const handleMenuItemClick = function ( event ) {
		const { $navbar, breakpoint } = event.data;

		if ( ! $navbar.hasClass( 'vertical-navbar' ) && $( window ).width() >= breakpoint ) {
			return;
		}

		event.preventDefault();

		const isOpen = $( this )
			.parent( '.menu-item' )
			.hasClass( 'is-toggled' );

		$navbar.find( '.is-toggled' ).removeClass( 'is-toggled' );
		$navbar.find( '.sub-menu.is-open' ).removeClass( 'is-open' );

		if ( ! isOpen ) {
			$( this ).next( '.sub-menu' ).addClass( 'is-open' );
			$( this ).parent( '.menu-item' ).addClass( 'is-toggled' );
		}
	};

	/**
	 * jQuery plugin that makes the menu items inside the selected element into dropdowns
	 *
	 * @param {number} breakpoint The dropdown will work only when the window width is under this breakpoint
	 * @return {jQuery} Returns the jQuery object for method chaining
	 */
	$.fn.animateDropdown = function ( breakpoint = 992 ) {
		if ( ! $( this ).length ) return this;

		const $this = $( this ),
			$menuItems = $this.find(
				'.navbar-nav > .menu-item-has-children > a:not(.no-toggle)'
			),
			$currentMenuItem = $this.find( '.current-menu-ancestor' );

		if ( ! $( 'body' ).hasClass( 'slideout-mini' ) ) {
			$currentMenuItem.addClass( 'is-toggled' );
			$currentMenuItem.find( '.sub-menu' ).addClass( 'is-open' );
		}

		$menuItems.off( 'click', handleMenuItemClick );
		$menuItems.on( 'click', { $navbar: $this, breakpoint }, handleMenuItemClick );

		return this;
	};

	$( function () {
		$( 'nav.main-navigation' ).animateDropdown();
	} );
} )( jQuery );
