<?php
defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'BP_Better_Messages_Block_Users' ) ){

    class BP_Better_Messages_Block_Users
    {

        public static function instance()
        {

            static $instance = null;

            if (null === $instance) {
                $instance = new BP_Better_Messages_Block_Users();
            }

            return $instance;
        }

        public function __construct(){
            add_action('wp_ajax_bp_messages_block_user',   array($this, 'block_user_ajax'));
            add_action('wp_ajax_bp_messages_unblock_user', array($this, 'unblock_user_ajax'));

            add_filter('bp_better_messages_thread_expanding_buttons', array($this, 'block_user_button'), 10, 5);

            add_filter( 'bp_better_messages_can_send_message', array( $this, 'disable_blocked_replies' ), 20, 3);
            add_action( 'bp_better_messages_before_new_thread', array( $this, 'disable_start_thread_for_blocked_users' ), 20, 2 );

            add_action( 'bp_better_messages_user_options_bottom', array( $this, 'block_users_settings' ) );
        }

        public function block_users_settings(){
            if( count(BP_Better_Messages()->settings['restrictBlockUsers']) > 0 ){
                foreach( BP_Better_Messages()->settings['restrictBlockUsers'] as $blockedRole ){
                    if( in_array( $blockedRole, wp_get_current_user()->roles ) ){
                        return;
                    }
                }
            }

            $blocked_users = $this->get_blocked_users( get_current_user_id() );
            ?>
            <h4 class="bpbm-user-option-title">
                <?php echo esc_attr_x('Blocked users', 'User settings page', 'bp-better-messages'); ?>
            </h4>
            <div class="bpbm-user-option">
                <div class="bpbm-user-option-description">
                    <?php echo esc_attr_x('This is list of users you blocked, you can remove them from blacklist here if needed.', 'User settings page', 'bp-better-messages'); ?>
                </div>
                <div class="bpbm-user-blacklist">
                    <?php if( count( $blocked_users ) > 0 ) { ?>
                    <table>
                        <tbody>
                        <?php foreach( $blocked_users as $user_id => $timestamp ){ ?>
                            <tr>
                                <td><?php echo BP_Better_Messages()->functions->get_user_link( $user_id, 20 ); ?></td>
                                <td><a href="#" class="bpbm-unblock-user" data-user-id="<?php echo $user_id; ?>"><?php echo esc_attr_x('Unblock user', 'User settings page', 'bp-better-messages'); ?></a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <?php } ?>
                    <div class="bpbm-user-blacklist-empty">
                        <i class="fas fa-stream"></i>
                        <?php echo esc_attr_x("You didn't blocked anyone yet", 'User settings page', 'bp-better-messages'); ?>
                    </div>
                </div>
            </div>
            <script type="text/javascript">
                /**
                 * Unblock user
                 */
                jQuery('.bpbm-user-blacklist').on('click', '.bpbm-unblock-user', function (event) {
                    event.preventDefault();
                    var button  = jQuery(this);
                    var row     = button.closest('tr');
                    var tbody   = button.closest('tbody');
                    var table   = button.closest('table');
                    var user_id = parseInt(button.attr('data-user-id'));

                    var confirmBlock = confirm(BP_Messages['strings']['user_unblock']);

                    if( confirmBlock ){
                        jQuery.post( BP_Messages[ 'ajaxUrl' ], {
                            'action'       : 'bp_messages_unblock_user',
                            'user_id'      : user_id,
                            nonce          : BP_Messages['userNonce']
                        }, function (response) {
                            row.remove();

                            if( tbody.find('> tr').length === 0 ){
                                table.remove();
                            }
                        });
                    }
                });
            </script>
            <?php
        }

        public function disable_start_thread_for_blocked_users(&$args, &$errors){
            if( current_user_can('manage_options' ) ) {
                return null;
            }

            $recipients = $args['recipients'];
            if( ! is_array( $recipients ) ) $recipients = [ $recipients ];

            foreach($recipients as $recipient) {
                $user = get_user_by('slug', $recipient);

                $is_blocked_1 = $this->is_user_blocked(get_current_user_id(), $user->ID);
                if ($is_blocked_1){
                    $errors[] = sprintf(_x('%s blocked by you', 'Error when starting new thread but user blocked', 'bp-better-messages'), BP_Better_Messages()->functions->get_name($user->ID));
                    continue;
                }

                $is_blocked_2 = $this->is_user_blocked($user->ID, get_current_user_id());
                if ($is_blocked_2){
                    $errors[] = sprintf(_x('%s blocked you', 'Error when starting new thread but user blocked', 'bp-better-messages'), BP_Better_Messages()->functions->get_name($user->ID));
                    continue;
                }
            }
        }

        public function disable_blocked_replies( $allowed, $user_id, $thread_id ){
            $current_user = get_userdata( $user_id );

            if( ! $current_user ) {
                return $allowed;
            }

            if(in_array('administrator', $current_user->roles)){
                return $allowed;
            }

            $participants = BP_Better_Messages()->functions->get_participants($thread_id);

            if( count($participants['recipients']) !== 1) return $allowed;

            $thread_type = BP_Better_Messages()->functions->get_thread_type( $thread_id );
            if( $thread_type !== 'thread' ) return $allowed;

            $user_id_2 = $participants['recipients'][0];

            /**
             *  Current user blocked other
             */
            $is_blocked_1 = $this->is_user_blocked( get_current_user_id(), $user_id_2 );
            if( $is_blocked_1 ) {
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['user_blocked_messages'] = _x("You can't send message to user who was blocked by you", 'Message when user cant send message to user blocked by him' ,'bp-better-messages');
                return false;
            }

            /**
             *  Other user blocked current user
             */
            $is_blocked_2 = $this->is_user_blocked( $user_id_2, get_current_user_id() );
            if( $is_blocked_2 ) {
                global $bp_better_messages_restrict_send_message;
                $bp_better_messages_restrict_send_message['user_blocked_messages'] = _x("You can't send message to user who blocked you", 'Message when user cant send message to user who blocked him' ,'bp-better-messages');
                return false;
            }

            return $allowed;
        }


        public function block_user_ajax(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $blocked_user_id = intval( $_POST['user_id'] );
            wp_send_json($this->block_user( get_current_user_id(), $blocked_user_id ));
        }

        public function unblock_user_ajax(){
            if( ! wp_verify_nonce( $_POST[ 'nonce' ], 'bpbm_edit_nonce' ) ){
                exit;
            }

            $blocked_user_id = intval( $_POST['user_id'] );
            wp_send_json($this->unblock_user( get_current_user_id(), $blocked_user_id ));
        }

        public function block_user_button( $buttons, $thread_id, $participants, $is_mini, $type = 'thread'){
            if( $type !== 'thread' || count($participants['recipients']) !== 1 ) return $buttons;

            $user_id = $participants['recipients'][0];

            $can_block  = $this->can_block_user( get_current_user_id(), $user_id );
            if( ! $can_block ) return $buttons;

            $is_blocked = $this->is_user_blocked( get_current_user_id(), $user_id );

            if( ! $is_blocked ) {
                $buttons[] = '<span title="' . _x('Block private messages from this user to you', 'Block user button tooltip in more options dropdown', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-block-user bpbm-can-be-hidden" data-user-id="' . $user_id . '"><i class="fas fa-ban"></i> ' . _x('Block user', 'Block user button in more options dropdown', 'bp-better-messages') . '</span>';
            } else {
                $buttons[] = '<span title="' . _x('Unblock private messages from this user to you', 'Unblock user button tooltip in more options dropdown', 'bp-better-messages') . '" class="bpbm-dropdown-item bpbm-unblock-user bpbm-can-be-hidden" data-user-id="' . $user_id . '"><i class="fas fa-ban"></i> ' . _x('Unblock user', 'Unblock user button in more options dropdown', 'bp-better-messages') . '</span>';
            }
            return $buttons;
        }

        public function get_blocked_users( $user_id ){
            $blocked_users = get_user_meta($user_id, 'bm_blocked_users', true);

            if( ! is_array( $blocked_users ) || empty( $blocked_users ) ) {
                $blocked_users = [];
            }

            return $blocked_users;
        }

        public function is_user_blocked( $user_id, $blocked_id ){
            $blocked_users = $this->get_blocked_users( $user_id );

            if( isset( $blocked_users[$blocked_id] ) ){
                return true;
            } else {
                return false;
            }
        }

        public function block_user( $user_id, $blocked_id ){
            $blocked_users = $this->get_blocked_users( $user_id );

            $can_block = $this->can_block_user( $user_id, $blocked_id );

            if( $can_block ) {
                $blocked_users[ $blocked_id ] = time();
                update_user_meta( $user_id, 'bm_blocked_users', $blocked_users );
                return true;
            } else {
                return false;
            }
        }

        public function unblock_user( $user_id, $blocked_id ){
            $blocked_users = $this->get_blocked_users( $user_id );

            $can_unblock = $this->can_unblock_user( $user_id, $blocked_id );

            if( $can_unblock ) {
                if( isset( $blocked_users[ $blocked_id ] ) ) {
                    unset( $blocked_users[ $blocked_id ] );
                }

                update_user_meta( $user_id, 'bm_blocked_users', $blocked_users );

                return true;
            } else {
                return false;
            }
        }

        public function can_block_user( $user_id, $blocked_id ){
            $blocker_user = get_userdata( $user_id );
            $blocked_user = get_userdata( $blocked_id );

            $can_block = true;

            if( (int) $user_id === (int) $blocked_id ){
                $can_block = false;
            } else if( ! $blocked_user || ! $blocker_user ) {
                $can_block = false;
            } else {
                /**
                 * Administrator can't be blocked
                 */
                if (in_array('administrator', $blocked_user->roles)) {
                    $can_block = false;
                }

                if( count(BP_Better_Messages()->settings['restrictBlockUsers']) > 0 ){
                    foreach( BP_Better_Messages()->settings['restrictBlockUsers'] as $blockedRole ){
                        if( in_array( $blockedRole, $blocker_user->roles ) ){
                            $can_block = false;
                        }
                    }
                }

                if( count(BP_Better_Messages()->settings['restrictBlockUsersImmun']) > 0 ){
                    foreach( BP_Better_Messages()->settings['restrictBlockUsersImmun'] as $blockedRole ){
                        if( in_array( $blockedRole, $blocked_user->roles ) ){
                            $can_block = false;
                        }
                    }
                }

                /**
                 * Administrator always can block
                 */
                if (in_array('administrator', $blocker_user->roles)) {
                    $can_block = true;
                }
            }

            return apply_filters( 'bp_better_messages_can_block_user', $can_block, $user_id, $blocked_id );
        }

        public function can_unblock_user( $user_id, $blocked_id ){
            return apply_filters( 'bp_better_messages_can_unblock_user', true, $user_id, $blocked_id );
        }

    }
}

