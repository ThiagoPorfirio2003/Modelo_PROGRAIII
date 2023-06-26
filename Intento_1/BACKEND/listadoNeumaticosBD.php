<?php
/*
listadoNeumaticosBD.php: (GET) Se mostrará el listado completo de los neumáticos (obtenidos de la base de
datos) en una tabla (HTML con cabecera). Invocar al método traer.

Nota: Si se recibe el parámetro tabla con el valor mostrar, retornará los datos en una tabla (HTML con cabecera),
preparar la tabla para que muestre la imagen, si es que la tiene.
Si el parámetro no es pasado o no contiene el valor mostrar, retornará el array de objetos con formato JSON.
*/
use Porfirio\Thiago\NeumaticoBD;

require_once "./clases/neumatico.php";
require_once "./clases/IParte1.php";
require_once "./clases/IParte2.php";
require_once "./clases/IParte3.php";
require_once "./clases/IParte4.php";
require_once "./clases/neumaticoBD.php";

    $tabla = isset($_GET["tabla"]) ? $_GET["tabla"] : false;
    $retorno = "[";

    $neumaticos =  NeumaticoBD::traer();

    if($tabla == "mostrar")
    {
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
        $cantidadNeumaticos = count($neumaticos);

        for($i = 0; $i < $cantidadNeumaticos; $i++)
        {
            $retorno.= $neumaticos[$i]->toJSON();
            if($i != $cantidadNeumaticos-1)
            {
                $retorno.=",";
            }
        }
    
        $retorno .= "]";
    }
    
    echo $retorno;
?>