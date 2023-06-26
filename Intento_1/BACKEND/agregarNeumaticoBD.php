<?php
/*
agregarNeumaticoBD.php: Se recibirán por POST los valores: marca, medidas, precio y la foto para registrar un
neumático en la base de datos.
Verificar la previa existencia del neumático invocando al método existe. Se le pasará como parámetro el array que
retorna el método traer.
Si el neumático ya existe en la base de datos, se retornará un mensaje que indique lo acontecido.
Si el neumático no existe, se invocará al método agregar. La imagen se guardará en “./neumaticos/imagenes/”,
con el nombre formado por el marca punto hora, minutos y segundos del alta (Ejemplo: pirelli.105905.jpg).
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido
*/

use Porfirio\Thiago\NeumaticoBD;

    require_once "./clases/neumatico.php";
    require_once "./clases/IParte1.php";
    require_once "./clases/IParte2.php";
    require_once "./clases/IParte3.php";
    require_once "./clases/IParte4.php";
    require_once "./clases/neumaticoBD.php";

    $marca = isset($_POST["marca"]) ? $_POST["marca"] : false;
    $medidas = isset($_POST["medidas"]) ? $_POST["medidas"] : false;
    $precio = isset($_POST["precio"]) ? $_POST["precio"] : false;
    $foto = $_FILES["foto"];

    $retorno = new stdClass();
    $retorno->mensaje = "No se pudo agregar el neumatico a la base de datos";

    $pathFoto = "./neumaticos/imagenes/" . $marca . "." . date("his") . "." . pathinfo($foto["name"], PATHINFO_EXTENSION);
    $neumatico = new NeumaticoBD($marca, $medidas, $precio, -1, $pathFoto);//, 10,"fake.jpg");

    if($neumatico->existe(NeumaticoBD::traer()))
    {
        $retorno->mensaje = "No se pudo agregar el neumatico a la base de datos porque ya existe";
        $retorno->resultado = false;
    }
    else
    {
        $retorno->resultado = $neumatico->agregar();

        if($retorno->resultado)
        {
            $retorno->mensaje = "El neumatico ha sido agregado con exito";
            $retorno->resultado = move_uploaded_file($foto["tmp_name"], $pathFoto);

            if(!$retorno->resultado)
            {
                $retorno->mensaje .= ", pero la foto no se pudo guardar";
            }
        }
    }

    echo json_encode($retorno);

?>