
<?php do_action( 'bp_before_member_' . bp_current_action() . '_content' ); ?>

<div id="members-dir-list" class="dir-list members follow <?php echo bp_current_action(); ?>" data-bp-list="members">

	<h2 class="text-capitalize h3 pt-1 pb-0 mb-0"><?php echo bp_current_action(); ?></h2>

	<?php if ( function_exists( 'bp_nouveau' ) ) : ?>

		<div id="bp-ajax-loader"><?php bp_nouveau_user_feedback( 'generic-loading' ); ?></div>

	<?php else : ?>

		<?php bp_get_template_part( 'members/members-loop' ) ?>

	<?php endif; ?>

</div>

<?php do_action( 'bp_after_member_' . bp_current_action() . '_content' ); ?>
