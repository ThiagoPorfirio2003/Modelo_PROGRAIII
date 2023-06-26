<?php
    /*

verificarNeumaticoBD.php: Se recibe por POST el parámetro obj_neumatico, que será una cade a JSON (marca y
medidas), si coincide con algún registro de la base de datos (invocar al método traer) retornará los datos del
objeto (invocar al toJSON). Caso contrario, un JSON vacío ({}).
    */

    use Porfirio\Thiago\NeumaticoBD;

    require_once "./clases/neumatico.php";
    require_once "./clases/IParte1.php";
    require_once "./clases/IParte2.php";
    require_once "./clases/IParte3.php";
    require_once "./clases/IParte4.php";
    require_once "./clases/neumaticoBD.php";

    $neumatico_JSON = $_POST['obj_neumatico'];

    $retorno = "{}";

    $neumatico_std = json_decode($neumatico_JSON);
    $neumatico = new NeumaticoBD($neumatico_std->marca, $neumatico_std->medidas);

    if($neumatico->existe(NeumaticoBD::traer()))
    {
        $neumaticoRecuperado = NeumaticoBD::traerUno($neumatico_std->marca, $neumatico_std->medidas);
        
        if($neumaticoRecuperado != null)
        {
            $retorno = $neumaticoRecuperado->toJSON();
        }
    }

    echo $retorno;
?>