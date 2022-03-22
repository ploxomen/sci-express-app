@extends('general.index')
@section('head')
    <script src="{{asset('asset/guias.js')}}"></script>
    <title>Guias</title>
@endsection
@section('body')
<section class="p-3">
    <form id="frm_guia" class="mb-4 row">
        <div class="my-4 col-12">
            <div class="baner-modulo">
                <img src="{{asset('asset/img/intranet/bill.png')}}" alt="Imagen de clientes">
            </div>
            <h3 class="text-primary text-center">Guías</h3>
        </div>
        <div class="col-12 p-0 px-lg-2 mb-4">
            <div class="bg-white p-3 form-row">
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_resp_pago">Responsable del pago</label>
                    <select name="resp_pago" id="id_resp_pago" class="form-control">
                        <option value="REMITENTE">REMITENTE</option>
                        <option value="DESTINATARIO">DESTINATARIO</option>
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_estado">Estado</label>
                    <select name="estado" id="id_estado" class="form-control">
                        <option value="1">ACTIVA</option>
                        <option value="2">ANULADA</option>
                        <option value="3">INACTIVA</option>
                    </select>
                </div>
                {{-- <div class="form-group d-none col-12 col-md-6 col-lg-3">
                    <label for="id_situ">Situación</label>
                    <select name="situ" id="id_situ" disabled class="form-control">
                        @foreach ($situacion as $docu)
                            <option value="{{$docu->id}}">{{$docu->tipo}}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_fech_emis">Fecha Emisión</label>
                    <input type="date" class="form-control" value="{{$hoy}}" max="{{$hoy}}" name="fech_emis" id="id_fech_emis">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_comp_clie">Comprobantes</label>
                    <select name="comp_clie[]" id="id_comp_clie" class="form-control" multiple>
                        @foreach ($comprobantes as $comp)
                            <option value="{{$comp->id}}">{{$comp->comprobantes}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_comp_cli">N° comprobante</label>
                    <select name="nro_comp_cli[]" id="id_nro_comp_cli" class="form-control" multiple></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_cant_comp">Cantidad Comprobante</label>
                    <input type="text" class="form-control" name="cant_comp" id="id_cant_comp">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_orde_comp">Orden de compra</label>
                    <input type="text" class="form-control" name="orde_comp" id="id_orde_comp">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_fech_venc_orde_comp">Vto OC</label>
                    <input type="date" class="form-control" name="fech_venc_orde_comp" id="id_fech_venc_orde_comp">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_fech_tras">Fecha Traslado</label>
                    <input type="date" class="form-control" min="{{$hoy}}" name="fech_tras" id="id_fech_tras">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_tray_env">Trayecto Envio</label>
                    <select name="tray_env" id="id_tray_env" class="form-control">
                        @foreach ($trayecto as $tra)
                            <option value="{{$tra->id}}">{{$tra->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_nro_manifiesto">Vía Envío</label>
                    <select name="via_envi" id="id_via_envi" class="form-control">
                        @foreach ($via_envios as $via)
                            <option value="{{$via->id}}">{{$via->envio}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_via_tras">Via Traslado</label>
                    <select name="via_tras" id="id_via_tras" class="form-control">
                        @foreach ($via_envios as $via)
                            <option value="{{$via->id}}">{{$via->envio}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12 p-0 px-lg-2 col-lg-6 mb-4">
            <fieldset class="px-3 bg-white border form-row">
                <legend class="d-inline-block bg-white w-auto px-2 border shadow-sm text-left legend-add text-info">REMITENTE</legend>
                <div class="form-group col-12">
                    <label for="id_remi_nomb">Nombres</label>
                    <select class="form-control simple-select2" name="id_remitente" id="id_remi_nomb">
                        <option></option>
                        @foreach ($clientes as $cli)
                            <option data-type="cliente" value="{{$cli->id}}">{{$cli->nom_cli}}</option>
                            @if (!empty($cli->proveedores))
                                <optgroup label="{{$cli->nom_cli}}">
                                @foreach ($cli->proveedores as $prov)
                                    <option data-type="proveedor" data-id-cliente="{{$cli->id}}" value="{{$prov->id}}">{{$prov->nomb_prov}}</option>
                                @endforeach
                                </optgroup>

                            @endif                               
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_remi_tipo_docu">Tipo documento</label>
                    <select name="remi_tipo_docu" id="id_remi_tipo_docu" class="form-control" data-valor-cliente="doc_ident" data-valor-proveedor="tipo_docu_prov">
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_remi_nro_docu">N° documento</label>
                    <input type="text" class="form-control" name="remi_nro_docu" id="id_remi_nro_docu" data-valor-cliente="nro_doc" data-valor-proveedor="nro_docu_prov">
                </div>
                <div class="form-group col-12">
                    <label for="id_remi_dire">Dirección</label>
                    <input type="text" class="form-control" name="remi_dire" id="id_remi_dire" data-valor-cliente="dir_sede_so" data-valor-proveedor="dire_prov">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_remi_depa">Departamento</label>
                    <select class="form-control cb-ubigeo" name="remi_depa" id="id_remi_depa" data-consulta="provincia" data-info="id_remi_prov" data-valor-proveedor="depa_prov" data-info-distrito="id_remi_dist" data-valor-cliente="dpto_so">
                        <option value="" hidden>Seleccione un departamento</option>
                        @foreach ($departamentos as $depar)
                            <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_remi_prov">Provincia</label>
                    <select class="form-control cb-ubigeo" name="remi_prov" id="id_remi_prov" data-consulta="distrito" data-info="id_remi_dist" data-valor-proveedor="prov_prov" data-valor-cliente="prov_so">
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="">Distrito</label>
                    <select class="form-control cb-ubigeo" name="remi_dist" id="id_remi_dist" data-valor-cliente="dist_so" data-valor-proveedor="dist_prov">
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="col-12 p-0 px-lg-2 col-lg-6 mb-4">
            <fieldset class="px-3 bg-white border form-row">
                <legend class="d-inline-block bg-white w-auto px-2 border shadow-sm text-left legend-add text-info">DESTINATARIO</legend>
                <div class="form-group col-12">
                    <label for="id_dest_nomb">Nombres</label>
                    <select class="form-control cb-select2-tags" name="id_destinatario" id="id_dest_nomb">
                        <option></option>
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_dest_tipo_docu">Tipo documento</label>
                    <select name="dest_tipo_docu" id="id_dest_tipo_docu" class="form-control" data-cli-cli="tipo_doc">
                        @foreach ($tipo_documentos as $tipo_docu)
                            <option value="{{$tipo_docu->id}}">{{$tipo_docu->tipo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-lg-6">
                    <label for="id_dest_nro_docu">N° documento</label>
                    <input type="text" class="form-control" name="dest_nro_docu" id="id_dest_nro_docu" data-cli-cli="nro_doc">
                </div>
                
                <div class="form-group col-12">
                    <label for="id_dest_dire">Dirección</label>
                    <input type="text" class="form-control" name="dest_dire" id="id_dest_dire" data-cli-cli="direccion">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dest_depa">Departamento</label>
                    <select name="dest_depa" id="id_dest_depa" class="form-control cb-ubigeo cb-dest" data-cli-cli="departamento" data-consulta="provincia" data-info="id_dest_prov" data-info-distrito="id_dest_dist">
                        <option value="" hidden>Seleccione un departamento</option>
                        @foreach ($departamentos as $depar)
                            <option value="{{$depar->id_ubigeo}}">{{$depar->nombre_ubigeo}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dest_prov">Provincia</label>
                    <select name="dest_prov" id="id_dest_prov" class="form-control cb-ubigeo cb-dest" data-cli-cli="provincia" data-consulta="distrito" data-info="id_dest_dist"></select>
                </div>
                <div class="form-group col-12 col-md-6 col-lg-4">
                    <label for="id_dest_dist">Distrito</label>
                    <select name="dest_dist" id="id_dest_dist" class="form-control cb-ubigeo cb-dest" data-cli-cli="distrito">
                    </select>
                </div>
            </fieldset>
        </div>
        <div class="col-12 p-0 px-lg-2 mb-4">
            <div class="bg-white p-3 form-row">
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="id_dias_tran">Días de tránsito</label>
                    <input type="text" class="form-control" name="dias_tran" id="id_dias_tran">
                </div>
                <div class="form-group col-12 col-md-6 col-lg-3">
                    <label for="">Cst_Mest_Env</label>
                    <input type="text" class="form-control" name="cst_mest_env" id="id_cst_mest_env">
                </div>
            </div>
            
        </div>
        <div class="col-12 p-0 px-lg-2 form-group">
            <div class="form-group text-right">
                <button type="button" class="btn btn-primary" id="add-new-detalle"><i class="fas fa-plus"></i></button>
            </div>
            <div class="p-3 bg-white table-responsive">
                <table class="table table-sm table-bordered" style="min-width: 1300px;">
                    <thead>
                        <tr>
                            <th style="width: 200px;">Grupo Envio</th>
                            <th style="width: 100px;">Tipo Envio</th>
                            <th style="width: 200px;">Descripción</th>
                            <th style="width: 200px;">Embase o <br> Embalaje</th>
                            <th style="width: 100px;">Cantida</th>
                            <th style="width: 100px;">Kg</th>
                            <th style="width: 100px;">Lar</th>
                            <th style="width: 100px;">Anc</th>
                            <th style="width: 100px;">Alt</th>
                            <th style="width: 100px;">Vol</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-detalle">
                            @for ($i = 0; $i < 6; $i++)
                            <tr>
                                <td>
                                    <select data-name="grupo_envio" class="form-control form-control-sm simple-select2">
                                        <option value="" hidden>Seleccione una opción</option>
                                        <option value="MEDICINA">MEDICINA</option>
                                        <option value="REPUESTOS">REPUESTOS</option>
                                        <option value="VALORADA">VALORADA</option>
                                        <option value="OTROS">OTROS</option>
                                    </select>
                                </td>
                                <td>
                                    <select data-name="tipo_envio" class="form-control form-control-sm simple-select2">
                                        <option value="" hidden>Seleccione una opción</option>
                                        <option value="DGR">DGR</option>
                                        <option value="GENERAL">GENERAL</option>
                                        <option value="REFRIGERADO">REFRIGERADO</option>
                                        <option value="VACUNA">VACUNA</option>
                                        <option value="DOCUMENTOS">DOCUMENTOS</option>
                                        <option value="EQ COMP. COMUN">EQ COMP. COMUN</option>
                                        <option value="OTROS">OTROS</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea data-name="descripcion" class="form-control form-control-sm"></textarea>
                                </td>
                                <td>
                                    <select data-name="embase_embalaje" class="form-control form-control-sm simple-select2">
                                        <option></option>
                                        <option value="EMBASE">A GRANEL</option>
                                        <option value="EMBALAJE">ATADOS</option>
                                        <option value="BALDES">BALDES</option>
                                        <option value="BARRICAS">BARRICAS</option>
                                        <option value="BIDONES">BIDONES</option>
                                        <option value="BOBINAS">BOBINAS</option>
                                        <option value="BOLSAS">BOLSAS</option>
                                        <option value="BOTELLONES">BOTELLONES</option>
                                        <option value="CAJAS">CAJAS</option>
                                        <option value="CAJONES">CAJONES</option>
                                        <option value="CANASTOS">CANASTOS</option>
                                        <option value="CANECAS">CANECAS</option>
                                        <option value="CARRETES">CARRETES</option>
                                        <option value="CILINDROS">CILINDROS</option>
                                        <option value="CISTERNAS">CISTERNAS</option>
                                        <option value="CUÑETES">CUÑETES</option>
                                        <option value="FARDOS">FARDOS</option>
                                        <option value="JAULAS">JAULAS</option>
                                        <option value="LATAS">LATAS</option>
                                        <option value="PLATAFORMA">PLATAFORMA</option>
                                        <option value="ROLLOS">ROLLOS</option>
                                        <option value="SACOS">SACOS</option>
                                        <option value="TAMBORES">TAMBORES</option>
                                        <option value="TONELES">TONELES</option>
                                    </select>
                                </td>
                                <td>
                                    <input data-name="cantidad" type="number" value="0.00" class="form-control form-control-sm" onchange="calcTotal(event,4,'total-cantidad')" min="0" step="0.01">
                                </td>
                                <td>
                                    <input data-name="kg" type="number" value="0.00" class="form-control form-control-sm" onchange="calcTotal(event,5,'total-kg')" min="0" step="0.01">
                                </td>
                                <td>
                                    <input data-name="largo" type="number" value="0.00" class="form-control form-control-sm" onchange="calcVolumen(event)" min="0" step="0.01">
                                </td>
                                <td>
                                    <input data-name="ancho" type="number" value="0.00" class="form-control form-control-sm" onchange="calcVolumen(event)" min="0" step="0.01">
                                </td>
                                <td>
                                    <input data-name="altura" type="number" value="0.00" class="form-control form-control-sm" onchange="calcVolumen(event)" min="0" step="0.01">
                                </td>
                                <td>
                                    <input data-name="volumen" type="number" value="0.00" class="form-control form-control-sm" onchange="calcTotal(event,9,'total-vol')" min="0" step="0.01">
                                </td>
                            </tr>
                            @endfor
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-right">
                                Totales
                            </td>
                            <td>
                                <input type="number" value="0.00" id="total-cantidad" class="form-control form-control-sm" min="0" step="0.01">
                            </td>
                            <td>
                                <input type="number" value="0.00" id="total-kg" class="form-control form-control-sm" min="0" step="0.01">
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <input type="number" value="0.00" id="total-vol" class="form-control form-control-sm" min="0" step="0.01">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div class="col-12 form-group p-0 px-lg-2">
            <button type="submit" class="btn btn-success">Generar</button>
        </div>
    </form>
</section>
@endsection