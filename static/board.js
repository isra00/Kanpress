/**
 * JavaScript functions and event bindings for board front-end
 */

var anteriorColumna;
var doAjax = true;

$ = jQuery;
    
/**
 * Altura del tablero = algo menos de la altura de la página
 */
function ajustarTablero() {
    altura = $("#footer").position().top - $("#footer").height() - $(".area-tareas:eq(0)").position().top - 50;
    $(".area-tareas").css("min-height", altura + "px");

    //Iguala las alturas de todos los .area-tareas
    mayorAltura = 0;
    $.each($(".area-tareas"), function(indice, elemento) {
        altura = $(elemento).height();
        mayorAltura = (mayorAltura > altura) ? mayorAltura : altura;
    });
    $(".area-tareas").height(mayorAltura);

    //Responsive layout
    if ($(window).width() < 700) {
        /** @todo Cambiar <h3> "artículos planteados" => "planteados", "pendiente de revisión"=>"pendiente" */
        $(".wrap").addClass("responsive-pq");
    } else {
        $(".wrap").removeClass("responsive-pq");
    }
};


/**
 * Pone el # de tareas en el título de cada columna
 */
function contarTareas() {
    $("#col1, #col2, #col3").each(function(n, columna) {
        tareas = $(columna).find(".tarea").length;
        $(columna).find("h3 span").html("(" + tareas + ")");
    });
}


/**
 * Mostrar el pop-up con el formulario de nueva tarea
 *
 * Si el argumento noVaciarFormulario es true, se mantiene y no se vacía. Si es
 * cualquier otro valor, o no se pasa, se vaciará el formulario.
 */
function mostrarPopupNuevoArticulo(noVaciarFormulario) {
    
    //Título del pop-up
    $("#TB_ajaxWindowTitle").html("Proponer nuevo artículo");
    
    //Contenido del pop-up
    $("#ventana-contenido").html($("#form-nueva").html());

    //Muestra el overlay y el pop-up
    abrirPopup();
    
    //Vacía el formulario por si había sido enviado y no había validado
    if (!(noVaciarFormulario == true)) {
    
        $(".val").hide();
        $("#resumen").val("");
        $("#descripcion").val("");
        
        /** @todo Resetear valores de los <select> */
    }
    
    $("#resumen").focus();
}


/**
 * Muestra y centra el pop-up thickbox
 */
function abrirPopup() {
    /*
     * Por algún motivo que desconozco, el left empieza a contar en #wpbody, no 
     * en el principio de la pantalla
     */
    leftPosition = (screen.width / 2) - ($("#TB_window").width() / 2) - $("#wpbody").position().left;
    $("#TB_window").css("left", leftPosition + "px");
    
    $("#TB_overlay").show();
    $("#TB_window").show();
}


/**
 * Oculta el pop-up thickbox
 */
function cerrarPopup() {
    $("#TB_overlay").hide();
    $("#TB_window").hide();
}


function eliminarTarea() {
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
}
    
$(function() {
    
    //Ajustar altura del tablero al inicio y cuando la ventana se redimensione
    ajustarTablero();
    $(window).resize(ajustarTablero);
    
    //Pop-ups (genérico)
    $("#TB_closeWindowButton").click(function() {
        cerrarPopup();
    });
    
    //Lanzar al inicio, of course
    contarTareas();
    
    //Pop-up (nueva tarea)
    $(".add-new-h2").click(mostrarPopupNuevoArticulo);
    
    //Enlaces "eliminar tarea" fuera del popup (=artículos publicados)
    $(".remove-task-link").click(eliminarTarea);
    
    //Pop-up (detalles de tarea)
    $(".enlace-detalles").click(function() {
    
        //Título del pop-up
        $("#TB_ajaxWindowTitle").html($(this).parent().parent().find("h4").html());
        
        //Pone el contenido apropiado dentro del pop-up
        idTarea = $(this).attr("id");
        
        /*
         * Mueve el .tarea-detalles al pop-up y oculta los "hermanos" que ya
         * estuvieran dentro
         */
        $("#ventana-contenido .tarea-detalles").hide();
        $("#detalles-" + idTarea).appendTo($("#ventana-contenido")).show();
        
        //Muestra el overlay y el pop-up
        abrirPopup();
            
        //Enlace "cerrar sin guardar"
        $(".cerrar-popup").click(function() {
            cerrarPopup();
        });
        
        //Enlace "eliminar tarea"
        $(".remove-task-link").click(eliminarTarea);

        /*
         * Editar tarea
         */
        $(".btn-guardar").click(function() {
            
            priorities = ["low", "medium", "high"];

            taskId = $(this).attr("id").substr(8);
            formulario = $(this).parent();
            description = formulario.find(".edit-description").val();
            priority = formulario.find(".task-priority select").val();
            category = formulario.find(".task-category select").val();
            
            //Show the new values in the task card...
            descriptionToShow = (description.length > 100) ? description.substr(0, 100) + "..." : description;
            $("#short-" + taskId).html(descriptionToShow);
            $("#tarea-" + taskId).find("h4").attr("class", priorities[priority]);
            categoryName = formulario.find(".task-category select option:selected").html();
            $("#tarea-" + taskId).find(".seccion").html(categoryName);
            
            $("#tarea-" + taskId).find(".edit-description").val(description);
            
            cerrarPopup();
            
            //...and send them to the server via AJAX
            $.post(KANPRESS + "/ajax_edit_task.php", 
                {
                    description: description, 
                    taskId: taskId,
                    priority: priority,
                    category: category
                }, 
                function() {
                    
                }
            );
        });
        
        /*
        * Enlazar artículo (crea un nuevo artículo y redirige al panel de edición)
        */
        $(".create-article").click(function() {
            taskId = $(this).attr("id").substr(7);
            
            /** @todo Sustituir elemento <a> por <span> */
            $(this)
                .css("text-decoration", "none")
                .html('<img src="images/loading.gif" /> Creando...');
            
            $.ajax({
                type: "POST",
                dataType: "text",
                data: { task_id: taskId },
                url: KANPRESS + "/ajax_link_task.php",
                
                success: function(postId) {
                    //The HTTP response must be the linked post ID
                    location.href = "post.php?action=edit&post=" + postId;
                },
                error: function() {
                    /** @todo Handle 400 and 403 errors */
                }
            });
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
        abrirPopup();
        $("#user").focus();

        //Pass the task ID to the form
        taskId = $(this).parent().parent().attr("id").substr(6);
        $("#taskId").val(taskId);
        
        /*
         * Asignar tarea
         */
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
            
            //Actualizar los contadores
            contarTareas();
        }
    });
});