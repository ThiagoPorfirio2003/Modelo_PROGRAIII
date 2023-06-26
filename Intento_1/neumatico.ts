namespace Entidades
{
    /*
    Neumatico: marca(cadena), medidas(cadena) y precio (numérico) como atributos. 
    Un constructor que reciba tres parámetros.
Un método, ToString(), que retorne la representación de la clase en formato cadena (preparar la
cadena para que, al juntarse con el método ToJSON, forme una cadena JSON válida).

Un método de instancia, ToJSON(), que retorne la representación de la instancia en formato de
cadena JSON válido. Reutilizar código.
    */
    export class Neumatico
    {
        public marca : string;
        public medidas : string;
        public precio : number;

        public constructor(marca : string, medidas : string, precio : number)
        {
            this.marca = marca;
            this.medidas = medidas;
            this.precio = precio;
        }

        public ToString() : string
        {
            return `{"marca":"${this.marca}","medidas":"${this.medidas}","precio":${this.precio}`;
        }

        public ToJSON() : string
        {
            return this.ToString() + "}";
        }
    }
}

