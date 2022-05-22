<?php
defined( 'ABSPATH' ) || exit;
$stacks = BP_Better_Messages()->functions->get_starred_stacks();
?>
<div class="bp-messages-wrap bp-messages-wrap-main <?php BP_Better_Messages()->functions->messages_classes(); ?>">

    <div class="bp-messages-threads-wrapper threads-hidden">
        <?php $side_threads = (BP_Better_Messages()->settings['combinedView'] === '1');
        if( $side_threads) {
            BP_Better_Messages()->functions->render_side_column( get_current_user_id() );
        } ?>
        <div class="bp-messages-column">
            <div class="chat-header">
                <a href="<?php echo BP_Better_Messages()->functions->get_link(); ?>" class="back ajax" title="<?php _e( 'Back', 'bp-better-messages' ); ?>"><i class="fas fa-chevron-left" aria-hidden="true"></i></a>
                <a href="#" class="mobileClose"><i class="fas fa-window-close"></i></a>
            </div>

            <?php if ( !empty( $stacks ) ) { ?>
                <div class="scroller scrollbar-inner starred">
                    <div class="list">
                        <?php foreach ( $stacks as $stack ) {
                            echo BP_Better_Messages()->functions->render_stack( $stack );
                        } ?>
                    </div>
                </div>
            <?php } else { ?>
                    <div class="empty bpbm-favorite-empty">
                        <p class="bpbm-empty-icon"><i class="far fa-star"></i></p>
                        <p class="bpbm-empty-message"><?php _e( 'No starred messages yet!', 'bp-better-messages' ); ?></p>
                    </div>
            <?php } ?>

            <div class="preloader"></div>
            <?php if( BP_Better_Messages()->settings['disableTapToOpen'] === '0' ){ ?>
                <div class="bp-messages-mobile-tap"><?php _e( 'Tap to open messages', 'bp-better-messages' ); ?></div>
            <?php } ?>
        </div>
    </div>
</div>