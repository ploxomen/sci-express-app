<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\InformacionAdicional;
use App\Models\ModeloCluster;
use App\Models\ModeloGuias;
use App\Models\Tipos\Documentos;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Gias extends Controller
{
    public function viewGuias()
    {
        $modulos = User::getModulos(Auth::id());
        $via_envios = InformacionAdicional::viaEnvio();
        $tipo_documentos = Documentos::where('estado','1')->get();
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        $trayecto = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = Clientes::obtenerCliConProvee();
        $cli_cli = Clientes::obtenerCliDeCli();
        $nro_guia = ModeloGuias::select('prefijo','nro_guia')->orderBy('nro_guia', 'desc')->first();
        $nro_os = ModeloGuias::select('nro_guia')->where(['prefijo' => null])->orderBy('nro_guia', 'desc')->first();
        $nro_guia_result = '001-0000001';
        if(!empty($nro_guia)){
            if($nro_guia->nro_guia == 9999999){
                $nro_guia_result = str_pad((intval($nro_guia->prefijo) + 1),3,'0',STR_PAD_LEFT) . '-' . '0000001';
            }
            if($nro_guia->nro_guia < 9999999){
                $nro_guia_result = str_pad((intval($nro_guia->prefijo)), 3, '0', STR_PAD_LEFT) . '-' . str_pad((intval($nro_guia->nro_guia) + 1), 7, '0', STR_PAD_LEFT);
            }
        }
        if (!empty($nro_os)) {
            $nro_os->nro_guia++;
            $nro_os = str_pad($nro_os->nro_guia,7,"0",STR_PAD_LEFT);
        }else{
            $nro_os = "0000001";
        }
        $hoy = date('Y-m-d');
        $antes = date('Y-m-d',strtotime($hoy . '-2 days'));
        return view('comprobante.guias',compact('nro_guia_result','cli_cli','clientes','departamentos','hoy','trayecto','via_envios', 'nro_os','tipo_documentos','modulos','antes'));
    }
    public function deleteImgGuia(Request $request)
    {
        $guia = ModeloGuias::find($request->id_guia);
        if(empty($guia)){
            return response()->json(['error' => 'No se encontró la guía']);
        }
        $deleteGuia = ModeloGuias::deleteImgEntregaGuia($request->id_guia,$request->id_img);
        if(!Storage::disk('guias-img')->exists($deleteGuia['nomFile']->nom_id_file)){
            return ['error' => 'Imagen no encontrada para ser eliminada'];
        }
        if($deleteGuia['delete'] === 1 && Storage::disk('guias-img')->exists($deleteGuia['nomFile']->nom_id_file)){
            Storage::disk('guias-img')->delete($deleteGuia['nomFile']->nom_id_file);
            return response()->json(['success' => 'Imagen eliminada correctamente']);
        }
        return response()->json(['error' => 'No se eliminó la imagen']);
    }
    public function obtenerDiasTransito(Request $request)
    {
        try {
            return response()->json(['dias' => InformacionAdicional::obtenerDiasTransito($request->departamento_origen,$request->departamento_destino,$request->provincia_destino,$request->ciudad_final)]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function viewGuiasRetornadas()
    {
        $modulos = User::getModulos(Auth::id());
        $clusters = ModeloCluster::obtenerTodosClusters();
        return view('retornos.retornosguiasencargado',compact('modulos','clusters'));
    }
    public function uploadImgDetalleGuia(Request $request)
    {
        $filesDb = [];
        try {
            $guia = ModeloGuias::find($request->id_guia);
            if (empty($guia)) {
                return response()->json(['error' => 'No se encontró la guía']);
            }
            if($guia->situ < 8 || $guia->estado === 3){
                return response()->json(['error' => 'La guía/OS no esta apta para agregar imagenes']);
            }
            $id_file = time();
            foreach ($request->file('image') as $file) {
                $nombre = $id_file . '_' . $file->getClientOriginalName();
                Storage::disk('guias-img')->put($nombre, File::get($file));
                $filesDb[] = [
                    'id_guia' => $request->id_guia,
                    'nom_file' => $file->getClientOriginalName(),
                    'nom_id_file' => $nombre,
                    'size' => $file->getSize(),
                    'type' => $file->extension()
                ];
            }
            $imgAdd = ModeloGuias::addNewImgGuiaEntrega($filesDb);
            return response()->json(['success' => count($imgAdd) > 1 ? 'Imagen cargada correctamente' : 'Imágenes subidas correctamente','list' => $imgAdd]);
        } catch (Exception $ex) {
            Storage::disk('guias-img')->delete($filesDb);
            return response()->json(['error' => $ex->getMessage()]);
        }
        dd($request->all());
    }
    public function guiasRetornadas(Request $request)
    {
        if($request->ajax()){
            try {
                $id_usuario = $request->has('cluster') ? $request->cluster : Auth::id();
                switch ($request->tipo) {
                    case 'mostrar-guias':
                        return datatables()->of(ModeloGuias::mostrarRetornoGuias($request->tipo,$id_usuario))->toJson();
                    break;
                    case 'cambio-estado':
                        ModeloGuias::cambiarEstadoRetornoGuia($request->cluster, $request->guia, $request->estado);
                        return response()->json(['success' => 'se cambio el status de la guia de manera correcta']);
                    break;
                }

            } catch (Exception $ex) {
                return response()->json(['error' => $ex->getMessage()]);
            }
        }
    }
    public function eliminarDetalleGuia(Request $request)
    {
        return response()->json(ModeloGuias::elimiarDetalleGuia($request->guia,$request->detalle,$request->situacion_guia));
    }
    public function obtenerDetalleTabla()
    {
        $datos['grupo_envio'] = InformacionAdicional::getGrupoEnvio();
        $datos['tipo_envio'] = InformacionAdicional::getTipoEnvio();
        $datos['envase_embalaje'] = InformacionAdicional::getEnvaseEmbalaje();
        return response()->json($datos);
    }
    public function detalleMisGuias(Request $request)
    {
        try {
            $resultGuia = $request->tipo == 'detalle' ? ModeloGuias::obtenerDetalleMisGuias($request->guia) : ModeloGuias::verEditar($request->guia);
            return response()->json(['datos' => $resultGuia]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerAsignacionGlobal()
    {
        $guiasPorAsignar = ModeloGuias::selectRaw("DATE_FORMAT(fech_emis,'%d/%m/%Y') AS fecha_emision,id,prefijo,nro_guia")->where(['id_asignado' => null,'activo' => 1, 'situ' => 1, 'estado' => 1])->get();
        return response()->json(['guias' => $guiasPorAsignar]);
    }
    public function misGuias()
    {
        $modulos = User::getModulos(Auth::id());
        $estado_guias = InformacionAdicional::situacionEstado();
        $nro_por_asignar = ModeloGuias::guiaPorAsignar();
        $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
        $trayecto = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = Clientes::obtenerCliConProvee();
        $cli_cli = Clientes::obtenerCliDeCli();
        $via_envios = InformacionAdicional::viaEnvio();
        $departamentos = (new Ubigeo)->getDepartamentosClientes();
        $tipo_documentos = Documentos::where('estado', '1')->get();
        $usuarios = User::obtenerUsuarioRol(3);
        return view('comprobante.misguias',compact('usuarios','trayecto','departamentos','via_envios','clientes','tipo_documentos','cli_cli','modulos', 'estado_guias', 'nro_por_asignar', 'fechaFiltros'));
    }
    public function asignarMuchasGuiasUsuario(Request $request)
    {
        $usuario = $request->id_usuario;
        $guias = json_decode($request->guias,true);
        foreach ($guias as $guia) {
            ModeloGuias::where(['id' => $guia["id"], 'id_asignado' => null, 'activo' => 1, 'situ' => 1, 'estado' => 1])->update(['id_asignado' => $usuario]);
        }
        $nro_por_asignar = ModeloGuias::guiaPorAsignar();
        return response()->json(['success' => 'guías/OS asignadas correctamente', 'porAsignar' => isset($nro_por_asignar[0]->total) ? $nro_por_asignar[0]->total : 0]);
    }
    public function updateGuia(Request $request)
    {
        try {
            $datos = $request->all();
            $idGuia = $request->id_guia;
            $add_cli_bool = $request->has('add_cli_bool') ? $request->add_cli_bool : null;
            $detallesNew = [];
            $detallesUpdate = [];
            if (isset($datos['comp_clie'])) {
                $datos['comp_clie'] = implode(';', $datos['comp_clie']);
            }
            if (isset($datos['nro_comp_cli'])) {
                $datos['nro_comp_cli'] = implode(';', $datos['nro_comp_cli']);
            }
            if (isset($datos['detalle_guia_new'])) {
                unset($datos['detalle_guia_new']);
                $detallesNew = json_decode($request->detalle_guia_new, true);
            }
            if (isset($datos['detalle_guia_update'])) {
                unset($datos['detalle_guia_update']);
                $detallesUpdate = json_decode($request->detalle_guia_update, true);
            }
            $response = ModeloGuias::updateGuia($idGuia,$datos, $detallesNew, $detallesUpdate, $add_cli_bool);
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function anularGuia(Request $request)
    {
        try {
            ModeloGuias::anularGuias($request->id_guia);
            return response()->json(['success' => 'Guia anulada correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function cambiarEstadoGuia(Request $request)
    {
        try {
            ModeloGuias::cambioEstadoGuia($request->id_guia, $request->estado);
            return response()->json(['success' => 'Estado de guia cambiada con éxito']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function asignarUsuario(Request $request)
    {
        try {
            $total = ModeloGuias::asignarPersona($request->id_user,$request->id_guia);
            $resta = isset($total[0]->total) ? $total[0]->total : 0;
            return response()->json(['success' => 'Guia asignada correctamente','procesar' => $resta]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerMisGuias(Request $request)
    {
        if($request->ajax()){
            $guias = ModeloGuias::mostrarTodasGuias($request->estado,$request->tipo,$request->fechaInicio,$request->fechaFin);
            return datatables()->of($guias)->toJson();
        }
    }
    public function cambiarCluster(Request $request)
    {
        if ($request->ajax()) {
            ModeloGuias::where(['id' => $request->id_guia,'activo' => 1])->update(['id_cluster' => $request->id_cluster]);
            return response()->json(['success' => 'Se cambió correctamente el cluster']);
        }
    }
    public function actualizarInformes(Request $request)
    {
        if ($request->ajax()) {
            InformacionAdicional::calcularTransito();
            return response()->json(['success' => 'Embarques actualizados correctamente de informados a transito']);
        }
    }
    public function addGuia(Request $request)
    {
        try {
            $datos = $request->all();
            $add_cli = $request->has('add_cli_cli') ? $request->add_cli_cli : null;
            $add_cli_bool = $request->has('add_cli_bool') ? $request->add_cli_bool : null;
            $detalles = [];
            if(isset($datos['comp_clie'])){
                $datos['comp_clie'] = implode(';', $datos['comp_clie']);
            }
            if (isset($datos['nro_comp_cli'])) {
                $datos['nro_comp_cli'] = implode(';', $datos['nro_comp_cli']);
            }
            if(isset($datos['detalle_guia'])){
                unset($datos['detalle_guia']);
                $detalles = json_decode($request->detalle_guia, true);
            }
            $response = ModeloGuias::agregar($datos,$detalles, $add_cli, $add_cli_bool,Auth::id());
            return response()->json($response);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerCliOrProvDeta(Request $request)
    {
        try {
            $provincias = [];
            $distritos = [];
            $ínvitados = [];
            $idCliente = $request->has('idcliente') ? $request->idcliente : $request->id;
            if($request->tipo == 'cliente'){
                $datos = Clientes::getDetalleSoliCli($request->id);
                $provincias = Ubigeo::getProvinciasCliente($datos->dpto_so);
                $distritos = Ubigeo::getDistritosCliente($datos->prov_so);
            }else{
                $datos = Clientes::obtenerProveedor($request->id);
                $provincias = $datos->listProv;
                $distritos = $datos->listDist;
            }
            $ínvitados = Clientes::obtenerInvitados($idCliente);
            return response()->json(['datos' => $datos, 'prov' => $provincias, 'dist' => $distritos,'invitados' => $ínvitados]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function consultaGuia(Request $request)
    {
        if($request->ajax()){
            return response()->json(['result' => ModeloGuias::consultaGuiaCliente($request->all())]);
        }
    }
    public function consultaGuiaRemitentes(Request $request)
    {
        if($request->ajax()){
            switch ($request->tipo) {
                case 'remitentes':
                    $datos = ModeloGuias::obtenerGuiasConsulta($request->remitente,$request->fecha);
                    return datatables()->of($datos)->toJson();
                break;
                case 'remitentes-cliente':
                    $datos = ModeloGuias::obtenerGuiasConsultaTrabajador($request->cliente,Auth::id(), $request->fechaIncio,$request->fechaFin);
                    return datatables()->of($datos)->toJson();
                break;
            }
        }
    }
    public function detalleCliDeCli(Request $request)
    {
        try {
            $datos = Clientes::detalleCliDeCli($request->id);
            $provincias = [];
            $distritos = [];
            $ciudades = [];
            $dias = null;
            $complejoDistante = null;
            if(!empty($datos)){
                $provincias = Ubigeo::getProvinciasCliente($datos->departamento);
                $distritos = Ubigeo::getDistritosCliente($datos->provincia);
                $ciudades = Ubigeo::getCiudadesClientes($datos->distrito);
                $infoDepDest = (new Ubigeo)->getNameDepartamento($datos->departamento);
                $infoProvDest = (new Ubigeo)->getNameProvincia($datos->provincia);
                $infoCiudDest = (new Ubigeo)->getNameDistrito($datos->distrito);
                if(!empty($datos->id_ciudad_final)){
                    $infoCiudDest = Clientes::obtenerNombreCliDest($datos->id_ciudad_final);
                }
                $complejoDistante = Clientes::obtenerComplejoDistante(!empty($datos->id_ciudad_final) ? 'ciudad' : 'distrito', $datos->distrito, $datos->id_ciudad_final);
                $dias = InformacionAdicional::obtenerDiasTransito($request->departamentoOrigen, $infoDepDest, $infoProvDest, $infoCiudDest);
            }
            return response()->json(['datos' => $datos, 'prov' => $provincias,'dias' => $dias ,'dist' => $distritos, 'ciudades' => $ciudades,'complejo' => $complejoDistante]);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
}
