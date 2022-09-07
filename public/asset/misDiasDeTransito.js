function loadPage() {
    let general = new General();
    let idDiaTransito = null;
    let dataDiasTransito = $('#tabla-dias-transito').DataTable({
        ajax: {
            url: 'ver',
            method: 'POST',
            headers: general.jsonToken
        },
        columns: [
            { data: null, searchable: false, orderable: false },
            { data: 'departamento_origen' },
            { data: 'departamento_destino'},
            { data: 'ciudad_base'},
            { data: 'ciudad_destino' },
            { data: 'origen_base_km'},
            { data: 'origen_base_hr_terrestre'},
            { data: 'origen_base_hr_aereo'},
            { data: 'base_destino_km' },
            { data: 'transito_destino_hr' },
            { data: 'total_km_origen_destino' },
            { data: 'total_hr_origen_destino' },
            { data: 'total_origen_base_hr_aereo' },
            { data: 'id', searchable: false, orderable: false, name: 'id', render: function (data, type, row) { return `<div class="d-flex justify-content-center flex-wrap" style="gap:5px;"><button data-transito="${data}" class="btn btn-sm btn-primary edidt-transito"><i class="fas fa-pen"></i></button><button data-transito="${data}" class="btn btn-sm btn-danger delete-transito"><i class="fas fa-trash-alt"></i></button></div>` } }
        ],
    });
    
    dataDiasTransito.on('draw.dt', function () {
        let info = dataDiasTransito.page.info();
        dataDiasTransito.column(0, { search: 'applied', order: 'applied', page: 'applied' }).nodes().each(function (cell, i) {
            cell.innerHTML = i + 1 + info.start;
        });
    });

    $('.calcular-km').change(function(e){
        let total = 0;
        for (const valor of document.querySelectorAll('.calcular-km')) {
            let km = parseFloat(valor.value);
            total += isNaN(km) ? 0 : km;
        }
        document.getElementById('id_total_km_origen_destino').value = total.toFixed(2);
    });
    $('.calcular-hr').change(function(e){
        let totalhrTe = parseFloat($('#id_origen_base_hr_terrestre').val());
        let ambosHr = parseFloat($('#id_transito_destino_hr').val());
        let totalHrAe = parseFloat($('#id_origen_base_hr_aereo').val());
        totalhrTe = isNaN(totalhrTe) ? 0 : totalhrTe;
        ambosHr = isNaN(ambosHr) ? 0 : ambosHr;
        totalHrAe = isNaN(totalHrAe) ? 0 : totalHrAe;
        $('#id_total_hr_origen_destino').val(totalhrTe > 0 ? (totalhrTe + ambosHr).toFixed(2) : 0);
        $('#id_total_origen_base_hr_aereo').val(totalHrAe > 0 ? (totalHrAe + ambosHr).toFixed(2) : 0);
    })
    document.getElementById('tabla-dias-transito').onclick = async function (e) {
        if (e.target.classList.contains('delete-transito')) {
            alertify.confirm('Alerta', '¿Estás seguro de eliminar este día de tránsito?', async () => {
                let datos = new FormData()
                datos.append('dias_transito', e.target.dataset.transito);
                try {
                    let response = await general.funcfetch('eliminar', datos);
                    alertify.success(response.success);
                    dataDiasTransito.draw();
                } catch (error) {
                    alertify.error('Error al eliminar el día de tránsito');
                    console.error(error);
                }
            }, () => { })
        }
        if (e.target.classList.contains('edidt-transito')) {
            let datos = new FormData()
            datos.append('dias_transito', e.target.dataset.transito);
            try {
                let response = await general.funcfetch('ver', datos);
                $('.simple-select2').val("").trigger('change');
                for (const key in response.transito) {
                    if (Object.hasOwnProperty.call(response.transito, key)) {
                        const valor = response.transito[key];
                        const dom = document.getElementById('id_' + key);
                        if (key == 'departamento_origen' || key == 'departamento_destino'){
                            $('#id_' + key + ' option').each((k,v) => {
                                if ($(v).text().toLowerCase() == valor.toLowerCase()){
                                    $('#id_' + key).val(v.value);
                                }
                            });
                            continue;
                        }
                        if (!dom) {
                            continue;
                        }
                        dom.value = valor;
                    }
                }
                $('.simple-select2').trigger('change');
                document.getElementById('titulo-modal-transito').textContent = 'Modificar Día Tránsito';
                document.getElementById('agregar-transito').textContent = 'Actualizar';
                idDiaTransito = e.target.dataset.transito;
                $('#modal-detalle-dias-transito').modal('show');
            } catch (error) {
                alertify.error('Error al obtener día de tránsito');
                console.error(error);
            }
        }
    }
    document.getElementById('agregar-transito').onclick = () => document.getElementById('id_send_dias_transito').click();

    document.getElementById('modificar-dias-fletes').onsubmit = async function (e) {
        e.preventDefault();
        let url = 'agregar';
        let datos = new FormData(this);
        if (idDiaTransito) {
            url = 'actualizar';
            datos.append('dias_transito', idDiaTransito);
        }
        let response = await general.funcfetch(url, datos);
        if (response.alerta) {
            alertify.alert('Alerta', response.alerta);
            return
        }
        this.reset();
        alertify.success(response.success);
        dataDiasTransito.draw();
        $('#modal-detalle-dias-transito').modal('hide');
    }
    $('#modal-detalle-dias-transito').on('hidden.bs.modal', function (e) {
        document.getElementById('titulo-modal-transito').textContent = 'Agregar Día Tránsito';
        document.getElementById('agregar-transito').textContent = 'Agregar';
        idDiaTransito = null;
        document.getElementById('modificar-dias-fletes').reset();
        $('#id_departamento_origen').val("3926").trigger("change");
        $('#id_departamento_destino').val("").trigger("change");
    });
}
window.addEventListener('DOMContentLoaded', loadPage);