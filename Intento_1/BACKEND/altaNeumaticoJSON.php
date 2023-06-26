<?php
/*
listadoNeumaticosJSON.php: (GET) Se mostrará el listado de todos los neumáticos en formato JSON (traerJSON).
Pasarle './archivos/neumaticos.json' cómo parámetro.
*/

use Porfirio\Thiago\Neumatico;

require_once "./clases/neumatico.php";

    $marca = isset($_POST["marca"]) ? $_POST["marca"] : false;
    $medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : false;
    $precio = isset($_POST["precio"]) ? $_POST["precio"] : false;

    $neumatio = new Neumatico($marca, $medidas, $precio);
    $retorno = $neumatio->guardarJSON("./archivos/neumaticos.json");

    echo $retorno;
?>