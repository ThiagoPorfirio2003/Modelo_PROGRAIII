<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

class MiClase
{

    public function MostrarInstancia(Request $request, RequestHandler $handler) : ResponseMW
    {
        //EJECUTO ACCIONES ANTES DE INVOCAR AL SIGUIENTE MW
        $antes = " Desde método de instancia (antes del verbo)<br>";

        //INVOCO AL SIGUIENTE MW
        $response = $handler->handle($request);

        //OBTENGO LA RESPUESTA DEL MW
        $contenidoAPI = (string) $response->getBody();

        //GENERO UNA NUEVA RESPUESTA
        $response = new ResponseMW();

        //EJECUTO ACCIONES DESPUES DE INVOCAR AL SIGUIENTE MW
        $despues = " Desde método de instancia (después del verbo) ";

        $response->getBody()->write("{$antes} {$contenidoAPI} <br> {$despues}");

        return $response;
    }
    
    public static function MostrarEstatico(Request $request, RequestHandler $handler) : ResponseMW
    {
        //EJECUTO ACCIONES ANTES DE INVOCAR AL SIGUIENTE MW
        $antes = " Desde método estático (antes del verbo)<br>";

        //INVOCO AL SIGUIENTE MW
        $response = $handler->handle($request);

        //OBTENGO LA RESPUESTA DEL MW
        $contenidoAPI = (string) $response->getBody();

        //GENERO UNA NUEVA RESPUESTA
        $response = new ResponseMW();

        //EJECUTO ACCIONES DESPUES DE INVOCAR AL SIGUIENTE MW
        $despues = " Desde método estático (después del verbo)<br> ";

        $response->getBody()->write("{$antes} {$contenidoAPI} <br> {$despues}");

        return $response;
    }
}