jQuery(document).ready(function($) {

	/*************************************************************************************************
	 *
	 *          Help Dialog Submissions page
	 *
	 ************************************************************************************************/

	// Delete all submissions - only call delete dialog
	$( '#epkb-admin__boxes-list__submissions' ).on( 'click', '.epkb-admin__items-list__delete-all input', function(e) {
		$( '#epkb-admin__items-list__delete-all_confirmation' ).addClass( 'epkb-dialog-box-form--active' );
	});


	/*************************************************************************************************
	 *
	 *          Generic
	 *
	 ************************************************************************************************/

	// Delete all items by press on confirmation button
	$( '#epkb-admin__items-list__delete-all_confirmation form' ).on( 'submit', function(e){
		e.preventDefault();

		let postData = {
			action: $( this ).find( '[name="action"]' ).val(),
			_wpnonce_epkb_ajax_action: epkb_help_dialog_vars.nonce
		};

		$( '#epkb-admin__items-list__delete-all_confirmation' ).removeClass( 'epkb-dialog-box-form--active' );

		epkb_send_ajax( postData, function( response ){

			if ( ! response.error && typeof response.message != 'undefined' ) {

				let container = $( '#epkb-admin__items-list__delete-all_confirmation' ).parent().parent();

				// Show success message
				epkb_show_success_notification( response.message );

				// Delete items
				container.find( '.epkb-admin__items-list .epkb-admin__items-list__item' ).remove();

				// Delete 'Load more items' button, because we have no items in the table
				container.find( '.epkb-admin__items-list__more-items-message' ).remove();

				// Update total number of items
				container.find( '.epkb-admin__items-list__totally-found' ).html( '0' );

				// Hide 'Clear Table' button, because we have no items in the table
				container.find( '.epkb-admin__items-list__delete-all' ).hide();
			}
		} );

		return false;
	});

	// Handle delete action for single item in table
	let epkb_items_list_delete_item = function(e){
		e.preventDefault();

		let item_row = $( this ).closest( '.epkb-admin__items-list__item' );

		// check that we have filled input
		let item_id = item_row.find( '[name="item_id"]' ).val();

		if ( typeof item_id == 'undefined' || ! item_id ) {
			epkb_show_error_notification( epkb_vars.reload_try_again );
			return;
		}

		let postData = {
			action: item_row.find( '[name="action"]' ).val(),
			item_id: item_id,
			_wpnonce_epkb_ajax_action: epkb_help_dialog_vars.nonce
		};

		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {

				// Show success message
				epkb_show_success_notification( response.message );

				let container = item_row.closest( '.epkb-admin__items-list' ).parent().parent().parent();
				let page_number = parseInt( container.find( '[name="page_number"]' ).val() );

				// Update total number of items
				container.find( '.epkb-admin__items-list__totally-found' ).html( response.total_number );

				// Fadeout the removed item row and then delete it
				item_row.fadeOut( 500, function() {

					// Delete item
					item_row.remove();

					// Hide 'Clear Table' button if there is no items left
					if ( parseInt( response.total_number ) === 0 ) {
						container.find( '.epkb-admin__items-list__delete-all' ).hide();
					}
				} );
			}
		} );

		return false;
	};
	$( '.epkb-admin__items-list form.epkb-admin__items-list__field-actions__form' ).on( 'submit', epkb_items_list_delete_item );

	// Load more items
	$( '.epkb-admin__items-list__more-items-message form' ).on( 'submit', function(e){
		e.preventDefault();

		let container = $( this ).closest( '.epkb-admin__items-list__more-items-message' ).parent();
		let form = $( this );
		let insert_before = container.find( '.epkb-admin__items-list .epkb-admin__items-list__no-results' );

		let page_number = parseInt( form.find( '[name="page_number"]' ).val() );

		let postData = {
			action: form.find( '[name="action"]' ).val(),
			page_number: page_number + 1,
			_wpnonce_epkb_ajax_action: epkb_help_dialog_vars.nonce
		};

		epkb_send_ajax( postData, function( response ){

			if ( ! response.error && typeof response.message != 'undefined' ) {

				let new_items = $( response.items );
				new_items.css( 'display', 'none' );

				page_number = page_number + 1;

				// Initialize submit handlers for each new items
				new_items.find( 'form.epkb-admin__items-list__field-actions__form' ).on( 'submit', epkb_items_list_delete_item );

				// Delete 'Load more items' button if there is no more items exist
				if ( response.total_number <= response.per_page * page_number ) {
					container.find( '.epkb-admin__items-list__more-items-message' ).remove();
				}

				// Insert new items
				$( insert_before ).before( new_items );
				new_items.fadeIn( 1000 );

				// Increase page number
				form.find( '[name="page_number"]' ).val( page_number );
			}
		} );

		return false;
	});


	/*************************************************************************************************
	 *
	 *          AJAX calls
	 *
	 ************************************************************************************************/
	
	// generic AJAX call handler
	function epkb_send_ajax( postData, refreshCallback, callbackParam, reload, alwaysCallback, $loader ) {

		let errorMsg;
		let theResponse;
		refreshCallback = (typeof refreshCallback === 'undefined') ? 'epkb_callback_noop' : refreshCallback;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				if ( typeof $loader == 'undefined' || $loader === false ) {
					epkb_loading_Dialog('show', '');
				} 
				
				if ( typeof $loader == 'object' ) {
					epkb_loading_Dialog('show', '', $loader);
				} 
				
			}
		}).done(function (response)        {
			theResponse = ( response ? response : '' );
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				errorMsg = theResponse.message ? theResponse.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
			}

		}).fail( function ( response, textStatus, error )        {
			//noinspection JSUnresolvedVariable
			errorMsg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
			//noinspection JSUnresolvedVariable
			errorMsg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, errorMsg, 'error');
		}).always(function() {
			
			theResponse = (typeof theResponse === 'undefined') ? '' : theResponse;
			
			if ( typeof alwaysCallback == 'function' ) {
				alwaysCallback( theResponse );
			} 
			
			epkb_loading_Dialog('remove', '');

			if ( errorMsg ) {
				$('.eckb-bottom-notice-message').remove();
				$('body').append(errorMsg).removeClass('fadeOutDown');
				
				setTimeout( function() {
					$('.eckb-bottom-notice-message').addClass( 'fadeOutDown' );
				}, 10000 );
				return;
			}

			if ( typeof refreshCallback === "function" ) {
				
				if ( callbackParam === 'undefined' ) {
					refreshCallback(theResponse);
				} else {
					refreshCallback(theResponse, callbackParam);
				}
			} else {
				if ( reload ) {
					location.reload();
				}
			}
		});
	}

	/**
	 * Displays a Center Dialog box with a loading icon and text.
	 *
	 * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	 * This code is used in these files, any changes here must be done to the following files.
	 *   - admin-plugin-pages.js
	 *   - admin-kb-config-scripts.js
	 *
	 * @param  {string}    displayType     Show or hide Dialog initially. ( show, remove )
	 * @param  {string}    message         Optional    Message output from database or settings.
	 *
	 * @return {html}                      Removes old dialogs and adds the HTML to the end body tag with optional message.
	 *
	 */
	function epkb_loading_Dialog( displayType, message, $el ){

		if( displayType === 'show' ){
			
			let loadingClass = ( typeof $el == 'undefined' ) ? '' : 'epkb-admin-dialog-box-loading--relative';
			
			let output =
				'<div class="epkb-admin-dialog-box-loading ' + loadingClass + '">' +

				//<-- Header -->
				'<div class="epkb-admin-dbl__header">' +
				'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>'+
				(message ? '<div class="epkb-admin-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>' +
				'<div class="epkb-admin-dialog-box-overlay ' + loadingClass + '"></div>';

			//Add message output at the end of Body Tag
			if ( typeof $el == 'undefined' ) {
				$( 'body' ).append( output );
			} else { 
				$el.append( output );
			}
			
		}else if( displayType === 'remove' ){

			// Remove loading dialogs.
			$( '.epkb-admin-dialog-box-loading' ).remove();
			$( '.epkb-admin-dialog-box-overlay' ).remove();
		}

	}

	/* Dialogs --------------------------------------------------------------------*/
	// SHOW INFO MESSAGES
	function epkb_admin_notification( $title, $message , $type ) {
		return '<div class="eckb-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>'+$title+'</h4>' : '' ) +
			($message ? '<p>' + $message + '</p>': '') +
			'</span>' +
			'</div>' +
			'<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>' +
			'</div>';
	}

	function epkb_show_error_notification( $message, $title = '' ) {
		$('.eckb-bottom-notice-message').remove();
		$('body').append( epkb_admin_notification( $title, $message, 'error' ) );

		setTimeout( function() {
			$('.eckb-bottom-notice-message').addClass( 'fadeOutDown' );
		}, 10000 );
	}
	
	function epkb_show_success_notification( $message, $title = '' ) {
		$('.eckb-bottom-notice-message').remove();
		$('body').append( epkb_admin_notification( $title, $message, 'success' ) );

		setTimeout( function() {
			$('.eckb-bottom-notice-message').addClass( 'fadeOutDown' );
		}, 10000 );
	}
	
	// scrool to element with animation 
	function epkb_scroll_to( $el ) {
		if ( $el.length == 0 ) {
			return;
		}
		
		$("html, body").animate({ scrollTop: $el.offset().top - 100 }, 300);
	}
	//Add fadeout class to notice if close icon clicked.
	$('body').on('click', '.epkb-close-notice', function (){
		let bottom_message = $( this ).closest( '.eckb-bottom-notice-message' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 1000);
	});
});