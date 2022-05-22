jQuery(document).ready(function($) {

	let wizard = $( '#epkb-config-wizard-content' );

	// If the Wizard is not detected don't run scripts.
	if ( wizard.length <= 0 ) {
		return;
	}

	/**
	 * Handle Setup Wizard Apply Button
	 */
	wizard.find( '.epkb-setup-wizard-button-apply' ).on( 'click' , function(e){

		let menu_ids = [];

		let postData = {
			action: 'epkb_apply_setup_wizard_changes',
			_wpnonce_apply_wizard_changes: $('#_wpnonce_apply_wizard_changes').val(),
			epkb_wizard_kb_id: $('#epkb_wizard_kb_id').val(),
		};

		let theme_name = $('input[name="epkp-theme"]:checked').val();
		if ( typeof theme_name == 'undefined' ) {
			theme_name = 'standard'
		}

		if ( $('.epkb-menu-checkbox input[type=checkbox]:checked').length ) {
			$('.epkb-menu-checkbox input[type=checkbox]:checked').each(function(){
				menu_ids.push($(this).prop('name').split('epkb_menu_')[1]);
			});
		}

		postData.theme_name = theme_name;
		postData.kb_name = $('.epkb-wizard-name input').val();
		postData.kb_slug = $('.epkb-wizard-slug input').val();
		postData.menu_ids = menu_ids;

		epkb_send_ajax ( postData, function( response ) {

			// after ajax function
			if ( ! response.error && typeof response.message !== 'undefined' ) {
				$('#epkb-wsb-step-2-panel').removeClass('epkb-wc-step-panel--active');
				$('#epkb-wsb-step-3-panel').addClass('epkb-wc-step-panel--active').show();
				$('#epkb-wsb-step-2').removeClass('epkb-wsb-step epkb-wsb-step--active').addClass('epkb-wsb-step epkb-wsb-step--completed');
				$('#epkb-wsb-step-3').addClass('epkb-wsb-step epkb-wsb-step--completed');
				
				$('.epkb-setup-info-box__link--edit').attr('href',response.kb_main_page_link);
				$('.epkb-setup-info-box__link--view').attr('href',response.kb_main_page_view_link);
				
				$( '.epkb-wizard-top-bar' ).hide();
				$( '.epkb-wizard-footer' ).hide();
			}

			$('.epkb-wizard-button-container').hide();

		}, false, epkb_vars.save_config);

	});

	/**
	 * Button JS for next Step.
	 */
	wizard.find( '.epkb-setup-wizard-button-next' ).on( 'click' , function(e){
		e.preventDefault();

		// Get the Step values
		let nextStep = Number( wizard.find( '#epkb-setup-wizard-button-next' ).val() );

		// Remove all Active Step classes in Step Status Bar.
		$( '.epkb-wsb-step' ).removeClass( 'epkb-wsb-step--active' );

		// Add Active class to next Step in Status Bar.
		$( '#epkb-wsb-step-'+nextStep ).addClass( 'epkb-wsb-step--active' );

		// Remove all active class from panels.
		$( '.epkb-wc-step-panel' ).removeClass( 'epkb-wc-step-panel--active' );
		$( '.epkb-wc-step-panel-button' ).removeClass( 'epkb-wc-step-panel-button--active' );

		// Add Active class to next panel in the steps.
		$( '#epkb-wsb-step-'+nextStep+'-panel' ).addClass( 'epkb-wc-step-panel--active' );
		$( '.epkb-wsb-step-'+nextStep+'-panel-button' ).addClass( 'epkb-wc-step-panel-button--active' );
		$( '.epkb-wizard-top-bar' ).show();


		setup_wizard_status_bar_highlight_completed_steps( nextStep );
		wizard_scroll_to_top();
	});

	/**
	 * Button JS for prev Step.
	 */
	wizard.find( '.epkb-setup-wizard-button-prev' ).on( 'click' , function(e){
		e.preventDefault();

		// Get the Step values
		let prevStep = Number( wizard.find( '#epkb-setup-wizard-button-prev' ).val() );

		// Remove all Active Step classes in Step Status Bar.
		$( '.epkb-wsb-step' ).removeClass( 'epkb-wsb-step--active' );

		// Add Active class to next Step in Status Bar.
		$( '#epkb-wsb-step-'+prevStep ).addClass( 'epkb-wsb-step--active' );

		// Remove all active class from panels.
		$( '.epkb-wc-step-panel' ).removeClass( 'epkb-wc-step-panel--active' );
		$( '.epkb-wc-step-panel-button' ).removeClass( 'epkb-wc-step-panel-button--active' );

		// Add Active class to next panel in the steps.
		$( '#epkb-wsb-step-'+prevStep+'-panel' ).addClass( 'epkb-wc-step-panel--active' );
		$( '.epkb-wsb-step-'+prevStep+'-panel-button' ).addClass( 'epkb-wc-step-panel-button--active' );
		$( '.epkb-wizard-top-bar' ).hide();

		setup_wizard_status_bar_highlight_completed_steps( prevStep );
		wizard_scroll_to_top();
	});

	/**
	 * Highlight all completed steps in status bar.
	 */
	function setup_wizard_status_bar_highlight_completed_steps( nextStep ){

		// Clear Completed Classes
		wizard.find( '.epkb-wizard-status-bar .epkb-wsb-step' ).removeClass( 'epkb-wsb-step--completed' );

		wizard.find( '.epkb-wizard-status-bar .epkb-wsb-step' ).each( function(){

			// Get each Step ID
			let id = $( this ).attr( 'id' );

			// Get last character the number of each ID
			let lastChar = id[id.length -1];

			// If the ID is less than the current step then add completed class.
			if( lastChar < nextStep ){
				$( this ).addClass( 'epkb-wsb-step--completed' );
			}
		});
	}

	/**
	 * Quickly scroll the user back to the top.
	 */
	function wizard_scroll_to_top(){
		$("html, body").animate({ scrollTop: 0 }, 0);
	}


	/*************************************************************************************************
	 *
	 *          Utilities
	 *
	 ***********************************************************************************************/

	/**
	 * Displays a Center Dialog box with a loading icon and text.
	 *
	 * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	 * This code is used in these files, any changes here must be done to the following files.
	 *   - admin-plugin-pages.js
	 *   - admin-kb-config-scripts.js
	 *   - admin-kb-wizard-script.js
	 *	 - admin-kb-setup-wizard-script.js
	 * @param  {string}    displayType     Show or hide Dialog initially. ( show, remove )
	 * @param  {string}    message         Optional    Message output from database or settings.
	 *
	 * @return {html}                      Removes old dialogs and adds the HTML to the end body tag with optional message.
	 */
	function epkb_loading_Dialog( displayType, message ){

		if ( displayType === 'show' ){

			let output =
				'<div class="epkb-admin-dialog-box-loading">' +

				//<-- Header -->
				'<div class="epkb-admin-dbl__header">' +
				'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>'+
				(message ? '<div class="epkb-admin-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>' +
				'<div class="epkb-admin-dialog-box-overlay"></div>';

			//Add message output at the end of Body Tag
			$( 'body' ).append( output );

		} else if( displayType === 'remove' ){
			// Remove loading dialogs.
			$( '.epkb-admin-dialog-box-loading' ).remove();
			$( '.epkb-admin-dialog-box-overlay' ).remove();
		}
	}

	/**
	 * SHOW INFO MESSAGES
	 */
	function epkb_admin_notification( $title, $message , $type ) {

		return '<div class="eckb-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
			($message ? $message : '') +
			'</span>' +
			'</div>' +
			'</div>';
	}

	// generic AJAX call handler
	function epkb_send_ajax( postData, refreshCallback, reload, loaderMessage, silent_mode = false ) {

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
				if ( ! silent_mode ) {
					epkb_loading_Dialog( 'show', loaderMessage );
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
		}).always(function ()        {
			if ( ! silent_mode ) {
				epkb_loading_Dialog( 'remove', '' );
			}

			if ( errorMsg ) {
				$('.eckb-bottom-notice-message').replaceWith(errorMsg);
				$("html, body").animate({scrollTop: 0}, "slow");
			} else {
				if ( ! silent_mode ) {
					if ( ! theResponse.error && typeof theResponse.message !== 'undefined' ) {

						$('.eckb-bottom-notice-message').replaceWith(
							epkb_admin_notification('', theResponse.message, 'success')
						);
					}
				}

				if ( typeof refreshCallback === "function" ) {
					theResponse = (typeof theResponse === 'undefined') ? '' : theResponse;
					refreshCallback(theResponse);
				} else {
					if ( reload ) {
						location.reload();
					}
				}
			}
		});
	}

	// PREVIEW POPUP
	(function(){
		//Open Popup larger Image
		wizard.find( '.epkb-setup-option__featured-img-container' ).on( 'click', function( e ){

			e.preventDefault();
			e.stopPropagation();

			wizard.find( '.image_zoom' ).remove();

			var img_src;
			var img_tag = $( this ).find( 'img' );
			if ( img_tag.length > 1 ) {
				img_src = $(img_tag[0]).is(':visible') ? $(img_tag[0]).attr('src') :
					( $(img_tag[1]).is(':visible') ? $(img_tag[1]).attr('src') : $(img_tag[2]).attr('src') );

			} else {
				img_src = $( this ).find( 'img' ).attr( 'src' );
			}

			$( this ).after('' +
				'<div id="epkb_image_zoom" class="image_zoom">' +
				'<img src="' + img_src + '" class="image_zoom">' +
				'<span class="close epkbfa epkbfa-close"></span>'+
				'</div>' + '');

			//Close Plugin Preview Popup
			$('html, body').on('click', function(){
				$( '#epkb_image_zoom' ).remove();
				$('html, body').off('click');
			});
		});
	})();
});