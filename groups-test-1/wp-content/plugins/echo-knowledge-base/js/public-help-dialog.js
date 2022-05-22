jQuery(document).ready(function($) {

	if( $( '.eckb-hd-toggle__right_away' ).length ) {
		jQuery('.eckb-hd-toggle__right_away').show();
	}

	if( $( '.eckb-hd-toggle__after_delay' ).length ) {
		setTimeout(function () {
			jQuery('.eckb-hd-toggle__after_delay').show();
		}, 1000);
	}

	/*************************************************************************************************
	 *
	 *          FRONTEND: FAQ box
	 *
	 ************************************************************************************************/

	/********************************************************************
	 *                      Category Box
	 ********************************************************************/

	// Category Click
	$( 'body' ).on( 'click', '.epkb-hd_cat-item', function( e ) {
		e.preventDefault();

		if ( $(this).data('category') === '' ) {
			return;
		}

		let category = $(this).data('category');

		$( '.epkb-hd__search_step' ).removeClass('epkb-hd__search_step_active');
		$( '#epkb-hd__cat-article' ).addClass('epkb-hd__search_step_active');
		$( '#epkb-hd__cat-article .epkb-hd_article-box' ).hide();
		$( '#epkb-hd__cat-article .epkb-hd_article-box[data-category='+category+']' ).show();

		show_back();
	});


	/********************************************************************
	 *                      Article Box
	 ********************************************************************/

	// Category Click
	$( 'body' ).on( 'click', '.epkb-hd_article-item', function( e ) {
		e.preventDefault();
		if ( $(this).data('kb-article-id') === '' ) {
			return;
		}

		var postData = {
			action: 'epkb_help_dialog_article_detail',
			article_id: $(this).data('kb-article-id'),
			type: $(this).data('type'),
		};

		var msg = '';

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				add_spinner();
			}

		}).done(function (response)
		{
			response = ( response ? response : '' );

			remove_spinner();

			if ( response.error || response.status !== 'success') {
				//noinspection JSUnresolvedVariable
				msg = epkb_vars.msg_try_again;
			} else {
				msg = response.search_result;
			}

		}).fail(function (response, textStatus, error)
		{
			//noinspection JSUnresolvedVariable
			msg = epkb_vars.msg_try_again + '. [' + ( error ? error : epkb_vars.unknown_error ) + ']';

		}).always(function ()
		{
			remove_spinner();

			if ( msg ) {

				$( '.eckb-hd-body-container' ).removeClass( 'eckb-hd-body--show-both-columns' );
				$( '.eckb-hd-body-container' ).addClass( 'eckb-hd-body--show-right-col-only' );

				$( '.eckb-hd-kb-articles__header' ).toggle();
				$( '.epkb-hd-search-results__article-list' ).removeClass( 'epkb-hd-search-results__article-list--active' );
				$( '.epkb-hd__search_step' ).removeClass('epkb-hd__search_step_active');
				$( '#epkb-hd__search_results-cat-article-details' ).addClass('epkb-hd__search_results-cat-article-details--active');
				$( '#epkb-hd__search_results-cat-article-details' ).html( msg );

				show_back();
			}

		});
	});


	/********************************************************************
	 *                      Search Box
	 ********************************************************************/

	$( 'body' ).on( 'input', '#epkb-hd__search-terms', function() {
		let $term = $( this ).val();
		if ( $term.length >= 3 ) {  // will cause search to be invoked by this
			help_dialog_live_search( $( this ), 500 );
			$( '.eckb-hd-body-container' ).addClass( ' eckb-hd-body--show-both-columns ' );
			$( '.eckb-hd-faq__list__item-container' ).removeClass( 'eckb-hd-faq__list__item--active' );
		}
	});

	// cleanup search if search keywords deleted or length < 3
	$("#epkb-hd__search-terms").keyup(function (event) {
		if (!$( this ).val() || $( this ).val().length < 3) {
			$( '.epkb-hd__search_step' ).removeClass('epkb-hd__search_step_active');
			$( '#epkb-hd__cat' ).addClass('epkb-hd__search_step_active');
		}
	});

	function help_dialog_live_search( $input, $delay ) {
		let $this_input = $input,
			$search_value = $this_input.val(),
			$kb_id = $this_input.data( 'kb-id' );

		setTimeout( function(){
			if ( $search_value === $this_input.val() ) {
				var postData = {
					action: 'epkb_help_dialog_search_kb',
					search_terms: $search_value,
				};
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: ajaxurl,
					data: postData,
					beforeSend: function (data) {
						add_spinner();
					}
				}).done(function (response)
				{
					response = ( response ? response : '' );

					remove_spinner();

					if ( response.error || response.status !== 'success') {
						//noinspection JSUnresolvedVariable
						msg = epkb_vars.msg_try_again;
					} else {
						msg = response.search_result;
					}

				}).fail(function (response, textStatus, error)
				{
					//noinspection JSUnresolvedVariable
					msg = epkb_vars.msg_try_again + '. [' + ( error ? error : epkb_vars.unknown_error ) + ']';

				}).always(function ()
				{
					remove_spinner();

					if ( msg ) {


						$( '.epkb-hd__search_step' ).removeClass('epkb-hd__search_step_active');
						$( '#epkb-hd__search_results' ).addClass('epkb-hd-search-results__article-list--active');
						$( '#epkb-hd__search_results' ).html( msg );

						show_logo();
					}

				});
			}
		}, $delay );
	}


	/********************************************************************
	 *                      Help dialog Toggle
	 ********************************************************************/
	$(".eckb-hd-toggle").on('click', function(){

		// Show / Hide Dialog Box
		$('#eckb-help-dialog').slideToggle();

		// Change the Toggle Icon
		$( this ).toggleClass( 'epkbfa-comments-o', '');


	});

	function add_spinner( contact = false ) {
		if ( contact ) {
			$('#epkb-hd-body__contact-container').addClass('epkb-hd__loading');
		} else {
			$( '.epkb-hd-search-results-container' ).addClass( 'epkb-hd__loading' );
		}
		$( '.eckb-hd__loading-spinner' ).show();
	}

	function remove_spinner( contact = false ) {
		if ( contact ) {
			$( '#epkb-hd-body__contact-container' ).removeClass( 'epkb-hd__loading' );
		} else {
			$('.epkb-hd-search-results-container').removeClass('epkb-hd__loading');

		}

		$( '.eckb-hd__loading-spinner' ).hide();
	}
	
	// FAQ item Click
	$(document.body).on( 'click', '.eckb-hd-faq__list__item-container', function(e) {

		// Clear All active classes
		$( '.eckb-hd-faq__list__item-container' ).removeClass( 'eckb-hd-faq__list__item--active' );

		// Remove Top Class to make the container full Width
		$( '.eckb-hd-body-container' ).removeClass( 'eckb-hd-body--show-right-col-only' );
		$( '.eckb-hd-body-container' ).removeClass( 'eckb-hd-body--show-both-columns' );

		// Add Active class to clicked on Question.
		$( this ).toggleClass( 'eckb-hd-faq__list__item--active' );

	});

	/********************************************************************
	 *                      Help dialog Header Events
	 ********************************************************************/

	$(".eckb-hd-button__contact-btn").on('click', function() {
		$( '#eckb-help-dialog' ).addClass( 'eckb-help-dialog--contact-us-active' );
	});

	$(".eckb-hd-button__faq-btn").on('click', function() {
		$( '#eckb-help-dialog' ).removeClass( 'eckb-help-dialog--contact-us-active' );
	});


	$(".eckb-hd__header-button-search").on('click', function() {
		$( this ).hide();
		$( '.eckb-hd-search-container' ).show();
		$( '.epkb-hd__search_step' ).removeClass('epkb-hd__search_step_active');
		$( '#epkb-hd__cat' ).addClass('epkb-hd__search_step_active');
		$( '.eckb-hd__header-button-contact' ).show();
		$( '.eckb-hd__header_faq__title' ).show();
		$( '.eckb-hd__header_contact__title' ).hide();
		show_logo();
	});

	$( ".eckb-hd__header-back-icon" ).on('click', function() {

		let current = $( '.epkb-hd__search_step_active' ).data('step');
		let back = ( current - 1 ) == 0 ? 2 : ( current - 1 ); // default active step : 2

		if( current == 4 && $('.epkb-hd_type_search').length ) {
			back = 1; // Back 1 for search article
		}

		if ( back == 1 || back == 2 ) {
			show_logo();
		}
		$( '.epkb-hd__search_step' ).removeClass('epkb-hd__search_step_active');
		$( '.epkb-hd__search_step[data-step='+back+']' ).addClass('epkb-hd__search_step_active');
	});

	function show_back() {
		$( ".eckb-hd__header-back-icon" ).show();
		$( ".eckb-hd__header-logo" ).hide();
	}

	function show_logo() {
		$( ".eckb-hd__header-back-icon" ).hide();
		$( ".eckb-hd__header-logo" ).show();
	}


	/*************************************************************************************************
	 *
	 *          FRONTEND: Contact Us box
	 *
	 ************************************************************************************************/

	$(document).on('submit', '#epkb-hd__contact-form', function(event){
		event.preventDefault();

		if( !jQuery("#eckb-help-dialog").is(":visible") ){
			return;
		}
		let $form = $(this);
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $form.serialize(),
			beforeSend: function (xhr) {
				add_spinner(true);
			}
		}).done(function (response) {
			// success message
			if ( typeof response.success !== 'undefined' && response.success == false ) {
				$('.epkb-hd__contact-form-response').html( response.data );
			} else if ( typeof response.success !== 'undefined' && response.success == true ) {
				$('.epkb-hd__contact-form-response').html( response.data );
			} else {
				// something went wrong
				$('.epkb-hd__contact-form-response').html( epkb_vars.msg_try_again );
			}
		}).fail(function (response, textStatus, error) {
			// something went wrong
			$('.epkb-hd__contact-form-response').html( epkb_vars.msg_try_again );
		}).always(function () {
			remove_spinner(true);
		});
	});


});