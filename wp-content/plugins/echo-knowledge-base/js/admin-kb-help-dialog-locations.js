jQuery(document).ready(function($) {

	/*************************************************************************************************
	 *
	 *          Misc
	 *
	 ************************************************************************************************/

	$( '.epkb-admin__reload-link' ).on( 'click', function() {
		$( '.epkb-admin__top-panel__item[data-target="' + $( this ).attr( 'href' ).replace( '#', '' ) + '"]' ).trigger( 'click' );
	});

	// Show message if no Questions were created
	function epkb_check_no_questions_message() {
		if ( $( '.epkb-questions-list-container li' ).length === 0 && $( '.epkb-all-questions-list-container li' ).length === 0 ) {
			$( '#epkb-admin__no-question-message' ).show();
		} else {
			$( '#epkb-admin__no-question-message' ).hide();
		}
	}
	epkb_check_no_questions_message();

	/*************************************************************************************************
	 *
	 *          Search Posts and Pages
	 *
	 ************************************************************************************************/
	 
	$( 'body' ).on( 'input', '.epkb-hd-location-option-input', function(){
		let $input = $(this),
			search_value = $input.val(),
			post_type = $input.data('post-type'),
			$search_wrap = $input.closest('.epkb-hd-location-option-select');
		
		if ( search_value.length < 3 ) {
			return;
		}
		
		setTimeout( function() {
			if ( search_value != $input.val() ) {
				return;
			}
			
			let postData = {
				action: 'epkb_help_dialog_find_location_pages',
				search_value : search_value,
				search_post_type : post_type,
				location_id : $('#epkb-list-of-kbs').val(),
				_wpnonce_epkb_ajax_action : epkb_help_dialog_vars.nonce
			};
			
			epkb_send_ajax( postData, function( response ) {
				console.log(response);
				$search_wrap.find('.epkb-hd-location-option-search-results').html( response.data );
			}, false, false, undefined, $search_wrap.find('.epkb-hd-location-option-search-results') );
		}, 500 );
	});
	
	$('body').on( 'click', '.epkb-hd-location-option-search-results li', function(){
		
		if ( $(this).attr('disabled') == 'disabled' ) {
			return true;
		}
		
		let type = $(this).closest('.epkb-hd-location-option-select').find('input').data('post-type');
		let post_id = $(this).data('post-id');
		
		if ( $(this).closest('.epkb-hd-location-option-select').find('.epkb-hd-location-option-list li[data-post-id=' + post_id + ']').length == 0 ) {
			$(this).closest('.epkb-hd-location-option-select').find('.epkb-hd-location-option-list').append( '<li class="epkb-hd-location-option" data-post-id="' + $(this).data('post-id') + '" data-location-type="' + type + '">' + $(this).text() + '</li>' );
		}
		
		$(this).remove();
	});
	
	$('body').on( 'click', '.epkb-hd-location-option', function(){
		$(this).remove();
	});
	
	/*************************************************************************************************
	 *
	 *          Help Dialog Configuration page
	 *
	 ************************************************************************************************/

	// order questions
	$('.epkb-questions-list-container').sortable({
		axis: 'y',
		forceHelperSize: true,
		forcePlaceholderSize: true,
		placeholder: 'epkb-sortable-placeholder',
		change: epkb_show_question_save_button
	});

	// reload the page when user changes Location
	$('#epkb-change-help-dialog-location select').on( 'change', function(){
		epkb_loading_Dialog('show', '');
		$(this).closest('form').submit();
	} );
	
	// remove old selected pages when the user change include/exclude type
	$('.epkb-hd-location-option-radio input').on('change', function(){
		let type = $(this).val();
		
		if ( type == 'in' ) {
			$(this).closest('.epkb-hd-location-container').find('.epkb-hd-location-option-input[data-include-type=except]').prev().html('');
		} else {
			$(this).closest('.epkb-hd-location-container').find('.epkb-hd-location-option-input[data-include-type=in]').prev().html('');
		}
	});

	// ADD and EDIT location - Submit
	$(document.body).on( 'click', '#epkb_location', function(e) {
		e.preventDefault();
		
		// check name first 
		let name = $('#epkb-location-name').val();
		
		if ( ! name ) {
			epkb_show_error_notification( epkb_vars.location_name_required );
			epkb_scroll_to( $('#epkb-location-name').closest('.epkb-s__input-row-container') );
			return;
		}
		
		let selected_pages = {
			page: [],
			post: [],
			cpt: []
		},
		
		excluded_pages =  {
			page: [],
			post: [],
			cpt: []
		};
		
		// collect data from locations 
		if ( $('.epkb-hd-location-option-list__page-in').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			$('.epkb-hd-location-option-list__page-in .epkb-hd-location-option').each(function(){
				selected_pages.page.push( $(this).data('post-id') );
			});
		}
		
		if ( $('.epkb-hd-location-option-list__page-except').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			$('.epkb-hd-location-option-list__page-except .epkb-hd-location-option').each(function(){
				excluded_pages.page.push( $(this).data('post-id') );
			});
		}
		
		
		if ( $('.epkb-hd-location-option-list__post-in').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			$('.epkb-hd-location-option-list__post-in .epkb-hd-location-option').each(function(){
				selected_pages.post.push( $(this).data('post-id') );
			});
		}
		
		if ( $('.epkb-hd-location-option-list__post-except').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			$('.epkb-hd-location-option-list__post-except .epkb-hd-location-option').each(function(){
				excluded_pages.post.push( $(this).data('post-id') );
			});
		}
		
		if ( $('.epkb-hd-location-option-list__cpt-in').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			$('.epkb-hd-location-option-list__cpt-in .epkb-hd-location-option').each(function(){
				selected_pages.cpt.push( $(this).data('post-id') );
			});
		}
		
		if ( $('.epkb-hd-location-option-list__cpt-except').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			$('.epkb-hd-location-option-list__cpt-except .epkb-hd-location-option').each(function(){
				excluded_pages.cpt.push( $(this).data('post-id') );
			});
		}
		
		// check empty excluded options 
		if ( excluded_pages.page.length == 0 && $('.epkb-hd-location-option-list__page-except').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			excluded_pages.page.push( -1 );
		}
		
		if ( excluded_pages.post.length == 0 && $('.epkb-hd-location-option-list__post-except').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			excluded_pages.post.push( -1 );
		}
		
		if ( excluded_pages.cpt.length == 0 && $('.epkb-hd-location-option-list__cpt-except').closest('.epkb-hd-location-option-wrap').find( 'input[type=radio]' ).prop('checked') ) {
			excluded_pages.page.push( -1 );
		}
		
		// Check if the user selected something
		if ( excluded_pages.page.length == 0
			 && excluded_pages.post.length == 0 
			 && excluded_pages.cpt.length == 0 
			 && selected_pages.page.length == 0 
			 && selected_pages.page.length == 0
			 && selected_pages.page.length == 0 ) {
			epkb_show_error_notification( epkb_vars.location_pages_required );
			epkb_scroll_to( $('.epkb-hd-location-container').closest('.epkb-admin__boxes-list__box') );
			return;
		}
		
		let kb_ids = [];
		
		$('.epkb-help-dialog-kbs-container .epkb-settings-control__input__toggle:checked').each(function(){
			kb_ids.push( $(this).closest('.epkb-settings-control-type-toggle').data('field') );
		})
		
		let postData = {
			action: 'epkb_create_update_location',
			location_id: $('#epkb-list-of-kbs').val(),
			location_name: name,
			location_status: $('body').find('[name=epkb-location-status]:checked').val(),
			_wpnonce_epkb_ajax_action: epkb_help_dialog_vars.nonce,
			selected_pages: selected_pages,
			excluded_pages: excluded_pages,
			kb_ids: kb_ids
		};
		
		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				
				epkb_show_success_notification( response.message );
				
				// check if we added new category 
				if ( $('#epkb-list-of-kbs').val() == 0 ) {
					$('#epkb-list-of-kbs').prepend('<option value="' + response.location_id + '">' + $('#epkb-location-name').val() + '</option>');
					$('#epkb-list-of-kbs').val( response.location_id );
					epkb_loading_Dialog('show', '');
					$('#epkb-change-help-dialog-location').submit();
				} else {
					// rename category inside dropdown  epkb_location
					let $option = $('#epkb-list-of-kbs').find('option[value=' + response.location_id + ']');
					
					if ( $option.length ) {
						$option.text( name );
					}
				}

				if ( typeof response.url != 'undefined' && response.url && $('.epkb-admin__header__view-kb__link').length ) {
					$('.epkb-admin__header__view-kb__link').prop( 'href', response.url );
				}
				
				if ( typeof response.editor_url != 'undefined' && response.editor_url && $('#epkb-admin__boxes-list__frontend-editor a').length ) {
					$('#epkb-admin__boxes-list__frontend-editor a').prop( 'href', response.editor_url );
				}
	
			}
			
			
		}, undefined, false, function( response ){
			if ( response.error && response.error_code == 'term_exists' ) {
				epkb_scroll_to( $('#epkb-location-name').closest('.epkb-toggle-setting-container') );
			}
		} );
	});

	// CANCEL adding location
	$(document.body).on( 'click', '.epkb__hdl__action__cancel input', function(e) {
		e.preventDefault();

		let val = $('#epkb-list-of-kbs').find('option').first().val();
		$('#epkb-list-of-kbs').val( val );
		epkb_loading_Dialog('show', '');
		$('#epkb-change-help-dialog-location').trigger('submit');
	});

	// DELETE location Only call delete dialog 
	$(document.body).on( 'click', '.epkb__hdl__action__delete input', function(e) {
		$('#epkb_help_location_delete_confirmation').addClass( 'epkb-dialog-box-form--active' );
	});
	
	// Remove location by press on confirmation button 
	$('#epkb_help_location_delete_confirmation form').on( 'submit', function(e){
		e.preventDefault();
		
		// check that we have filled input 
		let location_id = $('#epkb_help_location_delete_confirmation_id').val();
		
		if ( typeof location_id == 'undefined' || ! location_id ) {
			epkb_show_error_notification( epkb_vars.reload_try_again );
			return;
		}
		
		let postData = {
			action: 'epkb_delete_location',
			location_id: location_id,
			_wpnonce_epkb_ajax_action : epkb_help_dialog_vars.nonce
		};
		
		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				
				epkb_show_success_notification( response.message );
				
				$('#epkb-list-of-kbs').find('option[value=' + response.location_id + ']').remove();
				$('#epkb-list-of-kbs').val( $('#epkb-list-of-kbs').find('option').first().val() );
				epkb_loading_Dialog('show', '');
				$('#epkb-change-help-dialog-location').trigger('submit');
			}
			
			$('#epkb_help_dialog_delete_confirmation').removeClass('epkb-dialog-box-form--active');
			
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


	/*************************************************************************************************
	 *
	 *          Question edit functions 
	 *
	 ************************************************************************************************/

	let epkb_editor_update_timer = false;
	
	function epkb_show_question_save_button() {
		$('#epkb-admin__boxes-list__questions .epkb-admin__list-actions-row .epkb-success-btn').css({'visibility' : 'visible'});
		$('#epkb-admin__boxes-list__questions .epkb-admin__list-actions-row .epkb-error-btn').css({'visibility' : 'visible'});
	}
	
	function epkb_hide_question_save_button() {
		$('#epkb-admin__boxes-list__questions .epkb-admin__list-actions-row .epkb-success-btn').css({'visibility' : 'hidden'});
		$('#epkb-admin__boxes-list__questions .epkb-admin__list-actions-row .epkb-error-btn').css({'visibility' : 'hidden'});
	}
	
	// get data about the question and fill the form 
	function epkb_show_question_form( question_id ) {
		
		epkb_editor_update_timer = setInterval( epkb_calculate_characters_counter, 1000 );
		
		let editor = tinymce.get('epkb_help_editor');
		
		// new question 
		if ( typeof question_id == 'undefined' || ! question_id ) {
			$('#epkb_help_question').val( '' );
			$('#epkb_help_question_id').val(0);
			$('.epkb-help-wp-editor').addClass('active');
			
			if ( editor ) {
				editor.setContent( '' );
			}
			
			$('.epkb-characters_left-counter').text( '0' );
			return;
		}
		
		// get existing question data to fill wp editor 
		let postData = {
			action: 'epkb_get_question_data',
			question_id: question_id,
			_wpnonce_epkb_ajax_action : epkb_help_dialog_vars.nonce
		};
		
		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.data != 'undefined' ) {
				$('#epkb_help_question').val( response.data.title );
				$('#epkb_help_question_id').val( question_id );
				
				if ( editor == null ) {
					$('#epkb_help_editor').val( response.data.content );
				} else {
					editor.setContent( response.data.content );
				}
				
				$('.epkb-help-wp-editor').addClass('active');
			}
		} );
	}
	
	function epkb_calculate_characters_counter() {
		if ( $('#epkb_help_question').length ) {
			
			let question_length = $('#epkb_help_question').val().length;
			
			if ( question_length > 200 ) {
				$('.epkb-help-wp-editor__question .epkb-characters_left-counter').text( 200 );
				$('#epkb_help_question').val( $('#epkb_help_question').val().substring( 0, 200 ) );
			} else {
				$('.epkb-help-wp-editor__question .epkb-characters_left-counter').text( question_length );
			}
		}
		
		if ( $('#epkb_help_editor').length ) {
			
			let editor = tinymce.get('epkb_help_editor');
			let answer = '';
			
			if ( editor && $('.wp-editor-wrap').hasClass('tmce-active') ) {
				answer = editor.getContent();
			} else {
				answer = $('#epkb_help_editor').val();
			}

			if ( answer.length > 1500 ) {
				answer = answer.substring( 0, 1500 );
				
				if ( editor ) {
					editor.setContent( answer );
				}
				
				$('#epkb_help_editor').val( answer );
			}
			
			$('.epkb-help-wp-editor__answer .epkb-characters_left-counter').text( answer.length );
			
		}
	}
	
	function epkb_check_faq_questions_notice() {
		if ( $('.epkb-location-notice--no-questions').length == 0 ) {
			return;
		}
		
		// check if there added sme questions 
		if ( $('.epkb-questions-list-container li').length ) {
			$('.epkb-location-notice--no-questions').addClass('epkb-location-notice--hidden');
		} else {
			$('.epkb-location-notice--no-questions').removeClass('epkb-location-notice--hidden');
		}
	}
	
	// hide editor 
	$('.epkb__help_editor__action__cancel, .epkb-help-wp-editor__overlay').on('click', function(e){
		$('.epkb-help-wp-editor').removeClass('active');
		clearInterval( epkb_editor_update_timer );
		return false;
	});
	
	// save question from the popup with wp editor 
	$('#epkb-help-article-form').on( 'submit', function(){
		
		epkb_calculate_characters_counter();
		tinyMCE.triggerSave();
		
		let postData = {
			action: 'epkb_save_question_data',
			form: $(this).serialize(),
			_wpnonce_epkb_ajax_action : epkb_help_dialog_vars.nonce
		};
		
		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				
				epkb_show_success_notification( response.message );
				
				// change article title in the list 
				if ( $( '.epkb-question-' + response.data.id ).length ) {
					$( '.epkb-question-' + response.data.id + ' .epkb-question__text' ).text( response.data.title );
					
				// add article to the list 
				} else {
					
					let $leftEl = $(response.data.html ).appendTo( '.epkb-questions-list-container');
					
					$leftEl.addClass('ui-sortable-handle');
					
					let $rightEl = $(response.data.html ).appendTo( '.epkb-all-questions-list-container');
					
					$rightEl.addClass('epkb-question-container-disabled');
					
				}
				
				$('.epkb-help-wp-editor').removeClass('active');
				
				epkb_sort_all_questions();
				epkb_show_question_save_button();
				epkb_check_faq_questions_notice();
				epkb_check_no_questions_message();
			}
		} );
		
		return false;
	});
	
	// add new question button 
	$('#epkb_help_add_new_question').on( 'click', function(){
		epkb_show_question_form();
		return false;
	});
	
	// edit question button 
	$('body').on( 'click', '.epkb-question__edit', function(){
		let question_id = $(this).closest('.epkb-question-container').data('id');

		if ( typeof question_id == 'undefined' ) {
			epkb_show_error_notification( epkb_vars.reload_try_again );
			return;
		}
		
		epkb_show_question_form( question_id );
	});
	
	// search input on the  top of the all questions list 
	$('#epkb_all_questions_filter').on('change keyup', function(){
		
		let val = $(this).val().toLowerCase();
			
		$('.epkb-all-questions-list-container .epkb-question-container').each(function(){

			let title = $(this).find('.epkb-question__text').text();
				
			if ( val.length == 0 || ~ title.toLowerCase().indexOf( val ) ) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});
	
	// trash button on all articles list. Only call delete dialog 
	$('body').on( 'click', '.epkb-question__delete', function(){
		let id = $(this).closest('.epkb-question-container').data('id');
		
		if ( typeof id == 'undefined' || ! id ) {
			epkb_show_error_notification( epkb_vars.reload_try_again );
			return;
		}
		
		$('#epkb_help_dialog_delete_confirmation_id').val( id );
		$('#epkb_help_dialog_delete_confirmation').addClass( 'epkb-dialog-box-form--active' );
	});
	
	// Remove question by press on confirmation button 
	$('#epkb_help_dialog_delete_confirmation form').on( 'submit', function(){

		// check that we have filled input 
		let id = $('#epkb_help_dialog_delete_confirmation_id').val();
		
		if ( typeof id == 'undefined' || ! id ) {
			epkb_show_error_notification( epkb_vars.reload_try_again );
			return;
		}
		
		let postData = {
			action: 'epkb_delete_question',
			id: id,
			_wpnonce_epkb_ajax_action : epkb_help_dialog_vars.nonce
		};
		
		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				epkb_show_success_notification( response.message );
				$( '.epkb-question-' + id ).remove();
			}
			
			$('#epkb_help_dialog_delete_confirmation').removeClass('epkb-dialog-box-form--active');
			epkb_check_faq_questions_notice();
			epkb_check_no_questions_message();
		} );
		
		return false;
	});
	
	// Move question from location to the list 
	$('body').on( 'click', '.epkb-question__move_right', function(){
		let id = $(this).closest('.epkb-question-container').data('id');
		
		if ( typeof id == 'undefined' || ! id ) {
			epkb_show_error_notification( epkb_vars.reload_try_again );
			return;
		}
		
		$('.epkb-questions-list-container').find( '.epkb-question-' + id ).remove();
		$('.epkb-help-dialog-all-questions').find( '.epkb-question-' + id ).removeClass('epkb-question-container-disabled');
		epkb_show_question_save_button();
		epkb_check_faq_questions_notice();
	});
	
	// Move question from list to location
	$('body').on( 'click', '.epkb-question__move_left', function(){
		let id = $(this).closest('.epkb-question-container').data('id');
		
		if ( typeof id == 'undefined' || ! id ) {
			epkb_show_error_notification( epkb_vars.reload_try_again );
			return;
		}
		
		$(this).closest('.epkb-question-container').addClass('epkb-question-container-disabled');
		
		// already added 
		if ( $('.epkb-questions-list-container').find( '.epkb-question-' + id ).length ) {
			return;
		}
		
		let $el = $(this).closest('.epkb-question-container').clone();
		$el.removeClass('epkb-question-container-disabled');
		$el.addClass('ui-sortable-handle');
		$el.appendTo('.epkb-questions-list-container');
		
		epkb_show_question_save_button();
		epkb_check_faq_questions_notice();
	});
	
	// save order button
	$('.epkb__hdl__action__save_order .epkb-success-btn').on( 'click', function(){

		let postData = {
			action: 'epkb_save_help_questions_order',
			location: $('#epkb-list-of-kbs').val(),
			questions_order: [],
			_wpnonce_epkb_ajax_action : epkb_help_dialog_vars.nonce
		};

		$('.epkb-questions-list-container .epkb-question-container').each(function(){
			postData.questions_order.push( $(this).data('id') );
		});

		epkb_send_ajax( postData, function( response ){
			if ( ! response.error && typeof response.message != 'undefined' ) {
				epkb_show_success_notification( response.message );
				epkb_hide_question_save_button();
			}
		} );

	});
	
	// add background to element when hover trash button 
	$('.epkb-question__delete').on('mouseenter', function(){
		$(this).closest('.epkb-question-container').addClass('epkb-question-container--delete-hightlight');
	});
	
	$('.epkb-question__delete').on('mouseleave', function(){
		$(this).closest('.epkb-question-container').removeClass('epkb-question-container--delete-hightlight');
	});
	
	$('.epkb-question__move_left').on('mouseenter', function(){
		$(this).closest('.epkb-question-container').addClass('epkb-question-container--move_left-hightlight');
	});
	
	$('.epkb-question__move_left').on('mouseleave', function(){
		$(this).closest('.epkb-question-container').removeClass('epkb-question-container--move_left-hightlight');
	});
	
	$('.epkb-question__move_right').on('mouseenter', function(){
		$(this).closest('.epkb-question-container').addClass('epkb-question-container--move_right-hightlight');
	});
	
	$('.epkb-question__move_right').on('mouseleave', function(){
		$(this).closest('.epkb-question-container').removeClass('epkb-question-container--move_right-hightlight');
	});
	
	$('.epkb-question__edit').on('mouseenter', function(){
		$(this).closest('.epkb-question-container').addClass('epkb-question-container--edit-hightlight');
	});
	
	$('.epkb-question__edit').on('mouseleave', function(){
		$(this).closest('.epkb-question-container').removeClass('epkb-question-container--edit-hightlight');
	});
	
	function epkb_sort_all_questions() {
		
		let questions = $('.epkb-all-questions-list-container'),
			 cont = questions.children('.epkb-question-container');
    
		cont.detach().sort(function (a, b) {

			// stripping the id to get the position number
			let modifiedA = $(a).data('modified');
			let modifiedB = $(b).data('modified');

			// checking for the greater position and order accordingly
			if (parseInt(modifiedA) <= parseInt(modifiedB)) {
				return 0;
			} else {
				return -1;
			}
		})
   
		questions.append(cont);
	}

	epkb_sort_all_questions();
	epkb_check_faq_questions_notice();
	
	// Cancel buttons (just reload the page) 
	$('body').on('click', '.epkb__hdl__action__reload input', function(){
		location.reload();
	});
});