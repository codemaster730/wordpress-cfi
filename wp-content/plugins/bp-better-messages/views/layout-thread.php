<?php
defined( 'ABSPATH' ) || exit;
global $wpdb;
$message_id = false;
if(isset($_GET['message_id'])) $message_id = intval($_GET['message_id']);
$participants = BP_Better_Messages()->functions->get_participants( $thread_id );

if($message_id){
	$stacks = BP_Better_Messages()->functions->get_stacks( $thread_id, $message_id, 'to_message');
} else {
	$stacks = BP_Better_Messages()->functions->get_stacks( $thread_id );
}

$side_threads = (BP_Better_Messages()->settings['combinedView'] === '1');
$user_id = get_current_user_id();
$participants_count = $participants['count'];

$is_participant = in_array( $user_id, $participants['recipients'] );

if( $is_mini ) {
    $side_threads = false;
}

if( $side_threads === true && ! isset( $_REQUEST['ignore_threads'] ) ){
    $threads = BP_Better_Messages()->functions->get_threads( $user_id );
}

$can_moderate = BP_Better_Messages()->functions->is_thread_super_moderator( get_current_user_id(), $thread_id );
?><div class="bp-messages-wrap bp-messages-wrap-main <?php BP_Better_Messages()->functions->messages_classes( $thread_id ); ?>" data-thread-id="<?php esc_attr_e($thread_id); ?>">
    <div class="bp-messages-threads-wrapper threads-hidden <?php if( isset($threads) && count( $threads ) === 0 ) echo 'no-threads'; ?>">
    <?php if( $side_threads ) {
        if( ! isset( $_REQUEST['ignore_threads'] ) ) { ?>
        <div class="bp-messages-side-threads">
            <div class="chat-header side-header">
                <?php
                if( BP_Better_Messages()->settings['disableNewThread'] === '0' || current_user_can('manage_options') ) {
                    echo '<a href="' . add_query_arg( 'new-message', '', BP_Better_Messages()->functions->get_link( get_current_user_id()  ) ) . '" class="new-message ajax" title="'. __( 'New Thread', 'bp-better-messages' ) . '"><i class="far fa-edit" aria-hidden="true"></i></a>';
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
            <?php
            $extra_tabs = BP_Better_Messages()->functions->side_view_extra_tabs();
            echo BP_Better_Messages()->functions->render_extra_tabs( $extra_tabs );
            ?>
            <div class="scroller scrollbar-inner threads-list-wrapper">
                <div class="bpbm-search-results"></div>
                <?php echo BP_Better_Messages()->functions->render_extra_tabs_content( $extra_tabs ); ?>
                <div class="threads-list">
                    <?php
                    if ( !empty( $threads ) ) {
                        foreach ( $threads as $thread ) {
                            echo $thread->html;
                        }
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
    } ?>

    <div class="bp-messages-column">

    <div class="chat-header">
        <?php if( ! $is_mini ){ ?>
            <a href="<?php echo BP_Better_Messages()->functions->get_link(); ?>" class="back ajax" title="<?php _e( 'Back', 'bp-better-messages' ); ?>"><i class="fas fa-chevron-left" aria-hidden="true"></i></a>
        <?php }
        if( $participants_count <= 2) {
            $_user_id = $participants[ 'recipients' ][0];
            $name     = $participants[ 'links' ][0];
            $user     = get_userdata($_user_id);

            if( ! is_object($user ) ){
                $name = '<a href="#" class="bpbm-deleted-user-link">' . __('Deleted User', 'bp-better-messages') . '</a>';
            }

            if($is_mini){
                echo apply_filters('bp_better_messages_mini_chat_username', strip_tags($name), $_user_id, $thread_id);
            } else {
                echo apply_filters('bp_better_messages_full_chat_username', $name, $_user_id, $thread_id);
            }
        } else {
            $subject = BP_Better_Messages()->functions->get_thread_subject($thread_id);
            if( empty($subject ) ){
                if ( $participants_count >= 2 ){
                    $subject = $participants_count . ' ' . __('Participants', 'bp-better-messages');
                }
            }

            $_subject = $subject;
            if( $is_mini ) {
                $_subject = mb_strimwidth($_subject,0, 20, '...');
            }
            echo '<strong title="' . $subject . '">' . $_subject . '</strong>';
        }

        if( ! $is_mini ){
            echo '<a href="#" class="mobileClose"><i class="fas fa-window-close"></i></a>';
        }
        $expandingButtons = apply_filters('bp_better_messages_thread_expanding_buttons', [], $thread_id, $participants, $is_mini, 'thread' );
        if( count($expandingButtons) > 0 ){
            ?>
            <div class="expandingButtons" title="<?php _e('More', 'bp-better-messages'); ?>">
                <i class="fas fa-ellipsis-v"></i>
                <div class="bpbm-dropdown-menu">
                    <?php foreach( $expandingButtons as $slug => $expandingButton) {
                        echo $expandingButton;
                    } ?>
                </div>
            </div>
        <?php }

        if( ! $is_mini ){ ?>
            <?php do_action( 'bp_better_messages_thread_pre_header', $thread_id, $participants, $is_mini, 'thread' ); ?>
        <?php } else {
            do_action( 'bp_better_messages_thread_pre_header', $thread_id, $participants, $is_mini, 'thread' );
        } ?>
    </div>

    <div class="bpbm-chat-content">
        <div class="bpbm-chat-main">
        <div class="bpbm-pinned-message">
            <?php do_action('bp_better_messages_pinned_message', $thread_id, $participants, $is_mini, 'thread' ); ?>
        </div>
        <?php
        if( ( $is_participant && $participants_count > 2 || ! $is_participant && $participants_count > 1 ) && ! isset($_GET['mini'])) {
            $allow_invite = (BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'allow_invite' ) === 'yes');
            ?><div class="participants-panel">
                <?php if( $can_moderate ) { ?>
                <h4><?php _e('Thread settings', 'bp-better-messages'); ?></h4>
                <?php if( $participants_count > 2 ){ ?>
                <div class="bpbm-thread-options">
                    <div class="bpbm-thread-option">
                        <div class="bpbm-thread-option-toggle">
                            <input id="allow_invite" type="checkbox" value="yes" <?php checked(true, $allow_invite); ?>>
                            <label for="allow_invite"><?php _e('Allow thread members to invite other members', 'bp-better-messages'); ?></label>
                        </div>
                        <div class="bpbm-thread-option-description"><?php _e('When enabled, thread participants will be able to add other users to thread.', 'bp-better-messages'); ?></div>
                    </div>
                </div>
                <?php } ?>

                <?php if( isset( $subject ) ) { ?>
                <div class="bpbm-thread-options">
                    <div class="bpbm-thread-option">
                        <div class="bpbm-thread-option-toggle">
                            <label for="rename_thread"><?php _e('Change thread subject', 'bp-better-messages'); ?></label>
                            <input id="rename_thread" type="text" value="<?php echo esc_attr($subject); ?>">
                        </div>
                        <div class="bpbm-thread-option-description bpbm-change-subject-div" style="display: none">
                            <button><?php _e('Change subject', 'bp-better-messages'); ?></button>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <script type="text/javascript">
                    jQuery('#allow_invite').change(function (){
                        var is_checked = jQuery(this).is(':checked');

                        jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            'action'    : 'bp_messages_change_thread_option',
                            'thread_id' : '<?php echo $thread_id; ?>',
                            'option'    : 'allow_invite',
                            'value'     : is_checked,
                            '_wpnonce'  : '<?php echo wp_create_nonce( 'bp_messages_change_thread_option_' . $thread_id ); ?>'
                        }, function (response) {
                            if( response.result === true ){
                                BBPMNotice( response.message );
                            } else {
                                BBPMShowError( response.errors.join("\n") );
                            }
                        });
                    });

                    jQuery('#rename_thread').on('change keyup', function (){
                        jQuery('.bpbm-change-subject-div').show();
                    });


                    jQuery('.bpbm-change-subject-div > button').click(function (){
                        if ( confirm('<?php _e('Are you sure you want to change subject of this thread?', 'bp-better-messages'); ?>') === true ) {
                            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                'action'    : 'bp_messages_change_thread_option',
                                'thread_id' : '<?php echo $thread_id; ?>',
                                'option'    : 'rename_thread',
                                'value'     : jQuery('#rename_thread').val(),
                                '_wpnonce'  : '<?php echo wp_create_nonce( 'bp_messages_change_thread_option_' . $thread_id ); ?>'
                            }, function (response) {
                                if( response.result === true ){
                                    BBPMNotice( response.message );
                                } else {
                                    BBPMShowError( response.errors.join("\n") );
                                }
                            });
                        }
                    });
                </script>
                <?php } ?>
                <h4><?php _e('Participants', 'bp-better-messages'); ?></h4>
                <div class="bp-messages-user-list">
                    <div class="bp-messages-user-list-loader"><i class="fas fa-spinner fa-spin"></i></div>
                </div>
            </div>
            <?php if( $can_moderate || $allow_invite || BP_Better_Messages()->settings['privateThreadInvite'] === '1' ){ ?>
            <div class="add-user-panel">
                <div class="add-user" data-thread-id="<?php esc_attr_e($thread_id); ?>">
                    <h4><?php _e('Add new participants', 'bp-better-messages'); ?></h4>
                    <div style="position: relative">
                    <div id="send-to" class="input"></div>
                    </div>
                    <div class="buttons">
                        <button type="submit"><?php _e('Add participants', 'bp-better-messages'); ?></button>
                        <button class="bpbm-close"><?php _e('Close', 'bp-better-messages'); ?></button>
                    </div>
                </div>
            </div>
            <?php } ?>
        <?php } ?>
        <?php do_action( 'bp_better_messages_thread_before_scroller', $thread_id, $participants, $is_mini, 'thread' ); ?>

        <div class="scroller scrollbar-inner thread bm-infodiv" data-id="<?php echo $thread_id; ?>"<?php do_action('bp_better_messages_thread_div', $thread_id) ?>>
            <div class="loading-messages">
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
            </div>
            <div class="list<?php if($can_moderate ) echo ' can-moderate'; ?>">
                <?php if(count($stacks) == 0 || ( count($stacks) == 1 && $stacks[0]['user_id'] == 0) || ( count($stacks) == 1 && $stacks[0]['messages'][0]['message'] === '<!— BBPM START THREAD —>') ) { ?>
                <div class="empty-thread">
                    <i class="fas fa-comments"></i>
                    <span><?php esc_attr_e('Write the message to start conversation', 'bp-better-messages'); ?></span>
                </div>
                <?php } else {
                    foreach ( $stacks as $stack ) {
                        echo BP_Better_Messages()->functions->render_stack( $stack );
                    }
                } ?>
            </div>
        </div>

        <?php do_action( 'bp_better_messages_thread_after_scroller', $thread_id, $participants, $is_mini, 'thread' ); ?>

        <span class="writing" data-thread-id="<?php echo $thread_id; ?>" style="display: none"></span>

        <?php if( apply_filters('bp_better_messages_can_send_message', BP_Better_Messages()->functions->check_access( $thread_id ), get_current_user_id(), $thread_id ) ) { ?>
        <div class="reply">
            <form action="" method="POST">
                <div class="message">
                    <?php do_action( 'bp_messages_before_reply_textarea', $thread_id ); ?>
                    <textarea placeholder="<?php esc_attr_e( "Write your message", 'bp-better-messages' ); ?>" name="message" autocomplete="off"></textarea>
                    <?php do_action( 'bp_messages_after_reply_textarea', $thread_id ); ?>
                </div>
                <div class="send">
                    <?php do_action('bp_better_messages_before_reply_send'); ?>
                    <button type="submit" title="<?php _e( 'Send Message', 'bp-better-messages' ); ?>"><i class="fas fa-paper-plane" aria-hidden="true"></i></button>
                    <?php do_action('bp_better_messages_after_reply_send'); ?>
                </div>
                <input type="hidden" name="action" value="bp_messages_send_message">
                <input type="hidden" name="thread_id" value="<?php echo $thread_id; ?>">
                <?php wp_nonce_field( 'sendMessage_' . $thread_id ); ?>
            </form>

            <span class="clearfix"></span>

            <?php do_action( 'bp_messages_after_reply_form', $thread_id ); ?>
        </div>


       <?php do_action( 'bp_messages_after_reply_div', $thread_id ); ?>
        <?php } else {
            global $bp_better_messages_restrict_send_message;
            if( is_array($bp_better_messages_restrict_send_message) && ! empty( $bp_better_messages_restrict_send_message ) ){
                echo '<div class="reply">';
                echo '<ul class="bp-better-messages-restrict-send-message">';
                foreach( $bp_better_messages_restrict_send_message as $error ){
                    echo '<li>' . $error . '</li>';
                }
                echo '</ul>';
                echo '</div>';
            }
        } ?>
        </div>

        <?php do_action( 'bp_messages_thread_main_content', $thread_id, $participants, $is_mini, 'thread' ); ?>
    </div>

    <div class="preloader"></div>
    <?php if( ! $is_mini && BP_Better_Messages()->settings['disableTapToOpen'] === '0' ){ ?>
        <div class="bp-messages-mobile-tap"><?php _e( 'Tap to open messages', 'bp-better-messages' ); ?></div>
    <?php } ?>

    </div>
</div>
</div>