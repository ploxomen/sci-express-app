<?php

namespace App\Http\Controllers;

use App\Models\ClienteDetalleEnvio;
use App\Models\Clientes as ModelsClientes;
use App\Models\DocumentosObligatorios;
use App\Models\DocumentosObligatoriosExcluidos;
use App\Models\InformacionAdicional;
use App\Models\ModeloSolicitud;
use App\Models\Tipos\Documentos;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Clientes extends Controller
{
    public function viewAgreModi(Request $request)
    {
        $dep = (new Ubigeo)->getDepartamentosClientes();
        $tipo_documentos = Documentos::where('estado','1')->get();
        $agregar = true;
        $info_cli = null;
        $id_new_prov = time();
        if($request->has('nro_doc')){
            $agregar = false;
            $info_cli = ['nro_doc' => $request->nro_doc];
        }
        $embaseEmbalaje = InformacionAdicional::getEnvaseEmbalaje();
        return view('cliente.cliagregar',compact('id_new_prov','info_cli','agregar','dep','tipo_documentos','embaseEmbalaje'));
    }
    public function agregarSolicitudCliente()
    {
        $tipo_documentos = Documentos::where('estado', '1')->get();
        $hoy = date('Y-m-d');
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = User::obtenerClientesTrabajadores(Auth::id());
        $ultimo_id = ModeloSolicitud::getLastId();
        $dep = (new Ubigeo)->getDepartamentosClientes();
        $usuarioNom = Auth::user()->name . ' ' . Auth::user()->last_name;
        if (is_null($ultimo_id)) {
            $ultimo_id = 1;
        } else {
            $ultimo_id = $ultimo_id->id + 1;
        }
        return view('solicitud.solicitudcliente', compact('usuarioNom','dep', 'ultimo_id', 'clientes', 'tipo_documentos', 'hoy', 'trayectos'));
    }
    public function viewConsultaTrabajador()
    {
        $clientes = User::obtenerClientesTrabajadores(Auth::id());
        return view("cliente.trabajadores.consultaGuia",compact( "clientes"));
    }
    public function viewConsultaGeneral(){
        return view('comprobante.consultaguia');        
    }
    public function viewConsultaDetallada(){
        return view('comprobante.consultaguiaremitente');
    }
    public function eliminarProveedor(Request $request)
    {
        try {
            if ($request->ajax()) {
                ModelsClientes::deleteProveedor($request->cod_prov,$request->cod_cli);
                return response()->json(['success' => 'proveedor eliminado correctamente']);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerArchivosCli(Request $request)
    {
        try {
            if($request->ajax()){
                $result = ModelsClientes::getFileCliente($request->nro_doc);
                $cliente = ModelsClientes::firstWhere('nro_doc',$request->nro_doc);
                $result->alternativasDesaprobacion = DocumentosObligatorios::all(['id','opcion']);
                $result->alternativasDesaprobacionInactiva = DocumentosObligatoriosExcluidos::select('doc_oblig_id')->where('cliente_id',$cliente->id)->pluck('doc_oblig_id');
                return response()->json(['files' => $result]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerCliEdit(Request $request)
    {
        try {
            if($request->ajax()){
                $result = ModelsClientes::where(['id' => $request->nro_doc])->first();
                $info_ubigeo = null;
                $proveedores = [];
                $detalleEnvios = [];
                if(!is_null($result)){
                    $info_ubigeo['prov_sa'] = (new Ubigeo)->getProvinciasCliente($result->dpto_sa);
                    $info_ubigeo['dist_sa'] = (new Ubigeo)->getDistritosCliente($result->prov_sa);
                    $info_ubigeo['prov_so'] = (new Ubigeo)->getProvinciasCliente($result->dpto_so);
                    $info_ubigeo['dist_so'] = (new Ubigeo)->getDistritosCliente($result->prov_so);
                    $detalleEnvios = ClienteDetalleEnvio::select('envase_embalaje','largo','ancho','altura')->where('cliente_id',$result->id)->get();
                    $proveedores = ModelsClientes::obtenerProveedores($result->id);
                }
                $message = is_null($result) ? ['info' => 'No se encontró el cliente'] : ['cliente' => $result,'ubigeos' => $info_ubigeo,'proveedores' => $proveedores, 'detalleEnvios' => $detalleEnvios];
                return response()->json($message);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function misClientes()
    {
        return view('cliente.misclientes');
    }
    public function obtenerClientes(Request $request)
    {
        try {
            $datos = $request->only('estadoDoc');
            foreach ($datos as $k => $v) {
                $datos[$k] = is_null($v) ? "" : $v;
            }
            $clientes = ModelsClientes::obtenerCli($datos);
            return datatables()->of($clientes)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function eliminarCli(Request $request)
    {
        try {
            ModelsClientes::where(['id' => $request->id_cliente])->update(['estado' => 0]);
            return response()->json(['success' => 'Cliente eliminado correctamente']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function viewArchivos(Request $request)
    {
        $clientes = ModelsClientes::getClientesFiles();
        return view('cliente.clidocumento',compact( 'clientes'));
    }
    public function documentosObligatorios(Request $request) {
        $result = [];
        switch ($request->tipo) {
            case 'agregar-opcion':
                DocumentosObligatorios::create([
                    'opcion' => $request->opcion
                ]);
                $result = ['success' => 'Alternativa creada correctamente'];
            break;
            case 'eliminar-opcion':
                DocumentosObligatorios::find($request->id)->delete();
                $result = ['success' => 'Alternativa creada correctamente'];
            break;
            case 'desmarcar-opcion-cliente':
                $cliente = ModelsClientes::firstWhere('nro_doc',$request->nro_doc);
                if($request->seleccionar === "false"){
                    DocumentosObligatoriosExcluidos::create([
                        'doc_oblig_id' => $request->id,
                        'cliente_id' => $cliente->id
                    ]);
                }else{
                    DocumentosObligatoriosExcluidos::where([
                        'doc_oblig_id' => $request->id,
                        'cliente_id' => $cliente->id
                    ])->delete();
                }
                $result = ['success' => 'Alternativa quitada correctamente para el cliente'];
            break;
        }
        return response()->json($result);
    }
    public function cambiarEstadoArchi(Request $request)
    {
       try {
            $datos = $request->all();
            $valores = [
               $datos['colum'] => $datos['valor'] , 
               $datos['colum_doc'] => $datos['valor_doc']
            ];
            if($request->colum_doc == 'estado_cliente_file'){
                unset($valores['estado_cliente_file']);
            }
            $response = ModelsClientes::updateEstusFile($valores,$request->nro_doc);
            return response()->json(['success' => $response]);

       } catch (Exception $ex) {
           return response()->json(['error' => $ex->getMessage()]);
       }
    }
    public function agregarCli(Request $request)
    {
        try {
            $datos = $request->except(['envase_embalaje','detalle_largo','detalle_ancho','detalle_altura']);
            $msg = '';
            $agregar = true;
            $proveedorAdd = null;
            $proveedorEdid = null;
            if($request->has('prov_modi')){
                $proveedorEdid = json_decode($datos['prov_modi'], true);
                unset($datos['prov_modi']);
            }
            if ($request->has('prov_add')) {
                $proveedorAdd = json_decode($datos['prov_add'], true);
                unset($datos['prov_add']);
            }
            $datos['centros_costo'] = $request->has('centros_costo') ? implode(";", $datos['centros_costo']) : null;
            $id_cli = $request->has('id_cliente') ? $request->id_cliente : null;
            if ($request->has('id_cliente')) {
                unset($datos['id_cliente']);
                $datos['aprob_file'] = $request->has('aprob_file') ? 1 : 0;
                $datos['aprob_cred'] = $request->has('aprob_cred') ? 1 : 0;
                $datos['alta_clien'] = $request->has('alta_clien') ? 1 : 0;
                $datos['estado_doc'] = $request->has('aprob_file') && $request->has('aprob_cred') && $request->has('alta_clien') ? 1 : 0;
                $datos['last_user_update'] = Auth::id();
                $datos['last_date_update'] = date('Y-m-d H:i:s');
                ModelsClientes::where(['id' => $id_cli])->update($datos);
                $msg = 'Cliente modificado correctamente';
                $agregar = false;
            }else{
                $datos['user_create'] = Auth::id();
                $id_cli = ModelsClientes::addCliente($datos);
                $msg = 'Cliente agregado correctamente';
            }
            ClienteDetalleEnvio::where('cliente_id',$id_cli)->delete();
            if($request->has('envase_embalaje')){
                $totalDetalle = count($request->envase_embalaje);
                for ($i=0; $i < $totalDetalle; $i++) { 
                    ClienteDetalleEnvio::create([
                        'cliente_id' => $id_cli,
                        'envase_embalaje' => $request->envase_embalaje[$i],
                        'largo' => $request->detalle_largo[$i],
                        'ancho' => $request->detalle_ancho[$i],
                        'altura' => $request->detalle_altura[$i]
                    ]);
                }
            }
            ModelsClientes::addOrUpdateProveedores(Auth::id(), $proveedorAdd, $proveedorEdid,$id_cli);
            return response()->json(['message' => $msg, 'action' => $agregar]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }
}
