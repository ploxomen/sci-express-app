<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmbarqueCiudadBaseImg extends Model
{
    use HasFactory;

    protected $fillable = ['id_ciudad_llegada','nombre_original','nombre_unico','tipo_archivo','ruta'];
}
