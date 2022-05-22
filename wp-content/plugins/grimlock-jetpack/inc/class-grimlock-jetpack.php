<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Grimlock_Jetpack
 *
 * @author  themoasaurus
 * @since   1.0.0
 * @package grimlock-jetpack/inc
 */
class Grimlock_Jetpack {

	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		load_plugin_textdomain( 'grimlock-jetpack', false, 'grimlock-jetpack/languages' );

		require_once GRIMLOCK_JETPACK_PLUGIN_DIR_PATH . 'inc/grimlock-jetpack-template-functions.php';
		require_once GRIMLOCK_JETPACK_PLUGIN_DIR_PATH . 'inc/grimlock-jetpack-template-hooks.php';

		require_once GRIMLOCK_JETPACK_PLUGIN_DIR_PATH . 'inc/customizer/class-grimlock-jetpack-testimonial-customizer.php';

		add_action( 'after_setup_theme',                          array( $this, 'setup'                                ), 10    );
		add_action( 'init',                                       array( $this, 'force_register_tiled_gallery_block'   ), 10    );
		add_filter( 'infinite_scroll_archive_supported',          array( $this, 'infinite_scroll_support'              ), 10    );
		add_filter( 'comments_open',                              array( $this, 'remove_attachment_comments'           ), 10, 2 );

		add_filter( 'jetpack_relatedposts_filter_post_context',   array( $this, 'change_relatedposts_post_content'     ), 10, 2 );
		add_filter( 'jetpack_relatedposts_filter_headline',       array( $this, 'change_relatedposts_headline'         ), 10, 2 );
		add_filter( 'jetpack_relatedposts_filter_thumbnail_size', array( $this, 'change_relatedposts_thumbnail_size'   ), 10, 2 );
		add_action( 'wp',                                         array( $this, 'remove_relatedposts_the_content'      ), 20, 2 );
		add_action( 'grimlock_single_template',                   array( $this, 'add_relatedposts_after_entry_content' ), 35, 1 );

		add_action( 'wp_enqueue_scripts',                         array( $this, 'wp_enqueue_scripts'                   ), 10    );
		add_action( 'enqueue_block_editor_assets',                array( $this, 'enqueue_block_editor_styles'          ), 10    );
		add_action( 'wp_footer',                                  array( $this, 'change_infinite_scroll_button_text'   ), 20    );

