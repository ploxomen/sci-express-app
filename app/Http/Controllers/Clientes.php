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
        if($request->has('nro_doc')){
            $agregar = false;
            $info_cli = ['nro_doc' => $request->nro_doc];
        }
        return view('cliente.cliagregar',compact('info_cli','agregar','dep','tipo_documentos','modulos'));
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
                if(!is_null($result)){
                    $info_ubigeo['prov_sa'] = (new Ubigeo)->getProvinciasCliente($result->dpto_sa);
                    $info_ubigeo['dist_sa'] = (new Ubigeo)->getDistritosCliente($result->prov_sa);
                    $info_ubigeo['prov_so'] = (new Ubigeo)->getProvinciasCliente($result->dpto_so);
                    $info_ubigeo['dist_so'] = (new Ubigeo)->getDistritosCliente($result->prov_so);
                }
                $message = is_null($result) ? ['info' => 'No se encontró el cliente'] : ['cliente' => $result,'ubigeos' => $info_ubigeo];
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
            $datos = $request->all();
            foreach ($datos as $k => $v) {
                $datos[$k] = is_null($v) ? "" : $v;
            }
            $tipo = $request->tipo;
            $limites = ['i' => $request->has('inicio') ? $request->inicio : 0, 'f' => $request->has('fin') ? $request->fin : 0];
            // dd($datos, $tipo, $limites['i'], $limites['f']);
            $result = ModelsClientes::obtenerCli($datos, $tipo,$limites['i'],$limites['f']);
            return response()->json($result);
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
            if ($request->has('id_cliente')) {
                unset($datos['id_cliente']);
                $datos['aprob_file'] = $request->has('aprob_file') ? 1 : 0;
                $datos['aprob_cred'] = $request->has('aprob_cred') ? 1 : 0;
                $datos['alta_clien'] = $request->has('alta_clien') ? 1 : 0;
                $datos['estado_doc'] = $request->has('aprob_file') && $request->has('aprob_cred') && $request->has('alta_clien') ? 1 : 0;
                $datos['last_user_update'] = Auth::id();
                $datos['last_date_update'] = date('Y-m-d H:i:s');
                ModelsClientes::where(['id' => $request->id_cliente])->update($datos);
                $msg = 'Cliente modificado correctamente';
                $agregar = false;
            }else{
                $datos['user_create'] = Auth::id();
                ModelsClientes::insert($datos);
                $msg = 'Cliente agregado correctamente';
            }
            return response()->json(['message' => $msg, 'action' => $agregar]);
        } catch (Exception $ex) {
            return response()->json(['message' => $ex->getMessage()]);
        }
    }
}
