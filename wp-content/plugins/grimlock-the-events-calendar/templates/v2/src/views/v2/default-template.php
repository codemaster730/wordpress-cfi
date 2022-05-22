<?php
/**
 * View: Default Template for Events
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/v2/default-template.php
 *
 * See more documentation about our views templating system.
 *
 * @link http://m.tri.be/1aiy
 *
 * @version 5.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

use Tribe\Events\Views\V2\Template_Bootstrap;

get_header();
get_sidebar( 'left' ); ?>

	<div id="primary" class="content-area region__col region__col--2">
		<main id="main" class="site-main">

			<?php echo tribe( Template_Bootstrap::class )->get_view_html(); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar( 'right' );
get_footer();
