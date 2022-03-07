<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModeloManifiesto extends Model
{
    protected $table = 'manifiesto';
    public $timestamps = false;

    public static function agregarManifiesto($manifiesto,$guias)
    {
        DB::beginTransaction();
        try {
            $id_manifiesto = DB::table('manifiesto')->insertGetId($manifiesto);
            for ($i=0; $i < count($guias) ; $i++) {
                $guias[$i]['id_manifiesto'] = $id_manifiesto;
            }
            DB::table('detalle_manifiesto')->insert($guias);
            foreach ($guias as $n_guia) {
                DB::table('guias')->where(['id' => $n_guia['id_guia']])->update(['situ' => 2]);
            }
            DB::commit();
            return ['success' => 'Manifiesto agregado correctamente'];
        } catch (Exception $ex) {
            DB::rollBack();
            return ['error' => $ex->getMessage()];
        }
    }
}
