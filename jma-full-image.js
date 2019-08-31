jQuery(document).ready(function($) {
    $site_main = $('.jma-header-item.image').next();

    $('.jma-local-menu > li > a').on('click', function(event) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: $(this.hash).offset().top - 180
        }, 500);

    });

    $window = $(window);
    $body = $('body');
    $top = $('#top');
    $jma_header_image = $('.jma-header-image');

    function fix_slider() {
        $jma_header_image = $('.jma-header-image');
        window_width = $window.width();
        window_height = $window.height();
        admin_bar_scroll_height = admin_bar_height = 0;
        if ($('#wpadminbar').length) {
            admin_bar_scroll_height = admin_bar_height = $('#wpadminbar').height();
            if ($('#wpadminbar').css('position') != 'fixed') {
                admin_bar_scroll_height = 0;
            }
        }
        available_top_height = 0;
        $top.find('#branding > .wrap').children().each(function() {
            $this = $(this);
            if (!$this.is('ul')) {
                if ($this.hasClass('fix-menu')) {
                    //if fix-menu us present, it is alone one screen
                    available_top_height = $this.outerHeight();
                    return false;
                } else {
                    //add others up at load (and thereafter provided on fix-menu)
                    available_top_height += $this.outerHeight();
                }
            }
        });

        top_height = $top.height();
        image_width = $jma_header_image.data('image_width');
        image_height = $jma_header_image.data('image_height');
        image_ratio = image_height / image_width;
        scroll_top_height = $('#access').css('position') != 'fixed' ? admin_bar_scroll_height : admin_bar_scroll_height + $('#access').height();

        //how far from bottom of screen is top of page
        main_showing_by = 100;
        classes = $body.attr('class').split(' ');
        var i;
        for (i = 0; i < classes.length; ++i) {
            if (classes[i].match("^jmashowamount")) {
                get_main_showing_by = parseInt(classes[i].replace("jmashowamount", ""), 10);
            }
        }
        main_showing_by = $("#dont-edit-this-element").css("z-index") == 20 ? get_main_showing_by : window_height - (top_height + admin_bar_height + window_width * image_ratio);


        available_height = $body.hasClass('constrict-header') ? window_height : window_height - available_top_height - admin_bar_height - main_showing_by;
        offset = $window.scrollTop();
        $body.attr('data-available_height', available_height);

        //fix the page top (local) menu
        $jma_local_menu = $('.jma-local-menu');
        offset_top = window_height - scroll_top_height - main_showing_by;
        //if the image has overlays that go relative on small screens
        if ($('.jma-header-item.image').children('.wrap').css('position') == 'relative') {
            offset_top += $('.jma-header-item.image').children('.wrap').height();
        }
        if ($('#container > #full-page-title').length) {
            offset_top += $('#full-page-title').height();
        }

        margin_top = $body.hasClass('constrict-header') ? admin_bar_height : admin_bar_height + available_top_height;

        if (offset > offset_top) {
            $jma_local_menu.addClass('fix-local');
            $jma_local_menu.css('margin-top', scroll_top_height + 'px');
        } else {
            $jma_local_menu.removeClass('fix-local');
            $jma_local_menu.css('margin-top', '');
        }
        //deal with the slider

        $image_elements = $('.jma-header-item.image.logo.image.jma-header-content.header-content, .jma-header-item.image.jma-header-content.sidebar.header-content');
        //only 991px and wider
        if ($('#dont-edit-this-element').css('z-index') == 20) {
            $body.addClass('big_slider_wide');
            $body.removeClass('big_slider_narrow');
            $jma_header_image = $('.jma-header-image');
            available_ratio = available_height / window_width;

            $site_main.css({ //trailing 1 is for 1px of overlap site-main onto image
                'margin-top': (window_height - admin_bar_height - main_showing_by - 1) + 'px'
            });

            $top.css('top', admin_bar_height + 'px'); /**/
            $jma_header_image.css({
                'top': margin_top + 'px',
                'height': available_height + 'px'
            });
            $image_elements.css({
                'top': margin_top + 'px'
            });
            //cut off image left and right use all of height
            if (image_ratio < available_ratio) {
                $('.jma-header-image-wrap').css({
                    'width': (available_height * (1 / image_ratio)) + 'px',
                    'max-width': (available_height * (1 / image_ratio)) + 'px'
                });
            } else //cut off image top and bottom use all of width
            {
                $('.jma-header-image-wrap').css({
                    'width': window_width + 'px',
                    'max-width': ''
                });
            }
        } else {
            $body.removeClass('big_slider_wide');
            $body.addClass('big_slider_narrow');
            $('.image.jma-header-content').css('height', '');
            $site_main.css('margin-top', '');
            $top.css('top', '');
            $('html').css('background', '');

            $image_elements.css({
                'top': ''
            });
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

    $window.scroll(function() {
        fix_slider();
    });

    $window.load(function() {
        fix_slider();
    });

    $window.bind('bigslresizeEnd', function() {
        //do something, window hasn't changed size in 1000ms
        fix_slider();
    });

    $window.resize(function() {
        if (this.bigslresizeTO) clearTimeout(this.bigslresizeTO);
        this.bigslresizeTO = setTimeout(function() {
            $(this).trigger('bigslresizeEnd');
        }, 1000);
    });

    $window.resize(function() {
        fix_slider();
    });

});