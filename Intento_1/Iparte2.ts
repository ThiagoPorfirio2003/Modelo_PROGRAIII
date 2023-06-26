interface Iparte2
{
/*
 EliminarNeumatico. Recibe como parámetro al objeto JSON que se ha de eliminar. Pedir confirmación,
    mostrando la marca y las medidas, antes de eliminar.
    Si se confirma se invocará (por AJAX) a “./BACKEND/eliminarNeumaticoBD.php” pasándole cómo parámetro
    neumatico_json (id, marca, medidas y precio, en formato de cadena JSON) por POST y se deberá borrar el neumático de la base de
    datos (invocando al método eliminar).
    Si se pudo borrar en la base de datos, invocar al método guardarJSON y pasarle './BACKEND/archivos/neumaticos_eliminados.json'
    cómo parámetro.
    Retornar un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
    Informar por consola y alert lo acontecido. Refrescar el listado para visualizar los cambios.

    ModificarNeumatico. Mostrará todos los datos del neumáticoBD que recibe por parámetro (objeto JSON), en
    el formulario, de tener foto, incluirla en “imgFoto”. Permitirá modificar cualquier campo, a excepción del id.
    Al pulsar el botón Modificar sin foto (de la página) se invocará (por AJAX) a
    “./BACKEND/modificarNeumaticoBD.php” Se recibirán por POST los siguientes valores: neumatico_json (id, marca,
    medidas, y precio, en formato de cadena JSON) para modificar un neumático en la base de datos. Invocar al método modificar.
    Nota: El valor del id, será el id del neumático 'original', mientras que el resto de los valores serán los del neumático a ser modificado.
    Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
    Refrescar el listado solo si se pudo modificar, caso contrario, informar (por alert y consola) de lo acontecido.

    NOTA: Agregar una columna extra (Acciones) al listado de neumáticos que permita: Eliminar y Modificar al
    neumático elegido. Para ello, agregue dos botones (input [type=button]) que invoquen a las funciones
    EliminarNeumatico y ModificarNeumatico, respectivamente.
*/
    EliminarNeumatico(neumaticoAEliminar_json : any) : void;
    ModificarNeumatico(neumaticoAMostrar_json : any) : void;
}