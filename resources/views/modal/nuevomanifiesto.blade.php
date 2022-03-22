<div class="modal fade" id="manifestar-guias" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Manifestar guías</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div class="row form-group">
            <div class="form-group col-12">
                <label for="id_fech_mani">Fecha maniesto</label>
                <input type="date" class="form-control" name="fech_mani" id="id_fech_mani" max="{{$hoy}}" value="{{$hoy}}">
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="prov_view_origen">Prov Origen</label>
                <input type="text" class="form-control" id="prov_view_origen" disabled>
            </div>
            <div class="form-group col-12 col-lg-6">
                <label for="prov_view_destino">Prov Destino</label>
                <input type="text" class="form-control" id="prov_view_destino" disabled>
            </div>
             <div class="form-group col-12">
                 <span class="text-danger">N° de guías seleccionadas: <span id="nro_selecionadas"></span></span>
            </div>
            <div class="form-group col-12">
                <label for="id_cluster">Cluster</label>
                <select name="cluster" id="id_cluster" class="form-control simple-select2"></select>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="btn-generar-mani">Generar</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>