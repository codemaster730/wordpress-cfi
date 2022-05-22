<?php
/**
 * Settings page
 */
defined( 'ABSPATH' ) || exit;
global $wpdb;
$messages_table = bpbm_get_table('messages');

$page = (isset($_GET['cpage'])) ? intval( $_GET['cpage'] ) : 1;


$messages_total = $wpdb->get_var("
SELECT COUNT(*) 
FROM {$messages_table} 
WHERE `date_sent` > '0000-00-00 00:00:00'
AND `message` != '<!-- BBPM START THREAD -->'
ORDER BY `id` DESC
");

$per_page = 20;
$offset = 0;
if( $page > 1 ){
    $offset = ( $page - 1 ) * $per_page;
}

$messages = $wpdb->get_results("
SELECT * 
FROM {$messages_table} 
WHERE `date_sent` > '0000-00-00 00:00:00'
AND `message` != '<!-- BBPM START THREAD -->'
ORDER BY `id` DESC
LIMIT {$offset}, {$per_page}
");
?>

<link rel='stylesheet' href='<?php echo BP_Better_Messages()->url; ?>/assets/admin/viewer.css?ver=<?php echo BP_Better_Messages()->version; ?>' media='all' />
<div class="wrap">
    <h1><?php _e( 'Messages Viewer', 'bp-better-messages' ); ?></h1>

    <table class="bp-messages-list widefat fixed">
        <thead>
            <tr>
                <th><?php _e( 'Sender', 'bp-better-messages' ); ?></th>
                <th><?php _e( 'Message', 'bp-better-messages' ); ?></th>
                <th><?php _e( 'Thread',  'bp-better-messages' ); ?></th>
                <th><?php _e( 'Time Sent', 'bp-better-messages' ); ?></th>
            </tr>
        </thead>
        <?php foreach ( $messages as $message ){ ?>
        <tr>
            <td class="user-td"><?php
                $userdata = get_userdata( $message->sender_id );

                if( ! $userdata ){
                    _e( 'Deleted User', 'bp-better-messages' );
                } else {
                    $link = bp_core_get_userlink( $message->sender_id, false, true );

                    echo BP_Better_Messages()->functions->get_avatar($message->sender_id, 20);
                    echo '<a href="' . $link . '" target="_blank">';
                    echo BP_Better_Messages()->functions->get_name($message->sender_id);
                    echo '</a>';
                }

                ?></td>
            <td><?php echo $message->message; ?></td>
            <td><?php
                $participants = BP_Better_Messages()->functions->get_participants( $message->thread_id );
                _e( 'Thread ID:',  'bp-better-messages' );
                echo ' ' . $message->thread_id . '<br>';
                _e( 'Participants Count:',  'bp-better-messages' );
                echo ' ' . $participants['count'] . '<br>';
                $view_link = add_query_arg([
                    'thread_id' => $message->thread_id
                ], BP_Better_Messages()->functions->get_link() );

                echo '<a href="' . $view_link . '" target="_blank">';
                _e( 'View Thread',  'bp-better-messages' );
                echo '</a>';
                ?></td>
            <td><?php echo $message->date_sent; ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php
    echo '<div class="pagination">';
    echo paginate_links( array(
        'base' => add_query_arg( 'cpage', '%#%' ),
        'format' => '',
        'prev_text' => __('&laquo;'),
        'next_text' => __('&raquo;'),
        'total' => ceil($messages_total / $per_page),
        'current' => $page,
        'type' => 'list'
    ));
    echo '</div>';
    ?>
</div>