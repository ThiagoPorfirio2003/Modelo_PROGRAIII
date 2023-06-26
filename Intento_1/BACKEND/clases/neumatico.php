<?php

namespace Porfirio\Thiago;

use Exception;
use stdClass;

class Neumatico
{
    protected string $marca;
    protected string $medidas;
    protected float $precio;
    
    public function __construct(string $marca, string $medidas, float $precio=0)
    {
        $this->marca = $marca;
        $this->medidas = $medidas;
        $this->precio = $precio;
    }

    public function toJSON() : string
    {
        $stdNeumatico = new stdClass();

        $stdNeumatico->precio = $this->precio;
        $stdNeumatico->marca = $this->marca;
        $stdNeumatico->medidas = $this->medidas;

        return json_encode($stdNeumatico);    
    }

    public function guardarJSON(string $path) : string
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "No se pudo agregar el neumatico"; 

        try
        {
            $archivo = fopen($path, "a");

            if(fwrite($archivo, $this->toJSON() . "\r\n") > 0)
            {
                $retorno->exito = true;
                $retorno->mensaje = "Se pudo guardar en neumatico";             
            }

            fclose($archivo);
        }
        catch(Exception)
        {
            echo "error";
        }

        return json_encode($retorno);
    }

    public static function traerJSON(string $path) : array
    {
        $retorno = array();

        try
        {
            $archivo = fopen($path, "r");

            /*            
            $archivo_leido = fread($archivo, filesize($path));

            $retorno = explode("\r\n", $archivo_leido);
            */

            while(!feof($archivo))
            {
                $lineaLeida = fgets($archivo);
                $json_recuperado = trim($lineaLeida);

                if($json_recuperado != "")
                {
                    $std_neumatico = json_decode($json_recuperado);
                    array_push($retorno, new Neumatico($std_neumatico->marca, $std_neumatico->medidas, $std_neumatico->precio));
                }
            }

            fclose($archivo);
        }
        catch(Exception)
        {

        }

        return $retorno;
    }

    public function neumaticoCompare(Neumatico $neumatico)
    {
        return $this->marca == $neumatico->marca && $this->medidas == $neumatico->medidas;;
    }

    public static function verificarNeumaticoJSON(Neumatico $neumatico)
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "El neumatico no existe"; 
        $sumatoria = 0;

        try
        {
            $neumaticos_Recuperados = Neumatico::traerJSON("./archivos/neumaticos.json");

            foreach($neumaticos_Recuperados as $neumatico_AComparar)
            {
                if($neumatico->neumaticoCompare($neumatico_AComparar))
                {   
                    $retorno->exito = true;
                    $sumatoria += $neumatico_AComparar->precio;
                }
            }

        }
        catch(Exception)
        {
            echo "error";
        }

        if($sumatoria != 0)
        {
            $sumatoria += $neumatico->precio;
            $retorno->mensaje = $sumatoria;
        }

        return json_encode($retorno);
    }


}
?>