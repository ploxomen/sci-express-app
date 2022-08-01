function loadPage(){
    let tablaGuias = $('#tabla-guias').DataTable({
        ajax : {
            url: 'remitentes',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            data : function(d){
                d.tipo = 'remitentes';
                d.remitente = $('#rucdniremi').val(); 
                d.fecha = $('#fechadepacho').val();
            }
        },
        columns: [
            {data: 'id', searchable: false, orderable : false},
            {data: 'comp_clie', searchable: false, orderable : false,render:function(data){
                if(data){
                    let comprobantes = data.split(';').join(`</span><span class='d-block'>`);
                    return `<span class="d-block">` + comprobantes + '</span>';
                }
                return 'NINGUNO';
            }},
            {data: 'nro_comp_cli', searchable: false, orderable : false, render : function(data,){
                let template = '';
                for (const keyc in data) {
                    if (Object.hasOwnProperty.call(data, keyc)) {
                        const element = data[keyc];
                        if(element.length > 0){
                            template += `<span class='d-block'>` + element.join(`</span><span class="d-bock">`) + "</span>";
                        }
                    }
                } 
                return template;
            }},
            {data: 'nro_guia'},
            {data: 'id',searchable: false, orderable : false ,render : function(data,type,row){
                return `<button data-guia="${row.nro_guia}" class='btn-view-detalles btn btn-primary btn-sm' title="Ver detalle"><i class="fas fa-eye"></i></button>`;
            }}
        ],
        createdRow : function(row,data){
            if($('#nameremitente').val() == "" || $('#nameremitente').val() == null){
                $('#nameremitente').val(data.nombre_remitente);
            }
        }
    });
    tablaGuias.on('draw.dt', function () {
        let info = tablaGuias.page.info();
        if(info.pages === 0 && $('#rucdniremi').val() != "" && $('#fechadepacho').val() != null)
        {
            alertify.alert('ALERTA','NO SE ENCONTRARON RESULTADOS, FAVOR VERIFICAR LA INFORMACION CON SU ALMACEN');
            return false;
        }
        console.log(info);
        tablaGuias.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });
    document.getElementById('from-consulta-remitentes').addEventListener('submit',function(e){
        e.preventDefault();
        $('#nameremitente').val("");
        tablaGuias.draw();
    });
    document.getElementById('tabla-body-guias').addEventListener('click',function(e){
        if(e.target.classList.contains('btn-view-detalles')){
            for (const textos of document.querySelectorAll('.text-proceso')) {
                textos.textContent = '';
            }
            const datos = new FormData();
            datos.append('tipoguia','guia');
            datos.append('nro_guia_sci',e.target.dataset.guia);
            datos.append('nro_guia_remitente','0');
            datos.append('selectguia','0');
            datos.append('documento_remitente','0');
            let request = new XMLHttpRequest();
            request.onreadystatechange = function(){
                if(request.readyState == 4 && request.status == 200){
                    if(!request.response.result){
                        alertify.alert('ALERTA','NO SE ENCONTRARON RESULTADOS, FAVOR VERIFICAR LA INFORMACION CON SU ALMACEN');
                        return false;
                    }
                    for (const key in request.response.result) {
                        if (Object.hasOwnProperty.call(request.response.result, key)) {
                            const valor = request.response.result[key];
                            const dom = document.getElementById('info_'+key);
                            if(key == 'seguimiento'){
                                for (let i = 0; i < valor.length; i++) {
                                    const domse = document.getElementById('info_fecha_rastreo_' + valor[i].id);
                                    if(!domse){
                                        continue;
                                    }
                                    domse.textContent = valor[i].fecha_cambio;
                                }
                            }
                            if(!dom){
                                continue;
                            }
                            dom.textContent = valor;
                        }
                    }
                    $('#modal-detalle-consultas').modal("show");
                }
            }
            request.open('POST','datos');
            request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            request.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            request.responseType = 'json';
            request.send(datos);
        }
    })
}
window.addEventListener('DOMContentLoaded',loadPage);