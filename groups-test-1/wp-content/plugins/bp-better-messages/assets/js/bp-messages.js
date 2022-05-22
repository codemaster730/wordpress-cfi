/* global BP_Messages */
var BPBMJQ = jQuery.noConflict();
BPBMJQ.fn.bpbmtooltip = jQuery.fn.tooltip;
var BPBMOnlineUsers = [];
(function ($) {
    var checkerTimer, // Timer variable
        thread,  // Current thread_id or false
        socket,
        threads, // True if we are on thread list screen or false
        openThreads = {},
        miniChats = {},
        miniMessages = false,
        bpMessagesWrap,
        online = [],
        onlineFetched = false,
        loadingMore = {},
        unread = {},
        reIniting = false,
        blockScroll = false,
        isRtl = $('html[dir="rtl"]').length !== 0,
        RTCPeerConnection = window.RTCPeerConnection || window.webkitRTCPeerConnection,
        RTCSessionDescription = window.mozRTCSessionDescription || window.RTCSessionDescription,
        isInCall = false,
        blockSelect = false,
        originalTitle = document.title,
        loadingHtml = '<div class="loading-messages"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>';

    var incomingSelector = '.bp-messages-wrap .list .messages-stack.incoming .content .messages-list li .message-content';
    var outgoingSelector = '.bp-messages-wrap .list .messages-stack.outgoing .content .messages-list li .message-content';
    var threadsSelector  = '.bp-messages-wrap .threads-list .thread';

    if( store.enabled && BP_Messages['realtime'] == "1" ){
        var lastUnread = store.get('bp-better-messages-last-unread');

        if( typeof lastUnread !== 'undefined' ){
            if(parseInt(BP_Messages['total_unread']) !== parseInt(lastUnread)){
                BPBMUpdateUnreadCount(lastUnread);
            }
        }
    }

    ifvisible.setIdleDuration(3);

    var status_icons = {};
    var icons = {
        'gif' : '<svg xmlns="http://www.w3.org/2000/svg" viewBox="5.002 9.969 23.017 13.042">\n' +
            '<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\n' +
            '<g fill="currentColor">\n' +
            '<path d="M8.00684834,10 C6.34621185,10 5,11.3422643 5,12.9987856 L5,20.0012144 C5,21.6573979 6.33599155,23 8.00684834,23 L24.9931517,23 C26.6537881,23 28,21.6577357 28,20.0012144 L28,12.9987856 C28,11.3426021 26.6640085,10 24.9931517,10 L8.00684834,10 L8.00684834,10 Z M7.99456145,11 C6.89299558,11 6,11.9001762 6,12.992017 L6,20.007983 C6,21.1081436 6.90234375,22 7.99456145,22 L25.0054385,22 C26.1070044,22 27,21.0998238 27,20.007983 L27,12.992017 C27,11.8918564 26.0976562,11 25.0054385,11 L7.99456145,11 L7.99456145,11 Z M13,17 L13,19 L10.9998075,19 C10.4437166,19 10,18.5523709 10,18.0001925 L10,14.9998075 C10,14.4437166 10.4476291,14 10.9998075,14 L14,14 L14,13 L11.0048815,13 C9.89761602,13 9,13.8865548 9,15.0059191 L9,17.9940809 C9,19.1019194 9.8938998,20 11.0048815,20 L14,20 L14,19.25 L14,19.25 L14,17 L14,16 L11,16 L11,17 L13,17 L13,17 Z M16,14 L16,19 L15,19 L15,20 L18,20 L18,19 L17,19 L17,14 L18,14 L18,13 L15,13 L15,14 L16,14 L16,14 Z M20,16 L20,14 L24,14 L24,13 L19,13 L19,20 L20,20 L20,17 L23,17 L23,16 L20,16 L20,16 Z"/>\n' +
            '</g>\n' +
            '</g>\n' +
            '</svg>'
    }

    

    var sounds = {};

    if( BP_Messages['soundLevels']['notification'] > 0 ) {
        sounds['notification'] = new Howl({
            src: [ BP_Messages.assets + 'notification.mp3', BP_Messages.assets + 'notification.ogg'],
            loop: false,
            volume: BP_Messages['soundLevels']['notification']
        });
    }

    if( BP_Messages['soundLevels']['sent'] > 0 ) {
        sounds['sent'] = new Howl({
            src: [BP_Messages.assets + 'sent.mp3', BP_Messages.assets + 'sent.ogg'],
            loop: false,
            volume: BP_Messages['soundLevels']['sent']
        });
    }

    

    $.fn.BPBMremoveAttributes = function() {
        return this.each(function() {
            var attributes = $.map(this.attributes, function(item) {
                return item.name;
            });
            var img = $(this);
            $.each(attributes, function(i, item) {
                img.removeAttr(item);
            });
        });
    }

    function BPBMUpdateUnreadCount( unread ){
        var _unread = parseInt( unread );
        if( isNaN(_unread) ) _unread = 0;

        document.dispatchEvent(new CustomEvent('bp-better-messages-update-unread', {
            detail: {
                unread: _unread
            }
        }));

        BP_Messages.total_unread = _unread;

        if( _unread === 0 ){
            $('.bp-messages-wrap .threads-list .thread .unread-count').html('');
        }

        if( BP_Messages['titleNotifications'] === '1' ){
            if( isNaN(_unread) || _unread <= 0 ){
                document.title = originalTitle;
            } else {
                document.title = '(' + unread +  ') ' + originalTitle;
            }
        }
    }

    function BPBMformatTextArea(textarea){
        if ( typeof textarea[0].BPemojioneArea !== 'undefined' ){
            textarea[0].BPemojioneArea.trigger('change');
        }

        var message = textarea.val();

        if( $(textarea).next('.bp-emojionearea').length === 0 ) return message;

        var new_html = BPBMformatMessage(message);

        textarea.val(new_html);

        return new_html;
    }

    function BPBMformatMessage(message){
        message = message.replace(/<p><\/p>/g, '');

        if( message.substring(0, 3) !== '<p>' ) {
            message = '<p>' + message;
        }

        if( message.substring(message.length - 4) !== '</p>' ) {
            message = message + '</p>';
        }


        var message_html = $.parseHTML( message );

        $.each( message_html, function( i, el ) {
            var element = $(this);

            $.each(element.find('img.emojioneemoji,img.emojione'), function () {
                var emojiicon = $(this);
                emojiicon.replaceWith(emojiicon.attr('alt'));
            });

            element.BPBMremoveAttributes();
            element.find('*').BPBMremoveAttributes();

        });

        var new_html = '';
        $.each( message_html, function(){
            new_html += this.outerHTML;
        } );

        if(new_html === '<p></p>') new_html = '';

        new_html = new_html.replace(/&amp;/g, '&');

        return new_html;
    }

    $(window).on('focus',function() {
        ifvisible.focus();
    });

    $(window).on('blur', function() {
        ifvisible.blur();
    });

    $(window).on('click', function(){
        $('.bp-messages-wrap .expandingButtons.expandingButtonsOpen').removeClass('expandingButtonsOpen');
    })

    var isMobile = false; //initiate as false
    if( /iPad|iPhone|iPod/.test(navigator.userAgent)
        || /(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

    //if( BP_Messages['mobileFullScreen'] === '0' ) isMobile = false;

    if( BP_Messages['forceMobile'] === '1' ) isMobile = true;

    var isTapped = false;
    if( isMobile ) {
        $(window).on('touchstart', function () {
            isTapped = true;
        });

        $(window).on('touchend', function () {
            isTapped = false;
        });
    }

    var holderClass = 'bp-better-messages-mobile-holder';

    function initImagesPopup() {
        if (typeof $.fn.BPBMmagnificPopup === 'function') {
            $('.bp-messages-wrap .list .messages-stack .content .messages-list li .images').each(function(){
                var images = $(this);
                var message = images.closest('li');

                images.BPBMmagnificPopup({
                    delegate : 'a',
                    key      : message.attr('data-id'),
                    type     : 'image',
                    gallery  : {
                        enabled:true
                    }
                });
            });


        }
    }

    function openMobile( wrap ){
        if( wrap.length === 0 ) return false;

        if( $('.' + holderClass ).length === 0 ){
            $('<div class="' + holderClass + '"></div>').insertBefore(wrap);
        }

        var source = wrap;
        source.addClass('bp-messages-mobile');
        source.attr('id', 'bp-better-messages-mobile-view-container');

        var windowHeight = window.innerHeight;
        $('html').addClass('bp-messages-mobile').css('overflow', 'hidden');
        $('body').addClass('bp-messages-mobile').css('min-height', windowHeight);

        var _mobileViewContainer = source.appendTo( $('body') );
        _mobileViewContainer.show();

        var usedHeight = 0;
        usedHeight = usedHeight + _mobileViewContainer.find('.chat-header').outerHeight();
        if( _mobileViewContainer.find('.chat-footer:visible').length > 0 ) {
            usedHeight = usedHeight + _mobileViewContainer.find('.chat-footer:visible').outerHeight();
        }

        if( _mobileViewContainer.find('.reply').length > 0 ) {
            usedHeight = usedHeight + _mobileViewContainer.find('.reply').outerHeight();
        }

        var resultHeight = windowHeight - usedHeight;

        _mobileViewContainer.find('.scroller').css({
            'max-height': '',
            'height': resultHeight
        });

        calculateTitle(_mobileViewContainer);
        scrollBottom();

        hidePossibleBreakingElements();

        $(document).trigger("bp-better-messages-mobile-open");

        blockScroll = true;
    }

    if (store.enabled) {
        openThreads = store.get('bp-better-messages-open-threads') || {};
        miniChats = store.get('bp-better-messages-mini-chats') || {};
        miniMessages = store.get('bp-better-messages-mini-messages') || false;
        setInterval(updateOpenThreads, 1000);
    }

    

    $(document).ready(function () {
        isRtl = $('html[dir="rtl"]').length !== 0;
        bpMessagesWrap          = $(".bp-messages-wrap:not(.bp-better-messages-list, #bp-better-messages-mini-mobile-open)");
        var miniMobileContainer = $('#bp-better-messages-mini-mobile-container');
        var mobileViewContainer = '#bp-better-messages-mobile-view-container';
        var mobileOpenButton    = $('#bp-better-messages-mini-mobile-open');

        if( ! isMobile ){
            mobileOpenButton.hide();
        }

        if(isMobile && BP_Messages['mobileFullScreen'] !== '0'){
            var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

            $(document).on('touchmove', '.bp-messages-wrap .chat-header,.bp-messages-wrap .reply', function(event) {
                if(blockScroll){
                    event.preventDefault();
                }
            });

            $(document).on('focus blur', '.bp-messages-wrap textarea, .bp-messages-wrap input', function( event ){
                setTimeout(function(){
                    mobileResize();
                    scrollBottom();
                }, 300);
            });

            $( window ).resize(function() {
                if( bpMessagesWrap.hasClass('bp-messages-mobile') ){
                    mobileResize();
                }
            });

            function mobileResize(){
                if( iOS ) {
                    return false;
                }
                /*
                var windowHeight = window.innerHeight;
                var usedHeight = 0;
                usedHeight = usedHeight + bpMessagesWrap.find('.chat-header').outerHeight();

                if( bpMessagesWrap.find('.chat-footer:visible').length > 0 ) {
                    usedHeight = usedHeight + bpMessagesWrap.find('.chat-footer:visible').outerHeight();
                }

                if( bpMessagesWrap.find('.reply').length > 0 ) {
                    usedHeight = usedHeight + bpMessagesWrap.find('.reply').outerHeight();
                }


                $('.scroller').css({
                    'max-height': '',
                    'height' : windowHeight - usedHeight
                });*/
            }

            bpMessagesWrap.addClass('mobile-ready');

            var touchmoved;
            var blockOpen = false;

            if( BP_Messages['disableTapToOpen'] === '0' ){
                bpMessagesWrap.find('.bp-messages-mobile-tap').css('line-height', bpMessagesWrap.height() + 'px');

                bpMessagesWrap.on('touchend', '.bp-messages-mobile-tap', function(event){

                    if(touchmoved != true && blockOpen === false){
                        var wrap = BPBMJQ(event.target).closest('.bp-messages-wrap');

                        if( ! wrap.hasClass('bp-messages-mobile') ){
                            event.preventDefault();
                            event.stopImmediatePropagation();
                            openMobile( wrap );
                        }
                    }
                }).on('touchmove', function(e){
                    touchmoved = true;
                }).on('touchstart', function(e){
                    touchmoved = false;
                });
            } else {
                bpMessagesWrap.on('touchend', function(event){
                    if(touchmoved != true && blockOpen === false){
                        var wrap = BPBMJQ(event.target).closest('.bp-messages-wrap');
                        if( ! wrap.hasClass('bp-messages-mobile') ){
                            event.preventDefault();
                            event.stopImmediatePropagation();
                            openMobile( wrap );

                            var clickedElement = $(event.originalEvent.target);
                            var threadClicked = clickedElement.closest('.thread');

                            if( threadClicked.length > 0 ){
                                threadClicked.click();
                            }

                        }
                    }
                }).on('touchmove', function(e){
                    touchmoved = true;
                }).on('touchstart', function(e){
                    touchmoved = false;
                });
            }
        }

        $(document).on('click', '.bp-messages-wrap .bpbm-gif .bpbm-gif-play', function(event){
            blockSelect = true;
            var gifPlay = $(this);
            var gif = $(this).parent();
            var video = gif.find('video');

            gifPlay.remove();
            video[0].play();
            setTimeout(function(){
                blockSelect = false;
            }, 500);
        });

        $(document).on('click', '.bp-messages-wrap .mobileClose', function(event){
            var wrap = BPBMJQ(event.target).closest('.bp-messages-wrap');
            if( wrap.hasClass('bp-messages-mobile') ){
                event.preventDefault();
                event.stopImmediatePropagation();

                blockOpen = true;
                $('html').removeClass('bp-messages-mobile').css('overflow', 'auto');
                wrap.removeClass('bp-messages-mobile').css('min-height', '');
                $('body').removeClass('bp-messages-mobile').css('min-height', '');

                var height = $( window ).height() - 250;
                if(height > BP_Messages['max_height']) height = BP_Messages['max_height'];

                /*wrap.find('.scroller').css({
                    'max-height' : height,
                    'height'     : ''
                });*/
                blockScroll = false;

                wrap.find('.bp-messages-mobile-tap').css('line-height', wrap.height() + 'px');

                if( wrap.is(miniMobileContainer) ){
                    miniMobileContainer.hide();
                }

                if( wrap.is(mobileViewContainer) ){
                    var source = wrap;
                    source.removeClass('bp-messages-mobile');
                    source.removeAttr('id');

                    var holder = $('.' + holderClass );
                    source.insertBefore( holder );
                    holder.remove();
                }

                $(window).trigger('resize');

                setTimeout(function () {
                    blockOpen = false;
                }, 100);
            }
        });


        $(document).on('click', '.bp-messages-wrap .expandingButtons', function(event){
            event.stopPropagation();
            $(this).toggleClass('expandingButtonsOpen');
        });

        mobileOpenButton.on('click', function(event){
            event.preventDefault();

            if( ! mobileOpenButton.hasClass('loading') ) {
                mobileOpenButton.addClass('loading');

                openMobileFullScreen( BP_Messages['baseUrl'] );
            }
        });

        var __thread = false;
        var __threads = false

        if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list) .thread.scroller[data-users-json]").length > 0) {
            __thread = $(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list) .thread.scroller[data-users-json]").attr('data-id');
        } else if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,#bp-better-messages-mini-mobile-container) .threads-list").length > 0) {
            __threads = true;
        }

        if( isMobile ) {
            if( BP_Messages['autoFullScreen'] === '1' ) {
                if (__thread || __threads) {

                    $('#bp-better-messages-mini-mobile-open, #bp-better-messages-mini-mobile-container').remove();
                    var wrap = $('.bp-messages-wrap.mobile-ready.bp-messages-wrap-main, .bp-messages-wrap.mobile-ready.bp-messages-group-thread');
                    openMobile(wrap);
                } else {
                    var wrap = $('.bp-messages-wrap.bp-messages-wrap-bulk.mobile-ready');
                    if (wrap.length > 0) {
                        openMobile(wrap);
                    }
                }
            } else {
                if (__thread || __threads) {
                    $('#bp-better-messages-mini-mobile-open, #bp-better-messages-mini-mobile-container').remove();
                }
            }
        }

        /**
         * Disable scroll page when scrolling chats
         */
        if( BP_Messages['blockScroll'] === '1' ){
            $('.bp-messages-wrap').on( 'mousewheel DOMMouseScroll', '.bpbm-os-viewport', function (e) {
                var e0 = e.originalEvent;
                var delta = e0.wheelDelta || -e0.detail;
                var max = e.currentTarget.scrollHeight - $(e.currentTarget).height();

                //this.scrollTop += ( delta < 0 ? 1 : -1 ) * 5;

                if( delta > 0 ) {
                    if( this.scrollTop < 5 ){
                        e.preventDefault();
                    }
                } else {
                    if( (max - this.scrollTop) < 5){
                        e.preventDefault();
                    }
                }
            });
        }

        reInit();

        if(typeof BP_Messages['socket_server'] === 'undefined' ) {
            setInterval(function () {
                updateLastActivity()
            }, 60000 * 5);

            function updateLastActivity() {
                $.post(BP_Messages.ajaxUrl, {
                    'action': 'bp_messages_last_activity_refresh'
                });
            }
        }

        /**
         * Go to thread from thread list
         */
        bpMessagesWrap.on('click', '.threads-list .thread:not(.blocked)', function (event) {
            if ( $(event.target).closest('.pic').length === 0 &&  $(event.target).closest('.delete').length === 0 && $(event.target).closest('.deleted').length === 0 ) {
                event.preventDefault();
                var href = $(this).attr('data-href');
                var container = $(this).closest('.bp-messages-wrap');
                ajaxRefresh(href, container);
            }
        });

        /**
         * Delete thread! :)
         */
        $(document).on('click', threadsSelector + ' span.delete', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var thread = $(this).parent().parent();
            var scroller = thread.parent().parent();
            var thread_id = $(thread).attr('data-id');
            var height = $(thread).height();

            var nonce = $(this).attr('data-nonce');

            $.post(BP_Messages.ajaxUrl, {
                'action': 'bp_messages_delete_thread',
                'thread_id': thread_id,
                'nonce': nonce
            }, function (data) {
                if (!data.result) {
                    BBPMShowError(data['errors'][0]);
                } else {
                    var top = thread.position().top;
                    //top = top + scroller.scrollTop();

                    $(thread).addClass('blocked');
                    $(thread).find('.deleted').show().css({
                        'height': height,
                        'line-height': height + 'px',
                        'top': top + 'px'
                    });
                }
            });
        });

        /**
         * UnDelete thread! :)
         */
        $(document).on('click', threadsSelector + ' a.undelete', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var thread = $(this).parent().parent();
            var thread_id = $(thread).attr('data-id');
            $(thread).removeClass('blocked');

            var nonce = $(this).attr('data-nonce');

            $.post(BP_Messages.ajaxUrl, {
                'action': 'bp_messages_un_delete_thread',
                'thread_id': thread_id,
                'nonce': nonce
            }, function (data) {
                if (!data.result) {
                    BBPMShowError(data['errors'][0]);
                } else {
                    $(thread).removeClass('blocked');
                    $(thread).find('.deleted').hide();
                }
            });
        });

        /**
         * Messages actions
         */
        bpMessagesWrap.on('click', '.messages-list li .favorite', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var message_id = $(this).parentsUntil('.messages-list', 'li').attr('data-id');
            var type = 'star';
            if ($(this).hasClass('active')) type = 'unstar';

            $(this).toggleClass('active');

            $.post(BP_Messages.ajaxUrl, {
                'action': 'bp_messages_favorite',
                'message_id': message_id,
                'thread_id': thread,
                'type': type
            }, function (bool) {});
        });

        bpMessagesWrap.on('click', '.messages-list li .bpbm-reply', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var message    = $(this).closest('li');
            var stack      = message.closest('.messages-stack');
            var wrap       = stack.closest('.bp-messages-threads-wrapper');
            var message_id = message.attr('data-id');
            var name    = stack.find('.content .info .name a').text();

            var content = '';
            var html_content = '';

            var reply = message.find('.bpbm-replied-message-reply');
            if( reply.length > 0 ){
                content += reply.text();
            } else {
                content += message.find('.message-content').text();
            }

            var descs = message.find('.message-content > [data-desc]');
            if (descs.length > 0 ) {
                descs.each(function(){
                    var _desc = $(this).attr('data-desc');
                    if (typeof _desc !== 'undefined') {
                        html_content += '<span class="bpbm-preview-desc">' + atob(_desc) + '</span>';
                    }
                });
            }


            var html = '<div class="bpbm-preview-message bpbm-reply-message" style="display:none" data-message-id="' + message_id + '">' +
            '<div class="bpbm-preview-message-cancel"><i class="far fa-times-circle"></i></div>' +
            '<div class="bpbm-preview-message-content">' +
            '<span class="bpbm-preview-message-name"></span>' +
            '<div class="bpbm-preview-message-text"></div>' +
            '</div>' +
            '</div>';

            var previewMessage = wrap.find('.bpbm-preview-message');
            if( previewMessage.length > 0 ){
                previewMessage = previewMessage.replaceWith(html);
                previewMessage = wrap.find('.bpbm-preview-message')
                previewMessage.show();
                updateWritingPosition();
            } else {
                previewMessage = $(html).insertBefore(wrap.find('.reply'));
                previewMessage.slideDown(100, function (){
                    updateWritingPosition();
                });
            }
            previewMessage.find('.bpbm-preview-message-name').text(name);
            previewMessage.find('.bpbm-preview-message-text').text(content);
            previewMessage.find('.bpbm-preview-message-text').append(html_content);

            previewMessage.find('.bpbm-gifs-icon').html(icons.gif);
        });

        bpMessagesWrap.on('click', '.bpbm-preview-message-cancel', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var previewMessage = $(this).closest('.bpbm-preview-message');
            previewMessage.slideUp(100, function (){
                previewMessage.remove();
                updateWritingPosition();
            });
        });


        var sendingMessage = false;
        var lastForm = '';
        var lastFormTimeout;

        /*
         * Reply submit
         */
        bpMessagesWrap.on('submit', '.reply > form', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            ifvisible.focus();

            if( sendingMessage === true ) return;

            var _form = $(this);
            if(_form.serialize() === lastForm) return false;
            var textarea = $(this).find('textarea[name="message"]');

            var message = BPBMformatTextArea(textarea);

            var form = $(this).serialize();

            //sendingMessage = true;
            var _thread = thread;

            var isEdit     =  false;
            var isReply    =  false;

            var container = $(this).parent().parent();
            var threadDiv = container.find('.thread.scroller[data-users-json]');
            var reply     = container.find('.bpbm-preview-message.bpbm-reply-message');
            if( reply.length > 0 ){
                isReply = true;
                form += '&reply=1&message_id=' + parseInt(reply.attr('data-message-id'));
            }

            var edit     = container.find('.bpbm-preview-message.bpbm-edit-message');
            if( edit.length > 0 ){
                isEdit = true;
                form += '&edit=1&message_id=' + parseInt(edit.attr('data-message-id'));
            }

            var usersJson = atob(threadDiv.attr('data-users-json'));
            var recipients = Object.keys($.parseJSON(usersJson));
            var maybeMini = $(this).closest('.bp-better-messages-mini').length > 0;
            if(maybeMini) _thread = $(this).closest('.chat').attr('data-thread');

            if( BP_Messages['encryption'] === '1' ){
                var secret_key = threadDiv.data('secret');
                message = BPBMAES256.encrypt(message, secret_key);
            }

            if( _thread && BP_Messages['realtime'] == "1" && ! isEdit && ! isReply ) {
                socket.emit( 'fast_message', _thread, message, recipients, function(response){
                    lastForm = form;

                    clearInterval(lastFormTimeout);
                    lastFormTimeout = setInterval(function(){lastForm = ''}, 3000);

                    $(document).trigger("bp-better-messages-message-sent");
                    if( typeof sounds.sent !== 'undefined' ) {
                        sounds.sent.play();
                    }

                    scrollBottom('.bp-messages-wrap[data-thread-id="' +  _thread + '"]');
                    scrollBottom('.bp-better-messages-mini .chats .chat[data-thread="' +  _thread + '"]');
                    //editing = false;
                    form += '&tempID=' + response;
                    $.post(BP_Messages.ajaxUrl, form, function (data) {
                        if (typeof data.result == 'undefined') return;

                        $.each(data['errors'], function(){
                            BBPMShowError(this);
                        });

                        if( typeof data.redirect !== 'undefined' ){
                            if( data.redirect === 'refresh' ){
                                var wrap = _form.closest('.bp-messages-wrap');
                                if( wrap.hasClass('bp-better-messages-mini') ){
                                    $('.bp-better-messages-mini .chats .chat[data-thread="' + _thread + '"]').remove();
                                    openMiniChat( _thread, true );
                                } else {
                                    ajaxRefresh(location.href, _form.closest('.bp-messages-wrap'));
                                }
                            }
                        }
                    }).always(function() {
                        sendingMessage = false;
                    });
                });
            } else {
                $.post(BP_Messages.ajaxUrl, form, function (data) {
                    if (typeof data.result == 'undefined') return;
                    if (data.result !== false) {
                        refreshThread();
                        $(document).trigger("bp-better-messages-message-sent");

                        if( ! isEdit ) {

                            if( typeof sounds.sent !== 'undefined' ) {
                                sounds.sent.play();
                            }

                            scrollBottom('.bp-messages-wrap[data-thread-id="' + _thread + '"]');
                            scrollBottom('.bp-better-messages-mini .chats .chat[data-thread="' + _thread + '"]');
                        }

                        lastForm = form;
                        clearInterval(lastFormTimeout);
                        lastFormTimeout = setInterval(function(){lastForm = ''}, 3000);

                        if( isReply ){
                            reply.find('.bpbm-preview-message-cancel').click();
                        }

                        if( isEdit ){
                            edit.find('.bpbm-preview-message-cancel').click();
                            if ( BP_Messages['realtime'] != "1" ) {
                                ajaxRefresh(location.href, _form.closest('.bp-messages-wrap'));
                            }
                        }

                    } else {
                        $.each(data['errors'], function(){
                            BBPMShowError(this);
                        });

                        if( typeof data.redirect !== 'undefined' ){
                            if( data.redirect === 'refresh' ){
                                ajaxRefresh(location.href, _form.closest('.bp-messages-wrap'));
                            }
                        }

                    }
                }).always(function() {
                    sendingMessage = false;
                });
            }

            $(this).find('textarea, .bp-emojionearea-editor').html('').val('');
            $(document).trigger("bp-better-messages-message-sent-end");
        });

        bpMessagesWrap.on('click ', '.bpbm-stickers-selector .bpbm-stickers-selector-sticker', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var sticker  = $(this);
            var stickersDiv = sticker.closest('.bpbm-stickers-selector');
            var container   = stickersDiv.parent();
            var formEl      = container.find('.reply form');

            var stickerId  = sticker.data('sticker-id');
            var stickerImg = sticker.find('img').attr('src');
            var threadId   = formEl.find('input[name="thread_id"]').val();
            var nonce      = formEl.find('input[name="_wpnonce"]').val();

            stickersDiv.hide();
            makeHeightBeautiful();

            $.post(BP_Messages.ajaxUrl, {
                'action'      : 'bpbm_messages_send_sticker',
                'thread_id'   : threadId,
                'sticker_id'  : stickerId,
                'sticker_img' : stickerImg,
                '_wpnonce'    : nonce,
            }, function (data) {
            });
        });

        bpMessagesWrap.on('click ', '.bpbm-gifs-selector .bpbm-gifs-selector-gif', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var gif        = $(this);
            var gifsDiv    = gif.closest('.bpbm-gifs-selector');
            var container  = gifsDiv.parent();
            var formEl     = container.find('.reply form');

            var gifId      = gif.data('gif-id');
            var threadId   = formEl.find('input[name="thread_id"]').val();
            var nonce      = formEl.find('input[name="_wpnonce"]').val();

            gifsDiv.hide();
            makeHeightBeautiful();

            $.post(BP_Messages.ajaxUrl, {
                'action'      : 'bpbm_messages_send_gif',
                'thread_id'   : threadId,
                'gif_id'      : gifId,
                '_wpnonce'    : nonce,
            }, function (data) {});
        });

        bpMessagesWrap.on('click touchstart', '.reply .bpbm-stickers-btn', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var replyDiv = $(this).closest('.reply');
            var wrapper  = replyDiv.parent();
            var formEl   = replyDiv.find('form');
            var textarea = formEl.find('textarea');
            var stickersDiv = wrapper.find('.bpbm-stickers-selector');
            textarea.blur();

            if( ! stickersDiv.is(':visible') ){
                stickersDiv.show();
                //makeHeightBeautiful();
            }
        });


        bpMessagesWrap.on('click touchstart', '.reply .bpbm-gifs-btn', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var replyDiv = $(this).closest('.reply');
            var wrapper  = replyDiv.parent();
            var formEl   = replyDiv.find('form');
            var textarea = formEl.find('textarea');
            var gifDiv = wrapper.find('.bpbm-gifs-selector');
            textarea.blur();

            if( ! gifDiv.is(':visible') ){
                gifDiv.show();
                gifDiv.find('.bpbm-gifs-tabs span[data-package-id="trending"]').click();
                //makeHeightBeautiful();
            }
        });


        bpMessagesWrap.on('click', '.bpbm-gifs-selector .bpbm-gifs-close', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var gifDiv = $(this).closest('.bpbm-gifs-selector');
            gifDiv.hide();
            gifDiv.find('.bpbm-gifs-tabs .bpbm-gifs-tabs-active').removeClass('bpbm-gifs-tabs-active');
            gifDiv.find('.bpbm-gifs-selector-gif-list').html('');
        });

        bpMessagesWrap.on('click', '.bpbm-gifs-selector .bpbm-gifs-head .bpbm-gifs-tabs span', function(event){
            event.preventDefault();
            event.stopPropagation();

            var activeClass  = 'bpbm-gifs-tabs-active';
            var tab          = $(this);
            var gifDiv       = tab.closest('.bpbm-gifs-selector');
            var gifContainer = gifDiv.find('.bpbm-gifs-selector-gif-container');
            if( ! tab.hasClass(activeClass) ){
                gifDiv.find('.bpbm-gifs-tabs span').removeClass(activeClass);
                tab.addClass(activeClass);
                gifContainer.html(loadingHtml);


                var selected = tab.data('package-id');
                $.post(BP_Messages.ajaxUrl, {
                    'action'  : 'bpbm_messages_get_gif_tab',
                    'package' : selected,
                }, function (data) {
                    gifContainer.html( data );

                    if( selected === 'trending' ) {
                        var loadingClass = 'bpbm-loading-gifs';

                        gifContainer.on('scroll', function (event){
                            event.stopImmediatePropagation();
                            var elem = $(event.currentTarget);
                            var height = elem.outerHeight() + 150;

                            if(elem[0].scrollHeight - elem.scrollTop() <= height){
                                var gifsList = gifContainer.find('.bpbm-gifs-selector-gif-list');
                                if ( ! gifsList.hasClass(loadingClass) ) {
                                    var maxPages   = gifsList.data('pages');
                                    var pageToLoad = gifsList.data('pages-loaded') + 1;

                                    if( pageToLoad < maxPages ) {
                                        gifsList.addClass(loadingClass);
                                        gifsSearchLoading = $.post(BP_Messages.ajaxUrl, {
                                            'action'  : 'bpbm_messages_get_gif_tab',
                                            'package' : selected,
                                            'page': pageToLoad
                                        }, function (response) {
                                            gifsList.data('pages-loaded', pageToLoad);
                                            $(response).appendTo(gifsList);
                                            gifsList.removeClass(loadingClass);
                                        });
                                    }
                                }
                            }
                        });
                    }
                });
            }
        });



        bpMessagesWrap.on('click', '.bpbm-stickers-selector .bpbm-stickers-close', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var stickersDiv = $(this).closest('.bpbm-stickers-selector');
            var container   = stickersDiv.parent();

            stickersDiv.hide();
        });

        bpMessagesWrap.on('click', '.bpbm-stickers-selector .bpbm-stickers-head .bpbm-stickers-tabs span', function(event){
            event.preventDefault();
            event.stopPropagation();

            var tab      = $(this);
            var stickersDiv = tab.closest('.bpbm-stickers-selector');
            var stickersContainer = stickersDiv.find('.bpbm-stickers-selector-sticker-container');

            stickersDiv.find('.bpbm-stickers-tabs span').removeClass('bpbm-stickers-tabs-active');
            tab.addClass('bpbm-stickers-tabs-active');
            stickersContainer.html(loadingHtml);

            var selected = tab.data('package-id');
            $.post(BP_Messages.ajaxUrl, {
                'action'  : 'bpbm_messages_get_sticker_tab',
                'package' : selected,
            }, function (data) {
                stickersContainer.html( data );
            });
        });

        var gifsSearchLoading;

        bpMessagesWrap.on('keyup change', '.bpbm-gifs-tabs input[name="search"]', function(event){
            event.preventDefault();
            event.stopPropagation();

            var input         = $(this);
            var gifsDiv       = input.closest('.bpbm-gifs-selector');
            var gifsContainer = gifsDiv.find('.bpbm-gifs-selector-gif-container');
            var gifsList      = gifsDiv.find('.bpbm-gifs-selector-gif-list');

            var loadingSearch = input.data('search-term');
            var newSearch = input.val();

            if( loadingSearch !== newSearch ) {
                input.data('search-term', newSearch);
                if (gifsSearchLoading) {
                    gifsSearchLoading.abort();
                }

                var loadingClass = 'bpbm-loading-gifs';

                gifsList.addClass(loadingClass);
                gifsSearchLoading = $.post(BP_Messages.ajaxUrl, {
                    'action': 'bpbm_messages_search_gifs',
                    'search': newSearch
                }, function (data) {
                    gifsList.replaceWith(data);

                    gifsContainer.on('scroll', function (event){
                        event.stopImmediatePropagation();
                        var elem = $(event.currentTarget);
                        var height = elem.outerHeight() + 150;

                        if(elem[0].scrollHeight - elem.scrollTop() <= height){
                            gifsList = gifsDiv.find('.bpbm-gifs-selector-gif-list');
                            if ( ! gifsList.hasClass(loadingClass) ) {
                                var maxPages   = gifsList.data('pages');
                                var pageToLoad = gifsList.data('pages-loaded') + 1;

                                if( pageToLoad < maxPages ) {
                                    gifsList.addClass(loadingClass);
                                    gifsSearchLoading = $.post(BP_Messages.ajaxUrl, {
                                        'action': 'bpbm_messages_search_gifs',
                                        'search': newSearch,
                                        'page': pageToLoad
                                    }, function (response) {
                                        gifsList.data('pages-loaded', pageToLoad);
                                        $(response).appendTo(gifsList);
                                        gifsList.removeClass(loadingClass);
                                    });
                                }
                            }
                        }
                    });
                });
            }
        });

        var stickersSearchLoading;

        bpMessagesWrap.on('keyup change', '.bpbm-stickers-selector .bpbm-stickers-search input', function(event){
            event.preventDefault();
            event.stopPropagation();

            var input        = $(this);
            var searchDiv    = input.closest('.bpbm-stickers-search');
            var stickersDiv  = input.closest('.bpbm-stickers-selector');
            var stickersList = stickersDiv.find('.bpbm-stickers-selector-sticker-list');

            var loadingSearch = searchDiv.data('search-term');
            var newSearch = input.val();

            if( loadingSearch !== newSearch ) {
                searchDiv.data('search-term', newSearch);
                if (stickersSearchLoading) {
                    stickersSearchLoading.abort();
                }

                stickersSearchLoading = $.post(BP_Messages.ajaxUrl, {
                    'action': 'bpbm_messages_search_stickers',
                    'search': newSearch
                }, function (data) {
                    stickersList.replaceWith(data);
                    stickersList = stickersDiv.find('.bpbm-stickers-selector-sticker-list');

                    stickersList.BPBMoverlayScrollbars({
                        'sizeAutoCapable': false,
                        callbacks : {
                            onScroll : function( arg1 ){
                                var position  = this.scroll();
                                var scroll    = position.position.y;
                                var height    = position.max.y;

                                if( height - scroll <= 10 ) {
                                    var elements     = this.getElements();
                                    var host         = BPBMJQ(elements.host);
                                    var loadingClass = 'bpbm-loading-stickers';
                                    if( ! host.hasClass(loadingClass) ){
                                        var maxPages   = host.data('pages');
                                        var pageToLoad = host.data('pages-loaded') + 1;
                                        if( pageToLoad <= maxPages ){
                                            host.addClass(loadingClass);
                                            $.post(BP_Messages.ajaxUrl, {
                                                'action' : 'bpbm_messages_search_stickers',
                                                'search' : newSearch,
                                                'page'   : pageToLoad
                                            }, function(response){
                                                host.data('pages-loaded', pageToLoad);
                                                $(response).appendTo( BPBMJQ(elements.content) );
                                                host.removeClass(loadingClass);
                                            });
                                        }
                                    }

                                }
                            }
                        }
                    });
                });
            }
        });


        /**
         * New Thread Submit
         */
        bpMessagesWrap.on('submit', '.new-message form', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var container = BPBMJQ(event.target).closest('.bp-messages-wrap');

            showPreloader(container);
            //$('.bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list) .preloader').show();

            var textarea = $(this).find('textarea[name="message"]');

            BPBMformatTextArea(textarea);

            var form = $(this);
            var data = form.serialize();

            $.post(BP_Messages.ajaxUrl, data, function (data) {
                if (data.result) {
                    ajaxRefresh(BP_Messages.threadUrl + data['result'], container);
                } else {
                    form.closest('.bp-messages-wrap').find('.preloader').hide();

                    $.each(data['errors'], function(){
                        BBPMShowError(this);
                    });
                }

            }).fail(function() {
                form.closest('.bp-messages-wrap').find('.preloader').hide();
            });
        });

        /**
         * Switches screens without page reloading
         */
        bpMessagesWrap.on('click', 'a.ajax', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var href = $(this).attr('href');
            var container = $(this).closest('.bp-messages-wrap');

            ajaxRefresh(href, container);
        });

        /**
         *  Search form functions
         */
        bpMessagesWrap.on('click', '.bpbm-search a.search', function (event) {
            event.preventDefault();
            event.stopPropagation();

            $(this).hide();
            $('.bpbm-search form').show();
            $('.bpbm-search form input').trigger('focus');

            if( isMobile ){
                $('.bp-messages-wrap .chat-header .settings').hide();
            }
        });

        bpMessagesWrap.on('click', '.bpbm-search form span.close', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var form = $(this).closest('.bpbm-search');
            var input = form.find('form input');
            if( form.closest('.bp-messages-side-threads').length > 0 ){
                input.val('');
                input.trigger('change');
                form.find('form .close').hide();
            } else {
                form.find('form').hide();
                form.find('a.search').show();
                input.val('');
            }

        });


        bpMessagesWrap.on('submit', '.bpbm-search form', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');
            ajaxRefresh(BP_Messages['url'] + '?' + $(this).serialize(), container );
        });

        /**
         * Send message on Enter
         */
        if( (BP_Messages['disableEnterForTouch'] === '1' && isMobile || BP_Messages['disableEnterForDesktop'] === '1' && !isMobile) === false ){

            bpMessagesWrap.on('keydown', '.reply .bp-emojionearea-editor', function (event) {
                if ( ! event.shiftKey && event.keyCode == 13 ) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    $(this).trigger('blur')
                    $(this).parent().parent().trigger("submit");
                    $(this).trigger('focus')
                }
            });
        }

        bpMessagesWrap.on('change', '.new-message .send-to-input', function (event) {
            event.preventDefault();
            event.stopPropagation();

            var href = $(this).attr('href');
            var container = $(this).closest('.bp-messages-wrap');

            ajaxRefresh(href, container);
        });


        if( isMobile ){
            bpMessagesWrap.on('touchend', '.scroller.starred .messages-list li, .scroller.search .messages-list li', function(event){
                if(touchmoved != true && blockOpen === false){
                    event.preventDefault();
                    event.stopPropagation();

                    var thread_id = $(this).attr('data-thread');
                    var message_id = $(this).attr('data-id');
                    ajaxRefresh(BP_Messages.threadUrl + thread_id + '&message_id=' + message_id, $(this).closest('.bp-messages-wrap'));
                }
            }).on('touchmove', function(e){
                touchmoved = true;
            }).on('touchstart', function(e){
                touchmoved = false;
            });
        } else {
            bpMessagesWrap.on('click', '.scroller.starred .messages-list li, .scroller.search .messages-list li', function (event) {
                event.preventDefault();
                event.stopPropagation();

                var thread_id = $(this).attr('data-thread');
                var message_id = $(this).attr('data-id');
                ajaxRefresh(BP_Messages.threadUrl + thread_id + '&message_id=' + message_id, $(this).closest('.bp-messages-wrap'));
            });
        }

        /**
         * Remove users from group thread
         */
        bpMessagesWrap.on('click', '.participants-panel .bp-messages-user-list .user .actions a.remove-from-thread', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');
            var user = $(this).parent().parent();
            var user_id = user.data('id');
            var thread_id = user.data('thread-id');
            var username = user.find('.name').text();

            var excl_msg = BP_Messages['strings']['exclude'];
            if( container.hasClass('bp-messages-wrap-chat') ){
                excl_msg = BP_Messages['strings']['exclude_chat'];
            }
            excl_msg = excl_msg.replace('%s', username);

            var exclude = confirm(excl_msg);

            if(exclude){
                $.post(BP_Messages.ajaxUrl, {
                    action: 'bp_better_messages_exclude_user_from_thread',
                    user_id: user_id,
                    thread_id: thread_id
                }, function(response){
                    if(response.result === true){
                        var url = BP_Messages['url'] + '?' + $.param({thread_id: thread_id, participants: "1"});
                        ajaxRefresh(url, container);
                    }
                });
            }
        });

        /**
         * Mute thread
         */
        bpMessagesWrap.on('click', '.bpbm-mute-thread', function (event) {
            event.preventDefault();

            var container = $(this).closest('.bp-messages-wrap');
            var isMini = $(this).closest('.bp-better-messages-mini').length > 0;
            var url = location.href;
            var thread_id;

            if(isMini) {
                thread_id = $(this).closest('.chat').attr('data-thread');
            } else {
                thread_id = container.attr('data-thread-id');

                if (container.is('#bp-better-messages-mini-mobile-container')) {
                    thread_id = container.attr('data-thread');
                    url = BP_Messages['baseUrl'] + '?thread_id=' + thread_id;
                }
            }

            if( typeof thread_id === 'undefined' ) return false;

            if(url.indexOf('?thread_id=') === -1) {
                url = BP_Messages['baseUrl'] + '?thread_id=' + thread_id;
            }

            var confirmDelete = confirm(BP_Messages['strings']['mute_thread']);

            if( confirmDelete ){

                if( isMini ) {
                    showPreloader($(this).closest('.chat'));
                } else {
                    showPreloader(container);
                }

                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'       : 'bp_messages_mute_thread',
                    'thread_id'    : thread_id
                }, function (response) {
                    if( isMini ){
                        openMiniChat( thread_id, true, true );
                    } else {
                        ajaxRefresh(url, container);
                    }
                    var counterRow = $(threadsSelector + '[data-id="' + thread_id + '"] .bpbm-counter-row');
                    counterRow.prepend('<span class="bpbm-thread-muted"><i class="fas fa-bell-slash"></i></span>');

                    BP_Messages.mutedThreads[ thread_id ] = Date.now();
                }).always(function() {
                });
            }
        });

        /**
         * Leave thread
         */
        bpMessagesWrap.on('click', '.bpbm-leave-thread', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');
            var isMini = $(this).closest('.bp-better-messages-mini').length > 0;
            var url = location.href;
            var thread_id;

            if(isMini) {
                thread_id = $(this).closest('.chat').attr('data-thread');
            } else {
                thread_id = container.attr('data-thread-id');

                if (container.is('#bp-better-messages-mini-mobile-container')) {
                    thread_id = container.attr('data-thread');
                    url = BP_Messages['baseUrl'] + '?thread_id=' + thread_id;
                }
            }

            if( typeof thread_id === 'undefined' ) return false;

            var confirmDelete = confirm(BP_Messages['strings']['leave_thread']);

            if( confirmDelete ){
                if( isMini ) {
                    showPreloader($(this).closest('.chat'));
                } else {
                    showPreloader(container);
                }



                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'       : 'bp_messages_leave_thread',
                    'thread_id'    : thread_id
                }, function (response) {
                    if( response ){
                        if(typeof socket !== 'undefined') {
                            socket.emit('threadOpen', thread_id);
                        }
                        $(threadsSelector + '[data-id="' + thread_id + '"]').remove();

                        if( isMini ){
                            $('.bp-better-messages-mini .chats .chat[data-thread="' + thread_id + '"] > .head .close').click();
                        } else {
                            ajaxRefresh(BP_Messages['baseUrl'], container);
                        }
                    } else {
                        container.find('.preloader').hide();
                    }
                });
            }
        });

        bpMessagesWrap.on('click', '.bpbm-delete-thread', function (event) {
            event.preventDefault();

            var container = $(this).closest('.bp-messages-wrap');
            var isMini = $(this).closest('.bp-better-messages-mini').length > 0;
            var url = location.href;
            var thread_id;

            if(isMini) {
                thread_id = $(this).closest('.chat').attr('data-thread');
            } else {
                thread_id = container.attr('data-thread-id');

                if (container.is('#bp-better-messages-mini-mobile-container')) {
                    thread_id = container.attr('data-thread');
                    url = BP_Messages['baseUrl'];
                }
            }

            if( typeof thread_id === 'undefined' ) return false;

            if(url.indexOf('?thread_id=') === -1) {
                url = BP_Messages['baseUrl'];
            }

            var confirmDelete = confirm(BP_Messages['strings']['delete_thread']);

            if( confirmDelete ){

                if( isMini ) {
                    showPreloader($(this).closest('.chat'));
                } else {
                    showPreloader(container);
                }

                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'    : 'bp_messages_erase_thread',
                    'thread_id' : thread_id,
                    '_wpnonce'  : BP_Messages['editNonce']
                }, function (response) {
                    $('.bp-messages-wrap .threads-list .thread[data-id="' + thread_id + '"]').remove();
                    $('.bp-better-messages-mini .chats .chat[data-thread="' + thread_id + '"] .head span.close').click();
                    if( ! isMini ){
                        ajaxRefresh(url, container);
                    }
                }).always(function() {
                });
            }
        });


        bpMessagesWrap.on('click', '.bpbm-clear-thread', function (event) {
            event.preventDefault();

            var container = $(this).closest('.bp-messages-wrap');
            var isChat   = container.hasClass('bp-messages-wrap-chat');
            var isGroup   = container.hasClass('bp-messages-group-thread');
            var isMini = $(this).closest('.bp-better-messages-mini').length > 0;
            var url = location.href;
            var thread_id;

            if(isMini) {
                thread_id = $(this).closest('.chat').attr('data-thread');
            } else {
                thread_id = container.attr('data-thread-id');

                if (container.is('#bp-better-messages-mini-mobile-container')) {
                    thread_id = container.attr('data-thread');
                    url = BP_Messages['baseUrl'];
                }
            }

            if( typeof thread_id === 'undefined' ) return false;

            if(url.indexOf('?thread_id=') === -1) {
                url = BP_Messages['baseUrl'];
            }

            if( isChat || isGroup ){
                url = BP_Messages['baseUrl'] + '?thread_id=' + thread_id;
            }

            var confirmDelete = confirm(BP_Messages['strings']['clear_chat_thread']);

            if( confirmDelete ){

                if( isMini ) {
                    showPreloader($(this).closest('.chat'));
                } else {
                    showPreloader(container);
                }

                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_clear_thread',
                    'thread_id': thread_id,
                    '_wpnonce': BP_Messages['editNonce']
                }, function (response) {
                    if (isMini) {
                        openMiniChat(thread_id, true, true);
                    } else {
                        ajaxRefresh(url, container);
                    }
                }).always(function () {
                });
            }
        });

        bpMessagesWrap.on('click', '.chat-header .bpbm-invite-thread', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');

            var addUserPanel = container.find('.add-user-panel');
            var threadScroll = container.find('.scroller.thread');
            var participants = container.find('.participants-panel');

            if( ! addUserPanel.hasClass('open') ){
                addUserPanel.addClass('open');
                participants.removeClass('open');
                threadScroll.hide();
            }
        });

        bpMessagesWrap.on('click', '.chat-header .participants', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');

            var participants = container.find('.participants-panel');
            var threadScroll = container.find('.scroller.thread');
            var addUserPanel = container.find('.add-user-panel');
            var thread_id = parseInt(container.attr('data-thread-id'));

            if( ! participants.hasClass('participants-loaded') ){
                participants.addClass('participants-loaded');

                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'       : 'bp_messages_load_thread_participants',
                    'thread_id'    : thread_id
                }, function (response) {
                    participants.find('.bp-messages-user-list').html(response);
                }).always(function() {
                });
            }

            if( ! participants.hasClass('open') ){
                participants.addClass('open');
                addUserPanel.removeClass('open');
                threadScroll.hide();
            } else {
                participants.removeClass('open');
                threadScroll.show();
            }
        });


        /**
         * Mute thread
         */
        bpMessagesWrap.on('click', '.bpbm-unmute-thread', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');
            var isMini = $(this).closest('.bp-better-messages-mini').length > 0;
            var url = location.href;
            var thread_id;

            if(isMini) {
                thread_id = $(this).closest('.chat').attr('data-thread');
            } else {
                thread_id = container.attr('data-thread-id');

                if (container.is('#bp-better-messages-mini-mobile-container')) {
                    thread_id = container.attr('data-thread');
                    url = BP_Messages['baseUrl'] + '?thread_id=' + thread_id;
                }
            }

            var confirmDelete = confirm(BP_Messages['strings']['unmute_thread']);

            if( confirmDelete ){

                if( isMini ) {
                    showPreloader($(this).closest('.chat'));
                } else {
                    showPreloader(container);
                }

                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'       : 'bp_messages_unmute_thread',
                    'thread_id'    : thread_id
                }, function (response) {
                    if( typeof BP_Messages.mutedThreads[ thread_id ] !== 'undefined' ){
                        delete BP_Messages.mutedThreads[ thread_id ];
                    }

                    $(threadsSelector + '[data-id="' + thread_id + '"] .bpbm-counter-row .bpbm-thread-muted').remove();

                    if( isMini ){
                        openMiniChat( thread_id, true, true );
                    } else {
                        ajaxRefresh(url, container);
                    }
                }).always(function() {
                });
            }
        });

        /**
         * Add new users to group thread
         */
        bpMessagesWrap.on('click', '.add-user button[type="submit"]', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');
            var form      = $(this).closest('.add-user');
            var thread_id = form.data('thread-id');
            var users = [];

            form.find( 'input[name="recipients[]"]' ).each(function(){
                users.push( $(this).val() );
            });

            showPreloader( container );

            $.post(BP_Messages.ajaxUrl, {
                action: 'bp_better_messages_add_user_to_thread',
                users: users,
                thread_id: thread_id
            }, function(response){
                var url = BP_Messages['url'] + '?' + $.param( { thread_id: thread_id, participants: "1" });
                ajaxRefresh(url, container);
            });
        });

        bpMessagesWrap.on('click', '.add-user button.bpbm-close', function (event){
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');

            var addUserPanel = container.find('.add-user-panel');
            var threadScroll = container.find('.scroller.thread');

            if( addUserPanel.hasClass('open') ){
                addUserPanel.removeClass('open');
                threadScroll.show();
            }
        });

        bpMessagesWrap.on('click', '.bpbm-join-to-chat-button', function (event){
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');
            var chat_id = container.data('chat-id');
            var thread_id = container.data('thread-id');
            showPreloader( container );

            $.post(BP_Messages.ajaxUrl, {
                action: 'bp_better_messages_join_chat',
                chat_id: chat_id
            }, function(response){
                var url = BP_Messages['url'] + '?' + $.param( { thread_id: thread_id });
                ajaxRefresh(url, container);
            });
        });

        bpMessagesWrap.on('click', '.bpbm-leave-chat-room', function (event){
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');
            var chat_id = container.data('chat-id');
            var thread_id = container.data('thread-id');
            showPreloader( container );

            $.post(BP_Messages.ajaxUrl, {
                action: 'bp_better_messages_leave_chat',
                chat_id: chat_id
            }, function(response){
                var url = BP_Messages['url'] + '?' + $.param( { thread_id: thread_id });
                ajaxRefresh(url, container);
            });
        });





        

        

        $(document).on('click', '.bp-better-messages-list .tabs > div', function (event) {
            event.preventDefault();
            var tab = $(this).data('tab');

            if( tab === 'bpbm-close' ){
                $('.bp-better-messages-list .tabs > div, .bp-better-messages-list .tabs-content > div').removeClass('active');
                $('.bp-better-messages-list .tabs > div[data-tab="bpbm-close"]').hide();
                miniMessages = false;
            } else if (! $(this).hasClass('active') ) {
                $('.bp-better-messages-list .tabs > div, .bp-better-messages-list .tabs-content > div').removeClass('active');
                $(this).addClass('active');
                $('.bp-better-messages-list .tabs-content .' + tab).addClass('active');
                miniMessages = tab;
                $('.bp-better-messages-list .tabs > div[data-tab="bpbm-close"]').show();
            } else {
                $(this).removeClass('active');
                $('.bp-better-messages-list .tabs-content .' + tab).removeClass('active');
                $('.bp-better-messages-list .tabs > div[data-tab="bpbm-close"]').hide();
                miniMessages = false;
            }

            store.set('bp-better-messages-mini-messages', miniMessages);
        });


        $(document).on('click', '.bpbm-deleted-user-link', function (event) {
            event.preventDefault();
        });

        $(document).on('click', '.bp-better-messages-list .new-message', function(event) {
            var mainContainer = $('.bp-messages-wrap.bp-messages-wrap-main');
            if( mainContainer.length > 0 ){
                event.preventDefault();
                ajaxRefresh($(this).attr('href'), mainContainer);
            }
        });

        $(document).on('click', '.bp-messages-group-list .group:not(.blocked)', function(event){
            if($(event.target).is('div')){
                event.preventDefault();
                var group     = $(this);
                var group_id  = group.data('group-id');
                var thread_id = group.data('thread-id');
                var url       = group.find('.actions .open-group').attr('href');
                if( BP_Messages['enableGroups'] === '1' ) url += 'bp-messages/?scrollToContainer';
                var scroller = group.parent().parent();
                var height   = group.height();
                var top      = group.position().top;
                top          = top + scroller.scrollTop();
                group.find('.loading').css({
                    'height': height,
                    'line-height': height + 'px',
                    'top': top + 'px'
                });

                group.addClass('blocked loading');


                if (BP_Messages['enableGroups'] === '1' && BP_Messages['miniChats'] == '1' && !! thread_id ){
                    openMiniChat(thread_id, true).always(function (done) {
                        group.removeClass('blocked loading');
                    });

                } else {
                    location.href = url;
                }
            }
        });

        $(document).on('click', '.bp-messages-user-list .user:not(.blocked)', function(event){
           if($(event.target).is('div')){
               event.preventDefault();
               var user = $(this);
               var user_id = $(this).data('id');
               var username = $(this).data('username');

               if (BP_Messages['miniChats'] == '1' && BP_Messages['fastStart'] == '1') {
                   var scroller = user.parent().parent();
                   var height = $(user).height();
                   var top = user.position().top;
                   top = top + scroller.scrollTop();
                   $(user).find('.loading').css({
                       'height': height,
                       'line-height': height + 'px',
                       'top': top + 'px'
                   });

                   user.addClass('blocked loading');

                   openPrivateThread(user_id).always(function (done) {
                        user.removeClass('blocked loading');
                   });
               } else {
                   var redirect = BP_Messages['url'] + '?new-message&to=' + username;
                   if(BP_Messages['fastStart'] == '1') redirect += '&fast=1&scrollToContainer';

                   var mainContainer = $('.bp-messages-wrap.bp-messages-wrap-main');
                   if( mainContainer.length > 0 ){
                       ajaxRefresh(redirect, mainContainer);
                   } else {
                       location.href = redirect;
                   }
               }
           }
        });

        

        $(document).on('click', '.bp-messages-wrap .bpbm-user-me', function (event){
            event.preventDefault();

            $(this).toggleClass('bpbm-open');
        });

        $(document).on('click', '.bp-messages-wrap .bpbm-user-me .bpbm-user-me-popup .bpbm-user-me-popup-list-item', function (event){
            event.stopImmediatePropagation();
            var elem = $(this);

            if( ! elem.is('a') ) {
                event.preventDefault();

                
            }
        });

    });

    function deleteMessage( messages_ids ){
        $.each(messages_ids, function(){
           var message_id = this;

           var li = $('.bp-messages-wrap .list .messages-stack .content .messages-list li[data-id="' + message_id + '"]');
           var stack = li.closest('.messages-stack');
           li.remove();
           var messages = stack.find('.messages-list > li');
           if( messages.length === 0 ){
               stack.remove();
           }

           $(threadsSelector + '[data-message="' + message_id + '"] .info p').text('...');
        });
    }

    function changeMaxHeight(){
        var height = $( window ).height() - 50;
        var admin_bar = $('#wpadminbar');
        if( admin_bar.length > 0 ){
            if( admin_bar.is(':visible') ){
                height = height - admin_bar.height();
            }
        }

        var windowHeight = height;

        if(height > BP_Messages['max_height']) height = BP_Messages['max_height'];
        height = parseInt(height);

        BPBMJQ('.bp-messages-wrap .scroller').each(function(){
            var scroller = $(this);


            var bpMessagesColumn = scroller.closest('.bp-messages-column');
            var wrap             = scroller.closest('.bp-messages-wrap');

            if( wrap.hasClass('bp-better-messages-mini') ){
                var miniChatsHeight = BP_Messages['miniChatsHeight'];

                var replyHeight = wrap.find('.reply').outerHeight();

                if( miniChatsHeight > windowHeight ){
                    miniChatsHeight = windowHeight - replyHeight - 10;
                }

                scroller.css( 'max-height', miniChatsHeight );
                scroller.css( 'height', miniChatsHeight );
            } else if( wrap.hasClass('bp-better-messages-list') ){
                var miniWindowsHeight = BP_Messages['miniWindowsHeight'];
                if( miniWindowsHeight > windowHeight ){
                    miniWindowsHeight = windowHeight - 10;

                    var messages = scroller.closest('.messages');
                    if( messages.length > 0) {
                        var additionalHeight = messages.find('.chat-header');
                        if( additionalHeight.length > 0 ){
                            miniWindowsHeight = miniWindowsHeight - additionalHeight.outerHeight();
                        }
                    }
                }

                scroller.css( 'max-height', miniWindowsHeight );
                scroller.css( 'height', miniWindowsHeight );
            } else if( bpMessagesColumn.length > 0 && bpMessagesColumn.parent().find('.bp-messages-side-threads').is(':visible') ){
                var el = scroller.closest('.bp-messages-threads-wrapper');
                el.css( 'max-height', height );
                el.css( 'height', height );
            } else {
                var el = scroller.closest('.bp-messages-threads-wrapper');

                el.css( 'max-height', height );
                el.css( 'height', height );
            }
        });

        BPBMJQ('.bp-messages-wrap.bp-better-messages-list .scroller').css( 'max-height', height - 50 );
        BPBMJQ('.bp-messages-wrap.bp-better-messages-mini .scroller').css( 'max-height', height - 50 );
    }

    function makeHeightBeautiful(){
        $.each(bpMessagesWrap, function( index, item ){
            var wrap = $(item);
            if( wrap.hasClass('bp-messages-wrap-main') ) {
                var sideThreads = wrap.find('> .bp-messages-threads-wrapper .bp-messages-side-threads');
                if (sideThreads.length > 0) {
                    var threadWrapper = sideThreads.closest('.bp-messages-threads-wrapper');
                    var neededWidth = sideThreads.width() * 2;
                    if( neededWidth < 800 ) neededWidth = 800;

                    if( wrap.width() < neededWidth ){
                        threadWrapper.addClass('threads-hidden');
                        sideThreads.hide();
                    } else {
                        threadWrapper.removeClass('threads-hidden');
                        sideThreads.show();
                    }
                }
            }

            if( wrap.hasClass('bp-messages-mobile') ) {
                var windowHeight = window.innerHeight;
                var usedHeight = 0;
                var headerHeight = wrap.find('.chat-header').outerHeight();
                usedHeight = usedHeight + headerHeight;

                if( wrap.find('.chat-footer:visible').length > 0 ) {
                    usedHeight = usedHeight + wrap.find('.chat-footer:visible').outerHeight();
                }

                if( wrap.find('.reply').length > 0 ) {
                    usedHeight = usedHeight + wrap.find('.reply').outerHeight();
                }

                wrap.find('.scroller').css({
                    'height': windowHeight - usedHeight,
                });

                //bpMessagesWrap.find('.bp-messages-threads-wrapper').css('maxHeight', 'calc( 100% - ' + headerHeight + 'px )')
            } else {
                changeMaxHeight();
            }
        });


    }

    BPBMJQ(document).on('bp-better-messages-update-unread', function( event ) {
        var _unread = BPBMJQ('.bp-better-messages-unread');

        unread = parseInt(event.originalEvent.detail.unread);
        if( isNaN ( unread ) || unread < 0 ) unread = 0;

        store.set('bp-better-messages-last-unread', unread);

        _unread.each(function(){
            var __unread = BPBMJQ(this);
            var is_shortcode = __unread.hasClass('bpbmuc');

            __unread.text(unread);

            if( ! is_shortcode ) {
                if (unread === 0) {
                    __unread.addClass('no-count');
                } else {
                    __unread.removeClass('no-count');
                }
            } else {
                __unread.attr('data-count', unread);
            }

        });

        if( BPBMJQ('body').hasClass('my-account') ) {
            var tab = BPBMJQ('#user-bp_better_messages_tab');
            if (tab.length > 0) {
                var count = tab.find('span.count');

                if (unread > 0) {
                    if (count.length > 0) {
                        count.text(unread);
                    } else {
                        BPBMJQ('<span class="count">' + unread + '</span>').appendTo(tab);
                    }
                } else {
                    count.remove();
                }
            }
        }

    });

    

    function unique(list) {
        var result = [];
        $.each(list, function(i, e) {
            if ($.inArray(e, result) === -1) result.push(e);
        });
        return result;
    }

    function updateMessagesStatus( li ){
        
    }

    /**
     * Function to determine where we now and what we need to do
     */
    function reInit() {
        thread = false;
        threads = false;
        reIniting = true;
        clearTimeout(checkerTimer);

        // Only initialize new media elements.

        /*
        if( typeof $.fn.mediaelementplayer === 'function') {
            $( '.bp-messages-wrap .wp-audio-shortcode, .bp-messages-wrap .wp-video-shortcode' )
                .not( '.mejs-container' )
                .filter(function () {
                    return ! $( this ).parent().hasClass( '.mejs-mediaelement' );
                }).mediaelementplayer();
        }*/

        if( BP_Messages.realtime !== '1' ) {
            BPBMUpdateUnreadCount(BP_Messages.total_unread);
        }

        document.dispatchEvent(new Event('bp-better-messages-reinit-start'));

        updateOpenThreads();

        onlineInit();

        if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list) .thread.scroller[data-users-json]").length > 0) {
            thread = $(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list) .thread.scroller[data-users-json]").attr('data-id');
        } else if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,#bp-better-messages-mini-mobile-container) .threads-list").length > 0) {
            threads = true;
        }

        if (thread) {
            checkerTimer = setTimeout(refreshThread, BP_Messages.threadRefresh);
        } else {
            checkerTimer = setTimeout(refreshSite, BP_Messages.siteRefresh);
        }

        

        var direction = 'ltr';
        if( isRtl ) direction = 'rtl';

        var isEmojiEnabledOnMobile = BP_Messages['mobileEmojiEnable'] === '1';

        if( isEmojiEnabledOnMobile || ( ! isMobile && ! $('body').hasClass('bp-messages-mobile') ) ) {
            var selector = ".bp-messages-wrap .reply .message textarea, " +
                ".bp-messages-wrap .new-message #message-input, " +
                ".bp-messages-wrap .bulk-message #message-input";

            initializeEmojiArea(selector);
        } else {
            var selector = ".bp-better-messages-mini .chats .chat .reply .message textarea";

            initializeEmojiArea(selector);

            $.fn.BPBMloadEmojione(123);
            makeHeightBeautiful();
        }

        function initializeEmojiArea(selector){
            var emojionearea = $(selector).BPemojioneArea({
                tones: true,
                tonesStyle: 'bullet',
                saveEmojisAs : "unicode",
                autocomplete: false,
                stayFocused: false,
                attributes: {
                    dir: direction
                }
            });

            if( typeof emojionearea[0] !== 'undefined' ) {
                emojionearea.closest('form').addClass('bp-emoji-enabled');

                var areaLoaded = false;

                try {
                    emojionearea[0].BPemojioneArea.on("onLoad", function () {
                        afterAreaLoaded();
                    });
                } catch (e){}

                setTimeout(afterAreaLoaded, 333);

                function afterAreaLoaded(){
                    if( areaLoaded === true ) {
                        return false;
                    }

                    var areaExists = emojionearea.next('.bp-emojionearea').length > 0;

                    if( areaExists ) {
                        areaLoaded = true;
                        makeHeightBeautiful();

                        var opts = {
                            toolbar: {
                                allowMultiParagraphSelection: false,
                                buttons: ['bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'removeFormat'],
                                diffLeft: 0,
                                diffTop: -10,
                                firstButtonClass: 'medium-editor-button-first',
                                lastButtonClass: 'medium-editor-button-last',
                                relativeContainer: null,
                                standardizeSelectionStart: false,
                                static: false,
                                align: 'center',
                                sticky: false,
                                updateOnEmptySelection: true,
                            },
                            placeholder: false,
                            imageDragging: false
                        };

                        new BPBM_MediumEditor('.bp-emojionearea-editor', opts);

                        $(emojionearea).html('<p></p>');
                    } else {
                        setTimeout(afterAreaLoaded, 333);
                    }
                }
            }
        }

        if( typeof $.fn.BPBMoverlayScrollbars === 'function' ) {

            BPBMJQ('.bp-better-messages-list .tabs-content .friends .scroller,.bp-better-messages-list .tabs-content .bpbm-groups .scroller,.scroller.search, .scroller.starred').BPBMoverlayScrollbars({
                'sizeAutoCapable': false,
                overflowBehavior: {
                    x: 'hidden'
                },
            });

            BPBMJQ('.bpbm-stickers-selector .bpbm-stickers-head .bpbm-stickers-tabs').BPBMoverlayScrollbars({
                'sizeAutoCapable': false,
                overflowBehavior : {
                    y: 'hidden'
                }
            });

            var scrolling = false;

            var sizeAutoCapable = true;
            BPBMJQ('.scroller.thread').BPBMoverlayScrollbars({
                'sizeAutoCapable': sizeAutoCapable,
                'autoUpdate' : true,
                overflowBehavior: {
                    x: 'hidden'
                },
                callbacks : {
                    onInitialized: function(){
                        var elements   = this.getElements();
                        var position   = this.scroll();
                        var height     = position.max.y;

                        var message_to = false;

                        if (getParameterByName('message_id').length > 0) {
                            var message_id = getParameterByName('message_id');
                            var message = $(elements.content).find( ".messages-list li[data-id='" + message_id + "']");

                            if (message.length > 0) {
                                message_to = message;
                            }
                        }

                        if( message_to !== false ){
                            BPBMJQ(this.getElements().host).addClass('user-scrolled');
                            this.scroll({ el : message_to, scroll : "ifneeded", margin : 20 });
                        } else if( BPBMJQ(elements.host).is(':visible') && height === 0 ){
                            loadMoreMessages(this, true);
                        } else {
                            this.scroll({y: '100%'});
                        }
                    },
                    onScroll : function( arg1 ){
                        var scroller  = this;
                        var position  = this.scroll();
                        var scroll    = position.position.y;
                        var height    = position.max.y;

                        if( height === 0 ){
                            loadMoreMessages(this, true);
                        } else if( scroll === 0 ) {
                            loadMoreMessages(this);
                        } else if( scroll >= height ){
                            setTimeout(function(){
                                scroll    = position.position.y;
                                height    = position.max.y;
                                if( scroll >= height ) {
                                    BPBMJQ(scroller.getElements().host).removeClass('user-scrolled');
                                }
                            }, 300);
                        }
                    },
                    onContentSizeChanged : function( arg1 ){
                        var elements    = this.getElements();
                        var position    = this.scroll();
                        var height      = position.max.y;
                        var scroll      = position.position.y;
                        var host        = BPBMJQ(elements.host);
                        var content     = BPBMJQ(elements.content);
                        var last        = content.find('.list .messages-stack:last .content .messages-list li:last')
                        var last_height = last.outerHeight();
                        if( last_height < 100 ) last_height = 100;
                        var _this = this;

                        if( ! host.hasClass('user-scrolled') || ( height - scroll ) < last_height + 50 ){
                            if( ! isTapped ) {
                                scrolling = true;
                                _this.scroll({y: '100%'}, 100, undefined, function(){
                                    scrolling = false;
                                });
                            }
                        }

                        setTimeout(function(){
                            if( ! host.hasClass('user-scrolled') ){
                                if( ! isTapped ) {
                                    scrolling = true;
                                    _this.scroll({y: '100%'}, 100, undefined, function(){
                                        scrolling = false;
                                    });
                                }
                            }
                        }, 100);

                    },
                    onHostSizeChanged: function (){
                        var scroller  = this;
                        var position  = this.scroll();
                        var scroll    = position.position.y;
                        var height    = position.max.y;

                        if( ! BPBMJQ(scroller.getElements().host).hasClass('user-scrolled') && ! $('body').hasClass('bpbm-os-dragging') ){
                            this.scroll({y: '100%'}, 0);
                        } else if( scroll >= height ){
                            setTimeout(function(){
                                scroll    = position.position.y;
                                height    = position.max.y;
                                if( scroll >= height ) {
                                    BPBMJQ(scroller.getElements().host).removeClass('user-scrolled');
                                }
                            }, 300);
                        }
                    }
                }
            });


            BPBMJQ('.scroller.threads-list-wrapper').BPBMoverlayScrollbars({
                'sizeAutoCapable': false,
                overflowBehavior: {
                    x: 'hidden'
                },
                callbacks : {
                    onInitialized: function(){},
                    onScroll : function( arg1 ){
                        var elements  = this.getElements();
                        var position  = this.scroll();
                        var scroll    = position.position.y;
                        var height    = position.max.y;
                        var host      = BPBMJQ(elements.host);

                        if(height - scroll <= 100 ) {
                            loadMoreThreads(host);
                        }
                    },
                    onContentSizeChanged : function( arg1 ){
                    }
                }
            });
        }

        BPBMJQ('.scrollbar-inner').on('touchstart click mousewheel DOMMouseScroll', function( event ){
            var clicked =  BPBMJQ( event.target );

            if( clicked.closest('.bpbm-reply').length === 0 ) {
                BPBMJQ(this).addClass('user-scrolled');
            }
        });

        initImagesPopup();

        if(BP_Messages['realtime'] == '1') {

            // Load Statuses
            var messages_ids = [];
            var threadsIds   = [];
            $(  '.bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list) .list .messages-stack .content .messages-list li:not(.seen), ' +
                '.bp-messages-wrap.bp-better-messages-mini .chat.open .list .messages-stack .content .messages-list li:not(.seen)'
            ).each(function () {
                var id = $(this).data('id');
                var thread_id = $(this).data('thread');
                messages_ids.push(id);
                threadsIds.push(thread_id);
            });

            socket.emit('getStatuses', messages_ids, function(statuses){
                var requestedIds = {};
                $.each( messages_ids, function () {
                    requestedIds[this] = true;
                });

                $.each(statuses, function(index){
                    delete requestedIds[index];
                    var message = $('.bp-messages-wrap .list .messages-stack .content .messages-list li[data-id="' + index + '"]');
                    message.removeClass('sent delivered seen');
                    if( ! message.hasClass('my') && ! message.hasClass('fast') ){
                        if(this.toString() !== 'seen'){
                            socket.emit('seen', [index]);
                        }
                    } else {
                        var status = 'sent';
                        $.each(this, function(){
                           if(status == 'seen') return false;
                            if(this == 'delivered' && status != 'seen') status = 'delivered';
                            if(this == 'seen') status = 'seen';
                        });

                        message.addClass(status);
                        var statusTitle = '';
                        switch(status){
                            case 'sent':
                                statusTitle = BP_Messages['strings']['sent'];
                                break;
                            case 'delivered':
                                statusTitle = BP_Messages['strings']['delivered'];
                                break;
                            case 'seen':
                                statusTitle = BP_Messages['strings']['seen'];
                                break;
                        }

                        message.find('.status').attr('title', statusTitle);
                        updateMessagesStatus(message);
                    }
                });

                $.each(requestedIds, function(message_id){
                    var message = $('.bp-messages-wrap .list .messages-stack .content .messages-list li[data-id="' + message_id + '"]');
                    message.removeClass('sent delivered seen');
                    message.addClass('seen');
                    message.find('.status').attr('title', BP_Messages['strings']['seen']);
                    updateMessagesStatus(message);
                });
            });

            if(thread) {
                socket.emit('threadOpen', thread);
            }

            threadsIds = unique(threadsIds);
            $.each(threadsIds, function (index, item) {
                if(typeof item !== 'undefined'){
                    socket.emit('threadOpen', item);
                }
            });

            socket.emit('requestUnread');
        }


        function loadMoreThreads(element, loadUntilScroll) {
            if( typeof loadUntilScroll === 'undefined' ) loadUntilScroll = false;
            var wrapper = $( element );
            if( wrapper.hasClass('loading-more') || wrapper.hasClass('all-loaded') ) return false;

            wrapper.addClass('loading-more');
            wrapper.find('.loading-messages').show();

            var loadedThreads = [];
            var loader = wrapper.find('.loading-messages');

            wrapper.find('.threads-list > .thread').each(function () {
                loadedThreads.push( $(this).data('id') );
            });

            var args = {
                'action'         : 'bp_messages_get_more_threads',
                'loaded_threads' : loadedThreads,
                'user_id'        : BP_Messages['displayed_user_id']
            };

            if( wrapper.closest('.bp-messages-wrap').hasClass('bp-better-messages-list') ){
                args.user_id = BP_Messages['user_id']
            }

            $.post(
                BP_Messages.ajaxUrl,
                args
                , function (html) {
                    $(html).insertBefore( loader );
                    wrapper.removeClass('loading-more');
                    wrapper.find('.loading-messages').hide();

                    if( html.trim() === '' ){
                        wrapper.addClass('all-loaded');
                    } else {
                        if( loadUntilScroll && Math.ceil(element.get(0).scrollHeight) <= Math.ceil(element.innerHeight()) ) {
                            loadMoreThreads(element, true);
                        }
                    }

                }
            );

        }


        makeHeightBeautiful();

        $( window ).on('resize', function() {
            makeHeightBeautiful();
        });

        if ($('.bp-messages-wrap #send-to:not(.ready)').length > 0) {
            var cache = [];

            var sentTo = new Taggle('send-to', {
                placeholder: '',
                tabIndex: 2,
                hiddenInputName: 'recipients[]'
            });
            var container = sentTo.getContainer();
            var input     = sentTo.getInput();

            var to = $('input[name="to"]');

            if (to.length > 0) {
                $(to).each(function () {
                    var img   = $(this).data('img');
                    var label =  $(this).data('label');
                    sentTo.add($(this).val());
                    $(container).find('.taggle_sizer').text('');
                    $('#send-to li.taggle:last .taggle_text').html('<span class="bpbm-avatar"><img src="' + img + '" class="avatar photo" width="50" height="50"></span><span class="bpbm-name">' + label + '</span>');
                    $(this).remove();
                });

                $('#send-to').removeClass('active');
                $('#send-to input').css('width', '10px');
            }

            $(input).on( 'blur', function( event ) {
               var placeholder = $(container).find('.taggle_sizer').text();
               sentTo.add( placeholder );
            });

            if(BP_Messages['disableUsersSearch'] === '0') {
                $(input).autocomplete({
                    source: function (request, response) {
                        var term = request.term;
                        if (term in cache) {
                            response(cache[term]);
                            return;
                        }

                        $.getJSON(BP_Messages.ajaxUrl + "?q=" + term + "&limit=10&action=bp_messages_autocomplete&cookie=" + getAutocompleteCookies(), request, function (data, status, xhr) {
                            cache[term] = data;
                            response(data);
                        });
                    },
                    minLength: 2,
                    appendTo: container,
                    position: {at: "left bottom", of: container},
                    open: function (event, ui) {
                        var autocomplete = $(".bp-messages-wrap #send-to .ui-autocomplete");
                        var oldTop = parseInt(autocomplete.css('top'));
                        var newTop = oldTop - 3;
                        autocomplete.css("top", newTop);
                    },
                    select: function (event, data) {
                        event.preventDefault();
                        //Add the tag if user clicks
                        if (event.which === 1) {
                            sentTo.add(data.item.value);
                            $(container).find('.taggle_sizer').text('');
                            $('#send-to li.taggle:last .taggle_text').html('<span class="bpbm-avatar">' + data.item.img + '</span><span class="bpbm-name">' + data.item.label + '</span>');
                        }
                    },
                    response: function( event, data ){
                        $('.ui-helper-hidden-accessible').hide()
                    }
                }).autocomplete( "instance" )._renderItem = function( ul, item ) {
                    return $( "<li>" )
                        .attr( "data-value", item.value )
                        .attr( "data-label", item.label )
                        .append( '<span class="bpbm-avatar">' + item.img + '</span><span class="bpbm-name">' + item.label + '</span>' )
                        .appendTo( ul );
                };
            }

            $('#send-to').addClass('ready');
        }

        bpMessagesWrap.find('.bp-messages-mobile-tap').css('line-height', bpMessagesWrap.height() + 'px');

        $('.bp-messages-side-threads .threads-list .thread.bp-messages-active-thread').removeClass('bp-messages-active-thread');
        if( thread ){
            $('.bp-messages-side-threads .threads-list .thread[data-id="' + thread + '"]').addClass('bp-messages-active-thread');
        }

        var outgoingItems = {};
        var incomingItems = {};
        var menuEvents = {
            show : function(options){
                options.$trigger.closest('li').addClass('selected');
            },
            hide : function(options){
                options.$trigger.closest('li').removeClass('selected');
            }
        };

        outgoingItems['copy']  = {name: BP_Messages['strings']['copy_text'], icon: "fas fa-copy", callback: function(key, opt){
                var trigger = opt.$trigger;

                var reply = trigger.find('.bpbm-replied-message-reply');
                if( reply.length > 0 ){
                    copyToClipboard(reply.text());
                } else {
                    copyToClipboard(trigger.text());
                }
        }};
        incomingItems['copy']  = {name: BP_Messages['strings']['copy_text'], icon: "fas fa-copy", callback: function(key, opt){
                var trigger = opt.$trigger;

                var reply = trigger.find('.bpbm-replied-message-reply');
                if( reply.length > 0 ){
                    copyToClipboard(reply.text());
                } else {
                    copyToClipboard(trigger.text());
                }

            }};

        if( BP_Messages['allowEditMessages'] === '1' ) {
            outgoingItems['edit'] = {
                name: BP_Messages['strings']['edit'],
                icon: "fas fa-pen",
                callback: function (key, opt) {
                    var trigger = opt.$trigger;
                    var nonce = BP_Messages['editNonce'];
                    var message = trigger.closest('li');
                    var stack = message.closest('.messages-stack');
                    var wrap = stack.closest('.bp-messages-threads-wrapper');
                    var message_id = message.attr('data-id');

                    var content = '';
                    var html_content = '';
                    var reply = message.find('.bpbm-replied-message-reply');
                    if (reply.length > 0) {
                        content += reply.text()
                    } else {
                        content += message.find('.message-content').text()
                    }

                    var descs = message.find('.message-content > [data-desc]');
                    if (descs.length > 0) {
                        descs.each(function () {
                            var _desc = $(this).attr('data-desc');
                            if (typeof _desc !== 'undefined') {
                                html_content += '<span class="bpbm-preview-desc">' + atob(_desc) + '</span>';
                            }
                        });
                    }


                    var html = '<div class="bpbm-preview-message bpbm-edit-message" style="display:none" data-message-id="' + message_id + '">' +
                        '<div class="bpbm-preview-message-cancel"><i class="far fa-times-circle"></i></div>' +
                        '<div class="bpbm-preview-message-content">' +
                        '<span class="bpbm-preview-message-name">' + BP_Messages['strings']['edit_message'] + '</span>' +
                        '<div class="bpbm-preview-message-text"></div>' +
                        '</div>' +
                        '</div>';


                    var previewMessage = wrap.find('.bpbm-preview-message');
                    if( previewMessage.length > 0 ){
                        previewMessage = previewMessage.replaceWith(html);
                        previewMessage = wrap.find('.bpbm-preview-message')
                        previewMessage.show();
                        updateWritingPosition();
                    } else {
                        previewMessage = $(html).insertBefore(wrap.find('.reply'));
                        previewMessage.slideDown(100, function (){
                            updateWritingPosition();
                        });
                    }

                    previewMessage.find('.bpbm-preview-message-text').text(content);
                    previewMessage.find('.bpbm-preview-message-text').append(html_content);

                    previewMessage.find('.bpbm-gifs-icon').html(icons.gif);

                    $.post(BP_Messages['ajaxUrl'], {
                        'action': 'bp_messages_get_edit_message',
                        'message_id': message_id,
                        '_wpnonce': nonce
                    }, function (response) {
                        if( typeof response.errors !== 'undefined' ){
                            BBPMShowError(response.errors[0]);
                            previewMessage.find('.bpbm-preview-message-cancel').click();
                        } else {
                            var emojieditor = wrap.find('.bp-emojionearea-editor');
                            if (emojieditor.length > 0) {
                                emojieditor.html(emojione.toImage(response));
                            } else {
                                wrap.find('.reply .message textarea').html(response);
                            }
                        }
                    });
                },
                visible: function(key, opt){
                    if( opt.$trigger.closest('.bp-messages-threads-wrapper').find('.reply').length > 0 )
                    {
                        return true;
                    } else {
                        return false;
                    }
                }
            };
        }

        var deleteButton = {
            name: BP_Messages['strings']['delete'],
            icon: "fas fa-trash",
            callback: function (key, opt) {
                var trigger = opt.$trigger;
                var nonce = BP_Messages['editNonce'];
                var string = BP_Messages['strings']['confirm_delete'];
                var message = trigger.closest('li');

                var confirmDelete = confirm(string);

                if (confirmDelete) {
                    var messages_ids = [
                        message.attr('data-id')
                    ];

                    var thread = message.attr('data-thread');

                    $.post(BP_Messages['ajaxUrl'], {
                        'action': 'bp_messages_delete_message',
                        'thread_id': thread,
                        'messages_ids': messages_ids,
                        '_wpnonce': nonce
                    }, function (response) {
                        if (response.result) {
                            BBPMNotice(response.message);
                            deleteMessage(messages_ids);
                        } else {
                            BBPMShowError(response.errors[0]);
                        }
                    });
                }
            },
            visible: function(key, opt){


                if( opt.$trigger.closest('.bp-messages-threads-wrapper').find('.reply').length > 0 )
                {

                    if( BP_Messages['allowDeleteMessages'] === '1' ) {
                        if (opt.$trigger.closest('.messages-stack').hasClass('outgoing')) {
                            return true;
                        }
                    }

                    if( opt.$trigger.closest('.list').hasClass('can-moderate') ){
                        return true;
                    }

                    return false;
                } else {
                    return false;
                }
            }
        };


        if( BP_Messages['enableReplies'] === '1') {
            incomingItems['reply'] = {
                name: BP_Messages['strings']['reply'],
                icon: "fas fa-reply",
                callback: function (key, opt) {
                    opt.$trigger.closest('li').find('.bpbm-reply').click();
                },
                visible: function(key, opt){
                    if( opt.$trigger.closest('.bp-messages-threads-wrapper').find('.reply').length > 0 )
                    {
                        return true;
                    } else {
                        return false;
                    }
                }
            };
        }

        outgoingItems['delete'] = deleteButton;
        incomingItems['delete'] = deleteButton;


        $.BPBMcontextMenu({
            // define which elements trigger this menu
            selector: outgoingSelector,
            events: menuEvents,
            items: outgoingItems,
            autoHide: false,
            zIndex: 10,
            position: function(opt, x, y){
                var maxLeft = window.innerWidth - opt.$menu.width() - 100;
                if( x > maxLeft ) x = maxLeft;

                opt.$menu.css({left: x,top: y});
            }
        });

        $.BPBMcontextMenu({
            // define which elements trigger this menu
            selector: incomingSelector,
            events: menuEvents,
            items: incomingItems,
            autoHide: false,
            zIndex: 10,
            position: function(opt, x, y){
                var maxLeft = window.innerWidth - opt.$menu.width() - 100;
                if( x > maxLeft ) x = maxLeft;

                opt.$menu.css({left: x,top: y});
            }
        });

        var threadsMenuItems = {};

        threadsMenuItems['open'] = {
            name: BP_Messages['strings']['open'],
            icon: "fas fa-external-link-square-alt",
            callback: function (key, opt) {
                opt.$trigger.closest('.thread').click();
            }
        };

        threadsMenuItems['delete'] = {
            name: BP_Messages['strings']['delete'],
            icon: "fas fa-trash",
            callback: function (key, opt) {
                opt.$trigger.closest('.thread').find('span.delete').click();
            },
            visible: function(key, opt){
                if( opt.$trigger.closest('.thread').find('span.delete').length > 0 )
                {
                    return true;
                } else {
                    return false;
                }
            }
        };


        $.BPBMcontextMenu({
            // define which elements trigger this menu
            selector: threadsSelector,
            events: menuEvents,
            items: threadsMenuItems,
            autoHide: false,
            zIndex: 10,
            position: function(opt, x, y){
                var maxLeft = window.innerWidth - opt.$menu.width() - 100;
                if( x > maxLeft ) x = maxLeft;

                opt.$menu.css({left: x,top: y});
            }
        });

        initMobileSwipes();

        /**
         * JetPack Lazy Load
         */

        //document.body.dispatchEvent(new Event('post-load'));

        

        calculateTitle(bpMessagesWrap);

        $('.bpbm-gifs-icon').html(icons.gif);
        addIframeClasses();

        

        initTooltips();

        document.dispatchEvent(new Event('bp-better-messages-reinit-end'));

        reIniting = false;
    }

    function initTooltips( wrap ){
        if (typeof wrap === 'undefined') wrap = BPBMJQ('.bp-messages-wrap');

        if( ! isMobile ) {
            wrap.find( '.status[title], .bpbm-reply[title], .chat-header [title], .chat-footer [title], .threads-list [title], .chats .chat .head .controls [title]').each(function () {
                var el = BPBMJQ(this);

                if (el.is('strong') || el.is('input') ) {

                } else {
                    if( typeof (el[0]._tippy) !== 'undefined' ){
                        el[0]._tippy.destroy();
                    }

                    bpbmtippy(el[0], {
                        placement: 'top',
                        content: el.attr('title'),
                        arrow: false,
                        offset: [0, 5],
                        onShow: function(instance) {
                            var reference = BPBMJQ(instance.reference);
                            if( reference.is('.expandingButtons') && reference.hasClass('expandingButtonsOpen') ){
                                return false;
                            }
                        },
                    });

                    el[0].removeAttribute('title');
                }

            });
        }
    }

    function addIframeClasses(){
        $('.bp-messages-iframe-container').each(function(){
            var iframeContainer = $(this);
            var msgContent = iframeContainer.closest('.message-content');

            msgContent.addClass('has-iframe');
        });
    }

    function initMobileSwipes(){
        if( isMobile ){
            $(incomingSelector).BPBMswipe("destroy");
            $(incomingSelector).BPBMswipe({
                longTap : function(event, target) {
                    $(this).contextMenu({
                        x: event.changedTouches[0].screenX,
                        y: event.changedTouches[0].screenY,
                    });
                },
            });
            $(outgoingSelector).BPBMswipe("destroy");
            $(outgoingSelector).BPBMswipe({
                longTap : function(event, target) {
                    $(this).contextMenu({
                        x: event.changedTouches[0].screenX,
                        y: event.changedTouches[0].screenY,
                    });
                },
            });

            $(threadsSelector).BPBMswipe("destroy");
            $(threadsSelector).BPBMswipe({
                longTap : function(event, target) {
                    $(this).contextMenu({
                        x: event.changedTouches[0].screenX,
                        y: event.changedTouches[0].screenY,
                    });
                },
            });

        }
    }

    function copyToClipboard(text) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
    }

    

    function loadMoreMessages(scroller, loadUntilScroll) {
        if( typeof loadUntilScroll === 'undefined' ){
            loadUntilScroll = false;
        }

        var elements = scroller.getElements();
        var wrap = BPBMJQ(elements.host);

        if( wrap.hasClass('loadingAtTheMoment')
            || wrap.hasClass('allMessagesWasLoaded')
        ) return false;


        var thread_id = wrap.data('id');

        wrap.find('.loading-messages').show();
        wrap.addClass('loadingAtTheMoment');
        var last_message = wrap.find('.messages-stack:first-child .messages-list li:first-child');
        var last_message_id = last_message.attr('data-id');

        $.post(
            BP_Messages.ajaxUrl,
            {
                'action'     : 'bp_messages_thread_load_messages',
                'thread_id'  : thread_id,
                'message_id' : last_message_id
            }, function (data) {
                wrap.find('.loading-messages').hide();
                if(data.trim() === '') wrap.addClass('allMessagesWasLoaded');

                $(data).prependTo( wrap.find('.list') );
                wrap.addClass('hasLoadedMessages');

                scroller.scroll( { el: last_message.closest('.messages-stack'), margin : 15 } );

                reInit();
                wrap.removeClass('loadingAtTheMoment');


                if( loadUntilScroll ){
                    if( scroller.scroll().max.y === 0){
                        loadMoreMessages(scroller, true);
                    }
                }
           }
        );
    }

    function scrollBottom(target) {
        if(typeof target == 'undefined') target = '.bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list)';
        if($(target + " .list").length == 0) return;

        var scroller = $(target + ' .scroller[data-id]');
        var thread_id = scroller.data('id');
        if(typeof loadingMore[thread_id] !== 'undefined') return false;

        var list = $(target + " .list");
        var scroll = list[0].offsetHeight;

        if (getParameterByName('message_id').length > 0) {
            var message_id = getParameterByName('message_id');
            var message = $(target + " .messages-list li[data-id='" + message_id + "']");

            if (message.length > 0) {
                scroll = message[0].offsetTop - message[0].offsetHeight - 100;
            }
        }

        var scrollWrapper = list.closest('.scroller');
        var userScrolled  = scrollWrapper.hasClass('user-scrolled');

        var currentScroll = scrollWrapper[0].scrollHeight - scrollWrapper.scrollTop() - scrollWrapper.height();
        //if(  scrollWrapper.scrollTop() === 0 ) currentScroll = 0;

        if(scroll < scrollWrapper.outerHeight()) return;
        if( ! userScrolled || (100 >= currentScroll)){
            var scrollbar = scroller.BPBMoverlayScrollbars();
            if(typeof scrollbar !== 'undefined'){
                scrollbar.update();
                scrollbar.scroll({y: '100%'});
            }
        }
    }

    /**
     * Check for new messages on all sites page
     */
    var refreshSiteRunning = false;
    function refreshSite() {
        clearInterval(checkerTimer);
        checkerTimer = setTimeout(refreshSite, BP_Messages.siteRefresh);

        if (BP_Messages['realtime'] == "1" || refreshSiteRunning) return;

        var last_check = readCookie('bp-messages-last-check');
        refreshSiteRunning = true;
        $.post(BP_Messages.ajaxUrl, {
            'action': 'bp_messages_check_new',
            'last_check': last_check
        }, function (response) {

            if (response.threads.length > 0) {
                $.each(response.threads, function () {
                    var message = this;

                    if(typeof message['avatar'] !== 'undefined' ) {
                        message['avatar'] = message['avatar'].replace("loading='lazy'", '').replace('loading="lazy"', '');
                    }

                    if (threads) {
                        updateThreads(message);
                    } else {
                        showMessage(message.thread_id, message['message'], message['name'], message['avatar']);
                    }
                });
            }

            BPBMUpdateUnreadCount(response.total_unread);

            refreshSiteRunning = false;
        });
    }

    /**
     * Check for new messages on open thread screen
     */
    var refreshThreadRunning = false;

    BPBMJQ(document).on('bp-better-messages-refresh-thread', function (event) {
        refreshThread();
    });

    function refreshThread() {
        clearInterval(checkerTimer);
        checkerTimer = setTimeout(refreshThread, BP_Messages.threadRefresh);
        if (BP_Messages['realtime'] == "1" || refreshThreadRunning) return;

        var last_check = readCookie('bp-messages-last-check');
        var last_message = $('.messages-stack:last-child .messages-list li:last-child').attr('data-time');
        refreshThreadRunning = true;
        $.post(BP_Messages.ajaxUrl, {
            'action': 'bp_messages_thread_check_new',
            'last_check': last_check,
            'thread_id': thread,
            'last_message': last_message
        }, function (response) {
            $.each(response.messages, function () {
                if(typeof this['avatar'] === 'string' ) {
                    this['avatar'] = this['avatar'].replace("loading='lazy'", '').replace('loading="lazy"', '');
                }
                renderMessage(this);
            });

            $.each(response.threads, function () {
                if(typeof this['avatar'] === 'string' ) {
                    this['avatar'] = this['avatar'].replace("loading='lazy'", '').replace('loading="lazy"', '');
                }
                showMessage(this.thread_id, this['message'], this['name'], this['avatar']);
            });

            BPBMUpdateUnreadCount(response.total_unread);

            refreshThreadRunning = false;
        });
    }

    function updateOpenThreads() {
        if ( ! store.enabled )  return false;

        openThreads = store.get('bp-better-messages-open-threads') || {};

        if (thread !== false) {
            openThreads[thread] = Date.now();
        }

        $.each(openThreads, function (index) {
            if ((this + 2000) < Date.now()) delete openThreads[index];
        });

        store.set('bp-better-messages-open-threads', openThreads);
    }


    /**
     * Simple function to avoid page reloading
     *
     * @param url
     */
    function ajaxRefresh(url, container) {
        if( typeof container === 'undefined' ){
            var target = BPBMJQ(event.target);
            if( target.hasClass('bp-messages-wrap') ){
                container = target;
            } else {
                container = target.closest('.bp-messages-wrap');
            }
        }

        if( isInCall ){
            if ( confirm(BP_Messages['strings']['you_are_in_call']) === false ) {
                return false;
            } else {
                BPBMJQ(document).trigger('bpbm-end-call');
            }
        }

        if( container.hasClass('bp-messages-wrap-main') && ! container.hasClass('bp-messages-group-thread') ){
            try {
                window.history.pushState("", "", url);
            } catch(e){}
        }

        showPreloader(container);

        var containerHeight = container.height();
        container.css('min-height', containerHeight);

        $(window).off( ".bp-messages" );

        var target_url = '?action=bp_messages_load_via_ajax';
        if( typeof url.split('?')[1] !== 'undefined' ){
            target_url = '?' + url.split('?')[1] + '&action=bp_messages_load_via_ajax';
        }

        var side_threads = container.find('.bp-messages-side-threads');
        var ajax_url = BP_Messages['ajaxUrl'] + target_url;

        if( side_threads.length > 0 ){
            ajax_url += '&ignore_threads';
        }

        $('.bpbm-notice').remove();

        $.ajax({
            method: "GET",
            url: ajax_url,
            dataType: 'json',
            cache: false,
            success: function (json) {
                if( typeof json['total_unread'] !== 'undefined'){
                    BPBMUpdateUnreadCount(json['total_unread']);
                }

                var _html = json['html'];
                var html = $( _html );

                var side_threads_in_return = html.find('.bp-messages-side-threads');

                if( side_threads.length > 0 && side_threads_in_return.length > 0 ) {
                    container.find('.bp-messages-threads-wrapper .bp-messages-column').html(html.find('.bp-messages-threads-wrapper .bp-messages-column').html());
                    container.find('.bp-messages-threads-wrapper').closest('.bp-messages-wrap').find('.chat-header:not(.side-header)').html(html.find('.bp-messages-threads-wrapper').closest('.bp-messages-wrap').find('.chat-header:not(.side-header)').html());
                } else {
                    container.html( $(html).html() );
                }

                if( container.hasClass('bp-messages-wrap-main') ){
                    container.attr('data-thread-id', html.attr('data-thread-id') );

                    if( typeof json.errors !== 'undefined' ){
                        $(json.errors.join('')).insertBefore(container);
                    }
                }

                if(container.is('#bp-better-messages-mini-mobile-container')){
                    container.attr('data-thread', html.attr('data-thread-id') );
                }

                reInit();

                container.css('min-height', '');
            }
        });
    }

    /**
     * Online avatars init
     */
    function onlineInit() {
        $('.bp-messages-wrap img.avatar[data-user-id]').each(function () {
            var img = $(this);
            var user_id = img.attr('data-user-id');
            var color   = img.attr('data-bpbm-status-color');
            var parent = false;

            if (img.parent().hasClass('bbpm-avatar')) parent = img.parent();

            if (!parent) {
                var data_width   = img.data('size');
                var data_height  = img.data('size');
                var width        = img.height();
                var height       = img.height();

                if(data_width < width ){
                    width  = data_width;
                }

                if(data_height < height ){
                    height = data_height;
                }

                var marginTop    = img.css('marginTop');
                var marginLeft   = img.css('marginLeft');
                var marginBottom = img.css('marginBottom');
                var marginRight  = img.css('marginRight');
                $(this).css({
                    marginTop: 0,
                    marginLeft: 0,
                    marginRight: 0,
                    marginBottom: 0
                });

                var wrap = '<span class="avatar bbpm-avatar" data-user-id="' + user_id + '"';
                if( BP_Messages['userStatus'] !== '0') {
                    if (typeof color !== 'undefined') {
                        wrap += 'style="color:' + color + '"';
                    }
                }
                wrap += '></span>';

                img.wrap(wrap);
                parent = img.parent();

                parent.css({
                    marginTop: marginTop,
                    marginLeft: marginLeft,
                    marginRight: marginRight,
                    marginBottom: marginBottom,
                    minWidth: width,
                    minHeight: height
                });
            }

            if (online.indexOf(user_id) > -1) {
                $(parent).addClass('online');
            } else {
                $(parent).removeClass('online');
            }
        });

        sortOnlineFriends();
    }

    function sortOnlineFriends(){
        var friendsWidget = $('.bp-better-messages-list .tabs-content .friends');
        if( friendsWidget.length > 0 ){
            var friendsList = friendsWidget.find('.bp-messages-user-list');
            if( friendsList.length > 0 ){
                var toOffline = [];
                var friends = friendsList.find(' > .user');
                friends.each(function (){
                    var friend    = $(this);
                    var user_id   = friend.attr('data-id');
                    var prevOnline = friend.prev().hasClass('online');

                    if (online.indexOf(user_id) > -1) {
                        friend.addClass('online');
                        if( ! prevOnline ){
                            friend.prependTo(friendsList);
                        }
                    } else {
                        toOffline.push(friend);
                        friend.removeClass('online');
                    }
                });

                if( toOffline.length > 0 ){
                    $.each(toOffline, function(){
                        var friend = $(this);
                        var nextOnline = friend.next().hasClass('online');

                        if( nextOnline ){
                            friend.insertAfter(friendsList.find(' > .user.online:last'));
                        }
                    });
                }

            }
        }
    }

    /**
     * Refreshes threads on thread list screen
     * @param message
     */
    function updateThreads(message) {
        if( typeof message['html'] === 'undefined' ) return false;
        if( message.fast || message.edit == '1' ) return false;
        $(".bp-messages-wrap .threads-list .empty").remove();
        $(".bp-messages-wrap .bp-messages-threads-wrapper.no-threads").removeClass('no-threads');

        var thread_id = message['thread_id'];

        var html = message['html'];
        html = html.replace('loading="lazy"', '');
        var exist = $(threadsSelector + "[data-id='" + thread_id + "']");

        if( exist.length > 0 ){
            var message_content = message.content_site;
            exist.each(function(){
                var __thread = $(this);
                var _p = __thread.find('.info p');
                var __container = __thread.closest('.threads-list');
                __container.removeClass('empty');
                _p.html( message_content );

                if( typeof _p.attr('writing-reserved') !== 'undefined' ) {
                    _p.attr('writing-reserved', message_content )
                }

                __thread.attr('data-message', message.id);
                __thread.prependTo(__container);

                if( BP_Messages['realtime'] === '1' ){
                    var livestamp = __thread.find('.time-wrapper');
                    var timestamp = parseInt(message.timestamp);
                    livestamp.livestamp( new Date(timestamp * 1000) );
                } else if( BP_Messages['realtime'] !== '1' && typeof message.html !== 'undefined' ){
                    __thread.replaceWith( message.html );
                }
            });
        } else {

            $(".bp-messages-wrap .threads-list").each(function(){
                if( $(this).closest('.bpbm-search-results-section').length === 0 ) {
                    $(this).removeClass('empty');
                    $(html).prependTo($(this));
                }
            });
        }

        if (typeof BP_Messages.mutedThreads[thread_id] === 'undefined' && BP_Messages.user_id != message.user_id) {
            if( typeof message.count_unread !== 'undefined' ){
                if( message.count_unread !== '0' ) {
                    playSound(message.id);
                }
            } else {
                playSound( message.id );
            }
        }

        onlineInit();
    }

    /**
     * Properly placing new message on thread screen
     * @param message
     */
    function renderMessage(message, selector) {
        if( typeof BP_Messages['fast'] === 'string' ) {
            if (message.fast && BP_Messages['fast'] !== '1') {
                return false;
            }
        }

        var replaceTemp = false;
        var firstMessage = false;
        if(typeof selector === 'undefined') selector = '.bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list)';

        if( message.message.trim() == '' ) return false;
        var isEdit = (message['edit'] == '1') ? true : false;

        var avatar;
        if( typeof message.avatar !== 'string' ){
            avatar = '';
        } else {
            avatar = '<div class="pic">' + message.avatar.replace("loading='lazy'", '').replace('loading="lazy"', '') + '</div>';
        }

        if( BP_Messages['realtime'] == "1" && ! message.fast ) {
            if( isEdit ){
                replaceTemp = true;
                message.temp_id = message.id;
            } else if ( ! message.temp_id ) {
                //return false;
            } else {
                replaceTemp = true;
            }
        }

        var readableDate = moment(newTime).format('YYYY-MM-DD HH:mm:ss').toString();
        var stack = $(selector + ' .messages-stack:last-child');
        var container = stack.closest('.bp-messages-wrap');

        var tmpl = 'standard';

        if( container.hasClass('bpbm-template-modern') ){
            tmpl = 'modern';
        }

        if(stack.length === 0 && $(selector + ' .empty-thread').length > 0) {
            firstMessage = true;
        }
        var same_message = $(selector + ' .messages-list li[data-id="' + message.id + '"]');
        var className = '';
        var openInMobileView = $('body').hasClass('bp-messages-mobile') && parseInt(message['thread_id']) === parseInt(thread);
        if( ! openInMobileView && ! ifvisible.now('active') && BP_Messages['realtime'] == "1" && message.user_id !== BP_Messages['user_id'] ) className += ' unread';
        if(message.user_id == BP_Messages['user_id']) className += ' my';
        if( !! message.fast ) className += ' fast';
        if(message.user_id == BP_Messages['user_id'] && BP_Messages['realtime'] == "1") className += ' sent';
        className = className.trim();
        var findMessage = $(selector + ' .messages-list li[data-id="' + message.temp_id + '"]');
        if(isEdit && findMessage.length > 0) className = findMessage.attr('class');

        var messageHtml = '<li class="' + className + '" data-thread="' + message.thread_id + '" title="' + readableDate + '" data-time="' + message.timestamp + '" data-id="' + message.id + '">';

        var actionsHtml = '';
        var replyHtml = '';
        var favoriteHtml = '';
        var contentClass = 'message-content';


        if( BP_Messages['enableReplies'] === '1' ) {
            if( message.user_id != BP_Messages['user_id'] ) {
                replyHtml = '<span class="bpbm-reply" title="' + BP_Messages['strings']['reply'] + '"><i class="fas fa-reply"></i></span>';
            }
            contentClass += ' reply-enabled';
        }

        if(BP_Messages['messagesStatus'] === '1' && message.user_id == BP_Messages['user_id']) {
            actionsHtml += '<span class="status" title="' + BP_Messages['strings']['sent'] + '"></span>';
        }

        if( BP_Messages['disableFavoriteMessages'] !== '1' ) {
            favoriteHtml += '<span class="favorite"><i class="fas" aria-hidden="true"></i></span>';
        }

        if(tmpl === 'standard'){
            messageHtml += actionsHtml;
            messageHtml += favoriteHtml;
            messageHtml += replyHtml;
        }

        messageHtml += '<span class="' + contentClass + '">';

        if(tmpl === 'modern'){
            messageHtml += replyHtml;
            messageHtml += actionsHtml;
            messageHtml += favoriteHtml;
        }

        messageHtml += message.message;
        messageHtml += '</span></li>';

        if(replaceTemp){
            if(findMessage.length > 0){
                if(message.message !== findMessage.find('.message-content').html()){
                    findMessage.replaceWith(messageHtml);
                    $('.bp-messages-wrap .list .messages-stack .content .messages-list li[data-id="' + message.id + '"]').each(function(){
                        updateMessagesStatus( this );
                        initTooltips( BPBMJQ(this) );
                    });
                } else {
                    findMessage.attr('data-id', message.id);
                    findMessage.removeClass('fast');
                    updateMessagesStatus( findMessage );
                }

                initMobileSwipes();
                addIframeClasses();

                return true;
            }
        }

        if (same_message.length === 0 && (stack.length > 0 || firstMessage)) {
            if(firstMessage == true) $(selector + ' .empty-thread').remove();
            var newStack = true;
            var lastTime = new Date( stack.find('.messages-list > li:last-child').attr('data-time') * 1000 );
            var lastTimeString = lastTime.getFullYear() + '-' + lastTime.getMonth() + '-' + lastTime.getDate() + '-' + lastTime.getHours() + '-' + lastTime.getMinutes();
            var newTime = new Date( message.timestamp * 1000 );
            var newTimeString = newTime.getFullYear() + '-' + newTime.getMonth() + '-' + newTime.getDate() + '-' + newTime.getHours() + '-' + newTime.getMinutes();

            if (stack.attr('data-user-id') === message.user_id) newStack = false;
            if (lastTimeString !== newTimeString) newStack = true;

            if ( newStack === false ) {
                stack.find('.messages-list').append(messageHtml);
            } else {
                var stackClass = 'messages-stack';
                if(message.user_id == BP_Messages['user_id']) {
                    stackClass += ' outgoing';
                } else {
                    stackClass += ' incoming';
                }

                var link = '<a href="' + message.link + '">' + message.name + '</a>';
                if( message.link === false ){
                    link = '<span>' + message.name + '</span>';
                }

                var newStackHtml = '<div class="' + stackClass + '" data-user-id="' + message.user_id + '">';

                newStackHtml += avatar;
                newStackHtml += '<div class="content">' +
                    '<div class="info">' +
                    '<div class="name">' +
                    link +
                    '</div>' +
                    '<div class="time" title="' + readableDate + '" data-livestamp="' + message.timestamp + '"></div>' +
                    '</div>' +
                    '<ul class="messages-list">' +
                    '</ul>' +
                    '</div>' +
                    '</div>';

                $(selector + ' .list').append(newStackHtml);

                $(selector + ' .messages-stack:last-child .messages-list').append(messageHtml);
            }

            

            //$(selector + " .wp-audio-shortcode, " + selector + " .wp-video-shortcode").not(".mejs-container").filter(function(){return!$(this).parent().hasClass(".mejs-mediaelement")}).mediaelementplayer();

            if( typeof $.fn.BPBMmagnificPopup === 'function') {
                $('.bp-messages-wrap .list .messages-stack .content .messages-list li .images').BPBMmagnificPopup({
                    delegate: 'a',
                    type: 'image'
                });
            }

            if ( typeof BP_Messages.mutedThreads[message.thread_id] === 'undefined' && BP_Messages.user_id != message.user_id) {
                if( typeof message.count_unread !== 'undefined' ){
                    if( message.count_unread !== '0' ) {
                        playSound(message.id);
                    }
                } else {
                    playSound( message.id );
                }
            }
        }

        var createdMessage = $('.bp-messages-wrap .list .messages-stack .content .messages-list li[data-id="' + message.id + '"]');
        if( ! isMobile ) {
            createdMessage.find('.bpbm-gif').each(function () {
                var gif = $(this);
                gif.find('.bpbm-gif-play').remove();
                var video = gif.find('video');
                video[0].play();
            });
        }

        $('.bpbm-gifs-icon').html(icons.gif);

        updateMessagesStatus( createdMessage );

        initImagesPopup();
        onlineInit();
        initMobileSwipes();
        addIframeClasses();

        initTooltips( createdMessage );

        document.dispatchEvent(new Event('bp-better-messages-message-render-end'));

    }

    /**
     * Show message notification popup
     *
     * @param thread_id
     * @param message
     * @param name
     * @param avatar
     */
    function showMessage(thread_id, message, name, avatar) {
        if (BP_Messages['disableOnSiteNotification'] === '1') return;

        if (typeof openThreads[thread_id] !== 'undefined') return;
        if( typeof BP_Messages.mutedThreads[thread_id] !== 'undefined' ) return;

        if( $('body').hasClass('bp-messages-mobile') ) return;

        var findVisibleThread = $(threadsSelector + '[data-id="' + thread_id + '"]:visible');
        if(findVisibleThread.length > 0 ) return false;

        var findSrc = avatar.match(/src\="([^\s]*)"\s/);
        var findSrc2 = avatar.match(/src\='([^\s]*)'\s/);

        if (findSrc != null) {
            avatar = findSrc[1];
        }

        if (findSrc2 != null) {
            avatar = findSrc2[1];
        }

        var popupExist = $('.amaran.thread_' + thread_id);
        if(popupExist.length > 0){
            popupExist.find('.icon > img').attr('src', avatar);
            var msg  = '<b>'+ name +'</b>';
            msg += message;
            popupExist.find('.info').html(msg);
            return true;
        }

        if(BP_Messages['miniMessages'] == '1' && miniMessages === 'messages'){
            // add animation here later
        } else {
            $.amaran({
                'theme': 'user message thread_' + thread_id,
                'content': {
                    img: avatar,
                    user: name,
                    message: message
                },
                'sticky': true,
                'closeOnClick': false,
                'closeButton': true,
                'delay': 10000,
                'thread_id': thread_id,
                'position': 'bottom right',
                onClick: function () {
                    
                        if( isMobile ){
                            var mobilePopup = $('#bp-better-messages-mini-mobile-open');
                            if( mobilePopup.length > 0 ){
                                var originalUrl = BP_Messages['url'];
                                BP_Messages['url'] = BP_Messages['threadUrl'] + this.thread_id;
                                mobilePopup.click();
                                BP_Messages['url'] = originalUrl;
                                $('.amaran.user.message.thread_' + thread_id).remove();
                            } else {
                                location.href = BP_Messages.threadUrl + this.thread_id;
                            }
                        } else {
                            var url = location.href = BP_Messages.threadUrl + this.thread_id + '&acceptCall';
                            var mainContainer = $('.bp-messages-wrap.bp-messages-wrap-main');
                            if (mainContainer.length > 0) {
                                ajaxRefresh(url, mainContainer);
                            } else {
                                location.href = url;
                            }
                        }
                    
                }
            });


            $('.bpbm-gifs-icon').html(icons.gif);
        }

        if( typeof message.count_unread !== 'undefined' ){
            if( message.count_unread !== '0' ) {
                playSound(message.id);
            }
        } else {
            playSound( message.id );
        }
    }

    function hidePossibleBreakingElements(){
        var fixed_elements = $('*:not(.bp-messages-hide-on-mobile)').filter(function () {
            return ( $(this).css('position') === 'fixed' || $(this).css('position') === 'absolute' )
                && ! $(this).hasClass('bp-messages-wrap')
                && $(this).closest('.uppy').length === 0
                && $(this).closest('.bp-messages-wrap').length === 0;
        });

        fixed_elements.addClass('bp-messages-hide-on-mobile');
    }

    function openMobileFullScreen( url ) {
        var _miniMobileContainer = $('#bp-better-messages-mini-mobile-container');
        _miniMobileContainer.addClass('bp-messages-mobile');
        _miniMobileContainer.find('.bp-messages-mobile-tap').remove();

        var windowHeight = window.innerHeight;
        $('html').addClass('bp-messages-mobile').css('overflow', 'hidden');
        $('body').addClass('bp-messages-mobile').css('min-height', windowHeight);
        bpMessagesWrap.addClass('bp-messages-mobile').css('min-height', windowHeight);

        var usedHeight = 0;
        usedHeight = usedHeight + bpMessagesWrap.find('.chat-header').outerHeight();
        usedHeight = usedHeight + bpMessagesWrap.find('.reply').outerHeight();

        var resultHeight = windowHeight - usedHeight;

        $('.scroller').css({
            'max-height': '',
            'height': resultHeight
        });

        hidePossibleBreakingElements();

        _miniMobileContainer.show().html(
            '<div class="loading-messages" style="display: block;line-height: ' + resultHeight + 'px">\n' +
            '<div class="bounce1"></div>\n' +
            '<div class="bounce2"></div>\n' +
            '<div class="bounce3"></div>\n' +
            '</div>'
        );

        var target_url = '?action=bp_messages_load_via_ajax&mobileFullScreen=1';
        if( typeof url.split('?')[1] !== 'undefined' ){
            target_url = '?' + url.split('?')[1] + '&action=bp_messages_load_via_ajax&mobileFullScreen=1';
        }

        var ajax_url = BP_Messages['ajaxUrl'] + target_url;

        $.get(ajax_url, function (json) {
            if( typeof json['total_unread'] !== 'undefined'){
                BPBMUpdateUnreadCount(json['total_unread']);
            }
            var html = json['html'];
            var newWrapper = $(html).filter('.bp-messages-wrap').html();
            _miniMobileContainer.html(newWrapper).show();

            blockScroll = true;

            reInit();

            $('#bp-better-messages-mini-mobile-open').removeClass('loading');
        }, 'json');
    }

    $(document).on("submit", '.side-header > .bpbm-search form', function(event){
        event.preventDefault();
        event.stopImmediatePropagation();
    });

    var loadingSearch = false;

    $(document).on("change keyup", '.side-header > .bpbm-search input[name="search"]', function (event) {
        var input     = $(this);
        var search    = input.val().trim();

        var form = input.closest('form');
        var close = form.find('.close');
        var column = input.closest('.bp-messages-side-threads');
        var results = column.find('.bpbm-search-results');
        var threads = column.find('.threads-list');

        var loadingSearchTerm = input.data('search-term');

        if( loadingSearchTerm !== search ) {
            if( search === '' ) {
                threads.show();
                results.hide();
                close.hide();
                input.data('search-term', search);
            } else {
                results.show();
                close.show();
                threads.hide();

                if (loadingSearch) {
                    loadingSearch.abort();
                }

                if( results.find('.loading-messages').length === 0 ) {
                    results.html(loadingHtml);
                }

                input.data('search-term', search);

                loadingSearch = $.post(BP_Messages.ajaxUrl, {
                    action: 'bp_messages_thread_search',
                    search: search
                }, function (response) {
                    results.html(response);
                    loadingSearch = false;
                    onlineInit();
                    $('.bpbm-gifs-icon').html(icons.gif);
                });
            }
        }
    });

    $(document).on("change keyup", '.bpbm-search-in-list > input', function (event) {
        var input     = $(this);
        var search    = input.val().trim().toLowerCase();
        var searchDiv = input.parent();
        var searchIn  = searchDiv.next();

        if( searchIn.hasClass('threads-list') ) {

        } else {
            searchIn.find('> div').each(function () {
                var item = $(this);
                var name = item.find('.name').text().toLowerCase();

                if ( name.indexOf(search) === -1 ) {
                    item.hide();
                } else {
                    item.show();
                }
            });
        }
    });

    $(document).on('click', '.bpbm-send-files.bpbm-has-files', function(event){
       var button = $(this);
       var uppy = button.closest('.uppy-Dashboard');
       var thread_id = parseInt(uppy.attr('data-thread-id'));

       var uploadButton = $('#bpbm-upload-btn-' + thread_id);
       uppy.find('.uppy-Dashboard-close').click();
       var form = uploadButton.closest('form');
       form.submit();
    });

    

    function updateWritingPosition() {
        var writingSpans = $('span.writing');
        writingSpans.each(function() {
            var writingSpan = $(this);
            var wrap = writingSpan.closest('.bp-messages-threads-wrapper');
            var height = wrap.find('.reply').outerHeight();
            var previewHeight = wrap.find('.bpbm-preview-message').outerHeight();

            if (typeof previewHeight !== 'undefined') {
                height += previewHeight;
            }

            writingSpan.css('bottom', height);
        });
    }

    function updateUnreadCounters(thread_id, unread){
        var unreadText;

        if(unread < 1) {
            unreadText = '';
            $('.threads-list .thread[data-id="' + thread_id + '"], .bp-better-messages-mini .chat[data-thread="' + thread_id + '"]').removeClass('unread');
            $('.bp-messages-wrap .messages-list li[data-thread="' + thread_id + '"]').removeClass('unread');
        } else {
            unreadText = '+' + unread;
            $('.threads-list .thread[data-id="' + thread_id + '"], .bp-better-messages-mini .chat[data-thread="' + thread_id + '"]').addClass('unread');
        }
        $('.threads-list .thread[data-id="' + thread_id + '"] .time .unread-count').text(unreadText);
        $('.bp-better-messages-mini .chat[data-thread="' + thread_id + '"] .unread-count').attr('class', 'unread-count count-' + unread).text(unread);
    }

    /**
     * Playing notification sound!
     */
    function playSound( message_id ) {
        if( BP_Messages['enableSound'] !== '1' || BP_Messages['userStatus'] === 'dnd' ) return false;
        if( typeof message_id === 'string' && message_id.substr(0, 4) !== 'tmp_' ){
            if( typeof sounds.notification !== 'undefined' ) {
                sounds.notification.play();
            }
        }
    }

    function createCookie(name, value, days) {
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires=" + date.toUTCString();
        }
        else var expires = "";
        document.cookie = name + "=" + value + expires + "; path=/";
    }

    function readCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }


    function getAutocompleteCookies() {
        var allCookies = document.cookie.split(';'),  // get all cookies and split into an array
            bpCookies = {},
            cookiePrefix = 'bp-',
            i, cookie, delimiter, name, value;

        // loop through cookies
        for (i = 0; i < allCookies.length; i++) {
            cookie = allCookies[i];
            delimiter = cookie.indexOf('=');
            name = BPBMJQ.trim(unescape(cookie.slice(0, delimiter)));
            value = unescape(cookie.slice(delimiter + 1));

            // if BP cookie, store it
            if (name.indexOf(cookiePrefix) === 0) {
                bpCookies[name] = value;
            }
        }

        // returns BP cookies as querystring
        return encodeURIComponent(BPBMJQ.param(bpCookies));
    }

    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
        var regexS = "[\\?&]" + name + "=([^&#]*)";
        var regex = new RegExp(regexS);
        var results = regex.exec(window.location.search);
        if (results == null)
            return "";
        else
            return decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    function msToTime(duration) {
        var milliseconds = parseInt((duration % 1000) / 100),
            seconds = Math.floor((duration / 1000) % 60),
            minutes = Math.floor((duration / (1000 * 60)) % 60),
            hours = Math.floor((duration / (1000 * 60 * 60)) % 24);

        hours = (hours < 10) ? "0" + hours : hours;
        minutes = (minutes < 10) ? "0" + minutes : minutes;
        seconds = (seconds < 10) ? "0" + seconds : seconds;

        if( hours !== '00' ) {
            return hours + ":" + minutes + ":" + seconds;
        } else {
            return minutes + ":" + seconds;
        }
    }
    

    function calculateTitle(wraps){
        wraps.each(function(){
            var wrap = BPBMJQ(this);

            var head       = wrap.find('.chat-header:not(.side-header)');
            var title      = head.find('> strong');
            var user       = head.find('> .user');

            var iconsWidth = 0;
            head.find('> a:visible:not(.user),> div:visible').each(function () {
                iconsWidth = iconsWidth + $(this).width();
            });

            var resultWidth = iconsWidth + 10;

            if( title.length > 0 ) {
                title.css( 'width', 'calc(100% - ' + resultWidth + 'px)' )
            }

            if( user.length > 0 ){
                user.css( 'max-width', 'calc(100% - ' + resultWidth + 'px)' );
            }

            wrap.find('.chats .chat').each(function(){
                var chat  = BPBMJQ(this);
                var head  = chat.find('> .head');
                var title = head.find('> .title');

                var iconsWidth = 0;

                head.find('> div.controls').each(function () {
                    iconsWidth = iconsWidth + $(this).width();
                });

                var resultWidth = iconsWidth + 40;


                if( title.length > 0 ) {
                    title.css( 'max-width', 'calc(100% - ' + resultWidth + 'px)' )
                }

            });
        })

    }

    function showPreloader(container){
        var headerHeight = container.find('.chat-header').height();
        var preloader    = container.find('.preloader');
        preloader.show();
        if( ! preloader.parent().hasClass('bp-messages-column') ){
            preloader.css('top', headerHeight);
            preloader.css('height', 'calc(100% - ' + headerHeight + 'px)')
        }
    }
})(BPBMJQ);


function BPBMurlBase64ToUint8Array( base64String ) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
}

/**
 * Show notice popup
 */
function BBPMNotice(notice) {
    BPBMJQ.amaran({
        'theme': 'colorful',
        'content': {
            bgcolor: 'black',
            color: '#fff',
            message: notice
        },
        'sticky': false,
        'closeOnClick': true,
        'closeButton': true,
        'delay': 10000,
        'position': 'bottom right'
    });
}

/**
 * Show error popup
 */
function BBPMShowError(error) {
    BPBMJQ.amaran({
        'theme': 'colorful',
        'content': {
            bgcolor: '#c0392b',
            color: '#fff',
            message: error
        },
        'sticky': false,
        'closeOnClick': true,
        'closeButton': true,
        'delay': 10000,
        'position': 'bottom right'
    });
}

function BBPMOpenMiniChat(thread_id, open) {
    BPBMJQ(document).trigger("bp-better-messages-open-mini-chat", [thread_id, open]);
}

function BBPMOpenPrivateThread(user_id) {
    BPBMJQ(document).trigger("bp-better-messages-open-private-thread", [user_id]);
}

function BPBMGetOnlineUsers(){
    return BPBMOnlineUsers;
}