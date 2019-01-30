jQuery(document).ready(function($) {
    $site_main = $('.site-main');

    $site_main.on('click', '#menu-home > li > a', function(event) {

        event.preventDefault();

        $('html, body').animate({
            scrollTop: $(this.hash).offset().top - 180
        }, 500);

    });

    $window = $(window);
    $body = $('body');
    $top = $('#top');
    $jma_header_image = $('.jma-header-image');
    $old_im_loc = $('#top').find('.jma-header-item.image');

    function fix_slider() {
        window_width = $window.width();
        window_height = $window.height();
        admin_bar_scroll_height = admin_bar_height = 0;
        if ($('#wpadminbar').length) {
            admin_bar_scroll_height = admin_bar_height = $('#wpadminbar').height();
            if ($('#wpadminbar').css('position') != 'fixed') {
                admin_bar_scroll_height = 0;
            }
        }
        //admin_bar_scroll_height = $('#wpadminbar').length && $('#wpadminbar').css('position') == 'fixed' ? $('#wpadminbar').height() : 0;
        //admin_bar_height = $('#wpadminbar').length ? $('#wpadminbar').height() : 0;
        top_height = 0;
        $('.jma-header-item.image').prevAll().each(function() {
            top_height += $(this).height();
        });
        image_width = $jma_header_image.data('image_width');
        image_height = $jma_header_image.data('image_height');
        image_ratio = image_height / image_width;
        scroll_top_height = $('#top').css('position') != 'fixed' ? admin_bar_scroll_height : admin_bar_scroll_height + top_height;
        //how far from bottom of screen is top of page
        main_showing_by = 100;
        classes = $body.attr('class').split(' ');
        var i;
        var get_main_showing_by = 0;
        for (i = 0; i < classes.length; ++i) {
            if (classes[i].match("^jmashowamount")) {
                get_main_showing_by = classes[i].replace("jmashowamount", "");
            }

        }

        main_showing_by = $(".copyright").css("margin-bottom") == "5px" ? parseInt(get_main_showing_by, 10) : window_height - (top_height + admin_bar_height + window_width * image_ratio);


        available_height = $body.hasClass('constrict-header') ? window_height : window_height - top_height - admin_bar_height - main_showing_by;
        offset = $window.scrollTop();

        //fix the page top (local) menu
        $jma_local_menu = $('.jma-local-menu');
        offset_top = window_height - scroll_top_height - main_showing_by;
        margin_top = $body.hasClass('constrict-header') ? admin_bar_height : admin_bar_height + top_height;

        if (offset > offset_top) {
            $jma_local_menu.addClass('fix-local');
            $jma_local_menu.css('margin-top', scroll_top_height + 'px');
        } else {
            $jma_local_menu.removeClass('fix-local');
            $jma_local_menu.css('margin-top', '');
        }
        //deal with the slider
        if ($(".copyright").css("margin-bottom") == "5px") {
            main_showing_by = parseInt(get_main_showing_by, 10);
            $body.addClass('big_slider_wide');
            $body.removeClass('big_slider_narrow');

            available_ratio = available_height / window_width;

            if ($body.hasClass('center-vert'))
                $site_main.css({
                    'margin-top': (((window_height - $site_main.height()) / 2) + $top.height() / 2) + 'px'
                });
            else
                $site_main.css({ //trailing 1 is for 1px of overlap site-main onto image
                    'margin-top': (window_height - admin_bar_height - main_showing_by - 1) + 'px'
                });

            $top.css('top', admin_bar_height + 'px');
            console.log(available_height);
            $jma_header_image.css({
                //'top': margin_top + 'px',
                'height': available_height + 'px'
            });
            if (image_ratio < available_ratio) {
                $('.jma-header-image-wrap').css({
                    'width': (available_height * (1 / image_ratio)) + 'px',
                    'max-width': (available_height * (1 / image_ratio)) + 'px'
                });
            } else {
                $('.jma-header-image-wrap').css({
                    'width': image_width + 'px',
                    'max-width': '100%'
                });
            }
        } else {
            $body.removeClass('big_slider_wide');
            $body.addClass('big_slider_narrow');
            $('.image.jma-header-content').css('height', '');
            $site_main.css('margin-top', '');
            $top.css('top', '');
            $jma_header_image.css({
                'top': '',
                'height': ''
            });
            $('.jma-header-image-wrap').css({
                'width': '',
                'max-width': ''
            });
        }
    }




    function fix_slider_nav() {
        window_width = $window.width();
        if (($(".copyright").css("margin-bottom") == "5px")) {
            $jma_header_image.find('.nivo-directionNav').css({
                'width': window_width + 'px'
            });
        } else {
            $jma_header_image.find('.nivo-directionNav').css('width', '');
        }
    }



    $window.scroll(function() {
        fix_slider();
    });

    $window.load(function() {
        fix_slider();
    });

    function handleCanvas(canvas) {
        //do stuff here
        fix_slider_nav();
    }

    // set up the mutation observer
    var observer = new MutationObserver(function(mutations, bigheaderme) {
        // `mutations` is an array of mutations that occurred
        // `me` is the MutationObserver instance
        var canvas = $('.nivo-directionNav').length;
        if (canvas) {
            handleCanvas(canvas);
            bigheaderme.disconnect(); // stop observing
            return;
        }
    });

    // start observing
    observer.observe(document, {
        childList: true,
        subtree: true
    });

    $window.resize(function() {
        fix_slider();
        fix_slider_nav();
    });

});