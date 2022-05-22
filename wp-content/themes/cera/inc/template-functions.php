<?php
/**
 * Cera template functions.
 *
 * @package cera
 */

if ( ! function_exists( 'cera_nav_menu_css_class' ) ) :
	/**
	 * Add CSS classes to default primary menu.
	 *
	 * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
	 * @param WP_Post  $item    The current menu item.
	 * @param stdClass $args    An object of wp_nav_menu() arguments.
	 * @param int      $depth   Depth of menu item. Used for padding.
	 *
	 * @return array            The updated array of CSS classes applied to the menu item's `<li>` element.
	 */
	function cera_nav_menu_css_class( $classes, $item, $args, $depth ) {
		if ( 'primary' === $args->theme_location ) {
			$classes[] = 'list-inline-item';
		}
		return $classes;
	}
endif;

if ( ! function_exists( 'cera_body_classes' ) ) :
	/**
	 * Add CSS classes to the body
	 */
	function cera_body_classes( $classes ) {

		$cera_classes = array(
			'grimlock--navigation-fixed-left',
			'grimlock--navigation-classic-top',
			'grimlock--navigation-fixed',
		);

		if ( has_header_image() ) {
			$cera_custom_header_class = array( 'grimlock--custom_header-displayed' );
			$cera_classes = array_merge($cera_classes, $cera_custom_header_class);
		}

		$classes = array_merge($classes, $cera_classes);


		return $classes;
	}
endif;

