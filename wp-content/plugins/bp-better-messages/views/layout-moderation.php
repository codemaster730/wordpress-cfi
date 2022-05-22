<?php
/**
 * Settings page
 */
defined( 'ABSPATH' ) || exit;
wp_enqueue_script( 'react' );
wp_enqueue_script( 'react-dom' );

$filepath = BP_Better_Messages()->path . 'assets/admin/admin.js'; ?>

<div class="wrap">
    <h1><?php _e( 'Messages Viewer', 'bp-better-messages' ); ?></h1>

    <div id="layout-moderation"></div>
</div>
<script src='<?php echo BP_Better_Messages()->url; ?>/assets/admin/admin.js?ver=<?php echo filemtime($filepath); ?>' />