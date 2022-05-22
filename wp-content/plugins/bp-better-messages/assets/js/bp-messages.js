/* global BP_Messages */
var BPBMJQ = jQuery.noConflict();
var BPBMEvent = Event;
BPBMJQ.fn.bpbmtooltip = jQuery.fn.tooltip;
var BPBMOnlineUsers = [];
var urlSeparator = (BP_Messages['url'].split('?')[1] ? '&':'?');

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
        isInCall = false,
        blockSelect = false,
        originalTitle = document.title,
        loadingHtml = '<div class="loading-messages"><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>',
        badWords    = false;


    if( typeof( BP_Messages['badWordList'] ) !== 'undefined' ){
        try {
            badWords = JSON.parse(atob(BP_Messages['badWordList']));
        } catch (e){}
    }

    var incomingSelector = '.bp-messages-wrap .list .messages-stack.incoming .content .messages-list li .message-content';
    var outgoingSelector = '.bp-messages-wrap .list .messages-stack.outgoing .content .messages-list li .message-content';
    var threadsSelector  = '.bp-messages-wrap .threads-list .thread';

    function BMStoreSet(key, value){
        try{
            store.set(key, value);
        } catch (e) { console.debug("Not possible to write to LocalStorage") }
    }


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

        if( $(textarea).next('.bp-emojionearea').length === 0 && $(textarea).prev('.bm-mobile-area').length === 0 ) return message;
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

        message = message.replace(/<div>/g, '<p>');
        message = message.replace(/<\/div>/g, '</p>');

        var message_html = $.parseHTML( message );

        $.each( message_html, function( i, el ) {
            var element = $(this);

            $.each(element.find('img.emojioneemoji,img.emojione'), function () {
                var emojiicon = $(this);
                emojiicon.replaceWith(emojiicon.attr('alt'));
            });


            element.BPBMremoveAttributes();
            element.find('*:not(.bm-medium-editor-mention-at)').BPBMremoveAttributes();
        });

        var new_html = '';
        $.each( message_html, function(){
            new_html += this.outerHTML;
        } );

        if(new_html === '<p></p>') new_html = '';

        new_html = new_html.replace(/&amp;/g, '&');
        new_html = new_html.replace(/<font>/g, '');
        new_html = new_html.replace(/<\/font>/g, '');

        return new_html;
    }

    var tabID = sessionStorage.tabID &&
    sessionStorage.closedLastTab !== '2' ?
        sessionStorage.tabID :
        sessionStorage.tabID = Math.random();
    sessionStorage.closedLastTab = '2';

    var activeTabs = store.get( 'bp-better-messages-active-tabs' ) || {};

    updateTabStatus();

    setInterval(function(){
        updateTabStatus();
    }, 2500);

    function updateTabStatus(){
        try {
            var currentTime = Date.now();

            var isActive = !document.hidden && document.hasFocus();
            activeTabs = store.get('bp-better-messages-active-tabs') || {};

            if (!isActive) {
                if (typeof activeTabs[tabID] !== 'undefined') {
                    delete activeTabs[tabID];
                    BMStoreSet('bp-better-messages-active-tabs', activeTabs);
                }
            } else {
                activeTabs[tabID] = currentTime;
                BMStoreSet('bp-better-messages-active-tabs', activeTabs);
            }
        } catch (e) { console.debug("Error while trying to use local storage.") }
    }
    
    $(window).on('unload beforeunload', function() {
        sessionStorage.closedLastTab = '1';
        activeTabs = store.get( 'bp-better-messages-active-tabs' ) || {};
        if( typeof activeTabs[tabID] !== 'undefined' ) {
            delete activeTabs[tabID];
            BMStoreSet('bp-better-messages-active-tabs', activeTabs);
        }
    });

    $(window).on('focus',function() {
        ifvisible.focus();
        setTimeout(updateTabStatus, 100);
    });

    $(window).on('blur', function() {
        ifvisible.blur();
        setTimeout(updateTabStatus, 100);
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

        BP_Messages['miniChats'] = '0';
    }

    var holderClass = 'bp-better-messages-mobile-holder';
    var fullScreenHolderClass = 'bp-better-messages-mobile-holder';

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
        var chatHeaderHeight = _mobileViewContainer.find('.chat-header').outerHeight();
        usedHeight = usedHeight + chatHeaderHeight;
        if( _mobileViewContainer.find('.chat-footer:visible').length > 0 ) {
            usedHeight = usedHeight + _mobileViewContainer.find('.chat-footer:visible').outerHeight();
        }

        if( _mobileViewContainer.find('.reply').length > 0 ) {
            usedHeight = usedHeight + _mobileViewContainer.find('.reply').outerHeight();
        }

        bpMessagesWrap.find('.bpbm-chat-content').css('max-height', 'calc( 100% - ' + chatHeaderHeight + 'px )');

        var resultHeight = windowHeight - usedHeight;

        _mobileViewContainer.find('.scroller').css({
            'max-height': '',
            'height': resultHeight
        });

        calculateTitle(_mobileViewContainer);
        scrollBottom();

        hidePossibleBreakingElements();

        document.dispatchEvent(new CustomEvent('bp-better-messages-mobile-open'));

        blockScroll = true;
    }

    if (store.enabled) {
        openThreads = store.get('bp-better-messages-open-threads') || {};
        miniChats = store.get('bp-better-messages-mini-chats') || {};
        miniMessages = store.get('bp-better-messages-mini-messages') || false;
        setInterval(updateOpenThreads, 1000);
    }

    

    $(document).ready(function (){
        initialInit();
    });

    function initialInit() {
        isRtl = $('html[dir="rtl"]').length !== 0;
        bpMessagesWrap          = $(".bp-messages-wrap:not(.bp-better-messages-list, .bm-threads-list, #bp-better-messages-mini-mobile-open,.bp-better-messages-secondary)");
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

            var mainContainer = $('.bp-messages-wrap.bp-messages-wrap-main');

            if( mainContainer.length > 0 ){
                openMobile(mainContainer);
            } else if( ! mobileOpenButton.hasClass('loading') ) {
                mobileOpenButton.addClass('loading');

                openMobileFullScreen( BP_Messages['baseUrl'] );
            }
        });

        var __thread = false;
        var __threads = false

        if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary) .thread.scroller.bm-infodiv[data-id]").length > 0) {
            __thread = $(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary) .thread.scroller.bm-infodiv").attr('data-id');
        } else if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary,#bp-better-messages-mini-mobile-container,.bm-threads-list) .threads-list").length > 0) {
            __threads = true;
        }

        if( isMobile ) {
            if( BP_Messages['autoFullScreen'] === '1' ) {
                if (__thread || __threads) {
                    //$('#bp-better-messages-mini-mobile-open, #bp-better-messages-mini-mobile-container').remove();
                    var wrap = $('.bp-messages-wrap.mobile-ready.bp-messages-wrap-main, .bp-messages-wrap.mobile-ready.bp-messages-group-thread');
                    openMobile(wrap);
                } else {
                    var wrap = $('.bp-messages-wrap.bp-messages-wrap-bulk.mobile-ready');
                    if (wrap.length > 0) {
                        openMobile(wrap);
                    }
                }

                var times = 0;
                bbAppFix();
                function bbAppFix(){
                    times++;
                    $('.bp-messages-wrap.bbapp_hide_all, .bp-messages-wrap.bbapp_hide_all *').removeClass('bbapp_hide_all');
                    if(times < 10){
                        setTimeout(function(){
                            bbAppFix();
                        }, 500);
                    }
                }
            } else {
                if (__thread || __threads) {
                    //$('#bp-better-messages-mini-mobile-container').remove();
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
                'type' : type,
                'nonce': BP_Messages['userNonce']
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
            textarea.trigger('change');

            var message = BPBMformatTextArea(textarea);
            var form = $(this).serialize();

            $(this).find('textarea, .bp-emojionearea-editor').html('').val('');
            $(this).find('.bm-mobile-area').html('').addClass('bpbm-medium-editor-placeholder');
            textarea.trigger('change');
            //sendingMessage = true;
            var _thread = _form.find('input[name="thread_id"]').val();

            var isEdit     =  false;
            var isReply    =  false;

            var container = $(this).parent().parent();
            var threadDiv = container.find('.thread.scroller.bm-infodiv');
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

            var maybeMini = $(this).closest('.bp-better-messages-mini').length > 0;
            if(maybeMini) _thread = $(this).closest('.chat').attr('data-thread');

            var sendFast = BP_Messages['realtime'] === "1";

            if( badWords !== false ) {
                if (badWords.some(function(v) { return message.indexOf(v) >= 0; })) {
                    sendFast = false;
                }
            }

            var message = JSON.stringify({
                message: message,
                me: BP_Messages['me']
            });

            if( BP_Messages['encryption'] === '1' ){
                var secret_key = threadDiv.data('secret');
                message = BPBMAES256.encrypt(message, secret_key);
            }

            if( _thread && sendFast && ! isEdit && ! isReply ) {
                socket.emit( 'fast_message', _thread, message, '', function(response){
                    lastForm = form;

                    clearInterval(lastFormTimeout);
                    lastFormTimeout = setInterval(function(){ lastForm = ''; }, 3000);

                    document.dispatchEvent(new CustomEvent('bp-better-messages-message-sent'));

                    if( typeof sounds.sent !== 'undefined' ) {
                        sounds.sent.play();
                    }

                    scrollBottom('.bp-messages-wrap[data-thread-id="' +  _thread + '"]');
                    scrollBottom('.bp-better-messages-mini .chats .chat[data-thread="' +  _thread + '"]');

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
                        document.dispatchEvent(new CustomEvent('bp-better-messages-message-sent'));

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

            document.dispatchEvent(new CustomEvent('bp-better-messages-message-sent-end'));
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
            var isMini = container.hasClass('bp-better-messages-mini');

            if( isMini ) {
                showPreloader(BPBMJQ(event.target).closest('.chat'));
            } else {
                showPreloader(container);
            }

            var textarea = $(this).find('textarea[name="message"]');
            textarea.trigger('change');

            BPBMformatTextArea(textarea);

            var form = $(this);
            var data = form.serialize();

            document.dispatchEvent(new CustomEvent('bp-better-messages-new-thread-start'));

            $.post(BP_Messages.ajaxUrl, data, function (data) {
                if (data.result) {
                    document.dispatchEvent(new CustomEvent('bp-better-messages-new-thread-created'));

                    if( isMini && BP_Messages['miniChats'] === '1' ){
                        form.closest('.chat').remove();
                        openMiniChat( data['result'], true );
                    } else {
                        ajaxRefresh(BP_Messages.threadUrl + data['result'], container);
                    }
                } else {
                    document.dispatchEvent(new CustomEvent('bp-better-messages-new-thread-error'));
                    form.closest('.bp-messages-wrap').find('.preloader').hide();

                    $.each(data['errors'], function(){
                        BBPMShowError(this);
                    });
                }

                document.dispatchEvent(new CustomEvent('bp-better-messages-new-thread-end'));
            }).fail(function() {
                form.closest('.bp-messages-wrap').find('.preloader').hide();
                if( isMini && BP_Messages['miniChats'] === '1' ) {
                    form.closest('.chat').find('.preloader').hide();
                }

                document.dispatchEvent(new CustomEvent('bp-better-messages-new-thread-end'));
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
            ajaxRefresh(BP_Messages['url'] + urlSeparator + $(this).serialize(), container );
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
                    $(this).trigger('focus');
                }
            });

            bpMessagesWrap.on('keyup', '.reply .bp-emojionearea-editor', function (event) {
                if ( ! event.shiftKey && event.keyCode == 13 ) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
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
                    thread_id: thread_id,
                    nonce: BP_Messages['userNonce']
                }, function(response){
                    if(response.result === true){
                        var url = BP_Messages['url'] + urlSeparator + $.param({thread_id: thread_id, participants: "1"});
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
                    'thread_id'    : thread_id,
                    nonce          : BP_Messages['userNonce']
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
         * Block user
         */
        bpMessagesWrap.on('click', '.bpbm-block-user', function (event) {
            event.preventDefault();

            var container = $(this).closest('.bp-messages-wrap');
            var isMini = $(this).closest('.bp-better-messages-mini').length > 0;
            var url = location.href;
            var thread_id;
            var user_id = parseInt($(this).attr('data-user-id'));

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

            var confirmBlock = confirm(BP_Messages['strings']['user_block']);

            if( confirmBlock ){

                if( isMini ) {
                    showPreloader($(this).closest('.chat'));
                } else {
                    showPreloader(container);
                }


                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'       : 'bp_messages_block_user',
                    'user_id'      : user_id,
                    nonce          : BP_Messages['userNonce']
                }, function (response) {
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
         * Unblock user
         */
        bpMessagesWrap.on('click', '.bpbm-unblock-user', function (event) {
            event.preventDefault();

            var container = $(this).closest('.bp-messages-wrap');
            var isMini = $(this).closest('.bp-better-messages-mini').length > 0;
            var url = location.href;
            var thread_id;
            var user_id = parseInt($(this).attr('data-user-id'));

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

            var confirmBlock = confirm(BP_Messages['strings']['user_unblock']);

            if( confirmBlock ){

                if( isMini ) {
                    showPreloader($(this).closest('.chat'));
                } else {
                    showPreloader(container);
                }


                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'       : 'bp_messages_unblock_user',
                    'user_id'      : user_id,
                    nonce          : BP_Messages['userNonce']
                }, function (response) {
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
                    'thread_id'    : thread_id,
                    nonce: BP_Messages['userNonce']
                }, function (response) {
                    if( response ){
                        if(typeof socket !== 'undefined') {
                            threadOpenEvent(thread_id);
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

            if(url.indexOf('?thread_id=') !== -1) {
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
                    '_wpnonce'  : BP_Messages['userNonce']
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
                    '_wpnonce': BP_Messages['userNonce']
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
            var reply        = container.find('.reply');
            var participants = container.find('.participants-panel');

            if( ! addUserPanel.hasClass('open') ){
                addUserPanel.addClass('open');
                participants.removeClass('open');
                threadScroll.hide();
                reply.hide();
            }
        });

        bpMessagesWrap.on('click', '.chat-header .participants', function (event) {
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');

            var participants = container.find('.participants-panel');
            var threadScroll = container.find('.scroller.thread');
            var addUserPanel = container.find('.add-user-panel');
            var reply        = container.find('.reply');
            var thread_id    = parseInt(container.attr('data-thread-id'));

            var usersList    = participants.find( '.bp-messages-user-list' );
            if(isNaN(thread_id)){
                thread_id = parseInt(container.attr('data-thread'));
            }

            if( ! participants.hasClass('participants-loaded') ){
                participants.addClass('participants-loaded');

                $.post( BP_Messages[ 'ajaxUrl' ], {
                    'action'       : 'bp_messages_load_thread_participants',
                    'thread_id'    : thread_id
                }, function (response) {
                    usersList.html(response.html);

                    if( response.loadMore ){
                        $( response.loadMore ).insertAfter( usersList );
                    }
                }).always(function() {});
            }

            if( ! participants.hasClass('open') ){
                participants.addClass('open');
                addUserPanel.removeClass('open');
                threadScroll.hide();
                reply.hide();

                participants.BPBMoverlayScrollbars({
                    'sizeAutoCapable': false,
                    overflowBehavior: {
                        x: 'hidden'
                    },
                });
            } else {
                participants.removeClass('open');
                threadScroll.show();
                reply.show();
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
                    'thread_id'    : thread_id,
                    nonce          : BP_Messages['userNonce']
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
                thread_id: thread_id,
                nonce: BP_Messages['userNonce']
            }, function(response){
                var url = BP_Messages['url'] + urlSeparator + $.param( { thread_id: thread_id, participants: "1" });
                ajaxRefresh(url, container);
            });
        });

        bpMessagesWrap.on('click', '.add-user button.bpbm-close', function (event){
            event.preventDefault();
            var container = $(this).closest('.bp-messages-wrap');

            var addUserPanel = container.find('.add-user-panel');
            var threadScroll = container.find('.scroller.thread');
            var reply        = container.find('.reply');

            if( addUserPanel.hasClass('open') ){
                addUserPanel.removeClass('open');
                threadScroll.show();
                reply.show();
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
                chat_id: chat_id,
                nonce: BP_Messages['userNonce']
            }, function(response){
                var url = BP_Messages['url'] + urlSeparator + $.param( { thread_id: thread_id });
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
                chat_id: chat_id,
                nonce: BP_Messages['userNonce']
            }, function(response){
                var url = BP_Messages['url'] + urlSeparator + $.param( { thread_id: thread_id });
                ajaxRefresh(url, container);
            });
        });

        bpMessagesWrap.on('click', '.participants-panel .bm-load-more-participants', function (event) {
            event.preventDefault();

            var button    = $(this);
            var container = button.closest('.bp-messages-wrap');
            var page      = button.data('page');
            var thread_id = button.data('thread-id');
            var buttonContainer = button.closest('.bm-load-more-participants-div');

            button.replaceWith('<i class="fas fa-spinner fa-spin"></i>');

            var usersList = container.find('.bp-messages-user-list');
            $.post( BP_Messages[ 'ajaxUrl' ], {
                'action'       : 'bp_messages_load_thread_participants',
                'thread_id'    : thread_id,
                'page'         : page
            }, function ( response ) {
                usersList.append( response.html );

                if( response.loadMore ){
                    buttonContainer.replaceWith( response.loadMore );
                } else {
                    buttonContainer.remove();
                }
            }).always(function() {});

        });

        

        


        $(document).on('click', '.bm-threads-list .threads-list .thread:not(.blocked)', function (event) {
            event.preventDefault();

            var thread = $(this);
            var thread_id = thread.data('id');
            if (BP_Messages['miniChats'] == '1') {
                var scroller = thread.parent().parent();
                var height = $(thread).height();
                var top = thread.position().top;
                top = top + scroller.scrollTop();

                thread.addClass('blocked loading');

                $(thread).find('.loading').css({
                    'height': height,
                    'line-height': height + 'px',
                    'top': top + 'px'
                });

                openMiniChat(thread_id, true).always(function (done) {
                    reInit();
                    thread.removeClass('blocked loading');
                });
            } else {
                var url = BP_Messages['threadUrl'] + thread_id + '&scrollToContainer';

                if( isMobile ){
                    var mobilePopup = $('#bp-better-messages-mini-mobile-open');
                    if( mobilePopup.length > 0 ){
                        var originalUrl = BP_Messages['baseUrl'];
                        BP_Messages['baseUrl'] = BP_Messages['threadUrl'] + thread_id;
                        mobilePopup.click();
                        BP_Messages['baseUrl'] = originalUrl;
                    } else {
                        location.href = url;
                    }
                } else {
                    var mainContainer = $('.bp-messages-wrap.bp-messages-wrap-main');

                    if (mainContainer.length > 0) {
                        ajaxRefresh(url, mainContainer);
                    } else {
                        location.href = url;
                    }
                }
            }
        });

        $(document).on('click', '.bp-messages-wrap .chat-tabs > div', function (event) {
            event.preventDefault();
            var tab  = $(this);
            var tabs = tab.closest('.chat-tabs');
            var container = tabs.parent();
            var currentTab = tab.data('tab');

            if (! $(this).hasClass('active') ) {
                tabs.find('> div').removeClass('active');
                tab.addClass('active');

                tabs.find('> div').each(function(){
                    var tabData = $(this).data('tab');
                    container.find('.' + tabData).hide();
                });

                var newTabContent = container.find('.' + currentTab);

                chatAdditionalTab( newTabContent );
            }
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

            initMiniTab(miniMessages);

            BMStoreSet('bp-better-messages-mini-messages', miniMessages);
        });

        $(document).on('click', '.bpbm-deleted-user-link', function (event) {
            event.preventDefault();
        });

        $(document).on('click', '.bp-better-messages-list .new-message', function(event) {
            var mainContainer = $('.bp-messages-wrap.bp-messages-wrap-main');
            var miniChats = $('.bp-messages-wrap.bp-better-messages-mini');
            if( mainContainer.length > 0 ){
                event.preventDefault();
                ajaxRefresh($(this).attr('href'), mainContainer);
            } else if( miniChats.length > 0 ){
                event.preventDefault();
                var target_url = '?action=bp_messages_load_via_ajax&new-message&mini=1';
                var ajax_url   = BP_Messages['ajaxUrl'] + target_url;

                $.get(ajax_url, function (json) {
                    if( typeof json['total_unread'] !== 'undefined'){
                        BPBMUpdateUnreadCount(json['total_unread']);
                    }

                    if( typeof json['errors'] !== 'undefined'){
                        $.each(json['errors'], function(){
                            BBPMShowError(this);
                        });
                    }

                    var html = json['html'];
                    var chat = $(html);

                    var extraControls = '';

                    chat.find('.chat-header').remove();

                    var titleHead = chat.find('#bm-new-thread-title');
                    var title = titleHead.text();
                    titleHead.remove();

                    var chatClass = 'chat open';
                    var visibility = '';

                    if(BP_Messages['disableEnterForDesktop'] === '1' ){
                        chatClass += ' enter-disabled'
                    }

                    var content = '<div class="' + chatClass + '" data-thread="0" style="' + visibility + '">';
                    content += '<div class="head"><span class="unread-count count-0"></span><span class="title"><strong>' + title + '</strong></span>';
                    content += '<div class="controls">\n' +
                        extraControls +
                        '<span class="open" title="' + BP_Messages['strings']['maximize'] + '"><i class="fas fa-window-maximize" aria-hidden="true"></i></span>\n' +
                        '<span class="close" title="' + BP_Messages['strings']['close'] + '"><i class="fas fa-times" aria-hidden="true"></i></span>\n' +
                        '</div>' +
                        '</div>';
                    content += chat.html();
                    content += '</div>';

                    var chatExist = $('.bp-better-messages-mini .chats .chat[data-thread="0"]');
                    if( chatExist.length > 0 ){
                        chatExist.replaceWith(content);
                    } else {
                        $(content).appendTo('.bp-messages-wrap.bp-better-messages-mini .chats');
                    }

                    reInit();
                }, 'json');
            }
        });

        $(document).on('click', '.bp-messages-group-list .group:not(.blocked)', function(event){
            if($(event.target).is('div')){
                event.preventDefault();
                var group     = $(this);

                if( group.hasClass('bpbm-messages-disabled') ) {
                    location.href = group.find('.open-group').attr('href');
                } else {
                    var insideColumnView = group.closest('.bp-messages-column').length > 0 || group.closest('.bp-messages-side-threads').length > 0;
                    var group_id = group.data('group-id');
                    var thread_id = group.data('thread-id');
                    var url = group.find('.actions .open-group').attr('href');
                    if (BP_Messages['enableGroups'] === '1') url += BP_Messages['groupsSlug'] + '/?scrollToContainer';

                    var dataUrl = group.attr('data-url');
                    if( typeof dataUrl !== 'undefined' ){
                        url = dataUrl;
                    }

                    var scroller = group.parent().parent();
                    var height = group.height();
                    var top = group.position().top;
                    top = top + scroller.scrollTop();
                    group.find('.loading').css({
                        'height': height,
                        'line-height': height + 'px',
                        'top': top + 'px'
                    });

                    if( insideColumnView ){
                        var mainContainer = group.closest('.bp-messages-wrap');
                        if( mainContainer.length > 0 ) {
                            ajaxRefresh(BP_Messages['threadUrl'] + thread_id, mainContainer);
                        }
                    } else if (BP_Messages['enableGroups'] === '1' && BP_Messages['miniChats'] == '1' && !!thread_id) {
                        group.addClass('blocked loading');
                        openMiniChat(thread_id, true).always(function (done) {
                            group.removeClass('blocked loading');
                        });
                    } else {
                        group.addClass('blocked loading');
                        location.href = url;
                    }
                }
            }
        });

        $(document).on('click', '.bp-messages-user-list .user:not(.blocked)', function(event){
            if($(event.target).is('div')){
                event.preventDefault();
                var user = $(this);
                var user_id = $(this).data('id');
                var username = $(this).data('username');

                var insideColumnView = user.closest('.bp-messages-column').length > 0 || user.closest('.bp-messages-side-threads').length > 0;

                if( insideColumnView ){
                    var redirect = BP_Messages['url'] + urlSeparator + 'new-message&to=' + username;
                    if(BP_Messages['fastStart'] == '1') redirect += '&fast=1';

                    var mainContainer = user.closest('.bp-messages-wrap');
                    if( mainContainer.length > 0 ) {
                        ajaxRefresh(redirect, mainContainer);
                    }
                } else if ( BP_Messages['miniChats'] == '1' && BP_Messages['fastStart'] == '1' ) {
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
                    var redirect = BP_Messages['url'] + urlSeparator + 'new-message&to=' + username;
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

    }

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

        var min_height = BP_Messages['min_height'];

        if(height > BP_Messages['max_height']) {
            height = BP_Messages['max_height'];
        }

        if( BP_Messages['fixedHeaderHeight'] > 0 ){
            height = height - BP_Messages['fixedHeaderHeight'];
        }

        height = parseInt(height);

        if( height < min_height ) height = min_height;

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

    BPBMJQ(document).on('click', '.bp-messages-wrap .chat-header .bpbm-maximize', function (event){
        event.preventDefault();

        var wrap = BPBMJQ(this).closest('.bp-messages-wrap');

        if( $('.' + fullScreenHolderClass ).length === 0 ){
            $('<div class="' + fullScreenHolderClass + '"></div>').insertBefore(wrap);
        }

        var source = wrap;
        source.addClass('bp-messages-full-screen');

        var windowHeight = window.innerHeight;
        $('html').addClass('bp-messages-full-screen').css('overflow', 'hidden');
        $('body').addClass('bp-messages-full-screen').css('min-height', windowHeight);
        $('.bp-better-messages-mini .chats .chat.open').removeClass('open');
        var fullScreenViewContainer = source.appendTo( $('body') );
        fullScreenViewContainer.find('.user-scrolled').removeClass('user-scrolled');
        fullScreenViewContainer.show();
        calculateTitle(fullScreenViewContainer);

        $(window).trigger('resize');

        blockScroll = true;
    });


    BPBMJQ(document).on('click', '.bp-messages-wrap .chat-header .bpbm-minimize', function (event){
        event.preventDefault();

        var wrap = BPBMJQ(this).closest('.bp-messages-wrap');

        if( wrap.hasClass('bp-messages-full-screen') ){
            event.preventDefault();
            event.stopImmediatePropagation();

            $('html').removeClass('bp-messages-full-screen').css('overflow', 'auto');
            wrap.removeClass('bp-messages-full-screen').css('min-height', '');
            $('body').removeClass('bp-messages-full-screen').css('min-height', '');

            blockScroll = false;

            var source = wrap;
            source.removeClass('bp-messages-full-screen');
            source.removeAttr('id');

            var holder = $('.' + fullScreenHolderClass );
            source.insertBefore( holder );
            source.find('.user-scrolled').removeClass('user-scrolled');
            holder.remove();

            $(window).trigger('resize');

            calculateTitle(source);
            //scrollBottom(source);
        }

        blockScroll = false;
    });

    BPBMJQ(document).on('bp-better-messages-update-unread', function( event ) {
        var _unread = BPBMJQ('.bp-better-messages-unread');

        unread = parseInt(event.originalEvent.detail.unread);
        if( isNaN ( unread ) || unread < 0 ) unread = 0;

        BMStoreSet('bp-better-messages-last-unread', unread);

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


    jQuery(document).on('bp-better-messages-reinit', function (event) {
         reInit();
    });

    /**
     * Function to determine where we now and what we need to do
     */
    function reInit() {
        thread = false;
        threads = false;
        reIniting = true;
        clearTimeout(checkerTimer);



        if( BP_Messages.realtime !== '1' ) {
            BPBMUpdateUnreadCount(BP_Messages.total_unread);
        }

        document.dispatchEvent(new BPBMEvent('bp-better-messages-reinit-start'));

        updateOpenThreads();

        onlineInit();

        if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary) .thread.scroller.bm-infodiv").length > 0) {
            thread = $(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary) .thread.scroller.bm-infodiv[data-id]").attr('data-id');
        } else if ($(".bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,#bp-better-messages-mini-mobile-container,.bp-better-messages-secondary) .threads-list").length > 0) {
            threads = true;
        }

        if (thread) {
            checkerTimer = setTimeout(refreshThread, BP_Messages.threadRefresh);
        } else {
            checkerTimer = setTimeout(refreshSite, BP_Messages.siteRefresh);
        }

        

        var direction = 'ltr';
        if( isRtl ) direction = 'rtl';

        var textAreaOpts = {
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
            imageDragging: false,
            extensions: {
                "mention": new BMTCMention({
                    tagName: "span",
                    renderPanelContent: function (panelEl, currentMentionText, selectMentionCallback, extensionObject) {
                        var suggestionsDiv = jQuery(panelEl);

                        var searchText = currentMentionText.substring(1).toLowerCase();
                        var chatContainer = jQuery(extensionObject.base.elements[0]).closest('.bpbm-chat-content');

                        var thread_id = chatContainer.find('.bm-infodiv[data-id]').attr('data-id');


                        var cache = {};


                        if( typeof cache[thread_id] === 'undefined' ){
                            cache[thread_id] = {};
                        }

                        if( typeof cache[thread_id][searchText] !== 'undefined' ){
                            updateUsersList( cache[thread_id][searchText] );
                        } else {
                            suggestionsDiv.html('<i class="fas fa-spinner fa-spin"></i>');

                            jQuery.post(BP_Messages['ajaxUrl'], {
                                'action': 'bm_get_thread_mentions_suggestions',
                                'thread_id': thread_id,
                                'search': searchText,
                                'nonce': BP_Messages['userNonce'],
                            }, function (response) {
                                cache[thread_id][response['search']] = response['users'];
                                updateUsersList(response['users']);
                            });
                        }


                        function updateUsersList( users ){
                            var items = [];

                            jQuery.each(users, function (user_id, user) {
                                if (BP_Messages['user_id'] !== user_id) {
                                    if (user.name.toLowerCase().indexOf(searchText) !== -1) {
                                        items.push('<li data-user-id="' + user_id + '" data-label="' + user.name + '">' + user.avatar + ' ' + user.name + '</li>');
                                    }
                                }
                            });

                            if (items.length > 0) {
                                var htmlList = '<ul>';
                                htmlList += items.join('');
                                htmlList += '</ul>';

                                suggestionsDiv.html(htmlList);

                                suggestionsDiv.find('> ul > li').click(function (event) {
                                    event.preventDefault();
                                    event.stopImmediatePropagation();
                                    var li = jQuery(this);

                                    var user_id = li.attr('data-user-id');
                                    var label = li.attr('data-label');

                                    var tag = jQuery('.bm-medium-editor-mention-at-active');
                                    tag.attr('data-user-id', user_id);
                                    tag.text('@' + label);
                                    tag.addClass('bm-medium-editor-mention-user-' + user_id);

                                    selectMentionCallback();
                                });

                                suggestionsDiv.show();
                            } else {
                                suggestionsDiv.hide();
                            }
                        }

                        //if( typeof usersJson === 'undefined' ){
                        //    suggestionsDiv.hide();
                        //    return;
                        //}


                    },
                    activeTriggerList: ["@"]
                })
            }
        };

        if( ! isMobile && ! $('body').hasClass('bp-messages-mobile') ) {
            var selector = ".bp-messages-wrap .reply .message textarea, " +
                ".bp-messages-wrap .new-message #message-input, " +
                ".bp-messages-wrap .bulk-message #message-input";
            initializeEmojiArea(selector);
        } else {
            var selector = ".bp-messages-wrap.mobile-ready .reply .message textarea";

            initializeMobileArea(selector);
            makeHeightBeautiful();
        }

        function setCursor(node, pos) {
            // Creates range object
            var setpos = document.createRange();
            // Creates object for selection
            var set = window.getSelection();
            // Set start position of range
            setpos.setStart(node, pos);

            // Collapse range within its boundary points
            // Returns boolean
            setpos.collapse(true);

            // Remove all ranges set
            set.removeAllRanges();

            // Add range with respect to range object.
            set.addRange(setpos);
        }

        function initMediaEditor(media_editor){
            media_editor.subscribe('focus', function (data, editable) {
                var element = $(editable);
                if( editable.childNodes.length === 0 ) {
                    element.html('<p></p>');

                    setCursor(editable.childNodes[0], 0);
                    editable.focus();
                }
            });

            media_editor.subscribe('editableInput', function (data, editable) {
                var element = $(editable);

                //if (!element.hasClass('bpbm-interacted')) {
                //    setTimeout(function () {
                //        //element.addClass('bpbm-interacted');
                //    }, 100);
                //}

                if( element.hasClass('bm-mobile-area') ) {
                    element.next().trigger('change');
                }

                var childNodes = editable.childNodes;
                if( childNodes.length > 0 ) {
                    var lastNode = childNodes[childNodes.length - 1];
                    if( lastNode.outerHTML === '<p><br></p>' ) {
                        setCursor(lastNode, 0);
                        editable.focus();
                    }
                }

                element.find('font').removeAttr('color');
            });

            media_editor.subscribe('editablePaste', function (data, editable) {
                var editor = $(editable);
                var reply = editor.closest('.reply');
                if (!reply.find('form > .message').hasClass('file-uploader-enabled')) return;
                var files = toArray(event.clipboardData.items);

                var filesToUpload = [];

                files.forEach(function (file) {
                    if (file.kind !== 'file') return;

                    var blob = file.getAsFile();
                    if (!blob) {
                        return;
                    }

                    filesToUpload.push({
                        name: file.name,
                        type: file.type,
                        data: blob
                    });
                });

                if (filesToUpload.length > 0) {
                    var thread_id = reply.find('input[name="thread_id"]').val();

                    reply.find('.upload-btn').click();

                    var uppy = $('body > .uppy.uppy-thread-' + thread_id);
                    if (uppy.length > 0) {
                        uppy = uppy[0].uppy;
                        filesToUpload.forEach(function (file) {
                            var args = {
                                source: uppy.id,
                                type: file.type,
                                data: file.data
                            };

                            if (typeof file.name !== 'undefined' && file.name !== 'undefined') {
                                args['name'] = file.name;
                            } else {
                                args['name'] = '';
                            }

                            uppy.addFile(args).catch(function () {
                                // Ignore
                            });
                        });
                    }
                }
            });
        }

        function initializeMobileArea(selector){
            var areasToInit   = $(selector);
            areasToInit.each(function(){
                var area = $(this);

                var opts = {...textAreaOpts}

                area.attr("data-placeholder",  area.attr("placeholder"));
                opts.placeholder = true;

                setTimeout(function(){
                    var media_editor = new BPBM_MediumEditor(area, opts);

                    $(media_editor.elements[0]).addClass('bm-mobile-area');
                    initMediaEditor( media_editor );
                }, 333);
            });
        }

        function initializeEmojiArea(selector){
            var areasToInit   = $(selector);

            areasToInit.each(function(){
                var emojioneareaParent = $(this);
                if( ! emojioneareaParent.hasClass('bp-emoji-area-launched') ) {
                    emojioneareaParent.addClass('bp-emoji-area-launched');

                    var emojionearea = emojioneareaParent.BPemojioneArea({
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
                        } catch (e) {
                        }

                        setTimeout(afterAreaLoaded, 333);

                        function afterAreaLoaded() {
                            if (areaLoaded === true) {
                                return false;
                            }

                            var emojiArea  = emojionearea.next('.bp-emojionearea');
                            var areaExists = emojiArea.length > 0;

                            if (areaExists) {
                                areaLoaded = true;
                                makeHeightBeautiful();

                                var media_editor = new BPBM_MediumEditor(document.querySelectorAll('.bp-emojionearea-editor'), textAreaOpts);

                                initMediaEditor( media_editor );

                                $(emojionearea).html('<p></p>');
                            } else {
                                setTimeout(afterAreaLoaded, 333);
                            }
                        }
                    }
                }
            });
        }

        if( typeof $.fn.BPBMoverlayScrollbars === 'function' ) {

            $(document).on("bp-better-messages-init-scrollers", function(){

                BPBMJQ('.bpbm-users-avatars-list').BPBMoverlayScrollbars({
                    'sizeAutoCapable': true,
                    overflowBehavior: {
                        x: 'scroll',
                        y: 'hidden'
                    },
                    scrollbars: {
                        clickScrolling: true,
                        autoHide: 'leave'
                    }
                });

                BPBMJQ('.bp-messages-wrap div.new-message').BPBMoverlayScrollbars({
                    'sizeAutoCapable': true,
                    overflowBehavior : {
                        x: 'hidden'
                    }
                });

                BPBMJQ('.bpbm-stickers-selector .bpbm-stickers-head .bpbm-stickers-tabs').BPBMoverlayScrollbars({
                    'sizeAutoCapable': false,
                    overflowBehavior : {
                        y: 'hidden'
                    }
                });


                var scrolling = false;
                var sizeAutoCapable = false;

                BPBMJQ('.scroller.thread').BPBMoverlayScrollbars({
                    'sizeAutoCapable': sizeAutoCapable,
                    'autoUpdate' : true,
                    overflowBehavior: {
                        x: 'hidden'
                    },
                    callbacks : {
                        onInitialized: function(){
                            var scroller   = this;
                            var elements   = this.getElements();
                            var position   = this.scroll();
                            var host       = BPBMJQ(this.getElements().host);
                            var height     = position.max.y;

                            var message_to = false;

                            if (getParameterByName('message_id').length > 0) {
                                var message_id = getParameterByName('message_id');
                                var message = $(elements.content).find( ".messages-list li[data-id='" + message_id + "']");

                                if (message.length > 0) {
                                    message_to = message;
                                }
                            }

                            setTimeout(function(){
                                var scrollDownEl = BPBMJQ('<span class="bpbm-scroll-down"><i class="fas fa-chevron-down"></i></span>').prependTo(host);

                                scrollDownEl.on('click touchstart', function(event){
                                    event.preventDefault();
                                    scroller.scrollStop();
                                    scroller.scroll({y: '100%'});
                                    scrollDownEl.removeClass('bpbm-shown');
                                    scrollDownEl.addClass('bmbm-blocked');

                                    setTimeout(function(){
                                        scrollDownEl.removeClass('bmbm-blocked');
                                    }, 1000);
                                });

                            }, 1000);

                            if( message_to !== false ){
                                BPBMJQ(scroller.getElements().host).addClass('user-scrolled');
                                scroller.scroll({ el : message_to, scroll : "ifneeded", margin : 20 });
                            } else if( BPBMJQ(elements.host).is(':visible') && height === 0 ){
                                loadMoreMessages(this, true);
                            } else {
                                scroller.scrollStop();
                                scroller.scroll({y: '100%'});
                            }
                        },
                        onScroll : function( arg1 ){
                            var scroller  = this;
                            var position  = this.scroll();
                            var scroll    = position.position.y;
                            var height    = position.max.y;
                            var host      = BPBMJQ(scroller.getElements().host);

                            var scrollDown = host.find('.bpbm-scroll-down:not(.bmbm-blocked)');
                            if( ( height - scroll ) > ( host.height() * 2 ) ){
                                scrollDown.addClass('bpbm-shown');
                            } else {
                                scrollDown.removeClass('bpbm-shown');
                            }
                            if( height === 0 ){
                                loadMoreMessages(this, true);
                                host.addClass('user-scrolled');
                            } else if( scroll === 0 ) {
                                loadMoreMessages(this);
                                host.addClass('user-scrolled');
                            } else if( scroll >= height ){
                                setTimeout(function(){
                                    scroll    = position.position.y;
                                    height    = position.max.y;
                                    if( scroll >= height ) {
                                        host.removeClass('user-scrolled');
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
                                    _this.scrollStop();
                                    _this.scroll({y: '100%'}, 100, undefined, function(){
                                        scrolling = false;
                                    });
                                }
                            }

                            setTimeout(function(){
                                if( ! host.hasClass('user-scrolled') ){
                                    if( ! isTapped ) {
                                        scrolling = true;
                                        _this.scrollStop();
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
                                this.scrollStop();
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


                BPBMJQ('.bp-better-messages-list .tabs-content .scroller,.scroller.search, .scroller.starred').BPBMoverlayScrollbars({
                    'sizeAutoCapable': false,
                    overflowBehavior: {
                        x: 'hidden'
                    },
                });
            });


            document.dispatchEvent(new CustomEvent('bp-better-messages-init-scrollers'));
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
            $(  '.bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary) .list .messages-stack .content .messages-list li:not(.seen), ' +
                '.bp-messages-wrap.bp-better-messages-mini .chat.open .list .messages-stack .content .messages-list li:not(.seen)'
            ).each(function () {
                var id = $(this).data('id');
                var thread_id = $(this).data('thread');
                messages_ids.push(id);
                threadsIds.push(thread_id);
            });

            if(thread) {
                threadOpenEvent(thread);
                updateGroupCallStatus( thread );
            }

            if( BP_Messages['messagesStatus'] === '1') {
                if( messages_ids.length > 0 ) {
                    socket.emit('getStatuses', messages_ids, function (statuses) {
                        var requestedIds = {};
                        $.each(messages_ids, function () {
                            requestedIds[this] = true;
                        });

                        $.each(statuses, function (index) {
                            delete requestedIds[index];
                            var message = $('.bp-messages-wrap .list .messages-stack .content .messages-list li[data-id="' + index + '"]');
                            message.removeClass('sent delivered seen');
                            if (!message.hasClass('my') && !message.hasClass('fast')) {
                                if (this.toString() !== 'seen') {
                                    socket.emit('seen', [index]);
                                }
                            } else {
                                var status = 'sent';

                                $.each(this, function () {
                                    if (status == 'seen') return false;
                                    if (this == 'delivered' && status != 'seen') status = 'delivered';
                                    if (this == 'seen') status = 'seen';
                                });

                                message.addClass(status);
                                var statusTitle = '';
                                switch (status) {
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

                        $.each(requestedIds, function (message_id) {
                            var message = $('.bp-messages-wrap .list .messages-stack .content .messages-list li[data-id="' + message_id + '"]');
                            message.removeClass('sent delivered seen');
                            message.addClass('seen');
                            message.find('.status').attr('title', BP_Messages['strings']['seen']);
                            updateMessagesStatus(message);
                        });
                    });
                }
            }

            threadsIds = unique(threadsIds);
            $.each(threadsIds, function (index, item) {
                if(typeof item !== 'undefined'){
                    threadOpenEvent(item);
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

            var keys = new Map();

            var args = {
                valueField: "value",
                labelField: "label",
                searchField: "label",
                delimiter: ",",
                create: true,
                loadThrottle: 300,
                closeAfterSelect: true,
                openOnFocus: false,
                plugins: ["remove_button"],
                render: {
                    option: function (item, escape) {
                        var avatar = '';

                        try{
                            item = JSON.parse(item.label);

                            if( typeof item.img !== 'undefined' ) {
                                avatar += '<span class="bpbm-avatar"><img src="' + escape(item.img) + '" class="avatar photo" width="50" height="50"></span>';
                            }

                            return (
                                '<div>' + avatar + '<span class="bpbm-name">' + escape(item.label) + '</span></div>'
                            );
                        } catch (e) {}

                        if( typeof item.img !== 'undefined' ) {
                            avatar += '<span class="bpbm-avatar">' + item.img + '</span>';
                        }

                        if( typeof item.label !== 'undefined' )
                        {
                            return (
                            '<div>' + avatar + '<span class="bpbm-name">' + escape(item.label) + '</span></div>'
                            );
                        } else {
                            return '<div></div>';
                        }
                    },
                    option_create: function (data, escape) {
                        return '<div class="create"><strong>' + escape(data.input) + "</strong></div>"
                    },
                    item: function (item, escape) {
                        var avatar = '';
                        if( keys.has( item.value ) ) {
                            var data = keys.get(item.value);

                            if( typeof data.img !== 'undefined' ) {
                                avatar += '<span class="bpbm-avatar"><img src="' + escape(data.img) + '" class="avatar photo" width="50" height="50"></span>';
                            }

                            return (
                                '<div><span class="bpbm-avatar">' + avatar + '</span><span class="bpbm-name">' + escape(data.label) + '</span></div>'
                            );
                        }

                        if( typeof item.label !== 'undefined' ) {

                            if( typeof item.img !== 'undefined' ) {
                                avatar += '<span class="bpbm-avatar">' + item.img + '</span>';
                            }

                            return (
                                '<div>' + avatar + '<span class="bpbm-name">' + escape(item.label) + '</span></div>'
                            );
                        } else {
                            return (
                                '<div><span class="bpbm-name">' + escape(item.value) + '</span></div>'
                            );
                        }
                    },
                },

                formatValueToKey: function(key){
                    try {
                        var data = JSON.parse(key);
                        keys.set(data.value, data);
                        return data.value;
                    } catch (e) {}

                    return key;
                },

                onChange: function(value){
                    $('input[name="recipients[]"]').remove();

                    var recipients = value.split(',');

                    $.each(recipients, function(){
                        $('<input type="hidden" name="recipients[]" value="' + this + '">').insertAfter($('#send-to'));
                    })
                }
            };

            if(BP_Messages['disableUsersSearch'] === '0') {

                $('.bpbm-users-avatars-list .bpbm-users-avatars-list-item').click(function (event) {
                    event.preventDefault();
                    var el = $(this);
                    var name = el.find('.bpbm-users-avatars-list-item-name').text();
                    var image = el.find('.bpbm-users-avatars-list-item-avatar img').attr('src');
                    var nicename = el.attr('data-nicename');

                    $("#send-to")[0].bmselectise.createItem( JSON.stringify({
                        value : nicename,
                        img   : image,
                        label : name
                    }) );
                });

                args['load'] = function (query, callback) {
                    if (!query.length) return callback();

                    $.ajax({
                        url: BP_Messages.ajaxUrl + "?q=" + encodeURIComponent(query) + "&limit=10&action=bp_messages_autocomplete&cookie=" + getAutocompleteCookies(),
                        type: "GET",
                        error: function () {
                            callback();
                        },
                        success: function (res) {
                            callback(res);
                        },
                    });
                };
            }

            var sendToSelect = $("#send-to").bmselectise(args);

            var to = $('input[name="to"]');

            if (to.length > 0) {
                $(to).each(function () {
                    var img   = $(this).data('img');
                    var label =  $(this).data('label');

                    sendToSelect[0].bmselectise.createItem( JSON.stringify({
                        value : $(this).val(),
                        img   : img,
                        label : label
                    }) );

                    $(this).remove();
                });
            }
        }

        if( typeof bpMessagesWrap !== 'undefined' ) {
            bpMessagesWrap.find('.bp-messages-mobile-tap').css('line-height', bpMessagesWrap.height() + 'px');
        }

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
                    var nonce = BP_Messages['userNonce'];
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
                                emojieditor.html(joypixels.toImage(response));
                            } else {
                                wrap.find('.reply .message textarea').val(response);
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
                var nonce = BP_Messages['userNonce'];
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

        if( BP_Messages['user_id'] !== '0' ) {

            $.BPBMcontextMenu({
                // define which elements trigger this menu
                selector: outgoingSelector,
                events: menuEvents,
                items: outgoingItems,
                autoHide: false,
                zIndex: 10,
                position: function (opt, x, y) {
                    var maxLeft = window.innerWidth - opt.$menu.width() - 100;
                    if (x > maxLeft) x = maxLeft;

                    opt.$menu.css({left: x, top: y});
                }
            });

            $.BPBMcontextMenu({
                // define which elements trigger this menu
                selector: incomingSelector,
                events: menuEvents,
                items: incomingItems,
                autoHide: false,
                zIndex: 10,
                position: function (opt, x, y) {
                    var maxLeft = window.innerWidth - opt.$menu.width() - 100;
                    if (x > maxLeft) x = maxLeft;

                    opt.$menu.css({left: x, top: y});
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
                visible: function (key, opt) {
                    if (opt.$trigger.closest('.thread').find('span.delete').length > 0) {
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
                position: function (opt, x, y) {
                    var maxLeft = window.innerWidth - opt.$menu.width() - 100;
                    if (x > maxLeft) x = maxLeft;

                    opt.$menu.css({left: x, top: y});
                }
            });

            initMobileSwipes();
            /**
             * JetPack Lazy Load
             */

            //document.body.dispatchEvent(new BPBMEvent('post-load'));

            

        }

        

        calculateTitle(bpMessagesWrap);

        $('.bpbm-gifs-icon').html(icons.gif);
        addIframeClasses();

        

        initTooltips();

        if( isMobile && typeof bpMessagesWrap !== 'undefined' ) {
            bpMessagesWrap.find('.bpbm-chat-content').css('max-height', 'calc( 100% - ' + bpMessagesWrap.find('.chat-header').outerHeight() + 'px )');
        }

        initReactionsSelectors();

        document.dispatchEvent(new BPBMEvent('bp-better-messages-reinit-end'));

        reIniting = false;
    }

    function initReactionsSelectors(){
        var reactions        = BP_Messages['reactions'];
        if( typeof reactions === 'undefined' ) return false;

        var reactionSelector = '<span class="bm-reactions-selector">';

        jQuery.each(reactions, function(unicode, name){
            reactionSelector += '<span title="' + name + '" data-unicode="' + unicode + '" class="bm-reaction-icon">' +
                '<img alt="' + name + '" src="https://cdn.bpbettermessages.com/emojies/6.6/png/unicode/32/' + unicode + '.png">' +
                '</span>';
        });

        reactionSelector += '</span>';

        var messages = jQuery('.bp-messages-wrap .list .messages-stack .content .messages-list > li:not(.my) .message-content:not(.bm-reactions-selector-added)');

        messages.each(function(){
            jQuery( this ).addClass('bm-reactions-selector-added').append(reactionSelector);

            var addedSelector = jQuery( this ).find('.bm-reactions-selector');

            if( isMobile ) {
                addedSelector.on('mouseenter', function () {
                    var selector = $(this);
                    setTimeout(function () {
                        selector.addClass('bm-reactions-selector-hover');
                    }, 500);
                });

                addedSelector.on('mouseleave', function () {
                    var selector = $(this);
                    setTimeout(function () {
                        selector.removeClass('bm-reactions-selector-hover')
                    }, 500);
                });
            }


            addedSelector.on('click', '.bm-reaction-icon', function(event){
                event.preventDefault();
                var button     = $(this);
                var message    = button.closest('li');
                var unicode    = button.attr('data-unicode');
                var message_id = message.attr('data-id');
                var content    = message.find('.message-content');

                var selector   = button.closest('.bm-reactions-selector');

                if( isMobile && ! selector.hasClass('bm-reactions-selector-hover') ){
                    return false;
                }

                $.post(BP_Messages.ajaxUrl, {
                    action     : 'bp_messages_add_reaction',
                    message_id : message_id,
                    unicode    : unicode,
                    nonce      : BP_Messages['userNonce']
                }, function(response){
                    if( response.success === true ){
                        var new_reactions = response.data;

                        var old_reactions = content.find('.bm-reactions');
                        if( old_reactions.length > 0 ){
                            old_reactions.replaceWith(new_reactions);
                        } else {
                            content.append(new_reactions);
                        }
                    }
                });
            });
        });
    }

    function initTooltips( wrap ){
        if (typeof wrap === 'undefined') wrap = BPBMJQ('.bp-messages-wrap');

        if( ! isMobile ) {
            wrap.find( '.bpbm-users-avatars-list-item[title], .status[title], .bpbm-reply[title], .chat-header [title], .chat-footer [title], .threads-list [title], .chats .chat .head .controls [title]').each(function () {
                var el = BPBMJQ(this);

                if (el.is('strong') || el.is('input') ) {

                } else {
                    if( typeof (el[0]._tippy) !== 'undefined' ){
                        el[0]._tippy.destroy();
                    }

                    bpbmtippy(el[0], {
                        placement: 'top',
                        content: el.attr('title'),
                        allowHTML: true,
                        arrow: false,
                        offset: [0, 5],
                        onShow: function(instance) {
                            var reference = BPBMJQ(instance.reference);
                            if( reference.is('.expandingButtons') && reference.hasClass('expandingButtonsOpen') ){
                                return false;
                            }
                        },
                    });

                    el[0].setAttribute('data-title', el.attr('title'));
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

                if( data.trim() !== '' ) {
                    var loadingMore = wrap.find('.loading-messages');
                    var margin = loadingMore.height() + 15;
                    scroller.scroll( { el: last_message.closest('.messages-stack'), margin : margin } );

                    reInit();
                    wrap.removeClass('loadingAtTheMoment');


                    if( loadUntilScroll ){
                        if( scroller.scroll().max.y === 0){
                            loadMoreMessages(scroller, true);
                        }
                    }
                }
            }
        );
    }

    function scrollBottom(target) {
        if(typeof target == 'undefined') target = '.bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary)';
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

                    if(typeof message['avatar'] === 'string' ) {
                        message['avatar'] = message['avatar'].replace("loading='lazy'", '').replace('loading="lazy"', '');
                    }

                    updateThreads(message);

                    if ( ! threads ) {
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
        if( BP_Messages['user_id'] === '0' ) return false;

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

        BMStoreSet('bp-better-messages-open-threads', openThreads);
    }


    jQuery(document).on('bp-better-messages-ajax-refresh', function(event){
        var url       = event.originalEvent.detail.url;
        var container = event.originalEvent.detail.container;

        ajaxRefresh(url, container);
    });
    
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
                document.dispatchEvent(new CustomEvent('bpbm-end-call'));
                document.dispatchEvent(new CustomEvent('bpbm-end-group-call'));
            }
        }


        if( typeof window.BMAppDeviceId === 'undefined' && container.hasClass('bp-messages-wrap-main') && ! container.hasClass('bp-messages-group-thread') ){
            try {
                window.history.pushState("", "", url);
            } catch(e){}
        }

        showPreloader(container);

        var containerHeight = container.height();
        container.css('min-height', containerHeight);

        $(window).off( ".bp-messages" );
        var target_url = '';
        var question_added = false;

        if( typeof BP_Messages['ajaxUrl'].split('?')[1] !== 'undefined' ){
            question_added = true;
        }

        if( question_added ){
            target_url += '&';
        } else {
            question_added = true;
            target_url += '?';
        }

        if( typeof url.split('?')[1] !== 'undefined' ){
            target_url += url.split('?')[1] + '&action=bp_messages_load_via_ajax';
        } else {
            target_url += 'action=bp_messages_load_via_ajax';
        }

        var side_threads = container.find('.bp-messages-side-threads');
        var ajax_url = BP_Messages['ajaxUrl'] + target_url;

        if( side_threads.length > 0 ){
            ajax_url += '&ignore_threads';
        }

        $('.bpbm-notice').remove();

        document.dispatchEvent(new CustomEvent('bp-better-messages-ajax-refresh-start'));

        $.ajax({
            method: "GET",
            url: ajax_url,
            dataType: 'json',
            cache: false,
            success: function (json) {
                if( typeof json['total_unread'] !== 'undefined'){
                    BPBMUpdateUnreadCount(json['total_unread']);
                }

                if( typeof json['errors'] !== 'undefined'){
                    $.each(json['errors'], function(){
                        BBPMShowError(this);
                    });
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
                    var newThreadId = html.attr('data-thread-id');
                    if( !! newThreadId ) {
                        container.attr('data-thread-id', html.attr('data-thread-id'));
                    } else {
                        container.removeAttr('data-thread-id');
                    }
                    if( typeof json.errors !== 'undefined' ){
                        $(json.errors.join('')).insertBefore(container);
                    }
                }

                if(container.is('#bp-better-messages-mini-mobile-container')){
                    container.attr('data-thread', html.attr('data-thread-id') );
                }

                if( container.hasClass('bp-messages-wrap-chat') ) {
                    container.find('.chat-header .back').remove();
                }

                reInit();

                container.removeClass('bpbm-call-view');

                container.css('min-height', '');

                document.dispatchEvent(new CustomEvent('bp-better-messages-ajax-refresh-end'));
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
        var friendsWidgets = $('.bp-better-messages-list .tabs-content .friends,.bp-better-messages-list .tabs-content .um-friends,.bp-better-messages-list .tabs-content .ps-friends, .bpbm-friends-list, .bpbm-um-friends-list, .bpbm-ps-friends-list');
        if( friendsWidgets.length > 0 ){
            friendsWidgets.each(function (){
                var friendsWidget = $(this);
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
            });
        }
    }

    function chatAdditionalTab( currentTab ){
        if( currentTab.hasClass('bpbm-friends-list') ) {
            if( ! currentTab.hasClass('bpbm-loaded') ) {
                currentTab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_friends_list',
                    'nonce': BP_Messages['userNonce'],
                }, function (response) {
                    currentTab.html(response);
                    onlineInit();
                    sortOnlineFriends();
                });
            }
        }

        if( currentTab.hasClass('bpbm-groups-list') ) {
            if( ! currentTab.hasClass('bpbm-loaded') ) {
                currentTab.addClass('bpbm-loaded');

                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_groups_list',
                    'nonce': BP_Messages['userNonce'],
                }, function (response) {
                    currentTab.html(response);
                });
            }
        }

        if( currentTab.hasClass('bpbm-ps-groups-list') ) {
            if( ! currentTab.hasClass('bpbm-loaded') ) {
                currentTab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_ps_groups_list',
                    'nonce': BP_Messages['userNonce']
                }, function (response) {
                    currentTab.html(response);
                });
            }
        }

        if( currentTab.hasClass('bpbm-um-friends-list') ) {
            if( ! currentTab.hasClass('bpbm-loaded') ) {
                currentTab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_um_friends_list',
                    'nonce': BP_Messages['userNonce'],
                }, function (response) {
                    currentTab.html(response);
                    onlineInit();
                    sortOnlineFriends();
                });
            }
        }

        if( currentTab.hasClass('bpbm-ps-friends-list') ) {
            if( ! currentTab.hasClass('bpbm-loaded') ) {
                currentTab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_ps_friends_list',
                    'nonce': BP_Messages['userNonce'],
                }, function (response) {
                    currentTab.html(response);
                    onlineInit();
                    sortOnlineFriends();
                });
            }
        }

        currentTab.show();
    }

    function initMiniTab( selectedTab ){
        var tab;

        if( selectedTab === 'friends' ) {
            tab = $('.bp-better-messages-list .tabs-content > .friends');

            if( ! tab.hasClass('bpbm-loaded') ) {
                tab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_friends_list',
                    'nonce': BP_Messages['userNonce'],
                    'mini' : true
                }, function (response) {
                    tab.find('.bp-messages-user-list').html(response);
                    onlineInit();
                    sortOnlineFriends();
                });
            }
        }

        if( selectedTab === 'ps-friends' ) {
            tab = $('.bp-better-messages-list .tabs-content > .ps-friends');

            if( ! tab.hasClass('bpbm-loaded') ) {
                tab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_ps_friends_list',
                    'nonce': BP_Messages['userNonce'],
                    'mini' : true
                }, function (response) {
                    tab.find('.bp-messages-user-list').html(response);
                    onlineInit();
                    sortOnlineFriends();
                });
            }
        }

        if( selectedTab === 'um-friends' ) {
            tab = $('.bp-better-messages-list .tabs-content > .um-friends');

            if( ! tab.hasClass('bpbm-loaded') ) {
                tab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_um_friends_list',
                    'nonce': BP_Messages['userNonce'],
                    'mini' : true
                }, function (response) {
                    tab.find('.bp-messages-user-list').html(response);
                    onlineInit();
                    sortOnlineFriends();
                });
            }
        }

        if( selectedTab === 'bpbm-groups' ) {
            tab = $('.bp-better-messages-list .tabs-content > .bpbm-groups');

            if( ! tab.hasClass('bpbm-loaded') ) {
                tab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_groups_list',
                    'nonce': BP_Messages['userNonce'],
                    'mini' : true
                }, function (response) {
                    tab.find('.bp-messages-group-list').html(response);
                });
            }
        }

        if( selectedTab === 'ps-groups' ) {
            tab = $('.bp-better-messages-list .tabs-content > .ps-groups');

            if( ! tab.hasClass('bpbm-loaded') ) {
                tab.addClass('bpbm-loaded');
                $.post(BP_Messages['ajaxUrl'], {
                    'action': 'bp_messages_load_ps_groups_list',
                    'nonce': BP_Messages['userNonce'],
                    'mini' : true
                }, function (response) {
                    tab.find('.bp-messages-group-list').html(response);
                });
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
        if(typeof selector === 'undefined') selector = '.bp-messages-wrap:not(.bp-better-messages-mini,.bp-better-messages-list,.bp-better-messages-secondary)';

        if( message.message.trim() == '' ) return false;
        var isEdit = (message['edit'] == '1') ? true : false;


        if( BP_Messages['realtime'] == "1" && message.fast ){
            var messageAlreadyExist = $(selector + ' .messages-list li[data-temp-id="' + message.id + '"]');
            if( messageAlreadyExist.length > 0 ){
                return false;
            }
        }

        var avatar;
        if( typeof message.avatar !== 'string' || BP_Messages['showAvatars'] !== '1' ){
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

        if( container.length === 0 ){
            container = $(selector);
        }

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

        var dataTempId  = '';
        if (replaceTemp){
            dataTempId = ' data-temp-id="' + message.temp_id + '"';
        }
        var messageHtml = '<li class="' + className + '" data-thread="' + message.thread_id + '" title="' + readableDate + '" data-time="' + message.timestamp + '" data-id="' + message.id + '" ' + dataTempId + '>';

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
        initReactionsSelectors();

        document.dispatchEvent(new BPBMEvent('bp-better-messages-message-render-end'));

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

        if( typeof avatar === 'string' ){
            var findSrc = avatar.match(/src\="([^\s]*)"\s/);
            var findSrc2 = avatar.match(/src\='([^\s]*)'\s/);

            if (findSrc != null) {
                avatar = findSrc[1];
            }

            if (findSrc2 != null) {
                avatar = findSrc2[1];
            }
        } else {
            avatar = false;
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
            var args = {
                user: name,
                message: message
            };

            if( avatar !== false ){
                args.img = avatar;
            } else {
                args.img = '';
            }

            $.amaran({
                'theme': 'user message thread_' + thread_id,
                'content': args,
                'sticky': true,
                'closeOnClick': false,
                'closeButton': true,
                'delay': 10000,
                'thread_id': thread_id,
                'position': 'bottom right',
                onClick: function () {
                    openThread( this.thread_id );
                }
            });

            if( avatar === false ) {
                var popup = $('.amaran.thread_' + thread_id);
                popup.find('.icon').remove();
                popup.find('.info').css('paddingLeft', 0);
            }


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

    function hasActiveTab(){
        activeTabs = store.get( 'bp-better-messages-active-tabs' ) || {};

        if (typeof activeTabs[tabID] !== 'undefined') {
            return true;
        } else {
            return false;
        }
    }

    

    function showNotification( message ){
        
    }

    function threadOpenEvent( thread_id ){
        
    }


    function openThread( thread_id ){
        
            if( isMobile ){
                var mobilePopup = $('#bp-better-messages-mini-mobile-open');
                if( mobilePopup.length > 0 ){
                    var originalUrl = BP_Messages['baseUrl'];
                    BP_Messages['baseUrl'] = BP_Messages['threadUrl'] + this.thread_id;
                    mobilePopup.click();
                    BP_Messages['baseUrl'] = originalUrl;
                    $('.amaran.user.message.thread_' + thread_id).remove();
                } else {
                    location.href = BP_Messages.threadUrl + thread_id;
                }
            } else {
                var url = BP_Messages.threadUrl + thread_id + '&scrollToContainer';
                var mainContainer = $('.bp-messages-wrap.bp-messages-wrap-main');
                if (mainContainer.length > 0) {
                    ajaxRefresh(url, mainContainer);
                    $('.amaran.user.message.thread_' + thread_id).remove();
                } else {
                    location.href = url;
                }
            }
            
    }

    function hidePossibleBreakingElements(){
        if( BP_Messages['hPBE'] !== '1' ) return false;

        var fixed_elements = $('*:not(.bp-messages-hide-on-full-screen)').filter(function () {
            return ( $(this).css('position') === 'fixed' || $(this).css('position') === 'absolute' )
                && ! $(this).hasClass('bp-messages-wrap')
                && $(this).closest('.uppy').length === 0
                && $(this).closest('.bp-messages-wrap').length === 0;
        });

        fixed_elements.addClass('bp-messages-hide-on-full-screen');
    }

    function openMobileFullScreen( url ) {
        var _miniMobileContainer = $('#bp-better-messages-mini-mobile-container');
        _miniMobileContainer.addClass('bp-messages-mobile');
        _miniMobileContainer.find('.bp-messages-mobile-tap').remove();

        var windowHeight = window.innerHeight;
        $('html').addClass('bp-messages-mobile').css('overflow', 'hidden');
        $('body').addClass('bp-messages-mobile').css('min-height', windowHeight);

        _miniMobileContainer.addClass('bp-messages-mobile').css('min-height', windowHeight);

        var usedHeight = 0;
        var chatHeaderHeight = _miniMobileContainer.find('.chat-header').outerHeight();
        usedHeight           = usedHeight + chatHeaderHeight;
        usedHeight           = usedHeight + _miniMobileContainer.find('.reply').outerHeight();

        var resultHeight = windowHeight - usedHeight;

        $('.scroller').css({
            'max-height': '',
            'height': resultHeight
        });

        bpMessagesWrap.find('.bpbm-chat-content').css('max-height', 'calc( 100% - ' + chatHeaderHeight + 'px )');

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
            if( typeof json['errors'] !== 'undefined'){
                $.each(json['errors'], function(){
                    BBPMShowError(this);
                });
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
        var contentTabs = results.parent().find('> *:not(.bpbm-search-results)');
        var chatTabs = column.find('.chat-tabs');

        var loadingSearchTerm = input.data('search-term');

        if( loadingSearchTerm !== search ) {
            if( search === '' ) {
                contentTabs.removeClass('bpbm-display-none');
                chatTabs.show();
                results.hide();
                close.hide();
                input.data('search-term', search);
            } else {
                results.show();
                close.show();
                contentTabs.addClass('bpbm-display-none');
                chatTabs.hide();

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

    $(document).on("bp-better-messages-open-mini-chat", function (event, thread_id, open) {
        openMiniChat(thread_id, open);
    });

    $(document).on("bp-better-messages-open-private-thread", function (event, user_id) {
        openPrivateThread(user_id);
    });

    function openPrivateThread(user_id){
        var dfd = new $.Deferred();

        $.post(BP_Messages.ajaxUrl, {
            action: 'bp_messages_get_pm_thread',
            user_id: user_id
        }, function(thread_id){
            if( BP_Messages['miniChats'] !== '1' || ( isMobile && BP_Messages['mobileFullScreen'] === '1' ) ){
                var url = BP_Messages.threadUrl + thread_id;
                location.href = url;
            } else {
                openMiniChat(thread_id, true).always(function (done) {
                    dfd.resolve(true);
                });
            }
        });

        return dfd.promise();
    }

    

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
        if( typeof wraps === 'undefined' ) return false;
        wraps.each(function(){
            var wrap = BPBMJQ(this);

            if( ! wrap.is(':visible') ){
                setTimeout(function(){
                    calculateTitle(wrap);
                }, 100);
            } else {
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
            }
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

    function toArray(list, index) {
        var array = []

        index = index || 0

        for (var i = index || 0; i < list.length; i++) {
            array[i - index] = list[i]
        }

        return array
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
    if( typeof window.BMAppNoticeToast === 'function' ){
        window.BMAppNoticeToast('', notice, 10000);
    } else {
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
}

/**
 * Show error popup
 */
function BBPMShowError(error, time) {
    if( typeof time === 'undefined' ) time = 10000;
    if( typeof window.BMAppErrorToast === 'function' ){
        window.BMAppErrorToast('', error, time);
    } else {
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
            'delay': time,
            'position': 'bottom right'
        });
    }
}

function BBPMOpenMiniChat(thread_id, open) {
    BPBMJQ(document).trigger("bp-better-messages-open-mini-chat", [thread_id, open]);
}

function BBPMOpenPrivateThread(user_id) {
    BPBMJQ(document).trigger("bp-better-messages-open-private-thread", [user_id]);
}

function BBPMOpenPeepsoPrivateThread(user_id) {
    BPBMJQ(document).trigger("bp-better-messages-open-private-thread", [user_id]);
}

function BPBMGetOnlineUsers(){
    return BPBMOnlineUsers;
}

function BPBMOpenUrlOrNewTab( url ){
    if(event.which === 2){
        window.open(url, 'newWindow');
        event.preventDefault();
    } else {
        location.href = url;
    }
}

