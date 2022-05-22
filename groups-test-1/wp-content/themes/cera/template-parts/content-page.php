<?php
/**
 * Template part for displaying page content in page.php.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package cera
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php
	/**
	 * Functions hooked into cera_page action
	 *
	 * @hooked cera_page          - 10
	 * @hooked cera_grimlock_page - 10
	 */
	do_action( 'cera_page' ); ?>
</article><!-- #post-## -->