if ( ! function_exists( 'cera_before_site' ) ) :
	/**
	 * Prints HTML for the header.
	 *
	 * @since 1.0.0
	 */
	function cera_before_site() {
		?>
		<div id="slideout-wrapper" class="slideout-wrapper slideout-menu slideout-menu-left">
			<nav id="vertical-navigation" class="grimlock-vertical-navigation main-navigation grimlock-vertical-navbar vertical-navbar">
				<div class="navbar-wrapper">

					<div class="vertical-navbar-brand">

						<div class="site-branding grimlock-site_identity">
							<h1 class="screen-reader-text"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
							<?php
							if ( function_exists( 'has_custom_logo' ) && function_exists( 'the_custom_logo' ) && has_custom_logo() ) :
								the_custom_logo();
							else : ?>
								<div id="site-title" class="site-title navbar-brand__title">
									<a class="site-title-link navbar-brand__title-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" target="_self"><?php bloginfo( 'name' ); ?></a>
								</div>
								<?php
								$description = get_bloginfo( 'description', 'display' );
								if ( $description || is_customize_preview() ) : ?>
									<small id="site-description" class="site-description navbar-brand__tagline"><?php echo esc_html( $description ); ?></small>
								<?php
								endif;
							endif; ?>
						</div><!-- .site-branding -->

						<button id="navbar-toggler-mini" class="navbar-toggler collapsed d-none d-lg-flex" type="button">
							<i class="cera-icon cera-menu-arrow"></i>
						</button>

						<button id="navbar-toggler-mobile" class="navbar-toggler slideout-close d-lg-none" type="button">
							<span></span>
						</button>

					</div><!-- .vertical-navbar-brand -->
					<?php
					if ( has_nav_menu( 'primary' ) ) :
						wp_nav_menu( array(
							'theme_location' => 'primary',
							'menu_id'        => 'menu-primary',
							'menu_class'     => 'vertical-navbar-nav sidebar-nav nav navbar-nav navbar-nav--main-menu nav-pills nav-stacked',
							'container'      => false,
						) );
					endif; ?>

					<?php do_action( 'cera_vertical_navbar_sidebar_top' ); ?>

				</div><!-- .navbar-wrapper -->
			</nav><!-- .vertical-navbar -->
		</div><!-- #slideout-wrapper -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_header' ) ) :
	/**
	 * Prints HTML for the header.
	 *
	 * @since 1.0.0
	 */
	function cera_header() {
		?>
		<header id="header" class="site-header region">
			<div class="region__inner">
				<div class="region__container">

					<nav id="navigation" class="navbar-full grimlock-navigation site-navigation main-navigation grimlock-navbar navbar navbar--fixed-left navbar--container-fluid grimlock-navbar--hamburger hamburger-navbar">
						<div class="navbar__container">

							<div class="navbar__header d-lg-none">

								<button id="navbar-toggler" class="navbar-toggler navbar-toggler-right collapsed" type="button" data-toggle="collapse" data-target="#navigation-collapse">
									<span></span>
								</button><!-- .navbar-toggler -->

								<div class="navbar-brand">
									<div class="site-branding">
										<h1 class="screen-reader-text"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
										<?php
										if ( function_exists( 'has_custom_logo' ) && function_exists( 'the_custom_logo' ) && has_custom_logo() ) :
											the_custom_logo();
										else : ?>
											<div id="site-title" class="site-title navbar-brand__title">
												<a class="site-title-link navbar-brand__title-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" target="_self"><?php bloginfo( 'name' ); ?></a>
											</div>
											<?php
											$description = get_bloginfo( 'description', 'display' );
											if ( $description || is_customize_preview() ) : ?>
												<small id="site-description" class="site-description navbar-brand__tagline"><?php echo esc_html( $description ); ?></small>
											<?php
											endif;
										endif; ?>
									</div><!-- .site-branding -->
								</div><!-- .navbar-brand -->

							</div><!-- .navbar__header -->

							<ul class="nav navbar-nav navbar-nav--search d-none d-lg-flex">
								<li class="menu-item">
									<div class="navbar-search d-flex align-items-center">
										<span class="search-icon"><i class="cera-icon cera-search"></i></span>
										<?php get_search_form(); ?>
									</div><!-- .navbar-search -->
								</li><!-- .menu-item -->
							</ul><!-- .navbar-nav--search -->

							<div class="hamburger-navbar-nav-menu-container">
								<ul class="grimlock-login-navbar_nav_menu nav navbar-nav navbar-nav--login">
									<?php if ( is_user_logged_in() ):
									    $current_user = wp_get_current_user();
										$current_user_url = get_edit_profile_url( $current_user->ID ); ?>
										<li class="menu-item menu-item--profile m-0">
											<a href="<?php echo esc_url( $current_user_url ); ?>" class="p-0 text-inherit">
												<span class="avatar-round-ratio avatar-round-ratio--small mr-2">
										            <?php echo get_avatar( $current_user->ID, 32 ); ?>
												</span><!-- .avatar-round-ratio -->
												<span class="font-weight-bold d-none d-lg-block">
													<?php echo esc_html( $current_user->display_name ); ?>
												</span>
											</a><!-- .text-inherit -->
										</li><!-- .menu-item -->
									<?php else: ?>
										<li class="menu-item menu-item--login">
											<a href="<?php echo esc_url( wp_login_url() ); ?>" class="btn btn-outline-primary"><?php esc_html_e( 'Login', 'cera' ); ?></a>
										</li><!-- .menu-item -->
										<li class="menu-item menu-item--register">
											<a href="<?php echo esc_url( wp_registration_url() ); ?>" class="btn btn-primary"><?php esc_html_e( 'Register', 'cera' ); ?></a>
										</li><!-- .menu-item -->
									<?php endif; ?>
								</ul><!-- .grimlock-login-navbar_nav_menu -->
							</div><!-- .hamburger-navbar-nav-menu-container -->

						</div><!-- .navbar__container -->
					</nav><!-- .navbar__container -->

						<div id="custom_header" class="custom_header region region--12-cols-center region--container-classic section" <?php cera_header_image_style(); ?>>
							<div class="region__inner">
								<div class="region__container">
									<div class="region__row">
										<div class="region__col region__col--2">
											<div class="section__header">

												<?php
												if ( is_home() && is_front_page() ) : ?>
													<h1 class="section__title display-1">
														<?php bloginfo( 'name' ); ?>
													</h1>
												<?php elseif ( is_archive() ) : ?>
													<?php the_archive_title(); ?>
												<?php elseif ( is_search() ) :
													/* translators: %s: The search query */ ?>
													<h1 class="section__title display-1">
														<?php printf( esc_html__( 'Search Results for: %s', 'cera' ), '<span>' . get_search_query() . '</span>' ); ?>
													</h1>
												<?php elseif ( is_singular('post') ) :
													cera_the_category_list();
													the_title( '<h1 class="page-title entry-title">', '</h1>' ); ?>
													<?php

													do_action( 'cera_breadcrumb' );

													if ( 'post' === get_post_type() ) : ?>
														<span class="entry-meta">
															<?php cera_the_author(); ?>
															<?php cera_the_date(); ?>
														</span><!-- .entry-meta -->
													<?php
													endif;
												elseif ( is_home() && ! is_front_page() || is_singular() ) : ?>
													<h1 class="section__title display-1">
														<?php single_post_title(); ?>
												<?php endif; ?>
											</div><!-- .section__header -->
										</div><!-- .region__col -->
									</div><!-- .region__row -->
								</div><!-- .region__container -->
							</div><!-- .region__inner -->
						</div><!-- #custom_header -->

				</div><!-- .container -->
			</div><!-- .region__inner-->
		</header><!-- #header -->

		<div id="content" <?php cera_content_class(); ?> tabindex="-1">
			<div class="region__container">
				<div class="region__row">
		<?php
	}
endif;

if ( ! function_exists( 'cera_footer' ) ) :
	/**
	 * Prints footer in page.
	 */
	function cera_footer() {
		?>
					</div><!-- .region__row -->
			</div><!-- .region__container -->
		</div><!-- #content -->

		<div id="footer" class="site-footer region region--container-classic region--3-3-3-3-cols-left d-print-none">
			<div class="region__inner">
				<div class="region__container">
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
				</div><!-- .region__container -->
			</div><!-- .region__inner -->
		</div><!-- #footer -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_content_class' ) ) :
	/**
	 * Display the classes for the #site-content div.
	 *
	 * @since 1.0.0
	 */
	function cera_content_class() {
		$classes = array(
			'site-content',
			'region',
		);

		if ( is_page() ) {
			$page_template = get_page_template_slug( get_queried_object_id() );

			switch ( $page_template ) {
				case 'template-narrower-12-cols-left.php':
					$classes[] = 'region--12-cols-left';
					$classes[] = 'region--container-narrower';
					break;

				case 'template-narrow-12-cols-left.php':
					$classes[] = 'region--12-cols-left';
					$classes[] = 'region--container-narrow';
					break;

				case 'template-classic-12-cols-left.php':
				case 'template-minimal.php':
					$classes[] = 'region--12-cols-left';
					$classes[] = 'region--container-fluid';
					break;

				case 'template-classic-9-3-cols-left.php':
					$classes[] = 'region--9-3-cols-left';
					$classes[] = 'region--container-fluid';
					break;

				case 'template-classic-3-9-cols-left.php':
					$classes[] = 'region--3-9-cols-left';
					$classes[] = 'region--container-fluid';
					break;

				default:
					$classes[] = 'region--9-3-cols-left';
					$classes[] = 'region--container-fluid';
			}
		} elseif ( is_singular() ) {
			$classes[] = 'region--12-cols-left';
			$classes[] = 'region--container-narrow';
		} elseif ( is_404() ) {
			$classes[] = 'region--12-cols-left';
			$classes[] = 'region--container-narrower';
		} elseif ( is_archive() || is_home() ) {
			$classes[] = 'region--12-cols-left';
			$classes[] = 'region--container-fluid';
		} elseif ( is_search() ) {
			$classes[] = 'region--12-cols-left';
			$classes[] = 'region--container-narrower';
		} else {
			$classes[] = 'region--12-cols-left';
			$classes[] = 'region--container-fluid';
		}

		echo 'class="' . esc_attr( join( ' ', $classes ) ) . '"';
	}
endif;

if ( ! function_exists( 'cera_header_image_style' ) ) :
	/**
	 * Print the style attribute for the #custom_header div to display the header image.
	 *
	 * @since 1.0.0
	 */
	function cera_header_image_style() {
		if ( has_header_image() ) {
			echo 'style="background-image: url(' . esc_url_raw( get_header_image() ) . ');"';
		}
	}
endif;

if ( ! function_exists( 'cera_sidebar_right' ) ) :
	/**
	 * Prints right sidebar in page.
	 */
	function cera_sidebar_right() {
		if ( ( is_page_template( 'template-classic-9-3-cols-left.php' ) && is_active_sidebar( 'sidebar-1' ) ) || ( is_singular() && is_active_sidebar( 'sidebar-1' ) ) ) : ?>
			<aside id="secondary-right" class="widget-area sidebar region__col region__col--3">
				<?php dynamic_sidebar( 'sidebar-1' ); ?>
			</aside><!-- #secondary-right -->
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_vertical_navbar_sidebar_top' ) ) :
	/**
	 * Prints right sidebar in page.
	 */
	function cera_vertical_navbar_sidebar_top() {
		if ( is_active_sidebar( 'vertical-navbar-1' ) ) : ?>
			<div class="vertical-navbar__widgets vertical-navbar__widgets--top">
				<aside class="widget-area">
					<?php dynamic_sidebar( 'vertical-navbar-1' ); ?>
				</aside><!-- #secondary-right -->
			</div>
		<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_vertical_navbar_sidebar_bottom' ) ) :
	/**
	 * Prints right sidebar in page.
	 */
	function cera_vertical_navbar_sidebar_bottom() {
		if ( is_active_sidebar( 'vertical-navbar-2' ) ) : ?>
			<div class="vertical-navbar__widgets vertical-navbar__widgets--bottom">
				<aside class="widget-area">
					<?php dynamic_sidebar( 'vertical-navbar-2' ); ?>
				</aside><!-- #secondary-right -->
			</div>
		<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_before_posts' ) ) :
	/**
	 * Prints markups to open the posts wrapper.
	 */
	function cera_before_posts() {
		?>
		<div id="posts" class="posts blog-posts posts--4-4-4-cols-classic posts--height-not-equalized">
		<?php
	}
endif;

if ( ! function_exists( 'cera_before_search_posts' ) ) :
	/**
	 * Prints markups to open the posts wrapper.
	 */
	function cera_before_search_posts() {
		?>
		<div id="posts" class="posts blog-posts posts--12-cols-classic posts--height-not-equalized">
		<?php
	}
endif;

if ( ! function_exists( 'cera_after_posts' ) ) :
	/**
	 * Prints markups to close the posts wrapper.
	 */
	function cera_after_posts() {
		?>
		</div><!-- #posts -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_post' ) ) :
	/**
	 * Prints HTML for the post.
	 *
	 * @since 1.0.0
	 */
	function cera_post() {
		?>
		<div class="card">

			<?php if ( has_post_format( array( 'video', 'audio', 'image', 'gallery' ) ) ) : ?>
				<div class="post-media"><?php the_content(); ?></div>
			<?php elseif ( has_post_thumbnail() ) : ?>
				<?php
				cera_the_post_thumbnail( 'thumbnail-6-6-cols-classic', array(
					'class' => 'card-img wp-post-image',
				) ); ?>
			<?php endif; ?>

			<div class="card-body">
				<?php
				if ( 'post' === get_post_type() &&  has_post_format() || is_sticky() ) : ?>
					<div class="entry-labels">
						<?php
						cera_the_sticky_mark();
						cera_the_post_format(); ?>
					</div>
					<?php
				endif; ?>

				<header class="entry-header">
					<div class="entry-meta">
						<?php cera_the_category_list(); ?>
					</div><!-- .entry-meta -->
					<?php
					if ( is_single() ) :
						the_title( '<h1 class="entry-title">', '</h1>' );
					else :
						the_title( '<h2 class="entry-title h4"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
					endif; ?>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php if ( has_post_format( array( 'link', 'quote' ) ) ) : ?>
						<?php the_content(); ?>
					<?php else : ?>
						<?php the_excerpt(); ?>
					<?php endif; ?>
				</div><!-- .entry-content -->

			</div><!-- .card-body-->

			<?php if ( 'post' === get_post_type() ) : ?>
				<footer class="entry-footer">
					<?php
					cera_the_author();
					cera_the_date();
					cera_comments_link();
					?>
				</footer><!-- .entry-footer -->
			<?php endif; ?>

		</div><!-- .card-->
		<?php
	}
endif;

if ( ! function_exists( 'cera_the_post_thumbnail' ) ) :
	/**
	 * Prints HTML for the post thumbnail.
	 *
	 * @since 1.0.0
	 *
	 * @param string $size The size for the post thumbnail.
	 * @param array  $attr The array of attributes for the post thumbnail.
	 */
	function cera_the_post_thumbnail( $size = 'large', $attr = array() ) {
		if ( has_post_thumbnail() ) : ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>" class="post-thumbnail" rel="bookmark">
				<?php the_post_thumbnail( $size, $attr ); ?>
			</a>
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_search_post' ) ) :
	/**
	 * Prints HTML for the post.
	 *
	 * @since 1.0.0
	 */
	function cera_search_post() {
		?>
		<div class="card">

			<?php if ( has_post_format( array( 'video', 'audio', 'image', 'gallery' ) ) ) : ?>
				<div class="post-media"><?php the_content(); ?></div>
			<?php elseif ( has_post_thumbnail() ) : ?>
				<?php
				cera_the_post_thumbnail( 'thumbnail-6-6-cols-classic', array(
					'class' => 'card-img wp-post-image',
				) ); ?>
			<?php endif; ?>

			<div class="card-body">
				<?php
				if ( 'post' === get_post_type() &&  has_post_format() || is_sticky() ) : ?>
					<div class="entry-labels">
						<?php
						cera_the_sticky_mark();
						cera_the_post_format(); ?>
					</div>
					<?php
				endif; ?>

				<header class="entry-header">
					<div class="entry-meta">
						<?php cera_the_category_list(); ?>
					</div><!-- .entry-meta -->
					<?php the_title( '<h2 class="entry-title h4"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php if ( has_post_format( array( 'link', 'quote' ) ) ) : ?>
						<?php the_content(); ?>
						<a href="<?php the_permalink(); ?>" class="more-link btn btn-link"><?php esc_html_e( 'Continue reading', 'cera' ); ?></a>
					<?php else : ?>
						<?php the_excerpt(); ?>
						<a href="<?php the_permalink(); ?>" class="more-link btn btn-link"><?php esc_html_e( 'Continue reading', 'cera' ); ?></a>
						<?php if ( has_tag() ): cera_the_tag_list(); endif; ?>
					<?php endif; ?>

				</div><!-- .entry-content -->

			</div><!-- .card-body-->

			<?php if ( 'post' === get_post_type() ) : ?>
				<footer class="entry-footer">
					<?php
					cera_the_author();
					cera_the_date();
					cera_comments_link();
					?>
				</footer><!-- .entry-footer -->
			<?php endif; ?>

		</div><!-- .card-->
		<?php
	}
endif;

if ( ! function_exists( 'cera_single' ) ) :
	/**
	 * Prints HTML for the single post.
	 *
	 * @since 1.0.0
	 */
	function cera_single() {
		?>

		<header class="grimlock--page-header entry-header">
			<?php
			cera_the_category_list();
			the_title( '<h1 class="page-title entry-title">', '</h1>' ); ?>
			<?php
			do_action( 'cera_breadcrumb' );

			if ( 'post' === get_post_type() ) : ?>
				<span class="entry-meta">
					<?php cera_the_author(); ?>
					<?php cera_the_date(); ?>
				</span><!-- .entry-meta -->
			<?php
			endif; ?>
		</header><!-- .entry-header -->

		<div class="grimlock--single-content grimlock--page-content entry-content">
			<?php
			the_content();
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'cera' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text sr-only">' . esc_html__( 'Page', 'cera' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text sr-only">, </span>',
			) ); ?>
		</div><!-- .entry-content -->

		<footer class="grimlock--single-footer entry-footer d-none">
			<?php cera_the_tag_list(); ?>
		</footer><!-- .entry-footer -->

		<?php
	}
endif;

if ( ! function_exists( 'cera_page' ) ) :
	/**
	 * Prints HTML for the page.
	 *
	 * @since 1.0.0
	 */
	function cera_page() {
		?>

		<?php if ( empty( get_header_image() ) ) : ?>
			<header class="grimlock--page-header entry-header">
				<?php
				do_action( 'cera_breadcrumb' );
				the_title( '<h1 class="page-title entry-title">', '</h1>' ); ?>
			</header><!-- .entry-header -->
		<?php endif; ?>

		<div class="grimlock--page-content entry-content">
			<?php
			the_content();
			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'cera' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text sr-only">' . esc_html__( 'Page', 'cera' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text sr-only">, </span>',
			) ); ?>
		</div><!-- .entry-content -->

	<?php }
endif;

if ( ! function_exists( 'cera_404' ) ) :
	/**
	 * Prints HTML for the 404 page.
	 *
	 * @since 1.1.8
	 */
	function cera_404() {
		?>
		<div class="grimlock-404 error-404 not-found region grimlock-region grimlock-region--pt-0 grimlock-region--pb-0 region--6-6-cols-left region--container-fluid grimlock-section section grimlock-404--full-screen-displayed">
			<div class="region__inner">
				<div class="region__container">
					<div class="region__row">
						<div class="region__col region__col--1">
							<div class="section__thumbnail">
								<img class="grimlock-section__thumbnail-img section__thumbnail-img img-fluid" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/pages/page-404.jpg' ); ?>" alt="<?php esc_html_e( '404', 'cera' ); ?>" />
							</div><!-- .section__thumbnail -->
						</div><!-- .region__col -->
						<div class="region__col region__col--2">
							<h1 class="page-404-title"><?php esc_html_e( '404', 'cera' ); ?></h1>
							<h4 class="page-404-subtitle"><?php esc_html_e( 'Page not found.', 'cera' ); ?></h4>
							<p class="page-404-text"><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'cera' ); ?></p>
							<?php get_search_form(); ?>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary btn-lg col-12 col-sm-auto"><?php esc_html_e( 'Go back to homepage', 'cera' ); ?></a>
						</div><!-- .region__col -->
					</div><!-- .region__row -->
				</div><!-- .region__container -->
			</div><!-- .region__inner -->
		</div><!-- .grimlock-section -->
		<?php
	}
endif;

if ( ! function_exists( 'cera_the_date' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time.
	 */
	function cera_the_date() {
		if ( 'post' === get_post_type() || 'attachment' === get_post_type() ) {
			$allowed_html = array(
				'time' => array(
					'class'    => true,
					'datetime' => true,
				),
			);

			$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

			if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
				$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
			}

			$time_string = sprintf( $time_string,
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() ),
				esc_attr( get_the_modified_date( 'c' ) ),
				esc_html( get_the_modified_date() )
			);

			printf(
				'<span class="posted-on"><span class="posted-on-label">' . esc_html__( 'Posted on', 'cera' ) . ' </span>%s</span>',
				'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . wp_kses( $time_string, $allowed_html ) . '</a>'
			);
		}
	}
endif;

if ( ! function_exists( 'cera_the_author' ) ) :
	/**
	 * Prints HTML with meta information for the current author.
	 */
	function cera_the_author() {
		if ( 'post' === get_post_type() ) {
			printf(
				'<span class="byline author"><span class="byline-label">' . esc_html__( 'by', 'cera' ) . ' </span>%1$s %2$s</span>',
				'<span class="author-avatar"><span class="avatar-round-ratio"><a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . get_avatar( get_the_author_meta( 'ID' ), 50 ) . '</a></span></span>',
				'<span class="author-vcard vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
			);
		}
	}
endif;

if ( ! function_exists( 'cera_the_sticky_mark' ) ) :
	/**
	 * Prints HTML for "Featured" as Boostrap label when the post is sticky.
	 *
	 * @since 1.0.0
	 */
	function cera_the_sticky_mark() {
		if ( is_sticky() ) : ?>
			<span class="badge badge-primary post-sticky" data-toggle="tooltip" data-placement="top" title="<?php esc_attr_e( 'Sticky', 'cera' ); ?>"></span>
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_get_more_link_text' ) ) :
	/**
	 * Get the HTML for the More link.
	 *
	 * @since 1.0.0
	 *
	 * @return string The more link text.
	 */
	function cera_get_more_link_text() {
		$allowed_html = array(
			'span' => array(
				'class' => array(),
			),
		);

		$more_link_text = sprintf(
			/* translators: 1: Name of current post, 2: Right arrow */
			wp_kses( __( 'Continue reading %1$s %2$s', 'cera' ), $allowed_html ),
			the_title( '<span class="screen-reader-text sr-only">"', '"</span>', false ),
			'<span class="meta-nav">&rarr;</span>'
		);

		return apply_filters( 'cera_more_link_text', $more_link_text );
	}
endif;

if ( ! function_exists( 'cera_the_category_list' ) ) :
	/**
	 * Prints HTML with meta information for the categories.
	 */
	function cera_the_category_list() {
		if ( 'post' === get_post_type() ) {

			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( esc_html__( ' ', 'cera' ) );
			if ( $categories_list && cera_categorized_blog() ) {
				// $categories_list doesn't need to be escaped here cause it comes from native WP get_the_category_list() function
				printf( '<span class="cat-links">%1$s</span>', $categories_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
endif;

if ( ! function_exists( 'cera_the_tag_list' ) ) :
	/**
	 * Prints HTML with meta information for the post tags.
	 */
	function cera_the_tag_list() {
		if ( 'post' === get_post_type() ) {
			$tags_list = get_the_tag_list( '', ' ' );
			if ( $tags_list ) {
				// $tags_list doesn't need to be escaped here cause it comes from native WP get_the_tag_list() function
				printf( '<span class="tags-links">%1$s</span>', $tags_list ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}
	}
endif;

if ( ! function_exists( 'cera_the_post_format' ) ) :
	/**
	 * Prints HTML for the post format as Boostrap label.
	 *
	 * @since 1.0.0
	 */
	function cera_the_post_format() {
		$post_format = get_post_format();
		if ( false !== $post_format ) :
			$post_format_link_title = sprintf(
				/* translators: %s: The post format name */
				esc_html__( 'View posts formatted as %s', 'cera' ),
				esc_attr( strtolower( get_post_format_string( $post_format ) ) )
			); ?>
			<a href="<?php echo esc_url( get_post_format_link( $post_format ) ); ?>" title="<?php echo esc_attr( $post_format_link_title ); ?>" class="badge badge-primary post-format post-format--<?php echo esc_attr( $post_format ); ?>">
				<i class="cera-icon cera-<?php echo esc_html( $post_format ); ?>"></i>
			</a>
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_comments_link' ) ) :
	/**
	 * Prints HTML with meta information for the comments.
	 */
	function cera_comments_link() {
		$has_comments_link = ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() );
		if ( apply_filters( 'cera_has_comments_link', $has_comments_link ) ) {
			echo ' <span class="comments-link">';
			comments_popup_link( '0', '1', '%' );
			echo '</span>';
		}
	}
endif;

if ( ! function_exists( 'cera_the_author_biography' ) ) :
	/**
	 * Display the author biography.
	 */
	function cera_the_author_biography() {
		if ( '' !== get_the_author_meta( 'description' ) && 'post' === get_post_type() ) :
			$avatar_args = array(
				'class' => array( 'd-flex', 'align-self-start', 'mr-3' ),
			); ?>
			<div class="card card-static card--author-info bg-black-faded">
				<div class="media author-info">
					<span class="avatar-round-ratio big">
						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
							<?php echo get_avatar( get_the_author_meta( 'user_email' ), 140, '', '', $avatar_args ); ?>
						</a>
					</span>
					<div class="author-description media-body">
						<h4 class="author-title h5"><span class="author-heading"><?php esc_html_e( 'By', 'cera' ); ?></span> <?php echo get_the_author(); ?></h4>
						<div class="author-bio">
							<?php the_author_meta( 'description' ); ?>
							<div class="mt-1">
								<a class="btn btn-link" href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
									<?php
									/* translators: %s: The author name */
									printf( esc_html__( 'View all posts by %s', 'cera' ), esc_html( get_the_author() ) ); ?>
								</a>
							</div>
						</div><!-- .author-bio -->
					</div><!-- .author-description -->
				</div><!-- .author-info -->
			</div>
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_comment' ) ) :
	/**
	 * Template for comments and pingbacks.
	 *
	 * @param object $comment The comment object.
	 * @param array  $args    The array of arguments for the comment link.
	 * @param int    $depth   The depth of comment replies.
	 */
	function cera_comment( $comment, $args, $depth ) {
		// @codingStandardsIgnoreLine
		$GLOBALS['comment'] = $comment;

		if ( 'pingback' === $comment->comment_type || 'trackback' === $comment->comment_type ) : ?>

			<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>

				<div class="comment-body mb-2">
					<h5><?php esc_html_e( 'Pingback:', 'cera' ); ?></h5>
					<div><?php comment_author_link(); ?></div>
				</div><!-- .comment-body -->

			</li><!-- #-comment-## -->

			<?php
		else : ?>

		<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>

			<div id="div-comment-<?php comment_ID(); ?>" class="comment-main comment-main-<?php comment_ID(); ?> row ml-0 mr-0 mb-md-0 mb-4 mt-0">

				<div class="col-12 col-sm-auto pl-0 pr-0 pr-md-2 pb-2 pb-md-0">
					<div class="comment-img text-left m-0">
						<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
							<span class="avatar-round-ratio medium"><?php echo 0 !== $args['avatar_size'] ? get_avatar( $comment, $args['avatar_size'] ) : ''; ?></span>
						</a>
					</div><!-- .comment-img -->
				</div><!-- .col -->

				<div class="col pr-0 pl-0">

					<div class="comment-body p-3 p-sm-4">

						<h5 class="comment-title media-heading">
							<span class="fn"><?php comment_author_link(); ?></span>
						</h5><!-- .media-heading -->

						<div class="comment-content">
							<?php comment_text(); ?>

							<?php if ( '0' === $comment->comment_approved ) : ?>
								<p class="alert alert-danger comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'cera' ); ?></p>
							<?php endif; ?>

						</div><!-- .comment-content -->

						<footer class="comment-meta">

							<time datetime="<?php comment_time( 'c' ); ?>" class="comment-time">
								<?php
									/* translators: 1: Date, 2: Time */
									printf( esc_html_x( '%1$s at %2$s', '1: date, 2: time', 'cera' ), esc_html( get_comment_date() ), esc_html( get_comment_time() ) ); ?>
							</time><!-- .comment-time -->

							<?php
							$args = array_merge( $args, array(
								'add_below' => 'div-comment',
								'depth'     => $depth,
								'max_depth' => $args['max_depth'],
								'before'    => '<span class="reply">',
								'after'     => '</span>',
							) );
							comment_reply_link( $args ); ?>

							<?php edit_comment_link( esc_html__( 'Edit', 'cera' ), '<span class="edit-link">', '</span>' ); ?>

						</footer><!-- .comment-meta -->

					</div><!-- .comment-body -->

				</div><!-- .col -->

			</div><!-- .comment-main -->

			<!-- "</li>" No closure tag, wp_list_comments "end-callback" do the job -->
			<?php
		endif;
	}
endif;

if ( ! function_exists( 'cera_get_the_archive_title' ) ) :
	/**
	 * Change the retrieved the archive title based on the queried object.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $title The archive title.
	 *
	 * @return string        The updated archive title.
	 */
	function cera_get_the_archive_title( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_year() ) {
			$title = get_the_date( esc_html__( 'Y', 'cera' ) );
		} elseif ( is_month() ) {
			$title = get_the_date( esc_html__( 'F Y', 'cera' ) );
		} elseif ( is_day() ) {
			$title = get_the_date( esc_html__( 'F j, Y', 'cera' ) );
		} elseif ( is_tax( 'post_format' ) ) {
			if ( is_tax( 'post_format', 'post-format-aside' ) ) {
				$title = esc_html__( 'Asides', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
				$title = esc_html__( 'Galleries', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
				$title = esc_html__( 'Images', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
				$title = esc_html__( 'Videos', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
				$title = esc_html__( 'Quotes', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
				$title = esc_html__( 'Links', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
				$title = esc_html__( 'Statuses', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
				$title = esc_html__( 'Audio', 'cera' );
			} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
				$title = esc_html__( 'Chats', 'cera' );
			}
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
		} else {
			$title = esc_html__( 'Archives', 'cera' );
		}

		return $title;
	}
endif;

/**
 * List cera page templates.
 *
 * @since 1.0.0
 *
 * @param array $templates The array of page templates. Keys are filenames, values are translated names.
 *
 * @return array           The array of page templates.
 */
function cera_theme_page_templates( $templates ) {
	unset( $templates['template-classic-3-9-cols-left.php'] );
	unset( $templates['template-classic-9-3-cols-left.php'] );
	unset( $templates['template-homepage-minimal.php'] );
	unset( $templates['template-homepage.php'] );
	unset( $templates['template-dashboard.php'] );
	unset( $templates['template-minimal.php'] );
	return $templates;
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function cera_categorized_blog() {
	$all_the_cool_cats = get_transient( 'cera_categories' );
	if ( false === $all_the_cool_cats ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'cera_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so cera_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so cera_categorized_blog should return false.
		return false;
	}
}

/**
 * Allow to inject code immediately following the opening <body> tag.
 *
 */
if ( ! function_exists( 'cera_body_open' ) ) {
	function cera_body_open() {
		do_action( 'wp_body_open' );
	}
}

/**
 * Flush out the transients used in cera_categorized_blog.
 */
function cera_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'cera_categories' );
}
add_action( 'edit_category', 'cera_category_transient_flusher' );
add_action( 'save_post',     'cera_category_transient_flusher' );
