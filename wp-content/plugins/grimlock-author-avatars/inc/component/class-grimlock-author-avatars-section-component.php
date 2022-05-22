<?php
/**
 * Grimlock_Author_Avatars_Section_Component Class
 *
 * @author  Themosaurus
 * @since   1.0.0
 * @package  grimlock
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The class to generate section in page.
 */
class Grimlock_Author_Avatars_Section_Component extends Grimlock_Section_Component {
	/**
	 * @var array List of layouts that require swiper.js
	 */
	protected $slider_layouts;

	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'limit'          => 3,
			'roles'          => array( 'Subscriber' ),
			'show_name'      => true,
			'user_link'      => 'bp_memberpage',
			'orderby'        => 'display_name',
			'sort_direction' => 'ascending',
			'hiddenusers'    => array(),
		) ) );

		$this->slider_layouts = array(
			'avatars-12-by-5-cols-overlay-slider',
			'avatars-3-3-3-3-cols-overlay-slider',
			'avatars-4-4-4-cols-overlay-slider',
		);

		// Enqueue swiper scripts if the layout is a slider
		if ( in_array( $this->props['avatars_layout'], $this->slider_layouts ) && ( ! wp_script_is( 'swiper' ) || ! wp_script_is( 'grimlock-swiper' ) ) ) {
			wp_enqueue_style( 'swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/css/vendor/swiper.min.css', array(), '4.4.6' );
			wp_enqueue_script( 'swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/vendor/swiper.min.js', array(), '4.4.6', true );
			wp_enqueue_script( 'grimlock-swiper', GRIMLOCK_PLUGIN_DIR_URL . 'assets/js/swiper.js', array( 'swiper', 'jquery' ), GRIMLOCK_VERSION, true );
		}
	}

	/**
	 * Change the author avatars user list classes
	 *
	 * @param array $classes Classes on the user list container
	 *
	 * @return array Modified classes
	 */
	public function change_author_avatars_userlist_class( $classes ) {
		if ( in_array( $this->props['avatars_layout'], $this->slider_layouts ) ) {
			$classes[] = 'swiper-wrapper';
		}

		return $classes;
	}

	/**
	 * Change the author avatars user classes
	 *
	 * @param array $classes Classes on the user container
	 *
	 * @return array Modified classes
	 */
	public function change_author_avatars_user_class( $classes ) {
		if ( in_array( $this->props['avatars_layout'], $this->slider_layouts ) ) {
			$classes[] = 'swiper-slide';
		}

		return $classes;
	}

	/**
	 * Change the author avatars userlist template
	 *
	 * @param string $template Userlist template
	 *
	 * @return string Modified template
	 */
	public function change_author_avatars_userlist_template( $template ) {
		$template = $this->get_before_userlist() . $template . $this->get_after_userlist();

		return $template;
	}

	/**
	 * Retrieve the classes for the component as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param string|array $class One or more classes to add to the class list.
	 * @return array Array of classes.
	 */
	public function get_class( $class = '' ) {
		$classes   = parent::get_class( $class );
		$classes[] = 'grimlock-author-avatars-section';
		return array_unique( $classes );
	}

	/**
	 * Get HTML before the user list
	 */
	public function get_before_userlist() {
		ob_start();

		if ( in_array( $this->props['avatars_layout'], $this->slider_layouts ) ) : ?>

			<div class="swiper-container">

		<?php endif;

		return ob_get_clean();
	}

	/**
	 * Get HTML after the user list
	 */
	public function get_after_userlist() {
		ob_start();

		if ( in_array( $this->props['avatars_layout'], $this->slider_layouts ) ) : ?>

			<div class="swiper-pagination"></div><!-- swiper-pagination -->
			</div><!-- .swiper-container -->

			<div class="swiper-button-prev"></div>
			<div class="swiper-button-next"></div>

		<?php endif;

		return ob_get_clean();
	}

	/**
	 * Get data attributes as property-values pairs for the component using props.
	 *
	 * @since 1.4.1
	 */
	public function get_data_attributes() {
		$data_attributes = parent::get_data_attributes();

		if ( in_array( $this->props['avatars_layout'], $this->slider_layouts ) ) {
			$data_attributes['auto-slide-enabled'] = $this->props['auto_slide_enabled'];

			switch ( $this->props['avatars_layout'] ) {
				case 'avatars-12-by-5-cols-overlay-slider':
					$data_attributes['slides-per-view'] = 5;
					break;
				case 'avatars-3-3-3-3-cols-overlay-slider':
					$data_attributes['slides-per-view'] = 4;
					break;
				case 'avatars-4-4-4-cols-overlay-slider':
				default:
					$data_attributes['slides-per-view'] = 3;
					break;
			}
		}

		return $data_attributes;
	}

	/**
	 * Display the current component content.
	 *
	 * @since 1.0.0
	 */
	protected function render_content() {
		// Add Author Avatars filters
		add_filter( 'grimlock_author_avatars_userlist_class', array( $this, 'change_author_avatars_userlist_class'    ), 10, 1 );
		add_filter( 'grimlock_author_avatars_user_class',     array( $this, 'change_author_avatars_user_class'        ), 10, 1 );
		add_filter( 'aa_userlist_template',                   array( $this, 'change_author_avatars_userlist_template' ), 20, 1 );

		?>
        <div class="section__content section__content--<?php echo $this->props['layout']; ?> section__content--<?php echo $this->props['avatars_layout']; ?>">

			<?php
			$shortcode  = '[authoravatars';
			$shortcode .= " limit='{$this->props['limit']}'";
			$shortcode .= " user_link='{$this->props['user_link']}'";
			$shortcode .= " order='{$this->props['orderby']}'";
			$shortcode .= " sort_direction='{$this->props['sort_direction']}'";
			$shortcode .= ! empty( $this->props['show_name'] ) ? " show_name='true'" : '';
			$shortcode .= " blogs='all'";

			if ( ! empty( $this->props['hiddenusers'] ) ) :
				$hiddenuser       = is_array( $this->props['hiddenusers'] ) ? implode( ',', $this->props['hiddenusers'] ) : $this->props['hiddenusers'];
				$shortcode .= " hiddenusers='{$hiddenuser}'";
			endif;

			if ( ! empty( $this->props['roles'] ) ) :
				$role       = implode( ',', $this->props['roles'] );
				$shortcode .= " roles='{$role}'";
			endif;

			echo do_shortcode( $shortcode . ']' ); ?>

        </div><!-- .section__content -->
		<?php

		// Remove Author Avatars filters
		remove_filter( 'grimlock_author_avatars_userlist_class', array( $this, 'change_author_avatars_userlist_class'    ) );
		remove_filter( 'grimlock_author_avatars_user_class',     array( $this, 'change_author_avatars_user_class'        ) );
		remove_filter( 'aa_userlist_template',                   array( $this, 'change_author_avatars_userlist_template' ), 20 );
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) :
			?>
			<<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>
			<div class="region__inner" <?php $this->render_inner_style(); ?>>
				<div class="region__container">
					<div class="region__row">
						<div class="region__col">
							<?php
							$this->render_header();
							$this->render_content();
							$this->render_footer(); ?>
						</div><!-- .region__col -->
					</div><!-- .region__row -->
				</div><!-- .region__container -->
			</div><!-- .region__inner -->
			</<?php $this->render_el(); ?>><!-- .grimlock-section -->
			<?php
		endif;
	}
}
