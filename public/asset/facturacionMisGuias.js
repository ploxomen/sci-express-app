function loadPage(){
    let general = new General();
    let tablaFacturacion = $('#tabla-guias').DataTable({
        ajax: {
            url: 'ver',
            method: 'POST',
            headers: general.jsonToken,
            data: function (d) {
                d.cliente = $('#id_filtro_cliente').val();
                d.trayecto = $('#id_tray_env').val();
                d.fechaInicio = $('#fecha_inicio').val();
                d.fechaFin = $('#id_fecha_fin').val();
            }
        },
        columns: [{
            data: 'fechaEmision'
        },
        {
            data: 'nroGuia'
        },
        {
            data: 'destinatario'
        },
        {
            data: 'comp_clie'
        },
        {
            data: 'nro_comp_cli'
        },
        {
            data: 'baseCluster'
        },
        {
            data: 'ciudadFinal'
        },
        {
            data: 'esAlejado'
        },
        {
            data: 'descripcionGuia',
            render : function(data){
                let template = "No definido";
                if(data && data.length > 0){
                    template = "";
                    data.forEach(d => {
                        if(d != 'null' && d != null){
                            template += `<p class="mb-1">${d}</p>`;
                        }
                    });
                    if(template == ""){
                        template = "No definido";
                    }
                }
                return template;
            }
        },
        {
            data: 'pesoGuia'
        },
        {
            data: 'pesoVolumen',
            render : function(data,type,full){
                if(data > full.pesoGuia){
                    return '(en blanco)';
                }
                return data;
            }
        },{
            data: 'valor_complejo_distante'
        },{
            data: 'fact_final'
        }
        ],
        pageLength : 100
        
    });
    document.getElementById('aplicar-filtros-btn').onclick = function () {  
        tablaFacturacion.draw();
    }
    for (const filtro of document.querySelectorAll('.filtros')) {
        $(filtro).on('select2:select', e => {
            tablaFacturacion.draw();

        })
    }
}
window.addEventListener('DOMContentLoaded',loadPage);