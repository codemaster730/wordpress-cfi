<?php
/**
 * Template part for displaying a message that posts cannot be found.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package cera
 */

?>

<section class="no-results not-found card card-static p-5 tex-center mt-5 mb-5 justify-content-center align-items-center">

	<header class="grimlock--page-header entry-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'cera' ); ?></h1>
	</header><!-- .grimlock--page-header -->

	<div class="page-content clearfix">
		<?php
		if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<p><?php
				$allowed_html = array(
					'a' => array(
						'href' => array(),
					),
				);
				/* translators: %s: Edit post admin URL */
				printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'cera' ), $allowed_html ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>
		<?php elseif ( is_search() ) : ?>
			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'cera' ); ?></p>
			<?php
			get_search_form();
		else : ?>
			<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'cera' ); ?></p>
			<?php
			get_search_form();
		endif; ?>
	</div><!-- .page-content -->
</section><!-- .no-results -->
