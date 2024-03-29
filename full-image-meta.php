<?php
/*
 * Adds a box to the right column on the Post and Page edit screens.
 */

if (!defined('ABSPATH')) {
    die('No direct access.');
}
function jma_full_image_input_box()
{
    $screens = array('post', 'page', 'portfolio_item');
    $screens = apply_filters('jma_full_image_input_screens_filter', $screens);
    foreach ($screens as $screen) {
        add_meta_box(
                'jma_full_image_input_section',
                __('Big Header', 'jma_textdomain'),
                'jma_full_image_box',
                $screen,
                'side',
                'high'
            );
    }
}
add_action('add_meta_boxes', 'jma_full_image_input_box');

/*
 * Prints the box content
 *
 * @param WP_Post $post The object for the current post/page.
 */

    function jma_full_image_box($post)
    {
        // Add an nonce field so we can check for it later.
        wp_nonce_field('jma_full_image_box', 'jma_full_image_box_nonce');

        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $full_image_values = array('show_amount' => '', 'use_full_image' => '');
        if (get_post_meta($post->ID, '_jma_full_image_data_key', true)) {
            $full_image_values = get_post_meta($post->ID, '_jma_full_image_data_key', true);
        }

        echo '<p></p>';

        echo '<label for="use_full_image">';
        _e('Use Big Header.', 'jma_textdomain');
        echo '</label><br/><br/> ';
        $checked = $full_image_values['use_full_image'];
        $current = 1;
        echo '<input type="checkbox" name="use_full_image" value="1"' . checked($checked, $current, false) . '/><br/>';
        echo '<hr/>';

        echo '<label for="show_amount">';
        _e('Show Amount.', 'jma_textdomain');
        echo '</label><br/><br/> ';
        $full_image_value = is_numeric($full_image_values['show_amount'])? $full_image_values['show_amount']: 0;
        echo '<input type="text" id="show_amount" name="show_amount" value="'. $full_image_value . '"><br><br/>';

        $menus = get_terms('nav_menu');

        echo '<label for="big_menu">';
        _e('Pick a menu to display', 'jma_textdomain');
        echo '</label><br/><br/> ';
        echo '<select name="big_menu">';
        echo '<option value=""'.selected($full_image_values['big_menu'], '').'>Select...</option>';
        foreach ($menus as $menu) {
            echo '<option value="'.$menu->slug.'"'.selected($full_image_values['big_menu'], $menu->slug).'>'.$menu->name.'</option>';
        }
        echo '</select><br/><br/>';
    }
/*
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */

    function jma_save_full_image_postdata($post_id)
    {
        /*
         * We need to verify this came from the our screen and with proper authorization,
         * because save_post can be triggered at other times.
         */

        // Check if our nonce is set.
        if (!isset($_POST['jma_full_image_box_nonce'])) {
            return $post_id;
        }

        $nonce = $_POST['jma_full_image_box_nonce'];

        // Verify that the nonce is valid.
        if (!wp_verify_nonce($nonce, 'jma_full_image_box')) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }

        // Check the user's permissions.
        if ('page' === $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }

        /* OK, its safe for us to save the data now. */

        // Sanitize user input.
        $jma_data['show_amount'] = sanitize_text_field($_POST['show_amount']);
        $jma_data['use_full_image'] = sanitize_text_field($_POST['use_full_image']);
        $jma_data['big_menu'] = sanitize_text_field($_POST['big_menu']);

        // Update the meta field in the database.
        update_post_meta($post_id, '_jma_full_image_data_key', $jma_data);
    }
add_action('save_post', 'jma_save_full_image_postdata');
add_action('after_switch_theme', 'jma_save_full_image_postdata');
