<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InformacionAdicional extends Model
{

    public static function viaEnvio()
    {
        return DB::table('via_envio')->where(['estado' => 1])->get();
    }
    public static function situacionEstado()
    {
        return DB::table('situacion_estado')->where(['estado' => 1])->get();
    }
    public static function situacionGuiaCluster()
    {
        return DB::table('situacion_estado')->where(['estado' => 1])->whereIn('id',[5,6,7,8,9,10])->get();
    }
    public static function obtenerEmpresaTrasnporte($id)
    {
        return DB::table('empresa_transporte')->where(['id' => $id, 'activo' => 1])->first();
    }
    public static function obtenerEmpresasTrasnportes()
    {
        return DB::table('empresa_transporte')->where(['activo' => 1])->get();
    }
    public static function agregarEmpresaTransporte($datos)
    {
        return DB::table('empresa_transporte')->insert($datos);
    }
    public static function actualizarEmpresaTransporte($datos,$id)
    {
        return DB::table('empresa_transporte')->where(['activo' => 1, 'id' => $id])->update($datos);
    }
    public static function eliminarEmpresaTransporte($id)
    {
        return DB::table('empresa_transporte')->where(['activo' => 1, 'id' => $id])->update(['activo' => 0]);
    }
    public static function obtenerDiasTransito($dep_origen,$dep_destino,$prov_destino,$ciudad_destino)
    {
        $destinos = DB::table('transito_destinos')->select('total_hr_origen_destino','total_origen_base_hr_aereo')
        ->where(['departamento_origen' => $dep_origen,'departamento_destino' => $dep_destino, 'ciudad_destino' => $ciudad_destino])->first();
        if(empty($destinos)){
            $destinos = DB::table('transito_destinos')->select('total_hr_origen_destino', 'total_origen_base_hr_aereo')
            ->where(['departamento_origen' => $dep_origen, 'departamento_destino' => $dep_destino, 'ciudad_destino' => $prov_destino])->first();
        }
        if(empty($destinos)){
            $destinos = DB::table('transito_destinos')->select('total_hr_origen_destino', 'total_origen_base_hr_aereo')
            ->where(['departamento_origen' => $dep_origen, 'departamento_destino' => $dep_destino])->first();
        }
        return $destinos;
    }
    public static function obtenerCiudadBaseEmbarcar($idUsuario)
    {
        $ciudades = DB::select("CALL sp_embarque_filtros_manifiestos(?)",[$idUsuario]);
        foreach ($ciudades as $ciudad) {
            $ciudad->nombre = $ciudad->tipo_base == 'distrito' ? mb_strtoupper((new Ubigeo)->getNameDistrito($ciudad->ubigeo_distrito),'UTF-8') : $ciudad->nombre_ciudad;
            $ciudad->value = $ciudad->tipo_base == 'distrito' ? $ciudad->ubigeo_distrito : $ciudad->id;
        }
        return $ciudades;
    }
    public static function obtenerFletes()
    {
        return DB::table('fletes')->select('cajas','cajones','ciudad_base','jabas','paquetes','sobres','parihuelas')->get();
    }
    public static function calcularTransito()
    {
        DB::select('CALL sp_evento_actualizar_estados_transito()');
    }
    public static function getGrupoEnvio()
    {
       return ['MEDICINA', 'REPUESTOS', 'VALORADA', 'OTROS'];
    }
    public static function getTipoEnvio()
    {
        return DB::table("facturacion_tipo_envio")->select("id","tipo_envio")->get();
    }
    public static function rolModuloEditarGuia(){
        return [1,2,4];
    }
    public static function getEnvaseEmbalaje()
    {
        return ['A GRANEL','ATADOS','BALDES', 'BARRICAS', 'BIDONES', 'BOBINAS', 'BOLSAS', 'BOTELLONES', 'CAJAS', 'CAJONES', 'CANASTOS', 'CANECAS', 'CARRETES', 'CILINDROS', 'CISTERNAS','EMBASE', 'EMBALAJE', 'CUÑETES', 'FARDOS', 'JAULAS', 'LATAS', 'PAQUETES','PARIHUELA' ,'PLATAFORMA', 'ROLLOS', 'SACOS', 'SOBRES', 'TAMBORES', 'TONELES', 'UNIDADES'];
    }
    public static function obtenerDiasFiltros()
    {
        return [
            'inicioFiltro' => date('Y-m-d'),
            'finFiltro' => date('Y-m-d',strtotime(date('Y-m-d') . ' -90 days')),
        ];
    }
}
