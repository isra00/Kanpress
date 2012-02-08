<?php

/**
 * Some useful functions
 */

/**
 * Transformar un array en forma [0]=>(a, b) a un array en forma [a] = b
 * Útil para campos de formulario Select
 *
 * @param   array $salidaBD El array original
 * @return  array           El array transformado
 */
function array_atributo_valor($salidaBD) {
    $tmp = array();
    
    $elemento_indice = 0;
    $elemento_valor = 1;
    
    //Si el array es asociativo, asocia la primera clave con la segunda
    if (isset($salidaBD[0])) {
        if (!isset($salidaBD[0][0])) {
            $campos = array_keys($salidaBD[0]);
            $elemento_indice = $campos[0];
            $elemento_valor = $campos[1];
        }
    }
    
    foreach ($salidaBD as $fila) {    
        $tmp[$fila[$elemento_indice]] = $fila[$elemento_valor];
    }
    return $tmp;
}


/**
 * Genera un <select> con las opciones especificadas y un valor por defecto.
 * Soporta arrays anidados generando <optgroup />
 * 
 * @param string    $nombre         El nombre del select
 * @param array     $opciones       Valores que tomará el select, por ejemplo, 
 *                                  array('hombre' => 'Hombre', 'mujer'=>'Mujer');
 * @param mixed     $seleccionada   Opción seleccionada por defecto
 * @param array     $atributos      Atributos para el elemento <select>
 * @return string   El HTML final
 */
function form_select($nombre, $opciones, $seleccionada=null, $atributos=null, $primera_opcion=array('0'=>'Todos')) {
    
    $seleccionada = (string) $seleccionada;
    
    $salida = '<select name="' . $nombre . '"';
    $salida = $salida . ' id="' . $nombre . '"';
    if (is_array($atributos) && count($atributos)) {
        foreach ($atributos as $n => $valor) {
            $salida .= ' ' . $n . '="' . $valor . '"';
        }
    }

    $salida .= ">\n";
    
    //Primera opción
    if (is_array($primera_opcion)) {
        /** @todo Esto permite muchas "primeras opciones". ¿Es bueno o malo? */
        foreach ($primera_opcion as $i=>$v) {
            $salida .= "<option value='".$i."'>".$v."</option>\n";
        }
    }
    
    foreach ($opciones as $c => $v) {

        if (is_array($v)) {
            $salida .= "<optgroup label=\"$c\">\n";
            foreach ($v as $subclave=>$subvalor) {
                $salida .= '<option value="' . $subvalor . '"';
                if ($subvalor == $seleccionada) $salida .= ' selected="selected"';
                $salida .= '>' . $subclave . "</option>\n";
            }
            $salida .= "</optgroup>\n";
        } else {
            $salida .= '<option value="' . $c . '"';
            if ($c == $seleccionada) $salida .= ' selected="selected"';
            $salida .= '>' . $v . '</option>';
        }
    }
    $salida .= '</select>';
    return $salida;
}


/**
 * Fecha y hora en formato amigable. ¡OJO! Necesita la clase DateTime de SPL
 * 
 * @param   int     $time       La fecha original
 * @param   int     $day_limit  No se tiene en cuenta ahora
 * @return  string  Una cadena como "hace 2 días"
 */
function fecha_amigable($time, $day_limit = 5)
{
    $fecha = new DateTime(date('c', $time)); //Formato ISO 8601
    $ahora = new DateTime();
    
    $diferencia = date_diff($ahora, $fecha);
    
    if ($diferencia->y == 0 && $diferencia->m == 0 && $diferencia->d == 0) {
        if ($diferencia->h == 0 && $diferencia->i == 0) {
            if ($diferencia->s > 0) {
                return 'Hace ' . $diferencia->s . ' segundos';
            } else {
                return 'Ahora mismo';
            }
        }
        if ($diferencia->h == 0) {
            return 'Hace ' . $diferencia->i . ' minutos';
        } else {
            $salida = 'Hace ' . $diferencia->h . ' hora';
            if ($diferencia->h > 1) $salida .= 's';
            return $salida;
        }
    } else {
        if ($diferencia->y == 0 && $diferencia->m == 0) {
            if ($diferencia->d == 1) {
                return 'Ayer';
            } else {
                return 'Hace ' . $diferencia->d . ' días';
            }
        } else {
            return fecha_dia_mes($time);
        }
    }
}


