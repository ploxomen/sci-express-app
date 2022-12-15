<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Clientes as ModelsClientes;

class ModeloSolicitud extends Model
{
    protected $table = 'solicitud';
    public $timestamps = false;
    
    protected $fillable = ['last_modificacion','last_user','confirmacion_solicitud'];

    public static function getLastId()
    {
        return ModeloSolicitud::select('id')->latest('id')->first();
    }
    public static function obtenerTotalPorProcesar()
    {
        return ModeloSolicitud::where(['usuario_responsable' => null, 'activo' => 1, 'estado' => 1])->count();
    }
    public static function getSolicitud($estado,$id_user = 0,$fechaInico,$fechaFin)
    {
        $solicitudes = DB::select('CALL sp_obtener_solicitudes(?,?,?,?)',[$estado, $id_user,$fechaInico,$fechaFin]);
        if(!empty($solicitudes)){
            $estados_solicitud = EstadoSolicitud::where(['activado' => 1])->get();
            $usuarios = User::obtenerUsuarioRol(3);
            foreach ($solicitudes as $soli) {
                $soli->listSoli = $estados_solicitud;
                $soli->listUsu = $usuarios;
            }
        }
        return $solicitudes;
    }
    public static function getFechaFiltroSolicitud()
    {
        return DB::table('solicitud')->selectRaw("DATE_FORMAT(dia_recojo,'%d/%m/%Y') AS fecha,count(*) AS total")->where(['activo' => 1])->whereIn('estado',[1,6])->groupBy('dia_recojo')->get();
    }
    public static function getSolicitudesProgramar($diaRecojo,$trayecto,$idUsuario)
    {
        $solicitudes = DB::select('CALL sp_obtener_solicitudes_programar(?,?)',[$diaRecojo, $trayecto]);
        foreach ($solicitudes as $solicitud) {
            $solicitud->checked = $idUsuario === $solicitud->usuario_responsable ? true : false;
            $solicitud->depart_cli_recojo = (new Ubigeo)->getNameDepartamento($solicitud->depart_cli_recojo);
            $solicitud->ciudad_cli_recojo = (new Ubigeo)->getNameProvincia($solicitud->ciudad_cli_recojo);
            $solicitud->distrito_cli_recojo = (new Ubigeo)->getNameDistrito($solicitud->distrito_cli_recojo);
        }
        return $solicitudes;
    }
    public static function editarSolicitud($datos,$idUsuario)
    {
        DB::beginTransaction();
        try {
            if(isset($datos['agregarDetalles'])){
                DB::table('solicitud_detalle')->insert($datos['agregarDetalles']);
                unset($datos['agregarDetalles']);
            }
            if (isset($datos['editarDetalles'])) {
                foreach ($datos['editarDetalles'] as $key => $detalle) {
                    $idDetalle = $detalle['id'];
                    unset($datos['editarDetalles'][$key]['id']);
                    DB::table('solicitud_detalle')->where('id',$idDetalle)->update($detalle);
                }
                unset($datos['editarDetalles']);
            }
            if(isset($datos['nuevo_proveedor']) && $datos['nuevo_proveedor'] == 'true'){
                $idProveedor = DB::table('proveedor_clientes')->insertGetId([
                    'tipo_docu_prov' => $datos['doc_iden_soli'],
                    'nro_docu_prov' => $datos['nro_doc_soli'],
                    'nomb_prov' => $datos['id_soli'],
                    'depa_prov' => $datos['depart_cli_recojo'],
                    'prov_prov' => $datos['ciudad_cli_recojo'],
                    'dist_prov' => $datos['distrito_cli_recojo'],
                    'dire_prov' => $datos['direccion_recojo']
                ]);
                DB::table('relacion_cliente_proveedor')->insert(['id_cliente' => $datos['id_cliente'],'id_proveedor' => $idProveedor]);
                $datos['id_soli'] = $idProveedor;
            }
            $cuenta = Clientes::comprobarNroCuenta($datos['id_cliente'], $datos['id_invitado'], $datos['cuenta_invitado']);
            if (!is_null($cuenta)) {
                DB::table('usuario_cliente')->where(['cod_usuario' =>$datos['id_invitado'], 'cod_cliente' => $datos['id_cliente']])->update(['cuenta' => $cuenta]);
            }
            unset($datos['nuevo_proveedor']);
            $idSolicitud = $datos['id_solicitud'];
            unset($datos['id_solicitud']);
            $datos['last_user'] = $idUsuario;
            $datos['last_modificacion'] = now();
            DB::table('solicitud')->where('id',$idSolicitud)->update($datos);
            DB::commit();
            return ['success' => 'solicitud modificada correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function eliminarDetalle($idDetalle)
    {
        $solicitud = DB::table('solicitud_detalle AS ds')->select('id_solicitud')->where('id',$idDetalle)->first();
        $cantidadDetalle = DB::table('solicitud_detalle')->where('id_solicitud',$solicitud->id_solicitud)->count();
        if($cantidadDetalle === 1){
            return ['error' => 'la solicitud debe tener como mínimo un detalle'];
        }
        DB::table('solicitud_detalle')->where('id',$idDetalle)->delete();
        return ['success' => 'el detalle de la solicitud se eliminó correctamente'];
    }
    public static function obtenerDetalleSoli($id)
    {
        $result = DB::select('CALL sp_obtener_detalle_solicitud(?)',[$id]);
        if(isset($result[0])){
            $result[0]->lista_provincia_recojo = (new Ubigeo)->getProvinciasCliente($result[0]->depart_cli_recojo);
            $result[0]->lista_distrito_recojo = (new Ubigeo)->getDistritosCliente($result[0]->ciudad_cli_recojo);
            $result[0]->lista_proveedores = ModelsClientes::obtenerProveedores($result[0]->id_cliente);
            if(!empty($result[0]->id_cliente)){
                $result[0]->lista_invitados = ModelsClientes::obtenerInvitados($result[0]->id_cliente);
                if(!empty($result[0]->id_invitado)){
                    $result[0]->lista_invitado_cc = ModelsClientes::centrosCosto($result[0]->id_invitado, $result[0]->id_cliente);
                }
            }
            $result[0]->lista_detalle_envio = DB::table('solicitud_detalle')->select('id','departamento','provincia','distrito','sobres','cajas','paquetes','otros','detalle_otros')->where('id_solicitud',$result[0]->id)->get();
            if($result[0]->lista_detalle_envio->count() > 0){
                foreach($result[0]->lista_detalle_envio as $kdetalle => $detale){
                    $result[0]->lista_detalle_envio[$kdetalle]->lista_provincias = (new Ubigeo)->getProvinciasCliente($detale->departamento);
                    $result[0]->lista_detalle_envio[$kdetalle]->lista_distritos = (new Ubigeo)->getDistritosCliente($detale->provincia);
                }
            }
        }
        return isset($result[0]) ? ['success' => $result[0]] : ['info' => 'No se encontró los detalles de esta solicitud'];
    }
    public static function asignarPersonalSoli($datos,$usuario)
    {
        $idSolicitud = $datos['id_solicitud'];
        DB::table('solicitud')->where(['id' => $idSolicitud])->update(['usuario_responsable' => $datos['id_user'],'last_modificacion' => now(),'last_user' => $usuario]);
        return 'responsable asignado correctamente';
    }
    public static function visualizarNotificaciones()
    {
        return ModeloSolicitud::selectRaw("solicitud.id,dia_recojo,date_format(hr_recojo_estimado,'%H:%i') AS hr_recojo,name,last_name,phone")->where(['responsable' => 'INVITADO', 'estado' => 1, 'activo' => 1, 'confirmacion_solicitud' => 0])->leftJoin('usuarios','solicitud.id_invitado','=','usuarios.id')->get();
    }
    public static function confirmacionRestante()
    {
        return ModeloSolicitud::where(['responsable' => 'INVITADO', 'estado' => 1, 'activo' => 1, 'confirmacion_solicitud' => 0])->count();
    }
    public static function addSolicitudCliente($datos,$user)
    {
        DB::beginTransaction();
        try {
            $ultimo_id = ModeloSolicitud::getLastId();
            $datosAgregar = [
                'fecha_soli' => date('Y-m-d'),
                'responsable' => 'INVITADO',
                'trayecto' => $datos['trayecto'],
                'id_cliente' => $datos['cliente_id'],
                'doc_iden_cli' => $datos['doc_cliente'],
                'nro_doc_cli' => $datos['nro_doc_cliente'],
                'id_soli' => $datos['id_soli'],
                'doc_iden_soli' => $datos['doc_proveedor'],
                'nro_doc_soli' => $datos['nro_proveedor'],
                'id_invitado' => $user,
                'user_create' => $user,
                'depart_cli_recojo' => $datos['departamento'],
                'ciudad_cli_recojo' => $datos['provincia'],
                'distrito_cli_recojo' => $datos['distrito'],
                'direccion_recojo' => $datos['direccion'],
                'centro_costo_invitado' => $datos['centro_costo_invitado'],
                'cuenta_invitado' => $datos['cuenta_invitado'],
                'dia_recojo' => $datos['dia_recojo'],
                'hr_recojo_estimado' => $datos['hr_recojo_estimado'],
                'total_bulto' => $datos['cant_bulto'],
                'nro_solicitud' => is_null($ultimo_id) ? 1 : $ultimo_id->id + 1
            ];
            $cuenta = Clientes::comprobarNroCuenta($datos['cliente_id'], $user, $datos['cuenta_invitado']);
            if (!is_null($cuenta)) {
                DB::table('usuario_cliente')->where(['cod_usuario' => $user, 'cod_cliente' => $datos['cliente_id']])->update(['cuenta' => $cuenta]);
            }
            $idSolicitud = DB::table('solicitud')->insertGetId($datosAgregar);
            $detalle = self::filtrarDetalleSolicitud($datos, $idSolicitud);
            DB::table('solicitud_detalle')->insert($detalle);
            DB::commit();
            return ['success' => 'Solicitud agregada correctamente'];
        } catch (Exception $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function filtrarDetalleSolicitud($datos,$idSolicitud)
    {
        $detalle = [];
        for ($i=0; $i < count($datos['depar_cli']); $i++) { 
            $detalle[] = [
                'id_solicitud' => $idSolicitud,
                'departamento' => $datos['depar_cli'][$i],
                'provincia' => $datos['ciudad_cli'][$i],
                'distrito' => $datos['distrito_cli'][$i],
                'sobres' => empty($datos['sobres'][$i]) ? 0 :$datos['sobres'][$i],
                'cajas' => empty($datos['cajas'][$i]) ? 0 :$datos['cajas'][$i],
                'paquetes' => empty($datos['paquetes'][$i]) ? 0 :$datos['paquetes'][$i],
                'otros' => empty($datos['otros'][$i]) ? 0 :$datos['otros'][$i],
                'detalle_otros' => empty($datos['detalle_otros'][$i]) ? '' : $datos['detalle_otros'][$i]
            ];
        }
        return $detalle;
    }
    public static function addSolicitud($datos,$user)
    {
        DB::beginTransaction();
        try {
            $ultimo_id = ModeloSolicitud::getLastId();
            $datos['nro_solicitud'] = is_null($ultimo_id) ? 1 : $ultimo_id->id + 1;
            $datos['user_create'] = $user;
            if($datos['responsable'] == 'INVITADO' && !isset($datos['id_invitado'])){
                return ['error' => 'por favor seleccione un invitado'];
            }
            $id_proveedor = 0;
            if ($datos['nuevo_prov'] == 'true' && isset($datos['agregar_prov'])) {
                $proveedor = [
                    'tipo_docu_prov' => $datos['doc_iden_soli'],
                    'nro_docu_prov' => $datos['nro_doc_soli'],
                    'nomb_prov' => $datos['id_soli'],
                    'depa_prov' => $datos['depart_cli_recojo'],
                    'prov_prov' => $datos['ciudad_cli_recojo'],
                    'dist_prov' => $datos['distrito_cli_recojo'],
                    'dire_prov' => $datos['direccion_recojo']
                ];
                $id_proveedor = DB::table('proveedor_clientes')->insertGetId($proveedor);
                if ($datos['agregar_prov'] == 'true') {
                    DB::table('relacion_cliente_proveedor')->insert(['id_cliente' => $datos['id_cliente'], 'id_proveedor' => $id_proveedor]);
                }
            }
            if (!is_null($datos['id_soli']) && $datos['nuevo_prov'] == 'false') {
                $id_proveedor = intval($datos['id_soli']) === 0 ? 0 : $datos['id_soli'];
            }
            $datos['id_soli'] = $id_proveedor === 0 ? null : $id_proveedor;
            if (isset($datos['nuevo_prov'])) {
                unset($datos['nuevo_prov']);
            }
            if(isset($datos['cant_bulto'])){
                $datos['total_bulto'] = $datos['cant_bulto'];
                unset($datos['cant_bulto']);
            }
            if (isset($datos['agregar_prov'])) {
                unset($datos['agregar_prov']);
            }
            $filter = ['depar_cli', 'ciudad_cli', 'distrito_cli', 'sobres', 'cajas', 'paquetes', 'otros', 'detalle_otros'];
            $detalleDatos = [];
            foreach ($datos as $key => $value) {
                if (in_array($key, $filter)) {
                    $detalleDatos[$key] = $value;
                    unset($datos[$key]);
                }
            }
            $id_soli = DB::table('solicitud')->insertGetId($datos);
            $detalle = self::filtrarDetalleSolicitud($detalleDatos, $id_soli);
            DB::table('solicitud_detalle')->insert($detalle);
            if(isset($datos['id_invitado'])){
                $cuenta = Clientes::comprobarNroCuenta($datos['id_cliente'], $datos['id_invitado'], $datos['cuenta_invitado']);
                if (!is_null($cuenta)) {
                    DB::table('usuario_cliente')->where(['cod_usuario' => $datos['id_invitado'], 'cod_cliente' => $datos['id_cliente']])->update(['cuenta' => $cuenta]);
                }
            }
            DB::commit();
            return ['success' => 'Solicitud ingresada con exito'];
        } catch (Exception $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }        
    }
    
}
