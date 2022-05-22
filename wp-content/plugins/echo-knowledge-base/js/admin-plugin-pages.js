jQuery(document).ready(function($) {

	var epkb = $( '#ekb-admin-page-wrap' );

	// Set special CSS class to #wpwrap for only KB admin pages
	if ( $( epkb ).find( '.epkb-admin__content' ).length > 0 ) {
		$( '#wpwrap' ).addClass( 'epkb-admin__wpwrap' );
	}

	let remove_message_timeout = false;


	/*************************************************************************************************
	 *
	 *          KB CONFIGURATION PAGE
	 *
	 ************************************************************************************************/

	// KBs DROPDOWN - reload on change
	$( '#epkb-list-of-kbs' ).on( 'change', function(e) {

		let selected_option = $( this ).find( 'option:selected' );

		// Do nothing for options added by hook (they should execute their own JS)
		if ( selected_option.attr( 'data-plugin' ) !== 'core' ) {
			return;
		}

		// Redirect if user does not have access for the current page in the selected KB
		if ( selected_option.val() === 'closed' ) {
			window.location = selected_option.attr( 'data-target' );
			return;
		}

		let current_location_href = window.location.href;

		// Handle archived KBs page
		if ( $( this ).val() === 'archived' ) {
			let location_parts = window.location.href.split( '#' );
			window.location = location_parts[0] + '&archived-kbs=on';
			return;
		} else {
			current_location_href = current_location_href.replaceAll( '&archived-kbs=on', '' ).replaceAll( '&epkb_after_kb_setup', '' );
		}

		// Handle external link - Open link in new tab and stay on the previous item selected in the dropdown
		let data_link = selected_option.attr( 'data-link' );
		if ( typeof data_link !== 'undefined' && data_link.length > 0 ) {
			window.open( data_link, '_blank' );
			$( this ).val( $( this ).attr( 'data-active-kb-id' ) ).trigger( 'change' );
			return;
		}

		let prev_kb_id = $( this ).attr( 'data-active-kb-id' );
		let kb_id = $( this ).val();
		if ( kb_id ) {
			$( this ).attr( 'data-active-kb-id', kb_id );
			window.location.href = current_location_href.replaceAll( 'epkb_post_type_' + prev_kb_id, 'epkb_post_type_' + kb_id );
		}
	});

	// Rename KB
	$('.epkb-admin__kb-rename .epkb-edit-toggle').on('click', function(){
		$(this).parent().parent().find('.epkb-admin__kb-rename__edit').slideToggle();
	});

	// Save Access Control settings
	$( '#epkb_save_access_control' ).on( 'click', function() {
		epkb_send_ajax(
			{
				action: 'epkb_save_access_control',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
				epkb_kb_id: $( '#epkb-list-of-kbs' ).val(),
				admin_eckb_access_need_help_read: $( '#admin_eckb_access_need_help_read input[type="radio"]:checked' ).val(),
				admin_eckb_access_search_analytics_read: $( '#admin_eckb_access_search_analytics_read input[type="radio"]:checked' ).val(),
				admin_eckb_access_addons_news_read: $( '#admin_eckb_access_addons_news_read input[type="radio"]:checked' ).val(),
				admin_eckb_access_order_articles_write: $( '#admin_eckb_access_order_articles_write input[type="radio"]:checked' ).val(),
				admin_eckb_access_frontend_editor_write: $( '#admin_eckb_access_frontend_editor_write input[type="radio"]:checked' ).val()
			},
			function( response ) {
				$( '.eckb-top-notice-message' ).remove();
				if ( typeof response.message !== 'undefined' ) {
					clear_bottom_notifications();
					$( 'body' ).append( response.message );
				}
				clear_message_after_set_time();
			}
		);
	});

	// Update KB Nickname
	$( '#epkb-admin__kb-rename__form' ).on( 'submit', function( event ) {
		event.preventDefault();

		let $form = $( this );
		let new_kb_name = $form.find( 'input[name="epkb_kb_name_input"]' ).val();
		let kb_id = $form.find( 'input[name="epkb_kb_id"]' ).val();
		let postData = {
			action: 'epkb_update_kb_name',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			epkb_kb_id: kb_id,
			epkb_kb_name_input: new_kb_name
		};

		epkb_send_ajax( postData, function( response ){
			$( '.eckb-top-notice-message' ).remove();
			if ( typeof response.message !== 'undefined' ) {
				clear_bottom_notifications();
				$( 'body' ).append( response.message );
			}
			clear_message_after_set_time();
			$( '#epkb-admin__kb-rename__value' ).html( new_kb_name );
			$( '#epkb-list-of-kbs option[value="' + kb_id + '"]' ).html( new_kb_name );
		} );
	});

	// open panel
	$('#epkb-admin__boxes-list__tools .epkb-kbnh__feature-links .epkb-primary-btn').on('click', function(){

		let id = $(this).prop('id');

		if ( id == 'epkb_core_export' ) {
			$('form.epkb-export-kbs').submit();
			return false;
		}

		if ( $('.epkb-kbnh__feature-panel-container--' + id).length == 0 ) {
			return false;
		}

		$(this).closest('.epkb-setting-box__list').find('.epkb-kbnh__feature-container').css({'display' : 'none'});
		$(this).closest('.epkb-setting-box__list').find('.epkb-kbnh__feature-panel-container--' + id).css({'display' : 'block'});

		return false;
	});

	// back button
	$(document).on( 'epkb_hide_export_import_panels', function(){
		$('#epkb-admin__boxes-list__tools .epkb-setting-box__list>.epkb-kbnh__feature-container').css({'display' : 'flex'});
		$('#epkb-admin__boxes-list__tools .epkb-setting-box__list>.epkb-kbnh__feature-panel-container').css({'display' : 'none'});
	} );

	$('.epkb-kbnh-back-btn').on('click', function(){
		$(document).trigger('epkb_hide_export_import_panels');
		return false;
	});

	/*************************************************************************************************
	 *
	 *          ADMIN PAGES
	 *
	 ************************************************************************************************/

	/* Admin Top Panel Items -----------------------------------------------------*/
	$( '.epkb-admin__top-panel__item' ).on( 'click', function() {

		// Warning for Frontend Editor
		if ( $( this ).hasClass( 'epkb-article-structure-dialog' ) ) {
			return;
		}

		let active_top_panel_item_class = 'epkb-admin__top-panel__item--active';
		let active_boxes_list_class = 'epkb-admin__boxes-list--active';
		let active_secondary_panel_class = 'epkb-admin__secondary-panel--active';
		let active_secondary_item_class = 'epkb-admin__secondary-panel__item--active';

		// Do nothing for already active item, only trigger secondary item to make sure we have correct hash in URL
		if ( $( this ).hasClass( active_top_panel_item_class ) ) {
			let active_secondary_item = $( active_secondary_panel_class ).find( '.' + active_secondary_item_class ).length
				? $( active_secondary_panel_class ).find( '.' + active_secondary_item_class )
				: $( $( active_secondary_panel_class ).find( '.epkb-admin__secondary-panel__item' )[0] );
			setTimeout( function () { active_secondary_item.trigger( 'click' ); }, 100 );
			return;
		}

		let list_key = $( this ).attr( 'data-target' );

		// Change class for active Top Panel item
		$( '.epkb-admin__top-panel__item' ).removeClass( active_top_panel_item_class );
		$( this ).addClass( active_top_panel_item_class );

		// Change class for active Boxes List
		$( '.epkb-admin__boxes-list' ).removeClass( active_boxes_list_class );
		$( '#epkb-admin__boxes-list__' + list_key ).addClass( active_boxes_list_class );

		// Change class for active Secondary Panel and trigger click event on active secondary tab to initialize JS and AJAX loading content
		$( '.epkb-admin__secondary-panel' ).removeClass( active_secondary_panel_class );
		let active_secondary_panel = $( '#epkb-admin__secondary-panel__' + list_key ).addClass( active_secondary_panel_class );
		let active_secondary_item = active_secondary_panel.find( '.' + active_secondary_item_class ).length
			? active_secondary_panel.find( '.' + active_secondary_item_class )
			: $( active_secondary_panel.find( '.epkb-admin__secondary-panel__item' )[0] );
		setTimeout( function () { active_secondary_item.trigger( 'click' ); }, 100 );

		// Licenses tab on Add-ons page - support for existing add-ons JS handlers
		let active_top_panel_item = this;
		setTimeout( function () {
			if ( $( active_top_panel_item ).attr( 'id' ) === 'eckb_license_tab' ) {
				$( '#eckb_license_tab' ).trigger( 'click' );
			}
		}, 100 );

		// track event if user visited 'Features' tab first time
		if ( list_key === 'features' && ! $( this ).hasClass( 'epkb-admin__flag--visited' ) ) {
			$.ajax( {
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'epkb_features_tab_visited',
					_wpnonce_epkb_ajax_action: epkb_vars.nonce
				},
				url: ajaxurl
			} ).done( function() {
				$( '#epkb-admin__step-cta-box__features .epkb-admin__step-cta-box__header' ).after( '<span class="epkb-admin__step-cta-box__content__icon epkbfa epkbfa-check-circle"></span>' );
			});
		}

		// Update anchor
		window.location.hash = '#' + list_key;
	});

	// Set correct active tab after the page reloading
	(function(){
		let url_parts = window.location.href.split( '#' );

		// Set first item as active if there is no any anchor
		if ( url_parts.length === 1 ) {
			$( $( '.epkb-admin__top-panel__item' )[0] ).trigger( 'click' );
			return;
		}

		let target_kyes = url_parts[1].split( '__' );

		let target_main_items = $( '.epkb-admin__top-panel__item[data-target="' + target_kyes[0] + '"]' );

		// If no target items was found, then set the first item as active
		if ( target_main_items.length === 0 ) {
			$( $( '.epkb-admin__top-panel__item' )[0] ).trigger( 'click' );
			return;
		}

		// Change class for active item
		$( target_main_items[0] ).trigger( 'click' );

		// Key for Secondary item was specified and it is not empty otherwise take the first Secondary item
		let target_secondary_item_selector = '.epkb-admin__secondary-panel__item[data-target="' + url_parts[1] + '"]';
		let target_secondary_item = target_kyes.length > 1 && target_kyes[1].length && $( target_secondary_item_selector ).length
			? $( target_secondary_item_selector )
			: $( '.epkb-admin__secondary-panel--active' ).find( '.epkb-admin__secondary-panel__item' )[0];

		// Change class for active item
		setTimeout( function() { $( target_secondary_item ).trigger( 'click' ); }, 100 );
	})();

	/* Admin Secondary Panel Items -----------------------------------------------*/
	$( '.epkb-admin__secondary-panel__item' ).on( 'click', function() {

		// Warning for Frontend Editor
		if ( $( this ).hasClass( 'epkb-article-structure-dialog' ) ) {
			return;
		}

		let active_secondary_panel_item_class = 'epkb-admin__secondary-panel__item--active';
		let active_secondary_boxes_list_class = 'epkb-setting-box__list--active';

		// Do nothing for already active item, only make sure we have correct hash in URL
		if ( $( this ).hasClass( active_secondary_panel_item_class ) ) {
			window.location.hash = '#' + $( this ).attr( 'data-target' );
			return;
		}

		let list_key = $( this ).attr( 'data-target' );
		let parent_list_key = list_key.split( '__' )[0];

		// Change class for active Top Panel item
		$( '#epkb-admin__secondary-panel__' + parent_list_key ).find( '.epkb-admin__secondary-panel__item' ).removeClass( active_secondary_panel_item_class );
		$( this ).addClass( active_secondary_panel_item_class );

		// Change class for active Boxes List
		$( '#epkb-admin__boxes-list__' + parent_list_key ).find( '.epkb-setting-box__list' ).removeClass( active_secondary_boxes_list_class );
		$( '#epkb-setting-box__list-' + list_key ).addClass( active_secondary_boxes_list_class );

		// Update anchor
		window.location.hash = '#' + list_key;
	});

	/* Tabs ----------------------------------------------------------------------*/
	(function(){

		/**
		 * Toggles Tabs
		 *
		 * The HTML Structure for this is as follows:
		 * 1. tab_nav_container must be the main ID or class element for the navigation tabs containing the tabs.
		 *    Those nav items must have a class of nav_tab.
		 *
		 * 2. tab_panel_container must be the main ID or class element for the panels. Those panel items must have
		 *    a class of ekb-admin-page-tab-panel
		 *
		 * @param tab_nav_container  ( ID/class containing the Navs )
		 * @param tab_panel_container ( ID/class containing the Panels
		 */
		(function(){
			function tab_toggle( tab_nav_container, tab_panel_container ){

				epkb.find( tab_nav_container+ ' > .nav_tab' ).on( 'click', function(){

					//Remove all Active class from Nav tabs
					epkb.find(tab_nav_container + ' > .nav_tab').removeClass('active');

					//Add Active class to clicked Nav
					$(this).addClass('active');

					//Remove Class from the tab panels
					epkb.find(tab_panel_container + ' > .ekb-admin-page-tab-panel').removeClass('active');

					//Set Panel active
					var number = $(this).index() + 1;
					epkb.find(tab_panel_container + ' > .ekb-admin-page-tab-panel:nth-child( ' + number + ' ) ').addClass('active');
				});
			}

			tab_toggle( '.add_on_container .epkb-main-nav > .epkb-admin-pages-nav-tabs', '#add_on_panels' );
			tab_toggle( '.epkb-main-nav > .epkb-admin-pages-nav-tabs', '#main_panels' );
			tab_toggle( '#help_tabs_nav', '#help_tab_panel' );
			tab_toggle( '#new_features_tabs_nav', '#new_features_tab_panel' );
		})();

	})();

	/* Toggle admin tabs  ----------------------------------------------------------------------*/
	$('.epkb-header__tab').on('click',function(e){

		let id = $( this ).attr( 'id' );

		// Clear all active classes
		$( '.epkb-header__tab' ).removeClass( 'epkb-header__tab--active' );
		$( '.epkb-content__tab' ).removeClass( 'epkb-content__tab--active' );
		$( this ).addClass( 'epkb-header__tab--active' );

		// Add Class to clicked on tab
		$( '#'+id+'_content' ).addClass( 'epkb-content__tab--active' );

	});

	/* Misc ----------------------------------------------------------------------*/
	(function(){

		// TOGGLE DEBUG
		epkb.find( '#epkb_toggle_debug' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html('');

			let postData = {
				action: 'epkb_toggle_debug',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function() {
				location.reload();
			} );
		});

		// TOGGLE ADVANCED SEARCH DEBUG
		epkb.find( '#epkb_enable_advanced_search_debug' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html('');

			let postData = {
				action: 'epkb_enable_advanced_search_debug',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce
			};

			epkb_send_ajax( postData, function() {
				location.reload();
			} );
		});

		// ADD-ON PLUGINS + OUR OTHER PLUGINS - PREVIEW POPUP
		 (function(){
			//Open Popup larger Image
			epkb.find( '.featured_img' ).on( 'click', function( e ){

				e.preventDefault();
				e.stopPropagation();

				epkb.find( '.image_zoom' ).remove();

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
					'<span class="close icon_close"></span>'+
					'</div>' + '');

				//Close Plugin Preview Popup
				$('html, body').on('click.epkb', function(){
					$( '#epkb_image_zoom' ).remove();
					$('html, body').off('click.epkb');
				});
			});
		})();

		//Info Icon for Licenses
		$( '#add_on_panels' ).on( 'click', '.ep_font_icon_info', function(){

			$( this ).parent().find( '.ep_font_icon_info_content').toggle();

		});
	})();

	// Copy to clipboard button
	$( '.epkb-kbnh__feature-copy-link' ).on('click', function( e ){
		e.preventDefault();
		let textarea = document.createElement( 'textarea' );
		textarea.value = $( this ).data( 'copy' );
		textarea.style.position = 'fixed';
		document.body.appendChild( textarea );
		textarea.focus();
		textarea.select();
		document.execCommand( 'copy' );
		textarea.remove();
	});

	/*************************************************************************************************
	 *
	 *          ANALYTICS PAGE
	 *
	 ************************************************************************************************/
	var analytics_container = $( '.epkb-analytics-page-container' );

	//When Top Nav is clicked on show it's content.
	analytics_container.find( '.page-icon' ).on( 'click', function(){

		// Do nothing for already active page icon
		if ( $( this ).closest( '.eckb-nav-section' ).hasClass( 'epkb-active-nav' ) ) {
			return;
		}

		//Reset ( Hide all content, remove all active classes )
		analytics_container.find( '.eckb-config-content' ).removeClass( 'epkb-active-content' );
		analytics_container.find( '.eckb-nav-section' ).removeClass( 'epkb-active-nav' );

		//Get ID of Icon
		var id = $( this ).attr( 'id' );

		//Target Content from icon ID
		analytics_container.find( '#' + id + '-content').addClass( 'epkb-active-content' );

		//Set this Nav to be active
		analytics_container.find( this ).parents( '.eckb-nav-section' ).addClass( 'epkb-active-nav' )

	});


	/*************************************************************************************************
	 *
	 *          CATEGORY ICONS
	 *
	 ************************************************************************************************/
	if ($('.epkb-categories-icons').length) {
		// Tabs
		$('.epkb-categories-icons__button').on('click',function(){

			if ($(this).hasClass('epkb-categories-icons__button--active')) {
				return;
			}

			$('.epkb-categories-icons__button').removeClass('epkb-categories-icons__button--active');
			$(this).addClass('epkb-categories-icons__button--active');


			$('.epkb-categories-icons__tab-body').slideUp('fast');

			var val = $(this).data('type');

			if ( $('.epkb-categories-icons__tab-body--' + val).length ) {
				$('.epkb-categories-icons__tab-body--' + val).slideDown('fast');
			}

			$('#epkb_head_category_icon_type').val(val);
		});

		// Icon Save
		$('.epkb-icon-pack__icon').on('click',function(){
			$('.epkb-icon-pack__icon').removeClass('epkb-icon-pack__icon--checked');
			$(this).addClass('epkb-icon-pack__icon--checked');
			$('#epkb_head_category_icon_name').val($(this).data('key'));
		});

		// Image save
		$('.epkb-category-image__button').on('click',function(e){
			e.preventDefault();

			var button = $(this),
				custom_uploader = wp.media({
					title: button.data('title'),
					library : {
						type : 'image'
					},
					multiple: false
				}).on('select', function() {
					var attachment = custom_uploader.state().get('selection').first().toJSON();

					$('#epkb_head_category_icon_image').val(attachment.id);
					$('.epkb-category-image__button').removeClass('epkb-category-image__button--no-image');
					$('.epkb-category-image__button').addClass('epkb-category-image__button--have-image');
					$('.epkb-category-image__button').css({'background-image' : 'url('+attachment.url+')'});
				})
					.open();
		});

		// Show/Hide Categories block depends on category parent
		$('#parent').on( 'change', function(){

			var category_level;
			var option;
			var select = $(this);
			var template = $('#epkb_head_category_template').val();
			var hide_block = false;

			select.find('option').each(function(){
				if ( $(this).val() == select.val() ) {
					option = $(this);
				}
			});

			if ( option.val() == '-1' ) {
				category_level = 1;
			} else if ( option.hasClass('level-0') ) {
				category_level = 2;
			} else {
				category_level = 3;
			}

			if ( template == 'Tabs' ) {
				if ( category_level !== 2 ) {
					hide_block = true;
				}
			} else if ( template == 'Sidebar' ) {
				hide_block = true;
			} else {
				// all else layouts
				if ( category_level > 1 ) {
					hide_block = true;
				}
			}

			if ( hide_block ) {
				$('.epkb-categories-icons').hide();
				$('.epkb-categories-icons+.epkb-term-options-message').show();
			} else {
				$('.epkb-categories-icons').show();
				$('.epkb-categories-icons+.epkb-term-options-message').hide();
			}

		});

		function epkb_reset_categories_icon_box() {
			$('#epkb_font_icon').trigger('click');
			$('#epkb_head_category_thumbnail_size').val( $('#epkb_head_category_thumbnail_size').find('option').eq(0).val() );
			$('.epkb-category-image__button').addClass('epkb-category-image__button--no-image');
			$('.epkb-category-image__button').removeClass('epkb-category-image__button--have-image');
			$('.epkb-category-image__button').css({'background-image' : ''});
			$('#epkb_head_category_icon_image').val(0);
			$('div[data-key=ep_font_icon_document]').trigger('click');
		}

		// look when new category was added
		$( document ).ajaxComplete(function( event, xhr, settings ) {

			if ( ! settings ) {
				return;
			}

			let data = settings.data.split('&');
			let i;

			for (i = 0; i < data.length; i++) {
				sParameterName = data[i].split('=');

				if (sParameterName[0] === 'action' && sParameterName[1] === 'add-tag' ) {
					epkb_reset_categories_icon_box();

					$("html, body").animate({ scrollTop: $('.wp-heading-inline').offset().top }, 300);
				}
			}
		});
	}

	/*************************************************************************************************
	 *
	 *          CATEGORY ORDER LINK
	 *
	 ************************************************************************************************/
	if ( $('#epkb-admin__categories_sorting_link').length ) {
		$('#epkb-admin__categories_sorting_link').insertAfter('.bulkactions');
		$('#epkb-admin__categories_sorting_link').css('display', 'block');
	}
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

				if ( typeof callbackParam === 'undefined' ) {
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


	/*************************************************************************************************
	 *
	 *          DIALOGS
	 *
	 ************************************************************************************************/

	/**
	  * Displays a Center Dialog box with a loading icon and text.
	  *
	  * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	  * This code is used in these files, any changes here must be done to the following files.
	  *   - admin-plugin-pages.js
	  *   - admin-kb-config-scripts.js
	  *   - admin-kb-wizard-script.js
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

	// Close Button Message if Close Icon clicked
	$( document.body ).on( 'click', '.epkb-close-notice', function() {
		let bottom_message = $( this ).closest( '.eckb-bottom-notice-message' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 1000);
	} );

	// HELP ICON DIALOG
	// open dialog but re-center when loading finished so that it stays in the center of the screen
	var epkb_help_dialog = $("#epkb-dialog-info-icon").dialog(
		{
			resizable: false,
			autoOpen: false,
			modal: true,
			buttons: {
				Ok: function ()
				{
					$( this ).dialog( "close" );
				}
			},
			close: function ()
			{
				$('#epkb-dialog-info-icon-msg').html();
			}
		}
	);
	epkb.find( '.ekb-admin-page-tab-panel, .epkb-config-sidebar-options' ).on('click', '.info-icon',  function () {
		var has_image = false;
		var img = '';
		var title = $( this ).parent().find( '.label' ).text();
		title = ( title ? title : '' );

		var msg = $( this ).find( 'p' ).html();
		if( msg )
		{
			var arrayOfStrings = msg.split('@');
			msg = arrayOfStrings[0] ? arrayOfStrings[0] : epkb_vars.help_text_coming;
			if ( arrayOfStrings[1] ) {
				has_image = true;
				img = '<img class="epkb-help-image" src="' + arrayOfStrings[1] + '">';
			}
		} else {
			msg = epkb_vars.help_text_coming;
		}

		$('#epkb-dialog-info-icon-msg').html('<p>' + msg + '</p><br/>' + img);

		epkb_help_dialog.dialog( {
			title: title,
			width: (has_image ? 1000 : 400),
			maxHeight: (has_image ? 750 : 300),
			open: function ()
			{
				// reposition dialog after image loads
				$("#epkb-dialog-info-icon").find('.epkb-help-image').one("load", function ()
				{
					epkb_help_dialog.dialog('option', { position: { my: "center", at: "center", of: window } } );
					//  $(this).dialog({position: {my: "center", at: "center", of: window}});
				});

				// close dialog if user clicks outside of it
				$( '.ui-widget-overlay' ).on( 'click', function ()
				{
					$("#epkb-dialog-info-icon").dialog('close')
				});
			}
		}).dialog('open');
	});

	// AJAX DIALOG USED BY KB CONFIGURATION AND SETTINGS PAGES
	$('#epkb-ajax-in-progress').dialog({
		resizable: false,
		height: 70,
		width: 200,
		modal: false,
		autoOpen: false
	}).hide();


	// New ToolTip
	epkb.on( 'click', '.epkb__option-tooltip__button', function(){
		let tooltip_on = $( this ).parent().find( '.epkb__option-tooltip__contents' ).css('display') == 'block';

		$('.epkb__option-tooltip__contents').fadeOut();

		if ( ! tooltip_on ) {
			$( this ).parent().find( '.epkb__option-tooltip__contents' ).fadeIn();
		}
	});

	// ToolTip
	epkb.on( 'click', '.eckb-tooltip-button', function(){
		$( this ).parent().find( '.eckb-tooltip-contents' ).fadeToggle();
	});

	// SHOW INFO MESSAGES
	function epkb_admin_notification( $title, $message , $type ) {
		return '<div class="eckb-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
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

	/**
	 * Accordion for the options 
	 */
	$('body').on('click', '.eckb-wizard-accordion .eckb-wizard-option-heading', function(){
		var wrap = $(this).closest('.eckb-wizard-accordion');
		var currentItem = $(this).closest('.eckb-wizard-accordion__body-content');
		var isCurrentActive = currentItem.hasClass('eckb-wizard-accordion__body-content--active');

		wrap.find('.eckb-wizard-accordion__body-content').removeClass('eckb-wizard-accordion__body-content--active');
		
		if (!isCurrentActive) {
			currentItem.addClass('eckb-wizard-accordion__body-content--active');
		}
		
	});

	$('body').on('click', '#eckb-wizard-main-page-preview a, .epkb-wizard-theme-panel-container a, #eckb-wizard-article-page-preview a', false);

	//Admin Notice
	$('.epkb-notice-remind').on('click',function(e){
		e.preventDefault();
		$(this).parent().parent().remove();
	});

	//Dismiss ongoing notice
	$(document).on( 'click', '.epkb-notice-dismiss', function( event ) {
		event.preventDefault();

		$('#'+$(this).data('notice-id')).slideUp();

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

	// Editor Disabled dialog if article structure v2
	$( '#epkb-editor-disabled .epkb-article-structure-dialog' ).on( 'click',function(e) {
		e.preventDefault();
		$( '#epkb-editor-disabled' ).find( '.epkb-dialog-box-form' ).toggleClass( 'epkb-dialog-box-form--active' );
		return false;
	});

	// Shared handlers for close buttons of Dialog Box Form
	$('.epkb-dialog-box-form .epkb-dbf__close, .epkb-dialog-box-form .epkb-dbf__footer__cancel').on('click',function(){
		$(this).closest( '.epkb-dialog-box-form' ).toggleClass( 'epkb-dialog-box-form--active' );
	});
	$('.epkb-dialog-box-form .epkb-dbf__footer__accept__btn').on('click',function(){
		$(this).closest('.epkb-dialog-box-form').find('form').trigger( 'submit' );
	});

	// Reveal Settings ( Edit Button )
	$( 'body' ).find( '.epkb__header__edit' ).on( 'click', function(){
		$( this ).parent().parent().find('.epkb-ts__input-container').slideToggle();
		$( this ).parent().parent().find('.epkb-ts__action-container').slideToggle();
		$( this ).parents( '.epkb-toggle-setting-container' ).toggleClass( 'epkb-toggle-setting-container--active' );
	});



	// Admin Questionnaire item click
	$( 'body' ).on( 'click', '.eckb-Q__list__item-container', function(){

		$( this ).find('.eckb-Q__item__question__toggle-icon').toggleClass( "epkbfa-plus-square epkbfa-minus-square" );

		if( $( this ).hasClass( "eckb-Q__list__item--active" ) ) {

			$( this ).removeClass( "eckb-Q__list__item--active" );

		} else {

			$( this ).addClass( "eckb-Q__list__item--active" );

		}

	});

	/** Save config WPML settings */
	$( 'body' ).on( 'change', '#epkb-setting-box__list-settings__various [name=epkb_wpml_enable]', function(){
		
		// Remove old messages
		$('.eckb-top-notice-message').remove();

		let theResponse, msg, postData = {
			action: 'epkb_wpml_enable',
			_wpnonce_epkb_wpml_enable: $('#_wpnonce_epkb_wpml_enable').val(),
			wpml_enable: $(this).prop('checked') ? 'on' : 'off',
			epkb_kb_id: $('#epkb_wpml_enable_kb_id').val()
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: postData,
			beforeSend: function (xhr)
			{
				epkb_loading_Dialog( 'show' );
			}
		}).done(function (response)
		{
			theResponse = ( response ? response : '' );
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				msg = theResponse.message ? theResponse.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
				return;
			}

		}).fail( function ( response, textStatus, error )
		{
			//noinspection JSUnresolvedVariable
			msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
			//noinspection JSUnresolvedVariable
			msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
		}).always(function ()
		{

			epkb_loading_Dialog( 'remove', '' );

			if ( typeof theResponse.message !== 'undefined' ) {
				msg = theResponse.message;
			}

			if ( msg ) {
				clear_bottom_notifications();
				$('body').append(msg);
			}

			clear_message_after_set_time();
		});
			
	});

	// Confirm button for popup notification
	$( '.epkb-notification-box-popup__button-confirm' ).on( 'click', function () {
		if ( $( this ).attr( 'data-target' ).length > 0 ) {
			$( this ).closest( $( this ).attr( 'data-target' ) ).remove();
		}
	});

	// 'Explore Features' button on 'Need Help?' => 'Get Started' page (possibly other similar links)
	$( '.epkb-admin__step-cta-box__link[data-target]' ).on( 'click', function () {

		// Get target keys
		let target_keys = $( this ).attr( 'data-target' );
		if ( typeof target_keys === 'undefined' || target_keys.length === 0 ) {
			return;
		}
		target_keys = target_keys.split( '__' );

		// Top panel item
		$( '.epkb-admin__top-panel__item[data-target="' + target_keys[0] + '"]' ).trigger( 'click' );

		// Secondary panel item
		if ( target_keys.length > 1 ) {
			setTimeout( function () {
				$( '.epkb-admin__secondary-panel__item[data-target="' + target_keys[1] + '"]' ).trigger( 'click' );
			}, 100 );
		}
	});

	function clear_bottom_notifications() {
		var bottom_message = $('body').find('.eckb-bottom-notice-message');
		if ( bottom_message.length ) {
			bottom_message.addClass( 'fadeOutDown' );
			setTimeout( function() {
				bottom_message.html( '' );
			}, 1000);
		}
	}

	function clear_message_after_set_time(){

		if( $('.eckb-bottom-notice-message' ).length > 0 ) {
			clearTimeout( remove_message_timeout );

			//Add fadeout class to notice after set amount of time has passed.
			remove_message_timeout = setTimeout(function () {
				clear_bottom_notifications();
			} , 10000);
		}
	}
	clear_message_after_set_time();

	// Backend mode scripts
	$(document).on( 'change', '#editor_backend_mode input', function(){

		// Remove old messages
		$('.eckb-top-notice-message').remove();

		// Clear Input Active class
		$( '#editor_backend_mode li' ).removeClass( 'epkb-radio--active' );

		// Set Active class to input
		$(this).parents( 'li' ).addClass( 'epkb-radio--active' );

		// Show Description
		switch( $(this).val() ) {
			case '0':
				$( '#editor_backend_mode_group .radio-buttons-horizontal-desc' ).addClass( 'radio-buttons-horizontal-desc--show' );
				break;
			case '1':
				$( '#editor_backend_mode_group .radio-buttons-horizontal-desc' ).removeClass( 'radio-buttons-horizontal-desc--show' );
				break;
		}

		let theResponse, msg, postData = {
			action: 'epkb_editor_backend_mode',
			_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			editor_backend_mode: $(this).val(),
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: postData,
			beforeSend: function (xhr)
			{
				epkb_loading_Dialog( 'show' );
			}
		}).done(function (response)
		{
			theResponse = ( response ? response : '' );
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				msg = theResponse.message ? theResponse.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
				// set frontend editor if something went wrong
				$('#editor_backend_mode0').prop('checked', true);
				$('#editor_backend_mode1').prop('checked', false);
				return;
			}

		}).fail( function ( response, textStatus, error )
		{
			//noinspection JSUnresolvedVariable
			msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
			//noinspection JSUnresolvedVariable
			msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');

			// set frontend editor if something went wrong
			$('#editor_backend_mode0').prop('checked', true);
			$('#editor_backend_mode1').prop('checked', false);
		}).always(function ()
		{

			epkb_loading_Dialog( 'remove', '' );

			if ( typeof theResponse.message !== 'undefined' ) {
				msg = theResponse.message;
			}

			if ( msg ) {
				clear_bottom_notifications();
				$('body').append(msg);
			}

			clear_message_after_set_time();
		});
	});

	// open iframe with editor
	$(document).on('click', '.epkb-main-page-editor-link a, .epkb-article-page-editor-link a, .epkb-archive-page-editor-link a, .epkb-search-page-editor-link a', function(){
		if ( $('#editor_backend_mode input:checked').length == 0 || $('#editor_backend_mode input:checked').val() == 0 ) {
			return true;
		}

		$('body').append(`
			<div class="epkb-editor-popup" id="epkb-editor-popup">
				<iframe src="${$(this).prop('href')}" ></iframe>
			</div>
		`);

		return false;
	});

	$('body').on('click', '.epkb-editor-popup', function(e){
		e.stopPropagation()
	});

	$('body').on('click', function(){
		$('.epkb-editor-popup').remove();
	});
});