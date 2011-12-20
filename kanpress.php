<?php

/*
Plugin Name: Kanpress
Plugin URI: http://www.israelviana.es/kanpress
Description: Kanban board for managing the creation of posts
Version: 0.1
Author: Israel Viana
Author URI: http://www.israelviana.es
License: LGPL
*/

require_once 'util.php';

/*
 * Some constants
 */
define('CAPABILITY_REMOVE_TASKS', 'delete_post');
define('KANPRESS', plugins_url() . '/kanpress');

/*
 * Initialization
 */

//Establece el huso horario para evitar problemas y los warning 5.3
/** @todo Coger el del sistema */
date_default_timezone_set('Europe/Madrid');

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
    add_menu_page("Kanpress board", "Kanpress", "edit_posts", "kanpress", "kanpress_show_board_page");

    //Agrega el elemento de menú Kanpress board
    add_submenu_page('kanpress', "Board", "Settings", "create_users", "kanpress-config", 'kanpress_show_config_page');
}

add_action('admin_menu', 'kanpress_create_admin_menu');

