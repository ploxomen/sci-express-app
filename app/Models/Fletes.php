<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Fletes extends Model
{
    protected $table = "fletes";
    public $timestamps = false;
    protected $fillable = ['ciudad_base','cajas','sobres','paquetes','cajones','paquetes','jabas','parihuelas'];

    

}
