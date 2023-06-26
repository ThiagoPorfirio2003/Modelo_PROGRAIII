<?php
/*
agregarNeumaticoSinFoto.php: Se recibe por POST el parámetro neumático_json (marca, medidas y precio), en
formato de cadena JSON. Se invocará al método agregar.
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.

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

    $neumatico_JSON = isset($_POST['neumatico_json']) ? $_POST["neumatico_json"] : false;

    $retorno = new stdClass();
    $retorno->mensaje = "No se pudo agregar el neumatico a la base de datos";

    $neumatico_std = json_decode($neumatico_JSON);
    $neumatico = new NeumaticoBD($neumatico_std->marca, $neumatico_std->medidas, $neumatico_std->precio);//, 10,"fake.jpg");

    $retorno->resultado = $neumatico->agregar();

    if($retorno->resultado)
    {
        $retorno->mensaje = "El neumatico ha sido agregado con exito";
    }

    echo json_encode($retorno);

?>