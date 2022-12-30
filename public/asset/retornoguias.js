function loadPage(){
    let general = new General();
    const renderListas = function(data){
        let template = `<div>`;
        if(data){
            const dataCount = data.split(';');
            if(dataCount.length === 1){
                return `<span class="text-info d-block">${data}</span>`;
            }
            data.split(';').map(function(v,k){
                template += `<span class="text-info d-block">${v}</span>`
            });
        }else{
            template += `<span class="text-danger">Por definir</span>`;
        }
        template += `</div>`;
        return template;
    }
    let columnsTable = [
        {data : 'nro_embarque'},
        {data : 'fecha_embarque'},
        {data : 'empresa'},
        {data : 'fecha_hr_llegada'}
    ];
    columnsTable.push(
        {data: 'importe_embarque'},
        {data : 'tipo_comprobantes', render : renderListas},
        {data : 'nro_guias', render : renderListas},
        {data : 'lista_guias',render: renderListas}
    )
    columnsTable.push({data : 'total_bultos'},
        {data : 'id_embarque', render: function(ndata,type,row){
            if(ndata != 0){
                return `<button data-nroembarque="${row.nro_embarque}" data-cajas="${!row.nro_cajas ? '-' : row.nro_cajas}" data-cajones="${!row.nro_cajones  ? '-' : row.nro_cajones}" data-paquetes="${!row.nro_paquetes  ? '-' : row.nro_paquetes}" data-otros="${!row.num_otros  ? '-' : row.num_otros}" data-sobres="${!row.nro_sobres  ? '-' : row.nro_sobres}" data-detalle="${!row.detalle_otro  ? '-' : row.detalle_otro}" data-jabas="${!row.nro_jabas  ? '-' : row.nro_jabas}" class="btn btn-primary btn-sm btn-bultos" data-embarque="${ndata}" title="Ver detalle de bultos"><i class="fas fa-eye"></i></button>`;
            }else{
                return 'Sin acciones';
            }
        }})
    let tablaRetorno = $('#tabla-retorno').DataTable({
        ajax : {
            url: window.location.origin + '/intranet/comprobante/guias/retornadas',
            method: 'POST',
            headers: general.jsonToken,
            data: function(d){
                d.tipo = 'mostrar-guias';
            }
        },
        columns: columnsTable
    });
    document.getElementById('cuerpo-detalle-guias').addEventListener('click',function(e){
        if(e.target.classList.contains('btn-bultos')){
            document.getElementById('id_modal_nro_cajas').textContent = e.target.dataset.cajas;
            document.getElementById('id_modal_nro_jabas').textContent = e.target.dataset.jabas;
            document.getElementById('id_modal_nro_cajones').textContent = e.target.dataset.cajones;
            document.getElementById('id_modal_nro_sobres').textContent = e.target.dataset.sobres;
            document.getElementById('id_modal_nro_paquetes').textContent = e.target.dataset.paquetes;
            document.getElementById('id_modal_num_otros').textContent = e.target.dataset.otros;
            document.getElementById('id_modal_detalle_otro').textContent = e.target.dataset.detalle;
            document.getElementById('id_modal_nro_embarque').textContent = e.target.dataset.nroembarque;
            $('#detalle-embarque').modal('show');
        }
    });
    $('#detalle-embarque').on('hidden.bs.modal',function(e){
        $('.clear-text').text('-');
    });
    
}
window.addEventListener('DOMContentLoaded',loadPage);