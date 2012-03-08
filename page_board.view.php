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
    <h2>Kanpress <a href="javascript:void(0)" class="add-new-h2"><?php _e('Nueva tarea', 'kanpress') ?></a></h2>

    <div class="kanban-contenedor tres-col">
        <div class="col" id="col1">
            <h3><?php _e('Artículos planteados', 'kanpress') ?> <span></span></h3>
            <div class="area-tareas">
                <?php foreach ($tareas_propuestas as $task) : ?>
                <?php kanpress_html_task($task, $categorias) ?>
                <?php endforeach ?>
            </div>
        </div>
        <div class="col" id="col2">
            <h3><?php _e('En desarollo', 'kanpress') ?> <span></span></h3>
            <div class="area-tareas">
                <?php foreach ($tareas_asignadas as $task) : ?>
                <?php kanpress_html_task($task, $categorias) ?>
                <?php endforeach ?>
            </div>
        </div>
        <div class="col" id="col3">
            <h3><?php _e('Pendiente de revisión', 'kanpress') ?> <span></span></h3>
            <div class="area-tareas">
                <?php foreach ($tareas_pendientes as $task) : ?>
                <?php kanpress_html_task($task, $categorias) ?>
                <?php endforeach ?>
            </div>
        </div>
    </div>
</div>


<!-- Capas inicialmente ocultas -->

<div id="TB_overlay" class="TB_overlayBG"></div>
<div id="TB_window" style="background: white">
    <div id="TB_title" style="overflow: hidden">
        <div id="TB_ajaxWindowTitle"></div>
        <div id="TB_closeAjaxWindow"><a href="#" id="TB_closeWindowButton" title="<?php _e('Cerrar', 'kanpress') ?>"><img src="<?php bloginfo('wpurl') ?>/wp-includes/js/thickbox/tb-close.png"></a></div>
    </div>
    <div id="ventana-contenido"></div>
</div>

<div id="form-nueva">
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>" class="kanpress-form form-nueva">
        <table class="form-table">
            <tbody>
                <tr class="<?php invalido('resumen', $validacion) ?>">
                    <th><label for="resumen"><?php _e('Resumen', 'kanpress') ?>:</label></th>
                    <td>
                        <input id="resumen" name="resumen" type="text" class="regular-text" value="<?php echo stripslashes(htmlentities(post('resumen', true))) ?>" />
                        <span class="description"></span>
                        <div class="val"><?php if (isset($validacion['resumen'])) echo $validacion['resumen'] ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="descripcion"><?php _e('Descripción', 'kanpress') ?>:</label></th>
                    <td>
                        <textarea id="descripcion" name="descripcion" type="text" class="regular-text" cols="25" rows="5"><?php echo stripslashes(htmlentities(post('descripcion', true))) ?></textarea>
                        <span class="description"></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="categoria"><?php _e('Sección', 'kanpress') ?>:</label></th>
                    <td>
                        <?php echo form_select('categoria', $categorias, post('categoria', true), null, null) ?>
                        <span class="description"></span>
                    </td>
                </tr>
                <tr>
                    <th><label for="prioridad"><?php _e('Prioridad', 'kanpress') ?>:</label></th>
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
    <form method="post" action="<?php echo KANPRESS ?>'/ajax_assign_task.php" class="kanpress-form asignar-tarea">
        <input type="hidden" name="taskId" id="taskId" />
        
        <table class="form-table">
            <tbody>
                <tr>
                    <th><label for="categoria" style="width: auto"><?php _e('Asignar a:', 'kanpress') ?>:</label></th>
                    <td>
                        <?php echo form_select('user', $users, null, null, null) ?>
                        <span class="description"></span>
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <button type="button" name="assign" class="button-primary margen-arriba" id="btn-asignar"><?php _e('Asignar tarea', 'kanpress') ?></button>
        </p>
    </form>
</div>
