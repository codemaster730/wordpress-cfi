<?php
	/**
	 * **************************************
	 * Main.php
	 *
	 * The main template file, that loads the header, footer and sidebar
	 * apart from loading the appropriate rtMedia template
	 *
	 * @package RTMedia
	 * ***************************************
	 */

	// by default it is not an ajax request
	global $rt_ajax_request;
	$rt_ajax_request = false;

	// check if it is an ajax request
	$_rt_ajax_request = rtm_get_server_var( 'HTTP_X_REQUESTED_WITH', 'FILTER_SANITIZE_STRING' );
	if ( 'xmlhttprequest' === strtolower( $_rt_ajax_request ) ) {
		$rt_ajax_request = true;
	}
?>
<div id="buddypress" class="<?php if ( class_exists( 'Mp_BP_Match' ) && ! bp_is_my_profile() ) : ?>bp-match-displayed<?php endif; ?> <?php if ( function_exists( 'bp_follow_init' ) ) : ?>bp-follow-displayed<?php endif; ?>">
<?php
// if it's not an ajax request, load headers
if ( ! $rt_ajax_request ) {
	// if this is a BuddyPress page, set template type to
	// buddypress to load appropriate headers
	if ( class_exists( 'BuddyPress' ) && ! bp_is_blog_page() && apply_filters( 'rtm_main_template_buddypress_enable', true ) ) {
		$template_type = 'buddypress';
	} else {
		$template_type = '';
	}

if ( 'buddypress' === $template_type ) {
	// load buddypress markup
if ( bp_displayed_user_id() ) {

	// if it is a buddypress member profile
	?>
	<?php do_action( 'bp_before_member_home_content' ); ?>

	<div id="item-header" role="complementary">
		<?php bp_get_template_part( 'members/single/cover-image-header' ); ?>
	</div> <!--#item-header-->

	<div class="profile-content">

	<div id="profile-content__nav" class="item-list-tabs no-ajax"  aria-label="<?php esc_attr_e( 'Member primary navigation', 'buddypress' ); ?>" role="navigation">
		<div class="container container--medium">
			<div class="row">
				<div class="profile-content__nav-wrapper col-12">
					<ul class="main-nav priority-ul clearfix d-md-inline-block">
						<?php bp_get_displayed_user_nav(); ?>
						<?php do_action( 'bp_member_options_nav' ); ?>
					</ul> <!-- .main-nav -->
					<ul class="main-nav settings-nav d-none d-md-inline-block">
						<?php grimlock_buddypress_get_displayed_user_secondary_nav(); ?>
					</ul> <!-- .main-nav -->
				</div> <!-- .profile-content__nav-wrapper -->
			</div>
		</div> <!-- .container -->
	</div> <!-- #profile-content__nav -->

	<div id="item-body" class="profile-content__body" role="main">
	<div class="container container--medium">
	<div class="row">
	<div class="col-md-12 col-lg-8 col-xl-9">

	<?php do_action( 'bp_before_member_body' ); ?>
	<?php do_action( 'bp_before_member_media' ); ?>

	<div id="subnav" role="navigation" class="d-flex flex-column flex-lg-row mb-4 mt-0">
		<div class="item-list-tabs primary-list-tabs no-ajax">
			<ul class="item-list-tabs-ul clearfix">
				<?php rtmedia_sub_nav(); ?>
				<?php do_action( 'rtmedia_sub_nav' ); ?>
			</ul>
		</div>
	</div>

	<?php
		} elseif ( bp_is_group() ) {

		// not a member profile, but a group
	?>

	<?php if ( bp_has_groups() ) : while ( bp_groups() ) :
		bp_the_group(); ?>

	<?php do_action( 'bp_before_group_home_content' ); ?>

	<div id="item-header" role="complementary">
		<?php bp_get_template_part( 'groups/single/cover-image-header' ); ?>
	</div><!--#item-header-->

	<div class="profile-content">

	<div id="profile-content__nav" class="item-list-tabs no-ajax" role="navigation">
		<div class="container container--medium">
			<div class="row">
				<div class="profile-content__nav-wrapper col-12">
					<ul class="clearfix pl-2 pr-2 pl-lg-0 pr-lg-0">
						<?php bp_get_options_nav(); ?>
						<?php do_action( 'bp_group_options_nav' ); ?>
					</ul> <!-- .clearfix -->
				</div> <!-- .profile-content__nav-wrapper -->
			</div>
		</div> <!-- .container -->
	</div> <!-- #profile-content__nav -->

	<div id="item-body" class="profile-content__body" role="main">
	<div class="container container--medium">
	<div class="row">
	<div class="col-md-12 col-lg-8 col-xl-9">

	<?php do_action( 'bp_before_group_body' ); ?>
	<?php do_action( 'bp_before_group_media' ); ?>

	<div id="subnav" role="navigation" class="d-flex flex-column flex-lg-row mb-4 mt-0">
		<div class="item-list-tabs primary-list-tabs no-ajax">
			<ul class="item-list-tabs-ul clearfix">
				<?php rtmedia_sub_nav(); ?>
				<?php do_action( 'rtmedia_sub_nav' ); ?>
			</ul>
		</div>
	</div>
	<?php endwhile;
		endif; // group/profile if/else
		}
		} else { // if BuddyPress
	?>
	<div id="item-body" class="profile-content__body">
	<div class="container container--medium">
	<div class="row">
	<div class="col-md-12 col-lg-8 col-xl-9">
	<?php
}
}   // if ajax
	// include the right rtMedia template
	rtmedia_load_template();

	if ( ! $rt_ajax_request ) {
		if ( function_exists( 'bp_displayed_user_id' ) && 'buddypress' === $template_type && ( bp_displayed_user_id() || bp_is_group() ) ) {
			if ( bp_is_group() ) {
				do_action( 'bp_after_group_media' );
				do_action( 'bp_after_group_body' );
			}
			if ( bp_displayed_user_id() ) {
				do_action( 'bp_after_member_media' );
				do_action( 'bp_after_member_body' );
			}
		}
		?>
		</div> <!-- .col-* -->
		<?php bp_get_template_part( 'bp-sidebar-2' ); ?>
		</div> <!-- .row -->
		</div> <!-- .container -->
		</div><!--#item-body-->
		</div><!--.profile-content-->
		<?php
		if ( function_exists( 'bp_displayed_user_id' ) && 'buddypress' === $template_type && ( bp_displayed_user_id() || bp_is_group() ) ) {
			if ( bp_is_group() ) {
				do_action( 'bp_after_group_home_content' );
			}
			if ( bp_displayed_user_id() ) {
				do_action( 'bp_after_member_home_content' );
			}
		}
	}
	// close all markup
	?>
	</div><!--#buddypress-->

	<!-- Modal admins -->
<?php if ( bp_is_group() ) { bp_get_template_part( 'bp-group-admins' ); }
