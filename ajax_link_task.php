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
        if ($taskId <= 0) { /** @todo Error 403 */ }

        $q_task = 'SELECT * FROM ' . TABLE_TASK . ' WHERE task_id = %d LIMIT 1';
        $task = $wpdb->get_row($wpdb->prepare($q_task, $taskId), ARRAY_A);

        if (empty($task)) { /** @todo Error 403 */ }

        //Creates the post
        $post_data = array(
            'post_title'    => ' ',
            'post_category' => array($task['term_id']),
            'post_content'  => '',
            'post_status'  => 'draft',
            'post_author'   => $task['assigned_to']
        );
        $post_id = wp_insert_post($post_data);
        
        //Insert post metadata with task data
        /** @todo i18n the meta key? */
        update_post_meta($post_id, 'task_id', $task['task_id']);
        update_post_meta($post_id, 'task_summary', $task['summary']);
        update_post_meta($post_id, 'task_description', $task['description']);

        //Associate the task and the post
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
