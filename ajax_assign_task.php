<?php

/**
 * Assign a task via AJAX
 */

//Load Wordpress API
if (!defined('WP_PLUGIN_URL')) {
    require_once( realpath('../../../') . '/wp-config.php' );
}

/**
 * @todo Cambiar capability por una propia de Kanpress
 */
if (current_user_can('edit_users')) {
    if (isset($_POST['taskId'])) {

        /* 
         * Checks wether the specified user ID exists 
         */
        $q_select = 'SELECT ID, display_name FROM ' . $wpdb->prefix . 'users '
                . 'WHERE ID = %d';

        $user = $wpdb->get_row($wpdb->prepare($q_select, intval($_POST['user'])), ARRAY_A);

        if (!empty($user)) {
            
            /** @todo Use mysql function NOW() for the time_assigned field */
            $wpdb->update($wpdb->prefix . 'kanpress_task', 
                    array('assigned_to' => $_POST['user'], 'time_assigned' => date('Y-m-d H:i:s')), 
                    array('task_id' => $_POST['taskId']));

            //Returns the assigned user avatar (including <img> tag)
            die(get_avatar($$usertask['ID'], 50, null, $user['display_name']));
            
        } else {
            header("HTTP/1.0 400 Bad request");
            die("User does not exist");
        }
    }
} else {
    header("HTTP/1.0 403 Forbidden");
    die("Permission denied");
}