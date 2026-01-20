<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiaDetalle extends Model
{
    protected $table = 'detalle_guias';
    public function tipoEnvio()
    {
        return $this->belongsTo(TipoEnvio::class,'tipo_envio');
    }
}
