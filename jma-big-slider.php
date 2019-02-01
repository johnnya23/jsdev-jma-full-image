<?php
/*
Plugin Name: JMA Big Meta Slider for 7.2
Description: This plugin intrages the meta slider make a full size slider on selected pages for 7.2
Version: 1.1
Author: John Antonacci
Author URI: http://cleansupersites.com
License: GPL2
*/

require('custom-meta.php');

function use_big_slider()
{
    global $post;
    $header_values = $return = false;

    if (get_post_meta(get_the_ID(), '_jma_big_header_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_big_header_data_key', true);
    }
    if (is_array($header_values)) {
        $return = $header_values['use_big_header'];
    }
    return $return;
}


function big_slider_scripts()
{
    wp_enqueue_style('jma_big_slider_css', plugins_url('/jma-big-slider.css', __FILE__));
    wp_enqueue_script('jma_big_slider_js', plugins_url('/jma-big-slider.js', __FILE__), array( 'jquery', 'adjust_site_js' ));
}
function jma_big_sl_body_cl($class)
{
    global $jma_spec_options;
    global $post;

    if (!$jma_spec_options['not_full_width_header']) {//is full width
        $menu_has_bg = true;
        $items = $jma_spec_options['header_content'];
        foreach ($items as $item) {
            if ($item['header_element'] == 'access' && $item['remove_root_bg']) {
                $menu_has_bg = false;
            }
        }
        //header_background_color
        $html_bg = $menu_has_bg? $jma_spec_options['menu_background_color']: $jma_spec_options['header_background_color'];
        $class[] = 'htmlbg' . $html_bg;
    }
    $class[] = 'big_slider';
    $class[] = $jma_spec_options['center_main_vert'];
    $class[] = $jma_spec_options['not_full_width_header'];
    if (get_post_meta(get_the_ID(), '_jma_big_header_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_big_header_data_key', true);
    }
    if (is_array($header_values)) {
        $class[] = 'jmashowamount' . $header_values['show_amount'];
    }

    return $class;
}

$full_options = array(
    array(
    'name' 		=> 'Full Page Image Width',
    'desc' 		=> 'Width of header image in px (don\'t add unit abbreviation)',
    'id' 		=> 'header_full_page_width',
    'std'		=> '2000',
    'type' 		=> 'text'
    ),
    array(
    'name' 		=> 'Full Page Image Height',
    'desc' 		=> 'Height of header image in px (don\'t add unit abbreviation)',
    'id' 		=> 'header_full_page_height',
    'std'		=> '800',
    'type' 		=> 'text'
    ),
    array(
    'name' 		=> 'Main Width',
    'desc' 		=> 'the width of the content on the big image page (leave blank to match site width)',
    'id' 		=> 'big_main_width',
    'std'		=> '',
    'type' 		=> 'text'
    ),
    array(
    'name'      => __('Main Background Color', 'themeblvd'),
    'desc'      => __('The color for the content background (leave blank to match page color)', 'themeblvd'),
    'id'        => 'big_main_background_color',
    'std'       => '',
    'type'      => 'color'
    ),
    array(
    'name' 		=> 'Center Content',
    'desc' 		=> 'Center the page content vertically?',
    'id' 		=> 'center_main_vert',
    'std'		=> '',
    'type' 		=> 'select',
    'options'   => array(
        '' => 'No, keep content at bottom',
        'center-vert' => 'Yes'
        )
    ),
    array(
    'name' 		=> 'Full Width Header',
    'desc' 		=> 'Full width header or just as wide as the menu',
    'id' 		=> 'not_full_width_header',
    'std'		=> '',
    'type' 		=> 'select',
    'options'   => array(
        '' => 'Full Width',
        'constrict-header' => 'As wide as menu'
        )
    ),
    array(
    'name' 		=> 'Main Transparency',
    'desc' 		=> '1 for not transparent 0 for invisible (or in bewteen)',
    'id' 		=> 'main_trans',
    'std'		=> '0.9',
    'type' 		=> 'text',
    )
);
function jma_add_bg_sl__options()
{
    global $full_options;
    if (function_exists('themeblvd_add_option')) {
        themeblvd_add_option_section('jma_styles_header_images', 'jma_full_page_image_options', 'Full Page Image Sizes', 'Image sizes for the pages that have the big imges', $full_options);
    }
}
add_action('after_setup_theme', 'jma_add_bg_sl__options');

