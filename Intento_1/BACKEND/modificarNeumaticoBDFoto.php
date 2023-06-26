<?php

/*
modificarNeumaticoBDFoto.php: Se recibirán por POST los siguientes valores: neumatico_json (id, marca,
medidas y precio, en formato de cadena JSON) y la foto (para modificar un neumático en la base de datos).
Invocar al método modificar.
Nota: El valor del id, será el id del neumático 'original', mientras que el resto de los valores serán los del neumático
a ser modificado.
Si se pudo modificar en la base de datos, la foto original del registro modificado se moverá al subdirectorio
“./neumaticosModificados/”, con el nombre formado por el id punto marca punto 'modificado' punto hora,
minutos y segundos de la modificación (Ejemplo: 987.fateo.modificado.105905.jpg).
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
    $foto = isset($_FILES['foto']) ? $_FILES["foto"] : false;

    $retorno = new stdClass();
    $retorno->mensaje = "No se pudo modificar el neumatico de la base de datos";
    $retorno->exito = false; 

    $neumatico_std = json_decode($neumatico_JSON);
    $neumaticoOriginal = NeumaticoBD::traerPorId($neumatico_std->id);

    if($neumaticoOriginal != null)
    {
        $pathOriginal = $neumaticoOriginal->getPathFoto();
        $extensionNueva = pathinfo($foto["name"], PATHINFO_EXTENSION);
        $existeFotoOriginal = file_exists($pathOriginal);

        if($existeFotoOriginal)
        {
            $partesPathOriginal = explode(".",$pathOriginal);
            $extension_Original= trim($partesPathOriginal[3]);
        
            $pathNuevaFoto = "./neumaticos/imagenes/" . $neumatico_std->marca . "." . $partesPathOriginal[2] . "." 
            . $extensionNueva;
            $pathModificado = "./neumaticosModificados/" . $neumaticoOriginal->getId() . "." . 
            $neumaticoOriginal->getMarca() . ".modificado." . date("his") . "." . $extension_Original;
        }
        else
        {
            $pathNuevaFoto = $pathFoto = "./neumaticos/imagenes/" . $neumatico_std->marca . "." . date("his") . "." .
            $extensionNueva; 
        }
    
        $neumatico = new NeumaticoBD($neumatico_std->marca, $neumatico_std->medidas, 
        $neumatico_std->precio, $neumatico_std->id, $pathNuevaFoto);
    
        if($neumatico->modificar())
        {
            $retorno->mensaje = "El neumatico ha sido modificado con exito";
            if((($existeFotoOriginal && rename($pathOriginal, $pathModificado)) || !$existeFotoOriginal)
                && move_uploaded_file($foto["tmp_name"], $pathNuevaFoto))
            {
                $retorno->exito = true;
            }
            else
            {
                $retorno->mensaje .= "Pero hubo un error al hora de modificar la foto";
            }
        }
    }
    


    $retorno->exito = $neumatico->modificar();

    if($retorno->exito)
    {
        $retorno->mensaje = "El neumatico ha sido modificado con exito";
    }

    echo json_encode($retorno);
?>