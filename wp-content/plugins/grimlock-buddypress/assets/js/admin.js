'use strict';

/*global
 jQuery
 */

/*eslint
 yoda: [2, "always"]
 */

/**
 * admin.js
 *
 * BuddyPress admin enhancements.
 */

jQuery( function($) {
    $( 'select#fieldtype' ).change( function() {
        var value = $( this ).val();

        if ( value === 'textarea' ) {
            $( '#sync_with_bio_postbox' ).show();
        }
        else {
            $( '#sync_with_bio_postbox' ).hide();
        }
    } );
} );
