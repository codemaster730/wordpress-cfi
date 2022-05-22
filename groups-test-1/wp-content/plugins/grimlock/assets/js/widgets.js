'use strict';

/*global
    jQuery, wp, _, grimlock_widgets, ajaxurl
 */

/**
 * widgets.js
 *
 * Widgets enhancements for a better user experience.
 */

(function($){
    if ( ! window.grimlock ) {
        window.grimlock = {};
    }

    if ( ! window.grimlock.widgets ) {
        window.grimlock.widgets = {};
    }

    /**
     * Callback function for the 'click' event on the upload button.
     *
     * Displays the media uploader for selecting an image.
     *
     * @since 1.0.0
     */
    window.grimlock.widgets.uploadMedia = function (e) {
        var file_frame, json;

        /**
         * If an instance of file_frame already exists, then we can open it
         * rather than creating a new instance.
         */
        if (undefined !== file_frame) {
            file_frame.open();
            return;
        }

        /**
         * Use the wp.media library to define the settings of the Media
         * Uploader.
         */
        file_frame = wp.media.frames.file_frame = wp.media({
            title:  grimlock_widgets.frame_title,
            button: {
                text: grimlock_widgets.button_text
            },
            multiple: false
        });

        /**
         * Setup an event handler for what to do when an image has been
         * selected.
         */
        file_frame.on('select', function() {
            json = file_frame.state().get('selection').first().toJSON();

            if (0 < $.trim(json.url).length) {
                var $wrapper = $(e.target).parents('.grimlock_section_widget-image');

                $wrapper.find('.attachment-media-view').addClass([
                    'attachment-media-view-image',
                    'landscape'
                ].join(' '));

                $wrapper.find('.placeholder').addClass('hidden');

                $wrapper.find('.thumbnail')
                .children('img')
                .attr('src', json.url)
                .parent()
                .removeClass('hidden');

                $wrapper.find('.upload-actions').addClass('hidden');
                $wrapper.find('.remove-actions').removeClass('hidden');

                $wrapper.find('.attachment-media-src').val(json.id).change();
            }
        });

        file_frame.open();
    };

    /**
     * Callback function for the 'click' event on the remove button.
     *
     * Removes the media url from widget form and resets attachment view.
     *
     * @since 1.0.0
     */
    window.grimlock.widgets.removeMedia = function(e) {
        var $wrapper = $(e.target).parents('.grimlock_section_widget-image');

        $wrapper.find('.attachment-media-view').removeClass([
            'attachment-media-view-image',
            'landscape'
        ].join(' '));

        $wrapper.find('.placeholder').removeClass('hidden');
        $wrapper.find('.thumbnail').addClass('hidden');

        $wrapper.find('.upload-actions').removeClass('hidden');
        $wrapper.find('.remove-actions').addClass('hidden');

        $wrapper.find('.attachment-media-src').val('').change();
    };

    /**
     * Callback for the 'widget-added', 'widget-updated' and 'ajax-complete' events.
     *
     * Activate widget form inputs.
     *
     * @since 1.0.0
     */
    window.grimlock.widgets.init = function($parent) {
        var handleChange = function(e, ui) {
            // Check whether the input value has changed.
            if ('text' === e.target.type && !_.isUndefined(ui)) {
                var tmp   = $(e.target).data('tmp');
                var color = {
                    color: ui.color._color,
                    alpha: ui.color._alpha
                };

                // Check whether the input value has changed since last event.
                if (!_.isEqual(tmp, color)) {
                    $(e.target).data('tmp', color);
                    if (!_.isUndefined(tmp)) {
                        // @Hack: Trigger change on the "Clear" button.
                        $(e.target).parents('.wp-picker-container').find('input.wp-picker-clear').change();
                    }
                }
            } else if ('button' === e.target.type) {
                $(e.target).change();
            }
        };

        // Activate the color picker.
        $parent.find('.grimlock_section_widget-color-picker').wpColorPicker({
            defaultColor: false,
            change:       handleChange,
            clear:        handleChange,
            hide:         true,
            palettes:     true
        });

        // Activate the radio image buttons.
        $parent.find('.grimlock_section_widget-radio-image__buttonset').buttonset();

        var updateInputRangeValueIndicator = function() {
            var $valueIndicator = $(this).siblings('span.grimlock_input-range-value-indicator');
            var unit = $(this).data('unit');

            if (!$valueIndicator.length) {
                $valueIndicator = $('<span class="grimlock_input-range-value-indicator"></span>');
                $(this).after($valueIndicator);
            }

            $valueIndicator.html($(this).val() + ' ' + unit);
        }

        // Init value indicator for range inputs
        $parent.find('.grimlock-widget input[type="range"]').each(updateInputRangeValueIndicator);
        $parent.find('.grimlock-widget input[type="range"]').on('input change', updateInputRangeValueIndicator);

        /**
         * Process conditional logic to determine whether a field should be displayed or not
         *
         * @param conditionalLogic Array of conditional logic
         * @param $widgetFieldsContainer Widget fields container
         * @param relation Relation to apply between conditions. Automatically alternates between AND and OR for each nested array.
         * @return {*}
         */
        var processConditions = function( conditionalLogic, $widgetFieldsContainer, relation ) {
            if ( ! relation )
                relation = 'AND';

            var finalResult;

            conditionalLogic.forEach( function( conditions ) {
                var result;

                if ( conditions.length ) {
                    // If we have a nested array, use recursion to get the result of the nested conditions
                    result = processConditions( conditions, $widgetFieldsContainer, relation === 'AND' ? 'OR' : 'AND' );
                }
                else if ( conditions['field'] && conditions['operator'] && conditions['value'] !== undefined ) {
                    // Get the input value
                    var $input = $widgetFieldsContainer.find( '[name$="[' + conditions['field'] + ']"]' );

                    if ( $input.length ) {

                        if ( $input.is( '[type="radio"]' ) )
                            $input = $widgetFieldsContainer.find( '[name$="[' + conditions[ 'field' ] + ']"]:checked' );

                        var value = $input.val();

                        // For a checkbox, the value we want is its :checked state
                        if ( $input.is( '[type="checkbox"]' ) )
                            value = $input.is( ':checked' );

                        // If we have a condition, get its result
                        switch ( conditions[ 'operator' ] ) {
                            case '===':
                                result = value === conditions[ 'value' ];
                                break;
                            case '==':
                                result = value == conditions[ 'value' ];
                                break;
                            case '!==':
                                result = value !== conditions[ 'value' ];
                                break;
                            case '!=':
                                result = value != conditions[ 'value' ];
                                break;
                            case '>':
                                result = value > conditions[ 'value' ];
                                break;
                            case '>=':
                                result = value >= conditions[ 'value' ];
                                break;
                            case '<':
                                result = value < conditions[ 'value' ];
                                break;
                            case '<=':
                                result = value <= conditions[ 'value' ];
                                break;
                        }

                    }
                    else {
                        // If input doesn't exist default to true
                        result = true;
                    }
                }

                if ( undefined !== result ) {
                    if ( undefined === finalResult )
                        finalResult = result;
                    else
                        finalResult = relation === 'AND' ? finalResult && result : finalResult || result;
                }
            } );

            return finalResult;
        }

        var handleFieldsVisibility = function() {
            var $widget = $( this );

            $( this ).find( '[data-grimlock-widget-conditional-logic]' ).each( function() {
                var displayed = true;
                var conditionalLogic = $( this ).data( 'grimlock-widget-conditional-logic' );

                if ( conditionalLogic && conditionalLogic.length )
                    displayed = processConditions( conditionalLogic, $widget );

                if ( ! displayed )
                    $( this ).hide();
                else
                    $( this ).show();
            } );
        };

        $parent.find( '.grimlock-widget' ).each( handleFieldsVisibility );
        $parent.find( '.grimlock-widget' ).on( 'change', handleFieldsVisibility );

        // Handle widget tab changes.
        $('.grimlock-widget-tabs a').click(function(e) {
            e.preventDefault();
            var tabId = $(this).attr('href');
            $(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
            $(this).closest('.grimlock-widget-tabs').siblings('.tabs-panel').hide();
            $(tabId).show();
        });

        // Reload parent terms list on each change of taxonomy list.
        var $taxonomyFields = $parent.find( '.grimlock-widget [name$="[taxonomy]"]' );

        $taxonomyFields.on( 'change', function() {
            var $parentTermField = $( this ).closest( '.grimlock-widget' ).find( '[name$="[parent]"]' );

            var data = {
                'action':     'grimlock_ajax_terms',
                'taxonomy':   $( this ).val(),
                'ajax_nonce': grimlock_widgets.ajax_nonce
            };

            $parentTermField.html( '<option>Loading...</option>' );
            $parentTermField.attr( 'disabled', 'disabled' );
            jQuery.post( ajaxurl, data, function( response ) {
                $parentTermField.attr( 'disabled', false );
                $parentTermField.html( response );
            } );
        });

        var handlePopulatePostsSelect = function() {
            var $postSelectField = $( this );
            $postSelectField.select2( { width: '100%', placeholder: 'Loading...' } );

            var postType = $postSelectField.data( 'grimlock-widget-post-type' );

            // If postType is a string contained in brackets, we replace it by the value of the input with the name in brackets
            if ( typeof postType === 'string' && postType.match( /^{.+}$/g ) ) {
                // Remove the brackets
                var inputName = postType.slice( 1, postType.length - 1 );

                // Get the input value
                var $postTypeInput = $postSelectField.closest( '.grimlock-widget' ).find( '[name$="[' + inputName + ']"]' );
                postType = $postTypeInput.val();

                // Bind event to reload the select2 when post type changes
                $postTypeInput.one( 'change', function() {
                    $postSelectField.val( null ).trigger( 'change' );
                    handlePopulatePostsSelect.call( $postSelectField );
                } );
            }

            if ( ! postType )
                return;

            $.get( grimlock_widgets.rest_url + 'types/' + postType, {}, function( typeResponse ) {
                if ( typeResponse && typeResponse.rest_base ) {
                    $postSelectField.select2( {
                        width: '100%',
                        closeOnSelect: false,
                        placeholder: 'Select...',
                        ajax: {
                            url: grimlock_widgets.rest_url + typeResponse.rest_base,
                            dataType: 'json',
                            delay: 250,
                            data: function ( params ) {
                                return params.term ? { search: params.term } : '';
                            },
                            processResults: function ( postsResponse ) {
                                if ( ! postsResponse && ! postsResponse.length )
                                    return { results: [] };

                                var results = $.map( postsResponse, function( post ) {
                                    return { id: post.id, text: post.title.rendered };
                                } );

                                return { results: results };
                            },
                            cache: true,
                        },
                    } );
                }
            } );
        };

        var $postSelectFields = $parent.find( '.grimlock-widget .grimlock-widget-post-select' );
        $postSelectFields.each( handlePopulatePostsSelect );

        var handlePopulateTermsSelect = function() {
            var $termSelectField = $( this );
            $termSelectField.select2( { width: '100%', placeholder: 'Loading...' } );

            var taxonomy = $termSelectField.data( 'grimlock-widget-taxonomy' );

            // If taxonomy is a string contained in brackets, we replace it by the value of the input with the name in brackets
            if ( typeof taxonomy === 'string' && taxonomy.match( /^{.+}$/g ) ) {
                // Remove the brackets
                var inputName = taxonomy.slice( 1, taxonomy.length - 1 );

                // Get the input value
                var $taxonomyInput = $termSelectField.closest( '.grimlock-widget' ).find( '[name$="[' + inputName + ']"]' );
                taxonomy = $taxonomyInput.val();

                // Bind event to reload the select2 when taxonomy changes
                $taxonomyInput.one( 'change', function() {
                    $termSelectField.val( null ).trigger( 'change' );
                    handlePopulateTermsSelect.call( $termSelectField );
                } );
            }

            if ( ! taxonomy )
                return;

            $.get( grimlock_widgets.rest_url + 'taxonomies/' + taxonomy, {}, function( taxonomyResponse ) {
                if ( taxonomyResponse && taxonomyResponse.rest_base ) {
                    $termSelectField.select2( {
                        width: '100%',
                        closeOnSelect: false,
                        placeholder: 'Select...',
                        ajax: {
                            url: grimlock_widgets.rest_url + taxonomyResponse.rest_base,
                            dataType: 'json',
                            delay: 250,
                            data: function ( params ) {
                                return params.term ? { search: params.term } : '';
                            },
                            processResults: function ( termsResponse ) {
                                if ( ! termsResponse && ! termsResponse.length )
                                    return { results: [] };

                                var results = $.map( termsResponse, function( term ) {
                                    return { id: term.id, text: term.name };
                                } );

                                return { results: results };
                            },
                            cache: true,
                        },
                    } );
                }
            } );
        };

        var $termSelectFields = $parent.find( '.grimlock-widget .grimlock-widget-term-select' );
        $termSelectFields.each( handlePopulateTermsSelect );
    };

    $(document).ready(function() {

        $(document).on('click', '.grimlock_section_widget-image .upload-button', function(e) {
            e.preventDefault();
            window.grimlock.widgets.uploadMedia(e);
        });

        $(document).on('click', '.grimlock_section_widget-image .remove-button', function(e) {
            e.preventDefault();
            window.grimlock.widgets.removeMedia(e);
        });

        // Initialize all widgets already in page.
        window.grimlock.widgets.init($('#wp_inactive_widgets, #widgets-right'));

        // Initialize added widgets.
        $(document).on('widget-added', function(e, widget) {
            window.grimlock.widgets.init(widget);
        });

        // Reinitialize updated widgets.
        $(document).on('widget-updated', function(e, widget) {
            window.grimlock.widgets.init(widget);
        });
    });
})(jQuery);
