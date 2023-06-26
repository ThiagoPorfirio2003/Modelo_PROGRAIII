<?php
/*
eliminarNeumaticoBD.php: Recibe el parámetro neumatico_json (id, marca, medidas y precio, en formato de
cadena JSON) por POST y se deberá borrar el neumático (invocando al método eliminar).
Si se pudo borrar en la base de datos, invocar al método guardarJSON y pasarle cómo parámetro el valor
'./archivos/neumaticos_eliminados.json'.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.

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

    $retorno = new stdClass();
    $retorno->mensaje = "No se pudo eliminar el neumatico de la base de datos";
    $retorno->exito = false;

    $neumatico_std = json_decode($neumatico_JSON);
    $neumatico = new NeumaticoBD($neumatico_std->marca, $neumatico_std->medidas, $neumatico_std->precio, $neumatico_std->id);

    $neumatico = $neumatico->traerPorId($neumatico_std->id);

    if($neumatico != null)
    {
        $retorno->exito = NeumaticoBD::eliminar($neumatico_std->id);

        if($retorno->exito)
        {
            $retorno->mensaje = "El neumatico ha sido eliminado con exito";
            $std_retornoGuardarArchivo = json_decode($neumatico->guardarJSON("./archivos/neumaticos_eliminados.json"));

            if(!$retorno->exito)
            {
                $retorno->mensaje = "El neumatico eliminado no pudo guardarse en neumaticos_eliminados.json";
            }
        }
    }

    

    echo json_encode($retorno);
?>