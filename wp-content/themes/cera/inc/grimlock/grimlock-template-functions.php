<?php
/**
 * Cera template functions for Grimlock.
 *
 * @package cera
 */

if ( ! function_exists( 'cera_grimlock_before_site' ) ) :
	/**
	 * Prints grimlock hooks before page.
	 */
	function cera_grimlock_before_site() {
		do_action( 'grimlock_loader' );
		do_action( 'grimlock_vertical_navigation' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_header' ) ) :
	/**
	 * Prints header in page.
	 */
	function cera_grimlock_header() {
		do_action( 'grimlock_preheader', array(
			'callback' => 'cera_grimlock_preheader_callback',
		) );

		do_action( 'grimlock_header', array(
			'callback' => 'cera_grimlock_header_callback',
		) );
	}
endif;

if ( ! function_exists( 'cera_grimlock_preheader_callback' ) ) :
	/**
	 * Prints the preheader widget areas.
	 */
	function cera_grimlock_preheader_callback() {
		cera_grimlock_widget_areas( 'preheader', 4 );
	}
endif;

if ( ! function_exists( 'cera_grimlock_header_callback' ) ) :
	/**
	 * Prints the preheader widget areas.
	 */
	function cera_grimlock_header_callback() {
		if ( apply_filters( 'grimlock_navigation_has_position_top', true ) ) :
			do_action( 'grimlock_navigation' );
		endif;

		do_action( 'grimlock_custom_header' );

		if ( apply_filters( 'grimlock_navigation_has_position_bottom', true ) ) :
			do_action( 'grimlock_navigation' );
		endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_navbar_nav_menu' ) ) :
	/**
	 * Prints the wp nav menu inside Grimlock Navbar.
	 *
	 * @param array $args The array of arguments for the navigation menu.
	 */
	function cera_grimlock_navbar_nav_menu( $args ) {
		if ( has_nav_menu( 'primary' ) ) :
			wp_nav_menu( wp_parse_args( $args, array(
				'theme_location' => 'primary',
			) ) );
		endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_navbar_secondary_nav_menu' ) ) :
	/**
	 * Prints the wp nav menu inside Grimlock Navbar.
	 *
	 * @param array $args The array of arguments for the navigation menu.
	 */
	function cera_grimlock_navbar_secondary_nav_menu( $args ) {
		if ( has_nav_menu( 'secondary' ) ) :
			wp_nav_menu( wp_parse_args( $args, array(
				'theme_location' => 'secondary',
			) ) );
		endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_vertical_navbar_nav_menu' ) ) :
	/**
	 * Prints the wp nav menu inside Grimlock Vertical Navbar.
	 *
	 * @param array $args The array of arguments for the navigation menu.
	 */
	function cera_grimlock_vertical_navbar_nav_menu( $args ) {
		if ( has_nav_menu( 'primary' ) ) :
			wp_nav_menu( wp_parse_args( $args, array(
				'theme_location' => 'primary',
			) ) );
		endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_before_content' ) ) :
	/**
	 * Prints markups to open the content wrapper.
	 */
	function cera_grimlock_before_content() {
		?>
		<?php if ( is_active_sidebar( 'before-content-1' ) ) : ?>
			<div id="before_content" class="before_content site-before-content"><?php cera_grimlock_widget_area( 'before-content-1' ); ?></div>
		<?php endif; ?>

		<div id="content" <?php cera_grimlock_content_class( array( 'site-content', 'region' ) ); ?> tabindex="-1">
			<div class="region__container">
				<div class="region__row">
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_after_content' ) ) :
	/**
	 * Prints markups to close the content wrapper.
	 */
	function cera_grimlock_after_content() {
		?>
				</div><!-- .region__row -->
			</div><!-- .region__container -->
		</div><!-- #content -->

		<?php if ( is_active_sidebar( 'after-content-1' ) ) : ?>
			<div id="after_content" class="after_content site-after-content d-print-none"><?php cera_grimlock_widget_area( 'after-content-1' ); ?></div>
		<?php endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_homepage_before_content' ) ) :
	/**
	 * Prints markups to open the content wrapper.
	 */
	function cera_grimlock_homepage_before_content() {
		?>
		<?php if ( is_active_sidebar( 'before-content-1' ) ) : ?>
			<div id="before_content" class="before_content site-before-content"><?php cera_grimlock_widget_area( 'before-content-1' ); ?></div>
		<?php endif; ?>

		<div id="content" tabindex="-1">
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_homepage_after_content' ) ) :
	/**
	 * Prints markups to close the content wrapper.
	 */
	function cera_grimlock_homepage_after_content() {
		?>
		</div><!-- #content -->
		<?php if ( is_active_sidebar( 'after-content-1' ) ) : ?>
			<div id="after_content" class="after_content site-after-content d-print-none"><?php cera_grimlock_widget_area( 'after-content-1' ); ?></div>
		<?php endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_404_before_content' ) ) :
	/**
	 * Prints markups to open the content wrapper.
	 */
	function cera_grimlock_404_before_content() {
		?>
		<div id="content" tabindex="-1">
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_404_after_content' ) ) :
	/**
	 * Prints markups to close the content wrapper.
	 */
	function cera_grimlock_404_after_content() {
		?>
		</div><!-- #content -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_footer' ) ) :
	/**
	 * Prints footer in page.
	 */
	function cera_grimlock_footer() {
		do_action( 'grimlock_prefooter', array(
			'callback' => 'cera_grimlock_prefooter_callback',
		) );

		do_action( 'grimlock_footer', array(
			'callback' => 'cera_grimlock_footer_callback',
		) );
	}
endif;

if ( ! function_exists( 'cera_grimlock_prefooter_callback' ) ) :
	/**
	 * Prints the prefooter widget areas.
	 */
	function cera_grimlock_prefooter_callback() {
		cera_grimlock_widget_areas( 'prefooter', 4 );
	}
endif;

if ( ! function_exists( 'cera_grimlock_footer_callback' ) ) :
	/**
	 * Prints the footer widget areas, site info and back to top button.
	 */
	function cera_grimlock_footer_callback() {
		?>
		<div class="region__row">
			<?php
			$sidebar_active = false;
			for ( $i = 1; $i <= 4; $i++ ) :
				if ( is_active_sidebar( "footer-{$i}" ) ) :
					$sidebar_active = true; ?>
					<div class="<?php echo esc_attr( "region__col region__col--{$i} widget-area" ); ?>">
						<?php dynamic_sidebar( "footer-{$i}" ); ?>
					</div><!-- .region__col -->
					<?php
				endif;
			endfor;

			if ( ! $sidebar_active ) : ?>
				<div class="site-info text-center w-100" role="contentinfo">
					<?php bloginfo( 'title' ); ?><span class="sep"> | </span><?php bloginfo( 'description' ); ?>
				</div><!-- .site-info -->
				<?php
			endif; ?>
		</div><!-- .region__row -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_after_site' ) ) :
	/**
	 * Prints back to top button after site.
	 */
	function cera_grimlock_after_site() {
		do_action( 'grimlock_back_to_top_button' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_sidebar_left' ) ) :
	/**
	 * Prints left sidebar in page.
	 */
	function cera_grimlock_sidebar_left() {
		do_action( 'grimlock_sidebar_left' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_sidebar_right' ) ) :
	/**
	 * Prints right sidebar in page.
	 */
	function cera_grimlock_sidebar_right() {
		do_action( 'grimlock_sidebar_right' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_widget_areas' ) ) :
	/**
	 * Display widget areas as Grimlock region columns.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id     The ID prefix of the widget areas.
	 * @param int    $number The number of widget areas.
	 */
	function cera_grimlock_widget_areas( $id, $number = 1 ) {
		?>
		<div class="region__row">
			<?php
			$number = intval( $number );
			for ( $i = 1; $i <= $number; $i++ ) :
				if ( is_active_sidebar( "{$id}-{$i}" ) ) : ?>
					<div class="<?php echo esc_attr( "region__col region__col--{$i} widget-area" ); ?>">
						<?php dynamic_sidebar( "{$id}-{$i}" ); ?>
					</div><!-- .region__col -->
					<?php
				endif;
			endfor; ?>
		</div><!-- .region__row -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_widget_area' ) ) :
	/**
	 * Display widget areas in homepage.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id The ID of the widget area.
	 */
	function cera_grimlock_widget_area( $id ) {
		if ( is_active_sidebar( $id ) ) : ?>
			<div class="widget-area">
				<?php dynamic_sidebar( $id ); ?>
			</div><!-- .widget-area -->
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_homepage' ) ) :
	/**
	 * Prints widget area in homepage.
	 */
	function cera_grimlock_homepage() {
		if ( post_password_required() ) {
			echo wp_kses( get_the_password_form(), array(
				'form'  => array(
					'action' => array(),
					'class'  => array(),
					'method' => array(),
				),
				'p'     => array(),
				'label' => array(
					'for' => array(),
				),
				'input' => array(
					'name'  => array(),
					'id'    => array(),
					'type'  => array(),
					'size'  => array(),
					'value' => array(),
				),
			) );
			return;
		}
		cera_grimlock_widget_area( 'homepage-1' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_the_author_biography' ) ) :
	/**
	 * Display the author biography.
	 */
	function cera_grimlock_the_author_biography( $args ) {
		if ( ! empty( $args['post_author_biography_displayed'] ) && '' !== get_the_author_meta( 'description' ) && 'post' === get_post_type() ) {
			cera_the_author_biography();
		}
	}
endif;

if ( ! function_exists( 'cera_grimlock_before_posts' ) ) :
	/**
	 * Prints markups to open the posts wrapper.
	 */
	function cera_grimlock_before_posts() {

		$taxonomies = array(
			'category'    => array(
				'before'             => '<span class="posts-filters__title">' . esc_html__( 'Categories: ', 'cera' ) . '</span>',
				'archive_link_label' => esc_html__( 'All categories', 'cera' ),
			),
			'post_tag'    => array(
				'before'             => '<span class="posts-filters__title">' . esc_html__( 'Post tags: ', 'cera' ) . '</span>',
				'archive_link_label' => esc_html__( 'All post tags', 'cera' ),
			),
			'post_format' => array(
				'before'             => '<span class="posts-filters__title">' . esc_html__( 'Post formats: ', 'cera' ) . '</span>',
				'archive_link_label' => esc_html__( 'All post formats', 'cera' ),
			),
		); ?>

		<div class="posts-filters">
			<?php
				foreach ( $taxonomies as $tax => $args ) :
					if ( apply_filters( "grimlock_archive_{$tax}_terms_displayed", false ) ) : ?>
						<div class="posts-filters__child posts-filters__child--<?php echo esc_attr($tax); ?> d-flex w-100">
							<?php
								do_action( 'grimlock_terms', apply_filters( "grimlock_archive_{$tax}_terms_args", wp_parse_args( $args, array(
									'taxonomy'     => $tax,
									'order'        => 'DESC',
									'orderby'      => 'count',
									'archive_link' => get_post_type_archive_link( 'post' ),
								) ) ) ); ?>
						</div>
					<?php
					endif;
				endforeach; ?>
		</div><!-- .posts-filters -->

		<div id="posts" <?php cera_grimlock_posts_class(); ?>>
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_after_posts' ) ) :
	/**
	 * Prints markups to close the posts wrapper.
	 */
	function cera_grimlock_after_posts() {
		?>
		</div><!-- #posts -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_grimlock_post' ) ) :
	/**
	 * Output the Grimlock Post Component in `content` template part.
	 *
	 * @since 1.0.0
	 */
	function cera_grimlock_post() {
		do_action( 'grimlock_post' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_search_post' ) ) :
	/**
	 * Output the Grimlock Search Post Component in `content-search` template part.
	 *
	 * @since 1.0.0
	 */
	function cera_grimlock_search_post() {
		do_action( 'grimlock_search_post' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_page' ) ) :
	/**
	 * Output the Grimlock Page Component in `content-page` template part.
	 *
	 * @since 1.0.0
	 */
	function cera_grimlock_page() {
		do_action( 'grimlock_page' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_404' ) ) :
	/**
	 * Output the Grimlock 404 Component in the `404` template.
	 *
	 * @since 1.1.8
	 */
	function cera_grimlock_404() {
		do_action( 'grimlock_404' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_single' ) ) :
	/**
	 * Output the Grimlock Single Component in `content-single` template part.
	 *
	 * @since 1.0.0
	 */
	function cera_grimlock_single() {
		do_action( 'grimlock_single' );
	}
endif;

if ( ! function_exists( 'cera_grimlock_post_author' ) ) :
	/**
	 * Prints HTML for post author
	 *
	 * @since 1.0.0
	 *
	 * @param string $args The component args
	 */
	function cera_grimlock_post_author( $args ) {
		if ( true == $args['post_author_displayed'] ) :
			cera_the_author();
		endif;
	}
endif;

if ( ! function_exists( 'cera_grimlock_remove_actions' ) ) :
	/**
	 * Remove several actions using conditional tags.
	 *
	 * @since 1.0.0
	 */
	function cera_grimlock_remove_actions() {
		if ( is_page_template( 'template-homepage.php' ) || is_page_template( 'template-dashboard.php' ) || is_page_template( 'template-homepage-minimal.php' ) ) :
			remove_action( 'cera_header', 'cera_grimlock_before_content',          20 );
			add_action(    'cera_header', 'cera_grimlock_homepage_before_content', 20 );

			remove_action( 'cera_footer', 'cera_grimlock_after_content',           10 );
			add_action(    'cera_footer', 'cera_grimlock_homepage_after_content',  10 );
		elseif ( is_404() ) :
			remove_action( 'cera_header', 'cera_grimlock_before_content',     20 );
			add_action(    'cera_header', 'cera_grimlock_404_before_content', 20 );

			remove_action( 'cera_footer', 'cera_grimlock_after_content',      10 );
			add_action(    'cera_footer', 'cera_grimlock_404_after_content',  10 );
		endif;
	}
endif;

/**
 * Display the classes for the #site-content div.
 *
 * @since 1.0.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function cera_grimlock_content_class( $class = '' ) {
	$classes = cera_grimlock_to_array( $class );
	echo 'class="' . esc_attr( join( ' ', array_unique( apply_filters( 'grimlock_content_class', $classes ) ) ) ) . '"';
}

/**
 * Display the classes for the #posts div.
 *
 * @since 1.0.0
 *
 * @param string|array $class One or more classes to add to the class list.
 */
function cera_grimlock_posts_class( $class = '' ) {
	$classes = cera_grimlock_to_array( $class );
	echo 'class="' . esc_attr( join( ' ', array_unique( apply_filters( 'grimlock_posts_class', $classes ) ) ) ) . '"';
}

/**
 * Convert a given argument `$args` into an array.
 *
 * @param  string|array $args The string or array to cast as an array.
 *
 * @return array              The casted array of arguments.
 */
function cera_grimlock_to_array( $args ) {
	if ( ! empty( $args ) && ( is_string( $args ) || is_array( $args ) ) ) {
		if ( is_string( $args ) ) {
			$args = preg_split( '#\s+#', $args );
		}
		return array_map( 'esc_attr', $args );
	}
	return array();
}
