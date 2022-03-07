function loadedPage(){
    let frmFiltroCli = document.getElementById('frm_filtros');
    let tblCli = document.getElementById('data-clientes');
    let general = new General();
    function obtenerClientes(){
        $('[data-toggle="tooltip"]').tooltip('dispose');
        let datos = new FormData(frmFiltroCli);
        $('#pagination-cliente').pagination({
            pageSize : 25,
            dataSource: function(done){
                $.ajax({
                    url: "obtener?tipo=total",
                    type : "POST",
                    data : datos,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': general.token
                    },
                    dataType:"JSON",
                }).done(function(data){
                    let result = []
                    if(data.total != 0){
                        for (let i = 1; i <= parseInt(data.total); i++) {
                            result.push(i)
                        }
                    }
                    done(result)
                })
            },
            callback : function(data,pagination){
                if(!pagination.totalNumber){
                    tblCli.innerHTML = `<tr><td colspan="9">Sin clientes para mostrar</td></tr>`;
                }else{
                    datos.append('inicio',(pagination.pageNumber - 1) * pagination.pageSize);
                    datos.append('fin',pagination.pageSize);
                    obtenerInfoCli(datos)
                }
            },
            header : function(currentPage, totalPage, totalNumber){
                return "<div class='pagination-header'>Total:&nbsp;<p class='mdl-typography--body-2 m-0 p-1 main-content rounded border'>" +
                totalNumber +
                "</p></div>";
            }
        })

    }
    async function obtenerInfoCli(datos) {  
        try {
            let response = await general.funcfetch('obtener?tipo=info',datos);
            let template = ``;
            let indice = parseInt(datos.get('inicio'));
            response.clientes.forEach(cli => {
                indice++;
                template +=`<tr>
                    <td>${indice}</td>
                    <td>${cli.nom_cli}</td>
                    <td>${cli.dir_sede_adr}</td>
                    <td>${cli.nom_rl}</td>
                    <td>${cli.cel_rl}</td>
                    <td>${cli.email_rl}</td>
                    <td class="${cli.est_doc == 'PENDIENTE' ? 'text-danger' : 'text-success'}">${cli.est_doc}</td>
                    <td class="${cli.estado_text == 'INACTIVO' ? 'text-danger' : 'text-success'}">${cli.estado_text}</td>
                    <td><div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><a href="agregar-modificar?nro_doc=${cli.id}" class="btn btn-sm btn-primary" data-toggle="tooltip" title="Editar cliente"><i class="fas fa-user-edit"></i></a><button data-toggle="tooltip" title="Desactivar cliente" class="btn btn-sm btn-danger"><i class="fas fa-user-times"></i></button></div></td>
                </tr>`
            });
            tblCli.innerHTML = template;
            $('[data-toggle="tooltip"]').tooltip()
        } catch (error) {
            alertify.alert('Error',error.message);
            return error;
        }
    }
    obtenerClientes();


}
window.addEventListener('DOMContentLoaded',loadedPage);