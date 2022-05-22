<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package cera
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Functions hooked into cera_search_post action
	 *
	 * @hooked cera_search_post          - 10
	 * @hooked cera_grimlock_search_post - 10
	 */
	do_action( 'cera_search_post' ); ?>
</article><!-- #post-## -->
