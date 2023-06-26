<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;
use Firebase\JWT\JWT;


Class Middlewares
{
    private static $CLAVE = "MI_CLAVE";
    private static array $FORMATO = ['HS256'];

    public function verificarSeteados(Request $request, RequestHandler $handler) : ResponseMW
    {
        $arrayBody = $request->getParsedBody();
        $usuario = json_decode($arrayBody["usuario"]);

        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Error el correo y/o clave no estan seteados";
        $stdResponse->status = 403;
        $stdResponse->exito = false;

        $response_aux = new ResponseMW();

        
        if(isset($usuario->correo) && isset($usuario->clave))
        {
            $responseMW = $handler->handle($request);
            $stdResponse = json_decode($responseMW->getBody());
            /*
            $stdRespuesta =  json_decode($responseMW->getBody());
            if($stdRespuesta->status == 200)
            {
                $stdResponse->mensaje = "El usuario ha sido agregado con exito";
                $stdResponse->status = 200;
            }*/
        }
            
        $retorno = $response_aux->withStatus($stdResponse->status);
        $retorno->getBody()->write(json_encode($stdResponse));

        return $retorno->withHeader('Content-Type', 'application/json');	
    }

    /*
        2.- (método de clase) Si alguno está vacío (o los dos) retorne un JSON con el mensaje de error
        correspondiente (y status 409).
        Caso contrario, pasar al siguiente Middleware.
    */
    public static function verificarVacios(Request $request, RequestHandler $handler) : ResponseMW
    {
        $arrayBody = $request->getParsedBody();
        $usuario = json_decode($arrayBody["usuario"]);

        $stdResponse = new stdClass();
        $stdResponse->mensaje = 'Error el correo y / o clave estan vacios';
        $stdResponse->status = 409;
        $stdResponse->exito = false;

        $response_aux = new ResponseMW();

       // $stdResponse->mensaje = "correo ==". strlen($usuario->correo) . "  clave ==".strlen($usuario->clave);
        if(strlen($usuario->correo) > 0 && strlen($usuario->clave) > 0)
        {
            $responseMW = $handler->handle($request);
            $stdResponse = json_decode($responseMW->getBody());
        }
            
        $retorno = $response_aux->withStatus($stdResponse->status);
        $retorno->getBody()->write(json_encode($stdResponse));

        return $retorno->withHeader('Content-Type', 'application/json');
    }

    /*
        3.- (método de instancia) Verificar que el correo y clave existan en la base de datos. Si NO
        existen, retornar un JSON con el mensaje de error correspondiente (y status 403).
        Caso contrario, acceder al verbo de la API.
    */
    public function verificarInicioSesion(Request $request, RequestHandler $handler) : ResponseMW
    {
        $arrayBody = $request->getParsedBody();
        $usuario = json_decode($arrayBody["usuario"]);

        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Error el correo y/o clave no existen";
        $stdResponse->status = 403;

        $response_aux = new ResponseMW();

        
        if(Usuario::traerSegunDatosDeInicio($usuario->correo, $usuario->clave) != null)
        {
            $responseMW = $handler->handle($request);
            $stdResponse = json_decode($responseMW->getBody());
        }
            
        $retorno = $response_aux->withStatus($stdResponse->status);
        $retorno->getBody()->write(json_encode($stdResponse));

        return $retorno->withHeader('Content-Type', 'application/json');
    }

     /*
    4.- (método de clase) Verificar que el correo no exista en la base de datos. Si EXISTE, retornar
    un JSON con el mensaje de error correspondiente (y status 403).
    Caso contrario, acceder al verbo de la API.
    */
    public static function verificarExisteCorreo(Request $request, RequestHandler $handler) : ResponseMW
    {
        $arrayBody = $request->getParsedBody();
        $usuario = json_decode($arrayBody["usuario"]);

        $stdResponse = new stdClass();
        $stdResponse->mensaje = "Error, el correo ya esta asociado a un usuario";
        $stdResponse->status = 403;
        $stdResponse->exito = false;

        $response_aux = new ResponseMW();

        
        if(Usuario::traerSegunCorreo($usuario->correo) == null)
        {
            $responseMW = $handler->handle($request);
            $stdResponse = json_decode($responseMW->getBody());
        }
            
        $retorno = $response_aux->withStatus($stdResponse->status);
        $retorno->getBody()->write(json_encode($stdResponse));

        return $retorno->withHeader('Content-Type', 'application/json');
    }

            /*
5.- (método de instancia) Verificar que el precio posea un rango de entre 50.000 y 600.000 y
que el color no sea ‘azul’. Si no pasa la validación alguno de los dos (o los dos) retorne un JSON
con el mensaje de error correspondiente (y status 409).
Caso contrario, acceder al verbo de la API.
    */ 
    public function verificarColorYPrecio(Request $request, RequestHandler $handler) : ResponseMW
    {
        //Se envía un JSON → auto (color, marca, precio y modelo).
        $arrayBody = $request->getParsedBody();
        $auto = json_decode($arrayBody["auto"]);

        $stdResponse = new stdClass();
        $stdResponse->mensaje = "El precio no esta entre 50.000 y 600.000 y/o el color es azul";
        $stdResponse->status = 409;
        $stdResponse->exito = false;

        $response_aux = new ResponseMW();

        if($auto->precio > 49999 && $auto->precio < 600001 && $auto->color !== "azul")
        {
            $responseMW = $handler->handle($request);
            $stdResponse = json_decode($responseMW->getBody());
        }
            
        $retorno = $response_aux->withStatus($stdResponse->status);
        $retorno->getBody()->write(json_encode($stdResponse));

        return $retorno->withHeader('Content-Type', 'application/json');
    }

    //------------------------------------------------------------------------------------/
    /*
    Crear los siguientes Middlewares (en la clase MW) para que:

    /*
    1.- (método de instancia) verifique que el token sea válido.
    Recibe el JWT → token (en el header) a ser verificado.
    Retorna un JSON con el mensaje de error correspondiente (y status 403), en caso de no
    ser válido.
    Caso contrario, pasar al siguiente callable.
    */
    public function verificarToken(Request $request, RequestHandler $handler) : ResponseMW
    {
        $tokenRecibido = $request->getHeader("token")[0];
        $stdResponse = new stdClass();

        try {

            JWT::decode($tokenRecibido, self::$CLAVE,self::$FORMATO);

            $responseMW = $handler->handle($request);
            $stdResponse = json_decode($responseMW->getBody());
        } catch (Exception $e) 
        { 
            $stdResponse->mensaje = "El JWT es INVALIDO";
            $stdResponse->status = 403;
        }

        $retorno = (new ResponseMW())->withStatus($stdResponse->status);
		$retorno->getBody()->write(json_encode($stdResponse));
		return $retorno->withHeader('Content-Type', 'application/json');	
    }

    /*
        2.- (método de clase) verifique si es un ‘propietario’ o no.
        Recibe el JWT → token (en el header) a ser verificado.
        Retorna un JSON con propietario: true/false; mensaje: string (mensaje correspondiente);
        status: 200/409.
    */
    public static function esPropietario(Request $request, RequestHandler $handler) : ResponseMW
    {
        $tokenRecibido = $request->getHeader("token")[0];
        $stdResponse = new stdClass();
        $stdResponse->exito = false;
        $stdResponse->status = 409;

        try {
            $datos = JWT::decode($tokenRecibido, self::$CLAVE,self::$FORMATO)->data;
            $perfil = $datos->perfil;

            if($perfil == "propietario")
            {
                $responseMW = $handler->handle($request);
                $stdResponse = json_decode($responseMW->getBody());
            }
            else
            {
                $stdResponse->mensaje = "El JWT no es valido o el usuario no es propietario, es: " . $perfil;
            }
            

        } catch (Exception $e) { 
        }

        $retorno = (new ResponseMW())->withStatus($stdResponse->status);
		$retorno->getBody()->write(json_encode($stdResponse));
		return $retorno->withHeader('Content-Type', 'application/json');		
    }

    /*
        3.- (método de instancia) verifique si es un ‘encargado’ o no.
    Recibe el JWT → token (en el header) a ser verificado.
    Retorna un JSON con encargado: true/false; mensaje: string (mensaje correspondiente);
    status: 200/409.
    */

    public static function esEncargado(Request $request, RequestHandler $handler) : ResponseMW
    {
        $tokenRecibido = $request->getHeader("token")[0];
        $stdResponse = new stdClass();
        $stdResponse->exito = false;
        $stdResponse->status = 409;

        try {
            $datos = JWT::decode($tokenRecibido, self::$CLAVE,self::$FORMATO)->data;

            if($datos->perfil == "encargado")
            {
                $responseMW = $handler->handle($request);
                $stdResponse = json_decode($responseMW->getBody());
            }
            else
            {
                $stdResponse->mensaje = "El JWT no es valido o el usuario no es encargado, es: " . $datos->perfil;
            }

        } catch (Exception $e) { 

        }

        $retorno = (new ResponseMW())->withStatus($stdResponse->status);
		$retorno->getBody()->write(json_encode($stdResponse));
		return $retorno->withHeader('Content-Type', 'application/json');		
    }
    
}

?>