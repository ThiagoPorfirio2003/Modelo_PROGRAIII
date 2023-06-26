<?php

namespace Porfirio\Thiago;

use PDO;
use Exception;
use PDOException;
use IPart1;
use IPart2;
use IPart3;
use IPart4;
use stdClass;

class NeumaticoBD extends Neumatico implements IPart1, IPart2,IPart3, IPart4
{
    protected int $id;
    protected string $pathFoto;

    public function __construct(string $marca, string $medidas, float $precio=0, int $id=-1, string $pathFoto = "fake.jpg")
    {
        parent::__construct($marca,$medidas,$precio);
        $this->id = $id;
        $this->pathFoto = $pathFoto;
    }

    public function getId() : int
    {
        return $this->id;
    }

    public function getMarca() : string
    {
        return $this->marca;
    }

    public function getMedidas() : string
    {
        return $this->medidas;
    }

    public function getPrecio() : float
    {
        return $this->precio;
    }
    
    public function getPathFoto() : string
    {
        return $this->pathFoto;
    }

    public function toJSON(): string
    {
        $stdNeumaticoBD = new stdClass();

        $stdNeumaticoBD->id= $this->id;
        $stdNeumaticoBD->marca = $this->marca;
        $stdNeumaticoBD->medidas = $this->medidas;
        $stdNeumaticoBD->precio = $this->precio;
        $stdNeumaticoBD->pathFoto = $this->pathFoto;

        return json_encode($stdNeumaticoBD); 
    }

    public function agregar(): bool
    {
        $retorno = false;

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->prepare('INSERT INTO neumaticos (marca, medidas, precio, foto) VALUES
            (:marca, :medidas, :precio, :foto)');

            $pdoStatement->bindValue(':marca', $this->marca, PDO::PARAM_STR);
            $pdoStatement->bindValue(':medidas', $this->medidas, PDO::PARAM_STR);
            $pdoStatement->bindValue(':precio', $this->precio, PDO::PARAM_INT);
            $pdoStatement->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);

            $retorno = $pdoStatement->execute();
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }
    
