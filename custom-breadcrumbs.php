<?php
/*
* Plugin Name: Sempai Custom Breadcrumbs
* Description: A WordPress plugin that allows you to add custom breadcrumbs to your website with the ability to customize the styling.
* Version: 1.0.1
* Requires at least: 6.2
* Requires PHP: 7.2
* Author: sempaiagency
* Author URI: https://sempai.pl
* License: GPLv2 or later
* Text Domain: custom-breadcrumbs
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once plugin_dir_path(__FILE__) . 'includes/admin.php';

/**
 * @return void
 */
function sempai_custom_breadcrumbs_install(): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'sempai_custom_breadcrumbs';

    $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            page_id BIGINT(20) UNSIGNED NOT NULL,
            breadcrumb_text VARCHAR(255) NOT NULL,
            date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
}

register_activation_hook(__FILE__, 'sempai_custom_breadcrumbs_install');

/**
 * @return void
 */
function sempai_custom_breadcrumbs_uninstall(): void
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'sempai_custom_breadcrumbs';

    $sql = $wpdb->prepare("DROP TABLE IF EXISTS %s", $table_name);
    dbDelta($sql);
}

register_uninstall_hook(__FILE__, 'sempai_custom_breadcrumbs_uninstall');
