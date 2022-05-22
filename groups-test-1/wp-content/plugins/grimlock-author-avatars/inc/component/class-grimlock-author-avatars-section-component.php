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
	 * Display the current component content.
	 *
	 * @since 1.0.0
	 */
	protected function render_content() {
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
