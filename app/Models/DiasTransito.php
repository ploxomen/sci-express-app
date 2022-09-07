<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiasTransito extends Model
{
    protected $table = "transito_destinos";
    public $timestamps = false;

    protected $fillable = ['departamento_origen', 'departamento_destino', 'ciudad_base', 'origen_base_km', 'origen_base_hr_terrestre', 'origen_base_hr_aereo', 'ciudad_destino', 'base_destino_km', 'transito_destino_hr', 'total_km_origen_destino', 'total_hr_origen_destino', 'total_origen_base_hr_aereo'];

    
}
