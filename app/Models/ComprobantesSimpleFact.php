<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantesSimpleFact extends Model
{
    protected $fillable = ['tipo', 'serie', 'correlativo'];
    public const NRO_REMISION_REMITENTE = "09";
    public const NRO_REMISION_TRANSPORTISTA = "31";

    
    protected function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }
    protected function scopePorSerie($query, $serie)
    {
        return $query->where('serie', $serie);
    }
    public static function numeroComprobantes($letra)
    {
        switch ($letra) {
            case 'F':
                return '01';
            case 'G':
                return '09';
            case 'B':
                return '03';
            default:
                return '00';
        }
    }
    public static function comprobanteSUNAT($dbSerie)
    {
        [$letra, $comprobante] = explode('/', $dbSerie);
        return [
            'COD_TIP_DOC_REF' => self::numeroComprobantes($letra),
            'NUM_DOC_REF' => $comprobante
        ];
    }
}
