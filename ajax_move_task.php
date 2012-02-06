<?php

/**
 * Change a task status
 */
 
//Load Wordpress API
if (!defined('WP_PLUGIN_URL')) {
    require_once( realpath('../../../').'/wp-config.php' );
}

/**
 * @todo Change capability
 */
if (current_user_can('edit_users')) {
    if (isset($_POST['task_id'])) {
        die($wpdb->update('wp_kanpress_task', array(
                'status' => $_POST['status']),
                array('task_id' => $_POST['task_id'])));
    }
} else {
    die("Permission denied");
}
