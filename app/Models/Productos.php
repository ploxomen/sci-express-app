<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    protected $table = 'products';
    public $timestamps = false;

    public static function totalProductos()
    {
        return Productos::where(['status' => 1])->count();
    }
}
