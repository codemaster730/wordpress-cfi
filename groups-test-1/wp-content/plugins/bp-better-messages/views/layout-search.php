<?php
defined( 'ABSPATH' ) || exit;
$search = sanitize_text_field( $_GET['search'] );
$stacks = BP_Better_Messages()->functions->get_search_stacks($search);
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

                <div class="bpbm-search">
                    <form>
                        <input title="<?php _e( 'Search', 'bp-better-messages' ); ?>" type="text" name="search" value="<?php esc_attr_e($search); ?>">
                        <span class="close"><i class="fas fa-times" aria-hidden="true"></i></span>
                    </form>
                    <a style="display: none" href="#" class="search" title="<?php _e( 'Search', 'bp-better-messages' ); ?>"><i class="fas fa-search" aria-hidden="true"></i></a>
                </div>

                <a href="#" class="mobileClose"><i class="fas fa-window-close"></i></a>
            </div>

            <?php if ( !empty( $stacks ) ) { ?>
                <div class="scroller scrollbar-inner search">
                    <div class="list">
                        <?php foreach ( $stacks as $stack ) {
                            echo BP_Better_Messages()->functions->render_stack( $stack );
                        } ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="empty bpbm-search-empty">
                    <p class="bpbm-empty-icon"><i class="fas fa-search"></i></p>
                    <p class="bpbm-empty-message"><?php _e( 'No messages found', 'bp-better-messages' ); ?></p>
                </div>
            <?php } ?>

            <div class="preloader"></div>
            <?php if( BP_Better_Messages()->settings['disableTapToOpen'] === '0' ){ ?>
                <div class="bp-messages-mobile-tap"><?php _e( 'Tap to open messages', 'bp-better-messages' ); ?></div>
            <?php } ?>
        </div>
    </div>
</div>