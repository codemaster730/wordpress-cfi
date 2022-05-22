<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Grimlock_Site_Identity_Component
 *
 * @author  themosaurus
 * @since   1.0.0
 * @package grimlock/inc/components
 */
class Grimlock_Site_Identity_Component extends Grimlock_Component {
	/**
	 * Create a new Grimlock_Component instance.
	 *
	 * @param array $props Array of variables to be used within template.
	 */
	public function __construct( $props = array() ) {
	    parent::__construct( wp_parse_args( $props, array(
	        'custom_logo'               => '',
		    'custom_logo_displayed'     => false,
		    'blogname_displayed'        => true,
		    'blogdescription_displayed' => true,
	    ) ) );
	}

	/**
	 * Render the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
            <div <?php $this->render_class( array( 'site-branding' ) ); ?>>
				<?php
				if ( true == $this->props['custom_logo_displayed'] ) : ?>
                    <div class="grimlock-site-logo grimlock-navbar-brand__logo site-logo navbar-brand__logo">
                        <?php echo $this->props['custom_logo']; ?>
                    </div><!-- navbar-brand__logo -->
					<?php
				endif;

				if ( true == $this->props['blogname_displayed'] ) : ?>
                    <div class="grimlock-site-title grimlock-navbar-brand__title site-title navbar-brand__title">
                        <a class="grimlock-site-title-link grimlock-navbar-brand__title-link site-title-link navbar-brand__title-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
                    </div><!-- navbar-brand__title -->
					<?php
				endif;

				if ( true == $this->props['blogdescription_displayed'] ) : ?>
                    <small class="grimlock-site-description grimlock-navbar-brand__tagline site-description navbar-brand__tagline">
						<?php bloginfo( 'description' ); ?>
                    </small><!-- navbar-brand__tagline -->
					<?php
				endif; ?>
            </div><!-- .site-branding -->
		<?php endif;
	}
}
