<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Rol;
use App\Models\Tipos\Documentos;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Usuario extends Controller
{
    public function viewHome()
    {
        $modulos = User::getModulos(Auth::id());
        return view('users.home',compact('modulos'));
    }
    public function viewLista()
    {
        $modulos = User::getModulos(Auth::id());
        return view('users.lista',compact('modulos'));
    }
    public function cerrarSesion(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('login'));
    }
    
    public function viewCrearModificar()
    {
        $tipo_documentos = Documentos::where('estado','1')->get();
        $areas = Area::get();
        $roles = Rol::get();
        $modulos = User::getModulos(Auth::id());
        return view('users.crear',compact('tipo_documentos','areas','roles','modulos'));
    }
    public function autenticacion(Request $request)
    {
        if($request->ajax()){
            $credenciales = $request->only('n_document','password');
            $remember = $request->has('remember');
            $message = [];
            if(Auth::attempt($credenciales,$remember)){
                $request->session()->regenerate();
                $message = ['success' => true];
            }else{
                $message = ['not_user' => true];
            }
            return response()->json($message);
        }
    }
    public function misUsuarios()
    {
        try {
            $misUsuarios = User::misUsuarios(Auth::id());
            return datatables()->of($misUsuarios)->toJson();
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    } 
    public function viewLogin()
    {
        return view('users.login');
    }
    public function agregar(Request $request)
    {
        try {
            if ($request->ajax()) {
                $datos = $request->all();
                $repetidos = User::where(['type_document' => $datos['type_document'], 'n_document' => $datos['n_document']])->count();
                if ($repetidos > 0) {
                    return response()->json(['alerta' => 'Se encontró ' . $repetidos . ' coincidencia en el tipo y número de documento, para continuar por favor cambie el tipo o número de documento']);
                }
                $datos['password'] = Hash::make($datos['password']);
                $result = User::addUser($datos);
                return response()->json($result);
            }
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function getArea(Request $request)
    {
        try {
            $result = Rol::getRolArea($request->post('id_area'));
            return response()->json(['success' => $result]);
            
        } catch (\Throwable $th) {
           return response()->json(['error'=>$th->getMessage()]);
        }
    }
    public function listarUsuarios()
    {
        return view('intranet.users.lista');
    }
}
