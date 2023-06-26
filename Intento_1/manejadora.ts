/// <reference path="ajax.ts" />
/// <reference path="neumatico.ts" />
/// <reference path="neumaticoBD.ts" />
/// <reference path="Iparte2.ts" />
/// <reference path="Iparte3.ts" />
/// <reference path="Iparte4.ts" />

namespace PrimerParcial
{
    export class Manejadora implements Iparte2, Iparte3, Iparte4
    {
        private static AJAX : Ajax = new Ajax();

        private static informar(mensaje : string)
        {
            console.log(mensaje);
            alert(mensaje);
        }

        public static AgregarNeumaticoJSON() : void
        {
            let marca = (<HTMLInputElement> document.getElementById("marca")).value;
            let medidas = (<HTMLInputElement> document.getElementById("medidas")).value;
            let precio = (<HTMLInputElement> document.getElementById("precio")).value;

            let formData = new FormData();

            formData.append("marca", marca);
            formData.append("medidas", medidas);
            formData.append("precio", precio);

            Manejadora.AJAX.enviar_POST("./BACKEND/altaNeumaticoJSON.php",formData, (jsonRecibido : any) =>
            {
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

        private static armarTablaJSON(neumaticos_json : string) : void
        {
            let neumaticos : any[]= JSON.parse(neumaticos_json);
            let tabla : string;

            tabla = "<table><tr><th>MARCA</th><th>MEDIDA</th><th>PRECIO</th><tr/>"

            neumaticos.forEach(neumatico => {
                tabla+=`<tr><td>${neumatico.marca}</td><td>${neumatico.medidas}</td><td>${neumatico.precio}</td></tr>`;
            });

            tabla +="</table>";

            (<HTMLDivElement> document.getElementById("divTabla")).innerHTML = tabla;
        }

        public static MostrarNeumaticosJSON() : void
        {
            Manejadora.AJAX.enviar_GET("./BACKEND/listadoNeumaticosJSON.php", "", (neumaticos_json : string)=>
            {
                Manejadora.armarTablaJSON(neumaticos_json);
                console.log(neumaticos_json);
            });
        }

        public static VerificarNeumaticoJSON() : void
        {
            let marca = (<HTMLInputElement> document.getElementById("marca")).value;
            let medidas = (<HTMLInputElement> document.getElementById("medidas")).value;

            let formData = new FormData();

            formData.append("marca", marca);
            formData.append("medidas", medidas);

            Manejadora.AJAX.enviar_POST("./BACKEND/verificarNeumaticoJSON.php",formData, (jsonRecibido : any) =>
            {
                let respuesta = JSON.parse(jsonRecibido);
                Manejadora.informar(respuesta.mensaje);
            });
        }

        public static AgregarNeumaticoSinFoto() : void
        {
            let marca = (<HTMLInputElement> document.getElementById("marca")).value;
            let medidas = (<HTMLInputElement> document.getElementById("medidas")).value;
            let precio = parseInt((<HTMLInputElement> document.getElementById("precio")).value);
            
            let neumatico : Entidades.Neumatico = new Entidades.Neumatico(marca, medidas, precio);

            let formData = new FormData();

            formData.append("neumatico_json", neumatico.ToJSON());

            Manejadora.AJAX.enviar_POST("./BACKEND/agregarNeumaticoSinFoto.php",formData, (jsonRecibido : string) =>
            {
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

        private static armarTablaBD(neumaticos_json : string, crearBotones : boolean=true) : string
        {
            let neumaticos : any[]= JSON.parse(neumaticos_json);
            let tabla : string;

            tabla = "<table><tr><th>ID</th><th>MARCA</th><th>MEDIDA</th><th>PRECIO</th><th>FOTO</th>";
            
            if(crearBotones)
            {
                tabla+="<th>ACCIONES</th>";
            }

            tabla+="<tr/>";

            neumaticos.forEach(neumatico => {
                let foto = neumatico.pathFoto != "" ? neumatico.pathFoto : "fake.jpg";

                tabla+=`<tr><td>${neumatico.id}</td><td>${neumatico.marca}</td><td>${neumatico.medidas}</td><td>${neumatico.precio}</td>` +
                `<td><img src= "./BACKEND/${foto}" width= 50 height= 50></td>`; 
        
                if(crearBotones)
                {
                    tabla+='<td><input type="button" value="Llenar datos" name="btn-llenarDatos" data-obj=' + JSON.stringify(neumatico) + ' </td>' + 
                    '<td><input type="button" value="Eliminar" name="btn-Eliminar" data-obj= ' + JSON.stringify(neumatico)  + ' </td></tr>';
                }
            });

            tabla +="</table>";

            return tabla;
        }

        private static agregarFuncionesABotones(Eliminar : Function, Modificar : Function) : void
        {
            document.getElementsByName("btn-llenarDatos").forEach(element => 
            {
                element.addEventListener("click", () =>
                {
                    let json : any = element.getAttribute("data-obj");
                    
                    Modificar(JSON.parse(json));
                }
                );
            }
            ); 
                    
            document.getElementsByName("btn-Eliminar").forEach(element => 
            {
                element.addEventListener("click", () =>
                {
                    let neumaticoJSON : any = element.getAttribute("data-obj");

                    Eliminar(JSON.parse(neumaticoJSON));
                }
                );
            }
            );
        }

        public static MostrarNeumaticosBD() : void
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
                console.log(neumaticos_json);
            });
        }

        public EliminarNeumatico(neumaticoAEliminar_json: any): void 
        {
            if(confirm(`Seguro que desea eliminar el neumatico?:\n${neumaticoAEliminar_json.marca} - ${neumaticoAEliminar_json.medidas}`))
            {
                let formData = new FormData();

                formData.append("neumatico_json", JSON.stringify(neumaticoAEliminar_json))

                Manejadora.AJAX.enviar_POST("./BACKEND/eliminarNeumaticoBD.php", formData, (respuestaJSON : string)=>
                {
                    let respuesta = JSON.parse(respuestaJSON);

                    Manejadora.informar(respuesta.mensaje);

                    if(respuesta.exito)
                    {
                        Manejadora.MostrarNeumaticosBD();
                    }

                });    
            }
        }

        public ModificarNeumatico(neumaticoAMostrar_json: any): void 
        {
            (<HTMLInputElement> document.getElementById("idNeumatico")).value = neumaticoAMostrar_json.id;
            (<HTMLInputElement> document.getElementById("marca")).value = neumaticoAMostrar_json.marca;
            (<HTMLInputElement> document.getElementById("medidas")).value = neumaticoAMostrar_json.medidas;
            (<HTMLInputElement> document.getElementById("precio")).value = neumaticoAMostrar_json.precio;
            //(<HTMLInputElement> document.getElementById("imgFoto")).value = neumaticoAMostrar_json.pathFoto;
        }

        public static ModificarNeumaticoSinFoto() : void
        {
            let id : number = parseInt((<HTMLInputElement> document.getElementById("idNeumatico")).value);
            let marca : string= (<HTMLInputElement> document.getElementById("marca")).value;
            let medidas : string = (<HTMLInputElement> document.getElementById("medidas")).value;
            let precio : number = parseInt((<HTMLInputElement> document.getElementById("precio")).value);

            let formData = new FormData();

            formData.append("neumatico_json", (new Entidades.NeumaticoBD(marca, medidas, precio, id)).ToJSON());

            Manejadora.AJAX.enviar_POST("./BACKEND/modificarNeumaticoBD.php", formData, (jsonRecibido : string) =>
            {
                let respuesta : any = JSON.parse(jsonRecibido);

                if(respuesta.exito)
                {
                    Manejadora.MostrarNeumaticosBD();
                }

                Manejadora.informar(respuesta.mensaje);
            });
        }

        public VerificarNeumaticoBD(): void 
        {
            let marca : string= (<HTMLInputElement> document.getElementById("marca")).value;
            let medidas : string = (<HTMLInputElement> document.getElementById("medidas")).value;

            let form = new FormData();
            form.append("obj_neumatico", `{"marca":"${marca}","medidas":"${medidas}"}`);

            Manejadora.AJAX.enviar_POST("./BACKEND/verificarNeumaticoBD.php", form, Manejadora.exitoVerificarNeumaticoBD);
        }

        private static exitoVerificarNeumaticoBD(neumaticoJSON : string) : void
        {
            let mensaje : string;
            let manejadora : Manejadora = new Manejadora(); 

            mensaje = "El neumatico no existe";
            if(neumaticoJSON !== "{}")
            {
                mensaje = "Se encontro el neumatico";
                (<HTMLDivElement> document.getElementById("divInfo")).innerHTML = Manejadora.armarTablaBD(`[${neumaticoJSON}]`);
                Manejadora.agregarFuncionesABotones(manejadora.BorrarNeumaticoFoto, manejadora.ModificarNeumaticoBD);
            }

            console.log(mensaje);
        }

        public static VerificarNeumaticoEstatico() : void
        {
            new Manejadora().VerificarNeumaticoBD();
        }

        public AgregarNeumaticoFoto(): void 
        {
            let marca : string= (<HTMLInputElement> document.getElementById("marca")).value;
            let medidas : string = (<HTMLInputElement> document.getElementById("medidas")).value;
            let precio : string = (<HTMLInputElement> document.getElementById("precio")).value;
            let foto : any = <HTMLImageElement> document.getElementById("foto");

            let form : FormData = new FormData();

            form.append("marca", marca);
            form.append("medidas", medidas);
            form.append("precio", precio);
            form.append("foto", foto.files[0]);

            Manejadora.AJAX.enviar_POST("./BACKEND/agregarNeumaticoBD.php", form, (respuesta_json : string)=>
            {
                let respuesta : any = JSON.parse(respuesta_json);
                Manejadora.MostrarNeumaticosBD();
                Manejadora.informar(respuesta.mensaje);
            });
        }

        public static AgregarNeumaticoFotoEstatico() : void
        {
            new Manejadora().AgregarNeumaticoFoto();
        }

        public BorrarNeumaticoFoto(neumatico_json: any): void 
        {
            if(confirm(`Seguro que desea eliminar el neumatico?:\n${neumatico_json.marca} - ${neumatico_json.medidas}`))
            {
                let formData = new FormData();

                formData.append("neumatico_json", JSON.stringify(neumatico_json))

                Manejadora.AJAX.enviar_POST("./BACKEND/eliminarNeumaticoBDFoto.php", formData, (respuestaJSON : string)=>
                {
                    let respuesta = JSON.parse(respuestaJSON);

                    if(respuesta.exito)
                    {
                        Manejadora.MostrarNeumaticosBD();
                    }

                    Manejadora.informar(respuesta.mensaje);
                });
                
            }
        }

        public ModificarNeumaticoBD(neumatico_json: any): void 
        {
            (<HTMLInputElement> document.getElementById("idNeumatico")).value = neumatico_json.id;
            (<HTMLInputElement> document.getElementById("marca")).value = neumatico_json.marca;
            (<HTMLInputElement> document.getElementById("medidas")).value = neumatico_json.medidas;
            (<HTMLInputElement> document.getElementById("precio")).value = neumatico_json.precio;
            (<HTMLImageElement> document.getElementById("imgFoto")).src = "./BACKEND/"+ neumatico_json.pathFoto;
        }

        public static ModificarNeumaticoBDFoto() : void
        {
            let id : number = parseInt((<HTMLInputElement> document.getElementById("idNeumatico")).value);
            let marca : string= (<HTMLInputElement> document.getElementById("marca")).value;
            let medidas : string = (<HTMLInputElement> document.getElementById("medidas")).value;
            let precio : number = parseInt((<HTMLInputElement> document.getElementById("precio")).value);
            let foto : any = <HTMLImageElement> document.getElementById("foto");

            let formData = new FormData();

            formData.append("neumatico_json", (new Entidades.NeumaticoBD(marca, medidas, precio, id)).ToJSON());
            formData.append("foto", foto.files[0]);

            Manejadora.AJAX.enviar_POST("./BACKEND/modificarNeumaticoBDFoto.php", formData, (jsonRecibido : string) =>
            {
                let respuesta : any = JSON.parse(jsonRecibido);

                if(respuesta.exito)
                {
                    Manejadora.MostrarNeumaticosBD();
                }
                else
                {
                    Manejadora.informar(respuesta.mensaje);
                }

            });
        }

        public MostrarBorradosJSON(): void 
        {
            Manejadora.AJAX.enviar_GET("./BACKEND/mostrarBorradosJSON.php", "", (tabla : string)=>
            {
                (<HTMLDivElement> document.getElementById("divInfo")).innerHTML = tabla;
                console.log(tabla)
            });
        }

        public static mostrarBorradosJSON() : void
        {
            new Manejadora().MostrarBorradosJSON();
        }

        public MostrarFotosModificados(): void 
        {
            Manejadora.AJAX.enviar_GET("./BACKEND/mostrarFotosDeModificados.php", "", (tabla : string)=>
            {
                (<HTMLDivElement> document.getElementById("divTabla")).innerHTML = tabla;
                console.log(tabla)
            });
        }

        public static mostrarFotosModificados() : void
        {
            new Manejadora().MostrarFotosModificados();
        }
/*
MostrarBorradosJSON: Invocará (por AJAX) a “./BACKEND/mostrarBorradosJSON.php” que muestra todo lo
registrado en el archivo “neumaticos_eliminados.json”. Para ello, agregar un método estático (en NeumaticoBD), llamado
mostrarBorradosJSON.
Informar por consola el mensaje recibido y en la página (div id='divInfo').

MostrarFotosModificados: Invocará (por AJAX) a “./BACKEND/mostrarFotosDeModificados.php” que muestra (en
una tabla HTML) todas las imagenes (50px X 50px) de los neumáticos registrados en el directorio
“./BACKEND/neumaticosModificados/”. Para ello, agregar un método estático (en NeumaticoBD), llamado mostrarModificados.
Mostrar el listado en la página (div id='divTabla').
*/
//http://localhost/dashboard/

    }
}

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