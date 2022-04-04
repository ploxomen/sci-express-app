<?php

namespace App\Http\Controllers;

use App\Models\EmpresaTransporte;
use App\Models\ModeloEmbarque;
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
        return view('pedidos.entregas',compact('modulos'));
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
   public function agregarPedidosEntregados(Request $request)
   {
      try {
         $id_file = time();
         $filesDb = [];
         foreach ($request->file('img-guias') as $file) {
            $id_file++;
            $nombre = $id_file . '_' . $file->getClientOriginalName();
            Storage::disk('guias-img')->put($nombre,File::get($file));
            $filesDb[] = [
               'id_guia' => $request->guia,
               'nom_file' => $file->getClientOriginalName(),
               'nom_id_file' => $nombre,
               'size' => $file->getSize(),
               'type' => $file->extension()
            ];
         }
         ModeloEmbarque::confirmarEntregaGuias($request->guia,Auth::id(),$filesDb);
         return response(['success' => 'La Guia N° - <b>' . str_pad($request->guia,5,'0',STR_PAD_LEFT) .'</b> modificada con exito']);
      } catch (Exception $ex) {
         return response()->json(['error' => $ex->getMessage()]);
      }
   }
}
