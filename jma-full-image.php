<?php
/*
Plugin Name: JMA Full Image for 7.3
Description: This plugin integrates the image selection make to fill the page pages for 7.3
Version: 1.0
Author: John Antonacci
Author URI: http://cleansupersites.com
License: GPL2
*/
if (!defined('ABSPATH')) {
    die('No direct access.');
}

require('full-image-meta.php');

function jma_use_full_image()
{
    global $post;
    $header_values = $return = false;

    if (get_post_meta(get_the_ID(), '_jma_full_image_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_full_image_data_key', true);
    }
    if (is_array($header_values)) {
        $return = $header_values['use_full_image'];
    }
    return $return;
}

function jma_use_menu()
{
    global $post;
    $header_values = $return = false;

    if (get_post_meta(get_the_ID(), '_jma_full_image_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_full_image_data_key', true);
    }
    if (is_array($header_values)) {
        $return = $header_values['big_menu'];
    }
    return $return;
}


function jma_full_image_scripts()
{
    if (jma_use_full_image()  || jma_use_menu()) {
        wp_enqueue_style('jma_full_image_css', plugins_url('/jma-full-image.css', __FILE__));
        wp_enqueue_script('jma_full_image_js', plugins_url('/jma-full-image.js', __FILE__), array( 'jquery' ), '1.0', true);

        /*global $jma_spec_options;
        if (!$jma_spec_options['not_full_width_header']) {//is full width
            $menu_has_bg = true;
            $items = $jma_spec_options['header_content'];
            foreach ($items as $item) {
                if ($item['header_element'] == 'access' && $item['remove_root_bg']) {
                    $menu_has_bg = false;
                }
            }

            //wp_add_inline_style('jma_full_image_css', $data);
        }*/
    }
}

