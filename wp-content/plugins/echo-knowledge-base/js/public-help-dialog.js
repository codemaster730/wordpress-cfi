jQuery(document).ready(function($) {

	if( $( '.eckb-hd-toggle' ).length ) {
		let start_delay = jQuery('.eckb-hd-toggle').data('start-delay');

		setTimeout(function () {
			jQuery('.eckb-hd-toggle').show();
		}, start_delay * 1000 );
	}

	/*************************************************************************************************
	 *
	 *          FRONTEND: FAQ box
	 *
	 ************************************************************************************************/
	function adjust_help_dialog_height(){
		let windowHeight = window.innerHeight;
		let minWindowHeight = 720; // This is the height at which the HD will be cut

		// Calculate the height difference and minus it from our fixed value of 477
		let diff =  477 - ( minWindowHeight - windowHeight );

		// If the logo is active, then add extra space.
		let logoSpace = '';
		if( $( '.eckb-hd-header__logo' ).length ) {
			logoSpace = 50;
		}

		// If in KB Editor mode then add extra space
		let KB_EditorSpace = '';
		if( $( 'html' ).find( '.epkb-editor-zone' ).length ) {
			KB_EditorSpace = 32;
		}

		// Start making the body container smaller if the diff is less.
		if( diff > 477 ){
			$( '#eckb-hd-body-container' ).css( 'height', ( 477 - logoSpace ) - KB_EditorSpace );
			$( '.eckb-hd-header__logo' ).show();
		}else {
			// Very small screen
			$( '#eckb-hd-body-container' ).css( 'height', ( diff - KB_EditorSpace )  );

			// Hide Logo
			$( '.eckb-hd-header__logo' ).hide();
		}
	}

	if( $( '#eckb-help-dialog' ).length ) {

		// On page load set height
		adjust_help_dialog_height();
		/**
		 * If window re-sizes vertically, adjust the Help Dialog box height.
		 * This will prevent the HD from being cut off from the top of the browser window.
		 */
		window.addEventListener('resize', function(){
			adjust_help_dialog_height();
		});


	}




	/********************************************************************
	 *                      Article Box
	 ********************************************************************/

	// Article Click
	$( 'body' ).on( 'click', '.epkb-hd_article-item', function( e ) {
		
		e.preventDefault();

		// Clear Height Attribute
		$('#epkb-hd_article-desc').css('height', '');

		// Get the iframe styling
		let helpBox_iframeStyling = $('help-dialog-iframe-styles').html();

		let url = $(this).data('url'), 
			article_excerpt = epkb_help_dialog_vars.article_preview_not_available;
		
		if ( typeof url == 'undefined' || ! url ) {
			return;
		}
		
		// set title 
		let article_title = $(this).find('.epkb-hd_article-item__text').text();
		
		// set read more link 
		$('.epkb-hd_article-link').prop( 'href', url );
		
		// clear and the iframe
		$('#epkb-hd_article-desc').contents().find('body').html( '' );
		
		// check if the iframe have the same styles 
		if ( $('#epkb-hd_article-desc').contents().find('link').length == 0 ) {


			// ADD LINKS ----------------------------------------------/
			// Approved stylesheet ID's
			let stylesheet_Ids = [
				'wp-block-library-css',
				'elementor-icons-css',
				'elementor-common-css',
				'divi-fonts-css',
				'divi-style-css',
				'elementor-animations-css',
				'elementor-frontend-css',
				'elementor-post-196-css',
				'google-fonts-1-css',
			];
			// Go through each Link in the header and check if they are stylesheets.
			$('head link').each(function(){

				// If it's not a stylesheet skip.
				if ( $(this).prop('rel') != 'stylesheet' ) {
					return true;
				}

				// If this stylesheet does not have the approved ID skip.
				// ** Not using it for now, keeping code if we decide to reuse it.
				/*if( stylesheet_Ids.indexOf( $(this ).attr('id') ) === -1 ) {
					return true;
				}*/

				// Add Style stylesheet to head.
				$(this).clone().appendTo( $('#epkb-hd_article-desc').contents().find('head') );
			});

			// ADD STYLES ---------------------------------------------/
			$('head style').each(function(){
				$(this).clone().appendTo( $('#epkb-hd_article-desc').contents().find('head') );
			});
			
			$('body link').each(function(){
				
				if ( $(this).prop('rel') != 'stylesheet' ) {
					return true;
				}
				
				$(this).clone().appendTo( $('#epkb-hd_article-desc').contents().find('head') );
			});

			$('body style').each(function(){
				$(this).clone().appendTo( $('#epkb-hd_article-desc').contents().find('head') );
			});
			
			$('#epkb-hd_article-desc').contents().find('head').append( '<link rel="stylesheet" type="text/css" href="' + epkb_help_dialog_vars.help_iframe_styles + '">' );
		}
		
		$.ajax({
			type: 'GET',
			dataType: 'html',
			url: url,
			beforeSend: function (xhr) {
				$( '#eckb-help-dialog' ).attr( 'data-prev-sub-step', $( '#eckb-help-dialog' ).attr( 'data-sub-step' ) );
				help_dialog_change_sub_step( 4 );
				$('.epkb-hd_article-desc').html('');
			}

		}).done(function (response) {
			
			if ( $(response).find( '#eckb-article-content-body' ).length == 0 ) {
				return true;
			}
			
			let $article_html = $(response).find( '#eckb-article-content-body' );
			
			// strip not allowed tags  
			let allowed_tags = [ 'br', 'ul', 'li', 'ol', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'b', 'strong', 'i', 'a', 'img' ];
			
			// strip not allowed tags ( will not remove empty tags as there no contents )
			$article_html.find('*').each( function(){
				
				if ( ! ~allowed_tags.indexOf( $(this).prop('tagName').toLowerCase() ) ) {
					// add spaces between words instead of tags 
					$(this).append( ' ' );
					$(this).contents().unwrap();
				}

			});
			
			// remove empty tags 
			$article_html.find('*').each( function(){
				
				if ( $(this).prop('tagName').toLowerCase() !== 'img' && $(this).html().trim() == '' ) {
					$(this).remove();
				}

			});
			
			// convert to string
			$article_html = $article_html.html();
			
			// remove all comments
			$article_html = $article_html.replace(/<!--[\s\S]*?-->/g, "");
			
			// get only first 30 words 
			article_excerpt = $article_html.split(' ').slice(0, 500).join(' ');

			// Fill unclosed tags 
			let div_content = document.createElement('div');
			div_content.innerHTML = article_excerpt;
			
			// remove empty tags again
			$(div_content).find('*').each( function(){
				
				if ( $(this).prop('tagName').toLowerCase() !== 'img' && $(this).html().trim() == '' ) {
					$(this).remove();
				}

			});
			
			article_excerpt = $(div_content).html();

			if ( $article_html.length > article_excerpt.length  ) {
				article_excerpt += '...' ;
			} 
			
		}).always(function () {
			help_dialog_change_step( 3 );
			help_dialog_change_sub_step( parseInt( $( '#eckb-help-dialog' ).attr( 'data-prev-sub-step' ) ) );
			$('#epkb-hd_article-desc').contents().find('body').html( '<h1 class="epkb-hd_article-title">' + article_title + '</h1>' + article_excerpt );

			// Add the active theme class to the HTML Tag
			$('#epkb-hd_article-desc').contents().find( 'html' ).addClass( $( '#epkb-hd_article-desc' ).attr( 'data-active-theme-class' ) );

			// Add Class to iframe body tag
			$('#epkb-hd_article-desc').contents().find( 'body' ).addClass( 'epkb-hd_article-desc__body' );

			// Add Help Box iframe style
			$('#epkb-hd_article-desc').contents().find('html head').append( helpBox_iframeStyling );

			// Resize iframe
			let textSize = $('#epkb-hd_article-desc').contents().find('body').height();

			// If the logo is active, then remove from height.
			let logoSpace = 0;
			if( $( '.eckb-hd-header__logo' ).length ) {
				logoSpace = 50;
			}

			if ( textSize < 250 ){
				// Set the size if there isn't much text
				$('#epkb-hd_article-desc').height( ( 290 - logoSpace ) );

			}else {
				$('#epkb-hd_article-desc').height( $('#epkb-hd_article-desc').contents().find('body').height() );
			}


		});

	});

	// Back to FAQs button click
	$( '.epkb-hd__faq__back-btn' ).on( 'click', function( e ) {
		help_dialog_change_step( 'back' );
	});



	/********************************************************************
	 *                      Search Box
	 ********************************************************************/

	$( 'body' ).on( 'input', '#epkb-hd__search-terms', function() {
		let $term = $( this ).val();
		if ( $term.length >= 3 ) {  // will cause search to be invoked by this
			help_dialog_live_search( $( this ), 500 );
			$( '.eckb-hd-faq__list__item-container' ).removeClass( 'eckb-hd-faq__list__item--active' );
		}
	});

	// cleanup search if search keywords deleted or length < 3
	$( "#epkb-hd__search-terms" ).keyup( function ( event ) {

		// article search with ajax 
		if ( ! $( this ).val() || $( this ).val().length < 3 ) {
			$( '#eckb-help-dialog' ).attr( 'data-step', '1' );
		}
		
		// js search in the faq 
		if ( ! $( this ).val() ) {
			$( '#epkb-hd__search_results__faqs .eckb-hd-faq__list__item-container' ).show();
		}
	});

	function help_dialog_live_search( $input, $delay ) {
		let $this_input = $input,
			$search_value = $this_input.val(),
			$kb_ids = $this_input.data( 'kb-ids' );
		
		setTimeout( function(){
			if ( $search_value === $this_input.val() ) {
				
				let postData = {
					action: 'epkb_help_dialog_search_kb',
					search_terms: $search_value,
					kb_ids: $kb_ids
				}, faq_results = '',
					article_results = '',
					no_results = '';
				
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: epkb_help_dialog_vars.ajaxurl,
					data: postData,
					beforeSend: function (data) {
						help_dialog_change_step( 2 );
						help_dialog_change_sub_step( 4 );
					}

				}).done(function (response) {
					response = ( response ? response : '' );

					if ( response.error || response.status !== 'success') {
						//noinspection JSUnresolvedVariable
						no_results = epkb_help_dialog_vars.msg_try_again;
					} else {
						
						if ( response.no_results ) {
							no_results = response.no_results;
						}
						
						if ( response.faq_results ) {
							faq_results = response.faq_results;
						}
						
						if ( response.article_results ) {
							article_results = response.article_results;
						}
						
					}

				}).fail(function (response, textStatus, error) {
					//noinspection JSUnresolvedVariable
					no_results = epkb_help_dialog_vars.msg_try_again + '. [' + ( error ? error : epkb_help_dialog_vars.unknown_error ) + ']';

				}).always(function () {

					$('#epkb-hd__search_results__errors').html(
						'<div class="epkb-hd__search_results__no-results-container"> ' +
							'<div class="epkb-hd-no-results__msg">' + no_results + '</div>' +
							'<div class="epkb-hd-no-results__keywords"></div>' +
							'<div class="epkb-hd-no-results__hints">' +
							'<p>Here are some hints:</p>' +
							'<ol>' +
								'<li>Dont use generic terms instead use specific ones.</li>' +
								'<li>Try using fewer words.</li>' +
								'<li>Make sure the spelling is correct.</li>' +
							'</ol>' +
							'</div>'
						+ '</div>'
					);
					
					$('#epkb-hd__search_results__errors .epkb-hd-no-results__keywords').text($search_value);
					$('#epkb-hd__search_results__articles').html( article_results );
					$('#epkb-hd__search_results__faqs').html( faq_results );

					// We have an error or no results
					help_dialog_change_step( 2 );
					if ( no_results.length > 0 ) {
						help_dialog_change_sub_step(3);

					// Found results
					} else {

						// Found only FAQs
						if ( $( faq_results ).find( 'div' ).length > 0 && $( article_results ).find( 'li' ).length === 0 ) {
							help_dialog_change_sub_step( 5 );

						// Found only articles
						} else if ( $( article_results ).find( 'li' ).length > 0 && $( faq_results ).find( 'div' ).length === 0 ) {
							help_dialog_change_sub_step( 6 );

						// Found both: FAQs and articles
						} else {
							help_dialog_change_sub_step( 1 );
						}
					}
				});
			}
		}, $delay );
	}

	// Breadcrumb clicks
	$( '.eckb-hd__breadcrumb_text' ).on( 'click', function () {
		help_dialog_change_step( parseInt( $( this ).attr( 'data-target-step' ) ) );
	});

	// Switch search results between FAQs and Articles
	$( '.epkb-hd__search-results-title' ).on( 'click', function () {
		help_dialog_change_step( 2 );
		help_dialog_change_sub_step( parseInt( $( this ).attr( 'data-target-sub-step' ) ) );

		// Clear all classes
		$( '.epkb-hd__search-results-title' ).removeClass( 'epkb-hd__search-results-title--active' );

		// Add Active Class to Tab
		$( this ).addClass( 'epkb-hd__search-results-title--active' );
	});

	// Change step
	// Step 1 - FAQs list
	// Step 2 - Search results, FAQs list & articles list
	// Step 3 - Search results, article details
	function help_dialog_change_step( step ) {
		let help_dialog_container = $( '#eckb-help-dialog' );
		let next_step = step === 'back' ? parseInt( help_dialog_container.attr( 'data-step' ) ) - 1 : step;
		if ( next_step === 1 ) {
			$( '#epkb-hd__search-terms' ).val( '' );
		}
		help_dialog_container.attr( 'data-step', next_step );
	}
	

	// Sub-step 1 - Search results, FAQs list
	// Sub-step 2 - Search results, articles list
	// Sub-step 3 - Search results, error case
	// Sub-step 4 - spinner for Search results or Contact Us form
	// Sub-step 5 - found only one type of results: FAQs
	// Sub-step 6 - found only one type of results: articles
	function help_dialog_change_sub_step( sub_step ) {
		if ( typeof sub_step === 'number' && sub_step > 0 ) {
			$( '#eckb-help-dialog' ).attr( 'data-sub-step', sub_step );

			// Set corresponding Tab as active
			$( '.epkb-hd__search-results-title' ).removeClass( 'epkb-hd__search-results-title--active' );
			$( '.epkb-hd__search-results-title[data-target-sub-step=' + sub_step + ']' ).addClass( 'epkb-hd__search-results-title--active' );
		}
	}


	/********************************************************************
	*                      Help dialog Toggle
	********************************************************************/
	$(".eckb-hd-toggle").on('click', function(){

		// Show / Hide Dialog Box
		$('#eckb-help-dialog').slideToggle();

		// Change the Toggle Icon
		$( '.eckb-hd-toggle__icon' ).toggleClass( 'epkbfa-comments-o epkbfa-times');

		// Set Toggle Status
		$( this ).toggleClass( 'eckb-hd-toggle--off eckb-hd-toggle--on');

		$( '#eckb-help-dialog' ).addClass( 'eckb-hd-toggle--check' );
	});
	
	// FAQ item Click
	$(document.body).on( 'click', '.eckb-hd__item__question', function(e) {

		let current_container = $( this ).closest( '.eckb-hd-faq__list__item-container' );

		// If clicked again on already opened Question
		if ( current_container.hasClass( 'epkb-hd__element--active' ) ) {
			current_container.removeClass( 'epkb-hd__element--active' );
			return;
		}

		// Close currently opened Questions
		current_container.parent().find( '.eckb-hd-faq__list__item-container' ).removeClass( 'epkb-hd__element--active' );

		// Add Active class to clicked on Question.
		current_container.toggleClass( 'epkb-hd__element--active' );

		//Scroll to div top
		let el = document.querySelector( '.eckb-hd-faq__list__item-container.epkb-hd__element--active' );
		el.scrollIntoView({
			block: "start",
			behavior: "smooth"
		});
	});

	// Contact Us link when no Questions at Home step
	$( '.epkb-hd__contact-us__link' ).on( 'click', function() {
		$( '.eckb-hd-tab[data-target-tab=' + $( this ).attr( 'data-target-tab' ) + ']' ).trigger( 'click' );
	});


	/********************************************************************
	 *                      Help dialog Header Events
	 ********************************************************************/
	$( '.eckb-hd-tab' ).on( 'click', function (){
		$( '#eckb-help-dialog' ).attr( 'data-tab', $( this ).attr( 'data-target-tab' ) );

		//Remove Classes
		$( '.eckb-hd-tab' ).removeClass( 'eckb-hd-tab--active' );

		// Add Active Class
		$( this ).addClass( 'eckb-hd-tab--active' );

	});


	/*************************************************************************************************
	 *
	 *          FRONTEND: Contact Us box
	 *
	 ************************************************************************************************/

	$(document).on('submit', '#epkb-hd__contact-form', function( event ){
		event.preventDefault();

		if( ! $("#eckb-help-dialog").is(":visible") || $('.epkb-editor-preview').length ){
			return;
		}

		let form_data = $( this ).serialize();

		// add additional parameter to verify the form is submitted by our JS
		form_data += '&jsnonce=' + epkb_help_dialog_vars.nonce;

		// check if the Help Dialog box was actually opened by user (has class in this case), otherwise stop execution
		if ( ! $( '#eckb-help-dialog' ).hasClass( 'eckb-hd-toggle--check' ) ) {
			return;
		}

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: epkb_help_dialog_vars.ajaxurl,
			data: form_data,
			beforeSend: function (xhr) {
				$( '#eckb-help-dialog' ).attr( 'data-prev-sub-step', $( '#eckb-help-dialog' ).attr( 'data-sub-step' ) );
				help_dialog_change_sub_step( 4 );
			}
		}).done(function (response) {

			// success message
			if ( typeof response.success !== 'undefined' && response.success !== false ) {

				$('.epkb-hd__contact-form-response').html( '<div class="epkb-hd__contact-form-response--success">'+response.data+'</div>' );

			} else if ( typeof response.success !== 'undefined' && response.success === true ) {

				$('.epkb-hd__contact-form-response').html( '<div class="epkb-hd__contact-form-response--success">'+response.data+'</div>' );

			} else {

				// something went wrong
				if ( typeof response.data !== 'undefined' && response.data.length > 5 ) {
					$('.epkb-hd__contact-form-response').html( '<div class="epkb-hd__contact-form-response--error">'+response.data+'</div>' );
				} else {
					$('.epkb-hd__contact-form-response').html( '<div class="epkb-hd__contact-form-response--error">'+epkb_help_dialog_vars.msg_try_again+'</div>' );
				}

			}
		}).fail(function (response, textStatus, error) {
			// something went wrong
			$('.epkb-hd__contact-form-response').html( '<div class="epkb-hd__contact-form-response--error">'+epkb_help_dialog_vars.msg_try_again+'</div>' );
		}).always(function () {
			$('.epkb-hd__contact-form-response').show();
			help_dialog_change_sub_step( parseInt( $( '#eckb-help-dialog' ).attr( 'data-prev-sub-step' ) ) );
		});
	});


});