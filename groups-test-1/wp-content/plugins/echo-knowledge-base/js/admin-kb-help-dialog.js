jQuery(document).ready(function($) {

	/*************************************************************************************************
	 *
	 *          Search Posts and Pages
	 *
	 ************************************************************************************************/

	$( document ).click( function() {
		$( '.epkb__live_search_res' ).removeClass( 'visible_search_res' );
	});

	$( 'body' ).on( 'click', '.epkb__search_posts', function() {
		return false;
	});

	$( 'body' ).on( 'input', '.epkb__search_posts', function() {
		epkb_live_search_handler( $( this ), 1000, 1 );
	});

	$( 'body' ).on( 'click', '.epkb_search_results li', function() {
		let $this_item = $( this );

		if ( ! $this_item.hasClass( 'epkb__no_res' ) ) {
			let $text = $this_item.text(),
				$id = $this_item.data( 'post_id' ),
				$main_container = $this_item.closest('.epkb_search_posts_form'),
				$display_box = $main_container.find( '.epkb_selected_posts' ),
				$value_field = $main_container.find( '.epkb_search_input input[type="hidden"]' ),
				$new_item = '<span data-post_id="' + $id + '">' + $text + '<span class="epkb__menu_remove epkbfa epkbfa-close"></span></span>';

			if ( -1 === $display_box.html().indexOf( 'data-post_id="' + $id + '"' ) ) {
				$display_box.append( $new_item );

				if ( '' === $value_field.val() ) {
					$value_field.val( $id );
				} else {
					$value_field.val( function( index, value ) {
						return value + "," + $id;
					});
				}
			}
		}

		return false;
	});

	$( 'body' ).on( 'mousewheel DOMMouseScroll', '.epkb_search_results', function() {
		let $page = typeof $(this).data( 'page' ) === 'undefined' ? 1 : $(this).data( 'page' ),
			$max_scroll = typeof $(this).data( 'scroll' ) === 'undefined' ? 0 : $(this).data( 'scroll' );

		if ( ( 0 === $(this).scrollTop() % 200 ) && $(this).scrollTop() > $max_scroll ) {
			$(this).data( 'page', $page + 1 );
			$(this).data( 'scroll', $(this).scrollTop() );
			epkb_live_search_handler( $(this).closest('.epkb_search_posts_form').find( '.epkb__search_posts' ), 0, $page + 1 );
		}
	});

	$( 'body' ).on( 'click', '.epkb_selected_posts span.epkb__menu_remove', function() {
		let $this_item = $( this ).parent(),
			$value_field = $this_item.closest('.epkb_search_posts_form').find( '.epkb_search_input input[type="hidden"]' ),
			$value_string = $value_field.val(),
			$id = $this_item.data( 'post_id' );
		if ( -1 !== $value_string.indexOf( $id ) ) {
			let $string_to_replace = -1 !== $value_string.indexOf( ',' + $id ) ? ',' + $id : $id + ',';
			$string_to_replace = -1 !== $value_string.indexOf( ',' ) ? $string_to_replace : $id;

			let $new_value = $value_string.replace( $string_to_replace, '' );
			$value_field.val( $new_value );
		}

		$this_item.remove();

		return false;
	});

	function epkb_live_search_handler( $input, $delay, $page ) {
		let $this_input = $input,
			$search_value = $this_input.val(),
			$post_type = $this_input.data( 'post_type' );

		setTimeout( function() {
			if ( $search_value !== $this_input.val() ) {
				return;
			}

			let postData = {
				action: 'epkb_search_posts',
				search_value : $search_value,
				search_post_type : $post_type,
				search_page : $page,
				_wpnonce_epkb_post_search : $('#_wpnonce_epkb_post_search').val()
			};

			epkb_send_ajax( postData, epkb_handle_search_results, $input );

		}, $delay );
	}

	function epkb_handle_search_results( response, user_input ) {
		if ( 1 == response['page'] ) {
			user_input.closest('.epkb_search_posts_form').find( '.epkb_search_results' ).replaceWith( response['data'] );
		} else {
			user_input.closest('.epkb_search_posts_form').find( '.epkb_search_results' ).append( response['data'] );
		}
	}

	/*************************************************************************************************
	 *
	 *          Overview Tab
	 *
	 ************************************************************************************************/

	// Enable Help Dialog
	$(document.body).on( 'change', 'input[name=epkb_help_dialog_enable]', function(e) {

		if( $(this).prop("checked") ) {
			$(this).val("on");
		}
		else {
			$(this).val("off");
		}

		let postData = {
			action: 'epkb_enable_help_dialog',
			epkb_help_dialog_enable: $(this).val(),
			_wpnonce_epkb_enable_help_dialog: $('#_wpnonce_epkb_enable_help_dialog').val()
		};
		epkb_send_ajax( postData, '', '', true );
	});


	/*************************************************************************************************
	 *
	 *          Locations Tab - add/update/remove entries
	 *
	 ************************************************************************************************/

	// open location form
	$(document.body).on( 'click', '#epkb-location-add', function(e) {
		e.preventDefault();
		$('#epkb-add-location-form').css('display', 'flex');
		$('#epkb-location-add').hide();
		$('#epkb_create_location').show();
		$('#epkb_update_location').hide();
	});

	// ADD location
	$(document.body).on( 'click', '#epkb_create_location', function(e) {
		e.preventDefault();

		let foundPostId = $('#epkb-add-location-form').find('.epkb_selected_posts').find("[data-post_id]" ).data('post_id');
		if ( foundPostId == undefined ) {
			return;
		}

		let selectedQuestions = [];
		$('#epkb-add-location-form').find('#epkb-question-checkbox').find("input:checkbox[name^=epkb-question-checkbox]:checked").each(function(){
			selectedQuestions.push($(this).val());
		});

		let postData = {
			action: 'epkb_create_location',
			location_type: 'location-post',
			location_id: foundPostId,
			selected_questions: selectedQuestions,
			_wpnonce_epkb_create_location: $('#_wpnonce_epkb_create_location').val()
		};

		epkb_send_ajax( postData, epkb_update_locations_list );
	});

	// EDIT location
	$(document.body).on( 'click', '.epkb_edit_location', function(e) {
		e.preventDefault();
		let postData = {
			action: 'epkb_get_location',
			location_id: $(this).closest('[id^=epkb-row-id]').data('record-id'),
			_wpnonce_epkb_location: $(document.body).find('#_wpnonce_epkb_location').val()
		};

		epkb_send_ajax( postData, epkb_edit_location );
	});
	function epkb_edit_location(response) {
		// Show Edit / Cancel Button  epkb-hd-create-location__btn
		$('.epkb-hd-update-location__btn' ).show();
		$('.epkb-hd-cancel-location__btn' ).show();

		$('#epkb-add-location-form').show();
		$('#epkb_create_location').hide();
		$('#epkb_update_location').show();
		$('#epkb-location-add').hide();
		$('#epkb-location-id').val(response.location['id']);
		$('#epkb-location-input').val(response.location['location']);
		$('#epkb-answer-input').val(response.location['answer']);
	}

	// EDIT location - Submit
	$(document.body).on( 'click', '#epkb_update_location', function(e) {
		e.preventDefault();
		let postData = {
			action: 'epkb_update_location',
			location_id: $(this).closest('#epkb-add-location-form').find('#epkb-location-id').val(),
			location_input: $('#epkb-location-input').val(),
			answer_input: $('#epkb-answer-input').val(),
			_wpnonce_epkb_location: $(document.body).find('#_wpnonce_epkb_location').val()
		};

		epkb_send_ajax( postData, epkb_update_locations_list );
	});

	// CANCEL location
	$(document.body).on( 'click', '#epkb_cancel_location', function(e) {
		e.preventDefault();
		epkb_hide_location_input_form();
	});

	// DELETE location
	$(document.body).on( 'click', '.epkb_delete_location', function(e) {
		e.preventDefault();
		let postData = {
			action: 'epkb_delete_location',
			location_id: $(this).closest('[id^=epkb-row-id]').data('record-id'),
			_wpnonce_epkb_location: $(document.body).find('#_wpnonce_epkb_location').val()
		};

		epkb_send_ajax( postData, epkb_update_locations_list );
	});

	function epkb_update_locations_list(response) {
		epkb_hide_location_input_form();
		let postData = {
			action: 'epkb_update_location_list',
			_wpnonce_epkb_create_location: $('#_wpnonce_epkb_create_location').val()
		};
		epkb_send_ajax( postData, epkb_load_location_list );
	}

	function epkb_hide_location_input_form() {
		$('#epkb-add-location-form').hide();
		$('#epkb-location-input').val('');
		$('#epkb-answer-input').val('');
		$('#epkb-location-add').show();
	}

	// Load LOCATIONs list
	function epkb_load_location_list( response ) {
		$('.epkb-locations-list').html();
		$('.epkb-locations-list').html( response.message );
	}


	/*************************************************************************************************
	 *
	 *          FAQs Tab - add/update/remove entries
	 *
	 ************************************************************************************************/

	// open faq form
	$(document.body).on( 'click', '#epkb-faq-add', function(e) {
		e.preventDefault();
		$('#epkb-add-faq-form').css('display', 'flex');
		$('#epkb-faq-add').hide();
		$('#epkb_create_faq').show();
		$('#epkb_update_faq').hide();
	});

	// ADD faq
	$(document.body).on( 'click', '#epkb_create_faq', function(e) {
		e.preventDefault();

		let postData = {
			action: 'epkb_create_faq',
			question_input: $(this).closest('#epkb-add-faq-form').find('#epkb-question-input').val(),
			answer_input: $(this).closest('#epkb-add-faq-form').find('#epkb-answer-input').val(),
			_wpnonce_epkb_create_faq: $('#_wpnonce_epkb_create_faq').val()
		};

		epkb_send_ajax( postData, epkb_update_faqs_list );
	});

	// EDIT faq
	$(document.body).on( 'click', '.epkb_edit_faq', function(e) {
		e.preventDefault();
		let postData = {
			action: 'epkb_get_faq',
			faq_id: $(this).closest('[id^=epkb-row-id]').data('record-id'),
			_wpnonce_epkb_faq: $(document.body).find('#_wpnonce_epkb_faq').val()
		};

		epkb_send_ajax( postData, epkb_edit_faq );
	});
	function epkb_edit_faq(response) {
		// Show Edit / Cancel Button  epkb-hd-create-question__btn
		$('.epkb-hd-update-question__btn' ).show();
		$('.epkb-hd-cancel-question__btn' ).show();

		$('#epkb-add-faq-form').show();
		$('#epkb_create_faq').hide();
		$('#epkb_update_faq').show();
		$('#epkb-faq-add').hide();
		$('#epkb-faq-id').val(response.faq['id']);
		$('#epkb-question-input').val(response.faq['question']);
		$('#epkb-answer-input').val(response.faq['answer']);
	}

	// EDIT faq - Submit
	$(document.body).on( 'click', '#epkb_update_faq', function(e) {
		e.preventDefault();
		let postData = {
			action: 'epkb_update_faq',
			faq_id: $(this).closest('#epkb-add-faq-form').find('#epkb-faq-id').val(),
			question_input: $('#epkb-question-input').val(),
			answer_input: $('#epkb-answer-input').val(),
			_wpnonce_epkb_faq: $(document.body).find('#_wpnonce_epkb_faq').val()
		};

		epkb_send_ajax( postData, epkb_update_faqs_list );
	});

	// CANCEL faq
	$(document.body).on( 'click', '#epkb_cancel_faq', function(e) {
		e.preventDefault();
		epkb_hide_faq_input_form();
	});

	// DELETE faq
	$(document.body).on( 'click', '.epkb_delete_faq', function(e) {
		e.preventDefault();
		let postData = {
			action: 'epkb_delete_faq',
			faq_id: $(this).closest('[id^=epkb-row-id]').data('record-id'),
			_wpnonce_epkb_faq: $(document.body).find('#_wpnonce_epkb_faq').val()
		};

		epkb_send_ajax( postData, epkb_update_faqs_list );
	});

	function epkb_update_faqs_list(response) {
		epkb_hide_faq_input_form();
		let postData = {
			action: 'epkb_update_faq_list',
			_wpnonce_epkb_create_faq: $('#_wpnonce_epkb_create_faq').val()
		};
		epkb_send_ajax( postData, epkb_load_faq_list );
	}

	function epkb_hide_faq_input_form() {
		$('#epkb-add-faq-form').hide();
		$('#epkb-question-input').val('');
		$('#epkb-answer-input').val('');
		$('#epkb-faq-add').show();
	}

	// Load FAQs list
	function epkb_load_faq_list( response ) {
		$('.epkb-faqs-list').html();
		$('.epkb-faqs-list').html( response.message );
	}


	/*************************************************************************************************
	 *
	 *          AJAX calls
	 *
	 ************************************************************************************************/


	// generic AJAX call handler
	function epkb_send_ajax( postData, refreshCallback, callbackParam, reload ) {

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
				epkb_loading_Dialog('show', '');
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

			if ( errorMsg ) {
				epkb_loading_Dialog('remove', '');
				$('.eckb-bottom-notice-message').html(errorMsg).removeClass('fadeOutDown');
				return;
			}

			//Complete Spinner animation.
			epkb_loading_Dialog('remove', '');

			if ( typeof refreshCallback === "function" ) {
				theResponse = (typeof theResponse === 'undefined') ? '' : theResponse;
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
	function epkb_loading_Dialog( displayType, message ){

		if( displayType === 'show' ){

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
		}else if( displayType === 'remove' ){

			// Remove loading dialogs.
			$( '.epkb-admin-dialog-box-loading' ).remove();
			$( '.epkb-admin-dialog-box-overlay' ).remove();
		}

	}

	/* Dialogs --------------------------------------------------------------------*/
	// SHOW INFO MESSAGES
	function epkb_admin_notification( $title, $message , $type ) {
		return '<div class="eckb-top-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>'+$title+'</h4>' : '' ) +
			($message ? $message : '') +
			'</span>' +
			'</div>' +
			'</div>';
	}
});