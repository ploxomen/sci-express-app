<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidades extends Model
{
    protected $table = 'units';
    public $timestamps = false;
    protected $fillable = ['unidades','abreviado','created_at','update_at','user_create','estado'];

    public static function obtenerUnidades()
    {
        return Unidades::where(['estado' => 1])->get();
    }
}
