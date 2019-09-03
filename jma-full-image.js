jQuery(document).ready(function($) {
    $jma_local_menu = $('.jma-local-menu');

    $jma_local_menu.find('a').on('click', function(event) {
        event.preventDefault();
        $('html, body').animate({
            scrollTop: $(this.hash).offset().top - 180
        }, 500);

    });

    $window = $(window);
    $body = $('body');
    admin_bar_height = 0;
    if ($('#wpadminbar').length) {
        admin_bar_height = $('#wpadminbar').height();
    }



    function fix_local_menu() {
        if ($('#wpadminbar').css('position') != 'fixed') {
            admin_bar_height = 0;
        }
        offset = $window.scrollTop();
        $fix_menu = $('.fix-menu');
        fix_menu_height = $fix_menu.length ? $fix_menu.height() : 0;
        menu_pos = $(".site-main").offset().top;

        if (offset > menu_pos - admin_bar_height - fix_menu_height) {
            $jma_local_menu.addClass('fix-local');
            $jma_local_menu.css('margin-top', (admin_bar_height + fix_menu_height) + 'px');
        } else {
            $jma_local_menu.removeClass('fix-local');
            $jma_local_menu.css('margin-top', '');
        }
    }

    $jma_header_image = $('.jma-header-image');

    if ($body.hasClass('full_image')) {
        //none of these values are effected by window size
        image_width = $jma_header_image.data('image_width');
        image_height = $jma_header_image.data('image_height');
        image_ratio = image_height / image_width;

        //how far from bottom of screen is top of page
        main_showing_by = 100;
        classes = $body.attr('class').split(' ');
        var i;
        for (i = 0; i < classes.length; ++i) {
            if (classes[i].match("^jmashowamount")) {
                main_showing_by = parseInt(classes[i].replace("jmashowamount", ""), 10);
            }
        }
    }

    //deal with the slider
    function fix_slider() {
        if ($('#wpadminbar').css('position') != 'fixed') {
            admin_bar_height = 0;
        }
        sl_offset = $window.scrollTop();
        $top = $('#top');

        top_height = 0;
        $top.find('#branding > .wrap').children().each(function() {
            $this = $(this);
            //if we get to one of these we are done
            if (!$this.hasClass('image') && $this.css('position') != 'absolute') {
                //fix-menu doesnt count, but we dont want to escape
                if (!$this.hasClass('fix-menu')) {
                    top_height += $this.outerHeight();
                }
            } else {
                return false;
            }
        });
        window_width = $window.width();
        window_height = $window.height();
        available_height = $body.hasClass('constrict-header') ? window_height - admin_bar_height : window_height - top_height - admin_bar_height - main_showing_by;
        $body.attr('data-available_height', window_height - top_height - admin_bar_height - main_showing_by);
        //only 991px and wider
        if ($('#dont-edit-this-element').css('z-index') == 20) {
            $body.addClass('full_image_wide');
            $body.removeClass('full_image_narrow');
            //jma-header-item wraps around jma-header-image
            $jma_header_image_item = $('.jma-header-item.image');
            //if there is a header element after image it becomes "$site_main"
            $site_main = $jma_header_image_item.next().hasClass('mobile-nav') ? $('.site-main') : $jma_header_image_item.next();
            available_ratio = available_height / window_width;

            if ($body.hasClass('constrict-header')) {
                $jma_header_image_item.css({
                    'top': admin_bar_height + 'px',
                    'height': available_height + 'px'
                });
                $site_main.css({
                    'margin-top': (window_height - top_height - admin_bar_height - main_showing_by) + 'px'
                });
                $('.soliloquy-caption').css('margin-top', ((top_height - main_showing_by) / 2) + 'px');
            } else {
                im_pos = $jma_header_image_item.prev().offset().top + $jma_header_image_item.prev().outerHeight();

                if (sl_offset > im_pos - admin_bar_height) {
                    $site_main.css('margin-top', (window_height - top_height - admin_bar_height - main_showing_by) + 'px');
                    $jma_header_image_item.addClass('fix-image');
                    $jma_header_image_item.css({
                        'top': admin_bar_height + 'px',
                        'height': available_height + 'px'
                    });
                } else {
                    $jma_header_image_item.removeClass('fix-image');
                    $jma_header_image_item.css('top', '');
                    $site_main.css('margin-top', '');
                }
                $jma_header_image_item.css({
                    'height': available_height + 'px'
                });
            }
            //cut off image left and right use all of height
            if (image_ratio < available_ratio) {
                $jma_header_image.css({
                    'width': (available_height * (1 / image_ratio)) + 'px',
                    'max-width': (available_height * (1 / image_ratio)) + 'px'
                });
            } else //cut off image top and bottom use all of width
            {
                $jma_header_image.css({
                    'width': window_width + 'px',
                    'max-width': ''
                });
            }
        } else {
            $body.removeClass('full_image_wide');
            $body.addClass('full_image_narrow');
            $('.image.jma-header-content').css('height', '');
            $site_main.css('margin-top', '');
            $jma_header_image.css({
                'top': '',
                'height': ''
            });
            $jma_header_image.css({
                'width': '',
                'max-width': ''
            });
        }
    }

    $window.scroll(function() {
        if ($jma_local_menu.length) {
            fix_local_menu();
        }
        fix_slider();
    });

    $window.load(function() {
        if ($jma_local_menu.length) {
            fix_local_menu();
        }
        $.when(fix_slider()).then(function() {
            $jma_header_image.css('opacity', 1);
        });
    });

    $window.resize(function() {
        fix_slider();
    });

});