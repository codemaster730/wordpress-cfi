<?php
/**
 * Settings page
 */
defined( 'ABSPATH' ) || exit;

$websocket_allowed = bpbm_fs()->can_use_premium_code__premium_only();
?>
<style type="text/css">
    .bpbm-tab{
        display: none;
    }

    .bpbm-tab.active{
        display: block;
    }

    td.attachments-formats ul{
        display: inline-block;
        vertical-align: top;
        padding: 0 30px 0 0;
        margin-top: 5px;
    }

    td.attachments-formats ul > strong{
        display: block;
        margin-bottom: 5px;
    }

    .cols{
        overflow: hidden;
    }

    .cols .col{
        width: 49%;
        float: left;
    }

    @media only screen and (max-width: 1050px){
        .cols .col{
            width: 100%;
            float: none;
        }
    }

    .wordplus-host{
        padding: 11px 15px;
        font-size: 14px;
        text-align: left;
        margin: 25px 20px 0 2px;
        background-color: #fff;
        box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
    }

    .wordplus-host .go-order{
        display: block;
        margin: 0 auto;
        max-width: 300px;
        height: 35px;
        line-height: 35px;
        background-color: #1bdb68;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        text-align: center;
        vertical-align: middle;
        text-decoration: none;
        border: 2px solid transparent;
        padding: 0 25px;
        touch-action: manipulation;
        cursor: pointer;
        background-image: none;
        white-space: nowrap;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -webkit-transition: color 0.3s ease-out, background-color 0.3s ease-out;
        -o-transition: color 0.3s ease-out, background-color 0.3s ease-out;
        transition: color 0.3s ease-out, background-color 0.3s ease-out;
    }

    .wordplus-host .go-order:hover{
        background-color: #15ae52;
    }

    .bpbm-tab .form-table th{
        width: auto;
    }

    .bpbm-tab#customization .form-table th{
        width: 200px;
    }

    input[type=checkbox], input[type=radio]{
        margin: 0 5px 0 0;
    }

    .bp-better-messages-facebook,
    .bp-better-messages-facebook:hover,
    .bp-better-messages-facebook:focus{
        background: #3b5998;
        display: inline-block;
        width: 300px;
        max-width: 100%;
        text-align: center;
        color: white;
        cursor: pointer;
        text-decoration: none;
        padding: 10px;
        font-size: 16px;
        margin-top: 30px;
    }

    .bp-better-messages-trial,
    .bp-better-messages-trial:hover,
    .bp-better-messages-trial:focus{
        background: #2271b1;
        display: inline-block;
        width: 300px;
        max-width: 100%;
        text-align: center;
        color: white;
        cursor: pointer;
        text-decoration: none;
        padding: 10px;
        font-size: 16px;
        margin-top: 20px;
    }

    .bp-better-messages-connection-check{
        display: block;
        margin: 10px 0;
        color: #856404;
        background-color: #fff3cd;
        border: 1px solid #f9e4a6;
        padding: 15px;
        line-height: 24px;
        max-width: 550px;
    }
    .bp-better-messages-connection-check.bpbm-error{
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
    .bp-better-messages-connection-check.bpbm-ok{
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }

    @-moz-keyframes bpbm-spin { 100% { -moz-transform: rotate(360deg); } }
    @-webkit-keyframes bpbm-spin { 100% { -webkit-transform: rotate(360deg); } }
    @keyframes bpbm-spin { 100% { -webkit-transform: rotate(360deg); transform:rotate(360deg); } }

    .bp-better-messages-roles-list{
        max-height: 250px;
        overflow: auto;
        background: white;
        padding: 15px;
        border: 1px solid #ccc;
    }

    .bp-better-messages-roles-list td,
    .bp-better-messages-roles-list th{
        padding: 5px;
    }

</style>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var hash = location.href.split('#')[1];
        if(typeof hash != 'undefined'){
            var selector = jQuery("#bpbm-tabs > a[href='#"+ hash+"']");
            jQuery('#bpbm-tabs > a').removeClass('nav-tab-active');
            jQuery('.bpbm-tab').removeClass('active');

            jQuery( selector ).addClass('nav-tab-active');
            jQuery( '#' + hash ).addClass('active');
        }


        $('input[name="mechanism"]').change(function () {
            var mechanism = $('input[name="mechanism"]:checked').val();

            $('.ajax, .websocket').hide();
            $('.' + mechanism).show();

            if(mechanism == 'websocket'){
                $('input[name="miniChatsEnable"]').attr('disabled', false);
                $('input[name="miniThreadsEnable"]').attr('disabled', false);
                $('input[name="messagesStatus"]').attr('disabled', false);
            } else {
                $('input[name="miniChatsEnable"]').attr('disabled', true);
                $('input[name="miniThreadsEnable"]').attr('disabled', true);
                $('input[name="messagesStatus"]').attr('disabled', true);
            }
        });

        changeTemplate();

        $('input[name="template"]').change(function () {
            changeTemplate();
        });

        function changeTemplate(){
            var template = $('input[name="template"]:checked').val();

            if(template === 'standard'){
                $('input[name="modernLayout"').attr('disabled', true);
                $('input[name="modernBorderRadius"').attr('disabled', true);
            } else {
                $('input[name="modernLayout"').attr('disabled', false);
                $('input[name="modernBorderRadius"').attr('disabled', false);
            }
        }

        $("#bpbm-tabs > a").on('click touchstart', function(event){
            event.preventDefault();
            event.stopPropagation();

            if( $(this).hasClass('nav-tab-active') ) return false;

            var selector = $(this).attr('href');
            window.history.pushState("", "", selector);

            $('#bpbm-tabs > a').removeClass('nav-tab-active');
            $('.bpbm-tab').removeClass('active');

            $(this).addClass('nav-tab-active');
            $(selector).addClass('active');
        });

        $('.color-selector').wpColorPicker();
    });