		// Initialize components.
		require_once GRIMLOCK_JETPACK_PLUGIN_DIR_PATH . 'inc/component/class-grimlock-jetpack-jetpack-testimonial-component.php';
		add_action( 'grimlock_query_jetpack-testimonial', array( $this, 'query_jetpack_testimonial' ), 10, 1 );
	}

	/**
	 * Jetpack setup function.
	 *
	 * @since 1.0.0
	 *
	 * @link https://jetpack.me/support/infinite-scroll/
	 * @link https://jetpack.me/support/responsive-videos/
	 */
	public function setup() {
		// Add theme support for Infinite Scroll.
		add_theme_support( 'infinite-scroll', array(
			'container' => 'main',
			'render'    => array( $this, 'infinite_scroll_render' ),
			'footer'    => 'page',
		) );

		// Add theme support for Responsive Videos.
		add_theme_support( 'jetpack-responsive-videos' );
	}

	/**
	 * Force register tiled gallery block even if JetPack is not connected
	 */
	public function force_register_tiled_gallery_block() {
		if ( class_exists( '\Automattic\Jetpack\Blocks' ) && class_exists( '\Automattic\Jetpack\Extensions\Tiled_Gallery' ) ) {
			\Automattic\Jetpack\Blocks::jetpack_register_block(
				\Automattic\Jetpack\Extensions\Tiled_Gallery::BLOCK_NAME,
				array(
					'render_callback' => array( \Automattic\Jetpack\Extensions\Tiled_Gallery::class, 'render' ),
				)
			);
		}
	}

	/**
	 * Init Jetpack infinite scroll only in some cases.
	 *
	 * @since 2.0.0
	 *
	 * @link https://jetpack.me/support/infinite-scroll/
	 */
	public function infinite_scroll_support() {
		return current_theme_supports( 'infinite-scroll' ) && ( is_home() || is_category() || is_tag() || is_author() || is_search() );
	}


	/**
	 * Custom render function for Infinite Scroll.
	 *
	 * @since 1.0.0
	 */
	public function infinite_scroll_render() {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', is_search() ? 'search' : get_post_format() );
		}
	}

	/**
	 * Remove Comment box on Jetpack Carousel.
	 *
	 * @since 2.0.0
	 */
	function remove_attachment_comments( $open, $post_id ) {
		$post = get_post( $post_id );
		if ( 'attachment' == $post->post_type ) {
			return false;
		}
		return $open;
	}

	/**
	 * Enqueue scripts and stylesheets.
	 *
	 * @since 1.0.0
	 */
	public function wp_enqueue_scripts() {
		if ( apply_filters( 'grimlock_jetpack_js_enqueued', is_home() || is_archive() || is_search() ) ) {
			wp_enqueue_script( 'grimlock-jetpack-infinite-scroll', GRIMLOCK_JETPACK_PLUGIN_DIR_URL . 'assets/js/infinite-scroll.js', array( 'jquery', 'jquery-masonry', 'imagesloaded' ), GRIMLOCK_JETPACK_VERSION, true );
		}
		wp_enqueue_style( 'grimlock-jetpack', GRIMLOCK_JETPACK_PLUGIN_DIR_URL . 'assets/css/style.css', array(), GRIMLOCK_JETPACK_VERSION );

		/*
		 * Load style-rtl.css instead of style.css for RTL compatibility
		 */
		wp_style_add_data( 'grimlock-jetpack', 'rtl', 'replace' );
	}

	/**
	 * Registers an editor stylesheet for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_block_editor_styles() {
		wp_enqueue_style( 'grimlock-jetpack-block-editor-styles', GRIMLOCK_JETPACK_PLUGIN_DIR_URL . 'assets/css/style-editor.css', array(), GRIMLOCK_JETPACK_VERSION, 'all' );
	}

	/**
	 * Change Jetpack Infinite Scroll module 'Older Posts' button text.
	 *
	 * @since 1.0.2
	 */
	public function change_infinite_scroll_button_text() {
		if ( is_home() || is_archive() || is_search() ) : ?>
			<script type="text/javascript">
                if ( typeof infiniteScroll !== 'undefined' ) {
                    //<![CDATA[
                    infiniteScroll.settings.text = '<?php echo esc_html__( 'Load more', 'grimlock-jetpack' ); ?>';
                    //]]>
                }
			</script>
		<?php
		endif;
	}

	/**
	 * Display the post content after the existing Related Posts context.
	 *
	 * @since 1.0.6
	 *
	 * @param $context
	 * @param int $post_id The post ID.
	 *
	 * @return string      The updated post content.
	 */
	public function change_relatedposts_post_content( $context, $post_id ) {
		$post_date     = get_the_date();
		$post_category = get_the_category_list('<span class="separator mr-1">,</span>');

		return sprintf(
			'<div class="entry-meta text-muted d-flex justify-content-center px-0"><span class="post-date">%2$s</span><span class="separator pl-2 pr-2">•</span>%1$s</div>',
			$post_category,
			esc_html( $post_date )
		);
	}

	/**
	 * Custom Related posts heading markups.
	 *
	 * @since 1.0.6
	 *
	 * @param  string $headline The markups for the post headline.
	 *
	 * @return string           The updated markups for the post headline.
	 */
	public function change_relatedposts_headline( $headline ) {
		return $headline;
	}

	/**
	 * Custom Related posts image size.
	 *
	 * @since 1.0.6
	 *
	 * @param  array $size The array of arguments for the thumbnail.
	 *
	 * @return array       The updated array of arguments for the thumbnail.
	 */
	public function change_relatedposts_thumbnail_size( $size ) {
		$size = array(
			'width'  => 800,
			'height' => 600
		);
		return $size;
	}

	/**
	 * Remove Related Posts from the_content.
	 *
	 * @since 1.0.6
	 */
	public function remove_relatedposts_the_content() {
		if ( class_exists( 'Jetpack_RelatedPosts' ) ) {
			$jetpack_related_posts = Jetpack_RelatedPosts::init();
			remove_filter( 'the_content', array( $jetpack_related_posts, 'filter_add_target_to_dom' ), 40 );
		}
	}

	/**
	 * Add JetPack related posts after the "entry-content" in Grimlock single
	 *
	 * @param array $args Grimlock single component args
	 */
	public function add_relatedposts_after_entry_content( $args ) {
		if ( class_exists( 'Jetpack_RelatedPosts' ) &&
		     ! has_shortcode( get_the_content(), Jetpack_RelatedPosts::SHORTCODE ) &&
		     ! has_block( 'jetpack/related-posts' ) &&
		     class_exists( 'Automattic\Jetpack\Blocks' ) &&
		     ! Automattic\Jetpack\Blocks::is_fse_theme() ) {

			echo do_shortcode( '[jetpack-related-posts]' );

		}
	}

	/**
	 * Display the testimonial component.
	 *
	 * @since 1.0.9
	 *
	 * @param array $args
	 */
	public function query_jetpack_testimonial( $args = array() ) {
		$component = new Grimlock_Jetpack_Jetpack_Testimonial_Component( apply_filters( 'grimlock_query_jetpack_testimonial_args', $args ) );
		$component->render();
	}
}
