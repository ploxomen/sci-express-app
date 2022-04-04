<?php

namespace App\Models;

use App\Http\Controllers\Ubigeo;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloEmbarque extends Model
{
    protected $table = "embarque";
    public $timestamps = false;

    public static function embarcarTrabajador($id_user){
        $embarques = DB::select('CALL sp_embarcar_trabajador(?)',[$id_user]);
        foreach ($embarques as $em) {
            $em->transportes = DB::table('transporte_embarque AS te')
            ->join('empresa_transporte AS et','te.id_empr_tran','=','et.id')->where(['te.id_list_emba' => $em->id, 'et.activo' => 1])->get();
            $em->manfiestos = DB::table('embarque_manifiesto AS em')
            ->select('m.id','m.fech_mani','tra.tipo','m.prov_dest','m.prov_orig')
            ->join('manifiesto AS m','em.id_manifiesto','=','m.id')
            ->join('tipo_trayecto_envio AS tra','m.trayecto','=','tra.id')
            ->where(['em.id_embarque' => $em->id,'m.activo' => 1])->get();
            foreach ($em->manfiestos as $prov) {
                $prov->prov_dest = (new Ubigeo)->getNameProvincia($prov->prov_dest);
                $prov->prov_orig = (new Ubigeo)->getNameProvincia($prov->prov_orig);
            }
        }
        return $embarques;
    }

    public static function actualizarEmbarque($id_embarque,$id_user,$empresa)
    {
        return DB::select('CALL sp_actualizar_embarque_trabajador(?,?,?,?)',[$id_embarque,$id_user,date('H:i'),$empresa]);
    }
    public static function obtenerEmbarqueInforme($id_usuario)
    {
        return DB::select('CALL sp_obtener_embarques_informar(?)',[$id_usuario]);
    }
    public static function obtenerEmbarqueDetalle($id_embarque,$id_user)
    {
        return DB::select('CALL sp_obtener_embarque_detalle_informe(?,?)',[$id_embarque,$id_user]);
    }
    public static function agregarInformeEmbarque($datos,$usuario)
    {
        DB::beginTransaction();
        try {
            $id = $datos['id_embarque'];
            unset($datos['id_embarque']);
            if(isset($datos['comprobantes'])){
                $datos['comprobantes'] = implode(';',$datos['comprobantes']);
            }
            if (isset($datos['nro_comprobantes'])) {
                $datos['nro_comprobantes'] = implode(';', $datos['nro_comprobantes']);
            }
            $datos['estado'] = 4;
            $datos['user_update'] = $usuario;
            $datos['date_update'] = date('Y-m-d H:i:s');
            DB::table('embarque')->where(['id' => $id, 'estado' => 3, 'activo' => 1])->update($datos);
            DB::table('embarque_manifiesto AS em')->join('manifiesto AS m', 'em.id_manifiesto', '=', 'm.id')
                ->where(['m.activo' => 1, 'm.estado' => 3, 'em.id_embarque' => $id])
                ->update(['m.estado' => $datos['estado'], 'date_update' => $datos['date_update'], 'user_update' => $usuario]);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
        }
    }
    public static function obtenerEmbarqueArribado($id_embarque,$id_guia,$id_usuario,$tipo)
    {
        $result = DB::select('CALL sp_obtener_embarques_arribar(?,?,?,?)',[$id_embarque,$id_guia,$id_usuario,$tipo]);
        switch ($tipo) {
            case 'manifiestos':
                foreach ($result as $mani) {
                    $mani->prov_dest = (new Ubigeo)->getNameProvincia($mani->prov_dest);
                    $mani->prov_orig = (new Ubigeo)->getNameProvincia($mani->prov_orig);
                    $mani->guias = DB::table('detalle_manifiesto AS dt')->select('g.id')
                    ->join('guias AS g','dt.id_guia','=','g.id','left')
                    ->where(['dt.id_manifiesto' => $mani->id, 'g.situ' => 5])->get();
                }
                break;
            case 'detalle-guia':
                foreach ($result as $mani) {
                    $mani->remi_depa = (new Ubigeo)->getNameDepartamento($mani->remi_depa);
                    $mani->remi_prov = (new Ubigeo)->getNameProvincia($mani->remi_prov);
                    $mani->remi_dist = (new Ubigeo)->getNameDistrito($mani->remi_dist);
                    $mani->dest_depa = (new Ubigeo)->getNameDepartamento($mani->dest_depa);
                    $mani->dest_prov = (new Ubigeo)->getNameProvincia($mani->dest_prov);
                    $mani->dest_dist = (new Ubigeo)->getNameDistrito($mani->dest_dist);
                    $mani->detalles_guias = DB::table('detalle_guias')->where(['id_guia' => $mani->id])->get();
                }
            break;
            
        }
        return $result;
    }
    public static function devolucionCargo($guia,$tipo,$usuario)
    {
        $result = DB::select('CALL sp_retorno_cargo(?,?,?)',[$guia,$usuario,$tipo]);
        if($tipo == 'ver-cargos-pendientes'){
            foreach ($result as $guia) {
                $guia->remi_prov = (new Ubigeo)->getNameProvincia($guia->remi_prov);
                $guia->dest_prov = (new Ubigeo)->getNameProvincia($guia->dest_prov);
            }
        }
        return $result;
    }
    public static function actualizarDevolucionCargo($nuevo_retorno,$guias,$usuario)
    {
        DB::beginTransaction();
        try {
            $id_retorno = DB::table('retorno_cargo')->insertGetId($nuevo_retorno);
            for ($i=0; $i < count($guias) ; $i++) {
                $guias[$i]['id_retorno_cargo'] = $id_retorno;
                DB::select('CALL sp_retorno_cargo(?,?,?)', [$guias[$i]['id_guia'], $usuario, 'actualizar-guia']);
            }
            DB::table('detalle_retorno_cargo')->insert($guias);
            DB::commit();
            return ['success' => 'Información agregada correctamente'];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
            DB::rollBack();
        }
    }
    public static function actualizarGuiaLlegadas($id_embarque,$id_manifiesto,$id_guia,$estado, $texto)
    {
        return DB::select('CALL sp_actualizar_llegadas_guias(?,?,?,?,?)',[$id_embarque, $id_manifiesto,$id_guia,$estado,$texto]);
    }
    public static function entregaGuias($id_usuario)
    {
        $result = DB::select('CALL sp_obtener_guias_entregar(?)',[$id_usuario]);
        foreach ($result as $mani) {
            $mani->remi_depa = (new Ubigeo)->getNameDepartamento($mani->remi_depa);
            $mani->remi_prov = (new Ubigeo)->getNameProvincia($mani->remi_prov);
            $mani->remi_dist = (new Ubigeo)->getNameDistrito($mani->remi_dist);
            $mani->dest_depa = (new Ubigeo)->getNameDepartamento($mani->dest_depa);
            $mani->dest_prov = (new Ubigeo)->getNameProvincia($mani->dest_prov);
            $mani->dest_dist = (new Ubigeo)->getNameDistrito($mani->dest_dist);
        }
        return $result;
    }
    public static function confirmarEntregaGuias($id_guia,$usuario,$archivos)
    {
        DB::beginTransaction();
        try {
            DB::select('CALL sp_actualizar_entrega_guia(?,?)',[$usuario, $id_guia]);
            DB::table('imagenes_guia')->insert($archivos);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
        }
    }
}
