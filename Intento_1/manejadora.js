"use strict";
/// <reference path="ajax.ts" />
/// <reference path="neumatico.ts" />
/// <reference path="neumaticoBD.ts" />
/// <reference path="Iparte2.ts" />
/// <reference path="Iparte3.ts" />
/// <reference path="Iparte4.ts" />
var PrimerParcial;
(function (PrimerParcial) {
    class Manejadora {
        static informar(mensaje) {
            console.log(mensaje);
            alert(mensaje);
        }
        static AgregarNeumaticoJSON() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = document.getElementById("precio").value;
            let formData = new FormData();
            formData.append("marca", marca);
            formData.append("medidas", medidas);
            formData.append("precio", precio);
            Manejadora.AJAX.enviar_POST("./BACKEND/altaNeumaticoJSON.php", formData, (jsonRecibido) => {
                let respuesta = JSON.parse(jsonRecibido);
                Manejadora.informar(respuesta.mensaje);
            });
        }
        /*
MostrarNeumaticosJSON. Recuperará (por AJAX) todos los neumáticos del archivo neumaticos.json y
generará un listado dinámico, crear una tabla HTML con cabecera (en el FRONTEND) que mostrará toda la
información de cada uno de los neumáticos. Invocar a “./BACKEND/listadoNeumaticosJSON.php”, recibe la
petición (por GET) y retornará el listado de todos los neumáticos en formato JSON.
Informar por consola el mensaje recibido y mostrar el listado en la página (div id='divTabla')
*/
        static armarTablaJSON(neumaticos_json) {
            let neumaticos = JSON.parse(neumaticos_json);
            let tabla;
            tabla = "<table><tr><th>MARCA</th><th>MEDIDA</th><th>PRECIO</th><tr/>";
            neumaticos.forEach(neumatico => {
                tabla += `<tr><td>${neumatico.marca}</td><td>${neumatico.medidas}</td><td>${neumatico.precio}</td></tr>`;
            });
            tabla += "</table>";
            document.getElementById("divTabla").innerHTML = tabla;
        }
        static MostrarNeumaticosJSON() {
            Manejadora.AJAX.enviar_GET("./BACKEND/listadoNeumaticosJSON.php", "", (neumaticos_json) => {
                Manejadora.armarTablaJSON(neumaticos_json);
                console.log(neumaticos_json);
            });
        }
        static VerificarNeumaticoJSON() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let formData = new FormData();
            formData.append("marca", marca);
            formData.append("medidas", medidas);
            Manejadora.AJAX.enviar_POST("./BACKEND/verificarNeumaticoJSON.php", formData, (jsonRecibido) => {
                let respuesta = JSON.parse(jsonRecibido);
                Manejadora.informar(respuesta.mensaje);
            });
        }
        static AgregarNeumaticoSinFoto() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = parseInt(document.getElementById("precio").value);
            let neumatico = new Entidades.Neumatico(marca, medidas, precio);
            let formData = new FormData();
            formData.append("neumatico_json", neumatico.ToJSON());
            Manejadora.AJAX.enviar_POST("./BACKEND/agregarNeumaticoSinFoto.php", formData, (jsonRecibido) => {
                let respuesta = JSON.parse(jsonRecibido);
                Manejadora.informar(respuesta.mensaje);
            });
        }
        /*
        private RecuperarDeBD(mostrarPorConsola : boolean=false) : void
        {
            Manejadora.AJAX.enviar_GET("./BACKEND/listadoNeumaticosBD.php", "tabla=pepe", (neumaticos_json : string)=>
            {
                let url : string = window.location.href;
                let array_url : Array<string> =  url.split("/");
                let html : string = (array_url.reverse())[0];
                let manejadora : Manejadora = new Manejadora();
                let eliminarFunction : Function = manejadora.BorrarNeumaticoFoto;
                let modificarFunction: Function = manejadora.ModificarNeumaticoBD;

                (<HTMLDivElement> document.getElementById("divTabla")).innerHTML = Manejadora.armarTablaBD(neumaticos_json);

                if(html === "neumatico.html")
                {
                    eliminarFunction = manejadora.EliminarNeumatico;
                    modificarFunction = manejadora.ModificarNeumatico;
                }

                Manejadora.agregarFuncionesABotones(eliminarFunction, modificarFunction);
                if(mostrarPorConsola)
                {
                    console.log(neumaticos_json);
                }
            });
        }*/
        static armarTablaBD(neumaticos_json, crearBotones = true) {
            let neumaticos = JSON.parse(neumaticos_json);
            let tabla;
            tabla = "<table><tr><th>ID</th><th>MARCA</th><th>MEDIDA</th><th>PRECIO</th><th>FOTO</th>";
            if (crearBotones) {
                tabla += "<th>ACCIONES</th>";
            }
            tabla += "<tr/>";
            neumaticos.forEach(neumatico => {
                let foto = neumatico.pathFoto != "" ? neumatico.pathFoto : "fake.jpg";
                tabla += `<tr><td>${neumatico.id}</td><td>${neumatico.marca}</td><td>${neumatico.medidas}</td><td>${neumatico.precio}</td>` +
                    `<td><img src= "./BACKEND/${foto}" width= 50 height= 50></td>`;
                if (crearBotones) {
                    tabla += '<td><input type="button" value="Llenar datos" name="btn-llenarDatos" data-obj=' + JSON.stringify(neumatico) + ' </td>' +
                        '<td><input type="button" value="Eliminar" name="btn-Eliminar" data-obj= ' + JSON.stringify(neumatico) + ' </td></tr>';
                }
            });
            tabla += "</table>";
            return tabla;
        }
        static agregarFuncionesABotones(Eliminar, Modificar) {
            document.getElementsByName("btn-llenarDatos").forEach(element => {
                element.addEventListener("click", () => {
                    let json = element.getAttribute("data-obj");
                    Modificar(JSON.parse(json));
                });
            });
            document.getElementsByName("btn-Eliminar").forEach(element => {
                element.addEventListener("click", () => {
                    let neumaticoJSON = element.getAttribute("data-obj");
                    Eliminar(JSON.parse(neumaticoJSON));
                });
            });
        }
        static MostrarNeumaticosBD() {
            Manejadora.AJAX.enviar_GET("./BACKEND/listadoNeumaticosBD.php", "tabla=pepe", (neumaticos_json) => {
                let url = window.location.href;
                let array_url = url.split("/");
                let html = (array_url.reverse())[0];
                let manejadora = new Manejadora();
                let eliminarFunction = manejadora.BorrarNeumaticoFoto;
                let modificarFunction = manejadora.ModificarNeumaticoBD;
                document.getElementById("divTabla").innerHTML = Manejadora.armarTablaBD(neumaticos_json);
                if (html === "neumatico.html") {
                    eliminarFunction = manejadora.EliminarNeumatico;
                    modificarFunction = manejadora.ModificarNeumatico;
                }
                Manejadora.agregarFuncionesABotones(eliminarFunction, modificarFunction);
                console.log(neumaticos_json);
            });
        }
        EliminarNeumatico(neumaticoAEliminar_json) {
            if (confirm(`Seguro que desea eliminar el neumatico?:\n${neumaticoAEliminar_json.marca} - ${neumaticoAEliminar_json.medidas}`)) {
                let formData = new FormData();
                formData.append("neumatico_json", JSON.stringify(neumaticoAEliminar_json));
                Manejadora.AJAX.enviar_POST("./BACKEND/eliminarNeumaticoBD.php", formData, (respuestaJSON) => {
                    let respuesta = JSON.parse(respuestaJSON);
                    Manejadora.informar(respuesta.mensaje);
                    if (respuesta.exito) {
                        Manejadora.MostrarNeumaticosBD();
                    }
                });
            }
        }
        ModificarNeumatico(neumaticoAMostrar_json) {
            document.getElementById("idNeumatico").value = neumaticoAMostrar_json.id;
            document.getElementById("marca").value = neumaticoAMostrar_json.marca;
            document.getElementById("medidas").value = neumaticoAMostrar_json.medidas;
            document.getElementById("precio").value = neumaticoAMostrar_json.precio;
            //(<HTMLInputElement> document.getElementById("imgFoto")).value = neumaticoAMostrar_json.pathFoto;
        }
        static ModificarNeumaticoSinFoto() {
            let id = parseInt(document.getElementById("idNeumatico").value);
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = parseInt(document.getElementById("precio").value);
            let formData = new FormData();
            formData.append("neumatico_json", (new Entidades.NeumaticoBD(marca, medidas, precio, id)).ToJSON());
            Manejadora.AJAX.enviar_POST("./BACKEND/modificarNeumaticoBD.php", formData, (jsonRecibido) => {
                let respuesta = JSON.parse(jsonRecibido);
                if (respuesta.exito) {
                    Manejadora.MostrarNeumaticosBD();
                }
                Manejadora.informar(respuesta.mensaje);
            });
        }
        VerificarNeumaticoBD() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let form = new FormData();
            form.append("obj_neumatico", `{"marca":"${marca}","medidas":"${medidas}"}`);
            Manejadora.AJAX.enviar_POST("./BACKEND/verificarNeumaticoBD.php", form, Manejadora.exitoVerificarNeumaticoBD);
        }
        static exitoVerificarNeumaticoBD(neumaticoJSON) {
            let mensaje;
            let manejadora = new Manejadora();
            mensaje = "El neumatico no existe";
            if (neumaticoJSON !== "{}") {
                mensaje = "Se encontro el neumatico";
                document.getElementById("divInfo").innerHTML = Manejadora.armarTablaBD(`[${neumaticoJSON}]`);
                Manejadora.agregarFuncionesABotones(manejadora.BorrarNeumaticoFoto, manejadora.ModificarNeumaticoBD);
            }
            console.log(mensaje);
        }
        static VerificarNeumaticoEstatico() {
            new Manejadora().VerificarNeumaticoBD();
        }
        AgregarNeumaticoFoto() {
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = document.getElementById("precio").value;
            let foto = document.getElementById("foto");
            let form = new FormData();
            form.append("marca", marca);
            form.append("medidas", medidas);
            form.append("precio", precio);
            form.append("foto", foto.files[0]);
            Manejadora.AJAX.enviar_POST("./BACKEND/agregarNeumaticoBD.php", form, (respuesta_json) => {
                let respuesta = JSON.parse(respuesta_json);
                Manejadora.MostrarNeumaticosBD();
                Manejadora.informar(respuesta.mensaje);
            });
        }
        static AgregarNeumaticoFotoEstatico() {
            new Manejadora().AgregarNeumaticoFoto();
        }
        BorrarNeumaticoFoto(neumatico_json) {
            if (confirm(`Seguro que desea eliminar el neumatico?:\n${neumatico_json.marca} - ${neumatico_json.medidas}`)) {
                let formData = new FormData();
                formData.append("neumatico_json", JSON.stringify(neumatico_json));
                Manejadora.AJAX.enviar_POST("./BACKEND/eliminarNeumaticoBDFoto.php", formData, (respuestaJSON) => {
                    let respuesta = JSON.parse(respuestaJSON);
                    if (respuesta.exito) {
                        Manejadora.MostrarNeumaticosBD();
                    }
                    Manejadora.informar(respuesta.mensaje);
                });
            }
        }
        ModificarNeumaticoBD(neumatico_json) {
            document.getElementById("idNeumatico").value = neumatico_json.id;
            document.getElementById("marca").value = neumatico_json.marca;
            document.getElementById("medidas").value = neumatico_json.medidas;
            document.getElementById("precio").value = neumatico_json.precio;
            document.getElementById("imgFoto").src = "./BACKEND/" + neumatico_json.pathFoto;
        }
        static ModificarNeumaticoBDFoto() {
            let id = parseInt(document.getElementById("idNeumatico").value);
            let marca = document.getElementById("marca").value;
            let medidas = document.getElementById("medidas").value;
            let precio = parseInt(document.getElementById("precio").value);
            let foto = document.getElementById("foto");
            let formData = new FormData();
            formData.append("neumatico_json", (new Entidades.NeumaticoBD(marca, medidas, precio, id)).ToJSON());
            formData.append("foto", foto.files[0]);
            Manejadora.AJAX.enviar_POST("./BACKEND/modificarNeumaticoBDFoto.php", formData, (jsonRecibido) => {
                let respuesta = JSON.parse(jsonRecibido);
                if (respuesta.exito) {
                    Manejadora.MostrarNeumaticosBD();
                }
                else {
                    Manejadora.informar(respuesta.mensaje);
                }
            });
        }
        MostrarBorradosJSON() {
            Manejadora.AJAX.enviar_GET("./BACKEND/mostrarBorradosJSON.php", "", (tabla) => {
                document.getElementById("divInfo").innerHTML = tabla;
                console.log(tabla);
            });
        }
        static mostrarBorradosJSON() {
            new Manejadora().MostrarBorradosJSON();
        }
        MostrarFotosModificados() {
            Manejadora.AJAX.enviar_GET("./BACKEND/mostrarFotosDeModificados.php", "", (tabla) => {
                document.getElementById("divTabla").innerHTML = tabla;
                console.log(tabla);
            });
        }
        static mostrarFotosModificados() {
            new Manejadora().MostrarFotosModificados();
        }
    }
    Manejadora.AJAX = new Ajax();
    PrimerParcial.Manejadora = Manejadora;
})(PrimerParcial || (PrimerParcial = {}));
/*
public static BorrarNeumaticoFotoEstatico() : void
{
    let id : number = parseInt((<HTMLInputElement> document.getElementById("idNeumatico")).value);
    let marca : string= (<HTMLInputElement> document.getElementById("marca")).value;
    let medidas : string = (<HTMLInputElement> document.getElementById("medidas")).value;
    let precio : number = parseFloat((<HTMLInputElement> document.getElementById("precio")).value);
    let pathFoto : string = (<HTMLImageElement> document.getElementById("imgFoto")).src;

    let neumatico : Entidades.NeumaticoBD = new Entidades.NeumaticoBD(marca, medidas, precio, id, pathFoto);
    let neumatico_json : any

    new Manejadora().BorrarNeumaticoFoto();
}*/ 
//# sourceMappingURL=manejadora.js.map