<?php
    /*
Crear, en ./clases, la interface IParte1. Esta interface poseerá los métodos:
● agregar: agrega, a partir de la instancia actual, un nuevo registro en la tabla neumaticos (id, marca,
medidas, precio, foto), de la base de datos gomeria_bd. Retorna true, si se pudo agregar, false, caso
contrario.
● traer: este método estático retorna un array de objetos de tipo NeumaticoBD, recuperados de la base de
datos.
Implementar la interface en la clase NeumaticoBD
    */

interface IPart1
{
    function agregar() : bool;
    static function traer() : array;
}
?>