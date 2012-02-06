<?php

require_once 'util.php';

global $wpdb;

$validacion = array();

/*
 * Create new task
 */
if (isset($_POST['enviado'])) {
    
    $uid = get_current_user_id();
    
    //Sanitize data
    //$wpdb->escape_by_ref($_POST['resumen']);
    //$wpdb->escape_by_ref($_POST['descripcion']);
    
    $prioridad = intval($_POST['prioridad']);
    if ($prioridad < 0 || $prioridad > 2) $prioridad = 1;
    
    //Form validation
    if (empty($_POST['resumen']) || strlen($_POST['resumen']) < 10) {
        $validacion['resumen'] = "Debes escribir un resumen de mínimo 10 letras";
    }
    
    if (!count($validacion)) {
    
        /** @todo Use the correct prefix */
        $wpdb->insert('wp_kanpress_task', array(
                'proposed_by' => $uid,
                'assigned_to' => null,
                'revised_by' => null,
                'term_id' => $_POST['categoria'],
                'post_id' => null,
                'priority' => $prioridad,
                'status' => 0,
                'summary' => $_POST['resumen'],
                'description' => $_POST['descripcion'],
                'time_proposed' => date('Y-m-d H:i:s'),
                'time_assigned' => null,
                'time_done' => null));
                
        $_POST = array();
    }
}

/*
 * Assign or change task status
 */
if (isset($_POST['assign'])) {

    $wpdb->update('wp_kanpress_task', array(
            'assigned_to' => $_POST['user'],
            'status' => $_POST['taskStatus']),
            array('task_id' => $_POST['taskId']));
}

/*
 * Load kanban board
 */

$select = "SELECT * FROM wp_kanpress_task "
        . "JOIN wp_terms ON wp_kanpress_task.term_id = wp_terms.term_id "
        . "WHERE status < 3 "
        . "ORDER BY time_proposed DESC";

$tasks = $wpdb->get_results($select, ARRAY_A);

$tareas_propuestas = array();
$tareas_asignadas = array();
$tareas_pendientes = array();

//Fetch all active tasks
foreach ($tasks as &$t) {
    
    $t['summary'] = stripslashes($t['summary']);
    $t['description'] = stripslashes($t['description']);
    
    //Fetch the display name for the reporter
    $t['user_proposed'] = get_userdata(intval($t['proposed_by']))->data->display_name;
    
    //Fetch the display name for the assigned author
    if (intval($t['assigned_to']) > 0) {
        $t['user_assigned'] = get_userdata(intval($t['assigned_to']))->data->display_name;
    }
    
    //Fetch the display name for the revising author
    if (intval($t['revised_by']) > 0) {
        $t['user_revised'] = get_userdata(intval($t['revised_by']))->data->display_name;
    }
    
    //Classify each task into proposed, assigned and pendant
    switch ($t['status']) {
        case 0:
            $tareas_propuestas[] = $t;
            break;
        case 1:
            $tareas_asignadas[] = $t;
            break;
        case 2:
            $tareas_pendientes[] = $t;
            break;
    }
}

$categorias = $wpdb->get_results("SELECT wp_terms.term_id, wp_terms.name "
        . "FROM wp_terms JOIN wp_term_taxonomy "
        . "ON wp_term_taxonomy.term_id = wp_terms.term_id "
        . "WHERE wp_term_taxonomy.taxonomy = 'category' "
        . "ORDER BY name ASC", ARRAY_A);
        
//Prepare array for <select>
$categorias = array_atributo_valor($categorias);

//Get al the users
$users = array();
$usuarios_original = get_users();
foreach ($usuarios_original as $u) {
    $users[$u->ID] = $u->display_name;
}

//Load JS/CSS and the view for this page
wp_enqueue_script('jquery-ui-droppable');
wp_enqueue_style('', KANPRESS . '/static/kanpress.css');

//wp_enqueue_style('', includes_url() . '/js/thickbox/thickbox.css');

include 'page_board.view.php';
