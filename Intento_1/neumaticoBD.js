"use strict";
/// <reference path="./neumatico.ts" />
var Entidades;
(function (Entidades) {
    /*
    b. NeumaticoBD, hereda de Neumatico, posee como atributos id (numérico) y pathFoto(cadena).
    Un constructor para inicializar los atributos (con todos sus parámetros opcionales). Un método
    ToJSON(), que retornará la representación del objeto en formato JSON. Reutilizar código.
    */
    class NeumaticoBD extends Entidades.Neumatico {
        constructor(marca = "-", medidas = "-", precio = -1, id = -1, pathFoto = "fake.jpg") {
            super(marca, medidas, precio);
            this.id = id;
            this.pathFoto = pathFoto;
        }
        ToJSON() {
            return super.ToString() + `,"id":${this.id},"pathFoto":"${this.pathFoto}"}`;
        }
    }
    Entidades.NeumaticoBD = NeumaticoBD;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=neumaticoBD.js.map