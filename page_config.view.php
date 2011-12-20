<style>
@import "<?php echo plugins_url() ?>/kanpress/static/kanpress.css";
</style>

<div class="wrap">

	<div id="icono-kanban" class="icon32"><br></div>
	<h2>Kanban settings</h2>

    <div class="error settings-error"><p><strong>Advertencia de validación</strong></p></div>
    
    <form method="post" action="" class="kanpress-form">
        <div>
            <label for="uncampo">Un campo:</label>
            <div>
                <input id="uncampo" type="text" class="regular-text" />
                <span class="sugerencia">Sugerencia</span>
            </div>
        </div> 
        <div class="no-valido">
            <label for="otrocampo">Otro campo:</label>
            <div>
                <input id="otrocampo" type="text" class="regular-text" />
                <span class="sugerencia">Sugerencia</span>
            </div>
        </div> 
    </form>
    
</div>
