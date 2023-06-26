<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Auto
{
    public int $id;
    public string $color;
    public string $marca;
    public int $precio;
    public string $modelo;

    /*
        (GET) Listado de autos. Mostrará el listado completo de los autos (array JSON).
        Retorna un JSON (éxito: true/false; mensaje: string; tabla: stringJSON; status: 200/424)
    */
    public static function traerListadoAutos(Request $request, Response $response, array $args): Response
    {
        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Hubo un error con la base de datos";
        $stdResponse->status = 424;
        $stdResponse->exito = false;
        $stdResponse->tabla = array();

        $registros = self::recuperarDeBD();

        if(count($registros) > 0)
        {    
            $stdResponse->mensaje = "Los autos han sido leidos exitosamente";
            $stdResponse->status = 200;
            $stdResponse->tabla = $registros;
            $stdResponse->exito = true;
        }

        $newResponse = $response->withStatus($stdResponse->status);
		$newResponse->getBody()->write(json_encode($stdResponse));
		return $newResponse->withHeader('Content-Type', 'application/json');	
    } 

    public static function recuperarDeBD(): array 
    {
        $retorno = array();

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=concesionaria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->query('SELECT * FROM autos');
            $pdoStatement->execute();
            $registros =  $pdoStatement->fetchAll(PDO::FETCH_CLASS, "Auto");
            if($registros != false)
            {
                /*
                foreach($registros as $usuario)
                {
                    $usuario->foto = __DIR__  . $usuario->foto;
                }*/

                $retorno = $registros;

            }	
        }
        catch(PDOException)
        {

        }
        return $retorno;
    }

    /*
    A nivel de aplicación:
    (POST) Alta de autos. Se agregará un nuevo registro en la tabla autos *.
    Se envía un JSON → auto (color, marca, precio y modelo).
    * ID auto-incremental.
    Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    */
    public static function altaAuto(Request $request, Response $response, array $args): Response 
    {
        $arrayBody = $request->getParsedBody();
        $autoNuevo = json_decode($arrayBody["auto"]);
        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Hubo un error con la base de datos";
        $stdResponse->status = 418;
        $stdResponse->exito = false;

        if(self::agregarAuto($autoNuevo))
        {
            $stdResponse->mensaje = "El auto ha sido agregado con exito";
            $stdResponse->status = 200;
            $stdResponse->exito = true;
        }

        $retorno = $response->withStatus($stdResponse->status);
        $retorno->getBody()->write(json_encode($stdResponse));

        return $retorno->withHeader('Content-Type', 'application/json');
    }

    public static function agregarAuto(stdClass $autoNuevo) : bool
    {
        $retorno = false;
        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=concesionaria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->prepare('INSERT INTO autos (color, marca, precio, modelo) VALUES
            (:color, :marca, :precio, :modelo)');

            $pdoStatement->bindValue(':color', $autoNuevo->color, PDO::PARAM_STR);
            $pdoStatement->bindValue(':marca', $autoNuevo->marca, PDO::PARAM_STR);
            $pdoStatement->bindValue(':precio', $autoNuevo->precio, PDO::PARAM_INT);
            $pdoStatement->bindValue(':modelo', $autoNuevo->modelo, PDO::PARAM_STR);

            $retorno = $pdoStatement->execute();
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

//-----------------------------------------------------------------------------------------------------------------------------------------------//

    /*
    (DELETE) Borrado de autos por ID.
    Recibe el ID del auto a ser borrado (id_auto) más el JWT → token (en el header).
    Si el perfil es ‘propietario’ se borrará de la base de datos. Caso contrario, se mostrará el
    mensaje correspondiente (indicando que usuario intentó realizar la acción).
    Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)

    (PUT) Modificar los autos por ID.
    Recibe el JSON del auto a ser modificado (auto), el ID (id_auto) y el JWT → token (en el
    header).
    Si el perfil es ‘encargado’ se modificará de la base de datos. Caso contrario, se mostrará
    el mensaje correspondiente (indicando que usuario intentó realizar la acción).
    Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    */

    public static function borrarAuto(Request $request, Response $response, array $args): Response
    {
        //$arrayBody = $request->getParsedBody();
        //$id_auto = $arrayBody["id_auto"];
        $id_auto = $request->getHeader("id_auto")[0];

        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Hubo un error con la eliminacion";
        $stdResponse->status = 418;
        $stdResponse->exito = false;
        
        if(self::borrarPorId($id_auto))
        {    
            $stdResponse->mensaje = "El auto ha sido eliminado exitosamente";
            $stdResponse->status = 200;
            $stdResponse->exito = true;
        }

        $newResponse = $response->withStatus($stdResponse->status);
		$newResponse->getBody()->write(json_encode($stdResponse));
		return $newResponse->withHeader('Content-Type', 'application/json');	
    }

    public static function borrarPorId(int $id)
    {
        $retorno = false;

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=concesionaria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->prepare('DELETE FROM autos WHERE id = :id');

            $pdoStatement->bindValue(':id', $id, PDO::PARAM_INT);

            $retorno = $pdoStatement->execute();
            
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    /*
        (PUT) Modificar los autos por ID.
    Recibe el JSON del auto a ser modificado (auto), el ID (id_auto) y el JWT → token (en el
    header).
    Si el perfil es ‘encargado’ se modificará de la base de datos. Caso contrario, se mostrará
    el mensaje correspondiente (indicando que usuario intentó realizar la acción).
    Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
    */
    
    public static function modificarAuto(Request $request, Response $response, array $args): Response
    {
        //$autoAModificar = $request->getHeader("auto")[0];
        $autoAModificar = json_decode($request->getHeader("auto")[0]);
        
        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Hubo un error en la eliminacion";
        $stdResponse->status = 418;
        $stdResponse->exito = false;
        
        if(self::modificar($autoAModificar))
        {    
            $stdResponse->mensaje = "El auto ha sido modificado exitosamente";
            $stdResponse->status = 200;
            $stdResponse->exito = true;
        }

        $newResponse = $response->withStatus($stdResponse->status);
		$newResponse->getBody()->write(json_encode($stdResponse));
		return $newResponse->withHeader('Content-Type', 'application/json');	
    }

    public static function modificar(stdClass $autoModif): bool
    {
        $retorno = false;

        try
        {
            //$neumaticoAModificar = $this->traerPorId($this->id);

            /*
            public int $id;
    public string $color;
    public string $marca;
    public int $precio;
    public string $modelo;
            */
            //if($neumaticoAModificar != null)
           // {
                $pdo = new PDO('mysql:host=localhost;dbname=concesionaria_bd;charset=utf8', "root", "");

                $pdoStatement = $pdo->prepare('UPDATE autos SET marca = :marca , color = :color , precio = :precio , 
                modelo = :modelo WHERE id =:id');
                
                $pdoStatement->bindValue(':color', $autoModif->color, PDO::PARAM_STR);
                $pdoStatement->bindValue(':marca', $autoModif->marca, PDO::PARAM_STR);
                $pdoStatement->bindValue(':precio', $autoModif->precio, PDO::PARAM_INT);
                $pdoStatement->bindValue(':modelo', $autoModif->modelo, PDO::PARAM_STR);
                $pdoStatement->bindValue(':id', $autoModif->id, PDO::PARAM_INT);
    
                $retorno = $pdoStatement->execute();
          //  }
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }
}
?>