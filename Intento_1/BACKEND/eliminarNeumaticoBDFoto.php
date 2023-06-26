<?php
/*
eliminarNeumaticoBDFoto.php: Se recibe el parámetro neumatico_json (id, marca, medidas, precio y pathFoto
en formato de cadena JSON) por POST. Se deberá borrar el neumático (invocando al método eliminar).
Si se pudo borrar en la base de datos, invocar al método guardarEnArchivo.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
Si se invoca por GET (sin parámetros), se mostrarán en una tabla (HTML) la información de todos los neumáticos
borrados y sus respectivas imagenes.
*/
use Porfirio\Thiago\NeumaticoBD;

require_once "./clases/neumatico.php";
require_once "./clases/IParte1.php";
require_once "./clases/IParte2.php";
require_once "./clases/IParte3.php";
require_once "./clases/IParte4.php";
require_once "./clases/neumaticoBD.php";

    if($_SERVER['REQUEST_METHOD'] === 'GET')
    {

        $neumaticos = NeumaticoBD::traerEliminadosTXT("./archivos/neumaticosbd_borrados.txt");
        $tabla= "<table><tr><th>ID</th><th>MARCA</th><th>MEDIDAS</th><th>PRECIO</th><th>FOTO</th></tr>";
        //<th>ACCIONES</th></tr>";

        foreach($neumaticos as $neumatico)
        {
            $id = $neumatico->getId();
            $marca = $neumatico->getMarca();
            $medidas = $neumatico->getMedidas();
            $precio = $neumatico->getPrecio();
            $pathFoto = $neumatico->getPathFoto();

            $tabla.= "<tr><td>{$id}</td><td>{$marca}</td><td>{$medidas}</td><td>{$precio}</td>";
            $tabla.="<td><img src={$pathFoto} width= 50 height= 50></td></tr>";
        }   
    
        $tabla.= "</table>";

        $retorno = $tabla;
    }
    else
    {
        $neumatico_JSON = isset($_POST['neumatico_json']) ? $_POST["neumatico_json"] : false;
        $neumatico_std = json_decode($neumatico_JSON);

        $std_retorno = new stdClass();
        $std_retorno->exito = false;
        $std_retorno->mensaje = "No se pudo eliminar el neumatico de la base de datos";
    
        $neumatico = new NeumaticoBD($neumatico_std->marca, $neumatico_std->medidas, $neumatico_std->precio, $neumatico_std->id, 
        $neumatico_std->pathFoto);
    
        $neumatico = $neumatico->traerPorId($neumatico_std->id);
    
        if($neumatico != null)
        {
            $std_retorno->exito = NeumaticoBD::eliminar($neumatico_std->id);
    
            if($std_retorno->exito)
            {
                $std_retorno = json_decode($neumatico->guardarEnArchivo());
    
                if(!$std_retorno->exito)
                {
                    $std_retorno->mensaje = "El neumatico eliminado no pudo guardarse en neumaticos_eliminados.json";
                }
            }
    
        }    
        $retorno = json_encode($std_retorno);
    }
    
    echo $retorno;

?>