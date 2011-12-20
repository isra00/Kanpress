<?php

/**
 * Remove a tasks
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
        $wpdb->query("DELETE FROM wp_kanpress_task WHERE task_id = '" . intval($_POST['task_id']) . "'");
        /** @todo Instead of 1, return # of rows deleted */
        die(1);
    }
} else {
    die("Permission denied");
}