</script>
<div class="wrap">
    <h1><?php _e( 'BP Better Messages', 'bp-better-messages' ); ?></h1>
    <div class="nav-tab-wrapper" id="bpbm-tabs">
        <a class="nav-tab nav-tab-active" id="general-tab" href="#general"><?php _e( 'General', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="chat-tab" href="#chat"><?php _e( 'Messages', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="groups-tab" href="#groups"><?php _e( 'Groups', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="mobile-tab" href="#mobile"><?php _e( 'Mobile', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="attachments-tab" href="#attachments"><?php _e( 'Attachments', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="notifications-tab" href="#notifications"><?php _e( 'Notifications', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="stickers-tab" href="#stickers"><?php _e( 'GIFs & Stickers', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="rules-tab" href="#rules"><?php _e( 'Restrictions', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="sounds-tab" href="#sounds"><?php _e( 'Sounds', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="calls-tab" href="#calls"><?php _e( 'Calls', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="customization-tab" href="#customization"><?php _e( 'Customization', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="shortcodes-tab" href="#shortcodes"><?php _e( 'Shortcodes', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="export-import-tab" href="#export-import"><?php _e( 'Export/Import', 'bp-better-messages' ); ?></a>
    </div>
    <form action="" method="POST">
        <?php wp_nonce_field( 'bp-better-messages-settings' ); ?>
        <div id="general" class="bpbm-tab active">
            <div class="cols">
                <div class="col">
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row" style="width: 300px">
                                <?php _e( 'Refresh mechanism', 'bp-better-messages' ); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <fieldset>
                                        <legend class="screen-reader-text">
                                            <span><?php _e( 'Refresh mechanism', 'bp-better-messages' ); ?></span></legend>
                                        <label><input type="radio" name="mechanism" value="ajax" <?php checked( $this->settings[ 'mechanism' ], 'ajax' ); ?> <?php if($websocket_allowed) echo 'disabled'; ?>> <?php _e( 'AJAX', 'bp-better-messages' ); ?>
                                        </label>
                                        <br>
                                        <label><input type="radio" name="mechanism" value="websocket" <?php checked( $this->settings[ 'mechanism' ], 'websocket' ); ?> <?php if(! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium()) echo 'disabled'; ?>>
                                            <?php _e( 'WebSocket', 'bp-better-messages' ); ?>
                                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                                        </label>
                                    </fieldset>
                                </fieldset>
                            </td>
                        </tr>

                        <tr class="ajax"
                            style="<?php if ( $this->settings[ 'mechanism' ] == 'websocket' ) echo 'display:none;'; ?>">
                            <th scope="row">
                                <?php _e( 'Thread Refresh Interval', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Ajax check interval on open thread', 'wp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e( 'Thread Refresh Interval', 'bp-better-messages' ); ?></span></legend>
                                    <label><input type="number" name="thread_interval" value="<?php echo esc_attr( $this->settings[ 'thread_interval' ] ); ?>"></label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr class="ajax"
                            style="<?php if ( $this->settings[ 'mechanism' ] == 'websocket' ) echo 'display:none;'; ?>">
                            <th scope="row">
                                <?php _e( 'Site Refresh Interval', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Ajax check interval on other sites pages', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e( 'Thread Refresh Interval', 'bp-better-messages' ); ?></span></legend>
                                    <label><input type="number" name="site_interval" value="<?php echo esc_attr( $this->settings[ 'site_interval' ] ); ?>"></label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr style="<?php if ( $this->settings[ 'mechanism' ] != 'websocket' ) echo 'display:none;'; ?>">
                            <th scope="row">
                                <?php _e( 'Enable Encryption', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Encrypts all sensitive content before transfer to websocket server and decrypt on client site with special secret keys not known by our side.', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <fieldset>
                                    <input name="encryptionEnabled" type="checkbox" <?php checked( $this->settings[ 'encryptionEnabled' ], '1' ); ?> value="1" /></label>
                                </fieldset>
                            </td>
                        </tr>


                        <tr>
                            <th scope="row" style="width: 300px">
                                <?php _e( 'Messages styling', 'bp-better-messages' ); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <fieldset>
                                        <label><input type="radio" name="template" value="standard" <?php checked( $this->settings[ 'template' ], 'standard' ); ?>>
                                            <?php _e( 'Standard', 'bp-better-messages' ); ?>
                                        </label>
                                        <br>
                                        <label><input type="radio" name="template" value="modern" <?php checked( $this->settings[ 'template' ], 'modern' ); ?>>
                                            <?php _e( 'Modern', 'bp-better-messages' ); ?>
                                        </label>
                                    </fieldset>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row" style="width: 300px">
                                <?php _e( 'Modern messages layout', 'bp-better-messages' ); ?>
                            </th>
                            <td>
                                <fieldset>
                                    <fieldset>
                                        <label><input type="radio" name="modernLayout" value="left" <?php checked( $this->settings[ 'modernLayout' ], 'left' ); ?>>
                                            <?php _e( 'My messages at left side', 'bp-better-messages' ); ?>
                                        </label>
                                        <br>
                                        <label><input type="radio" name="modernLayout" value="right" <?php checked( $this->settings[ 'modernLayout' ], 'right' ); ?>>
                                            <?php _e( 'My messages at right side', 'bp-better-messages' ); ?>
                                        </label>
                                        <br>
                                        <label><input type="radio" name="modernLayout" value="leftAll" <?php checked( $this->settings[ 'modernLayout' ], 'leftAll' ); ?>>
                                            <?php _e( 'All messages at left side', 'bp-better-messages' ); ?>
                                        </label>
                                    </fieldset>
                                </fieldset>
                            </td>
                        </tr>

                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Modern messages roundness', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Bigger value will result to more rounded borders', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input type="number" name="modernBorderRadius" min="0" max="30" value="<?php echo esc_attr( $this->settings[ 'modernBorderRadius' ] ); ?>">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e( 'User Statuses', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Allow users to set their status: Online, Away or Do not disturb', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <fieldset>
                                    <label>
                                        <input type="checkbox" name="userStatuses" <?php checked( $this->settings[ 'userStatuses' ], '1' ); ?> value="1" <?php if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
                                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                                    </label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e( 'Number of Messages', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Number of Messages per request on user open thread or loading old messages through ajax', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text">
                                        <span><?php _e( 'Thread Refresh Interval', 'bp-better-messages' ); ?></span></legend>
                                    <label><input type="number" name="messagesPerPage" value="<?php echo esc_attr( $this->settings[ 'messagesPerPage' ] ); ?>"></label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Better Messages Location', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Choose the page where Better Messages will be located', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <?php
                                $defaults = array(
                                    'depth'                 => 0,
                                    'child_of'              => 0,
                                    'selected'              => 0,
                                    'echo'                  => 1,
                                    'name'                  => 'page_id',
                                    'id'                    => '',
                                    'class'                 => '',
                                    'show_option_none'      => '',
                                    'show_option_no_change' => '',
                                    'option_none_value'     => '',
                                    'value_field'           => 'ID',
                                );

                                $parsed_args = wp_parse_args( array(
                                    'show_option_none' => __('Show in BuddyPress profile', 'bp-better-messages'),
                                    'name' => 'chatPage',
                                    'selected' => $this->settings[ 'chatPage' ],
                                    'option_none_value' => '0'
                                ), $defaults );

                                global $sitepress;
                                if( defined('ICL_LANGUAGE_CODE') && !! $sitepress ){
                                    $backup_code = ICL_LANGUAGE_CODE;
                                    $default_code = $sitepress->get_default_language();
                                    $sitepress->switch_lang( $default_code );
                                    $pages  = get_pages( $parsed_args );
                                    $sitepress->switch_lang( $backup_code );
                                } else {
                                    $pages  = get_pages( $parsed_args );
                                }

                                // Back-compat with old system where both id and name were based on $name argument.
                                if ( empty( $parsed_args['id'] ) ) {
                                    $parsed_args['id'] = $parsed_args['name'];
                                }

                                $output = "<select name='" . esc_attr( $parsed_args['name'] ) . "' id='" . esc_attr( $parsed_args['id'] ) . "'>\n";

                                if ( $parsed_args['show_option_none'] ) {
                                    $output .= "\t<option value=\"" . esc_attr( $parsed_args['option_none_value'] ) . '">' . $parsed_args['show_option_none'] . "</option>\n";
                                }

                                if( class_exists('AsgarosForum') ) {
                                    $output .= "\t<option value=\"asgaros-forum\" " . selected($parsed_args['selected'], 'asgaros-forum', false) . ">" . __('Show in Asgaros Forum Profile') . "</option>\n";
                                }

                                if( class_exists('WooCommerce') ) {
                                    $output .= "\t<option value=\"woocommerce\" " . selected($parsed_args['selected'], 'woocommerce', false) . ">" . __('Show in WooCommerce My Account') . "</option>\n";
                                }



                                if ( ! empty( $pages ) ) {
                                    $output .= walk_page_dropdown_tree( $pages, $parsed_args['depth'], $parsed_args );
                                }

                                $output .= "</select>\n";

                                echo $output;
                                ?>

                                <p><?php echo sprintf(__('You can use <code>%s</code> shortcode to place chat in specific place of your selected page, if you not used this shortcode all page content will be replaced.', 'bp-better-messages'), '[bp-better-messages]'); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <?php _e( 'BuddyPress Profile Slug', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Change messages tab URL slug in BuddyPress profile ("messages" slug is not allowed)', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <fieldset>
                                    <label><input type="text" name="bpProfileSlug" value="<?php echo esc_attr( $this->settings[ 'bpProfileSlug' ] ); ?>"></label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Compatibility Mode', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Enable this option if shortcode displayed instead of plugin in BuddyPress profile/groups', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input type="checkbox" name="compatibilityMode" <?php checked( $this->settings[ 'compatibilityMode' ], '1' ); ?> value="1" />
                            </td>
                        </tr>
                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Combined View', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Always show threads list on left side of thread', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input name="combinedView" type="checkbox" <?php checked( $this->settings[ 'combinedView' ], '1' ); ?> value="1" />
                            </td>
                        </tr>
                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Block Scroll on Hover', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'When hovering messages container scroll of the site will be disabled to improve user experience while using messages', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input name="blockScroll" type="checkbox" <?php checked( $this->settings[ 'blockScroll' ], '1' ); ?> value="1" />
                            </td>
                        </tr>
                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Show My Profile Button', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Show my profile button at the messages bottom part', 'bp-better-messages' ); ?></p>

                            </th>
                            <td>
                                <input name="myProfileButton" type="checkbox" <?php checked( $this->settings[ 'myProfileButton' ], '1' ); ?> value="1" />
                            </td>
                        </tr>
                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Show Private Message Link at Members List', 'bp-better-messages' ); ?>
                            </th>
                            <td>
                                <input name="userListButton" type="checkbox" <?php checked( $this->settings[ 'userListButton' ], '1' ); ?> value="1" />
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <a class="bp-better-messages-facebook" href="https://www.facebook.com/groups/bpbettermessages/" target="_blank"><span class="dashicons dashicons-facebook"></span> Join Facebook Group</a>

                    <?php
                    if( ! bpbm_fs()->is_trial_utilized() && ! bpbm_fs()->can_use_premium_code() ){
                        $url = bpbm_fs()->get_trial_url();
                        echo '<br><a class="bp-better-messages-trial" href="' . $url . '">Start Websocket 3 Days Trial</a>';
                    }
                    ?>
                    <?php if( bpbm_fs()->is_premium() && ! bpbm_fs()->can_use_premium_code() ){ ?>
                        <div class="bp-better-messages-connection-check bpbm-error">
                            <p><?php _e('This website using WebSocket plugin version, but has no active license attached.', 'bp-better-messages'); ?></p>
                            <p><?php echo sprintf(__('If you have license and it must be attached to this website, try to press sync button in <a href="%s">your account</a>.', 'bp-better-messages'), admin_url('admin.php?page=bp-better-messages-account')); ?></p>
                        </div>
                    <?php } else if( bpbm_fs()->can_use_premium_code() ){
                    if( ! class_exists('BP_Better_Messages_Premium') ) {  ?>
                        <div class="bp-better-messages-connection-check bpbm-error">
                            <p><?php echo sprintf(__('Seems like this website has active WebSocket License, but you are still using free version of plugin. Try to download and install plugin from <a href="%s">your account</a> page.', 'bp-better-messages'), admin_url('admin.php?page=bp-better-messages-account')); ?></p>
                        </div>
                    <?php
                    } else { ?>
                        <div class="bp-better-messages-connection-check">
                            <p><?php echo sprintf(__('This website has domain name <b>%s</b>', 'bp-better-messages'), BP_Better_Messages_Premium()->site_id); ?></p>
                            <p class="bpbm-checking-sync"><span class="dashicons dashicons-update-alt" style="animation:bpbm-spin 4s linear infinite;"></span> <?php _e('Double-checking if WebSocket server know about this domain and sync is fine', 'bp-better-messages'); ?></p>
                        </div>
                        <script type="text/javascript">
                            jQuery(document).ready(function($){
                                var checking = $('.bpbm-checking-sync');
                                $.post('https://license.bpbettermessages.com/checksync.php', {
                                    domain     : '<?php echo BP_Better_Messages_Premium()->site_id; ?>',
                                    secret_key : '<?php echo base64_encode(BP_Better_Messages_Premium()->secret_key); ?>'
                                }, function(response){
                                    if( response ){
                                        checking.parent().addClass('bpbm-ok');
                                        checking.html('<span class="dashicons dashicons-yes-alt"></span> <?php esc_attr_e('All good, WebSocket server know about this domain, all should be working good.', 'bp-better-messages'); ?>');
                                    } else {
                                        checking.parent().addClass('bpbm-error');
                                        checking.html('<span class="dashicons dashicons-dismiss"></span> <?php esc_attr_e('Oh no, WebSocket server dont know about this domain, realtime functionality will not work. If you just activated the license at this website wait 5-15 mins and check it again.', 'bp-better-messages'); ?>');
                                    }
                                });
                            });
                        </script>
                    <?php } } ?>

                    <?php
                    if( ( ! defined('DISABLE_WP_CRON') || DISABLE_WP_CRON !== true ) ){ ?>
                        <p style="<?php if ( $this->settings[ 'mechanism' ] != 'websocket' ) echo 'display:none;'; ?>color: #856404;background-color: #fff3cd;border: 1px solid #f9e4a6;padding: 15px;line-height: 24px;max-width: 550px;">
                            <?php _e('For the best performance disable WP Cron and schedule it with your server scheduler.', 'bp-better-messages'); ?><br>
                            <a href="https://www.wordplus.org/disablewpcron" target="_blank"><?php _e('How to Disable WP-Cron for Faster Performance', 'bp-better-messages'); ?></a><br>
                            <small><?php _e('This message will disappear when WP Cron is disabled.', 'bp-better-messages'); ?></small>
                        </p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div id="chat" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Easy Start Thread', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'When clicking the Private Message button user will be immediately redirected to new thread instead of new message screen', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="fastStart" type="checkbox" <?php checked( $this->settings[ 'fastStart' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable replies to messages', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Users will be able to select messages to reply', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enableReplies" type="checkbox" <?php checked( $this->settings[ 'enableReplies' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Allow users to edit messages', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow users to edit their messages only', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="allowEditMessages" type="checkbox" <?php checked( $this->settings[ 'allowEditMessages' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Allow users to delete messages', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow users to delete their messages only', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="allowDeleteMessages" type="checkbox" <?php checked( $this->settings[ 'allowDeleteMessages' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Threads with multiple participants', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Don`t allow to create threads with multiple participants', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableGroupThreads" type="checkbox"  <?php checked( $this->settings[ 'disableGroupThreads' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Allow users to leave threads with multiple participants', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow users to leave threads with multiple participants (creator can`t leave thread he started)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="allowGroupLeave" type="checkbox"  <?php checked( $this->settings[ 'allowGroupLeave' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Multiple Threads', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'This will prevent users from starting few threads with same user', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="singleThreadMode" type="checkbox"  <?php checked( $this->settings[ 'singleThreadMode' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Auto Redirect to Existing Thread', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'This will redirect user to existing thread with another user if they already have thread and Disable Multiple Threads is enabled', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="redirectToExistingThread" type="checkbox"  <?php checked( $this->settings[ 'redirectToExistingThread' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <?php _e( 'Mini Friends', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enables mini friends list widget fixed to the bottom of browser window', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Mini Friends', 'bp-better-messages' ); ?></span></legend>
                            <label>
                                <input type="checkbox" name="miniFriendsEnable" <?php disabled( ! function_exists('friends_get_friend_user_ids') ); ?> <?php checked( $this->settings[ 'miniFriendsEnable' ] && function_exists('friends_get_friend_user_ids'), '1' ); ?> value="1">
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Mini Threads', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enables mini threads list widget fixed to the bottom of browser window', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Small Chats', 'bp-better-messages' ); ?></span></legend>
                            <label>
                                <input type="checkbox" name="miniThreadsEnable" <?php checked( $this->settings[ 'miniThreadsEnable' ], '1' ); ?> value="1" <?php if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
                                <?php BP_Better_Messages()->functions->license_proposal(); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Mini Chats', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enables mini chats fixed to the bottom of browser window', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Small Chats', 'bp-better-messages' ); ?></span></legend>
                            <label>
                                <input type="checkbox" name="miniChatsEnable" <?php checked( $this->settings[ 'miniChatsEnable' ], '1' ); ?> value="1" <?php if(! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
                                <?php BP_Better_Messages()->functions->license_proposal(); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Messages Status', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enable messages status functionality', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="messagesStatus" <?php checked( $this->settings[ 'messagesStatus' ], '1' ); ?> value="1" <?php if(! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
                                <?php BP_Better_Messages()->functions->license_proposal(); ?>
                            </label>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable additional security check when deleting thread', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Check this if you have issue with thread deleting', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableDeleteThreadCheck" type="checkbox" <?php checked( $this->settings[ 'disableDeleteThreadCheck' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable users search', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disables suggestions when starting new thread', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableUsersSearch" type="checkbox" <?php checked( $this->settings[ 'disableUsersSearch' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Search all users', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enable search among all users when starting new thread', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="searchAllUsers" type="checkbox" <?php if($this->settings[ 'disableUsersSearch' ] === '1') echo 'disabled'; ?> <?php checked( $this->settings[ 'searchAllUsers' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable oEmbed for popular services', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'oEmbed YouTube, Vimeo, VideoPress, Flickr, DailyMotion, Kickstarter, Meetup.com, Mixcloud, SoundCloud and more', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="oEmbedEnable" type="checkbox" <?php checked( $this->settings[ 'oEmbedEnable' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable nice links', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Nice links finds link in user messages, fetching title and description if available and shows it at the bottom of message', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enableNiceLinks" type="checkbox" <?php checked( $this->settings[ 'enableNiceLinks' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Subject', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disable Subject when starting new thread', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableSubject" type="checkbox" <?php checked( $this->settings[ 'disableSubject' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Send on Enter for Desktop devices', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="disableEnterForDesktop" type="checkbox" <?php checked( $this->settings[ 'disableEnterForDesktop' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable onsite notifications', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disable onsite notifications (black notifications in right corner)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableOnSiteNotification" type="checkbox" <?php checked( $this->settings[ 'disableOnSiteNotification' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="groups" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Messages for Groups', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enable messages for BuddyPress groups', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enableGroups" type="checkbox" <?php if ( ! bp_is_active( 'groups' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'enableGroups' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Mini Groups', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enables mini groups widget fixed to the bottom of browser window', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enableMiniGroups" type="checkbox" <?php if ( ! bp_is_active( 'groups' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'enableMiniGroups' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="mobile" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Mobile Chat at Any Page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Adds button fixed to the right corner on mobile devices, on click fully featured messaging will appear in full screen mode', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="mobilePopup" type="checkbox" <?php checked( $this->settings[ 'mobilePopup' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Full Screen Mode for Touch Screens', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="mobileFullScreen" type="checkbox" <?php checked( $this->settings[ 'mobileFullScreen' ], '1' ); ?> value="1" />
                        <p style="font-size: 10px;color: green;"><strong><?php _e( 'Recommended', 'bp-better-messages' ); ?></strong></p>
                    </td>
                </tr>


                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Auto open full screen mode when opening messages page', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="autoFullScreen" type="checkbox" <?php checked( $this->settings[ 'autoFullScreen' ], '1' ); ?> <?php if($this->settings[ 'mobileFullScreen' ] == '0') echo 'disabled'; ?> value="1" />
                        <p style="font-size: 10px;color: green;"><strong><?php _e( 'Recommended', 'bp-better-messages' ); ?></strong></p>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Send on Enter for Touch Screens', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="disableEnterForTouch" type="checkbox" <?php checked( $this->settings[ 'disableEnterForTouch' ], '1' ); ?> <?php if($this->settings[ 'mobileFullScreen' ] == '0') echo 'disabled'; ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Tap to Open for Touch Screens', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="disableTapToOpen" type="checkbox" <?php checked( $this->settings[ 'disableTapToOpen' ], '1' ); ?> <?php if($this->settings[ 'mobileFullScreen' ] == '0') echo 'disabled'; ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Emoji Selector in mobile view', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="mobileEmojiEnable" type="checkbox" <?php checked( $this->settings[ 'mobileEmojiEnable' ], '1' ); ?> value="1" />
                        <p style="font-size: 10px;color: red;"><strong><?php _e( 'Not recommended!', 'bp-better-messages' ); ?></strong></p>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <div id="attachments" class="bpbm-tab">
            <?php $formats = wp_get_ext_types(); unset($formats['code']); ?>
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable files', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enable file sharing between users', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="attachmentsEnable" type="checkbox" <?php checked( $this->settings[ 'attachmentsEnable' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Hide Attachments', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Hides attachments from media gallery', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="attachmentsHide" type="checkbox" <?php checked( $this->settings[ 'attachmentsHide' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Random file names', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Changes file names to random to improve users privacy', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="attachmentsRandomName" type="checkbox" <?php checked( $this->settings[ 'attachmentsRandomName' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Delete attachment after', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="attachmentsRetention" type="number" value="<?php esc_attr_e( $this->settings[ 'attachmentsRetention' ] ); ?>"/> days
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Max attachment size', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="attachmentsMaxSize" type="number" value="<?php esc_attr_e( $this->settings[ 'attachmentsMaxSize' ] ); ?>"/> Mb
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php _e( 'Allowed formats', 'bp-better-messages' ); ?>
                    </th>
                    <td class="attachments-formats">
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e( 'Allowed formats', 'bp-better-messages' ); ?></span>
                            </legend>
                            <?php foreach($formats as $type => $extensions){ ?>
                                <ul>
                                    <strong><?php echo ucfirst($type); ?></strong>
                                    <?php foreach($extensions as $ext){ ?>
                                        <li>
                                            <label>
                                                <input type="checkbox" name="attachmentsFormats[]" value="<?php echo $ext; ?>" <?php if(in_array($ext, $this->settings[ 'attachmentsFormats' ])) echo 'checked="checked"'; ?>>
                                                <?php echo $ext; ?>
                                            </label>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </fieldset>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="notifications" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Title notifications', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Show unread threads number in website title (browser tab)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="titleNotifications" type="checkbox" <?php checked( $this->settings[ 'titleNotifications' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Mute Threads', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'When enabled users will be able to mute threads', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="allowMuteThreads" type="checkbox" <?php checked( $this->settings[ 'allowMuteThreads' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Replace Standard BuddyPress Email Notifications', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'When enabled instead of standard notification on each new message, plugin will group messages by thread and send it every 15 minutes with cron job.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="replaceStandardEmail" type="checkbox" <?php checked( $this->settings[ 'replaceStandardEmail' ], '1' ); ?> <?php  if( ! function_exists('bp_send_email') ) echo 'disabled'; ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Send notifications every (minutes)', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Set to 0 to disable', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="notificationsInterval" value="<?php echo esc_attr( $this->settings[ 'notificationsInterval' ] ); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Stop messages notifications to be added to BuddyPress Notifications Bell', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'This will work only with setting above', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="stopBPNotifications" type="checkbox" <?php checked( $this->settings[ 'stopBPNotifications' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Browser Push Notifications', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow users to enable web push notifications, so they can receive messages even with closed browser', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _e( 'Supported in all major browsers like: Chrome, Opera, Firefox, IE, Edge and others', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enablePushNotifications" type="checkbox" <?php checked( $this->settings[ 'enablePushNotifications' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="rules" class="bpbm-tab">
            <?php
            $roles = get_editable_roles();
            if(isset($roles['administrator'])) unset( $roles['administrator'] );
            ?>
            <table class="form-table">
                <tbody>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Only Friends Mode', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow only friends to send messages each other', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="friendsMode" type="checkbox" <?php disabled( ! function_exists('friends_check_friendship') ); ?>  <?php checked( $this->settings[ 'friendsMode' ] && function_exists('friends_check_friendship'), '1' ); ?> value="1" />
                    </td>
                </tr>


                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Rate limiting new threads (seconds)', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Limit new threads creation to prevent users spam', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _e( 'Set to 0 to disable', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="rateLimitNewThread" value="<?php echo esc_attr( $this->settings[ 'rateLimitNewThread' ] ); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Restrict users from deleting threads', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disable users from being able to delete thread (admin always can delete)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="restrictThreadsDeleting" type="checkbox" <?php checked( $this->settings[ 'restrictThreadsDeleting' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Rate limiting for new replies', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Limit max amount of replies within timeframe', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <div class="bp-better-messages-roles-list">
                            <table style="width: 100%">
                                <thead>
                                    <tr>
                                        <th><?php _e('Role', 'bp-better-messages'); ?></th>
                                        <th><?php _e('Limitation (0 to disable)', 'bp-better-messages'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach( $roles as $slug => $role ){
                                    $value = 0;
                                    $type  = 'hour';

                                    if( isset($this->settings['rateLimitReply'][$slug])){
                                        $value = $this->settings['rateLimitReply'][$slug]['value'];
                                        $type  = $this->settings['rateLimitReply'][$slug]['type'];
                                    }
                                    ?>
                                    <tr>
                                        <td><?php echo $role['name']; ?></td>
                                        <td>
                                            <input name="rateLimitReply[<?php echo $slug; ?>][value]" type="number" min="0" value="<?php esc_attr_e($value); ?>">
                                            <span><?php _e('messages per', 'bp-better-messages'); ?></span>
                                            <select name="rateLimitReply[<?php echo $slug; ?>][type]">
                                                <option value="hour" <?php selected( $type, 'hour' ); ?>><?php _e('Hour', 'bp-better-messages'); ?></option>
                                                <option value="day" <?php selected( $type, 'day' ); ?>><?php _e('Day', 'bp-better-messages'); ?></option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Rate limiting for new replies message', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input type="text" style="width: 100%" name="rateLimitReplyMessage" value="<?php esc_attr_e($this->settings['rateLimitReplyMessage']); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict the creation of a new thread', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Selected roles will not be allowed to start new threads', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <ul class="bp-better-messages-roles-list">
                            <?php foreach( $roles as $slug => $role ){ ?>
                                <li><input id="<?php echo $slug; ?>_1" type="checkbox" name="restrictNewThreads[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictNewThreads' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_1"><?php echo $role['name']; ?></label></li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict the creation of a new thread message', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input id="<?php echo $slug; ?>_2" type="text" style="width: 100%" name="restrictNewThreadsMessage" value="<?php esc_attr_e($this->settings['restrictNewThreadsMessage']); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Remove new thread button for restricted users', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="restrictNewThreadsRemoveNewThreadButton" type="checkbox" <?php checked( $this->settings[ 'restrictNewThreadsRemoveNewThreadButton' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict new replies', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Selected roles will not be allowed to reply', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <ul class="bp-better-messages-roles-list">
                            <?php foreach( $roles as $slug => $role ){ ?>
                                <li><input id="<?php echo $slug; ?>_3" type="checkbox" name="restrictNewReplies[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictNewReplies' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_3"><?php echo $role['name']; ?></label></li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict new replies message', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input id="<?php echo $slug; ?>_4" type="text" style="width: 100%" name="restrictNewRepliesMessage" value="<?php esc_attr_e($this->settings['restrictNewRepliesMessage']); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict from viewing message', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Selected roles will see message configured below instead of real message', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <ul class="bp-better-messages-roles-list">
                            <?php foreach( $roles as $slug => $role ){ ?>
                                <li><input id="<?php echo $slug; ?>_5" type="checkbox" name="restrictViewMessages[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictViewMessages' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_5"><?php echo $role['name']; ?></label></li>
                            <?php } ?>
                        </ul>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict viewing message', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input id="<?php echo $slug; ?>_6" type="text" style="width: 100%" name="restrictViewMessagesMessage" value="<?php esc_attr_e($this->settings['restrictViewMessagesMessage']); ?>">
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="calls" class="bpbm-tab">
            <?php if(bpbm_fs()->can_use_premium_code() && ! is_ssl() ){ ?>
                <div class="bp-better-messages-connection-check bpbm-error" style="margin: 20px 0;">
                    <p><?php esc_attr_e('<strong>Website must to have SSL certificate</strong> in order to audio and video calls work.', 'bp-better-messages'); ?></p>
                    <p><?php esc_attr_e('This is security requirements by browsers. Contact your hosting company to enable SSL certificate at your website.', 'bp-better-messages'); ?></p>
                    <p><small><?php esc_attr_e('This notice will be hidden when website will work via HTTPS', 'bp-better-messages'); ?></small></p>
                </div>
            <?php } ?>

            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Video Calls', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow users to make video calls between each other', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _e( 'Video calls are possible only with websocket version, its using most secure and modern WebRTC technology to empower video chats.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="videoCalls" type="checkbox" <?php checked( $this->settings[ 'videoCalls' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Enable Audio Calls', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow users to make audio calls between each other', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _e( 'Audio calls are possible only with websocket version, its using most secure and modern WebRTC technology to empower audio calls.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="audioCalls" type="checkbox" <?php checked( $this->settings[ 'audioCalls' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Revert Mute Voice & Hide Video icons', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Makes mute and hide video icons to appear in reverse way', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="callsRevertIcons" type="checkbox" <?php checked( $this->settings[ 'callsRevertIcons' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Call time limit before call marked as missed (seconds)', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text">
                                <span><?php _e( 'Call Request Time Limit', 'bp-better-messages' ); ?></span></legend>
                            <label>
                                <input type="number" name="callRequestTimeLimit" value="<?php echo esc_attr( $this->settings[ 'callRequestTimeLimit' ] ); ?>" <?php if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?>>
                            </label>
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Missed call message when user was offline', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Leaving message about missed call for user if user was offline at that moment.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="offlineCallsNotifications" type="checkbox" <?php checked( $this->settings[ 'offlineCallsNotifications' ], '1' ); ?> value="1" <?php if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Limit calls only to the friends', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow only friends to make calls between each other (admins always can call)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="callsLimitFriends" type="checkbox" <?php disabled( ! function_exists('friends_get_friend_user_ids') ); ?> <?php checked( $this->settings[ 'callsLimitFriends' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Audio Call button in user profile', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Add audio call button to user profile', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="profileAudioCall" type="checkbox" <?php checked( $this->settings[ 'profileAudioCall' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Video Call button in user profile', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Add video call button to user profile', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="profileVideoCall" type="checkbox" <?php checked( $this->settings[ 'profileVideoCall' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Audio Call button in mini chats', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Add audio call button to the mini chat', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="miniChatAudioCall" type="checkbox" <?php checked( $this->settings[ 'miniChatAudioCall' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Video Call button in mini chats', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Add video call button to the mini chat', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="miniChatVideoCall" type="checkbox" <?php checked( $this->settings[ 'miniChatVideoCall' ], '1' ); ?> value="1" <?php  if( ! bpbm_fs()->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                </tbody>
            </table>


            <p style="color: #0c5460;background-color: #d1ecf1;border: 1px solid #d1ecf1;padding: 15px;line-height: 24px;max-width: 550px;">
                <a href="https://www.wordplus.org/knowledge-base/how-video-calls-works/" target="_blank">How video/audio calls works?</a><br>
            </p>
        </div>

        <div id="stickers" class="bpbm-tab">
            <h1 style="padding-top: 20px">GIPHY Integration</h1>
            <?php
            $giphy_error = get_option( 'bp_better_messages_giphy_error', false );
            if( !! $giphy_error ){
                echo '<div class="notice notice-error">';
                echo '<p><b>GIPHY Error:</b> ' . $giphy_error . '</p>';
                echo '</div>';
            }
            ?>

            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'GIPHY API Key', 'bp-better-messages' ); ?>
                        <p><?php _e('Leave this field empty to disable giphy', 'bp-better-messages'); ?></p>
                        <p><a href="https://developers.giphy.com/docs/api#quick-start-guide" target="_blank"><?php _e('How to create GIPHY API key', 'bp-better-messages'); ?></a></p>
                    </th>
                    <td>
                        <input name="giphyApiKey" type="text" style="width: 100%"  value="<?php esc_attr_e($this->settings['giphyApiKey']); ?>" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'GIPHY Content rating', 'bp-better-messages' ); ?>
                        <p><?php echo sprintf(__('GIPHY Content Rating <a href="%s" target="_blank">Learn more</a>', 'bp-better-messages'), 'https://developers.giphy.com/docs/optional-settings#rating'); ?></p>
                    </th>
                    <td>
                        <input name="giphyContentRating" type="text" style="width: 100%"  value="<?php esc_attr_e($this->settings['giphyContentRating']); ?>" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'GIPHY Language', 'bp-better-messages' ); ?>
                        <p><?php echo sprintf(__('GIPHY Language <a href="%s" target="_blank">Learn more</a>', 'bp-better-messages'), 'https://developers.giphy.com/docs/optional-settings#language-support'); ?></p>
                    </th>
                    <td>
                        <input name="giphyLanguage" type="text" style="width: 100%"  value="<?php esc_attr_e($this->settings['giphyLanguage']); ?>" />
                    </td>
                </tr>
                </tbody>
            </table>

            <h1>Stipop.io Stickers Integration</h1>
            <p style="font-size: 1.3rem;background: white;border: 1px solid #ccc;padding: 15px;">
                <strong>Stipop.io changed their plans and allows only 500 monthly active users instead of 10000 for free.</strong>
                <br><br>
                If you have more then 500 monthly users active, consider disabling stickers or subscribe to Stipop.io paid options.
                <br><br>
                To activate stickers you need to register <a href="https://www.wordplus.org/stipopregister" target="_blank">here</a> and insert API Key which you will get after registration in the settings below.
            </p>

            <?php
            $stipop_error = get_option( 'bp_better_messages_stipop_error', false );
            if( !! $stipop_error ){
                echo '<div class="notice notice-error">';
                echo '<p><b>Stipop Error:</b> ' . $stipop_error . '</p>';
                echo '</div>';
            }
            ?>
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Stipop.io API Key', 'bp-better-messages' ); ?>
                        <p><?php _e('Leave this field empty to disable stickers', 'bp-better-messages'); ?></p>
                    </th>
                    <td>
                        <input name="stipopApiKey" type="text" style="width: 100%"  value="<?php esc_attr_e($this->settings['stipopApiKey']); ?>" />
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Language', 'bp-better-messages' ); ?>
                        <p><?php _e('Two letter language code for showing stickers which best fits this language', 'bp-better-messages'); ?></p>
                        <p><?php _e('For example (en, ko, es)', 'bp-better-messages'); ?></p>
                    </th>
                    <td>
                        <input name="stipopLanguage" type="text" style="width: 100%"  value="<?php esc_attr_e($this->settings['stipopLanguage']); ?>" />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="customization" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'General Color', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input type="text" name="colorGeneral" class="color-selector" value="<?php esc_attr_e( $this->settings[ 'colorGeneral'] ); ?>" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Add close button to Mini Threads & Friends Widget', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="enableMiniCloseButton" type="checkbox" <?php checked( $this->settings[ 'enableMiniCloseButton' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Mini Threads & Friends height', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Mini windows height in PX.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="miniWindowsHeight" value="<?php echo esc_attr( $this->settings[ 'miniWindowsHeight' ] ); ?>">
                    </td>
                </tr>

                <?php if( $this->settings[ 'mechanism' ] == 'websocket' ) { ?>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Mini Chats Height', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Mini chats height in PX.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="miniChatsHeight" value="<?php echo esc_attr( $this->settings[ 'miniChatsHeight' ] ); ?>">
                    </td>
                </tr>
                <?php } ?>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Fixed Header Height', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'If your website has fixed header specify its height in PX.', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _e( 'This needed for correct scrolling in some cases.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="fixedHeaderHeight" value="<?php echo esc_attr( $this->settings[ 'fixedHeaderHeight' ] ); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Max Height of Messages Container', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Max Height of Messages Container in PX.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="messagesHeight" value="<?php echo esc_attr( $this->settings[ 'messagesHeight' ] ); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Allow to disable sound notification' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow user disable sound notifications in their user settings', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="allowSoundDisable" type="checkbox" <?php checked( $this->settings[ 'allowSoundDisable' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Search', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disables search functionality', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableSearch" type="checkbox" <?php checked( $this->settings[ 'disableSearch' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable Favorite Messages', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disables favorite messages functionality', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableFavoriteMessages" type="checkbox" <?php checked( $this->settings[ 'disableFavoriteMessages' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable User Settings', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disables settings button in the messages header', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableUserSettings" type="checkbox" <?php checked( $this->settings[ 'disableUserSettings' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable New Threads Screen', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disables new thread button and screen (admin will always see it)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableNewThread" type="checkbox" <?php checked( $this->settings[ 'disableNewThread' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="sounds" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Message notification sound volume', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'From 0 to 100 (0 to disable)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="notificationSound" min="0" max="100" value="<?php echo esc_attr( $this->settings[ 'notificationSound' ] ); ?>">
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Message sent sound volume', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'From 0 to 100 (0 to disable)', 'bp-be    tter-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="sentSound" min="0" max="100" value="<?php echo esc_attr( $this->settings[ 'sentSound' ] ); ?>">
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Incoming call sound volume', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'From 0 to 100 (0 to disable)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="callSound" min="0" max="100" value="<?php echo esc_attr( $this->settings[ 'callSound' ] ); ?>" <?php  if( ! bpbm_fs()->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> >
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="export-import" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 150px;">
                        <?php _e( 'Export Settings', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Copy settings, so you can import them later to another website', 'bp-better-messages'); ?></p>
                    </th>
                    <td>
                        <?php $options = get_option( 'bp-better-chat-settings', array() ); ?>
                        <textarea id="export-settings" readonly style="width: 100%;height: 200px;" onclick="this.focus();this.select()"><?php echo base64_encode(json_encode($options)); ?></textarea>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 150px;">
                        <?php _e( 'Import Settings', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Paste settings copied before', 'bp-better-messages'); ?></p>
                    </th>
                    <td>
                        <textarea id="bpbm-import-area" style="width: 100%;height: 200px;"></textarea>
                        <button id="bpbm-import-settings" class="button" style="display:none;">Import</button>

                        <script type="text/javascript">

                            jQuery('#bpbm-import-area').change(function( event ){
                                var settings = jQuery(this).val();

                                if( settings.trim() === '' ){
                                    jQuery('#bpbm-import-settings').hide();
                                } else {
                                    jQuery('#bpbm-import-settings').show();
                                }
                            });

                            jQuery('#bpbm-import-settings').click(function( event ){
                                event.preventDefault();
                                var settingsArea = jQuery('#bpbm-import-area');
                                var settings = settingsArea.val();

                                if( settings.trim() !== '' ){
                                    jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                        'action'   : 'bp_messages_admin_import_options',
                                        'settings' : settings,
                                        'nonce'    : '<?php echo wp_create_nonce( 'bpbm-import-options' ); ?>'
                                    }, function(response){
                                        alert(response.data);
                                        if( response.success ){
                                            location.reload();
                                        }
                                    });
                                }
                            });
                        </script>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="shortcodes" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 350px;">
                        <?php _e( 'Unread messages counter', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Show unread messages counter anywhere in your website', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('To add this shortcode to your menu item you can use <a href="https://wordpress.org/plugins/shortcode-in-menus/" target="_blank">Shortcode in Menus</a> plugin.', 'bp-better-messages'); ?></p>
                    </th>
                    <td>
                        <input readonly type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_unread_counter hide_when_no_messages="1" preserve_space="1"]'>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 350px;">
                        <?php _e( 'My messages URL', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Return url to logged in user inbox', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php
                            $result = do_shortcode('[bp_better_messages_my_messages_url]');
                            if( ! empty( $result ) ) {
                                _e('For example: ', 'bp-better-messages');
                                echo '<strong>' . $result . '</strong>';
                            }
                        ?></p>
                    </th>
                    <td>
                        <input readonly type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_my_messages_url]'>
                    </td>
                </tr>


                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 350px;">
                        <?php _e( 'Private Message Button', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Shows private message button', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('This shortcode will try to find user_id from environment, for example author of post and display Private Message button.', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('If user_id not found it will not display anything. You can force user id with user_id="1" attribute.', 'bp-better-messages'); ?></p>
                    </th>
                    <td>
                        <input readonly type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_pm_button text="Private Message" subject="Have a question to you" message="Lorem Ipsum is simply dummy text of the printing and typesetting industry." target="_self" class="extra-class" fast_start="0"]'>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
        <p class="submit">
            <input type="submit" name="save" id="submit" class="button button-primary"
                   value="<?php _e( 'Save Changes', 'bp-better-messages' ); ?>">
        </p>
    </form>
</div>