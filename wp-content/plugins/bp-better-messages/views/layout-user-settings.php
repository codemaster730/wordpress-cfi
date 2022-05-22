<?php
defined( 'ABSPATH' ) || exit;

$user_id = get_current_user_id();
if ( function_exists('buddypress') && bp_displayed_user_id() !== 0 ) {
    $user_id = bp_displayed_user_id();
}

$threads = BP_Better_Messages()->functions->get_threads( $user_id );
$favorited = BP_Better_Messages()->functions->get_starred_count();

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

            <div class="scroller scrollbar-inner ">
                <div class="bpbm-user-options">
                    <?php
                    if( BP_Better_Messages()->settings['allowUsersRestictNewThreads'] === '1' ){
                        $options = BP_Better_Messages()->functions->get_who_can_start_options();
                        $current = BP_Better_Messages()->functions->get_who_can_start_value( get_current_user_id() );
                        ?>
                        <h4 class="bpbm-user-option-title">
                            <?php esc_attr_e('Who can start private conversations with you?', 'bp-better-messages'); ?>
                        </h4>
                        <div class="bpbm-user-option ">
                            <div style="margin: 10px 0" id="bpbm-who-can-start-conversations">
                                <?php
                                foreach( $options as $key => $value ) {
                                    echo '<div>';
                                    echo '<input id="bpbm-who-can-start-conversations-' . $key . '" class="bpbm-checkbox" type="checkbox" value="' . $key . '" ' . ( ( in_array( $key, $current ) ) ? "checked" : "" ) . '><label for="bpbm-who-can-start-conversations-' . $key . '">' . $value . '</label>';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <h4 class="bpbm-user-option-title">
                        <?php _e('Notifications', 'bp-better-messages'); ?>
                    </h4>

                    <div class="bpbm-user-option">
                        <?php
                        $checked = (get_user_meta( $user_id, 'notification_messages_new_message', true ) !== 'no'); ?>
                        <div class="bpbm-user-option-toggle">
                            <input id="email_notifications" class="bpbm-checkbox" type="checkbox" value="yes" <?php checked(true, $checked); ?>>
                            <label for="email_notifications"><?php esc_attr_e('Enable notifications via email', 'bp-better-messages'); ?></label>
                        </div>
                        <div class="bpbm-user-option-description">
                            <?php esc_attr_e('When enabled, you will receive notifications about new messages via email when you are offline.', 'bp-better-messages'); ?>
                        </div>
                    </div>

                    <?php if( BP_Better_Messages()->settings['allowSoundDisable'] === '1' ) { ?>
                    <div class="bpbm-user-option">
                        <?php
                        $checked = (get_user_meta( $user_id, 'bpbm_disable_sound_notification', true ) === 'yes'); ?>
                        <div class="bpbm-user-option-toggle">
                            <input id="sound_notifications" class="bpbm-checkbox" type="checkbox" value="yes" <?php checked(true, $checked); ?>>
                            <label for="sound_notifications"><?php esc_attr_e('Disable sound notification on new message', 'bp-better-messages'); ?></label>
                        </div>
                        <div class="bpbm-user-option-description">
                            <?php esc_attr_e('When enabled, you will not hear sound when you receive new message.', 'bp-better-messages'); ?>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if(BP_Better_Messages()->settings['enablePushNotifications'] === '1' ) { ?>
                    <div class="bpbm-user-option BPBMpushNotifications">
                        <div class="bpbm-user-option-toggle">
                            <label><?php esc_attr_e('Browser push notifications', 'bp-better-messages'); ?></label>
                        </div>
                        <div class="bpbm-user-option-description">
                            <?php esc_attr_e('When enabled, you will receive messages notifications even if browser is closed.', 'bp-better-messages'); ?>
                            <div class="BPBMenablePushNotificationsControls">
                                <button class="BPBMenablePushNotifications" style="display: none"><?php _e('Enable', 'bp-better-messages'); ?></button>
                                <button class="BPBMdisablePushNotifications" style="display: none"><?php _e('Disable', 'bp-better-messages'); ?></button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>


                    <?php do_action('bp_better_messages_user_options_bottom'); ?>

                    <?php do_action('bp_better_messages_user_options_scripts'); ?>

                    <script type="text/javascript">
                        jQuery('#bpbm-who-can-start-conversations input').change(function(){
                            var input = jQuery(this);
                            var value = input.val();
                            var container = input.closest('#bpbm-who-can-start-conversations');

                            if( value === 'everyone' || value === 'nobody' ){
                                container.find('input').not( input ).prop( 'checked', false );
                            } else {
                                container.find('#bpbm-who-can-start-conversations-nobody,#bpbm-who-can-start-conversations-everyone').prop( 'checked', false );
                            }

                            var values = [];
                            var checked = container.find('input:checked');

                            if( checked.length === 0 ){
                                container.find('#bpbm-who-can-start-conversations-everyone').prop( 'checked', true );
                                checked = container.find('input:checked');
                            }

                            checked.each(function(){
                                var inp = jQuery(this);
                                values.push( inp.val() );
                            });


                            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                'action'   : 'bp_messages_change_user_option',
                                'user_id'  : '<?php echo $user_id; ?>',
                                'option'   : 'who_can_start_conversations',
                                'value'    : values.join(','),
                                '_wpnonce' : '<?php echo wp_create_nonce( 'bp_messages_change_user_option_' . $user_id ); ?>'
                            }, function (response) {
                                if( response.result === true ){
                                    BBPMNotice( response.message );
                                } else {
                                    BBPMShowError( response.errors.join("\n") );
                                }
                            });
                        });

                        jQuery('#email_notifications').change(function(){
                            var is_checked = jQuery(this).is(':checked');

                            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                'action'   : 'bp_messages_change_user_option',
                                'user_id'  : '<?php echo $user_id; ?>',
                                'option'   : 'email_notifications',
                                'value'    : is_checked,
                                '_wpnonce' : '<?php echo wp_create_nonce( 'bp_messages_change_user_option_' . $user_id ); ?>'
                            }, function (response) {
                                if( response.result === true ){
                                    BBPMNotice( response.message );
                                } else {
                                    BBPMShowError( response.errors.join("\n") );
                                }
                            });
                        });


                        jQuery('#sound_notifications').change(function(){
                            var is_checked = jQuery(this).is(':checked');

                            jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                'action'   : 'bp_messages_change_user_option',
                                'user_id'  : '<?php echo $user_id; ?>',
                                'option'   : 'sound_notifications',
                                'value'    : is_checked,
                                '_wpnonce' : '<?php echo wp_create_nonce( 'bp_messages_change_user_option_' . $user_id ); ?>'
                            }, function (response) {
                                if( response.result === true ){
                                    BBPMNotice( response.message );
                                    if( is_checked ){
                                        BP_Messages['enableSound'] = '0';
                                    } else {
                                        BP_Messages['enableSound'] = '1';
                                    }
                                } else {
                                    BBPMShowError( response.errors.join("\n") );
                                }
                            });
                        });
                    </script>


                </div>
            </div>

            <?php  BP_Better_Messages()->functions->render_footer('bpbm-mobile-only'); ?>
            <div class="preloader"></div>

            <?php if( BP_Better_Messages()->settings['disableTapToOpen'] === '0' ){ ?>
                <div class="bp-messages-mobile-tap"><?php _e( 'Tap to open messages', 'bp-better-messages' ); ?></div>
            <?php } ?>
        </div>
    </div>
</div>