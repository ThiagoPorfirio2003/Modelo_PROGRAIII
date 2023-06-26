"use strict";
class Ajax {
    constructor() {
        this._xhttp = new XMLHttpRequest();
        this.DONE = 4;
        this.OK = 200;
    }
    //No valida si es null
    auxiliar_ready(exito, error) {
        if (this._xhttp.readyState === this.DONE) {
            if (this._xhttp.status === this.OK) {
                if (exito !== undefined) {
                    exito(this._xhttp.responseText);
                }
            }
            else {
                if (error !== undefined) {
                    error(this._xhttp.statusText);
                }
            }
        }
    }
    enviar_GET(ruta, parametros = "", exito, error) {
        if (ruta != undefined && ruta.length > 0 &&
            parametros != undefined) {
            ruta = parametros.length > 0 ? ruta + "?" + parametros : ruta;
            this._xhttp.open("GET", ruta, true);
            this._xhttp.send();
            this._xhttp.onreadystatechange = () => this.auxiliar_ready(exito, error);
        }
    }
    enviar_POST(ruta, form, exito, error) {
        if (ruta != undefined && ruta.length > 0 &&
            form != undefined) {
            this._xhttp.open("POST", ruta, true);
            this._xhttp.setRequestHeader("enctype", "multipart/form-data");
            this._xhttp.send(form);
            this._xhttp.onreadystatechange = () => this.auxiliar_ready(exito, error);
        }
    }
}
//# sourceMappingURL=ajax.js.map