jQuery(document).ready(function($) {

	/* Variables -----------------------------------------------------------------*/
	var knowledgebase = $( '#epkb-main-page-container' );
	var tabContainer = $('#epkb-content-container');
	var navTabsLi    = $('.epkb-nav-tabs li');
	var tabPanel     = $('.epkb-tab-panel');
	var articleContent = $('#eckb-article-content-body');
	var articleToc     = $('.eckb-article-toc');

	/********************************************************************
	 *                      Search
	 ********************************************************************/

	// handle KB search form
	$( 'body' ).on( 'submit', '#epkb_search_form', function( e ) {
		e.preventDefault();  // do not submit the form

		if ( $('#epkb_search_terms').val() === '' ) {
			return;
		}

		var postData = {
			action: 'epkb-search-kb',
			epkb_kb_id: $('#epkb_kb_id').val(),
			search_words: $('#epkb_search_terms').val(),
			is_kb_main_page: $('.eckb_search_on_main_page').length
		};

		var msg = '';

		$.ajax({
			type: 'GET',
			dataType: 'json',
			data: postData,
			url: epkb_vars.ajaxurl,
			beforeSend: function (xhr)
			{
				//Loading Spinner
				$( '.loading-spinner').css( 'display','block');
				$('#epkb-ajax-in-progress').show();
			}

		}).done(function (response)
		{
			response = ( response ? response : '' );

			//Hide Spinner
			$( '.loading-spinner').css( 'display','none');

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
			$('#epkb-ajax-in-progress').hide();

			if ( msg ) {
				$( '#epkb_search_results' ).css( 'display','block' );
				$( '#epkb_search_results' ).html( msg );

			}

		});
	});

	$("#epkb_search_terms").on( 'keyup', function() {
		if (!this.value) {
			$('#epkb_search_results').css( 'display','none' );
		}
	});


	/********************************************************************
	 *                      Tabs / Mobile Select
	 ********************************************************************/

	//Get the highest height of Tab and make all other tabs the same height
	$(window).on('load', function(){
		var tallestHeight = 0;
		tabContainer.find( navTabsLi ).each( function(){

			var this_element = $(this).outerHeight( true );
			if( this_element > tallestHeight ) {
				tallestHeight = this_element;
			}
		});
		tabContainer.find( navTabsLi ).css( 'min-height', tallestHeight );
	});

	function changePanels( Index ){
		$('.epkb-panel-container .epkb-tab-panel:nth-child(' + (Index + 1) + ')').addClass('active');
	}

	function updateTabURL( tab_id, tab_name ) {
		var location = window.location.href;
		location = update_query_string_parameter(location, 'top-category', tab_name);
		window.history.pushState({"tab":tab_id}, "title", location);
		// http://stackoverflow.com/questions/32828160/appending-parameter-to-url-without-refresh
	}

	window.onpopstate = function(e){

		if ( e.state && e.state.tab.indexOf('epkb_tab_') !== -1) {
			//document.title = e.state.pageTitle;

			// hide old section
			tabContainer.find('.epkb_top_panel').removeClass('active');

			// re-set tab; true if mobile drop-down
			if ( $( "#main-category-selection" ).length > 0 )
			{
				$("#main-category-selection").val(tabContainer.find('#' + e.state.tab).val());
			} else {
				tabContainer.find('.epkb_top_categories').removeClass('active');
				tabContainer.find('#' + e.state.tab).addClass('active');
			}

			tabContainer.find('.' + e.state.tab).addClass('active');

		// if user tabs back to the initial state, select the first tab if not selected already
		} else if ( $('#epkb_tab_1').length > 0 && ! tabContainer.find('#epkb_tab_1').hasClass('active') ) {

			// hide old section
			tabContainer.find('.epkb_top_panel').removeClass('active');

			// re-set tab; true if mobile drop-down
			if ( $( "#main-category-selection" ).length > 0 )
			{
				$("#main-category-selection").val(tabContainer.find('#epkb_tab_1').val());
			} else {
				tabContainer.find('.epkb_top_categories').removeClass('active');
				tabContainer.find('#epkb_tab_1').addClass('active');
			}

			tabContainer.find('.epkb_tab_1').addClass('active');
		}
	};

	// Tabs Layout: switch to the top category user clicked on
	tabContainer.find( navTabsLi ).each(function(){

		$(this).on('click', function (){
			tabContainer.find( navTabsLi ).removeClass('active');

			$(this).addClass('active');

			tabContainer.find(tabPanel).removeClass('active');
			changePanels ( $(this).index() );
			updateTabURL( $(this).attr('id'), $(this).data('cat-name') );
		});
	});

	// Tabs Layout: MOBILE: switch to the top category user selected
	$( "#main-category-selection" ).on( 'change', function() {
			tabContainer.find(tabPanel).removeClass('active');
			// drop down
			$( "#main-category-selection option:selected" ).each(function() {
				var selected_index = $( this ).index();
				changePanels ( selected_index );
				updateTabURL( $(this).attr('id'), $(this).data('cat-name') );
			});
		});

	function update_query_string_parameter(uri, key, value) {
		var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";
		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		}
		else {
			return uri + separator + key + "=" + value;
		}
	}


	/********************************************************************
	 *                      Sections
	 ********************************************************************/

	//Detect if a an div is inside an list item then it's a sub category
	$('.epkb-section-body .epkb-category-level-2-3').each(function(){

		$(this).on('click', function(){

			$(this).parent().children('ul').toggleClass('active');

			// Accessibility: aria-expand

			// Get current data attribute value
			let ariaExpandedVal = $( this ).attr( 'aria-expanded' );

			// Switch the value of the data Attribute on click.
			switch( ariaExpandedVal ) {
				case 'true':
					// It is being closed so Set to False
					$( this ).attr( 'aria-expanded', 'false' );
					break;
				case 'false':
					// It is being opened so Set to True
					$( this ).attr( 'aria-expanded', 'true' );
					break;
				default:
			}

		});
	});

	/**
	 * Sub Category icon toggle
	 *
	 * Toggle between open icon and close icon
	 * Accessibility: Set aria-expand values
	 */
	tabContainer.find('.epkb-section-body .epkb-category-level-2-3').each(function(){

		if( $(this).hasClass( 'epkb-category-focused' ) ) {
			return;
		}

		var $icon = $(this).find('.epkb-category-level-2-3__cat-icon');

		$(this).on('click', function (){

			var plus_icons = [ 'ep_font_icon_plus' ,'ep_font_icon_minus' ];
			var plus_icons_box = [ 'ep_font_icon_plus_box' ,'ep_font_icon_minus_box' ];
			var arrow_icons1 = [ 'ep_font_icon_right_arrow' ,'ep_font_icon_down_arrow' ];
			var arrow_icons2 = [ 'ep_font_icon_arrow_carrot_right' ,'ep_font_icon_arrow_carrot_down' ];
			var arrow_icons3 = [ 'ep_font_icon_arrow_carrot_right_circle' ,'ep_font_icon_arrow_carrot_down_circle' ];
			var folder_icon = [ 'ep_font_icon_folder_add' ,'ep_font_icon_folder_open' ];

			function toggle_category_icons( $array ){

				//If Parameter Icon exists
				if( $icon.hasClass( $array[0] ) ){

					$icon.removeClass( $array[0] );
					$icon.addClass( $array[1] );

				}else if ( $icon.hasClass( $array[1] )){

					$icon.removeClass( $array[1] );
					$icon.addClass($array[0]);
				}
			}

			toggle_category_icons( plus_icons );
			toggle_category_icons( plus_icons_box );
			toggle_category_icons( arrow_icons1 );
			toggle_category_icons( arrow_icons2 );
			toggle_category_icons( arrow_icons3 );
			toggle_category_icons( folder_icon );
		});
	});

	/**
	 * Show all articles functionality
	 *
	 * When user clicks on the "Show all articles" it will toggle the "hide" class on all hidden articles
	 */
	knowledgebase.find('.epkb-show-all-articles').on( 'click', function () {

		$( this ).toggleClass( 'active' );
		var parent = $( this ).parent( 'ul' );
		var article = parent.find( 'li');

		//If this has class "active" then change the text to Hide extra articles
		if ( $(this).hasClass( 'active')) {

			//If Active
			$(this).find('.epkb-show-text').addClass('epkb-hide-elem');
			$(this).find('.epkb-hide-text').removeClass('epkb-hide-elem');
			$(this).attr( 'aria-expanded','true' );

		} else {
			//If not Active
			$(this).find('.epkb-show-text').removeClass('epkb-hide-elem');
			$(this).find('.epkb-hide-text').addClass('epkb-hide-elem');
			$(this).attr( 'aria-expanded','false' );
		}

		$( article ).each(function() {

			//If has class "hide" remove it and replace it with class "Visible"
			if ( $(this).hasClass( 'epkb-hide-elem')) {
				$(this).removeClass('epkb-hide-elem');
				$(this).addClass('visible');
			}else if ( $(this).hasClass( 'visible')) {
				$(this).removeClass('visible');
				$(this).addClass('epkb-hide-elem');
			}
		});
	});
	
	let search_text = $( '#epkb-search-kb' ).text();
	$( '#epkb-search-kb' ).text( search_text );


	/********************************************************************
	 *                      Article Print 
	 ********************************************************************/
	$('body').on("click", ".eckb-print-button-container, .eckb-print-button-meta-container", function(event) {
		
		if ( $('body').hasClass('epkb-editor-preview') ) {
			return;
		}
		
		$('#eckb-article-content').parents().each(function(){
			$(this).siblings().addClass('eckb-print-hidden');
		});
		
		window.print();
	});
	
	/** Article TOC v2 */
	
	let TOC = {
		
		firstLevel: 1, 
		lastLevel: 6, 
		searchStr: 'h1', 
		currentId: '',
		offset: 50,
		excludeClass: false,
		
		init: function() {
			this.getOptions();
			
			let articleHeaders = this.getArticleHeaders();
			
			// show TOC only if headers are present
			if ( articleHeaders.length > 0 ) {
				
				articleToc.html( this.getToCHTML( articleHeaders ) );
				/** articleContent.find(searchStr).scrollSpy( params ); */

				// Add h2 title for Article content section
				if( $('#eckb-article-content .eckb-article-toc').length > 0 ) {
					
					$('#eckb-article-content .eckb-article-toc').html( this.getToCHTML( articleHeaders, 'h2' ) );
				}

				if( $(' .eckb-article-toc--position-middle ').length > 0 ) {
					articleToc.css('display', 'inline-block' );
				} else {
					articleToc.fadeIn();
				}
				
			} else {
				articleToc.hide();

				//FOR FE Editor ONLY
				if ($('body').hasClass('epkb-editor-preview')) {
					articleToc.show();
					let title = articleToc.find('.eckb-article-toc__title').html();
					let html = `
						<div class="eckb-article-toc__inner">
							<h4 class="eckb-article-toc__title">${title}</h4>
							<nav class="eckb-article-toc-outline" role="navigation" aria-label="Article outline">
							<ul>
								<li>TOC is not displayed because there are no matching headers in the article.</li>
							</ul>
							</nav>
							</div>
						</div>	
						`;
					articleToc.html( html );
				}
				
			}
			
			let that = this;
			
			$('.eckb-article-toc__level a').on('click', function( e ){
				
				if ( $('.epkb-editor-preview').length ) {
					e.preventDefault();
					return;
				}
				
				let target = $(this).data('target');
				
				if ( ! target || $( '[data-id=' + target + ']' ).length == 0 ) {
					return false;
				}

				// calculate the speed of animation
				let initial_scroll_top = $('body, html').scrollTop();
				let current_scroll_top = $( '[data-id=' + target + ']').offset().top - that.offset;
				let animate_speed =  parseInt($(this).closest('.eckb-article-toc').data('speed'));

				$('body, html').animate({ scrollTop: current_scroll_top }, animate_speed);
				
				return false;
			});
			
			$(window).on( 'scroll', this.scrollSpy );
			
			this.scrollSpy();
			
			// scroll to element if it is in the hash 
			if ( ! location.hash ) {
				return;
			}
			
			let hash_link = $('[data-target=' + location.hash.slice(1) + ']' );
			if ( hash_link.length ) {
				hash_link.trigger( 'click' );
			}
		},
		
		getOptions: function() {
			
			if ( articleToc.data( 'min' ) ) {
				this.firstLevel = articleToc.data( 'min' );
			}
			
			if ( articleToc.data( 'max' ) ) {
				this.lastLevel = articleToc.data( 'max' );
			}
			
			if ( articleToc.data( 'offset' ) ) {
				this.offset = articleToc.data( 'offset' );
			}
			
			if ( typeof articleToc.data('exclude_class') !== 'undefined' ) {
				this.excludeClass = articleToc.data('exclude_class');
			}
			
			while ( this.firstLevel < this.lastLevel ) {
				this.searchStr += ', h' + this.firstLevel;
				this.firstLevel++;
			}
		},
		
		// return object with headers and their ids 
		getArticleHeaders: function () {
			let headers = [];
			let that = this;
			
			articleContent.find( that.searchStr ).each( function(){
					
				if ( $(this).text().length == 0 ) {
					return;
				}
					
				if ( that.excludeClass && $(this).hasClass( that.excludeClass ) ) {
					return;
				}
					
				let tid;
				let header = {};
						
				if ( $(this).data( 'id' ) ) {
					tid = $(this).data( 'id' );
				} else {
					tid = 'articleTOC_' + headers.length;
					$(this).attr( 'data-id', tid );
				}

				header.id = tid;
				header.title = $(this).text();
						
				if ('H1' == $(this).prop("tagName")) {
					header.level = 1;
				} else if ('H2' == $(this).prop("tagName")) {
					header.level = 2;
				} else if ('H3' == $(this).prop("tagName")) {
					header.level = 3;
				} else if ('H4' == $(this).prop("tagName")) {
					header.level = 4;
				} else if ('H5' == $(this).prop("tagName")) {
					header.level = 5;
				} else if ('H6' == $(this).prop("tagName")) {
					header.level = 6;
				}
					
				headers.push(header);
				
			});
				
			if ( headers.length == 0 ) {
				return headers;
			}
				
			// find max and min header level 
			let maxH = 1;
			let minH = 6;
				
			headers.forEach(function(header){
				if (header.level > maxH) {
					maxH = header.level
				}
					
				if (header.level < minH) {
					minH = header.level
				}
			});
				
			// move down all levels to have 1 lowest 
			if ( minH > 1 ) {
				headers.forEach(function(header, i){
					headers[i].level = header.level - minH + 1;
				});
			}
				
			// now we have levels started from 1 but maybe some levels do not exist
			// check level exist and decrease if not exist 
			let i = 1;
				
			while ( i < maxH ) {
				let levelExist = false;
				headers.forEach( function( header ) {
					if ( header.level == i ) {
						levelExist = true;
					}
				});
					
				if ( levelExist ) {
					// all right, level exist, go to the next 
					i++;
				} else {
					// no such levelm move all levels that more than current down and check once more
					headers.forEach( function( header, j ) {
						if ( header.level > i ) {
							headers[j].level = header.level - 1;
						}
					});
				}
				
				i++;
			}
				
			return headers;
		},
		
		// return html from headers object 
		getToCHTML: function ( headers, titleTag='h4' ) {
			let html;
				
			if ( articleToc.find('.eckb-article-toc__title').length ) {
					
				let title = articleToc.find('.eckb-article-toc__title').html();
				html = `
					<div class="eckb-article-toc__inner">
						<${titleTag} class="eckb-article-toc__title">${title}</${titleTag}>
						<nav class="eckb-article-toc-outline" role="navigation" aria-label="Article outline">
						<ul>
					`;
					
			} else {
					
				html = `
					<div class="eckb-article-toc__inner">
						<ul>
					`;
			}

			headers.forEach( function( header ) {
				let url = new URL( location.href );
				url.hash = header.id;
				url = url.toString();
				html += `<li class="eckb-article-toc__level eckb-article-toc__level-${header.level}"><a href="${url}" aria-label="Scrolls down the page to this heading" data-target="${header.id}">${header.title}</a></li>`;
			});
				
			html += `
						</ul>
						</nav>
					</div>
				`;
				
			return html;
		},
		
		// hightlight needed element 
		scrollSpy: function () {
			
			let currentTop = $(window).scrollTop();
			let currentBottom = $(window).scrollTop() + $(window).height();
			let highlighted = false;
			let $highlightedEl = false;
			
			$('.eckb-article-toc__level a').each(function(){
				
				$(this).removeClass('active');
				
				if ( highlighted ) {
					return true;
				}
				
				let target = $(this).data('target');
				
				if ( ! target || $( '[data-id=' + target + ']' ).length == 0 ) {
					return true;
				}
				
				if ( $( '[data-id=' + target + ']' ).offset().top < currentBottom && $( '[data-id=' + target + ']' ).offset().top > currentTop ) {
					$(this).addClass('active');
					highlighted = true;
					$highlightedEl = $(this);
				}
				
			});
			
			// check if the highlighted element is visible 
			if ( ! $highlightedEl || ! highlighted ){
				return;
			}
			
			let hightlightPosition = $highlightedEl.position().top;
			
			if ( hightlightPosition < 0 || hightlightPosition > $highlightedEl.closest('.eckb-article-toc__inner').height() ) {	
				$highlightedEl.closest('.eckb-article-toc__inner').scrollTop( hightlightPosition - $highlightedEl.closest('.eckb-article-toc__inner').find( '.eckb-article-toc__title' ).position().top );
			}
		},
		
	};
	

		
	setTimeout ( function() {

		if ( articleToc.length ) {
			TOC.init();
		}

		// Get the Article Content Body Position
		let articleContentBodyPosition = $('#eckb-article-content-body' ).position();
		let window_width = $(window).width();
		let default_mobile_breakpoint = 768 // This is the default set on first installation.
		let mobile_breakpoint = typeof $('#eckb-article-page-container-v2').data('mobile_breakpoint') == "undefined" ? default_mobile_breakpoint : $('#eckb-article-page-container-v2').data('mobile_breakpoint');

		//TODO: Dave - Change Sidebar position if TOC is in the Middle
		// If the setting is on, Offset the Sidebar to match the article Content
		if( $('.eckb-article-page--L-sidebar-to-content').length > 0 && window_width > mobile_breakpoint ){
			$('#eckb-article-page-container-v2').find( '#eckb-article-left-sidebar ').css( "margin-top" , articleContentBodyPosition.top+'px' );
		}

		if( $('.eckb-article-page--R-sidebar-to-content').length > 0 && window_width > mobile_breakpoint ){
			$('#eckb-article-page-container-v2').find( '#eckb-article-right-sidebar ').css( "margin-top" , articleContentBodyPosition.top+'px' );
		}

		if ( articleToc.length ) {
			mobile_TOC();
		}
	}, 500 );

	function mobile_TOC() {
		let window_width = $(window).width();
		let mobile_breakpoint = typeof $('#eckb-article-page-container-v2').data('mobile_breakpoint') == "undefined" ? 111 : $('#eckb-article-page-container-v2').data('mobile_breakpoint');

		if ( window_width > mobile_breakpoint ) {
			return;
		}

		if ( $('#eckb-article-content-header-v2 .eckb-article-toc').length ) {
			return;
		}

		if ( $('#eckb-article-left-sidebar .eckb-article-toc').length ) {
			$('#eckb-article-content-header-v2').append($('#eckb-article-left-sidebar .eckb-article-toc'));
			return;
		}

		if ( $('#eckb-article-right-sidebar .eckb-article-toc').length ) {
			$('#eckb-article-content-header-v2').append($('#eckb-article-right-sidebar .eckb-article-toc'));
		}
	}


	/********************************************************************
	 *                      Logged in users
	 ********************************************************************/
	$( document ).on( 'click', '#eckb-kb-create-demo-data', function( e ) {
		e.preventDefault();

		// Do nothing on Editor preview mode
		if ( $( this ).closest( '.epkb-editor-preview' ).length ) {
			return;
		}

		let postData = {
			action: 'epkb_create_kb_demo_data',
			epkb_kb_id: $( this ).data( 'id' ),
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
		};

		let parent_container = $( this ).closest( '.eckb-kb-no-content' ),
			confirmation_box = $( '.eckb-kb-no-content' ).find( '#epkb-created-kb-content' );

		$.ajax( {
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: epkb_vars.ajaxurl,
			beforeSend: function( xhr ) {
				epkb_loading_Dialog( 'show', '', parent_container );
			}

		} ).done( function( response ) {
			response = ( response ? response : '' );
			if ( typeof response.message !== 'undefined' ) {
				confirmation_box.addClass( 'epkb-dialog-box-form--active' );
			}

		} ).fail( function( response, textStatus, error ) {
						confirmation_box.addClass( 'epkb-dialog-box-form--active' ).find( '.epkb-dbf__body' ).html( error );

		} ).always( function() {
			epkb_loading_Dialog( 'remove', '', parent_container );
		} );
	});

	function epkb_loading_Dialog( displayType, message, parent_container ){

		if ( displayType === 'show' ) {

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
			parent_container.append( output );

		} else if( displayType === 'remove' ) {

			// Remove loading dialogs.
			parent_container.find( '.epkb-admin-dialog-box-loading' ).remove();
			parent_container.find( '.epkb-admin-dialog-box-overlay' ).remove();
		}
	}

	$( document ).on( 'click', '.eckb-kb-no-content #epkb-created-kb-content .epkb-dbf__footer__accept__btn', function() {
		location.reload();
	} );

	// Sidebar V2 ------------------------------------------------------------------------------------------------------/
	if( $( '#elay-sidebar-container-v2' ).length == 0 && $( '#epkb-sidebar-container-v2' ).length > 0 ){

		function epkb_toggle_category_icons( icon, icon_name ) {

			var icons_closed = [ 'ep_font_icon_plus', 'ep_font_icon_plus_box', 'ep_font_icon_right_arrow', 'ep_font_icon_arrow_carrot_right', 'ep_font_icon_arrow_carrot_right_circle', 'ep_font_icon_folder_add' ];
			var icons_opened = [ 'ep_font_icon_minus', 'ep_font_icon_minus_box', 'ep_font_icon_down_arrow', 'ep_font_icon_arrow_carrot_down', 'ep_font_icon_arrow_carrot_down_circle', 'ep_font_icon_folder_open' ];

			var index_closed = icons_closed.indexOf( icon_name );
			var index_opened = icons_opened.indexOf( icon_name );

			if ( index_closed >= 0 ) {
				icon.removeClass( icons_closed[index_closed] );
				icon.addClass( icons_opened[index_closed] );
			} else if ( index_opened >= 0 ) {
				icon.removeClass( icons_opened[index_opened] );
				icon.addClass( icons_closed[index_opened] );
			}
		}

		function epkb_open_and_highlight_selected_article_v2() {
			// active article id
			// TODO remove .kb-article-id
			var id =  ( typeof $('#eckb-article-content').data('article-id') !== 'undefined' ) ? $('#eckb-article-content').data('article-id') : $('.kb-article-id').attr('id');

			// TODO remove .kb-article-id
			// true if we have article with multiple categories (locations) in the SBL; ignore old links
			var $el = typeof $('#eckb-article-content').data('kb_article_seq_no') !== 'undefined' ? $('#eckb-article-content') : $('.kb-article-id');

			if ( typeof $el.data('kb_article_seq_no') !== 'undefined' && $el.data('kb_article_seq_no') > 0 ) {

				var new_id = id + '_' + $el.data('kb_article_seq_no');

				id = $('#sidebar_link_' + new_id).length > 0 ? new_id : id;
			}

			// after refresh highlight the Article link that is now active
			$('.epkb-sidebar__cat__top-cat li').removeClass( 'active' );
			$('.epkb-category-level-1').removeClass( 'active' );
			$('.epkb-category-level-2-3').removeClass( 'active' );
			$('.epkb-sidebar__cat__top-cat__heading-container').removeClass( 'active' );
			$('#sidebar_link_' + id).addClass('active');

			// open all subcategories 
			$('#sidebar_link_' + id).parents('.epkb-sub-sub-category, .epkb-articles').each(function(){

				let $button = $(this).parent().children('.epkb-category-level-2-3');
				if ( $button.length == 0 ) {
					return true;
				}

				if ( ! $button.hasClass('epkb-category-level-2-3') ) {
					return true;
				}

				$button.next().show();
				$button.next().next().show();

				let icon = $button.find('.epkb_sidebar_expand_category_icon');
				if ( icon.length > 0 ) {
					epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
				}
			});

			// open main accordeon 
			$('#sidebar_link_' + id).closest('.epkb-sidebar__cat__top-cat').parent().toggleClass( 'epkb-active-top-category' );
			$('#sidebar_link_' + id).closest('.epkb-sidebar__cat__top-cat').find( $( '.epkb-sidebar__cat__top-cat__body-container') ).show();

			let icon = $('#sidebar_link_' + id).closest('.epkb-sidebar__cat__top-cat').find('.epkb-sidebar__cat__top-cat__heading-container .epkb-sidebar__heading__inner span');
			if ( icon.length > 0 ) {
				epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
			}
		}

		var sidebarV2 = $('#epkb-sidebar-container-v2');

		// TOP-CATEGORIES -----------------------------------/
		// Show or hide article in sliding motion
		sidebarV2.on('click', '.epkb-top-class-collapse-on', function (e) {

			// prevent open categories when click on editor tabs 
			if ( typeof e.originalEvent !== 'undefined' && ( $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--active') || $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--parent') ) ) {
				return;
			}

			$( this ).parent().toggleClass( 'epkb-active-top-category' );
			$( this).parent().find( $( '.epkb-sidebar__cat__top-cat__body-container') ).slideToggle();
		});

		// Icon toggle - toggle between open icon and close icon
		sidebarV2.on('click', '.epkb-sidebar__cat__top-cat__heading-container', function (e) {

			// prevent open categories when click on editor tabs 
			if ( typeof e.originalEvent !== 'undefined' && ( $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--active') || $(e.originalEvent.target).hasClass('epkb-editor-zone__tab--parent') ) ) {
				return;
			}

			var icon = $(this).find('.epkb-sidebar__heading__inner span');
			if ( icon.length > 0 ) {
				epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
			}
		});

		// SUB-CATEGORIES -----------------------------------/
		// Show or hide article in sliding motion
		sidebarV2.on('click', '.epkb-category-level-2-3', function () {

			// show lower level of categories and show articles in this category
			$( this ).next().slideToggle();
			$( this ).next().next().slideToggle();

		});
		// Icon toggle - toggle between open icon and close icon
		sidebarV2.on('click', '.epkb-category-level-2-3', function () {
			var icon = $(this).find('span');
			if ( icon.length > 0 ) {
				epkb_toggle_category_icons(icon, icon.attr('class').match(/\ep_font_icon_\S+/g)[0]);
			}
		});

		// SHOW ALL articles functionality
		sidebarV2.on('click', '.epkb-show-all-articles', function () {

			$( this ).toggleClass( 'active' );
			var parent = $( this ).parent( 'ul' );
			var article = parent.find( 'li');

			//If this has class "active" then change the text to Hide extra articles
			if ( $(this).hasClass( 'active') ) {

				//If Active
				$(this).find('.epkb-show-text').addClass('epkb-hide-elem');
				$(this).find('.epkb-hide-text').removeClass('epkb-hide-elem');
				$(this).attr( 'aria-expanded','true' );

			} else {
				//If not Active
				$(this).find('.epkb-show-text').removeClass('epkb-hide-elem');
				$(this).find('.epkb-hide-text').addClass('epkb-hide-elem');
				$(this).attr( 'aria-expanded','false' );
			}

			$( article ).each(function() {
				//If has class "hide" remove it and replace it with class "Visible"
				if ( $(this).hasClass( 'epkb-hide-elem') ) {
					$(this).removeClass('epkb-hide-elem');
					$(this).addClass('visible');
				} else if ( $(this).hasClass( 'visible')) {
					$(this).removeClass('visible');
					$(this).addClass('epkb-hide-elem');
				}
			});
		});

		epkb_open_and_highlight_selected_article_v2();
	}
});
