<?php
/**
 * Grimlock Jetpack template hooks.
 *
 * @package grimlock-jetpack
 */

/**
 * Testimonial component hooks
 *
 * @see grimlock_jetpack_jetpack_testimonial_template
 *
 * @see grimlock_jetpack_jetpack_testimonial_header
 * @see grimlock_post_content
 * @see grimlock_post_excerpt
 * @see grimlock_jetpack_jetpack_testimonial_footer
 *
 * @see grimlock_post_thumbnail
 * @see grimlock_post_title
 */
add_action( 'grimlock_jetpack_jetpack_testimonial_template',  'grimlock_jetpack_jetpack_testimonial_template',   10, 1 );

add_action( 'grimlock_jetpack_jetpack_testimonial_card_body', 'grimlock_jetpack_jetpack_testimonial_header',     10, 1 );
add_action( 'grimlock_jetpack_jetpack_testimonial_card_body', 'grimlock_jetpack_jetpack_testimonial_excerpt',    30, 1 );
add_action( 'grimlock_jetpack_jetpack_testimonial_card_body', 'grimlock_jetpack_jetpack_testimonial_footer',     40, 1 );

add_action( 'grimlock_jetpack_jetpack_testimonial_footer',    'grimlock_post_thumbnail',                         10, 1 );
add_action( 'grimlock_jetpack_jetpack_testimonial_footer',    'grimlock_post_title',                             10, 1 );
