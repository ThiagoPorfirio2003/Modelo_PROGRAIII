<?php
/*
Crear, en ./clases, la interface IParte3. Esta interface poseerá el método:
● existe: retorna true, si la instancia actual está en el array de objetos de tipo NeumaticoBD que recibe como
parámetro (comparar por marca y medidas). Caso contrario retorna false.
*/
interface IPart3
{
    function existe(array $neumaticos) : bool;
}

?>