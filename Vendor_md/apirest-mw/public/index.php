<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();


//*********************************************************************************************//
//01.- CONFIGURO LOS VERBOS GET, POST, PUT Y DELETE
//*********************************************************************************************//
//firma correcta para el standard -> (request, response, array) => response

$app->get('/', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("GET => Bienvenido!!! a SlimFramework 4");
    return $response;
});

$app->post('/', function (Request $request, Response $response, array $args) : Response { 

    $response->getBody()->write("POST => Bienvenido!!! a SlimFramework 4");
    return $response;
});

$app->put('/', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("PUT => Bienvenido!!! a SlimFramework 4");
    return $response;
});

$app->delete('/', function (Request $request, Response $response, array $args) : Response {  

    $response->getBody()->write("DELETE => Bienvenido!!! a SlimFramework 4");
    return $response;
});



//*********************************************************************************************//
//FUNCION MIDDLEWARE - SEGUN ESTANDARIZACION PSR7
//*********************************************************************************************//

/**
 * Example middleware closure
 *
 * @param  ServerRequest  $request PSR-7 request
 * @param  RequestHandler $handler PSR-15 request handler
 *
 * @return ResponseMW
 */

$mwUno = function (Request $request, RequestHandler $handler) : ResponseMW {

    //EJECUTO ACCIONES ANTES DE INVOCAR AL VERBO
    $antes = " en MW_UNO antes del callable <br>";

    //INVOCO AL VERBO
    $response = $handler->handle($request);

    //OBTENGO LA RESPUESTA DEL VERBO
    $contenidoAPI = (string) $response->getBody();
    //GENERO UNA NUEVA RESPUESTA
    $response = new ResponseMW();

    //EJECUTO ACCIONES DESPUES DE INVOCAR AL VERBO
    $despues = " en MW_UNO después del callable <br>";

    $response->getBody()->write("{$antes} {$contenidoAPI} <br> {$despues}");

    return $response;
};

$mwDos = function (Request $request, RequestHandler $handler) : ResponseMW {

    //EJECUTO ACCIONES ANTES DE INVOCAR AL SIGUIENTE MW
    $antes = " en MW_DOS antes del callable <br>";

    //INVOCO AL SIGUIENTE MW
    $response = $handler->handle($request);

    //OBTENGO LA RESPUESTA DEL MW
    $contenidoAPI = (string) $response->getBody();

    //GENERO UNA NUEVA RESPUESTA
    $response = new ResponseMW();

    //EJECUTO ACCIONES DESPUES DE INVOCAR AL SIGUIENTE MW
    $despues = " en MW_DOS después del callable <br>";

    $response->getBody()->write("{$antes} {$contenidoAPI} {$despues}");

    return $response;
};

//*********************************************************************************************//
//01.-AGREGO MIDDLEWARE PARA TODOS LOS VERBOS
//*********************************************************************************************//

$app->add($mwUno);
$app->add($mwDos);

  
//*********************************************************************************************//
//02.-AGREGO ROUTE MIDDLEWARE, SOLO PARA PUT
//*********************************************************************************************//

$app->put('/param/', function (Request $request, Response $response, array $args) : Response {   

  $response->getBody()->write("API => PUT");
  return $response;

})->add(function (Request $request, RequestHandler $handler) : ResponseMW {

    //EJECUTO ACCIONES ANTES DE INVOCAR AL SIGUIENTE MW
    $antes = " en MW_PUT antes del callable <br>";

    //INVOCO AL SIGUIENTE MW
    $response = $handler->handle($request);

    //OBTENGO LA RESPUESTA DEL MW
    $contenidoAPI = (string) $response->getBody();

    //GENERO UNA NUEVA RESPUESTA
    $response = new ResponseMW();

    //EJECUTO ACCIONES DESPUES DE INVOCAR AL SIGUIENTE MW
    $despues = " en MW_PUT después del callable ";

    $response->getBody()->write("{$antes} {$contenidoAPI} <br> {$despues}");

    return $response;
});

  
//*********************************************************************************************//
//03.-AGREGO GROUP MIDDLEWARE
//*********************************************************************************************//

