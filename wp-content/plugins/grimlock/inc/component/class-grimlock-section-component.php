<?php
/**
 * Grimlock_Section_Component Class
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
class Grimlock_Section_Component extends Grimlock_Region_Component {
	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'title_displayed'           => true,
            'title'                     => '',
			'title_format'              => 'display-4',
			'subtitle_displayed'        => true,
            'subtitle'                  => '',
			'subtitle_format'           => 'lead',
			'text_displayed'            => true,
            'text'                      => '',
			'text_wpautoped'            => true,
			'button_displayed'          => false,
            'button_text'               => '',
            'button_link'               => '#',
            'button_target_blank'       => false,
			'button_format'             => 'btn-primary',
			'button_size'               => 'btn-lg',
			'button_extra_displayed'    => false,
			'button_extra_text'         => '',
			'button_extra_link'         => '#',
			'button_extra_target_blank' => false,
			'button_extra_format'       => 'btn-secondary',
			'button_extra_size'         => 'btn-lg',
			'margin_top'                => 0, // %
			'margin_bottom'             => 0, // %
			'padding_top'               => GRIMLOCK_SECTION_PADDING_Y, // %
			'padding_bottom'            => GRIMLOCK_SECTION_PADDING_Y, // %
            'thumbnail'                 => '',
			'thumbnail_alt'             => '',
			'thumbnail_caption'         => '',
			'layout'                    => '12-cols-left',
			'container_layout'          => 'classic',
			'class'                     => '',
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
		$classes[] = 'grimlock-section';
		$classes[] = 'section';
		return array_unique( $classes );
	}

	/**
	 * Get inline styles as property-values pairs for the component using props.
	 *
	 * @return array The styles for the component as property-values pairs
	 */
	public function get_style() {
		return array_merge( $this->get_css_vars(), parent::get_style() );
	}

	/**
	 * Get css vars as property-values pairs for the component using props.
	 *
	 * @return array The css vars for the component using props.
	 */
	protected function get_css_vars() {
		$css_vars = array();

		if ( ! empty( $this->props['content_background_color'] ) ) {
			$css_vars['--grimlock-section-content-background-color'] = $this->props['content_background_color'];
		}

		return $css_vars;
	}

	/**
	 * Display the section title using title prop.
	 *
	 * @since 1.0.0
	 *
	 * @param string $el
	 */
	protected function render_title( $el = 'h2' ) {
		if ( true == $this->props['title_displayed'] ) :
			// TODO: Consider creating an additional prop called `title_styles`.
			$styles = array();
			if ( ! empty( $this->props['title_color'] ) ) :
				$styles['color'] = esc_attr( $this->props['title_color'] );
			endif;

			$classes = array( 'grimlock-section__title', 'section__title' );
			if ( ! empty( $this->props['title_format'] ) ) :
                $classes[] = "grimlock-{$this->props['title_format']}";
                $classes[] = $this->props['title_format'];
			endif; ?>

            <<?php echo $el; ?> <?php $this->output_class( $classes ); ?> <?php $this->output_inline_style( $styles ); ?>>
				<?php $this->output_title( $this->props['title'] ); ?>
            </<?php echo $el; ?>>
			<?php
		endif;
	}

	/**
	 * Output the escaped text for the title.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title The title to display
	 */
	public function output_title( $title ) {
		if ( ! empty( $title ) ) {
			echo wp_kses( $title, array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'em'     => array( 'class' => array() ),
				'i'      => array( 'class' => array() ),
				'b'      => array( 'class' => array() ),
				'strong' => array( 'class' => array() ),
				'ins'    => array( 'class' => array() ),
				'del'    => array( 'class' => array() ),
				'sup'    => array( 'class' => array() ),
				'sub'    => array( 'class' => array() ),
				'a'      => array( 'class' => array(), 'href'  => array(), 'title' => array() ),
			) );
		}
	}

	/**
	 * Display the section subtitle using subtitle prop.
	 *
	 * @since 1.0.0
	 *
	 * @param string $el
	 */
	protected function render_subtitle( $el = 'h3' ) {
		if ( true == $this->props['subtitle_displayed'] ) :
			// TODO: Consider creating an additional prop called `subtitle_styles`.
			$styles = array();
			if ( ! empty( $this->props['subtitle_color'] ) ) :
				$styles['color'] = esc_attr( $this->props['subtitle_color'] );
			endif;

			$classes = array( 'grimlock-section__subtitle', 'section__subtitle' );
			if ( ! empty( $this->props['subtitle_format'] ) ) :
				$classes[] = "grimlock-{$this->props['subtitle_format']}";
				$classes[] = $this->props['subtitle_format'];
			endif;
			
			if ( ! empty( $this->props['subtitle'] ) ) : ?>
				<<?php echo $el; ?> <?php $this->output_class( $classes ); ?> <?php $this->output_inline_style( $styles ); ?>>
					<?php $this->output_title( $this->props['subtitle'] ); ?>
				</<?php echo $el; ?>>
			<?php endif;
		endif;
	}

	/**
	 * Display the section text using title prop.
	 *
	 * @since 1.0.0
	 */
	protected function render_text() {
		if ( true == $this->props['text_displayed'] ) : ?>
            <div class="grimlock-section__text section__text">
				<?php
				$text = do_shortcode( wp_kses_post( $this->props['text'] ) );
				echo true == $this->props['text_wpautoped'] ? wpautop( $text ) : $text; ?>
            </div>
			<?php
		endif;
	}

	/**
	 * Display the section button using props.
	 *
	 * @since 1.0.0
	 */
	protected function render_button() {
		if ( true == $this->props['button_displayed'] ) :
			$classes = array(
				'grimlock-section__btn',
				'grimlock-section__btn--1',
			    'section__btn',
                'btn',
            );

		    if ( ! empty( $this->props['button_format'] ) ) :
				$classes[] = "grimlock-{$this->props['button_format']}";
				$classes[] = $this->props['button_format'];
			endif;

			if ( ! empty( $this->props['button_size'] ) ) :
				$classes[] = "grimlock-{$this->props['button_size']}";
				$classes[] = $this->props['button_size'];
			endif; ?>

            <a href="<?php echo esc_url( $this->props['button_link'] ); ?>" <?php $this->output_class( $classes ); ?> <?php echo true == $this->props['button_target_blank'] ? 'target="_blank"' : '' ?>>
	            <?php echo wp_kses( $this->props['button_text'], array(
		            'i'    => array( 'class' => array() ),
		            'span' => array( 'class' => array() ),
	            ) ); ?>
            </a>
			<?php
		endif;
	}

	/**
	 * Display the section extra button using props.
	 *
	 * @since 1.0.0
	 */
	protected function render_button_extra() {
		if ( true == $this->props['button_extra_displayed'] ) :
			$classes = array(
				'grimlock-section__btn',
				'grimlock-section__btn--2',
			    'section__btn',
                'btn',
            );

		    if ( ! empty( $this->props['button_extra_format'] ) ) :
				$classes[] = "grimlock-{$this->props['button_extra_format']}";
				$classes[] = $this->props['button_extra_format'];
			endif;

			if ( ! empty( $this->props['button_extra_size'] ) ) :
				$classes[] = "grimlock-{$this->props['button_extra_size']}";
				$classes[] = $this->props['button_extra_size'];
			endif; ?>

            <a href="<?php echo esc_url( $this->props['button_extra_link'] ); ?>" <?php $this->output_class( $classes ); ?> <?php echo true == $this->props['button_extra_target_blank'] ? 'target="_blank"' : '' ?>>
	            <?php echo wp_kses( $this->props['button_extra_text'], array(
		            'i'    => array( 'class' => array() ),
		            'span' => array( 'class' => array() ),
	            ) ); ?>
            </a>
			<?php
		endif;
	}

	/**
	 * Display the section featured image using `thumbnail` prop.
	 *
	 * @since 1.0.0
	 */
	protected function render_thumbnail() {
		if ( ! empty( $this->props['thumbnail'] ) ) :
			$attachment_id = attachment_url_to_postid( $this->props['thumbnail'] ); ?>
			<div class="grimlock-section__thumbnail section__thumbnail">
				<?php if ( ! empty( $attachment_id ) ) :
					echo wp_get_attachment_image( $attachment_id, false, false, array( 'src' => esc_url( $this->props['thumbnail'] ), 'class' => 'grimlock-section__thumbnail-img section__thumbnail-img img-fluid' ) );
				else : ?>
					<img class="grimlock-section__thumbnail-img section__thumbnail-img img-fluid" src="<?php echo esc_url( $this->props['thumbnail'] ); ?>" alt="<?php echo esc_attr( $this->props['thumbnail_alt'] ); ?>" />
				<?php endif; ?>

				<?php if ( ! empty( $this->props['thumbnail_caption'] ) ) : ?>
					<div class="grimlock-section__thumbnail-caption section__thumbnail-caption"><?php echo $this->props['thumbnail_caption']; ?></div>
				<?php endif; ?>
            </div><!-- .section__thumbnail -->
			<?php
		endif;
	}

	/**
	 * Display the current component header.
	 *
	 * @since 1.0.0
	 */
	protected function render_header() {
		if ( $this->has_header() ) : ?>
	        <div class="grimlock-section__header section__header">
				<?php
				$this->render_title();
				$this->render_subtitle(); ?>
	        </div><!-- .section__header -->
			<?php
		endif;
	}

	/**
	 * Check whether the header has to be displayed.
	 *
	 * @since 1.0.7
	 *
	 * @return bool True when the component header, false otherwise.
	 */
	protected function has_header() {
		return true == $this->props['title_displayed'] || true == $this->props['subtitle_displayed'];
	}

	/**
	 * Display the current component content.
	 *
	 * @since 1.0.0
	 */
	protected function render_content() {
		if ( $this->has_content() ) : ?>
	        <div class="grimlock-section__content section__content">
				<?php $this->render_text(); ?>
	        </div><!-- .section__content -->
			<?php
		endif;
	}

	/**
	 * Check whether the content has to be displayed.
	 *
	 * @since 1.0.7
	 *
	 * @return bool True when the component content, false otherwise.
	 */
	protected function has_content() {
		return true == $this->props['text_displayed'];
	}

	/**
	 * Display the current component footer.
	 *
	 * @since 1.0.0
	 */
	protected function render_footer() {
		if ( $this->has_footer() ) : ?>
	        <div class="grimlock-section__footer section__footer">
	            <?php $this->render_button(); ?>
	            <?php $this->render_button_extra(); ?>
	        </div><!-- .section__footer -->
			<?php
		endif;
    }

	/**
	 * Check whether the header has to be displayed.
	 *
	 * @since 1.0.7
	 *
	 * @return bool True when the component header, false otherwise.
	 */
	protected function has_footer() {
		return true == $this->props['button_displayed'];
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
            <<?php $this->render_el(); ?> <?php $this->render_id(); ?> <?php $this->render_class(); ?> <?php $this->render_style(); ?> <?php $this->render_role(); ?> <?php $this->render_data_attributes(); ?>>
            <div class="grimlock-region__inner region__inner" <?php $this->render_inner_style(); ?>>
                <div class="grimlock-region__container region__container">
                    <div class="grimlock-region__row region__row">
                        <div class="grimlock-region__col grimlock-region__col--1 region__col region__col--1">
							<?php $this->render_thumbnail(); ?>
                        </div><!-- .region__col -->
                        <div class="grimlock-region__col grimlock-region__col--2 region__col region__col--2">
							<?php
							$this->render_header();
							$this->render_content();
							$this->render_footer(); ?>
                        </div><!-- .region__col -->
                    </div><!-- .region__row -->
                </div><!-- .region__container -->
            </div><!-- .region__inner -->
            </<?php $this->render_el(); ?>><!-- .grimlock-section -->
		<?php endif;
	}
}
