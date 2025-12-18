<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantesSimpleFact extends Model
{
    protected $fillable = ['tipo','serie','correlativo'];
    protected function scopePorTipo($query,$tipo)
    {
        return $query->where('tipo',$tipo);
    }
    protected function scopePorSerie($query, $serie)
    {
        return $query->where('serie',$serie);
    }
    public static function numeroComprobantes($letra)
    {
        return match($letra){
            'F' => '01',
            'G' => '09',
            'B' => '03',
            default => '00'
        };
    }
    public static function comprobanteSUNAT($dbSerie)
    {
        [$letra, $comprobante] = explode('/',$dbSerie);
        return [
            'COD_TIP_DOC_REF' => self::numeroComprobantes($letra),
            'NUM_DOC_REF' => $comprobante
        ];
    }
}
