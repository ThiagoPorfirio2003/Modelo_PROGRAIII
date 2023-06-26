<?php
/*
modificarNeumaticoBD.php: Se recibirán por POST los siguientes valores: neumatico_json (id, marca, medidas y
precio, en formato de cadena JSON) para modificar un neumático en la base de datos. Invocar al método
modificar.

Nota: El valor del id, será el id del neumático 'original', mientras que el resto de los valores serán los del neumático
a ser modificado.
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.

*/

use Porfirio\Thiago\NeumaticoBD;

require_once "./clases/neumatico.php";
require_once "./clases/IParte1.php";
require_once "./clases/IParte2.php";
require_once "./clases/IParte3.php";
require_once "./clases/IParte4.php";
require_once "./clases/neumaticoBD.php";

    $neumatico_JSON = isset($_POST['neumatico_json']) ? $_POST["neumatico_json"] : false;

    //echo var_dump($neumatico_JSON);

    $retorno = new stdClass();
    $retorno->mensaje = "No se pudo modificar el neumatico de la base de datos";
    $retorno->exito = false;

    $neumatico_std = json_decode($neumatico_JSON);
    $neumatico = new NeumaticoBD($neumatico_std->marca, $neumatico_std->medidas, $neumatico_std->precio, $neumatico_std->id);

    $retorno->exito = $neumatico->modificar();

    if($retorno->exito)
    {
        $retorno->mensaje = "El neumatico ha sido modificado con exito";
    }

    echo json_encode($retorno);
?>