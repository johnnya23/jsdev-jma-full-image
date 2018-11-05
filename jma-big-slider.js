jQuery(document).ready(function($) {

    $('.site-main').on('click', '#menu-home > li > a', function(event) {

        event.preventDefault();

        $('html, body').animate({
            scrollTop: $(this.hash).offset().top - 200
        }, 1000);

    });

    $window = $(window);

    var fix_slider = function() {
        var $window = $(window);
        $window_width = $window.width();
        if ($window_width > 992) {
            $('body').addClass('big_slider_wide');
            $('body').removeClass('big_slider_narrow');
            var $admin_bar_height = $('#wpadminbar').length ? $('#wpadminbar').height() : 0;
            $top_add = $('body').hasClass('constrict-header') ? 0 : $('#top').height();
            $top_height = $admin_bar_height + $top_add;

            $image_width = $('.jma-header-image').data('image_width');
            $image_height = $('.jma-header-image').data('image_height'); //console.log($image_width);
            $image_ratio = $image_height / $image_width;

            $window_height = $window.height();
            $available_height = $('body').hasClass('constrict-header') ? $window_height : $window_height - $top_height - 100;
            $available_ratio = $available_height / $window_width;
            main_showing_by = 100;

            if ($('body').hasClass('center-vert'))
                $('.site-main').css({
                    'margin-top': ((($window_height - $('.site-main').height()) / 2) + $('#top').height() / 2) + 'px'
                });
            else
                $('.site-main').css({
                    'margin-top': ($window_height - $admin_bar_height - main_showing_by) + 'px'
                });

            $('#top').css('top', $admin_bar_height + 'px');
            $('.jma-header-image').css({
                'top': $top_height + 'px',
                'height': $available_height + 'px'
            });
            $('.jma-header-image .nivo-directionNav').css('width', $window_width + 'px');
            if ($image_ratio < $available_ratio) {
                $('.jma-header-image-wrap').css('width', ($available_height * (1 / $image_ratio)) + 'px');
            } else {
                $('.jma-header-image-wrap').css('width', $image_width + 'px');
            }
            var offset = $('body').hasClass('constrict-header') ? $window.scrollTop() : $window.scrollTop() - main_showing_by - $admin_bar_height;
            header_adjustment = $('body').hasClass('constrict-header') ? $('#top').height() + main_showing_by : main_showing_by;
            if (offset > $available_height - header_adjustment - $admin_bar_height) {
                $('.jma-local-menu').addClass('fix-local');
                $('.jma-local-menu').css('margin-top', ($top_height + header_adjustment - main_showing_by) + 'px');
            } else {
                $('.jma-local-menu').removeClass('fix-local');
                $('.jma-local-menu').css('margin-top', '');
            }
        } else {
            $('body').removeClass('big_slider_wide');
            $('body').removeClass('tablet-on');
            $('body').addClass('big_slider_narrow');
            $('.image.jma-header-content').css('height', '');
            $('.site-main').css('margin-top', '');
            $('#top').css('top', '');
            $('.jma-header-image').css({
                'width': '',
                'top': '',
                'height': ''
            });
            $('.jma-header-image .nivo-directionNav').css('width', '');
        }
    };


    if ($('body').is('.big_slider')) {
        $(window).scroll(fix_slider);
        $(window).load(fix_slider);
        $(window).resize(fix_slider);
    }

});