/**
 * Formatea una fecha como "hace x tiempo". Si tiempo > 1 mes, formato español
 * @todo Revisar esta función (D&G)
 *
 * @param   int     $fecha_unix         La fecha en formato timestamp
 * @return  string  Fecha formateada
 */
function hace_tiempo($fecha_unix) {
    $ahora = time();

    //obtener la diferencia de segundos
    $segundos = $ahora - $fecha_unix;
    
    if ($segundos < 60) {
        return 'Hace ' . $segundos . ' segundos';
    }

    //dias es la division de n segs entre 86400 segundos que representa un dia
    $dias = floor($segundos / 86400);

    //mod_hora es el sobrante, en horas, de la division de días
    $mod_hora = $segundos % 86400;

    //hora es la division entre el sobrante de horas y 3600 segundos que representa +una hora
    $horas = floor($mod_hora / 3600);

    //mod_minuto es el sobrante, en minutos, de la division de horas
    $mod_minuto = $mod_hora % 3600;

    //minuto es la division entre el sobrante y 60 segundos que representa un minuto
    $minutos = floor($mod_minuto / 60);

    if ($horas <= 0) {
        return 'Hace ' . $minutos . ' minutos';
    } elseif ($dias <= 0) {
        return 'Hace ' . $horas . ' horas ';
    } else {
        return strftime('%e de %B', $fecha_unix);
    }
}


/**
 * Devuelve la fecha en formato día de mes [de año] si año != actual
 *
 * @param int $timestamp La fecha/hora en formato UNIX timestamp. Si no se
 * especifica, se tomará la fecha/hora actuales.
 * @return string La fecha formateada
 */
function fecha_dia_mes($timestamp=null)
{
    if ($timestamp == null) {
        $timestamp = intval(time());
    }

    $cadena = strftime('%e de %B', $timestamp);

    if (($anho = strftime('%Y', $timestamp)) != strftime('%Y')) {
        $cadena .= ' de ' . $anho;
    }

    return $cadena;
}


/**
 * Cut string to n symbols and add delim but do not break words.
 *
 * Example:
 * <code>
 *  $string = 'this sentence is way too long';
 *  echo cortar_texto($string, 16);
 * </code>
 *
 * Output: 'this sentence is...'
 *
 * @author  Justin Cook
 * @url     http://www.justin-cook.com/wp/2006/06/27/php-trim-a-string-without-cutting-any-words/
 * @param   string      string we are operating with
 * @param   integer     character count to cut to
 * @param   string|NULL delimiter. Default: '...'
 * @return  string      processed string
 */
function cortar_texto($str, $n, $delim='...') {

    $len = strlen($str);
    if ($len > $n) {
        preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
        return rtrim($matches[1]) . $delim;
    } else {
        return $str;
    }
}


/**
 * Imprime o devuelve "invalido" si el campo no ha sido validado.
 *
 * @param string    $campo                  Nombre del campo
 * @param array     $resultados_validacion  Resultados de validación
 * @param boolean   $devolver               Devolver en vez de imprimir
 * @return string   "invalido" si $devolver=true y el campo es inválido
 */
function invalido($campo, $resultados_validacion, $devolver=false)
{
    $salida = '';

    if (isset($resultados_validacion[$campo])) {
        $salida = 'no-valido';
    }

    if ($devolver === true)
        return $salida;
    else
        echo $salida;
}


/**
 * Imprime un parámetro POST si está definido
 * 
 * @param string $indice El nombre de la clave
 */
function post($indice, $devolver=false) {
    if (!$devolver) {
        if (isset($_POST[$indice])) echo $_POST[$indice];
    } else {
        if (isset($_POST[$indice])) return $_POST[$indice];
        else return null;
    }
}

/**
 * Imprime una cadena escapando caracteres UTF en HTML
 * @param type $cadena 
 */
function e($cadena) {
    echo htmlentities($cadena, null, 'UTF-8');
}