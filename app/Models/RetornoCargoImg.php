<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetornoCargoImg extends Model
{
    protected $table = "retorno_cargo_img";
    protected $fillable = ['retorno_cargo_id','nombre','url'];
}