function bg_sl_dynamic_filter($dynamic_styles)
{
    $jma_spec_options = jma_get_theme_values();
    $main_color = $jma_spec_options['big_main_background_color']? $jma_spec_options['big_main_background_color']: $jma_spec_options['site_page_color'];
    $main_color_info = get_tint($main_color);
    $root_rgb = $main_color_info['str_split'];
    $big_main_width = '100%';
    $big_main_padding = ' 0';
    if ($jma_spec_options['not_full_width_header']) {
        $big_main_width = $jma_spec_options['big_main_width']? ($jma_spec_options['big_main_width'])  . 'px': ($jma_spec_options['site_width']-2) . 'px';
        $big_main_padding = '0 20px';
    }
    $dynamic_styles['bg_sl_100'] = array('.big_slider_wide .site-main',
            array('position', 'relative'),
            array('transition', 'margin-top 0.5s'),
            array('-webkit-transition', 'margin-top 0.5s'),
            array('transition-delay', '1s'),
            array('-webkit-transition-delay', '1s'),
            array('max-width', $big_main_width),
            array('margin', '0 auto'),
            array('padding', ' 0'),
            array('-moz-box-shadow', 'none'),
            array('-webkkit-box-shadow', 'none'),
            array('box-shadow', 'none'),
            array('border', 'none'),
            );
    $dynamic_styles['bg_sl_105'] = array('.big_slider_wide #custom-main',
            array('background', 'rgba(' . $root_rgb[0] . ',' . $root_rgb[1] . ',' . $root_rgb[2] . ',' . $jma_spec_options['main_trans'] . ')')
            );
    $dynamic_styles['bg_sl_115'] = array('.big_slider_wide.constrict-header .site-main > .wrap',
            array('border', 'solid 1px ' . $jma_spec_options['footer_background_color']),
            array('padding', '20px 0'),
            array('background', 'rgba(' . $root_rgb[0] . ',' . $root_rgb[1] . ',' . $root_rgb[2] . ',' . $jma_spec_options['main_trans'] . ')')
            );
    $dynamic_styles['bg_sl_117'] = array('.big_slider_wide #main',
            array('background', 'rgba(' . $root_rgb[0] . ',' . $root_rgb[1] . ',' . $root_rgb[2] . ',' . $jma_spec_options['main_trans'] . ')'),
            array('padding', '20px 0'),
            );
    $dynamic_styles['bg_sl_120'] = array('.big_slider_wide .site-main > .wrap #sidebar_layout',
            array('padding', '0 20px'),
            );
    if ($jma_spec_options['border_shadow'] == 'on') {
        $dynamic_styles['bg_sl_130'] = array('.big_slider_wide.constrict-header .site-header > .wrap, .big_slider_wide.constrict-header #main > .wrap, .big_slider_wide.constrict-header #custom-main, .big_slider_wide.constrict-header .site-footer',
                    array('-webkkit-box-shadow', '0 2px 10px ' . $jma_spec_options['footer_background_color']),
                    array('box-shadow', '0 2px 10px ' . $jma_spec_options['footer_background_color']),
                    );
    }

    $dynamic_styles['bg_sl_140'] = array('.big_slider_wide.constrict-header .site-header, body.big_slider_wide.constrict-header #main, body.big_slider_wide #main > .wrap, .big_slider_wide.constrict-header #bottom',
            array('max-width', ($jma_spec_options['site_width']+40) . 'px'),
            array('margin', '0 auto'),
            array('padding-left', '20px'),
            array('padding-right', '20px'),
            );
    $dynamic_styles['bg_sl_141'] = array('.big_slider_wide.constrict-header #access.fix-menu',
            array('max-width', ($jma_spec_options['site_width']) . 'px'),
            array('padding-left', '0'),
            array('padding-right', '0'),
            );
    $dynamic_styles['bg_sl_142'] = array('.big_slider_wide.constrict-header #access.fix-menu > .wrap',
                array('-webkkit-box-shadow', '0 2px 10px ' . $jma_spec_options['footer_background_color']),
                array('box-shadow', '0 2px 10px ' . $jma_spec_options['footer_background_color']),
                );

    $dynamic_styles['bg_sl_144'] = array('.big_slider_wide.constrict-header .site-header, body.big_slider_wide.constrict-header .site-main',
                    array('background', 'none'),
                    );
    $dynamic_styles['bg_sl_150'] = array('.big_slider_wide.constrict-header .site-header > .wrap',
            array('background', $jma_spec_options['header_background_color'])
    );
    if ($jma_spec_options['body_shape'] == 'stretch') {
        $dynamic_styles['bg_sl_160'] = array('.big_slider_narrow .site-inner > .wrap > #full-page-title',
            array('display', 'none'),
            );
    }
    $dynamic_styles['bg_sl_180'] = array('.jma-local-menu li a',
    array('border', 'solid 1px ' . $jma_spec_options['footer_background_color']),
    array('background', $jma_spec_options['footer_background_color']),
    array('color', $jma_spec_options['footer_font_color']),
    );
    $dynamic_styles['bg_sl_190'] = array('.jma-local-menu li a:hover',
    array('color', $jma_spec_options['footer_background_color']),
    array('background', $jma_spec_options['footer_font_color']),
    );


    return $dynamic_styles;
}
add_filter('dynamic_styles_filter', 'bg_sl_dynamic_filter');

