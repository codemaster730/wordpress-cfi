<div class="bp-messages-wrap bp-messages-wrap-main <?php BP_Better_Messages()->functions->messages_classes(); ?>">
    <div class="bp-messages-threads-wrapper threads-hidden">
        <?php $side_threads = (BP_Better_Messages()->settings['combinedView'] === '1');
        if( $side_threads) {
            BP_Better_Messages()->functions->render_side_column( get_current_user_id() );
        } ?>
        <div class="bp-messages-column">
            <div class="chat-header">
                <a href="<?php echo BP_Better_Messages()->functions->get_link( get_current_user_id()  ); ?>" class="new-message ajax" title="<?php _e( 'Close', 'bp-better-messages' ); ?>"><i class="fas fa-times" aria-hidden="true"></i></a>

                <?php if(current_user_can('manage_options')){ ?>
                <a href="<?php echo add_query_arg( 'bulk-message', '', BP_Better_Messages()->functions->get_link() ); ?>" class="mass-message ajax" title="<?php _e( 'Mass Message', 'bp-better-messages' ); ?>"><i class="fas fa-envelope" aria-hidden="true"></i></a>
                <?php }

                do_action( 'bp_better_messages_thread_pre_header', 0, [], false, 'new-thread' );

                ?>
                <a href="#" class="mobileClose"><i class="fas fa-window-close"></i></a>
            </div>
            <div class="new-message">
                <h4 id="bm-new-thread-title"><?php _ex('Start new conversation', 'New threads screen', 'bp-better-messages'); ?></h4>
                <form>
                    <?php if( BP_Better_Messages()->settings['disableUsersSearch'] !== '1' && BP_Better_Messages()->settings['enableUsersSuggestions'] === '1' ) {
                    $user_ids = apply_filters('better_messages_predefined_suggestions_user_ids', []);

                    if( ! is_array( $user_ids ) || count( $user_ids ) === 0 ){
                        $total_to_get = 12;
                        $friends = BP_Better_Messages()->functions->get_friends_sorted( get_current_user_id(), $total_to_get );

                        $user_ids = [];
                        if( count( $friends ) > 0 ) {
                            foreach ( $friends as $user_id => $time ){
                                $user_ids[] = $user_id;
                                $total_to_get--;
                            }
                        }

                        if( $total_to_get > 0 ) {
                            if ( BP_Better_Messages()->settings['searchAllUsers'] === '1' ) {
                                $other_users = BP_Better_Messages()->functions->get_users_sorted(get_current_user_id(), array_keys($friends), $total_to_get);
                                if (count($other_users) > 0) {
                                    foreach ($other_users as $user_id => $time) {
                                        $user_ids[] = $user_id;
                                    }
                                }
                            }
                        }
                    }

                    if( count( $user_ids ) > 0 ){
                    $args = [
                        'include'  => $user_ids,
                        'orderby'  => 'include'
                    ];

                    $all_users = get_users($args);

                    if( count( $all_users ) > 0 ){ ?>
                    <div>
                        <div class="bpbm-users-avatars-list">
                        <?php foreach( $all_users as $user ) {
                            $name = BP_Better_Messages()->functions->get_name( $user->ID );
                            $label = sprintf(_x('Add <strong>%s</strong> to new conversation', 'New threads screen', 'bp-better-messages'), $name);
                            echo '<div class="bpbm-users-avatars-list-item" data-nicename="' . $user->user_nicename  . '" title="' . $label . '">';
                                echo '<span class="bpbm-users-avatars-list-item-avatar">';
                                echo BP_Better_Messages()->functions->get_avatar( $user->ID, 50 );
                                echo '</span>';
                                echo '<span class="bpbm-users-avatars-list-item-name">';
                                echo BP_Better_Messages()->functions->get_name( $user->ID );
                                echo '</span>';
                            echo '</div>';
                        } ?>
                        </div>
                    </div>
                    <?php } } } ?>
                    <div>
                        <label><?php _e( "Send To (Username or Friend's Name)", 'bp-better-messages' ); ?></label>
                        <div id="send-to" class="input" tabindex="2"></div>
                        <span class="clearfix"></span>
                    </div>
                    <?php if(BP_Better_Messages()->settings['disableSubject'] !== '1') {
                        $subject = '';
                        if ( isset( $_GET[ 'subject' ] ) && ! empty( $_GET[ 'subject' ] ) ) {
                            $subject = BP_Better_Messages()->functions->sanitize_xss(sanitize_text_field($_GET[ 'subject' ]));
                        } ?>
                    <div>
                        <label for="subject-input"><?php _e( 'Subject', 'bp-better-messages' ); ?></label>
                        <input type="text" tabindex="3" name="subject" class="subject-input" id="subject-input" value="<?php echo $subject; ?>" autocomplete="off">
                        <span class="clearfix"></span>
                    </div>
                    <?php } ?>
                    <?php
                    $message = '';
                    if ( isset( $_GET[ 'message' ] ) && !empty( $_GET[ 'message' ] ) ) {
                        $message = sanitize_textarea_field($_GET['message']);
                    } ?>
                    <div>
                        <label for="message-input"><?php _e( 'Message', 'bp-better-messages' ); ?></label>

                        <div class="message">
                            <textarea name="message" tabindex="4" placeholder="<?php esc_attr_e( "Write your message", 'bp-better-messages' ); ?>" id="message-input" autocomplete="off"><?php echo $message; ?></textarea>
                        </div>

                        <span class="clearfix"></span>
                    </div>

                    <?php do_action( 'bp_messages_new_thread_form_before_send_button', 0 ); ?>

                    <button type="submit"><?php _e( 'Send Message', 'bp-better-messages' ); ?></button>

                    <?php if ( isset( $_GET[ 'to' ] ) && !empty( $_GET[ 'to' ] ) ) {
                        $recepients = explode(',', $_GET['to']);

                        foreach ($recepients as $recepient){
                            $user = false;

                            if( is_numeric( $recepient ) ){
                                $user = get_userdata( $recepient );
                            }
                            if( ! $user ) {
                                $user = get_user_by('slug', $recepient);
                            }

                            if( ! $user ) continue;
                            $img  = BP_Better_Messages()->functions->get_avatar( $user->ID, 40, [ 'html' => false ] );

                            echo '<input type="hidden" name="to" data-label="' . BP_Better_Messages()->functions->get_name($user->ID) . '" data-img="' . $img . '" value="' . $recepient . '">';
                        }
                    } ?>

                    <input type="hidden" name="action" value="bp_messages_new_thread">
                    <?php wp_nonce_field( 'newThread' ); ?>
                </form>

                <?php do_action( 'bp_messages_after_new_thread_form', 0 ); ?>
            </div>

            <script type="text/javascript">
                setTimeout(tabIndexFix, 100);
                function tabIndexFix(){
                    var result =  jQuery('.emojionearea-editor').attr('tabindex', '4');
                    if(result.length === 0) setTimeout(tabIndexFix, 100);
                }
            </script>

            <div class="preloader"></div>
            <?php if( BP_Better_Messages()->settings['disableTapToOpen'] === '0' ){ ?>
                <div class="bp-messages-mobile-tap"><?php _e( 'Tap to open messages', 'bp-better-messages' ); ?></div>
            <?php } ?>
                </div>
            </div>
</div>