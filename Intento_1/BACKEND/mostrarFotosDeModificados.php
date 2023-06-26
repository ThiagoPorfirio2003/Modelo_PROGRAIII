<?php
/*
mostrarFotosDeModificados.php: Muestra (en una tabla HTML) todas las imagenes (50px X 50px) de los
neumáticos registrados en el directorio “./neumaticosModificados/”. Para ello, agregar un método estático (en
NeumaticoBD), llamado mostrarModificados.
*/

use Porfirio\Thiago\NeumaticoBD;

require_once "./clases/neumatico.php";
require_once "./clases/IParte1.php";
require_once "./clases/IParte2.php";
require_once "./clases/IParte3.php";
require_once "./clases/IParte4.php";
require_once "./clases/neumaticoBD.php";

    echo NeumaticoBD::mostrarModificados();
?>