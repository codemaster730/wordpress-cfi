'use strict';

/*global
    jQuery, wp, grimlock_customizer_controls
 */

/**
 * customizer-controls.js
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to organize the Theme Customizer controls in tabs.
 */

(function ($) {
    /**
     * Rearrange a customizer section into tabs
     *
     * @param options An array of plain objects representing the tabs with the following structure:
     *  {
     *      label: 'General',
     *      class: 'my-section-general-tab',
     *      controls: [
     *          'my_section_text',
     *          'my_section_layout',
     *          ...
     *      ]
     *  }
     */
    $.fn.customizerTabs = function (options) {
        var $this = $(this);
        $this.addClass('grimlock-customizer-tabs-inside');

        // Create the tabs container
        var $sectionTabs = $('<div class="grimlock-customizer-tabs"></div>');
        $this.prepend($sectionTabs);

        // Add the tabs
        var tabsSelectors = [];
        $.each(options, function (i, tab) {
            var tabSelector = '.' + tab.class;
            $sectionTabs.append('<div><a title="' + tab.label + '"  data-tab="' + tabSelector + '">' + tab.label + '</a></div>');

            // Add the tab class on each control in this tab
            $.each(tab.controls, function (j, controlName) {
                var control = wp.customize.control(controlName);
                if (control) {
                    control.container.addClass(tab.class);
                }
            });

            tabsSelectors.push(tabSelector);
        });

        // Put orphan controls in an 'Other' tab
        var $sectionOtherControls = $this.children('li.customize-control:not(' + tabsSelectors.join() + ')');
        if ($sectionOtherControls.length) {
            $sectionTabs.append('<div><a title="Other" data-tab=".' + $this.attr('id') + '-other-tab">Other</a></div>');
            $sectionOtherControls.addClass($this.attr('id') + '-other-tab');
        }
    };

    wp.customize.state.bind('change', function () {
        setTimeout(function() {
            /**
             * Handle linking to controls, sections or panels in customizer
             *
             * Links must be in the following form to work:
             *
             *  - Control link: <a href="#my-control-id" rel="tc-control"></a>
             *  - Section link: <a href="#my-section-id" rel="tc-section"></a>
             *  - Panel link:   <a href="#my-panel-id" rel="tc-panel"></a>
             */
            $(['control', 'section', 'panel']).each(function(i, type) {
                // Create a click event for each type of link
                $('a[rel="tc-' + type + '"]').click(function(e) {
                    e.preventDefault();

                    // Get the id of the element we want to go to
                    var id = $(this).attr('href').replace('#', '');

                    if(wp.customize[type].has(id)) {
                        // Get the element if it exists in the customizer
                        var element = wp.customize[type].instance(id);

                        // If the element is a control it can be hidden inside a tab
                        // so we need to open the tab in which the control is located
                        if ('control' === type) {
                            var section = wp.customize.section(element.section());
                            var $tabs = section.contentContainer.find('.grimlock-customizer-tabs a');

                            // Find in which tab is the control
                            $tabs.each(function () {
                                var tabClass = $(this).data('tab').replace('.', '');

                                if (element.container.hasClass(tabClass)) {
                                    // Open the tab
                                    $(this).click();
                                }
                            });
                        }

                        // Focus the element to automatically navigate to it
                        element.focus();
                    }
                });
            });
        }, 400);
    });

    var initializedTabs = false;

    /**
     * Create tabs to better sort Customizer controls.
     *
     * @since 1.0.0
     */
    wp.customize.bind('pane-contents-reflowed', function () {

        if (!initializedTabs) {
            if (grimlock_customizer_controls.tabs) {
                $.each(grimlock_customizer_controls.tabs, function (sectionName, tabs) {
                    var section = wp.customize.section(sectionName);
                    if (section) {
                        section.container.filter('ul').customizerTabs(tabs);
                    }
                });
            }

            // Handle tabs
            $('.grimlock-customizer-tabs a').click(function(e) {
                e.preventDefault();
                var tabSelector = $(this).data('tab');
                $(this).parent().addClass('grimlock-customizer-tab-active').siblings('div').removeClass('grimlock-customizer-tab-active');
                $(tabSelector).siblings('li:not(.customize-section-description-container)').hide();
                $(tabSelector).each(function () {
                    var control = wp.customize.control(this.id.replace('customize-control-', ''));

                    // Only show the control if it is currently active to prevent conflict with active callbacks
                    if (control && control.active()) {
                        $(this).show();
                    }
                });
            });

            // Make the first tab of each section active and hide the others
            $('.grimlock-customizer-tabs').each(function () {
                var $firstTab = $(this).children().first();
                $firstTab.addClass('grimlock-customizer-tab-active');

                var tabSelector = $firstTab.find('a').data('tab');
                $(tabSelector).siblings('li:not(.customize-section-description-container)').hide();
                $(tabSelector).each(function () {
                    var control = wp.customize.control(this.id.replace('customize-control-', ''));

                    // Only show the control if it is currently active to prevent conflict with active callbacks
                    if (control && control.active()) {
                        $(this).show();
                    }
                });
            });
            initializedTabs = true;
        }

    });

    /**
     * Disable Posts Page controls to make them appear read-only.
     *
     * @since 1.0.7
     */
    $(document).ready(function () {

        $([
            '#customize-control-custom_header_layout input.image-select',
            '#customize-control-custom_header_container_layout input.image-select',
            '#customize-control-archive_title input',
            '#customize-control-archive_description textarea',
            '#customize-control-archive_custom_header_background_image .image-upload-remove-button',
            '#customize-control-archive_custom_header_background_image .image-default-button',
            '#customize-control-archive_custom_header_background_image .image-upload-button'
        ].join(',')).prop('disabled', true).addClass('disabled');

        $([
            '#customize-control-custom_header_layout',
            '#customize-control-custom_header_container_layout',
            '#customize-control-archive_title',
            '#customize-control-archive_description',
            '#customize-control-archive_custom_header_background_image'
        ].join(',')).addClass('customize-control--disabled');

    });

})(jQuery);
