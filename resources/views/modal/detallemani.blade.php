<div class="modal fade" id="manifiesto-editar" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manifiesto N° <span id="informe_nro_manifiesto"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form class="row" id="update-manifiesto-form">
            <div class="form-group col-6">
                <label for="informe_fech_mani">Fecha Manifiesto</label>
                <input type="date" name="fech_mani" id="informe_fech_mani" class="form-control" required>
            </div>
            <div class="form-group col-6">
                <label for="cb_trayecto">Trayecto</label>
                <select class="form-control" id="cb_trayecto" disabled>
                  <option value="TERRESTRE">TERRESTRE</option>
                  <option value="AEREO">AEREO</option>
                </select>
            </div>
            <div class="form-group col-6">
                <label for="informe_nom_prov_orig">Provincia Origen</label>
                <input type="text" disabled id="informe_nom_prov_orig" class="form-control">
            </div>
            <div class="form-group col-6">
                <label for="informe_nom_prov_dest">Provincia Destino</label>
                <input type="text" disabled id="informe_nom_prov_dest" class="form-control">
            </div>
            <div class="form-group col-12">
                <label for="informe_clusters">Cluster</label>
                <select class="form-control simple-select2" name="id_cluster" id="informe_clusters" required></select>
            </div>
            <div class="form-group col-12">
                <h6 class="text-primary">Guías manifiestadas</h6>
            </div>
            <div class="col-12">
                <ol id="lista-guias" class="ml-3"></ol>
            </div>
            <div class="form-group col-12">
                <h6 class="text-primary">Insertar Guías por manifestar</h6>
                <small class="text-info">Recuerda que solo se mostrará las guias que tengan el mismo trayecto y cluster</small>
            </div>
            <div class="col-12">
                <ol id="lista-guias-pendientes" class="ml-3"></ol>
                <button type="submit" hidden id="update-manifiesto"></button>
            </div>

        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="actualizar-manifiesto">Actualizar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
