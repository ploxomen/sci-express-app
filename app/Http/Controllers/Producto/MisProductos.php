<?php

namespace App\Http\Controllers\Producto;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Productos;
use App\Models\Rol;
use App\Models\Unidades;
use Exception;
use Illuminate\Http\Request;

class MisProductos extends Controller
{
    public function agregarProducto()
    {
        $categorias = Categoria::get();
        $marcas = Marca::obtenerMarcas();
        $unidades = Unidades::obtenerUnidades();
        return view('intranet.productos.proagregar',compact('categorias','marcas','unidades'));
    }
    public function addProduct(Request $request)
    {
        if($request->ajax()){
            // $files = $request->file('productos-img');
            dd($request->all());
        }
    }
    public function obtenerProductos(Request $request)
    {
        if($request->ajax()){
            try {
                
               
            } catch (Exception $ex) {
                
            }
        }
    }
    public function listaProductos()
    {
        $totalProductos = Productos::totalProductos();
        return view('intranet.productos.prolistar',compact('totalProductos'));
    }
}
