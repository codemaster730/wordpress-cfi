<?php
/**
 * Grimlock for bbPress template hooks.
 *
 * @package grimlock-bbpress
 */

/**
 * Forum component hooks.
 *
 * @since 1.0.1
 */
add_action( 'grimlock_bbpress_forum_title',          'grimlock_bbpress_forum_title',        10    );
add_action( 'grimlock_bbpress_forum_topic_count',    'grimlock_bbpress_forum_topic_count',  10    );
add_action( 'grimlock_bbpress_forum_reply_count',    'grimlock_bbpress_forum_reply_count',  10    );
add_action( 'grimlock_bbpress_forum_freshness',      'grimlock_bbpress_forum_freshness',    10    );
add_action( 'grimlock_bbpress_forum_content',        'grimlock_bbpress_forum_content',      10, 1 );
add_filter( 'grimlock_bbpress_forum_more_link_text', 'grimlock_bbpress_get_more_link_text', 10, 1 );

/**
 * Topic component hooks.
 *
 * @since 1.0.1
 */
add_action( 'grimlock_bbpress_topic_title',          'grimlock_bbpress_topic_title',        10    );
add_action( 'grimlock_bbpress_topic_started_by',     'grimlock_bbpress_topic_started_by',   10    );
add_action( 'grimlock_bbpress_topic_voice_count',    'grimlock_bbpress_topic_voice_count',  10    );
add_action( 'grimlock_bbpress_topic_reply_count',    'grimlock_bbpress_topic_reply_count',  10    );
add_action( 'grimlock_bbpress_topic_freshness',      'grimlock_bbpress_topic_freshness',    10    );
add_action( 'grimlock_bbpress_topic_more_link',      'grimlock_bbpress_topic_more_link',    10, 1 );
add_filter( 'grimlock_bbpress_topic_more_link_text', 'grimlock_bbpress_get_more_link_text', 10, 1 );

/**
 * Reply component hooks.
 *
 * @since 1.0.1
 */
add_action( 'grimlock_bbpress_reply_title',          'grimlock_bbpress_reply_title',        10    );
add_action( 'grimlock_bbpress_reply_date',           'grimlock_bbpress_reply_date',         10    );
add_action( 'grimlock_bbpress_reply_permalink',      'grimlock_bbpress_reply_permalink',    10    );
add_action( 'grimlock_bbpress_reply_author',         'grimlock_bbpress_reply_author',       10    );
add_action( 'grimlock_bbpress_reply_content',        'grimlock_bbpress_reply_content',      10, 1 );
add_filter( 'grimlock_bbpress_reply_more_link_text', 'grimlock_bbpress_get_more_link_text', 10, 1 );
