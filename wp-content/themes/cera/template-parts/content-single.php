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
	 * Functions hooked into cera_single action
	 *
	 * @hooked cera_single          - 10
	 * @hooked cera_grimlock_single - 10
	 */
	do_action( 'cera_single' ); ?>
</article><!-- #post-## -->
