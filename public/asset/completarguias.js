function loadPage() {
    const general = new General();
    let tablaGuias = $('#tabla-guias').DataTable({
        ajax: {
            url: 'preguias-completar',
            method: 'POST',
            headers: general.jsonToken,
            data: function (d) {
                d.accion = 'consultar-guias';
            }
        },
        columns: [{
                data: 'nroGuia'
            },
            {
                data: 'fechaEmision'
            },
            {
                data: 'nomRemitente'
            },
            {
                data: 'nombreCiudadCluster'
            },
            {
                data: 'nombreCiudadFinal'
            },
            {
                data: 'nombreCluster'
            },
            {
                data: 'id',
                render : function(data){
                    return `<button class="btn btn-sm btn-outline-primary" data-guia="${data}">
                        <i class="fas fa-pencil-alt"></i>
                    </button>`
                }
            }
        ]
    });
    let detalleTabla = null,
        idGuia = null,
        newCliCli = false,
        tipoCbRemi = null;
    general.obtenerDetalleGuia().then(data => {
        detalleTabla = data;
    }).catch(error => {
        alertify.alert('Error', 'Ocurrió un problema al obtener los datos de la tabla detalle, por favor intentelo más tarde');
    });
    const cbOrdenServicio = document.getElementById('id_orden_servicio'),
        txtNGuia = document.getElementById('id_editar__nro_guia'),
        labelGuia = document.getElementById('label_nro_guia'),
        $boxComplejo = document.getElementById('id-editar-box-complejo-distante'),
        $cbCliente = document.getElementById('id_editar_id_remitente'),
        $cbDestinatario = document.getElementById("id_editar_id_destinatario"),
        $detalleGuiaTabla = document.getElementById('tabla-detalle');
    document.querySelector("#tabla-detalle-guias").onclick = async function(e){
        if(e.target.classList.contains("btn-outline-primary")){
            let datos = new FormData();
            datos.append("guia",e.target.dataset.guia);
            datos.append("accion","detalle-guia")
            try {
                const response = await general.funcfetch("preguias-completar",datos);
                if(response.error){
                    return alertify.error(response.error);
                }
                tipoCbRemi = response.datos.tipo_guia;
                $('#modal-completar-pre-guias .simple-select2, #modal-completar-pre-guias .cb-select2-tags, #modal-completar-pre-guias .cb-ubigeo').val("").trigger('change');
                idGuia = e.target.dataset.guia;
                const { listDistDest, listDistRemi, listProvDest, listProvRemi } = response.datos;
                general.transforData(listProvRemi, document.getElementById('id_editar_remi_prov'));
                general.transforData(listDistRemi, document.getElementById('id_editar_remi_dist'));
                general.transforData(listProvDest, document.getElementById('id_editar_dest_prov'));
                general.transforData(listDistDest, document.getElementById('id_editar_dest_dist'));
                const idInvitado = '#id_editar_id_invitado';
                const ccInvitado = $(idInvitado).attr('data-cuenta');
                $(ccInvitado).empty();
                $(ccInvitado).prop('disabled', true);
                $(idInvitado).empty();
                $(idInvitado).append(new Option('Ninguno', 'ninguno', false,false));
                if (response.datos.listInvitados.length > 0){
                    response.datos.listInvitados.forEach(invitado => {
                        const optNew = new Option(invitado.name + ' ' + invitado.last_name, invitado.id, false, false);
                        $(idInvitado).append(optNew)
                    });
                    $(ccInvitado).prop('disabled', false);
                }
                const valorInvitado = response.datos.id_invitado == null ? "ninguno" : response.datos.id_invitado;
                $(idInvitado).val(valorInvitado).trigger("change");
                if (response.datos.listCc.cc && response.datos.listCc.cc.length > 0) {
                    response.datos.listCc.cc.forEach(cc => {
                        const optNew = new Option(cc, cc, cc == response.datos.cc_invitado ? true : false, false);
                        $(ccInvitado).append(optNew)
                    });
                }
                $(ccInvitado).prop('required', valorInvitado != 'ninguno' ? true : false);
                if (response.datos.ciudadesFinales){
                    let templateOpt = "<option></option>";
                    response.datos.ciudadesFinales.forEach(c => {
                        templateOpt += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
                    });
                    document.getElementById('id_editar_ciudad_destino_final').innerHTML = templateOpt;
                }
                $('#modal-completar-pre-guias').modal("show");
                for (const key in response.datos) {
                    if (Object.hasOwnProperty.call(response.datos, key)) {
                        const valor = response.datos[key];
                        const dom = document.getElementById('id_editar_' + key);
                        if (key == 'comp_clie' && valor) {
                            if(valor === 'NINGUNO'){
                                $('#id_editar_nro_comp_cli').prop("disabled",true);
                            }
                            $(dom).val(valor.split(';'));
                            continue;
                        }
                        if (key == 'ciudad_alejada' && valor != null){
                            $boxComplejo.hidden = false;
                            dom.textContent = valor;
                        }
                        if (key == 'nro_comp_cli' && valor) {
                            let templateCom = "";
                            valor.split(';').forEach(ve => {
                                templateCom += `<option value="${ve}" selected>${ve}</option>`;
                            });
                            dom.innerHTML = templateCom;
                            continue;
                        }
                        if (key == 'detalle' && valor.length && detalleTabla) {
                            let $templateDetalle = "";
                            valor.forEach(de => {
                                $templateDetalle += general.templateDetalle(de.id, de.grupo_envio, de.tipo_envio, de.embase_embalaje, !de.descripcion ? '' : de.descripcion, de.cantidad, de.kg, de.largo, de.ancho, de.altura, de.volumen,detalleTabla);
                            });
                            document.getElementById('tabla-detalle').innerHTML = $templateDetalle;
                            continue;
                        }
                        if (key == 'detalle' && !valor.length && detalleTabla) {
                            document.getElementById('tabla-detalle').innerHTML = general.templateDetalle(null, null, null, null, '', 0, '0.00', '0.00', '0.00', '0.00', '0.00',detalleTabla);
                            continue;
                        }
                        if (key == 'prefijo') {
                            if (valor !== null && valor !== '') {
                                txtNGuia.value = valor.toString().padStart(3, '0') + '-' + response.datos['nro_guia'].toString().padStart(7, '0');
                                labelGuia.innerHTML = 'N° Guía ' + `<small class="text-danger">*</small>`;
                                cbOrdenServicio.value = '1';
                                txtNGuia.setAttribute('maxlength', 11);
                                txtNGuia.setAttribute('pattern', '[0-9]{3}[-][0-9]{7}');
                            } else {
                                labelGuia.innerHTML = 'N° OS ' + `<small class="text-danger">*</small>`;
                                txtNGuia.value = response.datos['nro_guia'].toString().padStart(7, '0');
                                cbOrdenServicio.value = '2';
                                txtNGuia.setAttribute('maxlength', 7);
                                txtNGuia.setAttribute('pattern', '[0-9]{7}');
                            }
                            continue;
                        }
                        if (!dom) {
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('.select-new-detalle').select2({
                    theme: 'bootstrap',
                    width: '100%',
                    placeholder: 'Seleccione una opción'
                });
                $('#modal-completar-pre-guias .simple-select2, #modal-completar-pre-guias .cb-select2-tags, #modal-completar-pre-guias .cb-ubigeo').trigger('change');
                if (response.datos.situ >= 2) {
                    $('.disable_estado_2').prop('disabled', true);
                }
            } catch (error) {
                console.error(error);
                alertify.error("error al obtener los datos de la guía/os");
            }
        }
    }
    $('#modal-completar-pre-guias').on('hide.bs.modal', function (e) {
        $('.disable_estado_2').prop('disabled', false);
        idGuia = null;
        tipoCbRemi = null;
        newCliCli = false;
    });
    $(cbOrdenServicio).on('select2:select', function (e) {
        let datosNroGuia = general.valoresFormatoGuia($(this).val());
        general.changeTipoGuiaFormato(datosNroGuia.idTxtNroGuia, null, datosNroGuia.max, datosNroGuia.condicion, datosNroGuia.textLabel, datosNroGuia.valid);
    });
    $('#id_editar_nro_comp_cli').on('select2:select', function (e) {
        $('#id_editar_cant_comp').val($(this).val() ? $(this).val().length : 0);
    });
    $($cbCliente).on('select2:select', async function (e) {
        tipoCbRemi = await general.getDataRemitenteGuia(this, '#id_editar_remi_prov', '#id_editar_remi_dist', '#id_editar_remi_depa', '#id_editar_remi_tipo_docu','#id_editar_id_invitado');
    });
    $($cbDestinatario).on('select2:select', async function (e) {
        newCliCli = await general.getDataDestGuia(this, '#id_editar_remi_depa', '#id_editar_dest_tipo_docu', '#id_editar_dest_depa', '#id_editar_dest_prov', '#id_editar_dest_dist', '#id_editar_ciudad_destino_final', '#id_editar_dias_tran', '#id_editar_via_envi','#id-editar-box-complejo-distante');
    });
    document.getElementById('add-new-detalle').onclick = function (event) {
        event.preventDefault();
        general.agregarNuevoDetalleGuia("1", '.select-new-detalle');
    }
    $detalleGuiaTabla.onclick = function (event) {
        if (event.target.classList.contains('eliminar-detalle-guia')) {
            general.eliminarDetalleGuia(event,$detalleGuiaTabla,"1",idGuia);
        }
    }
    document.getElementById('btn-actualizar-guias').onclick = () => document.getElementById('update-guia').click();    
    const $formEditarGuia = document.getElementById('update-guia-complete');
    $formEditarGuia.onsubmit = function(event){
        event.preventDefault();
        general.actualizarDatos($formEditarGuia,tipoCbRemi,idGuia,"1",newCliCli,'preguias-completar-editar','#modal-completar-pre-guias',$cbCliente,tablaGuias);
    }
}
function calcVolumen(event) {
    if (parseFloat(event.target.value) < 0 || isNaN(event.target.value)) {
        event.target.value = 0;
    }
    const fila = event.target.parentElement.parentElement;
    let total = 1;
    for (let i = 6; i < 9; i++) {
        total = total * parseFloat(fila.children[i].children[0].value);
    }
    fila.children[9].children[0].value = (total / 6000).toFixed(2);
    const e = new Event("change");
    fila.children[9].children[0].dispatchEvent(e);
}

function calcTotal(event, num, dom) {
    if (parseInt(event.target.value) < 0 || isNaN(event.target.value)) {
        event.target.value = 0;
    }
    const filaTotal = document.getElementById('tabla-detalle');
    let total = 0;
    for (let i = 0; i < filaTotal.children.length; i++) {
        const isFloat = filaTotal.children[i].children[num].children[0].getAttribute('step') == '0.01' ? true : false;
        total += isFloat ? parseFloat(filaTotal.children[i].children[num].children[0].value) : parseInt(filaTotal.children[i].children[num].children[0].value);
    }
    document.getElementById(dom).value = total;
}
window.addEventListener('DOMContentLoaded',loadPage);