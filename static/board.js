/**
 * JavaScript functions and event bindings for board front-end
 */

var anteriorColumna;
var doAjax = true;

jQuery(function() {
    $ = jQuery;
    
    //Altura del tablero = algo menos de la altura de la página
    function ajustarAlturaTablero() {
        altura = $("#footer").position().top - $("#footer").height() - $(".area-tareas:eq(0)").position().top - 50;
        $(".area-tareas").css("min-height", altura + "px");
        
        //Iguala las alturas de todos los .area-tareas
        mayorAltura = 0;
        $.each($(".area-tareas"), function(indice, elemento) {
            altura = $(elemento).height();
            mayorAltura = (mayorAltura > altura) ? mayorAltura : altura;
        });
        $(".area-tareas").height(mayorAltura);
    };
    
    ajustarAlturaTablero();
    $(window).resize(ajustarAlturaTablero);
    
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

        /*
         * Editar tarea
         */
        $(".btn-guardar").click(function() {

            taskId = $(this).attr("id").substr(8);
            d = $(this).parent().find(".edit-description").val();
            
            /** @todo No poner ... si la cadena < 101 caracteres */
            $("#short-" + taskId).html(d.substr(0, 100) + "...");
                
            $.post(KANPRESS + "/ajax_edit_task.php", 
                {description: d, taskId: taskId}, 
                function() {
                    
                }
            );

            cerrarPopup();
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
        $("#TB_overlay").show();
        $("#TB_window").show();
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
            
            //Actualizar los contadores
            contarTareas();
        }
    });
    
    /**
     * Pone el # de tareas en el título de cada columna
     */
    function contarTareas() {
        $("#col1, #col2, #col3").each(function(n, columna) {
            tareas = $(columna).find(".tarea").length;
            $(columna).find("h3 span").html("(" + tareas + ")");
        });
    }
    
    //Lanzar al inicio, of course
    contarTareas();    
        
    function cerrarPopup() {
        $("#TB_overlay").hide();
        $("#TB_window").hide();
    }
});