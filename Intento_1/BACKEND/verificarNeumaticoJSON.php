<?php
/*
listadoNeumaticosJSON.php: (GET) Se mostrará el listado de todos los neumáticos en formato JSON (traerJSON).
Pasarle './archivos/neumaticos.json' cómo parámetro.

verificarNeumaticoJSON.php: Se recibe por POST la marca y las medidas.
Retornar un JSON que contendrá: éxito(bool) y mensaje(string) (agregar el mensaje obtenido del método
verificarNeumaticoJSON).

agregarNeumaticoSinFoto.php: Se recibe por POST el parámetro neumático_json (marca, medidas y precio), en
formato de cadena JSON. Se invocará al método agregar.

Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
listadoNeumaticosBD.php: (GET) Se mostrará el listado completo de los neumáticos (obtenidos de la base de
datos) en una tabla (HTML con cabecera). Invocar al método traer.

Nota: Si se recibe el parámetro tabla con el valor mostrar, retornará los datos en una tabla (HTML con cabecera),
preparar la tabla para que muestre la imagen, si es que la tiene.
Si el parámetro no es pasado o no contiene el valor mostrar, retornará el array de objetos con formato JSON.

*/

use Porfirio\Thiago\Neumatico;

require_once "./clases/neumatico.php";

    $marca = $_POST["marca"];
    $medidas = $_POST["medidas"];

    $neumatico = new Neumatico($marca,$medidas);

    echo Neumatico::verificarNeumaticoJSON($neumatico);
?>