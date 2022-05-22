jQuery(document).ready(function($) {

	/*************************************************************************************************
	 *
	 *          Save Settings
	 *
	 ************************************************************************************************/

	// Save Settings button - Submit
	$(document.body).on( 'click', '#epkb_hd_save_settings_btn', function(e) {
		e.preventDefault();

		// Prepare form data
		let postData = {
			action: epkb_help_dialog_vars.save_hd_settings_action,
			_wpnonce_epkb_ajax_action: epkb_help_dialog_vars.nonce
		};

		// Set form data: to include any field to the form, add 'epkb-admin__input-field' CSS class to the field input
		$( '#epkb-admin__boxes-list__settings .epkb-admin__input-field' ).each( function() {
			let field_name = $( this ).attr( 'name' );
			postData[field_name] = $( this ).val();
		});

		// Send form
		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				epkb_show_success_notification( response.message );
			}
		});
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