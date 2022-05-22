<?php
/**
 * Settings page
 */
defined( 'ABSPATH' ) || exit;

$websocket_allowed = BP_Better_Messages()->functions->can_use_premium_code_premium_only();

$all_roles = get_editable_roles();
$roles = $all_roles;
if (isset($roles['administrator'])) unset($roles['administrator']);
?>
<style type="text/css">
    .bpbm-tab{
        display: none;
    }

    .bpbm-tab.active{
        display: block;
    }

    .bpbm-subtab{
        display: none;
    }

    .bpbm-subtab.active{
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

    .cols .col.secondary-col{
        padding-left: 2%;
    }

    @media only screen and (max-width: 1050px){
        .cols .col{
            width: 100%;
            float: none;
            padding-left: 0 !important;
        }
    }


    .bm-switcher-table{
        width: auto;
    }

    @media only screen and (min-width: 783px) {
        .bm-switcher-table td {
            padding-left: 20px;
            width: 1px;
        }

        .bm-switcher-table th {
            padding-right: 20px;
        }
    }


    @media only screen and (max-width: 782px) {
        .bm-switcher-table{
            padding: 10px !important;
        }
    }

    .bpbm-tab .form-table th{
        width: auto;
    }

    .bpbm-tab#customization .form-table th{
        width: 200px;
    }

    .bpbm-subtab .form-table th{
        width: auto;
    }

    .bpbm-subtab#customization .form-table th{
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
        margin-top: 22px;
    }


    .bp-better-messages-roadmap,
    .bp-better-messages-roadmap:hover,
    .bp-better-messages-roadmap:focus{
        background: #3b3d89;
        display: inline-block;
        width: 300px;
        max-width: 100%;
        text-align: center;
        color: white;
        cursor: pointer;
        text-decoration: none;
        padding: 10px;
        font-size: 16px;
        margin-top: 10px;
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

    .role-block-empty + table{
        display: none;
    }

    .delete-row{
        cursor: pointer;
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

        $(".bpbm-sub-tabs > a").on('click touchstart', function(event){
            event.preventDefault();
            event.stopPropagation();

            if( $(this).hasClass('nav-tab-active') ) return false;

            var container = $(this).closest('.bpbm-tab');
            var selector = $(this).attr('href');

            container.find('.bpbm-sub-tabs > a').removeClass('nav-tab-active');
            container.find('.bpbm-subtab').removeClass('active');

            $(this).addClass('nav-tab-active');
            container.find(selector).addClass('active');
        });

        $('.color-selector').wpColorPicker();
    });
