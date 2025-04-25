<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetornoCargo extends Model
{
    protected $table = "retorno_cargo";
    protected $fillable = ['fch_emba','id_empr_tran','tipo_comprobantes','nro_guias','importe_embarque','fecha_llegada','hr_llegada','total_bultos','nro_cajas','nro_jabas','nro_cajones','nro_sobres','nro_paquetes','num_otros','detalle_otro'];
    public $timestamps = false;
    public function imagenes()
    {
        return $this->hasMany(RetornoCargoImg::class,'retorno_cargo_id');
    }
    public function detalleGuias()
    {
        return $this->hasMany(RetornoCargoDetalle::class,'id_retorno_cargo');
    }
}
