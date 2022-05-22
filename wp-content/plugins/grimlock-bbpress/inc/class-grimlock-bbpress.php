<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_bbPress
 *
 * @author  themoasaurus
 * @since   1.0.0
 * @package grimlock-bbpress/inc
 */
class Grimlock_bbPress {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-bbpress', false, 'grimlock-bbpress/languages' );

		add_action( 'bbp_init', array( $this, 'register_bbp_template_stack' ) );

		add_filter( 'bbp_get_topic_author_avatar', array( $this, 'change_topic_author_avatar_size'  ), 20, 3 );
		add_filter( 'bbp_get_reply_author_avatar', array( $this, 'change_reply_author_avatar_size'  ), 20, 3 );
		add_filter( 'bbp_get_current_user_avatar', array( $this, 'change_current_user_avatar_size'  ), 20, 2 );
		add_action( 'init',                        array( $this, 'add_post_type_support_for_forums' ), 10    );
		add_action( 'widgets_init',                array( $this, 'widgets_init'                     ), 10    );

		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/grimlock-bbpress-template-functions.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/grimlock-bbpress-template-hooks.php';

		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-bbpress-forum-component.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-bbpress-topic-component.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-bbpress-reply-component.php';

		add_action( 'grimlock_query_forum', array( $this, 'query_forum' ), 10, 1 );
		add_action( 'grimlock_query_topic', array( $this, 'query_topic' ), 10, 1 );
		add_action( 'grimlock_query_reply', array( $this, 'query_reply' ), 10, 1 );

		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-customizer.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-single-forum-customizer.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-archive-topic-customizer.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-single-topic-customizer.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-single-reply-customizer.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-single-view-customizer.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-single-user-customizer.php';
		require_once GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-bbpress-search-customizer.php';

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
	}

	/**
	 * Register a new template location in the BBPress template stack
	 */
	public function register_bbp_template_stack() {
		bbp_register_template_stack( array( $this, 'get_bbp_templates_location' ), 9 );
	}

	/**
	 * Get the BBPress template location in the plugin
	 *
	 * @return string
	 */
	public function get_bbp_templates_location() {
		return GRIMLOCK_BBPRESS_PLUGIN_DIR_PATH . 'templates/';
	}

	/**
	 * Increase bbPress avatar sizes for the topic author.
	 *
	 * @param string $author_avatar Avatar of the author of the topic.
	 * @param int    $topic_id      Optional. Topic id.
	 * @param int    $size          Optional. Avatar size. Defaults to 40.
	 *
	 * @return false|string
	 */
	public function change_topic_author_avatar_size( $author_avatar, $topic_id, $size ) {
		$topic_id = bbp_get_topic_id( $topic_id );

		if ( 14 === $size ) {
			$size = 50;
		} elseif ( 80 === $size ) {
			$size = 110;
		}

		if ( ! empty( $topic_id ) ) {
			if ( ! bbp_is_topic_anonymous( $topic_id ) ) {
				$author_avatar = get_avatar( bbp_get_topic_author_id( $topic_id ), $size );
			} else {
				$author_avatar = get_avatar( get_post_meta( $topic_id, '_bbp_anonymous_email', true ), $size );
			}
		}
		return $author_avatar;
	}

	/**
	 * Increase bbPress avatar sizes for the reply author.
	 *
	 * @param string $author_avatar Avatar of the author of the topic.
	 * @param int    $reply_id      Optional. Reply id.
	 * @param int    $size          Optional. Avatar size. Defaults to 40.
	 *
	 * @return false|string
	 */
	public function change_reply_author_avatar_size( $author_avatar, $reply_id, $size ) {
		$reply_id = bbp_get_reply_id( $reply_id );

		if ( 14 === $size ) {
			$size = 50;
		} elseif ( 80 === $size ) {
			$size = 110;
		}

		if ( ! empty( $reply_id ) ) {
			if ( ! bbp_is_reply_anonymous( $reply_id ) ) {
				$author_avatar = get_avatar( bbp_get_reply_author_id( $reply_id ), $size );
			} else {
				$author_avatar = get_avatar( get_post_meta( $reply_id, '_bbp_anonymous_email', true ), $size );
			}
		}
		return $author_avatar;
	}

	/**
	 * Increase bbPress avatars size for the current user.
	 *
	 * @param string $avatar Avatar of the author of the topic.
	 * @param int    $size   Optional. Avatar size. Defaults to 40.
	 *
	 * @return false|string
	 */
	public function change_current_user_avatar_size( $avatar, $size ) {
		if ( 14 === $size ) {
			$size = 50;
		} elseif ( 80 === $size ) {
			$size = 110;
		}

		$avatar = get_avatar( bbp_get_current_user_id(), $size );
		return $avatar;
	}

	/**
	 * Enables excerpt and thumbnail support for the forum post type
	 *
	 * @since 1.0.0
	 */
	public function add_post_type_support_for_forums() {
		add_post_type_support( 'forum', array( 'thumbnail', 'excerpt' ) );
	}

	/**
	 * Register the custom sidebars.
	 */
	public function widgets_init() {
		register_sidebar( apply_filters( 'grimlock_bbpress_sidebar_1_args', array(
			'id'            => 'bbp-sidebar-1',
			'name'          => esc_html__( 'bbPress Sidebar 1', 'grimlock-bbpress' ),
			'description'   => esc_html__( 'The left hand area for bbPress pages.', 'grimlock-bbpress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) ) );

		register_sidebar( apply_filters( 'grimlock_bbpress_sidebar_2_args', array(
			'id'            => 'bbp-sidebar-2',
			'name'          => esc_html__( 'bbPress Sidebar 2', 'grimlock-bbpress' ),
			'description'   => esc_html__( 'The right hand area for bbPress pages.', 'grimlock-bbpress' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		) ) );
	}

	/**
	 * Display the forum component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	public function query_forum( $args = array() ) {
		$component = new Grimlock_bbPress_Forum_Component( apply_filters( 'grimlock_query_forum_args', $args ) );
		$component->render();
	}

	/**
	 * Display the topic component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	public function query_topic( $args = array() ) {
		$component = new Grimlock_bbPress_Topic_Component( apply_filters( 'grimlock_query_topic_args', $args ) );
		$component->render();
	}

	/**
	 * Display the reply component.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	public function query_reply( $args = array() ) {
		$component = new Grimlock_bbPress_Reply_Component( apply_filters( 'grimlock_query_reply_args', $args ) );
		$component->render();
	}

	/**
	 * Enqueue scripts
	 *
	 * @since 1.0.4
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'grimlock-bbpress', GRIMLOCK_BBPRESS_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_BBPRESS_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-bbpress', 'rtl', 'replace' );
	}
}
