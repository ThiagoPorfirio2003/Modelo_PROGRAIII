/// <reference path="./neumatico.ts" />


namespace Entidades
{
    /*
    b. NeumaticoBD, hereda de Neumatico, posee como atributos id (numérico) y pathFoto(cadena).
    Un constructor para inicializar los atributos (con todos sus parámetros opcionales). Un método
    ToJSON(), que retornará la representación del objeto en formato JSON. Reutilizar código.
    */

    export class NeumaticoBD extends Neumatico
    {
        public id : number;
        public pathFoto : string;

        public constructor(marca : string="-", medidas : string="-", precio : number=-1,
        id : number = -1, pathFoto : string = "fake.jpg")
        {
            super(marca, medidas, precio);
            this.id = id;
            this.pathFoto = pathFoto;
        }

        public ToJSON() : string 
        {
            return super.ToString() + `,"id":${this.id},"pathFoto":"${this.pathFoto}"}`;
        }
    }
}