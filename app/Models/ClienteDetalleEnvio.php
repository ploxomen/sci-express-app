<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteDetalleEnvio extends Model
{
    protected $table = 'clientes_detalle_envios';
    protected $fillable = ['cliente_id','envase_embalaje','largo','ancho','altura'];
}
