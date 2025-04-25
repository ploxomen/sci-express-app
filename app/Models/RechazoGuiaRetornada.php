<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechazoGuiaRetornada extends Model
{
    protected $table = 'rechazo_guia_retornadas';
    protected $fillable = ['opciones_denegado','detalles','guia_id','usuario_id'];
}
