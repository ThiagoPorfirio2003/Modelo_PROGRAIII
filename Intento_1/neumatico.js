"use strict";
var Entidades;
(function (Entidades) {
    /*
    Neumatico: marca(cadena), medidas(cadena) y precio (numérico) como atributos.
    Un constructor que reciba tres parámetros.
Un método, ToString(), que retorne la representación de la clase en formato cadena (preparar la
cadena para que, al juntarse con el método ToJSON, forme una cadena JSON válida).

Un método de instancia, ToJSON(), que retorne la representación de la instancia en formato de
cadena JSON válido. Reutilizar código.
    */
    class Neumatico {
        constructor(marca, medidas, precio) {
            this.marca = marca;
            this.medidas = medidas;
            this.precio = precio;
        }
        ToString() {
            return `{"marca":"${this.marca}","medidas":"${this.medidas}","precio":${this.precio}`;
        }
        ToJSON() {
            return this.ToString() + "}";
        }
    }
    Entidades.Neumatico = Neumatico;
})(Entidades || (Entidades = {}));
//# sourceMappingURL=neumatico.js.map