$app->group('/grupo', function (RouteCollectorProxy $grupo) {

  //EN LA RAIZ DEL GRUPO
  $grupo->get('/', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write("-GET- En el raíz del grupo...");
    return $response;

  });

  $grupo->post('/', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write("-POST- En el raíz del grupo...");
    return $response;

  });
  //------------------------------------------------------
  
  $grupo->get('/hoy', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write(date('Y-m-d'));
    return $response;

  });
    
  $grupo->get('/hora', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write(date('H:i:s'));
    return $response;

  })->add(function(Request $request, RequestHandler $handler) : ResponseMW {

    //EJECUTO ACCIONES ANTES DE INVOCAR AL SIGUIENTE MW
    $antes = " en MW_GRUPO_DOS antes del callable <br>";

    //INVOCO AL SIGUIENTE MW
    $response = $handler->handle($request);

    //OBTENGO LA RESPUESTA DEL MW
    $contenidoAPI = (string) $response->getBody();

    //GENERO UNA NUEVA RESPUESTA
    $response = new ResponseMW();

    //EJECUTO ACCIONES DESPUES DE INVOCAR AL SIGUIENTE MW
    $despues = " en MW_GRUPO_DOS después del callable ";

    $response->getBody()->write("{$antes} {$contenidoAPI} <br> {$despues}");

    return $response;
  });

  })->add(function (Request $request, RequestHandler $handler) : ResponseMW {

    //EJECUTO ACCIONES ANTES DE INVOCAR AL SIGUIENTE MW
    $antes = " en MW_GRUPO_UNO antes del callable <br>";

    //INVOCO AL SIGUIENTE MW
    $response = $handler->handle($request);

    //OBTENGO LA RESPUESTA DEL MW
    $contenidoAPI = (string) $response->getBody();

    //GENERO UNA NUEVA RESPUESTA
    $response = new ResponseMW();

    //EJECUTO ACCIONES DESPUES DE INVOCAR AL SIGUIENTE MW
    $despues = " en MW_GRUPO_UNO después del callable ";

    $response->getBody()->write("{$antes} {$contenidoAPI} <br> {$despues}");

    return $response;
});

  
//*********************************************************************************************//
//04.-AGREGO MAP MIDDLEWARE
//*********************************************************************************************//

$app->map(['GET', 'POST'], '/mapeado', function (Request $request, Response $response, array $args) : Response {

  $response->getBody()->write("API => GET o POST");
  return $response;

})->add(function(Request $request, RequestHandler $handler) : ResponseMW {

    if($request->getMethod() === "GET") 
    {
      $respuesta = 'Entro por GET';
    }
    else if($request->getMethod() === "POST")
    {
      $respuesta = 'Entro por POST';
    }

    //INVOCO AL SIGUIENTE MW
    $response = $handler->handle($request);

    //OBTENGO LA RESPUESTA DEL MW
    $contenidoAPI = (string) $response->getBody();

    //GENERO UNA NUEVA RESPUESTA
    $response = new ResponseMW();

    $response->getBody()->write("{$respuesta} <br> {$contenidoAPI}");

    return $response;

})->add(function(Request $request, RequestHandler $handler) : ResponseMW {

  $contenidoAPI = "";

  //EJECUTO ACCIONES ANTES DE INVOCAR AL SIGUIENTE MW
  $antes = 'en MW_MAPA antes del callable <br>';

  if($request->getMethod() === "GET") 
  {
    //INVOCO AL SIGUIENTE MW
    $response = $handler->handle($request);

    //OBTENGO LA RESPUESTA DEL MW
    $contenidoAPI = (string) $response->getBody();
  }  
  
  //EJECUTO ACCIONES DESPUES DE INVOCAR AL SIGUIENTE MW
  $despues = " en MW_MAPA después del callable ";

  //GENERO UNA NUEVA RESPUESTA
  $response = new ResponseMW();

  $response->getBody()->write("{$antes} {$contenidoAPI} <br> {$despues}");

  return $response;
});

  
//*********************************************************************************************//
//05.-AGREGO GROUP MIDDLEWARE CON POO
//*********************************************************************************************//

require_once __DIR__ . '/../src/poo/miClase.php';

$app->group('/grupo/POO', function (RouteCollectorProxy $grupo) {  

  //VERBOS EN LA RAIZ DEL GRUPO
  $grupo->get('/', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write("API => GET - En el raíz del grupo...");
    return $response;
  }); 

  $grupo->post('/', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write("API => POST - En el raíz del grupo...");
    return $response;
  });  

  //VERBOS EN RUTA
  $grupo->get('/hoy', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write(date('Y-m-d'));
    return $response;
  }); 

  //MW A NIVEL DE RUTA
  $grupo->get('/hora', function (Request $request, Response $response, array $args) : Response {

    date_default_timezone_set('America/Argentina/Buenos_Aires');  
    $response->getBody()->write(date('H:i:s'));    
    return $response;

  })->add(\MiClase::class . ":MostrarInstancia");

  //MW A NIVEL DE MAP
  $grupo->map(['PUT', 'DELETE'], '/map', function (Request $request, Response $response, array $args) : Response {

    $response->getBody()->write('API => PUT o DELETE - En verbo dentro de map.');
    return $response;

  })->add(\MiClase::class . "::MostrarEstatico")
    ->add(\MiClase::class . ":MostrarInstancia");
  
//MW A NIVEL DE GRUPO
})->add(\MiClase::class . "::MostrarEstatico");






//CORRE LA APLICACIÓN.
$app->run();