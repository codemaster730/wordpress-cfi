jQuery(document).ready(function($) {


	/*********************************************************************************************
	 *********************************************************************************************
	 *
	 *                TOP PANEL
	 *
	 * ********************************************************************************************
	 ********************************************************************************************/

	{
		// 1. KBs DROPDOWN - reload on change
		$( '#epkb-list-of-kbs' ).on( 'change', function(e) {
			// var what = e.target.value;
			var kb_admin_url = $(this).find(":selected").data('kb-admin-url');
			if ( kb_admin_url ) {
				window.location.href = kb_admin_url;
			}
		});

		// 2. User switches between top panel buttons
		$( '#epkb-config-main-info').find('.page-icon' ).on( 'click', function() {

			if ($(this).hasClass('epkb-article-structure-dialog')){
				return;
			}

			// remove old info/error notices
			$('.eckb-bottom-notice-message').html('');

			// remove info boxes that are still open
			$('.option-info-icon').removeClass('active-info');
			$('.option-info-content').addClass('hidden');

			// select Button clicked on
			$( '.epkb-info-section').removeClass( 'epkb-active-page' );
			$( this ).parent().parent().toggleClass( 'epkb-active-page' );

			//Hide all content and then toggle current content
			$( '.epkb-config-content-wrapper' ).hide();

			// Toggle Page Content
			var id = $( this ).attr( 'id' );
		    $( '#' + id + '-content' ).fadeToggle();

		});

	}


});

