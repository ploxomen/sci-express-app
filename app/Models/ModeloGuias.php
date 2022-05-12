<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloGuias extends Model
{
    protected $table = 'guias';
    public $timestamps = false;

    public static function agregar($guia,$detalles,$cli,$cli_bool,$user)
    {
        DB::beginTransaction();
        try {
            if(strlen(trim($guia['nro_guia'])) > 11 && strpos($guia['nro_guia'],'-') != 3){
                return ['alerta' => 'La guía N° '.$guia['nro_guia'] . ' no está con el formato solicitado'];
            }
            $nro_guia = explode('-',trim($guia['nro_guia']));
            if(DB::table('guias')->where(['nro_guia' => $nro_guia[1], 'prefijo' => $nro_guia[0]])->count() > 0){
                return ['alerta' => 'La guía N° <b>' . $guia['nro_guia'] . '</b> ya se encuentra registrada, por favor intente con otro número'];
            }
            if($guia['estado'] == '1' || $guia['estado'] == '2'){
                if (DB::table('relacion_guias_embarque')->where(['nro_guia_embarque' => $nro_guia[1], 'prefijo_guia_embarque' => $nro_guia[0]])->count() > 0) {
                    return ['alerta' => 'Para registrar la guía N° <b>' . $guia['nro_guia'] . '</b> debe de ser una guía inactiva.'];
                }
            }
            $arrayConsulta = [];
            if(empty($guia['ciudad_destino_final'])){
                $arrayConsulta = [
                    'ubigeo_distrito' => $guia['dest_dist'], 
                    'activo' => 1, 
                    'tipo_base' => 'distrito'
                ];
            }else{
                $arrayConsulta = [
                    'ubigeo_distrito' => $guia['dest_dist'],
                    'activo' => 1,
                    'tipo_base' => 'ciudad',
                    'id_ciudad' => $guia['ciudad_destino_final']
                ];
            }
            $id_cluster = DB::table('ciudades_base_cluster')->select('id_cluster')
            ->where($arrayConsulta)
            ->first();
            if(empty($id_cluster)){
                return ['alerta' => 'No se encontraron clusters para la ciudad o distrito del destino final, favor de agregar en el modulo de mis cluster'];
            }
            $id_cli_cli = $guia['id_destinatario'];
            if(!empty($cli_bool)){
                $id_cli_cli = DB::table('clientes_de_clientes')->insertGetId([
                    'tipo_doc' => $guia['dest_tipo_docu'],
                    'nro_doc' => $guia['dest_nro_docu'],
                    'nombres' => $guia['id_destinatario'],
                    'direccion' => $guia['dest_dire'],
                    'departamento' => $guia['dest_depa'],
                    'provincia' => $guia['dest_prov'],
                    'distrito' => $guia['dest_dist']
                ]);
            }
            if(isset($guia['add_cli_bool'])){
                unset($guia['add_cli_bool']);
            }
            if (isset($guia['add_cli_cli'])) {
                unset($guia['add_cli_cli']);
            }
            if (isset($guia['nro_guia'])) {
                unset($guia['nro_guia']);
            }
            $guia['id_cluster'] = $id_cluster->id_cluster;
            $guia['id_destinatario'] = $id_cli_cli;
            $guia['prefijo'] = $nro_guia[0];
            $guia['nro_guia'] = $nro_guia[1];
            $id_guia = DB::table('guias')->insertGetId($guia);
            $tot_cant = 0;
            $tot_kg = 0;
            $tot_vol = 0;
            for ($i=0; $i < count($detalles) ; $i++) {
                $detalles[$i]['id_guia'] = $id_guia;
                $tot_cant += floatval($detalles[$i]['cantidad']);
                $tot_kg += floatval($detalles[$i]['kg']);
                $tot_vol += floatval($detalles[$i]['volumen']);
            }
            DB::table('detalle_guias')->insert($detalles);
            DB::table('guias')->where(['id' => $id_guia])->update(['tot_cant' => $tot_cant, 'tot_kg' => $tot_kg, 'tot_vol' => $tot_vol]);
            DB::table('procesos_guia')->insert(['id_guia' => $id_guia,'situacion' => 1,'id_user' => $user]);
            DB::commit();
            return ['success' => 'La guía fue creada correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function obtenerDetalleMisGuias($guia)
    {
        $guia = DB::select('CALL sp_obtener_detalle_guia(?)',[$guia]);
        if (isset($guia[0])) {
            $guia[0]->remi_depa = (new Ubigeo)->getNameDepartamento($guia[0]->remi_depa);
            $guia[0]->remi_prov = (new Ubigeo)->getNameProvincia($guia[0]->remi_prov);
            $guia[0]->remi_dist = (new Ubigeo)->getNameDistrito($guia[0]->remi_dist);
            $guia[0]->dest_depa = (new Ubigeo)->getNameDepartamento($guia[0]->dest_depa);
            $guia[0]->dest_prov = (new Ubigeo)->getNameProvincia($guia[0]->dest_prov);
            $guia[0]->dest_dist = (new Ubigeo)->getNameDistrito($guia[0]->dest_dist);
            $guia[0]->detalles_guias = DB::table('detalle_guias')->where(['id_guia' => $guia[0]->id])->get();

        }
        return isset($guia[0]) ? $guia[0] : null;
    }
    public static function actualizarEstadoGuiasCluster($id_guia,$id_usuario,$estado)
    {
        return DB::select('CALL sp_actualizar_estado_guia_cluster(?,?,?)',[$id_guia,$id_usuario,$estado]);
    }
    public static function obtenerGuiasCluster($guia, $usuario,$estado,$tipo)
    {
        $resultado = DB::select('CALL sp_obtener_guias_cluster(?,?,?,?)',[$guia,$usuario,$estado,$tipo]);
        $estados = InformacionAdicional::situacionGuiaCluster();
        foreach ($resultado as $re) {
            $re->prov_orig = (new Ubigeo)->getNameProvincia($re->prov_orig);
            $re->prov_dest = (new Ubigeo)->getNameProvincia($re->prov_dest);
            $re->lista_estados = $estados;
        }
        return $resultado;
    }
    public static function obtenerGuiasClusterDetalle($guia, $usuario, $estado, $tipo)
    {
        $guia = DB::select('CALL sp_obtener_guias_cluster(?,?,?,?)', [$guia, $usuario, $estado, $tipo]);
        if (isset($guia[0])) {
            $guia[0]->remi_depa = (new Ubigeo)->getNameDepartamento($guia[0]->remi_depa);
            $guia[0]->remi_prov = (new Ubigeo)->getNameProvincia($guia[0]->remi_prov);
            $guia[0]->remi_dist = (new Ubigeo)->getNameDistrito($guia[0]->remi_dist);
            $guia[0]->dest_depa = (new Ubigeo)->getNameDepartamento($guia[0]->dest_depa);
            $guia[0]->dest_prov = (new Ubigeo)->getNameProvincia($guia[0]->dest_prov);
            $guia[0]->dest_dist = (new Ubigeo)->getNameDistrito($guia[0]->dest_dist);
            $guia[0]->detalles_guias = DB::table('detalle_guias')->where(['id_guia' => $guia[0]->id])->get();
        }
        return $guia;
    }
    public static function obtenerGuias($filtros,$id_usuario)
    {
        $guias = DB::select('CALL sp_obtener_guias(?,?,?,?)',[$filtros['prov_dest'], $filtros['prov_orig'],$filtros['cluster_id'],$id_usuario]);
        for ($i=0; $i < count($guias) ; $i++) {
            if ($guias[$i]->tipo_ciudad_base_cluster == 'distrito') {
                $guias[$i]->ciudad_base_cluster = (new Ubigeo)->getNameDistrito($guias[$i]->ciudad_base_cluster);
            }
            if ($guias[$i]->tipo_ciudad_final == 'distrito') {
                $guias[$i]->ciudad_final = (new Ubigeo)->getNameDistrito($guias[$i]->ciudad_final);
            }
            $guias[$i]->dest_prov = (new Ubigeo)->getNameProvincia($guias[$i]->dest_prov);
            $guias[$i]->remi_prov = (new Ubigeo)->getNameProvincia($guias[$i]->remi_prov);
        }
        return $guias;
    }
    public static function anularGuias($guia)
    {
        return DB::table('guias')->where(['id' => $guia])->update(['estado' => 2]);
    }
    public static function cambioEstadoGuia($guia,$estado)
    {
        return DB::table('guias')->where(['estado' => 1, 'id' => $guia])->update(['situ' => $estado]);
    }
    public static function guiaPorAsignar()
    {
        return DB::select('SELECT fn_obtener_guias_para_asignar() AS total');
    }
    public static function asignarPersona($usuario,$guia)
    {
        DB::table('guias')->where(['estado' => 1, 'id' => $guia])->update(['id_asignado' => $usuario]);
        return self::guiaPorAsignar();
    }
    public static function filtrarClusters($lista,$resultados)
    {
        $nuevoFiltro = [];
        foreach ($resultados as $vresult) {
            foreach ($lista as $vlista) {
                if($vresult->id_cluster == $vlista->id){
                    $nuevoFiltro[] = [
                        'id_cluster' => $vlista->id,
                        'nombres_completos' => $vlista->name . ' ' . $vlista->last_name
                    ];
                    continue;
                }
            }
        }
        return $nuevoFiltro;
    }
    public static function mostrarRetornoGuias($tipo,$usuario)
    {
        $guias = DB::select('CALL sp_obtener_guias_retornadas(?,?,?)',[0, $usuario,$tipo]);
        foreach ($guias as $miguia) {
            $miguia->listaEstados = InformacionAdicional::situacionEstado();
        }
        return $guias;
    }
    public static function cambiarEstadoRetornoGuia($id_usuario,$id_guia, $nuevo_estado)
    {
        return DB::select('CALL sp_actualizar_estado_guia_retorno(?,?,?)',[$id_guia,$id_usuario,$nuevo_estado]);
    }
    public static function mostrarTodasGuias($estado,$tipo)
    {
        $resultado = DB::select('CALL sp_obtener_guias_todos(?,?)',[$estado,$tipo]);
        $estados = InformacionAdicional::situacionEstado();
        $usuarios = User::obtenerUsuarioRol(3);
        $clusters = DB::table('cluster AS c')->select('c.id','u.name','u.last_name')->leftJoin('usuarios AS u','c.id_usuario','=','u.id')->where('u.status','!=','0')->get();
        foreach ($resultado as $guia) {
            $arrayConsulta = [];
            if ($guia->tipo_ciudad_final == 'distrito') {
                $arrayConsulta = [
                    'ubigeo_distrito' => $guia->dest_dist,
                    'activo' => 1,
                    'tipo_base' => 'distrito'
                ];
            } else {
                $arrayConsulta = [
                    'ubigeo_distrito' => $guia->dest_dist,
                    'id_ciudad' => $guia->ciudad_destino_final,
                    'activo' => 1,
                    'tipo_base' => 'ciudad'
                ];
            }
            $filtroClusters = DB::table('ciudades_base_cluster')->select('id_cluster')
            ->where($arrayConsulta)->groupBy('id_cluster')->get();
            $guia->listClusters = self::filtrarClusters($clusters, $filtroClusters);
            if($guia->tipo_ciudad_base_cluster == 'distrito'){
                $guia->ciudad_base_cluster = (new Ubigeo)->getNameDistrito($guia->ciudad_base_cluster);
            }
            if($guia->tipo_ciudad_final == 'distrito'){
                $guia->ciudad_final = (new Ubigeo)->getNameDistrito($guia->ciudad_final);
            }
            $guia->listSoli = $estados;
            $guia->listUsu = $usuarios;
        }
        return $resultado;
    }
}
