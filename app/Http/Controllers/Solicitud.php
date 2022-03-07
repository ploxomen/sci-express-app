<?php

namespace App\Http\Controllers;

use App\Models\Clientes;
use App\Models\EstadoSolicitud;
use App\Models\ModeloSolicitud;
use App\Models\Rol;
use Illuminate\Http\Request;
use App\Models\Tipos\Documentos;
use App\Models\TrayectoEnvio;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;

class Solicitud extends Controller
{
    public function viewSolicitud()
    {
        $tipo_documentos = Documentos::where('estado','1')->get();
        $modulos = User::getModulos(Auth::id());
        $hoy = date('Y-m-d');
        $trayectos = TrayectoEnvio::where(['estado' => 1])->get();
        $clientes = Clientes::getClientesFiles();
        $ultimo_id = ModeloSolicitud::getLastId();
        $estados_solicitud = EstadoSolicitud::where(['activado' => 1])->get();
        $dep = (new Ubigeo)->getDepartamentosClientes();
        if(is_null($ultimo_id)){
            $ultimo_id = 1;
        }else{
            $ultimo_id = $ultimo_id->id + 1;
        }
        return view('solicitud',compact('dep','estados_solicitud','ultimo_id','clientes','tipo_documentos','modulos','hoy','trayectos'));
    }
    public function obtenerSolicitud(Request $request)
    {
        try {
            if ($request->ajax()) {
                $detalles = ModeloSolicitud::getSolicitud($request->estado,$request->nro_doc);
                $estados_solicitud = EstadoSolicitud::where(['activado' => 1])->get();
                return response()->json(['solicitudes' => $detalles,'estados' => $estados_solicitud]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function obtenerDetalleCli(Request $request)
    {
        try {
            if($request->ajax()){
                $detalles = Clientes::getDetalleSoliCli($request->nro_doc);
                $provincias = Ubigeo::getProvinciasCliente($detalles->dpto_sa);
                $distritos = Ubigeo::getDistritosCliente($detalles->prov_sa);
                return response()->json(['cliente' => $detalles,'provincias' => $provincias,'distritos' => $distritos]);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function insertarNuevaSoli(Request $request)
    {
        try {
            if ($request->ajax()) {
                ModeloSolicitud::insert($request->all());
                return response()->json(['success' => 'Solicitud registrada correctamente']);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
   
}
