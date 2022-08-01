<div class="modal fade" id="manifiesto-mostrar" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manifiesto N° <span id="informe_nro_manifiesto"></span></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="form-group col-6">
                <label for="informe_fech_mani">Fecha Manifiesto</label>
                <input type="date" name="fech_mani" id="informe_fech_mani" class="form-control" disabled>
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
                <select class="form-control simple-select2" disabled name="id_cluster" id="informe_clusters"></select>
            </div>
            <div class="form-group col-12">
                <h6 class="text-primary">Guías manifiestadas</h6>
            </div>
            <div class="col-12">
                <ol id="lista-guias" class="ml-3"></ol>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
