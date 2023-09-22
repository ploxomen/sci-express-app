<?php

namespace App\Http\Controllers;

use App\Exports\Reportes;
use App\Models\Clientes;
use App\Models\EmpresaTransporte;
use App\Models\InformacionAdicional;
use App\Models\ModeloCluster;
use App\Models\ModeloEmbarque;
use App\Models\ModeloGuias;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class Entrega extends Controller
{
   public function viewEntrega()
   {
      $modulos = User::getModulos(Auth::id());
      $clusters = ModeloCluster::obtenerTodosClusters();
      $nro_pendients = User::obtenerDatosHome(null,22);
      $fechaFiltros = InformacionAdicional::obtenerDiasFiltros();
      $clientes = Clientes::obtenerCliConProvee();
      return view('pedidos.entregas',compact('modulos','clusters', 'nro_pendients', 'fechaFiltros', 'clientes'));
   }
   public function reporteEntregas($tipo, $fechaInicio, $fechaFin, $cliente,$cluster)
   {
      ini_set('memory_limit', '1024M');
      $cliente = $cliente == "todos" ? 0 : intval($cliente);
      $cluster = $cluster == "todos" ? 0 : intval($cluster);
      switch ($tipo) {
         case 'pdf':
            
            $datos = ModeloEmbarque::reporteEntregas($fechaInicio, $fechaFin, $cliente, $cluster);
            $reporte = Pdf::loadView("pedidos.reportes.entregasPdf", ["resultados" => $datos, 'fechaIni' => date('d/m/Y', strtotime($fechaInicio)), 'fechaFi' => date('d/m/Y', strtotime($fechaFin))])->setPaper('a4', 'landscape');
            return $reporte->download("entregas_" . $fechaInicio . "_" . $fechaFin . ".pdf");
            break;
         case 'excel':
            $datos = ModeloEmbarque::reporteEntregas($fechaInicio, $fechaFin, $cliente, $cluster);
            return Excel::download(new Reportes('pedidos.reportes.entregasExcel', $datos, "LISTA DE ENTREGAS DESDE" . date('d/m/Y', strtotime($fechaInicio)) . " HASTA " . date('d/m/Y', strtotime($fechaFin))), "entregas_" . $fechaInicio . "_" . $fechaFin . ".xls");
            break;
      }
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
               $fechaInicoReparto = empty($request->fechaIncioReparto) ? "" : $request->fechaIncioReparto;
               $fechaFinReparto = empty($request->fechaFinReparto) ? "" : $request->fechaFinReparto;
               $cluster = $request->has('nombre_cluster') ? $request->nombre_cluster : 0;
               if(!$request->has('nombre_cluster')){
                  $cluster = (ModeloCluster::getCodigoCluster(Auth::id()))->id;
               }
               $ciudad_final = $request->has('ciudad_final') ? $request->ciudad_final : "";
               $tipo_ciudad = $request->has('tipo_ciudad') ? $request->tipo_ciudad : "";
               $query = ModeloEmbarque::obtenerGuiasRepartoConfirmado($fechaInicoReparto, $fechaFinReparto, $request->estado_guia, $cluster,$ciudad_final,$tipo_ciudad);
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
         $datos = json_decode($request->datos_adicionales, true);
         $datalleNroVisitas = [];
         if($datos['estado_entregado'] == "NO ENTREGADO"){
            $datalleNroVisitas = [
               'id_guia' => $request->guia,
               'visita' => $datos['nro_visitas_reparto'],
               'motivo' => $datos['motivo_no_ent']
            ];
            switch ($datos['nro_visitas_reparto']) {
               case 'PRIMERA':
                  $datos['nro_visitas_reparto'] = 'SEGUNDA';
               break;
               case 'SEGUNDA':
                  $datos['nro_visitas_reparto'] = 'TERCERA';
               break;
               case 'TERCERA':
                  $datos['nro_visitas_reparto'] = 'RETORNO';
               break;
            }
         }
         ModeloEmbarque::confirmarEntregaGuias($request->guia,$datos,Auth::id(),$filesDb, $datalleNroVisitas);
         return response(['success' => 'La Guía N° <b>' . $request->nro_guia .'</b> modificada con exito']);
      } catch (Exception $ex) {
         return response()->json(['error' => $ex->getMessage()]);
      }
   }
}
