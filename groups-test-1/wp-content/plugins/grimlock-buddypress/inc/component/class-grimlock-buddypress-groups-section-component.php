<?php
/**
 * Grimlock_BuddyPress_Groups_Section_Component Class
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
class Grimlock_BuddyPress_Groups_Section_Component extends Grimlock_Section_Component {
	/**
	 * Setup class.
	 *
	 * @param array $props
	 * @since 1.0.0
	 */
	public function __construct( $props = array() ) {
		parent::__construct( wp_parse_args( $props, array(
			'title'         => esc_html__( 'Groups', 'grimlock-buddypress' ),
			'max_groups'    => 5,
			'group_default' => 'popular',
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
		$classes[] = 'grimlock-buddypress-groups-section';
		return array_unique( $classes );
	}

	/**
	 * Display the current component content.
	 *
	 * @since 1.0.0
	 */
	protected function render_content() {
		?>
	<div class="section__content section__content--<?php echo $this->props['layout']; ?> section__content--<?php echo $this->props['groups_layout']; ?>">
		<?php
		if ( function_exists( 'buddypress' ) ) :
			global $groups_template;

			/**
			 * Filters the user ID to use with the widget instance.
			 *
			 * @since 1.5.0
			 *
			 * @param string $value Empty user ID.
			 */
			$user_id = apply_filters( 'bp_group_widget_user_id', '0' );

			/**
			 * Filters the separator of the group widget links.
			 *
			 * @since 2.4.0
			 *
			 * @param string $separator Separator string. Default '|'.
			 */
			$separator = apply_filters( 'bp_groups_widget_separator', '|' );

			$group_args = array(
				'user_id'  => $user_id,
				'type'     => $this->props['group_default'],
				'per_page' => $this->props['max_groups'],
				'max'      => $this->props['max_groups'],
			);

			// Back up the global.
			$old_groups_template = $groups_template; ?>

			<?php if( bp_is_active('groups') ): ?>

                <?php if ( bp_has_groups( $group_args ) ) : ?>
                    <div class="item-options" id="groups-list-options">
                        <a href="<?php bp_groups_directory_permalink(); ?>" id="newest-groups"<?php if ( $this->props['group_default'] == 'newest' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( 'Newest', 'grimlock-buddypress' ); ?></a>
                        <span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
                        <a href="<?php bp_groups_directory_permalink(); ?>" id="recently-active-groups"<?php if ( $this->props['group_default'] == 'active' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( 'Active', 'grimlock-buddypress' ); ?></a>
                        <span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
                        <a href="<?php bp_groups_directory_permalink(); ?>" id="popular-groups" <?php if ( $this->props['group_default'] == 'popular' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( 'Popular', 'grimlock-buddypress' ); ?></a>
                        <span class="bp-separator" role="separator"><?php echo esc_html( $separator ); ?></span>
                        <a href="<?php bp_groups_directory_permalink(); ?>" id="alphabetical-groups" <?php if ( $this->props['group_default'] == 'alphabetical' ) : ?> class="selected"<?php endif; ?>><?php esc_html_e( 'Alphabetical', 'grimlock-buddypress' ); ?></a>
                    </div>

                    <ul id="groups-list" class="item-list" aria-live="polite" aria-relevant="all" aria-atomic="true">
                        <?php
                        while ( bp_groups() ) : bp_the_group(); ?>
                            <li <?php bp_group_class(); ?>>
						        <div class="item-avatar">
						            <a href="<?php bp_group_permalink(); ?>" class="bp-tooltip" data-bp-tooltip="<?php bp_group_name(); ?>"><?php bp_group_avatar(); ?></a>
						        </div>

                                <div class="item">
                                    <div class="item-title"><?php bp_group_link(); ?></div>
                                    <div class="item-meta">
                                    <span class="activity">
                                    <?php
                                    if ( 'newest' == $this->props['group_default'] ) :
                                        printf( __( 'created %s', 'buddypress' ), bp_get_group_date_created() );
                                    elseif ( 'popular' == $this->props['group_default'] ) :
                                        bp_group_member_count();
                                    else :
                                        printf( __( 'active %s', 'buddypress' ), bp_get_group_last_active() );
                                    endif; ?>
                                    </span>
                                    </div>
                                </div>
                            </li>

                            <?php
                        endwhile; ?>
                    </ul>
                    <?php wp_nonce_field( 'groups_widget_groups_list', '_wpnonce-groups' ); ?>
                    <input type="hidden" name="groups_widget_max" id="groups_widget_max" value="<?php echo esc_attr( $this->props['max_groups'] ); ?>" />

                <?php else : ?>
                    <div class="widget-error">
                        <?php esc_html_e( 'There are no groups to display.', 'grimlock-buddypress' ); ?>
                    </div>
				<?php endif; ?>

		    <?php else : ?>
                <div class="widget-error">
                    <?php esc_html_e( 'You must activate "User Groups component" in the BuddyPress settings page', 'grimlock-buddypress' ); ?>
                </div>
            <?php endif;
			$groups_template = $old_groups_template; ?>
			</div><!-- .section__content -->
			<?php
		endif;
	}

	/**
	 * Display the current component with props data on page.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( $this->is_displayed() ) : ?>
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
