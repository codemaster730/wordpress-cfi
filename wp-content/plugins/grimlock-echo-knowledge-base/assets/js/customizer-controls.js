'use strict';

/*global
    jQuery
 */

/**
 * customizer-controls.js
 *
 * Theme Customizer enhancements for a better user experience with Grimlock WooCommerce.
 */

(function ($) {

    $(document).ready(function () {

        $([
            '#customize-control-archive_epkb_post_type_title input',
            '#customize-control-archive_epkb_post_type_description textarea',
            '#customize-control-archive_epkb_post_type_custom_header_background_image .image-upload-remove-button',
            '#customize-control-archive_epkb_post_type_custom_header_background_image .image-default-button',
            '#customize-control-archive_epkb_post_type_custom_header_background_image .image-upload-button'
        ].join(',')).prop('disabled', true).addClass('disabled');

        $([
            '#customize-control-archive_epkb_post_type_title',
            '#customize-control-archive_epkb_post_type_description',
            '#customize-control-archive_epkb_post_type_custom_header_background_image'
        ].join(',')).addClass('customize-control--disabled');

    });

})(jQuery);
