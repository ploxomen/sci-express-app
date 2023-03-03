function loadPage() {
    let general = new General();
    let detalleTabla = null;
    let datosGlobales = {
        idGuia: null
    }
    let newCliCli = false;
    const $cbCliente = document.getElementById('id_editar_id_remitente');
    let tipoCbRemi = null;
    general.obtenerDetalleGuia().then(data => {
        detalleTabla = data;
    }).catch(errer => {
        alertify.alert('Error', 'Ocurrió un problema al obtener los datos de la tabla detalle, por favor intentelo más tarde');
    });
    $('.simple-select2-2').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: 'Seleccione una opción'
    })
    const tblDetalleGuia = document.getElementById('tabla-detalle-guias');
    let tablaGuias = $('#tabla-guias').DataTable({
        ajax: {
            url: 'mis-guias',
            method: 'POST',
            headers: general.jsonToken,
            data: function (d) {
                d.estado = $('#estado_guias').val();
                d.tipo = $('#id_tipo').val();
                d.fechaInicio = $('#fecha_inicio').val();
                d.fechaFin = $('#id_fecha_fin').val();
            }
        },
        columns: [{
                data: 'nro_guia'
            },
            {
                data: 'fecha_emision'
            },
            {
                data: 'fecha_traslado',
            },
            {
                data: 'nro_comp_cli',
                render : function(data){
                    if(data == null || data == ""){
                        return 'Sin comprobantes';
                    }
                    return data.replace(/;/i,"<br>");
                }
            },
            {
                data: 'dias_tran'
            },
            {
                data: 'nom_remi'
            },
            {
                data: 'nom_dest'
            },
            {
                data: 'ciudad_base_cluster'
            },
            {
                data: 'ciudad_final'
            },
            {
                data: 'listClusters',
                render: function (data, type, row) {
                    if (row.guia_tipo == 'INACTIVA' || row.guia_tipo == 'ANULADA') {
                        return '<span class="text-danger">Sin acciones</span>';
                    }
                    let tempClust = `<option></option>`;
                    if (data) {
                        data.forEach(c => {
                            tempClust += `<option value="${c.id_cluster}" ${row.id_cluster == c.id_cluster ? 'selected' : ''}>${c.nombres_completos}</option>`;
                        });
                    }
                    return `<select data-guia="${row.id}" ${row.situ >= 5 ? 'disabled' : ''} class="form-control form-control-sm cb-clustes">${tempClust}</select>`;
                }
            },
            {
                data: 'id_asignado',
                searchable: false,
                render: function (data, type, full) {
                    if (full.guia_tipo == 'INACTIVA' || full.guia_tipo == 'ANULADA') {
                        return '<span class="text-danger">Sin acciones</span>'
                    }
                    let tempUsu = `<option></option>`;
                    for (let i = 0; i < full.listUsu.length; i++) {
                        tempUsu += `<option value="${full.listUsu[i].id}" ${full.listUsu[i].status != 1 ? 'disabled' : ''} ${data == full.listUsu[i].id ? 'selected' : ''}>${full.listUsu[i].name} ${full.listUsu[i].last_name}</option>`;
                    }
                    return `<select data-guia="${full.id}" ${full.situ != 1 ? 'disabled' : ''} class="form-control form-control-sm cb-usuarios">${tempUsu}</select>`;
                }
            },
            {
                data: 'situ',
                searchable: false,
                orderable: false,
                render: function (data, type, full) {
                    if (full.guia_tipo == 'INACTIVA' || full.guia_tipo == 'ANULADA') {
                        return '<span class="text-danger">Sin acciones</span>'
                    }
                    let template = ``;
                    full.listSoli.forEach(sol => {
                        template += `<option value="${sol.id}" ${data == sol.id ? 'selected' : ''} ${sol.id < 10 ? 'disabled' : ''}>${sol.tipo}</option>`;
                    });
                    return `<select data-guia="${full.id}" class="form-control form-control-sm cb-estado-change">${template}</select>`;
                }
            },
            {
                data: 'guia_tipo'
            },
            {
                data: 'id',
                searchable: false,
                orderable: false,
                name: 'id',
                render: function (data, type, full) {
                    let temButtons = `<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button data-toggle="tooltip" title="Visualizar solicitud" data-guia="${data}" class="btn btn-sm btn-secondary view-guia"><i class="far fa-eye"></i></button><button data-guia="${data}" class="btn btn-sm btn-primary editar-guia"><i class="fas fa-pen"></i></button>`;
                    if (full.guia_tipo != 'ANULADA') {
                        temButtons += `<button data-toggle="tooltip" title="Anular guia" data-guia="${data}" class="btn btn-sm btn-danger delete-guia"><i class="fas fa-trash-alt"></i></button></div>`;
                    } else {
                        temButtons += '</div>';
                    }
                    return temButtons;
                }
            }
        ],
        createdRow: function (row, data) {
            let bgTable = !data.id_asignado && data.guia_tipo == 'ACTIVA' ? 'table-warning' : '';
            $(row).addClass(bgTable);
        }
    });
    let estado = 0;
    const $nroGuia = document.getElementById('titulo-guia-editar'),
        cbOrdenServicio = document.getElementById('id_orden_servicio'),
        txtNGuia = document.getElementById('id_editar__nro_guia'),
        labelGuia = document.getElementById('label_nro_guia'),
        $boxImgGuia = document.getElementById('list-img'),
        $boxComplejo = document.getElementById('id-editar-box-complejo-distante');
    tblDetalleGuia.addEventListener('click', async function (e) {
        if (e.target.classList.contains('delete-guia')) {
            const guia = e.target.dataset.guia;
            alertify.confirm('Mensaje', '¿Deseas anular esta guía?', async () => {
                let datos = new FormData();
                datos.append('id_guia', guia);
                let response = await general.funcfetch('anular-guia', datos);
                if (response.error) {
                    alertify.alert('Error', response.error);
                    return
                }
                alertify.success(response.success);
                tablaGuias.draw();
            }, () => {});
        }
        if (e.target.classList.contains('view-guia')) {
            let datos = new FormData();
            datos.append('guia', e.target.dataset.guia);
            datos.append('tipo', 'detalle');
            let response = await general.funcfetch('obtener-detalles', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            general.verDetalleGuia(response.datos);
        }
        if (e.target.classList.contains('editar-guia')) {
            let datos = new FormData();
            datos.append('guia', e.target.dataset.guia);
            datos.append('tipo', 'view-editar');
            let response = await general.funcfetch('obtener-detalles', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            tipoCbRemi = response.datos.tipo_guia;
            estado = response.datos.situ;
            $('#editar-guias .simple-select2, #editar-guias .cb-select2-tags, #editar-guias .cb-ubigeo').val("").trigger('change');
            datosGlobales.idGuia = e.target.dataset.guia;
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
            $('#editar-guias').modal('show');
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
                    if(key == 'id_remitente'){
                        document.querySelector(`#id_editar_id_remitente option[value='${valor}'][data-type='${response.datos.tipo_guia}'`).selected = true;
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
                            $nroGuia.textContent = 'Guía N° ' + valor.toString().padStart(3, '0') + '-' + response.datos['nro_guia'].toString().padStart(7, '0');
                            txtNGuia.value = valor.toString().padStart(3, '0') + '-' + response.datos['nro_guia'].toString().padStart(7, '0');
                            labelGuia.innerHTML = 'N° Guía ' + `<small class="text-danger">*</small>`;
                            cbOrdenServicio.value = '1';
                            txtNGuia.setAttribute('maxlength', 11);
                            txtNGuia.setAttribute('pattern', '[0-9]{3}[-][0-9]{7}');
                        } else {
                            labelGuia.innerHTML = 'N° OS ' + `<small class="text-danger">*</small>`;
                            $nroGuia.textContent = 'Orden de Servicio N° ' + response.datos['nro_guia'].toString().padStart(7, '0');
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
            $('.enable-estado-4').prop('disabled',true);

            $('#editar-guias .simple-select2, #editar-guias .cb-select2-tags, #editar-guias .cb-ubigeo').trigger('change');
            if (response.datos.estado === 3) {
                $('.disable_inactiva').prop('disabled', true);
            }
            if(response.datos.situ === 1){
                $('.disable-estado-1').prop('disabled',true);
            }
            if(response.datos.situ >= 4){
                $('.enable-estado-4').prop('disabled',false);
            }
            if (response.datos.situ >= 2) {
                $('.disable_estado_2').prop('disabled', true);
            }
            if (response.datos.situ >= 7) {
                $('.disable_estado_7').prop('disabled', false);
                $('#id_editar_situacion_envio').trigger('change');
            }
            if (response.datos.situ >= 8) {
                $boxImgGuia.append(ButtonAddImgDelivery());
                $('.disable_estado_8').prop('disabled', false);
                if (response.datos.imagenes) {
                    response.datos.imagenes.map((img) => {
                        $boxImgGuia.append(ImageDelivery(img.nom_file, img.id, img.nom_id_file))
                    });
                }
                $('#id_editar_situacion_durante_envio').trigger('change');
                $('#id_editar_estado_entregado').trigger('change');
                return
            }
            let templateImg = "";
            if (response.datos.situ < 8 && response.datos.estado !== 3) {
                templateImg = "<h5 class='text-primary'>La guía/OS aun no está habilitada para ingresar nuevas imágenes</h5>"
            }
            if (response.datos.situ < 8 && response.datos.estado === 3){
                templateImg = "<h5 class='text-primary'>No se pueden ingresar imágenes a las guías/OS de tipo inactivas</h5>"
            }
            $boxImgGuia.innerHTML = templateImg;
            $('#id_editar_estado').trigger('change');
        }
    });
    $('#id_editar_id_invitado').on('select2:select', async function (e) {
        general.getCCInvitados('#id_editar_id_invitado', $cbCliente);
    });
    document.getElementById('view-lista-guias').onclick = async function(e){
        e.preventDefault();
        const response = await general.funcfetch('obtener-asignacion-global',null);
        if (response.guias){
            let template = "";
            response.guias.forEach(g => {
                const nroGuia = !g.prefijo ? g.nro_guia.toString().padStart(7, '0') : g.prefijo.toString().padStart(3, '0') +'-'+ g.nro_guia.toString().padStart(7, '0');
                template+=`<tr>
                <td class="text-center">
                    <input type="checkbox" value="${g.id}">
                </td>
                <td>
                    ${nroGuia}
                </td>
                <td>
                    ${g.fecha_emision}
                </td>
                </tr>`
            });
            if (!response.guias.length){
                document.getElementById('asignar-masivo-guias').hidden = true;
            }
            document.getElementById('llenar-guias-por-asignar').innerHTML = !template ? `<tr><td colspan="3" class="text-center">No se encontraron guías/OS pendientes para ser asignadas</td></tr>` : template;
            $('#modal-asignar-masivo').modal('show');
        }
    }
    $('#modal-asignar-masivo').on('hide.bs.modal', function (e) {
        document.getElementById('asignar-masivo-guias').hidden = false;
        document.getElementById('seleccion-todo-asignar').checked = false;
    });
    document.getElementById('seleccion-todo-asignar').onchange = function(e){
        for (const checkbox of document.querySelectorAll('#llenar-guias-por-asignar input[type="checkbox"]')) {
            checkbox.checked = e.target.checked;
        }
    }
    document.getElementById('asignar-masivo-guias').onclick = e => {
        let guiaAsignar = [];
        const selectGuias = document.querySelectorAll(`#llenar-guias-por-asignar input[type="checkbox"]:checked`);
        for (const checkbox of selectGuias) {
            guiaAsignar.push({
                id : checkbox.value
            });
        }
        if(!guiaAsignar.length){
            return alertify.error('Por favor selecione una o más guias/OS para ser asignados');
        }
        alertify.confirm('Mensaje', '¿Estas seguro de asignar ' + guiaAsignar.length + ' guía/OS al usuario ' + $('#id_asignacion_user option:selected').text() + '?',async ()=>{
            let datos = new FormData();
            datos.append('id_usuario', $('#id_asignacion_user').val());
            datos.append('guias', JSON.stringify(guiaAsignar));
            const response = await general.funcfetch('asignar-global', datos);
            if(response.success){
                alertify.success(response.success);
                tablaGuias.draw();
                document.getElementById('total-por-procesar-guias').textContent = response.porAsignar;
                $('#modal-asignar-masivo').modal('hide');
            }
        },()=>{})
    }
    $('#id_editar_estado_entregado').on('change',function(e){
        let active = false;
        if ($(this).val() === 'ENTREGADO'){
            $('#id_editar_motivo_no_ent').val("");
            active = true;
        }
        $('#id_editar_motivo_no_ent').prop('disabled', active);
        $('#id_editar_motivo_no_ent').prop('required', active ? false : true);
    });
    const listEntrega = document.getElementById('visitas-entregas');
    $('#id_editar_nro_visitas_reparto').on('select2:select',function(e){
        const valor = $(this).val();
        if ($('#visitas-entregas .motivo-no-entregado')){
            $('#visitas-entregas .motivo-no-entregado').select2('destroy');
        }
        if(valor == 'PRIMERA'){
            listEntrega.innerHTML = "";
            return
        }
        let detalleEntrega = [];
        const longitudLista = listEntrega.children.length;
        if (valor == 'SEGUNDA' || (valor == 'TERCERA' && longitudLista === 0)){
            detalleEntrega.push(
                {
                    visita: 'PRIMERA',
                    motivo: ""
                }
            );
        }
        if (valor == 'TERCERA') {
            detalleEntrega.push({
                visita: 'SEGUNDA',
                motivo: ""
            })
        }
        if (detalleEntrega.length < listEntrega.children.length){
            for (let i = listEntrega.children.length; i > detalleEntrega.length; i--) {
                listEntrega.children[i-1].remove();
            }
            $('#visitas-entregas .motivo-no-entregado').select2({
                theme: 'bootstrap',
                width: '100%',
                placeholder: 'Seleccione una opción'
            })
            return
        }
        detalleEntrega.forEach(entrega => {
            listEntrega.append(AddVisitaEntrega(entrega.visita, entrega.motivo));
        });
        
        $('#visitas-entregas .motivo-no-entregado').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        })
    });
    function AddVisitaEntrega(visita,motivo){
        const lista = document.createElement('li');
        lista.classList.add('form-group');
        lista.innerHTML = `<div class="d-flex" style="gap: 0 15px;">
            <div>
                <input type="text" class="form-control form-control-sm" value="${visita}" maxlength="15" disabled>
            </div>
            <div style="width:100%; min-width:150px; max-width:150px;">
                <select class="motivo-no-entregado" required>
                    option></option>
                    <option value="AUSENTE" ${motivo === 'AUSENTE' ? 'selected' : ''}>AUSENTE</option>
                    <option value="DIR INCOMPLETA" ${motivo === 'DIR INCOMPLETA' ? 'selected' : ''}>DIR INCOMPLETA</option>
                    <option value="SE MUDO" ${motivo === 'SE MUDO' ? 'selected' : ''}>SE MUDO</option>
                    <option value="DESCONOCIDO" ${motivo === 'DESCONOCIDO' ? 'selected' : ''}>DESCONOCIDO</option>
                    <option value="RECHAZADO" ${motivo === 'RECHAZADO' ? 'selected' : ''}>RECHAZADO</option>
                </select>
            </div>
        </div>`
        return lista;
    }
    function ButtonAddImgDelivery(){
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.classList.add('new-img');
        btn.innerHTML = `<i class="far fa-image"></i>`;
        return btn;
    }
    function ImageDelivery(nomFile,idFile,nomIdFile){
        const contenedor = document.createElement('div');
        contenedor.classList.add('img-delivery');
        contenedor.innerHTML = `<button type="button" class="eliminar-img" title="Eliminar esta imagen" data-name="${nomFile}" data-id="${idFile}"><i class="far fa-trash-alt"></i></button>
            <img src="${window.location.origin + "/intranet/storage/img/entrega/" + nomIdFile}"
            title="${nomFile}"
            alt="Imagen de entrega de la guía">
        </button>`;
        return contenedor;
    }
    const inputAddImg = document.getElementById('sendNewImg');
    $boxImgGuia.onclick = function(event){
        if (event.target.classList.contains('eliminar-img') && datosGlobales.idGuia) {
            alertify.confirm('Eliminar imagen','¿Estas seguro de eliminar la image ' + event.target.dataset.name + '?',async ()=>{
                let datos = new FormData();
                datos.append('id_img',event.target.dataset.id);
                datos.append('id_guia', datosGlobales.idGuia);
                const response = await general.funcfetch('delete-img-guia', datos);
                if(response.error){
                    return alertify.alert('Error',response.error);
                }
                alertify.success(response.success);
                event.target.parentElement.remove();
            },()=>{})
        }
        if (event.target.classList.contains('new-img') && datosGlobales.idGuia) {
            inputAddImg.click();
        }
    }
    inputAddImg.onchange = function(event){
        if (event.target.files.length && datosGlobales.idGuia) {
            let datos = new FormData();
            datos.append('id_guia', datosGlobales.idGuia);
            for (let i = 0; i < event.target.files.length; i++) {
                datos.append('image[]', event.target.files[i]);
            }
            const response = new XMLHttpRequest();
            response.onreadystatechange = () => {
                if(response.readyState == 4 && response.status == 200){
                    const {success,list} = JSON.parse(response.responseText);
                    list.forEach(img => {
                        $boxImgGuia.append(ImageDelivery(img.nom_file, img.id_img, img.nom_id_file));
                    });
                    alertify.success(success);
                }
            }
            response.open('POST','upload-img-entrega');
            response.setRequestHeader('X-CSRF-TOKEN',general.token);
            response.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            response.send(datos);
        }
    }
    document.getElementById('btn-actualizar-guias').onclick = () => document.getElementById('update-guia').click();    
    const $formEditarGuia = document.getElementById('update-guia-complete');
    $formEditarGuia.onsubmit = function(event){
        event.preventDefault();
        general.actualizarDatos($formEditarGuia,tipoCbRemi,datosGlobales.idGuia,document.getElementById('id_editar_estado').value,newCliCli,'update-total-guia','#editar-guias',$cbCliente,tablaGuias);
    }
    
    $($cbCliente).on('select2:select', async function (e) {
        tipoCbRemi = await general.getDataRemitenteGuia(this, '#id_editar_remi_prov', '#id_editar_remi_dist', '#id_editar_remi_depa', '#id_editar_remi_tipo_docu','#id_editar_id_invitado');
    });
    $('#id_editar_id_destinatario').on('select2:select', async function (e) {
        newCliCli = await general.getDataDestGuia(this, '#id_editar_remi_depa', '#id_editar_dest_tipo_docu', '#id_editar_dest_depa', '#id_editar_dest_prov', '#id_editar_dest_dist', '#id_editar_ciudad_destino_final', '#id_editar_dias_tran', '#id_editar_via_envi','#id-editar-box-complejo-distante',estado);
    });
    $('.calculo-dia').on('select2:select', function () {
        general.calculoDiasTrancitoCb('#id_editar_remi_depa', '#id_editar_dest_depa', '#id_editar_dest_prov', '#id_editar_dias_tran', '#id_editar_via_envi');
        const distrito = $(this).attr("data-cli-cli") == "distrito" ? $(this).val() : $($(this).attr("data-distrito")).val();
        const tipo = $(this).attr("data-cli-cli") == "distrito" ? "distrito" : "ciudad";
        general.consultaComplejoDistante(distrito, tipo, "#id-editar-box-complejo-distante", tipo == "ciudad" ? $(this).val() : null);
    });
    $(cbOrdenServicio).on('select2:select', function (e) {
        let datosNroGuia = general.valoresFormatoGuia($(this).val());
        general.changeTipoGuiaFormato(datosNroGuia.idTxtNroGuia, null, datosNroGuia.max, datosNroGuia.condicion, datosNroGuia.textLabel, datosNroGuia.valid);
    });
    $('#id_editar_comp_clie').on('select2:select', function (e) {
        general.validarSeleccionComprobantes($(this).val(), '#id_editar_comp_clie', '#id_editar_nro_comp_cli');
    });
    $('#id_editar_nro_comp_cli').on('change.select2', function (e) {
        $('#id_editar_cant_comp').val($(this).val() ? $(this).val().length : 0);
    });
    const $detalleGuiaTabla = document.getElementById('tabla-detalle');
    $detalleGuiaTabla.onclick = function (event) {
        if (event.target.classList.contains('eliminar-detalle-guia')) {
            general.eliminarDetalleGuia(event,$detalleGuiaTabla,$('#id_editar_estado').val(),datosGlobales.idGuia);
        }
    }
    document.getElementById('add-new-detalle').onclick = function (event) {
        event.preventDefault();
        general.agregarNuevoDetalleGuia('id_editar_estado', '.select-new-detalle');
    }
    $('#id_editar_dest_dist').on('select2:select', function (e) {
        general.getCiudadesFinales($(this).val(), $(this).attr('data-ciudades'));
    });
    $('#id_editar_situacion_envio').on('change', function (event) {
        validarEntregaReparto('#id_editar_incidencia_envio', '#id_editar_text_disconforme', $(this).val());
    });
    $('#id_editar_situacion_durante_envio').on('change', function (event) {
        validarEntregaReparto('#id_editar_incidencia_durante_envio', '#id_editar_text_durante_disconforme', $(this).val());
    });

    function validarEntregaReparto($incidencia, $textoDisconforme, valor) {
        $($incidencia).prop('disabled', valor === 'CONFORME' ? true : false);
        $($textoDisconforme).prop('disabled', valor === 'CONFORME' ? true : false);
        if (valor === 'CONFORME') {
            $($incidencia).val("").trigger('change');
            $($textoDisconforme).val("");
        }
    }
    
    $('#editar-guias').on('hide.bs.modal', function (e) {
        $boxImgGuia.innerHTML = "";
        $('.disable_estado_2, .disable_inactiva').prop('disabled', false);
        $('.disable_estado_7, .disable_estado_8').val("").trigger('change');
        document.getElementById('id_editar_ciudad_destino_final').innerHTML = "";
        datosGlobales.idGuia = null;
        tipoCbRemi = null;
        estado = 0;
        newCliCli = false;
    });
    tablaGuias.on('responsive-display', function (e, datatable, row, showHide, update) {
        const tabla = e.target.children[0].children[0];
        if (tabla.children[8].style.display == 'none') {
            $('.cb-clustes').off('change');
            $('.cb-clustes').attr('data-responsive', 'true');
            $('.cb-clustes').on('change', async function () {
                let datos = new FormData();
                datos.append('id_guia', $(this).attr('data-guia'));
                datos.append('id_cluster', $(this).val());
                let response = await general.funcfetch('cambiar-cluster', datos);
                if (response.error) {
                    alertify.alert('Error', response.error);
                    return
                }
                alertify.success(response.success);
            });
        }
        if (tabla.children[9].style.display == 'none') {
            $('.cb-usuarios').off('change');
            $('.cb-usuarios').attr('data-responsive', 'true');
            $('.cb-usuarios').on('change', async function (event) {
                let datos = new FormData();
                datos.append('id_guia', event.target.dataset.guia);
                datos.append('id_user', $(this).val());
                let response = await general.funcfetch('asignar-personal', datos);
                if (response.error) {
                    alertify.alert('Mensaje', response.error);
                    return
                }
                document.getElementById('total-por-procesar-guias').textContent = response.procesar;
                alertify.success(response.success);
            });
        }
        if (tabla.children[10].style.display == 'none') {
            $('.cb-estado-change').off('change');
            $('.cb-estado-change').attr('data-responsive', 'true');
            $('.cb-estado-change').on('change', async function () {
                let datos = new FormData();
                datos.append('id_guia', $(this).attr('data-guia'));
                datos.append('estado', $(this).val());
                let response = await general.funcfetch('cambiar-estado', datos);
                if (response.error) {
                    alertify.alert('Error', response.error);
                    return
                }
                alertify.success(response.success);
            })
        }
    });
    tablaGuias.on('draw.dt', function () {
        $('.cb-usuarios').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('select2:select', async function (event) {
            let datos = new FormData();
            datos.append('id_guia', event.target.dataset.guia);
            datos.append('id_user', $(this).val());
            let response = await general.funcfetch('asignar-personal', datos);
            if (response.error) {
                alertify.alert('Mensaje', response.error);
                return
            }
            document.getElementById('total-por-procesar-guias').textContent = response.procesar;
            alertify.success(response.success);
        });
        $('.cb-clustes').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('select2:select', async function (event) {
            let datos = new FormData();
            datos.append('id_guia', $(this).attr('data-guia'));
            datos.append('id_cluster', $(this).val());
            let response = await general.funcfetch('cambiar-cluster', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            alertify.success(response.success);
        });
        $('.cb-estado-change').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('select2:select', async function (event) {
            let datos = new FormData();
            datos.append('id_guia', $(this).attr('data-guia'));
            datos.append('estado', $(this).val());
            let response = await general.funcfetch('cambiar-estado', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            alertify.success(response.success);
        });
    });
    tablaGuias.on('responsive-resize', function (e, datatable, columns) {
        if (columns[8] && $('.cb-clustes').attr('data-responsive') == 'true') {
            $('.cb-clustes').off('change');
            $('.cb-clustes').attr('data-responsive', 'false');
        }
        if (columns[9] && $('.cb-usuarios').attr('data-responsive') == 'true') {
            $('.cb-usuarios').off('change');
            $('.cb-usuarios').attr('data-responsive', 'false');
        }
        if (columns[10] && $('.cb-estado-change').attr('data-responsive') == 'true') {
            $('.cb-estado-change').off('change');
            $('.cb-estado-change').attr('data-responsive', 'false');
        }
    });
    for (const filtros of document.querySelectorAll('.cb-filtros')) {
        filtros.addEventListener('change', function (e) {
            tablaGuias.draw();
        });
    }
    document.getElementById('frm-aplicar-filtros').addEventListener('submit', (e) => {
        e.preventDefault();
        tablaGuias.draw();
    });
    $('#id_editar_estado').on('change', function (e) {
        general.changeTipoGuia(this.value);
    });
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
window.addEventListener('DOMContentLoaded', loadPage);
