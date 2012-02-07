<?php

/**
 * Single task template
 * @param array $task Task data
 */

function kanpress_html_task($task) { 
    
    $priorities = array(0=>'slow', 1=>'medium', 2=>'high'); 
?>
    <div class="tarea" id="tarea-<?php echo $task['task_id'] ?>">
        <div class="dentro">
        
            <a class="img asignar" href="javascript:void(0)">
            <?php if (intval($task['assigned_to']) > 0) : ?>
                <?php echo get_avatar($task['assigned_to'], 50, null, $task['user_assigned']) ?>
            <?php else : ?>
                No asignada
            <?php endif ?>
            </a>
            
            <?php $prioridades = array("baja", "normal", "alta") ?>
            <h4 class="<?php echo $priorities[$task['priority']] ?>" title="Prioridad <?php echo $prioridades[$task['priority']] ?>">
                <?php echo $task['summary'] ?>
            </h4>
            
            <p>
                <span class="task-description-short" id="short-<?php echo $task['task_id'] ?>"><?php echo cortar_texto($task['description'], 80) ?></span>
                
                <a href="javascript:void(0)" class="enlace-detalles" id="<?php echo $task['task_id'] ?>">[+info]</a>
                
                <div id="detalles-<?php echo $task['task_id'] ?>" class="tarea-detalles">
                    <p class="asignacion">
                    <?php if (empty($task['assigned_to'])) : ?>
                        La tarea todavía no ha sido asignada a nadie.
                    <?php else : ?>
                        <?php echo get_avatar($task['assigned_to'], 50, null, $task['user_assigned']) ?>
                        <span class="light">Asignada a</span>
                        <br />
                        <?php echo $task['user_assigned'] ?>
                    <?php endif ?>
                    </p>
                    
                    <div class="task-description">
                        Descripción: <br />
                        <textarea rows="4" cols="30" class="edit-description"><?php echo $task['description'] ?></textarea>
                    </div>
                    
                    <ul class="task-history">
                        <li>
                            Creada 
                            <span><?php echo strtolower(hace_tiempo(strtotime($task['time_proposed']))) ?></span>
                            por <span><?php echo $task['user_proposed'] ?></span>
                        </li>
                        <?php if (intval($task['assigned_to']) > 0) : ?>
                        <li>
                            Asignada
                            <span><?php echo strtolower(hace_tiempo(strtotime($task['time_assigned']))) ?></span>
                            a <span><?php echo $task['user_assigned'] ?></span>
                        </li>
                        <?php endif ?>
                        <?php if (intval($task['time_done']) > 0) : ?>
                        <li>
                            Completada
                            <span><?php echo strtolower(hace_tiempo(strtotime($task['time_done']))) ?></span>
                        </li>
                        <?php endif ?>
                    </ul>
                    
                    <hr /> 
                    
                    <button type="button" name="save" class="button-primary margen-arriba btn-guardar" id="guardar-<?php echo $task['task_id'] ?>">Guardar</button>
                    o <a href="javascript:void(0)" class="cerrar-popup">cerrar sin guardar</a>
                    o <a href="javascript:void(0)" class="remove-task-link" id="remove-<?php echo $task['task_id'] ?>">Eliminar tarea</a>
                </div>
            </p>
        </div>
        
        <div class="pie">
            <div class="seccion">
                <?php echo $task['name'] ?>
            </div>
            <div class="meta">
                <span class="creation-time"><?php echo hace_tiempo(strtotime($task['time_proposed'])) ?></span>
            </div>
        </div>
    </div>
<?php } ?>