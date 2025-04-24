<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetornoCargoDetalle extends Model
{
    protected $table = "detalle_retorno_cargo";
    public function guia()
    {
        return $this->belongsTo(ModeloGuias::class,'id_guia');
    }

}
