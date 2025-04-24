<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetornoCargo extends Model
{
    protected $table = "retorno_cargo";
    
    public function imagenes()
    {
        return $this->hasMany(RetornoCargoImg::class,'retorno_cargo_id');
    }
    public function detalleGuias()
    {
        return $this->hasMany(RetornoCargoDetalle::class,'id_retorno_cargo');
    }
}
