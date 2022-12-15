function loadPage(){
    $('#lista-detalles-bultos .cb-ubigeo').select2('destroy');
    let general = new General();
    let nuevoProveedor = false;
    let idSolicitud = null;
    const listaDetalle = document.getElementById('lista-detalles-bultos');
    listaDetalle.children[0].hidden = true;
    let datatableSoli = $('#tabla-solicitud').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
            data : function(d){
                d.estadoSol = $('#estado_soli').val();
                d.fechaInicio = $('#filtro_solicitud_inicio').val();
                d.fechaFin = $('#filtro_solicitud_fin').val();
            }
        },
        columns: [
            {data: 'nro_soli'},
            {data: 'nro_recojo'},
            {data: 'fecha_soli', name: 'fecha_soli'},
            {data: 'dia_recojo', name: 'dia_recojo'},
            {data: 'hr_estimado', name: 'hr_estimado'},
            {data: 'nro_doc', name : 'nro_doc'},
            {data: 'nom_cli', name: 'nom_cli'},
            { data: 'total_bulto', name: 'total_bulto'},
            {data: 'nomb_prov', name: 'nomb_prov'},
            {data: 'tipo', name: 'tipo'},
            {data: 'usuario_responsable', searchable: false, render: function(data,type,full){
                let tempUsu = `<option></option>`;
                for (let i = 0; i < full.listUsu.length; i++) {
                    tempUsu += `<option ${full.listUsu[i].status != 1 ? 'disabled' : ''} value="${full.listUsu[i].id}" ${data == full.listUsu[i].id ? 'selected' : ''}>${full.listUsu[i].name} ${full.listUsu[i].last_name}</option>`;
                }
                return `<select data-solicitud="${full.id}" class="form-control form-control-sm cb-usuarios">${tempUsu}</select>`;
            }},
            {data: 'estado',searchable: false, orderable: false, name: 'estado', render: function(data,type,full){
                let template = ``;
                full.listSoli.forEach(sol => {
                    template += `<option value="${sol.id}" ${data == sol.id ? 'selected' : ''}>${sol.estado}</option>`;
                });
                return `<select data-solicitud="${full.id}" class="form-control form-control-sm cb-estado-change">${template}</select>`;
            }},
            { data: 'id', searchable: false, orderable: false, name: 'id', render: function (data, type, full) { 
                const btnEditar = `<button data-toggle="tooltip" title="Editar solicitud" data-solicitud="${data}" class="btn btn-sm btn-primary editar-solicitud"><i class="fas fa-pencil-alt"></i></button>`;
                const btnEliminar = full.nro_recojo == 'Por definir' ? `<button data-toggle="tooltip" title="Eliminar cliente" data-solicitud="${data}" class="btn btn-sm btn-danger delete-solicitud"><i class="fas fa-trash-alt"></i></button>`: "";
                return `<div class="d-flex justify-content-center flex-wrap" style="gap:5px;">${btnEditar}${btnEliminar}</div>`
            }}
        ],
    });
    const $btnAsignacionMasiva = document.getElementById('btn-asignacion-masiva');
    document.getElementById('filtro-ver-solicitudes').onsubmit = function (e) {  
        e.preventDefault();
        datatableSoli.draw();
    }
    datatableSoli.on('draw.dt', function () {
        $('.cb-usuarios').select2({
            theme : 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione una opción'
        }).on('select2:select',async function(event){
            let datos = new FormData();
            datos.append('id_solicitud',event.target.dataset.solicitud);
            datos.append('id_user',$(this).val());
            let response  = await general.funcfetch('asignar-personal',datos);
            if(response.error){
                alertify.alert('Mensaje',response.error);
                return
            }
            document.getElementById('total-por-procesar').textContent = response.procesar;
            $btnAsignacionMasiva.dataset.info = response.porAsignar;
            if (response.porAsignar == 0) {
                $btnAsignacionMasiva.classList.remove('badge-notification');
            }
            alertify.success(response.success);
        });
        $('.cb-estado-change').select2({
            theme : 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione una opción'
        }).on('select2:select',async function(e){
            let datos = new FormData();
            datos.append('id_solicitud',this.dataset.solicitud);
            datos.append('estado',this.value);
            let response = await general.funcfetch('cambiar-estado',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return 
            }
            alertify.success(response.success);
        });
    });
    datatableSoli.on('responsive-display', function ( e, datatable, row, showHide, update ) {
        const tabla = e.target.children[0].children[0];
        if(tabla.children[10].style.display == 'none'){
            $('.cb-usuarios').off('change');
            $('.cb-usuarios').attr('data-responsive','true');
            $('.cb-usuarios').on('change',async function(event){
                let datos = new FormData();
                datos.append('id_solicitud',event.target.dataset.solicitud);
                datos.append('id_user',$(this).val());
                let response  = await general.funcfetch('asignar-personal',datos);
                if(response.error){
                    alertify.alert('Mensaje',response.error);
                    return
                }
                document.getElementById('total-por-procesar').textContent = response.procesar;
                alertify.success(response.success);
            });
        }
        if(tabla.children[11].style.display == 'none'){
            $('.cb-estado-change').off('change');
            $('.cb-estado-change').attr('data-responsive','true');
            $('.cb-estado-change').on('change',async function(e){
                let datos = new FormData();
                datos.append('id_solicitud',this.dataset.solicitud);
                datos.append('estado',this.value);
                let response = await general.funcfetch('cambiar-estado',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return 
                }
                alertify.success(response.success);
            });
        }
    });
    datatableSoli.on('responsive-resize', function ( e, datatable, columns ) {
        if(columns[10] && $('.cb-usuarios').attr('data-responsive') == 'true'){
            $('.cb-usuarios').off('change');
            $('.cb-usuarios').attr('data-responsive','false');
        }
        if(columns[11] && $('.cb-estado-change').attr('data-responsive') == 'true'){
            $('.cb-estado-change').off('change');
            $('.cb-estado-change').attr('data-responsive','false');
        }
    });
    $('#modal-asignar-masivo-solicitud').on('hide.bs.modal', function (e) {
        document.getElementById('asignar-masivo-solicitud').hidden = false;
        document.getElementById('seleccion-todo-asignar').checked = false;
    });
    document.getElementById('seleccion-todo-asignar').onchange = function (e) {
        for (const checkbox of document.querySelectorAll('#llenar-solicitud-por-asignar input[type="checkbox"]')) {
            checkbox.checked = e.target.checked;
        }
    }
    document.getElementById('asignar-masivo-solicitud').onclick = e => {
        let solicitudAsignar = [];
        const seleccionSolicitud = document.querySelectorAll(`#llenar-solicitud-por-asignar input[type="checkbox"]:checked`);
        for (const checkbox of seleccionSolicitud) {
            solicitudAsignar.push({
                id: checkbox.value
            });
        }
        if (!solicitudAsignar.length) {
            return alertify.error('Por favor selecione una o más solicitudes para ser asignadas');
        }
        alertify.confirm('Mensaje', '¿Estas seguro de asignar ' + solicitudAsignar.length + ' solicitudes al usuario ' + $('#id_asignacion_user option:selected').text() + '?', async () => {
            let datos = new FormData();
            datos.append('id_usuario', $('#id_asignacion_user').val());
            datos.append('solicitudes', JSON.stringify(solicitudAsignar));
            datos.append('accion','asignar-solicitud');
            const response = await general.funcfetch('asignar-global', datos);
            if (response.success) {
                alertify.success(response.success);
                datatableSoli.draw();
                document.getElementById('total-por-procesar').textContent = response.porAsignar;
                $btnAsignacionMasiva.dataset.info = response.porAsignar;
                if (response.porAsignar == 0){
                    $btnAsignacionMasiva.classList.remove('badge-notification');
                }
                $('#modal-asignar-masivo-solicitud').modal('hide');
            }
        }, () => { })
    }
    $btnAsignacionMasiva.onclick = async function (e) {
        e.preventDefault();
        let datos = new FormData();
        datos.append('accion','mostrar-datos');
        const response = await general.funcfetch('asignar-global', datos);
        if (response.solicitudes) {
            let template = "";
            response.solicitudes.forEach(g => {
                template += `<tr>
                <td class="text-center">
                    <input type="checkbox" value="${g.id}">
                </td>
                <td>
                    ${g.nro_solicitud}
                </td>
                <td>
                    ${g.fecha_solicitud}
                </td>
                </tr>`
            });
            if (!response.solicitudes.length) {
                document.getElementById('asignar-masivo-solicitud').hidden = true;
            }
            document.getElementById('llenar-solicitud-por-asignar').innerHTML = !template ? `<tr><td colspan="3" class="text-center">No se encontraron solicitudes pendientes para ser asignadas</td></tr>` : template;
            $('#modal-asignar-masivo-solicitud').modal('show');
        }
    }
    let cbProveedores = document.getElementById('solicitud_modificar_id_soli');
    let cbInvitados = document.getElementById('solicitud_modificar_id_invitado');
    let tablaSoli = document.getElementById('tabla-solicitudes');
    let cbCentroCostos = document.getElementById('solicitud_modificar_centro_costo_invitado');
    let cbCuentaInvitado = document.getElementById('solicitud_modificar_cuenta_invitado');
    tablaSoli.addEventListener('click',async function(e){
        if(e.target.classList.contains('editar-solicitud')){
            let datos = new FormData();
            datos.append('id_solicitud',e.target.dataset.solicitud);
            let response = await general.funcfetch('ver-detalle',datos);
            if(response.error){
                return alertify.alert('Error',response.error);
            }else if(response.info){
                return alertify.alert('Alerta',response.info);
            }
            idSolicitud = e.target.dataset.solicitud;
            let template = "<option></option>";
            if (response.success.lista_proveedores.length){
                response.success.lista_proveedores.forEach(prov => {
                    template += `<option value="${prov.id}">${prov.nomb_prov}</option>`;
                });
            }
            cbProveedores.innerHTML = template;
            template = "<option></option>";
            if (response.success.lista_invitados) {
                response.success.lista_invitados.forEach(inv => {
                    template += `<option value="${inv.id}">${inv.name + ' ' + inv.last_name}</option>`;
                });
            }
            cbInvitados.innerHTML = template;
            general.transforData(response.success.lista_provincia_recojo, document.getElementById('solicitud_modificar_ciudad_cli_recojo'));
            general.transforData(response.success.lista_distrito_recojo, document.getElementById('solicitud_modificar_distrito_cli_recojo'));
            template = "<option></option>";
            if (response.success.lista_invitado_cc && response.success.lista_invitado_cc.cc) {
                response.success.lista_invitado_cc.cc.forEach(c => {
                    template += `<option value="${c}">${c}</option>`;
                });
            }
            cbCentroCostos.innerHTML = template;
            template = "<option></option>";
            if (response.success.lista_invitado_cc && response.success.lista_invitado_cc.cuenta) {
                response.success.lista_invitado_cc.cuenta.forEach(c => {
                    template += `<option value="${c}">${c}</option>`;
                });
            }
            cbCuentaInvitado.innerHTML = template;
            $('#modal-solicitud-actualizar').modal();
            for (const key in response.success) {
                if (Object.hasOwnProperty.call(response.success, key)) {
                    const valor = response.success[key];
                    const dom = document.getElementById('solicitud_modificar_' + key);
                    if(key === 'nro_solicitud'){
                        dom.value = valor.toString().padStart(5,'0');
                        continue;
                    }
                    if (key === 'lista_detalle_envio'){
                        limpiarDetalle();
                        valor.forEach(d => {
                            const siguienteLugar = addDetalleSolicitud(d.departamento, d.provincia, d.lista_provincias, d.distrito, d.lista_distritos, d.sobres, d.cajas, d.paquetes, d.otros, d.detalle_otros,d.id);
                            listaDetalle.append(siguienteLugar);
                            general.selectUbigeoCli(siguienteLugar);
                            calcularBultos(siguienteLugar.querySelectorAll('.calcular-bultos'));
                            $(siguienteLugar).find('.cb-ubigeo').select2({
                                theme: 'bootstrap',
                                width: '100%',
                                placeholder: 'Seleccione una opción'
                            });
                        });
                        
                        continue;
                    }
                    if(!dom || valor === null){
                        continue;
                    }
                    dom.value = valor;
                }
            }
            if(response.success.estado > 1 && response.success.estado !== 6){
                $('.disabled-estatus-2').prop('disabled',true);
            }
            $('#modal-solicitud-actualizar .simple-select2').trigger("change");
            $('#modal-solicitud-actualizar #solicitud_modificar_depart_cli_recojo').trigger("change");
        }
        if(e.target.classList.contains('delete-solicitud')){
            alertify.confirm('Alerta','¿Deseas eliminar esta solicitud?',async ()=>{
                let datos = new FormData();
                datos.append('id_solicitud',e.target.dataset.solicitud);
                let response = await general.funcfetch('eliminar-solicitud',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.success(response.success);
                datatableSoli.draw();
            },()=>{});
        }
    });
    $('#modal-solicitud-actualizar').on('hidden.bs.modal',e => {
        $('.disabled-estatus-2').prop('disabled', false);
        idSolicitud = null;
    })
    for (const cb of document.querySelectorAll('.cb-filtros')) {
        cb.addEventListener('change',function(e){
            datatableSoli.draw();
        });
    }
    document.getElementById('actualiza-solicitud').onclick = e => document.getElementById('btn-actualizar-solicitud').click();
    function fnNotificar(event) {
        const nombres = event.target.dataset.name + ' ' + event.target.dataset.lastname;
        const celular = event.target.dataset.phone == 'null' ? null : event.target.dataset.phone;
        if (!celular) {
            return alertify.error('numero de celular no especificado');
        }
        const parent = this.parentElement.parentElement;
        const fecha = parent.querySelector("input[type='date']").value;
        const hr = parent.querySelector("input[type='time']").value;
        const solicitud = parent.querySelector("input[type='checkbox']");
        let texto = "Estimado Sr(a). " + nombres + " le informamos que el recojo de su solicitud *No. - " + solicitud.value.padStart(5, '0') + "* será a partir del día *" + general.formaterFecha(fecha, '/') + "* a las *" + general.formaterHora(hr) + "* aproximadamente.";
        let enlace = document.createElement("a");
        enlace.href = "https://wa.me/" + celular + "?text=" + encodeURIComponent(texto);
        enlace.target = "_blank";
        document.body.append(enlace);
        solicitud.checked = true;
        enlace.click();
        enlace.remove();
    }
    $(cbProveedores).select2({
        theme: 'bootstrap',
        width: '100%',
        tags: true,
        placeholder: 'Seleccione un proveedor'
    });
    function retornarEnetroDetalle(valor) {  
        return valor == '' || valor == null ? 0 : valor;
    }
    document.getElementById('formulario-editar-solicitud').onsubmit = async function(e){
        e.preventDefault();
        let datos = new FormData(this);
        datos.append('id_solicitud', idSolicitud);
        datos.append('nuevo_proveedor',nuevoProveedor);
        let detalleNuevos = [];
        let detalleUpdate = [];
        for (const li of listaDetalle.children) {
            if(!li.dataset.db){
                continue;
            }
            const datosJson = {
                departamento: $(li.querySelector('[data-name="depar_cli"]')).val(),
                provincia: $(li.querySelector('[data-name="ciudad_cli"]')).val(),
                distrito: $(li.querySelector('[data-name="distrito_cli"]')).val(),
                sobres: retornarEnetroDetalle(li.querySelector('[data-name="sobres"]').value),
                cajas: retornarEnetroDetalle(li.querySelector('[data-name="cajas"]').value),
                paquetes: retornarEnetroDetalle(li.querySelector('[data-name="paquetes"]').value),
                otros: retornarEnetroDetalle(li.querySelector('[data-name="otros"]').value),
                detalle_otros: li.querySelector('[data-name="detalle_otros"]').value
            }
            if(li.dataset.db == 'old'){
                datosJson.id = li.dataset.detalle;
                detalleUpdate.push(datosJson);
            }else{
                datosJson.id_solicitud = idSolicitud;
                detalleNuevos.push(datosJson);
            }
        }
        if (detalleNuevos.length){
            datos.append('agregarDetalles', JSON.stringify(detalleNuevos));
        }
        if (detalleUpdate.length) {
            datos.append('editarDetalles', JSON.stringify(detalleUpdate));
        }
        try {
            const response = await general.funcfetch('editar',datos);
            if(response.error){
                return alertify.alert('Error',response.error);
            }
            alertify.success(response.success);
            $('#modal-solicitud-actualizar').modal('hide');
        } catch (error) {
            console.error(error);
            alertify.error('no se pudo actualizar la solicitud');
        }
    }
    document.getElementById('seleccion-todo-notificar').onchange = function(){
        for (const notificar of document.querySelectorAll('.check-notificar')) {
            notificar.checked = this.checked;
        }
    }
    for (const notificar of document.querySelectorAll('.notificar-whatsapp')) {
        notificar.addEventListener('click', fnNotificar);
    }
    document.getElementById('guardar-notificaciones').onclick = async function(){
        let modificar = [];
        for (const notificar of document.querySelectorAll('.check-notificar:checked')) {
            modificar.push({
                solicitud : notificar.value
            });
        }
        if (!modificar.length){
            return alertify.error('no se seleccionaron solicitudes para modificar el estado de notificacion');
        }
        try {
            let datos = new FormData();
            datos.append('solicitudes',JSON.stringify(modificar));
            let response = await general.funcfetch("actualizar/notificacion", datos);
            alertify.success(response.success);
            if(response.pendientes === 0){
                document.getElementById('btn-notificar-whatsapp').classList.remove('badge-notification');
            }
            document.getElementById('btn-notificar-whatsapp').dataset.info = response.pendientes;
            deleteNotificationTable(modificar);
            $('#modal-notificar-whatsapp').modal("hide");
        } catch (error) {
            alertify.error('error al actualizar las notificaciones');
            console.error(error);
        }
    }
    function deleteNotificationTable(arraySolitud){
        arraySolitud.forEach(soli => {
            document.querySelector("#modal-notificar-whatsapp #solicitud-notificacion-" + soli.solicitud).remove();
        })
        if (!document.getElementById('llenar-solicitud').children.length){
            document.getElementById('llenar-solicitud').innerHTML = `<tr><td class="text-center" colspan="5">No se encontraron resultados</td></tr>`
        }
    }
    
    $('#modal-notificar-whatsapp').on('hidden.bs.modal', function (e) {
        document.getElementById('seleccion-todo-notificar').checked = false;
        if (!document.getElementById('llenar-solicitud').children.length) {
            return
        }
        for (const notificar of document.querySelectorAll('.check-notificar')) {
            notificar.checked = false;
        }
    });
    let contadorListaDetalle = 1;
    function limpiarDetalle() {  
        [...listaDetalle.children].forEach((v,k) => {
            if(k > 0){
                v.remove();
            }
        });
    }
    function addDetalleSolicitud(valDepar,valProv,listaProv,valDist,listaDist,valSobre,valCaja,valPaquete,valOtro,valDetaOtro,idDeta){
        const primerLugar = listaDetalle.children[0];
        const siguienteLugar = primerLugar.cloneNode(true);
        const listaCambio = siguienteLugar.querySelectorAll('label, select, input, textarea');
        if (siguienteLugar.dataset.detalle){
            siguienteLugar.removeAttribute('data-detalle');
        }
        if (siguienteLugar.hidden){
            siguienteLugar.hidden = false;
        }
        if(idDeta !== null){
            siguienteLugar.dataset.db = 'old';
            siguienteLugar.dataset.detalle = idDeta;
        }else{
            siguienteLugar.dataset.db = 'new';
            contadorListaDetalle++;
        }
        for (const element of listaCambio) {
            const replace = idDeta === null ? element.dataset.replace + '_' + contadorListaDetalle : 'consulta_' + element.dataset.replace + '_' + idDeta;
            if (element.localName == 'label') {
                element.htmlFor = replace;
            }
            if (element.localName == 'select') {
                element.disabled = false;
                element.id = replace;
                if (element.dataset.consulta == 'provincia') {
                    element.value = valDepar;
                    element.dataset.info = idDeta === null ? 'id_ciudad_cli_' + contadorListaDetalle : 'consulta_id_ciudad_cli_' + idDeta;
                    element.dataset.infoDistrito = idDeta === null ? 'id_distrito_cli_' + contadorListaDetalle : 'consulta_id_distrito_cli_' + idDeta;
                } else if (element.dataset.consulta == 'distrito') {
                    let templateProv = "";
                    if (listaProv){
                        templateProv = "<option></option>";
                        listaProv.forEach(e => {
                            templateProv += `<option value="${e.id_ubigeo}">${e.nombre_ubigeo}</option>`;
                        });
                    }
                    element.innerHTML = templateProv;
                    element.value = valProv;
                    element.dataset.info = idDeta === null ? 'id_distrito_cli_' + contadorListaDetalle : 'consulta_id_distrito_cli_' + idDeta;
                } else if(element.dataset.consulta == 'distrito2'){
                    let templateDist = "";
                    if (listaDist) {
                        templateDist = "<option></option>";
                        listaDist.forEach(e => {
                            templateDist += `<option value="${e.id_ubigeo}">${e.nombre_ubigeo}</option>`;
                        });
                    }
                    element.innerHTML = templateDist;
                    element.value = valDist;
                }
            }
            if (element.localName == 'input' || element.localName == 'textarea') {
                element.disabled = false;
                switch (element.dataset.consulta) {
                    case 'sobre':
                        element.value = valSobre;
                        break;
                    case 'caja':
                        element.value = valCaja;
                        break;
                    case 'paquete':
                        element.value = valPaquete;
                        break;
                    case 'otro':
                        element.value = valOtro;
                        break;
                    case 'detalle':
                        element.value = valDetaOtro;
                        break;
                }
                element.id = replace;
            }
        }
        return siguienteLugar;
    }
    listaDetalle.addEventListener('click',function(e){
        if (e.target.classList.contains('delete-detalle-solicitud')){
            const li = e.target.parentElement.parentElement.parentElement;
            if(this.children.length === 2){
                return alertify.error('la solicitud debe tener como minino 1 detalle');
            }
            if(li.dataset.db == 'old'){
                alertify.confirm('Eliminar','¿Estás seguro de eliminar este detalle de la solicitud?',async ()=>{
                    let datos = new FormData();
                    datos.append('idDetalle',li.dataset.detalle);
                    const response = await general.funcfetch('eliminar-detalle',datos);
                    if(response.error){
                        return alertify.error(response.error);
                    }
                    li.remove();
                    alertify.success(response.success);
                    document.getElementById('solicitud_modificar_total_bulto').value = general.totalBultos(listaDetalle, '.calcular-bultos');
                },()=>{});
            }else{
                li.remove();
                alertify.success('el detalle de la solicitud se eliminó correctamente');
                document.getElementById('solicitud_modificar_total_bulto').value = general.totalBultos(listaDetalle, '.calcular-bultos');
            }
        }
    });
    $('#solicitud_modificar_responsable').on('change.select2',function(e){
        general.setAtributos('.datos-proveedor, .datos-invitado', 'required', false);
        switch (this.value) {
            case 'PROVEEDOR':
                general.setAtributos('.datos-proveedor', 'required', true);
                break;
            case 'INVITADO':
                general.setAtributos('.datos-invitado', 'required', true);
                break;
        }
    });
    document.getElementById('agregar-datos-recojo').addEventListener('click',function(e){
        e.preventDefault();
        const siguienteLugar = addDetalleSolicitud("","",null,"",null,"","","","","",null);
        listaDetalle.append(siguienteLugar);
        general.selectUbigeoCli(siguienteLugar);
        calcularBultos(siguienteLugar.querySelectorAll('.calcular-bultos'));
        $(siguienteLugar).find('.cb-ubigeo').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        });
    });
    let cbProvincias = document.getElementById('solicitud_modificar_ciudad_cli_recojo');
    let cbDistritos = document.getElementById('solicitud_modificar_distrito_cli_recojo');
    $('#solicitud_modificar_id_cliente').on('select2:select', async function (event) {
        let dato = new FormData();
        dato.append('id', $(this).val());
        try {
            let response = await general.funcfetch(window.location.origin + '/intranet/solicitud/obtener-cliente', dato);
            if (response.error) {
                alertify.error('Error', response.error);
                return
            }
            limpiarFrm();
            let template = ``;
            response.provincias.forEach(prov => {
                template += `<option value="${prov.id_ubigeo}">${prov.nombre_ubigeo}</option>`;
            });
            cbProvincias.innerHTML = template;
            template = '';
            response.distritos.forEach(dist => {
                template += `<option value="${dist.id_ubigeo}">${dist.nombre_ubigeo}</option>`;
            });
            cbDistritos.innerHTML = template;
            template = `<option value=""><option>`;
            if (response.proveedores.length) {
                $('.limitar-proveedor').prop('required', true);
                response.proveedores.forEach(prov => {
                    template += `<option value="${prov.id}">${prov.nomb_prov}</option>`;
                });
            } else {
                $('.limitar-proveedor').prop('required', false);
            }
            cbProveedores.innerHTML = template;
            template = '';
            if (response.invitados.length) {
                template += "<option></option>";
                response.invitados.forEach(inv => {
                    template += `<option value="${inv.id}">${inv.name + ' ' + inv.last_name}</option>`;
                });
            }
            cbInvitados.innerHTML = template;
            cbCentroCostos.innerHTML = "<option></option>";
            cbCuentaInvitado.innerHTML = "<option></option>";
            template = '';
            for (const key in response.cliente) {
                if (Object.hasOwnProperty.call(response.cliente, key)) {
                    const element = response.cliente[key];
                    const domelement = document.querySelector(`[data-cliente="${key}"]`);
                    if (!domelement) {
                        continue;
                    }
                    domelement.value = element;
                }
            }
            $('#solicitud_modificar_depart_cli_recojo').trigger('change');
            $('.cb-tipo-doc').trigger('change');
            general.validarMinMaxTipoDocumento($('#solicitud_modificar_doc_iden_cli').data('input'), $('#solicitud_modificar_doc_iden_cli').val());
        } catch (error) {
            console.error(error);
            alertify.alert('Error', error.message);
        }
    });
    $('#solicitud_modificar_id_soli').on('select2:select', async function (event) {
        let datos = new FormData();
        datos.append('id_proveedor', $(this).val());
        try {
            let response = await general.funcfetch(window.location.origin + '/intranet/solicitud/ver-proveedor', datos);
            if (response.error) {
                alertify.alert('Error', response.error);
                return
            }
            if (!response.proveedor) {
                nuevoProveedor = true;
                document.getElementById('solicitud_modificar_doc_iden_soli').value = "";
                document.getElementById('solicitud_modificar_nro_doc_soli').value = "";
                return
            }
            nuevoProveedor = false;
            document.getElementById('solicitud_modificar_doc_iden_soli').value = response.proveedor.tipo_docu_prov;
            document.getElementById('solicitud_modificar_nro_doc_soli').value = response.proveedor.nro_docu_prov;
            alertify.confirm('Atención', '¿Deseas sustituir la informacion de recojo?', () => {
                for (const key in response.proveedor) {
                    if (Object.hasOwnProperty.call(response.proveedor, key)) {
                        const value = response.proveedor[key];
                        const nodo = document.querySelector(`[data-proveedor='${key}']`);
                        if (!nodo) {
                            continue;
                        }
                        switch (key) {
                            case 'prov_prov':
                                let template = `<option value=""></option>`;
                                response.proveedor.listProv.forEach(prov => {
                                    template += `<option value="${prov.id_ubigeo}">${prov.nombre_ubigeo}</option>`;
                                });
                                nodo.innerHTML = template;
                                nodo.value = value;
                                break;
                            case 'dist_prov':
                                let template2 = `<option value=""></option>`;
                                response.proveedor.listDist.forEach(prov => {
                                    template2 += `<option value="${prov.id_ubigeo}">${prov.nombre_ubigeo}</option>`;
                                });
                                nodo.innerHTML = template2;
                                nodo.value = value;
                                break;
                            default:
                                nodo.value = value;
                                break;
                        }
                    }
                }
                $('#solicitud_modificar_depart_cli_recojo').trigger('change');
                general.validarMinMaxTipoDocumento($('#solicitud_modificar_doc_iden_soli').data('input'), $('#solicitud_modificar_doc_iden_soli').val());
            }, () => { });
            $('.cb-tipo-doc').trigger('change');
        } catch (error) {
            alertify.alert('Error', error.message);
        }
    });
    function limpiarFrm() {
        for (const element of document.querySelectorAll('.text-clear')) {
            element.value = "";
        }
    }
    function calcularBultos($bultos) {
        for (const calculo of $bultos) {
            calculo.addEventListener('change', function (e) {
                document.getElementById('solicitud_modificar_total_bulto').value = general.totalBultos(listaDetalle, '.calcular-bultos');
            });
        }
    }
    $(cbInvitados).on('select2:select', async function (e) {
        let dato = new FormData();
        dato.append('usuario', $(this).val());
        dato.append('cliente', $('#solicitud_modificar_id_cliente').val());
        try {
            let response = await general.funcfetch(window.location.origin + '/intranet/solicitud/obtener-cliente-cc', dato);
            let template = '<option></option>';
            response.centrosCostos.cc.forEach(c => {
                template += `<option value="${c}">${c}</option>`;
            });
            cbCentroCostos.innerHTML = template;
            template = "<option></option>";
            response.centrosCostos.cuenta.forEach(c => {
                template += `<option value="${c}">${c}</option>`;
            });
            cbCuentaInvitado.innerHTML = template;
            template = '';
        } catch (error) {
            alertify.error('error al obtener los cc del invitado');
            console.error(error);
        }
    });

    
}
window.addEventListener('DOMContentLoaded',loadPage);