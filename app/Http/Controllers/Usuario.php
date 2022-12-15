<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Clientes;
use App\Models\Rol;
use App\Models\Tipos\Documentos;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Usuario extends Controller
{
    public function viewHome()
    {
        $modulos = User::getModulos(Auth::id());
        $rol_user = User::obtenerRolActivo(Auth::id());
        switch ($rol_user->id_rol) {
            case '1':
                $datos = [
                    'totalrecojo' => User::obtenerDatosHome(null, 33),
                    'recojoconcretado' => User::obtenerDatosHome(null, 34),
                    'totalembarque' => User::obtenerDatosHome(null, 35),
                    'embarqueconcretado' => User::obtenerDatosHome(null, 36),
                    'totalentregas' => User::obtenerDatosHome(null, 37),
                    'entregasconcretadas' => User::obtenerDatosHome(null, 38)
                ];
            break;
            case '2':
                $datos = [
                    'recojodeldia' => User::obtenerDatosHome(null, 24),
                    'recoprogramado' => User::obtenerDatosHome(null, 25),
                    'recoefectuado' => User::obtenerDatosHome(null, 26),
                    'manipendienteguia' => User::obtenerDatosHome(null, 2),
                    'guiahoymanifestar' => User::obtenerDatosHome(null, 27),
                    'manifiestoembarcar' => User::obtenerDatosHome(null, 3),
                    'embarquependiente' => User::obtenerDatosHome(null, 28),
                    'embarqueefectuados' => User::obtenerDatosHome(null, 29),
                    'embarqueporinformar' => User::obtenerDatosHome(null, 30),
                ];
            break;
            case '3':
                $datos = [
                    'guiasembarquependiente' => User::obtenerDatosHome(null, 17),
                    'guiasmanifestar' => User::obtenerDatosHome(Auth::id(), 18),
                    'maniembarque' => User::obtenerDatosHome(Auth::id(), 19)
                ];
            break;
            case '4':
                $datos = [
                    'recojodeldia' => User::obtenerDatosHome(null, 24),
                    'recoprogramado' => User::obtenerDatosHome(null, 25),
                    'recoefectuado' => User::obtenerDatosHome(null, 26),
                    'manipendienteguia' => User::obtenerDatosHome(null, 2),
                    'guiahoymanifestar' => User::obtenerDatosHome(null, 27),
                    'manifiestoembarcar' => User::obtenerDatosHome(null, 3),
                    'embarquependiente' => User::obtenerDatosHome(null, 28),
                    'embarqueefectuados' => User::obtenerDatosHome(null, 29),
                    'embarqueporinformar' => User::obtenerDatosHome(null, 30),
                    'guiaporconfirmar' => User::obtenerDatosHome(null, 21),
                    'guiasenreparto' => User::obtenerDatosHome(null, 22),
                    'guiasentregadas' => User::obtenerDatosHome(null, 31),
                    'guiasretornar' => User::obtenerDatosHome(null, 23),
                ];
                break;
            case '5':
                $datos = [
                    'solicitudpendiente' => User::obtenerDatosHome(null, 20),
                    'guiaporconfirmar' => User::obtenerDatosHome(null, 21),
                    'guiasenreparto' => User::obtenerDatosHome(null, 22),
                    'guiasentregadas' => User::obtenerDatosHome(null, 31),
                    'guiasretornar' => User::obtenerDatosHome(null, 23),
                ];
                break;
            case '6':
                $datos = [
                    'rhoy' => User::obtenerDatosHome(Auth::id(), 5),
                    'recojostotal' => User::obtenerDatosHome(Auth::id(), 8),
                    'ehoy' => User::obtenerDatosHome(Auth::id(), 9),
                    'embarquetotal' => User::obtenerDatosHome(Auth::id(), 11),
                    'informespendientes' => User::obtenerDatosHome(Auth::id(), 13)
                ];
                break;
            case '7':
                $datos = [
                    'rhoy' => User::obtenerDatosHome(Auth::id(), 6),
                    'recojostotal' => User::obtenerDatosHome(Auth::id(), 7),
                    'ehoy' => User::obtenerDatosHome(Auth::id(), 10),
                    'embarquetotal' => User::obtenerDatosHome(Auth::id(), 12),
                    'informespendientes' => User::obtenerDatosHome(Auth::id(), 13)
                ];
                break;
            case '8':
                $datos = [
                    'arriboconfirmar' => User::obtenerDatosHome(Auth::id(), 14),
                    'entregaspendientes' => User::obtenerDatosHome(Auth::id(), 15),
                    'cargosretornar' => User::obtenerDatosHome(Auth::id(), 16),
                    'guiasentregadas' => User::obtenerDatosHome(Auth::id(), 32),
                ];
                break;
            default:
                $datos = [];
            break;
        }
        return view('users.home',compact('modulos', 'datos', 'rol_user'));
    }
    public function getCentroCosto(Request $request)
    {
        if($request->ajax()){
            $cc = User::getCentroCostoCliente($request->cliente);
            return response()->json(['success' => $cc]);
        }
    }
    public function viewLista()
    {
        $modulos = User::getModulos(Auth::id());
        $year = date('Y');
        $tipo_documentos = Documentos::where('estado', '1')->get();
        $areas = Area::get();
        $roles = Rol::get();
        $clientes = Clientes::getClientesFiles();
        return view('users.lista',compact('modulos','year', 'tipo_documentos', 'areas', 'roles', 'clientes'));
    }
    public function miPerfilModificar(Request $request)
    {
        try {
            if(!$request->ajax()){
                return response()->json(['error' => 'Consulta invalida']);
            }
            switch ($request->tipo) {
                case 'obtener':
                    return response()->json(['usuario' => User::obtenerUnUsuario(Auth::id())]);
                break;
                case 'actualizar':
                    $datos = $request->all();
                    if(isset($datos['tipo'])){
                        unset($datos['tipo']);
                    }
                    if (isset($datos['id_usuario'])) {
                        unset($datos['id_usuario']);
                    }
                    User::where(['id' => $request->id_usuario])->update($datos);
                    return response()->json(['success' => 'datos actualizados de forma correcta']);
                break;
            }
        } catch (Exception $ex) {
           return response()->json(['error' => $ex->getMessage()]);
        }
    }
    public function miPerfil(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        $tipo_documentos = Documentos::where('estado', '1')->get();
        return view('users.miperfil', compact('modulos', 'tipo_documentos'));
    }
    public function obtenerRoles(Request $request)
    {
        try {
            if ($request->ajax() && Auth::check()) {
                return response()->json(['roles' => User::getRoles(Auth::id()), 'area' => User::miArea(Auth::id())]);
            }
            return response()->json(['error' => 'No se encontró al usuario']);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
        
    }
    public function cerrarSesion(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('login'));
    }
    public function cambiarRol($id)
    {
        $cambioRol = User::cambiarRol(Auth::id(),$id);
        if(!$cambioRol){
            return abort(404,'Este rol no se a asignado a tú usuario');
        }
        
        return redirect(route('home'));
        
    }
    public function modificarUsuarios(Request $request)
    {
        try {
            if(!$request->ajax()){
                return ;
            }
            $message = ['error' => 'no se encontró el proceso'];
            switch ($request->accion) {
                case 'eliminar':
                    User::where(['id' => $request->id_usuario])->update(['status' => 0]);
                    $message = ['success' => 'usuario eliminado'];
                break;
                case 'mostrar_editar':
                    $message = User::obtenerDatosEditar($request->id_usuario);
                break;
                case 'mostrar_roles':
                    $message = ['success' => ['usuario' => $request->id_usuario, 'roles' => Rol::obtenerRolesUsuario($request->id_usuario)]];
                break;
                case 'actualizar':
                    $datos = $request->all();
                    if(isset($datos['id_usuario'])){
                        unset($datos['id_usuario']);
                    }
                    if (isset($datos['accion'])) {
                        unset($datos['accion']);
                    }
                    User::where(['id' => $request->id_usuario])->update($datos);
                    $message = ['success' => 'usuario modificado correctamente'];
                break;
                case 'restaurar' :
                    User::where(['id' => $request->id_usuario])->update(['password' => Hash::make($request->password),'status' => 2]);
                    $message = ['success' => 'Contraseña restaurada, recuerda que la contraseña temporal es: <strong>' . $request->password . '</strong>'];
                break;
                case 'actualizar_rol':
                    $message = Rol::modificarUsuarioRol($request->id_usuario,json_decode($request->roles,true),$request->has('elimiarRol') ? true : false);
                break;
            }
            return response()->json($message);
        } catch (Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }
    
    public function viewCrearModificar()
    {
        $tipo_documentos = Documentos::where('estado','1')->get();
        $areas = Area::get();
        $roles = Rol::get();
        $clientes = Clientes::getClientesFiles();
        $modulos = User::getModulos(Auth::id());
        return view('users.crear',compact('clientes','tipo_documentos','areas','roles','modulos'));
    }
    public function restablecerUsuario(Request $request)
    {
        if(!isset($_COOKIE['login_first'])){
            return response()->json(['not_cookie' => true]);
        }
        $user = User::select("password","id")->where(['email' => $_COOKIE['login_first'], 'status' => 2])->first();
        if (!empty($user)) {
            if (Hash::check($request->password_temp, $user->password)) {
                User::where(['id' => $user->id])->update(['password' => Hash::make($request->password), 'status' => 1]);
                Auth::attempt(['email' => $_COOKIE['login_first'], 'password' => $request->password, 'status' => 1], true);
                if(isset($_COOKIE['login_first'])){
                    unset($_COOKIE['login_first']);
                    setcookie("login_first", null, -1 ,"/");
                }
                User::defaultSelectRol($user->id);
                $request->session()->regenerate();
                return response()->json(['success' => true]);
            }else{
                return response()->json(['error' => 'Contraseña temporal incorrecta']);
            }
        }
        return response()->json(['error' => 'No se procedio a encontrar el usuario']);
    }
    public function restablecerUsuarioCancelar(Request $request)
    {
        if (isset($_COOKIE['login_first'])) {
            unset($_COOKIE['login_first']);
            setcookie("login_first",null,-1,"/");
        }
        return response()->json(['success' => true]);
    }
    public function autenticacion(Request $request)
    {
        if($request->ajax()){
            $user = User::select("password")->where(['email' => $request->email,'status' => 2])->first();
            if(!empty($user)){
                if(Hash::check($request->password,$user->password)){
                    // dd('sii');
                    setcookie('login_first', $request->email,time() + 3600, '/');
                    $message = ['success' => true];
                    return response()->json($message);
                }
            }
            $credenciales = $request->only('email','password');
            $credenciales['status'] = 1;

            $remember = $request->has('remember');
            $message = [];
            if(Auth::attempt($credenciales,$remember)){
                $request->session()->regenerate();
                User::defaultSelectRol(Auth::id());
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
        // dd($_COOKIE);
        if(isset($_COOKIE['login_first'])){
            return view('users.restore');
        }
        return view('users.login');
    }
    public function agregar(Request $request)
    {
        try {
            if ($request->ajax()) {
                $datos = $request->all();
                $repetidos = User::where(['email' => $request->email])->count();
                if ($repetidos > 0) {
                    return response()->json(['alerta' => 'El correo ' . $request->email . ' ya se encuentra registrado, por favor intente con otro correo']);
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
