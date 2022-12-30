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
            if($guia['select_comprobantes'] == 'nro_guia'){
                if (strlen(trim($guia['nro_guia'])) > 11 && strpos($guia['nro_guia'], '-') != 3) {
                    return ['alerta' => 'La guía N° ' . $guia['nro_guia'] . ' no está con el formato solicitado'];
                }
                $nro_guia = explode('-', trim($guia['nro_guia']));
                if (DB::table('guias')->where(['nro_guia' => $nro_guia[1], 'prefijo' => $nro_guia[0]])->count() > 0) {
                    return ['alerta' => 'La guía N° <b>' . $guia['nro_guia'] . '</b> ya se encuentra registrada, por favor intente con otro número'];
                }
                if ($guia['estado'] == '1' || $guia['estado'] == '2') {
                    if (DB::table('relacion_guias_embarque')->where(['nro_guia_embarque' => $nro_guia[1], 'prefijo_guia_embarque' => $nro_guia[0]])->count() > 0) {
                        return ['alerta' => 'Para registrar la guía N° <b>' . $guia['nro_guia'] . '</b> debe de ser una guía inactiva.'];
                    }
                }
                $guia['prefijo'] = $nro_guia[0];
                $guia['nro_guia'] = $nro_guia[1];
                $msj = 'La guía N° <b>' . $guia['prefijo'] . '-' .$guia['nro_guia'] . '</b> se creó de manera correcta';
            }
            if ($guia['select_comprobantes'] == 'nro_or') {
                if(strlen(trim($guia['nro_guia'])) > 7) {
                    return ['alerta' => 'La Orden de Servicio N° ' . $guia['nro_guia'] . ' no está con el formato solicitado'];
                }
                if (DB::table('guias')->where(['nro_guia' => intval($guia['nro_guia']), 'prefijo' => null])->count() > 0) {
                    return ['alerta' => 'La Orden de Servicio N° <b>' . $guia['nro_guia'] . '</b> ya se encuentra registrada, por favor intente con otro número'];
                }
                $guia['prefijo'] = null;
                $guia['nro_guia'] = intval($guia['nro_guia']);
                $msj = 'La Orden de Servicio N° <b>' . str_pad($guia['nro_guia'],7,'0',STR_PAD_LEFT) . '</b> se creó de manera correcta';
            }
            $arrayConsulta = [];
            if(empty($guia['id_ciudad_final'])){
                $guia['ciudad_destino_final'] = null;
                $arrayConsulta = [
                    'ubigeo_distrito' => $guia['dest_dist'], 
                    'activo' => 1, 
                    'tipo_base' => 'distrito'
                ];
            }else{
                $guia['ciudad_destino_final'] = $guia['id_ciudad_final'];
                $arrayConsulta = [
                    'ubigeo_distrito' => $guia['dest_dist'],
                    'activo' => 1,
                    'tipo_base' => 'ciudad',
                    'id_ciudad' => $guia['id_ciudad_final']
                ];
            }
            if($guia['estado'] != '3'){
                $id_cluster = DB::table('ciudades_base_cluster')->select('id_cluster')
                ->where($arrayConsulta)
                ->first();
                if (empty($id_cluster)) {
                    return ['alerta' => 'No se encontraron clusters para la ciudad o distrito del destino final, favor de agregar en el modulo de mis cluster'];
                }
                $guia['id_cluster'] = $id_cluster->id_cluster;
            }else{
                $guia['via_tras'] = null;
                $guia['id_cluster'] = null;
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
                    'distrito' => $guia['dest_dist'],
                    'id_ciudad_final' => empty($guia['id_ciudad_final']) ? null : $guia['id_ciudad_final']
                ]);
            }
            if ($guia['id_invitado'] == 'ninguno') {
                $guia['id_invitado'] = null;
            }
            unset($guia['id_ciudad_final']);
            if(isset($guia['add_cli_bool'])){
                unset($guia['add_cli_bool']);
            }
            if (isset($guia['select_comprobantes'])) {
                unset($guia['select_comprobantes']);
            }
            if (isset($guia['add_cli_cli'])) {
                unset($guia['add_cli_cli']);
            }
            $guia['id_destinatario'] = $id_cli_cli;
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
            return ['success' => $msj];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function determinarClienteProveedor($tipo,$id)
    {
        $idCliente = 0;
        if($tipo === 'proveedor'){
            $cliente = DB::table('relacion_cliente_proveedor')->select('id_cliente')->where('id_proveedor',$id)->first();
            if(!empty($cliente->id_cliente)){
                $idCliente = $cliente->id_cliente;
            }
            return $idCliente;
        }
        return intval($id);

    }
    public static function camposNoCambiados()
    {
        return [
            'select_comprobantes_update',
            'estado',
            'tray_env',
            'via_envi',
            'id_remitente',
            'remi_depa',
            'remi_prov',
            'remi_dist',
            'id_destinatario',
            'dest_depa',
            'dest_prov',
            'dest_dist',
            'ciudad_destino_final'
        ];
    }
    public static function updateGuia($idGuia,$guia, $detallesNew, $detallesUpdate, $cli_bool,$completar)
    {
        $condicionalGuia = [
            'id' => $idGuia
        ];
        if($completar){
            $condicionalGuia['preguia'] = 1;
            $guia['estado'] = "1";
        }
        DB::beginTransaction();
        try {
            if (isset($guia['id_invitado']) && $guia['id_invitado'] == 'ninguno') {
                $guia['id_invitado'] = null;
            }
            $estadoGeneral = DB::table('guias')->select('situ','estado','prefijo','nro_guia')->where($condicionalGuia)->first();
            if(empty($estadoGeneral)){
                return ['error' => 'La guía u O/S no existe'];
            }
            if($estadoGeneral->situ > 1){
                $guia['select_comprobantes_update'] = !empty($estadoGeneral->prefijo) ? '1' : '2';
                $guia['estado'] = strval($estadoGeneral->estado);
            }
            if (isset($guia['select_comprobantes_update']) && $guia['select_comprobantes_update'] === '1') {
                if (strlen(trim($guia['nro_guia'])) > 11 && strpos($guia['nro_guia'], '-') != 3) {
                    return ['alerta' => 'La guía N° ' . $guia['nro_guia'] . ' no está con el formato solicitado'];
                }
                $nro_guia = explode('-', trim($guia['nro_guia']));
                if (DB::table('guias')->where(['nro_guia' => $nro_guia[1], 'prefijo' => $nro_guia[0]])->where('id','!=',$idGuia)->count() > 0) {
                    return ['alerta' => 'La guía N° <b>' . $guia['nro_guia'] . '</b> ya se encuentra registrada, por favor intente con otro número'];
                }
                if ($guia['estado'] === '1' || $guia['estado'] === '2') {
                    if (DB::table('relacion_guias_embarque')->where(['nro_guia_embarque' => $nro_guia[1], 'prefijo_guia_embarque' => $nro_guia[0]])->count() > 0) {
                        return ['alerta' => 'Para registrar la guía N° <b>' . $guia['nro_guia'] . '</b> debe de ser una guía inactiva.'];
                    }
                }
                $guia['prefijo'] = $nro_guia[0];
                $guia['nro_guia'] = $nro_guia[1];
                $msj = 'La guía N° <b>' . $guia['prefijo'] . '-' . $guia['nro_guia'] . '</b> se modificó de manera correcta';
            }
            if (isset($guia['select_comprobantes_update']) && $guia['select_comprobantes_update'] === '2') {
                if (strlen(trim($guia['nro_guia'])) > 7) {
                    return ['alerta' => 'La Orden de Servicio N° ' . $guia['nro_guia'] . ' no está con el formato solicitado'];
                }
                if (DB::table('guias')->where(['nro_guia' => intval($guia['nro_guia']), 'prefijo' => null])->where('id','!=',$idGuia)->count() > 0) {
                    return ['alerta' => 'La Orden de Servicio N° <b>' . $guia['nro_guia'] . '</b> ya se encuentra registrada, por favor intente con otro número'];
                }
                $guia['prefijo'] = null;
                $guia['nro_guia'] = intval($guia['nro_guia']);
                $msj = 'La Orden de Servicio N° <b>' . str_pad($guia['nro_guia'], 7, '0', STR_PAD_LEFT) . '</b> se modificó de manera correcta';
            }
            if($estadoGeneral->situ === 1){
                $arrayConsulta = [];
                if (empty($guia['id_ciudad_final'])) {
                    $guia['ciudad_destino_final'] = null;
                    $arrayConsulta = [
                        'ubigeo_distrito' => $guia['dest_dist'],
                        'activo' => 1,
                        'tipo_base' => 'distrito'
                    ];
                }
                if (!empty($guia['id_ciudad_final'])) {
                    $guia['ciudad_destino_final'] = $guia['id_ciudad_final'];
                    $arrayConsulta = [
                        'ubigeo_distrito' => $guia['dest_dist'],
                        'activo' => 1,
                        'tipo_base' => 'ciudad',
                        'id_ciudad' => $guia['id_ciudad_final']
                    ];
                }
                if (isset($guia['estado']) && $guia['estado'] !== '3') {
                    $id_cluster = DB::table('ciudades_base_cluster')->select('id_cluster')
                        ->where($arrayConsulta)->first();
                    if (isset($guia['estado']) && empty($id_cluster)) {
                        return ['alerta' => 'No se encontraron clusters para la ciudad o distrito del destino final, favor de agregar en el modulo de mis cluster'];
                    }
                    $guia['id_cluster'] = $id_cluster->id_cluster;
                }
                if (isset($guia['estado']) && $guia['estado'] === '3') {
                    $guia['via_tras'] = null;
                    $guia['id_cluster'] = null;
                }
                $id_cli_cli = $guia['id_destinatario'];
                if (!empty($cli_bool)) {
                    $id_cli_cli = DB::table('clientes_de_clientes')->insertGetId([
                        'tipo_doc' => $guia['dest_tipo_docu'],
                        'nro_doc' => $guia['dest_nro_docu'],
                        'nombres' => $guia['id_destinatario'],
                        'direccion' => $guia['dest_dire'],
                        'departamento' => $guia['dest_depa'],
                        'provincia' => $guia['dest_prov'],
                        'distrito' => $guia['dest_dist'],
                        'id_ciudad_final' => empty($guia['id_ciudad_final']) ? null : $guia['id_ciudad_final']
                    ]);
                }
                $guia['id_destinatario'] = $id_cli_cli;
            }
            if($estadoGeneral->situ > 1){
                foreach (self::camposNoCambiados() as $value) {
                    if(isset($guia[$value])){
                        unset($guia[$value]);
                    }
                }
            }
            
            if (isset($guia['id_guia'])) {
                unset($guia['id_guia']);
            }
            if(isset($guia['id_ciudad_final'])){
                unset($guia['id_ciudad_final']);
            }
            if (isset($guia['add_cli_bool'])) {
                unset($guia['add_cli_bool']);
            }
            if (isset($guia['select_comprobantes_update'])) {
                unset($guia['select_comprobantes_update']);
            }
            if (isset($guia['add_cli_cli'])) {
                unset($guia['add_cli_cli']);
            }
            if($completar){
                $guia['preguia'] = 2;
            }
            DB::table('guias')->where($condicionalGuia)->update(['id_invitado' => null,'cc_invitado' => null]);
            DB::table('guias')->where($condicionalGuia)->update($guia);
            $tot_cant = 0;
            $tot_kg = 0;
            $tot_vol = 0;
            for ($i = 0; $i < count($detallesNew); $i++) {
                $detallesNew[$i]['id_guia'] = $idGuia;
                $tot_cant += intval($detallesNew[$i]['cantidad']);
                $tot_kg += floatval($detallesNew[$i]['kg']);
                $tot_vol += floatval($detallesNew[$i]['volumen']);
            }
            DB::table('detalle_guias')->insert($detallesNew);
            for ($i = 0; $i < count($detallesUpdate); $i++) {
                $tot_cant += intval($detallesUpdate[$i]['cantidad']);
                $tot_kg += floatval($detallesUpdate[$i]['kg']);
                $tot_vol += floatval($detallesUpdate[$i]['volumen']);
                $idDetalleGuia = $detallesUpdate[$i]['idDetalle'];
                unset($detallesUpdate[$i]['idDetalle']);
                DB::table('detalle_guias')->where(['id' => $idDetalleGuia,'id_guia' => $idGuia])->update($detallesUpdate[$i]);
            }
            DB::table('guias')->where($condicionalGuia)->update(['tot_cant' => $tot_cant, 'tot_kg' => $tot_kg, 'tot_vol' => $tot_vol]);
            DB::commit();
            return ['success' => $msj];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
    public static function deleteImgEntregaGuia($id_guia, $id_img)
    {
        $image = DB::table('imagenes_guia')->where(['id_guia' => $id_guia, 'id' => $id_img]);
        return ['nomFile' => $image->first('nom_id_file'),'delete' => $image->delete()];
    }
    public static function agregarPreGuias(array $datos,string $tipo)
    {
        $guiasError = [];
        $guiasSuccess = [];
        for ($i=0; $i < count($datos['nroGuia']); $i++) { 
            if(!empty(self::consultarNroGuia($datos['nroGuia'][$i],$datos['tipo']))){
                $guiasError[] = [
                    'nroGuia' => $datos['nroGuia'][$i],
                    'error' => 'La Guía/OS ya se encuentra registrada'
                ];
                continue;
            }
            $cluster = self::consultarCluster($datos['departamento'][$i],$datos['provincia'][$i],$datos['distrito'][$i]);
            if(empty($cluster)){
                $guiasError[] = [
                    'nroGuia' => $datos['nroGuia'][$i],
                    'error' => 'No se encontró cluster para esta Guía/OS'
                ];
                continue;
            }
            if($tipo == 'agregar'){
                $datosCliente = Clientes::getRemitente($datos['nroRuc']);
                if(empty($datosCliente)){
                    return ['error' => 'No se encontró al remitente'];
                }
                $nuevaGuia = [
                    'fech_emis' => date('Y-m-d'),
                    'preguia' => 1,
                    'id_cluster' => $cluster->id,
                    'id_remitente' =>  $datosCliente->id,
                    'remi_dire' => $datosCliente->direccion,
                    'remi_depa' => $datosCliente->dpto,
                    'remi_prov' => $datosCliente->prov,
                    'remi_dist' => $datosCliente->dist,
                    'remi_nro_docu' => $datos['nroRuc'],
                    'remi_tipo_docu' => 4,
                    'tray_env' => $datos['trayecto'][$i],
                    'via_envi' => $datos['traslado'][$i],
                    'dest_depa' => $datos['departamento'][$i],
                    'dest_prov' => $datos['provincia'][$i],
                    'dest_dist' => $datos['distrito'][$i],
                    'estado' => 1
                ];
                if($datos['tipo'] == 'os'){
                    $nuevaGuia['nro_guia'] = $datos['nroGuia'][$i];
                }else{
                    $nuevaGuia['nro_guia'] = intval(explode('-',$datos['nroGuia'][$i])[1]);
                    $nuevaGuia['prefijo'] = intval(explode('-',$datos['nroGuia'][$i])[0]);
                }
                if($datos['tipoFacturacion'] == 'proveedor'){
                    $newCliente = Clientes::obtenerClienteProveedor($datosCliente->id);
                    $nuevaGuia['id_cliente_facturacion'] =  $newCliente->id;
                    $nuevaGuia['tipo_guia'] = 'proveedor';
                }else if($datos['tipoFacturacion'] == 'cliente'){
                    $nuevaGuia['id_cliente_facturacion'] =  $datosCliente->id;
                    $nuevaGuia['tipo_guia'] = 'cliente';
                }else{
                    return ['error' => 'No se encontró al remitente'];
                }
                $guiasSuccess[] = $nuevaGuia;
            }
        }
        if($tipo == 'agregar'){
            if(count($guiasSuccess) === 0){
                return false;
            }
            return DB::table('guias')->insert($guiasSuccess);
        }else{
            return $guiasError;
        }
    }
    public static function guiasPorCompletar()
    {
        $guias = DB::select('CALL sp_preguia_ver_tabla()');
        foreach ($guias as $guia) {
            if ($guia->tipoCiudad == 'distrito') {
                $guia->nombreCiudadFinal = (new Ubigeo())->getNameDistrito($guia->nombreCiudadFinal);
            }
            if ($guia->tipoCiudadCluster == 'distrito') {
                $guia->nombreCiudadCluster = (new Ubigeo())->getNameDistrito($guia->nombreCiudadCluster);
            }
        }
        return $guias;
    }
    public static function guiasPorCompletarDetalle(int $idGuia)
    {
        $guia = DB::table('guias')->select("id","prefijo","nro_guia","fech_emis","tipo_guia","resp_pago","comp_clie","nro_comp_cli","orde_comp","fech_venc_orde_comp","fech_tras","tray_env","via_envi","via_tras","remi_tipo_docu","remi_nro_docu","id_remitente","remi_dire","remi_depa","remi_prov","remi_dist","dest_tipo_docu","dest_nro_docu","id_destinatario","dest_dire","dest_depa","dest_prov","dest_dist","ciudad_destino_final","dias_tran","cst_mest_env","situ","tot_cant","tot_kg","tot_vol","id_invitado","cc_invitado","valor_complejo_distante","ciudad_alejada")->where(['id' => $idGuia,"estado" => 1, "preguia" => 1])->first();
        if(empty($guia)){
            return ['error' => 'no se encontro la gía/os'];
        }
        $ubigeo = new Ubigeo();
        $idCLiente = self::determinarClienteProveedor($guia->tipo_guia,$guia->id_remitente);
        $guia->listProvRemi = $ubigeo->getProvinciasCliente($guia->remi_depa);
        $guia->listDistRemi = $ubigeo->getDistritosCliente($guia->remi_prov); 
        $guia->listProvDest = $ubigeo->getProvinciasCliente($guia->dest_depa);
        $guia->listDistDest = $ubigeo->getDistritosCliente($guia->dest_prov);
        $guia->listInvitados = Clientes::obtenerInvitados($idCLiente);
        $guia->listCc = empty($guia->id_invitado) ? [] : Clientes::centrosCosto($guia->id_invitado, $idCLiente);
        $guia->ciudadesFinales = DB::table('ciudades')->select('id', 'nombre_ciudad')->where(['ubigeo_distrito' => $guia->dest_dist])->get();
        $guia->detalle = DB::table('detalle_guias')->where("id_guia",$guia->id)->get();
        return ['datos' => $guia];
    }
    public static function consultarCluster(string $departamento, string $provincia, string $distrito)
    {
        return DB::table('cluster AS c')->select("c.*")->join('ciudades_base_cluster AS cb','c.id','=','cb.id_cluster')
        ->where(['c.departamento_clu' => $departamento, 'c.provincia_clu' => $provincia, 'cb.ubigeo_distrito' => $distrito, 'cb.activo' => 1])->first();
    }
    public static function consultarNroGuia(string $nroGuia, string $tipoGuia)
    {
        $query = DB::table('guias');
        if($tipoGuia == "guia"){
            $query =  $query->where(['prefijo' => intval(explode('-',$nroGuia)[0]), 'nro_guia' => intval(explode('-',$nroGuia)[1])]);
        }else{
            $query = $query->where('nro_guia',$nroGuia);
        }
        return $query->first();
    }
    public function scopeElimiarDetalleGuia($query,$idGuia,$idDetalleGuia,$estadoActual)
    {
        $guia = $query->where('id', $idGuia)->first();
        if(empty($guia)){
            return ['error' => 'No se encontro la guía'];
        }
        $nroDetalleGuia = DB::table('detalle_guias AS dg')->join('guias AS g','g.id','=','dg.id_guia')->where('dg.id_guia', $idGuia);
        if($guia->estado === 1 && $nroDetalleGuia->count() === 1 && intval($estadoActual) === 1){
            return ['error' => 'Las guías activas deben de tener al menos un detalle'];
        }
        $nroDetalleGuia->where('dg.id',$idDetalleGuia)->delete();
        return ['success' => 'Detalle de guía eliminada correctamente'];
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
            $guia[0]->img_list = DB::table('imagenes_guia')->select('nom_id_file','nom_file')->where(['id_guia' => $guia[0]->id])->get();
            $guia[0]->detalles_guias = DB::table('detalle_guias')->where(['id_guia' => $guia[0]->id])->get();
        }
        return isset($guia[0]) ? $guia[0] : null;
    }
    public function scopeVerEditar($query,$idGuia)
    {   
        $ubigeo = new Ubigeo();
        $guia = $query->where('id',$idGuia)->first();
        // dd($guia);
        $idCLiente = self::determinarClienteProveedor($guia->tipo_guia,$guia->id_remitente);
        $guia->listProvRemi = $ubigeo->getProvinciasCliente($guia->remi_depa);
        $guia->listDistRemi = $ubigeo->getDistritosCliente($guia->remi_prov); 
        $guia->listProvDest = $ubigeo->getProvinciasCliente($guia->dest_depa);
        $guia->listDistDest = $ubigeo->getDistritosCliente($guia->dest_prov);
        $guia->listInvitados = Clientes::obtenerInvitados($idCLiente);
        $guia->listCc = empty($guia->id_invitado) ? [] : Clientes::centrosCosto($guia->id_invitado, $idCLiente);
        $guia->ciudadesFinales = DB::table('ciudades')->select('id', 'nombre_ciudad')->where(['ubigeo_distrito' => $guia->dest_dist])->get();
        $guia->detalle = DB::table('detalle_guias AS dg')->select('dg.*')->join('guias AS g','g.id','=','dg.id_guia')->where('dg.id_guia',$idGuia)->get();
        $guia->imagenes = DB::table('imagenes_guia AS im')->select('im.*')->join('guias AS g', 'g.id', '=', 'im.id_guia')->where('im.id_guia', $idGuia)->get();
        $guia->visitas = DB::table('numero_visitas_guias AS nvg')->select('nvg.*')->join('guias AS g', 'g.id', '=', 'nvg.id_guia')->where('nvg.id_guia', $idGuia)->get();
        return $guia;
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
        $guias = DB::select('CALL sp_obtener_guias(?,?,?,?,?,?)',[$filtros['prov_dest'], $filtros['prov_orig'],$filtros['cluster_id'],$id_usuario,$filtros['via_traslado'],$filtros['estado_guia']]);
        for ($i=0; $i < count($guias) ; $i++) {
            if ($guias[$i]->tipo_ciudad_base_cluster == 'distrito') {
                $guias[$i]->ciudad_base_cluster = mb_strtoupper((new Ubigeo)->getNameDistrito($guias[$i]->ciudad_base_cluster),'UTF-8');
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
    public static function addNewImgGuiaEntrega(array $datos)
    {
        $imgAddGuia = [];
        foreach($datos as $img){
            $idImg = DB::table('imagenes_guia')->insertGetId($img);
            $imgAddGuia[] = [
                'nom_file' => $img['nom_file'],
                'nom_id_file' => $img['nom_id_file'],
                'id_img' => $idImg
            ];
        }
        return $imgAddGuia;
    }
    public static function mostrarRetornoGuias($tipo,$usuario)
    {
        $guias = DB::select('CALL sp_obtener_guias_retornadas(?,?,?)',[0, $usuario,$tipo]);
        foreach ($guias as $miguia) {
            $miguia->listaEstados = InformacionAdicional::situacionEstado();
        }
        return $guias;
    }
    public static function mostrarGuiasRetornadasParaProgramar($fechaLlegada,$cluster)
    {
        $query = DB::table('retorno_cargo AS rc')->select("rc.id","rc.importe_embarque","rc.nro_guias","rc.tipo_comprobantes","rc.total_bultos","et.empresa")
        ->selectRaw("LPAD(rc.id,5,'0') AS nroRetorno,DATE_FORMAT(rc.fch_emba,'%d/%m/%Y') AS fechaRetorno, fn_obtener_nombres_clusters(rc.id_cluster) AS nombreCluster,CONCAT(DATE_FORMAT(rc.fecha_llegada,'%d/%m/%Y'),' ',TIME_FORMAT(rc.hr_llegada,'%h:%i %p')) AS fechaHrLlegada")
        ->join("empresa_transporte AS et","rc.id_empr_tran","=","et.id")
        ->where(['rc.activo' => 1, 'rc.estado' => 1, 'rc.fecha_llegada' => $fechaLlegada]);
        if(!empty($cluster)){
            $idCluster = DB::table('cluster')->where('id_usuario',$cluster)->first();
            $query = $query->where('id_cluster',$idCluster->id);
        }
        return $query->get();
    }
    public static function bultosRetornados($idRetorno)
    {
        return  DB::table('retorno_cargo')->select("nro_cajas","nro_jabas","nro_cajones","nro_sobres","nro_paquetes","num_otros","detalle_otro")
        ->where(['activo' => 1, 'estado' => 1,'id' => $idRetorno])->first();
    }
    public static function programarRecojosRetornos($fechaRecojo,$usuario,$detalles,$vehiculo,$driver,$dealer)
    {
        DB::beginTransaction();
        try {
            $idProgramacion = DB::table('programacion_recojo_retornos')->insertGetId(['fechaProgramada' => $fechaRecojo,'usuarioCreado' => $usuario]);
            $detalles = json_decode($detalles,true);
            $idsRetornos = [];
            for ($i=0; $i < count($detalles); $i++) { 
                $detalles[$i]['idProgramacionRecojo'] = $idProgramacion;
                $detalles[$i]['idRetornoCargo'] = $detalles[$i]['codigo'];
                $detalles[$i]['idVehiculo'] = $vehiculo;
                $detalles[$i]['idConductor'] = $driver;
                $detalles[$i]['idDealer'] = $dealer;
                $idsRetornos[] = $detalles[$i]['codigo'];
                unset($detalles[$i]['codigo']);
            }
            DB::table('programacion_detaller_recojo_retornos')->insert($detalles);
            DB::table('retorno_cargo')->where(['activo' => 1, 'estado' => 1])->whereIn('id',$idsRetornos)->update(['estado' => 2]);
            DB::commit();
            return ['success' => 'retornos programados correctamente'];
        } catch (\Throwable $th) {
            DB::rollBack();
            return ['error' => $th->getMessage()];
        }
    }
    public static function cantidadGuiasRetornadas()
    {
        return DB::table('retorno_cargo')->selectRaw("DATE_FORMAT(fecha_llegada,'%d/%m/%Y') AS fechaLlegada")->where(['activo' => 1, 'estado' => 1])->get();
    }
    public static function cambiarEstadoRetornoGuia($id_usuario,$id_guia, $nuevo_estado)
    {
        return DB::select('CALL sp_actualizar_estado_guia_retorno(?,?,?)',[$id_guia,$id_usuario,$nuevo_estado]);
    }
    public static function mostrarTodasGuias($estado,$tipo,$fechaInicio,$fechaFin)
    {
        $resultado = DB::select('CALL sp_obtener_guias_todos(?,?,?,?)',[$estado,$tipo,$fechaInicio,$fechaFin]);
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
    public static function actualizarEntrega($cluster,$guia,$datos)
    {
        DB::table('guias')->where(['id_cluster' => $cluster, 'id' => $guia,'situ' => 7])->orWhere(['situ' => 8, 'situ' => 14])->update($datos);
        return 'Entrega actualizada con exito';
    }
    public static function filterComprobantes($stringComprobante)
    {
        $comprobantes = explode(';',trim($stringComprobante));
        $nuevoComprobante = [
            'nro_guias' => [],
            'nro_boletas' => [],
            'nro_facturas' => [],
            'otros' => []
        ];
        foreach ($comprobantes as $key => $value) {
            $obtenerboleta = explode('/',trim($value));
            if(count($obtenerboleta) < 2 || empty($obtenerboleta)){
                continue;
            }
            $keyComprobante = 'otros';
            if(strpos($value,'b') === 0 || strpos($value,'B') === 0){
                $keyComprobante = 'nro_boletas';
            }
            if(strpos($value,'f') === 0 || strpos($value,'F') === 0){
                $keyComprobante = 'nro_facturas';
            }
            if(strpos($value,'g') === 0 || strpos($value,'G') === 0){
                $keyComprobante = 'nro_guias';
            }
            $nuevoComprobante[$keyComprobante][] = $obtenerboleta[1];
        }
        return $nuevoComprobante;
    }
    public static function obtenerGuiasConsulta($nro_remitente,$fecha)
    {
        $datos = DB::select('CALL sp_consulta_guias_remitente(?,?)',[$nro_remitente,$fecha]);
        foreach ($datos as $value) {
            $value->nro_comp_cli = self::filterComprobantes($value->nro_comp_cli);
        }
        return $datos;
     
    }
    public static function obtenerGuiasConsultaTrabajador($idCliente,$idUsuario,$fechaInicio,$fechaFin)
    {
        $datos = DB::select('CALL sp_consulta_guias_remitente_trabajador(?,?,?,?)', [$idCliente, $idUsuario, $fechaInicio, $fechaFin]);
        foreach ($datos as $value) {
            $value->nro_comp_cli = self::filterComprobantes($value->nro_comp_cli);
        }
        return $datos;
    }
    public static function consultaGuiaCliente($filtros)
    {
        $queryEstados = "SELECT DATE_FORMAT(pg.fecha_hr_update,'%d/%m/%Y') AS fecha_cambio,se.id,se.tipo FROM procesos_guia pg LEFT JOIN situacion_estado se ON pg.situacion = se.id WHERE pg.id_guia = ?";
        if($filtros['tipoguia'] == 'guia'){
            $nro_guia = explode('-',trim($filtros['nro_guia_sci']));
            if(isset($nro_guia[1])){
                $nro_guia = trim($nro_guia[0]) . '-' . trim($nro_guia[1]);
            }else{
                $nro_guia = '0-0';
            }
            $filtros['nro_guia_sci'] = $nro_guia;
        }
        if($filtros['tipoguia'] == 'remitente'){
            $filtros['nro_guia_remitente'] = $filtros['selectguia'] . '/' . $filtros['nro_guia_remitente'];
        }
        $consulta = DB::select('CALL sp_consulta_guia(?,?,?,?)',[$filtros['tipoguia'],$filtros['nro_guia_sci'],$filtros['documento_remitente'],$filtros['nro_guia_remitente']]);
        if(!empty($consulta) && isset($consulta[0])){
            $consulta[0]->seguimiento = DB::select($queryEstados,[$consulta[0]->id]);
            $consulta[0]->imagenes = DB::table('imagenes_guia')->select("nom_file","nom_id_file","size","type")->where('id_guia', $consulta[0]->id)->get();
            $consulta[0]->departamento_destinatario = (new Ubigeo)->getNameDepartamento($consulta[0]->departamento_destinatario);
            $consulta[0]->departamento_remitente = (new Ubigeo)->getNameDepartamento($consulta[0]->departamento_remitente);
            $consulta[0]->provincia_destinatario = (new Ubigeo)->getNameProvincia($consulta[0]->provincia_destinatario);
            $consulta[0]->provincia_remitente = (new Ubigeo)->getNameProvincia($consulta[0]->provincia_remitente);
            $consulta[0]->distrito_destinatario = (new Ubigeo)->getNameDistrito($consulta[0]->distrito_destinatario);
            $consulta[0]->distrito_remitente = (new Ubigeo)->getNameDistrito($consulta[0]->distrito_remitente);
            $consulta[0]->comprobantes = self::filterComprobantes($consulta[0]->nro_comp_cli);
            return $consulta[0];
        }
        return null;
    }
}