function jma_full_image_body_cl($class)
{
    global $jma_spec_options;
    global $post;
    $class[] = 'full_image';
    if ($jma_spec_options['not_full_width_header']) {
        $class[] = $jma_spec_options['not_full_width_header'];
    } else {
        $class[] = 'expand-header';
    }
    if (get_post_meta(get_the_ID(), '_jma_full_image_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_full_image_data_key', true);
    }
    $show_amount = 0;
    if (is_array($header_values)) {
        //make sure a value is entered
        if (is_numeric($header_values['show_amount'])) {
            $show_amount = $header_values['show_amount'];
        }
        $class[] = 'jmashowamount' . $show_amount;
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
    'name'      => __('Main Background Color', 'themeblvd'),
    'desc'      => __('The color for the content background (leave blank to match page color)', 'themeblvd'),
    'id'        => 'big_main_background_color',
    'std'       => '',
    'type'      => 'color'
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

function jma_add_full_image_options()
{
    global $full_options;
    if (function_exists('themeblvd_add_option')) {
        themeblvd_add_option_section('jma_styles_header_images', 'jma_full_page_image_options', 'Full Page Image Sizes', 'Image sizes for the pages that have the big imges', $full_options);
    }
}
add_action('after_setup_theme', 'jma_add_full_image_options');

function full_image_dynamic_filter($dynamic_styles)
{
    $jma_spec_options = jma_get_theme_values();
    $main_color = $jma_spec_options['big_main_background_color']? $jma_spec_options['big_main_background_color']: $jma_spec_options['site_page_color'];
    $main_color_info = get_tint($main_color);
    $root_rgb = $main_color_info['str_split'];

    $dynamic_styles['full_image_100'] = array('.full_image_wide .site-main',
            array('position', 'relative'),
            /*array('transition', 'margin-top 0.5s'),
            array('-webkit-transition', 'margin-top 0.5s'),
            array('transition-delay', '0.5s'),
            array('-webkit-transition-delay', '0.5s'),*/
            array('margin', '0 auto'),
            array('padding', ' 0'),
            array('-moz-box-shadow', 'none'),
            array('-webkkit-box-shadow', 'none'),
            array('box-shadow', 'none'),
            array('border', 'none'),
            );
    $dynamic_styles['full_image_103'] = array('.full_image_wide .site-main',
            array('background', 'rgba(' . $root_rgb[0] . ',' . $root_rgb[1] . ',' . $root_rgb[2] . ',' . $jma_spec_options['main_trans'] . ')')
            );
    $dynamic_styles['full_image_105'] = array('.full_image_wide.constrict-header #main>*, .full_image_wide.constrict-header #custom-main',
            array('background', 'rgba(' . $root_rgb[0] . ',' . $root_rgb[1] . ',' . $root_rgb[2] . ',' . $jma_spec_options['main_trans'] . ')!important'),
            array('border-left', 'solid 1px ' . $jma_spec_options['footer_background_color']),
            array('border-right', 'solid 1px ' . $jma_spec_options['footer_background_color']),
            array('border-top', 'solid 1px ' . $jma_spec_options['footer_background_color']),
            );

    $dynamic_styles['full_image_140'] = array('.full_image_wide.constrict-header .site-header, body.full_image_wide.constrict-header #main, body.full_image_wide.constrict-header .jma-custom-wrap, .full_image_wide.constrict-header #bottom, .full_image_wide #main>*',
            array('max-width', ($jma_spec_options['site_width']+40) . 'px'),
            array('margin', '0 auto'),
            array('padding-left', '20px!important'),
            array('padding-right', '20px!important'),
            );
    $dynamic_styles['full_image_141'] = array('.full_image_wide.constrict-header #access.fix-menu',
            array('max-width', ($jma_spec_options['site_width']) . 'px'),
            array('padding-left', '0'),
            array('padding-right', '0'),
            );

    $dynamic_styles['full_image_144'] = array('.full_image_wide.constrict-header .site-header, body.full_image_wide.constrict-header .site-main',
            array('background', 'none'),
            );
    $dynamic_styles['full_image_150'] = array('.full_image_wide.constrict-header .site-header > .wrap>.jma-header-item.jma-header-content',
            array('background', $jma_spec_options['header_background_color'])
    );
    $dynamic_styles['full_image_180'] = array('.jma-local-menu li a',
    array('border', 'solid 1px ' . $jma_spec_options['footer_background_color']),
    array('background', $jma_spec_options['footer_background_color']),
    array('color', $jma_spec_options['footer_font_color']),
    );
    $dynamic_styles['full_image_190'] = array('.jma-local-menu li a:hover',
    array('color', $jma_spec_options['footer_background_color']),
    array('background', $jma_spec_options['footer_font_color']),
    );


    return $dynamic_styles;
}
add_filter('dynamic_styles_filter', 'full_image_dynamic_filter');

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

function full_image_code_size($x)
{
    $x = 'jma-full-header';
    return $x;
}

function jma_full_image_code()
{
    global $jma_spec_options;
    global $post;
    if (get_post_meta(get_the_ID(), '_jma_full_image_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_full_image_data_key', true);
    }
    //for scrolling to local menu links
    add_filter('themeblvd_section_html_id', 'jma_section_html_id', 10, 4);
    add_action('wp_enqueue_scripts', 'jma_full_image_scripts', 9999);
    if ($header_values['big_menu']) {
        $menu_hook = is_page_template('template_builder.php')? 'themeblvd_content_top': 'themeblvd_main_top';
        add_action($menu_hook, 'jma_big_local_menu');
    }

    if (jma_use_full_image()) {
        if (is_page_template('template_builder.php') && $jma_spec_options['not_full_width_header']) {
            //wrap the custom template with div .jma-custom-wrap
            add_action('themeblvd_header_after', 'jma_custom_border_top', 9999);
            add_action('themeblvd_footer_before', 'jma_custom_border_bottom', 1);
        }
        add_filter('header_image_code_size', 'full_image_code_size', 15);
        add_filter('body_class', 'jma_full_image_body_cl');
    }
}
add_action('template_redirect', 'jma_full_image_code', 1);

function jma_big_local_menu()
{
    global $post;
    if (get_post_meta(get_the_ID(), '_jma_full_image_data_key', true)) {
        $header_values =  get_post_meta(get_the_ID(), '_jma_full_image_data_key', true);
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
