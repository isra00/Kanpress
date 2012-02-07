<?php

/**
 * Change a task status
 */
//Load Wordpress API
if (!defined('WP_PLUGIN_URL')) {
    require_once( realpath('../../../') . '/wp-config.php' );
}

/**
 * @todo Cambiar capability por una propia de Kanpress
 */
if (current_user_can('edit_users')) {

    if (isset($_POST['task_id'])) {

        $taskId = intval($_POST['task_id']);
        if ($taskId <= 0) { /** @todo Error 403 */
        }

        $q_task = "SELECT * FROM " . $wpdb->prefix . "kanpress_task WHERE task_id = '$taskId'";
        $task = $wpdb->get_results($select, ARRAY_A);

        if (empty($task)) { /** @todo Error 403 */
        }

        $task = $task[0];

        $post_data = array(
            'post_title'    => ' ',
            'post_category' => $task['term_id'],
            'post_content'  => '',
            'post_status'  => 'draft',
            'post_author'   => $task['assigned_to']
        );

        $post_id = wp_insert_post($post_data);

        $wpdb->update($wpdb->prefix . 'kanpress_task', 
                array('post_id' => $post_id), 
                array('task_id' => $taskId));
        
        //AJAX output is the post ID
        echo $post_id;
        die;
    }
} else {
    die("Permission denied");
}
