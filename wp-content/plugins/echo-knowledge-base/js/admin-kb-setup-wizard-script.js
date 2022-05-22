jQuery(document).ready(function($) {

	let wizard = $( '#epkb-config-wizard-content' );

	// If the Wizard is not detected don't run scripts.
	if ( wizard.length <= 0 ) {
		return;
	}

	let admin_report_error_form = $( '.epkb-admin__error-form__container' );

	/**
	 * Handle Setup Wizard Apply Button
	 */
	wizard.find( '.epkb-setup-wizard-button-apply' ).on( 'click' , function(e){

		let menu_ids = [];

		let postData = {
			action: 'epkb_apply_setup_wizard_changes',
			_wpnonce_apply_wizard_changes: $('#_wpnonce_apply_wizard_changes').val(),
			epkb_wizard_kb_id: $('#epkb_wizard_kb_id').val(),
			sidebar: '0'
		};

		let theme_name = $('input[name="epkp-theme"]:checked').val();
		if ( typeof theme_name == 'undefined' ) {
			theme_name = 'current'
		}

		if ( $('.epkb-menu-checkbox input[type=checkbox]:checked').length ) {
			$('.epkb-menu-checkbox input[type=checkbox]:checked').each(function(){
				menu_ids.push($(this).prop('name').split('epkb_menu_')[1]);
			});
		}

		if ( $('.epkb-setup-wizard-sidebar input[type=radio]:checked').length ) {
			postData.sidebar = $('.epkb-setup-wizard-sidebar input[type=radio]:checked').eq(0).val();
		}

		postData.theme_name = theme_name;
		postData.kb_name = $('.epkb-wizard-name input').val();
		postData.kb_slug = $('.epkb-wizard-slug input').val();
		postData.menu_ids = menu_ids;


		epkb_send_ajax ( postData );
	});

	/**
	 * Button JS for next and prev Step.
	 */
	wizard.find( '.epkb-setup-wizard-button-next, .epkb-setup-wizard-button-prev' ).on( 'click' , function(e){
		e.preventDefault();

		// Get the Step values
		let nextStep = Number( $(this).val() );

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

		// Check if we have different headers to the steps
		if ( $('.epkb-setup-wizard-theme-header__info__title[data-title-id]').length ) {
			$('.epkb-setup-wizard-theme-header__info__title[data-title-id]').addClass('hidden');
			$('.epkb-setup-wizard-theme-header__info__title[data-title-id='+nextStep+']').removeClass('hidden');
		}

		setup_wizard_status_bar_highlight_completed_steps( nextStep );
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

	/**
	 * Highlight selected theme
	 */
	$( '.epkb-setup-option__option__label' ).on( 'click', function () {
		$( '.epkb-setup-option-container' ).removeClass( 'epkb-setup-option-container--active' );
		$( this ).closest( '.epkb-setup-option-container' ).addClass( 'epkb-setup-option-container--active' );
	});

	// For wizard step 3: click on image will turn on the radio input
	$('#epkb-wsb-step-3-panel .epkb-setup-option__featured-img-container').on( 'click', function(){
		$(this).closest('.epkb-setup-option__selection').find('label').trigger('click');
	});
	/**
	 * Live change of KB Main Page slug
	 */
	$( '.epkb-wizard-slug input[type="text"]' ).on( 'input', function () {
		$( '#epkb-wizard-slug-target' ).text( $( this ).val() );
	});


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

			// Error in response
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				errorMsg = theResponse.message ? theResponse.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');

				// Success in response - redirect to 'Need Help?' page
			} else if ( theResponse.redirect_to_url && theResponse.redirect_to_url.length > 0 ) {
				window.location = theResponse.redirect_to_url;
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
				$( admin_report_error_form ).find( '.epkb-admin__error-form__title' ).text( epkb_vars.setup_wizard_error_title );
				$( admin_report_error_form ).find( '.epkb-admin__error-form__desc' ).text( epkb_vars.setup_wizard_error_desc );
				$( admin_report_error_form ).find( '#epkb-admin__error-form__message' ).val( $( errorMsg ).text().trim() );
				$( admin_report_error_form ).css( 'display', 'block', 'important' );

			} else {
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
		wizard.find( '.eckb-wizard-step-2 .epkb-setup-option__featured-img-container' ).on( 'click', function( e ){

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

	// Close Button Message if Close Icon clicked
	$(document).on( 'click', '.epkb-close-notice', function(){
		$( this ).parent().addClass( 'fadeOutDown' );
	});


	/**
	 * Report the Report Error Form
	 */
	// Close Error Submit Form if Close Icon or Close Button clicked
	$( admin_report_error_form ).on( 'click', '.epkb-close-notice, .epkb-admin__error-form__btn-cancel', function(){
		window.location = epkb_vars.need_help_url;
	});

	// Submit the Report Error Form
	$( admin_report_error_form ).find( '#epkb-admin__error-form' ).on( 'submit', function ( event ) {
		event.preventDefault();

		let $form = $(this);
		let app = event.data;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $form.serialize(),
			beforeSend: function (xhr) {
				// block the form and add loader
				$( admin_report_error_form ).find( 'input, textarea' ).prop( 'disabled', 'disabled' );
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.sending_error_report );
			}
		}).done(function (response) {
			// success message
			if ( typeof response.success !== 'undefined' && response.success == false ) {
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( response.data );
			} else if ( typeof response.success !== 'undefined' && response.success == true ) {
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( response.data );
			} else {
				// something went wrong
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.send_report_error );
			}
		}).fail(function (response, textStatus, error) {
			// something went wrong
			$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.send_report_error );
		}).always(function () {
			// remove form loader
			$( admin_report_error_form ).find( 'input, textarea' ).prop( 'disabled', false );
			setTimeout( function() {
				window.location = epkb_vars.need_help_url;
			}, 1000 );
		});
	});

	//Dismiss ongoing notice
	$(document).on( 'click', '.epkb-notice-dismiss', function( event ) {
		event.preventDefault();
		$('.notice-'+$(this).data('notice-id')).slideUp();
		var postData = {
			action: 'epkb_dismiss_ongoing_notice',
			epkb_dismiss_id: $(this).data('notice-id')
		};
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: postData
		});
	} );
});