jQuery(document).ready(function($) {
    $site_main = $('.site-main');

    $site_main.on('click', '#menu-home > li > a', function(event) {

        event.preventDefault();

        $('html, body').animate({
            scrollTop: $(this.hash).offset().top - 180
        }, 400);

    });

    $window = $(window);
    $body = $('body');
    $top = $('#top');
    $jma_header_image = $('.jma-header-image');

    function fix_slider() {
        window_width = $window.width();
        if (window_width > 992) {
            main_showing_by = 100;
            classes = $body.attr('class').split(' ');
            var i;
            for (i = 0; i < classes.length; ++i) {
                if (classes[i].match("^jmashowamount")) {
                    get_main_showing_by = classes[i].replace("jmashowamount", "");
                }

            }
            main_showing_by = parseInt(get_main_showing_by, 10);
            console.log(main_showing_by);
            $body.addClass('big_slider_wide');
            $body.removeClass('big_slider_narrow');
            admin_bar_height = $('#wpadminbar').length ? $('#wpadminbar').height() : 0;
            top_add = $body.hasClass('constrict-header') ? 0 : $top.height();
            top_height = admin_bar_height + top_add;
            $jma_header_image = $('.jma-header-image');
            image_width = $jma_header_image.data('image_width');
            image_height = $jma_header_image.data('image_height'); //console.log(image_width);
            image_ratio = image_height / image_width;

            window_height = $window.height();
            available_height = $body.hasClass('constrict-header') ? window_height : window_height - top_height - main_showing_by;
            available_ratio = available_height / window_width;

            if ($body.hasClass('center-vert'))
                $site_main.css({
                    'margin-top': (((window_height - $site_main.height()) / 2) + $top.height() / 2) + 'px'
                });
            else
                $site_main.css({
                    'margin-top': (window_height - admin_bar_height - main_showing_by) + 'px'
                });

            $top.css('top', admin_bar_height + 'px');
            $jma_header_image.css({
                'top': top_height + 'px',
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
                    'max-width': image_width + 'px'
                });
            }
            offset = $body.hasClass('constrict-header') ? $window.scrollTop() : $window.scrollTop() - main_showing_by - admin_bar_height;
            header_adjustment = $body.hasClass('constrict-header') ? $top.height() + main_showing_by : main_showing_by;
            $jma_local_menu = $('.jma-local-menu');
            if (offset > available_height - header_adjustment - admin_bar_height) {
                $jma_local_menu.addClass('fix-local');
                $jma_local_menu.css('margin-top', (top_height + header_adjustment - main_showing_by) + 'px');
            } else {
                $jma_local_menu.removeClass('fix-local');
                $jma_local_menu.css('margin-top', '');
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




    function fix_nav() {
        window_width = $window.width();
        if (window_width > 992) {
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
        var myVar = setInterval(checknav, 100);
        i = 0;

        function checknav() {
            i++;
            if ($('.nivo-directionNav').length || i > 10) {
                fix_nav();
                stopchecknav();
            }
        }

        function stopchecknav() {
            clearInterval(myVar);
        }

    });

    $window.resize(function() {
        fix_slider();
        fix_nav();
    });

});