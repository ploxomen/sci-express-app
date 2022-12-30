function loadPage(){
    const general = new General();
    let $btnBuscarRemitente = document.getElementById('btnBuscarRemitente'),
        $txtNroInicioSCI = document.querySelector("#nroGuiaInicioSCI"),
        $txtNroRucRemitente = document.querySelector("#txtNumeroRuc"),
        $txtNombreRemitente =document.querySelector("#txtRemitente"),
        $txtCantiGuia = document.querySelector("#txtCantidadGuiasSCI"),
        $txtDireccionRemitente = document.querySelector("#txtDireccion");
    let $frmGuias = document.querySelector("#formGenerarGuias"),
        $btnNuevaGuiaTr = document.querySelector("#btnAgregarNuevaFilaGuia");
    let nroIdGuias = 1;
    let $tablaNuevaGuia = document.querySelector("#tablaNuevaGuia"),
        inputOneNuevaGuia = $tablaNuevaGuia.children[0].querySelector(`[data-input="nroguia"]`);
    let provinciasLima = [];
    let borrarTabla = true;
    [...document.querySelector("#idDestiProv1")].forEach(v=>{
        provinciasLima.push({
            value : v.value,
            text : v.text,
        })
    });
    let tipoFacturacion = null;
    $txtNroRucRemitente.oninput = e => {
        $txtNombreRemitente.value = "";
        $txtDireccionRemitente.value = "";
        tipoFacturacion = null;
    }
    $btnBuscarRemitente.onclick = async function(e){
        e.preventDefault();
        let datos = new FormData();
        datos.append('accion','obtener-remitente');
        datos.append('ruc',$txtNroRucRemitente.value);
        try {
            const response = await general.funcfetch('preguias-modificacion',datos);
            if(!response.data){
                $txtNombreRemitente.value = "";
                $txtDireccionRemitente.value = "";
                tipoFacturacion = null;
                return alertify.error('no se encontró remitente');
            }
            $txtNombreRemitente.value = response.data.nomb_prov || response.data.nom_cli;
            $txtDireccionRemitente.value = response.data.direccion;
            tipoFacturacion = response.data.tipo;
            return alertify.success("remitente encontrado");
        } catch (error) {
            tipoFacturacion = null;
            console.error(error);
            alertify.error("error al obtener los datos del remitente");
        }
    }

    function changeOptionNroGuia(e){
        $txtNroInicioSCI.value = e.target.dataset.numero;
        const pattern = e.target.value == 'os' ? "[0-9]{7}" : "[0-9]{3}[-][0-9]{7}";
        const minMaxLength = e.target.value == 'os' ? "7" : "11";
        $txtNroInicioSCI.setAttribute("pattern",pattern);
        $txtNroInicioSCI.setAttribute("minlength",minMaxLength);
        $txtNroInicioSCI.setAttribute("maxlength",minMaxLength);
        inputOneNuevaGuia.setAttribute("pattern",pattern);
        inputOneNuevaGuia.setAttribute("minlength",minMaxLength);
        inputOneNuevaGuia.setAttribute("maxlength",minMaxLength);
    }
    for (const $optionNroGuia of document.querySelectorAll(".changeNroSCI")) {
        $optionNroGuia.onchange = changeOptionNroGuia;
    }
    $frmGuias.addEventListener("submit",function(e){
        e.preventDefault();
        if(!tipoFacturacion){
            return alertify.error("no se encontró el remitente, por favor intente con otro número de RUC");
        }
        inputOneNuevaGuia.value = $txtNroInicioSCI.value;
        const limite = +$txtCantiGuia.value - 1;
        for (let index = 1; index <= limite; index++) {
            generarFilasNuevas();            
        }
        $('#generar-nro-guias').modal("show");
    });
    $('#generar-nro-guias').on('hidden.bs.modal', function (e) {
        if(borrarTabla){
            borrarTrTablaGenerarGuias();
        }
    });
    function borrarTrTablaGenerarGuias() {
        $('#generar-nro-guias .simple-select2, #generar-nro-guias .cb-ubigeo').select2('destroy');
        [...$tablaNuevaGuia.children].forEach((e,k) => {
        if(k > 0){
            e.remove();
        }
        });
        for (const elemento of $tablaNuevaGuia.children[0].querySelectorAll("select")) {
            if(elemento.classList.contains("simple-select2")){
                elemento.value = "1";
            }
            if(elemento.classList.contains("cb-ubigeo")){
                switch (elemento.dataset.consulta) {
                    case 'provincia':
                        elemento.value = "3926";
                    break;
                    case 'distrito':
                        let template = ""
                        provinciasLima.forEach(v => {
                            template += `<option value="${v.value}">${v.text}</option>`;
                        });
                        elemento.innerHTML = template;
                    break;
                    default:
                        elemento.innerHTML = "";
                    break;
                }
            }
        }
        $('#generar-nro-guias .simple-select2').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        $('#generar-nro-guias .cb-ubigeo').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        general.selectUbigeoCli($tablaNuevaGuia);
    }
    let $preguiaFormPrevius = document.querySelector("#frmPreGuiaPrevius"),
        $btnPreviusForm = document.querySelector("#btnFormSendPrev"),
        $btnGenerarGuias = document.querySelector("#btnSiguientePreGuiaGenerar");
    document.querySelector("#btnSiguientePreGuia").onclick = e => $btnPreviusForm.click();
    $btnNuevaGuiaTr.onclick = e => {
        generarFilasNuevas()
        $txtCantiGuia.value = +$txtCantiGuia.value + 1;
        alertify.success('fila agregada correctamente');
    };
    $btnGenerarGuias.onclick = (e) => {
        let msgConfirm = previusListaErrorGuias.length ? "Se encontraron inconvenientes en algunas Guías/OS generadas, solo se crearan aquellas que esten correctas." : "Se procederá a crear todas las Guías/OS generadas.";
        msgConfirm += "<br>¿Deseas continuar?";
        alertify.confirm("Mensaje",msgConfirm,async ()=>{
            let datos = new FormData($preguiaFormPrevius);
            datos.append('accion','generar-guias');
            datos.append('tipoFacturacion',tipoFacturacion);
            datos.append('tipo',document.querySelector(".changeNroSCI:checked").value);
            datos.append('nroRuc',$txtNroRucRemitente.value);
            try {
                const resultado = await general.funcfetch("preguias-modificacion",datos);
                if(resultado.success === false){
                    return alertify.error("no se detectó ninguna guía/os para ser creadas");
                }
                if(resultado.success === true){
                    previusListaGuias = [];
                    borrarTrTablaGenerarGuias();
                    $('#view-pre-guias').modal("hide");
                    alertify.alert("Mensaje","Guías/OS creadas correctamente",() => window.location.reload());
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al crear las Guías/OS");
            }
        },()=>{})
        
    }
    let previusListaGuias = [];
    let previusListaErrorGuias = [];
    $preguiaFormPrevius.addEventListener('submit', async function(e){
        e.preventDefault();
        previusListaGuias = [];
        for (const tr of $tablaNuevaGuia.children) {
            const trayecto = tr.querySelector("[data-tipo='trayecto']");
            const traslado = tr.querySelector("[data-tipo='traslado']");
            const dpto = tr.querySelector(".cb-ubigeo[name='departamento[]']");
            const prov = tr.querySelector(".cb-ubigeo[name='provincia[]']");
            const dist = tr.querySelector(".cb-ubigeo[name='distrito[]']");
            previusListaGuias.push({
                nroGuia : tr.querySelector("input").value,
                trayecto : trayecto.options[trayecto.selectedIndex].text,
                traslado : traslado.options[traslado.selectedIndex].text,
                dpto: dpto.options[dpto.selectedIndex].text,
                prov: prov.options[prov.selectedIndex].text,
                dist: dist.options[dist.selectedIndex].text
            });
        }
        let datos = new FormData(this);
        datos.append('accion','previo-guias');
        datos.append('tipoFacturacion',tipoFacturacion);
        datos.append('tipo',document.querySelector(".changeNroSCI:checked").value);
        datos.append('nroRuc',$txtNroRucRemitente.value);
        try {
            const resultado = await general.funcfetch("preguias-modificacion",datos);
            previusListaErrorGuias = resultado.listaError;
            agregarListaGuias();
            borrarTabla = false;
            $('#generar-nro-guias').modal('hide');
            setTimeout(() => {
                $('#view-pre-guias').modal('show');
            }, 500);
        } catch (error) {
            console.error(error);
            alertify.error("error al obtener los datos previos");
        }
        
    });
    $('#view-pre-guias').on('hidden.bs.modal', function (e) {
        borrarTabla = true;
        previusListaErrorGuias = [];
        setTimeout(() => {
            if(previusListaGuias.length){
                $('#generar-nro-guias').modal('show');
            }
        }, 500);
    })
    let $listaPrevioGuia = document.querySelector("#listaViewPreguias");
    function agregarListaGuias(){
        $listaPrevioGuia.innerHTML = "";
        previusListaGuias.forEach(e => {
            const existe = previusListaErrorGuias.findIndex(error => error.nroGuia == e.nroGuia);
            const li = document.createElement("li");
            let template = "";
            if(existe >= 0){
                template = `
                <div class="mb-1">
                    <small class="text-danger"><i class="fas fa-exclamation-circle"></i> ${previusListaErrorGuias[existe].error}</small>
                </div>
                `
            }else{
                template = `
                <div class="mb-1">
                    <small class="text-success"><i class="far fa-check-circle"></i> Listo para ser agregado</small>
                </div>
                `
            }
            template += `<div class="d-flex flex-wrap" style="gap:10px;">
                <div>
                    <b>N° Guía/OS:</b>
                    <span>${e.nroGuia}</span>
                </div>
                <div>
                    <b>Trayecto:</b>
                    <span>${e.trayecto}</span>
                </div>
                <div>
                    <b>Traslado:</b>
                    <span>${e.traslado}</span>
                </div>
                <div>
                    <b>Departamento:</b>
                    <span>${e.dpto}</span>
                </div>
                <div>
                    <b>Provincia:</b>
                    <span>${e.prov}</span>
                </div>
                <div>
                    <b>Distrito:</b>
                    <span>${e.dist}</span>
                </div>
            </div>`
            li.className = "mb-3";
            li.innerHTML = template;
            $listaPrevioGuia.append(li);
        });
        document.querySelector("#totalPreGuiasText").textContent = previusListaGuias.length ;
    }
    function generarFilasNuevas() {
        nroIdGuias++;
        $('#generar-nro-guias .simple-select2, #generar-nro-guias .cb-ubigeo').select2('destroy');
        const primero = $tablaNuevaGuia.children[0];
        const longitudTabla = $tablaNuevaGuia.children.length;
        const nuevoTr = primero.cloneNode(true);
        nuevoTr.children[0].textContent = longitudTabla + 1;
        for (const elemento of nuevoTr.querySelectorAll("select, input")) {
            if(elemento.tagName == "INPUT"){
                const nroGuiaAnterior = $tablaNuevaGuia.children[longitudTabla - 1].querySelector(`[data-input="nroguia"]`).value;
                elemento.value = nuevoNumeroGuia(nroGuiaAnterior);
                continue;
            }
            if(elemento.classList.contains("simple-select2")){
                elemento.value = "1";
                elemento.id = elemento.dataset.tipo == "trayecto" ? "idTrayectoEnvio" + nroIdGuias : "idViaTraslado" + nroIdGuias;
            }
            if(elemento.classList.contains("cb-ubigeo")){
                switch (elemento.dataset.consulta) {
                    case 'provincia':
                        elemento.value = "3926";
                        elemento.id = "idDestiDepart" + nroIdGuias;
                        elemento.dataset.info = "idDestiProv" + nroIdGuias;
                        elemento.dataset.infoDistrito = "idDestiDist" + nroIdGuias;
                    break;
                    case 'distrito':
                        let template = ""
                        provinciasLima.forEach(v => {
                            template += `<option value="${v.value}">${v.text}</option>`;
                        });
                        elemento.innerHTML = template;
                        elemento.id = "idDestiProv" + nroIdGuias;
                        elemento.dataset.info = "idDestiDist" + nroIdGuias;
                    break;
                    default:
                        elemento.innerHTML = "";
                        elemento.id = "idDestiDist" + nroIdGuias;
                    break;
                }
            }
        }
        $tablaNuevaGuia.append(nuevoTr);
        $('#generar-nro-guias .simple-select2').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        $('#generar-nro-guias .cb-ubigeo').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
        general.selectUbigeoCli($tablaNuevaGuia);
    }
    function nuevoNumeroGuia(value){
        const radio = document.querySelector(".changeNroSCI:checked");
        let resultado = "";
        if(radio.value = "guia"){
            let nroGuia = value.split("-");
            if(nroGuia[1] == 9999999){
                resultado = (+nroGuia[0] + 1).toString().padStart(3,"0") + '-' + '0000001';
            }else if (nroGuia[1] < 9999999){
                resultado =  nroGuia[0] + '-' + (+nroGuia[1] + 1).toString().padStart(7,"0");
            }
        }else if(radio.value = "os"){
            resultado = (+value + 1).toString().padStart(7,"0");
        }
        return resultado;
    }
    function resetearNumeracion($tablaBody){
        [...$tablaBody.children].forEach((e,k) => {
            e.children[0].textContent = (k+1);
        });
    }
    $tablaNuevaGuia.onclick = function(e){
        if(e.target.classList.contains('btn-outline-danger')){
            if($tablaNuevaGuia.children.length <= 1){
                return alertify.error("debe existir como mínimo una fila");
            }
            alertify.confirm("Mensaje",'¿Deseas eliminar esta fila?',()=>{
                e.target.parentElement.parentElement.remove();
                resetearNumeracion($tablaNuevaGuia);
                $txtCantiGuia.value = +$txtCantiGuia.value - 1;
                alertify.success("fila eliminada");
            },()=>{})
        }
    }
}
window.addEventListener('DOMContentLoaded',loadPage);