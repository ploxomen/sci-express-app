function loadPage(){
    let general = new General();
    let contadorLi = 1;
    let frmAddCluster = document.getElementById('frm_cluster');
    const btnAddCluster = document.getElementById('agregar-cluster');
    $('#lista-destino-final .cb-ubigeo, #lista-destino-final .cb-select2-tags').select2('destroy');
    const ulListCiudad = document.getElementById('lista-destino-final');
    frmAddCluster.addEventListener('submit',function(e){
        e.preventDefault();
        $('#modal-difinir-cluster').modal("show");
        
    });
    btnAddCluster.addEventListener('click',async function(e){
        e.preventDefault();
        for (const li of ulListCiudad.children) {
            if(li.querySelector('.cb-ubigeo').value == ''){
                alertify.error('complete la selección de distritos');
                return
            }
        }
        const selecionarBase = ulListCiudad.querySelectorAll(`input:checked`).length;
        if(!selecionarBase){
            alertify.error('favor de elegir la ciudad base');
            return
        }
        let ciudadesDestino = new Array();
        for (const ciudades of ulListCiudad.children) {
            const ubigeo = ciudades.querySelector('.cb-ubigeo');
            const ciudad = ciudades.querySelector('.cb-select2-tags');
            const base = ciudades.querySelectorAll(`input:checked`).length ? 1 : 0;
            ciudadesDestino.push({
                distrito : $(ubigeo).val(),
                ciudad : $(ciudad).val(),
                ciudad_base :  base
            })
        }
        let datos = new FormData(frmAddCluster);
        datos.append('type_document','3');
        datos.append('destino_final',JSON.stringify(ciudadesDestino));
        try {
            let response = await general.funcfetch('agregar',datos);
            alertify.alert('Mensaje',response.error ? response.error : response.success);
            if(response.success){
                resetForm(frmAddCluster)
            }
            $('#modal-difinir-cluster').modal("hide");
        } catch (error) {
            alertify.alert('Error',error.message);
            console.error(error);
        }
        
    });
    $('#modal-difinir-cluster').on('hidden.bs.modal', function (e) {
        Array.from(ulListCiudad.children).forEach((v,k) => {
            if(k !== 0){
                v.remove();
            }
        });
        $('#lista-destino-final .cb-ubigeo').val("").trigger("change");
        $('#lista-destino-final .cb-select2-tags').html("<option></option>");
        $('#lista-destino-final .selection-base').prop("checked",false);
    })
    $('#modal-difinir-cluster').on("show");
    const btnAddCiudad = document.getElementById('btn-add-ciudad');
    btnAddCiudad.addEventListener('click',function(e){
        e.preventDefault();
        contadorLi++;
        $('#lista-destino-final .cb-ubigeo, #lista-destino-final .cb-select2-tags').select2('destroy');
        const liPrimero = ulListCiudad.children[0];
        const liDuplicado = liPrimero.cloneNode(true);
        liDuplicado.querySelector(".cb-ubigeo").setAttribute('id','id_distrito_clu_' + contadorLi);
        liDuplicado.querySelector(".cb-ubigeo").setAttribute('data-ciudades','#id_ciudad_destino_' + contadorLi);
        liDuplicado.querySelector(".cb-select2-tags").setAttribute('id','id_ciudad_destino_' + contadorLi);
        liDuplicado.querySelector(".selection-base").checked = false;
        for (const element of liDuplicado.querySelectorAll("select")) {
            element.value = "";
            if(element.classList.contains('cb-select2-tags')){
                element.innerHTML = '<option></option>';
            }
        }
        ulListCiudad.append(liDuplicado);
        alertify.success('accion relizada');
        $('#lista-destino-final .cb-ubigeo').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder: 'Seleccione una opción'
        }).on('select2:select',async function(e){
            let datosn = new FormData();
            datosn.append('distrito',$(this).val());
            let response = await general.funcfetch(window.location.origin + '/intranet/ubigeo/ciudades',datosn);
            let template = `<option></option>`;
            response.ciudades.forEach(c => {
                template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
            });
            document.querySelector($(this).attr('data-ciudades')).innerHTML = template;
        });
        $('#lista-destino-final .cb-select2-tags').select2({
            theme: 'bootstrap',
            width: '100%',
            placeholder : 'Seleccione o agregue una opción',
            tags: true,
        });
    });
    ulListCiudad.addEventListener('click',function(e){
        if(e.target.classList.contains('btn-delete-destino')){
            if(ulListCiudad.children.length === 1){
                alertify.error('debe haber como mínimo una ciudad de destino');
                return
            }
            e.target.parentElement.parentElement.parentElement.remove();
            alertify.success('accion relizada');
        }
    })
    function resetForm(form){
        form.reset();
        $('.cb-ubigeo, .cb-mult-nuevo').val("").trigger('change');
        $('.simple-select2').val("").trigger('change');
    }
    frmAddCluster.addEventListener('reset',function(e){
        resetForm(this);
    });
    $('#lista-destino-final .cb-ubigeo').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder: 'Seleccione una opción',
    }).on('select2:select',async function(e){
        let datosn = new FormData();
        datosn.append('distrito',$(this).val());
        let response = await general.funcfetch(window.location.origin + '/intranet/ubigeo/ciudades',datosn);
        let template = `<option></option>`;
        response.ciudades.forEach(c => {
            template += `<option value="${c.id}">${c.nombre_ciudad}</option>`;
        });
        document.querySelector($(this).attr('data-ciudades')).innerHTML = template;
    });
    $('#lista-destino-final .cb-select2-tags').select2({
        theme: 'bootstrap',
        width: '100%',
        placeholder : 'Seleccione o agregue una opción',
        tags: true,
    });
}
window.addEventListener('DOMContentLoaded',loadPage);