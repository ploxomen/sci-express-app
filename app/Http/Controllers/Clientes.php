<?php

namespace App\Http\Controllers;

use App\Models\Clientes as ModelsClientes;
use App\Models\Tipos\Documentos;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Clientes extends Controller
{
    public function viewAgreModi(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        $dep = (new Ubigeo)->getDepartamentosClientes();
        $tipo_documentos = Documentos::where('estado','1')->get();
        $agregar = true;
        $info_cli = null;
        $id_new_prov = time();
        if($request->has('nro_doc')){
            $agregar = false;
            $info_cli = ['nro_doc' => $request->nro_doc];
        }
        return view('cliente.cliagregar',compact('id_new_prov','info_cli','agregar','dep','tipo_documentos','modulos'));
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
                if(!is_null($result)){
                    $info_ubigeo['prov_sa'] = (new Ubigeo)->getProvinciasCliente($result->dpto_sa);
                    $info_ubigeo['dist_sa'] = (new Ubigeo)->getDistritosCliente($result->prov_sa);
                    $info_ubigeo['prov_so'] = (new Ubigeo)->getProvinciasCliente($result->dpto_so);
                    $info_ubigeo['dist_so'] = (new Ubigeo)->getDistritosCliente($result->prov_so);
                    $proveedores = ModelsClientes::obtenerProveedores($result->id);
                }
                $message = is_null($result) ? ['info' => 'No se encontró el cliente'] : ['cliente' => $result,'ubigeos' => $info_ubigeo,'proveedores' => $proveedores];
                return response()->json($message);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function misClientes()
    {
        $modulos = User::getModulos(Auth::id());
        return view('cliente.misclientes',compact('modulos'));
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
        $modulos = User::getModulos(Auth::id());
        return view('cliente.clidocumento',compact('modulos', 'clientes'));
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
            $datos = $request->all();
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
            ModelsClientes::addOrUpdateProveedores(Auth::id(), $proveedorAdd, $proveedorEdid,$id_cli);
            return response()->json(['message' => $msg, 'action' => $agregar]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }
}
