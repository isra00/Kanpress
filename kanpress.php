<?php

/*
Plugin Name: Kanpress
Plugin URI: http://www.israelviana.es/kanpress
Description: Kanban board for managing the creation of Wordpress posts
Version: 0.1
Author: Israel Viana
Author URI: http://www.israelviana.es
License: LGPL
*/

/*
 * Some constants
 */
define('CAPABILITY_REMOVE_TASKS', 'delete_post');
define('KANPRESS', plugins_url('', __FILE__));

/*
 * Initialization
 */

//Set the timezome. It avoids some warnings in PHP 5.3
/** @todo Coger el del sistema */
/** @todo ¿Es realmente necesario establecerlo? ¿No está ya establecido? */
date_default_timezone_set('Europe/Madrid');
setlocale(LC_ALL, 'es_ES.utf8');

/*
 * Wordpress bindings
 */

function kanpress_show_board_page() {
    include 'page_board.php';
}

function kanpress_show_config_page() {
    include 'page_config.php';
}

function kanpress_create_admin_menu() {

    //Agrega el menú Kanpress, que enlaza también al tablero
    add_menu_page("Kanpress board", "Kanpress", "edit_posts", "kanpress", 
            "kanpress_show_board_page");

    //Agrega el elemento de menú Kanpress board
    /*add_submenu_page('kanpress', "Board", "Settings", "create_users", 
            "kanpress-config", 'kanpress_show_config_page');*/
}

add_action('admin_menu', 'kanpress_create_admin_menu');


/**
 * Create or replace the DB schema table for store Kanpress tasks
 * 
 * @global type $wpdb   Wordpress database connection
 */
function kanpress_create_db_schema() {
    global $wpdb;
    /** @todo Implementar realmente! Esto es código de ejemplo!! */
    /*$sql = $wpdb->prepare( "CREATE TABLE %s (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            name tinytext NOT NULL,
            text text NOT NULL,
            url VARCHAR(55) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
            );", $table_name);

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);*/
}

register_activation_hook(__FILE__,'jal_install');
