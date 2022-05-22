<?php
/**
 * Grimlock The Events Calendar template hooks.
 *
 * @package grimlock-the-events-calendar
 */

/**
 * Tribe events component hooks
 *
 * @see grimlock_the_events_calendar_tribe_events_template
 *
 * @see grimlock_post_thumbnail
 *
 * @see grimlock_the_events_calendar_tribe_events_header
 * @see grimlock_the_events_calendar_tribe_events_content
 * @see grimlock_the_events_calendar_tribe_events_excerpt
 * @see grimlock_the_events_calendar_tribe_events_footer
 *
 * @see grimlock_post_title
 * @see grimlock_the_events_calendar_tribe_events_start_date
 *
 * @see grimlock_edit_post_link
 */
add_action( 'grimlock_the_events_calendar_tribe_events_template',         'grimlock_the_events_calendar_tribe_events_template',   10, 1 );

add_action( 'grimlock_the_events_calendar_tribe_events_before_card_body', 'grimlock_post_thumbnail',                              10, 1 );

add_action( 'grimlock_the_events_calendar_tribe_events_card_body',        'grimlock_the_events_calendar_tribe_events_header',     10, 1 );
add_action( 'grimlock_the_events_calendar_tribe_events_card_body',        'grimlock_post_content',                                20, 1 );
add_action( 'grimlock_the_events_calendar_tribe_events_card_body',        'grimlock_post_excerpt',                                30, 1 );
add_action( 'grimlock_the_events_calendar_tribe_events_card_body',        'grimlock_the_events_calendar_tribe_events_footer',     40, 1 );

add_action( 'grimlock_the_events_calendar_tribe_events_header',           'grimlock_the_events_calendar_tribe_events_category_list', 5,  1 );
add_action( 'grimlock_the_events_calendar_tribe_events_header',           'grimlock_post_title',                                     10, 1 );
add_action( 'grimlock_the_events_calendar_tribe_events_header',           'grimlock_the_events_calendar_tribe_events_start_date',    20, 1 );

add_action( 'grimlock_the_events_calendar_tribe_events_footer',           'grimlock_the_events_calendar_tribe_events_venue',         10,  1 );
add_action( 'grimlock_the_events_calendar_tribe_events_footer',           'grimlock_the_events_calendar_tribe_events_cost',          20,  1 );

/**
 * Custom Header component hooks
 *
 * @see grimlock_the_events_calendar_custom_header_single_post_back
 * @see grimlock_the_events_calendar_single_tribe_events_custom_header_category_list
 *
 * @see grimlock_the_events_calendar_single_tribe_events_custom_header_date
 * @see grimlock_the_events_calendar_single_tribe_events_custom_header_venue
 * @see grimlock_the_events_calendar_single_tribe_events_custom_header_organizer
 * @see grimlock_the_events_calendar_single_tribe_events_custom_header_cost
 */
add_action( 'grimlock_custom_header_before_title',   'grimlock_the_events_calendar_custom_header_single_post_back',                  10, 1 );

add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_events_custom_header_category_list', 5, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_events_custom_header_date',          20, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_events_custom_header_venue',         25, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_events_custom_header_organizer',     30, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_events_custom_header_cost',          35, 1 );

add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_venue_custom_header_address',        15, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_venue_custom_header_phone',          20, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_venue_custom_header_website',        25, 1 );

add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_organizer_custom_header_phone',      15, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_organizer_custom_header_website',    20, 1 );
add_action( 'grimlock_custom_header_after_subtitle', 'grimlock_the_events_calendar_single_tribe_organizer_custom_header_email',      25, 1 );