function jma_full_image_sizes($sizes)
{
    global $jma_spec_options;

    // image size for header slider
    $sizes['jma-full-header']['name'] = 'Full Header';
    $sizes['jma-full-header']['width'] = isset($jma_spec_options['header_full_page_width'])? $jma_spec_options['header_full_page_width']: 2000;
    $sizes['jma-full-header']['height'] = isset($jma_spec_options['header_full_page_height'])? $jma_spec_options['header_full_page_height']: 800;
    $sizes['jma-full-header']['crop'] = true;
    return $sizes;
}
add_filter('themeblvd_image_sizes', 'jma_full_image_sizes');

function use_full_image($x)
{
    $x = 'jma-full-header';
    return $x;
}

function big_slder_code()
{
    global $jma_spec_options;
    global $post;
    if (get_post_meta(get_the_ID(), '_jma_big_header_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_big_header_data_key', true);
    }
    if (use_big_slider()) {
        if ($header_values['big_menu']) {
            $menu_hook = is_page_template('template_builder.php')? 'themeblvd_content_top': 'themeblvd_main_top';
            add_action($menu_hook, 'jma_big_local_menu');
            add_filter('themeblvd_section_html_id', 'jma_section_html_id', 10, 4);
            add_action('wp_enqueue_scripts', 'jma_scroll_scripts');
        }
        if (is_page_template('template_builder.php') && $jma_spec_options['not_full_width_header']) {
            //wrap the custom template with div .jma-custom-wrap
            add_action('themeblvd_header_after', 'jma_custom_border_top', 9999);
            add_action('themeblvd_footer_before', 'jma_custom_border_bottom', 1);
        }
        add_filter('header_image_code_size', 'use_full_image', 15);
        add_action('wp_enqueue_scripts', 'big_slider_scripts', PHP_INT_MAX);
        add_filter('body_class', 'jma_big_sl_body_cl');
        remove_action('jma_header_image', 'jma_header_image_html');
        add_action('themeblvd_header_after', 'jma_header_image_html');
        if ($jma_spec_options['body_shape'] == 'stretch') {
            add_action('themeblvd_main_top', 'jma_add_title', 999);
        }
    }
}
add_action('template_redirect', 'big_slder_code', 999);

function jma_big_local_menu()
{
    global $post;
    if (get_post_meta(get_the_ID(), '_jma_big_header_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_big_header_data_key', true);
    }
    if (is_array($header_values)) {
        $menuslug = $header_values['big_menu'];
    }
    echo wp_nav_menu(array( 'menu' => $menuslug, 'menu_class' => 'jma-local-menu clearfix', 'container' => null ));
}

/**
 * Filter HTML ID's for sections in custom layout to
 * use labels set in Builder user interface.
 */
function jma_section_html_id($section_id, $layout_name, $layout_id, $data)
{
    $section_id = $data['label'];

    return $section_id;
}

function jma_scroll_scripts()
{
    wp_enqueue_script(
        'jma_scroll_script',
        esc_url(get_stylesheet_directory_uri() . '/theme.js'),
        array( 'themeblvd' )
    );
}
