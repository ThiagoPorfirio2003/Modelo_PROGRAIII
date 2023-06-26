"use strict";
/*
VerificarNeumaticoBD. Se recupera la marca y las medidas del neumáticoBD desde la página
neumatico_BD.html y se invoca (por AJAX) a “./BACKEND/verificarNeumaticoBD.php” que recibe por POST el
parámetro obj_neumatico, que será una cadena JSON (marca y medidas), si coincide con algún registro de la base de datos (invocar
al método traer) retornará los datos del objeto (invocar al toJSON). Caso contrario, un JSON vacío ({}).
Informar por consola lo acontecido y mostrar el objeto recibido en la página (div id='divInfo').
*/ 
//# sourceMappingURL=Iparte3.js.map