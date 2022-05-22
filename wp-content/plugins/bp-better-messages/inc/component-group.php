<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Component Class.
 *
 * @since 1.0.0
 */
class BP_Better_Messages_Group extends BP_Group_Extension
{

    public static function instance()
    {
        // Store the instance locally to avoid private static replication
        static $instance = null;

        // Only run these methods if they haven't been run previously
        if ( null === $instance ) {
            $instance = new BP_Better_Messages_Group;
            $instance->setup_hooks();
        }

        // Always return the instance
        return $instance;

        // The last metroid is in captivity. The galaxy is at peace.
    }

    /**
     * @since 1.0.0
     */
    public function __construct()
    {
        $args = array(
            'slug'              => BP_Better_Messages()->settings['bpGroupSlug'],
            'name'              => __( 'Messages', 'bp-better-messages' ),
            'nav_item_position' => 105,
            'enable_nav_item'   => apply_filters( 'bp_better_messages_enable_groups_tab', true ),
            'screens'           => array(),
            'visibility'        => 'private',
            'access'            => 'member'
        );

        if( BP_Better_Messages()->settings['enableGroups'] === '1' ){
            global $bp;
            if( isset( $bp->groups->current_group->id ) ) {
                $enabled = ($this->is_group_messages_enabled( $bp->groups->current_group->id ) === 'enabled' );
                if( $enabled ){
                    parent::init( $args );
                }
            } else if(  is_customize_preview() ){
                parent::init( $args );
            }
        }
    }

    /**
     * Set some hooks to maximize BuddyPress integration.
     *
     * @since 1.0.0
     */
    public function setup_hooks()
    {
        add_action( 'groups_join_group',            array( $this, 'on_groups_member_status_change'), 10, 2 );
        add_action( 'groups_leave_group',           array( $this, 'on_groups_member_status_change'), 10, 2 );
        add_action( 'groups_ban_member',            array( $this, 'on_groups_member_status_change'), 10, 2 );
        add_action( 'groups_remove_member',         array( $this, 'on_groups_member_status_change'), 10, 2 );
        add_action( 'groups_unban_member',          array( $this, 'on_groups_member_status_change'), 10, 2 );
        add_action( 'groups_group_request_managed', array( $this, 'on_groups_member_status_change'), 10, 1 );
        add_action( 'groups_member_after_remove',   array( $this, 'groups_member_after_remove'), 10, 1 );

        add_action( 'bp_invitations_accepted_invite', array( $this, 'bp_invitations_accepted_invite'), 10, 1 );

        add_action( 'groups_delete_group',  array( $this, 'on_delete_group'), 10, 1 );

        add_action( 'bp_rest_group_members_create_item', array( $this, 'on_groups_member_rest_update'), 10, 5 );
        add_action( 'bp_rest_group_members_update_item', array( $this, 'on_groups_member_rest_update'), 10, 5 );
        add_action( 'bp_rest_group_members_delete_item', array( $this, 'on_groups_member_rest_update'), 10, 5 );

        if( BP_Better_Messages()->settings['enableGroups'] === '1' ) {
            add_action('bp_after_group_settings_admin', array($this, 'layout_group_setting'), 10);
            add_action('bp_after_group_settings_creation_step', array($this, 'layout_group_setting'), 10);
            add_action( 'groups_settings_updated',           array( $this, 'group_setting_update'), 10 );
            add_action( 'groups_create_group_step_complete', array( $this, 'group_setting_create'), 10 );

            if( BP_Better_Messages()->settings['enableGroupsFiles'] === '0' ) {
                add_action('bp_better_messages_user_can_upload_files', array($this, 'disable_upload_files'), 10, 3);
            }
        }
    }

    public function disable_upload_files( $can_upload, $user_id, $thread_id ){
        if( BP_Better_Messages()->functions->get_thread_type( $thread_id ) === 'group' ) {
            return false;
        }

        return $can_upload;
    }

