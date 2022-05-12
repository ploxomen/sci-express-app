function loadPage(){
    let general = new General();
    let cbClusters = document.getElementById('cb-clusters');
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
    if(cbClusters){
        columnsTable.push(
            {data: 'cluster'}
        );
    }
    columnsTable.push(
        {data : 'tipo_comprobantes', render : renderListas},
        {data : 'nro_guias', render : renderListas},
        {data : 'lista_guias',render: renderListas}
    )
    if(cbClusters){
        columnsTable.push(
            {data: 'estadoguia', render : function(data,type,row){
                if(row.id_embarque){
                    let template = `<select class="form-control form-control-sm cambio-estado" data-guia="${row.id_guia}" data-cluster="${row.id_cluster}">`;
                    row.listaEstados.forEach(l => {
                        if(l.id >= 9 && l.id <= 13){
                            template += `<option ${data == l.id ? 'selected' : ''} value="${l.id}">${l.tipo}</option>`;
                        }
                    });
                    template += `</select>`;
                    return template;
                }else{
                    return `<span class="text-danger">${data}</span>`;
                }
            }}
        )
    }
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
                if(cbClusters){
                    d.cluster = $('#cb-clusters').val();
                }
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
    if(cbClusters){
        $('#cb-clusters').on('select2:select',function(e){
            tablaRetorno.draw();
        });
        tablaRetorno.on('draw',function(){
            $('.cambio-estado').off('change');
            $('.cambio-estado').on('change',async function(e){
                let datos = new FormData();
                datos.append('cluster',this.dataset.cluster);
                datos.append('estado',this.value);
                datos.append('tipo','cambio-estado');
                datos.append('guia',this.dataset.guia);
                let response = await general.funcfetch(window.location.origin + '/intranet/comprobante/guias/retornadas',datos);
                if(response.error){
                    alertify.alert('Error',response.error);
                    return
                }
                alertify.success(response.success);
            })
        });
        tablaRetorno.on('responsive-display', function ( e, datatable, row, showHide, update ) {
            const tabla = e.target.children[0].children[0];
            if(tabla.children[8].style.display == 'none'){
                $('.cambio-estado').off('change');
                $('.cambio-estado').attr('data-responsive','true');
                $('.cambio-estado').on('change',async function(e){
                    let datos = new FormData();
                    datos.append('cluster',this.dataset.cluster);
                    datos.append('estado',this.value);
                    datos.append('tipo','cambio-estado');
                    datos.append('guia',this.dataset.guia);
                    let response = await general.funcfetch(window.location.origin + '/intranet/comprobante/guias/retornadas',datos);
                    if(response.error){
                        alertify.alert('Error',response.error);
                        return
                    }
                    alertify.success(response.success);
                })
            }
        });
        tablaGuias.on('responsive-resize', function ( e, datatable, columns ) {
            if(columns[8] && $('.cambio-estado').attr('data-responsive') == 'true'){
                $('.cambio-estado').off('change');
                $('.cambio-estado').attr('data-responsive','false');
            }
        });
    }
    
}
window.addEventListener('DOMContentLoaded',loadPage);