<?php

namespace App\Models\Tipos;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documentos extends Model
{
    protected $table = 'tipo_documento';
    public $timestamps = false;
    protected $fillable = ['tipo','estado','sunat_cod'];
    
}
