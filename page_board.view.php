<script type="text/javascript">

var KANPRESS = '<?php echo plugins_url() ?>/kanpress';

jQuery(function() {
    <?php //Si el formulario no valida, mostramos el pop-up al inicio ?>
    <?php if (!empty($validacion)) : ?>
    mostrarPopupNuevoArticulo(true);
    <?php endif ?>
});
</script>

<div class="wrap">
    <div id="icono-kanpress" class="icon32"><br></div>
    <h2>Kanpress <a href="javascript:void(0)" class="add-new-h2">Proponer nuevo artículo</a></h2>

    <div class="kanban-contenedor tres-col">
        <div class="col" id="col1">
            <h3>Artículos planteados <span></span></h3>
            <div class="area-tareas">
                <?php foreach ($tareas_propuestas as $task) : ?>
                <?php kanpress_html_task($task) ?>
                <?php endforeach ?>
            </div>
        </div>
        <div class="col" id="col2">
            <h3>En desarrollo <span></span></h3>
            <div class="area-tareas">
                <?php foreach ($tareas_asignadas as $task) : ?>
                <?php kanpress_html_task($task) ?>
                <?php endforeach ?>
            </div>
        </div>
        <div class="col" id="col3">
            <h3>Pendiente de revisión <span></span></h3>
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
<div id="TB_window">
    <div id="TB_title">
        <div id="TB_ajaxWindowTitle"></div>
        <div id="TB_closeAjaxWindow"><a href="#" id="TB_closeWindowButton" title="Cerrar"><img src="http://localhost/wordpress/wp-includes/js/thickbox/tb-close.png"></a></div>
    </div>
    <div id="ventana-contenido"></div>
</div>

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
                        <textarea id="descripcion" name="descripcion" type="text" class="regular-text" cols="25" rows="5"><?php echo stripslashes(htmlentities(post('descripcion', true))) ?></textarea>
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
