<?php
/*
listadoNeumaticosJSON.php: (GET) Se mostrará el listado de todos los neumáticos en formato JSON (traerJSON).
Pasarle './archivos/neumaticos.json' cómo parámetro.

verificarNeumaticoJSON.php: Se recibe por POST la marca y las medidas.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) (agregar el mensaje obtenido del método
verificarNeumaticoJSON).
*/
use Porfirio\Thiago\Neumatico;

require_once "./clases/neumatico.php";

    $retorno = "[";

    $neumaticos = Neumatico::traerJSON("./archivos/neumaticos.json"); 
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

    echo $retorno;
?>