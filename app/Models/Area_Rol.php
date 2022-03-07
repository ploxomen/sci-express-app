<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area_Rol extends Model
{
    protected $table = 'area_rol';
    public $timestamps = false;
    protected $fillable = ['id_area','id_rol'];

   
}
