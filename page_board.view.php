<?php function kanpress_html_task($task) { ?>
<?php $priorities = array(0=>'slow', 1=>'medium', 2=>'high'); ?>
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
                <?php echo cortar_texto($task['description'], 80) ?>
                
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
                        <textarea rows="4" cols="30"><?php echo $task['description'] ?></textarea>
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
                    
                    <button type="button" name="assign" class="button-primary margen-arriba" id="btn-guardar">Guardar</button>
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

<style>
@import "<?php echo plugins_url() ?>/kanpress/static/kanpress.css";
</style>

<script type="text/javascript">

var KANPRESS = '<?php echo plugins_url() ?>/kanpress';
var anteriorColumna;
var doAjax = true;

jQuery(function() {
    $ = jQuery;
    
    //Altura del tablero = algo menos de la altura de la página
    function ajustarAlturaTablero() {
        altura = $("#footer").position().top - $("#footer").height() - $(".area-tareas:eq(0)").position().top - 50;
        $(".area-tareas").css("min-height", altura + "px");
    };
    
    $(window).resize(ajustarAlturaTablero);
    
    //Iguala las alturas de todos los .area-tareas
    mayorAltura = 0;
    $.each($(".area-tareas"), function(indice, elemento) {
        altura = $(elemento).height();
        mayorAltura = (mayorAltura > altura) ? mayorAltura : altura;
    });
    $(".area-tareas").height(mayorAltura);
    
    //Pop-ups (genérico)
    $("#TB_closeWindowButton").click(function() {
        cerrarPopup();
    });
    
    //Pop-up (detalles de tarea)
    $(".enlace-detalles").click(function() {
    
        //Título del pop-up
        $("#TB_ajaxWindowTitle").html($(this).parent().parent().find("h4").html());
        
        //Pone el contenido apropiado dentro del pop-up
        idTarea = $(this).attr("id");
        $("#ventana-contenido").html($("#detalles-" + idTarea).html());
        
        //Muestra el overlay y el pop-up
        $("#TB_overlay").show();
        $("#TB_window").show();
            
        //Enlace "cerrar sin guardar"
        $(".cerrar-popup").click(function() {
            cerrarPopup();
        });
        
        //Enlace "eliminar tarea"
        $(".remove-task-link").click(function() {

            if (confirm("¿Seguro que quieres eliminar esta tarea?\n¡No la podrás recuperar!")) {

                //The element ID "remove-xxx" where xxx is the task ID. 
                //7 is the length of "remove-"
                taskId = $(this).attr("id").substr(7);

                /* 
                * For faster response, we remove the task from the HTML before we 
                * get the confirmation from the server
                */

                //if (parseInt(response) == 1) {
                    $("#tarea-" + taskId).hide('slow', function() {
                        $(this).remove();
                    });

                    //Hide the popup also
                    cerrarPopup();
                //}

                //Performs the AJAX request to remove the task
                $.post(KANPRESS + '/ajax_remove_task.php', {task_id: taskId});
            }
        });
    });
    
    /**
     * Muestra el pop-up para asignar una tarea
     */
    $(".asignar").click(function() {
        //Título del pop-up
        $("#TB_ajaxWindowTitle").html("Asignar tarea");
        $("#ventana-contenido").html($("#asignar-tarea").html());

        //Muestra el overlay y el pop-up
        $("#TB_overlay").show();
        $("#TB_window").show();
        $("#user").focus();

        //Pass the task ID to the form
        taskId = $(this).parent().parent().attr("id").substr(6);
        $("#taskId").val(taskId);
        
        $("#btn-asignar").click(function() {

            taskId = $("#taskId").val();
            userId = $("#user").val();

            postData = {
                'taskId': taskId,
                'user': userId
            };
            
            $("#TB_overlay").hide('fast');
            $("#TB_window").hide('fast');
            
            $.ajax({
                type: 'POST',
                url: KANPRESS + '/ajax_assign_task.php',
                dataType: 'html',
                data: postData,
                success: function(response) {
                    $("#tarea-" + taskId + " .asignar").html(response);
                    
                    //Update the details pop-up ("assigned to...")
                    $("#tarea-" + taskId + " .asignacion").html(
                        response + '<span class="light">Asignada a</span>'
                        + '<br />' + $("#user option[value=" + userId + "]").html()
                    );
                },
                error: function() {
                    
                }
            });
        });
    });
    
    function mostrarPopupNuevoArticulo() {
    
        //Título del pop-up
        $("#TB_ajaxWindowTitle").html("Proponer nuevo artículo");
        $("#ventana-contenido").html($("#form-nueva").html());
        
        //Muestra el overlay y el pop-up
        $("#TB_overlay").show();
        $("#TB_window").show();
        $("#resumen").focus();
    }
    
    
    //Pop-up (nueva tarea)
    $(".add-new-h2").click(mostrarPopupNuevoArticulo);
    
    /**
     * Task drag-and-drop
     */
    $(".tarea").draggable();
    
    $(".area-tareas").droppable({
        drop: function(event, ui) {
        
            //When dropping on a column, set the position of the task to the flow
            $(".ui-draggable-dragging")
                .css("top", 0)
                .css("left", 0)
                .appendTo($(this));
            
            task = $(".ui-draggable-dragging");
            
            //Get the task id
            taskId = parseInt(task.attr("id").substr(6));
            
            //Change status via AJAX
            if (doAjax) {
            
                newStatus = parseInt($(this).parent().attr("id").substr(3)) - 1;
                
                $.post(KANPRESS + '/ajax_move_task.php', {task_id: taskId, status: newStatus});
                
                //Shine effect
                task.animate({opacity: .4}, 200, function() {
                    task.animate({opacity: 1}, 200);
                });
            }
        }
    });
        
    function cerrarPopup() {
        $("#TB_overlay").hide();
        $("#TB_window").hide();
    }

    <?php //Si el formulario no valida, mostramos el pop-up al inicio ?>
    <?php if (!empty($validacion)) : ?>
    mostrarPopupNuevoArticulo();
    <?php endif ?>
});
</script>

<div class="wrap">
	<div id="icono-kanpress" class="icon32">
		<br>
	</div>
	<h2>Kanban <a href="javascript:void(0)" class="add-new-h2">Proponer nuevo artículo</a></h2>

    <div class="kanban-contenedor tres-col">
        <div class="col" id="col1">
            <h3>Artículos planteados</h3>
            <div class="area-tareas">
                <?php foreach ($tareas_propuestas as $task) : ?>
                <?php kanpress_html_task($task) ?>
                <?php endforeach ?>
            </div>
        </div>
        <div class="col" id="col2">
            <h3>En desarrollo</h3>
            <div class="area-tareas">
                <?php foreach ($tareas_asignadas as $task) : ?>
                <?php kanpress_html_task($task) ?>
                <?php endforeach ?>
            </div>
        </div>
        <div class="col" id="col3">
            <h3>Pendiente de revisión</h3>
            <div class="area-tareas">
                <?php foreach ($tareas_pendientes as $task) : ?>
                <?php kanpress_html_task($task) ?>
                <?php endforeach ?>
            </div>
        </div>
    </div>

</div>


<!-- Capas inicialmente ocultas -->

<div id="TB_overlay" class="TB_overlayBG"></div>
<div id="TB_window" style="width: 670px; height: auto; margin-left: -335px; top: 100px; margin-top: 0px; visibility: visible; "><div id="TB_title"><div id="TB_ajaxWindowTitle"></div><div id="TB_closeAjaxWindow"><a href="#" id="TB_closeWindowButton" title="Cerrar"><img src="http://localhost/wordpress/wp-includes/js/thickbox/tb-close.png"></a></div></div><div id="ventana-contenido"></div></div>

<div id="form-nueva">
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" class="kanpress-form">
        <table class="form-table">
            <tbody>
                <tr class="<?php invalido('resumen', $validacion) ?>">
	                <th><label for="resumen">Resumen:</label></th>
	                <td>
	                    <input id="resumen" name="resumen" type="text" class="regular-text" value="<?php echo stripslashes(htmlentities(post('resumen', true))) ?>" />
                        <span class="description"></span>
                        <div class="val"><?php if (isset($validacion['resumen'])) echo $validacion['resumen'] ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="descripcion">Descripción:</label></th>
                    <td>
                        <textarea id="descripcion" name="descripcion" type="text" class="regular-text" cols="25" rows="5"><?php stripslashes(htmlentities(post('descripcion', true))) ?></textarea>
                        <span class="description"></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="categoria">Sección:</label></th>
                    <td>
                        <?php echo form_select('categoria', $categorias, post('categoria', true), null, null) ?>
                        <span class="description"></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="prioridad">Prioridad:</label></th>
                    <?php $prioridad = 1; //Por defecto ?>
                    <?php if (post('prioridad', true)) $prioridad = post('prioridad', true); ?>
                    <td><?php echo form_select('prioridad', array('Baja', 'Normal', 'Alta'), $prioridad, null, null) ?>
                    <span class="description">description</span></td>
                    </td>
                </tr>
            </tbody>
        </table>
            
        <p class="submit">
            <button type="submit" name="enviado" class="button-primary margen-arriba">Enviar propuesta</button>
        </p>
    </form>
</div>

<div id="asignar-tarea">
    <form method="post" action="<?php echo KANPRESS ?>'/ajax_assign_task.php" class="kanpress-form">
        <input type="hidden" name="taskId" id="taskId" />
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="categoria" style="width: auto">Asignar a:</label></th>
                    <td>
                        <?php echo form_select('user', $users, null, null, null) ?>
                        <span class="description">Se enviará una notificación</span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <button type="button" name="assign" class="button-primary margen-arriba" id="btn-asignar">Asignar tarea</button>
        </p>
    </form>
</div>
