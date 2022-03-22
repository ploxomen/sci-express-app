function loadPage(){
    let general = new General();
    let bodySoli = document.getElementById('tabla-cuerpo-soli');
    let frmSoli = document.getElementById('frm_recojo');
    let btnSave = document.getElementById('btn-change-estado');
    let btnCancel = document.getElementById('btn-cancelar');
    let datosGlobales = {
        id_recojo : null,
        id_solicitud : null,
        cluster: false
    };
    bodySoli.addEventListener('click',async function(event) {
        if(event.target.classList.contains('btn_editar')){
            verifTrayecto(['id_id_cluster','id_id_conductor','id_id_dealer'],true);
            frmSoli.reset();
            datosGlobales.id_recojo = null;
            datosGlobales.id_solicitud = null;
            datosGlobales.cluster = false;
            let datos = new FormData();
            datos.append('id_solicitud',event.target.dataset.idsolicitud);
            let response = await general.funcfetch('recojos/obtener',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            datosGlobales.id_solicitud = event.target.dataset.idsolicitud;
            if(!response.recojos){
                alertify.success('listo para programar el recojo');
                changeEstadoInput(false,'Agregar');
                $('.simple-select2').val('').trigger('change');
            }else{
                if(response.clusters){
                    let tempClu = `<option></option>`;
                    response.clusters.forEach(c => {
                        tempClu += `<option value="${c.id}">${c.nom}</option>`;
                    });
                    document.getElementById('id_id_cluster').innerHTML = tempClu;
                }
                for (const key in response.recojos) {
                    if (Object.hasOwnProperty.call(response.recojos, key)) {
                        const valor = response.recojos[key];
                        const dom = document.getElementById('id_' + key);
                        if(key == 'doc_recojo' && valor != null){
                            for (const e of document.querySelectorAll(`[name="doc_recojo"]`)) {
                                if(e.value == valor){
                                    e.checked = true;
                                }
                            }
                            continue;
                        }
                        if(!dom){
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                if(response.recojos.trayecto === 4 || response.recojos.trayecto === 2){
                    verifTrayecto(['id_id_cluster'],false);
                    datosGlobales.cluster = true;
                }else{
                    verifTrayecto(['id_id_conductor','id_id_dealer'],false);
                }
                changeEstadoInput(false,'Actualizar');
                if(response.recojos.estado_soli == 4){
                    datosGlobales.id_recojo = false;
                    btnSave.hidden = true;
                }else{
                    datosGlobales.id_recojo = response.recojos.id;
                    alertify.success('recojo listo para modificar');
                }
                $('.simple-select2').trigger('change');
            }
        }
    });
    function verifTrayecto(elements,disabled){
        elements.forEach(e => {
            document.getElementById(e).disabled = disabled;
            document.getElementById(e).parentElement.hidden = disabled;  
        });
    }
    let datatableSoli = $('#tabla-solicitud').DataTable({
        ajax : {
            url: './../solicitud/obtener?recojos=true',
            method: 'POST',
            headers: general.jsonToken,
            data : function(d){d.estadoSol = $('#id_estado_cli').val()}
        },
        columns: [
            {data: 'nro_soli'},
            {data: 'fecha_soli', name: 'fecha_soli'},
            {data: 'dia_recojo', name: 'dia_recojo'},
            {data: 'hr_estimado', name: 'hr_estimado'},
            {data: 'nomb_prov', name: 'nomb_prov'},
            {data: 'nro_doc', name: 'nro_doc'},
            {data: 'nom_cli', name: 'nom_cli'},
            {data: 'tipo', name: 'tipo'},
            {data: 'direccion_cli', name: 'direccion_cli'},
            {data: 'cant_bulto', name: 'cant_bulto'},
            {data: 'estado',searchable: false, orderable: false, name: 'estado', render: function(data,type,full){
                let template = ``;
                full.listSoli.forEach(sol => {
                    template += `<option value="${sol.id}" ${data == sol.id ? 'selected' : ''}>${sol.estado}</option>`;
                });
                return `<select data-solicitud="${full.id}" ${full.estado == 4 ? 'disabled' : ''} class="form-control form-control-sm ${full.estado < 4 ? 'cb-estado-change' : ''}">${template}</select>`;
            }},
            {data: 'id', searchable: false, orderable: false, name: 'id', render: function(data,type,full){return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button data-toggle="tooltip" title="Editar solicitud" data-idsolicitud="${data}" class="btn btn-sm btn-primary btn_editar"><i class="fas fa-edit"></i></button></div>`}}
        ],
        createdRow : function(row,data){
            let bgTable = data.id_user ? 'table-success' : 'table-warning';
            $(row).addClass(bgTable);
        }
    });
    
    function changeEstadoInput(active,tipo) {  
        btnSave.hidden = active;
        btnCancel.hidden = active;
        btnSave.textContent = tipo;
    }
    btnCancel.addEventListener('click',function(e){
        changeEstadoInput(true,'');
        $('.simple-select2').val('').trigger('change');
        datosGlobales.id_recojo = null;
        datosGlobales.id_solicitud = null;
    });
    frmSoli.addEventListener('submit',async function(e){
        e.preventDefault();
        if(datosGlobales.id_recojo === false){
            return
        }
        let url = datosGlobales.id_recojo !== null ? 'recojos/modificar' : 'recojos/agregar';
        let datos = new FormData(this);
        datos.append('id_solicitud',datosGlobales.id_solicitud);
        if(datosGlobales.id_recojo){
            datos.append('id_recojo',datosGlobales.id_recojo);
        }
        if(datosGlobales.cluster){
            datos.set('id_conductor',"");
            datos.set('id_dealer',"");
        }else{
            datos.set('id_cluster',"");
        }
        let response = await general.funcfetch(url,datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        changeEstadoInput(true,'');
        alertify.alert('Mensaje',response.success);
        datatableSoli.draw();
        this.reset();
        $('.simple-select2').val('').trigger('change');
    })
    document.getElementById('id_estado_cli').addEventListener('change',()=>{
        datatableSoli.draw();
    })
}

window.addEventListener('DOMContentLoaded',loadPage);