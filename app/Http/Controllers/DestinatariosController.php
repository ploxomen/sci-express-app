<?php

namespace App\Http\Controllers;

use App\Models\Destinatarios;
use App\Models\Tipos\Documentos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DestinatariosController extends Controller
{
    private $attributes = [
        'tipo_doc' => 'tipo de documento',
        'nro_doc' => 'número de documento',
        'nombres' => 'nombres',
        'id_ciudad_final' => 'ciudad final'
    ];
    private $rules = [
        'tipo_doc' => 'required',
        'nro_doc' => 'required',
        'nombres' => 'required',
        'direccion' => 'required',
        'departamento' => 'required|numeric',
        'provincia' => 'required|numeric',
        'distrito' => 'required|numeric',
    ];
    public function index() {
        return view('destinatario.administrador');
    }
    public function list(){
        $destinatarios = Destinatarios::documentos()->select(['clientes_de_clientes.id','tipo','nro_doc','nombres','direccion','distrito','activo'])->get();
        return datatables()->of($destinatarios)->toJson();
    }
    public function store(Request $request)
    {
        $datos = $request->except('_token');
        $validator = Validator::make($datos, $this->rules);
        $validator->setAttributeNames($this->attributes);
        
        $validator->validate();
        $destinatario = new Destinatarios();
        $destinatario->fill($datos);
        $destinatario->activo = 1;
        $destinatario->save();
        return response()->json(['success' => 'destinatario agregado correctamente']);
    }
    public function show(Destinatarios $destinatario)
    {
        $exists = $destinatario->exists;
        $ubigeo = new Ubigeo();
        $tipo_documentos = Documentos::where('estado','1')->get();
        $agregar = !$exists;
        $departamentos = $ubigeo->getDepartamentosClientes();
        $provincias = $exists ? $ubigeo->getProvinciasCliente($destinatario->departamento) : [];
        $distritos = $exists ? $ubigeo->getDistritosCliente($destinatario->provincia) : [];
        $caserios = $exists ? $ubigeo->getCiudadesClientes($destinatario->distrito) : [];
        return view("destinatario.crear",compact('departamentos','tipo_documentos','agregar','destinatario','provincias','distritos','caserios'));
    }
    public function update(Request $request, Destinatarios $destinatario)
    {
        $validator = Validator::make($request->all(), $this->rules);
        $validator->setAttributeNames($this->attributes);
        $validator->validate();
        $destinatario->fill($request->all());
        $destinatario->save();
        return response()->json(['success' => 'destinatario actualizado correctamente']);
    }
    public function drop(Destinatarios $destinatario)
    {
        $destinatario->activo = 0;
        $destinatario->save();
        return response()->json(['success' => 'destinatario eliminado correctamente']);
    }
}
