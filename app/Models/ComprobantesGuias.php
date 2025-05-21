<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComprobantesGuias extends Model
{
    protected $table = "comprobantes_guias";
    protected $fillable = ['comprobante','serie'];
    public function scopeListaComprobantes($query){
        return $query->select('comprobante','serie')->get()->map(function($value){
            $series =  empty($value->serie) ? [] : explode(';',$value->serie);
            sort($series);
            return  [
                'comprobante' => $value->comprobante,
                'series' => $series
            ];
        });
    }
}
