function loadPage() {
    let general = new General();
    const tbodyEmpresa = document.getElementById('lista-empresas');
    let dataTableEmpresas = $('#tabla-empresas').DataTable({
        ajax : {
            url: 'obtener',
            method: 'POST',
            headers: general.jsonToken,
        },
        columns: [
            {data: null,searchable: false, orderable: false,},
            {data: 'nro_ruc'},
            {data: 'empresa'},
            {data: 'nro_contacto',render : function(data){
                return data ? data.replace(/;/gi,'/') : 'No definido';
            }},
            {data: 'ciudades_atencion',render : function(data){
                return data ? data.replace(/;/gi,'/') : 'No definido';
            }},
            {data: 'direccion'},
            {data: 'id', searchable: false, orderable: false, name: 'id', 
            render: function(data){
                return`<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button class="btn btn-sm btn-primary update-empresa" data-toggle="tooltip" title="Editar cluster" data-empresa="${data}"><i class="fas fa-user-edit"></i></button><button data-toggle="tooltip" title="Eliminar cluster" data-empresa="${data}" class="btn btn-sm btn-danger delete-empresa"><i class="fas fa-user-times"></i></button></div>`
            }}
        ],
    });
    dataTableEmpresas.on('draw.dt', function () {
        let info = dataTableEmpresas.page.info();
        dataTableEmpresas.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
    tbodyEmpresa.addEventListener('click',async function(e){
        if(e.target.classList.contains('update-empresa')){
            let datos = new FormData();
            datos.append('empresa_id',e.target.dataset.empresa);
            let response = await general.funcfetch('obtener',datos);
            if(response.error){
                alertify.alert('Error',response.error);
                return
            }
            document.getElementById('empresa_id').value = response.empresas.id;
            for (const key in response.empresas) {
                if (Object.hasOwnProperty.call(response.empresas, key)) {
                    const valor = response.empresas[key];
                    const dom = document.getElementById('id_' + key);
                    if(!dom){
                        continue;
                    }
                    if((key == 'nro_contacto' || key == 'correos') && valor != null){
                        const opciones = valor.split(";");
                        let template = ``;
                        opciones.forEach(o => {
                            template+=`<option value="${o}">${o}</option>`;
                        });
                        $(dom).html(template);
                        $(dom).val(opciones).trigger('change');
                        continue;
                    }
                    dom.value = valor;
                }
            }
            $('#modal-nueva-empresa').modal('show');
        }
        if(e.target.classList.contains('delete-empresa')){
            alertify.confirm('Mensaje','¿Deseas eliminar esta empresa de transporte?',async ()=>{
                let datos = new FormData();
                datos.append('empresa_id',e.target.dataset.empresa);
                let response = await general.funcfetch('eliminar',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.success(response.success);
                dataTableEmpresas.draw();
            },()=>{})
            
        }
    });
    document.getElementById('btn-save').addEventListener('click',function(e){
        document.getElementById('btn-send-frm').click();
    });
    document.getElementById('frm_transporte').addEventListener('submit',async function(e){
        e.preventDefault();
        const id = document.getElementById('empresa_id');
        let ruta = 'nuevo';
        let datos = new FormData(this);
        console.log(id.value);  
        if(id.value != ''){
            datos.append('id_empresa',id.value);
            ruta = 'actualizar';
        }
        let response = await general.funcfetch(ruta,datos);
        if(response.error){
            alertify.alert('Error',response.error);
            return
        }
        alertify.success(ruta == 'nuevo' ? 'Empresa de transporte agregado correctamente' : 'Empresa de transporte actualizado correctamente');
        dataTableEmpresas.draw();
        $('#modal-nueva-empresa').modal('hide');
    });
    $('#modal-nueva-empresa').on('hidden.bs.modal', function (e) {
        document.getElementById('empresa_id').value = "";
        document.getElementById('frm_transporte').reset();
        $('.cb-mult-nuevo').val("").trigger("change");
    });
}
window.addEventListener('DOMContentLoaded',loadPage);