</script>
<div class="wrap">
    <h1><?php _e( 'Better Messages', 'bp-better-messages' ); ?></h1>
    <div class="nav-tab-wrapper" id="bpbm-tabs">
        <a class="nav-tab nav-tab-active" id="general-tab" href="#general"><?php _e( 'General', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="chat-tab" href="#chat"><?php _e( 'Messages', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="integrations-tab" href="#integrations"><?php _e( 'Integrations', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="mini-widgets-tab" href="#mini-widgets"><?php _e( 'Mini Widgets', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="mobile-tab" href="#mobile"><?php _e( 'Mobile', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="attachments-tab" href="#attachments"><?php _e( 'Attachments', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="notifications-tab" href="#notifications"><?php _e( 'Notifications', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="rules-tab" href="#rules"><?php _e( 'Restrictions', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="sounds-tab" href="#sounds"><?php _e( 'Sounds', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="calls-tab" href="#calls"><?php _e( 'Calls', 'bp-better-messages' ); ?></a>
        <a class="nav-tab" id="group-calls-tab" href="#group-calls"><?php _e( 'Group Calls', 'bp-better-messages' ); ?></a>
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

                                $option_none = __('Select page', 'bp-better-messages');

                                if( class_exists( 'BuddyPress' ) ){
                                    $option_none =  __('Show in BuddyPress profile', 'bp-better-messages');
                                } else if( defined('ultimatemember_version') ){
                                    $option_none =  __('Show in Ultimate Member profile', 'bp-better-messages');
                                }

                                $parsed_args = wp_parse_args( array(
                                    'show_option_none' => $option_none,
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
                                        <label><input type="radio" name="mechanism" value="websocket" <?php checked( $this->settings[ 'mechanism' ], 'websocket' ); ?> <?php if(! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium()) echo 'disabled'; ?>>
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
                                    <input name="encryptionEnabled" type="checkbox" checked disabled value="1" /></label>
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
                                        <input type="checkbox" name="userStatuses" <?php checked( $this->settings[ 'userStatuses' ], '1' ); ?> value="1" <?php if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
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
                                <?php _e( 'Combined View', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Always show threads list on left side of thread', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input name="combinedView" type="checkbox" <?php checked( $this->settings[ 'combinedView' ], '1' ); ?> value="1" />
                            </td>
                        </tr>

                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Full Screen Mode', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Show full screen button for desktop browsers', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input name="desktopFullScreen" type="checkbox" <?php checked( $this->settings[ 'desktopFullScreen' ], '1' ); ?> value="1" />
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

                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Enable Messages Viewer', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Enable messages viewer page in WordPress admin', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input name="messagesViewer" type="checkbox" <?php checked( $this->settings[ 'messagesViewer' ], '1' ); ?> value="1" />
                            </td>
                        </tr>

                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _e( 'Enable Smart Cache', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _e( 'Enable smart cache of some plugin logic that shouldnt be noticed by users, but improve response times', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input name="smartCache" type="checkbox" <?php checked( $this->settings[ 'smartCache' ], '1' ); ?> value="1" />
                            </td>
                        </tr>



                        </tbody>
                    </table>
                </div>
                <div class="col secondary-col">
                    <a class="bp-better-messages-facebook" href="https://www.facebook.com/groups/bpbettermessages/" target="_blank"><span class="dashicons dashicons-facebook"></span> Join Facebook Group</a>
                    <br>
                    <a class="bp-better-messages-roadmap" href="https://www.wordplus.org/roadmap" target="_blank"><span class="dashicons dashicons-schedule"></span> Roadmap & Feature Suggestions</a>

                    <?php
                    if( ! bpbm_fs()->is_trial_utilized() && ! BP_Better_Messages()->functions->can_use_premium_code() ){
                        $url = bpbm_fs()->get_trial_url();
                        echo '<br><a class="bp-better-messages-trial" href="' . $url . '">Start Websocket 3 Days Trial</a>';
                    }
                    ?>
                    <?php if( bpbm_fs()->is_premium() && ! BP_Better_Messages()->functions->can_use_premium_code() ){ ?>
                        <div class="bp-better-messages-connection-check bpbm-error">
                            <p><?php _e('This website using WebSocket plugin version, but has no active license attached.', 'bp-better-messages'); ?></p>
                            <p><?php echo sprintf(__('If you have license and it must be attached to this website, try to press sync button in <a href="%s">your account</a>.', 'bp-better-messages'), admin_url('admin.php?page=bp-better-messages-account')); ?></p>
                        </div>
                    <?php } else if( BP_Better_Messages()->functions->can_use_premium_code() ){
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
                                $.post('https://license.bpbettermessages.com/checksyncv4.php', {
                                    site_id    : '<?php echo bpbm_fs()->get_site()->id; ?>',
                                    domain     : '<?php echo BP_Better_Messages_Premium()->site_id; ?>',
                                    secret_key : '<?php echo base64_encode(BP_Better_Messages_Premium()->secret_key); ?>'
                                }, function(response){
                                    if( response.success ){
                                        checking.parent().addClass('bpbm-ok');
                                        var message = '<span class="dashicons dashicons-yes-alt"></span> <?php esc_attr_e('All good, WebSocket server know about this domain, all should be working good.', 'bp-better-messages'); ?>';
                                    } else {
                                        checking.parent().addClass('bpbm-error');

                                        var message = '<span class="dashicons dashicons-dismiss"></span> <?php esc_attr_e('WebSocket server dont know about this domain, realtime functionality will not work. If you just activated the license at this website need to wait some time for system to sync your license with websocket servers. It usually takes up to 15 minutes, but in some cases can take longer.', 'bp-better-messages'); ?>';

                                        if( response.data.license_attached !== false ){
                                            message += '<br><br>This license is currently attached to <strong>' + response.data.license_attached + '</strong>';
                                        }
                                    }

                                    if( response.data.locked_to !== false ){
                                        message += '<br><br>This license is currently locked to <strong>' + response.data.locked_to + '</strong>';
                                        message += '<br><br> <span class="button bpbm-unlock-license" data-domain="' + response.data.locked_to + '">Unlock license from ' + response.data.locked_to + '</span>';
                                    } else {
                                        message += '<br><br>This license is currently not locked. Its recommended to lock your license to your live domain.';
                                        message += '<br><br> <span class="button bpbm-lock-license">Lock license to <?php echo BP_Better_Messages_Premium()->site_id; ?></span>';
                                    }

                                    checking.html( message );

                                    $('.bpbm-unlock-license').click(function(event){
                                        var domain = $('.bpbm-unlock-license').attr('data-domain');
                                        if( confirm( 'Confirm the unlock of license from ' + domain ) ) {
                                            $.post('https://license.bpbettermessages.com/changeLock.php', {
                                                domain: domain,
                                                secret_key: '<?php echo base64_encode(BP_Better_Messages_Premium()->secret_key); ?>',
                                                action: 'unlock'
                                            }, function (response) {
                                                location.reload();
                                            });
                                        }
                                    });

                                    $('.bpbm-lock-license').click(function(event){
                                        if( confirm( 'Confirm the lock of license to <?php echo BP_Better_Messages_Premium()->site_id; ?>' ) ) {
                                            $.post('https://license.bpbettermessages.com/changeLock.php', {
                                                domain: '<?php echo BP_Better_Messages_Premium()->site_id; ?>',
                                                secret_key: '<?php echo base64_encode(BP_Better_Messages_Premium()->secret_key); ?>',
                                                action: 'lock'
                                            }, function (response) {
                                                location.reload();
                                            });
                                        }
                                    });
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
                        <?php _ex( 'Enable reactions to messages', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Users will be able to react messages with emojis', 'Settings page', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _ex( 'You can select reactions in Integrations -> Emojies Tab', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enableReactions" type="checkbox" <?php checked( $this->settings[ 'enableReactions' ], '1' ); ?> value="1" />
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
                        <?php _ex( 'Allow invite more participants to private threads', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to invite more participants to private threads converting them to group conversation', 'Settings page', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _ex( '(admins can add more participants even if this option is disabled)', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="privateThreadInvite" type="checkbox" <?php checked( $this->settings[ 'privateThreadInvite' ], '1' ); ?> value="1" />
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
                        <?php _e( 'Messages Status', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Enable messages status functionality', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="messagesStatus" <?php checked( $this->settings[ 'messagesStatus' ], '1' ); ?> value="1" <?php if(! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
                                <?php BP_Better_Messages()->functions->license_proposal(); ?>
                            </label>
                        </fieldset>
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
                        <?php _ex( 'Users suggestions', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Enable users suggestions on new threads screen for the fast selection of users', 'Settings page', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _ex( 'Friends are listed first, after that listed lastly active users', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enableUsersSuggestions" type="checkbox" <?php if($this->settings[ 'disableUsersSearch' ] === '1') echo 'disabled'; ?> <?php checked( $this->settings[ 'enableUsersSuggestions' ], '1' ); ?> value="1" />
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
                        <?php _e( 'Disable additional security check when deleting thread', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Check this if you have issue with thread deleting', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="disableDeleteThreadCheck" type="checkbox" <?php checked( $this->settings[ 'disableDeleteThreadCheck' ], '1' ); ?> value="1" />
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="mini-widgets" class="bpbm-tab">

            <table class="form-table">
                <tbody>
                    <tr>
                        <th scope="row">
                            <?php _e( 'Mini Threads', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Enables mini threads list widget fixed to the bottom of browser window', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <fieldset>
                                <legend class="screen-reader-text"><span><?php _e( 'Small Chats', 'bp-better-messages' ); ?></span></legend>
                                <label>
                                    <input type="checkbox" name="miniThreadsEnable" <?php checked( $this->settings[ 'miniThreadsEnable' ], '1' ); ?> value="1" <?php if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
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
                                    <input type="checkbox" name="miniChatsEnable" <?php checked( $this->settings[ 'miniChatsEnable' ], '1' ); ?> value="1" <?php if(! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() || $this->settings[ 'mechanism' ] == 'ajax') echo 'disabled'; ?>>
                                    <?php BP_Better_Messages()->functions->license_proposal(); ?>
                                </label>
                            </fieldset>
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


                    <?php if( $this->settings[ 'mechanism' ] == 'websocket' ) { ?>
                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _ex( 'Height of Mini Chats', 'Settings page', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _ex( 'Measured in pixels', 'Settings page', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input type="number" name="miniChatsHeight" value="<?php echo esc_attr( $this->settings[ 'miniChatsHeight' ] ); ?>">
                            </td>
                        </tr>
                        <tr valign="top" class="">
                            <th scope="row" valign="top">
                                <?php _ex( 'Width of Mini Chats', 'Settings page', 'bp-better-messages' ); ?>
                                <p style="font-size: 10px;"><?php _ex( 'Measured in pixels', 'Settings page', 'bp-better-messages' ); ?></p>
                            </th>
                            <td>
                                <input type="number" name="miniChatsWidth" value="<?php echo esc_attr( $this->settings[ 'miniChatsWidth' ] ); ?>">
                            </td>
                        </tr>
                    <?php } ?>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Indent of Mini Widgets', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Indent of mini widgets from the window side in pixels.', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input type="number" name="miniWindowsOffset" value="<?php echo esc_attr( $this->settings[ 'miniWindowsOffset' ] ); ?>">
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Height of Mini Widgets', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Measured in pixels', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input type="number" name="miniWindowsHeight" value="<?php echo esc_attr( $this->settings[ 'miniWindowsHeight' ] ); ?>">
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Width of Mini Widgets', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Measured in pixels', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input type="number" name="miniWindowsWidth" value="<?php echo esc_attr( $this->settings[ 'miniWindowsWidth' ] ); ?>">
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _e( 'Audio Call button in mini chats', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Add audio call button to the mini chat', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="miniChatAudioCall" type="checkbox" <?php checked( $this->settings[ 'miniChatAudioCall' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _e( 'Video Call button in mini chats', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Add video call button to the mini chat', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="miniChatVideoCall" type="checkbox" <?php checked( $this->settings[ 'miniChatVideoCall' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>

        <div id="mobile" class="bpbm-tab">
            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 500px;">
                        <?php _e( 'Enable Mobile Chat at Any Page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Adds button fixed to the right corner on mobile devices, on click fully featured messaging will appear in full screen mode', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="mobilePopup" type="checkbox" <?php checked( $this->settings[ 'mobilePopup' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr>
                    <th scope="row" style="width: 300px">
                        <?php _e( 'Mobile Chat button position', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <fieldset>
                            <fieldset>
                                <label><input type="radio" name="mobilePopupLocation" value="left" <?php checked( $this->settings[ 'mobilePopupLocation' ], 'left' ); ?>>
                                    <?php _e( 'Left', 'bp-better-messages' ); ?>
                                </label>
                                <br>
                                <label><input type="radio" name="mobilePopupLocation" value="right" <?php checked( $this->settings[ 'mobilePopupLocation' ], 'right' ); ?>>
                                    <?php _e( 'Right', 'bp-better-messages' ); ?>
                                </label>
                            </fieldset>
                        </fieldset>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Mobile Chat button margin from bottom (px)', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input type="number" name="mobilePopupLocationBottom" value="<?php echo esc_attr( $this->settings[ 'mobilePopupLocationBottom' ] ); ?>">
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Dont show Mobile Chat button', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Dont show mobile button to following roles', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <ul class="bp-better-messages-roles-list">
                            <?php foreach( $roles as $slug => $role ){ ?>
                                <li><input id="<?php echo $slug; ?>_10" type="checkbox" name="restrictMobilePopup[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictMobilePopup' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_10"><?php echo $role['name']; ?></label></li>
                            <?php } ?>
                        </ul>
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
                        <?php _ex( 'Hide Possible Overlaying Elements', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'If in mobile view something overlaying the messages enable this option', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="hidePossibleBreakingElements" type="checkbox" <?php checked( $this->settings[ 'hidePossibleBreakingElements' ], '1' ); ?> <?php if($this->settings[ 'mobileFullScreen' ] == '0') echo 'disabled'; ?> value="1" />
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
                        <p style="font-size: 10px;"><?php _e( 'When enabled instead of standard notification on each new message, plugin will group messages by thread and send it every 15 minutes or other specified in this settings interval with cron job.', 'bp-better-messages' ); ?></p>
                        <p style="color: green;"><strong><?php _ex( 'Recommended - When using with BuddyPress its huge performance impact and UX issue to use standard Email Notifications.', 'Settings page', 'bp-better-messages' ); ?></strong></p>
                    </th>
                    <td>
                        <input name="replaceStandardEmail" type="checkbox" <?php checked( $this->settings[ 'replaceStandardEmail' ], '1' ); ?> <?php  if( ! function_exists('bp_send_email') ) echo 'disabled'; ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Auto create BuddyPress Email template if its missing', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;">
                            <?php _ex( 'You need to disable it only if you modified email template and plugin try to replace it', 'Settings page', 'bp-better-messages' ); ?>
                        </p>
                    </th>
                    <td>
                        <input name="createEmailTemplate" type="checkbox" <?php checked( $this->settings[ 'createEmailTemplate' ], '1' ); ?> <?php  if( ! function_exists('bp_send_email') ) echo 'disabled'; ?> value="1" />
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
                        <?php _ex( 'Send email after user is not online for (minutes)', 'Settings page', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input type="number" name="notificationsOfflineDelay" value="<?php echo esc_attr( $this->settings[ 'notificationsOfflineDelay' ] ); ?>">
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
                        <p style="font-size: 10px;"><?php _e( 'Allow users to enable web push notifications, so they can receive messages even with closed website', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _e( 'Also adds notifications, when user has website opened in other tab', 'bp-better-messages' ); ?></p>
                        <p style="font-size: 10px;"><?php _e( 'Supported in all major browsers like: Chrome, Opera, Firefox, IE, Edge and others', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="enablePushNotifications" type="checkbox" <?php checked( $this->settings[ 'enablePushNotifications' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Disable new messages onsite notifications', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="disableOnSiteNotification" type="checkbox" <?php checked( $this->settings[ 'disableOnSiteNotification' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Additional real time on site notifications', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><a href="https://www.wordplus.org/knowledge-base/additional-on-site-notifications/" target="_blank"><?php _e('How it works?', 'bp-better-messages'); ?></a></p>
                    </th>
                    <td>
                        <div style="position: relative">
                            <?php $license_message = BP_Better_Messages()->functions->license_proposal( true );
                            if( ! empty( $license_message ) ) { ?>
                                <div style="box-sizing: border-box;position:absolute;background: #ffffffb8;width: 100%;height: 100%;text-align: center;display: flex;align-items: center;justify-content: center;">
                                    <?php echo $license_message; ?>
                                </div>
                            <?php } ?>
                            <ul class="bp-better-messages-roles-list">
                                <li>
                                    <input id="friendsOnSiteNotifications" type="checkbox" name="friendsOnSiteNotifications" value="1" <?php checked( $this->settings[ 'friendsOnSiteNotifications' ], '1' ); ?> <?php if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> >
                                    <label for="friendsOnSiteNotifications"><?php _e('BuddyPress Friends', 'bp-better-messages'); ?></label>
                                </li>
                                <li>
                                    <input id="groupsOnSiteNotifications" type="checkbox" name="groupsOnSiteNotifications" value="1" <?php checked( $this->settings[ 'groupsOnSiteNotifications' ], '1' ); ?> <?php if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> >
                                    <label for="groupsOnSiteNotifications"><?php _e('BuddyPress Groups', 'bp-better-messages'); ?></label>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <div id="rules" class="bpbm-tab">
            <table class="form-table">
                <tbody>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Allow users to restrict who can start conversations with them', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Allow users to select who start conversations with them in plugin user settings', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="allowUsersRestictNewThreads" type="checkbox" <?php checked( $this->settings[ 'allowUsersRestictNewThreads' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Allow users to block other users', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to block other users from sending them messages (admins cant be blocked)', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="allowUsersBlock" type="checkbox" <?php checked( $this->settings[ 'allowUsersBlock' ], '1' ); ?> value="1" />
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _ex( 'Restrict user role from blocking other users', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Selected roles which will not be able to block other users if previous option is enabled', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <div style="
                            display: flex;
                            flex-wrap: nowrap;
                            justify-content: center;
                            align-items: center;
                            flex-direction: row;
                            width: 100%;
                        ">
                            <div style="width: 100%;margin-right: 5px">
                                <h4><?php _ex("Roles which can't block other users", 'Settings page', 'bp-better-messages'); ?></h4>
                                <ul class="bp-better-messages-roles-list">
                                    <?php foreach( $roles as $slug => $role ){ ?>
                                        <li><input id="<?php echo $slug; ?>_block" type="checkbox" name="restrictBlockUsers[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ "restrictBlockUsers" ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_block"><?php echo $role['name']; ?></label></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div style="width: 100%">
                                <h4><?php _ex("Roles which can't be blocked by other users", 'Settings page', 'bp-better-messages'); ?></h4>
                                <ul class="bp-better-messages-roles-list">
                                    <?php foreach( $roles as $slug => $role ){ ?>
                                        <li><input id="<?php echo $slug; ?>_block_2" type="checkbox" name="restrictBlockUsersImmun[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ "restrictBlockUsersImmun" ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_block_2"><?php echo $role['name']; ?></label></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
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
                    <th scope="row" valign="top">
                        <?php _e( 'Role to Role restrictions', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Disable users from being able to write each other based on user role', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <div class="bp-better-messages-roles-list">
                            <?php $roleBlock = $this->settings['restrictRoleBlock']; ?>

                            <?php if( count( $roleBlock ) === 0 ){ ?>
                                <div class="role-block-empty"><?php esc_attr_e('No role block rules added', 'bp-better-messages'); ?></div>
                            <?php } ?>

                            <table style="margin:0 -8px;">
                                <thead>
                                <tr>
                                    <th><?php _e( 'From', 'bp-better-messages' ); ?></th>
                                    <th><?php _e( 'To', 'bp-better-messages' ); ?></th>
                                    <th><?php _e( 'Message', 'bp-better-messages' ); ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody class="role-block-rows">
                                <?php foreach( $roleBlock as $index => $value ){ ?>
                                    <tr>
                                        <td>
                                            <select name="restrictRoleBlock[<?php esc_attr_e($index); ?>][from]" data-name="restrictRoleBlock[index][from]">
                                                <?php foreach( $roles as $slug => $role ){
                                                    echo '<option value="' . esc_attr( $slug ) . '" ' . selected( $value['from'], $slug, false ) . '>' . esc_attr( $role['name'] ) . '</option>';
                                                } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="restrictRoleBlock[<?php esc_attr_e($index); ?>][to]" data-name="restrictRoleBlock[index][to]">
                                                <?php foreach( $all_roles as $slug => $role ){
                                                    echo '<option value="' . esc_attr( $slug ) . '" ' . selected( $value['to'], $slug, false ) . '>' . esc_attr( $role['name'] ) . '</option>';
                                                } ?>
                                            </select>
                                        </td>
                                        <td style="width: 100%">
                                            <input type="text" style="width: 100%" name="restrictRoleBlock[<?php esc_attr_e($index); ?>][message]" data-name="restrictRoleBlock[index][message]" value="<?php esc_attr_e($value['message']); ?>">
                                        </td>
                                        <td><span class="delete-row"><span class="dashicons dashicons-trash"></span></span></td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>

                            <div style="margin: 10px 0 0;">
                                <button id="addRoleBlockRow" class="button"><?php _e( 'Add new rule', 'bp-better-messages' ); ?></button>

                                <table style="display: none">
                                    <tbody>
                                    <tr id="dummyRoleBlockRow">
                                        <td>
                                            <select name="restrictRoleBlock[index][from]">
                                                <?php foreach( $roles as $slug => $role ){
                                                    echo '<option value="' . esc_attr( $slug ) . '" disabled>' . esc_attr( $role['name'] ) . '</option>';
                                                } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="restrictRoleBlock[index][to]">
                                                <?php foreach( $all_roles as $slug => $role ){
                                                    echo '<option value="' . esc_attr( $slug ) . '" disabled>' . esc_attr( $role['name'] ) . '</option>';
                                                } ?>
                                            </select>
                                        </td>
                                        <td style="width: 100%">
                                            <input type="text" style="width: 100%" name="restrictRoleBlock[index][message]" disabled value="<?php esc_attr_e('You cannot send messages to this user', 'bp-better-messages'); ?>">
                                        </td>
                                        <td><span class="delete-row"><span class="dashicons dashicons-trash"></span></span></td>
                                    </tr>
                                    </tbody>
                                </table>

                                <script type="text/javascript">
                                    jQuery(document).ready(function( $ ){
                                        $('#addRoleBlockRow').click(function( event ){
                                            event.preventDefault();

                                            var rows      = $('.role-block-rows');
                                            var rowsCount = rows.find('> tr').length;
                                            var dummyRow  = '<tr>' + $('#dummyRoleBlockRow').html().replaceAll('[index]', '[' + rowsCount + ']').replaceAll('disabled', '') + '</tr>';

                                            rows.append(dummyRow);
                                            $('.role-block-empty').remove();
                                        });


                                        $('.role-block-rows').on('click', '.delete-row', function( event ){
                                            event.preventDefault();

                                            var button = $(this);
                                            var tr = button.closest('tr');
                                            tr.remove();

                                            $('.role-block-rows tr').each(function(){
                                                var tr = $(this);
                                                var index = tr.index();

                                                tr.find('[data-name]').each(function(){
                                                    var el   = $(this);
                                                    var name = el.attr('data-name').replaceAll('[index]', '[' + index + ']');

                                                    el.attr( 'name', name );
                                                });

                                            });
                                        });
                                    });
                                </script>
                            </div>
                        </div>
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

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict from viewing mini widgets', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Selected roles will not see selected widgets', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <div style="
                            display: flex;
                            flex-wrap: nowrap;
                            justify-content: center;
                            align-items: center;
                            flex-direction: row;
                            width: 100%;
                        ">
                            <div style="width: 100%;">
                                <h4>Mini Threads</h4>
                                <div style="position: relative">
                                    <?php $license_message = BP_Better_Messages()->functions->license_proposal( true );
                                    if( ! empty( $license_message ) ) { ?>
                                    <div style="box-sizing: border-box;position:absolute;background: #ffffffb8;width: 100%;height: 100%;text-align: center;display: flex;align-items: center;justify-content: center;">
                                        <?php echo $license_message; ?>
                                    </div>
                                    <?php } ?>
                                    <ul class="bp-better-messages-roles-list">
                                        <?php foreach( $roles as $slug => $role ){ ?>
                                            <li><input id="<?php echo $slug; ?>_7" type="checkbox" name="restrictViewMiniThreads[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictViewMiniThreads' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_7"><?php echo $role['name']; ?></label></li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>

                            <div style="width: 100%;margin: 5px;">
                                <h4>Mini Friends</h4>
                                <ul class="bp-better-messages-roles-list">
                                    <?php foreach( $roles as $slug => $role ){ ?>
                                        <li><input id="<?php echo $slug; ?>_8" type="checkbox" name="restrictViewMiniFriends[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictViewMiniFriends' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_8"><?php echo $role['name']; ?></label></li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <div style="width: 100%">
                                <h4>Mini Groups</h4>
                                <ul class="bp-better-messages-roles-list">
                                    <?php foreach( $roles as $slug => $role ){ ?>
                                        <li><input id="<?php echo $slug; ?>_9" type="checkbox" name="restrictViewMiniGroups[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictViewMiniGroups' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_9"><?php echo $role['name']; ?></label></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>


                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Bad Words List', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'One word per line', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <textarea name="badWordsList" style="width: 100%;height: 200px;" placeholder="word 1&#10;word 2"><?php esc_attr_e($this->settings[ 'badWordsList' ]); ?></textarea>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Bad Words List message', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input type="text" style="width: 100%" name="restrictBadWordsList" value="<?php esc_attr_e($this->settings['restrictBadWordsList']); ?>">
                    </td>
                </tr>

                </tbody>
            </table>
        </div>

        <div id="calls" class="bpbm-tab">
            <?php if(BP_Better_Messages()->functions->can_use_premium_code() && ! is_ssl() ){ ?>
                <div class="bp-better-messages-connection-check bpbm-error" style="margin: 20px 0;">
                    <p><?php esc_attr_e('Website must to have SSL certificate in order to audio and video calls work.', 'bp-better-messages'); ?></p>
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
                        <input name="videoCalls" type="checkbox" <?php checked( $this->settings[ 'videoCalls' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
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
                        <input name="audioCalls" type="checkbox" <?php checked( $this->settings[ 'audioCalls' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Revert Mute Voice & Hide Video icons', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Makes mute and hide video icons to appear in reverse way', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="callsRevertIcons" type="checkbox" <?php checked( $this->settings[ 'callsRevertIcons' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
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
                                <input type="number" name="callRequestTimeLimit" value="<?php echo esc_attr( $this->settings[ 'callRequestTimeLimit' ] ); ?>" <?php if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?>>
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
                        <input name="offlineCallsNotifications" type="checkbox" <?php checked( $this->settings[ 'offlineCallsNotifications' ], '1' ); ?> value="1" <?php if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Allow call even when user is offline', 'Settings page', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <input name="offlineCallsAllowed" type="checkbox" <?php checked( $this->settings[ 'offlineCallsAllowed' ], '1' ); ?> value="1" <?php if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _ex( 'Restrict calls', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Selected roles will not be allowed to call, but they will be able to receive calls still', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <div style="position: relative">
                            <?php $license_message = BP_Better_Messages()->functions->license_proposal( true );
                            if( ! empty( $license_message ) ) { ?>
                                <div style="box-sizing: border-box;position:absolute;background: #ffffffb8;width: 100%;height: 100%;text-align: center;display: flex;align-items: center;justify-content: center;">
                                    <?php echo $license_message; ?>
                                </div>
                            <?php } ?>
                            <ul class="bp-better-messages-roles-list">
                                <?php foreach( $roles as $slug => $role ){ ?>
                                    <li><input id="<?php echo $slug; ?>_calls_1" type="checkbox" name="restrictCalls[]" value="<?php echo $slug; ?>" <?php if(in_array($slug, $this->settings[ 'restrictCalls' ])) echo 'checked="checked"'; ?>><label for="<?php echo $slug; ?>_calls_1"><?php echo $role['name']; ?></label></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 320px;">
                        <?php _e( 'Restrict call message', 'bp-better-messages' ); ?>
                    </th>
                    <td>
                        <div style="position: relative">
                            <?php $license_message = BP_Better_Messages()->functions->license_proposal( true );
                            if( ! empty( $license_message ) ) { ?>
                                <div style="box-sizing: border-box;position:absolute;background: #ffffffb8;width: 100%;height: 100%;text-align: center;display: flex;align-items: center;justify-content: center;">
                                    <?php echo $license_message; ?>
                                </div>
                            <?php } ?>
                            <input type="text" style="width: 100%" name="restrictCallsMessage" value="<?php esc_attr_e($this->settings['restrictCallsMessage']); ?>">
                        </div>
                    </td>
                </tr>

                </tbody>
            </table>


            <p style="color: #0c5460;background-color: #d1ecf1;border: 1px solid #d1ecf1;padding: 15px;line-height: 24px;max-width: 550px;">
                <a href="https://www.wordplus.org/knowledge-base/how-video-calls-works/" target="_blank">How video/audio calls works?</a><br>
            </p>
        </div>

        <div id="group-calls" class="bpbm-tab">
            <h3><?php _ex( 'Group Video Chat', 'Settings page', 'bp-better-messages' ); ?></h3>
            <p><?php _ex( 'Group audio chat allows to start high definition video & voice group chat up to 9 people per 1 conversation.', 'Settings page', 'Settings page', 'bp-better-messages' ); ?></p>

            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Enable Video Chat for Groups', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to start group video chats in Group Chats', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="groupCallsGroups" type="checkbox" <?php checked( $this->settings[ 'groupCallsGroups' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Enable Video Chat for Threads', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to start group video chats in threads with many participants', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="groupCallsThreads" type="checkbox" <?php checked( $this->settings[ 'groupCallsThreads' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                </tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Enable Video Chat for Chat Rooms', 'Settings page','bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to start group video chats in chat rooms', 'Settings page','bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="groupCallsChats" type="checkbox" <?php checked( $this->settings[ 'groupCallsChats' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                </tbody>
            </table>

            <h3><?php _ex( 'Group Audio Chat', 'Settings page', 'bp-better-messages' ); ?></h3>
            <p><?php _ex( 'Group audio chat allowing your user to start high definition voice only group chat up to 50 people per 1 conversation.', 'Settings page', 'bp-better-messages' ); ?></p>


            <table class="form-table">
                <tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Enable Audio Chat for Groups', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to start group audio chats in Group Chats', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="groupAudioCallsGroups" type="checkbox" <?php checked( $this->settings[ 'groupAudioCallsGroups' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Enable Audio Chat for Threads', 'Settings page', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to start group audio chats in threads with many participants', 'Settings page', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="groupAudioCallsThreads" type="checkbox" <?php checked( $this->settings[ 'groupAudioCallsThreads' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                </tbody>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _ex( 'Enable Audio Chat for Chat Rooms', 'Settings page','bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _ex( 'Allow users to start group audio chats in chat rooms', 'Settings page','bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input name="groupAudioCallsChats" type="checkbox" <?php checked( $this->settings[ 'groupAudioCallsChats' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
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
                        <br>
                        <p><a href="https://www.wordplus.org/knowledge-base/dark-mode-with-css/" target="_blank"><?php _e( 'Dark Mode', 'bp-better-messages' ); ?></a></p>
                    </th>
                    <td>
                        <input type="text" name="colorGeneral" class="color-selector" value="<?php esc_attr_e( $this->settings[ 'colorGeneral'] ); ?>" />
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
                        <?php _e( 'Min Height of Messages Container', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Min Height of Messages Container in PX.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="messagesMinHeight" value="<?php echo esc_attr( $this->settings[ 'messagesMinHeight' ] ); ?>">
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
                        <?php _e( 'Side Threads Width', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'Side Threads Width when Combined View is enabled in PX.', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="sideThreadsWidth" value="<?php echo esc_attr( $this->settings[ 'sideThreadsWidth' ] ); ?>">
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

        <?php
        $active_integration = 'bm-buddypress';
        if( defined('ultimatemember_version') ){
            $active_integration = 'bm-ultimate-member';
        }
        if( class_exists('PeepSo') ){
            $active_integration = 'bm-peepso';
        }
        ?>
        <div id="integrations" class="bpbm-tab">
            <div class="nav-tab-wrapper bpbm-sub-tabs">
                <a class="nav-tab <?php if($active_integration === 'bm-buddypress') echo 'nav-tab-active'; ?>" id="bm-buddypress-tab" href="#bm-buddypress"><?php _ex( 'BuddyPress & BuddyBoss', 'Settings page',  'bp-better-messages' ); ?></a>
                <a class="nav-tab <?php if($active_integration === 'bm-ultimate-member') echo 'nav-tab-active'; ?>" id="bm-ultimate-member-tab" href="#bm-ultimate-member"><?php _ex( 'Ultimate Member', 'Settings page',  'bp-better-messages' ); ?></a>
                <a class="nav-tab <?php if($active_integration === 'bm-peepso') echo 'nav-tab-active'; ?>" id="bm-peepso-tab" href="#bm-peepso"><?php _ex( 'PeepSo', 'Settings page',  'bp-better-messages' ); ?></a>
                <a class="nav-tab" id="mycred-tab" href="#mycred"><?php _ex( 'MyCRED', 'Settings page', 'bp-better-messages' ); ?></a>
                <a class="nav-tab" id="stickers-tab" href="#stickers"><?php _ex( 'GIFs & Stickers', 'Settings page', 'bp-better-messages' ); ?></a>
                <a class="nav-tab" id="bbpress-tab" href="#bm-bbpress"><?php _ex( 'bbPress', 'Settings page','bp-better-messages' ); ?></a>
                <a class="nav-tab" id="emojies-tab" href="#bm-emojies"><?php _ex( 'Emojies', 'Settings page','bp-better-messages' ); ?></a>
            </div>

            <div id="bm-peepso" class="bpbm-subtab <?php if($active_integration === 'bm-peepso') echo 'active'; ?>">
                <?php if( ! class_exists('PeepSo') ){ ?>
                    <div class="bp-better-messages-connection-check bpbm-error" style="margin: 20px 0;">
                        <p><?php echo sprintf(esc_html_x('Website must to have %s plugin to be installed.', 'Settings page', 'bp-better-messages'), '<a href="https://www.wordplus.org/peepso" target="_blank">PeepSo</a>'); ?></p>
                        <p><small><?php echo esc_attr_x('This notice will be hidden when PeepSo plugin is installed', 'Settings page', 'bp-better-messages'); ?></small></p>
                    </div>
                <?php } ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable PeepSo Header at Messages Page', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>
                            <input name="peepsoHeader" type="checkbox" <?php checked( $this->settings[ 'peepsoHeader' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Only Friends Mode', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Allow only friends to send messages each other', 'Settings page', 'bp-better-messages' ); ?></p>
                            <p style="font-size: 10px;"><?php printf(_x( '%s must be installed', 'Settings page', 'bp-better-messages' ), '<a href="https://www.peepso.com/features/#friends" target="_blank">PeepSo - Friends addon</a>'); ?></p>
                        </th>
                        <td>
                            <input name="PSonlyFriendsMode" type="checkbox" <?php disabled( ! class_exists('PeepSoFriendsPlugin') ); ?>  <?php checked( $this->settings[ 'PSonlyFriendsMode' ] && class_exists('PeepSoFriendsPlugin'), '1' ); ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _ex( 'PeepSo Friends', 'Settings page','bp-better-messages' ); ?>
                        </th>
                        <td>
                            <fieldset>
                                <table class="widefat bm-switcher-table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="PSminiFriendsEnable" <?php disabled( ! class_exists('PeepSoFriendsPlugin') ); ?> <?php checked( $this->settings[ 'PSminiFriendsEnable' ] && class_exists('PeepSoFriendsPlugin'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mini Widget', 'Settings page', 'bp-better-messages' ); ?>

                                            <p style="font-size: 10px;"><?php _ex( 'Enables mini friends list widget fixed to the bottom of browser window', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="PScombinedFriendsEnable" <?php disabled( ! class_exists('PeepSoFriendsPlugin') ); ?> <?php checked( $this->settings[ 'PScombinedFriendsEnable' ] && class_exists('PeepSoFriendsPlugin'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Combined View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Friends in left column of Combined view', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="PSmobileFriendsEnable" <?php disabled( ! class_exists('PeepSoFriendsPlugin') ); ?> <?php checked( $this->settings[ 'PSmobileFriendsEnable' ] && class_exists('PeepSoFriendsPlugin'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mobile View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Friends as tab at bottom of Mobile View', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable Messages for PeepSo Groups', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Enable messages for PeepSo groups', 'Settings page', 'bp-better-messages' ); ?></p>
                            <p style="font-size: 10px;"><?php printf(_x( '%s must be installed', 'Settings page', 'bp-better-messages' ), '<a href="https://www.peepso.com/features/#groups" target="_blank">PeepSo - Groups addon</a>'); ?></p>
                        </th>
                        <td>
                            <input name="PSenableGroups" type="checkbox" <?php if ( ! class_exists( 'PeepSoGroupsPlugin' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'PSenableGroups' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable file uploading in PeepSo Groups Messages', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>
                            <input name="PSenableGroupsFiles" type="checkbox" <?php if ( ! class_exists( 'PeepSoGroupsPlugin' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'PSenableGroupsFiles' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable Email Notifications for PeepSo Groups', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'When enabled users will receive email notifications for Group Chats', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="PSenableGroupsEmails" type="checkbox" <?php if ( ! class_exists( 'PeepSoGroupsPlugin' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'PSenableGroupsEmails' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _ex( 'PeepSo Groups', 'Settings page','bp-better-messages' ); ?>
                        </th>
                        <td>
                            <fieldset>
                                <table class="widefat bm-switcher-table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="PSminiGroupsEnable" <?php disabled( ! class_exists('PeepSoGroup') ); ?> <?php checked( $this->settings[ 'PSminiGroupsEnable' ] && class_exists('PeepSoGroup'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mini Widget', 'Settings page', 'bp-better-messages' ); ?>

                                            <p style="font-size: 10px;"><?php _ex( 'Enables mini groups list widget fixed to the bottom of browser window', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="PScombinedGroupsEnable" <?php disabled( ! class_exists('PeepSoGroup') ); ?> <?php checked( $this->settings[ 'PScombinedGroupsEnable' ] && class_exists('PeepSoGroup'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Combined View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Groups in left column of Combined view', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="PSmobileGroupsEnable" <?php disabled( ! class_exists('PeepSoGroup') ); ?> <?php checked( $this->settings[ 'PSmobileGroupsEnable' ] && class_exists('PeepSoGroup'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mobile View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Groups as tab at bottom of Mobile View', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </td>
                    </tr>


                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Audio Call button in user profile', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Add audio call button to user profile', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="peepsoProfileAudioCall" type="checkbox" <?php checked( $this->settings[ 'peepsoProfileAudioCall' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Video Call button in user profile', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Add video call button to user profile', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="peepsoProfileVideoCall" type="checkbox" <?php checked( $this->settings[ 'peepsoProfileVideoCall' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="bm-emojies" class="bpbm-subtab">
                <style type="text/css">
                    .smilesList{
                    }

                    .smilesList .smilesListItem{
                        display: inline-block;
                        vertical-align: top;
                        margin: 0 5px 5px;
                        cursor: pointer;
                    }

                    .smilesList .smilesListItem.disabled{
                        opacity: 0.5;
                    }

                    .reactions-emojies{
                        background: white;
                        border: 1px solid #ccc;
                        padding: 5px 5px;
                    }
                    .reactions-emojies tr{}
                    .reactions-emojies tr td{
                        padding: 5px 5px;
                    }

                    .reactions-emojies tr td .dashicons-trash{
                        cursor: pointer;
                    }

                    .reactions-selector{}
                    .reactions-selector img{
                        display: inline-block;
                        vertical-align: top;
                        margin: 0 5px 5px;
                        cursor: pointer;
                    }
                </style>
                <table class="form-table">
                    <tbody>
                    <?php
                    $emojies_ruleset = new \BetterMessages\JoyPixels\Ruleset();
                    $emojies = $emojies_ruleset->getShortcodeReplace();

                    $sorting = BP_Better_Messages_Emojies()->get_emojies_settings();

                    $emoji_list = [];
                    foreach( $emojies as $code => $emoji ){
                        $category = $emoji[2];
                        if( ! isset( $sorting[$category] ) ) continue;

                        $unicode  = $emoji[1];

                        if( ! isset( $emoji_list[ $category ] ) ){
                            $emoji_list[ $category ] = [];
                        }

                        $code = str_replace(':', '', $code);

                        if( strpos($code, '_tone') !== false ) continue;
                        if( $category === 'flags' && strlen( $code ) < 3 ) continue;
                        if( in_array( $unicode, $emoji_list[$category] ) ) continue;

                        $emoji_list[$category][$code] = $unicode;
                    }

                    $reactions = BP_Better_Messages_Reactions::instance()->get_reactions();
                    ?>
                    <tr>
                        <th scope="row" style="white-space: nowrap">
                            <?php _ex( 'Emojies for Reactions', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>
                            <table class="reactions-emojies">
                                <?php foreach( $reactions as $unicode => $name ){
                                    echo '<tr>';
                                    echo '<td>';
                                    echo '<img class="emojione" loading="lazy" style="width: 25px;height: 25px" alt="" src="https://cdn.bpbettermessages.com/emojies/6.6/png/unicode/32/' . $unicode . '.png">';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<input type="text" name="reactionsEmojies[' . esc_attr( $unicode ) . ']" value="' . esc_attr($name) . '">';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<span class="dashicons dashicons-trash"></span>';
                                    echo '</td>';
                                    echo '</tr>';
                                }

                                echo '<tr class="newReactionRow">';
                                echo '<td colspan="2">';
                                echo '<button id="addNewReaction" class="button">' . _x( 'Add new reaction', 'Settings page', 'bp-better-messages' ) . '</button>';
                                echo '</td>';
                                echo '</tr>';

                                ?>
                            </table>

                            <div class="reactions-selector" style="display: none">
                                <h4><?php _ex( 'Select Emoji for new reaction', 'Settings page', 'bp-better-messages' ); ?></h4>
                                <div class="reactions-selector-emojies"></div>
                            </div>

                            <script type="text/javascript">
                                jQuery(document).ready(function( $ ) {
                                    var selectEvent;

                                    jQuery('.reactions-emojies tbody').sortable({
                                        stop: function( event, ui ) {
                                            //alert('sorting finished');
                                            //calculateNewValue();
                                        }
                                    });

                                    $('.reactions-selector-emojies').on('click', '> .emojione', function(event){
                                        event.preventDefault();

                                        var imageHtml = this.outerHTML;
                                        var unicode   = $(this).attr('data-unicode');

                                        var newRow = '<tr><td>' + imageHtml + '</td><td><input type="text" name="reactionsEmojies[' + unicode + ']" value=""></td><td><span class="dashicons dashicons-trash"></span></td></tr>';

                                        $(newRow).insertBefore( $('.newReactionRow') );
                                        $('.reactions-selector').hide();
                                        $('.reactions-selector-emojies').html('');
                                    });

                                    $('.reactions-emojies').on('click', 'tr .dashicons-trash', function(event){
                                        event.preventDefault();

                                        var button = $(this);
                                        var table  = button.closest('table');
                                        var row    = button.closest('tr');
                                        var rows = table.find('tr').length;

                                        row.remove();
                                    });

                                    $('.reactions-emojies').on('click', '> .emojione', function(event){
                                        event.preventDefault();

                                        var imageHtml = this.outerHTML;
                                        var unicode   = $(this).attr('data-unicode');

                                        var newRow = '<tr><td>' + imageHtml + '</td><td><input type="text" name="reactionsEmojies[' + unicode + ']" value=""></td><td><span><span class="dashicons dashicons-trash"></span></span></td></tr>';

                                        $(newRow).insertBefore( $('.newReactionRow') );
                                        $('.reactions-selector').hide();
                                        $('.reactions-selector-emojies').html('');
                                    });

                                    $('#addNewReaction').on('click', function (event) {
                                        event.preventDefault();

                                        var html = '';
                                        $('.smilesListItem img.emojione').each(function(){
                                            html += this.outerHTML;
                                        });

                                        $('.reactions-selector').show();
                                        $('.reactions-selector-emojies').html(html);
                                    });
                                });
                            </script>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row" style="white-space: nowrap">
                            <?php _ex( 'Emojies for Selector', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>
                            <p class="bp-better-messages-connection-check" style="background: white;color: black;border-color: #ccc;margin-bottom: 10px;"><?php _ex( 'Click on emoji to enable or disable it, drag it to change it position in emoji selector', 'Settings page', 'bp-better-messages' ); ?></p>
                            <table>
                                <tbody>
                                <?php foreach( $emoji_list as $category => $emojies ){
                                    echo '<tr><th style="padding: 0.2em 0 1em">' . ucfirst($category) . '</th></tr>';
                                    echo '<tr class="emoji-category" data-category="' . $category . '">';
                                    echo '<td style="padding: 0">';

                                    $emojies = array_reverse($emojies, true);
                                    $order = [];
                                    if( isset( $sorting[$category] ) ){
                                        $order = $sorting[$category];

                                        $sorted_first = [];
                                        foreach( $emojies as $shortcode => $unicode ){
                                            if( in_array($shortcode, $order) ){
                                                $sorted_first[ $shortcode ] = $unicode;
                                                unset($emojies[$shortcode]);
                                            }
                                        }

                                        uksort($sorted_first, function($key1, $key2) use ($order) {
                                            return (array_search($key1, $order) > array_search($key2, $order));
                                        });

                                        $emojies = $sorted_first + $emojies;
                                    }

                                    echo '<div class="smilesList">';
                                    foreach( $emojies as $shortcode => $unicode ){
                                        $disabledClass = ( ! in_array($shortcode, $order) ) ? ' disabled' : '';
                                        echo '<div class="smilesListItem' . $disabledClass . '" data-shortcode="' . $shortcode . '">';
                                        echo '<img class="emojione" loading="lazy" style="width: 25px;height: 25px" data-unicode="' . $unicode . '" alt="" src="https://cdn.bpbettermessages.com/emojies/6.6/png/unicode/32/' . $unicode . '.png">';
                                        echo '</div>';
                                    }
                                    echo '</div>';

                                    echo '</td>';
                                    echo '</tr>';

                                }
                                ?>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <input type="hidden" name="emojiSettings" value="">
                <script type="text/javascript">
                    var input = jQuery('input[name="emojiSettings"]');

                    function calculateNewValue(){
                        var result = {};
                        var categories = jQuery('tr.emoji-category');
                        categories.each(function(){
                            var item = jQuery( this );
                            var category = item.data('category');

                            result[category] = [];
                            var smileList = item.find('.smilesList');
                            var smiles = smileList.find('> .smilesListItem:not(.disabled)');

                            smiles.each(function(){
                                var shortcode = jQuery(this).data('shortcode');

                                result[category].push(shortcode);
                            });
                        });

                        input.val( JSON.stringify( result ) );
                    }

                    jQuery('.smilesList .smilesListItem').click(function(){
                        jQuery(this).toggleClass('disabled');
                        calculateNewValue();
                    });

                    jQuery(document).ready(function(){
                        jQuery('.smilesList').sortable({
                            stop: function( event, ui ) {
                                //alert('sorting finished');
                                calculateNewValue();
                            }
                        });
                    });
                </script>
            </div>

            <div id="bm-ultimate-member" class="bpbm-subtab <?php if($active_integration === 'bm-ultimate-member') echo 'active'; ?>">
                <?php if( ! defined('ultimatemember_version') ){ ?>
                    <div class="bp-better-messages-connection-check bpbm-error" style="margin: 20px 0;">
                        <p><?php echo sprintf(esc_html_x('Website must to have %s plugin to be installed.', 'Settings page', 'bp-better-messages'), '<a href="https://wordpress.org/plugins/ultimate-member/" target="_blank">Ultimate Member</a>'); ?></p>
                        <p><small><?php echo esc_attr_x('This notice will be hidden when Ultimate Member plugin is installed', 'Settings page', 'bp-better-messages'); ?></small></p>
                    </div>
                <?php } ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'User Profile - Private Message Button', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Show Private Message button in user profiles', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="umProfilePMButton" type="checkbox" <?php checked( $this->settings[ 'umProfilePMButton' ], '1' ); ?> value="1" />
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Only Friends Mode', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Allow only friends to send messages each other', 'Settings page', 'bp-better-messages' ); ?></p>
                            <p style="font-size: 10px;"><?php printf(_x( '%s must be installed', 'Settings page', 'bp-better-messages' ), '<a href="https://ultimatemember.com/extensions/friends/" target="_blank">Ultimate Member - Friends addon</a>'); ?></p>
                        </th>
                        <td>
                            <input name="umOnlyFriendsMode" type="checkbox" <?php disabled( ! class_exists('UM_Friends_API') ); ?>  <?php checked( $this->settings[ 'umOnlyFriendsMode' ] && class_exists('UM_Friends_API'), '1' ); ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _ex( 'Ultimate Member Friends', 'Settings page','bp-better-messages' ); ?>
                        </th>
                        <td>
                            <fieldset>
                                <table class="widefat bm-switcher-table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="UMminiFriendsEnable" <?php disabled( ! class_exists('UM_Friends_API') ); ?> <?php checked( $this->settings[ 'UMminiFriendsEnable' ] && class_exists('UM_Friends_API'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mini Widget', 'Settings page', 'bp-better-messages' ); ?>

                                            <p style="font-size: 10px;"><?php _ex( 'Enables mini friends list widget fixed to the bottom of browser window', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="UMcombinedFriendsEnable" <?php disabled( ! class_exists('UM_Friends_API') ); ?> <?php checked( $this->settings[ 'UMcombinedFriendsEnable' ] && class_exists('UM_Friends_API'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Combined View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Friends in left column of Combined view', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="UMmobileFriendsEnable" <?php disabled( ! class_exists('UM_Friends_API') ); ?> <?php checked( $this->settings[ 'UMmobileFriendsEnable' ] && class_exists('UM_Friends_API'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mobile View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Friends as tab at bottom of Mobile View', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Only Followers Mode', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Allow only send message if user following the user or followed by the user', 'Settings page', 'bp-better-messages' ); ?></p>
                            <p style="font-size: 10px;"><?php printf(_x( '%s must be installed', 'Settings page', 'bp-better-messages' ), '<a href="https://ultimatemember.com/extensions/followers/" target="_blank">Ultimate Member - Followers addon</a>'); ?></p>
                        </th>
                        <td>
                            <input name="umOnlyFollowersMode" type="checkbox" <?php disabled( ! class_exists('UM_Followers_API') ); ?>  <?php checked( $this->settings[ 'umOnlyFollowersMode' ] && class_exists('UM_Followers_API'), '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable Messages for Ultimate Member Groups', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Enable messages for Ultimate Member groups', 'Settings page', 'bp-better-messages' ); ?></p>
                            <p style="font-size: 10px;"><?php printf(_x( '%s must be installed', 'Settings page', 'bp-better-messages' ), '<a href="https://ultimatemember.com/extensions/groups/" target="_blank">Ultimate Member - Groups addon</a>'); ?></p>
                        </th>
                        <td>
                            <input name="UMenableGroups" type="checkbox" <?php if ( ! class_exists('UM_Groups') ) echo 'disabled'; ?> <?php checked( $this->settings[ 'UMenableGroups' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable file uploading in Ultimate Member Groups Messages', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>
                            <input name="UMenableGroupsFiles" type="checkbox" <?php if ( ! class_exists('UM_Groups') ) echo 'disabled'; ?> <?php checked( $this->settings[ 'UMenableGroupsFiles' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable Email Notifications for Ultimate Member Groups', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'When enabled users will receive email notifications for Group Chats', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="UMenableGroupsEmails" type="checkbox" <?php if ( ! class_exists('UM_Groups') ) echo 'disabled'; ?> <?php checked( $this->settings[ 'UMenableGroupsEmails' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <?php _ex( 'Ultimate Member Groups', 'Settings page','bp-better-messages' ); ?>
                        </th>
                        <td>
                            <fieldset>
                                <table class="widefat bm-switcher-table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="UMminiGroupsEnable" <?php disabled( ! class_exists('UM_Groups') ); ?> <?php checked( $this->settings[ 'UMminiGroupsEnable' ] && class_exists('UM_Groups'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mini Widget', 'Settings page', 'bp-better-messages' ); ?>

                                            <p style="font-size: 10px;"><?php _ex( 'Enables mini groups list widget fixed to the bottom of browser window', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="UMcombinedGroupsEnable" <?php disabled( ! class_exists('UM_Groups') ); ?> <?php checked( $this->settings[ 'UMcombinedGroupsEnable' ] && class_exists('UM_Groups'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Combined View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Groups in left column of Combined view', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="UMmobileGroupsEnable" <?php disabled( ! class_exists('UM_Groups') ); ?> <?php checked( $this->settings[ 'UMmobileGroupsEnable' ] && class_exists('UM_Groups'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mobile View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Groups as tab at bottom of Mobile View', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <div id="stickers" class="bpbm-subtab">
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
                    <strong>Stipop.io changed their plans and allows only 100 monthly active users instead of 10000 for free.</strong>
                    <br><br>
                    If you have more then 100 monthly users active, consider disabling stickers or subscribe to Stipop.io paid options.
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

            <div id="bm-buddypress" class="bpbm-subtab <?php if($active_integration === 'bm-buddypress') echo 'active'; ?>">
                <?php if ( ! class_exists( 'BuddyPress' ) ) { ?>
                <div class="bp-better-messages-connection-check bpbm-error" style="margin: 20px 0;">
                    <p><?php echo sprintf(esc_html_x('Website must to have %s plugin to be installed.', 'Settings page', 'bp-better-messages'), '<a href="https://wordpress.org/plugins/buddypress/" target="_blank">BuddyPress</a>'); ?></p>
                    <p><small><?php echo esc_attr_x('This notice will be hidden when BuddyPress plugin is installed', 'Settings page', 'bp-better-messages'); ?></small></p>
                </div>
                <?php } ?>
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

                    <tr>
                        <th scope="row">
                            <?php _ex( 'BuddyPress Friends', 'Settings page','bp-better-messages' ); ?>
                        </th>
                        <td>
                            <fieldset>
                                <table class="widefat bm-switcher-table">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="miniFriendsEnable" <?php disabled( ! function_exists('friends_get_friend_user_ids') ); ?> <?php checked( $this->settings[ 'miniFriendsEnable' ] && function_exists('friends_get_friend_user_ids'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mini Widget', 'Settings page', 'bp-better-messages' ); ?>

                                            <p style="font-size: 10px;"><?php _ex( 'Enables mini friends list widget fixed to the bottom of browser window', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="combinedFriendsEnable" <?php disabled( ! function_exists('friends_get_friend_user_ids') ); ?> <?php checked( $this->settings[ 'combinedFriendsEnable' ] && function_exists('friends_get_friend_user_ids'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Combined View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Friends in left column of Combined view', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="mobileFriendsEnable" <?php disabled( ! function_exists('friends_get_friend_user_ids') ); ?> <?php checked( $this->settings[ 'mobileFriendsEnable' ] && function_exists('friends_get_friend_user_ids'), '1' ); ?> value="1">
                                        </td>
                                        <th>
                                            <?php _ex( 'Mobile View', 'Settings page', 'bp-better-messages' ); ?>
                                            <p style="font-size: 10px;"><?php _ex( 'Shows Friends as tab at bottom of Mobile View', 'Settings page','bp-better-messages' ); ?></p>
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
                            </fieldset>
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _e( 'Enable Messages for BuddyPress Groups', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Enable messages for BuddyPress groups', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="enableGroups" type="checkbox" <?php if ( ! bp_is_active( 'groups' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'enableGroups' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Enable file uploading in BuddyPress Groups Messages', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>
                            <input name="enableGroupsFiles" type="checkbox" <?php if ( ! bp_is_active( 'groups' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'enableGroupsFiles' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'BuddyPress Groups', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>

                            <table class="widefat bm-switcher-table">
                                <tbody>
                                <tr>
                                    <td>
                                        <input name="enableMiniGroups" type="checkbox" <?php if ( ! bp_is_active( 'groups' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'enableMiniGroups' ], '1' ); ?> value="1" />
                                    </td>
                                    <th>
                                        <?php _ex( 'Mini Widget', 'Settings page', 'bp-better-messages' ); ?>
                                        <p style="font-size: 10px;"><?php _ex( 'Enables mini groups widget fixed to the bottom of browser window', 'Settings page', 'bp-better-messages' ); ?></p>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <input name="combinedGroupsEnable" type="checkbox" <?php if ( ! bp_is_active( 'groups' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'combinedGroupsEnable' ], '1' ); ?> value="1" />
                                    </td>
                                    <th>
                                        <?php _ex( 'Combined View', 'Settings page', 'bp-better-messages' ); ?>
                                        <p style="font-size: 10px;"><?php _ex( 'Shows Groups in left column of Combined view', 'Settings page', 'bp-better-messages' ); ?></p>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="mobileGroupsEnable" <?php disabled( ! bp_is_active( 'groups' ) ); ?> <?php checked( $this->settings[ 'mobileGroupsEnable' ], '1' ); ?> value="1">
                                    </td>
                                    <th>
                                        <?php _ex( 'Mobile View', 'Settings page', 'bp-better-messages' ); ?>
                                        <p style="font-size: 10px;"><?php _ex( 'Shows Groups as tab at bottom of Mobile View', 'Settings page','bp-better-messages' ); ?></p>
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _e( 'Enable Email Notifications for BuddyPress Groups', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'When enabled users will receive email notifications for Group Chats', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="enableGroupsEmails" type="checkbox" <?php if ( ! bp_is_active( 'groups' ) ) echo 'disabled'; ?> <?php checked( $this->settings[ 'enableGroupsEmails' ], '1' ); ?> value="1" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e( 'BuddyPress Group Slug', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Change messages tab URL slug in BuddyPress group', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <fieldset>
                                <label><input type="text" name="bpGroupSlug" value="<?php echo esc_attr( $this->settings[ 'bpGroupSlug' ] ); ?>"></label>
                            </fieldset>
                        </td>
                    </tr>

                    <?php
                    $buddyboss_installed = ( class_exists('BuddyBoss_Theme') || function_exists( 'buddyboss_theme_register_required_plugins' ) );
                    ?>
                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Replace BuddyBoss Header Messages', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Replaces BuddyBoss Header Messages with plugin native messages layout', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="replaceBuddyBossHeader" type="checkbox" <?php if ( ! $buddyboss_installed ) echo 'disabled'; ?> <?php checked( $this->settings[ 'replaceBuddyBossHeader' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _e( 'Limit calls only to the friends', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Allow only friends to make calls between each other (admins always can call)', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="callsLimitFriends" type="checkbox" <?php disabled( ! function_exists('friends_get_friend_user_ids') ); ?> <?php checked( $this->settings[ 'callsLimitFriends' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _e( 'Audio Call button in user profile', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Add audio call button to user profile', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="profileAudioCall" type="checkbox" <?php checked( $this->settings[ 'profileAudioCall' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
                        </td>
                    </tr>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _e( 'Video Call button in user profile', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _e( 'Add video call button to user profile', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input name="profileVideoCall" type="checkbox" <?php checked( $this->settings[ 'profileVideoCall' ], '1' ); ?> value="1" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code() || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> />
                            <?php BP_Better_Messages()->functions->license_proposal(); ?>
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
                    </tbody>
                </table>
            </div>

            <div id="mycred" class="bpbm-subtab ">
                <?php if ( ! class_exists( 'myCRED_Core' ) ) { ?>
                    <div class="bp-better-messages-connection-check bpbm-error" style="margin: 20px 0;">
                        <p><?php echo sprintf(esc_html_x('Website must to have %s plugin to be installed.', 'bp-better-messages'), '<a href="https://www.wordplus.org/mc" target="_blank">MyCRED</a>'); ?></p>
                        <p><small><?php esc_attr_e('This notice will be hidden when MyCRED plugin is installed', 'bp-better-messages'); ?></small></p>
                    </div>
                <?php } else { ?>
                    <div class="bp-better-messages-connection-check" style="margin: 20px 0;">
                        <p><?php echo sprintf(esc_html_x('Plugin also support %s addon, so please use it carefully together with this settings as double charge can occur.', 'Settings page', 'bp-better-messages'), '<a href="https://www.wordplus.org/mcbc" target="_blank">MyCRED BP Charges</a>'); ?></p>
                    </div>
                <?php } ?>
                <table class="form-table">
                    <tbody>
                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Price for new message in the conversation', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'Use 0 if this is free', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <div class="bp-better-messages-roles-list">
                                <table style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th><?php _ex('Role', 'Settings page', 'bp-better-messages'); ?></th>
                                        <th><?php _ex('Price', 'Settings page', 'bp-better-messages'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach( $roles as $slug => $role ){
                                        $value = 0;

                                        if( isset($this->settings['myCredNewMessageCharge'][$slug])){
                                            $value = $this->settings['myCredNewMessageCharge'][$slug]['value'];
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $role['name']; ?></td>
                                            <td>
                                                <input name="myCredNewMessageCharge[<?php echo $slug; ?>][value]" type="number" min="0" value="<?php esc_attr_e($value); ?>">
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
                            <?php _ex( 'Message when user can`t send new reply', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'HTML Allowed', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input type="text" style="width: 100%" name="myCredNewMessageChargeMessage" value="<?php esc_attr_e($this->settings['myCredNewMessageChargeMessage']); ?>">
                        </td>
                    </tr>
                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Price for new starting new conversation', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'The charge will be applied additionally to the message price', 'Settings page', 'bp-better-messages' ); ?></p>
                            <p style="font-size: 10px;"><?php _ex( 'Use 0 if this is free', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <div class="bp-better-messages-roles-list">
                                <table style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th><?php _ex('Role', 'Settings page', 'bp-better-messages'); ?></th>
                                        <th><?php _ex('Price', 'Settings page', 'bp-better-messages'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach( $roles as $slug => $role ){
                                        $value = 0;

                                        if( isset($this->settings['myCredNewThreadCharge'][$slug])){
                                            $value = $this->settings['myCredNewThreadCharge'][$slug]['value'];
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $role['name']; ?></td>
                                            <td>
                                                <input name="myCredNewThreadCharge[<?php echo $slug; ?>][value]" type="number" min="0" value="<?php esc_attr_e($value); ?>">
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
                            <?php _ex( 'Message when user can`t send start new conversation', 'Settings page', 'bp-better-messages' ); ?>
                            <p style="font-size: 10px;"><?php _ex( 'HTML Allowed', 'Settings page', 'bp-better-messages' ); ?></p>
                        </th>
                        <td>
                            <input type="text" style="width: 100%" name="myCredNewThreadChargeMessage" value="<?php esc_attr_e($this->settings['myCredNewThreadChargeMessage']); ?>">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div id="bm-bbpress" class="bpbm-subtab ">
                <?php if ( ! class_exists( 'bbPress' ) ) { ?>
                    <div class="bp-better-messages-connection-check bpbm-error" style="margin: 20px 0;">
                        <p><?php echo sprintf(esc_html_x('Website must to have %s plugin to be installed.', 'Settings page', 'bp-better-messages'), '<a href="https://wordpress.org/plugins/bbpress/" target="_blank">bbPress</a>'); ?></p>
                        <p><small><?php echo esc_attr_x('This notice will be hidden when bbPress plugin is installed', 'Settings page', 'bp-better-messages'); ?></small></p>
                    </div>
                <?php } ?>
                <table class="form-table">
                    <tbody>

                    <tr valign="top" class="">
                        <th scope="row" valign="top">
                            <?php _ex( 'Show link in bbPress author details', 'Settings page', 'bp-better-messages' ); ?>
                        </th>
                        <td>
                            <input name="bbPressAuthorDetailsLink" type="checkbox" <?php checked( $this->settings[ 'bbPressAuthorDetailsLink' ], '1' ); ?> value="1" />
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

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
                        <input type="number" name="callSound" min="0" max="100" value="<?php echo esc_attr( $this->settings[ 'callSound' ] ); ?>" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> >
                        <?php BP_Better_Messages()->functions->license_proposal(); ?>
                    </td>
                </tr>
                <tr valign="top" class="">
                    <th scope="row" valign="top">
                        <?php _e( 'Outgoing call sound volume', 'bp-better-messages' ); ?>
                        <p style="font-size: 10px;"><?php _e( 'From 0 to 100 (0 to disable)', 'bp-better-messages' ); ?></p>
                    </th>
                    <td>
                        <input type="number" name="dialingSound" min="0" max="100" value="<?php echo esc_attr( $this->settings[ 'dialingSound' ] ); ?>" <?php  if( ! BP_Better_Messages()->functions->can_use_premium_code()  || ! bpbm_fs()->is_premium() ) echo 'disabled'; ?> >
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
                        <?php
                        //$options = get_option( 'bp-better-chat-settings', array() );
                        //echo base64_encode(json_encode($options));
                        ?>
                        <textarea id="export-settings" readonly style="width: 100%;height: 200px;" onclick="this.focus();this.select()"></textarea>
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

                            var button = jQuery('#export-import-tab');

                            var bpbmsettingsLoaded = false;
                            function loadSettingsBase64(){
                                if( bpbmsettingsLoaded ){
                                    return false;
                                }

                                bpbmsettingsLoaded = true;

                                jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                                    'action'   : 'bp_messages_admin_export_options',
                                    'nonce'    : '<?php echo wp_create_nonce( 'bpbm-import-options' ); ?>'
                                }, function(response){
                                    jQuery('#export-settings').val(response);
                                });
                            }

                            jQuery(document).ready(function() {
                                if (button.hasClass('nav-tab-active')) {
                                    loadSettingsBase64();
                                }
                            });

                            button.click(function( event ){
                                loadSettingsBase64();
                            });

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
                        <input readonly type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_pm_button text="Private Message" subject="Have a question to you" message="Lorem Ipsum is simply dummy text of the printing and typesetting industry." target="_self" class="extra-class" fast_start="0" url_only="0"]'>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 350px;">
                        <?php _e( 'Video Call Button', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Shows video call button', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('This shortcode will try to find user_id from environment, for example author of post and display Video Call button.', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('If user_id not found it will not display anything. You can force user id with user_id="1" attribute.', 'bp-better-messages'); ?></p>
                    </th>
                    <td>
                        <div style="position: relative">
                        <?php $license_message = BP_Better_Messages()->functions->license_proposal( true );
                        if( ! empty( $license_message ) ) { ?>
                            <div style="box-sizing: border-box;position:absolute;background: #ffffff;width: 100%;height: 100%;text-align: center;display: flex;align-items: center;justify-content: center;">
                                <?php echo $license_message; ?>
                            </div>
                        <?php } ?>
                        <input readonly type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_video_call_button text="Video Call" url_only="0" class="extra-class"]'>
                        </div>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 350px;">
                        <?php _e( 'Audio Call Button', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Shows audio call button', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('This shortcode will try to find user_id from environment, for example author of post and display Audio Call button.', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('If user_id not found it will not display anything. You can force user id with user_id="1" attribute.', 'bp-better-messages'); ?></p>
                    </th>
                    <td style="position: relative">
                        <div style="position: relative">
                            <?php $license_message = BP_Better_Messages()->functions->license_proposal( true );
                            if( ! empty( $license_message ) ) { ?>
                                <div style="box-sizing: border-box;position:absolute;background: #ffffff;width: 100%;height: 100%;text-align: center;display: flex;align-items: center;justify-content: center;">
                                    <?php echo $license_message; ?>
                                </div>
                            <?php } ?>
                            <input readonly type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_audio_call_button text="Audio Call" url_only="0" class="extra-class"]'>
                        </div>
                    </td>
                </tr>

                <tr valign="top" class="">
                    <th scope="row" valign="top" style="width: 350px;">
                        <?php _e( 'Mini Chat Button', 'bp-better-messages' ); ?>
                        <p style="font-weight: normal"><?php _e('Shows mini chat button (opens mini chat with user on click)', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('This button will work only if Mini Chats option is enabled', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('This shortcode will try to find user_id from environment, for example author of post and display Mini Chat button.', 'bp-better-messages'); ?></p>
                        <p style="font-weight: normal"><?php _e('If user_id not found it will not display anything. You can force user id with user_id="1" attribute.', 'bp-better-messages'); ?></p>
                    </th>
                    <td style="position: relative">
                        <div style="position: relative">
                            <?php $license_message = BP_Better_Messages()->functions->license_proposal( true );
                            if( ! empty( $license_message ) ) { ?>
                                <div style="box-sizing: border-box;position:absolute;background: #ffffff;width: 100%;height: 100%;text-align: center;display: flex;align-items: center;justify-content: center;">
                                    <?php echo $license_message; ?>
                                </div>
                            <?php } ?>
                            <input readonly type="text" style="width: 100%;" onclick="this.focus();this.select()" value='[bp_better_messages_mini_chat_button text="Private Message" class="extra-class"]'>
                        </div>
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