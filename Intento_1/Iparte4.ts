/*
MostrarBorradosJSON: Invocará (por AJAX) a “./BACKEND/mostrarBorradosJSON.php” que muestra todo lo
registrado en el archivo “neumaticos_eliminados.json”. Para ello, agregar un método estático (en NeumaticoBD), llamado
mostrarBorradosJSON.
Informar por consola el mensaje recibido y en la página (div id='divInfo').

MostrarFotosModificados: Invocará (por AJAX) a “./BACKEND/mostrarFotosDeModificados.php” que muestra (en
una tabla HTML) todas las imagenes (50px X 50px) de los neumáticos registrados en el directorio
“./BACKEND/neumaticosModificados/”. Para ello, agregar un método estático (en NeumaticoBD), llamado mostrarModificados.
Mostrar el listado en la página (div id='divTabla').
*/
interface Iparte4
{
    MostrarBorradosJSON() : void;
    MostrarFotosModificados() : void;
}