    public function bp_invitations_accepted_invite($r){
        if( isset( $r['class'] ) && isset ( $r['item_id'] ) ){
            if( $r['class'] === 'BP_Groups_Invitation_Manager' ){
                $group_id = $r['item_id'];
                $this->on_groups_member_status_change( $group_id );
            }
        }
    }

    public function on_delete_group($group_id){
        global $wpdb;

        $thread_id = (int) $wpdb->get_var( $wpdb->prepare( "
        SELECT bpbm_threads_id 
        FROM `" . bpbm_get_table('threadsmeta') . "` 
        WHERE `meta_key` = 'group_id' 
        AND   `meta_value` = %s
        ", $group_id ) );

        if( !! $thread_id ){
            BP_Better_Messages()->functions->delete_thread_meta( $thread_id, 'group_id' );
            BP_Better_Messages()->functions->delete_thread_meta( $thread_id, 'group_thread' );
        }
    }

    public function is_group_messages_enabled( $group_id = false ){
        if( BP_Better_Messages()->settings['enableGroups'] !== '1' ){
            return 'disabled';
        }

        $messages = 'enabled';
        if( !! $group_id ) {
            $messages = groups_get_groupmeta( $group_id, 'bpbm_messages' );
            if( empty( $messages ) ) $messages = 'enabled';
        }

        return $messages;
    }

    public function group_setting_create(){
        if( isset($_POST['bpbm_messages']) ){
            global $bp;
            $group_id = $bp->groups->new_group_id;
            $messages_status = sanitize_text_field($_POST['bpbm_messages']);
            groups_update_groupmeta( $group_id, 'bpbm_messages', $messages_status );
        }
    }

    public function group_setting_update( $group_id ){
        if( isset($_POST['bpbm_messages']) ){
            $messages_status = sanitize_text_field($_POST['bpbm_messages']);
            groups_update_groupmeta( $group_id, 'bpbm_messages', $messages_status );
        }
    }

    public function layout_group_setting(){
        if( doing_action('bp_after_group_settings_creation_step') ) {
            $group_id = false;
        } else {
            $group_id = bp_get_group_id();
        }

        $messages = $this->is_group_messages_enabled( $group_id ); ?>
        <div class="group-settings-selections">
        <fieldset class="radio ">
            <legend><?php esc_html_e( 'Group Messages', 'bp-better-messages' ); ?></legend>

            <p tabindex="0"><?php _ex( 'Enable Group Messages feature for this group', 'BuddyPress Groups', 'bp-better-messages' ); ?></p>
            <p tabindex="0"><?php _ex( 'All members of the group will be automatically joined to the conversation of this group', 'BuddyPress Groups', 'bp-better-messages' ); ?></p>

            <label for="group-bp-messages-enabled">
                <input type="radio" name="bpbm_messages" id="group-bp-messages-enabled" value="enabled" <?php checked($messages, 'enabled'); ?>/>
                <?php esc_html_e( 'Enabled', 'bp-better-messages' ); ?>
            </label>

            <label for="group-bp-messages-disabled">
                <input type="radio" name="bpbm_messages" id="group-bp-messages-disabled" value="disabled" <?php checked($messages, 'disabled'); ?> />
                <?php esc_html_e( 'Disabled', 'bp-better-messages' ); ?>
            </label>

        </fieldset>
        </div>
        <?php
    }

    public function groups_member_after_remove( $object ){
        $this->on_groups_member_status_change( $object->group_id, $object->user_id );
    }

    public function on_groups_member_rest_update( $user, $group_member, $group, $response, $request ){
        $this->on_groups_member_status_change( $group->id, $user->id );
    }

    public function on_groups_member_status_change( $group_id, $user_id = false ){
        $thread_id = $this->get_group_thread_id( $group_id );
        $this->sync_thread_members( $thread_id );
    }

    public function get_group_thread_id( $group_id ){
        global $wpdb;

        $thread_id = (int) $wpdb->get_var( $wpdb->prepare( "
        SELECT bpbm_threads_id 
        FROM `" . bpbm_get_table('threadsmeta') . "` 
        WHERE `meta_key` = 'group_id' 
        AND   `meta_value` = %s
        ", $group_id ) );

        $recipients_count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*)  FROM `" . bpbm_get_table('recipients') . "` WHERE `thread_id` = %d", $thread_id));

        if( $recipients_count === 0 ){
            $thread_id = false;
        }

        if( ! $thread_id ) {
            $wpdb->query( $wpdb->prepare( "
            DELETE  
            FROM `" . bpbm_get_table('threadsmeta') . "` 
            WHERE `meta_key` = 'group_id' 
            AND   `meta_value` = %s
            ", $group_id ) );

            $last_thread = intval($wpdb->get_var("SELECT MAX(thread_id) FROM `" . bpbm_get_table('messages') . "`;"));
            $thread_id = $last_thread + 1;
            $group = new BP_Groups_Group( $group_id );

            $wpdb->insert(
                bpbm_get_table('messages'),
                array(
                    'sender_id' => 0,
                    'thread_id' => $thread_id,
                    'subject' => $group->name,
                    'message' => '<!-- BBPM START THREAD -->'
                )
            );

            BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'group_thread', true );
            BP_Better_Messages()->functions->update_thread_meta( $thread_id, 'group_id', $group_id );

            $this->sync_thread_members( $thread_id );
        }

        return $thread_id;
    }

    public function sync_thread_members( $thread_id ){
        wp_cache_delete( 'thread_recipients_' . $thread_id, 'bp_messages' );
        wp_cache_delete( 'bm_thread_recipients_' . $thread_id, 'bp_messages' );
        $group_id = BP_Better_Messages()->functions->get_thread_meta( $thread_id, 'group_id' );
        $group    = new BP_Groups_Group( $group_id );

        if( ! $group ) {
            return false;
        }

        global $wpdb;
        $members   = BP_Groups_Member::get_group_member_ids( $group_id );
        $array     = [];

        /**
         * All users ids in thread
         */
        $recipients = BP_Messages_Thread::get_recipients_for_thread( $thread_id );

        foreach( $members as $index => $member ){
            if( isset( $recipients[$member] ) ){
                unset( $recipients[$member] );
                continue;
            }

            $array[] = [
                $member,
                $thread_id,
                0,
                0,
                0,
            ];
        }

        if( count($array) > 0 ) {
            $sql = "INSERT INTO " . bpbm_get_table('recipients') . "
            (user_id, thread_id, unread_count, sender_only, is_deleted)
            VALUES ";

            $values = [];

            foreach ($array as $item) {
                $values[] = $wpdb->prepare( "(%d, %d, %d, %d, %d)", $item );
            }

            $sql .= implode( ',', $values );

            $wpdb->query( $sql );
        }

        if( count($recipients) > 0 ) {
            foreach ($recipients as $user_id => $recipient) {
                global $wpdb;
                $wpdb->delete( bpbm_get_table('recipients'), [
                    'thread_id' => $thread_id,
                    'user_id'   => $user_id
                ], ['%d','%d'] );
            }
        }

        BP_Better_Messages()->hooks->clean_thread_cache( $thread_id );

        return true;
    }

    function display( $group_id = NULL ) {
        $compatibilityMode = BP_Better_Messages()->settings['compatibilityMode'] === '1';
        if( $compatibilityMode ) {
            echo do_shortcode('[bp_better_messages_group group_id="' . $group_id . '"]');
        } else {
            echo '[bp_better_messages_group group_id="' . $group_id . '"]';
        }
    }
}

bp_register_group_extension( 'BP_Better_Messages_Group' );

function BP_Better_Messages_Group()
{
    return BP_Better_Messages_Group::instance();
}