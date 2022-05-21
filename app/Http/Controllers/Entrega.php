<?php

namespace App\Http\Controllers;

use App\Models\EmpresaTransporte;
use App\Models\ModeloCluster;
use App\Models\ModeloEmbarque;
use App\Models\ModeloGuias;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Entrega extends Controller
{
   public function viewEntrega()
   {
        $modulos = User::getModulos(Auth::id());
        $clusters = ModeloCluster::obtenerTodosClusters();
        $nro_pendients = User::obtenerDatosHome(null,22);
        return view('pedidos.entregas',compact('modulos','clusters', 'nro_pendients'));
   }
   public function actualizarEntrega(Request $request)
   {
      if($request->ajax()){
         try {
            $cluster = $request->id_cluster;
            $id_guia = $request->id_guia;
            $datos = $request->all();
            if($request->has('id_cluster')){
               unset($datos['id_cluster']);
            }
            if ($request->has('id_guia')) {
               unset($datos['id_guia']);
            }
            return response()->json(['success' => ModeloGuias::actualizarEntrega($cluster,$id_guia,$datos)]);
         } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
         }
      }
   }
   public function listaEntregas(Request $request)
   {
      if($request->ajax()){
         switch ($request->tipo) {
            case 'consulta':
               $fecha_confirmacion = $request->fecha_confirmacion == null ? "" : $request->fecha_confirmacion;
               $fecha_reparto = $request->fecha_reparto == null ? "" : $request->fecha_reparto;
               $cluster = $request->has('nombre_cluster') ? $request->nombre_cluster : 0;
               if(!$request->has('nombre_cluster')){
                  $cluster = (ModeloCluster::getCodigoCluster(Auth::id()))->id;
               }
               $ciudad_final = $request->has('ciudad_final') ? $request->ciudad_final : "";
               $tipo_ciudad = $request->has('tipo_ciudad') ? $request->tipo_ciudad : "";
               $query = ModeloEmbarque::obtenerGuiasRepartoConfirmado($fecha_confirmacion, $fecha_reparto, $request->estado_guia, $cluster,$ciudad_final,$tipo_ciudad);
               return datatables()->of($query)->toJson();               
            break;
            case 'kpi':
               $kpi = ModeloEmbarque::calculoKpi($request->id_cluster);
               return response()->json(['success' => $kpi]);
            break;
            case 'consulta-detalle':
               $id_cluster = $request->cluster;
               $id_guia = $request->guia;
               return response()->json(ModeloEmbarque::obtenerDetalleGuiaEditarEmbarque($id_guia,$id_cluster));
            break;
         }
      }
   }
   public function entregaGuias(Request $request)
   {
      $modulos = User::getModulos(Auth::id());
      $guias = ModeloEmbarque::entregaGuias(Auth::id());
      return view('pedidos.entregarguias', compact('modulos','guias'));
   }
   public function devolverGuias()
   {
      $modulos = User::getModulos(Auth::id());
      $transportes = EmpresaTransporte::obtenerTransportes();
      $hoy = date('Y-m-d');
      return view('pedidos.devolverguias', compact('modulos', 'transportes','hoy'));
   }
   public function obtenerImg($filename)
   {
      $path = storage_path('app/entrega-guias/' . $filename);
      if(File::exists($path)){
         $file = File::get($path);
         $type = File::mimeType($path);
         $response = response()->make($file);
         $response->header("Content-Type",$type);
         return $response;
      }
   }
   public function agregarPedidosEntregados(Request $request)
   {
      try {
         $id_file = time();
         $filesDb = [];
         if(!is_null($request->file('img-guias'))){
            foreach ($request->file('img-guias') as $file) {
               $id_file++;
               $nombre = $id_file . '_' . $file->getClientOriginalName();
               Storage::disk('guias-img')->put($nombre, File::get($file));
               $filesDb[] = [
                  'id_guia' => $request->guia,
                  'nom_file' => $file->getClientOriginalName(),
                  'nom_id_file' => $nombre,
                  'size' => $file->getSize(),
                  'type' => $file->extension()
               ];
            }
         }
         ModeloEmbarque::confirmarEntregaGuias($request->guia,json_decode($request->datos_adicionales,true),Auth::id(),$filesDb);
         return response(['success' => 'La Guía N° <b>' . $request->nro_guia .'</b> modificada con exito']);
      } catch (Exception $ex) {
         return response()->json(['error' => $ex->getMessage()]);
      }
   }
}
