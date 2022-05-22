<?php
defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();

$admin_mode = false;
if( current_user_can( 'manage_options' )){
    $admin_mode = true;
}

if( $admin_mode && ! isset( $_REQUEST['mobileFullScreen'] ) ) {
    if ( bp_displayed_user_id() !== 0 ) {
        $user_id = bp_displayed_user_id();
    }
}

$side_threads = (BP_Better_Messages()->settings['combinedView'] === '1');

$threads = BP_Better_Messages()->functions->get_threads( $user_id );

ob_start();
foreach ($threads as $thread) {
    echo $thread->html;
}
$threads_html = ob_get_clean();
?>
<div class="bp-messages-wrap bp-messages-wrap-main <?php BP_Better_Messages()->functions->messages_classes(); ?>">
    <div class="bp-messages-threads-wrapper threads-hidden <?php if( empty($threads_html) ) echo 'no-threads'; ?>">
    <?php if( $side_threads ) {
        if( ! isset( $_REQUEST['ignore_threads'] ) ) { ?>
            <div class="bp-messages-side-threads">
                <div class="chat-header side-header">
                    <?php
                    if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) {
                        echo '<a href="' . add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link() ) . '" class="new-message ajax" title="'. __( 'New Thread', 'bp-better-messages' ) . '"><i class="far fa-edit" aria-hidden="true"></i></a>';
                    }

                    if( BP_Better_Messages()->settings['disableSearch'] === '0' ) { ?>
                        <div class="bpbm-search">
                            <form>
                                <input title="<?php _e('Search', 'bp-better-messages'); ?>" placeholder="<?php _e('Search...', 'bp-better-messages'); ?>" type="text" name="search" value="">
                                <span class="close"><i class="fas fa-times" aria-hidden="true"></i></span>
                            </form>
                        </div>
                    <?php } ?>
                </div>
                <div class="scroller scrollbar-inner threads-list-wrapper">
                    <div class="bpbm-search-results"></div>
                    <div class="threads-list">
                        <?php
                        if ( ! empty( $threads ) ) {
                            echo $threads_html;
                        } ?>
                        <div class="loading-messages">
                            <div class="bounce1"></div>
                            <div class="bounce2"></div>
                            <div class="bounce3"></div>
                        </div>
                    </div>
                </div>
                <?php BP_Better_Messages()->functions->render_footer(); ?>
            </div>
            <?php
        } else {
            echo '<div class="bp-messages-side-threads"></div>';
        }
        ?>
        <div class="bp-messages-column">
            <div class="chat-header bpbm-index">
                <?php
                if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) {
                    echo '<a href="' . add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link() ) . '" class="new-message ajax" title="'. __( 'New Thread', 'bp-better-messages' ) . '"><i class="far fa-edit" aria-hidden="true"></i></a>';
                }

                if( BP_Better_Messages()->settings['disableFavoriteMessages'] === '0' ) {
                    $favorited = BP_Better_Messages()->functions->get_starred_count();
                    echo '<a href="' . add_query_arg( 'starred', '', BP_Better_Messages()->functions->get_link() ) . '" class="starred-messages ajax" title="'. __( 'Starred', 'bp-better-messages' ) . '"><i class="fas fa-star" aria-hidden="true"></i> ' . $favorited . '</a>';
                }

                if( BP_Better_Messages()->settings['disableSearch'] === '0' ) { ?>
                    <div class="bpbm-search">
                        <form style="display: none">
                            <input title="<?php _e( 'Search', 'bp-better-messages' ); ?>" type="text" name="search" value="">
                            <span class="close"><i class="fas fa-times" aria-hidden="true"></i></span>
                        </form>
                        <a href="#" class="search" title="<?php _e( 'Search', 'bp-better-messages' ); ?>"><i class="fas fa-search" aria-hidden="true"></i></a>
                    </div>
                <?php } ?>
                <a href="#" class="mobileClose"><i class="fas fa-window-close"></i></a>
            </div>
            <?php if ( ! empty( $threads ) ) { ?>
            <div class="scroller scrollbar-inner threads-list-wrapper threads-list-index">
                <div class="threads-list">
                    <?php echo $threads_html; ?>
                    <div class="loading-messages">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                </div>
            </div>
            <?php } else { ?>
            <div class="scroller scrollbar-inner threads-list-wrapper threads-list-index">
            <div class="threads-list-index threads-list empty">
                <div class="empty">
                    <p class="bpbm-empty-icon"><i class="far fa-comments"></i></p>
                    <p class="bpbm-empty-message"><?php _e( 'No messages yet!', 'bp-better-messages' ); ?></p>
                    <?php if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) { ?>
                        <p class="bpbm-empty-link"><a class="ajax" href="<?php echo add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link() ); ?>"><?php _e('Start new conversation', 'bp-better-messages'); ?></a></p>
                    <?php } ?>
                </div>
            </div>
            </div>
            <?php } ?>
            <div class="thread-not-selected empty">
                <div class="empty">
                    <p class="bpbm-empty-icon"><i class="far fa-comments"></i></p>
                    <p class="bpbm-empty-message"><?php _e( 'Select thread to display messages', 'bp-better-messages' ); ?></p>
                    <?php if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) { ?>
                        <p class="bpbm-empty-or"><?php _e( 'or', 'bp-better-messages' ); ?></p>
                        <p class="bpbm-empty-link"><a class="ajax" href="<?php echo add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link() ); ?>"><?php _e('Start new conversation', 'bp-better-messages'); ?></a></p>
                    <?php } ?>
                </div>
            </div>
            <?php BP_Better_Messages()->functions->render_footer(); BP_Better_Messages()->functions->render_preloader(); ?>
        </div>
    </div>
    <?php

    echo '</div>';
    } else {  ?>
    <div class="bp-messages-column">
    <div class="chat-header">
        <?php
        if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) {
            echo '<a href="' . add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link() ) . '" class="new-message ajax" title="'. __( 'New Thread', 'bp-better-messages' ) . '"><i class="far fa-edit" aria-hidden="true"></i></a>';
        }

        if( BP_Better_Messages()->settings['disableFavoriteMessages'] === '0' ) {
        $favorited = BP_Better_Messages()->functions->get_starred_count();
        echo '<a href="' . add_query_arg( 'starred', '', BP_Better_Messages()->functions->get_link() ) . '" class="starred-messages ajax" title="'. __( 'Starred', 'bp-better-messages' ) . '"><i class="fas fa-star" aria-hidden="true"></i> ' . $favorited . '</a>';
        }

        if( BP_Better_Messages()->settings['disableSearch'] === '0' ) { ?>
        <div class="bpbm-search">
            <form style="display: none">
                <input title="<?php _e( 'Search', 'bp-better-messages' ); ?>" type="text" name="search" value="">
                <span class="close"><i class="fas fa-times" aria-hidden="true"></i></span>
            </form>
            <a href="#" class="search" title="<?php _e( 'Search', 'bp-better-messages' ); ?>"><i class="fas fa-search" aria-hidden="true"></i></a>
        </div>
        <?php } ?>
        <a href="#" class="mobileClose"><i class="fas fa-window-close"></i></a>
    </div>
    <?php if ( ! empty( $threads ) ) { ?>
        <div class="scroller scrollbar-inner threads-list-wrapper">
            <div class="threads-list">
                <?php echo $threads_html; ?>
                <div class="loading-messages">
                    <div class="bounce1"></div>
                    <div class="bounce2"></div>
                    <div class="bounce3"></div>
                </div>
            </div>
        </div>
    <?php } else { ?>
    <div class="scroller scrollbar-inner threads-list-wrapper">
        <div class="threads-list empty">
            <div class="empty">
                <p class="bpbm-empty-icon"><i class="far fa-comments"></i></p>
                <p class="bpbm-empty-message"><?php _e( 'No messages yet!', 'bp-better-messages' ); ?></p>
                <?php if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) { ?>
                <p class="bpbm-empty-link"><a class="ajax" href="<?php echo add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link() ); ?>"><?php _e('Start new conversation', 'bp-better-messages'); ?></a></p>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php }
    BP_Better_Messages()->functions->render_footer();
    BP_Better_Messages()->functions->render_preloader();
    echo '</div></div></div>';
}