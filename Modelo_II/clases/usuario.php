<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;


class Usuario
{
    public int $id;
    public string $correo;
    public string $clave;
    public string $nombre;
    public string $apellido;
    public string $perfil;
    public string $foto;

    private static $CLAVE = "MI_CLAVE";
    private static array $FORMATO = ['HS256'];

    public static function traerListadoUsuarios(Request $request, Response $response, array $args): Response
    {
        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Hubo un error con la base de datos";
        $stdResponse->status = 424;
        $stdResponse->exito = false;
        $stdResponse->tabla = array();

        $registros = self::recuperarDeBD();

        if(count($registros) > 0)
        {    
            $stdResponse->mensaje = "Los usuarios han sido leidos exitosamente";
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

            $pdoStatement = $pdo->query('SELECT * FROM usuarios');
            $pdoStatement->execute();
            $registros =  $pdoStatement->fetchAll(PDO::FETCH_CLASS, "Usuario");
            
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
A nivel de ruta (/usuarios):
(POST) Alta de usuarios. Se agregará un nuevo registro en la tabla usuarios *.
Se envía un JSON → usuario (correo, clave, nombre, apellido, perfil**) y foto.
La foto se guardará en ./fotos, con el siguiente formato: correo_id.extension.
Ejemplo: ./fotos/juan@perez_152.jpg
* ID auto-incremental. ** propietario, encargado y empleado.
Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)
*/
	public static function altaUsuario(Request $request, Response $response, array $args): Response 
    {
        $arrayBody = $request->getParsedBody();
        $usuario = json_decode($arrayBody["usuario"]);
        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Hubo un error con la base de datos";
        $stdResponse->status = 418;
        $stdResponse->exito = false;

        $archivos = $request->getUploadedFiles();
        //$destino = __DIR__ . "/../fotos/";

        $nombreAnterior = $archivos['foto']->getClientFilename();
        $extension = explode(".", $nombreAnterior);
        $extension = array_reverse($extension);

        //$pathFoto = __DIR__ . "./fotos/" . $usuario->correo . "." . $extension[0];
        //$pathFoto = "./fotos/" . $usuario->correo . "." . $extension[0];
        $usuario->foto = "./fotos/" . $usuario->correo . "." . $extension[0];

        if(self::agregarUsuario($usuario))
        {
            $archivos['foto']->moveTo($usuario->foto);
            $stdResponse->mensaje = "El usuario ha sido agregado con exito";
            $stdResponse->status = 200;
            $stdResponse->exito = true;
        }


        $retorno = $response->withStatus($stdResponse->status);
        $retorno->getBody()->write(json_encode($stdResponse));

        return $retorno->withHeader('Content-Type', 'application/json');
    }

    public static function agregarUsuario(stdClass $usuarioRecuperado) : bool
    {
        $retorno = false;
        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=concesionaria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->prepare('INSERT INTO usuarios (correo, clave, nombre, apellido, perfil, foto) VALUES
            (:correo, :clave, :nombre, :apellido, :perfil, :foto)');

            $pdoStatement->bindValue(':correo', $usuarioRecuperado->correo, PDO::PARAM_STR);
            $pdoStatement->bindValue(':clave', $usuarioRecuperado->clave, PDO::PARAM_STR);
            $pdoStatement->bindValue(':nombre', $usuarioRecuperado->nombre, PDO::PARAM_STR);
            $pdoStatement->bindValue(':apellido', $usuarioRecuperado->apellido, PDO::PARAM_STR);
            $pdoStatement->bindValue(':perfil', $usuarioRecuperado->perfil, PDO::PARAM_STR);
            $pdoStatement->bindValue(':foto', $usuarioRecuperado->foto, PDO::PARAM_STR);

            $retorno = $pdoStatement->execute();
        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    public static function traerSegunDatosDeInicio(string $correo, string $clave) : null | Usuario
    {
        $retorno = null;

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=concesionaria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->prepare('SELECT * FROM usuarios WHERE correo = :correo AND clave = :clave');

            $pdoStatement->bindValue(':correo', $correo, PDO::PARAM_STR);
            $pdoStatement->bindValue(':clave', $clave, PDO::PARAM_STR);

            if($pdoStatement->execute())
            {
                $registros = $pdoStatement->fetchAll(PDO::FETCH_CLASS, "Usuario");
                if($registros != false && count($registros) > 0)
                {
                    $retorno = $registros[0];
                }
                /*
                $registro = $pdoStatement->fetch();
                if($registro != false && count($registro) > 0)
                {
                    $retorno = new Usuario();
                    $retorno->correo = $registro["correo"];
                    $retorno->clave = $registro["clave"];
                }*/
            }

        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    public static function traerSegunCorreo(string $correo) : null | Usuario
    {
        $retorno = null;

        try
        {
            $pdo = new PDO('mysql:host=localhost;dbname=concesionaria_bd;charset=utf8', "root", "");

            $pdoStatement = $pdo->prepare('SELECT * FROM usuarios WHERE correo = :correo');

            $pdoStatement->bindValue(':correo', $correo, PDO::PARAM_STR);

            if($pdoStatement->execute())
            {
                $registros = $pdoStatement->fetchAll(PDO::FETCH_CLASS, "Usuario");
                if($registros != false && count($registros) > 0)
                {
                    $retorno = $registros[0];
                }
                /*
                $registro = $pdoStatement->fetch();
                if(count($registro) > 0)
                {
                    $retorno = new Usuario();
                    $retorno->correo = $registro["correo"];
                }*/            
            }

        }
        catch(PDOException)
        {

        }

        return $retorno;
    }

    /*
    A nivel de ruta (/login):
(POST) Se envía un JSON → user (correo y clave) y retorna un JSON (éxito: true/false; jwt: JWT
(con todos los datos del usuario) / null; status: 200/403)
    */
    public static function login(Request $request, Response $response, array $args): Response 
    {
        $arrayBody = $request->getParsedBody();
        $usuarioRecuperado = json_decode($arrayBody["usuario"]);
        $stdResponse = new stdClass();
        $stdResponse->jwt = null;
        $stdResponse->status = 403;
        $stdResponse->exito = false;

        $usuarioCompleto = self::traerSegunDatosDeInicio($usuarioRecuperado->correo, $usuarioRecuperado->clave);

        if($usuarioCompleto != null) 
        {   
            $hora = time();
            $usuarioSinClave = new stdClass();

            $usuarioSinClave->id = $usuarioCompleto->id;
            $usuarioSinClave->correo = $usuarioCompleto->correo;
            $usuarioSinClave->nombre = $usuarioCompleto->nombre;
            $usuarioSinClave->apellido = $usuarioCompleto->apellido;
            $usuarioSinClave->perfil = $usuarioCompleto->perfil;
            $usuarioSinClave->foto = $usuarioCompleto->foto;

            $payload = array(
                'iat'=>$hora,
                'exp' => $hora + 30,
                'data' => $usuarioSinClave
            );

            $stdResponse->jwt = JWT::encode($payload, self::$CLAVE);
            $stdResponse->status = 200;
            $stdResponse->exito = true;
        }

        $newResponse = $response->withStatus($stdResponse->status);
		$newResponse->getBody()->write(json_encode($stdResponse));
		return $newResponse->withHeader('Content-Type', 'application/json');	
    }

    /*
    (GET) Se envía el JWT → token (en el header) y se verifica. En caso exitoso, retorna un JSON
con mensaje y status 200. Caso contrario, retorna un JSON con mensaje y status 403.
    */
    public static function verificarToken(Request $request, Response $response, array $args): Response 
    {
        $tokenRecibido = $request->getHeader("token")[0];
        $stdResponse = new stdClass();
        $stdResponse->mensaje = "El JWT es valido";
        $stdResponse->status = 200;

        try {

            JWT::decode($tokenRecibido, self::$CLAVE,self::$FORMATO);

        } catch (Exception $e) { 
            $stdResponse->status = 403;
            $stdResponse->mensaje = $e->getMessage();
        }

        $newResponse = $response->withStatus($stdResponse->status);
		$newResponse->getBody()->write(json_encode($stdResponse));
		return $newResponse->withHeader('Content-Type', 'application/json');	
    }
}


?>