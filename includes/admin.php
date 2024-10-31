<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
require_once plugin_dir_path(__FILE__) . 'view.php';
function sempai_breadcrumbs_save_meta_box($post_id)
{
    if (array_key_exists('sempai_breadcrumbs_fields', $_POST)) {
        if ( ! current_user_can('edit_pages') ) {
            return;
        }

        if ( ! isset( $_POST['sempai_breadcrumbs_nonce'] ) || ! wp_verify_nonce( sanitize_text_field(wp_unslash($_POST['sempai_breadcrumbs_nonce'])), 'sempai_save_breadcrumbs' ) ) {
            wp_die( 'Nieautoryzowane działanie. Nonce niepoprawny.', 'textdomain' );
        }

        $fields = array();
        if (isset($_POST['sempai_breadcrumbs_fields']['page'])) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Dane zostaną zsanityzowane później
            $breadcrumbs_fields = wp_unslash( $_POST['sempai_breadcrumbs_fields'] );
            foreach ($breadcrumbs_fields['page'] as $key => $page) {
                $field = array(
                    'page' => sanitize_text_field($page)
                );

                if (isset($_POST['sempai_breadcrumbs_fields']['name'][$key])) {
                    $field['name'] = sanitize_text_field(wp_unslash($_POST['sempai_breadcrumbs_fields']['name'][$key]));
                }

                $fields[] = $field;
            }
        }
        update_post_meta($post_id, '_breadcrumbs_meta_key', $fields);

        global $wpdb;
        $table_name = $wpdb->prefix . 'sempai_custom_breadcrumbs';

        wp_cache_delete('sempai_custom_breadcrumbs_' . $post_id, 'sempai_custom_breadcrumbs');

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $wpdb->delete($table_name, array('post_id' => absint($post_id))); // db call ok

        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($field['name']) {
                    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
                    $wpdb->insert($table_name, array(
                        'post_id' => $post_id,
                        'page_id' => $field['page'],
                        'breadcrumb_text' => $field['name']
                    ));
                }
            }
        }
    }
}

add_action('save_post', 'sempai_breadcrumbs_save_meta_box');

function sempai_breadcrumbs_enqueue_scripts($hook)
{
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }
    $version = '1.0.1';
    wp_enqueue_script(
        'sempai-breadcrumbs-script',
        plugins_url('../assets/script.js', __FILE__),
        array(),
        $version,
        true
    );
}

add_action('admin_enqueue_scripts', 'sempai_breadcrumbs_enqueue_scripts');

function sempai_breadcrumbs_enqueue_styles($hook)
{
    if ('post.php' != $hook && 'post-new.php' != $hook) {
        return;
    }
    $version = '1.0.1';

    wp_enqueue_style(
        'sempai-breadcrumbs-style',
        plugins_url('../assets/style.css', __FILE__),
        array(),
        $version,
        'all'
    );
}

add_action('admin_enqueue_scripts', 'sempai_breadcrumbs_enqueue_styles');

function sempai_get_custom_breadcrumbs($post_id)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'sempai_custom_breadcrumbs';
    $cache_key = 'sempai_custom_breadcrumbs_' . $post_id;

    $breadcrumbs = wp_cache_get($cache_key, 'sempai_custom_breadcrumbs');
    if (false === $breadcrumbs) {

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT DISTINCT page_id, breadcrumb_text FROM %i WHERE post_id = %d",
            [$table_name, $post_id]
        ), ARRAY_A);

        $breadcrumbs = array();
        if ($results) {
            foreach ($results as $result) {
                $breadcrumbs[] = array(
                    'page' => get_permalink($result['page_id']),
                    'name' => $result['breadcrumb_text']
                );
            }
        }

        wp_cache_set($cache_key, $breadcrumbs, 'sempai_custom_breadcrumbs', 3600);
    }

    return $breadcrumbs;
}

function sempai_display_custom_breadcrumbs()
{
    global $post;
    return sempai_get_custom_breadcrumbs($post->ID);
}
