<?php
/*
Crear, en ./clases, la interface IParte4. Esta interface poseerá el método:
● guardarEnArchivo: escribirá en un archivo de texto (./archivos/neumaticosbd_borrados.txt) toda la
información del neumático más la nueva ubicación de la foto. La foto se moverá al subdirectorio
“./neumaticosBorrados/”, con el nombre formado por el id punto marca punto 'borrado' punto hora,
minutos y segundos del borrado (Ejemplo: 688.bridgestone.borrado.105905.jpg).
Se retornará un JSON que contendrá: éxito(bool) y mensaje(string) indicando lo acontecido.
*/

interface IPart4
{
    function guardarEnArchivo() : string;
}
?>