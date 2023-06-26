<?php
/*
Crear, en ./clases, la interface IParte2. Esta interface poseerá los métodos:

● eliminar: este método estático, elimina de la base de datos el registro coincidente con el id recibido cómo
parámetro. Retorna true, si se pudo eliminar, false, caso contrario.

● modificar: Modifica en la base de datos el registro coincidente con la instancia actual (comparar por id).
Retorna true, si se pudo modificar, false, caso contrario.
*/
    interface IPart2
    {
        static function eliminar(int $id) : bool;
        function modificar() : bool;
    }
?>