    public static function traer(): array
    {
        $retorno = array();

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->query('SELECT * FROM neumaticos');

            foreach($pdoStatement->fetchAll() as $registro)
            {
                $foto = isset($registro['foto']) ? $registro['foto'] : "";
                array_push($retorno, new NeumaticoBD($registro['marca'], $registro['medidas'], $registro['precio'], $registro['id'],
                $foto));
            }
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    public static function traerEliminadosTXT(string $path) : array
    {
        $retorno = array();

        try
        {
            $archivo = fopen($path, "r");

            while(!feof($archivo))
            {
                $lineaLeida = fgets($archivo);
                $json_recuperado = trim($lineaLeida);

                if($json_recuperado != "")
                {
                    $std_neumatico = json_decode($json_recuperado);
                    array_push($retorno, new NeumaticoBD($std_neumatico->marca, $std_neumatico->medidas, $std_neumatico->precio,
                    $std_neumatico->id, $std_neumatico->pathFoto));
                }
            }

            fclose($archivo);
        }
        catch(Exception)
        {

        }

        return $retorno;
    }

    public static function traerUno(string $marca, string $medidas) : null | NeumaticoBD
    {
        $retorno = null;

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->prepare('SELECT * FROM neumaticos WHERE marca = :marca AND medidas = :medidas');

            $pdoStatement->bindValue(':marca', $marca, PDO::PARAM_STR);
            $pdoStatement->bindValue(':medidas', $medidas, PDO::PARAM_STR);

            if($pdoStatement->execute())
            {
                $registro = $pdoStatement->fetch();

                $foto = isset($registro['foto']) ? $registro['foto'] : "";
                $retorno = new NeumaticoBD($registro['marca'], $registro['medidas'], $registro['precio'], $registro['id'],
                            $foto);
            
            }

        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    public static function traerPorId(int $id) : NeumaticoBD | null
    {
        $retorno = null;
        $neumaticos = NeumaticoBD::traer();
        
        foreach($neumaticos as $neumatico)
        {
            if($neumatico->id == $id)
            {
                $retorno = $neumatico;
                break;
            }
        }

        return $retorno;    
    }

    public static function eliminar(int $id): bool
    {
        $retorno = false;

        try
        {
            if(NeumaticoBD::traerPorId($id) != null)
            {
                $pdo = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");

                $pdoStatement = $pdo->prepare('DELETE FROM neumaticos WHERE id = :id');
    
                $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);
    
                $retorno = $pdoStatement->execute();
            }
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    public function modificar(): bool
    {
        $retorno = false;

        try
        {
            $neumaticoAModificar = $this->traerPorId($this->id);

            if($neumaticoAModificar != null)
            {
                $pdo = new PDO('mysql:host=localhost;dbname=gomeria_bd;charset=utf8', "root", "");

                $pdoStatement = $pdo->prepare('UPDATE neumaticos SET marca = :marca , medidas = :medidas , precio = :precio , 
                foto = :foto WHERE id =:id');
    
                $pdoStatement->bindValue(':marca', $this->marca, PDO::PARAM_STR);
                $pdoStatement->bindValue(':medidas', $this->medidas, PDO::PARAM_STR);
                $pdoStatement->bindValue(':precio', $this->precio, PDO::PARAM_INT);
                $pdoStatement->bindValue(':foto', $this->pathFoto, PDO::PARAM_STR);
                $pdoStatement->bindValue(':id', $this->id, PDO::PARAM_INT);
    
                $retorno = $pdoStatement->execute();
            }
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    public function existe(array $neumaticos): bool
    {
        $retorno = false;

        foreach($neumaticos as $neumatico)
        {
            if($this->neumaticoCompare($neumatico))
            {
                $retorno = true;
                break;
            }
        }

        return $retorno;
    }

    public function guardarEnArchivo(): string
    {
        $retorno = new stdClass();
        $retorno->exito = false;
        $retorno->mensaje = "No se pudo agregar el neumatico al archivo de los borrados"; 

        $viejoPathFoto = $this->pathFoto;
        $nuevoPathFoto = "./neumaticosBorrados/" . $this->id . "." . $this->marca . ".borrado." . date("his") . "." . 
        pathinfo($this->pathFoto, PATHINFO_EXTENSION);
        $this->pathFoto = $nuevoPathFoto;

        try
        {
            $archivo = fopen("./archivos/neumaticosbd_borrados.txt", "a");

            $retorno->exito = fwrite($archivo, $this->toJSON() . "\r\n") > 0;
            
            if($retorno->exito)
            {
                $retorno->mensaje = "El neumatico se ha guardado en el archivo de los borrados";
                if(file_exists($viejoPathFoto))
                {
                    $retorno->exito = rename($viejoPathFoto, $nuevoPathFoto);

                    if(!$retorno->exito)
                    {
                        $retorno->mensaje .= ", pero no la foto";             
                    }
                }             
            }

            fclose($archivo);
        }
        catch(Exception)
        {

        }

        return json_encode($retorno);
    }

    public static function mostrarBorradosJSON(string $path) : string
    {
        $neumaticos = self::traerEliminadosTXT($path);
        $tabla= "<table><tr><th>ID</th><th>MARCA</th><th>MEDIDAS</th><th>PRECIO</th><th>FOTO</th></tr>";

        if(count($neumaticos) > 0)
        {
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
        }

        $tabla.= "</table>";
        
        return $tabla;
    }

    /*
    mostrarFotosDeModificados.php: Muestra (en una tabla HTML) todas las imagenes (50px X 50px) de los
neumáticos registrados en el directorio “./neumaticosModificados/”. Para ello, agregar un método estático (en
NeumaticoBD), llamado mostrarModificados.
    */

    public static function mostrarModificados() : string
    {
        $directorio = "./neumaticosModificados/";
        $tabla = "<table><tr><th>Modificadas</th></tr>";

        $archivos = scandir($directorio);

        if($archivos != false)
        {
            foreach($archivos as $nombre)
            {
                if($nombre != "." && $nombre != "..")
                {
                    //$pathFoto = $directorio . $nombre;

                    $pathFoto = "./BACKEND/neumaticosModificados/" . $nombre;

                    $tabla.="<td><img src={$pathFoto} width= 50 height= 50></td></tr>";
                }
            }
        }

        $tabla .= "</table>";

        return $tabla;
    }
}
?>