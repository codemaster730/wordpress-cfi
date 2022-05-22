<?php if( $has_chat_footer ) { ob_start(); ?>
    <div class="chat-footer">
        <?php if($has_new_button) { ?>
            <a href="<?php echo add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link( get_current_user_id() ) ); ?>&scrollToContainer" class="new-message ajax" title="<?php _e( 'New Thread', 'bp-better-messages' ); ?>"><i class="far fa-edit" aria-hidden="true"></i></a>
        <?php } ?>
        <?php echo $me; ?>
    </div>
    <?php $chat_footer = ob_get_clean();
}

?>
<div class="bp-messages-wrap bp-better-messages-list <?php BP_Better_Messages()->functions->messages_classes(); ?>">
    <div class="tabs">
        <?php foreach( $tabs as $key => $value ){
            switch ($key){
                case 'messages':
                    echo '<div data-tab="messages"><span class="unread-count" style="display:none"></span><i class="fas fa-comments"></i> ' . __('Messages', 'bp-better-messages') . '</div>';
                    break;
                case 'friends':
                    echo '<div data-tab="friends"><i class="fas fa-user-friends"></i> ' . __('Friends', 'bp-better-messages') . '</div>';
                    break;
                case 'groups':
                    echo '<div data-tab="bpbm-groups"><i class="fas fa-users"></i> ' . __('Groups', 'bp-better-messages') . '</div>';
                    break;
                default:
                    do_action( 'bp_better_messages_mini_tabs_head', $key );
                    break;
            }
        } ?>
        <?php if( BP_Better_Messages()->settings['enableMiniCloseButton'] === '1' ){ ?>
        <div data-tab="bpbm-close" title="<?php _e('Close', 'bp-better-messages'); ?>"><i class="fas fa-times"></i></div>
        <?php } ?>
    </div>
    <div class="tabs-content">
    <?php if(in_array('messages', $tabs)){
        $threads = BP_Better_Messages()->functions->get_threads( $user_id );
        ?>
        <div class="messages<?php if( ! $has_chat_footer ) echo ' no-chat-footer'; ?>">

            <?php if ( !empty( $threads ) ) { ?>
                <div class="scroller scrollbar-inner threads-list-wrapper">
                    <div class="threads-list">
                        <?php foreach ( $threads as $thread ) {
                            echo $thread->html;
                        } ?>
                        <div class="loading-messages">
                            <div class="bounce1"></div>
                            <div class="bounce2"></div>
                            <div class="bounce3"></div>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="threads-list">
                    <p class="empty">
                        <?php _e( 'Nothing found', 'bp-better-messages' ); ?>
                    </p>
                </div>
            <?php } ?>
            <?php echo $chat_footer; ?>
        </div>
    <?php } ?>
    <?php if(in_array('friends', $tabs)){ ?>
        <div class="friends">
            <div class="scroller scrollbar-inner">
                <div class="bpbm-search-in-list">
                    <input title="<?php _e('Search...', 'bp-better-messages'); ?>" type="text" name="bpbm-search" value="" placeholder="<?php _e('Search...', 'bp-better-messages'); ?>">
                </div>
                <div class="bp-messages-user-list">
                    <div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div>
                </div>
            </div>
            <?php echo $chat_footer; ?>
        </div>
    <?php } ?>
    <?php if(in_array('groups', $tabs)){ ?>
        <div class="bpbm-groups">
            <div class="scroller scrollbar-inner">
                <div class="bpbm-search-in-list">
                    <input title="<?php _e('Search...', 'bp-better-messages'); ?>" type="text" name="bpbm-search" value="" placeholder="<?php _e('Search...', 'bp-better-messages'); ?>">
                </div>
                <div class="bp-messages-group-list">
                    <div class="bpbm-loader-icon"><i class="fas fa-spinner fa-spin"></i></div>
                    <?php /*foreach($groups['groups'] as $group_id){
                        $group = new BP_Groups_Group( (int) $group_id );
                        if( $group->id === 0 ) continue;

                        echo BP_Better_Messages()->functions->render_group( $group );
                    }*/ ?>
                </div>
            </div>

            <?php echo $chat_footer; ?>
        </div>
    <?php } ?>

    <?php do_action('bp_better_messages_mini_tabs_content', $chat_footer); ?>
    </div>
</div>