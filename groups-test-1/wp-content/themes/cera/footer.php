<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package cera
 */

?>

			<?php
			/**
			 * Functions hooked into cera_footer action
			 *
			 * @hooked cera_footer                 - 10
			 * @hooked cera_grimlock_after_content - 10
			 * @hooked cera_grimlock_footer        - 20
			 */
			do_action( 'cera_footer' ); ?>

		</div><!-- #site -->

		<?php
		do_action( 'cera_after_site' ); ?>

	</div><!-- #site-wrapper -->

<?php wp_footer(); ?>

</body>
</html>
