/**
 * Maintenance
 * https://wpmaintenancemode.com/
 * (c) WebFactory Ltd, 2022
 */

jQuery(document).ready(function($){
    if (typeof mtnc_pointers  == 'undefined') {
      return;
    }

    $.each(mtnc_pointers, function(index, pointer) {
      if (index.charAt(0) == '_') {
        return true;
      }
      $(pointer.target).pointer({
          content: '<h3>Maintenance</h3><p>' + pointer.content + '</p>',
          pointerWidth: 380,
          position: {
              edge: pointer.edge,
              align: pointer.align
          },
          close: function() {
                  $.get(ajaxurl, {
                      notice_name: index,
                      _ajax_nonce: mtnc_pointers._nonce_dismiss_pointer,
                      action: 'mtnc_dismiss_notice'
                  });
          }
        }).pointer('open');
    });
  });
