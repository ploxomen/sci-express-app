<div class="modal fade" id="seleccion-comprobantes" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Seleccionar comprobantes</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="!flex !gap-4 justify-center flex-wrap py-2">
            <button id="btn-seleccion-remitente" class="border w-full max-w-80 rounded-lg px-4 py-2 text-center hover:bg-slate-50">
                <img src="/asset/img/comprobado.png" class="w-20 mx-auto block" alt="Check en una hoja">
                <span class="font-bold !text-base">Remitente</span>
                <p class="!text-xs text-slate-400">Genera una guía de remisión remitente en SUNAT </p>
            </button>
            <button id="btn-seleccion-transportista" class="border w-full max-w-80 rounded-lg px-4 py-2 text-center hover:bg-slate-50">
                <img src="/asset/img/envio.png" class="w-20 mx-auto block" alt="Transporte">
                <span class="font-bold !text-base">Transportista</span>
                <p class="!text-xs text-slate-400">Genera una guía de remisión transportista en SUNAT </p>
            </button>
        </div>
      </div>
    </div>
  </div>
</div>