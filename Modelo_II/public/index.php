<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as ResponseMW;

use Slim\Factory\AppFactory;
use \Slim\Routing\RouteCollectorProxy;

//require __DIR__ . '/../vendor/autoload.php';
require '../vendor/autoload.php';
require_once "../clases/usuario.php";
require_once "../clases/auto.php";
require_once "../clases/mw.php";

$app = AppFactory::create();


/*
A nivel de ruta (/usuarios):
(POST) Alta de usuarios. Se agregará un nuevo registro en la tabla usuarios *.
Se envía un JSON → usuario (correo, clave, nombre, apellido, perfil**) y foto.
La foto se guardará en ./fotos, con el siguiente formato: correo_id.extension.
Ejemplo: ./fotos/juan@perez_152.jpg
* ID auto-incremental. ** propietario, encargado y empleado.
Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)

A nivel de aplicación:
(GET) Listado de usuarios. Mostrará el listado completo de los usuarios (array JSON).
Retorna un JSON (éxito: true/false; mensaje: string; tabla: stringJSON; status: 200/424)

A nivel de aplicación:
(POST) Alta de autos. Se agregará un nuevo registro en la tabla autos *.
Se envía un JSON → auto (color, marca, precio y modelo).
* ID auto-incremental.
Retorna un JSON (éxito: true/false; mensaje: string; status: 200/418)

A nivel de ruta (/autos):
(GET) Listado de autos. Mostrará el listado completo de los autos (array JSON).
Retorna un JSON (éxito: true/false; mensaje: string; tabla: stringJSON; status: 200/424)

A nivel de ruta (/login):
(POST) Se envía un JSON → user (correo y clave) y retorna un JSON (éxito: true/false; jwt: JWT
(con todos los datos del usuario) / null; status: 200/403)
(GET) Se envía el JWT → token (en el header) y se verifica. En caso exitoso, retorna un JSON
con mensaje y status 200. Caso contrario, retorna un JSON con mensaje y status 403.

*/

/*
$app->group('/cd', function (RouteCollectorProxy $grupo) {   
    $grupo->get('/', Cd::class . ':traerTodos');
});
*/



//require_once __DIR__ . "/../clases/usuario.php";

$app->get('/', Usuario::class . '::traerListadoUsuarios');

$app->post('/', Auto::class . '::altaAuto')
->add(Middlewares::class . ":verificarColorYPrecio");

$app->post('/usuarios', Usuario::class . '::altaUsuario'
)->add(Middlewares::class . "::verificarExisteCorreo")
->add(Middlewares::class . "::verificarVacios")
->add(Middlewares::class . ":verificarSeteados");

$app->get('/autos', Auto::class . '::traerListadoAutos');

$app->post('/login', Usuario::class . '::login'
)->add(Middlewares::class . ":verificarInicioSesion")
->add(Middlewares::class . "::verificarVacios")
->add(Middlewares::class . ":verificarSeteados");

$app->get('/login', Usuario::class . '::esTokenValido');

/*
Crear, a nivel de aplicación, los verbos:
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

Crear los siguientes Middlewares (en la clase MW) para que:
1.- (método de instancia) verifique que el token sea válido.
Recibe el JWT → token (en el header) a ser verificado.
Retorna un JSON con el mensaje de error correspondiente (y status 403), en caso de no
ser válido.
Caso contrario, pasar al siguiente callable.

2.- (método de clase) verifique si es un ‘propietario’ o no.
Recibe el JWT → token (en el header) a ser verificado.
Retorna un JSON con propietario: true/false; mensaje: string (mensaje correspondiente);
status: 200/409.

3.- (método de instancia) verifique si es un ‘encargado’ o no.
Recibe el JWT → token (en el header) a ser verificado.
Retorna un JSON con encargado: true/false; mensaje: string (mensaje correspondiente);
status: 200/409.
Aplicar los Middlewares a los verbos PUT y DELETE.
*/

$app->delete("/", Auto::class . "::borrarAuto")
->add(Middlewares::class . "::esPropietario")
->add(Middlewares::class . ":verificarToken");

$app->put("/", Auto::class . "::modificarAuto")
->add(Middlewares::class . "::esEncargado")
->add(Middlewares::class . ":verificarToken");

$app->run();
//request->getHeaderLine();

?>