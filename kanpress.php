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
define('TABLE_TASK', $wpdb->prefix . 'kanpress_task');

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
    
    $sql = "CREATE TABLE " . $wpdb->prefix . "kanpress_task (
            task_id int(10) unsigned NOT NULL AUTO_INCREMENT,
            proposed_by bigint(20) unsigned NOT NULL,
            assigned_to bigint(20) unsigned DEFAULT NULL,
            revised_by bigint(20) unsigned DEFAULT NULL,
            term_id bigint(20) unsigned NOT NULL,
            post_id bigint(20) unsigned DEFAULT NULL,
            priority tinyint(4) unsigned NOT NULL,
            status tinyint(4) unsigned NOT NULL COMMENT '0=propuesta, 1=asignada, 2=pendiente, 3=terminada',
            summary varchar(255) COLLATE utf8_spanish_ci NOT NULL,
            description text COLLATE utf8_spanish_ci NOT NULL,
            time_proposed timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            time_assigned timestamp NULL DEFAULT '0000-00-00 00:00:00',
            time_done timestamp NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (task_id)
            );";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

//Plug-in activation function
register_activation_hook(__FILE__, 'kanpress_create_db_schema');
